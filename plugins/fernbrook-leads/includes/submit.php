<?php
/**
 * Handles a submitted enquiry form.
 *
 * Flow: check for bots → validate → save the lead → email the office → send the visitor to the thank-you page.
 * If something is missing, the visitor goes back to the form with their answers kept and clear error messages.
 *
 * Spam protection without a nonce, on purpose: nonces expire and break on cached pages,
 * which silently loses real leads. Instead we use a hidden "honeypot" field, a signed
 * timestamp (bots submit instantly), and a per-visitor limit.
 *
 * Nothing is ever thrown away: anything that looks like a bot is saved with the status
 * "Suspected spam", with no email alert and no conversion event. If a real person trips
 * a check, the lead is still in the inbox.
 *
 * @package Fernbrook Dog Academy Leads
 */

defined( 'ABSPATH' ) || exit;

add_action( 'admin_post_nopriv_npl_submit', 'fbl_handle_submit' );
add_action( 'admin_post_npl_submit', 'fbl_handle_submit' );

/**
 * Signed timestamp placed in the form when it is shown.
 *
 * @return string
 */
function fbl_form_token() {
	$time = time();
	return $time . '.' . hash_hmac( 'sha256', (string) $time, wp_salt( 'nonce' ) );
}

/**
 * True if the token is genuine and the form was open for at least 3 seconds.
 * No maximum age: a cached page can be days old and still carry a real person's lead.
 *
 * @param string $token Token from the form.
 * @return bool
 */
function fbl_token_ok( $token ) {
	$parts = explode( '.', (string) $token );
	if ( 2 !== count( $parts ) ) {
		return false;
	}
	list( $time, $sig ) = $parts;
	if ( ! hash_equals( hash_hmac( 'sha256', $time, wp_salt( 'nonce' ) ), $sig ) ) {
		return false;
	}
	return time() - (int) $time >= 3;
}

/**
 * Validate the posted answers.
 *
 * @param array $in Raw input.
 * @return array{0: array, 1: array} Clean values, errors keyed by field.
 */
function fbl_validate( $in ) {
	$choices = fbl_choices();
	$v       = array(
		'zip'      => substr( preg_replace( '/\D/', '', $in['zip'] ?? '' ), 0, 5 ),
		'program'  => sanitize_key( $in['program'] ?? '' ),
		'dog_age' => sanitize_key( $in['dog_age'] ?? '' ),
		'name'     => sanitize_text_field( wp_unslash( $in['name'] ?? '' ) ),
		'phone'    => sanitize_text_field( wp_unslash( $in['phone'] ?? '' ) ),
		'email'    => sanitize_email( wp_unslash( $in['email'] ?? '' ) ),
	);
	$errors  = array();

	if ( 5 !== strlen( $v['zip'] ) ) {
		$errors['zip'] = __( 'Enter your 5-digit ZIP code.', 'fernbrook-leads' );
	}
	if ( ! isset( $choices['program'][ $v['program'] ] ) ) {
		$errors['program'] = __( 'Choose what you are interested in.', 'fernbrook-leads' );
	}
	if ( ! isset( $choices['dog_age'][ $v['dog_age'] ] ) ) {
		$errors['dog_age'] = __( "Choose your dog's age.", 'fernbrook-leads' );
	}
	if ( strlen( $v['name'] ) < 2 ) {
		$errors['name'] = __( 'Enter your first name.', 'fernbrook-leads' );
	}
	$digits = preg_replace( '/\D/', '', $v['phone'] );
	if ( 11 === strlen( $digits ) && '1' === $digits[0] ) {
		$digits = substr( $digits, 1 );
	}
	if ( 10 !== strlen( $digits ) ) {
		$errors['phone'] = __( 'Enter a 10-digit phone number, like 555 010 0142.', 'fernbrook-leads' );
	} else {
		$v['phone'] = sprintf( '(%s) %s-%s', substr( $digits, 0, 3 ), substr( $digits, 3, 3 ), substr( $digits, 6 ) );
	}
	if ( ! is_email( $v['email'] ) ) {
		$errors['email'] = __( 'Enter an email address, like name@example.com.', 'fernbrook-leads' );
	}

	foreach ( fbl_tracking_fields() as $key ) {
		$raw     = wp_unslash( $in[ $key ] ?? '' );
		$v[ $key ] = in_array( $key, array( 'landing_page', 'referrer' ), true ) ? esc_url_raw( $raw ) : sanitize_text_field( $raw );
		$v[ $key ] = substr( $v[ $key ], 0, 500 );
	}

	return array( $v, $errors );
}

