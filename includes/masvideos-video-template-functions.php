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
        'columns'      => masvideos_get_default_videos_per_row(),
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
 * Get the default columns setting - this is how many videos will be shown per row in loops.
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

    return apply_filters( 'masvideos_video_columns', max( 1, $columns ) );
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

    return apply_filters( 'masvideos_video_rows', $rows );
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
    post_class( $class );
}

/**
 * Search Form
 */
if ( ! function_exists( 'masvideos_get_video_search_form' ) ) {

    /**
     * Display video search form.
     *
     * Will first attempt to locate the video-searchform.php file in either the child or.
     * the parent, then load it. If it doesn't exist, then the default search form.
     * will be displayed.
     *
     * The default searchform uses html5.
     *
     * @param bool $echo (default: true).
     * @return string
     */
    function masvideos_get_video_search_form( $echo = true ) {
        global $video_search_form_index;

        ob_start();

        if ( empty( $video_search_form_index ) ) {
            $video_search_form_index = 0;
        }

        do_action( 'pre_masvideos_get_video_search_form' );

        masvideos_get_template( 'search-form.php', array(
            'index' => $video_search_form_index++,
            'post_type' => 'video',
        ) );

        $form = apply_filters( 'masvideos_get_video_search_form', ob_get_clean() );

        if ( ! $echo ) {
            return $form;
        }

        echo $form; // WPCS: XSS ok.
    }
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

if ( ! function_exists( 'masvideos_no_videos_found' ) ) {

    /**
     * Handles the loop when no videos were found/no video exist.
     */
    function masvideos_no_videos_found() {
        ?><p class="masvideos-info"><?php _e( 'No videos were found matching your selection.', 'masvideos' ); ?></p><?php
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

            if ( empty( $page_title ) ) {
                $page_title = post_type_archive_title( '', false );
            }

        }

        $page_title = apply_filters( 'masvideos_video_page_title', $page_title );

        if ( $echo ) {
            echo $page_title; // WPCS: XSS ok.
        } else {
            return $page_title;
        }
    }
}

if ( ! function_exists( 'masvideos_display_video_page_title' ) ) {
    /**
     * Outputs Mas Videos Title
     */
    function masvideos_display_video_page_title() {

        if ( apply_filters( 'masvideos_display_video_page_title', true ) ) {
            ?>
            <header class="page-header">
                <h1 class="page-title"><?php masvideos_video_page_title(); ?></h1>
            </header>
            <?php
        }
    }
}

