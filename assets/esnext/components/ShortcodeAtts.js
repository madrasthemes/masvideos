import { PostSelector } from './PostSelector';
import { TermSelector } from './TermSelector';

const { __ } = wp.i18n;
const { Component } = wp.element;
const { RangeControl, SelectControl, CheckboxControl } = wp.components;
const { applyFilters } = wp.hooks;

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
        this.onChangeTag = this.onChangeTag.bind(this);
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

    onChangeTag( newTag ) {
        this.props.updateShortcodeAtts({
            tag: newTag.join(',')
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
        const { attributes, postType, catTaxonomy, tagTaxonomy, minLimit = 1, maxLimit = 20, minColumns = 1, maxColumns = 6, hideFields } = this.props;
        const { limit, columns, orderby, order, ids, category, genre, tag, featured, top_rated } = attributes;

        return (
            <div>
                { !( hideFields && hideFields.includes('limit') ) ? (
                <RangeControl
                    label={__('Limit', 'masvideos')}
                    value={ limit }
                    onChange={ this.onChangeLimit }
                    min={ applyFilters( 'masvideos.component.shortcodeAtts.limit.min', minLimit ) }
                    max={ applyFilters( 'masvideos.component.shortcodeAtts.limit.max', maxLimit ) }
                />
                ) : '' }
                { !( hideFields && hideFields.includes('columns') ) ? (
                <RangeControl
                    label={__('Columns', 'masvideos')}
                    value={ columns }
                    onChange={ this.onChangeColumns }
                    min={ applyFilters( 'masvideos.component.shortcodeAtts.columns.min', minColumns ) }
                    max={ applyFilters( 'masvideos.component.shortcodeAtts.columns.max', maxColumns ) }
                />
                ) : '' }
                { !( hideFields && hideFields.includes('orderby') ) ? (
                <SelectControl
                    label={__('Orderby', 'masvideos')}
                    value={ orderby }
                    options={ applyFilters( 'masvideos.component.shortcodeAtts.orderby.options', [
                        { label: __('Title', 'masvideos'), value: 'title' },
                        { label: __('Date', 'masvideos'), value: ( postType === 'movie' ? 'release_date' : 'date' ) },
                        { label: __('ID', 'masvideos'), value: 'id' },
                        { label: __('Random', 'masvideos'), value: 'rand' },
                    ], this.props ) }
                    onChange={ this.onChangeOrderby }
                />
                ) : '' }
                { !( hideFields && hideFields.includes('order') ) ? (
                <SelectControl
                    label={__('Order', 'masvideos')}
                    value={ order }
                    options={ applyFilters( 'masvideos.component.shortcodeAtts.order.options', [
                        { label: __('ASC', 'masvideos'), value: 'ASC' },
                        { label: __('DESC', 'masvideos'), value: 'DESC' },
                    ], this.props ) }
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
                { ( postType === 'video' ) && catTaxonomy && !( hideFields && hideFields.includes('category') ) ? (
                <TermSelector
                    postType = { postType }
                    taxonomy = { catTaxonomy }
                    title = { __('Search Category', 'masvideos') }
                    selectedTermIds={ category ? category.split(',').map(Number) : [] }
                    updateSelectedTermIds={ this.onChangeCategory }
                />
                ) : ( catTaxonomy && !( hideFields && hideFields.includes('genre') ) ? (
                <TermSelector
                    postType = { postType }
                    taxonomy = { catTaxonomy }
                    title = { __('Search Genre', 'masvideos') }
                    selectedTermIds={ genre ? genre.split(',').map(Number) : [] }
                    updateSelectedTermIds={ this.onChangeGenre }
                />
                ) : '' ) }
                { !( hideFields && hideFields.includes('tag') ) ? (
                <TermSelector
                    postType = { postType }
                    taxonomy = { tagTaxonomy }
                    title = { __('Search Tag', 'masvideos') }
                    selectedTermIds={ tag ? tag.split(',').map(Number) : [] }
                    updateSelectedTermIds={ this.onChangeTag }
                />
                ) : '' }
                { !( hideFields && hideFields.includes('featured') ) ? (
                <CheckboxControl
                    label={__('Featured', 'masvideos')}
                    help={__('Check to select featured posts.', 'masvideos')}
                    checked={ featured }
                    onChange={ this.onChangeFeatured }
                />
                ) : '' }
                { !( hideFields && hideFields.includes('top_rated') ) ? (
                <CheckboxControl
                    label={__('Top Rated', 'masvideos')}
                    help={__('Check to select top rated posts.', 'masvideos')}
                    checked={ top_rated }
                    onChange={ this.onChangeTopRated }
                />
                ) : '' }
            </div>
        );
    }
}