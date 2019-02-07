<?php
/**
 * Template functions in Home v6
 *
 */
if ( ! function_exists( 'vodi_home_v7_video_section' ) ) {
    function vodi_home_v7_video_section() {
        if( vodi_is_masvideos_activated() ) {
            $args = apply_filters( 'vodi_home_v7_video_section_default_args', array(
                'section_title'         => esc_html__( ' Featured TV Series', 'vodi' ),
                'section_nav_links'     => array(
                    array(
                        'nav_title'         => esc_html__( 'Today', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                    array(
                        'nav_title'         => esc_html__( 'This week', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                    array(
                        'nav_title'         => esc_html__( 'This month', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                    array(
                        'nav_title'         => esc_html__( 'Last 3 months', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                ),
                'section_background'    => '',
                'videos_shortcode'      => 'mas_videos',
                'shortcode_atts'        => array(
                    'columns'               => '4',
                    'limit'                 => '8',
                ),
            ) );

            vodi_video_section( $args );
        }
    }
}