import { ShortcodeAtts } from '../components/ShortcodeAtts';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls } = wp.editor;
const { ServerSideRender, PanelBody } = wp.components;

registerBlockType( 'masvideos/videos', {
    title: __('Videos Block', 'masvideos'),

    icon: 'format-video',

    category: 'masvideos-blocks',

    edit: ( ( props ) => {
        const { attributes, className, setAttributes } = props;

        const onChangeShortcodeAtts = newShortcodeAtts => {
            setAttributes( { ...newShortcodeAtts } );
        };

        return [
            <InspectorControls>
                <PanelBody
                    title={__('Videos Attributes', 'masvideos')}
                    initialOpen={ true }
                >
                    <ShortcodeAtts
                        postType = 'video'
                        catTaxonomy = 'video_cat'
                        attributes = { { ...attributes } }
                        updateShortcodeAtts = { onChangeShortcodeAtts }
                    />
                </PanelBody>
            </InspectorControls>,
            <ServerSideRender
                block = "masvideos/videos"
                attributes = { attributes }
            />
        ];
    } ),

    save() {
        // Rendering in PHP
        return null;
    },
} );