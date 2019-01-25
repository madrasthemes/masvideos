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
