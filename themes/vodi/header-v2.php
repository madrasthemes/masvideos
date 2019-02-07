<?php
/**
 * The header v2 for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package vodi
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php do_action( 'vodi_before_site' ); ?>

<div id="page" class="hfeed site">
    
    <?php do_action( 'vodi_before_header' ); ?>

    <header id="masthead" class="site-header header-v2 transparent" role="banner" style="<?php vodi_header_styles(); ?>">
        <div class="container-fluid">
            <div class="site-header__inner">
                <?php
                /**
                 * @hooked vodi_offcanvas_menu
                 * @hooked vodi_header_logo
                 * @hooked vodi_header_search_menu_start 
                 * @hooked vodi_header_search 
                 * @hooked vodi_primary_nav 
                 * @hooked vodi_header_search_menu_end 
                 * @hooked vodi_header_icon_start
                 * @hooked vodi_header_upload_link
                 * @hooked vodi_header_notification
                 * @hooked vodi_header_user_account
                 * @hooked vodi_header_icon_end
                 *
                 */
                do_action( 'vodi_header_v2' ); ?>
            </div>
        </div>
    </header><!-- #masthead -->

    <?php
    /**
     * Functions hooked in to vodi_before_content
     *
     * @hooked vodi_header_widget_region - 10
     * @hooked woocommerce_breadcrumb - 10
     */
    do_action( 'vodi_before_content' );
    ?>

    <div id="content" class="site-content" tabindex="-1">
        
        <?php do_action( 'vodi_content_top' ); ?>
            
            <div class="site-content__inner">