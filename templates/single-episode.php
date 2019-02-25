<?php
/**
 * The Template for displaying all single episodes
 *
 * This template can be overridden by copying it to yourtheme/masvideos/single-episode.php.
 *
 * HOWEVER, on occasion MasVideos will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @package MasVideos/Templates
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'episode' );

/**
 * masvideos_before_main_content hook.
 *
 * @hooked masvideos_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked masvideos_breadcrumb - 20
 */
do_action( 'masvideos_before_main_content' );

while ( have_posts() ) : the_post();

    masvideos_get_template_part( 'content', 'single-episode' );

endwhile; // end of the loop.

/**
 * masvideos_after_main_content hook.
 *
 * @hooked masvideos_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'masvideos_after_main_content' );

get_footer( 'episode' );
