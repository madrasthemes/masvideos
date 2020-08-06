<?php
/**
 * MasVideos Template
 *
 * Functions for the templating system.
 *
 * @package  MasVideos\Functions
 * @version  1.0.0
 */

defined( 'ABSPATH' ) || exit;

require MASVIDEOS_ABSPATH . 'includes/masvideos-person-template-functions.php';
require MASVIDEOS_ABSPATH . 'includes/masvideos-episode-template-functions.php';
require MASVIDEOS_ABSPATH . 'includes/masvideos-tv-show-template-functions.php';
require MASVIDEOS_ABSPATH . 'includes/masvideos-tv-show-playlist-template-functions.php';
require MASVIDEOS_ABSPATH . 'includes/masvideos-video-template-functions.php';
require MASVIDEOS_ABSPATH . 'includes/masvideos-video-playlist-template-functions.php';
require MASVIDEOS_ABSPATH . 'includes/masvideos-movie-template-functions.php';
require MASVIDEOS_ABSPATH . 'includes/masvideos-movie-playlist-template-functions.php';

/**
 * Handle redirects before content is output - hooked into template_redirect so is_page works.
 */
function masvideos_template_redirect() {
    global $wp_query, $wp;

    if ( ! empty( $_GET['page_id'] ) && '' === get_option( 'permalink_structure' ) && masvideos_get_page_id( 'persons' ) === absint( $_GET['page_id'] ) && get_post_type_archive_link( 'person' ) ) { // WPCS: input var ok, CSRF ok.

        // When default permalinks are enabled, redirect persons page to post type archive url.
        wp_safe_redirect( get_post_type_archive_link( 'person' ) );
        exit;

    } elseif ( is_search() && is_post_type_archive( 'person' ) && apply_filters( 'masvideos_redirect_single_search_result', true ) && 1 === absint( $wp_query->found_posts ) ) {

        // Redirect to the person page if we have a single person.
        $person = masvideos_get_person( $wp_query->post );

        if ( $person && $person->is_visible() ) {
            wp_safe_redirect( get_permalink( $person->get_id() ), 302 );
            exit;
        }

    } elseif ( ! empty( $_GET['page_id'] ) && '' === get_option( 'permalink_structure' ) && masvideos_get_page_id( 'episodes' ) === absint( $_GET['page_id'] ) && get_post_type_archive_link( 'episode' ) ) { // WPCS: input var ok, CSRF ok.

        // When default permalinks are enabled, redirect episodes page to post type archive url.
        wp_safe_redirect( get_post_type_archive_link( 'episode' ) );
        exit;

    } elseif ( is_search() && is_post_type_archive( 'episode' ) && apply_filters( 'masvideos_redirect_single_search_result', true ) && 1 === absint( $wp_query->found_posts ) ) {

        // Redirect to the episode page if we have a single episode.
        $episode = masvideos_get_episode( $wp_query->post );

        if ( $episode && $episode->is_visible() ) {
            wp_safe_redirect( get_permalink( $episode->get_id() ), 302 );
            exit;
        }

    } elseif ( ! empty( $_GET['page_id'] ) && '' === get_option( 'permalink_structure' ) && masvideos_get_page_id( 'tv_shows' ) === absint( $_GET['page_id'] ) && get_post_type_archive_link( 'tv_show' ) ) { // WPCS: input var ok, CSRF ok.

        // When default permalinks are enabled, redirect tv shows page to post type archive url.
        wp_safe_redirect( get_post_type_archive_link( 'tv_show' ) );
        exit;

    } elseif ( is_search() && is_post_type_archive( 'tv_show' ) && apply_filters( 'masvideos_redirect_single_search_result', true ) && 1 === absint( $wp_query->found_posts ) ) {

        // Redirect to the tv show page if we have a single tv show.
        $tv_show = masvideos_get_tv_show( $wp_query->post );

        if ( $tv_show && $tv_show->is_visible() ) {
            wp_safe_redirect( get_permalink( $tv_show->get_id() ), 302 );
            exit;
        }

    } elseif ( ! empty( $_GET['page_id'] ) && '' === get_option( 'permalink_structure' ) && masvideos_get_page_id( 'videos' ) === absint( $_GET['page_id'] ) && get_post_type_archive_link( 'video' ) ) { // WPCS: input var ok, CSRF ok.

        // When default permalinks are enabled, redirect videos page to post type archive url.
        wp_safe_redirect( get_post_type_archive_link( 'video' ) );
        exit;

    } elseif ( is_search() && is_post_type_archive( 'video' ) && apply_filters( 'masvideos_redirect_single_search_result', true ) && 1 === absint( $wp_query->found_posts ) ) {

        // Redirect to the video page if we have a single video.
        $video = masvideos_get_video( $wp_query->post );

        if ( $video && $video->is_visible() ) {
            wp_safe_redirect( get_permalink( $video->get_id() ), 302 );
            exit;
        }

    } elseif ( ! empty( $_GET['page_id'] ) && '' === get_option( 'permalink_structure' ) && masvideos_get_page_id( 'movies' ) === absint( $_GET['page_id'] ) && get_post_type_archive_link( 'movie' ) ) { // WPCS: input var ok, CSRF ok.

        // When default permalinks are enabled, redirect movies page to post type archive url.
        wp_safe_redirect( get_post_type_archive_link( 'movie' ) );
        exit;

    } elseif ( is_search() && is_post_type_archive( 'movie' ) && apply_filters( 'masvideos_redirect_single_search_result', true ) && 1 === absint( $wp_query->found_posts ) ) {

        // Redirect to the movie page if we have a single movie.
        $movie = masvideos_get_movie( $wp_query->post );

        if ( $movie && $movie->is_visible() ) {
            wp_safe_redirect( get_permalink( $movie->get_id() ), 302 );
            exit;
        }

    }
}
add_action( 'template_redirect', 'masvideos_template_redirect' );

