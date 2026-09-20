<?php
/**
 * Title: Price guide
 * Slug: fernbrook/price-guide
 * Categories: fernbrook-sections
 * Description: What each program costs, what is included, and what is not.
 */
$url = function ( $path ) { return esc_url( home_url( $path ) ); };
?>
<!-- wp:group {"align":"full","anchor":"pricing","className":"fb-sec-price","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained","contentSize":"1180px"}} -->
<div id="pricing" class="wp-block-group alignfull fb-sec-price" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|60"}}}} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"width":"40%"} -->
<div class="wp-block-column" style="flex-basis:40%"><!-- wp:paragraph {"className":"is-style-eyebrow"} -->
<p class="is-style-eyebrow">Prices</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">What it costs, in full</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"ink-soft"} -->
<p class="has-ink-soft-color has-text-color">One price per course. Nothing is added later, and we do not sell packages of ten sessions you may not need.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Included in every course</h3>
<!-- /wp:heading -->

<!-- wp:list {"className":"is-style-checklist","fontSize":"small"} -->
<ul class="wp-block-list is-style-checklist has-small-font-size"><!-- wp:list-item -->
<li>Six classes, one hour each</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>A one-page written plan after every class</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Email questions between classes</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>A free catch-up week on the next course</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"60%"} -->
<div class="wp-block-column" style="flex-basis:60%"><!-- wp:group {"className":"is-style-card","layout":{"type":"default"}} -->
<div class="wp-block-group is-style-card"><!-- wp:table {"hasFixedLayout":false,"className":"fb-price-table"} -->
<figure class="wp-block-table fb-price-table"><table><thead><tr><th>Program</th><th>Who it suits</th><th>Price</th></tr></thead><tbody><tr><td>Puppy Foundations</td><td>8 to 18 weeks</td><td>$320</td></tr><tr><td>Adolescent Manners</td><td>6 to 18 months</td><td>$340</td></tr><tr><td>Private training</td><td>Any age, any problem</td><td>$145 / hour</td></tr><tr><td>Trail session</td><td>Dogs that struggle outdoors</td><td>$165 / hour</td></tr></tbody></table><figcaption class="wp-element-caption">Sample prices for this demo site. Payment is taken when a place is confirmed.</figcaption></figure>
<!-- /wp:table -->

<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size"><strong>Not included:</strong> the treats you bring, and a well-fitted harness if your dog does not have one. We will tell you what to buy and never sell it to you.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"fb-track-cta"} -->
<div class="wp-block-button fb-track-cta"><a class="wp-block-button__link wp-element-button" href="<?php echo $url( '/enquire/' ); ?>">Ask about a place</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
