<?php
/**
 * Video Images
 *
 * Display the video images meta box.
 *
 * @category    Admin
 * @package  MasVideos/Admin/Meta Boxes
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * MasVideos_Meta_Box_Video_Images Class.
 */
class MasVideos_Meta_Box_Video_Images {

    /**
     * Output the metabox.
     *
     * @param WP_Post $post
     */
    public static function output( $post ) {
        global $thepostid, $video_object;

        $thepostid      = $post->ID;
        $video_object = $thepostid ? masvideos_get_video( $thepostid ) : new MasVideos_Video();
        wp_nonce_field( 'masvideos_save_data', 'masvideos_meta_nonce' );
        ?>
        <div id="video_images_container">
            <ul class="video_images">
                <?php
                $video_image_gallery = $video_object->get_gallery_image_ids( 'edit' );

                $attachments         = array_filter( $video_image_gallery );
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

                    // need to update video meta to set new gallery ids
                    if ( $update_meta ) {
                        update_post_meta( $post->ID, '_video_image_gallery', implode( ',', $updated_gallery_ids ) );
                    }
                }
                ?>
            </ul>

            <input type="hidden" id="video_image_gallery" name="video_image_gallery" value="<?php echo esc_attr( implode( ',', $updated_gallery_ids ) ); ?>" />

        </div>
        <p class="add_video_images hide-if-no-js">
            <a href="#" data-choose="<?php esc_attr_e( 'Add images to video gallery', 'masvideos' ); ?>" data-update="<?php esc_attr_e( 'Add to gallery', 'masvideos' ); ?>" data-delete="<?php esc_attr_e( 'Delete image', 'masvideos' ); ?>" data-text="<?php esc_attr_e( 'Delete', 'masvideos' ); ?>"><?php _e( 'Add video gallery images', 'masvideos' ); ?></a>
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
        $classname = MasVideos_Video_Factory::get_video_classname( $post_id );
        $video = new $classname( $post_id );
        $attachment_ids = isset( $_POST['video_image_gallery'] ) ? array_filter( explode( ',', masvideos_clean( $_POST['video_image_gallery'] ) ) ) : array();

        $video->set_gallery_image_ids( $attachment_ids );
        $video->save();
    }
}
