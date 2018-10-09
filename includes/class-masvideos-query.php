<?php
/**
 * Contains the query functions for Masvideos which alter the front-end post queries and loops
 *
 * @version 1.0.0
 * @package Masvideos\Classes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Mas_Videos_Video_Query Class.
 */
class Mas_Videos_Video_Query {

    /**
     * Reference to the main video query on the page.
     *
     * @var array
     */
    private static $video_query;

    /**
     * Stores chosen taxonomies.
     *
     * @var array
     */
    private static $_chosen_taxonomies;

    public function __construct() {
        if ( ! is_admin() ) {
            add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
        }
    }

    /**
     * Are we currently on the front page?
     *
     * @param WP_Query $q Query instance.
     * @return bool
     */
    private function is_showing_page_on_front( $q ) {
        return $q->is_home() && 'page' === get_option( 'show_on_front' );
    }

    /**
     * Is the front page a page we define?
     *
     * @param int $page_id Page ID.
     * @return bool
     */
    private function page_on_front_is( $page_id ) {
        return absint( get_option( 'page_on_front' ) ) === absint( $page_id );
    }

    public function pre_get_posts( $q ) {
        if ( ! $q->is_main_query() ){
            return;
        }

        if ( ! $q->is_post_type_archive( 'video' ) && ! $q->is_tax( get_object_taxonomies( 'video' ) ) ) {
            // Only apply to video categories, the video post archive, the videos page, and video taxonomies.
            return;
        }

        if ( ! is_feed() ) {
            $ordering = $this->get_catalog_ordering_args();
            $q->set( 'orderby', $ordering['orderby'] );
            $q->set( 'order', $ordering['order'] );

            if ( isset( $ordering['meta_key'] ) ) {
                $q->set( 'meta_key', $ordering['meta_key'] );
            }
        }

        // Query vars that affect posts shown.
        // $this->get_search_query( $q );
        // $q->set( 'meta_query', $this->get_meta_query( $q->get( 'meta_query' ), true ) );
        // $q->set( 'tax_query', $this->get_tax_query( $q->get( 'tax_query' ), true ) );
        // $q->set( 'date_query', $this->get_date_query( $q->get( 'date_query' ), true ) );
        $q->set( 'masvideos_video_query', 'video_query' );
        $q->set( 'posts_per_page', $this->get_posts_per_page( $q->get( 'posts_per_page' ), true ) );

        // Store reference to this query.
        self::$video_query = $q;
    }

    /**
     * Appends meta queries to an array.
     *
     * @param  array $meta_query Meta query.
     * @param  bool  $main_query If is main query.
     * @return array
     */
    public function get_search_query( $q ) {
        if ( ! empty( $_GET['s'] ) ) {
            global $job_manager_keyword;
            $job_manager_keyword = sanitize_text_field( $_GET['s'] );

            if ( ! empty( $job_manager_keyword ) ) {
                add_filter( 'posts_search', 'get_job_listings_keyword_search' );
            }
        }
    }

    /**
     * Appends meta queries to an array.
     *
     * @param  array $meta_query Meta query.
     * @param  bool  $main_query If is main query.
     * @return array
     */
    public function get_meta_query( $meta_query = array(), $main_query = false ) {
        if ( ! is_array( $meta_query ) ) {
            $meta_query = array();
        }
        $meta_query['search_location_filter'] = $this->search_location_filter_meta_query();
        return array_filter( apply_filters( 'masvideos_video_query_meta_query', $meta_query, $this ) );
    }

    /**
     * Appends tax queries to an array.
     *
     * @param  array $tax_query  Tax query.
     * @param  bool  $main_query If is main query.
     * @return array
     */
    public function get_tax_query( $tax_query = array(), $main_query = false ) {
        if ( ! is_array( $tax_query ) ) {
            $tax_query = array(
                'relation' => 'AND',
            );
        }

        // Layered nav filters on terms.
        if ( $main_query ) {
            foreach ( $this->get_layered_nav_chosen_taxonomies() as $taxonomy => $data ) {
                $tax_query[] = array(
                    'taxonomy'         => $taxonomy,
                    'field'            => 'slug',
                    'terms'            => $data['terms'],
                    'operator'         => 'and' === $data['query_type'] ? 'AND' : 'IN',
                    'include_children' => false,
                );
            }
        }

        // Filter by category.
        if ( ! empty( $_GET['search_category'] ) ) {
            $categories = is_array( $_GET['search_category'] ) ? $_GET['search_category'] : array_filter( array_map( 'trim', explode( ',', $_GET['search_category'] ) ) );
            $field      = is_numeric( $categories[0] ) ? 'term_id' : 'slug';
            $operator   = 'all' === get_option( 'job_manager_category_filter_type', 'all' ) && sizeof( $categories ) > 1 ? 'AND' : 'IN';
            $tax_query[] = array(
                'taxonomy'         => 'job_listing_category',
                'field'            => $field,
                'terms'            => array_values( $categories ),
                'include_children' => $operator !== 'AND' ,
                'operator'         => $operator
            );
        }

        return array_filter( apply_filters( 'masvideos_video_query_tax_query', $tax_query, $this ) );
    }

