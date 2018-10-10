<?php
/**
 * Masvideos Template Hooks
 *
 * Action/filter hooks used for Masvideos functions/templates.
 *
 * @package Masvideos/Templates
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Videos Loop.
 */
add_action( 'masvideos_before_videos_loop_item', 'masvideos_template_loop_video_link_open', 10 );
add_action( 'masvideos_videos_loop_item_title', 'masvideos_template_loop_video_title', 10 );
add_action( 'masvideos_after_videos_loop_item', 'masvideos_template_loop_video_link_close', 10 );

/**
 * Movies Loop.
 */
add_action( 'masvideos_before_movies_loop_item', 'masvideos_template_loop_movie_link_open', 10 );
add_action( 'masvideos_movies_loop_item_title', 'masvideos_template_loop_movie_title', 10 );
add_action( 'masvideos_after_movies_loop_item', 'masvideos_template_loop_movie_link_close', 10 );


/**
 * Video Single.
 */
add_action( 'masvideos_single_video_summary', 'masvideos_template_single_video_title', 5 );

/**
 * Movie Single.
 */
add_action( 'masvideos_single_movie_summary', 'masvideos_template_single_movie_title', 5 );