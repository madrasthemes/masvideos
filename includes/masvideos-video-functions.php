<?php
/**
 * Masvideos Video Functions
 *
 * Functions for video specific things.
 *
 * @package Masvideos/Functions
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

    $query = new Mas_Videos_Video_Query( $args ); // WC_Product_Query
    return $query->get_videos();
}

/**
 * Main function for returning videos, uses the Mas_Videos_Video_Factory class.
 *
 * @since 1.0.0
 *
 * @param mixed $the_video Post object or post ID of the video.
 * @return Mas_Videos_Video|null|false
 */
function masvideos_get_video( $the_video = false ) {
    if ( ! did_action( 'masvideos_init' ) ) {
        /* translators: 1: masvideos_get_video 2: masvideos_init */
        _doing_it_wrong( __FUNCTION__, sprintf( __( '%1$s should not be called before the %2$s action.', 'masvideos' ), 'masvideos_get_video', 'masvideos_init' ), '1.0.0' );
        return false;
    }
    return Mas_Videos()->video_factory->get_video( $the_video, $deprecated );
}