    /**
     * Appends date queries to an array.
     *
     * @param  array $date_query Date query.
     * @param  bool  $main_query If is main query.
     * @return array
     */
    public function get_date_query( $date_query = array(), $main_query = false ) {
        if ( ! is_array( $date_query ) ) {
            $date_query = array();
        }

        if ( ! empty( $_GET['posted_before'] ) ) {
            $posted_before  = masvideos_clean( wp_unslash( $_GET['posted_before'] ) );
            $posted_arr     = explode( '-', $posted_before );
            $date_query[] = array(
                'after' => implode( ' ', $posted_arr ) . ' ago'
            );
        }

        return array_filter( apply_filters( 'masvideos_video_query_date_query', $date_query, $this ) );
    }

    /**
     * Return posts_per_page value.
     *
     * @param  int   $per_page posts_per_page value.
     * @param  bool  $main_query If is main query.
     * @return int
     */
    public function get_posts_per_page( $per_page = 10, $main_query = false ) {
        if( $main_query ) {
            $per_page = 10;
        }

        return intval( apply_filters( 'masvideos_video_query_posts_per_page', $per_page ) );
    }

    /**
     * Return a meta query for filtering by location.
     *
     * @return array
     */
    private function search_location_filter_meta_query() {
        if ( ! empty( $_GET['search_location'] ) ) {
            $location_meta_keys = array( 'geolocation_formatted_address', '_job_location', 'geolocation_state_long' );
            $location_search    = array( 'relation' => 'OR' );
            foreach ( $location_meta_keys as $meta_key ) {
                $location_search[] = array(
                    'key'     => $meta_key,
                    'value'   => $_GET['search_location'],
                    'compare' => 'like'
                );
            }

            return $location_search;
        }

        return array();
    }

    /**
     * Returns an array of arguments for ordering videos based on the selected values.
     *
     * @param string $orderby Order by param.
     * @param string $order Order param.
     * @return array
     */
    public function get_catalog_ordering_args( $orderby = '', $order = '' ) {
        // Get ordering from query string unless defined.
        if ( ! $orderby ) {
            $orderby_value = isset( $_GET['orderby'] ) ? masvideos_clean( (string) wp_unslash( $_GET['orderby'] ) ) : masvideos_clean( get_query_var( 'orderby' ) ); // WPCS: sanitization ok, input var ok, CSRF ok.

            if ( ! $orderby_value ) {
                if ( is_search() ) {
                    $orderby_value = 'relevance';
                } else {
                    $orderby_value = apply_filters( 'masvideos_video_default_catalog_orderby', 'date' );
                }
            }

            // Get order + orderby args from string.
            $orderby_value = explode( '-', $orderby_value );
            $orderby       = esc_attr( $orderby_value[0] );
            $order         = ! empty( $orderby_value[1] ) ? $orderby_value[1] : $order;
        }

        $orderby = strtolower( $orderby );
        $order   = strtoupper( $order );
        $args    = array(
            'orderby'  => $orderby,
            'order'    => ( 'DESC' === $order ) ? 'DESC' : 'ASC',
            'meta_key' => '', // @codingStandardsIgnoreLine
        );

        switch ( $orderby ) {
            case 'menu_order':
                $args['orderby'] = 'menu_order title';
                break;
            case 'title':
                $args['orderby'] = 'title';
                $args['order']   = ( 'DESC' === $order ) ? 'DESC' : 'ASC';
                break;
            case 'relevance':
                $args['orderby'] = 'relevance';
                $args['order']   = 'DESC';
                break;
            case 'rand':
                $args['orderby'] = 'rand'; // @codingStandardsIgnoreLine
                break;
            case 'date':
                $args['orderby'] = 'date ID';
                $args['order']   = ( 'ASC' === $order ) ? 'ASC' : 'DESC';
                break;
        }

        return apply_filters( 'masvideos_video_get_catalog_ordering_args', $args );
    }

