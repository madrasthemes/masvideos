<?php
/**
 * The template for displaying Landing Page v1.
 * 
 * Template name: Landing page v1
 *
 * @package vodi
 */

get_header( 'landing-v1' );

do_action( 'vodi_before_main_content' ); ?>

<div class="landing-hero">
    <div class="landing-hero__inner">
        <div class="landing-hero__caption">
            <h2 class="landing-hero__title">We'll Show You</h2>
            <p class="landing-hero__subtitle">Best movies, shows & lives for $9/monthly</p>
            <button type="button" class="landing-hero__btn-action">Start Your Free 14-Days Trial</button>
        </div><!-- /.hero__caption -->
    </div>
</div><!-- /.hero -->

<div id="primary" class="content-area">
    <main class="site-main">
        <?php
            do_action( 'vodi_landing_v1' ); ?>        
    </main>
</div> <?php

do_action( 'vodi_after_main_content' );

    get_footer( 'landing-v1' );