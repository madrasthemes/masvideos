<?php
/**
 * MasVideos Video Functions
 *
 * Functions for video specific things.
 *
 * @package MasVideos/Functions
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Standard way of retrieving videos based on certain parameters.
 *
 * This function should be used for video retrieval so that we have a data agnostic
 * way to get a list of videos.
 *
 * @since  1.0.0
 * @param  array $args Array of args (above).
 * @return array|stdClass Number of pages and an array of video objects if
 *                             paginate is true, or just an array of values.
 */
function masvideos_get_videos( $args ) {
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

    $query = new MasVideos_Video_Query( $args );
    return $query->get_videos();
}

/**
 * Main function for returning videos, uses the MasVideos_Video_Factory class.
 *
 * @since 1.0.0
 *
 * @param mixed $the_video Post object or post ID of the video.
 * @return MasVideos_Video|null|false
 */
function masvideos_get_video( $the_video = false ) {
    if ( ! did_action( 'masvideos_init' ) ) {
        /* translators: 1: masvideos_get_video 2: masvideos_init */
        _doing_it_wrong( __FUNCTION__, sprintf( __( '%1$s should not be called before the %2$s action.', 'masvideos' ), 'masvideos_get_video', 'masvideos_init' ), '1.0.0' );
        return false;
    }
    return MasVideos()->video_factory->get_video( $the_video );
}

/**
 * Clear all transients cache for video data.
 *
 * @param int $post_id (default: 0).
 */
function masvideos_delete_video_transients( $post_id = 0 ) {
    // Core transients.
    $transients_to_clear = array(
        'masvideos_featured_videos',
        'masvideos_count_comments',
    );

    // Transient names that include an ID.
    $post_transient_names = array(
        'masvideos_video_children_',
        'masvideos_related_',
    );

    if ( $post_id > 0 ) {
        foreach ( $post_transient_names as $transient ) {
            $transients_to_clear[] = $transient . $post_id;
        }

        // Does this video have a parent?
        $video = masvideos_get_video( $post_id );

        if ( $video ) {
            if ( $video->get_parent_id() > 0 ) {
                masvideos_delete_video_transients( $video->get_parent_id() );
            }
        }
    }

    // Delete transients.
    foreach ( $transients_to_clear as $transient ) {
        delete_transient( $transient );
    }

    // Increments the transient version to invalidate cache.
    MasVideos_Cache_Helper::get_transient_version( 'video', true );

    do_action( 'masvideos_delete_video_transients', $post_id );
}

/**
 * Get video visibility options.
 *
 * @since 1.0.0
 * @return array
 */
