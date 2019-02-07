(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
"use strict";

var _PostSelector = require("../components/PostSelector");

var _DesignOptions = require("../components/DesignOptions");

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; var ownKeys = Object.keys(source); if (typeof Object.getOwnPropertySymbols === 'function') { ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function (sym) { return Object.getOwnPropertyDescriptor(source, sym).enumerable; })); } ownKeys.forEach(function (key) { _defineProperty(target, key, source[key]); }); } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

var __ = wp.i18n.__;
var registerBlockType = wp.blocks.registerBlockType;
var _wp$editor = wp.editor,
    InspectorControls = _wp$editor.InspectorControls,
    MediaUpload = _wp$editor.MediaUpload;
var Fragment = wp.element.Fragment;
var _wp$components = wp.components,
    ServerSideRender = _wp$components.ServerSideRender,
    Disabled = _wp$components.Disabled,
    PanelBody = _wp$components.PanelBody,
    TextControl = _wp$components.TextControl,
    SelectControl = _wp$components.SelectControl,
    Button = _wp$components.Button;
registerBlockType('vodi/section-featured-video', {
  title: __('Featured Video', 'vodi'),
  icon: 'format-video',
  category: 'vodi-blocks',
  edit: function edit(props) {
    var attributes = props.attributes,
        setAttributes = props.setAttributes;
    var feature_video_action_icon = attributes.feature_video_action_icon,
        video_id = attributes.video_id,
        image = attributes.image,
        bg_image = attributes.bg_image,
        design_options = attributes.design_options;

    var onChangeFeatureVideoActionIcon = function onChangeFeatureVideoActionIcon(newFeatureVideoActionIcon) {
      setAttributes({
        feature_video_action_icon: newFeatureVideoActionIcon
      });
    };

    var onChangeIds = function onChangeIds(newIds) {
      setAttributes({
        video_id: newIds.join(',')
      });
    };

    var onChangeImage = function onChangeImage(media) {
      setAttributes({
        image: media.id
      });
    };

    var onChangeBgImage = function onChangeBgImage(media) {
      setAttributes({
        bg_image: media.id
      });
    };

    var onChangeDesignOptions = function onChangeDesignOptions(newDesignOptions) {
      setAttributes({
        design_options: _objectSpread({}, design_options, newDesignOptions)
      });
    };

    var getImageButton = function getImageButton(openEvent) {
      return wp.element.createElement("div", {
        className: "button-container"
      }, wp.element.createElement(Button, {
        onClick: openEvent,
        className: "button button-large"
      }, __('Pick an Image', 'vodi')));
    };

    var getBgImageButton = function getBgImageButton(openEvent) {
      return wp.element.createElement("div", {
        className: "button-container"
      }, wp.element.createElement(Button, {
        onClick: openEvent,
        className: "button button-large"
      }, __('Pick an Background Image', 'vodi')));
    };

    return wp.element.createElement(Fragment, null, wp.element.createElement(InspectorControls, null, wp.element.createElement(_PostSelector.PostSelector, {
      postType: "video",
      selectSingle: true,
      selectedPostIds: video_id ? video_id.split(',').map(Number) : [],
      updateSelectedPostIds: onChangeIds
    }), wp.element.createElement(TextControl, {
      label: __('Feature Video Action Icon', 'vodi'),
      value: feature_video_action_icon,
      onChange: onChangeFeatureVideoActionIcon
    }), wp.element.createElement(MediaUpload, {
      onSelect: onChangeImage,
      type: "image",
      value: image,
      render: function render(_ref) {
        var open = _ref.open;
        return getImageButton(open);
      }
    }), wp.element.createElement(MediaUpload, {
      onSelect: onChangeBgImage,
      type: "image",
      value: bg_image,
      render: function render(_ref2) {
        var open = _ref2.open;
        return getImageButton(open);
      }
    }), wp.element.createElement(PanelBody, {
      title: __('Design Options', 'vodi'),
      initialOpen: false
    }, wp.element.createElement(_DesignOptions.DesignOptions, {
      attributes: _objectSpread({}, design_options),
      updateDesignOptions: onChangeDesignOptions
    }))), wp.element.createElement(Disabled, null, video_id ? wp.element.createElement(ServerSideRender, {
      block: "vodi/section-featured-video",
      attributes: attributes
    }) : __('Choose a video', 'vodi')));
  },
  save: function save() {
    // Rendering in PHP
    return null;
  }
});

},{"../components/DesignOptions":2,"../components/PostSelector":5}],2:[function(require,module,exports){
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

},{"../utils/api":6,"../utils/useful-funcs":7,"./ItemList":4}],6:[function(require,module,exports){
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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJzcmMvdGhlbWVzL3ZvZGkvYXNzZXRzL2VzbmV4dC9ibG9ja3Mvc2VjdGlvbi1mZWF0dXJlZC12aWRlby5qcyIsInNyYy90aGVtZXMvdm9kaS9hc3NldHMvZXNuZXh0L2NvbXBvbmVudHMvRGVzaWduT3B0aW9ucy5qcyIsInNyYy90aGVtZXMvdm9kaS9hc3NldHMvZXNuZXh0L2NvbXBvbmVudHMvSXRlbS5qcyIsInNyYy90aGVtZXMvdm9kaS9hc3NldHMvZXNuZXh0L2NvbXBvbmVudHMvSXRlbUxpc3QuanMiLCJzcmMvdGhlbWVzL3ZvZGkvYXNzZXRzL2VzbmV4dC9jb21wb25lbnRzL1Bvc3RTZWxlY3Rvci5qcyIsInNyYy90aGVtZXMvdm9kaS9hc3NldHMvZXNuZXh0L3V0aWxzL2FwaS5qcyIsInNyYy90aGVtZXMvdm9kaS9hc3NldHMvZXNuZXh0L3V0aWxzL3VzZWZ1bC1mdW5jcy5qcyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTs7O0FDQUE7O0FBQ0E7Ozs7OztJQUVRLEUsR0FBTyxFQUFFLENBQUMsSSxDQUFWLEU7SUFDQSxpQixHQUFzQixFQUFFLENBQUMsTSxDQUF6QixpQjtpQkFDbUMsRUFBRSxDQUFDLE07SUFBdEMsaUIsY0FBQSxpQjtJQUFtQixXLGNBQUEsVztJQUNuQixRLEdBQWEsRUFBRSxDQUFDLE8sQ0FBaEIsUTtxQkFDOEUsRUFBRSxDQUFDLFU7SUFBakYsZ0Isa0JBQUEsZ0I7SUFBa0IsUSxrQkFBQSxRO0lBQVUsUyxrQkFBQSxTO0lBQVcsVyxrQkFBQSxXO0lBQWEsYSxrQkFBQSxhO0lBQWUsTSxrQkFBQSxNO0FBRTNFLGlCQUFpQixDQUFFLDZCQUFGLEVBQWlDO0FBQzlDLEVBQUEsS0FBSyxFQUFFLEVBQUUsQ0FBQyxnQkFBRCxFQUFtQixNQUFuQixDQURxQztBQUc5QyxFQUFBLElBQUksRUFBRSxjQUh3QztBQUs5QyxFQUFBLFFBQVEsRUFBRSxhQUxv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QSxHQUFHO0FBQUEsU0FBSSxRQUFRLENBQUMsR0FBRCxFQUFNLElBQU4sQ0FBWjtBQUFBLENBQXRCO0FBRVA7Ozs7Ozs7Ozs7QUFNTyxJQUFNLFFBQVEsR0FBRyxTQUFYLFFBQVcsQ0FBQyxJQUFELEVBQU8sSUFBUCxFQUFnQjtBQUNwQyxNQUFJLE9BQU8sR0FBRyxJQUFkO0FBRUEsU0FBTyxZQUFZO0FBQ2YsUUFBTSxPQUFPLEdBQUcsSUFBaEI7QUFDQSxRQUFNLElBQUksR0FBRyxTQUFiOztBQUVBLFFBQU0sS0FBSyxHQUFHLFNBQVIsS0FBUSxHQUFNO0FBQ2hCLE1BQUEsSUFBSSxDQUFDLEtBQUwsQ0FBVyxPQUFYLEVBQW9CLElBQXBCO0FBQ0gsS0FGRDs7QUFJQSxJQUFBLFlBQVksQ0FBQyxPQUFELENBQVo7QUFDQSxJQUFBLE9BQU8sR0FBRyxVQUFVLENBQUMsS0FBRCxFQUFRLElBQVIsQ0FBcEI7QUFDSCxHQVZEO0FBV0gsQ0FkTSIsImZpbGUiOiJnZW5lcmF0ZWQuanMiLCJzb3VyY2VSb290IjoiIiwic291cmNlc0NvbnRlbnQiOlsiKGZ1bmN0aW9uKCl7ZnVuY3Rpb24gcihlLG4sdCl7ZnVuY3Rpb24gbyhpLGYpe2lmKCFuW2ldKXtpZighZVtpXSl7dmFyIGM9XCJmdW5jdGlvblwiPT10eXBlb2YgcmVxdWlyZSYmcmVxdWlyZTtpZighZiYmYylyZXR1cm4gYyhpLCEwKTtpZih1KXJldHVybiB1KGksITApO3ZhciBhPW5ldyBFcnJvcihcIkNhbm5vdCBmaW5kIG1vZHVsZSAnXCIraStcIidcIik7dGhyb3cgYS5jb2RlPVwiTU9EVUxFX05PVF9GT1VORFwiLGF9dmFyIHA9bltpXT17ZXhwb3J0czp7fX07ZVtpXVswXS5jYWxsKHAuZXhwb3J0cyxmdW5jdGlvbihyKXt2YXIgbj1lW2ldWzFdW3JdO3JldHVybiBvKG58fHIpfSxwLHAuZXhwb3J0cyxyLGUsbix0KX1yZXR1cm4gbltpXS5leHBvcnRzfWZvcih2YXIgdT1cImZ1bmN0aW9uXCI9PXR5cGVvZiByZXF1aXJlJiZyZXF1aXJlLGk9MDtpPHQubGVuZ3RoO2krKylvKHRbaV0pO3JldHVybiBvfXJldHVybiByfSkoKSIsImltcG9ydCB7IFBvc3RTZWxlY3RvciB9IGZyb20gJy4uL2NvbXBvbmVudHMvUG9zdFNlbGVjdG9yJztcbmltcG9ydCB7IERlc2lnbk9wdGlvbnMgfSBmcm9tICcuLi9jb21wb25lbnRzL0Rlc2lnbk9wdGlvbnMnO1xuXG5jb25zdCB7IF9fIH0gPSB3cC5pMThuO1xuY29uc3QgeyByZWdpc3RlckJsb2NrVHlwZSB9ID0gd3AuYmxvY2tzO1xuY29uc3QgeyBJbnNwZWN0b3JDb250cm9scywgTWVkaWFVcGxvYWQgfSA9IHdwLmVkaXRvcjtcbmNvbnN0IHsgRnJhZ21lbnQgfSA9IHdwLmVsZW1lbnQ7XG5jb25zdCB7IFNlcnZlclNpZGVSZW5kZXIsIERpc2FibGVkLCBQYW5lbEJvZHksIFRleHRDb250cm9sLCBTZWxlY3RDb250cm9sLCBCdXR0b24gfSA9IHdwLmNvbXBvbmVudHM7XG5cbnJlZ2lzdGVyQmxvY2tUeXBlKCAndm9kaS9zZWN0aW9uLWZlYXR1cmVkLXZpZGVvJywge1xuICAgIHRpdGxlOiBfXygnRmVhdHVyZWQgVmlkZW8nLCAndm9kaScpLFxuXG4gICAgaWNvbjogJ2Zvcm1hdC12aWRlbycsXG5cbiAgICBjYXRlZ29yeTogJ3ZvZGktYmxvY2tzJyxcblxuICAgIGVkaXQ6ICggKCBwcm9wcyApID0+IHtcbiAgICAgICAgY29uc3QgeyBhdHRyaWJ1dGVzLCBzZXRBdHRyaWJ1dGVzIH0gPSBwcm9wcztcbiAgICAgICAgY29uc3QgeyBmZWF0dXJlX3ZpZGVvX2FjdGlvbl9pY29uLCB2aWRlb19pZCwgaW1hZ2UsIGJnX2ltYWdlLCBkZXNpZ25fb3B0aW9ucyB9ID0gYXR0cmlidXRlcztcblxuICAgICAgICBjb25zdCBvbkNoYW5nZUZlYXR1cmVWaWRlb0FjdGlvbkljb24gPSBuZXdGZWF0dXJlVmlkZW9BY3Rpb25JY29uID0+IHtcbiAgICAgICAgICAgIHNldEF0dHJpYnV0ZXMoIHsgZmVhdHVyZV92aWRlb19hY3Rpb25faWNvbjogbmV3RmVhdHVyZVZpZGVvQWN0aW9uSWNvbiB9ICk7XG4gICAgICAgIH07XG5cbiAgICAgICAgY29uc3Qgb25DaGFuZ2VJZHMgPSBuZXdJZHM9PiB7XG4gICAgICAgICAgICBzZXRBdHRyaWJ1dGVzKCB7IHZpZGVvX2lkOiBuZXdJZHMuam9pbignLCcpIH0gKTtcbiAgICAgICAgfTtcblxuICAgICAgICBjb25zdCBvbkNoYW5nZUltYWdlID0gbWVkaWEgPT4ge1xuICAgICAgICAgICAgc2V0QXR0cmlidXRlcyggeyBpbWFnZTogbWVkaWEuaWQgfSApO1xuICAgICAgICB9O1xuXG4gICAgICAgIGNvbnN0IG9uQ2hhbmdlQmdJbWFnZSA9IG1lZGlhID0+IHtcbiAgICAgICAgICAgIHNldEF0dHJpYnV0ZXMoIHsgYmdfaW1hZ2U6IG1lZGlhLmlkIH0gKTtcbiAgICAgICAgfTtcblxuICAgICAgICBjb25zdCBvbkNoYW5nZURlc2lnbk9wdGlvbnMgPSBuZXdEZXNpZ25PcHRpb25zID0+IHtcbiAgICAgICAgICAgIHNldEF0dHJpYnV0ZXMoIHsgZGVzaWduX29wdGlvbnM6IHsgLi4uZGVzaWduX29wdGlvbnMsIC4uLm5ld0Rlc2lnbk9wdGlvbnMgfSB9ICk7XG4gICAgICAgIH07XG5cblxuICAgICAgICBjb25zdCBnZXRJbWFnZUJ1dHRvbiA9IChvcGVuRXZlbnQpID0+IHtcbiAgICAgICAgICAgIHJldHVybiAoXG4gICAgICAgICAgICAgICAgPGRpdiBjbGFzc05hbWU9XCJidXR0b24tY29udGFpbmVyXCI+XG4gICAgICAgICAgICAgICAgICAgIDxCdXR0b24gXG4gICAgICAgICAgICAgICAgICAgICAgICBvbkNsaWNrPXsgb3BlbkV2ZW50IH1cbiAgICAgICAgICAgICAgICAgICAgICAgIGNsYXNzTmFtZT1cImJ1dHRvbiBidXR0b24tbGFyZ2VcIlxuICAgICAgICAgICAgICAgICAgICA+XG4gICAgICAgICAgICAgICAgICAgICAgICB7X18oJ1BpY2sgYW4gSW1hZ2UnLCAndm9kaScpfVxuICAgICAgICAgICAgICAgICAgICA8L0J1dHRvbj5cbiAgICAgICAgICAgICAgICA8L2Rpdj5cbiAgICAgICAgICAgICk7XG4gICAgICAgIH07XG5cbiAgICAgICAgY29uc3QgZ2V0QmdJbWFnZUJ1dHRvbiA9IChvcGVuRXZlbnQpID0+IHtcbiAgICAgICAgICAgIHJldHVybiAoXG4gICAgICAgICAgICAgICAgPGRpdiBjbGFzc05hbWU9XCJidXR0b24tY29udGFpbmVyXCI+XG4gICAgICAgICAgICAgICAgICAgIDxCdXR0b24gXG4gICAgICAgICAgICAgICAgICAgICAgICBvbkNsaWNrPXsgb3BlbkV2ZW50IH1cbiAgICAgICAgICAgICAgICAgICAgICAgIGNsYXNzTmFtZT1cImJ1dHRvbiBidXR0b24tbGFyZ2VcIlxuICAgICAgICAgICAgICAgICAgICA+XG4gICAgICAgICAgICAgICAgICAgICAgICB7X18oJ1BpY2sgYW4gQmFja2dyb3VuZCBJbWFnZScsICd2b2RpJyl9XG4gICAgICAgICAgICAgICAgICAgIDwvQnV0dG9uPlxuICAgICAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICAgICAgKTtcbiAgICAgICAgfTtcblxuICAgICAgICByZXR1cm4gKFxuICAgICAgICAgICAgPEZyYWdtZW50PlxuICAgICAgICAgICAgICAgIDxJbnNwZWN0b3JDb250cm9scz5cbiAgICAgICAgICAgICAgICAgICAgPFBvc3RTZWxlY3RvclxuICAgICAgICAgICAgICAgICAgICAgICAgcG9zdFR5cGUgPSAndmlkZW8nXG4gICAgICAgICAgICAgICAgICAgICAgICBzZWxlY3RTaW5nbGUgPSB7IHRydWUgfVxuICAgICAgICAgICAgICAgICAgICAgICAgc2VsZWN0ZWRQb3N0SWRzPXsgdmlkZW9faWQgPyB2aWRlb19pZC5zcGxpdCgnLCcpLm1hcChOdW1iZXIpIDogW10gfVxuICAgICAgICAgICAgICAgICAgICAgICAgdXBkYXRlU2VsZWN0ZWRQb3N0SWRzPXsgb25DaGFuZ2VJZHMgfVxuICAgICAgICAgICAgICAgICAgICAvPlxuICAgICAgICAgICAgICAgICAgICA8VGV4dENvbnRyb2xcbiAgICAgICAgICAgICAgICAgICAgICAgIGxhYmVsPXtfXygnRmVhdHVyZSBWaWRlbyBBY3Rpb24gSWNvbicsICd2b2RpJyl9XG4gICAgICAgICAgICAgICAgICAgICAgICB2YWx1ZT17IGZlYXR1cmVfdmlkZW9fYWN0aW9uX2ljb24gfVxuICAgICAgICAgICAgICAgICAgICAgICAgb25DaGFuZ2U9eyBvbkNoYW5nZUZlYXR1cmVWaWRlb0FjdGlvbkljb24gfVxuICAgICAgICAgICAgICAgICAgICAvPlxuICAgICAgICAgICAgICAgICAgICA8TWVkaWFVcGxvYWRcbiAgICAgICAgICAgICAgICAgICAgICAgIG9uU2VsZWN0PXtvbkNoYW5nZUltYWdlfVxuICAgICAgICAgICAgICAgICAgICAgICAgdHlwZT1cImltYWdlXCJcbiAgICAgICAgICAgICAgICAgICAgICAgIHZhbHVlPXsgaW1hZ2UgfVxuICAgICAgICAgICAgICAgICAgICAgICAgcmVuZGVyPXsgKHsgb3BlbiB9KSA9PiBnZXRJbWFnZUJ1dHRvbihvcGVuKSB9XG4gICAgICAgICAgICAgICAgICAgIC8+XG4gICAgICAgICAgICAgICAgICAgIDxNZWRpYVVwbG9hZFxuICAgICAgICAgICAgICAgICAgICAgICAgb25TZWxlY3Q9e29uQ2hhbmdlQmdJbWFnZX1cbiAgICAgICAgICAgICAgICAgICAgICAgIHR5cGU9XCJpbWFnZVwiXG4gICAgICAgICAgICAgICAgICAgICAgICB2YWx1ZT17IGJnX2ltYWdlIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIHJlbmRlcj17ICh7IG9wZW4gfSkgPT4gZ2V0SW1hZ2VCdXR0b24ob3BlbikgfVxuICAgICAgICAgICAgICAgICAgICAvPlxuICAgICAgICAgICAgICAgICAgICA8UGFuZWxCb2R5XG4gICAgICAgICAgICAgICAgICAgICAgICB0aXRsZT17X18oJ0Rlc2lnbiBPcHRpb25zJywgJ3ZvZGknKX1cbiAgICAgICAgICAgICAgICAgICAgICAgIGluaXRpYWxPcGVuPXsgZmFsc2UgfVxuICAgICAgICAgICAgICAgICAgICA+XG4gICAgICAgICAgICAgICAgICAgICAgICA8RGVzaWduT3B0aW9uc1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGF0dHJpYnV0ZXMgPSB7IHsgLi4uZGVzaWduX29wdGlvbnMgfSB9XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdXBkYXRlRGVzaWduT3B0aW9ucyA9IHsgb25DaGFuZ2VEZXNpZ25PcHRpb25zIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIC8+XG4gICAgICAgICAgICAgICAgICAgIDwvUGFuZWxCb2R5PlxuICAgICAgICAgICAgICAgIDwvSW5zcGVjdG9yQ29udHJvbHM+XG4gICAgICAgICAgICAgICAgPERpc2FibGVkPlxuICAgICAgICAgICAgICAgICAgICB7IHZpZGVvX2lkID8gKFxuICAgICAgICAgICAgICAgICAgICA8U2VydmVyU2lkZVJlbmRlclxuICAgICAgICAgICAgICAgICAgICAgICAgYmxvY2s9XCJ2b2RpL3NlY3Rpb24tZmVhdHVyZWQtdmlkZW9cIlxuICAgICAgICAgICAgICAgICAgICAgICAgYXR0cmlidXRlcz17IGF0dHJpYnV0ZXMgfVxuICAgICAgICAgICAgICAgICAgICAvPlxuICAgICAgICAgICAgICAgICAgICApIDogX18oJ0Nob29zZSBhIHZpZGVvJywgJ3ZvZGknKSB9XG4gICAgICAgICAgICAgICAgPC9EaXNhYmxlZD5cbiAgICAgICAgICAgIDwvRnJhZ21lbnQ+XG4gICAgICAgICk7XG4gICAgfSApLFxuXG4gICAgc2F2ZSgpIHtcbiAgICAgICAgLy8gUmVuZGVyaW5nIGluIFBIUFxuICAgICAgICByZXR1cm4gbnVsbDtcbiAgICB9LFxufSApOyIsImNvbnN0IHsgX18gfSA9IHdwLmkxOG47XG5jb25zdCB7IENvbXBvbmVudCB9ID0gd3AuZWxlbWVudDtcbmNvbnN0IHsgUmFuZ2VDb250cm9sIH0gPSB3cC5jb21wb25lbnRzO1xuXG4vKipcbiAqIERlc2lnbk9wdGlvbnMgQ29tcG9uZW50XG4gKi9cbmV4cG9ydCBjbGFzcyBEZXNpZ25PcHRpb25zIGV4dGVuZHMgQ29tcG9uZW50IHtcbiAgICAvKipcbiAgICAgKiBDb25zdHJ1Y3RvciBmb3IgRGVzaWduT3B0aW9ucyBDb21wb25lbnQuXG4gICAgICogU2V0cyB1cCBzdGF0ZSwgYW5kIGNyZWF0ZXMgYmluZGluZ3MgZm9yIGZ1bmN0aW9ucy5cbiAgICAgKiBAcGFyYW0gb2JqZWN0IHByb3BzIC0gY3VycmVudCBjb21wb25lbnQgcHJvcGVydGllcy5cbiAgICAgKi9cbiAgICBjb25zdHJ1Y3Rvcihwcm9wcykge1xuICAgICAgICBzdXBlciguLi5hcmd1bWVudHMpO1xuICAgICAgICB0aGlzLnByb3BzID0gcHJvcHM7XG5cbiAgICAgICAgdGhpcy5vbkNoYW5nZVBhZGRpbmdUb3AgPSB0aGlzLm9uQ2hhbmdlUGFkZGluZ1RvcC5iaW5kKHRoaXMpO1xuICAgICAgICB0aGlzLm9uQ2hhbmdlUGFkZGluZ0JvdHRvbSA9IHRoaXMub25DaGFuZ2VQYWRkaW5nQm90dG9tLmJpbmQodGhpcyk7XG4gICAgICAgIHRoaXMub25DaGFuZ2VQYWRkaW5nTGVmdCA9IHRoaXMub25DaGFuZ2VQYWRkaW5nTGVmdC5iaW5kKHRoaXMpO1xuICAgICAgICB0aGlzLm9uQ2hhbmdlUGFkZGluZ1JpZ2h0ID0gdGhpcy5vbkNoYW5nZVBhZGRpbmdSaWdodC5iaW5kKHRoaXMpO1xuICAgICAgICB0aGlzLm9uQ2hhbmdlTWFyZ2luVG9wID0gdGhpcy5vbkNoYW5nZU1hcmdpblRvcC5iaW5kKHRoaXMpO1xuICAgICAgICB0aGlzLm9uQ2hhbmdlTWFyZ2luQm90dG9tID0gdGhpcy5vbkNoYW5nZU1hcmdpbkJvdHRvbS5iaW5kKHRoaXMpO1xuICAgIH1cblxuICAgIG9uQ2hhbmdlUGFkZGluZ1RvcCggbmV3b25DaGFuZ2VQYWRkaW5nVG9wICkge1xuICAgICAgICB0aGlzLnByb3BzLnVwZGF0ZURlc2lnbk9wdGlvbnMoe1xuICAgICAgICAgICAgcGFkZGluZ190b3A6IG5ld29uQ2hhbmdlUGFkZGluZ1RvcFxuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICBvbkNoYW5nZVBhZGRpbmdCb3R0b20oIG5ld29uQ2hhbmdlUGFkZGluZ0JvdHRvbSApIHtcbiAgICAgICAgdGhpcy5wcm9wcy51cGRhdGVEZXNpZ25PcHRpb25zKHtcbiAgICAgICAgICAgIHBhZGRpbmdfYm90dG9tOiBuZXdvbkNoYW5nZVBhZGRpbmdCb3R0b21cbiAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgb25DaGFuZ2VQYWRkaW5nTGVmdCggbmV3b25DaGFuZ2VQYWRkaW5nTGVmdCApIHtcbiAgICAgICAgdGhpcy5wcm9wcy51cGRhdGVEZXNpZ25PcHRpb25zKHtcbiAgICAgICAgICAgIHBhZGRpbmdfbGVmdDogbmV3b25DaGFuZ2VQYWRkaW5nTGVmdFxuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICBvbkNoYW5nZVBhZGRpbmdSaWdodCggbmV3b25DaGFuZ2VQYWRkaW5nUmlnaHQgKSB7XG4gICAgICAgIHRoaXMucHJvcHMudXBkYXRlRGVzaWduT3B0aW9ucyh7XG4gICAgICAgICAgICBwYWRkaW5nX3JpZ2h0OiBuZXdvbkNoYW5nZVBhZGRpbmdSaWdodFxuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICBvbkNoYW5nZU1hcmdpblRvcCggbmV3b25DaGFuZ2VNYXJnaW5Ub3AgKSB7XG4gICAgICAgIHRoaXMucHJvcHMudXBkYXRlRGVzaWduT3B0aW9ucyh7XG4gICAgICAgICAgICBtYXJnaW5fdG9wOiBuZXdvbkNoYW5nZU1hcmdpblRvcFxuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICBvbkNoYW5nZU1hcmdpbkJvdHRvbSggbmV3b25DaGFuZ2VNYXJnaW5Cb3R0b20gKSB7XG4gICAgICAgIHRoaXMucHJvcHMudXBkYXRlRGVzaWduT3B0aW9ucyh7XG4gICAgICAgICAgICBtYXJnaW5fYm90dG9tOiBuZXdvbkNoYW5nZU1hcmdpbkJvdHRvbVxuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBSZW5kZXJzIHRoZSBEZXNpZ25PcHRpb25zIGNvbXBvbmVudC5cbiAgICAgKi9cbiAgICByZW5kZXIoKSB7XG4gICAgICAgIGNvbnN0IHsgYXR0cmlidXRlcyB9ID0gdGhpcy5wcm9wcztcbiAgICAgICAgY29uc3QgeyBwYWRkaW5nX3RvcCwgcGFkZGluZ19ib3R0b20sIHBhZGRpbmdfbGVmdCwgcGFkZGluZ19yaWdodCwgbWFyZ2luX3RvcCwgbWFyZ2luX2JvdHRvbSB9ID0gYXR0cmlidXRlcztcblxuICAgICAgICByZXR1cm4gKFxuICAgICAgICAgICAgPGRpdj5cbiAgICAgICAgICAgICAgICA8UmFuZ2VDb250cm9sXG4gICAgICAgICAgICAgICAgICAgIGxhYmVsPXtfXygnUGFkZGluZyBUb3AgKHB4KScsICd2b2RpJyl9XG4gICAgICAgICAgICAgICAgICAgIHZhbHVlPXsgcGFkZGluZ190b3AgfVxuICAgICAgICAgICAgICAgICAgICBvbkNoYW5nZT17IHRoaXMub25DaGFuZ2VQYWRkaW5nVG9wIH1cbiAgICAgICAgICAgICAgICAgICAgbWluPXsgMCB9XG4gICAgICAgICAgICAgICAgICAgIG1heD17IDEwMCB9XG4gICAgICAgICAgICAgICAgLz5cbiAgICAgICAgICAgICAgICA8UmFuZ2VDb250cm9sXG4gICAgICAgICAgICAgICAgICAgIGxhYmVsPXtfXygnUGFkZGluZyBCb3R0b20gKHB4KScsICd2b2RpJyl9XG4gICAgICAgICAgICAgICAgICAgIHZhbHVlPXsgcGFkZGluZ19ib3R0b20gfVxuICAgICAgICAgICAgICAgICAgICBvbkNoYW5nZT17IHRoaXMub25DaGFuZ2VQYWRkaW5nQm90dG9tIH1cbiAgICAgICAgICAgICAgICAgICAgbWluPXsgMCB9XG4gICAgICAgICAgICAgICAgICAgIG1heD17IDEwMCB9XG4gICAgICAgICAgICAgICAgLz5cbiAgICAgICAgICAgICAgICA8UmFuZ2VDb250cm9sXG4gICAgICAgICAgICAgICAgICAgIGxhYmVsPXtfXygnUGFkZGluZyBMZWZ0IChweCknLCAndm9kaScpfVxuICAgICAgICAgICAgICAgICAgICB2YWx1ZT17IHBhZGRpbmdfbGVmdCB9XG4gICAgICAgICAgICAgICAgICAgIG9uQ2hhbmdlPXsgdGhpcy5vbkNoYW5nZVBhZGRpbmdMZWZ0IH1cbiAgICAgICAgICAgICAgICAgICAgbWluPXsgMCB9XG4gICAgICAgICAgICAgICAgICAgIG1heD17IDEwMCB9XG4gICAgICAgICAgICAgICAgLz5cbiAgICAgICAgICAgICAgICA8UmFuZ2VDb250cm9sXG4gICAgICAgICAgICAgICAgICAgIGxhYmVsPXtfXygnUGFkZGluZyBSaWdodCAocHgpJywgJ3ZvZGknKX1cbiAgICAgICAgICAgICAgICAgICAgdmFsdWU9eyBwYWRkaW5nX3JpZ2h0IH1cbiAgICAgICAgICAgICAgICAgICAgb25DaGFuZ2U9eyB0aGlzLm9uQ2hhbmdlUGFkZGluZ1JpZ2h0IH1cbiAgICAgICAgICAgICAgICAgICAgbWluPXsgMCB9XG4gICAgICAgICAgICAgICAgICAgIG1heD17IDEwMCB9XG4gICAgICAgICAgICAgICAgLz5cbiAgICAgICAgICAgICAgICA8UmFuZ2VDb250cm9sXG4gICAgICAgICAgICAgICAgICAgIGxhYmVsPXtfXygnTWFyZ2luIFRvcCAocHgpJywgJ3ZvZGknKX1cbiAgICAgICAgICAgICAgICAgICAgdmFsdWU9eyBtYXJnaW5fdG9wIH1cbiAgICAgICAgICAgICAgICAgICAgb25DaGFuZ2U9eyB0aGlzLm9uQ2hhbmdlTWFyZ2luVG9wIH1cbiAgICAgICAgICAgICAgICAgICAgbWluPXsgLTEwMCB9XG4gICAgICAgICAgICAgICAgICAgIG1heD17IDEwMCB9XG4gICAgICAgICAgICAgICAgLz5cbiAgICAgICAgICAgICAgICA8UmFuZ2VDb250cm9sXG4gICAgICAgICAgICAgICAgICAgIGxhYmVsPXtfXygnTWFyZ2luIEJvdHRvbSAocHgpJywgJ3ZvZGknKX1cbiAgICAgICAgICAgICAgICAgICAgdmFsdWU9eyBtYXJnaW5fYm90dG9tIH1cbiAgICAgICAgICAgICAgICAgICAgb25DaGFuZ2U9eyB0aGlzLm9uQ2hhbmdlTWFyZ2luQm90dG9tIH1cbiAgICAgICAgICAgICAgICAgICAgbWluPXsgLTEwMCB9XG4gICAgICAgICAgICAgICAgICAgIG1heD17IDEwMCB9XG4gICAgICAgICAgICAgICAgLz5cbiAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICApO1xuICAgIH1cbn0iLCJcbi8qKlxuICogSXRlbSBDb21wb25lbnQuXG4gKlxuICogQHBhcmFtIHtzdHJpbmd9IGl0ZW1UaXRsZSAtIEN1cnJlbnQgaXRlbSB0aXRsZS5cbiAqIEBwYXJhbSB7ZnVuY3Rpb259IGNsaWNrSGFuZGxlciAtIHRoaXMgaXMgdGhlIGhhbmRsaW5nIGZ1bmN0aW9uIGZvciB0aGUgYWRkL3JlbW92ZSBmdW5jdGlvblxuICogQHBhcmFtIHtJbnRlZ2VyfSBpdGVtSWQgLSBDdXJyZW50IGl0ZW0gSURcbiAqIEBwYXJhbSBpY29uXG4gKiBAcmV0dXJucyB7Kn0gSXRlbSBIVE1MLlxuICovXG5leHBvcnQgY29uc3QgSXRlbSA9ICh7IHRpdGxlOiB7IHJlbmRlcmVkOiBpdGVtVGl0bGUgfSA9IHt9LCBuYW1lLCBjbGlja0hhbmRsZXIsIGlkOiBpdGVtSWQsIGljb24gfSkgPT4gKFxuICAgIDxhcnRpY2xlIGNsYXNzTmFtZT1cIml0ZW1cIj5cbiAgICAgICAgPGRpdiBjbGFzc05hbWU9XCJpdGVtLWJvZHlcIj5cbiAgICAgICAgICAgIDxoMyBjbGFzc05hbWU9XCJpdGVtLXRpdGxlXCI+e2l0ZW1UaXRsZX17bmFtZX08L2gzPlxuICAgICAgICA8L2Rpdj5cbiAgICAgICAgPGJ1dHRvbiBvbkNsaWNrPXsoKSA9PiBjbGlja0hhbmRsZXIoaXRlbUlkKX0+e2ljb259PC9idXR0b24+XG4gICAgPC9hcnRpY2xlPlxuKTsiLCJpbXBvcnQgeyBJdGVtIH0gZnJvbSAnLi9JdGVtJztcblxuY29uc3QgeyBfXyB9ID0gd3AuaTE4bjtcblxuLyoqXG4gKiBJdGVtTGlzdCBDb21wb25lbnRcbiAqIEBwYXJhbSBvYmplY3QgcHJvcHMgLSBDb21wb25lbnQgcHJvcHMuXG4gKiBAcmV0dXJucyB7Kn1cbiAqIEBjb25zdHJ1Y3RvclxuICovXG5leHBvcnQgY29uc3QgSXRlbUxpc3QgPSBwcm9wcyA9PiB7XG4gICAgY29uc3QgeyBmaWx0ZXJlZCA9IGZhbHNlLCBsb2FkaW5nID0gZmFsc2UsIGl0ZW1zID0gW10sIGFjdGlvbiA9ICgpID0+IHt9LCBpY29uID0gbnVsbCB9ID0gcHJvcHM7XG5cbiAgICBpZiAobG9hZGluZykge1xuICAgICAgICByZXR1cm4gPHAgY2xhc3NOYW1lPVwibG9hZGluZy1pdGVtc1wiPntfXygnTG9hZGluZyAuLi4nLCAndm9kaScpfTwvcD47XG4gICAgfVxuXG4gICAgaWYgKGZpbHRlcmVkICYmIGl0ZW1zLmxlbmd0aCA8IDEpIHtcbiAgICAgICAgcmV0dXJuIChcbiAgICAgICAgICAgIDxkaXYgY2xhc3NOYW1lPVwiaXRlbS1saXN0XCI+XG4gICAgICAgICAgICAgICAgPHA+e19fKCdZb3VyIHF1ZXJ5IHlpZWxkZWQgbm8gcmVzdWx0cywgcGxlYXNlIHRyeSBhZ2Fpbi4nLCAndm9kaScpfTwvcD5cbiAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICApO1xuICAgIH1cblxuICAgIGlmICggISBpdGVtcyB8fCBpdGVtcy5sZW5ndGggPCAxICkge1xuICAgICAgICByZXR1cm4gPHAgY2xhc3NOYW1lPVwibm8taXRlbXNcIj57X18oJ05vdCBmb3VuZC4nLCAndm9kaScpfTwvcD5cbiAgICB9XG5cbiAgICByZXR1cm4gKFxuICAgICAgICA8ZGl2IGNsYXNzTmFtZT1cIml0ZW0tbGlzdFwiPlxuICAgICAgICAgICAge2l0ZW1zLm1hcCgoaXRlbSkgPT4gPEl0ZW0ga2V5PXtpdGVtLmlkfSB7Li4uaXRlbX0gY2xpY2tIYW5kbGVyPXthY3Rpb259IGljb249e2ljb259IC8+KX1cbiAgICAgICAgPC9kaXY+XG4gICAgKTtcbn07IiwiaW1wb3J0IHsgSXRlbUxpc3QgfSBmcm9tICcuL0l0ZW1MaXN0JztcbmltcG9ydCAqIGFzIGFwaSBmcm9tICcuLi91dGlscy9hcGknO1xuaW1wb3J0IHsgdW5pcXVlQnlJZCwgZGVib3VuY2UgfSBmcm9tICcuLi91dGlscy91c2VmdWwtZnVuY3MnO1xuXG5jb25zdCB7IF9fIH0gPSB3cC5pMThuO1xuY29uc3QgeyBJY29uIH0gPSB3cC5jb21wb25lbnRzO1xuY29uc3QgeyBDb21wb25lbnQgfSA9IHdwLmVsZW1lbnQ7XG5cbi8qKlxuICogUG9zdFNlbGVjdG9yIENvbXBvbmVudFxuICovXG5leHBvcnQgY2xhc3MgUG9zdFNlbGVjdG9yIGV4dGVuZHMgQ29tcG9uZW50IHtcbiAgICAvKipcbiAgICAgKiBDb25zdHJ1Y3RvciBmb3IgUG9zdFNlbGVjdG9yIENvbXBvbmVudC5cbiAgICAgKiBTZXRzIHVwIHN0YXRlLCBhbmQgY3JlYXRlcyBiaW5kaW5ncyBmb3IgZnVuY3Rpb25zLlxuICAgICAqIEBwYXJhbSBvYmplY3QgcHJvcHMgLSBjdXJyZW50IGNvbXBvbmVudCBwcm9wZXJ0aWVzLlxuICAgICAqL1xuICAgIGNvbnN0cnVjdG9yKHByb3BzKSB7XG4gICAgICAgIHN1cGVyKC4uLmFyZ3VtZW50cyk7XG4gICAgICAgIHRoaXMucHJvcHMgPSBwcm9wcztcblxuICAgICAgICB0aGlzLnN0YXRlID0ge1xuICAgICAgICAgICAgcG9zdHM6IFtdLFxuICAgICAgICAgICAgbG9hZGluZzogZmFsc2UsXG4gICAgICAgICAgICB0eXBlOiBwcm9wcy5wb3N0VHlwZSB8fCAncG9zdCcsXG4gICAgICAgICAgICB0eXBlczogW10sXG4gICAgICAgICAgICBmaWx0ZXI6ICcnLFxuICAgICAgICAgICAgZmlsdGVyTG9hZGluZzogZmFsc2UsXG4gICAgICAgICAgICBmaWx0ZXJQb3N0czogW10sXG4gICAgICAgICAgICBpbml0aWFsTG9hZGluZzogZmFsc2UsXG4gICAgICAgIH07XG5cbiAgICAgICAgdGhpcy5hZGRQb3N0ID0gdGhpcy5hZGRQb3N0LmJpbmQodGhpcyk7XG4gICAgICAgIHRoaXMucmVtb3ZlUG9zdCA9IHRoaXMucmVtb3ZlUG9zdC5iaW5kKHRoaXMpO1xuICAgICAgICB0aGlzLmhhbmRsZUlucHV0RmlsdGVyQ2hhbmdlID0gdGhpcy5oYW5kbGVJbnB1dEZpbHRlckNoYW5nZS5iaW5kKHRoaXMpO1xuICAgICAgICB0aGlzLmRvUG9zdEZpbHRlciA9IGRlYm91bmNlKHRoaXMuZG9Qb3N0RmlsdGVyLmJpbmQodGhpcyksIDMwMCk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogV2hlbiB0aGUgY29tcG9uZW50IG1vdW50cyBpdCBjYWxscyB0aGlzIGZ1bmN0aW9uLlxuICAgICAqIEZldGNoZXMgcG9zdHMgdHlwZXMsIHNlbGVjdGVkIHBvc3RzIHRoZW4gbWFrZXMgZmlyc3QgY2FsbCBmb3IgcG9zdHNcbiAgICAgKi9cbiAgICBjb21wb25lbnREaWRNb3VudCgpIHtcbiAgICAgICAgdGhpcy5zZXRTdGF0ZSh7XG4gICAgICAgICAgICBpbml0aWFsTG9hZGluZzogdHJ1ZSxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgYXBpLmdldFBvc3RUeXBlcygpXG4gICAgICAgICAgICAudGhlbigoIHJlc3BvbnNlICkgPT4ge1xuICAgICAgICAgICAgICAgIHRoaXMuc2V0U3RhdGUoe1xuICAgICAgICAgICAgICAgICAgICB0eXBlczogcmVzcG9uc2VcbiAgICAgICAgICAgICAgICB9LCAoKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMucmV0cmlldmVTZWxlY3RlZFBvc3RzKClcbiAgICAgICAgICAgICAgICAgICAgICAgIC50aGVuKCgpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB0aGlzLnNldFN0YXRlKHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgaW5pdGlhbExvYWRpbmc6IGZhbHNlLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgICAgICAgICAgfSlcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH0pO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEdldFBvc3RzIHdyYXBwZXIsIGJ1aWxkcyB0aGUgcmVxdWVzdCBhcmd1bWVudCBiYXNlZCBzdGF0ZSBhbmQgcGFyYW1ldGVycyBwYXNzZWQvXG4gICAgICogQHBhcmFtIHtvYmplY3R9IGFyZ3MgLSBkZXNpcmVkIGFyZ3VtZW50cyAoY2FuIGJlIGVtcHR5KS5cbiAgICAgKiBAcmV0dXJucyB7UHJvbWlzZTxUPn1cbiAgICAgKi9cbiAgICBnZXRQb3N0cyhhcmdzID0ge30pIHtcbiAgICAgICAgY29uc3QgeyBzZWxlY3RlZFBvc3RJZHMgfSA9IHRoaXMucHJvcHM7XG5cbiAgICAgICAgY29uc3QgZGVmYXVsdEFyZ3MgPSB7XG4gICAgICAgICAgICBwZXJfcGFnZTogMTAsXG4gICAgICAgICAgICB0eXBlOiB0aGlzLnN0YXRlLnR5cGUsXG4gICAgICAgICAgICBzZWFyY2g6IHRoaXMuc3RhdGUuZmlsdGVyLFxuICAgICAgICB9O1xuXG4gICAgICAgIGNvbnN0IHJlcXVlc3RBcmd1bWVudHMgPSB7XG4gICAgICAgICAgICAuLi5kZWZhdWx0QXJncyxcbiAgICAgICAgICAgIC4uLmFyZ3NcbiAgICAgICAgfTtcblxuICAgICAgICByZXF1ZXN0QXJndW1lbnRzLnJlc3RCYXNlID0gdGhpcy5zdGF0ZS50eXBlc1t0aGlzLnN0YXRlLnR5cGVdLnJlc3RfYmFzZTtcblxuICAgICAgICByZXR1cm4gYXBpLmdldFBvc3RzKHJlcXVlc3RBcmd1bWVudHMpXG4gICAgICAgICAgICAudGhlbihyZXNwb25zZSA9PiB7XG4gICAgICAgICAgICAgICAgaWYgKHJlcXVlc3RBcmd1bWVudHMuc2VhcmNoKSB7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMuc2V0U3RhdGUoe1xuICAgICAgICAgICAgICAgICAgICAgICAgZmlsdGVyUG9zdHM6IHJlc3BvbnNlLmZpbHRlcigoeyBpZCB9KSA9PiBzZWxlY3RlZFBvc3RJZHMuaW5kZXhPZihpZCkgPT09IC0xKSxcbiAgICAgICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHJlc3BvbnNlO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIHRoaXMuc2V0U3RhdGUoe1xuICAgICAgICAgICAgICAgICAgICBwb3N0czogdW5pcXVlQnlJZChbLi4udGhpcy5zdGF0ZS5wb3N0cywgLi4ucmVzcG9uc2VdKSxcbiAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgIC8vIHJldHVybiByZXNwb25zZSB0byBjb250aW51ZSB0aGUgY2hhaW5cbiAgICAgICAgICAgICAgICByZXR1cm4gcmVzcG9uc2U7XG4gICAgICAgICAgICB9KTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBHZXRzIHRoZSBzZWxlY3RlZCBwb3N0cyBieSBpZCBmcm9tIHRoZSBgcG9zdHNgIHN0YXRlIG9iamVjdCBhbmQgc29ydHMgdGhlbSBieSB0aGVpciBwb3NpdGlvbiBpbiB0aGUgc2VsZWN0ZWQgYXJyYXkuXG4gICAgICogQHJldHVybnMgQXJyYXkgb2Ygb2JqZWN0cy5cbiAgICAgKi9cbiAgICBnZXRTZWxlY3RlZFBvc3RzKCkge1xuICAgICAgICBjb25zdCB7IHNlbGVjdGVkUG9zdElkcyB9ID0gdGhpcy5wcm9wcztcbiAgICAgICAgcmV0dXJuIHRoaXMuc3RhdGUucG9zdHNcbiAgICAgICAgICAgIC5maWx0ZXIoKHsgaWQgfSkgPT4gc2VsZWN0ZWRQb3N0SWRzLmluZGV4T2YoaWQpICE9PSAtMSlcbiAgICAgICAgICAgIC5zb3J0KChhLCBiKSA9PiB7XG4gICAgICAgICAgICAgICAgY29uc3QgYUluZGV4ID0gdGhpcy5wcm9wcy5zZWxlY3RlZFBvc3RJZHMuaW5kZXhPZihhLmlkKTtcbiAgICAgICAgICAgICAgICBjb25zdCBiSW5kZXggPSB0aGlzLnByb3BzLnNlbGVjdGVkUG9zdElkcy5pbmRleE9mKGIuaWQpO1xuXG4gICAgICAgICAgICAgICAgaWYgKGFJbmRleCA+IGJJbmRleCkge1xuICAgICAgICAgICAgICAgICAgICByZXR1cm4gMTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBpZiAoYUluZGV4IDwgYkluZGV4KSB7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybiAtMTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICByZXR1cm4gMDtcbiAgICAgICAgICAgIH0pO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIE1ha2VzIHRoZSBuZWNlc3NhcnkgYXBpIGNhbGxzIHRvIGZldGNoIHRoZSBzZWxlY3RlZCBwb3N0cyBhbmQgcmV0dXJucyBhIHByb21pc2UuXG4gICAgICogQHJldHVybnMgeyp9XG4gICAgICovXG4gICAgcmV0cmlldmVTZWxlY3RlZFBvc3RzKCkge1xuICAgICAgICBjb25zdCB7IHBvc3RUeXBlLCBzZWxlY3RlZFBvc3RJZHMgfSA9IHRoaXMucHJvcHM7XG4gICAgICAgIGNvbnN0IHsgdHlwZXMgfSA9IHRoaXMuc3RhdGU7XG5cbiAgICAgICAgaWYgKCBzZWxlY3RlZFBvc3RJZHMgJiYgIXNlbGVjdGVkUG9zdElkcy5sZW5ndGggPiAwICkge1xuICAgICAgICAgICAgLy8gcmV0dXJuIGEgZmFrZSBwcm9taXNlIHRoYXQgYXV0byByZXNvbHZlcy5cbiAgICAgICAgICAgIHJldHVybiBuZXcgUHJvbWlzZSgocmVzb2x2ZSkgPT4gcmVzb2x2ZSgpKTtcbiAgICAgICAgfVxuXG4gICAgICAgIHJldHVybiB0aGlzLmdldFBvc3RzKHtcbiAgICAgICAgICAgIGluY2x1ZGU6IHRoaXMucHJvcHMuc2VsZWN0ZWRQb3N0SWRzLmpvaW4oJywnKSxcbiAgICAgICAgICAgIHBlcl9wYWdlOiAxMDAsXG4gICAgICAgICAgICBwb3N0VHlwZVxuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBBZGRzIGRlc2lyZWQgcG9zdCBpZCB0byB0aGUgc2VsZWN0ZWRQb3N0SWRzIExpc3RcbiAgICAgKiBAcGFyYW0ge0ludGVnZXJ9IHBvc3RfaWRcbiAgICAgKi9cbiAgICBhZGRQb3N0KHBvc3RfaWQpIHtcbiAgICAgICAgaWYgKHRoaXMuc3RhdGUuZmlsdGVyKSB7XG4gICAgICAgICAgICBjb25zdCBwb3N0ID0gdGhpcy5zdGF0ZS5maWx0ZXJQb3N0cy5maWx0ZXIocCA9PiBwLmlkID09PSBwb3N0X2lkKTtcbiAgICAgICAgICAgIGNvbnN0IHBvc3RzID0gdW5pcXVlQnlJZChbXG4gICAgICAgICAgICAgICAgLi4udGhpcy5zdGF0ZS5wb3N0cyxcbiAgICAgICAgICAgICAgICAuLi5wb3N0XG4gICAgICAgICAgICBdKTtcblxuICAgICAgICAgICAgdGhpcy5zZXRTdGF0ZSh7XG4gICAgICAgICAgICAgICAgcG9zdHNcbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYoIHRoaXMucHJvcHMuc2VsZWN0U2luZ2xlICkge1xuICAgICAgICAgICAgdGhpcy5wcm9wcy51cGRhdGVTZWxlY3RlZFBvc3RJZHMoW3Bvc3RfaWRdKTtcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgIHRoaXMucHJvcHMudXBkYXRlU2VsZWN0ZWRQb3N0SWRzKFtcbiAgICAgICAgICAgICAgICAuLi50aGlzLnByb3BzLnNlbGVjdGVkUG9zdElkcyxcbiAgICAgICAgICAgICAgICBwb3N0X2lkXG4gICAgICAgICAgICBdKTtcbiAgICAgICAgfVxuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFJlbW92ZXMgZGVzaXJlZCBwb3N0IGlkIHRvIHRoZSBzZWxlY3RlZFBvc3RJZHMgTGlzdFxuICAgICAqIEBwYXJhbSB7SW50ZWdlcn0gcG9zdF9pZFxuICAgICAqL1xuICAgIHJlbW92ZVBvc3QocG9zdF9pZCkge1xuICAgICAgICB0aGlzLnByb3BzLnVwZGF0ZVNlbGVjdGVkUG9zdElkcyhbXG4gICAgICAgICAgICAuLi50aGlzLnByb3BzLnNlbGVjdGVkUG9zdElkc1xuICAgICAgICBdLmZpbHRlcihpZCA9PiBpZCAhPT0gcG9zdF9pZCkpO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEhhbmRsZXMgdGhlIHNlYXJjaCBib3ggaW5wdXQgdmFsdWVcbiAgICAgKiBAcGFyYW0gc3RyaW5nIHR5cGUgLSBjb21lcyBmcm9tIHRoZSBldmVudCBvYmplY3QgdGFyZ2V0LlxuICAgICAqL1xuICAgIGhhbmRsZUlucHV0RmlsdGVyQ2hhbmdlKHsgdGFyZ2V0OiB7IHZhbHVlOmZpbHRlciA9ICcnIH0gPSB7fSB9ID0ge30pIHtcbiAgICAgICAgdGhpcy5zZXRTdGF0ZSh7XG4gICAgICAgICAgICBmaWx0ZXJcbiAgICAgICAgfSwgKCkgPT4ge1xuICAgICAgICAgICAgaWYgKCFmaWx0ZXIpIHtcbiAgICAgICAgICAgICAgICAvLyByZW1vdmUgZmlsdGVyZWQgcG9zdHNcbiAgICAgICAgICAgICAgICByZXR1cm4gdGhpcy5zZXRTdGF0ZSh7IGZpbHRlcmVkUG9zdHM6IFtdLCBmaWx0ZXJpbmc6IGZhbHNlIH0pO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB0aGlzLmRvUG9zdEZpbHRlcigpO1xuICAgICAgICB9KVxuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEFjdHVhbCBhcGkgY2FsbCBmb3Igc2VhcmNoaW5nIGZvciBxdWVyeSwgdGhpcyBmdW5jdGlvbiBpcyBkZWJvdW5jZWQgaW4gY29uc3RydWN0b3IuXG4gICAgICovXG4gICAgZG9Qb3N0RmlsdGVyKCkge1xuICAgICAgICBjb25zdCB7IGZpbHRlciA9ICcnIH0gPSB0aGlzLnN0YXRlO1xuXG4gICAgICAgIGlmICghZmlsdGVyKSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLnNldFN0YXRlKHtcbiAgICAgICAgICAgIGZpbHRlcmluZzogdHJ1ZSxcbiAgICAgICAgICAgIGZpbHRlckxvYWRpbmc6IHRydWVcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy5nZXRQb3N0cygpXG4gICAgICAgICAgICAudGhlbigoKSA9PiB7XG4gICAgICAgICAgICAgICAgdGhpcy5zZXRTdGF0ZSh7XG4gICAgICAgICAgICAgICAgICAgIGZpbHRlckxvYWRpbmc6IGZhbHNlXG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9KTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBSZW5kZXJzIHRoZSBQb3N0U2VsZWN0b3IgY29tcG9uZW50LlxuICAgICAqL1xuICAgIHJlbmRlcigpIHtcbiAgICAgICAgY29uc3QgaXNGaWx0ZXJlZCA9IHRoaXMuc3RhdGUuZmlsdGVyaW5nO1xuICAgICAgICBjb25zdCBwb3N0TGlzdCA9IGlzRmlsdGVyZWQgJiYgIXRoaXMuc3RhdGUuZmlsdGVyTG9hZGluZyA/IHRoaXMuc3RhdGUuZmlsdGVyUG9zdHMgOiBbXTtcbiAgICAgICAgY29uc3QgU2VsZWN0ZWRQb3N0TGlzdCAgPSB0aGlzLmdldFNlbGVjdGVkUG9zdHMoKTtcblxuICAgICAgICBjb25zdCBhZGRJY29uID0gPEljb24gaWNvbj1cInBsdXNcIiAvPjtcbiAgICAgICAgY29uc3QgcmVtb3ZlSWNvbiA9IDxJY29uIGljb249XCJtaW51c1wiIC8+O1xuXG4gICAgICAgIHJldHVybiAoXG4gICAgICAgICAgICA8ZGl2IGNsYXNzTmFtZT1cImNvbXBvbmVudHMtYmFzZS1jb250cm9sIGNvbXBvbmVudHMtcG9zdC1zZWxlY3RvclwiPlxuICAgICAgICAgICAgICAgIDxkaXYgY2xhc3NOYW1lPVwiY29tcG9uZW50cy1iYXNlLWNvbnRyb2xfX2ZpZWxkLS1zZWxlY3RlZFwiPlxuICAgICAgICAgICAgICAgICAgICA8aDI+e19fKCdTZWFyY2ggUG9zdCcsICd2b2RpJyl9PC9oMj5cbiAgICAgICAgICAgICAgICAgICAgPEl0ZW1MaXN0XG4gICAgICAgICAgICAgICAgICAgICAgICBpdGVtcz17U2VsZWN0ZWRQb3N0TGlzdH1cbiAgICAgICAgICAgICAgICAgICAgICAgIGxvYWRpbmc9e3RoaXMuc3RhdGUuaW5pdGlhbExvYWRpbmd9XG4gICAgICAgICAgICAgICAgICAgICAgICBhY3Rpb249e3RoaXMucmVtb3ZlUG9zdH1cbiAgICAgICAgICAgICAgICAgICAgICAgIGljb249e3JlbW92ZUljb259XG4gICAgICAgICAgICAgICAgICAgIC8+XG4gICAgICAgICAgICAgICAgPC9kaXY+XG4gICAgICAgICAgICAgICAgPGRpdiBjbGFzc05hbWU9XCJjb21wb25lbnRzLWJhc2UtY29udHJvbF9fZmllbGRcIj5cbiAgICAgICAgICAgICAgICAgICAgPGxhYmVsIGh0bWxGb3I9XCJzZWFyY2hpbnB1dFwiIGNsYXNzTmFtZT1cImNvbXBvbmVudHMtYmFzZS1jb250cm9sX19sYWJlbFwiPlxuICAgICAgICAgICAgICAgICAgICAgICAgPEljb24gaWNvbj1cInNlYXJjaFwiIC8+XG4gICAgICAgICAgICAgICAgICAgIDwvbGFiZWw+XG4gICAgICAgICAgICAgICAgICAgIDxpbnB1dFxuICAgICAgICAgICAgICAgICAgICAgICAgY2xhc3NOYW1lPVwiY29tcG9uZW50cy10ZXh0LWNvbnRyb2xfX2lucHV0XCJcbiAgICAgICAgICAgICAgICAgICAgICAgIGlkPVwic2VhcmNoaW5wdXRcIlxuICAgICAgICAgICAgICAgICAgICAgICAgdHlwZT1cInNlYXJjaFwiXG4gICAgICAgICAgICAgICAgICAgICAgICBwbGFjZWhvbGRlcj17X18oJ1BsZWFzZSBlbnRlciB5b3VyIHNlYXJjaCBxdWVyeS4uLicsICd2b2RpJyl9XG4gICAgICAgICAgICAgICAgICAgICAgICB2YWx1ZT17dGhpcy5zdGF0ZS5maWx0ZXJ9XG4gICAgICAgICAgICAgICAgICAgICAgICBvbkNoYW5nZT17dGhpcy5oYW5kbGVJbnB1dEZpbHRlckNoYW5nZX1cbiAgICAgICAgICAgICAgICAgICAgLz5cbiAgICAgICAgICAgICAgICAgICAgPEl0ZW1MaXN0XG4gICAgICAgICAgICAgICAgICAgICAgICBpdGVtcz17cG9zdExpc3R9XG4gICAgICAgICAgICAgICAgICAgICAgICBsb2FkaW5nPXt0aGlzLnN0YXRlLmluaXRpYWxMb2FkaW5nfHx0aGlzLnN0YXRlLmxvYWRpbmd8fHRoaXMuc3RhdGUuZmlsdGVyTG9hZGluZ31cbiAgICAgICAgICAgICAgICAgICAgICAgIGZpbHRlcmVkPXtpc0ZpbHRlcmVkfVxuICAgICAgICAgICAgICAgICAgICAgICAgYWN0aW9uPXt0aGlzLmFkZFBvc3R9XG4gICAgICAgICAgICAgICAgICAgICAgICBpY29uPXthZGRJY29ufVxuICAgICAgICAgICAgICAgICAgICAvPlxuICAgICAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICAgICAgPC9kaXY+XG4gICAgICAgICk7XG4gICAgfVxufSIsImNvbnN0IHsgYXBpRmV0Y2ggfSA9IHdwO1xuXG4vKipcbiAqIE1ha2VzIGEgZ2V0IHJlcXVlc3QgdG8gdGhlIFBvc3RUeXBlcyBlbmRwb2ludC5cbiAqXG4gKiBAcmV0dXJucyB7UHJvbWlzZTxhbnk+fVxuICovXG5leHBvcnQgY29uc3QgZ2V0UG9zdFR5cGVzID0gKCkgPT4ge1xuICAgIHJldHVybiBhcGlGZXRjaCggeyBwYXRoOiAnL3dwL3YyL3R5cGVzJyB9ICk7XG59O1xuXG4vKipcbiAqIE1ha2VzIGEgZ2V0IHJlcXVlc3QgdG8gdGhlIGRlc2lyZWQgcG9zdCB0eXBlIGFuZCBidWlsZHMgdGhlIHF1ZXJ5IHN0cmluZyBiYXNlZCBvbiBhbiBvYmplY3QuXG4gKlxuICogQHBhcmFtIHtzdHJpbmd8Ym9vbGVhbn0gcmVzdEJhc2UgLSByZXN0IGJhc2UgZm9yIHRoZSBxdWVyeS5cbiAqIEBwYXJhbSB7b2JqZWN0fSBhcmdzXG4gKiBAcmV0dXJucyB7UHJvbWlzZTxhbnk+fVxuICovXG5leHBvcnQgY29uc3QgZ2V0UG9zdHMgPSAoeyByZXN0QmFzZSA9IGZhbHNlLCAuLi5hcmdzIH0pID0+IHtcbiAgICBjb25zdCBxdWVyeVN0cmluZyA9IE9iamVjdC5rZXlzKGFyZ3MpLm1hcChhcmcgPT4gYCR7YXJnfT0ke2FyZ3NbYXJnXX1gKS5qb2luKCcmJyk7XG5cbiAgICBsZXQgcGF0aCA9IGAvd3AvdjIvJHtyZXN0QmFzZX0/JHtxdWVyeVN0cmluZ30mX2VtYmVkYDtcbiAgICByZXR1cm4gYXBpRmV0Y2goIHsgcGF0aDogcGF0aCB9ICk7XG59O1xuXG4vKipcbiAqIE1ha2VzIGEgZ2V0IHJlcXVlc3QgdG8gdGhlIFBvc3RUeXBlIFRheG9ub21pZXMgZW5kcG9pbnQuXG4gKlxuICogQHJldHVybnMge1Byb21pc2U8YW55Pn1cbiAqL1xuZXhwb3J0IGNvbnN0IGdldFRheG9ub21pZXMgPSAoeyAuLi5hcmdzIH0pID0+IHtcbiAgICBjb25zdCBxdWVyeVN0cmluZyA9IE9iamVjdC5rZXlzKGFyZ3MpLm1hcChhcmcgPT4gYCR7YXJnfT0ke2FyZ3NbYXJnXX1gKS5qb2luKCcmJyk7XG5cbiAgICBsZXQgcGF0aCA9IGAvd3AvdjIvdGF4b25vbWllcz8ke3F1ZXJ5U3RyaW5nfSZfZW1iZWRgO1xuICAgIHJldHVybiBhcGlGZXRjaCggeyBwYXRoOiBwYXRoIH0gKTtcbn07XG5cbi8qKlxuICogTWFrZXMgYSBnZXQgcmVxdWVzdCB0byB0aGUgZGVzaXJlZCBwb3N0IHR5cGUgYW5kIGJ1aWxkcyB0aGUgcXVlcnkgc3RyaW5nIGJhc2VkIG9uIGFuIG9iamVjdC5cbiAqXG4gKiBAcGFyYW0ge3N0cmluZ3xib29sZWFufSByZXN0QmFzZSAtIHJlc3QgYmFzZSBmb3IgdGhlIHF1ZXJ5LlxuICogQHBhcmFtIHtvYmplY3R9IGFyZ3NcbiAqIEByZXR1cm5zIHtQcm9taXNlPGFueT59XG4gKi9cbmV4cG9ydCBjb25zdCBnZXRUZXJtcyA9ICh7IHJlc3RCYXNlID0gZmFsc2UsIC4uLmFyZ3MgfSkgPT4ge1xuICAgIGNvbnN0IHF1ZXJ5U3RyaW5nID0gT2JqZWN0LmtleXMoYXJncykubWFwKGFyZyA9PiBgJHthcmd9PSR7YXJnc1thcmddfWApLmpvaW4oJyYnKTtcblxuICAgIGxldCBwYXRoID0gYC93cC92Mi8ke3Jlc3RCYXNlfT8ke3F1ZXJ5U3RyaW5nfSZfZW1iZWRgO1xuICAgIHJldHVybiBhcGlGZXRjaCggeyBwYXRoOiBwYXRoIH0gKTtcbn07IiwiLyoqXG4gKiBSZXR1cm5zIGEgdW5pcXVlIGFycmF5IG9mIG9iamVjdHMgYmFzZWQgb24gYSBkZXNpcmVkIGtleS5cbiAqIEBwYXJhbSB7YXJyYXl9IGFyciAtIGFycmF5IG9mIG9iamVjdHMuXG4gKiBAcGFyYW0ge3N0cmluZ3xpbnR9IGtleSAtIGtleSB0byBmaWx0ZXIgb2JqZWN0cyBieVxuICovXG5leHBvcnQgY29uc3QgdW5pcXVlQnkgPSAoYXJyLCBrZXkpID0+IHtcbiAgICBsZXQga2V5cyA9IFtdO1xuICAgIHJldHVybiBhcnIuZmlsdGVyKGl0ZW0gPT4ge1xuICAgICAgICBpZiAoa2V5cy5pbmRleE9mKGl0ZW1ba2V5XSkgIT09IC0xKSB7XG4gICAgICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4ga2V5cy5wdXNoKGl0ZW1ba2V5XSk7XG4gICAgfSk7XG59O1xuXG4vKipcbiAqIFJldHVybnMgYSB1bmlxdWUgYXJyYXkgb2Ygb2JqZWN0cyBiYXNlZCBvbiB0aGUgaWQgcHJvcGVydHkuXG4gKiBAcGFyYW0ge2FycmF5fSBhcnIgLSBhcnJheSBvZiBvYmplY3RzIHRvIGZpbHRlci5cbiAqIEByZXR1cm5zIHsqfVxuICovXG5leHBvcnQgY29uc3QgdW5pcXVlQnlJZCA9IGFyciA9PiB1bmlxdWVCeShhcnIsICdpZCcpO1xuXG4vKipcbiAqIERlYm91bmNlIGEgZnVuY3Rpb24gYnkgbGltaXRpbmcgaG93IG9mdGVuIGl0IGNhbiBydW4uXG4gKiBAcGFyYW0ge2Z1bmN0aW9ufSBmdW5jIC0gY2FsbGJhY2sgZnVuY3Rpb25cbiAqIEBwYXJhbSB7SW50ZWdlcn0gd2FpdCAtIFRpbWUgaW4gbWlsbGlzZWNvbmRzIGhvdyBsb25nIGl0IHNob3VsZCB3YWl0LlxuICogQHJldHVybnMge0Z1bmN0aW9ufVxuICovXG5leHBvcnQgY29uc3QgZGVib3VuY2UgPSAoZnVuYywgd2FpdCkgPT4ge1xuICAgIGxldCB0aW1lb3V0ID0gbnVsbDtcblxuICAgIHJldHVybiBmdW5jdGlvbiAoKSB7XG4gICAgICAgIGNvbnN0IGNvbnRleHQgPSB0aGlzO1xuICAgICAgICBjb25zdCBhcmdzID0gYXJndW1lbnRzO1xuXG4gICAgICAgIGNvbnN0IGxhdGVyID0gKCkgPT4ge1xuICAgICAgICAgICAgZnVuYy5hcHBseShjb250ZXh0LCBhcmdzKTtcbiAgICAgICAgfTtcblxuICAgICAgICBjbGVhclRpbWVvdXQodGltZW91dCk7XG4gICAgICAgIHRpbWVvdXQgPSBzZXRUaW1lb3V0KGxhdGVyLCB3YWl0KTtcbiAgICB9XG59OyJdfQ==
