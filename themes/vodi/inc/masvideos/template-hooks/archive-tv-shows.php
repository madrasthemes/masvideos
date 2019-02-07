<?php
remove_action( 'masvideos_before_tv_shows_loop', 'masvideos_tv_shows_control_bar', 10 );
remove_action( 'masvideos_after_tv_shows_loop', 'masvideos_tv_shows_page_control_bar', 10 );

add_action( 'masvideos_before_tv_shows_loop', 'vodi_tv_shows_loop_title',           10 );
add_action( 'masvideos_before_tv_shows_loop', 'vodi_tv_shows_control_bar_top',      20 );
add_action( 'masvideos_before_tv_shows_loop', 'vodi_archive_wrapper_open',        30 );
add_action( 'masvideos_after_tv_shows_loop', 'vodi_archive_wrapper_close',        10 );
add_action( 'masvideos_after_tv_shows_loop', 'vodi_tv_shows_control_bar_bottom',    20 );

/**
 * Control Bar Top
 *
 * @see vodi_tv_shows_control_bar_top_open()
 * @see vodi_tv_shows_control_bar_top_left()
 * @see vodi_tv_shows_control_bar_top_right()
 * @see vodi_tv_shows_control_bar_top_close()
 */
add_action( 'vodi_tv_shows_control_bar_top', 'vodi_tv_shows_control_bar_top_open', 10 );
add_action( 'vodi_tv_shows_control_bar_top', 'vodi_tv_shows_control_bar_top_left', 20 );
add_action( 'vodi_tv_shows_control_bar_top', 'vodi_tv_shows_control_bar_top_right', 30 );
add_action( 'vodi_tv_shows_control_bar_top', 'vodi_tv_shows_control_bar_top_close', 999 );

/**
 * Control Bar Top
 *
 * @see vodi_tv_shows_control_bar_top_open()
 * @see vodi_tv_shows_control_bar_top_left()
 * @see vodi_tv_shows_control_bar_top_right()
 * @see vodi_tv_shows_control_bar_top_close()
 */
add_action( 'vodi_tv_shows_control_bar_bottom', 'vodi_tv_shows_control_bar_bottom_open', 10 );
add_action( 'vodi_tv_shows_control_bar_bottom', 'masviseos_tv_shows_per_page', 20 );
add_action( 'vodi_tv_shows_control_bar_bottom', 'masvideos_tv_shows_count', 30 );
add_action( 'vodi_tv_shows_control_bar_bottom', 'masvideos_tv_shows_pagination', 40 );
add_action( 'vodi_tv_shows_control_bar_bottom', 'vodi_tv_shows_control_bar_bottom_close', 999 );

