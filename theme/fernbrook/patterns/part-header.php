<?php
/**
 * Title: Part: header
 * Slug: fernbrook/part-header
 * Inserter: no
 *
 * Template part markup lives in a pattern so links use this site's own address.
 */

// Point the menu at the site's "Main menu" (created by the demo setup, editable in Appearance → Editor → Navigation).
$fb_menu = get_posts( array( 'post_type' => 'wp_navigation', 'title' => 'Main menu', 'numberposts' => 1, 'fields' => 'ids', 'post_status' => 'publish' ) );
$fb_ref  = $fb_menu ? '"ref":' . (int) $fb_menu[0] . ',' : '';
?>
<!-- wp:group {"tagName":"div","className":"fb-header","style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20"}}},"backgroundColor":"base","layout":{"type":"constrained","contentSize":"1180px"}} -->
<div class="wp-block-group fb-header has-base-background-color has-background" style="padding-top:var(--wp--preset--spacing--20);padding-bottom:var(--wp--preset--spacing--20)"><!-- wp:group {"align":"wide","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
<div class="wp-block-group alignwide"><!-- wp:group {"className":"fb-brand","layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group fb-brand"><!-- wp:site-title {"level":0} /--></div>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"right"}} -->
<div class="wp-block-group"><!-- wp:navigation {<?php echo $fb_ref; // phpcs:ignore ?>"overlayMenu":"mobile","layout":{"type":"flex","justifyContent":"right"},"style":{"spacing":{"blockGap":"var:preset|spacing|30"}}} /-->

<!-- wp:paragraph {"className":"fb-phone","style":{"typography":{"lineHeight":"1.2"}}} -->
<p class="fb-phone"><a href="tel:+15550100188">(555) 010-0188</a><small>Tue–Sat</small></p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"className":"fb-header-btn"} -->
<div class="wp-block-buttons fb-header-btn"><!-- wp:button {"className":"fb-track-cta"} -->
<div class="wp-block-button fb-track-cta"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/enquire/' ) ); ?>">Book a place</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
