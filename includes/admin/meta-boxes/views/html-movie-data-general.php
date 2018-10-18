<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div id="general_movie_data" class="panel masvideos_options_panel">

    <div class="options_group">
        <?php
        masvideos_wp_text_input(
            array(
                'id'          => '_movie_url',
                // 'value'       => is_callable( array( $movie_object, 'get_movie_url' ) ) ? $movie_object->get_movie_url( 'edit' ) : '',
                'label'       => __( 'Movie URL', 'masvideos' ),
                'placeholder' => 'http://',
                'description' => __( 'Enter the external URL to the movie.', 'masvideos' ),
            )
        );
        ?>
    </div>

    <?php do_action( 'masvideos_movie_options_general_movie_data' ); ?>
</div>