    /**
     * Get the main query which video queries ran against.
     *
     * @return array
     */
    public static function get_main_query() {
        return self::$video_query;
    }

    /**
     * Get the tax query which was used by the main query.
     *
     * @return array
     */
    public static function get_main_tax_query() {
        $tax_query = isset( self::$video_query->tax_query, self::$video_query->tax_query->queries ) ? self::$video_query->tax_query->queries : array();

        return $tax_query;
    }

    /**
     * Get the meta query which was used by the main query.
     *
     * @return array
     */
    public static function get_main_meta_query() {
        $args       = isset( self::$video_query->query_vars ) ? self::$video_query->query_vars : array();
        $meta_query = isset( $args['meta_query'] ) ? $args['meta_query'] : array();

        return $meta_query;
    }

    /**
     * Get the date query which was used by the main query.
     *
     * @return array
     */
    public static function get_main_date_query() {
        $date_query = isset( self::$video_query->date_query, self::$video_query->date_query->queries ) ? self::$video_query->date_query->queries : array();

        return $date_query;
    }

    /**
     * Based on WP_Query::parse_search
     */
    public static function get_main_search_query_sql() {
        global $wpdb;

        $args       = isset( self::$video_query->query_vars ) ? self::$video_query->query_vars : array();
        $search_terms = isset( $args['search_terms'] ) ? $args['search_terms'] : array();
        $sql          = array();

        foreach ( $search_terms as $term ) {
            // Terms prefixed with '-' should be excluded.
            $include = '-' !== substr( $term, 0, 1 );

            if ( $include ) {
                $like_op  = 'LIKE';
                $andor_op = 'OR';
            } else {
                $like_op  = 'NOT LIKE';
                $andor_op = 'AND';
                $term     = substr( $term, 1 );
            }

            $like  = '%' . $wpdb->esc_like( $term ) . '%';
            $sql[] = $wpdb->prepare( "(($wpdb->posts.post_title $like_op %s) $andor_op ($wpdb->posts.post_excerpt $like_op %s) $andor_op ($wpdb->posts.post_content $like_op %s))", $like, $like, $like ); // unprepared SQL ok.
        }

        if ( ! empty( $sql ) && ! is_user_logged_in() ) {
            $sql[] = "($wpdb->posts.post_password = '')";
        }

        return implode( ' AND ', $sql );
    }

    /**
     * Get an array of taxonomies and terms selected with the layered nav widget.
     *
     * @return array
     */
    public static function get_layered_nav_chosen_taxonomies() {
        if ( ! is_array( self::$_chosen_taxonomies ) ) {
            self::$_chosen_taxonomies = array();
            $taxonomies     = jh_wpjm_get_all_taxonomies();

            if ( ! empty( $taxonomies ) ) {
                foreach ( $taxonomies as $tax ) {
                    $taxonomy = $tax['taxonomy'];
                    $filter_terms = ! empty( $_GET[ 'filter_' . $taxonomy ] ) ? explode( ',', masvideos_clean( wp_unslash( $_GET[ 'filter_' . $taxonomy ] ) ) ) : array(); // WPCS: sanitization ok, input var ok, CSRF ok.

                    if ( empty( $filter_terms ) || ! taxonomy_exists( $taxonomy ) ) {
                        continue;
                    }

                    $query_type                                     = ! empty( $_GET[ 'query_type_' . $taxonomy ] ) && in_array( $_GET[ 'query_type_' . $taxonomy ], array( 'and', 'or' ), true ) ? masvideos_clean( wp_unslash( $_GET[ 'query_type_' . $taxonomy ] ) ) : ''; // WPCS: sanitization ok, input var ok, CSRF ok.
                    self::$_chosen_taxonomies[ $taxonomy ]['terms'] = array_map( 'sanitize_title', $filter_terms ); // Ensures correct encoding.
                    self::$_chosen_taxonomies[ $taxonomy ]['query_type'] = $query_type ? $query_type : apply_filters( 'masvideos_video_layered_nav_default_query_type', 'and' );
                }
            }
        }
        return self::$_chosen_taxonomies;
    }
}


