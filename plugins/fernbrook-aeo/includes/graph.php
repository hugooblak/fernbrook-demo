<?php
/**
 * The entity graph.
 *
 * Every page prints ONE script tag holding a @graph: a list of connected nodes, each with a
 * stable @id, pointing at each other. Not five separate blocks that happen to sit on the same
 * page. That is the difference between "this page mentions a business" and "this page is part
 * of a business that a machine can follow around the site".
 *
 * Business-level @id values hang off the site root and never move.
 * Page-level @id values hang off the page address.
 *
 * @package Fernbrook AI Search
 */

defined( 'ABSPATH' ) || exit;

/**
 * Title tag for a post: what the editor set, or the post title plus the site name.
 *
 * @param WP_Post $post Post.
 * @return string
 */
function fbaeo_title_for( $post ) {
	$set = fbaeo_meta( $post->ID, '_fbaeo_title' );
	if ( $set ) {
		return $set;
	}
	$title = get_the_title( $post );
	$name  = fbaeo_get( 'name', get_bloginfo( 'name' ) );
	return $title && false === strpos( $title, $name ) ? $title . ' | ' . $name : $title;
}

/**
 * Meta description for a post: what the editor set, or the excerpt.
 *
 * @param WP_Post $post Post.
 * @return string
 */
function fbaeo_description_for( $post ) {
	$set = fbaeo_meta( $post->ID, '_fbaeo_description' );
	if ( $set ) {
		return $set;
	}
	$excerpt = has_excerpt( $post ) ? get_the_excerpt( $post ) : '';
	return trim( wp_strip_all_tags( $excerpt ) );
}

/**
 * Canonical address for a post.
 *
 * @param WP_Post $post Post.
 * @return string
 */
function fbaeo_canonical_for( $post ) {
	$set = fbaeo_meta( $post->ID, '_fbaeo_canonical' );
	return $set ? $set : (string) get_permalink( $post );
}

/**
 * The entity slug used in this page's own @id values.
 *
 * @param WP_Post $post Post.
 * @return string
 */
function fbaeo_entity_slug( $post ) {
	$set = fbaeo_meta( $post->ID, '_fbaeo_entity' );
	return $set ? $set : $post->post_name;
}

/* ---------------------------------------------------------------------------
 * Business-level nodes. The same on every page, which is what makes them stable.
 * ------------------------------------------------------------------------ */

/**
 * Organization, LocalBusiness, WebSite, the people, and the venue.
 *
 * @return array
 */
