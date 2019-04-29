<?php
/**
 * MasVideos TV Show Playlist Functions
 *
 * Functions for tv show playlist specific things.
 *
 * @package MasVideos/Functions
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Standard way of retrieving tv show playlists based on certain parameters.
 *
 * This function should be used for tv show playlist retrieval so that we have a data agnostic
 * way to get a list of tv show playlists.
 *
 * @since  1.0.0
 * @param  array $args Array of args (above).
 * @return array|stdClass Number of pages and an array of tv show playlist objects if
 *                             paginate is true, or just an array of values.
 */
function masvideos_get_tv_show_playlists( $args ) {
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

    $query = new MasVideos_TV_Show_Playlist_Query( $args );
    return $query->get_tv_show_playlists();
}

/**
 * Main function for returning tv_show_playlists, uses the MasVideos_TV_Show_Playlist_Factory class.
 *
 * @since 1.0.0
 *
 * @param mixed $the_tv_show_playlist Post object or post ID of the tv_show_playlist.
 * @return MasVideos_TV_Show_Playlist|null|false
 */
function masvideos_get_tv_show_playlist( $the_tv_show_playlist = false ) {
    if ( ! did_action( 'masvideos_init' ) ) {
        /* translators: 1: masvideos_get_tv_show_playlist 2: masvideos_init */
        _doing_it_wrong( __FUNCTION__, sprintf( __( '%1$s should not be called before the %2$s action.', 'masvideos' ), 'masvideos_get_tv_show_playlist', 'masvideos_init' ), '1.0.0' );
        return false;
    }
    return MasVideos()->tv_show_playlist_factory->get_tv_show_playlist( $the_tv_show_playlist );
}

/**
 * Clear all transients cache for tv show playlist data.
 *
 * @param int $post_id (default: 0).
 */
function masvideos_delete_tv_show_playlist_transients( $post_id = 0 ) {
    // Core transients.
    $transients_to_clear = array(
        'masvideos_featured_tv_show_playlists',
        'masvideos_count_comments',
    );

    // Transient names that include an ID.
    $post_transient_names = array(
        'masvideos_tv_show_playlist_children_',
        'masvideos_related_',
    );

    if ( $post_id > 0 ) {
        foreach ( $post_transient_names as $transient ) {
            $transients_to_clear[] = $transient . $post_id;
        }

        // Does this tv show playlist have a parent?
        $tv_show_playlist = masvideos_get_tv_show_playlist( $post_id );

        if ( $tv_show_playlist ) {
            if ( $tv_show_playlist->get_parent_id() > 0 ) {
                masvideos_delete_tv_show_playlist_transients( $tv_show_playlist->get_parent_id() );
            }
        }
    }

    // Delete transients.
    foreach ( $transients_to_clear as $transient ) {
        delete_transient( $transient );
    }

    // Increments the transient version to invalidate cache.
    MasVideos_Cache_Helper::get_transient_version( 'tv_show_playlist', true );

    do_action( 'masvideos_delete_tv_show_playlist_transients', $post_id );
}

/**
 * Update a playlist.
 *
 * @since  1.0.0
 * @param  int   $id   Playlist ID.
 * @param  array $args Playlist arguments.
 * @return int|WP_Error
 */
function masvideos_update_tv_show_playlist( $id = 0, $args ) {
    $tv_show_playlist = masvideos_get_tv_show_playlist( $id );

    if ( ! $tv_show_playlist ) {
        $current_user_id = get_current_user_id();
        $slug = uniqid( $current_user_id );
        $tv_show_playlist = new MasVideos_TV_Show_Playlist( $id );
        $tv_show_playlist->set_slug( $slug );
    }

    $tv_show_playlist->set_name( $args['name'] );
    $tv_show_playlist->set_status( $args['status'] );
    $tv_show_playlist->save();

    return $tv_show_playlist;
}

/**
 * Add tv show to playlist.
 *
 * @since  1.0.0
 * @param  int   $id            Playlist ID.
 * @param  int   $tv_show_id    TV Show ID.
 * @return int|WP_Error
 */
function masvideos_add_tv_show_to_playlist( $id, $tv_show_id ) {
    $tv_show_playlist = masvideos_get_tv_show_playlist( $id );

    if ( ! $tv_show_playlist ) {
        return false;
    }

    if( masvideos_is_tv_show_added_to_playlist( $id, $tv_show_id ) ) {
        return false;
    }

    $tv_show_ids = $tv_show_playlist->get_tv_show_ids( 'edit' );

    if( is_array( $tv_show_ids ) ) {
        $tv_show_ids[] = $tv_show_id;
    } else {
        $tv_show_ids = array( $tv_show_id );
    }

    $tv_show_playlist->set_tv_show_ids( $tv_show_ids );
    $tv_show_playlist->save();

    return $tv_show_playlist;
}

