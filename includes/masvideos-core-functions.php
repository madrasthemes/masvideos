<?php
/**
 * MasVideos Core Functions
 *
 * General core functions available on both the front-end and admin.
 *
 * @package MasVideos\Functions
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Include core functions (available in both admin and frontend).
require MASVIDEOS_ABSPATH . 'includes/masvideos-conditional-functions.php';
require MASVIDEOS_ABSPATH . 'includes/masvideos-formatting-functions.php';
require MASVIDEOS_ABSPATH . 'includes/masvideos-term-functions.php';
require MASVIDEOS_ABSPATH . 'includes/masvideos-attribute-functions.php';
require MASVIDEOS_ABSPATH . 'includes/masvideos-page-functions.php';
require MASVIDEOS_ABSPATH . 'includes/masvideos-video-functions.php';
require MASVIDEOS_ABSPATH . 'includes/masvideos-movie-functions.php';
require MASVIDEOS_ABSPATH . 'includes/masvideos-widget-functions.php';

/**
 * Define a constant if it is not already defined.
 *
 * @since 1.0.0
 * @param string $name  Constant name.
 * @param string $value Value.
 */
function masvideos_maybe_define_constant( $name, $value ) {
    if ( ! defined( $name ) ) {
        define( $name, $value );
    }
}

/**
 * Get template part (for templates like the shop-loop).
 *
 * MASVIDEOS_TEMPLATE_DEBUG_MODE will prevent overrides in themes from taking priority.
 *
 * @access public
 * @param mixed  $slug Template slug.
 * @param string $name Template name (default: '').
 */
function masvideos_get_template_part( $slug, $name = '' ) {
    $template = '';

    // Look in yourtheme/slug-name.php and yourtheme/masvideos/slug-name.php.
    if ( $name && ! MASVIDEOS_TEMPLATE_DEBUG_MODE ) {
        $template = locate_template( array( "{$slug}-{$name}.php", MasVideos()->template_path() . "{$slug}-{$name}.php" ) );
    }

    // Get default slug-name.php.
    if ( ! $template && $name && file_exists( MasVideos()->plugin_path() . "/templates/{$slug}-{$name}.php" ) ) {
        $template = MasVideos()->plugin_path() . "/templates/{$slug}-{$name}.php";
    }

    // If template file doesn't exist, look in yourtheme/slug.php and yourtheme/masvideos/slug.php.
    if ( ! $template && ! MASVIDEOS_TEMPLATE_DEBUG_MODE ) {
        $template = locate_template( array( "{$slug}.php", MasVideos()->template_path() . "{$slug}.php" ) );
    }

    // Allow 3rd party plugins to filter template file from their plugin.
    $template = apply_filters( 'masvideos_get_template_part', $template, $slug, $name );

    if ( $template ) {
        load_template( $template, false );
    }
}

/**
 * Get other templates (e.g. product attributes) passing attributes and including the file.
 *
 * @access public
 * @param string $template_name Template name.
 * @param array  $args          Arguments. (default: array).
 * @param string $template_path Template path. (default: '').
 * @param string $default_path  Default path. (default: '').
 */
function masvideos_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
    if ( ! empty( $args ) && is_array( $args ) ) {
        extract( $args ); // @codingStandardsIgnoreLine
    }

    $located = masvideos_locate_template( $template_name, $template_path, $default_path );

    if ( ! file_exists( $located ) ) {
        /* translators: %s template */
        _doing_it_wrong( __FUNCTION__, sprintf( __( '%s does not exist.', 'masvideos' ), '<code>' . $located . '</code>' ), '1.0.0' );
        return;
    }

    // Allow 3rd party plugin filter template file from their plugin.
    $located = apply_filters( 'masvideos_get_template', $located, $template_name, $args, $template_path, $default_path );

    do_action( 'masvideos_before_template_part', $template_name, $template_path, $located, $args );

    include $located;

    do_action( 'masvideos_after_template_part', $template_name, $template_path, $located, $args );
}

/**
 * Like masvideos_get_template, but returns the HTML instead of outputting.
 *
 * @see masvideos_get_template
 * @since 1.0.0
 * @param string $template_name Template name.
 * @param array  $args          Arguments. (default: array).
 * @param string $template_path Template path. (default: '').
 * @param string $default_path  Default path. (default: '').
 *
 * @return string
 */
function masvideos_get_template_html( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
    ob_start();
    masvideos_get_template( $template_name, $args, $template_path, $default_path );
    return ob_get_clean();
}

