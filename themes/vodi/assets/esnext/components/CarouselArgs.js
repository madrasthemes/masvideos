const { __ } = wp.i18n;
const { Component } = wp.element;
const { RangeControl, CheckboxControl } = wp.components;

/**
 * CarouselArgs Component
 */
export class CarouselArgs extends Component {
    /**
     * Constructor for CarouselArgs Component.
     * Sets up state, and creates bindings for functions.
     * @param object props - current component properties.
     */
    constructor(props) {
        super(...arguments);
        this.props = props;

        this.onChangeSlidesToShow = this.onChangeSlidesToShow.bind(this);
        this.onChangeSlidesToScroll = this.onChangeSlidesToScroll.bind(this);
        this.onChangeDots = this.onChangeDots.bind(this);
        this.onChangeArrows = this.onChangeArrows.bind(this);
        this.onChangeAutoplay = this.onChangeAutoplay.bind(this);
        this.onChangeInfinite = this.onChangeInfinite.bind(this);
    }

    onChangeSlidesToShow( newSlidesToShow ) {
        this.props.updateCarouselArgs({
            slidesToShow: newSlidesToShow
        });
    }

    onChangeSlidesToScroll( newSlidesToScroll ) {
        this.props.updateCarouselArgs({
            slidesToScroll: newSlidesToScroll
        });
    }

    onChangeDots( newDots ) {
        this.props.updateCarouselArgs({
            dots: newDots
        });
    }

    onChangeArrows( newArrows ) {
        this.props.updateCarouselArgs({
            arrows: newArrows
        });
    }

    onChangeAutoplay( newAutoplay ) {
        this.props.updateCarouselArgs({
            autoplay: newAutoplay
        });
    }

    onChangeInfinite( newInfinite ) {
        this.props.updateCarouselArgs({
            infinite: newInfinite
        });
    }

    /**
     * Renders the CarouselArgs component.
     */
    render() {
        const { attributes } = this.props;
        const { slidesToShow, slidesToScroll, dots, arrows, autoplay, infinite } = attributes;

        return (
            <div>
                <RangeControl
                    label={__('Slide To Show', 'vodi')}
                    value={ slidesToShow }
                    onChange={ this.onChangeSlidesToShow }
                    min={ 1 }
                    max={ 10 }
                />
                <RangeControl
                    label={__('Slides To Scroll', 'vodi')}
                    value={ slidesToScroll }
                    onChange={ this.onChangeSlidesToScroll }
                    min={ 1 }
                    max={ 10 }
                />
                <CheckboxControl
                    label={__('Dots', 'vodi')}
                    help={__('Check to show carousel dots.', 'vodi')}
                    checked={ dots }
                    onChange={ this.onChangeDots }
                />
                <CheckboxControl
                    label={__('Arrows', 'vodi')}
                    help={__('Check to show carousel arrows.', 'vodi')}
                    checked={ arrows }
                    onChange={ this.onChangeArrows }
                />
                <CheckboxControl
                    label={__('Autoplay', 'vodi')}
                    help={__('Check to autoplay carousel.', 'vodi')}
                    checked={ autoplay }
                    onChange={ this.onChangeAutoplay }
                />
                <CheckboxControl
                    label={__('Infinite Scroll', 'vodi')}
                    help={__('Check to infinite scroll carousel.', 'vodi')}
                    checked={ infinite }
                    onChange={ this.onChangeInfinite }
                />
            </div>
        );
    }
}