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
                'class'         => 'short show_hide_select',
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
                'label'         => __( 'Embed Movie Content', 'masvideos' ),
                'description'   => __( 'Enter the embed content to the movie.', 'masvideos' ),
                'wrapper_class' => 'show_if_movie_embed hide',
            )
        );

        masvideos_wp_text_input(
            array(
                'id'            => '_movie_url_link',
                'value'         => is_callable( array( $movie_object, 'get_movie_url_link' ) ) ? $movie_object->get_movie_url_link( 'edit' ) : '',
                'label'         => __( 'Movie URL', 'masvideos' ),
                'placeholder'   => 'http://',
                'description'   => __( 'Enter the external URL to the video.', 'masvideos' ),
                'wrapper_class' => 'show_if_movie_url hide',
            )
        );

        masvideos_wp_checkbox(
            array(
                'id'            => '_movie_is_affiliate_link',
                'value'         => is_callable( array( $movie_object, 'get_movie_is_affiliate_link' ) ) ? masvideos_bool_to_string( $movie_object->get_movie_is_affiliate_link() ) : '',
                'label'         => __( 'Is Affiliate URL ?', 'masvideos' ),
                'wrapper_class' => 'show_if_movie_url hide',
            )
        );

        masvideos_wp_date_picker(
            array(
                'id'            => '_movie_release_date',
                'value'         => $movie_object->get_movie_release_date( 'edit' ) && ( $date = $movie_object->get_movie_release_date( 'edit' )->getOffsetTimestamp() ) ? date_i18n( 'Y-m-d', $date ) : '',
                'label'         => __( 'Movie Release Date', 'masvideos' ),
                'description'   => __( 'Enter the release date of the movie.', 'masvideos' ),
                'wrapper_class' => 'movie_date_picker',
            )
        );

        masvideos_wp_text_input(
            array(
                'id'            => '_movie_run_time',
                'value'         => is_callable( array( $movie_object, 'get_movie_run_time' ) ) ? $movie_object->get_movie_run_time( 'edit' ) : '',
                'label'         => __( 'Movie Time Dutration', 'masvideos' ),
                'description'   => __( 'Enter the movie run time duration.', 'masvideos' ),
            )
        );

        masvideos_wp_text_input(
            array(
                'id'            => '_movie_censor_rating',
                'value'         => is_callable( array( $movie_object, 'get_movie_censor_rating' ) ) ? $movie_object->get_movie_censor_rating( 'edit' ) : '',
                'label'         => __( 'Movie Censore Rating', 'masvideos' ),
                'description'   => __( 'Enter the movie censore rating.', 'masvideos' ),
            )
        );

        masvideos_wp_text_input(
            array(
                'id'            => '_imdb_id',
                'value'         => is_callable( array( $movie_object, 'get_imdb_id' ) ) ? $movie_object->get_imdb_id( 'edit' ) : '',
                'label'         => __( 'IMDB ID', 'masvideos' ),
                'description'   => __( 'Enter IMDB ID of the movie.', 'masvideos' ),
            )
        );

        masvideos_wp_text_input(
            array(
                'id'            => '_tmdb_id',
                'value'         => is_callable( array( $movie_object, 'get_tmdb_id' ) ) ? $movie_object->get_tmdb_id( 'edit' ) : '',
                'label'         => __( 'TMDB ID', 'masvideos' ),
                'description'   => __( 'Enter TMDB ID of the movie.', 'masvideos' ),
            )
        );

        ?>
    </div>

    <div class="options_group">
        <?php
        masvideos_wp_radio(
            array(
                'id'            => '_catalog_visibility',
                'value'         => is_callable( array( $movie_object, 'get_catalog_visibility' ) ) ? $movie_object->get_catalog_visibility() : '',
                'label'         => __( 'Catalog visibility', 'masvideos' ),
                'options'       => masvideos_get_movie_visibility_options(),
                'description'   => __( 'This setting determines which catalog pages movie will be listed on.', 'masvideos' ),
            )
        );

        masvideos_wp_checkbox(
            array(
                'id'            => '_featured',
                'value'         => is_callable( array( $movie_object, 'get_featured' ) ) ? masvideos_bool_to_string( $movie_object->get_featured() ) : '',
                'label'         => __( 'Featured', 'masvideos' ),
                'description'   => __( 'This is a featured movie.', 'masvideos' ),
            )
        );
        ?>
    </div>

    <?php do_action( 'masvideos_movie_options_general_movie_data' ); ?>
</div>
