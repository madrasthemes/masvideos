<?php
/**
 * Template Loader
 *
 * @package MasVideos/Classes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Template loader class.
 */
class MasVideos_Template_Loader {

    /**
     * The persons page ID.
     *
     * @var integer
     */
    private static $persons_page_id = 0;

    /**
     * The episodes page ID.
     *
     * @var integer
     */
    private static $episodes_page_id = 0;

    /**
     * The tv shows page ID.
     *
     * @var integer
     */
    private static $tv_shows_page_id = 0;

    /**
     * The videos page ID.
     *
     * @var integer
     */
    private static $videos_page_id = 0;

    /**
     * The movies page ID.
     *
     * @var integer
     */
    private static $movies_page_id = 0;

    /**
     * Is MasVideos support defined?
     *
     * @var boolean
     */
    private static $theme_support = false;

    /**
     * Hook in methods.
     */
    public static function init() {
        self::$theme_support = current_theme_supports( 'masvideos' );
        self::$persons_page_id  = masvideos_get_page_id( 'persons' );
        self::$episodes_page_id  = masvideos_get_page_id( 'episodes' );
        self::$tv_shows_page_id  = masvideos_get_page_id( 'tv_shows' );
        self::$videos_page_id  = masvideos_get_page_id( 'videos' );
        self::$movies_page_id  = masvideos_get_page_id( 'movies' );

        // Supported themes.
        if ( self::$theme_support ) {
            add_filter( 'template_include', array( __CLASS__, 'template_loader' ) );
            add_filter( 'comments_template', array( __CLASS__, 'comments_template_loader' ) );
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
             * Filter hook to choose which files to find before MasVideos does it's own logic.
             *
             * @since 1.0.0
             * @var array
             */
            $search_files = self::get_template_loader_files( $default_file );
            $template     = locate_template( $search_files );

            if ( ! $template || MASVIDEOS_TEMPLATE_DEBUG_MODE ) {
                $template = MasVideos()->plugin_path() . '/templates/' . $default_file;
            }
        }

        return $template;
    }

    /**
     * Get the default filename for a template.
     *
     * @since  1.0.0
     * @return string
     */
    private static function get_template_loader_default_file() {
        if ( is_singular( 'person' ) ) {
            $default_file = 'single-person.php';
        } elseif ( is_person_taxonomy() || is_persons() ) {
            $default_file = self::$theme_support ? 'archive-person.php' : '';
        } elseif ( is_singular( 'episode' ) ) {
            $default_file = 'single-episode.php';
        } elseif ( is_episode_taxonomy() || is_episodes() ) {
            $default_file = self::$theme_support ? 'archive-episode.php' : '';
        } elseif ( is_singular( 'tv_show' ) ) {
            $default_file = 'single-tv-show.php';
        } elseif ( is_tv_show_taxonomy() || is_tv_shows() ) {
            $default_file = self::$theme_support ? 'archive-tv-show.php' : '';
        } elseif ( is_singular( 'tv_show_playlist' ) ) {
            $default_file = 'single-tv-show-playlist.php';
        } elseif ( is_singular( 'video' ) ) {
            $default_file = 'single-video.php';
        } elseif ( is_video_taxonomy() || is_videos() ) {
            $default_file = self::$theme_support ? 'archive-video.php' : '';
        } elseif ( is_singular( 'video_playlist' ) ) {
            $default_file = 'single-video-playlist.php';
        } elseif ( is_singular( 'movie' ) ) {
            $default_file = 'single-movie.php';
        } elseif ( is_movie_taxonomy() || is_movies() ) {
            $default_file = self::$theme_support ? 'archive-movie.php' : '';
        } elseif ( is_singular( 'movie_playlist' ) ) {
            $default_file = 'single-movie-playlist.php';
        } else {
            $default_file = '';
        }
        return $default_file;
    }

