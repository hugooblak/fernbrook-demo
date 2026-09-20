<?php
/**
 * Title: Page: Puppy Foundations
 * Slug: fernbrook/page-puppy-foundations
 * Categories: fernbrook-pages
 * Block Types: core/post-content
 * Post Types: page
 * Description: Program page for the six-week puppy course: who it is for, what each week covers, the practical facts, and the next dates.
 */
$img = get_theme_file_uri( 'assets/img/' );
$url = function ( $path ) { return esc_url( home_url( $path ) ); };
?>
<!-- wp:group {"align":"full","className":"fb-sec-hero","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"backgroundColor":"sand","layout":{"type":"constrained","contentSize":"1180px"}} -->
<div class="wp-block-group alignfull fb-sec-hero has-sand-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:columns {"verticalAlignment":"center","align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|60"}}}} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"56%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:56%"><!-- wp:paragraph {"className":"is-style-eyebrow"} -->
<p class="is-style-eyebrow">Puppy Foundations · 6 weeks · $320</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading">Puppy Foundations: the six weeks that decide the next ten years.</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"ink-soft","fontSize":"large"} -->
<p class="has-ink-soft-color has-text-color has-large-font-size">A six-week course for puppies between 8 and 18 weeks old. Six puppies, two instructors, one hour a week at the Harbour Lane hall in Alder Bay.</p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"is-style-checklist"} -->
<ul class="wp-block-list is-style-checklist"><!-- wp:list-item -->
<li>Start at <strong>8 weeks</strong>, after the first vaccination</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Toilet training, settling, handling, recall and calm greetings</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>A written one-page plan after every class</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>$320</strong> for the whole course, nothing added later</li>
<!-- /wp:list-item -->

</ul>
<!-- /wp:list -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"fb-track-cta"} -->
<div class="wp-block-button fb-track-cta"><a class="wp-block-button__link wp-element-button" href="<?php echo $url( '/enquire/' ); ?>">Ask about a place</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo $url( '/classes/' ); ?>">See class dates</a></div>
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
<h2 class="wp-block-heading">What each week covers</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"ink-soft"} -->
<p class="has-ink-soft-color has-text-color">The order matters. Early weeks build the habits that make the later ones possible.</p>
<!-- /wp:paragraph -->

<!-- wp:group {"className":"is-style-card","style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group is-style-card" style="margin-top:var(--wp--preset--spacing--30)"><!-- wp:heading {"level":3,"fontSize":"large"} -->
<h3 class="wp-block-heading has-large-font-size">Week 1: Settling and name</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"ink-soft","fontSize":"small"} -->
<p class="has-ink-soft-color has-text-color has-small-font-size">Getting a puppy to lie down in a busy room without being told twice. Marking the name, and the first food-hand work.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"is-style-card","style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group is-style-card" style="margin-top:var(--wp--preset--spacing--30)"><!-- wp:heading {"level":3,"fontSize":"large"} -->
<h3 class="wp-block-heading has-large-font-size">Week 2: Handling and the vet</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"ink-soft","fontSize":"small"} -->
<p class="has-ink-soft-color has-text-color has-small-font-size">Ears, paws, collar and harness, taught as a game so the vet is not the first person to do it.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"is-style-card","style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group is-style-card" style="margin-top:var(--wp--preset--spacing--30)"><!-- wp:heading {"level":3,"fontSize":"large"} -->
<h3 class="wp-block-heading has-large-font-size">Week 3: Toilet training and alone time</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"ink-soft","fontSize":"small"} -->
<p class="has-ink-soft-color has-text-color has-small-font-size">Reading the signs, a schedule that fits your day, and the first two minutes of being left alone.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"is-style-card","style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group is-style-card" style="margin-top:var(--wp--preset--spacing--30)"><!-- wp:heading {"level":3,"fontSize":"large"} -->
<h3 class="wp-block-heading has-large-font-size">Week 4: Recall, indoors then out</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"ink-soft","fontSize":"small"} -->
<p class="has-ink-soft-color has-text-color has-small-font-size">One word, high value food, short distances. We add the garden and then the hall doorway.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"is-style-card","style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group is-style-card" style="margin-top:var(--wp--preset--spacing--30)"><!-- wp:heading {"level":3,"fontSize":"large"} -->
<h3 class="wp-block-heading has-large-font-size">Week 5: Loose lead walking</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"ink-soft","fontSize":"small"} -->
<p class="has-ink-soft-color has-text-color has-small-font-size">Rewarding the position you want instead of pulling back. Turns, stops, and what to do when a dog passes.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"is-style-card","style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group is-style-card" style="margin-top:var(--wp--preset--spacing--30)"><!-- wp:heading {"level":3,"fontSize":"large"} -->
<h3 class="wp-block-heading has-large-font-size">Week 6: Greetings and the world</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"ink-soft","fontSize":"small"} -->
<p class="has-ink-soft-color has-text-color has-small-font-size">Four feet on the floor when someone says hello. Traffic, bicycles, umbrellas and hats, at a distance the puppy can handle.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

</div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"fb-sec-facts is-style-section-sand","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained","contentSize":"760px"}} -->
<div class="wp-block-group alignfull fb-sec-facts is-style-section-sand" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:heading -->
<h2 class="wp-block-heading">The practical facts</h2>
<!-- /wp:heading -->

<!-- wp:table {"hasFixedLayout":false,"className":"fb-price-table"} -->
<figure class="wp-block-table fb-price-table"><table><tbody><tr><td>Ages</td><td>8 to 18 weeks at the first class</td></tr><tr><td>Length</td><td>6 classes, one hour each, same night every week</td></tr><tr><td>Class size</td><td>6 puppies, 2 instructors</td></tr><tr><td>Where</td><td>Harbour Lane hall, Alder Bay</td></tr><tr><td>Price</td><td>$320 for the course</td></tr><tr><td>Bring</td><td>A flat collar or harness, a 2 m lead, and small soft treats</td></tr><tr><td>Vaccination</td><td>First vaccination done, second not required</td></tr></tbody></table><figcaption class="wp-element-caption">Sample details for this demo site.</figcaption></figure>
<!-- /wp:table --></div>
<!-- /wp:group -->

<!-- wp:pattern {"slug":"fernbrook/class-dates"} /-->

<!-- wp:pattern {"slug":"fernbrook/answers"} /-->

<!-- wp:pattern {"slug":"fernbrook/final-cta"} /-->
