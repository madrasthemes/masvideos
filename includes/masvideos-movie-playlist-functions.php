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

/**
 * Update a playlist.
 *
 * @since  1.0.0
 * @param  int   $id   Playlist ID.
 * @param  array $args Playlist arguments.
 * @return int|WP_Error
 */
function masvideos_update_movie_playlist( $id = 0, $args ) {
    $movie_playlist = masvideos_get_movie_playlist( $id );

    if ( ! $movie_playlist ) {
        $current_user_id = get_current_user_id();
        $slug = uniqid( $current_user_id );
        $movie_playlist = new MasVideos_Movie_Playlist( $id );
        $movie_playlist->set_slug( $slug );
    }

    $movie_playlist->set_name( $args['name'] );
    $movie_playlist->set_status( $args['status'] );
    $movie_playlist->save();

    return $movie_playlist;
}

/**
 * Add movie to playlist.
 *
 * @since  1.0.0
 * @param  int   $id        Playlist ID.
 * @param  int   $movie_id  Movie ID.
 * @return int|WP_Error
 */
function masvideos_add_movie_to_playlist( $id, $movie_id ) {
    $movie_playlist = masvideos_get_movie_playlist( $id );

    if ( ! $movie_playlist ) {
        return false;
    }

    if( masvideos_is_movie_added_to_playlist( $id, $movie_id ) ) {
        return false;
    }

    $movie_ids = $movie_playlist->get_movie_ids( 'edit' );

    if( is_array( $movie_ids ) ) {
        $movie_ids[] = $movie_id;
    } else {
        $movie_ids = array( $movie_id );
    }

    $movie_playlist->set_movie_ids( $movie_ids );
    $movie_playlist->save();

    return $movie_playlist;
}

/**
 * Remove movie from playlist.
 *
 * @since  1.0.0
 * @param  int   $id        Playlist ID.
 * @param  int   $movie_id  Movie ID.
 * @return int|WP_Error
 */
function masvideos_remove_movie_from_playlist( $id, $movie_id ) {
    $movie_playlist = masvideos_get_movie_playlist( $id );

    if ( ! $movie_playlist ) {
        return false;
    }

    if( ! masvideos_is_movie_added_to_playlist( $id, $movie_id ) ) {
        return false;
    }

    $movie_ids = $movie_playlist->get_movie_ids( 'edit' );

    if ( false === $key = array_search( $movie_id, $movie_ids ) ) {
        return false;
    }

    array_splice( $movie_ids, $key, 1 );

    $movie_playlist->set_movie_ids( $movie_ids );
    $movie_playlist->save();

    return $movie_playlist;
}

/**
 * Check a movie added to a playlist.
 *
 * @since  1.0.0
 * @param  int   $id        Playlist ID.
 * @param  int   $movie_id  Movie ID.
 * @return int|WP_Error
 */
function masvideos_is_movie_added_to_playlist( $id, $movie_id ) {
    $movie_playlist = masvideos_get_movie_playlist( $id );

    if ( ! $movie_playlist ) {
        return false;
    }

    $movie_ids = $movie_playlist->get_movie_ids();

    if( is_array( $movie_ids ) && in_array( $movie_id, $movie_ids ) ) {
        return true;
    }

    return false;
}

/**
 * Get current user's playlists.
 *
 * @since  1.0.0
 * @return array|boolean
 */
function masvideos_get_current_user_movie_playlists() {
    if ( is_user_logged_in() ) {
        $current_user_id = get_current_user_id();

        $args = array(
            'post_type'         => 'movie_playlist',
            'post_status'       => array_keys( masvideos_get_movie_playlist_visibility_options() ),
            'posts_per_page'    => -1,
            'author'            => $current_user_id
        );

        $current_user_posts = get_posts( $args );

        return $current_user_posts;
    }

    return false;
}

/**
 * Get single playlist's all movies.
 *
 * @since  1.0.0
 * @return array|boolean
 */
function masvideos_single_movie_playlist_movies( $id ) {
    $movie_playlist = masvideos_get_movie_playlist( $id );

    if ( ! $movie_playlist ) {
        return false;
    }

    return $movie_playlist->get_movie_ids();
}

/**
 * Set current user's watched history to playlist.
 *
 * @since  1.0.0
 * @return array|boolean
 */
function masvideos_set_watched_movie_history_to_playlist() {
    if ( is_user_logged_in() && is_movie() ) {
        global $movie;

        $current_user_id = get_current_user_id();

        $playlist_id = get_user_option( 'masvideos_history_movie_playlist_id', $current_user_id );

        if( empty( $playlist_id ) || is_null( get_post( $playlist_id ) ) ) {
            $args = array(
                'name'      => esc_html__( 'History', 'masvideos' ),
                'status'    => 'private',
            );
            $movie_playlist = masvideos_update_movie_playlist( 0, $args );
            $playlist_id = $movie_playlist->get_id();
            update_user_option( $current_user_id, 'masvideos_history_movie_playlist_id', $playlist_id );
        }

        $movie_playlist = masvideos_add_movie_to_playlist( $playlist_id, $movie->get_id() );

        return $movie_playlist;
    }
}

/**
 * Update single movie's link for playlist.
 *
 * @since  1.0.0
 * @return array|boolean
 */
function masvideos_loop_movie_link_for_movie_playlist( $link, $movie ) {
    if ( is_movie_playlist() ) {
        $movie_playlist_id = get_queried_object_id();
        return add_query_arg( 'movie_playlist_id', $movie_playlist_id, $link );
    } elseif( is_movie() ) {
        $movie_playlist_id = isset( $_GET['movie_playlist_id'] ) ? absint( $_GET['movie_playlist_id'] ) : 0;
        return add_query_arg( 'movie_playlist_id', $movie_playlist_id, $link );
    }

    return $link;
}

/**
 * Get movie playlist visibility options.
 *
 * @since 1.0.0
 * @return array
 */
function masvideos_get_movie_playlist_visibility_options() {
    return apply_filters(
        'masvideos_movie_playlist_visibility_options', array(
            'publish' => __( 'Public', 'masvideos' ),
            'private' => __( 'Private', 'masvideos' ),
        )
    );
}
