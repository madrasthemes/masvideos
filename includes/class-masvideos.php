<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! class_exists( 'MasVideos' ) ) {
    /**
     * Main plugin class
     *
     * @class MasVideos
     * @version 1.0.0
     */
    final class MasVideos {
        /**
         * Version
         *
         * @var string
         */
        public $version = '0.0.1';

        /**
         * The single instance of the class.
         *
         * @var MasVideos
         */
        protected static $_instance = null;

        /**
         * Query instance.
         *
         * @var MasVideos_Videos_Query
         */
        public $video_query = null;

        /**
         * Query instance.
         *
         * @var MasVideos_Movies_Query
         */
        public $movie_query = null;

        /**
         * Video factory instance.
         *
         * @var MasVideos_Video_Factory
         */
        public $video_factory = null;

        /**
         * Movie factory instance.
         *
         * @var MasVideos_Movie_Factory
         */
        public $movie_factory = null;

        /**
         * Main MasVideos Instance.
         *
         * Ensures only one instance of MasVideos is loaded or can be loaded.
         *
         * @static
         * @return MasVideos - Main instance.
         */
        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        /**
         * Cloning is forbidden.
         */
        public function __clone() {
            _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'masvideos' ), '1.0.0' );
        }

        /**
         * Unserializing instances of this class is forbidden.
         */
        public function __wakeup() {
            _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'masvideos' ), '1.0.0' );
        }

        /**
         * MasVideos Constructor.
         */
        public function __construct() {
            $this->define_constants();
            $this->includes();
            $this->init_hooks();

            do_action( 'masvideos_loaded' );
        }

        /**
         * Define constants
         */
        private function define_constants() {
            $this->define( 'MASVIDEOS_ABSPATH', dirname( MASVIDEOS_PLUGIN_FILE ) . '/' );
            $this->define( 'MASVIDEOS_PLUGIN_BASENAME', plugin_basename( MASVIDEOS_PLUGIN_FILE ) );
            $this->define( 'MASVIDEOS_VERSION', $this->version );
            $this->define( 'MASVIDEOS_DELIMITER', '|' );
            $this->define( 'MASVIDEOS_TEMPLATE_DEBUG_MODE', false );
        }

        /**
         * Init MasVideos when Wordpress Initializes
         */
        public function includes() {
            /**
             * Class autoloader.
             */
            include_once MASVIDEOS_ABSPATH . 'includes/class-masvideos-autoloader.php';

            /**
             * Interfaces.
             */
            include_once MASVIDEOS_ABSPATH . 'includes/interfaces/class-masvideos-object-data-store-interface.php';
            include_once MASVIDEOS_ABSPATH . 'includes/interfaces/class-masvideos-video-data-store-interface.php';
            include_once MASVIDEOS_ABSPATH . 'includes/interfaces/class-masvideos-movie-data-store-interface.php';

            /**
             * Abstract classes.
             */
            include_once MASVIDEOS_ABSPATH . 'includes/abstracts/abstract-masvideos-data.php';
            include_once MASVIDEOS_ABSPATH . 'includes/abstracts/abstract-masvideos-object-query.php';
            include_once MASVIDEOS_ABSPATH . 'includes/abstracts/abstract-masvideos-video.php';
            include_once MASVIDEOS_ABSPATH . 'includes/abstracts/abstract-masvideos-movie.php';

            /**
             * Core classes.
             */
            include_once MASVIDEOS_ABSPATH . 'includes/masvideos-core-functions.php';
            include_once MASVIDEOS_ABSPATH . 'includes/class-masvideos-datetime.php';
            include_once MASVIDEOS_ABSPATH . 'includes/class-masvideos-post-types.php';
            include_once MASVIDEOS_ABSPATH . 'includes/class-masvideos-install.php';
            include_once MASVIDEOS_ABSPATH . 'includes/class-masvideos-ajax.php';
            include_once MASVIDEOS_ABSPATH . 'includes/class-masvideos-comments.php';
            include_once MASVIDEOS_ABSPATH . 'includes/class-masvideos-query.php';
            include_once MASVIDEOS_ABSPATH . 'includes/class-masvideos-video-factory.php';
            include_once MASVIDEOS_ABSPATH . 'includes/class-masvideos-video-query.php';
            include_once MASVIDEOS_ABSPATH . 'includes/class-masvideos-movie-factory.php';
            include_once MASVIDEOS_ABSPATH . 'includes/class-masvideos-movie-query.php';
            include_once MASVIDEOS_ABSPATH . 'includes/class-masvideos-shortcodes.php';
            include_once MASVIDEOS_ABSPATH . 'includes/class-masvideos-gutenberg-blocks.php';
            include_once MASVIDEOS_ABSPATH . 'includes/class-masvideos-structured-data.php';

            /**
             * Data stores - used to store and retrieve CRUD object data from the database.
             */
            include_once MASVIDEOS_ABSPATH . 'includes/class-masvideos-data-store.php';
            include_once MASVIDEOS_ABSPATH . 'includes/data-stores/class-masvideos-data-store-wp.php';
            include_once MASVIDEOS_ABSPATH . 'includes/data-stores/class-masvideos-video-data-store-cpt.php';
            include_once MASVIDEOS_ABSPATH . 'includes/data-stores/class-masvideos-movie-data-store-cpt.php';

            if ( $this->is_request( 'admin' ) ) {
                include_once MASVIDEOS_ABSPATH . 'includes/admin/class-masvideos-admin.php';
            }

            if ( $this->is_request( 'frontend' ) ) {
                $this->frontend_includes();
            }

            $this->video_query = new MasVideos_Videos_Query();
            $this->movie_query = new MasVideos_Movies_Query();
        }

        /**
         * Include required frontend files.
         */
        public function frontend_includes() {
            include_once MASVIDEOS_ABSPATH . 'includes/masvideos-template-hooks.php';
            include_once MASVIDEOS_ABSPATH . 'includes/class-masvideos-template-loader.php';
            include_once MASVIDEOS_ABSPATH . 'includes/class-masvideos-frontend-scripts.php';
        }

        /**
         * Function used to Init WooCommerce Template Functions - This makes them pluggable by plugins and themes.
         */
        public function include_template_functions() {
            include_once MASVIDEOS_ABSPATH . 'includes/masvideos-template-functions.php';
        }

        /**
         * Init MasVideos when Wordpress Initializes
         */
        public function init_hooks() {
            register_activation_hook( MASVIDEOS_PLUGIN_FILE, array( 'MasVideos_Install', 'install' ) );
            add_action( 'after_setup_theme', array( $this, 'include_template_functions' ), 11 );
            add_action( 'init', array( $this, 'init' ), 0 );
            add_action( 'init', array( 'MasVideos_Shortcodes', 'init' ) );
            add_action( 'init', array( 'MasVideos_Gutenberg_Blocks', 'init' ) );
            add_action( 'init', array( $this, 'add_image_sizes' ) );
        }

        /**
         * Init WooCommerce when WordPress Initialises.
         */
        public function init() {
            // Before init action.
            do_action( 'before_masvideos_init' );

            // Set up localisation.
            $this->load_plugin_textdomain();

            // Load class instances.
            $this->video_factory                     = new MasVideos_Video_Factory();
            $this->movie_factory                     = new MasVideos_Movie_Factory();
            // $this->structured_data                     = new WC_Structured_Data();

            // Classes/actions loaded for the frontend and for ajax requests.
            // if ( $this->is_request( 'frontend' ) ) {
            // }

            // Init action.
            do_action( 'masvideos_init' );
        }

        /**
         * Load Localisation files.
         *
         * Note: the first-loaded translation file overrides any following ones if the same translation is present.
         *
         * Locales found in:
         *      - WP_LANG_DIR/masvideos/masvideos-LOCALE.mo
         *      - WP_LANG_DIR/plugins/masvideos-LOCALE.mo
         */
        public function load_plugin_textdomain() {
            $locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
            $locale = apply_filters( 'plugin_locale', $locale, 'masvideos' );

            unload_textdomain( 'masvideos' );
            load_textdomain( 'masvideos', WP_LANG_DIR . '/masvideos/masvideos-' . $locale . '.mo' );
            load_plugin_textdomain( 'masvideos', false, plugin_basename( dirname( MASVIDEOS_PLUGIN_FILE ) ) . '/i18n/languages' );
        }

        /**
         * Add Image sizes to WP.
         *
         * Image sizes can be registered via themes using add_theme_support for masvideos
         * and defining an array of args. If these are not defined, we will use defaults. This is
         * handled in masvideos_get_image_size function.
         *
         * @since 1.0.0
         */
        public function add_image_sizes() {
            $video_large            = masvideos_get_image_size( 'video_large' );
            $video_medium           = masvideos_get_image_size( 'video_medium' );
            $video_thumbnail        = masvideos_get_image_size( 'video_thumbnail' );

            $movie_large            = masvideos_get_image_size( 'movie_large' );
            $movie_medium           = masvideos_get_image_size( 'movie_medium' );
            $movie_thumbnail        = masvideos_get_image_size( 'movie_thumbnail' );

            add_image_size( 'masvideos_video_large', $video_large['width'], $video_large['height'], $video_large['crop'] );
            add_image_size( 'masvideos_video_medium', $video_medium['width'], $video_medium['height'], $video_medium['crop'] );
            add_image_size( 'masvideos_video_thumbnail', $video_thumbnail['width'], $video_thumbnail['height'], $video_thumbnail['crop'] );

            add_image_size( 'masvideos_movie_large', $movie_large['width'], $movie_large['height'], $movie_large['crop'] );
            add_image_size( 'masvideos_movie_medium', $movie_medium['width'], $movie_medium['height'], $movie_medium['crop'] );
            add_image_size( 'masvideos_movie_thumbnail', $movie_thumbnail['width'], $movie_thumbnail['height'], $movie_thumbnail['crop'] );
        }

        /**
         * Get the plugin url.
         * @return string
         */
        public function plugin_url() {
            return untrailingslashit( plugins_url( '/', MASVIDEOS_PLUGIN_FILE ) );
        }

        /**
         * Get the plugin path.
         * @return string
         */
        public function plugin_path() {
            return untrailingslashit( plugin_dir_path( MASVIDEOS_PLUGIN_FILE ) );
        }

        /**
         * Get the template path.
         * @return string
         */
        public function template_path() {
            return apply_filters( 'masvideos_template_path', 'masvideos/' );
        }

        /**
         * Get Ajax URL.
         * @return string
         */
        public function ajax_url() {
            return admin_url( 'admin-ajax.php', 'relative' );
        }

        /**
         * Define constant if not already set.
         *
         * @param  string $name
         * @param  string|bool $value
         */
        private function define( $name, $value ) {
            if ( ! defined( $name ) ) {
                define( $name, $value );
            }
        }

        /**
         * What type of request is this?
         *
         * @param  string $type admin, ajax, cron or frontend.
         * @return bool
         */
        private function is_request( $type ) {
            switch ( $type ) {
                case 'admin':
                    return is_admin();
                case 'ajax':
                    return defined( 'DOING_AJAX' );
                case 'cron':
                    return defined( 'DOING_CRON' );
                case 'frontend':
                    return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
            }
        }
    }
}