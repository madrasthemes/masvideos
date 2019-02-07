<?php
/**
 * The template for displaying Landing Page v2.
 * 
 * Template name: Landing page v2
 *
 * @package vodi
 */


get_header( 'landing-v2' );

do_action( 'vodi_before_main_content' ); ?>

<div class="site-content">
    <main class="site-main">

        <?php
            do_action( 'vodi_landing_v2' ); ?>  
    </main>       
</div><?php

do_action( 'vodi_after_main_content' );

    get_footer( 'landing-v2' );