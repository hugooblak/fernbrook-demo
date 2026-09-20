<?php
/**
 * "Export CSV" button on the Leads screen. Opens in Excel or Google Sheets.
 *
 * @package Fernbrook Dog Academy Leads
 */

defined( 'ABSPATH' ) || exit;

add_filter(
	'views_edit-fbl_lead',
	function ( $views ) {
		if ( ! current_user_can( 'export_fbl_leads' ) ) {
			return $views;
		}
		$url = wp_nonce_url( admin_url( 'admin-post.php?action=fbl_export' ), 'fbl_export' );
		$views['fbl_export'] = '<a href="' . esc_url( $url ) . '" class="button" style="margin-left:6px">' . esc_html__( 'Export CSV', 'fernbrook-leads' ) . '</a>';
		return $views;
	}
);

add_action(
	'admin_post_npl_export',
	function () {
		if ( ! current_user_can( 'export_fbl_leads' ) ) {
			wp_die( esc_html__( 'You are not allowed to export leads.', 'fernbrook-leads' ), 403 );
		}
		check_admin_referer( 'fbl_export' );

		$leads = get_posts(
			array(
				'post_type'   => 'fbl_lead',
				'numberposts' => -1,
				'post_status' => 'publish',
			)
		);

		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=leads-' . gmdate( 'Y-m-d' ) . '.csv' );

		$out     = fopen( 'php://output', 'w' );
		$columns = array_merge( array( 'received', 'status', 'name', 'phone', 'email', 'zip', 'program', 'dog_age', 'source' ), fbl_tracking_fields() );
		fputcsv( $out, $columns );
		foreach ( $leads as $lead ) {
			$row = array(
				$lead->post_date,
				get_post_meta( $lead->ID, 'status', true ),
				get_post_meta( $lead->ID, 'name', true ),
				get_post_meta( $lead->ID, 'phone', true ),
				get_post_meta( $lead->ID, 'email', true ),
				get_post_meta( $lead->ID, 'zip', true ),
				fbl_label( 'program', get_post_meta( $lead->ID, 'program', true ) ),
				fbl_label( 'dog_age', get_post_meta( $lead->ID, 'dog_age', true ) ),
				fbl_source_summary( $lead->ID ),
			);
			foreach ( fbl_tracking_fields() as $key ) {
				$row[] = get_post_meta( $lead->ID, $key, true );
			}
			// Stop spreadsheet formula injection: a cell starting with = + - @ is treated as text.
			$row = array_map(
				function ( $cell ) {
					return preg_match( '/^[=+\-@]/', (string) $cell ) ? "'" . $cell : $cell;
				},
				$row
			);
			fputcsv( $out, $row );
		}
		fclose( $out ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		exit;
	}
);
