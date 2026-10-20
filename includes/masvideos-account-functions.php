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
 * Resolve and authorize an account-owned post for a frontend edit/delete operation.
 *
 * Enforces, in strict order: authentication, a positive integer ID, existence of the
 * target post, an exact post-type allowlist, and finally authorization. Authorization
 * succeeds when the current user is the post author (when $allow_owner is true) OR the
 * current user passes the given meta capability for that specific object.
 *
 * The post-type allowlist is the critical guard: because it runs before the ownership
 * exception, the owner shortcut can never reach an unrelated post type (pages,
 * attachments, other plugins' content, other MAS Videos types). The ownership exception
 * exists only to preserve the intentional frontend self-service feature for users who
 * can create their own videos/playlists but do not hold the "others"/"published" meta
 * capabilities.
 *
 * @since 1.3.5
 *
 * @param mixed        $post_id     Untrusted target post ID.
 * @param string|array $post_types  Allowed post type slug(s).
 * @param string       $meta_cap    Meta capability checked against the object, e.g. 'edit_post' or 'delete_post'.
 * @param bool         $allow_owner Whether the post author is authorized regardless of the meta capability. Default true.
 * @return WP_Post|false The validated post on success, false on any failure.
 */
function masvideos_get_authorized_account_post( $post_id, $post_types, $meta_cap, $allow_owner = true ) {
    if ( ! is_user_logged_in() ) {
        return false;
    }

    if ( ! is_scalar( $post_id ) ) {
        return false;
    }

    $post_id = absint( $post_id );

    if ( $post_id <= 0 ) {
        return false;
    }

    $post = get_post( $post_id );

    if ( ! $post instanceof WP_Post ) {
        return false;
    }

    if ( ! in_array( $post->post_type, (array) $post_types, true ) ) {
        return false;
    }

    $user_id = get_current_user_id();

    if ( $allow_owner && $user_id > 0 && (int) $post->post_author === $user_id ) {
        return $post;
    }

    if ( current_user_can( $meta_cap, $post_id ) ) {
        return $post;
    }

    return false;
}

/**
 * Whether wp_trash_post() performs a RECOVERABLE trash rather than a permanent delete.
 *
 * WordPress permanently deletes instead of trashing when EMPTY_TRASH_DAYS is falsy
 * (0, or not yet defined). In that configuration an "ownership-only" trash would destroy
 * content and thereby bypass the permanent-deletion capability boundary, so callers must
 * fall back to requiring the delete capability. This fails closed: if we cannot confirm
 * that trashing is recoverable, we treat it as not recoverable.
 *
 * @since 1.3.5
 * @return bool True only when a trash operation is guaranteed recoverable.
 */
function masvideos_trash_is_recoverable() {
    return defined( 'EMPTY_TRASH_DAYS' ) && EMPTY_TRASH_DAYS > 0;
}

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
        'edit-account'      => get_option( 'masvideos_myaccount_edit_account_endpoint', 'edit-account' ),
        'user-logout'       => get_option( 'masvideos_logout_endpoint', 'user-logout' ),
    );

    $items = array(
        'dashboard'         => esc_html__( 'Dashboard', 'masvideos' ),
        'videos'            => esc_html__( 'Videos', 'masvideos' ),
        'movie-playlists'   => esc_html__( 'Movie playlists', 'masvideos' ),
        'video-playlists'   => esc_html__( 'Video playlists', 'masvideos' ),
        'tv-show-playlists' => esc_html__( 'TV Show playlists', 'masvideos' ),
        'edit-account'      => esc_html__( 'Account details', 'masvideos' ),
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
 * Get the link to the edit account details page.
 *
 * @return string
 */
function masvideos_user_edit_account_url() {
    $edit_account_url = masvideos_get_endpoint_url( 'edit-account', '', masvideos_get_page_permalink( 'myaccount' ) );

    return apply_filters( 'masvideos_user_edit_account_url', $edit_account_url );
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
 * @param  int|MasVideos_Video $video Video instance or ID.
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
            'url'  => add_query_arg( array( 'post' => $video->get_id(), 'action' => 'edit' ), masvideos_get_page_permalink( 'upload_video' ) ),
            'name' => esc_html__( 'Edit', 'masvideos' ),
        ),
        'delete' => array(
            'url'  => wp_nonce_url( add_query_arg( array( 'post' => $video->get_id(), 'action' => 'trash' ), get_permalink( $video->get_id() ) ), 'masvideos-delete-video_' . $video->get_id(), 'masvideos-delete-video-nonce' ),
            'name' => esc_html__( 'Delete', 'masvideos' ),
        ),
    );

    return apply_filters( 'masvideos_my_account_videos_actions', $actions, $video );
}

/**
 * Get account playlists actions.
 *
 * @since  1.0.0
 * @param  Playlist instance.
 * @return array
 */
