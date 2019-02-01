<?php
/**
 * MasVideos TV Show Template
 *
 * Functions for the templating system.
 *
 * @package  MasVideos\Functions
 * @version  1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * When the_post is called, put tv show data into a global.
 *
 * @param mixed $post Post Object.
 * @return MasVideos_Movie
 */
function masvideos_setup_tv_show_data( $post ) {
    unset( $GLOBALS['tv_show'] );

    if ( is_int( $post ) ) {
        $the_post = get_post( $post );
    } else {
        $the_post = $post;
    }

    if ( empty( $the_post->post_type ) || ! in_array( $the_post->post_type, array( 'tv_show' ), true ) ) {
        return;
    }

    $GLOBALS['tv_show'] = masvideos_get_tv_show( $the_post );

    return $GLOBALS['tv_show'];
}
add_action( 'the_post', 'masvideos_setup_tv_show_data' );

/**
 * Sets up the masvideos_tv_shows_loop global from the passed args or from the main query.
 *
 * @since 1.0.0
 * @param array $args Args to pass into the global.
 */
function masvideos_setup_tv_shows_loop( $args = array() ) {
    $default_args = array(
        'loop'         => 0,
        'columns'      => 4,
        'name'         => '',
        'is_shortcode' => false,
        'is_paginated' => true,
        'is_search'    => false,
        // 'is_filtered'  => false,
        'total'        => 0,
        'total_pages'  => 0,
        'per_page'     => 0,
        'current_page' => 1,
    );

    // If this is a main WC query, use global args as defaults.
    if ( $GLOBALS['wp_query']->get( 'masvideos_tv_show_query' ) ) {
        $default_args = array_merge( $default_args, array(
            'is_search'    => $GLOBALS['wp_query']->is_search(),
            // 'is_filtered'  => is_filtered(),
            'total'        => $GLOBALS['wp_query']->found_posts,
            'total_pages'  => $GLOBALS['wp_query']->max_num_pages,
            'per_page'     => $GLOBALS['wp_query']->get( 'posts_per_page' ),
            'current_page' => max( 1, $GLOBALS['wp_query']->get( 'paged', 1 ) ),
        ) );
    }

    // Merge any existing values.
    if ( isset( $GLOBALS['masvideos_tv_shows_loop'] ) ) {
        $default_args = array_merge( $default_args, $GLOBALS['masvideos_tv_shows_loop'] );
    }

    $GLOBALS['masvideos_tv_shows_loop'] = wp_parse_args( $args, $default_args );
}
add_action( 'masvideos_before_tv_shows_loop', 'masvideos_setup_tv_shows_loop' );

/**
 * Resets the masvideos_tv_shows_loop global.
 *
 * @since 1.0.0
 */
function masvideos_reset_tv_shows_loop() {
    unset( $GLOBALS['masvideos_tv_shows_loop'] );
}
add_action( 'masvideos_after_tv_shows_loop', 'masvideos_reset_tv_shows_loop', 999 );

/**
 * Gets a property from the masvideos_tv_shows_loop global.
 *
 * @since 1.0.0
 * @param string $prop Prop to get.
 * @param string $default Default if the prop does not exist.
 * @return mixed
 */
function masvideos_get_tv_shows_loop_prop( $prop, $default = '' ) {
    masvideos_setup_tv_shows_loop(); // Ensure shop loop is setup.

    return isset( $GLOBALS['masvideos_tv_shows_loop'], $GLOBALS['masvideos_tv_shows_loop'][ $prop ] ) ? $GLOBALS['masvideos_tv_shows_loop'][ $prop ] : $default;
}

/**
 * Sets a property in the masvideos_tv_shows_loop global.
 *
 * @since 1.0.0
 * @param string $prop Prop to set.
 * @param string $value Value to set.
 */
function masvideos_set_tv_shows_loop_prop( $prop, $value = '' ) {
    if ( ! isset( $GLOBALS['masvideos_tv_shows_loop'] ) ) {
        masvideos_setup_tv_shows_loop();
    }
    $GLOBALS['masvideos_tv_shows_loop'][ $prop ] = $value;
}

/**
 * Check if we will be showing tv shows.
 *
 * @return bool
 */
function masvideos_tv_shows_will_display() {
    return 0 < masvideos_get_tv_shows_loop_prop( 'total', 0 );
}

