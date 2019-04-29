<?php
/**
 * MasVideos Video Playlist Functions
 *
 * Functions for video playlist specific things.
 *
 * @package MasVideos/Functions
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Standard way of retrieving video playlists based on certain parameters.
 *
 * This function should be used for video playlist retrieval so that we have a data agnostic
 * way to get a list of video playlists.
 *
 * @since  1.0.0
 * @param  array $args Array of args (above).
 * @return array|stdClass Number of pages and an array of video playlist objects if
 *                             paginate is true, or just an array of values.
 */
function masvideos_get_video_playlists( $args ) {
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

    $query = new MasVideos_Video_Playlist_Query( $args );
    return $query->get_video_playlists();
}

/**
 * Main function for returning video_playlists, uses the MasVideos_Video_Playlist_Factory class.
 *
 * @since 1.0.0
 *
 * @param mixed $the_video_playlist Post object or post ID of the video_playlist.
 * @return MasVideos_Video_Playlist|null|false
 */
function masvideos_get_video_playlist( $the_video_playlist = false ) {
    if ( ! did_action( 'masvideos_init' ) ) {
        /* translators: 1: masvideos_get_video_playlist 2: masvideos_init */
        _doing_it_wrong( __FUNCTION__, sprintf( __( '%1$s should not be called before the %2$s action.', 'masvideos' ), 'masvideos_get_video_playlist', 'masvideos_init' ), '1.0.0' );
        return false;
    }
    return MasVideos()->video_playlist_factory->get_video_playlist( $the_video_playlist );
}

/**
 * Clear all transients cache for video playlist data.
 *
 * @param int $post_id (default: 0).
 */
function masvideos_delete_video_playlist_transients( $post_id = 0 ) {
    // Core transients.
    $transients_to_clear = array(
        'masvideos_featured_video_playlists',
        'masvideos_count_comments',
    );

    // Transient names that include an ID.
    $post_transient_names = array(
        'masvideos_video_playlist_children_',
        'masvideos_related_',
    );

    if ( $post_id > 0 ) {
        foreach ( $post_transient_names as $transient ) {
            $transients_to_clear[] = $transient . $post_id;
        }

        // Does this video playlist have a parent?
        $video_playlist = masvideos_get_video_playlist( $post_id );

        if ( $video_playlist ) {
            if ( $video_playlist->get_parent_id() > 0 ) {
                masvideos_delete_video_playlist_transients( $video_playlist->get_parent_id() );
            }
        }
    }

    // Delete transients.
    foreach ( $transients_to_clear as $transient ) {
        delete_transient( $transient );
    }

    // Increments the transient version to invalidate cache.
    MasVideos_Cache_Helper::get_transient_version( 'video_playlist', true );

    do_action( 'masvideos_delete_video_playlist_transients', $post_id );
}

/**
 * Update a playlist.
 *
 * @since  1.0.0
 * @param  int   $id   Playlist ID.
 * @param  array $args Playlist arguments.
 * @return int|WP_Error
 */
function masvideos_update_video_playlist( $id = 0, $args ) {
    $video_playlist = masvideos_get_video_playlist( $id );

    if ( ! $video_playlist ) {
        $current_user_id = get_current_user_id();
        $slug = uniqid( $current_user_id );
        $video_playlist = new MasVideos_Video_Playlist( $id );
        $video_playlist->set_slug( $slug );
    }

    $video_playlist->set_name( $args['name'] );
    $video_playlist->set_status( $args['status'] );
    $video_playlist->save();

    return $video_playlist;
}

/**
 * Add video to playlist.
 *
 * @since  1.0.0
 * @param  int   $id        Playlist ID.
 * @param  int   $video_id  Video ID.
 * @return int|WP_Error
 */
function masvideos_add_video_to_playlist( $id, $video_id ) {
    $video_playlist = masvideos_get_video_playlist( $id );

    if ( ! $video_playlist ) {
        return false;
    }

    if( masvideos_is_video_added_to_playlist( $id, $video_id ) ) {
        return false;
    }

    $video_ids = $video_playlist->get_video_ids( 'edit' );

    if( is_array( $video_ids ) ) {
        $video_ids[] = $video_id;
    } else {
        $video_ids = array( $video_id );
    }

    $video_playlist->set_video_ids( $video_ids );
    $video_playlist->save();

    return $video_playlist;
}

