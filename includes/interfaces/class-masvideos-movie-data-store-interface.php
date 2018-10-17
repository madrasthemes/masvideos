<?php
/**
 * Movie Data Store Interface
 *
 * @version 1.0.0
 * @package MasVideos/Interface
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * MasVideos Movie Data Store Interface
 *
 * Functions that must be defined by movie store classes.
 *
 * @version  1.0.0
 */
interface MasVideos_Movie_Data_Store_Interface {

    /**
     * Returns a list of movie IDs ( id as key => parent as value) that are
     * featured. Uses get_posts instead of masvideos_get_movies since we want
     * some extra meta queries and ALL movies (posts_per_page = -1).
     *
     * @return array
     */
    public function get_featured_movie_ids();

    /**
     * Return a list of related movies (using data like categories and IDs).
     *
     * @param array $cats_array List of categories IDs.
     * @param array $tags_array List of tags IDs.
     * @param array $exclude_ids Excluded IDs.
     * @param int   $limit Limit of results.
     * @param int   $movie_id Movie ID.
     * @return array
     */
    public function get_related_movies( $cats_array, $tags_array, $exclude_ids, $limit, $movie_id );

    /**
     * Returns an array of movies.
     *
     * @param array $args @see masvideos_get_movies.
     * @return array
     */
    public function get_movies( $args = array() );
}
