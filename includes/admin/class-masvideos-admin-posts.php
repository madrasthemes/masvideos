<?php
/**
 * Handles posts in admin
 *
 * @class    MasVideos_Admin_Posts
 * @version  1.0.0
 * @package  MasVideos/Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * MasVideos_Admin_Posts class.
 */
class MasVideos_Admin_Posts {

    /**
     * Constructor.
     */
    public function __construct() {
        // Add columns for movie
        add_filter( 'manage_movie_posts_columns', array( $this, 'add_feature_image_columns' ) );
        add_action( 'manage_movie_posts_custom_column', array( $this, 'add_feature_image_column' ), 10, 2 );

        // Add columns for video
        add_filter( 'manage_video_posts_columns', array( $this, 'add_feature_image_columns' ) );
        add_action( 'manage_video_posts_custom_column', array( $this, 'add_feature_image_column' ), 10, 2 );

        // Add columns for tv show
        add_filter( 'manage_tv_show_posts_columns', array( $this, 'add_feature_image_columns' ) );
        add_action( 'manage_tv_show_posts_custom_column', array( $this, 'add_feature_image_column' ), 10, 2 );

        // Add columns for episode
        add_filter( 'manage_episode_posts_columns', array( $this, 'add_feature_image_columns' ) );
        add_action( 'manage_episode_posts_custom_column', array( $this, 'add_feature_image_column' ), 10, 2 );

        // Add columns for person
        add_filter( 'manage_person_posts_columns', array( $this, 'add_feature_image_columns' ) );
        add_action( 'manage_person_posts_custom_column', array( $this, 'add_feature_image_column' ), 10, 2 );
    }

    /**
     * Thumbnail column added to movie, video, tv show, episode and person  admin.
     *
     * @param mixed $columns
     * @return array
     */
    public function add_feature_image_columns( $columns ) {
        $new_columns = array();

        if ( isset( $columns['cb'] ) ) {
            $new_columns['cb'] = $columns['cb'];
            unset( $columns['cb'] );
        }

        $new_columns['thumb'] = __( 'Image', 'masvideos' );

        $columns = array_merge( $new_columns, $columns );

        return $columns;
    }

    /**
     * Thumbnail column value added to movie, video, tv show, episode and person admin.
     *
     * @param string $columns
     * @param string $column
     * @param int    $id
     *
     * @return string
     */
    public function add_feature_image_column( $column, $id ) {
        if ( 'thumb' === $column ) {

            $thumbnail_id = get_post_thumbnail_id( $id );

            if ( $thumbnail_id ) {
                $image = wp_get_attachment_thumb_url( $thumbnail_id );
            } else {
                $image = masvideos_placeholder_img_src();
            }

            // Prevent esc_url from breaking spaces in urls for image embeds. Ref: https://core.trac.wordpress.org/ticket/23605
            $image    = str_replace( ' ', '%20', $image );
            echo '<img src="' . esc_url( $image ) . '" alt="' . esc_attr__( 'Thumbnail', 'masvideos' ) . '" class="wp-post-image" height="60" width="60" />';
        }
    }
}

new MasVideos_Admin_Posts();
