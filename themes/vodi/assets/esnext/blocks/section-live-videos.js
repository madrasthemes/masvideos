import { Repeater } from '../components/Repeater';
import { ShortcodeAtts } from '../components/ShortcodeAtts';
import { DesignOptions } from '../components/DesignOptions';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls } = wp.editor;
const { Fragment } = wp.element;
const { ServerSideRender, Disabled, PanelBody, TextControl, SelectControl } = wp.components;

registerBlockType( 'vodi/section-live-videos', {
    title: __('Section Live Video Block', 'vodi'),

    icon: 'format-video',

    category: 'vodi-blocks',

    edit: ( ( props ) => {
        const { attributes, setAttributes } = props;
        const { live_videos_title, footer_action_text, footer_action_link, shortcode_atts, design_options } = attributes;

        const onChangeSectionTitle = newSectionTitle => {
            setAttributes( { live_videos_title: newSectionTitle } );
        };

        const onChangeFooterActionText = newFooterActionText => {
            setAttributes( { footer_action_text: newFooterActionText } );
        };

        const onChangeFooterActionLink = newFooterActionLink => {
            setAttributes( { footer_action_link: newFooterActionLink } );
        };

        const onChangeShortcodeAtts = newShortcodeAtts => {
            setAttributes( { shortcode_atts: { ...shortcode_atts, ...newShortcodeAtts } } );
        };

        const onChangeDesignOptions = newDesignOptions => {
            setAttributes( { design_options: { ...design_options, ...newDesignOptions } } );
        };

        return (
            <Fragment>
                <InspectorControls>
                    <TextControl
                        label={__('Live Title', 'vodi')}
                        value={ live_videos_title }
                        onChange={ onChangeSectionTitle }
                    />
                    <TextControl
                        label={__('Footer Action Text', 'vodi')}
                        value={ footer_action_text }
                        onChange={ onChangeFooterActionText }
                    />
                    <TextControl
                        label={__('Footer Action Link', 'vodi')}
                        value={ footer_action_link }
                        onChange={ onChangeFooterActionLink }
                    />
                    <PanelBody
                        title={__('Videos Attributes', 'vodi')}
                        initialOpen={ true }
                    >
                        <ShortcodeAtts
                            postType = 'video'
                            catTaxonomy = 'video_cat'
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
                        block="vodi/section-live-videos"
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
