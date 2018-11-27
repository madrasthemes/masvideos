<?php
/**
 * Contains the query functions for MasVideos which alter the front-end post queries and loops
 *
 * @version 1.0.0
 * @package MasVideos\Classes
 */

defined( 'ABSPATH' ) || exit;

/**
 * MasVideos_Videos_Query Class.
 */
class MasVideos_Videos_Query {

    /**
     * Reference to the main video query on the page.
     *
     * @var array
     */
    private static $video_query;

    /**
     * Chosen attributes.
     *
     * @var array
     */
    private static $_chosen_attributes;

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

    /**
     * Hook into pre_get_posts to do the main video query.
     *
     * @param WP_Query $q Query instance.
     */
    public function pre_get_posts( $q ) {
        // We only want to affect the main query.
        if ( ! $q->is_main_query() ) {
            return;
        }

        // When orderby is set, WordPress shows posts on the front-page. Get around that here.
        if ( $this->is_showing_page_on_front( $q ) && $this->page_on_front_is( masvideos_get_page_id( 'videos' ) ) ) {
            $_query = wp_parse_args( $q->query );
            if ( empty( $_query ) || ! array_diff( array_keys( $_query ), array( 'preview', 'page', 'paged', 'cpage', 'orderby' ) ) ) {
                $q->set( 'page_id', (int) get_option( 'page_on_front' ) );
                $q->is_page = true;
                $q->is_home = false;

                // WP supporting themes show post type archive.
                if ( current_theme_supports( 'masvideos' ) ) {
                    $q->set( 'post_type', 'video' );
                } else {
                    $q->is_singular = true;
                }
            }
        }

        // Fix video feeds.
        if ( $q->is_feed() && $q->is_post_type_archive( 'video' ) ) {
            $q->is_comment_feed = false;
        }

        // Special check for videos with the PRODUCT POST TYPE ARCHIVE on front.
        if ( current_theme_supports( 'masvideos' ) && $q->is_page() && 'page' === get_option( 'show_on_front' ) && absint( $q->get( 'page_id' ) ) === masvideos_get_page_id( 'videos' ) ) {
            // This is a front-page videos.
            $q->set( 'post_type', 'video' );
            $q->set( 'page_id', '' );

            if ( isset( $q->query['paged'] ) ) {
                $q->set( 'paged', $q->query['paged'] );
            }

            // Define a variable so we know this is the front page videos later on.
            masvideos_maybe_define_constant( 'VIDEOS_ON_FRONT', true );

            // Get the actual WP page to avoid errors and let us use is_front_page().
            // This is hacky but works. Awaiting https://core.trac.wordpress.org/ticket/21096.
            global $wp_post_types;

            $videos_page = get_post( masvideos_get_page_id( 'videos' ) );

            $wp_post_types['video']->ID         = $videos_page->ID;
            $wp_post_types['video']->post_title = $videos_page->post_title;
            $wp_post_types['video']->post_name  = $videos_page->post_name;
            $wp_post_types['video']->post_type  = $videos_page->post_type;
            $wp_post_types['video']->ancestors  = get_ancestors( $videos_page->ID, $videos_page->post_type );

            // Fix conditional Functions like is_front_page.
            $q->is_singular          = false;
            $q->is_post_type_archive = true;
            $q->is_archive           = true;
            $q->is_page              = true;

            // Remove post type archive name from front page title tag.
            add_filter( 'post_type_archive_title', '__return_empty_string', 5 );

            // Fix WP SEO.
            if ( class_exists( 'WPSEO_Meta' ) ) {
                add_filter( 'wpseo_metadesc', array( $this, 'wpseo_metadesc' ) );
                add_filter( 'wpseo_metakey', array( $this, 'wpseo_metakey' ) );
            }
        } elseif ( ! $q->is_post_type_archive( 'video' ) && ! $q->is_tax( get_object_taxonomies( 'video' ) ) ) {
            // Only apply to video categories, the video post archive, the videos page, video tags, and video attribute taxonomies.
            return;
        }

        $this->video_query( $q );
    }

    /**
     * WP SEO meta description.
     *
     * Hooked into wpseo_ hook already, so no need for function_exist.
     *
     * @return string
     */
    public function wpseo_metadesc() {
        return WPSEO_Meta::get_value( 'metadesc', masvideos_get_page_id( 'videos' ) );
    }

