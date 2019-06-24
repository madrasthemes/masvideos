<?php
/**
 * MasVideos Person Template
 *
 * Functions for the templating system.
 *
 * @package  MasVideos\Functions
 * @version  1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * When the_post is called, put person data into a global.
 *
 * @param mixed $post Post Object.
 * @return MasVideos_Person
 */
function masvideos_setup_person_data( $post ) {
    unset( $GLOBALS['person'] );

    if ( is_int( $post ) ) {
        $the_post = get_post( $post );
    } else {
        $the_post = $post;
    }

    if ( empty( $the_post->post_type ) || ! in_array( $the_post->post_type, array( 'person' ), true ) ) {
        return;
    }

    $GLOBALS['person'] = masvideos_get_person( $the_post );

    return $GLOBALS['person'];
}
add_action( 'the_post', 'masvideos_setup_person_data' );

/**
 * Sets up the masvideos_persons_loop global from the passed args or from the main query.
 *
 * @since 1.0.0
 * @param array $args Args to pass into the global.
 */
function masvideos_setup_persons_loop( $args = array() ) {
    $default_args = array(
        'loop'         => 0,
        'columns'      => masvideos_get_default_persons_per_row(),
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
    if ( $GLOBALS['wp_query']->get( 'masvideos_person_query' ) ) {
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
    if ( isset( $GLOBALS['masvideos_persons_loop'] ) ) {
        $default_args = array_merge( $default_args, $GLOBALS['masvideos_persons_loop'] );
    }

    $GLOBALS['masvideos_persons_loop'] = wp_parse_args( $args, $default_args );
}
add_action( 'masvideos_before_persons_loop', 'masvideos_setup_persons_loop' );

/**
 * Resets the masvideos_persons_loop global.
 *
 * @since 1.0.0
 */
function masvideos_reset_persons_loop() {
    unset( $GLOBALS['masvideos_persons_loop'] );
}
add_action( 'masvideos_after_persons_loop', 'masvideos_reset_persons_loop', 999 );

/**
 * Gets a property from the masvideos_persons_loop global.
 *
 * @since 1.0.0
 * @param string $prop Prop to get.
 * @param string $default Default if the prop does not exist.
 * @return mixed
 */
function masvideos_get_persons_loop_prop( $prop, $default = '' ) {
    masvideos_setup_persons_loop(); // Ensure shop loop is setup.

    return isset( $GLOBALS['masvideos_persons_loop'], $GLOBALS['masvideos_persons_loop'][ $prop ] ) ? $GLOBALS['masvideos_persons_loop'][ $prop ] : $default;
}

/**
 * Sets a property in the masvideos_persons_loop global.
 *
 * @since 1.0.0
 * @param string $prop Prop to set.
 * @param string $value Value to set.
 */
function masvideos_set_persons_loop_prop( $prop, $value = '' ) {
    if ( ! isset( $GLOBALS['masvideos_persons_loop'] ) ) {
        masvideos_setup_persons_loop();
    }
    $GLOBALS['masvideos_persons_loop'][ $prop ] = $value;
}

/**
 * Check if we will be showing persons.
 *
 * @return bool
 */
function masvideos_persons_will_display() {
    return 0 < masvideos_get_persons_loop_prop( 'total', 0 );
}

/**
 * Should the MasVideos loop be displayed?
 *
 * This will return true if we have posts (persons) or if we have subcats to display.
 *
 * @since 3.4.0
 * @return bool
 */
function masvideos_persons_loop() {
    return have_posts();
}

/**
 * Get the default columns setting - this is how many persons will be shown per row in loops.
 *
 * @since 1.0.0
 * @return int
 */
function masvideos_get_default_persons_per_row() {
    $columns      = get_option( 'masvideos_person_columns', 4 );
    $person_grid   = masvideos_get_theme_support( 'person_grid' );
    $min_columns  = isset( $person_grid['min_columns'] ) ? absint( $person_grid['min_columns'] ) : 0;
    $max_columns  = isset( $person_grid['max_columns'] ) ? absint( $person_grid['max_columns'] ) : 0;

    if ( $min_columns && $columns < $min_columns ) {
        $columns = $min_columns;
        update_option( 'masvideos_person_columns', $columns );
    } elseif ( $max_columns && $columns > $max_columns ) {
        $columns = $max_columns;
        update_option( 'masvideos_person_columns', $columns );
    }

    $columns = absint( $columns );

    return apply_filters( 'masvideos_person_columns', max( 1, $columns ) );
}

/**
 * Get the default rows setting - this is how many person rows will be shown in loops.
 *
 * @since 1.0.0
 * @return int
 */
function masvideos_get_default_person_rows_per_page() {
    $rows         = absint( get_option( 'masvideos_person_rows', 4 ) );
    $person_grid   = masvideos_get_theme_support( 'person_grid' );
    $min_rows     = isset( $person_grid['min_rows'] ) ? absint( $person_grid['min_rows'] ) : 0;
    $max_rows     = isset( $person_grid['max_rows'] ) ? absint( $person_grid['max_rows'] ) : 0;

    if ( $min_rows && $rows < $min_rows ) {
        $rows = $min_rows;
        update_option( 'masvideos_person_rows', $rows );
    } elseif ( $max_rows && $rows > $max_rows ) {
        $rows = $max_rows;
        update_option( 'masvideos_person_rows', $rows );
    }

    return apply_filters( 'masvideos_person_rows', $rows );
}

/**
 * Display the classes for the person div.
 *
 * @since 1.0.0
 * @param string|array           $class      One or more classes to add to the class list.
 * @param int|WP_Post|MasVideos_Persons_Query $person_id Person ID or person object.
 */
function masvideos_person_class( $class = '', $person_id = null ) {
    // echo 'class="' . esc_attr( join( ' ', wc_get_person_class( $class, $person_id ) ) ) . '"';
    post_class();
}

/**
 * Search Form
 */
if ( ! function_exists( 'masvideos_get_person_search_form' ) ) {

    /**
     * Display person search form.
     *
     * Will first attempt to locate the person-searchform.php file in either the child or.
     * the parent, then load it. If it doesn't exist, then the default search form.
     * will be displayed.
     *
     * The default searchform uses html5.
     *
     * @param bool $echo (default: true).
     * @return string
     */
    function masvideos_get_person_search_form( $echo = true ) {
        global $person_search_form_index;

        ob_start();

        if ( empty( $person_search_form_index ) ) {
            $person_search_form_index = 0;
        }

        do_action( 'pre_masvideos_get_person_search_form' );

        masvideos_get_template( 'search-form.php', array(
            'index' => $person_search_form_index++,
            'post_type' => 'person',
        ) );

        $form = apply_filters( 'masvideos_get_person_search_form', ob_get_clean() );

        if ( ! $echo ) {
            return $form;
        }

        echo $form; // WPCS: XSS ok.
    }
}
