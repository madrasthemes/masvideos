<?php
/**
 * The template for displaying all single posts.
 *
 * @package vodi
 */

get_header();

    do_action( 'vodi_before_main_content' ); ?>

    <div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">
        
        <?php while ( have_posts() ) : the_post();

            do_action( 'vodi_single_post_before' );

            get_template_part( 'templates/contents/content', 'single' );

            do_action( 'vodi_single_post_after' );

        endwhile; // End of the loop. ?>
        
        </main><!-- #main -->
    </div><!-- #primary --><?php

    do_action( 'vodi_after_main_content' );

get_footer();