/**
 * Should the MasVideos loop be displayed?
 *
 * This will return true if we have posts (tv shows) or if we have subcats to display.
 *
 * @since 3.4.0
 * @return bool
 */
function masvideos_tv_shows_loop() {
    return have_posts();
}

/**
 * Get the default columns setting - this is how many tv shows will be shown per row in loops.
 *
 * @since 1.0.0
 * @return int
 */
function masvideos_get_default_tv_shows_per_row() {
    $columns      = get_option( 'masvideos_tv_show_columns', 4 );
    $tv_show_grid   = masvideos_get_theme_support( 'tv_show_grid' );
    $min_columns  = isset( $tv_show_grid['min_columns'] ) ? absint( $tv_show_grid['min_columns'] ) : 0;
    $max_columns  = isset( $tv_show_grid['max_columns'] ) ? absint( $tv_show_grid['max_columns'] ) : 0;

    if ( $min_columns && $columns < $min_columns ) {
        $columns = $min_columns;
        update_option( 'masvideos_tv_show_columns', $columns );
    } elseif ( $max_columns && $columns > $max_columns ) {
        $columns = $max_columns;
        update_option( 'masvideos_tv_show_columns', $columns );
    }

    $columns = absint( $columns );

    return max( 1, $columns );
}

/**
 * Get the default rows setting - this is how many tv show rows will be shown in loops.
 *
 * @since 1.0.0
 * @return int
 */
function masvideos_get_default_tv_show_rows_per_page() {
    $rows         = absint( get_option( 'masvideos_tv_show_rows', 4 ) );
    $tv_show_grid   = masvideos_get_theme_support( 'tv_show_grid' );
    $min_rows     = isset( $tv_show_grid['min_rows'] ) ? absint( $tv_show_grid['min_rows'] ) : 0;
    $max_rows     = isset( $tv_show_grid['max_rows'] ) ? absint( $tv_show_grid['max_rows'] ) : 0;

    if ( $min_rows && $rows < $min_rows ) {
        $rows = $min_rows;
        update_option( 'masvideos_tv_show_rows', $rows );
    } elseif ( $max_rows && $rows > $max_rows ) {
        $rows = $max_rows;
        update_option( 'masvideos_tv_show_rows', $rows );
    }

    return $rows;
}

/**
 * Display the classes for the tv show div.
 *
 * @since 1.0.0
 * @param string|array           $class      One or more classes to add to the class list.
 * @param int|WP_Post|MasVideos_TV_Shows_Query $tv_show_id TV Show ID or tv show object.
 */
function masvideos_tv_show_class( $class = '', $tv_show_id = null ) {
    // echo 'class="' . esc_attr( join( ' ', wc_get_tv_show_class( $class, $tv_show_id ) ) ) . '"';
    post_class();
}

/**
 * Loop
 */

if ( ! function_exists( 'masvideos_tv_show_loop_start' ) ) {

    /**
     * Output the start of a tv show loop. By default this is a UL.
     *
     * @param bool $echo Should echo?.
     * @return string
     */
    function masvideos_tv_show_loop_start( $echo = true ) {
        ob_start();

        masvideos_set_tv_shows_loop_prop( 'loop', 0 );

        ?><div class="tv_shows columns-<?php echo esc_attr( masvideos_get_tv_shows_loop_prop( 'columns' ) ); ?>"><div class="tv_shows__inner"><?php

        $loop_start = apply_filters( 'masvideos_tv_show_loop_start', ob_get_clean() );

        if ( $echo ) {
            echo $loop_start; // WPCS: XSS ok.
        } else {
            return $loop_start;
        }
    }
}

if ( ! function_exists( 'masvideos_tv_shows_loop_content' ) ) {
    /*
     * Output the tv show loop. By default this is a UL.
     */
    function masvideos_tv_shows_loop_content() {
        masvideos_get_template_part( 'content', 'tv_show' );
    }
}

if ( ! function_exists( 'masvideos_tv_show_loop_end' ) ) {

    /**
     * Output the end of a tv show loop. By default this is a UL.
     *
     * @param bool $echo Should echo?.
     * @return string
     */
    function masvideos_tv_show_loop_end( $echo = true ) {
        ob_start();

        ?></div></div><?php

        $loop_end = apply_filters( 'masvideos_tv_show_loop_end', ob_get_clean() );

        if ( $echo ) {
            echo $loop_end; // WPCS: XSS ok.
        } else {
            return $loop_end;
        }
    }
}