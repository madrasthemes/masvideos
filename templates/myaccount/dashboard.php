<?php
/**
 * My Account Dashboard
 *
 * Shows the first intro screen on the account dashboard.
 *
 * This template can be overridden by copying it to yourtheme/masvideos/myaccount/dashboard.php.
 *
 * HOWEVER, on occasion MasVideos will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @package     MasVideos/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>

<p><?php
    /* translators: 1: user display name 2: logout url */
    printf(
        __( 'Hello %1$s (not %1$s? <a href="%2$s">Log out</a>)', 'masvideos' ),
        '<strong>' . esc_html( $current_user->display_name ) . '</strong>',
        esc_url( masvideos_logout_url() )
    );
?></p>

<p><?php
    printf(
        __( 'From your account dashboard you can view your <a href="%1$s">recently added videos</a>, manage your <a href="%2$s">movie playlists</a>, manage your <a href="%3$s">video playlists</a>, and manage your <a href="%4$s">tv show playlists</a>.', 'masvideos' ),
        esc_url( masvideos_get_endpoint_url( 'videos' ) ),
        esc_url( masvideos_get_endpoint_url( 'movie-playlists' ) ),
        esc_url( masvideos_get_endpoint_url( 'video-playlists' ) ),
        esc_url( masvideos_get_endpoint_url( 'tv-show-playlists' ) )
    );
?></p>

<?php
    /**
     * My Account dashboard.
     *
     */
    do_action( 'masvideos_account_dashboard' );