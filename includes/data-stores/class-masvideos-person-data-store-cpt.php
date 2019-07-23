<?php
/**
 * MasVideos_Person_Data_Store_CPT class file.
 *
 * @package MasVideos/Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * MasVideos Person Data Store: Stored in CPT.
 *
 * @version  1.0.0
 */
class MasVideos_Person_Data_Store_CPT extends MasVideos_Data_Store_WP implements MasVideos_Object_Data_Store_Interface, MasVideos_Person_Data_Store_Interface {

    /**
     * Data stored in meta keys, but not considered "meta".
     *
     * @since 1.0.0
     * @var array
     */
    protected $internal_meta_keys = array(
        '_visibility',
        '_default_attributes',
        '_person_attributes',
        '_movie_cast',
        '_movie_crew',
        '_tv_show_cast',
        '_tv_show_crew',
        '_featured',
        '_thumbnail_id',
        '_file_paths',
        '_person_image_gallery',
        '_person_version',
        '_wp_old_slug',
        '_edit_last',
        '_edit_lock',
        '_also_known_as',
        '_place_of_birth',
        '_birthday',
        '_deathday',
        '_imdb_id',
        '_tmdb_id'
    );

    /**
     * If we have already saved our extra data, don't do automatic / default handling.
     *
     * @var bool
     */
    protected $extra_data_saved = false;

    /**
     * Stores updated props.
     *
     * @var array
     */
    protected $updated_props = array();

    /*
    |--------------------------------------------------------------------------
    | CRUD Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Method to create a new person in the database.
     *
     * @param MasVideos_Person $person Person object.
     */
    public function create( &$person ) {
        if ( ! $person->get_date_created( 'edit' ) ) {
            $person->set_date_created( current_time( 'timestamp', true ) );
        }

        $id = wp_insert_post(
            apply_filters(
                'masvideos_new_person_data', array(
                    'post_type'      => 'person',
                    'post_status'    => $person->get_status() ? $person->get_status() : 'publish',
                    'post_author'    => get_current_user_id(),
                    'post_title'     => $person->get_name() ? $person->get_name() : __( 'Person', 'masvideos' ),
                    'post_content'   => $person->get_description(),
                    'post_excerpt'   => $person->get_short_description(),
                    'post_parent'    => $person->get_parent_id(),
                    'comment_status' => 'closed',
                    'ping_status'    => 'closed',
                    'menu_order'     => $person->get_menu_order(),
                    'post_date'      => gmdate( 'Y-m-d H:i:s', $person->get_date_created( 'edit' )->getOffsetTimestamp() ),
                    'post_date_gmt'  => gmdate( 'Y-m-d H:i:s', $person->get_date_created( 'edit' )->getTimestamp() ),
                    'post_name'      => $person->get_slug( 'edit' ),
                )
            ), true
        );

        if ( $id && ! is_wp_error( $id ) ) {
            $person->set_id( $id );

            $this->update_post_meta( $person, true );
            $this->update_terms( $person, true );
            $this->update_visibility( $person, true );
            $this->update_attributes( $person, true );
            $this->handle_updated_props( $person );

            $person->save_meta_data();
            $person->apply_changes();

            $this->clear_caches( $person );

            do_action( 'masvideos_new_person', $id );
        }
    }

    /**
     * Method to read a person from the database.
     *
     * @param MasVideos_Person $person Person object.
     * @throws Exception If invalid person.
     */
    public function read( &$person ) {
        $person->set_defaults();
        $post_object = get_post( $person->get_id() );

        if ( ! $person->get_id() || ! $post_object || 'person' !== $post_object->post_type ) {
            throw new Exception( __( 'Invalid person.', 'masvideos' ) );
        }

        $person->set_props(
            array(
                'name'              => $post_object->post_title,
                'slug'              => $post_object->post_name,
                'date_created'      => 0 < $post_object->post_date_gmt ? masvideos_string_to_timestamp( $post_object->post_date_gmt ) : null,
                'date_modified'     => 0 < $post_object->post_modified_gmt ? masvideos_string_to_timestamp( $post_object->post_modified_gmt ) : null,
                'status'            => $post_object->post_status,
                'description'       => $post_object->post_content,
                'short_description' => $post_object->post_excerpt,
                'parent_id'         => $post_object->post_parent,
                'menu_order'        => $post_object->menu_order
            )
        );

        $this->read_attributes( $person );
        $this->read_visibility( $person );
        $this->read_person_data( $person );
        $this->read_extra_data( $person );
        $person->set_object_read( true );
    }