/**
 * Outputs hidden form inputs for each query string variable.
 *
 * @since 1.0.0
 * @param string|array $values Name value pairs, or a URL to parse.
 * @param array        $exclude Keys to exclude.
 * @param string       $current_key Current key we are outputting.
 * @param bool         $return Whether to return.
 * @return string
 */
function masvideos_query_string_form_fields( $values = null, $exclude = array(), $current_key = '', $return = false ) {
    if ( is_null( $values ) ) {
        $values = $_GET; // WPCS: input var ok, CSRF ok.
    } elseif ( is_string( $values ) ) {
        $url_parts = wp_parse_url( $values );
        $values    = array();

        if ( ! empty( $url_parts['query'] ) ) {
            parse_str( $url_parts['query'], $values );
        }
    }
    $html = '';

    foreach ( $values as $key => $value ) {
        if ( in_array( $key, $exclude, true ) ) {
            continue;
        }
        if ( $current_key ) {
            $key = $current_key . '[' . $key . ']';
        }
        if ( is_array( $value ) ) {
            $html .= masvideos_query_string_form_fields( $value, $exclude, $key, true );
        } else {
            $html .= '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( wp_unslash( $value ) ) . '" />';
        }
    }

    if ( $return ) {
        return $html;
    }

    echo $html; // WPCS: XSS ok.
}

/**
 * Output generator tag to aid debugging.
 *
 * @access public
 *
 * @param string $gen Generator.
 * @param string $type Type.
 *
 * @return string
 */
function masvideos_generator_tag( $gen, $type ) {
    switch ( $type ) {
        case 'html':
            $gen .= "\n" . '<meta name="generator" content="MasVideos ' . esc_attr( MASVIDEOS_VERSION ) . '">';
            break;
        case 'xhtml':
            $gen .= "\n" . '<meta name="generator" content="MasVideos ' . esc_attr( MASVIDEOS_VERSION ) . '" />';
            break;
    }
    return $gen;
}

/**
 * Add body classes for MasVideos pages.
 *
 * @param  array $classes Body Classes.
 * @return array
 */
function masvideos_body_class( $classes ) {
    $classes = (array) $classes;

    if ( is_masvideos() ) {

        $classes[] = 'masvideos';
        $classes[] = 'masvideos-page';

        if( is_video() || is_movie() || is_episode() || is_tv_show() ) {
            $classes[] = 'masvideos-single';
        } else {
            $classes[] = 'masvideos-archive';
        }

    } elseif ( masvideos_is_account_page() ) {

        $classes[] = 'masvideos-account';
        $classes[] = 'masvideos-page';

    }

    foreach ( MasVideos()->query->get_query_vars() as $key => $value ) {
        if ( is_masvideos_endpoint_url( $key ) ) {
            $classes[] = 'masvideos-account-' . sanitize_html_class( $key );
        }
    }

    $classes[] = 'masvideos-no-js';

    add_action( 'wp_footer', 'masvideos_no_js' );

    return array_unique( $classes );
}

