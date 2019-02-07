import { ShortcodeAtts } from '../components/ShortcodeAtts';
import { DesignOptions } from '../components/DesignOptions';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, MediaUpload } = wp.editor;
const { Fragment } = wp.element;
const { ServerSideRender, Disabled, PanelBody, TextControl, Button } = wp.components;

registerBlockType( 'vodi/section-featured-tv-show', {
    title: __('Featured TV Show', 'vodi'),

    icon: 'format-video',

    category: 'vodi-blocks',

    edit: ( ( props ) => {
        const { attributes, setAttributes } = props;
        const { feature_tv_show_pre_title, feature_tv_show_title, feature_tv_show_subtitle, bg_image, shortcode_atts, design_options } = attributes;

        const onChangeFeatureTvShowPreTitle = newFeatureTvShowPreTitle => {
            setAttributes( { feature_tv_show_pre_title: newFeatureTvShowPreTitle } );
        };

        const onChangeFeatureTvShowTitle = newFeatureTvShowTitle => {
            setAttributes( { feature_tv_show_title: newFeatureTvShowTitle } );
        };
        
        const onChangeFeatureTvShowSubtitle = newFeatureTvShowSubtitle => {
            setAttributes( { feature_tv_show_subtitle: newFeatureTvShowSubtitle } );
        };

        const onChangeBgImage = media => {
            setAttributes( { bg_image: media.id } );
        };

        const onChangeShortcodeAtts = newShortcodeAtts => {
            setAttributes( { shortcode_atts: { ...shortcode_atts, ...newShortcodeAtts } } );
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
                        {__('Pick an Background Image', 'vodi')}
                    </Button>
                </div>
            );
        };

        return (
            <Fragment>
                <InspectorControls>
                    <TextControl
                        label={__('Feature Tv Show PreTitle', 'vodi')}
                        value={ feature_tv_show_pre_title }
                        onChange={ onChangeFeatureTvShowPreTitle }
                    />
                    <TextControl
                        label={__('Feature Tv Show Title', 'vodi')}
                        value={ feature_tv_show_title }
                        onChange={ onChangeFeatureTvShowTitle }
                    />
                    <TextControl
                        label={__('Feature Tv Show Subtitle', 'vodi')}
                        value={ feature_tv_show_subtitle }
                        onChange={ onChangeFeatureTvShowSubtitle }
                    />
                    <MediaUpload
                        onSelect={onChangeBgImage}
                        type="image"
                        value={ bg_image }
                        render={ ({ open }) => getImageButton(open) }
                    />
                    <PanelBody
                        title={__('TV Attributes 1', 'vodi')}
                        initialOpen={ true }
                    >
                        <ShortcodeAtts
                            postType = 'video'
                            catTaxonomy = 'video_genre'
                            attributes = { { ...shortcode_atts } }
                            updateShortcodeAtts = { onChangeShortcodeAtts }
                        />
                    </PanelBody>
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
                    <ServerSideRender
                        block="vodi/section-featured-tv-show"
                        attributes={ attributes }
                    />
                </Disabled>
            </Fragment>
        );
    } ),

    save() {
        // Rendering in PHP
        return null;
    },
} );