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
        'columns'      => masvideos_get_default_movies_per_row(),
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

    return apply_filters( 'masvideos_movie_columns', max( 1, $columns ) );
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

    return apply_filters( 'masvideos_movie_rows', $rows );
}

/**
 * Display the classes for the movie div.
 *
 * @since 1.0.0
 * @param string|array           $class      One or more classes to add to the class list.
 * @param int|WP_Post|MasVideos_Movies_Query $movie_id Movie ID or movie object.
 */
function masvideos_movie_class( $class = '', $movie_id = null ) {
    // echo 'class="' . esc_attr( join( ' ', masvideos_movie_get_movie_class( $class, $movie_id ) ) ) . '"';
    post_class();
}

/**
 * Search Form
 */
if ( ! function_exists( 'masvideos_get_movie_search_form' ) ) {

    /**
     * Display movie search form.
     *
     * Will first attempt to locate the movie-searchform.php file in either the child or.
     * the parent, then load it. If it doesn't exist, then the default search form.
     * will be displayed.
     *
     * The default searchform uses html5.
     *
     * @param bool $echo (default: true).
     * @return string
     */
    function masvideos_get_movie_search_form( $echo = true ) {
        global $movie_search_form_index;

        ob_start();

        if ( empty( $movie_search_form_index ) ) {
            $movie_search_form_index = 0;
        }

        do_action( 'pre_masvideos_get_movie_search_form' );

        masvideos_get_template( 'search-form.php', array(
            'index' => $movie_search_form_index++,
            'post_type' => 'movie',
        ) );

        $form = apply_filters( 'masvideos_get_movie_search_form', ob_get_clean() );

        if ( ! $echo ) {
            return $form;
        }

        echo $form; // WPCS: XSS ok.
    }
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

if ( ! function_exists( 'masvideos_no_movies_found' ) ) {

    /**
     * Handles the loop when no movies were found/no movie exist.
     */
    function masvideos_no_movies_found() {
        ?><p class="masvideos-info"><?php _e( 'No movies were found matching your selection.', 'masvideos' ); ?></p><?php
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

            if ( empty( $page_title ) ) {
                $page_title = post_type_archive_title( '', false );
            }

        }

        $page_title = apply_filters( 'masvideos_movie_page_title', $page_title );

        if ( $echo ) {
            echo $page_title; // WPCS: XSS ok.
        } else {
            return $page_title;
        }
    }
}

if ( ! function_exists( 'masvideos_display_movie_page_title' ) ) {
    /**
     * Outputs Mas Movies Title
     */
    function masvideos_display_movie_page_title() {

        if ( apply_filters( 'masvideos_display_movie_page_title', true ) ) {
            ?>
            <header class="page-header">
                <h1 class="page-title"><?php masvideos_movie_page_title(); ?></h1>
            </header>
            <?php
        }
    }
}

