/* global masvideos_playlist_tv_show_params */
jQuery( function( $ ) {

    if ( typeof masvideos_playlist_tv_show_params === 'undefined' ) {
        return false;
    }

    $( document ).on( 'click', '.masvideos-ajax-toggle-tv-show-playlist', function(e) {
        var $thisbutton = $( this );

        if ( ! $thisbutton.attr( 'data-playlist_id' ) ) {
            return true;
        }

        if ( ! $thisbutton.attr( 'data-tv_show_id' ) ) {
            return true;
        }

        e.preventDefault();

        var data = {
            delete : $thisbutton.is( '.added' )
        };

        $.each( $thisbutton.data(), function( key, value ) {
            data[ key ] = value;
        });

        
        // Trigger event.
        $( document.body ).trigger( 'toggle_tv_show_playlist_before', [ $thisbutton, data ] );

        // Ajax action.
        $.post( masvideos_playlist_tv_show_params.masvideos_ajax_url.toString().replace( '%%endpoint%%', 'toggle_tv_show_playlist' ), data, function( response ) {
            if ( ! response ) {
                return;
            }

            if ( response.error && response.tv_show_url ) {
                window.location = response.tv_show_url;
                return;
            }

            $thisbutton.toggleClass( 'added' );

            // Trigger event so themes can refresh other areas.
            $( document.body ).trigger( 'toggle_tv_show_playlist_after', [ response.html, $thisbutton, data ] );
        });
    });
} );