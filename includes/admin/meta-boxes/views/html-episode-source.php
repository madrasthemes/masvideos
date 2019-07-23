<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="masvideos_source masvideos-metabox closed <?php echo esc_attr( implode( ' ', $metabox_class ) ); ?>" rel="<?php echo esc_attr( $source['position'] ); ?>">
    <h3>
        <a href="#" class="remove_row delete"><?php esc_html_e( 'Remove', 'masvideos' ); ?></a>
        <div class="handlediv" title="<?php esc_attr_e( 'Click to toggle', 'masvideos' ); ?>"></div>
        <strong class="source_name"><?php echo esc_html( $source['name'] ); ?></strong>
    </h3>
    <div class="masvideos_source_data masvideos-metabox-content">
        <table cellpadding="0" cellspacing="0">
            <tbody>
                <tr>
                    <td class="source_name">
                        <p class="form-field">
                            <label><?php esc_html_e( 'Name', 'masvideos' ); ?>:</label>

                            <input type="text" class="source_name" name="source_names[<?php echo esc_attr( $i ); ?>]" value="<?php echo esc_attr( $source['name'] ); ?>" />

                            <input type="hidden" name="source_position[<?php echo esc_attr( $i ); ?>]" class="source_position" value="<?php echo esc_attr( $source['position'] ); ?>" />
                        </p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="options_group">
                            <input type="hidden" name="source_choice[<?php echo esc_attr( $i ); ?>]" class="source_choice" value="episode_embed" />
                            <?php
                                masvideos_wp_embed_video(
                                    array(
                                        'id'            => 'source_embed_content[' . $i . ']',
                                        'value'         => $source['embed_content'],
                                        'label'         => __( 'Embed Content', 'masvideos' ),
                                        'description'   => __( 'Enter the embed content to the episode.', 'masvideos' ),
                                    )
                                );
                            ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php
                            masvideos_wp_text_input(
                                array(
                                    'id'            => 'source_quality[' . $i . ']',
                                    'value'         => $source['quality'],
                                    'label'         => __( 'Quality', 'masvideos' ),
                                    'description'   => __( 'Enter the source quality of the episode.', 'masvideos' ),
                                )
                            );
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php
                            masvideos_wp_text_input(
                                array(
                                    'id'            => 'source_language[' . $i . ']',
                                    'value'         => $source['language'],
                                    'label'         => __( 'Language', 'masvideos' ),
                                    'description'   => __( 'Enter the source language of the episode.', 'masvideos' ),
                                )
                            );
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php
                            masvideos_wp_text_input(
                                array(
                                    'id'            => 'source_player[' . $i . ']',
                                    'value'         => $source['player'],
                                    'label'         => __( 'Player', 'masvideos' ),
                                    'description'   => __( 'Enter the source player of the episode.', 'masvideos' ),
                                )
                            );
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php
                            masvideos_wp_date_picker(
                                array(
                                    'id'            => 'source_date_added[' . $i . ']',
                                    'value'         => $source['date_added'],
                                    'label'         => __( 'Date Added', 'masvideos' ),
                                    'description'   => __( 'Enter the source added date of the episode.', 'masvideos' ),
                                    'wrapper_class' => 'episode_date_picker',
                                )
                            );
                        ?>
                    </td>
                </tr>
                <?php do_action( 'masvideos_after_episode_source_settings', $source, $i ); ?>
            </tbody>
        </table>
    </div>
</div>
