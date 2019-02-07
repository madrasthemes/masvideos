(function( $, wpcustomize ) {

    var Vodi_Tabbed_Widget;
    var $document = $( document );
    Vodi_Tabbed_Widget = function( tabs ){

        function tab_content_change( $context ){

            $context.on( 'change', '.widget_type', function(){
                var widget = jQuery( this).val();
                jQuery( '.spinner', $context).addClass( 'is-active' );
                jQuery.ajax( {
                    data: {
                        widget: widget,
                        action: 'vodi_tabbed_get_settings_form',
                        _nonce: Vodi_Tabbed_Widget_Settings.nonce
                    },
                    url: ajaxurl,
                    type: 'post',
                    dataType: 'html',
                    success: function( settings_html ){
                        var $settings_html = $( settings_html );
                        var tab = jQuery( '.tabbed-widget-settings', $context );
                        tab.html( $settings_html );
                        jQuery( '.spinner', $context).removeClass( 'is-active' );
                        $document.trigger( 'widget-added', [ tab ] );
                        jQuery( window).trigger( 'resize' );
                    }
                } );
            } );
        }

        function update_value( nav, tab ){
            tab.on( 'change keyup', 'input, select, textarea',  function(){
                var data = jQuery( 'input, select, textarea', tab ).serialize();
                jQuery( 'input.tab-value', nav ).val( data );
                if ( jQuery('input[id*="-title"]', tab ).length > 0 ) {
                    var title = jQuery('input[id*="-title"]', tab ).val() || Vodi_Tabbed_Widget_Settings.untitled;
                    jQuery( '.vodi-tw-label', nav ).text( title );
                }
            } );

        }

        //Setup when tab element changes
        jQuery( '.vodi-tw-tab-content', tabs ).each( function(){
            var settings_div =  jQuery( this );
            tab_content_change( settings_div );
        } );

        // Set up when load
        jQuery( '.vodi-tw-nav li.vodi-tw-title', tabs ).each( function( index ){
            var tab_id = 'tab-'+index+ ( new Date().getTime() );
            jQuery( this ).attr( 'data-for', tab_id );
            var tab_wrapper =  jQuery( '.vodi-tw-tab-content', tabs ).eq( index );
            tab_wrapper.attr( 'id', tab_id );
            tab_wrapper.attr( 'data-index', index );
            var widget = tab_wrapper.find( '.widget.tabbed-widget-settings' );
            if ( widget.length > 0 ) {
                $document.trigger( 'widget-added', [ widget ] );
            }

        } );


        // Witch to current tab
        tabs.on( 'click', '.vodi-tw-nav li.vodi-tw-title', function( e ){
            e.preventDefault();
            if ( ! jQuery( '.dashicons',  jQuery( this) ).is( e.target ) && ! jQuery( '.vodi-tw-remove',  jQuery( this) ).is( e.target ) ) {
                if ( ! jQuery( this).hasClass( 'ui-state-disabled' ) ) {
                    var id = jQuery( this ).attr( 'data-for' );
                    jQuery( '.vodi-tw-tab-content', tabs ).removeClass( 'tab-active' );
                    jQuery( '#'+id+'.vodi-tw-tab-content', tabs ).addClass( 'tab-active' );
                    jQuery( '.vodi-tw-nav li', tabs ).removeClass( 'nav-active' );
                    jQuery( this ).addClass( 'nav-active' );

                    jQuery( '.vodi-tw-nav li.vodi-tw-title', tabs ).each( function( index ){

                        if ( jQuery( this ).hasClass( 'nav-active' ) ) {
                            jQuery( 'input.current_active', tabs ).val( index );
                        }
                    } );
                }
            }
        } );

        var current_active = jQuery( 'input.current_active', tabs).val();
        current_active =  parseInt( current_active );
        if( isNaN( current_active ) ) {
            current_active = 0;
        }

        if ( jQuery( '.vodi-tw-nav li.vodi-tw-title', tabs ).eq( current_active ).length ){
            jQuery( '.vodi-tw-nav li.vodi-tw-title', tabs ).eq( current_active ).trigger( 'click' );
        } else {
            jQuery( '.vodi-tw-nav li.vodi-tw-title', tabs ).eq( 0 ).trigger( 'click' );
        }

        // Set item index active
        function set_active_to_index( index ){
            if ( jQuery( '.vodi-tw-nav li.vodi-tw-title', tabs ).eq( index ).length > 0 ) {
                jQuery( '.vodi-tw-nav li.vodi-tw-title', tabs ).eq( index ).trigger( 'click' );
            } else {
                jQuery( '.vodi-tw-nav li.vodi-tw-title', tabs ).eq( 0 ).trigger( 'click' );
            }
        }

        // Setup id for tabs
        jQuery( '.vodi-tw-nav li.vodi-tw-title', tabs ).each( function( index ){

            var tab_id = 'tab-'+index+ ( new Date().getTime() );
            var li =jQuery( this );
            li.attr( 'data-for', tab_id );
            var tab = jQuery( '.vodi-tw-tab-content', tabs ).eq( index );
            tab.attr( 'id', tab_id );
            tab.attr( 'data-index', index );

            update_value( li, tab );

            var data = jQuery( 'input, select, textarea', tab ).serialize();
            jQuery( 'input.tab-value', li ).val( data );
        } );

        // Sort tabs
        jQuery( ".vodi-tw-nav", tabs ).sortable( {
            containment: "parent",
            items: "li:not(.ui-state-disabled)",
            change: function( event, ui ) {
                // Trigger change to show save button in customizer
                var base_id =  new Date().getTime();
                $( '.base_tab_id', tabs ).val( base_id ).trigger( 'change' );
            }
        });

        // Remove tab
        tabs.on( 'click', '.vodi-tw-nav li .vodi-tw-remove', function( e ){
            e.preventDefault();
            var parent = jQuery( this).parent();
            if ( ! parent.hasClass( 'ui-state-disabled' ) ) {
                var id = parent.attr( 'data-for' );
                jQuery( '#'+id+'.vodi-tw-tab-content', tabs ).remove();
                parent.remove();
                var l = jQuery( '.vodi-tw-nav li.vodi-tw-title', tabs ).length;
                set_active_to_index( 0 );
                jQuery( window).trigger( 'resize' );
                if ( l <= 0 ){
                    jQuery( '.no-tabs', tabs).show();
                }

                // Trigger change to show save button in customizer
                var base_id =  new Date().getTime();
                $( '.base_tab_id', tabs ).val( base_id ).trigger( 'change' );
            }
        } );

        // Add new tab
        tabs.on( 'click', '.add-new-tab', function( e ){
            e.preventDefault();
            var index = Math.floor( ( Math.random() * 100 ) + 1 );
            var tab_id = 'tab-'+index+ ( new Date().getTime() );

            console.log( 'addnew' );

            var new_li = jQuery( jQuery( '.title-tpl', tabs).html() );
            new_li.attr( 'data-for', tab_id );
            jQuery( '.vodi-tw-nav', tabs).append( new_li );

            var tab_content = jQuery(  jQuery( '.settings-tpl', tabs).html() );
            tab_content.attr( 'id', tab_id );
            jQuery( '.vodi-tw-tab-contents', tabs).append( tab_content );
            jQuery( window).trigger( 'resize' );
            tab_content_change( tab_content );
            update_value( new_li, tab_content );
            new_li.trigger( 'click' );

            jQuery( '.no-tabs', tabs).hide();

        } );

    };

    if ( ! wpcustomize ) {

        $document.ready( function(){

            var widgetContainers = $('.widgets-holder-wrap:not(#available-widgets)').find('div.widget');
            widgetContainers.one('click.toggle-widget-expanded', function toggleWidgetExpanded() {
                var widgetContainer = $(this);
                new Vodi_Tabbed_Widget( $( '.vodi-tw-tabs', widgetContainer ) );
            });

            jQuery( document ).on( 'widget-added widget-updated', function( event, widget ){
                new Vodi_Tabbed_Widget( $( '.vodi-tw-tabs', widget ) );
            } );
        } );

        // When siteorigin page builder added widget
        $document.on('panelsopen', function (e) {
            var widget = $(e.target);
            new Vodi_Tabbed_Widget( $( '.vodi-tw-tabs', widget ) );
        });

    } else {
        wpcustomize.bind( 'ready', function( e, b ) {

            var widgetContainers = $('.widgets-holder-wrap:not(#available-widgets)').find('div.widget');
            widgetContainers.one('click.toggle-widget-expanded', function toggleWidgetExpanded() {
                var widgetContainer = $(this);
                new Vodi_Tabbed_Widget(  $( '.vodi-tw-tabs', widgetContainer ) );
            });

            jQuery( document ).on( 'widget-added widget-updated', function( event, widget ){
                new Vodi_Tabbed_Widget( $( '.vodi-tw-tabs', widget ) );
            } );
        } );
    }

})( jQuery, wp.customize || null );