/**
 * Mas_Videos_Movie_Query Class.
 */
class Mas_Videos_Movie_Query {

    /**
     * Reference to the main movie query on the page.
     *
     * @var array
     */
    private static $movie_query;

    /**
     * Stores chosen taxonomies.
     *
     * @var array
     */
    private static $_chosen_taxonomies;

    public function __construct() {
        if ( ! is_admin() ) {
            add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
        }
    }

    /**
     * Are we currently on the front page?
     *
     * @param WP_Query $q Query instance.
     * @return bool
     */
    private function is_showing_page_on_front( $q ) {
        return $q->is_home() && 'page' === get_option( 'show_on_front' );
    }

    /**
     * Is the front page a page we define?
     *
     * @param int $page_id Page ID.
     * @return bool
     */
    private function page_on_front_is( $page_id ) {
        return absint( get_option( 'page_on_front' ) ) === absint( $page_id );
    }

    public function pre_get_posts( $q ) {
        if ( ! $q->is_main_query() ){
            return;
        }

        if ( ! $q->is_post_type_archive( 'movie' ) && ! $q->is_tax( get_object_taxonomies( 'movie' ) ) ) {
            // Only apply to movie categories, the movie post archive, the movies page, and movie taxonomies.
            return;
        }

        if ( ! is_feed() ) {
            $ordering = $this->get_catalog_ordering_args();
            $q->set( 'orderby', $ordering['orderby'] );
            $q->set( 'order', $ordering['order'] );

            if ( isset( $ordering['meta_key'] ) ) {
                $q->set( 'meta_key', $ordering['meta_key'] );
            }
        }

        // Query vars that affect posts shown.
        // $this->get_search_query( $q );
        // $q->set( 'meta_query', $this->get_meta_query( $q->get( 'meta_query' ), true ) );
        // $q->set( 'tax_query', $this->get_tax_query( $q->get( 'tax_query' ), true ) );
        // $q->set( 'date_query', $this->get_date_query( $q->get( 'date_query' ), true ) );
        $q->set( 'masvideos_movie_query', 'movie_query' );
        $q->set( 'posts_per_page', $this->get_posts_per_page( $q->get( 'posts_per_page' ), true ) );

        // Store reference to this query.
        self::$movie_query = $q;
    }

    /**
     * Appends meta queries to an array.
     *
     * @param  array $meta_query Meta query.
     * @param  bool  $main_query If is main query.
     * @return array
     */
    public function get_search_query( $q ) {
        if ( ! empty( $_GET['s'] ) ) {
            global $job_manager_keyword;
            $job_manager_keyword = sanitize_text_field( $_GET['s'] );

            if ( ! empty( $job_manager_keyword ) ) {
                add_filter( 'posts_search', 'get_job_listings_keyword_search' );
            }
        }
    }

    /**
     * Appends meta queries to an array.
     *
     * @param  array $meta_query Meta query.
     * @param  bool  $main_query If is main query.
     * @return array
     */
    public function get_meta_query( $meta_query = array(), $main_query = false ) {
        if ( ! is_array( $meta_query ) ) {
            $meta_query = array();
        }
        $meta_query['search_location_filter'] = $this->search_location_filter_meta_query();
        return array_filter( apply_filters( 'masvideos_video_query_meta_query', $meta_query, $this ) );
    }

    /**
     * Appends tax queries to an array.
     *
     * @param  array $tax_query  Tax query.
     * @param  bool  $main_query If is main query.
     * @return array
     */
    public function get_tax_query( $tax_query = array(), $main_query = false ) {
        if ( ! is_array( $tax_query ) ) {
            $tax_query = array(
                'relation' => 'AND',
            );
        }

        // Layered nav filters on terms.
        if ( $main_query ) {
            foreach ( $this->get_layered_nav_chosen_taxonomies() as $taxonomy => $data ) {
                $tax_query[] = array(
                    'taxonomy'         => $taxonomy,
                    'field'            => 'slug',
                    'terms'            => $data['terms'],
                    'operator'         => 'and' === $data['query_type'] ? 'AND' : 'IN',
                    'include_children' => false,
                );
            }
        }

        // Filter by category.
        if ( ! empty( $_GET['search_category'] ) ) {
            $categories = is_array( $_GET['search_category'] ) ? $_GET['search_category'] : array_filter( array_map( 'trim', explode( ',', $_GET['search_category'] ) ) );
            $field      = is_numeric( $categories[0] ) ? 'term_id' : 'slug';
            $operator   = 'all' === get_option( 'job_manager_category_filter_type', 'all' ) && sizeof( $categories ) > 1 ? 'AND' : 'IN';
            $tax_query[] = array(
                'taxonomy'         => 'job_listing_category',
                'field'            => $field,
                'terms'            => array_values( $categories ),
                'include_children' => $operator !== 'AND' ,
                'operator'         => $operator
            );
        }

        return array_filter( apply_filters( 'masvideos_video_query_tax_query', $tax_query, $this ) );
    }

