<?php
/**
 * Video Playlist Factory
 *
 * The MasVideos video playlist factory creating the right video playlist object.
 *
 * @package MasVideos/Classes
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Video Playlist factory class.
 */
class MasVideos_Video_Playlist_Factory {

    /**
     * Get a video playlist.
     *
     * @param mixed $video_playlist_id MasVideos_Video_Playlist|WP_Post|int|bool $video_playlist Video Playlist instance, post instance, numeric or false to use global $post.
     * @return MasVideos_Video_Playlist|bool Video Playlist object or null if the video playlist cannot be loaded.
     */
    public function get_video_playlist( $video_playlist_id = false ) {
        $video_playlist_id = $this->get_video_playlist_id( $video_playlist_id );

        if ( ! $video_playlist_id ) {
            return false;
        }

        $classname = $this->get_video_playlist_classname( $video_playlist_id );

        try {
            return new $classname( $video_playlist_id );
        } catch ( Exception $e ) {
            return false;
        }
    }

    /**
     * Gets a video playlist classname and allows filtering. Returns MasVideos_Video_Playlist if the class does not exist.
     *
     * @since  1.0.0
     * @param  int    $video_playlist_id   Video Playlist ID.
     * @return string
     */
    public static function get_video_playlist_classname( $video_playlist_id ) {
        $classname = apply_filters( 'masvideos_video_playlist_class', 'MasVideos_Video_Playlist', $video_playlist_id );

        if ( ! $classname || ! class_exists( $classname ) ) {
            $classname = 'MasVideos_Video_Playlist';
        }

        return $classname;
    }

    /**
     * Get the video playlist ID depending on what was passed.
     *
     * @since  1.0.0
     * @param  MasVideos_Video_Playlist|WP_Post|int|bool $video_playlist Video Playlist instance, post instance, numeric or false to use global $post.
     * @return int|bool false on failure
     */
    private function get_video_playlist_id( $video_playlist ) {
        global $post;

        if ( false === $video_playlist && isset( $post, $post->ID ) && 'video_playlist' === get_post_type( $post->ID ) ) {
            return absint( $post->ID );
        } elseif ( is_numeric( $video_playlist ) ) {
            return $video_playlist;
        } elseif ( $video_playlist instanceof MasVideos_Video_Playlist ) {
            return $video_playlist->get_id();
        } elseif ( ! empty( $video_playlist->ID ) ) {
            return $video_playlist->ID;
        } else {
            return false;
        }
    }
}
