const { __ } = wp.i18n;
const { Component } = wp.element;
const { RangeControl } = wp.components;

/**
 * DesignOptions Component
 */
export class DesignOptions extends Component {
    /**
     * Constructor for DesignOptions Component.
     * Sets up state, and creates bindings for functions.
     * @param object props - current component properties.
     */
    constructor(props) {
        super(...arguments);
        this.props = props;

        this.onChangePaddingTop = this.onChangePaddingTop.bind(this);
        this.onChangePaddingBottom = this.onChangePaddingBottom.bind(this);
        this.onChangePaddingLeft = this.onChangePaddingLeft.bind(this);
        this.onChangePaddingRight = this.onChangePaddingRight.bind(this);
        this.onChangeMarginTop = this.onChangeMarginTop.bind(this);
        this.onChangeMarginBottom = this.onChangeMarginBottom.bind(this);
    }

    onChangePaddingTop( newonChangePaddingTop ) {
        this.props.updateDesignOptions({
            padding_top: newonChangePaddingTop
        });
    }

    onChangePaddingBottom( newonChangePaddingBottom ) {
        this.props.updateDesignOptions({
            padding_bottom: newonChangePaddingBottom
        });
    }

    onChangePaddingLeft( newonChangePaddingLeft ) {
        this.props.updateDesignOptions({
            padding_left: newonChangePaddingLeft
        });
    }

    onChangePaddingRight( newonChangePaddingRight ) {
        this.props.updateDesignOptions({
            padding_right: newonChangePaddingRight
        });
    }

    onChangeMarginTop( newonChangeMarginTop ) {
        this.props.updateDesignOptions({
            margin_top: newonChangeMarginTop
        });
    }

    onChangeMarginBottom( newonChangeMarginBottom ) {
        this.props.updateDesignOptions({
            margin_bottom: newonChangeMarginBottom
        });
    }

    /**
     * Renders the DesignOptions component.
     */
    render() {
        const { attributes } = this.props;
        const { padding_top, padding_bottom, padding_left, padding_right, margin_top, margin_bottom } = attributes;

        return (
            <div>
                <RangeControl
                    label={__('Padding Top (px)', 'vodi')}
                    value={ padding_top }
                    onChange={ this.onChangePaddingTop }
                    min={ 0 }
                    max={ 100 }
                />
                <RangeControl
                    label={__('Padding Bottom (px)', 'vodi')}
                    value={ padding_bottom }
                    onChange={ this.onChangePaddingBottom }
                    min={ 0 }
                    max={ 100 }
                />
                <RangeControl
                    label={__('Padding Left (px)', 'vodi')}
                    value={ padding_left }
                    onChange={ this.onChangePaddingLeft }
                    min={ 0 }
                    max={ 100 }
                />
                <RangeControl
                    label={__('Padding Right (px)', 'vodi')}
                    value={ padding_right }
                    onChange={ this.onChangePaddingRight }
                    min={ 0 }
                    max={ 100 }
                />
                <RangeControl
                    label={__('Margin Top (px)', 'vodi')}
                    value={ margin_top }
                    onChange={ this.onChangeMarginTop }
                    min={ -100 }
                    max={ 100 }
                />
                <RangeControl
                    label={__('Margin Bottom (px)', 'vodi')}
                    value={ margin_bottom }
                    onChange={ this.onChangeMarginBottom }
                    min={ -100 }
                    max={ 100 }
                />
            </div>
        );
    }
}