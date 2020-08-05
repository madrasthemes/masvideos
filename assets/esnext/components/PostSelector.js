import { ItemList } from './ItemList';
import * as api from '../utils/api';
import { uniqueById, debounce } from '../utils/useful-funcs';

const { __ } = wp.i18n;
const { Icon } = wp.components;
const { Component } = wp.element;

/**
 * PostSelector Component
 */
export class PostSelector extends Component {
    /**
     * Constructor for PostSelector Component.
     * Sets up state, and creates bindings for functions.
     * @param object props - current component properties.
     */
    constructor(props) {
        super(...arguments);
        this.props = props;

        this.state = {
            posts: [],
            loading: false,
            type: props.postType || 'post',
            types: [],
            filter: '',
            filterLoading: false,
            filterPosts: [],
            initialLoading: false,
            selectedPosts: [],
        };

        this.addPost = this.addPost.bind(this);
        this.removePost = this.removePost.bind(this);
        this.handleInputFilterChange = this.handleInputFilterChange.bind(this);
        this.doPostFilter = debounce(this.doPostFilter.bind(this), 300);
        this.getSelectedPostIds = this.getSelectedPostIds.bind(this);
        this.getSelectedPosts = this.getSelectedPosts.bind(this);
    }

    /**
     * When the component mounts it calls this function.
     * Fetches posts types, selected posts then makes first call for posts
     */
    componentDidMount() {
        this.setState({
            initialLoading: true,
        });

        api.getPostTypes()
            .then(( response ) => {
                this.setState({
                    types: response
                }, () => {
                    this.retrieveSelectedPosts()
                        .then(( selectedPosts ) => {
                            if( selectedPosts ) {
                                this.setState({
                                    initialLoading: false,
                                    selectedPosts: selectedPosts,
                                });
                            } else {
                                this.setState({
                                    initialLoading: false,
                                });
                            }
                        })
                });
            });
    }

    /**
     * GetPosts wrapper, builds the request argument based state and parameters passed/
     * @param {object} args - desired arguments (can be empty).
     * @returns {Promise<T>}
     */
    getPosts(args = {}) {
        const postIds = this.getSelectedPostIds();

        const defaultArgs = {
            per_page: 10,
            type: this.state.type,
            search: this.state.filter,
        };

        const requestArguments = {
            ...defaultArgs,
            ...args
        };

        requestArguments.restBase = this.state.types[this.state.type].rest_base;

        return api.getPosts(requestArguments)
            .then(response => {
                if (requestArguments.search) {
                    this.setState({
                        filterPosts: response.filter(({ id }) => postIds.indexOf(id) === -1),
                    });

                    return response;
                }

                this.setState({
                    posts: uniqueById([...this.state.posts, ...response]),
                });

                // return response to continue the chain
                return response;
            });
    }

    /**
     * Gets the selected posts by id from the `posts` state object and sorts them by their position in the selected array.
     * @returns Array of objects.
     */
    getSelectedPostIds() {
        const { selectedPostIds } = this.props;

        if( selectedPostIds ) {
            const postIds = Array.isArray( selectedPostIds ) ? selectedPostIds : selectedPostIds.split(',');
            return postIds;
        }

        return [];
    }

    /**
     * Gets the selected posts by id from the `posts` state object and sorts them by their position in the selected array.
     * @returns Array of objects.
     */
    getSelectedPosts( postIds ) {
        const postList = uniqueById([
            ...this.state.filterPosts,
            ...this.state.posts
        ]);
        const selectedPosts = postList
            .filter(({ id }) => postIds.indexOf(id) !== -1)
            .sort((a, b) => {
                const aIndex = postIds.indexOf(a.id);
                const bIndex = postIds.indexOf(b.id);

                if (aIndex > bIndex) {
                    return 1;
                }

                if (aIndex < bIndex) {
                    return -1;
                }

                return 0;
            });

        this.setState({
            selectedPosts: selectedPosts,
        });
    }

    /**
     * Makes the necessary api calls to fetch the selected posts and returns a promise.
     * @returns {*}
     */
    retrieveSelectedPosts() {
        const { postType, selectedPostIds } = this.props;
        const { types } = this.state;

        const postIds = this.getSelectedPostIds().join(',');

        if ( ! postIds ) {
            // return a fake promise that auto resolves.
            return new Promise((resolve) => resolve());
        }

        let post_args = {
            include: postIds,
            per_page: 100,
            postType
        };

        if( this.props.postStatus ) {
            post_args.status = this.props.postStatus;
        }

        return this.getPosts({
            ...post_args
        });
    }

    /**
     * Adds desired post id to the selectedPostIds List
     * @param {Integer} post_id
     */
    addPost(post_id) {
        if (this.state.filter) {
            const post = this.state.filterPosts.filter(p => p.id === post_id);
            const posts = uniqueById([
                ...this.state.posts,
                ...post
            ]);

            this.setState({
                posts
            });
        }

        if( this.props.selectSingle ) {
            const selectedPostIds = [ post_id ];
            this.props.updateSelectedPostIds( selectedPostIds );
            this.getSelectedPosts( selectedPostIds );
        } else {
            const postIds = this.getSelectedPostIds();
            const selectedPostIds = [ ...postIds, post_id ];
            this.props.updateSelectedPostIds( selectedPostIds );
            this.getSelectedPosts( selectedPostIds );
        }
    }

    /**
     * Removes desired post id to the selectedPostIds List
     * @param {Integer} post_id
     */
    removePost(post_id) {
        const postIds = this.getSelectedPostIds();
        const selectedPostIds = [ ...postIds ].filter(id => id !== post_id);
        this.props.updateSelectedPostIds( selectedPostIds );
        this.getSelectedPosts( selectedPostIds );
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
                // remove filtered posts
                return this.setState({ filteredPosts: [], filtering: false });
            }

            this.doPostFilter();
        })
    }

    /**
     * Actual api call for searching for query, this function is debounced in constructor.
     */
    doPostFilter() {
        const { filter = '' } = this.state;

        if (!filter) {
            return;
        }

        this.setState({
            filtering: true,
            filterLoading: true
        });

        let post_args = {};

        if( this.props.postStatus ) {
            post_args.status = this.props.postStatus;
        }

        this.getPosts({
            ...post_args
        }).then(() => {
            this.setState({
                filterLoading: false
            });
        });
    }

    /**
     * Renders the PostSelector component.
     */
    render() {
        const postList = this.state.filtering && !this.state.filterLoading ? this.state.filterPosts : [];

        const addIcon = <Icon icon="plus" />;
        const removeIcon = <Icon icon="minus" />;

        const searchinputuniqueId = 'searchinput-' + Math.random().toString(36).substr(2, 16);

        return (
            <div className="components-base-control components-post-selector">
                <div className="components-base-control__field--selected">
                    <h2>{__('Search Post', 'masvideos')}</h2>
                    <ItemList
                        items={ [...this.state.selectedPosts] }
                        loading={this.state.initialLoading}
                        action={this.removePost}
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
                        items={postList}
                        loading={this.state.initialLoading||this.state.loading||this.state.filterLoading}
                        filtered={this.state.filtering}
                        action={this.addPost}
                        icon={addIcon}
                    />
                </div>
            </div>
        );
    }
}