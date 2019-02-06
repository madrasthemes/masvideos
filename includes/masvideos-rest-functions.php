<?php
/**
 * MasVideos REST Functions
 *
 * Functions for REST specific things.
 *
 * @package MasVideos/Functions
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Parses and formats a date for ISO8601/RFC3339.
 *
 * Required WP 4.4 or later.
 * See https://developer.wordpress.org/reference/functions/mysql_to_rfc3339/
 *
 * @since  1.0.0
 * @param  string|null|MasVideos_DateTime $date Date.
 * @param  bool                    $utc  Send false to get local/offset time.
 * @return string|null ISO8601/RFC3339 formatted datetime.
 */
function masvideos_rest_prepare_date_response( $date, $utc = true ) {
    if ( is_numeric( $date ) ) {
        $date = new MasVideos_DateTime( "@$date", new DateTimeZone( 'UTC' ) );
        $date->setTimezone( new DateTimeZone( masvideos_timezone_string() ) );
    } elseif ( is_string( $date ) ) {
        $date = new MasVideos_DateTime( $date, new DateTimeZone( 'UTC' ) );
        $date->setTimezone( new DateTimeZone( masvideos_timezone_string() ) );
    }

    if ( ! is_a( $date, 'MasVideos_DateTime' ) ) {
        return null;
    }

    // Get timestamp before changing timezone to UTC.
    return gmdate( 'Y-m-d\TH:i:s', $utc ? $date->getTimestamp() : $date->getOffsetTimestamp() );
}

/**
 * Returns image mime types users are allowed to upload via the API.
 *
 * @since  2.6.4
 * @return array
 */
function masvideos_rest_allowed_image_mime_types() {
    return apply_filters(
        'masvideos_rest_allowed_image_mime_types', array(
            'jpg|jpeg|jpe' => 'image/jpeg',
            'gif'          => 'image/gif',
            'png'          => 'image/png',
            'bmp'          => 'image/bmp',
            'tiff|tif'     => 'image/tiff',
            'ico'          => 'image/x-icon',
        )
    );
}

/**
 * Upload image from URL.
 *
 * @since 1.0.0
 * @param string $image_url Image URL.
 * @return array|WP_Error Attachment data or error message.
 */
function masvideos_rest_upload_image_from_url( $image_url ) {
    $file_name  = basename( current( explode( '?', $image_url ) ) );
    $parsed_url = wp_parse_url( $image_url );

    // Check parsed URL.
    if ( ! $parsed_url || ! is_array( $parsed_url ) ) {
        /* translators: %s: image URL */
        return new WP_Error( 'masvideos_rest_invalid_image_url', sprintf( __( 'Invalid URL %s.', 'masvideos' ), $image_url ), array( 'status' => 400 ) );
    }

    // Ensure url is valid.
    $image_url = esc_url_raw( $image_url );

    // Get the file.
    $response = wp_safe_remote_get(
        $image_url, array(
            'timeout' => 10,
        )
    );

    if ( is_wp_error( $response ) ) {
        return new WP_Error( 'masvideos_rest_invalid_remote_image_url',
            /* translators: %s: image URL */
            sprintf( __( 'Error getting remote image %s.', 'masvideos' ), $image_url ) . ' '
            /* translators: %s: error message */
            . sprintf( __( 'Error: %s.', 'masvideos' ), $response->get_error_message() ), array( 'status' => 400 )
        );
    } elseif ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
        /* translators: %s: image URL */
        return new WP_Error( 'masvideos_rest_invalid_remote_image_url', sprintf( __( 'Error getting remote image %s.', 'masvideos' ), $image_url ), array( 'status' => 400 ) );
    }

    // Ensure we have a file name and type.
    $wp_filetype = wp_check_filetype( $file_name, masvideos_rest_allowed_image_mime_types() );

    if ( ! $wp_filetype['type'] ) {
        $headers = wp_remote_retrieve_headers( $response );
        if ( isset( $headers['content-disposition'] ) && strstr( $headers['content-disposition'], 'filename=' ) ) {
            $content     = explode( 'filename=', $headers['content-disposition'] );
            $disposition = end( $content );
            $disposition = sanitize_file_name( $disposition );
            $file_name   = $disposition;
        } elseif ( isset( $headers['content-type'] ) && strstr( $headers['content-type'], 'image/' ) ) {
            $file_name = 'image.' . str_replace( 'image/', '', $headers['content-type'] );
        }
        unset( $headers );

        // Recheck filetype.
        $wp_filetype = wp_check_filetype( $file_name, masvideos_rest_allowed_image_mime_types() );

        if ( ! $wp_filetype['type'] ) {
            return new WP_Error( 'masvideos_rest_invalid_image_type', __( 'Invalid image type.', 'masvideos' ), array( 'status' => 400 ) );
        }
    }

    // Upload the file.
    $upload = wp_upload_bits( $file_name, '', wp_remote_retrieve_body( $response ) );

    if ( $upload['error'] ) {
        return new WP_Error( 'masvideos_rest_image_upload_error', $upload['error'], array( 'status' => 400 ) );
    }

    // Get filesize.
    $filesize = filesize( $upload['file'] );

    if ( ! $filesize ) {
        @unlink( $upload['file'] ); // @codingStandardsIgnoreLine
        unset( $upload );

        return new WP_Error( 'masvideos_rest_image_upload_file_error', __( 'Zero size file downloaded.', 'masvideos' ), array( 'status' => 400 ) );
    }

    do_action( 'masvideos_rest_api_uploaded_image_from_url', $upload, $image_url );

    return $upload;
}

