import { ShortcodeAtts } from '../components/ShortcodeAtts';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls } = wp.blockEditor;
const { Fragment } = wp.element;
const { Disabled, PanelBody } = wp.components;
const { serverSideRender: ServerSideRender } = wp;

registerBlockType( 'masvideos/movies', {
    title: __('Movies Block', 'masvideos'),

    icon: 'editor-video',

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
                        title={__('Movies Attributes', 'masvideos')}
                        initialOpen={ true }
                    >
                        <ShortcodeAtts
                            postType = 'movie'
                            catTaxonomy = 'movie_genre'
                            tagTaxonomy = 'movie_tag'
                            attributes = { { ...attributes } }
                            updateShortcodeAtts = { onChangeShortcodeAtts }
                        />
                    </PanelBody>
                </InspectorControls>
                <Disabled>
                    <ServerSideRender
                        block = "masvideos/movies"
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