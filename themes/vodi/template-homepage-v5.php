<?php
/**
 * The template for displaying Home v5.
 * 
 * Template name: Home v5
 *
 * @package vodi
 */
get_header( 'v1' );

    do_action( 'vodi_before_main_content' ); ?>
    
    <div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">

            <?php
                
                do_action( 'vodi_home_v5' ); ?>

        </main><!-- #main -->
    </div><!-- #primary --><?php 

get_footer( 'v1' );