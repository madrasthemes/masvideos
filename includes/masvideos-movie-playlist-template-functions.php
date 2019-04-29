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
    $class .= "movie-playlist";
    post_class( $class );
}

/**
 * Loop
 */

if ( ! function_exists( 'masvideos_movie_playlist_loop_start' ) ) {

    /**
     * Output the start of a movie playlist loop. By default this is a UL.
     *
     * @param bool $echo Should echo?.
     * @return string
     */
    function masvideos_movie_playlist_loop_start( $echo = true ) {
        ob_start();

        masvideos_set_movie_playlists_loop_prop( 'loop', 0 );

        ?><div class="movie-playlists columns-<?php echo esc_attr( masvideos_get_movie_playlists_loop_prop( 'columns' ) ); ?>"><div class="movie-playlists__inner"><?php

        $loop_start = apply_filters( 'masvideos_movie_playlist_loop_start', ob_get_clean() );

        if ( $echo ) {
            echo $loop_start; // WPCS: XSS ok.
        } else {
            return $loop_start;
        }
    }
}

if ( ! function_exists( 'masvideos_movie_playlist_loop_end' ) ) {

    /**
     * Output the end of a movie playlist loop. By default this is a UL.
     *
     * @param bool $echo Should echo?.
     * @return string
     */
    function masvideos_movie_playlist_loop_end( $echo = true ) {
        ob_start();

        ?></div></div><?php

        $loop_end = apply_filters( 'masvideos_movie_playlist_loop_end', ob_get_clean() );

        if ( $echo ) {
            echo $loop_end; // WPCS: XSS ok.
        } else {
            return $loop_end;
        }
    }
}

/**
 * Single
 */

if ( ! function_exists( 'masvideos_template_single_movie_playlist_title' ) ) {

    /**
     * Output the movie playlist title.
     */
    function masvideos_template_single_movie_playlist_title() {
        the_title( '<h1 class="movie-playlist-title entry-title">', '</h1>' );
    }
}

if ( ! function_exists( 'masvideos_template_single_movie_playlist_movies' ) ) {

    /**
     * Output the movie playlist all movies.
     */
    function masvideos_template_single_movie_playlist_movies( $movie_playlist_id = false, $args = array() ) {
        global $movie_playlist;

        $movie_playlist_id = $movie_playlist_id ? $movie_playlist_id : $movie_playlist->get_id();

        if ( ! $movie_playlist_id ) {
            return;
        }

        $movie_ids = masvideos_single_movie_playlist_movies( $movie_playlist_id );

        if( ! empty( $movie_ids ) ) {
            $defaults = apply_filters( 'masvideos_template_single_movie_playlist_movies_default_args', array(
                'limit'          => -1,
                'columns'        => 5,
                'orderby'        => 'post__in',
                'ids'            => implode( ",", array_reverse( $movie_ids ) )
            ) );

            $args = wp_parse_args( $args, $defaults );

            add_filter( 'masvideos_loop_movie_link', 'masvideos_loop_movie_link_for_movie_playlist', 99, 2 );
            echo MasVideos_Shortcodes::movies( $args );
            remove_filter( 'masvideos_loop_movie_link', 'masvideos_loop_movie_link_for_movie_playlist', 99, 2 );
        }
    }
}

if ( ! function_exists( 'masvideos_template_button_toggle_user_movie_playlist' ) ) {
    /**
     * Button for Add/Remove movie to playlist.
     *
     * @since  1.0.0
     */
    function masvideos_template_button_toggle_user_movie_playlist( $movie_id ) {
        $movie_playlists = masvideos_get_current_user_movie_playlists();
        if( ! empty( $movie_playlists ) ) {
            foreach ( $movie_playlists as $key => $movie_playlist ) {
                $playlist_id = $movie_playlist->ID;
                $is_movie_added = masvideos_is_movie_added_to_playlist( $playlist_id, $movie_id );
                ?><a class="toggle-playlist masvideos-ajax-toggle-movie-playlist<?php echo $is_movie_added ? ' added' : ''; ?>" href="<?php echo get_permalink( $playlist_id ); ?>" data-playlist_id=<?php echo esc_attr( $playlist_id ); ?> data-movie_id=<?php echo esc_attr( $movie_id ); ?>><?php echo get_the_title( $playlist_id ); ?></a><?php
            }
        }
    }
}

