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

add_filter( 'body_class', 'masvideos_body_class' );

add_action( 'masvideos_before_main_content', 'masvideos_template_loop_content_area_open', 10 );
add_action( 'masvideos_before_movies_loop', 'masvideos_movies_control_bar', 10 );
add_action( 'masvideos_after_movies_loop', 'masvideos_movies_page_control_bar', 10 );
add_action( 'masvideos_before_tv_shows_loop', 'masvideos_tv_shows_control_bar', 10 );
add_action( 'masvideos_after_tv_shows_loop', 'masvideos_tv_shows_page_control_bar', 10 );
add_action( 'masvideos_after_main_content', 'masvideos_template_loop_content_area_close', 999 );


/**
 * Breadcrumbs.
 *
 * @see masvideos_breadcrumb()
 */
add_action( 'masvideos_before_main_content', 'masvideos_breadcrumb', 20, 0 );

/**
 * Notices.
 */
add_action( 'masvideos_before_user_register_login_form', 'masvideos_output_all_notices', 10 );

/**
 * Episodes Loop.
 */
add_action( 'masvideos_no_episodes_found', 'masvideos_no_episodes_found', 10 );
add_action( 'masvideos_episodes_loop', 'masvideos_episodes_loop_content', 20 );
add_action( 'masvideos_before_episodes_loop_item', 'masvideos_template_loop_episode_poster_open', 30 );
add_action( 'masvideos_before_episodes_loop_item', 'masvideos_template_loop_episode_link_open', 40 );
add_action( 'masvideos_before_episodes_loop_item', 'masvideos_template_loop_episode_poster', 50 );
add_action( 'masvideos_before_episodes_loop_item', 'masvideos_template_loop_episode_link_close', 60 );
add_action( 'masvideos_before_episodes_loop_item', 'masvideos_template_loop_episode_poster_close', 70 );
add_action( 'masvideos_before_episodes_loop_item_title', 'masvideos_template_loop_episode_body_open', 80 );
add_action( 'masvideos_before_episodes_loop_item_title', 'masvideos_template_loop_episode_link_open', 90 );
add_action( 'masvideos_episodes_loop_item_title', 'masvideos_template_loop_episode_title', 10 );
add_action( 'masvideos_after_episodes_loop_item_title', 'masvideos_template_loop_episode_link_close', 10 );
add_action( 'masvideos_after_episodes_loop_item_title', 'masvideos_template_loop_episode_body_close', 20 );

/**
 * Episode Single.
 */
add_action( 'masvideos_before_single_episode_summary', 'masvideos_template_single_episode_head_wrap_open', 10 );
add_action( 'masvideos_before_single_episode_summary', 'masvideos_template_single_episode_prev_navigation', 20 );
add_action( 'masvideos_before_single_episode_summary', 'masvideos_template_single_episode_player_wrap_open', 30 );
add_action( 'masvideos_before_single_episode_summary', 'masvideos_template_single_episode_episode', 40 );
add_action( 'masvideos_before_single_episode_summary', 'masvideos_template_single_episode_player_wrap_close', 50 );
add_action( 'masvideos_before_single_episode_summary', 'masvideos_template_single_episode_next_navigation', 60 );
add_action( 'masvideos_before_single_episode_summary', 'masvideos_template_single_episode_head_wrap_close', 70 );
add_action( 'masvideos_single_episode_summary', 'masvideos_template_single_episode_title', 5 );
add_action( 'masvideos_single_episode_summary', 'masvideos_template_single_episode_info_head_open', 11 );
add_action( 'masvideos_single_episode_summary', 'masvideos_template_single_episode_meta', 20 );
add_action( 'masvideos_single_episode_summary', 'masvideos_template_single_episode_rating_with_sharing_open', 30 );
add_action( 'masvideos_single_episode_summary', 'masvideos_template_single_episode_avg_rating', 40 );
add_action( 'masvideos_single_episode_summary', 'masvideos_template_single_sharing', 50 );
add_action( 'masvideos_single_episode_summary', 'masvideos_template_single_episode_rating_with_sharing_close', 60 );
add_action( 'masvideos_single_episode_summary', 'masvideos_template_single_episode_info_head_close', 70 );
add_action( 'masvideos_single_episode_summary', 'masvideos_template_single_episode_info_body_open', 80 );
add_action( 'masvideos_single_episode_summary', 'masvideos_template_single_episode_description', 90 );
add_action( 'masvideos_single_episode_summary', 'masvideos_template_single_episode_tags', 100 );
add_action( 'masvideos_single_episode_summary', 'masvideos_template_single_episode_info_body_close', 110 );
add_action( 'masvideos_after_single_episode_summary', 'masvideos_template_single_episode_seasons_tabs', 10 );
add_action( 'masvideos_after_single_episode_summary', 'masvideos_template_single_episode_related_tv_shows', 20 );
add_action( 'masvideos_after_single_episode_summary', 'comments_template', 30 );

