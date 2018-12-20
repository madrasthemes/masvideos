(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
"use strict";

var registerBlockType = wp.blocks.registerBlockType;
var InspectorControls = wp.editor.InspectorControls;
var _wp$components = wp.components,
    ServerSideRender = _wp$components.ServerSideRender,
    TextControl = _wp$components.TextControl,
    RangeControl = _wp$components.RangeControl,
    SelectControl = _wp$components.SelectControl,
    CheckboxControl = _wp$components.CheckboxControl; // const { select } = wp.data;
// const { addQueryArgs } = wp.url;

registerBlockType('masvideos/movies', {
  title: 'Movies Block',
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
        ids = attributes.ids,
        featured = attributes.featured,
        top_rated = attributes.top_rated; // const { getEntity, getEntityRecords } = select( 'core' );
    // let query = {
    //     search: "2",
    //     per_page: -1,
    // };
    // const movies = getEntityRecords( 'postType', 'movie', query );
    // const categories = getEntityRecords( 'taxonomy', 'movie_cat', query );
    // console.log( movies );
    // console.log( categories );
    // const movies = wp.apiFetch( {
    //     path: addQueryArgs( '/wp/v2/movie', {
    //         search: "2",
    //         per_page: -1,
    //     } ),
    // } );
    // console.log( movies );
    // const getPostTypes = wp.apiFetch( {
    //     path: '/wp/v2/types',
    // } );
    // console.log( getPostTypes );

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

},{}]},{},[1])
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJhc3NldHMvZXNuZXh0L2Jsb2Nrcy9tb3ZpZXMuanMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7OztJQ0FRLGlCLEdBQXNCLEVBQUUsQ0FBQyxNLENBQXpCLGlCO0lBQ0EsaUIsR0FBc0IsRUFBRSxDQUFDLE0sQ0FBekIsaUI7cUJBQ2dGLEVBQUUsQ0FBQyxVO0lBQW5GLGdCLGtCQUFBLGdCO0lBQWtCLFcsa0JBQUEsVztJQUFhLFksa0JBQUEsWTtJQUFjLGEsa0JBQUEsYTtJQUFlLGUsa0JBQUEsZSxFQUNwRTtBQUNBOztBQUVBLGlCQUFpQixDQUFFLGtCQUFGLEVBQXNCO0FBQ25DLEVBQUEsS0FBSyxFQUFFLGNBRDRCO0FBR25DLEVBQUEsSUFBSSxFQUFFLFdBSDZCO0FBS25DLEVBQUEsUUFBUSxFQUFFLFNBTHlCO0FBT25DLEVBQUEsSUFBSSxFQUFJLGNBQUUsS0FBRixFQUFhO0FBQUEsUUFDVCxVQURTLEdBQ2dDLEtBRGhDLENBQ1QsVUFEUztBQUFBLFFBQ0csU0FESCxHQUNnQyxLQURoQyxDQUNHLFNBREg7QUFBQSxRQUNjLGFBRGQsR0FDZ0MsS0FEaEMsQ0FDYyxhQURkO0FBQUEsUUFFVCxLQUZTLEdBRW9ELFVBRnBELENBRVQsS0FGUztBQUFBLFFBRUYsT0FGRSxHQUVvRCxVQUZwRCxDQUVGLE9BRkU7QUFBQSxRQUVPLE9BRlAsR0FFb0QsVUFGcEQsQ0FFTyxPQUZQO0FBQUEsUUFFZ0IsS0FGaEIsR0FFb0QsVUFGcEQsQ0FFZ0IsS0FGaEI7QUFBQSxRQUV1QixHQUZ2QixHQUVvRCxVQUZwRCxDQUV1QixHQUZ2QjtBQUFBLFFBRTRCLFFBRjVCLEdBRW9ELFVBRnBELENBRTRCLFFBRjVCO0FBQUEsUUFFc0MsU0FGdEMsR0FFb0QsVUFGcEQsQ0FFc0MsU0FGdEMsRUFHakI7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBRUE7QUFDQTtBQUNB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QyxlQUFEO0FBQ0ksTUFBQSxLQUFLLEVBQUMsV0FEVjtBQUVJLE1BQUEsSUFBSSxFQUFDLG1DQUZUO0FBR0ksTUFBQSxPQUFPLEVBQUcsU0FIZDtBQUlJLE1BQUEsUUFBUSxFQUFHO0FBSmYsTUF6Q0osQ0FERyxFQWlESCx5QkFBQyxnQkFBRDtBQUNJLE1BQUEsS0FBSyxFQUFDLGtCQURWO0FBRUksTUFBQSxVQUFVLEVBQUc7QUFGakIsTUFqREcsQ0FBUDtBQXNESCxHQS9Ha0M7QUFpSG5DLEVBQUEsSUFqSG1DLGtCQWlINUI7QUFDSDtBQUNBLFdBQU8sSUFBUDtBQUNIO0FBcEhrQyxDQUF0QixDQUFqQiIsImZpbGUiOiJnZW5lcmF0ZWQuanMiLCJzb3VyY2VSb290IjoiIiwic291cmNlc0NvbnRlbnQiOlsiKGZ1bmN0aW9uKCl7ZnVuY3Rpb24gcihlLG4sdCl7ZnVuY3Rpb24gbyhpLGYpe2lmKCFuW2ldKXtpZighZVtpXSl7dmFyIGM9XCJmdW5jdGlvblwiPT10eXBlb2YgcmVxdWlyZSYmcmVxdWlyZTtpZighZiYmYylyZXR1cm4gYyhpLCEwKTtpZih1KXJldHVybiB1KGksITApO3ZhciBhPW5ldyBFcnJvcihcIkNhbm5vdCBmaW5kIG1vZHVsZSAnXCIraStcIidcIik7dGhyb3cgYS5jb2RlPVwiTU9EVUxFX05PVF9GT1VORFwiLGF9dmFyIHA9bltpXT17ZXhwb3J0czp7fX07ZVtpXVswXS5jYWxsKHAuZXhwb3J0cyxmdW5jdGlvbihyKXt2YXIgbj1lW2ldWzFdW3JdO3JldHVybiBvKG58fHIpfSxwLHAuZXhwb3J0cyxyLGUsbix0KX1yZXR1cm4gbltpXS5leHBvcnRzfWZvcih2YXIgdT1cImZ1bmN0aW9uXCI9PXR5cGVvZiByZXF1aXJlJiZyZXF1aXJlLGk9MDtpPHQubGVuZ3RoO2krKylvKHRbaV0pO3JldHVybiBvfXJldHVybiByfSkoKSIsImNvbnN0IHsgcmVnaXN0ZXJCbG9ja1R5cGUgfSA9IHdwLmJsb2NrcztcbmNvbnN0IHsgSW5zcGVjdG9yQ29udHJvbHMgfSA9IHdwLmVkaXRvcjtcbmNvbnN0IHsgU2VydmVyU2lkZVJlbmRlciwgVGV4dENvbnRyb2wsIFJhbmdlQ29udHJvbCwgU2VsZWN0Q29udHJvbCwgQ2hlY2tib3hDb250cm9sIH0gPSB3cC5jb21wb25lbnRzO1xuLy8gY29uc3QgeyBzZWxlY3QgfSA9IHdwLmRhdGE7XG4vLyBjb25zdCB7IGFkZFF1ZXJ5QXJncyB9ID0gd3AudXJsO1xuXG5yZWdpc3RlckJsb2NrVHlwZSggJ21hc3ZpZGVvcy9tb3ZpZXMnLCB7XG4gICAgdGl0bGU6ICdNb3ZpZXMgQmxvY2snLFxuXG4gICAgaWNvbjogJ21lZ2FwaG9uZScsXG5cbiAgICBjYXRlZ29yeTogJ3dpZGdldHMnLFxuXG4gICAgZWRpdDogKCAoIHByb3BzICkgPT4ge1xuICAgICAgICBjb25zdCB7IGF0dHJpYnV0ZXMsIGNsYXNzTmFtZSwgc2V0QXR0cmlidXRlcyB9ID0gcHJvcHM7XG4gICAgICAgIGNvbnN0IHsgbGltaXQsIGNvbHVtbnMsIG9yZGVyYnksIG9yZGVyLCBpZHMsIGZlYXR1cmVkLCB0b3BfcmF0ZWQgfSA9IGF0dHJpYnV0ZXM7XG4gICAgICAgIC8vIGNvbnN0IHsgZ2V0RW50aXR5LCBnZXRFbnRpdHlSZWNvcmRzIH0gPSBzZWxlY3QoICdjb3JlJyApO1xuICAgICAgICAvLyBsZXQgcXVlcnkgPSB7XG4gICAgICAgIC8vICAgICBzZWFyY2g6IFwiMlwiLFxuICAgICAgICAvLyAgICAgcGVyX3BhZ2U6IC0xLFxuICAgICAgICAvLyB9O1xuICAgICAgICAvLyBjb25zdCBtb3ZpZXMgPSBnZXRFbnRpdHlSZWNvcmRzKCAncG9zdFR5cGUnLCAnbW92aWUnLCBxdWVyeSApO1xuICAgICAgICAvLyBjb25zdCBjYXRlZ29yaWVzID0gZ2V0RW50aXR5UmVjb3JkcyggJ3RheG9ub215JywgJ21vdmllX2NhdCcsIHF1ZXJ5ICk7XG4gICAgICAgIC8vIGNvbnNvbGUubG9nKCBtb3ZpZXMgKTtcbiAgICAgICAgLy8gY29uc29sZS5sb2coIGNhdGVnb3JpZXMgKTtcblxuICAgICAgICAvLyBjb25zdCBtb3ZpZXMgPSB3cC5hcGlGZXRjaCgge1xuICAgICAgICAvLyAgICAgcGF0aDogYWRkUXVlcnlBcmdzKCAnL3dwL3YyL21vdmllJywge1xuICAgICAgICAvLyAgICAgICAgIHNlYXJjaDogXCIyXCIsXG4gICAgICAgIC8vICAgICAgICAgcGVyX3BhZ2U6IC0xLFxuICAgICAgICAvLyAgICAgfSApLFxuICAgICAgICAvLyB9ICk7XG4gICAgICAgIC8vIGNvbnNvbGUubG9nKCBtb3ZpZXMgKTtcbiAgICAgICAgXG4gICAgICAgIC8vIGNvbnN0IGdldFBvc3RUeXBlcyA9IHdwLmFwaUZldGNoKCB7XG4gICAgICAgIC8vICAgICBwYXRoOiAnL3dwL3YyL3R5cGVzJyxcbiAgICAgICAgLy8gfSApO1xuICAgICAgICAvLyBjb25zb2xlLmxvZyggZ2V0UG9zdFR5cGVzICk7XG5cbiAgICAgICAgY29uc3Qgb25DaGFuZ2VMaW1pdCA9IG5ld0xpbWl0ID0+IHtcbiAgICAgICAgICAgIHNldEF0dHJpYnV0ZXMoIHsgbGltaXQ6IG5ld0xpbWl0IH0gKTtcbiAgICAgICAgfTtcblxuICAgICAgICBjb25zdCBvbkNoYW5nZUNvbHVtbnMgPSBuZXdDb2x1bW5zID0+IHtcbiAgICAgICAgICAgIHNldEF0dHJpYnV0ZXMoIHsgY29sdW1uczogbmV3Q29sdW1ucyB9ICk7XG4gICAgICAgIH07XG5cbiAgICAgICAgY29uc3Qgb25DaGFuZ2VPcmRlcmJ5ID0gbmV3T3JkZXJieSA9PiB7XG4gICAgICAgICAgICBzZXRBdHRyaWJ1dGVzKCB7IG9yZGVyYnk6IG5ld09yZGVyYnkgfSApO1xuICAgICAgICB9O1xuXG4gICAgICAgIGNvbnN0IG9uQ2hhbmdlT3JkZXIgPSBuZXdPcmRlciA9PiB7XG4gICAgICAgICAgICBzZXRBdHRyaWJ1dGVzKCB7IG9yZGVyOiBuZXdPcmRlciB9ICk7XG4gICAgICAgIH07XG5cbiAgICAgICAgY29uc3Qgb25DaGFuZ2VGZWF0dXJlZCA9IG5ld0ZlYXR1cmVkID0+IHtcbiAgICAgICAgICAgIHNldEF0dHJpYnV0ZXMoIHsgZmVhdHVyZWQ6IG5ld0ZlYXR1cmVkIH0gKTtcbiAgICAgICAgfTtcblxuICAgICAgICBjb25zdCBvbkNoYW5nZVRvcFJhdGVkID0gbmV3VG9wUmF0ZWQgPT4ge1xuICAgICAgICAgICAgc2V0QXR0cmlidXRlcyggeyB0b3BfcmF0ZWQ6IG5ld1RvcFJhdGVkIH0gKTtcbiAgICAgICAgfTtcblxuICAgICAgICByZXR1cm4gW1xuICAgICAgICAgICAgPEluc3BlY3RvckNvbnRyb2xzPlxuICAgICAgICAgICAgICAgIDxSYW5nZUNvbnRyb2xcbiAgICAgICAgICAgICAgICAgICAgbGFiZWw9XCJMaW1pdFwiXG4gICAgICAgICAgICAgICAgICAgIHZhbHVlPXsgbGltaXQgfVxuICAgICAgICAgICAgICAgICAgICBvbkNoYW5nZT17IG9uQ2hhbmdlTGltaXQgfVxuICAgICAgICAgICAgICAgICAgICBtaW49eyAxIH1cbiAgICAgICAgICAgICAgICAgICAgbWF4PXsgNTAgfVxuICAgICAgICAgICAgICAgIC8+XG4gICAgICAgICAgICAgICAgPFJhbmdlQ29udHJvbFxuICAgICAgICAgICAgICAgICAgICBsYWJlbD1cIkNvbHVtbnNcIlxuICAgICAgICAgICAgICAgICAgICB2YWx1ZT17IGNvbHVtbnMgfVxuICAgICAgICAgICAgICAgICAgICBvbkNoYW5nZT17IG9uQ2hhbmdlQ29sdW1ucyB9XG4gICAgICAgICAgICAgICAgICAgIG1pbj17IDEgfVxuICAgICAgICAgICAgICAgICAgICBtYXg9eyAxMCB9XG4gICAgICAgICAgICAgICAgLz5cbiAgICAgICAgICAgICAgICA8U2VsZWN0Q29udHJvbFxuICAgICAgICAgICAgICAgICAgICBsYWJlbD1cIk9yZGVyYnlcIlxuICAgICAgICAgICAgICAgICAgICB2YWx1ZT17IG9yZGVyYnkgfVxuICAgICAgICAgICAgICAgICAgICBvcHRpb25zPXsgW1xuICAgICAgICAgICAgICAgICAgICAgICAgeyBsYWJlbDogJ1RpdGxlJywgdmFsdWU6ICd0aXRsZScgfSxcbiAgICAgICAgICAgICAgICAgICAgICAgIHsgbGFiZWw6ICdEYXRlJywgdmFsdWU6ICdkYXRlJyB9LFxuICAgICAgICAgICAgICAgICAgICAgICAgeyBsYWJlbDogJ0lEJywgdmFsdWU6ICdpZCcgfSxcbiAgICAgICAgICAgICAgICAgICAgICAgIHsgbGFiZWw6ICdSYW5kb20nLCB2YWx1ZTogJ3JhbmQnIH0sXG4gICAgICAgICAgICAgICAgICAgIF0gfVxuICAgICAgICAgICAgICAgICAgICBvbkNoYW5nZT17IG9uQ2hhbmdlT3JkZXJieSB9XG4gICAgICAgICAgICAgICAgLz5cbiAgICAgICAgICAgICAgICA8U2VsZWN0Q29udHJvbFxuICAgICAgICAgICAgICAgICAgICBsYWJlbD1cIk9yZGVyXCJcbiAgICAgICAgICAgICAgICAgICAgdmFsdWU9eyBvcmRlciB9XG4gICAgICAgICAgICAgICAgICAgIG9wdGlvbnM9eyBbXG4gICAgICAgICAgICAgICAgICAgICAgICB7IGxhYmVsOiAnQVNDJywgdmFsdWU6ICdBU0MnIH0sXG4gICAgICAgICAgICAgICAgICAgICAgICB7IGxhYmVsOiAnREVTQycsIHZhbHVlOiAnREVTQycgfSxcbiAgICAgICAgICAgICAgICAgICAgXSB9XG4gICAgICAgICAgICAgICAgICAgIG9uQ2hhbmdlPXsgb25DaGFuZ2VPcmRlciB9XG4gICAgICAgICAgICAgICAgLz5cbiAgICAgICAgICAgICAgICA8Q2hlY2tib3hDb250cm9sXG4gICAgICAgICAgICAgICAgICAgIGxhYmVsPVwiRmVhdHVyZWRcIlxuICAgICAgICAgICAgICAgICAgICBoZWxwPVwiQ2hlY2sgdG8gc2VsZWN0IGZlYXR1cmVkIG1vdmllcy5cIlxuICAgICAgICAgICAgICAgICAgICBjaGVja2VkPXsgZmVhdHVyZWQgfVxuICAgICAgICAgICAgICAgICAgICBvbkNoYW5nZT17IG9uQ2hhbmdlRmVhdHVyZWQgfVxuICAgICAgICAgICAgICAgIC8+XG4gICAgICAgICAgICAgICAgPENoZWNrYm94Q29udHJvbFxuICAgICAgICAgICAgICAgICAgICBsYWJlbD1cIlRvcCBSYXRlZFwiXG4gICAgICAgICAgICAgICAgICAgIGhlbHA9XCJDaGVjayB0byBzZWxlY3QgdG9wIHJhdGVkIG1vdmllcy5cIlxuICAgICAgICAgICAgICAgICAgICBjaGVja2VkPXsgdG9wX3JhdGVkIH1cbiAgICAgICAgICAgICAgICAgICAgb25DaGFuZ2U9eyBvbkNoYW5nZVRvcFJhdGVkIH1cbiAgICAgICAgICAgICAgICAvPlxuICAgICAgICAgICAgPC9JbnNwZWN0b3JDb250cm9scz4sXG4gICAgICAgICAgICA8U2VydmVyU2lkZVJlbmRlclxuICAgICAgICAgICAgICAgIGJsb2NrPVwibWFzdmlkZW9zL21vdmllc1wiXG4gICAgICAgICAgICAgICAgYXR0cmlidXRlcz17IGF0dHJpYnV0ZXMgfVxuICAgICAgICAgICAgLz5cbiAgICAgICAgXTtcbiAgICB9ICksXG5cbiAgICBzYXZlKCkge1xuICAgICAgICAvLyBSZW5kZXJpbmcgaW4gUEhQXG4gICAgICAgIHJldHVybiBudWxsO1xuICAgIH0sXG59ICk7Il19