    /**
     * WP SEO meta key.
     *
     * Hooked into wpseo_ hook already, so no need for function_exist.
     *
     * @return string
     */
    public function wpseo_metakey() {
        return WPSEO_Meta::get_value( 'metakey', masvideos_get_page_id( 'videos' ) );
    }

    /**
     * Query the videos, applying sorting/ordering etc.
     * This applies to the main WordPress loop.
     *
     * @param WP_Query $q Query instance.
     */
    public function video_query( $q ) {
        if ( ! is_feed() ) {
            $ordering = $this->get_catalog_ordering_args();
            $q->set( 'orderby', $ordering['orderby'] );
            $q->set( 'order', $ordering['order'] );

            if ( isset( $ordering['meta_key'] ) ) {
                $q->set( 'meta_key', $ordering['meta_key'] );
            }
        }

        // Query vars that affect posts shown.
        $q->set( 'meta_query', $this->get_meta_query( $q->get( 'meta_query' ), true ) );
        $q->set( 'tax_query', $this->get_tax_query( $q->get( 'tax_query' ), true ) );
        $q->set( 'masvideos_video_query', 'video_query' );
        $q->set( 'post__in', array_unique( (array) apply_filters( 'loop_videos_post_in', array() ) ) );

        // Work out how many videos to query.
        $q->set( 'posts_per_page', $q->get( 'posts_per_page' ) ? $q->get( 'posts_per_page' ) : apply_filters( 'masvideos_video_query_posts_per_page', 10 ) );

        // Store reference to this query.
        self::$video_query = $q;

        do_action( 'masvideos_video_query', $q, $this );
    }

    /**
     * Remove the query.
     */
    public function remove_video_query() {
        remove_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
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
                    $orderby_value = apply_filters( 'masvideos_default_catalog_orderby', get_option( 'masvideos_default_catalog_orderby', 'menu_order' ) );
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
            case 'id':
                $args['orderby'] = 'ID';
                break;
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

        return apply_filters( 'masvideos_get_catalog_ordering_args', $args );
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
            foreach ( $this->get_layered_nav_chosen_attributes() as $taxonomy => $data ) {
                $tax_query[] = array(
                    'taxonomy'         => $taxonomy,
                    'field'            => 'slug',
                    'terms'            => $data['terms'],
                    'operator'         => 'and' === $data['query_type'] ? 'AND' : 'IN',
                    'include_children' => false,
                );
            }
        }

        return array_filter( apply_filters( 'masvideos_video_query_tax_query', $tax_query, $this ) );
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
     * Based on WP_Query::parse_search
     */
    public static function get_main_search_query_sql() {
        global $wpdb;

        $args         = isset( self::$video_query->query_vars ) ? self::$video_query->query_vars : array();
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
     * Get an array of attributes and terms selected with the layered nav widget.
     *
     * @return array
     */
    public static function get_layered_nav_chosen_attributes() {
        if ( ! is_array( self::$_chosen_attributes ) ) {
            self::$_chosen_attributes = array();

            if ( ! empty( $_GET ) ) { // WPCS: input var ok, CSRF ok.
                foreach ( $_GET as $key => $value ) { // WPCS: input var ok, CSRF ok.
                    if ( 0 === strpos( $key, 'filter_' ) ) {
                        $attribute    = masvideos_sanitize_taxonomy_name( str_replace( 'filter_', '', $key ) );
                        $taxonomy     = masvideos_attribute_taxonomy_name( 'video', $attribute );
                        $filter_terms = ! empty( $value ) ? explode( ',', masvideos_clean( wp_unslash( $value ) ) ) : array();

                        if ( empty( $filter_terms ) || ! taxonomy_exists( $taxonomy ) || ! masvideos_attribute_taxonomy_id_by_name( 'video', $attribute ) ) {
                            continue;
                        }

                        $query_type                                     = ! empty( $_GET[ 'query_type_' . $attribute ] ) && in_array( $_GET[ 'query_type_' . $attribute ], array( 'and', 'or' ), true ) ? masvideos_clean( wp_unslash( $_GET[ 'query_type_' . $attribute ] ) ) : ''; // WPCS: sanitization ok, input var ok, CSRF ok.
                        self::$_chosen_attributes[ $taxonomy ]['terms'] = array_map( 'sanitize_title', $filter_terms ); // Ensures correct encoding.
                        self::$_chosen_attributes[ $taxonomy ]['query_type'] = $query_type ? $query_type : apply_filters( 'masvideos_layered_nav_default_query_type', 'and' );
                    }
                }
            }
        }
        return self::$_chosen_attributes;
    }
}


/**
 * MasVideos_Movies_Query Class.
 */
class MasVideos_Movies_Query {

