<?php
/**
 * MasVideos Episode Template
 *
 * Functions for the templating system.
 *
 * @package  MasVideos\Functions
 * @version  1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * When the_post is called, put episode data into a global.
 *
 * @param mixed $post Post Object.
 * @return MasVideos_Episode
 */
function masvideos_setup_episode_data( $post ) {
    unset( $GLOBALS['episode'] );

    if ( is_int( $post ) ) {
        $the_post = get_post( $post );
    } else {
        $the_post = $post;
    }

    if ( empty( $the_post->post_type ) || ! in_array( $the_post->post_type, array( 'episode' ), true ) ) {
        return;
    }

    $GLOBALS['episode'] = masvideos_get_episode( $the_post );

    return $GLOBALS['episode'];
}
add_action( 'the_post', 'masvideos_setup_episode_data' );

/**
 * Sets up the masvideos_episodes_loop global from the passed args or from the main query.
 *
 * @since 1.0.0
 * @param array $args Args to pass into the global.
 */
function masvideos_setup_episodes_loop( $args = array() ) {
    $default_args = array(
        'loop'         => 0,
        'columns'      => masvideos_get_default_episodes_per_row(),
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
    if ( $GLOBALS['wp_query']->get( 'masvideos_episode_query' ) ) {
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
    if ( isset( $GLOBALS['masvideos_episodes_loop'] ) ) {
        $default_args = array_merge( $default_args, $GLOBALS['masvideos_episodes_loop'] );
    }

    $GLOBALS['masvideos_episodes_loop'] = wp_parse_args( $args, $default_args );
}
add_action( 'masvideos_before_episodes_loop', 'masvideos_setup_episodes_loop' );

/**
 * Resets the masvideos_episodes_loop global.
 *
 * @since 1.0.0
 */
function masvideos_reset_episodes_loop() {
    unset( $GLOBALS['masvideos_episodes_loop'] );
}
add_action( 'masvideos_after_episodes_loop', 'masvideos_reset_episodes_loop', 999 );

/**
 * Gets a property from the masvideos_episodes_loop global.
 *
 * @since 1.0.0
 * @param string $prop Prop to get.
 * @param string $default Default if the prop does not exist.
 * @return mixed
 */
function masvideos_get_episodes_loop_prop( $prop, $default = '' ) {
    masvideos_setup_episodes_loop(); // Ensure shop loop is setup.

    return isset( $GLOBALS['masvideos_episodes_loop'], $GLOBALS['masvideos_episodes_loop'][ $prop ] ) ? $GLOBALS['masvideos_episodes_loop'][ $prop ] : $default;
}

/**
 * Sets a property in the masvideos_episodes_loop global.
 *
 * @since 1.0.0
 * @param string $prop Prop to set.
 * @param string $value Value to set.
 */
function masvideos_set_episodes_loop_prop( $prop, $value = '' ) {
    if ( ! isset( $GLOBALS['masvideos_episodes_loop'] ) ) {
        masvideos_setup_episodes_loop();
    }
    $GLOBALS['masvideos_episodes_loop'][ $prop ] = $value;
}

/**
 * Check if we will be showing episodes.
 *
 * @return bool
 */
function masvideos_episodes_will_display() {
    return 0 < masvideos_get_episodes_loop_prop( 'total', 0 );
}

/**
 * Should the MasVideos loop be displayed?
 *
 * This will return true if we have posts (episodes) or if we have subcats to display.
 *
 * @since 3.4.0
 * @return bool
 */
function masvideos_episodes_loop() {
    return have_posts();
}

/**
 * Get the default columns setting - this is how many episodes will be shown per row in loops.
 *
 * @since 1.0.0
 * @return int
 */
function masvideos_get_default_episodes_per_row() {
    $columns      = get_option( 'masvideos_episode_columns', 4 );
    $episode_grid   = masvideos_get_theme_support( 'episode_grid' );
    $min_columns  = isset( $episode_grid['min_columns'] ) ? absint( $episode_grid['min_columns'] ) : 0;
    $max_columns  = isset( $episode_grid['max_columns'] ) ? absint( $episode_grid['max_columns'] ) : 0;

    if ( $min_columns && $columns < $min_columns ) {
        $columns = $min_columns;
        update_option( 'masvideos_episode_columns', $columns );
    } elseif ( $max_columns && $columns > $max_columns ) {
        $columns = $max_columns;
        update_option( 'masvideos_episode_columns', $columns );
    }

    $columns = absint( $columns );

    return max( 1, $columns );
}

/**
 * Get the default rows setting - this is how many episode rows will be shown in loops.
 *
 * @since 1.0.0
 * @return int
 */
function masvideos_get_default_episode_rows_per_page() {
    $rows         = absint( get_option( 'masvideos_episode_rows', 4 ) );
    $episode_grid   = masvideos_get_theme_support( 'episode_grid' );
    $min_rows     = isset( $episode_grid['min_rows'] ) ? absint( $episode_grid['min_rows'] ) : 0;
    $max_rows     = isset( $episode_grid['max_rows'] ) ? absint( $episode_grid['max_rows'] ) : 0;

    if ( $min_rows && $rows < $min_rows ) {
        $rows = $min_rows;
        update_option( 'masvideos_episode_rows', $rows );
    } elseif ( $max_rows && $rows > $max_rows ) {
        $rows = $max_rows;
        update_option( 'masvideos_episode_rows', $rows );
    }

    return $rows;
}

/**
 * Display the classes for the episode div.
 *
 * @since 1.0.0
 * @param string|array           $class      One or more classes to add to the class list.
 * @param int|WP_Post|MasVideos_Episodes_Query $episode_id Episode ID or episode object.
 */
function masvideos_episode_class( $class = '', $episode_id = null ) {
    // echo 'class="' . esc_attr( join( ' ', wc_get_episode_class( $class, $episode_id ) ) ) . '"';
    post_class();
}

/**
 * Loop
 */

if ( ! function_exists( 'masvideos_episode_loop_start' ) ) {

    /**
     * Output the start of a episode loop. By default this is a UL.
     *
     * @param bool $echo Should echo?.
     * @return string
     */
    function masvideos_episode_loop_start( $echo = true ) {
        ob_start();

        masvideos_set_episodes_loop_prop( 'loop', 0 );

        ?><div class="episodes columns-<?php echo esc_attr( masvideos_get_episodes_loop_prop( 'columns' ) ); ?>"><div class="episodes__inner"><?php

        $loop_start = apply_filters( 'masvideos_episode_loop_start', ob_get_clean() );

        if ( $echo ) {
            echo $loop_start; // WPCS: XSS ok.
        } else {
            return $loop_start;
        }
    }
}

if ( ! function_exists( 'masvideos_episodes_loop_content' ) ) {
    /*
     * Output the episode loop. By default this is a UL.
     */
    function masvideos_episodes_loop_content() {
        masvideos_get_template_part( 'content', 'episode' );
    }
}

if ( ! function_exists( 'masvideos_episode_loop_end' ) ) {

    /**
     * Output the end of a episode loop. By default this is a UL.
     *
     * @param bool $echo Should echo?.
     * @return string
     */
    function masvideos_episode_loop_end( $echo = true ) {
        ob_start();

        ?></div></div><?php

        $loop_end = apply_filters( 'masvideos_episode_loop_end', ob_get_clean() );

        if ( $echo ) {
            echo $loop_end; // WPCS: XSS ok.
        } else {
            return $loop_end;
        }
    }
}

if ( ! function_exists( 'masvideos_episode_page_title' ) ) {

    /**
     * Page Title function.
     *
     * @param  bool $echo Should echo title.
     * @return string
     */
    function masvideos_episode_page_title( $echo = true ) {

        if ( is_search() ) {
            /* translators: %s: search query */
            $page_title = sprintf( __( 'Search results: &ldquo;%s&rdquo;', 'masvideos' ), get_search_query() );

            if ( get_query_var( 'paged' ) ) {
                /* translators: %s: page number */
                $page_title .= sprintf( __( '&nbsp;&ndash; Page %s', 'masvideos' ), get_query_var( 'paged' ) );
            }
        } elseif ( is_tax() ) {

            $page_title = single_term_title( '', false );

        } else {

            $episodes_page_id = masvideos_get_page_id( 'episodes' );
            $page_title   = get_the_title( $episodes_page_id );

            if ( empty( $page_title ) ) {
                $page_title = post_type_archive_title( '', false );
            }

        }

        $page_title = apply_filters( 'masvideos_episode_page_title', $page_title );

        if ( $echo ) {
            echo $page_title; // WPCS: XSS ok.
        } else {
            return $page_title;
        }
    }
}

if ( ! function_exists( 'masvideos_template_loop_episode_poster_open' ) ) {
    /**
     * episode poster open in the loop.
     */
    function masvideos_template_loop_episode_poster_open() {
        echo '<div class="episode__poster">';
    }
}

if ( ! function_exists( 'masvideos_template_loop_episode_link_open' ) ) {
    /**
     * Insert the opening anchor tag for episode in the loop.
     */
    function masvideos_template_loop_episode_link_open() {
        global $episode;

        $link = apply_filters( 'masvideos_loop_episode_link', get_the_permalink(), $episode );

        echo '<a href="' . esc_url( $link ) . '" class="masvideos-LoopEpisode-link masvideos-loop-episode__link episode__link">';
    }
}

if ( ! function_exists( 'masvideos_template_loop_episode_poster' ) ) {
    /**
     * episode poster in the loop.
     */
    function masvideos_template_loop_episode_poster() {
        echo masvideos_get_episode_thumbnail( 'masvideos_episode_medium' );
    }
}

if ( ! function_exists( 'masvideos_template_loop_episode_link_close' ) ) {
    /**
     * Insert the opening anchor tag for episode in the loop.
     */
    function masvideos_template_loop_episode_link_close() {
        echo '</a>';
    }
}

if ( ! function_exists( 'masvideos_template_loop_episode_poster_close' ) ) {
    /**
     * episode poster close in the loop.
     */
    function masvideos_template_loop_episode_poster_close() {
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_template_loop_episode_body_open' ) ) {

    /**
     * episode body open in the episode loop.
     */
    function masvideos_template_loop_episode_body_open() {
         global $episode;
        echo '<div class="episode__body">';
        $link = apply_filters( 'masvideos_loop_episode_link', get_the_permalink(), $episode );
        echo '<a href="' . esc_url( $link ) . '" class="masvideos-LoopEpisode-link masvideos-loop-episode__link episode__link">';
    }
}

if ( ! function_exists( 'masvideos_template_loop_episode_body_close' ) ) {

    /**
     * episode body close in the episode loop.
     */
    function masvideos_template_loop_episode_body_close() {
        echo '</a>';
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_template_loop_episode_title' ) ) {

    /**
     * Show the episode title in the episode loop. By default this is an H3.
     */
    function masvideos_template_loop_episode_title() {
        global $episode;
        $episode_number = $episode->get_episode_number();
        if(! empty( $episode_number )) {
            echo '<span class="masvideos-loop-episode__number episode__number">' . $episode_number . '</span>';
        }

        the_title( '<h3 class="masvideos-loop-episode__title  episode__title">', '</h3>' );
    }
}

/**
 * Single
 */

if ( ! function_exists( 'masvideos_template_single_episode_episode' ) ) {

    /**
     * Output the episode.
     */
    function masvideos_template_single_episode_episode() {
        masvideos_the_episode();
    }
}

if ( ! function_exists( 'masvideos_template_single_episode_title' ) ) {

    /**
     * Output the episode title.
     */
    function masvideos_template_single_episode_title() {
        the_title( '<h1 class="episode_title entry-title">', '</h1>' );
    }
}

if ( ! function_exists( 'masvideos_template_single_episode_meta' ) ) {

    /**
     * Episode meta in the episode single.
     */
    function masvideos_template_single_episode_meta() {
        echo '<div class="episode__meta">';
            do_action( 'masvideos_single_episode_meta' );
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_template_single_episode_genres' ) ) {

    /**
     * Episode genres in the episode single.
     */
    function masvideos_template_single_episode_genres() {
        global $episode;

        $categories = get_the_term_list( $episode->get_id(), 'episode_genre', '', ', ' );

        if( ! empty ( $categories ) ) {
           echo '<span class="episode__meta--genre">' . $categories . '</span>';
        }
    }
}

if ( ! function_exists( 'masvideos_template_single_episode_tags' ) ) {

    /**
     * Episode tags in the episode single.
     */
    function masvideos_template_single_episode_tags() {
        global $episode;

        $tags = get_the_term_list( $episode->get_id(), 'episode_tag', '', ', ' );

        if( ! empty ( $tags ) ) {
            echo sprintf( '<span class="episode-tags">%s %s</span>', esc_html__( 'Tags:', 'masvideos' ), $tags );
        }
    }
}

if ( ! function_exists( 'masvideos_template_single_episode_release_date' ) ) {

    /**
     * Episode release date in the episode single.
     */
    function masvideos_template_single_episode_release_date() {
        global $episode;
        
        $release_date_formated = '';
        $release_date = $episode->get_episode_release_date();
        if( ! empty( $release_date ) ) {
            $release_date_formated = date( 'd.m.Y', strtotime( $release_date ) );
        }

        if( ! empty ( $release_date_formated ) ) {
            echo sprintf( '<span class="episode__meta--release-date">%s %s</span>', esc_html__( 'Added:', 'masvideos' ), $release_date_formated );
        }
    }
}

if ( ! function_exists( 'masvideos_template_single_episode_duration' ) ) {

    /**
     * Episode duration in the episode single.
     */
    function masvideos_template_single_episode_duration() {
        global $episode;
        
        $duration = $episode->get_episode_run_time();

        if( ! empty ( $duration ) ) {
            echo sprintf( '<span class="episode__meta--duration">%s</span>', $duration );
        }
    }
}

if ( ! function_exists( 'masvideos_template_single_episode_seasons_tabs' ) ) {

    /**
     * Episode's TV Show seasons and other episodes tabs in the episode single.
     */
    function masvideos_template_single_episode_seasons_tabs() {
        global $episode;

        $episode_id = $episode->get_id();
        $tv_show_id = $episode->get_tv_show_id();
        $tv_show = masvideos_get_tv_show( $tv_show_id );

        if( ! $tv_show ) {
            return;
        }

        $seasons = $tv_show->get_seasons();
        if( ! empty( $seasons ) ) {
            $tabs = array();
            foreach ( $seasons as $key => $season ) {
                if( ! empty( $season['name'] ) && ! empty( $season['episodes'] ) ) {
                    $episode_ids = $season['episodes'];
                    if ( ( $key = array_search( $episode_id, $episode_ids ) ) !== false ) {
                        unset( $episode_ids[$key] );
                    }
                    $episode_ids = implode( ",", $episode_ids );
                    $shortcode_atts = apply_filters( 'masvideos_template_single_episode_seasons_tab_shortcode_atts', array(
                        'orderby'   => 'post__in',
                        'order'     => 'DESC',
                        'limit'     => '-1',
                        'columns'   => '6',
                        'ids'       => $episode_ids,
                    ), $season, $key );

                    $tab = array(
                        'title'     => $season['name'],
                        'content'   => MasVideos_Shortcodes::episodes( $shortcode_atts ),
                        'priority'  => $key
                    );

                    $tabs[] = $tab;
                }
            }

            masvideos_get_template( 'global/tabs.php', array( 'tabs' => $tabs ) );
        }
    }
}

if ( ! function_exists( 'masvideos_template_single_episode_related_tv_shows' ) ) {

    /**
     * Episode related tv shows in the episode single.
     */
    function masvideos_template_single_episode_related_tv_shows() {
        global $episode;
        
        $tv_show_id = $episode->get_tv_show_id();

        if( $tv_show_id ) {
            masvideos_related_tv_shows( $tv_show_id );
        }
    }
}

if ( ! function_exists( 'masvideos_related_episodes' ) ) {

    /**
     * Output the related episodes.
     *
     * @param array $args Provided arguments.
     */
    function masvideos_related_episodes( $episode_id = false, $args = array() ) {
        global $episode;

        $episode_id = $episode_id ? $episode_id : $episode->get_id();

        if ( ! $episode_id ) {
            return;
        }

        $defaults = array(
            'limit'          => 5,
            'columns'        => 5,
            'orderby'        => 'rand',
            'order'          => 'desc',
        );

        $args = wp_parse_args( $args, $defaults );

        $related_episode_ids = masvideos_get_related_episodes( $episode_id, $args['limit'] );
        $args['ids'] = implode( ',', $related_episode_ids );

        echo MasVideos_Shortcodes::episodes( $args );
    }
}

if ( ! function_exists( 'masvideos_episode_comments' ) ) {

    /**
     * Output the Review comments template.
     *
     * @param WP_Comment $comment Comment object.
     * @param array      $args Arguments.
     * @param int        $depth Depth.
     */
    function masvideos_episode_comments( $comment, $args, $depth ) {
        $GLOBALS['comment'] = $comment; // WPCS: override ok.
        masvideos_get_template( 'single-episode/review.php', array(
            'comment' => $comment,
            'args'    => $args,
            'depth'   => $depth,
        ) );
    }
}

if ( ! function_exists( 'masvideos_episode_review_display_gravatar' ) ) {
    /**
     * Display the review authors gravatar
     *
     * @param array $comment WP_Comment.
     * @return void
     */
    function masvideos_episode_review_display_gravatar( $comment ) {
        echo get_avatar( $comment, apply_filters( 'masvideos_episode_review_gravatar_size', '60' ), '' );
    }
}

if ( ! function_exists( 'masvideos_episode_review_display_rating' ) ) {
    /**
     * Display the reviewers star rating
     *
     * @return void
     */
    function masvideos_episode_review_display_rating() {
        if ( post_type_supports( 'episode', 'comments' ) ) {
            masvideos_get_template( 'single-episode/review-rating.php' );
        }
    }
}

if ( ! function_exists( 'masvideos_episode_review_display_meta' ) ) {
    /**
     * Display the review authors meta (name, verified owner, review date)
     *
     * @return void
     */
    function masvideos_episode_review_display_meta() {
        masvideos_get_template( 'single-episode/review-meta.php' );
    }
}

if ( ! function_exists( 'masvideos_episode_review_display_comment_text' ) ) {
    /**
     * Display the review content.
     */
    function masvideos_episode_review_display_comment_text() {
        echo '<div class="description">';
        comment_text();
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_template_single_episode_info_head_open' ) ) {
    /**
     * Single episode info head open
     */
    function masvideos_template_single_episode_info_head_open() {
        echo '<div class="episode__info--head">';
    }
}

if ( ! function_exists( 'masvideos_template_single_episode_info_head_close' ) ) {
    /**
     * Single episode info head close
     */
    function masvideos_template_single_episode_info_head_close() {
         echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_template_single_episode_rating_with_sharing_open' ) ) {
    /**
     * Single episode rating with sharing info open
     */
    function masvideos_template_single_episode_rating_with_sharing_open() {
        echo '<div class="episode__rating-with-sharing">';
    }
}


if ( ! function_exists( 'masvideos_template_single_episode_rating_with_sharing_close' ) ) {
    /**
     * Single episode rating with sharing info close
     */
    function masvideos_template_single_episode_rating_with_sharing_close() {
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_template_single_episode_rating_info' ) ) {
    /**
     * Single episode rating info open
     */
    function masvideos_template_single_episode_rating_info() {
        echo '<div class="episode__rating-with-sharing">';
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_template_single_episode_sharing_info' ) ) {
    /**
     * Single episode sharing info open
     */
    function masvideos_template_single_episode_sharing_info() {
        echo '<div class="episode__rating-with-sharing">';
        echo '</div>';
    }
}

