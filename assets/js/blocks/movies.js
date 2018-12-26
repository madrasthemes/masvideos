(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
"use strict";

var _PostSelector = require("../components/PostSelector");

var _TermSelector = require("../components/TermSelector");

var registerBlockType = wp.blocks.registerBlockType;
var InspectorControls = wp.editor.InspectorControls;
var _wp$components = wp.components,
    ServerSideRender = _wp$components.ServerSideRender,
    TextControl = _wp$components.TextControl,
    RangeControl = _wp$components.RangeControl,
    SelectControl = _wp$components.SelectControl,
    CheckboxControl = _wp$components.CheckboxControl;
registerBlockType('masvideos/movies', {
  title: 'Movies Block',
  icon: 'format-video',
  category: 'masvideos-blocks',
  edit: function edit(props) {
    var attributes = props.attributes,
        className = props.className,
        setAttributes = props.setAttributes;
    var limit = attributes.limit,
        columns = attributes.columns,
        orderby = attributes.orderby,
        order = attributes.order,
        ids = attributes.ids,
        category = attributes.category,
        featured = attributes.featured,
        top_rated = attributes.top_rated;

    var onChangeLimit = function onChangeLimit(newLimit) {
      setAttributes({
        limit: newLimit
      });
    };

    var onChangeColumns = function onChangeColumns(newColumns) {
      setAttributes({
        columns: newColumns
      });
    };

    var onChangeOrderby = function onChangeOrderby(newOrderby) {
      setAttributes({
        orderby: newOrderby
      });
    };

    var onChangeOrder = function onChangeOrder(newOrder) {
      setAttributes({
        order: newOrder
      });
    };

    var onChangeIds = function onChangeIds(newIds) {
      setAttributes({
        ids: newIds.join(',')
      });
    };

    var onChangeCategory = function onChangeCategory(newCategory) {
      setAttributes({
        category: newCategory.join(',')
      });
    };

    var onChangeFeatured = function onChangeFeatured(newFeatured) {
      setAttributes({
        featured: newFeatured
      });
    };

    var onChangeTopRated = function onChangeTopRated(newTopRated) {
      setAttributes({
        top_rated: newTopRated
      });
    };

    return [wp.element.createElement(InspectorControls, null, wp.element.createElement(RangeControl, {
      label: "Limit",
      value: limit,
      onChange: onChangeLimit,
      min: 1,
      max: 50
    }), wp.element.createElement(RangeControl, {
      label: "Columns",
      value: columns,
      onChange: onChangeColumns,
      min: 1,
      max: 10
    }), wp.element.createElement(SelectControl, {
      label: "Orderby",
      value: orderby,
      options: [{
        label: 'Title',
        value: 'title'
      }, {
        label: 'Date',
        value: 'date'
      }, {
        label: 'ID',
        value: 'id'
      }, {
        label: 'Random',
        value: 'rand'
      }],
      onChange: onChangeOrderby
    }), wp.element.createElement(SelectControl, {
      label: "Order",
      value: order,
      options: [{
        label: 'ASC',
        value: 'ASC'
      }, {
        label: 'DESC',
        value: 'DESC'
      }],
      onChange: onChangeOrder
    }), wp.element.createElement(_PostSelector.PostSelector, {
      postType: "movie",
      selectedPostIds: ids ? ids.split(',').map(Number) : [],
      updateSelectedPostIds: onChangeIds
    }), wp.element.createElement(_TermSelector.TermSelector, {
      postType: "movie",
      taxonomy: "movie_cat",
      selectedTermIds: category ? category.split(',').map(Number) : [],
      updateSelectedTermIds: onChangeCategory
    }), wp.element.createElement(CheckboxControl, {
      label: "Featured",
      help: "Check to select featured movies.",
      checked: featured,
      onChange: onChangeFeatured
    }), wp.element.createElement(CheckboxControl, {
      label: "Top Rated",
      help: "Check to select top rated movies.",
      checked: top_rated,
      onChange: onChangeTopRated
    })), wp.element.createElement(ServerSideRender, {
      block: "masvideos/movies",
      attributes: attributes
    })];
  },
  save: function save() {
    // Rendering in PHP
    return null;
  }
});

},{"../components/PostSelector":4,"../components/TermSelector":5}],2:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.Item = void 0;

/**
 * Item Component.
 *
 * @param {string} itemTitle - Current item title.
 * @param {function} clickHandler - this is the handling function for the add/remove function
 * @param {Integer} itemId - Current item ID
 * @param icon
 * @returns {*} Item HTML.
 */
var Item = function Item(_ref) {
  var _ref$title = _ref.title;
  _ref$title = _ref$title === void 0 ? {} : _ref$title;
  var itemTitle = _ref$title.rendered,
      name = _ref.name,
      clickHandler = _ref.clickHandler,
      itemId = _ref.id,
      icon = _ref.icon;
  return wp.element.createElement("article", {
    className: "item"
  }, wp.element.createElement("div", {
    className: "item-body"
  }, wp.element.createElement("h3", {
    className: "item-title"
  }, itemTitle, name)), wp.element.createElement("button", {
    onClick: function onClick() {
      return clickHandler(itemId);
    }
  }, icon));
};

exports.Item = Item;

},{}],3:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.ItemList = void 0;

var _Item = require("./Item");

function _extends() { _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }

/**
 * ItemList Component
 * @param object props - Component props.
 * @returns {*}
 * @constructor
 */
var ItemList = function ItemList(props) {
  var _props$filtered = props.filtered,
      filtered = _props$filtered === void 0 ? false : _props$filtered,
      _props$loading = props.loading,
      loading = _props$loading === void 0 ? false : _props$loading,
      _props$items = props.items,
      items = _props$items === void 0 ? [] : _props$items,
      _props$action = props.action,
      action = _props$action === void 0 ? function () {} : _props$action,
      _props$icon = props.icon,
      icon = _props$icon === void 0 ? null : _props$icon;

  if (loading) {
    return wp.element.createElement("p", {
      className: "loading-items"
    }, "Loading ...");
  }

  if (filtered && items.length < 1) {
    return wp.element.createElement("div", {
      className: "item-list"
    }, wp.element.createElement("p", null, "Your query yielded no results, please try again."));
  }

  if (!items || items.length < 1) {
    return wp.element.createElement("p", {
      className: "no-items"
    }, "Not found.");
  }

  return wp.element.createElement("div", {
    class: "item-list"
  }, items.map(function (item) {
    return wp.element.createElement(_Item.Item, _extends({
      key: item.id
    }, item, {
      clickHandler: action,
      icon: icon
    }));
  }));
};

exports.ItemList = ItemList;

},{"./Item":2}],4:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.PostSelector = void 0;

var _ItemList = require("./ItemList");

var api = _interopRequireWildcard(require("../utils/api"));

var _usefulFuncs = require("../utils/useful-funcs");

function _interopRequireWildcard(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) { var desc = Object.defineProperty && Object.getOwnPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : {}; if (desc.get || desc.set) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } } newObj.default = obj; return newObj; } }

function _typeof(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _nonIterableSpread(); }

function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance"); }

function _iterableToArray(iter) { if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === "[object Arguments]") return Array.from(iter); }

function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; var ownKeys = Object.keys(source); if (typeof Object.getOwnPropertySymbols === 'function') { ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function (sym) { return Object.getOwnPropertyDescriptor(source, sym).enumerable; })); } ownKeys.forEach(function (key) { _defineProperty(target, key, source[key]); }); } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

var Icon = wp.components.Icon;
var Component = wp.element.Component;
/**
 * PostSelector Component
 */

var PostSelector =
/*#__PURE__*/
function (_Component) {
  _inherits(PostSelector, _Component);

  /**
   * Constructor for PostSelector Component.
   * Sets up state, and creates bindings for functions.
   * @param object props - current component properties.
   */
  function PostSelector(props) {
    var _this;

    _classCallCheck(this, PostSelector);

    _this = _possibleConstructorReturn(this, _getPrototypeOf(PostSelector).apply(this, arguments));
    _this.props = props;
    _this.state = {
      posts: [],
      loading: false,
      type: props.postType || 'post',
      types: [],
      filter: '',
      filterLoading: false,
      filterPosts: [],
      initialLoading: false
    };
    _this.addPost = _this.addPost.bind(_assertThisInitialized(_assertThisInitialized(_this)));
    _this.removePost = _this.removePost.bind(_assertThisInitialized(_assertThisInitialized(_this)));
    _this.handleInputFilterChange = _this.handleInputFilterChange.bind(_assertThisInitialized(_assertThisInitialized(_this)));
    _this.doPostFilter = (0, _usefulFuncs.debounce)(_this.doPostFilter.bind(_assertThisInitialized(_assertThisInitialized(_this))), 300);
    return _this;
  }
  /**
   * When the component mounts it calls this function.
   * Fetches posts types, selected posts then makes first call for posts
   */


  _createClass(PostSelector, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      var _this2 = this;

      this.setState({
        initialLoading: true
      });
      api.getPostTypes().then(function (response) {
        _this2.setState({
          types: response
        }, function () {
          _this2.retrieveSelectedPosts().then(function () {
            _this2.setState({
              initialLoading: false
            });
          });
        });
      });
    }
    /**
     * GetPosts wrapper, builds the request argument based state and parameters passed/
     * @param {object} args - desired arguments (can be empty).
     * @returns {Promise<T>}
     */

  }, {
    key: "getPosts",
    value: function getPosts() {
      var _this3 = this;

      var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var selectedPostIds = this.props.selectedPostIds;
      var defaultArgs = {
        per_page: 10,
        type: this.state.type,
        search: this.state.filter
      };

      var requestArguments = _objectSpread({}, defaultArgs, args);

      requestArguments.restBase = this.state.types[this.state.type].rest_base;
      return api.getPosts(requestArguments).then(function (response) {
        if (requestArguments.search) {
          _this3.setState({
            filterPosts: response.filter(function (_ref) {
              var id = _ref.id;
              return selectedPostIds.indexOf(id) === -1;
            })
          });

          return response;
        }

        _this3.setState({
          posts: (0, _usefulFuncs.uniqueById)([].concat(_toConsumableArray(_this3.state.posts), _toConsumableArray(response)))
        }); // return response to continue the chain


        return response;
      });
    }
    /**
     * Gets the selected posts by id from the `posts` state object and sorts them by their position in the selected array.
     * @returns Array of objects.
     */

  }, {
    key: "getSelectedPosts",
    value: function getSelectedPosts() {
      var _this4 = this;

      var selectedPostIds = this.props.selectedPostIds;
      return this.state.posts.filter(function (_ref2) {
        var id = _ref2.id;
        return selectedPostIds.indexOf(id) !== -1;
      }).sort(function (a, b) {
        var aIndex = _this4.props.selectedPostIds.indexOf(a.id);

        var bIndex = _this4.props.selectedPostIds.indexOf(b.id);

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

  }, {
    key: "retrieveSelectedPosts",
    value: function retrieveSelectedPosts() {
      var _this$props = this.props,
          postType = _this$props.postType,
          selectedPostIds = _this$props.selectedPostIds;
      var types = this.state.types;

      if (selectedPostIds && !selectedPostIds.length > 0) {
        // return a fake promise that auto resolves.
        return new Promise(function (resolve) {
          return resolve();
        });
      }

      return this.getPosts({
        include: this.props.selectedPostIds.join(','),
        per_page: 100,
        postType: postType
      });
    }
    /**
     * Adds desired post id to the selectedPostIds List
     * @param {Integer} post_id
     */

  }, {
    key: "addPost",
    value: function addPost(post_id) {
      if (this.state.filter) {
        var post = this.state.filterPosts.filter(function (p) {
          return p.id === post_id;
        });
        var posts = (0, _usefulFuncs.uniqueById)([].concat(_toConsumableArray(this.state.posts), _toConsumableArray(post)));
        this.setState({
          posts: posts
        });
      }

      this.props.updateSelectedPostIds([].concat(_toConsumableArray(this.props.selectedPostIds), [post_id]));
    }
    /**
     * Removes desired post id to the selectedPostIds List
     * @param {Integer} post_id
     */

  }, {
    key: "removePost",
    value: function removePost(post_id) {
      this.props.updateSelectedPostIds(_toConsumableArray(this.props.selectedPostIds).filter(function (id) {
        return id !== post_id;
      }));
    }
    /**
     * Handles the search box input value
     * @param string type - comes from the event object target.
     */

  }, {
    key: "handleInputFilterChange",
    value: function handleInputFilterChange() {
      var _this5 = this;

      var _ref3 = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {},
          _ref3$target = _ref3.target;

      _ref3$target = _ref3$target === void 0 ? {} : _ref3$target;
      var _ref3$target$value = _ref3$target.value,
          filter = _ref3$target$value === void 0 ? '' : _ref3$target$value;
      this.setState({
        filter: filter
      }, function () {
        if (!filter) {
          // remove filtered posts
          return _this5.setState({
            filteredPosts: [],
            filtering: false
          });
        }

        _this5.doPostFilter();
      });
    }
    /**
     * Actual api call for searching for query, this function is debounced in constructor.
     */

  }, {
    key: "doPostFilter",
    value: function doPostFilter() {
      var _this6 = this;

      var _this$state$filter = this.state.filter,
          filter = _this$state$filter === void 0 ? '' : _this$state$filter;

      if (!filter) {
        return;
      }

      this.setState({
        filtering: true,
        filterLoading: true
      });
      this.getPosts().then(function () {
        _this6.setState({
          filterLoading: false
        });
      });
    }
    /**
     * Renders the PostSelector component.
     */

  }, {
    key: "render",
    value: function render() {
      var isFiltered = this.state.filtering;
      var postList = isFiltered && !this.state.filterLoading ? this.state.filterPosts : [];
      var SelectedPostList = this.getSelectedPosts();
      var addIcon = wp.element.createElement(Icon, {
        icon: "plus"
      });
      var removeIcon = wp.element.createElement(Icon, {
        icon: "minus"
      });
      return wp.element.createElement("div", {
        className: "components-base-control components-post-selector"
      }, wp.element.createElement("div", {
        className: "components-base-control__field"
      }, wp.element.createElement("label", {
        htmlFor: "searchinput",
        className: "components-base-control__label"
      }, wp.element.createElement(Icon, {
        icon: "search"
      })), wp.element.createElement("input", {
        className: "components-text-control__input",
        id: "searchinput",
        type: "search",
        placeholder: "Please enter your search query...",
        value: this.state.filter,
        onChange: this.handleInputFilterChange
      }), wp.element.createElement(_ItemList.ItemList, {
        items: postList,
        loading: this.state.initialLoading || this.state.loading || this.state.filterLoading,
        filtered: isFiltered,
        action: this.addPost,
        icon: addIcon
      })), wp.element.createElement("div", {
        className: "components-base-control__field--selected"
      }, wp.element.createElement("h2", null, "Selected"), wp.element.createElement(_ItemList.ItemList, {
        items: SelectedPostList,
        loading: this.state.initialLoading,
        action: this.removePost,
        icon: removeIcon
      })));
    }
  }]);

  return PostSelector;
}(Component);

