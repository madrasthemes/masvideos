<?php
/**
 * MasVideos Episode Functions
 *
 * Functions for episode specific things.
 *
 * @package MasVideos/Functions
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Standard way of retrieving episodes based on certain parameters.
 *
 * This function should be used for episode retrieval so that we have a data agnostic
 * way to get a list of episodes.
 *
 * @since  1.0.0
 * @param  array $args Array of args (above).
 * @return array|stdClass Number of pages and an array of episode objects if
 *                             paginate is true, or just an array of values.
 */
function masvideos_get_episodes( $args ) {
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

    $query = new MasVideos_Episode_Query( $args );
    return $query->get_episodes();
}

/**
 * Main function for returning episodes, uses the MasVideos_Episode_Factory class.
 *
 * @since 1.0.0
 *
 * @param mixed $the_episode Post object or post ID of the episode.
 * @return MasVideos_Episode|null|false
 */
function masvideos_get_episode( $the_episode = false ) {
    if ( ! did_action( 'masvideos_init' ) ) {
        /* translators: 1: masvideos_get_episode 2: masvideos_init */
        _doing_it_wrong( __FUNCTION__, sprintf( __( '%1$s should not be called before the %2$s action.', 'masvideos' ), 'masvideos_get_episode', 'masvideos_init' ), '1.0.0' );
        return false;
    }
    return MasVideos()->episode_factory->get_episode( $the_episode );
}

/**
 * Clear all transients cache for episode data.
 *
 * @param int $post_id (default: 0).
 */
function masvideos_delete_episode_transients( $post_id = 0 ) {
    // Core transients.
    $transients_to_clear = array(
        'masvideos_featured_episodes',
        'masvideos_count_comments',
    );

    // Transient names that include an ID.
    $post_transient_names = array(
        'masvideos_episode_children_',
        'masvideos_related_',
    );

    if ( $post_id > 0 ) {
        foreach ( $post_transient_names as $transient ) {
            $transients_to_clear[] = $transient . $post_id;
        }

        // Does this episode have a parent?
        $episode = masvideos_get_episode( $post_id );

        if ( $episode ) {
            if ( $episode->get_parent_id() > 0 ) {
                masvideos_delete_episode_transients( $episode->get_parent_id() );
            }
        }
    }

    // Delete transients.
    foreach ( $transients_to_clear as $transient ) {
        delete_transient( $transient );
    }

    // Increments the transient version to invalidate cache.
    MasVideos_Cache_Helper::get_transient_version( 'episode', true );

    do_action( 'masvideos_delete_episode_transients', $post_id );
}

/**
 * Get episode visibility options.
 *
 * @since 1.0.0
 * @return array
 */
