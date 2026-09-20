<?php
/**
 * The twelve checks.
 *
 * These run inside WordPress, before anything is sent anywhere. They catch the mistakes that
 * Google's Rich Results Test will not: an @id that points at nothing, two nodes sharing an @id,
 * an answer that exists in the JSON-LD but not on the page.
 *
 * The external validators are still the final word, so every row links straight to them with
 * the page address already filled in.
 *
 * @package Fernbrook AI Search
 */

defined( 'ABSPATH' ) || exit;

/**
 * Walk a graph and collect every "@id" used as a reference (not as a definition).
 *
 * @param array $graph Nodes.
 * @return string[]
 */
function fbaeo_referenced_ids( $graph ) {
	$refs = array();
	$walk = function ( $value, $is_node_root ) use ( &$walk, &$refs ) {
		if ( ! is_array( $value ) ) {
			return;
		}
		$keys    = array_keys( $value );
		$is_ref  = ! $is_node_root && isset( $value['@id'] ) && 1 === count( $keys );
		if ( $is_ref ) {
			$refs[] = $value['@id'];
			return;
		}
		foreach ( $value as $key => $child ) {
			if ( '@id' === $key ) {
				continue;
			}
			$walk( $child, false );
		}
	};
	foreach ( $graph as $node ) {
		$walk( $node, true );
	}
	return array_values( array_unique( $refs ) );
}

/**
 * Run the twelve checks against one page.
 *
 * @param WP_Post $post Post.
 * @return array[] Each: label, state (pass|fail|info), note.
 */
