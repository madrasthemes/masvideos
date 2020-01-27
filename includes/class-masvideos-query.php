<?php
/**
 * Contains the query functions for MasVideos which alter the front-end post queries and loops
 *
 * @version 1.0.0
 * @package MasVideos\Classes
 */

defined( 'ABSPATH' ) || exit;

/**
 * MasVideos_Query Class.
 */
class MasVideos_Query {

    /**
     * Query vars to add to wp.
     *
     * @var array
     */
    public $query_vars = array();

    /**
     * Constructor for the query class. Hooks in methods.
     */
    public function __construct() {
        add_action( 'init', array( $this, 'add_endpoints' ) );
        if ( ! is_admin() ) {
            add_action( 'wp_loaded', array( $this, 'get_errors' ), 20 );
            add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0 );
            add_action( 'parse_request', array( $this, 'parse_request' ), 0 );
        }
        $this->init_query_vars();
    }

    /**
     * Get any errors from querystring.
     */
    public function get_errors() {
        $error = ! empty( $_GET['masvideos_error'] ) ? sanitize_text_field( wp_unslash( $_GET['masvideos_error'] ) ) : ''; // WPCS: input var ok, CSRF ok.

        if ( $error && ! masvideos_has_notice( $error, 'error' ) ) {
            masvideos_add_notice( $error, 'error' );
        }
    }

    /**
     * Init query vars by loading options.
     */
    public function init_query_vars() {
        // Query vars to add to WP.
        $this->query_vars = array(
            // My account actions.
            'videos'                    => get_option( 'masvideos_myaccount_videos_endpoint', 'videos' ),
            'movie-playlists'           => get_option( 'masvideos_myaccount_movie_playlists_endpoint', 'movie-playlists' ),
            'video-playlists'           => get_option( 'masvideos_myaccount_video_playlists_endpoint', 'video-playlists' ),
            'tv-show-playlists'         => get_option( 'masvideos_myaccount_tv_show_playlists_endpoint', 'tv-show-playlists' ),
            'edit-account'              => get_option( 'masvideos_myaccount_edit_account_endpoint', 'edit-account' ),
            'user-logout'               => get_option( 'masvideos_logout_endpoint', 'user-logout' ),
        );
    }

    /**
     * Get page title for an endpoint.
     *
     * @param  string $endpoint Endpoint key.
     * @return string
     */
    public function get_endpoint_title( $endpoint ) {
        global $wp;

        switch ( $endpoint ) {
            case 'videos':
                if ( ! empty( $wp->query_vars['videos'] ) ) {
                    /* translators: %s: page */
                    $title = sprintf( esc_html__( 'Videos (page %d)', 'masvideos' ), intval( $wp->query_vars['videos'] ) );
                } else {
                    $title = esc_html__( 'Videos', 'masvideos' );
                }
                break;
            case 'movie-playlists':
                $title = esc_html__( 'Movie playlists', 'masvideos' );
                break;
            case 'video-playlists':
                $title = esc_html__( 'Video playlists', 'masvideos' );
                break;
            case 'tv-show-playlists':
                $title = esc_html__( 'TV Show playlists', 'masvideos' );
                break;
            case 'edit-account':
                $title = esc_html__( 'Account details', 'masvideos' );
                break;
            case 'user-logout':
                $title = esc_html__( 'Logout', 'masvideos' );
                break;
            default:
                $title = '';
                break;
        }

        return apply_filters( 'masvideos_endpoint_' . $endpoint . '_title', $title, $endpoint );
    }

    /**
     * Endpoint mask describing the places the endpoint should be added.
     * @return int
     */
    public function get_endpoints_mask() {
        if ( 'page' === get_option( 'show_on_front' ) ) {
            $page_on_front     = get_option( 'page_on_front' );
            $myaccount_page_id = get_option( 'masvideos_myaccount_page_id' );

            if ( in_array( $page_on_front, array( $myaccount_page_id ), true ) ) {
                return EP_ROOT | EP_PAGES;
            }
        }

        return EP_PAGES;
    }

    /**
     * Add endpoints for query vars.
     */
    public function add_endpoints() {
        $mask = $this->get_endpoints_mask();

        foreach ( $this->get_query_vars() as $key => $var ) {
            if ( ! empty( $var ) ) {
                add_rewrite_endpoint( $var, $mask );
            }
        }
    }

    /**
     * Add query vars.
     *
     * @param array $vars Query vars.
     * @return array
     */
    public function add_query_vars( $vars ) {
        foreach ( $this->get_query_vars() as $key => $var ) {
            $vars[] = $key;
        }
        return $vars;
    }

    /**
     * Get query vars.
     *
     * @return array
     */
    public function get_query_vars() {
        return apply_filters( 'masvideos_get_query_vars', $this->query_vars );
    }

    /**
     * Get query current active query var.
     *
     * @return string
     */
    public function get_current_endpoint() {
        global $wp;

        foreach ( $this->get_query_vars() as $key => $value ) {
            if ( isset( $wp->query_vars[ $key ] ) ) {
                return $key;
            }
        }
        return '';
    }

    /**
     * Parse the request and look for query vars - endpoints may not be supported.
     */
    public function parse_request() {
        global $wp;

        // Map query vars to their keys, or get them if endpoints are not supported.
        foreach ( $this->get_query_vars() as $key => $var ) {
            if ( isset( $_GET[ $var ] ) ) { // WPCS: input var ok, CSRF ok.
                $wp->query_vars[ $key ] = sanitize_text_field( wp_unslash( $_GET[ $var ] ) ); // WPCS: input var ok, CSRF ok.
            } elseif ( isset( $wp->query_vars[ $var ] ) ) {
                $wp->query_vars[ $key ] = $wp->query_vars[ $var ];
            }
        }
    }
}