/**
 * Remove tv show from playlist.
 *
 * @since  1.0.0
 * @param  int   $id            Playlist ID.
 * @param  int   $tv_show_id    TV Show ID.
 * @return int|WP_Error
 */
function masvideos_remove_tv_show_from_playlist( $id, $tv_show_id ) {
    $tv_show_playlist = masvideos_get_tv_show_playlist( $id );

    if ( ! $tv_show_playlist ) {
        return false;
    }

    if( ! masvideos_is_tv_show_added_to_playlist( $id, $tv_show_id ) ) {
        return false;
    }

    $tv_show_ids = $tv_show_playlist->get_tv_show_ids( 'edit' );

    if ( false === $key = array_search( $tv_show_id, $tv_show_ids ) ) {
        return false;
    }

    array_splice( $tv_show_ids, $key, 1 );

    $tv_show_playlist->set_tv_show_ids( $tv_show_ids );
    $tv_show_playlist->save();

    return $tv_show_playlist;
}

/**
 * Check a tv show added to a playlist.
 *
 * @since  1.0.0
 * @param  int   $id        Playlist ID.
 * @param  int   $tv_show_id  TV Show ID.
 * @return int|WP_Error
 */
function masvideos_is_tv_show_added_to_playlist( $id, $tv_show_id ) {
    $tv_show_playlist = masvideos_get_tv_show_playlist( $id );

    if ( ! $tv_show_playlist ) {
        return false;
    }

    $tv_show_ids = $tv_show_playlist->get_tv_show_ids();

    if( is_array( $tv_show_ids ) && in_array( $tv_show_id, $tv_show_ids ) ) {
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
function masvideos_get_current_user_tv_show_playlists() {
    if ( is_user_logged_in() ) {
        $current_user_id = get_current_user_id();

        $args = array(
            'post_type'         => 'tv_show_playlist',
            'post_status'       => array_keys( masvideos_get_tv_show_playlist_visibility_options() ),
            'posts_per_page'    => -1,
            'author'            => $current_user_id
        );

        $current_user_posts = get_posts( $args );

        return $current_user_posts;
    }

    return false;
}

/**
 * Get single playlist's all tv shows.
 *
 * @since  1.0.0
 * @return array|boolean
 */
function masvideos_single_tv_show_playlist_tv_shows( $id ) {
    $tv_show_playlist = masvideos_get_tv_show_playlist( $id );

    if ( ! $tv_show_playlist ) {
        return false;
    }

    return $tv_show_playlist->get_tv_show_ids();
}

/**
 * Set current user's watched history to playlist.
 *
 * @since  1.0.0
 * @return array|boolean
 */
function masvideos_set_watched_tv_show_history_to_playlist() {
    if ( is_user_logged_in() && is_tv_show() ) {
        global $tv_show;

        $current_user_id = get_current_user_id();

        $playlist_id = get_user_option( 'masvideos_history_tv_show_playlist_id', $current_user_id );

        if( empty( $playlist_id ) || is_null( get_post( $playlist_id ) ) ) {
            $args = array(
                'name'      => esc_html__( 'History', 'masvideos' ),
                'status'    => 'private',
            );
            $tv_show_playlist = masvideos_update_tv_show_playlist( 0, $args );
            $playlist_id = $tv_show_playlist->get_id();
            update_user_option( $current_user_id, 'masvideos_history_tv_show_playlist_id', $playlist_id );
        }

        $tv_show_playlist = masvideos_add_tv_show_to_playlist( $playlist_id, $tv_show->get_id() );

        return $tv_show_playlist;
    }
}

/**
 * Update single tv show's link for playlist.
 *
 * @since  1.0.0
 * @return array|boolean
 */
function masvideos_loop_tv_show_link_for_tv_show_playlist( $link, $tv_show ) {
    if ( is_tv_show_playlist() ) {
        $tv_show_playlist_id = get_queried_object_id();
        return add_query_arg( 'tv_show_playlist_id', $tv_show_playlist_id, $link );
    } elseif( is_tv_show() ) {
        $tv_show_playlist_id = isset( $_GET['tv_show_playlist_id'] ) ? absint( $_GET['tv_show_playlist_id'] ) : 0;
        return add_query_arg( 'tv_show_playlist_id', $tv_show_playlist_id, $link );
    }

    return $link;  
}

/**
 * Get tv show playlist visibility options.
 *
 * @since 1.0.0
 * @return array
 */
function masvideos_get_tv_show_playlist_visibility_options() {
    return apply_filters(
        'masvideos_tv_show_playlist_visibility_options', array(
            'publish' => __( 'Public', 'masvideos' ),
            'private' => __( 'Private', 'masvideos' ),
        )
    );
}
