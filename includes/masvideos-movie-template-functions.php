<?php
/**
 * MasVideos Movie Template
 *
 * Functions for the templating system.
 *
 * @package  MasVideos\Functions
 * @version  1.0.0
 */

defined( 'ABSPATH' ) || exit;

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
add_action( 'the_post', 'masvideos_setup_movie_data' );

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
 * Resets the masvideos_movies_loop global.
 *
 * @since 1.0.0
 */
function masvideos_reset_movies_loop() {
    unset( $GLOBALS['masvideos_movies_loop'] );
}
add_action( 'masvideos_after_movies_loop', 'masvideos_reset_movies_loop', 999 );

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
 * Check if we will be showing movies.
 *
 * @return bool
 */
function masvideos_movies_will_display() {
    return 0 < masvideos_get_movies_loop_prop( 'total', 0 );
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
 * Loop
 */

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

if ( ! function_exists( 'masvideos_movies_loop_content' ) ) {
    /*
     * Output the movie loop. By default this is a UL.
     */
    function masvideos_movies_loop_content() {
        masvideos_get_template_part( 'content', 'movie' );
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

if ( ! function_exists( 'masvideos_movies_control_bar' ) ) {
    /**
     * Display Control Bar.
     */
    function masvideos_movies_control_bar() {
        echo '<div class="masvideos-control-bar masvideos-movies-control-bar">';
            masviseos_movies_per_page();
            masvideos_movies_catalog_ordering();
        echo '</div>';
    }
}

if ( ! function_exists( 'masviseos_movies_per_page' ) ) {
    /**
     * Outputs a dropdown for user to select how many movies to show per page
     */
    function masviseos_movies_per_page() {

        global $wp_query;

        $action             = '';
        $cat                = '';
        $cat                = $wp_query->get_queried_object();
        $method             = apply_filters( 'masviseos_movies_mpp_method', 'post' );
        $return_to_first    = apply_filters( 'masviseos_movies_mpp_return_to_first', false );
        $total              = $wp_query->found_posts;
        $per_page           = $wp_query->get( 'posts_per_page' );
        $_per_page          = 2;

        // Generate per page options
        $movies_per_page_options = array();
        while( $_per_page < $total ) {
            $movies_per_page_options[] = $_per_page;
            $_per_page = $_per_page * 2;
        }

        if ( empty( $movies_per_page_options ) ) {
            return;
        }

        $movies_per_page_options[] = -1;

        // Set action url if option behaviour is true
        // Paste QUERY string after for filter and orderby support
        $query_string = ! empty( $_SERVER['QUERY_STRING'] ) ? '?' . add_query_arg( array( 'mpp' => false ), $_SERVER['QUERY_STRING'] ) : null;

        if ( isset( $cat->term_id ) && isset( $cat->taxonomy ) && $return_to_first ) :
            $action = get_term_link( $cat->term_id, $cat->taxonomy ) . $query_string;
        elseif ( $return_to_first ) :
            $action = get_permalink( masviseos_get_page_id( 'movies' ) ) . $query_string;
        endif;

        // Only show on movie categories
        if ( ! masvideos_movies_will_display() ) :
            return;
        endif;

        do_action( 'masviseos_mpp_before_dropdown_form' );

        ?><form method="POST" action="<?php echo esc_url( $action ); ?>" class="form-masviseos-mpp"><?php

             do_action( 'masviseos_mpp_before_dropdown' );

            ?><select name="mpp" onchange="this.form.submit()" class="masviseos-mmpp-select c-select"><?php

                foreach( $movies_per_page_options as $key => $value ) :

                    ?><option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $per_page ); ?>><?php
                        $mpp_text = apply_filters( 'masviseos_mpp_text', __( 'Show %s', 'masvideos' ), $value );
                        esc_html( printf( $mpp_text, $value == -1 ? __( 'All', 'masvideos' ) : $value ) ); // Set to 'All' when value is -1
                    ?></option><?php

                endforeach;

            ?></select><?php

            // Keep query string vars intact
            foreach ( $_GET as $key => $val ) :

                if ( 'mpp' === $key || 'submit' === $key ) :
                    continue;
                endif;
                if ( is_array( $val ) ) :
                    foreach( $val as $inner_val ) :
                        ?><input type="hidden" name="<?php echo esc_attr( $key ); ?>[]" value="<?php echo esc_attr( $inner_val ); ?>" /><?php
                    endforeach;
                else :
                    ?><input type="hidden" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $val ); ?>" /><?php
                endif;
            endforeach;

            do_action( 'masviseos_mpp_after_dropdown' );

        ?></form><?php

        do_action( 'masviseos_mpp_after_dropdown_form' );
    }
}

if ( ! function_exists( 'masvideos_movies_catalog_ordering' ) ) {
    function masvideos_movies_catalog_ordering() {
        if ( ! masvideos_get_movies_loop_prop( 'is_paginated' ) || ! masvideos_movies_will_display() ) {
            return;
        }

        $catalog_orderby_options = apply_filters( 'masvideos_movies_catalog_orderby', array(
            'title-asc'  => esc_html__( 'Name: Ascending', 'masvideos' ),
            'title-desc' => esc_html__( 'Name: Descending', 'masvideos' ),
            'date'       => esc_html__( 'Latest', 'masvideos' ),
            'menu_order' => esc_html__( 'Menu Order', 'masvideos' ),
            'rating'     => esc_html__( 'Rating', 'masvideos' ),
        ) );

        $default_orderby = masvideos_get_movies_loop_prop( 'is_search' ) ? 'relevance' : apply_filters( 'masvideos_movies_default_catalog_orderby', 'date' );
        $orderby         = isset( $_GET['orderby'] ) ? masvideos_clean( wp_unslash( $_GET['orderby'] ) ) : $default_orderby; // WPCS: sanitization ok, input var ok, CSRF ok.

        if ( masvideos_get_movies_loop_prop( 'is_search' ) ) {
            $catalog_orderby_options = array_merge( array( 'relevance' => esc_html__( 'Relevance', 'masvideos' ) ), $catalog_orderby_options );

            unset( $catalog_orderby_options['menu_order'] );
        }

        if ( ! array_key_exists( $orderby, $catalog_orderby_options ) ) {
            $orderby = current( array_keys( $catalog_orderby_options ) );
        }

        ?>
        <form method="get">
            <select name="orderby" class="orderby" onchange="this.form.submit();">
                <?php foreach ( $catalog_orderby_options as $id => $name ) : ?>
                    <option value="<?php echo esc_attr( $id ); ?>" <?php selected( $orderby, $id ); ?>><?php echo esc_html( $name ); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" name="paged" value="1" />
        </form>
        <?php
    }
}

if ( ! function_exists( 'masvideos_movies_page_control_bar' ) ) {
    /**
     * Display Page Control Bar.
     */
    function masvideos_movies_page_control_bar() {
        echo '<div class="masvideos-page-control-bar masvideos-movies-page-control-bar">';
            masvideos_movies_count();
            masvideos_movies_pagination();
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_movies_count' ) ) {

    /**
     * Output the result count text (Showing x - x of x results).
     */
    function masvideos_movies_count() {
        if ( ! masvideos_get_movies_loop_prop( 'is_paginated' ) || ! masvideos_movies_will_display() ) {
            return;
        }
        $args = array(
            'total'    => masvideos_get_movies_loop_prop( 'total' ),
            'per_page' => masvideos_get_movies_loop_prop( 'per_page' ),
            'current'  => masvideos_get_movies_loop_prop( 'current_page' ),
        );

        ?>
        <p class="masvideos-result-count masvideos-movies-result-count">
            <?php
            if ( $args['total'] <= $args['per_page'] || -1 === $args['per_page'] ) {
                /* translators: %d: total results */
                printf( _n( 'Showing the single result', 'Showing all %d results', $args['total'], 'masvideos' ), $args['total'] );
            } else {
                $first = ( $args['per_page'] * $args['current'] ) - $args['per_page'] + 1;
                $last  = min( $args['total'], $args['per_page'] * $args['current'] );
                /* translators: 1: first result 2: last result 3: total results */
                printf( _nx( 'Showing the single result', 'Showing %1$d&ndash;%2$d of %3$d results', $args['total'], 'with first and last result', 'masvideos' ), $first, $last, $args['total'] );
            }
            ?>
        </p>
        <?php
    }
}

if ( ! function_exists( 'masvideos_movies_pagination' ) ) {
    /**
     * Display Pagination.
     */
    function masvideos_movies_pagination() {
        if ( ! masvideos_get_movies_loop_prop( 'is_paginated' ) || ! masvideos_movies_will_display() ) {
            return;
        }

        $args = array(
            'total'   => masvideos_get_movies_loop_prop( 'total_pages' ),
            'current' => masvideos_get_movies_loop_prop( 'current_page' ),
            'base'    => esc_url_raw( add_query_arg( 'movie-page', '%#%', false ) ),
            'format'  => '?movie-page=%#%',
        );

        if ( ! masvideos_get_movies_loop_prop( 'is_shortcode' ) ) {
            $args['format'] = '';
            $args['base']   = esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) );
        }

        if (  $args['total'] <= 1 ) {
            return;
        }
        ?>

        <nav class="masvideos-pagination masvideos-movies-pagination">
            <?php
                echo paginate_links( apply_filters( 'masvideos_movies_pagination_args', array( // WPCS: XSS ok.
                    'base'         => $args['base'],
                    'format'       => $args['format'],
                    'add_args'     => false,
                    'current'      => max( 1, $args['current'] ),
                    'total'        => $args['total'],
                    'prev_text'    => '&larr;',
                    'next_text'    => '&rarr;',
                    'type'         => 'list',
                    'end_size'     => 3,
                    'mid_size'     => 3,
                ) ) );
            ?>
        </nav>
        <?php
    }
}

