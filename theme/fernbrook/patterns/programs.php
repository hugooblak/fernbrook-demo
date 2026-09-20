<?php
/**
 * Title: Programs grid
 * Slug: fernbrook/programs
 * Categories: fernbrook-sections
 * Description: Four cards, one per program. Each links to its own page, or to the booking form with that program already chosen.
 */
$img = get_theme_file_uri( 'assets/img/' );
$url = function ( $path ) { return esc_url( home_url( $path ) ); };
?>
<!-- wp:group {"align":"full","anchor":"programs","className":"fb-sec-programs is-style-section-sand","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained","contentSize":"1180px"}} -->
<div id="programs" class="wp-block-group alignfull fb-sec-programs is-style-section-sand" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:group {"align":"wide","layout":{"type":"constrained","contentSize":"640px","justifyContent":"left"}} -->
<div class="wp-block-group alignwide"><!-- wp:paragraph {"className":"is-style-eyebrow"} -->
<p class="is-style-eyebrow">Programs</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Pick the one that matches your dog's age</h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"wide","style":{"spacing":{"margin":{"top":"var:preset|spacing|50"},"blockGap":"var:preset|spacing|40"}},"layout":{"type":"grid","minimumColumnWidth":"15rem"}} -->
<div class="wp-block-group alignwide" style="margin-top:var(--wp--preset--spacing--50)"><?php
$programs = array(
	array( 'icon-paw', 'Puppy Foundations', '8 to 18 weeks. Six weekly classes covering toilet training, settling, handling and the first recall.', '/puppy-foundations/', 'See the six weeks' ),
	array( 'icon-class', 'Adolescent Manners', '6 to 18 months. For the dog that knew "sit" last month and has forgotten it this month.', '/adolescent-manners/', 'See the six weeks' ),
	array( 'icon-home', 'Private training', 'One to one, at your home or on a quiet trail. For barking, lunging, or anything a class is too busy for.', '/private-training/', 'See how sessions work' ),
	array( 'icon-chat', 'Not sure which?', 'Tell us your dog\'s age and what is hard right now. We answer in one working day, for free.', '/enquire/', 'Ask us' ),
);
foreach ( $programs as $s ) :
	?><!-- wp:group {"className":"is-style-card","layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group is-style-card"><!-- wp:image {"width":"48px","height":"48px","sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full is-resized"><img src="<?php echo esc_url( $img . $s[0] . '.svg' ); ?>" alt="" style="width:48px;height:48px"/></figure>
<!-- /wp:image -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php echo esc_html( $s[1] ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"ink-soft","fontSize":"small"} -->
<p class="has-ink-soft-color has-text-color has-small-font-size"><?php echo esc_html( $s[2] ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"fontWeight":"700"}},"fontSize":"small"} -->
<p class="has-small-font-size" style="font-weight:700"><a href="<?php echo $url( $s[3] ); ?>"><?php echo esc_html( $s[4] ); ?> →</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<?php endforeach; ?></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
