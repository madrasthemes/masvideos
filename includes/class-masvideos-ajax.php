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
            'toggle_tv_show_playlist'                          => true,
            'toggle_video_playlist'                            => true,
            'toggle_movie_playlist'                            => true,
            'add_attribute_person'                             => false,
            'add_new_attribute_person'                         => false,
            'save_attributes_person'                           => false,
            'json_search_persons'                              => false,
            'add_source_episode'                               => false,
            'save_sources_episode'                             => false,
            'add_attribute_episode'                            => false,
            'add_new_attribute_episode'                        => false,
            'save_attributes_episode'                          => false,
            'json_search_episodes'                             => false,
            'add_person_tv_show_cast'                          => false,
            'save_persons_tv_show_cast'                        => false,
            'add_person_tv_show_crew'                          => false,
            'save_persons_tv_show_crew'                        => false,
            'add_season_tv_show'                               => false,
            'save_seasons_tv_show'                             => false,
            'add_attribute_tv_show'                            => false,
            'add_new_attribute_tv_show'                        => false,
            'save_attributes_tv_show'                          => false,
            'json_search_tv_shows'                             => false,
            'add_person_movie_cast'                            => false,
            'save_persons_movie_cast'                          => false,
            'add_person_movie_crew'                            => false,
            'save_persons_movie_crew'                          => false,
            'add_source_movie'                                 => false,
            'save_sources_movie'                               => false,
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
     * Add an attribute row.
     */
    public static function add_attribute_person() {
        ob_start();

        check_ajax_referer( 'add-attribute-person', 'security' );

        if ( ! current_user_can( 'edit_persons' ) ) {
            wp_die( -1 );
        }

        $i             = absint( $_POST['i'] );
        $metabox_class = array();
        $attribute     = new MasVideos_Person_Attribute();

        $attribute->set_id( masvideos_attribute_taxonomy_id_by_name( 'person', sanitize_text_field( $_POST['taxonomy'] ) ) );
        $attribute->set_name( sanitize_text_field( $_POST['taxonomy'] ) );
        $attribute->set_visible( apply_filters( 'masvideos_attribute_default_visibility', 1 ) );

        if ( $attribute->is_taxonomy() ) {
            $metabox_class[] = 'taxonomy';
            $metabox_class[] = $attribute->get_name();
        }

        include 'admin/meta-boxes/views/html-person-attribute.php';
        wp_die();
    }

    /**
     * Add a new attribute via ajax function.
     */
    public static function add_new_attribute_person() {
        check_ajax_referer( 'add-attribute-person', 'security' );

        if ( current_user_can( 'manage_person_terms' ) ) {
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
    public static function save_attributes_person() {
        check_ajax_referer( 'save-attributes-person', 'security' );

        if ( ! current_user_can( 'edit_persons' ) ) {
            wp_die( -1 );
        }

        try {
            parse_str( $_POST['data'], $data );

            $attributes   = MasVideos_Meta_Box_Person_Data::prepare_attributes( $data );
            $person_id     = absint( $_POST['post_id'] );
            $classname    = MasVideos_Person_Factory::get_person_classname( $person_id );
            $person        = new $classname( $person_id );

            $person->set_attributes( $attributes );
            $person->save();

            $response = array();

            ob_start();
            $attributes = $person->get_attributes( 'edit' );
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

                include( 'admin/meta-boxes/views/html-person-attribute.php' );
            }

            $response['html'] = ob_get_clean();

            wp_send_json_success( $response );
        } catch ( Exception $e ) {
            wp_send_json_error( array( 'error' => $e->getMessage() ) );
        }
        wp_die();
    }

    /**
     * Search for persons and echo json.
     *
     * @param string $term (default: '')
     * @param bool   $include_variations in search or not
     */
    public static function json_search_persons( $term = '' ) {
        check_ajax_referer( 'search-persons', 'security' );

        $term = masvideos_clean( empty( $term ) ? wp_unslash( $_GET['term'] ) : $term );

        if ( empty( $term ) ) {
            wp_die();
        }

        if ( ! empty( $_GET['limit'] ) ) {
            $limit = absint( $_GET['limit'] );
        } else {
            $limit = absint( apply_filters( 'masvideos_json_search_limit', 30 ) );
        }

        $data_store = MasVideos_Data_Store::load( 'person' );
        $ids        = $data_store->search_persons( $term, false, $limit );

        if ( ! empty( $_GET['exclude'] ) ) {
            $ids = array_diff( $ids, (array) $_GET['exclude'] );
        }

        if ( ! empty( $_GET['include'] ) ) {
            $ids = array_intersect( $ids, (array) $_GET['include'] );
        }

        $person_objects = array_filter( array_map( 'masvideos_get_person', $ids ), 'masvideos_persons_array_filter_readable' );
        $persons        = array();

        foreach ( $person_objects as $person_object ) {
            $name = $person_object->get_name();
            $persons[ $person_object->get_id() ] = rawurldecode( $name );
        }

        wp_send_json( apply_filters( 'masvideos_json_search_found_persons', $persons ) );
    }

    /**
     * Add an source row.
     */
    public static function add_source_episode() {
        ob_start();

        check_ajax_referer( 'add-source-episode', 'security' );

        if ( ! current_user_can( 'edit_episodes' ) ) {
            wp_die( -1 );
        }

        $i             = absint( $_POST['i'] );
        $metabox_class = array();
        $source        = array(
            'name'          => sprintf( __( 'Source %d', 'masvideos' ), $i+1 ),
            'choice'        => '',
            'embed_content' => '',
            'link'          => '',
            'quality'       => '',
            'language'      => '',
            'player'        => '',
            'date_added'    => '',
            'position'      => $i,
        );

        include 'admin/meta-boxes/views/html-episode-source.php';
        wp_die();
    }

    /**
     * Save sources via ajax.
     */
    public static function save_sources_episode() {
        check_ajax_referer( 'save-sources-episode', 'security' );

        if ( ! current_user_can( 'edit_episodes' ) ) {
            wp_die( -1 );
        }

        try {
            parse_str( $_POST['data'], $data );

            $sources      = MasVideos_Meta_Box_Episode_Data::prepare_sources( $data );
            $episode_id   = absint( $_POST['post_id'] );
            $classname    = MasVideos_Episode_Factory::get_episode_classname( $episode_id );
            $episode      = new $classname( $episode_id );

            $episode->set_sources( $sources );
            $episode->save();

            $response = array();

            ob_start();
            $sources    = $episode->get_sources( 'edit' );
            $i          = -1;

            foreach ( $data['source_names'] as $source_name ) {
                $source = isset( $sources[ sanitize_title( $source_name ) ] ) ? $sources[ sanitize_title( $source_name ) ] : false;
                if ( ! $source ) {
                    continue;
                }
                $i++;
                $metabox_class = array();

                include( 'admin/meta-boxes/views/html-episode-source.php' );
            }

            $response['html'] = ob_get_clean();

            wp_send_json_success( $response );
        } catch ( Exception $e ) {
            wp_send_json_error( array( 'error' => $e->getMessage() ) );
        }
        wp_die();
    }

    /**
     * Add an attribute row.
     */
    public static function add_attribute_episode() {
        ob_start();

        check_ajax_referer( 'add-attribute-episode', 'security' );

        if ( ! current_user_can( 'edit_episodes' ) ) {
            wp_die( -1 );
        }

        $i             = absint( $_POST['i'] );
        $metabox_class = array();
        $attribute     = new MasVideos_Episode_Attribute();

        $attribute->set_id( masvideos_attribute_taxonomy_id_by_name( 'episode', sanitize_text_field( $_POST['taxonomy'] ) ) );
        $attribute->set_name( sanitize_text_field( $_POST['taxonomy'] ) );
        $attribute->set_visible( apply_filters( 'masvideos_attribute_default_visibility', 1 ) );

        if ( $attribute->is_taxonomy() ) {
            $metabox_class[] = 'taxonomy';
            $metabox_class[] = $attribute->get_name();
        }

        include 'admin/meta-boxes/views/html-episode-attribute.php';
        wp_die();
    }

    /**
     * Add a new attribute via ajax function.
     */
    public static function add_new_attribute_episode() {
        check_ajax_referer( 'add-attribute-episode', 'security' );

        if ( current_user_can( 'manage_episode_terms' ) ) {
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
    public static function save_attributes_episode() {
        check_ajax_referer( 'save-attributes-episode', 'security' );

        if ( ! current_user_can( 'edit_episodes' ) ) {
            wp_die( -1 );
        }

        try {
            parse_str( $_POST['data'], $data );

            $attributes   = MasVideos_Meta_Box_Episode_Data::prepare_attributes( $data );
            $episode_id   = absint( $_POST['post_id'] );
            $classname    = MasVideos_Episode_Factory::get_episode_classname( $episode_id );
            $episode      = new $classname( $episode_id );

            $episode->set_attributes( $attributes );
            $episode->save();

            $response = array();

            ob_start();
            $attributes = $episode->get_attributes( 'edit' );
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

                include( 'admin/meta-boxes/views/html-episode-attribute.php' );
            }

            $response['html'] = ob_get_clean();

            wp_send_json_success( $response );
        } catch ( Exception $e ) {
            wp_send_json_error( array( 'error' => $e->getMessage() ) );
        }
        wp_die();
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
     * Add tv show cast person row.
     */
    public static function add_person_tv_show_cast() {
        ob_start();

        check_ajax_referer( 'add-person-tv_show', 'security' );

        if ( ! current_user_can( 'edit_tv_shows' ) ) {
            wp_die( -1 );
        }

        $i             = absint( $_POST['i'] );
        $metabox_class = array();
        $person        = array(
            'id'            => absint( $_POST['person_id'] ),
            'character'     => '',
            'position'      => $i
        );

        include 'admin/meta-boxes/views/html-tv-show-cast-person.php';
        wp_die();
    }

    /**
     * Save tv show cast persons via ajax.
     */
    public static function save_persons_tv_show_cast() {
        check_ajax_referer( 'save-persons-tv_show', 'security' );

        if ( ! current_user_can( 'edit_tv_shows' ) ) {
            wp_die( -1 );
        }

        try {
            parse_str( $_POST['data'], $data );

            $cast           = MasVideos_Meta_Box_TV_Show_Data::prepare_cast( $data );
            $tv_show_id     = absint( $_POST['post_id'] );
            $classname      = MasVideos_TV_Show_Factory::get_tv_show_classname( $tv_show_id );
            $tv_show        = new $classname( $tv_show_id );

            $tv_show->set_cast( $cast );
            $tv_show->save();

            if( ! empty( $cast ) ) {
                foreach ( $cast as $key => $person ) {
                    if( ! empty( $person['id'] ) ) {
                        MasVideos_Meta_Box_Person_Data::update_credit( $tv_show_id, $person['id'], 'tv_show_cast' );
                    }
                }
            }

            $response = array();

            ob_start();
            $cast       = $tv_show->get_cast( 'edit' );
            $i          = -1;

            foreach ( $data['person_ids'] as $person_id ) {
                $person = isset( $cast[ absint( $person_id ) ] ) ? $cast[ absint( $person_id ) ] : false;
                if ( ! $person ) {
                    continue;
                }
                $i++;
                $metabox_class = array();

                include( 'admin/meta-boxes/views/html-tv-show-cast-person.php' );
            }

            $response['html'] = ob_get_clean();

            wp_send_json_success( $response );
        } catch ( Exception $e ) {
            wp_send_json_error( array( 'error' => $e->getMessage() ) );
        }
        wp_die();
    }

    /**
     * Add tv show crew person row.
     */
    public static function add_person_tv_show_crew() {
        ob_start();

        check_ajax_referer( 'add-person-tv_show', 'security' );

        if ( ! current_user_can( 'edit_tv_shows' ) ) {
            wp_die( -1 );
        }

        $i             = absint( $_POST['i'] );
        $metabox_class = array();
        $person        = array(
            'id'            => absint( $_POST['person_id'] ),
            'category'      => '',
            'job'           => '',
            'position'      => $i
        );

        include 'admin/meta-boxes/views/html-tv-show-crew-person.php';
        wp_die();
    }

    /**
     * Save tv show crew persons via ajax.
     */
    public static function save_persons_tv_show_crew() {
        check_ajax_referer( 'save-persons-tv_show', 'security' );

        if ( ! current_user_can( 'edit_tv_shows' ) ) {
            wp_die( -1 );
        }

        try {
            parse_str( $_POST['data'], $data );

            $crew           = MasVideos_Meta_Box_TV_Show_Data::prepare_crew( $data );
            $tv_show_id     = absint( $_POST['post_id'] );
            $classname      = MasVideos_TV_Show_Factory::get_tv_show_classname( $tv_show_id );
            $tv_show        = new $classname( $tv_show_id );

            $tv_show->set_crew( $crew );
            $tv_show->save();

            if( ! empty( $crew ) ) {
                foreach ( $crew as $key => $person ) {
                    if( ! empty( $person['id'] ) ) {
                        MasVideos_Meta_Box_Person_Data::update_credit( $tv_show_id, $person['id'], 'tv_show_crew' );
                    }
                }
            }

            $response = array();

            ob_start();
            $crew       = $tv_show->get_crew( 'edit' );
            $i          = -1;

            foreach ( $data['person_ids'] as $person_id ) {
                $person = isset( $crew[ absint( $person_id ) ] ) ? $crew[ absint( $person_id ) ] : false;
                if ( ! $person ) {
                    continue;
                }
                $i++;
                $metabox_class = array();

                include( 'admin/meta-boxes/views/html-tv-show-crew-person.php' );
            }

            $response['html'] = ob_get_clean();

            wp_send_json_success( $response );
        } catch ( Exception $e ) {
            wp_send_json_error( array( 'error' => $e->getMessage() ) );
        }
        wp_die();
    }

    /**
     * Add an attribute row.
     */
    public static function add_attribute_tv_show() {
        ob_start();

        check_ajax_referer( 'add-attribute-tv_show', 'security' );

        if ( ! current_user_can( 'edit_tv_shows' ) ) {
            wp_die( -1 );
        }

        $i             = absint( $_POST['i'] );
        $metabox_class = array();
        $attribute     = new MasVideos_TV_Show_Attribute();

        $attribute->set_id( masvideos_attribute_taxonomy_id_by_name( 'tv_show', sanitize_text_field( $_POST['taxonomy'] ) ) );
        $attribute->set_name( sanitize_text_field( $_POST['taxonomy'] ) );
        $attribute->set_visible( apply_filters( 'masvideos_attribute_default_visibility', 1 ) );

        if ( $attribute->is_taxonomy() ) {
            $metabox_class[] = 'taxonomy';
            $metabox_class[] = $attribute->get_name();
        }

        include 'admin/meta-boxes/views/html-tv-show-attribute.php';
        wp_die();
    }

    /**
     * Add a new attribute via ajax function.
     */
    public static function add_new_attribute_tv_show() {
        check_ajax_referer( 'add-attribute-tv_show', 'security' );

        if ( current_user_can( 'manage_tv_show_terms' ) ) {
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
    public static function save_attributes_tv_show() {
        check_ajax_referer( 'save-attributes-tv_show', 'security' );

        if ( ! current_user_can( 'edit_tv_shows' ) ) {
            wp_die( -1 );
        }

        try {
            parse_str( $_POST['data'], $data );

            $attributes   = MasVideos_Meta_Box_TV_Show_Data::prepare_attributes( $data );
            $tv_show_id   = absint( $_POST['post_id'] );
            $classname    = MasVideos_TV_Show_Factory::get_tv_show_classname( $tv_show_id );
            $tv_show      = new $classname( $tv_show_id );

            $tv_show->set_attributes( $attributes );
            $tv_show->save();

            $response = array();

            ob_start();
            $attributes = $tv_show->get_attributes( 'edit' );
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

                include( 'admin/meta-boxes/views/html-tv-show-attribute.php' );
            }

            $response['html'] = ob_get_clean();

            wp_send_json_success( $response );
        } catch ( Exception $e ) {
            wp_send_json_error( array( 'error' => $e->getMessage() ) );
        }
        wp_die();
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
            'year'          => '',
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

            if( ! empty( $seasons ) ) {
                foreach ( $seasons as $key => $season ) {
                    if( ! empty( $season['episodes'] ) ) {
                        foreach ( $season['episodes'] as $episode_id ) {
                            $episode = masvideos_get_episode( $episode_id );
                            $episode->set_tv_show_id( $tv_show_id );
                            $episode->set_tv_show_season_id( $key );
                            $episode->save();
                        }
                    }
                }
            }

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
     * Add movie cast person row.
     */
    public static function add_person_movie_cast() {
        ob_start();

        check_ajax_referer( 'add-person-movie', 'security' );

        if ( ! current_user_can( 'edit_movies' ) ) {
            wp_die( -1 );
        }

        $i             = absint( $_POST['i'] );
        $metabox_class = array();
        $person        = array(
            'id'            => absint( $_POST['person_id'] ),
            'character'     => '',
            'position'      => $i
        );

        include 'admin/meta-boxes/views/html-movie-cast-person.php';
        wp_die();
    }

    /**
     * Save movie cast persons via ajax.
     */
    public static function save_persons_movie_cast() {
        check_ajax_referer( 'save-persons-movie', 'security' );

        if ( ! current_user_can( 'edit_movies' ) ) {
            wp_die( -1 );
        }

        try {
            parse_str( $_POST['data'], $data );

            $cast         = MasVideos_Meta_Box_Movie_Data::prepare_cast( $data );
            $movie_id     = absint( $_POST['post_id'] );
            $classname    = MasVideos_Movie_Factory::get_movie_classname( $movie_id );
            $movie        = new $classname( $movie_id );

            $movie->set_cast( $cast );
            $movie->save();

            if( ! empty( $cast ) ) {
                foreach ( $cast as $key => $person ) {
                    if( ! empty( $person['id'] ) ) {
                        MasVideos_Meta_Box_Person_Data::update_credit( $movie_id, $person['id'], 'movie_cast' );
                    }
                }
            }

            $response = array();

            ob_start();
            $cast       = $movie->get_cast( 'edit' );
            $i          = -1;

            if( isset( $data['person_ids'] ) ) {
                foreach ( $data['person_ids'] as $person_id ) {
                    $person = isset( $cast[ absint( $person_id ) ] ) ? $cast[ absint( $person_id ) ] : false;
                    if ( ! $person ) {
                        continue;
                    }
                    $i++;
                    $metabox_class = array();

                    include( 'admin/meta-boxes/views/html-movie-cast-person.php' );
                }
            }

            $response['html'] = ob_get_clean();

            wp_send_json_success( $response );
        } catch ( Exception $e ) {
            wp_send_json_error( array( 'error' => $e->getMessage() ) );
        }
        wp_die();
    }

    /**
     * Add movie crew person row.
     */
    public static function add_person_movie_crew() {
        ob_start();

        check_ajax_referer( 'add-person-movie', 'security' );

        if ( ! current_user_can( 'edit_movies' ) ) {
            wp_die( -1 );
        }

        $i             = absint( $_POST['i'] );
        $metabox_class = array();
        $person        = array(
            'id'            => absint( $_POST['person_id'] ),
            'category'      => '',
            'job'           => '',
            'position'      => $i
        );

        include 'admin/meta-boxes/views/html-movie-crew-person.php';
        wp_die();
    }

    /**
     * Save movie crew persons via ajax.
     */
    public static function save_persons_movie_crew() {
        check_ajax_referer( 'save-persons-movie', 'security' );

        if ( ! current_user_can( 'edit_movies' ) ) {
            wp_die( -1 );
        }

        try {
            parse_str( $_POST['data'], $data );

            $crew         = MasVideos_Meta_Box_Movie_Data::prepare_crew( $data );
            $movie_id     = absint( $_POST['post_id'] );
            $classname    = MasVideos_Movie_Factory::get_movie_classname( $movie_id );
            $movie        = new $classname( $movie_id );

            $movie->set_crew( $crew );
            $movie->save();

            if( ! empty( $crew ) ) {
                foreach ( $crew as $key => $person ) {
                    if( ! empty( $person['id'] ) ) {
                        MasVideos_Meta_Box_Person_Data::update_credit( $movie_id, $person['id'], 'movie_crew' );
                    }
                }
            }

            $response = array();

            ob_start();
            $crew       = $movie->get_crew( 'edit' );
            $i          = -1;

            if( isset( $data['person_ids'] ) ) {
                foreach ( $data['person_ids'] as $person_id ) {
                    $person = isset( $crew[ absint( $person_id ) ] ) ? $crew[ absint( $person_id ) ] : false;
                    if ( ! $person ) {
                        continue;
                    }
                    $i++;
                    $metabox_class = array();

                    include( 'admin/meta-boxes/views/html-movie-crew-person.php' );
                }
            }

            $response['html'] = ob_get_clean();

            wp_send_json_success( $response );
        } catch ( Exception $e ) {
            wp_send_json_error( array( 'error' => $e->getMessage() ) );
        }
        wp_die();
    }

    /**
     * Add an source row.
     */
    public static function add_source_movie() {
        ob_start();

        check_ajax_referer( 'add-source-movie', 'security' );

        if ( ! current_user_can( 'edit_movies' ) ) {
            wp_die( -1 );
        }

        $i             = absint( $_POST['i'] );
        $metabox_class = array();
        $source        = array(
            'name'          => sprintf( __( 'Source %d', 'masvideos' ), $i+1 ),
            'choice'        => '',
            'embed_content' => '',
            'link'          => '',
            'is_affiliate'  => '',
            'quality'       => '',
            'language'      => '',
            'player'        => '',
            'date_added'    => '',
            'position'      => $i,
        );

        include 'admin/meta-boxes/views/html-movie-source.php';
        wp_die();
    }

    /**
     * Save sources via ajax.
     */
    public static function save_sources_movie() {
        check_ajax_referer( 'save-sources-movie', 'security' );

        if ( ! current_user_can( 'edit_movies' ) ) {
            wp_die( -1 );
        }

        try {
            parse_str( $_POST['data'], $data );

            $sources      = MasVideos_Meta_Box_Movie_Data::prepare_sources( $data );
            $movie_id     = absint( $_POST['post_id'] );
            $classname    = MasVideos_Movie_Factory::get_movie_classname( $movie_id );
            $movie        = new $classname( $movie_id );

            $movie->set_sources( $sources );
            $movie->save();

            $response = array();

            ob_start();
            $sources    = $movie->get_sources( 'edit' );
            $i          = -1;

            foreach ( $data['source_names'] as $source_name ) {
                $source = isset( $sources[ sanitize_title( $source_name ) ] ) ? $sources[ sanitize_title( $source_name ) ] : false;
                if ( ! $source ) {
                    continue;
                }
                $i++;
                $metabox_class = array();

                include( 'admin/meta-boxes/views/html-movie-source.php' );
            }

            $response['html'] = ob_get_clean();

            wp_send_json_success( $response );
        } catch ( Exception $e ) {
            wp_send_json_error( array( 'error' => $e->getMessage() ) );
        }
        wp_die();
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

    /**
     * AJAX toggle tv show to playlist.
     */
    public static function toggle_tv_show_playlist() {
        ob_start();

        $playlist_id        = absint( $_POST['playlist_id'] );
        $tv_show_id         = absint( $_POST['tv_show_id'] );
        $delete             = isset( $_POST['delete'] ) ? masvideos_string_to_bool( $_POST['delete'] ) : false;

        if( $delete ) {
            $tv_show_playlist = masvideos_remove_tv_show_from_playlist( $playlist_id, $tv_show_id );
        } else {
            $tv_show_playlist = masvideos_add_tv_show_to_playlist( $playlist_id, $tv_show_id );
        }

        if( $tv_show_playlist ) {
            $data = array(
                'success'       => true,
            );
        } else {
            $data = array(
                'error'         => true,
                'tv_show_url'   => apply_filters( 'masvideos_toggle_tv_show_playlist_redirect_after_error', get_permalink( $tv_show_id ), $tv_show_id ),
            );
        }

        wp_send_json( $data );
    }

    /**
     * AJAX toggle video to playlist.
     */
    public static function toggle_video_playlist() {
        ob_start();

        $playlist_id        = absint( $_POST['playlist_id'] );
        $video_id           = absint( $_POST['video_id'] );
        $delete             = isset( $_POST['delete'] ) ? masvideos_string_to_bool( $_POST['delete'] ) : false;

        if( $delete ) {
            $video_playlist = masvideos_remove_video_from_playlist( $playlist_id, $video_id );
        } else {
            $video_playlist = masvideos_add_video_to_playlist( $playlist_id, $video_id );
        }

        if( $video_playlist ) {
            $data = array(
                'success'       => true,
            );
        } else {
            $data = array(
                'error'         => true,
                'video_url'     => apply_filters( 'masvideos_toggle_video_playlist_redirect_after_error', get_permalink( $video_id ), $video_id ),
            );
        }

        wp_send_json( $data );
    }

    /**
     * AJAX toggle movie to playlist.
     */
    public static function toggle_movie_playlist() {
        ob_start();

        $playlist_id        = absint( $_POST['playlist_id'] );
        $movie_id           = absint( $_POST['movie_id'] );
        $delete             = isset( $_POST['delete'] ) ? masvideos_string_to_bool( $_POST['delete'] ) : false;

        if( $delete ) {
            $movie_playlist = masvideos_remove_movie_from_playlist( $playlist_id, $movie_id );
        } else {
            $movie_playlist = masvideos_add_movie_to_playlist( $playlist_id, $movie_id );
        }

        if( $movie_playlist ) {
            $data = array(
                'success'       => true,
            );
        } else {
            $data = array(
                'error'         => true,
                'movie_url'     => apply_filters( 'masvideos_toggle_movie_playlist_redirect_after_error', get_permalink( $movie_id ), $movie_id ),
            );
        }

        wp_send_json( $data );
    }
}

MasVideos_AJAX::init();
