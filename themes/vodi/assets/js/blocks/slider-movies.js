(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
"use strict";

var _ShortcodeAtts = require("../components/ShortcodeAtts");

var _DesignOptions = require("../components/DesignOptions");

var _Repeater = require("../components/Repeater");

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; var ownKeys = Object.keys(source); if (typeof Object.getOwnPropertySymbols === 'function') { ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function (sym) { return Object.getOwnPropertyDescriptor(source, sym).enumerable; })); } ownKeys.forEach(function (key) { _defineProperty(target, key, source[key]); }); } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

var __ = wp.i18n.__;
var registerBlockType = wp.blocks.registerBlockType;
var InspectorControls = wp.editor.InspectorControls;
var Fragment = wp.element.Fragment;
var _wp$components = wp.components,
    ServerSideRender = _wp$components.ServerSideRender,
    Disabled = _wp$components.Disabled,
    PanelBody = _wp$components.PanelBody,
    TextControl = _wp$components.TextControl,
    SelectControl = _wp$components.SelectControl;
registerBlockType('vodi/slider-movies', {
  title: __('Slider Movies Block', 'vodi'),
  icon: 'format-video',
  category: 'vodi-blocks',
  edit: function edit(props) {
    var attributes = props.attributes,
        setAttributes = props.setAttributes;
    var shortcode_atts = attributes.shortcode_atts,
        design_options = attributes.design_options;

    var onChangeShortcodeAtts = function onChangeShortcodeAtts(newShortcodeAtts) {
      setAttributes({
        shortcode_atts: _objectSpread({}, shortcode_atts, newShortcodeAtts)
      });
    };

    var onChangeDesignOptions = function onChangeDesignOptions(newDesignOptions) {
      setAttributes({
        design_options: _objectSpread({}, design_options, newDesignOptions)
      });
    };

    return wp.element.createElement(Fragment, null, wp.element.createElement(InspectorControls, null, wp.element.createElement(PanelBody, {
      title: __('Slider Movies', 'vodi'),
      initialOpen: true
    }, wp.element.createElement(_ShortcodeAtts.ShortcodeAtts, {
      postType: "movie",
      catTaxonomy: "movie_genre",
      attributes: _objectSpread({}, shortcode_atts),
      updateShortcodeAtts: onChangeShortcodeAtts
    })), wp.element.createElement(PanelBody, {
      title: __('Design Options', 'vodi'),
      initialOpen: false
    }, wp.element.createElement(_DesignOptions.DesignOptions, {
      attributes: _objectSpread({}, design_options),
      updateDesignOptions: onChangeDesignOptions
    }))), wp.element.createElement(Disabled, null, wp.element.createElement(ServerSideRender, {
      block: "vodi/slider-movies",
      attributes: attributes
    })));
  },
  save: function save() {
    // Rendering in PHP
    return null;
  }
});

},{"../components/DesignOptions":2,"../components/Repeater":6,"../components/ShortcodeAtts":7}],2:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.DesignOptions = void 0;

function _typeof(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

var __ = wp.i18n.__;
var Component = wp.element.Component;
var RangeControl = wp.components.RangeControl;
/**
 * DesignOptions Component
 */

var DesignOptions =
/*#__PURE__*/
function (_Component) {
  _inherits(DesignOptions, _Component);

  /**
   * Constructor for DesignOptions Component.
   * Sets up state, and creates bindings for functions.
   * @param object props - current component properties.
   */
  function DesignOptions(props) {
    var _this;

    _classCallCheck(this, DesignOptions);

    _this = _possibleConstructorReturn(this, _getPrototypeOf(DesignOptions).apply(this, arguments));
    _this.props = props;
    _this.onChangePaddingTop = _this.onChangePaddingTop.bind(_assertThisInitialized(_assertThisInitialized(_this)));
    _this.onChangePaddingBottom = _this.onChangePaddingBottom.bind(_assertThisInitialized(_assertThisInitialized(_this)));
    _this.onChangePaddingLeft = _this.onChangePaddingLeft.bind(_assertThisInitialized(_assertThisInitialized(_this)));
    _this.onChangePaddingRight = _this.onChangePaddingRight.bind(_assertThisInitialized(_assertThisInitialized(_this)));
    _this.onChangeMarginTop = _this.onChangeMarginTop.bind(_assertThisInitialized(_assertThisInitialized(_this)));
    _this.onChangeMarginBottom = _this.onChangeMarginBottom.bind(_assertThisInitialized(_assertThisInitialized(_this)));
    return _this;
  }

  _createClass(DesignOptions, [{
    key: "onChangePaddingTop",
    value: function onChangePaddingTop(newonChangePaddingTop) {
      this.props.updateDesignOptions({
        padding_top: newonChangePaddingTop
      });
    }
  }, {
    key: "onChangePaddingBottom",
    value: function onChangePaddingBottom(newonChangePaddingBottom) {
      this.props.updateDesignOptions({
        padding_bottom: newonChangePaddingBottom
      });
    }
  }, {
    key: "onChangePaddingLeft",
    value: function onChangePaddingLeft(newonChangePaddingLeft) {
      this.props.updateDesignOptions({
        padding_left: newonChangePaddingLeft
      });
    }
  }, {
    key: "onChangePaddingRight",
    value: function onChangePaddingRight(newonChangePaddingRight) {
      this.props.updateDesignOptions({
        padding_right: newonChangePaddingRight
      });
    }
  }, {
    key: "onChangeMarginTop",
    value: function onChangeMarginTop(newonChangeMarginTop) {
      this.props.updateDesignOptions({
        margin_top: newonChangeMarginTop
      });
    }
  }, {
    key: "onChangeMarginBottom",
    value: function onChangeMarginBottom(newonChangeMarginBottom) {
      this.props.updateDesignOptions({
        margin_bottom: newonChangeMarginBottom
      });
    }
    /**
     * Renders the DesignOptions component.
     */

  }, {
    key: "render",
    value: function render() {
      var attributes = this.props.attributes;
      var padding_top = attributes.padding_top,
          padding_bottom = attributes.padding_bottom,
          padding_left = attributes.padding_left,
          padding_right = attributes.padding_right,
          margin_top = attributes.margin_top,
          margin_bottom = attributes.margin_bottom;
      return wp.element.createElement("div", null, wp.element.createElement(RangeControl, {
        label: __('Padding Top (px)', 'vodi'),
        value: padding_top,
        onChange: this.onChangePaddingTop,
        min: 0,
        max: 100
      }), wp.element.createElement(RangeControl, {
        label: __('Padding Bottom (px)', 'vodi'),
        value: padding_bottom,
        onChange: this.onChangePaddingBottom,
        min: 0,
        max: 100
      }), wp.element.createElement(RangeControl, {
        label: __('Padding Left (px)', 'vodi'),
        value: padding_left,
        onChange: this.onChangePaddingLeft,
        min: 0,
        max: 100
      }), wp.element.createElement(RangeControl, {
        label: __('Padding Right (px)', 'vodi'),
        value: padding_right,
        onChange: this.onChangePaddingRight,
        min: 0,
        max: 100
      }), wp.element.createElement(RangeControl, {
        label: __('Margin Top (px)', 'vodi'),
        value: margin_top,
        onChange: this.onChangeMarginTop,
        min: -100,
        max: 100
      }), wp.element.createElement(RangeControl, {
        label: __('Margin Bottom (px)', 'vodi'),
        value: margin_bottom,
        onChange: this.onChangeMarginBottom,
        min: -100,
        max: 100
      }));
    }
  }]);

  return DesignOptions;
}(Component);

exports.DesignOptions = DesignOptions;

},{}],3:[function(require,module,exports){
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

},{}],4:[function(require,module,exports){
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

},{"./Item":3}],5:[function(require,module,exports){
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

      if (this.props.selectSingle) {
        this.props.updateSelectedPostIds([post_id]);
      } else {
        this.props.updateSelectedPostIds([].concat(_toConsumableArray(this.props.selectedPostIds), [post_id]));
      }
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
        className: "components-base-control__field--selected"
      }, wp.element.createElement("h2", null, __('Search Post', 'vodi')), wp.element.createElement(_ItemList.ItemList, {
        items: SelectedPostList,
        loading: this.state.initialLoading,
        action: this.removePost,
        icon: removeIcon
      })), wp.element.createElement("div", {
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
        placeholder: __('Please enter your search query...', 'vodi'),
        value: this.state.filter,
        onChange: this.handleInputFilterChange
      }), wp.element.createElement(_ItemList.ItemList, {
        items: postList,
        loading: this.state.initialLoading || this.state.loading || this.state.filterLoading,
        filtered: isFiltered,
        action: this.addPost,
        icon: addIcon
      })));
    }
  }]);

  return PostSelector;
}(Component);

exports.PostSelector = PostSelector;

},{"../utils/api":9,"../utils/useful-funcs":10,"./ItemList":4}],6:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.Repeater = void 0;

function _typeof(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; var ownKeys = Object.keys(source); if (typeof Object.getOwnPropertySymbols === 'function') { ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function (sym) { return Object.getOwnPropertyDescriptor(source, sym).enumerable; })); } ownKeys.forEach(function (key) { _defineProperty(target, key, source[key]); }); } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _nonIterableSpread(); }

function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance"); }

function _iterableToArray(iter) { if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === "[object Arguments]") return Array.from(iter); }

function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

var __ = wp.i18n.__;
var _wp$element = wp.element,
    Component = _wp$element.Component,
    Children = _wp$element.Children;
var _wp$components = wp.components,
    Button = _wp$components.Button,
    Icon = _wp$components.Icon;
/**
 * Repeater Component
 */

var Repeater =
/*#__PURE__*/
function (_Component) {
  _inherits(Repeater, _Component);

  /**
   * Constructor for Repeater Component.
   * Sets up state, and creates bindings for functions.
   * @param object props - current component properties.
   */
  function Repeater(props) {
    var _this;

    _classCallCheck(this, Repeater);

    _this = _possibleConstructorReturn(this, _getPrototypeOf(Repeater).apply(this, arguments));
    _this.props = props;
    _this.state = {
      values: []
    };
    _this.renderAddButton = _this.renderAddButton.bind(_assertThisInitialized(_assertThisInitialized(_this)));
    _this.renderRemoveButton = _this.renderRemoveButton.bind(_assertThisInitialized(_assertThisInitialized(_this)));
    _this.handleAdd = _this.handleAdd.bind(_assertThisInitialized(_assertThisInitialized(_this)));
    _this.handleRemove = _this.handleRemove.bind(_assertThisInitialized(_assertThisInitialized(_this)));
    _this.renderChildrenElements = _this.renderChildrenElements.bind(_assertThisInitialized(_assertThisInitialized(_this)));
    return _this;
  }
  /**
   * Fetches children from parent
   */


  _createClass(Repeater, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      var values = this.props.values;

      if (values) {
        this.setState({
          values: values
        });
      }
    }
  }, {
    key: "renderAddButton",
    value: function renderAddButton() {
      return wp.element.createElement(Button, {
        isDefault: true,
        onClick: this.handleAdd
      }, wp.element.createElement(Icon, {
        icon: "plus"
      }));
    }
  }, {
    key: "renderRemoveButton",
    value: function renderRemoveButton() {
      return wp.element.createElement(Button, {
        isDefault: true,
        onClick: this.handleRemove
      }, wp.element.createElement(Icon, {
        icon: "minus"
      }));
    }
  }, {
    key: "handleAdd",
    value: function handleAdd() {
      var _this$props = this.props,
          defaultValues = _this$props.defaultValues,
          updateValues = _this$props.updateValues;
      var values = this.state.values;
      var current_values = values ? [].concat(_toConsumableArray(values), [_objectSpread({}, defaultValues)]) : [_objectSpread({}, defaultValues)];
      this.setState({
        values: current_values
      });
      updateValues(current_values);
    }
  }, {
    key: "handleRemove",
    value: function handleRemove(index) {
      var updateValues = this.props.updateValues;
      var values = this.state.values;
      var current_values = values.filter(function (value, i) {
        return i != index;
      });
      this.setState({
        values: current_values
      });
      updateValues(current_values);
    }
  }, {
    key: "renderChildrenElements",
    value: function renderChildrenElements(values, children) {
      if (!values) {
        return [];
      }

      var remove_button = this.renderRemoveButton();
      return values.map(function (value, index) {
        var updated_children = Children.map(children, function (child) {
          var child_props = _objectSpread({}, child.props);

          if (values[index][child.props.name]) {
            child_props[child.props.valuekey] = values[index][child.props.name];
          }

          child_props[child.props.trigger_method_name] = function (value) {
            return child.props[child.props.trigger_method_name](value, index);
          };

          return React.cloneElement(child, _objectSpread({}, child_props));
        });
        var updated_remove_button = React.cloneElement(remove_button, {
          key: 'repeater-remove-' + index,
          onClick: function onClick() {
            return remove_button.props['onClick'](index);
          }
        });
        return React.createElement('div', {
          key: 'repeater-child-' + index
        }, [updated_children, updated_remove_button]);
      });
    }
    /**
     * Renders the Repeater component.
     */

  }, {
    key: "render",
    value: function render() {
      var _this$props2 = this.props,
          title = _this$props2.title,
          children = _this$props2.children;
      var values = this.state.values;
      var childrenWithProps = this.renderChildrenElements(values, children);
      return wp.element.createElement("div", null, title, childrenWithProps, this.renderAddButton());
    }
  }]);

  return Repeater;
}(Component);

