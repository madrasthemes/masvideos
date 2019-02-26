<?php
/**
 * MasVideos Movie Playlist Template
 *
 * Functions for the templating system.
 *
 * @package  MasVideos\Functions
 * @version  1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * When the_post is called, put movie playlist data into a global.
 *
 * @param mixed $post Post Object.
 * @return MasVideos_Movie_Playlist
 */
function masvideos_setup_movie_playlist_data( $post ) {
    unset( $GLOBALS['movie_playlist'] );

    if ( is_int( $post ) ) {
        $the_post = get_post( $post );
    } else {
        $the_post = $post;
    }

    if ( empty( $the_post->post_type ) || ! in_array( $the_post->post_type, array( 'movie_playlist' ), true ) ) {
        return;
    }

    $GLOBALS['movie_playlist'] = masvideos_get_movie_playlist( $the_post );

    return $GLOBALS['movie_playlist'];
}
add_action( 'the_post', 'masvideos_setup_movie_playlist_data' );

/**
 * Sets up the masvideos_movie_playlists_loop global from the passed args or from the main query.
 *
 * @since 1.0.0
 * @param array $args Args to pass into the global.
 */
function masvideos_setup_movie_playlists_loop( $args = array() ) {
    $default_args = array(
        'loop'         => 0,
        'columns'      => 5,
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
    if ( $GLOBALS['wp_query']->get( 'masvideos_movie_playlist_query' ) ) {
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
    if ( isset( $GLOBALS['masvideos_movie_playlists_loop'] ) ) {
        $default_args = array_merge( $default_args, $GLOBALS['masvideos_movie_playlists_loop'] );
    }

    $GLOBALS['masvideos_movie_playlists_loop'] = wp_parse_args( $args, $default_args );
}
add_action( 'masvideos_before_movie_playlists_loop', 'masvideos_setup_movie_playlists_loop' );

/**
 * Resets the masvideos_movie_playlists_loop global.
 *
 * @since 1.0.0
 */
function masvideos_reset_movie_playlists_loop() {
    unset( $GLOBALS['masvideos_movie_playlists_loop'] );
}
add_action( 'masvideos_after_movie_playlists_loop', 'masvideos_reset_movie_playlists_loop', 999 );

/**
 * Gets a property from the masvideos_movie_playlists_loop global.
 *
 * @since 1.0.0
 * @param string $prop Prop to get.
 * @param string $default Default if the prop does not exist.
 * @return mixed
 */
function masvideos_get_movie_playlists_loop_prop( $prop, $default = '' ) {
    masvideos_setup_movie_playlists_loop(); // Ensure shop loop is setup.

    return isset( $GLOBALS['masvideos_movie_playlists_loop'], $GLOBALS['masvideos_movie_playlists_loop'][ $prop ] ) ? $GLOBALS['masvideos_movie_playlists_loop'][ $prop ] : $default;
}

/**
 * Sets a property in the masvideos_movie_playlists_loop global.
 *
 * @since 1.0.0
 * @param string $prop Prop to set.
 * @param string $value Value to set.
 */
function masvideos_set_movie_playlists_loop_prop( $prop, $value = '' ) {
    if ( ! isset( $GLOBALS['masvideos_movie_playlists_loop'] ) ) {
        masvideos_setup_movie_playlists_loop();
    }
    $GLOBALS['masvideos_movie_playlists_loop'][ $prop ] = $value;
}

/**
 * Display the classes for the movie playlist div.
 *
 * @since 1.0.0
 * @param string|array           $class      One or more classes to add to the class list.
 * @param int|WP_Post|MasVideos_Movie_Playlists_Query $movie_playlist_id Movie Playlist ID or movie playlist object.
 */
function masvideos_movie_playlist_class( $class = '', $movie_playlist_id = null ) {
    // echo 'class="' . esc_attr( join( ' ', wc_get_movie_class( $class, $movie_playlist_id ) ) ) . '"';
    post_class();
}