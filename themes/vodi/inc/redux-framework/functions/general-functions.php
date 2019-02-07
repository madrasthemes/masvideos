<?php
/**
 * Filter functions for General Section of Theme Options
 */

if ( ! function_exists( 'redux_apply_bg_style' ) ) {
    function redux_apply_bg_style( $bg_style ) {
        global $vodi_options;

        if( isset( $vodi_options['bg_style'] ) ) {
            $bg_style = $vodi_options['bg_style'];
        }

        return $bg_style;
    }
}