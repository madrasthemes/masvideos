<?php
/**
 * Abstract Person importer
 *
 * @package  MasVideos/Import
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Include dependencies.
 */
if ( ! class_exists( 'MasVideos_Importer_Interface', false ) ) {
    include_once MASVIDEOS_ABSPATH . 'includes/interfaces/class-masvideos-importer-interface.php';
}

/**
 * MasVideos_Person_Importer Class.
 */
abstract class MasVideos_Person_Importer implements MasVideos_Importer_Interface {

    /**
     * CSV file.
     *
     * @var string
     */
    protected $file = '';

    /**
     * The file position after the last read.
     *
     * @var int
     */
    protected $file_position = 0;

    /**
     * Importer parameters.
     *
     * @var array
     */
    protected $params = array();

    /**
     * Raw keys - CSV raw headers.
     *
     * @var array
     */
    protected $raw_keys = array();

    /**
     * Mapped keys - CSV headers.
     *
     * @var array
     */
    protected $mapped_keys = array();

    /**
     * Raw data.
     *
     * @var array
     */
    protected $raw_data = array();

    /**
     * Raw data.
     *
     * @var array
     */
    protected $file_positions = array();

    /**
     * Parsed data.
     *
     * @var array
     */
    protected $parsed_data = array();

    /**
     * Start time of current import.
     *
     * (default value: 0)
     *
     * @var int
     */
    protected $start_time = 0;

    /**
     * Get file raw headers.
     *
     * @return array
     */
    public function get_raw_keys() {
        return $this->raw_keys;
    }

    /**
     * Get file mapped headers.
     *
     * @return array
     */
    public function get_mapped_keys() {
        return ! empty( $this->mapped_keys ) ? $this->mapped_keys : $this->raw_keys;
    }

    /**
     * Get raw data.
     *
     * @return array
     */
    public function get_raw_data() {
        return $this->raw_data;
    }

    /**
     * Get parsed data.
     *
     * @return array
     */
    public function get_parsed_data() {
        return apply_filters( 'masvideos_person_importer_parsed_data', $this->parsed_data, $this->get_raw_data() );
    }

    /**
     * Get importer parameters.
     *
     * @return array
     */
    public function get_params() {
        return $this->params;
    }

    /**
     * Get file pointer position from the last read.
     *
     * @return int
     */
    public function get_file_position() {
        return $this->file_position;
    }

    /**
     * Get file pointer position as a percentage of file size.
     *
     * @return int
     */
    public function get_percent_complete() {
        $size = filesize( $this->file );
        if ( ! $size ) {
            return 0;
        }

        return absint( min( round( ( $this->file_position / $size ) * 100 ), 100 ) );
    }

    /**
     * Prepare a single person for create or update.
     *
     * @param  array $data     Item data.
     * @return MasVideos_Person|WP_Error
     */
    protected function get_person_object( $data ) {
        $id = isset( $data['id'] ) ? absint( $data['id'] ) : 0;

        if ( ! empty( $data['id'] ) ) {
            $person = masvideos_get_person( $id );

            if ( ! $person ) {
                return new WP_Error(
                    'masvideos_person_csv_importer_invalid_id',
                    /* translators: %d: person ID */
                    sprintf( __( 'Invalid person ID %d.', 'masvideos' ), $id ),
                    array(
                        'id'     => $id,
                        'status' => 401,
                    )
                );
            }
        } else {
            $person = new MasVideos_Person( $id );
        }

        return apply_filters( 'masvideos_person_import_get_person_object', $person, $data );
    }

