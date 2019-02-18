<?php
/**
 * MasVideos Movie Functions
 *
 * Functions for movie specific things.
 *
 * @package MasVideos/Functions
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Standard way of retrieving movies based on certain parameters.
 *
 * This function should be used for movie retrieval so that we have a data agnostic
 * way to get a list of movies.
 *
 * @since  1.0.0
 * @param  array $args Array of args (above).
 * @return array|stdClass Number of pages and an array of movie objects if
 *                             paginate is true, or just an array of values.
 */
function masvideos_get_movies( $args ) {
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

    $query = new MasVideos_Movie_Query( $args );
    return $query->get_movies();
}

/**
 * Main function for returning movies, uses the MasVideos_Movie_Factory class.
 *
 * @since 1.0.0
 *
 * @param mixed $the_movie Post object or post ID of the movie.
 * @return MasVideos_Movie|null|false
 */
function masvideos_get_movie( $the_movie = false ) {
    if ( ! did_action( 'masvideos_init' ) ) {
        /* translators: 1: masvideos_get_movie 2: masvideos_init */
        _doing_it_wrong( __FUNCTION__, sprintf( __( '%1$s should not be called before the %2$s action.', 'masvideos' ), 'masvideos_get_movie', 'masvideos_init' ), '1.0.0' );
        return false;
    }
    return MasVideos()->movie_factory->get_movie( $the_movie );
}

/**
 * Clear all transients cache for movie data.
 *
 * @param int $post_id (default: 0).
 */
function masvideos_delete_movie_transients( $post_id = 0 ) {
    // Core transients.
    $transients_to_clear = array(
        'masvideos_featured_movies',
        'masvideos_count_comments',
    );

    // Transient names that include an ID.
    $post_transient_names = array(
        'masvideos_movie_children_',
        'masvideos_related_',
    );

    if ( $post_id > 0 ) {
        foreach ( $post_transient_names as $transient ) {
            $transients_to_clear[] = $transient . $post_id;
        }

        // Does this movie have a parent?
        $movie = masvideos_get_movie( $post_id );

        if ( $movie ) {
            if ( $movie->get_parent_id() > 0 ) {
                masvideos_delete_movie_transients( $movie->get_parent_id() );
            }
        }
    }

    // Delete transients.
    foreach ( $transients_to_clear as $transient ) {
        delete_transient( $transient );
    }

    // Increments the transient version to invalidate cache.
    MasVideos_Cache_Helper::get_transient_version( 'movie', true );

    do_action( 'masvideos_delete_movie_transients', $post_id );
}

/**
 * Get movie visibility options.
 *
 * @since 1.0.0
 * @return array
 */
