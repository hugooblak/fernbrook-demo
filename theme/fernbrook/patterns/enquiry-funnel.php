<?php
/**
 * Title: Page: enquiry funnel
 * Slug: fernbrook/enquiry-funnel
 * Categories: fernbrook-pages
 * Block Types: core/post-content
 * Post Types: page
 * Description: The focused booking page: no menu, one form, and the few facts people need before they send it.
 */
$img = get_theme_file_uri( 'assets/img/' );
?>
<!-- wp:group {"align":"full","className":"fb-sec-funnel","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|70"}}},"backgroundColor":"sand","layout":{"type":"constrained","contentSize":"1180px"}} -->
<div class="wp-block-group alignfull fb-sec-funnel has-sand-background-color has-background" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|60"}}}} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"width":"56%"} -->
<div class="wp-block-column" style="flex-basis:56%"><!-- wp:group {"className":"is-style-card fb-lift","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group is-style-card fb-lift" style="padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)"><!-- wp:heading {"level":1,"fontSize":"xx-large"} -->
<h1 class="wp-block-heading has-xx-large-font-size">Ask about a place</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"ink-soft"} -->
<p class="has-ink-soft-color has-text-color">Four short questions. An instructor replies within one working day.</p>
<!-- /wp:paragraph -->

<!-- wp:fernbrook/lead-form /--></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"44%"} -->
<div class="wp-block-column" style="flex-basis:44%"><!-- wp:heading {"level":2,"fontSize":"large"} -->
<h2 class="wp-block-heading has-large-font-size">What happens next</h2>
<!-- /wp:heading -->

<!-- wp:list {"className":"is-style-checklist"} -->
<ul class="wp-block-list is-style-checklist"><!-- wp:list-item -->
<li>An instructor reads it, not a booking robot</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>We reply within one working day, Tue to Sat</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>If a class is wrong for your dog, we say so</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Nothing is charged until you accept a place</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:paragraph {"backgroundColor":"bay-light","style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"fontSize":"small"} -->
<p class="has-bay-light-background-color has-background has-small-font-size" style="padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)">Rather talk? Call <a href="tel:+15550100188"><strong>(555) 010-0188</strong></a>, Tue–Sat 8am–6pm. What we do with your details is set out in the <a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>">privacy policy</a>.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