    /**
     * Appends date queries to an array.
     *
     * @param  array $date_query Date query.
     * @param  bool  $main_query If is main query.
     * @return array
     */
    public function get_date_query( $date_query = array(), $main_query = false ) {
        if ( ! is_array( $date_query ) ) {
            $date_query = array();
        }

        if ( ! empty( $_GET['posted_before'] ) ) {
            $posted_before  = masvideos_clean( wp_unslash( $_GET['posted_before'] ) );
            $posted_arr     = explode( '-', $posted_before );
            $date_query[] = array(
                'after' => implode( ' ', $posted_arr ) . ' ago'
            );
        }

        return array_filter( apply_filters( 'masvideos_video_query_date_query', $date_query, $this ) );
    }

    /**
     * Return posts_per_page value.
     *
     * @param  int   $per_page posts_per_page value.
     * @param  bool  $main_query If is main query.
     * @return int
     */
    public function get_posts_per_page( $per_page = 10, $main_query = false ) {
        if( $main_query ) {
            $per_page = 10;
        }

        return intval( apply_filters( 'masvideos_video_query_posts_per_page', $per_page ) );
    }

    /**
     * Return a meta query for filtering by location.
     *
     * @return array
     */
    private function search_location_filter_meta_query() {
        if ( ! empty( $_GET['search_location'] ) ) {
            $location_meta_keys = array( 'geolocation_formatted_address', '_job_location', 'geolocation_state_long' );
            $location_search    = array( 'relation' => 'OR' );
            foreach ( $location_meta_keys as $meta_key ) {
                $location_search[] = array(
                    'key'     => $meta_key,
                    'value'   => $_GET['search_location'],
                    'compare' => 'like'
                );
            }

            return $location_search;
        }

        return array();
    }

    /**
     * Returns an array of arguments for ordering movies based on the selected values.
     *
     * @param string $orderby Order by param.
     * @param string $order Order param.
     * @return array
     */
    public function get_catalog_ordering_args( $orderby = '', $order = '' ) {
        // Get ordering from query string unless defined.
        if ( ! $orderby ) {
            $orderby_value = isset( $_GET['orderby'] ) ? masvideos_clean( (string) wp_unslash( $_GET['orderby'] ) ) : masvideos_clean( get_query_var( 'orderby' ) ); // WPCS: sanitization ok, input var ok, CSRF ok.

            if ( ! $orderby_value ) {
                if ( is_search() ) {
                    $orderby_value = 'relevance';
                } else {
                    $orderby_value = apply_filters( 'masvideos_movie_default_catalog_orderby', 'date' );
                }
            }

            // Get order + orderby args from string.
            $orderby_value = explode( '-', $orderby_value );
            $orderby       = esc_attr( $orderby_value[0] );
            $order         = ! empty( $orderby_value[1] ) ? $orderby_value[1] : $order;
        }

        $orderby = strtolower( $orderby );
        $order   = strtoupper( $order );
        $args    = array(
            'orderby'  => $orderby,
            'order'    => ( 'DESC' === $order ) ? 'DESC' : 'ASC',
            'meta_key' => '', // @codingStandardsIgnoreLine
        );

        switch ( $orderby ) {
            case 'menu_order':
                $args['orderby'] = 'menu_order title';
                break;
            case 'title':
                $args['orderby'] = 'title';
                $args['order']   = ( 'DESC' === $order ) ? 'DESC' : 'ASC';
                break;
            case 'relevance':
                $args['orderby'] = 'relevance';
                $args['order']   = 'DESC';
                break;
            case 'rand':
                $args['orderby'] = 'rand'; // @codingStandardsIgnoreLine
                break;
            case 'date':
                $args['orderby'] = 'date ID';
                $args['order']   = ( 'ASC' === $order ) ? 'ASC' : 'DESC';
                break;
        }

        return apply_filters( 'masvideos_movie_get_catalog_ordering_args', $args );
    }