exports.PostSelector = PostSelector;

},{"../utils/api":6,"../utils/useful-funcs":7,"./ItemList":3}],5:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.TermSelector = void 0;

var _ItemList = require("./ItemList");

var api = _interopRequireWildcard(require("../utils/api"));

var _usefulFuncs = require("../utils/useful-funcs");

function _interopRequireWildcard(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) { var desc = Object.defineProperty && Object.getOwnPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : {}; if (desc.get || desc.set) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } } newObj.default = obj; return newObj; } }

function _typeof(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _nonIterableSpread(); }

function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance"); }

function _iterableToArray(iter) { if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === "[object Arguments]") return Array.from(iter); }

function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; var ownKeys = Object.keys(source); if (typeof Object.getOwnPropertySymbols === 'function') { ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function (sym) { return Object.getOwnPropertyDescriptor(source, sym).enumerable; })); } ownKeys.forEach(function (key) { _defineProperty(target, key, source[key]); }); } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

var Icon = wp.components.Icon;
var Component = wp.element.Component;
/**
 * TermSelector Component
 */

var TermSelector =
/*#__PURE__*/
function (_Component) {
  _inherits(TermSelector, _Component);

  /**
   * Constructor for TermSelector Component.
   * Sets up state, and creates bindings for functions.
   * @param object props - current component properties.
   */
  function TermSelector(props) {
    var _this;

    _classCallCheck(this, TermSelector);

    _this = _possibleConstructorReturn(this, _getPrototypeOf(TermSelector).apply(this, arguments));
    _this.props = props;
    _this.state = {
      terms: [],
      loading: false,
      type: props.postType || 'post',
      taxonomy: props.taxonomy || 'category',
      taxonomies: [],
      filter: '',
      filterLoading: false,
      filterTerms: [],
      initialLoading: false
    };
    _this.addTerm = _this.addTerm.bind(_assertThisInitialized(_assertThisInitialized(_this)));
    _this.removeTerm = _this.removeTerm.bind(_assertThisInitialized(_assertThisInitialized(_this)));
    _this.handleInputFilterChange = _this.handleInputFilterChange.bind(_assertThisInitialized(_assertThisInitialized(_this)));
    _this.doTermFilter = (0, _usefulFuncs.debounce)(_this.doTermFilter.bind(_assertThisInitialized(_assertThisInitialized(_this))), 300);
    return _this;
  }
  /**
   * When the component mounts it calls this function.
   * Fetches terms taxonomies, selected terms then makes first call for terms
   */


  _createClass(TermSelector, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      var _this2 = this;

      this.setState({
        initialLoading: true
      });
      api.getTaxonomies({
        type: this.state.type
      }).then(function (response) {
        _this2.setState({
          taxonomies: response
        }, function () {
          _this2.retrieveSelectedTerms().then(function () {
            _this2.setState({
              initialLoading: false
            });
          });
        });
      });
    }
    /**
     * GetTerms wrapper, builds the request argument based state and parameters passed/
     * @param {object} args - desired arguments (can be empty).
     * @returns {Promise<T>}
     */

  }, {
    key: "getTerms",
    value: function getTerms() {
      var _this3 = this;

      var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var selectedTermIds = this.props.selectedTermIds;
      var defaultArgs = {
        per_page: 10,
        type: this.state.type,
        taxonomy: this.state.taxonomy,
        search: this.state.filter
      };

      var requestArguments = _objectSpread({}, defaultArgs, args);

      requestArguments.restBase = this.state.taxonomies[this.state.taxonomy].rest_base;
      return api.getTerms(requestArguments).then(function (response) {
        if (requestArguments.search) {
          _this3.setState({
            filterTerms: response.filter(function (_ref) {
              var id = _ref.id;
              return selectedTermIds.indexOf(id) === -1;
            })
          });

          return response;
        }

        _this3.setState({
          terms: (0, _usefulFuncs.uniqueById)([].concat(_toConsumableArray(_this3.state.terms), _toConsumableArray(response)))
        }); // return response to continue the chain


        return response;
      });
    }
    /**
     * Gets the selected terms by id from the `terms` state object and sorts them by their position in the selected array.
     * @returns Array of objects.
     */

  }, {
    key: "getSelectedTerms",
    value: function getSelectedTerms() {
      var _this4 = this;

      var selectedTermIds = this.props.selectedTermIds;
      return this.state.terms.filter(function (_ref2) {
        var id = _ref2.id;
        return selectedTermIds.indexOf(id) !== -1;
      }).sort(function (a, b) {
        var aIndex = _this4.props.selectedTermIds.indexOf(a.id);

        var bIndex = _this4.props.selectedTermIds.indexOf(b.id);

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

  }, {
    key: "retrieveSelectedTerms",
    value: function retrieveSelectedTerms() {
      var _this$props = this.props,
          termType = _this$props.termType,
          selectedTermIds = _this$props.selectedTermIds;
      var taxonomies = this.state.taxonomies;

      if (selectedTermIds && !selectedTermIds.length > 0) {
        // return a fake promise that auto resolves.
        return new Promise(function (resolve) {
          return resolve();
        });
      }

      return this.getTerms({
        include: this.props.selectedTermIds.join(','),
        per_page: 100,
        termType: termType
      });
    }
    /**
     * Adds desired term id to the selectedTermIds List
     * @param {Integer} term_id
     */

  }, {
    key: "addTerm",
    value: function addTerm(term_id) {
      if (this.state.filter) {
        var term = this.state.filterTerms.filter(function (p) {
          return p.id === term_id;
        });
        var terms = (0, _usefulFuncs.uniqueById)([].concat(_toConsumableArray(this.state.terms), _toConsumableArray(term)));
        this.setState({
          terms: terms
        });
      }

      this.props.updateSelectedTermIds([].concat(_toConsumableArray(this.props.selectedTermIds), [term_id]));
    }
    /**
     * Removes desired term id to the selectedTermIds List
     * @param {Integer} term_id
     */

  }, {
    key: "removeTerm",
    value: function removeTerm(term_id) {
      this.props.updateSelectedTermIds(_toConsumableArray(this.props.selectedTermIds).filter(function (id) {
        return id !== term_id;
      }));
    }
    /**
     * Handles the search box input value
     * @param string type - comes from the event object target.
     */

  }, {
    key: "handleInputFilterChange",
    value: function handleInputFilterChange() {
      var _this5 = this;

      var _ref3 = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {},
          _ref3$target = _ref3.target;

      _ref3$target = _ref3$target === void 0 ? {} : _ref3$target;
      var _ref3$target$value = _ref3$target.value,
          filter = _ref3$target$value === void 0 ? '' : _ref3$target$value;
      this.setState({
        filter: filter
      }, function () {
        if (!filter) {
          // remove filtered terms
          return _this5.setState({
            filteredTerms: [],
            filtering: false
          });
        }

        _this5.doTermFilter();
      });
    }
    /**
     * Actual api call for searching for query, this function is debounced in constructor.
     */

  }, {
    key: "doTermFilter",
    value: function doTermFilter() {
      var _this6 = this;

      var _this$state$filter = this.state.filter,
          filter = _this$state$filter === void 0 ? '' : _this$state$filter;

      if (!filter) {
        return;
      }

      this.setState({
        filtering: true,
        filterLoading: true
      });
      this.getTerms().then(function () {
        _this6.setState({
          filterLoading: false
        });
      });
    }
    /**
     * Renders the TermSelector component.
     */

  }, {
    key: "render",
    value: function render() {
      var isFiltered = this.state.filtering;
      var termList = isFiltered && !this.state.filterLoading ? this.state.filterTerms : [];
      var SelectedTermList = this.getSelectedTerms();
      var addIcon = wp.element.createElement(Icon, {
        icon: "plus"
      });
      var removeIcon = wp.element.createElement(Icon, {
        icon: "minus"
      });
      return wp.element.createElement("div", {
        className: "components-base-control components-term-selector"
      }, wp.element.createElement("div", {
        className: "components-base-control__field"
      }, wp.element.createElement("label", {
        htmlFor: "searchinput",
        className: "components-base-control__label"
      }, wp.element.createElement(Icon, {
        icon: "search"
      })), wp.element.createElement("input", {
        className: "components-text-control__input",
        id: "searchinput",
        type: "search",
        placeholder: "Please enter your search query...",
        value: this.state.filter,
        onChange: this.handleInputFilterChange
      }), wp.element.createElement(_ItemList.ItemList, {
        items: termList,
        loading: this.state.initialLoading || this.state.loading || this.state.filterLoading,
        filtered: isFiltered,
        action: this.addTerm,
        icon: addIcon
      })), wp.element.createElement("div", {
        className: "components-base-control__field--selected"
      }, wp.element.createElement("h2", null, "Selected"), wp.element.createElement(_ItemList.ItemList, {
        items: SelectedTermList,
        loading: this.state.initialLoading,
        action: this.removeTerm,
        icon: removeIcon
      })));
    }
  }]);

  return TermSelector;
}(Component);

exports.TermSelector = TermSelector;

},{"../utils/api":6,"../utils/useful-funcs":7,"./ItemList":3}],6:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.getTerms = exports.getTaxonomies = exports.getPosts = exports.getPostTypes = void 0;

function _extends() { _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }

function _objectWithoutProperties(source, excluded) { if (source == null) return {}; var target = _objectWithoutPropertiesLoose(source, excluded); var key, i; if (Object.getOwnPropertySymbols) { var sourceSymbolKeys = Object.getOwnPropertySymbols(source); for (i = 0; i < sourceSymbolKeys.length; i++) { key = sourceSymbolKeys[i]; if (excluded.indexOf(key) >= 0) continue; if (!Object.prototype.propertyIsEnumerable.call(source, key)) continue; target[key] = source[key]; } } return target; }

function _objectWithoutPropertiesLoose(source, excluded) { if (source == null) return {}; var target = {}; var sourceKeys = Object.keys(source); var key, i; for (i = 0; i < sourceKeys.length; i++) { key = sourceKeys[i]; if (excluded.indexOf(key) >= 0) continue; target[key] = source[key]; } return target; }

var _wp = wp,
    apiFetch = _wp.apiFetch;
/**
 * Makes a get request to the PostTypes endpoint.
 *
 * @returns {Promise<any>}
 */

var getPostTypes = function getPostTypes() {
  return apiFetch({
    path: '/wp/v2/types'
  });
};
/**
 * Makes a get request to the desired post type and builds the query string based on an object.
 *
 * @param {string|boolean} restBase - rest base for the query.
 * @param {object} args
 * @returns {Promise<any>}
 */


exports.getPostTypes = getPostTypes;

var getPosts = function getPosts(_ref) {
  var _ref$restBase = _ref.restBase,
      restBase = _ref$restBase === void 0 ? false : _ref$restBase,
      args = _objectWithoutProperties(_ref, ["restBase"]);

  var queryString = Object.keys(args).map(function (arg) {
    return "".concat(arg, "=").concat(args[arg]);
  }).join('&');
  var path = "/wp/v2/".concat(restBase, "?").concat(queryString, "&_embed");
  return apiFetch({
    path: path
  });
};
/**
 * Makes a get request to the PostType Taxonomies endpoint.
 *
 * @returns {Promise<any>}
 */


