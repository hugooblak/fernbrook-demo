<?php
/**
 * Semantic HTML, internal links and page weight.
 *
 * Three things the job asks for that nobody can see by looking at a page: is the heading
 * outline sane, does anything link to this page, and how heavy is it on a phone.
 *
 * @package Fernbrook AI Search
 */

defined( 'ABSPATH' ) || exit;

/**
 * Pages and posts this site publishes, in a sensible order.
 *
 * @return WP_Post[]
 */
function fbaeo_all_content() {
	$pages = get_posts(
		array(
			'post_type'   => 'page',
			'numberposts' => 100,
			'post_status' => 'publish',
			'orderby'     => 'menu_order title',
			'order'       => 'ASC',
		)
	);
	$posts = get_posts( array( 'post_type' => 'post', 'numberposts' => 100, 'post_status' => 'publish' ) );
	return array_merge( $pages, $posts );
}

/**
 * The heading outline of a page, template heading included.
 *
 * A page template that prints the post title as an H1 is part of the outline even though the
 * H1 is nowhere in the content. Checking only the content is how sites end up with two H1s.
 *
 * @param WP_Post $post Post.
 * @return array[] Each: level, text, source.
 */
function fbaeo_headings( $post ) {
	$out      = array();
	$template = (string) get_post_meta( $post->ID, '_wp_page_template', true );
	$title_in_template = ! in_array( $template, array( 'page-landing', 'page-focus' ), true );

	if ( $title_in_template ) {
		$out[] = array( 'level' => 1, 'text' => get_the_title( $post ), 'source' => 'template' );
	}

	$walk = function ( $blocks ) use ( &$walk, &$out ) {
		foreach ( $blocks as $block ) {
			if ( 'core/heading' === $block['blockName'] ) {
				$level = isset( $block['attrs']['level'] ) ? (int) $block['attrs']['level'] : 2;
				$out[] = array(
					'level'  => $level,
					'text'   => trim( wp_strip_all_tags( $block['innerHTML'] ) ),
					'source' => 'content',
				);
			}
			if ( 'core/details' === $block['blockName'] ) {
				continue; // A question is not a heading.
			}
			if ( ! empty( $block['innerBlocks'] ) ) {
				$walk( $block['innerBlocks'] );
			}
		}
	};
	$walk( parse_blocks( fbaeo_expanded_content( $post ) ) );
	return $out;
}

/**
 * Page content with pattern references expanded, so headings inside patterns are counted.
 *
 * @param WP_Post $post Post.
 * @return string
 */
function fbaeo_expanded_content( $post ) {
	return preg_replace_callback(
		'#<!-- wp:pattern \{"slug":"([^"]+)"\} /-->#',
		function ( $m ) {
			$pattern = WP_Block_Patterns_Registry::get_instance()->get_registered( $m[1] );
			return $pattern ? $pattern['content'] : '';
		},
		$post->post_content
	);
}

/**
 * What is wrong with a heading outline, if anything.
 *
 * @param array $headings From fbaeo_headings().
 * @return string[] Problems, empty when the outline is fine.
 */
function fbaeo_heading_problems( $headings ) {
	$problems = array();
	$h1       = 0;
	$previous = 0;
	foreach ( $headings as $heading ) {
		if ( 1 === $heading['level'] ) {
			$h1++;
		}
		if ( $previous && $heading['level'] > $previous + 1 ) {
			$problems[] = sprintf(
				/* translators: 1: skipped-from level, 2: skipped-to level, 3: heading text */
				__( 'Jumps from H%1$d to H%2$d at "%3$s".', 'fernbrook-aeo' ),
				$previous,
				$heading['level'],
				$heading['text']
			);
		}
		$previous = $heading['level'];
	}
	if ( 0 === $h1 ) {
		$problems[] = __( 'No H1 on the page.', 'fernbrook-aeo' );
	}
	if ( $h1 > 1 ) {
		$problems[] = sprintf( /* translators: %d: number of H1s */ __( '%d H1 headings. A page should have one.', 'fernbrook-aeo' ), $h1 );
	}
	return $problems;
}

