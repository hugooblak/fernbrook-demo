<?php
/**
 * Demo site setup for Fernbrook Dog Academy.
 *
 * Builds the whole site from the theme's patterns: pages, guides, class dates, the menu,
 * the AI-search settings for every page, a few redirects and three sample enquiries.
 * Safe to run again: it skips anything that already exists.
 *
 * Playground runs this automatically (see blueprint.json).
 * Local run:  wp eval-file content/setup.php
 *
 * @package Fernbrook Dog Academy
 */

defined( 'ABSPATH' ) || exit;

wp_set_current_user( 1 ); // Admin, so block markup is saved exactly as written.

/**
 * Pattern markup with nested pattern references expanded, so saved pages hold plain editable blocks.
 *
 * @param string $slug Pattern slug.
 * @return string
 */
function fbs_pattern( $slug ) {
	$pattern = WP_Block_Patterns_Registry::get_instance()->get_registered( $slug );
	if ( ! $pattern ) {
		fbs_log( "Missing pattern: $slug" );
		return '';
	}
	return preg_replace_callback(
		'#<!-- wp:pattern \{"slug":"([^"]+)"\} /-->#',
		function ( $m ) {
			return fbs_pattern( $m[1] );
		},
		$pattern['content']
	);
}

function fbs_log( $msg ) {
	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		WP_CLI::log( $msg );
	}
}

function fbs_page( $slug, $title, $content, $excerpt, $template = '', $meta = array() ) {
	$existing = get_page_by_path( $slug );
	if ( $existing ) {
		return $existing->ID;
	}
	$meta_input = $meta;
	if ( $template ) {
		$meta_input['_wp_page_template'] = $template;
	}
	$id = wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_name'    => $slug,
			'post_title'   => $title,
			'post_content' => $content,
			'post_excerpt' => $excerpt,
			'meta_input'   => $meta_input,
		)
	);
	fbs_log( "Page: $title" );
	return $id;
}

/**
 * Build a set of open/close questions as core Details blocks.
 * The AI Search plugin reads these same blocks for the FAQ structured data.
 *
 * @param array $faqs Pairs of question and answer.
 * @return string
 */
function fbs_details( $faqs ) {
	$out = '';
	foreach ( $faqs as $f ) {
		$out .= "\n\n<!-- wp:details -->\n<details class=\"wp-block-details\"><summary>" . esc_html( $f[0] ) . "</summary><!-- wp:paragraph -->\n<p>" . esc_html( $f[1] ) . "</p>\n<!-- /wp:paragraph --></details>\n<!-- /wp:details -->";
	}
	return $out;
}

function fbs_h2( $text ) {
	return "\n\n<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">" . esc_html( $text ) . "</h2>\n<!-- /wp:heading -->";
}

function fbs_p( $html, $attrs = '' ) {
	$json  = $attrs ? ' ' . $attrs : '';
	$class = '';
	if ( false !== strpos( $attrs, '"fontSize":"large"' ) ) {
		$class = ' class="has-large-font-size"';
	}
	return "\n\n<!-- wp:paragraph{$json} -->\n<p{$class}>" . $html . "</p>\n<!-- /wp:paragraph -->";
}

/* --------------------------------------------------------------------------
 * Site settings
 * ----------------------------------------------------------------------- */
update_option( 'blogname', 'Fernbrook Dog Academy' );
update_option( 'blogdescription', 'Reward-based puppy and dog training in Alder Bay, California.' );
update_option( 'timezone_string', 'America/Los_Angeles' );
update_option( 'date_format', 'F j, Y' );
update_option( 'posts_per_page', 9 );

foreach ( array( 'hello-world' => 'post', 'sample-page' => 'page' ) as $slug => $type ) {
	$sample = get_page_by_path( $slug, OBJECT, $type );
	if ( $sample ) {
		wp_delete_post( $sample->ID, true );
	}
}
$privacy_draft = get_page_by_path( 'privacy-policy' );
if ( $privacy_draft && 'draft' === $privacy_draft->post_status ) {
	wp_delete_post( $privacy_draft->ID, true );
}

/* --------------------------------------------------------------------------
 * Pages
 * ----------------------------------------------------------------------- */
$home = fbs_page(
	'home',
	'Puppy and dog training in Alder Bay',
	fbs_pattern( 'fernbrook/page-home' ),
	'Reward-based puppy classes, adolescent classes and private dog training in Alder Bay, California. Six dogs per class, two instructors, six-week courses from $320.',
	'page-landing',
	array(
		'_fbaeo_title'       => 'Puppy and dog training in Alder Bay, California',
		'_fbaeo_description' => 'Reward-based puppy classes, adolescent classes and one-to-one dog training in Alder Bay. Six dogs per class, two instructors, six-week courses from $320.',
		'_fbaeo_primary'     => '1',
	)
);

$puppy = fbs_page(
	'puppy-foundations',
	'Puppy Foundations',
	fbs_pattern( 'fernbrook/page-puppy-foundations' ),
	'A six-week reward-based course for puppies aged 8 to 18 weeks in Alder Bay. Toilet training, settling, handling and recall. $320, six puppies per class.',
	'page-landing',
	array(
		'_fbaeo_type'        => 'course',
		'_fbaeo_title'       => 'Puppy Foundations: six-week puppy classes in Alder Bay',
		'_fbaeo_description' => 'A six-week reward-based course for puppies aged 8 to 18 weeks in Alder Bay. Toilet training, settling, handling and recall. $320, six puppies per class.',
		'_fbaeo_price'       => '320',
		'_fbaeo_weeks'       => '6',
		'_fbaeo_audience'    => 'Puppies aged 8 to 18 weeks and their owners',
		'_fbaeo_primary'     => '1',
	)
);

