<?php
/**
 * The "Enquiries" inbox: a private post type, list columns, a details box and a status field.
 *
 * @package Fernbrook Dog Academy Leads
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	function () {
		register_post_type(
			'fbl_lead',
			array(
				'labels'          => array(
					'name'          => __( 'Enquiries', 'fernbrook-leads' ),
					'singular_name' => __( 'Enquiry', 'fernbrook-leads' ),
					'edit_item'     => __( 'Enquiry details', 'fernbrook-leads' ),
					'search_items'  => __( 'Search enquiries', 'fernbrook-leads' ),
					'not_found'     => __( 'No enquiries yet. Send the enquiry form to see one here.', 'fernbrook-leads' ),
					'all_items'     => __( 'All enquiries', 'fernbrook-leads' ),
				),
				'public'          => false, // Never visible on the front end or in search.
				'show_ui'         => true,
				'show_in_rest'    => false, // Keeps lead data out of the public REST API.
				'menu_icon'       => 'dashicons-email-alt',
				'menu_position'   => 3,
				'supports'        => array( 'title' ),
				// Own permissions, so only Administrators and Editors see leads (not Authors or Contributors).
				'capability_type' => array( 'fbl_lead', 'fbl_leads' ),
				'capabilities'    => array( 'create_posts' => 'do_not_allow' ), // Leads only come from the form.
				'map_meta_cap'    => true,
			)
		);
	}
);

/**
 * Give Administrators and Editors the lead permissions. Runs once, then remembers it.
 */
add_action(
	'init',
	function () {
		if ( get_option( 'fbl_caps_version' ) === FBL_VERSION ) {
			return;
		}
		$caps = array( 'edit_fbl_leads', 'edit_others_fbl_leads', 'edit_published_fbl_leads', 'read_private_fbl_leads', 'delete_fbl_leads', 'delete_others_fbl_leads', 'delete_published_fbl_leads', 'publish_fbl_leads', 'export_fbl_leads' );
		foreach ( array( 'administrator', 'editor' ) as $role_name ) {
			$role = get_role( $role_name );
			if ( $role ) {
				foreach ( $caps as $cap ) {
					$role->add_cap( $cap );
				}
			}
		}
		update_option( 'fbl_caps_version', FBL_VERSION );
	},
	5
);

/**
 * Columns on the Leads list.
 */
add_filter(
	'manage_fbl_lead_posts_columns',
	function () {
		return array(
			'cb'           => '<input type="checkbox" />',
			'title'        => __( 'Name', 'fernbrook-leads' ),
			'fbl_phone'    => __( 'Phone', 'fernbrook-leads' ),
			'fbl_program' => __( 'Program', 'fernbrook-leads' ),
			'fbl_dog_age' => __( 'Dog age', 'fernbrook-leads' ),
			'fbl_zip'      => __( 'ZIP', 'fernbrook-leads' ),
			'fbl_source'   => __( 'Source', 'fernbrook-leads' ),
			'fbl_status'   => __( 'Status', 'fernbrook-leads' ),
			'date'         => __( 'Received', 'fernbrook-leads' ),
		);
	}
);

add_action(
	'manage_fbl_lead_posts_custom_column',
	function ( $column, $post_id ) {
		switch ( $column ) {
			case 'fbl_phone':
				$phone = get_post_meta( $post_id, 'phone', true );
				printf( '<a href="tel:%s">%s</a>', esc_attr( preg_replace( '/\D/', '', $phone ) ), esc_html( $phone ) );
				break;
			case 'fbl_program':
				echo esc_html( fbl_label( 'program', get_post_meta( $post_id, 'program', true ) ) );
				break;
			case 'fbl_dog_age':
				echo esc_html( fbl_label( 'dog_age', get_post_meta( $post_id, 'dog_age', true ) ) );
				break;
			case 'fbl_zip':
				echo esc_html( get_post_meta( $post_id, 'zip', true ) );
				break;
			case 'fbl_source':
				echo esc_html( fbl_source_summary( $post_id ) );
				break;
			case 'fbl_status':
				$statuses = fbl_statuses();
				$status   = get_post_meta( $post_id, 'status', true ) ?: 'new';
				echo esc_html( $statuses[ $status ] ?? $status );
				break;
		}
	},
	10,
	2
);

/**
 * Short "where did this lead come from" label, e.g. "google / cpc" or "Direct".
 *
 * @param int $post_id Lead ID.
 * @return string
 */
