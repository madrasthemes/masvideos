<?php
/**
 * Options available for Shop sub menu of Theme Options
 * 
 */

// $nav_menus    = get_terms( 'nav_menu' );
// $menu_options = array(
//     '0' => esc_html__( 'Default WooCommerce account menu', 'vodi' )
// );

// foreach( $nav_menus as $nav_menu ) {
//     $menu_options[ $nav_menu->term_id ] = $nav_menu->name;
// }

$header_options     = apply_filters( 'vodi_header_options_args', array(
    'title'     => esc_html__( 'Header', 'vodi' ),
    'icon'      => 'far fa-arrow-alt-circle-up',
    'fields'    => array(
        array(
            'title'     => esc_html__( 'Logo', 'vodi' ),
            'id'        => 'logo_start',
            'type'      => 'section',
            'indent'    => true
        ),

        array(
            'title'     => esc_html__( 'Logo SVG', 'vodi' ),
            'subtitle'  => esc_html__( 'Enable to display svg logo instead of site title.', 'vodi' ),
            'desc'      => esc_html__( 'This will not work when you use site logo in customizer.', 'vodi' ),
            'id'        => 'logo_svg',
            'type'      => 'switch',
            'on'        => esc_html__( 'Enabled', 'vodi' ),
            'off'       => esc_html__( 'Disabled', 'vodi' ),
            'default'   => 1,
        ),

        array(
            'id'        => 'logo_end',
            'type'      => 'section',
            'indent'    => false
        ),

        array(
            'title'     => esc_html__( 'Masthead', 'vodi' ),
            'id'        => 'masthead_start',
            'type'      => 'section',
            'indent'    => true
        ),

        array(
            'title'     => esc_html__('Header Style', 'vodi'),
            'subtitle'  => esc_html__('Select the header style.', 'vodi'),
            'id'        => 'header_version',
            'type'      => 'select',
            'options'   => array(
                'v1'          => esc_html__( 'Header v1', 'vodi' ),
                'v2'          => esc_html__( 'Header v2', 'vodi' ),
                'v3'          => esc_html__( 'Header v3', 'vodi' ),
                'v4'          => esc_html__( 'Header v4', 'vodi' ),
                'landing v1'  => esc_html__( 'Header Landing v1', 'vodi' ),
                'landing v2'  => esc_html__( 'Header Landing v2', 'vodi' ),
                'coming soon' => esc_html__( 'Header Coming Soon', 'vodi' ),
            ),
            'default'   => 'v1',
        ),

         array(
            'id'        => 'masthead_end',
            'type'      => 'section',
            'indent'    => false
        ),
    )
) );