<?php
/**
 * MasVideos Movie Playlist Functions
 *
 * Functions for movie playlist specific things.
 *
 * @package MasVideos/Functions
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Standard way of retrieving movie playlists based on certain parameters.
 *
 * This function should be used for movie playlist retrieval so that we have a data agnostic
 * way to get a list of movie playlists.
 *
 * @since  1.0.0
 * @param  array $args Array of args (above).
 * @return array|stdClass Number of pages and an array of movie playlist objects if
 *                             paginate is true, or just an array of values.
 */
function masvideos_get_movie_playlists( $args ) {
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

    $query = new MasVideos_Movie_Playlist_Query( $args );
    return $query->get_movie_playlists();
}

/**
 * Main function for returning movie_playlists, uses the MasVideos_Movie_Playlist_Factory class.
 *
 * @since 1.0.0
 *
 * @param mixed $the_movie_playlist Post object or post ID of the movie_playlist.
 * @return MasVideos_Movie_Playlist|null|false
 */
function masvideos_get_movie_playlist( $the_movie_playlist = false ) {
    if ( ! did_action( 'masvideos_init' ) ) {
        /* translators: 1: masvideos_get_movie_playlist 2: masvideos_init */
        _doing_it_wrong( __FUNCTION__, sprintf( __( '%1$s should not be called before the %2$s action.', 'masvideos' ), 'masvideos_get_movie_playlist', 'masvideos_init' ), '1.0.0' );
        return false;
    }
    return MasVideos()->movie_playlist_factory->get_movie_playlist( $the_movie_playlist );
}

/**
 * Clear all transients cache for movie playlist data.
 *
 * @param int $post_id (default: 0).
 */
function masvideos_delete_movie_playlist_transients( $post_id = 0 ) {
    // Core transients.
    $transients_to_clear = array(
        'masvideos_featured_movie_playlists',
        'masvideos_count_comments',
    );

    // Transient names that include an ID.
    $post_transient_names = array(
        'masvideos_movie_playlist_children_',
        'masvideos_related_',
    );

    if ( $post_id > 0 ) {
        foreach ( $post_transient_names as $transient ) {
            $transients_to_clear[] = $transient . $post_id;
        }

        // Does this movie playlist have a parent?
        $movie_playlist = masvideos_get_movie_playlist( $post_id );

        if ( $movie_playlist ) {
            if ( $movie_playlist->get_parent_id() > 0 ) {
                masvideos_delete_movie_playlist_transients( $movie_playlist->get_parent_id() );
            }
        }
    }

    // Delete transients.
    foreach ( $transients_to_clear as $transient ) {
        delete_transient( $transient );
    }

    // Increments the transient version to invalidate cache.
    MasVideos_Cache_Helper::get_transient_version( 'movie_playlist', true );

    do_action( 'masvideos_delete_movie_playlist_transients', $post_id );
}
