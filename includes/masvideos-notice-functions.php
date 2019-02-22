<?php
/**
 * MasVideos Message Functions
 *
 * Functions for error/message handling and display.
 *
 * @package MasVideos/Functions
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get the count of notices added, either for all notices (default) or for one.
 * particular notice type specified by $notice_type.
 *
 * @since  1.0.0
 * @param  string $notice_type Optional. The name of the notice type - either error, success or notice.
 * @return int
 */
function masvideos_notice_count( $notice_type = '' ) {
    if ( ! did_action( 'masvideos_init' ) ) {
        _doing_it_wrong( __FUNCTION__, __( 'This function should not be called before masvideos_init.', 'masvideos' ), '2.3' );
        return;
    }

    $notice_count = 0;
    $all_notices  = MasVideos()->session->get( 'masvideos_notices' );

    if ( isset( $all_notices[ $notice_type ] ) ) {

        $notice_count = count( $all_notices[ $notice_type ] );

    } elseif ( empty( $notice_type ) ) {

        foreach ( $all_notices as $notices ) {
            $notice_count += count( $notices );
        }
    }

    return $notice_count;
}

/**
 * Check if a notice has already been added.
 *
 * @since  1.0.0
 * @param  string $message The text to display in the notice.
 * @param  string $notice_type Optional. The name of the notice type - either error, success or notice.
 * @return bool
 */
function masvideos_has_notice( $message, $notice_type = 'success' ) {
    if ( ! did_action( 'masvideos_init' ) ) {
        _doing_it_wrong( __FUNCTION__, __( 'This function should not be called before masvideos_init.', 'masvideos' ), '2.3' );
        return false;
    }

    $notices = MasVideos()->session->get( 'masvideos_notices' );
    $notices = isset( $notices[ $notice_type ] ) ? $notices[ $notice_type ] : array();
    return array_search( $message, $notices, true ) !== false;
}

/**
 * Add and store a notice.
 *
 * @since 1.0.0
 * @param string $message The text to display in the notice.
 * @param string $notice_type Optional. The name of the notice type - either error, success or notice.
 */
function masvideos_add_notice( $message, $notice_type = 'success' ) {
    if ( ! did_action( 'masvideos_init' ) ) {
        _doing_it_wrong( __FUNCTION__, __( 'This function should not be called before masvideos_init.', 'masvideos' ), '2.3' );
        return;
    }

    $notices = MasVideos()->session->get( 'masvideos_notices' );

    // Backward compatibility.
    if ( 'success' === $notice_type ) {
        $message = apply_filters( 'masvideos_add_message', $message );
    }

    $notices[ $notice_type ][] = apply_filters( 'masvideos_add_' . $notice_type, $message );

    MasVideos()->session->set( 'masvideos_notices', $notices );
}

/**
 * Set all notices at once.
 *
 * @since 1.0.0
 * @param mixed $notices Array of notices.
 */
function masvideos_set_notices( $notices ) {
    if ( ! did_action( 'masvideos_init' ) ) {
        _doing_it_wrong( __FUNCTION__, __( 'This function should not be called before masvideos_init.', 'masvideos' ), '2.6' );
        return;
    }
    MasVideos()->session->set( 'masvideos_notices', $notices );
}


/**
 * Unset all notices.
 *
 * @since 1.0.0
 */
function masvideos_clear_notices() {
    if ( ! did_action( 'masvideos_init' ) ) {
        _doing_it_wrong( __FUNCTION__, __( 'This function should not be called before masvideos_init.', 'masvideos' ), '2.3' );
        return;
    }
    MasVideos()->session->set( 'masvideos_notices', null );
}

/**
 * Prints messages and errors which are stored in the session, then clears them.
 *
 * @since 1.0.0
 * @param bool $return true to return rather than echo.
 * @return string|null
 */
function masvideos_print_notices( $return = false ) {
    if ( ! did_action( 'masvideos_init' ) ) {
        _doing_it_wrong( __FUNCTION__, __( 'This function should not be called before masvideos_init.', 'masvideos' ), '2.3' );
        return;
    }

    $all_notices  = MasVideos()->session->get( 'masvideos_notices' );
    $notice_types = apply_filters( 'masvideos_notice_types', array( 'error', 'success', 'notice' ) );

    // Buffer output.
    ob_start();

    foreach ( $notice_types as $notice_type ) {
        if ( masvideos_notice_count( $notice_type ) > 0 ) {
            masvideos_get_template( "notices/{$notice_type}.php", array(
                'messages' => array_filter( $all_notices[ $notice_type ] ),
            ) );
        }
    }

    masvideos_clear_notices();

    $notices = masvideos_kses_notice( ob_get_clean() );

    if ( $return ) {
        return $notices;
    }

    echo $notices; // WPCS: XSS ok.
}

/**
 * Print a single notice immediately.
 *
 * @since 1.0.0
 * @param string $message The text to display in the notice.
 * @param string $notice_type Optional. The singular name of the notice type - either error, success or notice.
 */
function masvideos_print_notice( $message, $notice_type = 'success' ) {
    if ( 'success' === $notice_type ) {
        $message = apply_filters( 'masvideos_add_message', $message );
    }

    masvideos_get_template( "notices/{$notice_type}.php", array(
        'messages' => array( apply_filters( 'masvideos_add_' . $notice_type, $message ) ),
    ) );
}

/**
 * Returns all queued notices, optionally filtered by a notice type.
 *
 * @since  1.0.0
 * @param  string $notice_type Optional. The singular name of the notice type - either error, success or notice.
 * @return array|mixed
 */
function masvideos_get_notices( $notice_type = '' ) {
    if ( ! did_action( 'masvideos_init' ) ) {
        _doing_it_wrong( __FUNCTION__, __( 'This function should not be called before masvideos_init.', 'masvideos' ), '2.3' );
        return;
    }

    $all_notices = MasVideos()->session->get( 'masvideos_notices' );

    if ( empty( $notice_type ) ) {
        $notices = $all_notices;
    } elseif ( isset( $all_notices[ $notice_type ] ) ) {
        $notices = $all_notices[ $notice_type ];
    } else {
        $notices = array();
    }

    return $notices;
}

/**
 * Add notices for WP Errors.
 *
 * @param WP_Error $errors Errors.
 */
function masvideos_add_wp_error_notices( $errors ) {
    if ( is_wp_error( $errors ) && $errors->get_error_messages() ) {
        foreach ( $errors->get_error_messages() as $error ) {
            masvideos_add_notice( $error, 'error' );
        }
    }
}

/**
 * Filters out the same tags as wp_kses_post, but allows tabindex for <a> element.
 *
 * @since 1.0.0
 * @param string $message Content to filter through kses.
 * @return string
 */
function masvideos_kses_notice( $message ) {
    return wp_kses( $message,
        array_replace_recursive( // phpcs:ignore PHPCompatibility.PHP.NewFunctions.array_replace_recursiveFound
            wp_kses_allowed_html( 'post' ),
            array(
                'a' => array(
                    'tabindex' => true,
                ),
            )
        )
    );
}