/**
 * Internal links: who links to whom, counted from page content only.
 *
 * Links in the header and footer are left out on purpose. Every page has those, so counting
 * them would say every page is well linked, which tells you nothing. The page body is rendered
 * first, so links a listing block writes at render time are counted too.
 *
 * @return array [ 'outbound' => [ id => count ], 'inbound' => [ id => count ] ]
 */
function fbaeo_link_map() {
	$outbound = array();
	$inbound  = array();
	$home     = untrailingslashit( home_url() );

	foreach ( fbaeo_all_content() as $post ) {
		$outbound[ $post->ID ] = 0;
		if ( ! isset( $inbound[ $post->ID ] ) ) {
			$inbound[ $post->ID ] = 0;
		}
		// Rendered, not raw: a listing block writes its links at render time, and those count.
		$html = do_blocks( fbaeo_expanded_content( $post ) );
		if ( preg_match_all( '~href="([^"#?]+)~i', $html, $matches ) ) {
			$seen = array();
			foreach ( $matches[1] as $href ) {
				if ( 0 !== strpos( $href, $home ) ) {
					continue;
				}
				$path   = fbaeo_normalise_path( $href );
				$target = get_page_by_path( trim( $path, '/' ) );
				if ( ! $target ) {
					$target = get_page_by_path( trim( $path, '/' ), OBJECT, 'post' );
				}
				if ( ! $target || $target->ID === $post->ID || isset( $seen[ $target->ID ] ) ) {
					continue;
				}
				$seen[ $target->ID ]   = true;
				$outbound[ $post->ID ] = $outbound[ $post->ID ] + 1;
				$inbound[ $target->ID ] = ( $inbound[ $target->ID ] ?? 0 ) + 1;
			}
		}
	}
	return array( 'outbound' => $outbound, 'inbound' => $inbound );
}

/**
 * A rough page weight: the HTML this page produces, plus the stylesheet, the font and every
 * local image it references. Not a substitute for a real field measurement, and labelled as such.
 *
 * @param WP_Post $post Post.
 * @return int Bytes.
 */
function fbaeo_page_weight( $post ) {
	$html  = do_blocks( fbaeo_expanded_content( $post ) );
	$bytes = strlen( $html );

	$style = get_theme_file_path( 'style.css' );
	if ( file_exists( $style ) ) {
		$bytes += filesize( $style );
	}
	$font = get_theme_file_path( 'assets/fonts/figtree-var.woff2' );
	if ( file_exists( $font ) ) {
		$bytes += filesize( $font );
	}
	if ( preg_match_all( '#src="([^"]+\.svg)"#i', $html, $matches ) ) {
		foreach ( array_unique( $matches[1] ) as $src ) {
			$relative = str_replace( get_theme_file_uri(), '', $src );
			$path     = get_theme_file_path( ltrim( $relative, '/' ) );
			if ( file_exists( $path ) ) {
				$bytes += filesize( $path );
			}
		}
	}
	return $bytes;
}

/**
 * True when nothing on the site links to this page, and it is supposed to be found.
 *
 * The front page and pages deliberately marked noindex (a thank-you page, for one) are not
 * orphans — nobody is meant to arrive at them from a link.
 *
 * @param WP_Post $post  Post.
 * @param array   $links From fbaeo_link_map().
 * @return bool
 */
function fbaeo_orphan( $post, $links ) {
	if ( (int) get_option( 'page_on_front' ) === $post->ID ) {
		return false;
	}
	if ( '1' === fbaeo_meta( $post->ID, '_fbaeo_noindex' ) ) {
		return false;
	}
	return 0 === ( $links['inbound'][ $post->ID ] ?? 0 );
}

/**
 * The Headings and links screen.
 */
