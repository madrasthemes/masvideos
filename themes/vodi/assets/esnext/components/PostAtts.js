import { PostSelector } from './PostSelector';
import { TermSelector } from './TermSelector';

const { __ } = wp.i18n;
const { Component } = wp.element;
const { TextControl, RangeControl, SelectControl, CheckboxControl } = wp.components;

/**
 * PostAtts Component
 */
export class PostAtts extends Component {
    /**
     * Constructor for PostAtts Component.
     * Sets up state, and creates bindings for functions.
     * @param object props - current component properties.
     */
    constructor(props) {
        super(...arguments);
        this.props = props;

        this.onChangeNumber = this.onChangeNumber.bind(this);
        this.onChangeOrderby = this.onChangeOrderby.bind(this);
        this.onChangeOrder = this.onChangeOrder.bind(this);
        this.onChangeIds = this.onChangeIds.bind(this);
        this.onChangeCategory = this.onChangeCategory.bind(this);
        this.onChangeSticky = this.onChangeSticky.bind(this);
    }

    onChangeNumber( newNumber ) {
        this.props.updatePostAtts({
            posts_per_page: newNumber
        });
    }

    onChangeOrderby( newOrderby ) {
        this.props.updatePostAtts({
            orderby: newOrderby
        });
    }

    onChangeOrder( newOrder ) {
        this.props.updatePostAtts({
            order: newOrder
        });
    }

    onChangeIds( newIds ) {
        this.props.updatePostAtts({
            ids: newIds.join(',')
        });
    }

    onChangeCategory( newCategory ) {
        this.props.updatePostAtts({
            category: newCategory.join(',')
        });
    }

    onChangeSticky( newSticky ) {
        this.props.updatePostAtts({
            sticky: newSticky
        });
    }

    /**
     * Renders the PostAtts component.
     */
    render() {
        const { attributes, catTaxonomy } = this.props;
        const { posts_per_page, orderby, order, ids, category, sticky } = attributes;

        return (
            <div>
                <RangeControl
                    label={__('Limit', 'vodi')}
                    value={ posts_per_page }
                    onChange={ this.onChangeNumber }
                    min={ 1 }
                    max={ 10 }
                />
                <SelectControl
                    label={__('Orderby', 'vodi')}
                    value={ orderby }
                    options={ [
                        { label: __('Title', 'vodi'), value: 'title' },
                        { label: __('Date', 'vodi'), value: 'date' },
                        { label: __('ID', 'vodi'), value: 'id' },
                        { label: __('Random', 'vodi'), value: 'rand' },
                    ] }
                    onChange={ this.onChangeOrderby }
                />
                <SelectControl
                    label={__('Order', 'vodi')}
                    value={ order }
                    options={ [
                        { label: __('ASC', 'vodi'), value: 'ASC' },
                        { label: __('DESC', 'vodi'), value: 'DESC' },
                    ] }
                    onChange={ this.onChangeOrder }
                />
                <PostSelector
                    postType = 'post'
                    selectedPostIds={ ids ? ids.split(',').map(Number) : [] }
                    updateSelectedPostIds={ this.onChangeIds }
                />
                <TermSelector
                    postType = 'post'
                    taxonomy = { catTaxonomy }
                    selectedTermIds={ category ? category.split(',').map(Number) : [] }
                    updateSelectedTermIds={ this.onChangeCategory }
                />
                <SelectControl
                    label={__('Sticky Posts', 'vodi')}
                    value={ sticky }
                    options={ [
                        { label: __('Show All Posts', 'vodi'), value: 'show' },
                        { label: __('Hide Sticky Posts', 'vodi'), value: 'hide' },
                        { label: __('Show Only Sticky Posts', 'vodi'), value: 'only' },
                    ] }
                    onChange={ this.onChangeSticky }
                />
            </div>
        );
    }
}