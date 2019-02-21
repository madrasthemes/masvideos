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
}
