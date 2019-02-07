<?php
/**
 * Vod functions.
 *
 * @package vodi
 */
if ( ! function_exists( 'vodi_is_landing_page' ) ) {
    function vodi_is_landing_page() {
        return is_page_template( 'template-landingpage-v2.php' );
    }
}

if( ! function_exists( 'vodi_is_redux_activated' ) ) {
    /**
     * Check if Redux Framework is activated
     */

    function vodi_is_redux_activated() {
        return class_exists( 'ReduxFrameworkPlugin' ) ? true : false;
    }
}

if ( ! function_exists( 'vodi_is_woocommerce_activated' ) ) {
    /**
     * Query WooCommerce activation
     */
    function vodi_is_woocommerce_activated() {
        return class_exists( 'WooCommerce' ) ? true : false;
    }
}

if ( ! function_exists( 'vodi_is_jetpack_activated' ) ) {
    function vodi_is_jetpack_activated() {
        return class_exists( 'Jetpack' ) ? true : false;
    }
}

if ( ! function_exists( 'vodi_is_masvideos_activated' ) ) {
    function vodi_is_masvideos_activated() {
        return class_exists( 'MasVideos' ) ? true : false;
    }
}

if ( function_exists( 'vodi_is_masvideos_activated' ) && vodi_is_masvideos_activated() ) {
    require_once get_template_directory() . '/inc/masvideos/classes/class-vodi-shortcode-movies.php';
}

/**
 * Call a shortcode function by tag name.
 *
 * @since  1.0.0
 *
 * @param string $tag     The shortcode whose function to call.
 * @param array  $atts    The attributes to pass to the shortcode function. Optional.
 * @param array  $content The shortcode's content. Default is null (none).
 *
 * @return string|bool False on failure, the result of the shortcode on success.
 */
function vodi_do_shortcode( $tag, array $atts = array(), $content = null ) {
    global $shortcode_tags;

    if ( ! isset( $shortcode_tags[ $tag ] ) ) {
        return false;
    }

    return call_user_func( $shortcode_tags[ $tag ], $atts, $content, $tag );
}

/**
 * Get the content background color
 *
 * @since  1.0.0
 * @return string the background color
 */
function vodi_get_content_background_color() {

    $bg_color = str_replace( '#', '', get_theme_mod( 'background_color' ) );
    return '#' . $bg_color;
}

/**
 * Apply inline style to the Vodi header.
 *
 * @uses  get_header_image()
 * @since  1.0.0
 */
function vodi_header_styles() {
    $is_header_image = get_header_image();
    $header_bg_image = '';

    if ( $is_header_image ) {
        $header_bg_image = 'url(' . esc_url( $is_header_image ) . ')';
    }

    $styles = array();

    if ( '' !== $header_bg_image ) {
        $styles['background-image'] = $header_bg_image;
    }

    $styles = apply_filters( 'vodi_header_styles', $styles );

    foreach ( $styles as $style => $value ) {
        echo esc_attr( $style . ': ' . $value . '; ' );
    }
}

/**
 * Apply inline style to the Vodi homepage content.
 *
 * @uses  get_the_post_thumbnail_url()
 * @since  1.0.0
 */
function vodi_homepage_content_styles() {
    $featured_image   = get_the_post_thumbnail_url( get_the_ID() );
    $background_image = '';

    if ( $featured_image ) {
        $background_image = 'url(' . esc_url( $featured_image ) . ')';
    }

    $styles = array();

    if ( '' !== $background_image ) {
        $styles['background-image'] = $background_image;
    }

    $styles = apply_filters( 'vodi_homepage_content_styles', $styles );

    foreach ( $styles as $style => $value ) {
        echo esc_attr( $style . ': ' . $value . '; ' );
    }
}

/**
 * Adjust a hex color brightness
 * Allows us to create hover styles for custom link colors
 *
 * @param  strong  $hex   hex color e.g. #111111.
 * @param  integer $steps factor by which to brighten/darken ranging from -255 (darken) to 255 (brighten).
 * @return string        brightened/darkened hex color
 * @since  1.0.0
 */