if ( ! function_exists( 'masvideos_template_loop_movie_feature_badge' ) ) {
    /**
     * movies container open in the loop.
     */
    function masvideos_template_loop_movie_feature_badge() {
        global $movie;

        if ( $movie->get_featured() ) {
            echo '<span class="movie__badge">';
            echo '<span class="movie__badge--featured">' . esc_html__( apply_filters( 'masvideos_template_loop_movie_feature_badge_text', 'Featured' ), 'masvideos' ) . '</span>';
            echo '</span>';
        }
    }
}

if ( ! function_exists( 'masvideos_template_loop_movie_poster_open' ) ) {
    /**
     * movies poster open in the loop.
     */
    function masvideos_template_loop_movie_poster_open() {
        echo '<div class="movie__poster">';
    }
}

if ( ! function_exists( 'masvideos_template_loop_movie_link_open' ) ) {
    /**
     * Insert the opening anchor tag for movies in the loop.
     */
    function masvideos_template_loop_movie_link_open() {
        global $movie;

        $link = apply_filters( 'masvideos_loop_movie_link', get_the_permalink(), $movie );

        echo '<a href="' . esc_url( $link ) . '" class="masvideos-LoopMovie-link masvideos-loop-movie__link movie__link">';
    }
}

if ( ! function_exists( 'masvideos_template_loop_movie_poster' ) ) {
    /**
     * movies poster in the loop.
     */
    function masvideos_template_loop_movie_poster() {
        echo masvideos_get_movie_thumbnail( 'masvideos_movie_medium' );
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

if ( ! function_exists( 'masvideos_template_loop_movie_poster_close' ) ) {
    /**
     * movies poster close in the loop.
     */
    function masvideos_template_loop_movie_poster_close() {
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_template_loop_movie_body_open' ) ) {

    /**
     * video body open in the video loop.
     */
    function masvideos_template_loop_movie_body_open() {
        echo '<div class="movie__body">';
    }
}

if ( ! function_exists( 'masvideos_template_loop_movie_info_open' ) ) {

    /**
     * video info open in the video loop.
     */
    function masvideos_template_loop_movie_info_open() {
        echo '<div class="movie__info">';
    }
}

if ( ! function_exists( 'masvideos_template_loop_movie_info_head_open' ) ) {

    /**
     * video info body open in the video loop.
     */
    function masvideos_template_loop_movie_info_head_open() {
        echo '<div class="movie__info--head">';
    }
}

if ( ! function_exists( 'masvideos_template_loop_movie_title' ) ) {

    /**
     * Show the movie title in the movie loop. By default this is an H3.
     */
    function masvideos_template_loop_movie_title() {
        the_title( '<h3 class="masvideos-loop-movie__title  movie__title">', '</h3>' );
    }
}

if ( ! function_exists( 'masvideos_template_loop_movie_short_desc' ) ) {

    /**
     * video short description in the video loop.
     */
    function masvideos_template_loop_movie_short_desc() {
        global $post;

        $short_description = apply_filters( 'masvideos_template_loop_movie_short_desc', $post->post_excerpt );

        if ( ! $short_description ) {
            return;
        }

        ?>
        <div class="movie__short-description">
            <?php echo '<p>' . $short_description . '</p>'; ?>
        </div>

        <?php
    }
}

if ( ! function_exists( 'masvideos_template_loop_movie_info_head_close' ) ) {

    /**
     * video info body close in the video loop.
     */
    function masvideos_template_loop_movie_info_head_close() {
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_template_loop_movie_actions' ) ) {

    /**
     * video actions in the video loop.
     */
    function masvideos_template_loop_movie_actions() {
        global $movie;
        echo '<div class="movie__actions">';
            echo '<a href="' . esc_url( get_permalink( $movie ) ) . '" class="movie-actions--link_watch">' . esc_html__( 'Watch Now', 'masvideos' ) . '</a>';
            echo '<a href="#" class="movie-actions--link_add-to-playlist">' . esc_html__( '+ Playlist', 'masvideos' ) . '</a>';
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_template_loop_movie_info_close' ) ) {

    /**
     * video info close in the video loop.
     */
    function masvideos_template_loop_movie_info_close() {
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_template_loop_movie_review_info_open' ) ) {

    /**
     * video review info open in the video loop.
     */
    function masvideos_template_loop_movie_review_info_open() {
        echo '<div class="movie__review-info">';
    }
}

if ( ! function_exists( 'masvideos_template_loop_movie_avg_rating' ) ) {

    /**
     * movie avg rating in the movie loop.
     */
    function masvideos_template_loop_movie_avg_rating() {
        global $movie;

        if ( ! empty( $movie->get_review_count() ) && $movie->get_review_count() > 0 ) {
            ?>
            <a href="<?php echo esc_url( get_permalink( $movie->get_id() ) . '#reviews' ); ?>" class="avg-rating">
                <span class="avg-rating-number"> <?php echo number_format( $movie->get_average_rating(), 1, '.', '' ); ?></span>
                <span class="avg-rating-text">
                    <?php echo wp_kses_post( sprintf( _n( '<span>%s</span> Vote', '<span>%s</span> Votes', $movie->get_review_count(), 'masvideos' ), $movie->get_review_count() ) ) ; ?>
                </span>
            </a>
            <?php
        }
    }
}

if ( ! function_exists( 'masvideos_template_loop_movie_viewers_count' ) ) {

    /**
     * video actions in the video loop.
     */
    function masvideos_template_loop_movie_viewers_count() {
        echo '<div class="viewers-count"></div>';
    }
}

if ( ! function_exists( 'masvideos_template_loop_movie_review_info_close' ) ) {

    /**
     * video review info close in the video loop.
     */
    function masvideos_template_loop_movie_review_info_close() {
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_template_loop_movie_body_close' ) ) {

    /**
     * video body close in the video loop.
     */
    function masvideos_template_loop_movie_body_close() {
        echo '</div>';
    }
}


/**
 * Single
 */

if ( ! function_exists( 'masvideos_template_single_movie_movie' ) ) {

    /**
     * Output the movie.
     */
    function masvideos_template_single_movie_movie() {
        masvideos_the_movie();
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

if ( ! function_exists( 'masvideos_template_single_movie_meta' ) ) {

    /**
     * Movie meta in the movie single.
     */
    function masvideos_template_single_movie_meta() {
        echo '<div class="movie__meta">';
            do_action( 'masvideos_single_movie_meta' );
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_template_single_movie_genres' ) ) {

    /**
     * Movie genres in the movie single.
     */
    function masvideos_template_single_movie_genres() {
        global $movie;

        $categories = get_the_term_list( $movie->get_id(), 'movie_genre', '', ', ' );

        if( ! empty ( $categories ) ) {
           echo '<span class="movie__meta--genre">' . $categories . '</span>';
        }
    }
}

if ( ! function_exists( 'masvideos_template_single_movie_release_year' ) ) {

    /**
     * Movie release year in the movie single.
     */
    function masvideos_template_single_movie_release_year() {
        global $movie;
        
        $relaese_year = '';
        $release_date = $movie->get_movie_release_date();
        if( ! empty( $release_date ) ) {
            $relaese_year = date( 'Y', strtotime( $release_date ) );
        }

        if( ! empty ( $relaese_year ) ) {
            echo sprintf( '<span class="movie__meta--release-year">%s</span>', $relaese_year );
        }
    }
}

if ( ! function_exists( 'masvideos_template_single_movie_duration' ) ) {

    /**
     * Movie release year in the movie single.
     */
    function masvideos_template_single_movie_duration() {
        global $movie;
        
        $duration = $movie->get_movie_run_time();

        if( ! empty ( $duration ) ) {
            echo sprintf( '<span class="movie__meta--duration">%s</span>', $duration );
        }
    }
}

if ( ! function_exists( 'masvideos_movie_comments' ) ) {

    /**
     * Output the Review comments template.
     *
     * @param WP_Comment $comment Comment object.
     * @param array      $args Arguments.
     * @param int        $depth Depth.
     */
    function masvideos_movie_comments( $comment, $args, $depth ) {
        $GLOBALS['comment'] = $comment; // WPCS: override ok.
        masvideos_get_template( 'single-movie/review.php', array(
            'comment' => $comment,
            'args'    => $args,
            'depth'   => $depth,
        ) );
    }
}

if ( ! function_exists( 'masvideos_movie_review_display_gravatar' ) ) {
    /**
     * Display the review authors gravatar
     *
     * @param array $comment WP_Comment.
     * @return void
     */
    function masvideos_movie_review_display_gravatar( $comment ) {
        echo get_avatar( $comment, apply_filters( 'masvideos_movie_review_gravatar_size', '60' ), '' );
    }
}

if ( ! function_exists( 'masvideos_movie_review_display_rating' ) ) {
    /**
     * Display the reviewers star rating
     *
     * @return void
     */
    function masvideos_movie_review_display_rating() {
        if ( post_type_supports( 'movie', 'comments' ) ) {
            masvideos_get_template( 'single-movie/review-rating.php' );
        }
    }
}

if ( ! function_exists( 'masvideos_movie_review_display_meta' ) ) {
    /**
     * Display the review authors meta (name, verified owner, review date)
     *
     * @return void
     */
    function masvideos_movie_review_display_meta() {
        masvideos_get_template( 'single-movie/review-meta.php' );
    }
}

if ( ! function_exists( 'masvideos_movie_review_display_comment_text' ) ) {
    /**
     * Display the review content.
     */
    function masvideos_movie_review_display_comment_text() {
        echo '<div class="description">';
        comment_text();
        echo '</div>';
    }
}