/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 * yourtheme/$template_path/$template_name
 * yourtheme/$template_name
 * $default_path/$template_name
 *
 * @access public
 * @param string $template_name Template name.
 * @param string $template_path Template path. (default: '').
 * @param string $default_path  Default path. (default: '').
 * @return string
 */
function masvideos_locate_template( $template_name, $template_path = '', $default_path = '' ) {
    if ( ! $template_path ) {
        $template_path = MasVideos()->template_path();
    }

    if ( ! $default_path ) {
        $default_path = MasVideos()->plugin_path() . '/templates/';
    }

    // Look within passed path within the theme - this is priority.
    $template = locate_template(
        array(
            trailingslashit( $template_path ) . $template_name,
            $template_name,
        )
    );

    // Get default template/.
    if ( ! $template || MASVIDEOS_TEMPLATE_DEBUG_MODE ) {
        $template = $default_path . $template_name;
    }

    // Return what we found.
    return apply_filters( 'masvideos_locate_template', $template, $template_name, $template_path );
}

/**
 * Get rounding precision for internal MasVideos calculations.
 * Will increase the precision of masvideos_get_price_decimals by 2 decimals, unless MASVIDEOS_ROUNDING_PRECISION is set to a higher number.
 *
 * @since 1.0.0
 * @return int
 */
function masvideos_get_rounding_precision() {
    $precision = masvideos_get_price_decimals() + 2;
    if ( absint( MASVIDEOS_ROUNDING_PRECISION ) > $precision ) {
        $precision = absint( MASVIDEOS_ROUNDING_PRECISION );
    }
    return $precision;
}

/**
 * Add precision to a number and return a number.
 *
 * @since  1.0.0
 * @param  float $value Number to add precision to.
 * @param  bool  $round If should round after adding precision.
 * @return int|float
 */
function masvideos_add_number_precision( $value, $round = true ) {
    $cent_precision = pow( 10, masvideos_get_price_decimals() );
    $value          = $value * $cent_precision;
    return $round ? round( $value, masvideos_get_rounding_precision() - masvideos_get_price_decimals() ) : $value;
}

/**
 * Remove precision from a number and return a float.
 *
 * @since  1.0.0
 * @param  float $value Number to add precision to.
 * @return float
 */
function masvideos_remove_number_precision( $value ) {
    $cent_precision = pow( 10, masvideos_get_price_decimals() );
    return $value / $cent_precision;
}

/**
 * Add precision to an array of number and return an array of int.
 *
 * @since  1.0.0
 * @param  array $value Number to add precision to.
 * @param  bool  $round Should we round after adding precision?.
 * @return int
 */
function masvideos_add_number_precision_deep( $value, $round = true ) {
    if ( is_array( $value ) ) {
        foreach ( $value as $key => $subvalue ) {
            $value[ $key ] = masvideos_add_number_precision_deep( $subvalue, $round );
        }
    } else {
        $value = masvideos_add_number_precision( $value, $round );
    }
    return $value;
}

/**
 * Remove precision from an array of number and return an array of int.
 *
 * @since  1.0.0
 * @param  array $value Number to add precision to.
 * @return int
 */
function masvideos_remove_number_precision_deep( $value ) {
    if ( is_array( $value ) ) {
        foreach ( $value as $key => $subvalue ) {
            $value[ $key ] = masvideos_remove_number_precision_deep( $subvalue );
        }
    } else {
        $value = masvideos_remove_number_precision( $value );
    }
    return $value;
}

/**
 * Wrapper for set_time_limit to see if it is enabled.
 *
 * @since 1.0.0
 * @param int $limit Time limit.
 */
function masvideos_set_time_limit( $limit = 0 ) {
    if ( function_exists( 'set_time_limit' ) && false === strpos( ini_get( 'disable_functions' ), 'set_time_limit' ) && ! ini_get( 'safe_mode' ) ) { // phpcs:ignore PHPCompatibility.PHP.DeprecatedIniDirectives.safe_modeDeprecatedRemoved
        @set_time_limit( $limit ); // @codingStandardsIgnoreLine
    }
}

/**
 * Wrapper for nocache_headers which also disables page caching.
 *
 * @since 1.0.0
 */
function masvideos_nocache_headers() {
    MasVideos_Cache_Helper::set_nocache_constants();
    nocache_headers();
}

