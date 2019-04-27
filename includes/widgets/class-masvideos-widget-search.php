<?php
/**
 * Search Widget.
 *
 * @package MasVideos/Widgets
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Widget search class.
 */
class MasVideos_Widget_Search extends MasVideos_Widget {

    /**
     * Constructor.
     */
    public function __construct() {
        $this->widget_cssclass    = 'masvideos masvideos_widget_search';
        $this->widget_description = __( 'A search form for your site.', 'masvideos' );
        $this->widget_id          = 'masvideos_search';
        $this->widget_name        = __( 'MAS Videos Search', 'masvideos' );
        $this->settings           = array(
            'title'     => array(
                'type'      => 'text',
                'std'       => '',
                'label'     => esc_html__( 'Title', 'masvideos' ),
            ),
            'post_type' => array(
                'type'      => 'select',
                'std'       => 'movie',
                'label'     => esc_html__( 'Posttype', 'masvideos' ),
                'options'   => array(
                    'episode'   => esc_html__( 'Episode', 'masvideos' ),
                    'tv_show'   => esc_html__( 'TV Show', 'masvideos' ),
                    'video'     => esc_html__( 'Video', 'masvideos' ),
                    'movie'     => esc_html__( 'Movie', 'masvideos' ),
                ),
            ),
        );

        parent::__construct();
    }

    /**
     * Output widget.
     *
     * @see WP_Widget
     *
     * @param array $args     Arguments.
     * @param array $instance Widget instance.
     */
    public function widget( $args, $instance ) {
        $this->widget_start( $args, $instance );

        $function_name = 'masvideos_get_' . $instance['post_type'] . '_search_form';
        if( function_exists( $function_name ) ) {
            call_user_func( $function_name );
        }

        $this->widget_end( $args );
    }
}
