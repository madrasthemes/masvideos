<?php
/**
 * MasVideos TV Show Functions
 *
 * Functions for tv show specific things.
 *
 * @package MasVideos/Functions
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Standard way of retrieving tv shows based on certain parameters.
 *
 * This function should be used for tv show retrieval so that we have a data agnostic
 * way to get a list of tv shows.
 *
 * @since  1.0.0
 * @param  array $args Array of args (above).
 * @return array|stdClass Number of pages and an array of tv show objects if
 *                             paginate is true, or just an array of values.
 */
function masvideos_get_tv_shows( $args ) {
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

    $query = new MasVideos_TV_Show_Query( $args );
    return $query->get_tv_shows();
}

/**
 * Main function for returning tv_shows, uses the MasVideos_TV_Show_Factory class.
 *
 * @since 1.0.0
 *
 * @param mixed $the_tv_show Post object or post ID of the tv_show.
 * @return MasVideos_TV_Show|null|false
 */
function masvideos_get_tv_show( $the_tv_show = false ) {
    if ( ! did_action( 'masvideos_init' ) ) {
        /* translators: 1: masvideos_get_tv_show 2: masvideos_init */
        _doing_it_wrong( __FUNCTION__, sprintf( __( '%1$s should not be called before the %2$s action.', 'masvideos' ), 'masvideos_get_tv_show', 'masvideos_init' ), '1.0.0' );
        return false;
    }
    return MasVideos()->tv_show_factory->get_tv_show( $the_tv_show );
}

/**
 * Clear all transients cache for tv show data.
 *
 * @param int $post_id (default: 0).
 */
function masvideos_delete_tv_show_transients( $post_id = 0 ) {
    // Core transients.
    $transients_to_clear = array(
        'masvideos_featured_tv_shows',
        'masvideos_count_comments',
    );

    // Transient names that include an ID.
    $post_transient_names = array(
        'masvideos_tv_show_children_',
        'masvideos_related_',
    );

    if ( $post_id > 0 ) {
        foreach ( $post_transient_names as $transient ) {
            $transients_to_clear[] = $transient . $post_id;
        }

        // Does this tv show have a parent?
        $tv_show = masvideos_get_tv_show( $post_id );

        if ( $tv_show ) {
            if ( $tv_show->get_parent_id() > 0 ) {
                masvideos_delete_tv_show_transients( $tv_show->get_parent_id() );
            }
        }
    }

    // Delete transients.
    foreach ( $transients_to_clear as $transient ) {
        delete_transient( $transient );
    }

    // Increments the transient version to invalidate cache.
    MasVideos_Cache_Helper::get_transient_version( 'tv_show', true );

    do_action( 'masvideos_delete_tv_show_transients', $post_id );
}

/**
 * Get tv show visibility options.
 *
 * @since 1.0.0
 * @return array
 */