/**
 * Get permalink settings for things like videos and taxonomies.
 *
 * This is more inline with WP core behavior which does not localize slugs.
 *
 * @since  1.0.0
 * @return array
 */
function masvideos_get_video_permalink_structure() {
    $saved_permalinks = (array) get_option( 'masvideos_video_permalinks', array() );
    $permalinks       = wp_parse_args(
        array_filter( $saved_permalinks ), array(
            'video_base'                   => _x( 'video', 'slug', 'masvideos' ),
            'video_category_base'          => _x( 'video-category', 'slug', 'masvideos' ),
            'video_tag_base'               => _x( 'video-tag', 'slug', 'masvideos' ),
            'video_attribute_base'         => '',
            'movie_base'                   => _x( 'movie', 'slug', 'masvideos' ),
            'movie_genre_base'             => _x( 'movie-genre', 'slug', 'masvideos' ),
            'movie_tag_base'               => _x( 'movie-tag', 'slug', 'masvideos' ),
            'movie_attribute_base'         => '',
            'use_verbose_page_rules'       => false,
        )
    );

    if ( $saved_permalinks !== $permalinks ) {
        update_option( 'masvideos_video_permalinks', $permalinks );
    }

    $permalinks['video_rewrite_slug']           = untrailingslashit( $permalinks['video_base'] );
    $permalinks['video_category_rewrite_slug']  = untrailingslashit( $permalinks['video_category_base'] );
    $permalinks['video_tag_rewrite_slug']       = untrailingslashit( $permalinks['video_tag_base'] );
    $permalinks['video_attribute_rewrite_slug'] = untrailingslashit( $permalinks['video_attribute_base'] );

    $permalinks['movie_rewrite_slug']           = untrailingslashit( $permalinks['movie_base'] );
    $permalinks['movie_genre_rewrite_slug']     = untrailingslashit( $permalinks['movie_genre_base'] );
    $permalinks['movie_tag_rewrite_slug']       = untrailingslashit( $permalinks['movie_tag_base'] );
    $permalinks['movie_attribute_rewrite_slug'] = untrailingslashit( $permalinks['movie_attribute_base'] );

    return $permalinks;
}

/**
 * Return the html selected attribute if stringified $value is found in array of stringified $options
 * or if stringified $value is the same as scalar stringified $options.
 *
 * @param string|int       $value   Value to find within options.
 * @param string|int|array $options Options to go through when looking for value.
 * @return string
 */
function masvideos_selected( $value, $options ) {
    if ( is_array( $options ) ) {
        $options = array_map( 'strval', $options );
        return selected( in_array( (string) $value, $options, true ), true, false );
    }

    return selected( $value, $options, false );
}

/**
 * Display a MasVideos help tip.
 *
 * @since  1.0.0
 *
 * @param  string $tip        Help tip text.
 * @param  bool   $allow_html Allow sanitized HTML if true or escape.
 * @return string
 */
function masvideos_help_tip( $tip, $allow_html = false ) {
    if ( $allow_html ) {
        $tip = masvideos_sanitize_tooltip( $tip );
    } else {
        $tip = esc_attr( $tip );
    }

    return '<span class="masvideos-help-tip" data-tip="' . $tip . '"></span>';
}

/**
 * Return "theme support" values from the current theme, if set.
 *
 * @since  1.0.0
 * @param  string $prop Name of prop (or key::subkey for arrays of props) if you want a specific value. Leave blank to get all props as an array.
 * @param  mixed  $default Optional value to return if the theme does not declare support for a prop.
 * @return mixed  Value of prop(s).
 */
function masvideos_get_theme_support( $prop = '', $default = null ) {
    $theme_support = get_theme_support( 'masvideos' );
    $theme_support = is_array( $theme_support ) ? $theme_support[0] : false;

    if ( ! $theme_support ) {
        return $default;
    }

    if ( $prop ) {
        $prop_stack = explode( '::', $prop );
        $prop_key   = array_shift( $prop_stack );

        if ( isset( $theme_support[ $prop_key ] ) ) {
            $value = $theme_support[ $prop_key ];

            if ( count( $prop_stack ) ) {
                foreach ( $prop_stack as $prop_key ) {
                    if ( is_array( $value ) && isset( $value[ $prop_key ] ) ) {
                        $value = $value[ $prop_key ];
                    } else {
                        $value = $default;
                        break;
                    }
                }
            }
        } else {
            $value = $default;
        }

        return $value;
    }

    return $theme_support;
}

