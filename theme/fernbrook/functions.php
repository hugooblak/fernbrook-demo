<?php
/**
 * Fernbrook Dog Academy theme functions.
 *
 * Kept short on purpose. Design lives in theme.json, markup in templates and patterns.
 * Enquiries live in the "Fernbrook Dog Academy Leads" plugin and search data in "Fernbrook AI Search",
 * so both survive a theme change.
 *
 * @package Fernbrook Dog Academy
 */

defined( 'ABSPATH' ) || exit;

/**
 * Load style.css (the few rules theme.json can't express) on the front end and in the editor.
 */
add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_style( 'fernbrook', get_stylesheet_uri(), array(), wp_get_theme()->get( 'Version' ) );
	}
);
add_action(
	'after_setup_theme',
	function () {
		add_editor_style( 'style.css' );
	}
);

/**
 * Preload the one font file so headings don't jump when it arrives.
 */
add_action(
	'wp_head',
	function () {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( get_theme_file_uri( 'assets/fonts/figtree-var.woff2' ) )
		);
	},
	1
);

/**
 * Remove WordPress's emoji script and styles. Browsers draw emoji on their own.
 */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );

/**
 * Block styles the editor can pick from the Styles panel.
 */
add_action(
	'init',
	function () {
		register_block_style( 'core/paragraph', array( 'name' => 'eyebrow', 'label' => __( 'Eyebrow label', 'fernbrook' ) ) );
		register_block_style( 'core/list', array( 'name' => 'checklist', 'label' => __( 'Tick list', 'fernbrook' ) ) );

		register_block_pattern_category( 'fernbrook-sections', array( 'label' => __( 'Fernbrook Dog Academy sections', 'fernbrook' ) ) );
		register_block_pattern_category( 'fernbrook-pages', array( 'label' => __( 'Fernbrook Dog Academy page layouts', 'fernbrook' ) ) );
	}
);

/**
 * Metadata, canonical links and all structured data live in the "Fernbrook AI Search" plugin,
 * not here. They are business data: they must survive a theme change, and the client's own
 * site runs a different theme entirely.
 */