    /**
     * Method to update a person in the database.
     *
     * @param MasVideos_Person $person Person object.
     */
    public function update( &$person ) {
        $person->save_meta_data();
        $changes = $person->get_changes();

        // Only update the post when the post data changes.
        if ( array_intersect( array( 'description', 'short_description', 'name', 'parent_id', 'reviews_allowed', 'status', 'menu_order', 'date_created', 'date_modified', 'slug' ), array_keys( $changes ) ) ) {
            $post_data = array(
                'post_content'   => $person->get_description( 'edit' ),
                'post_excerpt'   => $person->get_short_description( 'edit' ),
                'post_title'     => $person->get_name( 'edit' ),
                'post_parent'    => $person->get_parent_id( 'edit' ),
                'comment_status' => 'closed',
                'post_status'    => $person->get_status( 'edit' ) ? $person->get_status( 'edit' ) : 'publish',
                'menu_order'     => $person->get_menu_order( 'edit' ),
                'post_name'      => $person->get_slug( 'edit' ),
                'post_type'      => 'person',
            );
            if ( $person->get_date_created( 'edit' ) ) {
                $post_data['post_date']     = gmdate( 'Y-m-d H:i:s', $person->get_date_created( 'edit' )->getOffsetTimestamp() );
                $post_data['post_date_gmt'] = gmdate( 'Y-m-d H:i:s', $person->get_date_created( 'edit' )->getTimestamp() );
            }
            if ( isset( $changes['date_modified'] ) && $person->get_date_modified( 'edit' ) ) {
                $post_data['post_modified']     = gmdate( 'Y-m-d H:i:s', $person->get_date_modified( 'edit' )->getOffsetTimestamp() );
                $post_data['post_modified_gmt'] = gmdate( 'Y-m-d H:i:s', $person->get_date_modified( 'edit' )->getTimestamp() );
            } else {
                $post_data['post_modified']     = current_time( 'mysql' );
                $post_data['post_modified_gmt'] = current_time( 'mysql', 1 );
            }

            /**
             * When updating this object, to prevent infinite loops, use $wpdb
             * to update data, since wp_update_post spawns more calls to the
             * save_post action.
             *
             * This ensures hooks are fired by either WP itself (admin screen save),
             * or an update purely from CRUD.
             */
            if ( doing_action( 'save_post' ) ) {
                $GLOBALS['wpdb']->update( $GLOBALS['wpdb']->posts, $post_data, array( 'ID' => $person->get_id() ) );
                clean_post_cache( $person->get_id() );
            } else {
                wp_update_post( array_merge( array( 'ID' => $person->get_id() ), $post_data ) );
            }
            $person->read_meta_data( true ); // Refresh internal meta data, in case things were hooked into `save_post` or another WP hook.

        } else { // Only update post modified time to record this save event.
            $GLOBALS['wpdb']->update(
                $GLOBALS['wpdb']->posts,
                array(
                    'post_modified'     => current_time( 'mysql' ),
                    'post_modified_gmt' => current_time( 'mysql', 1 ),
                ),
                array(
                    'ID' => $person->get_id(),
                )
            );
            clean_post_cache( $person->get_id() );
        }

        $this->update_post_meta( $person );
        $this->update_terms( $person );
        $this->update_visibility( $person );
        $this->update_attributes( $person );
        $this->handle_updated_props( $person );

        $person->apply_changes();

        $this->clear_caches( $person );

        do_action( 'masvideos_update_person', $person->get_id() );
    }

