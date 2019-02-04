<?php
/**
 * Episode Data Store Interface
 *
 * @version 1.0.0
 * @package MasVideos/Interface
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * MasVideos Episode Data Store Interface
 *
 * Functions that must be defined by episode store classes.
 *
 * @version  1.0.0
 */
interface MasVideos_Episode_Data_Store_Interface {

    /**
     * Returns a list of episode IDs ( id as key => parent as value) that are
     * featured. Uses get_posts instead of masvideos_get_episodes since we want
     * some extra meta queries and ALL episodes (posts_per_page = -1).
     *
     * @return array
     */
    public function get_featured_episode_ids();

    /**
     * Return a list of related episodes (using data like categories and IDs).
     *
     * @param array $cats_array List of categories IDs.
     * @param array $tags_array List of tags IDs.
     * @param array $exclude_ids Excluded IDs.
     * @param int   $limit Limit of results.
     * @param int   $episode_id Episode ID.
     * @return array
     */
    public function get_related_episodes( $cats_array, $tags_array, $exclude_ids, $limit, $episode_id );

    /**
     * Returns an array of episodes.
     *
     * @param array $args @see masvideos_get_episodes.
     * @return array
     */
    public function get_episodes( $args = array() );
}
