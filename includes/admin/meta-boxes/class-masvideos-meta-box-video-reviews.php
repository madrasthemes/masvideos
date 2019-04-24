<?php
/**
 * Video Reviews
 *
 * Functions for displaying video reviews data meta box.
 *
 * @package MasVideos/Admin/Meta Boxes
 */

defined( 'ABSPATH' ) || exit;

/**
 * MasVideos_Meta_Box_Video_Reviews
 */
class MasVideos_Meta_Box_Video_Reviews {

    /**
     * Output the metabox.
     *
     * @param object $comment Comment being shown.
     */
    public static function output( $comment ) {
        wp_nonce_field( 'masvideos_save_data', 'masvideos_meta_nonce' );

        $current = get_comment_meta( $comment->comment_ID, 'rating', true );
        ?>
        <select name="rating" id="rating">
            <?php
            for ( $rating = 1; $rating <= 10; $rating ++ ) {
                printf( '<option value="%1$s"%2$s>%1$s</option>', $rating, selected( $current, $rating, false ) ); // WPCS: XSS ok.
            }
            ?>
        </select>
        <?php
    }

    /**
     * Save meta box data
     *
     * @param mixed $data Data to save.
     * @return mixed
     */
    public static function save( $data ) {
        // Not allowed, return regular value without updating meta.
        if ( ! isset( $_POST['masvideos_meta_nonce'], $_POST['rating'] ) || ! wp_verify_nonce( wp_unslash( $_POST['masvideos_meta_nonce'] ), 'masvideos_save_data' ) ) { // WPCS: input var ok, sanitization ok.
            return $data;
        }

        if ( $_POST['rating'] > 10 || $_POST['rating'] < 0 ) { // WPCS: input var ok.
            return $data;
        }

        $comment_id = $data['comment_ID'];

        update_comment_meta( $comment_id, 'rating', intval( wp_unslash( $_POST['rating'] ) ) ); // WPCS: input var ok.

        // Return regular value after updating.
        return $data;
    }
}
