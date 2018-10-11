<?php
/**
 * WooCommerce Formatting
 *
 * Functions for formatting data.
 *
 * @package WooCommerce/Functions
 * @version 2.1.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Converts a string (e.g. 'yes' or 'no') to a bool.
 *
 * @since 3.0.0
 * @param string $string String to convert.
 * @return bool
 */
function masvideos_string_to_bool( $string ) {
    return is_bool( $string ) ? $string : ( 'yes' === $string || 1 === $string || 'true' === $string || '1' === $string );
}

/**
 * Converts a bool to a 'yes' or 'no'.
 *
 * @since 3.0.0
 * @param bool $bool String to convert.
 * @return string
 */
function masvideos_bool_to_string( $bool ) {
    if ( ! is_bool( $bool ) ) {
        $bool = masvideos_string_to_bool( $bool );
    }
    return true === $bool ? 'yes' : 'no';
}

/**
 * Explode a string into an array by $delimiter and remove empty values.
 *
 * @since 3.0.0
 * @param string $string    String to convert.
 * @param string $delimiter Delimiter, defaults to ','.
 * @return array
 */
function masvideos_string_to_array( $string, $delimiter = ',' ) {
    return is_array( $string ) ? $string : array_filter( explode( $delimiter, $string ) );
}

/**
 * Sanitize taxonomy names. Slug format (no spaces, lowercase).
 * Urldecode is used to reverse munging of UTF8 characters.
 *
 * @param string $taxonomy Taxonomy name.
 * @return string
 */
function masvideos_sanitize_taxonomy_name( $taxonomy ) {
    return apply_filters( 'sanitize_taxonomy_name', urldecode( sanitize_title( urldecode( $taxonomy ) ) ), $taxonomy );
}

/**
 * Sanitize permalink values before insertion into DB.
 *
 * Cannot use masvideos_clean because it sometimes strips % chars and breaks the user's setting.
 *
 * @since  2.6.0
 * @param  string $value Permalink.
 * @return string
 */
function masvideos_sanitize_permalink( $value ) {
    global $wpdb;

    $value = $wpdb->strip_invalid_text_for_column( $wpdb->options, 'option_value', $value );

    if ( is_wp_error( $value ) ) {
        $value = '';
    }

    $value = esc_url_raw( trim( $value ) );
    $value = str_replace( 'http://', '', $value );
    return untrailingslashit( $value );
}

/**
 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
 * Non-scalar values are ignored.
 *
 * @param string|array $var Data to sanitize.
 * @return string|array
 */
function masvideos_clean( $var ) {
    if ( is_array( $var ) ) {
        return array_map( 'masvideos_clean', $var );
    } else {
        return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
    }
}

/**
 * Run masvideos_clean over posted textarea but maintain line breaks.
 *
 * @since  3.0.0
 * @param  string $var Data to sanitize.
 * @return string
 */
function masvideos_sanitize_textarea( $var ) {
    return implode( "\n", array_map( 'masvideos_clean', explode( "\n", $var ) ) );
}

/**
 * Sanitize a string destined to be a tooltip.
 *
 * @since  1.0.0 Tooltips are encoded with htmlspecialchars to prevent XSS. Should not be used in conjunction with esc_attr()
 * @param  string $var Data to sanitize.
 * @return string
 */
function masvideos_sanitize_tooltip( $var ) {
    return htmlspecialchars(
        wp_kses(
            html_entity_decode( $var ), array(
                'br'     => array(),
                'em'     => array(),
                'strong' => array(),
                'small'  => array(),
                'span'   => array(),
                'ul'     => array(),
                'li'     => array(),
                'ol'     => array(),
                'p'      => array(),
            )
        )
    );
}

/**
 * Merge two arrays.
 *
 * @param array $a1 First array to merge.
 * @param array $a2 Second array to merge.
 * @return array
 */
function masvideos_array_overlay( $a1, $a2 ) {
    foreach ( $a1 as $k => $v ) {
        if ( ! array_key_exists( $k, $a2 ) ) {
            continue;
        }
        if ( is_array( $v ) && is_array( $a2[ $k ] ) ) {
            $a1[ $k ] = masvideos_array_overlay( $v, $a2[ $k ] );
        } else {
            $a1[ $k ] = $a2[ $k ];
        }
    }
    return $a1;
}

/**
 * Implode and escape HTML attributes for output.
 *
 * @since 1.0.0
 * @param array $raw_attributes Attribute name value pairs.
 * @return string
 */
function masvideos_implode_html_attributes( $raw_attributes ) {
    $attributes = array();
    foreach ( $raw_attributes as $name => $value ) {
        $attributes[] = esc_attr( $name ) . '="' . esc_attr( $value ) . '"';
    }
    return implode( ' ', $attributes );
}