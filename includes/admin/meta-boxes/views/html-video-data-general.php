<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div id="general_video_data" class="panel masvideos_options_panel">

    <div class="options_group">
        <?php
        masvideos_wp_text_input(
            array(
                'id'          => '_video_url',
                // 'value'       => is_callable( array( $video_object, 'get_video_url' ) ) ? $video_object->get_video_url( 'edit' ) : '',
                'label'       => __( 'Video URL', 'masvideos' ),
                'placeholder' => 'http://',
                'description' => __( 'Enter the external URL to the video.', 'masvideos' ),
            )
        );
        ?>
    </div>

    <?php do_action( 'masvideos_video_options_general_video_data' ); ?>
</div>