    /**
     * Process a single item and save.
     *
     * @throws Exception If item cannot be processed.
     * @param  array $data Raw CSV data.
     * @return array|MasVideos_Error
     */
    protected function process_item( $data ) {
        try {
            do_action( 'masvideos_person_import_before_process_item', $data );

            // Get person ID from TMDB ID if created during the importation.
            if ( empty( $data['id'] ) && ! empty( $data['tmdb_id'] ) ) {
                $person_id = masvideos_get_person_id_by_tmdb_id( $data['tmdb_id'] );

                if ( $person_id ) {
                    $data['id'] = $person_id;
                }
            }

            // Get person ID from IMDB ID if created during the importation.
            if ( empty( $data['id'] ) && ! empty( $data['imdb_id'] ) ) {
                $person_id = masvideos_get_person_id_by_imdb_id( $data['imdb_id'] );

                if ( $person_id ) {
                    $data['id'] = $person_id;
                }
            }

            $object   = $this->get_person_object( $data );
            $updating = false;

            if ( is_wp_error( $object ) ) {
                return $object;
            }

            if ( $object->get_id() && 'importing' !== $object->get_status() ) {
                $updating = true;
            }

            if ( 'importing' === $object->get_status() ) {
                $object->set_status( 'publish' );
                $object->set_slug( '' );
            }

            $result = $object->set_props( array_diff_key( $data, array_flip( array( 'meta_data', 'raw_image_id', 'raw_gallery_image_ids', 'raw_attributes' ) ) ) );

            if ( is_wp_error( $result ) ) {
                throw new Exception( $result->get_error_message() );
            }

            $this->set_person_data( $object, $data );
            $this->set_image_data( $object, $data );
            $this->set_meta_data( $object, $data );

            $object = apply_filters( 'masvideos_person_import_pre_insert_person_object', $object, $data );
            $object->save();

            do_action( 'masvideos_person_import_inserted_person_object', $object, $data );

            return array(
                'id'      => $object->get_id(),
                'updated' => $updating,
            );
        } catch ( Exception $e ) {
            return new WP_Error( 'masvideos_person_importer_error', $e->getMessage(), array( 'status' => $e->getCode() ) );
        }
    }

    /**
     * Convert raw image URLs to IDs and set.
     *
     * @param MasVideos_Person $person Person instance.
     * @param array           $data    Item data.
     */
    protected function set_image_data( &$person, $data ) {
        // Image URLs need converting to IDs before inserting.
        if ( isset( $data['raw_image_id'] ) ) {
            $person->set_image_id( $this->get_attachment_id_from_url( $data['raw_image_id'], $person->get_id() ) );
        }

        // Gallery image URLs need converting to IDs before inserting.
        if ( isset( $data['raw_gallery_image_ids'] ) ) {
            $gallery_image_ids = array();

            foreach ( $data['raw_gallery_image_ids'] as $image_id ) {
                $gallery_image_ids[] = $this->get_attachment_id_from_url( $image_id, $person->get_id() );
            }
            $person->set_gallery_image_ids( $gallery_image_ids );
        }
    }

    /**
     * Append meta data.
     *
     * @param MasVideos_Person $person Person instance.
     * @param array           $data  Item data.
     */
    protected function set_meta_data( &$person, $data ) {
        if ( isset( $data['meta_data'] ) ) {
            foreach ( $data['meta_data'] as $meta ) {
                $person->update_meta_data( $meta['key'], $meta['value'] );
            }
        }
    }

    /**
     * Set person data.
     *
     * @param MasVideos_Person $person Person instance.
     * @param array           $data  Item data.
     * @throws Exception             If data cannot be set.
     */
    protected function set_person_data( &$person, $data ) {
        if ( isset( $data['raw_attributes'] ) ) {
            $attributes          = array();
            // $existing_attributes = $person->get_attributes();

            foreach ( $data['raw_attributes'] as $position => $attribute ) {
                $attribute_id = 0;

                // Get ID if is a global attribute.
                if ( ! empty( $attribute['taxonomy'] ) ) {
                    $attribute_id = $this->get_attribute_taxonomy_id( $attribute['name'] );
                }

                // Set attribute visibility.
                if ( isset( $attribute['visible'] ) ) {
                    $is_visible = $attribute['visible'];
                } else {
                    $is_visible = 1;
                }

                // Get name.
                $attribute_name = $attribute_id ? masvideos_attribute_taxonomy_name_by_id( $attribute_id ) : $attribute['name'];

                if ( $attribute_id ) {
                    if ( isset( $attribute['value'] ) ) {
                        $options = array_map( 'masvideos_sanitize_term_text_based', $attribute['value'] );
                        $options = array_filter( $options, 'strlen' );
                    } else {
                        $options = array();
                    }

                    if ( ! empty( $options ) ) {
                        $attribute_object = new MasVideos_Person_Attribute();
                        $attribute_object->set_id( $attribute_id );
                        $attribute_object->set_name( $attribute_name );
                        $attribute_object->set_options( $options );
                        $attribute_object->set_position( $position );
                        $attribute_object->set_visible( $is_visible );
                        $attributes[] = $attribute_object;
                    }
                } elseif ( isset( $attribute['value'] ) ) {
                    $attribute_object = new MasVideos_Person_Attribute();
                    $attribute_object->set_name( $attribute['name'] );
                    $attribute_object->set_options( $attribute['value'] );
                    $attribute_object->set_position( $position );
                    $attribute_object->set_visible( $is_visible );
                    $attributes[] = $attribute_object;
                }
            }

            $person->set_attributes( $attributes );
        }
    }

