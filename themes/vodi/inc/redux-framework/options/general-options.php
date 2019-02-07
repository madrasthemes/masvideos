<?php
/**
 * General Theme Options
 *
 */

$general_options = apply_filters( 'vodi_general_options_args', array(
    'title'     => esc_html__( 'General', 'vodi' ),
    'icon'      => 'far fa-dot-circle',
    'fields'    => array(
        array(
            'title'     => esc_html__('Page Backgroud Style', 'vodi'),
            'subtitle'  => esc_html__('Select the background style.', 'vodi'),
            'id'        => 'bg_style',
            'type'      => 'select',
            'options'   => array(
                'light'            => esc_html__( 'Light', 'vodi' ),
                'dark'            => esc_html__( 'Dark', 'vodi' ),
            ),
            'default'   => 'light',
        ),
    )
) );
