<?php
/**
 * Handle frontend scripts
 *
 * @package MasVideos/Classes
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Frontend scripts class.
 */
class MasVideos_Frontend_Scripts {

    /**
     * Contains an array of script handles registered by MasVideos.
     *
     * @var array
     */
    private static $scripts = array();

    /**
     * Contains an array of script handles registered by MasVideos.
     *
     * @var array
     */
    private static $styles = array();

    /**
     * Contains an array of script handles localized by MasVideos.
     *
     * @var array
     */
    private static $wp_localize_scripts = array();

    /**
     * Hook in methods.
     */
    public static function init() {
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_scripts' ) );
        add_action( 'wp_print_scripts', array( __CLASS__, 'localize_printed_scripts' ), 5 );
        add_action( 'wp_print_footer_scripts', array( __CLASS__, 'localize_printed_scripts' ), 5 );
    }

    /**
     * Get styles for the frontend.
     *
     * @return array
     */
    public static function get_styles() {
        return apply_filters(
            'masvideos_enqueue_styles', array(
                'masvideos-general'     => array(
                    'src'     => self::get_asset_url( 'assets/css/masvideos.css' ),
                    'deps'    => '',
                    'version' => MASVIDEOS_VERSION,
                    'media'   => 'all',
                    'has_rtl' => true,
                ),
            )
        );
    }

    /**
     * Return asset URL.
     *
     * @param string $path Assets path.
     * @return string
     */
    private static function get_asset_url( $path ) {
        return apply_filters( 'masvideos_get_asset_url', plugins_url( $path, MASVIDEOS_PLUGIN_FILE ), $path );
    }

    /**
     * Register a script for use.
     *
     * @uses   wp_register_script()
     * @param  string   $handle    Name of the script. Should be unique.
     * @param  string   $path      Full URL of the script, or path of the script relative to the WordPress root directory.
     * @param  string[] $deps      An array of registered script handles this script depends on.
     * @param  string   $version   String specifying script version number, if it has one, which is added to the URL as a query string for cache busting purposes. If version is set to false, a version number is automatically added equal to current installed WordPress version. If set to null, no version is added.
     * @param  boolean  $in_footer Whether to enqueue the script before </body> instead of in the <head>. Default 'false'.
     */
    private static function register_script( $handle, $path, $deps = array( 'jquery' ), $version = MASVIDEOS_VERSION, $in_footer = true ) {
        self::$scripts[] = $handle;
        wp_register_script( $handle, $path, $deps, $version, $in_footer );
    }

    /**
     * Register and enqueue a script for use.
     *
     * @uses   wp_enqueue_script()
     * @param  string   $handle    Name of the script. Should be unique.
     * @param  string   $path      Full URL of the script, or path of the script relative to the WordPress root directory.
     * @param  string[] $deps      An array of registered script handles this script depends on.
     * @param  string   $version   String specifying script version number, if it has one, which is added to the URL as a query string for cache busting purposes. If version is set to false, a version number is automatically added equal to current installed WordPress version. If set to null, no version is added.
     * @param  boolean  $in_footer Whether to enqueue the script before </body> instead of in the <head>. Default 'false'.
     */
    private static function enqueue_script( $handle, $path = '', $deps = array( 'jquery' ), $version = MASVIDEOS_VERSION, $in_footer = true ) {
        if ( ! in_array( $handle, self::$scripts, true ) && $path ) {
            self::register_script( $handle, $path, $deps, $version, $in_footer );
        }
        wp_enqueue_script( $handle );
    }

    /**
     * Register a style for use.
     *
     * @uses   wp_register_style()
     * @param  string   $handle  Name of the stylesheet. Should be unique.
     * @param  string   $path    Full URL of the stylesheet, or path of the stylesheet relative to the WordPress root directory.
     * @param  string[] $deps    An array of registered stylesheet handles this stylesheet depends on.
     * @param  string   $version String specifying stylesheet version number, if it has one, which is added to the URL as a query string for cache busting purposes. If version is set to false, a version number is automatically added equal to current installed WordPress version. If set to null, no version is added.
     * @param  string   $media   The media for which this stylesheet has been defined. Accepts media types like 'all', 'print' and 'screen', or media queries like '(orientation: portrait)' and '(max-width: 640px)'.
     * @param  boolean  $has_rtl If has RTL version to load too.
     */
    private static function register_style( $handle, $path, $deps = array(), $version = MASVIDEOS_VERSION, $media = 'all', $has_rtl = false ) {
        self::$styles[] = $handle;
        wp_register_style( $handle, $path, $deps, $version, $media );

        if ( $has_rtl ) {
            wp_style_add_data( $handle, 'rtl', 'replace' );
        }
    }

