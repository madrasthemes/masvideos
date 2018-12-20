"use strict";

var registerBlockType = wp.blocks.registerBlockType;
var InspectorControls = wp.editor.InspectorControls;
var _wp$components = wp.components,
    ServerSideRender = _wp$components.ServerSideRender,
    TextControl = _wp$components.TextControl,
    RangeControl = _wp$components.RangeControl,
    SelectControl = _wp$components.SelectControl,
    CheckboxControl = _wp$components.CheckboxControl;
registerBlockType('masvideos/videos', {
  title: 'Videos Block',
  icon: 'megaphone',
  category: 'widgets',
  edit: function edit(props) {
    var attributes = props.attributes,
        className = props.className,
        setAttributes = props.setAttributes;
    var limit = attributes.limit,
        columns = attributes.columns,
        orderby = attributes.orderby,
        order = attributes.order,
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
    }), wp.element.createElement(CheckboxControl, {
      label: "Featured",
      help: "Check to select featured videos.",
      checked: featured,
      onChange: onChangeFeatured
    }), wp.element.createElement(CheckboxControl, {
      label: "Top Rated",
      help: "Check to select top rated videos.",
      checked: top_rated,
      onChange: onChangeTopRated
    })), wp.element.createElement(ServerSideRender, {
      block: "masvideos/videos",
      attributes: attributes
    })];
  },
  save: function save() {
    // Rendering in PHP
    return null;
  }
});
