<?php
/**
 * MasVideos Uninstall
 *
 * Uninstalling MasVideos deletes user roles, pages, tables, and options.
 *
 * @package MasVideos\Uninstaller
 * @version 1.0.0
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

global $wpdb;

/*
 * Only remove ALL movie, video, tv show and page data if MASVIDEOS_REMOVE_ALL_DATA constant is set to true in user's
 * wp-config.php. This is to prevent data loss when deleting the plugin from the backend
 * and to ensure only the site owner can perform this action.
 */
if ( defined( 'MASVIDEOS_REMOVE_ALL_DATA' ) && true === MASVIDEOS_REMOVE_ALL_DATA ) {
    include_once dirname( __FILE__ ) . '/includes/class-masvideos-install.php';

    // Roles + caps.
    MasVideos_Install::remove_roles();

    // Pages.
    wp_trash_post( get_option( 'masvideos_myaccount_page_id' ) );
    wp_trash_post( get_option( 'masvideos_upload_video_page_id' ) );
    wp_trash_post( get_option( 'masvideos_movies_page_id' ) );
    wp_trash_post( get_option( 'masvideos_tv_shows_page_id' ) );
    wp_trash_post( get_option( 'masvideos_videos_page_id' ) );
    wp_trash_post( get_option( 'masvideos_persons_page_id' ) );

    // Tables.
    MasVideos_Install::drop_tables();

    // Delete options.
    $wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'masvideos\_%';" );
    $wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'widget\_masvideos\_%';" );

    // Delete usermeta.
    $wpdb->query( "DELETE FROM $wpdb->usermeta WHERE meta_key LIKE 'masvideos\_%';" );

    // Delete posts + data.
    $wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type IN ( 'movie', 'movie_playlist', 'video', 'video_playlist', 'tv_show', 'tv_show_playlist', 'episode' );" );
    $wpdb->query( "DELETE meta FROM {$wpdb->postmeta} meta LEFT JOIN {$wpdb->posts} posts ON posts.ID = meta.post_id WHERE posts.ID IS NULL;" );

    $wpdb->query( "DELETE meta FROM {$wpdb->commentmeta} meta LEFT JOIN {$wpdb->comments} comments ON comments.comment_ID = meta.comment_id WHERE comments.comment_ID IS NULL;" );

    // Clear any cached data that has been removed.
    wp_cache_flush();
}
