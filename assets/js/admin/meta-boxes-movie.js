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
    $( '.type_box' ).appendTo( '#masvideos-movie-data .handle span' );

    // Date picker fields.
    function date_picker_select( datepicker ) {
        var option         = $( datepicker ).next().is( '.hasDatepicker' ) ? 'minDate' : 'maxDate',
            otherDateField = 'minDate' === option ? $( datepicker ).next() : $( datepicker ).prev(),
            date           = $( datepicker ).datepicker( 'getDate' );

        $( otherDateField ).datepicker( 'option', option, date );
        $( datepicker ).change();
    }

    $( '.movie_date_picker' ).each( function() {
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

    // Cast Person Tables.

    // Initial order.
    var masvideos_person_items = $( '.movie_cast_persons' ).find( '.masvideos_cast_person' ).get();

    masvideos_person_items.sort( function( a, b ) {
       var compA = parseInt( $( a ).attr( 'rel' ), 10 );
       var compB = parseInt( $( b ).attr( 'rel' ), 10 );
       return ( compA < compB ) ? -1 : ( compA > compB ) ? 1 : 0;
    });
    $( masvideos_person_items ).each( function( index, el ) {
        $( '.movie_cast_persons' ).append( el );
    });

    function movie_cast_person_row_indexes() {
        $( '.movie_cast_persons .masvideos_cast_person' ).each( function( index, el ) {
            $( '.person_position', el ).val( parseInt( $( el ).index( '.movie_cast_persons .masvideos_cast_person' ), 10 ) );
        });
    }

    $( '.movie_cast_persons .masvideos_cast_person' ).each( function( index, el ) {
        if ( $( el ).css( 'display' ) !== 'none' ) {
            var exclude_ids = $( '#movie_cast_persons select.person_id' ).data( 'exclude' ) || [];
            exclude_ids.push( $( el ).data( 'person_id' ) );
            $( '#movie_cast_persons select.person_id' ).data( 'exclude', exclude_ids );
        }
    });

    // Add rows.
    $( 'button.add_person_movie_cast' ).on( 'click', function() {
        var size         = $( '.movie_cast_persons .masvideos_cast_person' ).length;
        var person_id    = $( '#movie_cast_persons select.person_id' ).val();
        var $wrapper     = $( this ).closest( '#movie_cast_persons' );
        var $persons     = $wrapper.find( '.movie_cast_persons' );
        var data         = {
            action:   'masvideos_add_person_movie_cast',
            person_id: person_id,
            i:        size,
            security: masvideos_admin_meta_boxes.add_person_movie_nonce
        };

        $wrapper.block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });

        $.post( masvideos_admin_meta_boxes.ajax_url, data, function( response ) {
            $persons.append( response );

            $( document.body ).trigger( 'masvideos-enhanced-select-init' );
            movie_cast_person_row_indexes();
            $wrapper.unblock();

            $( document.body ).trigger( 'masvideos_added_person_movie_cast' );
        });

        if ( person_id ) {
            var exclude_ids = $( '#movie_cast_persons select.person_id' ).data( 'exclude' ) || [];
            exclude_ids.push( person_id );
            $( '#movie_cast_persons select.person_id' ).data( 'exclude', exclude_ids );
            $( '#movie_cast_persons select.person_id' ).val( '' ).trigger( 'change' );
        }

        return false;
    });

    $( '.movie_cast_persons' ).on( 'blur', 'input.person_id', function() {
        $( this ).closest( '.masvideos_cast_person' ).find( 'strong.person_id' ).text( $( this ).val() );
    });

    $( '.movie_cast_persons' ).on( 'click', '.remove_row', function() {
        if ( window.confirm( masvideos_admin_meta_boxes.remove_person ) ) {
            var $parent = $( this ).parent().parent();

            $parent.find( 'select, input[type=text], input[type=hidden]' ).val( '' );
            $parent.hide();
            var exclude_ids = $( '#movie_cast_persons select.person_id' ).data( 'exclude' ) || [];
            exclude_ids.splice( $.inArray( $parent.data( 'person_id' ), exclude_ids ), 1 );
            $( '#movie_cast_persons select.person_id' ).data( 'exclude', exclude_ids );
            movie_cast_person_row_indexes();
        }
        return false;
    });

    // Person ordering.
    $( '.movie_cast_persons' ).sortable({
        items: '.masvideos_cast_person',
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
            movie_cast_person_row_indexes();
        }
    });

    // Save persons and update variations.
    $( '.save_persons_movie_cast' ).on( 'click', function() {

        $( '#masvideos-movie-data' ).block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });

        var data = {
            post_id     : masvideos_admin_meta_boxes.post_id,
            data        : $( '.movie_cast_persons' ).find( 'input, select, textarea' ).serialize(),
            action      : 'masvideos_save_persons_movie_cast',
            security    : masvideos_admin_meta_boxes.save_persons_movie_nonce
        };

        $.post( masvideos_admin_meta_boxes.ajax_url, data, function() {
            // Reload variations panel.
            var this_page = window.location.toString();
            this_page = this_page.replace( 'post-new.php?', 'post.php?post=' + masvideos_admin_meta_boxes.post_id + '&action=edit&' );
            $( '#masvideos-movie-data' ).unblock();
        });
    });

    // Crew Person Tables.

    // Initial order.
    var masvideos_person_items = $( '.movie_crew_persons' ).find( '.masvideos_crew_person' ).get();

    masvideos_person_items.sort( function( a, b ) {
       var compA = parseInt( $( a ).attr( 'rel' ), 10 );
       var compB = parseInt( $( b ).attr( 'rel' ), 10 );
       return ( compA < compB ) ? -1 : ( compA > compB ) ? 1 : 0;
    });
    $( masvideos_person_items ).each( function( index, el ) {
        $( '.movie_crew_persons' ).append( el );
    });

    function movie_crew_person_row_indexes() {
        $( '.movie_crew_persons .masvideos_crew_person' ).each( function( index, el ) {
            $( '.person_position', el ).val( parseInt( $( el ).index( '.movie_crew_persons .masvideos_crew_person' ), 10 ) );
        });
    }

    $( '.movie_crew_persons .masvideos_crew_person' ).each( function( index, el ) {
        if ( $( el ).css( 'display' ) !== 'none' ) {
            var exclude_ids = $( '#movie_crew_persons select.person_id' ).data( 'exclude' ) || [];
            // exclude_ids.push( $( el ).data( 'person_id' ) );
            $( '#movie_crew_persons select.person_id' ).data( 'exclude', exclude_ids );
        }
    });

    // Add rows.
    $( 'button.add_person_movie_crew' ).on( 'click', function() {
        var size         = $( '.movie_crew_persons .masvideos_crew_person' ).length;
        var person_id    = $( '#movie_crew_persons select.person_id' ).val();
        var $wrapper     = $( this ).closest( '#movie_crew_persons' );
        var $persons     = $wrapper.find( '.movie_crew_persons' );
        var data         = {
            action:   'masvideos_add_person_movie_crew',
            person_id: person_id,
            i:        size,
            security: masvideos_admin_meta_boxes.add_person_movie_nonce
        };

        $wrapper.block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });

        $.post( masvideos_admin_meta_boxes.ajax_url, data, function( response ) {
            $persons.append( response );

            $( document.body ).trigger( 'masvideos-enhanced-select-init' );
            movie_crew_person_row_indexes();
            $wrapper.unblock();

            $( document.body ).trigger( 'masvideos_added_person_movie_crew' );
        });

        if ( person_id ) {
            var exclude_ids = $( '#movie_crew_persons select.person_id' ).data( 'exclude' ) || [];
            // exclude_ids.push( person_id );
            $( '#movie_crew_persons select.person_id' ).data( 'exclude', exclude_ids );
            $( '#movie_crew_persons select.person_id' ).val( '' ).trigger( 'change' );
        }

        return false;
    });

    $( '.movie_crew_persons' ).on( 'blur', 'input.person_id', function() {
        $( this ).closest( '.masvideos_crew_person' ).find( 'strong.person_id' ).text( $( this ).val() );
    });

    $( '.movie_crew_persons' ).on( 'click', '.remove_row', function() {
        if ( window.confirm( masvideos_admin_meta_boxes.remove_person ) ) {
            var $parent = $( this ).parent().parent();

            $parent.find( 'select, input[type=text], input[type=hidden]' ).val( '' );
            $parent.hide();
            var exclude_ids = $( '#movie_crew_persons select.person_id' ).data( 'exclude' ) || [];
            // exclude_ids.splice( $.inArray( $parent.data( 'person_id' ), exclude_ids ), 1 );
            $( '#movie_crew_persons select.person_id' ).data( 'exclude', exclude_ids );
            movie_crew_person_row_indexes();
        }
        return false;
    });

    // Person ordering.
    $( '.movie_crew_persons' ).sortable({
        items: '.masvideos_crew_person',
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
            movie_crew_person_row_indexes();
        }
    });

    // Save persons and update variations.
    $( '.save_persons_movie_crew' ).on( 'click', function() {

        $( '#masvideos-movie-data' ).block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });

        var data = {
            post_id     : masvideos_admin_meta_boxes.post_id,
            data        : $( '.movie_crew_persons' ).find( 'input, select, textarea' ).serialize(),
            action      : 'masvideos_save_persons_movie_crew',
            security    : masvideos_admin_meta_boxes.save_persons_movie_nonce
        };

        $.post( masvideos_admin_meta_boxes.ajax_url, data, function() {
            // Reload variations panel.
            var this_page = window.location.toString();
            this_page = this_page.replace( 'post-new.php?', 'post.php?post=' + masvideos_admin_meta_boxes.post_id + '&action=edit&' );
            $( '#masvideos-movie-data' ).unblock();
        });
    });

    // Source Tables.

    // Initial order.
    var masvideos_source_items = $( '.movie_sources' ).find( '.masvideos_source' ).get();

    masvideos_source_items.sort( function( a, b ) {
       var compA = parseInt( $( a ).attr( 'rel' ), 10 );
       var compB = parseInt( $( b ).attr( 'rel' ), 10 );
       return ( compA < compB ) ? -1 : ( compA > compB ) ? 1 : 0;
    });
    $( masvideos_source_items ).each( function( index, el ) {
        $( '.movie_sources' ).append( el );
    });

    function movie_source_row_indexes() {
        $( '.movie_sources .masvideos_source' ).each( function( index, el ) {
            $( '.source_position', el ).val( parseInt( $( el ).index( '.movie_sources .masvideos_source' ), 10 ) );
        });
    }

    // Add a new source (via ajax).
    $( 'button.add_movie_source' ).on( 'click', function() {
        var size         = $( '.movie_sources .masvideos_source' ).length;
        var $wrapper     = $( this ).closest( '#movie_sources' );
        var $sources     = $wrapper.find( '.movie_sources' );
        var data         = {
            action:   'masvideos_add_source_movie',
            i:        size,
            security: masvideos_admin_meta_boxes.add_source_movie_nonce
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
            movie_source_row_indexes();
            $wrapper.unblock();

            $( document.body ).trigger( 'masvideos_added_source_movie' );
        });

        return false;
    });

    $( document ).on( 'masvideos_added_source_movie', function() {
        $( '.show_hide_select' ).on( 'change', function() {
            var shortcode_select = $(this).val(), 
                $masvideos_wp_shortcode = $(this).parents( '.options_group' );

            $masvideos_wp_shortcode.find( '.hide' ).hide();
            $masvideos_wp_shortcode.find( '.show_if_' + shortcode_select ).show();
        }).change();
    });

    $( '.movie_sources' ).on( 'blur', 'input.source_name', function() {
        $( this ).closest( '.masvideos_source' ).find( 'strong.source_name' ).text( $( this ).val() );
    });

    $( '.movie_sources' ).on( 'click', '.remove_row', function() {
        if ( window.confirm( masvideos_admin_meta_boxes.remove_source ) ) {
            var $parent = $( this ).parent().parent();

            $parent.find( 'select, input[type=text]' ).val( '' );
            $parent.hide();
            movie_source_row_indexes();
        }
        return false;
    });

    // Source ordering.
    $( '.movie_sources' ).sortable({
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
            movie_source_row_indexes();
        }
    });

    // Save sources and update variations.
    $( '.save_sources_movie' ).on( 'click', function() {

        $( '#masvideos-movie-data' ).block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });

        var data = {
            post_id     : masvideos_admin_meta_boxes.post_id,
            data        : $( '.movie_sources' ).find( 'input, select, textarea' ).serialize(),
            action      : 'masvideos_save_sources_movie',
            security    : masvideos_admin_meta_boxes.save_sources_movie_nonce
        };

        $.post( masvideos_admin_meta_boxes.ajax_url, data, function() {
            // Reload variations panel.
            var this_page = window.location.toString();
            this_page = this_page.replace( 'post-new.php?', 'post.php?post=' + masvideos_admin_meta_boxes.post_id + '&action=edit&' );
            $( '#masvideos-movie-data' ).unblock();
        });
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
            movie_attribute_row_indexes();
            $wrapper.unblock();

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

        $( '.movie_attributes' ).block({
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

                $( '.movie_attributes' ).unblock();
            });

        } else {
            $( '.movie_attributes' ).unblock();
        }

        return false;
    });

    // Save attributes and update variations.
    $( '.save_attributes_movie' ).on( 'click', function() {

        $( '#masvideos-movie-data' ).block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });

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
            $( '#masvideos-movie-data' ).unblock();
        });
    });

    // Movie gallery file uploads.
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