add_action( 'masvideos_single_episode_meta', 'masvideos_template_single_episode_duration', 10 );
add_action( 'masvideos_single_episode_meta', 'masvideos_template_single_episode_release_date', 20 );

/**
 * Episode Reviews
 *
 * @see masvideos_episode_review_display_gravatar()
 * @see masvideos_episode_review_display_rating()
 * @see masvideos_episode_review_display_meta()
 * @see masvideos_episode_review_display_comment_text()
 */
add_action( 'masvideos_episode_review_before', 'masvideos_episode_review_display_gravatar', 10 );
add_action( 'masvideos_episode_review_before_comment_meta', 'masvideos_episode_review_display_rating', 10 );
add_action( 'masvideos_episode_review_meta', 'masvideos_episode_review_display_meta', 10 );
add_action( 'masvideos_episode_review_comment_text', 'masvideos_episode_review_display_comment_text', 10 );

/**
 * TV Shows Loop.
 */
add_action( 'masvideos_no_tv_shows_found', 'masvideos_no_tv_shows_found', 10 );
add_action( 'masvideos_tv_shows_loop', 'masvideos_tv_shows_loop_content', 20 );
add_action( 'masvideos_before_tv_shows_loop_item', 'masvideos_template_loop_tv_show_feature_badge', 10 );
add_action( 'masvideos_before_tv_shows_loop_item', 'masvideos_template_loop_tv_show_poster_open', 30 );
add_action( 'masvideos_before_tv_shows_loop_item', 'masvideos_template_loop_tv_show_link_open', 40 );
add_action( 'masvideos_before_tv_shows_loop_item', 'masvideos_template_loop_tv_show_poster', 50 );
add_action( 'masvideos_before_tv_shows_loop_item', 'masvideos_template_loop_tv_show_link_close', 60 );
add_action( 'masvideos_before_tv_shows_loop_item', 'masvideos_template_loop_tv_show_poster_close', 70 );
add_action( 'masvideos_before_tv_shows_loop_item_title', 'masvideos_template_loop_tv_show_body_open', 10 );
add_action( 'masvideos_before_tv_shows_loop_item_title', 'masvideos_template_loop_tv_show_info_open', 20 );

add_action( 'masvideos_tv_shows_loop_item_title', 'masvideos_template_loop_tv_show_info_head_open', 10 );
add_action( 'masvideos_tv_shows_loop_item_title', 'masvideos_template_single_tv_show_meta', 20 );
add_action( 'masvideos_tv_shows_loop_item_title', 'masvideos_template_loop_tv_show_link_open', 30 );
add_action( 'masvideos_tv_shows_loop_item_title', 'masvideos_template_loop_tv_show_title', 40 );
add_action( 'masvideos_tv_shows_loop_item_title', 'masvideos_template_loop_tv_show_link_close', 50 );
add_action( 'masvideos_tv_shows_loop_item_title', 'masvideos_template_loop_tv_show_new_episode', 60 );
add_action( 'masvideos_tv_shows_loop_item_title', 'masvideos_template_loop_tv_show_info_head_close', 70 );

add_action( 'masvideos_after_tv_shows_loop_item_title', 'masvideos_template_loop_tv_show_short_desc', 10 );
add_action( 'masvideos_after_tv_shows_loop_item_title', 'masvideos_template_loop_tv_show_actions', 20 );
add_action( 'masvideos_after_tv_shows_loop_item_title', 'masvideos_template_loop_tv_show_info_close', 30 );
add_action( 'masvideos_after_tv_shows_loop_item', 'masvideos_template_loop_tv_show_review_info_open', 10 );
add_action( 'masvideos_after_tv_shows_loop_item', 'masvideos_template_loop_tv_show_avg_rating', 20 );
add_action( 'masvideos_after_tv_shows_loop_item', 'masvideos_template_loop_tv_show_viewers_count', 30 );
add_action( 'masvideos_after_tv_shows_loop_item', 'masvideos_template_loop_tv_show_review_info_close', 40 );
add_action( 'masvideos_after_tv_shows_loop_item', 'masvideos_template_loop_tv_show_body_close', 50 );

