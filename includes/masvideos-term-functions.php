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
 * Cached version of wp_get_post_terms().
 * This is a private function (internal use ONLY).
 *
 * @since  1.0.0
 * @param  int    $episode_id Episode ID.
 * @param  string $taxonomy   Taxonomy slug.
 * @param  array  $args       Query arguments.
 * @return array
 */
function _masvideos_get_cached_episode_terms( $episode_id, $taxonomy, $args = array() ) {
    $cache_key   = 'masvideos_' . $taxonomy . md5( wp_json_encode( $args ) );
    $cache_group = MasVideos_Cache_Helper::get_cache_prefix( 'episode_' . $episode_id ) . $episode_id;
    $terms       = wp_cache_get( $cache_key, $cache_group );

    if ( false !== $terms ) {
        return $terms;
    }

    $terms = wp_get_post_terms( $episode_id, $taxonomy, $args );

    wp_cache_add( $cache_key, $terms, $cache_group );

    return $terms;
}

/**
 * Wrapper for wp_get_post_terms which supports ordering by parent.
 *
 * NOTE: At this point in time, ordering by menu_order for example isn't possible with this function. wp_get_post_terms has no.
 *   filters which we can utilise to modify it's query. https://core.trac.wordpress.org/ticket/19094.
 *
 * @param  int    $episode_id Episode ID.
 * @param  string $taxonomy   Taxonomy slug.
 * @param  array  $args       Query arguments.
 * @return array
 */
function masvideos_get_episode_terms( $episode_id, $taxonomy, $args = array() ) {
    if ( ! taxonomy_exists( $taxonomy ) ) {
        return array();
    }

    if ( empty( $args['orderby'] ) && taxonomy_is_episode_attribute( $taxonomy ) ) {
        $args['orderby'] = masvideos_attribute_orderby( 'episode', $taxonomy );
    }

    // Support ordering by parent.
    if ( ! empty( $args['orderby'] ) && in_array( $args['orderby'], array( 'name_num', 'parent' ), true ) ) {
        $fields  = isset( $args['fields'] ) ? $args['fields'] : 'all';
        $orderby = $args['orderby'];

        // Unset for wp_get_post_terms.
        unset( $args['orderby'] );
        unset( $args['fields'] );

        $terms = _masvideos_get_cached_episode_terms( $episode_id, $taxonomy, $args );

        switch ( $orderby ) {
            case 'name_num':
                usort( $terms, '_masvideos_get_episode_terms_name_num_usort_callback' );
                break;
            case 'parent':
                usort( $terms, '_masvideos_get_episode_terms_parent_usort_callback' );
                break;
        }

        switch ( $fields ) {
            case 'names':
                $terms = wp_list_pluck( $terms, 'name' );
                break;
            case 'ids':
                $terms = wp_list_pluck( $terms, 'term_id' );
                break;
            case 'slugs':
                $terms = wp_list_pluck( $terms, 'slug' );
                break;
        }
    } elseif ( ! empty( $args['orderby'] ) && 'menu_order' === $args['orderby'] ) {
        // wp_get_post_terms doesn't let us use custom sort order.
        $args['include'] = masvideos_get_object_terms( $episode_id, $taxonomy, 'term_id' );

        if ( empty( $args['include'] ) ) {
            $terms = array();
        } else {
            // This isn't needed for get_terms.
            unset( $args['orderby'] );

            // Set args for get_terms.
            $args['menu_order'] = isset( $args['order'] ) ? $args['order'] : 'ASC';
            $args['hide_empty'] = isset( $args['hide_empty'] ) ? $args['hide_empty'] : 0;
            $args['fields']     = isset( $args['fields'] ) ? $args['fields'] : 'names';

            // Ensure slugs is valid for get_terms - slugs isn't supported.
            $args['fields'] = ( 'slugs' === $args['fields'] ) ? 'id=>slug' : $args['fields'];
            $terms          = get_terms( $taxonomy, $args );
        }
    } else {
        $terms = _masvideos_get_cached_episode_terms( $episode_id, $taxonomy, $args );
    }

    return apply_filters( 'masvideos_get_episode_terms', $terms, $episode_id, $taxonomy, $args );
}

/**
 * Sort by name (numeric).
 *
 * @param  WP_Post $a First item to compare.
 * @param  WP_Post $b Second item to compare.
 * @return int
 */
