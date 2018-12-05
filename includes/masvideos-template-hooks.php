<?php
/**
 * MasVideos Template Hooks
 *
 * Action/filter hooks used for MasVideos functions/templates.
 *
 * @package MasVideos/Templates
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
add_action( 'masvideos_before_movies_loop_item', 'masvideos_template_loop_movie_poster_open', 10 );
add_action( 'masvideos_before_movies_loop_item', 'masvideos_template_loop_movie_link_open', 20 );
add_action( 'masvideos_before_movies_loop_item', 'masvideos_template_loop_movie_poster', 30 );
add_action( 'masvideos_before_movies_loop_item', 'masvideos_template_loop_movie_link_close', 40 );
add_action( 'masvideos_before_movies_loop_item', 'masvideos_template_loop_movie_poster_close', 50 );
add_action( 'masvideos_before_movies_loop_item_title', 'masvideos_template_loop_movie_body_open', 10 );
add_action( 'masvideos_before_movies_loop_item_title', 'masvideos_template_loop_movie_info_open', 20 );
add_action( 'masvideos_movies_loop_item_title', 'masvideos_template_loop_movie_link_open', 10 );
add_action( 'masvideos_movies_loop_item_title', 'masvideos_template_loop_movie_meta', 20 );
add_action( 'masvideos_movies_loop_item_title', 'masvideos_template_loop_movie_title', 30 );
add_action( 'masvideos_movies_loop_item_title', 'masvideos_template_loop_movie_link_close', 40 );
add_action( 'masvideos_after_movies_loop_item_title', 'masvideos_template_loop_movie_short_desc', 10 );
add_action( 'masvideos_after_movies_loop_item_title', 'masvideos_template_loop_movie_actions', 20 );
add_action( 'masvideos_after_movies_loop_item_title', 'masvideos_template_loop_movie_info_close', 30 );
add_action( 'masvideos_after_movies_loop_item', 'masvideos_template_loop_movie_review_info_open', 10 );
add_action( 'masvideos_after_movies_loop_item', 'masvideos_template_loop_movie_avg_rating', 20 );
add_action( 'masvideos_after_movies_loop_item', 'masvideos_template_loop_movie_viewers_count', 30 );
add_action( 'masvideos_after_movies_loop_item', 'masvideos_template_loop_movie_review_info_close', 40 );
add_action( 'masvideos_after_movies_loop_item', 'masvideos_template_loop_movie_body_close', 50 );

/**
 * Video Single.
 */
add_action( 'masvideos_before_single_video_summary', 'masvideos_template_single_video_video', 10 );
add_action( 'masvideos_single_video_summary', 'masvideos_template_single_video_title', 5 );
add_action( 'masvideos_single_video_summary', 'masvideos_template_single_video_meta', 10 );

/**
 * Movie Single.
 */
add_action( 'masvideos_before_single_movie_summary', 'masvideos_template_single_movie_movie', 10 );
add_action( 'masvideos_single_movie_summary', 'masvideos_template_single_movie_title', 5 );