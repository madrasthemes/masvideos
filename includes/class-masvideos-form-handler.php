<?php
/**
 * Handle frontend forms.
 *
 * @version 1.0.0
 * @package MasVideos/Classes/
 */

defined( 'ABSPATH' ) || exit;

/**
 * MasVideos_Form_Handler class.
 */
class MasVideos_Form_Handler {

    /**
     * Hook in methods.
     */
    public static function init() {
        add_action( 'wp_loaded', array( __CLASS__, 'process_login' ), 20 );
        add_action( 'wp_loaded', array( __CLASS__, 'process_registration' ), 20 );
    }

    /**
     * Process the login form.
     */
    public static function process_login() {
        // The global form-login.php template used `_wpnonce` in template versions < 3.3.0.
        $nonce_value = masvideos_get_var( $_REQUEST['masvideos-login-nonce'], masvideos_get_var( $_REQUEST['_wpnonce'], '' ) ); // @codingStandardsIgnoreLine.

        if ( ! empty( $_POST['login'] ) && wp_verify_nonce( $nonce_value, 'masvideos-login' ) ) {

            try {
                $creds = array(
                    'user_login'    => trim( $_POST['username'] ),
                    'user_password' => $_POST['password'],
                    'remember'      => isset( $_POST['rememberme'] ),
                );

                $validation_error = new WP_Error();
                $validation_error = apply_filters( 'masvideos_process_login_errors', $validation_error, $_POST['username'], $_POST['password'] );

                if ( $validation_error->get_error_code() ) {
                    throw new Exception( '<strong>' . __( 'Error:', 'masvideos' ) . '</strong> ' . $validation_error->get_error_message() );
                }

                if ( empty( $creds['user_login'] ) ) {
                    throw new Exception( '<strong>' . __( 'Error:', 'masvideos' ) . '</strong> ' . __( 'Username is required.', 'masvideos' ) );
                }

                // On multisite, ensure user exists on current site, if not add them before allowing login.
                if ( is_multisite() ) {
                    $user_data = get_user_by( is_email( $creds['user_login'] ) ? 'email' : 'login', $creds['user_login'] );

                    if ( $user_data && ! is_user_member_of_blog( $user_data->ID, get_current_blog_id() ) ) {
                        add_user_to_blog( get_current_blog_id(), $user_data->ID, 'contributor' );
                    }
                }

                // Perform the login
                $user = wp_signon( apply_filters( 'masvideos_login_credentials', $creds ), is_ssl() );

                if ( is_wp_error( $user ) ) {
                    $message = $user->get_error_message();
                    $message = str_replace( '<strong>' . esc_html( $creds['user_login'] ) . '</strong>', '<strong>' . esc_html( $creds['user_login'] ) . '</strong>', $message );
                    throw new Exception( $message );
                } else {

                    if ( ! empty( $_POST['redirect'] ) ) {
                        $redirect = $_POST['redirect'];
                    } elseif ( wp_get_raw_referer() ) {
                        $redirect = wp_get_raw_referer();
                    } else {
                        $redirect = admin_url();
                    }

                    wp_redirect( wp_validate_redirect( apply_filters( 'masvideos_login_redirect', remove_query_arg( 'masvideos_error', $redirect ), $user ), admin_url() ) );
                    exit;
                }
            } catch ( Exception $e ) {
                masvideos_add_notice( apply_filters( 'login_errors', $e->getMessage() ), 'error' );
                do_action( 'masvideos_login_failed' );
            }
        }
    }

    /**
     * Process the registration form.
     */
    public static function process_registration() {
        $nonce_value = isset( $_POST['_wpnonce'] ) ? $_POST['_wpnonce'] : '';
        $nonce_value = isset( $_POST['masvideos-register-nonce'] ) ? $_POST['masvideos-register-nonce'] : $nonce_value;

        if ( ! empty( $_POST['register'] ) && wp_verify_nonce( $nonce_value, 'masvideos-register' ) ) {
            $username = 'no' === get_option( 'masvideos_registration_generate_username' ) ? $_POST['username'] : '';
            $password = 'no' === get_option( 'masvideos_registration_generate_password' ) ? $_POST['password'] : '';
            $email    = $_POST['email'];

            try {
                $validation_error = new WP_Error();
                $validation_error = apply_filters( 'masvideos_process_registration_errors', $validation_error, $username, $password, $email );

                if ( $validation_error->get_error_code() ) {
                    throw new Exception( $validation_error->get_error_message() );
                }

                $new_user = masvideos_create_new_user( sanitize_email( $email ), masvideos_clean( $username ), $password );

                if ( is_wp_error( $new_user ) ) {
                    throw new Exception( $new_user->get_error_message() );
                }

                if ( apply_filters( 'masvideos_registration_auth_new_user', true, $new_user ) ) {
                    masvideos_set_user_auth_cookie( $new_user );
                }

                if ( ! empty( $_POST['redirect'] ) ) {
                    $redirect = wp_sanitize_redirect( $_POST['redirect'] );
                } elseif ( wp_get_raw_referer() ) {
                    $redirect = wp_get_raw_referer();
                } else {
                    $redirect = admin_url();
                }

                wp_redirect( wp_validate_redirect( apply_filters( 'masvideos_registration_redirect', $redirect ), admin_url() ) );
                exit;

            } catch ( Exception $e ) {
                masvideos_add_notice( '<strong>' . __( 'Error:', 'masvideos' ) . '</strong> ' . $e->getMessage(), 'error' );
                do_action( 'masvideos_registration_failed' );
            }
        }
    }
}

MasVideos_Form_Handler::init();