    /**
     * Get attachment ID.
     *
     * @param  string $url       Attachment URL.
     * @param  int    $person_id  Person ID.
     * @return int
     * @throws Exception If attachment cannot be loaded.
     */
    public function get_attachment_id_from_url( $url, $person_id ) {
        if ( empty( $url ) ) {
            return 0;
        }

        $id         = 0;
        $upload_dir = wp_upload_dir( null, false );
        $base_url   = $upload_dir['baseurl'] . '/';

        // Check first if attachment is inside the WordPress uploads directory, or we're given a filename only.
        if ( false !== strpos( $url, $base_url ) || false === strpos( $url, '://' ) ) {
            // Search for yyyy/mm/slug.extension or slug.extension - remove the base URL.
            $file = str_replace( $base_url, '', $url );
            $args = array(
                'post_type'   => 'attachment',
                'post_status' => 'any',
                'fields'      => 'ids',
                'meta_query'  => array( // @codingStandardsIgnoreLine.
                    'relation' => 'OR',
                    array(
                        'key'     => '_wp_attached_file',
                        'value'   => '^' . $file,
                        'compare' => 'REGEXP',
                    ),
                    array(
                        'key'     => '_wp_attached_file',
                        'value'   => '/' . $file,
                        'compare' => 'LIKE',
                    ),
                    array(
                        'key'     => '_masvideos_attachment_source',
                        'value'   => '/' . $file,
                        'compare' => 'LIKE',
                    ),
                ),
            );
        } else {
            // This is an external URL, so compare to source.
            $args = array(
                'post_type'   => 'attachment',
                'post_status' => 'any',
                'fields'      => 'ids',
                'meta_query'  => array( // @codingStandardsIgnoreLine.
                    array(
                        'value' => $url,
                        'key'   => '_masvideos_attachment_source',
                    ),
                ),
            );
        }

        $ids = get_posts( $args ); // @codingStandardsIgnoreLine.

        if ( $ids ) {
            $id = current( $ids );
        }

        // Upload if attachment does not exists.
        if ( ! $id && stristr( $url, '://' ) ) {
            $upload = masvideos_rest_upload_image_from_url( $url );

            if ( is_wp_error( $upload ) ) {
                throw new Exception( $upload->get_error_message(), 400 );
            }

            $id = masvideos_rest_set_uploaded_image_as_attachment( $upload, $person_id );

            if ( ! wp_attachment_is_image( $id ) ) {
                /* translators: %s: image URL */
                throw new Exception( sprintf( __( 'Not able to attach "%s".', 'masvideos' ), $url ), 400 );
            }

            // Save attachment source for future reference.
            update_post_meta( $id, '_masvideos_attachment_source', $url );
        }

        if ( ! $id ) {
            /* translators: %s: image URL */
            throw new Exception( sprintf( __( 'Unable to use image "%s".', 'masvideos' ), $url ), 400 );
        }

        return $id;
    }

