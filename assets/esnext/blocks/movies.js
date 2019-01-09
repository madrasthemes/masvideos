import { ShortcodeAtts } from '../components/ShortcodeAtts';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls } = wp.editor;
const { ServerSideRender, PanelBody } = wp.components;

registerBlockType( 'masvideos/movies', {
    title: __('Movies Block', 'masvideos'),

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
                    title={__('Movies Attributes', 'masvideos')}
                    initialOpen={ true }
                >
                    <ShortcodeAtts
                        postType = 'movie'
                        catTaxonomy = 'movie_cat'
                        attributes = { { ...attributes } }
                        updateShortcodeAtts = { onChangeShortcodeAtts }
                    />
                </PanelBody>
            </InspectorControls>,
            <ServerSideRender
                block = "masvideos/movies"
                attributes = { attributes }
            />
        ];
    } ),

    save() {
        // Rendering in PHP
        return null;
    },
} );