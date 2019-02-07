import { DesignOptions } from '../components/DesignOptions';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, MediaUpload } = wp.editor;
const { Fragment } = wp.element;
const { ServerSideRender, Disabled, PanelBody, TextControl, Button } = wp.components;

registerBlockType( 'vodi/section-full-width-banner', {
    title: __('Full-width Banner Block', 'vodi'),

    icon: 'format-image',

    category: 'vodi-blocks',

    edit: ( ( props ) => {
        const { attributes, setAttributes } = props;
        const { banner_image, banner_link, design_options } = attributes;

        const onChangeBannerImage = media => {
            setAttributes( { banner_image: media.id } );
        };

        const onChangeBannerLink = newBannerLink => {
            setAttributes( { banner_link: newBannerLink } );
        };

        const onChangeDesignOptions = newDesignOptions => {
            setAttributes( { design_options: { ...design_options, ...newDesignOptions } } );
        };

        const getImageButton = (openEvent) => {
            return (
                <div className="button-container">
                    <Button 
                        onClick={ openEvent }
                        className="button button-large"
                    >
                        {__('Pick an image', 'vodi')}
                    </Button>
                </div>
            );
        };

        return (
            <Fragment>
                <InspectorControls>
                    <MediaUpload
                        onSelect={onChangeBannerImage}
                        type="image"
                        value={ banner_image }
                        render={ ({ open }) => getImageButton(open) }
                    />
                    <TextControl
                        label={__('Link', 'vodi')}
                        value={ banner_link }
                        onChange={ onChangeBannerLink }
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
                        block="vodi/section-full-width-banner"
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