import { ShortcodeAtts } from '../components/ShortcodeAtts';
import { DesignOptions } from '../components/DesignOptions';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls } = wp.editor;
const { Fragment } = wp.element;
const { ServerSideRender, Disabled, PanelBody, TextControl, SelectControl } = wp.components;

registerBlockType( 'vodi/video-section-aside-header', {
    title: __('Videos Section Aside Header Block', 'vodi'),

    icon: 'format-video',

    category: 'vodi-blocks',

    edit: ( ( props ) => {
        const { attributes, setAttributes } = props;
        const { section_title, section_subtitle, action_text, action_link, section_background, section_style, shortcode_atts_1, shortcode_atts_2, design_options } = attributes;

        const onChangeSectionTitle = newSectionTitle => {
            setAttributes( { section_title: newSectionTitle } );
        };

        const onChangeSectionSubtitle = newSectionSubtitle => {
            setAttributes( { section_subtitle: newSectionSubtitle } );
        };
        
        const onChangeActionText = newActionText => {
            setAttributes( { action_text: newActionText } );
        };

        const onChangeActionLink = newActionLink => {
            setAttributes( { action_link: newActionLink } );
        };

        const onChangeSectionBackground = newSectionBackground => {
            setAttributes( { section_background: newSectionBackground } );
        };

        const onChangeSectionStyle = newSectionStyle => {
            setAttributes( { section_style: newSectionStyle } );
        };

        const onChangeShortcodeAtts1 = newShortcodeAtts1 => {
            setAttributes( { shortcode_atts_1: { ...shortcode_atts_1, ...newShortcodeAtts1 } } );
        };

        const onChangeShortcodeAtts2 = newShortcodeAtts2 => {
            setAttributes( { shortcode_atts_2: { ...shortcode_atts_2, ...newShortcodeAtts2 } } );
        };

        const onChangeDesignOptions = newDesignOptions => {
            setAttributes( { design_options: { ...design_options, ...newDesignOptions } } );
        };

        return (
            <Fragment>
                <InspectorControls>
                    <TextControl
                        label={__('Section Title', 'vodi')}
                        value={ section_title }
                        onChange={ onChangeSectionTitle }
                    />
                    <TextControl
                        label={__('Section Subtitle', 'vodi')}
                        value={ section_subtitle }
                        onChange={ onChangeSectionSubtitle }
                    />
                    <TextControl
                        label={__('Action Text', 'vodi')}
                        value={ action_text }
                        onChange={ onChangeActionText }
                    />
                    <TextControl
                        label={__('Action Link', 'vodi')}
                        value={ action_link }
                        onChange={ onChangeActionLink }
                    />
                    <SelectControl
                        label={__('Background Color', 'vodi')}
                        value={ section_background }
                        options={ [
                            { label: __('Default', 'vodi'), value: '' },
                            { label: __('Dark', 'vodi'), value: 'dark' },
                            { label: __('More Dark', 'vodi'), value: 'dark more-dark' },
                            { label: __('Less Dark', 'vodi'), value: 'dark less-dark' },
                            { label: __('Light', 'vodi'), value: 'light' },
                            { label: __('More Light', 'vodi'), value: 'light more-light' },
                        ] }
                        onChange={ onChangeSectionBackground }
                    />
                    <SelectControl
                        label={__('Style', 'vodi')}
                        value={ section_style }
                        options={ [
                            { label: __('Style 1', 'vodi'), value: '' },
                            { label: __('Style 2', 'vodi'), value: 'style-2' },
                        ] }
                        onChange={ onChangeSectionStyle }
                    />
                    <PanelBody
                        title={__('Videos Attributes 1', 'vodi')}
                        initialOpen={ true }
                    >
                        <ShortcodeAtts
                            postType = 'video'
                            catTaxonomy = 'video_cat'
                            attributes = { { ...shortcode_atts_1 } }
                            updateShortcodeAtts = { onChangeShortcodeAtts1 }
                        />
                    </PanelBody>
                     <PanelBody
                        title={__('Videos Attributes 2', 'vodi')}
                        initialOpen={ true }
                    >
                        <ShortcodeAtts
                            postType = 'video'
                            catTaxonomy = 'video_cat'
                            attributes = { { ...shortcode_atts_2 } }
                            updateShortcodeAtts = { onChangeShortcodeAtts2 }
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
                        block="vodi/video-section-aside-header"
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