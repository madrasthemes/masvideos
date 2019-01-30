<?php
/**
 * MasVideos MasVideos_AJAX. AJAX Event Handlers.
 *
 * @class    MasVideos_AJAX
 * @package  MasVideos/Classes
 */

defined( 'ABSPATH' ) || exit;

/**
 * MasVideos_Ajax class.
 */
class MasVideos_AJAX {

    /**
     * Hook in ajax handlers.
     */
    public static function init() {
        add_action( 'init', array( __CLASS__, 'define_ajax' ), 0 );
        add_action( 'template_redirect', array( __CLASS__, 'do_masvideos_ajax' ), 0 );
        self::add_ajax_events();
    }

    /**
     * Get MasVideos Ajax Endpoint.
     *
     * @param  string $request Optional.
     * @return string
     */
    public static function get_endpoint( $request = '' ) {
        return esc_url_raw( apply_filters( 'masvideos_ajax_get_endpoint', add_query_arg( 'masvideos-ajax', $request, remove_query_arg( array( 'remove_item', 'add-to-cart', 'added-to-cart', 'order_again', '_wpnonce' ), home_url( '/', 'relative' ) ) ), $request ) );
    }

    /**
     * Set MasVideos AJAX constant and headers.
     */
    public static function define_ajax() {
        if ( ! empty( $_GET['masvideos-ajax'] ) ) {
            masvideos_maybe_define_constant( 'DOING_AJAX', true );
            masvideos_maybe_define_constant( 'MasVideos_DOING_AJAX', true );
            if ( ! WP_DEBUG || ( WP_DEBUG && ! WP_DEBUG_DISPLAY ) ) {
                @ini_set( 'display_errors', 0 ); // Turn off display_errors during AJAX events to prevent malformed JSON.
            }
            $GLOBALS['wpdb']->hide_errors();
        }
    }

    /**
     * Send headers for MasVideos Ajax Requests.
     *
     * @since 1.0.0
     */
    private static function masvideos_ajax_headers() {
        send_origin_headers();
        @header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
        @header( 'X-Robots-Tag: noindex' );
        send_nosniff_header();
        masvideos_nocache_headers();
        status_header( 200 );
    }

    /**
     * Check for MasVideos Ajax request and fire action.
     */
    public static function do_masvideos_ajax() {
        global $wp_query;

        if ( ! empty( $_GET['masvideos-ajax'] ) ) {
            $wp_query->set( 'masvideos-ajax', sanitize_text_field( wp_unslash( $_GET['masvideos-ajax'] ) ) );
        }

        $action = $wp_query->get( 'masvideos-ajax' );

        if ( $action ) {
            self::masvideos_ajax_headers();
            $action = sanitize_text_field( $action );
            do_action( 'masvideos_ajax_' . $action );
            wp_die();
        }
    }

    /**
     * Hook in methods - uses WordPress ajax handlers (admin-ajax).
     */
    public static function add_ajax_events() {
        // masvideos_EVENT => nopriv.
        $ajax_events = array(
            'json_search_episodes'                             => false,
            'add_season_tv_show'                               => false,
            'save_seasons_tv_show'                             => false,
            'json_search_tv_shows'                             => false,
            'add_attribute_movie'                              => false,
            'add_new_attribute_movie'                          => false,
            'save_attributes_movie'                            => false,
            'json_search_movies'                               => false,
            'add_attribute_video'                              => false,
            'add_new_attribute_video'                          => false,
            'save_attributes_video'                            => false,
            'json_search_videos'                               => false,
        );

        foreach ( $ajax_events as $ajax_event => $nopriv ) {
            add_action( 'wp_ajax_masvideos_' . $ajax_event, array( __CLASS__, $ajax_event ) );

            if ( $nopriv ) {
                add_action( 'wp_ajax_nopriv_masvideos_' . $ajax_event, array( __CLASS__, $ajax_event ) );

                // MasVideos AJAX can be used for frontend ajax requests.
                add_action( 'masvideos_ajax_' . $ajax_event, array( __CLASS__, $ajax_event ) );
            }
        }
    }

