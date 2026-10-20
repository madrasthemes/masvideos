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
 * Hostnames allowed as <iframe> embed sources for user-supplied video metadata.
 *
 * Embeds whose iframe `src` host is not in this list are removed when rendered. This
 * intentionally restricts the formerly unlimited "Embed Video" field to known video
 * providers; a site can extend the list via the filter.
 *
 * @since 1.3.5
 * @return string[] Lower-cased hostnames.
 */
function masvideos_get_allowed_video_embed_providers() {
    // Map of EXACT iframe host => list of allowed path prefixes (empty list = any path on that host).
    // These are dedicated player endpoints only, not providers' general web hosts.
    $providers = array(
        'www.youtube.com'          => array( '/embed/' ),
        'youtube.com'              => array( '/embed/' ),
        'www.youtube-nocookie.com' => array( '/embed/' ),
        'youtube-nocookie.com'     => array( '/embed/' ),
        'player.vimeo.com'         => array( '/video/' ),
        'www.dailymotion.com'      => array( '/embed/' ),
        'geo.dailymotion.com'      => array( '/player' ),
        'player.twitch.tv'         => array( '/' ),
        'clips.twitch.tv'          => array( '/embed' ),
        'w.soundcloud.com'         => array( '/player' ),
        'archive.org'              => array( '/embed/' ),
    );

    /**
     * Filter the video embed iframe providers. Keys are EXACT hostnames; values are arrays of
     * allowed URL path prefixes (an empty array allows any path on that host). This is the
     * server-side extension point for site owners who need additional trusted providers;
     * untrusted content cannot reach it.
     *
     * @since 1.3.5
     * @param array $providers host => string[] path prefixes.
     */
    $providers = (array) apply_filters( 'masvideos_allowed_video_embed_providers', $providers );

    // Normalise host keys to lower-case.
    $normalised = array();
    foreach ( $providers as $host => $prefixes ) {
        $normalised[ strtolower( (string) $host ) ] = (array) $prefixes;
    }

    return $normalised;
}

/**
 * Back-compat accessor: the list of allowed embed hostnames (derived from the provider map).
 *
 * @since 1.3.5
 * @return string[] Lower-cased hostnames.
 */
function masvideos_get_allowed_video_embed_hosts() {
    return array_keys( masvideos_get_allowed_video_embed_providers() );
}

/**
 * Validate a single iframe `src` URL against the provider allowlist and URL-safety rules.
 *
 * Requires: https scheme; no userinfo (user:pass@); default port only; an EXACT host match in
 * the provider map (no subdomain/suffix matching); and a path matching one of that host's
 * allowed prefixes. Malformed URLs are rejected.
 *
 * @since 1.3.5
 * @param string $src Iframe source URL.
 * @return bool
 */
function masvideos_is_allowed_embed_iframe_src( $src ) {
    $src = (string) $src;

    if ( '' === $src ) {
        return false;
    }

    $parts = wp_parse_url( $src );

    if ( ! is_array( $parts ) || empty( $parts['scheme'] ) || empty( $parts['host'] ) ) {
        return false;
    }

    if ( 'https' !== strtolower( $parts['scheme'] ) ) {
        return false;
    }

    // Reject embedded credentials.
    if ( ! empty( $parts['user'] ) || ! empty( $parts['pass'] ) ) {
        return false;
    }

    // Reject non-default ports.
    if ( isset( $parts['port'] ) && 443 !== (int) $parts['port'] ) {
        return false;
    }

    $host      = strtolower( $parts['host'] );
    $path      = ( isset( $parts['path'] ) && '' !== $parts['path'] ) ? $parts['path'] : '/';
    $providers = masvideos_get_allowed_video_embed_providers();

    if ( ! isset( $providers[ $host ] ) ) {
        return false;
    }

    $prefixes = $providers[ $host ];

    if ( empty( $prefixes ) ) {
        return true;
    }

    foreach ( $prefixes as $prefix ) {
        if ( 0 === strpos( $path, (string) $prefix ) ) {
            return true;
        }
    }

    return false;
}

