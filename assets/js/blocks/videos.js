(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
"use strict";

var _ShortcodeAtts = require("../components/ShortcodeAtts");

function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { keys.push.apply(keys, Object.getOwnPropertySymbols(object)); } if (enumerableOnly) keys = keys.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(source, true).forEach(function (key) { _defineProperty(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(source).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

var __ = wp.i18n.__;
var registerBlockType = wp.blocks.registerBlockType;
var InspectorControls = wp.editor.InspectorControls;
var Fragment = wp.element.Fragment;
var _wp$components = wp.components,
    ServerSideRender = _wp$components.ServerSideRender,
    Disabled = _wp$components.Disabled,
    PanelBody = _wp$components.PanelBody;
registerBlockType('masvideos/videos', {
  title: __('Videos Block', 'masvideos'),
  icon: 'video-alt2',
  category: 'masvideos-blocks',
  edit: function edit(props) {
    var attributes = props.attributes,
        className = props.className,
        setAttributes = props.setAttributes;

    var onChangeShortcodeAtts = function onChangeShortcodeAtts(newShortcodeAtts) {
      setAttributes(_objectSpread({}, newShortcodeAtts));
    };

    return wp.element.createElement(Fragment, null, wp.element.createElement(InspectorControls, null, wp.element.createElement(PanelBody, {
      title: __('Videos Attributes', 'masvideos'),
      initialOpen: true
    }, wp.element.createElement(_ShortcodeAtts.ShortcodeAtts, {
      postType: "video",
      catTaxonomy: "video_cat",
      hideFields: ['top_rated'],
      attributes: _objectSpread({}, attributes),
      updateShortcodeAtts: onChangeShortcodeAtts
    }))), wp.element.createElement(Disabled, null, wp.element.createElement(ServerSideRender, {
      block: "masvideos/videos",
      attributes: attributes
    })));
  },
  save: function save() {
    // Rendering in PHP
    return null;
  }
});

},{"../components/ShortcodeAtts":5}],2:[function(require,module,exports){
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

var __ = wp.i18n.__;
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
    }, __('Loading ...', 'vodi'));
  }

  if (filtered && items.length < 1) {
    return wp.element.createElement("div", {
      className: "item-list"
    }, wp.element.createElement("p", null, __('Your query yielded no results, please try again.', 'vodi')));
  }

  if (!items || items.length < 1) {
    return wp.element.createElement("p", {
      className: "no-items"
    }, __('Not found.', 'vodi'));
  }

  return wp.element.createElement("div", {
    className: "item-list"
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

function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { keys.push.apply(keys, Object.getOwnPropertySymbols(object)); } if (enumerableOnly) keys = keys.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(source, true).forEach(function (key) { _defineProperty(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(source).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

var __ = wp.i18n.__;
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
      initialLoading: false,
      selectedPosts: []
    };
    _this.addPost = _this.addPost.bind(_assertThisInitialized(_this));
    _this.removePost = _this.removePost.bind(_assertThisInitialized(_this));
    _this.handleInputFilterChange = _this.handleInputFilterChange.bind(_assertThisInitialized(_this));
    _this.doPostFilter = (0, _usefulFuncs.debounce)(_this.doPostFilter.bind(_assertThisInitialized(_this)), 300);
    _this.getSelectedPostIds = _this.getSelectedPostIds.bind(_assertThisInitialized(_this));
    _this.getSelectedPosts = _this.getSelectedPosts.bind(_assertThisInitialized(_this));
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
          _this2.retrieveSelectedPosts().then(function (selectedPosts) {
            if (selectedPosts) {
              _this2.setState({
                initialLoading: false,
                selectedPosts: selectedPosts
              });
            } else {
              _this2.setState({
                initialLoading: false
              });
            }
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
      var postIds = this.getSelectedPostIds();
      var defaultArgs = {
        per_page: 10,
        type: this.state.type,
        search: this.state.filter
      };

      var requestArguments = _objectSpread({}, defaultArgs, {}, args);

      requestArguments.restBase = this.state.types[this.state.type].rest_base;
      return api.getPosts(requestArguments).then(function (response) {
        if (requestArguments.search) {
          _this3.setState({
            filterPosts: response.filter(function (_ref) {
              var id = _ref.id;
              return postIds.indexOf(id) === -1;
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
    key: "getSelectedPostIds",
    value: function getSelectedPostIds() {
      var selectedPostIds = this.props.selectedPostIds;

      if (selectedPostIds) {
        var postIds = Array.isArray(selectedPostIds) ? selectedPostIds : selectedPostIds.split(',');
        return postIds;
      }

      return [];
    }
    /**
     * Gets the selected posts by id from the `posts` state object and sorts them by their position in the selected array.
     * @returns Array of objects.
     */

  }, {
    key: "getSelectedPosts",
    value: function getSelectedPosts(postIds) {
      var postList = (0, _usefulFuncs.uniqueById)([].concat(_toConsumableArray(this.state.filterPosts), _toConsumableArray(this.state.posts)));
      var selectedPosts = postList.filter(function (_ref2) {
        var id = _ref2.id;
        return postIds.indexOf(id) !== -1;
      }).sort(function (a, b) {
        var aIndex = postIds.indexOf(a.id);
        var bIndex = postIds.indexOf(b.id);

        if (aIndex > bIndex) {
          return 1;
        }

        if (aIndex < bIndex) {
          return -1;
        }

        return 0;
      });
      this.setState({
        selectedPosts: selectedPosts
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
      var postIds = this.getSelectedPostIds().join(',');

      if (!postIds) {
        // return a fake promise that auto resolves.
        return new Promise(function (resolve) {
          return resolve();
        });
      }

      var post_args = {
        include: postIds,
        per_page: 100,
        postType: postType
      };

      if (this.props.postStatus) {
        post_args.status = this.props.postStatus;
      }

      return this.getPosts(_objectSpread({}, post_args));
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

      if (this.props.selectSingle) {
        var selectedPostIds = [post_id];
        this.props.updateSelectedPostIds(selectedPostIds);
        this.getSelectedPosts(selectedPostIds);
      } else {
        var postIds = this.getSelectedPostIds();

        var _selectedPostIds = [].concat(_toConsumableArray(postIds), [post_id]);

        this.props.updateSelectedPostIds(_selectedPostIds);
        this.getSelectedPosts(_selectedPostIds);
      }
    }
    /**
     * Removes desired post id to the selectedPostIds List
     * @param {Integer} post_id
     */

  }, {
    key: "removePost",
    value: function removePost(post_id) {
      var postIds = this.getSelectedPostIds();

      var selectedPostIds = _toConsumableArray(postIds).filter(function (id) {
        return id !== post_id;
      });

      this.props.updateSelectedPostIds(selectedPostIds);
      this.getSelectedPosts(selectedPostIds);
    }
    /**
     * Handles the search box input value
     * @param string type - comes from the event object target.
     */

  }, {
    key: "handleInputFilterChange",
    value: function handleInputFilterChange() {
      var _this4 = this;

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
          return _this4.setState({
            filteredPosts: [],
            filtering: false
          });
        }

        _this4.doPostFilter();
      });
    }
    /**
     * Actual api call for searching for query, this function is debounced in constructor.
     */

  }, {
    key: "doPostFilter",
    value: function doPostFilter() {
      var _this5 = this;

      var _this$state$filter = this.state.filter,
          filter = _this$state$filter === void 0 ? '' : _this$state$filter;

      if (!filter) {
        return;
      }

      this.setState({
        filtering: true,
        filterLoading: true
      });
      var post_args = {};

      if (this.props.postStatus) {
        post_args.status = this.props.postStatus;
      }

      this.getPosts(_objectSpread({}, post_args)).then(function () {
        _this5.setState({
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
      var postList = this.state.filtering && !this.state.filterLoading ? this.state.filterPosts : [];
      var addIcon = wp.element.createElement(Icon, {
        icon: "plus"
      });
      var removeIcon = wp.element.createElement(Icon, {
        icon: "minus"
      });
      var searchinputuniqueId = 'searchinput-' + Math.random().toString(36).substr(2, 16);
      return wp.element.createElement("div", {
        className: "components-base-control components-post-selector"
      }, wp.element.createElement("div", {
        className: "components-base-control__field--selected"
      }, wp.element.createElement("h2", null, __('Search Post', 'vodi')), wp.element.createElement(_ItemList.ItemList, {
        items: _toConsumableArray(this.state.selectedPosts),
        loading: this.state.initialLoading,
        action: this.removePost,
        icon: removeIcon
      })), wp.element.createElement("div", {
        className: "components-base-control__field"
      }, wp.element.createElement("label", {
        htmlFor: searchinputuniqueId,
        className: "components-base-control__label"
      }, wp.element.createElement(Icon, {
        icon: "search"
      })), wp.element.createElement("input", {
        className: "components-text-control__input",
        id: searchinputuniqueId,
        type: "search",
        placeholder: __('Please enter your search query...', 'vodi'),
        value: this.state.filter,
        onChange: this.handleInputFilterChange
      }), wp.element.createElement(_ItemList.ItemList, {
        items: postList,
        loading: this.state.initialLoading || this.state.loading || this.state.filterLoading,
        filtered: this.state.filtering,
        action: this.addPost,
        icon: addIcon
      })));
    }
  }]);

  return PostSelector;
}(Component);

exports.PostSelector = PostSelector;

},{"../utils/api":7,"../utils/useful-funcs":8,"./ItemList":3}],5:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.ShortcodeAtts = void 0;

var _PostSelector = require("./PostSelector");

var _TermSelector = require("./TermSelector");

function _typeof(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

var __ = wp.i18n.__;
var Component = wp.element.Component;
var _wp$components = wp.components,
    RangeControl = _wp$components.RangeControl,
    SelectControl = _wp$components.SelectControl,
    CheckboxControl = _wp$components.CheckboxControl;
/**
 * ShortcodeAtts Component
 */

var ShortcodeAtts =
/*#__PURE__*/
function (_Component) {
  _inherits(ShortcodeAtts, _Component);

  /**
   * Constructor for ShortcodeAtts Component.
   * Sets up state, and creates bindings for functions.
   * @param object props - current component properties.
   */
  function ShortcodeAtts(props) {
    var _this;

    _classCallCheck(this, ShortcodeAtts);

    _this = _possibleConstructorReturn(this, _getPrototypeOf(ShortcodeAtts).apply(this, arguments));
    _this.props = props;
    _this.onChangeLimit = _this.onChangeLimit.bind(_assertThisInitialized(_this));
    _this.onChangeColumns = _this.onChangeColumns.bind(_assertThisInitialized(_this));
    _this.onChangeOrderby = _this.onChangeOrderby.bind(_assertThisInitialized(_this));
    _this.onChangeOrder = _this.onChangeOrder.bind(_assertThisInitialized(_this));
    _this.onChangeIds = _this.onChangeIds.bind(_assertThisInitialized(_this));
    _this.onChangeCategory = _this.onChangeCategory.bind(_assertThisInitialized(_this));
    _this.onChangeGenre = _this.onChangeGenre.bind(_assertThisInitialized(_this));
    _this.onChangeFeatured = _this.onChangeFeatured.bind(_assertThisInitialized(_this));
    _this.onChangeTopRated = _this.onChangeTopRated.bind(_assertThisInitialized(_this));
    return _this;
  }

  _createClass(ShortcodeAtts, [{
    key: "onChangeLimit",
    value: function onChangeLimit(newLimit) {
      this.props.updateShortcodeAtts({
        limit: newLimit
      });
    }
  }, {
    key: "onChangeColumns",
    value: function onChangeColumns(newColumns) {
      this.props.updateShortcodeAtts({
        columns: newColumns
      });
    }
  }, {
    key: "onChangeOrderby",
    value: function onChangeOrderby(newOrderby) {
      this.props.updateShortcodeAtts({
        orderby: newOrderby
      });
    }
  }, {
    key: "onChangeOrder",
    value: function onChangeOrder(newOrder) {
      this.props.updateShortcodeAtts({
        order: newOrder
      });
    }
  }, {
    key: "onChangeIds",
    value: function onChangeIds(newIds) {
      this.props.updateShortcodeAtts({
        ids: newIds.join(',')
      });
    }
  }, {
    key: "onChangeCategory",
    value: function onChangeCategory(newCategory) {
      this.props.updateShortcodeAtts({
        category: newCategory.join(',')
      });
    }
  }, {
    key: "onChangeGenre",
    value: function onChangeGenre(newGenre) {
      this.props.updateShortcodeAtts({
        genre: newGenre.join(',')
      });
    }
  }, {
    key: "onChangeFeatured",
    value: function onChangeFeatured(newFeatured) {
      this.props.updateShortcodeAtts({
        featured: newFeatured
      });
    }
  }, {
    key: "onChangeTopRated",
    value: function onChangeTopRated(newTopRated) {
      this.props.updateShortcodeAtts({
        top_rated: newTopRated
      });
    }
    /**
     * Renders the ShortcodeAtts component.
     */

  }, {
    key: "render",
    value: function render() {
      var _this$props = this.props,
          attributes = _this$props.attributes,
          postType = _this$props.postType,
          catTaxonomy = _this$props.catTaxonomy,
          hideFields = _this$props.hideFields;
      var limit = attributes.limit,
          columns = attributes.columns,
          orderby = attributes.orderby,
          order = attributes.order,
          ids = attributes.ids,
          category = attributes.category,
          genre = attributes.genre,
          featured = attributes.featured,
          top_rated = attributes.top_rated;
      return wp.element.createElement("div", null, !(hideFields && hideFields.includes('limit')) ? wp.element.createElement(RangeControl, {
        label: __('Limit', 'vodi'),
        value: limit,
        onChange: this.onChangeLimit,
        min: 1,
        max: 50
      }) : '', !(hideFields && hideFields.includes('columns')) ? wp.element.createElement(RangeControl, {
        label: __('Columns', 'vodi'),
        value: columns,
        onChange: this.onChangeColumns,
        min: 1,
        max: 6
      }) : '', !(hideFields && hideFields.includes('orderby')) ? wp.element.createElement(SelectControl, {
        label: __('Orderby', 'vodi'),
        value: orderby,
        options: [{
          label: __('Title', 'vodi'),
          value: 'title'
        }, {
          label: __('Date', 'vodi'),
          value: postType === 'movie' ? 'release_date' : 'date'
        }, {
          label: __('ID', 'vodi'),
          value: 'id'
        }, {
          label: __('Random', 'vodi'),
          value: 'rand'
        }],
        onChange: this.onChangeOrderby
      }) : '', !(hideFields && hideFields.includes('order')) ? wp.element.createElement(SelectControl, {
        label: __('Order', 'vodi'),
        value: order,
        options: [{
          label: __('ASC', 'vodi'),
          value: 'ASC'
        }, {
          label: __('DESC', 'vodi'),
          value: 'DESC'
        }],
        onChange: this.onChangeOrder
      }) : '', !(hideFields && hideFields.includes('ids')) ? wp.element.createElement(_PostSelector.PostSelector, {
        postType: postType,
        selectedPostIds: ids ? ids.split(',').map(Number) : [],
        updateSelectedPostIds: this.onChangeIds
      }) : '', postType === 'video' && !(hideFields && hideFields.includes('category')) ? wp.element.createElement(_TermSelector.TermSelector, {
        postType: postType,
        taxonomy: catTaxonomy,
        selectedTermIds: category ? category.split(',').map(Number) : [],
        updateSelectedTermIds: this.onChangeCategory
      }) : !(hideFields && hideFields.includes('genre')) ? wp.element.createElement(_TermSelector.TermSelector, {
        postType: postType,
        taxonomy: catTaxonomy,
        selectedTermIds: genre ? genre.split(',').map(Number) : [],
        updateSelectedTermIds: this.onChangeGenre
      }) : '', !(hideFields && hideFields.includes('featured')) ? wp.element.createElement(CheckboxControl, {
        label: __('Featured', 'vodi'),
        help: __('Check to select featured posts.', 'vodi'),
        checked: featured,
        onChange: this.onChangeFeatured
      }) : '', !(hideFields && hideFields.includes('top_rated')) ? wp.element.createElement(CheckboxControl, {
        label: __('Top Rated', 'vodi'),
        help: __('Check to select top rated posts.', 'vodi'),
        checked: top_rated,
        onChange: this.onChangeTopRated
      }) : '');
    }
  }]);

  return ShortcodeAtts;
}(Component);

exports.ShortcodeAtts = ShortcodeAtts;

},{"./PostSelector":4,"./TermSelector":6}],6:[function(require,module,exports){
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

function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { keys.push.apply(keys, Object.getOwnPropertySymbols(object)); } if (enumerableOnly) keys = keys.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(source, true).forEach(function (key) { _defineProperty(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(source).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

var __ = wp.i18n.__;
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
    _this.addTerm = _this.addTerm.bind(_assertThisInitialized(_this));
    _this.removeTerm = _this.removeTerm.bind(_assertThisInitialized(_this));
    _this.handleInputFilterChange = _this.handleInputFilterChange.bind(_assertThisInitialized(_this));
    _this.doTermFilter = (0, _usefulFuncs.debounce)(_this.doTermFilter.bind(_assertThisInitialized(_this)), 300);
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

      var requestArguments = _objectSpread({}, defaultArgs, {}, args);

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
      var searchinputuniqueId = 'searchinput-' + Math.random().toString(36).substr(2, 16);
      return wp.element.createElement("div", {
        className: "components-base-control components-term-selector"
      }, wp.element.createElement("div", {
        className: "components-base-control__field--selected"
      }, wp.element.createElement("h2", null, __('Search Term', 'vodi')), wp.element.createElement(_ItemList.ItemList, {
        items: SelectedTermList,
        loading: this.state.initialLoading,
        action: this.removeTerm,
        icon: removeIcon
      })), wp.element.createElement("div", {
        className: "components-base-control__field"
      }, wp.element.createElement("label", {
        htmlFor: searchinputuniqueId,
        className: "components-base-control__label"
      }, wp.element.createElement(Icon, {
        icon: "search"
      })), wp.element.createElement("input", {
        className: "components-text-control__input",
        id: searchinputuniqueId,
        type: "search",
        placeholder: __('Please enter your search query...', 'vodi'),
        value: this.state.filter,
        onChange: this.handleInputFilterChange
      }), wp.element.createElement(_ItemList.ItemList, {
        items: termList,
        loading: this.state.initialLoading || this.state.loading || this.state.filterLoading,
        filtered: isFiltered,
        action: this.addTerm,
        icon: addIcon
      })));
    }
  }]);

  return TermSelector;
}(Component);

exports.TermSelector = TermSelector;

},{"../utils/api":7,"../utils/useful-funcs":8,"./ItemList":3}],7:[function(require,module,exports){
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

},{}],8:[function(require,module,exports){
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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJhc3NldHMvZXNuZXh0L2Jsb2Nrcy92aWRlb3MuanMiLCJhc3NldHMvZXNuZXh0L2NvbXBvbmVudHMvSXRlbS5qcyIsImFzc2V0cy9lc25leHQvY29tcG9uZW50cy9JdGVtTGlzdC5qcyIsImFzc2V0cy9lc25leHQvY29tcG9uZW50cy9Qb3N0U2VsZWN0b3IuanMiLCJhc3NldHMvZXNuZXh0L2NvbXBvbmVudHMvU2hvcnRjb2RlQXR0cy5qcyIsImFzc2V0cy9lc25leHQvY29tcG9uZW50cy9UZXJtU2VsZWN0b3IuanMiLCJhc3NldHMvZXNuZXh0L3V0aWxzL2FwaS5qcyIsImFzc2V0cy9lc25leHQvdXRpbHMvdXNlZnVsLWZ1bmNzLmpzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBOzs7QUNBQTs7Ozs7Ozs7SUFFUSxFLEdBQU8sRUFBRSxDQUFDLEksQ0FBVixFO0lBQ0EsaUIsR0FBc0IsRUFBRSxDQUFDLE0sQ0FBekIsaUI7SUFDQSxpQixHQUFzQixFQUFFLENBQUMsTSxDQUF6QixpQjtJQUNBLFEsR0FBYSxFQUFFLENBQUMsTyxDQUFoQixRO3FCQUMwQyxFQUFFLENBQUMsVTtJQUE3QyxnQixrQkFBQSxnQjtJQUFrQixRLGtCQUFBLFE7SUFBVSxTLGtCQUFBLFM7QUFFcEMsaUJBQWlCLENBQUUsa0JBQUYsRUFBc0I7QUFDbkMsRUFBQSxLQUFLLEVBQUUsRUFBRSxDQUFDLGNBQUQsRUFBaUIsV0FBakIsQ0FEMEI7QUFHbkMsRUFBQSxJQUFJLEVBQUUsWUFINkI7QUFLbkMsRUFBQSxRQUFRLEVBQUUsa0JBTHlCO0FBT25DLEVBQUEsSUFBSSxFQUFJLGNBQUUsS0FBRixFQUFhO0FBQUEsUUFDVCxVQURTLEdBQ2dDLEtBRGhDLENBQ1QsVUFEUztBQUFBLFFBQ0csU0FESCxHQUNnQyxLQURoQyxDQUNHLFNBREg7QUFBQSxRQUNjLGFBRGQsR0FDZ0MsS0FEaEMsQ0FDYyxhQURkOztB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TSxPQUFPLEdBQUcsSUFBaEI7QUFDQSxRQUFNLElBQUksR0FBRyxTQUFiOztBQUVBLFFBQU0sS0FBSyxHQUFHLFNBQVIsS0FBUSxHQUFNO0FBQ2hCLE1BQUEsSUFBSSxDQUFDLEtBQUwsQ0FBVyxPQUFYLEVBQW9CLElBQXBCO0FBQ0gsS0FGRDs7QUFJQSxJQUFBLFlBQVksQ0FBQyxPQUFELENBQVo7QUFDQSxJQUFBLE9BQU8sR0FBRyxVQUFVLENBQUMsS0FBRCxFQUFRLElBQVIsQ0FBcEI7QUFDSCxHQVZEO0FBV0gsQ0FkTSIsImZpbGUiOiJnZW5lcmF0ZWQuanMiLCJzb3VyY2VSb290IjoiIiwic291cmNlc0NvbnRlbnQiOlsiKGZ1bmN0aW9uKCl7ZnVuY3Rpb24gcihlLG4sdCl7ZnVuY3Rpb24gbyhpLGYpe2lmKCFuW2ldKXtpZighZVtpXSl7dmFyIGM9XCJmdW5jdGlvblwiPT10eXBlb2YgcmVxdWlyZSYmcmVxdWlyZTtpZighZiYmYylyZXR1cm4gYyhpLCEwKTtpZih1KXJldHVybiB1KGksITApO3ZhciBhPW5ldyBFcnJvcihcIkNhbm5vdCBmaW5kIG1vZHVsZSAnXCIraStcIidcIik7dGhyb3cgYS5jb2RlPVwiTU9EVUxFX05PVF9GT1VORFwiLGF9dmFyIHA9bltpXT17ZXhwb3J0czp7fX07ZVtpXVswXS5jYWxsKHAuZXhwb3J0cyxmdW5jdGlvbihyKXt2YXIgbj1lW2ldWzFdW3JdO3JldHVybiBvKG58fHIpfSxwLHAuZXhwb3J0cyxyLGUsbix0KX1yZXR1cm4gbltpXS5leHBvcnRzfWZvcih2YXIgdT1cImZ1bmN0aW9uXCI9PXR5cGVvZiByZXF1aXJlJiZyZXF1aXJlLGk9MDtpPHQubGVuZ3RoO2krKylvKHRbaV0pO3JldHVybiBvfXJldHVybiByfSkoKSIsImltcG9ydCB7IFNob3J0Y29kZUF0dHMgfSBmcm9tICcuLi9jb21wb25lbnRzL1Nob3J0Y29kZUF0dHMnO1xuXG5jb25zdCB7IF9fIH0gPSB3cC5pMThuO1xuY29uc3QgeyByZWdpc3RlckJsb2NrVHlwZSB9ID0gd3AuYmxvY2tzO1xuY29uc3QgeyBJbnNwZWN0b3JDb250cm9scyB9ID0gd3AuZWRpdG9yO1xuY29uc3QgeyBGcmFnbWVudCB9ID0gd3AuZWxlbWVudDtcbmNvbnN0IHsgU2VydmVyU2lkZVJlbmRlciwgRGlzYWJsZWQsIFBhbmVsQm9keSB9ID0gd3AuY29tcG9uZW50cztcblxucmVnaXN0ZXJCbG9ja1R5cGUoICdtYXN2aWRlb3MvdmlkZW9zJywge1xuICAgIHRpdGxlOiBfXygnVmlkZW9zIEJsb2NrJywgJ21hc3ZpZGVvcycpLFxuXG4gICAgaWNvbjogJ3ZpZGVvLWFsdDInLFxuXG4gICAgY2F0ZWdvcnk6ICdtYXN2aWRlb3MtYmxvY2tzJyxcblxuICAgIGVkaXQ6ICggKCBwcm9wcyApID0+IHtcbiAgICAgICAgY29uc3QgeyBhdHRyaWJ1dGVzLCBjbGFzc05hbWUsIHNldEF0dHJpYnV0ZXMgfSA9IHByb3BzO1xuXG4gICAgICAgIGNvbnN0IG9uQ2hhbmdlU2hvcnRjb2RlQXR0cyA9IG5ld1Nob3J0Y29kZUF0dHMgPT4ge1xuICAgICAgICAgICAgc2V0QXR0cmlidXRlcyggeyAuLi5uZXdTaG9ydGNvZGVBdHRzIH0gKTtcbiAgICAgICAgfTtcblxuICAgICAgICByZXR1cm4gKFxuICAgICAgICAgICAgPEZyYWdtZW50PlxuICAgICAgICAgICAgICAgIDxJbnNwZWN0b3JDb250cm9scz5cbiAgICAgICAgICAgICAgICAgICAgPFBhbmVsQm9keVxuICAgICAgICAgICAgICAgICAgICAgICAgdGl0bGU9e19fKCdWaWRlb3MgQXR0cmlidXRlcycsICdtYXN2aWRlb3MnKX1cbiAgICAgICAgICAgICAgICAgICAgICAgIGluaXRpYWxPcGVuPXsgdHJ1ZSB9XG4gICAgICAgICAgICAgICAgICAgID5cbiAgICAgICAgICAgICAgICAgICAgICAgIDxTaG9ydGNvZGVBdHRzXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgcG9zdFR5cGUgPSAndmlkZW8nXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgY2F0VGF4b25vbXkgPSAndmlkZW9fY2F0J1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGhpZGVGaWVsZHMgPSB7IFsndG9wX3JhdGVkJ10gfVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGF0dHJpYnV0ZXMgPSB7IHsgLi4uYXR0cmlidXRlcyB9IH1cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB1cGRhdGVTaG9ydGNvZGVBdHRzID0geyBvbkNoYW5nZVNob3J0Y29kZUF0dHMgfVxuICAgICAgICAgICAgICAgICAgICAgICAgLz5cbiAgICAgICAgICAgICAgICAgICAgPC9QYW5lbEJvZHk+XG4gICAgICAgICAgICAgICAgPC9JbnNwZWN0b3JDb250cm9scz5cbiAgICAgICAgICAgICAgICA8RGlzYWJsZWQ+XG4gICAgICAgICAgICAgICAgICAgIDxTZXJ2ZXJTaWRlUmVuZGVyXG4gICAgICAgICAgICAgICAgICAgICAgICBibG9jayA9IFwibWFzdmlkZW9zL3ZpZGVvc1wiXG4gICAgICAgICAgICAgICAgICAgICAgICBhdHRyaWJ1dGVzID0geyBhdHRyaWJ1dGVzIH1cbiAgICAgICAgICAgICAgICAgICAgLz5cbiAgICAgICAgICAgICAgICA8L0Rpc2FibGVkPlxuICAgICAgICAgICAgPC9GcmFnbWVudD5cbiAgICAgICAgKTtcbiAgICB9ICksXG5cbiAgICBzYXZlKCkge1xuICAgICAgICAvLyBSZW5kZXJpbmcgaW4gUEhQXG4gICAgICAgIHJldHVybiBudWxsO1xuICAgIH0sXG59ICk7IiwiXG4vKipcbiAqIEl0ZW0gQ29tcG9uZW50LlxuICpcbiAqIEBwYXJhbSB7c3RyaW5nfSBpdGVtVGl0bGUgLSBDdXJyZW50IGl0ZW0gdGl0bGUuXG4gKiBAcGFyYW0ge2Z1bmN0aW9ufSBjbGlja0hhbmRsZXIgLSB0aGlzIGlzIHRoZSBoYW5kbGluZyBmdW5jdGlvbiBmb3IgdGhlIGFkZC9yZW1vdmUgZnVuY3Rpb25cbiAqIEBwYXJhbSB7SW50ZWdlcn0gaXRlbUlkIC0gQ3VycmVudCBpdGVtIElEXG4gKiBAcGFyYW0gaWNvblxuICogQHJldHVybnMgeyp9IEl0ZW0gSFRNTC5cbiAqL1xuZXhwb3J0IGNvbnN0IEl0ZW0gPSAoeyB0aXRsZTogeyByZW5kZXJlZDogaXRlbVRpdGxlIH0gPSB7fSwgbmFtZSwgY2xpY2tIYW5kbGVyLCBpZDogaXRlbUlkLCBpY29uIH0pID0+IChcbiAgICA8YXJ0aWNsZSBjbGFzc05hbWU9XCJpdGVtXCI+XG4gICAgICAgIDxkaXYgY2xhc3NOYW1lPVwiaXRlbS1ib2R5XCI+XG4gICAgICAgICAgICA8aDMgY2xhc3NOYW1lPVwiaXRlbS10aXRsZVwiPntpdGVtVGl0bGV9e25hbWV9PC9oMz5cbiAgICAgICAgPC9kaXY+XG4gICAgICAgIDxidXR0b24gb25DbGljaz17KCkgPT4gY2xpY2tIYW5kbGVyKGl0ZW1JZCl9PntpY29ufTwvYnV0dG9uPlxuICAgIDwvYXJ0aWNsZT5cbik7IiwiaW1wb3J0IHsgSXRlbSB9IGZyb20gJy4vSXRlbSc7XG5cbmNvbnN0IHsgX18gfSA9IHdwLmkxOG47XG5cbi8qKlxuICogSXRlbUxpc3QgQ29tcG9uZW50XG4gKiBAcGFyYW0gb2JqZWN0IHByb3BzIC0gQ29tcG9uZW50IHByb3BzLlxuICogQHJldHVybnMgeyp9XG4gKiBAY29uc3RydWN0b3JcbiAqL1xuZXhwb3J0IGNvbnN0IEl0ZW1MaXN0ID0gcHJvcHMgPT4ge1xuICAgIGNvbnN0IHsgZmlsdGVyZWQgPSBmYWxzZSwgbG9hZGluZyA9IGZhbHNlLCBpdGVtcyA9IFtdLCBhY3Rpb24gPSAoKSA9PiB7fSwgaWNvbiA9IG51bGwgfSA9IHByb3BzO1xuXG4gICAgaWYgKGxvYWRpbmcpIHtcbiAgICAgICAgcmV0dXJuIDxwIGNsYXNzTmFtZT1cImxvYWRpbmctaXRlbXNcIj57X18oJ0xvYWRpbmcgLi4uJywgJ3ZvZGknKX08L3A+O1xuICAgIH1cblxuICAgIGlmIChmaWx0ZXJlZCAmJiBpdGVtcy5sZW5ndGggPCAxKSB7XG4gICAgICAgIHJldHVybiAoXG4gICAgICAgICAgICA8ZGl2IGNsYXNzTmFtZT1cIml0ZW0tbGlzdFwiPlxuICAgICAgICAgICAgICAgIDxwPntfXygnWW91ciBxdWVyeSB5aWVsZGVkIG5vIHJlc3VsdHMsIHBsZWFzZSB0cnkgYWdhaW4uJywgJ3ZvZGknKX08L3A+XG4gICAgICAgICAgICA8L2Rpdj5cbiAgICAgICAgKTtcbiAgICB9XG5cbiAgICBpZiAoICEgaXRlbXMgfHwgaXRlbXMubGVuZ3RoIDwgMSApIHtcbiAgICAgICAgcmV0dXJuIDxwIGNsYXNzTmFtZT1cIm5vLWl0ZW1zXCI+e19fKCdOb3QgZm91bmQuJywgJ3ZvZGknKX08L3A+XG4gICAgfVxuXG4gICAgcmV0dXJuIChcbiAgICAgICAgPGRpdiBjbGFzc05hbWU9XCJpdGVtLWxpc3RcIj5cbiAgICAgICAgICAgIHtpdGVtcy5tYXAoKGl0ZW0pID0+IDxJdGVtIGtleT17aXRlbS5pZH0gey4uLml0ZW19IGNsaWNrSGFuZGxlcj17YWN0aW9ufSBpY29uPXtpY29ufSAvPil9XG4gICAgICAgIDwvZGl2PlxuICAgICk7XG59OyIsImltcG9ydCB7IEl0ZW1MaXN0IH0gZnJvbSAnLi9JdGVtTGlzdCc7XG5pbXBvcnQgKiBhcyBhcGkgZnJvbSAnLi4vdXRpbHMvYXBpJztcbmltcG9ydCB7IHVuaXF1ZUJ5SWQsIGRlYm91bmNlIH0gZnJvbSAnLi4vdXRpbHMvdXNlZnVsLWZ1bmNzJztcblxuY29uc3QgeyBfXyB9ID0gd3AuaTE4bjtcbmNvbnN0IHsgSWNvbiB9ID0gd3AuY29tcG9uZW50cztcbmNvbnN0IHsgQ29tcG9uZW50IH0gPSB3cC5lbGVtZW50O1xuXG4vKipcbiAqIFBvc3RTZWxlY3RvciBDb21wb25lbnRcbiAqL1xuZXhwb3J0IGNsYXNzIFBvc3RTZWxlY3RvciBleHRlbmRzIENvbXBvbmVudCB7XG4gICAgLyoqXG4gICAgICogQ29uc3RydWN0b3IgZm9yIFBvc3RTZWxlY3RvciBDb21wb25lbnQuXG4gICAgICogU2V0cyB1cCBzdGF0ZSwgYW5kIGNyZWF0ZXMgYmluZGluZ3MgZm9yIGZ1bmN0aW9ucy5cbiAgICAgKiBAcGFyYW0gb2JqZWN0IHByb3BzIC0gY3VycmVudCBjb21wb25lbnQgcHJvcGVydGllcy5cbiAgICAgKi9cbiAgICBjb25zdHJ1Y3Rvcihwcm9wcykge1xuICAgICAgICBzdXBlciguLi5hcmd1bWVudHMpO1xuICAgICAgICB0aGlzLnByb3BzID0gcHJvcHM7XG5cbiAgICAgICAgdGhpcy5zdGF0ZSA9IHtcbiAgICAgICAgICAgIHBvc3RzOiBbXSxcbiAgICAgICAgICAgIGxvYWRpbmc6IGZhbHNlLFxuICAgICAgICAgICAgdHlwZTogcHJvcHMucG9zdFR5cGUgfHwgJ3Bvc3QnLFxuICAgICAgICAgICAgdHlwZXM6IFtdLFxuICAgICAgICAgICAgZmlsdGVyOiAnJyxcbiAgICAgICAgICAgIGZpbHRlckxvYWRpbmc6IGZhbHNlLFxuICAgICAgICAgICAgZmlsdGVyUG9zdHM6IFtdLFxuICAgICAgICAgICAgaW5pdGlhbExvYWRpbmc6IGZhbHNlLFxuICAgICAgICAgICAgc2VsZWN0ZWRQb3N0czogW10sXG4gICAgICAgIH07XG5cbiAgICAgICAgdGhpcy5hZGRQb3N0ID0gdGhpcy5hZGRQb3N0LmJpbmQodGhpcyk7XG4gICAgICAgIHRoaXMucmVtb3ZlUG9zdCA9IHRoaXMucmVtb3ZlUG9zdC5iaW5kKHRoaXMpO1xuICAgICAgICB0aGlzLmhhbmRsZUlucHV0RmlsdGVyQ2hhbmdlID0gdGhpcy5oYW5kbGVJbnB1dEZpbHRlckNoYW5nZS5iaW5kKHRoaXMpO1xuICAgICAgICB0aGlzLmRvUG9zdEZpbHRlciA9IGRlYm91bmNlKHRoaXMuZG9Qb3N0RmlsdGVyLmJpbmQodGhpcyksIDMwMCk7XG4gICAgICAgIHRoaXMuZ2V0U2VsZWN0ZWRQb3N0SWRzID0gdGhpcy5nZXRTZWxlY3RlZFBvc3RJZHMuYmluZCh0aGlzKTtcbiAgICAgICAgdGhpcy5nZXRTZWxlY3RlZFBvc3RzID0gdGhpcy5nZXRTZWxlY3RlZFBvc3RzLmJpbmQodGhpcyk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogV2hlbiB0aGUgY29tcG9uZW50IG1vdW50cyBpdCBjYWxscyB0aGlzIGZ1bmN0aW9uLlxuICAgICAqIEZldGNoZXMgcG9zdHMgdHlwZXMsIHNlbGVjdGVkIHBvc3RzIHRoZW4gbWFrZXMgZmlyc3QgY2FsbCBmb3IgcG9zdHNcbiAgICAgKi9cbiAgICBjb21wb25lbnREaWRNb3VudCgpIHtcbiAgICAgICAgdGhpcy5zZXRTdGF0ZSh7XG4gICAgICAgICAgICBpbml0aWFsTG9hZGluZzogdHJ1ZSxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgYXBpLmdldFBvc3RUeXBlcygpXG4gICAgICAgICAgICAudGhlbigoIHJlc3BvbnNlICkgPT4ge1xuICAgICAgICAgICAgICAgIHRoaXMuc2V0U3RhdGUoe1xuICAgICAgICAgICAgICAgICAgICB0eXBlczogcmVzcG9uc2VcbiAgICAgICAgICAgICAgICB9LCAoKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMucmV0cmlldmVTZWxlY3RlZFBvc3RzKClcbiAgICAgICAgICAgICAgICAgICAgICAgIC50aGVuKCggc2VsZWN0ZWRQb3N0cyApID0+IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZiggc2VsZWN0ZWRQb3N0cyApIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdGhpcy5zZXRTdGF0ZSh7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBpbml0aWFsTG9hZGluZzogZmFsc2UsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBzZWxlY3RlZFBvc3RzOiBzZWxlY3RlZFBvc3RzLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB0aGlzLnNldFN0YXRlKHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGluaXRpYWxMb2FkaW5nOiBmYWxzZSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgfSlcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH0pO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEdldFBvc3RzIHdyYXBwZXIsIGJ1aWxkcyB0aGUgcmVxdWVzdCBhcmd1bWVudCBiYXNlZCBzdGF0ZSBhbmQgcGFyYW1ldGVycyBwYXNzZWQvXG4gICAgICogQHBhcmFtIHtvYmplY3R9IGFyZ3MgLSBkZXNpcmVkIGFyZ3VtZW50cyAoY2FuIGJlIGVtcHR5KS5cbiAgICAgKiBAcmV0dXJucyB7UHJvbWlzZTxUPn1cbiAgICAgKi9cbiAgICBnZXRQb3N0cyhhcmdzID0ge30pIHtcbiAgICAgICAgY29uc3QgcG9zdElkcyA9IHRoaXMuZ2V0U2VsZWN0ZWRQb3N0SWRzKCk7XG5cbiAgICAgICAgY29uc3QgZGVmYXVsdEFyZ3MgPSB7XG4gICAgICAgICAgICBwZXJfcGFnZTogMTAsXG4gICAgICAgICAgICB0eXBlOiB0aGlzLnN0YXRlLnR5cGUsXG4gICAgICAgICAgICBzZWFyY2g6IHRoaXMuc3RhdGUuZmlsdGVyLFxuICAgICAgICB9O1xuXG4gICAgICAgIGNvbnN0IHJlcXVlc3RBcmd1bWVudHMgPSB7XG4gICAgICAgICAgICAuLi5kZWZhdWx0QXJncyxcbiAgICAgICAgICAgIC4uLmFyZ3NcbiAgICAgICAgfTtcblxuICAgICAgICByZXF1ZXN0QXJndW1lbnRzLnJlc3RCYXNlID0gdGhpcy5zdGF0ZS50eXBlc1t0aGlzLnN0YXRlLnR5cGVdLnJlc3RfYmFzZTtcblxuICAgICAgICByZXR1cm4gYXBpLmdldFBvc3RzKHJlcXVlc3RBcmd1bWVudHMpXG4gICAgICAgICAgICAudGhlbihyZXNwb25zZSA9PiB7XG4gICAgICAgICAgICAgICAgaWYgKHJlcXVlc3RBcmd1bWVudHMuc2VhcmNoKSB7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMuc2V0U3RhdGUoe1xuICAgICAgICAgICAgICAgICAgICAgICAgZmlsdGVyUG9zdHM6IHJlc3BvbnNlLmZpbHRlcigoeyBpZCB9KSA9PiBwb3N0SWRzLmluZGV4T2YoaWQpID09PSAtMSksXG4gICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiByZXNwb25zZTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICB0aGlzLnNldFN0YXRlKHtcbiAgICAgICAgICAgICAgICAgICAgcG9zdHM6IHVuaXF1ZUJ5SWQoWy4uLnRoaXMuc3RhdGUucG9zdHMsIC4uLnJlc3BvbnNlXSksXG4gICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICAvLyByZXR1cm4gcmVzcG9uc2UgdG8gY29udGludWUgdGhlIGNoYWluXG4gICAgICAgICAgICAgICAgcmV0dXJuIHJlc3BvbnNlO1xuICAgICAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogR2V0cyB0aGUgc2VsZWN0ZWQgcG9zdHMgYnkgaWQgZnJvbSB0aGUgYHBvc3RzYCBzdGF0ZSBvYmplY3QgYW5kIHNvcnRzIHRoZW0gYnkgdGhlaXIgcG9zaXRpb24gaW4gdGhlIHNlbGVjdGVkIGFycmF5LlxuICAgICAqIEByZXR1cm5zIEFycmF5IG9mIG9iamVjdHMuXG4gICAgICovXG4gICAgZ2V0U2VsZWN0ZWRQb3N0SWRzKCkge1xuICAgICAgICBjb25zdCB7IHNlbGVjdGVkUG9zdElkcyB9ID0gdGhpcy5wcm9wcztcblxuICAgICAgICBpZiggc2VsZWN0ZWRQb3N0SWRzICkge1xuICAgICAgICAgICAgY29uc3QgcG9zdElkcyA9IEFycmF5LmlzQXJyYXkoIHNlbGVjdGVkUG9zdElkcyApID8gc2VsZWN0ZWRQb3N0SWRzIDogc2VsZWN0ZWRQb3N0SWRzLnNwbGl0KCcsJyk7XG4gICAgICAgICAgICByZXR1cm4gcG9zdElkcztcbiAgICAgICAgfVxuXG4gICAgICAgIHJldHVybiBbXTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBHZXRzIHRoZSBzZWxlY3RlZCBwb3N0cyBieSBpZCBmcm9tIHRoZSBgcG9zdHNgIHN0YXRlIG9iamVjdCBhbmQgc29ydHMgdGhlbSBieSB0aGVpciBwb3NpdGlvbiBpbiB0aGUgc2VsZWN0ZWQgYXJyYXkuXG4gICAgICogQHJldHVybnMgQXJyYXkgb2Ygb2JqZWN0cy5cbiAgICAgKi9cbiAgICBnZXRTZWxlY3RlZFBvc3RzKCBwb3N0SWRzICkge1xuICAgICAgICBjb25zdCBwb3N0TGlzdCA9IHVuaXF1ZUJ5SWQoW1xuICAgICAgICAgICAgLi4udGhpcy5zdGF0ZS5maWx0ZXJQb3N0cyxcbiAgICAgICAgICAgIC4uLnRoaXMuc3RhdGUucG9zdHNcbiAgICAgICAgXSk7XG4gICAgICAgIGNvbnN0IHNlbGVjdGVkUG9zdHMgPSBwb3N0TGlzdFxuICAgICAgICAgICAgLmZpbHRlcigoeyBpZCB9KSA9PiBwb3N0SWRzLmluZGV4T2YoaWQpICE9PSAtMSlcbiAgICAgICAgICAgIC5zb3J0KChhLCBiKSA9PiB7XG4gICAgICAgICAgICAgICAgY29uc3QgYUluZGV4ID0gcG9zdElkcy5pbmRleE9mKGEuaWQpO1xuICAgICAgICAgICAgICAgIGNvbnN0IGJJbmRleCA9IHBvc3RJZHMuaW5kZXhPZihiLmlkKTtcblxuICAgICAgICAgICAgICAgIGlmIChhSW5kZXggPiBiSW5kZXgpIHtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIDE7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgaWYgKGFJbmRleCA8IGJJbmRleCkge1xuICAgICAgICAgICAgICAgICAgICByZXR1cm4gLTE7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgcmV0dXJuIDA7XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICB0aGlzLnNldFN0YXRlKHtcbiAgICAgICAgICAgIHNlbGVjdGVkUG9zdHM6IHNlbGVjdGVkUG9zdHMsXG4gICAgICAgIH0pO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIE1ha2VzIHRoZSBuZWNlc3NhcnkgYXBpIGNhbGxzIHRvIGZldGNoIHRoZSBzZWxlY3RlZCBwb3N0cyBhbmQgcmV0dXJucyBhIHByb21pc2UuXG4gICAgICogQHJldHVybnMgeyp9XG4gICAgICovXG4gICAgcmV0cmlldmVTZWxlY3RlZFBvc3RzKCkge1xuICAgICAgICBjb25zdCB7IHBvc3RUeXBlLCBzZWxlY3RlZFBvc3RJZHMgfSA9IHRoaXMucHJvcHM7XG4gICAgICAgIGNvbnN0IHsgdHlwZXMgfSA9IHRoaXMuc3RhdGU7XG5cbiAgICAgICAgY29uc3QgcG9zdElkcyA9IHRoaXMuZ2V0U2VsZWN0ZWRQb3N0SWRzKCkuam9pbignLCcpO1xuXG4gICAgICAgIGlmICggISBwb3N0SWRzICkge1xuICAgICAgICAgICAgLy8gcmV0dXJuIGEgZmFrZSBwcm9taXNlIHRoYXQgYXV0byByZXNvbHZlcy5cbiAgICAgICAgICAgIHJldHVybiBuZXcgUHJvbWlzZSgocmVzb2x2ZSkgPT4gcmVzb2x2ZSgpKTtcbiAgICAgICAgfVxuXG4gICAgICAgIGxldCBwb3N0X2FyZ3MgPSB7XG4gICAgICAgICAgICBpbmNsdWRlOiBwb3N0SWRzLFxuICAgICAgICAgICAgcGVyX3BhZ2U6IDEwMCxcbiAgICAgICAgICAgIHBvc3RUeXBlXG4gICAgICAgIH07XG5cbiAgICAgICAgaWYoIHRoaXMucHJvcHMucG9zdFN0YXR1cyApIHtcbiAgICAgICAgICAgIHBvc3RfYXJncy5zdGF0dXMgPSB0aGlzLnByb3BzLnBvc3RTdGF0dXM7XG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gdGhpcy5nZXRQb3N0cyh7XG4gICAgICAgICAgICAuLi5wb3N0X2FyZ3NcbiAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogQWRkcyBkZXNpcmVkIHBvc3QgaWQgdG8gdGhlIHNlbGVjdGVkUG9zdElkcyBMaXN0XG4gICAgICogQHBhcmFtIHtJbnRlZ2VyfSBwb3N0X2lkXG4gICAgICovXG4gICAgYWRkUG9zdChwb3N0X2lkKSB7XG4gICAgICAgIGlmICh0aGlzLnN0YXRlLmZpbHRlcikge1xuICAgICAgICAgICAgY29uc3QgcG9zdCA9IHRoaXMuc3RhdGUuZmlsdGVyUG9zdHMuZmlsdGVyKHAgPT4gcC5pZCA9PT0gcG9zdF9pZCk7XG4gICAgICAgICAgICBjb25zdCBwb3N0cyA9IHVuaXF1ZUJ5SWQoW1xuICAgICAgICAgICAgICAgIC4uLnRoaXMuc3RhdGUucG9zdHMsXG4gICAgICAgICAgICAgICAgLi4ucG9zdFxuICAgICAgICAgICAgXSk7XG5cbiAgICAgICAgICAgIHRoaXMuc2V0U3RhdGUoe1xuICAgICAgICAgICAgICAgIHBvc3RzXG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmKCB0aGlzLnByb3BzLnNlbGVjdFNpbmdsZSApIHtcbiAgICAgICAgICAgIGNvbnN0IHNlbGVjdGVkUG9zdElkcyA9IFsgcG9zdF9pZCBdO1xuICAgICAgICAgICAgdGhpcy5wcm9wcy51cGRhdGVTZWxlY3RlZFBvc3RJZHMoIHNlbGVjdGVkUG9zdElkcyApO1xuICAgICAgICAgICAgdGhpcy5nZXRTZWxlY3RlZFBvc3RzKCBzZWxlY3RlZFBvc3RJZHMgKTtcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgIGNvbnN0IHBvc3RJZHMgPSB0aGlzLmdldFNlbGVjdGVkUG9zdElkcygpO1xuICAgICAgICAgICAgY29uc3Qgc2VsZWN0ZWRQb3N0SWRzID0gWyAuLi5wb3N0SWRzLCBwb3N0X2lkIF07XG4gICAgICAgICAgICB0aGlzLnByb3BzLnVwZGF0ZVNlbGVjdGVkUG9zdElkcyggc2VsZWN0ZWRQb3N0SWRzICk7XG4gICAgICAgICAgICB0aGlzLmdldFNlbGVjdGVkUG9zdHMoIHNlbGVjdGVkUG9zdElkcyApO1xuICAgICAgICB9XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogUmVtb3ZlcyBkZXNpcmVkIHBvc3QgaWQgdG8gdGhlIHNlbGVjdGVkUG9zdElkcyBMaXN0XG4gICAgICogQHBhcmFtIHtJbnRlZ2VyfSBwb3N0X2lkXG4gICAgICovXG4gICAgcmVtb3ZlUG9zdChwb3N0X2lkKSB7XG4gICAgICAgIGNvbnN0IHBvc3RJZHMgPSB0aGlzLmdldFNlbGVjdGVkUG9zdElkcygpO1xuICAgICAgICBjb25zdCBzZWxlY3RlZFBvc3RJZHMgPSBbIC4uLnBvc3RJZHMgXS5maWx0ZXIoaWQgPT4gaWQgIT09IHBvc3RfaWQpO1xuICAgICAgICB0aGlzLnByb3BzLnVwZGF0ZVNlbGVjdGVkUG9zdElkcyggc2VsZWN0ZWRQb3N0SWRzICk7XG4gICAgICAgIHRoaXMuZ2V0U2VsZWN0ZWRQb3N0cyggc2VsZWN0ZWRQb3N0SWRzICk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogSGFuZGxlcyB0aGUgc2VhcmNoIGJveCBpbnB1dCB2YWx1ZVxuICAgICAqIEBwYXJhbSBzdHJpbmcgdHlwZSAtIGNvbWVzIGZyb20gdGhlIGV2ZW50IG9iamVjdCB0YXJnZXQuXG4gICAgICovXG4gICAgaGFuZGxlSW5wdXRGaWx0ZXJDaGFuZ2UoeyB0YXJnZXQ6IHsgdmFsdWU6ZmlsdGVyID0gJycgfSA9IHt9IH0gPSB7fSkge1xuICAgICAgICB0aGlzLnNldFN0YXRlKHtcbiAgICAgICAgICAgIGZpbHRlclxuICAgICAgICB9LCAoKSA9PiB7XG4gICAgICAgICAgICBpZiAoIWZpbHRlcikge1xuICAgICAgICAgICAgICAgIC8vIHJlbW92ZSBmaWx0ZXJlZCBwb3N0c1xuICAgICAgICAgICAgICAgIHJldHVybiB0aGlzLnNldFN0YXRlKHsgZmlsdGVyZWRQb3N0czogW10sIGZpbHRlcmluZzogZmFsc2UgfSk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHRoaXMuZG9Qb3N0RmlsdGVyKCk7XG4gICAgICAgIH0pXG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogQWN0dWFsIGFwaSBjYWxsIGZvciBzZWFyY2hpbmcgZm9yIHF1ZXJ5LCB0aGlzIGZ1bmN0aW9uIGlzIGRlYm91bmNlZCBpbiBjb25zdHJ1Y3Rvci5cbiAgICAgKi9cbiAgICBkb1Bvc3RGaWx0ZXIoKSB7XG4gICAgICAgIGNvbnN0IHsgZmlsdGVyID0gJycgfSA9IHRoaXMuc3RhdGU7XG5cbiAgICAgICAgaWYgKCFmaWx0ZXIpIHtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIHRoaXMuc2V0U3RhdGUoe1xuICAgICAgICAgICAgZmlsdGVyaW5nOiB0cnVlLFxuICAgICAgICAgICAgZmlsdGVyTG9hZGluZzogdHJ1ZVxuICAgICAgICB9KTtcblxuICAgICAgICBsZXQgcG9zdF9hcmdzID0ge307XG5cbiAgICAgICAgaWYoIHRoaXMucHJvcHMucG9zdFN0YXR1cyApIHtcbiAgICAgICAgICAgIHBvc3RfYXJncy5zdGF0dXMgPSB0aGlzLnByb3BzLnBvc3RTdGF0dXM7XG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLmdldFBvc3RzKHtcbiAgICAgICAgICAgIC4uLnBvc3RfYXJnc1xuICAgICAgICB9KS50aGVuKCgpID0+IHtcbiAgICAgICAgICAgIHRoaXMuc2V0U3RhdGUoe1xuICAgICAgICAgICAgICAgIGZpbHRlckxvYWRpbmc6IGZhbHNlXG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogUmVuZGVycyB0aGUgUG9zdFNlbGVjdG9yIGNvbXBvbmVudC5cbiAgICAgKi9cbiAgICByZW5kZXIoKSB7XG4gICAgICAgIGNvbnN0IHBvc3RMaXN0ID0gdGhpcy5zdGF0ZS5maWx0ZXJpbmcgJiYgIXRoaXMuc3RhdGUuZmlsdGVyTG9hZGluZyA/IHRoaXMuc3RhdGUuZmlsdGVyUG9zdHMgOiBbXTtcblxuICAgICAgICBjb25zdCBhZGRJY29uID0gPEljb24gaWNvbj1cInBsdXNcIiAvPjtcbiAgICAgICAgY29uc3QgcmVtb3ZlSWNvbiA9IDxJY29uIGljb249XCJtaW51c1wiIC8+O1xuXG4gICAgICAgIGNvbnN0IHNlYXJjaGlucHV0dW5pcXVlSWQgPSAnc2VhcmNoaW5wdXQtJyArIE1hdGgucmFuZG9tKCkudG9TdHJpbmcoMzYpLnN1YnN0cigyLCAxNik7XG5cbiAgICAgICAgcmV0dXJuIChcbiAgICAgICAgICAgIDxkaXYgY2xhc3NOYW1lPVwiY29tcG9uZW50cy1iYXNlLWNvbnRyb2wgY29tcG9uZW50cy1wb3N0LXNlbGVjdG9yXCI+XG4gICAgICAgICAgICAgICAgPGRpdiBjbGFzc05hbWU9XCJjb21wb25lbnRzLWJhc2UtY29udHJvbF9fZmllbGQtLXNlbGVjdGVkXCI+XG4gICAgICAgICAgICAgICAgICAgIDxoMj57X18oJ1NlYXJjaCBQb3N0JywgJ3ZvZGknKX08L2gyPlxuICAgICAgICAgICAgICAgICAgICA8SXRlbUxpc3RcbiAgICAgICAgICAgICAgICAgICAgICAgIGl0ZW1zPXsgWy4uLnRoaXMuc3RhdGUuc2VsZWN0ZWRQb3N0c10gfVxuICAgICAgICAgICAgICAgICAgICAgICAgbG9hZGluZz17dGhpcy5zdGF0ZS5pbml0aWFsTG9hZGluZ31cbiAgICAgICAgICAgICAgICAgICAgICAgIGFjdGlvbj17dGhpcy5yZW1vdmVQb3N0fVxuICAgICAgICAgICAgICAgICAgICAgICAgaWNvbj17cmVtb3ZlSWNvbn1cbiAgICAgICAgICAgICAgICAgICAgLz5cbiAgICAgICAgICAgICAgICA8L2Rpdj5cbiAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzTmFtZT1cImNvbXBvbmVudHMtYmFzZS1jb250cm9sX19maWVsZFwiPlxuICAgICAgICAgICAgICAgICAgICA8bGFiZWwgaHRtbEZvcj17c2VhcmNoaW5wdXR1bmlxdWVJZH0gY2xhc3NOYW1lPVwiY29tcG9uZW50cy1iYXNlLWNvbnRyb2xfX2xhYmVsXCI+XG4gICAgICAgICAgICAgICAgICAgICAgICA8SWNvbiBpY29uPVwic2VhcmNoXCIgLz5cbiAgICAgICAgICAgICAgICAgICAgPC9sYWJlbD5cbiAgICAgICAgICAgICAgICAgICAgPGlucHV0XG4gICAgICAgICAgICAgICAgICAgICAgICBjbGFzc05hbWU9XCJjb21wb25lbnRzLXRleHQtY29udHJvbF9faW5wdXRcIlxuICAgICAgICAgICAgICAgICAgICAgICAgaWQ9e3NlYXJjaGlucHV0dW5pcXVlSWR9XG4gICAgICAgICAgICAgICAgICAgICAgICB0eXBlPVwic2VhcmNoXCJcbiAgICAgICAgICAgICAgICAgICAgICAgIHBsYWNlaG9sZGVyPXtfXygnUGxlYXNlIGVudGVyIHlvdXIgc2VhcmNoIHF1ZXJ5Li4uJywgJ3ZvZGknKX1cbiAgICAgICAgICAgICAgICAgICAgICAgIHZhbHVlPXt0aGlzLnN0YXRlLmZpbHRlcn1cbiAgICAgICAgICAgICAgICAgICAgICAgIG9uQ2hhbmdlPXt0aGlzLmhhbmRsZUlucHV0RmlsdGVyQ2hhbmdlfVxuICAgICAgICAgICAgICAgICAgICAvPlxuICAgICAgICAgICAgICAgICAgICA8SXRlbUxpc3RcbiAgICAgICAgICAgICAgICAgICAgICAgIGl0ZW1zPXtwb3N0TGlzdH1cbiAgICAgICAgICAgICAgICAgICAgICAgIGxvYWRpbmc9e3RoaXMuc3RhdGUuaW5pdGlhbExvYWRpbmd8fHRoaXMuc3RhdGUubG9hZGluZ3x8dGhpcy5zdGF0ZS5maWx0ZXJMb2FkaW5nfVxuICAgICAgICAgICAgICAgICAgICAgICAgZmlsdGVyZWQ9e3RoaXMuc3RhdGUuZmlsdGVyaW5nfVxuICAgICAgICAgICAgICAgICAgICAgICAgYWN0aW9uPXt0aGlzLmFkZFBvc3R9XG4gICAgICAgICAgICAgICAgICAgICAgICBpY29uPXthZGRJY29ufVxuICAgICAgICAgICAgICAgICAgICAvPlxuICAgICAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICAgICAgPC9kaXY+XG4gICAgICAgICk7XG4gICAgfVxufSIsImltcG9ydCB7IFBvc3RTZWxlY3RvciB9IGZyb20gJy4vUG9zdFNlbGVjdG9yJztcbmltcG9ydCB7IFRlcm1TZWxlY3RvciB9IGZyb20gJy4vVGVybVNlbGVjdG9yJztcblxuY29uc3QgeyBfXyB9ID0gd3AuaTE4bjtcbmNvbnN0IHsgQ29tcG9uZW50IH0gPSB3cC5lbGVtZW50O1xuY29uc3QgeyBSYW5nZUNvbnRyb2wsIFNlbGVjdENvbnRyb2wsIENoZWNrYm94Q29udHJvbCB9ID0gd3AuY29tcG9uZW50cztcblxuLyoqXG4gKiBTaG9ydGNvZGVBdHRzIENvbXBvbmVudFxuICovXG5leHBvcnQgY2xhc3MgU2hvcnRjb2RlQXR0cyBleHRlbmRzIENvbXBvbmVudCB7XG4gICAgLyoqXG4gICAgICogQ29uc3RydWN0b3IgZm9yIFNob3J0Y29kZUF0dHMgQ29tcG9uZW50LlxuICAgICAqIFNldHMgdXAgc3RhdGUsIGFuZCBjcmVhdGVzIGJpbmRpbmdzIGZvciBmdW5jdGlvbnMuXG4gICAgICogQHBhcmFtIG9iamVjdCBwcm9wcyAtIGN1cnJlbnQgY29tcG9uZW50IHByb3BlcnRpZXMuXG4gICAgICovXG4gICAgY29uc3RydWN0b3IocHJvcHMpIHtcbiAgICAgICAgc3VwZXIoLi4uYXJndW1lbnRzKTtcbiAgICAgICAgdGhpcy5wcm9wcyA9IHByb3BzO1xuXG4gICAgICAgIHRoaXMub25DaGFuZ2VMaW1pdCA9IHRoaXMub25DaGFuZ2VMaW1pdC5iaW5kKHRoaXMpO1xuICAgICAgICB0aGlzLm9uQ2hhbmdlQ29sdW1ucyA9IHRoaXMub25DaGFuZ2VDb2x1bW5zLmJpbmQodGhpcyk7XG4gICAgICAgIHRoaXMub25DaGFuZ2VPcmRlcmJ5ID0gdGhpcy5vbkNoYW5nZU9yZGVyYnkuYmluZCh0aGlzKTtcbiAgICAgICAgdGhpcy5vbkNoYW5nZU9yZGVyID0gdGhpcy5vbkNoYW5nZU9yZGVyLmJpbmQodGhpcyk7XG4gICAgICAgIHRoaXMub25DaGFuZ2VJZHMgPSB0aGlzLm9uQ2hhbmdlSWRzLmJpbmQodGhpcyk7XG4gICAgICAgIHRoaXMub25DaGFuZ2VDYXRlZ29yeSA9IHRoaXMub25DaGFuZ2VDYXRlZ29yeS5iaW5kKHRoaXMpO1xuICAgICAgICB0aGlzLm9uQ2hhbmdlR2VucmUgPSB0aGlzLm9uQ2hhbmdlR2VucmUuYmluZCh0aGlzKTtcbiAgICAgICAgdGhpcy5vbkNoYW5nZUZlYXR1cmVkID0gdGhpcy5vbkNoYW5nZUZlYXR1cmVkLmJpbmQodGhpcyk7XG4gICAgICAgIHRoaXMub25DaGFuZ2VUb3BSYXRlZCA9IHRoaXMub25DaGFuZ2VUb3BSYXRlZC5iaW5kKHRoaXMpO1xuICAgIH1cblxuICAgIG9uQ2hhbmdlTGltaXQoIG5ld0xpbWl0ICkge1xuICAgICAgICB0aGlzLnByb3BzLnVwZGF0ZVNob3J0Y29kZUF0dHMoe1xuICAgICAgICAgICAgbGltaXQ6IG5ld0xpbWl0XG4gICAgICAgIH0pO1xuICAgIH1cblxuICAgIG9uQ2hhbmdlQ29sdW1ucyggbmV3Q29sdW1ucyApIHtcbiAgICAgICAgdGhpcy5wcm9wcy51cGRhdGVTaG9ydGNvZGVBdHRzKHtcbiAgICAgICAgICAgIGNvbHVtbnM6IG5ld0NvbHVtbnNcbiAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgb25DaGFuZ2VPcmRlcmJ5KCBuZXdPcmRlcmJ5ICkge1xuICAgICAgICB0aGlzLnByb3BzLnVwZGF0ZVNob3J0Y29kZUF0dHMoe1xuICAgICAgICAgICAgb3JkZXJieTogbmV3T3JkZXJieVxuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICBvbkNoYW5nZU9yZGVyKCBuZXdPcmRlciApIHtcbiAgICAgICAgdGhpcy5wcm9wcy51cGRhdGVTaG9ydGNvZGVBdHRzKHtcbiAgICAgICAgICAgIG9yZGVyOiBuZXdPcmRlclxuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICBvbkNoYW5nZUlkcyggbmV3SWRzICkge1xuICAgICAgICB0aGlzLnByb3BzLnVwZGF0ZVNob3J0Y29kZUF0dHMoe1xuICAgICAgICAgICAgaWRzOiBuZXdJZHMuam9pbignLCcpXG4gICAgICAgIH0pO1xuICAgIH1cblxuICAgIG9uQ2hhbmdlQ2F0ZWdvcnkoIG5ld0NhdGVnb3J5ICkge1xuICAgICAgICB0aGlzLnByb3BzLnVwZGF0ZVNob3J0Y29kZUF0dHMoe1xuICAgICAgICAgICAgY2F0ZWdvcnk6IG5ld0NhdGVnb3J5LmpvaW4oJywnKVxuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICBvbkNoYW5nZUdlbnJlKCBuZXdHZW5yZSApIHtcbiAgICAgICAgdGhpcy5wcm9wcy51cGRhdGVTaG9ydGNvZGVBdHRzKHtcbiAgICAgICAgICAgIGdlbnJlOiBuZXdHZW5yZS5qb2luKCcsJylcbiAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgb25DaGFuZ2VGZWF0dXJlZCggbmV3RmVhdHVyZWQgKSB7XG4gICAgICAgIHRoaXMucHJvcHMudXBkYXRlU2hvcnRjb2RlQXR0cyh7XG4gICAgICAgICAgICBmZWF0dXJlZDogbmV3RmVhdHVyZWRcbiAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgb25DaGFuZ2VUb3BSYXRlZCggbmV3VG9wUmF0ZWQgKSB7XG4gICAgICAgIHRoaXMucHJvcHMudXBkYXRlU2hvcnRjb2RlQXR0cyh7XG4gICAgICAgICAgICB0b3BfcmF0ZWQ6IG5ld1RvcFJhdGVkXG4gICAgICAgIH0pO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFJlbmRlcnMgdGhlIFNob3J0Y29kZUF0dHMgY29tcG9uZW50LlxuICAgICAqL1xuICAgIHJlbmRlcigpIHtcbiAgICAgICAgY29uc3QgeyBhdHRyaWJ1dGVzLCBwb3N0VHlwZSwgY2F0VGF4b25vbXksIGhpZGVGaWVsZHMgfSA9IHRoaXMucHJvcHM7XG4gICAgICAgIGNvbnN0IHsgbGltaXQsIGNvbHVtbnMsIG9yZGVyYnksIG9yZGVyLCBpZHMsIGNhdGVnb3J5LCBnZW5yZSwgZmVhdHVyZWQsIHRvcF9yYXRlZCB9ID0gYXR0cmlidXRlcztcblxuICAgICAgICByZXR1cm4gKFxuICAgICAgICAgICAgPGRpdj5cbiAgICAgICAgICAgICAgICB7ICEoIGhpZGVGaWVsZHMgJiYgaGlkZUZpZWxkcy5pbmNsdWRlcygnbGltaXQnKSApID8gKFxuICAgICAgICAgICAgICAgIDxSYW5nZUNvbnRyb2xcbiAgICAgICAgICAgICAgICAgICAgbGFiZWw9e19fKCdMaW1pdCcsICd2b2RpJyl9XG4gICAgICAgICAgICAgICAgICAgIHZhbHVlPXsgbGltaXQgfVxuICAgICAgICAgICAgICAgICAgICBvbkNoYW5nZT17IHRoaXMub25DaGFuZ2VMaW1pdCB9XG4gICAgICAgICAgICAgICAgICAgIG1pbj17IDEgfVxuICAgICAgICAgICAgICAgICAgICBtYXg9eyA1MCB9XG4gICAgICAgICAgICAgICAgLz5cbiAgICAgICAgICAgICAgICApIDogJycgfVxuICAgICAgICAgICAgICAgIHsgISggaGlkZUZpZWxkcyAmJiBoaWRlRmllbGRzLmluY2x1ZGVzKCdjb2x1bW5zJykgKSA/IChcbiAgICAgICAgICAgICAgICA8UmFuZ2VDb250cm9sXG4gICAgICAgICAgICAgICAgICAgIGxhYmVsPXtfXygnQ29sdW1ucycsICd2b2RpJyl9XG4gICAgICAgICAgICAgICAgICAgIHZhbHVlPXsgY29sdW1ucyB9XG4gICAgICAgICAgICAgICAgICAgIG9uQ2hhbmdlPXsgdGhpcy5vbkNoYW5nZUNvbHVtbnMgfVxuICAgICAgICAgICAgICAgICAgICBtaW49eyAxIH1cbiAgICAgICAgICAgICAgICAgICAgbWF4PXsgNiB9XG4gICAgICAgICAgICAgICAgLz5cbiAgICAgICAgICAgICAgICApIDogJycgfVxuICAgICAgICAgICAgICAgIHsgISggaGlkZUZpZWxkcyAmJiBoaWRlRmllbGRzLmluY2x1ZGVzKCdvcmRlcmJ5JykgKSA/IChcbiAgICAgICAgICAgICAgICA8U2VsZWN0Q29udHJvbFxuICAgICAgICAgICAgICAgICAgICBsYWJlbD17X18oJ09yZGVyYnknLCAndm9kaScpfVxuICAgICAgICAgICAgICAgICAgICB2YWx1ZT17IG9yZGVyYnkgfVxuICAgICAgICAgICAgICAgICAgICBvcHRpb25zPXsgW1xuICAgICAgICAgICAgICAgICAgICAgICAgeyBsYWJlbDogX18oJ1RpdGxlJywgJ3ZvZGknKSwgdmFsdWU6ICd0aXRsZScgfSxcbiAgICAgICAgICAgICAgICAgICAgICAgIHsgbGFiZWw6IF9fKCdEYXRlJywgJ3ZvZGknKSwgdmFsdWU6ICggcG9zdFR5cGUgPT09ICdtb3ZpZScgPyAncmVsZWFzZV9kYXRlJyA6ICdkYXRlJyApIH0sXG4gICAgICAgICAgICAgICAgICAgICAgICB7IGxhYmVsOiBfXygnSUQnLCAndm9kaScpLCB2YWx1ZTogJ2lkJyB9LFxuICAgICAgICAgICAgICAgICAgICAgICAgeyBsYWJlbDogX18oJ1JhbmRvbScsICd2b2RpJyksIHZhbHVlOiAncmFuZCcgfSxcbiAgICAgICAgICAgICAgICAgICAgXSB9XG4gICAgICAgICAgICAgICAgICAgIG9uQ2hhbmdlPXsgdGhpcy5vbkNoYW5nZU9yZGVyYnkgfVxuICAgICAgICAgICAgICAgIC8+XG4gICAgICAgICAgICAgICAgKSA6ICcnIH1cbiAgICAgICAgICAgICAgICB7ICEoIGhpZGVGaWVsZHMgJiYgaGlkZUZpZWxkcy5pbmNsdWRlcygnb3JkZXInKSApID8gKFxuICAgICAgICAgICAgICAgIDxTZWxlY3RDb250cm9sXG4gICAgICAgICAgICAgICAgICAgIGxhYmVsPXtfXygnT3JkZXInLCAndm9kaScpfVxuICAgICAgICAgICAgICAgICAgICB2YWx1ZT17IG9yZGVyIH1cbiAgICAgICAgICAgICAgICAgICAgb3B0aW9ucz17IFtcbiAgICAgICAgICAgICAgICAgICAgICAgIHsgbGFiZWw6IF9fKCdBU0MnLCAndm9kaScpLCB2YWx1ZTogJ0FTQycgfSxcbiAgICAgICAgICAgICAgICAgICAgICAgIHsgbGFiZWw6IF9fKCdERVNDJywgJ3ZvZGknKSwgdmFsdWU6ICdERVNDJyB9LFxuICAgICAgICAgICAgICAgICAgICBdIH1cbiAgICAgICAgICAgICAgICAgICAgb25DaGFuZ2U9eyB0aGlzLm9uQ2hhbmdlT3JkZXIgfVxuICAgICAgICAgICAgICAgIC8+XG4gICAgICAgICAgICAgICAgKSA6ICcnIH1cbiAgICAgICAgICAgICAgICB7ICEoIGhpZGVGaWVsZHMgJiYgaGlkZUZpZWxkcy5pbmNsdWRlcygnaWRzJykgKSA/IChcbiAgICAgICAgICAgICAgICA8UG9zdFNlbGVjdG9yXG4gICAgICAgICAgICAgICAgICAgIHBvc3RUeXBlID0geyBwb3N0VHlwZSB9XG4gICAgICAgICAgICAgICAgICAgIHNlbGVjdGVkUG9zdElkcz17IGlkcyA/IGlkcy5zcGxpdCgnLCcpLm1hcChOdW1iZXIpIDogW10gfVxuICAgICAgICAgICAgICAgICAgICB1cGRhdGVTZWxlY3RlZFBvc3RJZHM9eyB0aGlzLm9uQ2hhbmdlSWRzIH1cbiAgICAgICAgICAgICAgICAvPlxuICAgICAgICAgICAgICAgICkgOiAnJyB9XG4gICAgICAgICAgICAgICAgeyAoIHBvc3RUeXBlID09PSAndmlkZW8nICkgJiYgISggaGlkZUZpZWxkcyAmJiBoaWRlRmllbGRzLmluY2x1ZGVzKCdjYXRlZ29yeScpICkgPyAoXG4gICAgICAgICAgICAgICAgPFRlcm1TZWxlY3RvclxuICAgICAgICAgICAgICAgICAgICBwb3N0VHlwZSA9IHsgcG9zdFR5cGUgfVxuICAgICAgICAgICAgICAgICAgICB0YXhvbm9teSA9IHsgY2F0VGF4b25vbXkgfVxuICAgICAgICAgICAgICAgICAgICBzZWxlY3RlZFRlcm1JZHM9eyBjYXRlZ29yeSA/IGNhdGVnb3J5LnNwbGl0KCcsJykubWFwKE51bWJlcikgOiBbXSB9XG4gICAgICAgICAgICAgICAgICAgIHVwZGF0ZVNlbGVjdGVkVGVybUlkcz17IHRoaXMub25DaGFuZ2VDYXRlZ29yeSB9XG4gICAgICAgICAgICAgICAgLz5cbiAgICAgICAgICAgICAgICApIDogKFxuICAgICAgICAgICAgICAgICEoIGhpZGVGaWVsZHMgJiYgaGlkZUZpZWxkcy5pbmNsdWRlcygnZ2VucmUnKSApID8gKFxuICAgICAgICAgICAgICAgIDxUZXJtU2VsZWN0b3JcbiAgICAgICAgICAgICAgICAgICAgcG9zdFR5cGUgPSB7IHBvc3RUeXBlIH1cbiAgICAgICAgICAgICAgICAgICAgdGF4b25vbXkgPSB7IGNhdFRheG9ub215IH1cbiAgICAgICAgICAgICAgICAgICAgc2VsZWN0ZWRUZXJtSWRzPXsgZ2VucmUgPyBnZW5yZS5zcGxpdCgnLCcpLm1hcChOdW1iZXIpIDogW10gfVxuICAgICAgICAgICAgICAgICAgICB1cGRhdGVTZWxlY3RlZFRlcm1JZHM9eyB0aGlzLm9uQ2hhbmdlR2VucmUgfVxuICAgICAgICAgICAgICAgIC8+XG4gICAgICAgICAgICAgICAgKSA6ICcnICkgfVxuICAgICAgICAgICAgICAgIHsgISggaGlkZUZpZWxkcyAmJiBoaWRlRmllbGRzLmluY2x1ZGVzKCdmZWF0dXJlZCcpICkgPyAoXG4gICAgICAgICAgICAgICAgPENoZWNrYm94Q29udHJvbFxuICAgICAgICAgICAgICAgICAgICBsYWJlbD17X18oJ0ZlYXR1cmVkJywgJ3ZvZGknKX1cbiAgICAgICAgICAgICAgICAgICAgaGVscD17X18oJ0NoZWNrIHRvIHNlbGVjdCBmZWF0dXJlZCBwb3N0cy4nLCAndm9kaScpfVxuICAgICAgICAgICAgICAgICAgICBjaGVja2VkPXsgZmVhdHVyZWQgfVxuICAgICAgICAgICAgICAgICAgICBvbkNoYW5nZT17IHRoaXMub25DaGFuZ2VGZWF0dXJlZCB9XG4gICAgICAgICAgICAgICAgLz5cbiAgICAgICAgICAgICAgICApIDogJycgfVxuICAgICAgICAgICAgICAgIHsgISggaGlkZUZpZWxkcyAmJiBoaWRlRmllbGRzLmluY2x1ZGVzKCd0b3BfcmF0ZWQnKSApID8gKFxuICAgICAgICAgICAgICAgIDxDaGVja2JveENvbnRyb2xcbiAgICAgICAgICAgICAgICAgICAgbGFiZWw9e19fKCdUb3AgUmF0ZWQnLCAndm9kaScpfVxuICAgICAgICAgICAgICAgICAgICBoZWxwPXtfXygnQ2hlY2sgdG8gc2VsZWN0IHRvcCByYXRlZCBwb3N0cy4nLCAndm9kaScpfVxuICAgICAgICAgICAgICAgICAgICBjaGVja2VkPXsgdG9wX3JhdGVkIH1cbiAgICAgICAgICAgICAgICAgICAgb25DaGFuZ2U9eyB0aGlzLm9uQ2hhbmdlVG9wUmF0ZWQgfVxuICAgICAgICAgICAgICAgIC8+XG4gICAgICAgICAgICAgICAgKSA6ICcnIH1cbiAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICApO1xuICAgIH1cbn0iLCJpbXBvcnQgeyBJdGVtTGlzdCB9IGZyb20gXCIuL0l0ZW1MaXN0XCI7XG5pbXBvcnQgKiBhcyBhcGkgZnJvbSAnLi4vdXRpbHMvYXBpJztcbmltcG9ydCB7IHVuaXF1ZUJ5SWQsIGRlYm91bmNlIH0gZnJvbSAnLi4vdXRpbHMvdXNlZnVsLWZ1bmNzJztcblxuY29uc3QgeyBfXyB9ID0gd3AuaTE4bjtcbmNvbnN0IHsgSWNvbiB9ID0gd3AuY29tcG9uZW50cztcbmNvbnN0IHsgQ29tcG9uZW50IH0gPSB3cC5lbGVtZW50O1xuXG4vKipcbiAqIFRlcm1TZWxlY3RvciBDb21wb25lbnRcbiAqL1xuZXhwb3J0IGNsYXNzIFRlcm1TZWxlY3RvciBleHRlbmRzIENvbXBvbmVudCB7XG4gICAgLyoqXG4gICAgICogQ29uc3RydWN0b3IgZm9yIFRlcm1TZWxlY3RvciBDb21wb25lbnQuXG4gICAgICogU2V0cyB1cCBzdGF0ZSwgYW5kIGNyZWF0ZXMgYmluZGluZ3MgZm9yIGZ1bmN0aW9ucy5cbiAgICAgKiBAcGFyYW0gb2JqZWN0IHByb3BzIC0gY3VycmVudCBjb21wb25lbnQgcHJvcGVydGllcy5cbiAgICAgKi9cbiAgICBjb25zdHJ1Y3Rvcihwcm9wcykge1xuICAgICAgICBzdXBlciguLi5hcmd1bWVudHMpO1xuICAgICAgICB0aGlzLnByb3BzID0gcHJvcHM7XG5cbiAgICAgICAgdGhpcy5zdGF0ZSA9IHtcbiAgICAgICAgICAgIHRlcm1zOiBbXSxcbiAgICAgICAgICAgIGxvYWRpbmc6IGZhbHNlLFxuICAgICAgICAgICAgdHlwZTogcHJvcHMucG9zdFR5cGUgfHwgJ3Bvc3QnLFxuICAgICAgICAgICAgdGF4b25vbXk6IHByb3BzLnRheG9ub215IHx8ICdjYXRlZ29yeScsXG4gICAgICAgICAgICB0YXhvbm9taWVzOiBbXSxcbiAgICAgICAgICAgIGZpbHRlcjogJycsXG4gICAgICAgICAgICBmaWx0ZXJMb2FkaW5nOiBmYWxzZSxcbiAgICAgICAgICAgIGZpbHRlclRlcm1zOiBbXSxcbiAgICAgICAgICAgIGluaXRpYWxMb2FkaW5nOiBmYWxzZSxcbiAgICAgICAgfTtcblxuICAgICAgICB0aGlzLmFkZFRlcm0gPSB0aGlzLmFkZFRlcm0uYmluZCh0aGlzKTtcbiAgICAgICAgdGhpcy5yZW1vdmVUZXJtID0gdGhpcy5yZW1vdmVUZXJtLmJpbmQodGhpcyk7XG4gICAgICAgIHRoaXMuaGFuZGxlSW5wdXRGaWx0ZXJDaGFuZ2UgPSB0aGlzLmhhbmRsZUlucHV0RmlsdGVyQ2hhbmdlLmJpbmQodGhpcyk7XG4gICAgICAgIHRoaXMuZG9UZXJtRmlsdGVyID0gZGVib3VuY2UodGhpcy5kb1Rlcm1GaWx0ZXIuYmluZCh0aGlzKSwgMzAwKTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBXaGVuIHRoZSBjb21wb25lbnQgbW91bnRzIGl0IGNhbGxzIHRoaXMgZnVuY3Rpb24uXG4gICAgICogRmV0Y2hlcyB0ZXJtcyB0YXhvbm9taWVzLCBzZWxlY3RlZCB0ZXJtcyB0aGVuIG1ha2VzIGZpcnN0IGNhbGwgZm9yIHRlcm1zXG4gICAgICovXG4gICAgY29tcG9uZW50RGlkTW91bnQoKSB7XG4gICAgICAgIHRoaXMuc2V0U3RhdGUoe1xuICAgICAgICAgICAgaW5pdGlhbExvYWRpbmc6IHRydWUsXG4gICAgICAgIH0pO1xuXG4gICAgICAgIGFwaS5nZXRUYXhvbm9taWVzKCB7IHR5cGU6IHRoaXMuc3RhdGUudHlwZSB9IClcbiAgICAgICAgICAgIC50aGVuKCggcmVzcG9uc2UgKSA9PiB7XG4gICAgICAgICAgICAgICAgdGhpcy5zZXRTdGF0ZSh7XG4gICAgICAgICAgICAgICAgICAgIHRheG9ub21pZXM6IHJlc3BvbnNlXG4gICAgICAgICAgICAgICAgfSwgKCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICB0aGlzLnJldHJpZXZlU2VsZWN0ZWRUZXJtcygpXG4gICAgICAgICAgICAgICAgICAgICAgICAudGhlbigoKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdGhpcy5zZXRTdGF0ZSh7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGluaXRpYWxMb2FkaW5nOiBmYWxzZSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH0pXG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9KTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBHZXRUZXJtcyB3cmFwcGVyLCBidWlsZHMgdGhlIHJlcXVlc3QgYXJndW1lbnQgYmFzZWQgc3RhdGUgYW5kIHBhcmFtZXRlcnMgcGFzc2VkL1xuICAgICAqIEBwYXJhbSB7b2JqZWN0fSBhcmdzIC0gZGVzaXJlZCBhcmd1bWVudHMgKGNhbiBiZSBlbXB0eSkuXG4gICAgICogQHJldHVybnMge1Byb21pc2U8VD59XG4gICAgICovXG4gICAgZ2V0VGVybXMoYXJncyA9IHt9KSB7XG4gICAgICAgIGNvbnN0IHsgc2VsZWN0ZWRUZXJtSWRzIH0gPSB0aGlzLnByb3BzO1xuXG4gICAgICAgIGNvbnN0IGRlZmF1bHRBcmdzID0ge1xuICAgICAgICAgICAgcGVyX3BhZ2U6IDEwLFxuICAgICAgICAgICAgdHlwZTogdGhpcy5zdGF0ZS50eXBlLFxuICAgICAgICAgICAgdGF4b25vbXk6IHRoaXMuc3RhdGUudGF4b25vbXksXG4gICAgICAgICAgICBzZWFyY2g6IHRoaXMuc3RhdGUuZmlsdGVyLFxuICAgICAgICB9O1xuXG4gICAgICAgIGNvbnN0IHJlcXVlc3RBcmd1bWVudHMgPSB7XG4gICAgICAgICAgICAuLi5kZWZhdWx0QXJncyxcbiAgICAgICAgICAgIC4uLmFyZ3NcbiAgICAgICAgfTtcblxuICAgICAgICByZXF1ZXN0QXJndW1lbnRzLnJlc3RCYXNlID0gdGhpcy5zdGF0ZS50YXhvbm9taWVzW3RoaXMuc3RhdGUudGF4b25vbXldLnJlc3RfYmFzZTtcblxuICAgICAgICByZXR1cm4gYXBpLmdldFRlcm1zKHJlcXVlc3RBcmd1bWVudHMpXG4gICAgICAgICAgICAudGhlbihyZXNwb25zZSA9PiB7XG4gICAgICAgICAgICAgICAgaWYgKHJlcXVlc3RBcmd1bWVudHMuc2VhcmNoKSB7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMuc2V0U3RhdGUoe1xuICAgICAgICAgICAgICAgICAgICAgICAgZmlsdGVyVGVybXM6IHJlc3BvbnNlLmZpbHRlcigoeyBpZCB9KSA9PiBzZWxlY3RlZFRlcm1JZHMuaW5kZXhPZihpZCkgPT09IC0xKSxcbiAgICAgICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHJlc3BvbnNlO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIHRoaXMuc2V0U3RhdGUoe1xuICAgICAgICAgICAgICAgICAgICB0ZXJtczogdW5pcXVlQnlJZChbLi4udGhpcy5zdGF0ZS50ZXJtcywgLi4ucmVzcG9uc2VdKSxcbiAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgIC8vIHJldHVybiByZXNwb25zZSB0byBjb250aW51ZSB0aGUgY2hhaW5cbiAgICAgICAgICAgICAgICByZXR1cm4gcmVzcG9uc2U7XG4gICAgICAgICAgICB9KTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBHZXRzIHRoZSBzZWxlY3RlZCB0ZXJtcyBieSBpZCBmcm9tIHRoZSBgdGVybXNgIHN0YXRlIG9iamVjdCBhbmQgc29ydHMgdGhlbSBieSB0aGVpciBwb3NpdGlvbiBpbiB0aGUgc2VsZWN0ZWQgYXJyYXkuXG4gICAgICogQHJldHVybnMgQXJyYXkgb2Ygb2JqZWN0cy5cbiAgICAgKi9cbiAgICBnZXRTZWxlY3RlZFRlcm1zKCkge1xuICAgICAgICBjb25zdCB7IHNlbGVjdGVkVGVybUlkcyB9ID0gdGhpcy5wcm9wcztcbiAgICAgICAgcmV0dXJuIHRoaXMuc3RhdGUudGVybXNcbiAgICAgICAgICAgIC5maWx0ZXIoKHsgaWQgfSkgPT4gc2VsZWN0ZWRUZXJtSWRzLmluZGV4T2YoaWQpICE9PSAtMSlcbiAgICAgICAgICAgIC5zb3J0KChhLCBiKSA9PiB7XG4gICAgICAgICAgICAgICAgY29uc3QgYUluZGV4ID0gdGhpcy5wcm9wcy5zZWxlY3RlZFRlcm1JZHMuaW5kZXhPZihhLmlkKTtcbiAgICAgICAgICAgICAgICBjb25zdCBiSW5kZXggPSB0aGlzLnByb3BzLnNlbGVjdGVkVGVybUlkcy5pbmRleE9mKGIuaWQpO1xuXG4gICAgICAgICAgICAgICAgaWYgKGFJbmRleCA+IGJJbmRleCkge1xuICAgICAgICAgICAgICAgICAgICByZXR1cm4gMTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBpZiAoYUluZGV4IDwgYkluZGV4KSB7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybiAtMTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICByZXR1cm4gMDtcbiAgICAgICAgICAgIH0pO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIE1ha2VzIHRoZSBuZWNlc3NhcnkgYXBpIGNhbGxzIHRvIGZldGNoIHRoZSBzZWxlY3RlZCB0ZXJtcyBhbmQgcmV0dXJucyBhIHByb21pc2UuXG4gICAgICogQHJldHVybnMgeyp9XG4gICAgICovXG4gICAgcmV0cmlldmVTZWxlY3RlZFRlcm1zKCkge1xuICAgICAgICBjb25zdCB7IHRlcm1UeXBlLCBzZWxlY3RlZFRlcm1JZHMgfSA9IHRoaXMucHJvcHM7XG4gICAgICAgIGNvbnN0IHsgdGF4b25vbWllcyB9ID0gdGhpcy5zdGF0ZTtcblxuICAgICAgICBpZiAoIHNlbGVjdGVkVGVybUlkcyAmJiAhc2VsZWN0ZWRUZXJtSWRzLmxlbmd0aCA+IDAgKSB7XG4gICAgICAgICAgICAvLyByZXR1cm4gYSBmYWtlIHByb21pc2UgdGhhdCBhdXRvIHJlc29sdmVzLlxuICAgICAgICAgICAgcmV0dXJuIG5ldyBQcm9taXNlKChyZXNvbHZlKSA9PiByZXNvbHZlKCkpO1xuICAgICAgICB9XG5cbiAgICAgICAgcmV0dXJuIHRoaXMuZ2V0VGVybXMoe1xuICAgICAgICAgICAgaW5jbHVkZTogdGhpcy5wcm9wcy5zZWxlY3RlZFRlcm1JZHMuam9pbignLCcpLFxuICAgICAgICAgICAgcGVyX3BhZ2U6IDEwMCxcbiAgICAgICAgICAgIHRlcm1UeXBlXG4gICAgICAgIH0pO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEFkZHMgZGVzaXJlZCB0ZXJtIGlkIHRvIHRoZSBzZWxlY3RlZFRlcm1JZHMgTGlzdFxuICAgICAqIEBwYXJhbSB7SW50ZWdlcn0gdGVybV9pZFxuICAgICAqL1xuICAgIGFkZFRlcm0odGVybV9pZCkge1xuICAgICAgICBpZiAodGhpcy5zdGF0ZS5maWx0ZXIpIHtcbiAgICAgICAgICAgIGNvbnN0IHRlcm0gPSB0aGlzLnN0YXRlLmZpbHRlclRlcm1zLmZpbHRlcihwID0+IHAuaWQgPT09IHRlcm1faWQpO1xuICAgICAgICAgICAgY29uc3QgdGVybXMgPSB1bmlxdWVCeUlkKFtcbiAgICAgICAgICAgICAgICAuLi50aGlzLnN0YXRlLnRlcm1zLFxuICAgICAgICAgICAgICAgIC4uLnRlcm1cbiAgICAgICAgICAgIF0pO1xuXG4gICAgICAgICAgICB0aGlzLnNldFN0YXRlKHtcbiAgICAgICAgICAgICAgICB0ZXJtc1xuICAgICAgICAgICAgfSk7XG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLnByb3BzLnVwZGF0ZVNlbGVjdGVkVGVybUlkcyhbXG4gICAgICAgICAgICAuLi50aGlzLnByb3BzLnNlbGVjdGVkVGVybUlkcyxcbiAgICAgICAgICAgIHRlcm1faWRcbiAgICAgICAgXSk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogUmVtb3ZlcyBkZXNpcmVkIHRlcm0gaWQgdG8gdGhlIHNlbGVjdGVkVGVybUlkcyBMaXN0XG4gICAgICogQHBhcmFtIHtJbnRlZ2VyfSB0ZXJtX2lkXG4gICAgICovXG4gICAgcmVtb3ZlVGVybSh0ZXJtX2lkKSB7XG4gICAgICAgIHRoaXMucHJvcHMudXBkYXRlU2VsZWN0ZWRUZXJtSWRzKFtcbiAgICAgICAgICAgIC4uLnRoaXMucHJvcHMuc2VsZWN0ZWRUZXJtSWRzXG4gICAgICAgIF0uZmlsdGVyKGlkID0+IGlkICE9PSB0ZXJtX2lkKSk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogSGFuZGxlcyB0aGUgc2VhcmNoIGJveCBpbnB1dCB2YWx1ZVxuICAgICAqIEBwYXJhbSBzdHJpbmcgdHlwZSAtIGNvbWVzIGZyb20gdGhlIGV2ZW50IG9iamVjdCB0YXJnZXQuXG4gICAgICovXG4gICAgaGFuZGxlSW5wdXRGaWx0ZXJDaGFuZ2UoeyB0YXJnZXQ6IHsgdmFsdWU6ZmlsdGVyID0gJycgfSA9IHt9IH0gPSB7fSkge1xuICAgICAgICB0aGlzLnNldFN0YXRlKHtcbiAgICAgICAgICAgIGZpbHRlclxuICAgICAgICB9LCAoKSA9PiB7XG4gICAgICAgICAgICBpZiAoIWZpbHRlcikge1xuICAgICAgICAgICAgICAgIC8vIHJlbW92ZSBmaWx0ZXJlZCB0ZXJtc1xuICAgICAgICAgICAgICAgIHJldHVybiB0aGlzLnNldFN0YXRlKHsgZmlsdGVyZWRUZXJtczogW10sIGZpbHRlcmluZzogZmFsc2UgfSk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHRoaXMuZG9UZXJtRmlsdGVyKCk7XG4gICAgICAgIH0pXG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogQWN0dWFsIGFwaSBjYWxsIGZvciBzZWFyY2hpbmcgZm9yIHF1ZXJ5LCB0aGlzIGZ1bmN0aW9uIGlzIGRlYm91bmNlZCBpbiBjb25zdHJ1Y3Rvci5cbiAgICAgKi9cbiAgICBkb1Rlcm1GaWx0ZXIoKSB7XG4gICAgICAgIGNvbnN0IHsgZmlsdGVyID0gJycgfSA9IHRoaXMuc3RhdGU7XG5cbiAgICAgICAgaWYgKCFmaWx0ZXIpIHtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIHRoaXMuc2V0U3RhdGUoe1xuICAgICAgICAgICAgZmlsdGVyaW5nOiB0cnVlLFxuICAgICAgICAgICAgZmlsdGVyTG9hZGluZzogdHJ1ZVxuICAgICAgICB9KTtcblxuICAgICAgICB0aGlzLmdldFRlcm1zKClcbiAgICAgICAgICAgIC50aGVuKCgpID0+IHtcbiAgICAgICAgICAgICAgICB0aGlzLnNldFN0YXRlKHtcbiAgICAgICAgICAgICAgICAgICAgZmlsdGVyTG9hZGluZzogZmFsc2VcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH0pO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFJlbmRlcnMgdGhlIFRlcm1TZWxlY3RvciBjb21wb25lbnQuXG4gICAgICovXG4gICAgcmVuZGVyKCkge1xuICAgICAgICBjb25zdCBpc0ZpbHRlcmVkID0gdGhpcy5zdGF0ZS5maWx0ZXJpbmc7XG4gICAgICAgIGNvbnN0IHRlcm1MaXN0ID0gaXNGaWx0ZXJlZCAmJiAhdGhpcy5zdGF0ZS5maWx0ZXJMb2FkaW5nID8gdGhpcy5zdGF0ZS5maWx0ZXJUZXJtcyA6IFtdO1xuICAgICAgICBjb25zdCBTZWxlY3RlZFRlcm1MaXN0ICA9IHRoaXMuZ2V0U2VsZWN0ZWRUZXJtcygpO1xuXG4gICAgICAgIGNvbnN0IGFkZEljb24gPSA8SWNvbiBpY29uPVwicGx1c1wiIC8+O1xuICAgICAgICBjb25zdCByZW1vdmVJY29uID0gPEljb24gaWNvbj1cIm1pbnVzXCIgLz47XG5cbiAgICAgICAgY29uc3Qgc2VhcmNoaW5wdXR1bmlxdWVJZCA9ICdzZWFyY2hpbnB1dC0nICsgTWF0aC5yYW5kb20oKS50b1N0cmluZygzNikuc3Vic3RyKDIsIDE2KTtcblxuICAgICAgICByZXR1cm4gKFxuICAgICAgICAgICAgPGRpdiBjbGFzc05hbWU9XCJjb21wb25lbnRzLWJhc2UtY29udHJvbCBjb21wb25lbnRzLXRlcm0tc2VsZWN0b3JcIj5cbiAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzTmFtZT1cImNvbXBvbmVudHMtYmFzZS1jb250cm9sX19maWVsZC0tc2VsZWN0ZWRcIj5cbiAgICAgICAgICAgICAgICAgICAgPGgyPntfXygnU2VhcmNoIFRlcm0nLCAndm9kaScpfTwvaDI+XG4gICAgICAgICAgICAgICAgICAgIDxJdGVtTGlzdFxuICAgICAgICAgICAgICAgICAgICAgICAgaXRlbXM9e1NlbGVjdGVkVGVybUxpc3R9XG4gICAgICAgICAgICAgICAgICAgICAgICBsb2FkaW5nPXt0aGlzLnN0YXRlLmluaXRpYWxMb2FkaW5nfVxuICAgICAgICAgICAgICAgICAgICAgICAgYWN0aW9uPXt0aGlzLnJlbW92ZVRlcm19XG4gICAgICAgICAgICAgICAgICAgICAgICBpY29uPXtyZW1vdmVJY29ufVxuICAgICAgICAgICAgICAgICAgICAvPlxuICAgICAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICAgICAgICAgIDxkaXYgY2xhc3NOYW1lPVwiY29tcG9uZW50cy1iYXNlLWNvbnRyb2xfX2ZpZWxkXCI+XG4gICAgICAgICAgICAgICAgICAgIDxsYWJlbCBodG1sRm9yPXtzZWFyY2hpbnB1dHVuaXF1ZUlkfSBjbGFzc05hbWU9XCJjb21wb25lbnRzLWJhc2UtY29udHJvbF9fbGFiZWxcIj5cbiAgICAgICAgICAgICAgICAgICAgICAgIDxJY29uIGljb249XCJzZWFyY2hcIiAvPlxuICAgICAgICAgICAgICAgICAgICA8L2xhYmVsPlxuICAgICAgICAgICAgICAgICAgICA8aW5wdXRcbiAgICAgICAgICAgICAgICAgICAgICAgIGNsYXNzTmFtZT1cImNvbXBvbmVudHMtdGV4dC1jb250cm9sX19pbnB1dFwiXG4gICAgICAgICAgICAgICAgICAgICAgICBpZD17c2VhcmNoaW5wdXR1bmlxdWVJZH1cbiAgICAgICAgICAgICAgICAgICAgICAgIHR5cGU9XCJzZWFyY2hcIlxuICAgICAgICAgICAgICAgICAgICAgICAgcGxhY2Vob2xkZXI9e19fKCdQbGVhc2UgZW50ZXIgeW91ciBzZWFyY2ggcXVlcnkuLi4nLCAndm9kaScpfVxuICAgICAgICAgICAgICAgICAgICAgICAgdmFsdWU9e3RoaXMuc3RhdGUuZmlsdGVyfVxuICAgICAgICAgICAgICAgICAgICAgICAgb25DaGFuZ2U9e3RoaXMuaGFuZGxlSW5wdXRGaWx0ZXJDaGFuZ2V9XG4gICAgICAgICAgICAgICAgICAgIC8+XG4gICAgICAgICAgICAgICAgICAgIDxJdGVtTGlzdFxuICAgICAgICAgICAgICAgICAgICAgICAgaXRlbXM9e3Rlcm1MaXN0fVxuICAgICAgICAgICAgICAgICAgICAgICAgbG9hZGluZz17dGhpcy5zdGF0ZS5pbml0aWFsTG9hZGluZ3x8dGhpcy5zdGF0ZS5sb2FkaW5nfHx0aGlzLnN0YXRlLmZpbHRlckxvYWRpbmd9XG4gICAgICAgICAgICAgICAgICAgICAgICBmaWx0ZXJlZD17aXNGaWx0ZXJlZH1cbiAgICAgICAgICAgICAgICAgICAgICAgIGFjdGlvbj17dGhpcy5hZGRUZXJtfVxuICAgICAgICAgICAgICAgICAgICAgICAgaWNvbj17YWRkSWNvbn1cbiAgICAgICAgICAgICAgICAgICAgLz5cbiAgICAgICAgICAgICAgICA8L2Rpdj5cbiAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICApO1xuICAgIH1cbn0iLCJjb25zdCB7IGFwaUZldGNoIH0gPSB3cDtcblxuLyoqXG4gKiBNYWtlcyBhIGdldCByZXF1ZXN0IHRvIHRoZSBQb3N0VHlwZXMgZW5kcG9pbnQuXG4gKlxuICogQHJldHVybnMge1Byb21pc2U8YW55Pn1cbiAqL1xuZXhwb3J0IGNvbnN0IGdldFBvc3RUeXBlcyA9ICgpID0+IHtcbiAgICByZXR1cm4gYXBpRmV0Y2goIHsgcGF0aDogJy93cC92Mi90eXBlcycgfSApO1xufTtcblxuLyoqXG4gKiBNYWtlcyBhIGdldCByZXF1ZXN0IHRvIHRoZSBkZXNpcmVkIHBvc3QgdHlwZSBhbmQgYnVpbGRzIHRoZSBxdWVyeSBzdHJpbmcgYmFzZWQgb24gYW4gb2JqZWN0LlxuICpcbiAqIEBwYXJhbSB7c3RyaW5nfGJvb2xlYW59IHJlc3RCYXNlIC0gcmVzdCBiYXNlIGZvciB0aGUgcXVlcnkuXG4gKiBAcGFyYW0ge29iamVjdH0gYXJnc1xuICogQHJldHVybnMge1Byb21pc2U8YW55Pn1cbiAqL1xuZXhwb3J0IGNvbnN0IGdldFBvc3RzID0gKHsgcmVzdEJhc2UgPSBmYWxzZSwgLi4uYXJncyB9KSA9PiB7XG4gICAgY29uc3QgcXVlcnlTdHJpbmcgPSBPYmplY3Qua2V5cyhhcmdzKS5tYXAoYXJnID0+IGAke2FyZ309JHthcmdzW2FyZ119YCkuam9pbignJicpO1xuXG4gICAgbGV0IHBhdGggPSBgL3dwL3YyLyR7cmVzdEJhc2V9PyR7cXVlcnlTdHJpbmd9Jl9lbWJlZGA7XG4gICAgcmV0dXJuIGFwaUZldGNoKCB7IHBhdGg6IHBhdGggfSApO1xufTtcblxuLyoqXG4gKiBNYWtlcyBhIGdldCByZXF1ZXN0IHRvIHRoZSBQb3N0VHlwZSBUYXhvbm9taWVzIGVuZHBvaW50LlxuICpcbiAqIEByZXR1cm5zIHtQcm9taXNlPGFueT59XG4gKi9cbmV4cG9ydCBjb25zdCBnZXRUYXhvbm9taWVzID0gKHsgLi4uYXJncyB9KSA9PiB7XG4gICAgY29uc3QgcXVlcnlTdHJpbmcgPSBPYmplY3Qua2V5cyhhcmdzKS5tYXAoYXJnID0+IGAke2FyZ309JHthcmdzW2FyZ119YCkuam9pbignJicpO1xuXG4gICAgbGV0IHBhdGggPSBgL3dwL3YyL3RheG9ub21pZXM/JHtxdWVyeVN0cmluZ30mX2VtYmVkYDtcbiAgICByZXR1cm4gYXBpRmV0Y2goIHsgcGF0aDogcGF0aCB9ICk7XG59O1xuXG4vKipcbiAqIE1ha2VzIGEgZ2V0IHJlcXVlc3QgdG8gdGhlIGRlc2lyZWQgcG9zdCB0eXBlIGFuZCBidWlsZHMgdGhlIHF1ZXJ5IHN0cmluZyBiYXNlZCBvbiBhbiBvYmplY3QuXG4gKlxuICogQHBhcmFtIHtzdHJpbmd8Ym9vbGVhbn0gcmVzdEJhc2UgLSByZXN0IGJhc2UgZm9yIHRoZSBxdWVyeS5cbiAqIEBwYXJhbSB7b2JqZWN0fSBhcmdzXG4gKiBAcmV0dXJucyB7UHJvbWlzZTxhbnk+fVxuICovXG5leHBvcnQgY29uc3QgZ2V0VGVybXMgPSAoeyByZXN0QmFzZSA9IGZhbHNlLCAuLi5hcmdzIH0pID0+IHtcbiAgICBjb25zdCBxdWVyeVN0cmluZyA9IE9iamVjdC5rZXlzKGFyZ3MpLm1hcChhcmcgPT4gYCR7YXJnfT0ke2FyZ3NbYXJnXX1gKS5qb2luKCcmJyk7XG5cbiAgICBsZXQgcGF0aCA9IGAvd3AvdjIvJHtyZXN0QmFzZX0/JHtxdWVyeVN0cmluZ30mX2VtYmVkYDtcbiAgICByZXR1cm4gYXBpRmV0Y2goIHsgcGF0aDogcGF0aCB9ICk7XG59OyIsIi8qKlxuICogUmV0dXJucyBhIHVuaXF1ZSBhcnJheSBvZiBvYmplY3RzIGJhc2VkIG9uIGEgZGVzaXJlZCBrZXkuXG4gKiBAcGFyYW0ge2FycmF5fSBhcnIgLSBhcnJheSBvZiBvYmplY3RzLlxuICogQHBhcmFtIHtzdHJpbmd8aW50fSBrZXkgLSBrZXkgdG8gZmlsdGVyIG9iamVjdHMgYnlcbiAqL1xuZXhwb3J0IGNvbnN0IHVuaXF1ZUJ5ID0gKGFyciwga2V5KSA9PiB7XG4gICAgbGV0IGtleXMgPSBbXTtcbiAgICByZXR1cm4gYXJyLmZpbHRlcihpdGVtID0+IHtcbiAgICAgICAgaWYgKGtleXMuaW5kZXhPZihpdGVtW2tleV0pICE9PSAtMSkge1xuICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICB9XG5cbiAgICAgICAgcmV0dXJuIGtleXMucHVzaChpdGVtW2tleV0pO1xuICAgIH0pO1xufTtcblxuLyoqXG4gKiBSZXR1cm5zIGEgdW5pcXVlIGFycmF5IG9mIG9iamVjdHMgYmFzZWQgb24gdGhlIGlkIHByb3BlcnR5LlxuICogQHBhcmFtIHthcnJheX0gYXJyIC0gYXJyYXkgb2Ygb2JqZWN0cyB0byBmaWx0ZXIuXG4gKiBAcmV0dXJucyB7Kn1cbiAqL1xuZXhwb3J0IGNvbnN0IHVuaXF1ZUJ5SWQgPSBhcnIgPT4gdW5pcXVlQnkoYXJyLCAnaWQnKTtcblxuLyoqXG4gKiBEZWJvdW5jZSBhIGZ1bmN0aW9uIGJ5IGxpbWl0aW5nIGhvdyBvZnRlbiBpdCBjYW4gcnVuLlxuICogQHBhcmFtIHtmdW5jdGlvbn0gZnVuYyAtIGNhbGxiYWNrIGZ1bmN0aW9uXG4gKiBAcGFyYW0ge0ludGVnZXJ9IHdhaXQgLSBUaW1lIGluIG1pbGxpc2Vjb25kcyBob3cgbG9uZyBpdCBzaG91bGQgd2FpdC5cbiAqIEByZXR1cm5zIHtGdW5jdGlvbn1cbiAqL1xuZXhwb3J0IGNvbnN0IGRlYm91bmNlID0gKGZ1bmMsIHdhaXQpID0+IHtcbiAgICBsZXQgdGltZW91dCA9IG51bGw7XG5cbiAgICByZXR1cm4gZnVuY3Rpb24gKCkge1xuICAgICAgICBjb25zdCBjb250ZXh0ID0gdGhpcztcbiAgICAgICAgY29uc3QgYXJncyA9IGFyZ3VtZW50cztcblxuICAgICAgICBjb25zdCBsYXRlciA9ICgpID0+IHtcbiAgICAgICAgICAgIGZ1bmMuYXBwbHkoY29udGV4dCwgYXJncyk7XG4gICAgICAgIH07XG5cbiAgICAgICAgY2xlYXJUaW1lb3V0KHRpbWVvdXQpO1xuICAgICAgICB0aW1lb3V0ID0gc2V0VGltZW91dChsYXRlciwgd2FpdCk7XG4gICAgfVxufTsiXX0=