/**
 * NO JS handling.
 *
 * @since 3.4.0
 */
function masvideos_no_js() {
    ?>
    <script type="text/javascript">
        var c = document.body.className;
        c = c.replace(/masvideos-no-js/, 'masvideos-js');
        document.body.className = c;
    </script>
    <?php
}

/**
 * Get the placeholder image URL etc.
 *
 * @access public
 * @return string
 */
function masvideos_placeholder_img_src() {
    return apply_filters( 'masvideos_placeholder_img_src', MasVideos()->plugin_url() . '/assets/images/placeholder.png' );
}

/**
 * Get the placeholder image.
 *
 * @param string $size Image size.
 * @return string
 */
function masvideos_placeholder_img( $size = 'masvideos_thumbnail' ) {
    $dimensions = masvideos_get_image_size( $size );

    return apply_filters( 'masvideos_placeholder_img', '<img src="' . masvideos_placeholder_img_src( $size ) . '" alt="' . esc_attr__( 'Placeholder', 'masvideos' ) . '" width="' . esc_attr( $dimensions['width'] ) . '" class="masvideos-placeholder wp-post-image" height="' . esc_attr( $dimensions['height'] ) . '" />', $size, $dimensions );
}

/**
 * Outputs all queued notices on WC pages.
 *
 * @since 1.0.0
 */
function masvideos_output_all_notices() {
    echo '<div class="masvideos-notices-wrapper">';
    masvideos_print_notices();
    echo '</div>';
}

/**
 * Get logout endpoint.
 *
 * @since  1.0.0
 *
 * @param string $redirect Redirect URL.
 *
 * @return string
 */
function masvideos_logout_url( $redirect = '' ) {
    $redirect = $redirect ? $redirect : masvideos_get_page_permalink( 'myaccount' );

    return wp_logout_url( $redirect );
}

