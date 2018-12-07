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
                    'movie_file'    => __( 'Upload Movie', 'masvideos' ),
                    'movie_embed'   => __( 'Embed Movie', 'masvideos' ),
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

    <div class="options_group">
        <?php
        $movie_release_date = $movie_object->get_movie_relese_date( 'edit' ) && ( $date = $movie_object->get_movie_relese_date( 'edit' )->getOffsetTimestamp() ) ? date_i18n( 'Y-m-d', $date ) : '';

        echo '<p class="form-field movie_release_date_fields">
                <label for="_movie_release_date">' . esc_html__( 'Movie Release Date', 'masvideos' ) . '</label>
                <input type="text" class="short" name="_movie_release_date" id="_movie_release_date" value="' . esc_attr( $movie_release_date ) . '" placeholder="' . ' YYYY-MM-DD" maxlength="10" pattern="' . esc_attr( apply_filters( 'masvideos_date_input_html_pattern', '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])' ) ) . '" />
            </p>';
        ?>
    </div>

    <?php do_action( 'masvideos_movie_options_general_movie_data' ); ?>
</div>
