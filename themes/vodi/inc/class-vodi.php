<?php
/**
 * Vodi Class
 *
 * @since    0.0.1
 * @package  vodi
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Vodi' ) ) :

    /**
     * The main Vodi class
     */
    class Vodi {

        /**
         * Setup class.
         *
         * @since 1.0
         */
        public function __construct() {
            $this->includes();
            $this->init_hooks();
        }

        public function includes() {
            // Gutenberg Blocks
            require get_template_directory() . '/inc/gutenberg/vodi-gutenberg-block-functions.php';
            require get_template_directory() . '/inc/gutenberg/class-vodi-gutenberg-blocks.php';
        }

        public function init_hooks() {
            add_action( 'widgets_init',         array( $this, 'widgets_init' ), 10 );
            add_action( 'widgets_init',         array( $this, 'widgets_register' ) );
            add_action( 'after_setup_theme',    array( $this, 'setup' ) );
            add_action( 'after_setup_theme',    array( $this, 'vodi_template_debug_mode' ) );
            add_action( 'wp_enqueue_scripts',   array( $this, 'scripts' ), 10 );
            add_action( 'wp_enqueue_scripts',   array( $this, 'child_scripts' ), 30 ); // After WooCommerce.
            add_action( 'enqueue_block_assets', array( $this, 'block_assets' ) );
            add_filter( 'body_class',           array( $this, 'body_classes' ) );
            add_filter( 'embed_defaults',       array( $this, 'embed_defaults' ) );
            add_action( 'init',                 array( 'Vodi_Gutenberg_Blocks', 'init' ) );
        }

        /**
         * Sets up theme defaults and registers support for various WordPress features.
         *
         * Note that this function is hooked into the after_setup_theme hook, which
         * runs before the init hook. The init hook is too late for some features, such
         * as indicating support for post thumbnails.
         */
        public function setup() {
            /*
             * Load Localisation files.
             *
             * Note: the first-loaded translation file overrides any following ones if the same translation is present.
             */

            // Loads wp-content/languages/themes/vodi-it_IT.mo.
            load_theme_textdomain( 'vodi', trailingslashit( WP_LANG_DIR ) . 'themes/' );

            // Loads wp-content/themes/vodi-child/languages/it_IT.mo.
            load_theme_textdomain( 'vodi', get_stylesheet_directory() . '/languages' );

            // Loads wp-content/themes/vodi/languages/it_IT.mo.
            load_theme_textdomain( 'vodi', get_template_directory() . '/languages' );

            /**
             * Add default posts and comments RSS feed links to head.
             */
            add_theme_support( 'automatic-feed-links' );

            /*
             * Enable support for Post Thumbnails on posts and pages.
             *
             * @link https://developer.wordpress.org/reference/functions/add_theme_support/#Post_Thumbnails
             */
            add_theme_support( 'post-thumbnails' );

            add_image_size( 'vodi-featured-image', 480, 270, true );
            add_image_size( 'vodi-single-post-featured-image', 990, 440, true );

            /*
             * Enable support for Post Formats.
             *
             * See: https://codex.wordpress.org/Post_Formats
             */
            add_theme_support(
                'post-formats', array(
                    'aside',
                    'image',
                    'video',
                    'quote',
                    'link',
                    'gallery',
                    'audio',
                )
            );

            /**
             * Enable support for site logo.
             */
            add_theme_support(
                'custom-logo', apply_filters(
                    'vodi_custom_logo_args', array(
                        'height'      => 110,
                        'width'       => 470,
                        'flex-width'  => true,
                        'flex-height' => true,
                    )
                )
            );


            /**
             * Register menu locations.
             */
            register_nav_menus(
                apply_filters(
                    'vodi_register_nav_menus', array(
                        'primary'               => esc_html__( 'Primary Menu', 'vodi' ),
                        'secondary'             => esc_html__( 'Secondary Menu', 'vodi' ),
                        'secondary-nav-v3'      => esc_html__( 'Secondary Nav V3 Menu', 'vodi' ),
                        'offcanvas'             => esc_html__( 'Offcanvas Menu', 'vodi' ),
                        'navbar-primary'        => esc_html__( 'Navbar Primary', 'vodi' ),
                        'footer'                => esc_html__( 'Footer Menu', 'vodi' ),
                        'footer-primary-menu'   => esc_html__( 'Footer Primary Menu', 'vodi' ),
                        'footer-secondary-menu' => esc_html__( 'Footer Secondary Menu', 'vodi' ),
                        'footer-tertiary-menu'  => esc_html__( 'Footer Tertiary Menu', 'vodi' ),
                    )
                )
            );

            /*
             * Switch default core markup for search form, comment form, comments, galleries, captions and widgets
             * to output valid HTML5.
             */
            add_theme_support(
                'html5', apply_filters(
                    'vodi_html5_args', array(
                        'search-form',
                        'comment-form',
                        'comment-list',
                        'gallery',
                        'caption',
                        'widgets',
                    )
                )
            );

            /**
             * Setup the WordPress core custom background feature.
             */
            add_theme_support(
                'custom-background', apply_filters(
                    'vodi_custom_background_args', array(
                        'default-color' => apply_filters( 'vodi_default_background_color', 'ffffff' ),
                        'default-image' => '',
                    )
                )
            );

            /**
             * Setup the WordPress core custom header feature.
             */
            add_theme_support(
                'custom-header', apply_filters(
                    'vodi_custom_header_args', array(
                        'default-image' => '',
                        'header-text'   => false,
                        'width'         => 1440,
                        'height'        => 500,
                        'flex-width'    => true,
                        'flex-height'   => true,
                    )
                )
            );

            /**
             *  Add support for the Site Logo plugin and the site logo functionality in JetPack
             *  https://github.com/automattic/site-logo
             *  http://jetpack.me/
             */
            add_theme_support(
                'site-logo', apply_filters(
                    'vodi_site_logo_args', array(
                        'size' => 'full',
                    )
                )
            );

            /**
             * Declare support for title theme feature.
             */
            add_theme_support( 'title-tag' );

            /**
             * Declare support for selective refreshing of widgets.
             */
            add_theme_support( 'customize-selective-refresh-widgets' );

            /**
             * Declare support for masvideos.
             */
            add_theme_support( 'masvideos', array(
                'image_sizes'   => array(
                    'video_large'       => array(
                        'width'     => 600,
                        'height'    => 900,
                        'crop'      => 1,
                    ),
                    'video_medium'      => array(
                        'width'     => 300,
                        'height'    => 450,
                        'crop'      => 1,
                    ),
                    'video_thumbnail'   => array(
                        'width'     => 150,
                        'height'    => 225,
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

            /**
             * Add support for Block Styles.
             */
            add_theme_support( 'wp-block-styles' );

            /**
             * Add support for full and wide align images.
             */
            add_theme_support( 'align-wide' );

            /**
             * Add support for editor styles.
             */
            add_theme_support( 'editor-styles' );

            /**
             * Enqueue editor styles.
             */
            add_editor_style( array( get_template_directory_uri() . '/assets/css/gutenberg-editor.css', $this->google_fonts() ) );

            /**
             * Add support for responsive embedded content.
             */
            add_theme_support( 'responsive-embeds' );
        }

        /**
         * Register widgets.
         *
         * @link http://codex.wordpress.org/Function_Reference/register_sidebar
         */
        public function widgets_register() {

            include_once get_template_directory() . '/inc/widgets/class-vodi-posts-widget.php';
            register_widget( 'Vodi_Posts_widget' );

            include_once get_template_directory() . '/inc/widgets/class-vodi-tab-widget.php';
            register_widget( 'Vodi_Tabbed_Widget' );

            if (class_exists('MasVideos_Widget_Movies_Layered_Nav') ){
                include_once get_template_directory() . '/inc/widgets/class-vodi-movies-genres-filter-widgets.php';
                register_widget( 'Vodi_Movies_Genres_Filter_Widget' );
            }

            //include_once get_template_directory() . '/inc/widgets/class-vodi-widget-layered-nav.php';
            //register_widget( 'Vodi_Widget_Layered_Nav' );
        }

        /**
         * Enqueue scripts and styles.
         *
         * @since  1.0.0
         */
        public function scripts() {
            global $vodi_version;

            /**
             * Styles
             */
            if ( vodi_is_landing_page() ) {
                wp_enqueue_style( 'vodi-landing', get_template_directory_uri() . '/assets/css/landing.css', '', $vodi_version );
                wp_enqueue_style( 'vodi-fontawesome', get_template_directory_uri() . '/assets/css/fontawesome.css', '', $vodi_version );
            } else {
                wp_enqueue_style( 'vodi-style', get_template_directory_uri() . '/style.css', '', $vodi_version );
                wp_style_add_data( 'vodi-style', 'rtl', 'replace' );

                wp_enqueue_style( 'vodi-fontawesome', get_template_directory_uri() . '/assets/css/fontawesome.css', '', $vodi_version );
                wp_style_add_data( 'vodi-fontawesome', 'rtl', 'replace' );

                wp_enqueue_style( 'vodi-theme-color', get_template_directory_uri() . '/assets/css/theme.css', $vodi_version );

                if ( vodi_is_masvideos_activated() ) {
                    wp_enqueue_style( 'vodi-masvideos', get_template_directory_uri() . '/assets/css/masvideos.css', '', $vodi_version );
                }


                if( apply_filters( 'vodi_use_predefined_colors', true ) ) {
                    $color_css_file = apply_filters( 'vodi_primary_color', 'blue' );
                    wp_enqueue_style( 'vodi-color', get_template_directory_uri() . '/assets/css/colors/' . $color_css_file . '.css', '', $vodi_version );
                }
            }

            /**
             * Fonts
             */
            wp_enqueue_style( 'vodi-fonts', $this->google_fonts(), array(), null );

            /**
             * Scripts
             */
            $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

            wp_enqueue_script( 'vodi-bootstrap-bundle', get_template_directory_uri() . '/assets/js/bootstrap.bundle' . $suffix . '.js', array( 'jquery' ), $vodi_version, true );

            wp_enqueue_script( 'slick', get_template_directory_uri() . '/assets/js/slick.min.js', array( 'jquery' ), $vodi_version, true );

            wp_enqueue_script( 'vodi-scripts', get_template_directory_uri() . '/assets/js/vodi.js', array( 'jquery' ), $vodi_version, true );

            $vodi_js_options = apply_filters( 'vodi_localize_script_data', array(
                'ajax_url'              => admin_url( 'admin-ajax.php' ),
                'deal_countdown_text'   => apply_filters( 'vodi_deal_countdown_timer_clock_text', array(
                    'days_text'     => esc_html__( 'Days', 'vodi' ),
                    'hours_text'    => esc_html__( 'Hours', 'vodi' ),
                    'mins_text'     => esc_html__( 'Mins', 'vodi' ),
                    'secs_text'     => esc_html__( 'Secs', 'vodi' ),
                ) ),
            ) );

            wp_localize_script( 'vodi-scripts', 'vodi_options', $vodi_js_options );

            if ( has_nav_menu( 'handheld' ) ) {
                $vodi_l10n = array(
                    'expand'   => __( 'Expand child menu', 'vodi' ),
                    'collapse' => __( 'Collapse child menu', 'vodi' ),
                );

                wp_localize_script( 'vodi-navigation', 'vodiScreenReaderText', $vodi_l10n );
            }

            if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
                wp_enqueue_script( 'comment-reply' );
            }
        }

        /**
         * Enqueue child theme stylesheet.
         * A separate function is required as the child theme css needs to be enqueued _after_ the parent theme
         * primary css and the separate WooCommerce css.
         *
         * @since  1.0.0
         */
        public function child_scripts() {
            if ( is_child_theme() ) {
                $child_theme = wp_get_theme( get_stylesheet() );
                wp_enqueue_style( 'vodi-child-style', get_stylesheet_uri(), array(), $child_theme->get( 'Version' ) );
            }
        }

        /**
         * Enqueue supplemental block editor assets.
         *
         * @since 1.0.0
         */
        public function block_assets() {
            global $vodi_version;

            // Styles.
            wp_enqueue_style( 'vodi-block-styles', get_template_directory_uri() . '/assets/css/gutenberg-blocks.css', false, $vodi_version, 'all' );
            wp_style_add_data( 'vodi-block-styles', 'rtl', 'replace' );
        }

        /**
         * Register Google fonts.
         *
         * @since 1.0.0
         * @return string Google fonts URL for the theme.
         */
        public function google_fonts() {
            $google_fonts = apply_filters(
                'vodi_google_font_families', array(
                    'montserrat' => 'Montserrat:300,400,500,600,700,800',
                    'open-sans'  => 'Open+Sans:400,600,700'
                )
            );

            $query_args = array(
                'family' => implode( '|', $google_fonts ),
                'subset' => urlencode( 'latin,latin-ext' ),
            );

            $fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
            return $fonts_url;
        }

        /**
         * Enables template debug mode
         */
        public function vodi_template_debug_mode() {
            if ( ! defined( 'VODI_TEMPLATE_DEBUG_MODE' ) ) {
                $status_options = get_option( 'woocommerce_status_options', array() );
                if ( ! empty( $status_options['template_debug_mode'] ) && current_user_can( 'manage_options' ) ) {
                    define( 'VODI_TEMPLATE_DEBUG_MODE', true );
                } else {
                    define( 'VODI_TEMPLATE_DEBUG_MODE', false );
                }
            }
        }

        /**
         * Register widget area.
         *
         * @link https://codex.wordpress.org/Function_Reference/register_sidebar
         */
        public function widgets_init() {
            $sidebar_args['sidebar_blog'] = array(
                'name'          => esc_html__( 'Blog Sidebar', 'vodi' ),
                'id'            => 'sidebar-blog',
                'description'   => esc_html__( 'Widgets added to this region will appear in the blog and single post page.', 'vodi' ),
                'widget_tags'   => array(
                    'before_widget' => '<div id="%1$s" class="widget %2$s">',
                    'after_widget'  => '</div></div>',
                    'before_title'  => '<div class="widget-header"><span class="widget-title">',
                    'after_title'   => '</span></div><div class="widget-body">',
                ),
            );

            $sidebar_args['home-sidebar'] = array(
                'name'        => esc_html__( 'Home Sidebar', 'vodi' ),
                'id'          => 'home-sidebar',
                'description' => esc_html__( 'Widgets added to this region will appear on Home Page v6 and v7.', 'vodi' ),
            );

            $sidebar_args['sidebar_shop'] = array(
                'name'        => esc_html__( 'Shop Sidebar', 'vodi' ),
                'id'          => 'sidebar-shop',
                'description' => ''
            );

            $sidebar_args['sidebar-movie'] = array(
                'name'        => esc_html__( 'Movie Sidebar', 'vodi' ),
                'id'          => 'sidebar-movie',
                'description' => esc_html__( 'Widgets added to this region will appear on movie pages.', 'vodi' ),
            );

            $rows    = intval( apply_filters( 'vodi_footer_widget_rows', 1 ) );
            $regions = intval( apply_filters( 'vodi_footer_widget_columns', 3 ) );
            for ( $row = 1; $row <= $rows; $row++ ) {
                for ( $region = 1; $region <= $regions; $region++ ) {
                    $footer_n = $region + $regions * ( $row - 1 ); // Defines footer sidebar ID.
                    $footer   = sprintf( 'footer_%d', $footer_n );
                    if ( 1 == $rows ) {
                        $footer_region_name = sprintf( esc_html__( 'Footer Column %1$d', 'vodi' ), $region );
                        $footer_region_description = sprintf( esc_html__( 'Widgets added here will appear in column %1$d of the footer.', 'vodi' ), $region );
                    } else {
                        $footer_region_name = sprintf( esc_html__( 'Footer Row %1$d - Column %2$d', 'vodi' ), $row, $region );
                        $footer_region_description = sprintf( esc_html__( 'Widgets added here will appear in column %1$d of footer row %2$d.', 'vodi' ), $region, $row );
                    }
                    $sidebar_args[ $footer ] = array(
                        'name'        => $footer_region_name,
                        'id'          => sprintf( 'footer-%d', $footer_n ),
                        'description' => $footer_region_description,
                    );
                }
            }

            $sidebar_args = apply_filters( 'vodi_sidebar_args', $sidebar_args );

            foreach ( $sidebar_args as $sidebar => $args ) {

                $widget_tags = array(
                    'before_widget' => '<div id="%1$s" class="widget %2$s">',
                    'after_widget'  => '</div>',
                    'before_title'  => '<div class="widget-header"><span class="widget-title">',
                    'after_title'   => '</span></div>',
                );

                /**
                 * Dynamically generated filter hooks. Allow changing widget wrapper and title tags. See the list below.
                 *
                 * 'vodi_sidebar_shop_widget_tags'
                 *
                 */
                $filter_hook = sprintf( 'vodi_%s_widget_tags', $sidebar );
                $widget_tags = apply_filters( $filter_hook, $widget_tags );

                if ( is_array( $widget_tags ) ) {
                    register_sidebar( $args + $widget_tags );
                }
            }
        }

        /**
         * Adds custom classes to the array of body classes.
         *
         * @param array $classes Classes for the body element.
         * @return array
         */
        public function body_classes( $classes ) {
            // Adds a class of group-blog to blogs with more than 1 published author.
            if ( is_multi_author() ) {
                $classes[] = 'group-blog';
            }

            if ( ! function_exists( 'woocommerce_breadcrumb' ) ) {
                $classes[]  = 'no-wc-breadcrumb';
            }

            /**
             * What is this?!
             * Take the blue pill, close this file and forget you saw the following code.
             * Or take the red pill, filter vodi_make_me_cute and see how deep the rabbit hole goes...
             */
            $cute = apply_filters( 'vodi_make_me_cute', false );

            if ( true === $cute ) {
                $classes[] = 'vodi-cute';
            }

            // Layout
            $layout = vodi_get_layout();

            if ( 'sidebar-right' === $layout ||
                 'sidebar-left'  === $layout ||
                 'full-width'    === $layout ) {
                $classes[] = $layout ;
            }

            $classes[] = vodi_bg_style();

            return $classes;
        }

        public function embed_defaults( $embed_size ) {

            if ( is_sticky() || is_single() ) {
                $embed_size['width']  = 990;
                $embed_size['height'] = 560;
            } else {
                $embed_size['width']  = 480;
                $embed_size['height'] = 270;
            }

            return $embed_size;
        }
    }

endif;

return new Vodi();
