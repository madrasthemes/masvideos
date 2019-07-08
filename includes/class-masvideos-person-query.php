<?php
/**
 * Class for parameter-based Person querying
 *
 * @package  MasVideos/Classes
 * @version  1.0.0
 * @since    1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Person query class.
 */
class MasVideos_Person_Query extends MasVideos_Object_Query {

    /**
     * Valid query vars for persons.
     *
     * @return array
     */
    protected function get_default_query_vars() {
        return array_merge(
            parent::get_default_query_vars(),
            array(
                'status'            => array( 'draft', 'pending', 'private', 'publish' ),
                'type'              => array( 'person' ),
                'limit'             => get_option( 'posts_per_page' ),
                'include'           => array(),
                'date_created'      => '',
                'date_modified'     => '',
                'featured'          => '',
                'visibility'        => '',
                'category'          => array(),
                'tag'               => array(),
            )
        );
    }

    /**
     * Get persons matching the current query vars.
     *
     * @return array|object of MasVideos_Person objects
     */
    public function get_persons() {
        $args    = apply_filters( 'masvideos_person_object_query_args', $this->get_query_vars() );
        $results = MasVideos_Data_Store::load( 'person' )->query( $args );
        return apply_filters( 'masvideos_person_object_query', $results, $args );
    }
}
