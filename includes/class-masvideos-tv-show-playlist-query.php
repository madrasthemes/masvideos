<?php
/**
 * Class for parameter-based TV Show Playlist querying
 *
 * @package  MasVideos/Classes
 * @version  1.0.0
 * @since    1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * TV Show Playlist query class.
 */
class MasVideos_TV_Show_Playlist_Query extends MasVideos_Object_Query {

    /**
     * Valid query vars for tv show playlist.
     *
     * @return array
     */
    protected function get_default_query_vars() {
        return array_merge(
            parent::get_default_query_vars(),
            array(
                'status'            => array( 'draft', 'pending', 'private', 'publish' ),
                'type'              => array( 'tv_show_playlist' ),
                'limit'             => get_option( 'posts_per_page' ),
                'include'           => array(),
                'date_created'      => '',
                'date_modified'     => '',
            )
        );
    }

    /**
     * Get tv show playlist matching the current query vars.
     *
     * @return array|object of MasVideos_TV_Show_Playlist objects
     */
    public function get_tv_show_playlists() {
        $args    = apply_filters( 'masvideos_tv_show_playlist_object_query_args', $this->get_query_vars() );
        $results = MasVideos_Data_Store::load( 'tv_show_playlist' )->query( $args );
        return apply_filters( 'masvideos_tv_show_playlist_object_query', $results, $args );
    }
}