/**
 * Main handler.
 */
function fbl_handle_submit() {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- see file comment.
	$in   = $_POST;
	$back = wp_get_referer() ? wp_get_referer() : home_url( '/enquire/' );

	// 1. Validate. On errors, go back with answers kept.
	list( $values, $errors ) = fbl_validate( $in );
	if ( $errors ) {
		$key = bin2hex( random_bytes( 8 ) );
		set_transient( 'fbl_err_' . $key, array( 'errors' => $errors, 'values' => $values ), 10 * MINUTE_IN_SECONDS );
		wp_safe_redirect( add_query_arg( 'npl', $key, remove_query_arg( 'npl', $back ) ) . '#fbl-form' );
		exit;
	}

	// 2. Bot checks: hidden field filled, submitted too fast or with a forged token, or more than
	//    10 leads from one visitor in 10 minutes. The IP is hashed for the count and never stored.
	$ip_key  = 'fbl_rate_' . substr( hash_hmac( 'sha256', $_SERVER['REMOTE_ADDR'] ?? '', wp_salt() ), 0, 20 ); // phpcs:ignore
	$count   = (int) get_transient( $ip_key );
	$suspect = ! empty( $in['company_website'] ) || ! fbl_token_ok( $in['fbl_token'] ?? '' ) || $count >= 10;
	set_transient( $ip_key, $count + 1, 10 * MINUTE_IN_SECONDS );

	// 3. Save.
	$lead_id = wp_insert_post(
		array(
			'post_type'   => 'fbl_lead',
			'post_status' => 'publish',
			'post_title'  => sprintf( '%s · %s', $values['name'], fbl_label( 'program', $values['program'] ) ),
			'meta_input'  => array_merge(
				$values,
				array(
					'status'       => $suspect ? 'spam' : 'new',
					'consent_at'   => current_time( 'mysql' ),
					'consent_text' => fbl_consent_text(),
				)
			),
		),
		true
	);
	if ( is_wp_error( $lead_id ) ) {
		wp_die( esc_html__( 'Sorry, something went wrong. Please call us on (555) 010-0142.', 'fernbrook-leads' ), 500 );
	}

	// Suspected spam: kept for review, but no alert and no conversion event.
	if ( $suspect ) {
		wp_safe_redirect( add_query_arg( 'lead', 'ok', fbl_thank_you_url() ) );
		exit;
	}

	// 4. Tell the office. Uses WordPress's normal email (add an SMTP plugin on a live site so it isn't marked as spam).
	$to   = apply_filters( 'fbl_notify_email', get_option( 'admin_email' ) );
	$body = sprintf(
		"New enquiry\n\nName: %s\nPhone: %s\nEmail: %s\nZIP: %s\nProgram: %s\nDog age: %s\nSource: %s\n\nOpen: %s",
		$values['name'],
		$values['phone'],
		$values['email'],
		$values['zip'],
		fbl_label( 'program', $values['program'] ),
		fbl_label( 'dog_age', $values['dog_age'] ),
		fbl_source_summary( $lead_id ),
		admin_url( 'post.php?post=' . $lead_id . '&action=edit' )
	);
	wp_mail( $to, sprintf( 'New lead: %s (%s)', $values['name'], $values['zip'] ), $body, array( 'Reply-To: ' . $values['email'] ) );

	do_action( 'fbl_lead_saved', $lead_id, $values ); // Hook for a CRM, Zapier or Slack integration.

	// 5. Thank-you page. A one-time key lets analytics count this lead exactly once (see tracking.php).
	$ref = substr( wp_hash( 'lead' . $lead_id . wp_rand() ), 0, 12 );
	set_transient( 'fbl_ref_' . $ref, $values['program'], DAY_IN_SECONDS );
	wp_safe_redirect(
		add_query_arg(
			array(
				'lead'    => 'ok',
				'program' => $values['program'],
				'ref'     => $ref,
			),
			fbl_thank_you_url()
		)
	);
	exit;
	// phpcs:enable
}

/**
 * Errors and kept answers for the form currently being shown, if the visitor was sent back.
 *
 * @return array{errors: array, values: array}
 */
function fbl_current_state() {
	static $state = null;
	if ( null !== $state ) {
		return $state;
	}
	$state = array( 'errors' => array(), 'values' => array() );
	$key   = isset( $_GET['npl'] ) ? sanitize_key( $_GET['npl'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( $key ) {
		$saved = get_transient( 'fbl_err_' . $key );
		if ( is_array( $saved ) ) {
			$state = $saved;
		}
	}
	return $state;
}
