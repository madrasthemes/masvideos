<?php
/**
 * MasVideos Video Playlist Template
 *
 * Functions for the templating system.
 *
 * @package  MasVideos\Functions
 * @version  1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * When the_post is called, put video playlist data into a global.
 *
 * @param mixed $post Post Object.
 * @return MasVideos_Video_Playlist
 */
function masvideos_setup_video_playlist_data( $post ) {
    unset( $GLOBALS['video_playlist'] );

    if ( is_int( $post ) ) {
        $the_post = get_post( $post );
    } else {
        $the_post = $post;
    }

    if ( empty( $the_post->post_type ) || ! in_array( $the_post->post_type, array( 'video_playlist' ), true ) ) {
        return;
    }

    $GLOBALS['video_playlist'] = masvideos_get_video_playlist( $the_post );

    return $GLOBALS['video_playlist'];
}
add_action( 'the_post', 'masvideos_setup_video_playlist_data' );

/**
 * Sets up the masvideos_video_playlists_loop global from the passed args or from the main query.
 *
 * @since 1.0.0
 * @param array $args Args to pass into the global.
 */
function masvideos_setup_video_playlists_loop( $args = array() ) {
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
    if ( $GLOBALS['wp_query']->get( 'masvideos_video_playlist_query' ) ) {
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
    if ( isset( $GLOBALS['masvideos_video_playlists_loop'] ) ) {
        $default_args = array_merge( $default_args, $GLOBALS['masvideos_video_playlists_loop'] );
    }

    $GLOBALS['masvideos_video_playlists_loop'] = wp_parse_args( $args, $default_args );
}
add_action( 'masvideos_before_video_playlists_loop', 'masvideos_setup_video_playlists_loop' );

/**
 * Resets the masvideos_video_playlists_loop global.
 *
 * @since 1.0.0
 */
function masvideos_reset_video_playlists_loop() {
    unset( $GLOBALS['masvideos_video_playlists_loop'] );
}
add_action( 'masvideos_after_video_playlists_loop', 'masvideos_reset_video_playlists_loop', 999 );

/**
 * Gets a property from the masvideos_video_playlists_loop global.
 *
 * @since 1.0.0
 * @param string $prop Prop to get.
 * @param string $default Default if the prop does not exist.
 * @return mixed
 */
function masvideos_get_video_playlists_loop_prop( $prop, $default = '' ) {
    masvideos_setup_video_playlists_loop(); // Ensure shop loop is setup.

    return isset( $GLOBALS['masvideos_video_playlists_loop'], $GLOBALS['masvideos_video_playlists_loop'][ $prop ] ) ? $GLOBALS['masvideos_video_playlists_loop'][ $prop ] : $default;
}

/**
 * Sets a property in the masvideos_video_playlists_loop global.
 *
 * @since 1.0.0
 * @param string $prop Prop to set.
 * @param string $value Value to set.
 */
function masvideos_set_video_playlists_loop_prop( $prop, $value = '' ) {
    if ( ! isset( $GLOBALS['masvideos_video_playlists_loop'] ) ) {
        masvideos_setup_video_playlists_loop();
    }
    $GLOBALS['masvideos_video_playlists_loop'][ $prop ] = $value;
}

/**
 * Display the classes for the video playlist div.
 *
 * @since 1.0.0
 * @param string|array           $class      One or more classes to add to the class list.
 * @param int|WP_Post|MasVideos_Video_Playlists_Query $video_playlist_id Video Playlist ID or video playlist object.
 */
function masvideos_video_playlist_class( $class = '', $video_playlist_id = null ) {
    // echo 'class="' . esc_attr( join( ' ', wc_get_video_class( $class, $video_playlist_id ) ) ) . '"';
    $class .= "video-playlist";
    post_class( $class );
}

/**
 * Loop
 */

if ( ! function_exists( 'masvideos_video_playlist_loop_start' ) ) {

    /**
     * Output the start of a video playlist loop. By default this is a UL.
     *
     * @param bool $echo Should echo?.
     * @return string
     */
    function masvideos_video_playlist_loop_start( $echo = true ) {
        ob_start();

        masvideos_set_video_playlists_loop_prop( 'loop', 0 );

        ?><div class="video-playlists columns-<?php echo esc_attr( masvideos_get_video_playlists_loop_prop( 'columns' ) ); ?>"><div class="video-playlists__inner"><?php

        $loop_start = apply_filters( 'masvideos_video_playlist_loop_start', ob_get_clean() );

        if ( $echo ) {
            echo $loop_start; // WPCS: XSS ok.
        } else {
            return $loop_start;
        }
    }
}

if ( ! function_exists( 'masvideos_video_playlist_loop_end' ) ) {

    /**
     * Output the end of a video playlist loop. By default this is a UL.
     *
     * @param bool $echo Should echo?.
     * @return string
     */
    function masvideos_video_playlist_loop_end( $echo = true ) {
        ob_start();

        ?></div></div><?php

        $loop_end = apply_filters( 'masvideos_video_playlist_loop_end', ob_get_clean() );

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

if ( ! function_exists( 'masvideos_template_single_video_playlist_title' ) ) {

    /**
     * Output the video playlist title.
     */
    function masvideos_template_single_video_playlist_title() {
        the_title( '<h1 class="video-playlist-title entry-title">', '</h1>' );
    }
}

if ( ! function_exists( 'masvideos_template_single_video_playlist_videos' ) ) {

    /**
     * Output the video playlist all videos.
     */
    function masvideos_template_single_video_playlist_videos( $video_playlist_id = false, $args = array() ) {
        global $video_playlist;

        $video_playlist_id = $video_playlist_id ? $video_playlist_id : $video_playlist->get_id();

        if ( ! $video_playlist_id ) {
            return;
        }

        $video_ids = masvideos_single_video_playlist_videos( $video_playlist_id );

        if( ! empty( $video_ids ) ) {
            $defaults = apply_filters( 'masvideos_template_single_video_playlist_videos_default_args', array(
                'limit'          => -1,
                'columns'        => 5,
                'orderby'        => 'post__in',
                'ids'            => implode( ",", array_reverse( $video_ids ) )
            ) );

            $args = wp_parse_args( $args, $defaults );

            add_filter( 'masvideos_loop_video_link', 'masvideos_loop_video_link_for_video_playlist', 99, 2 );
            echo MasVideos_Shortcodes::videos( $args );
            remove_filter( 'masvideos_loop_video_link', 'masvideos_loop_video_link_for_video_playlist', 99, 2 );
        }
    }
}

if ( ! function_exists( 'masvideos_template_button_toggle_user_video_playlist' ) ) {
    /**
     * Button for Add/Remove video to playlist.
     *
     * @since  1.0.0
     */
    function masvideos_template_button_toggle_user_video_playlist( $video_id ) {
        $video_playlists = masvideos_get_current_user_video_playlists();
        if( ! empty( $video_playlists ) ) {
            foreach ( $video_playlists as $key => $video_playlist ) {
                $playlist_id = $video_playlist->ID;
                $is_video_added = masvideos_is_video_added_to_playlist( $playlist_id, $video_id );
                ?><a class="toggle-playlist masvideos-ajax-toggle-video-playlist<?php echo $is_video_added ? ' added' : ''; ?>" href="<?php echo get_permalink( $playlist_id ); ?>" data-playlist_id=<?php echo esc_attr( $playlist_id ); ?> data-video_id=<?php echo esc_attr( $video_id ); ?>><?php echo get_the_title( $playlist_id ); ?></a><?php
            }
        }
    }
}

if ( ! function_exists( 'masvideos_template_loop_video_playlist_link_open' ) ) {
    /**
     * Insert the opening anchor tag for video playlists in the loop.
     */
    function masvideos_template_loop_video_playlist_link_open() {
        global $video;
        $link = apply_filters( 'masvideos_loop_video_link', get_the_permalink(), $video );

        ?><a href="<?php echo esc_url( $link ); ?>" class="masvideos-LoopMoviePlaylist-link masvideos-loop-video-playlist__link video-playlist__link"><?php
    }
}

if ( ! function_exists( 'masvideos_template_loop_video_playlist_title' ) ) {

    /**
     * Show the video playlist title in the video playlists loop. By default this is an H3.
     */
    function masvideos_template_loop_video_playlist_title() {
        the_title( '<h3 class="masvideos-loop-video-playlist__title  video-playlist__title">', '</h3>' );
    }
}

if ( ! function_exists( 'masvideos_template_loop_video_playlist_poster_open' ) ) {
    /**
     * videos playlist poster open in the loop.
     */
    function masvideos_template_loop_video_playlist_poster_open() {
        ?><div class="video-playlist__poster"><?php
    }
}

if ( ! function_exists( 'masvideos_template_loop_video_playlist_poster' ) ) {
    /**
     * videos playlist poster in the loop.
     */
    function masvideos_template_loop_video_playlist_poster() {
        $video_ids = masvideos_single_video_playlist_videos( get_the_ID() );

        if( $video_ids ) {
            $recently_added_video = masvideos_get_video( end( $video_ids ) );
            $image_size = apply_filters( 'masvideos_video_playlist_thumbnail_size', 'masvideos_video_medium' );
            echo is_object( $recently_added_video ) ? $recently_added_video->get_image( $image_size , array( 'class' => 'video-playlist__poster--image' ) ) : '';
        }
    }
}

if ( ! function_exists( 'masvideos_template_loop_video_playlist_poster_close' ) ) {
    /**
     * videos playlist poster close in the loop.
     */
    function masvideos_template_loop_video_playlist_poster_close() {
        ?></div><?php
    }
}

if ( ! function_exists( 'masvideos_template_loop_video_playlist_info_open' ) ) {
    /**
     * videos playlist info open in the loop.
     */
    function masvideos_template_loop_video_playlist_info_open() {
        ?><div class="video-playlist__info"><?php
    }
}

if ( ! function_exists( 'masvideos_template_loop_video_playlist_author' ) ) {
    /**
     * videos playlist author info in the loop.
     */
    function masvideos_template_loop_video_playlist_author() {
        global $video_playlist;
        ?>
        <div class="video-playlist__author-info">
            <div class="video-playlist__author--image">
                <?php echo get_avatar( $video_playlist, apply_filters( 'masvideos_video_review_gravatar_size', '60' ), '' ); ?>
            </div>
            <h6 class="video-playlist__author--name"><?php echo sprintf( '%s %s', esc_html__( 'By: ', 'masvideos' ), get_the_author() ); ?></h6>
        </div>
        <?php
    }
}

if ( ! function_exists( 'masvideos_template_loop_video_playlist_meta' ) ) {
    /**
     * videos playlist meta in the loop.
     */
    function masvideos_template_loop_video_playlist_meta() {
        ?><div class="video-playlist__meta"><?php
            do_action( 'masvideos_template_loop_video_playlist_meta' );
        ?></div><?php
    }
}

if ( ! function_exists( 'masvideos_template_loop_video_playlist_info_close' ) ) {
    /**
     * videos playlist poster close in the loop.
     */
    function masvideos_template_loop_video_playlist_info_close() {
        ?></div><?php
    }
}

if ( ! function_exists( 'masvideos_template_loop_video_playlist_link_close' ) ) {
    /**
     * Insert the opening anchor tag for video playlists in the loop.
     */
    function masvideos_template_loop_video_playlist_link_close() {
        ?></a><?php
    }
}

if ( ! function_exists( 'masvideos_template_loop_video_playlist_videos_count' ) ) {
    /**
     * videos playlist meta videos conut close in the loop..
     */
    function masvideos_template_loop_video_playlist_videos_count() {
        ?><span class="video-playlist__meta--videos-count"><?php
            $videos_count = count( masvideos_single_video_playlist_videos( get_the_ID() ) );

            if( $videos_count > 0 ) {
                printf( _n( '%s Video', '%s Videos', $videos_count, 'masvideos' ), number_format_i18n( $videos_count ) );
            }
        ?></span><?php
    }
}