if ( ! function_exists( 'masvideos_movies_control_bar' ) ) {
    /**
     * Display Control Bar.
     */
    function masvideos_movies_control_bar() {
        echo '<div class="masvideos-control-bar masvideos-movies-control-bar">';
            masvideos_movies_count();
            masvideos_movies_catalog_ordering();
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_movies_catalog_ordering' ) ) {
    function masvideos_movies_catalog_ordering() {
        if ( ! masvideos_get_movies_loop_prop( 'is_paginated' ) || ! masvideos_movies_will_display() ) {
            return;
        }

        $catalog_orderby_options = apply_filters( 'masvideos_default_movies_catalog_orderby_options', array(
            'title-asc'     => esc_html__( 'Name: Ascending', 'masvideos' ),
            'title-desc'    => esc_html__( 'Name: Descending', 'masvideos' ),
            'release_date'  => esc_html__( 'Latest', 'masvideos' ),
            'menu_order'    => esc_html__( 'Menu Order', 'masvideos' ),
            'rating'        => esc_html__( 'Rating', 'masvideos' ),
        ) );

        $default_orderby = masvideos_get_movies_loop_prop( 'is_search' ) ? 'relevance' : apply_filters( 'masvideos_default_movies_catalog_orderby', get_option( 'masvideos_default_movies_catalog_orderby', 'release_date' ) );
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
            <?php masvideos_query_string_form_fields( null, array( 'orderby', 'submit', 'paged', 'movie-page' ) ); ?>
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
     * movie body open in the movie loop.
     */
    function masvideos_template_loop_movie_body_open() {
        echo '<div class="movie__body">';
    }
}

if ( ! function_exists( 'masvideos_template_loop_movie_info_open' ) ) {

    /**
     * movie info open in the movie loop.
     */
    function masvideos_template_loop_movie_info_open() {
        echo '<div class="movie__info">';
    }
}

if ( ! function_exists( 'masvideos_template_loop_movie_info_head_open' ) ) {

    /**
     * movie info body open in the movie loop.
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
     * movie short description in the movie loop.
     */
    function masvideos_template_loop_movie_short_desc() {
        global $post;

        $short_description = apply_filters( 'masvideos_template_loop_movie_short_desc', $post->post_excerpt );

        if ( ! $short_description ) {
            return;
        }

        ?>
        <div class="movie__short-description">
            <?php echo '<div>' . $short_description . '</div>'; ?>
        </div>

        <?php
    }
}

if ( ! function_exists( 'masvideos_template_loop_movie_info_head_close' ) ) {

    /**
     * movie info body close in the movie loop.
     */
    function masvideos_template_loop_movie_info_head_close() {
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_template_loop_movie_actions' ) ) {

    /**
     * movie actions in the movie loop.
     */
    function masvideos_template_loop_movie_actions() {
        global $movie;
        echo '<div class="movie__actions">';
            $link = apply_filters( 'masvideos_loop_movie_link', get_the_permalink(), $movie );
            $text = apply_filters( 'masvideos_loop_movie_action_button_text', esc_html__( 'Watch Now', 'masvideos' ), $movie );
            echo '<a href="' . esc_url( $link ) . '" class="movie-actions--link_watch">' . esc_html( $text ) . '</a>';
            masvideos_template_button_movie_playlist();
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_template_loop_movie_info_close' ) ) {

    /**
     * movie info close in the movie loop.
     */
    function masvideos_template_loop_movie_info_close() {
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_template_loop_movie_review_info_open' ) ) {

    /**
     * movie review info open in the movie loop.
     */
    function masvideos_template_loop_movie_review_info_open() {
        echo '<div class="movie__review-info">';
    }
}

if ( ! function_exists( 'masvideos_template_loop_movie_viewers_count' ) ) {

    /**
     * movie actions in the movie loop.
     */
    function masvideos_template_loop_movie_viewers_count() {
        echo '<div class="viewers-count"></div>';
    }
}

if ( ! function_exists( 'masvideos_template_loop_movie_review_info_close' ) ) {

    /**
     * movie review info close in the movie loop.
     */
    function masvideos_template_loop_movie_review_info_close() {
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_template_loop_movie_body_close' ) ) {

    /**
     * movie body close in the movie loop.
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

if ( ! function_exists( 'masvideos_template_single_movie_poster' ) ) {

    /**
     * Output the movie poster.
     */
    function masvideos_template_single_movie_poster() {
        echo masvideos_get_movie_thumbnail( 'masvideos_movie_large' );
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

if ( ! function_exists( 'masvideos_template_single_movie_tags' ) ) {

    /**
     * Movie tags in the movie single.
     */
    function masvideos_template_single_movie_tags() {
        global $movie;

        $tags = get_the_term_list( $movie->get_id(), 'movie_tag', '', ', ' );

        if( ! empty ( $tags ) ) {
            echo sprintf( '<span class="movie__tags">%s %s</span>', esc_html__( 'Tags:', 'masvideos' ), $tags );
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
     * Movie duration in the movie single.
     */
    function masvideos_template_single_movie_duration() {
        global $movie;
        
        $duration = $movie->get_movie_run_time();

        if( ! empty ( $duration ) ) {
            echo sprintf( '<span class="movie__meta--duration">%s</span>', $duration );
        }
    }
}

if ( ! function_exists( 'masvideos_template_single_movie_avg_rating' ) ) {

    /**
     * Single movie average rating
     */
    function masvideos_template_single_movie_avg_rating() {
        global $movie;

        ?><div class="movie__avg-rating">
        <?php if ( ! empty( $movie->get_review_count() ) && $movie->get_review_count() > 0 ) { ?>
            <a href="<?php echo esc_url( get_permalink( $movie->get_id() ) . '#reviews' ); ?>" class="avg-rating">
                <div class="avg-rating__inner">
                    <span class="avg-rating__number"> <?php echo number_format( $movie->get_average_rating(), 1, '.', '' ); ?></span>
                    <span class="avg-rating__text">
                        <?php echo wp_kses_post( sprintf( _n( '<span>%s</span> Vote', '<span>%s</span> votes', $movie->get_review_count(), 'masvideos' ), $movie->get_review_count() ) ) ; ?>
                    </span>
                </div>
            </a>
        <?php } ?>
        </div><?php
    }
}

if ( ! function_exists( 'masvideos_related_movies' ) ) {

    /**
     * Output the related movies.
     *
     * @param array $args Provided arguments.
     */
    function masvideos_related_movies( $movie_id = false, $args = array() ) {
        global $movie;

        $movie_id = $movie_id ? $movie_id : $movie->get_id();

        if ( ! $movie_id ) {
            return;
        }

        $defaults = apply_filters( 'masvideos_related_movies_default_args', array(
            'limit'          => 6,
            'columns'        => 6,
            'orderby'        => 'rand',
            'order'          => 'desc',
        ) );

        $args = wp_parse_args( $args, $defaults );

        $title = apply_filters( 'masvideos_related_movies_title', esc_html__( 'You Also May Like', 'masvideos' ), $movie_id );

        $related_movie_ids = masvideos_get_related_movies( $movie_id, $args['limit'] );
        $args['ids'] = implode( ',', $related_movie_ids );

        if( ! empty( $related_movie_ids ) ) {
            echo '<section class="movie__related">';
                echo '<div class="movie__related--inner">';
                    echo sprintf( '<h2 class="movie__related--title">%s</h2>', $title );
                    echo MasVideos_Shortcodes::movies( $args );
                echo '</div>';
            echo '</section>';
        }
    }
}

if ( ! function_exists( 'masvideos_recommended_movies' ) ) {

    /**
     * Output the recommended movies.
     *
     * @param array $args Provided arguments.
     */
    function masvideos_recommended_movies( $movie_id = false, $args = array() ) {
        global $movie;

        $movie_id = $movie_id ? $movie_id : $movie->get_id();

        if ( ! $movie_id ) {
            return;
        }

        $defaults = apply_filters( 'masvideos_recommended_movies_default_args', array(
            'limit'          => 4,
            'columns'        => 4,
            'orderby'        => 'post__in',
            'order'          => 'asc',
        ) );

        $args = wp_parse_args( $args, $defaults );

        $title = apply_filters( 'masvideos_recommended_movies_title', esc_html__( 'We Recommend', 'masvideos' ), $movie_id );

        $recommended_movie_ids = $movie->get_recommended_movie_ids();
        $args['ids'] = implode( ',', $recommended_movie_ids );

        if( ! empty( $recommended_movie_ids ) ) {
            echo '<section class="movie__recommended">';
                echo sprintf( '<h2 class="movie__recommended--title">%s</h2>', $title );
                echo MasVideos_Shortcodes::movies( $args );
            echo '</section>';
        }
    }
}

if ( ! function_exists( 'masvideos_movie_related_videos' ) ) {

    /**
     * Output the related videos of a movie.
     *
     * @param array $args Provided arguments.
     */
    function masvideos_movie_related_videos( $movie_id = false, $args = array() ) {
        global $movie;

        $movie_id = $movie_id ? $movie_id : $movie->get_id();

        if ( ! $movie_id ) {
            return;
        }

        $defaults = apply_filters( 'masvideos_movie_related_videos_default_args', array(
            'limit'          => 4,
            'columns'        => 4,
            'orderby'        => 'post__in',
            'order'          => 'asc',
        ) );

        $args = wp_parse_args( $args, $defaults );

        $title = apply_filters( 'masvideos_movie_related_videos_title', esc_html__( 'Trailers & Clips', 'masvideos' ), $movie_id );

        $related_video_ids = $movie->get_related_video_ids();
        $args['ids'] = implode( ',', $related_video_ids );

        if( ! empty( $related_video_ids ) ) {
            echo '<section class="movie__related-video">';
            echo '<div class="movie__related-video--inner">';
                echo sprintf( '<h2 class="movie__related-video--title">%s</h2>', $title );
                echo MasVideos_Shortcodes::videos( $args );
            echo '</div>';
            echo '</section>';
        }
    }
}

if ( ! function_exists( 'masvideos_template_single_movie_tabs' ) ) {

    /**
     * Movie tabs in the movie single.
     */
    function masvideos_template_single_movie_tabs() {
        global $movie, $post;

        $tabs = array();
        
        // Description tab - shows movie content.
        if ( $post->post_content ) {
            $tabs['description'] = array(
                'title'     => esc_html__( 'Description', 'masvideos' ),
                'callback'  => 'masvideos_template_single_movie_description_tab',
                'priority'  => 10
            );
        }

        // Sources tab - shows link sources.
        if ( $movie && ( $movie->has_sources() ) ) {
            $tabs['sources'] = array(
                'title'     => esc_html__( 'Sources', 'masvideos' ),
                'callback'  => 'masvideos_template_single_movie_sources',
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

        $tabs = apply_filters( 'masvideos_template_single_movie_tabs', $tabs );

        if( ! empty( $tabs ) ) {
            masvideos_get_template( 'global/tabs.php', array( 'tabs' => $tabs, 'class' => 'movie-tabs' ) );
        }
    }
}

if ( ! function_exists( 'masvideos_template_single_movie_description_tab' ) ) {
    /**
     * Single movie description tab
     */
    function masvideos_template_single_movie_description_tab() {
        global $movie;
        echo '<div id="movie__description-tab" class="movie__description-tab">';
            do_action( 'masvideos_single_movie_description_tab', $movie );
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_template_single_movie_sources' ) ) {

    /**
     * Movie sources in the movie single.
     */
    function masvideos_template_single_movie_sources() {
        masvideos_get_template( 'single-movie/sources.php' );
    }
}

if ( ! function_exists( 'masvideos_display_movie_attributes' ) ) {
    /**
     * Outputs a list of movie attributes for a movie.
     *
     * @since  1.0.0
     * @param  Mas_Videos $movie Movie Object.
     */
    function masvideos_display_movie_attributes() {
        global $movie;
        masvideos_get_template( 'single-movie/movie-attributes.php', array(
            'movie'         => $movie,
            'attributes'    => array_filter( $movie->get_attributes(), 'masvideos_attributes_movie_array_filter_visible' ),
        ) );
    }
}

if ( ! function_exists( 'masvideos_template_button_movie_playlist' ) ) {
    /**
     * Button dropdown for Add/Remove movie to playlist.
     *
     * @since  1.0.0
     */
    function masvideos_template_button_movie_playlist() {
        global $movie;

        ?>
        <div class="movie-actions--link_add-to-playlist dropdown">
            <a class="dropdown-toggle" href="<?php echo get_permalink( $movie->get_id() ); ?>" data-toggle="dropdown"><?php echo esc_html__( '+ Playlist', 'masvideos' ) ?></a>
            <div class="dropdown-menu">
                <?php
                    $movie_playlists_page_url = masvideos_get_endpoint_url( 'movie-playlists', '', masvideos_get_page_permalink( 'myaccount' ) );
                    if ( is_user_logged_in() ) {
                        masvideos_template_button_toggle_user_movie_playlist( $movie->get_id() );
                        ?><a class="create-playlist-link" href="<?php echo esc_attr( $movie_playlists_page_url ); ?>"><?php echo esc_html__( 'Create a playlist', 'masvideos' ); ?></a><?php
                    } else {
                        ?><a class="login-link" href="<?php echo esc_attr( $movie_playlists_page_url ); ?>"><?php echo esc_html__( 'Sign in to add this movie to a playlist.', 'masvideos' ); ?></a><?php
                    }
                ?>
            </div>
        </div>
        <?php
    }
}

if ( ! function_exists( 'masvideos_template_single_movie_summary_open' ) ) {
    /**
     * Single movie summary open
     */
    function masvideos_template_single_movie_summary_open() {
        ?><div class="summary entry-summary"><?php
    }
}

if ( ! function_exists( 'masvideos_template_single_movie_summary_close' ) ) {
    /**
     * Single movie summary close
     */
    function masvideos_template_single_movie_summary_close() {
        ?></div><?php
    }
}

if ( ! function_exists( 'masvideos_template_single_movie_description' ) ) {
    /**
     * Single movie description
     */
    function masvideos_template_single_movie_description() {
        ?>
        <div class="movie__description">
            <div><?php the_content(); ?></div>
        </div>
        <?php
    }
}

if ( ! function_exists( 'masvideos_template_single_movie_short_desc' ) ) {
    /**
     * Single movie short description
     */
    function masvideos_template_single_movie_short_desc() {
        global $post;

        $short_description = apply_filters( 'masvideos_template_single_movie_short_desc', $post->post_excerpt );

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

if ( ! function_exists( 'masvideos_template_single_movie_head_wrap_open' ) ) {
    /**
     * Single movie head open
     */
    function masvideos_template_single_movie_head_wrap_open() {
        ?>
        <div class="movie__head">
        <?php
    }
}

if ( ! function_exists( 'masvideos_template_single_movie_head_wrap_close' ) ) {
    /**
     * Single movie head close
     */
    function masvideos_template_single_movie_head_wrap_close() {
        ?>
        </div>
        <?php
    }
}

if ( ! function_exists( 'masvideos_template_single_movie_player_wrap_open' ) ) {
    /**
     * Single movie player open
     */
    function masvideos_template_single_movie_player_wrap_open() {
        ?>
        <div class="movie__player">
        <?php
    }
}

if ( ! function_exists( 'masvideos_template_single_movie_player_wrap_close' ) ) {
    /**
     * Single movie player close
     */
    function masvideos_template_single_movie_player_wrap_close() {
        ?>
        </div>
        <?php
    }
}

if ( ! function_exists( 'masvideos_template_single_movie_play_source_link' ) ) {
    /**
     * Single movie play source link
     */
    function masvideos_template_single_movie_play_source_link( $source ) {
        $source_content = ( $source['choice'] == 'movie_url' ) ? $source['link'] : $source['embed_content'];

        if( isset( $source['is_affiliate'] ) && $source['is_affiliate'] && ! empty( $source_content ) ) {
            ?>
            <a href="<?php echo esc_url( $source_content ); ?>" class="play-source movie-affiliate-play-source" target="_blank">
                <span><?php echo apply_filters( 'masvideos_movie_play_source_text', esc_html__( 'Play Now', 'masvideos' ) ); ?></span>
            </a>
            <?php
        } else {
            $source_content = apply_filters( 'the_content', $source_content );
            ?>
            <a href="#" class="play-source movie-play-source" data-content="<?php echo esc_attr( htmlspecialchars( $source_content ) ); ?>">
                <span><?php echo apply_filters( 'masvideos_movie_play_source_text', esc_html__( 'Play Now', 'masvideos' ) ); ?></span>
            </a>
            <?php
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

if ( ! function_exists( 'masvideos_template_single_movie_rating_with_playlist_wrap_open' ) ) {
    function masvideos_template_single_movie_rating_with_playlist_wrap_open() {
        ?>
        <div class="movie__rating-with-playlist">
        <?php
    }
}

if ( ! function_exists( 'masvideos_template_single_movie_rating_with_playlist_wrap_close' ) ) {
    function masvideos_template_single_movie_rating_with_playlist_wrap_close() {
        ?>
        </div>
        <?php
    }
}

if ( ! function_exists( 'masvideos_template_single_movie_info_left_wrap_open' ) ) {
    function masvideos_template_single_movie_info_left_wrap_open() {
        ?>
        <div class="movie__info--left">
        <?php
    }
}

if ( ! function_exists( 'masvideos_template_single_movie_info_left_wrap_close' ) ) {
    function masvideos_template_single_movie_info_left_wrap_close() {
        ?>
        </div>
        <?php
    }
}


if ( ! function_exists( 'masvideos_template_single_movie_info_right_wrap_open' ) ) {
    function masvideos_template_single_movie_info_right_wrap_open() {
        ?>
        <div class="movie__info--right">
        <?php
    }
}

if ( ! function_exists( 'masvideos_template_single_movie_info_right_wrap_close' ) ) {
    function masvideos_template_single_movie_info_right_wrap_close() {
        ?>
        </div>
        <?php
    }
}

if ( ! function_exists( 'masvideos_template_single_movie_cast_crew_tabs' ) ) {

    /**
     * Movie cast and crew tabs in the movie single.
     */
    function masvideos_template_single_movie_cast_crew_tabs() {
        global $movie;

        $tabs = array();

        // Cast tab - shows movie content.
        if ( $movie && ! empty( $movie->get_cast() ) ) {
            $tabs['cast'] = array(
                'title'     => esc_html__( 'Cast', 'masvideos' ),
                'callback'  => 'masvideos_template_single_movie_cast_tab',
                'priority'  => 10
            );
        }

        // Crew tab - shows movie content.
        if ( $movie && ! empty( $movie->get_crew() ) ) {
            $tabs['crew'] = array(
                'title'     => esc_html__( 'Crew', 'masvideos' ),
                'callback'  => 'masvideos_template_single_movie_crew_tab',
                'priority'  => 20
            );
        }

        $tabs = apply_filters( 'masvideos_template_single_movie_cast_crew_tabs', $tabs );

        if( ! empty( $tabs ) ) {
            ?><h2 class="crew-casts-title"><?php echo apply_filters( 'masvideos_template_single_movie_cast_crew_section_title', esc_html__( 'Cast & Crew', 'masvideos' ) ); ?></h2><?php
            masvideos_get_template( 'global/tabs.php', array( 'tabs' => $tabs, 'class' => 'movie-cast-crew-tabs' ) );
        }
    }
}

if ( ! function_exists( 'masvideos_template_single_movie_cast_tab' ) ) {
    function masvideos_template_single_movie_cast_tab() {
        global $movie;
        $casts = $movie->get_cast();

        if( ! empty( $casts ) ) {
            ?>
            <div class="movie-casts">
                <?php
                foreach( $casts as $cast ) {
                    $person = masvideos_get_person( $cast['id'] );
                    if( $person && is_a( $person, 'MasVideos_Person' ) ) {
                        ?>
                        <div class="movie-cast">
                            <div class="person-image">
                                <a href="<?php the_permalink( $person->get_ID() ); ?>">
                                    <?php echo masvideos_get_person_thumbnail( 'masvideos_movie_thumbnail', $person ); ?>
                                </a>
                            </div>
                            <div class="movie-cast__person-info">
                                <a class="person-name-link" href="<?php the_permalink( $person->get_ID() ); ?>">
                                    <h3 class="person-name"><?php echo esc_html( $person->get_name() ); ?></h3>
                                </a>
                                <?php if( !empty( $cast['character'] )): ?>
                                    <div class="person-role">
                                        <?php echo esc_html( $cast['character'] ); ?>
                                    </div>
                                <?php endif; ?>
                                <?php do_action( 'masvideos_template_single_movie_cast_end', $cast ); ?>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
            <?php
        }
    }
}

if ( ! function_exists( 'masvideos_template_single_movie_crew_tab' ) ) {
    function masvideos_template_single_movie_crew_tab() {
        global $movie;
        $crews = $movie->get_crew();
        $category_based_crews = array();

        if( ! empty( $crews ) ) {
            foreach( $crews as $crew ) {
                $person = masvideos_get_person( $crew['id'] );
                if( $person && is_a( $person, 'MasVideos_Person' ) ) {
                    $crew['person_name'] = $person->get_name();
                    $crew['person_url'] = get_the_permalink( $person->get_ID() );
                    $crew['person_image'] = masvideos_get_person_thumbnail( 'masvideos_movie_thumbnail', $person );
                    $category_based_crews[$crew['category']][] = $crew;
                }
            }

            foreach( $category_based_crews as $term_id => $crews ) {
                $term = get_term( $term_id );
                ?>
                <div class="movie-crews">
                    <h2 class="movie-crews-category-title"><?php echo esc_html( ! is_wp_error( $term ) ? $term->name : __( 'Unknown', 'masvideos' ) ); ?></h2>
                    <?php foreach( $crews as $crew ): ?>
                        <div class="movie-crew">
                            <div class="person-image">
                                <a href="<?php echo esc_url( $crew['person_url'] ); ?>">
                                    <?php echo wp_kses_post( $crew['person_image'] ); ?>
                                </a>
                            </div>
                            <div class="movie-crew__person-info">
                                <a class="person-name-link" href="<?php echo esc_url( $crew['person_url'] ); ?>">
                                    <h3 class="person-name"><?php echo esc_html( $crew['person_name'] ); ?></h3>
                                </a>
                                <?php if( !empty( $crew['job'] )): ?>
                                    <div class="person-role">
                                        <?php echo esc_html( $crew['job'] ); ?>
                                    </div>
                                <?php endif; ?>
                                <?php do_action( 'masvideos_template_single_movie_crew_end', $crew ); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php
            }
        }
    }
}

if ( ! function_exists( 'masvideos_template_single_movie_gallery' ) ) {
    function masvideos_template_single_movie_gallery() {
        global $movie;

        $columns           = apply_filters( 'masvideos_movie_gallery_thumbnails_columns', 8 );
        $attachment_ids    = $movie->get_gallery_image_ids();
        $wrapper_classes   = apply_filters( 'masvideos_single_movie_image_gallery_classes', array(
            'masvideos-movie-gallery',
            'masvideos-movie-gallery--' . ( $movie->get_image_id() ? 'with-images' : 'without-images' ),
            'masvideos-movie-gallery--columns-' . absint( $columns ),
            'images',
        ) );
        $title = apply_filters( 'masvideos_template_single_movie_gallery_title', esc_html__( 'Gallery', 'masvideos' ));

        if ( $attachment_ids && $movie->get_image_id() ) {
            ?>
            <div class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) ); ?>" data-columns="<?php echo esc_attr( $columns ); ?>">
                <?php echo sprintf( '<h2 class="masvideos-movie-gallery__title">%s</h2>', $title ); ?>
                <div class="masvideos-movie-gallery__inner">
                    <?php
                    foreach ( $attachment_ids as $attachment_id ) {
                        echo apply_filters( 'masvideos_single_movie_image_thumbnail_html', masvideos_get_gallery_image_html( $attachment_id ), $attachment_id ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
                    }
                ?>
                </div>
            </div>
            <?php
        }
    }
}
