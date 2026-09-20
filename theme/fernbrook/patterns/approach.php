<?php
/**
 * Title: Our approach
 * Slug: fernbrook/approach
 * Categories: fernbrook-sections
 * Description: Dark section that answers the two questions owners ask before booking: what methods, and what if it does not work.
 */
$url = function ( $path ) { return esc_url( home_url( $path ) ); };
?>
<!-- wp:group {"align":"full","className":"fb-sec-approach is-style-section-dark","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained","contentSize":"1180px"}} -->
<div class="wp-block-group alignfull fb-sec-approach is-style-section-dark" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|60"}}}} -->
<div class="wp-block-columns alignwide"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"className":"is-style-eyebrow"} -->
<p class="is-style-eyebrow">Our methods</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Reward-based, and we will tell you exactly what that means.</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>We teach the dog what to do instead of punishing what it did. Food, play and timing do the work. Nothing we use causes pain or fear.</p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"is-style-checklist"} -->
<ul class="wp-block-list is-style-checklist"><!-- wp:list-item -->
<li>No prong collars, choke chains or shock collars, in class or at home</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>You keep the lead. Instructors coach you, they do not take your dog away</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Every exercise is written down, so the rest of the household can do it the same way</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"className":"is-style-eyebrow"} -->
<p class="is-style-eyebrow">If it is not working</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">We would rather move you than keep you.</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>If your dog is not coping in a class, we move you to a private session and only charge the difference. If the problem looks medical, we say so and ask you to see your vet first.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Both instructors are named, with their training and their limits written down: <a href="<?php echo $url( '/about/' ); ?>">meet Dana and Priya</a>. If you would rather ask a question than fill in a form, the <a href="<?php echo $url( '/contact/' ); ?>">contact page</a> has the phone number and the hall address.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size"><span class="fb-sample">Sample terms written for this demo.</span></p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"fb-track-cta"} -->
<div class="wp-block-button fb-track-cta"><a class="wp-block-button__link wp-element-button" href="<?php echo $url( '/enquire/' ); ?>">Ask about your dog</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
