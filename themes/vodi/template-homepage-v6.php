<?php
/**
 * The template for displaying Home v6.
 * 
 * Template name: Home v6
 *
 * @package vodi
 */
get_header( 'v1' );

    do_action( 'vodi_before_main_content' ); ?>
    
    <div id="primary" class="content-area light">
        <main id="main" class="site-main" role="main">
        	<div class="site-main-inner">

            <?php
                
                do_action( 'vodi_home_v6' ); ?>
            </div><!-- .site-main-inner -->

        </main><!-- #main -->
    </div><!-- #primary --><?php 

	do_action( 'vodi_sidebar', 'home' );

get_footer( 'v1' );