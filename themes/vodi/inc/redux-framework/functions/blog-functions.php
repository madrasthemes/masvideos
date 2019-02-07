<?php
/**
 * Filter functions for Blog Section of Theme Options
 */
if ( ! function_exists( 'redux_apply_vodi_get_blog_layout' ) ) {
    function redux_apply_vodi_get_blog_layout( $layout ) {
        global $vodi_options;

        if( isset( $vodi_options['blog_layout'] ) && !empty( $vodi_options['blog_layout'] ) ) {
            $layout = $vodi_options['blog_layout'];
        }

        return $layout;
    }
}

if ( ! function_exists( 'redux_toggle_author_info' ) ) {
    function redux_toggle_author_info( $enable ) {
        global $vodi_options;

        if ( ! isset( $vodi_options['show_blog_post_author_info'] ) ) {
            $vodi_options['show_blog_post_author_info'] = true;
        }

        if ( $vodi_options['show_blog_post_author_info'] ) {
            $enable = true;
        } else {
            $enable = false;
        }
        
        return $enable;
    }
}
