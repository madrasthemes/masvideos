<?php
/**
 * MasVideos Account Functions
 *
 * Functions for account specific things.
 *
 * @package MasVideos/Functions
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get My Account menu items.
 *
 * @since 1.0.0
 * @return array
 */
function masvideos_get_account_menu_items() {
    $endpoints = array(
        'videos'            => get_option( 'masvideos_myaccount_videos_endpoint', 'videos' ),
        'movie-playlists'   => get_option( 'masvideos_myaccount_movie_playlists_endpoint', 'movie-playlists' ),
        'video-playlists'   => get_option( 'masvideos_myaccount_video_playlists_endpoint', 'video-playlists' ),
        'tv-show-playlists' => get_option( 'masvideos_myaccount_tv_show_playlists_endpoint', 'tv-show-playlists' ),
        'user-logout'       => get_option( 'masvideos_logout_endpoint', 'user-logout' ),
    );

    $items = array(
        'dashboard'         => esc_html__( 'Dashboard', 'masvideos' ),
        'videos'            => esc_html__( 'Videos', 'masvideos' ),
        'movie-playlists'   => esc_html__( 'Movie playlists', 'masvideos' ),
        'video-playlists'   => esc_html__( 'Video playlists', 'masvideos' ),
        'tv-show-playlists' => esc_html__( 'TV Show playlists', 'masvideos' ),
        'user-logout'       => esc_html__( 'Logout', 'masvideos' ),
    );

    // Remove missing endpoints.
    foreach ( $endpoints as $endpoint_id => $endpoint ) {
        if ( empty( $endpoint ) ) {
            unset( $items[ $endpoint_id ] );
        }
    }

    return apply_filters( 'masvideos_account_menu_items', $items, $endpoints );
}

/**
 * Get account menu item classes.
 *
 * @since 1.0.0
 * @param string $endpoint Endpoint.
 * @return string
 */
function masvideos_get_account_menu_item_classes( $endpoint ) {
    global $wp;

    $classes = array(
        'masvideos-MyAccount-navigation-link',
        'masvideos-MyAccount-navigation-link--' . $endpoint,
    );

    // Set current item class.
    $current = isset( $wp->query_vars[ $endpoint ] );
    if ( 'dashboard' === $endpoint && ( isset( $wp->query_vars['page'] ) || empty( $wp->query_vars ) ) ) {
        $current = true; // Dashboard is not an endpoint, so needs a custom check.
    }

    if ( $current ) {
        $classes[] = 'is-active';
    }

    $classes = apply_filters( 'masvideos_account_menu_item_classes', $classes, $endpoint );

    return implode( ' ', array_map( 'sanitize_html_class', $classes ) );
}

/**
 * Get My Account > Videos columns.
 *
 * @since   1.0.0
 * @return  array
 */
function masvideos_get_account_videos_columns() {
    $columns = apply_filters(
        'masvideos_account_videos_columns',
        array(
            'video-title'   => esc_html__( 'Title', 'masvideos' ),
            'video-date'    => esc_html__( 'Date', 'masvideos' ),
            'video-status'  => esc_html__( 'Status', 'masvideos' ),
            'video-actions' => esc_html__( 'Actions', 'masvideos' ),
        )
    );

    return $columns;
}

/**
 * Get account videos actions.
 *
 * @since  1.0.0
 * @param  int|WC_Order $video Order instance or ID.
 * @return array
 */
function masvideos_get_account_videos_actions( $video ) {
    if ( ! is_object( $video ) ) {
        $video_id = absint( $video );
        $video    = masvideos_get_video( $video_id );
    }

    $actions = array(
        'view'   => array(
            'url'  => get_permalink( $video->get_id() ),
            'name' => esc_html__( 'View', 'masvideos' ),
        ),
        'edit'    => array(
            'url'  => '#',
            'name' => esc_html__( 'Edit', 'masvideos' ),
        ),
        'delete' => array(
            'url'  => '#',
            'name' => esc_html__( 'Delete', 'masvideos' ),
        ),
    );

    return apply_filters( 'masvideos_my_account_my_videos_actions', $actions, $video );
}

/**
 * Get endpoint URL.
 *
 * Gets the URL for an endpoint, which varies depending on permalink settings.
 *
 * @param  string $endpoint  Endpoint slug.
 * @param  string $value     Query param value.
 * @param  string $permalink Permalink.
 *
 * @return string
 */
function masvideos_get_endpoint_url( $endpoint, $value = '', $permalink = '' ) {
    if ( ! $permalink ) {
        $permalink = get_permalink();
    }

    // Map endpoint to options.
    $query_vars = MasVideos()->query->get_query_vars();
    $endpoint   = ! empty( $query_vars[ $endpoint ] ) ? $query_vars[ $endpoint ] : $endpoint;

    if ( get_option( 'permalink_structure' ) ) {
        if ( strstr( $permalink, '?' ) ) {
            $query_string = '?' . wp_parse_url( $permalink, PHP_URL_QUERY );
            $permalink    = current( explode( '?', $permalink ) );
        } else {
            $query_string = '';
        }
        $url = trailingslashit( $permalink ) . trailingslashit( $endpoint );

        if ( $value ) {
            $url .= trailingslashit( $value );
        }

        $url .= $query_string;
    } else {
        $url = add_query_arg( $endpoint, $value, $permalink );
    }

    return apply_filters( 'masvideos_get_endpoint_url', $url, $endpoint, $value, $permalink );
}

/**
 * Get account endpoint URL.
 *
 * @since 1.0.0
 * @param string $endpoint Endpoint.
 * @return string
 */
function masvideos_get_account_endpoint_url( $endpoint ) {
    if ( 'dashboard' === $endpoint ) {
        return masvideos_get_page_permalink( 'myaccount' );
    }

    if ( 'user-logout' === $endpoint ) {
        return masvideos_logout_url();
    }

    return masvideos_get_endpoint_url( $endpoint, '', masvideos_get_page_permalink( 'myaccount' ) );
}