/**
 * MasVideos_Persons_Query Class.
 */
class MasVideos_Persons_Query {

    /**
     * Reference to the main person query on the page.
     *
     * @var array
     */
    private static $person_query;

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
     * Hook into pre_get_posts to do the main person query.
     *
     * @param WP_Query $q Query instance.
     */
    public function pre_get_posts( $q ) {
        // We only want to affect the main query.
        if ( ! $q->is_main_query() ) {
            return;
        }

        // When orderby is set, WordPress shows posts on the front-page. Get around that here.
        if ( $this->is_showing_page_on_front( $q ) && $this->page_on_front_is( masvideos_get_page_id( 'persons' ) ) ) {
            $_query = wp_parse_args( $q->query );
            if ( empty( $_query ) || ! array_diff( array_keys( $_query ), array( 'preview', 'page', 'paged', 'cpage', 'orderby' ) ) ) {
                $q->set( 'page_id', (int) get_option( 'page_on_front' ) );
                $q->is_page = true;
                $q->is_home = false;

                // WP supporting themes show post type archive.
                if ( current_theme_supports( 'masvideos' ) ) {
                    $q->set( 'post_type', 'person' );
                } else {
                    $q->is_singular = true;
                }
            }
        }

        // Fix person feeds.
        if ( $q->is_feed() && $q->is_post_type_archive( 'person' ) ) {
            $q->is_comment_feed = false;
        }

        // Special check for persons with the PRODUCT POST TYPE ARCHIVE on front.
        if ( current_theme_supports( 'masvideos' ) && $q->is_page() && 'page' === get_option( 'show_on_front' ) && absint( $q->get( 'page_id' ) ) === masvideos_get_page_id( 'persons' ) ) {
            // This is a front-page persons.
            $q->set( 'post_type', 'person' );
            $q->set( 'page_id', '' );

            if ( isset( $q->query['paged'] ) ) {
                $q->set( 'paged', $q->query['paged'] );
            }

            // Define a variable so we know this is the front page persons later on.
            masvideos_maybe_define_constant( 'PERSONS_ON_FRONT', true );

            // Get the actual WP page to avoid errors and let us use is_front_page().
            // This is hacky but works. Awaiting https://core.trac.wordpress.org/ticket/21096.
            global $wp_post_types;

            $persons_page = get_post( masvideos_get_page_id( 'persons' ) );

            $wp_post_types['person']->ID         = $persons_page->ID;
            $wp_post_types['person']->post_title = $persons_page->post_title;
            $wp_post_types['person']->post_name  = $persons_page->post_name;
            $wp_post_types['person']->post_type  = $persons_page->post_type;
            $wp_post_types['person']->ancestors  = get_ancestors( $persons_page->ID, $persons_page->post_type );

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
        } elseif ( ! $q->is_post_type_archive( 'person' ) && ! $q->is_tax( get_object_taxonomies( 'person' ) ) ) {
            // Only apply to person categories, the person post archive, the persons page, person tags, and person attribute taxonomies.
            return;
        }

        $this->person_query( $q );
    }

    /**
     * WP SEO meta description.
     *
     * Hooked into wpseo_ hook already, so no need for function_exist.
     *
     * @return string
     */
    public function wpseo_metadesc() {
        return WPSEO_Meta::get_value( 'metadesc', masvideos_get_page_id( 'persons' ) );
    }

    /**
     * WP SEO meta key.
     *
     * Hooked into wpseo_ hook already, so no need for function_exist.
     *
     * @return string
     */
    public function wpseo_metakey() {
        return WPSEO_Meta::get_value( 'metakey', masvideos_get_page_id( 'persons' ) );
    }