    /**
     * Search for episodes and echo json.
     *
     * @param string $term (default: '')
     * @param bool   $include_variations in search or not
     */
    public static function json_search_episodes( $term = '' ) {
        check_ajax_referer( 'search-episodes', 'security' );

        $term = masvideos_clean( empty( $term ) ? wp_unslash( $_GET['term'] ) : $term );

        if ( empty( $term ) ) {
            wp_die();
        }

        if ( ! empty( $_GET['limit'] ) ) {
            $limit = absint( $_GET['limit'] );
        } else {
            $limit = absint( apply_filters( 'masvideos_json_search_limit', 30 ) );
        }

        $data_store = MasVideos_Data_Store::load( 'episode' );
        $ids        = $data_store->search_episodes( $term, false, $limit );

        if ( ! empty( $_GET['exclude'] ) ) {
            $ids = array_diff( $ids, (array) $_GET['exclude'] );
        }

        if ( ! empty( $_GET['include'] ) ) {
            $ids = array_intersect( $ids, (array) $_GET['include'] );
        }

        $episode_objects = array_filter( array_map( 'masvideos_get_episode', $ids ), 'masvideos_episodes_array_filter_readable' );
        $episodes        = array();

        foreach ( $episode_objects as $episode_object ) {
            $name = $episode_object->get_name();
            $episodes[ $episode_object->get_id() ] = rawurldecode( $name );
        }

        wp_send_json( apply_filters( 'masvideos_json_search_found_episodes', $episodes ) );
    }

    /**
     * Add an season row.
     */
    public static function add_season_tv_show() {
        ob_start();

        check_ajax_referer( 'add-season-tv_show', 'security' );

        if ( ! current_user_can( 'edit_tv_shows' ) ) {
            wp_die( -1 );
        }

        $i             = absint( $_POST['i'] );
        $metabox_class = array();
        $season        = array(
            'name'          => sprintf( __( 'Season %d', 'masvideos' ), $i+1 ),
            'image_id'      => 0,
            'episodes'      => array(),
            'description'   => '',
            'position'      => 0
        );

        include 'admin/meta-boxes/views/html-tv-show-season.php';
        wp_die();
    }

    /**
     * Save seasons via ajax.
     */
    public static function save_seasons_tv_show() {
        check_ajax_referer( 'save-seasons-tv_show', 'security' );

        if ( ! current_user_can( 'edit_tv_shows' ) ) {
            wp_die( -1 );
        }

        try {
            parse_str( $_POST['data'], $data );

            $seasons      = MasVideos_Meta_Box_TV_Show_Data::prepare_seasons( $data );
            $tv_show_id   = absint( $_POST['post_id'] );
            $classname    = MasVideos_TV_Show_Factory::get_tv_show_classname( $tv_show_id );
            $tv_show      = new $classname( $tv_show_id );

            $tv_show->set_seasons( $seasons );
            $tv_show->save();

            $response = array();

            ob_start();
            $seasons    = $tv_show->get_seasons( 'edit' );
            $i          = -1;

            foreach ( $data['season_names'] as $season_name ) {
                $season = isset( $seasons[ sanitize_title( $season_name ) ] ) ? $seasons[ sanitize_title( $season_name ) ] : false;
                if ( ! $season ) {
                    continue;
                }
                $i++;
                $metabox_class = array();

                include( 'admin/meta-boxes/views/html-tv-show-season.php' );
            }

            $response['html'] = ob_get_clean();

            wp_send_json_success( $response );
        } catch ( Exception $e ) {
            wp_send_json_error( array( 'error' => $e->getMessage() ) );
        }
        wp_die();
    }

    /**
     * Search for tv shows and echo json.
     *
     * @param string $term (default: '')
     * @param bool   $include_variations in search or not
     */
    public static function json_search_tv_shows( $term = '' ) {
        check_ajax_referer( 'search-tv_shows', 'security' );

        $term = masvideos_clean( empty( $term ) ? wp_unslash( $_GET['term'] ) : $term );

        if ( empty( $term ) ) {
            wp_die();
        }

        if ( ! empty( $_GET['limit'] ) ) {
            $limit = absint( $_GET['limit'] );
        } else {
            $limit = absint( apply_filters( 'masvideos_json_search_limit', 30 ) );
        }

        $data_store = MasVideos_Data_Store::load( 'tv_show' );
        $ids        = $data_store->search_tv_shows( $term, false, $limit );

        if ( ! empty( $_GET['exclude'] ) ) {
            $ids = array_diff( $ids, (array) $_GET['exclude'] );
        }

        if ( ! empty( $_GET['include'] ) ) {
            $ids = array_intersect( $ids, (array) $_GET['include'] );
        }

        $tv_show_objects = array_filter( array_map( 'masvideos_get_tv_show', $ids ), 'masvideos_tv_shows_array_filter_readable' );
        $tv_shows        = array();

        foreach ( $tv_show_objects as $tv_show_object ) {
            $name = $tv_show_object->get_name();
            $tv_shows[ $tv_show_object->get_id() ] = rawurldecode( $name );
        }

        wp_send_json( apply_filters( 'masvideos_json_search_found_tv_shows', $tv_shows ) );
    }