$adolescent = fbs_page(
	'adolescent-manners',
	'Adolescent Manners',
	fbs_pattern( 'fernbrook/page-adolescent-manners' ),
	'A six-week course for dogs aged 6 to 18 months in Alder Bay: recall around other dogs, loose lead walking, and impulse control. $340 for six classes.',
	'page-landing',
	array(
		'_fbaeo_type'        => 'course',
		'_fbaeo_title'       => 'Adolescent Manners: dog classes for 6 to 18 months',
		'_fbaeo_description' => 'A six-week course for dogs aged 6 to 18 months in Alder Bay: recall around other dogs, loose lead walking, and impulse control. $340 for six classes.',
		'_fbaeo_price'       => '340',
		'_fbaeo_weeks'       => '6',
		'_fbaeo_audience'    => 'Dogs aged 6 to 18 months and their owners',
		'_fbaeo_primary'     => '1',
	)
);

$private = fbs_page(
	'private-training',
	'Private training',
	fbs_pattern( 'fernbrook/page-private-training' ),
	'One-to-one dog training at your home or on a quiet trail near Alder Bay: barking and lunging on the lead, being left alone, guarding. From $145 an hour.',
	'page-landing',
	array(
		'_fbaeo_type'        => 'service',
		'_fbaeo_title'       => 'Private dog training at home near Alder Bay',
		'_fbaeo_description' => 'One-to-one dog training at your home or on a quiet trail near Alder Bay: barking and lunging on the lead, being left alone, guarding. From $145 an hour.',
		'_fbaeo_price'       => '145',
		'_fbaeo_audience'    => 'Dogs of any age with a specific problem',
		'_fbaeo_primary'     => '1',
	)
);

$classes_content  = fbs_p( 'Courses run for six weeks, the same evening each week, in the Harbour Lane hall. A place is held for 48 hours once we offer it, and nothing is charged until you accept.', '{"fontSize":"large"}' );
$classes_content .= "\n\n<!-- wp:fernbrook/class-list {\"limit\":8} /-->";
$classes_content .= fbs_h2( 'What to bring' );
$classes_content .= "\n\n<!-- wp:list {\"className\":\"is-style-checklist\"} -->\n<ul class=\"wp-block-list is-style-checklist\"><!-- wp:list-item -->\n<li>A flat collar or a well-fitted harness, and a two-metre lead. No extending leads in the hall.</li>\n<!-- /wp:list-item -->\n\n<!-- wp:list-item -->\n<li>Small soft treats your dog will actually work for, in a pot or a pouch.</li>\n<!-- /wp:list-item -->\n\n<!-- wp:list-item -->\n<li>A mat or a towel for your dog to settle on.</li>\n<!-- /wp:list-item -->\n\n<!-- wp:list-item -->\n<li>Water. There is a bowl in the hall, but bring your own in summer.</li>\n<!-- /wp:list-item --></ul>\n<!-- /wp:list -->";
$classes_content .= fbs_h2( 'Getting there' );
$classes_content .= fbs_p( 'The hall is at 47 Harbour Lane, Alder Bay. There is parking behind the building and a quiet grass strip where dogs can settle before class. Arrive five minutes early and stay in the car until the previous class has left, so the doorway is never crowded.' );
$classes_content .= fbs_p( 'Not sure which course suits your dog? Read <a href="' . esc_url( home_url( '/puppy-foundations/' ) ) . '">Puppy Foundations</a> and <a href="' . esc_url( home_url( '/adolescent-manners/' ) ) . '">Adolescent Manners</a>, or ask us on the <a href="' . esc_url( home_url( '/contact/' ) ) . '">contact page</a>.' );
$classes_content .= fbs_pattern( 'fernbrook/cta-inline' );

fbs_page(
	'classes',
	'Class dates',
	$classes_content,
	'The next puppy and adolescent class start dates at the Harbour Lane hall in Alder Bay, what each course costs, and what to bring to the first class.',
	'',
	array(
		'_fbaeo_type'        => 'collection',
		'_fbaeo_title'       => 'Class dates in Alder Bay',
		'_fbaeo_description' => 'The next puppy and adolescent class start dates at the Harbour Lane hall in Alder Bay, what each course costs, and what to bring to the first class.',
		'_fbaeo_primary'     => '1',
	)
);