/**
 * Remove <iframe> elements whose `src` is not an allowed, well-formed provider player URL.
 *
 * @since 1.3.5
 * @param string $html Already-KSES'd HTML.
 * @return string
 */
function masvideos_restrict_iframe_src_hosts( $html ) {
    $html = (string) $html;

    if ( '' === trim( $html ) || false === stripos( $html, '<iframe' ) ) {
        return $html;
    }

    if ( ! class_exists( 'DOMDocument' ) ) {
        // Cannot verify hosts reliably; fail closed by removing all iframes.
        $html = preg_replace( '#<iframe\b[^>]*>.*?</iframe>#is', '', $html );
        return (string) preg_replace( '#<iframe\b[^>]*/?>#i', '', (string) $html );
    }

    $dom             = new DOMDocument();
    $libxml_previous = libxml_use_internal_errors( true );
    $dom->loadHTML( '<?xml encoding="utf-8"?><div data-masvideos-embed-root="1">' . $html . '</div>', LIBXML_NOERROR | LIBXML_NOWARNING );
    libxml_clear_errors();
    libxml_use_internal_errors( $libxml_previous );

    foreach ( iterator_to_array( $dom->getElementsByTagName( 'iframe' ) ) as $iframe ) {
        if ( ! masvideos_is_allowed_embed_iframe_src( (string) $iframe->getAttribute( 'src' ) ) && $iframe->parentNode ) {
            $iframe->parentNode->removeChild( $iframe );
        }
    }

    $root = $dom->getElementsByTagName( 'div' )->item( 0 );

    if ( ! $root ) {
        return '';
    }

    $out = '';
    foreach ( $root->childNodes as $child ) {
        $out .= $dom->saveHTML( $child );
    }

    return $out;
}

/**
 * KSES a user-supplied video embed to a safe, script-free iframe/player policy, then enforce
 * the iframe host allowlist. No shortcodes, blocks, scripts or event handlers survive.
 *
 * @since 1.3.5
 * @param string $html Embed markup.
 * @return string
 */
function masvideos_kses_video_embed( $html ) {
    $allowed = array(
        'iframe' => array(
            'src'             => true,
            'width'           => true,
            'height'          => true,
            'frameborder'     => true,
            'allow'           => true,
            'allowfullscreen' => true,
            'title'           => true,
            'loading'         => true,
            'referrerpolicy'  => true,
            'sandbox'         => true,
            'class'           => true,
            // NOTE: 'style' and 'name' are intentionally omitted — 'style' enables CSS-based
            // overlay/clickjacking and 'name' enables frame-targeting, neither of which a
            // legitimate provider embed needs.
        ),
        'video'  => array(
            'src'         => true,
            'width'       => true,
            'height'      => true,
            'controls'    => true,
            'poster'      => true,
            'preload'     => true,
            'loop'        => true,
            'muted'       => true,
            'autoplay'    => true,
            'playsinline' => true,
            'class'       => true,
            'style'       => true,
        ),
        'source' => array(
            'src'  => true,
            'type' => true,
        ),
        'a'      => array(
            'href'   => true,
            'target' => true,
            'rel'    => true,
            'class'  => true,
            'title'  => true,
        ),
        'img'    => array(
            'src'     => true,
            'alt'     => true,
            'width'   => true,
            'height'  => true,
            'class'   => true,
            'style'   => true,
            'loading' => true,
        ),
        'p'      => array( 'class' => true ),
        'br'     => array(),
        'span'   => array(
            'class' => true,
            'style' => true,
        ),
        'div'    => array(
            'class' => true,
            'style' => true,
        ),
    );

    /**
     * Filter the allowed HTML for user-supplied video embeds (wp_kses format).
     *
     * @since 1.3.5
     * @param array $allowed Allowed tags/attributes.
     */
    $allowed = apply_filters( 'masvideos_video_embed_allowed_html', $allowed );

    $clean = wp_kses( (string) $html, $allowed, array( 'http', 'https' ) );

    return masvideos_restrict_iframe_src_hosts( $clean );
}

