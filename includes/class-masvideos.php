<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Mas_Videos' ) ) {
    /**
     * Main plugin class
     *
     * @class Mas_Videos
     * @version 1.0.0
     */
    final class Mas_Videos {
        /**
         * Version
         *
         * @var string
         */
        public $version = '0.0.1';

        /**
         * The single instance of the class.
         *
         * @var Mas_Videos
         */
        protected static $_instance = null;

        /**
         * Main Mas_Videos Instance.
         *
         * Ensures only one instance of Mas_Videos is loaded or can be loaded.
         *
         * @static
         * @return Mas_Videos - Main instance.
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
         * Mas_Videos Constructor.
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
            $this->define( 'MAS_VIDEOS_ABSPATH', dirname( MAS_VIDEOS_PLUGIN_FILE ) . '/' );
            $this->define( 'MAS_VIDEOS_PLUGIN_BASENAME', plugin_basename( MAS_VIDEOS_PLUGIN_FILE ) );
            $this->define( 'MAS_VIDEOS_VERSION', $this->version );
            $this->define( 'MAS_VIDEOS_TEMPLATE_DEBUG_MODE', false );
        }

        /**
         * Init Mas_Videos when Wordpress Initializes
         */
        public function includes() {
            /**
             * Core classes.
             */
            include_once MAS_VIDEOS_ABSPATH . 'includes/masvideos-core-functions.php';
            include_once MAS_VIDEOS_ABSPATH . 'includes/class-masvideos-post-types.php';
            include_once MAS_VIDEOS_ABSPATH . 'includes/class-masvideos-install.php';

            if ( $this->is_request( 'admin' ) ) {
                include_once MAS_VIDEOS_ABSPATH . 'includes/admin/class-masvideos-admin.php';
            }

            if ( $this->is_request( 'frontend' ) ) {
                $this->frontend_includes();
            }
        }

        /**
         * Include required frontend files.
         */
        public function frontend_includes() {
            include_once MAS_VIDEOS_ABSPATH . 'includes/masvideos-template-hooks.php';
            include_once MAS_VIDEOS_ABSPATH . 'includes/class-masvideos-template-loader.php';
        }

        /**
         * Function used to Init WooCommerce Template Functions - This makes them pluggable by plugins and themes.
         */
        public function include_template_functions() {
            include_once MAS_VIDEOS_ABSPATH . 'includes/masvideos-template-functions.php';
        }

        /**
         * Init Mas_Videos when Wordpress Initializes
         */
        public function init_hooks() {
            register_activation_hook( MAS_VIDEOS_PLUGIN_FILE, array( 'Mas_Videos_Install', 'install' ) );
            add_action( 'after_setup_theme', array( $this, 'include_template_functions' ), 11 );
            add_action( 'init', array( $this, 'init' ), 0 );
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
            // $this->product_factory                     = new WC_Product_Factory();
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
            load_plugin_textdomain( 'masvideos', false, plugin_basename( dirname( MAS_VIDEOS_PLUGIN_FILE ) ) . '/i18n/languages' );
        }

        /**
         * Get the plugin url.
         * @return string
         */
        public function plugin_url() {
            return untrailingslashit( plugins_url( '/', MAS_VIDEOS_PLUGIN_FILE ) );
        }

        /**
         * Get the plugin path.
         * @return string
         */
        public function plugin_path() {
            return untrailingslashit( plugin_dir_path( MAS_VIDEOS_PLUGIN_FILE ) );
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