<?php
/**
 * MasVideos Template
 *
 * Functions for the templating system.
 *
 * @package  MasVideos\Functions
 * @version  1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Handle redirects before content is output - hooked into template_redirect so is_page works.
 */
function masvideos_template_redirect() {
    global $wp_query, $wp;

    if ( ! empty( $_GET['page_id'] ) && '' === get_option( 'permalink_structure' ) && masvideos_get_page_id( 'videos' ) === absint( $_GET['page_id'] ) && get_post_type_archive_link( 'video' ) ) { // WPCS: input var ok, CSRF ok.

        // When default permalinks are enabled, redirect videos page to post type archive url.
        wp_safe_redirect( get_post_type_archive_link( 'video' ) );
        exit;

    } elseif ( is_search() && is_post_type_archive( 'video' ) && apply_filters( 'masvideos_redirect_single_search_result', true ) && 1 === absint( $wp_query->found_posts ) ) {

        // Redirect to the video page if we have a single video.
        $video = masvideos_get_video( $wp_query->post );

        if ( $video && $video->is_visible() ) {
            wp_safe_redirect( get_permalink( $video->get_id() ), 302 );
            exit;
        }

    } elseif ( ! empty( $_GET['page_id'] ) && '' === get_option( 'permalink_structure' ) && masvideos_get_page_id( 'movies' ) === absint( $_GET['page_id'] ) && get_post_type_archive_link( 'movie' ) ) { // WPCS: input var ok, CSRF ok.

        // When default permalinks are enabled, redirect movies page to post type archive url.
        wp_safe_redirect( get_post_type_archive_link( 'movie' ) );
        exit;

    } elseif ( is_search() && is_post_type_archive( 'movie' ) && apply_filters( 'masvideos_redirect_single_search_result', true ) && 1 === absint( $wp_query->found_posts ) ) {

        // Redirect to the movie page if we have a single movie.
        $movie = masvideos_get_movie( $wp_query->post );

        if ( $movie && $movie->is_visible() ) {
            wp_safe_redirect( get_permalink( $movie->get_id() ), 302 );
            exit;
        }

    }
}
add_action( 'template_redirect', 'masvideos_template_redirect' );

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
// add_action( 'the_post', 'masvideos_setup_video_data' );

/**
 * When the_post is called, put movie data into a global.
 *
 * @param mixed $post Post Object.
 * @return MasVideos_Movie
 */
function masvideos_setup_movie_data( $post ) {
    unset( $GLOBALS['movie'] );

    if ( is_int( $post ) ) {
        $the_post = get_post( $post );
    } else {
        $the_post = $post;
    }

    if ( empty( $the_post->post_type ) || ! in_array( $the_post->post_type, array( 'movie' ), true ) ) {
        return;
    }

    $GLOBALS['movie'] = masvideos_get_movie( $the_post );

    return $GLOBALS['movie'];
}
// add_action( 'the_post', 'masvideos_setup_movie_data' );

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
 * Sets up the masvideos_movies_loop global from the passed args or from the main query.
 *
 * @since 1.0.0
 * @param array $args Args to pass into the global.
 */