add_action( 'masvideos_after_tv_shows_loop_item', 'masvideos_template_loop_tv_show_hover_area_open', 60 );
add_action( 'masvideos_after_tv_shows_loop_item', 'masvideos_template_loop_tv_show_hover_area_poster_info_open', 70 );
add_action( 'masvideos_after_tv_shows_loop_item', 'masvideos_template_loop_tv_show_poster_open', 75 );
add_action( 'masvideos_after_tv_shows_loop_item', 'masvideos_template_loop_tv_show_link_open', 80 );
add_action( 'masvideos_after_tv_shows_loop_item', 'masvideos_template_loop_tv_show_poster', 90 );
add_action( 'masvideos_after_tv_shows_loop_item', 'masvideos_template_loop_tv_show_link_close', 100 );
add_action( 'masvideos_after_tv_shows_loop_item', 'masvideos_template_loop_tv_show_poster_close', 110 );

add_action( 'masvideos_after_tv_shows_loop_item', 'masvideos_template_loop_tv_show_info_head_open', 120 );
add_action( 'masvideos_after_tv_shows_loop_item', 'masvideos_template_single_tv_show_meta', 130 );
add_action( 'masvideos_after_tv_shows_loop_item', 'masvideos_template_loop_tv_show_link_open', 140 );
add_action( 'masvideos_after_tv_shows_loop_item', 'masvideos_template_loop_tv_show_title', 150 );
add_action( 'masvideos_after_tv_shows_loop_item', 'masvideos_template_loop_tv_show_link_close', 160 );
add_action( 'masvideos_after_tv_shows_loop_item', 'masvideos_template_loop_tv_show_info_head_close', 170 );
add_action( 'masvideos_after_tv_shows_loop_item', 'masvideos_template_loop_tv_show_hover_area_poster_info_close', 180 );

add_action( 'masvideos_after_tv_shows_loop_item', 'masvideos_template_loop_tv_show_hover_area_body_info_open', 190 );
add_action( 'masvideos_after_tv_shows_loop_item', 'masvideos_template_loop_tv_show_seasons_episode_wrap_open', 200 );
add_action( 'masvideos_after_tv_shows_loop_item', 'masvideos_template_loop_tv_show_seasons', 205 );
add_action( 'masvideos_after_tv_shows_loop_item', 'masvideos_template_loop_tv_show_new_episode', 210 );
add_action( 'masvideos_after_tv_shows_loop_item', 'masvideos_template_loop_tv_show_seasons_episode_wrap_close', 220 );

add_action( 'masvideos_after_tv_shows_loop_item', 'masvideos_template_loop_tv_show_review_info_open', 230 );
add_action( 'masvideos_after_tv_shows_loop_item', 'masvideos_template_loop_tv_show_avg_rating', 240 );
add_action( 'masvideos_after_tv_shows_loop_item', 'masvideos_template_loop_tv_show_viewers_count', 250 );
add_action( 'masvideos_after_tv_shows_loop_item', 'masvideos_template_loop_tv_show_review_info_close', 260 );
add_action( 'masvideos_after_tv_shows_loop_item', 'masvideos_template_loop_tv_show_actions', 270 );

add_action( 'masvideos_after_tv_shows_loop_item', 'masvideos_template_loop_tv_show_hover_area_body_info_close', 280 );
add_action( 'masvideos_after_tv_shows_loop_item', 'masvideos_template_loop_tv_show_hover_area_info_close', 290 );


/**
 * TV Shows Loop Template: TV Shows Widget.
 */
add_action( 'masvideos_before_tv_show_widget_item', 'masvideos_template_loop_tv_show_poster_open', 10 );
add_action( 'masvideos_before_tv_show_widget_item', 'masvideos_template_loop_tv_show_link_open', 20 );
add_action( 'masvideos_before_tv_show_widget_item', 'masvideos_template_loop_tv_show_poster', 30 );
add_action( 'masvideos_before_tv_show_widget_item', 'masvideos_template_loop_tv_show_link_close', 40 );
add_action( 'masvideos_before_tv_show_widget_item', 'masvideos_template_loop_tv_show_poster_close', 50 );
add_action( 'masvideos_before_tv_show_widget_item', 'masvideos_template_loop_tv_show_body_open', 60 );
add_action( 'masvideos_before_tv_show_widget_item_title', 'masvideos_template_single_tv_show_release_year', 10 );
add_action( 'masvideos_tv_show_widget_item_title', 'masvideos_template_loop_tv_show_link_open', 10 );
add_action( 'masvideos_tv_show_widget_item_title', 'masvideos_template_loop_tv_show_title', 20 );
add_action( 'masvideos_tv_show_widget_item_title', 'masvideos_template_loop_tv_show_link_close', 30 );
add_action( 'masvideos_after_tv_show_widget_item_title', 'masvideos_template_single_tv_show_genres', 10 );
add_action( 'masvideos_after_tv_show_widget_item', 'masvideos_template_loop_tv_show_body_close', 10 );

