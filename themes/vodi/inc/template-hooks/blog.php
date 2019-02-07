<?php
/**
 * Hooks used for Posts, Single Post
 */
add_action( 'vodi_loop_before', 'vodi_loop_wrap_open',      10 );
add_action( 'vodi_loop_after',  'vodi_loop_wrap_close',     10 );
add_action( 'vodi_loop_after',  'vodi_paging_nav',          20 );

add_action( 'vodi_loop_post',   'vodi_post_attachment',     10 );
add_action( 'vodi_loop_post',   'vodi_post_summary',        20 );

add_action( 'vodi_post_summary', 'vodi_post_header',  10 );
add_action( 'vodi_post_summary', 'vodi_post_excerpt', 20 );

add_action( 'vodi_post_header', 'vodi_post_title', 20 );
add_action( 'vodi_post_header', 'vodi_post_meta',  30 );

add_action( 'vodi_post_meta',   'vodi_post_categories', 10 );
add_action( 'vodi_post_meta',   'vodi_post_date', 20 );
add_action( 'vodi_post_meta',   'vodi_post_comments', 30 );

add_action( 'vodi_loop_post_aside', 'vodi_post_meta',        10 );
add_action( 'vodi_loop_post_aside', 'vodi_post_the_content', 20 );
add_action( 'vodi_loop_post_quote', 'vodi_post_the_content', 10 );
add_action( 'vodi_loop_post_link',  'vodi_post_the_content', 10 );

add_action( 'vodi_single_post_top', 'vodi_post_attachment' );
add_action( 'vodi_single_post_top', 'vodi_wrap_single_post_start' );

add_action( 'vodi_single_post', 'vodi_single_post_header' );

add_action( 'vodi_single_post_header', 'vodi_single_post_title', 20 );
add_action( 'vodi_single_post_header', 'vodi_post_meta',  30 );

add_action( 'vodi_single_post', 'vodi_jetpack_share' );

add_action( 'vodi_single_post', 'vodi_post_content' );

add_action( 'vodi_single_post_bottom', 'vodi_wrap_single_post_end' );

add_action( 'vodi_single_post_bottom', 'vodi_jetpack_share' );

add_action( 'vodi_single_post_bottom', 'vodi_post_nav' );
add_action( 'vodi_single_post_bottom', 'vodi_display_comments' );

add_action( 'vodi_single_post_after', 'vodi_related_posts', 10 );

add_filter( 'previous_post_link', 'vodi_adjacent_post_link', 10, 5 );
add_filter( 'next_post_link', 'vodi_adjacent_post_link', 10, 5 );

add_filter( 'excerpt_length',              'vodi_custom_excerpt_length', 100 );