<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$person_object = masvideos_get_person( $person['id'] );

if ( ! is_object( $person_object ) ) {
    return;
}

?>
<div data-person_id="<?php echo esc_attr( $person['id'] ); ?>" class="masvideos_crew_person masvideos-metabox closed <?php echo esc_attr( implode( ' ', $metabox_class ) ); ?>" rel="<?php echo esc_attr( $person['position'] ); ?>">
    <h3>
        <a href="#" class="remove_row delete"><?php esc_html_e( 'Remove', 'masvideos' ); ?></a>
        <div class="handlediv" title="<?php esc_attr_e( 'Click to toggle', 'masvideos' ); ?>"></div>
        <strong class="person_id"><?php echo $person_object->get_name(); ?></strong>
    </h3>
    <div class="masvideos_crew_person_data masvideos-metabox-content">
        <table cellpadding="0" cellspacing="0">
            <tbody>
                <tr>
                    <td class="person_id">
                        <label><?php esc_html_e( 'Name', 'masvideos' ); ?>:</label>

                        <strong><?php echo $person_object->get_name(); ?></strong>
                        <input type="hidden" name="crew_person_ids[<?php echo esc_attr( $i ); ?>]" value="<?php echo esc_attr( $person['id'] ); ?>" />
                        <input type="hidden" name="crew_person_position[<?php echo esc_attr( $i ); ?>]" class="person_position" value="<?php echo esc_attr( $person['position'] ); ?>" />
                    </td>
                    <td>
                        <label><?php esc_html_e( 'Department', 'masvideos' ); ?>:</label>
                        <select data-placeholder="<?php esc_attr_e( 'Select term', 'masvideos' ); ?>" class="person_categories masvideos-enhanced-select" name="crew_person_categories[<?php echo esc_attr( $i ); ?>]">
                            <?php

                            $all_terms = get_the_terms( $person['id'], 'person_cat' );

                            if ( $all_terms ) {
                                foreach ( $all_terms as $term ) {
                                    $option = isset( $person['category'] ) ? $person['category'] : '';
                                    echo '<option value="' . esc_attr( $term->term_id ) . '"' . masvideos_selected( $term->term_id, $option ) . '>' . esc_attr( apply_filters( 'masvideos_tv_show_crew_person_cat_term_name', $term->name, $term ) ) . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </td>
                    <td>
                        <?php
                            masvideos_wp_text_input(
                                array(
                                    'id'            => 'crew_person_jobs[' . $i . ']',
                                    'value'         => isset( $person['job'] ) ? $person['job'] : '',
                                    'label'         => __( 'Job', 'masvideos' ),
                                    'description'   => __( 'Enter the job name.', 'masvideos' ),
                                )
                            );
                        ?>
                    </td>
                </tr>
                <?php do_action( 'masvideos_after_tv_show_person_settings', $person, $i ); ?>
            </tbody>
        </table>
    </div>
</div>