/**
 * Get an image size by name or defined dimensions.
 *
 * The returned variable is filtered by masvideos_get_image_size_{image_size} filter to
 * allow 3rd party customisation.
 *
 * Sizes defined by the theme take priority over settings. Settings are hidden when a theme
 * defines sizes.
 *
 * @param array|string $image_size Name of the image size to get, or an array of dimensions.
 * @return array Array of dimensions including width, height, and cropping mode. Cropping mode is 0 for no crop, and 1 for hard crop.
 */
function masvideos_get_image_size( $image_size ) {
    $size = array(
        'width'  => 600,
        'height' => 600,
        'crop'   => 1,
    );

    if ( is_array( $image_size ) ) {
        $size       = array(
            'width'  => isset( $image_size[0] ) ? absint( $image_size[0] ) : 600,
            'height' => isset( $image_size[1] ) ? absint( $image_size[1] ) : 600,
            'crop'   => isset( $image_size[2] ) ? absint( $image_size[2] ) : 1,
        );
        $image_size = $size['width'] . '_' . $size['height'];
    } else {
        $image_size = str_replace( 'masvideos_', '', $image_size );

        if ( 'video_large' === $image_size ) {
            $size['width']  = absint( masvideos_get_theme_support( 'image_sizes::video_large::width', 600 ) );
            $size['height'] = absint( masvideos_get_theme_support( 'image_sizes::video_large::height', 600 ) );
            $size['crop']   = absint( masvideos_get_theme_support( 'image_sizes::video_large::crop', 1 ) );

        } elseif ( 'video_medium' === $image_size ) {
            $size['width']  = absint( masvideos_get_theme_support( 'image_sizes::video_medium::width', 300 ) );
            $size['height'] = absint( masvideos_get_theme_support( 'image_sizes::video_medium::height', 300 ) );
            $size['crop']   = absint( masvideos_get_theme_support( 'image_sizes::video_medium::crop', 1 ) );

        } elseif ( 'video_thumbnail' === $image_size ) {
            $size['width']  = absint( masvideos_get_theme_support( 'image_sizes::video_thumbnail::width', 100 ) );
            $size['height'] = absint( masvideos_get_theme_support( 'image_sizes::video_thumbnail::height', 100 ) );
            $size['crop']   = absint( masvideos_get_theme_support( 'image_sizes::video_thumbnail::crop', 1 ) );

        } elseif ( 'movie_large' === $image_size ) {
            $size['width']  = absint( masvideos_get_theme_support( 'image_sizes::movie_large::width', 600 ) );
            $size['height'] = absint( masvideos_get_theme_support( 'image_sizes::movie_large::height', 600 ) );
            $size['crop']   = absint( masvideos_get_theme_support( 'image_sizes::movie_large::crop', 1 ) );

        } elseif ( 'movie_medium' === $image_size ) {
            $size['width']  = absint( masvideos_get_theme_support( 'image_sizes::movie_medium::width', 300 ) );
            $size['height'] = absint( masvideos_get_theme_support( 'image_sizes::movie_medium::height', 300 ) );
            $size['crop']   = absint( masvideos_get_theme_support( 'image_sizes::movie_medium::crop', 1 ) );

        } elseif ( 'movie_thumbnail' === $image_size ) {
            $size['width']  = absint( masvideos_get_theme_support( 'image_sizes::movie_thumbnail::width', 100 ) );
            $size['height'] = absint( masvideos_get_theme_support( 'image_sizes::movie_thumbnail::height', 100 ) );
            $size['crop']   = absint( masvideos_get_theme_support( 'image_sizes::movie_thumbnail::crop', 1 ) );

        }
    }

    return apply_filters( 'masvideos_get_image_size_' . $image_size, $size );
}

/**
 * Queue some JavaScript code to be output in the footer.
 *
 * @param string $code Code.
 */
function masvideos_enqueue_js( $code ) {
    global $masvideos_queued_js;

    if ( empty( $masvideos_queued_js ) ) {
        $masvideos_queued_js = '';
    }

    $masvideos_queued_js .= "\n" . $code . "\n";
}

/**
 * Outputs a "back" link so admin screens can easily jump back a page.
 *
 * @param string $label Title of the page to return to.
 * @param string $url   URL of the page to return to.
 */
function masvideos_back_link( $label, $url ) {
    echo '<small class="masvideos-admin-breadcrumb"><a href="' . esc_url( $url ) . '" aria-label="' . esc_attr( $label ) . '">&#x2934;</a></small>';
}