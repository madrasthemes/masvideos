<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="masvideos_season masvideos-metabox closed <?php echo esc_attr( implode( ' ', $metabox_class ) ); ?>" rel="<?php echo esc_attr( $season['position'] ); ?>">
    <h3>
        <a href="#" class="remove_row delete"><?php esc_html_e( 'Remove', 'masvideos' ); ?></a>
        <div class="handlediv" title="<?php esc_attr_e( 'Click to toggle', 'masvideos' ); ?>"></div>
        <strong class="season_name"><?php echo esc_html( $season['name'] ); ?></strong>
    </h3>
    <div class="masvideos_season_data masvideos-metabox-content">
        <table cellpadding="0" cellspacing="0">
            <tbody>
                <tr>
                    <td class="season_name">
                        <p class="form-field">
                            <label><?php esc_html_e( 'Name', 'masvideos' ); ?>:</label>

                            <input type="text" class="season_name short" name="season_names[<?php echo esc_attr( $i ); ?>]" value="<?php echo esc_attr( $season['name'] ); ?>" />

                            <input type="hidden" name="season_position[<?php echo esc_attr( $i ); ?>]" class="season_position" value="<?php echo esc_attr( $season['position'] ); ?>" />
                        </p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p id="season_image_id_<?php echo esc_attr( $i ); ?>" class="form-field media-attachment-image media-option">
                            <label><?php echo esc_html__( 'Season image', 'masvideos' ); ?>:</label>

                            <img class="upload_image_preview" src="<?php echo $season['image_id'] ? esc_url( wp_get_attachment_thumb_url( $season['image_id'] ) ) : esc_url( masvideos_placeholder_img_src() ); ?>" data-placeholder-src="<?php echo esc_url( masvideos_placeholder_img_src() ); ?>" width="150px" height="auto" style="display:block; margin-bottom:1em;"/>
                            <input type="hidden" name="season_image_id[<?php echo esc_attr( $i ); ?>]" class="upload_image_id" value="<?php echo esc_attr( $season['image_id'] ); ?>" />

                            <a href="#" class="button masvideos_upload_image_button tips"><?php echo esc_html__( 'Upload an image', 'masvideos' ); ?></a>
                            <a href="#" class="button masvideos_remove_image_button tips"><?php echo esc_html__( 'Remove this image', 'masvideos' ); ?></a>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p class="form-field">
                            <label><?php esc_html_e( 'Episode(s)', 'masvideos' ); ?>:</label>
                            <select multiple="multiple" data-placeholder="<?php esc_attr_e( 'Search for episodes', 'masvideos' ); ?>" class="multiselect season_episodes masvideos-enhanced-search" name="season_episodes[<?php echo esc_attr( $i ); ?>][]" data-sortable="true" data-action="masvideos_json_search_episodes" data-nonce_key="search_episodes_nonce">
                                <?php
                                $episode_ids = $season['episodes'];

                                foreach ( $episode_ids as $episode_id ) {
                                    $episode = masvideos_get_episode( $episode_id );
                                    if ( is_object( $episode ) ) {
                                        echo '<option value="' . esc_attr( $episode_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $episode->get_name() ) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                            <?php do_action( 'masvideos_tv_show_option_episodes', $season, $i ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p class="form-field">
                            <label><?php esc_html_e( 'Year', 'masvideos' ); ?>:</label>
                            <input type="text" class="short" name="season_year[<?php echo esc_attr( $i ); ?>]" value="<?php echo esc_attr( $season['year'] ); ?>" />
                        </p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p class="form-field">
                            <label><?php esc_html_e( 'Description', 'masvideos' ); ?>:</label>
                            <textarea class="short" name="season_description[<?php echo esc_attr( $i ); ?>]" cols="5" rows="5" placeholder="<?php esc_attr_e( 'Enter some description.', 'masvideos' ); ?>"><?php echo esc_textarea( $season['description'] ); ?></textarea>
                        </p>
                    </td>
                </tr>
                <?php do_action( 'masvideos_after_tv_show_season_settings', $season, $i ); ?>
            </tbody>
        </table>
    </div>
</div>
