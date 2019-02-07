<?php
/**
 * Functions used in Footer
 */

if ( ! function_exists( 'vodi_get_footer_version' ) ) {
    /**
     * Gets the Footer version set in theme options
     */
    function vodi_get_footer_version() {

        $footer_version = apply_filters( 'vodi_footer_version', 'v1' );

        if ( is_page() ) {
            global $post;
            $clean_page_meta_values = get_post_meta( $post->ID, '_vodi_page_metabox', true );
            $page_meta_values = maybe_unserialize( $clean_page_meta_values );

            if ( isset( $page_meta_values['site_footer_style'] ) && ! empty( $page_meta_values['site_footer_style'] ) ) {
                $footer_version = apply_filters( 'vodi_page_footer_version', $page_meta_values['site_footer_style'] );
            }
        }
        
        return $footer_version;
    }
}
