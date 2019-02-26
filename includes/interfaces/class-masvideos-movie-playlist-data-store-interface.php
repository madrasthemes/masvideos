<?php
/**
 * Movie Playlist Data Store Interface
 *
 * @version 1.0.0
 * @package MasVideos/Interface
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * MasVideos Movie Playlist Data Store Interface
 *
 * Functions that must be defined by movie playlist store classes.
 *
 * @version  1.0.0
 */
interface MasVideos_Movie_Playlist_Data_Store_Interface {

    /**
     * Returns an array of movie playlists.
     *
     * @param array $args @see masvideos_get_movie_playlists.
     * @return array
     */
    public function get_movie_playlists( $args = array() );
}
