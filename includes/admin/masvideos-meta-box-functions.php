<?php
/**
 * MasVideos Meta Box Functions
 *
 * @category    Core
 * @package     MasVideos/Admin/Functions
 * @version     1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Output a text input box.
 *
 * @param array $field
 */
function masvideos_wp_text_input( $field ) {
    global $thepostid, $post;

    $thepostid              = empty( $thepostid ) && ! empty( $post ) ? $post->ID : $thepostid;
    $field['placeholder']   = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
    $field['class']         = isset( $field['class'] ) ? $field['class'] : 'short';
    $field['style']         = isset( $field['style'] ) ? $field['style'] : '';
    $field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
    $field['value']         = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
    $field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
    $field['type']          = isset( $field['type'] ) ? $field['type'] : 'text';
    $field['desc_tip']      = isset( $field['desc_tip'] ) ? $field['desc_tip'] : false;
    $data_type              = empty( $field['data_type'] ) ? '' : $field['data_type'];

    switch ( $data_type ) {
        case 'url':
            $field['class'] .= ' masvideos_input_url';
            $field['value']  = esc_url( $field['value'] );
            break;

        default:
            break;
    }

    // Custom attribute handling
    $custom_attributes = array();

    if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {

        foreach ( $field['custom_attributes'] as $attribute => $value ) {
            $custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
        }
    }

    echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '">
        <label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label>';

    if ( ! empty( $field['description'] ) && false !== $field['desc_tip'] ) {
        echo masvideos_help_tip( $field['description'] );
    }

    echo '<input type="' . esc_attr( $field['type'] ) . '" class="' . esc_attr( $field['class'] ) . '" style="' . esc_attr( $field['style'] ) . '" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $field['value'] ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" ' . implode( ' ', $custom_attributes ) . ' /> ';

    if ( ! empty( $field['description'] ) && false === $field['desc_tip'] ) {
        echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
    }

    echo '</p>';
}

/**
 * Output a hidden input box.
 *
 * @param array $field
 */
