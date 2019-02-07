<?php
/**
 * The template for displaying Coming Soon.
 * 
 * Template name: Coming Soon
 *
 * @package vodi
 */

get_header( 'coming-soon' );

    do_action( 'vodi_before_main_content' ); ?>

<div id="primary" class="content-area">
    <main class="site-main">
        
        <?php
            do_action( 'vodi_coming_soon' ); ?>          
    </main>
</div> <?php

do_action( 'vodi_after_main_content' );

 get_footer( 'coming-soon' );