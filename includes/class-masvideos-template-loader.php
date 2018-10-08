<?php
/**
 * Template Loader
 *
 * @package Masvideos/Classes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Template loader class.
 */
class Mas_Videos_Template_Loader {

    /**
     * Store the videos page ID.
     *
     * @var integer
     */
    private static $videos_page_id = 0;

    /**
     * Store the movies page ID.
     *
     * @var integer
     */
    private static $movies_page_id = 0;

    /**
     * Is WooCommerce support defined?
     *
     * @var boolean
     */
    private static $theme_support = false;

    /**
     * Hook in methods.
     */
    public static function init() {
        self::$theme_support = current_theme_supports( 'masvideos' );
        self::$videos_page_id  = masvideos_get_page_id( 'videos' );
        self::$movies_page_id  = masvideos_get_page_id( 'movies' );

        // Supported themes.
        if ( self::$theme_support ) {
            add_filter( 'template_include', array( __CLASS__, 'template_loader' ) );
        }
    }

    /**
     * Load a template.
     *
     * Handles template usage so that we can use our own templates instead of the themes.
     *
     * Templates are in the 'templates' folder. masvideos looks for theme.
     * overrides in /theme/masvideos/ by default.
     *
     * For beginners, it also looks for a masvideos.php template first. If the user adds.
     * this to the theme (containing a masvideos() inside) this will be used for all.
     * masvideos templates.
     *
     * @param string $template Template to load.
     * @return string
     */
    public static function template_loader( $template ) {
        if ( is_embed() ) {
            return $template;
        }

        $default_file = self::get_template_loader_default_file();

        if ( $default_file ) {
            /**
             * Filter hook to choose which files to find before WooCommerce does it's own logic.
             *
             * @since 3.0.0
             * @var array
             */
            $search_files = self::get_template_loader_files( $default_file );
            $template     = locate_template( $search_files );

            if ( ! $template || MAS_VIDEOS_TEMPLATE_DEBUG_MODE ) {
                $template = Mas_Videos()->plugin_path() . '/templates/' . $default_file;
            }
        }

        return $template;
    }

    /**
     * Get the default filename for a template.
     *
     * @since  3.0.0
     * @return string
     */
    private static function get_template_loader_default_file() {
        if ( is_singular( 'video' ) ) {
            $default_file = 'single-video.php';
        } elseif ( is_video_taxonomy() ) {
            $object = get_queried_object();

            if ( is_tax( 'video_cat' ) || is_tax( 'video_tag' ) ) {
                $default_file = 'taxonomy-' . $object->taxonomy . '.php';
            } else {
                $default_file = 'archive-video.php';
            }
        } elseif ( is_post_type_archive( 'video' ) || is_page( masvideos_get_page_id( 'videos' ) ) ) {
            $default_file = self::$theme_support ? 'archive-video.php' : '';
        } elseif ( is_singular( 'movie' ) ) {
            $default_file = 'single-movie.php';
        } elseif ( is_movie_taxonomy() ) {
            $object = get_queried_object();

            if ( is_tax( 'movie_cat' ) || is_tax( 'movie_tag' ) ) {
                $default_file = 'taxonomy-' . $object->taxonomy . '.php';
            } else {
                $default_file = 'archive-movie.php';
            }
        } elseif ( is_post_type_archive( 'movie' ) || is_page( masmovies_get_page_id( 'movies' ) ) ) {
            $default_file = self::$theme_support ? 'archive-movie.php' : '';
        } else {
            $default_file = '';
        }
        return $default_file;
    }

    /**
     * Get an array of filenames to search for a given template.
     *
     * @since  3.0.0
     * @param  string $default_file The default file name.
     * @return string[]
     */
    private static function get_template_loader_files( $default_file ) {
        $templates   = apply_filters( 'masvideos_template_loader_files', array(), $default_file );
        $templates[] = 'masvideos.php';

        if ( is_page_template() ) {
            $templates[] = get_page_template_slug();
        }

        if ( is_singular( 'video' ) ) {
            $object       = get_queried_object();
            $name_decoded = urldecode( $object->post_name );
            if ( $name_decoded !== $object->post_name ) {
                $templates[] = "single-video-{$name_decoded}.php";
            }
            $templates[] = "single-video-{$object->post_name}.php";
        }

        if ( is_video_taxonomy() ) {
            $object      = get_queried_object();
            $templates[] = 'taxonomy-' . $object->taxonomy . '-' . $object->slug . '.php';
            $templates[] = Mas_Videos()->template_path() . 'taxonomy-' . $object->taxonomy . '-' . $object->slug . '.php';
            $templates[] = 'taxonomy-' . $object->taxonomy . '.php';
            $templates[] = Mas_Videos()->template_path() . 'taxonomy-' . $object->taxonomy . '.php';
        }

        if ( is_singular( 'movie' ) ) {
            $object       = get_queried_object();
            $name_decoded = urldecode( $object->post_name );
            if ( $name_decoded !== $object->post_name ) {
                $templates[] = "single-movie-{$name_decoded}.php";
            }
            $templates[] = "single-movie-{$object->post_name}.php";
        }

        if ( is_movie_taxonomy() ) {
            $object      = get_queried_object();
            $templates[] = 'taxonomy-' . $object->taxonomy . '-' . $object->slug . '.php';
            $templates[] = Mas_Videos()->template_path() . 'taxonomy-' . $object->taxonomy . '-' . $object->slug . '.php';
            $templates[] = 'taxonomy-' . $object->taxonomy . '.php';
            $templates[] = Mas_Videos()->template_path() . 'taxonomy-' . $object->taxonomy . '.php';
        }

        $templates[] = $default_file;
        $templates[] = Mas_Videos()->template_path() . $default_file;

        return array_unique( $templates );
    }
}

add_action( 'init', array( 'Mas_Videos_Template_Loader', 'init' ) );