    /**
     * Method to delete a person from the database.
     *
     * @param MasVideos_Person $person Person object.
     * @param array      $args Array of args to pass to the delete method.
     */
    public function delete( &$person, $args = array() ) {
        $id        = $person->get_id();
        $post_type = 'person';

        $args = wp_parse_args(
            $args, array(
                'force_delete' => false,
            )
        );

        if ( ! $id ) {
            return;
        }

        if ( $args['force_delete'] ) {
            do_action( 'masvideos_before_delete_' . $post_type, $id );
            wp_delete_post( $id );
            $person->set_id( 0 );
            do_action( 'masvideos_delete_' . $post_type, $id );
        } else {
            wp_trash_post( $id );
            $person->set_status( 'trash' );
            do_action( 'masvideos_trash_' . $post_type, $id );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Additional Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Read person data. Can be overridden by child classes to load other props.
     *
     * @param MasVideos_Person $person Person object.
     * @since 1.0.0
     */
    protected function read_person_data( &$person ) {
        $id             = $person->get_id();

        $person->set_props(
            array(
                'default_attributes'    => get_post_meta( $id, '_default_attributes', true ),
                'movie_cast'            => get_post_meta( $id, '_movie_cast', true ),
                'movie_crew'            => get_post_meta( $id, '_movie_crew', true ),
                'tv_show_cast'          => get_post_meta( $id, '_tv_show_cast', true ),
                'tv_show_crew'          => get_post_meta( $id, '_tv_show_crew', true ),
                'category_ids'          => $this->get_term_ids( $person, 'person_cat' ),
                'tag_ids'               => $this->get_term_ids( $person, 'person_tag' ),
                'gallery_image_ids'     => array_filter( explode( ',', get_post_meta( $id, '_person_image_gallery', true ) ) ),
                'image_id'              => get_post_thumbnail_id( $id ),
                'also_known_as'         => get_post_meta( $id, '_also_known_as', true ),
                'place_of_birth'        => get_post_meta( $id, '_place_of_birth', true ),
                'birthday'              => get_post_meta( $id, '_birthday', true ),
                'deathday'              => get_post_meta( $id, '_deathday', true ),
                'imdb_id'               => get_post_meta( $id, '_imdb_id', true ),
                'tmdb_id'               => get_post_meta( $id, '_tmdb_id', true ),
            )
        );
    }

    /**
     * Read extra data associated with the person, like button text or person URL for external persons.
     *
     * @param MasVideos_Person $person Person object.
     * @since 1.0.0
     */
    protected function read_extra_data( &$person ) {
        foreach ( $person->get_extra_data_keys() as $key ) {
            $function = 'set_' . $key;
            if ( is_callable( array( $person, $function ) ) ) {
                $person->{$function}( get_post_meta( $person->get_id(), '_' . $key, true ) );
            }
        }
    }

    /**
     * Convert visibility terms to props.
     * Catalog visibility valid values are 'visible', 'catalog', 'search', and 'hidden'.
     *
     * @param MasVideos_Person $person Person object.
     * @since 1.0.0
     */
    protected function read_visibility( &$person ) {
        $terms           = get_the_terms( $person->get_id(), 'person_visibility' );
        $term_names      = is_array( $terms ) ? wp_list_pluck( $terms, 'name' ) : array();
        $featured        = in_array( 'featured', $term_names, true );
        $exclude_search  = in_array( 'exclude-from-search', $term_names, true );
        $exclude_catalog = in_array( 'exclude-from-catalog', $term_names, true );

        if ( $exclude_search && $exclude_catalog ) {
            $catalog_visibility = 'hidden';
        } elseif ( $exclude_search ) {
            $catalog_visibility = 'catalog';
        } elseif ( $exclude_catalog ) {
            $catalog_visibility = 'search';
        } else {
            $catalog_visibility = 'visible';
        }

        $person->set_props(
            array(
                'featured'           => $featured,
                'catalog_visibility' => $catalog_visibility,
            )
        );
    }

    /**
     * Read attributes from post meta.
     *
     * @param MasVideos_Person $person Person object.
     */
    protected function read_attributes( &$person ) {
        $meta_attributes = get_post_meta( $person->get_id(), '_person_attributes', true );

        if ( ! empty( $meta_attributes ) && is_array( $meta_attributes ) ) {
            $attributes = array();
            foreach ( $meta_attributes as $meta_attribute_key => $meta_attribute_value ) {
                $meta_value = array_merge(
                    array(
                        'name'         => '',
                        'value'        => '',
                        'position'     => 0,
                        'is_visible'   => 0,
                        'is_taxonomy'  => 0,
                    ), (array) $meta_attribute_value
                );

                // Check if is a taxonomy attribute.
                if ( ! empty( $meta_value['is_taxonomy'] ) ) {
                    if ( ! taxonomy_exists( $meta_value['name'] ) ) {
                        continue;
                    }
                    $id      = masvideos_attribute_taxonomy_id_by_name( 'person', $meta_value['name'] );
                    $options = masvideos_get_object_terms( $person->get_id(), $meta_value['name'], 'term_id' );
                } else {
                    $id      = 0;
                    $options = masvideos_get_text_attributes( $meta_value['value'] );
                }

                $attribute = new MasVideos_Person_Attribute();
                $attribute->set_id( $id );
                $attribute->set_name( $meta_value['name'] );
                $attribute->set_options( $options );
                $attribute->set_position( $meta_value['position'] );
                $attribute->set_visible( $meta_value['is_visible'] );
                $attributes[] = $attribute;
            }
            $person->set_attributes( $attributes );
        }
    }

    /**
     * Helper method that updates all the post meta for a person based on it's settings in the MasVideos_Person class.
     *
     * @param MasVideos_Person $person Person object.
     * @param bool       $force Force update. Used during create.
     * @since 1.0.0
     */
    protected function update_post_meta( &$person, $force = false ) {
        $meta_key_to_props = array(
            '_default_attributes'           => 'default_attributes',
            '_movie_cast'                   => 'movie_cast',
            '_movie_crew'                   => 'movie_crew',
            '_tv_show_cast'                 => 'tv_show_cast',
            '_tv_show_crew'                 => 'tv_show_crew',
            '_person_image_gallery'         => 'gallery_image_ids',
            '_thumbnail_id'                 => 'image_id',
            '_also_known_as'                => 'also_known_as',
            '_place_of_birth'               => 'place_of_birth',
            '_birthday'                     => 'birthday',
            '_deathday'                     => 'deathday',
            '_imdb_id'                      => 'imdb_id',
            '_tmdb_id'                      => 'tmdb_id',
        );

        // Make sure to take extra data (like person url or text for external persons) into account.
        $extra_data_keys = $person->get_extra_data_keys();

        foreach ( $extra_data_keys as $key ) {
            $meta_key_to_props[ '_' . $key ] = $key;
        }

        $props_to_update = $force ? $meta_key_to_props : $this->get_props_to_update( $person, $meta_key_to_props );

        foreach ( $props_to_update as $meta_key => $prop ) {
            $value = $person->{"get_$prop"}( 'edit' );
            $value = is_string( $value ) ? wp_slash( $value ) : $value;
            switch ( $prop ) {
                case 'gallery_image_ids':
                    $updated = update_post_meta( $person->get_id(), $meta_key, implode( ',', $value ) );
                    break;
                case 'image_id':
                    if ( ! empty( $value ) ) {
                        set_post_thumbnail( $person->get_id(), $value );
                    } else {
                        delete_post_meta( $person->get_id(), '_thumbnail_id' );
                    }
                    $updated = true;
                    break;
                case 'birthday':
                case 'deathday':
                    $updated = update_post_meta( $person->get_id(), $meta_key, $value ? $value->getTimestamp() : '' );
                    break;
                default:
                    $updated = update_post_meta( $person->get_id(), $meta_key, $value );
                    break;
            }
            if ( $updated ) {
                $this->updated_props[] = $prop;
            }
        }

        // Update extra data associated with the person like button text or person URL for external persons.
        if ( ! $this->extra_data_saved ) {
            foreach ( $extra_data_keys as $key ) {
                if ( ! array_key_exists( '_' . $key, $props_to_update ) ) {
                    continue;
                }
                $function = 'get_' . $key;
                if ( is_callable( array( $person, $function ) ) ) {
                    $value = $person->{$function}( 'edit' );
                    $value = is_string( $value ) ? wp_slash( $value ) : $value;

                    if ( update_post_meta( $person->get_id(), '_' . $key, $value ) ) {
                        $this->updated_props[] = $key;
                    }
                }
            }
        }
    }

    /**
     * Handle updated meta props after updating meta data.
     *
     * @since 1.0.0
     * @param MasVideos_Person $person Person Object.
     */
    protected function handle_updated_props( &$person ) {

        // Trigger action so 3rd parties can deal with updated props.
        do_action( 'masvideos_person_object_updated_props', $person, $this->updated_props );

        // After handling, we can reset the props array.
        $this->updated_props = array();
    }

    /**
     * For all stored terms in all taxonomies, save them to the DB.
     *
     * @param MasVideos_Person $person Person object.
     * @param bool       $force Force update. Used during create.
     * @since 1.0.0
     */
    protected function update_terms( &$person, $force = false ) {
        $changes = $person->get_changes();

        if ( $force || array_key_exists( 'category_ids', $changes ) ) {
            $categories = $person->get_category_ids( 'edit' );

            if ( empty( $categories ) && get_option( 'default_person_cat', 0 ) ) {
                $categories = array( get_option( 'default_person_cat', 0 ) );
            }

            wp_set_post_terms( $person->get_id(), $categories, 'person_cat', false );
        }
        if ( $force || array_key_exists( 'tag_ids', $changes ) ) {
            wp_set_post_terms( $person->get_id(), $person->get_tag_ids( 'edit' ), 'person_tag', false );
        }
    }

    /**
     * Update visibility terms based on props.
     *
     * @since 1.0.0
     *
     * @param MasVideos_Person $person Person object.
     * @param bool       $force Force update. Used during create.
     */
    protected function update_visibility( &$person, $force = false ) {
        $changes = $person->get_changes();

        if ( $force || array_intersect( array( 'featured', 'catalog_visibility' ), array_keys( $changes ) ) ) {
            $terms = array();

            if ( $person->get_featured() ) {
                $terms[] = 'featured';
            }

            switch ( $person->get_catalog_visibility() ) {
                case 'hidden':
                    $terms[] = 'exclude-from-search';
                    $terms[] = 'exclude-from-catalog';
                    break;
                case 'catalog':
                    $terms[] = 'exclude-from-search';
                    break;
                case 'search':
                    $terms[] = 'exclude-from-catalog';
                    break;
            }

            if ( ! is_wp_error( wp_set_post_terms( $person->get_id(), $terms, 'person_visibility', false ) ) ) {
                delete_transient( 'masvideos_featured_persons' );
                do_action( 'masvideos_person_set_visibility', $person->get_id(), $person->get_catalog_visibility() );
            }
        }
    }

    /**
     * Update attributes which are a mix of terms and meta data.
     *
     * @param MasVideos_Person $person Person object.
     * @param bool       $force Force update. Used during create.
     * @since 1.0.0
     */
    protected function update_attributes( &$person, $force = false ) {
        $changes = $person->get_changes();

        if ( $force || array_key_exists( 'attributes', $changes ) ) {
            $attributes  = $person->get_attributes();
            $meta_values = array();

            if ( $attributes ) {
                foreach ( $attributes as $attribute_key => $attribute ) {
                    $value = '';

                    delete_transient( 'masvideos_layered_nav_counts_' . $attribute_key );

                    if ( is_null( $attribute ) ) {
                        if ( taxonomy_exists( $attribute_key ) ) {
                            // Handle attributes that have been unset.
                            wp_set_object_terms( $person->get_id(), array(), $attribute_key );
                        }
                        continue;

                    } elseif ( $attribute->is_taxonomy() ) {
                        wp_set_object_terms( $person->get_id(), wp_list_pluck( $attribute->get_terms(), 'term_id' ), $attribute->get_name() );
                    } else {
                        $value = masvideos_implode_text_attributes( $attribute->get_options() );
                    }

                    // Store in format MasVideos uses in meta.
                    $meta_values[ $attribute_key ] = array(
                        'name'         => $attribute->get_name(),
                        'value'        => $value,
                        'position'     => $attribute->get_position(),
                        'is_visible'   => $attribute->get_visible() ? 1 : 0,
                        'is_taxonomy'  => $attribute->is_taxonomy() ? 1 : 0,
                    );
                }
            }
            update_post_meta( $person->get_id(), '_person_attributes', $meta_values );
        }
    }

    /**
     * Clear any caches.
     *
     * @param MasVideos_Person $person Person object.
     * @since 1.0.0
     */
    protected function clear_caches( &$person ) {
        masvideos_delete_person_transients( $person->get_id() );
        MasVideos_Cache_Helper::incr_cache_prefix( 'person_' . $person->get_id() );
    }

    /*
    |--------------------------------------------------------------------------
    | masvideos-person-functions.php methods
    |--------------------------------------------------------------------------
    */

    /**
     * Returns a list of person IDs ( id as key => parent as value) that are
     * featured. Uses get_posts instead of masvideos_get_persons since we want
     * some extra meta queries and ALL persons (posts_per_page = -1).
     *
     * @return array
     * @since 1.0.0
     */
    public function get_featured_person_ids() {
        $person_visibility_term_ids = masvideos_get_person_visibility_term_ids();

        return get_posts(
            array(
                'post_type'      => array( 'person' ),
                'posts_per_page' => -1,
                'post_status'    => 'publish',
                // phpcs:ignore WordPress.VIP.SlowDBQuery.slow_db_query_tax_query
                'tax_query'      => array(
                    'relation' => 'AND',
                    array(
                        'taxonomy' => 'person_visibility',
                        'field'    => 'term_taxonomy_id',
                        'terms'    => array( $person_visibility_term_ids['featured'] ),
                    ),
                    array(
                        'taxonomy' => 'person_visibility',
                        'field'    => 'term_taxonomy_id',
                        'terms'    => array( $person_visibility_term_ids['exclude-from-catalog'] ),
                        'operator' => 'NOT IN',
                    ),
                ),
                'fields'         => 'id=>parent',
            )
        );
    }

    /**
     * Check if person imdb_id is found for any other person IDs.
     *
     * @since 3.0.0
     * @param int    $person_id Person ID.
     * @param string $imdb_id Will be slashed to work around https://core.trac.wordpress.org/ticket/27421.
     * @return bool
     */
    public function is_existing_imdb_id( $person_id, $imdb_id ) {
        global $wpdb;

        // phpcs:ignore WordPress.VIP.DirectDatabaseQuery.DirectQuery
        return $wpdb->get_var(
            $wpdb->prepare(
                "
                SELECT posts.ID
                FROM {$wpdb->posts} as posts
                INNER JOIN {$wpdb->postmeta} AS pmeta ON posts.ID = pmeta.post_id
                WHERE
                posts.post_type IN ( 'person' )
                AND posts.post_status != 'trash'
                AND pmeta.meta_key = '_imdb_id'
                AND pmeta.meta_value = %s
                AND pmeta.post_id <> %d
                LIMIT 1
                ",
                wp_slash( $imdb_id ),
                $person_id
            )
        );
    }

    /**
     * Return person ID based on IMDB Id.
     *
     * @since 3.0.0
     * @param string $imdb_id Person IMDB Id.
     * @return int
     */
    public function get_person_id_by_imdb_id( $imdb_id ) {
        global $wpdb;

        // phpcs:ignore WordPress.VIP.DirectDatabaseQuery.DirectQuery
        $id = $wpdb->get_var(
            $wpdb->prepare(
                "
                SELECT posts.ID
                FROM {$wpdb->posts} as posts
                INNER JOIN {$wpdb->postmeta} AS pmeta ON posts.ID = pmeta.post_id
                WHERE
                posts.post_type IN ( 'person' )
                AND posts.post_status != 'trash'
                AND pmeta.meta_key = '_imdb_id'
                AND pmeta.meta_value = %s
                LIMIT 1
                ",
                $imdb_id
            )
        );

        return (int) apply_filters( 'masvideos_get_person_id_by_imdb_id', $id, $imdb_id );
    }

    /**
     * Check if person tmdb_id is found for any other person IDs.
     *
     * @since 3.0.0
     * @param int    $person_id Person ID.
     * @param string $tmdb_id Will be slashed to work around https://core.trac.wordpress.org/ticket/27421.
     * @return bool
     */
    public function is_existing_tmdb_id( $person_id, $tmdb_id ) {
        global $wpdb;

        // phpcs:ignore WordPress.VIP.DirectDatabaseQuery.DirectQuery
        return $wpdb->get_var(
            $wpdb->prepare(
                "
                SELECT posts.ID
                FROM {$wpdb->posts} as posts
                INNER JOIN {$wpdb->postmeta} AS pmeta ON posts.ID = pmeta.post_id
                WHERE
                posts.post_type IN ( 'person' )
                AND posts.post_status != 'trash'
                AND pmeta.meta_key = '_tmdb_id'
                AND pmeta.meta_value = %s
                AND pmeta.post_id <> %d
                LIMIT 1
                ",
                wp_slash( $tmdb_id ),
                $person_id
            )
        );
    }

    /**
     * Return person ID based on TMDB Id.
     *
     * @since 3.0.0
     * @param string $tmdb_id Person TMDB Id.
     * @return int
     */
    public function get_person_id_by_tmdb_id( $tmdb_id ) {
        global $wpdb;

        // phpcs:ignore WordPress.VIP.DirectDatabaseQuery.DirectQuery
        $id = $wpdb->get_var(
            $wpdb->prepare(
                "
                SELECT posts.ID
                FROM {$wpdb->posts} as posts
                INNER JOIN {$wpdb->postmeta} AS pmeta ON posts.ID = pmeta.post_id
                WHERE
                posts.post_type IN ( 'person' )
                AND posts.post_status != 'trash'
                AND pmeta.meta_key = '_tmdb_id'
                AND pmeta.meta_value = %s
                LIMIT 1
                ",
                $tmdb_id
            )
        );

        return (int) apply_filters( 'masvideos_get_person_id_by_tmdb_id', $id, $tmdb_id );
    }

    /**
     * Return a list of related persons (using data like categories and IDs).
     *
     * @since 1.0.0
     * @param array $cats_array  List of categories IDs.
     * @param array $tags_array  List of tags IDs.
     * @param array $exclude_ids Excluded IDs.
     * @param int   $limit       Limit of results.
     * @param int   $person_id  Person ID.
     * @return array
     */
    public function get_related_persons( $cats_array, $tags_array, $exclude_ids, $limit, $person_id ) {
        global $wpdb;

        $args = array(
            'categories'  => $cats_array,
            'tags'        => $tags_array,
            'exclude_ids' => $exclude_ids,
            'limit'       => $limit + 10,
        );

        $related_person_query = (array) apply_filters( 'masvideos_person_related_posts_query', $this->get_related_persons_query( $cats_array, $tags_array, $exclude_ids, $limit + 10 ), $person_id, $args );

        // phpcs:ignore WordPress.VIP.DirectDatabaseQuery.DirectQuery, WordPress.WP.PreparedSQL.NotPrepared
        return $wpdb->get_col( implode( ' ', $related_person_query ) );
    }

    /**
     * Builds the related posts query.
     *
     * @since 1.0.0
     *
     * @param array $cats_array  List of categories IDs.
     * @param array $tags_array  List of tags IDs.
     * @param array $exclude_ids Excluded IDs.
     * @param int   $limit       Limit of results.
     *
     * @return array
     */
    public function get_related_persons_query( $cats_array, $tags_array, $exclude_ids, $limit ) {
        global $wpdb;

        $include_term_ids            = array_merge( $cats_array, $tags_array );
        $exclude_term_ids            = array();
        $person_visibility_term_ids   = masvideos_get_person_visibility_term_ids();

        if ( $person_visibility_term_ids['exclude-from-catalog'] ) {
            $exclude_term_ids[] = $person_visibility_term_ids['exclude-from-catalog'];
        }

        $query = array(
            'fields' => "
                SELECT DISTINCT ID FROM {$wpdb->posts} p
            ",
            'join'   => '',
            'where'  => "
                WHERE 1=1
                AND p.post_status = 'publish'
                AND p.post_type = 'person'

            ",
            'limits' => '
                LIMIT ' . absint( $limit ) . '
            ',
        );

        if ( count( $exclude_term_ids ) ) {
            $query['join']  .= " LEFT JOIN ( SELECT object_id FROM {$wpdb->term_relationships} WHERE term_taxonomy_id IN ( " . implode( ',', array_map( 'absint', $exclude_term_ids ) ) . ' ) ) AS exclude_join ON exclude_join.object_id = p.ID';
            $query['where'] .= ' AND exclude_join.object_id IS NULL';
        }

        if ( count( $include_term_ids ) ) {
            $query['join'] .= " INNER JOIN ( SELECT object_id FROM {$wpdb->term_relationships} INNER JOIN {$wpdb->term_taxonomy} using( term_taxonomy_id ) WHERE term_id IN ( " . implode( ',', array_map( 'absint', $include_term_ids ) ) . ' ) ) AS include_join ON include_join.object_id = p.ID';
        }

        if ( count( $exclude_ids ) ) {
            $query['where'] .= ' AND p.ID NOT IN ( ' . implode( ',', array_map( 'absint', $exclude_ids ) ) . ' )';
        }

        return $query;
    }

    /**
     * Returns an array of persons.
     *
     * @param  array $args Args to pass to MasVideos_Person_Query().
     * @return array|object
     * @see masvideos_get_persons
     */
    public function get_persons( $args = array() ) {
        $query = new MasVideos_Person_Query( $args );
        return $query->get_persons();
    }

    /**
     * Search person data for a term and return ids.
     *
     * @param  string   $term Search term.
     * @param  bool     $all_statuses Should we search all statuses or limit to published.
     * @param  null|int $limit Limit returned results.
     * @since  1.0.0.
     * @return array of ids
     */
    public function search_persons( $term, $all_statuses = false, $limit = null ) {
        global $wpdb;

        $post_types    = array( 'person' );
        $post_statuses = current_user_can( 'edit_private_persons' ) ? array( 'private', 'publish' ) : array( 'publish' );
        $type_join     = '';
        $type_where    = '';
        $status_where  = '';
        $limit_query   = '';
        $term          = masvideos_strtolower( $term );

        // See if search term contains OR keywords.
        if ( strstr( $term, ' or ' ) ) {
            $term_groups = explode( ' or ', $term );
        } else {
            $term_groups = array( $term );
        }

        $search_where   = '';
        $search_queries = array();

        foreach ( $term_groups as $term_group ) {
            // Parse search terms.
            if ( preg_match_all( '/".*?("|$)|((?<=[\t ",+])|^)[^\t ",+]+/', $term_group, $matches ) ) {
                $search_terms = $this->get_valid_search_terms( $matches[0] );
                $count        = count( $search_terms );

                // if the search string has only short terms or stopwords, or is 10+ terms long, match it as sentence.
                if ( 9 < $count || 0 === $count ) {
                    $search_terms = array( $term_group );
                }
            } else {
                $search_terms = array( $term_group );
            }

            $term_group_query = '';
            $searchand        = '';

            foreach ( $search_terms as $search_term ) {
                $like              = '%' . $wpdb->esc_like( $search_term ) . '%';
                $term_group_query .= $wpdb->prepare( " {$searchand} ( ( posts.post_title LIKE %s) OR ( posts.post_excerpt LIKE %s) OR ( posts.post_content LIKE %s ) )", $like, $like, $like ); // @codingStandardsIgnoreLine.
                $searchand         = ' AND ';
            }

            if ( $term_group_query ) {
                $search_queries[] = $term_group_query;
            }
        }

        if ( ! empty( $search_queries ) ) {
            $search_where = 'AND (' . implode( ') OR (', $search_queries ) . ')';
        }

        if ( ! $all_statuses ) {
            $status_where = " AND posts.post_status IN ('" . implode( "','", $post_statuses ) . "') ";
        }

        if ( $limit ) {
            $limit_query = $wpdb->prepare( ' LIMIT %d ', $limit );
        }

        // phpcs:ignore WordPress.VIP.DirectDatabaseQuery.DirectQuery
        $search_results = $wpdb->get_results(
            // phpcs:disable
            "SELECT DISTINCT posts.ID as person_id, posts.post_parent as parent_id FROM {$wpdb->posts} posts
            LEFT JOIN {$wpdb->postmeta} postmeta ON posts.ID = postmeta.post_id
            $type_join
            WHERE posts.post_type IN ('" . implode( "','", $post_types ) . "')
            $search_where
            $status_where
            $type_where
            ORDER BY posts.post_parent ASC, posts.post_title ASC
            $limit_query
            "
            // phpcs:enable
        );

        $person_ids = wp_parse_id_list( array_merge( wp_list_pluck( $search_results, 'person_id' ), wp_list_pluck( $search_results, 'parent_id' ) ) );

        if ( is_numeric( $term ) ) {
            $post_id   = absint( $term );
            $post_type = get_post_type( $post_id );

            if ( 'person' === $post_type ) {
                $person_ids[] = $post_id;
            }

            $person_ids[] = wp_get_post_parent_id( $post_id );
        }

        return wp_parse_id_list( $person_ids );
    }

    /**
     * Get valid WP_Query args from a MasVideos_Person_Query's query variables.
     *
     * @since 3.2.0
     * @param array $query_vars Query vars from a MasVideos_Person_Query.
     * @return array
     */
    protected function get_wp_query_args( $query_vars ) {

        // Map query vars to ones that get_wp_query_args or WP_Query recognize.
        $key_mapping = array(
            'status'         => 'post_status',
            'page'           => 'paged',
            'include'        => 'post__in',
            'average_rating' => 'masvideos_average_rating',
            'review_count'   => 'masvideos_review_count',
        );
        foreach ( $key_mapping as $query_key => $db_key ) {
            if ( isset( $query_vars[ $query_key ] ) ) {
                $query_vars[ $db_key ] = $query_vars[ $query_key ];
                unset( $query_vars[ $query_key ] );
            }
        }

        // These queries cannot be auto-generated so we have to remove them and build them manually.
        $manual_queries = array(
            'featured'   => '',
            'visibility' => '',
        );
        foreach ( $manual_queries as $key => $manual_query ) {
            if ( isset( $query_vars[ $key ] ) ) {
                $manual_queries[ $key ] = $query_vars[ $key ];
                unset( $query_vars[ $key ] );
            }
        }

        $wp_query_args = parent::get_wp_query_args( $query_vars );

        if ( ! isset( $wp_query_args['date_query'] ) ) {
            $wp_query_args['date_query'] = array();
        }
        if ( ! isset( $wp_query_args['meta_query'] ) ) {
            // phpcs:ignore WordPress.VIP.SlowDBQuery.slow_db_query_meta_query
            $wp_query_args['meta_query'] = array();
        }

        // Handle person categories.
        if ( ! empty( $query_vars['genre'] ) ) {
            $wp_query_args['tax_query'][] = array(
                'taxonomy' => 'person_cat',
                'field'    => 'slug',
                'terms'    => $query_vars['genre'],
            );
        }

        // Handle person tags.
        if ( ! empty( $query_vars['tag'] ) ) {
            unset( $wp_query_args['tag'] );
            $wp_query_args['tax_query'][] = array(
                'taxonomy' => 'person_tag',
                'field'    => 'slug',
                'terms'    => $query_vars['tag'],
            );
        }

        // Handle featured.
        if ( '' !== $manual_queries['featured'] ) {
            $person_visibility_term_ids = masvideos_get_person_visibility_term_ids();
            if ( $manual_queries['featured'] ) {
                $wp_query_args['tax_query'][] = array(
                    'taxonomy' => 'person_visibility',
                    'field'    => 'term_taxonomy_id',
                    'terms'    => array( $person_visibility_term_ids['featured'] ),
                );
                $wp_query_args['tax_query'][] = array(
                    'taxonomy' => 'person_visibility',
                    'field'    => 'term_taxonomy_id',
                    'terms'    => array( $person_visibility_term_ids['exclude-from-catalog'] ),
                    'operator' => 'NOT IN',
                );
            } else {
                $wp_query_args['tax_query'][] = array(
                    'taxonomy' => 'person_visibility',
                    'field'    => 'term_taxonomy_id',
                    'terms'    => array( $person_visibility_term_ids['featured'] ),
                    'operator' => 'NOT IN',
                );
            }
        }

        // Handle visibility.
        if ( $manual_queries['visibility'] ) {
            switch ( $manual_queries['visibility'] ) {
                case 'search':
                    $wp_query_args['tax_query'][] = array(
                        'taxonomy' => 'person_visibility',
                        'field'    => 'slug',
                        'terms'    => array( 'exclude-from-search' ),
                        'operator' => 'NOT IN',
                    );
                    break;
                case 'catalog':
                    $wp_query_args['tax_query'][] = array(
                        'taxonomy' => 'person_visibility',
                        'field'    => 'slug',
                        'terms'    => array( 'exclude-from-catalog' ),
                        'operator' => 'NOT IN',
                    );
                    break;
                case 'visible':
                    $wp_query_args['tax_query'][] = array(
                        'taxonomy' => 'person_visibility',
                        'field'    => 'slug',
                        'terms'    => array( 'exclude-from-catalog', 'exclude-from-search' ),
                        'operator' => 'NOT IN',
                    );
                    break;
                case 'hidden':
                    $wp_query_args['tax_query'][] = array(
                        'taxonomy' => 'person_visibility',
                        'field'    => 'slug',
                        'terms'    => array( 'exclude-from-catalog', 'exclude-from-search' ),
                        'operator' => 'AND',
                    );
                    break;
            }
        }

        // Handle date queries.
        $date_queries = array(
            'date_created'          => 'post_date',
            'date_modified'         => 'post_modified',
            'birthday'              => '_birthday',
            'deathday'              => '_deathday',
        );
        foreach ( $date_queries as $query_var_key => $db_key ) {
            if ( isset( $query_vars[ $query_var_key ] ) && '' !== $query_vars[ $query_var_key ] ) {

                // Remove any existing meta queries for the same keys to prevent conflicts.
                $existing_queries = wp_list_pluck( $wp_query_args['meta_query'], 'key', true );
                foreach ( $existing_queries as $query_index => $query_contents ) {
                    unset( $wp_query_args['meta_query'][ $query_index ] );
                }

                $wp_query_args = $this->parse_date_for_wp_query( $query_vars[ $query_var_key ], $db_key, $wp_query_args );
            }
        }

        // Handle paginate.
        if ( ! isset( $query_vars['paginate'] ) || ! $query_vars['paginate'] ) {
            $wp_query_args['no_found_rows'] = true;
        }

        // Handle reviews_allowed.
        if ( isset( $query_vars['reviews_allowed'] ) && is_bool( $query_vars['reviews_allowed'] ) ) {
            add_filter( 'posts_where', array( $this, 'reviews_allowed_query_where' ), 10, 2 );
        }

        return apply_filters( 'masvideos_person_data_store_cpt_get_persons_query', $wp_query_args, $query_vars, $this );
    }

    /**
     * Query for Persons matching specific criteria.
     *
     * @since 1.0.0
     *
     * @param array $query_vars Query vars from a MasVideos_Person_Query.
     *
     * @return array|object
     */
    public function query( $query_vars ) {
        $args = $this->get_wp_query_args( $query_vars );

        if ( ! empty( $args['errors'] ) ) {
            $query = (object) array(
                'posts'         => array(),
                'found_posts'   => 0,
                'max_num_pages' => 0,
            );
        } else {
            $query = new WP_Query( $args );
        }

        if ( isset( $query_vars['return'] ) && 'objects' === $query_vars['return'] && ! empty( $query->posts ) ) {
            // Prime caches before grabbing objects.
            update_post_caches( $query->posts, array( 'person' ) );
        }

        $persons = ( isset( $query_vars['return'] ) && 'ids' === $query_vars['return'] ) ? $query->posts : array_filter( array_map( 'masvideos_get_person', $query->posts ) );

        if ( isset( $query_vars['paginate'] ) && $query_vars['paginate'] ) {
            return (object) array(
                'persons'      => $persons,
                'total'         => $query->found_posts,
                'max_num_pages' => $query->max_num_pages,
            );
        }

        return $persons;
    }
}
