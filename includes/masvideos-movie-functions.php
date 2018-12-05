<?php
/**
 * MasVideos Movie Functions
 *
 * Functions for movie specific things.
 *
 * @package MasVideos/Functions
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Standard way of retrieving movies based on certain parameters.
 *
 * This function should be used for movie retrieval so that we have a data agnostic
 * way to get a list of movies.
 *
 * @since  1.0.0
 * @param  array $args Array of args (above).
 * @return array|stdClass Number of pages and an array of movie objects if
 *                             paginate is true, or just an array of values.
 */
function masvideos_get_movies( $args ) {
    // Handle some BW compatibility arg names where wp_query args differ in naming.
    $map_legacy = array(
        'numberposts'    => 'limit',
        'post_status'    => 'status',
        'post_parent'    => 'parent',
        'posts_per_page' => 'limit',
        'paged'          => 'page',
    );

    foreach ( $map_legacy as $from => $to ) {
        if ( isset( $args[ $from ] ) ) {
            $args[ $to ] = $args[ $from ];
        }
    }

    $query = new MasVideos_Movie_Query( $args );
    return $query->get_movies();
}

/**
 * Main function for returning movies, uses the MasVideos_Movie_Factory class.
 *
 * @since 1.0.0
 *
 * @param mixed $the_movie Post object or post ID of the movie.
 * @return MasVideos_Movie|null|false
 */
function masvideos_get_movie( $the_movie = false ) {
    if ( ! did_action( 'masvideos_init' ) ) {
        /* translators: 1: masvideos_get_movie 2: masvideos_init */
        _doing_it_wrong( __FUNCTION__, sprintf( __( '%1$s should not be called before the %2$s action.', 'masvideos' ), 'masvideos_get_movie', 'masvideos_init' ), '1.0.0' );
        return false;
    }
    return MasVideos()->movie_factory->get_movie( $the_movie );
}

/**
 * Clear all transients cache for movie data.
 *
 * @param int $post_id (default: 0).
 */
function masvideos_delete_movie_transients( $post_id = 0 ) {
    // Core transients.
    $transients_to_clear = array(
        'masvideos_movies_onsale',
        'masvideos_featured_movies',
        'masvideos_count_comments',
    );

    // Transient names that include an ID.
    $post_transient_names = array(
        'masvideos_movie_children_',
        'masvideos_related_',
    );

    if ( $post_id > 0 ) {
        foreach ( $post_transient_names as $transient ) {
            $transients_to_clear[] = $transient . $post_id;
        }

        // Does this movie have a parent?
        $movie = masvideos_get_movie( $post_id );

        if ( $movie ) {
            if ( $movie->get_parent_id() > 0 ) {
                masvideos_delete_movie_transients( $movie->get_parent_id() );
            }
        }
    }

    // Delete transients.
    foreach ( $transients_to_clear as $transient ) {
        delete_transient( $transient );
    }

    // Increments the transient version to invalidate cache.
    MasVideos_Cache_Helper::get_transient_version( 'movie', true );

    do_action( 'masvideos_delete_movie_transients', $post_id );
}

/**
 * Get movie visibility options.
 *
 * @since 1.0.0
 * @return array
 */
function masvideos_get_movie_visibility_options() {
	return apply_filters(
		'masvideos_movie_visibility_options', array(
			'visible' => __( 'Catalog and search results', 'masvideos' ),
			'catalog' => __( 'Catalog only', 'masvideos' ),
			'search'  => __( 'Search results only', 'masvideos' ),
			'hidden'  => __( 'Hidden', 'masvideos' ),
		)
	);
}

if ( ! function_exists ( 'masvideos_the_movie' ) ) {
    function masvideos_the_movie( $post = null ) {
        global $post;
        $thepostid = $post->ID;
        $movie_object = $thepostid ? masvideos_get_movie( $thepostid ) : new MasVideos_Movie();
        $movie_src = masvideos_get_the_movie( $movie_object );
        $movie_choice = $movie_object->get_movie_choice();

        if ( ! empty ( $movie_src ) ) {
            if ( $movie_choice == 'movie_file' ) {
                echo do_shortcode('[video src="' . $movie_src . '"]');
            } elseif ( $movie_choice == 'movie_embed' ) {
                echo '<div class="wp-video">' . $movie_src . '</div>';
            } elseif ( $movie_choice == 'movie_url' ) {
                echo do_shortcode('[video src="' . $movie_src . '"]');
            }
        }
    }
}

if ( ! function_exists ( 'masvideos_get_the_movie' ) ) {
    function masvideos_get_the_movie( $post = null ) {
        global $post;
        $thepostid = $post->ID;
        $movie_object = $thepostid ? masvideos_get_movie( $thepostid ) : new MasVideos_Movie();
        $movie_src = '';
        $movie_choice = $movie_object->get_movie_choice();

        if ( $movie_choice == 'movie_file' ) {
            $movie_src =  wp_get_attachment_url( $movie_object->get_movie_attachment_id() );
        } elseif ( $movie_choice == 'movie_embed' ) {
            $movie_src = $movie_object->get_movie_embed_content();
        } elseif ( $movie_choice == 'movie_url' ) {
            $movie_src = $movie_object->get_movie_url_link();
        }

        return $movie_src;
    }
}