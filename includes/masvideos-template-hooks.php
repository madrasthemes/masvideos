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

add_action( 'masvideos_before_main_content', 'masvideos_template_loop_content_area_open', 10 );
add_action( 'masvideos_before_movies_loop', 'masvideos_movies_control_bar', 10 );
add_action( 'masvideos_after_movies_loop', 'masvideos_movies_page_control_bar', 10 );
add_action( 'masvideos_after_main_content', 'masvideos_template_loop_content_area_close', 999 );

/**
 * Videos Loop.
 */
add_action( 'masvideos_videos_loop', 'masvideos_videos_loop_content', 20 );
add_action( 'masvideos_before_videos_loop_item', 'masvideos_template_loop_video_feature_badge', 10 );
add_action( 'masvideos_before_videos_loop_item', 'masvideos_template_loop_video_container_open', 20 );
add_action( 'masvideos_before_videos_loop_item', 'masvideos_template_loop_video_link_open', 30 );
add_action( 'masvideos_before_videos_loop_item', 'masvideos_template_loop_video_poster_open', 40 );
add_action( 'masvideos_before_videos_loop_item', 'masvideos_template_loop_video_poster', 50 );
add_action( 'masvideos_before_videos_loop_item', 'masvideos_template_loop_video_poster_close', 60 );
add_action( 'masvideos_before_videos_loop_item', 'masvideos_template_loop_video_link_close', 70 );
add_action( 'masvideos_before_videos_loop_item', 'masvideos_template_loop_video_container_close', 80 );
add_action( 'masvideos_before_videos_loop_item_title', 'masvideos_template_loop_video_body_open', 10 );
add_action( 'masvideos_before_videos_loop_item_title', 'masvideos_template_loop_video_info_open', 20 );
add_action( 'masvideos_videos_loop_item_title', 'masvideos_template_loop_video_link_open', 10 );
add_action( 'masvideos_videos_loop_item_title', 'masvideos_template_loop_video_title', 30 );
add_action( 'masvideos_videos_loop_item_title', 'masvideos_template_loop_video_meta', 20 );
add_action( 'masvideos_videos_loop_item_title', 'masvideos_template_loop_video_link_close', 40 );
add_action( 'masvideos_after_videos_loop_item_title', 'masvideos_template_loop_video_short_desc', 10 );
add_action( 'masvideos_after_videos_loop_item_title', 'masvideos_template_loop_video_actions', 20 );
add_action( 'masvideos_after_videos_loop_item_title', 'masvideos_template_loop_video_info_close', 30 );
add_action( 'masvideos_after_videos_loop_item', 'masvideos_template_loop_video_review_info_open', 10 );
add_action( 'masvideos_after_videos_loop_item', 'masvideos_template_loop_video_avg_rating', 20 );
add_action( 'masvideos_after_videos_loop_item', 'masvideos_template_loop_video_viewers_count', 30 );
add_action( 'masvideos_after_videos_loop_item', 'masvideos_template_loop_video_review_info_close', 40 );
add_action( 'masvideos_after_videos_loop_item', 'masvideos_template_loop_video_body_close', 50 );

/**
 * Movies Loop.
 */
add_action( 'masvideos_movies_loop', 'masvideos_movies_loop_content', 20 );
add_action( 'masvideos_before_movies_loop_item', 'masvideos_template_loop_movie_poster_open', 10 );
add_action( 'masvideos_before_movies_loop_item', 'masvideos_template_loop_movie_link_open', 20 );
add_action( 'masvideos_before_movies_loop_item', 'masvideos_template_loop_movie_poster', 30 );
add_action( 'masvideos_before_movies_loop_item', 'masvideos_template_loop_movie_link_close', 40 );
add_action( 'masvideos_before_movies_loop_item', 'masvideos_template_loop_movie_poster_close', 50 );
add_action( 'masvideos_before_movies_loop_item_title', 'masvideos_template_loop_movie_body_open', 10 );
add_action( 'masvideos_before_movies_loop_item_title', 'masvideos_template_loop_movie_info_open', 20 );
add_action( 'masvideos_movies_loop_item_title', 'masvideos_template_loop_movie_info_head_open', 10 );
add_action( 'masvideos_movies_loop_item_title', 'masvideos_template_loop_movie_meta', 20 );
add_action( 'masvideos_movies_loop_item_title', 'masvideos_template_loop_movie_link_open', 30 );
add_action( 'masvideos_movies_loop_item_title', 'masvideos_template_loop_movie_title', 40 );
add_action( 'masvideos_movies_loop_item_title', 'masvideos_template_loop_movie_link_close', 50 );
add_action( 'masvideos_movies_loop_item_title', 'masvideos_template_loop_movie_info_head_close', 60 );
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
add_action( 'masvideos_after_single_video_summary', 'comments_template', 10 );

/**
 * Movie Single.
 */
add_action( 'masvideos_before_single_movie_summary', 'masvideos_template_single_movie_movie', 10 );
add_action( 'masvideos_single_movie_summary', 'masvideos_template_single_movie_title', 5 );
add_action( 'masvideos_after_single_movie_summary', 'comments_template', 10 );

/**
 * Movie Reviews
 *
 * @see masvideos_movie_review_display_gravatar()
 * @see masvideos_movie_review_display_rating()
 * @see masvideos_movie_review_display_meta()
 * @see masvideos_movie_review_display_comment_text()
 */
add_action( 'masvideos_movie_review_before', 'masvideos_movie_review_display_gravatar', 10 );
add_action( 'masvideos_movie_review_before_comment_meta', 'masvideos_movie_review_display_rating', 10 );
add_action( 'masvideos_movie_review_meta', 'masvideos_movie_review_display_meta', 10 );
add_action( 'masvideos_movie_review_comment_text', 'masvideos_movie_review_display_comment_text', 10 );

/**
 * Video Reviews
 *
 * @see masvideos_video_review_display_gravatar()
 * @see masvideos_video_review_display_rating()
 * @see masvideos_video_review_display_meta()
 * @see masvideos_video_review_display_comment_text()
 */
add_action( 'masvideos_video_review_before', 'masvideos_video_review_display_gravatar', 10 );
add_action( 'masvideos_video_review_before_comment_meta', 'masvideos_video_review_display_rating', 10 );
add_action( 'masvideos_video_review_meta', 'masvideos_video_review_display_meta', 10 );
add_action( 'masvideos_video_review_comment_text', 'masvideos_video_review_display_comment_text', 10 );
