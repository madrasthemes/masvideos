import { Repeater } from '../components/Repeater';
import { CategoryArgs } from '../components/CategoryArgs';
import { DesignOptions } from '../components/DesignOptions';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls } = wp.editor;
const { Fragment } = wp.element;
const { ServerSideRender, Disabled, PanelBody, TextControl, CheckboxControl, RangeControl, SelectControl } = wp.components;

registerBlockType( 'vodi/section-event-category-list', {
    title: __('Vodi Event Categories Block', 'vodi'),

    icon: 'playlist-video',

    category: 'vodi-blocks',

    edit: ( ( props ) => {
        const { attributes, setAttributes } = props;
        const { section_title, section_nav_links, columns, category_args, design_options } = attributes;

        const onChangeSectionTitle = newSectionTitle => {
            setAttributes( { section_title: newSectionTitle } );
        };

        const onChangeSectionNavLinks = newSectionNavLinks => {
            setAttributes( { section_nav_links: [...newSectionNavLinks] } );
        };

        const onChangeSectionNavLinksText = (newSectionNavLinksText, index) => {
            section_nav_links[index].title = newSectionNavLinksText;
            setAttributes( { section_nav_links: [...section_nav_links] } );
        };

        const onChangeSectionNavLinksLink = (newSectionNavLinksLink, index) => {
            section_nav_links[index].link = newSectionNavLinksLink;
            setAttributes( { section_nav_links: [...section_nav_links] } );
        };

        const onChangeColumns = newColumns => {
            setAttributes( { columns: newColumns } );
        };

        const onChangeCategoryArgs = newCategoryArgs => {
            setAttributes( { category_args: { ...category_args, ...newCategoryArgs } } );
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
                    <RangeControl
                        label={__('Columns', 'vodi')}
                        value={ columns }
                        onChange={ onChangeColumns }
                        min={ 3 }
                        max={ 5 }
                    />
                    <CategoryArgs
                        postType = 'video'
                        catTaxonomy = 'video_cat'
                        attributes = { { ...category_args } }
                        updateCategoryArgs = { onChangeCategoryArgs }
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
                        block="vodi/section-event-category-list"
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