<?php
/**
 * MasVideos Page Functions
 *
 * Functions related to pages and menus.
 *
 * @package  MasVideos\Functions
 * @version  1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Retrieve page ids.
 *
 * @param string $page Page slug.
 * @return int
 */
function masvideos_get_page_id( $page ) {
    $page = apply_filters( 'masvideos_get_' . $page . '_page_id', get_option( 'masvideos_' . $page . '_page_id' ) );

    return $page ? absint( $page ) : -1;
}

/**
 * Retrieve page permalink.
 *
 * @param string      $page page slug.
 * @param string|bool $fallback Fallback URL if page is not set. Defaults to home URL.
 * @return string
 */
function masvideos_get_page_permalink( $page, $fallback = null ) {
    $page_id   = masvideos_get_page_id( $page );
    $permalink = 0 < $page_id ? get_permalink( $page_id ) : '';

    if ( ! $permalink ) {
        $permalink = is_null( $fallback ) ? get_home_url() : $fallback;
    }

    return apply_filters( 'masvideos_get_' . $page . '_page_permalink', $permalink );
}
