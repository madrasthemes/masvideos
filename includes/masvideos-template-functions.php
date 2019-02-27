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

require MASVIDEOS_ABSPATH . 'includes/masvideos-episode-template-functions.php';
require MASVIDEOS_ABSPATH . 'includes/masvideos-tv-show-template-functions.php';
require MASVIDEOS_ABSPATH . 'includes/masvideos-tv-show-playlist-template-functions.php';
require MASVIDEOS_ABSPATH . 'includes/masvideos-video-template-functions.php';
require MASVIDEOS_ABSPATH . 'includes/masvideos-video-playlist-template-functions.php';
require MASVIDEOS_ABSPATH . 'includes/masvideos-movie-template-functions.php';
require MASVIDEOS_ABSPATH . 'includes/masvideos-movie-playlist-template-functions.php';

/**
 * Handle redirects before content is output - hooked into template_redirect so is_page works.
 */
function masvideos_template_redirect() {
    global $wp_query, $wp;

    if ( ! empty( $_GET['page_id'] ) && '' === get_option( 'permalink_structure' ) && masvideos_get_page_id( 'episodes' ) === absint( $_GET['page_id'] ) && get_post_type_archive_link( 'episode' ) ) { // WPCS: input var ok, CSRF ok.

        // When default permalinks are enabled, redirect episodes page to post type archive url.
        wp_safe_redirect( get_post_type_archive_link( 'episode' ) );
        exit;

    } elseif ( is_search() && is_post_type_archive( 'episode' ) && apply_filters( 'masvideos_redirect_single_search_result', true ) && 1 === absint( $wp_query->found_posts ) ) {

        // Redirect to the episode page if we have a single episode.
        $episode = masvideos_get_episode( $wp_query->post );

        if ( $episode && $episode->is_visible() ) {
            wp_safe_redirect( get_permalink( $episode->get_id() ), 302 );
            exit;
        }

    } elseif ( ! empty( $_GET['page_id'] ) && '' === get_option( 'permalink_structure' ) && masvideos_get_page_id( 'tv_shows' ) === absint( $_GET['page_id'] ) && get_post_type_archive_link( 'tv_show' ) ) { // WPCS: input var ok, CSRF ok.

        // When default permalinks are enabled, redirect tv shows page to post type archive url.
        wp_safe_redirect( get_post_type_archive_link( 'tv_show' ) );
        exit;

    } elseif ( is_search() && is_post_type_archive( 'tv_show' ) && apply_filters( 'masvideos_redirect_single_search_result', true ) && 1 === absint( $wp_query->found_posts ) ) {

        // Redirect to the tv show page if we have a single tv show.
        $tv_show = masvideos_get_tv_show( $wp_query->post );

        if ( $tv_show && $tv_show->is_visible() ) {
            wp_safe_redirect( get_permalink( $tv_show->get_id() ), 302 );
            exit;
        }

    } elseif ( ! empty( $_GET['page_id'] ) && '' === get_option( 'permalink_structure' ) && masvideos_get_page_id( 'videos' ) === absint( $_GET['page_id'] ) && get_post_type_archive_link( 'video' ) ) { // WPCS: input var ok, CSRF ok.

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
 * Add body classes for MasVideos pages.
 *
 * @param  array $classes Body Classes.
 * @return array
 */
function masvideos_body_class( $classes ) {
    $classes = (array) $classes;

    if ( is_masvideos() ) {

        $classes[] = 'masvideos';
        $classes[] = 'masvideos-page';

        if( is_video() || is_movie() || is_episode() || is_tv_show() ) {
            $classes[] = 'masvideos-single';
        } else {
            $classes[] = 'masvideos-archive';
        }

    }

    $classes[] = 'masvideos-no-js';

    add_action( 'wp_footer', 'masvideos_no_js' );

    return array_unique( $classes );
}

/**
 * NO JS handling.
 *
 * @since 3.4.0
 */
function masvideos_no_js() {
    ?>
    <script type="text/javascript">
        var c = document.body.className;
        c = c.replace(/masvideos-no-js/, 'masvideos-js');
        document.body.className = c;
    </script>
    <?php
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
 * Get the placeholder image.
 *
 * @param string $size Image size.
 * @return string
 */
function masvideos_placeholder_img( $size = 'masvideos_thumbnail' ) {
    $dimensions = masvideos_get_image_size( $size );

    return apply_filters( 'masvideos_placeholder_img', '<img src="' . masvideos_placeholder_img_src( $size ) . '" alt="' . esc_attr__( 'Placeholder', 'masvideos' ) . '" width="' . esc_attr( $dimensions['width'] ) . '" class="masvideos-placeholder wp-post-image" height="' . esc_attr( $dimensions['height'] ) . '" />', $size, $dimensions );
}

/**
 * Outputs all queued notices on WC pages.
 *
 * @since 1.0.0
 */
function masvideos_output_all_notices() {
    echo '<div class="masvideos-notices-wrapper">';
    masvideos_print_notices();
    echo '</div>';
}

if ( ! function_exists( 'masvideos_star_rating' ) ) {
    /**
     * Output a HTML element with a star rating for a given rating.
     *
     * This is a clone of wp_star_rating().
     * 
     * @since 1.0.0
     * @param array $args Array of star ratings arguments.
     * @return string Star rating HTML.
     */
    function masvideos_star_rating( $args = array() ) {
        $defaults = array(
            'rating' => 0,
            'type'   => 'rating',
            'number' => 0,
            'echo'   => true,
        );
        $r = wp_parse_args( $args, $defaults );
     
        // Non-English decimal places when the $rating is coming from a string
        $rating = (float) str_replace( ',', '.', $r['rating'] );
     
        // Convert Percentage to star rating, 0..5 in .5 increments
        if ( 'percent' === $r['type'] ) {
            $rating = round( $rating / 10, 0 ) / 2;
        }
     
        // Calculate the number of each type of star needed
        $full_stars = floor( $rating );
        $half_stars = ceil( $rating - $full_stars );
        $empty_stars = 10 - $full_stars - $half_stars;
     
        if ( $r['number'] ) {
            /* translators: 1: The rating, 2: The number of ratings */
            $format = _n( '%1$s rating based on %2$s rating', '%1$s rating based on %2$s ratings', $r['number'] );
            $title = sprintf( $format, number_format_i18n( $rating, 1 ), number_format_i18n( $r['number'] ) );
        } else {
            /* translators: 1: The rating */
            $title = sprintf( __( '%s rating' ), number_format_i18n( $rating, 1 ) );
        }
     
        $output = '<div class="star-rating">';
        $output .= '<span class="screen-reader-text">' . $title . '</span>';
        $output .= str_repeat( '<div class="star star-full" aria-hidden="true"></div>', $full_stars );
        $output .= str_repeat( '<div class="star star-half" aria-hidden="true"></div>', $half_stars );
        $output .= str_repeat( '<div class="star star-empty" aria-hidden="true"></div>', $empty_stars );
        $output .= '</div>';
     
        if ( $r['echo'] ) {
            echo $output;
        }
     
        return $output;
    }
}

if ( ! function_exists( 'masvideos_get_star_rating_html' ) ) {
    /**
     * Get HTML for star rating.
     *
     * @since  1.0.0
     * @param  float $rating Rating being shown.
     * @param  int   $count  Total number of ratings.
     * @return string
     */
    function masvideos_get_star_rating_html( $rating, $count = 0 ) {
        $args = array(
            'rating'    => $rating,
            'type'      => 'rating',
            'number'    => $count,
            'echo'      => false,
        );
        $html = 0 < $rating ? masvideos_star_rating( $args ) : '';
        return apply_filters( 'masvideos_get_star_rating_html', $html, $rating, $count );
    }
}

if ( ! function_exists( 'masvideos_breadcrumb' ) ) {

    /**
     * Output the MasVideos Breadcrumb.
     *
     * @param array $args Arguments.
     */
    function masvideos_breadcrumb( $args = array() ) {
        $args = wp_parse_args( $args, apply_filters( 'masvideos_breadcrumb_defaults', array(
            'delimiter'   => '&nbsp;&#47;&nbsp;',
            'wrap_before' => '<nav class="masvideos-breadcrumb">',
            'wrap_after'  => '</nav>',
            'before'      => '',
            'after'       => '',
            'home'        => _x( 'Home', 'breadcrumb', 'masvideos' ),
        ) ) );

        $breadcrumbs = new MasVideos_Breadcrumb();

        if ( ! empty( $args['home'] ) ) {
            $breadcrumbs->add_crumb( $args['home'], apply_filters( 'masvideos_breadcrumb_home_url', home_url() ) );
        }

        $args['breadcrumb'] = $breadcrumbs->generate();

        /**
         * MasVideos Breadcrumb hook
         *
         * @hooked MasVideos_Structured_Data::generate_breadcrumblist_data() - 10
         */
        do_action( 'masvideos_breadcrumb', $breadcrumbs, $args );

        masvideos_get_template( 'global/breadcrumb.php', $args );
    }
}

if ( ! function_exists( 'masvideos_template_loop_content_area_open' ) ) {
    /**
     * Content Area open in the loop.
     */
    function masvideos_template_loop_content_area_open() {
        echo '<div id="primary" class="content-area">';
    }
}

if ( ! function_exists( 'masvideos_template_loop_content_area_close' ) ) {
    /**
     * Content Area open in the loop.
     */
    function masvideos_template_loop_content_area_close() {
        echo '</div><!-- /.content-area -->';
    }
}

if ( ! function_exists( 'masvideos_template_single_sharing' ) ) {

    /**
     * Output the sharing.
     */
    function masvideos_template_single_sharing() {
        do_action( 'masvideos_share' );
    }
}