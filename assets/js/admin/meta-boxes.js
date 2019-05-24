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

    $( '.show_hide_select' ).on( 'change', function() {
        
        var shortcode_select = $(this).val(), 
            $masvideos_wp_shortcode = $(this).parents( '.options_group' );

        $masvideos_wp_shortcode.find( '.hide' ).hide();
        $masvideos_wp_shortcode.find( '.show_if_' + shortcode_select ).show();
    }).change();

    // Uploading Image
    if ( ! $('.upload_image_id').val() ) {
        $('.masvideos_remove_image_button').hide();
    }

    $(document).on( 'click', '.masvideos_upload_image_button', function( event ){
        var $this = $(this);
        var current_block = $(this).parent('.form-field').attr('id');

        if( typeof file_frame == 'undefined' ) {
            var file_frame;
        }

        event.preventDefault();

        // If the media frame already exists, reopen it.
        if ( file_frame ) {
            file_frame.open();
            return;
        }

        // Create the media frame.
        file_frame = wp.media.frames.downloadable_file = wp.media({
            title: 'Choose an image',
            button: {
                text: 'Use image',
            },
            library: {
                type: [ 'image' ],
            },
            multiple: false
        });

        // When an image is selected, run a callback.
        file_frame.on( 'select', function() {
            attachment = file_frame.state().get('selection').first().toJSON();

            $('#'+current_block+' .upload_image_id').val( attachment.id );
            $('#'+current_block+' img.upload_image_preview').attr('src', attachment.url );
            $('#'+current_block+' .masvideos_remove_image_button').show();
            $this.closest( '.widget-inside' ).find( '.widget-control-save' ).prop( 'disabled', false );
        });

        // Finally, open the modal.
        file_frame.open();
    });

    $(document).on( 'click', '.masvideos_remove_image_button', function( event ){
        var $this = $(this);
        var current_block = $(this).parent('.form-field').attr('id');

        $('#'+current_block+' img.upload_image_preview').attr('src', $('#'+current_block+' img.upload_image_preview').data('placeholder-src'));
        $('#'+current_block+' .upload_image_id').val('');
        $('#'+current_block+' .masvideos_remove_image_button').hide();
        $this.closest( '.widget-inside' ).find( '.widget-control-save' ).prop( 'disabled', false );

        return false;
    });

    // Uploading Video
    if ( ! $('.upload_video_id').val() ) {
        $('.masvideos_remove_video_button').hide();
    }

    $(document).on( 'click', '.masvideos_upload_video_button', function( event ){
        var $this = $(this);
        var current_block = $(this).parent('.form-field').attr('id');

        if( typeof file_frame == 'undefined' ) {
            var file_frame;
        }

        event.preventDefault();

        // If the media frame already exists, reopen it.
        if ( file_frame ) {
            file_frame.open();
            return;
        }

        // Create the media frame.
        file_frame = wp.media.frames.downloadable_file = wp.media({
            title: 'Choose an video',
            button: {
                text: 'Use video',
            },
            library: {
                type: [ 'video' ],
            },
            multiple: false
        });

        // When an video is selected, run a callback.
        file_frame.on( 'select', function() {
            attachment = file_frame.state().get('selection').first().toJSON();

            $('#'+current_block+' .upload_video_id').val( attachment.id );
            $('#'+current_block+' .masvideos_remove_video_button').show();
            $this.closest( '.widget-inside' ).find( '.widget-control-save' ).prop( 'disabled', false );
        });

        // Finally, open the modal.
        file_frame.open();
    });

    $(document).on( 'click', '.masvideos_remove_video_button', function( event ){
        var $this = $(this);
        var current_block = $(this).parent('.form-field').attr('id');

        $('#'+current_block+' div.wp-video').remove();
        $('#'+current_block+' .upload_video_id').val('');
        $('#'+current_block+' .masvideos_remove_video_button').hide();
        $this.closest( '.widget-inside' ).find( '.widget-control-save' ).prop( 'disabled', false );

        return false;
    });
});
