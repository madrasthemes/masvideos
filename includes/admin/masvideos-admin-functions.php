<?php
/**
 * MasVideos Admin Functions
 *
 * @category Core
 * @package  MasVideos/Admin/Functions
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Get all MasVideos screen ids.
 *
 * @return array
 */
function masvideos_get_screen_ids() {

    $masvideos_screen_id = sanitize_title( __( 'MasVideos', 'masvideos' ) );
    $screen_ids   = array(
        'toplevel_page_' . $masvideos_screen_id,
        'person_page_person_attributes',
        'edit-person',
        'person',
        'edit-person_cat',
        'edit-person_tag',
        'episode_page_episode_attributes',
        'edit-episode',
        'episode',
        'edit-episode_genre',
        'edit-episode_tag',
        'tv_show_page_tv_show_attributes',
        'edit-tv_show',
        'tv_show',
        'edit-tv_show_genre',
        'edit-tv_show_tag',
        'video_page_video_attributes',
        'edit-video',
        'video',
        'edit-video_cat',
        'edit-video_tag',
        'movie_page_movie_attributes',
        'edit-movie',
        'movie',
        'edit-movie_genre',
        'edit-movie_tag',
        'profile',
        'user-edit',
        'movie_page_movie_exporter',
        'movie_page_movie_importer',
        'video_page_video_exporter',
        'video_page_video_importer',
        'tv_show_page_tv_show_exporter',
        'tv_show_page_tv_show_importer',
        'person_page_person_exporter',
        'person_page_person_importer',
    );

    if ( $attributes = masvideos_get_attribute_taxonomies() ) {
        foreach ( $attributes as $attribute ) {
            $screen_ids[] = 'edit-' . masvideos_attribute_taxonomy_name( $attribute->post_type, $attribute->attribute_name );
        }
    }

    return apply_filters( 'masvideos_screen_ids', $screen_ids );
}

/**
 * Create a page and store the ID in an option.
 *
 * @param mixed  $slug Slug for the new page
 * @param string $option Option name to store the page's ID
 * @param string $page_title (default: '') Title for the new page
 * @param string $page_content (default: '') Content for the new page
 * @param int    $post_parent (default: 0) Parent for the new page
 * @return int page ID
 */
function masvideos_create_page( $slug, $option = '', $page_title = '', $page_content = '', $post_parent = 0 ) {
    global $wpdb;

    $option_value = get_option( $option );

    if ( $option_value > 0 && ( $page_object = get_post( $option_value ) ) ) {
        if ( 'page' === $page_object->post_type && ! in_array( $page_object->post_status, array( 'pending', 'trash', 'future', 'auto-draft' ) ) ) {
            // Valid page is already in place
            return $page_object->ID;
        }
    }

    if ( strlen( $page_content ) > 0 ) {
        // Search for an existing page with the specified page content (typically a shortcode)
        $valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' ) AND post_content LIKE %s LIMIT 1;", "%{$page_content}%" ) );
    } else {
        // Search for an existing page with the specified page slug
        $valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' )  AND post_name = %s LIMIT 1;", $slug ) );
    }

    $valid_page_found = apply_filters( 'masvideos_create_page_id', $valid_page_found, $slug, $page_content );

    if ( $valid_page_found ) {
        if ( $option ) {
            update_option( $option, $valid_page_found );
        }
        return $valid_page_found;
    }

    // Search for a matching valid trashed page
    if ( strlen( $page_content ) > 0 ) {
        // Search for an existing page with the specified page content (typically a shortcode)
        $trashed_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_content LIKE %s LIMIT 1;", "%{$page_content}%" ) );
    } else {
        // Search for an existing page with the specified page slug
        $trashed_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_name = %s LIMIT 1;", $slug ) );
    }

    if ( $trashed_page_found ) {
        $page_id   = $trashed_page_found;
        $page_data = array(
            'ID'          => $page_id,
            'post_status' => 'publish',
        );
        wp_update_post( $page_data );
    } else {
        $page_data = array(
            'post_status'    => 'publish',
            'post_type'      => 'page',
            'post_author'    => 1,
            'post_name'      => $slug,
            'post_title'     => $page_title,
            'post_content'   => $page_content,
            'post_parent'    => $post_parent,
            'comment_status' => 'closed',
        );
        $page_id   = wp_insert_post( $page_data );
    }

    if ( $option ) {
        update_option( $option, $page_id );
    }

    return $page_id;
}