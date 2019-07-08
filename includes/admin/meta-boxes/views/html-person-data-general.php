<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div id="general_person_data" class="panel masvideos_options_panel">

    <div class="options_group">
        <?php
        masvideos_wp_text_input(
            array(
                'id'            => '_also_known_as',
                'value'         => is_callable( array( $person_object, 'get_also_known_as' ) ) ? $person_object->get_also_known_as( 'edit' ) : '',
                'label'         => __( 'Also known as', 'masvideos' ),
                'description'   => __( 'Enter alternate names of the person.', 'masvideos' ),
            )
        );

        masvideos_wp_text_input(
            array(
                'id'            => '_place_of_birth',
                'value'         => is_callable( array( $person_object, 'get_place_of_birth' ) ) ? $person_object->get_place_of_birth( 'edit' ) : '',
                'label'         => __( 'Birth Place', 'masvideos' ),
                'description'   => __( 'Enter Birth Place of the person.', 'masvideos' ),
            )
        );

        masvideos_wp_date_picker(
            array(
                'id'            => '_birthday',
                'value'         => $person_object->get_birthday( 'edit' ) && ( $date = $person_object->get_birthday( 'edit' )->getOffsetTimestamp() ) ? date_i18n( 'Y-m-d', $date ) : '',
                'label'         => __( 'Birth Date', 'masvideos' ),
                'description'   => __( 'Enter the birth date of the person.', 'masvideos' ),
                'wrapper_class' => 'person_date_picker',
            )
        );

        masvideos_wp_date_picker(
            array(
                'id'            => '_deathday',
                'value'         => $person_object->get_deathday( 'edit' ) && ( $date = $person_object->get_deathday( 'edit' )->getOffsetTimestamp() ) ? date_i18n( 'Y-m-d', $date ) : '',
                'label'         => __( 'Death Date', 'masvideos' ),
                'description'   => __( 'Enter the death date of the person.', 'masvideos' ),
                'wrapper_class' => 'person_date_picker',
            )
        );

        masvideos_wp_text_input(
            array(
                'id'            => '_imdb_id',
                'value'         => is_callable( array( $person_object, 'get_imdb_id' ) ) ? $person_object->get_imdb_id( 'edit' ) : '',
                'label'         => __( 'IMDB ID', 'masvideos' ),
                'description'   => __( 'Enter IMDB ID of the person.', 'masvideos' ),
            )
        );

        masvideos_wp_text_input(
            array(
                'id'            => '_tmdb_id',
                'value'         => is_callable( array( $person_object, 'get_tmdb_id' ) ) ? $person_object->get_tmdb_id( 'edit' ) : '',
                'label'         => __( 'TMDB ID', 'masvideos' ),
                'description'   => __( 'Enter TMDB ID of the person.', 'masvideos' ),
            )
        );

        ?>
    </div>

    <div class="options_group">
        <?php
        masvideos_wp_radio(
            array(
                'id'            => '_catalog_visibility',
                'value'         => is_callable( array( $person_object, 'get_catalog_visibility' ) ) ? $person_object->get_catalog_visibility() : '',
                'label'         => __( 'Catalog visibility', 'masvideos' ),
                'options'       => masvideos_get_person_visibility_options(),
                'description'   => __( 'This setting determines which catalog pages person will be listed on.', 'masvideos' ),
            )
        );

        masvideos_wp_checkbox(
            array(
                'id'            => '_featured',
                'value'         => is_callable( array( $person_object, 'get_featured' ) ) ? masvideos_bool_to_string( $person_object->get_featured() ) : '',
                'label'         => __( 'Featured', 'masvideos' ),
                'description'   => __( 'This is a featured person.', 'masvideos' ),
            )
        );
        ?>
    </div>

    <?php do_action( 'masvideos_person_options_general_person_data' ); ?>
</div>