function fbaeo_check_post( $post ) {
	$results  = array();
	$headings = fbaeo_headings( $post );
	$problems = fbaeo_heading_problems( $headings );
	$graph    = fbaeo_graph_for_post( $post );
	$title    = fbaeo_title_for( $post );
	$desc     = fbaeo_description_for( $post );
	$canon    = fbaeo_canonical_for( $post );
	$permalink = (string) get_permalink( $post );

	$h1 = 0;
	foreach ( $headings as $heading ) {
		if ( 1 === $heading['level'] ) {
			$h1++;
		}
	}

	$add = function ( $label, $ok, $note, $state = null ) use ( &$results ) {
		$results[] = array(
			'label' => $label,
			'state' => $state ? $state : ( $ok ? 'pass' : 'fail' ),
			'note'  => $note,
		);
	};

	// 1.
	$add(
		__( 'One H1', 'fernbrook-aeo' ),
		1 === $h1,
		1 === $h1 ? __( 'Exactly one.', 'fernbrook-aeo' ) : sprintf( /* translators: %d: count */ __( '%d found. Remove the extras, or switch the page template.', 'fernbrook-aeo' ), $h1 )
	);

	// 2.
	$skips = array_values( array_filter( $problems, function ( $p ) { return false !== strpos( $p, 'Jumps' ); } ) );
	$add(
		__( 'Heading levels in order', 'fernbrook-aeo' ),
		! $skips,
		$skips ? implode( ' ', $skips ) : __( 'No skipped levels.', 'fernbrook-aeo' )
	);

	// 3.
	$title_len = mb_strlen( $title );
	$add(
		__( 'Title length', 'fernbrook-aeo' ),
		$title_len >= 15 && $title_len <= 60,
		sprintf( /* translators: %d: characters */ __( '%d characters. Aim for 15 to 60.', 'fernbrook-aeo' ), $title_len )
	);

	// 4.
	$desc_len = mb_strlen( $desc );
	$add(
		__( 'Meta description length', 'fernbrook-aeo' ),
		$desc_len >= 70 && $desc_len <= 160,
		$desc ? sprintf( /* translators: %d: characters */ __( '%d characters. Aim for 70 to 160.', 'fernbrook-aeo' ), $desc_len ) : __( 'Empty. Write one in the AI search panel on the page.', 'fernbrook-aeo' )
	);

	// 5.
	$self = untrailingslashit( $canon ) === untrailingslashit( $permalink );
	$add(
		__( 'Canonical address', 'fernbrook-aeo' ),
		true,
		$self ? __( 'Points at itself, which is what a normal page should do.', 'fernbrook-aeo' ) : sprintf( /* translators: %s: address */ __( 'Points at %s. Deliberate override — check that is still right.', 'fernbrook-aeo' ), $canon ),
		$self ? 'pass' : 'info'
	);

	// 6.
	$noindex = '1' === fbaeo_meta( $post->ID, '_fbaeo_noindex' );
	$add(
		__( 'Open to search engines', 'fernbrook-aeo' ),
		! $noindex,
		$noindex ? __( 'Marked noindex on purpose. It will not appear in search.', 'fernbrook-aeo' ) : __( 'Indexable.', 'fernbrook-aeo' ),
		$noindex ? 'info' : 'pass'
	);

	// 7.
	$json  = wp_json_encode( array( '@context' => 'https://schema.org', '@graph' => array_values( $graph ) ), JSON_UNESCAPED_SLASHES );
	$valid = $json && null !== json_decode( $json, true );
	$add(
		__( 'JSON-LD is valid JSON', 'fernbrook-aeo' ),
		$valid && count( $graph ) > 0,
		$valid ? sprintf( /* translators: 1: node count, 2: size */ __( '%1$d nodes, %2$s.', 'fernbrook-aeo' ), count( $graph ), size_format( strlen( $json ) ) ) : __( 'Could not be encoded.', 'fernbrook-aeo' )
	);

	// 8.
	$missing = 0;
	foreach ( $graph as $node ) {
		if ( empty( $node['@type'] ) || empty( $node['@id'] ) ) {
			$missing++;
		}
	}
	$add(
		__( 'Every node has a @type and an @id', 'fernbrook-aeo' ),
		0 === $missing,
		0 === $missing ? __( 'All nodes are addressable.', 'fernbrook-aeo' ) : sprintf( /* translators: %d: count */ __( '%d node(s) cannot be referenced.', 'fernbrook-aeo' ), $missing )
	);

	// 9.
	$ids        = array();
	$duplicates = array();
	foreach ( $graph as $node ) {
		if ( empty( $node['@id'] ) ) {
			continue;
		}
		if ( in_array( $node['@id'], $ids, true ) ) {
			$duplicates[] = $node['@id'];
		}
		$ids[] = $node['@id'];
	}
	$add(
		__( 'No duplicate @id values', 'fernbrook-aeo' ),
		! $duplicates,
		$duplicates ? implode( ', ', array_unique( $duplicates ) ) : __( 'Every @id appears once.', 'fernbrook-aeo' )
	);

	// 10.
	$known     = array_merge( $ids, fbaeo_known_ids() );
	$refs      = fbaeo_referenced_ids( $graph );
	$dangling  = array_values( array_diff( $refs, $known ) );
	$add(
		__( 'Every @id reference resolves', 'fernbrook-aeo' ),
		! $dangling,
		$dangling
			? sprintf( /* translators: %s: list */ __( 'Points at nothing: %s', 'fernbrook-aeo' ), implode( ', ', $dangling ) )
			: sprintf( /* translators: %d: count */ __( '%d references, all of them resolve to a real entity on this site.', 'fernbrook-aeo' ), count( $refs ) )
	);

	// 11.
	$faq       = fbaeo_faq_items( $post );
	$empty_faq = 0;
	foreach ( $faq as $item ) {
		if ( '' === trim( $item['answer'] ) || '' === trim( $item['question'] ) ) {
			$empty_faq++;
		}
	}
	$add(
		__( 'FAQ answers are on the page', 'fernbrook-aeo' ),
		0 === $empty_faq,
		$faq
			? ( 0 === $empty_faq
				? sprintf( /* translators: %d: count */ __( '%d questions, every answer read straight from the page.', 'fernbrook-aeo' ), count( $faq ) )
				: sprintf( /* translators: %d: count */ __( '%d question(s) have no answer text.', 'fernbrook-aeo' ), $empty_faq ) )
			: __( 'No FAQ blocks on this page.', 'fernbrook-aeo' ),
		$faq ? null : 'info'
	);

	// 12.
	$has_rating = false !== strpos( (string) $json, '"aggregateRating"' ) || false !== strpos( (string) $json, '"reviewRating"' );
	$add(
		__( 'No invented ratings or reviews', 'fernbrook-aeo' ),
		! $has_rating,
		$has_rating
			? __( 'Rating data found. Only publish this when it comes from real, collected reviews.', 'fernbrook-aeo' )
			: __( 'None. This site publishes no rating data, because it has no real reviews to publish.', 'fernbrook-aeo' )
	);

	return $results;
}

/**
 * The Validate screen.
 */
