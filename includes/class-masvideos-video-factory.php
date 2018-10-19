<?php
/**
 * Video Factory
 *
 * The MasVideos video factory creating the right video object.
 *
 * @package MasVideos/Classes
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Video factory class.
 */
class MasVideos_Video_Factory {

    /**
     * Get a video.
     *
     * @param mixed $video_id MasVideos_Video|WP_Post|int|bool $video Video instance, post instance, numeric or false to use global $post.
     * @return MasVideos_Video|bool Video object or null if the video cannot be loaded.
     */
    public function get_video( $video_id = false ) {
        $video_id = $this->get_video_id( $video_id );

        if ( ! $video_id ) {
            return false;
        }

        $classname = $this->get_video_classname( $video_id );

        try {
            return new $classname( $video_id );
        } catch ( Exception $e ) {
            return false;
        }
    }

    /**
     * Gets a video classname and allows filtering. Returns MasVideos_Video if the class does not exist.
     *
     * @since  1.0.0
     * @param  int    $video_id   Video ID.
     * @return string
     */
    public static function get_video_classname( $video_id ) {
        $classname = apply_filters( 'masvideos_video_class', 'MasVideos_Video', $video_id );

        if ( ! $classname || ! class_exists( $classname ) ) {
            $classname = 'MasVideos_Video';
        }

        return $classname;
    }

    /**
     * Get the video ID depending on what was passed.
     *
     * @since  1.0.0
     * @param  MasVideos_Video|WP_Post|int|bool $video Video instance, post instance, numeric or false to use global $post.
     * @return int|bool false on failure
     */
    private function get_video_id( $video ) {
        global $post;

        if ( false === $video && isset( $post, $post->ID ) && 'video' === get_post_type( $post->ID ) ) {
            return absint( $post->ID );
        } elseif ( is_numeric( $video ) ) {
            return $video;
        } elseif ( $video instanceof MasVideos_Video ) {
            return $video->get_id();
        } elseif ( ! empty( $video->ID ) ) {
            return $video->ID;
        } else {
            return false;
        }
    }
}
