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
        'masvideos_videos_onsale',
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

if ( ! function_exists ( 'masvideos_the_video' ) ) {
    function masvideos_the_video( $video = null ) {
        global $video;

        $video_src = masvideos_get_the_video( $video );
        $video_choice = $video->get_video_choice();

        if ( ! empty ( $video_src ) ) {
            if ( $video_choice == 'video_file' ) {
                echo do_shortcode('[video src="' . $video_src . '"]');
            } elseif ( $video_choice == 'video_embed' ) {
                echo '<div class="wp-video">' . $video_src . '</div>';
            } elseif ( $video_choice == 'video_url' ) {
                echo do_shortcode('[video src="' . $video_src . '"]');
            }
        }
    }
}

if ( ! function_exists ( 'masvideos_get_the_video' ) ) {
    function masvideos_get_the_video( $vieo = null ) {
        global $video;

        $video_src = '';
        $video_choice = $video->get_video_choice();

        if ( $video_choice == 'video_file' ) {
            $video_src =  wp_get_attachment_url( $video->get_video_attachment_id() );
        } elseif ( $video_choice == 'video_embed' ) {
            $video_src = $video->get_video_embed_content();
        } elseif ( $video_choice == 'video_url' ) {
            $video_src = $video->get_video_url_link();
        }

        return $video_src;
    }
}

if ( ! function_exists( 'masvideos_get_video_thumbnail' ) ) {
    /**
     * Get the masvideos thumbnail, or the placeholder if not set.
     */
    function masvideos_get_video_thumbnail( $size = 'masvideos_thumbnail' ) {
        global $video;

        $image_size = apply_filters( 'masvideos_video_archive_thumbnail_size', $size );

        return $video ? $video->get_image( $image_size , array( 'class' => 'video__poster--image' ) ) : '';
    }
}