/**
 * TV Show Single.
 */
add_action( 'masvideos_single_tv_show_summary', 'masvideos_template_single_tv_show_title', 5 );
add_action( 'masvideos_after_single_tv_show_summary', 'comments_template', 10 );

add_action( 'masvideos_single_tv_show_meta', 'masvideos_template_single_tv_show_genres', 10 );
add_action( 'masvideos_single_tv_show_meta', 'masvideos_template_single_tv_show_release_year', 20 );

/**
 * Videos Loop.
 */
add_action( 'masvideos_no_videos_found', 'masvideos_no_videos_found', 10 );
add_action( 'masvideos_videos_loop', 'masvideos_videos_loop_content', 20 );
add_action( 'masvideos_before_videos_loop_item', 'masvideos_template_loop_video_feature_badge', 10 );
add_action( 'masvideos_before_videos_loop_item', 'masvideos_template_loop_video_container_open', 20 );
add_action( 'masvideos_before_videos_loop_item', 'masvideos_template_loop_video_poster_open', 30 );
add_action( 'masvideos_before_videos_loop_item', 'masvideos_template_loop_video_link_open', 40 );
add_action( 'masvideos_before_videos_loop_item', 'masvideos_template_loop_video_poster', 50 );
add_action( 'masvideos_before_videos_loop_item', 'masvideos_template_loop_video_link_close', 60 );
add_action( 'masvideos_before_videos_loop_item', 'masvideos_template_loop_video_poster_close', 70 );
add_action( 'masvideos_before_videos_loop_item', 'masvideos_template_loop_video_container_close', 80 );
add_action( 'masvideos_before_videos_loop_item_title', 'masvideos_template_loop_video_body_open', 10 );
add_action( 'masvideos_before_videos_loop_item_title', 'masvideos_template_loop_video_info_open', 20 );
add_action( 'masvideos_videos_loop_item_title', 'masvideos_template_loop_video_info_head_open', 10 );
add_action( 'masvideos_videos_loop_item_title', 'masvideos_template_loop_video_link_open', 20 );
add_action( 'masvideos_videos_loop_item_title', 'masvideos_template_loop_video_title', 30 );
add_action( 'masvideos_videos_loop_item_title', 'masvideos_template_loop_video_link_close', 40 );
add_action( 'masvideos_videos_loop_item_title', 'masvideos_template_loop_video_meta', 50 );
add_action( 'masvideos_videos_loop_item_title', 'masvideos_template_loop_video_info_head_close', 60 );
add_action( 'masvideos_after_videos_loop_item_title', 'masvideos_template_loop_video_short_desc', 10 );
add_action( 'masvideos_after_videos_loop_item_title', 'masvideos_template_loop_video_actions', 20 );
add_action( 'masvideos_after_videos_loop_item_title', 'masvideos_template_loop_video_info_close', 30 );
add_action( 'masvideos_after_videos_loop_item', 'masvideos_template_loop_video_review_info_open', 10 );
add_action( 'masvideos_after_videos_loop_item', 'masvideos_template_loop_video_avg_rating', 20 );
add_action( 'masvideos_after_videos_loop_item', 'masvideos_template_loop_video_viewers_count', 30 );
add_action( 'masvideos_after_videos_loop_item', 'masvideos_template_loop_video_review_info_close', 40 );
add_action( 'masvideos_after_videos_loop_item', 'masvideos_template_loop_video_body_close', 50 );

/**
 * Videos Loop Template: Videos Widget.
 */
add_action( 'masvideos_before_video_widget_item', 'masvideos_template_loop_video_poster_open', 10 );
add_action( 'masvideos_before_video_widget_item', 'masvideos_template_loop_video_link_open', 20 );
add_action( 'masvideos_before_video_widget_item', 'masvideos_template_loop_video_poster', 30 );
add_action( 'masvideos_before_video_widget_item', 'masvideos_template_loop_video_link_close', 40 );
add_action( 'masvideos_before_video_widget_item', 'masvideos_template_loop_video_poster_close', 50 );
add_action( 'masvideos_before_video_widget_item', 'masvideos_template_loop_video_body_open', 60 );
add_action( 'masvideos_video_widget_item_title', 'masvideos_template_loop_video_link_open', 10 );
add_action( 'masvideos_video_widget_item_title', 'masvideos_template_loop_video_title', 20 );
add_action( 'masvideos_video_widget_item_title', 'masvideos_template_loop_video_link_close', 30 );
add_action( 'masvideos_after_video_widget_item_title', 'masvideos_template_single_video_categories', 10 );
add_action( 'masvideos_after_video_widget_item', 'masvideos_template_loop_video_body_close', 10 );


