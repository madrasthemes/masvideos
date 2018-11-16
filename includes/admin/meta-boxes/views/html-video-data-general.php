<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div id="general_video_data" class="panel masvideos_options_panel">

    <div class="options_group">
        <?php
        masvideos_wp_select(
            array(
                'id'            => '_video_choice',
                'label'         => __( 'Choose Video Method', 'masvideos' ),
                'options'       => array(
                    'video_file'    => __( 'Upload Video', 'woocommerce' ),
                    'video_url'     => __( 'Video URL', 'woocommerce' ),
                ),
                'class'         => 'show_hide_select',
            )
        );

        masvideos_wp_upload_video(
            array(
                'id'          => '_video_id',
                'value'       => is_callable( array( $video_object, 'get_video_id' ) ) ? $video_object->get_video_id( 'edit' ) : '',
                'label'       => __( 'Upload Video', 'masvideos' ),
                'placeholder' => 'Upload your video file',
                'description' => __( 'Upload your video file', 'masvideos' ),
                'wrapper_class' => 'show_if_video_file hide',
            )
        );

        masvideos_wp_text_input(
            array(
                'id'          => '_video_url',
                // 'value'       => is_callable( array( $video_object, 'get_video_url' ) ) ? $video_object->get_video_url( 'edit' ) : '',
                'label'       => __( 'Video URL', 'masvideos' ),
                'placeholder' => 'http://',
                'description' => __( 'Enter the external URL to the video.', 'masvideos' ),
                'wrapper_class' => 'show_if_video_url hide',
            )
        );

        ?>
    </div>

    <?php do_action( 'masvideos_video_options_general_video_data' ); ?>
</div>
