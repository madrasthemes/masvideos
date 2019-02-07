<?php
/**
 * Filter functions for Footer Section of Theme Options
 */

if ( ! function_exists( 'redux_apply_footer_version' ) ) {
    function redux_apply_footer_version( $footer_version ) {
        global $vodi_options;

        if( isset( $vodi_options['footer_version'] ) && !empty( $vodi_options['footer_version'] ) ) {
            $footer_version = $vodi_options['footer_version'];
        }

        return $footer_version;
    }
}