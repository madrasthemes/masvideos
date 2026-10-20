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
        add_action( 'template_redirect', array( __CLASS__, 'save_account_details' ) );
        add_action( 'wp_loaded', array( __CLASS__, 'process_login' ), 20 );
        add_action( 'wp_loaded', array( __CLASS__, 'process_registration' ), 20 );
        add_action( 'wp_loaded', array( __CLASS__, 'edit_playlist' ), 20 );
        add_action( 'wp_loaded', array( __CLASS__, 'delete_playlist' ), 20 );
        add_action( 'wp_loaded', array( __CLASS__, 'edit_video' ), 20 );
        add_action( 'wp_loaded', array( __CLASS__, 'delete_video' ), 20 );
    }

    /**
     * Save the password/account details and redirect back to the my account page.
     */
    public static function save_account_details() {
        $nonce_value = masvideos_get_var( $_REQUEST['save-account-details-nonce'], masvideos_get_var( $_REQUEST['_wpnonce'], '' ) ); // @codingStandardsIgnoreLine.

        if ( ! wp_verify_nonce( $nonce_value, 'save_account_details' ) ) {
            return;
        }

        if ( empty( $_POST['action'] ) || 'save_account_details' !== $_POST['action'] ) {
            return;
        }

        // wc_nocache_headers();

        $user_id = get_current_user_id();

        if ( $user_id <= 0 ) {
            return;
        }

        $account_first_name   = ! empty( $_POST['account_first_name'] ) ? masvideos_clean( wp_unslash( $_POST['account_first_name'] ) ) : '';
        $account_last_name    = ! empty( $_POST['account_last_name'] ) ? masvideos_clean( wp_unslash( $_POST['account_last_name'] ) ) : '';
        $account_display_name = ! empty( $_POST['account_display_name'] ) ? masvideos_clean( wp_unslash( $_POST['account_display_name'] ) ) : '';
        $account_email        = ! empty( $_POST['account_email'] ) ? sanitize_email( wp_unslash( $_POST['account_email'] ) ) : '';
        $pass_cur             = ! empty( $_POST['password_current'] ) ? $_POST['password_current'] : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
        $pass1                = ! empty( $_POST['password_1'] ) ? $_POST['password_1'] : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
        $pass2                = ! empty( $_POST['password_2'] ) ? $_POST['password_2'] : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
        $save_pass            = true;

        // Current user data.
        $current_user       = get_user_by( 'id', $user_id );
        $current_first_name = $current_user->first_name;
        $current_last_name  = $current_user->last_name;
        $current_email      = $current_user->user_email;

        // New user data.
        $user               = new stdClass();
        $user->ID           = $user_id;
        $user->first_name   = $account_first_name;
        $user->last_name    = $account_last_name;
        $user->display_name = $account_display_name;

        // Prevent display name to be changed to email.
        if ( is_email( $account_display_name ) ) {
            masvideos_add_notice( __( 'Display name cannot be changed to email address due to privacy concern.', 'masvideos' ), 'error' );
        }

        // Handle required fields.
        $required_fields = apply_filters(
            'masvideos_save_account_details_required_fields',
            array(
                'account_first_name'   => __( 'First name', 'masvideos' ),
                'account_last_name'    => __( 'Last name', 'masvideos' ),
                'account_display_name' => __( 'Display name', 'masvideos' ),
                'account_email'        => __( 'Email address', 'masvideos' ),
            )
        );

        foreach ( $required_fields as $field_key => $field_name ) {
            if ( empty( $_POST[ $field_key ] ) ) {
                /* translators: %s: Field name. */
                masvideos_add_notice( sprintf( __( '%s is a required field.', 'masvideos' ), '<strong>' . esc_html( $field_name ) . '</strong>' ), 'error', array( 'id' => $field_key ) );
            }
        }

        if ( $account_email ) {
            $account_email = sanitize_email( $account_email );
            if ( ! is_email( $account_email ) ) {
                masvideos_add_notice( __( 'Please provide a valid email address.', 'masvideos' ), 'error' );
            } elseif ( email_exists( $account_email ) && $account_email !== $current_user->user_email ) {
                masvideos_add_notice( __( 'This email address is already registered.', 'masvideos' ), 'error' );
            }
            $user->user_email = $account_email;
        }

        if ( ! empty( $pass_cur ) && empty( $pass1 ) && empty( $pass2 ) ) {
            masvideos_add_notice( __( 'Please fill out all password fields.', 'masvideos' ), 'error' );
            $save_pass = false;
        } elseif ( ! empty( $pass1 ) && empty( $pass_cur ) ) {
            masvideos_add_notice( __( 'Please enter your current password.', 'masvideos' ), 'error' );
            $save_pass = false;
        } elseif ( ! empty( $pass1 ) && empty( $pass2 ) ) {
            masvideos_add_notice( __( 'Please re-enter your password.', 'masvideos' ), 'error' );
            $save_pass = false;
        } elseif ( ( ! empty( $pass1 ) || ! empty( $pass2 ) ) && $pass1 !== $pass2 ) {
            masvideos_add_notice( __( 'New passwords do not match.', 'masvideos' ), 'error' );
            $save_pass = false;
        } elseif ( ! empty( $pass1 ) && ! wp_check_password( $pass_cur, $current_user->user_pass, $current_user->ID ) ) {
            masvideos_add_notice( __( 'Your current password is incorrect.', 'masvideos' ), 'error' );
            $save_pass = false;
        }

        if ( $pass1 && $save_pass ) {
            $user->user_pass = $pass1;
        }

        // Allow plugins to return their own errors.
        $errors = new WP_Error();
        do_action_ref_array( 'masvideos_save_account_details_errors', array( &$errors, &$user ) );

        if ( $errors->get_error_messages() ) {
            foreach ( $errors->get_error_messages() as $error ) {
                masvideos_add_notice( $error, 'error' );
            }
        }

        if ( masvideos_notice_count( 'error' ) === 0 ) {
            wp_update_user( $user );

            masvideos_add_notice( __( 'Account details changed successfully.', 'masvideos' ) );

            do_action( 'masvideos_save_account_details', $user->ID );

            wp_safe_redirect( masvideos_get_page_permalink( 'myaccount' ) );
            exit;
        }
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

                // Perform the login
                $user = wp_signon( apply_filters( 'masvideos_login_credentials', $creds ), is_ssl() );

                if ( is_wp_error( $user ) ) {
                    $message = $user->get_error_message();
                    $message = str_replace( '<strong>' . esc_html( $creds['user_login'] ) . '</strong>', '<strong>' . esc_html( $creds['user_login'] ) . '</strong>', $message );
                    throw new Exception( $message );
                } else {

                    // Multisite: optionally enrol the AUTHENTICATED user into the current site. This
                    // runs only after wp_signon() has returned a valid WP_User (so a failed login can
                    // never change site membership), is disabled by default, and never lets request
                    // input choose or escalate the role (MV-10).
                    self::maybe_add_user_to_blog_on_login( $user );

                    if ( ! empty( $_POST['redirect'] ) ) {
                        $redirect = $_POST['redirect'];
                    } elseif ( wp_get_raw_referer() ) {
                        $redirect = wp_get_raw_referer();
                    } else {
                        $redirect = admin_url();
                    }

                    wp_safe_redirect( apply_filters( 'masvideos_login_redirect', remove_query_arg( 'masvideos_error', $redirect ), $user ) );
                    exit;
                }
            } catch ( Exception $e ) {
                masvideos_add_notice( apply_filters( 'login_errors', $e->getMessage() ), 'error' );
                do_action( 'masvideos_login_failed' );
            }
        }
    }

    /**
     * Optionally add an authenticated user to the current site on multisite (MV-10).
     *
     * Enrolment is disabled by default. When a site explicitly opts in via the trusted
     * `masvideos_add_user_to_blog_on_login` filter, the user added is the exact authenticated
     * account returned by wp_signon() (never a separately resolved submitted username), the role
     * comes only from the trusted `masvideos_add_user_to_blog_on_login_role` filter (defaulting to
     * the least-privileged subscriber and validated against the registered roles — request input
     * can neither select nor escalate it), and any failure is swallowed so it cannot disrupt the
     * already-successful login. Existing members are a no-op.
     *
     * @param WP_User $user The authenticated user returned by wp_signon().
     */
    private static function maybe_add_user_to_blog_on_login( $user ) {
        if ( ! is_multisite() ) {
            return;
        }

        if ( ! ( $user instanceof WP_User ) || ! $user->exists() ) {
            return;
        }

        if ( ! apply_filters( 'masvideos_add_user_to_blog_on_login', false ) ) {
            return;
        }

        $blog_id = get_current_blog_id();

        if ( is_user_member_of_blog( $user->ID, $blog_id ) ) {
            return;
        }

        // The role is supplied only by the trusted filter; it defaults to subscriber and must be a
        // currently registered role. No request value participates in choosing it.
        $role = apply_filters( 'masvideos_add_user_to_blog_on_login_role', 'subscriber', $user );

        if ( ! is_string( $role ) || '' === $role || ! wp_roles()->is_role( $role ) ) {
            return;
        }

        $result = add_user_to_blog( $blog_id, $user->ID, $role );

        if ( is_wp_error( $result ) ) {
            // A failed enrolment must not affect the successful authentication.
            return;
        }
    }

    /**
     * Process the registration form.
     */
    public static function process_registration() {
        $nonce_value = isset( $_POST['_wpnonce'] ) ? $_POST['_wpnonce'] : '';
        $nonce_value = isset( $_POST['masvideos-register-nonce'] ) ? $_POST['masvideos-register-nonce'] : $nonce_value;

        if ( ! empty( $_POST['register'] ) && wp_verify_nonce( $nonce_value, 'masvideos-register' ) ) {
            // Registration must respect the site setting at request time. Checking it server-side
            // here (not just hiding the form) ensures a retained/valid nonce cannot be replayed to
            // create an account after registration has been disabled (MV-11).
            if ( 'yes' !== get_option( 'masvideos_enable_myaccount_registration' ) ) {
                masvideos_add_notice( '<strong>' . __( 'Error:', 'masvideos' ) . '</strong> ' . __( 'User registration is currently not allowed.', 'masvideos' ), 'error' );
                do_action( 'masvideos_registration_failed' );
                return;
            }

            // An already authenticated user must not be able to trigger a new-account registration.
            if ( is_user_logged_in() ) {
                masvideos_add_notice( '<strong>' . __( 'Error:', 'masvideos' ) . '</strong> ' . __( 'You are already logged in.', 'masvideos' ), 'error' );
                do_action( 'masvideos_registration_failed' );
                return;
            }

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

                wp_safe_redirect( apply_filters( 'masvideos_registration_redirect', $redirect ) );
                exit;

            } catch ( Exception $e ) {
                masvideos_add_notice( '<strong>' . __( 'Error:', 'masvideos' ) . '</strong> ' . $e->getMessage(), 'error' );
                do_action( 'masvideos_registration_failed' );
            }
        }
    }

    /**
     * Process the edit video form.
     *
     * Handles both creating a new video and editing an existing one:
     *
     * - Editing an existing post requires it to be a `video` and the user to pass
     *   `current_user_can( 'edit_post', $id )` (identical to the My Account edit screen gate).
     * - Creating a new video requires the `edit_videos` capability (filterable).
     *
     * The submitted status is restricted to the statuses actually offered by the form, so
     * a crafted request cannot publish (or set an internal status) when the site has not
     * opted in via `masvideos_allow_users_to_publish_video`.
     */
    public static function edit_video() {
        if ( empty( $_POST['edit-video'] ) ) {
            return;
        }

        $nonce_value = isset( $_POST['masvideos-edit-video-nonce'] ) && is_scalar( $_POST['masvideos-edit-video-nonce'] )
            ? sanitize_text_field( wp_unslash( $_POST['masvideos-edit-video-nonce'] ) )
            : ( isset( $_POST['_wpnonce'] ) && is_scalar( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : '' );

        if ( ! wp_verify_nonce( $nonce_value, 'masvideos-edit-video' ) ) {
            return;
        }

        if ( ! is_user_logged_in() ) {
            return;
        }

        $id = isset( $_POST['id'] ) && is_scalar( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0;

        // Authorize, then resolve the object. Authorization happens before any post, meta or taxonomy write.
        if ( $id > 0 ) {
            // Editing an existing object: it must be a video the user may edit.
            if ( 'video' !== get_post_type( $id ) || ! current_user_can( 'edit_post', $id ) ) {
                masvideos_add_notice( '<strong>' . __( 'Error:', 'masvideos' ) . '</strong> ' . __( 'You are not allowed to edit this video.', 'masvideos' ), 'error' );
                do_action( 'masvideos_edit_video_failed' );
                return;
            }

            try {
                $video = masvideos_get_video( $id );
            } catch ( Exception $e ) {
                $video = false;
            }

            if ( ! $video ) {
                masvideos_add_notice( '<strong>' . __( 'Error:', 'masvideos' ) . '</strong> ' . __( 'Invalid video.', 'masvideos' ), 'error' );
                do_action( 'masvideos_edit_video_failed' );
                return;
            }
        } else {
            // Creating a new video requires an explicit capability (independent of playlist features).
            $create_capability = apply_filters( 'masvideos_frontend_create_video_capability', 'edit_videos' );

            if ( ! current_user_can( $create_capability ) ) {
                masvideos_add_notice( '<strong>' . __( 'Error:', 'masvideos' ) . '</strong> ' . __( 'You are not allowed to upload videos.', 'masvideos' ), 'error' );
                do_action( 'masvideos_edit_video_failed' );
                return;
            }

            $video = new MasVideos_Video( 0 );
        }

        $fields = masvideos_get_edit_video_fields();

        // Statuses actually offered by the form. 'publish' is only present when the site opts in
        // via masvideos_allow_users_to_publish_video (see masvideos_get_edit_video_fields()).
        $allowed_statuses = ( isset( $fields['status']['options'] ) && is_array( $fields['status']['options'] ) ) ? array_keys( $fields['status']['options'] ) : array( 'pending' );
        $default_status   = in_array( 'pending', $allowed_statuses, true ) ? 'pending' : ( ! empty( $allowed_statuses ) ? $allowed_statuses[0] : 'pending' );

        foreach ( $fields as $key => $field ) {
            if ( ! isset( $field['type'] ) ) {
                $field['type'] = 'text';
            }

            // Get Value.
            if ( 'checkbox' === $field['type'] ) {
                $value = (int) isset( $_POST[ $key ] );
            } elseif ( 'status' === $key ) {
                // Restrict to the statuses the form actually offers; never trust the posted value.
                $submitted = isset( $_POST[ $key ] ) && is_scalar( $_POST[ $key ] ) ? masvideos_clean( wp_unslash( $_POST[ $key ] ) ) : '';

                if ( in_array( $submitted, $allowed_statuses, true ) ) {
                    // A valid, offered status chosen by an authorized user.
                    $value = $submitted;
                } elseif ( $id > 0 ) {
                    // Editing an existing video: an invalid/missing status must NOT change the
                    // current status (never silently unpublish or downgrade the post).
                    $current = get_post_status( $id );
                    $value   = $current ? $current : $default_status;
                } else {
                    // Creating a new video: fall back to the safe default.
                    $value = $default_status;
                }
            } elseif ( 'tag-multiselect' === $field['type'] ) {
                // Use the taxonomy declared by the field, never a taxonomy supplied in the request:
                // a submitted `taxonomy` must not let a user assign to or create terms in an arbitrary
                // taxonomy (MV-18). Term creation additionally requires that taxonomy's edit_terms cap.
                $value           = array();
                $raw_tags        = ( isset( $_POST[ $key ] ) && is_array( $_POST[ $key ] ) ) ? masvideos_clean( wp_unslash( $_POST[ $key ] ) ) : array();
                $raw_tags        = array_filter( array_map( 'sanitize_text_field', $raw_tags ) );
                $taxonomy        = isset( $field['taxonomy'] ) ? $field['taxonomy'] : '';
                $taxonomy_object = $taxonomy ? get_taxonomy( $taxonomy ) : false;

                if ( $taxonomy_object && in_array( 'video', (array) $taxonomy_object->object_type, true ) && current_user_can( $taxonomy_object->cap->assign_terms ) && ! empty( $raw_tags ) ) {
                    $can_create_terms = current_user_can( $taxonomy_object->cap->edit_terms );

                    foreach ( $raw_tags as $tag ) {
                        $existing_term = get_term_by( 'name', $tag, $taxonomy );

                        if ( $existing_term ) {
                            $value[] = (int) $existing_term->term_id;
                        } elseif ( $can_create_terms ) {
                            $new_term = wp_insert_term( $tag, $taxonomy );
                            if ( ! is_wp_error( $new_term ) ) {
                                $value[] = (int) $new_term['term_id'];
                            }
                        }
                    }
                }
            } elseif ( 'term-multiselect' === $field['type'] ) {
                // An array of term IDs restricted to the field's own taxonomy. Validating that each
                // submitted ID actually belongs to that taxonomy, and requiring the assign_terms cap,
                // prevents cross-taxonomy / arbitrary-term assignment (MV-18).
                $value           = array();
                $taxonomy        = isset( $field['taxonomy'] ) ? $field['taxonomy'] : '';
                $taxonomy_object = $taxonomy ? get_taxonomy( $taxonomy ) : false;
                $submitted_ids   = ( isset( $_POST[ $key ] ) && is_array( $_POST[ $key ] ) ) ? array_filter( array_map( 'absint', wp_unslash( $_POST[ $key ] ) ) ) : array();

                if ( $taxonomy_object && in_array( 'video', (array) $taxonomy_object->object_type, true ) && current_user_can( $taxonomy_object->cap->assign_terms ) && ! empty( $submitted_ids ) ) {
                    foreach ( $submitted_ids as $term_id ) {
                        $term = get_term( $term_id, $taxonomy );
                        if ( $term && ! is_wp_error( $term ) ) {
                            $value[] = (int) $term_id;
                        }
                    }
                }
            } elseif ( 'video_embed_content' === $key ) {
                $value = isset( $_POST[ $key ] ) && is_scalar( $_POST[ $key ] ) ? masvideos_sanitize_textarea_iframe( stripslashes( $_POST[ $key ] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
            } else {
                $value = isset( $_POST[ $key ] ) && is_scalar( $_POST[ $key ] ) ? masvideos_clean( wp_unslash( $_POST[ $key ] ) ) : '';
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
                if ( 'title' === $key ) {
                    $video->set_name( $value );
                } elseif ( 'video_attachment_id' === $key && ! empty( $value ) ) {
                    $video->{"set_$key"}( $value );
                    $video->{"set_video_choice"}( 'video_file' );
                } elseif ( is_callable( array( $video, "set_$key" ) ) ) {
                    $video->{"set_$key"}( $value );
                } else {
                    $video->update_meta_data( $key, $value );
                }
            } catch ( Exception $e ) {
                masvideos_add_notice( $e->getMessage(), 'error' );
            }
        }

        // "Save as draft" button. 'draft' is always one of the offered statuses.
        if ( isset( $_POST['edit-video'] ) && 'draft' === $_POST['edit-video'] && in_array( 'draft', $allowed_statuses, true ) ) {
            $video->set_status( 'draft' );
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

        if ( ! empty( $_POST['redirect'] ) && is_scalar( $_POST['redirect'] ) ) {
            $redirect = wp_sanitize_redirect( wp_unslash( $_POST['redirect'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        } elseif ( wp_get_raw_referer() ) {
            $redirect = wp_get_raw_referer();
        } else {
            $redirect = masvideos_get_page_permalink( 'myaccount' );
        }

        wp_safe_redirect( wp_validate_redirect( apply_filters( 'masvideos_upload_video_redirect', $redirect ), masvideos_get_page_permalink( 'myaccount' ) ) );
        exit;
    }

    /**
     * Process the delete video form.
     *
     * Deletes or trashes a single `video` post. Requires an authenticated user, a nonce
     * bound to the specific object, a validated post type, and either ownership of the
     * video or the appropriate WordPress deletion capability for that object.
     */
    public static function delete_video() {
        // Only act on explicitly supported actions; leave the request untouched otherwise.
        $action = isset( $_GET['action'] ) && is_scalar( $_GET['action'] ) ? sanitize_key( wp_unslash( $_GET['action'] ) ) : '';

        if ( ! in_array( $action, array( 'delete', 'trash' ), true ) ) {
            return;
        }

        $id = isset( $_GET['post'] ) && is_scalar( $_GET['post'] ) ? absint( wp_unslash( $_GET['post'] ) ) : 0;

        if ( $id <= 0 ) {
            return;
        }

        $nonce_value = isset( $_GET['masvideos-delete-video-nonce'] ) && is_scalar( $_GET['masvideos-delete-video-nonce'] )
            ? sanitize_text_field( wp_unslash( $_GET['masvideos-delete-video-nonce'] ) )
            : ( isset( $_GET['_wpnonce'] ) && is_scalar( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '' );

        // Nonce is bound to the specific object and (implicitly) the deletion operation.
        if ( ! wp_verify_nonce( $nonce_value, 'masvideos-delete-video_' . $id ) ) {
            return;
        }

        // Authorize. Ownership authorizes only a RECOVERABLE trash; permanent deletion
        // requires the WordPress delete capability for the object (which encodes moderation
        // boundaries such as delete_published_videos / delete_private_videos). When the site
        // has disabled Trash (EMPTY_TRASH_DAYS = 0), wp_trash_post() permanently deletes, so
        // we must require the capability even for the 'trash' action — fail closed.
        $allow_owner = ( 'trash' === $action ) && masvideos_trash_is_recoverable();
        $post        = masvideos_get_authorized_account_post( $id, 'video', 'delete_post', $allow_owner );

        if ( ! $post ) {
            masvideos_add_notice( __( 'You are not allowed to delete this video.', 'masvideos' ), 'error' );
            do_action( 'masvideos_delete_video_failed' );
            return;
        }

        try {
            $validation_error = new WP_Error();
            $validation_error = apply_filters( 'masvideos_process_delete_video_errors', $validation_error, $id, $action );

            if ( $validation_error->get_error_code() ) {
                throw new Exception( $validation_error->get_error_message() );
            }

            if ( 'delete' === $action ) {
                $result = wp_delete_post( $id, true );
            } else {
                $result = wp_trash_post( $id );
            }

            if ( ! $result ) {
                throw new Exception( __( 'This video could not be deleted.', 'masvideos' ) );
            }

            if ( ! empty( $_GET['redirect'] ) && is_scalar( $_GET['redirect'] ) ) {
                $redirect = wp_sanitize_redirect( wp_unslash( $_GET['redirect'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
            } elseif ( wp_get_raw_referer() ) {
                $redirect = wp_get_raw_referer();
            } else {
                $redirect = masvideos_get_page_permalink( 'myaccount' );
            }

            wp_safe_redirect( wp_validate_redirect( apply_filters( 'masvideos_delete_video_redirect', $redirect ), masvideos_get_page_permalink( 'myaccount' ) ) );
            exit;

        } catch ( Exception $e ) {
            masvideos_add_notice( '<strong>' . __( 'Error:', 'masvideos' ) . '</strong> ' . $e->getMessage(), 'error' );
            do_action( 'masvideos_delete_video_failed' );
        }
    }

    /**
     * Process the edit playlist form.
     *
     * Creates a new playlist (any logged-in user) or updates an existing one. Updating an
     * existing playlist requires the submitted type to be a supported playlist type, the
     * stored post to be of that exact type, and the user to own it (or hold the object
     * edit capability). Visibility is restricted to the statuses allowed for that playlist
     * type, so another user's private playlist cannot be flipped to public.
     */
    public static function edit_playlist() {
        if ( empty( $_POST['edit-playlist'] ) ) {
            return;
        }

        $nonce_value = isset( $_POST['masvideos-edit-playlist-nonce'] ) && is_scalar( $_POST['masvideos-edit-playlist-nonce'] )
            ? sanitize_text_field( wp_unslash( $_POST['masvideos-edit-playlist-nonce'] ) )
            : ( isset( $_POST['_wpnonce'] ) && is_scalar( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : '' );

        if ( ! wp_verify_nonce( $nonce_value, 'masvideos-edit-playlist' ) ) {
            return;
        }

        if ( ! is_user_logged_in() ) {
            return;
        }

        $id         = isset( $_POST['id'] ) && is_scalar( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0;
        $title      = isset( $_POST['title'] ) && is_scalar( $_POST['title'] ) ? masvideos_clean( wp_unslash( $_POST['title'] ) ) : '';
        $visibility = isset( $_POST['visibility'] ) && is_scalar( $_POST['visibility'] ) ? masvideos_clean( wp_unslash( $_POST['visibility'] ) ) : '';
        $post_type  = isset( $_POST['post_type'] ) && is_scalar( $_POST['post_type'] ) ? masvideos_clean( wp_unslash( $_POST['post_type'] ) ) : '';

        $allowed_types = array( 'movie_playlist', 'video_playlist', 'tv_show_playlist' );

        if ( ! in_array( $post_type, $allowed_types, true ) ) {
            masvideos_add_notice( '<strong>' . __( 'Error:', 'masvideos' ) . '</strong> ' . __( 'Posttype is not valid.', 'masvideos' ), 'error' );
            do_action( 'masvideos_edit_playlist_failed' );
            return;
        }

        // Restrict visibility to the statuses allowed for this playlist type.
        $visibility_options_func = 'masvideos_get_' . $post_type . '_visibility_options';
        $allowed_visibility      = function_exists( $visibility_options_func ) ? array_keys( call_user_func( $visibility_options_func ) ) : array();

        if ( ! in_array( $visibility, $allowed_visibility, true ) ) {
            // Fall back to the most private option available.
            if ( in_array( 'private', $allowed_visibility, true ) ) {
                $visibility = 'private';
            } elseif ( ! empty( $allowed_visibility ) ) {
                $visibility = $allowed_visibility[0];
            } else {
                $visibility = 'private';
            }
        }

        // Authorize edits to an existing playlist. New playlists ($id === 0) are created for and
        // owned by the current user by the data store.
        if ( $id > 0 ) {
            $post = masvideos_get_authorized_account_post( $id, $post_type, 'edit_post', true );

            if ( ! $post ) {
                masvideos_add_notice( '<strong>' . __( 'Error:', 'masvideos' ) . '</strong> ' . __( 'You are not allowed to edit this playlist.', 'masvideos' ), 'error' );
                do_action( 'masvideos_edit_playlist_failed' );
                return;
            }
        }

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
            }

            if ( is_wp_error( $playlist ) ) {
                throw new Exception( $playlist->get_error_message() );
            }

            if ( ! empty( $_POST['redirect'] ) && is_scalar( $_POST['redirect'] ) ) {
                $redirect = wp_sanitize_redirect( wp_unslash( $_POST['redirect'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
            } elseif ( wp_get_raw_referer() ) {
                $redirect = wp_get_raw_referer();
            } else {
                $redirect = masvideos_get_page_permalink( 'myaccount' );
            }

            wp_safe_redirect( wp_validate_redirect( apply_filters( 'masvideos_edit_playlist_redirect', $redirect ), masvideos_get_page_permalink( 'myaccount' ) ) );
            exit;

        } catch ( Exception $e ) {
            masvideos_add_notice( '<strong>' . __( 'Error:', 'masvideos' ) . '</strong> ' . $e->getMessage(), 'error' );
            do_action( 'masvideos_edit_playlist_failed' );
        }
    }

    /**
     * Process the delete playlist form.
     *
     * Deletes or trashes a single playlist post of a supported playlist type. Requires an
     * authenticated user, a nonce bound to the specific object, a validated playlist post
     * type, and either ownership of the playlist or the appropriate WordPress deletion
     * capability for that object.
     */
    public static function delete_playlist() {
        // Only act on explicitly supported actions; leave the request untouched otherwise.
        $action = isset( $_GET['action'] ) && is_scalar( $_GET['action'] ) ? sanitize_key( wp_unslash( $_GET['action'] ) ) : '';

        if ( ! in_array( $action, array( 'delete', 'trash' ), true ) ) {
            return;
        }

        $id = isset( $_GET['post'] ) && is_scalar( $_GET['post'] ) ? absint( wp_unslash( $_GET['post'] ) ) : 0;

        if ( $id <= 0 ) {
            return;
        }

        $nonce_value = isset( $_GET['masvideos-delete-playlist-nonce'] ) && is_scalar( $_GET['masvideos-delete-playlist-nonce'] )
            ? sanitize_text_field( wp_unslash( $_GET['masvideos-delete-playlist-nonce'] ) )
            : ( isset( $_GET['_wpnonce'] ) && is_scalar( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '' );

        // Nonce is bound to the specific object and (implicitly) the deletion operation.
        if ( ! wp_verify_nonce( $nonce_value, 'masvideos-delete-playlist_' . $id ) ) {
            return;
        }

        // Authorize. Ownership authorizes only a RECOVERABLE trash; permanent deletion
        // requires the WordPress delete capability for the object (which encodes moderation
        // boundaries such as delete_published_* / delete_private_*). When the site has
        // disabled Trash (EMPTY_TRASH_DAYS = 0), wp_trash_post() permanently deletes, so we
        // must require the capability even for the 'trash' action — fail closed.
        $allowed_types = array( 'movie_playlist', 'video_playlist', 'tv_show_playlist' );
        $allow_owner   = ( 'trash' === $action ) && masvideos_trash_is_recoverable();
        $post          = masvideos_get_authorized_account_post( $id, $allowed_types, 'delete_post', $allow_owner );

        if ( ! $post ) {
            masvideos_add_notice( __( 'You are not allowed to delete this playlist.', 'masvideos' ), 'error' );
            do_action( 'masvideos_delete_playlist_failed' );
            return;
        }

        try {
            $validation_error = new WP_Error();
            $validation_error = apply_filters( 'masvideos_process_delete_playlist_errors', $validation_error, $id, $action );

            if ( $validation_error->get_error_code() ) {
                throw new Exception( $validation_error->get_error_message() );
            }

            if ( 'delete' === $action ) {
                $result = wp_delete_post( $id, true );
            } else {
                $result = wp_trash_post( $id );
            }

            if ( ! $result ) {
                throw new Exception( __( 'This playlist could not be deleted.', 'masvideos' ) );
            }

            if ( ! empty( $_GET['redirect'] ) && is_scalar( $_GET['redirect'] ) ) {
                $redirect = wp_sanitize_redirect( wp_unslash( $_GET['redirect'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
            } elseif ( wp_get_raw_referer() ) {
                $redirect = wp_get_raw_referer();
            } else {
                $redirect = masvideos_get_page_permalink( 'myaccount' );
            }

            wp_safe_redirect( wp_validate_redirect( apply_filters( 'masvideos_delete_playlist_redirect', $redirect ), masvideos_get_page_permalink( 'myaccount' ) ) );
            exit;

        } catch ( Exception $e ) {
            masvideos_add_notice( '<strong>' . __( 'Error:', 'masvideos' ) . '</strong> ' . $e->getMessage(), 'error' );
            do_action( 'masvideos_delete_playlist_failed' );
        }
    }
}

MasVideos_Form_Handler::init();
