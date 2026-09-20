<?php
/**
 * Plugin Name: Fernbrook Dog Academy Demo Notes
 * Description: Portfolio helper. Shows a "this is a demo" bar and, when switched on, a note above each section explaining the search and AI-assistant decision behind it. Delete this plugin on a real site.
 * Version: 1.0.0
 * Requires at least: 6.6
 * Author: Hugo Oblak
 * License: GPL-2.0-or-later
 *
 * The notes are not stored in page content, so editors never see them and they vanish with the plugin.
 *
 * @package Fernbrook Dog Academy Demo Notes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Notes on or off. ?notes=1 turns them on (and remembers it in a cookie while you browse), ?notes=0 turns them off.
 *
 * @return bool
 */
function fbd_notes_on() {
	static $on = null;
	if ( null === $on ) {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['notes'] ) ) {
			$on = '1' === $_GET['notes'];
		} else {
			$on = isset( $_COOKIE['fbd_notes'] ) && '1' === $_COOKIE['fbd_notes'];
		}
		// phpcs:enable
	}
	return $on;
}

add_action(
	'send_headers',
	function () {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['notes'] ) && ! headers_sent() ) {
			setcookie( 'fbd_notes', fbd_notes_on() ? '1' : '0', 0, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, is_ssl(), true );
		}
	}
);

/**
 * The notes, keyed by the section class used in the theme's patterns.
 *
 * @return array
 */
function fbd_notes() {
	return array(
		'fb-sec-hero'     => array(
			'Answer first, in the first paragraph',
			'An assistant quoting this page will take the first clear sentence that answers the question. So the opening paragraph says what the business does, for which dogs, where, and at what class size — in one sentence, no build-up. The H1 is the only H1 on the page, and it names the thing plus the town.',
		),
		'fb-sec-trust'    => array(
			'Facts a machine can check',
			'Short, specific claims: six dogs per class, reward-based only, a reply in one working day. Each of these also appears in the LocalBusiness and Course data, so the page and the structured data say the same thing. Icons are one-kilobyte SVGs.',
		),
		'fb-sec-programs' => array(
			'One page per thing you sell',
			'Each program gets its own page, its own Service and Course node, and its own stable @id. Lumping three programs onto one page gives a machine one blurry entity instead of three sharp ones.',
		),
		'fb-sec-steps'    => array(
			'Process written as steps',
			'A numbered list of what happens next is one of the formats assistants quote most readily, because it survives being pulled out of the page. It also answers the question people actually have.',
		),
		'fb-sec-dates'    => array(
			'One record, two outputs',
			'The dates you can see here come from the Classes screen in the admin. The same records generate the EducationEvent data and the CourseInstance entries on each program page. Nothing is typed twice, so the page and the schema cannot drift apart.',
		),
		'fb-sec-price'    => array(
			'Publish the price',
			'Price is the first question, and a page that does not answer it will not be quoted for it. The same figures go into the Offer inside the Course and Service data.',
		),
		'fb-sec-approach' => array(
			'Say what you will not do',
			'Specific commitments ("no prong collars, no shock collars, you keep the lead") are what make a page quotable, because they answer a question competitors answer vaguely.',
		),
		'fb-sec-guides'   => array(
			'Internal links a machine can follow',
			'The home page links into the guides, and every guide links back to the program it is about. That is what turns a pile of pages into one entity a machine can navigate. The Headings and links screen flags any page nothing links to.',
		),
		'fb-sec-answers'  => array(
			'FAQ data read off the page',
			'These are WordPress\'s own Details blocks. The AI Search plugin reads them and builds the FAQ structured data from them, rather than from a separate field. Structured data that says something the visitor cannot see is exactly what gets a site penalised.',
		),
		'fb-sec-final'    => array(
			'One action, repeated',
			'The same low-effort first step as the hero. A page with one clear action is easier for a person to act on and easier for an assistant to summarise.',
		),
		'fb-sec-weeks'    => array(
			'A syllabus is quotable content',
			'Week-by-week detail gives an assistant something specific to answer "what does a puppy course actually cover" with. It is also the Course description a competitor page usually leaves vague.',
		),
		'fb-sec-facts'    => array(
			'Facts in a table',
			'Ages, length, class size, price and what to bring, in one place. Tables survive extraction better than the same facts spread across paragraphs.',
		),
		'fb-sec-limits'   => array(
			'Limits, in writing',
			'"We will not promise a fixed number of sessions" is a real answer to a real question. Honest limits are quotable; marketing claims are not.',
		),
		'fb-sec-funnel'   => array(
			'Focus page: nothing to click except the form',
			'No menu and no footer links, so there are no exits. One question per step, big choice cards that move on by themselves, contact details asked last. Hidden fields record where the visitor came from — including referrals from assistants, which is how you find out whether any of this is working. Works without JavaScript as a normal form.',
		),
		'fb-sec-thanks'   => array(
			'Thank-you page, kept out of search',
			'Useful to the person who just sent the form, useless in a search result, so it is marked noindex in the AI search panel. The Validate screen shows that as a deliberate note rather than a failure.',
		),
	);
}

/**
 * Put a note in front of each matching section when notes are on.
 */