function _masvideos_get_episode_terms_name_num_usort_callback( $a, $b ) {
    $a_name = (float) $a->name;
    $b_name = (float) $b->name;

    if ( abs( $a_name - $b_name ) < 0.001 ) {
        return 0;
    }

    return ( $a_name < $b_name ) ? -1 : 1;
}

/**
 * Sort by parent.
 *
 * @param  WP_Post $a First item to compare.
 * @param  WP_Post $b Second item to compare.
 * @return int
 */
function _masvideos_get_episode_terms_parent_usort_callback( $a, $b ) {
    if ( $a->parent === $b->parent ) {
        return 0;
    }
    return ( $a->parent < $b->parent ) ? 1 : -1;
}

/**
 * Get full list of episode visibilty term ids.
 *
 * @since  1.0.0
 * @return int[]
 */
function masvideos_get_episode_visibility_term_ids() {
    if ( ! taxonomy_exists( 'episode_visibility' ) ) {
        _doing_it_wrong( __FUNCTION__, 'masvideos_get_episode_visibility_term_ids should not be called before taxonomies are registered (masvideos_after_register_post_type action).', '3.1' );
        return array();
    }
    return array_map(
        'absint', wp_parse_args(
            wp_list_pluck(
                get_terms(
                    array(
                        'taxonomy'   => 'episode_visibility',
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
                'rated-6'              => 0,
                'rated-7'              => 0,
                'rated-8'              => 0,
                'rated-9'              => 0,
                'rated-10'             => 0,
            )
        )
    );
}

/**
 * MasVideos Dropdown categories.
 *
 * @param array $args Args to control display of dropdown.
 */
function masvideos_episode_dropdown_categories( $args = array() ) {
    global $wp_query;

    $args = wp_parse_args(
        $args, array(
            'pad_counts'         => 1,
            'show_count'         => 1,
            'hierarchical'       => 1,
            'hide_empty'         => 1,
            'show_uncategorized' => 1,
            'orderby'            => 'name',
            'selected'           => isset( $wp_query->query_vars['episode_genre'] ) ? $wp_query->query_vars['episode_genre'] : '',
            'menu_order'         => false,
            'show_option_none'   => esc_html__( 'Select a Genre', 'masvideos' ),
            'option_none_value'  => '',
            'value_field'        => 'slug',
            'taxonomy'           => 'episode_genre',
            'name'               => 'episode_genre',
            'class'              => 'dropdown_episode_genre',
        )
    );

    if ( 'order' === $args['orderby'] ) {
        $args['menu_order'] = 'asc';
        $args['orderby']    = 'name';
    }

    wp_dropdown_categories( $args );
}

/**
 * Cached version of wp_get_post_terms().
 * This is a private function (internal use ONLY).
 *
 * @since  1.0.0
 * @param  int    $tv_show_id TV Show ID.
 * @param  string $taxonomy   Taxonomy slug.
 * @param  array  $args       Query arguments.
 * @return array
 */
function _masvideos_get_cached_tv_show_terms( $tv_show_id, $taxonomy, $args = array() ) {
    $cache_key   = 'masvideos_' . $taxonomy . md5( wp_json_encode( $args ) );
    $cache_group = MasVideos_Cache_Helper::get_cache_prefix( 'tv_show_' . $tv_show_id ) . $tv_show_id;
    $terms       = wp_cache_get( $cache_key, $cache_group );

    if ( false !== $terms ) {
        return $terms;
    }

    $terms = wp_get_post_terms( $tv_show_id, $taxonomy, $args );

    wp_cache_add( $cache_key, $terms, $cache_group );

    return $terms;
}

/**
 * Wrapper for wp_get_post_terms which supports ordering by parent.
 *
 * NOTE: At this point in time, ordering by menu_order for example isn't possible with this function. wp_get_post_terms has no.
 *   filters which we can utilise to modify it's query. https://core.trac.wordpress.org/ticket/19094.
 *
 * @param  int    $tv_show_id TV Show ID.
 * @param  string $taxonomy   Taxonomy slug.
 * @param  array  $args       Query arguments.
 * @return array
 */
function masvideos_get_tv_show_terms( $tv_show_id, $taxonomy, $args = array() ) {
    if ( ! taxonomy_exists( $taxonomy ) ) {
        return array();
    }

    if ( empty( $args['orderby'] ) && taxonomy_is_tv_show_attribute( $taxonomy ) ) {
        $args['orderby'] = masvideos_attribute_orderby( 'tv_show', $taxonomy );
    }

    // Support ordering by parent.
    if ( ! empty( $args['orderby'] ) && in_array( $args['orderby'], array( 'name_num', 'parent' ), true ) ) {
        $fields  = isset( $args['fields'] ) ? $args['fields'] : 'all';
        $orderby = $args['orderby'];

        // Unset for wp_get_post_terms.
        unset( $args['orderby'] );
        unset( $args['fields'] );

        $terms = _masvideos_get_cached_tv_show_terms( $tv_show_id, $taxonomy, $args );

        switch ( $orderby ) {
            case 'name_num':
                usort( $terms, '_masvideos_get_tv_show_terms_name_num_usort_callback' );
                break;
            case 'parent':
                usort( $terms, '_masvideos_get_tv_show_terms_parent_usort_callback' );
                break;
        }

        switch ( $fields ) {
            case 'names':
                $terms = wp_list_pluck( $terms, 'name' );
                break;
            case 'ids':
                $terms = wp_list_pluck( $terms, 'term_id' );
                break;
            case 'slugs':
                $terms = wp_list_pluck( $terms, 'slug' );
                break;
        }
    } elseif ( ! empty( $args['orderby'] ) && 'menu_order' === $args['orderby'] ) {
        // wp_get_post_terms doesn't let us use custom sort order.
        $args['include'] = masvideos_get_object_terms( $tv_show_id, $taxonomy, 'term_id' );

        if ( empty( $args['include'] ) ) {
            $terms = array();
        } else {
            // This isn't needed for get_terms.
            unset( $args['orderby'] );

            // Set args for get_terms.
            $args['menu_order'] = isset( $args['order'] ) ? $args['order'] : 'ASC';
            $args['hide_empty'] = isset( $args['hide_empty'] ) ? $args['hide_empty'] : 0;
            $args['fields']     = isset( $args['fields'] ) ? $args['fields'] : 'names';

            // Ensure slugs is valid for get_terms - slugs isn't supported.
            $args['fields'] = ( 'slugs' === $args['fields'] ) ? 'id=>slug' : $args['fields'];
            $terms          = get_terms( $taxonomy, $args );
        }
    } else {
        $terms = _masvideos_get_cached_tv_show_terms( $tv_show_id, $taxonomy, $args );
    }

    return apply_filters( 'masvideos_get_tv_show_terms', $terms, $tv_show_id, $taxonomy, $args );
}

/**
 * Sort by name (numeric).
 *
 * @param  WP_Post $a First item to compare.
 * @param  WP_Post $b Second item to compare.
 * @return int
 */
function _masvideos_get_tv_show_terms_name_num_usort_callback( $a, $b ) {
    $a_name = (float) $a->name;
    $b_name = (float) $b->name;

    if ( abs( $a_name - $b_name ) < 0.001 ) {
        return 0;
    }

    return ( $a_name < $b_name ) ? -1 : 1;
}

/**
 * Sort by parent.
 *
 * @param  WP_Post $a First item to compare.
 * @param  WP_Post $b Second item to compare.
 * @return int
 */
function _masvideos_get_tv_show_terms_parent_usort_callback( $a, $b ) {
    if ( $a->parent === $b->parent ) {
        return 0;
    }
    return ( $a->parent < $b->parent ) ? 1 : -1;
}

/**
 * Get full list of tv_show visibilty term ids.
 *
 * @since  1.0.0
 * @return int[]
 */
function masvideos_get_tv_show_visibility_term_ids() {
    if ( ! taxonomy_exists( 'tv_show_visibility' ) ) {
        _doing_it_wrong( __FUNCTION__, 'masvideos_get_tv_show_visibility_term_ids should not be called before taxonomies are registered (masvideos_after_register_post_type action).', '3.1' );
        return array();
    }
    return array_map(
        'absint', wp_parse_args(
            wp_list_pluck(
                get_terms(
                    array(
                        'taxonomy'   => 'tv_show_visibility',
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
                'rated-6'              => 0,
                'rated-7'              => 0,
                'rated-8'              => 0,
                'rated-9'              => 0,
                'rated-10'             => 0,
            )
        )
    );
}

/**
 * MasVideos Dropdown categories.
 *
 * @param array $args Args to control display of dropdown.
 */
function masvideos_tv_show_dropdown_categories( $args = array() ) {
    global $wp_query;

    $args = wp_parse_args(
        $args, array(
            'pad_counts'         => 1,
            'show_count'         => 1,
            'hierarchical'       => 1,
            'hide_empty'         => 1,
            'show_uncategorized' => 1,
            'orderby'            => 'name',
            'selected'           => isset( $wp_query->query_vars['tv_show_genre'] ) ? $wp_query->query_vars['tv_show_genre'] : '',
            'menu_order'         => false,
            'show_option_none'   => esc_html__( 'Select a Genre', 'masvideos' ),
            'option_none_value'  => '',
            'value_field'        => 'slug',
            'taxonomy'           => 'tv_show_genre',
            'name'               => 'tv_show_genre',
            'class'              => 'dropdown_tv_show_genre',
        )
    );

    if ( 'order' === $args['orderby'] ) {
        $args['menu_order'] = 'asc';
        $args['orderby']    = 'name';
    }

    wp_dropdown_categories( $args );
}

function masvideos_tv_show_dropdown_genres( $args = array() ) {
    global $wp_query;

    $args = wp_parse_args(
        $args, array(
            'pad_counts'         => 1,
            'show_count'         => 1,
            'hierarchical'       => 1,
            'hide_empty'         => 1,
            'show_uncategorized' => 1,
            'orderby'            => 'name',
            'selected'           => isset( $wp_query->query_vars['tv_show_genre'] ) ? $wp_query->query_vars['tv_show_genre'] : '',
            'menu_order'         => false,
            'show_option_none'   => esc_html__( 'Select a Genre', 'masvideos' ),
            'option_none_value'  => '',
            'value_field'        => 'slug',
            'taxonomy'           => 'tv_show_genre',
            'name'               => 'tv_show_genre',
            'class'              => 'dropdown_tv_show_genre',
        )
    );

    if ( 'order' === $args['orderby'] ) {
        $args['menu_order'] = 'asc';
        $args['orderby']    = 'name';
    }

    wp_dropdown_categories( $args );
}

/**
 * Cached version of wp_get_post_terms().
 * This is a private function (internal use ONLY).
 *
 * @since  1.0.0
 * @param  int    $movie_id   Movie ID.
 * @param  string $taxonomy   Taxonomy slug.
 * @param  array  $args       Query arguments.
 * @return array
 */
function _masvideos_get_cached_movie_terms( $movie_id, $taxonomy, $args = array() ) {
	$cache_key   = 'masvideos_' . $taxonomy . md5( wp_json_encode( $args ) );
	$cache_group = MasVideos_Cache_Helper::get_cache_prefix( 'movie_' . $movie_id ) . $movie_id;
	$terms       = wp_cache_get( $cache_key, $cache_group );

	if ( false !== $terms ) {
		return $terms;
	}

	$terms = wp_get_post_terms( $movie_id, $taxonomy, $args );

	wp_cache_add( $cache_key, $terms, $cache_group );

	return $terms;
}

/**
 * Wrapper for wp_get_post_terms which supports ordering by parent.
 *
 * NOTE: At this point in time, ordering by menu_order for example isn't possible with this function. wp_get_post_terms has no.
 *   filters which we can utilise to modify it's query. https://core.trac.wordpress.org/ticket/19094.
 *
 * @param  int    $movie_id   Movie ID.
 * @param  string $taxonomy   Taxonomy slug.
 * @param  array  $args       Query arguments.
 * @return array
 */
function masvideos_get_movie_terms( $movie_id, $taxonomy, $args = array() ) {
	if ( ! taxonomy_exists( $taxonomy ) ) {
		return array();
	}

	if ( empty( $args['orderby'] ) && taxonomy_is_movie_attribute( $taxonomy ) ) {
		$args['orderby'] = masvideos_attribute_orderby( 'movie', $taxonomy );
	}

	// Support ordering by parent.
	if ( ! empty( $args['orderby'] ) && in_array( $args['orderby'], array( 'name_num', 'parent' ), true ) ) {
		$fields  = isset( $args['fields'] ) ? $args['fields'] : 'all';
		$orderby = $args['orderby'];

		// Unset for wp_get_post_terms.
		unset( $args['orderby'] );
		unset( $args['fields'] );

		$terms = _masvideos_get_cached_movie_terms( $movie_id, $taxonomy, $args );

		switch ( $orderby ) {
			case 'name_num':
				usort( $terms, '_masvideos_get_movie_terms_name_num_usort_callback' );
				break;
			case 'parent':
				usort( $terms, '_masvideos_get_movie_terms_parent_usort_callback' );
				break;
		}

		switch ( $fields ) {
			case 'names':
				$terms = wp_list_pluck( $terms, 'name' );
				break;
			case 'ids':
				$terms = wp_list_pluck( $terms, 'term_id' );
				break;
			case 'slugs':
				$terms = wp_list_pluck( $terms, 'slug' );
				break;
		}
	} elseif ( ! empty( $args['orderby'] ) && 'menu_order' === $args['orderby'] ) {
		// wp_get_post_terms doesn't let us use custom sort order.
		$args['include'] = masvideos_get_object_terms( $movie_id, $taxonomy, 'term_id' );

		if ( empty( $args['include'] ) ) {
			$terms = array();
		} else {
			// This isn't needed for get_terms.
			unset( $args['orderby'] );

			// Set args for get_terms.
			$args['menu_order'] = isset( $args['order'] ) ? $args['order'] : 'ASC';
			$args['hide_empty'] = isset( $args['hide_empty'] ) ? $args['hide_empty'] : 0;
			$args['fields']     = isset( $args['fields'] ) ? $args['fields'] : 'names';

			// Ensure slugs is valid for get_terms - slugs isn't supported.
			$args['fields'] = ( 'slugs' === $args['fields'] ) ? 'id=>slug' : $args['fields'];
			$terms          = get_terms( $taxonomy, $args );
		}
	} else {
		$terms = _masvideos_get_cached_movie_terms( $movie_id, $taxonomy, $args );
	}

	return apply_filters( 'masvideos_get_movie_terms', $terms, $movie_id, $taxonomy, $args );
}

/**
 * Sort by name (numeric).
 *
 * @param  WP_Post $a First item to compare.
 * @param  WP_Post $b Second item to compare.
 * @return int
 */
function _masvideos_get_movie_terms_name_num_usort_callback( $a, $b ) {
	$a_name = (float) $a->name;
	$b_name = (float) $b->name;

	if ( abs( $a_name - $b_name ) < 0.001 ) {
		return 0;
	}

	return ( $a_name < $b_name ) ? -1 : 1;
}

/**
 * Sort by parent.
 *
 * @param  WP_Post $a First item to compare.
 * @param  WP_Post $b Second item to compare.
 * @return int
 */
function _masvideos_get_movie_terms_parent_usort_callback( $a, $b ) {
	if ( $a->parent === $b->parent ) {
		return 0;
	}
	return ( $a->parent < $b->parent ) ? 1 : -1;
}

/**
 * Get full list of movie visibilty term ids.
 *
 * @since  1.0.0
 * @return int[]
 */
function masvideos_get_movie_visibility_term_ids() {
	if ( ! taxonomy_exists( 'movie_visibility' ) ) {
		_doing_it_wrong( __FUNCTION__, 'masvideos_get_movie_visibility_term_ids should not be called before taxonomies are registered (masvideos_after_register_post_type action).', '3.1' );
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
				'rated-6'              => 0,
				'rated-7'              => 0,
				'rated-8'              => 0,
				'rated-9'              => 0,
				'rated-10'             => 0,
			)
		)
	);
}

/**
 * MasVideos Dropdown categories.
 *
 * @param array $args Args to control display of dropdown.
 */
function masvideos_movie_dropdown_genres( $args = array() ) {
	global $wp_query;

	$args = wp_parse_args(
		$args, array(
			'pad_counts'         => 1,
			'show_count'         => 1,
			'hierarchical'       => 1,
			'hide_empty'         => 1,
			'show_uncategorized' => 1,
			'orderby'            => 'name',
			'selected'           => isset( $wp_query->query_vars['movie_genre'] ) ? $wp_query->query_vars['movie_genre'] : '',
			'menu_order'         => false,
			'show_option_none'   => esc_html__( 'Select a Genre', 'masvideos' ),
			'option_none_value'  => '',
			'value_field'        => 'slug',
			'taxonomy'           => 'movie_genre',
			'name'               => 'movie_genre',
			'class'              => 'dropdown_movie_genre',
		)
	);

	if ( 'order' === $args['orderby'] ) {
		$args['menu_order'] = 'asc';
		$args['orderby']    = 'name';
	}

	wp_dropdown_categories( $args );
}

/**
 * Cached version of wp_get_post_terms().
 * This is a private function (internal use ONLY).
 *
 * @since  1.0.0
 * @param  int    $person_id   Person ID.
 * @param  string $taxonomy   Taxonomy slug.
 * @param  array  $args       Query arguments.
 * @return array
 */
function _masvideos_get_cached_person_terms( $person_id, $taxonomy, $args = array() ) {
    $cache_key   = 'masvideos_' . $taxonomy . md5( wp_json_encode( $args ) );
    $cache_group = MasVideos_Cache_Helper::get_cache_prefix( 'person_' . $person_id ) . $person_id;
    $terms       = wp_cache_get( $cache_key, $cache_group );

    if ( false !== $terms ) {
        return $terms;
    }

    $terms = wp_get_post_terms( $person_id, $taxonomy, $args );

    wp_cache_add( $cache_key, $terms, $cache_group );

    return $terms;
}

/**
 * Wrapper for wp_get_post_terms which supports ordering by parent.
 *
 * NOTE: At this point in time, ordering by menu_order for example isn't possible with this function. wp_get_post_terms has no.
 *   filters which we can utilise to modify it's query. https://core.trac.wordpress.org/ticket/19094.
 *
 * @param  int    $person_id   Person ID.
 * @param  string $taxonomy   Taxonomy slug.
 * @param  array  $args       Query arguments.
 * @return array
 */
function masvideos_get_person_terms( $person_id, $taxonomy, $args = array() ) {
    if ( ! taxonomy_exists( $taxonomy ) ) {
        return array();
    }

    if ( empty( $args['orderby'] ) && taxonomy_is_person_attribute( $taxonomy ) ) {
        $args['orderby'] = masvideos_attribute_orderby( 'person', $taxonomy );
    }

    // Support ordering by parent.
    if ( ! empty( $args['orderby'] ) && in_array( $args['orderby'], array( 'name_num', 'parent' ), true ) ) {
        $fields  = isset( $args['fields'] ) ? $args['fields'] : 'all';
        $orderby = $args['orderby'];

        // Unset for wp_get_post_terms.
        unset( $args['orderby'] );
        unset( $args['fields'] );

        $terms = _masvideos_get_cached_person_terms( $person_id, $taxonomy, $args );

        switch ( $orderby ) {
            case 'name_num':
                usort( $terms, '_masvideos_get_person_terms_name_num_usort_callback' );
                break;
            case 'parent':
                usort( $terms, '_masvideos_get_person_terms_parent_usort_callback' );
                break;
        }

        switch ( $fields ) {
            case 'names':
                $terms = wp_list_pluck( $terms, 'name' );
                break;
            case 'ids':
                $terms = wp_list_pluck( $terms, 'term_id' );
                break;
            case 'slugs':
                $terms = wp_list_pluck( $terms, 'slug' );
                break;
        }
    } elseif ( ! empty( $args['orderby'] ) && 'menu_order' === $args['orderby'] ) {
        // wp_get_post_terms doesn't let us use custom sort order.
        $args['include'] = masvideos_get_object_terms( $person_id, $taxonomy, 'term_id' );

        if ( empty( $args['include'] ) ) {
            $terms = array();
        } else {
            // This isn't needed for get_terms.
            unset( $args['orderby'] );

            // Set args for get_terms.
            $args['menu_order'] = isset( $args['order'] ) ? $args['order'] : 'ASC';
            $args['hide_empty'] = isset( $args['hide_empty'] ) ? $args['hide_empty'] : 0;
            $args['fields']     = isset( $args['fields'] ) ? $args['fields'] : 'names';

            // Ensure slugs is valid for get_terms - slugs isn't supported.
            $args['fields'] = ( 'slugs' === $args['fields'] ) ? 'id=>slug' : $args['fields'];
            $terms          = get_terms( $taxonomy, $args );
        }
    } else {
        $terms = _masvideos_get_cached_person_terms( $person_id, $taxonomy, $args );
    }

    return apply_filters( 'masvideos_get_person_terms', $terms, $person_id, $taxonomy, $args );
}

/**
 * Sort by name (numeric).
 *
 * @param  WP_Post $a First item to compare.
 * @param  WP_Post $b Second item to compare.
 * @return int
 */
function _masvideos_get_person_terms_name_num_usort_callback( $a, $b ) {
    $a_name = (float) $a->name;
    $b_name = (float) $b->name;

    if ( abs( $a_name - $b_name ) < 0.001 ) {
        return 0;
    }

    return ( $a_name < $b_name ) ? -1 : 1;
}

/**
 * Sort by parent.
 *
 * @param  WP_Post $a First item to compare.
 * @param  WP_Post $b Second item to compare.
 * @return int
 */
function _masvideos_get_person_terms_parent_usort_callback( $a, $b ) {
    if ( $a->parent === $b->parent ) {
        return 0;
    }
    return ( $a->parent < $b->parent ) ? 1 : -1;
}

/**
 * Get full list of person visibilty term ids.
 *
 * @since  1.0.0
 * @return int[]
 */
function masvideos_get_person_visibility_term_ids() {
    if ( ! taxonomy_exists( 'person_visibility' ) ) {
        _doing_it_wrong( __FUNCTION__, 'masvideos_get_person_visibility_term_ids should not be called before taxonomies are registered (masvideos_after_register_post_type action).', '3.1' );
        return array();
    }
    return array_map(
        'absint', wp_parse_args(
            wp_list_pluck(
                get_terms(
                    array(
                        'taxonomy'   => 'person_visibility',
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
            )
        )
    );
}

/**
 * MasVideos Dropdown categories.
 *
 * @param array $args Args to control display of dropdown.
 */
function masvideos_person_dropdown_categories( $args = array() ) {
    global $wp_query;

    $args = wp_parse_args(
        $args, array(
            'pad_counts'         => 1,
            'show_count'         => 1,
            'hierarchical'       => 1,
            'hide_empty'         => 1,
            'show_uncategorized' => 1,
            'orderby'            => 'name',
            'selected'           => isset( $wp_query->query_vars['person_cat'] ) ? $wp_query->query_vars['person_cat'] : '',
            'menu_order'         => false,
            'show_option_none'   => esc_html__( 'Select a category', 'masvideos' ),
            'option_none_value'  => '',
            'value_field'        => 'slug',
            'taxonomy'           => 'person_cat',
            'name'               => 'person_cat',
            'class'              => 'dropdown_person_cat',
        )
    );

    if ( 'order' === $args['orderby'] ) {
        $args['menu_order'] = 'asc';
        $args['orderby']    = 'name';
    }

    wp_dropdown_categories( $args );
}

/**
 * Cached version of wp_get_post_terms().
 * This is a private function (internal use ONLY).
 *
 * @since  1.0.0
 * @param  int    $video_id   Video ID.
 * @param  string $taxonomy   Taxonomy slug.
 * @param  array  $args       Query arguments.
 * @return array
 */
function _masvideos_get_cached_video_terms( $video_id, $taxonomy, $args = array() ) {
    $cache_key   = 'masvideos_' . $taxonomy . md5( wp_json_encode( $args ) );
    $cache_group = MasVideos_Cache_Helper::get_cache_prefix( 'video_' . $video_id ) . $video_id;
    $terms       = wp_cache_get( $cache_key, $cache_group );

    if ( false !== $terms ) {
        return $terms;
    }

    $terms = wp_get_post_terms( $video_id, $taxonomy, $args );

    wp_cache_add( $cache_key, $terms, $cache_group );

    return $terms;
}

/**
 * Wrapper for wp_get_post_terms which supports ordering by parent.
 *
 * NOTE: At this point in time, ordering by menu_order for example isn't possible with this function. wp_get_post_terms has no.
 *   filters which we can utilise to modify it's query. https://core.trac.wordpress.org/ticket/19094.
 *
 * @param  int    $video_id   Video ID.
 * @param  string $taxonomy   Taxonomy slug.
 * @param  array  $args       Query arguments.
 * @return array
 */
function masvideos_get_video_terms( $video_id, $taxonomy, $args = array() ) {
    if ( ! taxonomy_exists( $taxonomy ) ) {
        return array();
    }

    if ( empty( $args['orderby'] ) && taxonomy_is_video_attribute( $taxonomy ) ) {
        $args['orderby'] = masvideos_attribute_orderby( 'video', $taxonomy );
    }

    // Support ordering by parent.
    if ( ! empty( $args['orderby'] ) && in_array( $args['orderby'], array( 'name_num', 'parent' ), true ) ) {
        $fields  = isset( $args['fields'] ) ? $args['fields'] : 'all';
        $orderby = $args['orderby'];

        // Unset for wp_get_post_terms.
        unset( $args['orderby'] );
        unset( $args['fields'] );

        $terms = _masvideos_get_cached_video_terms( $video_id, $taxonomy, $args );

        switch ( $orderby ) {
            case 'name_num':
                usort( $terms, '_masvideos_get_video_terms_name_num_usort_callback' );
                break;
            case 'parent':
                usort( $terms, '_masvideos_get_video_terms_parent_usort_callback' );
                break;
        }

        switch ( $fields ) {
            case 'names':
                $terms = wp_list_pluck( $terms, 'name' );
                break;
            case 'ids':
                $terms = wp_list_pluck( $terms, 'term_id' );
                break;
            case 'slugs':
                $terms = wp_list_pluck( $terms, 'slug' );
                break;
        }
    } elseif ( ! empty( $args['orderby'] ) && 'menu_order' === $args['orderby'] ) {
        // wp_get_post_terms doesn't let us use custom sort order.
        $args['include'] = masvideos_get_object_terms( $video_id, $taxonomy, 'term_id' );

        if ( empty( $args['include'] ) ) {
            $terms = array();
        } else {
            // This isn't needed for get_terms.
            unset( $args['orderby'] );

            // Set args for get_terms.
            $args['menu_order'] = isset( $args['order'] ) ? $args['order'] : 'ASC';
            $args['hide_empty'] = isset( $args['hide_empty'] ) ? $args['hide_empty'] : 0;
            $args['fields']     = isset( $args['fields'] ) ? $args['fields'] : 'names';

            // Ensure slugs is valid for get_terms - slugs isn't supported.
            $args['fields'] = ( 'slugs' === $args['fields'] ) ? 'id=>slug' : $args['fields'];
            $terms          = get_terms( $taxonomy, $args );
        }
    } else {
        $terms = _masvideos_get_cached_video_terms( $video_id, $taxonomy, $args );
    }

    return apply_filters( 'masvideos_get_video_terms', $terms, $video_id, $taxonomy, $args );
}

/**
 * Sort by name (numeric).
 *
 * @param  WP_Post $a First item to compare.
 * @param  WP_Post $b Second item to compare.
 * @return int
 */
function _masvideos_get_video_terms_name_num_usort_callback( $a, $b ) {
    $a_name = (float) $a->name;
    $b_name = (float) $b->name;

    if ( abs( $a_name - $b_name ) < 0.001 ) {
        return 0;
    }

    return ( $a_name < $b_name ) ? -1 : 1;
}

/**
 * Sort by parent.
 *
 * @param  WP_Post $a First item to compare.
 * @param  WP_Post $b Second item to compare.
 * @return int
 */
function _masvideos_get_video_terms_parent_usort_callback( $a, $b ) {
    if ( $a->parent === $b->parent ) {
        return 0;
    }
    return ( $a->parent < $b->parent ) ? 1 : -1;
}

/**
 * Get full list of video visibilty term ids.
 *
 * @since  1.0.0
 * @return int[]
 */
function masvideos_get_video_visibility_term_ids() {
    if ( ! taxonomy_exists( 'video_visibility' ) ) {
        _doing_it_wrong( __FUNCTION__, 'masvideos_get_video_visibility_term_ids should not be called before taxonomies are registered (masvideos_after_register_post_type action).', '3.1' );
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
                'rated-6'              => 0,
                'rated-7'              => 0,
                'rated-8'              => 0,
                'rated-9'              => 0,
                'rated-10'             => 0,
            )
        )
    );
}

/**
 * MasVideos Dropdown categories.
 *
 * @param array $args Args to control display of dropdown.
 */
function masvideos_video_dropdown_categories( $args = array() ) {
    global $wp_query;

    $args = wp_parse_args(
        $args, array(
            'pad_counts'         => 1,
            'show_count'         => 1,
            'hierarchical'       => 1,
            'hide_empty'         => 1,
            'show_uncategorized' => 1,
            'orderby'            => 'name',
            'selected'           => isset( $wp_query->query_vars['video_cat'] ) ? $wp_query->query_vars['video_cat'] : '',
            'menu_order'         => false,
            'show_option_none'   => esc_html__( 'Select a category', 'masvideos' ),
            'option_none_value'  => '',
            'value_field'        => 'slug',
            'taxonomy'           => 'video_cat',
            'name'               => 'video_cat',
            'class'              => 'dropdown_video_cat',
        )
    );

    if ( 'order' === $args['orderby'] ) {
        $args['menu_order'] = 'asc';
        $args['orderby']    = 'name';
    }

    wp_dropdown_categories( $args );
}