    /**
     * Get an array of filenames to search for a given template.
     *
     * @since  1.0.0
     * @param  string $default_file The default file name.
     * @return string[]
     */
    private static function get_template_loader_files( $default_file ) {
        $templates   = apply_filters( 'masvideos_template_loader_files', array(), $default_file );
        $templates[] = 'masvideos.php';

        if ( is_page_template() ) {
            $templates[] = get_page_template_slug();
        }

        if ( is_singular( 'person' ) ) {
            $object       = get_queried_object();
            $name_decoded = urldecode( $object->post_name );
            if ( $name_decoded !== $object->post_name ) {
                $templates[] = "single-person-{$name_decoded}.php";
            }
            $templates[] = "single-person-{$object->post_name}.php";
        }

        if ( is_person_taxonomy() ) {
            $object      = get_queried_object();
            $templates[] = 'taxonomy-' . $object->taxonomy . '-' . $object->slug . '.php';
            $templates[] = MasVideos()->template_path() . 'taxonomy-' . $object->taxonomy . '-' . $object->slug . '.php';
            $templates[] = 'taxonomy-' . $object->taxonomy . '.php';
            $templates[] = MasVideos()->template_path() . 'taxonomy-' . $object->taxonomy . '.php';
        }

        if ( is_singular( 'episode' ) ) {
            $object       = get_queried_object();
            $name_decoded = urldecode( $object->post_name );
            if ( $name_decoded !== $object->post_name ) {
                $templates[] = "single-episode-{$name_decoded}.php";
            }
            $templates[] = "single-episode-{$object->post_name}.php";
        }

        if ( is_episode_taxonomy() ) {
            $object      = get_queried_object();
            $templates[] = 'taxonomy-' . $object->taxonomy . '-' . $object->slug . '.php';
            $templates[] = MasVideos()->template_path() . 'taxonomy-' . $object->taxonomy . '-' . $object->slug . '.php';
            $templates[] = 'taxonomy-' . $object->taxonomy . '.php';
            $templates[] = MasVideos()->template_path() . 'taxonomy-' . $object->taxonomy . '.php';
        }

        if ( is_singular( 'tv_show' ) ) {
            $object       = get_queried_object();
            $name_decoded = urldecode( $object->post_name );
            if ( $name_decoded !== $object->post_name ) {
                $templates[] = "single-tv-show-{$name_decoded}.php";
            }
            $templates[] = "single-tv-show-{$object->post_name}.php";
        }

        if ( is_tv_show_taxonomy() ) {
            $object      = get_queried_object();
            $templates[] = 'taxonomy-' . $object->taxonomy . '-' . $object->slug . '.php';
            $templates[] = MasVideos()->template_path() . 'taxonomy-' . $object->taxonomy . '-' . $object->slug . '.php';
            $templates[] = 'taxonomy-' . $object->taxonomy . '.php';
            $templates[] = MasVideos()->template_path() . 'taxonomy-' . $object->taxonomy . '.php';
        }

        if ( is_singular( 'tv_show_playlist' ) ) {
            $object       = get_queried_object();
            $name_decoded = urldecode( $object->post_name );
            if ( $name_decoded !== $object->post_name ) {
                $templates[] = "single-tv-show-playlist-{$name_decoded}.php";
            }
            $templates[] = "single-tv-show-playlist-{$object->post_name}.php";
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
            $templates[] = MasVideos()->template_path() . 'taxonomy-' . $object->taxonomy . '-' . $object->slug . '.php';
            $templates[] = 'taxonomy-' . $object->taxonomy . '.php';
            $templates[] = MasVideos()->template_path() . 'taxonomy-' . $object->taxonomy . '.php';
        }

        if ( is_singular( 'video_playlist' ) ) {
            $object       = get_queried_object();
            $name_decoded = urldecode( $object->post_name );
            if ( $name_decoded !== $object->post_name ) {
                $templates[] = "single-video-playlist-{$name_decoded}.php";
            }
            $templates[] = "single-video-playlist-{$object->post_name}.php";
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
            $templates[] = MasVideos()->template_path() . 'taxonomy-' . $object->taxonomy . '-' . $object->slug . '.php';
            $templates[] = 'taxonomy-' . $object->taxonomy . '.php';
            $templates[] = MasVideos()->template_path() . 'taxonomy-' . $object->taxonomy . '.php';
        }

        if ( is_singular( 'movie_playlist' ) ) {
            $object       = get_queried_object();
            $name_decoded = urldecode( $object->post_name );
            if ( $name_decoded !== $object->post_name ) {
                $templates[] = "single-movie-playlist-{$name_decoded}.php";
            }
            $templates[] = "single-movie-playlist-{$object->post_name}.php";
        }

        $templates[] = $default_file;
        $templates[] = MasVideos()->template_path() . $default_file;

        return array_unique( $templates );
    }

    /**
     * Load comments template.
     *
     * @param string $template template to load.
     * @return string
     */
    public static function comments_template_loader( $template ) {
        $post_type = get_post_type();

        if ( ! in_array( $post_type, array( 'episode', 'tv_show', 'video', 'movie' ) ) ) {
            return $template;
        }

        $check_dirs = array(
            trailingslashit( get_stylesheet_directory() ) . MasVideos()->template_path(),
            trailingslashit( get_template_directory() ) . MasVideos()->template_path(),
            trailingslashit( get_stylesheet_directory() ),
            trailingslashit( get_template_directory() ),
            trailingslashit( MasVideos()->plugin_path() ) . 'templates/',
        );

        if ( MASVIDEOS_TEMPLATE_DEBUG_MODE ) {
            $check_dirs = array( array_pop( $check_dirs ) );
        }

        foreach ( $check_dirs as $dir ) {
            switch ( $post_type ) {
                case 'episode':
                    $file_name = 'single-episode-reviews.php';
                    break;
                case 'tv_show':
                    $file_name = 'single-tv-show-reviews.php';
                    break;
                case 'video':
                    $file_name = 'single-video-reviews.php';
                    break;
                case 'movie':
                    $file_name = 'single-movie-reviews.php';
                    break;
                default:
                    $file_name = 'single-video-reviews.php';
                    break;
            }

            if ( file_exists( trailingslashit( $dir ) . $file_name ) ) {
                return trailingslashit( $dir ) . $file_name;
            }
        }
    }
}

add_action( 'init', array( 'MasVideos_Template_Loader', 'init' ) );
