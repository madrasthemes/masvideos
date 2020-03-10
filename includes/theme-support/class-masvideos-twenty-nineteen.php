<?php
/**
 * Twenty Nineteen support.
 *
 * @since   1.0.0
 * @package MasVideos/Classes
 */

defined( 'ABSPATH' ) || exit;

/**
 * MasVideos_Twenty_Nineteen class.
 */
class MasVideos_Twenty_Nineteen {

    /**
     * Theme init.
     */
    public static function init() {

        // Change MasVideos wrappers.
        remove_action( 'masvideos_before_main_content', 'masvideos_template_loop_content_area_open', 10 );
        remove_action( 'masvideos_after_main_content', 'masvideos_template_loop_content_area_close', 999 );

        add_action( 'masvideos_before_main_content', array( __CLASS__, 'output_content_wrapper' ), 10 );
        add_action( 'masvideos_after_main_content', array( __CLASS__, 'output_content_wrapper_end' ), 999 );

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

        // Tweak Twenty Nineteen features.
        add_action( 'wp', array( __CLASS__, 'tweak_theme_features' ) );

        // Color scheme CSS
        add_filter( 'twentynineteen_custom_colors_css', array( __CLASS__, 'custom_colors_css' ), 10, 3 );
    }

    /**
     * Open the Twenty Nineteen wrapper.
     */
    public static function output_content_wrapper() {
        echo '<section id="primary" class="content-area">';
        echo '<main id="main" class="site-main">';
    }

    /**
     * Close the Twenty Nineteen wrapper.
     */
    public static function output_content_wrapper_end() {
        echo '</main>';
        echo '</section>';
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
            'src'     => str_replace( array( 'http:', 'https:' ), '', MasVideos()->plugin_url() ) . '/assets/css/twenty-nineteen.css',
            'deps'    => '',
            'version' => MASVIDEOS_VERSION,
            'media'   => 'all',
            'has_rtl' => true,
        );

        return apply_filters( 'masvideos_twenty_nineteen_styles', $styles );
    }

    /**
     * Tweak Twenty Nineteen features.
     */
    public static function tweak_theme_features() {
        if ( is_masvideos() ) {
            add_filter( 'twentynineteen_can_show_post_thumbnail', '__return_false' );
        }
    }

    /**
     * Filters Twenty Nineteen custom colors CSS.
     *
     * @param string $css           Base theme colors CSS.
     * @param int    $primary_color The user's selected color hue.
     * @param string $saturation    Filtered theme color saturation level.
     */
    public static function custom_colors_css( $css, $primary_color, $saturation ) {
        if ( function_exists( 'register_block_type' ) && is_admin() ) {
            return $css;
        }

        $lightness = absint( apply_filters( 'twentynineteen_custom_colors_lightness', 33 ) );
        $lightness = $lightness . '%';

        $css .= '
            .masvideos-info {
                background-color: hsl( ' . $primary_color . ', ' . $saturation . ', ' . $lightness . ' );
            }

            .masvideos-tabs ul li.active a {
                color: hsl( ' . $primary_color . ', ' . $saturation . ', ' . $lightness . ' );
                box-shadow: 0 2px 0 hsl( ' . $primary_color . ', ' . $saturation . ', ' . $lightness . ' );
            }
        ';

        return $css;
    }
}

MasVideos_Twenty_Nineteen::init();