function masvideos_get_movie_visibility_options() {
	return apply_filters(
		'masvideos_movie_visibility_options', array(
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
 * @param  MasVideos_Movie $movie MasVideos_Movie object.
 * @return bool
 */
function masvideos_movies_array_filter_visible( $movie ) {
    return $movie && is_a( $movie, 'MasVideos_Movie' ) && $movie->is_visible();
}

/**
 * Callback for array filter to get movies the user can edit only.
 *
 * @since  1.0.0
 * @param  MasVideos_Movie $movie MasVideos_Movie object.
 * @return bool
 */
function masvideos_movies_array_filter_editable( $movie ) {
    return $movie && is_a( $movie, 'MasVideos_Movie' ) && current_user_can( 'edit_movie', $movie->get_id() );
}

/**
 * Callback for array filter to get movies the user can view only.
 *
 * @since  1.0.0
 * @param  MasVideos_Movie $movie MasVideos_Movie object.
 * @return bool
 */
function masvideos_movies_array_filter_readable( $movie ) {
    return $movie && is_a( $movie, 'MasVideos_Movie' ) && current_user_can( 'read_movie', $movie->get_id() );
}

/**
 * Sort an array of movies by a value.
 *
 * @since  1.0.0
 *
 * @param array  $movies List of movies to be ordered.
 * @param string $orderby Optional order criteria.
 * @param string $order Ascending or descending order.
 *
 * @return array
 */
function masvideos_movies_array_orderby( $movies, $orderby = 'date', $order = 'desc' ) {
    $orderby = strtolower( $orderby );
    $order   = strtolower( $order );
    switch ( $orderby ) {
        case 'title':
        case 'id':
        case 'date':
        case 'modified':
        case 'menu_order':
            usort( $movies, 'masvideos_movies_array_orderby_' . $orderby );
            break;
        default:
            shuffle( $movies );
            break;
    }
    if ( 'desc' === $order ) {
        $movies = array_reverse( $movies );
    }
    return $movies;
}

/**
 * Sort by title.
 *
 * @since  1.0.0
 * @param  MasVideos_Movie $a First MasVideos_Movie object.
 * @param  MasVideos_Movie $b Second MasVideos_Movie object.
 * @return int
 */
function masvideos_movies_array_orderby_title( $a, $b ) {
    return strcasecmp( $a->get_name(), $b->get_name() );
}

/**
 * Sort by id.
 *
 * @since  1.0.0
 * @param  MasVideos_Movie $a First MasVideos_Movie object.
 * @param  MasVideos_Movie $b Second MasVideos_Movie object.
 * @return int
 */
function masvideos_movies_array_orderby_id( $a, $b ) {
    if ( $a->get_id() === $b->get_id() ) {
        return 0;
    }
    return ( $a->get_id() < $b->get_id() ) ? -1 : 1;
}

/**
 * Sort by date.
 *
 * @since  1.0.0
 * @param  MasVideos_Movie $a First MasVideos_Movie object.
 * @param  MasVideos_Movie $b Second MasVideos_Movie object.
 * @return int
 */
function masvideos_movies_array_orderby_date( $a, $b ) {
    if ( $a->get_date_created() === $b->get_date_created() ) {
        return 0;
    }
    return ( $a->get_date_created() < $b->get_date_created() ) ? -1 : 1;
}

/**
 * Sort by modified.
 *
 * @since  1.0.0
 * @param  MasVideos_Movie $a First MasVideos_Movie object.
 * @param  MasVideos_Movie $b Second MasVideos_Movie object.
 * @return int
 */
function masvideos_movies_array_orderby_modified( $a, $b ) {
    if ( $a->get_date_modified() === $b->get_date_modified() ) {
        return 0;
    }
    return ( $a->get_date_modified() < $b->get_date_modified() ) ? -1 : 1;
}

/**
 * Sort by menu order.
 *
 * @since  1.0.0
 * @param  MasVideos_Movie $a First MasVideos_Movie object.
 * @param  MasVideos_Movie $b Second MasVideos_Movie object.
 * @return int
 */
function masvideos_movies_array_orderby_menu_order( $a, $b ) {
    if ( $a->get_menu_order() === $b->get_menu_order() ) {
        return 0;
    }
    return ( $a->get_menu_order() < $b->get_menu_order() ) ? -1 : 1;
}

/**
 * Get related movies based on movie genre and tags.
 *
 * @since  1.0.0
 * @param  int   $movie_id  Movie ID.
 * @param  int   $limit       Limit of results.
 * @param  array $exclude_ids Exclude IDs from the results.
 * @return array
 */
function masvideos_get_related_movies( $movie_id, $limit = 5, $exclude_ids = array() ) {

    $movie_id       = absint( $movie_id );
    $limit          = $limit >= -1 ? $limit : 5;
    $exclude_ids    = array_merge( array( 0, $movie_id ), $exclude_ids );
    $transient_name = 'masvideos_related_movies_' . $movie_id;
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

        $genres_array = apply_filters( 'masvideos_movie_related_posts_relate_by_genre', true, $movie_id ) ? apply_filters( 'masvideos_get_related_movie_genre_terms', masvideos_get_term_ids( $movie_id, 'movie_genre' ), $movie_id ) : array();
        $tags_array = apply_filters( 'masvideos_movie_related_posts_relate_by_tag', true, $movie_id ) ? apply_filters( 'masvideos_get_related_movie_tag_terms', masvideos_get_term_ids( $movie_id, 'movie_tag' ), $movie_id ) : array();

        // Don't bother if none are set, unless masvideos_movie_related_posts_force_display is set to true in which case all movies are related.
        if ( empty( $genres_array ) && empty( $tags_array ) && ! apply_filters( 'masvideos_movie_related_posts_force_display', false, $movie_id ) ) {
            $related_posts = array();
        } else {
            $data_store    = WC_Data_Store::load( 'movie' );
            $related_posts = $data_store->get_related_movies( $genres_array, $tags_array, $exclude_ids, $limit + 10, $movie_id );
        }

        if ( $transient ) {
            $transient[ $query_args ] = $related_posts;
        } else {
            $transient = array( $query_args => $related_posts );
        }

        set_transient( $transient_name, $transient, DAY_IN_SECONDS );
    }

    $related_posts = apply_filters(
        'masvideos_related_movies', $related_posts, $movie_id, array(
            'limit'        => $limit,
            'excluded_ids' => $exclude_ids,
        )
    );

    shuffle( $related_posts );

    return array_slice( $related_posts, 0, $limit );
}

if ( ! function_exists ( 'masvideos_the_movie' ) ) {
    function masvideos_the_movie( $post = null ) {
        global $movie;

        $movie_src = masvideos_get_the_movie( $movie );
        $movie_choice = $movie->get_movie_choice();

        if ( ! empty ( $movie_src ) ) {
            if ( $movie_choice == 'movie_file' ) {
                echo do_shortcode('[video src="' . $movie_src . '"]');
            } elseif ( $movie_choice == 'movie_embed' ) {
                echo '<div class="wp-video">' . $movie_src . '</div>';
            } elseif ( $movie_choice == 'movie_url' ) {
                echo do_shortcode('[video src="' . $movie_src . '"]');
            }
        }
    }
}

if ( ! function_exists ( 'masvideos_get_the_movie' ) ) {
    function masvideos_get_the_movie( $post = null ) {
        global $movie;

        $movie_src = '';
        $movie_choice = $movie->get_movie_choice();

        if ( $movie_choice == 'movie_file' ) {
            $movie_src =  wp_get_attachment_url( $movie->get_movie_attachment_id() );
        } elseif ( $movie_choice == 'movie_embed' ) {
            $movie_src = $movie->get_movie_embed_content();
        } elseif ( $movie_choice == 'movie_url' ) {
            $movie_src = $movie->get_movie_url_link();
        }

        return $movie_src;
    }
}


if ( ! function_exists( 'masvideos_get_movie_thumbnail' ) ) {
    /**
     * Get the masvideos thumbnail, or the placeholder if not set.
     */
    function masvideos_get_movie_thumbnail( $size = 'masvideos_movie_medium' ) {
        global $movie;

        $image_size = apply_filters( 'masvideos_movie_archive_thumbnail_size', $size );

        return $movie ? $movie->get_image( $image_size , array( 'class' => 'movie__poster--image' ) ) : '';
    }
}