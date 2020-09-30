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
 * Search Form
 */
if ( ! function_exists( 'masvideos_get_episode_search_form' ) ) {

    /**
     * Display episode search form.
     *
     * Will first attempt to locate the episode-searchform.php file in either the child or.
     * the parent, then load it. If it doesn't exist, then the default search form.
     * will be displayed.
     *
     * The default searchform uses html5.
     *
     * @param bool $echo (default: true).
     * @return string
     */
    function masvideos_get_episode_search_form( $echo = true ) {
        global $episode_search_form_index;

        ob_start();

        if ( empty( $episode_search_form_index ) ) {
            $episode_search_form_index = 0;
        }

        do_action( 'pre_masvideos_get_episode_search_form' );

        masvideos_get_template( 'search-form.php', array(
            'index' => $episode_search_form_index++,
            'post_type' => 'episode',
        ) );

        $form = apply_filters( 'masvideos_get_episode_search_form', ob_get_clean() );

        if ( ! $echo ) {
            return $form;
        }

        echo $form; // WPCS: XSS ok.
    }
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

if ( ! function_exists( 'masvideos_no_episodes_found' ) ) {

    /**
     * Handles the loop when no episodes were found/no episode exist.
     */
    function masvideos_no_episodes_found() {
        ?><p class="masvideos-info"><?php _e( 'No episodes were found matching your selection.', 'masvideos' ); ?></p><?php
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
    }
}

if ( ! function_exists( 'masvideos_template_loop_episode_body_close' ) ) {

    /**
     * episode body close in the episode loop.
     */
    function masvideos_template_loop_episode_body_close() {
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_template_loop_episode_title' ) ) {

    /**
     * Show the episode title in the episode loop. By default this is an H3.
     */
    function masvideos_template_loop_episode_title() {
        global $episode;

        $tv_show_id = $episode->get_tv_show_id();
        $episode_number = $episode->get_episode_number();

        if( apply_filters( 'masvideos_template_loop_episode_display_tv_show_name_before_title', false ) && ! empty( $tv_show_id ) ) {
            echo '<span class="masvideos-loop-episode__tv-show-name episode__tv-show-name">' . get_the_title( $tv_show_id ) . '</span>';
        }

        if( apply_filters( 'masvideos_template_loop_episode_display_episode_number_before_title', true ) && ! empty( $episode_number ) ) {
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
        global $episode;

        $before_title = '';

        $tv_show_id = $episode->get_tv_show_id();
        if( ! empty( $tv_show_id ) ) {
            $before_title .= get_the_title( $tv_show_id ) . ' - ';
        }

        $episode_number = $episode->get_episode_number();
        if( ! empty( $episode_number ) ) {
            $before_title .= $episode_number . ' - ';
        }

        the_title( '<h1 class="episode_title entry-title">' . $before_title, '</h1>' );
    }
}

if ( ! function_exists( 'masvideos_template_single_episode_meta' ) ) {

    /**
     * Episode meta in the episode single.
     */
    function masvideos_template_single_episode_meta() {
        ?>
        <div class="episode__meta">
            <?php do_action( 'masvideos_single_episode_meta' ); ?>
        </div><?php
    }
}

if ( ! function_exists( 'masvideos_template_single_episode_genres' ) ) {

    /**
     * Episode genres in the episode single.
     */
    function masvideos_template_single_episode_genres() {
        global $episode;

        if ( masvideos_is_episode_archive() ) {
            $genres = get_the_term_list( $episode->get_id(), 'episode_genre', '', ', ' );
        } else {
            $tv_show_id = $episode->get_tv_show_id();
            $genres = ! empty( $tv_show_id ) ? get_the_term_list( $tv_show_id, 'tv_show_genre', '', ', ' ) : '';
        }

        if( ! empty ( $genres ) ) {
            echo sprintf( '<span class="episode__meta--genres">%s</span>', $genres );
        }
    }
}

if ( ! function_exists( 'masvideos_template_single_episode_tags' ) ) {

    /**
     * Episode tags in the episode single.
     */
    function masvideos_template_single_episode_tags() {
        global $episode;

        if ( masvideos_is_episode_archive() ) {
            $tags = get_the_term_list( $episode->get_id(), 'episode_tag', '', ', ' );
        } else {
            $tv_show_id = $episode->get_tv_show_id();
            $tags = ! empty( $tv_show_id ) ? get_the_term_list( $tv_show_id, 'tv_show_tag', '', ', ' ) : '';
        }

        if( ! empty ( $tags ) ) {
            echo sprintf( '<span class="episode__tags">%s %s</span>', esc_html__( 'Tags:', 'masvideos' ), $tags );
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

if ( ! function_exists( 'masvideos_template_single_episode_prev_navigation' ) ) {

    /**
     * Episode previous link in the episode single.
     */
    function masvideos_template_single_episode_prev_navigation() {
        global $episode;

        $episodes = masvideos_get_single_episode_prev_next_ids( $episode );

        if( isset( $episodes['prev'] ) && $episodes['prev'] ) {
            $episode_url = get_permalink( $episodes['prev'] );
            echo '<div class="episode__player--prev-episode">';
            echo '<a href="' . esc_url( $episode_url ) . '" class="episode__player--prev-episode__link">';
            echo '<span class="episode__player--prev-episode__label">
                    ' . esc_html__( 'Previous Episode ', 'masvideos' ) . '</span>';
            echo '</a>';
            echo '</div>';
        }
    }
}

if ( ! function_exists( 'masvideos_template_single_episode_next_navigation' ) ) {

    /**
     * Episode next link in the episode single.
     */
    function masvideos_template_single_episode_next_navigation() {
        global $episode;

        $episodes = masvideos_get_single_episode_prev_next_ids( $episode );

        if( isset( $episodes['next'] ) && $episodes['next'] ) {
            $episode_url = get_permalink( $episodes['next'] );
            echo '<div class="episode__player--next-episode">';
            echo '<a href="' . esc_url( $episode_url ) . '" class="episode__player--next-episode__link">';
            echo '<span class="episode__player--next-episode__label">
                    ' . esc_html__( 'Next Episode ', 'masvideos' ) . '</span>';
            echo '</a>';
            echo '</div>';
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
            $season_id = $episode->get_tv_show_season_id();
            $season_position = 0;
            foreach ( $seasons as $key => $season ) {
                if( ! empty( $season['name'] ) && ! empty( $season['episodes'] ) ) {
                    $episode_ids = $season['episodes'];
                    if ( ( $current_episode_key = array_search( $episode_id, $episode_ids ) ) !== false ) {
                        unset( $episode_ids[$current_episode_key] );
                    }
                    $episode_ids = implode( ",", $episode_ids );
                    if( ! empty( $episode_ids ) ) {
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
            }

            if( ! empty( $season_id ) && isset( $seasons[$season_id] ) && ! empty( $seasons[$season_id] ) ) {
                $season_position = $seasons[$season_id]['position'];
            }

            masvideos_get_template( 'global/tabs.php', array( 'tabs' => $tabs, 'class' => 'episode-seasons-tabs', 'default_active_tab' => $season_position ) );
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

        $defaults = apply_filters( 'masvideos_related_episodes_default_args', array(
            'limit'          => 5,
            'columns'        => 5,
            'orderby'        => 'rand',
            'order'          => 'desc',
        ) );

        $args = wp_parse_args( $args, $defaults );

        $title = apply_filters( 'masvideos_related_episodes_title', esc_html__( 'Related Episodes', 'masvideos' ), $episode_id );

        $related_episode_ids = masvideos_get_related_episodes( $episode_id, $args['limit'] );
        $args['ids'] = implode( ',', $related_episode_ids );

        if( ! empty( $related_episode_ids ) ) {
            echo '<section class="episode__related">';
                echo sprintf( '<h2 class="episode__related--title">%s</h2>', $title );
                echo MasVideos_Shortcodes::episodes( $args );
            echo '</section>';
        }
    }
}

if ( ! function_exists( 'masvideos_template_single_episode_tabs' ) ) {

    /**
     * Episode tabs in the episode single.
     */
    function masvideos_template_single_episode_tabs() {
        global $episode, $post;

        $tabs = array();
        
        // Description tab - shows episode content.
        if ( $post->post_content ) {
            $tabs['description'] = array(
                'title'     => esc_html__( 'Description', 'masvideos' ),
                'callback'  => 'masvideos_template_single_episode_description',
                'priority'  => 10
            );
        }

        // Sources tab - shows link sources.
        if ( $episode && ( $episode->has_sources() ) ) {
            $tabs['sources'] = array(
                'title'     => esc_html__( 'Sources', 'masvideos' ),
                'callback'  => 'masvideos_template_single_episode_sources',
                'priority'  => 30
            );
        }

        // Reviews tab - shows comments.
        if ( comments_open() ) {
            $tabs['reviews'] = array(
                'title'     => esc_html__( 'Review', 'masvideos' ),
                'callback'  => 'comments_template',
                'priority'  => 20
            );
        }

        $tabs = apply_filters( 'masvideos_template_single_episode_tabs', $tabs );

        if( ! empty( $tabs ) ) {
            masvideos_get_template( 'global/tabs.php', array( 'tabs' => $tabs, 'class' => 'episode-tabs' ) );
        }
    }
}

if ( ! function_exists( 'masvideos_template_single_episode_info_head_open' ) ) {
    /**
     * Single episode info head open
     */
    function masvideos_template_single_episode_info_head_open() {
        ?><div class="episode__info--head"><?php
    }
}

if ( ! function_exists( 'masvideos_template_single_episode_info_head_close' ) ) {
    /**
     * Single episode info head close
     */
    function masvideos_template_single_episode_info_head_close() {
        ?></div><?php
    }
}

if ( ! function_exists( 'masvideos_template_single_episode_rating_with_sharing_open' ) ) {
    /**
     * Single episode rating with sharing info open
     */
    function masvideos_template_single_episode_rating_with_sharing_open() {
        ?><div class="episode__rating-with-sharing"><?php
    }
}


if ( ! function_exists( 'masvideos_template_single_episode_rating_with_sharing_close' ) ) {
    /**
     * Single episode rating with sharing info close
     */
    function masvideos_template_single_episode_rating_with_sharing_close() {
        ?></div><?php
    }
}

if ( ! function_exists( 'masvideos_template_single_episode_rating_with_playlist_wrap_open' ) ) {
    function masvideos_template_single_episode_rating_with_playlist_wrap_open() {
        ?>
        <div class="episode__rating-with-playlist">
        <?php
    }
}

if ( ! function_exists( 'masvideos_template_single_episode_rating_with_playlist_wrap_close' ) ) {
    function masvideos_template_single_episode_rating_with_playlist_wrap_close() {
        ?>
        </div>
        <?php
    }
}

if ( ! function_exists( 'masvideos_template_single_episode_avg_rating' ) ) {
    /**
     * Single episode rating open
     */
    function masvideos_template_single_episode_avg_rating() {
        global $episode;
        ?>
        <div class="episode__avg-rating">

        <?php if ( ! empty( $episode->get_review_count() ) && $episode->get_review_count() > 0 ) { ?>
            <a href="<?php echo esc_url( get_permalink( $episode->get_id() ) . '#reviews' ); ?>" class="avg-rating">
                <div class="avg-rating__inner">
                    <span class="avg-rating__number"> <?php echo number_format( $episode->get_average_rating(), 1, '.', '' ); ?></span>
                    <span class="avg-rating__text">
                        <?php echo wp_kses_post( sprintf( _n( '<span>%s</span> Vote', '<span>%s</span> votes', $episode->get_review_count(), 'masvideos' ), $episode->get_review_count() ) ) ; ?>
                    </span>
                </div>
            </a>
        <?php } ?>
        </div><?php
    }
}

if ( ! function_exists( 'masvideos_template_single_episode_info_body_open' ) ) {
    /**
     * Single episode info body open
     */
    function masvideos_template_single_episode_info_body_open() {
        ?><div class="episode__info--body"><?php
    }
}

if ( ! function_exists( 'masvideos_template_single_episode_info_body_close' ) ) {
    /**
     * Single episode info body close
     */
    function masvideos_template_single_episode_info_body_close() {
        ?></div><?php
    }
}

if ( ! function_exists( 'masvideos_template_single_episode_description' ) ) {
    /**
     * Single episode description
     */
    function masvideos_template_single_episode_description() {
        ?>
        <div class="episode__description">
            <div><?php the_content(); ?></div>
        </div>
        <?php
    }
}

if ( ! function_exists( 'masvideos_template_single_episode_short_desc' ) ) {
    /**
     * Single episode short description
     */
    function masvideos_template_single_episode_short_desc() {
        global $post;

        $short_description = apply_filters( 'masvideos_template_single_episode_short_desc', $post->post_excerpt );

        if ( ! $short_description ) {
            return;
        }

        ?>
        <div class="episode__short-description">
            <?php echo '<p>' . $short_description . '</p>'; ?>
        </div>
        <?php
    }
}

if ( ! function_exists( 'masvideos_template_single_episode_sources' ) ) {

    /**
     * Episode sources in the episode single.
     */
    function masvideos_template_single_episode_sources() {
        masvideos_get_template( 'single-episode/sources.php' );
    }
}

if ( ! function_exists( 'masvideos_template_single_episode_head_wrap_open' ) ) {
    /**
     * Single episode head open
     */
    function masvideos_template_single_episode_head_wrap_open() {
        ?>
        <div class="episode__head">
        <?php
    }
}

if ( ! function_exists( 'masvideos_template_single_episode_head_wrap_close' ) ) {
    /**
     * Single episode head close
     */
    function masvideos_template_single_episode_head_wrap_close() {
        ?>
        </div>
        <?php
    }
}


if ( ! function_exists( 'masvideos_template_single_episode_player_wrap_open' ) ) {
    /**
     * Single episode player open
     */
    function masvideos_template_single_episode_player_wrap_open() {
        ?>
        <div class="episode__player">
        <?php
    }
}

if ( ! function_exists( 'masvideos_template_single_episode_player_wrap_close' ) ) {
    /**
     * Single episode player close
     */
    function masvideos_template_single_episode_player_wrap_close() {
        ?>
        </div>
        <?php
    }
}

if ( ! function_exists( 'masvideos_template_single_episode_play_source_link' ) ) {
    /**
     * Single episode play source link
     */
    function masvideos_template_single_episode_play_source_link( $source ) {
        if( ! empty( $source['embed_content'] ) ) {
            $source_content = apply_filters( 'the_content', $source['embed_content'] );
            ?>
            <a href="#" class="play-source episode-play-source" data-content="<?php echo esc_attr( htmlspecialchars( $source_content ) ); ?>">
                <span><?php echo apply_filters( 'masvideos_episode_play_source_text', esc_html__( 'Play Now', 'masvideos' ) ); ?></span>
            </a>
            <?php
        }
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
