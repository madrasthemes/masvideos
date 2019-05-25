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
        add_action( 'wp_loaded', array( __CLASS__, 'edit_playlist' ), 20 );
        add_action( 'wp_loaded', array( __CLASS__, 'delete_playlist' ), 20 );
        add_action( 'wp_loaded', array( __CLASS__, 'edit_video' ), 20 );
        add_action( 'wp_loaded', array( __CLASS__, 'delete_video' ), 20 );
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

    /**
     * Process the edit video form.
     */
    public static function edit_video() {
        $nonce_value = isset( $_POST['_wpnonce'] ) ? $_POST['_wpnonce'] : '';
        $nonce_value = isset( $_POST['masvideos-edit-video-nonce'] ) ? $_POST['masvideos-edit-video-nonce'] : $nonce_value;

        if ( ! empty( $_POST['edit-video'] ) && wp_verify_nonce( $nonce_value, 'masvideos-edit-video' ) ) {

            $id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
            $video = masvideos_get_video( $id );

            if ( ! $video ) {
                $video = new MasVideos_Video( $id );
            }

            $fields = masvideos_get_edit_video_fields();

            foreach ( $fields as $key => $field ) {
                if ( ! isset( $field['type'] ) ) {
                    $field['type'] = 'text';
                }

                // Get Value.
                if ( 'checkbox' === $field['type'] ) {
                    $value = (int) isset( $_POST[ $key ] );
                } elseif ( 'term-multiselect' === $field['type'] ) {
                    $value = isset( $_POST[ $key ] ) ? masvideos_clean( wp_unslash( $_POST[ $key ] ) ) : array();
                } else {
                    $value = isset( $_POST[ $key ] ) ? masvideos_clean( wp_unslash( $_POST[ $key ] ) ) : '';
                }

                // Hook to allow modification of value.
                $value = apply_filters( 'masvideos_process_upload_video_field_' . $key, $value );

                // Validation: Required fields.
                if ( ! empty( $field['required'] ) && empty( $value ) ) {
                    /* translators: %s: Field name. */
                    masvideos_add_notice( sprintf( __( '%s is a required field.', 'masvideos' ), $field['label'] ), 'error' );
                }

                try {
                    // Set prop in video object.
                    if( $key == 'title' ) {
                        $video->set_name( $value );
                    } elseif( $key === 'video_attachment_id' && !empty( $value ) ) {
                        $video->{"set_$key"}( $value );
                        $video->{"set_video_choice"}( masvideos_clean( wp_unslash( 'video_file' ) ) );
                    } elseif ( is_callable( array( $video, "set_$key" ) ) ) {
                        $video->{"set_$key"}( $value );
                    } else {
                        $video->update_meta_data( $key, $value );
                    }
                } catch ( Exception $e ) {
                    // Set notices. Ignore invalid billing email, since is already validated.
                    masvideos_add_notice( $e->getMessage(), 'error' );
                }
            }

            /**
             * Hook: masvideos_after_save_upload_video_validation.
             *
             * Allow developers to add custom validation logic and throw an error to prevent save.
             *
             * @param array             $fields The fields fields.
             * @param MasVideos_Video   $video The video object being saved.
             */
            do_action( 'masvideos_after_save_upload_video_validation', $fields, $video );

            if ( 0 < masvideos_notice_count( 'error' ) ) {
                return;
            }

            $video->save();

            masvideos_add_notice( __( 'Video uploaded successfully.', 'masvideos' ) );

            do_action( 'masvideos_after_save_upload_video', $video );

            if ( ! empty( $_POST['redirect'] ) ) {
                $redirect = wp_sanitize_redirect( $_POST['redirect'] );
            } elseif ( wp_get_raw_referer() ) {
                $redirect = wp_get_raw_referer();
            } else {
                $redirect = admin_url();
            }

            wp_redirect( wp_validate_redirect( apply_filters( 'masvideos_upload_video_redirect', $redirect ), '#' ) );
            exit;
        }
    }

    /**
     * Process the delete video form.
     */
    public static function delete_video() {
        $nonce_value = isset( $_GET['_wpnonce'] ) ? $_GET['_wpnonce'] : '';
        $nonce_value = isset( $_GET['masvideos-delete-video-nonce'] ) ? $_GET['masvideos-delete-video-nonce'] : $nonce_value;

        if ( ! empty( $_GET['action'] ) && wp_verify_nonce( $nonce_value, 'masvideos-delete-video' ) ) {
            $id         = isset( $_GET['post'] ) ? intval( $_GET['post'] ) : 0;
            $action     = masvideos_clean( $_GET['action'] );

            try {
                $validation_error = new WP_Error();
                $validation_error = apply_filters( 'masvideos_process_delete_video_errors', $validation_error, $id, $action );

                if ( $validation_error->get_error_code() ) {
                    throw new Exception( $validation_error->get_error_message() );
                }

                if( $action == 'delete' ) {
                    $video = wp_delete_post( $id, true );
                } elseif( $action == 'trash' ) {
                    $video = wp_delete_post( $id );
                }

                if ( is_wp_error( $video ) ) {
                    throw new Exception( $video->get_error_message() );
                }

                if ( ! empty( $_GET['redirect'] ) ) {
                    $redirect = wp_sanitize_redirect( $_GET['redirect'] );
                } elseif ( wp_get_raw_referer() ) {
                    $redirect = wp_get_raw_referer();
                } else {
                    $redirect = admin_url();
                }

                wp_redirect( wp_validate_redirect( apply_filters( 'masvideos_delete_video_redirect', $redirect ), '#' ) );
                exit;

            } catch ( Exception $e ) {
                masvideos_add_notice( '<strong>' . __( 'Error:', 'masvideos' ) . '</strong> ' . $e->getMessage(), 'error' );
                do_action( 'masvideos_delete_video_failed' );
            }
        }
    }

    /**
     * Process the edit playlist form.
     */
    public static function edit_playlist() {
        $nonce_value = isset( $_POST['_wpnonce'] ) ? $_POST['_wpnonce'] : '';
        $nonce_value = isset( $_POST['masvideos-edit-playlist-nonce'] ) ? $_POST['masvideos-edit-playlist-nonce'] : $nonce_value;

        if ( ! empty( $_POST['edit-playlist'] ) && wp_verify_nonce( $nonce_value, 'masvideos-edit-playlist' ) ) {
            $id         = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
            $title      = masvideos_clean( $_POST['title'] );
            $visibility = masvideos_clean( $_POST['visibility'] );
            $post_type  = masvideos_clean( $_POST['post_type'] );

            try {
                $validation_error = new WP_Error();
                $validation_error = apply_filters( 'masvideos_process_edit_playlist_errors', $validation_error, $title, $visibility, $post_type );

                if ( $validation_error->get_error_code() ) {
                    throw new Exception( $validation_error->get_error_message() );
                }

                $args = array(
                    'name'      => $title,
                    'status'    => $visibility,
                );

                switch ( $post_type ) {
                    case 'tv_show_playlist':
                        $playlist = masvideos_update_tv_show_playlist( $id, $args );
                        break;

                    case 'video_playlist':
                        $playlist = masvideos_update_video_playlist( $id, $args );
                        break;

                    case 'movie_playlist':
                        $playlist = masvideos_update_movie_playlist( $id, $args );
                        break;

                    default:
                        throw new Exception( '<strong>' . __( 'Error:', 'masvideos' ) . '</strong> ' . __( 'Posttype is not valid.', 'masvideos' ) );
                        break;
                }

                if ( is_wp_error( $playlist ) ) {
                    throw new Exception( $playlist->get_error_message() );
                }

                if ( ! empty( $_POST['redirect'] ) ) {
                    $redirect = wp_sanitize_redirect( $_POST['redirect'] );
                } elseif ( wp_get_raw_referer() ) {
                    $redirect = wp_get_raw_referer();
                } else {
                    $redirect = admin_url();
                }

                wp_redirect( wp_validate_redirect( apply_filters( 'masvideos_edit_playlist_redirect', $redirect ), '#' ) );
                exit;

            } catch ( Exception $e ) {
                masvideos_add_notice( '<strong>' . __( 'Error:', 'masvideos' ) . '</strong> ' . $e->getMessage(), 'error' );
                do_action( 'masvideos_edit_playlist_failed' );
            }
        }
    }

    /**
     * Process the delete playlist form.
     */
    public static function delete_playlist() {
        $nonce_value = isset( $_GET['_wpnonce'] ) ? $_GET['_wpnonce'] : '';
        $nonce_value = isset( $_GET['masvideos-delete-playlist-nonce'] ) ? $_GET['masvideos-delete-playlist-nonce'] : $nonce_value;

        if ( ! empty( $_GET['action'] ) && wp_verify_nonce( $nonce_value, 'masvideos-delete-playlist' ) ) {
            $id         = isset( $_GET['post'] ) ? intval( $_GET['post'] ) : 0;
            $action     = masvideos_clean( $_GET['action'] );

            try {
                $validation_error = new WP_Error();
                $validation_error = apply_filters( 'masvideos_process_delete_playlist_errors', $validation_error, $id, $action );

                if ( $validation_error->get_error_code() ) {
                    throw new Exception( $validation_error->get_error_message() );
                }

                if( $action == 'delete' ) {
                    $playlist = wp_delete_post( $id, true );
                } elseif( $action == 'trash' ) {
                    $playlist = wp_delete_post( $id );
                }

                if ( is_wp_error( $playlist ) ) {
                    throw new Exception( $playlist->get_error_message() );
                }

                if ( ! empty( $_GET['redirect'] ) ) {
                    $redirect = wp_sanitize_redirect( $_GET['redirect'] );
                } elseif ( wp_get_raw_referer() ) {
                    $redirect = wp_get_raw_referer();
                } else {
                    $redirect = admin_url();
                }

                wp_redirect( wp_validate_redirect( apply_filters( 'masvideos_delete_playlist_redirect', $redirect ), '#' ) );
                exit;

            } catch ( Exception $e ) {
                masvideos_add_notice( '<strong>' . __( 'Error:', 'masvideos' ) . '</strong> ' . $e->getMessage(), 'error' );
                do_action( 'masvideos_delete_playlist_failed' );
            }
        }
    }
}

MasVideos_Form_Handler::init();
