<?php
/**
 * Person Data Store Interface
 *
 * @version 1.0.0
 * @package MasVideos/Interface
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * MasVideos Person Data Store Interface
 *
 * Functions that must be defined by person store classes.
 *
 * @version  1.0.0
 */
interface MasVideos_Person_Data_Store_Interface {

    /**
     * Returns a list of person IDs ( id as key => parent as value) that are
     * featured. Uses get_posts instead of masvideos_get_persons since we want
     * some extra meta queries and ALL persons (posts_per_page = -1).
     *
     * @return array
     */
    public function get_featured_person_ids();

    /**
     * Return a list of related persons (using data like categories and IDs).
     *
     * @param array $cats_array List of categories IDs.
     * @param array $tags_array List of tags IDs.
     * @param array $exclude_ids Excluded IDs.
     * @param int   $limit Limit of results.
     * @param int   $person_id Person ID.
     * @return array
     */
    public function get_related_persons( $cats_array, $tags_array, $exclude_ids, $limit, $person_id );

    /**
     * Returns an array of persons.
     *
     * @param array $args @see masvideos_get_persons.
     * @return array
     */
    public function get_persons( $args = array() );
}
