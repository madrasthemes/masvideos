<?php
/**
 * MasVideos User Functions
 *
 * Functions for users.
 *
 * @package MasVideos/Functions
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'masvideos_create_new_user' ) ) {

    /**
     * Create a new user.
     *
     * @param  string $email User email.
     * @param  string $username User username.
     * @param  string $password User password.
     * @return int|WP_Error Returns WP_Error on failure, Int (user ID) on success.
     */
    function masvideos_create_new_user( $email, $username = '', $password = '' ) {

        // Check the email address.
        if ( empty( $email ) || ! is_email( $email ) ) {
            return new WP_Error( 'registration-error-invalid-email', __( 'Please provide a valid email address.', 'masvideos' ) );
        }

        if ( email_exists( $email ) ) {
            return new WP_Error( 'registration-error-email-exists', apply_filters( 'masvideos_registration_error_email_exists', __( 'An account is already registered with your email address. Please log in.', 'masvideos' ), $email ) );
        }

        // Handle username creation.
        if ( 'no' === get_option( 'masvideos_registration_generate_username' ) || ! empty( $username ) ) {
            $username = sanitize_user( $username );

            if ( empty( $username ) || ! validate_username( $username ) ) {
                return new WP_Error( 'registration-error-invalid-username', __( 'Please enter a valid account username.', 'masvideos' ) );
            }

            if ( username_exists( $username ) ) {
                return new WP_Error( 'registration-error-username-exists', __( 'An account is already registered with that username. Please choose another.', 'masvideos' ) );
            }
        } else {
            $username = sanitize_user( current( explode( '@', $email ) ), true );

            // Ensure username is unique.
            $append     = 1;
            $o_username = $username;

            while ( username_exists( $username ) ) {
                $username = $o_username . $append;
                $append++;
            }
        }

        // Handle password creation.
        if ( 'no' === get_option( 'masvideos_registration_generate_password' ) || ! empty( $password ) ) {
            $password_generated = false;
        } else {
            $password           = wp_generate_password();
            $password_generated = true;
        }

        if ( empty( $password ) ) {
            return new WP_Error( 'registration-error-missing-password', __( 'Please enter an account password.', 'masvideos' ) );
        }

        // Use WP_Error to handle registration errors.
        $errors = new WP_Error();

        do_action( 'masvideos_register_post', $username, $email, $errors );

        $errors = apply_filters( 'masvideos_registration_errors', $errors, $username, $email );

        if ( $errors->get_error_code() ) {
            return $errors;
        }

        $new_user_data = apply_filters(
            'masvideos_new_user_data', array(
                'user_login' => $username,
                'user_pass'  => $password,
                'user_email' => $email,
                'role'       => 'contributor',
            )
        );

        $user_id = wp_insert_user( $new_user_data );

        if ( is_wp_error( $user_id ) ) {
            return new WP_Error( 'registration-error', __( 'Couldn&#8217;t register you&hellip; please contact us if you continue to have problems.', 'masvideos' ) );
        }

        do_action( 'masvideos_created_user', $user_id, $new_user_data, $password_generated );

        return $user_id;
    }
}

/**
 * Login a user (set auth cookie and set global user object).
 *
 * @param int $user_id User ID.
 */
function masvideos_set_user_auth_cookie( $user_id ) {
    global $current_user;

    $current_user = get_user_by( 'id', $user_id ); // WPCS: override ok.

    wp_set_auth_cookie( $user_id, true );
}