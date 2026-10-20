<?php
/**
 * My Account Shortcodes
 *
 * Shows the 'my account' section.
 *
 * @package MasVideos/Shortcodes
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Shortcode my account class.
 */
class MasVideos_Shortcode_My_Account {

    /**
     * Output the shortcode.
     *
     * @param array $atts Shortcode attributes.
     */
    public static function my_account( $atts ) {
        if ( ! is_user_logged_in() ) {
            masvideos_get_template( 'myaccount/form-register-login.php' );
        } else {
            masvideos_get_template( 'myaccount/my-account.php' );
        }
    }

    /**
     * Edit account details page.
     */
    public static function edit_account() {
        masvideos_get_template( 'myaccount/form-edit-account.php', array( 'user' => get_user_by( 'id', get_current_user_id() ) ) );
    }

    /**
     * Output the shortcode.
     *
     * @param array $atts Shortcode attributes.
     */
    public static function edit_video() {
        if ( ! is_user_logged_in() ) {
            masvideos_get_template( 'myaccount/form-register-login.php' );
            return;
        }

        $editing_id = 0;

        if ( isset( $_GET['post'], $_GET['action'] ) && 'edit' === $_GET['action'] ) { // WPCS: input var ok, CSRF ok.
            $editing_id = is_scalar( $_GET['post'] ) ? absint( wp_unslash( $_GET['post'] ) ) : 0; // WPCS: input var ok, CSRF ok.
        }

        // Only show the form to users who may edit this specific video, or who may create one.
        // This mirrors the authorization enforced in MasVideos_Form_Handler::edit_video().
        if ( $editing_id > 0 ) {
            $can_manage = ( 'video' === get_post_type( $editing_id ) && current_user_can( 'edit_post', $editing_id ) );
        } else {
            $can_manage = current_user_can( apply_filters( 'masvideos_frontend_create_video_capability', 'edit_videos' ) );
        }

        if ( ! $can_manage ) {
            echo '<div class="masvideos-message masvideos-message--info masvideos-Message masvideos-Message--info masvideos-info">' . esc_html__( 'You do not have permission to upload or edit videos.', 'masvideos' ) . '</div>';
            return;
        }

        $title = apply_filters( 'masvideos_my_account_upload_video_title', esc_html__( 'Upload video', 'masvideos' ) );
        $button_text = apply_filters( 'masvideos_my_account_upload_video_button_text', esc_html__( 'Submit video', 'masvideos' ) );
        $button_draft_text = apply_filters( 'masvideos_my_account_upload_video_button_draft_text', esc_html__( 'Save as Draft', 'masvideos' ) );
        $fields = masvideos_get_edit_video_fields();
        $video = false;

        if ( $editing_id > 0 && ( $video = masvideos_get_video( $editing_id ) ) ) {
            foreach ( $fields as $key => $field ) {
                // Set prop in video object.
                if ( is_callable( array( $video, "get_$key" ) ) ) {
                    $fields[ $key ]['value'] = $video->{"get_$key"}( 'edit' );
                } else {
                    $fields[ $key ]['value'] = $video->get_meta( $key, true, 'edit' );
                }
            }

            $title = apply_filters( 'masvideos_my_account_edit_video_title', esc_html__( 'Edit video', 'masvideos' ) );
            $button_text = apply_filters( 'masvideos_my_account_edit_video_button_text', esc_html__( 'Save video', 'masvideos' ) );
        }

        masvideos_get_template( 'myaccount/edit-video.php', array(
            'title'         => $title,
            'button_text'   => $button_text,
            'fields'        => $fields,
            'video'         => $video,
            'button_draft_text' => $button_draft_text,
        ) );
    }

    /**
     * Output the shortcode.
     *
     * @param array $atts Shortcode attributes.
     */
    public static function register( $atts ) {
        if ( ! is_user_logged_in() ) {
            masvideos_get_template( 'myaccount/form-register.php' );
        } else {
            masvideos_get_template( 'myaccount/my-account.php' );
        }
    }

    /**
     * Output the shortcode.
     *
     * @param array $atts Shortcode attributes.
     */
    public static function login( $atts ) {
        if ( ! is_user_logged_in() ) {
            masvideos_get_template( 'myaccount/form-login.php' );
        } else {
            masvideos_get_template( 'myaccount/my-account.php' );
        }
    }

    /**
     * Output the shortcode.
     *
     * @param array $atts Shortcode attributes.
     */
    public static function manage_playlists( $atts ) {
        if ( is_user_logged_in() ) {
            $post_type = ! empty( $atts['post_type'] ) ? $atts['post_type'] : 'movie_playlist';

            if( isset( $_GET['post'] ) && isset( $_GET['action'] ) && $_GET['action'] == 'edit' ) {
                $id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : absint( get_query_var( 'post', 0 ) ); // WPCS: sanitization ok, input var ok, CSRF ok.
                $playlist_func_name = 'masvideos_get_' . $post_type;
                $playlist = current_user_can( 'edit_post', $id ) && function_exists( $playlist_func_name ) ? $playlist_func_name( $id ) : false;
            }

            masvideos_get_template( 'myaccount/edit-playlist.php', wp_parse_args( array(
                'post_type'     => $post_type,
                'playlist'      => isset( $playlist ) && $playlist ? $playlist : false,
            ), $atts ) );

            $playlists_func_name = 'masvideos_get_current_user_' . $post_type . 's';

            masvideos_get_template( 'myaccount/manage-playlists.php', wp_parse_args( array(
                'post_type'     => $post_type,
                'playlists'     => function_exists( $playlists_func_name ) ? $playlists_func_name() : false
            ), $atts ) );
        }
    }

    /**
     * Output the shortcode.
     *
     * @param array $atts Shortcode attributes.
     */
    public static function history( $atts ) {
        if ( is_user_logged_in() ) {
            $post_type = ! empty( $atts['post_type'] ) ? $atts['post_type'] : 'movie';

            $current_user_id = get_current_user_id();

            $args = apply_filters( 'masvideos_history_items_args', array(
                'limit'          => isset( $atts['limit'] ) ? intval( $atts['limit'] ) : -1,
                'columns'        => isset( $atts['columns'] ) ? absint( $atts['columns'] ) : 5,
            ), $post_type );

            switch ( $post_type ) {
                case 'movie':
                    $playlist_id = get_user_option( 'masvideos_history_movie_playlist_id', $current_user_id );
                    if( ! empty( $playlist_id ) ) {
                        masvideos_template_single_movie_playlist_movies( $playlist_id, $args );
                    }
                    break;

                case 'video':
                    $playlist_id = get_user_option( 'masvideos_history_video_playlist_id', $current_user_id );
                    if( ! empty( $playlist_id ) ) {
                        masvideos_template_single_video_playlist_videos( $playlist_id, $args );
                    }
                    break;

                case 'tv_show':
                    $playlist_id = get_user_option( 'masvideos_history_tv_show_playlist_id', $current_user_id );
                    if( ! empty( $playlist_id ) ) {
                        masvideos_template_single_tv_show_playlist_tv_shows( $playlist_id, $args );
                    }
                    break;
            }
        }
    }
}
