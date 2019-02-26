<?php
/**
 * TV Show Playlist Factory
 *
 * The MasVideos tv show playlist factory creating the right tv show playlist object.
 *
 * @package MasVideos/Classes
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * TV Show Playlist factory class.
 */
class MasVideos_TV_Show_Playlist_Factory {

    /**
     * Get a tv show playlist.
     *
     * @param mixed $tv_show_playlist_id MasVideos_TV_Show_Playlist|WP_Post|int|bool $tv_show_playlist TV Show Playlist instance, post instance, numeric or false to use global $post.
     * @return MasVideos_TV_Show_Playlist|bool TV Show Playlist object or null if the tv show playlist cannot be loaded.
     */
    public function get_tv_show_playlist( $tv_show_playlist_id = false ) {
        $tv_show_playlist_id = $this->get_tv_show_playlist_id( $tv_show_playlist_id );

        if ( ! $tv_show_playlist_id ) {
            return false;
        }

        $classname = $this->get_tv_show_playlist_classname( $tv_show_playlist_id );

        try {
            return new $classname( $tv_show_playlist_id );
        } catch ( Exception $e ) {
            return false;
        }
    }

    /**
     * Gets a tv show playlist classname and allows filtering. Returns MasVideos_TV_Show_Playlist if the class does not exist.
     *
     * @since  1.0.0
     * @param  int    $tv_show_playlist_id   TV Show Playlist ID.
     * @return string
     */
    public static function get_tv_show_playlist_classname( $tv_show_playlist_id ) {
        $classname = apply_filters( 'masvideos_tv_show_playlist_class', 'MasVideos_TV_Show_Playlist', $tv_show_playlist_id );

        if ( ! $classname || ! class_exists( $classname ) ) {
            $classname = 'MasVideos_TV_Show_Playlist';
        }

        return $classname;
    }

    /**
     * Get the tv show playlist ID depending on what was passed.
     *
     * @since  1.0.0
     * @param  MasVideos_TV_Show_Playlist|WP_Post|int|bool $tv_show_playlist TV Show Playlist instance, post instance, numeric or false to use global $post.
     * @return int|bool false on failure
     */
    private function get_tv_show_playlist_id( $tv_show_playlist ) {
        global $post;

        if ( false === $tv_show_playlist && isset( $post, $post->ID ) && 'tv_show_playlist' === get_post_type( $post->ID ) ) {
            return absint( $post->ID );
        } elseif ( is_numeric( $tv_show_playlist ) ) {
            return $tv_show_playlist;
        } elseif ( $tv_show_playlist instanceof MasVideos_TV_Show_Playlist ) {
            return $tv_show_playlist->get_id();
        } elseif ( ! empty( $tv_show_playlist->ID ) ) {
            return $tv_show_playlist->ID;
        } else {
            return false;
        }
    }
}
