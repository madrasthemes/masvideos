import { ShortcodeAtts } from '../components/ShortcodeAtts';
import { PostSelector } from '../components/PostSelector';
import { DesignOptions } from '../components/DesignOptions';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, MediaUpload } = wp.editor;
const { Fragment } = wp.element;
const { ServerSideRender, Disabled, PanelBody, TextControl, SelectControl, Button } = wp.components;

registerBlockType( 'vodi/single-featured-movie', {
    title: __('Single Movie', 'vodi'),

    icon: 'format-video',

    category: 'vodi-blocks',

    edit: ( ( props ) => {
        const { attributes, setAttributes } = props;
        const { movie_action_icon, action_text, bg_image, movie_id, design_options  } = attributes;

        const onChangeFeatureVideoActionIcon = newFeatureVideoActionIcon => {
            setAttributes( { movie_action_icon: newFeatureVideoActionIcon } );
        };

        const onChangeActionText = newActionText => {
            setAttributes( { action_text: newActionText } );
        };

        const onChangeBgImage = media => {
            setAttributes( { bg_image: media.id } );
        };

        const onChangeIds = newIds=> {
            setAttributes( { movie_id: newIds.join(',') } );
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
                        {__('Pick an image', 'vodi')}
                    </Button>
                </div>
            );
        };

        return (
            <Fragment>
                <InspectorControls>


                    <TextControl
                        label={__('Action Icon', 'vodi')}
                        value={ movie_action_icon }
                        onChange={ onChangeFeatureVideoActionIcon }
                    />

                    <TextControl
                        label={__('Action Text', 'vodi')}
                        value={ action_text }
                        onChange={ onChangeActionText }
                    />

                    <MediaUpload
                        onSelect={onChangeBgImage}
                        type="image"
                        value={ bg_image }
                        render={ ({ open }) => getImageButton(open) }
                    />

                    <PostSelector
                        postType = 'movie'
                        selectSingle = { true }
                        selectedPostIds={ movie_id ? movie_id.split(',').map(Number) : [] }
                        updateSelectedPostIds={ onChangeIds }
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
                    { movie_id ? (
                    <ServerSideRender
                        block="vodi/single-featured-movie"
                        attributes={ attributes }
                    />
                    ) : __('Choose a movie', 'vodi') }
                </Disabled>
            </Fragment>
        );
    } ),

    save() {
        // Rendering in PHP
        return null;
    },
} );