add_filter(
	'render_block',
	function ( $html, $block ) {
		if ( is_admin() || ! fbd_notes_on() || empty( $block['attrs']['className'] ) ) {
			return $html;
		}
		foreach ( fbd_notes() as $class => $note ) {
			if ( preg_match( '/(^|\s)' . preg_quote( $class, '/' ) . '(\s|$)/', $block['attrs']['className'] ) ) {
				return sprintf(
					'<aside class="fbd-note" aria-label="%s"><p class="fbd-note-title"><span>AI search note</span> %s</p><p>%s</p></aside>',
					esc_attr( 'AI search note: ' . $note[0] ),
					esc_html( $note[0] ),
					esc_html( $note[1] )
				) . $html;
			}
		}
		return $html;
	},
	10,
	2
);

/**
 * Demo bar at the top of every page.
 *
 * Printed in front of the header template part rather than on wp_body_open. WordPress inserts
 * its skip link just before the header, so a bar printed earlier would steal the first Tab
 * stop from it.
 */
add_filter(
	'render_block',
	function ( $html, $block ) {
		static $done = false;
		if ( $done || is_admin() || 'core/template-part' !== $block['blockName'] ) {
			return $html;
		}
		$slug = $block['attrs']['slug'] ?? '';
		if ( 'header' !== $slug && 'header-focus' !== $slug ) {
			return $html;
		}
		$done   = true;
		$toggle = add_query_arg( 'notes', fbd_notes_on() ? '0' : '1' );
		$bar    = sprintf(
			'<div class="fbd-bar" role="region" aria-label="%s"><p><strong>%s</strong> %s <a href="%s">%s</a></p></div>',
			esc_attr__( 'Demo notice', 'default' ),
			esc_html( 'Portfolio demo.' ),
			esc_html( 'Fernbrook Dog Academy is a fictional business in a fictional town; names, prices and dates are sample content.' ),
			esc_url( $toggle ),
			esc_html( fbd_notes_on() ? 'Hide the notes' : 'Show the AI search notes behind each section' )
		);
		return $bar . $html;
	},
	10,
	2
);

add_action(
	'wp_head',
	function () {
		?>
		<style id="fbd-css">
			.fbd-bar{background:#16202B;color:#E9EEF2;font-size:.875rem;line-height:1.4;padding:.5rem 1rem;text-align:center}
			.fbd-bar p{margin:0}.fbd-bar a{color:#E3A13D;font-weight:700;margin-left:.35rem}
			.fbd-note{max-width:1180px;margin:1rem auto;box-sizing:border-box;width:calc(100% - 2rem);background:#FFF8DB;border:2px dashed #A37A05;border-radius:10px;padding:.8rem 1rem;color:#3D2E00;font-size:.9375rem;line-height:1.5}
			.fbd-note p{margin:0}.fbd-note-title{font-weight:800;margin-bottom:.25rem !important}
			.fbd-note-title span{display:inline-block;background:#3D2E00;color:#FFF8DB;font-size:.6875rem;letter-spacing:.06em;text-transform:uppercase;border-radius:4px;padding:.1rem .4rem;margin-right:.35rem;vertical-align:.1em}
		</style>
		<?php
	}
);

/**
 * A short guide on the Dashboard for anyone reviewing the demo in WordPress Playground.
 */
add_action(
	'wp_dashboard_setup',
	function () {
		wp_add_dashboard_widget(
			'fbd_guide',
			'Fernbrook Dog Academy demo: where to look',
			function () {
				$items = array(
					array( admin_url( 'admin.php?page=fernbrook-ai-search-validate' ), 'Validate: twelve checks on every page, with the JSON-LD' ),
					array( admin_url( 'admin.php?page=fernbrook-ai-search' ), 'AI Search overview: the stable @id values the site uses' ),
					array( admin_url( 'admin.php?page=fernbrook-ai-search-profile' ), 'Business profile: the facts typed once and reused everywhere' ),
					array( admin_url( 'admin.php?page=fernbrook-ai-search-crawlers' ), 'Crawler policy and llms.txt' ),
					array( admin_url( 'admin.php?page=fernbrook-ai-search-redirects' ), 'Redirects, with the chain and loop check' ),
					array( admin_url( 'admin.php?page=fernbrook-ai-search-audit' ), 'Headings, internal links and page weight' ),
					array( admin_url( 'edit.php?post_type=fb_class' ), 'Classes: the records behind the dates and the Event data' ),
					array( home_url( '/?notes=1' ), 'Home page with the AI search notes switched on' ),
					array( home_url( '/llms.txt' ), 'The generated llms.txt' ),
					array( home_url( '/robots.txt' ), 'robots.txt with the AI crawler policy' ),
					array( admin_url( 'post.php?post=' . get_option( 'page_on_front' ) . '&action=edit' ), 'Edit the home page: normal blocks, no page builder' ),
					array( admin_url( 'edit.php?post_type=fbl_lead' ), 'Enquiries inbox, with status and CSV export' ),
				);
				echo '<ol>';
				foreach ( $items as $item ) {
					printf( '<li><a href="%s">%s</a></li>', esc_url( $item[0] ), esc_html( $item[1] ) );
				}
				echo '</ol>';
			}
		);
	}
);
