<?php
/**
 * MasVideos Video Template
 *
 * Functions for the templating system.
 *
 * @package  MasVideos\Functions
 * @version  1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * When the_post is called, put video data into a global.
 *
 * @param mixed $post Post Object.
 * @return MasVideos_Video
 */
function masvideos_setup_video_data( $post ) {
    unset( $GLOBALS['video'] );

    if ( is_int( $post ) ) {
        $the_post = get_post( $post );
    } else {
        $the_post = $post;
    }

    if ( empty( $the_post->post_type ) || ! in_array( $the_post->post_type, array( 'video' ), true ) ) {
        return;
    }

    $GLOBALS['video'] = masvideos_get_video( $the_post );

    return $GLOBALS['video'];
}
add_action( 'the_post', 'masvideos_setup_video_data' );

/**
 * Sets up the masvideos_videos_loop global from the passed args or from the main query.
 *
 * @since 1.0.0
 * @param array $args Args to pass into the global.
 */
function masvideos_setup_videos_loop( $args = array() ) {
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
    if ( $GLOBALS['wp_query']->get( 'masvideos_video_query' ) ) {
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
    if ( isset( $GLOBALS['masvideos_videos_loop'] ) ) {
        $default_args = array_merge( $default_args, $GLOBALS['masvideos_videos_loop'] );
    }

    $GLOBALS['masvideos_videos_loop'] = wp_parse_args( $args, $default_args );
}
add_action( 'masvideos_before_videos_loop', 'masvideos_setup_videos_loop' );

/**
 * Resets the masvideos_videos_loop global.
 *
 * @since 1.0.0
 */
function masvideos_reset_videos_loop() {
    unset( $GLOBALS['masvideos_videos_loop'] );
}
add_action( 'masvideos_after_videos_loop', 'masvideos_reset_videos_loop', 999 );

/**
 * Gets a property from the masvideos_videos_loop global.
 *
 * @since 1.0.0
 * @param string $prop Prop to get.
 * @param string $default Default if the prop does not exist.
 * @return mixed
 */
function masvideos_get_videos_loop_prop( $prop, $default = '' ) {
    masvideos_setup_videos_loop(); // Ensure shop loop is setup.

    return isset( $GLOBALS['masvideos_videos_loop'], $GLOBALS['masvideos_videos_loop'][ $prop ] ) ? $GLOBALS['masvideos_videos_loop'][ $prop ] : $default;
}

/**
 * Sets a property in the masvideos_videos_loop global.
 *
 * @since 1.0.0
 * @param string $prop Prop to set.
 * @param string $value Value to set.
 */
function masvideos_set_videos_loop_prop( $prop, $value = '' ) {
    if ( ! isset( $GLOBALS['masvideos_videos_loop'] ) ) {
        masvideos_setup_videos_loop();
    }
    $GLOBALS['masvideos_videos_loop'][ $prop ] = $value;
}

/**
 * Check if we will be showing videos.
 *
 * @return bool
 */
function masvideos_videos_will_display() {
    return 0 < masvideos_get_videos_loop_prop( 'total', 0 );
}

/**
 * Should the MasVideos loop be displayed?
 *
 * This will return true if we have posts (videos) or if we have subcats to display.
 *
 * @since 3.4.0
 * @return bool
 */
function masvideos_videos_loop() {
    return have_posts();
}

/**
 * Get the default columns setting - this is how many movies will be shown per row in loops.
 *
 * @since 1.0.0
 * @return int
 */
function masvideos_get_default_videos_per_row() {
    $columns      = get_option( 'masvideos_video_columns', 4 );
    $video_grid   = masvideos_get_theme_support( 'video_grid' );
    $min_columns  = isset( $video_grid['min_columns'] ) ? absint( $video_grid['min_columns'] ) : 0;
    $max_columns  = isset( $video_grid['max_columns'] ) ? absint( $video_grid['max_columns'] ) : 0;

    if ( $min_columns && $columns < $min_columns ) {
        $columns = $min_columns;
        update_option( 'masvideos_video_columns', $columns );
    } elseif ( $max_columns && $columns > $max_columns ) {
        $columns = $max_columns;
        update_option( 'masvideos_video_columns', $columns );
    }

    $columns = absint( $columns );

    return max( 1, $columns );
}

/**
 * Get the default rows setting - this is how many video rows will be shown in loops.
 *
 * @since 1.0.0
 * @return int
 */
function masvideos_get_default_video_rows_per_page() {
    $rows         = absint( get_option( 'masvideos_video_rows', 4 ) );
    $video_grid   = masvideos_get_theme_support( 'video_grid' );
    $min_rows     = isset( $video_grid['min_rows'] ) ? absint( $video_grid['min_rows'] ) : 0;
    $max_rows     = isset( $video_grid['max_rows'] ) ? absint( $video_grid['max_rows'] ) : 0;

    if ( $min_rows && $rows < $min_rows ) {
        $rows = $min_rows;
        update_option( 'masvideos_video_rows', $rows );
    } elseif ( $max_rows && $rows > $max_rows ) {
        $rows = $max_rows;
        update_option( 'masvideos_video_rows', $rows );
    }

    return $rows;
}

/**
 * Display the classes for the video div.
 *
 * @since 1.0.0
 * @param string|array           $class      One or more classes to add to the class list.
 * @param int|WP_Post|MasVideos_Videos_Query $video_id Video ID or video object.
 */
function masvideos_video_class( $class = '', $video_id = null ) {
    // echo 'class="' . esc_attr( join( ' ', wc_get_video_class( $class, $video_id ) ) ) . '"';
    post_class();
}

/**
 * Loop
 */

if ( ! function_exists( 'masvideos_video_loop_start' ) ) {
    /**
     * Output the start of a video loop. By default this is a UL.
     *
     * @param bool $echo Should echo?.
     * @return string
     */
    function masvideos_video_loop_start( $echo = true ) {
        ob_start();

        masvideos_set_videos_loop_prop( 'loop', 0 );

        ?><div class="videos columns-<?php echo esc_attr( masvideos_get_videos_loop_prop( 'columns' ) ); ?>"><div class="videos__inner"><?php

        $loop_start = apply_filters( 'masvideos_video_loop_start', ob_get_clean() );

        if ( $echo ) {
            echo $loop_start; // WPCS: XSS ok.
        } else {
            return $loop_start;
        }
    }
}

if ( ! function_exists( 'masvideos_videos_loop_content' ) ) {
    /*
     * Output the video loop. By default this is a UL.
     */
    function masvideos_videos_loop_content() {
        masvideos_get_template_part( 'content', 'video' );
    }
}

if ( ! function_exists( 'masvideos_video_loop_end' ) ) {

    /**
     * Output the end of a video loop. By default this is a UL.
     *
     * @param bool $echo Should echo?.
     * @return string
     */
    function masvideos_video_loop_end( $echo = true ) {
        ob_start();

        ?></div></div><?php

        $loop_end = apply_filters( 'masvideos_video_loop_end', ob_get_clean() );

        if ( $echo ) {
            echo $loop_end; // WPCS: XSS ok.
        } else {
            return $loop_end;
        }
    }
}

if ( ! function_exists( 'masvideos_video_page_title' ) ) {

    /**
     * Page Title function.
     *
     * @param  bool $echo Should echo title.
     * @return string
     */
    function masvideos_video_page_title( $echo = true ) {

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

            $videos_page_id = masvideos_get_page_id( 'videos' );
            $page_title   = get_the_title( $videos_page_id );

        }

        $page_title = apply_filters( 'masvideos_video_page_title', $page_title );

        if ( $echo ) {
            echo $page_title; // WPCS: XSS ok.
        } else {
            return $page_title;
        }
    }
}

