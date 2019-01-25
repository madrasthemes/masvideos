<?php
/**
 * TV Show Factory
 *
 * The MasVideos tv show factory creating the right tv show object.
 *
 * @package MasVideos/Classes
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * TV Show factory class.
 */
class MasVideos_TV_Show_Factory {

    /**
     * Get a tv show.
     *
     * @param mixed $tv_show_id MasVideos_TV_Show|WP_Post|int|bool $tv_show TV Show instance, post instance, numeric or false to use global $post.
     * @return MasVideos_TV_Show|bool TV Show object or null if the tv show cannot be loaded.
     */
    public function get_tv_show( $tv_show_id = false ) {
        $tv_show_id = $this->get_tv_show_id( $tv_show_id );

        if ( ! $tv_show_id ) {
            return false;
        }

        $classname = $this->get_tv_show_classname( $tv_show_id );

        try {
            return new $classname( $tv_show_id );
        } catch ( Exception $e ) {
            return false;
        }
    }

    /**
     * Gets a tv show classname and allows filtering. Returns MasVideos_TV_Show if the class does not exist.
     *
     * @since  1.0.0
     * @param  int    $tv_show_id   TV Show ID.
     * @return string
     */
    public static function get_tv_show_classname( $tv_show_id ) {
        $classname = apply_filters( 'masvideos_tv_show_class', 'MasVideos_TV_Show', $tv_show_id );

        if ( ! $classname || ! class_exists( $classname ) ) {
            $classname = 'MasVideos_TV_Show';
        }

        return $classname;
    }

    /**
     * Get the tv show ID depending on what was passed.
     *
     * @since  1.0.0
     * @param  MasVideos_TV_Show|WP_Post|int|bool $tv_show TV Show instance, post instance, numeric or false to use global $post.
     * @return int|bool false on failure
     */
    private function get_tv_show_id( $tv_show ) {
        global $post;

        if ( false === $tv_show && isset( $post, $post->ID ) && 'tv_show' === get_post_type( $post->ID ) ) {
            return absint( $post->ID );
        } elseif ( is_numeric( $tv_show ) ) {
            return $tv_show;
        } elseif ( $tv_show instanceof MasVideos_TV_Show ) {
            return $tv_show->get_id();
        } elseif ( ! empty( $tv_show->ID ) ) {
            return $tv_show->ID;
        } else {
            return false;
        }
    }
}
