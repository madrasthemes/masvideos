import { Repeater } from '../components/Repeater';
import { ShortcodeAtts } from '../components/ShortcodeAtts';
import { DesignOptions } from '../components/DesignOptions';
import { PostSelector } from '../components/PostSelector';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls } = wp.editor;
const { Fragment } = wp.element;
const { ServerSideRender, Disabled, PanelBody, TextControl, SelectControl } = wp.components;

registerBlockType( 'vodi/movies-list', {
    title: __('Movies List', 'vodi'),

    icon: 'format-video',

    category: 'vodi-blocks',

    edit: ( ( props ) => {
        const { attributes, setAttributes } = props;
        const { section_title_1, section_title_2, section_nav_links_1, section_nav_links_2, shortcode_atts_1, shortcode_atts_2, featured_movie_id, design_options } = attributes;

        const onChangeSectionTitle1 = newSectionTitle1 => {
            setAttributes( { section_title_1: newSectionTitle1 } );
        };

        const onChangeSectionTitle2 = newSectionTitle2 => {
            setAttributes( { section_title_2: newSectionTitle2 } );
        };

        const onChangeSectionNavLinks1 = newSectionNavLinks1 => {
            setAttributes( { section_nav_links_1: [...newSectionNavLinks1] } );
        };

        const onChangeSectionNavLinks2 = newSectionNavLinks2 => {
            setAttributes( { section_nav_links_2: [...newSectionNavLinks2] } );
        };

        const onChangeSectionNavLinksText1 = (newSectionNavLinksText1, index) => {
            var section_nav_links_1_updated = [ ...section_nav_links_1 ];
            section_nav_links_1_updated[index].title = newSectionNavLinksText1;
            setAttributes( { section_nav_links_1: [...section_nav_links_1_updated] } );
        };

        const onChangeSectionNavLinksLink1 = (newSectionNavLinksLink1, index) => {
            var section_nav_links_1_updated = [ ...section_nav_links_1 ];
            section_nav_links_1_updated[index].link = newSectionNavLinksLink1;
            setAttributes( { section_nav_links_1: [...section_nav_links_1_updated] } );
        };

        const onChangeSectionNavLinksText2 = (newSectionNavLinksText2, index) => {
            var section_nav_links_2_updated = [ ...section_nav_links_2 ];
            section_nav_links_2_updated[index].title = newSectionNavLinksText2;
            setAttributes( { section_nav_links_2: [...section_nav_links_2_updated] } );
        };

        const onChangeSectionNavLinksLink2 = (newSectionNavLinksLink2, index) => {
            var section_nav_links_2_updated = [ ...section_nav_links_2 ];
            section_nav_links_2_updated[index].link = newSectionNavLinksLink2;
            setAttributes( { section_nav_links_2: [...section_nav_links_2_updated] } );
        };

        const onChangeShortcodeAtts1 = newShortcodeAtts1 => {
            setAttributes( { shortcode_atts_1: { ...shortcode_atts_1, ...newShortcodeAtts1 } } );
        };

        const onChangeShortcodeAtts2 = newShortcodeAtts2 => {
            setAttributes( { shortcode_atts_2: { ...shortcode_atts_2, ...newShortcodeAtts2 } } );
        };

        const onChangeIds = newIds=> {
            setAttributes( { featured_movie_id: newIds.join(',') } );
        };

        const onChangeDesignOptions = newDesignOptions => {
            setAttributes( { design_options: { ...design_options, ...newDesignOptions } } );
        };

        return (
            <Fragment>
                <InspectorControls>
                <PanelBody
                    title={__('Movies List', 'vodi')}
                    initialOpen={ true }
                >
                    <TextControl
                        label={__('Section Title', 'vodi')}
                        value={ section_title_1 }
                        onChange={ onChangeSectionTitle1 }
                    />
                    <Repeater
                        title={__('Nav Links', 'vodi')}
                        values={ section_nav_links_1 }
                        defaultValues={ { title: '', link: '' } }
                        updateValues={ onChangeSectionNavLinks1 }
                    >
                        <TextControl
                            label={__('Action Text', 'vodi')}
                            name='title'
                            valuekey='value'
                            value=''
                            trigger_method_name='onChange'
                            onChange={ onChangeSectionNavLinksText1 }
                        />
                        <TextControl
                            label={__('Action Link', 'vodi')}
                            name='link'
                            valuekey='value'
                            value=''
                            trigger_method_name='onChange'
                            onChange={ onChangeSectionNavLinksLink1 }
                        />
                    </Repeater>
                    <PanelBody
                        title={__('Movies List Attribute 1', 'vodi')}
                        initialOpen={ true }
                    >
                        <ShortcodeAtts
                            postType = 'movie'
                            catTaxonomy = 'movie_cat'
                            attributes = { { ...shortcode_atts_1 } }
                            updateShortcodeAtts = { onChangeShortcodeAtts1 }
                        />
                    </PanelBody>
                </PanelBody>
                <PanelBody
                    title={__('Featured Movie with Movies List', 'vodi')}
                    initialOpen={ true }
                    >
                    <TextControl
                        label={__('Section Title', 'vodi')}
                        value={ section_title_2 }
                        onChange={ onChangeSectionTitle2 }
                    />
                    <Repeater
                        title={__('Nav Links', 'vodi')}
                        values={ section_nav_links_2 }
                        defaultValues={ { title: '', link: '' } }
                        updateValues={ onChangeSectionNavLinks2 }
                    >
                        <TextControl
                            label={__('Action Text', 'vodi')}
                            name='title'
                            valuekey='value'
                            value=''
                            trigger_method_name='onChange'
                            onChange={ onChangeSectionNavLinksText2 }
                        />
                        <TextControl
                            label={__('Action Link', 'vodi')}
                            name='link'
                            valuekey='value'
                            value=''
                            trigger_method_name='onChange'
                            onChange={ onChangeSectionNavLinksLink2 }
                        />
                    </Repeater>
                    <PostSelector
                        label={__('Enter Featured Movie Id', 'vodi')}
                        postType = 'movie'
                        selectSingle = { true }
                        selectedPostIds={ featured_movie_id ? featured_movie_id.split(',').map(Number) : [] }
                        updateSelectedPostIds={ onChangeIds }
                    />
                    <PanelBody
                        title={__('Movies List Attribute 2', 'vodi')}
                        initialOpen={ true }
                    >
                    <ShortcodeAtts
                        postType = 'movie'
                        catTaxonomy = 'movie_cat'
                        attributes = { { ...shortcode_atts_2 } }
                        updateShortcodeAtts = { onChangeShortcodeAtts2 }
                    />
                    </PanelBody>
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
                        block="vodi/movies-list"
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
