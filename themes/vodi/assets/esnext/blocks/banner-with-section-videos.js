import { Repeater } from '../components/Repeater';
import { ShortcodeAtts } from '../components/ShortcodeAtts';
import { DesignOptions } from '../components/DesignOptions';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, MediaUpload } = wp.editor;
const { Fragment } = wp.element;
const { ServerSideRender, Disabled, PanelBody, TextControl, SelectControl, Button } = wp.components;

registerBlockType( 'vodi/banner-with-section-videos', {
    title: __('Banner With Videos Block', 'vodi'),

    icon: 'format-video',

    category: 'vodi-blocks',

    edit: ( ( props ) => {
        const { attributes, setAttributes } = props;
        const { section_title, section_nav_links, section_background, section_style, footer_action_text, footer_action_link, image, shortcode_atts, design_options } = attributes;

        const onChangeSectionTitle = newSectionTitle => {
            setAttributes( { section_title: newSectionTitle } );
        };

        const onChangeSectionBackground = newSectionBackground => {
            setAttributes( { section_background: newSectionBackground } );
        };

        const onChangeSectionStyle = newSectionStyle => {
            setAttributes( { section_style: newSectionStyle } );
        };

        const onChangeFooterActionText = newFooterActionText => {
            setAttributes( { footer_action_text: newFooterActionText } );
        };

        const onChangeFooterActionLink = newFooterActionLink => {
            setAttributes( { footer_action_link: newFooterActionLink } );
        };

        const onChangeImage = media => {
            setAttributes( { image: media.id } );
        };

        const onChangeShortcodeAtts = newShortcodeAtts => {
            setAttributes( { shortcode_atts: { ...shortcode_atts, ...newShortcodeAtts } } );
        };

        const onChangeSectionNavLinks = newSectionNavLinks => {
            setAttributes( { section_nav_links: [...newSectionNavLinks] } );
        };

        const onChangeSectionNavLinksText = (newSectionNavLinksText, index) => {
            var section_nav_links_updated = [ ...section_nav_links ];
            section_nav_links_updated[index].title = newSectionNavLinksText;
            setAttributes( { section_nav_links: [...section_nav_links_updated] } );
        };

        const onChangeSectionNavLinksLink = (newSectionNavLinksLink, index) => {
            var section_nav_links_updated = [ ...section_nav_links ];
            section_nav_links_updated[index].link = newSectionNavLinksLink;
            setAttributes( { section_nav_links: [...section_nav_links_updated] } );
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

        return (
            <Fragment>
                <InspectorControls>
                    <TextControl
                        label={__('Section Title', 'vodi')}
                        value={ section_title }
                        onChange={ onChangeSectionTitle }
                    />
                    <Repeater
                        title={__('Nav Links', 'vodi')}
                        values={ section_nav_links }
                        defaultValues={ { title: '', link: '' } }
                        updateValues={ onChangeSectionNavLinks }
                    >
                        <TextControl
                            label={__('Action Text', 'vodi')}
                            name='title'
                            valuekey='value'
                            value=''
                            trigger_method_name='onChange'
                            onChange={ onChangeSectionNavLinksText }
                        />
                        <TextControl
                            label={__('Action Link', 'vodi')}
                            name='link'
                            valuekey='value'
                            value=''
                            trigger_method_name='onChange'
                            onChange={ onChangeSectionNavLinksLink }
                        />
                    </Repeater>
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
                    <MediaUpload
                        onSelect={onChangeImage}
                        type="image"
                        value={ image }
                        render={ ({ open }) => getImageButton(open) }
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
                        block="vodi/banner-with-section-videos"
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