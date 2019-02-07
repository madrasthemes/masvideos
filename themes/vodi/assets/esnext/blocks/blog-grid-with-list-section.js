import { Repeater } from '../components/Repeater';
import { PostAtts } from '../components/PostAtts';
import { DesignOptions } from '../components/DesignOptions';
import { PostSelector } from '../components/PostSelector';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls } = wp.editor;
const { Fragment } = wp.element;
const { ServerSideRender, Disabled, PanelBody, TextControl, CheckboxControl, SelectControl } = wp.components;

registerBlockType( 'vodi/blog-grid-with-list-section', {
    title: __('Vodi Blog Grid with List Section', 'vodi'),

    icon: 'grid-view',

    category: 'vodi-blocks',

    edit: ( ( props ) => {
        const { attributes, setAttributes } = props;
        const { section_title, section_nav_links, hide_excerpt_1,hide_excerpt_2, post_atts_2, ids, design_options } = attributes;

        const onChangeSectionTitle = newSectionTitle => {
            setAttributes( { section_title: newSectionTitle } );
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

        const onChangeHideExcerpt1 = newHideExcerpt1 => {
            setAttributes( { hide_excerpt_1: newHideExcerpt1 } );
        };

        const onChangeHideExcerpt2 = newHideExcerpt2 => {
            setAttributes( { hide_excerpt_2: newHideExcerpt2 } );
        };


        const onChangePostAtts2 = newPostAtts2 => {
            setAttributes( { post_atts_2: { ...post_atts_2, ...newPostAtts2 } } );
        };

        const onChangeIds = newIds=> {
            setAttributes( { ids: newIds.join(',') } );
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
                   <PanelBody
                        title={__('Grid View Attributes', 'vodi')}
                        initialOpen={ false }
                    >
                    <PostSelector
                        postType = 'post'
                        selectSingle = { true }
                        selectedPostIds={ ids ? ids.split(',').map(Number) : [] }
                        updateSelectedPostIds={ onChangeIds }
                    />
                    <CheckboxControl
                        label={__('Hide Excerpt', 'vodi')}
                        help={__('Check to hide excerpt.', 'vodi')}
                        checked={ hide_excerpt_1 }
                        onChange={ onChangeHideExcerpt1 }
                    />
                    </PanelBody>
                    
                    <PanelBody
                        title={__('List View Attributes', 'vodi')}
                        initialOpen={ false }
                    >
                    <PostAtts
                        attributes = { { ...post_atts_2 } }
                        updatePostAtts = { onChangePostAtts2 }
                    />
                    <CheckboxControl
                        label={__('Hide Excerpt', 'vodi')}
                        help={__('Check to hide excerpt.', 'vodi')}
                        checked={ hide_excerpt_2 }
                        onChange={ onChangeHideExcerpt2 }
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
                        block="vodi/blog-grid-with-list-section"
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