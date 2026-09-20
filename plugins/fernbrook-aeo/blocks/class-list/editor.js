/**
 * Editor side of the Class dates block. No build step: plain JavaScript using WordPress's globals.
 * The preview is the same PHP the visitor gets, so nothing can look right here and wrong live.
 */
( function ( wp ) {
	var el = wp.element.createElement;
	var __ = wp.i18n.__;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var PanelBody = wp.components.PanelBody;
	var RangeControl = wp.components.RangeControl;
	var Disabled = wp.components.Disabled;
	var ServerSideRender = wp.serverSideRender;

	wp.blocks.registerBlockType( 'fernbrook/class-list', {
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
						{ title: __( 'Class dates', 'fernbrook-aeo' ) },
						el( RangeControl, {
							label: __( 'How many dates to show', 'fernbrook-aeo' ),
							value: a.limit,
							min: 1,
							max: 12,
							onChange: function ( v ) { props.setAttributes( { limit: v } ); },
						} ),
						el( 'p', { style: { color: '#555' } }, __( 'Dates are edited under Classes in the admin menu. The same records produce the Event structured data on this page.', 'fernbrook-aeo' ) )
					)
				),
				el( Disabled, null, el( ServerSideRender, { block: 'fernbrook/class-list', attributes: a } ) )
			);
		},
		save: function () { return null; },
	} );
} )( window.wp );
