<?php
/**
 * Plugin Name: Fernbrook Dog Academy Leads
 * Description: Multi-step enquiry form block, enquiry inbox in the admin, CSV export, spam protection and analytics events. Built for the Fernbrook Dog Academy demo.
 * Version: 1.0.0
 * Requires at least: 6.6
 * Requires PHP: 7.4
 * Author: Hugo Oblak
 * License: GPL-2.0-or-later
 * Text Domain: fernbrook-leads
 *
 * Why a plugin and not theme code: leads are business data. They must survive a redesign.
 *
 * @package Fernbrook Dog Academy Leads
 */

defined( 'ABSPATH' ) || exit;

define( 'FBL_VERSION', '1.0.0' );
define( 'FBL_DIR', __DIR__ );
define( 'FBL_URL', plugin_dir_url( __FILE__ ) );

require FBL_DIR . '/includes/fields.php';       // One place that lists the form's questions and answers.
require FBL_DIR . '/includes/lead-post-type.php'; // The "Leads" inbox in the admin.
require FBL_DIR . '/includes/submit.php';       // Validates and saves a submitted form.
require FBL_DIR . '/includes/export.php';       // "Export CSV" button on the Leads screen.
require FBL_DIR . '/includes/tracking.php';     // Analytics events (dataLayer) for Google Tag Manager / GA4.
require FBL_DIR . '/includes/privacy.php';      // Export or erase a person's leads with WordPress's privacy tools.

add_action(
	'init',
	function () {
		register_block_type( FBL_DIR . '/blocks/lead-form' );
	}
);
