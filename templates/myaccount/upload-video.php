<?php
/**
 * Orders
 *
 * Shows upload video page.
 *
 * This template can be overridden by copying it to yourtheme/masvideos/myaccount/upload-video.php.
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

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

do_action( 'masvideos_before_edit_upload_video_form' ); ?>

<form method="post">

    <h3><?php echo esc_html( $title ); ?></h3><?php // @codingStandardsIgnoreLine ?>

    <div class="masvideos-upload-video-fields">
        <?php do_action( "masvideos_before_edit_upload_video" ); ?>

        <div class="masvideos-upload-video-fields__field-wrapper">
            <?php foreach ( $fields as $key => $field ) {
                $value = isset( $field['value'] ) ? $field['value'] : null;
                masvideos_form_field( $key, $field, masvideos_get_post_data_by_key( $key, $value ) );
            } ?>
        </div>

        <?php do_action( "masvideos_after_edit_upload_video" ); ?>

        <p>
            <button type="submit" class="button" name="save_video" value="<?php echo esc_attr( $button_text ); ?>"><?php echo esc_html( $button_text ); ?></button>
            <?php wp_nonce_field( 'masvideos-upload_video', 'masvideos-upload-video-nonce' ); ?>
            <input type="hidden" name="action" value="upload_video" />
        </p>
    </div>

</form>

<?php do_action( 'masvideos_after_edit_upload_video_form' ); ?>
