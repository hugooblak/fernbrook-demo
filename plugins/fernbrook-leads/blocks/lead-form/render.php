<?php
/**
 * Quote form markup (server-rendered, so it works without JavaScript).
 *
 * Without JS: all four questions show as one normal form.
 * With JS (view.js): one question at a time, progress bar, back button, instant error messages.
 *
 * @var array $attributes Block attributes.
 * @package Fernbrook Dog Academy Leads
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.Security.NonceVerification.Recommended -- reading prefill values from the URL only.
$fbl_variant = ( $attributes['variant'] ?? 'full' ) === 'start' ? 'start' : 'full';
$fbl_state   = fbl_current_state();
$fbl_errors  = $fbl_state['errors'];
$fbl_values  = $fbl_state['values'];
$fbl_choices = fbl_choices();

// Prefill from the URL: ?zip=94099 (from the short form) and ?program=private (from program pages).
if ( empty( $fbl_values['zip'] ) && isset( $_GET['zip'] ) ) {
	$fbl_values['zip'] = substr( preg_replace( '/\D/', '', wp_unslash( $_GET['zip'] ) ), 0, 5 );
}
if ( empty( $fbl_values['program'] ) && isset( $_GET['program'] ) ) {
	$fbl_values['program'] = sanitize_key( $_GET['program'] );
}
// phpcs:enable

$fbl_val = function ( $key ) use ( $fbl_values ) {
	return isset( $fbl_values[ $key ] ) ? (string) $fbl_values[ $key ] : '';
};

/**
 * Error message element for a field, linked to it with aria-describedby.
 */
$fbl_error = function ( $key, $uid ) use ( $fbl_errors ) {
	$msg = $fbl_errors[ $key ] ?? '';
	printf(
		'<p class="fbl-error" id="%1$s-%2$s-err"%3$s>%4$s</p>',
		esc_attr( $uid ),
		esc_attr( $key ),
		$msg ? '' : ' hidden',
		esc_html( $msg )
	);
};

// Unique IDs, so two forms on one page never share label targets.
$GLOBALS['fbl_form_count'] = ( $GLOBALS['fbl_form_count'] ?? 0 ) + 1;
$fbl_uid                   = 'npl' . $GLOBALS['fbl_form_count'];

$fbl_wrapper = get_block_wrapper_attributes(
	array(
		'class' => 'fbl-form fbl-variant-' . $fbl_variant,
	)
);

/* ----------------------------------------------------------------------------
 * Variant "start": ZIP only. Sends people to the full form with the ZIP filled in.
 * One easy first question gets people moving; finishing what you started is a strong pull.
 * ------------------------------------------------------------------------- */
if ( 'start' === $fbl_variant ) :
	$fbl_lead_page = (int) get_option( 'fbl_lead_page' );
	$fbl_action     = $fbl_lead_page ? get_permalink( $fbl_lead_page ) : home_url( '/enquire/' );
	$fbl_button     = ! empty( $attributes['buttonText'] ) ? $attributes['buttonText'] : __( 'Ask about a place', 'fernbrook-leads' );
	?>
	<div <?php echo $fbl_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
		<form class="fbl-start" method="get" action="<?php echo esc_url( $fbl_action ); ?>" novalidate data-fbl-start>
			<label class="fbl-label" for="<?php echo esc_attr( $fbl_uid ); ?>-zip"><?php esc_html_e( 'Your ZIP code', 'fernbrook-leads' ); ?></label>
			<div class="fbl-inline">
				<input class="fbl-input" id="<?php echo esc_attr( $fbl_uid ); ?>-zip" name="zip" type="text" inputmode="numeric" autocomplete="postal-code" pattern="[0-9]{5}" maxlength="5" required placeholder="e.g. 94099" aria-describedby="<?php echo esc_attr( $fbl_uid ); ?>-zip-err" value="<?php echo esc_attr( $fbl_val( 'zip' ) ); ?>">
				<button class="fbl-btn wp-element-button" type="submit"><?php echo esc_html( $fbl_button ); ?> <span aria-hidden="true">→</span></button>
			</div>
			<?php $fbl_error( 'zip', $fbl_uid ); ?>
			<p class="fbl-fineprint"><?php esc_html_e( 'Step 1 of 4. We never sell or share your details.', 'fernbrook-leads' ); ?></p>
		</form>
	</div>
	<?php
	return;
endif;

/* ----------------------------------------------------------------------------
 * Variant "full": the 4-step form.
 * ------------------------------------------------------------------------- */
