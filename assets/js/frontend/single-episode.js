jQuery( function( $ ) {

    // masvideos_single_episode_params is required to continue.
    if ( typeof masvideos_single_episode_params === 'undefined' ) {
        return false;
    }

    $( 'body' )
        // Star ratings for comments
        .on( 'init', '#rating', function() {
            $( '#rating' ).hide().before( '<p class="stars"><span><a class="star-1" href="#">1</a><a class="star-2" href="#">2</a><a class="star-3" href="#">3</a><a class="star-4" href="#">4</a><a class="star-5" href="#">5</a><a class="star-6" href="#">6</a><a class="star-7" href="#">7</a><a class="star-8" href="#">8</a><a class="star-9" href="#">9</a><a class="star-10" href="#">10</a></span></p>' );
        } )
        .on( 'click', '#respond p.stars a', function() {
            var $star       = $( this ),
                $rating     = $( this ).closest( '#respond' ).find( '#rating' ),
                $container  = $( this ).closest( '.stars' );

            $rating.val( $star.text() );
            $star.siblings( 'a' ).removeClass( 'active' );
            $star.addClass( 'active' );
            $container.addClass( 'selected' );

            return false;
        } )
        .on( 'click', '#respond #submit', function() {
            var $rating = $( this ).closest( '#respond' ).find( '#rating' ),
                rating  = $rating.val();

            if ( $rating.length > 0 && ! rating && masvideos_single_episode_params.review_rating_required === 'yes' ) {
                window.alert( masvideos_single_episode_params.i18n_required_rating_text );

                return false;
            }
        } );

    // Init Star Ratings
    $( '#rating' ).trigger( 'init' );

    $( '.single-episode .episode-play-source' ).on( 'click', function(e) {
        e.preventDefault();

        var $content = $(this).data( 'content' );
        $( '.episode__player' ).html( $content );

        $('html, body').animate({
            scrollTop: $( '.episode__player' )
        }, 600);
    });
} );