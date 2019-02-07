<?php
/**
 * Functions used in Header
 */

if ( ! function_exists( 'vodi_get_header_version' ) ) {
    /**
     * Gets the Header version set in theme options
     */
    function vodi_get_header_version() {

        $header_version = apply_filters( 'vodi_header_version', 'v1' );

        if ( is_page() ) {
            global $post;
            $clean_page_meta_values = get_post_meta( $post->ID, '_vodi_page_metabox', true );
            $page_meta_values = maybe_unserialize( $clean_page_meta_values );

            if ( isset( $page_meta_values['site_header_style'] ) && ! empty( $page_meta_values['site_header_style'] ) ) {
                $header_version = apply_filters( 'vodi_page_header_version', $page_meta_values['site_header_style'] );
            }
        }
        
        return $header_version;
    }
}