    /**
     * Query the persons, applying sorting/ordering etc.
     * This applies to the main WordPress loop.
     *
     * @param WP_Query $q Query instance.
     */
    public function person_query( $q ) {
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
        $q->set( 'masvideos_person_query', 'person_query' );
        $q->set( 'post__in', array_unique( (array) apply_filters( 'loop_persons_post_in', array() ) ) );

        // Work out how many persons to query.
        $q->set( 'posts_per_page', $q->get( 'posts_per_page' ) ? $q->get( 'posts_per_page' ) : apply_filters( 'masvideos_person_query_posts_per_page', masvideos_get_default_persons_per_row() * masvideos_get_default_person_rows_per_page() ) );

        // Store reference to this query.
        self::$person_query = $q;

        do_action( 'masvideos_person_query', $q, $this );
    }

    /**
     * Remove the query.
     */
    public function remove_person_query() {
        remove_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
    }

    /**
     * Remove ordering queries.
     */
    public function remove_ordering_args() {
        // remove_filter( 'posts_clauses', array( $this, 'order_by_rating_post_clauses' ) );
    }

    /**
     * Returns an array of arguments for ordering persons based on the selected values.
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
                    $orderby_value = apply_filters( 'masvideos_default_persons_catalog_orderby', get_option( 'masvideos_default_persons_catalog_orderby', 'release_date' ) );
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

        return apply_filters( 'masvideos_get_persons_catalog_ordering_args', $args, $orderby, $order );
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

        return array_filter( apply_filters( 'masvideos_person_query_meta_query', $meta_query, $this ) );
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

        $person_visibility_terms  = masvideos_get_person_visibility_term_ids();
        $person_visibility_not_in = array( is_search() && $main_query ? $person_visibility_terms['exclude-from-search'] : $person_visibility_terms['exclude-from-catalog'] );

        if ( ! empty( $person_visibility_not_in ) ) {
            $tax_query[] = array(
                'taxonomy' => 'person_visibility',
                'field'    => 'term_taxonomy_id',
                'terms'    => $person_visibility_not_in,
                'operator' => 'NOT IN',
            );
        }

        return array_filter( apply_filters( 'masvideos_person_query_tax_query', $tax_query, $this ) );
    }

    /**
     * Get the main query which person queries ran against.
     *
     * @return array
     */
    public static function get_main_query() {
        return self::$person_query;
    }

    /**
     * Get the tax query which was used by the main query.
     *
     * @return array
     */
    public static function get_main_tax_query() {
        $tax_query = isset( self::$person_query->tax_query, self::$person_query->tax_query->queries ) ? self::$person_query->tax_query->queries : array();

        return $tax_query;
    }

    /**
     * Get the meta query which was used by the main query.
     *
     * @return array
     */
    public static function get_main_meta_query() {
        $args       = isset( self::$person_query->query_vars ) ? self::$person_query->query_vars : array();
        $meta_query = isset( $args['meta_query'] ) ? $args['meta_query'] : array();

        return $meta_query;
    }

