<?php
/**
 * The template for displaying Home v4.
 * 
 * Template name: Home v4
 *
 * @package vodi
 */
get_header( 'v3' );

    do_action( 'vodi_before_main_content' ); ?>
    
    <div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">

            <?php
                
                do_action( 'vodi_home_v4' ); ?>

        </main><!-- #main -->
    </div><!-- #primary --><?php 

get_footer( 'v2' );