if ( ! function_exists( 'masvideos_template_loop_video_feature_badge' ) ) {
    /**
     * videos container open in the loop.
     */
    function masvideos_template_loop_video_feature_badge() {
        global $video;

        if ( $video->get_featured() ) {
            echo '<span class="video__badge">';
            echo '<span class="video__badge--featured">' . esc_html__( apply_filters( 'masvideos_template_loop_video_feature_badge_text', 'Featured' ), 'masvideos' ) . '</span>';
            echo '</span>';
        }
    }
}

if ( ! function_exists( 'masvideos_template_loop_video_container_open' ) ) {
    /**
     * videos container open in the loop.
     */
    function masvideos_template_loop_video_container_open() {
        echo '<div class="video__container">';
    }
}

if ( ! function_exists( 'masvideos_template_loop_video_poster_open' ) ) {
    /**
     * videos poster open in the loop.
     */
    function masvideos_template_loop_video_poster_open() {
        echo '<div class="video__poster">';
    }
}

if ( ! function_exists( 'masvideos_template_loop_video_link_open' ) ) {
    /**
     * Insert the opening anchor tag for videos in the loop.
     */
    function masvideos_template_loop_video_link_open() {
        global $video;

        $link = apply_filters( 'masvideos_loop_video_link', get_the_permalink(), $video );

        echo '<a href="' . esc_url( $link ) . '" class="masvideos-LoopMovie-link masvideos-loop-video__link video__link">';
    }
}


