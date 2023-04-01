import { ShortcodeAtts } from '../components/ShortcodeAtts';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls } = wp.blockEditor;
const { Fragment } = wp.element;
const { Disabled, PanelBody } = wp.components;
const { serverSideRender: ServerSideRender } = wp;

registerBlockType( 'masvideos/videos', {
    title: __('Videos Block', 'masvideos'),

    icon: 'video-alt2',

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
                        title={__('Videos Attributes', 'masvideos')}
                        initialOpen={ true }
                    >
                        <ShortcodeAtts
                            postType = 'video'
                            catTaxonomy = 'video_cat'
                            tagTaxonomy = 'video_tag'
                            hideFields = { ['top_rated'] }
                            attributes = { { ...attributes } }
                            updateShortcodeAtts = { onChangeShortcodeAtts }
                        />
                    </PanelBody>
                </InspectorControls>
                <Disabled>
                    <ServerSideRender
                        block = "masvideos/videos"
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