function fbaeo_screen_audit() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to see this screen.', 'fernbrook-aeo' ) );
	}
	$links    = fbaeo_link_map();
	$content  = fbaeo_all_content();
	$orphans  = 0;
	foreach ( $content as $post ) {
		if ( fbaeo_orphan( $post, $links ) ) {
			$orphans++;
		}
	}
	?>
	<div class="wrap fbaeo-wrap">
		<h1><?php esc_html_e( 'Headings, links and weight', 'fernbrook-aeo' ); ?></h1>
		<p class="fbaeo-intro"><?php esc_html_e( 'Three things you cannot see by looking at a page: whether the heading outline makes sense, whether anything links to it, and how heavy it is on a phone.', 'fernbrook-aeo' ); ?></p>

		<div class="fbaeo-grid">
			<div class="fbaeo-card">
				<p class="fbaeo-num"><?php echo (int) count( $content ); ?></p>
				<h3><?php esc_html_e( 'Pages and guides', 'fernbrook-aeo' ); ?></h3>
				<p><?php esc_html_e( 'Published and checked here.', 'fernbrook-aeo' ); ?></p>
			</div>
			<div class="fbaeo-card">
				<p class="fbaeo-num <?php echo $orphans ? 'fbaeo-fail' : 'fbaeo-pass'; ?>"><?php echo (int) $orphans; ?></p>
				<h3><?php esc_html_e( 'Pages nothing links to', 'fernbrook-aeo' ); ?></h3>
				<p><?php esc_html_e( 'Counting links in the page body only, not the menu or the footer. The front page and noindex pages are not counted.', 'fernbrook-aeo' ); ?></p>
			</div>
		</div>

		<table class="widefat striped fbaeo-table">
			<thead><tr>
				<th><?php esc_html_e( 'Page', 'fernbrook-aeo' ); ?></th>
				<th><?php esc_html_e( 'Heading outline', 'fernbrook-aeo' ); ?></th>
				<th style="width:7rem"><?php esc_html_e( 'Links in', 'fernbrook-aeo' ); ?></th>
				<th style="width:7rem"><?php esc_html_e( 'Links out', 'fernbrook-aeo' ); ?></th>
				<th style="width:8rem"><?php esc_html_e( 'Weight', 'fernbrook-aeo' ); ?></th>
			</tr></thead>
			<tbody>
			<?php foreach ( $content as $post ) :
				$headings = fbaeo_headings( $post );
				$problems = fbaeo_heading_problems( $headings );
				$outline  = array();
				foreach ( $headings as $heading ) {
					$outline[] = 'H' . $heading['level'];
				}
				$weight = fbaeo_page_weight( $post );
				?>
				<tr>
					<td>
						<strong><?php echo esc_html( get_the_title( $post ) ); ?></strong><br>
						<a href="<?php echo esc_url( get_permalink( $post ) ); ?>"><code><?php echo esc_html( wp_make_link_relative( get_permalink( $post ) ) ); ?></code></a>
					</td>
					<td>
						<code><?php echo esc_html( implode( ' · ', $outline ) ); ?></code>
						<?php if ( $problems ) : ?>
							<p class="fbaeo-fail"><?php echo esc_html( implode( ' ', $problems ) ); ?></p>
						<?php else : ?>
							<span class="fbaeo-pill fbaeo-pill-pass"><?php esc_html_e( 'Sound', 'fernbrook-aeo' ); ?></span>
						<?php endif; ?>
					</td>
					<td class="<?php echo fbaeo_orphan( $post, $links ) ? 'fbaeo-fail' : ''; ?>"><?php echo (int) ( $links['inbound'][ $post->ID ] ?? 0 ); ?></td>
					<td><?php echo (int) ( $links['outbound'][ $post->ID ] ?? 0 ); ?></td>
					<td><?php echo esc_html( size_format( $weight, 0 ) ); ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<p class="description"><?php esc_html_e( 'Weight is an estimate: the HTML this page produces, plus the stylesheet, the one font file and every image it references. It is not a field measurement — use PageSpeed Insights for that.', 'fernbrook-aeo' ); ?></p>
	</div>
	<?php
}
