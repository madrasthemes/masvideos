<?php
/**
 * Video Playlist Data Store Interface
 *
 * @version 1.0.0
 * @package MasVideos/Interface
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * MasVideos Video Playlist Data Store Interface
 *
 * Functions that must be defined by video playlist store classes.
 *
 * @version  1.0.0
 */
interface MasVideos_Video_Playlist_Data_Store_Interface {

    /**
     * Returns an array of video playlists.
     *
     * @param array $args @see masvideos_get_video_playlists.
     * @return array
     */
    public function get_video_playlists( $args = array() );
}
