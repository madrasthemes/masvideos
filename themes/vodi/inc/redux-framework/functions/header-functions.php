
<?php
/**
 * Filter functions for Header Section of Theme Options
 */

if( ! function_exists( 'redux_toggle_logo_svg' ) ) {
    function redux_toggle_logo_svg() {
        global $vodi_options;

        if( isset( $vodi_options['logo_svg'] ) && $vodi_options['logo_svg'] == '1' ) {
            $logo_svg = true;
        } else {
            $logo_svg = false;
        }

        return $logo_svg;
    }
}

if ( ! function_exists( 'redux_apply_header_version' ) ) {
    function redux_apply_header_version( $header_version ) {
        global $vodi_options;

        if( isset( $vodi_options['header_version'] ) && !empty( $vodi_options['header_version'] ) ) {
            $header_version = $vodi_options['header_version'];
        }

        return $header_version;
    }
}