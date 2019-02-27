<?php
/**
 * The template for displaying video playlist content within loops
 *
 * This template can be overridden by copying it to yourtheme/masvideos/content-video-playlist.php.
 *
 * HOWEVER, on occasion MasVideos will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @package MasVideos/Templates
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

global $video_playlist;

// Ensure visibility.
if ( empty( $video_playlist ) || ! $video_playlist->is_visible() ) {
    return;
}
?>
<div <?php masvideos_video_playlist_class(); ?>>
    <?php
    /**
     * Hook: masvideos_before_video_playlists_loop_item.
     *
     * @hooked masvideos_template_loop_video_playlist_link_open - 10
     */
    do_action( 'masvideos_before_video_playlists_loop_item' );

    /**
     * Hook: masvideos_before_video_playlists_loop_item_title.
     *
     * @hooked masvideos_show_video_playlist_loop_sale_flash - 10
     * @hooked masvideos_template_loop_video_playlist_thumbnail - 10
     */
    do_action( 'masvideos_before_video_playlists_loop_item_title' );

    /**
     * Hook: masvideos_video_playlists_loop_item_title.
     *
     * @hooked masvideos_template_loop_video_playlist_title - 10
     */
    do_action( 'masvideos_video_playlists_loop_item_title' );

    /**
     * Hook: masvideos_after_video_playlists_loop_item_title.
     *
     * @hooked masvideos_template_loop_rating - 5
     * @hooked masvideos_template_loop_price - 10
     */
    do_action( 'masvideos_after_video_playlists_loop_item_title' );

    /**
     * Hook: masvideos_after_video_playlists_loop_item.
     *
     * @hooked masvideos_template_loop_video_playlist_link_close - 5
     * @hooked masvideos_template_loop_add_to_cart - 10
     */
    do_action( 'masvideos_after_video_playlists_loop_item' );
    ?>
</div>
