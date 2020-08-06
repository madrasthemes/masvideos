<?php
/**
 * MasVideos Formatting
 *
 * Functions for formatting data.
 *
 * @package MasVideos/Functions
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Converts a string (e.g. 'yes' or 'no') to a bool.
 *
 * @since 1.0.0
 * @param string $string String to convert.
 * @return bool
 */
function masvideos_string_to_bool( $string ) {
    return is_bool( $string ) ? $string : ( 'yes' === $string || 1 === $string || 'true' === $string || '1' === $string );
}

/**
 * Converts a bool to a 'yes' or 'no'.
 *
 * @since 1.0.0
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
 * @since 1.0.0
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
 * @since  1.0.0
 * @param  string $var Data to sanitize.
 * @return string
 */
function masvideos_sanitize_textarea( $var ) {
    return implode( "\n", array_map( 'masvideos_clean', explode( "\n", $var ) ) );
}

/**
 * Run textarea with iframe.
 *
 * @since  1.0.0
 * @param  string $var Data to sanitize.
 * @return string
 */
function masvideos_sanitize_textarea_iframe( $var ) {
    $allowed_tags = wp_kses_allowed_html( 'post' );
    // iframe
    $allowed_tags['iframe'] = array(
        'src'             => array(),
        'height'          => array(),
        'width'           => array(),
        'frameborder'     => array(),
        'allowfullscreen' => array(),
    );
    return wp_kses( $var, $allowed_tags );
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
 * MasVideos Date Format - Allows to change date format for everything MasVideos.
 *
 * @return string
 */
function masvideos_date_format() {
    return apply_filters( 'masvideos_date_format', get_option( 'date_format' ) );
}

/**
 * MasVideos Time Format - Allows to change time format for everything MasVideos.
 *
 * @return string
 */
function masvideos_time_format() {
    return apply_filters( 'masvideos_time_format', get_option( 'time_format' ) );
}

/**
 * Convert mysql datetime to PHP timestamp, forcing UTC. Wrapper for strtotime.
 *
 * Based on wcs_strtotime_dark_knight() from WC Subscriptions by Prospress.
 *
 * @since  1.0.0
 * @param  string   $time_string    Time string.
 * @param  int|null $from_timestamp Timestamp to convert from.
 * @return int
 */
function masvideos_string_to_timestamp( $time_string, $from_timestamp = null ) {
    $original_timezone = date_default_timezone_get();

    // @codingStandardsIgnoreStart
    date_default_timezone_set( 'UTC' );

    if ( null === $from_timestamp ) {
        $next_timestamp = strtotime( $time_string );
    } else {
        $next_timestamp = strtotime( $time_string, $from_timestamp );
    }

    date_default_timezone_set( $original_timezone );
    // @codingStandardsIgnoreEnd

    return $next_timestamp;
}

/**
 * Convert a date string to a MasVideos_DateTime.
 *
 * @since  1.0.0
 * @param  string $time_string Time string.
 * @return MasVideos_DateTime
 */
function masvideos_string_to_datetime( $time_string ) {
    // Strings are defined in local WP timezone. Convert to UTC.
    if ( 1 === preg_match( '/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})(Z|((-|\+)\d{2}:\d{2}))$/', $time_string, $date_bits ) ) {
        $offset    = ! empty( $date_bits[7] ) ? iso8601_timezone_to_offset( $date_bits[7] ) : masvideos_timezone_offset();
        $timestamp = gmmktime( $date_bits[4], $date_bits[5], $date_bits[6], $date_bits[2], $date_bits[3], $date_bits[1] ) - $offset;
    } else {
        $timestamp = masvideos_string_to_timestamp( get_gmt_from_date( gmdate( 'Y-m-d H:i:s', masvideos_string_to_timestamp( $time_string ) ) ) );
    }
    $datetime = new MasVideos_DateTime( "@{$timestamp}", new DateTimeZone( 'UTC' ) );

    // Set local timezone or offset.
    if ( get_option( 'timezone_string' ) ) {
        $datetime->setTimezone( new DateTimeZone( masvideos_timezone_string() ) );
    } else {
        $datetime->set_utc_offset( masvideos_timezone_offset() );
    }

    return $datetime;
}

/**
 * MasVideos Timezone - helper to retrieve the timezone string for a site until.
 * a WP core method exists (see https://core.trac.wordpress.org/ticket/24730).
 *
 * Adapted from https://secure.php.net/manual/en/function.timezone-name-from-abbr.php#89155.
 *
 * @since 1.0.0
 * @return string PHP timezone string for the site
 */
function masvideos_timezone_string() {
    // If site timezone string exists, return it.
    $timezone = get_option( 'timezone_string' );
    if ( $timezone ) {
        return $timezone;
    }

    // Get UTC offset, if it isn't set then return UTC.
    $utc_offset = intval( get_option( 'gmt_offset', 0 ) );
    if ( 0 === $utc_offset ) {
        return 'UTC';
    }

    // Adjust UTC offset from hours to seconds.
    $utc_offset *= 3600;

    // Attempt to guess the timezone string from the UTC offset.
    $timezone = timezone_name_from_abbr( '', $utc_offset );
    if ( $timezone ) {
        return $timezone;
    }

    // Last try, guess timezone string manually.
    foreach ( timezone_abbreviations_list() as $abbr ) {
        foreach ( $abbr as $city ) {
            if ( (bool) date( 'I' ) === (bool) $city['dst'] && $city['timezone_id'] && intval( $city['offset'] ) === $utc_offset ) {
                return $city['timezone_id'];
            }
        }
    }

    // Fallback to UTC.
    return 'UTC';
}

/**
 * Get timezone offset in seconds.
 *
 * @since  1.0.0
 * @return float
 */
function masvideos_timezone_offset() {
    $timezone = get_option( 'timezone_string' );

    if ( $timezone ) {
        $timezone_object = new DateTimeZone( $timezone );
        return $timezone_object->getOffset( new DateTime( 'now' ) );
    } else {
        return floatval( get_option( 'gmt_offset', 0 ) ) * HOUR_IN_SECONDS;
    }
}

/**
 * Callback which can flatten post meta (gets the first value if it's an array).
 *
 * @since  1.0.0
 * @param  array $value Value to flatten.
 * @return mixed
 */
function masvideos_flatten_meta_callback( $value ) {
    return is_array( $value ) ? current( $value ) : $value;
}

if ( ! function_exists( 'masvideos_rgb_from_hex' ) ) {

    /**
     * Convert RGB to HEX.
     *
     * @param mixed $color Color.
     *
     * @return array
     */
    function masvideos_rgb_from_hex( $color ) {
        $color = str_replace( '#', '', $color );
        // Convert shorthand colors to full format, e.g. "FFF" -> "FFFFFF".
        $color = preg_replace( '~^(.)(.)(.)$~', '$1$1$2$2$3$3', $color );

        $rgb      = array();
        $rgb['R'] = hexdec( $color[0] . $color[1] );
        $rgb['G'] = hexdec( $color[2] . $color[3] );
        $rgb['B'] = hexdec( $color[4] . $color[5] );

        return $rgb;
    }
}

if ( ! function_exists( 'masvideos_hex_darker' ) ) {

    /**
     * Make HEX color darker.
     *
     * @param mixed $color  Color.
     * @param int   $factor Darker factor.
     *                      Defaults to 30.
     * @return string
     */
    function masvideos_hex_darker( $color, $factor = 30 ) {
        $base  = masvideos_rgb_from_hex( $color );
        $color = '#';

        foreach ( $base as $k => $v ) {
            $amount      = $v / 100;
            $amount      = round( $amount * $factor );
            $new_decimal = $v - $amount;

            $new_hex_component = dechex( $new_decimal );
            if ( strlen( $new_hex_component ) < 2 ) {
                $new_hex_component = '0' . $new_hex_component;
            }
            $color .= $new_hex_component;
        }

        return $color;
    }
}

if ( ! function_exists( 'masvideos_hex_lighter' ) ) {

    /**
     * Make HEX color lighter.
     *
     * @param mixed $color  Color.
     * @param int   $factor Lighter factor.
     *                      Defaults to 30.
     * @return string
     */
    function masvideos_hex_lighter( $color, $factor = 30 ) {
        $base  = masvideos_rgb_from_hex( $color );
        $color = '#';

        foreach ( $base as $k => $v ) {
            $amount      = 255 - $v;
            $amount      = $amount / 100;
            $amount      = round( $amount * $factor );
            $new_decimal = $v + $amount;

            $new_hex_component = dechex( $new_decimal );
            if ( strlen( $new_hex_component ) < 2 ) {
                $new_hex_component = '0' . $new_hex_component;
            }
            $color .= $new_hex_component;
        }

        return $color;
    }
}

if ( ! function_exists( 'masvideos_hex_is_light' ) ) {

    /**
     * Determine whether a hex color is light.
     *
     * @param mixed $color Color.
     * @return bool  True if a light color.
     */
    function masvideos_hex_is_light( $color ) {
        $hex = str_replace( '#', '', $color );

        $c_r = hexdec( substr( $hex, 0, 2 ) );
        $c_g = hexdec( substr( $hex, 2, 2 ) );
        $c_b = hexdec( substr( $hex, 4, 2 ) );

        $brightness = ( ( $c_r * 299 ) + ( $c_g * 587 ) + ( $c_b * 114 ) ) / 1000;

        return $brightness > 155;
    }
}

if ( ! function_exists( 'masvideos_light_or_dark' ) ) {

    /**
     * Detect if we should use a light or dark color on a background color.
     *
     * @param mixed  $color Color.
     * @param string $dark  Darkest reference.
     *                      Defaults to '#000000'.
     * @param string $light Lightest reference.
     *                      Defaults to '#FFFFFF'.
     * @return string
     */
    function masvideos_light_or_dark( $color, $dark = '#000000', $light = '#FFFFFF' ) {
        return masvideos_hex_is_light( $color ) ? $dark : $light;
    }
}

if ( ! function_exists( 'masvideos_format_hex' ) ) {

    /**
     * Format string as hex.
     *
     * @param string $hex HEX color.
     * @return string|null
     */
    function masvideos_format_hex( $hex ) {
        $hex = trim( str_replace( '#', '', $hex ) );

        if ( strlen( $hex ) === 3 ) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        return $hex ? '#' . $hex : null;
    }
}

/**
 * Wrapper for mb_strtoupper which see's if supported first.
 *
 * @since  1.0.0
 * @param  string $string String to format.
 * @return string
 */
function masvideos_strtoupper( $string ) {
    return function_exists( 'mb_strtoupper' ) ? mb_strtoupper( $string ) : strtoupper( $string );
}

/**
 * Make a string lowercase.
 * Try to use mb_strtolower() when available.
 *
 * @since  1.0.0
 * @param  string $string String to format.
 * @return string
 */
function masvideos_strtolower( $string ) {
    return function_exists( 'mb_strtolower' ) ? mb_strtolower( $string ) : strtolower( $string );
}

/**
 * Trim a string and append a suffix.
 *
 * @param  string  $string String to trim.
 * @param  integer $chars  Amount of characters.
 *                         Defaults to 200.
 * @param  string  $suffix Suffix.
 *                         Defaults to '...'.
 * @return string
 */
function masvideos_trim_string( $string, $chars = 200, $suffix = '...' ) {
    if ( strlen( $string ) > $chars ) {
        if ( function_exists( 'mb_substr' ) ) {
            $string = mb_substr( $string, 0, ( $chars - mb_strlen( $suffix ) ) ) . $suffix;
        } else {
            $string = substr( $string, 0, ( $chars - strlen( $suffix ) ) ) . $suffix;
        }
    }
    return $string;
}

/**
 * Sanitize terms from an attribute text based.
 *
 * @since  1.0.0
 * @param  string $term Term value.
 * @return string
 */
function masvideos_sanitize_term_text_based( $term ) {
    return trim( wp_unslash( strip_tags( $term ) ) );
}

/**
 * Get the price format depending on the currency position.
 *
 * @return string
 */
function  masvideos_get_price_format() {
    $currency_pos = 'left';
    $format       = '%1$s%2$s';

    switch ( $currency_pos ) {
        case 'left':
            $format = '%1$s%2$s';
            break;
        case 'right':
            $format = '%2$s%1$s';
            break;
        case 'left_space':
            $format = '%1$s&nbsp;%2$s';
            break;
        case 'right_space':
            $format = '%2$s&nbsp;%1$s';
            break;
    }

    return apply_filters( ' masvideos_price_format', $format, $currency_pos );
}

/**
 * Return the thousand separator for prices.
 *
 * @since  1.0.0
 * @return string
 */
function  masvideos_get_price_thousand_separator() {
    return stripslashes( apply_filters( ' masvideos_get_price_thousand_separator', ',' ) );
}

/**
 * Return the decimal separator for prices.
 *
 * @since  1.0.0
 * @return string
 */
function  masvideos_get_price_decimal_separator() {
    $separator = apply_filters( ' masvideos_get_price_decimal_separator', '.' );
    return $separator ? stripslashes( $separator ) : '.';
}

/**
 * Return the number of decimals after the decimal point.
 *
 * @since  1.0.0
 * @return int
 */
function  masvideos_get_price_decimals() {
    return absint( apply_filters( ' masvideos_get_price_decimals', 2 ) );
}

/**
 * Format decimal numbers ready for DB storage.
 *
 * Sanitize, remove decimals, and optionally round + trim off zeros.
 *
 * This function does not remove thousands - this should be done before passing a value to the function.
 *
 * @param  float|string $number     Expects either a float or a string with a decimal separator only (no thousands).
 * @param  mixed        $dp number  Number of decimal points to use, blank to use  masvideos_price_num_decimals, or false to avoid all rounding.
 * @param  bool         $trim_zeros From end of string.
 * @return string
 */
function masvideos_format_decimal( $number, $dp = false, $trim_zeros = false ) {
    $locale   = localeconv();
    $decimals = array( masvideos_get_price_decimal_separator(), $locale['decimal_point'], $locale['mon_decimal_point'] );

    // Remove locale from string.
    if ( ! is_float( $number ) ) {
        $number = str_replace( $decimals, '.', $number );
        $number = preg_replace( '/[^0-9\.,-]/', '', masvideos_clean( $number ) );
    }

    if ( false !== $dp ) {
        $dp     = intval( '' === $dp ? masvideos_get_price_decimals() : $dp );
        $number = number_format( floatval( $number ), $dp, '.', '' );
    } elseif ( is_float( $number ) ) {
        // DP is false - don't use number format, just return a string using whatever is given. Remove scientific notation using sprintf.
        $number = str_replace( $decimals, '.', sprintf( '%.' . masvideos_get_rounding_precision() . 'f', $number ) );
        // We already had a float, so trailing zeros are not needed.
        $trim_zeros = true;
    }

    if ( $trim_zeros && strstr( $number, '.' ) ) {
        $number = rtrim( rtrim( $number, '0' ), '.' );
    }

    return $number;
}

/**
 * Convert a float to a string without locale formatting which PHP adds when changing floats to strings.
 *
 * @param  float $float Float value to format.
 * @return string
 */
function masvideos_float_to_string( $float ) {
    if ( ! is_float( $float ) ) {
        return $float;
    }

    $locale = localeconv();
    $string = strval( $float );
    $string = str_replace( $locale['decimal_point'], '.', $string );

    return $string;
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

/**
 * Format a date for output.
 *
 * @since  1.0.0
 * @param  MasVideos_DateTime   $date   Instance of MasVideos_DateTime.
 * @param  string               $format Data format.
 *                                      Defaults to the masvideos_date_format function if not set.
 * @return string
 */
function masvideos_format_datetime( $date, $format = '' ) {
    if ( ! $format ) {
        $format = masvideos_date_format();
    }
    if ( ! is_a( $date, 'MasVideos_DateTime' ) ) {
        return '';
    }
    return $date->date_i18n( $format );
}

/**
 * Process oEmbeds.
 *
 * @since  1.0.0
 * @param  string $content Content.
 * @return string
 */
function masvideos_do_oembeds( $content ) {
    global $wp_embed;

    $content = $wp_embed->autoembed( $content );

    return $content;
}