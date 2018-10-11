<?php
/**
 * MasVideos Conditional Functions
 *
 * Functions for determining the current query/page.
 *
 * @package     MasVideos/Functions
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'is_ajax' ) ) {

    /**
     * Is_ajax - Returns true when the page is loaded via ajax.
     *
     * @return bool
     */
    function is_ajax() {
        return defined( 'DOING_AJAX' );
    }
}

if ( ! function_exists( 'is_videos' ) ) {

    /**
     * Is_shop - Returns true when viewing the video type archive (shop).
     *
     * @return bool
     */
    function is_videos() {
        return ( is_post_type_archive( 'video' ) || is_page( masvideos_get_page_id( 'videos' ) ) );
    }
}

if ( ! function_exists( 'is_video_taxonomy' ) ) {

    /**
     * Is_video_taxonomy - Returns true when viewing a video taxonomy archive.
     *
     * @return bool
     */
    function is_video_taxonomy() {
        return is_tax( get_object_taxonomies( 'video' ) );
    }
}

if ( ! function_exists( 'is_video_category' ) ) {

    /**
     * Is_video_category - Returns true when viewing a video category.
     *
     * @param  string $term (default: '') The term slug your checking for. Leave blank to return true on any.
     * @return bool
     */
    function is_video_category( $term = '' ) {
        return is_tax( 'video_cat', $term );
    }
}

if ( ! function_exists( 'is_video_tag' ) ) {

    /**
     * Is_video_tag - Returns true when viewing a video tag.
     *
     * @param  string $term (default: '') The term slug your checking for. Leave blank to return true on any.
     * @return bool
     */
    function is_video_tag( $term = '' ) {
        return is_tax( 'video_tag', $term );
    }
}

if ( ! function_exists( 'is_video' ) ) {

    /**
     * Is_video - Returns true when viewing a single video.
     *
     * @return bool
     */
    function is_video() {
        return is_singular( array( 'video' ) );
    }
}

if ( ! function_exists( 'taxonomy_is_video_attribute' ) ) {

    /**
     * Returns true when the passed taxonomy name is a video attribute.
     *
     * @uses   $masvideos_attributes global which stores taxonomy names upon registration
     * @param  string $name of the attribute.
     * @return bool
     */
    function taxonomy_is_video_attribute( $name ) {
        global $masvideos_attributes;

        return taxonomy_exists( $name ) && array_key_exists( $name, (array) $masvideos_attributes['video'] );
    }
}

if ( ! function_exists( 'is_movies' ) ) {

    /**
     * Is_shop - Returns true when viewing the movie type archive (shop).
     *
     * @return bool
     */
    function is_movies() {
        return ( is_post_type_archive( 'movie' ) || is_page( masvideos_get_page_id( 'movies' ) ) );
    }
}

if ( ! function_exists( 'is_movie_taxonomy' ) ) {

    /**
     * Is_movie_taxonomy - Returns true when viewing a movie taxonomy archive.
     *
     * @return bool
     */
    function is_movie_taxonomy() {
        return is_tax( get_object_taxonomies( 'movie' ) );
    }
}

if ( ! function_exists( 'is_movie_category' ) ) {

    /**
     * Is_movie_category - Returns true when viewing a movie category.
     *
     * @param  string $term (default: '') The term slug your checking for. Leave blank to return true on any.
     * @return bool
     */
    function is_movie_category( $term = '' ) {
        return is_tax( 'movie_cat', $term );
    }
}

if ( ! function_exists( 'is_movie_tag' ) ) {

    /**
     * Is_movie_tag - Returns true when viewing a movie tag.
     *
     * @param  string $term (default: '') The term slug your checking for. Leave blank to return true on any.
     * @return bool
     */
    function is_movie_tag( $term = '' ) {
        return is_tax( 'movie_tag', $term );
    }
}

if ( ! function_exists( 'is_movie' ) ) {

    /**
     * Is_movie - Returns true when viewing a single movie.
     *
     * @return bool
     */
    function is_movie() {
        return is_singular( array( 'movie' ) );
    }
}

if ( ! function_exists( 'taxonomy_is_movie_attribute' ) ) {

    /**
     * Returns true when the passed taxonomy name is a movie attribute.
     *
     * @uses   $masmovies_attributes global which stores taxonomy names upon registration
     * @param  string $name of the attribute.
     * @return bool
     */
    function taxonomy_is_movie_attribute( $name ) {
        global $masvideos_attributes;

        return taxonomy_exists( $name ) && array_key_exists( $name, (array) $masvideos_attributes['movie'] );
    }
}
