<?php
/**
 * The form's questions and answer choices, in one place.
 *
 * Change a label here and it changes in the form, the admin inbox and the CSV export.
 *
 * @package Fernbrook Dog Academy Leads
 */

defined( 'ABSPATH' ) || exit;

/**
 * Answer choices for the two multiple-choice questions.
 *
 * @return array
 */
function fbl_choices() {
	return apply_filters(
		'fbl_choices',
		array(
			'program'  => array(
				'puppy'      => __( 'Puppy Foundations (8 to 18 weeks)', 'fernbrook-leads' ),
				'adolescent' => __( 'Adolescent Manners (6 to 18 months)', 'fernbrook-leads' ),
				'private'    => __( 'Private training, one to one', 'fernbrook-leads' ),
				'trail'      => __( 'Trail session, outdoors', 'fernbrook-leads' ),
				'not-sure'   => __( 'Not sure, please advise', 'fernbrook-leads' ),
			),
			'dog_age' => array(
				'puppy'   => __( 'Under 18 weeks', 'fernbrook-leads' ),
				'young'   => __( '18 weeks to 6 months', 'fernbrook-leads' ),
				'teen'    => __( '6 to 18 months', 'fernbrook-leads' ),
				'adult'   => __( 'Over 18 months', 'fernbrook-leads' ),
			),
		)
	);
}

/**
 * Hidden fields that record where the visitor came from (for ad and SEO reporting).
 *
 * @return string[]
 */
function fbl_tracking_fields() {
	return array( 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'gclid', 'fbclid', 'landing_page', 'referrer' );
}

/**
 * Human-readable label for a stored answer.
 *
 * @param string $field Field key.
 * @param string $value Stored value.
 * @return string
 */
function fbl_label( $field, $value ) {
	$choices = fbl_choices();
	return isset( $choices[ $field ][ $value ] ) ? $choices[ $field ][ $value ] : (string) $value;
}

/**
 * The consent sentence shown under the submit button. Saved with each lead as a record of what the person agreed to.
 *
 * @return string
 */
function fbl_consent_text() {
	return apply_filters(
		'fbl_consent_text',
		__( 'By sending this form you agree that Fernbrook Dog Academy may call, text or email you about a place on a course. We do not sell or share your details. Reply STOP to any text to opt out.', 'fernbrook-leads' )
	);
}

/**
 * Lead statuses for simple follow-up tracking in the admin.
 *
 * @return array
 */
function fbl_statuses() {
	return array(
		'new'       => __( 'New', 'fernbrook-leads' ),
		'contacted' => __( 'Contacted', 'fernbrook-leads' ),
		'offered'   => __( 'Place offered', 'fernbrook-leads' ),
		'booked'    => __( 'Booked', 'fernbrook-leads' ),
		'lost'      => __( 'Lost', 'fernbrook-leads' ),
		'spam'      => __( 'Suspected spam', 'fernbrook-leads' ),
	);
}

/**
 * Page people land on after a successful submit.
 *
 * @return string
 */
function fbl_thank_you_url() {
	$page_id = (int) get_option( 'fbl_thank_you_page' );
	$url     = $page_id ? get_permalink( $page_id ) : home_url( '/thank-you/' );
	return apply_filters( 'fbl_thank_you_url', $url );
}