$about  = fbs_p( 'Fernbrook Dog Academy is two instructors and one hall. We run six-week group courses for puppies and adolescent dogs, and one-to-one sessions for the problems a class is too busy to fix.', '{"fontSize":"large"}' );
$about .= fbs_p( 'The school opened in 2016. Everything we teach is reward-based: we show the dog what to do and pay for it, rather than correcting what it did. Nothing we use causes pain or fear, and we will tell you plainly when a problem is outside what training can fix.' );
$about .= fbs_h2( 'Dana Whitlock, owner and head trainer' );
$about .= fbs_p( 'Dana started the school in 2016 after ten years of running classes in village halls. She teaches the adolescent course and all the private work, and she is the person who will tell you honestly that your dog needs a vet rather than a trainer. Certified dog trainer (sample credential for this demo site).' );
$about .= fbs_h2( 'Priya Raman, puppy class instructor' );
$about .= fbs_p( 'Priya runs the puppy courses. She was a veterinary nurse for six years before she trained dogs full time, which is why the handling week is the one people remember. Certified dog trainer (sample credential for this demo site).' );
$about .= fbs_h2( 'What we will not do' );
$about .= fbs_p( 'We do not use prong collars, choke chains or shock collars, and we will not train a dog that someone else has been using them on without talking to you about it first. We do not take dogs away from their owners to train them, because the person holding the lead on Tuesday is the one who needs the practice.' );
$about .= fbs_h2( 'How this site is built' );
$about .= fbs_p( 'This site is a demonstration of search and AI-assistant work. If you want to see how the structured data, the crawler policy and the validation are put together, that is written up on <a href="' . esc_url( home_url( '/ai-search-setup/' ) ) . '">how this site is set up for AI search</a>. For the training itself, start with <a href="' . esc_url( home_url( '/private-training/' ) ) . '">private training</a> or the <a href="' . esc_url( home_url( '/classes/' ) ) . '">class dates</a>.' );
$about .= fbs_pattern( 'fernbrook/cta-inline' );

fbs_page(
	'about',
	'About Fernbrook Dog Academy',
	$about,
	'Two instructors, one hall in Alder Bay, and reward-based methods only. Meet Dana Whitlock and Priya Raman, and read what we will not do.',
	'',
	array(
		'_fbaeo_type'        => 'about',
		'_fbaeo_title'       => 'About Fernbrook Dog Academy, Alder Bay',
		'_fbaeo_description' => 'Two instructors, one hall in Alder Bay, and reward-based methods only. Meet Dana Whitlock and Priya Raman, and read plainly what we will not do.',
	)
);

$faq_groups = array(
	'Before you book' => array(
		array( 'How old does my puppy need to be?', 'Eight weeks, with the first vaccination done. Waiting until a puppy is fully vaccinated at sixteen weeks means missing most of the window when new things are easy to accept. The hall is cleaned between classes and puppies stay off the floor until their turn.' ),
		array( 'How many dogs are in a class?', 'Six, with two instructors. That is the number where every handler still gets hands-on time in an hour.' ),
		array( 'My dog barks and lunges at other dogs. Can we come to a class?', 'Not to a group class first. Book a private session so we can work at a distance your dog can handle. Many dogs join a class a few months later.' ),
		array( 'Do you take rescue dogs that have just arrived?', 'Yes, and usually as a private session for the first month. A new dog in a new house has enough to take in without five strangers in a hall.' ),
	),
	'Money'           => array(
		array( 'What do the courses cost?', 'Puppy Foundations is $320 and Adolescent Manners is $340, both for six weekly classes. Private sessions are $145 an hour, or $195 for the first ninety-minute session. Trail sessions are $165 an hour.' ),
		array( 'When do I pay?', 'When you accept a place. Nothing is taken while we are still working out whether the course suits your dog.' ),
		array( 'Do you sell packages?', 'No. We would rather you booked the next session when you need it. Most private clients book three to five sessions over six to eight weeks, but that is a pattern, not a package.' ),
	),
	'The classes'     => array(
		array( 'What if we miss a week?', 'Sit in on the same week of the next course at no cost. Tell us as early as you can so we keep the numbers right.' ),
		array( 'Where do the classes happen?', 'The Harbour Lane hall in Alder Bay. Weeks five and six of the adolescent course are outdoors on the green next to it.' ),
		array( 'Can children come?', 'Yes, and they are welcome to hold the lead in the exercises that suit them. One adult stays responsible for the dog.' ),
		array( 'What do I bring?', 'A flat collar or harness, a two-metre lead, small soft treats, a mat for your dog to settle on, and water in summer.' ),
	),
	'Methods'         => array(
		array( 'What does reward-based actually mean?', 'We pay the dog for the behaviour we want, with food, play or access to something it wants, and we manage the situation so the behaviour we do not want is hard to practise. We do not use pain, fear or intimidation.' ),
		array( 'Do you guarantee results?', 'No, and be careful of anyone who does. Behaviour that has been practised for years does not come apart on a schedule. What we do promise is a written plan, and an honest answer at every session about whether it is working.' ),
	),
);
$faq_content = fbs_p( 'Straight answers to what owners in Alder Bay ask us most. If yours is not here, call <a href="tel:+15550100188">(555) 010-0188</a> or use the <a href="' . esc_url( home_url( '/contact/' ) ) . '">contact page</a>.', '{"fontSize":"large"}' );
foreach ( $faq_groups as $group => $faqs ) {
	$faq_content .= fbs_h2( $group );
	$faq_content .= fbs_details( $faqs );
}
$faq_content .= fbs_pattern( 'fernbrook/cta-inline' );