    /**
     * Add an attribute row.
     */
    public static function add_attribute_movie() {
        ob_start();

        check_ajax_referer( 'add-attribute-movie', 'security' );

        if ( ! current_user_can( 'edit_movies' ) ) {
            wp_die( -1 );
        }

        $i             = absint( $_POST['i'] );
        $metabox_class = array();
        $attribute     = new MasVideos_Movie_Attribute();

        $attribute->set_id( masvideos_attribute_taxonomy_id_by_name( 'movie', sanitize_text_field( $_POST['taxonomy'] ) ) );
        $attribute->set_name( sanitize_text_field( $_POST['taxonomy'] ) );
        $attribute->set_visible( apply_filters( 'masvideos_attribute_default_visibility', 1 ) );

        if ( $attribute->is_taxonomy() ) {
            $metabox_class[] = 'taxonomy';
            $metabox_class[] = $attribute->get_name();
        }

        include 'admin/meta-boxes/views/html-movie-attribute.php';
        wp_die();
    }

    /**
     * Add a new attribute via ajax function.
     */
    public static function add_new_attribute_movie() {
        check_ajax_referer( 'add-attribute-movie', 'security' );

        if ( current_user_can( 'manage_movie_terms' ) ) {
            $taxonomy = esc_attr( $_POST['taxonomy'] );
            $term     = masvideos_clean( $_POST['term'] );

            if ( taxonomy_exists( $taxonomy ) ) {

                $result = wp_insert_term( $term, $taxonomy );

                if ( is_wp_error( $result ) ) {
                    wp_send_json(
                        array(
                            'error' => $result->get_error_message(),
                        )
                    );
                } else {
                    $term = get_term_by( 'id', $result['term_id'], $taxonomy );
                    wp_send_json(
                        array(
                            'term_id' => $term->term_id,
                            'name'    => $term->name,
                            'slug'    => $term->slug,
                        )
                    );
                }
            }
        }
        wp_die( -1 );
    }

    /**
     * Save attributes via ajax.
     */
    public static function save_attributes_movie() {
        check_ajax_referer( 'save-attributes-movie', 'security' );

        if ( ! current_user_can( 'edit_movies' ) ) {
            wp_die( -1 );
        }

        try {
            parse_str( $_POST['data'], $data );

            $attributes   = MasVideos_Meta_Box_Movie_Data::prepare_attributes( $data );
            $movie_id     = absint( $_POST['post_id'] );
            $classname    = MasVideos_Movie_Factory::get_movie_classname( $movie_id );
            $movie        = new $classname( $movie_id );

            $movie->set_attributes( $attributes );
            $movie->save();

            $response = array();

            ob_start();
            $attributes = $movie->get_attributes( 'edit' );
            $i          = -1;

            foreach ( $data['attribute_names'] as $attribute_name ) {
                $attribute = isset( $attributes[ sanitize_title( $attribute_name ) ] ) ? $attributes[ sanitize_title( $attribute_name ) ] : false;
                if ( ! $attribute ) {
                    continue;
                }
                $i++;
                $metabox_class = array();

                if ( $attribute->is_taxonomy() ) {
                    $metabox_class[] = 'taxonomy';
                    $metabox_class[] = $attribute->get_name();
                }

                include( 'admin/meta-boxes/views/html-movie-attribute.php' );
            }

            $response['html'] = ob_get_clean();

            wp_send_json_success( $response );
        } catch ( Exception $e ) {
            wp_send_json_error( array( 'error' => $e->getMessage() ) );
        }
        wp_die();
    }

    /**
     * Search for movies and echo json.
     *
     * @param string $term (default: '')
     * @param bool   $include_variations in search or not
     */
    public static function json_search_movies( $term = '' ) {
        check_ajax_referer( 'search-movies', 'security' );

        $term = masvideos_clean( empty( $term ) ? wp_unslash( $_GET['term'] ) : $term );

        if ( empty( $term ) ) {
            wp_die();
        }

        if ( ! empty( $_GET['limit'] ) ) {
            $limit = absint( $_GET['limit'] );
        } else {
            $limit = absint( apply_filters( 'masvideos_json_search_limit', 30 ) );
        }

        $data_store = MasVideos_Data_Store::load( 'movie' );
        $ids        = $data_store->search_movies( $term, false, $limit );

        if ( ! empty( $_GET['exclude'] ) ) {
            $ids = array_diff( $ids, (array) $_GET['exclude'] );
        }

        if ( ! empty( $_GET['include'] ) ) {
            $ids = array_intersect( $ids, (array) $_GET['include'] );
        }

        $movie_objects = array_filter( array_map( 'masvideos_get_movie', $ids ), 'masvideos_movies_array_filter_readable' );
        $movies        = array();

        foreach ( $movie_objects as $movie_object ) {
            $name = $movie_object->get_name();
            $movies[ $movie_object->get_id() ] = rawurldecode( $name );
        }

        wp_send_json( apply_filters( 'masvideos_json_search_found_movies', $movies ) );
    }

