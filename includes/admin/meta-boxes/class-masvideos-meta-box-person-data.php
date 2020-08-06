<?php
/**
 * Person Data
 *
 * Displays the person data box, tabbed, with several panels covering price, stock etc.
 *
 * @category Admin
 * @package  MasVideos/Admin/Meta Boxes
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * MasVideos_Meta_Box_Person_Data Class.
 */
class MasVideos_Meta_Box_Person_Data {

    /**
     * Output the metabox.
     *
     * @param WP_Post $post
     */
    public static function output( $post ) {
        global $thepostid, $person_object;

        $thepostid      = $post->ID;
        $person_object   = $thepostid ? masvideos_get_person( $thepostid ) : new MasVideos_Person();

        wp_nonce_field( 'masvideos_save_data', 'masvideos_meta_nonce' );

        include 'views/html-person-data-panel.php';
    }

    /**
     * Show tab content/settings.
     */
    private static function output_tabs() {
        global $post, $thepostid, $person_object;

        include 'views/html-person-data-general.php';
        include 'views/html-person-data-attributes.php';
    }

    /**
     * Return array of tabs to show.
     *
     * @return array
     */
    private static function get_person_data_tabs() {
        $tabs = apply_filters(
            'masvideos_person_data_tabs', array(
                'general'       => array(
                    'label'    => __( 'General', 'masvideos' ),
                    'target'   => 'general_person_data',
                    'class'    => array(),
                    'priority' => 10,
                ),
                'attribute'     => array(
                    'label'    => __( 'Attributes', 'masvideos' ),
                    'target'   => 'person_attributes',
                    'class'    => array(),
                    'priority' => 50,
                ),
            )
        );

        // Sort tabs based on priority.
        uasort( $tabs, array( __CLASS__, 'person_data_tabs_sort' ) );

        return $tabs;
    }

    /**
     * Callback to sort person data tabs on priority.
     *
     * @since 1.0.0
     * @param int $a First item.
     * @param int $b Second item.
     *
     * @return bool
     */
    private static function person_data_tabs_sort( $a, $b ) {
        if ( ! isset( $a['priority'], $b['priority'] ) ) {
            return -1;
        }

        if ( $a['priority'] == $b['priority'] ) {
            return 0;
        }

        return $a['priority'] < $b['priority'] ? -1 : 1;
    }

    public static function update_credit( $credit_id, $person_id, $meta_key ) {
        $person_object = masvideos_get_person( $person_id );

        $credits = $person_object->{"get_{$meta_key}"}( 'edit' );

        if( ! is_array( $credits ) ) {
            $credits = array();
        }

        if( ! in_array( $credit_id, $credits ) ) {
            $credits[] = $credit_id;
            $person_object->{"set_{$meta_key}"}( $credits );
            $person_object->save();
        }
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

        $post_type = 'person';

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

                if ( $post_type . '_' === substr( $attribute_name, 0, 7 ) ) {
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

                $attribute = new MasVideos_Person_Attribute();
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
     * Save meta box data.
     *
     * @param int  $post_id
     * @param $post
     */
    public static function save( $post_id, $post ) {
        // Process person type first so we have the correct class to run setters.
        $classname    = MasVideos_Person_Factory::get_person_classname( $post_id );
        $person       = new $classname( $post_id );
        $attributes   = self::prepare_attributes();

        $errors = $person->set_props(
            array(
                'featured'                  => isset( $_POST['_featured'] ),
                'catalog_visibility'        => masvideos_clean( wp_unslash( $_POST['_catalog_visibility'] ) ),
                'attributes'                => $attributes,
                'also_known_as'             => isset( $_POST['_also_known_as'] ) ? masvideos_clean( $_POST['_also_known_as'] ) : null,
                'place_of_birth'            => isset( $_POST['_place_of_birth'] ) ? masvideos_clean( $_POST['_place_of_birth'] ) : null,
                'birthday'                  => isset( $_POST['_birthday'] ) ? masvideos_clean( $_POST['_birthday'] ) : null,
                'deathday'                  => isset( $_POST['_deathday'] ) ? masvideos_clean( $_POST['_deathday'] ) : null,
                'imdb_id'                   => isset( $_POST['_imdb_id'] ) ? masvideos_clean( wp_unslash( $_POST['_imdb_id'] ) ) : null,
                'tmdb_id'                   => isset( $_POST['_tmdb_id'] ) ? masvideos_clean( wp_unslash( $_POST['_tmdb_id'] ) ) : null,
            )
        );

        if ( is_wp_error( $errors ) ) {
            MasVideos_Admin_Meta_Boxes::add_error( $errors->get_error_message() );
        }

        /**
         * @since 1.0.0 to set props before save.
         */
        do_action( 'masvideos_admin_process_person_object', $person );

        $person->save();
    }
}
