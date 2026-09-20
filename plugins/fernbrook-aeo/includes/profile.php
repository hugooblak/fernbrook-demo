<?php
/**
 * The business profile: every fact about the business, typed once.
 *
 * Everything else — the entity graph, the Open Graph tags, llms.txt, the validation screen —
 * reads from here. That is the point: one wrong phone number should be one edit, not nine.
 *
 * @package Fernbrook AI Search
 */

defined( 'ABSPATH' ) || exit;

const FBAEO_OPTION = 'fbaeo_profile';

/**
 * Sample values, written for this demo. On a real site these are the client's own details.
 *
 * @return array
 */
function fbaeo_profile_defaults() {
	return array(
		'legal_name'      => 'Fernbrook Dog Academy LLC',
		'name'            => 'Fernbrook Dog Academy',
		'business_type'   => 'LocalBusiness',
		'additional_type' => 'https://en.wikipedia.org/wiki/Dog_training',
		'description'     => 'Reward-based puppy development and dog training in Alder Bay, California. Six-week group courses, private sessions at home, and trail work for dogs that struggle outdoors.',
		'founding_date'   => '2016',
		'phone'           => '+1-555-010-0188',
		'phone_display'   => '(555) 010-0188',
		'email'           => 'hello@example.com',
		'street'          => '47 Harbour Lane',
		'locality'        => 'Alder Bay',
		'region'          => 'CA',
		'postal_code'     => '94099',
		'country'         => 'US',
		'latitude'        => '38.0000',
		'longitude'       => '-123.0000',
		'price_range'     => '$$',
		'currency'        => 'USD',
		'areas'           => array( 'Alder Bay', 'Harrowfield', 'Quarry Hill', 'Selkirk Ferry', 'Ravensmoor' ),
		'hours'           => array(
			array( 'days' => array( 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' ), 'opens' => '08:00', 'closes' => '18:00' ),
			array( 'days' => array( 'Sunday' ), 'opens' => '09:00', 'closes' => '14:00' ),
		),
		'same_as'         => array(
			'https://www.example.com/fernbrook-dog-academy',
			'https://maps.example.com/place/fernbrook-dog-academy',
		),
		'knows_about'     => array( 'Puppy socialisation', 'Reward-based dog training', 'Loose lead walking', 'Recall training', 'Lead reactivity' ),
		'gsc_token'       => '',
		'people'          => array(
			array(
				'slug'     => 'dana-whitlock',
				'name'     => 'Dana Whitlock',
				'job'      => 'Owner and head trainer',
				'bio'      => 'Dana has run group classes in Alder Bay since 2016 and teaches the adolescent and private work. Certified dog trainer (sample credential for this demo).',
				'same_as'  => array( 'https://www.example.com/people/dana-whitlock' ),
				'is_author' => '1',
			),
			array(
				'slug'     => 'priya-raman',
				'name'     => 'Priya Raman',
				'job'      => 'Puppy class instructor',
				'bio'      => 'Priya runs the puppy courses and the Saturday morning drop-in. Veterinary nurse before she trained dogs full time (sample credential for this demo).',
				'same_as'  => array( 'https://www.example.com/people/priya-raman' ),
				'is_author' => '',
			),
		),
		'venue_name'      => 'Harbour Lane hall',
	);
}

/**
 * The saved profile, with defaults filled in for anything missing.
 *
 * @return array
 */
function fbaeo_profile() {
	$saved = get_option( FBAEO_OPTION, array() );
	if ( ! is_array( $saved ) ) {
		$saved = array();
	}
	return array_merge( fbaeo_profile_defaults(), $saved );
}

/**
 * One field from the profile.
 *
 * @param string $key     Field name.
 * @param mixed  $default Value when the field is empty.
 * @return mixed
 */
function fbaeo_get( $key, $default = '' ) {
	$profile = fbaeo_profile();
	return ( isset( $profile[ $key ] ) && '' !== $profile[ $key ] ) ? $profile[ $key ] : $default;
}

/**
 * The site's address with a trailing slash. Every stable @id is built from this.
 *
 * @return string
 */
function fbaeo_base() {
	return trailingslashit( home_url( '/' ) );
}

/**
 * A stable @id for a business-level entity.
 *
 * These hang off the site root, not off a page, so they keep working when a page is renamed
 * or moved. Page-level entities (#webpage, #breadcrumb, #article) hang off the page instead.
 *
 * @param string $fragment For example "organization" or "person-dana-whitlock".
 * @return string
 */
function fbaeo_id( $fragment ) {
	return fbaeo_base() . '#' . ltrim( $fragment, '#' );
}

/**
 * One person from the profile, by slug.
 *
 * @param string $slug Person slug.
 * @return array|null
 */
function fbaeo_person( $slug ) {
	foreach ( fbaeo_get( 'people', array() ) as $person ) {
		if ( $person['slug'] === $slug ) {
			return $person;
		}
	}
	return null;
}

/**
 * The person used as the author when a post does not name one.
 *
 * @return array|null
 */
function fbaeo_default_author() {
	foreach ( fbaeo_get( 'people', array() ) as $person ) {
		if ( ! empty( $person['is_author'] ) ) {
			return $person;
		}
	}
	$people = fbaeo_get( 'people', array() );
	return $people ? $people[0] : null;
}

/**
 * Opening hours in the shape schema.org wants.
 *
 * @return array
 */
function fbaeo_opening_hours() {
	$out = array();
	foreach ( fbaeo_get( 'hours', array() ) as $slot ) {
		if ( empty( $slot['days'] ) ) {
			continue;
		}
		$out[] = array(
			'@type'     => 'OpeningHoursSpecification',
			'dayOfWeek' => array_values( $slot['days'] ),
			'opens'     => $slot['opens'],
			'closes'    => $slot['closes'],
		);
	}
	return $out;
}

/**
 * The postal address in the shape schema.org wants.
 *
 * @return array
 */
function fbaeo_postal_address() {
	return array(
		'@type'           => 'PostalAddress',
		'streetAddress'   => fbaeo_get( 'street' ),
		'addressLocality' => fbaeo_get( 'locality' ),
		'addressRegion'   => fbaeo_get( 'region' ),
		'postalCode'      => fbaeo_get( 'postal_code' ),
		'addressCountry'  => fbaeo_get( 'country' ),
	);
}

/**
 * The site logo, as a URL. Falls back to the theme's mark.
 *
 * @return string
 */
function fbaeo_logo_url() {
	$custom = get_theme_mod( 'custom_logo' );
	if ( $custom ) {
		$src = wp_get_attachment_image_src( $custom, 'full' );
		if ( $src ) {
			return $src[0];
		}
	}
	return get_theme_file_uri( 'assets/img/mark.svg' );
}
