<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$person_object = masvideos_get_person( $person['id'] );

if ( ! is_object( $person_object ) ) {
    return;
}

?>
<div data-person_id="<?php echo esc_attr( $person['id'] ); ?>" class="masvideos_cast_person masvideos-metabox closed <?php echo esc_attr( implode( ' ', $metabox_class ) ); ?>" rel="<?php echo esc_attr( $person['position'] ); ?>">
    <h3>
        <a href="#" class="remove_row delete"><?php esc_html_e( 'Remove', 'masvideos' ); ?></a>
        <div class="handlediv" title="<?php esc_attr_e( 'Click to toggle', 'masvideos' ); ?>"></div>
        <strong class="person_id"><?php echo $person_object->get_name(); ?></strong>
    </h3>
    <div class="masvideos_cast_person_data masvideos-metabox-content">
        <table cellpadding="0" cellspacing="0">
            <tbody>
                <tr>
                    <td class="person_id">
                        <label><?php esc_html_e( 'Name', 'masvideos' ); ?>:</label>

                        <strong><?php echo $person_object->get_name(); ?></strong>
                        <input type="hidden" name="cast_person_ids[<?php echo esc_attr( $i ); ?>]" value="<?php echo esc_attr( $person['id'] ); ?>" />
                        <input type="hidden" name="cast_person_position[<?php echo esc_attr( $i ); ?>]" class="person_position" value="<?php echo esc_attr( $person['position'] ); ?>" />
                    </td>
                    <td>
                        <?php
                            masvideos_wp_text_input(
                                array(
                                    'id'            => 'cast_person_characters[' . $i . ']',
                                    'value'         => isset( $person['character'] ) ? $person['character'] : '',
                                    'label'         => __( 'Character', 'masvideos' ),
                                    'description'   => __( 'Enter the character name.', 'masvideos' ),
                                )
                            );
                        ?>
                    </td>
                </tr>
                <?php do_action( 'masvideos_after_movie_person_settings', $person, $i ); ?>
            </tbody>
        </table>
    </div>
</div>