/**
 * Set uploaded image as attachment.
 *
 * @since 1.0.0
 * @param array $upload Upload information from wp_upload_bits.
 * @param int   $id Post ID. Default to 0.
 * @return int Attachment ID
 */
function masvideos_rest_set_uploaded_image_as_attachment( $upload, $id = 0 ) {
    $info    = wp_check_filetype( $upload['file'] );
    $title   = '';
    $content = '';

    if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
        include_once ABSPATH . 'wp-admin/includes/image.php';
    }

    $image_meta = wp_read_image_metadata( $upload['file'] );
    if ( $image_meta ) {
        if ( trim( $image_meta['title'] ) && ! is_numeric( sanitize_title( $image_meta['title'] ) ) ) {
            $title = masvideos_clean( $image_meta['title'] );
        }
        if ( trim( $image_meta['caption'] ) ) {
            $content = masvideos_clean( $image_meta['caption'] );
        }
    }

    $attachment = array(
        'post_mime_type' => $info['type'],
        'guid'           => $upload['url'],
        'post_parent'    => $id,
        'post_title'     => $title ? $title : basename( $upload['file'] ),
        'post_content'   => $content,
    );

    $attachment_id = wp_insert_attachment( $attachment, $upload['file'], $id );
    if ( ! is_wp_error( $attachment_id ) ) {
        wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $upload['file'] ) );
    }

    return $attachment_id;
}

/**
 * Validate reports request arguments.
 *
 * @since 1.0.0
 * @param mixed           $value   Value to valdate.
 * @param WP_REST_Request $request Request instance.
 * @param string          $param   Param to validate.
 * @return WP_Error|boolean
 */
function masvideos_rest_validate_reports_request_arg( $value, $request, $param ) {

    $attributes = $request->get_attributes();
    if ( ! isset( $attributes['args'][ $param ] ) || ! is_array( $attributes['args'][ $param ] ) ) {
        return true;
    }
    $args = $attributes['args'][ $param ];

    if ( 'string' === $args['type'] && ! is_string( $value ) ) {
        /* translators: 1: param 2: type */
        return new WP_Error( 'masvideos_rest_invalid_param', sprintf( __( '%1$s is not of type %2$s', 'masvideos' ), $param, 'string' ) );
    }

    if ( 'date' === $args['format'] ) {
        $regex = '#^\d{4}-\d{2}-\d{2}$#';

        if ( ! preg_match( $regex, $value, $matches ) ) {
            return new WP_Error( 'masvideos_rest_invalid_date', __( 'The date you provided is invalid.', 'masvideos' ) );
        }
    }

    return true;
}

/**
 * Encodes a value according to RFC 3986.
 * Supports multidimensional arrays.
 *
 * @since 1.0.0
 * @param string|array $value The value to encode.
 * @return string|array       Encoded values.
 */
function masvideos_rest_urlencode_rfc3986( $value ) {
    if ( is_array( $value ) ) {
        return array_map( 'masvideos_rest_urlencode_rfc3986', $value );
    }

    return str_replace( array( '+', '%7E' ), array( ' ', '~' ), rawurlencode( $value ) );
}