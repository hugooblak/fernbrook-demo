<?php
/**
 * What gets printed in the <head>.
 *
 * Title, meta description, canonical link, robots, Open Graph, Twitter, the Search Console
 * verification tag, and one JSON-LD script holding the whole graph.
 *
 * Deliberately switches itself off when a real SEO plugin is active, so a site never ends up
 * with two canonical links arguing with each other.
 *
 * @package Fernbrook AI Search
 */

defined( 'ABSPATH' ) || exit;

/**
 * True when another SEO plugin is already writing these tags.
 *
 * @return bool
 */
function fbaeo_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'THE_SEO_FRAMEWORK_VERSION' ) || defined( 'AIOSEO_VERSION' );
}

// A one-author business site has nothing useful at /author/admin/, and WordPress lists that
// page in its own sitemap along with the account name. Both go.
add_filter(
	'wp_sitemaps_add_provider',
	function ( $provider, $name ) {
		return 'users' === $name ? false : $provider;
	},
	10,
	2
);

add_filter(
	'wp_robots',
	function ( $robots ) {
		if ( is_author() ) {
			$robots['noindex'] = true;
		}
		return $robots;
	},
	20
);

// WordPress prints its own canonical link. Two of them on one page is a contradiction,
// so core's is removed and this plugin prints the one the editor controls.
add_action(
	'template_redirect',
	function () {
		if ( ! fbaeo_seo_plugin_active() ) {
			remove_action( 'wp_head', 'rel_canonical' );
		}
	}
);

add_filter(
	'pre_get_document_title',
	function ( $title ) {
		if ( fbaeo_seo_plugin_active() || ! is_singular( array( 'page', 'post' ) ) ) {
			return $title;
		}
		$post = get_queried_object();
		return $post instanceof WP_Post ? fbaeo_title_for( $post ) : $title;
	}
);

add_filter(
	'wp_robots',
	function ( $robots ) {
		if ( is_singular( array( 'page', 'post' ) ) ) {
			$post = get_queried_object();
			if ( $post instanceof WP_Post && '1' === fbaeo_meta( $post->ID, '_fbaeo_noindex' ) ) {
				$robots['noindex']  = true;
				$robots['nofollow'] = true;
				unset( $robots['max-image-preview'] );
				return $robots;
			}
		}
		$robots['max-image-preview'] = 'large';
		$robots['max-snippet']       = -1;
		$robots['max-video-preview'] = -1;
		return $robots;
	}
);

add_action(
	'wp_head',
	function () {
		if ( fbaeo_seo_plugin_active() ) {
			return;
		}

		$token = fbaeo_get( 'gsc_token' );
		if ( $token ) {
			printf( '<meta name="google-site-verification" content="%s">' . "\n", esc_attr( $token ) );
		}

		$post = is_singular( array( 'page', 'post' ) ) ? get_queried_object() : null;
		if ( is_home() && get_option( 'page_for_posts' ) ) {
			$post = get_post( (int) get_option( 'page_for_posts' ) );
		}

		if ( ! $post instanceof WP_Post ) {
			return;
		}

		$description = fbaeo_description_for( $post );
		$canonical   = fbaeo_canonical_for( $post );
		$title       = fbaeo_title_for( $post );

		if ( $description ) {
			printf( '<meta name="description" content="%s">' . "\n", esc_attr( $description ) );
		}
		printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $canonical ) );

		printf( '<meta property="og:type" content="%s">' . "\n", 'post' === $post->post_type ? 'article' : 'website' );
		printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
		if ( $description ) {
			printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $description ) );
		}
		printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $canonical ) );
		printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( fbaeo_get( 'name' ) ) );
		printf( '<meta property="og:locale" content="%s">' . "\n", 'en_US' );
		printf( '<meta name="twitter:card" content="%s">' . "\n", 'summary_large_image' );

		// One script, one graph. Not five separate blocks.
		$graph = fbaeo_graph_for_current();
		if ( $graph ) {
			$data = array(
				'@context' => 'https://schema.org',
				'@graph'   => array_values( $graph ),
			);
			echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
		}
	},
	2
);