function masvideos_get_video_visibility_options() {
	return apply_filters(
		'masvideos_video_visibility_options', array(
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
 * @param  MasVideos_Video $video MasVideos_Video object.
 * @return bool
 */
function masvideos_videos_array_filter_visible( $video ) {
    return $video && is_a( $video, 'MasVideos_Video' ) && $video->is_visible();
}

/**
 * Callback for array filter to get videos the user can edit only.
 *
 * @since  1.0.0
 * @param  MasVideos_Video $video MasVideos_Video object.
 * @return bool
 */
function masvideos_videos_array_filter_editable( $video ) {
    return $video && is_a( $video, 'MasVideos_Video' ) && current_user_can( 'edit_video', $video->get_id() );
}

/**
 * Callback for array filter to get videos the user can view only.
 *
 * @since  1.0.0
 * @param  MasVideos_Video $video MasVideos_Video object.
 * @return bool
 */
function masvideos_videos_array_filter_readable( $video ) {
    return $video && is_a( $video, 'MasVideos_Video' ) && current_user_can( 'read_video', $video->get_id() );
}

/**
 * Sort an array of videos by a value.
 *
 * @since  1.0.0
 *
 * @param array  $videos List of videos to be ordered.
 * @param string $orderby Optional order criteria.
 * @param string $order Ascending or descending order.
 *
 * @return array
 */
function masvideos_videos_array_orderby( $videos, $orderby = 'date', $order = 'desc' ) {
    $orderby = strtolower( $orderby );
    $order   = strtolower( $order );
    switch ( $orderby ) {
        case 'title':
        case 'id':
        case 'date':
        case 'modified':
        case 'menu_order':
            usort( $videos, 'masvideos_videos_array_orderby_' . $orderby );
            break;
        default:
            shuffle( $videos );
            break;
    }
    if ( 'desc' === $order ) {
        $videos = array_reverse( $videos );
    }
    return $videos;
}

/**
 * Sort by title.
 *
 * @since  1.0.0
 * @param  MasVideos_Video $a First MasVideos_Video object.
 * @param  MasVideos_Video $b Second MasVideos_Video object.
 * @return int
 */
function masvideos_videos_array_orderby_title( $a, $b ) {
    return strcasecmp( $a->get_name(), $b->get_name() );
}

/**
 * Sort by id.
 *
 * @since  1.0.0
 * @param  MasVideos_Video $a First MasVideos_Video object.
 * @param  MasVideos_Video $b Second MasVideos_Video object.
 * @return int
 */
function masvideos_videos_array_orderby_id( $a, $b ) {
    if ( $a->get_id() === $b->get_id() ) {
        return 0;
    }
    return ( $a->get_id() < $b->get_id() ) ? -1 : 1;
}

/**
 * Sort by date.
 *
 * @since  1.0.0
 * @param  MasVideos_Video $a First MasVideos_Video object.
 * @param  MasVideos_Video $b Second MasVideos_Video object.
 * @return int
 */
function masvideos_videos_array_orderby_date( $a, $b ) {
    if ( $a->get_date_created() === $b->get_date_created() ) {
        return 0;
    }
    return ( $a->get_date_created() < $b->get_date_created() ) ? -1 : 1;
}

/**
 * Sort by modified.
 *
 * @since  1.0.0
 * @param  MasVideos_Video $a First MasVideos_Video object.
 * @param  MasVideos_Video $b Second MasVideos_Video object.
 * @return int
 */
function masvideos_videos_array_orderby_modified( $a, $b ) {
    if ( $a->get_date_modified() === $b->get_date_modified() ) {
        return 0;
    }
    return ( $a->get_date_modified() < $b->get_date_modified() ) ? -1 : 1;
}

/**
 * Sort by menu order.
 *
 * @since  1.0.0
 * @param  MasVideos_Video $a First MasVideos_Video object.
 * @param  MasVideos_Video $b Second MasVideos_Video object.
 * @return int
 */
function masvideos_videos_array_orderby_menu_order( $a, $b ) {
    if ( $a->get_menu_order() === $b->get_menu_order() ) {
        return 0;
    }
    return ( $a->get_menu_order() < $b->get_menu_order() ) ? -1 : 1;
}

/**
 * Get related videos based on video category and tags.
 *
 * @since  1.0.0
 * @param  int   $video_id  Video ID.
 * @param  int   $limit       Limit of results.
 * @param  array $exclude_ids Exclude IDs from the results.
 * @return array
 */
function masvideos_get_related_videos( $video_id, $limit = 5, $exclude_ids = array() ) {

    $video_id       = absint( $video_id );
    $limit          = $limit >= -1 ? $limit : 5;
    $exclude_ids    = array_merge( array( 0, $video_id ), $exclude_ids );
    $transient_name = 'masvideos_related_videos_' . $video_id;
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

        $cats_array = apply_filters( 'masvideos_video_related_posts_relate_by_category', true, $video_id ) ? apply_filters( 'masvideos_get_related_video_cat_terms', masvideos_get_term_ids( $video_id, 'video_cat' ), $video_id ) : array();
        $tags_array = apply_filters( 'masvideos_video_related_posts_relate_by_tag', true, $video_id ) ? apply_filters( 'masvideos_get_related_video_tag_terms', masvideos_get_term_ids( $video_id, 'video_tag' ), $video_id ) : array();

        // Don't bother if none are set, unless masvideos_video_related_posts_force_display is set to true in which case all videos are related.
        if ( empty( $cats_array ) && empty( $tags_array ) && ! apply_filters( 'masvideos_video_related_posts_force_display', false, $video_id ) ) {
            $related_posts = array();
        } else {
            $data_store    = MasVideos_Data_Store::load( 'video' );
            $related_posts = $data_store->get_related_videos( $cats_array, $tags_array, $exclude_ids, $limit + 10, $video_id );
        }

        if ( $transient ) {
            $transient[ $query_args ] = $related_posts;
        } else {
            $transient = array( $query_args => $related_posts );
        }

        set_transient( $transient_name, $transient, DAY_IN_SECONDS );
    }

    $related_posts = apply_filters(
        'masvideos_related_videos', $related_posts, $video_id, array(
            'limit'        => $limit,
            'excluded_ids' => $exclude_ids,
        )
    );

    shuffle( $related_posts );

    return array_slice( $related_posts, 0, $limit );
}

if ( ! function_exists ( 'masvideos_the_video' ) ) {
    function masvideos_the_video( $post = null ) {
        echo masvideos_get_the_video( $post );
    }
}

if ( ! function_exists ( 'masvideos_get_the_video' ) ) {
    function masvideos_get_the_video( $video = null ) {
        if ( is_null( $video ) && ! empty( $GLOBALS['video'] ) ) {
            // Video was null so pull from global.
            $video = $GLOBALS['video'];
        }

        if ( $video && ! is_a( $video, 'MasVideos_Video' ) ) {
            // Make sure we have a valid video, or set to false.
            $video = masvideos_get_video( $video );
        }

        $video_src = '';
        $video_choice = $video->get_video_choice();

        if ( $video_choice == 'video_file' ) {
            $video_src =  wp_get_attachment_url( $video->get_video_attachment_id() );
        } elseif ( $video_choice == 'video_embed' ) {
            $video_src = $video->get_video_embed_content();
        } elseif ( $video_choice == 'video_url' ) {
            $video_src = $video->get_video_url_link();
        }

        return apply_filters( 'the_content', $video_src );
    }
}

if ( ! function_exists( 'masvideos_get_video_thumbnail' ) ) {
    /**
     * Get the masvideos thumbnail, or the placeholder if not set.
     */
    function masvideos_get_video_thumbnail( $size = 'masvideos_video_medium' ) {
        global $video;

        $image_size = apply_filters( 'masvideos_video_archive_thumbnail_size', $size );

        $gallery_image_ids = $video ? $video->get_gallery_image_ids() : '';
        $gallery_image_srcs = array();

        if( ! empty( $gallery_image_ids ) ) {
            foreach ( $gallery_image_ids as $gallery_image_id ) {
                $gallery_image_srcs[] =  wp_get_attachment_image_url( $gallery_image_id, $image_size );
            }
        }

        return $video ? $video->get_image( $image_size , array( 'class' => 'video__poster--image', 'data-gallery-images' => json_encode( $gallery_image_srcs ) ) ) : '';
    }
}

/**
 * Filter to allow video_cat in the permalinks for videos.
 *
 * @param  string  $permalink The existing permalink URL.
 * @param  WP_Post $post WP_Post object.
 * @return string
 */
function masvideos_video_post_type_link( $permalink, $post ) {
    // Abort if post is not a video.
    if ( 'video' !== $post->post_type ) {
        return $permalink;
    }

    // Abort early if the placeholder rewrite tag isn't in the generated URL.
    if ( false === strpos( $permalink, '%' ) ) {
        return $permalink;
    }

    // Get the custom taxonomy terms in use by this post.
    $terms = get_the_terms( $post->ID, 'video_cat' );

    if ( ! empty( $terms ) ) {
        $terms           = wp_list_sort(
            $terms,
            array(
                'parent'  => 'DESC',
                'term_id' => 'ASC',
            )
        );
        $genre_object = apply_filters( 'masvideos_video_post_type_link_video_cat', $terms[0], $terms, $post );
        $video_cat     = $genre_object->slug;

        if ( $genre_object->parent ) {
            $ancestors = get_ancestors( $genre_object->term_id, 'video_cat' );
            foreach ( $ancestors as $ancestor ) {
                $ancestor_object = get_term( $ancestor, 'video_cat' );
                $video_cat     = $ancestor_object->slug . '/' . $video_cat;
            }
        }
    } else {
        // If no terms are assigned to this post, use a string instead (can't leave the placeholder there).
        $video_cat = _x( 'uncategorized', 'slug', 'masvideos' );
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
        '%video_cat%',
    );

    $replace = array(
        date_i18n( 'Y', strtotime( $post->post_date ) ),
        date_i18n( 'm', strtotime( $post->post_date ) ),
        date_i18n( 'd', strtotime( $post->post_date ) ),
        date_i18n( 'H', strtotime( $post->post_date ) ),
        date_i18n( 'i', strtotime( $post->post_date ) ),
        date_i18n( 's', strtotime( $post->post_date ) ),
        $post->ID,
        $video_cat,
        $video_cat,
    );

    $permalink = str_replace( $find, $replace, $permalink );

    return $permalink;
}
add_filter( 'post_type_link', 'masvideos_video_post_type_link', 10, 2 );