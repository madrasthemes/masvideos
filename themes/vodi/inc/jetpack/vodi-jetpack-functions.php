<?php
/**
 *
 */

function vodi_show_jetpack_share() {
    if ( function_exists( 'sharing_display' ) ) {
        sharing_display( '', true );
    }
     
    if ( class_exists( 'Jetpack_Likes' ) ) {
        $custom_likes = new Jetpack_Likes;
        echo wp_kses_post ( $custom_likes->post_likes( '' ) );
    }
}