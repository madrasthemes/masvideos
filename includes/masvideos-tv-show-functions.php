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


if ( ! function_exists( 'masvideos_get_tv_show_thumbnail' ) ) {
    /**
     * Get the masvideos thumbnail, or the placeholder if not set.
     */
    function masvideos_get_tv_show_thumbnail( $size = 'masvideos_tv_show_medium' ) {
        global $tv_show;

        $image_size = apply_filters( 'masvideos_tv_show_archive_thumbnail_size', $size );
        return $tv_show ? $tv_show->get_image( $image_size , array( 'class' => 'tv_show__poster--image' ) ) : '';
        
    }
}

if ( ! function_exists( 'masvideos_get_tv_show_all_episodes' ) ) {
    function masvideos_get_tv_show_all_episodes() {
        global $tv_show;

        $episodes = array();

        $seasons = $tv_show->get_seasons();
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
