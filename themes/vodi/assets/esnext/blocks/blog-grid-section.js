import { Repeater } from '../components/Repeater';
import { PostAtts } from '../components/PostAtts';
import { DesignOptions } from '../components/DesignOptions';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls } = wp.editor;
const { Fragment } = wp.element;
const { ServerSideRender, Disabled, PanelBody, TextControl, CheckboxControl, SelectControl, RangeControl } = wp.components;

registerBlockType( 'vodi/blog-grid-section', {
    title: __('Vodi Blog Grid Section', 'vodi'),

    icon: 'grid-view',

    category: 'vodi-blocks',

    edit: ( ( props ) => {
        const { attributes, setAttributes } = props;
        const { section_title, section_nav_links, style, hide_excerpt, columns, post_atts, design_options } = attributes;

        const onChangeSectionTitle = newSectionTitle => {
            setAttributes( { section_title: newSectionTitle } );
        };

        const onChangeStyle = newStyle => {
            setAttributes( { style: newStyle } );
        };

        const onChangeHideExcerpt = newHideExcerpt => {
            setAttributes( { hide_excerpt: newHideExcerpt } );
        };

        const onChangeColumns = newColumns => {
            setAttributes( { columns: newColumns } );
        };

        const onChangePostAtts = newPostAtts => {
            setAttributes( { post_atts: { ...post_atts, ...newPostAtts } } );
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
                        label={__('Style', 'vodi')}
                        value={ style }
                        options={ [
                            { label: __('Style 1', 'vodi'), value: 'style-1' },
                            { label: __('Style 2', 'vodi'), value: 'style-2' },
                            { label: __('Style 3', 'vodi'), value: 'style-3' },
                        ] }
                        onChange={ onChangeStyle }
                    />
                    { ( style != 'style-3' ) ? (
                    <CheckboxControl
                        label={__('Hide Excerpt', 'vodi')}
                        help={__('Check to hide excerpt.', 'vodi')}
                        checked={ hide_excerpt }
                        onChange={ onChangeHideExcerpt }
                    />
                    ) : '' }
                    <RangeControl
                        label={__('Columns', 'vodi')}
                        value={ columns }
                        onChange={ onChangeColumns }
                        min={ 1 }
                        max={ 6 }
                    />
                    <PostAtts
                        attributes = { { ...post_atts } }
                        updatePostAtts = { onChangePostAtts }
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
                    <ServerSideRender
                        block="vodi/blog-grid-section"
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