<?php
/**
 * TV Show Playlist Data Store Interface
 *
 * @version 1.0.0
 * @package MasVideos/Interface
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * MasVideos TV Show Playlist Data Store Interface
 *
 * Functions that must be defined by tv show playlist store classes.
 *
 * @version  1.0.0
 */
interface MasVideos_TV_Show_Playlist_Data_Store_Interface {

    /**
     * Returns an array of tv show playlists.
     *
     * @param array $args @see masvideos_get_tv_show_playlists.
     * @return array
     */
    public function get_tv_show_playlists( $args = array() );
}
