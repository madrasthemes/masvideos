<?php
/**
 * TV Show Data Store Interface
 *
 * @version 1.0.0
 * @package MasVideos/Interface
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * MasVideos TV Show Data Store Interface
 *
 * Functions that must be defined by tv show store classes.
 *
 * @version  1.0.0
 */
interface MasVideos_TV_Show_Data_Store_Interface {

    /**
     * Returns a list of tv show IDs ( id as key => parent as value) that are
     * featured. Uses get_posts instead of masvideos_get_tv_show since we want
     * some extra meta queries and ALL tv shows (posts_per_page = -1).
     *
     * @return array
     */
    public function get_featured_tv_show_ids();

    /**
     * Return a list of related tv shows (using data like categories and IDs).
     *
     * @param array $cats_array List of categories IDs.
     * @param array $tags_array List of tags IDs.
     * @param array $exclude_ids Excluded IDs.
     * @param int   $limit Limit of results.
     * @param int   $tv_show_id TV Show ID.
     * @return array
     */
    public function get_related_tv_shows( $cats_array, $tags_array, $exclude_ids, $limit, $tv_show_id );

    /**
     * Returns an array of tv shows.
     *
     * @param array $args @see masvideos_get_tv_show.
     * @return array
     */
    public function get_tv_shows( $args = array() );
}