    /**
     * Get the main query which movie queries ran against.
     *
     * @return array
     */
    public static function get_main_query() {
        return self::$movie_query;
    }

    /**
     * Get the tax query which was used by the main query.
     *
     * @return array
     */
    public static function get_main_tax_query() {
        $tax_query = isset( self::$movie_query->tax_query, self::$movie_query->tax_query->queries ) ? self::$movie_query->tax_query->queries : array();

        return $tax_query;
    }

    /**
     * Get the meta query which was used by the main query.
     *
     * @return array
     */
    public static function get_main_meta_query() {
        $args       = isset( self::$movie_query->query_vars ) ? self::$movie_query->query_vars : array();
        $meta_query = isset( $args['meta_query'] ) ? $args['meta_query'] : array();

        return $meta_query;
    }

    /**
     * Get the date query which was used by the main query.
     *
     * @return array
     */
    public static function get_main_date_query() {
        $date_query = isset( self::$movie_query->date_query, self::$movie_query->date_query->queries ) ? self::$movie_query->date_query->queries : array();

        return $date_query;
    }

    /**
     * Based on WP_Query::parse_search
     */
    public static function get_main_search_query_sql() {
        global $wpdb;

        $args       = isset( self::$movie_query->query_vars ) ? self::$movie_query->query_vars : array();
        $search_terms = isset( $args['search_terms'] ) ? $args['search_terms'] : array();
        $sql          = array();

        foreach ( $search_terms as $term ) {
            // Terms prefixed with '-' should be excluded.
            $include = '-' !== substr( $term, 0, 1 );

            if ( $include ) {
                $like_op  = 'LIKE';
                $andor_op = 'OR';
            } else {
                $like_op  = 'NOT LIKE';
                $andor_op = 'AND';
                $term     = substr( $term, 1 );
            }

            $like  = '%' . $wpdb->esc_like( $term ) . '%';
            $sql[] = $wpdb->prepare( "(($wpdb->posts.post_title $like_op %s) $andor_op ($wpdb->posts.post_excerpt $like_op %s) $andor_op ($wpdb->posts.post_content $like_op %s))", $like, $like, $like ); // unprepared SQL ok.
        }

        if ( ! empty( $sql ) && ! is_user_logged_in() ) {
            $sql[] = "($wpdb->posts.post_password = '')";
        }

        return implode( ' AND ', $sql );
    }

    /**
     * Get an array of taxonomies and terms selected with the layered nav widget.
     *
     * @return array
     */
    public static function get_layered_nav_chosen_taxonomies() {
        if ( ! is_array( self::$_chosen_taxonomies ) ) {
            self::$_chosen_taxonomies = array();
            $taxonomies     = jh_wpjm_get_all_taxonomies();

            if ( ! empty( $taxonomies ) ) {
                foreach ( $taxonomies as $tax ) {
                    $taxonomy = $tax['taxonomy'];
                    $filter_terms = ! empty( $_GET[ 'filter_' . $taxonomy ] ) ? explode( ',', masvideos_clean( wp_unslash( $_GET[ 'filter_' . $taxonomy ] ) ) ) : array(); // WPCS: sanitization ok, input var ok, CSRF ok.

                    if ( empty( $filter_terms ) || ! taxonomy_exists( $taxonomy ) ) {
                        continue;
                    }

                    $query_type                                     = ! empty( $_GET[ 'query_type_' . $taxonomy ] ) && in_array( $_GET[ 'query_type_' . $taxonomy ], array( 'and', 'or' ), true ) ? masvideos_clean( wp_unslash( $_GET[ 'query_type_' . $taxonomy ] ) ) : ''; // WPCS: sanitization ok, input var ok, CSRF ok.
                    self::$_chosen_taxonomies[ $taxonomy ]['terms'] = array_map( 'sanitize_title', $filter_terms ); // Ensures correct encoding.
                    self::$_chosen_taxonomies[ $taxonomy ]['query_type'] = $query_type ? $query_type : apply_filters( 'masvideos_movie_layered_nav_default_query_type', 'and' );
                }
            }
        }
        return self::$_chosen_taxonomies;
    }
}