function fbaeo_business_nodes() {
	$base   = fbaeo_base();
	$people = fbaeo_get( 'people', array() );

	$logo = array(
		'@type'      => 'ImageObject',
		'@id'        => fbaeo_id( 'logo' ),
		'url'        => fbaeo_logo_url(),
		'contentUrl' => fbaeo_logo_url(),
		'caption'    => fbaeo_get( 'name' ),
	);

	$organization = array(
		'@type'         => 'Organization',
		'@id'           => fbaeo_id( 'organization' ),
		'name'          => fbaeo_get( 'legal_name' ),
		'alternateName' => fbaeo_get( 'name' ),
		'url'           => $base,
		'logo'          => array( '@id' => fbaeo_id( 'logo' ) ),
		'image'         => array( '@id' => fbaeo_id( 'logo' ) ),
		'foundingDate'  => fbaeo_get( 'founding_date' ),
		'email'         => fbaeo_get( 'email' ),
		'telephone'     => fbaeo_get( 'phone' ),
		'address'       => fbaeo_postal_address(),
		'sameAs'        => array_values( fbaeo_get( 'same_as', array() ) ),
	);

	$areas = array();
	foreach ( fbaeo_get( 'areas', array() ) as $area ) {
		$areas[] = array( '@type' => 'City', 'name' => $area );
	}

	$employees = array();
	foreach ( $people as $person ) {
		$employees[] = array( '@id' => fbaeo_id( 'person-' . $person['slug'] ) );
	}

	$local = array(
		'@type'                    => fbaeo_get( 'business_type', 'LocalBusiness' ),
		'@id'                      => fbaeo_id( 'localbusiness' ),
		'name'                     => fbaeo_get( 'name' ),
		'description'              => fbaeo_get( 'description' ),
		'url'                      => $base,
		'parentOrganization'       => array( '@id' => fbaeo_id( 'organization' ) ),
		'telephone'                => fbaeo_get( 'phone' ),
		'email'                    => fbaeo_get( 'email' ),
		'address'                  => fbaeo_postal_address(),
		'geo'                      => array(
			'@type'     => 'GeoCoordinates',
			'latitude'  => fbaeo_get( 'latitude' ),
			'longitude' => fbaeo_get( 'longitude' ),
		),
		'openingHoursSpecification' => fbaeo_opening_hours(),
		'areaServed'               => $areas,
		'priceRange'               => fbaeo_get( 'price_range' ),
		'image'                    => array( '@id' => fbaeo_id( 'logo' ) ),
		'knowsAbout'               => array_values( fbaeo_get( 'knows_about', array() ) ),
		'sameAs'                   => array_values( fbaeo_get( 'same_as', array() ) ),
	);
	if ( fbaeo_get( 'additional_type' ) ) {
		$local['additionalType'] = fbaeo_get( 'additional_type' );
	}
	if ( $employees ) {
		$local['employee'] = $employees;
	}

	$website = array(
		'@type'      => 'WebSite',
		'@id'        => fbaeo_id( 'website' ),
		'url'        => $base,
		'name'       => fbaeo_get( 'name' ),
		'publisher'  => array( '@id' => fbaeo_id( 'organization' ) ),
		'inLanguage' => 'en-US',
	);

	$nodes = array( $logo, $organization, $local, $website, fbaeo_venue() );

	$about_page = get_page_by_path( 'about' );
	foreach ( $people as $person ) {
		$node = array(
			'@type'       => 'Person',
			'@id'         => fbaeo_id( 'person-' . $person['slug'] ),
			'name'        => $person['name'],
			'jobTitle'    => $person['job'],
			'description' => $person['bio'],
			'worksFor'    => array( '@id' => fbaeo_id( 'localbusiness' ) ),
		);
		if ( ! empty( $person['same_as'] ) ) {
			$node['sameAs'] = array_values( $person['same_as'] );
		}
		if ( $about_page ) {
			$node['mainEntityOfPage'] = array( '@id' => get_permalink( $about_page ) . '#webpage' );
		}
		$nodes[] = $node;
	}

	return $nodes;
}

/* ---------------------------------------------------------------------------
 * Page-level nodes.
 * ------------------------------------------------------------------------ */

/**
 * Questions and answers read straight out of the page's own Details blocks.
 *
 * Reading the page instead of a separate field is deliberate. Structured data that says
 * something the visitor cannot see is exactly what Google penalises.
 *
 * @param WP_Post $post Post.
 * @return array[] Each: question, answer.
 */
function fbaeo_faq_items( $post ) {
	$items  = array();
	$blocks = parse_blocks( $post->post_content );
	$walk   = function ( $blocks ) use ( &$walk, &$items ) {
		foreach ( $blocks as $block ) {
			if ( 'core/details' === $block['blockName'] ) {
				$html = render_block( $block );
				if ( preg_match( '#<summary[^>]*>(.*?)</summary>(.*)#si', $html, $m ) ) {
					$answer = wp_strip_all_tags( preg_replace( '#</details>\s*$#i', '', $m[2] ) );
					$items[] = array(
						'question' => trim( wp_strip_all_tags( $m[1] ) ),
						'answer'   => trim( preg_replace( '/\s+/', ' ', $answer ) ),
					);
				}
			}
			if ( ! empty( $block['innerBlocks'] ) ) {
				$walk( $block['innerBlocks'] );
			}
		}
	};
	$walk( $blocks );
	return $items;
}

/**
 * True when this page shows the class list block, so Event data belongs on it.
 *
 * @param WP_Post $post Post.
 * @return bool
 */
function fbaeo_has_class_list( $post ) {
	return false !== strpos( $post->post_content, 'wp:fernbrook/class-list' );
}

/**
 * The trail of pages above this one.
 *
 * @param WP_Post $post Post.
 * @return array
 */
function fbaeo_breadcrumb( $post ) {
	$items = array(
		array(
			'@type'    => 'ListItem',
			'position' => 1,
			'name'     => __( 'Home', 'fernbrook-aeo' ),
			'item'     => fbaeo_base(),
		),
	);
	if ( 'post' === $post->post_type ) {
		$guides = get_page_by_path( 'guides' );
		if ( $guides ) {
			$items[] = array(
				'@type'    => 'ListItem',
				'position' => 2,
				'name'     => get_the_title( $guides ),
				'item'     => get_permalink( $guides ),
			);
		}
	}
	$items[] = array(
		'@type'    => 'ListItem',
		'position' => count( $items ) + 1,
		'name'     => get_the_title( $post ),
		'item'     => fbaeo_canonical_for( $post ),
	);
	return array(
		'@type'           => 'BreadcrumbList',
		'@id'             => fbaeo_canonical_for( $post ) . '#breadcrumb',
		'itemListElement' => $items,
	);
}

