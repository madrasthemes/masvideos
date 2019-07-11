<?php
/**
 * MasVideos Meta Boxes
 *
 * Sets up the write panels used by videos, movies and orders (custom post types).
 *
 * @category    Admin
 * @package     MasVideos/Admin/Meta Boxes
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * MasVideos_Admin_Meta_Boxes.
 */
class MasVideos_Admin_Meta_Boxes {

    /**
     * Is meta boxes saved once?
     *
     * @var boolean
     */
    private static $saved_meta_boxes = false;

    /**
     * Meta box error messages.
     *
     * @var array
     */
    public static $meta_box_errors = array();

    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );
        add_action( 'save_post', array( $this, 'save_meta_boxes' ), 1, 2 );

        // Save Person Meta Boxes.
        add_action( 'masvideos_process_person_meta', 'MasVideos_Meta_Box_Person_Data::save', 10, 2 );
        // add_action( 'masvideos_process_person_meta', 'MasVideos_Meta_Box_Person_Images::save', 20, 2 );

        // Save Episode Meta Boxes.
        add_action( 'masvideos_process_episode_meta', 'MasVideos_Meta_Box_Episode_Data::save', 10, 2 );
        // add_action( 'masvideos_process_episode_meta', 'MasVideos_Meta_Box_Episode_Images::save', 20, 2 );

        // Save Rating Meta Boxes.
        add_filter( 'wp_update_comment_data', 'MasVideos_Meta_Box_Episode_Reviews::save', 1 );

        // Save TV Show Meta Boxes.
        add_action( 'masvideos_process_tv_show_meta', 'MasVideos_Meta_Box_TV_Show_Data::save', 10, 2 );
        // add_action( 'masvideos_process_tv_show_meta', 'MasVideos_Meta_Box_TV_Show_Images::save', 20, 2 );

        // Save Rating Meta Boxes.
        add_filter( 'wp_update_comment_data', 'MasVideos_Meta_Box_Video_Reviews::save', 1 );

        // Save Video Meta Boxes.
        add_action( 'masvideos_process_video_meta', 'MasVideos_Meta_Box_Video_Data::save', 10, 2 );
        add_action( 'masvideos_process_video_meta', 'MasVideos_Meta_Box_Video_Images::save', 20, 2 );

        // Save Rating Meta Boxes.
        add_filter( 'wp_update_comment_data', 'MasVideos_Meta_Box_Video_Reviews::save', 1 );

        // Save Movie Meta Boxes.
        add_action( 'masvideos_process_movie_meta', 'MasVideos_Meta_Box_Movie_Data::save', 10, 2 );
        add_action( 'masvideos_process_movie_meta', 'MasVideos_Meta_Box_Movie_Images::save', 20, 2 );

        // Save Rating Meta Boxes.
        add_filter( 'wp_update_comment_data', 'MasVideos_Meta_Box_Movie_Reviews::save', 1 );

        // Error handling (for showing errors from meta boxes on next page load).
        add_action( 'admin_notices', array( $this, 'output_errors' ) );
        add_action( 'shutdown', array( $this, 'save_errors' ) );
    }

    /**
     * Add an error message.
     *
     * @param string $text
     */
    public static function add_error( $text ) {
        self::$meta_box_errors[] = $text;
    }

    /**
     * Save errors to an option.
     */
    public function save_errors() {
        update_option( 'masvideos_meta_box_errors', self::$meta_box_errors );
    }

    /**
     * Show any stored error messages.
     */
    public function output_errors() {
        $errors = array_filter( (array) get_option( 'masvideos_meta_box_errors' ) );

        if ( ! empty( $errors ) ) {

            echo '<div id="masvideos_errors" class="error notice is-dismissible">';

            foreach ( $errors as $error ) {
                echo '<p>' . wp_kses_post( $error ) . '</p>';
            }

            echo '</div>';

            // Clear
            delete_option( 'masvideos_meta_box_errors' );
        }
    }

    /**
     * Add WC Meta boxes.
     */
    public function add_meta_boxes() {
        $screen    = get_current_screen();
        $screen_id = $screen ? $screen->id : '';

        // Persons.
        add_meta_box( 'postexcerpt', __( 'Person short description', 'masvideos' ), 'MasVideos_Meta_Box_Person_Short_Description::output', 'person', 'normal' );
        add_meta_box( 'masvideos-person-data', __( 'Person data', 'masvideos' ), 'MasVideos_Meta_Box_Person_Data::output', 'person', 'normal', 'high' );
        // add_meta_box( 'masvideos-person-images', __( 'Person gallery', 'masvideos' ), 'MasVideos_Meta_Box_Person_Images::output', 'person', 'side', 'low' );

        // Episodes.
        add_meta_box( 'postexcerpt', __( 'Episode short description', 'masvideos' ), 'MasVideos_Meta_Box_Episode_Short_Description::output', 'episode', 'normal' );
        add_meta_box( 'masvideos-episode-data', __( 'Episode data', 'masvideos' ), 'MasVideos_Meta_Box_Episode_Data::output', 'episode', 'normal', 'high' );
        // add_meta_box( 'masvideos-episode-images', __( 'Episode gallery', 'masvideos' ), 'MasVideos_Meta_Box_Episode_Images::output', 'episode', 'side', 'low' );

        // Comment rating.
        if ( 'comment' === $screen_id && isset( $_GET['c'] ) && metadata_exists( 'comment', $_GET['c'], 'rating' ) ) {
            add_meta_box( 'masvideos-rating', __( 'Rating', 'masvideos' ), 'MasVideos_Meta_Box_Episode_Reviews::output', 'comment', 'normal', 'high' );
        }

        // TV Shows.
        add_meta_box( 'postexcerpt', __( 'TV Show short description', 'masvideos' ), 'MasVideos_Meta_Box_TV_Show_Short_Description::output', 'tv_show', 'normal' );
        add_meta_box( 'masvideos-tv-show-data', __( 'TV Show data', 'masvideos' ), 'MasVideos_Meta_Box_TV_Show_Data::output', 'tv_show', 'normal', 'high' );
        // add_meta_box( 'masvideos-tv-show-images', __( 'TV Show gallery', 'masvideos' ), 'MasVideos_Meta_Box_TV_Show_Images::output', 'tv_show', 'side', 'low' );

        // Comment rating.
        if ( 'comment' === $screen_id && isset( $_GET['c'] ) && metadata_exists( 'comment', $_GET['c'], 'rating' ) ) {
            add_meta_box( 'masvideos-rating', __( 'Rating', 'masvideos' ), 'MasVideos_Meta_Box_TV_Show_Reviews::output', 'comment', 'normal', 'high' );
        }

        // Videos.
        add_meta_box( 'postexcerpt', __( 'Video short description', 'masvideos' ), 'MasVideos_Meta_Box_Video_Short_Description::output', 'video', 'normal' );
        add_meta_box( 'masvideos-video-data', __( 'Video data', 'masvideos' ), 'MasVideos_Meta_Box_Video_Data::output', 'video', 'normal', 'high' );
        add_meta_box( 'masvideos-video-images', __( 'Video Gallery', 'masvideos' ), 'MasVideos_Meta_Box_Video_Images::output', 'video', 'side', 'low' );

        // Comment rating.
        if ( 'comment' === $screen_id && isset( $_GET['c'] ) && metadata_exists( 'comment', $_GET['c'], 'rating' ) ) {
            add_meta_box( 'masvideos-rating', __( 'Rating', 'masvideos' ), 'MasVideos_Meta_Box_Video_Reviews::output', 'comment', 'normal', 'high' );
        }

        // Movies.
        add_meta_box( 'postexcerpt', __( 'Movie short description', 'masvideos' ), 'MasVideos_Meta_Box_Movie_Short_Description::output', 'movie', 'normal' );
        add_meta_box( 'masvideos-movie-data', __( 'Movie data', 'masvideos' ), 'MasVideos_Meta_Box_Movie_Data::output', 'movie', 'normal', 'high' );
        add_meta_box( 'masvideos-movie-images', __( 'Movie gallery', 'masvideos' ), 'MasVideos_Meta_Box_Movie_Images::output', 'movie', 'side', 'low' );

        // Comment rating.
        if ( 'comment' === $screen_id && isset( $_GET['c'] ) && metadata_exists( 'comment', $_GET['c'], 'rating' ) ) {
            add_meta_box( 'masvideos-rating', __( 'Rating', 'masvideos' ), 'MasVideos_Meta_Box_Movie_Reviews::output', 'comment', 'normal', 'high' );
        }
    }

    /**
     * Check if we're saving, the trigger an action based on the post type.
     *
     * @param  int    $post_id
     * @param  object $post
     */
    public function save_meta_boxes( $post_id, $post ) {
        // $post_id and $post are required
        if ( empty( $post_id ) || empty( $post ) || self::$saved_meta_boxes ) {
            return;
        }

        // Dont' save meta boxes for revisions or autosaves
        if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
            return;
        }

        // Check the nonce
        if ( empty( $_POST['masvideos_meta_nonce'] ) || ! wp_verify_nonce( $_POST['masvideos_meta_nonce'], 'masvideos_save_data' ) ) {
            return;
        }

        // Check the post being saved == the $post_id to prevent triggering this call for other save_post events
        if ( empty( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id ) {
            return;
        }

        // Check user has permission to edit
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // We need this save event to run once to avoid potential endless loops. This would have been perfect:
        // remove_action( current_filter(), __METHOD__ );
        // But cannot be used due to https://github.com/masvideos/masvideos/issues/6485
        // When that is patched in core we can use the above. For now:
        self::$saved_meta_boxes = true;

        // Check the post type
        if ( in_array( $post->post_type, array( 'person', 'episode', 'tv_show', 'video', 'movie' ) ) ) {
            do_action( 'masvideos_process_' . $post->post_type . '_meta', $post_id, $post );
        }
    }
}

new MasVideos_Admin_Meta_Boxes();
