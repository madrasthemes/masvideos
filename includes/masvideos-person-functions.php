<?php
/**
 * MasVideos Person Functions
 *
 * Functions for person specific things.
 *
 * @package MasVideos/Functions
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Standard way of retrieving persons based on certain parameters.
 *
 * This function should be used for person retrieval so that we have a data agnostic
 * way to get a list of persons.
 *
 * @since  1.0.0
 * @param  array $args Array of args (above).
 * @return array|stdClass Number of pages and an array of person objects if
 *                             paginate is true, or just an array of values.
 */
function masvideos_get_persons( $args ) {
    // Handle some BW compatibility arg names where wp_query args differ in naming.
    $map_legacy = array(
        'numberposts'    => 'limit',
        'post_status'    => 'status',
        'post_parent'    => 'parent',
        'posts_per_page' => 'limit',
        'paged'          => 'page',
    );

    foreach ( $map_legacy as $from => $to ) {
        if ( isset( $args[ $from ] ) ) {
            $args[ $to ] = $args[ $from ];
        }
    }

    $query = new MasVideos_Person_Query( $args );
    return $query->get_persons();
}

/**
 * Main function for returning persons, uses the MasVideos_Person_Factory class.
 *
 * @since 1.0.0
 *
 * @param mixed $the_person Post object or post ID of the person.
 * @return MasVideos_Person|null|false
 */
function masvideos_get_person( $the_person = false ) {
    if ( ! did_action( 'masvideos_init' ) ) {
        /* translators: 1: masvideos_get_person 2: masvideos_init */
        _doing_it_wrong( __FUNCTION__, sprintf( __( '%1$s should not be called before the %2$s action.', 'masvideos' ), 'masvideos_get_person', 'masvideos_init' ), '1.0.0' );
        return false;
    }
    return MasVideos()->person_factory->get_person( $the_person );
}

/**
 * Clear all transients cache for person data.
 *
 * @param int $post_id (default: 0).
 */
function masvideos_delete_person_transients( $post_id = 0 ) {
    // Core transients.
    $transients_to_clear = array(
        'masvideos_featured_persons',
        'masvideos_count_comments',
    );

    // Transient names that include an ID.
    $post_transient_names = array(
        'masvideos_person_children_',
        'masvideos_related_',
    );

    if ( $post_id > 0 ) {
        foreach ( $post_transient_names as $transient ) {
            $transients_to_clear[] = $transient . $post_id;
        }

        // Does this person have a parent?
        $person = masvideos_get_person( $post_id );

        if ( $person ) {
            if ( $person->get_parent_id() > 0 ) {
                masvideos_delete_person_transients( $person->get_parent_id() );
            }
        }
    }

    // Delete transients.
    foreach ( $transients_to_clear as $transient ) {
        delete_transient( $transient );
    }

    // Increments the transient version to invalidate cache.
    MasVideos_Cache_Helper::get_transient_version( 'person', true );

    do_action( 'masvideos_delete_person_transients', $post_id );
}

/**
 * Get person visibility options.
 *
 * @since 1.0.0
 * @return array
 */
function masvideos_get_person_visibility_options() {
    return apply_filters(
        'masvideos_person_visibility_options', array(
            'visible' => __( 'Catalog and search results', 'masvideos' ),
            'catalog' => __( 'Catalog only', 'masvideos' ),
            'search'  => __( 'Search results only', 'masvideos' ),
            'hidden'  => __( 'Hidden', 'masvideos' ),
        )
    );
}

/**
 * Callback for array filter to get visible only.
 *
 * @since  1.0.0
 * @param  MasVideos_Person $person MasVideos_Person object.
 * @return bool
 */
function masvideos_persons_array_filter_visible( $person ) {
    return $person && is_a( $person, 'MasVideos_Person' ) && $person->is_visible();
}

/**
 * Callback for array filter to get persons the user can edit only.
 *
 * @since  1.0.0
 * @param  MasVideos_Person $person MasVideos_Person object.
 * @return bool
 */
function masvideos_persons_array_filter_editable( $person ) {
    return $person && is_a( $person, 'MasVideos_Person' ) && current_user_can( 'edit_person', $person->get_id() );
}

/**
 * Callback for array filter to get persons the user can view only.
 *
 * @since  1.0.0
 * @param  MasVideos_Person $person MasVideos_Person object.
 * @return bool
 */
function masvideos_persons_array_filter_readable( $person ) {
    return $person && is_a( $person, 'MasVideos_Person' ) && current_user_can( 'read_person', $person->get_id() );
}