    /**
     * Reference to the main movie query on the page.
     *
     * @var array
     */
    private static $movie_query;

    /**
     * Chosen attributes.
     *
     * @var array
     */
    private static $_chosen_attributes;

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

    /**
     * Hook into pre_get_posts to do the main movie query.
     *
     * @param WP_Query $q Query instance.
     */
    public function pre_get_posts( $q ) {
        // We only want to affect the main query.
        if ( ! $q->is_main_query() ) {
            return;
        }

        // When orderby is set, WordPress shows posts on the front-page. Get around that here.
        if ( $this->is_showing_page_on_front( $q ) && $this->page_on_front_is( masvideos_get_page_id( 'movies' ) ) ) {
            $_query = wp_parse_args( $q->query );
            if ( empty( $_query ) || ! array_diff( array_keys( $_query ), array( 'preview', 'page', 'paged', 'cpage', 'orderby' ) ) ) {
                $q->set( 'page_id', (int) get_option( 'page_on_front' ) );
                $q->is_page = true;
                $q->is_home = false;

                // WP supporting themes show post type archive.
                if ( current_theme_supports( 'masvideos' ) ) {
                    $q->set( 'post_type', 'movie' );
                } else {
                    $q->is_singular = true;
                }
            }
        }

        // Fix movie feeds.
        if ( $q->is_feed() && $q->is_post_type_archive( 'movie' ) ) {
            $q->is_comment_feed = false;
        }

        // Special check for movies with the PRODUCT POST TYPE ARCHIVE on front.
        if ( current_theme_supports( 'masvideos' ) && $q->is_page() && 'page' === get_option( 'show_on_front' ) && absint( $q->get( 'page_id' ) ) === masvideos_get_page_id( 'movies' ) ) {
            // This is a front-page movies.
            $q->set( 'post_type', 'movie' );
            $q->set( 'page_id', '' );

            if ( isset( $q->query['paged'] ) ) {
                $q->set( 'paged', $q->query['paged'] );
            }

            // Define a variable so we know this is the front page movies later on.
            masvideos_maybe_define_constant( 'MOVIES_ON_FRONT', true );

            // Get the actual WP page to avoid errors and let us use is_front_page().
            // This is hacky but works. Awaiting https://core.trac.wordpress.org/ticket/21096.
            global $wp_post_types;

            $movies_page = get_post( masvideos_get_page_id( 'movies' ) );

            $wp_post_types['movie']->ID         = $movies_page->ID;
            $wp_post_types['movie']->post_title = $movies_page->post_title;
            $wp_post_types['movie']->post_name  = $movies_page->post_name;
            $wp_post_types['movie']->post_type  = $movies_page->post_type;
            $wp_post_types['movie']->ancestors  = get_ancestors( $movies_page->ID, $movies_page->post_type );

            // Fix conditional Functions like is_front_page.
            $q->is_singular          = false;
            $q->is_post_type_archive = true;
            $q->is_archive           = true;
            $q->is_page              = true;

            // Remove post type archive name from front page title tag.
            add_filter( 'post_type_archive_title', '__return_empty_string', 5 );

            // Fix WP SEO.
            if ( class_exists( 'WPSEO_Meta' ) ) {
                add_filter( 'wpseo_metadesc', array( $this, 'wpseo_metadesc' ) );
                add_filter( 'wpseo_metakey', array( $this, 'wpseo_metakey' ) );
            }
        } elseif ( ! $q->is_post_type_archive( 'movie' ) && ! $q->is_tax( get_object_taxonomies( 'movie' ) ) ) {
            // Only apply to movie categories, the movie post archive, the movies page, movie tags, and movie attribute taxonomies.
            return;
        }

        $this->movie_query( $q );
    }

    /**
     * WP SEO meta description.
     *
     * Hooked into wpseo_ hook already, so no need for function_exist.
     *
     * @return string
     */
    public function wpseo_metadesc() {
        return WPSEO_Meta::get_value( 'metadesc', masvideos_get_page_id( 'movies' ) );
    }

    /**
     * WP SEO meta key.
     *
     * Hooked into wpseo_ hook already, so no need for function_exist.
     *
     * @return string
     */
    public function wpseo_metakey() {
        return WPSEO_Meta::get_value( 'metakey', masvideos_get_page_id( 'movies' ) );
    }