fbs_page(
	'faq',
	'Frequently asked questions',
	$faq_content,
	'Answers about puppy class ages, class sizes, prices, missed weeks, what to bring, and what reward-based training means in practice.',
	'',
	array(
		'_fbaeo_type'        => 'faq',
		'_fbaeo_title'       => 'Dog training questions, answered | Fernbrook Dog Academy',
		'_fbaeo_description' => 'Answers about puppy class ages, class sizes, prices, missed weeks, what to bring, and what reward-based dog training means in practice.',
		'_fbaeo_primary'     => '1',
	)
);

$contact  = fbs_p( 'An instructor reads every message and replies within one working day, Tuesday to Saturday.', '{"fontSize":"large"}' );
$contact .= fbs_h2( 'Where we are' );
$contact .= fbs_p( '<strong>Fernbrook Dog Academy</strong><br>47 Harbour Lane<br>Alder Bay, CA 94099' );
$contact .= fbs_p( 'Phone: <a href="tel:+15550100188">(555) 010-0188</a><br>Open Tuesday to Saturday, 8am to 6pm, and Sunday 9am to 2pm. Closed Mondays.' );
$contact .= fbs_p( 'Home visits and trail sessions cover Alder Bay, Harrowfield, Quarry Hill, Selkirk Ferry and Ravensmoor, with no travel charge.' );
$contact .= fbs_h2( 'Send us a message' );
$contact .= "\n\n<!-- wp:fernbrook/lead-form /-->";
$contact .= fbs_h2( 'If it is urgent' );
$contact .= fbs_p( 'If your dog has bitten someone, or you are frightened of it, call rather than write. If the problem started suddenly, see your vet first: pain changes behaviour faster than any trainer can.' );

fbs_page(
	'contact',
	'Contact Fernbrook Dog Academy',
	$contact,
	'Phone, address and opening hours for Fernbrook Dog Academy at 47 Harbour Lane, Alder Bay, plus a short form. An instructor replies within one working day.',
	'',
	array(
		'_fbaeo_type'        => 'contact',
		'_fbaeo_title'       => 'Contact Fernbrook Dog Academy, Alder Bay',
		'_fbaeo_description' => 'Phone, address and opening hours for Fernbrook Dog Academy at 47 Harbour Lane, Alder Bay, plus a short form. An instructor replies within one working day.',
		'_fbaeo_primary'     => '1',
	)
);

$enquire = fbs_page(
	'enquire',
	'Ask about a place',
	fbs_pattern( 'fernbrook/enquiry-funnel' ),
	'Four short questions about your dog. An instructor replies within one working day with the class that fits, or why a private session would suit better.',
	'page-focus',
	array(
		'_fbaeo_title'       => 'Ask about a place | Fernbrook Dog Academy',
		'_fbaeo_description' => 'Four short questions about your dog. An instructor replies within one working day with the class that fits, or why a private session would suit better.',
	)
);

$thanks = fbs_page(
	'thank-you',
	'Thank you',
	fbs_pattern( 'fernbrook/thank-you' ),
	'Your enquiry has been received. An instructor replies within one working day with the next class that fits, or an honest reason to book a private session.',
	'page-focus',
	array( '_fbaeo_noindex' => '1' )
);

$privacy_content  = fbs_p( '<strong>Sample policy for a demo site.</strong> A real policy has to be written for the business and checked by a lawyer.', '{"backgroundColor":"sand"}' );
$privacy_content .= fbs_h2( 'What we collect' );
$privacy_content .= fbs_p( 'When you send the enquiry form we collect your ZIP code, which program you are interested in, your dog\'s age, your first name, your phone number and your email address. We also record which page brought you to the site.' );
$privacy_content .= fbs_h2( 'How we use it' );
$privacy_content .= fbs_p( 'Only to answer your enquiry and arrange a place. We keep a record of the consent you gave when you sent the form. We never sell or share your details.' );
$privacy_content .= fbs_h2( 'Your choices' );
$privacy_content .= fbs_p( 'Reply STOP to any text, or email us to have your details deleted. This site uses WordPress\'s own privacy tools, so an export or an erasure request covers the enquiry records too.' );
$privacy = fbs_page( 'privacy-policy', 'Privacy policy', $privacy_content, 'What Fernbrook Dog Academy does with the details you send on the enquiry form, and how to have them deleted.', '', array( '_fbaeo_noindex' => '1' ) );
update_option( 'wp_page_for_privacy_policy', $privacy );