function masvideos_wp_hidden_input( $field ) {
    global $thepostid, $post;

    $thepostid      = empty( $thepostid ) && ! empty( $post ) ? $post->ID : $thepostid;
    $field['value'] = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
    $field['class'] = isset( $field['class'] ) ? $field['class'] : '';

    echo '<input type="hidden" class="' . esc_attr( $field['class'] ) . '" name="' . esc_attr( $field['id'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $field['value'] ) . '" /> ';
}

/**
 * Output a textarea input box.
 *
 * @param array $field
 */
function masvideos_wp_textarea_input( $field ) {
    global $thepostid, $post;

    $thepostid              = empty( $thepostid ) && ! empty( $post ) ? $post->ID : $thepostid;
    $field['placeholder']   = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
    $field['class']         = isset( $field['class'] ) ? $field['class'] : 'short';
    $field['style']         = isset( $field['style'] ) ? $field['style'] : '';
    $field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
    $field['value']         = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
    $field['desc_tip']      = isset( $field['desc_tip'] ) ? $field['desc_tip'] : false;
    $field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
    $field['rows']          = isset( $field['rows'] ) ? $field['rows'] : 2;
    $field['cols']          = isset( $field['cols'] ) ? $field['cols'] : 20;

    // Custom attribute handling
    $custom_attributes = array();

    if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {

        foreach ( $field['custom_attributes'] as $attribute => $value ) {
            $custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
        }
    }

    echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '">
        <label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label>';

    if ( ! empty( $field['description'] ) && false !== $field['desc_tip'] ) {
        echo masvideos_help_tip( $field['description'] );
    }

    echo '<textarea class="' . esc_attr( $field['class'] ) . '" style="' . esc_attr( $field['style'] ) . '"  name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" rows="' . esc_attr( $field['rows'] ) . '" cols="' . esc_attr( $field['cols'] ) . '" ' . implode( ' ', $custom_attributes ) . '>' . esc_textarea( $field['value'] ) . '</textarea> ';

    if ( ! empty( $field['description'] ) && false === $field['desc_tip'] ) {
        echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
    }

    echo '</p>';
}

/**
 * Output a checkbox input box.
 *
 * @param array $field
 */
function masvideos_wp_checkbox( $field ) {
    global $thepostid, $post;

    $thepostid              = empty( $thepostid ) && ! empty( $post ) ? $post->ID : $thepostid;
    $field['class']         = isset( $field['class'] ) ? $field['class'] : 'checkbox';
    $field['style']         = isset( $field['style'] ) ? $field['style'] : '';
    $field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
    $field['value']         = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
    $field['cbvalue']       = isset( $field['cbvalue'] ) ? $field['cbvalue'] : 'yes';
    $field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
    $field['desc_tip']      = isset( $field['desc_tip'] ) ? $field['desc_tip'] : false;

    // Custom attribute handling
    $custom_attributes = array();

    if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {

        foreach ( $field['custom_attributes'] as $attribute => $value ) {
            $custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
        }
    }

    echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '">
        <label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label>';

    if ( ! empty( $field['description'] ) && false !== $field['desc_tip'] ) {
        echo masvideos_help_tip( $field['description'] );
    }

    echo '<input type="checkbox" class="' . esc_attr( $field['class'] ) . '" style="' . esc_attr( $field['style'] ) . '" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $field['cbvalue'] ) . '" ' . checked( $field['value'], $field['cbvalue'], false ) . '  ' . implode( ' ', $custom_attributes ) . '/> ';

    if ( ! empty( $field['description'] ) && false === $field['desc_tip'] ) {
        echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
    }

    echo '</p>';
}

/**
 * Output a select input box.
 *
 * @param array $field Data about the field to render.
 */
function masvideos_wp_select( $field ) {
    global $thepostid, $post;

    $thepostid = empty( $thepostid ) && ! empty( $post ) ? $post->ID : $thepostid;
    $field     = wp_parse_args(
        $field, array(
            'class'             => 'select short',
            'style'             => '',
            'wrapper_class'     => '',
            'value'             => get_post_meta( $thepostid, $field['id'], true ),
            'name'              => $field['id'],
            'desc_tip'          => false,
            'custom_attributes' => array(),
        )
    );

    $wrapper_attributes = array(
        'class' => $field['wrapper_class'] . " form-field {$field['id']}_field",
    );

    $label_attributes = array(
        'for' => $field['id'],
    );

    $field_attributes          = (array) $field['custom_attributes'];
    $field_attributes['style'] = $field['style'];
    $field_attributes['id']    = $field['id'];
    $field_attributes['name']  = $field['name'];
    $field_attributes['class'] = $field['class'];

    $tooltip     = ! empty( $field['description'] ) && false !== $field['desc_tip'] ? $field['description'] : '';
    $description = ! empty( $field['description'] ) && false === $field['desc_tip'] ? $field['description'] : '';
    ?>
    <p <?php echo masvideos_implode_html_attributes( $wrapper_attributes ); // WPCS: XSS ok. ?>>
        <label <?php echo masvideos_implode_html_attributes( $label_attributes ); // WPCS: XSS ok. ?>><?php echo wp_kses_post( $field['label'] ); ?></label>
        <?php if ( $tooltip ) : ?>
            <?php echo masvideos_help_tip( $tooltip ); // WPCS: XSS ok. ?>
        <?php endif; ?>
        <select <?php echo masvideos_implode_html_attributes( $field_attributes ); // WPCS: XSS ok. ?>>
            <?php
            foreach ( $field['options'] as $key => $value ) {
                echo '<option value="' . esc_attr( $key ) . '"' . masvideos_selected( $key, $field['value'] ) . '>' . esc_html( $value ) . '</option>';
            }
            ?>
        </select>
        <?php if ( $description ) : ?>
            <span class="description"><?php echo wp_kses_post( $description ); ?></span>
        <?php endif; ?>
    </p>
    <?php
}

/**
 * Output a radio input box.
 *
 * @param array $field
 */
function masvideos_wp_radio( $field ) {
    global $thepostid, $post;

    $thepostid              = empty( $thepostid ) && ! empty( $post ) ? $post->ID : $thepostid;
    $field['class']         = isset( $field['class'] ) ? $field['class'] : 'select short';
    $field['style']         = isset( $field['style'] ) ? $field['style'] : '';
    $field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
    $field['value']         = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
    $field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
    $field['desc_tip']      = isset( $field['desc_tip'] ) ? $field['desc_tip'] : false;

    echo '<fieldset class="form-field form-field-radio ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '"><legend>' . wp_kses_post( $field['label'] ) . '</legend>';

    if ( ! empty( $field['description'] ) && false !== $field['desc_tip'] ) {
        echo masvideos_help_tip( $field['description'] );
    }

    echo '<ul class="masvideos-radios">';

    foreach ( $field['options'] as $key => $value ) {

        echo '<li><label><input
                name="' . esc_attr( $field['name'] ) . '"
                value="' . esc_attr( $key ) . '"
                type="radio"
                class="' . esc_attr( $field['class'] ) . '"
                style="' . esc_attr( $field['style'] ) . '"
                ' . checked( esc_attr( $field['value'] ), esc_attr( $key ), false ) . '
                /> ' . esc_html( $value ) . '</label>
        </li>';
    }
    echo '</ul>';

    if ( ! empty( $field['description'] ) && false === $field['desc_tip'] ) {
        echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
    }

    echo '</fieldset>';
}

/**
 * Outputs Upload Video
 */
function masvideos_wp_upload_video( $field ) {
    global $thepostid, $post;

    $thepostid              = empty( $thepostid ) && ! empty( $post ) ? $post->ID : $thepostid;
    $field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
    $field['value']         = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
    $field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
    $field['placeholder']   = isset( $field['placeholder'] ) ? $field['placeholder'] : false;

    echo '<div id="' . esc_attr( $field['id'] ) . '_field" class="form-field media-attachment-video media-option ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '"><label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label>';
    ?>
        <?php
        if ( absint( $field['value'] ) ) {
            $video_src = wp_get_attachment_url( $field['value'] );
            echo do_shortcode('[video src="' . $video_src . '"]');
        }
        ?>
        <input type="hidden" name="<?php echo esc_attr( $field['name'] ); ?>" class="upload_video_id" value="<?php echo esc_attr( $field['value'] ); ?>" />
        <a href="#" class="button masvideos_upload_video_button tips"><?php echo esc_html__( 'Upload/Add video', 'masvideos' ); ?></a>
        <a href="#" class="button masvideos_remove_video_button tips"><?php echo esc_html__( 'Remove this video', 'masvideos' ); ?></a>
    </div>
    <?php
}

/**
 * Outputs Embed Video
 */
function masvideos_wp_embed_video( $field ) {
    global $thepostid, $post;

    $thepostid              = empty( $thepostid ) && ! empty( $post ) ? $post->ID : $thepostid;
    $field['placeholder']   = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
    $field['class']         = isset( $field['class'] ) ? $field['class'] : 'short';
    $field['style']         = isset( $field['style'] ) ? $field['style'] : '';
    $field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
    $field['value']         = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
    $field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
    $field['rows']          = isset( $field['rows'] ) ? $field['rows'] : 1;
    $field['cols']          = isset( $field['cols'] ) ? $field['cols'] : 20;

    // Custom attribute handling
    $custom_attributes = array();

    if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {

        foreach ( $field['custom_attributes'] as $attribute => $value ) {
            $custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
        }
    }

    echo '<div class="form-field media-embed-video ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '">
        <label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label>';

        echo '<textarea class="' . esc_attr( $field['class'] ) . '" style="' . esc_attr( $field['style'] ) . '"  name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" rows="' . esc_attr( $field['rows'] ) . '" cols="' . esc_attr( $field['cols'] ) . '" ' . implode( ' ', $custom_attributes ) . '>' . esc_textarea( $field['value'] ) . '</textarea> ';

    echo '</div>';
}

/**
 * Outputs Video URL
 */
function masvideos_wp_video_url( $field ) {
    global $thepostid, $post;

    $thepostid              = empty( $thepostid ) && ! empty( $post ) ? $post->ID : $thepostid;
    $field['placeholder']   = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
    $field['class']         = isset( $field['class'] ) ? $field['class'] : 'short';
    $field['style']         = isset( $field['style'] ) ? $field['style'] : '';
    $field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
    $field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
    $field['type']          = isset( $field['type'] ) ? $field['type'] : 'text';
    $field['value']         = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );

    // Custom attribute handling
    $custom_attributes = array();

    if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {

        foreach ( $field['custom_attributes'] as $attribute => $value ){
            $custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
        }
    }

    echo '<div class="form-field media-video-url ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '"><label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label>';

        echo '<input type="' . esc_attr( $field['type'] ) . '" class="media-video-url-box ' . esc_attr( $field['class'] ) . '" style="' . esc_attr( $field['style'] ) . '" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $field['value'] ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" ' . implode( ' ', $custom_attributes ) . ' /> ';

        if ( ! empty( $field['value'] ) ) {
            echo do_shortcode('[video src="' . $field['value'] . '"]');
        }
    echo '</div>';
}

