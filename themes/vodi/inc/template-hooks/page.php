<?php
/**
 * Hooks used for Page
 */
add_action( 'vodi_page', 'vodi_page_content', 20 );
add_action( 'vodi_page', 'vodi_display_comments', 30 );

add_action( 'vodi_content_top', 'vodi_container_start', 0 );
add_action( 'vodi_content_bottom', 'vodi_container_end', 0 );