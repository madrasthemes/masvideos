/*global masvideos_admin_meta_boxes */
jQuery( function( $ ) {

    // Scroll to first checked category - https://github.com/scribu/wp-category-checklist-tree/blob/d1c3c1f449e1144542efa17dde84a9f52ade1739/category-checklist-tree.php
    $( function() {
        $( '[id$="-all"] > ul.categorychecklist' ).each( function() {
            var $list = $( this );
            var $firstChecked = $list.find( ':checked' ).first();

            if ( ! $firstChecked.length ) {
                return;
            }

            var pos_first   = $list.find( 'input' ).position().top;
            var pos_checked = $firstChecked.position().top;

            $list.closest( '.tabs-panel' ).scrollTop( pos_checked - pos_first + 5 );
        });
    });

    // Prevent enter submitting post form.
    $( '#upsell_tv_show_data' ).bind( 'keypress', function( e ) {
        if ( e.keyCode === 13 ) {
            return false;
        }
    });

    // Type box.
    $( '.type_box' ).appendTo( '#masvideos-tv-show-data .hndle span' );

    // Date picker fields.
    function date_picker_select( datepicker ) {
        var option         = $( datepicker ).next().is( '.hasDatepicker' ) ? 'minDate' : 'maxDate',
            otherDateField = 'minDate' === option ? $( datepicker ).next() : $( datepicker ).prev(),
            date           = $( datepicker ).datepicker( 'getDate' );

        $( otherDateField ).datepicker( 'option', option, date );
        $( datepicker ).change();
    }

    $( '._tv_show_release_date_field' ).each( function() {
        $( this ).find( 'input' ).datepicker({
            defaultDate: '',
            dateFormat: 'yy-mm-dd',
            numberOfMonths: 1,
            showButtonPanel: true,
            onSelect: function() {
                date_picker_select( $( this ) );
            }
        });
        $( this ).find( 'input' ).each( function() { date_picker_select( $( this ) ); } );
    });

    // Season Tables.

    // Initial order.
    var masvideos_season_items = $( '.tv_show_seasons' ).find( '.masvideos_season' ).get();

    masvideos_season_items.sort( function( a, b ) {
       var compA = parseInt( $( a ).attr( 'rel' ), 10 );
       var compB = parseInt( $( b ).attr( 'rel' ), 10 );
       return ( compA < compB ) ? -1 : ( compA > compB ) ? 1 : 0;
    });
    $( masvideos_season_items ).each( function( index, el ) {
        $( '.tv_show_seasons' ).append( el );
    });

    function tv_show_season_row_indexes() {
        $( '.tv_show_seasons .masvideos_season' ).each( function( index, el ) {
            $( '.season_position', el ).val( parseInt( $( el ).index( '.tv_show_seasons .masvideos_season' ), 10 ) );
        });
    }

    // Add a new season (via ajax).
    $( 'button.add_tv_show_season' ).on( 'click', function() {
        var size         = $( '.tv_show_seasons .masvideos_season' ).length;
        var $wrapper     = $( this ).closest( '#tv_show_seasons' );
        var $seasons     = $wrapper.find( '.tv_show_seasons' );
        var data         = {
            action:   'masvideos_add_season_tv_show',
            i:        size,
            security: masvideos_admin_meta_boxes.add_season_tv_show_nonce
        };

        // $wrapper.block({
        //     message: null,
        //     overlayCSS: {
        //         background: '#fff',
        //         opacity: 0.6
        //     }
        // });

        $.post( masvideos_admin_meta_boxes.ajax_url, data, function( response ) {
            $seasons.append( response );

            $( document.body ).trigger( 'masvideos-enhanced-select-init' );
            tv_show_season_row_indexes();
            // $wrapper.unblock();

            $( document.body ).trigger( 'masvideos_added_season_tv_show' );
        });

        return false;
    });

    $( '.tv_show_seasons' ).on( 'blur', 'input.season_name', function() {
        $( this ).closest( '.masvideos_season' ).find( 'strong.season_name' ).text( $( this ).val() );
    });

    $( '.tv_show_seasons' ).on( 'click', '.remove_row', function() {
        if ( window.confirm( masvideos_admin_meta_boxes.remove_season ) ) {
            var $parent = $( this ).parent().parent();

            $parent.find( 'select, input[type=text]' ).val( '' );
            $parent.hide();
            tv_show_season_row_indexes();
        }
        return false;
    });

    // Season ordering.
    $( '.tv_show_seasons' ).sortable({
        items: '.masvideos_season',
        cursor: 'move',
        axis: 'y',
        handle: 'h3',
        scrollSensitivity: 40,
        forcePlaceholderSize: true,
        helper: 'clone',
        opacity: 0.65,
        placeholder: 'masvideos-metabox-sortable-placeholder',
        start: function( event, ui ) {
            ui.item.css( 'background-color', '#f6f6f6' );
        },
        stop: function( event, ui ) {
            ui.item.removeAttr( 'style' );
            tv_show_season_row_indexes();
        }
    });

    // Save seasons and update variations.
    $( '.save_seasons_tv_show' ).on( 'click', function() {

        // $( '#masvideos-tv-show-data' ).block({
        //     message: null,
        //     overlayCSS: {
        //         background: '#fff',
        //         opacity: 0.6
        //     }
        // });

        var data = {
            post_id     : masvideos_admin_meta_boxes.post_id,
            data        : $( '.tv_show_seasons' ).find( 'input, select, textarea' ).serialize(),
            action      : 'masvideos_save_seasons_tv_show',
            security    : masvideos_admin_meta_boxes.save_seasons_tv_show_nonce
        };

        $.post( masvideos_admin_meta_boxes.ajax_url, data, function() {
            // Reload variations panel.
            var this_page = window.location.toString();
            this_page = this_page.replace( 'post-new.php?', 'post.php?post=' + masvideos_admin_meta_boxes.post_id + '&action=edit&' );
        });
    });

    // Product gallery file uploads.
    var tv_show_gallery_frame;
    var $image_gallery_ids = $( '#tv_show_image_gallery' );
    var $tv_show_images    = $( '#tv_show_images_container' ).find( 'ul.tv_show_images' );

    $( '.add_tv_show_images' ).on( 'click', 'a', function( event ) {
        var $el = $( this );

        event.preventDefault();

        // If the media frame already exists, reopen it.
        if ( tv_show_gallery_frame ) {
            tv_show_gallery_frame.open();
            return;
        }

        // Create the media frame.
        tv_show_gallery_frame = wp.media.frames.tv_show_gallery = wp.media({
            // Set the title of the modal.
            title: $el.data( 'choose' ),
            button: {
                text: $el.data( 'update' )
            },
            states: [
                new wp.media.controller.Library({
                    title: $el.data( 'choose' ),
                    filterable: 'all',
                    multiple: true
                })
            ]
        });

        // When an image is selected, run a callback.
        tv_show_gallery_frame.on( 'select', function() {
            var selection = tv_show_gallery_frame.state().get( 'selection' );
            var attachment_ids = $image_gallery_ids.val();

            selection.map( function( attachment ) {
                attachment = attachment.toJSON();

                if ( attachment.id ) {
                    attachment_ids   = attachment_ids ? attachment_ids + ',' + attachment.id : attachment.id;
                    var attachment_image = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;

                    $tv_show_images.append( '<li class="image" data-attachment_id="' + attachment.id + '"><img src="' + attachment_image + '" /><ul class="actions"><li><a href="#" class="delete" title="' + $el.data('delete') + '">' + $el.data('text') + '</a></li></ul></li>' );
                }
            });

            $image_gallery_ids.val( attachment_ids );
        });

        // Finally, open the modal.
        tv_show_gallery_frame.open();
    });

    // Image ordering.
    $tv_show_images.sortable({
        items: 'li.image',
        cursor: 'move',
        scrollSensitivity: 40,
        forcePlaceholderSize: true,
        forceHelperSize: false,
        helper: 'clone',
        opacity: 0.65,
        placeholder: 'masvideos-metabox-sortable-placeholder',
        start: function( event, ui ) {
            ui.item.css( 'background-color', '#f6f6f6' );
        },
        stop: function( event, ui ) {
            ui.item.removeAttr( 'style' );
        },
        update: function() {
            var attachment_ids = '';

            $( '#tv_show_images_container' ).find( 'ul li.image' ).css( 'cursor', 'default' ).each( function() {
                var attachment_id = $( this ).attr( 'data-attachment_id' );
                attachment_ids = attachment_ids + attachment_id + ',';
            });

            $image_gallery_ids.val( attachment_ids );
        }
    });

    // Remove images.
    $( '#tv_show_images_container' ).on( 'click', 'a.delete', function() {
        $( this ).closest( 'li.image' ).remove();

        var attachment_ids = '';

        $( '#tv_show_images_container' ).find( 'ul li.image' ).css( 'cursor', 'default' ).each( function() {
            var attachment_id = $( this ).attr( 'data-attachment_id' );
            attachment_ids = attachment_ids + attachment_id + ',';
        });

        $image_gallery_ids.val( attachment_ids );

        // Remove any lingering tooltips.
        $( '#tiptip_holder' ).removeAttr( 'style' );
        $( '#tiptip_arrow' ).removeAttr( 'style' );

        return false;
    });
});
