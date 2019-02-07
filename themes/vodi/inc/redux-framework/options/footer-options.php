<?php
/**
 * Options available for Footer sub menu in Theme Options
 */

$footer_options = apply_filters( 'vodi_footer_options_args', array(
    'title'     => esc_html__( 'Footer', 'vodi' ),
    'icon'      => 'far fa-arrow-alt-circle-down',
    'fields'    => array(
        array(
            'title'     => esc_html__('Footer Style', 'vodi'),
            'subtitle'  => esc_html__('Select the footer style.', 'vodi'),
            'id'        => 'footer_version',
            'type'      => 'select',
            'options'   => array(
                'v1'          => esc_html__( 'Footer v1', 'vodi' ),
                'v2'          => esc_html__( 'Footer v2', 'vodi' ),
                'v3'          => esc_html__( 'Footer v3', 'vodi' ),
                'landing v1'  => esc_html__( 'Footer Landing v1', 'vodi' ),
                'landing v2'  => esc_html__( 'Footer Landing v2', 'vodi' ),
                'coming soon' => esc_html__( 'Footer Coming Soon', 'vodi' ),
            ),
            'default'   => 'v1',
        ),
    )
) );