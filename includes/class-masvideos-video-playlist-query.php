<?php
/**
 * Class for parameter-based Video Playlist querying
 *
 * @package  MasVideos/Classes
 * @version  1.0.0
 * @since    1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Video Playlist query class.
 */
class MasVideos_Video_Playlist_Query extends MasVideos_Object_Query {

    /**
     * Valid query vars for video playlist.
     *
     * @return array
     */
    protected function get_default_query_vars() {
        return array_merge(
            parent::get_default_query_vars(),
            array(
                'status'            => array( 'draft', 'pending', 'private', 'publish' ),
                'type'              => array( 'video_playlist' ),
                'limit'             => get_option( 'posts_per_page' ),
                'include'           => array(),
                'date_created'      => '',
                'date_modified'     => '',
            )
        );
    }

    /**
     * Get video playlist matching the current query vars.
     *
     * @return array|object of MasVideos_Video_Playlist objects
     */
    public function get_video_playlists() {
        $args    = apply_filters( 'masvideos_video_playlist_object_query_args', $this->get_query_vars() );
        $results = MasVideos_Data_Store::load( 'video_playlist' )->query( $args );
        return apply_filters( 'masvideos_video_playlist_object_query', $results, $args );
    }
}
