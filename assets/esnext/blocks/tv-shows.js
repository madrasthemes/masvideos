import { ShortcodeAtts } from '../components/ShortcodeAtts';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls } = wp.editor;
const { Fragment } = wp.element;
const { ServerSideRender, Disabled, PanelBody } = wp.components;

registerBlockType( 'masvideos/tv-shows', {
    title: __('TV Shows Block', 'masvideos'),

    icon: 'welcome-view-site',

    category: 'masvideos-blocks',

    edit: ( ( props ) => {
        const { attributes, className, setAttributes } = props;

        const onChangeShortcodeAtts = newShortcodeAtts => {
            setAttributes( { ...newShortcodeAtts } );
        };

        return (
            <Fragment>
                <InspectorControls>
                    <PanelBody
                        title={__('TV Shows Attributes', 'masvideos')}
                        initialOpen={ true }
                    >
                        <ShortcodeAtts
                            postType = 'tv_show'
                            catTaxonomy = 'tv_show_genre'
                            attributes = { { ...attributes } }
                            updateShortcodeAtts = { onChangeShortcodeAtts }
                        />
                    </PanelBody>
                </InspectorControls>
                <Disabled>
                    <ServerSideRender
                        block = "masvideos/tv-shows"
                        attributes = { attributes }
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