    /**
     * Register and enqueue a styles for use.
     *
     * @uses   wp_enqueue_style()
     * @param  string   $handle  Name of the stylesheet. Should be unique.
     * @param  string   $path    Full URL of the stylesheet, or path of the stylesheet relative to the WordPress root directory.
     * @param  string[] $deps    An array of registered stylesheet handles this stylesheet depends on.
     * @param  string   $version String specifying stylesheet version number, if it has one, which is added to the URL as a query string for cache busting purposes. If version is set to false, a version number is automatically added equal to current installed WordPress version. If set to null, no version is added.
     * @param  string   $media   The media for which this stylesheet has been defined. Accepts media types like 'all', 'print' and 'screen', or media queries like '(orientation: portrait)' and '(max-width: 640px)'.
     * @param  boolean  $has_rtl If has RTL version to load too.
     */
    private static function enqueue_style( $handle, $path = '', $deps = array(), $version = MASVIDEOS_VERSION, $media = 'all', $has_rtl = false ) {
        if ( ! in_array( $handle, self::$styles, true ) && $path ) {
            self::register_style( $handle, $path, $deps, $version, $media, $has_rtl );
        }
        wp_enqueue_style( $handle );
    }

    /**
     * Register all MasVideos scripts.
     */
    private static function register_scripts() {
        $suffix           = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
        $register_scripts = array(
            'jquery-blockui'                => array(
                'src'     => self::get_asset_url( 'assets/js/jquery-blockui/jquery.blockUI' . $suffix . '.js' ),
                'deps'    => array( 'jquery' ),
                'version' => '2.70',
            ),
            'photoswipe'                    => array(
                'src'     => self::get_asset_url( 'assets/js/photoswipe/photoswipe' . $suffix . '.js' ),
                'deps'    => array(),
                'version' => '4.1.1',
            ),
            'photoswipe-ui-default'         => array(
                'src'     => self::get_asset_url( 'assets/js/photoswipe/photoswipe-ui-default' . $suffix . '.js' ),
                'deps'    => array( 'photoswipe' ),
                'version' => '4.1.1',
            ),
            'select2'                       => array(
                'src'     => self::get_asset_url( 'assets/js/select2/select2.full' . $suffix . '.js' ),
                'deps'    => array( 'jquery' ),
                'version' => '4.0.3',
            ),
            'selectWoo'                     => array(
                'src'     => self::get_asset_url( 'assets/js/selectWoo/selectWoo.full' . $suffix . '.js' ),
                'deps'    => array( 'jquery' ),
                'version' => '1.0.4',
            ),
            'popper'                        => array(
                'src'     => self::get_asset_url( 'assets/js/frontend/popper' . $suffix . '.js' ),
                'deps'    => array( 'jquery' ),
                'version' => MASVIDEOS_VERSION,
            ),
            'bootstrap-util'                => array(
                'src'     => self::get_asset_url( 'assets/js/frontend/bootstrap-util' . $suffix . '.js' ),
                'deps'    => array( 'jquery', 'popper' ),
                'version' => MASVIDEOS_VERSION,
            ),
            'bootstrap-tab'                 => array(
                'src'     => self::get_asset_url( 'assets/js/frontend/bootstrap-tab' . $suffix . '.js' ),
                'deps'    => array( 'jquery', 'popper', 'bootstrap-util' ),
                'version' => MASVIDEOS_VERSION,
            ),
            'bootstrap-dropdown'            => array(
                'src'     => self::get_asset_url( 'assets/js/frontend/bootstrap-dropdown' . $suffix . '.js' ),
                'deps'    => array( 'jquery', 'popper', 'bootstrap-util' ),
                'version' => MASVIDEOS_VERSION,
            ),
            'masvideos-single-episode'      => array(
                'src'     => self::get_asset_url( 'assets/js/frontend/single-episode' . $suffix . '.js' ),
                'deps'    => array( 'jquery' ),
                'version' => MASVIDEOS_VERSION,
            ),
            'masvideos-single-tv-show'      => array(
                'src'     => self::get_asset_url( 'assets/js/frontend/single-tv-show' . $suffix . '.js' ),
                'deps'    => array( 'jquery' ),
                'version' => MASVIDEOS_VERSION,
            ),
            'masvideos-single-video'        => array(
                'src'     => self::get_asset_url( 'assets/js/frontend/single-video' . $suffix . '.js' ),
                'deps'    => array( 'jquery' ),
                'version' => MASVIDEOS_VERSION,
            ),
            'masvideos-single-movie'        => array(
                'src'     => self::get_asset_url( 'assets/js/frontend/single-movie' . $suffix . '.js' ),
                'deps'    => array( 'jquery' ),
                'version' => MASVIDEOS_VERSION,
            ),
            'masvideos-playlist-tv-show'    => array(
                'src'     => self::get_asset_url( 'assets/js/frontend/playlist-tv-show' . $suffix . '.js' ),
                'deps'    => array( 'jquery' ),
                'version' => MASVIDEOS_VERSION,
            ),
            'masvideos-playlist-video'      => array(
                'src'     => self::get_asset_url( 'assets/js/frontend/playlist-video' . $suffix . '.js' ),
                'deps'    => array( 'jquery' ),
                'version' => MASVIDEOS_VERSION,
            ),
            'masvideos-playlist-movie'      => array(
                'src'     => self::get_asset_url( 'assets/js/frontend/playlist-movie' . $suffix . '.js' ),
                'deps'    => array( 'jquery' ),
                'version' => MASVIDEOS_VERSION,
            ),
            'masvideos-gallery-flip'        => array(
                'src'     => self::get_asset_url( 'assets/js/frontend/gallery-flip' . $suffix . '.js' ),
                'deps'    => array( 'jquery' ),
                'version' => MASVIDEOS_VERSION,
            ),
            'masvideos-edit-video'          => array(
                'src'     => self::get_asset_url( 'assets/js/frontend/edit-video' . $suffix . '.js' ),
                'deps'    => array( 'jquery' ),
                'version' => MASVIDEOS_VERSION,
            ),
        );
        foreach ( $register_scripts as $name => $props ) {
            self::register_script( $name, $props['src'], $props['deps'], $props['version'] );
        }
    }

