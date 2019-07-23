<?php
/**
 * Load assets
 *
 * @package     MasVideos/Admin
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'MasVideos_Admin_Assets', false ) ) :

    /**
     * MasVideos_Admin_Assets Class.
     */
    class MasVideos_Admin_Assets {

        /**
         * Hook in tabs.
         */
        public function __construct() {
            add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
            add_action( 'enqueue_block_editor_assets', array( $this, 'block_editor_assets' ) );
        }

        /**
         * Enqueue styles.
         */
        public function admin_styles() {
            global $wp_scripts;

            $screen    = get_current_screen();
            $screen_id = $screen ? $screen->id : '';
            $suffix    = '';

            // Register admin styles.
            wp_register_style( 'masvideos_admin_styles', MasVideos()->plugin_url() . '/assets/css/admin' . $suffix . '.css', array(), MASVIDEOS_VERSION );
            wp_register_style( 'jquery-ui-style', MasVideos()->plugin_url() . '/assets/css/jquery-ui/jquery-ui.min.css', array(), MASVIDEOS_VERSION );

            // Add RTL support for admin styles.
            wp_style_add_data( 'masvideos_admin_styles', 'rtl', 'replace' );

            // Admin styles for MasVideos pages only.
            if ( in_array( $screen_id, masvideos_get_screen_ids() ) ) {
                wp_enqueue_style( 'masvideos_admin_styles' );
                wp_enqueue_style( 'jquery-ui-style' );
                wp_enqueue_style( 'wp-color-picker' );
            }
        }


        /**
         * Enqueue scripts.
         */
        public function admin_scripts() {
            global $wp_query, $post;

            $screen       = get_current_screen();
            $screen_id    = $screen ? $screen->id : '';
            $wc_screen_id = sanitize_title( __( 'MasVideos', 'masvideos' ) );
            $suffix       = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

            // Register scripts.
            wp_register_script( 'jquery-blockui', MasVideos()->plugin_url() . '/assets/js/jquery-blockui/jquery.blockUI' . $suffix . '.js', array( 'jquery' ), '2.70', true );
            wp_register_script( 'jquery-tiptip', MasVideos()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip' . $suffix . '.js', array( 'jquery' ), MASVIDEOS_VERSION, true );
            wp_register_script( 'round', MasVideos()->plugin_url() . '/assets/js/round/round' . $suffix . '.js', array( 'jquery' ), MASVIDEOS_VERSION );
            wp_register_script( 'masvideos-admin-meta-boxes', MasVideos()->plugin_url() . '/assets/js/admin/meta-boxes' . $suffix . '.js', array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-sortable', 'round', 'masvideos-enhanced-select', 'plupload-all', 'stupidtable', 'jquery-tiptip' ), MASVIDEOS_VERSION );
            wp_register_script( 'stupidtable', MasVideos()->plugin_url() . '/assets/js/stupidtable/stupidtable' . $suffix . '.js', array( 'jquery' ), MASVIDEOS_VERSION );
            wp_register_script( 'select2', MasVideos()->plugin_url() . '/assets/js/select2/select2.full' . $suffix . '.js', array( 'jquery' ), '4.0.3' );
            wp_register_script( 'selectWoo', MasVideos()->plugin_url() . '/assets/js/selectWoo/selectWoo.full' . $suffix . '.js', array( 'jquery' ), '1.0.4' );
            wp_register_script( 'masvideos-enhanced-select', MasVideos()->plugin_url() . '/assets/js/admin/masvideos-enhanced-select' . $suffix . '.js', array( 'jquery', 'selectWoo' ), MASVIDEOS_VERSION );
            wp_localize_script(
                'masvideos-enhanced-select',
                'masvideos_enhanced_select_params',
                array(
                    'i18n_no_matches'           => _x( 'No matches found', 'enhanced select', 'masvideos' ),
                    'i18n_ajax_error'           => _x( 'Loading failed', 'enhanced select', 'masvideos' ),
                    'i18n_input_too_short_1'    => _x( 'Please enter 1 or more characters', 'enhanced select', 'masvideos' ),
                    'i18n_input_too_short_n'    => _x( 'Please enter %qty% or more characters', 'enhanced select', 'masvideos' ),
                    'i18n_input_too_long_1'     => _x( 'Please delete 1 character', 'enhanced select', 'masvideos' ),
                    'i18n_input_too_long_n'     => _x( 'Please delete %qty% characters', 'enhanced select', 'masvideos' ),
                    'i18n_selection_too_long_1' => _x( 'You can only select 1 item', 'enhanced select', 'masvideos' ),
                    'i18n_selection_too_long_n' => _x( 'You can only select %qty% items', 'enhanced select', 'masvideos' ),
                    'i18n_load_more'            => _x( 'Loading more results&hellip;', 'enhanced select', 'masvideos' ),
                    'i18n_searching'            => _x( 'Searching&hellip;', 'enhanced select', 'masvideos' ),
                    'ajax_url'                  => admin_url( 'admin-ajax.php' ),
                    'search_persons_nonce'      => wp_create_nonce( 'search-persons' ),
                    'search_episodes_nonce'     => wp_create_nonce( 'search-episodes' ),
                    'search_tv_shows_nonce'     => wp_create_nonce( 'search-tv_shows' ),
                    'search_videos_nonce'       => wp_create_nonce( 'search-videos' ),
                    'search_movies_nonce'       => wp_create_nonce( 'search-movies' ),
                )
            );

            // Meta boxes.
            if ( in_array( $screen_id, array( 'person', 'edit-person' ) ) ) {
                wp_enqueue_media();
                wp_register_script( 'masvideos-admin-person-meta-boxes', MasVideos()->plugin_url() . '/assets/js/admin/meta-boxes-person' . $suffix . '.js', array( 'masvideos-admin-meta-boxes', 'media-models', 'jquery-blockui' ), MASVIDEOS_VERSION );
                wp_enqueue_script( 'masvideos-admin-person-meta-boxes' );
            }
            if ( in_array( $screen_id, array( 'episode', 'edit-episode' ) ) ) {
                wp_enqueue_media();
                wp_register_script( 'masvideos-admin-episode-meta-boxes', MasVideos()->plugin_url() . '/assets/js/admin/meta-boxes-episode' . $suffix . '.js', array( 'masvideos-admin-meta-boxes', 'media-models', 'jquery-blockui' ), MASVIDEOS_VERSION );
                wp_enqueue_script( 'masvideos-admin-episode-meta-boxes' );
            }
            if ( in_array( $screen_id, array( 'tv_show', 'edit-tv_show' ) ) ) {
                wp_enqueue_media();
                wp_register_script( 'masvideos-admin-tv-show-meta-boxes', MasVideos()->plugin_url() . '/assets/js/admin/meta-boxes-tv-show' . $suffix . '.js', array( 'masvideos-admin-meta-boxes', 'media-models', 'jquery-blockui' ), MASVIDEOS_VERSION );
                wp_enqueue_script( 'masvideos-admin-tv-show-meta-boxes' );
            }
            if ( in_array( $screen_id, array( 'video', 'edit-video' ) ) ) {
                wp_enqueue_media();
                wp_register_script( 'masvideos-admin-video-meta-boxes', MasVideos()->plugin_url() . '/assets/js/admin/meta-boxes-video' . $suffix . '.js', array( 'masvideos-admin-meta-boxes', 'media-models', 'jquery-blockui' ), MASVIDEOS_VERSION );
                wp_enqueue_script( 'masvideos-admin-video-meta-boxes' );
            }
            if ( in_array( $screen_id, array( 'movie', 'edit-movie' ) ) ) {
                wp_enqueue_media();
                wp_register_script( 'masvideos-admin-movie-meta-boxes', MasVideos()->plugin_url() . '/assets/js/admin/meta-boxes-movie' . $suffix . '.js', array( 'masvideos-admin-meta-boxes', 'media-models', 'jquery-blockui' ), MASVIDEOS_VERSION );
                wp_enqueue_script( 'masvideos-admin-movie-meta-boxes' );
            }

            if ( in_array( str_replace( 'edit-', '', $screen_id ), array( 'person', 'episode', 'tv_show', 'video', 'movie' ) ) ) {
                $post_id            = isset( $post->ID ) ? $post->ID : '';
                $remove_item_notice = __( 'Are you sure you want to remove the selected items?', 'masvideos' );

                $params = array(
                    'remove_item_notice'            => $remove_item_notice,
                    'i18n_select_items'             => __( 'Please select some items.', 'masvideos' ),
                    'remove_item_meta'              => __( 'Remove this item meta?', 'masvideos' ),
                    'remove_attribute'              => __( 'Remove this attribute?', 'masvideos' ),
                    'name_label'                    => __( 'Name', 'masvideos' ),
                    'remove_label'                  => __( 'Remove', 'masvideos' ),
                    'click_to_toggle'               => __( 'Click to toggle', 'masvideos' ),
                    'values_label'                  => __( 'Value(s)', 'masvideos' ),
                    'text_attribute_tip'            => __( 'Enter some text, or some attributes by pipe (|) separating values.', 'masvideos' ),
                    'visible_label'                 => __( 'Visible on the masvideos page', 'masvideos' ),
                    'new_attribute_prompt'          => __( 'Enter a name for the new attribute term:', 'masvideos' ),
                    'featured_label'                => __( 'Featured', 'masvideos' ),
                    'plugin_url'                    => MasVideos()->plugin_url(),
                    'ajax_url'                      => admin_url( 'admin-ajax.php' ),
                    'add_attribute_person_nonce'    => wp_create_nonce( 'add-attribute-person' ),
                    'save_attributes_person_nonce'  => wp_create_nonce( 'save-attributes-person' ),
                    'add_source_episode_nonce'      => wp_create_nonce( 'add-source-episode' ),
                    'save_sources_episode_nonce'    => wp_create_nonce( 'save-sources-episode' ),
                    'add_attribute_episode_nonce'   => wp_create_nonce( 'add-attribute-episode' ),
                    'save_attributes_episode_nonce' => wp_create_nonce( 'save-attributes-episode' ),
                    'search_episodes_nonce'         => wp_create_nonce( 'search-episodes' ),
                    'add_person_tv_show_nonce'      => wp_create_nonce( 'add-person-tv_show' ),
                    'save_persons_tv_show_nonce'    => wp_create_nonce( 'save-persons-tv_show' ),
                    'add_season_tv_show_nonce'      => wp_create_nonce( 'add-season-tv_show' ),
                    'save_seasons_tv_show_nonce'    => wp_create_nonce( 'save-seasons-tv_show' ),
                    'add_attribute_tv_show_nonce'   => wp_create_nonce( 'add-attribute-tv_show' ),
                    'save_attributes_tv_show_nonce' => wp_create_nonce( 'save-attributes-tv_show' ),
                    'search_tv_shows_nonce'         => wp_create_nonce( 'search-tv_shows' ),
                    'add_attribute_video_nonce'     => wp_create_nonce( 'add-attribute-video' ),
                    'save_attributes_video_nonce'   => wp_create_nonce( 'save-attributes-video' ),
                    'search_videos_nonce'           => wp_create_nonce( 'search-videos' ),
                    'add_person_movie_nonce'        => wp_create_nonce( 'add-person-movie' ),
                    'save_persons_movie_nonce'      => wp_create_nonce( 'save-persons-movie' ),
                    'add_source_movie_nonce'        => wp_create_nonce( 'add-source-movie' ),
                    'save_sources_movie_nonce'      => wp_create_nonce( 'save-sources-movie' ),
                    'add_attribute_movie_nonce'     => wp_create_nonce( 'add-attribute-movie' ),
                    'save_attributes_movie_nonce'   => wp_create_nonce( 'save-attributes-movie' ),
                    'search_movies_nonce'           => wp_create_nonce( 'search-movies' ),
                    'post_id'                       => isset( $post->ID ) ? $post->ID : '',
                );

                wp_localize_script( 'masvideos-admin-meta-boxes', 'masvideos_admin_meta_boxes', $params );
            }
        }

        public function block_editor_assets() {
            wp_enqueue_style( 'masvideos-editor-block-styles', MasVideos()->plugin_url() . '/assets/css/post-selector.css', false, MASVIDEOS_VERSION, 'all' );
            wp_style_add_data( 'masvideos-editor-block-styles', 'rtl', 'replace' );
        }
    }

endif;

return new MasVideos_Admin_Assets();