    /**
     * Get attribute taxonomy ID from the imported data.
     * If does not exists register a new attribute.
     *
     * @param  string $raw_name Attribute name.
     * @return int
     * @throws Exception If taxonomy cannot be loaded.
     */
    public function get_attribute_taxonomy_id( $raw_name ) {
        global $wpdb, $masvideos_person_attributes;

        // These are exported as labels, so convert the label to a name if possible first.
        $attribute_labels = wp_list_pluck( masvideos_get_attribute_taxonomies( 'person' ), 'attribute_label', 'attribute_name' );
        $attribute_name   = array_search( $raw_name, $attribute_labels, true );

        if ( ! $attribute_name ) {
            $attribute_name = masvideos_sanitize_taxonomy_name( $raw_name );
        }

        $attribute_id = masvideos_attribute_taxonomy_id_by_name( 'person', $attribute_name );;

        // Get the ID from the name.
        if ( $attribute_id ) {
            return $attribute_id;
        }

        // If the attribute does not exist, create it.
        $attribute_id = masvideos_create_attribute( array(
            'name'         => $raw_name,
            'slug'         => $attribute_name,
            'type'         => 'select',
            'order_by'     => 'menu_order',
            'has_archives' => false,
            'post_type'    => 'person',
        ) );

        if ( is_wp_error( $attribute_id ) ) {
            throw new Exception( $attribute_id->get_error_message(), 400 );
        }

        // Register as taxonomy while importing.
        $taxonomy_name = masvideos_attribute_taxonomy_name( 'person', $attribute_name );
        register_taxonomy(
            $taxonomy_name,
            apply_filters( 'masvideos_taxonomy_objects_' . $taxonomy_name, array( 'person' ) ),
            apply_filters( 'masvideos_taxonomy_args_' . $taxonomy_name, array(
                'labels'       => array(
                    'name' => $raw_name,
                ),
                'hierarchical' => true,
                'show_ui'      => false,
                'query_var'    => true,
                'rewrite'      => false,
            ) )
        );

        // Set person attributes global.
        $masvideos_person_attributes = array();

        foreach ( masvideos_get_attribute_taxonomies( 'person' ) as $taxonomy ) {
            $masvideos_person_attributes[ masvideos_attribute_taxonomy_name( $taxonomy->post_type, $taxonomy->attribute_name ) ] = $taxonomy;
        }

        return $attribute_id;
    }

    /**
     * Memory exceeded
     *
     * Ensures the batch process never exceeds 90%
     * of the maximum WordPress memory.
     *
     * @return bool
     */
    protected function memory_exceeded() {
        $memory_limit   = $this->get_memory_limit() * 0.9; // 90% of max memory
        $current_memory = memory_get_usage( true );
        $return         = false;
        if ( $current_memory >= $memory_limit ) {
            $return = true;
        }
        return apply_filters( 'masvideos_person_importer_memory_exceeded', $return );
    }

    /**
     * Get memory limit
     *
     * @return int
     */
    protected function get_memory_limit() {
        if ( function_exists( 'ini_get' ) ) {
            $memory_limit = ini_get( 'memory_limit' );
        } else {
            // Sensible default.
            $memory_limit = '128M';
        }

        if ( ! $memory_limit || -1 === intval( $memory_limit ) ) {
            // Unlimited, set to 32GB.
            $memory_limit = '32000M';
        }
        return intval( $memory_limit ) * 1024 * 1024;
    }

    /**
     * Time exceeded.
     *
     * Ensures the batch never exceeds a sensible time limit.
     * A timeout limit of 30s is common on shared hosting.
     *
     * @return bool
     */
    protected function time_exceeded() {
        $finish = $this->start_time + apply_filters( 'masvideos_person_importer_default_time_limit', 20 ); // 20 seconds
        $return = false;
        if ( time() >= $finish ) {
            $return = true;
        }
        return apply_filters( 'masvideos_person_importer_time_exceeded', $return );
    }

    /**
     * Explode CSV cell values using commas by default, and handling escaped
     * separators.
     *
     * @since  3.2.0
     * @param  string $value Value to explode.
     * @return array
     */
    protected function explode_values( $value ) {
        $value  = str_replace( '\\,', '::separator::', $value );
        $values = explode( ',', $value );
        $values = array_map( array( $this, 'explode_values_formatter' ), $values );

        return $values;
    }

    /**
     * Remove formatting and trim each value.
     *
     * @since  3.2.0
     * @param  string $value Value to format.
     * @return string
     */
    protected function explode_values_formatter( $value ) {
        return trim( str_replace( '::separator::', ',', $value ) );
    }

    /**
     * The exporter prepends a ' to fields that start with a - which causes
     * issues with negative numbers. This removes the ' if the input is still a valid
     * number after removal.
     *
     * @since 3.3.0
     * @param string $value A numeric string that may or may not have ' prepended.
     * @return string
     */
    protected function unescape_negative_number( $value ) {
        if ( 0 === strpos( $value, "'-" ) ) {
            $unescaped = trim( $value, "'" );
            if ( is_numeric( $unescaped ) ) {
                return $unescaped;
            }
        }
        return $value;
    }
}
