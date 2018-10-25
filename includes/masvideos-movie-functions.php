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

    $query = new MasVideos_Movie_Query( $args ); // WC_Product_Query
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