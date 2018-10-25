<?php
/**
 * MasVideos Terms
 *
 * Functions for handling terms/term meta.
 *
 * @package MasVideos/Functions
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Helper to get cached object terms and filter by field using wp_list_pluck().
 * Works as a cached alternative for wp_get_post_terms() and wp_get_object_terms().
 *
 * @since  1.0.0
 * @param  int    $object_id Object ID.
 * @param  string $taxonomy  Taxonomy slug.
 * @param  string $field     Field name.
 * @param  string $index_key Index key name.
 * @return array
 */
function masvideos_get_object_terms( $object_id, $taxonomy, $field = null, $index_key = null ) {
    // Test if terms exists. get_the_terms() return false when it finds no terms.
    $terms = get_the_terms( $object_id, $taxonomy );

    if ( ! $terms || is_wp_error( $terms ) ) {
        return array();
    }

    return is_null( $field ) ? $terms : wp_list_pluck( $terms, $field, $index_key );
}