/**
 * Course instances for a program page, built from the class records.
 *
 * @param int $program_id Program page ID.
 * @return array
 */
function fbaeo_course_instances( $program_id ) {
	$out = array();
	foreach ( fbaeo_upcoming_classes( 12 ) as $class ) {
		if ( $class['program'] !== (int) $program_id ) {
			continue;
		}
		$weeks = $class['weeks'] ? $class['weeks'] : 6;
		$end   = gmdate( 'Y-m-d', strtotime( $class['date'] . ' +' . ( $weeks - 1 ) . ' weeks' ) );
		$out[] = array(
			'@type'          => 'CourseInstance',
			'@id'            => fbaeo_id( 'courseinstance-' . $class['id'] ),
			'courseMode'     => 'Onsite',
			'location'       => array( '@id' => fbaeo_id( 'place-venue' ) ),
			'startDate'      => $class['date'] . 'T' . $class['time'],
			'endDate'        => $end . 'T' . $class['time'],
			'courseWorkload' => 'PT1H',
			// The weekly repeat belongs in a Schedule, not loose on the instance.
			'courseSchedule' => array(
				'@type'           => 'Schedule',
				'startDate'       => $class['date'],
				'endDate'         => $end,
				'startTime'       => $class['time'],
				'repeatFrequency' => 'P1W',
				'repeatCount'     => $weeks,
				'duration'        => 'PT1H',
			),
		);
	}
	return $out;
}

/**
 * Event nodes for the upcoming classes.
 *
 * @param int $limit How many.
 * @return array
 */
function fbaeo_event_nodes( $limit = 6 ) {
	$nodes = array();
	foreach ( fbaeo_upcoming_classes( $limit ) as $class ) {
		$program = $class['program'] ? get_post( $class['program'] ) : null;
		$weeks   = $class['weeks'] ? $class['weeks'] : 6;
		$node    = array(
			'@type'                => 'EducationEvent',
			'@id'                  => fbaeo_id( 'event-' . $class['id'] ),
			'name'                 => $class['title'],
			'startDate'            => $class['date'] . 'T' . $class['time'],
			'endDate'              => gmdate( 'Y-m-d', strtotime( $class['date'] . ' +' . ( $weeks - 1 ) . ' weeks' ) ) . 'T' . $class['time'],
			'eventStatus'          => 'https://schema.org/EventScheduled',
			'eventAttendanceMode'  => 'https://schema.org/OfflineEventAttendanceMode',
			'location'             => array( '@id' => fbaeo_id( 'place-venue' ) ),
			'organizer'            => array( '@id' => fbaeo_id( 'organization' ) ),
		);
		if ( $program ) {
			$node['description'] = fbaeo_description_for( $program );
			$node['about']       = array( '@id' => fbaeo_id( 'course-' . fbaeo_entity_slug( $program ) ) );
			$node['url']         = get_permalink( $program );
		}
		if ( '' !== $class['price'] ) {
			$node['offers'] = array(
				'@type'         => 'Offer',
				'price'         => preg_replace( '/[^0-9.]/', '', $class['price'] ),
				'priceCurrency' => fbaeo_get( 'currency', 'USD' ),
				'availability'  => ( '0' === $class['spots'] ) ? 'https://schema.org/SoldOut' : 'https://schema.org/InStock',
				'url'           => home_url( '/enquire/' ),
				'validFrom'     => gmdate( 'Y-m-d' ),
			);
		}
		$nodes[] = $node;
	}
	return $nodes;
}

/**
 * The full graph for one post or page.
 *
 * @param WP_Post $post Post.
 * @return array
 */
