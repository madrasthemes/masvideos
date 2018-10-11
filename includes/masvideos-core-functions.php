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
require MASVIDEOS_ABSPATH . 'includes/masvideos-attribute-functions.php';
// require MASVIDEOS_ABSPATH . 'includes/masvideos-video-functions.php';
// require MASVIDEOS_ABSPATH . 'includes/masvideos-movie-functions.php';

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
 * @since 2.5.0
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
            'movie_category_base'          => _x( 'movie-category', 'slug', 'masvideos' ),
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
    $permalinks['movie_category_rewrite_slug']  = untrailingslashit( $permalinks['movie_category_base'] );
    $permalinks['movie_tag_rewrite_slug']       = untrailingslashit( $permalinks['movie_tag_base'] );
    $permalinks['movie_attribute_rewrite_slug'] = untrailingslashit( $permalinks['movie_attribute_base'] );

    return $permalinks;
}

/**
 * Retrieve page ids.
 *
 * @param string $page Page slug.
 * @return int
 */
function masvideos_get_page_id( $page ) {
    $page = apply_filters( 'masvideos_get_' . $page . '_page_id', get_option( 'masvideos_' . $page . '_page_id' ) );

    return $page ? absint( $page ) : -1;
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
 * Display a WooCommerce help tip.
 *
 * @since  2.5.0
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