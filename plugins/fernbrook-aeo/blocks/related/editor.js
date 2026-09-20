/**
 * Editor side of the Related pages block. No build step.
 */
( function ( wp ) {
	var el = wp.element.createElement;
	var __ = wp.i18n.__;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var Disabled = wp.components.Disabled;
	var ServerSideRender = wp.serverSideRender;

	wp.blocks.registerBlockType( 'fernbrook/related', {
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
						{ title: __( 'Related pages', 'fernbrook-aeo' ) },
						el( TextControl, {
							label: __( 'Heading', 'fernbrook-aeo' ),
							value: a.heading,
							onChange: function ( v ) { props.setAttributes( { heading: v } ); },
						} ),
						el( 'p', { style: { color: '#555' } }, __( 'The links are chosen from the entity graph: a guide links to the programs it is about, a program links to the other programs and the guides.', 'fernbrook-aeo' ) )
					)
				),
				el( Disabled, null, el( ServerSideRender, { block: 'fernbrook/related', attributes: a } ) )
			);
		},
		save: function () { return null; },
	} );
} )( window.wp );
