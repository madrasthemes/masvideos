import { ShortcodeAtts } from '../components/ShortcodeAtts';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls } = wp.editor;
const { Fragment } = wp.element;
const { ServerSideRender, Disabled, PanelBody } = wp.components;

registerBlockType( 'masvideos/movies', {
    title: __('Movies Block', 'masvideos'),

    icon: 'format-video',

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