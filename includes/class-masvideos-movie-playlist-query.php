<?php
/**
 * Class for parameter-based Movie Playlist querying
 *
 * @package  MasVideos/Classes
 * @version  1.0.0
 * @since    1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Movie Playlist query class.
 */
class MasVideos_Movie_Playlist_Query extends MasVideos_Object_Query {

    /**
     * Valid query vars for movie playlist.
     *
     * @return array
     */
    protected function get_default_query_vars() {
        return array_merge(
            parent::get_default_query_vars(),
            array(
                'status'            => array( 'draft', 'pending', 'private', 'publish' ),
                'type'              => array( 'movie_playlist' ),
                'limit'             => get_option( 'posts_per_page' ),
                'include'           => array(),
                'date_created'      => '',
                'date_modified'     => '',
            )
        );
    }

    /**
     * Get movie playlist matching the current query vars.
     *
     * @return array|object of MasVideos_Movie_Playlist objects
     */
    public function get_movie_playlists() {
        $args    = apply_filters( 'masvideos_movie_playlist_object_query_args', $this->get_query_vars() );
        $results = MasVideos_Data_Store::load( 'movie_playlist' )->query( $args );
        return apply_filters( 'masvideos_movie_playlist_object_query', $results, $args );
    }
}
