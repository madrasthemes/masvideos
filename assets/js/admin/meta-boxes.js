jQuery( function ( $ ) {

    // Run tipTip
    function runTipTip() {
        // Remove any lingering tooltips
        $( '#tiptip_holder' ).removeAttr( 'style' );
        $( '#tiptip_arrow' ).removeAttr( 'style' );
        $( '.tips' ).tipTip({
            'attribute': 'data-tip',
            'fadeIn': 50,
            'fadeOut': 50,
            'delay': 200
        });
    }

    runTipTip();

    // Allow Tabbing
    $( '#titlediv' ).find( '#title' ).keyup( function( event ) {
        var code = event.keyCode || event.which;

        // Tab key
        if ( code === '9' && $( '#masvideos-coupon-description' ).length > 0 ) {
            event.stopPropagation();
            $( '#masvideos-coupon-description' ).focus();
            return false;
        }
    });

    $( '.masvideos-metaboxes-wrapper' ).on( 'click', '.masvideos-metabox > h3', function() {
        $( this ).parent( '.masvideos-metabox' ).toggleClass( 'closed' ).toggleClass( 'open' );
    });

    // Tabbed Panels
    $( document.body ).on( 'masvideos-init-tabbed-panels', function() {
        $( 'ul.masvideos-tabs' ).show();
        $( 'ul.masvideos-tabs a' ).click( function( e ) {
            e.preventDefault();
            var panel_wrap = $( this ).closest( 'div.panel-wrap' );
            $( 'ul.masvideos-tabs li', panel_wrap ).removeClass( 'active' );
            $( this ).parent().addClass( 'active' );
            $( 'div.panel', panel_wrap ).hide();
            $( $( this ).attr( 'href' ) ).show();
        });
        $( 'div.panel-wrap' ).each( function() {
            $( this ).find( 'ul.masvideos-tabs li' ).eq( 0 ).find( 'a' ).click();
        });
    }).trigger( 'masvideos-init-tabbed-panels' );

    // Date Picker
    $( document.body ).on( 'masvideos-init-datepickers', function() {
        $( '.date-picker-field, .date-picker' ).datepicker({
            dateFormat: 'yy-mm-dd',
            numberOfMonths: 1,
            showButtonPanel: true
        });
    }).trigger( 'masvideos-init-datepickers' );

    // Meta-Boxes - Open/close
    $( '.masvideos-metaboxes-wrapper' ).on( 'click', '.masvideos-metabox h3', function( event ) {
        // If the user clicks on some form input inside the h3, like a select list (for variations), the box should not be toggled
        if ( $( event.target ).filter( ':input, option, .sort' ).length ) {
            return;
        }

        $( this ).next( '.masvideos-metabox-content' ).stop().slideToggle();
    })
    .on( 'click', '.expand_all', function() {
        $( this ).closest( '.masvideos-metaboxes-wrapper' ).find( '.masvideos-metabox > .masvideos-metabox-content' ).show();
        return false;
    })
    .on( 'click', '.close_all', function() {
        $( this ).closest( '.masvideos-metaboxes-wrapper' ).find( '.masvideos-metabox > .masvideos-metabox-content' ).hide();
        return false;
    });
    $( '.masvideos-metabox.closed' ).each( function() {
        $( this ).find( '.masvideos-metabox-content' ).hide();
    });
});
