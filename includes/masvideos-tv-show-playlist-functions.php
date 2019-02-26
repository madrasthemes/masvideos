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