/**
 * Sort an array of persons by a value.
 *
 * @since  1.0.0
 *
 * @param array  $persons List of persons to be ordered.
 * @param string $orderby Optional order criteria.
 * @param string $order Ascending or descending order.
 *
 * @return array
 */
function masvideos_persons_array_orderby( $persons, $orderby = 'date', $order = 'desc' ) {
    $orderby = strtolower( $orderby );
    $order   = strtolower( $order );
    switch ( $orderby ) {
        case 'title':
        case 'id':
        case 'date':
        case 'modified':
        case 'menu_order':
            usort( $persons, 'masvideos_persons_array_orderby_' . $orderby );
            break;
        default:
            shuffle( $persons );
            break;
    }
    if ( 'desc' === $order ) {
        $persons = array_reverse( $persons );
    }
    return $persons;
}

/**
 * Sort by title.
 *
 * @since  1.0.0
 * @param  MasVideos_Person $a First MasVideos_Person object.
 * @param  MasVideos_Person $b Second MasVideos_Person object.
 * @return int
 */
function masvideos_persons_array_orderby_title( $a, $b ) {
    return strcasecmp( $a->get_name(), $b->get_name() );
}

/**
 * Sort by id.
 *
 * @since  1.0.0
 * @param  MasVideos_Person $a First MasVideos_Person object.
 * @param  MasVideos_Person $b Second MasVideos_Person object.
 * @return int
 */
function masvideos_persons_array_orderby_id( $a, $b ) {
    if ( $a->get_id() === $b->get_id() ) {
        return 0;
    }
    return ( $a->get_id() < $b->get_id() ) ? -1 : 1;
}

/**
 * Sort by date.
 *
 * @since  1.0.0
 * @param  MasVideos_Person $a First MasVideos_Person object.
 * @param  MasVideos_Person $b Second MasVideos_Person object.
 * @return int
 */
function masvideos_persons_array_orderby_date( $a, $b ) {
    if ( $a->get_date_created() === $b->get_date_created() ) {
        return 0;
    }
    return ( $a->get_date_created() < $b->get_date_created() ) ? -1 : 1;
}

/**
 * Sort by modified.
 *
 * @since  1.0.0
 * @param  MasVideos_Person $a First MasVideos_Person object.
 * @param  MasVideos_Person $b Second MasVideos_Person object.
 * @return int
 */
function masvideos_persons_array_orderby_modified( $a, $b ) {
    if ( $a->get_date_modified() === $b->get_date_modified() ) {
        return 0;
    }
    return ( $a->get_date_modified() < $b->get_date_modified() ) ? -1 : 1;
}

/**
 * Sort by menu order.
 *
 * @since  1.0.0
 * @param  MasVideos_Person $a First MasVideos_Person object.
 * @param  MasVideos_Person $b Second MasVideos_Person object.
 * @return int
 */
function masvideos_persons_array_orderby_menu_order( $a, $b ) {
    if ( $a->get_menu_order() === $b->get_menu_order() ) {
        return 0;
    }
    return ( $a->get_menu_order() < $b->get_menu_order() ) ? -1 : 1;
}

/**
 * Get related persons based on person category and tags.
 *
 * @since  1.0.0
 * @param  int   $person_id  Person ID.
 * @param  int   $limit       Limit of results.
 * @param  array $exclude_ids Exclude IDs from the results.
 * @return array
 */
function masvideos_get_related_persons( $person_id, $limit = 5, $exclude_ids = array() ) {

    $person_id       = absint( $person_id );
    $limit          = $limit >= -1 ? $limit : 5;
    $exclude_ids    = array_merge( array( 0, $person_id ), $exclude_ids );
    $transient_name = 'masvideos_related_persons_' . $person_id;
    $query_args     = http_build_query(
        array(
            'limit'       => $limit,
            'exclude_ids' => $exclude_ids,
        )
    );

    $transient     = get_transient( $transient_name );
    $related_posts = $transient && isset( $transient[ $query_args ] ) ? $transient[ $query_args ] : false;

    // We want to query related posts if they are not cached, or we don't have enough.
    if ( false === $related_posts || count( $related_posts ) < $limit ) {

        $cats_array = apply_filters( 'masvideos_person_related_posts_relate_by_category', true, $person_id ) ? apply_filters( 'masvideos_get_related_person_cat_terms', masvideos_get_term_ids( $person_id, 'person_cat' ), $person_id ) : array();
        $tags_array = apply_filters( 'masvideos_person_related_posts_relate_by_tag', true, $person_id ) ? apply_filters( 'masvideos_get_related_person_tag_terms', masvideos_get_term_ids( $person_id, 'person_tag' ), $person_id ) : array();

        // Don't bother if none are set, unless masvideos_person_related_posts_force_display is set to true in which case all persons are related.
        if ( empty( $cats_array ) && empty( $tags_array ) && ! apply_filters( 'masvideos_person_related_posts_force_display', false, $person_id ) ) {
            $related_posts = array();
        } else {
            $data_store    = MasVideos_Data_Store::load( 'person' );
            $related_posts = $data_store->get_related_persons( $cats_array, $tags_array, $exclude_ids, $limit + 10, $person_id );
        }

        if ( $transient ) {
            $transient[ $query_args ] = $related_posts;
        } else {
            $transient = array( $query_args => $related_posts );
        }

        set_transient( $transient_name, $transient, DAY_IN_SECONDS );
    }

    $related_posts = apply_filters(
        'masvideos_related_persons', $related_posts, $person_id, array(
            'limit'        => $limit,
            'excluded_ids' => $exclude_ids,
        )
    );

    shuffle( $related_posts );

    return array_slice( $related_posts, 0, $limit );
}

