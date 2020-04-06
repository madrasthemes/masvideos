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

/**
 * is_masvideos - Returns true if on a page which uses MasVideos templates.
 *
 * @return bool
 */
function is_masvideos() {
    return apply_filters( 'is_masvideos', is_videos() || is_video_taxonomy() || is_video() || is_movies() || is_movie_taxonomy() || is_movie() || is_episodes() || is_episode_taxonomy() || is_episode() || is_tv_shows() || is_tv_show_taxonomy() || is_tv_show() );
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

if ( ! function_exists( 'is_persons' ) ) {

    /**
     * Is_shop - Returns true when viewing the person type archive (shop).
     *
     * @return bool
     */
    function is_persons() {
        return ( is_post_type_archive( 'person' ) || is_page( masvideos_get_page_id( 'persons' ) ) );
    }
}

if ( ! function_exists( 'is_person_taxonomy' ) ) {

    /**
     * Is_person_taxonomy - Returns true when viewing a person taxonomy archive.
     *
     * @return bool
     */
    function is_person_taxonomy() {
        return is_tax( get_object_taxonomies( 'person' ) );
    }
}

if ( ! function_exists( 'is_person_category' ) ) {

    /**
     * Is_person_category - Returns true when viewing a person category.
     *
     * @param  string $term (default: '') The term slug your checking for. Leave blank to return true on any.
     * @return bool
     */
    function is_person_category( $term = '' ) {
        return is_tax( 'person_cat', $term );
    }
}

if ( ! function_exists( 'is_person_tag' ) ) {

    /**
     * Is_person_tag - Returns true when viewing a person tag.
     *
     * @param  string $term (default: '') The term slug your checking for. Leave blank to return true on any.
     * @return bool
     */
    function is_person_tag( $term = '' ) {
        return is_tax( 'person_tag', $term );
    }
}

if ( ! function_exists( 'is_person' ) ) {

    /**
     * Is_person - Returns true when viewing a single person.
     *
     * @return bool
     */
    function is_person() {
        return is_singular( array( 'person' ) );
    }
}

if ( ! function_exists( 'taxonomy_is_person_attribute' ) ) {

    /**
     * Returns true when the passed taxonomy name is a person attribute.
     *
     * @uses   $maspersons_attributes global which stores taxonomy names upon registration
     * @param  string $name of the attribute.
     * @return bool
     */
    function taxonomy_is_person_attribute( $name ) {
        global $masvideos_attributes;

        return taxonomy_exists( $name ) && isset( $masvideos_attributes['person'] ) && array_key_exists( $name, (array) $masvideos_attributes['person'] );
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

        return taxonomy_exists( $name ) && isset( $masvideos_attributes['video'] ) && array_key_exists( $name, (array) $masvideos_attributes['video'] );
    }
}

if ( ! function_exists( 'is_video_playlist' ) ) {

    /**
     * Is_video_playlist - Returns true when viewing a single video playlist.
     *
     * @return bool
     */
    function is_video_playlist() {
        return is_singular( array( 'video_playlist' ) );
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

if ( ! function_exists( 'is_movie_genre' ) ) {

    /**
     * Is_movie_genre - Returns true when viewing a movie category.
     *
     * @param  string $term (default: '') The term slug your checking for. Leave blank to return true on any.
     * @return bool
     */
    function is_movie_genre( $term = '' ) {
        return is_tax( 'movie_genre', $term );
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

        return taxonomy_exists( $name ) && isset( $masvideos_attributes['movie'] ) && array_key_exists( $name, (array) $masvideos_attributes['movie'] );
    }
}

if ( ! function_exists( 'is_movie_playlist' ) ) {

    /**
     * Is_movie_playlist - Returns true when viewing a single movie playlist.
     *
     * @return bool
     */
    function is_movie_playlist() {
        return is_singular( array( 'movie_playlist' ) );
    }
}

if ( ! function_exists( 'is_episodes' ) ) {

    /**
     * Is_shop - Returns true when viewing the episode type archive (shop).
     *
     * @return bool
     */
    function is_episodes() {
        return ( is_post_type_archive( 'episode' ) || is_page( masvideos_get_page_id( 'episodes' ) ) );
    }
}

if ( ! function_exists( 'is_episode_taxonomy' ) ) {

    /**
     * Is_episode_taxonomy - Returns true when viewing a episode taxonomy archive.
     *
     * @return bool
     */
    function is_episode_taxonomy() {
        return is_tax( get_object_taxonomies( 'episode' ) );
    }
}

if ( ! function_exists( 'is_episode_genre' ) ) {

    /**
     * Is_episode_genre - Returns true when viewing a episode category.
     *
     * @param  string $term (default: '') The term slug your checking for. Leave blank to return true on any.
     * @return bool
     */
    function is_episode_genre( $term = '' ) {
        return is_tax( 'episode_genre', $term );
    }
}

if ( ! function_exists( 'is_episode_tag' ) ) {

    /**
     * Is_episode_tag - Returns true when viewing a episode tag.
     *
     * @param  string $term (default: '') The term slug your checking for. Leave blank to return true on any.
     * @return bool
     */
    function is_episode_tag( $term = '' ) {
        return is_tax( 'episode_tag', $term );
    }
}

if ( ! function_exists( 'is_episode' ) ) {

    /**
     * Is_episode - Returns true when viewing a single episode.
     *
     * @return bool
     */
    function is_episode() {
        return is_singular( array( 'episode' ) );
    }
}

if ( ! function_exists( 'taxonomy_is_episode_attribute' ) ) {

    /**
     * Returns true when the passed taxonomy name is a episode attribute.
     *
     * @uses   $masepisodes_attributes global which stores taxonomy names upon registration
     * @param  string $name of the attribute.
     * @return bool
     */
    function taxonomy_is_episode_attribute( $name ) {
        global $masvideos_attributes;

        return taxonomy_exists( $name ) && isset( $masvideos_attributes['episode'] ) && array_key_exists( $name, (array) $masvideos_attributes['episode'] );
    }
}

if ( ! function_exists( 'is_tv_shows' ) ) {

    /**
     * Is_shop - Returns true when viewing the tv_show type archive (shop).
     *
     * @return bool
     */
    function is_tv_shows() {
        return ( is_post_type_archive( 'tv_show' ) || is_page( masvideos_get_page_id( 'tv_shows' ) ) );
    }
}

if ( ! function_exists( 'is_tv_show_taxonomy' ) ) {

    /**
     * Is_tv_show_taxonomy - Returns true when viewing a tv_show taxonomy archive.
     *
     * @return bool
     */
    function is_tv_show_taxonomy() {
        return is_tax( get_object_taxonomies( 'tv_show' ) );
    }
}

if ( ! function_exists( 'is_tv_show_genre' ) ) {

    /**
     * Is_tv_show_genre - Returns true when viewing a tv_show category.
     *
     * @param  string $term (default: '') The term slug your checking for. Leave blank to return true on any.
     * @return bool
     */
    function is_tv_show_genre( $term = '' ) {
        return is_tax( 'tv_show_genre', $term );
    }
}

if ( ! function_exists( 'is_tv_show_tag' ) ) {

    /**
     * Is_tv_show_tag - Returns true when viewing a tv_show tag.
     *
     * @param  string $term (default: '') The term slug your checking for. Leave blank to return true on any.
     * @return bool
     */
    function is_tv_show_tag( $term = '' ) {
        return is_tax( 'tv_show_tag', $term );
    }
}

if ( ! function_exists( 'is_tv_show' ) ) {

    /**
     * Is_tv_show - Returns true when viewing a single tv_show.
     *
     * @return bool
     */
    function is_tv_show() {
        return is_singular( array( 'tv_show' ) );
    }
}

if ( ! function_exists( 'taxonomy_is_tv_show_attribute' ) ) {

    /**
     * Returns true when the passed taxonomy name is a tv_show attribute.
     *
     * @uses   $mastv_shows_attributes global which stores taxonomy names upon registration
     * @param  string $name of the attribute.
     * @return bool
     */
    function taxonomy_is_tv_show_attribute( $name ) {
        global $masvideos_attributes;

        return taxonomy_exists( $name ) && isset( $masvideos_attributes['tv_show'] ) && array_key_exists( $name, (array) $masvideos_attributes['tv_show'] );
    }
}

if ( ! function_exists( 'is_tv_show_playlist' ) ) {

    /**
     * Is_tv_show_playlist - Returns true when viewing a single tv show playlist.
     *
     * @return bool
     */
    function is_tv_show_playlist() {
        return is_singular( array( 'tv_show_playlist' ) );
    }
}

/**
 * Checks whether the content passed contains a specific short code.
 *
 * @param  string $tag Shortcode tag to check.
 * @return bool
 */
function masvideos_post_content_has_shortcode( $tag = '' ) {
    global $post;

    return is_singular() && is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, $tag );
}

if ( ! function_exists( 'masvideos_is_account_page' ) ) {

    /**
     * Returns true when viewing an account page.
     *
     * @return bool
     */
    function masvideos_is_account_page() {
        $page_id = masvideos_get_page_id( 'myaccount' );

        return ( $page_id && is_page( $page_id ) ) || masvideos_post_content_has_shortcode( 'mas_my_account' ) || apply_filters( 'masvideos_is_account_page', false );
    }
}

if ( ! function_exists( 'masvideos_is_video_upload_page' ) ) {

    /**
     * Returns true when viewing an Video Upload page.
     *
     * @return bool
     */
    function masvideos_is_video_upload_page() {
        $page_id = masvideos_get_page_id( 'upload_video' );

        return ( $page_id && is_page( $page_id ) ) || masvideos_post_content_has_shortcode( 'mas_upload_video' ) || apply_filters( 'masvideos_is_video_upload_page', false );
    }
}

if ( ! function_exists( 'masvideos_is_edit_account_page' ) ) {

    /**
     * Check for edit account page.
     * Returns true when viewing the edit account page.
     *
     * @return bool
     */
    function masvideos_is_edit_account_page() {
        global $wp;

        $page_id = masvideos_get_page_id( 'myaccount' );

        return ( $page_id && is_page( $page_id ) && isset( $wp->query_vars['edit-account'] ) );
    }
}

if ( ! function_exists( 'is_masvideos_endpoint_url' ) ) {

    /**
     * Is_masvideos_endpoint_url - Check if an endpoint is showing.
     *
     * @param string|false $endpoint Whether endpoint.
     * @return bool
     */
    function is_masvideos_endpoint_url( $endpoint = false ) {
        global $wp;

        $masvideos_endpoints = MasVideos()->query->get_query_vars();

        if ( false !== $endpoint ) {
            if ( ! isset( $masvideos_endpoints[ $endpoint ] ) ) {
                return false;
            } else {
                $endpoint_var = $masvideos_endpoints[ $endpoint ];
            }

            return isset( $wp->query_vars[ $endpoint_var ] );
        } else {
            foreach ( $masvideos_endpoints as $key => $value ) {
                if ( isset( $wp->query_vars[ $key ] ) ) {
                    return true;
                }
            }

            return false;
        }
    }
}