function masvideos_setup_movies_loop( $args = array() ) {
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
    if ( $GLOBALS['wp_query']->get( 'masvideos_movie_query' ) ) {
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
    if ( isset( $GLOBALS['masvideos_movies_loop'] ) ) {
        $default_args = array_merge( $default_args, $GLOBALS['masvideos_movies_loop'] );
    }

    $GLOBALS['masvideos_movies_loop'] = wp_parse_args( $args, $default_args );
}
add_action( 'masvideos_before_movies_loop', 'masvideos_setup_movies_loop' );

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
 * Resets the masvideos_movies_loop global.
 *
 * @since 1.0.0
 */
function masvideos_reset_movies_loop() {
    unset( $GLOBALS['masvideos_movies_loop'] );
}
add_action( 'masvideos_after_movies_loop', 'masvideos_reset_movies_loop', 999 );

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
 * Gets a property from the masvideos_movies_loop global.
 *
 * @since 1.0.0
 * @param string $prop Prop to get.
 * @param string $default Default if the prop does not exist.
 * @return mixed
 */
function masvideos_get_movies_loop_prop( $prop, $default = '' ) {
    masvideos_setup_movies_loop(); // Ensure shop loop is setup.

    return isset( $GLOBALS['masvideos_movies_loop'], $GLOBALS['masvideos_movies_loop'][ $prop ] ) ? $GLOBALS['masvideos_movies_loop'][ $prop ] : $default;
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
 * Sets a property in the masvideos_movies_loop global.
 *
 * @since 1.0.0
 * @param string $prop Prop to set.
 * @param string $value Value to set.
 */
function masvideos_set_movies_loop_prop( $prop, $value = '' ) {
    if ( ! isset( $GLOBALS['masvideos_movies_loop'] ) ) {
        masvideos_setup_movies_loop();
    }
    $GLOBALS['masvideos_movies_loop'][ $prop ] = $value;
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
 * Should the MasVideos loop be displayed?
 *
 * This will return true if we have posts (movies) or if we have subcats to display.
 *
 * @since 3.4.0
 * @return bool
 */
function masvideos_movies_loop() {
    return have_posts();
}

/**
 * Output generator tag to aid debugging.
 *
 * @access public
 *
 * @param string $gen Generator.
 * @param string $type Type.
 *
 * @return string
 */
function masvideos_generator_tag( $gen, $type ) {
    switch ( $type ) {
        case 'html':
            $gen .= "\n" . '<meta name="generator" content="MasVideos ' . esc_attr( MASVIDEOS_VERSION ) . '">';
            break;
        case 'xhtml':
            $gen .= "\n" . '<meta name="generator" content="MasVideos ' . esc_attr( MASVIDEOS_VERSION ) . '" />';
            break;
    }
    return $gen;
}

/**
 * Get the placeholder image URL etc.
 *
 * @access public
 * @return string
 */
function masvideos_placeholder_img_src() {
    return apply_filters( 'masvideos_placeholder_img_src', MasVideos()->plugin_url() . '/assets/images/placeholder.png' );
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
 * Display the classes for the movie div.
 *
 * @since 1.0.0
 * @param string|array           $class      One or more classes to add to the class list.
 * @param int|WP_Post|MasVideos_Movies_Query $movie_id Movie ID or movie object.
 */
function masvideos_movie_class( $class = '', $movie_id = null ) {
    // echo 'class="' . esc_attr( join( ' ', wc_get_movie_class( $class, $movie_id ) ) ) . '"';
    post_class();
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
 * Get the default columns setting - this is how many movies will be shown per row in loops.
 *
 * @since 1.0.0
 * @return int
 */
function masvideos_get_default_movies_per_row() {
    $columns      = get_option( 'masvideos_movie_columns', 4 );
    $movie_grid   = masvideos_get_theme_support( 'movie_grid' );
    $min_columns  = isset( $movie_grid['min_columns'] ) ? absint( $movie_grid['min_columns'] ) : 0;
    $max_columns  = isset( $movie_grid['max_columns'] ) ? absint( $movie_grid['max_columns'] ) : 0;

    if ( $min_columns && $columns < $min_columns ) {
        $columns = $min_columns;
        update_option( 'masvideos_movie_columns', $columns );
    } elseif ( $max_columns && $columns > $max_columns ) {
        $columns = $max_columns;
        update_option( 'masvideos_movie_columns', $columns );
    }

    $columns = absint( $columns );

    return max( 1, $columns );
}

/**
 * Get the default rows setting - this is how many movie rows will be shown in loops.
 *
 * @since 1.0.0
 * @return int
 */
function masvideos_get_default_movie_rows_per_page() {
    $rows         = absint( get_option( 'masvideos_movie_rows', 4 ) );
    $movie_grid   = masvideos_get_theme_support( 'movie_grid' );
    $min_rows     = isset( $movie_grid['min_rows'] ) ? absint( $movie_grid['min_rows'] ) : 0;
    $max_rows     = isset( $movie_grid['max_rows'] ) ? absint( $movie_grid['max_rows'] ) : 0;

    if ( $min_rows && $rows < $min_rows ) {
        $rows = $min_rows;
        update_option( 'masvideos_movie_rows', $rows );
    } elseif ( $max_rows && $rows > $max_rows ) {
        $rows = $max_rows;
        update_option( 'masvideos_movie_rows', $rows );
    }

    return $rows;
}

/**
 * Outputs hidden form inputs for each query string variable.
 *
 * @since 1.0.0
 * @param string|array $values Name value pairs, or a URL to parse.
 * @param array        $exclude Keys to exclude.
 * @param string       $current_key Current key we are outputting.
 * @param bool         $return Whether to return.
 * @return string
 */
function masvideos_query_string_form_fields( $values = null, $exclude = array(), $current_key = '', $return = false ) {
    if ( is_null( $values ) ) {
        $values = $_GET; // WPCS: input var ok, CSRF ok.
    } elseif ( is_string( $values ) ) {
        $url_parts = wp_parse_url( $values );
        $values    = array();

        if ( ! empty( $url_parts['query'] ) ) {
            parse_str( $url_parts['query'], $values );
        }
    }
    $html = '';

    foreach ( $values as $key => $value ) {
        if ( in_array( $key, $exclude, true ) ) {
            continue;
        }
        if ( $current_key ) {
            $key = $current_key . '[' . $key . ']';
        }
        if ( is_array( $value ) ) {
            $html .= masvideos_query_string_form_fields( $value, $exclude, $key, true );
        } else {
            $html .= '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( wp_unslash( $value ) ) . '" />';
        }
    }

    if ( $return ) {
        return $html;
    }

    echo $html; // WPCS: XSS ok.
}


/**
 * Loop
 */

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

if ( ! function_exists( 'masvideos_movie_page_title' ) ) {

    /**
     * Page Title function.
     *
     * @param  bool $echo Should echo title.
     * @return string
     */
    function masvideos_movie_page_title( $echo = true ) {

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

            $movies_page_id = masvideos_get_page_id( 'movies' );
            $page_title   = get_the_title( $movies_page_id );

        }

        $page_title = apply_filters( 'masvideos_movie_page_title', $page_title );

        if ( $echo ) {
            echo $page_title; // WPCS: XSS ok.
        } else {
            return $page_title;
        }
    }
}

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

if ( ! function_exists( 'masvideos_movie_loop_start' ) ) {

    /**
     * Output the start of a movie loop. By default this is a UL.
     *
     * @param bool $echo Should echo?.
     * @return string
     */
    function masvideos_movie_loop_start( $echo = true ) {
        ob_start();

        masvideos_set_movies_loop_prop( 'loop', 0 );

        ?><div class="movies columns-<?php echo esc_attr( masvideos_get_movies_loop_prop( 'columns' ) ); ?>"><div class="movies__inner"><?php

        $loop_start = apply_filters( 'masvideos_movie_loop_start', ob_get_clean() );

        if ( $echo ) {
            echo $loop_start; // WPCS: XSS ok.
        } else {
            return $loop_start;
        }
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

if ( ! function_exists( 'masvideos_movie_loop_end' ) ) {

    /**
     * Output the end of a movie loop. By default this is a UL.
     *
     * @param bool $echo Should echo?.
     * @return string
     */
    function masvideos_movie_loop_end( $echo = true ) {
        ob_start();

        ?></div></div><?php

        $loop_end = apply_filters( 'masvideos_movie_loop_end', ob_get_clean() );

        if ( $echo ) {
            echo $loop_end; // WPCS: XSS ok.
        } else {
            return $loop_end;
        }
    }
}

if ( ! function_exists( 'masvideos_template_loop_video_title' ) ) {

    /**
     * Show the video title in the video loop. By default this is an H2.
     */
    function masvideos_template_loop_video_title() {
        echo '<h2 class="masvideos-loop-video__title">' . get_the_title() . '</h2>';
    }
}

if ( ! function_exists( 'masvideos_template_loop_movie_title' ) ) {

    /**
     * Show the movie title in the movie loop. By default this is an H2.
     */
    function masvideos_template_loop_movie_title() {
        echo '<h2 class="masvideos-loop-movie__title">' . get_the_title() . '</h2>';
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

if ( ! function_exists( 'masvideos_template_loop_video_link_open' ) ) {
    /**
     * Insert the opening anchor tag for videos in the loop.
     */
    function masvideos_template_loop_video_link_open() {
        global $video;

        $link = apply_filters( 'masvideos_loop_video_link', get_the_permalink(), $video );

        echo '<a href="' . esc_url( $link ) . '" class="masvideos-LoopVideo-link masvideos-loop-video__link">';
    }
}

if ( ! function_exists( 'masvideos_template_loop_movie_link_open' ) ) {
    /**
     * Insert the opening anchor tag for movies in the loop.
     */
    function masvideos_template_loop_movie_link_open() {
        global $movie;

        $link = apply_filters( 'masvideos_loop_movie_link', get_the_permalink(), $movie );

        echo '<a href="' . esc_url( $link ) . '" class="masvideos-LoopMovie-link masvideos-loop-movie__link">';
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

if ( ! function_exists( 'masvideos_template_loop_movie_link_close' ) ) {
    /**
     * Insert the opening anchor tag for movies in the loop.
     */
    function masvideos_template_loop_movie_link_close() {
        echo '</a>';
    }
}

if ( ! function_exists( 'masvideos_template_loop_category_link_open' ) ) {
    /**
     * Insert the opening anchor tag for categories in the loop.
     *
     * @param int|object|string $category Category ID, Object or String.
     * @param string Taxonomy Name.
     */
    function masvideos_template_loop_category_link_open( $category, $taxonomy = 'video_cat' ) {
        echo '<a href="' . esc_url( get_term_link( $category, $taxonomy ) ) . '">';
    }
}

if ( ! function_exists( 'masvideos_template_loop_category_link_close' ) ) {
    /**
     * Insert the closing anchor tag for categories in the loop.
     */
    function masvideos_template_loop_category_link_close() {
        echo '</a>';
    }
}


/**
 * Single
 */
if ( ! function_exists( 'masvideos_template_single_video_video' ) ) {

    /**
     * Output the video title.
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



if ( ! function_exists( 'masvideos_template_single_movie_title' ) ) {

    /**
     * Output the movie title.
     */
    function masvideos_template_single_movie_title() {
        the_title( '<h1 class="movie_title entry-title">', '</h1>' );
    }
}