exports.getPosts = getPosts;

var getTaxonomies = function getTaxonomies(_ref2) {
  var args = _extends({}, _ref2);

  var queryString = Object.keys(args).map(function (arg) {
    return "".concat(arg, "=").concat(args[arg]);
  }).join('&');
  var path = "/wp/v2/taxonomies?".concat(queryString, "&_embed");
  return apiFetch({
    path: path
  });
};
/**
 * Makes a get request to the desired post type and builds the query string based on an object.
 *
 * @param {string|boolean} restBase - rest base for the query.
 * @param {object} args
 * @returns {Promise<any>}
 */


exports.getTaxonomies = getTaxonomies;

var getTerms = function getTerms(_ref3) {
  var _ref3$restBase = _ref3.restBase,
      restBase = _ref3$restBase === void 0 ? false : _ref3$restBase,
      args = _objectWithoutProperties(_ref3, ["restBase"]);

  var queryString = Object.keys(args).map(function (arg) {
    return "".concat(arg, "=").concat(args[arg]);
  }).join('&');
  var path = "/wp/v2/".concat(restBase, "?").concat(queryString, "&_embed");
  return apiFetch({
    path: path
  });
};

exports.getTerms = getTerms;

},{}],7:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.debounce = exports.uniqueById = exports.uniqueBy = void 0;

/**
 * Returns a unique array of objects based on a desired key.
 * @param {array} arr - array of objects.
 * @param {string|int} key - key to filter objects by
 */
var uniqueBy = function uniqueBy(arr, key) {
  var keys = [];
  return arr.filter(function (item) {
    if (keys.indexOf(item[key]) !== -1) {
      return false;
    }

    return keys.push(item[key]);
  });
};
/**
 * Returns a unique array of objects based on the id property.
 * @param {array} arr - array of objects to filter.
 * @returns {*}
 */


exports.uniqueBy = uniqueBy;

var uniqueById = function uniqueById(arr) {
  return uniqueBy(arr, 'id');
};
/**
 * Debounce a function by limiting how often it can run.
 * @param {function} func - callback function
 * @param {Integer} wait - Time in milliseconds how long it should wait.
 * @returns {Function}
 */


exports.uniqueById = uniqueById;