function masvideos_get_account_playlists_actions( $obj ) {
    global $wp;

    $current_page_link = get_permalink();

    if( isset( $wp->query_vars['movie-playlists'] ) ) {
        $current_page_link = masvideos_get_endpoint_url( 'movie-playlists', '', masvideos_get_page_permalink( 'myaccount' ) );
    } elseif( isset( $wp->query_vars['video-playlists'] ) ) {
        $current_page_link = masvideos_get_endpoint_url( 'video-playlists', '', masvideos_get_page_permalink( 'myaccount' ) );
    } elseif( isset( $wp->query_vars['tv-show-playlists'] ) ) {
        $current_page_link = masvideos_get_endpoint_url( 'tv-show-playlists', '', masvideos_get_page_permalink( 'myaccount' ) );
    }

    $actions = array(
        'view'   => array(
            'url'  => get_permalink( $obj->ID ),
            'name' => esc_html__( 'View', 'masvideos' ),
        ),
        'edit'    => array(
            'url'  => add_query_arg( array( 'post' => $obj->ID, 'action' => 'edit' ), $current_page_link ),
            'name' => esc_html__( 'Edit', 'masvideos' ),
        ),
        'delete' => array(
            'url'  => wp_nonce_url( add_query_arg( array( 'post' => $obj->ID, 'action' => 'trash' ), $current_page_link ), 'masvideos-delete-playlist_' . $obj->ID, 'masvideos-delete-playlist-nonce' ),
            'name' => esc_html__( 'Delete', 'masvideos' ),
        ),
    );

    return apply_filters( 'masvideos_my_account_playlists_actions', $actions, $obj );
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

/**
 * Get Upload Video fields.
 *
 * @since   1.0.4
 * @return  array
 */
function masvideos_get_edit_video_fields() {
    $status_options = array(
        'publish'   => __( 'Publish', 'masvideos' ),
        'pending'   => __( 'Pending', 'masvideos' ),
        'draft'     => __( 'Draft', 'masvideos' ),
        'private'   => __( 'Private', 'masvideos' ),
    );

    if( ! apply_filters( 'masvideos_allow_users_to_publish_video', false ) ) {
        unset( $status_options['publish'] );
    }

    $fields = array(
        'title'         => array(
            'label'        => __( 'Title', 'masvideos' ),
            'required'     => true,
            'class'        => array( 'form-row-title' ),
            'priority'     => 10,
        ),
        'description'   => array(
            'type'         => 'textarea',
            'label'        => __( 'Description', 'masvideos' ),
            'required'     => false,
            'class'        => array( 'form-row-description' ),
            'priority'     => 20,
        ),
        'category_ids'  => array(
            'type'         => 'term-multiselect',
            'taxonomy'     => 'video_cat',
            'label'        => __( 'Categories', 'masvideos' ),
            'required'     => false,
            'class'        => array( 'form-row-category_ids' ),
            'priority'     => 30,
        ),
        'tag_ids'  => array(
            'type'         => apply_filters( 'masvideos_allow_users_to_create_tag', false ) ? 'tag-multiselect' : 'term-multiselect',
            'taxonomy'     => 'video_tag',
            'label'        => __( 'Tags', 'masvideos' ),
            'required'     => false,
            'class'        => array( 'form-row-tag_ids' ),
            'priority'     => 40,
        ),
        'status'  => array(
            'type'         => 'select',
            'options'      => $status_options,
            'label'        => __( 'Privacy', 'masvideos' ),
            'required'     => true,
            'class'        => array( 'form-row-status' ),
            'priority'     => 50,
        ),
        'reviews_allowed'  => array(
            'type'         => 'checkbox',
            'label'        => __( 'Enable Video Comments', 'masvideos' ),
            'required'     => false,
            'class'        => array( 'form-row-reviews_allowed' ),
            'priority'     => 55,
        ),
        'video_choice'  => array(
            'type'         => 'select',
            'options'      => array(
                'video_file'    => __( 'Upload Video', 'masvideos' ),
                'video_embed'   => __( 'Embed Video', 'masvideos' ),
                'video_url'     => __( 'Video URL', 'masvideos' ),
            ),
            'label'        => __( 'Video Method', 'masvideos' ),
            'required'     => false,
            'class'        => array( 'form-row-video_choice' ),
            'priority'     => 60,
        ),
        'video_attachment_id'   => array(
            'type'         => 'video',
            'label'        => __( 'Video', 'masvideos' ),
            'required'     => false,
            'class'        => array( 'hide show_if_video_file form-row-video_attachment_id', 'form-field' ),
            'priority'     => 65,
        ),
        'video_embed_content'   => array(
            'type'         => 'textarea',
            'label'        => __( 'Embed Video', 'masvideos' ),
            'required'     => false,
            'class'        => array( 'hide show_if_video_embed form-row-video_embed_content', 'form-field' ),
            'priority'     => 70,
        ),
        'video_url_link'   => array(
            'type'         => 'text',
            'label'        => __( 'Video URL', 'masvideos' ),
            'required'     => false,
            'class'        => array( 'hide show_if_video_url form-row-video_url_link', 'form-field' ),
            'priority'     => 75,
        ),
        'image_id'         => array(
            'type'         => 'image',
            'label'        => __( 'Image', 'masvideos' ),
            'required'     => false,
            'class'        => array( 'form-row-image_id', 'form-field' ),
            'priority'     => 80,
        ),
        'gallery_image_ids'=> array(
            'type'         => 'video-gallery-image',
            'label'        => __( 'Gallery Image', 'masvideos' ),
            'required'     => false,
            'class'        => array( 'form-row-gallery_image_ids' ),
            'priority'     => 90,
        ),
    );

    return apply_filters( 'masvideos_upload_video_fields', $fields );
}

/**
 * User media attachments restriction.
 *
 * @since   1.0.4
 * @return  array
 */
function masvideos_show_current_user_attachments( $query = array() ) {
    $current_user = wp_get_current_user();
    if( in_array( 'contributor', $current_user->roles ) && $current_user->ID ) {
        $query['author'] = $current_user->ID;
    }

    return $query;
}

add_filter( 'ajax_query_attachments_args', 'masvideos_show_current_user_attachments', 10 );