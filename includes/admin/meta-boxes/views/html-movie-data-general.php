<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div id="general_movie_data" class="panel masvideos_options_panel">

    <div class="options_group">
        <?php
        masvideos_wp_select(
            array(
                'id'            => '_movie_choice',
                'value'         => is_callable( array( $movie_object, 'get_movie_choice' ) ) ? $movie_object->get_movie_choice( 'edit' ) : '',
                'label'         => __( 'Choose Movie Method', 'masvideos' ),
                'options'       => array(
                    'movie_file'    => __( 'Upload Movie', 'woocommerce' ),
                    'movie_embed'   => __( 'Embed Movie', 'woocommerce' ),
                    'movie_url'     => __( 'Movie URL', 'masvideos' ),
                ),
                'class'         => 'show_hide_select',
            )
        );

        masvideos_wp_upload_video(
            array(
                'id'            => '_movie_attachment_id',
                'value'         => is_callable( array( $movie_object, 'get_movie_attachment_id' ) ) ? $movie_object->get_movie_attachment_id( 'edit' ) : '',
                'label'         => __( 'Upload Movie', 'masvideos' ),
                'placeholder'   => 'Upload your movie file',
                'description'   => __( 'Upload your movie file', 'masvideos' ),
                'wrapper_class' => 'show_if_movie_file hide',
            )
        );

        masvideos_wp_embed_video(
            array(
                'id'            => '_movie_embed_content',
                'value'         => is_callable( array( $movie_object, 'get_movie_embed_content' ) ) ? $movie_object->get_movie_embed_content( 'edit' ) : '',
                'label'         => __( 'Embed Movie URL', 'masvideos' ),
                'description'   => __( 'Enter the external URL to the movie.', 'masvideos' ),
                'wrapper_class' => 'show_if_movie_embed hide',
            )
        );

        masvideos_wp_video_url(
            array(
                'id'            => '_movie_url_link',
                'value'         => is_callable( array( $movie_object, 'get_movie_url_link' ) ) ? $movie_object->get_movie_url_link( 'edit' ) : '',
                'label'         => __( 'Movie URL', 'masvideos' ),
                'placeholder'   => 'http://',
                'description'   => __( 'Enter the external URL to the video.', 'masvideos' ),
                'wrapper_class' => 'show_if_movie_url hide',
            )
        );
        ?>
    </div>

    <?php do_action( 'masvideos_movie_options_general_movie_data' ); ?>
</div>
