<?php
/**
 * Vodi template functions.
 *
 * @package vodi
 */

require_once get_template_directory() . '/inc/template-tags/blog.php';
require_once get_template_directory() . '/inc/template-tags/blocks.php';
require_once get_template_directory() . '/inc/template-tags/page.php';
require_once get_template_directory() . '/inc/template-tags/home.php';
require_once get_template_directory() . '/inc/template-tags/footer.php';
require_once get_template_directory() . '/inc/template-tags/landing-page.php';
require_once get_template_directory() . '/inc/template-tags/coming-soon.php';
require_once get_template_directory() . '/inc/template-tags/category.php';

if ( function_exists( 'vodi_is_masvideos_activated' ) && vodi_is_masvideos_activated() ) {
    require_once get_template_directory() . '/inc/masvideos/template-tags.php';
}

