<?php
/**
 * Class for parameter-based TV Show querying
 *
 * @package  MasVideos/Classes
 * @version  1.0.0
 * @since    1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * TV Show query class.
 */
class MasVideos_TV_Show_Query extends MasVideos_Object_Query {

    /**
     * Valid query vars for tv shows.
     *
     * @return array
     */
    protected function get_default_query_vars() {
        return array_merge(
            parent::get_default_query_vars(),
            array(
                'status'            => array( 'draft', 'pending', 'private', 'publish' ),
                'type'              => array( 'tv_show' ),
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
     * Get tv shows matching the current query vars.
     *
     * @return array|object of MasVideos_TV_Show objects
     */
    public function get_tv_shows() {
        $args    = apply_filters( 'masvideos_tv_show_object_query_args', $this->get_query_vars() );
        $results = MasVideos_Data_Store::load( 'tv_show' )->query( $args );
        return apply_filters( 'masvideos_tv_show_object_query', $results, $args );
    }
}
