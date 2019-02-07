import { Repeater } from '../components/Repeater';
import { PostAtts } from '../components/PostAtts';
import { DesignOptions } from '../components/DesignOptions';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls } = wp.editor;
const { Fragment } = wp.element;
const { ServerSideRender, Disabled, PanelBody, TextControl, SelectControl, CheckboxControl } = wp.components;

registerBlockType( 'vodi/blog-tab-section', {
    title: __('Vodi Blog Tab Section', 'vodi'),

    icon: 'welcome-widgets-menus',

    category: 'vodi-blocks',

    edit: ( ( props ) => {
        const { attributes, setAttributes } = props;
        const { tab_args, section_nav_links, style, design_options } = attributes;

        const onChangeDesignOptions = newDesignOptions => {
            setAttributes( { design_options: { ...design_options, ...newDesignOptions } } );
        };

        const onChangeTabArgs = newTabArgs => {
            setAttributes( { tab_args: [...newTabArgs] } );
        };

        const onChangeTabArgsTabTitle = (newTabArgsTabTitle, index) => {
            var tab_args_updated = [ ...tab_args ];
            tab_args_updated[index].tab_title = newTabArgsTabTitle;
            setAttributes( { tab_args: [...tab_args_updated] } );
        };

        const onChangeTabArgsPostAtts = (newTabArgsPostAtts, index) => {
            var tab_args_updated = [ ...tab_args ];
            tab_args_updated[index].post_atts = { ...tab_args[index].post_atts, ...newTabArgsPostAtts };
            setAttributes( { tab_args: [...tab_args_updated] } );
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

        const onChangeStyle = newStyle => {
            setAttributes( { style: newStyle } );
        };

        return (
            <Fragment>
                <InspectorControls>
                    <Repeater
                        title={__('Blog Tabs', 'vodi')}
                        values={ tab_args }
                        defaultValues={ { tab_title: '', post_atts: {} } }
                        updateValues={ onChangeTabArgs }
                    >
                        <TextControl
                            label={__('Tab Title', 'vodi')}
                            name='tab_title'
                            valuekey='value'
                            value=''
                            trigger_method_name='onChange'
                            onChange={ onChangeTabArgsTabTitle }
                        />
                        <PostAtts
                            name='post_atts'
                            valuekey='attributes'
                            attributes={ {} }
                            trigger_method_name='updatePostAtts'
                            updatePostAtts={ onChangeTabArgsPostAtts }
                        />
                    </Repeater>
                    { ( style != 'style-v2' ) ? (
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
                    ) : '' }
                    <SelectControl
                        label={__('Style', 'vodi')}
                        value={ style }
                        options={ [
                            { label: __('Style 1', 'vodi'), value: 'style-v1' },
                            { label: __('Style 2', 'vodi'), value: 'style-v2' },
                        ] }
                        onChange={ onChangeStyle }
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
                    { tab_args ? (
                    <ServerSideRender
                        block="vodi/blog-tab-section"
                        attributes={ attributes }
                    />
                    ) : __('Add Tab', 'vodi') }
                </Disabled>
            </Fragment>
        );
    } ),

    save() {
        // Rendering in PHP
        return null;
    },
} );