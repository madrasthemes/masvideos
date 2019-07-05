<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$person_object = masvideos_get_person( $person['id'] );

?>
<div data-person_id="<?php echo esc_attr( $person['id'] ); ?>" class="masvideos_person masvideos-metabox closed <?php echo esc_attr( implode( ' ', $metabox_class ) ); ?>" rel="<?php echo esc_attr( $i ); ?>">
    <h3>
        <a href="#" class="remove_row delete"><?php esc_html_e( 'Remove', 'masvideos' ); ?></a>
        <div class="handlediv" title="<?php esc_attr_e( 'Click to toggle', 'masvideos' ); ?>"></div>
        <strong class="person_id"><?php echo $person_object->get_name(); ?></strong>
    </h3>
    <div class="masvideos_person_data masvideos-metabox-content">
        <table cellpadding="0" cellspacing="0">
            <tbody>
                <tr>
                    <td class="person_id">
                        <label><?php esc_html_e( 'Name', 'masvideos' ); ?>:</label>

                        <strong><?php echo $person_object->get_name(); ?></strong>
                        <input type="hidden" name="person_ids[<?php echo esc_attr( $i ); ?>]" value="<?php echo esc_attr( $person['id'] ); ?>" />
                    </td>
                </tr>
                <?php
                    $all_terms = get_the_terms( $person['id'], 'person_cat' );
                    foreach ( $all_terms as $term ) {
                        $is_checked = false;
                        $title = '';
                        if( ! empty( $person['categoires'] ) ) {
                            foreach ( $person['categoires'] as $category ) {
                                if( $category['id'] == $term->term_id ) {
                                    $is_checked = true;
                                    $title = isset( $category['title'] ) ? $category['title'] : '';
                                    break;
                                }
                            }
                        }
                        ?>
                        <tr>
                            <td>
                                <label><input type="checkbox" class="checkbox" <?php checked( $is_checked, true ); ?> name="person_categoires[<?php echo esc_attr( $i ); ?>][]" value="<?php echo esc_attr( $term->term_id ); ?>" /> <?php echo esc_html( $term->name ); ?></label>
                            </td>
                            <td>
                                <?php
                                    masvideos_wp_text_input(
                                        array(
                                            'id'            => 'person_titles[' . $i . '][' . $term->term_id . ']',
                                            'value'         => $title,
                                            'label'         => __( 'Title', 'masvideos' ),
                                            'description'   => __( 'Enter the title.', 'masvideos' ),
                                        )
                                    );
                                ?>
                            </td>
                        </tr>
                        <?php
                    }
                ?>
                <?php do_action( 'masvideos_after_movie_person_settings', $person, $i ); ?>
            </tbody>
        </table>
    </div>
</div>
