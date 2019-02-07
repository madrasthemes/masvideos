<?php
/**
 * Loop Hooks
 */

add_action( 'masvideos_before_movies_slider_loop_item',         'movies_slider_template_loop_open', 10 );

add_action( 'masvideos_before_movies_slider_loop_item_title',   'movies_slider_action_button',  10 );

add_action( 'masvideos_movies_slider_loop_item_title',          'masvideos_template_loop_movie_link_open',      10 );
add_action( 'masvideos_movies_slider_loop_item_title',          'masvideos_template_loop_movie_title',          20 );
add_action( 'masvideos_movies_slider_loop_item_title',          'masvideos_template_loop_movie_link_close',     30 );
add_action( 'masvideos_movies_slider_loop_item_title',          'movies_slider_loop_movie_meta',                40 );

add_action( 'masvideos_after_movies_slider_loop_item_title',    'masvideos_template_loop_movie_short_desc_wrap_open',   10 );
add_action( 'masvideos_after_movies_slider_loop_item_title',    'masvideos_template_loop_movie_short_desc',             20 );
add_action( 'masvideos_after_movies_slider_loop_item_title',    'masvideos_template_loop_movie_short_desc_wrap_close',  30 );
add_action( 'masvideos_after_movies_slider_loop_item_title',    'masvideos_template_loop_movie_actions',                40 );

add_action( 'masvideos_after_movies_slider_loop_item',          'movies_slider_template_loop_close',    10 );


add_action( 'masvideos_before_movie_list_item',         		'movie_list_template_loop_open', 10 );
add_action( 'masvideos_before_movie_list_item', 				'movie_list_template_loop_movie_poster_open', 20 );
add_action( 'masvideos_before_movie_list_item', 				'movie_list_template_loop_movie_link_open', 30 );
add_action( 'masvideos_before_movie_list_item',                 'movie_list_template_loop_movie_poster', 40 );
add_action( 'masvideos_before_movie_list_item', 				'movie_list_template_loop_movie_link_close', 50 );
add_action( 'masvideos_before_movie_list_item', 				'movie_list_template_loop_movie_poster_close', 60 );


add_action( 'masvideos_movie_list_item_title', 					'movie_list_template_loop_movie_body_open', 10 );
add_action( 'masvideos_movie_list_item_title', 					'movie_list_template_loop_movie_release', 20 );
add_action( 'masvideos_movie_list_item_title', 					'movie_list_template_loop_movie_link_open', 30 );
add_action( 'masvideos_movie_list_item_title',                  'movie_list_template_loop_movie_title', 40 );
add_action( 'masvideos_movie_list_item_title', 					'movie_list_template_loop_movie_link_close', 50 );
add_action( 'masvideos_movie_list_item_title', 					'movie_list_template_loop_movie_category', 60 );
add_action( 'masvideos_movie_list_item_title', 					'movie_list_template_loop_movie_body_close', 70 );

add_action( 'masvideos_after_movie_list_item',          		'movie_list_template_loop_close',    10 );