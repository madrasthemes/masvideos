<?php
/**
 * Hooks used for Category
 */


// add_action( 'vodi_before_product_loop_item', 'vodi_product_item_wrap_open',                       1 );
// add_action( 'vodi_before_product_loop_item', 'vodi_product_item_header_open',                     5 );
// add_action( 'vodi_before_product_loop_item', 'vodi_template_loop_product_link_open',             10 );
// add_action( 'vodi_before_product_loop_item_title', 'vodi_show_movie_loop_featured_badge',        10 ); 
// add_action( 'vodi_before_product_loop_item_title', 'vodi_template_loop_movie_poster',            10 );
// add_action( 'vodi_before_product_loop_item_title', 'vodi_template_loop_product_link_close',      80 );
// add_action( 'vodi_before_product_loop_item_title', 'vodi_product_item_header_close',             90 );

// add_action( 'vodi_product_loop_item_title', 'vodi_product_item_body_open',                        5 );
// add_action( 'vodi_product_loop_item_title', 'vodi_template_loop_movie_info_wrapper',              6 );
// add_action( 'vodi_product_loop_item_title', 'vodi_template_loop_product_link_open',               7 );
// add_action( 'vodi_product_loop_item_title', 'vodi_template_loop_movie_meta',                      8 );
// add_action( 'vodi_product_loop_item_title', 'vodi_template_loop_movie_title',                    10 );
// add_action( 'vodi_product_loop_item_title', 'vodi_template_single_excerpt',                      15 );
// add_action( 'vodi_product_loop_item_title', 'vodi_template_loop_product_link_close',             20 );

// add_action( 'vodi_after_product_loop_item_title', 'vodi_product_item_actions',                   30 );
// add_action( 'vodi_after_product_loop_item_title', 'vodi_template_div_close',                     36 );

// add_action( 'vodi_after_product_loop_item_title', 'vodi_template_loop_review_info_wrapper',       40 );
// add_action( 'vodi_after_product_loop_item_title', 'vodi_template_loop_advanced_movie_ratings',    45 );
// add_action( 'vodi_after_product_loop_item_title', 'vodi_template_loop_views_count',               59 );
// add_action( 'vodi_after_product_loop_item_title', 'vodi_template_div_close',                      60 );
// add_action( 'vodi_after_product_loop_item', 'vodi_product_item_body_close',                       65 );



add_action( 'vodi_movie_category', 'vodi_movie_category_header');
add_action( 'vodi_movie_category', 'vodi_movie_category_content');
add_action( 'vodi_movie_category', 'vodi_page_control_bar_bottom' );

add_action( 'vodi_video_category', 'vodi_video_category_header');
add_action( 'vodi_video_category', 'vodi_video_category_content');
add_action( 'vodi_video_category', 'vodi_page_control_bar_bottom' );