$doc  = fbs_p( 'This page is the short written record of how this site is set up to be read by search engines and by AI assistants. It is on the site rather than in a document because the people who maintain the site are the people who need it.', '{"fontSize":"large"}' );
$doc .= fbs_h2( 'One graph per page, not five loose blocks' );
$doc .= fbs_p( 'Every page prints a single JSON-LD script holding a <code>@graph</code>. Inside it, each thing is a node with its own <code>@id</code>, and the nodes point at each other: the website is published by the organization, the local business is part of the organization, a course is provided by the organization and runs at a place, a guide is written by a person who works for the business.' );
$doc .= fbs_p( 'The point of the <code>@id</code> values is that they are stable. Business entities hang off the site root, so renaming a page never breaks them. Page entities hang off the page address.' );
$doc .= "\n\n<!-- wp:table {\"hasFixedLayout\":false,\"className\":\"fb-price-table\"} -->\n<figure class=\"wp-block-table fb-price-table\"><table><thead><tr><th>Stable @id</th><th>What it is</th></tr></thead><tbody><tr><td><code>/#organization</code></td><td>The company. Publisher of everything on the site.</td></tr><tr><td><code>/#localbusiness</code></td><td>The place people visit: address, hours, phone, areas served.</td></tr><tr><td><code>/#website</code></td><td>The website itself.</td></tr><tr><td><code>/#person-dana-whitlock</code></td><td>A trainer. The same node is the author of every guide she writes.</td></tr><tr><td><code>/#service-puppy-foundations</code></td><td>The service the business sells.</td></tr><tr><td><code>/#course-puppy-foundations</code></td><td>The taught course, with its dates as CourseInstances.</td></tr><tr><td><code>/#place-venue</code></td><td>The hall. Every class Event points at this one Place.</td></tr><tr><td><code>/about/#webpage</code></td><td>A page. Page-level nodes hang off the page address.</td></tr></tbody></table><figcaption class=\"wp-element-caption\">The full list is on the AI Search screen in the admin.</figcaption></figure>\n<!-- /wp:table -->";
$doc .= fbs_h2( 'Which types are used where' );
$doc .= fbs_p( 'Organization, LocalBusiness and WebSite on every page. Service and Course on the program pages, with the class dates as CourseInstance. EducationEvent for each class start, wherever the class list is shown. Article on every guide. FAQPage wherever there are questions on the page. Person for each trainer. ContactPage, AboutPage and CollectionPage where they fit.' );
$doc .= fbs_p( 'schema.org has no type for a dog trainer, so the business is a <code>LocalBusiness</code> with an <code>additionalType</code> pointing at a public page that does identify the trade. Inventing a type that nothing recognises would look specific and mean nothing.' );
$doc .= fbs_h2( 'The FAQ data is read off the page' );
$doc .= fbs_p( 'Questions and answers are not typed into a separate field. The plugin reads the page\'s own open-and-close blocks and builds the FAQ data from them. Structured data that says something a visitor cannot see is exactly what gets a site penalised, and keeping one source makes it impossible.' );
$doc .= fbs_h2( 'What is deliberately missing' );
$doc .= fbs_p( 'There is no rating and no review data anywhere on this site, because this is a demonstration site with no real reviews to publish. One of the twelve checks fails on purpose if any ever appears without real reviews behind it.' );
$doc .= fbs_p( 'There is no VideoObject either. The fields are built in and switch on the moment all four are filled, but an empty or invented one is worse than none.' );
$doc .= fbs_h2( 'Crawlers, sitemap and llms.txt' );
$doc .= fbs_p( 'The <a href="' . esc_url( home_url( '/robots.txt' ) ) . '">robots.txt</a> file has one rule per AI crawler, set in the admin, with a line explaining what each crawler does. The default allows the ones that can send a visitor or cite the site, and blocks the ones that only collect. <a href="' . esc_url( home_url( '/wp-sitemap.xml' ) ) . '">The sitemap</a> is WordPress\'s own, left switched on and linked from robots.txt. <a href="' . esc_url( home_url( '/llms.txt' ) ) . '">llms.txt</a> is generated from the pages themselves, so it cannot go stale.' );
$doc .= fbs_h2( 'Canonicals, redirects and indexing' );
$doc .= fbs_p( 'Every page states its own canonical address. Pages that should not compete in search, like the thank-you page, are marked noindex in the same panel. Old addresses are kept working by the redirect table, which also checks itself for chains and loops — a redirect that goes through another redirect throws away most of what the first one was for.' );
$doc .= fbs_h2( 'How it is checked' );
$doc .= fbs_p( 'Twelve checks run inside WordPress on every page: one H1, heading levels in order, title and description lengths, canonical, indexing, valid JSON-LD, every node addressable, no duplicate <code>@id</code>, every reference resolving, FAQ answers present on the page, and no invented ratings. Every page also links straight to the Schema.org validator and Google\'s Rich Results Test with its own address filled in. Search Console needs its own days of data before it can show anything, and no plugin can shortcut that.' );
$doc .= fbs_h2( 'Where the work lives' );
$doc .= fbs_p( 'All of it is in one plugin, not in the theme. The theme can be replaced, or swapped for a page builder, and none of this is lost.' );

fbs_page(
	'ai-search-setup',
	'How this site is set up for AI search',
	$doc,
	'How this site is set up for search engines and AI assistants: one entity graph per page, stable @id values, the crawler policy, llms.txt and twelve checks.',
	'',
	array(
		'_fbaeo_title'       => 'How this site is set up for AI search',
		'_fbaeo_description' => 'The written record: one entity graph per page, stable @id values, the crawler policy, llms.txt, canonicals, redirects and the twelve checks that run first.',
	)
);

$guides = fbs_page(
	'guides',
	'Owner guides',
	'',
	'Plain answers to the questions dog owners in Alder Bay ask us most: puppy socialising, lead reactivity on busy trails, and how to choose a class.',
	'',
	array(
		'_fbaeo_type'        => 'collection',
		'_fbaeo_title'       => 'Owner guides | Fernbrook Dog Academy',
		'_fbaeo_description' => 'Plain answers to the questions dog owners in Alder Bay ask us most: puppy socialising, lead reactivity on busy trails, and how to choose a puppy class.',
	)
);

update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $home );
update_option( 'page_for_posts', $guides );
update_option( 'fbl_enquiry_page', $enquire );
update_option( 'fbl_thank_you_page', $thanks );