/**
 * Output a date picker.
 *
 * @param array $field
 */
function masvideos_wp_date_picker( $field ) {
    global $thepostid, $post;

    $thepostid              = empty( $thepostid ) && ! empty( $post ) ? $post->ID : $thepostid;
    $field['placeholder']   = isset( $field['placeholder'] ) ? $field['placeholder'] : 'YYYY-MM-DD';
    $field['class']         = isset( $field['class'] ) ? $field['class'] : 'short';
    $field['style']         = isset( $field['style'] ) ? $field['style'] : '';
    $field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
    $field['value']         = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
    $field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
    $field['type']          = isset( $field['type'] ) ? $field['type'] : 'text';
    $field['desc_tip']      = isset( $field['desc_tip'] ) ? $field['desc_tip'] : false;

    // Custom attribute handling
    $custom_attributes = array();

    if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {

        foreach ( $field['custom_attributes'] as $attribute => $value ) {
            $custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
        }
    }

    echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '">
        <label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label>';

    echo '<input type="' . esc_attr( $field['type'] ) . '" class="' . esc_attr( $field['class'] ) . '" style="' . esc_attr( $field['style'] ) . '" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $field['value'] ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" maxlength="10" pattern="' . esc_attr( apply_filters( 'masvideos_date_input_html_pattern', '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])' ) ) . '" ' . implode( ' ', $custom_attributes ) . ' /> ';

    if ( ! empty( $field['description'] ) && false === $field['desc_tip'] ) {
        echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
    }

    echo '</p>';
}

