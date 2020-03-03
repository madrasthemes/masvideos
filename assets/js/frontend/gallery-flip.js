( function( $, window ) {
    'use strict';

    $( document ).ready( function() {
        /*===================================================================================*/
        /*  Videos Gallery Flipper
        /*===================================================================================*/
        $( '.videos .video__poster' ).each( function() {
            var $this = $( this ).find( '.video__poster--image' );
            if ( typeof $this.data('gallery-images') !== 'undefined' ) {
                var imagesjson = $this.attr( 'data-gallery-images' );
                var images = JSON.parse( imagesjson );
                var original_src = $this.attr( 'src' );
                var original_srcset = $this.attr( 'srcset' );
                var shouldRotateThumbnails = false;

                $( this ).on( 'mouseenter', function() {
                    shouldRotateThumbnails = true;
                    rotateThumbnails();
                });

                $( this ).on( 'mouseleave', function() {
                    shouldRotateThumbnails = false;
                    $this.attr( 'src', original_src );
                    $this.attr( 'srcset', original_srcset );
                });
            }

            function rotateThumbnails() {
                if( images.length > 0 ) {
                    $.each(images, function( key, value ) {
                        setTimeout( function () {
                            if( ! shouldRotateThumbnails ) {
                                return false;
                            }

                            $this.attr( 'src', value );
                            $this.attr( 'srcset', value );

                            if( images.length == ( key + 1 ) ) {
                                setTimeout( function () {
                                    rotateThumbnails();
                                }, 800 );
                            }
                        }, ( key * 800 ) );
                    } );
                }
            }
        } );
    } );
} ) ( jQuery, window );