if ( ! function_exists( 'masvideos_template_loop_video_poster' ) ) {
    /**
     * videos poster in the loop.
     */
    function masvideos_template_loop_video_poster() {
        echo masvideos_get_video_thumbnail( 'masvideos_video_medium' );
    }
}

if ( ! function_exists( 'masvideos_template_loop_video_link_close' ) ) {
    /**
     * Insert the opening anchor tag for videos in the loop.
     */
    function masvideos_template_loop_video_link_close() {
        echo '</a>';
    }
}

if ( ! function_exists( 'masvideos_template_loop_video_poster_close' ) ) {
    /**
     * videos poster close in the loop.
     */
    function masvideos_template_loop_video_poster_close() {
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_template_loop_video_duration' ) ) {
    /**
     * videos duration in the loop.
     */
    function masvideos_template_loop_video_duration() {
        global $movie;

        $duration = $movie->get_movie_run_time();
        
        if ( ! empty( $duration ) ) {
            echo '<span class="video__duration">' . wp_kses_post( $duration ) . '</span>';
        }
    }
}

if ( ! function_exists( 'masvideos_template_loop_video_container_close' ) ) {
    /**
     * videos container close in the loop.
     */
    function masvideos_template_loop_video_container_close() {
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_template_loop_video_body_open' ) ) {

    /**
     * video body open in the video loop.
     */
    function masvideos_template_loop_video_body_open() {
        echo '<div class="video__body">';
    }
}

if ( ! function_exists( 'masvideos_template_loop_video_info_open' ) ) {

    /**
     * video info open in the video loop.
     */
    function masvideos_template_loop_video_info_open() {
        echo '<div class="video__info">';
    }
}

if ( ! function_exists( 'masvideos_template_loop_video_info_head_open' ) ) {

    /**
     * video info head open in the video loop.
     */
    function masvideos_template_loop_video_info_head_open() {
        echo '<div class="video__info--head">';
    }
}

if ( ! function_exists( 'masvideos_template_loop_video_title' ) ) {

    /**
     * Show the video title in the video loop. By default this is an H3.
     */
    function masvideos_template_loop_video_title() {
        the_title( '<h3 class="masvideos-loop-video__title  video__title">', '</h3>' );
    }
}

if ( ! function_exists( 'masvideos_template_loop_video_meta' ) ) {

    /**
     * video meta in the video loop.
     */
    function masvideos_template_loop_video_meta() {
        echo '<div class="video__meta">';
            echo '<span class="video__meta--last-seen">' . human_time_diff( get_post_time( 'U' ), current_time( 'timestamp' ) ) . '</span>';
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_template_loop_video_info_head_close' ) ) {

    /**
     * video info head close in the video loop.
     */
    function masvideos_template_loop_video_info_head_close() {
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_template_loop_video_short_desc' ) ) {

    /**
     * video short description in the video loop.
     */
    function masvideos_template_loop_video_short_desc() {
        global $post;

        $short_description = apply_filters( 'masvideos_template_loop_video_short_desc', $post->post_excerpt );

        if ( ! $short_description ) {
            return;
        }

        ?>
        <div class="video__short-description">
            <?php echo '<p>' . $short_description . '</p>'; ?>
        </div>

        <?php
    }
}

if ( ! function_exists( 'masvideos_template_loop_video_actions' ) ) {

    /**
     * video actions in the video loop.
     */
    function masvideos_template_loop_video_actions() {
        global $video;
        echo '<div class="video__actions">';
            echo '<a href="' . esc_url( get_permalink( $video ) ) . '" class="video-actions--link_watch">' . esc_html__( 'Watch Now', 'masvideos' ) . '</a>';
            echo '<a href="#" class="video-actions--link_add-to-playlist">' . esc_html__( '+ Playlist', 'masvideos' ) . '</a>';
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_template_loop_video_info_close' ) ) {

    /**
     * video info close in the video loop.
     */
    function masvideos_template_loop_video_info_close() {
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_template_loop_video_review_info_open' ) ) {

    /**
     * video review info open in the video loop.
     */
    function masvideos_template_loop_video_review_info_open() {
        echo '<div class="video__review-info">';
    }
}

if ( ! function_exists( 'masvideos_template_loop_video_avg_rating' ) ) {

    /**
     * video avg rating in the video loop.
     */
    function masvideos_template_loop_video_avg_rating() {
        echo '<a href="#" class="avg-rating"></a>';
    }
}

if ( ! function_exists( 'masvideos_template_loop_video_viewers_count' ) ) {

    /**
     * video actions in the video loop.
     */
    function masvideos_template_loop_video_viewers_count() {
        echo '<div class="viewers-count"></div>';
    }
}

if ( ! function_exists( 'masvideos_template_loop_video_review_info_close' ) ) {

    /**
     * video review info close in the video loop.
     */
    function masvideos_template_loop_video_review_info_close() {
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_template_loop_video_body_close' ) ) {

    /**
     * video body close in the video loop.
     */
    function masvideos_template_loop_video_body_close() {
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_template_loop_category_title' ) ) {

    /**
     * Show the subcategory title in the loop.
     *
     * @param object $category Category object.
     */
    function masvideos_template_loop_category_title( $category ) {
        ?>
        <h2 class="masvideos-loop-category__title">
            <?php
            echo esc_html( $category->name );

            if ( $category->count > 0 ) {
                echo apply_filters( 'masvideos_subcategory_count_html', ' <mark class="count">(' . esc_html( $category->count ) . ')</mark>', $category ); // WPCS: XSS ok.
            }
            ?>
        </h2>
        <?php
    }
}


/**
 * Single
 */

if ( ! function_exists( 'masvideos_template_single_video_video' ) ) {

    /**
     * Output the video.
     */
    function masvideos_template_single_video_video() {
        masvideos_the_video();
    }
}

if ( ! function_exists( 'masvideos_template_single_video_title' ) ) {

    /**
     * Output the video title.
     */
    function masvideos_template_single_video_title() {
        the_title( '<h1 class="video_title entry-title">', '</h1>' );
    }
}

if ( ! function_exists( 'masvideos_template_single_video_meta' ) ) {

    /**
     * Output the video meta.
     */
    function masvideos_template_single_video_meta() {
        echo '<p class="single_video_meta">';
        masvideos_template_single_video_author();
        masvideos_template_single_video_posted_on();
        echo '</p>';
    }
}

if ( ! function_exists( 'masvideos_template_single_video_author' ) ) {

    /**
     * Output the video author.
     */
    function masvideos_template_single_video_author() {
        echo '<span class="video_author">' .  apply_filters( 'masvideos_template_single_video_author', esc_html( 'by', 'masvideos' ) ) . '<strong>' . get_the_author() . '</strong></span>';
    }
}

if ( ! function_exists( 'masvideos_template_single_video_posted_on' ) ) {

    /**
     * Output the video posted on.
     */
    function masvideos_template_single_video_posted_on() {
        echo '<span class="video_posted_on">' . apply_filters( 'masvideos_template_single_video_posted_on', esc_html( 'published on', 'masvideos' ) ) .  get_the_date() . '</span>';
    }
}

if ( ! function_exists( 'masvideos_video_comments' ) ) {

    /**
     * Output the Review comments template.
     *
     * @param WP_Comment $comment Comment object.
     * @param array      $args Arguments.
     * @param int        $depth Depth.
     */
    function masvideos_video_comments( $comment, $args, $depth ) {
        $GLOBALS['comment'] = $comment; // WPCS: override ok.
        masvideos_get_template( 'single-video/review.php', array(
            'comment' => $comment,
            'args'    => $args,
            'depth'   => $depth,
        ) );
    }
}

if ( ! function_exists( 'masvideos_video_review_display_gravatar' ) ) {
    /**
     * Display the review authors gravatar
     *
     * @param array $comment WP_Comment.
     * @return void
     */
    function masvideos_video_review_display_gravatar( $comment ) {
        echo get_avatar( $comment, apply_filters( 'masvideos_video_review_gravatar_size', '60' ), '' );
    }
}

if ( ! function_exists( 'masvideos_video_review_display_rating' ) ) {
    /**
     * Display the reviewers star rating
     *
     * @return void
     */
    function masvideos_video_review_display_rating() {
        if ( post_type_supports( 'video', 'comments' ) ) {
            masvideos_get_template( 'single-video/review-rating.php' );
        }
    }
}

if ( ! function_exists( 'masvideos_video_review_display_meta' ) ) {
    /**
     * Display the review authors meta (name, verified owner, review date)
     *
     * @return void
     */
    function masvideos_video_review_display_meta() {
        masvideos_get_template( 'single-video/review-meta.php' );
    }
}

if ( ! function_exists( 'masvideos_video_review_display_comment_text' ) ) {
    /**
     * Display the review content.
     */
    function masvideos_video_review_display_comment_text() {
        echo '<div class="description">';
        comment_text();
        echo '</div>';
    }
}