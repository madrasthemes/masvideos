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
            'video_base'             => _x( 'video', 'slug', 'masvideos' ),
            'category_base'          => _x( 'video-category', 'slug', 'masvideos' ),
            'tag_base'               => _x( 'video-tag', 'slug', 'masvideos' ),
            'attribute_base'         => '',
            'use_verbose_page_rules' => false,
        )
    );

    if ( $saved_permalinks !== $permalinks ) {
        update_option( 'masvideos_video_permalinks', $permalinks );
    }

    $permalinks['video_rewrite_slug']     = untrailingslashit( $permalinks['video_base'] );
    $permalinks['category_rewrite_slug']  = untrailingslashit( $permalinks['category_base'] );
    $permalinks['tag_rewrite_slug']       = untrailingslashit( $permalinks['tag_base'] );
    $permalinks['attribute_rewrite_slug'] = untrailingslashit( $permalinks['attribute_base'] );

    return $permalinks;
}
