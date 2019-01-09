import { PostSelector } from './PostSelector';
import { TermSelector } from './TermSelector';

const { __ } = wp.i18n;
const { Component } = wp.element;
const { RangeControl, SelectControl, CheckboxControl } = wp.components;

/**
 * ShortcodeAtts Component
 */
export class ShortcodeAtts extends Component {
    /**
     * Constructor for ShortcodeAtts Component.
     * Sets up state, and creates bindings for functions.
     * @param object props - current component properties.
     */
    constructor(props) {
        super(...arguments);
        this.props = props;

        this.onChangeLimit = this.onChangeLimit.bind(this);
        this.onChangeColumns = this.onChangeColumns.bind(this);
        this.onChangeOrderby = this.onChangeOrderby.bind(this);
        this.onChangeOrder = this.onChangeOrder.bind(this);
        this.onChangeIds = this.onChangeIds.bind(this);
        this.onChangeCategory = this.onChangeCategory.bind(this);
        this.onChangeFeatured = this.onChangeFeatured.bind(this);
        this.onChangeTopRated = this.onChangeTopRated.bind(this);
    }

    onChangeLimit( newLimit ) {
        this.props.updateShortcodeAtts({
            limit: newLimit
        });
    }

    onChangeColumns( newColumns ) {
        this.props.updateShortcodeAtts({
            columns: newColumns
        });
    }

    onChangeOrderby( newOrderby ) {
        this.props.updateShortcodeAtts({
            orderby: newOrderby
        });
    }

    onChangeOrder( newOrder ) {
        this.props.updateShortcodeAtts({
            order: newOrder
        });
    }

    onChangeIds( newIds ) {
        this.props.updateShortcodeAtts({
            ids: newIds.join(',')
        });
    }

    onChangeCategory( newCategory ) {
        this.props.updateShortcodeAtts({
            category: newCategory.join(',')
        });
    }

    onChangeFeatured( newFeatured ) {
        this.props.updateShortcodeAtts({
            featured: newFeatured
        });
    }

    onChangeTopRated( newTopRated ) {
        this.props.updateShortcodeAtts({
            top_rated: newTopRated
        });
    }

    /**
     * Renders the ShortcodeAtts component.
     */
    render() {
        const { attributes, postType, catTaxonomy } = this.props;
        const { limit, columns, orderby, order, ids, category, featured, top_rated } = attributes;

        return (
            <div>
                <RangeControl
                    label={__('Limit', 'masvideos')}
                    value={ limit }
                    onChange={ this.onChangeLimit }
                    min={ 1 }
                    max={ 50 }
                />
                <RangeControl
                    label={__('Columns', 'masvideos')}
                    value={ columns }
                    onChange={ this.onChangeColumns }
                    min={ 1 }
                    max={ 10 }
                />
                <SelectControl
                    label={__('Orderby', 'masvideos')}
                    value={ orderby }
                    options={ [
                        { label: __('Title', 'masvideos'), value: 'title' },
                        { label: __('Date', 'masvideos'), value: 'date' },
                        { label: __('ID', 'masvideos'), value: 'id' },
                        { label: __('Random', 'masvideos'), value: 'rand' },
                    ] }
                    onChange={ this.onChangeOrderby }
                />
                <SelectControl
                    label={__('Order', 'masvideos')}
                    value={ order }
                    options={ [
                        { label: __('ASC', 'masvideos'), value: 'ASC' },
                        { label: __('DESC', 'masvideos'), value: 'DESC' },
                    ] }
                    onChange={ this.onChangeOrder }
                />
                <PostSelector
                    postType = { postType }
                    selectedPostIds={ ids ? ids.split(',').map(Number) : [] }
                    updateSelectedPostIds={ this.onChangeIds }
                />
                <TermSelector
                    postType = { postType }
                    taxonomy = { catTaxonomy }
                    selectedTermIds={ category ? category.split(',').map(Number) : [] }
                    updateSelectedTermIds={ this.onChangeCategory }
                />
                <CheckboxControl
                    label={__('Featured', 'masvideos')}
                    help={__('Check to select featured posts.', 'masvideos')}
                    checked={ featured }
                    onChange={ this.onChangeFeatured }
                />
                <CheckboxControl
                    label={__('Top Rated', 'masvideos')}
                    help={__('Check to select top rated posts.', 'masvideos')}
                    checked={ top_rated }
                    onChange={ this.onChangeTopRated }
                />
            </div>
        );
    }
}