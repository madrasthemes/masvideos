( function( $, window ) {
    'use strict';

    $( document ).ready( function() {
        /*===================================================================================*/
        /*  MasVideos Select2
        /*===================================================================================*/
        $( '.masvideos-select2' ).selectWoo();
    } );

    // Uploading Image
    if ( ! $('.upload_image_id').val() ) {
        $('.masvideos_remove_image_button').hide();
    }

    $(document).on( 'click', '.masvideos_upload_image_button', function( event ){
        var $this = $(this);
        var current_block = $(this).parents('.form-field').attr('id');

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
            var attachment = file_frame.state().get('selection').first().toJSON();

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
        var current_block = $(this).parents('.form-field').attr('id');

        $('#'+current_block+' img.upload_image_preview').attr('src', $('#'+current_block+' img.upload_image_preview').data('placeholder-src'));
        $('#'+current_block+' .upload_image_id').val('');
        $('#'+current_block+' .masvideos_remove_image_button').hide();
        $this.closest( '.widget-inside' ).find( '.widget-control-save' ).prop( 'disabled', false );

        return false;
    });

    // Video gallery file uploads.
    var video_gallery_frame;
    var $image_gallery_ids = $( '#video_image_gallery' );
    var $video_images    = $( '#video_images_container' ).find( 'ul.video_images' );

    $( '.add_video_images' ).on( 'click', 'a', function( event ) {
        var $el = $( this );

        event.preventDefault();

        // If the media frame already exists, reopen it.
        if ( video_gallery_frame ) {
            video_gallery_frame.open();
            return;
        }

        // Create the media frame.
        video_gallery_frame = wp.media.frames.video_gallery = wp.media({
            // Set the title of the modal.
            title: $el.data( 'choose' ),
            button: {
                text: $el.data( 'update' )
            },
            library: {
                type: [ 'image' ],
            },
            multiple: true
        });

        // When an image is selected, run a callback.
        video_gallery_frame.on( 'select', function() {
            var selection = video_gallery_frame.state().get( 'selection' );
            var attachment_ids = $image_gallery_ids.val();

            selection.map( function( attachment ) {
                var attachment = attachment.toJSON();

                if ( attachment.id ) {
                    attachment_ids   = attachment_ids ? attachment_ids + ',' + attachment.id : attachment.id;
                    var attachment_image = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;

                    $video_images.append( '<li class="image" data-attachment_id="' + attachment.id + '"><img src="' + attachment_image + '" /><ul class="actions"><li><a href="#" class="delete" title="' + $el.data('delete') + '">' + $el.data('text') + '</a></li></ul></li>' );
                }
            });

            $image_gallery_ids.val( attachment_ids );
        });

        // Finally, open the modal.
        video_gallery_frame.open();
    });

    // Uploading Video
    if ( ! $('.upload_video_id').val() ) {
        $('.masvideos_remove_video_button').hide();
    }

    $(document).on( 'click', '.masvideos_upload_video_button', function( event ){
        var $this = $(this);
        var current_block = $(this).parents('.form-field').attr('id');

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
            var attachment = file_frame.state().get('selection').first().toJSON();

            $('#'+current_block+' .upload_video_id').val( attachment.id );
            $('#'+current_block+' .masvideos_remove_video_button').show();
            $this.closest( '.widget-inside' ).find( '.widget-control-save' ).prop( 'disabled', false );
        });

        // Finally, open the modal.
        file_frame.open();
    });
} ) ( jQuery, window ) ;