( function ( blocks, element, blockEditor ) {
	var el         = element.createElement;
	var InnerBlocks = blockEditor.InnerBlocks;
	var useBlockProps = blockEditor.useBlockProps;

	blocks.registerBlockType( 'sinxelo/callout', {
		edit: function () {
			var blockProps = useBlockProps( { className: 'callout-box' } );
			return el( 'div', blockProps,
				el( InnerBlocks, null )
			);
		},

		save: function () {
			var blockProps = useBlockProps.save( { className: 'callout-box' } );
			return el( 'div', blockProps,
				el( InnerBlocks.Content, null )
			);
		},
	} );
} )( window.wp.blocks, window.wp.element, window.wp.blockEditor );