if ( ! function_exists( 'masvideos_get_person_thumbnail' ) ) {
    /**
     * Get the masvideos thumbnail, or the placeholder if not set.
     */
    function masvideos_get_person_thumbnail( $size = 'masvideos_person_medium', $person = null ) {
        if ( is_null( $person ) && ! empty( $GLOBALS['person'] ) ) {
            // Person was null so pull from global.
            $person = $GLOBALS['person'];
        }

        if ( $person && ! is_a( $person, 'MasVideos_Person' ) ) {
            // Make sure we have a valid person, or set to false.
            $person = masvideos_get_person( $person );
        }

        $image_size = apply_filters( 'masvideos_person_archive_thumbnail_size', $size );

        return $person ? $person->get_image( $image_size , array( 'class' => 'person__poster--image' ) ) : '';
    }
}

/**
 * Filter to allow person_cat in the permalinks for persons.
 *
 * @param  string  $permalink The existing permalink URL.
 * @param  WP_Post $post WP_Post object.
 * @return string
 */
function masvideos_person_post_type_link( $permalink, $post ) {
    // Abort if post is not a person.
    if ( 'person' !== $post->post_type ) {
        return $permalink;
    }

    // Abort early if the placeholder rewrite tag isn't in the generated URL.
    if ( false === strpos( $permalink, '%' ) ) {
        return $permalink;
    }

    // Get the custom taxonomy terms in use by this post.
    $terms = get_the_terms( $post->ID, 'person_cat' );

    if ( ! empty( $terms ) ) {
        $terms           = wp_list_sort(
            $terms,
            array(
                'parent'  => 'DESC',
                'term_id' => 'ASC',
            )
        );
        $cat_object = apply_filters( 'masvideos_person_post_type_link_person_cat', $terms[0], $terms, $post );
        $person_cat     = $cat_object->slug;

        if ( $cat_object->parent ) {
            $ancestors = get_ancestors( $cat_object->term_id, 'person_cat' );
            foreach ( $ancestors as $ancestor ) {
                $ancestor_object = get_term( $ancestor, 'person_cat' );
                $person_cat     = $ancestor_object->slug . '/' . $person_cat;
            }
        }
    } else {
        // If no terms are assigned to this post, use a string instead (can't leave the placeholder there).
        $person_cat = _x( 'uncategorized', 'slug', 'masvideos' );
    }

    $find = array(
        '%year%',
        '%monthnum%',
        '%day%',
        '%hour%',
        '%minute%',
        '%second%',
        '%post_id%',
        '%category%',
        '%person_cat%',
    );

    $replace = array(
        date_i18n( 'Y', strtotime( $post->post_date ) ),
        date_i18n( 'm', strtotime( $post->post_date ) ),
        date_i18n( 'd', strtotime( $post->post_date ) ),
        date_i18n( 'H', strtotime( $post->post_date ) ),
        date_i18n( 'i', strtotime( $post->post_date ) ),
        date_i18n( 's', strtotime( $post->post_date ) ),
        $post->ID,
        $person_cat,
        $person_cat,
    );

    $permalink = str_replace( $find, $replace, $permalink );

    return $permalink;
}
add_filter( 'post_type_link', 'masvideos_person_post_type_link', 10, 2 );

/**
 * Check if person imdb_id is unique.
 *
 * @since  1.1
 * @param  int    $person_id Person ID.
 * @param  string $imdb_id Person IMDB ID.
 * @return bool
 */
function masvideos_person_has_unique_imdb_id( $person_id, $imdb_id ) {
    $data_store = MasVideos_Data_Store::load( 'person' );
    $imdb_id_found  = $data_store->is_existing_imdb_id( $person_id, $imdb_id );
    if ( apply_filters( 'masvideos_person_has_unique_imdb_id', $imdb_id_found, $person_id, $imdb_id ) ) {
        return false;
    }

    return true;
}