/* --------------------------------------------------------------------------
 * Owner guides (the local, educational pages)
 * ----------------------------------------------------------------------- */
function fbs_post( $slug, $title, $content, $excerpt, $days_ago, $meta = array() ) {
	$existing = get_page_by_path( $slug, OBJECT, 'post' );
	if ( $existing ) {
		return $existing->ID;
	}
	$id = wp_insert_post(
		array(
			'post_type'    => 'post',
			'post_status'  => 'publish',
			'post_name'    => $slug,
			'post_title'   => $title,
			'post_content' => $content,
			'post_excerpt' => $excerpt,
			'post_date'    => wp_date( 'Y-m-d H:i:s', strtotime( '-' . (int) $days_ago . ' days' ) ),
			'meta_input'   => array_merge( array( '_fbaeo_type' => 'article' ), $meta ),
		)
	);
	fbs_log( "Guide: $title" );
	return $id;
}

$g1  = fbs_p( 'Socialising a puppy does not mean letting it meet every dog in Alder Bay. It means showing it the world at a distance it can cope with, often enough that none of it stays interesting.', '{"fontSize":"large"}' );
$g1 .= fbs_h2( 'Start before the second vaccination' );
$g1 .= fbs_p( 'The window when a puppy accepts new things easily closes at around sixteen weeks. Waiting until then costs more than the small risk of being out early. Carry the puppy, or sit with it in the car with the window down, and let it watch.' );
$g1 .= fbs_h2( 'Where to sit and watch in Alder Bay' );
$g1 .= fbs_p( 'The bench at the top of Harbour Lane, twenty metres back from the path, is enough for the first week: bicycles, buggies, and people in hats go past without coming close. The ferry queue at Selkirk Ferry on a Saturday gives you a crowd that has somewhere else to be. The Quarry Hill car park is loud, flat and dull, which is exactly what you want on the day you are testing traffic noise.' );
$g1 .= fbs_h2( 'The rule that matters more than the list' );
$g1 .= fbs_p( 'Watch the puppy, not the checklist. A puppy that is eating, sniffing and moving loosely is learning. A puppy that has gone still, stopped taking food, or is trying to get behind your legs is not — it is coping. Move further away and try again tomorrow.' );
$g1 .= fbs_h2( 'How many dogs should my puppy meet?' );
$g1 .= fbs_p( 'Fewer than most people think. Three or four calm adult dogs who are good with puppies, several times each, beats twenty strangers once. Puppies that greet every dog they see usually grow into adolescents that drag you across the road to do it.' );
$g1 .= fbs_details(
	array(
		array( 'Can I take my puppy out before the second vaccination?', 'Carry it, or sit with it somewhere it cannot lick the ground. That gets you the sights and the sounds with almost none of the risk. Ask your vet about the ground where you live.' ),
		array( 'What if my puppy is frightened of something?', 'Move further away until it can eat and sniff again, and stay there. Forcing a puppy closer to something it is worried about is how a small worry becomes a lasting one.' ),
	)
);
$g1 .= fbs_p( 'Week one of <a href="' . esc_url( home_url( '/puppy-foundations/' ) ) . '">Puppy Foundations</a> is mostly this, done with an instructor watching. The <a href="' . esc_url( home_url( '/classes/' ) ) . '">class dates</a> are usually two or three weeks out.' );

fbs_post(
	'puppy-socialising-in-alder-bay',
	'Puppy socialising in Alder Bay: where to start',
	$g1,
	'Socialising a puppy is not meeting every dog in town. Where to sit and watch in Alder Bay, how to read whether it is working, and why fewer is better.',
	9,
	array(
		'_fbaeo_author'      => 'priya-raman',
		'_fbaeo_title'       => 'Puppy socialising in Alder Bay: where to start',
		'_fbaeo_description' => 'Socialising a puppy is not meeting every dog in town. Where to sit and watch in Alder Bay, how to tell whether it is working, and why fewer is better.',
	)
);

$g2  = fbs_p( 'A dog that barks and lunges at other dogs on the lead is not being difficult. It is usually frightened, and it has learned that making a noise works, because the other dog always goes away in the end.', '{"fontSize":"large"}' );
$g2 .= fbs_h2( 'Distance is the whole game' );
$g2 .= fbs_p( 'Every dog has a distance at which it can still eat, still hear you, and still think. Closer than that and none of those are available. Training happens at that distance and nowhere else, which is why a busy trail on a Sunday is the worst possible classroom.' );
$g2 .= fbs_h2( 'Picking the right trail, and the right hour' );
$g2 .= fbs_p( 'The Ravensmoor path is wide and straight, so you can see a dog coming for a hundred metres and step off. The Quarry Hill loop is narrow with blind corners, which is why it goes badly. On the Harrowfield trail, early on a weekday is empty and mid-morning at the weekend is not. Choosing the hour is doing most of the work for you.' );
$g2 .= fbs_h2( 'What to do when a dog appears' );
$g2 .= fbs_p( 'Turn and walk the other way before your dog reacts, feeding as you go. That is not avoiding the problem, it is paying your dog for the one behaviour you actually want when a dog appears: looking at you. Once that is reliable at forty metres, thirty becomes possible.' );
$g2 .= fbs_h2( 'What not to do' );
$g2 .= fbs_p( 'Do not tighten the lead when you see a dog. Do not let a stranger walk their dog up to yours "so they can say hello". Do not let anyone talk you into a prong or shock collar: stopping the barking by making it hurt leaves the fear in place and takes away the warning.' );
$g2 .= fbs_p( 'This is the work we do in a <a href="' . esc_url( home_url( '/private-training/' ) ) . '">private session</a>, usually on a trail rather than at home. Dogs that get there often join <a href="' . esc_url( home_url( '/adolescent-manners/' ) ) . '">Adolescent Manners</a> later.' );