var debounce = function debounce(func, wait) {
  var timeout = null;
  return function () {
    var context = this;
    var args = arguments;

    var later = function later() {
      func.apply(context, args);
    };

    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
};

exports.debounce = debounce;

},{}]},{},[1])
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJhc3NldHMvZXNuZXh0L2Jsb2Nrcy9tb3ZpZXMuanMiLCJhc3NldHMvZXNuZXh0L2NvbXBvbmVudHMvSXRlbS5qcyIsImFzc2V0cy9lc25leHQvY29tcG9uZW50cy9JdGVtTGlzdC5qcyIsImFzc2V0cy9lc25leHQvY29tcG9uZW50cy9Qb3N0U2VsZWN0b3IuanMiLCJhc3NldHMvZXNuZXh0L2NvbXBvbmVudHMvVGVybVNlbGVjdG9yLmpzIiwiYXNzZXRzL2VzbmV4dC91dGlscy9hcGkuanMiLCJhc3NldHMvZXNuZXh0L3V0aWxzL3VzZWZ1bC1mdW5jcy5qcyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTs7O0FDQUE7O0FBQ0E7O0lBRVEsaUIsR0FBc0IsRUFBRSxDQUFDLE0sQ0FBekIsaUI7SUFDQSxpQixHQUFzQixFQUFFLENBQUMsTSxDQUF6QixpQjtxQkFDZ0YsRUFBRSxDQUFDLFU7SUFBbkYsZ0Isa0JBQUEsZ0I7SUFBa0IsVyxrQkFBQSxXO0lBQWEsWSxrQkFBQSxZO0lBQWMsYSxrQkFBQSxhO0lBQWUsZSxrQkFBQSxlO0FBRXBFLGlCQUFpQixDQUFFLGtCQUFGLEVBQXNCO0FBQ25DLEVBQUEsS0FBSyxFQUFFLGNBRDRCO0FBR25DLEVBQUEsSUFBSSxFQUFFLGNBSDZCO0FBS25DLEVBQUEsUUFBUSxFQUFFLGtCQUx5QjtBQU9uQyxFQUFBLElBQUksRUFBSSxjQUFFLEtBQUYsRUFBYTtBQUFBLFFBQ1QsVUFEUyxHQUNnQyxLQURoQyxDQUNULFVBRFM7QUFBQSxRQUNHLFNBREgsR0FDZ0MsS0FEaEMsQ0FDRyxTQURIO0FBQUEsUUFDYyxhQURkLEdBQ2dDLEtBRGhDLENBQ2MsYUFEZDtBQUFBLFFBRVQsS0FGUyxHQUU4RCxVQUY5RCxDQUVULEtBRlM7QUFBQSxRQUVGLE9BRkUsR0FFOEQsVUFGOUQsQ0FFRixPQUZFO0FBQUEsUUFFTyxPQUZQLEdBRThELFVBRjlELENBRU8s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LFdBQU8sSUFBSSxDQUFDLElBQUwsQ0FBVSxJQUFJLENBQUMsR0FBRCxDQUFkLENBQVA7QUFDSCxHQU5NLENBQVA7QUFPSCxDQVRNO0FBV1A7Ozs7Ozs7OztBQUtPLElBQU0sVUFBVSxHQUFHLFNBQWIsVUFBYSxDQUFBLEdBQUc7QUFBQSxTQUFJLFFBQVEsQ0FBQyxHQUFELEVBQU0sSUFBTixDQUFaO0FBQUEsQ0FBdEI7QUFFUDs7Ozs7Ozs7OztBQU1PLElBQU0sUUFBUSxHQUFHLFNBQVgsUUFBVyxDQUFDLElBQUQsRUFBTyxJQUFQLEVBQWdCO0FBQ3BDLE1BQUksT0FBTyxHQUFHLElBQWQ7QUFFQSxTQUFPLFlBQVk7QUFDZixRQUFNLE9BQU8sR0FBRyxJQUFoQjtBQUNBLFFBQU0sSUFBSSxHQUFHLFNBQWI7O0FBRUEsUUFBTSxLQUFLLEdBQUcsU0FBUixLQUFRLEdBQU07QUFDaEIsTUFBQSxJQUFJLENBQUMsS0FBTCxDQUFXLE9BQVgsRUFBb0IsSUFBcEI7QUFDSCxLQUZEOztBQUlBLElBQUEsWUFBWSxDQUFDLE9BQUQsQ0FBWjtBQUNBLElBQUEsT0FBTyxHQUFHLFVBQVUsQ0FBQyxLQUFELEVBQVEsSUFBUixDQUFwQjtBQUNILEdBVkQ7QUFXSCxDQWRNIiwiZmlsZSI6ImdlbmVyYXRlZC5qcyIsInNvdXJjZVJvb3QiOiIiLCJzb3VyY2VzQ29udGVudCI6WyIoZnVuY3Rpb24oKXtmdW5jdGlvbiByKGUsbix0KXtmdW5jdGlvbiBvKGksZil7aWYoIW5baV0pe2lmKCFlW2ldKXt2YXIgYz1cImZ1bmN0aW9uXCI9PXR5cGVvZiByZXF1aXJlJiZyZXF1aXJlO2lmKCFmJiZjKXJldHVybiBjKGksITApO2lmKHUpcmV0dXJuIHUoaSwhMCk7dmFyIGE9bmV3IEVycm9yKFwiQ2Fubm90IGZpbmQgbW9kdWxlICdcIitpK1wiJ1wiKTt0aHJvdyBhLmNvZGU9XCJNT0RVTEVfTk9UX0ZPVU5EXCIsYX12YXIgcD1uW2ldPXtleHBvcnRzOnt9fTtlW2ldWzBdLmNhbGwocC5leHBvcnRzLGZ1bmN0aW9uKHIpe3ZhciBuPWVbaV1bMV1bcl07cmV0dXJuIG8obnx8cil9LHAscC5leHBvcnRzLHIsZSxuLHQpfXJldHVybiBuW2ldLmV4cG9ydHN9Zm9yKHZhciB1PVwiZnVuY3Rpb25cIj09dHlwZW9mIHJlcXVpcmUmJnJlcXVpcmUsaT0wO2k8dC5sZW5ndGg7aSsrKW8odFtpXSk7cmV0dXJuIG99cmV0dXJuIHJ9KSgpIiwiaW1wb3J0IHsgUG9zdFNlbGVjdG9yIH0gZnJvbSAnLi4vY29tcG9uZW50cy9Qb3N0U2VsZWN0b3InO1xuaW1wb3J0IHsgVGVybVNlbGVjdG9yIH0gZnJvbSAnLi4vY29tcG9uZW50cy9UZXJtU2VsZWN0b3InO1xuXG5jb25zdCB7IHJlZ2lzdGVyQmxvY2tUeXBlIH0gPSB3cC5ibG9ja3M7XG5jb25zdCB7IEluc3BlY3RvckNvbnRyb2xzIH0gPSB3cC5lZGl0b3I7XG5jb25zdCB7IFNlcnZlclNpZGVSZW5kZXIsIFRleHRDb250cm9sLCBSYW5nZUNvbnRyb2wsIFNlbGVjdENvbnRyb2wsIENoZWNrYm94Q29udHJvbCB9ID0gd3AuY29tcG9uZW50cztcblxucmVnaXN0ZXJCbG9ja1R5cGUoICdtYXN2aWRlb3MvbW92aWVzJywge1xuICAgIHRpdGxlOiAnTW92aWVzIEJsb2NrJyxcblxuICAgIGljb246ICdmb3JtYXQtdmlkZW8nLFxuXG4gICAgY2F0ZWdvcnk6ICdtYXN2aWRlb3MtYmxvY2tzJyxcblxuICAgIGVkaXQ6ICggKCBwcm9wcyApID0+IHtcbiAgICAgICAgY29uc3QgeyBhdHRyaWJ1dGVzLCBjbGFzc05hbWUsIHNldEF0dHJpYnV0ZXMgfSA9IHByb3BzO1xuICAgICAgICBjb25zdCB7IGxpbWl0LCBjb2x1bW5zLCBvcmRlcmJ5LCBvcmRlciwgaWRzLCBjYXRlZ29yeSwgZmVhdHVyZWQsIHRvcF9yYXRlZCB9ID0gYXR0cmlidXRlcztcblxuICAgICAgICBjb25zdCBvbkNoYW5nZUxpbWl0ID0gbmV3TGltaXQgPT4ge1xuICAgICAgICAgICAgc2V0QXR0cmlidXRlcyggeyBsaW1pdDogbmV3TGltaXQgfSApO1xuICAgICAgICB9O1xuXG4gICAgICAgIGNvbnN0IG9uQ2hhbmdlQ29sdW1ucyA9IG5ld0NvbHVtbnMgPT4ge1xuICAgICAgICAgICAgc2V0QXR0cmlidXRlcyggeyBjb2x1bW5zOiBuZXdDb2x1bW5zIH0gKTtcbiAgICAgICAgfTtcblxuICAgICAgICBjb25zdCBvbkNoYW5nZU9yZGVyYnkgPSBuZXdPcmRlcmJ5ID0+IHtcbiAgICAgICAgICAgIHNldEF0dHJpYnV0ZXMoIHsgb3JkZXJieTogbmV3T3JkZXJieSB9ICk7XG4gICAgICAgIH07XG5cbiAgICAgICAgY29uc3Qgb25DaGFuZ2VPcmRlciA9IG5ld09yZGVyID0+IHtcbiAgICAgICAgICAgIHNldEF0dHJpYnV0ZXMoIHsgb3JkZXI6IG5ld09yZGVyIH0gKTtcbiAgICAgICAgfTtcblxuICAgICAgICBjb25zdCBvbkNoYW5nZUlkcyA9IG5ld0lkcyA9PiB7XG4gICAgICAgICAgICBzZXRBdHRyaWJ1dGVzKCB7IGlkczogbmV3SWRzLmpvaW4oJywnKSB9ICk7XG4gICAgICAgIH07XG5cbiAgICAgICAgY29uc3Qgb25DaGFuZ2VDYXRlZ29yeSA9IG5ld0NhdGVnb3J5ID0+IHtcbiAgICAgICAgICAgIHNldEF0dHJpYnV0ZXMoIHsgY2F0ZWdvcnk6IG5ld0NhdGVnb3J5LmpvaW4oJywnKSB9ICk7XG4gICAgICAgIH07XG5cbiAgICAgICAgY29uc3Qgb25DaGFuZ2VGZWF0dXJlZCA9IG5ld0ZlYXR1cmVkID0+IHtcbiAgICAgICAgICAgIHNldEF0dHJpYnV0ZXMoIHsgZmVhdHVyZWQ6IG5ld0ZlYXR1cmVkIH0gKTtcbiAgICAgICAgfTtcblxuICAgICAgICBjb25zdCBvbkNoYW5nZVRvcFJhdGVkID0gbmV3VG9wUmF0ZWQgPT4ge1xuICAgICAgICAgICAgc2V0QXR0cmlidXRlcyggeyB0b3BfcmF0ZWQ6IG5ld1RvcFJhdGVkIH0gKTtcbiAgICAgICAgfTtcblxuICAgICAgICByZXR1cm4gW1xuICAgICAgICAgICAgPEluc3BlY3RvckNvbnRyb2xzPlxuICAgICAgICAgICAgICAgIDxSYW5nZUNvbnRyb2xcbiAgICAgICAgICAgICAgICAgICAgbGFiZWw9XCJMaW1pdFwiXG4gICAgICAgICAgICAgICAgICAgIHZhbHVlPXsgbGltaXQgfVxuICAgICAgICAgICAgICAgICAgICBvbkNoYW5nZT17IG9uQ2hhbmdlTGltaXQgfVxuICAgICAgICAgICAgICAgICAgICBtaW49eyAxIH1cbiAgICAgICAgICAgICAgICAgICAgbWF4PXsgNTAgfVxuICAgICAgICAgICAgICAgIC8+XG4gICAgICAgICAgICAgICAgPFJhbmdlQ29udHJvbFxuICAgICAgICAgICAgICAgICAgICBsYWJlbD1cIkNvbHVtbnNcIlxuICAgICAgICAgICAgICAgICAgICB2YWx1ZT17IGNvbHVtbnMgfVxuICAgICAgICAgICAgICAgICAgICBvbkNoYW5nZT17IG9uQ2hhbmdlQ29sdW1ucyB9XG4gICAgICAgICAgICAgICAgICAgIG1pbj17IDEgfVxuICAgICAgICAgICAgICAgICAgICBtYXg9eyAxMCB9XG4gICAgICAgICAgICAgICAgLz5cbiAgICAgICAgICAgICAgICA8U2VsZWN0Q29udHJvbFxuICAgICAgICAgICAgICAgICAgICBsYWJlbD1cIk9yZGVyYnlcIlxuICAgICAgICAgICAgICAgICAgICB2YWx1ZT17IG9yZGVyYnkgfVxuICAgICAgICAgICAgICAgICAgICBvcHRpb25zPXsgW1xuICAgICAgICAgICAgICAgICAgICAgICAgeyBsYWJlbDogJ1RpdGxlJywgdmFsdWU6ICd0aXRsZScgfSxcbiAgICAgICAgICAgICAgICAgICAgICAgIHsgbGFiZWw6ICdEYXRlJywgdmFsdWU6ICdkYXRlJyB9LFxuICAgICAgICAgICAgICAgICAgICAgICAgeyBsYWJlbDogJ0lEJywgdmFsdWU6ICdpZCcgfSxcbiAgICAgICAgICAgICAgICAgICAgICAgIHsgbGFiZWw6ICdSYW5kb20nLCB2YWx1ZTogJ3JhbmQnIH0sXG4gICAgICAgICAgICAgICAgICAgIF0gfVxuICAgICAgICAgICAgICAgICAgICBvbkNoYW5nZT17IG9uQ2hhbmdlT3JkZXJieSB9XG4gICAgICAgICAgICAgICAgLz5cbiAgICAgICAgICAgICAgICA8U2VsZWN0Q29udHJvbFxuICAgICAgICAgICAgICAgICAgICBsYWJlbD1cIk9yZGVyXCJcbiAgICAgICAgICAgICAgICAgICAgdmFsdWU9eyBvcmRlciB9XG4gICAgICAgICAgICAgICAgICAgIG9wdGlvbnM9eyBbXG4gICAgICAgICAgICAgICAgICAgICAgICB7IGxhYmVsOiAnQVNDJywgdmFsdWU6ICdBU0MnIH0sXG4gICAgICAgICAgICAgICAgICAgICAgICB7IGxhYmVsOiAnREVTQycsIHZhbHVlOiAnREVTQycgfSxcbiAgICAgICAgICAgICAgICAgICAgXSB9XG4gICAgICAgICAgICAgICAgICAgIG9uQ2hhbmdlPXsgb25DaGFuZ2VPcmRlciB9XG4gICAgICAgICAgICAgICAgLz5cbiAgICAgICAgICAgICAgICA8UG9zdFNlbGVjdG9yXG4gICAgICAgICAgICAgICAgICAgIHBvc3RUeXBlID0gJ21vdmllJ1xuICAgICAgICAgICAgICAgICAgICBzZWxlY3RlZFBvc3RJZHM9eyBpZHMgPyBpZHMuc3BsaXQoJywnKS5tYXAoTnVtYmVyKSA6IFtdIH1cbiAgICAgICAgICAgICAgICAgICAgdXBkYXRlU2VsZWN0ZWRQb3N0SWRzPXsgb25DaGFuZ2VJZHMgfVxuICAgICAgICAgICAgICAgIC8+XG4gICAgICAgICAgICAgICAgPFRlcm1TZWxlY3RvclxuICAgICAgICAgICAgICAgICAgICBwb3N0VHlwZSA9ICdtb3ZpZSdcbiAgICAgICAgICAgICAgICAgICAgdGF4b25vbXkgPSAnbW92aWVfY2F0J1xuICAgICAgICAgICAgICAgICAgICBzZWxlY3RlZFRlcm1JZHM9eyBjYXRlZ29yeSA/IGNhdGVnb3J5LnNwbGl0KCcsJykubWFwKE51bWJlcikgOiBbXSB9XG4gICAgICAgICAgICAgICAgICAgIHVwZGF0ZVNlbGVjdGVkVGVybUlkcz17IG9uQ2hhbmdlQ2F0ZWdvcnkgfVxuICAgICAgICAgICAgICAgIC8+XG4gICAgICAgICAgICAgICAgPENoZWNrYm94Q29udHJvbFxuICAgICAgICAgICAgICAgICAgICBsYWJlbD1cIkZlYXR1cmVkXCJcbiAgICAgICAgICAgICAgICAgICAgaGVscD1cIkNoZWNrIHRvIHNlbGVjdCBmZWF0dXJlZCBtb3ZpZXMuXCJcbiAgICAgICAgICAgICAgICAgICAgY2hlY2tlZD17IGZlYXR1cmVkIH1cbiAgICAgICAgICAgICAgICAgICAgb25DaGFuZ2U9eyBvbkNoYW5nZUZlYXR1cmVkIH1cbiAgICAgICAgICAgICAgICAvPlxuICAgICAgICAgICAgICAgIDxDaGVja2JveENvbnRyb2xcbiAgICAgICAgICAgICAgICAgICAgbGFiZWw9XCJUb3AgUmF0ZWRcIlxuICAgICAgICAgICAgICAgICAgICBoZWxwPVwiQ2hlY2sgdG8gc2VsZWN0IHRvcCByYXRlZCBtb3ZpZXMuXCJcbiAgICAgICAgICAgICAgICAgICAgY2hlY2tlZD17IHRvcF9yYXRlZCB9XG4gICAgICAgICAgICAgICAgICAgIG9uQ2hhbmdlPXsgb25DaGFuZ2VUb3BSYXRlZCB9XG4gICAgICAgICAgICAgICAgLz5cbiAgICAgICAgICAgIDwvSW5zcGVjdG9yQ29udHJvbHM+LFxuICAgICAgICAgICAgPFNlcnZlclNpZGVSZW5kZXJcbiAgICAgICAgICAgICAgICBibG9jaz1cIm1hc3ZpZGVvcy9tb3ZpZXNcIlxuICAgICAgICAgICAgICAgIGF0dHJpYnV0ZXM9eyBhdHRyaWJ1dGVzIH1cbiAgICAgICAgICAgIC8+XG4gICAgICAgIF07XG4gICAgfSApLFxuXG4gICAgc2F2ZSgpIHtcbiAgICAgICAgLy8gUmVuZGVyaW5nIGluIFBIUFxuICAgICAgICByZXR1cm4gbnVsbDtcbiAgICB9LFxufSApOyIsIlxuLyoqXG4gKiBJdGVtIENvbXBvbmVudC5cbiAqXG4gKiBAcGFyYW0ge3N0cmluZ30gaXRlbVRpdGxlIC0gQ3VycmVudCBpdGVtIHRpdGxlLlxuICogQHBhcmFtIHtmdW5jdGlvbn0gY2xpY2tIYW5kbGVyIC0gdGhpcyBpcyB0aGUgaGFuZGxpbmcgZnVuY3Rpb24gZm9yIHRoZSBhZGQvcmVtb3ZlIGZ1bmN0aW9uXG4gKiBAcGFyYW0ge0ludGVnZXJ9IGl0ZW1JZCAtIEN1cnJlbnQgaXRlbSBJRFxuICogQHBhcmFtIGljb25cbiAqIEByZXR1cm5zIHsqfSBJdGVtIEhUTUwuXG4gKi9cbmV4cG9ydCBjb25zdCBJdGVtID0gKHsgdGl0bGU6IHsgcmVuZGVyZWQ6IGl0ZW1UaXRsZSB9ID0ge30sIG5hbWUsIGNsaWNrSGFuZGxlciwgaWQ6IGl0ZW1JZCwgaWNvbiB9KSA9PiAoXG4gICAgPGFydGljbGUgY2xhc3NOYW1lPVwiaXRlbVwiPlxuICAgICAgICA8ZGl2IGNsYXNzTmFtZT1cIml0ZW0tYm9keVwiPlxuICAgICAgICAgICAgPGgzIGNsYXNzTmFtZT1cIml0ZW0tdGl0bGVcIj57aXRlbVRpdGxlfXtuYW1lfTwvaDM+XG4gICAgICAgIDwvZGl2PlxuICAgICAgICA8YnV0dG9uIG9uQ2xpY2s9eygpID0+IGNsaWNrSGFuZGxlcihpdGVtSWQpfT57aWNvbn08L2J1dHRvbj5cbiAgICA8L2FydGljbGU+XG4pOyIsImltcG9ydCB7IEl0ZW0gfSBmcm9tICcuL0l0ZW0nO1xuXG4vKipcbiAqIEl0ZW1MaXN0IENvbXBvbmVudFxuICogQHBhcmFtIG9iamVjdCBwcm9wcyAtIENvbXBvbmVudCBwcm9wcy5cbiAqIEByZXR1cm5zIHsqfVxuICogQGNvbnN0cnVjdG9yXG4gKi9cbmV4cG9ydCBjb25zdCBJdGVtTGlzdCA9IHByb3BzID0+IHtcbiAgICBjb25zdCB7IGZpbHRlcmVkID0gZmFsc2UsIGxvYWRpbmcgPSBmYWxzZSwgaXRlbXMgPSBbXSwgYWN0aW9uID0gKCkgPT4ge30sIGljb24gPSBudWxsIH0gPSBwcm9wcztcblxuICAgIGlmIChsb2FkaW5nKSB7XG4gICAgICAgIHJldHVybiA8cCBjbGFzc05hbWU9XCJsb2FkaW5nLWl0ZW1zXCI+TG9hZGluZyAuLi48L3A+O1xuICAgIH1cblxuICAgIGlmIChmaWx0ZXJlZCAmJiBpdGVtcy5sZW5ndGggPCAxKSB7XG4gICAgICAgIHJldHVybiAoXG4gICAgICAgICAgICA8ZGl2IGNsYXNzTmFtZT1cIml0ZW0tbGlzdFwiPlxuICAgICAgICAgICAgICAgIDxwPllvdXIgcXVlcnkgeWllbGRlZCBubyByZXN1bHRzLCBwbGVhc2UgdHJ5IGFnYWluLjwvcD5cbiAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICApO1xuICAgIH1cblxuICAgIGlmICggISBpdGVtcyB8fCBpdGVtcy5sZW5ndGggPCAxICkge1xuICAgICAgICByZXR1cm4gPHAgY2xhc3NOYW1lPVwibm8taXRlbXNcIj5Ob3QgZm91bmQuPC9wPlxuICAgIH1cblxuICAgIHJldHVybiAoXG4gICAgICAgIDxkaXYgY2xhc3M9XCJpdGVtLWxpc3RcIj5cbiAgICAgICAgICAgIHtpdGVtcy5tYXAoKGl0ZW0pID0+IDxJdGVtIGtleT17aXRlbS5pZH0gey4uLml0ZW19IGNsaWNrSGFuZGxlcj17YWN0aW9ufSBpY29uPXtpY29ufSAvPil9XG4gICAgICAgIDwvZGl2PlxuICAgICk7XG59OyIsImltcG9ydCB7IEl0ZW1MaXN0IH0gZnJvbSBcIi4vSXRlbUxpc3RcIjtcbmltcG9ydCAqIGFzIGFwaSBmcm9tICcuLi91dGlscy9hcGknO1xuaW1wb3J0IHsgdW5pcXVlQnlJZCwgZGVib3VuY2UgfSBmcm9tICcuLi91dGlscy91c2VmdWwtZnVuY3MnO1xuXG5jb25zdCB7IEljb24gfSA9IHdwLmNvbXBvbmVudHM7XG5jb25zdCB7IENvbXBvbmVudCB9ID0gd3AuZWxlbWVudDtcblxuLyoqXG4gKiBQb3N0U2VsZWN0b3IgQ29tcG9uZW50XG4gKi9cbmV4cG9ydCBjbGFzcyBQb3N0U2VsZWN0b3IgZXh0ZW5kcyBDb21wb25lbnQge1xuICAgIC8qKlxuICAgICAqIENvbnN0cnVjdG9yIGZvciBQb3N0U2VsZWN0b3IgQ29tcG9uZW50LlxuICAgICAqIFNldHMgdXAgc3RhdGUsIGFuZCBjcmVhdGVzIGJpbmRpbmdzIGZvciBmdW5jdGlvbnMuXG4gICAgICogQHBhcmFtIG9iamVjdCBwcm9wcyAtIGN1cnJlbnQgY29tcG9uZW50IHByb3BlcnRpZXMuXG4gICAgICovXG4gICAgY29uc3RydWN0b3IocHJvcHMpIHtcbiAgICAgICAgc3VwZXIoLi4uYXJndW1lbnRzKTtcbiAgICAgICAgdGhpcy5wcm9wcyA9IHByb3BzO1xuXG4gICAgICAgIHRoaXMuc3RhdGUgPSB7XG4gICAgICAgICAgICBwb3N0czogW10sXG4gICAgICAgICAgICBsb2FkaW5nOiBmYWxzZSxcbiAgICAgICAgICAgIHR5cGU6IHByb3BzLnBvc3RUeXBlIHx8ICdwb3N0JyxcbiAgICAgICAgICAgIHR5cGVzOiBbXSxcbiAgICAgICAgICAgIGZpbHRlcjogJycsXG4gICAgICAgICAgICBmaWx0ZXJMb2FkaW5nOiBmYWxzZSxcbiAgICAgICAgICAgIGZpbHRlclBvc3RzOiBbXSxcbiAgICAgICAgICAgIGluaXRpYWxMb2FkaW5nOiBmYWxzZSxcbiAgICAgICAgfTtcblxuICAgICAgICB0aGlzLmFkZFBvc3QgPSB0aGlzLmFkZFBvc3QuYmluZCh0aGlzKTtcbiAgICAgICAgdGhpcy5yZW1vdmVQb3N0ID0gdGhpcy5yZW1vdmVQb3N0LmJpbmQodGhpcyk7XG4gICAgICAgIHRoaXMuaGFuZGxlSW5wdXRGaWx0ZXJDaGFuZ2UgPSB0aGlzLmhhbmRsZUlucHV0RmlsdGVyQ2hhbmdlLmJpbmQodGhpcyk7XG4gICAgICAgIHRoaXMuZG9Qb3N0RmlsdGVyID0gZGVib3VuY2UodGhpcy5kb1Bvc3RGaWx0ZXIuYmluZCh0aGlzKSwgMzAwKTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBXaGVuIHRoZSBjb21wb25lbnQgbW91bnRzIGl0IGNhbGxzIHRoaXMgZnVuY3Rpb24uXG4gICAgICogRmV0Y2hlcyBwb3N0cyB0eXBlcywgc2VsZWN0ZWQgcG9zdHMgdGhlbiBtYWtlcyBmaXJzdCBjYWxsIGZvciBwb3N0c1xuICAgICAqL1xuICAgIGNvbXBvbmVudERpZE1vdW50KCkge1xuICAgICAgICB0aGlzLnNldFN0YXRlKHtcbiAgICAgICAgICAgIGluaXRpYWxMb2FkaW5nOiB0cnVlLFxuICAgICAgICB9KTtcblxuICAgICAgICBhcGkuZ2V0UG9zdFR5cGVzKClcbiAgICAgICAgICAgIC50aGVuKCggcmVzcG9uc2UgKSA9PiB7XG4gICAgICAgICAgICAgICAgdGhpcy5zZXRTdGF0ZSh7XG4gICAgICAgICAgICAgICAgICAgIHR5cGVzOiByZXNwb25zZVxuICAgICAgICAgICAgICAgIH0sICgpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5yZXRyaWV2ZVNlbGVjdGVkUG9zdHMoKVxuICAgICAgICAgICAgICAgICAgICAgICAgLnRoZW4oKCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMuc2V0U3RhdGUoe1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBpbml0aWFsTG9hZGluZzogZmFsc2UsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9KVxuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogR2V0UG9zdHMgd3JhcHBlciwgYnVpbGRzIHRoZSByZXF1ZXN0IGFyZ3VtZW50IGJhc2VkIHN0YXRlIGFuZCBwYXJhbWV0ZXJzIHBhc3NlZC9cbiAgICAgKiBAcGFyYW0ge29iamVjdH0gYXJncyAtIGRlc2lyZWQgYXJndW1lbnRzIChjYW4gYmUgZW1wdHkpLlxuICAgICAqIEByZXR1cm5zIHtQcm9taXNlPFQ+fVxuICAgICAqL1xuICAgIGdldFBvc3RzKGFyZ3MgPSB7fSkge1xuICAgICAgICBjb25zdCB7IHNlbGVjdGVkUG9zdElkcyB9ID0gdGhpcy5wcm9wcztcblxuICAgICAgICBjb25zdCBkZWZhdWx0QXJncyA9IHtcbiAgICAgICAgICAgIHBlcl9wYWdlOiAxMCxcbiAgICAgICAgICAgIHR5cGU6IHRoaXMuc3RhdGUudHlwZSxcbiAgICAgICAgICAgIHNlYXJjaDogdGhpcy5zdGF0ZS5maWx0ZXIsXG4gICAgICAgIH07XG5cbiAgICAgICAgY29uc3QgcmVxdWVzdEFyZ3VtZW50cyA9IHtcbiAgICAgICAgICAgIC4uLmRlZmF1bHRBcmdzLFxuICAgICAgICAgICAgLi4uYXJnc1xuICAgICAgICB9O1xuXG4gICAgICAgIHJlcXVlc3RBcmd1bWVudHMucmVzdEJhc2UgPSB0aGlzLnN0YXRlLnR5cGVzW3RoaXMuc3RhdGUudHlwZV0ucmVzdF9iYXNlO1xuXG4gICAgICAgIHJldHVybiBhcGkuZ2V0UG9zdHMocmVxdWVzdEFyZ3VtZW50cylcbiAgICAgICAgICAgIC50aGVuKHJlc3BvbnNlID0+IHtcbiAgICAgICAgICAgICAgICBpZiAocmVxdWVzdEFyZ3VtZW50cy5zZWFyY2gpIHtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5zZXRTdGF0ZSh7XG4gICAgICAgICAgICAgICAgICAgICAgICBmaWx0ZXJQb3N0czogcmVzcG9uc2UuZmlsdGVyKCh7IGlkIH0pID0+IHNlbGVjdGVkUG9zdElkcy5pbmRleE9mKGlkKSA9PT0gLTEpLFxuICAgICAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gcmVzcG9uc2U7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgdGhpcy5zZXRTdGF0ZSh7XG4gICAgICAgICAgICAgICAgICAgIHBvc3RzOiB1bmlxdWVCeUlkKFsuLi50aGlzLnN0YXRlLnBvc3RzLCAuLi5yZXNwb25zZV0pLFxuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgLy8gcmV0dXJuIHJlc3BvbnNlIHRvIGNvbnRpbnVlIHRoZSBjaGFpblxuICAgICAgICAgICAgICAgIHJldHVybiByZXNwb25zZTtcbiAgICAgICAgICAgIH0pO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEdldHMgdGhlIHNlbGVjdGVkIHBvc3RzIGJ5IGlkIGZyb20gdGhlIGBwb3N0c2Agc3RhdGUgb2JqZWN0IGFuZCBzb3J0cyB0aGVtIGJ5IHRoZWlyIHBvc2l0aW9uIGluIHRoZSBzZWxlY3RlZCBhcnJheS5cbiAgICAgKiBAcmV0dXJucyBBcnJheSBvZiBvYmplY3RzLlxuICAgICAqL1xuICAgIGdldFNlbGVjdGVkUG9zdHMoKSB7XG4gICAgICAgIGNvbnN0IHsgc2VsZWN0ZWRQb3N0SWRzIH0gPSB0aGlzLnByb3BzO1xuICAgICAgICByZXR1cm4gdGhpcy5zdGF0ZS5wb3N0c1xuICAgICAgICAgICAgLmZpbHRlcigoeyBpZCB9KSA9PiBzZWxlY3RlZFBvc3RJZHMuaW5kZXhPZihpZCkgIT09IC0xKVxuICAgICAgICAgICAgLnNvcnQoKGEsIGIpID0+IHtcbiAgICAgICAgICAgICAgICBjb25zdCBhSW5kZXggPSB0aGlzLnByb3BzLnNlbGVjdGVkUG9zdElkcy5pbmRleE9mKGEuaWQpO1xuICAgICAgICAgICAgICAgIGNvbnN0IGJJbmRleCA9IHRoaXMucHJvcHMuc2VsZWN0ZWRQb3N0SWRzLmluZGV4T2YoYi5pZCk7XG5cbiAgICAgICAgICAgICAgICBpZiAoYUluZGV4ID4gYkluZGV4KSB7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybiAxO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIGlmIChhSW5kZXggPCBiSW5kZXgpIHtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIC0xO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIHJldHVybiAwO1xuICAgICAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogTWFrZXMgdGhlIG5lY2Vzc2FyeSBhcGkgY2FsbHMgdG8gZmV0Y2ggdGhlIHNlbGVjdGVkIHBvc3RzIGFuZCByZXR1cm5zIGEgcHJvbWlzZS5cbiAgICAgKiBAcmV0dXJucyB7Kn1cbiAgICAgKi9cbiAgICByZXRyaWV2ZVNlbGVjdGVkUG9zdHMoKSB7XG4gICAgICAgIGNvbnN0IHsgcG9zdFR5cGUsIHNlbGVjdGVkUG9zdElkcyB9ID0gdGhpcy5wcm9wcztcbiAgICAgICAgY29uc3QgeyB0eXBlcyB9ID0gdGhpcy5zdGF0ZTtcblxuICAgICAgICBpZiAoIHNlbGVjdGVkUG9zdElkcyAmJiAhc2VsZWN0ZWRQb3N0SWRzLmxlbmd0aCA+IDAgKSB7XG4gICAgICAgICAgICAvLyByZXR1cm4gYSBmYWtlIHByb21pc2UgdGhhdCBhdXRvIHJlc29sdmVzLlxuICAgICAgICAgICAgcmV0dXJuIG5ldyBQcm9taXNlKChyZXNvbHZlKSA9PiByZXNvbHZlKCkpO1xuICAgICAgICB9XG5cbiAgICAgICAgcmV0dXJuIHRoaXMuZ2V0UG9zdHMoe1xuICAgICAgICAgICAgaW5jbHVkZTogdGhpcy5wcm9wcy5zZWxlY3RlZFBvc3RJZHMuam9pbignLCcpLFxuICAgICAgICAgICAgcGVyX3BhZ2U6IDEwMCxcbiAgICAgICAgICAgIHBvc3RUeXBlXG4gICAgICAgIH0pO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEFkZHMgZGVzaXJlZCBwb3N0IGlkIHRvIHRoZSBzZWxlY3RlZFBvc3RJZHMgTGlzdFxuICAgICAqIEBwYXJhbSB7SW50ZWdlcn0gcG9zdF9pZFxuICAgICAqL1xuICAgIGFkZFBvc3QocG9zdF9pZCkge1xuICAgICAgICBpZiAodGhpcy5zdGF0ZS5maWx0ZXIpIHtcbiAgICAgICAgICAgIGNvbnN0IHBvc3QgPSB0aGlzLnN0YXRlLmZpbHRlclBvc3RzLmZpbHRlcihwID0+IHAuaWQgPT09IHBvc3RfaWQpO1xuICAgICAgICAgICAgY29uc3QgcG9zdHMgPSB1bmlxdWVCeUlkKFtcbiAgICAgICAgICAgICAgICAuLi50aGlzLnN0YXRlLnBvc3RzLFxuICAgICAgICAgICAgICAgIC4uLnBvc3RcbiAgICAgICAgICAgIF0pO1xuXG4gICAgICAgICAgICB0aGlzLnNldFN0YXRlKHtcbiAgICAgICAgICAgICAgICBwb3N0c1xuICAgICAgICAgICAgfSk7XG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLnByb3BzLnVwZGF0ZVNlbGVjdGVkUG9zdElkcyhbXG4gICAgICAgICAgICAuLi50aGlzLnByb3BzLnNlbGVjdGVkUG9zdElkcyxcbiAgICAgICAgICAgIHBvc3RfaWRcbiAgICAgICAgXSk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogUmVtb3ZlcyBkZXNpcmVkIHBvc3QgaWQgdG8gdGhlIHNlbGVjdGVkUG9zdElkcyBMaXN0XG4gICAgICogQHBhcmFtIHtJbnRlZ2VyfSBwb3N0X2lkXG4gICAgICovXG4gICAgcmVtb3ZlUG9zdChwb3N0X2lkKSB7XG4gICAgICAgIHRoaXMucHJvcHMudXBkYXRlU2VsZWN0ZWRQb3N0SWRzKFtcbiAgICAgICAgICAgIC4uLnRoaXMucHJvcHMuc2VsZWN0ZWRQb3N0SWRzXG4gICAgICAgIF0uZmlsdGVyKGlkID0+IGlkICE9PSBwb3N0X2lkKSk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogSGFuZGxlcyB0aGUgc2VhcmNoIGJveCBpbnB1dCB2YWx1ZVxuICAgICAqIEBwYXJhbSBzdHJpbmcgdHlwZSAtIGNvbWVzIGZyb20gdGhlIGV2ZW50IG9iamVjdCB0YXJnZXQuXG4gICAgICovXG4gICAgaGFuZGxlSW5wdXRGaWx0ZXJDaGFuZ2UoeyB0YXJnZXQ6IHsgdmFsdWU6ZmlsdGVyID0gJycgfSA9IHt9IH0gPSB7fSkge1xuICAgICAgICB0aGlzLnNldFN0YXRlKHtcbiAgICAgICAgICAgIGZpbHRlclxuICAgICAgICB9LCAoKSA9PiB7XG4gICAgICAgICAgICBpZiAoIWZpbHRlcikge1xuICAgICAgICAgICAgICAgIC8vIHJlbW92ZSBmaWx0ZXJlZCBwb3N0c1xuICAgICAgICAgICAgICAgIHJldHVybiB0aGlzLnNldFN0YXRlKHsgZmlsdGVyZWRQb3N0czogW10sIGZpbHRlcmluZzogZmFsc2UgfSk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHRoaXMuZG9Qb3N0RmlsdGVyKCk7XG4gICAgICAgIH0pXG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogQWN0dWFsIGFwaSBjYWxsIGZvciBzZWFyY2hpbmcgZm9yIHF1ZXJ5LCB0aGlzIGZ1bmN0aW9uIGlzIGRlYm91bmNlZCBpbiBjb25zdHJ1Y3Rvci5cbiAgICAgKi9cbiAgICBkb1Bvc3RGaWx0ZXIoKSB7XG4gICAgICAgIGNvbnN0IHsgZmlsdGVyID0gJycgfSA9IHRoaXMuc3RhdGU7XG5cbiAgICAgICAgaWYgKCFmaWx0ZXIpIHtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIHRoaXMuc2V0U3RhdGUoe1xuICAgICAgICAgICAgZmlsdGVyaW5nOiB0cnVlLFxuICAgICAgICAgICAgZmlsdGVyTG9hZGluZzogdHJ1ZVxuICAgICAgICB9KTtcblxuICAgICAgICB0aGlzLmdldFBvc3RzKClcbiAgICAgICAgICAgIC50aGVuKCgpID0+IHtcbiAgICAgICAgICAgICAgICB0aGlzLnNldFN0YXRlKHtcbiAgICAgICAgICAgICAgICAgICAgZmlsdGVyTG9hZGluZzogZmFsc2VcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH0pO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFJlbmRlcnMgdGhlIFBvc3RTZWxlY3RvciBjb21wb25lbnQuXG4gICAgICovXG4gICAgcmVuZGVyKCkge1xuICAgICAgICBjb25zdCBpc0ZpbHRlcmVkID0gdGhpcy5zdGF0ZS5maWx0ZXJpbmc7XG4gICAgICAgIGNvbnN0IHBvc3RMaXN0ID0gaXNGaWx0ZXJlZCAmJiAhdGhpcy5zdGF0ZS5maWx0ZXJMb2FkaW5nID8gdGhpcy5zdGF0ZS5maWx0ZXJQb3N0cyA6IFtdO1xuICAgICAgICBjb25zdCBTZWxlY3RlZFBvc3RMaXN0ICA9IHRoaXMuZ2V0U2VsZWN0ZWRQb3N0cygpO1xuXG4gICAgICAgIGNvbnN0IGFkZEljb24gPSA8SWNvbiBpY29uPVwicGx1c1wiIC8+O1xuICAgICAgICBjb25zdCByZW1vdmVJY29uID0gPEljb24gaWNvbj1cIm1pbnVzXCIgLz47XG5cbiAgICAgICAgcmV0dXJuIChcbiAgICAgICAgICAgIDxkaXYgY2xhc3NOYW1lPVwiY29tcG9uZW50cy1iYXNlLWNvbnRyb2wgY29tcG9uZW50cy1wb3N0LXNlbGVjdG9yXCI+XG4gICAgICAgICAgICAgICAgPGRpdiBjbGFzc05hbWU9XCJjb21wb25lbnRzLWJhc2UtY29udHJvbF9fZmllbGRcIj5cbiAgICAgICAgICAgICAgICAgICAgPGxhYmVsIGh0bWxGb3I9XCJzZWFyY2hpbnB1dFwiIGNsYXNzTmFtZT1cImNvbXBvbmVudHMtYmFzZS1jb250cm9sX19sYWJlbFwiPlxuICAgICAgICAgICAgICAgICAgICAgICAgPEljb24gaWNvbj1cInNlYXJjaFwiIC8+XG4gICAgICAgICAgICAgICAgICAgIDwvbGFiZWw+XG4gICAgICAgICAgICAgICAgICAgIDxpbnB1dFxuICAgICAgICAgICAgICAgICAgICAgICAgY2xhc3NOYW1lPVwiY29tcG9uZW50cy10ZXh0LWNvbnRyb2xfX2lucHV0XCJcbiAgICAgICAgICAgICAgICAgICAgICAgIGlkPVwic2VhcmNoaW5wdXRcIlxuICAgICAgICAgICAgICAgICAgICAgICAgdHlwZT1cInNlYXJjaFwiXG4gICAgICAgICAgICAgICAgICAgICAgICBwbGFjZWhvbGRlcj17XCJQbGVhc2UgZW50ZXIgeW91ciBzZWFyY2ggcXVlcnkuLi5cIn1cbiAgICAgICAgICAgICAgICAgICAgICAgIHZhbHVlPXt0aGlzLnN0YXRlLmZpbHRlcn1cbiAgICAgICAgICAgICAgICAgICAgICAgIG9uQ2hhbmdlPXt0aGlzLmhhbmRsZUlucHV0RmlsdGVyQ2hhbmdlfVxuICAgICAgICAgICAgICAgICAgICAvPlxuICAgICAgICAgICAgICAgICAgICA8SXRlbUxpc3RcbiAgICAgICAgICAgICAgICAgICAgICAgIGl0ZW1zPXtwb3N0TGlzdH1cbiAgICAgICAgICAgICAgICAgICAgICAgIGxvYWRpbmc9e3RoaXMuc3RhdGUuaW5pdGlhbExvYWRpbmd8fHRoaXMuc3RhdGUubG9hZGluZ3x8dGhpcy5zdGF0ZS5maWx0ZXJMb2FkaW5nfVxuICAgICAgICAgICAgICAgICAgICAgICAgZmlsdGVyZWQ9e2lzRmlsdGVyZWR9XG4gICAgICAgICAgICAgICAgICAgICAgICBhY3Rpb249e3RoaXMuYWRkUG9zdH1cbiAgICAgICAgICAgICAgICAgICAgICAgIGljb249e2FkZEljb259XG4gICAgICAgICAgICAgICAgICAgIC8+XG4gICAgICAgICAgICAgICAgPC9kaXY+XG4gICAgICAgICAgICAgICAgPGRpdiBjbGFzc05hbWU9XCJjb21wb25lbnRzLWJhc2UtY29udHJvbF9fZmllbGQtLXNlbGVjdGVkXCI+XG4gICAgICAgICAgICAgICAgICAgIDxoMj5TZWxlY3RlZDwvaDI+XG4gICAgICAgICAgICAgICAgICAgIDxJdGVtTGlzdFxuICAgICAgICAgICAgICAgICAgICAgICAgaXRlbXM9e1NlbGVjdGVkUG9zdExpc3R9XG4gICAgICAgICAgICAgICAgICAgICAgICBsb2FkaW5nPXt0aGlzLnN0YXRlLmluaXRpYWxMb2FkaW5nfVxuICAgICAgICAgICAgICAgICAgICAgICAgYWN0aW9uPXt0aGlzLnJlbW92ZVBvc3R9XG4gICAgICAgICAgICAgICAgICAgICAgICBpY29uPXtyZW1vdmVJY29ufVxuICAgICAgICAgICAgICAgICAgICAvPlxuICAgICAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICAgICAgPC9kaXY+XG4gICAgICAgICk7XG4gICAgfVxufSIsImltcG9ydCB7IEl0ZW1MaXN0IH0gZnJvbSBcIi4vSXRlbUxpc3RcIjtcbmltcG9ydCAqIGFzIGFwaSBmcm9tICcuLi91dGlscy9hcGknO1xuaW1wb3J0IHsgdW5pcXVlQnlJZCwgZGVib3VuY2UgfSBmcm9tICcuLi91dGlscy91c2VmdWwtZnVuY3MnO1xuXG5jb25zdCB7IEljb24gfSA9IHdwLmNvbXBvbmVudHM7XG5jb25zdCB7IENvbXBvbmVudCB9ID0gd3AuZWxlbWVudDtcblxuLyoqXG4gKiBUZXJtU2VsZWN0b3IgQ29tcG9uZW50XG4gKi9cbmV4cG9ydCBjbGFzcyBUZXJtU2VsZWN0b3IgZXh0ZW5kcyBDb21wb25lbnQge1xuICAgIC8qKlxuICAgICAqIENvbnN0cnVjdG9yIGZvciBUZXJtU2VsZWN0b3IgQ29tcG9uZW50LlxuICAgICAqIFNldHMgdXAgc3RhdGUsIGFuZCBjcmVhdGVzIGJpbmRpbmdzIGZvciBmdW5jdGlvbnMuXG4gICAgICogQHBhcmFtIG9iamVjdCBwcm9wcyAtIGN1cnJlbnQgY29tcG9uZW50IHByb3BlcnRpZXMuXG4gICAgICovXG4gICAgY29uc3RydWN0b3IocHJvcHMpIHtcbiAgICAgICAgc3VwZXIoLi4uYXJndW1lbnRzKTtcbiAgICAgICAgdGhpcy5wcm9wcyA9IHByb3BzO1xuXG4gICAgICAgIHRoaXMuc3RhdGUgPSB7XG4gICAgICAgICAgICB0ZXJtczogW10sXG4gICAgICAgICAgICBsb2FkaW5nOiBmYWxzZSxcbiAgICAgICAgICAgIHR5cGU6IHByb3BzLnBvc3RUeXBlIHx8ICdwb3N0JyxcbiAgICAgICAgICAgIHRheG9ub215OiBwcm9wcy50YXhvbm9teSB8fCAnY2F0ZWdvcnknLFxuICAgICAgICAgICAgdGF4b25vbWllczogW10sXG4gICAgICAgICAgICBmaWx0ZXI6ICcnLFxuICAgICAgICAgICAgZmlsdGVyTG9hZGluZzogZmFsc2UsXG4gICAgICAgICAgICBmaWx0ZXJUZXJtczogW10sXG4gICAgICAgICAgICBpbml0aWFsTG9hZGluZzogZmFsc2UsXG4gICAgICAgIH07XG5cbiAgICAgICAgdGhpcy5hZGRUZXJtID0gdGhpcy5hZGRUZXJtLmJpbmQodGhpcyk7XG4gICAgICAgIHRoaXMucmVtb3ZlVGVybSA9IHRoaXMucmVtb3ZlVGVybS5iaW5kKHRoaXMpO1xuICAgICAgICB0aGlzLmhhbmRsZUlucHV0RmlsdGVyQ2hhbmdlID0gdGhpcy5oYW5kbGVJbnB1dEZpbHRlckNoYW5nZS5iaW5kKHRoaXMpO1xuICAgICAgICB0aGlzLmRvVGVybUZpbHRlciA9IGRlYm91bmNlKHRoaXMuZG9UZXJtRmlsdGVyLmJpbmQodGhpcyksIDMwMCk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogV2hlbiB0aGUgY29tcG9uZW50IG1vdW50cyBpdCBjYWxscyB0aGlzIGZ1bmN0aW9uLlxuICAgICAqIEZldGNoZXMgdGVybXMgdGF4b25vbWllcywgc2VsZWN0ZWQgdGVybXMgdGhlbiBtYWtlcyBmaXJzdCBjYWxsIGZvciB0ZXJtc1xuICAgICAqL1xuICAgIGNvbXBvbmVudERpZE1vdW50KCkge1xuICAgICAgICB0aGlzLnNldFN0YXRlKHtcbiAgICAgICAgICAgIGluaXRpYWxMb2FkaW5nOiB0cnVlLFxuICAgICAgICB9KTtcblxuICAgICAgICBhcGkuZ2V0VGF4b25vbWllcyggeyB0eXBlOiB0aGlzLnN0YXRlLnR5cGUgfSApXG4gICAgICAgICAgICAudGhlbigoIHJlc3BvbnNlICkgPT4ge1xuICAgICAgICAgICAgICAgIHRoaXMuc2V0U3RhdGUoe1xuICAgICAgICAgICAgICAgICAgICB0YXhvbm9taWVzOiByZXNwb25zZVxuICAgICAgICAgICAgICAgIH0sICgpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5yZXRyaWV2ZVNlbGVjdGVkVGVybXMoKVxuICAgICAgICAgICAgICAgICAgICAgICAgLnRoZW4oKCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMuc2V0U3RhdGUoe1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBpbml0aWFsTG9hZGluZzogZmFsc2UsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9KVxuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogR2V0VGVybXMgd3JhcHBlciwgYnVpbGRzIHRoZSByZXF1ZXN0IGFyZ3VtZW50IGJhc2VkIHN0YXRlIGFuZCBwYXJhbWV0ZXJzIHBhc3NlZC9cbiAgICAgKiBAcGFyYW0ge29iamVjdH0gYXJncyAtIGRlc2lyZWQgYXJndW1lbnRzIChjYW4gYmUgZW1wdHkpLlxuICAgICAqIEByZXR1cm5zIHtQcm9taXNlPFQ+fVxuICAgICAqL1xuICAgIGdldFRlcm1zKGFyZ3MgPSB7fSkge1xuICAgICAgICBjb25zdCB7IHNlbGVjdGVkVGVybUlkcyB9ID0gdGhpcy5wcm9wcztcblxuICAgICAgICBjb25zdCBkZWZhdWx0QXJncyA9IHtcbiAgICAgICAgICAgIHBlcl9wYWdlOiAxMCxcbiAgICAgICAgICAgIHR5cGU6IHRoaXMuc3RhdGUudHlwZSxcbiAgICAgICAgICAgIHRheG9ub215OiB0aGlzLnN0YXRlLnRheG9ub215LFxuICAgICAgICAgICAgc2VhcmNoOiB0aGlzLnN0YXRlLmZpbHRlcixcbiAgICAgICAgfTtcblxuICAgICAgICBjb25zdCByZXF1ZXN0QXJndW1lbnRzID0ge1xuICAgICAgICAgICAgLi4uZGVmYXVsdEFyZ3MsXG4gICAgICAgICAgICAuLi5hcmdzXG4gICAgICAgIH07XG5cbiAgICAgICAgcmVxdWVzdEFyZ3VtZW50cy5yZXN0QmFzZSA9IHRoaXMuc3RhdGUudGF4b25vbWllc1t0aGlzLnN0YXRlLnRheG9ub215XS5yZXN0X2Jhc2U7XG5cbiAgICAgICAgcmV0dXJuIGFwaS5nZXRUZXJtcyhyZXF1ZXN0QXJndW1lbnRzKVxuICAgICAgICAgICAgLnRoZW4ocmVzcG9uc2UgPT4ge1xuICAgICAgICAgICAgICAgIGlmIChyZXF1ZXN0QXJndW1lbnRzLnNlYXJjaCkge1xuICAgICAgICAgICAgICAgICAgICB0aGlzLnNldFN0YXRlKHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGZpbHRlclRlcm1zOiByZXNwb25zZS5maWx0ZXIoKHsgaWQgfSkgPT4gc2VsZWN0ZWRUZXJtSWRzLmluZGV4T2YoaWQpID09PSAtMSksXG4gICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiByZXNwb25zZTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICB0aGlzLnNldFN0YXRlKHtcbiAgICAgICAgICAgICAgICAgICAgdGVybXM6IHVuaXF1ZUJ5SWQoWy4uLnRoaXMuc3RhdGUudGVybXMsIC4uLnJlc3BvbnNlXSksXG4gICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICAvLyByZXR1cm4gcmVzcG9uc2UgdG8gY29udGludWUgdGhlIGNoYWluXG4gICAgICAgICAgICAgICAgcmV0dXJuIHJlc3BvbnNlO1xuICAgICAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogR2V0cyB0aGUgc2VsZWN0ZWQgdGVybXMgYnkgaWQgZnJvbSB0aGUgYHRlcm1zYCBzdGF0ZSBvYmplY3QgYW5kIHNvcnRzIHRoZW0gYnkgdGhlaXIgcG9zaXRpb24gaW4gdGhlIHNlbGVjdGVkIGFycmF5LlxuICAgICAqIEByZXR1cm5zIEFycmF5IG9mIG9iamVjdHMuXG4gICAgICovXG4gICAgZ2V0U2VsZWN0ZWRUZXJtcygpIHtcbiAgICAgICAgY29uc3QgeyBzZWxlY3RlZFRlcm1JZHMgfSA9IHRoaXMucHJvcHM7XG4gICAgICAgIHJldHVybiB0aGlzLnN0YXRlLnRlcm1zXG4gICAgICAgICAgICAuZmlsdGVyKCh7IGlkIH0pID0+IHNlbGVjdGVkVGVybUlkcy5pbmRleE9mKGlkKSAhPT0gLTEpXG4gICAgICAgICAgICAuc29ydCgoYSwgYikgPT4ge1xuICAgICAgICAgICAgICAgIGNvbnN0IGFJbmRleCA9IHRoaXMucHJvcHMuc2VsZWN0ZWRUZXJtSWRzLmluZGV4T2YoYS5pZCk7XG4gICAgICAgICAgICAgICAgY29uc3QgYkluZGV4ID0gdGhpcy5wcm9wcy5zZWxlY3RlZFRlcm1JZHMuaW5kZXhPZihiLmlkKTtcblxuICAgICAgICAgICAgICAgIGlmIChhSW5kZXggPiBiSW5kZXgpIHtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIDE7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgaWYgKGFJbmRleCA8IGJJbmRleCkge1xuICAgICAgICAgICAgICAgICAgICByZXR1cm4gLTE7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgcmV0dXJuIDA7XG4gICAgICAgICAgICB9KTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBNYWtlcyB0aGUgbmVjZXNzYXJ5IGFwaSBjYWxscyB0byBmZXRjaCB0aGUgc2VsZWN0ZWQgdGVybXMgYW5kIHJldHVybnMgYSBwcm9taXNlLlxuICAgICAqIEByZXR1cm5zIHsqfVxuICAgICAqL1xuICAgIHJldHJpZXZlU2VsZWN0ZWRUZXJtcygpIHtcbiAgICAgICAgY29uc3QgeyB0ZXJtVHlwZSwgc2VsZWN0ZWRUZXJtSWRzIH0gPSB0aGlzLnByb3BzO1xuICAgICAgICBjb25zdCB7IHRheG9ub21pZXMgfSA9IHRoaXMuc3RhdGU7XG5cbiAgICAgICAgaWYgKCBzZWxlY3RlZFRlcm1JZHMgJiYgIXNlbGVjdGVkVGVybUlkcy5sZW5ndGggPiAwICkge1xuICAgICAgICAgICAgLy8gcmV0dXJuIGEgZmFrZSBwcm9taXNlIHRoYXQgYXV0byByZXNvbHZlcy5cbiAgICAgICAgICAgIHJldHVybiBuZXcgUHJvbWlzZSgocmVzb2x2ZSkgPT4gcmVzb2x2ZSgpKTtcbiAgICAgICAgfVxuXG4gICAgICAgIHJldHVybiB0aGlzLmdldFRlcm1zKHtcbiAgICAgICAgICAgIGluY2x1ZGU6IHRoaXMucHJvcHMuc2VsZWN0ZWRUZXJtSWRzLmpvaW4oJywnKSxcbiAgICAgICAgICAgIHBlcl9wYWdlOiAxMDAsXG4gICAgICAgICAgICB0ZXJtVHlwZVxuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBBZGRzIGRlc2lyZWQgdGVybSBpZCB0byB0aGUgc2VsZWN0ZWRUZXJtSWRzIExpc3RcbiAgICAgKiBAcGFyYW0ge0ludGVnZXJ9IHRlcm1faWRcbiAgICAgKi9cbiAgICBhZGRUZXJtKHRlcm1faWQpIHtcbiAgICAgICAgaWYgKHRoaXMuc3RhdGUuZmlsdGVyKSB7XG4gICAgICAgICAgICBjb25zdCB0ZXJtID0gdGhpcy5zdGF0ZS5maWx0ZXJUZXJtcy5maWx0ZXIocCA9PiBwLmlkID09PSB0ZXJtX2lkKTtcbiAgICAgICAgICAgIGNvbnN0IHRlcm1zID0gdW5pcXVlQnlJZChbXG4gICAgICAgICAgICAgICAgLi4udGhpcy5zdGF0ZS50ZXJtcyxcbiAgICAgICAgICAgICAgICAuLi50ZXJtXG4gICAgICAgICAgICBdKTtcblxuICAgICAgICAgICAgdGhpcy5zZXRTdGF0ZSh7XG4gICAgICAgICAgICAgICAgdGVybXNcbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9XG5cbiAgICAgICAgdGhpcy5wcm9wcy51cGRhdGVTZWxlY3RlZFRlcm1JZHMoW1xuICAgICAgICAgICAgLi4udGhpcy5wcm9wcy5zZWxlY3RlZFRlcm1JZHMsXG4gICAgICAgICAgICB0ZXJtX2lkXG4gICAgICAgIF0pO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFJlbW92ZXMgZGVzaXJlZCB0ZXJtIGlkIHRvIHRoZSBzZWxlY3RlZFRlcm1JZHMgTGlzdFxuICAgICAqIEBwYXJhbSB7SW50ZWdlcn0gdGVybV9pZFxuICAgICAqL1xuICAgIHJlbW92ZVRlcm0odGVybV9pZCkge1xuICAgICAgICB0aGlzLnByb3BzLnVwZGF0ZVNlbGVjdGVkVGVybUlkcyhbXG4gICAgICAgICAgICAuLi50aGlzLnByb3BzLnNlbGVjdGVkVGVybUlkc1xuICAgICAgICBdLmZpbHRlcihpZCA9PiBpZCAhPT0gdGVybV9pZCkpO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEhhbmRsZXMgdGhlIHNlYXJjaCBib3ggaW5wdXQgdmFsdWVcbiAgICAgKiBAcGFyYW0gc3RyaW5nIHR5cGUgLSBjb21lcyBmcm9tIHRoZSBldmVudCBvYmplY3QgdGFyZ2V0LlxuICAgICAqL1xuICAgIGhhbmRsZUlucHV0RmlsdGVyQ2hhbmdlKHsgdGFyZ2V0OiB7IHZhbHVlOmZpbHRlciA9ICcnIH0gPSB7fSB9ID0ge30pIHtcbiAgICAgICAgdGhpcy5zZXRTdGF0ZSh7XG4gICAgICAgICAgICBmaWx0ZXJcbiAgICAgICAgfSwgKCkgPT4ge1xuICAgICAgICAgICAgaWYgKCFmaWx0ZXIpIHtcbiAgICAgICAgICAgICAgICAvLyByZW1vdmUgZmlsdGVyZWQgdGVybXNcbiAgICAgICAgICAgICAgICByZXR1cm4gdGhpcy5zZXRTdGF0ZSh7IGZpbHRlcmVkVGVybXM6IFtdLCBmaWx0ZXJpbmc6IGZhbHNlIH0pO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB0aGlzLmRvVGVybUZpbHRlcigpO1xuICAgICAgICB9KVxuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEFjdHVhbCBhcGkgY2FsbCBmb3Igc2VhcmNoaW5nIGZvciBxdWVyeSwgdGhpcyBmdW5jdGlvbiBpcyBkZWJvdW5jZWQgaW4gY29uc3RydWN0b3IuXG4gICAgICovXG4gICAgZG9UZXJtRmlsdGVyKCkge1xuICAgICAgICBjb25zdCB7IGZpbHRlciA9ICcnIH0gPSB0aGlzLnN0YXRlO1xuXG4gICAgICAgIGlmICghZmlsdGVyKSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLnNldFN0YXRlKHtcbiAgICAgICAgICAgIGZpbHRlcmluZzogdHJ1ZSxcbiAgICAgICAgICAgIGZpbHRlckxvYWRpbmc6IHRydWVcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy5nZXRUZXJtcygpXG4gICAgICAgICAgICAudGhlbigoKSA9PiB7XG4gICAgICAgICAgICAgICAgdGhpcy5zZXRTdGF0ZSh7XG4gICAgICAgICAgICAgICAgICAgIGZpbHRlckxvYWRpbmc6IGZhbHNlXG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9KTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBSZW5kZXJzIHRoZSBUZXJtU2VsZWN0b3IgY29tcG9uZW50LlxuICAgICAqL1xuICAgIHJlbmRlcigpIHtcbiAgICAgICAgY29uc3QgaXNGaWx0ZXJlZCA9IHRoaXMuc3RhdGUuZmlsdGVyaW5nO1xuICAgICAgICBjb25zdCB0ZXJtTGlzdCA9IGlzRmlsdGVyZWQgJiYgIXRoaXMuc3RhdGUuZmlsdGVyTG9hZGluZyA/IHRoaXMuc3RhdGUuZmlsdGVyVGVybXMgOiBbXTtcbiAgICAgICAgY29uc3QgU2VsZWN0ZWRUZXJtTGlzdCAgPSB0aGlzLmdldFNlbGVjdGVkVGVybXMoKTtcblxuICAgICAgICBjb25zdCBhZGRJY29uID0gPEljb24gaWNvbj1cInBsdXNcIiAvPjtcbiAgICAgICAgY29uc3QgcmVtb3ZlSWNvbiA9IDxJY29uIGljb249XCJtaW51c1wiIC8+O1xuXG4gICAgICAgIHJldHVybiAoXG4gICAgICAgICAgICA8ZGl2IGNsYXNzTmFtZT1cImNvbXBvbmVudHMtYmFzZS1jb250cm9sIGNvbXBvbmVudHMtdGVybS1zZWxlY3RvclwiPlxuICAgICAgICAgICAgICAgIDxkaXYgY2xhc3NOYW1lPVwiY29tcG9uZW50cy1iYXNlLWNvbnRyb2xfX2ZpZWxkXCI+XG4gICAgICAgICAgICAgICAgICAgIDxsYWJlbCBodG1sRm9yPVwic2VhcmNoaW5wdXRcIiBjbGFzc05hbWU9XCJjb21wb25lbnRzLWJhc2UtY29udHJvbF9fbGFiZWxcIj5cbiAgICAgICAgICAgICAgICAgICAgICAgIDxJY29uIGljb249XCJzZWFyY2hcIiAvPlxuICAgICAgICAgICAgICAgICAgICA8L2xhYmVsPlxuICAgICAgICAgICAgICAgICAgICA8aW5wdXRcbiAgICAgICAgICAgICAgICAgICAgICAgIGNsYXNzTmFtZT1cImNvbXBvbmVudHMtdGV4dC1jb250cm9sX19pbnB1dFwiXG4gICAgICAgICAgICAgICAgICAgICAgICBpZD1cInNlYXJjaGlucHV0XCJcbiAgICAgICAgICAgICAgICAgICAgICAgIHR5cGU9XCJzZWFyY2hcIlxuICAgICAgICAgICAgICAgICAgICAgICAgcGxhY2Vob2xkZXI9e1wiUGxlYXNlIGVudGVyIHlvdXIgc2VhcmNoIHF1ZXJ5Li4uXCJ9XG4gICAgICAgICAgICAgICAgICAgICAgICB2YWx1ZT17dGhpcy5zdGF0ZS5maWx0ZXJ9XG4gICAgICAgICAgICAgICAgICAgICAgICBvbkNoYW5nZT17dGhpcy5oYW5kbGVJbnB1dEZpbHRlckNoYW5nZX1cbiAgICAgICAgICAgICAgICAgICAgLz5cbiAgICAgICAgICAgICAgICAgICAgPEl0ZW1MaXN0XG4gICAgICAgICAgICAgICAgICAgICAgICBpdGVtcz17dGVybUxpc3R9XG4gICAgICAgICAgICAgICAgICAgICAgICBsb2FkaW5nPXt0aGlzLnN0YXRlLmluaXRpYWxMb2FkaW5nfHx0aGlzLnN0YXRlLmxvYWRpbmd8fHRoaXMuc3RhdGUuZmlsdGVyTG9hZGluZ31cbiAgICAgICAgICAgICAgICAgICAgICAgIGZpbHRlcmVkPXtpc0ZpbHRlcmVkfVxuICAgICAgICAgICAgICAgICAgICAgICAgYWN0aW9uPXt0aGlzLmFkZFRlcm19XG4gICAgICAgICAgICAgICAgICAgICAgICBpY29uPXthZGRJY29ufVxuICAgICAgICAgICAgICAgICAgICAvPlxuICAgICAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICAgICAgICAgIDxkaXYgY2xhc3NOYW1lPVwiY29tcG9uZW50cy1iYXNlLWNvbnRyb2xfX2ZpZWxkLS1zZWxlY3RlZFwiPlxuICAgICAgICAgICAgICAgICAgICA8aDI+U2VsZWN0ZWQ8L2gyPlxuICAgICAgICAgICAgICAgICAgICA8SXRlbUxpc3RcbiAgICAgICAgICAgICAgICAgICAgICAgIGl0ZW1zPXtTZWxlY3RlZFRlcm1MaXN0fVxuICAgICAgICAgICAgICAgICAgICAgICAgbG9hZGluZz17dGhpcy5zdGF0ZS5pbml0aWFsTG9hZGluZ31cbiAgICAgICAgICAgICAgICAgICAgICAgIGFjdGlvbj17dGhpcy5yZW1vdmVUZXJtfVxuICAgICAgICAgICAgICAgICAgICAgICAgaWNvbj17cmVtb3ZlSWNvbn1cbiAgICAgICAgICAgICAgICAgICAgLz5cbiAgICAgICAgICAgICAgICA8L2Rpdj5cbiAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICApO1xuICAgIH1cbn0iLCJjb25zdCB7IGFwaUZldGNoIH0gPSB3cDtcblxuLyoqXG4gKiBNYWtlcyBhIGdldCByZXF1ZXN0IHRvIHRoZSBQb3N0VHlwZXMgZW5kcG9pbnQuXG4gKlxuICogQHJldHVybnMge1Byb21pc2U8YW55Pn1cbiAqL1xuZXhwb3J0IGNvbnN0IGdldFBvc3RUeXBlcyA9ICgpID0+IHtcbiAgICByZXR1cm4gYXBpRmV0Y2goIHsgcGF0aDogJy93cC92Mi90eXBlcycgfSApO1xufTtcblxuLyoqXG4gKiBNYWtlcyBhIGdldCByZXF1ZXN0IHRvIHRoZSBkZXNpcmVkIHBvc3QgdHlwZSBhbmQgYnVpbGRzIHRoZSBxdWVyeSBzdHJpbmcgYmFzZWQgb24gYW4gb2JqZWN0LlxuICpcbiAqIEBwYXJhbSB7c3RyaW5nfGJvb2xlYW59IHJlc3RCYXNlIC0gcmVzdCBiYXNlIGZvciB0aGUgcXVlcnkuXG4gKiBAcGFyYW0ge29iamVjdH0gYXJnc1xuICogQHJldHVybnMge1Byb21pc2U8YW55Pn1cbiAqL1xuZXhwb3J0IGNvbnN0IGdldFBvc3RzID0gKHsgcmVzdEJhc2UgPSBmYWxzZSwgLi4uYXJncyB9KSA9PiB7XG4gICAgY29uc3QgcXVlcnlTdHJpbmcgPSBPYmplY3Qua2V5cyhhcmdzKS5tYXAoYXJnID0+IGAke2FyZ309JHthcmdzW2FyZ119YCkuam9pbignJicpO1xuXG4gICAgbGV0IHBhdGggPSBgL3dwL3YyLyR7cmVzdEJhc2V9PyR7cXVlcnlTdHJpbmd9Jl9lbWJlZGA7XG4gICAgcmV0dXJuIGFwaUZldGNoKCB7IHBhdGg6IHBhdGggfSApO1xufTtcblxuLyoqXG4gKiBNYWtlcyBhIGdldCByZXF1ZXN0IHRvIHRoZSBQb3N0VHlwZSBUYXhvbm9taWVzIGVuZHBvaW50LlxuICpcbiAqIEByZXR1cm5zIHtQcm9taXNlPGFueT59XG4gKi9cbmV4cG9ydCBjb25zdCBnZXRUYXhvbm9taWVzID0gKHsgLi4uYXJncyB9KSA9PiB7XG4gICAgY29uc3QgcXVlcnlTdHJpbmcgPSBPYmplY3Qua2V5cyhhcmdzKS5tYXAoYXJnID0+IGAke2FyZ309JHthcmdzW2FyZ119YCkuam9pbignJicpO1xuXG4gICAgbGV0IHBhdGggPSBgL3dwL3YyL3RheG9ub21pZXM/JHtxdWVyeVN0cmluZ30mX2VtYmVkYDtcbiAgICByZXR1cm4gYXBpRmV0Y2goIHsgcGF0aDogcGF0aCB9ICk7XG59O1xuXG4vKipcbiAqIE1ha2VzIGEgZ2V0IHJlcXVlc3QgdG8gdGhlIGRlc2lyZWQgcG9zdCB0eXBlIGFuZCBidWlsZHMgdGhlIHF1ZXJ5IHN0cmluZyBiYXNlZCBvbiBhbiBvYmplY3QuXG4gKlxuICogQHBhcmFtIHtzdHJpbmd8Ym9vbGVhbn0gcmVzdEJhc2UgLSByZXN0IGJhc2UgZm9yIHRoZSBxdWVyeS5cbiAqIEBwYXJhbSB7b2JqZWN0fSBhcmdzXG4gKiBAcmV0dXJucyB7UHJvbWlzZTxhbnk+fVxuICovXG5leHBvcnQgY29uc3QgZ2V0VGVybXMgPSAoeyByZXN0QmFzZSA9IGZhbHNlLCAuLi5hcmdzIH0pID0+IHtcbiAgICBjb25zdCBxdWVyeVN0cmluZyA9IE9iamVjdC5rZXlzKGFyZ3MpLm1hcChhcmcgPT4gYCR7YXJnfT0ke2FyZ3NbYXJnXX1gKS5qb2luKCcmJyk7XG5cbiAgICBsZXQgcGF0aCA9IGAvd3AvdjIvJHtyZXN0QmFzZX0/JHtxdWVyeVN0cmluZ30mX2VtYmVkYDtcbiAgICByZXR1cm4gYXBpRmV0Y2goIHsgcGF0aDogcGF0aCB9ICk7XG59OyIsIi8qKlxuICogUmV0dXJucyBhIHVuaXF1ZSBhcnJheSBvZiBvYmplY3RzIGJhc2VkIG9uIGEgZGVzaXJlZCBrZXkuXG4gKiBAcGFyYW0ge2FycmF5fSBhcnIgLSBhcnJheSBvZiBvYmplY3RzLlxuICogQHBhcmFtIHtzdHJpbmd8aW50fSBrZXkgLSBrZXkgdG8gZmlsdGVyIG9iamVjdHMgYnlcbiAqL1xuZXhwb3J0IGNvbnN0IHVuaXF1ZUJ5ID0gKGFyciwga2V5KSA9PiB7XG4gICAgbGV0IGtleXMgPSBbXTtcbiAgICByZXR1cm4gYXJyLmZpbHRlcihpdGVtID0+IHtcbiAgICAgICAgaWYgKGtleXMuaW5kZXhPZihpdGVtW2tleV0pICE9PSAtMSkge1xuICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICB9XG5cbiAgICAgICAgcmV0dXJuIGtleXMucHVzaChpdGVtW2tleV0pO1xuICAgIH0pO1xufTtcblxuLyoqXG4gKiBSZXR1cm5zIGEgdW5pcXVlIGFycmF5IG9mIG9iamVjdHMgYmFzZWQgb24gdGhlIGlkIHByb3BlcnR5LlxuICogQHBhcmFtIHthcnJheX0gYXJyIC0gYXJyYXkgb2Ygb2JqZWN0cyB0byBmaWx0ZXIuXG4gKiBAcmV0dXJucyB7Kn1cbiAqL1xuZXhwb3J0IGNvbnN0IHVuaXF1ZUJ5SWQgPSBhcnIgPT4gdW5pcXVlQnkoYXJyLCAnaWQnKTtcblxuLyoqXG4gKiBEZWJvdW5jZSBhIGZ1bmN0aW9uIGJ5IGxpbWl0aW5nIGhvdyBvZnRlbiBpdCBjYW4gcnVuLlxuICogQHBhcmFtIHtmdW5jdGlvbn0gZnVuYyAtIGNhbGxiYWNrIGZ1bmN0aW9uXG4gKiBAcGFyYW0ge0ludGVnZXJ9IHdhaXQgLSBUaW1lIGluIG1pbGxpc2Vjb25kcyBob3cgbG9uZyBpdCBzaG91bGQgd2FpdC5cbiAqIEByZXR1cm5zIHtGdW5jdGlvbn1cbiAqL1xuZXhwb3J0IGNvbnN0IGRlYm91bmNlID0gKGZ1bmMsIHdhaXQpID0+IHtcbiAgICBsZXQgdGltZW91dCA9IG51bGw7XG5cbiAgICByZXR1cm4gZnVuY3Rpb24gKCkge1xuICAgICAgICBjb25zdCBjb250ZXh0ID0gdGhpcztcbiAgICAgICAgY29uc3QgYXJncyA9IGFyZ3VtZW50cztcblxuICAgICAgICBjb25zdCBsYXRlciA9ICgpID0+IHtcbiAgICAgICAgICAgIGZ1bmMuYXBwbHkoY29udGV4dCwgYXJncyk7XG4gICAgICAgIH07XG5cbiAgICAgICAgY2xlYXJUaW1lb3V0KHRpbWVvdXQpO1xuICAgICAgICB0aW1lb3V0ID0gc2V0VGltZW91dChsYXRlciwgd2FpdCk7XG4gICAgfVxufTsiXX0=
