<?php
/**
 * Title: Not found
 * Slug: fernbrook/not-found
 * Categories: fernbrook-sections
 * Description: 404 message with the two most useful next steps.
 */
$url = function ( $path ) { return esc_url( home_url( $path ) ); };
?>
<!-- wp:heading {"level":1,"fontSize":"xx-large"} -->
<h1 class="wp-block-heading has-xx-large-font-size">We couldn't find that page</h1>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>The link may be old. If you came looking for class dates or a place, both are one click away.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"fb-track-cta"} -->
<div class="wp-block-button fb-track-cta"><a class="wp-block-button__link wp-element-button" href="<?php echo $url( '/classes/' ); ?>">See class dates</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo $url( '/' ); ?>">Go to the home page</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->