function fbaeo_screen_validate() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to see this screen.', 'fernbrook-aeo' ) );
	}
	$content  = fbaeo_all_content();
	$detail   = isset( $_GET['page_id'] ) ? absint( $_GET['page_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$failures = 0;
	$checks   = 0;
	$rows     = array();
	foreach ( $content as $post ) {
		$results = fbaeo_check_post( $post );
		$fails   = 0;
		foreach ( $results as $result ) {
			$checks++;
			if ( 'fail' === $result['state'] ) {
				$fails++;
				$failures++;
			}
		}
		$rows[] = array( 'post' => $post, 'results' => $results, 'fails' => $fails );
	}
	?>
	<div class="wrap fbaeo-wrap">
		<h1><?php esc_html_e( 'Validate', 'fernbrook-aeo' ); ?></h1>
		<p class="fbaeo-intro"><?php esc_html_e( 'Twelve checks on every page, run here inside WordPress. These catch the things an external validator cannot see: an @id that points at nothing, two nodes sharing an @id, an answer in the JSON-LD that is not on the page. Google\'s Rich Results Test is still the last word, so every page links straight to it.', 'fernbrook-aeo' ); ?></p>

		<div class="fbaeo-grid">
			<div class="fbaeo-card">
				<p class="fbaeo-num"><?php echo (int) $checks; ?></p>
				<h3><?php esc_html_e( 'Checks run', 'fernbrook-aeo' ); ?></h3>
				<p><?php echo esc_html( sprintf( /* translators: %d: pages */ __( 'Across %d pages and guides.', 'fernbrook-aeo' ), count( $content ) ) ); ?></p>
			</div>
			<div class="fbaeo-card">
				<p class="fbaeo-num <?php echo $failures ? 'fbaeo-fail' : 'fbaeo-pass'; ?>"><?php echo (int) $failures; ?></p>
				<h3><?php esc_html_e( 'Failures', 'fernbrook-aeo' ); ?></h3>
				<p><?php echo $failures ? esc_html__( 'Open a page below to see the exact fix.', 'fernbrook-aeo' ) : esc_html__( 'Nothing outstanding.', 'fernbrook-aeo' ); ?></p>
			</div>
		</div>

		<table class="widefat striped fbaeo-table">
			<thead><tr>
				<th><?php esc_html_e( 'Page', 'fernbrook-aeo' ); ?></th>
				<th><?php esc_html_e( 'Result', 'fernbrook-aeo' ); ?></th>
				<th style="width:22rem"><?php esc_html_e( 'Check it elsewhere', 'fernbrook-aeo' ); ?></th>
			</tr></thead>
			<tbody>
			<?php foreach ( $rows as $row ) :
				$url = get_permalink( $row['post'] );
				?>
				<tr>
					<td>
						<strong><?php echo esc_html( get_the_title( $row['post'] ) ); ?></strong><br>
						<a href="<?php echo esc_url( add_query_arg( 'page_id', $row['post']->ID ) ); ?>"><?php esc_html_e( 'See all twelve checks and the JSON-LD', 'fernbrook-aeo' ); ?></a>
					</td>
					<td>
						<?php if ( $row['fails'] ) : ?>
							<span class="fbaeo-pill fbaeo-pill-fail"><?php echo esc_html( sprintf( /* translators: %d: failures */ __( '%d to fix', 'fernbrook-aeo' ), $row['fails'] ) ); ?></span>
						<?php else : ?>
							<span class="fbaeo-pill fbaeo-pill-pass"><?php esc_html_e( '12 / 12', 'fernbrook-aeo' ); ?></span>
						<?php endif; ?>
					</td>
					<td>
						<a class="button button-small" target="_blank" rel="noopener" href="<?php echo esc_url( 'https://validator.schema.org/#url=' . rawurlencode( $url ) ); ?>"><?php esc_html_e( 'Schema.org validator', 'fernbrook-aeo' ); ?></a>
						<a class="button button-small" target="_blank" rel="noopener" href="<?php echo esc_url( 'https://search.google.com/test/rich-results?url=' . rawurlencode( $url ) ); ?>"><?php esc_html_e( 'Rich Results Test', 'fernbrook-aeo' ); ?></a>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>

		<?php
		if ( $detail ) :
			$post = get_post( $detail );
			if ( $post ) :
				$graph = fbaeo_graph_for_post( $post );
				$json  = wp_json_encode( array( '@context' => 'https://schema.org', '@graph' => array_values( $graph ) ), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
				?>
				<h2><?php echo esc_html( sprintf( /* translators: %s: page title */ __( 'Details: %s', 'fernbrook-aeo' ), get_the_title( $post ) ) ); ?></h2>
				<table class="widefat striped fbaeo-table">
					<thead><tr><th style="width:18rem"><?php esc_html_e( 'Check', 'fernbrook-aeo' ); ?></th><th style="width:6rem"><?php esc_html_e( 'Result', 'fernbrook-aeo' ); ?></th><th><?php esc_html_e( 'What it found', 'fernbrook-aeo' ); ?></th></tr></thead>
					<tbody>
					<?php foreach ( fbaeo_check_post( $post ) as $i => $result ) : ?>
						<tr>
							<td><?php echo (int) $i + 1; ?>. <?php echo esc_html( $result['label'] ); ?></td>
							<td><span class="fbaeo-pill fbaeo-pill-<?php echo esc_attr( 'info' === $result['state'] ? 'warn' : $result['state'] ); ?>">
								<?php echo esc_html( 'pass' === $result['state'] ? __( 'Pass', 'fernbrook-aeo' ) : ( 'info' === $result['state'] ? __( 'Note', 'fernbrook-aeo' ) : __( 'Fix', 'fernbrook-aeo' ) ) ); ?>
							</span></td>
							<td><?php echo esc_html( $result['note'] ); ?></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
				<h3><?php esc_html_e( 'The JSON-LD this page publishes', 'fernbrook-aeo' ); ?></h3>
				<p class="description"><?php esc_html_e( 'Select it and copy if you want to paste it into an external validator by hand.', 'fernbrook-aeo' ); ?></p>
				<pre class="fbaeo-json"><?php echo esc_html( $json ); ?></pre>
				<?php
			endif;
		endif;
		?>
	</div>
	<?php
}
