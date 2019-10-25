<?php
/*-----------------------------------------------------------------------------------*/
/* MasVideos Movies Widget Class
/*-----------------------------------------------------------------------------------*/
class MasVideos_Movies_Widget extends MasVideos_Widget {

    public $defaults;

    public function __construct() {

        $this->widget_cssclass    = 'masvideos masvideos_movies_widget masvideos-movies-widget';
        $this->widget_description = __( 'Your site&#8217;s movies.', 'masvideos' );
        $this->widget_id          = 'masvideos_movies_widget';
        $this->widget_name        = __( 'MAS Videos Movies', 'masvideos' );
        $this->settings           = apply_filters( 'masvideos_movies_widget_settings', array(
            'title'         => array(
                'type'  => 'text',
                'std'   => __( 'Top 5 List', 'masvideos' ),
                'label' => __( 'Title', 'masvideos' ),
            ),
            'limit'         => array(
                'type'  => 'number',
                'std'   => 5,
                'min'   => 1,
                'max'   => 20,
                'step'  => 1,
                'label' => __( 'Limit', 'masvideos' ),
            ),
            'orderby'       => array(
                'type'    => 'select',
                'std'     => 'date',
                'label'   => __( 'Order by', 'masvideos' ),
                'options' => array(
                    'title' => __( 'Title', 'masvideos' ),
                    'date'  => __( 'Date', 'masvideos' ),
                    'id'    => __( 'ID', 'masvideos' ),
                    'rand'  => __( 'Random', 'masvideos' ),
                ),
            ),
            'order'         => array(
                'type'    => 'select',
                'std'     => 'DESC',
                'label'   => __( 'Order', 'masvideos' ),
                'options' => array(
                    'ASC'   => __( 'ASC', 'masvideos' ),
                    'DESC'  => __( 'DESC', 'masvideos' ),
                ),
            ),
            'ids'           => array(
                'type'  => 'text',
                'std'   => '',
                'label' => __( 'Id&#8217;s ( separated by comma&#8217;s )', 'masvideos' ),
            ),
            'genre'         => array(
                'type'  => 'text',
                'std'   => '',
                'label' => __( 'Genere&#8217;s ( separated by comma&#8217;s )', 'masvideos' ),
            ),
            'featured'      => array(
                'type'  => 'checkbox',
                'std'   => 0,
                'label' => __( 'Show Featured Movies Only', 'masvideos' ),
            ),
            'top_rated'     => array(
                'type'  => 'checkbox',
                'std'   => 0,
                'label' => __( 'Show Top Rated Movies Only', 'masvideos' ),
            ),
        ) );

        parent::__construct();
    }

    public function widget( $args, $instance ) {

        $title = isset( $instance['title'] ) ? $instance['title'] : $this->settings['title']['std'];
        $limit = isset( $instance['limit'] ) ? $instance['limit'] : $this->settings['limit']['std'];
        $orderby = isset( $instance['orderby'] ) ? $instance['orderby'] : $this->settings['orderby']['std'];
        $order = isset( $instance['order'] ) ? $instance['order'] : $this->settings['order']['std'];
        $ids = isset( $instance['ids'] ) ? $instance['ids'] : $this->settings['ids']['std'];
        $genre = isset( $instance['genre'] ) ? $instance['genre'] : $this->settings['genre']['std'];
        $featured = isset( $instance['featured'] ) ? $instance['featured'] : $this->settings['featured']['std'];
        $top_rated = isset( $instance['top_rated'] ) ? $instance['top_rated'] : $this->settings['top_rated']['std'];

        $atts = array(
            'columns'   => 1,
            'limit'     => $limit,
            'orderby'   => $orderby,
            'order'     => $order,
            'ids'       => $ids,
            'genre'     => $genre,
            'featured'  => $featured,
            'top_rated' => $top_rated,
        );

        $this->widget_start( $args, $instance, $atts );

        $atts['template'] = 'content-movie-widget';

        $atts = apply_filters('masvideos_movie_widget_atts', $atts, $instance );

        echo MasVideos_Shortcodes::movies( $atts );

        $this->widget_end( $args, $instance, $atts );
    }
}