    /**
     * Based on WP_Query::parse_search
     */
    public static function get_main_search_query_sql() {
        global $wpdb;

        $args         = isset( self::$person_query->query_vars ) ? self::$person_query->query_vars : array();
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
                        $taxonomy     = in_array( $attribute, array( 'genre', 'tag' ) ) ? 'person_' . $attribute : masvideos_attribute_taxonomy_name( 'person', $attribute );
                        $filter_terms = ! empty( $value ) ? explode( ',', masvideos_clean( wp_unslash( $value ) ) ) : array();

                        if ( empty( $filter_terms ) || ! taxonomy_exists( $taxonomy ) || ! ( in_array( $attribute, array( 'genre', 'tag' ) ) || masvideos_attribute_taxonomy_id_by_name( 'person', $attribute ) ) ) {
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
 * MasVideos_Episodes_Query Class.
 */
class MasVideos_Episodes_Query {

    /**
     * Reference to the main episode query on the page.
     *
     * @var array
     */
    private static $episode_query;

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
     * Hook into pre_get_posts to do the main episode query.
     *
     * @param WP_Query $q Query instance.
     */
    public function pre_get_posts( $q ) {
        // We only want to affect the main query.
        if ( ! $q->is_main_query() ) {
            return;
        }

        // When orderby is set, WordPress shows posts on the front-page. Get around that here.
        if ( $this->is_showing_page_on_front( $q ) && $this->page_on_front_is( masvideos_get_page_id( 'episodes' ) ) ) {
            $_query = wp_parse_args( $q->query );
            if ( empty( $_query ) || ! array_diff( array_keys( $_query ), array( 'preview', 'page', 'paged', 'cpage', 'orderby' ) ) ) {
                $q->set( 'page_id', (int) get_option( 'page_on_front' ) );
                $q->is_page = true;
                $q->is_home = false;

                // WP supporting themes show post type archive.
                if ( current_theme_supports( 'masvideos' ) ) {
                    $q->set( 'post_type', 'episode' );
                } else {
                    $q->is_singular = true;
                }
            }
        }

        // Fix episode feeds.
        if ( $q->is_feed() && $q->is_post_type_archive( 'episode' ) ) {
            $q->is_comment_feed = false;
        }

        // Special check for episodes with the PRODUCT POST TYPE ARCHIVE on front.
        if ( current_theme_supports( 'masvideos' ) && $q->is_page() && 'page' === get_option( 'show_on_front' ) && absint( $q->get( 'page_id' ) ) === masvideos_get_page_id( 'episodes' ) ) {
            // This is a front-page episodes.
            $q->set( 'post_type', 'episode' );
            $q->set( 'page_id', '' );

            if ( isset( $q->query['paged'] ) ) {
                $q->set( 'paged', $q->query['paged'] );
            }

            // Define a variable so we know this is the front page episodes later on.
            masvideos_maybe_define_constant( 'EPISODES_ON_FRONT', true );

            // Get the actual WP page to avoid errors and let us use is_front_page().
            // This is hacky but works. Awaiting https://core.trac.wordpress.org/ticket/21096.
            global $wp_post_types;

            $episodes_page = get_post( masvideos_get_page_id( 'episodes' ) );

            $wp_post_types['episode']->ID         = $episodes_page->ID;
            $wp_post_types['episode']->post_title = $episodes_page->post_title;
            $wp_post_types['episode']->post_name  = $episodes_page->post_name;
            $wp_post_types['episode']->post_type  = $episodes_page->post_type;
            $wp_post_types['episode']->ancestors  = get_ancestors( $episodes_page->ID, $episodes_page->post_type );

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
        } elseif ( ! $q->is_post_type_archive( 'episode' ) && ! $q->is_tax( get_object_taxonomies( 'episode' ) ) ) {
            // Only apply to episode categories, the episode post archive, the episodes page, episode tags, and episode attribute taxonomies.
            return;
        }

        $this->episode_query( $q );
    }

    /**
     * WP SEO meta description.
     *
     * Hooked into wpseo_ hook already, so no need for function_exist.
     *
     * @return string
     */
    public function wpseo_metadesc() {
        return WPSEO_Meta::get_value( 'metadesc', masvideos_get_page_id( 'episodes' ) );
    }

    /**
     * WP SEO meta key.
     *
     * Hooked into wpseo_ hook already, so no need for function_exist.
     *
     * @return string
     */
    public function wpseo_metakey() {
        return WPSEO_Meta::get_value( 'metakey', masvideos_get_page_id( 'episodes' ) );
    }

    /**
     * Query the episodes, applying sorting/ordering etc.
     * This applies to the main WordPress loop.
     *
     * @param WP_Query $q Query instance.
     */
    public function episode_query( $q ) {
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
        $q->set( 'masvideos_episode_query', 'episode_query' );
        $q->set( 'post__in', array_unique( (array) apply_filters( 'loop_episodes_post_in', array() ) ) );

        // Work out how many episodes to query.
        $q->set( 'posts_per_page', $q->get( 'posts_per_page' ) ? $q->get( 'posts_per_page' ) : apply_filters( 'masvideos_episode_query_posts_per_page', masvideos_get_default_episodes_per_row() * masvideos_get_default_episode_rows_per_page() ) );

        // Store reference to this query.
        self::$episode_query = $q;

        do_action( 'masvideos_episode_query', $q, $this );
    }

    /**
     * Remove the query.
     */
    public function remove_episode_query() {
        remove_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
    }

    /**
     * Remove ordering queries.
     */
    public function remove_ordering_args() {
        // remove_filter( 'posts_clauses', array( $this, 'order_by_rating_post_clauses' ) );
    }

    /**
     * Returns an array of arguments for ordering episodes based on the selected values.
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
                    $orderby_value = apply_filters( 'masvideos_default_episodes_catalog_orderby', get_option( 'masvideos_default_episodes_catalog_orderby', 'date' ) );
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
            case 'release_date':
                $args['meta_key'] = '_episode_release_date'; // @codingStandardsIgnoreLine
                $args['orderby']  = array(
                    'meta_value_num' => 'DESC',
                    'ID'             => 'ASC',
                );
                break;
            case 'rating':
                $args['meta_key'] = '_masvideos_average_rating'; // @codingStandardsIgnoreLine
                $args['orderby']  = array(
                    'meta_value_num' => 'DESC',
                    'ID'             => 'ASC',
                );
                break;
        }

        return apply_filters( 'masvideos_get_episodes_catalog_ordering_args', $args, $orderby, $order );
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
        return array_filter( apply_filters( 'masvideos_episode_query_meta_query', $meta_query, $this ) );
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

        $episode_visibility_terms  = masvideos_get_episode_visibility_term_ids();
        $episode_visibility_not_in = array( is_search() && $main_query ? $episode_visibility_terms['exclude-from-search'] : $episode_visibility_terms['exclude-from-catalog'] );

        // Filter by rating.
        if ( isset( $_GET['rating_filter'] ) ) { // WPCS: input var ok, CSRF ok.
            $rating_filter = array_filter( array_map( 'absint', explode( ',', $_GET['rating_filter'] ) ) ); // WPCS: input var ok, CSRF ok, Sanitization ok.
            $rating_terms  = array();
            for ( $i = 1; $i <= 10; $i ++ ) {
                if ( in_array( $i, $rating_filter, true ) && isset( $episode_visibility_terms[ 'rated-' . $i ] ) ) {
                    $rating_terms[] = $episode_visibility_terms[ 'rated-' . $i ];
                }
            }
            if ( ! empty( $rating_terms ) ) {
                $tax_query[] = array(
                    'taxonomy'      => 'episode_visibility',
                    'field'         => 'term_taxonomy_id',
                    'terms'         => $rating_terms,
                    'operator'      => 'IN',
                    'rating_filter' => true,
                );
            }
        }

        if ( ! empty( $episode_visibility_not_in ) ) {
            $tax_query[] = array(
                'taxonomy' => 'episode_visibility',
                'field'    => 'term_taxonomy_id',
                'terms'    => $episode_visibility_not_in,
                'operator' => 'NOT IN',
            );
        }

        return array_filter( apply_filters( 'masvideos_episode_query_tax_query', $tax_query, $this ) );
    }

    /**
     * Get the main query which episode queries ran against.
     *
     * @return array
     */
    public static function get_main_query() {
        return self::$episode_query;
    }

    /**
     * Get the tax query which was used by the main query.
     *
     * @return array
     */
    public static function get_main_tax_query() {
        $tax_query = isset( self::$episode_query->tax_query, self::$episode_query->tax_query->queries ) ? self::$episode_query->tax_query->queries : array();

        return $tax_query;
    }

    /**
     * Get the meta query which was used by the main query.
     *
     * @return array
     */
    public static function get_main_meta_query() {
        $args       = isset( self::$episode_query->query_vars ) ? self::$episode_query->query_vars : array();
        $meta_query = isset( $args['meta_query'] ) ? $args['meta_query'] : array();

        return $meta_query;
    }

    /**
     * Based on WP_Query::parse_search
     */
    public static function get_main_search_query_sql() {
        global $wpdb;

        $args         = isset( self::$episode_query->query_vars ) ? self::$episode_query->query_vars : array();
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
                        $taxonomy     = in_array( $attribute, array( 'genre', 'tag' ) ) ? 'episode_' . $attribute : masvideos_attribute_taxonomy_name( 'episode', $attribute );
                        $filter_terms = ! empty( $value ) ? explode( ',', masvideos_clean( wp_unslash( $value ) ) ) : array();

                        if ( empty( $filter_terms ) || ! taxonomy_exists( $taxonomy ) || ! ( in_array( $attribute, array( 'genre', 'tag' ) ) || masvideos_attribute_taxonomy_id_by_name( 'episode', $attribute ) ) ) {
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
 * MasVideos_TV_Shows_Query Class.
 */
class MasVideos_TV_Shows_Query {

    /**
     * Reference to the main tv show query on the page.
     *
     * @var array
     */
    private static $tv_show_query;

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
     * Hook into pre_get_posts to do the main tv show query.
     *
     * @param WP_Query $q Query instance.
     */
    public function pre_get_posts( $q ) {
        // We only want to affect the main query.
        if ( ! $q->is_main_query() ) {
            return;
        }

        // When orderby is set, WordPress shows posts on the front-page. Get around that here.
        if ( $this->is_showing_page_on_front( $q ) && $this->page_on_front_is( masvideos_get_page_id( 'tv_shows' ) ) ) {
            $_query = wp_parse_args( $q->query );
            if ( empty( $_query ) || ! array_diff( array_keys( $_query ), array( 'preview', 'page', 'paged', 'cpage', 'orderby' ) ) ) {
                $q->set( 'page_id', (int) get_option( 'page_on_front' ) );
                $q->is_page = true;
                $q->is_home = false;

                // WP supporting themes show post type archive.
                if ( current_theme_supports( 'masvideos' ) ) {
                    $q->set( 'post_type', 'tv_show' );
                } else {
                    $q->is_singular = true;
                }
            }
        }

        // Fix tv show feeds.
        if ( $q->is_feed() && $q->is_post_type_archive( 'tv_show' ) ) {
            $q->is_comment_feed = false;
        }

        // Special check for tv shows with the PRODUCT POST TYPE ARCHIVE on front.
        if ( current_theme_supports( 'masvideos' ) && $q->is_page() && 'page' === get_option( 'show_on_front' ) && absint( $q->get( 'page_id' ) ) === masvideos_get_page_id( 'tv_shows' ) ) {
            // This is a front-page tv shows.
            $q->set( 'post_type', 'tv_show' );
            $q->set( 'page_id', '' );

            if ( isset( $q->query['paged'] ) ) {
                $q->set( 'paged', $q->query['paged'] );
            }

            // Define a variable so we know this is the front page tv shows later on.
            masvideos_maybe_define_constant( 'TV_SHOWS_ON_FRONT', true );

            // Get the actual WP page to avoid errors and let us use is_front_page().
            // This is hacky but works. Awaiting https://core.trac.wordpress.org/ticket/21096.
            global $wp_post_types;

            $tv_shows_page = get_post( masvideos_get_page_id( 'tv_shows' ) );

            $wp_post_types['tv_show']->ID         = $tv_shows_page->ID;
            $wp_post_types['tv_show']->post_title = $tv_shows_page->post_title;
            $wp_post_types['tv_show']->post_name  = $tv_shows_page->post_name;
            $wp_post_types['tv_show']->post_type  = $tv_shows_page->post_type;
            $wp_post_types['tv_show']->ancestors  = get_ancestors( $tv_shows_page->ID, $tv_shows_page->post_type );

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
        } elseif ( ! $q->is_post_type_archive( 'tv_show' ) && ! $q->is_tax( get_object_taxonomies( 'tv_show' ) ) ) {
            // Only apply to tv show categories, the tv show post archive, the tv shows page, tv show tags, and tv show attribute taxonomies.
            return;
        }

        $this->tv_show_query( $q );
    }

    /**
     * WP SEO meta description.
     *
     * Hooked into wpseo_ hook already, so no need for function_exist.
     *
     * @return string
     */
    public function wpseo_metadesc() {
        return WPSEO_Meta::get_value( 'metadesc', masvideos_get_page_id( 'tv_shows' ) );
    }

    /**
     * WP SEO meta key.
     *
     * Hooked into wpseo_ hook already, so no need for function_exist.
     *
     * @return string
     */
    public function wpseo_metakey() {
        return WPSEO_Meta::get_value( 'metakey', masvideos_get_page_id( 'tv_shows' ) );
    }

    /**
     * Query the tv shows, applying sorting/ordering etc.
     * This applies to the main WordPress loop.
     *
     * @param WP_Query $q Query instance.
     */
    public function tv_show_query( $q ) {
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
        $q->set( 'masvideos_tv_show_query', 'tv_show_query' );
        $q->set( 'post__in', array_unique( (array) apply_filters( 'loop_tv_shows_post_in', array() ) ) );

        // Work out how many tv shows to query.
        $q->set( 'posts_per_page', $q->get( 'posts_per_page' ) ? $q->get( 'posts_per_page' ) : apply_filters( 'masvideos_tv_show_query_posts_per_page', masvideos_get_default_tv_shows_per_row() * masvideos_get_default_tv_show_rows_per_page() ) );

        // Store reference to this query.
        self::$tv_show_query = $q;

        do_action( 'masvideos_tv_show_query', $q, $this );
    }

    /**
     * Remove the query.
     */
    public function remove_tv_show_query() {
        remove_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
    }

    /**
     * Remove ordering queries.
     */
    public function remove_ordering_args() {
        // remove_filter( 'posts_clauses', array( $this, 'order_by_rating_post_clauses' ) );
    }

    /**
     * Returns an array of arguments for ordering tv shows based on the selected values.
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
                    $orderby_value = apply_filters( 'masvideos_default_tv_shows_catalog_orderby', get_option( 'masvideos_default_tv_shows_catalog_orderby', 'date' ) );
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
            case 'rating':
                $args['meta_key'] = '_masvideos_average_rating'; // @codingStandardsIgnoreLine
                $args['orderby']  = array(
                    'meta_value_num' => 'DESC',
                    'ID'             => 'ASC',
                );
                break;
        }

        return apply_filters( 'masvideos_get_tv_shows_catalog_ordering_args', $args, $orderby, $order );
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
        return array_filter( apply_filters( 'masvideos_tv_show_query_meta_query', $meta_query, $this ) );
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

        $tv_show_visibility_terms  = masvideos_get_tv_show_visibility_term_ids();
        $tv_show_visibility_not_in = array( is_search() && $main_query ? $tv_show_visibility_terms['exclude-from-search'] : $tv_show_visibility_terms['exclude-from-catalog'] );

        // Filter by rating.
        if ( isset( $_GET['rating_filter'] ) ) { // WPCS: input var ok, CSRF ok.
            $rating_filter = array_filter( array_map( 'absint', explode( ',', $_GET['rating_filter'] ) ) ); // WPCS: input var ok, CSRF ok, Sanitization ok.
            $rating_terms  = array();
            for ( $i = 1; $i <= 10; $i ++ ) {
                if ( in_array( $i, $rating_filter, true ) && isset( $tv_show_visibility_terms[ 'rated-' . $i ] ) ) {
                    $rating_terms[] = $tv_show_visibility_terms[ 'rated-' . $i ];
                }
            }
            if ( ! empty( $rating_terms ) ) {
                $tax_query[] = array(
                    'taxonomy'      => 'tv_show_visibility',
                    'field'         => 'term_taxonomy_id',
                    'terms'         => $rating_terms,
                    'operator'      => 'IN',
                    'rating_filter' => true,
                );
            }
        }

        if ( ! empty( $tv_show_visibility_not_in ) ) {
            $tax_query[] = array(
                'taxonomy' => 'tv_show_visibility',
                'field'    => 'term_taxonomy_id',
                'terms'    => $tv_show_visibility_not_in,
                'operator' => 'NOT IN',
            );
        }

        return array_filter( apply_filters( 'masvideos_tv_show_query_tax_query', $tax_query, $this ) );
    }

    /**
     * Get the main query which tv show queries ran against.
     *
     * @return array
     */
    public static function get_main_query() {
        return self::$tv_show_query;
    }

    /**
     * Get the tax query which was used by the main query.
     *
     * @return array
     */
    public static function get_main_tax_query() {
        $tax_query = isset( self::$tv_show_query->tax_query, self::$tv_show_query->tax_query->queries ) ? self::$tv_show_query->tax_query->queries : array();

        return $tax_query;
    }

    /**
     * Get the meta query which was used by the main query.
     *
     * @return array
     */
    public static function get_main_meta_query() {
        $args       = isset( self::$tv_show_query->query_vars ) ? self::$tv_show_query->query_vars : array();
        $meta_query = isset( $args['meta_query'] ) ? $args['meta_query'] : array();

        return $meta_query;
    }

    /**
     * Based on WP_Query::parse_search
     */
    public static function get_main_search_query_sql() {
        global $wpdb;

        $args         = isset( self::$tv_show_query->query_vars ) ? self::$tv_show_query->query_vars : array();
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
                        $taxonomy     = in_array( $attribute, array( 'genre', 'tag' ) ) ? 'tv_show_' . $attribute : masvideos_attribute_taxonomy_name( 'tv_show', $attribute );
                        $filter_terms = ! empty( $value ) ? explode( ',', masvideos_clean( wp_unslash( $value ) ) ) : array();

                        if ( empty( $filter_terms ) || ! taxonomy_exists( $taxonomy ) || ! ( in_array( $attribute, array( 'genre', 'tag' ) ) || masvideos_attribute_taxonomy_id_by_name( 'tv_show', $attribute ) ) ) {
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
        $q->set( 'posts_per_page', $q->get( 'posts_per_page' ) ? $q->get( 'posts_per_page' ) : apply_filters( 'masvideos_video_query_posts_per_page', masvideos_get_default_videos_per_row() * masvideos_get_default_video_rows_per_page() ) );

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
     * Remove ordering queries.
     */
    public function remove_ordering_args() {
        // remove_filter( 'posts_clauses', array( $this, 'order_by_rating_post_clauses' ) );
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
                    $orderby_value = apply_filters( 'masvideos_default_videos_catalog_orderby', get_option( 'masvideos_default_videos_catalog_orderby', 'date' ) );
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
            case 'rating':
                $args['meta_key'] = '_masvideos_average_rating'; // @codingStandardsIgnoreLine
                $args['orderby']  = array(
                    'meta_value_num' => 'DESC',
                    'ID'             => 'ASC',
                );
                break;
        }

        return apply_filters( 'masvideos_get_videos_catalog_ordering_args', $args, $orderby, $order );
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

        $video_visibility_terms  = masvideos_get_video_visibility_term_ids();
		$video_visibility_not_in = array( is_search() && $main_query ? $video_visibility_terms['exclude-from-search'] : $video_visibility_terms['exclude-from-catalog'] );

		// Filter by rating.
		if ( isset( $_GET['rating_filter'] ) ) { // WPCS: input var ok, CSRF ok.
			$rating_filter = array_filter( array_map( 'absint', explode( ',', $_GET['rating_filter'] ) ) ); // WPCS: input var ok, CSRF ok, Sanitization ok.
			$rating_terms  = array();
			for ( $i = 1; $i <= 10; $i ++ ) {
				if ( in_array( $i, $rating_filter, true ) && isset( $video_visibility_terms[ 'rated-' . $i ] ) ) {
					$rating_terms[] = $video_visibility_terms[ 'rated-' . $i ];
				}
			}
			if ( ! empty( $rating_terms ) ) {
				$tax_query[] = array(
					'taxonomy'      => 'video_visibility',
					'field'         => 'term_taxonomy_id',
					'terms'         => $rating_terms,
					'operator'      => 'IN',
					'rating_filter' => true,
				);
			}
		}

		if ( ! empty( $video_visibility_not_in ) ) {
			$tax_query[] = array(
				'taxonomy' => 'video_visibility',
				'field'    => 'term_taxonomy_id',
				'terms'    => $video_visibility_not_in,
				'operator' => 'NOT IN',
			);
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
                        $taxonomy     = in_array( $attribute, array( 'cat', 'tag' ) ) ? 'video_' . $attribute : masvideos_attribute_taxonomy_name( 'video', $attribute );
                        $filter_terms = ! empty( $value ) ? explode( ',', masvideos_clean( wp_unslash( $value ) ) ) : array();

                        if ( empty( $filter_terms ) || ! taxonomy_exists( $taxonomy ) || ! ( in_array( $attribute, array( 'cat', 'tag' ) ) || masvideos_attribute_taxonomy_id_by_name( 'video', $attribute ) ) ) {
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
        $q->set( 'posts_per_page', $q->get( 'posts_per_page' ) ? $q->get( 'posts_per_page' ) : apply_filters( 'masvideos_movie_query_posts_per_page', masvideos_get_default_movies_per_row() * masvideos_get_default_movie_rows_per_page() ) );

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
     * Remove ordering queries.
     */
    public function remove_ordering_args() {
        // remove_filter( 'posts_clauses', array( $this, 'order_by_rating_post_clauses' ) );
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
                    $orderby_value = apply_filters( 'masvideos_default_movies_catalog_orderby', get_option( 'masvideos_default_movies_catalog_orderby', 'release_date' ) );
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
            case 'release_date':
                $args['meta_key'] = '_movie_release_date'; // @codingStandardsIgnoreLine
                $args['orderby']  = array(
                    'meta_value_num' => 'DESC',
                    'ID'             => 'ASC',
                );
                break;
            case 'rating':
                $args['meta_key'] = '_masvideos_average_rating'; // @codingStandardsIgnoreLine
                $args['orderby']  = array(
                    'meta_value_num' => 'DESC',
                    'ID'             => 'ASC',
                );
                break;
        }

        return apply_filters( 'masvideos_get_movies_catalog_ordering_args', $args, $orderby, $order );
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

        // Filter by release date.
        if ( $main_query ) {
            if ( isset( $_GET[ 'year_filter' ] ) ) { // WPCS: input var ok, CSRF ok.
                $year_filter = array_filter( array_map( 'absint', explode( ',', wp_unslash( $_GET['year_filter'] ) ) ) ); // WPCS: input var ok, CSRF ok, Sanitization ok.
                if( ! empty( $year_filter ) ) {
                    $year_filter_meta_query = array();
                    $year = $year_filter[0];
                    $start = $year . '-01-01';
                    $end = $year . '-12-31';
                    $results_meta_query = MasVideos_Data_Store::load( 'movie' )->parse_date_for_wp_query( $year . '-01-01...' . $year . '-12-31', '_movie_release_date' );
                    if( ! empty( $results_meta_query['meta_query'] ) ) {
                        $year_filter_meta_query[] = $results_meta_query['meta_query'];
                    }
                    $year_filter_meta_query['relation'] = 'OR';
                    $meta_query['year_filter'] = $year_filter_meta_query;
                }
            }
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

        $movie_visibility_terms  = masvideos_get_movie_visibility_term_ids();
		$movie_visibility_not_in = array( is_search() && $main_query ? $movie_visibility_terms['exclude-from-search'] : $movie_visibility_terms['exclude-from-catalog'] );

		// Filter by rating.
		if ( isset( $_GET['rating_filter'] ) ) { // WPCS: input var ok, CSRF ok.
			$rating_filter = array_filter( array_map( 'absint', explode( ',', $_GET['rating_filter'] ) ) ); // WPCS: input var ok, CSRF ok, Sanitization ok.
			$rating_terms  = array();
			for ( $i = 1; $i <= 10; $i ++ ) {
				if ( in_array( $i, $rating_filter, true ) && isset( $movie_visibility_terms[ 'rated-' . $i ] ) ) {
					$rating_terms[] = $movie_visibility_terms[ 'rated-' . $i ];
				}
			}
			if ( ! empty( $rating_terms ) ) {
				$tax_query[] = array(
					'taxonomy'      => 'movie_visibility',
					'field'         => 'term_taxonomy_id',
					'terms'         => $rating_terms,
					'operator'      => 'IN',
					'rating_filter' => true,
				);
			}
		}

		if ( ! empty( $movie_visibility_not_in ) ) {
			$tax_query[] = array(
				'taxonomy' => 'movie_visibility',
				'field'    => 'term_taxonomy_id',
				'terms'    => $movie_visibility_not_in,
				'operator' => 'NOT IN',
			);
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
                        $taxonomy     = in_array( $attribute, array( 'genre', 'tag' ) ) ? 'movie_' . $attribute : masvideos_attribute_taxonomy_name( 'movie', $attribute );
                        $filter_terms = ! empty( $value ) ? explode( ',', masvideos_clean( wp_unslash( $value ) ) ) : array();

                        if ( empty( $filter_terms ) || ! taxonomy_exists( $taxonomy ) || ! ( in_array( $attribute, array( 'genre', 'tag' ) ) || masvideos_attribute_taxonomy_id_by_name( 'movie', $attribute ) ) ) {
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