fbs_post(
	'leash-reactivity-on-busy-trails',
	'Lead reactivity on busy trails',
	$g2,
	'Why a dog barks and lunges at other dogs on the lead, how far away you need to be, and which trails near Alder Bay are worth using while you work on it.',
	21,
	array(
		'_fbaeo_author'      => 'dana-whitlock',
		'_fbaeo_title'       => 'Lead reactivity on busy trails near Alder Bay',
		'_fbaeo_description' => 'Why a dog barks and lunges at other dogs on the lead, how far away you need to be, and which trails near Alder Bay are worth using while you work on it.',
	)
);

$g3  = fbs_p( 'Most puppy classes look the same from the outside. Four questions separate the ones worth paying for.', '{"fontSize":"large"}' );
$g3 .= fbs_h2( 'How many puppies, and how many instructors?' );
$g3 .= fbs_p( 'Eight puppies and one instructor means you get about six minutes of attention in an hour. Ask for both numbers before you book. Six and two is a reasonable ratio for a puppy class.' );
$g3 .= fbs_h2( 'How much of the hour is off-lead play?' );
$g3 .= fbs_p( 'Some play is useful. Twenty minutes of it is a trainer filling the time, and for a nervous puppy it is a bad twenty minutes that can take months to undo. Ask what the hour actually contains.' );
$g3 .= fbs_h2( 'What happens when a puppy gets something wrong?' );
$g3 .= fbs_p( 'This is the question that tells you the most. You want an answer about making it easier, moving further away, or paying better. If the answer involves a noise, a spray, a tug or a "correction", keep looking.' );
$g3 .= fbs_h2( 'What do you get to take home?' );
$g3 .= fbs_p( 'An hour a week is not what trains a dog; the other six days are. A class that sends you home with nothing written down is asking you to remember a lesson you were only half watching, because you were holding a puppy.' );
$g3 .= fbs_p( 'For what it is worth, our answers are six puppies, two instructors, about five minutes of structured play, "make it easier", and a one-page plan every week. The details are on the <a href="' . esc_url( home_url( '/puppy-foundations/' ) ) . '">Puppy Foundations</a> page, and the <a href="' . esc_url( home_url( '/faq/' ) ) . '">FAQ</a> covers the rest.' );

fbs_post(
	'choosing-a-puppy-class',
	'How to choose a puppy class',
	$g3,
	'Four questions to ask before you book a puppy class: the numbers, how much of the hour is play, what happens when a puppy gets it wrong, and what you take home.',
	34,
	array(
		'_fbaeo_author'      => 'priya-raman',
		'_fbaeo_title'       => 'How to choose a puppy class: four questions to ask',
		'_fbaeo_description' => 'Four questions to ask before you book a puppy class: the numbers, how much of the hour is play, what happens when a puppy gets it wrong, and what you take home.',
	)
);

/* --------------------------------------------------------------------------
 * Class start dates. Worked out from today, so the demo never shows stale dates.
 * ----------------------------------------------------------------------- */
if ( post_type_exists( 'fb_class' ) && ! get_posts( array( 'post_type' => 'fb_class', 'numberposts' => 1, 'post_status' => 'any' ) ) ) {
	$next_tuesday = strtotime( 'next tuesday', strtotime( '+6 days' ) );
	$next_thursday = strtotime( 'next thursday', strtotime( '+6 days' ) );
	$sessions = array(
		array( 'Puppy Foundations, Tuesday evenings', $puppy, $next_tuesday, '18:30', 6, '$320', '2' ),
		array( 'Adolescent Manners, Thursday evenings', $adolescent, $next_thursday, '19:00', 6, '$340', '4' ),
		array( 'Puppy Foundations, Saturday mornings', $puppy, strtotime( '+13 days', $next_tuesday ), '10:00', 6, '$320', '5' ),
		array( 'Puppy Foundations, Tuesday evenings', $puppy, strtotime( '+42 days', $next_tuesday ), '18:30', 6, '$320', '6' ),
		array( 'Adolescent Manners, Thursday evenings', $adolescent, strtotime( '+42 days', $next_thursday ), '19:00', 6, '$340', '6' ),
		array( 'Puppy Foundations, Saturday mornings', $puppy, strtotime( '+55 days', $next_tuesday ), '10:00', 6, '$320', '0' ),
	);
	foreach ( $sessions as $s ) {
		wp_insert_post(
			array(
				'post_type'   => 'fb_class',
				'post_status' => 'publish',
				'post_title'  => $s[0],
				'meta_input'  => array(
					'_fbc_program' => (int) $s[1],
					'_fbc_start'   => wp_date( 'Y-m-d', $s[2] ),
					'_fbc_time'    => $s[3],
					'_fbc_weeks'   => (string) $s[4],
					'_fbc_price'   => $s[5],
					'_fbc_spots'   => $s[6],
				),
			)
		);
	}
	fbs_log( 'Class dates added.' );
}