    /**
     * Register all MasVideos sty;es.
     */
    private static function register_styles() {
        $register_styles = array(
            'photoswipe'                => array(
                'src'     => self::get_asset_url( 'assets/css/photoswipe/photoswipe.css' ),
                'deps'    => array(),
                'version' => MASVIDEOS_VERSION,
                'has_rtl' => false,
            ),
            'photoswipe-default-skin'   => array(
                'src'     => self::get_asset_url( 'assets/css/photoswipe/default-skin/default-skin.css' ),
                'deps'    => array( 'photoswipe' ),
                'version' => MASVIDEOS_VERSION,
                'has_rtl' => false,
            ),
            'select2'                   => array(
                'src'     => self::get_asset_url( 'assets/css/select2.css' ),
                'deps'    => array(),
                'version' => MASVIDEOS_VERSION,
                'has_rtl' => false,
            ),
        );
        foreach ( $register_styles as $name => $props ) {
            self::register_style( $name, $props['src'], $props['deps'], $props['version'], 'all', $props['has_rtl'] );
        }
    }

    /**
     * Register/queue frontend scripts.
     */
    public static function load_scripts() {
        global $post;

        if ( ! did_action( 'before_masvideos_init' ) ) {
            return;
        }

        self::register_scripts();
        self::register_styles();

        // Load single pages only if supported.
        if ( is_episode() ) {
            self::enqueue_script( 'masvideos-single-episode' );
        }

        if ( is_tv_show() ) {
            self::enqueue_script( 'masvideos-single-tv-show' );
        }

        if ( is_video() ) {
            self::enqueue_script( 'masvideos-single-video' );
        }

        if ( is_movie() ) {
            self::enqueue_script( 'masvideos-single-movie' );
        }

        // Global frontend scripts.
        if ( apply_filters( 'masvideos_enqueue_bootstrap_js', true ) ) {
            self::enqueue_script( 'popper' );
            self::enqueue_script( 'bootstrap-util' );
            self::enqueue_script( 'bootstrap-tab' );
            self::enqueue_script( 'bootstrap-dropdown' );
        }

        self::enqueue_script( 'masvideos-playlist-tv-show' );
        self::enqueue_script( 'masvideos-playlist-video' );
        self::enqueue_script( 'masvideos-playlist-movie' );

        // Enable Gallery Flip.
        if ( apply_filters( 'masvideos_enqueue_gallery_flip_js', true ) ) {
            self::enqueue_script( 'masvideos-gallery-flip' );
        }

        if( function_exists( 'masvideos_is_video_upload_page' ) && masvideos_is_video_upload_page() ) {
            wp_enqueue_media();
            self::enqueue_script( 'selectWoo' );
            self::enqueue_style( 'select2' );
            self::enqueue_script( 'masvideos-edit-video' );
        }

        if ( current_theme_supports( 'masvideos-movie-gallery-lightbox' ) || current_theme_supports( 'masvideos-video-gallery-lightbox' ) ) {
            self::enqueue_script( 'photoswipe-ui-default' );
            self::enqueue_style( 'photoswipe-default-skin' );
            add_action( 'wp_footer', 'masvideos_photoswipe' );
        }

        // CSS Styles.
        $enqueue_styles = self::get_styles();
        if ( $enqueue_styles ) {
            foreach ( $enqueue_styles as $handle => $args ) {
                if ( ! isset( $args['has_rtl'] ) ) {
                    $args['has_rtl'] = false;
                }

                self::enqueue_style( $handle, $args['src'], $args['deps'], $args['version'], $args['media'], $args['has_rtl'] );
            }
        }

        // Placeholder style.
        wp_register_style( 'masvideos-inline', false );
        wp_enqueue_style( 'masvideos-inline' );
    }

