<?php
/**
 * Person Factory
 *
 * The MasVideos person factory creating the right person object.
 *
 * @package MasVideos/Classes
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Person factory class.
 */
class MasVideos_Person_Factory {

    /**
     * Get a person.
     *
     * @param mixed $person_id MasVideos_Person|WP_Post|int|bool $person Person instance, post instance, numeric or false to use global $post.
     * @return MasVideos_Person|bool Person object or null if the person cannot be loaded.
     */
    public function get_person( $person_id = false ) {
        $person_id = $this->get_person_id( $person_id );

        if ( ! $person_id ) {
            return false;
        }

        $classname = $this->get_person_classname( $person_id );

        try {
            return new $classname( $person_id );
        } catch ( Exception $e ) {
            return false;
        }
    }

    /**
     * Gets a person classname and allows filtering. Returns MasVideos_Person if the class does not exist.
     *
     * @since  1.0.0
     * @param  int    $person_id   Person ID.
     * @return string
     */
    public static function get_person_classname( $person_id ) {
        $classname = apply_filters( 'masvideos_person_class', 'MasVideos_Person', $person_id );

        if ( ! $classname || ! class_exists( $classname ) ) {
            $classname = 'MasVideos_Person';
        }

        return $classname;
    }

    /**
     * Get the person ID depending on what was passed.
     *
     * @since  1.0.0
     * @param  MasVideos_Person|WP_Post|int|bool $person Person instance, post instance, numeric or false to use global $post.
     * @return int|bool false on failure
     */
    private function get_person_id( $person ) {
        global $post;

        if ( false === $person && isset( $post, $post->ID ) && 'person' === get_post_type( $post->ID ) ) {
            return absint( $post->ID );
        } elseif ( is_numeric( $person ) ) {
            return $person;
        } elseif ( $person instanceof MasVideos_Person ) {
            return $person->get_id();
        } elseif ( ! empty( $person->ID ) ) {
            return $person->ID;
        } else {
            return false;
        }
    }
}
