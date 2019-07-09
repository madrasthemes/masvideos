<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div id="general_tv_show_data" class="panel masvideos_options_panel">

    <div class="options_group">
        <?php
        masvideos_wp_radio(
            array(
                'id'            => '_catalog_visibility',
                'value'         => is_callable( array( $tv_show_object, 'get_catalog_visibility' ) ) ? $tv_show_object->get_catalog_visibility() : '',
                'label'         => __( 'Catalog visibility', 'masvideos' ),
                'options'       => masvideos_get_tv_show_visibility_options(),
                'description'   => __( 'This setting determines which catalog pages tv show will be listed on.', 'masvideos' ),
            )
        );

        masvideos_wp_checkbox(
            array(
                'id'            => '_featured',
                'value'         => is_callable( array( $tv_show_object, 'get_featured' ) ) ? masvideos_bool_to_string( $tv_show_object->get_featured() ) : '',
                'label'         => __( 'Featured', 'masvideos' ),
                'description'   => __( 'This is a featured tv show.', 'masvideos' ),
            )
        );

        masvideos_wp_text_input(
            array(
                'id'            => '_imdb_id',
                'value'         => is_callable( array( $tv_show_object, 'get_imdb_id' ) ) ? $tv_show_object->get_imdb_id( 'edit' ) : '',
                'label'         => __( 'IMDB ID', 'masvideos' ),
                'description'   => __( 'Enter IMDB ID of the tv show.', 'masvideos' ),
            )
        );

        masvideos_wp_text_input(
            array(
                'id'            => '_tmdb_id',
                'value'         => is_callable( array( $tv_show_object, 'get_tmdb_id' ) ) ? $tv_show_object->get_tmdb_id( 'edit' ) : '',
                'label'         => __( 'TMDB ID', 'masvideos' ),
                'description'   => __( 'Enter TMDB ID of the tv show.', 'masvideos' ),
            )
        );

        ?>
    </div>

    <?php do_action( 'masvideos_tv_show_options_general_tv_show_data' ); ?>
</div>