function masvideos_wp_upload_image( $field ) {
    global $thepostid, $post;

    $thepostid              = empty( $thepostid ) && ! empty( $post ) ? $post->ID : $thepostid;
    $field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
    $field['value']         = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
    $field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
    $field['placeholder']   = isset( $field['placeholder'] ) ? $field['placeholder'] : false;

    if ( absint( $field['value'] ) ) {
        $image = wp_get_attachment_url( $field['value'] );
    } elseif ( $field['placeholder'] ) {
        $image = masvideos_placeholder_img_src();
    } else {
        $image = '';
    }

    echo '<p id="' . esc_attr( $field['id'] ) . '_field" class="form-field media-option ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '"><label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label>';
    ?>
        <?php if ( isset ( $image ) ) : ?>
        <img src="<?php echo esc_attr( $image ); ?>" class="upload_image_preview" data-placeholder-src="<?php echo esc_attr( masvideos_placeholder_img_src() ); ?>" alt="<?php echo esc_attr__( 'Image', 'masvideos' ); ?>" width="150px" height="auto" style="display:block; margin-bottom:1em;"/>
        <?php endif; ?>
        <input type="hidden" name="<?php echo esc_attr( $field['name'] ); ?>" class="upload_image_id" value="<?php echo esc_attr( $field['value'] ); ?>" />
        <a href="#" class="button masvideos_upload_image_button tips"><?php echo esc_html__( 'Upload/Add image', 'masvideos' ); ?></a>
        <a href="#" class="button masvideos_remove_image_button tips"><?php echo esc_html__( 'Remove this image', 'masvideos' ); ?></a>
    </p>
    <?php
}