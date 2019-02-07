import { ShortcodeAtts } from '../components/ShortcodeAtts';
import { CarouselArgs } from '../components/CarouselArgs';
import { DesignOptions } from '../components/DesignOptions';
import { Repeater } from '../components/Repeater';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, MediaUpload } = wp.editor;
const { Fragment } = wp.element;
const { ServerSideRender, Disabled, PanelBody, TextControl, SelectControl, Button } = wp.components;

registerBlockType( 'vodi/featured-movies-carousel', {
    title: __('Featured Movies Carousel', 'vodi'),

    icon: 'format-video',

    category: 'vodi-blocks',

    edit: ( ( props ) => {
        const { attributes, setAttributes } = props;
        const { feature_movie_pre_title, feature_movie_title, feature_movie_subtitle, section_nav_links, section_background, section_style, bg_image, shortcode_atts, carousel_args, design_options } = attributes;

        const onChangeFeatureMoviePreTitle = newFeatureMoviePreTitle => {
            setAttributes( { feature_movie_pre_title: newFeatureMoviePreTitle } );
        };

        const onChangeFeatureMovieTitle = newFeatureMovieTitle => {
            setAttributes( { feature_movie_title: newFeatureMovieTitle } );
        };

        const onChangeFeatureMovieSubtitle = newFeatureMovieSubtitle => {
            setAttributes( { feature_movie_subtitle: newFeatureMovieSubtitle } );
        };

        const onChangeSectionBackground = newSectionBackground => {
            setAttributes( { section_background: newSectionBackground } );
        };

        const onChangeSectionStyle = newSectionStyle => {
            setAttributes( { section_style: newSectionStyle } );
        };
        const onChangeBgImage = media => {
            setAttributes( { bg_image: media.id } );
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
                        label={__('Feature Movie PreTitle', 'vodi')}
                        value={ feature_movie_pre_title }
                        onChange={ onChangeFeatureMoviePreTitle }
                    />
                    <TextControl
                        label={__('Feature Movie Title', 'vodi')}
                        value={ feature_movie_title }
                        onChange={ onChangeFeatureMovieTitle }
                    />
                    <TextControl
                        label={__('Feature Movie Subtitle', 'vodi')}
                        value={ feature_movie_subtitle }
                        onChange={ onChangeFeatureMovieSubtitle }
                    />
                    <MediaUpload
                        onSelect={onChangeBgImage}
                        type="image"
                        value={ bg_image }
                        render={ ({ open }) => getImageButton(open) }
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
                        block="vodi/featured-movies-carousel"
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