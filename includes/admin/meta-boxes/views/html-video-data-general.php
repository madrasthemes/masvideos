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
                'value'         => is_callable( array( $video_object, 'get_video_choice' ) ) ? $video_object->get_video_choice( 'edit' ) : '',
                'label'         => __( 'Choose Video Method', 'masvideos' ),
                'options'       => array(
                    'video_file'    => __( 'Upload Video', 'masvideos' ),
                    'video_embed'   => __( 'Embed Video', 'masvideos' ),
                    'video_url'     => __( 'Video URL', 'masvideos' ),
                ),
                'class'         => 'show_hide_select',
            )
        );

        masvideos_wp_upload_video(
            array(
                'id'            => '_video_attachment_id',
                'value'         => is_callable( array( $video_object, 'get_video_attachment_id' ) ) ? $video_object->get_video_attachment_id( 'edit' ) : '',
                'label'         => __( 'Upload Video', 'masvideos' ),
                'placeholder'   => 'Upload your video file',
                'description'   => __( 'Upload your video file', 'masvideos' ),
                'wrapper_class' => 'show_if_video_file hide',
            )
        );

        masvideos_wp_embed_video(
            array(
                'id'            => '_video_embed_content',
                'value'         => is_callable( array( $video_object, 'get_video_embed_content' ) ) ? $video_object->get_video_embed_content( 'edit' ) : '',
                'label'         => __( 'Embed Video', 'masvideos' ),
                'description'   => __( 'Paste the embed code for the video.', 'masvideos' ),
                'wrapper_class' => 'show_if_video_embed hide',
            )
        );

        masvideos_wp_video_url(
            array(
                'id'            => '_video_url_link',
                'value'         => is_callable( array( $video_object, 'get_video_url_link' ) ) ? $video_object->get_video_url_link( 'edit' ) : '',
                'label'         => __( 'Video URL', 'masvideos' ),
                'placeholder'   => 'http://',
                'description'   => __( 'Enter the external URL to the video.', 'masvideos' ),
                'wrapper_class' => 'show_if_video_url hide',
            )
        );
        ?>
    </div>

    <div class="options_group">
        <?php
        masvideos_wp_radio(
            array(
                'id'            => '_catalog_visibility',
                'value'         => is_callable( array( $video_object, 'get_catalog_visibility' ) ) ? $video_object->get_catalog_visibility() : '',
                'label'         => __( 'Catalog visibility', 'masvideos' ),
                'options'       => masvideos_get_video_visibility_options(),
                'description'   => __( 'This setting determines which catalog pages video will be listed on.', 'masvideos' ),
            )
        );

        masvideos_wp_checkbox(
            array(
                'id'            => '_featured',
                'value'         => is_callable( array( $video_object, 'get_featured' ) ) ? masvideos_bool_to_string( $video_object->get_featured() ) : '',
                'label'         => __( 'Featured', 'masvideos' ),
                'description'   => __( 'This is a featured video.', 'masvideos' ),
            )
        );
        ?>
    </div>

    <?php do_action( 'masvideos_video_options_general_video_data' ); ?>
</div>