if ( ! function_exists( 'masvideos_form_field' ) ) {

    /**
     * Outputs a checkout/address form field.
     *
     * @param string $key Key.
     * @param mixed  $args Arguments.
     * @param string $value (default: null).
     * @return string
     */
    function masvideos_form_field( $key, $args, $value = null, $post_id = null ) {
        $defaults = array(
            'type'              => 'text',
            'label'             => '',
            'description'       => '',
            'placeholder'       => '',
            'maxlength'         => false,
            'required'          => false,
            'autocomplete'      => false,
            'id'                => $key,
            'class'             => array(),
            'label_class'       => array(),
            'input_class'       => array(),
            'return'            => false,
            'options'           => array(),
            'custom_attributes' => array(),
            'validate'          => array(),
            'default'           => '',
            'autofocus'         => '',
            'priority'          => '',
            'taxonomy'          => 'video_cat',
        );

        $args = wp_parse_args( $args, $defaults );
        $args = apply_filters( 'masvideos_form_field_args', $args, $key, $value );

        if ( $args['required'] ) {
            $args['class'][] = 'validate-required';
            $required        = '&nbsp;<abbr class="required" title="' . esc_attr__( 'required', 'masvideos' ) . '">*</abbr>';
        } else {
            $required = '&nbsp;<span class="optional">(' . esc_html__( 'optional', 'masvideos' ) . ')</span>';
        }

        if ( is_string( $args['label_class'] ) ) {
            $args['label_class'] = array( $args['label_class'] );
        }

        if ( is_null( $value ) ) {
            $value = $args['default'];
        }

        // Custom attribute handling.
        $custom_attributes         = array();
        $args['custom_attributes'] = array_filter( (array) $args['custom_attributes'], 'strlen' );

        if ( $args['maxlength'] ) {
            $args['custom_attributes']['maxlength'] = absint( $args['maxlength'] );
        }

        if ( ! empty( $args['autocomplete'] ) ) {
            $args['custom_attributes']['autocomplete'] = $args['autocomplete'];
        }

        if ( true === $args['autofocus'] ) {
            $args['custom_attributes']['autofocus'] = 'autofocus';
        }

        if ( $args['description'] ) {
            $args['custom_attributes']['aria-describedby'] = $args['id'] . '-description';
        }

        if ( ! empty( $args['custom_attributes'] ) && is_array( $args['custom_attributes'] ) ) {
            foreach ( $args['custom_attributes'] as $attribute => $attribute_value ) {
                $custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
            }
        }

        if ( ! empty( $args['validate'] ) ) {
            foreach ( $args['validate'] as $validate ) {
                $args['class'][] = 'validate-' . $validate;
            }
        }

        $field           = '';
        $label_id        = $args['id'];
        $sort            = $args['priority'] ? $args['priority'] : '';
        $field_container = '<p class="form-row %1$s" id="%2$s" data-priority="' . esc_attr( $sort ) . '">%3$s</p>';

        switch ( $args['type'] ) {
            case 'textarea':
                $field .= '<textarea name="' . esc_attr( $key ) . '" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" id="' . esc_attr( $args['id'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '" ' . ( empty( $args['custom_attributes']['rows'] ) ? ' rows="2"' : '' ) . ( empty( $args['custom_attributes']['cols'] ) ? ' cols="5"' : '' ) . implode( ' ', $custom_attributes ) . '>' . esc_textarea( $value ) . '</textarea>';

                break;
            case 'checkbox':
                $field = '<label class="checkbox ' . implode( ' ', $args['label_class'] ) . '" ' . implode( ' ', $custom_attributes ) . '>
                        <input type="' . esc_attr( $args['type'] ) . '" class="input-checkbox ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" value="1" ' . checked( $value, 1, false ) . ' /> ' . $args['label'] . $required . '</label>';

                break;
            case 'text':
            case 'password':
            case 'datetime':
            case 'datetime-local':
            case 'date':
            case 'month':
            case 'time':
            case 'week':
            case 'number':
            case 'email':
            case 'url':
            case 'tel':
                $field .= '<input type="' . esc_attr( $args['type'] ) . '" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '"  value="' . esc_attr( $value ) . '" ' . implode( ' ', $custom_attributes ) . ' />';

                break;
            case 'select':
                $field   = '';
                $options = '';

                if ( ! empty( $args['options'] ) ) {
                    foreach ( $args['options'] as $option_key => $option_text ) {
                        if ( '' === $option_key ) {
                            // If we have a blank option, select2 needs a placeholder.
                            if ( empty( $args['placeholder'] ) ) {
                                $args['placeholder'] = $option_text ? $option_text : __( 'Choose an option', 'masvideos' );
                            }
                            $custom_attributes[] = 'data-allow_clear="true"';
                        }
                        $options .= '<option value="' . esc_attr( $option_key ) . '" ' . selected( $value, $option_key, false ) . '>' . esc_attr( $option_text ) . '</option>';
                    }

                    $field .= '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" class="masvideos-select2 ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" ' . implode( ' ', $custom_attributes ) . ' data-placeholder="' . esc_attr( $args['placeholder'] ) . '">
                            ' . $options . '
                        </select>';
                }

                break;
            case 'radio':
                $label_id .= '_' . current( array_keys( $args['options'] ) );

                if ( ! empty( $args['options'] ) ) {
                    foreach ( $args['options'] as $option_key => $option_text ) {
                        $field .= '<input type="radio" class="input-radio ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" value="' . esc_attr( $option_key ) . '" name="' . esc_attr( $key ) . '" ' . implode( ' ', $custom_attributes ) . ' id="' . esc_attr( $args['id'] ) . '_' . esc_attr( $option_key ) . '"' . checked( $value, $option_key, false ) . ' />';
                        $field .= '<label for="' . esc_attr( $args['id'] ) . '_' . esc_attr( $option_key ) . '" class="radio ' . implode( ' ', $args['label_class'] ) . '">' . $option_text . '</label>';
                    }
                }

                break;
            case 'tag-multiselect':
                ob_start();
                ?>
                <select multiple="multiple" data-placeholder="<?php esc_attr_e( 'Select tags', 'masvideos' ); ?>" class="masvideos-select2-tags" name="<?php echo esc_attr( $key ); ?>[]">
                    <?php
                    $all_terms = get_terms( $args['taxonomy'], apply_filters( 'masvideos_term_multiselect', array( 'orderby' => 'name', 'hide_empty' => 0, ) ) );
                    if ( $all_terms ) {
                        foreach ( $all_terms as $term ) {
                            $options = array();
                            if( ! empty( $value ) && is_array( $value ) ) {
                                foreach ( $value as $term_id ) {
                                    $selected_term = get_term_by( 'id', $term_id, $args['taxonomy'] );
                                    if ( ! is_wp_error( $selected_term ) ) {
                                        $options[] = $selected_term->name;
                                    }
                                }
                            }
                            echo '<option value="' . esc_attr( $term->name ) . '"' . masvideos_selected( $term->name, $options ) . '>' . esc_attr( apply_filters( 'masvideos_term_name', $term->name, $term ) ) . '</option>';
                        }
                    }
                    ?>
                </select>
                <?php
                $field = ob_get_clean();

                break;
            case 'term-multiselect':
                ob_start();
                ?>
                <select multiple="multiple" data-placeholder="<?php esc_attr_e( 'Select terms', 'masvideos' ); ?>" class=" masvideos-select2" name="<?php echo esc_attr( $key ); ?>[]">
                    <?php
                    $all_terms = get_terms( $args['taxonomy'], apply_filters( 'masvideos_term_multiselect', array( 'orderby'    => 'name', 'hide_empty' => 0, ) ) );
                    if ( $all_terms ) {
                        foreach ( $all_terms as $term ) {
                            $options = $value;
                            $options = ! empty( $options ) ? $options : array();
                            echo '<option value="' . esc_attr( $term->term_id ) . '"' . masvideos_selected( $term->term_id, $options ) . '>' . esc_attr( apply_filters( 'masvideos_term_name', $term->name, $term ) ) . '</option>';
                        }
                    }
                    ?>
                </select>
                <?php
                $field = ob_get_clean();

                break;
            case 'video':
                $field_container = '<div class="form-row %1$s" id="%2$s" data-priority="' . esc_attr( $sort ) . '">%3$s</div>';
                ob_start();
                    if ( absint( $value ) ) {
                        $video_src = wp_get_attachment_url( $value );
                        echo do_shortcode('[video src="' . $video_src . '"]');
                    }
                    ?>
                    <input type="hidden" name="<?php echo esc_attr( $key ); ?>" class="upload_video_id" value="<?php echo esc_attr( $value ); ?>" />
                    <a href="#" class="button masvideos_upload_video_button tips"><?php echo esc_html__( 'Upload/Add video', 'masvideos' ); ?></a>
                    <a href="#" class="button masvideos_remove_video_button tips"><?php echo esc_html__( 'Remove this video', 'masvideos' ); ?></a>
                <?php
                $field = ob_get_clean();

                break;
            case 'image':
                ob_start();

                if ( absint( $value ) ) {
                    $image = wp_get_attachment_url( $value );
                } elseif ( function_exists( 'masvideos_placeholder_img_src' ) ) {
                    $image = masvideos_placeholder_img_src();
                } else {
                    $image = '';
                }

                if ( isset ( $image ) ) :
                    ?>
                    <img src="<?php echo esc_attr( $image ); ?>" class="upload_image_preview" data-placeholder-src="<?php echo esc_attr( masvideos_placeholder_img_src() ); ?>" alt="<?php echo esc_attr__( 'Image', 'masvideos' ); ?>" width="150px" height="auto" style="display:block; margin-bottom:1em; max-height:150px;" />
                    <?php 
                endif;
                ?>
                    <input type="hidden" name="<?php echo esc_attr( $key ); ?>" class="upload_image_id" value="<?php echo esc_attr( $value ); ?>" />
                    <a href="#" class="button masvideos_upload_image_button tips"><?php echo esc_html__( 'Upload/Add image', 'masvideos' ); ?></a>
                    <a href="#" class="button masvideos_remove_image_button tips"><?php echo esc_html__( 'Remove this image', 'masvideos' ); ?></a>
                <?php
                $field = ob_get_clean();

                break;
            case 'video-gallery-image':
                $field_container = '<div class="form-row %1$s video_images_container" id="%2$s" data-priority="' . esc_attr( $sort ) . '">%3$s</div>';
                ob_start();
                ?>
                <ul class="video_images">
                    <?php
                    $video_object = $post_id ? masvideos_get_video( $post_id ) : new MasVideos_Video();
                    $video_image_gallery = $video_object->get_gallery_image_ids( 'edit' );

                    $attachments         = array_filter( $video_image_gallery );
                    $update_meta         = false;
                    $updated_gallery_ids = array();

                    if ( ! empty( $attachments ) ) :
                        foreach ( $attachments as $attachment_id ) :
                            $attachment = wp_get_attachment_image( $attachment_id, 'thumbnail' );

                            // if attachment is empty skip.
                            if ( empty( $attachment ) ) {
                                $update_meta = true;
                                continue;
                            }

                            echo '<li class="image" data-attachment_id="' . esc_attr( $attachment_id ) . '">
                                    ' . $attachment . '
                                    <ul class="actions">
                                        <li><a href="#" class="delete tips" data-tip="' . esc_attr__( 'Delete image', 'masvideos' ) . '">' . __( 'Delete', 'masvideos' ) . '</a></li>
                                    </ul>
                                </li>';

                            // rebuild ids to be saved.
                            $updated_gallery_ids[] = $attachment_id;
                        endforeach;
                    endif;
                    ?>
                </ul>
                <input type="hidden" id="video_image_gallery" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( implode( ',', $updated_gallery_ids ) ); ?>" />
                <p class="add_video_images hide-if-no-js">
                    <a href="#" data-choose="<?php esc_attr_e( 'Add images to video gallery', 'masvideos' ); ?>" data-update="<?php esc_attr_e( 'Add to gallery', 'masvideos' ); ?>" data-delete="<?php esc_attr_e( 'Delete image', 'masvideos' ); ?>" data-text="<?php esc_attr_e( 'Delete', 'masvideos' ); ?>"><?php _e( 'Add video gallery images', 'masvideos' ); ?></a>
                </p>
                <?php
                $field = ob_get_clean();

                break;
        }

        if ( ! empty( $field ) ) {
            $field_html = '';

            if ( $args['label'] && 'checkbox' !== $args['type'] ) {
                $field_html .= '<label for="' . esc_attr( $label_id ) . '" class="' . esc_attr( implode( ' ', $args['label_class'] ) ) . '">' . $args['label'] . $required . '</label>';
            }

            $field_html .= '<span class="masvideos-input-wrapper">' . $field;

            if ( $args['description'] ) {
                $field_html .= '<span class="description" id="' . esc_attr( $args['id'] ) . '-description" aria-hidden="true">' . wp_kses_post( $args['description'] ) . '</span>';
            }

            $field_html .= '</span>';

            $container_class = esc_attr( implode( ' ', $args['class'] ) );
            $container_id    = esc_attr( $args['id'] ) . '_field';
            $field           = sprintf( $field_container, $container_class, $container_id, $field_html );
        }

        /**
         * Filter by type.
         */
        $field = apply_filters( 'masvideos_form_field_' . $args['type'], $field, $key, $args, $value );

        /**
         * General filter on form fields.
         *
         * @since 3.4.0
         */
        $field = apply_filters( 'masvideos_form_field', $field, $key, $args, $value );

        if ( $args['return'] ) {
            return $field;
        } else {
            echo $field; // WPCS: XSS ok.
        }
    }
}

if ( ! function_exists( 'masvideos_star_rating' ) ) {
    /**
     * Output a HTML element with a star rating for a given rating.
     *
     * This is a clone of wp_star_rating().
     * 
     * @since 1.0.0
     * @param array $args Array of star ratings arguments.
     * @return string Star rating HTML.
     */
    function masvideos_star_rating( $args = array() ) {
        $defaults = array(
            'rating' => 0,
            'type'   => 'rating',
            'number' => 0,
            'echo'   => true,
        );
        $r = wp_parse_args( $args, $defaults );
     
        // Non-English decimal places when the $rating is coming from a string
        $rating = (float) str_replace( ',', '.', $r['rating'] );
     
        // Convert Percentage to star rating, 0..5 in .5 increments
        if ( 'percent' === $r['type'] ) {
            $rating = round( $rating / 10, 0 ) / 2;
        }
     
        // Calculate the number of each type of star needed
        $full_stars = floor( $rating );
        $half_stars = ceil( $rating - $full_stars );
        $empty_stars = 10 - $full_stars - $half_stars;
     
        if ( $r['number'] ) {
            /* translators: 1: The rating, 2: The number of ratings */
            $format = _n( '%1$s rating based on %2$s rating', '%1$s rating based on %2$s ratings', $r['number'], 'masvideos' );
            $title = sprintf( $format, number_format_i18n( $rating, 1 ), number_format_i18n( $r['number'] ) );
        } else {
            /* translators: 1: The rating */
            $title = sprintf( __( '%s rating', 'masvideos' ), number_format_i18n( $rating, 1 ) );
        }
     
        $output = '<div class="star-rating">';
        $output .= '<span class="screen-reader-text">' . $title . '</span>';
        $output .= str_repeat( '<div class="star star-full" aria-hidden="true"></div>', $full_stars );
        $output .= str_repeat( '<div class="star star-half" aria-hidden="true"></div>', $half_stars );
        $output .= str_repeat( '<div class="star star-empty" aria-hidden="true"></div>', $empty_stars );
        $output .= '</div>';
     
        if ( $r['echo'] ) {
            echo $output;
        }
     
        return $output;
    }
}

if ( ! function_exists( 'masvideos_get_star_rating_html' ) ) {
    /**
     * Get HTML for star rating.
     *
     * @since  1.0.0
     * @param  float $rating Rating being shown.
     * @param  int   $count  Total number of ratings.
     * @return string
     */
    function masvideos_get_star_rating_html( $rating, $count = 0 ) {
        $args = array(
            'rating'    => $rating,
            'type'      => 'rating',
            'number'    => $count,
            'echo'      => false,
        );
        $html = 0 < $rating ? masvideos_star_rating( $args ) : '';
        return apply_filters( 'masvideos_get_star_rating_html', $html, $rating, $count );
    }
}

if ( ! function_exists( 'masvideos_breadcrumb' ) ) {

    /**
     * Output the MasVideos Breadcrumb.
     *
     * @param array $args Arguments.
     */
    function masvideos_breadcrumb( $args = array() ) {
        $args = wp_parse_args( $args, apply_filters( 'masvideos_breadcrumb_defaults', array(
            'delimiter'   => '&nbsp;&#47;&nbsp;',
            'wrap_before' => '<nav class="masvideos-breadcrumb">',
            'wrap_after'  => '</nav>',
            'before'      => '',
            'after'       => '',
            'home'        => _x( 'Home', 'breadcrumb', 'masvideos' ),
        ) ) );

        $breadcrumbs = new MasVideos_Breadcrumb();

        if ( ! empty( $args['home'] ) ) {
            $breadcrumbs->add_crumb( $args['home'], apply_filters( 'masvideos_breadcrumb_home_url', home_url() ) );
        }

        $args['breadcrumb'] = $breadcrumbs->generate();

        /**
         * MasVideos Breadcrumb hook
         *
         * @hooked MasVideos_Structured_Data::generate_breadcrumblist_data() - 10
         */
        do_action( 'masvideos_breadcrumb', $breadcrumbs, $args );

        masvideos_get_template( 'global/breadcrumb.php', $args );
    }
}

if ( ! function_exists( 'masvideos_photoswipe' ) ) {

    /**
     * Get the photoswipe markup template.
     */
    function masvideos_photoswipe() {
        masvideos_get_template( 'global/photoswipe.php' );
    }
}

if ( ! function_exists( 'masvideos_template_loop_content_area_open' ) ) {
    /**
     * Content Area open in the loop.
     */
    function masvideos_template_loop_content_area_open() {
        echo '<div id="primary" class="content-area">';
    }
}

if ( ! function_exists( 'masvideos_template_loop_content_area_close' ) ) {
    /**
     * Content Area open in the loop.
     */
    function masvideos_template_loop_content_area_close() {
        echo '</div><!-- /.content-area -->';
    }
}

if ( ! function_exists( 'masvideos_template_single_sharing' ) ) {

    /**
     * Output the sharing.
     */
    function masvideos_template_single_sharing() {
        do_action( 'masvideos_share' );
    }
}

if ( ! function_exists( 'masvideos_account_navigation' ) ) {

    /**
     * My Account navigation template.
     */
    function masvideos_account_navigation() {
        masvideos_get_template( 'myaccount/navigation.php' );
    }
}

if ( ! function_exists( 'masvideos_account_content' ) ) {

    /**
     * My Account content output.
     */
    function masvideos_account_content() {
        global $wp;

        if ( ! empty( $wp->query_vars ) ) {
            foreach ( $wp->query_vars as $key => $value ) {
                // Ignore pagename param.
                if ( 'pagename' === $key ) {
                    continue;
                }

                if ( has_action( 'masvideos_account_' . $key . '_endpoint' ) ) {
                    do_action( 'masvideos_account_' . $key . '_endpoint', $value );
                    return;
                }
            }
        }

        // No endpoint found? Default to dashboard.
        masvideos_get_template(
            'myaccount/dashboard.php',
            array(
                'current_user' => get_user_by( 'id', get_current_user_id() ),
            )
        );
    }
}

if ( ! function_exists( 'masvideos_account_videos' ) ) {

    /**
     * My Account > Videos template.
     *
     * @param int $current_page Current page number.
     */
    function masvideos_account_videos( $current_page ) {
        $current_page   = empty( $current_page ) ? 1 : absint( $current_page );
        $user_videos    = masvideos_get_videos(
            apply_filters(
                'masvideos_account_my_videos_query',
                array(
                    'author'   => get_current_user_id(),
                    'page'     => $current_page,
                    'paginate' => true,
                )
            )
        );

        masvideos_get_template(
            'myaccount/videos.php',
            array(
                'current_page'      => absint( $current_page ),
                'user_videos'       => $user_videos,
                'has_videos'        => 0 < $user_videos->total,
            )
        );
    }
}

if ( ! function_exists( 'masvideos_account_movie_playlists' ) ) {

    /**
     * My Account > Movie playlists template.
     */
    function masvideos_account_movie_playlists() {
        MasVideos_Shortcode_My_Account::manage_playlists( array( 'post_type' => 'movie_playlist' ) );
    }
}

if ( ! function_exists( 'masvideos_account_video_playlists' ) ) {

    /**
     * My Account > Video playlists template.
     */
    function masvideos_account_video_playlists() {
        MasVideos_Shortcode_My_Account::manage_playlists( array( 'post_type' => 'video_playlist' ) );
    }
}

if ( ! function_exists( 'masvideos_account_tv_show_playlists' ) ) {

    /**
     * My Account > TV Show playlists template.
     */
    function masvideos_account_tv_show_playlists() {
        MasVideos_Shortcode_My_Account::manage_playlists( array( 'post_type' => 'tv_show_playlist' ) );
    }
}

if ( ! function_exists( 'masvideos_account_edit_account' ) ) {

    /**
     * My Account > Edit account template.
     */
    function masvideos_account_edit_account() {
        MasVideos_Shortcode_My_Account::edit_account();
    }
}


/**
 * Get HTML for a gallery image.
 *
 * masvideos_gallery_thumbnail_size, masvideos_gallery_image_size and masvideos_gallery_full_size accept name based image sizes, or an array of width/height values.
 *
 * @since 1.1
 * @param int  $attachment_id Attachment ID.
 * @param bool $main_image Is this the main image or a thumbnail?.
 * @return string
 */
function masvideos_get_gallery_image_html( $attachment_id, $main_image = false ) {
    $gallery_thumbnail = masvideos_get_image_size( 'movie_thumbnail' );
    $thumbnail_size    = apply_filters( 'masvideos_gallery_thumbnail_size', array( $gallery_thumbnail['width'], $gallery_thumbnail['height'] ) );
    $image_size        = apply_filters( 'masvideos_gallery_image_size', $main_image ? 'masvideos_single' : $thumbnail_size );
    $full_size         = apply_filters( 'masvideos_gallery_full_size', apply_filters( 'masvideos_product_thumbnails_large_size', 'full' ) );
    $thumbnail_src     = wp_get_attachment_image_src( $attachment_id, $thumbnail_size );
    $full_src          = wp_get_attachment_image_src( $attachment_id, $full_size );
    $alt_text          = trim( wp_strip_all_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) );
    $image             = wp_get_attachment_image(
        $attachment_id,
        $image_size,
        false,
        apply_filters(
            'masvideos_gallery_image_html_attachment_image_params',
            array(
                'title'                   => _wp_specialchars( get_post_field( 'post_title', $attachment_id ), ENT_QUOTES, 'UTF-8', true ),
                'data-caption'            => _wp_specialchars( get_post_field( 'post_excerpt', $attachment_id ), ENT_QUOTES, 'UTF-8', true ),
                'data-src'                => esc_url( $full_src[0] ),
                'data-large_image'        => esc_url( $full_src[0] ),
                'data-large_image_width'  => esc_attr( $full_src[1] ),
                'data-large_image_height' => esc_attr( $full_src[2] ),
                'class'                   => esc_attr( $main_image ? 'wp-post-image' : '' ),
            ),
            $attachment_id,
            $image_size,
            $main_image
        )
    );

    return '<div data-thumb="' . esc_url( $thumbnail_src[0] ) . '" data-thumb-alt="' . esc_attr( $alt_text ) . '" class="masvideos-gallery__image"><a href="' . esc_url( $full_src[0] ) . '">' . $image . '</a></div>';
}
