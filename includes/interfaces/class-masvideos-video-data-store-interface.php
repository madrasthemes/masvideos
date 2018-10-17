<?php
/**
 * Video Data Store Interface
 *
 * @version 1.0.0
 * @package MasVideos/Interface
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * MasVideos Video Data Store Interface
 *
 * Functions that must be defined by video store classes.
 *
 * @version  1.0.0
 */
interface MasVideos_Video_Data_Store_Interface {

    /**
     * Returns a list of video IDs ( id as key => parent as value) that are
     * featured. Uses get_posts instead of masvideos_get_videos since we want
     * some extra meta queries and ALL videos (posts_per_page = -1).
     *
     * @return array
     */
    public function get_featured_video_ids();

    /**
     * Return a list of related videos (using data like categories and IDs).
     *
     * @param array $cats_array List of categories IDs.
     * @param array $tags_array List of tags IDs.
     * @param array $exclude_ids Excluded IDs.
     * @param int   $limit Limit of results.
     * @param int   $video_id Video ID.
     * @return array
     */
    public function get_related_videos( $cats_array, $tags_array, $exclude_ids, $limit, $video_id );

    /**
     * Returns an array of videos.
     *
     * @param array $args @see masvideos_get_videos.
     * @return array
     */
    public function get_videos( $args = array() );
}
