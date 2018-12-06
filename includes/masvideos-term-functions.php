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

/**
 * Get full list of video visibilty term ids.
 *
 * @since  1.0.0
 * @return int[]
 */
function masvideos_get_video_visibility_term_ids() {
	if ( ! taxonomy_exists( 'video_visibility' ) ) {
		masvideos_doing_it_wrong( __FUNCTION__, 'masvideos_get_video_visibility_term_ids should not be called before taxonomies are registered (masvideos_after_register_post_type action).', '3.1' );
		return array();
	}
	return array_map(
		'absint', wp_parse_args(
			wp_list_pluck(
				get_terms(
					array(
						'taxonomy'   => 'video_visibility',
						'hide_empty' => false,
					)
				),
				'term_taxonomy_id',
				'name'
			),
			array(
				'exclude-from-catalog' => 0,
				'exclude-from-search'  => 0,
				'featured'             => 0,
				'rated-1'              => 0,
				'rated-2'              => 0,
				'rated-3'              => 0,
				'rated-4'              => 0,
				'rated-5'              => 0,
			)
		)
	);
}

/**
 * Get full list of movie visibilty term ids.
 *
 * @since  1.0.0
 * @return int[]
 */
function masvideos_get_movie_visibility_term_ids() {
	if ( ! taxonomy_exists( 'movie_visibility' ) ) {
		masvideos_doing_it_wrong( __FUNCTION__, 'masvideos_get_movie_visibility_term_ids should not be called before taxonomies are registered (masvideos_after_register_post_type action).', '3.1' );
		return array();
	}
	return array_map(
		'absint', wp_parse_args(
			wp_list_pluck(
				get_terms(
					array(
						'taxonomy'   => 'movie_visibility',
						'hide_empty' => false,
					)
				),
				'term_taxonomy_id',
				'name'
			),
			array(
				'exclude-from-catalog' => 0,
				'exclude-from-search'  => 0,
				'featured'             => 0,
				'rated-1'              => 0,
				'rated-2'              => 0,
				'rated-3'              => 0,
				'rated-4'              => 0,
				'rated-5'              => 0,
			)
		)
	);
}