function vodi_adjust_color_brightness( $hex, $steps ) {
    // Steps should be between -255 and 255. Negative = darker, positive = lighter.
    $steps  = max( -255, min( 255, $steps ) );

    // Format the hex color string.
    $hex    = str_replace( '#', '', $hex );

    if ( 3 == strlen( $hex ) ) {
        $hex    = str_repeat( substr( $hex, 0, 1 ), 2 ) . str_repeat( substr( $hex, 1, 1 ), 2 ) . str_repeat( substr( $hex, 2, 1 ), 2 );
    }

    // Get decimal values.
    $r  = hexdec( substr( $hex, 0, 2 ) );
    $g  = hexdec( substr( $hex, 2, 2 ) );
    $b  = hexdec( substr( $hex, 4, 2 ) );

    // Adjust number of steps and keep it inside 0 to 255.
    $r  = max( 0, min( 255, $r + $steps ) );
    $g  = max( 0, min( 255, $g + $steps ) );
    $b  = max( 0, min( 255, $b + $steps ) );

    $r_hex  = str_pad( dechex( $r ), 2, '0', STR_PAD_LEFT );
    $g_hex  = str_pad( dechex( $g ), 2, '0', STR_PAD_LEFT );
    $b_hex  = str_pad( dechex( $b ), 2, '0', STR_PAD_LEFT );

    return '#' . $r_hex . $g_hex . $b_hex;
}

/**
 * Sanitizes choices (selects / radios)
 * Checks that the input matches one of the available choices
 *
 * @param array $input the available choices.
 * @param array $setting the setting object.
 * @since  1.0.0
 */
function vodi_sanitize_choices( $input, $setting ) {
    // Ensure input is a slug.
    $input = sanitize_key( $input );

    // Get list of choices from the control associated with the setting.
    $choices = $setting->manager->get_control( $setting->id )->choices;

    // If the input is a valid key, return it; otherwise, return the default.
    return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
}

/**
 * Checkbox sanitization callback.
 *
 * Sanitization callback for 'checkbox' type controls. This callback sanitizes `$checked`
 * as a boolean value, either TRUE or FALSE.
 *
 * @param bool $checked Whether the checkbox is checked.
 * @return bool Whether the checkbox is checked.
 * @since  1.0.0
 */
function vodi_sanitize_checkbox( $checked ) {
    return ( ( isset( $checked ) && true == $checked ) ? true : false );
}

/**
 * Vodi Sanitize Hex Color
 *
 * @param string $color The color as a hex.
 * @todo remove in 2.1.
 */
