<?php
/**
 * Hooks used for Header, Footer, Sidebar
 */
add_action( 'vodi_header_v1', 'vodi_header_right_start' );
add_action( 'vodi_header_v1', 'vodi_offcanvas_menu' );
add_action( 'vodi_header_v1', 'vodi_header_logo' );
add_action( 'vodi_header_v1', 'vodi_primary_nav' );
add_action( 'vodi_header_v1', 'vodi_header_right_end' );
add_action( 'vodi_header_v1', 'vodi_header_left_start');
add_action( 'vodi_header_v1', 'vodi_header_search' );
add_action( 'vodi_header_v1', 'vodi_header_upload_link' );
add_action( 'vodi_header_v1', 'vodi_header_notification' );
add_action( 'vodi_header_v1', 'vodi_header_user_account' );
add_action( 'vodi_header_v1', 'vodi_header_left_end');

/**
 * Header v2 hooks
 */

add_action( 'vodi_header_v2', 'vodi_offcanvas_menu' );
add_action( 'vodi_header_v2', 'vodi_header_logo' );
add_action( 'vodi_header_v2', 'vodi_header_search_menu_start' );
add_action( 'vodi_header_v2', 'vodi_header_search' );
add_action( 'vodi_header_v2', 'vodi_primary_nav' );
add_action( 'vodi_header_v2', 'vodi_header_search_menu_end' );
add_action( 'vodi_header_v2', 'vodi_header_icon_start' );
add_action( 'vodi_header_v2', 'vodi_header_upload_link' );
add_action( 'vodi_header_v2', 'vodi_header_notification' );
add_action( 'vodi_header_v2', 'vodi_header_user_account' );
add_action( 'vodi_header_v2', 'vodi_header_icon_end' );

/**
 * Header v3 hooks
 */
add_action( 'vodi_header_v3', 	'vodi_masthead_v3', 10 );
add_action( 'vodi_header_v3', 	'vodi_quick_links', 20 );
add_action( 'vodi_masthead_v3', 'vodi_header_right_start' );
add_action( 'vodi_masthead_v3', 'vodi_header_logo' );
add_action( 'vodi_masthead_v3', 'vodi_primary_nav' );
add_action( 'vodi_masthead_v3', 'vodi_header_right_end' );
add_action( 'vodi_masthead_v3', 'vodi_header_left_start');
add_action( 'vodi_masthead_v3', 'vodi_secondary_nav' );
add_action( 'vodi_masthead_v3', 'vodi_masthead_v3_search' );
add_action( 'vodi_masthead_v3', 'vodi_header_notification' );
add_action( 'vodi_masthead_v3', 'vodi_header_user_account' );
add_action( 'vodi_masthead_v3', 'vodi_header_left_end');

//add_action( 'vodi_header_v4', 	'vodi_header_slider' );
add_action( 'vodi_header_v4', 	'vodi_masthead_v4' );
add_action( 'vodi_masthead_v4', 'vodi_offcanvas_menu' );
add_action( 'vodi_masthead_v4', 'vodi_header_v4_logo' );
add_action( 'vodi_masthead_v4', 'vodi_header_user_account' );
add_action( 'vodi_header_v4',   'vodi_menu_with_search_bar' );

add_action( 'vodi_menu_with_search_bar',   'vodi_navbar_primary' );
add_action( 'vodi_menu_with_search_bar',   'vodi_header_search' );

add_action( 'vodi_after_main_content', 'vodi_sidebar', 10 );
add_action( 'vodi_sidebar', 'vodi_get_sidebar', 10 );

add_action( 'vodi_header_comingsoon', 'vodi_site_header_comingsoon' );

add_action( 'vodi_header_landing_v1', 'vodi_site_header_landing_v1' );

add_action( 'vodi_header_landing_v2', 'vodi_site_header_landing_v2' );

add_action( 'vodi_before_header_v4', 'vodi_live_videos' );