function fbl_source_summary( $post_id ) {
	$source = get_post_meta( $post_id, 'utm_source', true );
	$medium = get_post_meta( $post_id, 'utm_medium', true );
	if ( $source ) {
		return $medium ? "$source / $medium" : $source;
	}
	if ( get_post_meta( $post_id, 'gclid', true ) ) {
		return 'google / cpc (gclid)';
	}
	$ref = get_post_meta( $post_id, 'referrer', true );
	if ( $ref ) {
		$host = wp_parse_url( $ref, PHP_URL_HOST );
		if ( $host && wp_parse_url( home_url(), PHP_URL_HOST ) !== $host ) {
			return $host;
		}
	}
	return __( 'Direct', 'fernbrook-leads' );
}

/**
 * Details box on a single lead, plus the status dropdown.
 */
add_action(
	'add_meta_boxes_fbl_lead',
	function () {
		add_meta_box( 'fbl_details', __( 'What they told us', 'fernbrook-leads' ), 'fbl_details_box', 'fbl_lead', 'normal', 'high' );
		add_meta_box( 'fbl_status', __( 'Follow-up status', 'fernbrook-leads' ), 'fbl_status_box', 'fbl_lead', 'side', 'high' );
	}
);

/**
 * Read-only table of everything the form captured.
 *
 * @param WP_Post $post Lead.
 */
function fbl_details_box( $post ) {
	$rows = array(
		__( 'Name', 'fernbrook-leads' )     => get_post_meta( $post->ID, 'name', true ),
		__( 'Phone', 'fernbrook-leads' )    => get_post_meta( $post->ID, 'phone', true ),
		__( 'Email', 'fernbrook-leads' )    => get_post_meta( $post->ID, 'email', true ),
		__( 'ZIP', 'fernbrook-leads' )      => get_post_meta( $post->ID, 'zip', true ),
		__( 'Program', 'fernbrook-leads' ) => fbl_label( 'program', get_post_meta( $post->ID, 'program', true ) ),
		__( 'Dog age', 'fernbrook-leads' ) => fbl_label( 'dog_age', get_post_meta( $post->ID, 'dog_age', true ) ),
		__( 'Source', 'fernbrook-leads' )   => fbl_source_summary( $post->ID ),
	);
	foreach ( fbl_tracking_fields() as $key ) {
		$value = get_post_meta( $post->ID, $key, true );
		if ( $value ) {
			$rows[ $key ] = $value;
		}
	}
	$rows[ __( 'Consent given', 'fernbrook-leads' ) ] = get_post_meta( $post->ID, 'consent_at', true ) . ' — "' . get_post_meta( $post->ID, 'consent_text', true ) . '"';

	echo '<table class="widefat striped"><tbody>';
	foreach ( $rows as $label => $value ) {
		printf( '<tr><th style="width:160px">%s</th><td>%s</td></tr>', esc_html( $label ), esc_html( $value ) );
	}
	echo '</tbody></table>';
}

/**
 * Status dropdown.
 *
 * @param WP_Post $post Lead.
 */
function fbl_status_box( $post ) {
	wp_nonce_field( 'fbl_status', 'fbl_status_nonce' );
	$current = get_post_meta( $post->ID, 'status', true ) ?: 'new';
	echo '<label class="screen-reader-text" for="fbl-status">' . esc_html__( 'Status', 'fernbrook-leads' ) . '</label><select id="fbl-status" name="fbl_status" style="width:100%">';
	foreach ( fbl_statuses() as $key => $label ) {
		printf( '<option value="%s"%s>%s</option>', esc_attr( $key ), selected( $current, $key, false ), esc_html( $label ) );
	}
	echo '</select>';
}

add_action(
	'save_post_fbl_lead',
	function ( $post_id ) {
		if ( ! isset( $_POST['fbl_status_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['fbl_status_nonce'] ), 'fbl_status' ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		$status = isset( $_POST['fbl_status'] ) ? sanitize_key( $_POST['fbl_status'] ) : 'new';
		if ( array_key_exists( $status, fbl_statuses() ) ) {
			update_post_meta( $post_id, 'status', $status );
		}
	}
);

/**
 * Count of new leads next to the menu item.
 */
add_action(
	'admin_menu',
	function () {
		global $menu;
		$new = get_posts(
			array(
				'post_type'   => 'fbl_lead',
				'numberposts' => 99,
				'fields'      => 'ids',
				'meta_query'  => array( array( 'key' => 'status', 'value' => 'new' ) ), // phpcs:ignore WordPress.DB.SlowDBQuery
			)
		);
		if ( ! $new ) {
			return;
		}
		foreach ( $menu as $i => $item ) {
			if ( 'edit.php?post_type=fbl_lead' === $item[2] ) {
				$menu[ $i ][0] .= ' <span class="awaiting-mod"><span class="count">' . count( $new ) . '</span></span>'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
			}
		}
	},
	99
);
