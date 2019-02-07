import { ShortcodeAtts } from '../components/ShortcodeAtts';
import { CarouselArgs } from '../components/CarouselArgs';
import { DesignOptions } from '../components/DesignOptions';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls } = wp.editor;
const { Fragment } = wp.element;
const { ServerSideRender, Disabled, PanelBody, TextControl, SelectControl } = wp.components;

registerBlockType( 'vodi/section-movies-carousel-aside-header', {
    title: __('Movies Carousel Aside Header Block', 'vodi'),

    icon: 'format-video',

    category: 'vodi-blocks',

    edit: ( ( props ) => {
        const { attributes, setAttributes } = props;
        const { section_title, section_subtitle, header_posisition, section_background, section_style, action_text, action_link, shortcode_atts, carousel_args, design_options } = attributes;

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

        const onChangeHeaderPosition = newHeaderPosition => {
            setAttributes( { header_posisition: newHeaderPosition } );
        };

        const onChangeSectionBackground = newSectionBackground => {
            setAttributes( { section_background: newSectionBackground } );
        };

        const onChangeSectionStyle = newSectionStyle => {
            setAttributes( { section_style: newSectionStyle } );
        };

        const onChangeShortcodeAtts = newShortcodeAtts => {
            setAttributes( { shortcode_atts: { ...shortcode_atts, ...newShortcodeAtts } } );
        };

        const onChangeCarouselArgs = newCarouselArgs => {
            setAttributes( { carousel_args: { ...carousel_args, ...newCarouselArgs } } );
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
                        label={__('Header Position', 'vodi')}
                        value={ header_posisition }
                        options={ [
                            { label: __('Left', 'vodi'), value: '' },
                            { label: __('Right', 'vodi'), value: 'header-right' },
                        ] }
                        onChange={ onChangeHeaderPosition }
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
                        title={__('Movies Attributes', 'vodi')}
                        initialOpen={ true }
                    >
                        <ShortcodeAtts
                            postType = 'movie'
                            catTaxonomy = 'movie_genre'
                            hideFields = { ['columns'] }
                            attributes = { { ...shortcode_atts } }
                            updateShortcodeAtts = { onChangeShortcodeAtts }
                        />
                    </PanelBody>
                     <PanelBody
                        title={__('Carousel Args', 'vodi')}
                        initialOpen={ true }
                    >
                        <CarouselArgs
                            attributes = { { ...carousel_args } }
                            updateCarouselArgs = { onChangeCarouselArgs }
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
                        block="vodi/section-movies-carousel-aside-header"
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