    /**
     * Localize a MasVideos script once.
     *
     * @since 1.0.0 this needs less wp_script_is() calls due to https://core.trac.wordpress.org/ticket/28404 being added in WP 4.0.
     * @param string $handle Script handle the data will be attached to.
     */
    private static function localize_script( $handle ) {
        if ( ! in_array( $handle, self::$wp_localize_scripts, true ) && wp_script_is( $handle ) ) {
            $data = self::get_script_data( $handle );

            if ( ! $data ) {
                return;
            }

            $name                        = str_replace( '-', '_', $handle ) . '_params';
            self::$wp_localize_scripts[] = $handle;
            wp_localize_script( $handle, $name, apply_filters( $name, $data ) );
        }
    }

    /**
     * Return data for script handles.
     *
     * @param  string $handle Script handle the data will be attached to.
     * @return array|bool
     */
    private static function get_script_data( $handle ) {
        global $wp;

        switch ( $handle ) {
            case 'masvideos':
                $params = array(
                    'ajax_url'                  => MasVideos()->ajax_url(),
                    'masvideos_ajax_url'        => MasVideos_AJAX::get_endpoint( '%%endpoint%%' ),
                );
                break;
            case 'masvideos-single-episode':
                $params = array(
                    'i18n_required_rating_text' => esc_attr__( 'Please select a rating', 'masvideos' ),
                    'review_rating_required'    => get_option( 'masvideos_episode_review_rating_required' ),
                );
                break;
            case 'masvideos-single-tv-show':
                $params = array(
                    'i18n_required_rating_text' => esc_attr__( 'Please select a rating', 'masvideos' ),
                    'review_rating_required'    => get_option( 'masvideos_tv_show_review_rating_required' ),
                );
                break;
            case 'masvideos-single-video':
                $params = array(
                    'i18n_required_rating_text' => esc_attr__( 'Please select a rating', 'masvideos' ),
                    'review_rating_required'    => get_option( 'masvideos_video_review_rating_required' ),
                    'photoswipe_enabled'        => apply_filters( 'masvideos_single_video_photoswipe_enabled', get_theme_support( 'masvideos-video-gallery-lightbox' ) ),
                    'photoswipe_options'        => apply_filters(
                        'masvideos_single_video_photoswipe_options',
                        array(
                            'shareEl'               => false,
                            'closeOnScroll'         => false,
                            'history'               => false,
                            'hideAnimationDuration' => 0,
                            'showAnimationDuration' => 0,
                        )
                    ),
                );
                break;
            case 'masvideos-single-movie':
                $params = array(
                    'i18n_required_rating_text' => esc_attr__( 'Please select a rating', 'masvideos' ),
                    'review_rating_required'    => get_option( 'masvideos_movie_review_rating_required' ),
                    'photoswipe_enabled'        => apply_filters( 'masvideos_single_movie_photoswipe_enabled', get_theme_support( 'masvideos-movie-gallery-lightbox' ) ),
                    'photoswipe_options'        => apply_filters(
                        'masvideos_single_movie_photoswipe_options',
                        array(
                            'shareEl'               => false,
                            'closeOnScroll'         => false,
                            'history'               => false,
                            'hideAnimationDuration' => 0,
                            'showAnimationDuration' => 0,
                        )
                    ),
                );
                break;
            case 'masvideos-playlist-tv-show':
                $params = array(
                    'ajax_url'                  => MasVideos()->ajax_url(),
                    'masvideos_ajax_url'        => MasVideos_AJAX::get_endpoint( '%%endpoint%%' ),
                );
                break;
            case 'masvideos-playlist-video':
                $params = array(
                    'ajax_url'                  => MasVideos()->ajax_url(),
                    'masvideos_ajax_url'        => MasVideos_AJAX::get_endpoint( '%%endpoint%%' ),
                );
                break;
            case 'masvideos-playlist-movie':
                $params = array(
                    'ajax_url'                  => MasVideos()->ajax_url(),
                    'masvideos_ajax_url'        => MasVideos_AJAX::get_endpoint( '%%endpoint%%' ),
                );
                break;
            default:
                $params = false;
        }

        return apply_filters( 'masvideos_get_script_data', $params, $handle );
    }

    /**
     * Localize scripts only when enqueued.
     */
    public static function localize_printed_scripts() {
        foreach ( self::$scripts as $handle ) {
            self::localize_script( $handle );
        }
    }
}

MasVideos_Frontend_Scripts::init();
