<?php
/**
 * Plugin Name: Fernbrook AI Search
 * Description: The GEO/AEO layer for this site: one connected JSON-LD entity graph with stable @id values, per-page metadata and canonicals, an AI-crawler policy in robots.txt, llms.txt, a redirect manager, and a validation screen that checks every page before you send it to Google.
 * Version: 1.0.0
 * Requires at least: 6.6
 * Requires PHP: 7.4
 * Author: Hugo Oblak
 * License: GPL-2.0-or-later
 * Text Domain: fernbrook-aeo
 *
 * Why a plugin and not theme code: this is business data, not decoration. It has to survive a
 * redesign, and it has to work on whatever theme the site runs — a block theme like this one,
 * or a page builder theme like Divi. Nothing in here touches the front-end markup of the theme.
 *
 * @package Fernbrook AI Search
 */

defined( 'ABSPATH' ) || exit;

define( 'FBAEO_VERSION', '1.0.0' );
define( 'FBAEO_DIR', __DIR__ );
define( 'FBAEO_URL', plugin_dir_url( __FILE__ ) );

require FBAEO_DIR . '/includes/profile.php';     // The business details, entered once.
require FBAEO_DIR . '/includes/page-fields.php'; // The per-page schema and metadata panel.
require FBAEO_DIR . '/includes/classes.php';     // Class sessions: the source for Event data.
require FBAEO_DIR . '/includes/graph.php';       // Builds the @graph for the current page.
require FBAEO_DIR . '/includes/head.php';        // Prints title, description, canonical, OG and the graph.
require FBAEO_DIR . '/includes/robots.php';      // robots.txt, AI crawler policy, llms.txt.
require FBAEO_DIR . '/includes/redirects.php';   // Old address → new address, with a loop check.
require FBAEO_DIR . '/includes/audit.php';       // Heading outline, internal links, page weight.
require FBAEO_DIR . '/includes/validate.php';    // The 12 checks, per page.
require FBAEO_DIR . '/includes/admin.php';       // The AI Search menu.

add_action(
	'init',
	function () {
		register_block_type( FBAEO_DIR . '/blocks/class-list' );
		register_block_type( FBAEO_DIR . '/blocks/related' );
	}
);

add_action(
	'admin_enqueue_scripts',
	function ( $hook ) {
		if ( false === strpos( (string) $hook, 'fernbrook-ai-search' ) && ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}
		wp_enqueue_style( 'fbaeo-admin', FBAEO_URL . 'assets/admin.css', array(), FBAEO_VERSION );
	}
);
