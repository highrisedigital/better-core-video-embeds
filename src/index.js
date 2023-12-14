import { addFilter } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, PanelRow } from '@wordpress/components';
import { createHigherOrderComponent } from '@wordpress/compose';

import { Image, MediaToolbar } from '@10up/block-components';

addFilter(
	'blocks.registerBlockType',
	'hd/better-core-video-embeds',
	( settings, name ) => {
		
		if ( name !== 'core/embed' ) {
			return settings;
		}

		return {
			...settings,
			attributes: {
				...settings.attributes,
				thumbnailId: {
					type: 'number',
					default: 0,
				}
			},
		};

	}
);

function Edit(props) {

	const { attributes, setAttributes } = props;
    const { thumbnailId, providerNameSlug } = attributes;

    function handleImageSelect( image ) {
        setAttributes({thumbnailId: image.id});
    }

	function handleImageRemove() {
        setAttributes({thumbnailId: null})
    }

	return (
		// only add the video thumnail option if the video is from youtube, vimeo, or dailymotion.
		( 'youtube' !== providerNameSlug && 'vimeo' !== providerNameSlug && 'dailymotion' !== providerNameSlug ) ? null : (
			<InspectorControls>
				<PanelBody title={__("Video Thumbnail")}>
					<PanelRow>
						<Image
							id={thumbnailId}
							className="bcve-thumbnail"
							size="medium"
							onSelect={handleImageSelect}
							allowedTypes={['image']}
							canEditImage={true}
							labels={{
								title: '',
								instructions: 'Select or upload a custom video thumbnail.'
							}}
						/>
					</PanelRow>
					{ null !== thumbnailId && (
						<PanelRow>
							<MediaToolbar
								isOptional={ true }
								id={ thumbnailId }
								onSelect={ handleImageSelect }
								onRemove={ handleImageRemove }
							/>
							
						</PanelRow>
					)}
					<PanelRow>
						<p>
							{__( "Overwrites the default thumbnail set at the provider.", "better-core-video-embeds" ) }
						</p>
					</PanelRow>
				</PanelBody>
			</InspectorControls>
		)
	)
}

/**
 * Add the edit component to the block.
 * This is the component that will be rendered in the editor.
 * It will be rendered after the original block edit component.
 *
 * @param {function} BlockEdit Original component
 * @returns {function} Wrapped component
 *
 * @see https://developer.wordpress.org/block-editor/developers/filters/block-filters/#editor-blockedit
 */
addFilter(
	"editor.BlockEdit",
	"hd/better-core-video-embeds",
	createHigherOrderComponent((BlockEdit) => {
		return (props) => {
			if (props.name !== "core/embed") {
				return <BlockEdit {...props} />;
			}

			return (
				<>
					<BlockEdit {...props} />
					<Edit {...props} />
				</>
			);
		};
	})
);