if ( ! function_exists( 'masvideos_videos_control_bar' ) ) {
    /**
     * Display Control Bar.
     */
    function masvideos_videos_control_bar() {
        echo '<div class="masvideos-control-bar masvideos-videos-control-bar">';
            masvideos_videos_count();
            masvideos_videos_catalog_ordering();
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_videos_catalog_ordering' ) ) {
    function masvideos_videos_catalog_ordering() {
        if ( ! masvideos_get_videos_loop_prop( 'is_paginated' ) || ! masvideos_videos_will_display() ) {
            return;
        }

        $catalog_orderby_options = apply_filters( 'masvideos_default_videos_catalog_orderby_options', array(
            'title-asc'     => esc_html__( 'Name: Ascending', 'masvideos' ),
            'title-desc'    => esc_html__( 'Name: Descending', 'masvideos' ),
            'date'          => esc_html__( 'Latest', 'masvideos' ),
            'menu_order'    => esc_html__( 'Menu Order', 'masvideos' ),
            'rating'        => esc_html__( 'Rating', 'masvideos' ),
        ) );

        $default_orderby = masvideos_get_videos_loop_prop( 'is_search' ) ? 'relevance' : apply_filters( 'masvideos_default_videos_catalog_orderby', get_option( 'masvideos_default_videos_catalog_orderby', 'date' ) );
        $orderby         = isset( $_GET['orderby'] ) ? masvideos_clean( wp_unslash( $_GET['orderby'] ) ) : $default_orderby; // WPCS: sanitization ok, input var ok, CSRF ok.

        if ( masvideos_get_videos_loop_prop( 'is_search' ) ) {
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
            <?php masvideos_query_string_form_fields( null, array( 'orderby', 'submit', 'paged', 'video-page' ) ); ?>
        </form>
        <?php
    }
}

if ( ! function_exists( 'masvideos_videos_count' ) ) {

    /**
     * Output the result count text (Showing x - x of x results).
     */
    function masvideos_videos_count() {
        if ( ! masvideos_get_videos_loop_prop( 'is_paginated' ) || ! masvideos_videos_will_display() ) {
            return;
        }
        $args = array(
            'total'    => masvideos_get_videos_loop_prop( 'total' ),
            'per_page' => masvideos_get_videos_loop_prop( 'per_page' ),
            'current'  => masvideos_get_videos_loop_prop( 'current_page' ),
        );

        ?>
        <p class="masvideos-result-count masvideos-videos-result-count">
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

        echo '<a href="' . esc_url( $link ) . '" class="masvideos-LoopVideo-link masvideos-loop-video__link video__link">';
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
            echo '<span class="video__meta--last-update">' . human_time_diff( get_post_time( 'U' ), current_time( 'timestamp' ) ) . ' ' .  esc_html__( 'ago', 'masvideos' ) . '</span>';
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
            <?php echo '<div>' . $short_description . '</div>'; ?>
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
            $link = apply_filters( 'masvideos_loop_video_link', get_the_permalink(), $video );
            $text = apply_filters( 'masvideos_loop_video_action_button_text', esc_html__( 'Watch Now', 'masvideos' ), $video );
            echo '<a href="' . esc_url( $link ) . '" class="video-actions--link_watch">' . esc_html( $text ) . '</a>';
            masvideos_template_button_video_playlist();
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

if ( ! function_exists( 'masvideos_template_single_video_categories' ) ) {
    /**
     * Video categories in the video single.
     */
    function masvideos_template_single_video_categories() {
        global $video;

        $categories = get_the_term_list( $video->get_id(), 'video_cat', '', ', ' );

        if( ! empty ( $categories ) ) {
           echo '<span class="video__meta--category">' . $categories . '</span>';
        }
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
        the_title( '<h1 class="single-video__title entry-title">', '</h1>' );
    }
}

if ( ! function_exists( 'masvideos_template_single_video_tags' ) ) {

    /**
     * Video tags in the video single.
     */
    function masvideos_template_single_video_tags() {
        global $video;

        $tags = get_the_term_list( $video->get_id(), 'video_tag', '', ', ' );

        if( ! empty ( $tags ) ) {
            echo sprintf( '<span class="video__tags">%s %s</span>', esc_html__( 'Tags:', 'masvideos' ), $tags );
        }
    }
}

if ( ! function_exists( 'masvideos_template_single_video_meta' ) ) {

    /**
     * Output the video meta.
     */
    function masvideos_template_single_video_meta() {
        echo '<p class="single-video__meta">';
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
        $author = get_the_author();
        if( ! empty( $author ) ) {
            echo sprintf( '<span class="video_author">%s <strong>%s</strong></span>', apply_filters( 'masvideos_template_single_video_author', esc_html( 'by', 'masvideos' ) ), $author );
        }
    }
}

if ( ! function_exists( 'masvideos_template_single_video_posted_on' ) ) {

    /**
     * Output the video posted on.
     */
    function masvideos_template_single_video_posted_on() {
        $date = get_the_date();
        if( ! empty( $date ) ) {
            echo sprintf( '<span class="video_posted_on">%s %s</span>', apply_filters( 'masvideos_template_single_video_posted_on', esc_html( 'published on', 'masvideos' ) ), $date );
        }
    }
}

if ( ! function_exists( 'masvideos_template_single_video_actions_bar' ) ) {
    /**
     * Single video actions
     */
    function masvideos_template_single_video_actions_bar() {
        ?>
        <div class="single-video__actions-bar">
            <?php masvideos_template_button_video_playlist(); ?>
        </div>
        <?php
    }
}

if ( ! function_exists( 'masvideos_template_single_video_description' ) ) {
    /**
     * Single video description
     */
    function masvideos_template_single_video_description() {
        ?>
        <div class="single-video__description">
            <div><?php the_content(); ?></div>
        </div>
        <?php
    }
}

if ( ! function_exists( 'masvideos_template_single_video_short_desc' ) ) {
    /**
     * Single video short description
     */
    function masvideos_template_single_video_short_desc() {
        global $post;

        $short_description = apply_filters( 'masvideos_template_single_video_short_desc', $post->post_excerpt );

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

if ( ! function_exists( 'masvideos_template_single_video_avg_rating' ) ) {

    /**
     * Single video average rating
     */
    function masvideos_template_single_video_avg_rating() {
        global $video;

        ?><div class="video__avg-rating">
        <?php if ( ! empty( $video->get_review_count() ) && $video->get_review_count() > 0 ) { ?>
            <a href="<?php echo esc_url( get_permalink( $video->get_id() ) . '#reviews' ); ?>" class="avg-rating">
                <div class="avg-rating__inner">
                    <span class="avg-rating__number"> <?php echo number_format( $video->get_average_rating(), 1, '.', '' ); ?></span>
                    <span class="avg-rating__text">
                        <?php echo wp_kses_post( sprintf( _n( '<span>%s</span> Vote', '<span>%s</span> votes', $video->get_review_count(), 'masvideos' ), $video->get_review_count() ) ) ; ?>
                    </span>
                </div>
            </a>
        <?php } ?>
        </div><?php
    }
}

if ( ! function_exists( 'masvideos_related_videos' ) ) {

    /**
     * Output the related videos.
     *
     * @param array $args Provided arguments.
     */
    function masvideos_related_videos( $video_id = false, $args = array() ) {
        global $video;

        $video_id = $video_id ? $video_id : $video->get_id();

        if ( ! $video_id ) {
            return;
        }

        $defaults = apply_filters( 'masvideos_related_videos_default_args', array(
            'limit'          => 5,
            'columns'        => 5,
            'orderby'        => 'rand',
            'order'          => 'desc',
        ) );

        $args = wp_parse_args( $args, $defaults );

        $title = apply_filters( 'masvideos_related_videos_title', esc_html__( 'Related Videos', 'masvideos' ), $video_id );

        $related_video_ids = masvideos_get_related_videos( $video_id, $args['limit'] );
        $args['ids'] = implode( ',', $related_video_ids );

        if( ! empty( $related_video_ids ) ) {
            echo '<section class="single-video__related">';
                echo sprintf( '<h2 class="single-video__related--title">%s</h2>', $title );
                echo MasVideos_Shortcodes::videos( $args );
            echo '</section>';
        }
    }
}

if ( ! function_exists( 'masvideos_template_single_video_related_playlist_videos' ) ) {
    /**
     * Single video page related playlist videos.
     *
     * @since  1.0.0
     */
    function masvideos_template_single_video_related_playlist_videos() {
        global $video;
        $video_id = $video->get_id();

        $video_playlist_id = isset( $_GET['video_playlist_id'] ) ? absint( $_GET['video_playlist_id'] ) : 0;

        if( $video_playlist_id > 0 ) {
            $videos_ids = masvideos_single_video_playlist_videos( $video_playlist_id );

            if( ! empty( $videos_ids ) ) {
                $title = apply_filters( 'masvideos_template_single_video_videos_playlist_title', get_the_title( $video_playlist_id ), $video_playlist_id );
                $count_info = apply_filters( 'masvideos_template_single_video_videos_playlist_count', count( $videos_ids ) . esc_html__( ' videos', 'masvideos' ), $video_playlist_id );
                $filtered_videos_ids = $videos_ids;

                if ( ( $current_video_key = array_search( $video_id, $filtered_videos_ids ) ) !== false ) {
                    unset( $filtered_videos_ids[$current_video_key] );
                }

                $args = apply_filters( 'masvideos_template_single_video_videos_playlist_args', array(
                    'limit'          => 5,
                    'columns'        => 5,
                    'orderby'        => 'rand',
                    'order'          => 'desc',
                    'ids'            => implode( ",", $filtered_videos_ids )
                ) );
                ?>
                <div class="single-video__related-playlist-videos">
                    <div class="single-video__related-playlist-videos--flex-header">
                        <?php
                            echo sprintf( '<h2 class="single-video__related-playlist-videos--title">%s</h2>', $title );
                            echo sprintf( '<a href="%s" class="single-video__related-playlist-videos--count">%s</a>', get_permalink( $video_playlist_id ), $count_info );
                        ?>
                    </div>
                    <div class="single-video__related-playlist-videos--content">
                        <?php masvideos_template_single_video_playlist_videos( $video_playlist_id, $args ); ?>
                    </div>
                </div>
                <?php
            }
        }
    }
}

if ( ! function_exists( 'masvideos_template_button_video_playlist' ) ) {
    /**
     * Button dropdown for Add/Remove video to playlist.
     *
     * @since  1.0.0
     */
    function masvideos_template_button_video_playlist() {
        global $video;

        ?>
        <div class="video-actions--link_add-to-playlist dropdown">
            <a class="dropdown-toggle" href="<?php echo get_permalink( $video->get_id() ); ?>" data-toggle="dropdown"><?php echo esc_html__( '+ Playlist', 'masvideos' ) ?></a>
            <div class="dropdown-menu">
                <?php
                    $video_playlists_page_url = masvideos_get_endpoint_url( 'video-playlists', '', masvideos_get_page_permalink( 'myaccount' ) );
                    if ( is_user_logged_in() ) {
                        masvideos_template_button_toggle_user_video_playlist( $video->get_id() );
                        ?><a class="create-playlist-link" href="<?php echo esc_attr( $video_playlists_page_url ); ?>"><?php echo esc_html__( 'Create a playlist', 'masvideos' ); ?></a><?php
                    } else {
                        ?><a class="login-link" href="<?php echo esc_attr( $video_playlists_page_url ); ?>"><?php echo esc_html__( 'Sign in to add this video to a playlist.', 'masvideos' ); ?></a><?php
                    }
                ?>
            </div>
        </div>
        <?php
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

if ( ! function_exists( 'masvideos_template_single_video_tabs' ) ) {

    /**
     * Video tabs in the video single.
     */
    function masvideos_template_single_video_tabs() {
        global $video, $post;

        $tabs = array();
        
        // Description tab - shows video content.
        if ( $post->post_content ) {
            $tabs['description'] = array(
                'title'     => esc_html__( 'Description', 'masvideos' ),
                'callback'  => 'masvideos_template_single_video_description_tab',
                'priority'  => 10
            );
        }

        // // Sources tab - shows link sources.
        // if ( $video && ( $video->has_sources() ) ) {
        //     $tabs['sources'] = array(
        //         'title'     => esc_html__( 'Additional Information', 'masvideos' ),
        //         'callback'  => 'masvideos_template_single_video_sources',
        //         'priority'  => 20
        //     );
        // }

        // Comments tab - shows comments.
        if ( comments_open() ) {
            $tabs['reviews'] = array(
                'title'     => esc_html__( 'Comments', 'masvideos' ),
                'callback'  => 'comments_template',
                'priority'  => 30
            );
        }

        $tabs = apply_filters( 'masvideos_template_single_video_tabs', $tabs );

        if( ! empty( $tabs ) ) {
            masvideos_get_template( 'global/tabs.php', array( 'tabs' => $tabs, 'class' => 'video-tabs' ) );
        }
    }
}

if ( ! function_exists( 'masvideos_template_single_video_description_tab' ) ) {
    /**
     * Single video description tab
     */
    function masvideos_template_single_video_description_tab() {
        global $video;
        echo '<div id="video__description-tab" class="video__description-tab">';
            do_action( 'masvideos_single_video_description_tab', $video );
        echo '</div>';
    }
}

if ( ! function_exists( 'masvideos_display_video_attributes' ) ) {
    /**
     * Outputs a list of video attributes for a video.
     *
     * @since  1.0.0
     * @param  Mas_Videos $video Video Object.
     */
    function masvideos_display_video_attributes() {
        global $video;
        masvideos_get_template( 'single-video/video-attributes.php', array(
            'video'         => $video,
            'attributes'    => array_filter( $video->get_attributes(), 'masvideos_attributes_video_array_filter_visible' ),
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

if ( ! function_exists( 'masvideos_videos_pagination' ) ) {
    /**
     * Display Pagination.
     */
    function masvideos_videos_pagination() {
        if ( ! masvideos_get_videos_loop_prop( 'is_paginated' ) || ! masvideos_videos_will_display() ) {
            return;
        }

        $args = array(
            'total'   => masvideos_get_videos_loop_prop( 'total_pages' ),
            'current' => masvideos_get_videos_loop_prop( 'current_page' ),
            'base'    => esc_url_raw( add_query_arg( 'video-page', '%#%', false ) ),
            'format'  => '?video-page=%#%',
        );

        if ( ! masvideos_get_videos_loop_prop( 'is_shortcode' ) ) {
            $args['format'] = '';
            $args['base']   = esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) );
        }

        if (  $args['total'] <= 1 ) {
            return;
        }
        ?>

        <nav class="masvideos-pagination masvideos-videos-pagination">
            <?php
                echo paginate_links( apply_filters( 'masvideos_videos_pagination_args', array( // WPCS: XSS ok.
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

if ( ! function_exists( 'masvideos_template_single_video_player_wrap_open' ) ) {
    /**
     * Single video player open
     */
    function masvideos_template_single_video_player_wrap_open() {
        ?>
        <div class="video__player">
        <?php
    }
}

if ( ! function_exists( 'masvideos_template_single_video_player_wrap_close' ) ) {
    /**
     * Single video player close
     */
    function masvideos_template_single_video_player_wrap_close() {
        ?>
        </div>
        <?php
    }
}

if ( ! function_exists( 'masvideos_template_single_video_gallery' ) ) {
    function masvideos_template_single_video_gallery() {
        global $video;

        $columns           = apply_filters( 'masvideos_video_gallery_thumbnails_columns', 8 );
        $attachment_ids    = $video->get_gallery_image_ids();
        $wrapper_classes   = apply_filters( 'masvideos_single_video_image_gallery_classes', array(
            'masvideos-video-gallery',
            'masvideos-video-gallery--' . ( $video->get_image_id() ? 'with-images' : 'without-images' ),
            'masvideos-video-gallery--columns-' . absint( $columns ),
            'images',
        ) );
        $title = apply_filters( 'masvideos_template_single_video_gallery_title', esc_html__( 'Gallery', 'masvideos' ));

        if ( $attachment_ids && $video->get_image_id() ) {
            ?>
            <div class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) ); ?>" data-columns="<?php echo esc_attr( $columns ); ?>">
                <?php echo sprintf( '<h2 class="masvideos-video-gallery__title">%s</h2>', $title ); ?>
                <div class="masvideos-video-gallery__inner">
                    <?php
                    foreach ( $attachment_ids as $attachment_id ) {
                        echo apply_filters( 'masvideos_single_video_image_thumbnail_html', masvideos_get_gallery_image_html( $attachment_id ), $attachment_id ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
                    }
                ?>
                </div>
            </div>
            <?php
        }
    }
}
