<?php
/**
 * Related pages, chosen from the entity graph rather than at random.
 *
 * A guide points at the programs it is about. A program points at the other programs and at
 * the guides. Internal links matter twice over here: a person follows them, and a machine uses
 * them to work out that these pages describe one business.
 *
 * @var array $attributes Block attributes.
 * @package Fernbrook AI Search
 */

defined( 'ABSPATH' ) || exit;

$fbaeo_post = get_post();
if ( ! $fbaeo_post ) {
	return;
}

$fbaeo_links = array();

if ( 'post' === $fbaeo_post->post_type ) {
	foreach ( array( 'puppy-foundations', 'adolescent-manners', 'private-training' ) as $fbaeo_slug ) {
		$fbaeo_page = get_page_by_path( $fbaeo_slug );
		if ( $fbaeo_page ) {
			$fbaeo_links[] = $fbaeo_page;
		}
	}
} else {
	foreach ( get_posts( array( 'post_type' => 'post', 'numberposts' => 3, 'post_status' => 'publish' ) ) as $fbaeo_guide ) {
		$fbaeo_links[] = $fbaeo_guide;
	}
}

if ( ! $fbaeo_links ) {
	return;
}

$fbaeo_wrapper = get_block_wrapper_attributes( array( 'class' => 'fbaeo-related' ) );
$fbaeo_heading = isset( $attributes['heading'] ) && $attributes['heading'] ? $attributes['heading'] : __( 'Read next', 'fernbrook-aeo' );
?>
<div <?php echo $fbaeo_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<h2><?php echo esc_html( $fbaeo_heading ); ?></h2>
	<ul>
		<?php foreach ( $fbaeo_links as $fbaeo_link ) : ?>
			<li><a href="<?php echo esc_url( get_permalink( $fbaeo_link ) ); ?>"><?php echo esc_html( get_the_title( $fbaeo_link ) ); ?></a></li>
		<?php endforeach; ?>
	</ul>
</div>