function fbaeo_graph_for_post( $post ) {
	$nodes     = fbaeo_business_nodes();
	$url       = fbaeo_canonical_for( $post );
	$type      = fbaeo_meta( $post->ID, '_fbaeo_type' );
	$slug      = fbaeo_entity_slug( $post );
	$faq_items = fbaeo_faq_items( $post );

	$page_types = array(
		'contact'    => 'ContactPage',
		'about'      => 'AboutPage',
		'collection' => 'CollectionPage',
	);
	$page_type  = isset( $page_types[ $type ] ) ? $page_types[ $type ] : 'WebPage';
	$page_type  = ( 'article' === $type ) ? 'WebPage' : $page_type;
	$types      = array( $page_type );
	if ( $faq_items ) {
		$types[] = 'FAQPage';
	}

	$webpage = array(
		'@type'            => count( $types ) > 1 ? $types : $types[0],
		'@id'              => $url . '#webpage',
		'url'              => $url,
		'name'             => fbaeo_title_for( $post ),
		'description'      => fbaeo_description_for( $post ),
		'isPartOf'         => array( '@id' => fbaeo_id( 'website' ) ),
		'about'            => array( '@id' => fbaeo_id( 'localbusiness' ) ),
		'breadcrumb'       => array( '@id' => $url . '#breadcrumb' ),
		'inLanguage'       => 'en-US',
		'datePublished'    => get_the_date( 'c', $post ),
		'dateModified'     => get_the_modified_date( 'c', $post ),
		'publisher'        => array( '@id' => fbaeo_id( 'organization' ) ),
	);
	if ( $faq_items ) {
		$main = array();
		foreach ( $faq_items as $item ) {
			$main[] = array(
				'@type'          => 'Question',
				'name'           => $item['question'],
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => $item['answer'],
				),
			);
		}
		$webpage['mainEntity'] = $main;
	}
	$nodes[] = $webpage;
	$nodes[] = fbaeo_breadcrumb( $post );

	// Service.
	if ( 'service' === $type || 'course' === $type ) {
		$service = array(
			'@type'            => 'Service',
			'@id'              => fbaeo_id( 'service-' . $slug ),
			'name'             => get_the_title( $post ),
			'description'      => fbaeo_description_for( $post ),
			'serviceType'      => __( 'Dog training', 'fernbrook-aeo' ),
			'provider'         => array( '@id' => fbaeo_id( 'localbusiness' ) ),
			'areaServed'       => array_values( fbaeo_get( 'areas', array() ) ),
			'url'              => $url,
			'mainEntityOfPage' => array( '@id' => $url . '#webpage' ),
		);
		$price = fbaeo_meta( $post->ID, '_fbaeo_price' );
		if ( $price ) {
			$service['offers'] = array(
				'@type'         => 'Offer',
				'price'         => preg_replace( '/[^0-9.]/', '', $price ),
				'priceCurrency' => fbaeo_get( 'currency', 'USD' ),
				'availability'  => 'https://schema.org/InStock',
				'url'           => home_url( '/enquire/' ),
			);
		}
		$audience = fbaeo_meta( $post->ID, '_fbaeo_audience' );
		if ( $audience ) {
			$service['audience'] = array( '@type' => 'Audience', 'audienceType' => $audience );
		}
		$nodes[] = $service;
	}

	// Course.
	if ( 'course' === $type ) {
		$course = array(
			'@type'            => 'Course',
			'@id'              => fbaeo_id( 'course-' . $slug ),
			'name'             => get_the_title( $post ),
			'description'      => fbaeo_description_for( $post ),
			'url'              => $url,
			'provider'         => array( '@id' => fbaeo_id( 'organization' ) ),
			'isRelatedTo'      => array( '@id' => fbaeo_id( 'service-' . $slug ) ),
			'inLanguage'       => 'en-US',
			'mainEntityOfPage' => array( '@id' => $url . '#webpage' ),
		);
		$instances = fbaeo_course_instances( $post->ID );
		if ( $instances ) {
			$course['hasCourseInstance'] = $instances;
		}
		$price = fbaeo_meta( $post->ID, '_fbaeo_price' );
		if ( $price ) {
			$course['offers'] = array(
				'@type'         => 'Offer',
				'category'      => 'Paid',
				'price'         => preg_replace( '/[^0-9.]/', '', $price ),
				'priceCurrency' => fbaeo_get( 'currency', 'USD' ),
				'url'           => home_url( '/enquire/' ),
			);
		}
		$weeks = fbaeo_meta( $post->ID, '_fbaeo_weeks' );
		if ( $weeks ) {
			$course['timeRequired'] = 'P' . (int) $weeks . 'W';
		}
		$nodes[] = $course;
	}

	// Article.
	if ( 'article' === $type || 'post' === $post->post_type ) {
		$person = fbaeo_person( fbaeo_meta( $post->ID, '_fbaeo_author' ) );
		if ( ! $person ) {
			$person = fbaeo_default_author();
		}
		$article = array(
			'@type'            => 'Article',
			'@id'              => $url . '#article',
			'headline'         => get_the_title( $post ),
			'description'      => fbaeo_description_for( $post ),
			'datePublished'    => get_the_date( 'c', $post ),
			'dateModified'     => get_the_modified_date( 'c', $post ),
			'publisher'        => array( '@id' => fbaeo_id( 'organization' ) ),
			'isPartOf'         => array( '@id' => $url . '#webpage' ),
			'mainEntityOfPage' => array( '@id' => $url . '#webpage' ),
			'about'            => array( '@id' => fbaeo_id( 'localbusiness' ) ),
			'inLanguage'       => 'en-US',
		);
		if ( $person ) {
			$article['author'] = array( '@id' => fbaeo_id( 'person-' . $person['slug'] ) );
		}
		$nodes[] = $article;
	}

	// Video, only when every field is filled in.
	$video = array(
		'name'  => fbaeo_meta( $post->ID, '_fbaeo_video_name' ),
		'desc'  => fbaeo_meta( $post->ID, '_fbaeo_video_desc' ),
		'url'   => fbaeo_meta( $post->ID, '_fbaeo_video_url' ),
		'thumb' => fbaeo_meta( $post->ID, '_fbaeo_video_thumb' ),
		'date'  => fbaeo_meta( $post->ID, '_fbaeo_video_date' ),
	);
	if ( ! in_array( '', $video, true ) ) {
		$nodes[] = array(
			'@type'        => 'VideoObject',
			'@id'          => $url . '#video',
			'name'         => $video['name'],
			'description'  => $video['desc'],
			'thumbnailUrl' => $video['thumb'],
			'contentUrl'   => $video['url'],
			'uploadDate'   => $video['date'],
			'publisher'    => array( '@id' => fbaeo_id( 'organization' ) ),
			'isPartOf'     => array( '@id' => $url . '#webpage' ),
		);
	}

	// Events, on pages that actually show the class list.
	if ( fbaeo_has_class_list( $post ) ) {
		$events = fbaeo_event_nodes( 6 );
		foreach ( $events as $event ) {
			$nodes[] = $event;
		}
	}

	return $nodes;
}

