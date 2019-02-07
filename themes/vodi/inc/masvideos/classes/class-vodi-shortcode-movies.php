<?php
/**
 * Vodi Movies shortcode
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Products shortcode class.
 */
class Vodi_Shortcode_Movies extends MasVideos_Shortcode_Movies {

    private $original_post;

    /**
     * Get shortcode content.
     *
     * @since  1.0.0
     * @return string
     */
    public function get_movies() {
        return $this->get_query_results();
    }

    /**
     * Get shortcode content.
     *
     * @since  1.0.0
     * @return string
     */
    public function movie_loop_start() {
        $columns  = absint( $this->attributes['columns'] );
        $classes  = $this->get_wrapper_classes( $columns );
        $movies = $this->get_query_results();

        // Prime meta cache to reduce future queries.
        update_meta_cache( 'post', $movies->ids );
        update_object_term_cache( $movies->ids, 'movie' );

        // Setup the loop.
        masvideos_setup_movies_loop(
            array(
                'columns'      => $columns,
                'name'         => $this->type,
                'is_shortcode' => true,
                'is_search'    => false,
                'is_paginated' => masvideos_string_to_bool( $this->attributes['paginate'] ),
                'total'        => $movies->total,
                'total_pages'  => $movies->total_pages,
                'per_page'     => $movies->per_page,
                'current_page' => $movies->current_page,
            )
        );

        $this->original_post = $GLOBALS['post'];

        echo '<div class="' . esc_attr( implode( ' ', $classes ) ) . '">';

        do_action( "masvideos_shortcode_before_{$this->type}_loop", $this->attributes );

        // Fire standard shop loop hooks when paginating results so we can show result counts and so on.
        if ( masvideos_string_to_bool( $this->attributes['paginate'] ) ) {
            do_action( 'masvideos_before_shop_loop' );
        }

        masvideos_movie_loop_start();
    }

    /**
     * Get shortcode content.
     *
     * @since  1.0.0
     * @return string
     */
    public function movie_loop_end() {
        masvideos_movie_loop_end();

        $GLOBALS['post'] = $this->original_post; // WPCS: override ok.

        // Fire standard shop loop hooks when paginating results so we can show result counts and so on.
        if ( masvideos_string_to_bool( $this->attributes['paginate'] ) ) {
            do_action( 'masvideos_after_shop_loop' );
        }

        do_action( "masvideos_shortcode_after_{$this->type}_loop", $this->attributes );

        echo '</div>';

        wp_reset_postdata();
        masvideos_reset_movies_loop();
    }

}
