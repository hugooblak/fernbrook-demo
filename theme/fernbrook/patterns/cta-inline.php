<?php
/**
 * Title: Inline call to action
 * Slug: fernbrook/cta-inline
 * Categories: fernbrook-sections
 * Description: Small box at the end of a guide or page, linking to the booking form.
 */
$url = function ( $path ) { return esc_url( home_url( $path ) ); };
?>
<!-- wp:group {"className":"is-style-card fb-sec-inline","style":{"spacing":{"margin":{"top":"var:preset|spacing|60"}}},"backgroundColor":"bay-light","layout":{"type":"default"}} -->
<div class="wp-block-group is-style-card fb-sec-inline has-bay-light-background-color has-background" style="margin-top:var(--wp--preset--spacing--60)"><!-- wp:heading {"level":2,"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-x-large-font-size">Want a second opinion on your dog?</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Send us the age and the one thing that is hard right now. We answer in one working day, and we will tell you if you do not need us.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"fb-track-cta"} -->
<div class="wp-block-button fb-track-cta"><a class="wp-block-button__link wp-element-button" href="<?php echo $url( '/enquire/' ); ?>">Ask about a place</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->
