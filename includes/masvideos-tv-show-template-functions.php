<?php
/**
 * MasVideos TV Show Template
 *
 * Functions for the templating system.
 *
 * @package  MasVideos\Functions
 * @version  1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * When the_post is called, put tv show data into a global.
 *
 * @param mixed $post Post Object.
 * @return MasVideos_Movie
 */
function masvideos_setup_tv_show_data( $post ) {
    unset( $GLOBALS['tv_show'] );

    if ( is_int( $post ) ) {
        $the_post = get_post( $post );
    } else {
        $the_post = $post;
    }

    if ( empty( $the_post->post_type ) || ! in_array( $the_post->post_type, array( 'tv_show' ), true ) ) {
        return;
    }

    $GLOBALS['tv_show'] = masvideos_get_tv_show( $the_post );

    return $GLOBALS['tv_show'];
}
add_action( 'the_post', 'masvideos_setup_tv_show_data' );

/**
 * Sets up the masvideos_tv_shows_loop global from the passed args or from the main query.
 *
 * @since 1.0.0
 * @param array $args Args to pass into the global.
 */
function masvideos_setup_tv_shows_loop( $args = array() ) {
    $default_args = array(
        'loop'         => 0,
        'columns'      => masvideos_get_default_tv_shows_per_row(),
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
    if ( $GLOBALS['wp_query']->get( 'masvideos_tv_show_query' ) ) {
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
    if ( isset( $GLOBALS['masvideos_tv_shows_loop'] ) ) {
        $default_args = array_merge( $default_args, $GLOBALS['masvideos_tv_shows_loop'] );
    }

    $GLOBALS['masvideos_tv_shows_loop'] = wp_parse_args( $args, $default_args );
}
add_action( 'masvideos_before_tv_shows_loop', 'masvideos_setup_tv_shows_loop' );

/**
 * Resets the masvideos_tv_shows_loop global.
 *
 * @since 1.0.0
 */
function masvideos_reset_tv_shows_loop() {
    unset( $GLOBALS['masvideos_tv_shows_loop'] );
}
add_action( 'masvideos_after_tv_shows_loop', 'masvideos_reset_tv_shows_loop', 999 );

/**
 * Gets a property from the masvideos_tv_shows_loop global.
 *
 * @since 1.0.0
 * @param string $prop Prop to get.
 * @param string $default Default if the prop does not exist.
 * @return mixed
 */
function masvideos_get_tv_shows_loop_prop( $prop, $default = '' ) {
    masvideos_setup_tv_shows_loop(); // Ensure shop loop is setup.

    return isset( $GLOBALS['masvideos_tv_shows_loop'], $GLOBALS['masvideos_tv_shows_loop'][ $prop ] ) ? $GLOBALS['masvideos_tv_shows_loop'][ $prop ] : $default;
}

/**
 * Sets a property in the masvideos_tv_shows_loop global.
 *
 * @since 1.0.0
 * @param string $prop Prop to set.
 * @param string $value Value to set.
 */
function masvideos_set_tv_shows_loop_prop( $prop, $value = '' ) {
    if ( ! isset( $GLOBALS['masvideos_tv_shows_loop'] ) ) {
        masvideos_setup_tv_shows_loop();
    }
    $GLOBALS['masvideos_tv_shows_loop'][ $prop ] = $value;
}

/**
 * Check if we will be showing tv shows.
 *
 * @return bool
 */
function masvideos_tv_shows_will_display() {
    return 0 < masvideos_get_tv_shows_loop_prop( 'total', 0 );
}

/**
 * Should the MasVideos loop be displayed?
 *
 * This will return true if we have posts (tv shows) or if we have subcats to display.
 *
 * @since 3.4.0
 * @return bool
 */
function masvideos_tv_shows_loop() {
    return have_posts();
}

/**
 * Get the default columns setting - this is how many tv shows will be shown per row in loops.
 *
 * @since 1.0.0
 * @return int
 */
function masvideos_get_default_tv_shows_per_row() {
    $columns      = get_option( 'masvideos_tv_show_columns', 4 );
    $tv_show_grid = masvideos_get_theme_support( 'tv_show_grid' );
    $min_columns  = isset( $tv_show_grid['min_columns'] ) ? absint( $tv_show_grid['min_columns'] ) : 0;
    $max_columns  = isset( $tv_show_grid['max_columns'] ) ? absint( $tv_show_grid['max_columns'] ) : 0;

    if ( $min_columns && $columns < $min_columns ) {
        $columns = $min_columns;
        update_option( 'masvideos_tv_show_columns', $columns );
    } elseif ( $max_columns && $columns > $max_columns ) {
        $columns = $max_columns;
        update_option( 'masvideos_tv_show_columns', $columns );
    }

    $columns = absint( $columns );

    return apply_filters( 'masvideos_tv_show_columns', max( 1, $columns ) );
}

/**
 * Get the default rows setting - this is how many tv show rows will be shown in loops.
 *
 * @since 1.0.0
 * @return int
 */
function masvideos_get_default_tv_show_rows_per_page() {
    $rows         = absint( get_option( 'masvideos_tv_show_rows', 4 ) );
    $tv_show_grid   = masvideos_get_theme_support( 'tv_show_grid' );
    $min_rows     = isset( $tv_show_grid['min_rows'] ) ? absint( $tv_show_grid['min_rows'] ) : 0;
    $max_rows     = isset( $tv_show_grid['max_rows'] ) ? absint( $tv_show_grid['max_rows'] ) : 0;

    if ( $min_rows && $rows < $min_rows ) {
        $rows = $min_rows;
        update_option( 'masvideos_tv_show_rows', $rows );
    } elseif ( $max_rows && $rows > $max_rows ) {
        $rows = $max_rows;
        update_option( 'masvideos_tv_show_rows', $rows );
    }

    return apply_filters( 'masvideos_tv_show_rows', $rows );
}

/**
 * Display the classes for the tv show div.
 *
 * @since 1.0.0
 * @param string|array           $class      One or more classes to add to the class list.
 * @param int|WP_Post|MasVideos_TV_Shows_Query $tv_show_id TV Show ID or tv show object.
 */
function masvideos_tv_show_class( $class = '', $tv_show_id = null ) {
    // echo 'class="' . esc_attr( join( ' ', wc_get_tv_show_class( $class, $tv_show_id ) ) ) . '"';
    $class .= "tv-show";
    post_class( $class );
}

/**
 * Search Form
 */
if ( ! function_exists( 'masvideos_get_tv_show_search_form' ) ) {

    /**
     * Display tv show search form.
     *
     * Will first attempt to locate the tv_show-searchform.php file in either the child or.
     * the parent, then load it. If it doesn't exist, then the default search form.
     * will be displayed.
     *
     * The default searchform uses html5.
     *
     * @param bool $echo (default: true).
     * @return string
     */
    function masvideos_get_tv_show_search_form( $echo = true ) {
        global $tv_show_search_form_index;

        ob_start();

        if ( empty( $tv_show_search_form_index ) ) {
            $tv_show_search_form_index = 0;
        }

        do_action( 'pre_masvideos_get_tv_show_search_form' );

        masvideos_get_template( 'search-form.php', array(
            'index' => $tv_show_search_form_index++,
            'post_type' => 'tv_show',
        ) );

        $form = apply_filters( 'masvideos_get_tv_show_search_form', ob_get_clean() );

        if ( ! $echo ) {
            return $form;
        }

        echo $form; // WPCS: XSS ok.
    }
}

/**
 * Loop
 */

if ( ! function_exists( 'masvideos_tv_show_loop_start' ) ) {

    /**
     * Output the start of a tv show loop. By default this is a UL.
     *
     * @param bool $echo Should echo?.
     * @return string
     */
    function masvideos_tv_show_loop_start( $echo = true ) {
        ob_start();

        masvideos_set_tv_shows_loop_prop( 'loop', 0 );

        ?><div class="tv-shows columns-<?php echo esc_attr( masvideos_get_tv_shows_loop_prop( 'columns' ) ); ?>"><div class="tv-shows__inner"><?php

        $loop_start = apply_filters( 'masvideos_tv_show_loop_start', ob_get_clean() );

        if ( $echo ) {
            echo $loop_start; // WPCS: XSS ok.
        } else {
            return $loop_start;
        }
    }
}

if ( ! function_exists( 'masvideos_tv_shows_loop_content' ) ) {

    /*
     * Output the tv show loop. By default this is a UL.
     */
    function masvideos_tv_shows_loop_content() {
        masvideos_get_template_part( 'content', 'tv-show' );
    }
}

if ( ! function_exists( 'masvideos_no_tv_shows_found' ) ) {

    /**
     * Handles the loop when no tv shows were found/no tv_show exist.
     */
    function masvideos_no_tv_shows_found() {
        ?><p class="masvideos-info"><?php _e( 'No tv shows were found matching your selection.', 'masvideos' ); ?></p><?php
    }
}

if ( ! function_exists( 'masvideos_tv_show_loop_end' ) ) {

    /**
     * Output the end of a tv show loop. By default this is a UL.
     *
     * @param bool $echo Should echo?.
     * @return string
     */
    function masvideos_tv_show_loop_end( $echo = true ) {
        ob_start();

        ?></div></div><?php

        $loop_end = apply_filters( 'masvideos_tv_show_loop_end', ob_get_clean() );

        if ( $echo ) {
            echo $loop_end; // WPCS: XSS ok.
        } else {
            return $loop_end;
        }
    }
}

if ( ! function_exists( 'masvideos_tv_show_page_title' ) ) {

    /**
     * Page Title function.
     *
     * @param  bool $echo Should echo title.
     * @return string
     */
    function masvideos_tv_show_page_title( $echo = true ) {

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

            $tv_shows_page_id = masvideos_get_page_id( 'tv_shows' );
            $page_title   = get_the_title( $tv_shows_page_id );

            if ( empty( $page_title ) ) {
                $page_title = post_type_archive_title( '', false );
            }

        }

        $page_title = apply_filters( 'masvideos_tv_show_page_title', $page_title );

        if ( $echo ) {
            echo $page_title; // WPCS: XSS ok.
        } else {
            return $page_title;
        }
    }
}

if ( ! function_exists( 'masvideos_tv_shows_control_bar' ) ) {
    /**
     * Display Control Bar.
     */
    function masvideos_tv_shows_control_bar() {
        echo '<div class="masvideos-control-bar masvideos-tv-shows-control-bar">';
            masviseos_tv_shows_per_page();
            masvideos_tv_shows_catalog_ordering();
        echo '</div>';
    }
}

if ( ! function_exists( 'masviseos_tv_shows_per_page' ) ) {
    /**
     * Outputs a dropdown for user to select how many tv shows to show per page
     */
    function masviseos_tv_shows_per_page() {

        global $wp_query;

        $action             = '';
        $cat                = '';
        $cat                = $wp_query->get_queried_object();
        $method             = apply_filters( 'masvideos_tv_shows_mpp_method', 'post' );
        $return_to_first    = apply_filters( 'masvideos_tv_shows_mpp_return_to_first', false );
        $total              = $wp_query->found_posts;
        $per_page           = $wp_query->get( 'posts_per_page' );
        $_per_page          = 2;

        // Generate per page options
        $tv_shows_per_page_options = array();
        while( $_per_page < $total ) {
            $tv_shows_per_page_options[] = $_per_page;
            $_per_page = $_per_page * 2;
        }

        if ( empty( $tv_shows_per_page_options ) ) {
            return;
        }

        $tv_shows_per_page_options[] = -1;

        // Set action url if option behaviour is true
        // Paste QUERY string after for filter and orderby support
        $query_string = ! empty( $_SERVER['QUERY_STRING'] ) ? '?' . add_query_arg( array( 'mpp' => false ), $_SERVER['QUERY_STRING'] ) : null;

        if ( isset( $cat->term_id ) && isset( $cat->taxonomy ) && $return_to_first ) :
            $action = get_term_link( $cat->term_id, $cat->taxonomy ) . $query_string;
        elseif ( $return_to_first ) :
            $action = get_permalink( masviseos_get_page_id( 'tv_shows' ) ) . $query_string;
        endif;

        // Only show on tv show categories
        if ( ! masvideos_tv_shows_will_display() ) :
            return;
        endif;

        do_action( 'masviseos_mpp_before_dropdown_form' );

        ?><form method="POST" action="<?php echo esc_url( $action ); ?>" class="form-masviseos-mpp"><?php

             do_action( 'masviseos_mpp_before_dropdown' );

            ?><select name="mpp" onchange="this.form.submit()" class="masviseos-mmpp-select c-select"><?php

                foreach( $tv_shows_per_page_options as $key => $value ) :

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

if ( ! function_exists( 'masvideos_tv_shows_catalog_ordering' ) ) {
    function masvideos_tv_shows_catalog_ordering() {
        if ( ! masvideos_get_tv_shows_loop_prop( 'is_paginated' ) || ! masvideos_tv_shows_will_display() ) {
            return;
        }

        $catalog_orderby_options = apply_filters( 'masvideos_tv_shows_catalog_orderby', array(
            'title-asc'  => esc_html__( 'Name: Ascending', 'masvideos' ),
            'title-desc' => esc_html__( 'Name: Descending', 'masvideos' ),
            'date'       => esc_html__( 'Latest', 'masvideos' ),
            'menu_order' => esc_html__( 'Menu Order', 'masvideos' ),
            'rating'     => esc_html__( 'Rating', 'masvideos' ),
        ) );

        $default_orderby = masvideos_get_tv_shows_loop_prop( 'is_search' ) ? 'relevance' : apply_filters( 'masvideos_tv_shows_default_catalog_orderby', 'date' );
        $orderby         = isset( $_GET['orderby'] ) ? masvideos_clean( wp_unslash( $_GET['orderby'] ) ) : $default_orderby; // WPCS: sanitization ok, input var ok, CSRF ok.

        if ( masvideos_get_tv_shows_loop_prop( 'is_search' ) ) {
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

if ( ! function_exists( 'masvideos_tv_shows_page_control_bar' ) ) {
    /**
     * Display Page Control Bar.
     */
    function masvideos_tv_shows_page_control_bar() {
        echo '<div class="masvideos-page-control-bar masvideos-tv-shows-page-control-bar">';
            masvideos_tv_shows_count();
            masvideos_tv_shows_pagination();
        echo '</div>';
    }
}


if ( ! function_exists( 'masvideos_tv_shows_count' ) ) {

    /**
     * Output the result count text (Showing x - x of x results).
     */
    function masvideos_tv_shows_count() {
        if ( ! masvideos_get_tv_shows_loop_prop( 'is_paginated' ) || ! masvideos_tv_shows_will_display() ) {
            return;
        }
        $args = array(
            'total'    => masvideos_get_tv_shows_loop_prop( 'total' ),
            'per_page' => masvideos_get_tv_shows_loop_prop( 'per_page' ),
            'current'  => masvideos_get_tv_shows_loop_prop( 'current_page' ),
        );

        ?>
        <p class="masvideos-result-count masvideos-tv-shows-result-count">
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

if ( ! function_exists( 'masvideos_tv_shows_pagination' ) ) {
    /**
     * Display Pagination.
     */
    function masvideos_tv_shows_pagination() {
        if ( ! masvideos_get_tv_shows_loop_prop( 'is_paginated' ) || ! masvideos_tv_shows_will_display() ) {
            return;
        }

        $args = array(
            'total'   => masvideos_get_tv_shows_loop_prop( 'total_pages' ),
            'current' => masvideos_get_tv_shows_loop_prop( 'current_page' ),
            'base'    => esc_url_raw( add_query_arg( 'tv-show-page', '%#%', false ) ),
            'format'  => '?tv-show-page=%#%',
        );

        if ( ! masvideos_get_tv_shows_loop_prop( 'is_shortcode' ) ) {
            $args['format'] = '';
            $args['base']   = esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) );
        }

        if (  $args['total'] <= 1 ) {
            return;
        }
        ?>

        <nav class="masvideos-pagination masvideos-tv-shows-pagination">
            <?php
                echo paginate_links( apply_filters( 'masvideos_tv_shows_pagination_args', array( // WPCS: XSS ok.
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


if ( ! function_exists( 'masvideos_template_loop_tv_show_feature_badge' ) ) {
    /**
     * tv show container open in the loop.
     */
    function masvideos_template_loop_tv_show_feature_badge() {
        global $tv_show;

        if ( $tv_show->get_featured() ) {
            echo '<span class="tv-show__badge">';
            echo '<span class="tv-show__badge--featured">' . esc_html__( apply_filters( 'masvideos_template_loop_tv_show_feature_badge_text', 'Featured' ), 'masvideos' ) . '</span>';
            echo '</span>';
        }
    }
}

if ( ! function_exists( 'masvideos_template_loop_tv_show_poster_open' ) ) {
    /**
     * tv show poster open in the loop.
     */
    function masvideos_template_loop_tv_show_poster_open() {
        echo '<div class="tv-show__poster">';
    }
}

if ( ! function_exists( 'masvideos_template_loop_tv_show_link_open' ) ) {
    /**
     * Insert the opening anchor tag for tv show in the loop.
     */
    function masvideos_template_loop_tv_show_link_open() {
        global $tv_show;

        $link = apply_filters( 'masvideos_loop_tv_show_link', get_the_permalink(), $tv_show );

        echo '<a href="' . esc_url( $link ) . '" class="masvideos-LoopTvShow-link masvideos-loop-tv-show__link tv-show__link">';
    }
}

if ( ! function_exists( 'masvideos_template_loop_tv_show_poster' ) ) {
    /**
     * tv show poster in the loop.
     */
    function masvideos_template_loop_tv_show_poster() {
        echo masvideos_get_tv_show_thumbnail( 'masvideos_tv_show_medium' );
    }
}

if ( ! function_exists( 'masvideos_template_loop_tv_show_link_close' ) ) {
    /**
     * Insert the opening anchor tag for tv show in the loop.
     */
    function masvideos_template_loop_tv_show_link_close() {
        echo '</a>';
    }
}

if ( ! function_exists( 'masvideos_template_loop_tv_show_poster_close' ) ) {
    /**
     * tv show poster close in the loop.
     */
    function masvideos_template_loop_tv_show_poster_close() {
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_template_loop_tv_show_body_open' ) ) {

    /**
     * tv show body open in the tv show loop.
     */
    function masvideos_template_loop_tv_show_body_open() {
        echo '<div class="tv-show__body">';
    }
}

if ( ! function_exists( 'masvideos_template_loop_tv_show_info_open' ) ) {

    /**
     * tv show info open in the tv show loop.
     */
    function masvideos_template_loop_tv_show_info_open() {
        echo '<div class="tv-show__info">';
    }
}

if ( ! function_exists( 'masvideos_template_loop_tv_show_info_head_open' ) ) {

    /**
     * tv show info body open in the tv show loop.
     */
    function masvideos_template_loop_tv_show_info_head_open() {
        echo '<div class="tv-show__info--head">';
    }
}

if ( ! function_exists( 'masvideos_template_loop_tv_show_title' ) ) {

    /**
     * Show the tv show title in the tv show loop. By default this is an H3.
     */
    function masvideos_template_loop_tv_show_title() {
        the_title( '<h3 class="masvideos-loop-tv-show__title  tv-show__title">', '</h3>' );
    }
}

if ( ! function_exists( 'masvideos_template_loop_tv_show_info_head_close' ) ) {

    /**
     * tv show info body close in the tv show loop.
     */
    function masvideos_template_loop_tv_show_info_head_close() {
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_template_loop_tv_show_short_desc' ) ) {

    /**
     * video short description in the video loop.
     */
    function masvideos_template_loop_tv_show_short_desc() {
        global $post;

        $short_description = apply_filters( 'masvideos_template_loop_tv_show_short_desc', $post->post_excerpt );

        if ( ! $short_description ) {
            return;
        }

        ?>
        <div class="tv-show__short-description">
            <?php echo '<p>' . $short_description . '</p>'; ?>
        </div>

        <?php
    }
}

if ( ! function_exists( 'masvideos_template_loop_tv_show_actions' ) ) {

    /**
     * TV Show actions in the tv show loop.
     */
    function masvideos_template_loop_tv_show_actions() {
        echo '<div class="tv-show__actions">';
            global $tv_show;
            $link = apply_filters( 'masvideos_loop_tv_show_link', get_the_permalink(), $tv_show );
            echo '<a href="' . esc_url( $link ) . '" class="tv-show-actions--link_watch">' . esc_html__( 'Watch Now', 'masvideos' ) . '</a>';
            echo '<a href="#" class="tv-show-actions--link_add-to-playlist">' . esc_html__( '+ Playlist', 'masvideos' ) . '</a>';
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_template_loop_tv_show_info_close' ) ) {

    /**
     * tv show info close in the tv show loop.
     */
    function masvideos_template_loop_tv_show_info_close() {
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_template_loop_tv_show_review_info_open' ) ) {

    /**
     * tv show review info open in the tv show loop.
     */
    function masvideos_template_loop_tv_show_review_info_open() {
        echo '<div class="tv-show__review-info">';
    }
}

if ( ! function_exists( 'masvideos_template_loop_tv_show_avg_rating' ) ) {

    /**
     * tv show avg rating in the tv show loop.
     */
    function masvideos_template_loop_tv_show_avg_rating() {
        global $tv_show;

        if ( ! empty( $tv_show->get_review_count() ) && $tv_show->get_review_count() > 0 ) {
            ?>
            <a href="<?php echo esc_url( get_permalink( $tv_show->get_id() ) . '#reviews' ); ?>" class="avg-rating">
                <span class="avg-rating-number"> <?php echo number_format( $tv_show->get_average_rating(), 1, '.', '' ); ?></span>
                <span class="avg-rating-text">
                    <?php echo wp_kses_post( sprintf( _n( '<span>%s</span> Vote', '<span>%s</span> Votes', $tv_show->get_review_count(), 'masvideos' ), $tv_show->get_review_count() ) ) ; ?>
                </span>
            </a>
            <?php
        }
    }
}

if ( ! function_exists( 'masvideos_template_loop_tv_show_viewers_count' ) ) {

    /**
     * tv show actions in the tv show loop.
     */
    function masvideos_template_loop_tv_show_viewers_count() {
        echo '<div class="viewers-count"></div>';
    }
}

if ( ! function_exists( 'masvideos_template_loop_tv_show_review_info_close' ) ) {

    /**
     * tv show review info close in the tv show loop.
     */
    function masvideos_template_loop_tv_show_review_info_close() {
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_template_loop_tv_show_body_close' ) ) {

    /**
     * tv show body close in the tv show loop.
     */
    function masvideos_template_loop_tv_show_body_close() {
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_template_loop_tv_show_new_episode' ) ) {

    /**
     * TV show new episode in the tv show loop.
     */
    function masvideos_template_loop_tv_show_new_episode() {
        $all_episodes = masvideos_get_tv_show_all_episodes();
        if( ! empty( $all_episodes ) ) {
            end( $all_episodes );
            $latest_episode_key = key( $all_episodes );
            $episode = masvideos_get_episode( $all_episodes[$latest_episode_key]['episode'] );
            $episode_url = get_permalink( $episode->get_ID() );
            $episode_title = $episode->get_episode_number();
            if( empty( $episode_title ) ) {
                $episode_title = $episode->get_title();
            }
            echo '<div class="tv-show__episode">'. esc_html__( 'Newest Episode: ', 'masvideos' );
            echo '<a href="' . esc_url( $episode_url ) . '" class="tv-show__episode--link">' . $episode_title . '</a>';
            echo '</div>';
        }
    }
}

if ( ! function_exists( 'masvideos_template_loop_tv_show_new_episodes_count' ) ) {

    /**
     * TV Show new episode in the tv show loop.
     */
    function masvideos_template_loop_tv_show_new_episodes_count() {
        $all_episodes = masvideos_get_tv_show_all_episodes();
        $count = count( $all_episodes );

        if( $count ) {
            echo wp_kses_post( sprintf( _n( '<div class="tv-show__episode">%s</div> Episode', '<span>%s</span> Episodes', $count, 'masvideos' ), $count ) ) ;
        }
    }
}


if ( ! function_exists( 'masvideos_template_loop_tv_show_hover_area_open' ) ) {
    /**
     * Display the hover area
     */
    function masvideos_template_loop_tv_show_hover_area_open() {
        ?>
        <div class="tv-show__hover-area"><div class="tv-show__hover-area--inner">
        <?php
    }
}

if ( ! function_exists( 'masvideos_template_loop_tv_show_hover_area_info_close' ) ) {
    /**
     * Close the hover area
     */
    function masvideos_template_loop_tv_show_hover_area_info_close() {
        ?>
        </div>
    </div>
    <?php
    }
}

if ( ! function_exists( 'masvideos_template_loop_tv_show_seasons_episode_wrap_open' ) ) {
    /**
     * Season episode wrap open
     */
    function masvideos_template_loop_tv_show_seasons_episode_wrap_open() {
        echo '<div class="tv-show__season-info">';
    }
}

if ( ! function_exists( 'masvideos_template_loop_tv_show_seasons_episode_wrap_close' ) ) {
    /**
     * Season episode wrap close
     */
    function masvideos_template_loop_tv_show_seasons_episode_wrap_close() {
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_template_loop_tv_show_hover_area_poster_info_open' ) ) {
    /**
     * Hover area poster info open
     */
    function masvideos_template_loop_tv_show_hover_area_poster_info_open() {
        echo '<div class="tv-show__hover-area--poster">';
    }
}

if ( ! function_exists( 'masvideos_template_loop_tv_show_hover_area_poster_info_close' ) ) {
    /**
     * Hover area poster info close
     */
    function masvideos_template_loop_tv_show_hover_area_poster_info_close() {
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_template_loop_tv_show_hover_area_body_info_open' ) ) {
    /**
     * Hover area body open
     */
    function masvideos_template_loop_tv_show_hover_area_body_info_open() {
        echo '<div class="tv-show__hover-area--body">';
    }
}

if ( ! function_exists( 'masvideos_template_loop_tv_show_hover_area_body_info_close' ) ) {
    /**
     * Hover area body close
     */
    function masvideos_template_loop_tv_show_hover_area_body_info_close() {
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_template_loop_tv_show_seasons' ) ) {
    /**
     * seasons
     */
    function masvideos_template_loop_tv_show_seasons() {
        global $tv_show;

        $season_titles = masvideos_get_tv_show_all_season_titles();
        if( ! empty( $season_titles ) ) {
            echo '<div class="tv-show__seasons">'. esc_html__( 'Seasons #:  ', 'masvideos' );
            foreach ( $season_titles as $season_title ) {
                echo '<a href="' . esc_url( get_permalink( $tv_show->get_ID() ) ) . '" class="tv-show__episode--link">' . $season_title . '</a>';
            }
            echo '</div>';
        }
    }
}

/**
 * Single
 */

if ( ! function_exists( 'masvideos_template_single_tv_show_tv_show' ) ) {

    /**
     * Output the tv show.
     */
    function masvideos_template_single_tv_show_tv_show() {
        masvideos_the_tv_show();
    }
}

if ( ! function_exists( 'masvideos_template_single_tv_show_title' ) ) {

    /**
     * Output the tv show title.
     */
    function masvideos_template_single_tv_show_title() {
        the_title( '<h1 class="tv-show_title entry-title">', '</h1>' );
    }
}

if ( ! function_exists( 'masvideos_template_single_tv_show_meta' ) ) {

    /**
     * tv show meta in the tv show single.
     */
    function masvideos_template_single_tv_show_meta() {
        echo '<div class="tv-show__meta">';
            do_action( 'masvideos_single_tv_show_meta' );
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_template_single_tv_show_genres' ) ) {

    /**
     * TV Show genres in the tv show single.
     */
    function masvideos_template_single_tv_show_genres() {
        global $tv_show;

        $categories = get_the_term_list( $tv_show->get_id(), 'tv_show_genre', '', ', ' );

        if( ! empty ( $categories ) ) {
           echo '<span class="tv-show__meta--genre">' . $categories . '</span>';
        }
    }
}

if ( ! function_exists( 'masvideos_template_single_tv_show_release_year' ) ) {

    /**
     * TV Show release year in the tv show single.
     */
    function masvideos_template_single_tv_show_release_year() {
        global $tv_show;
        
        $tv_show_year = '';

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

        if( ! empty ( $tv_show_year ) ) {
            echo sprintf( '<span class="tv-show__meta--release-year">%s</span>', $tv_show_year );
        }
    }
}

if ( ! function_exists( 'masvideos_template_single_tv_show_seasons_tabs' ) ) {

    /**
     * TV Show seasons and episodes tabs in the tv show single.
     */
    function masvideos_template_single_tv_show_seasons_tabs() {
        global $tv_show;

        $seasons = $tv_show->get_seasons();
        if( ! empty( $seasons ) ) {
            $tabs = array();
            foreach ( $seasons as $key => $season ) {
                if( ! empty( $season['name'] ) && ! empty( $season['episodes'] ) ) {
                    $episode_ids = implode( ",", $season['episodes'] );
                    $shortcode_atts = apply_filters( 'masvideos_template_single_tv_show_seasons_tab_shortcode_atts', array(
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

if ( ! function_exists( 'masvideos_related_tv_shows' ) ) {

    /**
     * Output the related tv shows.
     *
     * @param array $args Provided arguments.
     */
    function masvideos_related_tv_shows( $tv_show_id = false, $args = array() ) {
        global $tv_show;

        $tv_show_id = $tv_show_id ? $tv_show_id : $tv_show->get_id();

        if ( ! $tv_show_id ) {
            return;
        }

        $defaults = apply_filters( 'masvideos_related_tv_shows_default_args', array(
            'limit'          => 5,
            'columns'        => 5,
            'orderby'        => 'rand',
            'order'          => 'desc',
        ) );

        $args = wp_parse_args( $args, $defaults );

        $title = apply_filters( 'masvideos_related_tv_shows_title', esc_html__( 'Related TV Shows', 'masvideos' ), $tv_show_id );

        $related_tv_show_ids = masvideos_get_related_tv_shows( $tv_show_id, $args['limit'] );
        $args['ids'] = implode( ',', $related_tv_show_ids );

        if( ! empty( $related_tv_show_ids ) ) {
            echo '<section class="tv-show__related">';
                echo sprintf( '<h2 class="tv-show__related--title">%s</h2>', $title );
                echo MasVideos_Shortcodes::tv_shows( $args );
            echo '</section>';
        }
    }
}

if ( ! function_exists( 'masvideos_tv_show_comments' ) ) {

    /**
     * Output the Review comments template.
     *
     * @param WP_Comment $comment Comment object.
     * @param array      $args Arguments.
     * @param int        $depth Depth.
     */
    function masvideos_tv_show_comments( $comment, $args, $depth ) {
        $GLOBALS['comment'] = $comment; // WPCS: override ok.
        masvideos_get_template( 'single-tv-show/review.php', array(
            'comment' => $comment,
            'args'    => $args,
            'depth'   => $depth,
        ) );
    }
}

if ( ! function_exists( 'masvideos_tv_show_review_display_gravatar' ) ) {
    /**
     * Display the review authors gravatar
     *
     * @param array $comment WP_Comment.
     * @return void
     */
    function masvideos_tv_show_review_display_gravatar( $comment ) {
        echo get_avatar( $comment, apply_filters( 'masvideos_tv_show_review_gravatar_size', '60' ), '' );
    }
}

if ( ! function_exists( 'masvideos_tv_show_review_display_rating' ) ) {
    /**
     * Display the reviewers star rating
     *
     * @return void
     */
    function masvideos_tv_show_review_display_rating() {
        if ( post_type_supports( 'tv_show', 'comments' ) ) {
            masvideos_get_template( 'single-tv-show/review-rating.php' );
        }
    }
}

if ( ! function_exists( 'masvideos_tv_show_review_display_meta' ) ) {
    /**
     * Display the review authors meta (name, verified owner, review date)
     *
     * @return void
     */
    function masvideos_tv_show_review_display_meta() {
        masvideos_get_template( 'single-tv-show/review-meta.php' );
    }
}

if ( ! function_exists( 'masvideos_tv_show_review_display_comment_text' ) ) {
    /**
     * Display the review content.
     */
    function masvideos_tv_show_review_display_comment_text() {
        echo '<div class="description">';
        comment_text();
        echo '</div>';
    }
}

