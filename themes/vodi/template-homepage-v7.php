<?php
/**
 * The template for displaying Home v7.
 * 
 * Template name: Home v7
 *
 * @package vodi
 */
get_header( 'v1' );

    do_action( 'vodi_before_main_content' ); ?>
    
    <div id="primary" class="content-area dark">
        <main id="main" class="site-main" role="main">
        	<div class="site-main-inner">

            <?php
                
                do_action( 'vodi_home_v7' ); ?>
            </div><!-- .site-main-inner -->

        </main><!-- #main -->
    </div><!-- #primary --><?php 

	do_action( 'vodi_sidebar', 'home' );

get_footer( 'v1' );