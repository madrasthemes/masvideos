<?php
/**
 * Vodi hooks
 *
 * @package vodi
 */

require_once get_template_directory() . '/inc/template-hooks/blocks.php';
require_once get_template_directory() . '/inc/template-hooks/blog.php';
require_once get_template_directory() . '/inc/template-hooks/page.php';
require_once get_template_directory() . '/inc/template-hooks/home.php';
require_once get_template_directory() . '/inc/template-hooks/footer.php';
require_once get_template_directory() . '/inc/template-hooks/landing-page.php';
require_once get_template_directory() . '/inc/template-hooks/coming-soon.php';
require_once get_template_directory() . '/inc/template-hooks/category.php';

if ( function_exists( 'vodi_is_masvideos_activated' ) && vodi_is_masvideos_activated() ) {
    require_once get_template_directory() . '/inc/masvideos/template-hooks.php';
}