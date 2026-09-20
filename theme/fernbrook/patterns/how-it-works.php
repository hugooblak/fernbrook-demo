<?php
/**
 * Title: How it works (3 steps)
 * Slug: fernbrook/how-it-works
 * Categories: fernbrook-sections
 * Description: Three numbered steps from first message to the end of the course.
 */
$url = function ( $path ) { return esc_url( home_url( $path ) ); };
?>
<!-- wp:group {"align":"full","className":"fb-sec-steps","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained","contentSize":"1180px"}} -->
<div class="wp-block-group alignfull fb-sec-steps" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:group {"align":"wide","layout":{"type":"constrained","contentSize":"640px","justifyContent":"left"}} -->
<div class="wp-block-group alignwide"><!-- wp:paragraph {"className":"is-style-eyebrow"} -->
<p class="is-style-eyebrow">How it works</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">From first message to the last class</h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:columns {"align":"wide","style":{"spacing":{"margin":{"top":"var:preset|spacing|50"},"blockGap":{"left":"var:preset|spacing|50"}}}} -->
<div class="wp-block-columns alignwide" style="margin-top:var(--wp--preset--spacing--50)"><?php
$steps = array(
	array( 'Tell us about your dog', 'Four short questions online, or call us. We need the age, the breed if you know it, and the one thing you would fix today.' ),
	array( 'We suggest a class or a session', 'If a class fits, we hold a place and send the dates. If your dog would struggle in a room with five others, we say so and suggest a private session instead.' ),
	array( 'Six weeks, then a plan to keep', 'You get a one-page plan after every class and a short written summary at the end. Miss a week and you can sit in on the next course for free.' ),
);
foreach ( $steps as $i => $step ) :
	?><!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"className":"fb-step-num"} -->
<p class="fb-step-num"><?php echo (int) $i + 1; ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php echo esc_html( $step[0] ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"ink-soft"} -->
<p class="has-ink-soft-color has-text-color"><?php echo esc_html( $step[1] ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<?php endforeach; ?></div>
<!-- /wp:columns -->

<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--50)"><!-- wp:button {"className":"fb-track-cta"} -->
<div class="wp-block-button fb-track-cta"><a class="wp-block-button__link wp-element-button" href="<?php echo $url( '/enquire/' ); ?>">Start step 1: ask about a place</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->