function masvideos_get_episode_visibility_options() {
    return apply_filters(
        'masvideos_episode_visibility_options', array(
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
 * @param  MasVideos_Episode $episode MasVideos_Episode object.
 * @return bool
 */
function masvideos_episodes_array_filter_visible( $episode ) {
    return $episode && is_a( $episode, 'MasVideos_Episode' ) && $episode->is_visible();
}

/**
 * Callback for array filter to get episodes the user can edit only.
 *
 * @since  1.0.0
 * @param  MasVideos_Episode $episode MasVideos_Episode object.
 * @return bool
 */
function masvideos_episodes_array_filter_editable( $episode ) {
    return $episode && is_a( $episode, 'MasVideos_Episode' ) && current_user_can( 'edit_episode', $episode->get_id() );
}

/**
 * Callback for array filter to get episodes the user can view only.
 *
 * @since  1.0.0
 * @param  MasVideos_Episode $episode MasVideos_Episode object.
 * @return bool
 */
function masvideos_episodes_array_filter_readable( $episode ) {
    return $episode && is_a( $episode, 'MasVideos_Episode' ) && current_user_can( 'read_episode', $episode->get_id() );
}

/**
 * Sort an array of episodes by a value.
 *
 * @since  1.0.0
 *
 * @param array  $episodes List of episodes to be ordered.
 * @param string $orderby Optional order criteria.
 * @param string $order Ascending or descending order.
 *
 * @return array
 */
function masvideos_episodes_array_orderby( $episodes, $orderby = 'date', $order = 'desc' ) {
    $orderby = strtolower( $orderby );
    $order   = strtolower( $order );
    switch ( $orderby ) {
        case 'title':
        case 'id':
        case 'date':
        case 'modified':
        case 'menu_order':
            usort( $episodes, 'masvideos_episodes_array_orderby_' . $orderby );
            break;
        default:
            shuffle( $episodes );
            break;
    }
    if ( 'desc' === $order ) {
        $episodes = array_reverse( $episodes );
    }
    return $episodes;
}

/**
 * Sort by title.
 *
 * @since  1.0.0
 * @param  MasVideos_Episode $a First MasVideos_Episode object.
 * @param  MasVideos_Episode $b Second MasVideos_Episode object.
 * @return int
 */
function masvideos_episodes_array_orderby_title( $a, $b ) {
    return strcasecmp( $a->get_name(), $b->get_name() );
}

/**
 * Sort by id.
 *
 * @since  1.0.0
 * @param  MasVideos_Episode $a First MasVideos_Episode object.
 * @param  MasVideos_Episode $b Second MasVideos_Episode object.
 * @return int
 */
function masvideos_episodes_array_orderby_id( $a, $b ) {
    if ( $a->get_id() === $b->get_id() ) {
        return 0;
    }
    return ( $a->get_id() < $b->get_id() ) ? -1 : 1;
}

/**
 * Sort by date.
 *
 * @since  1.0.0
 * @param  MasVideos_Episode $a First MasVideos_Episode object.
 * @param  MasVideos_Episode $b Second MasVideos_Episode object.
 * @return int
 */
function masvideos_episodes_array_orderby_date( $a, $b ) {
    if ( $a->get_date_created() === $b->get_date_created() ) {
        return 0;
    }
    return ( $a->get_date_created() < $b->get_date_created() ) ? -1 : 1;
}

/**
 * Sort by modified.
 *
 * @since  1.0.0
 * @param  MasVideos_Episode $a First MasVideos_Episode object.
 * @param  MasVideos_Episode $b Second MasVideos_Episode object.
 * @return int
 */
function masvideos_episodes_array_orderby_modified( $a, $b ) {
    if ( $a->get_date_modified() === $b->get_date_modified() ) {
        return 0;
    }
    return ( $a->get_date_modified() < $b->get_date_modified() ) ? -1 : 1;
}

/**
 * Sort by menu order.
 *
 * @since  1.0.0
 * @param  MasVideos_Episode $a First MasVideos_Episode object.
 * @param  MasVideos_Episode $b Second MasVideos_Episode object.
 * @return int
 */
function masvideos_episodes_array_orderby_menu_order( $a, $b ) {
    if ( $a->get_menu_order() === $b->get_menu_order() ) {
        return 0;
    }
    return ( $a->get_menu_order() < $b->get_menu_order() ) ? -1 : 1;
}

/**
 * Get related episodes based on episode genre and tags.
 *
 * @since  1.0.0
 * @param  int   $episode_id  Episode ID.
 * @param  int   $limit       Limit of results.
 * @param  array $exclude_ids Exclude IDs from the results.
 * @return array
 */
function masvideos_get_related_episodes( $episode_id, $limit = 5, $exclude_ids = array() ) {

    $episode_id     = absint( $episode_id );
    $limit          = $limit >= -1 ? $limit : 5;
    $exclude_ids    = array_merge( array( 0, $episode_id ), $exclude_ids );
    $transient_name = 'masvideos_related_episodes_' . $episode_id;
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

        $genres_array = apply_filters( 'masvideos_episode_related_posts_relate_by_genre', true, $episode_id ) ? apply_filters( 'masvideos_get_related_episode_genre_terms', masvideos_get_term_ids( $episode_id, 'episode_genre' ), $episode_id ) : array();
        $tags_array = apply_filters( 'masvideos_episode_related_posts_relate_by_tag', true, $episode_id ) ? apply_filters( 'masvideos_get_related_episode_tag_terms', masvideos_get_term_ids( $episode_id, 'episode_tag' ), $episode_id ) : array();

        // Don't bother if none are set, unless masvideos_episode_related_posts_force_display is set to true in which case all episodes are related.
        if ( empty( $genres_array ) && empty( $tags_array ) && ! apply_filters( 'masvideos_episode_related_posts_force_display', false, $episode_id ) ) {
            $related_posts = array();
        } else {
            $data_store    = MasVideos_Data_Store::load( 'episode' );
            $related_posts = $data_store->get_related_episodes( $genres_array, $tags_array, $exclude_ids, $limit + 10, $episode_id );
        }

        if ( $transient ) {
            $transient[ $query_args ] = $related_posts;
        } else {
            $transient = array( $query_args => $related_posts );
        }

        set_transient( $transient_name, $transient, DAY_IN_SECONDS );
    }

    $related_posts = apply_filters(
        'masvideos_related_episodes', $related_posts, $episode_id, array(
            'limit'        => $limit,
            'excluded_ids' => $exclude_ids,
        )
    );

    shuffle( $related_posts );

    return array_slice( $related_posts, 0, $limit );
}

if ( ! function_exists ( 'masvideos_the_episode' ) ) {
    function masvideos_the_episode( $post = null ) {
        echo masvideos_get_the_episode( $post );
    }
}

if ( ! function_exists ( 'masvideos_get_the_episode' ) ) {
    function masvideos_get_the_episode( $episode = null ) {
        if ( is_null( $episode ) && ! empty( $GLOBALS['episode'] ) ) {
            // Episode was null so pull from global.
            $episode = $GLOBALS['episode'];
        }

        if ( $episode && ! is_a( $episode, 'MasVideos_Episode' ) ) {
            // Make sure we have a valid episode, or set to false.
            $episode = masvideos_get_episode( $episode );
        }

        $episode_src = '';
        $episode_choice = $episode->get_episode_choice();

        if ( $episode_choice == 'episode_file' ) {
            $episode_src =  wp_get_attachment_url( $episode->get_episode_attachment_id() );
        } elseif ( $episode_choice == 'episode_embed' ) {
            $episode_src = $episode->get_episode_embed_content();
        } elseif ( $episode_choice == 'episode_url' ) {
            $episode_src = $episode->get_episode_url_link();
        }

        return apply_filters( 'the_content', $episode_src );
    }
}

if ( ! function_exists( 'masvideos_get_episode_thumbnail' ) ) {
    /**
     * Get the masvideos thumbnail, or the placeholder if not set.
     */
    function masvideos_get_episode_thumbnail( $size = 'masvideos_episode_medium' ) {
        global $episode;

        $image_size = apply_filters( 'masvideos_episode_archive_thumbnail_size', $size );

        return $episode ? $episode->get_image( $image_size , array( 'class' => 'episode__poster--image' ) ) : '';
    }
}

if ( ! function_exists( 'masvideos_get_single_episode_prev_next_ids' ) ) {

    /**
     * Episode previous and next ids.
     */
    function masvideos_get_single_episode_prev_next_ids( $episode ) {
        $episodes = array();
        $prev = false;
        $next = false;

        $episode_id = $episode->get_id();
        $tv_show_id = $episode->get_tv_show_id();
        $tv_show = masvideos_get_tv_show( $tv_show_id );

        if ( $tv_show ) {
            $seasons = $tv_show->get_seasons();
            foreach ( $seasons as $season ) {
                if( ! empty( $season['episodes'] ) ) {
                    foreach ( $season['episodes'] as $episode ) {
                        $episodes[] = $episode;
                    }
                }
            }
        }

        if( ! empty( $episodes ) ) {
            $current_key = array_search( $episode_id, $episodes );
            $prev_key = masvideos_array_get_adjascent_key( $current_key, $episodes, -1 );
            $next_key = masvideos_array_get_adjascent_key( $current_key, $episodes, +1 );
            $prev = ( $prev_key !== false && $prev_key >= 0 && $prev_key < sizeof( $episodes ) ) ? $episodes[$prev_key] : false;
            $next = ( $next_key !== false && $next_key >= 0 && $next_key < sizeof( $episodes ) ) ? $episodes[$next_key] : false;
        }

        return array( 'prev' => $prev, 'next' => $next );
    }
}

/**
 * Filter to allow episode_genre in the permalinks for episodes.
 *
 * @param  string  $permalink The existing permalink URL.
 * @param  WP_Post $post WP_Post object.
 * @return string
 */
function masvideos_episode_post_type_link( $permalink, $post ) {
    // Abort if post is not a episode.
    if ( 'episode' !== $post->post_type ) {
        return $permalink;
    }

    // Abort early if the placeholder rewrite tag isn't in the generated URL.
    if ( false === strpos( $permalink, '%' ) ) {
        return $permalink;
    }

    if ( masvideos_is_episode_archive() ) {
        // Get the custom taxonomy terms in use by this post.
        $terms = get_the_terms( $post->ID, 'episode_genre' );

        if ( ! empty( $terms ) ) {
            $terms           = wp_list_sort(
                $terms,
                array(
                    'parent'  => 'DESC',
                    'term_id' => 'ASC',
                )
            );
            $genre_object = apply_filters( 'masvideos_episode_post_type_link_episode_genre', $terms[0], $terms, $post );
            $episode_genre     = $genre_object->slug;

            if ( $genre_object->parent ) {
                $ancestors = get_ancestors( $genre_object->term_id, 'episode_genre' );
                foreach ( $ancestors as $ancestor ) {
                    $ancestor_object = get_term( $ancestor, 'episode_genre' );
                    $episode_genre     = $ancestor_object->slug . '/' . $episode_genre;
                }
            }
        } else {
            // If no terms are assigned to this post, use a string instead (can't leave the placeholder there).
            $episode_genre = _x( 'uncategorized', 'slug', 'masvideos' );
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
            '%episode_genre%',
        );

        $replace = array(
            date_i18n( 'Y', strtotime( $post->post_date ) ),
            date_i18n( 'm', strtotime( $post->post_date ) ),
            date_i18n( 'd', strtotime( $post->post_date ) ),
            date_i18n( 'H', strtotime( $post->post_date ) ),
            date_i18n( 'i', strtotime( $post->post_date ) ),
            date_i18n( 's', strtotime( $post->post_date ) ),
            $post->ID,
            $episode_genre,
            $episode_genre,
        );
    } else {
        $find = array(
            '%year%',
            '%monthnum%',
            '%day%',
            '%hour%',
            '%minute%',
            '%second%',
            '%post_id%',
        );

        $replace = array(
            date_i18n( 'Y', strtotime( $post->post_date ) ),
            date_i18n( 'm', strtotime( $post->post_date ) ),
            date_i18n( 'd', strtotime( $post->post_date ) ),
            date_i18n( 'H', strtotime( $post->post_date ) ),
            date_i18n( 'i', strtotime( $post->post_date ) ),
            date_i18n( 's', strtotime( $post->post_date ) ),
            $post->ID,
        );
    }

    $permalink = str_replace( $find, $replace, $permalink );

    return $permalink;
}
add_filter( 'post_type_link', 'masvideos_episode_post_type_link', 10, 2 );

/**
 * Check if episode imdb_id is unique.
 *
 * @since  1.1
 * @param  int    $episode_id Episode ID.
 * @param  string $imdb_id Episode IMDB ID.
 * @return bool
 */
function masvideos_episode_has_unique_imdb_id( $episode_id, $imdb_id ) {
    $data_store = MasVideos_Data_Store::load( 'episode' );
    $imdb_id_found  = $data_store->is_existing_imdb_id( $episode_id, $imdb_id );
    if ( apply_filters( 'masvideos_episode_has_unique_imdb_id', $imdb_id_found, $episode_id, $imdb_id ) ) {
        return false;
    }

    return true;
}

/**
 * Force a unique IMDB Id.
 *
 * @since 1.1
 * @param integer $episode_id Episode ID.
 */
function masvideos_episode_force_unique_imdb_id( $episode_id ) {
    $episode     = masvideos_get_episode( $episode_id );
    $current_imdb_id = $episode ? $episode->get_imdb_id( 'edit' ) : '';

    if ( $current_imdb_id ) {
        try {
            $new_imdb_id = masvideos_episode_generate_unique_imdb_id( $episode_id, $current_imdb_id );

            if ( $current_imdb_id !== $new_imdb_id ) {
                $episode->set_imdb_id( $new_imdb_id );
                $episode->save();
            }
        } catch ( Exception $e ) {} // @codingStandardsIgnoreLine.
    }
}

/**
 * Recursively appends a suffix until a unique IMDB Id is found.
 *
 * @since  1.1
 * @param  integer $episode_id Episode ID.
 * @param  string  $imdb_id Episode IMDB Id.
 * @param  integer $index An optional index that can be added to the episode IMDB Id.
 * @return string
 */
function masvideos_episode_generate_unique_imdb_id( $episode_id, $imdb_id, $index = 0 ) {
    $generated_imdb_id = 0 < $index ? $imdb_id . '-' . $index : $imdb_id;

    if ( ! masvideos_episode_has_unique_imdb_id( $episode_id, $generated_imdb_id ) ) {
        $generated_imdb_id = masvideos_episode_generate_unique_imdb_id( $episode_id, $imdb_id, ( $index + 1 ) );
    }

    return $generated_imdb_id;
}

/**
 * Get episode ID by IMDB Id.
 *
 * @since  1.1
 * @param  string $imdb_id Episode IMDB Id.
 * @return int
 */
function masvideos_get_episode_id_by_imdb_id( $imdb_id ) {
    $data_store = MasVideos_Data_Store::load( 'episode' );
    return $data_store->get_episode_id_by_imdb_id( $imdb_id );
}

/**
 * Check if episode tmdb_id is unique.
 *
 * @since  1.1
 * @param  int    $episode_id Episode ID.
 * @param  string $tmdb_id Episode TMDB ID.
 * @return bool
 */
function masvideos_episode_has_unique_tmdb_id( $episode_id, $tmdb_id ) {
    $data_store = MasVideos_Data_Store::load( 'episode' );
    $tmdb_id_found  = $data_store->is_existing_tmdb_id( $episode_id, $tmdb_id );

    if ( apply_filters( 'masvideos_episode_has_unique_tmdb_id', $tmdb_id_found, $episode_id, $tmdb_id ) ) {
        return false;
    }

    return true;
}

/**
 * Force a unique TMDB Id.
 *
 * @since 1.1
 * @param integer $episode_id Episode ID.
 */
function masvideos_episode_force_unique_tmdb_id( $episode_id ) {
    $episode     = masvideos_get_episode( $episode_id );
    $current_tmdb_id = $episode ? $episode->get_tmdb_id( 'edit' ) : '';

    if ( $current_tmdb_id ) {
        try {
            $new_tmdb_id = masvideos_episode_generate_unique_tmdb_id( $episode_id, $current_tmdb_id );

            if ( $current_tmdb_id !== $new_tmdb_id ) {
                $episode->set_tmdb_id( $new_tmdb_id );
                $episode->save();
            }
        } catch ( Exception $e ) {} // @codingStandardsIgnoreLine.
    }
}

/**
 * Recursively appends a suffix until a unique TMDB Id is found.
 *
 * @since  1.1
 * @param  integer $episode_id Episode ID.
 * @param  string  $tmdb_id Episode TMDB Id.
 * @param  integer $index An optional index that can be added to the episode TMDB Id.
 * @return string
 */
function masvideos_episode_generate_unique_tmdb_id( $episode_id, $tmdb_id, $index = 0 ) {
    $generated_tmdb_id = 0 < $index ? $tmdb_id . '-' . $index : $tmdb_id;

    if ( ! masvideos_episode_has_unique_tmdb_id( $episode_id, $generated_tmdb_id ) ) {
        $generated_tmdb_id = masvideos_episode_generate_unique_tmdb_id( $episode_id, $tmdb_id, ( $index + 1 ) );
    }

    return $generated_tmdb_id;
}

/**
 * Get episode ID by TMDB Id.
 *
 * @since  1.1
 * @param  string $tmdb_id Episode TMDB Id.
 * @return int
 */
function masvideos_get_episode_id_by_tmdb_id( $tmdb_id ) {
    $data_store = MasVideos_Data_Store::load( 'episode' );
    return $data_store->get_episode_id_by_tmdb_id( $tmdb_id );
}