/**
 * Video Single.
 */
add_action( 'masvideos_before_single_video_summary', 'masvideos_template_single_video_video', 10 );
add_action( 'masvideos_single_video_summary', 'masvideos_template_single_video_title', 5 );
add_action( 'masvideos_single_video_summary', 'masvideos_template_single_video_meta', 10 );
add_action( 'masvideos_after_single_video_summary', 'masvideos_related_videos', 20 );
add_action( 'masvideos_after_single_video_summary', 'comments_template', 30 );

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

/**
 * Movies Loop.
 */
add_action( 'masvideos_no_movies_found', 'masvideos_no_movies_found', 10 );
add_action( 'masvideos_movies_loop', 'masvideos_movies_loop_content', 20 );
add_action( 'masvideos_before_movies_loop_item', 'masvideos_template_loop_movie_feature_badge', 5 );
add_action( 'masvideos_before_movies_loop_item', 'masvideos_template_loop_movie_poster_open', 10 );
add_action( 'masvideos_before_movies_loop_item', 'masvideos_template_loop_movie_link_open', 20 );
add_action( 'masvideos_before_movies_loop_item', 'masvideos_template_loop_movie_poster', 30 );
add_action( 'masvideos_before_movies_loop_item', 'masvideos_template_loop_movie_link_close', 40 );
add_action( 'masvideos_before_movies_loop_item', 'masvideos_template_loop_movie_poster_close', 50 );
add_action( 'masvideos_before_movies_loop_item_title', 'masvideos_template_loop_movie_body_open', 10 );
add_action( 'masvideos_before_movies_loop_item_title', 'masvideos_template_loop_movie_info_open', 20 );
add_action( 'masvideos_movies_loop_item_title', 'masvideos_template_loop_movie_info_head_open', 10 );
add_action( 'masvideos_movies_loop_item_title', 'masvideos_template_single_movie_meta', 20 );
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
 * Movies Loop Template: Movies Widget.
 */
add_action( 'masvideos_before_movie_widget_item', 'masvideos_template_loop_movie_poster_open', 10 );
add_action( 'masvideos_before_movie_widget_item', 'masvideos_template_loop_movie_link_open', 20 );
add_action( 'masvideos_before_movie_widget_item', 'masvideos_template_loop_movie_poster', 30 );
add_action( 'masvideos_before_movie_widget_item', 'masvideos_template_loop_movie_link_close', 40 );
add_action( 'masvideos_before_movie_widget_item', 'masvideos_template_loop_movie_poster_close', 50 );
add_action( 'masvideos_before_movie_widget_item', 'masvideos_template_loop_movie_body_open', 60 );
add_action( 'masvideos_before_movie_widget_item_title', 'masvideos_template_single_movie_release_year', 10 );
add_action( 'masvideos_movie_widget_item_title', 'masvideos_template_loop_movie_link_open', 10 );
add_action( 'masvideos_movie_widget_item_title', 'masvideos_template_loop_movie_title', 20 );
add_action( 'masvideos_movie_widget_item_title', 'masvideos_template_loop_movie_link_close', 30 );
add_action( 'masvideos_after_movie_widget_item_title', 'masvideos_template_single_movie_genres', 10 );
add_action( 'masvideos_after_movie_widget_item', 'masvideos_template_loop_movie_body_close', 10 );

/**
 * Movie Single.
 */
add_action( 'masvideos_before_single_movie_summary', 'masvideos_template_single_movie_movie', 10 );
add_action( 'masvideos_single_movie_summary', 'masvideos_template_single_movie_title', 5 );
add_action( 'masvideos_after_single_movie_summary', 'comments_template', 10 );

add_action( 'masvideos_single_movie_meta', 'masvideos_template_single_movie_genres', 10 );
add_action( 'masvideos_single_movie_meta', 'masvideos_template_single_movie_release_year', 20 );

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
 * Footer.
 *
 * @see  masvideos_print_js()
 */
add_action( 'wp_footer', 'masvideos_print_js', 25 );
