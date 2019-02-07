import { TermSelector } from './TermSelector';

const { __ } = wp.i18n;
const { Component } = wp.element;
const { RangeControl, SelectControl, CheckboxControl } = wp.components;

/**
 * CategoryArgs Component
 */
export class CategoryArgs extends Component {
    /**
     * Constructor for CategoryArgs Component.
     * Sets up state, and creates bindings for functions.
     * @param object props - current component properties.
     */
    constructor(props) {
        super(...arguments);
        this.props = props;

        this.onChangeNumber = this.onChangeNumber.bind(this);
        this.onChangeOrderby = this.onChangeOrderby.bind(this);
        this.onChangeOrder = this.onChangeOrder.bind(this);
        this.onChangeInclude = this.onChangeInclude.bind(this);
        this.onChangeHideEmpty = this.onChangeHideEmpty.bind(this);
    }

    onChangeNumber( newNumber ) {
        this.props.updateCategoryArgs({
            number: newNumber
        });
    }

    onChangeOrderby( newOrderby ) {
        this.props.updateCategoryArgs({
            orderby: newOrderby
        });
    }

    onChangeOrder( newOrder ) {
        this.props.updateCategoryArgs({
            order: newOrder
        });
    }

    onChangeInclude( newInclude ) {
        this.props.updateCategoryArgs({
            include: newInclude.join(',')
        });
    }

    onChangeHideEmpty( newHideEmpty ) {
        this.props.updateCategoryArgs({
            hide_empty: newHideEmpty
        });
    }

    /**
     * Renders the CategoryArgs component.
     */
    render() {
        const { attributes, postType, catTaxonomy, hideFields } = this.props;
        const { number, orderby, order, include, hide_empty } = attributes;

        return (
            <div>
                { !( hideFields && hideFields.includes('number') ) ? (
                <RangeControl
                    label={__('Limit', 'vodi')}
                    value={ number }
                    onChange={ this.onChangeNumber }
                    min={ 1 }
                    max={ 20 }
                />
                ) : '' }
                { !( hideFields && hideFields.includes('orderby') ) ? (
                <SelectControl
                    label={__('Orderby', 'vodi')}
                    value={ orderby }
                    options={ [
                        { label: __('Title', 'vodi'), value: 'name' },
                        { label: __('Count', 'vodi'), value: 'count' },
                        { label: __('ID', 'vodi'), value: 'id' },
                        { label: __('Slug', 'vodi'), value: 'slug' },
                        { label: __('Term ID', 'vodi'), value: 'term_id' },
                        { label: __('Term Group', 'vodi'), value: 'term_group' },
                        { label: __('Description', 'vodi'), value: 'description' },
                        { label: __('Parent', 'vodi'), value: 'parent' },
                        { label: __('Include', 'vodi'), value: 'include' },
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
                { !( hideFields && hideFields.includes('include') ) ? (
                <TermSelector
                    postType = { postType }
                    taxonomy = { catTaxonomy }
                    selectedTermIds={ include ? include.split(',').map(Number) : [] }
                    updateSelectedTermIds={ this.onChangeInclude }
                />
                ) : '' }
                { !( hideFields && hideFields.includes('hide_empty') ) ? (
                <CheckboxControl
                    label={__('Hide Empty', 'vodi')}
                    help={__('Check to select hide empty categories.', 'vodi')}
                    checked={ hide_empty }
                    onChange={ this.onChangeHideEmpty }
                />
                ) : '' }
            </div>
        );
    }
}