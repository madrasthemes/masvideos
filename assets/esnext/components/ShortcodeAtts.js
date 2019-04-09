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
        this.onChangeGenre = this.onChangeGenre.bind(this);
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

    onChangeGenre( newGenre ) {
        this.props.updateShortcodeAtts({
            genre: newGenre.join(',')
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
        const { attributes, postType, catTaxonomy, hideFields } = this.props;
        const { limit, columns, orderby, order, ids, category, genre, featured, top_rated } = attributes;

        return (
            <div>
                { !( hideFields && hideFields.includes('limit') ) ? (
                <RangeControl
                    label={__('Limit', 'vodi')}
                    value={ limit }
                    onChange={ this.onChangeLimit }
                    min={ 1 }
                    max={ 50 }
                />
                ) : '' }
                { !( hideFields && hideFields.includes('columns') ) ? (
                <RangeControl
                    label={__('Columns', 'vodi')}
                    value={ columns }
                    onChange={ this.onChangeColumns }
                    min={ 1 }
                    max={ 6 }
                />
                ) : '' }
                { !( hideFields && hideFields.includes('orderby') ) ? (
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
                ) : '' }
                { !( hideFields && hideFields.includes('order') ) ? (
                <SelectControl
                    label={__('Order', 'vodi')}
                    value={ order }
                    options={ [
                        { label: __('ASC', 'vodi'), value: 'ASC' },
                        { label: __('DESC', 'vodi'), value: 'DESC' },
                    ] }
                    onChange={ this.onChangeOrder }
                />
                ) : '' }
                { !( hideFields && hideFields.includes('ids') ) ? (
                <PostSelector
                    postType = { postType }
                    selectedPostIds={ ids ? ids.split(',').map(Number) : [] }
                    updateSelectedPostIds={ this.onChangeIds }
                />
                ) : '' }
                { ( postType === 'video' ) && !( hideFields && hideFields.includes('category') ) ? (
                <TermSelector
                    postType = { postType }
                    taxonomy = { catTaxonomy }
                    selectedTermIds={ category ? category.split(',').map(Number) : [] }
                    updateSelectedTermIds={ this.onChangeCategory }
                />
                ) : (
                !( hideFields && hideFields.includes('genre') ) ? (
                <TermSelector
                    postType = { postType }
                    taxonomy = { catTaxonomy }
                    selectedTermIds={ genre ? genre.split(',').map(Number) : [] }
                    updateSelectedTermIds={ this.onChangeGenre }
                />
                ) : '' ) }
                { !( hideFields && hideFields.includes('featured') ) ? (
                <CheckboxControl
                    label={__('Featured', 'vodi')}
                    help={__('Check to select featured posts.', 'vodi')}
                    checked={ featured }
                    onChange={ this.onChangeFeatured }
                />
                ) : '' }
                { !( hideFields && hideFields.includes('top_rated') ) ? (
                <CheckboxControl
                    label={__('Top Rated', 'vodi')}
                    help={__('Check to select top rated posts.', 'vodi')}
                    checked={ top_rated }
                    onChange={ this.onChangeTopRated }
                />
                ) : '' }
            </div>
        );
    }
}