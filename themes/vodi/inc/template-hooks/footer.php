<?php
/**
 * Template Hooks used in Footer
 */

add_action( 'vodi_footer_v1', 'vodi_footer_top_bar',        10 );
add_action( 'vodi_footer_v1', 'vodi_footer_widgets',        20 );
add_action( 'vodi_footer_v1', 'vodi_footer_site_info',      30 );

add_action( 'vodi_footer_top_bar', 'vodi_footer_logo',           10 );
add_action( 'vodi_footer_top_bar', 'vodi_footer_social_icons_1', 20 );

add_action( 'vodi_footer_site_info', 'vodi_credit',    10 );
add_action( 'vodi_footer_site_info', 'vodi_policy',    20 );


add_action( 'vodi_footer_v2',   'vodi_footer_menu_primary_menu',    10 );
add_action( 'vodi_footer_v2',   'vodi_footer_bottom',               20 );

add_action( 'vodi_footer_bottom',   'vodi_footer_bottom_content_div_open',  10 );
add_action( 'vodi_footer_bottom',   'vodi_footer_top_bar',                  20 );
add_action( 'vodi_footer_bottom',   'vodi_footer_menu_secondary_menu',      30 );
add_action( 'vodi_footer_bottom',   'vodi_credit',                          40 );
add_action( 'vodi_footer_bottom',   'vodi_footer_menu_tertiary_menu',       50 );
add_action( 'vodi_footer_bottom',   'vodi_footer_div_close',                60 );
add_action( 'vodi_footer_bottom',   'vodi_footer_news_letter',              70 );

add_action( 'vodi_footer_v3', 'vodi_footer_bar',     10 );
add_action( 'vodi_footer_v3', 'vodi_credit',            20 );

add_action( 'vodi_footer_bar', 'vodi_footer_logo',         10 );
add_action( 'vodi_footer_bar', 'vodi_footer_menu',         20 );
add_action( 'vodi_footer_bar', 'vodi_footer_social_icons', 30 );

add_action( 'vodi_footer_comingsoon', 'vodi_comingsoon_footer');

add_action( 'vodi_footer_landing_v1', 'vodi_landing_v1_footer');

add_action( 'vodi_footer_landing_v2', 'vodi_landing_v2_footer');
