<?php
/**
 * The visible list of class start dates.
 *
 * Read from the Classes records, which also produce the Event structured data for this page.
 * One source, two outputs. That is the rule: never publish structured data saying something
 * a visitor cannot read on the page.
 *
 * @var array $attributes Block attributes.
 * @package Fernbrook AI Search
 */

defined( 'ABSPATH' ) || exit;

$fbaeo_limit   = isset( $attributes['limit'] ) ? (int) $attributes['limit'] : 4;
$fbaeo_classes = fbaeo_upcoming_classes( $fbaeo_limit );
$fbaeo_wrapper = get_block_wrapper_attributes( array( 'class' => 'fbaeo-classes' ) );
?>
<div <?php echo $fbaeo_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<?php if ( ! $fbaeo_classes ) : ?>
		<p class="fbaeo-classes-none"><?php esc_html_e( 'No dates are open at the moment. Send an enquiry and we will tell you when the next course opens.', 'fernbrook-aeo' ); ?></p>
	<?php endif; ?>

	<?php
	foreach ( $fbaeo_classes as $fbaeo_class ) :
		$fbaeo_program = $fbaeo_class['program'] ? get_post( $fbaeo_class['program'] ) : null;
		$fbaeo_weeks   = $fbaeo_class['weeks'] ? $fbaeo_class['weeks'] : 6;
		$fbaeo_spots   = $fbaeo_class['spots'];
		?>
		<div class="fbaeo-class">
			<p class="fbaeo-class-date"><time datetime="<?php echo esc_attr( $fbaeo_class['date'] ); ?>"><?php echo esc_html( wp_date( 'D j M', $fbaeo_class['stamp'] ) ); ?></time></p>
			<p class="fbaeo-class-name">
				<?php if ( $fbaeo_program ) : ?>
					<a href="<?php echo esc_url( get_permalink( $fbaeo_program ) ); ?>"><?php echo esc_html( $fbaeo_class['title'] ); ?></a>
				<?php else : ?>
					<?php echo esc_html( $fbaeo_class['title'] ); ?>
				<?php endif; ?>
			</p>
			<p class="fbaeo-class-meta">
				<?php
				printf(
					/* translators: 1: start time, 2: number of weeks */
					esc_html__( '%1$s · %2$d weekly classes', 'fernbrook-aeo' ),
					esc_html( $fbaeo_class['time'] ),
					(int) $fbaeo_weeks
				);
				if ( '' !== $fbaeo_class['price'] ) {
					echo ' · ' . esc_html( $fbaeo_class['price'] );
				}
				?>
			</p>
			<p class="fbaeo-class-meta">
				<?php if ( '0' === $fbaeo_spots ) : ?>
					<span class="fbaeo-class-full"><?php esc_html_e( 'Full — join the waiting list', 'fernbrook-aeo' ); ?></span>
				<?php elseif ( '' !== $fbaeo_spots ) : ?>
					<?php
					printf(
						/* translators: %d: places */
						esc_html( _n( '%d place left', '%d places left', (int) $fbaeo_spots, 'fernbrook-aeo' ) ),
						(int) $fbaeo_spots
					);
					?>
				<?php endif; ?>
			</p>
		</div>
	<?php endforeach; ?>
</div>
