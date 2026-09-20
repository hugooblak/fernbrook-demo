<?php
/**
 * Who is allowed to read this site, and what they get.
 *
 * Two jobs: the AI-crawler policy written into robots.txt, and llms.txt — the short plain-text
 * index of the pages worth reading, which answer engines are starting to look for.
 *
 * The default is "allow anything that can send a visitor or cite us, block the ones that only
 * take". That is a business decision, not a technical one, so each crawler has a line here
 * explaining what it does and the client can change any of them.
 *
 * @package Fernbrook AI Search
 */

defined( 'ABSPATH' ) || exit;

const FBAEO_ROBOTS_OPTION = 'fbaeo_robots';

/**
 * The crawlers, what each one does, and whether we allow it by default.
 *
 * @return array
 */
function fbaeo_crawlers() {
	return array(
		'GPTBot'              => array( 'OpenAI. Collects pages to train future models. Does not send visitors.', false ),
		'OAI-SearchBot'       => array( 'OpenAI. Builds the index behind ChatGPT search results, which can link back to you.', true ),
		'ChatGPT-User'        => array( 'OpenAI. Fetches one page when a person asks ChatGPT about it.', true ),
		'ClaudeBot'           => array( 'Anthropic. Collects pages to train Claude.', false ),
		'Claude-SearchBot'    => array( 'Anthropic. Indexes pages so Claude can cite them in answers.', true ),
		'Claude-User'         => array( 'Anthropic. Fetches one page when a person asks Claude about it.', true ),
		'PerplexityBot'       => array( 'Perplexity. Indexes pages for its answer engine, with citations.', true ),
		'Perplexity-User'     => array( 'Perplexity. Fetches one page when a person follows a link in an answer.', true ),
		'Google-Extended'     => array( 'Google. Controls whether your pages help ground Gemini answers. It does NOT affect normal Google Search.', true ),
		'Applebot-Extended'   => array( 'Apple. Controls use of your pages in Apple Intelligence.', true ),
		'Amazonbot'           => array( 'Amazon. Feeds Alexa answers.', true ),
		'meta-externalagent'  => array( 'Meta. Collects pages for Meta AI.', true ),
		'CCBot'               => array( 'Common Crawl. A public archive that many model builders train on. Sends no visitors.', false ),
		'Bytespider'          => array( 'ByteDance. Known for heavy crawling, sends no visitors.', false ),
	);
}

/**
 * The saved policy: crawler name => '1' to allow.
 *
 * @return array
 */
function fbaeo_robots_settings() {
	$saved = get_option( FBAEO_ROBOTS_OPTION, null );
	if ( ! is_array( $saved ) ) {
		$saved = array( 'allow' => array(), 'extra' => '' );
		foreach ( fbaeo_crawlers() as $name => $info ) {
			$saved['allow'][ $name ] = $info[1] ? '1' : '';
		}
	}
	$saved['extra'] = isset( $saved['extra'] ) ? $saved['extra'] : '';
	return $saved;
}

/**
 * The lines this plugin adds to robots.txt.
 *
 * @return string
 */
function fbaeo_robots_block() {
	$settings = fbaeo_robots_settings();
	$lines    = array( '', '# AI and answer-engine crawlers. Managed in WordPress: AI Search → Crawlers.' );
	foreach ( fbaeo_crawlers() as $name => $info ) {
		$allowed = ! empty( $settings['allow'][ $name ] );
		$lines[] = 'User-agent: ' . $name;
		$lines[] = $allowed ? 'Allow: /' : 'Disallow: /';
		$lines[] = '';
	}
	if ( trim( $settings['extra'] ) ) {
		$lines[] = '# Extra rules';
		foreach ( preg_split( '/\R/', trim( $settings['extra'] ) ) as $line ) {
			$lines[] = $line;
		}
		$lines[] = '';
	}
	$lines[] = 'Sitemap: ' . home_url( '/wp-sitemap.xml' );
	$lines[] = '# Plain-text index for answer engines: ' . home_url( '/llms.txt' );
	return implode( "\n", $lines ) . "\n";
}

add_filter(
	'robots_txt',
	function ( $output ) {
		return $output . fbaeo_robots_block();
	},
	10
);

/* ---------------------------------------------------------------------------
 * llms.txt
 * ------------------------------------------------------------------------ */

add_action(
	'init',
	function () {
		add_rewrite_rule( '^llms\.txt$', 'index.php?fbaeo_llms=1', 'top' );
	}
);

add_filter(
	'query_vars',
	function ( $vars ) {
		$vars[] = 'fbaeo_llms';
		return $vars;
	}
);

// WordPress adds a trailing slash to anything it thinks is a page. llms.txt is a file.
add_filter(
	'redirect_canonical',
	function ( $redirect ) {
		return get_query_var( 'fbaeo_llms' ) ? false : $redirect;
	}
);

add_action(
	'template_redirect',
	function () {
		if ( ! get_query_var( 'fbaeo_llms' ) ) {
			return;
		}
		header( 'Content-Type: text/plain; charset=utf-8' );
		header( 'X-Robots-Tag: noindex' );
		echo fbaeo_llms_text(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- plain text file.
		exit;
	}
);

/**
 * The llms.txt body: what the business is, then the pages worth reading.
 *
 * @return string
 */
function fbaeo_llms_text() {
	$out  = '# ' . fbaeo_get( 'name' ) . "\n\n";
	$out .= '> ' . fbaeo_get( 'description' ) . "\n\n";
	$out .= 'Address: ' . fbaeo_get( 'street' ) . ', ' . fbaeo_get( 'locality' ) . ', ' . fbaeo_get( 'region' ) . ' ' . fbaeo_get( 'postal_code' ) . "\n";
	$out .= 'Phone: ' . fbaeo_get( 'phone' ) . "\n";
	$out .= 'Areas served: ' . implode( ', ', fbaeo_get( 'areas', array() ) ) . "\n\n";

	$sections = array(
		'Key pages' => array(),
		'Guides'    => array(),
	);

	$pages = get_posts(
		array(
			'post_type'   => 'page',
			'numberposts' => 100,
			'post_status' => 'publish',
			'orderby'     => 'menu_order title',
			'order'       => 'ASC',
		)
	);
	foreach ( $pages as $page ) {
		if ( '1' !== fbaeo_meta( $page->ID, '_fbaeo_primary' ) ) {
			continue;
		}
		$sections['Key pages'][] = sprintf( '- [%s](%s): %s', get_the_title( $page ), get_permalink( $page ), fbaeo_description_for( $page ) );
	}

	$posts = get_posts( array( 'post_type' => 'post', 'numberposts' => 30, 'post_status' => 'publish' ) );
	foreach ( $posts as $post ) {
		$sections['Guides'][] = sprintf( '- [%s](%s): %s', get_the_title( $post ), get_permalink( $post ), fbaeo_description_for( $post ) );
	}

	foreach ( $sections as $heading => $items ) {
		if ( ! $items ) {
			continue;
		}
		$out .= '## ' . $heading . "\n\n" . implode( "\n", $items ) . "\n\n";
	}

	$out .= "## Notes\n\n";
	$out .= "- Prices and class dates on the website are the ones to quote. This file is generated from those pages.\n";
	$out .= "- Structured data for every page is in the page's own JSON-LD, using stable @id values under " . fbaeo_base() . "\n";
	return $out;
}
