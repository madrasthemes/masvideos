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
