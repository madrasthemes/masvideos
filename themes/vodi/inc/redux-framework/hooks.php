<?php
/**
 * Redux Framworks hooks
 *
 * @package Vodi/ReduxFramework
 */
add_action( 'init',                                         'vodi_remove_demo_mode_link' );
add_action( 'redux/loaded',                                 'vodi_redux_disable_dev_mode_and_remove_admin_notices' );
add_action( 'redux/page/vodi_options/enqueue',              'redux_queue_font_awesome' );


// General Filters
add_filter( 'vodi_bg_style_class',                      'redux_apply_bg_style',                      10 );


// Header Filters
add_filter( 'vodi_site_logo_svg',                          'redux_toggle_logo_svg',                                    10 );
add_filter( 'vodi_get_header_version',                     'redux_apply_header_version',                               10 );

// Footer Filters
add_filter( 'vodi_get_footer_version',                     'redux_apply_footer_version',                               10 );

// Blog Filters
add_filter( 'vodi_get_blog_layout',                        'redux_apply_vodi_get_blog_layout',                        10);
add_filter( 'vodi_get_blog_view',                          'redux_apply_vodi_get_blog_view',                          10);
add_filter( 'vodi_get_blog_header_version',                'redux_apply_vodi_get_blog_header_version',                10 );
add_filter( 'vodi_get_blog_site_content_page_title',       'redux_apply_vodi_get_blog_site_content_page_title',       10 );
add_filter( 'vodi_get_blog_site_content_page_subtitle',    'redux_apply_vodi_get_blog_site_content_page_subtitle',    10 );
add_filter( 'vodi_show_author_info',                       'redux_toggle_author_info',                     			10 );