<?php
/**
 * Vodi engine room
 *
 * @package vodi
 */

/**
 * Assign the Vodi version to a var
 */
$theme        = wp_get_theme( 'vodi' );
$vodi_version = $theme['Version'];

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
    $content_width = 480; /* pixels */
}

$vodi = (object) array(
    'version'    => $vodi_version,
    
    /**
     * Initialize all the things.
     */
    'main'       => require 'inc/class-vodi.php'
);

// Jetpack
function vodi_jetpack_remove_share() {

    remove_filter( 'the_content', 'sharing_display', 19 );
    remove_filter( 'the_excerpt', 'sharing_display', 19 );
    
    if ( class_exists( 'Jetpack_Likes' ) ) {
        remove_filter( 'the_content', array( Jetpack_Likes::init(), 'post_likes' ), 30, 1 );
    }
}
 
add_action( 'loop_start', 'vodi_jetpack_remove_share' );

require_once get_template_directory() . '/inc/vodi-functions.php';
require_once get_template_directory() . '/inc/vodi-template-functions.php';
require_once get_template_directory() . '/inc/vodi-template-hooks.php';


if ( is_admin() ) {
    require get_template_directory() . '/inc/admin/class-vodi-admin.php';
}

if ( vodi_is_jetpack_activated() ) {
    require_once get_template_directory() . '/inc/jetpack/vodi-jetpack-functions.php';
}

if ( vodi_is_redux_activated() ) {
    require_once get_template_directory() . '/inc/redux-framework/vodi-options.php';
    require_once get_template_directory() . '/inc/redux-framework/functions.php';
    require_once get_template_directory() . '/inc/redux-framework/hooks.php';
}

/**
 * Note: Do not add any custom code here. Please use a custom plugin so that your customizations aren't lost during updates.
 * https://github.com/woocommerce/theme-customisations
 */
update_option( 'masvideos_movie_review_rating_required', 'yes' );
update_option( 'masvideos_video_review_rating_required', 'yes' );
update_option( 'masvideos_tv_show_review_rating_required', 'yes' );
