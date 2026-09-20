<?php
/**
 * Class sessions.
 *
 * One record per course start. The same record produces the visible list of dates and the
 * Event structured data, so the two can never disagree — which is the rule Google actually
 * enforces: structured data must describe what a visitor can see on the page.
 *
 * @package Fernbrook AI Search
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	function () {
		register_post_type(
			'fb_class',
			array(
				'labels'        => array(
					'name'          => __( 'Classes', 'fernbrook-aeo' ),
					'singular_name' => __( 'Class', 'fernbrook-aeo' ),
					'add_new_item'  => __( 'Add a class start date', 'fernbrook-aeo' ),
					'edit_item'     => __( 'Class start date', 'fernbrook-aeo' ),
					'all_items'     => __( 'Classes', 'fernbrook-aeo' ),
					'not_found'     => __( 'No class dates yet.', 'fernbrook-aeo' ),
				),
				'public'        => false,
				'show_ui'       => true,
				'show_in_menu'  => true,
				'menu_icon'     => 'dashicons-calendar-alt',
				'menu_position' => 4,
				'supports'      => array( 'title' ),
				'capability_type' => 'post',
			)
		);
	}
);

/**
 * The fields on a class record.
 *
 * @return array key => label
 */
function fbaeo_class_fields() {
	return array(
		'_fbc_program' => __( 'Program page', 'fernbrook-aeo' ),
		'_fbc_start'   => __( 'First class (YYYY-MM-DD)', 'fernbrook-aeo' ),
		'_fbc_time'    => __( 'Start time (24 hour, e.g. 18:30)', 'fernbrook-aeo' ),
		'_fbc_weeks'   => __( 'Number of weekly classes', 'fernbrook-aeo' ),
		'_fbc_price'   => __( 'Price for the course', 'fernbrook-aeo' ),
		'_fbc_spots'   => __( 'Places left', 'fernbrook-aeo' ),
	);
}

add_action(
	'add_meta_boxes_fb_class',
	function () {
		add_meta_box( 'fbc_details', __( 'When and where', 'fernbrook-aeo' ), 'fbaeo_class_box', 'fb_class', 'normal', 'high' );
	}
);

/**
 * The class details box.
 *
 * @param WP_Post $post Class record.
 */
function fbaeo_class_box( $post ) {
	wp_nonce_field( 'fbc_save', 'fbc_nonce' );
	$pages = get_posts(
		array(
			'post_type'   => 'page',
			'numberposts' => 50,
			'orderby'     => 'title',
			'order'       => 'ASC',
			'post_status' => 'publish',
		)
	);
	echo '<table class="form-table"><tbody>';
	foreach ( fbaeo_class_fields() as $key => $label ) {
		$value = get_post_meta( $post->ID, $key, true );
		echo '<tr><th scope="row"><label for="' . esc_attr( $key ) . '">' . esc_html( $label ) . '</label></th><td>';
		if ( '_fbc_program' === $key ) {
			echo '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '"><option value="">—</option>';
			foreach ( $pages as $page ) {
				printf( '<option value="%d" %s>%s</option>', (int) $page->ID, selected( (int) $value, (int) $page->ID, false ), esc_html( $page->post_title ) );
			}
			echo '</select><p class="description">' . esc_html__( 'The Event data links to this page by @id, so a class and its course are one connected thing.', 'fernbrook-aeo' ) . '</p>';
		} else {
			printf( '<input type="text" class="regular-text" name="%1$s" id="%1$s" value="%2$s">', esc_attr( $key ), esc_attr( $value ) );
		}
		echo '</td></tr>';
	}
	echo '</tbody></table>';
	echo '<p class="description">' . esc_html__( 'The title of this record is what visitors see in the list, for example "Puppy Foundations, Tuesday evenings".', 'fernbrook-aeo' ) . '</p>';
}

add_action(
	'save_post_fb_class',
	function ( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! isset( $_POST['fbc_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['fbc_nonce'] ) ), 'fbc_save' ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		foreach ( array_keys( fbaeo_class_fields() ) as $key ) {
			$value = isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '';
			if ( '' === $value ) {
				delete_post_meta( $post_id, $key );
			} else {
				update_post_meta( $post_id, $key, $value );
			}
		}
	}
);

/**
 * Classes that have not started yet, soonest first.
 *
 * @param int $limit How many.
 * @return array[] Each one: id, title, start (timestamp), program page id, price, spots, weeks.
 */
function fbaeo_upcoming_classes( $limit = 6 ) {
	$posts = get_posts(
		array(
			'post_type'   => 'fb_class',
			'numberposts' => 50,
			'post_status' => 'publish',
		)
	);
	$out = array();
	foreach ( $posts as $post ) {
		$date = get_post_meta( $post->ID, '_fbc_start', true );
		$time = get_post_meta( $post->ID, '_fbc_time', true ) ? get_post_meta( $post->ID, '_fbc_time', true ) : '18:30';
		if ( ! $date ) {
			continue;
		}
		$stamp = strtotime( $date . ' ' . $time );
		if ( ! $stamp || $stamp < time() - DAY_IN_SECONDS ) {
			continue;
		}
		$out[] = array(
			'id'      => $post->ID,
			'title'   => $post->post_title,
			'stamp'   => $stamp,
			'date'    => $date,
			'time'    => $time,
			'program' => (int) get_post_meta( $post->ID, '_fbc_program', true ),
			'weeks'   => (int) get_post_meta( $post->ID, '_fbc_weeks', true ),
			'price'   => (string) get_post_meta( $post->ID, '_fbc_price', true ),
			'spots'   => (string) get_post_meta( $post->ID, '_fbc_spots', true ),
		);
	}
	usort( $out, function ( $a, $b ) { return $a['stamp'] <=> $b['stamp']; } );
	return array_slice( $out, 0, max( 1, (int) $limit ) );
}

/**
 * The venue, as a schema.org Place. Used by every Event.
 *
 * @return array
 */
function fbaeo_venue() {
	return array(
		'@type'   => 'Place',
		'@id'     => fbaeo_id( 'place-venue' ),
		'name'    => fbaeo_get( 'venue_name' ),
		'address' => fbaeo_postal_address(),
	);
}
