<?php
/**
 * The template for displaying movie content within loops
 *
 * This template can be overridden by copying it to yourtheme/masvideos/content-movie.php.
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

global $movie;

// Ensure visibility.
if ( empty( $movie ) || ! $movie->is_visible() ) {
    return;
}


?>
<div <?php masvideos_movie_class(); ?>>
    <?php
    /**
     * Hook: masvideos_before_movies_loop_item.
     *
     * @hooked movies_slider_template_loop_open - 10
     */
    do_action( 'masvideos_before_movies_slider_loop_item' );

    /**
     * Hook: masvideos_before_movies_slider_loop_item_title.
     *
     * @hooked movies_slider_action_button - 10
     */
    do_action( 'masvideos_before_movies_slider_loop_item_title' );

    /**
     * Hook: masvideos_movies_slider_loop_item_title.
     *
     * @hooked masvideos_template_loop_movie_link_open  - 10
     * @hooked masvideos_template_loop_movie_title      - 20
     * @hooked masvideos_template_loop_movie_link_close - 30
     * @hooked movies_slider_loop_movie_meta            - 40
     */
    do_action( 'masvideos_movies_slider_loop_item_title' );

    /**
     * Hook: masvideos_after_movies_slider_loop_item_title.
     *
     * @hooked masvideos_template_loop_movie_short_desc_wrap_open   - 10
     * @hooked masvideos_template_loop_movie_short_desc             - 20
     * @hooked masvideos_template_loop_movie_short_desc_wrap_close  - 30
     * @hooked masvideos_template_loop_movie_actions                - 40
     */
    do_action( 'masvideos_after_movies_slider_loop_item_title' );

    /**
     * Hook: masvideos_after_movies_slider_loop_item.
     *
     * @hooked movies_slider_template_loop_close - 10
     */
    do_action( 'masvideos_after_movies_slider_loop_item' );
    ?>
</div>
