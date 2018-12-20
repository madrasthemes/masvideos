<?php
/**
 * Gutenberg Blocks
 *
 * @package MasVideos/Classes
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * MasVideos Gutenberg Blocks class.
 */
class MasVideos_Gutenberg_Blocks {

    /**
     * Init blocks.
     */
    public static function init() {
        if( function_exists( 'register_block_type' ) ) {
            $blocks = array(
                'videos'    => array(
                    'attributes'        => array(
                        'limit'         => array(
                            'type'      => 'number',
                            'default'   => 10
                        ),
                        'columns'       => array(
                            'type'      => 'number',
                            'default'   => 4
                        ),
                        'orderby'       => array(
                            'type'      => 'string',
                            'default'   => 'date'
                        ),
                        'order'         => array(
                            'type'      => 'string',
                            'default'   => 'DESC'
                        ),
                        'featured'    => array(
                            'type'      => 'boolean',
                            'default'   => false
                        ),
                        'top_rated'    => array(
                            'type'      => 'boolean',
                            'default'   => false
                        ),
                    ),
                    'editor_script'     => 'masvideos-videos', 
                    'render_callback'   => array( 'MasVideos_Shortcodes', 'videos' ),
                ),
                'movies'    => array(
                    'attributes'        => array(
                        'limit'         => array(
                            'type'      => 'number',
                            'default'   => 10
                        ),
                        'columns'       => array(
                            'type'      => 'number',
                            'default'   => 4
                        ),
                        'orderby'       => array(
                            'type'      => 'string',
                            'default'   => 'date'
                        ),
                        'order'         => array(
                            'type'      => 'string',
                            'default'   => 'DESC'
                        ),
                        'featured'    => array(
                            'type'      => 'boolean',
                            'default'   => false
                        ),
                        'top_rated'    => array(
                            'type'      => 'boolean',
                            'default'   => false
                        ),
                    ),
                    'editor_script'   => 'masvideos-movies', 
                    'render_callback' => array( 'MasVideos_Shortcodes', 'movies' ),
                ),
            );

            $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
            foreach ( $blocks as $block => $args ) {
                wp_register_script( $args['editor_script'], MasVideos()->plugin_url() . '/assets/js/blocks/' . $block . $suffix . '.js', array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' ) );
                register_block_type( 'masvideos/' . $block, $args );
            }
        }
    }
}