/**
 * Remove video from playlist.
 *
 * @since  1.0.0
 * @param  int   $id        Playlist ID.
 * @param  int   $video_id  Video ID.
 * @return int|WP_Error
 */
function masvideos_remove_video_from_playlist( $id, $video_id ) {
    $video_playlist = masvideos_get_video_playlist( $id );

    if ( ! $video_playlist ) {
        return false;
    }

    if( ! masvideos_is_video_added_to_playlist( $id, $video_id ) ) {
        return false;
    }

    $video_ids = $video_playlist->get_video_ids( 'edit' );

    if ( false === $key = array_search( $video_id, $video_ids ) ) {
        return false;
    }

    array_splice( $video_ids, $key, 1 );

    $video_playlist->set_video_ids( $video_ids );
    $video_playlist->save();

    return $video_playlist;
}

/**
 * Check a video added to a playlist.
 *
 * @since  1.0.0
 * @param  int   $id        Playlist ID.
 * @param  int   $video_id  Video ID.
 * @return int|WP_Error
 */
function masvideos_is_video_added_to_playlist( $id, $video_id ) {
    $video_playlist = masvideos_get_video_playlist( $id );

    if ( ! $video_playlist ) {
        return false;
    }

    $video_ids = $video_playlist->get_video_ids();

    if( is_array( $video_ids ) && in_array( $video_id, $video_ids ) ) {
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
function masvideos_get_current_user_video_playlists() {
    if ( is_user_logged_in() ) {
        $current_user_id = get_current_user_id();

        $args = array(
            'post_type'         => 'video_playlist',
            'post_status'       => array_keys( masvideos_get_video_playlist_visibility_options() ),
            'posts_per_page'    => -1,
            'author'            => $current_user_id
        );

        $current_user_posts = get_posts( $args );

        return $current_user_posts;
    }

    return false;
}

/**
 * Get single playlist's all videos.
 *
 * @since  1.0.0
 * @return array|boolean
 */
function masvideos_single_video_playlist_videos( $id ) {
    $video_playlist = masvideos_get_video_playlist( $id );

    if ( ! $video_playlist ) {
        return false;
    }

    return $video_playlist->get_video_ids();
}

/**
 * Set current user's watched history to playlist.
 *
 * @since  1.0.0
 * @return array|boolean
 */
function masvideos_set_watched_video_history_to_playlist() {
    if ( is_user_logged_in() && is_video() ) {
        global $video;

        $current_user_id = get_current_user_id();

        $playlist_id = get_user_option( 'masvideos_history_video_playlist_id', $current_user_id );

        if( empty( $playlist_id ) || is_null( get_post( $playlist_id ) ) ) {
            $args = array(
                'name'      => esc_html__( 'History', 'masvideos' ),
                'status'    => 'private',
            );
            $video_playlist = masvideos_update_video_playlist( 0, $args );
            $playlist_id = $video_playlist->get_id();
            update_user_option( $current_user_id, 'masvideos_history_video_playlist_id', $playlist_id );
        }

        $video_playlist = masvideos_add_video_to_playlist( $playlist_id, $video->get_id() );

        return $video_playlist;
    }
}

/**
 * Update single video's link for playlist.
 *
 * @since  1.0.0
 * @return array|boolean
 */
function masvideos_loop_video_link_for_video_playlist( $link, $video ) {
    if ( is_video_playlist() ) {
        $video_playlist_id = get_queried_object_id();
        return add_query_arg( 'video_playlist_id', $video_playlist_id, $link );
    } elseif( is_video() ) {
        $video_playlist_id = isset( $_GET['video_playlist_id'] ) ? absint( $_GET['video_playlist_id'] ) : 0;
        return add_query_arg( 'video_playlist_id', $video_playlist_id, $link );
    }

    return $link;  
}

/**
 * Get video playlist visibility options.
 *
 * @since 1.0.0
 * @return array
 */
function masvideos_get_video_playlist_visibility_options() {
    return apply_filters(
        'masvideos_video_playlist_visibility_options', array(
            'publish' => __( 'Public', 'masvideos' ),
            'private' => __( 'Private', 'masvideos' ),
        )
    );
}
