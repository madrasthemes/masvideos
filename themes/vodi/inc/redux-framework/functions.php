<?php
/**
 * Redux Framework functions
 *
 * @package Vodi/ReduxFramework
 */

/**
 * Setup functions for theme options
 */

function vodi_remove_demo_mode_link() {
    if ( class_exists('ReduxFrameworkPlugin') ) {
        remove_action('admin_notices', array( ReduxFrameworkPlugin::get_instance(), 'admin_notices' ) );    
    }
}

function vodi_redux_disable_dev_mode_and_remove_admin_notices( $redux ) {
    remove_action( 'admin_notices', array( $redux, '_admin_notices' ), 99 );
    $redux->args['dev_mode'] = false;
    $redux->args['forced_dev_mode_off'] = false;
}

/**
 * Enqueues font awesome for Redux Theme Options
 * 
 * @return void
 */
function redux_queue_font_awesome() {
    wp_register_style( 'redux-fontawesome', get_template_directory_uri() . '/assets/css/fontawesome.css', array(), time(), 'all' );
    wp_enqueue_style( 'redux-fontawesome' );
}

require_once get_template_directory() . '/inc/redux-framework/functions/general-functions.php';
// require_once get_template_directory() . '/inc/redux-framework/functions/shop-functions.php';
require_once get_template_directory() . '/inc/redux-framework/functions/blog-functions.php';
require_once get_template_directory() . '/inc/redux-framework/functions/header-functions.php';
require_once get_template_directory() . '/inc/redux-framework/functions/footer-functions.php';
// require_once get_template_directory() . '/inc/redux-framework/functions/404-functions.php';
// require_once get_template_directory() . '/inc/redux-framework/functions/style-functions.php';
// require_once get_template_directory() . '/inc/redux-framework/functions/typography-functions.php';