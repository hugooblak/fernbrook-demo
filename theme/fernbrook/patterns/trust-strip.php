<?php
/**
 * Title: Trust strip
 * Slug: fernbrook/trust-strip
 * Categories: fernbrook-sections
 * Description: Four short proof points with icons, placed right under the hero.
 */
$img = get_theme_file_uri( 'assets/img/' );
?>
<!-- wp:group {"align":"full","className":"fb-sec-trust fb-trust","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}},"border":{"bottom":{"color":"var:preset|color|line","width":"1px"}}},"layout":{"type":"constrained","contentSize":"1180px"}} -->
<div class="wp-block-group alignfull fb-sec-trust fb-trust" style="border-bottom-color:var(--wp--preset--color--line);border-bottom-width:1px;padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40)"><!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"grid","minimumColumnWidth":"14rem"}} -->
<div class="wp-block-group alignwide"><?php
$items = array(
	array( 'icon-class', 'Six dogs per class' ),
	array( 'icon-paw', 'Reward-based methods only' ),
	array( 'icon-clock', 'We reply within one working day' ),
	array( 'icon-shield', 'Insured, and first aid trained' ),
);
foreach ( $items as $item ) :
	?><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group"><!-- wp:image {"width":"40px","height":"40px","sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full is-resized"><img src="<?php echo esc_url( $img . $item[0] . '.svg' ); ?>" alt="" style="width:40px;height:40px"/></figure>
<!-- /wp:image -->

<!-- wp:paragraph -->
<p><?php echo esc_html( $item[1] ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<?php endforeach; ?></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