    /**
     * Add an attribute row.
     */
    public static function add_attribute_video() {
        ob_start();

        check_ajax_referer( 'add-attribute-video', 'security' );

        if ( ! current_user_can( 'edit_videos' ) ) {
            wp_die( -1 );
        }

        $i             = absint( $_POST['i'] );
        $metabox_class = array();
        $attribute     = new MasVideos_Video_Attribute();

        $attribute->set_id( masvideos_attribute_taxonomy_id_by_name( 'video', sanitize_text_field( $_POST['taxonomy'] ) ) );
        $attribute->set_name( sanitize_text_field( $_POST['taxonomy'] ) );
        $attribute->set_visible( apply_filters( 'masvideos_attribute_default_visibility', 1 ) );

        if ( $attribute->is_taxonomy() ) {
            $metabox_class[] = 'taxonomy';
            $metabox_class[] = $attribute->get_name();
        }

        include 'admin/meta-boxes/views/html-video-attribute.php';
        wp_die();
    }

    /**
     * Add a new attribute via ajax function.
     */
    public static function add_new_attribute_video() {
        check_ajax_referer( 'add-attribute-video', 'security' );

        if ( current_user_can( 'manage_video_terms' ) ) {
            $taxonomy = esc_attr( $_POST['taxonomy'] );
            $term     = masvideos_clean( $_POST['term'] );

            if ( taxonomy_exists( $taxonomy ) ) {

                $result = wp_insert_term( $term, $taxonomy );

                if ( is_wp_error( $result ) ) {
                    wp_send_json(
                        array(
                            'error' => $result->get_error_message(),
                        )
                    );
                } else {
                    $term = get_term_by( 'id', $result['term_id'], $taxonomy );
                    wp_send_json(
                        array(
                            'term_id' => $term->term_id,
                            'name'    => $term->name,
                            'slug'    => $term->slug,
                        )
                    );
                }
            }
        }
        wp_die( -1 );
    }

    /**
     * Save attributes via ajax.
     */
    public static function save_attributes_video() {
        check_ajax_referer( 'save-attributes-video', 'security' );

        if ( ! current_user_can( 'edit_videos' ) ) {
            wp_die( -1 );
        }

        try {
            parse_str( $_POST['data'], $data );

            $attributes   = MasVideos_Meta_Box_Video_Data::prepare_attributes( $data );
            $video_id     = absint( $_POST['post_id'] );
            $classname    = MasVideos_Video_Factory::get_video_classname( $video_id );
            $video        = new $classname( $video_id );

            $video->set_attributes( $attributes );
            $video->save();

            $response = array();

            ob_start();
            $attributes = $video->get_attributes( 'edit' );
            $i          = -1;

            foreach ( $data['attribute_names'] as $attribute_name ) {
                $attribute = isset( $attributes[ sanitize_title( $attribute_name ) ] ) ? $attributes[ sanitize_title( $attribute_name ) ] : false;
                if ( ! $attribute ) {
                    continue;
                }
                $i++;
                $metabox_class = array();

                if ( $attribute->is_taxonomy() ) {
                    $metabox_class[] = 'taxonomy';
                    $metabox_class[] = $attribute->get_name();
                }

                include( 'admin/meta-boxes/views/html-video-attribute.php' );
            }

            $response['html'] = ob_get_clean();

            wp_send_json_success( $response );
        } catch ( Exception $e ) {
            wp_send_json_error( array( 'error' => $e->getMessage() ) );
        }
        wp_die();
    }

    /**
     * Search for videos and echo json.
     *
     * @param string $term (default: '')
     * @param bool   $include_variations in search or not
     */
    public static function json_search_videos( $term = '' ) {
        check_ajax_referer( 'search-videos', 'security' );

        $term = masvideos_clean( empty( $term ) ? wp_unslash( $_GET['term'] ) : $term );

        if ( empty( $term ) ) {
            wp_die();
        }

        if ( ! empty( $_GET['limit'] ) ) {
            $limit = absint( $_GET['limit'] );
        } else {
            $limit = absint( apply_filters( 'masvideos_json_search_limit', 30 ) );
        }

        $data_store = MasVideos_Data_Store::load( 'video' );
        $ids        = $data_store->search_videos( $term, false, $limit );

        if ( ! empty( $_GET['exclude'] ) ) {
            $ids = array_diff( $ids, (array) $_GET['exclude'] );
        }

        if ( ! empty( $_GET['include'] ) ) {
            $ids = array_intersect( $ids, (array) $_GET['include'] );
        }

        $video_objects = array_filter( array_map( 'masvideos_get_video', $ids ), 'masvideos_videos_array_filter_readable' );
        $videos        = array();

        foreach ( $video_objects as $video_object ) {
            $name = $video_object->get_name();
            $videos[ $video_object->get_id() ] = rawurldecode( $name );
        }

        wp_send_json( apply_filters( 'masvideos_json_search_found_videos', $videos ) );
    }
}

MasVideos_AJAX::init();
