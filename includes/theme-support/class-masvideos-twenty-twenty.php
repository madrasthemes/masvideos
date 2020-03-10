<?php
/**
 * Twenty Twenty support.
 *
 * @since   1.0.0
 * @package MasVideos/Classes
 */

defined( 'ABSPATH' ) || exit;

/**
 * MasVideos_Twenty_Twenty class.
 */
class MasVideos_Twenty_Twenty {

    /**
     * Theme init.
     */
    public static function init() {

        // Change MasVideos wrappers.
        remove_action( 'masvideos_before_main_content', 'masvideos_template_loop_content_area_open', 10 );
        remove_action( 'masvideos_after_main_content', 'masvideos_template_loop_content_area_close', 999 );

        add_action( 'masvideos_before_main_content', array( __CLASS__, 'output_content_wrapper' ), 10 );
        add_action( 'masvideos_after_main_content', array( __CLASS__, 'output_content_wrapper_end' ), 999 );

        // This theme doesn't have a traditional sidebar.
        remove_action( 'masvideos_sidebar', 'masvideos_get_sidebar', 10 );

        // Enqueue theme compatibility styles.
        add_filter( 'masvideos_enqueue_styles', array( __CLASS__, 'enqueue_styles' ) );

        // Register theme features.
        add_theme_support( 'masvideos', array(
            'image_sizes'   => array(
                'video_large'       => array(
                    'width'     => 640,
                    'height'    => 480,
                    'crop'      => 1,
                ),
                'video_medium'      => array(
                    'width'     => 480,
                    'height'    => 360,
                    'crop'      => 1,
                ),
                'video_thumbnail'   => array(
                    'width'     => 120,
                    'height'    => 90,
                    'crop'      => 1,
                ),
                'movie_large'       => array(
                    'width'     => 600,
                    'height'    => 900,
                    'crop'      => 1,
                ),
                'movie_medium'      => array(
                    'width'     => 300,
                    'height'    => 450,
                    'crop'      => 1,
                ),
                'movie_thumbnail'   => array(
                    'width'     => 150,
                    'height'    => 225,
                    'crop'      => 1,
                )
            ),
        ) );

        add_theme_support( 'masvideos-movie-gallery-lightbox' );
        add_theme_support( 'masvideos-video-gallery-lightbox' );

        // Background color change.
        add_action( 'after_setup_theme', array( __CLASS__, 'set_white_background' ), 10 );

    }

    /**
     * Open the Twenty Twenty wrapper.
     */
    public static function output_content_wrapper() {
        echo '<section id="primary" class="content-area">';
        echo '<main id="main" class="site-main">';
    }

    /**
     * Close the Twenty Twenty wrapper.
     */
    public static function output_content_wrapper_end() {
        echo '</main>';
        echo '</section>';
    }

    /**
     * Set background color to white if it's default, otherwise don't touch it.
     */
    public static function set_white_background() {
        $background         = sanitize_hex_color_no_hash( get_theme_mod( 'background_color' ) );
        $background_default = 'f5efe0';

        // Don't change user's choice of background color.
        if ( ! empty( $background ) && $background !== $background_default ) {
            return;
        }

        // In case default background is found, change it to white.
        set_theme_mod( 'background_color', 'fff' );
    }

    /**
     * Enqueue CSS for this theme.
     *
     * @param  array $styles Array of registered styles.
     * @return array
     */
    public static function enqueue_styles( $styles ) {
        unset( $styles['masvideos-general'] );

        $styles['masvideos-general'] = array(
            'src'     => str_replace( array( 'http:', 'https:' ), '', MasVideos()->plugin_url() ) . '/assets/css/twenty-twenty.css',
            'deps'    => '',
            'version' => MASVIDEOS_VERSION,
            'media'   => 'all',
            'has_rtl' => true,
        );

        return apply_filters( 'masvideos_twenty_twenty_styles', $styles );
    }

}

MasVideos_Twenty_Twenty::init();