exports.Repeater = Repeater;

},{}],7:[function(require,module,exports){
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

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

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
    _this.onChangeLimit = _this.onChangeLimit.bind(_assertThisInitialized(_assertThisInitialized(_this)));
    _this.onChangeColumns = _this.onChangeColumns.bind(_assertThisInitialized(_assertThisInitialized(_this)));
    _this.onChangeOrderby = _this.onChangeOrderby.bind(_assertThisInitialized(_assertThisInitialized(_this)));
    _this.onChangeOrder = _this.onChangeOrder.bind(_assertThisInitialized(_assertThisInitialized(_this)));
    _this.onChangeIds = _this.onChangeIds.bind(_assertThisInitialized(_assertThisInitialized(_this)));
    _this.onChangeCategory = _this.onChangeCategory.bind(_assertThisInitialized(_assertThisInitialized(_this)));
    _this.onChangeFeatured = _this.onChangeFeatured.bind(_assertThisInitialized(_assertThisInitialized(_this)));
    _this.onChangeTopRated = _this.onChangeTopRated.bind(_assertThisInitialized(_assertThisInitialized(_this)));
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
        max: 10
      }) : '', !(hideFields && hideFields.includes('orderby')) ? wp.element.createElement(SelectControl, {
        label: __('Orderby', 'vodi'),
        value: orderby,
        options: [{
          label: __('Title', 'vodi'),
          value: 'title'
        }, {
          label: __('Date', 'vodi'),
          value: 'date'
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
      }) : '', !(hideFields && hideFields.includes('category')) ? wp.element.createElement(_TermSelector.TermSelector, {
        postType: postType,
        taxonomy: catTaxonomy,
        selectedTermIds: category ? category.split(',').map(Number) : [],
        updateSelectedTermIds: this.onChangeCategory
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

},{"./PostSelector":5,"./TermSelector":8}],8:[function(require,module,exports){
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
        className: "components-base-control__field--selected"
      }, wp.element.createElement("h2", null, __('Search Term', 'vodi')), wp.element.createElement(_ItemList.ItemList, {
        items: SelectedTermList,
        loading: this.state.initialLoading,
        action: this.removeTerm,
        icon: removeIcon
      })), wp.element.createElement("div", {
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

},{"../utils/api":9,"../utils/useful-funcs":10,"./ItemList":4}],9:[function(require,module,exports){
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

},{}],10:[function(require,module,exports){
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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJzcmMvdGhlbWVzL3ZvZGkvYXNzZXRzL2VzbmV4dC9ibG9ja3Mvc2xpZGVyLW1vdmllcy5qcyIsInNyYy90aGVtZXMvdm9kaS9hc3NldHMvZXNuZXh0L2NvbXBvbmVudHMvRGVzaWduT3B0aW9ucy5qcyIsInNyYy90aGVtZXMvdm9kaS9hc3NldHMvZXNuZXh0L2NvbXBvbmVudHMvSXRlbS5qcyIsInNyYy90aGVtZXMvdm9kaS9hc3NldHMvZXNuZXh0L2NvbXBvbmVudHMvSXRlbUxpc3QuanMiLCJzcmMvdGhlbWVzL3ZvZGkvYXNzZXRzL2VzbmV4dC9jb21wb25lbnRzL1Bvc3RTZWxlY3Rvci5qcyIsInNyYy90aGVtZXMvdm9kaS9hc3NldHMvZXNuZXh0L2NvbXBvbmVudHMvUmVwZWF0ZXIuanMiLCJzcmMvdGhlbWVzL3ZvZGkvYXNzZXRzL2VzbmV4dC9jb21wb25lbnRzL1Nob3J0Y29kZUF0dHMuanMiLCJzcmMvdGhlbWVzL3ZvZGkvYXNzZXRzL2VzbmV4dC9jb21wb25lbnRzL1Rlcm1TZWxlY3Rvci5qcyIsInNyYy90aGVtZXMvdm9kaS9hc3NldHMvZXNuZXh0L3V0aWxzL2FwaS5qcyIsInNyYy90aGVtZXMvdm9kaS9hc3NldHMvZXNuZXh0L3V0aWxzL3VzZWZ1bC1mdW5jcy5qcyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTs7O0FDQUE7O0FBQ0E7O0FBQ0E7Ozs7OztJQUVRLEUsR0FBTyxFQUFFLENBQUMsSSxDQUFWLEU7SUFDQSxpQixHQUFzQixFQUFFLENBQUMsTSxDQUF6QixpQjtJQUNBLGlCLEdBQXNCLEVBQUUsQ0FBQyxNLENBQXpCLGlCO0lBQ0EsUSxHQUFhLEVBQUUsQ0FBQyxPLENBQWhCLFE7cUJBQ3NFLEVBQUUsQ0FBQyxVO0lBQXpFLGdCLGtCQUFBLGdCO0lBQWtCLFEsa0JBQUEsUTtJQUFVLFMsa0JBQUEsUztJQUFXLFcsa0JBQUEsVztJQUFhLGEsa0JBQUEs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QUQsQ0FBWjtBQUNBLElBQUEsT0FBTyxHQUFHLFVBQVUsQ0FBQyxLQUFELEVBQVEsSUFBUixDQUFwQjtBQUNILEdBVkQ7QUFXSCxDQWRNIiwiZmlsZSI6ImdlbmVyYXRlZC5qcyIsInNvdXJjZVJvb3QiOiIiLCJzb3VyY2VzQ29udGVudCI6WyIoZnVuY3Rpb24oKXtmdW5jdGlvbiByKGUsbix0KXtmdW5jdGlvbiBvKGksZil7aWYoIW5baV0pe2lmKCFlW2ldKXt2YXIgYz1cImZ1bmN0aW9uXCI9PXR5cGVvZiByZXF1aXJlJiZyZXF1aXJlO2lmKCFmJiZjKXJldHVybiBjKGksITApO2lmKHUpcmV0dXJuIHUoaSwhMCk7dmFyIGE9bmV3IEVycm9yKFwiQ2Fubm90IGZpbmQgbW9kdWxlICdcIitpK1wiJ1wiKTt0aHJvdyBhLmNvZGU9XCJNT0RVTEVfTk9UX0ZPVU5EXCIsYX12YXIgcD1uW2ldPXtleHBvcnRzOnt9fTtlW2ldWzBdLmNhbGwocC5leHBvcnRzLGZ1bmN0aW9uKHIpe3ZhciBuPWVbaV1bMV1bcl07cmV0dXJuIG8obnx8cil9LHAscC5leHBvcnRzLHIsZSxuLHQpfXJldHVybiBuW2ldLmV4cG9ydHN9Zm9yKHZhciB1PVwiZnVuY3Rpb25cIj09dHlwZW9mIHJlcXVpcmUmJnJlcXVpcmUsaT0wO2k8dC5sZW5ndGg7aSsrKW8odFtpXSk7cmV0dXJuIG99cmV0dXJuIHJ9KSgpIiwiaW1wb3J0IHsgU2hvcnRjb2RlQXR0cyB9IGZyb20gJy4uL2NvbXBvbmVudHMvU2hvcnRjb2RlQXR0cyc7XG5pbXBvcnQgeyBEZXNpZ25PcHRpb25zIH0gZnJvbSAnLi4vY29tcG9uZW50cy9EZXNpZ25PcHRpb25zJztcbmltcG9ydCB7IFJlcGVhdGVyIH0gZnJvbSAnLi4vY29tcG9uZW50cy9SZXBlYXRlcic7XG5cbmNvbnN0IHsgX18gfSA9IHdwLmkxOG47XG5jb25zdCB7IHJlZ2lzdGVyQmxvY2tUeXBlIH0gPSB3cC5ibG9ja3M7XG5jb25zdCB7IEluc3BlY3RvckNvbnRyb2xzIH0gPSB3cC5lZGl0b3I7XG5jb25zdCB7IEZyYWdtZW50IH0gPSB3cC5lbGVtZW50O1xuY29uc3QgeyBTZXJ2ZXJTaWRlUmVuZGVyLCBEaXNhYmxlZCwgUGFuZWxCb2R5LCBUZXh0Q29udHJvbCwgU2VsZWN0Q29udHJvbCB9ID0gd3AuY29tcG9uZW50cztcblxucmVnaXN0ZXJCbG9ja1R5cGUoICd2b2RpL3NsaWRlci1tb3ZpZXMnLCB7XG4gICAgdGl0bGU6IF9fKCdTbGlkZXIgTW92aWVzIEJsb2NrJywgJ3ZvZGknKSxcblxuICAgIGljb246ICdmb3JtYXQtdmlkZW8nLFxuXG4gICAgY2F0ZWdvcnk6ICd2b2RpLWJsb2NrcycsXG5cbiAgICBlZGl0OiAoICggcHJvcHMgKSA9PiB7XG4gICAgICAgIGNvbnN0IHsgYXR0cmlidXRlcywgc2V0QXR0cmlidXRlcyB9ID0gcHJvcHM7XG4gICAgICAgIGNvbnN0IHsgc2hvcnRjb2RlX2F0dHMsIGRlc2lnbl9vcHRpb25zIH0gPSBhdHRyaWJ1dGVzO1xuXG4gICAgICAgIGNvbnN0IG9uQ2hhbmdlU2hvcnRjb2RlQXR0cyA9IG5ld1Nob3J0Y29kZUF0dHMgPT4ge1xuICAgICAgICAgICAgc2V0QXR0cmlidXRlcyggeyBzaG9ydGNvZGVfYXR0czogeyAuLi5zaG9ydGNvZGVfYXR0cywgLi4ubmV3U2hvcnRjb2RlQXR0cyB9IH0gKTtcbiAgICAgICAgfTtcblxuICAgICAgICBjb25zdCBvbkNoYW5nZURlc2lnbk9wdGlvbnMgPSBuZXdEZXNpZ25PcHRpb25zID0+IHtcbiAgICAgICAgICAgIHNldEF0dHJpYnV0ZXMoIHsgZGVzaWduX29wdGlvbnM6IHsgLi4uZGVzaWduX29wdGlvbnMsIC4uLm5ld0Rlc2lnbk9wdGlvbnMgfSB9ICk7XG4gICAgICAgIH07XG5cbiAgICAgICAgcmV0dXJuIChcbiAgICAgICAgICAgIDxGcmFnbWVudD5cbiAgICAgICAgICAgICAgICA8SW5zcGVjdG9yQ29udHJvbHM+XG4gICAgICAgICAgICAgICAgICAgIDxQYW5lbEJvZHlcbiAgICAgICAgICAgICAgICAgICAgICAgIHRpdGxlPXtfXygnU2xpZGVyIE1vdmllcycsICd2b2RpJyl9XG4gICAgICAgICAgICAgICAgICAgICAgICBpbml0aWFsT3Blbj17IHRydWUgfVxuICAgICAgICAgICAgICAgICAgICA+XG4gICAgICAgICAgICAgICAgICAgICAgICA8U2hvcnRjb2RlQXR0c1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHBvc3RUeXBlID0gJ21vdmllJ1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGNhdFRheG9ub215ID0gJ21vdmllX2dlbnJlJ1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGF0dHJpYnV0ZXMgPSB7IHsgLi4uc2hvcnRjb2RlX2F0dHMgfSB9XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdXBkYXRlU2hvcnRjb2RlQXR0cyA9IHsgb25DaGFuZ2VTaG9ydGNvZGVBdHRzIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIC8+XG4gICAgICAgICAgICAgICAgICAgIDwvUGFuZWxCb2R5PlxuICAgICAgICAgICAgICAgICAgICA8UGFuZWxCb2R5XG4gICAgICAgICAgICAgICAgICAgICAgICB0aXRsZT17X18oJ0Rlc2lnbiBPcHRpb25zJywgJ3ZvZGknKX1cbiAgICAgICAgICAgICAgICAgICAgICAgIGluaXRpYWxPcGVuPXsgZmFsc2UgfVxuICAgICAgICAgICAgICAgICAgICA+XG4gICAgICAgICAgICAgICAgICAgICAgICA8RGVzaWduT3B0aW9uc1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGF0dHJpYnV0ZXMgPSB7IHsgLi4uZGVzaWduX29wdGlvbnMgfSB9XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdXBkYXRlRGVzaWduT3B0aW9ucyA9IHsgb25DaGFuZ2VEZXNpZ25PcHRpb25zIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIC8+XG4gICAgICAgICAgICAgICAgICAgIDwvUGFuZWxCb2R5PlxuICAgICAgICAgICAgICAgIDwvSW5zcGVjdG9yQ29udHJvbHM+XG4gICAgICAgICAgICAgICAgPERpc2FibGVkPlxuICAgICAgICAgICAgICAgICAgICA8U2VydmVyU2lkZVJlbmRlclxuICAgICAgICAgICAgICAgICAgICAgICAgYmxvY2s9XCJ2b2RpL3NsaWRlci1tb3ZpZXNcIlxuICAgICAgICAgICAgICAgICAgICAgICAgYXR0cmlidXRlcz17IGF0dHJpYnV0ZXMgfVxuICAgICAgICAgICAgICAgICAgICAvPlxuICAgICAgICAgICAgICAgIDwvRGlzYWJsZWQ+XG4gICAgICAgICAgICA8L0ZyYWdtZW50PlxuICAgICAgICApO1xuICAgIH0gKSxcblxuICAgIHNhdmUoKSB7XG4gICAgICAgIC8vIFJlbmRlcmluZyBpbiBQSFBcbiAgICAgICAgcmV0dXJuIG51bGw7XG4gICAgfSxcbn0gKTsiLCJjb25zdCB7IF9fIH0gPSB3cC5pMThuO1xuY29uc3QgeyBDb21wb25lbnQgfSA9IHdwLmVsZW1lbnQ7XG5jb25zdCB7IFJhbmdlQ29udHJvbCB9ID0gd3AuY29tcG9uZW50cztcblxuLyoqXG4gKiBEZXNpZ25PcHRpb25zIENvbXBvbmVudFxuICovXG5leHBvcnQgY2xhc3MgRGVzaWduT3B0aW9ucyBleHRlbmRzIENvbXBvbmVudCB7XG4gICAgLyoqXG4gICAgICogQ29uc3RydWN0b3IgZm9yIERlc2lnbk9wdGlvbnMgQ29tcG9uZW50LlxuICAgICAqIFNldHMgdXAgc3RhdGUsIGFuZCBjcmVhdGVzIGJpbmRpbmdzIGZvciBmdW5jdGlvbnMuXG4gICAgICogQHBhcmFtIG9iamVjdCBwcm9wcyAtIGN1cnJlbnQgY29tcG9uZW50IHByb3BlcnRpZXMuXG4gICAgICovXG4gICAgY29uc3RydWN0b3IocHJvcHMpIHtcbiAgICAgICAgc3VwZXIoLi4uYXJndW1lbnRzKTtcbiAgICAgICAgdGhpcy5wcm9wcyA9IHByb3BzO1xuXG4gICAgICAgIHRoaXMub25DaGFuZ2VQYWRkaW5nVG9wID0gdGhpcy5vbkNoYW5nZVBhZGRpbmdUb3AuYmluZCh0aGlzKTtcbiAgICAgICAgdGhpcy5vbkNoYW5nZVBhZGRpbmdCb3R0b20gPSB0aGlzLm9uQ2hhbmdlUGFkZGluZ0JvdHRvbS5iaW5kKHRoaXMpO1xuICAgICAgICB0aGlzLm9uQ2hhbmdlUGFkZGluZ0xlZnQgPSB0aGlzLm9uQ2hhbmdlUGFkZGluZ0xlZnQuYmluZCh0aGlzKTtcbiAgICAgICAgdGhpcy5vbkNoYW5nZVBhZGRpbmdSaWdodCA9IHRoaXMub25DaGFuZ2VQYWRkaW5nUmlnaHQuYmluZCh0aGlzKTtcbiAgICAgICAgdGhpcy5vbkNoYW5nZU1hcmdpblRvcCA9IHRoaXMub25DaGFuZ2VNYXJnaW5Ub3AuYmluZCh0aGlzKTtcbiAgICAgICAgdGhpcy5vbkNoYW5nZU1hcmdpbkJvdHRvbSA9IHRoaXMub25DaGFuZ2VNYXJnaW5Cb3R0b20uYmluZCh0aGlzKTtcbiAgICB9XG5cbiAgICBvbkNoYW5nZVBhZGRpbmdUb3AoIG5ld29uQ2hhbmdlUGFkZGluZ1RvcCApIHtcbiAgICAgICAgdGhpcy5wcm9wcy51cGRhdGVEZXNpZ25PcHRpb25zKHtcbiAgICAgICAgICAgIHBhZGRpbmdfdG9wOiBuZXdvbkNoYW5nZVBhZGRpbmdUb3BcbiAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgb25DaGFuZ2VQYWRkaW5nQm90dG9tKCBuZXdvbkNoYW5nZVBhZGRpbmdCb3R0b20gKSB7XG4gICAgICAgIHRoaXMucHJvcHMudXBkYXRlRGVzaWduT3B0aW9ucyh7XG4gICAgICAgICAgICBwYWRkaW5nX2JvdHRvbTogbmV3b25DaGFuZ2VQYWRkaW5nQm90dG9tXG4gICAgICAgIH0pO1xuICAgIH1cblxuICAgIG9uQ2hhbmdlUGFkZGluZ0xlZnQoIG5ld29uQ2hhbmdlUGFkZGluZ0xlZnQgKSB7XG4gICAgICAgIHRoaXMucHJvcHMudXBkYXRlRGVzaWduT3B0aW9ucyh7XG4gICAgICAgICAgICBwYWRkaW5nX2xlZnQ6IG5ld29uQ2hhbmdlUGFkZGluZ0xlZnRcbiAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgb25DaGFuZ2VQYWRkaW5nUmlnaHQoIG5ld29uQ2hhbmdlUGFkZGluZ1JpZ2h0ICkge1xuICAgICAgICB0aGlzLnByb3BzLnVwZGF0ZURlc2lnbk9wdGlvbnMoe1xuICAgICAgICAgICAgcGFkZGluZ19yaWdodDogbmV3b25DaGFuZ2VQYWRkaW5nUmlnaHRcbiAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgb25DaGFuZ2VNYXJnaW5Ub3AoIG5ld29uQ2hhbmdlTWFyZ2luVG9wICkge1xuICAgICAgICB0aGlzLnByb3BzLnVwZGF0ZURlc2lnbk9wdGlvbnMoe1xuICAgICAgICAgICAgbWFyZ2luX3RvcDogbmV3b25DaGFuZ2VNYXJnaW5Ub3BcbiAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgb25DaGFuZ2VNYXJnaW5Cb3R0b20oIG5ld29uQ2hhbmdlTWFyZ2luQm90dG9tICkge1xuICAgICAgICB0aGlzLnByb3BzLnVwZGF0ZURlc2lnbk9wdGlvbnMoe1xuICAgICAgICAgICAgbWFyZ2luX2JvdHRvbTogbmV3b25DaGFuZ2VNYXJnaW5Cb3R0b21cbiAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogUmVuZGVycyB0aGUgRGVzaWduT3B0aW9ucyBjb21wb25lbnQuXG4gICAgICovXG4gICAgcmVuZGVyKCkge1xuICAgICAgICBjb25zdCB7IGF0dHJpYnV0ZXMgfSA9IHRoaXMucHJvcHM7XG4gICAgICAgIGNvbnN0IHsgcGFkZGluZ190b3AsIHBhZGRpbmdfYm90dG9tLCBwYWRkaW5nX2xlZnQsIHBhZGRpbmdfcmlnaHQsIG1hcmdpbl90b3AsIG1hcmdpbl9ib3R0b20gfSA9IGF0dHJpYnV0ZXM7XG5cbiAgICAgICAgcmV0dXJuIChcbiAgICAgICAgICAgIDxkaXY+XG4gICAgICAgICAgICAgICAgPFJhbmdlQ29udHJvbFxuICAgICAgICAgICAgICAgICAgICBsYWJlbD17X18oJ1BhZGRpbmcgVG9wIChweCknLCAndm9kaScpfVxuICAgICAgICAgICAgICAgICAgICB2YWx1ZT17IHBhZGRpbmdfdG9wIH1cbiAgICAgICAgICAgICAgICAgICAgb25DaGFuZ2U9eyB0aGlzLm9uQ2hhbmdlUGFkZGluZ1RvcCB9XG4gICAgICAgICAgICAgICAgICAgIG1pbj17IDAgfVxuICAgICAgICAgICAgICAgICAgICBtYXg9eyAxMDAgfVxuICAgICAgICAgICAgICAgIC8+XG4gICAgICAgICAgICAgICAgPFJhbmdlQ29udHJvbFxuICAgICAgICAgICAgICAgICAgICBsYWJlbD17X18oJ1BhZGRpbmcgQm90dG9tIChweCknLCAndm9kaScpfVxuICAgICAgICAgICAgICAgICAgICB2YWx1ZT17IHBhZGRpbmdfYm90dG9tIH1cbiAgICAgICAgICAgICAgICAgICAgb25DaGFuZ2U9eyB0aGlzLm9uQ2hhbmdlUGFkZGluZ0JvdHRvbSB9XG4gICAgICAgICAgICAgICAgICAgIG1pbj17IDAgfVxuICAgICAgICAgICAgICAgICAgICBtYXg9eyAxMDAgfVxuICAgICAgICAgICAgICAgIC8+XG4gICAgICAgICAgICAgICAgPFJhbmdlQ29udHJvbFxuICAgICAgICAgICAgICAgICAgICBsYWJlbD17X18oJ1BhZGRpbmcgTGVmdCAocHgpJywgJ3ZvZGknKX1cbiAgICAgICAgICAgICAgICAgICAgdmFsdWU9eyBwYWRkaW5nX2xlZnQgfVxuICAgICAgICAgICAgICAgICAgICBvbkNoYW5nZT17IHRoaXMub25DaGFuZ2VQYWRkaW5nTGVmdCB9XG4gICAgICAgICAgICAgICAgICAgIG1pbj17IDAgfVxuICAgICAgICAgICAgICAgICAgICBtYXg9eyAxMDAgfVxuICAgICAgICAgICAgICAgIC8+XG4gICAgICAgICAgICAgICAgPFJhbmdlQ29udHJvbFxuICAgICAgICAgICAgICAgICAgICBsYWJlbD17X18oJ1BhZGRpbmcgUmlnaHQgKHB4KScsICd2b2RpJyl9XG4gICAgICAgICAgICAgICAgICAgIHZhbHVlPXsgcGFkZGluZ19yaWdodCB9XG4gICAgICAgICAgICAgICAgICAgIG9uQ2hhbmdlPXsgdGhpcy5vbkNoYW5nZVBhZGRpbmdSaWdodCB9XG4gICAgICAgICAgICAgICAgICAgIG1pbj17IDAgfVxuICAgICAgICAgICAgICAgICAgICBtYXg9eyAxMDAgfVxuICAgICAgICAgICAgICAgIC8+XG4gICAgICAgICAgICAgICAgPFJhbmdlQ29udHJvbFxuICAgICAgICAgICAgICAgICAgICBsYWJlbD17X18oJ01hcmdpbiBUb3AgKHB4KScsICd2b2RpJyl9XG4gICAgICAgICAgICAgICAgICAgIHZhbHVlPXsgbWFyZ2luX3RvcCB9XG4gICAgICAgICAgICAgICAgICAgIG9uQ2hhbmdlPXsgdGhpcy5vbkNoYW5nZU1hcmdpblRvcCB9XG4gICAgICAgICAgICAgICAgICAgIG1pbj17IC0xMDAgfVxuICAgICAgICAgICAgICAgICAgICBtYXg9eyAxMDAgfVxuICAgICAgICAgICAgICAgIC8+XG4gICAgICAgICAgICAgICAgPFJhbmdlQ29udHJvbFxuICAgICAgICAgICAgICAgICAgICBsYWJlbD17X18oJ01hcmdpbiBCb3R0b20gKHB4KScsICd2b2RpJyl9XG4gICAgICAgICAgICAgICAgICAgIHZhbHVlPXsgbWFyZ2luX2JvdHRvbSB9XG4gICAgICAgICAgICAgICAgICAgIG9uQ2hhbmdlPXsgdGhpcy5vbkNoYW5nZU1hcmdpbkJvdHRvbSB9XG4gICAgICAgICAgICAgICAgICAgIG1pbj17IC0xMDAgfVxuICAgICAgICAgICAgICAgICAgICBtYXg9eyAxMDAgfVxuICAgICAgICAgICAgICAgIC8+XG4gICAgICAgICAgICA8L2Rpdj5cbiAgICAgICAgKTtcbiAgICB9XG59IiwiXG4vKipcbiAqIEl0ZW0gQ29tcG9uZW50LlxuICpcbiAqIEBwYXJhbSB7c3RyaW5nfSBpdGVtVGl0bGUgLSBDdXJyZW50IGl0ZW0gdGl0bGUuXG4gKiBAcGFyYW0ge2Z1bmN0aW9ufSBjbGlja0hhbmRsZXIgLSB0aGlzIGlzIHRoZSBoYW5kbGluZyBmdW5jdGlvbiBmb3IgdGhlIGFkZC9yZW1vdmUgZnVuY3Rpb25cbiAqIEBwYXJhbSB7SW50ZWdlcn0gaXRlbUlkIC0gQ3VycmVudCBpdGVtIElEXG4gKiBAcGFyYW0gaWNvblxuICogQHJldHVybnMgeyp9IEl0ZW0gSFRNTC5cbiAqL1xuZXhwb3J0IGNvbnN0IEl0ZW0gPSAoeyB0aXRsZTogeyByZW5kZXJlZDogaXRlbVRpdGxlIH0gPSB7fSwgbmFtZSwgY2xpY2tIYW5kbGVyLCBpZDogaXRlbUlkLCBpY29uIH0pID0+IChcbiAgICA8YXJ0aWNsZSBjbGFzc05hbWU9XCJpdGVtXCI+XG4gICAgICAgIDxkaXYgY2xhc3NOYW1lPVwiaXRlbS1ib2R5XCI+XG4gICAgICAgICAgICA8aDMgY2xhc3NOYW1lPVwiaXRlbS10aXRsZVwiPntpdGVtVGl0bGV9e25hbWV9PC9oMz5cbiAgICAgICAgPC9kaXY+XG4gICAgICAgIDxidXR0b24gb25DbGljaz17KCkgPT4gY2xpY2tIYW5kbGVyKGl0ZW1JZCl9PntpY29ufTwvYnV0dG9uPlxuICAgIDwvYXJ0aWNsZT5cbik7IiwiaW1wb3J0IHsgSXRlbSB9IGZyb20gJy4vSXRlbSc7XG5cbmNvbnN0IHsgX18gfSA9IHdwLmkxOG47XG5cbi8qKlxuICogSXRlbUxpc3QgQ29tcG9uZW50XG4gKiBAcGFyYW0gb2JqZWN0IHByb3BzIC0gQ29tcG9uZW50IHByb3BzLlxuICogQHJldHVybnMgeyp9XG4gKiBAY29uc3RydWN0b3JcbiAqL1xuZXhwb3J0IGNvbnN0IEl0ZW1MaXN0ID0gcHJvcHMgPT4ge1xuICAgIGNvbnN0IHsgZmlsdGVyZWQgPSBmYWxzZSwgbG9hZGluZyA9IGZhbHNlLCBpdGVtcyA9IFtdLCBhY3Rpb24gPSAoKSA9PiB7fSwgaWNvbiA9IG51bGwgfSA9IHByb3BzO1xuXG4gICAgaWYgKGxvYWRpbmcpIHtcbiAgICAgICAgcmV0dXJuIDxwIGNsYXNzTmFtZT1cImxvYWRpbmctaXRlbXNcIj57X18oJ0xvYWRpbmcgLi4uJywgJ3ZvZGknKX08L3A+O1xuICAgIH1cblxuICAgIGlmIChmaWx0ZXJlZCAmJiBpdGVtcy5sZW5ndGggPCAxKSB7XG4gICAgICAgIHJldHVybiAoXG4gICAgICAgICAgICA8ZGl2IGNsYXNzTmFtZT1cIml0ZW0tbGlzdFwiPlxuICAgICAgICAgICAgICAgIDxwPntfXygnWW91ciBxdWVyeSB5aWVsZGVkIG5vIHJlc3VsdHMsIHBsZWFzZSB0cnkgYWdhaW4uJywgJ3ZvZGknKX08L3A+XG4gICAgICAgICAgICA8L2Rpdj5cbiAgICAgICAgKTtcbiAgICB9XG5cbiAgICBpZiAoICEgaXRlbXMgfHwgaXRlbXMubGVuZ3RoIDwgMSApIHtcbiAgICAgICAgcmV0dXJuIDxwIGNsYXNzTmFtZT1cIm5vLWl0ZW1zXCI+e19fKCdOb3QgZm91bmQuJywgJ3ZvZGknKX08L3A+XG4gICAgfVxuXG4gICAgcmV0dXJuIChcbiAgICAgICAgPGRpdiBjbGFzc05hbWU9XCJpdGVtLWxpc3RcIj5cbiAgICAgICAgICAgIHtpdGVtcy5tYXAoKGl0ZW0pID0+IDxJdGVtIGtleT17aXRlbS5pZH0gey4uLml0ZW19IGNsaWNrSGFuZGxlcj17YWN0aW9ufSBpY29uPXtpY29ufSAvPil9XG4gICAgICAgIDwvZGl2PlxuICAgICk7XG59OyIsImltcG9ydCB7IEl0ZW1MaXN0IH0gZnJvbSAnLi9JdGVtTGlzdCc7XG5pbXBvcnQgKiBhcyBhcGkgZnJvbSAnLi4vdXRpbHMvYXBpJztcbmltcG9ydCB7IHVuaXF1ZUJ5SWQsIGRlYm91bmNlIH0gZnJvbSAnLi4vdXRpbHMvdXNlZnVsLWZ1bmNzJztcblxuY29uc3QgeyBfXyB9ID0gd3AuaTE4bjtcbmNvbnN0IHsgSWNvbiB9ID0gd3AuY29tcG9uZW50cztcbmNvbnN0IHsgQ29tcG9uZW50IH0gPSB3cC5lbGVtZW50O1xuXG4vKipcbiAqIFBvc3RTZWxlY3RvciBDb21wb25lbnRcbiAqL1xuZXhwb3J0IGNsYXNzIFBvc3RTZWxlY3RvciBleHRlbmRzIENvbXBvbmVudCB7XG4gICAgLyoqXG4gICAgICogQ29uc3RydWN0b3IgZm9yIFBvc3RTZWxlY3RvciBDb21wb25lbnQuXG4gICAgICogU2V0cyB1cCBzdGF0ZSwgYW5kIGNyZWF0ZXMgYmluZGluZ3MgZm9yIGZ1bmN0aW9ucy5cbiAgICAgKiBAcGFyYW0gb2JqZWN0IHByb3BzIC0gY3VycmVudCBjb21wb25lbnQgcHJvcGVydGllcy5cbiAgICAgKi9cbiAgICBjb25zdHJ1Y3Rvcihwcm9wcykge1xuICAgICAgICBzdXBlciguLi5hcmd1bWVudHMpO1xuICAgICAgICB0aGlzLnByb3BzID0gcHJvcHM7XG5cbiAgICAgICAgdGhpcy5zdGF0ZSA9IHtcbiAgICAgICAgICAgIHBvc3RzOiBbXSxcbiAgICAgICAgICAgIGxvYWRpbmc6IGZhbHNlLFxuICAgICAgICAgICAgdHlwZTogcHJvcHMucG9zdFR5cGUgfHwgJ3Bvc3QnLFxuICAgICAgICAgICAgdHlwZXM6IFtdLFxuICAgICAgICAgICAgZmlsdGVyOiAnJyxcbiAgICAgICAgICAgIGZpbHRlckxvYWRpbmc6IGZhbHNlLFxuICAgICAgICAgICAgZmlsdGVyUG9zdHM6IFtdLFxuICAgICAgICAgICAgaW5pdGlhbExvYWRpbmc6IGZhbHNlLFxuICAgICAgICB9O1xuXG4gICAgICAgIHRoaXMuYWRkUG9zdCA9IHRoaXMuYWRkUG9zdC5iaW5kKHRoaXMpO1xuICAgICAgICB0aGlzLnJlbW92ZVBvc3QgPSB0aGlzLnJlbW92ZVBvc3QuYmluZCh0aGlzKTtcbiAgICAgICAgdGhpcy5oYW5kbGVJbnB1dEZpbHRlckNoYW5nZSA9IHRoaXMuaGFuZGxlSW5wdXRGaWx0ZXJDaGFuZ2UuYmluZCh0aGlzKTtcbiAgICAgICAgdGhpcy5kb1Bvc3RGaWx0ZXIgPSBkZWJvdW5jZSh0aGlzLmRvUG9zdEZpbHRlci5iaW5kKHRoaXMpLCAzMDApO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFdoZW4gdGhlIGNvbXBvbmVudCBtb3VudHMgaXQgY2FsbHMgdGhpcyBmdW5jdGlvbi5cbiAgICAgKiBGZXRjaGVzIHBvc3RzIHR5cGVzLCBzZWxlY3RlZCBwb3N0cyB0aGVuIG1ha2VzIGZpcnN0IGNhbGwgZm9yIHBvc3RzXG4gICAgICovXG4gICAgY29tcG9uZW50RGlkTW91bnQoKSB7XG4gICAgICAgIHRoaXMuc2V0U3RhdGUoe1xuICAgICAgICAgICAgaW5pdGlhbExvYWRpbmc6IHRydWUsXG4gICAgICAgIH0pO1xuXG4gICAgICAgIGFwaS5nZXRQb3N0VHlwZXMoKVxuICAgICAgICAgICAgLnRoZW4oKCByZXNwb25zZSApID0+IHtcbiAgICAgICAgICAgICAgICB0aGlzLnNldFN0YXRlKHtcbiAgICAgICAgICAgICAgICAgICAgdHlwZXM6IHJlc3BvbnNlXG4gICAgICAgICAgICAgICAgfSwgKCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICB0aGlzLnJldHJpZXZlU2VsZWN0ZWRQb3N0cygpXG4gICAgICAgICAgICAgICAgICAgICAgICAudGhlbigoKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdGhpcy5zZXRTdGF0ZSh7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGluaXRpYWxMb2FkaW5nOiBmYWxzZSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH0pXG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9KTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBHZXRQb3N0cyB3cmFwcGVyLCBidWlsZHMgdGhlIHJlcXVlc3QgYXJndW1lbnQgYmFzZWQgc3RhdGUgYW5kIHBhcmFtZXRlcnMgcGFzc2VkL1xuICAgICAqIEBwYXJhbSB7b2JqZWN0fSBhcmdzIC0gZGVzaXJlZCBhcmd1bWVudHMgKGNhbiBiZSBlbXB0eSkuXG4gICAgICogQHJldHVybnMge1Byb21pc2U8VD59XG4gICAgICovXG4gICAgZ2V0UG9zdHMoYXJncyA9IHt9KSB7XG4gICAgICAgIGNvbnN0IHsgc2VsZWN0ZWRQb3N0SWRzIH0gPSB0aGlzLnByb3BzO1xuXG4gICAgICAgIGNvbnN0IGRlZmF1bHRBcmdzID0ge1xuICAgICAgICAgICAgcGVyX3BhZ2U6IDEwLFxuICAgICAgICAgICAgdHlwZTogdGhpcy5zdGF0ZS50eXBlLFxuICAgICAgICAgICAgc2VhcmNoOiB0aGlzLnN0YXRlLmZpbHRlcixcbiAgICAgICAgfTtcblxuICAgICAgICBjb25zdCByZXF1ZXN0QXJndW1lbnRzID0ge1xuICAgICAgICAgICAgLi4uZGVmYXVsdEFyZ3MsXG4gICAgICAgICAgICAuLi5hcmdzXG4gICAgICAgIH07XG5cbiAgICAgICAgcmVxdWVzdEFyZ3VtZW50cy5yZXN0QmFzZSA9IHRoaXMuc3RhdGUudHlwZXNbdGhpcy5zdGF0ZS50eXBlXS5yZXN0X2Jhc2U7XG5cbiAgICAgICAgcmV0dXJuIGFwaS5nZXRQb3N0cyhyZXF1ZXN0QXJndW1lbnRzKVxuICAgICAgICAgICAgLnRoZW4ocmVzcG9uc2UgPT4ge1xuICAgICAgICAgICAgICAgIGlmIChyZXF1ZXN0QXJndW1lbnRzLnNlYXJjaCkge1xuICAgICAgICAgICAgICAgICAgICB0aGlzLnNldFN0YXRlKHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGZpbHRlclBvc3RzOiByZXNwb25zZS5maWx0ZXIoKHsgaWQgfSkgPT4gc2VsZWN0ZWRQb3N0SWRzLmluZGV4T2YoaWQpID09PSAtMSksXG4gICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiByZXNwb25zZTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICB0aGlzLnNldFN0YXRlKHtcbiAgICAgICAgICAgICAgICAgICAgcG9zdHM6IHVuaXF1ZUJ5SWQoWy4uLnRoaXMuc3RhdGUucG9zdHMsIC4uLnJlc3BvbnNlXSksXG4gICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICAvLyByZXR1cm4gcmVzcG9uc2UgdG8gY29udGludWUgdGhlIGNoYWluXG4gICAgICAgICAgICAgICAgcmV0dXJuIHJlc3BvbnNlO1xuICAgICAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogR2V0cyB0aGUgc2VsZWN0ZWQgcG9zdHMgYnkgaWQgZnJvbSB0aGUgYHBvc3RzYCBzdGF0ZSBvYmplY3QgYW5kIHNvcnRzIHRoZW0gYnkgdGhlaXIgcG9zaXRpb24gaW4gdGhlIHNlbGVjdGVkIGFycmF5LlxuICAgICAqIEByZXR1cm5zIEFycmF5IG9mIG9iamVjdHMuXG4gICAgICovXG4gICAgZ2V0U2VsZWN0ZWRQb3N0cygpIHtcbiAgICAgICAgY29uc3QgeyBzZWxlY3RlZFBvc3RJZHMgfSA9IHRoaXMucHJvcHM7XG4gICAgICAgIHJldHVybiB0aGlzLnN0YXRlLnBvc3RzXG4gICAgICAgICAgICAuZmlsdGVyKCh7IGlkIH0pID0+IHNlbGVjdGVkUG9zdElkcy5pbmRleE9mKGlkKSAhPT0gLTEpXG4gICAgICAgICAgICAuc29ydCgoYSwgYikgPT4ge1xuICAgICAgICAgICAgICAgIGNvbnN0IGFJbmRleCA9IHRoaXMucHJvcHMuc2VsZWN0ZWRQb3N0SWRzLmluZGV4T2YoYS5pZCk7XG4gICAgICAgICAgICAgICAgY29uc3QgYkluZGV4ID0gdGhpcy5wcm9wcy5zZWxlY3RlZFBvc3RJZHMuaW5kZXhPZihiLmlkKTtcblxuICAgICAgICAgICAgICAgIGlmIChhSW5kZXggPiBiSW5kZXgpIHtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIDE7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgaWYgKGFJbmRleCA8IGJJbmRleCkge1xuICAgICAgICAgICAgICAgICAgICByZXR1cm4gLTE7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgcmV0dXJuIDA7XG4gICAgICAgICAgICB9KTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBNYWtlcyB0aGUgbmVjZXNzYXJ5IGFwaSBjYWxscyB0byBmZXRjaCB0aGUgc2VsZWN0ZWQgcG9zdHMgYW5kIHJldHVybnMgYSBwcm9taXNlLlxuICAgICAqIEByZXR1cm5zIHsqfVxuICAgICAqL1xuICAgIHJldHJpZXZlU2VsZWN0ZWRQb3N0cygpIHtcbiAgICAgICAgY29uc3QgeyBwb3N0VHlwZSwgc2VsZWN0ZWRQb3N0SWRzIH0gPSB0aGlzLnByb3BzO1xuICAgICAgICBjb25zdCB7IHR5cGVzIH0gPSB0aGlzLnN0YXRlO1xuXG4gICAgICAgIGlmICggc2VsZWN0ZWRQb3N0SWRzICYmICFzZWxlY3RlZFBvc3RJZHMubGVuZ3RoID4gMCApIHtcbiAgICAgICAgICAgIC8vIHJldHVybiBhIGZha2UgcHJvbWlzZSB0aGF0IGF1dG8gcmVzb2x2ZXMuXG4gICAgICAgICAgICByZXR1cm4gbmV3IFByb21pc2UoKHJlc29sdmUpID0+IHJlc29sdmUoKSk7XG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gdGhpcy5nZXRQb3N0cyh7XG4gICAgICAgICAgICBpbmNsdWRlOiB0aGlzLnByb3BzLnNlbGVjdGVkUG9zdElkcy5qb2luKCcsJyksXG4gICAgICAgICAgICBwZXJfcGFnZTogMTAwLFxuICAgICAgICAgICAgcG9zdFR5cGVcbiAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogQWRkcyBkZXNpcmVkIHBvc3QgaWQgdG8gdGhlIHNlbGVjdGVkUG9zdElkcyBMaXN0XG4gICAgICogQHBhcmFtIHtJbnRlZ2VyfSBwb3N0X2lkXG4gICAgICovXG4gICAgYWRkUG9zdChwb3N0X2lkKSB7XG4gICAgICAgIGlmICh0aGlzLnN0YXRlLmZpbHRlcikge1xuICAgICAgICAgICAgY29uc3QgcG9zdCA9IHRoaXMuc3RhdGUuZmlsdGVyUG9zdHMuZmlsdGVyKHAgPT4gcC5pZCA9PT0gcG9zdF9pZCk7XG4gICAgICAgICAgICBjb25zdCBwb3N0cyA9IHVuaXF1ZUJ5SWQoW1xuICAgICAgICAgICAgICAgIC4uLnRoaXMuc3RhdGUucG9zdHMsXG4gICAgICAgICAgICAgICAgLi4ucG9zdFxuICAgICAgICAgICAgXSk7XG5cbiAgICAgICAgICAgIHRoaXMuc2V0U3RhdGUoe1xuICAgICAgICAgICAgICAgIHBvc3RzXG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmKCB0aGlzLnByb3BzLnNlbGVjdFNpbmdsZSApIHtcbiAgICAgICAgICAgIHRoaXMucHJvcHMudXBkYXRlU2VsZWN0ZWRQb3N0SWRzKFtwb3N0X2lkXSk7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICB0aGlzLnByb3BzLnVwZGF0ZVNlbGVjdGVkUG9zdElkcyhbXG4gICAgICAgICAgICAgICAgLi4udGhpcy5wcm9wcy5zZWxlY3RlZFBvc3RJZHMsXG4gICAgICAgICAgICAgICAgcG9zdF9pZFxuICAgICAgICAgICAgXSk7XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBSZW1vdmVzIGRlc2lyZWQgcG9zdCBpZCB0byB0aGUgc2VsZWN0ZWRQb3N0SWRzIExpc3RcbiAgICAgKiBAcGFyYW0ge0ludGVnZXJ9IHBvc3RfaWRcbiAgICAgKi9cbiAgICByZW1vdmVQb3N0KHBvc3RfaWQpIHtcbiAgICAgICAgdGhpcy5wcm9wcy51cGRhdGVTZWxlY3RlZFBvc3RJZHMoW1xuICAgICAgICAgICAgLi4udGhpcy5wcm9wcy5zZWxlY3RlZFBvc3RJZHNcbiAgICAgICAgXS5maWx0ZXIoaWQgPT4gaWQgIT09IHBvc3RfaWQpKTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBIYW5kbGVzIHRoZSBzZWFyY2ggYm94IGlucHV0IHZhbHVlXG4gICAgICogQHBhcmFtIHN0cmluZyB0eXBlIC0gY29tZXMgZnJvbSB0aGUgZXZlbnQgb2JqZWN0IHRhcmdldC5cbiAgICAgKi9cbiAgICBoYW5kbGVJbnB1dEZpbHRlckNoYW5nZSh7IHRhcmdldDogeyB2YWx1ZTpmaWx0ZXIgPSAnJyB9ID0ge30gfSA9IHt9KSB7XG4gICAgICAgIHRoaXMuc2V0U3RhdGUoe1xuICAgICAgICAgICAgZmlsdGVyXG4gICAgICAgIH0sICgpID0+IHtcbiAgICAgICAgICAgIGlmICghZmlsdGVyKSB7XG4gICAgICAgICAgICAgICAgLy8gcmVtb3ZlIGZpbHRlcmVkIHBvc3RzXG4gICAgICAgICAgICAgICAgcmV0dXJuIHRoaXMuc2V0U3RhdGUoeyBmaWx0ZXJlZFBvc3RzOiBbXSwgZmlsdGVyaW5nOiBmYWxzZSB9KTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgdGhpcy5kb1Bvc3RGaWx0ZXIoKTtcbiAgICAgICAgfSlcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBBY3R1YWwgYXBpIGNhbGwgZm9yIHNlYXJjaGluZyBmb3IgcXVlcnksIHRoaXMgZnVuY3Rpb24gaXMgZGVib3VuY2VkIGluIGNvbnN0cnVjdG9yLlxuICAgICAqL1xuICAgIGRvUG9zdEZpbHRlcigpIHtcbiAgICAgICAgY29uc3QgeyBmaWx0ZXIgPSAnJyB9ID0gdGhpcy5zdGF0ZTtcblxuICAgICAgICBpZiAoIWZpbHRlcikge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgdGhpcy5zZXRTdGF0ZSh7XG4gICAgICAgICAgICBmaWx0ZXJpbmc6IHRydWUsXG4gICAgICAgICAgICBmaWx0ZXJMb2FkaW5nOiB0cnVlXG4gICAgICAgIH0pO1xuXG4gICAgICAgIHRoaXMuZ2V0UG9zdHMoKVxuICAgICAgICAgICAgLnRoZW4oKCkgPT4ge1xuICAgICAgICAgICAgICAgIHRoaXMuc2V0U3RhdGUoe1xuICAgICAgICAgICAgICAgICAgICBmaWx0ZXJMb2FkaW5nOiBmYWxzZVxuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogUmVuZGVycyB0aGUgUG9zdFNlbGVjdG9yIGNvbXBvbmVudC5cbiAgICAgKi9cbiAgICByZW5kZXIoKSB7XG4gICAgICAgIGNvbnN0IGlzRmlsdGVyZWQgPSB0aGlzLnN0YXRlLmZpbHRlcmluZztcbiAgICAgICAgY29uc3QgcG9zdExpc3QgPSBpc0ZpbHRlcmVkICYmICF0aGlzLnN0YXRlLmZpbHRlckxvYWRpbmcgPyB0aGlzLnN0YXRlLmZpbHRlclBvc3RzIDogW107XG4gICAgICAgIGNvbnN0IFNlbGVjdGVkUG9zdExpc3QgID0gdGhpcy5nZXRTZWxlY3RlZFBvc3RzKCk7XG5cbiAgICAgICAgY29uc3QgYWRkSWNvbiA9IDxJY29uIGljb249XCJwbHVzXCIgLz47XG4gICAgICAgIGNvbnN0IHJlbW92ZUljb24gPSA8SWNvbiBpY29uPVwibWludXNcIiAvPjtcblxuICAgICAgICByZXR1cm4gKFxuICAgICAgICAgICAgPGRpdiBjbGFzc05hbWU9XCJjb21wb25lbnRzLWJhc2UtY29udHJvbCBjb21wb25lbnRzLXBvc3Qtc2VsZWN0b3JcIj5cbiAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzTmFtZT1cImNvbXBvbmVudHMtYmFzZS1jb250cm9sX19maWVsZC0tc2VsZWN0ZWRcIj5cbiAgICAgICAgICAgICAgICAgICAgPGgyPntfXygnU2VhcmNoIFBvc3QnLCAndm9kaScpfTwvaDI+XG4gICAgICAgICAgICAgICAgICAgIDxJdGVtTGlzdFxuICAgICAgICAgICAgICAgICAgICAgICAgaXRlbXM9e1NlbGVjdGVkUG9zdExpc3R9XG4gICAgICAgICAgICAgICAgICAgICAgICBsb2FkaW5nPXt0aGlzLnN0YXRlLmluaXRpYWxMb2FkaW5nfVxuICAgICAgICAgICAgICAgICAgICAgICAgYWN0aW9uPXt0aGlzLnJlbW92ZVBvc3R9XG4gICAgICAgICAgICAgICAgICAgICAgICBpY29uPXtyZW1vdmVJY29ufVxuICAgICAgICAgICAgICAgICAgICAvPlxuICAgICAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICAgICAgICAgIDxkaXYgY2xhc3NOYW1lPVwiY29tcG9uZW50cy1iYXNlLWNvbnRyb2xfX2ZpZWxkXCI+XG4gICAgICAgICAgICAgICAgICAgIDxsYWJlbCBodG1sRm9yPVwic2VhcmNoaW5wdXRcIiBjbGFzc05hbWU9XCJjb21wb25lbnRzLWJhc2UtY29udHJvbF9fbGFiZWxcIj5cbiAgICAgICAgICAgICAgICAgICAgICAgIDxJY29uIGljb249XCJzZWFyY2hcIiAvPlxuICAgICAgICAgICAgICAgICAgICA8L2xhYmVsPlxuICAgICAgICAgICAgICAgICAgICA8aW5wdXRcbiAgICAgICAgICAgICAgICAgICAgICAgIGNsYXNzTmFtZT1cImNvbXBvbmVudHMtdGV4dC1jb250cm9sX19pbnB1dFwiXG4gICAgICAgICAgICAgICAgICAgICAgICBpZD1cInNlYXJjaGlucHV0XCJcbiAgICAgICAgICAgICAgICAgICAgICAgIHR5cGU9XCJzZWFyY2hcIlxuICAgICAgICAgICAgICAgICAgICAgICAgcGxhY2Vob2xkZXI9e19fKCdQbGVhc2UgZW50ZXIgeW91ciBzZWFyY2ggcXVlcnkuLi4nLCAndm9kaScpfVxuICAgICAgICAgICAgICAgICAgICAgICAgdmFsdWU9e3RoaXMuc3RhdGUuZmlsdGVyfVxuICAgICAgICAgICAgICAgICAgICAgICAgb25DaGFuZ2U9e3RoaXMuaGFuZGxlSW5wdXRGaWx0ZXJDaGFuZ2V9XG4gICAgICAgICAgICAgICAgICAgIC8+XG4gICAgICAgICAgICAgICAgICAgIDxJdGVtTGlzdFxuICAgICAgICAgICAgICAgICAgICAgICAgaXRlbXM9e3Bvc3RMaXN0fVxuICAgICAgICAgICAgICAgICAgICAgICAgbG9hZGluZz17dGhpcy5zdGF0ZS5pbml0aWFsTG9hZGluZ3x8dGhpcy5zdGF0ZS5sb2FkaW5nfHx0aGlzLnN0YXRlLmZpbHRlckxvYWRpbmd9XG4gICAgICAgICAgICAgICAgICAgICAgICBmaWx0ZXJlZD17aXNGaWx0ZXJlZH1cbiAgICAgICAgICAgICAgICAgICAgICAgIGFjdGlvbj17dGhpcy5hZGRQb3N0fVxuICAgICAgICAgICAgICAgICAgICAgICAgaWNvbj17YWRkSWNvbn1cbiAgICAgICAgICAgICAgICAgICAgLz5cbiAgICAgICAgICAgICAgICA8L2Rpdj5cbiAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICApO1xuICAgIH1cbn0iLCJjb25zdCB7IF9fIH0gPSB3cC5pMThuO1xuY29uc3QgeyBDb21wb25lbnQsIENoaWxkcmVuIH0gPSB3cC5lbGVtZW50O1xuY29uc3QgeyBCdXR0b24sIEljb24gfSA9IHdwLmNvbXBvbmVudHM7XG5cbi8qKlxuICogUmVwZWF0ZXIgQ29tcG9uZW50XG4gKi9cbmV4cG9ydCBjbGFzcyBSZXBlYXRlciBleHRlbmRzIENvbXBvbmVudCB7XG4gICAgLyoqXG4gICAgICogQ29uc3RydWN0b3IgZm9yIFJlcGVhdGVyIENvbXBvbmVudC5cbiAgICAgKiBTZXRzIHVwIHN0YXRlLCBhbmQgY3JlYXRlcyBiaW5kaW5ncyBmb3IgZnVuY3Rpb25zLlxuICAgICAqIEBwYXJhbSBvYmplY3QgcHJvcHMgLSBjdXJyZW50IGNvbXBvbmVudCBwcm9wZXJ0aWVzLlxuICAgICAqL1xuICAgIGNvbnN0cnVjdG9yKHByb3BzKSB7XG4gICAgICAgIHN1cGVyKC4uLmFyZ3VtZW50cyk7XG4gICAgICAgIHRoaXMucHJvcHMgPSBwcm9wcztcblxuICAgICAgICB0aGlzLnN0YXRlID0ge1xuICAgICAgICAgICAgdmFsdWVzOiBbXSxcbiAgICAgICAgfTtcblxuICAgICAgICB0aGlzLnJlbmRlckFkZEJ1dHRvbiA9IHRoaXMucmVuZGVyQWRkQnV0dG9uLmJpbmQodGhpcyk7XG4gICAgICAgIHRoaXMucmVuZGVyUmVtb3ZlQnV0dG9uID0gdGhpcy5yZW5kZXJSZW1vdmVCdXR0b24uYmluZCh0aGlzKTtcbiAgICAgICAgdGhpcy5oYW5kbGVBZGQgPSB0aGlzLmhhbmRsZUFkZC5iaW5kKHRoaXMpO1xuICAgICAgICB0aGlzLmhhbmRsZVJlbW92ZSA9IHRoaXMuaGFuZGxlUmVtb3ZlLmJpbmQodGhpcyk7XG4gICAgICAgIHRoaXMucmVuZGVyQ2hpbGRyZW5FbGVtZW50cyA9IHRoaXMucmVuZGVyQ2hpbGRyZW5FbGVtZW50cy5iaW5kKHRoaXMpO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEZldGNoZXMgY2hpbGRyZW4gZnJvbSBwYXJlbnRcbiAgICAgKi9cbiAgICBjb21wb25lbnREaWRNb3VudCgpIHtcbiAgICAgICAgY29uc3QgeyB2YWx1ZXMgfSA9IHRoaXMucHJvcHM7XG4gICAgICAgIGlmKCB2YWx1ZXMgKSB7XG4gICAgICAgICAgICB0aGlzLnNldFN0YXRlKHtcbiAgICAgICAgICAgICAgICB2YWx1ZXM6IHZhbHVlcyxcbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9XG4gICAgfVxuXG4gICAgcmVuZGVyQWRkQnV0dG9uKCkge1xuICAgICAgICByZXR1cm4oXG4gICAgICAgICAgICA8QnV0dG9uIGlzRGVmYXVsdCBvbkNsaWNrPXt0aGlzLmhhbmRsZUFkZH0+XG4gICAgICAgICAgICAgICAgPEljb24gaWNvbj1cInBsdXNcIiAvPlxuICAgICAgICAgICAgPC9CdXR0b24+XG4gICAgICAgICk7XG4gICAgfVxuXG4gICAgcmVuZGVyUmVtb3ZlQnV0dG9uKCkge1xuICAgICAgICByZXR1cm4oXG4gICAgICAgICAgICA8QnV0dG9uIGlzRGVmYXVsdCBvbkNsaWNrPXt0aGlzLmhhbmRsZVJlbW92ZX0+XG4gICAgICAgICAgICAgICAgPEljb24gaWNvbj1cIm1pbnVzXCIgLz5cbiAgICAgICAgICAgIDwvQnV0dG9uPlxuICAgICAgICApO1xuICAgIH1cblxuICAgIGhhbmRsZUFkZCgpIHtcbiAgICAgICAgY29uc3QgeyBkZWZhdWx0VmFsdWVzLCB1cGRhdGVWYWx1ZXMgfSA9IHRoaXMucHJvcHM7XG4gICAgICAgIGNvbnN0IHsgdmFsdWVzIH0gPSB0aGlzLnN0YXRlO1xuICAgICAgICBjb25zdCBjdXJyZW50X3ZhbHVlcyA9IHZhbHVlcyA/IFsgLi4udmFsdWVzLCB7IC4uLmRlZmF1bHRWYWx1ZXMgfSBdIDogWyB7IC4uLmRlZmF1bHRWYWx1ZXMgfSBdO1xuICAgICAgICB0aGlzLnNldFN0YXRlKHtcbiAgICAgICAgICAgIHZhbHVlczogY3VycmVudF92YWx1ZXMsXG4gICAgICAgIH0pO1xuICAgICAgICB1cGRhdGVWYWx1ZXMoIGN1cnJlbnRfdmFsdWVzICk7XG4gICAgfVxuXG4gICAgaGFuZGxlUmVtb3ZlKCBpbmRleCApIHtcbiAgICAgICAgY29uc3QgeyB1cGRhdGVWYWx1ZXMgfSA9IHRoaXMucHJvcHM7XG4gICAgICAgIGNvbnN0IHsgdmFsdWVzIH0gPSB0aGlzLnN0YXRlO1xuICAgICAgICBjb25zdCBjdXJyZW50X3ZhbHVlcyA9IHZhbHVlcy5maWx0ZXIoICggdmFsdWUsIGkgKSA9PiBpICE9IGluZGV4ICk7XG4gICAgICAgIHRoaXMuc2V0U3RhdGUoe1xuICAgICAgICAgICAgdmFsdWVzOiBjdXJyZW50X3ZhbHVlcyxcbiAgICAgICAgfSk7XG4gICAgICAgIHVwZGF0ZVZhbHVlcyggY3VycmVudF92YWx1ZXMgKTtcbiAgICB9XG5cbiAgICByZW5kZXJDaGlsZHJlbkVsZW1lbnRzKCB2YWx1ZXMsIGNoaWxkcmVuICkge1xuICAgICAgICBpZiggISB2YWx1ZXMgKSB7XG4gICAgICAgICAgICByZXR1cm4gW107XG4gICAgICAgIH1cblxuICAgICAgICBjb25zdCByZW1vdmVfYnV0dG9uID0gdGhpcy5yZW5kZXJSZW1vdmVCdXR0b24oKTtcblxuICAgICAgICByZXR1cm4gdmFsdWVzLm1hcCggKCB2YWx1ZSwgaW5kZXggKSA9PiB7XG4gICAgICAgICAgICBjb25zdCB1cGRhdGVkX2NoaWxkcmVuID0gQ2hpbGRyZW4ubWFwKGNoaWxkcmVuLCAoIGNoaWxkICkgPT4ge1xuICAgICAgICAgICAgICAgIGxldCBjaGlsZF9wcm9wcyA9IHsgLi4uY2hpbGQucHJvcHMgfTtcbiAgICAgICAgICAgICAgICBpZiggdmFsdWVzW2luZGV4XVtjaGlsZC5wcm9wcy5uYW1lXSApIHtcbiAgICAgICAgICAgICAgICAgICAgY2hpbGRfcHJvcHNbY2hpbGQucHJvcHMudmFsdWVrZXldID0gdmFsdWVzW2luZGV4XVtjaGlsZC5wcm9wcy5uYW1lXTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgY2hpbGRfcHJvcHNbY2hpbGQucHJvcHMudHJpZ2dlcl9tZXRob2RfbmFtZV0gPSAodmFsdWUpID0+IGNoaWxkLnByb3BzW2NoaWxkLnByb3BzLnRyaWdnZXJfbWV0aG9kX25hbWVdKHZhbHVlLCBpbmRleCk7XG4gICAgICAgICAgICAgICAgcmV0dXJuIFJlYWN0LmNsb25lRWxlbWVudCggY2hpbGQsIHsgLi4uY2hpbGRfcHJvcHMgfSApO1xuICAgICAgICAgICAgfSApO1xuXG4gICAgICAgICAgICBjb25zdCB1cGRhdGVkX3JlbW92ZV9idXR0b24gPSBSZWFjdC5jbG9uZUVsZW1lbnQoIHJlbW92ZV9idXR0b24sIHsga2V5OiAncmVwZWF0ZXItcmVtb3ZlLScraW5kZXgsIG9uQ2xpY2s6ICgpID0+IHJlbW92ZV9idXR0b24ucHJvcHNbJ29uQ2xpY2snXShpbmRleCkgfSApO1xuXG4gICAgICAgICAgICByZXR1cm4gUmVhY3QuY3JlYXRlRWxlbWVudCgnZGl2JywgeyBrZXk6ICdyZXBlYXRlci1jaGlsZC0nK2luZGV4IH0sIFt1cGRhdGVkX2NoaWxkcmVuLCB1cGRhdGVkX3JlbW92ZV9idXR0b25dKTtcbiAgICAgICAgfSApO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFJlbmRlcnMgdGhlIFJlcGVhdGVyIGNvbXBvbmVudC5cbiAgICAgKi9cbiAgICByZW5kZXIoKSB7XG4gICAgICAgIGNvbnN0IHsgdGl0bGUsIGNoaWxkcmVuIH0gPSB0aGlzLnByb3BzO1xuICAgICAgICBjb25zdCB7IHZhbHVlcyB9ID0gdGhpcy5zdGF0ZTtcbiAgICAgICAgXG4gICAgICAgIGNvbnN0IGNoaWxkcmVuV2l0aFByb3BzID0gdGhpcy5yZW5kZXJDaGlsZHJlbkVsZW1lbnRzKCB2YWx1ZXMsIGNoaWxkcmVuICk7XG5cbiAgICAgICAgcmV0dXJuIChcbiAgICAgICAgICAgIDxkaXY+XG4gICAgICAgICAgICAgICAge3RpdGxlfVxuICAgICAgICAgICAgICAgIHtjaGlsZHJlbldpdGhQcm9wc31cbiAgICAgICAgICAgICAgICB7dGhpcy5yZW5kZXJBZGRCdXR0b24oKX1cbiAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICApO1xuICAgIH1cbn0iLCJpbXBvcnQgeyBQb3N0U2VsZWN0b3IgfSBmcm9tICcuL1Bvc3RTZWxlY3Rvcic7XG5pbXBvcnQgeyBUZXJtU2VsZWN0b3IgfSBmcm9tICcuL1Rlcm1TZWxlY3Rvcic7XG5cbmNvbnN0IHsgX18gfSA9IHdwLmkxOG47XG5jb25zdCB7IENvbXBvbmVudCB9ID0gd3AuZWxlbWVudDtcbmNvbnN0IHsgUmFuZ2VDb250cm9sLCBTZWxlY3RDb250cm9sLCBDaGVja2JveENvbnRyb2wgfSA9IHdwLmNvbXBvbmVudHM7XG5cbi8qKlxuICogU2hvcnRjb2RlQXR0cyBDb21wb25lbnRcbiAqL1xuZXhwb3J0IGNsYXNzIFNob3J0Y29kZUF0dHMgZXh0ZW5kcyBDb21wb25lbnQge1xuICAgIC8qKlxuICAgICAqIENvbnN0cnVjdG9yIGZvciBTaG9ydGNvZGVBdHRzIENvbXBvbmVudC5cbiAgICAgKiBTZXRzIHVwIHN0YXRlLCBhbmQgY3JlYXRlcyBiaW5kaW5ncyBmb3IgZnVuY3Rpb25zLlxuICAgICAqIEBwYXJhbSBvYmplY3QgcHJvcHMgLSBjdXJyZW50IGNvbXBvbmVudCBwcm9wZXJ0aWVzLlxuICAgICAqL1xuICAgIGNvbnN0cnVjdG9yKHByb3BzKSB7XG4gICAgICAgIHN1cGVyKC4uLmFyZ3VtZW50cyk7XG4gICAgICAgIHRoaXMucHJvcHMgPSBwcm9wcztcblxuICAgICAgICB0aGlzLm9uQ2hhbmdlTGltaXQgPSB0aGlzLm9uQ2hhbmdlTGltaXQuYmluZCh0aGlzKTtcbiAgICAgICAgdGhpcy5vbkNoYW5nZUNvbHVtbnMgPSB0aGlzLm9uQ2hhbmdlQ29sdW1ucy5iaW5kKHRoaXMpO1xuICAgICAgICB0aGlzLm9uQ2hhbmdlT3JkZXJieSA9IHRoaXMub25DaGFuZ2VPcmRlcmJ5LmJpbmQodGhpcyk7XG4gICAgICAgIHRoaXMub25DaGFuZ2VPcmRlciA9IHRoaXMub25DaGFuZ2VPcmRlci5iaW5kKHRoaXMpO1xuICAgICAgICB0aGlzLm9uQ2hhbmdlSWRzID0gdGhpcy5vbkNoYW5nZUlkcy5iaW5kKHRoaXMpO1xuICAgICAgICB0aGlzLm9uQ2hhbmdlQ2F0ZWdvcnkgPSB0aGlzLm9uQ2hhbmdlQ2F0ZWdvcnkuYmluZCh0aGlzKTtcbiAgICAgICAgdGhpcy5vbkNoYW5nZUZlYXR1cmVkID0gdGhpcy5vbkNoYW5nZUZlYXR1cmVkLmJpbmQodGhpcyk7XG4gICAgICAgIHRoaXMub25DaGFuZ2VUb3BSYXRlZCA9IHRoaXMub25DaGFuZ2VUb3BSYXRlZC5iaW5kKHRoaXMpO1xuICAgIH1cblxuICAgIG9uQ2hhbmdlTGltaXQoIG5ld0xpbWl0ICkge1xuICAgICAgICB0aGlzLnByb3BzLnVwZGF0ZVNob3J0Y29kZUF0dHMoe1xuICAgICAgICAgICAgbGltaXQ6IG5ld0xpbWl0XG4gICAgICAgIH0pO1xuICAgIH1cblxuICAgIG9uQ2hhbmdlQ29sdW1ucyggbmV3Q29sdW1ucyApIHtcbiAgICAgICAgdGhpcy5wcm9wcy51cGRhdGVTaG9ydGNvZGVBdHRzKHtcbiAgICAgICAgICAgIGNvbHVtbnM6IG5ld0NvbHVtbnNcbiAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgb25DaGFuZ2VPcmRlcmJ5KCBuZXdPcmRlcmJ5ICkge1xuICAgICAgICB0aGlzLnByb3BzLnVwZGF0ZVNob3J0Y29kZUF0dHMoe1xuICAgICAgICAgICAgb3JkZXJieTogbmV3T3JkZXJieVxuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICBvbkNoYW5nZU9yZGVyKCBuZXdPcmRlciApIHtcbiAgICAgICAgdGhpcy5wcm9wcy51cGRhdGVTaG9ydGNvZGVBdHRzKHtcbiAgICAgICAgICAgIG9yZGVyOiBuZXdPcmRlclxuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICBvbkNoYW5nZUlkcyggbmV3SWRzICkge1xuICAgICAgICB0aGlzLnByb3BzLnVwZGF0ZVNob3J0Y29kZUF0dHMoe1xuICAgICAgICAgICAgaWRzOiBuZXdJZHMuam9pbignLCcpXG4gICAgICAgIH0pO1xuICAgIH1cblxuICAgIG9uQ2hhbmdlQ2F0ZWdvcnkoIG5ld0NhdGVnb3J5ICkge1xuICAgICAgICB0aGlzLnByb3BzLnVwZGF0ZVNob3J0Y29kZUF0dHMoe1xuICAgICAgICAgICAgY2F0ZWdvcnk6IG5ld0NhdGVnb3J5LmpvaW4oJywnKVxuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICBvbkNoYW5nZUZlYXR1cmVkKCBuZXdGZWF0dXJlZCApIHtcbiAgICAgICAgdGhpcy5wcm9wcy51cGRhdGVTaG9ydGNvZGVBdHRzKHtcbiAgICAgICAgICAgIGZlYXR1cmVkOiBuZXdGZWF0dXJlZFxuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICBvbkNoYW5nZVRvcFJhdGVkKCBuZXdUb3BSYXRlZCApIHtcbiAgICAgICAgdGhpcy5wcm9wcy51cGRhdGVTaG9ydGNvZGVBdHRzKHtcbiAgICAgICAgICAgIHRvcF9yYXRlZDogbmV3VG9wUmF0ZWRcbiAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogUmVuZGVycyB0aGUgU2hvcnRjb2RlQXR0cyBjb21wb25lbnQuXG4gICAgICovXG4gICAgcmVuZGVyKCkge1xuICAgICAgICBjb25zdCB7IGF0dHJpYnV0ZXMsIHBvc3RUeXBlLCBjYXRUYXhvbm9teSwgaGlkZUZpZWxkcyB9ID0gdGhpcy5wcm9wcztcbiAgICAgICAgY29uc3QgeyBsaW1pdCwgY29sdW1ucywgb3JkZXJieSwgb3JkZXIsIGlkcywgY2F0ZWdvcnksIGZlYXR1cmVkLCB0b3BfcmF0ZWQgfSA9IGF0dHJpYnV0ZXM7XG5cbiAgICAgICAgcmV0dXJuIChcbiAgICAgICAgICAgIDxkaXY+XG4gICAgICAgICAgICAgICAgeyAhKCBoaWRlRmllbGRzICYmIGhpZGVGaWVsZHMuaW5jbHVkZXMoJ2xpbWl0JykgKSA/IChcbiAgICAgICAgICAgICAgICA8UmFuZ2VDb250cm9sXG4gICAgICAgICAgICAgICAgICAgIGxhYmVsPXtfXygnTGltaXQnLCAndm9kaScpfVxuICAgICAgICAgICAgICAgICAgICB2YWx1ZT17IGxpbWl0IH1cbiAgICAgICAgICAgICAgICAgICAgb25DaGFuZ2U9eyB0aGlzLm9uQ2hhbmdlTGltaXQgfVxuICAgICAgICAgICAgICAgICAgICBtaW49eyAxIH1cbiAgICAgICAgICAgICAgICAgICAgbWF4PXsgNTAgfVxuICAgICAgICAgICAgICAgIC8+XG4gICAgICAgICAgICAgICAgKSA6ICcnIH1cbiAgICAgICAgICAgICAgICB7ICEoIGhpZGVGaWVsZHMgJiYgaGlkZUZpZWxkcy5pbmNsdWRlcygnY29sdW1ucycpICkgPyAoXG4gICAgICAgICAgICAgICAgPFJhbmdlQ29udHJvbFxuICAgICAgICAgICAgICAgICAgICBsYWJlbD17X18oJ0NvbHVtbnMnLCAndm9kaScpfVxuICAgICAgICAgICAgICAgICAgICB2YWx1ZT17IGNvbHVtbnMgfVxuICAgICAgICAgICAgICAgICAgICBvbkNoYW5nZT17IHRoaXMub25DaGFuZ2VDb2x1bW5zIH1cbiAgICAgICAgICAgICAgICAgICAgbWluPXsgMSB9XG4gICAgICAgICAgICAgICAgICAgIG1heD17IDEwIH1cbiAgICAgICAgICAgICAgICAvPlxuICAgICAgICAgICAgICAgICkgOiAnJyB9XG4gICAgICAgICAgICAgICAgeyAhKCBoaWRlRmllbGRzICYmIGhpZGVGaWVsZHMuaW5jbHVkZXMoJ29yZGVyYnknKSApID8gKFxuICAgICAgICAgICAgICAgIDxTZWxlY3RDb250cm9sXG4gICAgICAgICAgICAgICAgICAgIGxhYmVsPXtfXygnT3JkZXJieScsICd2b2RpJyl9XG4gICAgICAgICAgICAgICAgICAgIHZhbHVlPXsgb3JkZXJieSB9XG4gICAgICAgICAgICAgICAgICAgIG9wdGlvbnM9eyBbXG4gICAgICAgICAgICAgICAgICAgICAgICB7IGxhYmVsOiBfXygnVGl0bGUnLCAndm9kaScpLCB2YWx1ZTogJ3RpdGxlJyB9LFxuICAgICAgICAgICAgICAgICAgICAgICAgeyBsYWJlbDogX18oJ0RhdGUnLCAndm9kaScpLCB2YWx1ZTogJ2RhdGUnIH0sXG4gICAgICAgICAgICAgICAgICAgICAgICB7IGxhYmVsOiBfXygnSUQnLCAndm9kaScpLCB2YWx1ZTogJ2lkJyB9LFxuICAgICAgICAgICAgICAgICAgICAgICAgeyBsYWJlbDogX18oJ1JhbmRvbScsICd2b2RpJyksIHZhbHVlOiAncmFuZCcgfSxcbiAgICAgICAgICAgICAgICAgICAgXSB9XG4gICAgICAgICAgICAgICAgICAgIG9uQ2hhbmdlPXsgdGhpcy5vbkNoYW5nZU9yZGVyYnkgfVxuICAgICAgICAgICAgICAgIC8+XG4gICAgICAgICAgICAgICAgKSA6ICcnIH1cbiAgICAgICAgICAgICAgICB7ICEoIGhpZGVGaWVsZHMgJiYgaGlkZUZpZWxkcy5pbmNsdWRlcygnb3JkZXInKSApID8gKFxuICAgICAgICAgICAgICAgIDxTZWxlY3RDb250cm9sXG4gICAgICAgICAgICAgICAgICAgIGxhYmVsPXtfXygnT3JkZXInLCAndm9kaScpfVxuICAgICAgICAgICAgICAgICAgICB2YWx1ZT17IG9yZGVyIH1cbiAgICAgICAgICAgICAgICAgICAgb3B0aW9ucz17IFtcbiAgICAgICAgICAgICAgICAgICAgICAgIHsgbGFiZWw6IF9fKCdBU0MnLCAndm9kaScpLCB2YWx1ZTogJ0FTQycgfSxcbiAgICAgICAgICAgICAgICAgICAgICAgIHsgbGFiZWw6IF9fKCdERVNDJywgJ3ZvZGknKSwgdmFsdWU6ICdERVNDJyB9LFxuICAgICAgICAgICAgICAgICAgICBdIH1cbiAgICAgICAgICAgICAgICAgICAgb25DaGFuZ2U9eyB0aGlzLm9uQ2hhbmdlT3JkZXIgfVxuICAgICAgICAgICAgICAgIC8+XG4gICAgICAgICAgICAgICAgKSA6ICcnIH1cbiAgICAgICAgICAgICAgICB7ICEoIGhpZGVGaWVsZHMgJiYgaGlkZUZpZWxkcy5pbmNsdWRlcygnaWRzJykgKSA/IChcbiAgICAgICAgICAgICAgICA8UG9zdFNlbGVjdG9yXG4gICAgICAgICAgICAgICAgICAgIHBvc3RUeXBlID0geyBwb3N0VHlwZSB9XG4gICAgICAgICAgICAgICAgICAgIHNlbGVjdGVkUG9zdElkcz17IGlkcyA/IGlkcy5zcGxpdCgnLCcpLm1hcChOdW1iZXIpIDogW10gfVxuICAgICAgICAgICAgICAgICAgICB1cGRhdGVTZWxlY3RlZFBvc3RJZHM9eyB0aGlzLm9uQ2hhbmdlSWRzIH1cbiAgICAgICAgICAgICAgICAvPlxuICAgICAgICAgICAgICAgICkgOiAnJyB9XG4gICAgICAgICAgICAgICAgeyAhKCBoaWRlRmllbGRzICYmIGhpZGVGaWVsZHMuaW5jbHVkZXMoJ2NhdGVnb3J5JykgKSA/IChcbiAgICAgICAgICAgICAgICA8VGVybVNlbGVjdG9yXG4gICAgICAgICAgICAgICAgICAgIHBvc3RUeXBlID0geyBwb3N0VHlwZSB9XG4gICAgICAgICAgICAgICAgICAgIHRheG9ub215ID0geyBjYXRUYXhvbm9teSB9XG4gICAgICAgICAgICAgICAgICAgIHNlbGVjdGVkVGVybUlkcz17IGNhdGVnb3J5ID8gY2F0ZWdvcnkuc3BsaXQoJywnKS5tYXAoTnVtYmVyKSA6IFtdIH1cbiAgICAgICAgICAgICAgICAgICAgdXBkYXRlU2VsZWN0ZWRUZXJtSWRzPXsgdGhpcy5vbkNoYW5nZUNhdGVnb3J5IH1cbiAgICAgICAgICAgICAgICAvPlxuICAgICAgICAgICAgICAgICkgOiAnJyB9XG4gICAgICAgICAgICAgICAgeyAhKCBoaWRlRmllbGRzICYmIGhpZGVGaWVsZHMuaW5jbHVkZXMoJ2ZlYXR1cmVkJykgKSA/IChcbiAgICAgICAgICAgICAgICA8Q2hlY2tib3hDb250cm9sXG4gICAgICAgICAgICAgICAgICAgIGxhYmVsPXtfXygnRmVhdHVyZWQnLCAndm9kaScpfVxuICAgICAgICAgICAgICAgICAgICBoZWxwPXtfXygnQ2hlY2sgdG8gc2VsZWN0IGZlYXR1cmVkIHBvc3RzLicsICd2b2RpJyl9XG4gICAgICAgICAgICAgICAgICAgIGNoZWNrZWQ9eyBmZWF0dXJlZCB9XG4gICAgICAgICAgICAgICAgICAgIG9uQ2hhbmdlPXsgdGhpcy5vbkNoYW5nZUZlYXR1cmVkIH1cbiAgICAgICAgICAgICAgICAvPlxuICAgICAgICAgICAgICAgICkgOiAnJyB9XG4gICAgICAgICAgICAgICAgeyAhKCBoaWRlRmllbGRzICYmIGhpZGVGaWVsZHMuaW5jbHVkZXMoJ3RvcF9yYXRlZCcpICkgPyAoXG4gICAgICAgICAgICAgICAgPENoZWNrYm94Q29udHJvbFxuICAgICAgICAgICAgICAgICAgICBsYWJlbD17X18oJ1RvcCBSYXRlZCcsICd2b2RpJyl9XG4gICAgICAgICAgICAgICAgICAgIGhlbHA9e19fKCdDaGVjayB0byBzZWxlY3QgdG9wIHJhdGVkIHBvc3RzLicsICd2b2RpJyl9XG4gICAgICAgICAgICAgICAgICAgIGNoZWNrZWQ9eyB0b3BfcmF0ZWQgfVxuICAgICAgICAgICAgICAgICAgICBvbkNoYW5nZT17IHRoaXMub25DaGFuZ2VUb3BSYXRlZCB9XG4gICAgICAgICAgICAgICAgLz5cbiAgICAgICAgICAgICAgICApIDogJycgfVxuICAgICAgICAgICAgPC9kaXY+XG4gICAgICAgICk7XG4gICAgfVxufSIsImltcG9ydCB7IEl0ZW1MaXN0IH0gZnJvbSBcIi4vSXRlbUxpc3RcIjtcbmltcG9ydCAqIGFzIGFwaSBmcm9tICcuLi91dGlscy9hcGknO1xuaW1wb3J0IHsgdW5pcXVlQnlJZCwgZGVib3VuY2UgfSBmcm9tICcuLi91dGlscy91c2VmdWwtZnVuY3MnO1xuXG5jb25zdCB7IF9fIH0gPSB3cC5pMThuO1xuY29uc3QgeyBJY29uIH0gPSB3cC5jb21wb25lbnRzO1xuY29uc3QgeyBDb21wb25lbnQgfSA9IHdwLmVsZW1lbnQ7XG5cbi8qKlxuICogVGVybVNlbGVjdG9yIENvbXBvbmVudFxuICovXG5leHBvcnQgY2xhc3MgVGVybVNlbGVjdG9yIGV4dGVuZHMgQ29tcG9uZW50IHtcbiAgICAvKipcbiAgICAgKiBDb25zdHJ1Y3RvciBmb3IgVGVybVNlbGVjdG9yIENvbXBvbmVudC5cbiAgICAgKiBTZXRzIHVwIHN0YXRlLCBhbmQgY3JlYXRlcyBiaW5kaW5ncyBmb3IgZnVuY3Rpb25zLlxuICAgICAqIEBwYXJhbSBvYmplY3QgcHJvcHMgLSBjdXJyZW50IGNvbXBvbmVudCBwcm9wZXJ0aWVzLlxuICAgICAqL1xuICAgIGNvbnN0cnVjdG9yKHByb3BzKSB7XG4gICAgICAgIHN1cGVyKC4uLmFyZ3VtZW50cyk7XG4gICAgICAgIHRoaXMucHJvcHMgPSBwcm9wcztcblxuICAgICAgICB0aGlzLnN0YXRlID0ge1xuICAgICAgICAgICAgdGVybXM6IFtdLFxuICAgICAgICAgICAgbG9hZGluZzogZmFsc2UsXG4gICAgICAgICAgICB0eXBlOiBwcm9wcy5wb3N0VHlwZSB8fCAncG9zdCcsXG4gICAgICAgICAgICB0YXhvbm9teTogcHJvcHMudGF4b25vbXkgfHwgJ2NhdGVnb3J5JyxcbiAgICAgICAgICAgIHRheG9ub21pZXM6IFtdLFxuICAgICAgICAgICAgZmlsdGVyOiAnJyxcbiAgICAgICAgICAgIGZpbHRlckxvYWRpbmc6IGZhbHNlLFxuICAgICAgICAgICAgZmlsdGVyVGVybXM6IFtdLFxuICAgICAgICAgICAgaW5pdGlhbExvYWRpbmc6IGZhbHNlLFxuICAgICAgICB9O1xuXG4gICAgICAgIHRoaXMuYWRkVGVybSA9IHRoaXMuYWRkVGVybS5iaW5kKHRoaXMpO1xuICAgICAgICB0aGlzLnJlbW92ZVRlcm0gPSB0aGlzLnJlbW92ZVRlcm0uYmluZCh0aGlzKTtcbiAgICAgICAgdGhpcy5oYW5kbGVJbnB1dEZpbHRlckNoYW5nZSA9IHRoaXMuaGFuZGxlSW5wdXRGaWx0ZXJDaGFuZ2UuYmluZCh0aGlzKTtcbiAgICAgICAgdGhpcy5kb1Rlcm1GaWx0ZXIgPSBkZWJvdW5jZSh0aGlzLmRvVGVybUZpbHRlci5iaW5kKHRoaXMpLCAzMDApO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFdoZW4gdGhlIGNvbXBvbmVudCBtb3VudHMgaXQgY2FsbHMgdGhpcyBmdW5jdGlvbi5cbiAgICAgKiBGZXRjaGVzIHRlcm1zIHRheG9ub21pZXMsIHNlbGVjdGVkIHRlcm1zIHRoZW4gbWFrZXMgZmlyc3QgY2FsbCBmb3IgdGVybXNcbiAgICAgKi9cbiAgICBjb21wb25lbnREaWRNb3VudCgpIHtcbiAgICAgICAgdGhpcy5zZXRTdGF0ZSh7XG4gICAgICAgICAgICBpbml0aWFsTG9hZGluZzogdHJ1ZSxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgYXBpLmdldFRheG9ub21pZXMoIHsgdHlwZTogdGhpcy5zdGF0ZS50eXBlIH0gKVxuICAgICAgICAgICAgLnRoZW4oKCByZXNwb25zZSApID0+IHtcbiAgICAgICAgICAgICAgICB0aGlzLnNldFN0YXRlKHtcbiAgICAgICAgICAgICAgICAgICAgdGF4b25vbWllczogcmVzcG9uc2VcbiAgICAgICAgICAgICAgICB9LCAoKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMucmV0cmlldmVTZWxlY3RlZFRlcm1zKClcbiAgICAgICAgICAgICAgICAgICAgICAgIC50aGVuKCgpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB0aGlzLnNldFN0YXRlKHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgaW5pdGlhbExvYWRpbmc6IGZhbHNlLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgICAgICAgICAgfSlcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH0pO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEdldFRlcm1zIHdyYXBwZXIsIGJ1aWxkcyB0aGUgcmVxdWVzdCBhcmd1bWVudCBiYXNlZCBzdGF0ZSBhbmQgcGFyYW1ldGVycyBwYXNzZWQvXG4gICAgICogQHBhcmFtIHtvYmplY3R9IGFyZ3MgLSBkZXNpcmVkIGFyZ3VtZW50cyAoY2FuIGJlIGVtcHR5KS5cbiAgICAgKiBAcmV0dXJucyB7UHJvbWlzZTxUPn1cbiAgICAgKi9cbiAgICBnZXRUZXJtcyhhcmdzID0ge30pIHtcbiAgICAgICAgY29uc3QgeyBzZWxlY3RlZFRlcm1JZHMgfSA9IHRoaXMucHJvcHM7XG5cbiAgICAgICAgY29uc3QgZGVmYXVsdEFyZ3MgPSB7XG4gICAgICAgICAgICBwZXJfcGFnZTogMTAsXG4gICAgICAgICAgICB0eXBlOiB0aGlzLnN0YXRlLnR5cGUsXG4gICAgICAgICAgICB0YXhvbm9teTogdGhpcy5zdGF0ZS50YXhvbm9teSxcbiAgICAgICAgICAgIHNlYXJjaDogdGhpcy5zdGF0ZS5maWx0ZXIsXG4gICAgICAgIH07XG5cbiAgICAgICAgY29uc3QgcmVxdWVzdEFyZ3VtZW50cyA9IHtcbiAgICAgICAgICAgIC4uLmRlZmF1bHRBcmdzLFxuICAgICAgICAgICAgLi4uYXJnc1xuICAgICAgICB9O1xuXG4gICAgICAgIHJlcXVlc3RBcmd1bWVudHMucmVzdEJhc2UgPSB0aGlzLnN0YXRlLnRheG9ub21pZXNbdGhpcy5zdGF0ZS50YXhvbm9teV0ucmVzdF9iYXNlO1xuXG4gICAgICAgIHJldHVybiBhcGkuZ2V0VGVybXMocmVxdWVzdEFyZ3VtZW50cylcbiAgICAgICAgICAgIC50aGVuKHJlc3BvbnNlID0+IHtcbiAgICAgICAgICAgICAgICBpZiAocmVxdWVzdEFyZ3VtZW50cy5zZWFyY2gpIHtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5zZXRTdGF0ZSh7XG4gICAgICAgICAgICAgICAgICAgICAgICBmaWx0ZXJUZXJtczogcmVzcG9uc2UuZmlsdGVyKCh7IGlkIH0pID0+IHNlbGVjdGVkVGVybUlkcy5pbmRleE9mKGlkKSA9PT0gLTEpLFxuICAgICAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gcmVzcG9uc2U7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgdGhpcy5zZXRTdGF0ZSh7XG4gICAgICAgICAgICAgICAgICAgIHRlcm1zOiB1bmlxdWVCeUlkKFsuLi50aGlzLnN0YXRlLnRlcm1zLCAuLi5yZXNwb25zZV0pLFxuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgLy8gcmV0dXJuIHJlc3BvbnNlIHRvIGNvbnRpbnVlIHRoZSBjaGFpblxuICAgICAgICAgICAgICAgIHJldHVybiByZXNwb25zZTtcbiAgICAgICAgICAgIH0pO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEdldHMgdGhlIHNlbGVjdGVkIHRlcm1zIGJ5IGlkIGZyb20gdGhlIGB0ZXJtc2Agc3RhdGUgb2JqZWN0IGFuZCBzb3J0cyB0aGVtIGJ5IHRoZWlyIHBvc2l0aW9uIGluIHRoZSBzZWxlY3RlZCBhcnJheS5cbiAgICAgKiBAcmV0dXJucyBBcnJheSBvZiBvYmplY3RzLlxuICAgICAqL1xuICAgIGdldFNlbGVjdGVkVGVybXMoKSB7XG4gICAgICAgIGNvbnN0IHsgc2VsZWN0ZWRUZXJtSWRzIH0gPSB0aGlzLnByb3BzO1xuICAgICAgICByZXR1cm4gdGhpcy5zdGF0ZS50ZXJtc1xuICAgICAgICAgICAgLmZpbHRlcigoeyBpZCB9KSA9PiBzZWxlY3RlZFRlcm1JZHMuaW5kZXhPZihpZCkgIT09IC0xKVxuICAgICAgICAgICAgLnNvcnQoKGEsIGIpID0+IHtcbiAgICAgICAgICAgICAgICBjb25zdCBhSW5kZXggPSB0aGlzLnByb3BzLnNlbGVjdGVkVGVybUlkcy5pbmRleE9mKGEuaWQpO1xuICAgICAgICAgICAgICAgIGNvbnN0IGJJbmRleCA9IHRoaXMucHJvcHMuc2VsZWN0ZWRUZXJtSWRzLmluZGV4T2YoYi5pZCk7XG5cbiAgICAgICAgICAgICAgICBpZiAoYUluZGV4ID4gYkluZGV4KSB7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybiAxO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIGlmIChhSW5kZXggPCBiSW5kZXgpIHtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIC0xO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIHJldHVybiAwO1xuICAgICAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogTWFrZXMgdGhlIG5lY2Vzc2FyeSBhcGkgY2FsbHMgdG8gZmV0Y2ggdGhlIHNlbGVjdGVkIHRlcm1zIGFuZCByZXR1cm5zIGEgcHJvbWlzZS5cbiAgICAgKiBAcmV0dXJucyB7Kn1cbiAgICAgKi9cbiAgICByZXRyaWV2ZVNlbGVjdGVkVGVybXMoKSB7XG4gICAgICAgIGNvbnN0IHsgdGVybVR5cGUsIHNlbGVjdGVkVGVybUlkcyB9ID0gdGhpcy5wcm9wcztcbiAgICAgICAgY29uc3QgeyB0YXhvbm9taWVzIH0gPSB0aGlzLnN0YXRlO1xuXG4gICAgICAgIGlmICggc2VsZWN0ZWRUZXJtSWRzICYmICFzZWxlY3RlZFRlcm1JZHMubGVuZ3RoID4gMCApIHtcbiAgICAgICAgICAgIC8vIHJldHVybiBhIGZha2UgcHJvbWlzZSB0aGF0IGF1dG8gcmVzb2x2ZXMuXG4gICAgICAgICAgICByZXR1cm4gbmV3IFByb21pc2UoKHJlc29sdmUpID0+IHJlc29sdmUoKSk7XG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gdGhpcy5nZXRUZXJtcyh7XG4gICAgICAgICAgICBpbmNsdWRlOiB0aGlzLnByb3BzLnNlbGVjdGVkVGVybUlkcy5qb2luKCcsJyksXG4gICAgICAgICAgICBwZXJfcGFnZTogMTAwLFxuICAgICAgICAgICAgdGVybVR5cGVcbiAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogQWRkcyBkZXNpcmVkIHRlcm0gaWQgdG8gdGhlIHNlbGVjdGVkVGVybUlkcyBMaXN0XG4gICAgICogQHBhcmFtIHtJbnRlZ2VyfSB0ZXJtX2lkXG4gICAgICovXG4gICAgYWRkVGVybSh0ZXJtX2lkKSB7XG4gICAgICAgIGlmICh0aGlzLnN0YXRlLmZpbHRlcikge1xuICAgICAgICAgICAgY29uc3QgdGVybSA9IHRoaXMuc3RhdGUuZmlsdGVyVGVybXMuZmlsdGVyKHAgPT4gcC5pZCA9PT0gdGVybV9pZCk7XG4gICAgICAgICAgICBjb25zdCB0ZXJtcyA9IHVuaXF1ZUJ5SWQoW1xuICAgICAgICAgICAgICAgIC4uLnRoaXMuc3RhdGUudGVybXMsXG4gICAgICAgICAgICAgICAgLi4udGVybVxuICAgICAgICAgICAgXSk7XG5cbiAgICAgICAgICAgIHRoaXMuc2V0U3RhdGUoe1xuICAgICAgICAgICAgICAgIHRlcm1zXG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfVxuXG4gICAgICAgIHRoaXMucHJvcHMudXBkYXRlU2VsZWN0ZWRUZXJtSWRzKFtcbiAgICAgICAgICAgIC4uLnRoaXMucHJvcHMuc2VsZWN0ZWRUZXJtSWRzLFxuICAgICAgICAgICAgdGVybV9pZFxuICAgICAgICBdKTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBSZW1vdmVzIGRlc2lyZWQgdGVybSBpZCB0byB0aGUgc2VsZWN0ZWRUZXJtSWRzIExpc3RcbiAgICAgKiBAcGFyYW0ge0ludGVnZXJ9IHRlcm1faWRcbiAgICAgKi9cbiAgICByZW1vdmVUZXJtKHRlcm1faWQpIHtcbiAgICAgICAgdGhpcy5wcm9wcy51cGRhdGVTZWxlY3RlZFRlcm1JZHMoW1xuICAgICAgICAgICAgLi4udGhpcy5wcm9wcy5zZWxlY3RlZFRlcm1JZHNcbiAgICAgICAgXS5maWx0ZXIoaWQgPT4gaWQgIT09IHRlcm1faWQpKTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBIYW5kbGVzIHRoZSBzZWFyY2ggYm94IGlucHV0IHZhbHVlXG4gICAgICogQHBhcmFtIHN0cmluZyB0eXBlIC0gY29tZXMgZnJvbSB0aGUgZXZlbnQgb2JqZWN0IHRhcmdldC5cbiAgICAgKi9cbiAgICBoYW5kbGVJbnB1dEZpbHRlckNoYW5nZSh7IHRhcmdldDogeyB2YWx1ZTpmaWx0ZXIgPSAnJyB9ID0ge30gfSA9IHt9KSB7XG4gICAgICAgIHRoaXMuc2V0U3RhdGUoe1xuICAgICAgICAgICAgZmlsdGVyXG4gICAgICAgIH0sICgpID0+IHtcbiAgICAgICAgICAgIGlmICghZmlsdGVyKSB7XG4gICAgICAgICAgICAgICAgLy8gcmVtb3ZlIGZpbHRlcmVkIHRlcm1zXG4gICAgICAgICAgICAgICAgcmV0dXJuIHRoaXMuc2V0U3RhdGUoeyBmaWx0ZXJlZFRlcm1zOiBbXSwgZmlsdGVyaW5nOiBmYWxzZSB9KTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgdGhpcy5kb1Rlcm1GaWx0ZXIoKTtcbiAgICAgICAgfSlcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBBY3R1YWwgYXBpIGNhbGwgZm9yIHNlYXJjaGluZyBmb3IgcXVlcnksIHRoaXMgZnVuY3Rpb24gaXMgZGVib3VuY2VkIGluIGNvbnN0cnVjdG9yLlxuICAgICAqL1xuICAgIGRvVGVybUZpbHRlcigpIHtcbiAgICAgICAgY29uc3QgeyBmaWx0ZXIgPSAnJyB9ID0gdGhpcy5zdGF0ZTtcblxuICAgICAgICBpZiAoIWZpbHRlcikge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgdGhpcy5zZXRTdGF0ZSh7XG4gICAgICAgICAgICBmaWx0ZXJpbmc6IHRydWUsXG4gICAgICAgICAgICBmaWx0ZXJMb2FkaW5nOiB0cnVlXG4gICAgICAgIH0pO1xuXG4gICAgICAgIHRoaXMuZ2V0VGVybXMoKVxuICAgICAgICAgICAgLnRoZW4oKCkgPT4ge1xuICAgICAgICAgICAgICAgIHRoaXMuc2V0U3RhdGUoe1xuICAgICAgICAgICAgICAgICAgICBmaWx0ZXJMb2FkaW5nOiBmYWxzZVxuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogUmVuZGVycyB0aGUgVGVybVNlbGVjdG9yIGNvbXBvbmVudC5cbiAgICAgKi9cbiAgICByZW5kZXIoKSB7XG4gICAgICAgIGNvbnN0IGlzRmlsdGVyZWQgPSB0aGlzLnN0YXRlLmZpbHRlcmluZztcbiAgICAgICAgY29uc3QgdGVybUxpc3QgPSBpc0ZpbHRlcmVkICYmICF0aGlzLnN0YXRlLmZpbHRlckxvYWRpbmcgPyB0aGlzLnN0YXRlLmZpbHRlclRlcm1zIDogW107XG4gICAgICAgIGNvbnN0IFNlbGVjdGVkVGVybUxpc3QgID0gdGhpcy5nZXRTZWxlY3RlZFRlcm1zKCk7XG5cbiAgICAgICAgY29uc3QgYWRkSWNvbiA9IDxJY29uIGljb249XCJwbHVzXCIgLz47XG4gICAgICAgIGNvbnN0IHJlbW92ZUljb24gPSA8SWNvbiBpY29uPVwibWludXNcIiAvPjtcblxuICAgICAgICByZXR1cm4gKFxuICAgICAgICAgICAgPGRpdiBjbGFzc05hbWU9XCJjb21wb25lbnRzLWJhc2UtY29udHJvbCBjb21wb25lbnRzLXRlcm0tc2VsZWN0b3JcIj5cbiAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzTmFtZT1cImNvbXBvbmVudHMtYmFzZS1jb250cm9sX19maWVsZC0tc2VsZWN0ZWRcIj5cbiAgICAgICAgICAgICAgICAgICAgPGgyPntfXygnU2VhcmNoIFRlcm0nLCAndm9kaScpfTwvaDI+XG4gICAgICAgICAgICAgICAgICAgIDxJdGVtTGlzdFxuICAgICAgICAgICAgICAgICAgICAgICAgaXRlbXM9e1NlbGVjdGVkVGVybUxpc3R9XG4gICAgICAgICAgICAgICAgICAgICAgICBsb2FkaW5nPXt0aGlzLnN0YXRlLmluaXRpYWxMb2FkaW5nfVxuICAgICAgICAgICAgICAgICAgICAgICAgYWN0aW9uPXt0aGlzLnJlbW92ZVRlcm19XG4gICAgICAgICAgICAgICAgICAgICAgICBpY29uPXtyZW1vdmVJY29ufVxuICAgICAgICAgICAgICAgICAgICAvPlxuICAgICAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICAgICAgICAgIDxkaXYgY2xhc3NOYW1lPVwiY29tcG9uZW50cy1iYXNlLWNvbnRyb2xfX2ZpZWxkXCI+XG4gICAgICAgICAgICAgICAgICAgIDxsYWJlbCBodG1sRm9yPVwic2VhcmNoaW5wdXRcIiBjbGFzc05hbWU9XCJjb21wb25lbnRzLWJhc2UtY29udHJvbF9fbGFiZWxcIj5cbiAgICAgICAgICAgICAgICAgICAgICAgIDxJY29uIGljb249XCJzZWFyY2hcIiAvPlxuICAgICAgICAgICAgICAgICAgICA8L2xhYmVsPlxuICAgICAgICAgICAgICAgICAgICA8aW5wdXRcbiAgICAgICAgICAgICAgICAgICAgICAgIGNsYXNzTmFtZT1cImNvbXBvbmVudHMtdGV4dC1jb250cm9sX19pbnB1dFwiXG4gICAgICAgICAgICAgICAgICAgICAgICBpZD1cInNlYXJjaGlucHV0XCJcbiAgICAgICAgICAgICAgICAgICAgICAgIHR5cGU9XCJzZWFyY2hcIlxuICAgICAgICAgICAgICAgICAgICAgICAgcGxhY2Vob2xkZXI9e19fKCdQbGVhc2UgZW50ZXIgeW91ciBzZWFyY2ggcXVlcnkuLi4nLCAndm9kaScpfVxuICAgICAgICAgICAgICAgICAgICAgICAgdmFsdWU9e3RoaXMuc3RhdGUuZmlsdGVyfVxuICAgICAgICAgICAgICAgICAgICAgICAgb25DaGFuZ2U9e3RoaXMuaGFuZGxlSW5wdXRGaWx0ZXJDaGFuZ2V9XG4gICAgICAgICAgICAgICAgICAgIC8+XG4gICAgICAgICAgICAgICAgICAgIDxJdGVtTGlzdFxuICAgICAgICAgICAgICAgICAgICAgICAgaXRlbXM9e3Rlcm1MaXN0fVxuICAgICAgICAgICAgICAgICAgICAgICAgbG9hZGluZz17dGhpcy5zdGF0ZS5pbml0aWFsTG9hZGluZ3x8dGhpcy5zdGF0ZS5sb2FkaW5nfHx0aGlzLnN0YXRlLmZpbHRlckxvYWRpbmd9XG4gICAgICAgICAgICAgICAgICAgICAgICBmaWx0ZXJlZD17aXNGaWx0ZXJlZH1cbiAgICAgICAgICAgICAgICAgICAgICAgIGFjdGlvbj17dGhpcy5hZGRUZXJtfVxuICAgICAgICAgICAgICAgICAgICAgICAgaWNvbj17YWRkSWNvbn1cbiAgICAgICAgICAgICAgICAgICAgLz5cbiAgICAgICAgICAgICAgICA8L2Rpdj5cbiAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICApO1xuICAgIH1cbn0iLCJjb25zdCB7IGFwaUZldGNoIH0gPSB3cDtcblxuLyoqXG4gKiBNYWtlcyBhIGdldCByZXF1ZXN0IHRvIHRoZSBQb3N0VHlwZXMgZW5kcG9pbnQuXG4gKlxuICogQHJldHVybnMge1Byb21pc2U8YW55Pn1cbiAqL1xuZXhwb3J0IGNvbnN0IGdldFBvc3RUeXBlcyA9ICgpID0+IHtcbiAgICByZXR1cm4gYXBpRmV0Y2goIHsgcGF0aDogJy93cC92Mi90eXBlcycgfSApO1xufTtcblxuLyoqXG4gKiBNYWtlcyBhIGdldCByZXF1ZXN0IHRvIHRoZSBkZXNpcmVkIHBvc3QgdHlwZSBhbmQgYnVpbGRzIHRoZSBxdWVyeSBzdHJpbmcgYmFzZWQgb24gYW4gb2JqZWN0LlxuICpcbiAqIEBwYXJhbSB7c3RyaW5nfGJvb2xlYW59IHJlc3RCYXNlIC0gcmVzdCBiYXNlIGZvciB0aGUgcXVlcnkuXG4gKiBAcGFyYW0ge29iamVjdH0gYXJnc1xuICogQHJldHVybnMge1Byb21pc2U8YW55Pn1cbiAqL1xuZXhwb3J0IGNvbnN0IGdldFBvc3RzID0gKHsgcmVzdEJhc2UgPSBmYWxzZSwgLi4uYXJncyB9KSA9PiB7XG4gICAgY29uc3QgcXVlcnlTdHJpbmcgPSBPYmplY3Qua2V5cyhhcmdzKS5tYXAoYXJnID0+IGAke2FyZ309JHthcmdzW2FyZ119YCkuam9pbignJicpO1xuXG4gICAgbGV0IHBhdGggPSBgL3dwL3YyLyR7cmVzdEJhc2V9PyR7cXVlcnlTdHJpbmd9Jl9lbWJlZGA7XG4gICAgcmV0dXJuIGFwaUZldGNoKCB7IHBhdGg6IHBhdGggfSApO1xufTtcblxuLyoqXG4gKiBNYWtlcyBhIGdldCByZXF1ZXN0IHRvIHRoZSBQb3N0VHlwZSBUYXhvbm9taWVzIGVuZHBvaW50LlxuICpcbiAqIEByZXR1cm5zIHtQcm9taXNlPGFueT59XG4gKi9cbmV4cG9ydCBjb25zdCBnZXRUYXhvbm9taWVzID0gKHsgLi4uYXJncyB9KSA9PiB7XG4gICAgY29uc3QgcXVlcnlTdHJpbmcgPSBPYmplY3Qua2V5cyhhcmdzKS5tYXAoYXJnID0+IGAke2FyZ309JHthcmdzW2FyZ119YCkuam9pbignJicpO1xuXG4gICAgbGV0IHBhdGggPSBgL3dwL3YyL3RheG9ub21pZXM/JHtxdWVyeVN0cmluZ30mX2VtYmVkYDtcbiAgICByZXR1cm4gYXBpRmV0Y2goIHsgcGF0aDogcGF0aCB9ICk7XG59O1xuXG4vKipcbiAqIE1ha2VzIGEgZ2V0IHJlcXVlc3QgdG8gdGhlIGRlc2lyZWQgcG9zdCB0eXBlIGFuZCBidWlsZHMgdGhlIHF1ZXJ5IHN0cmluZyBiYXNlZCBvbiBhbiBvYmplY3QuXG4gKlxuICogQHBhcmFtIHtzdHJpbmd8Ym9vbGVhbn0gcmVzdEJhc2UgLSByZXN0IGJhc2UgZm9yIHRoZSBxdWVyeS5cbiAqIEBwYXJhbSB7b2JqZWN0fSBhcmdzXG4gKiBAcmV0dXJucyB7UHJvbWlzZTxhbnk+fVxuICovXG5leHBvcnQgY29uc3QgZ2V0VGVybXMgPSAoeyByZXN0QmFzZSA9IGZhbHNlLCAuLi5hcmdzIH0pID0+IHtcbiAgICBjb25zdCBxdWVyeVN0cmluZyA9IE9iamVjdC5rZXlzKGFyZ3MpLm1hcChhcmcgPT4gYCR7YXJnfT0ke2FyZ3NbYXJnXX1gKS5qb2luKCcmJyk7XG5cbiAgICBsZXQgcGF0aCA9IGAvd3AvdjIvJHtyZXN0QmFzZX0/JHtxdWVyeVN0cmluZ30mX2VtYmVkYDtcbiAgICByZXR1cm4gYXBpRmV0Y2goIHsgcGF0aDogcGF0aCB9ICk7XG59OyIsIi8qKlxuICogUmV0dXJucyBhIHVuaXF1ZSBhcnJheSBvZiBvYmplY3RzIGJhc2VkIG9uIGEgZGVzaXJlZCBrZXkuXG4gKiBAcGFyYW0ge2FycmF5fSBhcnIgLSBhcnJheSBvZiBvYmplY3RzLlxuICogQHBhcmFtIHtzdHJpbmd8aW50fSBrZXkgLSBrZXkgdG8gZmlsdGVyIG9iamVjdHMgYnlcbiAqL1xuZXhwb3J0IGNvbnN0IHVuaXF1ZUJ5ID0gKGFyciwga2V5KSA9PiB7XG4gICAgbGV0IGtleXMgPSBbXTtcbiAgICByZXR1cm4gYXJyLmZpbHRlcihpdGVtID0+IHtcbiAgICAgICAgaWYgKGtleXMuaW5kZXhPZihpdGVtW2tleV0pICE9PSAtMSkge1xuICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICB9XG5cbiAgICAgICAgcmV0dXJuIGtleXMucHVzaChpdGVtW2tleV0pO1xuICAgIH0pO1xufTtcblxuLyoqXG4gKiBSZXR1cm5zIGEgdW5pcXVlIGFycmF5IG9mIG9iamVjdHMgYmFzZWQgb24gdGhlIGlkIHByb3BlcnR5LlxuICogQHBhcmFtIHthcnJheX0gYXJyIC0gYXJyYXkgb2Ygb2JqZWN0cyB0byBmaWx0ZXIuXG4gKiBAcmV0dXJucyB7Kn1cbiAqL1xuZXhwb3J0IGNvbnN0IHVuaXF1ZUJ5SWQgPSBhcnIgPT4gdW5pcXVlQnkoYXJyLCAnaWQnKTtcblxuLyoqXG4gKiBEZWJvdW5jZSBhIGZ1bmN0aW9uIGJ5IGxpbWl0aW5nIGhvdyBvZnRlbiBpdCBjYW4gcnVuLlxuICogQHBhcmFtIHtmdW5jdGlvbn0gZnVuYyAtIGNhbGxiYWNrIGZ1bmN0aW9uXG4gKiBAcGFyYW0ge0ludGVnZXJ9IHdhaXQgLSBUaW1lIGluIG1pbGxpc2Vjb25kcyBob3cgbG9uZyBpdCBzaG91bGQgd2FpdC5cbiAqIEByZXR1cm5zIHtGdW5jdGlvbn1cbiAqL1xuZXhwb3J0IGNvbnN0IGRlYm91bmNlID0gKGZ1bmMsIHdhaXQpID0+IHtcbiAgICBsZXQgdGltZW91dCA9IG51bGw7XG5cbiAgICByZXR1cm4gZnVuY3Rpb24gKCkge1xuICAgICAgICBjb25zdCBjb250ZXh0ID0gdGhpcztcbiAgICAgICAgY29uc3QgYXJncyA9IGFyZ3VtZW50cztcblxuICAgICAgICBjb25zdCBsYXRlciA9ICgpID0+IHtcbiAgICAgICAgICAgIGZ1bmMuYXBwbHkoY29udGV4dCwgYXJncyk7XG4gICAgICAgIH07XG5cbiAgICAgICAgY2xlYXJUaW1lb3V0KHRpbWVvdXQpO1xuICAgICAgICB0aW1lb3V0ID0gc2V0VGltZW91dChsYXRlciwgd2FpdCk7XG4gICAgfVxufTsiXX0=
