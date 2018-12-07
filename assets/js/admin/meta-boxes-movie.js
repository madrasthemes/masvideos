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
    $( '#upsell_movie_data' ).bind( 'keypress', function( e ) {
        if ( e.keyCode === 13 ) {
            return false;
        }
    });

    // Type box.
    $( '.type_box' ).appendTo( '#masvideos-movie-data .hndle span' );

    $( function() {
        // Prevent inputs in meta box headings opening/closing contents.
        $( '#masvideos-movie-data' ).find( '.hndle' ).unbind( 'click.postboxes' );

        $( '#masvideos-movie-data' ).on( 'click', '.hndle', function( event ) {

            // If the user clicks on some form input inside the h3 the box should not be toggled.
            if ( $( event.target ).filter( 'input, option, label, select' ).length ) {
                return;
            }

            $( '#masvideos-movie-data' ).toggleClass( 'closed' );
        });
    });

    // Catalog Visibility.
    $( '#catalog-visibility' ).find( '.edit-catalog-visibility' ).click( function() {
        if ( $( '#catalog-visibility-select' ).is( ':hidden' ) ) {
            $( '#catalog-visibility-select' ).slideDown( 'fast' );
            $( this ).hide();
        }
        return false;
    });
    $( '#catalog-visibility' ).find( '.save-post-visibility' ).click( function() {
        $( '#catalog-visibility-select' ).slideUp( 'fast' );
        $( '#catalog-visibility' ).find( '.edit-catalog-visibility' ).show();

        var label = $( 'input[name=_visibility]:checked' ).attr( 'data-label' );

        if ( $( 'input[name=_featured]' ).is( ':checked' ) ) {
            label = label + ', ' + masvideos_admin_meta_boxes.featured_label;
            $( 'input[name=_featured]' ).attr( 'checked', 'checked' );
        }

        $( '#catalog-visibility-display' ).text( label );
        return false;
    });
    $( '#catalog-visibility' ).find( '.cancel-post-visibility' ).click( function() {
        $( '#catalog-visibility-select' ).slideUp( 'fast' );
        $( '#catalog-visibility' ).find( '.edit-catalog-visibility' ).show();

        var current_visibility = $( '#current_visibility' ).val();
        var current_featured   = $( '#current_featured' ).val();

        $( 'input[name=_visibility]' ).removeAttr( 'checked' );
        $( 'input[name=_visibility][value=' + current_visibility + ']' ).attr( 'checked', 'checked' );

        var label = $( 'input[name=_visibility]:checked' ).attr( 'data-label' );

        if ( 'yes' === current_featured ) {
            label = label + ', ' + masvideos_admin_meta_boxes.featured_label;
            $( 'input[name=_featured]' ).attr( 'checked', 'checked' );
        } else {
            $( 'input[name=_featured]' ).removeAttr( 'checked' );
        }

        $( '#catalog-visibility-display' ).text( label );
        return false;
    });

    // Date picker fields.
    function date_picker_select( datepicker ) {
        var option         = $( datepicker ).next().is( '.hasDatepicker' ) ? 'minDate' : 'maxDate',
            otherDateField = 'minDate' === option ? $( datepicker ).next() : $( datepicker ).prev(),
            date           = $( datepicker ).datepicker( 'getDate' );

        $( otherDateField ).datepicker( 'option', option, date );
        $( datepicker ).change();
    }

    $( '.movie_release_date_fields' ).each( function() {
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

    // Attribute Tables.

    // Initial order.
    var masvideos_attribute_items = $( '.movie_attributes' ).find( '.masvideos_attribute' ).get();

    masvideos_attribute_items.sort( function( a, b ) {
       var compA = parseInt( $( a ).attr( 'rel' ), 10 );
       var compB = parseInt( $( b ).attr( 'rel' ), 10 );
       return ( compA < compB ) ? -1 : ( compA > compB ) ? 1 : 0;
    });
    $( masvideos_attribute_items ).each( function( index, el ) {
        $( '.movie_attributes' ).append( el );
    });

    function movie_attribute_row_indexes() {
        $( '.movie_attributes .masvideos_attribute' ).each( function( index, el ) {
            $( '.attribute_position', el ).val( parseInt( $( el ).index( '.movie_attributes .masvideos_attribute' ), 10 ) );
        });
    }

    $( '.movie_attributes .masvideos_attribute' ).each( function( index, el ) {
        if ( $( el ).css( 'display' ) !== 'none' && $( el ).is( '.taxonomy' ) ) {
            $( 'select.attribute_taxonomy' ).find( 'option[value="' + $( el ).data( 'taxonomy' ) + '"]' ).attr( 'disabled', 'disabled' );
        }
    });

    // Add rows.
    $( 'button.add_attribute_movie' ).on( 'click', function() {
        var size         = $( '.movie_attributes .masvideos_attribute' ).length;
        var attribute    = $( 'select.attribute_taxonomy' ).val();
        var $wrapper     = $( this ).closest( '#movie_attributes' );
        var $attributes  = $wrapper.find( '.movie_attributes' );
        var data         = {
            action:   'masvideos_add_attribute_movie',
            taxonomy: attribute,
            i:        size,
            security: masvideos_admin_meta_boxes.add_attribute_movie_nonce
        };

        // $wrapper.block({
        //     message: null,
        //     overlayCSS: {
        //         background: '#fff',
        //         opacity: 0.6
        //     }
        // });

        $.post( masvideos_admin_meta_boxes.ajax_url, data, function( response ) {
            $attributes.append( response );

            $( document.body ).trigger( 'masvideos-enhanced-select-init' );
            movie_attribute_row_indexes();
            // $wrapper.unblock();

            $( document.body ).trigger( 'masvideos_added_attribute_movie' );
        });

        if ( attribute ) {
            $( 'select.attribute_taxonomy' ).find( 'option[value="' + attribute + '"]' ).attr( 'disabled','disabled' );
            $( 'select.attribute_taxonomy' ).val( '' );
        }

        return false;
    });

    $( '.movie_attributes' ).on( 'blur', 'input.attribute_name', function() {
        $( this ).closest( '.masvideos_attribute' ).find( 'strong.attribute_name' ).text( $( this ).val() );
    });

    $( '.movie_attributes' ).on( 'click', 'button.select_all_attributes', function() {
        $( this ).closest( 'td' ).find( 'select option' ).attr( 'selected', 'selected' );
        $( this ).closest( 'td' ).find( 'select' ).change();
        return false;
    });

    $( '.movie_attributes' ).on( 'click', 'button.select_no_attributes', function() {
        $( this ).closest( 'td' ).find( 'select option' ).removeAttr( 'selected' );
        $( this ).closest( 'td' ).find( 'select' ).change();
        return false;
    });

    $( '.movie_attributes' ).on( 'click', '.remove_row', function() {
        if ( window.confirm( masvideos_admin_meta_boxes.remove_attribute ) ) {
            var $parent = $( this ).parent().parent();

            if ( $parent.is( '.taxonomy' ) ) {
                $parent.find( 'select, input[type=text]' ).val( '' );
                $parent.hide();
                $( 'select.attribute_taxonomy' ).find( 'option[value="' + $parent.data( 'taxonomy' ) + '"]' ).removeAttr( 'disabled' );
            } else {
                $parent.find( 'select, input[type=text]' ).val( '' );
                $parent.hide();
                movie_attribute_row_indexes();
            }
        }
        return false;
    });

    // Attribute ordering.
    $( '.movie_attributes' ).sortable({
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
            movie_attribute_row_indexes();
        }
    });

    // Add a new attribute (via ajax).
    $( '.movie_attributes' ).on( 'click', 'button.add_new_attribute', function() {

        // $( '.movie_attributes' ).block({
        //     message: null,
        //     overlayCSS: {
        //         background: '#fff',
        //         opacity: 0.6
        //     }
        // });

        var $wrapper           = $( this ).closest( '.masvideos_attribute' );
        var attribute          = $wrapper.data( 'taxonomy' );
        var new_attribute_name = window.prompt( masvideos_admin_meta_boxes.new_attribute_prompt );

        if ( new_attribute_name ) {

            var data = {
                action:   'masvideos_add_new_attribute_movie',
                taxonomy: attribute,
                term:     new_attribute_name,
                security: masvideos_admin_meta_boxes.add_attribute_movie_nonce
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

                // $( '.movie_attributes' ).unblock();
            });

        } else {
            // $( '.movie_attributes' ).unblock();
        }

        return false;
    });

    // Save attributes and update variations.
    $( '.save_attributes_movie' ).on( 'click', function() {

        // $( '#masvideos-movie-data' ).block({
        //     message: null,
        //     overlayCSS: {
        //         background: '#fff',
        //         opacity: 0.6
        //     }
        // });

        var data = {
            post_id     : masvideos_admin_meta_boxes.post_id,
            data        : $( '.movie_attributes' ).find( 'input, select, textarea' ).serialize(),
            action      : 'masvideos_save_attributes_movie',
            security    : masvideos_admin_meta_boxes.save_attributes_movie_nonce
        };

        $.post( masvideos_admin_meta_boxes.ajax_url, data, function() {
            // Reload variations panel.
            var this_page = window.location.toString();
            this_page = this_page.replace( 'post-new.php?', 'post.php?post=' + masvideos_admin_meta_boxes.post_id + '&action=edit&' );
        });
    });

    // Product gallery file uploads.
    var movie_gallery_frame;
    var $image_gallery_ids = $( '#movie_image_gallery' );
    var $movie_images    = $( '#movie_images_container' ).find( 'ul.movie_images' );

    $( '.add_movie_images' ).on( 'click', 'a', function( event ) {
        var $el = $( this );

        event.preventDefault();

        // If the media frame already exists, reopen it.
        if ( movie_gallery_frame ) {
            movie_gallery_frame.open();
            return;
        }

        // Create the media frame.
        movie_gallery_frame = wp.media.frames.movie_gallery = wp.media({
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
        movie_gallery_frame.on( 'select', function() {
            var selection = movie_gallery_frame.state().get( 'selection' );
            var attachment_ids = $image_gallery_ids.val();

            selection.map( function( attachment ) {
                attachment = attachment.toJSON();

                if ( attachment.id ) {
                    attachment_ids   = attachment_ids ? attachment_ids + ',' + attachment.id : attachment.id;
                    var attachment_image = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;

                    $movie_images.append( '<li class="image" data-attachment_id="' + attachment.id + '"><img src="' + attachment_image + '" /><ul class="actions"><li><a href="#" class="delete" title="' + $el.data('delete') + '">' + $el.data('text') + '</a></li></ul></li>' );
                }
            });

            $image_gallery_ids.val( attachment_ids );
        });

        // Finally, open the modal.
        movie_gallery_frame.open();
    });

    // Image ordering.
    $movie_images.sortable({
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

            $( '#movie_images_container' ).find( 'ul li.image' ).css( 'cursor', 'default' ).each( function() {
                var attachment_id = $( this ).attr( 'data-attachment_id' );
                attachment_ids = attachment_ids + attachment_id + ',';
            });

            $image_gallery_ids.val( attachment_ids );
        }
    });

    // Remove images.
    $( '#movie_images_container' ).on( 'click', 'a.delete', function() {
        $( this ).closest( 'li.image' ).remove();

        var attachment_ids = '';

        $( '#movie_images_container' ).find( 'ul li.image' ).css( 'cursor', 'default' ).each( function() {
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
