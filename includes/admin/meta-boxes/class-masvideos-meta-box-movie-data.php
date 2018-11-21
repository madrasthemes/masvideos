<?php
/**
 * Movie Data
 *
 * Displays the movie data box, tabbed, with several panels covering price, stock etc.
 *
 * @category Admin
 * @package  MasVideos/Admin/Meta Boxes
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * MasVideos_Meta_Box_Movie_Data Class.
 */
class MasVideos_Meta_Box_Movie_Data {

    /**
     * Output the metabox.
     *
     * @param WP_Post $post
     */
    public static function output( $post ) {
        global $thepostid, $movie_object;

        $thepostid      = $post->ID;
        $movie_object   = $thepostid ? masvideos_get_movie( $thepostid ) : new MasVideos_Movie();

        wp_nonce_field( 'masvideos_save_data', 'masvideos_meta_nonce' );

        include 'views/html-movie-data-panel.php';
    }

    /**
     * Show tab content/settings.
     */
    private static function output_tabs() {
        global $post, $thepostid, $movie_object;

        include 'views/html-movie-data-general.php';
        include 'views/html-movie-data-attributes.php';
    }

    /**
     * Return array of tabs to show.
     *
     * @return array
     */
    private static function get_movie_data_tabs() {
        $tabs = apply_filters(
            'masvideos_movie_data_tabs', array(
                'general'        => array(
                    'label'    => __( 'General', 'masvideos' ),
                    'target'   => 'general_movie_data',
                    'class'    => array(),
                    'priority' => 10,
                ),
                'attribute'      => array(
                    'label'    => __( 'Attributes', 'masvideos' ),
                    'target'   => 'movie_attributes',
                    'class'    => array(),
                    'priority' => 50,
                ),
            )
        );

        // Sort tabs based on priority.
        uasort( $tabs, array( __CLASS__, 'movie_data_tabs_sort' ) );

        return $tabs;
    }

    /**
     * Callback to sort movie data tabs on priority.
     *
     * @since 1.0.0
     * @param int $a First item.
     * @param int $b Second item.
     *
     * @return bool
     */
    private static function movie_data_tabs_sort( $a, $b ) {
        if ( ! isset( $a['priority'], $b['priority'] ) ) {
            return -1;
        }

        if ( $a['priority'] == $b['priority'] ) {
            return 0;
        }

        return $a['priority'] < $b['priority'] ? -1 : 1;
    }

    /**
     * Prepare attributes for save.
     *
     * @param array $data
     *
     * @return array
     */
    public static function prepare_attributes( $data = false ) {
        $attributes = array();

        if ( ! $data ) {
            $data = $_POST;
        }

        $post_type = 'movie';

        if ( isset( $data['attribute_names'], $data['attribute_values'] ) ) {
            $attribute_names         = $data['attribute_names'];
            $attribute_values        = $data['attribute_values'];
            $attribute_visibility    = isset( $data['attribute_visibility'] ) ? $data['attribute_visibility'] : array();
            $attribute_position      = $data['attribute_position'];
            $attribute_names_max_key = max( array_keys( $attribute_names ) );

            for ( $i = 0; $i <= $attribute_names_max_key; $i++ ) {
                if ( empty( $attribute_names[ $i ] ) || ! isset( $attribute_values[ $i ] ) ) {
                    continue;
                }
                $attribute_id   = 0;
                $attribute_name = masvideos_clean( $attribute_names[ $i ] );

                if ( $post_type . '_' === substr( $attribute_name, 0, 6 ) ) {
                    $attribute_id = masvideos_attribute_taxonomy_id_by_name( $post_type, $attribute_name );
                }

                $options = isset( $attribute_values[ $i ] ) ? $attribute_values[ $i ] : '';

                if ( is_array( $options ) ) {
                    // Term ids sent as array.
                    $options = wp_parse_id_list( $options );
                } else {
                    // Terms or text sent in textarea.
                    $options = 0 < $attribute_id ? masvideos_sanitize_textarea( masvideos_sanitize_term_text_based( $options ) ) : masvideos_sanitize_textarea( $options );
                    $options = masvideos_get_text_attributes( $options );
                }

                if ( empty( $options ) ) {
                    continue;
                }

                $attribute = new MasVideos_Movie_Attribute();
                $attribute->set_id( $attribute_id );
                $attribute->set_name( $attribute_name );
                $attribute->set_options( $options );
                $attribute->set_position( $attribute_position[ $i ] );
                $attribute->set_visible( isset( $attribute_visibility[ $i ] ) );
                $attributes[] = $attribute;
            }
        }
        return $attributes;
    }

