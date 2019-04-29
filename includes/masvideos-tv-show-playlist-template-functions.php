<?php
/**
 * MasVideos TV Show Playlist Template
 *
 * Functions for the templating system.
 *
 * @package  MasVideos\Functions
 * @version  1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * When the_post is called, put tv show playlist data into a global.
 *
 * @param mixed $post Post Object.
 * @return MasVideos_TV_Show_Playlist
 */
function masvideos_setup_tv_show_playlist_data( $post ) {
    unset( $GLOBALS['tv_show_playlist'] );

    if ( is_int( $post ) ) {
        $the_post = get_post( $post );
    } else {
        $the_post = $post;
    }

    if ( empty( $the_post->post_type ) || ! in_array( $the_post->post_type, array( 'tv_show_playlist' ), true ) ) {
        return;
    }

    $GLOBALS['tv_show_playlist'] = masvideos_get_tv_show_playlist( $the_post );

    return $GLOBALS['tv_show_playlist'];
}
add_action( 'the_post', 'masvideos_setup_tv_show_playlist_data' );

/**
 * Sets up the masvideos_tv_show_playlists_loop global from the passed args or from the main query.
 *
 * @since 1.0.0
 * @param array $args Args to pass into the global.
 */
function masvideos_setup_tv_show_playlists_loop( $args = array() ) {
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
    if ( $GLOBALS['wp_query']->get( 'masvideos_tv_show_playlist_query' ) ) {
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
    if ( isset( $GLOBALS['masvideos_tv_show_playlists_loop'] ) ) {
        $default_args = array_merge( $default_args, $GLOBALS['masvideos_tv_show_playlists_loop'] );
    }

    $GLOBALS['masvideos_tv_show_playlists_loop'] = wp_parse_args( $args, $default_args );
}
add_action( 'masvideos_before_tv_show_playlists_loop', 'masvideos_setup_tv_show_playlists_loop' );

/**
 * Resets the masvideos_tv_show_playlists_loop global.
 *
 * @since 1.0.0
 */
function masvideos_reset_tv_show_playlists_loop() {
    unset( $GLOBALS['masvideos_tv_show_playlists_loop'] );
}
add_action( 'masvideos_after_tv_show_playlists_loop', 'masvideos_reset_tv_show_playlists_loop', 999 );

/**
 * Gets a property from the masvideos_tv_show_playlists_loop global.
 *
 * @since 1.0.0
 * @param string $prop Prop to get.
 * @param string $default Default if the prop does not exist.
 * @return mixed
 */
function masvideos_get_tv_show_playlists_loop_prop( $prop, $default = '' ) {
    masvideos_setup_tv_show_playlists_loop(); // Ensure shop loop is setup.

    return isset( $GLOBALS['masvideos_tv_show_playlists_loop'], $GLOBALS['masvideos_tv_show_playlists_loop'][ $prop ] ) ? $GLOBALS['masvideos_tv_show_playlists_loop'][ $prop ] : $default;
}

/**
 * Sets a property in the masvideos_tv_show_playlists_loop global.
 *
 * @since 1.0.0
 * @param string $prop Prop to set.
 * @param string $value Value to set.
 */
function masvideos_set_tv_show_playlists_loop_prop( $prop, $value = '' ) {
    if ( ! isset( $GLOBALS['masvideos_tv_show_playlists_loop'] ) ) {
        masvideos_setup_tv_show_playlists_loop();
    }
    $GLOBALS['masvideos_tv_show_playlists_loop'][ $prop ] = $value;
}

/**
 * Display the classes for the tv show playlist div.
 *
 * @since 1.0.0
 * @param string|array           $class      One or more classes to add to the class list.
 * @param int|WP_Post|MasVideos_TV_Show_Playlists_Query $tv_show_playlist_id TV Show Playlist ID or tv show playlist object.
 */
function masvideos_tv_show_playlist_class( $class = '', $tv_show_playlist_id = null ) {
    // echo 'class="' . esc_attr( join( ' ', wc_get_tv_show_class( $class, $tv_show_playlist_id ) ) ) . '"';
    $class .= "tv-show-playlist";
    post_class( $class );
}

/**
 * Loop
 */

if ( ! function_exists( 'masvideos_tv_show_playlist_loop_start' ) ) {

    /**
     * Output the start of a tv show playlist loop. By default this is a UL.
     *
     * @param bool $echo Should echo?.
     * @return string
     */
    function masvideos_tv_show_playlist_loop_start( $echo = true ) {
        ob_start();

        masvideos_set_tv_show_playlists_loop_prop( 'loop', 0 );

        ?><div class="tv-show-playlists columns-<?php echo esc_attr( masvideos_get_tv_show_playlists_loop_prop( 'columns' ) ); ?>"><div class="tv-show-playlists__inner"><?php

        $loop_start = apply_filters( 'masvideos_tv_show_playlist_loop_start', ob_get_clean() );

        if ( $echo ) {
            echo $loop_start; // WPCS: XSS ok.
        } else {
            return $loop_start;
        }
    }
}

if ( ! function_exists( 'masvideos_tv_show_playlist_loop_end' ) ) {

    /**
     * Output the end of a tv show playlist loop. By default this is a UL.
     *
     * @param bool $echo Should echo?.
     * @return string
     */
    function masvideos_tv_show_playlist_loop_end( $echo = true ) {
        ob_start();

        ?></div></div><?php

        $loop_end = apply_filters( 'masvideos_tv_show_playlist_loop_end', ob_get_clean() );

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

if ( ! function_exists( 'masvideos_template_single_tv_show_playlist_title' ) ) {

    /**
     * Output the tv show playlist title.
     */
    function masvideos_template_single_tv_show_playlist_title() {
        the_title( '<h1 class="tv_show-playlist-title entry-title">', '</h1>' );
    }
}

if ( ! function_exists( 'masvideos_template_single_tv_show_playlist_tv_shows' ) ) {

    /**
     * Output the tv show playlist all tv shows.
     */
    function masvideos_template_single_tv_show_playlist_tv_shows( $tv_show_playlist_id = false, $args = array() ) {
        global $tv_show_playlist;

        $tv_show_playlist_id = $tv_show_playlist_id ? $tv_show_playlist_id : $tv_show_playlist->get_id();

        if ( ! $tv_show_playlist_id ) {
            return;
        }

        $tv_show_ids = masvideos_single_tv_show_playlist_tv_shows( $tv_show_playlist_id );

        if( ! empty( $tv_show_ids ) ) {
            $defaults = apply_filters( 'masvideos_template_single_tv_show_playlist_tv_shows_default_args', array(
                'limit'          => -1,
                'columns'        => 5,
                'orderby'        => 'post__in',
                'ids'            => implode( ",", array_reverse( $tv_show_ids ) )
            ) );

            $args = wp_parse_args( $args, $defaults );

            add_filter( 'masvideos_loop_tv_show_link', 'masvideos_loop_tv_show_link_for_tv_show_playlist', 99, 2 );
            echo MasVideos_Shortcodes::tv_shows( $args );
            remove_filter( 'masvideos_loop_tv_show_link', 'masvideos_loop_tv_show_link_for_tv_show_playlist', 99, 2 );
        }
    }
}

if ( ! function_exists( 'masvideos_template_button_toggle_user_tv_show_playlist' ) ) {
    /**
     * Button for Add/Remove tv show to playlist.
     *
     * @since  1.0.0
     */
    function masvideos_template_button_toggle_user_tv_show_playlist( $tv_show_id ) {
        $tv_show_playlists = masvideos_get_current_user_tv_show_playlists();
        if( ! empty( $tv_show_playlists ) ) {
            foreach ( $tv_show_playlists as $key => $tv_show_playlist ) {
                $playlist_id = $tv_show_playlist->ID;
                $is_tv_show_added = masvideos_is_tv_show_added_to_playlist( $playlist_id, $tv_show_id );
                ?><a class="toggle-playlist masvideos-ajax-toggle-tv-show-playlist<?php echo $is_tv_show_added ? ' added' : ''; ?>" href="<?php echo get_permalink( $playlist_id ); ?>" data-playlist_id=<?php echo esc_attr( $playlist_id ); ?> data-tv_show_id=<?php echo esc_attr( $tv_show_id ); ?>><?php echo get_the_title( $playlist_id ); ?></a><?php
            }
        }
    }
}

if ( ! function_exists( 'masvideos_template_loop_tv_show_playlist_link_open' ) ) {
    /**
     * Insert the opening anchor tag for tv_show playlists in the loop.
     */
    function masvideos_template_loop_tv_show_playlist_link_open() {
        global $tv_show;
        $link = apply_filters( 'masvideos_loop_tv_show_link', get_the_permalink(), $tv_show );

        ?><a href="<?php echo esc_url( $link ); ?>" class="masvideos-LoopMoviePlaylist-link masvideos-loop-tv-show-playlist__link tv-show-playlist__link"><?php
    }
}

if ( ! function_exists( 'masvideos_template_loop_tv_show_playlist_title' ) ) {

    /**
     * Show the tv_show playlist title in the tv_show playlists loop. By default this is an H3.
     */
    function masvideos_template_loop_tv_show_playlist_title() {
        the_title( '<h3 class="masvideos-loop-tv-show-playlist__title  tv-show-playlist__title">', '</h3>' );
    }
}

if ( ! function_exists( 'masvideos_template_loop_tv_show_playlist_poster_open' ) ) {
    /**
     * tv_shows playlist poster open in the loop.
     */
    function masvideos_template_loop_tv_show_playlist_poster_open() {
        ?><div class="tv-show-playlist__poster"><?php
    }
}

if ( ! function_exists( 'masvideos_template_loop_tv_show_playlist_poster' ) ) {
    /**
     * tv_shows playlist poster in the loop.
     */
    function masvideos_template_loop_tv_show_playlist_poster() {
        $tv_show_ids = masvideos_single_tv_show_playlist_tv_shows( get_the_ID() );

        if( $tv_show_ids ) {
            $recently_added_tv_show = masvideos_get_tv_show( end( $tv_show_ids ) );
            $image_size = apply_filters( 'masvideos_tv_show_playlist_thumbnail_size', 'masvideos_tv_show_medium' );
            echo is_object( $recently_added_tv_show ) ? $recently_added_tv_show->get_image( $image_size , array( 'class' => 'tv-show-playlist__poster--image' ) ) : '';
        }
    }
}

if ( ! function_exists( 'masvideos_template_loop_tv_show_playlist_poster_close' ) ) {
    /**
     * tv_shows playlist poster close in the loop.
     */
    function masvideos_template_loop_tv_show_playlist_poster_close() {
        ?></div><?php
    }
}

if ( ! function_exists( 'masvideos_template_loop_tv_show_playlist_info_open' ) ) {
    /**
     * tv_shows playlist info open in the loop.
     */
    function masvideos_template_loop_tv_show_playlist_info_open() {
        ?><div class="tv-show-playlist__info"><?php
    }
}

if ( ! function_exists( 'masvideos_template_loop_tv_show_playlist_author' ) ) {
    /**
     * tv_shows playlist author info in the loop.
     */
    function masvideos_template_loop_tv_show_playlist_author() {
        global $tv_show_playlist;
        ?>
        <div class="tv-show-playlist__author-info">
            <div class="tv-show-playlist__author--image">
                <?php echo get_avatar( $tv_show_playlist, apply_filters( 'masvideos_tv_show_review_gravatar_size', '60' ), '' ); ?>
            </div>
            <h6 class="tv-show-playlist__author--name"><?php echo sprintf( '%s %s', esc_html__( 'By: ', 'masvideos' ), get_the_author() ); ?></h6>
        </div>
        <?php
    }
}

if ( ! function_exists( 'masvideos_template_loop_tv_show_playlist_meta' ) ) {
    /**
     * tv_shows playlist meta in the loop.
     */
    function masvideos_template_loop_tv_show_playlist_meta() {
        ?><div class="tv-show-playlist__meta"><?php
            do_action( 'masvideos_template_loop_tv_show_playlist_meta' );
        ?></div><?php
    }
}

if ( ! function_exists( 'masvideos_template_loop_tv_show_playlist_info_close' ) ) {
    /**
     * tv_shows playlist poster close in the loop.
     */
    function masvideos_template_loop_tv_show_playlist_info_close() {
        ?></div><?php
    }
}

if ( ! function_exists( 'masvideos_template_loop_tv_show_playlist_link_close' ) ) {
    /**
     * Insert the opening anchor tag for tv_show playlists in the loop.
     */
    function masvideos_template_loop_tv_show_playlist_link_close() {
        ?></a><?php
    }
}

if ( ! function_exists( 'masvideos_template_loop_tv_show_playlist_tv_shows_count' ) ) {
    /**
     * tv_shows playlist meta tv_shows conut close in the loop..
     */
    function masvideos_template_loop_tv_show_playlist_tv_shows_count() {
        ?><span class="tv-show-playlist__meta--tv_shows-count"><?php
            $tv_shows_count = count( masvideos_single_tv_show_playlist_tv_shows( get_the_ID() ) );

            if( $tv_shows_count > 0 ) {
                printf( _n( '%s TV Show', '%s TV Shows', $tv_shows_count, 'masvideos' ), number_format_i18n( $tv_shows_count ) );
            }
        ?></span><?php
    }
}
