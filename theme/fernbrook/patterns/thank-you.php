<?php
/**
 * Title: Thank you
 * Slug: fernbrook/thank-you
 * Categories: fernbrook-pages
 * Description: Confirmation after the form: what happens next, the phone number, and useful reading while they wait.
 */
$img = get_theme_file_uri( 'assets/img/' );
$url = function ( $path ) { return esc_url( home_url( $path ) ); };
?>
<!-- wp:group {"align":"full","className":"fb-sec-thanks","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained","contentSize":"700px"}} -->
<div class="wp-block-group alignfull fb-sec-thanks" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:group {"className":"is-style-card fb-lift","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group is-style-card fb-lift" style="padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)"><!-- wp:image {"width":"48px","height":"48px","sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full is-resized"><img src="<?php echo esc_url( $img . 'icon-paw.svg' ); ?>" alt="" style="width:48px;height:48px"/></figure>
<!-- /wp:image -->

<!-- wp:heading {"level":1,"fontSize":"xx-large"} -->
<h1 class="wp-block-heading has-xx-large-font-size">Thanks, we have your message.</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size">An instructor reads every enquiry and replies within <strong>one working day</strong>.</p>
<!-- /wp:paragraph -->

<!-- wp:list {"ordered":true} -->
<ol class="wp-block-list"><!-- wp:list-item -->
<li><strong>Today or tomorrow:</strong> we reply with the class that fits, or say why a private session would suit your dog better.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>If you take the place:</strong> we hold it for 48 hours and send the six dates.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>Before class one:</strong> a short email about what to bring and where to park.</li>
<!-- /wp:list-item --></ol>
<!-- /wp:list -->

<!-- wp:paragraph {"backgroundColor":"bay-light","style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}}} -->
<p class="has-bay-light-background-color has-background" style="padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)"><strong>Your dog has bitten someone, or you are afraid of it?</strong> Call <a href="tel:+15550100188">(555) 010-0188</a> rather than waiting for email.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:heading {"level":2,"fontSize":"large","style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
<h2 class="wp-block-heading has-large-font-size" style="margin-top:var(--wp--preset--spacing--50)">While you wait</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li><a href="<?php echo $url( '/guides/' ); ?>">Read the owner guides</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="<?php echo $url( '/classes/' ); ?>">See the next class dates</a></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:group -->
