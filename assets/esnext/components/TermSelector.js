import { ItemList } from "./ItemList";
import * as api from '../utils/api';
import { uniqueById, debounce } from '../utils/useful-funcs';

const { __ } = wp.i18n;
const { Icon } = wp.components;
const { Component } = wp.element;

/**
 * TermSelector Component
 */
export class TermSelector extends Component {
    /**
     * Constructor for TermSelector Component.
     * Sets up state, and creates bindings for functions.
     * @param object props - current component properties.
     */
    constructor(props) {
        super(...arguments);
        this.props = props;

        this.state = {
            terms: [],
            loading: false,
            type: props.postType || 'post',
            taxonomy: props.taxonomy || 'category',
            taxonomies: [],
            filter: '',
            filterLoading: false,
            filterTerms: [],
            initialLoading: false,
        };

        this.addTerm = this.addTerm.bind(this);
        this.removeTerm = this.removeTerm.bind(this);
        this.handleInputFilterChange = this.handleInputFilterChange.bind(this);
        this.doTermFilter = debounce(this.doTermFilter.bind(this), 300);
    }

    /**
     * When the component mounts it calls this function.
     * Fetches terms taxonomies, selected terms then makes first call for terms
     */
    componentDidMount() {
        this.setState({
            initialLoading: true,
        });

        api.getTaxonomies( { type: this.state.type } )
            .then(( response ) => {
                this.setState({
                    taxonomies: response
                }, () => {
                    this.retrieveSelectedTerms()
                        .then(() => {
                            this.setState({
                                initialLoading: false,
                            });
                        })
                });
            });
    }

    /**
     * GetTerms wrapper, builds the request argument based state and parameters passed/
     * @param {object} args - desired arguments (can be empty).
     * @returns {Promise<T>}
     */
    getTerms(args = {}) {
        const { selectedTermIds } = this.props;

        const defaultArgs = {
            per_page: 10,
            type: this.state.type,
            taxonomy: this.state.taxonomy,
            search: this.state.filter,
        };

        const requestArguments = {
            ...defaultArgs,
            ...args
        };

        requestArguments.restBase = this.state.taxonomies[this.state.taxonomy].rest_base;

        return api.getTerms(requestArguments)
            .then(response => {
                if (requestArguments.search) {
                    this.setState({
                        filterTerms: response.filter(({ id }) => selectedTermIds.indexOf(id) === -1),
                    });

                    return response;
                }

                this.setState({
                    terms: uniqueById([...this.state.terms, ...response]),
                });

                // return response to continue the chain
                return response;
            });
    }

    /**
     * Gets the selected terms by id from the `terms` state object and sorts them by their position in the selected array.
     * @returns Array of objects.
     */
    getSelectedTerms() {
        const { selectedTermIds } = this.props;
        return this.state.terms
            .filter(({ id }) => selectedTermIds.indexOf(id) !== -1)
            .sort((a, b) => {
                const aIndex = this.props.selectedTermIds.indexOf(a.id);
                const bIndex = this.props.selectedTermIds.indexOf(b.id);

                if (aIndex > bIndex) {
                    return 1;
                }

                if (aIndex < bIndex) {
                    return -1;
                }

                return 0;
            });
    }

    /**
     * Makes the necessary api calls to fetch the selected terms and returns a promise.
     * @returns {*}
     */
    retrieveSelectedTerms() {
        const { termType, selectedTermIds } = this.props;
        const { taxonomies } = this.state;

        if ( selectedTermIds && !selectedTermIds.length > 0 ) {
            // return a fake promise that auto resolves.
            return new Promise((resolve) => resolve());
        }

        return this.getTerms({
            include: this.props.selectedTermIds.join(','),
            per_page: 100,
            termType
        });
    }

    /**
     * Adds desired term id to the selectedTermIds List
     * @param {Integer} term_id
     */
    addTerm(term_id) {
        if (this.state.filter) {
            const term = this.state.filterTerms.filter(p => p.id === term_id);
            const terms = uniqueById([
                ...this.state.terms,
                ...term
            ]);

            this.setState({
                terms
            });
        }

        this.props.updateSelectedTermIds([
            ...this.props.selectedTermIds,
            term_id
        ]);
    }

    /**
     * Removes desired term id to the selectedTermIds List
     * @param {Integer} term_id
     */
    removeTerm(term_id) {
        this.props.updateSelectedTermIds([
            ...this.props.selectedTermIds
        ].filter(id => id !== term_id));
    }

    /**
     * Handles the search box input value
     * @param string type - comes from the event object target.
     */
    handleInputFilterChange({ target: { value:filter = '' } = {} } = {}) {
        this.setState({
            filter
        }, () => {
            if (!filter) {
                // remove filtered terms
                return this.setState({ filteredTerms: [], filtering: false });
            }

            this.doTermFilter();
        })
    }

    /**
     * Actual api call for searching for query, this function is debounced in constructor.
     */
    doTermFilter() {
        const { filter = '' } = this.state;

        if (!filter) {
            return;
        }

        this.setState({
            filtering: true,
            filterLoading: true
        });

        this.getTerms()
            .then(() => {
                this.setState({
                    filterLoading: false
                });
            });
    }

    /**
     * Renders the TermSelector component.
     */
    render() {
        const { title = __('Search Term', 'masvideos') } = this.props;
        const isFiltered = this.state.filtering;
        const termList = isFiltered && !this.state.filterLoading ? this.state.filterTerms : [];
        const SelectedTermList  = this.getSelectedTerms();

        const addIcon = <Icon icon="plus" />;
        const removeIcon = <Icon icon="minus" />;

        const searchinputuniqueId = 'searchinput-' + Math.random().toString(36).substr(2, 16);

        return (
            <div className="components-base-control components-term-selector">
                <div className="components-base-control__field--selected">
                    <h2>{ title }</h2>
                    <ItemList
                        items={SelectedTermList}
                        loading={this.state.initialLoading}
                        action={this.removeTerm}
                        icon={removeIcon}
                    />
                </div>
                <div className="components-base-control__field">
                    <label htmlFor={searchinputuniqueId} className="components-base-control__label">
                        <Icon icon="search" />
                    </label>
                    <input
                        className="components-text-control__input"
                        id={searchinputuniqueId}
                        type="search"
                        placeholder={__('Please enter your search query...', 'masvideos')}
                        value={this.state.filter}
                        onChange={this.handleInputFilterChange}
                    />
                    <ItemList
                        items={termList}
                        loading={this.state.initialLoading||this.state.loading||this.state.filterLoading}
                        filtered={isFiltered}
                        action={this.addTerm}
                        icon={addIcon}
                    />
                </div>
            </div>
        );
    }
}