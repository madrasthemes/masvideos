<?php
/**
 * Year Filter Widget and related functions.
 *
 * @package MasVideos/Widgets
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Widget year filter class.
 */
class MasVideos_Widget_Movies_Year_Filter extends MasVideos_Widget {

    /**
     * Constructor.
     */
    public function __construct() {
        $this->widget_cssclass    = 'masvideos masvideos-widget_movies_year_filter';
        $this->widget_description = __( 'Display a list of years to filter movies.', 'masvideos' );
        $this->widget_id          = 'masvideos_movies_year_filter';
        $this->widget_name        = __( 'Filter Movies by Year', 'masvideos' );
        $this->settings           = array(
            'title'         => array(
                'type'          => 'text',
                'std'           => __( 'MAS Videos Filter Movies by Year', 'masvideos' ),
                'label'         => __( 'Title', 'masvideos' ),
            ),
            'start_year'    => array(
                'type'          => 'number',
                'std'           => 2000,
                'min'           => 1900,
                'max'           => ( date("Y") - 1 ),
                'step'          => 1,
                'label'         => __( 'Start Year', 'masvideos' ),
            ),
        );
        parent::__construct();
    }

    /**
     * Widget function.
     *
     * @see WP_Widget
     * @param array $args     Arguments.
     * @param array $instance Widget instance.
     */
    public function widget( $args, $instance ) {
        if ( ! is_movies() && ! is_movie_taxonomy() ) {
            return;
        }

        if ( ! MasVideos()->movie_query->get_main_query()->post_count ) {
            return;
        }

        $start_year = absint( isset( $instance['start_year'] ) ? $instance['start_year'] : $this->settings['start_year']['std'] );
        $end_year   = date("Y");

        ob_start();

        $found       = false;
        $year_filter = isset( $_GET['year_filter'] ) ? array_filter( array_map( 'absint', explode( ',', wp_unslash( $_GET['year_filter'] ) ) ) ) : array(); // WPCS: input var ok, CSRF ok, sanitization ok.
        $year_filter = isset( $year_filter[0] ) ? $year_filter[0] : 0;

        $this->widget_start( $args, $instance );

        echo '<ul>';

        for ( $i = $end_year; $i >= $start_year; $i-- ) {

            $found = true;
            $link  = $this->get_current_page_url();

            if ( $i == $year_filter ) {
                $link_filter = false;
            } else {
                $link_filter = $i;
            }

            $class       = ( $i == $year_filter ) ? 'masvideos-layered-nav-movies-year chosen' : 'masvideos-layered-nav-movies-year';
            $link        = apply_filters( 'masvideos_movies_year_filter_link', $link_filter ? add_query_arg( 'year_filter', $link_filter ) : remove_query_arg( 'year_filter' ) );
            $year_html   = $i;
            $count_html  = '';

            printf( '<li class="%s"><a href="%s"><span>%s</span> %s</a></li>', esc_attr( $class ), esc_url( $link ), $year_html, $count_html ); // WPCS: XSS ok.
        }

        echo '</ul>';

        $this->widget_end( $args );

        if ( ! $found ) {
            ob_end_clean();
        } else {
            echo ob_get_clean(); // WPCS: XSS ok.
        }
    }
}
