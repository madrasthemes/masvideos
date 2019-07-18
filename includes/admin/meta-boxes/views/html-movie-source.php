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

                            <input type="text" class="source_name short" name="source_names[<?php echo esc_attr( $i ); ?>]" value="<?php echo esc_attr( $source['name'] ); ?>" />

                            <input type="hidden" name="source_position[<?php echo esc_attr( $i ); ?>]" class="source_position" value="<?php echo esc_attr( $source['position'] ); ?>" />
                        </p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="options_group">
                            <?php
                                masvideos_wp_select(
                                    array(
                                        'id'            => 'source_choice[' . $i . ']',
                                        'value'         => isset( $source['choice'] ) ? $source['choice'] : 'movie_embed',
                                        'label'         => __( 'Choose Method', 'masvideos' ),
                                        'options'       => array(
                                            'movie_embed'   => __( 'Embed Movie', 'masvideos' ),
                                            'movie_url'     => __( 'Movie URL', 'masvideos' ),
                                        ),
                                        'class'         => 'short show_hide_select',
                                    )
                                );
                                masvideos_wp_embed_video(
                                    array(
                                        'id'            => 'source_embed_content[' . $i . ']',
                                        'value'         => isset( $source['embed_content'] ) ? $source['embed_content'] : '',
                                        'label'         => __( 'Embed Movie', 'masvideos' ),
                                        'description'   => __( 'Enter the embed content to the movie.', 'masvideos' ),
                                        'wrapper_class' => 'show_if_movie_embed hide',
                                    )
                                );
                                masvideos_wp_textarea_input(
                                    array(
                                        'id'            => 'source_link[' . $i . ']',
                                        'value'         => isset( $source['link'] ) ? $source['link'] : '',
                                        'label'         => __( 'Movie URL', 'masvideos' ),
                                        'description'   => __( 'Enter the external URL to the video.', 'masvideos' ),
                                        'wrapper_class' => 'show_if_movie_url hide',
                                    )
                                );
                                masvideos_wp_checkbox(
                                    array(
                                        'id'            => 'source_is_affiliate[' . $i . ']',
                                        'value'         => isset( $source['is_affiliate'] ) ? masvideos_bool_to_string( $source['is_affiliate'] ) : '',
                                        'label'         => __( 'Is Affiliate URL ?', 'masvideos' ),
                                        'wrapper_class' => 'show_if_movie_url hide',
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
                                    'value'         => isset( $source['quality'] ) ? $source['quality'] : '',
                                    'label'         => __( 'Quality', 'masvideos' ),
                                    'description'   => __( 'Enter the source quality of the movie.', 'masvideos' ),
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
                                    'value'         => isset( $source['language'] ) ? $source['language'] : '',
                                    'label'         => __( 'Language', 'masvideos' ),
                                    'description'   => __( 'Enter the source language of the movie.', 'masvideos' ),
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
                                    'value'         => isset( $source['player'] ) ? $source['player'] : '',
                                    'label'         => __( 'Player', 'masvideos' ),
                                    'description'   => __( 'Enter the source player of the movie.', 'masvideos' ),
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
                                    'value'         => isset( $source['date_added'] ) ? $source['date_added'] : '',
                                    'label'         => __( 'Date Added', 'masvideos' ),
                                    'description'   => __( 'Enter the source added date of the movie.', 'masvideos' ),
                                    'wrapper_class' => 'movie_date_picker',
                                )
                            );
                        ?>
                    </td>
                </tr>
                <?php do_action( 'masvideos_after_movie_source_settings', $source, $i ); ?>
            </tbody>
        </table>
    </div>
</div>
