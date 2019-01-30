<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div id="general_episode_data" class="panel masvideos_options_panel">

    <div class="options_group">
        <?php
        masvideos_wp_select(
            array(
                'id'            => '_episode_choice',
                'value'         => is_callable( array( $episode_object, 'get_episode_choice' ) ) ? $episode_object->get_episode_choice( 'edit' ) : '',
                'label'         => __( 'Choose Episode Method', 'masvideos' ),
                'options'       => array(
                    'episode_file'    => __( 'Upload Episode', 'masvideos' ),
                    'episode_embed'   => __( 'Embed Episode', 'masvideos' ),
                    'episode_url'     => __( 'Episode URL', 'masvideos' ),
                ),
                'class'         => 'show_hide_select',
            )
        );

        masvideos_wp_upload_video(
            array(
                'id'            => '_episode_attachment_id',
                'value'         => is_callable( array( $episode_object, 'get_episode_attachment_id' ) ) ? $episode_object->get_episode_attachment_id( 'edit' ) : '',
                'label'         => __( 'Upload Episode', 'masvideos' ),
                'placeholder'   => 'Upload your episode file',
                'description'   => __( 'Upload your episode file', 'masvideos' ),
                'wrapper_class' => 'show_if_episode_file hide',
            )
        );

        masvideos_wp_embed_video(
            array(
                'id'            => '_episode_embed_content',
                'value'         => is_callable( array( $episode_object, 'get_episode_embed_content' ) ) ? $episode_object->get_episode_embed_content( 'edit' ) : '',
                'label'         => __( 'Embed Episode URL', 'masvideos' ),
                'description'   => __( 'Enter the external URL to the episode.', 'masvideos' ),
                'wrapper_class' => 'show_if_episode_embed hide',
            )
        );

        masvideos_wp_video_url(
            array(
                'id'            => '_episode_url_link',
                'value'         => is_callable( array( $episode_object, 'get_episode_url_link' ) ) ? $episode_object->get_episode_url_link( 'edit' ) : '',
                'label'         => __( 'Episode URL', 'masvideos' ),
                'placeholder'   => 'http://',
                'description'   => __( 'Enter the external URL to the video.', 'masvideos' ),
                'wrapper_class' => 'show_if_episode_url hide',
            )
        );

        ?>
    </div>

    <div class="options_group">
        <?php
        masvideos_wp_radio(
            array(
                'id'            => '_catalog_visibility',
                'value'         => is_callable( array( $episode_object, 'get_catalog_visibility' ) ) ? $episode_object->get_catalog_visibility() : '',
                'label'         => __( 'Catalog visibility', 'masvideos' ),
                'options'       => masvideos_get_episode_visibility_options(),
                'description'   => __( 'This setting determines which catalog pages episode will be listed on.', 'masvideos' ),
            )
        );

        masvideos_wp_checkbox(
            array(
                'id'            => '_featured',
                'value'         => is_callable( array( $episode_object, 'get_featured' ) ) ? masvideos_bool_to_string( $episode_object->get_featured() ) : '',
                'label'         => __( 'Featured', 'masvideos' ),
                'description'   => __( 'This is a featured episode.', 'masvideos' ),
            )
        );
        ?>
    </div>

    <?php do_action( 'masvideos_episode_options_general_episode_data' ); ?>
</div>