// Which step to open first: the first one with an error, or step 2 if a valid ZIP came from the short form.
$fbl_field_step = array( 'zip' => 1, 'program' => 2, 'dog_age' => 3, 'name' => 4, 'phone' => 4, 'email' => 4 );
$fbl_start      = 1;
if ( $fbl_errors ) {
	$fbl_start = min( array_map( fn( $k ) => $fbl_field_step[ $k ] ?? 1, array_keys( $fbl_errors ) ) );
} elseif ( preg_match( '/^\d{5}$/', $fbl_val( 'zip' ) ) ) {
	$fbl_start = 2;
}

$fbl_steps = array(
	1 => __( 'Where are you?', 'fernbrook-leads' ),
	2 => __( 'What are you interested in?', 'fernbrook-leads' ),
	3 => __( 'How old is your dog?', 'fernbrook-leads' ),
	4 => __( 'Where should we send our reply?', 'fernbrook-leads' ),
);
?>
<div <?php echo $fbl_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput ?> id="fbl-form">
	<form class="fbl-steps" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate data-fbl-steps data-total="4" data-start="<?php echo (int) $fbl_start; ?>">
		<input type="hidden" name="action" value="fbl_submit">
		<input type="hidden" name="fbl_token" value="<?php echo esc_attr( fbl_form_token() ); ?>">
		<?php foreach ( fbl_tracking_fields() as $fbl_field ) : ?>
			<input type="hidden" name="<?php echo esc_attr( $fbl_field ); ?>" value="" data-fbl-track="<?php echo esc_attr( $fbl_field ); ?>">
		<?php endforeach; ?>

		<?php // Honeypot: hidden from people and screen readers. Bots fill every field they find. ?>
		<div class="fbl-hp" aria-hidden="true">
			<label for="<?php echo esc_attr( $fbl_uid ); ?>-hp">Company website</label>
			<input id="<?php echo esc_attr( $fbl_uid ); ?>-hp" type="text" name="company_website" tabindex="-1" autocomplete="off">
		</div>

		<?php if ( $fbl_errors ) : ?>
			<div class="fbl-summary" role="alert" tabindex="-1" data-fbl-summary>
				<p><strong><?php esc_html_e( 'Please check these answers:', 'fernbrook-leads' ); ?></strong></p>
				<ul>
					<?php foreach ( $fbl_errors as $fbl_key => $fbl_msg ) : ?>
						<li><a href="#<?php echo esc_attr( $fbl_uid . '-' . $fbl_key ); ?>"><?php echo esc_html( $fbl_msg ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<div class="fbl-progress" data-fbl-progress hidden>
			<p class="fbl-progress-text" aria-live="polite" data-fbl-progress-text><?php echo esc_html( sprintf( /* translators: %d: step number */ __( 'Step %d of 4', 'fernbrook-leads' ), $fbl_start ) ); ?></p>
			<div class="fbl-bar" aria-hidden="true"><span data-fbl-bar style="width:<?php echo (int) $fbl_start * 25; ?>%"></span></div>
		</div>

		<?php /* Step 1: ZIP */ ?>
		<fieldset class="fbl-step" data-step="1" data-name="zip" tabindex="-1">
			<legend class="fbl-legend"><?php echo esc_html( $fbl_steps[1] ); ?></legend>
			<label class="fbl-label" for="<?php echo esc_attr( $fbl_uid ); ?>-zip"><?php esc_html_e( 'ZIP code', 'fernbrook-leads' ); ?></label>
			<input class="fbl-input fbl-input-short" id="<?php echo esc_attr( $fbl_uid ); ?>-zip" name="zip" type="text" inputmode="numeric" autocomplete="postal-code" pattern="[0-9]{5}" maxlength="5" required aria-describedby="<?php echo esc_attr( $fbl_uid ); ?>-zip-err"<?php echo isset( $fbl_errors['zip'] ) ? ' aria-invalid="true"' : ''; ?> value="<?php echo esc_attr( $fbl_val( 'zip' ) ); ?>">
			<?php $fbl_error( 'zip', $fbl_uid ); ?>
		</fieldset>

		<?php
		/* Steps 2 and 3: big tap-friendly choice cards (real radio buttons underneath). */
		foreach ( array( 2 => 'program', 3 => 'dog_age' ) as $fbl_n => $fbl_field ) :
			?>
			<fieldset class="fbl-step" data-step="<?php echo (int) $fbl_n; ?>" data-name="<?php echo esc_attr( $fbl_field ); ?>" tabindex="-1" id="<?php echo esc_attr( $fbl_uid . '-' . $fbl_field ); ?>" aria-describedby="<?php echo esc_attr( $fbl_uid . '-' . $fbl_field ); ?>-err"<?php echo isset( $fbl_errors[ $fbl_field ] ) ? ' aria-invalid="true"' : ''; ?>>
				<legend class="fbl-legend"><?php echo esc_html( $fbl_steps[ $fbl_n ] ); ?></legend>
				<p class="fbl-hint fbl-auto-hint"><?php esc_html_e( 'Choosing an answer takes you to the next question.', 'fernbrook-leads' ); ?></p>
				<div class="fbl-choices">
					<?php foreach ( $fbl_choices[ $fbl_field ] as $fbl_value => $fbl_label ) : ?>
						<label class="fbl-choice">
							<input type="radio" name="<?php echo esc_attr( $fbl_field ); ?>" value="<?php echo esc_attr( $fbl_value ); ?>" required<?php checked( $fbl_val( $fbl_field ), $fbl_value ); ?>>
							<span><?php echo esc_html( $fbl_label ); ?></span>
						</label>
					<?php endforeach; ?>
				</div>
				<?php $fbl_error( $fbl_field, $fbl_uid ); ?>
			</fieldset>
		<?php endforeach; ?>

		<?php /* Step 4: contact details. Only three fields, each with a reason. */ ?>
		<fieldset class="fbl-step" data-step="4" data-name="contact" tabindex="-1">
			<legend class="fbl-legend"><?php echo esc_html( $fbl_steps[4] ); ?></legend>
			<?php
			$fbl_contact = array(
				'name'  => array( __( 'First name', 'fernbrook-leads' ), 'text', 'given-name', '' ),
				'phone' => array( __( 'Mobile phone', 'fernbrook-leads' ), 'tel', 'tel-national', __( 'So we can call you about a place.', 'fernbrook-leads' ) ),
				'email' => array( __( 'Email', 'fernbrook-leads' ), 'email', 'email', __( 'Our reply and the class dates go here.', 'fernbrook-leads' ) ),
			);
			foreach ( $fbl_contact as $fbl_key => $fbl_f ) :
				$fbl_describe = $fbl_uid . '-' . $fbl_key . '-err' . ( $fbl_f[3] ? ' ' . $fbl_uid . '-' . $fbl_key . '-hint' : '' );
				?>
				<div class="fbl-field">
					<label class="fbl-label" for="<?php echo esc_attr( $fbl_uid . '-' . $fbl_key ); ?>"><?php echo esc_html( $fbl_f[0] ); ?></label>
					<?php if ( $fbl_f[3] ) : ?>
						<p class="fbl-hint" id="<?php echo esc_attr( $fbl_uid . '-' . $fbl_key ); ?>-hint"><?php echo esc_html( $fbl_f[3] ); ?></p>
					<?php endif; ?>
					<input class="fbl-input" id="<?php echo esc_attr( $fbl_uid . '-' . $fbl_key ); ?>" name="<?php echo esc_attr( $fbl_key ); ?>" type="<?php echo esc_attr( $fbl_f[1] ); ?>" autocomplete="<?php echo esc_attr( $fbl_f[2] ); ?>" required aria-describedby="<?php echo esc_attr( $fbl_describe ); ?>"<?php echo isset( $fbl_errors[ $fbl_key ] ) ? ' aria-invalid="true"' : ''; ?> value="<?php echo esc_attr( $fbl_val( $fbl_key ) ); ?>"<?php echo 'phone' === $fbl_key ? ' inputmode="tel"' : ''; ?>>
					<?php $fbl_error( $fbl_key, $fbl_uid ); ?>
				</div>
			<?php endforeach; ?>
		</fieldset>

		<div class="fbl-nav">
			<button type="button" class="fbl-back" data-fbl-back hidden><span aria-hidden="true">←</span> <?php esc_html_e( 'Back', 'fernbrook-leads' ); ?></button>
			<button type="button" class="fbl-btn wp-element-button" data-fbl-next hidden><?php esc_html_e( 'Next', 'fernbrook-leads' ); ?> <span aria-hidden="true">→</span></button>
			<button type="submit" class="fbl-btn wp-element-button" data-fbl-submit><?php esc_html_e( 'Send my enquiry', 'fernbrook-leads' ); ?></button>
		</div>
		<p class="fbl-fineprint" data-fbl-consent><?php echo esc_html( fbl_consent_text() ); ?></p>
	</form>
</div>
