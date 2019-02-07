<?php
/**
 * Options available for Blog sub menu of Theme Options
 * 
 */

$blog_options   = apply_filters( 'vodi_blog_options_args', array(
    'title'     => esc_html__( 'Blog', 'vodi' ),
    'icon'      => 'far fa-list-alt',
    'fields'    => array(
        array(
            'title'     => esc_html__('Blog Page Layout', 'vodi'),
            'subtitle'  => esc_html__('Select the layout for the Blog Listing.', 'vodi'),
            'id'        => 'blog_layout',
            'type'      => 'select',
            'options'   => array(
                'full-width'        => esc_html__( 'Full Width', 'vodi' ),
                'left-sidebar'      => esc_html__( 'Left Sidebar', 'vodi' ),
                'right-sidebar'     => esc_html__( 'Right Sidebar', 'vodi' ),
            ),
            'default'   => 'full-width',
        ),
        array(
            'title'     => esc_html__( 'Blog Post Author Info', 'vodi' ),
            'id'        => 'show_blog_post_author_info',
            'on'        => esc_html__('Show', 'vodi'),
            'off'       => esc_html__('Hide', 'vodi'),
            'type'      => 'switch',
            'default'   => true,
        ),
    )
) );