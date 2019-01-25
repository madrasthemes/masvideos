<?php
/**
 * Episode Factory
 *
 * The MasVideos episode factory creating the right episode object.
 *
 * @package MasVideos/Classes
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Episode factory class.
 */
class MasVideos_Episode_Factory {

    /**
     * Get a episode.
     *
     * @param mixed $episode_id MasVideos_Episode|WP_Post|int|bool $episode Episode instance, post instance, numeric or false to use global $post.
     * @return MasVideos_Episode|bool Episode object or null if the episode cannot be loaded.
     */
    public function get_episode( $episode_id = false ) {
        $episode_id = $this->get_episode_id( $episode_id );

        if ( ! $episode_id ) {
            return false;
        }

        $classname = $this->get_episode_classname( $episode_id );

        try {
            return new $classname( $episode_id );
        } catch ( Exception $e ) {
            return false;
        }
    }

    /**
     * Gets a episode classname and allows filtering. Returns MasVideos_Episode if the class does not exist.
     *
     * @since  1.0.0
     * @param  int    $episode_id   Episode ID.
     * @return string
     */
    public static function get_episode_classname( $episode_id ) {
        $classname = apply_filters( 'masvideos_episode_class', 'MasVideos_Episode', $episode_id );

        if ( ! $classname || ! class_exists( $classname ) ) {
            $classname = 'MasVideos_Episode';
        }

        return $classname;
    }

    /**
     * Get the episode ID depending on what was passed.
     *
     * @since  1.0.0
     * @param  MasVideos_Episode|WP_Post|int|bool $episode Episode instance, post instance, numeric or false to use global $post.
     * @return int|bool false on failure
     */
    private function get_episode_id( $episode ) {
        global $post;

        if ( false === $episode && isset( $post, $post->ID ) && 'episode' === get_post_type( $post->ID ) ) {
            return absint( $post->ID );
        } elseif ( is_numeric( $episode ) ) {
            return $episode;
        } elseif ( $episode instanceof MasVideos_Episode ) {
            return $episode->get_id();
        } elseif ( ! empty( $episode->ID ) ) {
            return $episode->ID;
        } else {
            return false;
        }
    }
}
