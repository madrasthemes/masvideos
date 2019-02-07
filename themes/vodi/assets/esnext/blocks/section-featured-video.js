import { PostSelector } from '../components/PostSelector';
import { DesignOptions } from '../components/DesignOptions';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, MediaUpload } = wp.editor;
const { Fragment } = wp.element;
const { ServerSideRender, Disabled, PanelBody, TextControl, SelectControl, Button } = wp.components;

registerBlockType( 'vodi/section-featured-video', {
    title: __('Featured Video', 'vodi'),

    icon: 'format-video',

    category: 'vodi-blocks',

    edit: ( ( props ) => {
        const { attributes, setAttributes } = props;
        const { feature_video_action_icon, video_id, image, bg_image, design_options } = attributes;

        const onChangeFeatureVideoActionIcon = newFeatureVideoActionIcon => {
            setAttributes( { feature_video_action_icon: newFeatureVideoActionIcon } );
        };

        const onChangeIds = newIds=> {
            setAttributes( { video_id: newIds.join(',') } );
        };

        const onChangeImage = media => {
            setAttributes( { image: media.id } );
        };

        const onChangeBgImage = media => {
            setAttributes( { bg_image: media.id } );
        };

        const onChangeDesignOptions = newDesignOptions => {
            setAttributes( { design_options: { ...design_options, ...newDesignOptions } } );
        };


        const getImageButton = (openEvent) => {
            return (
                <div className="button-container">
                    <Button 
                        onClick={ openEvent }
                        className="button button-large"
                    >
                        {__('Pick an Image', 'vodi')}
                    </Button>
                </div>
            );
        };

        const getBgImageButton = (openEvent) => {
            return (
                <div className="button-container">
                    <Button 
                        onClick={ openEvent }
                        className="button button-large"
                    >
                        {__('Pick an Background Image', 'vodi')}
                    </Button>
                </div>
            );
        };

        return (
            <Fragment>
                <InspectorControls>
                    <PostSelector
                        postType = 'video'
                        selectSingle = { true }
                        selectedPostIds={ video_id ? video_id.split(',').map(Number) : [] }
                        updateSelectedPostIds={ onChangeIds }
                    />
                    <TextControl
                        label={__('Feature Video Action Icon', 'vodi')}
                        value={ feature_video_action_icon }
                        onChange={ onChangeFeatureVideoActionIcon }
                    />
                    <MediaUpload
                        onSelect={onChangeImage}
                        type="image"
                        value={ image }
                        render={ ({ open }) => getImageButton(open) }
                    />
                    <MediaUpload
                        onSelect={onChangeBgImage}
                        type="image"
                        value={ bg_image }
                        render={ ({ open }) => getImageButton(open) }
                    />
                    <PanelBody
                        title={__('Design Options', 'vodi')}
                        initialOpen={ false }
                    >
                        <DesignOptions
                            attributes = { { ...design_options } }
                            updateDesignOptions = { onChangeDesignOptions }
                        />
                    </PanelBody>
                </InspectorControls>
                <Disabled>
                    { video_id ? (
                    <ServerSideRender
                        block="vodi/section-featured-video"
                        attributes={ attributes }
                    />
                    ) : __('Choose a video', 'vodi') }
                </Disabled>
            </Fragment>
        );
    } ),

    save() {
        // Rendering in PHP
        return null;
    },
} );