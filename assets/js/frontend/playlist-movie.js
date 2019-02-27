/* global masvideos_playlist_movie_params */
jQuery( function( $ ) {

    if ( typeof masvideos_playlist_movie_params === 'undefined' ) {
        return false;
    }

    $( document ).on( 'click', '.masvideos-ajax-toggle-movie-playlist', function(e) {
        var $thisbutton = $( this );

        if ( ! $thisbutton.attr( 'data-playlist_id' ) ) {
            return true;
        }

        if ( ! $thisbutton.attr( 'data-movie_id' ) ) {
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
        $( document.body ).trigger( 'toggle_movie_playlist_before', [ $thisbutton, data ] );

        // Ajax action.
        $.post( masvideos_playlist_movie_params.masvideos_ajax_url.toString().replace( '%%endpoint%%', 'toggle_movie_playlist' ), data, function( response ) {
            if ( ! response ) {
                return;
            }

            if ( response.error && response.movie_url ) {
                window.location = response.movie_url;
                return;
            }

            $thisbutton.toggleClass( 'added' );

            // Trigger event so themes can refresh other areas.
            $( document.body ).trigger( 'toggle_movie_playlist_after', [ response.html, $thisbutton, data ] );
        });
    });
} );