/* --------------------------------------------------------------------------
 * Redirects from the addresses the old site used, so nothing that is already
 * linked from elsewhere lands on a 404.
 * ----------------------------------------------------------------------- */
if ( function_exists( 'fbaeo_save_redirects' ) && ! get_option( 'fbaeo_redirects' ) ) {
	fbaeo_save_redirects(
		array(
			array( 'from' => '/puppy-class/', 'to' => '/puppy-foundations/', 'code' => 301, 'hits' => 0 ),
			array( 'from' => '/obedience/', 'to' => '/adolescent-manners/', 'code' => 301, 'hits' => 0 ),
			array( 'from' => '/one-to-one/', 'to' => '/private-training/', 'code' => 301, 'hits' => 0 ),
			array( 'from' => '/blog/', 'to' => '/guides/', 'code' => 301, 'hits' => 0 ),
			array( 'from' => '/prices/', 'to' => '/classes/', 'code' => 301, 'hits' => 0 ),
		)
	);
	fbs_log( 'Redirects added.' );
}

/* --------------------------------------------------------------------------
 * Main menu (Appearance → Editor → Navigation)
 * ----------------------------------------------------------------------- */
if ( ! get_posts( array( 'post_type' => 'wp_navigation', 'title' => 'Main menu', 'numberposts' => 1, 'post_status' => 'publish' ) ) ) {
	$links = array(
		array( 'Puppy Foundations', '/puppy-foundations/', 'page' ),
		array( 'Adolescent Manners', '/adolescent-manners/', 'page' ),
		array( 'Private training', '/private-training/', 'page' ),
		array( 'Class dates', '/classes/', 'page' ),
		array( 'Guides', '/guides/', 'page' ),
		array( 'FAQ', '/faq/', 'page' ),
	);
	$nav = '';
	foreach ( $links as $l ) {
		$attrs = array(
			'label' => $l[0],
			'url'   => home_url( $l[1] ),
			'kind'  => 'page' === $l[2] ? 'post-type' : 'custom',
		);
		if ( 'page' === $l[2] ) {
			$page          = get_page_by_path( trim( $l[1], '/' ) );
			$attrs['id']   = $page ? $page->ID : 0;
			$attrs['type'] = 'page';
		}
		$nav .= '<!-- wp:navigation-link ' . wp_json_encode( $attrs, JSON_UNESCAPED_SLASHES ) . ' /-->';
	}
	wp_insert_post(
		array(
			'post_type'    => 'wp_navigation',
			'post_status'  => 'publish',
			'post_title'   => 'Main menu',
			'post_content' => $nav,
		)
	);
	fbs_log( 'Menu created.' );
}

/* --------------------------------------------------------------------------
 * Three sample enquiries, so the inbox shows what real ones look like.
 * ----------------------------------------------------------------------- */
if ( post_type_exists( 'fbl_lead' ) && ! get_posts( array( 'post_type' => 'fbl_lead', 'numberposts' => 1 ) ) ) {
	$samples = array(
		array( 'Sample enquiry A', '(555) 010-0171', 'sample-a@example.com', '94099', 'puppy', 'puppy', 'contacted', array( 'utm_source' => 'google', 'utm_medium' => 'organic', 'landing_page' => home_url( '/puppy-foundations/' ) ), '-2 days' ),
		array( 'Sample enquiry B', '(555) 010-0183', 'sample-b@example.com', '94097', 'private', 'teen', 'new', array( 'utm_source' => 'chatgpt.com', 'utm_medium' => 'referral', 'landing_page' => home_url( '/leash-reactivity-on-busy-trails/' ) ), '-5 hours' ),
		array( 'Sample enquiry C', '(555) 010-0195', 'sample-c@example.com', '94098', 'not-sure', 'young', 'new', array( 'referrer' => 'https://www.perplexity.ai/' ), '-40 minutes' ),
	);
	foreach ( $samples as $s ) {
		wp_insert_post(
			array(
				'post_type'   => 'fbl_lead',
				'post_status' => 'publish',
				'post_title'  => $s[0] . ' · ' . fbl_label( 'program', $s[4] ),
				'post_date'   => wp_date( 'Y-m-d H:i:s', strtotime( $s[8] ) ),
				'meta_input'  => array_merge(
					array(
						'name'         => $s[0],
						'phone'        => $s[1],
						'email'        => $s[2],
						'zip'          => $s[3],
						'program'      => $s[4],
						'dog_age'      => $s[5],
						'status'       => $s[6],
						'landing_page' => home_url( '/' ),
						'consent_at'   => wp_date( 'Y-m-d H:i:s', strtotime( $s[8] ) ),
						'consent_text' => fbl_consent_text(),
					),
					$s[7]
				),
			)
		);
	}
	fbs_log( 'Sample enquiries added.' );
}

// Pretty links (/classes/ instead of ?page_id=5). Flushed last, after every page exists,
// and after the llms.txt rule has been registered.
global $wp_rewrite;
$wp_rewrite->set_permalink_structure( '/%postname%/' );
flush_rewrite_rules( false );

fbs_log( 'Fernbrook Dog Academy demo ready.' );
