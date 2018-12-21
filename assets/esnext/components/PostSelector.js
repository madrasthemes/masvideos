import { PostList } from "./PostList";
import * as api from '../utils/api';
import { uniqueById, debounce } from '../utils/useful-funcs';

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
        };

        this.addPost = this.addPost.bind(this);
        this.removePost = this.removePost.bind(this);
        this.handleInputFilterChange = this.handleInputFilterChange.bind(this);
        this.doPostFilter = debounce(this.doPostFilter.bind(this), 300);
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
                        .then(() => {
                            this.setState({
                                initialLoading: false,
                            });
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
        const { selectedPostIds } = this.props;

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
                        filterPosts: response.filter(({ id }) => selectedPostIds.indexOf(id) === -1),
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
    getSelectedPosts() {
        const { selectedPostIds } = this.props;
        return this.state.posts
            .filter(({ id }) => selectedPostIds.indexOf(id) !== -1)
            .sort((a, b) => {
                const aIndex = this.props.selectedPostIds.indexOf(a.id);
                const bIndex = this.props.selectedPostIds.indexOf(b.id);

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
     * Makes the necessary api calls to fetch the selected posts and returns a promise.
     * @returns {*}
     */
    retrieveSelectedPosts() {
        const { postType, selectedPostIds } = this.props;
        const { types } = this.state;

        if ( selectedPostIds && !selectedPostIds.length > 0 ) {
            // return a fake promise that auto resolves.
            return new Promise((resolve) => resolve());
        }

        return this.getPosts({
            include: this.props.selectedPostIds.join(','),
            per_page: 100,
            postType
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

        this.props.updateSelectedPostIds([
            ...this.props.selectedPostIds,
            post_id
        ]);
    }

    /**
     * Removes desired post id to the selectedPostIds List
     * @param {Integer} post_id
     */
    removePost(post_id) {
        this.props.updateSelectedPostIds([
            ...this.props.selectedPostIds
        ].filter(id => id !== post_id));
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

        this.getPosts()
            .then(() => {
                this.setState({
                    filterLoading: false
                });
            });
    }

    /**
     * Renders the PostSelector component.
     */
    render() {
        const isFiltered = this.state.filtering;
        const postList = isFiltered && !this.state.filterLoading ? this.state.filterPosts : [];
        const SelectedPostList  = this.getSelectedPosts();

        const addIcon = <Icon icon="plus" />;
        const removeIcon = <Icon icon="minus" />;

        return (
            <div className="components-base-control components-post-selector">
                <div className="components-base-control__field">
                    <label htmlFor="searchinput" className="components-base-control__label">
                        <Icon icon="search" />
                    </label>
                    <input
                        className="components-text-control__input"
                        id="searchinput"
                        type="search"
                        placeholder={"Please enter your search query..."}
                        value={this.state.filter}
                        onChange={this.handleInputFilterChange}
                    />
                    <PostList
                        posts={postList}
                        loading={this.state.initialLoading||this.state.loading||this.state.filterLoading}
                        filtered={isFiltered}
                        action={this.addPost}
                        icon={addIcon}
                    />
                </div>
                <div className="components-base-control__field--selected">
                    <h2>Selected</h2>
                    <PostList
                        posts={SelectedPostList}
                        loading={this.state.initialLoading}
                        action={this.removePost}
                        icon={removeIcon}
                    />
                </div>
            </div>
        );
    }
}