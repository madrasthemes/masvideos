<?php
/**
 * Masvideos Core Functions
 *
 * General core functions available on both the front-end and admin.
 *
 * @package Masvideos\Functions
 * @version 3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Include core functions (available in both admin and frontend).
require MAS_VIDEOS_ABSPATH . 'includes/masvideos-conditional-functions.php';
require MAS_VIDEOS_ABSPATH . 'includes/masvideos-formatting-functions.php';
require MAS_VIDEOS_ABSPATH . 'includes/masvideos-attribute-functions.php';

/**
 * Define a constant if it is not already defined.
 *
 * @since 1.0.0
 * @param string $name  Constant name.
 * @param string $value Value.
 */
function masvideos_maybe_define_constant( $name, $value ) {
    if ( ! defined( $name ) ) {
        define( $name, $value );
    }
}

/**
 * Get permalink settings for things like videos and taxonomies.
 *
 * This is more inline with WP core behavior which does not localize slugs.
 *
 * @since  1.0.0
 * @return array
 */
function masvideos_get_video_permalink_structure() {
    $saved_permalinks = (array) get_option( 'masvideos_video_permalinks', array() );
    $permalinks       = wp_parse_args(
        array_filter( $saved_permalinks ), array(
            'video_base'                   => _x( 'video', 'slug', 'masvideos' ),
            'video_category_base'          => _x( 'video-category', 'slug', 'masvideos' ),
            'video_tag_base'               => _x( 'video-tag', 'slug', 'masvideos' ),
            'video_attribute_base'         => '',
            'movie_base'                   => _x( 'movie', 'slug', 'masvideos' ),
            'movie_category_base'          => _x( 'movie-category', 'slug', 'masvideos' ),
            'movie_tag_base'               => _x( 'movie-tag', 'slug', 'masvideos' ),
            'movie_attribute_base'         => '',
            'use_verbose_page_rules'       => false,
        )
    );

    if ( $saved_permalinks !== $permalinks ) {
        update_option( 'masvideos_video_permalinks', $permalinks );
    }

    $permalinks['video_rewrite_slug']           = untrailingslashit( $permalinks['video_base'] );
    $permalinks['video_category_rewrite_slug']  = untrailingslashit( $permalinks['video_category_base'] );
    $permalinks['video_tag_rewrite_slug']       = untrailingslashit( $permalinks['video_tag_base'] );
    $permalinks['video_attribute_rewrite_slug'] = untrailingslashit( $permalinks['video_attribute_base'] );

    $permalinks['movie_rewrite_slug']           = untrailingslashit( $permalinks['movie_base'] );
    $permalinks['movie_category_rewrite_slug']  = untrailingslashit( $permalinks['movie_category_base'] );
    $permalinks['movie_tag_rewrite_slug']       = untrailingslashit( $permalinks['movie_tag_base'] );
    $permalinks['movie_attribute_rewrite_slug'] = untrailingslashit( $permalinks['movie_attribute_base'] );

    return $permalinks;
}

/**
 * Retrieve page ids.
 *
 * @param string $page Page slug.
 * @return int
 */
function masvideos_get_page_id( $page ) {
    $page = apply_filters( 'masvideos_get_' . $page . '_page_id', get_option( 'masvideos_' . $page . '_page_id' ) );

    return $page ? absint( $page ) : -1;
}
