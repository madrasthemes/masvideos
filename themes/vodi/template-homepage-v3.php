<?php
/**
 * The template for displaying Home v3.
 * 
 * Template name: Home v3
 *
 * @package vodi
 */
get_header( 'v2' );

    do_action( 'vodi_before_main_content' ); ?>
    
    <div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">

            <?php
                
                do_action( 'vodi_home_v3' ); ?>

        </main><!-- #main -->
    </div><!-- #primary --><?php 

get_footer( 'v1' );