/**
 * The graph for whatever WordPress is showing right now, or an empty array when there is none.
 *
 * @return array
 */
function fbaeo_graph_for_current() {
	if ( is_singular( array( 'page', 'post' ) ) ) {
		$post = get_queried_object();
		return $post instanceof WP_Post ? fbaeo_graph_for_post( $post ) : array();
	}
	if ( is_home() ) {
		$page = get_option( 'page_for_posts' ) ? get_post( (int) get_option( 'page_for_posts' ) ) : null;
		if ( $page ) {
			return fbaeo_graph_for_post( $page );
		}
	}
	return array();
}

/**
 * Every @id this site publishes anywhere. The validator uses it to tell a real cross-page
 * reference from a typo.
 *
 * @return string[]
 */
function fbaeo_known_ids() {
	$ids = array();
	foreach ( fbaeo_business_nodes() as $node ) {
		if ( ! empty( $node['@id'] ) ) {
			$ids[] = $node['@id'];
		}
	}
	$posts = get_posts(
		array(
			'post_type'   => array( 'page', 'post' ),
			'numberposts' => 200,
			'post_status' => 'publish',
		)
	);
	foreach ( $posts as $post ) {
		$url  = fbaeo_canonical_for( $post );
		$slug = fbaeo_entity_slug( $post );
		$type = fbaeo_meta( $post->ID, '_fbaeo_type' );
		$ids[] = $url . '#webpage';
		$ids[] = $url . '#breadcrumb';
		if ( 'article' === $type || 'post' === $post->post_type ) {
			$ids[] = $url . '#article';
		}
		if ( 'service' === $type || 'course' === $type ) {
			$ids[] = fbaeo_id( 'service-' . $slug );
		}
		if ( 'course' === $type ) {
			$ids[] = fbaeo_id( 'course-' . $slug );
		}
	}
	foreach ( fbaeo_upcoming_classes( 50 ) as $class ) {
		$ids[] = fbaeo_id( 'event-' . $class['id'] );
		$ids[] = fbaeo_id( 'courseinstance-' . $class['id'] );
	}
	return array_values( array_unique( $ids ) );
}