/**
 * Force a unique IMDB Id.
 *
 * @since 1.1
 * @param integer $person_id Person ID.
 */
function masvideos_person_force_unique_imdb_id( $person_id ) {
    $person     = masvideos_get_person( $person_id );
    $current_imdb_id = $person ? $person->get_imdb_id( 'edit' ) : '';

    if ( $current_imdb_id ) {
        try {
            $new_imdb_id = masvideos_person_generate_unique_imdb_id( $person_id, $current_imdb_id );

            if ( $current_imdb_id !== $new_imdb_id ) {
                $person->set_imdb_id( $new_imdb_id );
                $person->save();
            }
        } catch ( Exception $e ) {} // @codingStandardsIgnoreLine.
    }
}

/**
 * Recursively appends a suffix until a unique IMDB Id is found.
 *
 * @since  1.1
 * @param  integer $person_id Person ID.
 * @param  string  $imdb_id Person IMDB Id.
 * @param  integer $index An optional index that can be added to the person IMDB Id.
 * @return string
 */
function masvideos_person_generate_unique_imdb_id( $person_id, $imdb_id, $index = 0 ) {
    $generated_imdb_id = 0 < $index ? $imdb_id . '-' . $index : $imdb_id;

    if ( ! masvideos_person_has_unique_imdb_id( $person_id, $generated_imdb_id ) ) {
        $generated_imdb_id = masvideos_person_generate_unique_imdb_id( $person_id, $imdb_id, ( $index + 1 ) );
    }

    return $generated_imdb_id;
}

/**
 * Get person ID by IMDB Id.
 *
 * @since  1.1
 * @param  string $imdb_id Person IMDB Id.
 * @return int
 */
function masvideos_get_person_id_by_imdb_id( $imdb_id ) {
    $data_store = MasVideos_Data_Store::load( 'person' );
    return $data_store->get_person_id_by_imdb_id( $imdb_id );
}

/**
 * Check if person tmdb_id is unique.
 *
 * @since  1.1
 * @param  int    $person_id Person ID.
 * @param  string $tmdb_id Person TMDB ID.
 * @return bool
 */
function masvideos_person_has_unique_tmdb_id( $person_id, $tmdb_id ) {
    $data_store = MasVideos_Data_Store::load( 'person' );
    $tmdb_id_found  = $data_store->is_existing_tmdb_id( $person_id, $tmdb_id );

    if ( apply_filters( 'masvideos_person_has_unique_tmdb_id', $tmdb_id_found, $person_id, $tmdb_id ) ) {
        return false;
    }

    return true;
}

/**
 * Force a unique TMDB Id.
 *
 * @since 1.1
 * @param integer $person_id Person ID.
 */
function masvideos_person_force_unique_tmdb_id( $person_id ) {
    $person     = masvideos_get_person( $person_id );
    $current_tmdb_id = $person ? $person->get_tmdb_id( 'edit' ) : '';

    if ( $current_tmdb_id ) {
        try {
            $new_tmdb_id = masvideos_person_generate_unique_tmdb_id( $person_id, $current_tmdb_id );

            if ( $current_tmdb_id !== $new_tmdb_id ) {
                $person->set_tmdb_id( $new_tmdb_id );
                $person->save();
            }
        } catch ( Exception $e ) {} // @codingStandardsIgnoreLine.
    }
}

/**
 * Recursively appends a suffix until a unique TMDB Id is found.
 *
 * @since  1.1
 * @param  integer $person_id Person ID.
 * @param  string  $tmdb_id Person TMDB Id.
 * @param  integer $index An optional index that can be added to the person TMDB Id.
 * @return string
 */
function masvideos_person_generate_unique_tmdb_id( $person_id, $tmdb_id, $index = 0 ) {
    $generated_tmdb_id = 0 < $index ? $tmdb_id . '-' . $index : $tmdb_id;

    if ( ! masvideos_person_has_unique_tmdb_id( $person_id, $generated_tmdb_id ) ) {
        $generated_tmdb_id = masvideos_person_generate_unique_tmdb_id( $person_id, $tmdb_id, ( $index + 1 ) );
    }

    return $generated_tmdb_id;
}

/**
 * Get person ID by TMDB Id.
 *
 * @since  1.1
 * @param  string $tmdb_id Person TMDB Id.
 * @return int
 */
function masvideos_get_person_id_by_tmdb_id( $tmdb_id ) {
    $data_store = MasVideos_Data_Store::load( 'person' );
    return $data_store->get_person_id_by_tmdb_id( $tmdb_id );
}