if ( ! function_exists( 'masvideos_template_loop_movie_playlist_link_open' ) ) {
    /**
     * Insert the opening anchor tag for movie playlists in the loop.
     */
    function masvideos_template_loop_movie_playlist_link_open() {
        global $movie;
        $link = apply_filters( 'masvideos_loop_movie_link', get_the_permalink(), $movie );

        ?><a href="<?php echo esc_url( $link ); ?>" class="masvideos-LoopMoviePlaylist-link masvideos-loop-movie-playlist__link movie-playlist__link"><?php
    }
}

if ( ! function_exists( 'masvideos_template_loop_movie_playlist_title' ) ) {

    /**
     * Show the movie playlist title in the movie playlists loop. By default this is an H3.
     */
    function masvideos_template_loop_movie_playlist_title() {
        the_title( '<h3 class="masvideos-loop-movie-playlist__title  movie-playlist__title">', '</h3>' );
    }
}

if ( ! function_exists( 'masvideos_template_loop_movie_playlist_poster_open' ) ) {
    /**
     * movies playlist poster open in the loop.
     */
    function masvideos_template_loop_movie_playlist_poster_open() {
        ?><div class="movie-playlist__poster"><?php
    }
}

if ( ! function_exists( 'masvideos_template_loop_movie_playlist_poster' ) ) {
    /**
     * movies playlist poster in the loop.
     */
    function masvideos_template_loop_movie_playlist_poster() {
        $movie_ids = masvideos_single_movie_playlist_movies( get_the_ID() );

        if( $movie_ids ) {
            $recently_added_movie = masvideos_get_movie( end( $movie_ids ) );
            $image_size = apply_filters( 'masvideos_movie_playlist_thumbnail_size', 'masvideos_movie_medium' );
            echo is_object( $recently_added_movie ) ? $recently_added_movie->get_image( $image_size , array( 'class' => 'movie-playlist__poster--image' ) ) : '';
        }
    }
}

if ( ! function_exists( 'masvideos_template_loop_movie_playlist_poster_close' ) ) {
    /**
     * movies playlist poster close in the loop.
     */
    function masvideos_template_loop_movie_playlist_poster_close() {
        ?></div><?php
    }
}

if ( ! function_exists( 'masvideos_template_loop_movie_playlist_info_open' ) ) {
    /**
     * movies playlist info open in the loop.
     */
    function masvideos_template_loop_movie_playlist_info_open() {
        ?><div class="movie-playlist__info"><?php
    }
}

if ( ! function_exists( 'masvideos_template_loop_movie_playlist_author' ) ) {
    /**
     * movies playlist author info in the loop.
     */
    function masvideos_template_loop_movie_playlist_author() {
        ?>
        <div class="movie-playlist__author-info">
            <div class="movie-playlist__author--image">
                <?php echo get_avatar( get_the_author_meta( 'ID' ), apply_filters( 'masvideos_movie_review_gravatar_size', '60' ), '' ); ?>
            </div>
            <h6 class="movie-playlist__author--name"><?php echo sprintf( '%s %s', esc_html__( 'By: ', 'masvideos' ), get_the_author() ); ?></h6>
        </div>
        <?php
    }
}

if ( ! function_exists( 'masvideos_template_loop_movie_playlist_meta' ) ) {
    /**
     * movies playlist meta in the loop.
     */
    function masvideos_template_loop_movie_playlist_meta() {
        ?><div class="movie-playlist__meta"><?php
            do_action( 'masvideos_template_loop_movie_playlist_meta' );
        ?></div><?php
    }
}

if ( ! function_exists( 'masvideos_template_loop_movie_playlist_info_close' ) ) {
    /**
     * movies playlist poster close in the loop.
     */
    function masvideos_template_loop_movie_playlist_info_close() {
        ?></div><?php
    }
}

if ( ! function_exists( 'masvideos_template_loop_movie_playlist_link_close' ) ) {
    /**
     * Insert the opening anchor tag for movie playlists in the loop.
     */
    function masvideos_template_loop_movie_playlist_link_close() {
        ?></a><?php
    }
}

if ( ! function_exists( 'masvideos_template_loop_movie_playlist_movies_count' ) ) {
    /**
     * movies playlist meta movies conut close in the loop..
     */
    function masvideos_template_loop_movie_playlist_movies_count() {
        ?><span class="movie-playlist__meta--movies-count"><?php
            $movies_count = count( masvideos_single_movie_playlist_movies( get_the_ID() ) );

            if( $movies_count > 0 ) {
                printf( _n( '%s Movie', '%s Movies', $movies_count, 'masvideos' ), number_format_i18n( $movies_count ) );
            }
        ?></span><?php
    }
}