function vodi_sanitize_hex_color( $color ) {
    _deprecated_function( 'vodi_sanitize_hex_color', '2.0', 'sanitize_hex_color' );

    if ( '' === $color ) {
        return '';
    }

    // 3 or 6 hex digits, or the empty string.
    if ( preg_match( '|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ) {
        return $color;
    }

    return null;
}

if ( ! function_exists( 'vodi_get_social_networks' ) ) {
    /**
     * List of all available social networks
     *
     * @return array array of all social networks and its details
     */
    function vodi_get_social_networks() {
        return apply_filters( 'vodi_get_social_networks', array(
            'facebook'      => array(
                'label' => esc_html__( 'Facebook', 'vodi' ),
                'icon'  => 'fab fa-facebook-f',
                'id'    => 'facebook_link',
                'link'  => '#'
            ),
            'twitter'       => array(
                'label' => esc_html__( 'Twitter', 'vodi' ),
                'icon'  => 'fab fa-twitter',
                'id'    => 'twitter_link',
                'link'  => '#'
            ),
            'whatsapp-mobile'   => array(
                'label' => esc_html__( 'Whatsapp Mobile', 'vodi' ),
                'icon'  => 'fab fa-whatsapp mobile',
                'id'    => 'whatsapp_mobile_link',
            ),
            'whatsapp-desktop'  => array(
                'label' => esc_html__( 'Whatsapp Desktop', 'vodi' ),
                'icon'  => 'fab fa-whatsapp desktop',
                'id'    => 'whatsapp_desktop_link',
            ),
            'pinterest'     => array(
                'label' => esc_html__( 'Pinterest', 'vodi' ),
                'icon'  => 'fab fa-pinterest',
                'id'    => 'pinterest_link',
            ),
            'linkedin'      => array(
                'label' => esc_html__( 'LinkedIn', 'vodi' ),
                'icon'  => 'fab fa-linkedin',
                'id'    => 'linkedin_link',
            ),
            'googleplus'    => array(
                'label' => esc_html__( 'Google+', 'vodi' ),
                'icon'  => 'fab fa-google-plus-g',
                'id'    => 'googleplus_link',
                'link'  => '#'
            ),
            'tumblr'    => array(
                'label' => esc_html__( 'Tumblr', 'vodi' ),
                'icon'  => 'fab fa-tumblr',
                'id'    => 'tumblr_link'
            ),
            'instagram'     => array(
                'label' => esc_html__( 'Instagram', 'vodi' ),
                'icon'  => 'fab fa-instagram',
                'id'    => 'instagram_link'
            ),
            'youtube'       => array(
                'label' => esc_html__( 'Youtube', 'vodi' ),
                'icon'  => 'fab fa-youtube',
                'id'    => 'youtube_link'
            ),
            'vimeo'         => array(
                'label' => esc_html__( 'Vimeo', 'vodi' ),
                'icon'  => 'fab fa-vimeo-square',
                'id'    => 'vimeo_link'
            ),
            'dribbble'      => array(
                'label' => esc_html__( 'Dribbble', 'vodi' ),
                'icon'  => 'fab fa-dribbble',
                'id'    => 'dribbble_link',
                'link'  => '#'
            ),
            'stumbleupon'   => array(
                'label' => esc_html__( 'StumbleUpon', 'vodi' ),
                'icon'  => 'fab fa-stumbleupon',
                'id'    => 'stumble_upon_link'
            ),
            'soundcloud'    => array(
                'label' => esc_html__('Sound Cloud', 'vodi'),
                'id'    => 'soundcloud_link',
                'icon'  => 'fab fa-soundcloud',
            ),

            'vine'           => array(
                'label' => esc_html__('Vine', 'vodi'),
                'id'    => 'vine_link',
                'icon'  => 'fab fa-vine',
            ),

            'vk'              => array(
                'label' => esc_html__('VKontakte', 'vodi'),
                'id'    => 'vk_link',
                'icon'  => 'fab fa-vk',
            ),

            'telegram'        => array(
                'label' => esc_html__('Telegram', 'vodi'),
                'id'    => 'telegram_link',
                'icon'  => 'fab fa-telegram-plane',
            ),

            'rss'           => array(
                'label' => esc_html__( 'RSS', 'vodi' ),
                'icon'  => 'fas fa-rss',
                'id'    => 'rss_link',
                'link'  => get_bloginfo( 'rss2_url' ),
            )
        ) );
    }
}

if ( ! function_exists( 'pr' ) ) {
    function pr( $var ) {
        echo '<pre>' . print_r( $var, 1 ) . '</pre>';
    }
}

/**
 * Get other templates (e.g. product attributes) passing attributes and including the file.
 *
 * @access public
 * @param string $template_name
 * @param array $args (default: array())
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 * @return void
 */
function vodi_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
    if ( $args && is_array( $args ) ) {
        extract( $args );
    }
    $located = vodi_locate_template( $template_name, $template_path, $default_path );
    if ( ! file_exists( $located ) ) {
        _doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '2.1' );
        return;
    }
    // Allow 3rd party plugin filter template file from their plugin
    $located = apply_filters( 'vodi_get_template', $located, $template_name, $args, $template_path, $default_path );
    do_action( 'vodi_before_template_part', $template_name, $template_path, $located, $args );
    include( $located );
    do_action( 'vodi_after_template_part', $template_name, $template_path, $located, $args );
}
/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 *      yourtheme       /   $template_path  /   $template_name
 *      yourtheme       /   $template_name
 *      $default_path   /   $template_name
 *
 * @access public
 * @param string $template_name
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 * @return string
 */
function vodi_locate_template( $template_name, $template_path = '', $default_path = '' ) {
    if ( ! $template_path ) {
        $template_path = 'templates/';
    }
    if ( ! $default_path ) {
        $default_path = 'templates/';
    }
    // Look within passed path within the theme - this is priority
    $template = locate_template(
        array(
            trailingslashit( $template_path ) . $template_name,
            $template_name
        )
    );
    // Get default template
    if ( ! $template || VODI_TEMPLATE_DEBUG_MODE ) {
        $template = $default_path . $template_name;
    }
    // Return what we found
    return apply_filters( 'vodi_locate_template', $template, $template_name, $template_path );
}

if ( ! function_exists( 'vodi_get_layout' ) ) {
    function vodi_get_layout() {
        $layout = 'sidebar-right';

        if ( is_page() ) {
            $layout = 'full-width';
        }

        return apply_filters( 'vodi_get_layout', $layout );
    }
}

if ( ! function_exists( 'vodi_sticky_classes' ) ) {
    function vodi_sticky_classes( $classes, $class, $post_id ) {
        if ( ! is_sticky() ) {
            return $classes;
        }

        $classes[] = 'vodi-sticky';

        return $classes;
    }
}
add_filter( 'post_class', 'vodi_sticky_classes', 10, 3 );

require_once get_template_directory() . '/inc/functions/header.php';
require_once get_template_directory() . '/inc/functions/footer.php';