    /**
     * Prepare attributes for a specific variation or defaults.
     *
     * @param  array  $all_attributes
     * @param  string $key_prefix
     * @param  int    $index
     * @return array
     */
    private static function prepare_set_attributes( $all_attributes, $key_prefix = 'attribute_', $index = null ) {
        $attributes = array();

        if ( $all_attributes ) {
            foreach ( $all_attributes as $attribute ) {
                if ( $attribute->get_variation() ) {
                    $attribute_key = sanitize_title( $attribute->get_name() );

                    if ( ! is_null( $index ) ) {
                        $value = isset( $_POST[ $key_prefix . $attribute_key ][ $index ] ) ? wp_unslash( $_POST[ $key_prefix . $attribute_key ][ $index ] ) : '';
                    } else {
                        $value = isset( $_POST[ $key_prefix . $attribute_key ] ) ? wp_unslash( $_POST[ $key_prefix . $attribute_key ] ) : '';
                    }

                    if ( $attribute->is_taxonomy() ) {
                        // Don't use masvideos_clean as it destroys sanitized characters.
                        $value = sanitize_title( $value );
                    } else {
                        $value = html_entity_decode( masvideos_clean( $value ), ENT_QUOTES, get_bloginfo( 'charset' ) ); // WPCS: sanitization ok.
                    }

                    $attributes[ $attribute_key ] = $value;
                }
            }
        }

        return $attributes;
    }

    /**
     * Save meta box data.
     *
     * @param int  $post_id
     * @param $post
     */
    public static function save( $post_id, $post ) {
        // Process movie type first so we have the correct class to run setters.
        // $movie_type = empty( $_POST['movie-type'] ) ? MasVideos_Movie_Factory::get_movie_type( $post_id ) : sanitize_title( stripslashes( $_POST['movie-type'] ) );
        $classname    = MasVideos_Movie_Factory::get_movie_classname( $post_id );
        $movie      = new $classname( $post_id );
        $attributes   = self::prepare_attributes();

        $errors = $movie->set_props(
            array(
                'movie_choice'              => isset( $_POST['_movie_choice'] ) ? masvideos_clean( $_POST['_movie_choice'] ) : null,
                'movie_attachment_id'       => isset( $_POST['_movie_attachment_id'] ) ? masvideos_clean( $_POST['_movie_attachment_id'] ) : null,
                'movie_embed_content'       => isset( $_POST['_movie_embed_content'] ) ? masvideos_sanitize_textarea_iframe( $_POST['_movie_embed_content'] ) : null,
                'movie_url_link'            => isset( $_POST['_movie_url_link'] ) ? masvideos_clean( $_POST['_movie_url_link'] ) : null,
                'attributes'                => $attributes,
                // 'default_attributes' => self::prepare_set_attributes( $attributes, 'default_attribute_' ),
            )
        );

        if ( is_wp_error( $errors ) ) {
            MasVideos_Admin_Meta_Boxes::add_error( $errors->get_error_message() );
        }

        /**
         * @since 1.0.0 to set props before save.
         */
        do_action( 'masvideos_admin_process_movie_object', $movie );

        $movie->save();
    }
}
