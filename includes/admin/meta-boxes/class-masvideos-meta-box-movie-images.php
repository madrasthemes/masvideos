<?php
/**
 * Video Images
 *
 * Display the movie images meta box.
 *
 * @category    Admin
 * @package  MasVideos/Admin/Meta Boxes
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * MasVideos_Meta_Box_Movie_Images Class.
 */
class MasVideos_Meta_Box_Movie_Images {

    /**
     * Output the metabox.
     *
     * @param WP_Post $post
     */
    public static function output( $post ) {
        global $thepostid, $movie_object;

        $thepostid      = $post->ID;
        $movie_object = $thepostid ? masvideos_get_movie( $thepostid ) : new MasVideos_Movie();
        wp_nonce_field( 'masvideos_save_data', 'masvideos_meta_nonce' );
        ?>
        <div id="movie_images_container">
            <ul class="movie_images">
                <?php
                $movie_image_gallery = $movie_object->get_gallery_image_ids( 'edit' );

                $attachments         = array_filter( $movie_image_gallery );
                $update_meta         = false;
                $updated_gallery_ids = array();

                if ( ! empty( $attachments ) ) {
                    foreach ( $attachments as $attachment_id ) {
                        $attachment = wp_get_attachment_image( $attachment_id, 'thumbnail' );

                        // if attachment is empty skip.
                        if ( empty( $attachment ) ) {
                            $update_meta = true;
                            continue;
                        }

                        echo '<li class="image" data-attachment_id="' . esc_attr( $attachment_id ) . '">
                                ' . $attachment . '
                                <ul class="actions">
                                    <li><a href="#" class="delete tips" data-tip="' . esc_attr__( 'Delete image', 'masvideos' ) . '">' . __( 'Delete', 'masvideos' ) . '</a></li>
                                </ul>
                            </li>';

                        // rebuild ids to be saved.
                        $updated_gallery_ids[] = $attachment_id;
                    }

                    // need to update movie meta to set new gallery ids
                    if ( $update_meta ) {
                        update_post_meta( $post->ID, '_movie_image_gallery', implode( ',', $updated_gallery_ids ) );
                    }
                }
                ?>
            </ul>

            <input type="hidden" id="movie_image_gallery" name="movie_image_gallery" value="<?php echo esc_attr( implode( ',', $updated_gallery_ids ) ); ?>" />

        </div>
        <p class="add_movie_images hide-if-no-js">
            <a href="#" data-choose="<?php esc_attr_e( 'Add images to movie gallery', 'masvideos' ); ?>" data-update="<?php esc_attr_e( 'Add to gallery', 'masvideos' ); ?>" data-delete="<?php esc_attr_e( 'Delete image', 'masvideos' ); ?>" data-text="<?php esc_attr_e( 'Delete', 'masvideos' ); ?>"><?php _e( 'Add movie gallery images', 'masvideos' ); ?></a>
        </p>
        <?php
    }

    /**
     * Save meta box data.
     *
     * @param int     $post_id
     * @param WP_Post $post
     */
    public static function save( $post_id, $post ) {
        $classname = MasVideos_Movie_Factory::get_movie_classname( $post_id );
        $movie = new $classname( $post_id );
        $attachment_ids = isset( $_POST['movie_image_gallery'] ) ? array_filter( explode( ',', masvideos_clean( $_POST['movie_image_gallery'] ) ) ) : array();

        $movie->set_gallery_image_ids( $attachment_ids );
        $movie->save();
    }
}
