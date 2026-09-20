/**
 * Editor side of the Quote form block. No build step: plain JavaScript using WordPress's globals.
 * The preview is rendered by the same PHP as the live site, so what you see is what visitors get.
 */
( function ( wp ) {
	var el = wp.element.createElement;
	var __ = wp.i18n.__;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var PanelBody = wp.components.PanelBody;
	var SelectControl = wp.components.SelectControl;
	var TextControl = wp.components.TextControl;
	var Disabled = wp.components.Disabled;
	var ServerSideRender = wp.serverSideRender;

	wp.blocks.registerBlockType( 'fernbrook/lead-form', {
		edit: function ( props ) {
			var a = props.attributes;
			return el(
				'div',
				useBlockProps(),
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Form settings', 'fernbrook-leads' ) },
						el( SelectControl, {
							label: __( 'Form type', 'fernbrook-leads' ),
							value: a.variant,
							options: [
								{ label: __( 'Start (ZIP code only, for heroes and page ends)', 'fernbrook-leads' ), value: 'start' },
								{ label: __( 'Full (all 4 steps, for the booking page)', 'fernbrook-leads' ), value: 'full' },
							],
							onChange: function ( v ) { props.setAttributes( { variant: v } ); },
						} ),
						a.variant === 'start' && el( TextControl, {
							label: __( 'Button text', 'fernbrook-leads' ),
							help: __( 'Leave empty for "Ask about a place".', 'fernbrook-leads' ),
							value: a.buttonText,
							onChange: function ( v ) { props.setAttributes( { buttonText: v } ); },
						} ),
						el( 'p', { style: { color: '#555' } }, __( 'Questions and answer choices are set in the Fernbrook Dog Academy Leads plugin (includes/fields.php). Leads appear under Leads in the admin menu.', 'fernbrook-leads' ) )
					)
				),
				el( Disabled, null, el( ServerSideRender, { block: 'fernbrook/lead-form', attributes: a } ) )
			);
		},
		save: function () { return null; },
	} );
} )( window.wp );
