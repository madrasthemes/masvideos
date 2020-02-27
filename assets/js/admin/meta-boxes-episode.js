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
    $( '#upsell_episode_data' ).bind( 'keypress', function( e ) {
        if ( e.keyCode === 13 ) {
            return false;
        }
    });

    // Type box.
    $( '.type_box' ).appendTo( '#masvideos-episode-data .handle span' );

    // Date picker fields.
    function date_picker_select( datepicker ) {
        var option         = $( datepicker ).next().is( '.hasDatepicker' ) ? 'minDate' : 'maxDate',
            otherDateField = 'minDate' === option ? $( datepicker ).next() : $( datepicker ).prev(),
            date           = $( datepicker ).datepicker( 'getDate' );

        $( otherDateField ).datepicker( 'option', option, date );
        $( datepicker ).change();
    }

    $( '.episode_date_picker' ).each( function() {
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

    // Source Tables.

    // Initial order.
    var masvideos_source_items = $( '.episode_sources' ).find( '.masvideos_source' ).get();

    masvideos_source_items.sort( function( a, b ) {
       var compA = parseInt( $( a ).attr( 'rel' ), 10 );
       var compB = parseInt( $( b ).attr( 'rel' ), 10 );
       return ( compA < compB ) ? -1 : ( compA > compB ) ? 1 : 0;
    });
    $( masvideos_source_items ).each( function( index, el ) {
        $( '.episode_sources' ).append( el );
    });

    function episode_source_row_indexes() {
        $( '.episode_sources .masvideos_source' ).each( function( index, el ) {
            $( '.source_position', el ).val( parseInt( $( el ).index( '.episode_sources .masvideos_source' ), 10 ) );
        });
    }

    // Add a new source (via ajax).
    $( 'button.add_episode_source' ).on( 'click', function() {
        var size         = $( '.episode_sources .masvideos_source' ).length;
        var $wrapper     = $( this ).closest( '#episode_sources' );
        var $sources     = $wrapper.find( '.episode_sources' );
        var data         = {
            action:   'masvideos_add_source_episode',
            i:        size,
            security: masvideos_admin_meta_boxes.add_source_episode_nonce
        };

        $wrapper.block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });

        $.post( masvideos_admin_meta_boxes.ajax_url, data, function( response ) {
            $sources.append( response );

            $( document.body ).trigger( 'masvideos-enhanced-select-init' );
            episode_source_row_indexes();
            $wrapper.unblock();

            $( document.body ).trigger( 'masvideos_added_source_episode' );
        });

        return false;
    });

    $( document ).on( 'masvideos_added_source_episode', function() {
        $( '.show_hide_select' ).on( 'change', function() {
            var shortcode_select = $(this).val(), 
                $masvideos_wp_shortcode = $(this).parents( '.options_group' );

            $masvideos_wp_shortcode.find( '.hide' ).hide();
            $masvideos_wp_shortcode.find( '.show_if_' + shortcode_select ).show();
        }).change();
    });

    $( '.episode_sources' ).on( 'blur', 'input.source_name', function() {
        $( this ).closest( '.masvideos_source' ).find( 'strong.source_name' ).text( $( this ).val() );
    });

    $( '.episode_sources' ).on( 'click', '.remove_row', function() {
        if ( window.confirm( masvideos_admin_meta_boxes.remove_source ) ) {
            var $parent = $( this ).parent().parent();

            $parent.find( 'select, input[type=text]' ).val( '' );
            $parent.hide();
            episode_source_row_indexes();
        }
        return false;
    });

    // Source ordering.
    $( '.episode_sources' ).sortable({
        items: '.masvideos_source',
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
            episode_source_row_indexes();
        }
    });

    // Save sources and update variations.
    $( '.save_sources_episode' ).on( 'click', function() {

        $( '#masvideos-episode-data' ).block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });

        var data = {
            post_id     : masvideos_admin_meta_boxes.post_id,
            data        : $( '.episode_sources' ).find( 'input, select, textarea' ).serialize(),
            action      : 'masvideos_save_sources_episode',
            security    : masvideos_admin_meta_boxes.save_sources_episode_nonce
        };

        $.post( masvideos_admin_meta_boxes.ajax_url, data, function() {
            // Reload variations panel.
            var this_page = window.location.toString();
            this_page = this_page.replace( 'post-new.php?', 'post.php?post=' + masvideos_admin_meta_boxes.post_id + '&action=edit&' );
            $( '#masvideos-episode-data' ).unblock();
        });
    });

    // Attribute Tables.

    // Initial order.
    var masvideos_attribute_items = $( '.episode_attributes' ).find( '.masvideos_attribute' ).get();

    masvideos_attribute_items.sort( function( a, b ) {
       var compA = parseInt( $( a ).attr( 'rel' ), 10 );
       var compB = parseInt( $( b ).attr( 'rel' ), 10 );
       return ( compA < compB ) ? -1 : ( compA > compB ) ? 1 : 0;
    });
    $( masvideos_attribute_items ).each( function( index, el ) {
        $( '.episode_attributes' ).append( el );
    });

    function episode_attribute_row_indexes() {
        $( '.episode_attributes .masvideos_attribute' ).each( function( index, el ) {
            $( '.attribute_position', el ).val( parseInt( $( el ).index( '.episode_attributes .masvideos_attribute' ), 10 ) );
        });
    }

    $( '.episode_attributes .masvideos_attribute' ).each( function( index, el ) {
        if ( $( el ).css( 'display' ) !== 'none' && $( el ).is( '.taxonomy' ) ) {
            $( 'select.attribute_taxonomy' ).find( 'option[value="' + $( el ).data( 'taxonomy' ) + '"]' ).attr( 'disabled', 'disabled' );
        }
    });

    // Add rows.
    $( 'button.add_attribute_episode' ).on( 'click', function() {
        var size         = $( '.episode_attributes .masvideos_attribute' ).length;
        var attribute    = $( 'select.attribute_taxonomy' ).val();
        var $wrapper     = $( this ).closest( '#episode_attributes' );
        var $attributes  = $wrapper.find( '.episode_attributes' );
        var data         = {
            action:   'masvideos_add_attribute_episode',
            taxonomy: attribute,
            i:        size,
            security: masvideos_admin_meta_boxes.add_attribute_episode_nonce
        };

        $wrapper.block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });

        $.post( masvideos_admin_meta_boxes.ajax_url, data, function( response ) {
            $attributes.append( response );

            $( document.body ).trigger( 'masvideos-enhanced-select-init' );
            episode_attribute_row_indexes();
            $wrapper.unblock();

            $( document.body ).trigger( 'masvideos_added_attribute_episode' );
        });

        if ( attribute ) {
            $( 'select.attribute_taxonomy' ).find( 'option[value="' + attribute + '"]' ).attr( 'disabled','disabled' );
            $( 'select.attribute_taxonomy' ).val( '' );
        }

        return false;
    });

    $( '.episode_attributes' ).on( 'blur', 'input.attribute_name', function() {
        $( this ).closest( '.masvideos_attribute' ).find( 'strong.attribute_name' ).text( $( this ).val() );
    });

    $( '.episode_attributes' ).on( 'click', 'button.select_all_attributes', function() {
        $( this ).closest( 'td' ).find( 'select option' ).attr( 'selected', 'selected' );
        $( this ).closest( 'td' ).find( 'select' ).change();
        return false;
    });

    $( '.episode_attributes' ).on( 'click', 'button.select_no_attributes', function() {
        $( this ).closest( 'td' ).find( 'select option' ).removeAttr( 'selected' );
        $( this ).closest( 'td' ).find( 'select' ).change();
        return false;
    });

    $( '.episode_attributes' ).on( 'click', '.remove_row', function() {
        if ( window.confirm( masvideos_admin_meta_boxes.remove_attribute ) ) {
            var $parent = $( this ).parent().parent();

            if ( $parent.is( '.taxonomy' ) ) {
                $parent.find( 'select, input[type=text]' ).val( '' );
                $parent.hide();
                $( 'select.attribute_taxonomy' ).find( 'option[value="' + $parent.data( 'taxonomy' ) + '"]' ).removeAttr( 'disabled' );
            } else {
                $parent.find( 'select, input[type=text]' ).val( '' );
                $parent.hide();
                episode_attribute_row_indexes();
            }
        }
        return false;
    });

    // Attribute ordering.
    $( '.episode_attributes' ).sortable({
        items: '.masvideos_attribute',
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
            episode_attribute_row_indexes();
        }
    });

    // Add a new attribute (via ajax).
    $( '.episode_attributes' ).on( 'click', 'button.add_new_attribute', function() {

        $( '.episode_attributes' ).block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });

        var $wrapper           = $( this ).closest( '.masvideos_attribute' );
        var attribute          = $wrapper.data( 'taxonomy' );
        var new_attribute_name = window.prompt( masvideos_admin_meta_boxes.new_attribute_prompt );

        if ( new_attribute_name ) {

            var data = {
                action:   'masvideos_add_new_attribute_episode',
                taxonomy: attribute,
                term:     new_attribute_name,
                security: masvideos_admin_meta_boxes.add_attribute_episode_nonce
            };

            $.post( masvideos_admin_meta_boxes.ajax_url, data, function( response ) {

                if ( response.error ) {
                    // Error.
                    window.alert( response.error );
                } else if ( response.slug ) {
                    // Success.
                    $wrapper.find( 'select.attribute_values' ).append( '<option value="' + response.term_id + '" selected="selected">' + response.name + '</option>' );
                    $wrapper.find( 'select.attribute_values' ).change();
                }

                $( '.episode_attributes' ).unblock();
            });

        } else {
            $( '.episode_attributes' ).unblock();
        }

        return false;
    });

    // Save attributes and update variations.
    $( '.save_attributes_episode' ).on( 'click', function() {

        $( '#masvideos-episode-data' ).block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });

        var data = {
            post_id     : masvideos_admin_meta_boxes.post_id,
            data        : $( '.episode_attributes' ).find( 'input, select, textarea' ).serialize(),
            action      : 'masvideos_save_attributes_episode',
            security    : masvideos_admin_meta_boxes.save_attributes_episode_nonce
        };

        $.post( masvideos_admin_meta_boxes.ajax_url, data, function() {
            // Reload variations panel.
            var this_page = window.location.toString();
            this_page = this_page.replace( 'post-new.php?', 'post.php?post=' + masvideos_admin_meta_boxes.post_id + '&action=edit&' );
            $( '#masvideos-episode-data' ).unblock();
        });
    });

    // Episode gallery file uploads.
    var episode_gallery_frame;
    var $image_gallery_ids = $( '#episode_image_gallery' );
    var $episode_images    = $( '#episode_images_container' ).find( 'ul.episode_images' );

    $( '.add_episode_images' ).on( 'click', 'a', function( event ) {
        var $el = $( this );

        event.preventDefault();

        // If the media frame already exists, reopen it.
        if ( episode_gallery_frame ) {
            episode_gallery_frame.open();
            return;
        }

        // Create the media frame.
        episode_gallery_frame = wp.media.frames.episode_gallery = wp.media({
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
        episode_gallery_frame.on( 'select', function() {
            var selection = episode_gallery_frame.state().get( 'selection' );
            var attachment_ids = $image_gallery_ids.val();

            selection.map( function( attachment ) {
                attachment = attachment.toJSON();

                if ( attachment.id ) {
                    attachment_ids   = attachment_ids ? attachment_ids + ',' + attachment.id : attachment.id;
                    var attachment_image = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;

                    $episode_images.append( '<li class="image" data-attachment_id="' + attachment.id + '"><img src="' + attachment_image + '" /><ul class="actions"><li><a href="#" class="delete" title="' + $el.data('delete') + '">' + $el.data('text') + '</a></li></ul></li>' );
                }
            });

            $image_gallery_ids.val( attachment_ids );
        });

        // Finally, open the modal.
        episode_gallery_frame.open();
    });

    // Image ordering.
    $episode_images.sortable({
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

            $( '#episode_images_container' ).find( 'ul li.image' ).css( 'cursor', 'default' ).each( function() {
                var attachment_id = $( this ).attr( 'data-attachment_id' );
                attachment_ids = attachment_ids + attachment_id + ',';
            });

            $image_gallery_ids.val( attachment_ids );
        }
    });

    // Remove images.
    $( '#episode_images_container' ).on( 'click', 'a.delete', function() {
        $( this ).closest( 'li.image' ).remove();

        var attachment_ids = '';

        $( '#episode_images_container' ).find( 'ul li.image' ).css( 'cursor', 'default' ).each( function() {
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
