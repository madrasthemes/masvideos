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
    public static function register_login( $atts ) {
        if ( ! is_user_logged_in() ) {
            masvideos_get_template( 'myaccount/form-register-login.php' );
        }
    }

    /**
     * Output the shortcode.
     *
     * @param array $atts Shortcode attributes.
     */
    public static function register( $atts ) {
        if ( ! is_user_logged_in() ) {
            masvideos_get_template( 'myaccount/form-register.php' );
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
                $playlist = function_exists( $playlist_func_name ) ? $playlist_func_name( $id ) : false;
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
                    $playlist_id = get_user_meta( $current_user_id, 'masvideos_history_movie_playlist_id', true );
                    if( ! empty( $playlist_id ) ) {
                        masvideos_template_single_movie_playlist_movies( $playlist_id, $args );
                    }
                    break;

                case 'video':
                    $playlist_id = get_user_meta( $current_user_id, 'masvideos_history_video_playlist_id', true );
                    if( ! empty( $playlist_id ) ) {
                        masvideos_template_single_video_playlist_videos( $playlist_id, $args );
                    }
                    break;

                case 'tv_show':
                    $playlist_id = get_user_meta( $current_user_id, 'masvideos_history_tv_show_playlist_id', true );
                    if( ! empty( $playlist_id ) ) {
                        masvideos_template_single_tv_show_playlist_tv_shows( $playlist_id, $args );
                    }
                    break;
            }
        }
    }
}
