<?php
/**
 * Class for parameter-based Movie querying
 *
 * @package  MasVideos/Classes
 * @version  1.0.0
 * @since    1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Movie query class.
 */
class MasVideos_Movie_Query extends MasVideos_Object_Query {

    /**
     * Valid query vars for movies.
     *
     * @return array
     */
    protected function get_default_query_vars() {
        return array_merge(
            parent::get_default_query_vars(),
            array(
                'status'            => array( 'draft', 'pending', 'private', 'publish' ),
                'type'              => array( 'movie' ),
                'limit'             => get_option( 'posts_per_page' ),
                'include'           => array(),
                'date_created'      => '',
                'date_modified'     => '',
                'featured'          => '',
                'visibility'        => '',
                'reviews_allowed'   => '',
                'category'          => array(),
                'tag'               => array(),
                'average_rating'    => '',
                'review_count'      => '',
            )
        );
    }

    /**
     * Get movies matching the current query vars.
     *
     * @return array|object of MasVideos_Movie objects
     */
    public function get_movies() {
        $args    = apply_filters( 'masvideos_movie_object_query_args', $this->get_query_vars() );
        $results = MasVideos_Data_Store::load( 'movie' )->query( $args );
        return apply_filters( 'masvideos_movie_object_query', $results, $args );
    }
}
