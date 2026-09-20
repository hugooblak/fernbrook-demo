<?php
/**
 * Title: Page: Private training
 * Slug: fernbrook/page-private-training
 * Categories: fernbrook-pages
 * Block Types: core/post-content
 * Post Types: page
 * Description: Program page for one-to-one sessions: what they are for, how a session runs, what it costs, and what we will not promise.
 */
$img = get_theme_file_uri( 'assets/img/' );
$url = function ( $path ) { return esc_url( home_url( $path ) ); };
?>
<!-- wp:group {"align":"full","className":"fb-sec-hero","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"backgroundColor":"sand","layout":{"type":"constrained","contentSize":"1180px"}} -->
<div class="wp-block-group alignfull fb-sec-hero has-sand-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:columns {"verticalAlignment":"center","align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|60"}}}} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"56%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:56%"><!-- wp:paragraph {"className":"is-style-eyebrow"} -->
<p class="is-style-eyebrow">Private training · $145 an hour</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading">One to one, where the problem actually happens.</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"ink-soft","fontSize":"large"} -->
<p class="has-ink-soft-color has-text-color has-large-font-size">Some things cannot be fixed in a hall with five other dogs. We come to your home, your street, or a quiet trail, and work at the distance your dog can cope with.</p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"is-style-checklist"} -->
<ul class="wp-block-list is-style-checklist"><!-- wp:list-item -->
<li>Barking and lunging at dogs or people on the lead</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Being left alone, and the first hour of it</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Guarding food, toys or the sofa</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>A rescue dog in its first month with you</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"fb-track-cta"} -->
<div class="wp-block-button fb-track-cta"><a class="wp-block-button__link wp-element-button" href="<?php echo $url( '/enquire/' ); ?>">Ask about a session</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"44%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:44%"><!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"fb-hero-art"} -->
<figure class="wp-block-image size-full fb-hero-art"><img src="<?php echo esc_url( $img . 'trail-walk.svg' ); ?>" alt="A handler walking a dog on a loose lead along a coastal trail"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"fb-sec-weeks","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained","contentSize":"760px"}} -->
<div class="wp-block-group alignfull fb-sec-weeks" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:heading -->
<h2 class="wp-block-heading">How a session runs</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"ink-soft"} -->
<p class="has-ink-soft-color has-text-color">A first session is 90 minutes. The ones after it are an hour.</p>
<!-- /wp:paragraph -->

<!-- wp:list {"ordered":true} -->
<ol class="wp-block-list"><!-- wp:list-item -->
<li><strong>Twenty minutes talking.</strong> What happens, when, how often, and what you have already tried. The dog is usually asleep for this part.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>We watch it happen.</strong> At a safe distance, with the dog under threshold. We are reading body language, not testing your dog.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>You practise, we coach.</strong> You hold the lead. Coaching you is the whole point, because you are the one who is there on Tuesday.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>A written plan before we leave.</strong> Three things to do this week, and one thing to stop doing.</li>
<!-- /wp:list-item --></ol>
<!-- /wp:list --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"fb-sec-facts is-style-section-sand","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained","contentSize":"760px"}} -->
<div class="wp-block-group alignfull fb-sec-facts is-style-section-sand" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:heading -->
<h2 class="wp-block-heading">The practical facts</h2>
<!-- /wp:heading -->

<!-- wp:table {"hasFixedLayout":false,"className":"fb-price-table"} -->
<figure class="wp-block-table fb-price-table"><table><tbody><tr><td>First session</td><td>90 minutes, $195</td></tr><tr><td>Later sessions</td><td>1 hour, $145</td></tr><tr><td>Trail session</td><td>1 hour, $165</td></tr><tr><td>Where</td><td>Your home, your street, or a quiet trail</td></tr><tr><td>Travel</td><td>Alder Bay, Harrowfield, Quarry Hill, Selkirk Ferry and Ravensmoor, no travel charge</td></tr><tr><td>How many</td><td>Most owners book three to five over six to eight weeks</td></tr></tbody></table><figcaption class="wp-element-caption">Sample prices for this demo site.</figcaption></figure>
<!-- /wp:table --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"fb-sec-limits","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained","contentSize":"760px"}} -->
<div class="wp-block-group alignfull fb-sec-limits" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:heading -->
<h2 class="wp-block-heading">What we will not promise</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Nobody can promise a fixed number of sessions for behaviour that has been practised for years. What we can promise is a plan you understand, and an honest answer at every session about whether it is working.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>If your dog has bitten a person, or the problem looks like pain, we will say so and ask you to see your vet or a veterinary behaviourist first. We would rather lose the booking than take your money for the wrong thing.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:pattern {"slug":"fernbrook/answers"} /-->

<!-- wp:pattern {"slug":"fernbrook/final-cta"} /-->