function masvideos_get_tv_show_visibility_options() {
    return apply_filters(
        'masvideos_tv_show_visibility_options', array(
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
 * @param  MasVideos_TV_Show $tv_show MasVideos_TV_Show object.
 * @return bool
 */
function masvideos_tv_shows_array_filter_visible( $tv_show ) {
    return $tv_show && is_a( $tv_show, 'MasVideos_TV_Show' ) && $tv_show->is_visible();
}

/**
 * Callback for array filter to get tv shows the user can edit only.
 *
 * @since  1.0.0
 * @param  MasVideos_TV_Show $tv_show MasVideos_TV_Show object.
 * @return bool
 */
function masvideos_tv_shows_array_filter_editable( $tv_show ) {
    return $tv_show && is_a( $tv_show, 'MasVideos_TV_Show' ) && current_user_can( 'edit_tv_show', $tv_show->get_id() );
}

/**
 * Callback for array filter to get tv shows the user can view only.
 *
 * @since  1.0.0
 * @param  MasVideos_TV_Show $tv_show MasVideos_TV_Show object.
 * @return bool
 */
function masvideos_tv_shows_array_filter_readable( $tv_show ) {
    return $tv_show && is_a( $tv_show, 'MasVideos_TV_Show' ) && current_user_can( 'read_tv_show', $tv_show->get_id() );
}

/**
 * Sort an array of tv_shows by a value.
 *
 * @since  1.0.0
 *
 * @param array  $tv_shows List of tv_shows to be ordered.
 * @param string $orderby Optional order criteria.
 * @param string $order Ascending or descending order.
 *
 * @return array
 */
function masvideos_tv_shows_array_orderby( $tv_shows, $orderby = 'date', $order = 'desc' ) {
    $orderby = strtolower( $orderby );
    $order   = strtolower( $order );
    switch ( $orderby ) {
        case 'title':
        case 'id':
        case 'date':
        case 'modified':
        case 'menu_order':
            usort( $tv_shows, 'masvideos_tv_shows_array_orderby_' . $orderby );
            break;
        default:
            shuffle( $tv_shows );
            break;
    }
    if ( 'desc' === $order ) {
        $tv_shows = array_reverse( $tv_shows );
    }
    return $tv_shows;
}

/**
 * Sort by title.
 *
 * @since  1.0.0
 * @param  MasVideos_TV_Show $a First MasVideos_TV_Show object.
 * @param  MasVideos_TV_Show $b Second MasVideos_TV_Show object.
 * @return int
 */
function masvideos_tv_shows_array_orderby_title( $a, $b ) {
    return strcasecmp( $a->get_name(), $b->get_name() );
}

/**
 * Sort by id.
 *
 * @since  1.0.0
 * @param  MasVideos_TV_Show $a First MasVideos_TV_Show object.
 * @param  MasVideos_TV_Show $b Second MasVideos_TV_Show object.
 * @return int
 */
function masvideos_tv_shows_array_orderby_id( $a, $b ) {
    if ( $a->get_id() === $b->get_id() ) {
        return 0;
    }
    return ( $a->get_id() < $b->get_id() ) ? -1 : 1;
}

/**
 * Sort by date.
 *
 * @since  1.0.0
 * @param  MasVideos_TV_Show $a First MasVideos_TV_Show object.
 * @param  MasVideos_TV_Show $b Second MasVideos_TV_Show object.
 * @return int
 */
function masvideos_tv_shows_array_orderby_date( $a, $b ) {
    if ( $a->get_date_created() === $b->get_date_created() ) {
        return 0;
    }
    return ( $a->get_date_created() < $b->get_date_created() ) ? -1 : 1;
}

/**
 * Sort by modified.
 *
 * @since  1.0.0
 * @param  MasVideos_TV_Show $a First MasVideos_TV_Show object.
 * @param  MasVideos_TV_Show $b Second MasVideos_TV_Show object.
 * @return int
 */
function masvideos_tv_shows_array_orderby_modified( $a, $b ) {
    if ( $a->get_date_modified() === $b->get_date_modified() ) {
        return 0;
    }
    return ( $a->get_date_modified() < $b->get_date_modified() ) ? -1 : 1;
}

/**
 * Sort by menu order.
 *
 * @since  1.0.0
 * @param  MasVideos_TV_Show $a First MasVideos_TV_Show object.
 * @param  MasVideos_TV_Show $b Second MasVideos_TV_Show object.
 * @return int
 */
function masvideos_tv_shows_array_orderby_menu_order( $a, $b ) {
    if ( $a->get_menu_order() === $b->get_menu_order() ) {
        return 0;
    }
    return ( $a->get_menu_order() < $b->get_menu_order() ) ? -1 : 1;
}

/**
 * Get related tv shows based on tv show genre and tags.
 *
 * @since  1.0.0
 * @param  int   $tv_show_id  TV Show ID.
 * @param  int   $limit       Limit of results.
 * @param  array $exclude_ids Exclude IDs from the results.
 * @return array
 */
function masvideos_get_related_tv_shows( $tv_show_id, $limit = 5, $exclude_ids = array() ) {

    $tv_show_id     = absint( $tv_show_id );
    $limit          = $limit >= -1 ? $limit : 5;
    $exclude_ids    = array_merge( array( 0, $tv_show_id ), $exclude_ids );
    $transient_name = 'masvideos_related_tv_shows_' . $tv_show_id;
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

        $genres_array = apply_filters( 'masvideos_tv_show_related_posts_relate_by_genre', true, $tv_show_id ) ? apply_filters( 'masvideos_get_related_tv_show_genre_terms', masvideos_get_term_ids( $tv_show_id, 'tv_show_genre' ), $tv_show_id ) : array();
        $tags_array = apply_filters( 'masvideos_tv_show_related_posts_relate_by_tag', true, $tv_show_id ) ? apply_filters( 'masvideos_get_related_tv_show_tag_terms', masvideos_get_term_ids( $tv_show_id, 'tv_show_tag' ), $tv_show_id ) : array();

        // Don't bother if none are set, unless masvideos_tv_show_related_posts_force_display is set to true in which case all tv_shows are related.
        if ( empty( $genres_array ) && empty( $tags_array ) && ! apply_filters( 'masvideos_tv_show_related_posts_force_display', false, $tv_show_id ) ) {
            $related_posts = array();
        } else {
            $data_store    = MasVideos_Data_Store::load( 'tv_show' );
            $related_posts = $data_store->get_related_tv_shows( $genres_array, $tags_array, $exclude_ids, $limit + 10, $tv_show_id );
        }

        if ( $transient ) {
            $transient[ $query_args ] = $related_posts;
        } else {
            $transient = array( $query_args => $related_posts );
        }

        set_transient( $transient_name, $transient, DAY_IN_SECONDS );
    }

    $related_posts = apply_filters(
        'masvideos_related_tv_shows', $related_posts, $tv_show_id, array(
            'limit'        => $limit,
            'excluded_ids' => $exclude_ids,
        )
    );

    shuffle( $related_posts );

    return array_slice( $related_posts, 0, $limit );
}

if ( ! function_exists( 'masvideos_get_tv_show_thumbnail' ) ) {
    /**
     * Get the masvideos thumbnail, or the placeholder if not set.
     */
    function masvideos_get_tv_show_thumbnail( $size = 'masvideos_tv_show_medium' ) {
        global $tv_show;

        $image_size = apply_filters( 'masvideos_tv_show_archive_thumbnail_size', $size );
        return $tv_show ? $tv_show->get_image( $image_size , array( 'class' => 'tv-show__poster--image tv_show__poster--image' ) ) : '';
        
    }
}

if ( ! function_exists( 'masvideos_get_tv_show_all_episodes' ) ) {
    function masvideos_get_tv_show_all_episodes() {
        global $tv_show;

        $episodes = array();

        $seasons = $tv_show->get_seasons();
        if ( ! empty ($seasons) ) {
            foreach ( $seasons as $season ) {
                if( ! empty( $season['episodes'] ) ) {
                    foreach ( $season['episodes'] as $episode ) {
                        $episodes[] = array(
                            'season_name' => ! empty( $season['name'] ) ? $season['name'] : '',
                            'episode' => $episode,
                        );
                    }
                }
            }
        }

        return $episodes;
    }
}

if ( ! function_exists( 'masvideos_get_tv_show_all_season_titles' ) ) {
    function masvideos_get_tv_show_all_season_titles() {
        global $tv_show;

        $seasons = $tv_show->get_seasons();
        $season_titles = ! empty( $seasons ) ? array_column( $seasons, 'name' ) : array();

        return $season_titles;
    }
}

/**
 * Filter to allow tv_show_genre in the permalinks for tv_shows.
 *
 * @param  string  $permalink The existing permalink URL.
 * @param  WP_Post $post WP_Post object.
 * @return string
 */
function masvideos_tv_show_post_type_link( $permalink, $post ) {
    // Abort if post is not a tv_show.
    if ( 'tv_show' !== $post->post_type ) {
        return $permalink;
    }

    // Abort early if the placeholder rewrite tag isn't in the generated URL.
    if ( false === strpos( $permalink, '%' ) ) {
        return $permalink;
    }

    // Get the custom taxonomy terms in use by this post.
    $terms = get_the_terms( $post->ID, 'tv_show_genre' );

    if ( ! empty( $terms ) ) {
        $terms           = wp_list_sort(
            $terms,
            array(
                'parent'  => 'DESC',
                'term_id' => 'ASC',
            )
        );
        $genre_object = apply_filters( 'masvideos_tv_show_post_type_link_tv_show_genre', $terms[0], $terms, $post );
        $tv_show_genre     = $genre_object->slug;

        if ( $genre_object->parent ) {
            $ancestors = get_ancestors( $genre_object->term_id, 'tv_show_genre' );
            foreach ( $ancestors as $ancestor ) {
                $ancestor_object = get_term( $ancestor, 'tv_show_genre' );
                $tv_show_genre     = $ancestor_object->slug . '/' . $tv_show_genre;
            }
        }
    } else {
        // If no terms are assigned to this post, use a string instead (can't leave the placeholder there).
        $tv_show_genre = _x( 'uncategorized', 'slug', 'masvideos' );
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
        '%tv_show_genre%',
    );

    $replace = array(
        date_i18n( 'Y', strtotime( $post->post_date ) ),
        date_i18n( 'm', strtotime( $post->post_date ) ),
        date_i18n( 'd', strtotime( $post->post_date ) ),
        date_i18n( 'H', strtotime( $post->post_date ) ),
        date_i18n( 'i', strtotime( $post->post_date ) ),
        date_i18n( 's', strtotime( $post->post_date ) ),
        $post->ID,
        $tv_show_genre,
        $tv_show_genre,
    );

    $permalink = str_replace( $find, $replace, $permalink );

    return $permalink;
}
add_filter( 'post_type_link', 'masvideos_tv_show_post_type_link', 10, 2 );

/**
 * Check if tv show imdb_id is unique.
 *
 * @since  1.1
 * @param  int    $tv_show_id TV Show ID.
 * @param  string $imdb_id TV Show IMDB ID.
 * @return bool
 */
function masvideos_tv_show_has_unique_imdb_id( $tv_show_id, $imdb_id ) {
    $data_store = MasVideos_Data_Store::load( 'tv_show' );
    $imdb_id_found  = $data_store->is_existing_imdb_id( $tv_show_id, $imdb_id );
    if ( apply_filters( 'masvideos_tv_show_has_unique_imdb_id', $imdb_id_found, $tv_show_id, $imdb_id ) ) {
        return false;
    }

    return true;
}

/**
 * Force a unique IMDB Id.
 *
 * @since 1.1
 * @param integer $tv_show_id TV Show ID.
 */
function masvideos_tv_show_force_unique_imdb_id( $tv_show_id ) {
    $tv_show     = masvideos_get_tv_show( $tv_show_id );
    $current_imdb_id = $tv_show ? $tv_show->get_imdb_id( 'edit' ) : '';

    if ( $current_imdb_id ) {
        try {
            $new_imdb_id = masvideos_tv_show_generate_unique_imdb_id( $tv_show_id, $current_imdb_id );

            if ( $current_imdb_id !== $new_imdb_id ) {
                $tv_show->set_imdb_id( $new_imdb_id );
                $tv_show->save();
            }
        } catch ( Exception $e ) {} // @codingStandardsIgnoreLine.
    }
}

/**
 * Recursively appends a suffix until a unique IMDB Id is found.
 *
 * @since  1.1
 * @param  integer $tv_show_id TV Show ID.
 * @param  string  $imdb_id TV Show IMDB Id.
 * @param  integer $index An optional index that can be added to the tv show IMDB Id.
 * @return string
 */
function masvideos_tv_show_generate_unique_imdb_id( $tv_show_id, $imdb_id, $index = 0 ) {
    $generated_imdb_id = 0 < $index ? $imdb_id . '-' . $index : $imdb_id;

    if ( ! masvideos_tv_show_has_unique_imdb_id( $tv_show_id, $generated_imdb_id ) ) {
        $generated_imdb_id = masvideos_tv_show_generate_unique_imdb_id( $tv_show_id, $imdb_id, ( $index + 1 ) );
    }

    return $generated_imdb_id;
}

/**
 * Get tv show ID by IMDB Id.
 *
 * @since  1.1
 * @param  string $imdb_id TV Show IMDB Id.
 * @return int
 */
function masvideos_get_tv_show_id_by_imdb_id( $imdb_id ) {
    $data_store = MasVideos_Data_Store::load( 'tv_show' );
    return $data_store->get_tv_show_id_by_imdb_id( $imdb_id );
}

/**
 * Check if tv show tmdb_id is unique.
 *
 * @since  1.1
 * @param  int    $tv_show_id TV Show ID.
 * @param  string $tmdb_id TV Show TMDB ID.
 * @return bool
 */
function masvideos_tv_show_has_unique_tmdb_id( $tv_show_id, $tmdb_id ) {
    $data_store = MasVideos_Data_Store::load( 'tv_show' );
    $tmdb_id_found  = $data_store->is_existing_tmdb_id( $tv_show_id, $tmdb_id );

    if ( apply_filters( 'masvideos_tv_show_has_unique_tmdb_id', $tmdb_id_found, $tv_show_id, $tmdb_id ) ) {
        return false;
    }

    return true;
}

/**
 * Force a unique TMDB Id.
 *
 * @since 1.1
 * @param integer $tv_show_id TV Show ID.
 */
function masvideos_tv_show_force_unique_tmdb_id( $tv_show_id ) {
    $tv_show     = masvideos_get_tv_show( $tv_show_id );
    $current_tmdb_id = $tv_show ? $tv_show->get_tmdb_id( 'edit' ) : '';

    if ( $current_tmdb_id ) {
        try {
            $new_tmdb_id = masvideos_tv_show_generate_unique_tmdb_id( $tv_show_id, $current_tmdb_id );

            if ( $current_tmdb_id !== $new_tmdb_id ) {
                $tv_show->set_tmdb_id( $new_tmdb_id );
                $tv_show->save();
            }
        } catch ( Exception $e ) {} // @codingStandardsIgnoreLine.
    }
}

/**
 * Recursively appends a suffix until a unique TMDB Id is found.
 *
 * @since  1.1
 * @param  integer $tv_show_id TV Show ID.
 * @param  string  $tmdb_id TV Show TMDB Id.
 * @param  integer $index An optional index that can be added to the tv show TMDB Id.
 * @return string
 */
function masvideos_tv_show_generate_unique_tmdb_id( $tv_show_id, $tmdb_id, $index = 0 ) {
    $generated_tmdb_id = 0 < $index ? $tmdb_id . '-' . $index : $tmdb_id;

    if ( ! masvideos_tv_show_has_unique_tmdb_id( $tv_show_id, $generated_tmdb_id ) ) {
        $generated_tmdb_id = masvideos_tv_show_generate_unique_tmdb_id( $tv_show_id, $tmdb_id, ( $index + 1 ) );
    }

    return $generated_tmdb_id;
}

/**
 * Get tv show ID by TMDB Id.
 *
 * @since  1.1
 * @param  string $tmdb_id TV Show TMDB Id.
 * @return int
 */
function masvideos_get_tv_show_id_by_tmdb_id( $tmdb_id ) {
    $data_store = MasVideos_Data_Store::load( 'tv_show' );
    return $data_store->get_tv_show_id_by_tmdb_id( $tmdb_id );
}