    /**
     * Query the movies, applying sorting/ordering etc.
     * This applies to the main WordPress loop.
     *
     * @param WP_Query $q Query instance.
     */
    public function movie_query( $q ) {
        if ( ! is_feed() ) {
            $ordering = $this->get_catalog_ordering_args();
            $q->set( 'orderby', $ordering['orderby'] );
            $q->set( 'order', $ordering['order'] );

            if ( isset( $ordering['meta_key'] ) ) {
                $q->set( 'meta_key', $ordering['meta_key'] );
            }
        }

        // Query vars that affect posts shown.
        $q->set( 'meta_query', $this->get_meta_query( $q->get( 'meta_query' ), true ) );
        $q->set( 'tax_query', $this->get_tax_query( $q->get( 'tax_query' ), true ) );
        $q->set( 'masvideos_movie_query', 'movie_query' );
        $q->set( 'post__in', array_unique( (array) apply_filters( 'loop_movies_post_in', array() ) ) );

        // Work out how many movies to query.
        $q->set( 'posts_per_page', $q->get( 'posts_per_page' ) ? $q->get( 'posts_per_page' ) : apply_filters( 'masvideos_video_query_posts_per_page', 10 ) );

        // Store reference to this query.
        self::$movie_query = $q;

        do_action( 'masvideos_movie_query', $q, $this );
    }

    /**
     * Remove the query.
     */
    public function remove_movie_query() {
        remove_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
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
                    $orderby_value = apply_filters( 'masvideos_default_catalog_orderby', get_option( 'masvideos_default_catalog_orderby', 'menu_order' ) );
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
            case 'id':
                $args['orderby'] = 'ID';
                break;
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

        return apply_filters( 'masvideos_get_catalog_ordering_args', $args );
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
        return array_filter( apply_filters( 'masvideos_movie_query_meta_query', $meta_query, $this ) );
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
            foreach ( $this->get_layered_nav_chosen_attributes() as $taxonomy => $data ) {
                $tax_query[] = array(
                    'taxonomy'         => $taxonomy,
                    'field'            => 'slug',
                    'terms'            => $data['terms'],
                    'operator'         => 'and' === $data['query_type'] ? 'AND' : 'IN',
                    'include_children' => false,
                );
            }
        }

        return array_filter( apply_filters( 'masvideos_movie_query_tax_query', $tax_query, $this ) );
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
     * Based on WP_Query::parse_search
     */
    public static function get_main_search_query_sql() {
        global $wpdb;

        $args         = isset( self::$movie_query->query_vars ) ? self::$movie_query->query_vars : array();
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
     * Get an array of attributes and terms selected with the layered nav widget.
     *
     * @return array
     */
    public static function get_layered_nav_chosen_attributes() {
        if ( ! is_array( self::$_chosen_attributes ) ) {
            self::$_chosen_attributes = array();

            if ( ! empty( $_GET ) ) { // WPCS: input var ok, CSRF ok.
                foreach ( $_GET as $key => $value ) { // WPCS: input var ok, CSRF ok.
                    if ( 0 === strpos( $key, 'filter_' ) ) {
                        $attribute    = masvideos_sanitize_taxonomy_name( str_replace( 'filter_', '', $key ) );
                        $taxonomy     = masvideos_attribute_taxonomy_name( 'movie', $attribute );
                        $filter_terms = ! empty( $value ) ? explode( ',', masvideos_clean( wp_unslash( $value ) ) ) : array();

                        if ( empty( $filter_terms ) || ! taxonomy_exists( $taxonomy ) || ! masvideos_attribute_taxonomy_id_by_name( 'movie', $attribute ) ) {
                            continue;
                        }

                        $query_type                                     = ! empty( $_GET[ 'query_type_' . $attribute ] ) && in_array( $_GET[ 'query_type_' . $attribute ], array( 'and', 'or' ), true ) ? masvideos_clean( wp_unslash( $_GET[ 'query_type_' . $attribute ] ) ) : ''; // WPCS: sanitization ok, input var ok, CSRF ok.
                        self::$_chosen_attributes[ $taxonomy ]['terms'] = array_map( 'sanitize_title', $filter_terms ); // Ensures correct encoding.
                        self::$_chosen_attributes[ $taxonomy ]['query_type'] = $query_type ? $query_type : apply_filters( 'masvideos_layered_nav_default_query_type', 'and' );
                    }
                }
            }
        }
        return self::$_chosen_attributes;
    }
}