<?php
/**
 * MasVideos Person Template
 *
 * Functions for the templating system.
 *
 * @package  MasVideos\Functions
 * @version  1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * When the_post is called, put person data into a global.
 *
 * @param mixed $post Post Object.
 * @return MasVideos_Person
 */
function masvideos_setup_person_data( $post ) {
    unset( $GLOBALS['person'] );

    if ( is_int( $post ) ) {
        $the_post = get_post( $post );
    } else {
        $the_post = $post;
    }

    if ( empty( $the_post->post_type ) || ! in_array( $the_post->post_type, array( 'person' ), true ) ) {
        return;
    }

    $GLOBALS['person'] = masvideos_get_person( $the_post );

    return $GLOBALS['person'];
}
add_action( 'the_post', 'masvideos_setup_person_data' );

/**
 * Sets up the masvideos_persons_loop global from the passed args or from the main query.
 *
 * @since 1.0.0
 * @param array $args Args to pass into the global.
 */
function masvideos_setup_persons_loop( $args = array() ) {
    $default_args = array(
        'loop'         => 0,
        'columns'      => masvideos_get_default_persons_per_row(),
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
    if ( $GLOBALS['wp_query']->get( 'masvideos_person_query' ) ) {
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
    if ( isset( $GLOBALS['masvideos_persons_loop'] ) ) {
        $default_args = array_merge( $default_args, $GLOBALS['masvideos_persons_loop'] );
    }

    $GLOBALS['masvideos_persons_loop'] = wp_parse_args( $args, $default_args );
}
add_action( 'masvideos_before_persons_loop', 'masvideos_setup_persons_loop' );

/**
 * Resets the masvideos_persons_loop global.
 *
 * @since 1.0.0
 */
function masvideos_reset_persons_loop() {
    unset( $GLOBALS['masvideos_persons_loop'] );
}
add_action( 'masvideos_after_persons_loop', 'masvideos_reset_persons_loop', 999 );

/**
 * Gets a property from the masvideos_persons_loop global.
 *
 * @since 1.0.0
 * @param string $prop Prop to get.
 * @param string $default Default if the prop does not exist.
 * @return mixed
 */
function masvideos_get_persons_loop_prop( $prop, $default = '' ) {
    masvideos_setup_persons_loop(); // Ensure shop loop is setup.

    return isset( $GLOBALS['masvideos_persons_loop'], $GLOBALS['masvideos_persons_loop'][ $prop ] ) ? $GLOBALS['masvideos_persons_loop'][ $prop ] : $default;
}

/**
 * Sets a property in the masvideos_persons_loop global.
 *
 * @since 1.0.0
 * @param string $prop Prop to set.
 * @param string $value Value to set.
 */
function masvideos_set_persons_loop_prop( $prop, $value = '' ) {
    if ( ! isset( $GLOBALS['masvideos_persons_loop'] ) ) {
        masvideos_setup_persons_loop();
    }
    $GLOBALS['masvideos_persons_loop'][ $prop ] = $value;
}

/**
 * Check if we will be showing persons.
 *
 * @return bool
 */
function masvideos_persons_will_display() {
    return 0 < masvideos_get_persons_loop_prop( 'total', 0 );
}

/**
 * Should the MasVideos loop be displayed?
 *
 * This will return true if we have posts (persons) or if we have subcats to display.
 *
 * @since 3.4.0
 * @return bool
 */
function masvideos_persons_loop() {
    return have_posts();
}

/**
 * Get the default columns setting - this is how many persons will be shown per row in loops.
 *
 * @since 1.0.0
 * @return int
 */
function masvideos_get_default_persons_per_row() {
    $columns      = get_option( 'masvideos_person_columns', 4 );
    $person_grid   = masvideos_get_theme_support( 'person_grid' );
    $min_columns  = isset( $person_grid['min_columns'] ) ? absint( $person_grid['min_columns'] ) : 0;
    $max_columns  = isset( $person_grid['max_columns'] ) ? absint( $person_grid['max_columns'] ) : 0;

    if ( $min_columns && $columns < $min_columns ) {
        $columns = $min_columns;
        update_option( 'masvideos_person_columns', $columns );
    } elseif ( $max_columns && $columns > $max_columns ) {
        $columns = $max_columns;
        update_option( 'masvideos_person_columns', $columns );
    }

    $columns = absint( $columns );

    return apply_filters( 'masvideos_person_columns', max( 1, $columns ) );
}

/**
 * Get the default rows setting - this is how many person rows will be shown in loops.
 *
 * @since 1.0.0
 * @return int
 */
function masvideos_get_default_person_rows_per_page() {
    $rows         = absint( get_option( 'masvideos_person_rows', 4 ) );
    $person_grid   = masvideos_get_theme_support( 'person_grid' );
    $min_rows     = isset( $person_grid['min_rows'] ) ? absint( $person_grid['min_rows'] ) : 0;
    $max_rows     = isset( $person_grid['max_rows'] ) ? absint( $person_grid['max_rows'] ) : 0;

    if ( $min_rows && $rows < $min_rows ) {
        $rows = $min_rows;
        update_option( 'masvideos_person_rows', $rows );
    } elseif ( $max_rows && $rows > $max_rows ) {
        $rows = $max_rows;
        update_option( 'masvideos_person_rows', $rows );
    }

    return apply_filters( 'masvideos_person_rows', $rows );
}

/**
 * Display the classes for the person div.
 *
 * @since 1.0.0
 * @param string|array           $class      One or more classes to add to the class list.
 * @param int|WP_Post|MasVideos_Persons_Query $person_id Person ID or person object.
 */
function masvideos_person_class( $class = '', $person_id = null ) {
    // echo 'class="' . esc_attr( join( ' ', wc_get_person_class( $class, $person_id ) ) ) . '"';
    post_class();
}

/**
 * Search Form
 */
if ( ! function_exists( 'masvideos_get_person_search_form' ) ) {

    /**
     * Display person search form.
     *
     * Will first attempt to locate the person-searchform.php file in either the child or.
     * the parent, then load it. If it doesn't exist, then the default search form.
     * will be displayed.
     *
     * The default searchform uses html5.
     *
     * @param bool $echo (default: true).
     * @return string
     */
    function masvideos_get_person_search_form( $echo = true ) {
        global $person_search_form_index;

        ob_start();

        if ( empty( $person_search_form_index ) ) {
            $person_search_form_index = 0;
        }

        do_action( 'pre_masvideos_get_person_search_form' );

        masvideos_get_template( 'search-form.php', array(
            'index' => $person_search_form_index++,
            'post_type' => 'person',
        ) );

        $form = apply_filters( 'masvideos_get_person_search_form', ob_get_clean() );

        if ( ! $echo ) {
            return $form;
        }

        echo $form; // WPCS: XSS ok.
    }
}

/**
 * Loop
 */

if ( ! function_exists( 'masvideos_person_loop_start' ) ) {

    /**
     * Output the start of a person loop. By default this is a UL.
     *
     * @param bool $echo Should echo?.
     * @return string
     */
    function masvideos_person_loop_start( $echo = true ) {
        ob_start();

        masvideos_set_persons_loop_prop( 'loop', 0 );

        ?><div class="persons columns-<?php echo esc_attr( masvideos_get_persons_loop_prop( 'columns' ) ); ?>"><div class="persons__inner"><?php

        $loop_start = apply_filters( 'masvideos_person_loop_start', ob_get_clean() );

        if ( $echo ) {
            echo $loop_start; // WPCS: XSS ok.
        } else {
            return $loop_start;
        }
    }
}

if ( ! function_exists( 'masvideos_persons_loop_content' ) ) {

    /*
     * Output the person loop. By default this is a UL.
     */
    function masvideos_persons_loop_content() {
        masvideos_get_template_part( 'content', 'person' );
    }
}

if ( ! function_exists( 'masvideos_no_persons_found' ) ) {

    /**
     * Handles the loop when no persons were found/no person exist.
     */
    function masvideos_no_persons_found() {
        ?><p class="masvideos-info"><?php _e( 'No persons were found matching your selection.', 'masvideos' ); ?></p><?php
    }
}

if ( ! function_exists( 'masvideos_person_loop_end' ) ) {

    /**
     * Output the end of a person loop. By default this is a UL.
     *
     * @param bool $echo Should echo?.
     * @return string
     */
    function masvideos_person_loop_end( $echo = true ) {
        ob_start();

        ?></div></div><?php

        $loop_end = apply_filters( 'masvideos_person_loop_end', ob_get_clean() );

        if ( $echo ) {
            echo $loop_end; // WPCS: XSS ok.
        } else {
            return $loop_end;
        }
    }
}

if ( ! function_exists( 'masvideos_person_page_title' ) ) {

    /**
     * Page Title function.
     *
     * @param  bool $echo Should echo title.
     * @return string
     */
    function masvideos_person_page_title( $echo = true ) {

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

            $persons_page_id = masvideos_get_page_id( 'persons' );
            $page_title   = get_the_title( $persons_page_id );

            if ( empty( $page_title ) ) {
                $page_title = post_type_archive_title( '', false );
            }

        }

        $page_title = apply_filters( 'masvideos_person_page_title', $page_title );

        if ( $echo ) {
            echo $page_title; // WPCS: XSS ok.
        } else {
            return $page_title;
        }
    }
}

if ( ! function_exists( 'masvideos_display_person_page_title' ) ) {
    /**
     * Outputs Persons Page Title
     */
    function masvideos_display_person_page_title() {

        if ( apply_filters( 'masvideos_display_person_page_title', true ) ) {
            ?>
            <header class="page-header">
                <h1 class="page-title"><?php masvideos_person_page_title(); ?></h1>
            </header>
            <?php
        }
    }
}

if ( ! function_exists( 'masvideos_persons_control_bar' ) ) {
    /**
     * Display Control Bar.
     */
    function masvideos_persons_control_bar() {
        echo '<div class="masvideos-control-bar masvideos-persons-control-bar">';
            masvideos_persons_count();
            masvideos_persons_catalog_ordering();
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_persons_catalog_ordering' ) ) {
    function masvideos_persons_catalog_ordering() {
        if ( ! masvideos_get_persons_loop_prop( 'is_paginated' ) || ! masvideos_persons_will_display() ) {
            return;
        }

        $catalog_orderby_options = apply_filters( 'masvideos_default_persons_catalog_orderby_options', array(
            'title-asc'     => esc_html__( 'Name: Ascending', 'masvideos' ),
            'title-desc'    => esc_html__( 'Name: Descending', 'masvideos' ),
            'date'          => esc_html__( 'Latest', 'masvideos' ),
            'menu_order'    => esc_html__( 'Menu Order', 'masvideos' ),
        ) );

        $default_orderby = masvideos_get_persons_loop_prop( 'is_search' ) ? 'relevance' : apply_filters( 'masvideos_default_persons_catalog_orderby', get_option( 'masvideos_default_persons_catalog_orderby', 'date' ) );
        $orderby         = isset( $_GET['orderby'] ) ? masvideos_clean( wp_unslash( $_GET['orderby'] ) ) : $default_orderby; // WPCS: sanitization ok, input var ok, CSRF ok.

        if ( masvideos_get_persons_loop_prop( 'is_search' ) ) {
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
            <?php masvideos_query_string_form_fields( null, array( 'orderby', 'submit', 'paged', 'person-page' ) ); ?>
        </form>
        <?php
    }
}

if ( ! function_exists( 'masvideos_persons_page_control_bar' ) ) {
    /**
     * Display Page Control Bar.
     */
    function masvideos_persons_page_control_bar() {
        echo '<div class="masvideos-page-control-bar masvideos-persons-page-control-bar">';
            masvideos_persons_count();
            masvideos_persons_pagination();
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_persons_count' ) ) {

    /**
     * Output the result count text (Showing x - x of x results).
     */
    function masvideos_persons_count() {
        if ( ! masvideos_get_persons_loop_prop( 'is_paginated' ) || ! masvideos_persons_will_display() ) {
            return;
        }
        $args = array(
            'total'    => masvideos_get_persons_loop_prop( 'total' ),
            'per_page' => masvideos_get_persons_loop_prop( 'per_page' ),
            'current'  => masvideos_get_persons_loop_prop( 'current_page' ),
        );

        ?>
        <p class="masvideos-result-count masvideos-persons-result-count">
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

if ( ! function_exists( 'masvideos_persons_pagination' ) ) {
    /**
     * Display Pagination.
     */
    function masvideos_persons_pagination() {
        if ( ! masvideos_get_persons_loop_prop( 'is_paginated' ) || ! masvideos_persons_will_display() ) {
            return;
        }

        $args = array(
            'total'   => masvideos_get_persons_loop_prop( 'total_pages' ),
            'current' => masvideos_get_persons_loop_prop( 'current_page' ),
            'base'    => esc_url_raw( add_query_arg( 'person-page', '%#%', false ) ),
            'format'  => '?person-page=%#%',
        );

        if ( ! masvideos_get_persons_loop_prop( 'is_shortcode' ) ) {
            $args['format'] = '';
            $args['base']   = esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) );
        }

        if (  $args['total'] <= 1 ) {
            return;
        }
        ?>

        <nav class="masvideos-pagination masvideos-persons-pagination">
            <?php
                echo paginate_links( apply_filters( 'masvideos_persons_pagination_args', array( // WPCS: XSS ok.
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

if ( ! function_exists( 'masvideos_template_loop_person_link_open' ) ) {
    /**
     * Insert the opening anchor tag for person in the loop.
     */
    function masvideos_template_loop_person_link_open() {
        global $person;

        $link = apply_filters( 'masvideos_loop_person_link', get_the_permalink(), $person );

        echo '<a href="' . esc_url( $link ) . '" class="masvideos-LoopPerson-link masvideos-loop-person__link person__link">';
    }
}

if ( ! function_exists( 'masvideos_template_loop_person_link_close' ) ) {
    /**
     * Insert the opening anchor tag for person in the loop.
     */
    function masvideos_template_loop_person_link_close() {
        echo '</a>';
    }
}

if ( ! function_exists( 'masvideos_template_loop_person_poster_open' ) ) {
    /**
     * person poster open in the loop.
     */
    function masvideos_template_loop_person_poster_open() {
        echo '<div class="person__poster">';
    }
}

if ( ! function_exists( 'masvideos_template_loop_person_poster' ) ) {
    /**
     * person poster in the loop.
     */
    function masvideos_template_loop_person_poster() {
        echo masvideos_get_person_thumbnail( 'masvideos_person_medium' );
    }
}

if ( ! function_exists( 'masvideos_template_loop_person_poster_close' ) ) {
    /**
     * person poster close in the loop.
     */
    function masvideos_template_loop_person_poster_close() {
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_template_loop_person_body_open' ) ) {

    /**
     * person body open in the person loop.
     */
    function masvideos_template_loop_person_body_open() {
        echo '<div class="person__body">';
    }
}

if ( ! function_exists( 'masvideos_template_loop_person_body_close' ) ) {

    /**
     * person body close in the person loop.
     */
    function masvideos_template_loop_person_body_close() {
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_template_loop_person_title' ) ) {

    /**
     * Show the person title in the person loop. By default this is an H3.
     */
    function masvideos_template_loop_person_title() {
        the_title( '<h3 class="masvideos-loop-person__title  person__title">', '</h3>' );
    }
}

if ( ! function_exists( 'masvideos_template_single_person_content_sidebar_open' ) ) {
    /**
     * person sidebar open in the single person.
     */
    function masvideos_template_single_person_content_sidebar_open() {
        echo '<div class="single-person__content-sidebar">';
    }
}

if ( ! function_exists( 'masvideos_template_single_person_content_sidebar' ) ) {
    /**
     * person sidebar do action in the single person.
     */
    function masvideos_template_single_person_content_sidebar() {
        do_action( 'masvideos_template_single_person_content_sidebar' );
    }
}

if ( ! function_exists( 'masvideos_template_single_person_content_sidebar_close' ) ) {
    /**
     * person sidebar close in the single person.
     */
    function masvideos_template_single_person_content_sidebar_close() {
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_template_single_person_content_body_open' ) ) {
    /**
     * person sidebar open in the single person.
     */
    function masvideos_template_single_person_content_body_open() {
        echo '<div class="single-person__content-body">';
    }
}

if ( ! function_exists( 'masvideos_template_single_person_title' ) ) {
    /**
     * Show the person title in the single person. By default this is an H1.
     */
    function masvideos_template_single_person_title() {
        the_title( '<h1 class="masvideos-single-person__title  single-person___title">', '</h1>' );
    }
}

if ( ! function_exists( 'masvideos_template_single_person_short_desc' ) ) {
    /**
     * Show the person short description in the single person. By default this is an H1.
     */
    function masvideos_template_single_person_short_desc() {
        global $post;

        $short_description = apply_filters( 'masvideos_template_single_person_short_desc', $post->post_excerpt );

        if ( ! $short_description ) {
            return;
        }

        ?>
        <div class="single-person__short-description">
            <?php echo '<div>' . $short_description . '</div>'; ?>
        </div>
        <?php
    }
}

if ( ! function_exists( 'masvideos_template_single_person_credits_tabs' ) ) {

    /**
     * Movie cast and crew tabs in the movie single.
     */
    function masvideos_template_single_person_credits_tabs() {
        global $person;

        $tabs = array();

        // Movies tab - shows person content.
        if ( $person && ( ! empty( $person->get_movie_cast() ) || ! empty( $person->get_movie_crew() ) ) ) {
            $tabs['cast'] = array(
                'title'     => esc_html__( 'Movies', 'masvideos' ),
                'callback'  => 'masvideos_template_single_person_movies_tab',
                'priority'  => 10
            );
        }

        // TV Shows tab - shows person content.
        if ( $person && ( ! empty( $person->get_tv_show_cast() ) || ! empty( $person->get_tv_show_crew() ) ) ) {
            $tabs['crew'] = array(
                'title'     => esc_html__( 'TV Shows', 'masvideos' ),
                'callback'  => 'masvideos_template_single_person_tv_shows_tab',
                'priority'  => 20
            );
        }

        $tabs = apply_filters( 'masvideos_template_single_person_credits_tabs', $tabs );

        if( ! empty( $tabs ) ) {
            masvideos_get_template( 'global/tabs.php', array( 'tabs' => $tabs, 'class' => 'person-credits-tabs' ) );
        }
    }
}

if ( ! function_exists( 'masvideos_template_single_person_movies_tab' ) ) {
    function masvideos_template_single_person_movies_tab() {
        global $person;
        $cast_movie_ids = $person->get_movie_cast();
        $crew_movie_ids = $person->get_movie_crew();

        if( ! empty( $cast_movie_ids ) || ! empty( $crew_movie_ids ) ) {
            ?><div class="person-movies-credits"><?php
                if( ! empty( $cast_movie_ids ) ) {
                    ?>
                    <div class="person-cast-movies">
                        <h2 class="movie-cast-title">
                            <?php echo apply_filters( 'masvideos_template_single_movie_movies_cast_title_text',  esc_html__( 'Acting', 'masvideos' ) ); ?>
                        </h2>
                        <?php
                        foreach( $cast_movie_ids as $cast_movie_id ) {
                            $movie = masvideos_get_movie( $cast_movie_id );
                            if( $movie && is_a( $movie, 'MasVideos_Movie' ) ) {
                                $release_date = $movie->get_movie_release_date();
                                $movie_cast = $movie->get_cast(); 
                                $found_key = array_search( $person->get_ID(), array_column( $movie_cast, 'id' ) );
                                ?>
                                <div class="person-cast-movie">
                                    <div class="movie-release-year">
                                        <?php if( ! empty( $release_date ) ) : ?>
                                            <?php echo date( 'Y', strtotime( $release_date ) ); ?>
                                        <?php else : ?>
                                            -
                                        <?php endif; ?>
                                    </div>
                                    <a href="<?php the_permalink( $movie->get_ID() ); ?>">
                                        <h3 class="movie-name"><?php echo esc_html( $movie->get_name() ); ?></h3>
                                    </a>
                                    <?php if( $found_key !== false ) : ?>
                                        <span class="person-role-separator"> - </span>
                                        <span class="person-role">
                                            <?php echo esc_html( $movie_cast[$found_key]['character'] ); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                    <?php
                }

                if( ! empty( $crew_movie_ids ) ) {
                    $category_based_movies = array();

                    foreach( $crew_movie_ids as $crew_movie_id ) {
                        $movie = masvideos_get_movie( $crew_movie_id );
                        if( $movie && is_a( $movie, 'MasVideos_Movie' ) ) {
                            $movie_crew = $movie->get_crew();
                            $found_keys = array_keys( array_column( $movie_crew, 'id' ), $person->get_ID() );

                            foreach( $found_keys as $found_key ) {
                                if( $found_key !== false ) {
                                    $category_based_movies[$movie_crew[$found_key]['category']][] = array(
                                        'movie_id'      => $movie->get_ID(),
                                        'movie_name'    => $movie->get_name(),
                                        'release_year'  => ! empty( $movie->get_movie_release_date() ) ? date( 'Y', strtotime( $movie->get_movie_release_date() ) ) : '-',
                                        'job'           => $movie_crew[$found_key]['job'],
                                    );
                                }
                            }
                        }
                    }

                    foreach( $category_based_movies as $term_id => $movies ) {
                        $term = get_term( $term_id );
                        array_multisort( array_column( $movies, 'release_year' ), SORT_DESC, $movies );
                        ?>
                        <div class="person-crew-movies">
                            <h2 class="person-crews-movies-category-title"><?php echo esc_html( ! is_wp_error( $term ) ? $term->name : __( 'Unknown', 'masvideos' ) ); ?></h2>
                            <?php foreach( $movies as $movie ): ?>
                                <div class="person-crew-movie">
                                    <div class="movie-release-year">
                                        <?php echo esc_html( $movie['release_year'] ); ?>
                                    </div>
                                    <a href="<?php the_permalink( $movie['movie_id'] ); ?>">
                                        <h3 class="movie-name"><?php echo esc_html( $movie['movie_name'] ); ?></h3>
                                    </a>
                                    <?php if( $found_key !== false ) : ?>
                                        <span class="person-role-separator"> - </span>
                                        <span class="person-role">
                                            <?php echo esc_html( $movie['job'] ); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php
                    }
                }
            ?></div><?php
        }
    }
}

if ( ! function_exists( 'masvideos_template_single_person_tv_shows_tab' ) ) {
    function masvideos_template_single_person_tv_shows_tab() {
        global $person;
        $cast_tv_show_ids = $person->get_tv_show_cast();
        $crew_tv_show_ids = $person->get_tv_show_crew();

        if( ! empty( $cast_tv_show_ids ) || ! empty( $crew_tv_show_ids ) ) {
            ?><div class="person-tv-shows-credits"><?php
                if( ! empty( $cast_tv_show_ids ) ) {
                    ?>
                    <div class="person-cast-tv-shows">
                        <h2 class="tv-show-cast-title">
                            <?php echo apply_filters( 'masvideos_template_single_tv_show_tv_shows_cast_title_text',  esc_html__( 'Acting', 'masvideos' ) ); ?>
                        </h2>
                        <?php
                        foreach( $cast_tv_show_ids as $cast_tv_show_id ) {
                            $tv_show = masvideos_get_tv_show( $cast_tv_show_id );
                            if( $tv_show && is_a( $tv_show, 'MasVideos_TV_Show' ) ) {
                                $release_date = $tv_show->get_tv_show_release_date();
                                $tv_show_cast = $tv_show->get_cast(); 
                                $found_key = array_search( $person->get_ID(), array_column( $tv_show_cast, 'id' ) );
                                $seasons = $tv_show->get_seasons();

                                if( ! empty( $seasons ) ) {
                                    $season_years = array_column( $seasons, 'year' );
                                    $start = count( $season_years ) ? min( $season_years ) : '';
                                    $end = count( $season_years ) ? max( $season_years ) : '';

                                    if( ! empty( $start ) && ! empty( $end ) ) {
                                        $tv_show_year = $start . ' - ' . $end;
                                    } elseif( ! empty( $start ) ) {
                                        $tv_show_year = $start;
                                    } elseif( ! empty( $end ) ) {
                                        $tv_show_year = $end;
                                    }
                                }

                                ?>
                                <div class="person-cast-tv-show">
                                    <div class="tv-show-release-year">
                                        <?php echo ( ! empty( $tv_show_year ) ? esc_html( $tv_show_year ) : '-' ); ?>
                                    </div>
                                    <a href="<?php the_permalink( $tv_show->get_ID() ); ?>">
                                        <h3 class="tv-show-name"><?php echo esc_html( $tv_show->get_name() ); ?></h3>
                                    </a>
                                    <?php if( $found_key !== false ) : ?>
                                        <span class="person-role-separator"> - </span>
                                        <span class="person-role">
                                            <?php echo esc_html( $tv_show_cast[$found_key]['character'] ); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                    <?php
                }

                if( ! empty( $crew_tv_show_ids ) ) {
                    $category_based_tv_shows = array();

                    foreach( $crew_tv_show_ids as $crew_tv_show_id ) {
                        $tv_show = masvideos_get_tv_show( $crew_tv_show_id );
                        if( $tv_show && is_a( $tv_show, 'MasVideos_TV_Show' ) ) {
                            $tv_show_crew = $tv_show->get_crew();
                            $found_keys = array_keys( array_column( $tv_show_crew, 'id' ), $person->get_ID() );
                            $seasons = $tv_show->get_seasons();

                            if( ! empty( $seasons ) ) {
                                $season_years = array_column( $seasons, 'year' );
                                $start = count( $season_years ) ? min( $season_years ) : '';
                                $end = count( $season_years ) ? max( $season_years ) : '';

                                if( ! empty( $start ) && ! empty( $end ) ) {
                                    $tv_show_year = $start . ' - ' . $end;
                                } elseif( ! empty( $start ) ) {
                                    $tv_show_year = $start;
                                } elseif( ! empty( $end ) ) {
                                    $tv_show_year = $end;
                                }
                            }

                            foreach( $found_keys as $found_key ) {
                                if( $found_key !== false ) {
                                    $category_based_tv_shows[$tv_show_crew[$found_key]['category']][] = array(
                                        'tv_show_id'    => $tv_show->get_ID(),
                                        'tv_show_name'  => $tv_show->get_name(),
                                        'release_year'  => $tv_show_year,
                                        'job'           => $tv_show_crew[$found_key]['job'],
                                    );
                                }
                            }
                        }
                    }

                    foreach( $category_based_tv_shows as $term_id => $tv_shows ) {
                        $term = get_term( $term_id );
                        array_multisort( array_column( $tv_shows, 'release_year' ), SORT_DESC, $tv_shows );
                        ?>
                        <div class="person-crew-tv-shows">
                            <h2 class="person-crews-tv-shows-category-title"><?php echo esc_html( ! is_wp_error( $term ) ? $term->name : __( 'Unknown', 'masvideos' ) ); ?></h2>
                            <?php foreach( $tv_shows as $tv_show ): ?>
                                <div class="person-crew-tv-show">
                                    <div class="tv-show-release-year">
                                        <?php echo ( ! empty( $tv_show['release_date'] ) ? esc_html( $tv_show['release_date'] ) : '-' ); ?>
                                    </div>
                                    <a href="<?php the_permalink( $tv_show['tv_show_id'] ); ?>">
                                        <h3 class="tv-show-name"><?php echo esc_html( $tv_show['tv_show_name'] ); ?></h3>
                                    </a>
                                    <?php if( $found_key !== false ) : ?>
                                        <span class="person-role-separator"> - </span>
                                        <span class="person-role">
                                            <?php echo esc_html( $tv_show['job'] ); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php
                    }
                }
            ?></div><?php
        }
    }
}

if ( ! function_exists( 'masvideos_template_single_person_description' ) ) {
    /**
     * Show the person description in the single person. By default this is an H1.
     */
    function masvideos_template_single_person_description() {
        ?>
        <div class="single-person__description">
            <div><?php the_content(); ?></div>
        </div>
        <?php
    }
}

if ( ! function_exists( 'masvideos_template_single_person_content_body_close' ) ) {
    /**
     * Person sidebar close in the single person.
     */
    function masvideos_template_single_person_content_body_close() {
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_template_single_person_poster' ) ) {
    /**
     * Person poster in the single person.
     */
    function masvideos_template_single_person_poster() {
        ?>
        <div class="single-person__poster">
            <?php masvideos_template_loop_person_poster(); ?>
        </div>
        <?php
    }
}

if ( ! function_exists( 'masvideos_template_single_person_categories' ) ) {
    /**
     * Person categories in the single person.
     */
    function masvideos_template_single_person_categories() {
        global $post;
        $terms = get_the_terms( $post, 'person_cat' );
        if( ! $terms || is_wp_error( $terms ) )
            return;

        $term_names = wp_list_pluck( $terms, 'name' );

        if( ! empty( $term_names ) ) :
            $title = apply_filters( 'masvideos_template_single_person_categories_title_text', __( 'Known For', 'masvideos' ) );
            ?>
            <div class="single-person__categories">
                <h4 class="single-person__sidebar-title category-title"><?php echo esc_html( $title ); ?></h4>
                <?php echo wp_kses_post( implode( ', ', $term_names ) ); ?>
            </div>
            <?php
        endif;
    }
}

if ( ! function_exists( 'masvideos_template_single_person_credits' ) ) {
    /**
     * Person credits in the single person.
     */
    function masvideos_template_single_person_credits() {
        global $person;
        $movie_cast = $person->get_movie_cast() ? $person->get_movie_cast() : array();
        $movie_crew = $person->get_movie_crew() ? $person->get_movie_crew() : array();
        $tv_show_cast = $person->get_tv_show_cast() ? $person->get_tv_show_cast() : array();
        $tv_show_crew = $person->get_tv_show_crew() ? $person->get_tv_show_crew() : array();
        $credits = array_unique( array_merge ( $movie_cast, $movie_crew, $tv_show_cast, $tv_show_crew ) );
        if( count( $credits ) > 0 ) :
            $title = apply_filters( 'masvideos_template_single_person_credits_title_text', __( 'Known Credits', 'masvideos' ) );
            ?>
            <div class="single-person__credits">
                <h4 class="single-person__sidebar-title credits-title"><?php echo esc_html( $title ); ?></h4>
                <?php echo wp_kses_post( count( $credits ) ); ?>
            </div>
            <?php
        endif;
    }
}

if ( ! function_exists( 'masvideos_template_single_person_birthday' ) ) {
    /**
     * Person birthday in the single person.
     */
    function masvideos_template_single_person_birthday() {
        global $person;
        $birthday = $person->get_birthday();
        if( ! empty( $birthday ) ) :
            $date = $birthday->getOffsetTimestamp();
            $title = apply_filters( 'masvideos_template_single_person_birthday_title_text', __( 'Birthday', 'masvideos' ) );
            $format = apply_filters( 'masvideos_template_single_person_birthday_format', 'd-m-Y' );
            ?>
            <div class="single-person__birthday">
                <h4 class="single-person__sidebar-title birthday-title"><?php echo esc_html( $title ); ?></h4>
                <?php echo date_i18n( $format, $date ); ?>
            </div>
            <?php
        endif;
    }
}

if ( ! function_exists( 'masvideos_template_single_person_birth_place' ) ) {
    /**
     * Person birth place in the single person.
     */
    function masvideos_template_single_person_birth_place() {
        global $person;
        $birth_place = $person->get_place_of_birth();
        if( ! empty( $birth_place ) ) :
            $title = apply_filters( 'masvideos_template_single_person_birth_place_title_text', __( 'Place of Birth', 'masvideos' ) );
            ?>
            <div class="single-person__birth-place">
                <h4 class="single-person__sidebar-title birth-place-title"><?php echo esc_html( $title ); ?></h4>
                <?php echo wp_kses_post( $birth_place ); ?>
            </div>
            <?php
        endif;
    }
}

if ( ! function_exists( 'masvideos_template_single_person_also_known_as' ) ) {
    /**
     * Person birth place in the single person.
     */
    function masvideos_template_single_person_also_known_as() {
        global $person;
        $also_known_as = $person->get_also_known_as();
        if( ! empty( $also_known_as ) ) :
            $title = apply_filters( 'masvideos_template_single_person_also_known_as_title_text', __( 'Also Known As', 'masvideos' ) );
            ?>
            <div class="single-person__other-names">
                <h4 class="single-person__sidebar-title other-names-title"><?php echo esc_html( $title ); ?></h4>
                <?php echo wp_kses_post( $also_known_as ); ?>
            </div>
            <?php
        endif;
    }
}

if ( ! function_exists( 'masvideos_template_single_person_tabs' ) ) {

    /**
     * Movie tabs in the person single.
     */
    function masvideos_template_single_person_tabs() {
        global $person, $post;

        $tabs = array();
        
        // Description tab - shows person content.
        if ( $post->post_content ) {
            $tabs['description'] = array(
                'title'     => esc_html__( 'Description', 'masvideos' ),
                'callback'  => 'masvideos_template_single_person_description',
                'priority'  => 10
            );
        }

        // Additional information tab - shows attributes.
        if ( $person->has_attributes() ) {
            $tabs['additional_information'] = array(
                'title'     => esc_html__( 'Additional information', 'masvideos' ),
                'callback'  => 'masvideos_display_person_attributes',
                'priority'  => 20
            );
        }

        $tabs = apply_filters( 'masvideos_template_single_person_tabs', $tabs );

        if( ! empty( $tabs ) ) {
            masvideos_get_template( 'global/tabs.php', array( 'tabs' => $tabs, 'class' => 'person-tabs' ) );
        }
    }
}

if ( ! function_exists( 'masvideos_display_person_attributes' ) ) {
    /**
     * Outputs a list of person attributes for a person.
     *
     * @since  1.0.0
     * @param  Mas_Videos $person Person Object.
     */
    function masvideos_display_person_attributes() {
        global $person;
        masvideos_get_template( 'single-person/person-attributes.php', array(
            'person'        => $person,
            'attributes'    => array_filter( $person->get_attributes(), 'masvideos_attributes_person_array_filter_visible' ),
        ) );
    }
}