/**
 * Get safe oEmbed HTML for a provider URL, with remote discovery disabled.
 *
 * Only WordPress's registered oEmbed providers (YouTube, Vimeo, …) are used; discovery is
 * disabled so no HTTP request is made to an arbitrary, user-supplied URL.
 *
 * @since 1.3.5
 * @param string $url URL.
 * @return string Embed HTML, or '' when the provider is unsupported.
 */
function masvideos_get_safe_oembed_html( $url ) {
    $url = esc_url_raw( (string) $url );

    if ( '' === $url ) {
        return '';
    }

    $html = wp_oembed_get( $url, array( 'discover' => false ) );

    if ( ! is_string( $html ) || '' === $html ) {
        return '';
    }

    // The provider response is still externally-controlled markup: never return it raw. Run it
    // through the same embed sanitiser (script/handler stripping + iframe provider validation),
    // so a malformed or malicious provider response cannot inject executable markup.
    return masvideos_kses_video_embed( $html );
}

/**
 * Safely render a user-supplied video source (direct file URL, embed markup, or provider URL)
 * WITHOUT running the_content / do_shortcode / do_blocks on untrusted metadata.
 *
 * @since 1.3.5
 * @param string $choice Source type; only its suffix (_file | _embed | _url) is significant.
 * @param string $src    Stored source value.
 * @return string Safe HTML.
 */
function masvideos_render_video_source( $choice, $src ) {
    $choice = (string) $choice;
    $src    = (string) $src;

    if ( '' === trim( $src ) ) {
        return '';
    }

    $suffix = strrchr( $choice, '_' ); // "_file" | "_embed" | "_url".

    if ( '_file' === $suffix ) {
        return wp_video_shortcode( array( 'src' => esc_url( $src ) ) );
    }

    if ( '_embed' === $suffix ) {
        return masvideos_kses_video_embed( $src );
    }

    if ( '_url' === $suffix ) {
        $url = esc_url_raw( trim( $src ) );

        if ( '' === $url ) {
            return '';
        }

        // Only http(s) URLs may be rendered (reject ftp/data/javascript/etc. media URLs).
        $scheme = strtolower( (string) wp_parse_url( $url, PHP_URL_SCHEME ) );
        if ( ! in_array( $scheme, array( 'http', 'https' ), true ) ) {
            return '';
        }

        // Prefer oEmbed for supported providers (YouTube, Vimeo, …), discovery disabled.
        $oembed = masvideos_get_safe_oembed_html( $url );
        if ( $oembed ) {
            return $oembed;
        }

        // Native player for a direct media file.
        $filetype = wp_check_filetype( strtok( $url, '?' ) );
        if ( ! empty( $filetype['ext'] ) && in_array( $filetype['ext'], wp_get_video_extensions(), true ) ) {
            return wp_video_shortcode( array( 'src' => esc_url( $url ) ) );
        }

        // Otherwise a safe, escaped link (no raw HTML, no shortcode execution).
        return sprintf( '<a href="%1$s" rel="nofollow noopener noreferrer" target="_blank">%2$s</a>', esc_url( $url ), esc_html( $url ) );
    }

    return '';
}

/**
 * Reduce user-supplied text to plain, safe text for structured data: strip shortcodes (so
 * they are never executed) and all HTML tags.
 *
 * @since 1.3.5
 * @param string $text Text.
 * @return string
 */
function masvideos_safe_structured_data_text( $text ) {
    return wp_strip_all_tags( strip_shortcodes( (string) $text ) );
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
            // WordPress forces the PHP default timezone to UTC, so date( 'I' ) and gmdate( 'I' ) are
            // equivalent here (both report UTC's DST flag); gmdate() is used to satisfy the sniff
            // without changing this fallback's result.
            if ( (bool) gmdate( 'I' ) === (bool) $city['dst'] && $city['timezone_id'] && intval( $city['offset'] ) === $utc_offset ) {
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
    return trim( wp_unslash( wp_strip_all_tags( $term ) ) );
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