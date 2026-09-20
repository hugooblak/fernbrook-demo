<?php
/**
 * Title: Answers (short questions)
 * Slug: fernbrook/answers
 * Categories: fernbrook-sections
 * Description: Open/close questions, answer first. The AI Search plugin reads these same blocks to build the FAQ structured data, so the two can never drift apart.
 */
?>
<!-- wp:group {"align":"full","anchor":"answers","className":"fb-sec-answers","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained","contentSize":"760px"}} -->
<div id="answers" class="wp-block-group alignfull fb-sec-answers" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:paragraph {"className":"is-style-eyebrow"} -->
<p class="is-style-eyebrow">Questions</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Before you book</h2>
<!-- /wp:heading -->

<?php
$faqs = array(
	array( 'How old does my puppy need to be?', 'Eight weeks, with the first vaccination done. Waiting until a puppy is fully vaccinated at 16 weeks means missing most of the window when new things are easy to accept. Our hall is cleaned between classes and puppies stay off the floor until their turn.' ),
	array( 'How many dogs are in a class?', 'Six, with two instructors. That is the number where every handler still gets hands-on time in an hour.' ),
	array( 'What do the six weeks cost?', 'Puppy Foundations is $320 and Adolescent Manners is $340, for six weekly classes. Private sessions are $145 an hour. Prices include the written plan after each class.' ),
	array( 'What if we miss a week?', 'Sit in on the same week of the next course at no cost. Tell us as early as you can so we keep the numbers right.' ),
	array( 'My dog barks and lunges at other dogs. Can we come?', 'Not to a group class first. Book a private session so we can work at a distance your dog can handle. Many dogs join a class later.' ),
	array( 'Where do the classes happen?', 'The Harbour Lane hall in Alder Bay. Private sessions happen at your home, or on a quiet trail in Harrowfield, Quarry Hill, Selkirk Ferry or Ravensmoor.' ),
);
foreach ( $faqs as $f ) :
	?><!-- wp:details -->
<details class="wp-block-details"><summary><?php echo esc_html( $f[0] ); ?></summary><!-- wp:paragraph -->
<p><?php echo esc_html( $f[1] ); ?></p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->

<?php endforeach; ?><!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} -->
<p style="margin-top:var(--wp--preset--spacing--40)">Still not sure? <a href="tel:+15550100188">Call (555) 010-0188</a>, or read the <a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>">full FAQ</a>.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->
