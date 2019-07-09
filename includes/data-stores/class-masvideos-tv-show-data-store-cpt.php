<?php
/**
 * MasVideos_TV_Show_Data_Store_CPT class file.
 *
 * @package MasVideos/Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * MasVideos TV Show Data Store: Stored in CPT.
 *
 * @version  1.0.0
 */
class MasVideos_TV_Show_Data_Store_CPT extends MasVideos_Data_Store_WP implements MasVideos_Object_Data_Store_Interface, MasVideos_TV_Show_Data_Store_Interface {

    /**
     * Data stored in meta keys, but not considered "meta".
     *
     * @since 1.0.0
     * @var array
     */
    protected $internal_meta_keys = array(
        '_visibility',
        '_default_attributes',
        '_cast',
        '_crew',
        '_seasons',
        '_tv_show_attributes',
        '_featured',
        '_masvideos_rating_count',
        '_masvideos_average_rating',
        '_masvideos_review_count',
        '_thumbnail_id',
        '_file_paths',
        '_tv_show_image_gallery',
        '_tv_show_version',
        '_wp_old_slug',
        '_edit_last',
        '_edit_lock',
        '_imdb_id',
        '_tmdb_id',
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
     * Method to create a new tv show in the database.
     *
     * @param MasVideos_TV_Show $tv_show TV Show object.
     */
    public function create( &$tv_show ) {
        if ( ! $tv_show->get_date_created( 'edit' ) ) {
            $tv_show->set_date_created( current_time( 'timestamp', true ) );
        }

        $id = wp_insert_post(
            apply_filters(
                'masvideos_new_tv_show_data', array(
                    'post_type'      => 'tv_show',
                    'post_status'    => $tv_show->get_status() ? $tv_show->get_status() : 'publish',
                    'post_author'    => get_current_user_id(),
                    'post_title'     => $tv_show->get_name() ? $tv_show->get_name() : __( 'TV Show', 'masvideos' ),
                    'post_content'   => $tv_show->get_description(),
                    'post_excerpt'   => $tv_show->get_short_description(),
                    'post_parent'    => $tv_show->get_parent_id(),
                    'comment_status' => $tv_show->get_reviews_allowed() ? 'open' : 'closed',
                    'ping_status'    => 'closed',
                    'menu_order'     => $tv_show->get_menu_order(),
                    'post_date'      => gmdate( 'Y-m-d H:i:s', $tv_show->get_date_created( 'edit' )->getOffsetTimestamp() ),
                    'post_date_gmt'  => gmdate( 'Y-m-d H:i:s', $tv_show->get_date_created( 'edit' )->getTimestamp() ),
                    'post_name'      => $tv_show->get_slug( 'edit' ),
                )
            ), true
        );

        if ( $id && ! is_wp_error( $id ) ) {
            $tv_show->set_id( $id );

            $this->update_post_meta( $tv_show, true );
            $this->update_terms( $tv_show, true );
            $this->update_visibility( $tv_show, true );
            $this->update_attributes( $tv_show, true );
            $this->handle_updated_props( $tv_show );

            $tv_show->save_meta_data();
            $tv_show->apply_changes();

            $this->clear_caches( $tv_show );

            do_action( 'masvideos_new_tv_show', $id );
        }
    }

    /**
     * Method to read a tv show from the database.
     *
     * @param MasVideos_TV_Show $tv_show TV Show object.
     * @throws Exception If invalid tv show.
     */
    public function read( &$tv_show ) {
        $tv_show->set_defaults();
        $post_object = get_post( $tv_show->get_id() );

        if ( ! $tv_show->get_id() || ! $post_object || 'tv_show' !== $post_object->post_type ) {
            throw new Exception( __( 'Invalid tv show.', 'masvideos' ) );
        }

        $tv_show->set_props(
            array(
                'name'              => $post_object->post_title,
                'slug'              => $post_object->post_name,
                'date_created'      => 0 < $post_object->post_date_gmt ? masvideos_string_to_timestamp( $post_object->post_date_gmt ) : null,
                'date_modified'     => 0 < $post_object->post_modified_gmt ? masvideos_string_to_timestamp( $post_object->post_modified_gmt ) : null,
                'status'            => $post_object->post_status,
                'description'       => $post_object->post_content,
                'short_description' => $post_object->post_excerpt,
                'parent_id'         => $post_object->post_parent,
                'menu_order'        => $post_object->menu_order,
                'reviews_allowed'   => 'open' === $post_object->comment_status,
            )
        );

        $this->read_attributes( $tv_show );
        $this->read_visibility( $tv_show );
        $this->read_tv_show_data( $tv_show );
        $this->read_extra_data( $tv_show );
        $tv_show->set_object_read( true );
    }

    /**
     * Method to update a tv show in the database.
     *
     * @param MasVideos_TV_Show $tv_show TV Show object.
     */
    public function update( &$tv_show ) {
        $tv_show->save_meta_data();
        $changes = $tv_show->get_changes();

        // Only update the post when the post data changes.
        if ( array_intersect( array( 'description', 'short_description', 'name', 'parent_id', 'reviews_allowed', 'status', 'menu_order', 'date_created', 'date_modified', 'slug' ), array_keys( $changes ) ) ) {
            $post_data = array(
                'post_content'   => $tv_show->get_description( 'edit' ),
                'post_excerpt'   => $tv_show->get_short_description( 'edit' ),
                'post_title'     => $tv_show->get_name( 'edit' ),
                'post_parent'    => $tv_show->get_parent_id( 'edit' ),
                'comment_status' => $tv_show->get_reviews_allowed( 'edit' ) ? 'open' : 'closed',
                'post_status'    => $tv_show->get_status( 'edit' ) ? $tv_show->get_status( 'edit' ) : 'publish',
                'menu_order'     => $tv_show->get_menu_order( 'edit' ),
                'post_name'      => $tv_show->get_slug( 'edit' ),
                'post_type'      => 'tv_show',
            );
            if ( $tv_show->get_date_created( 'edit' ) ) {
                $post_data['post_date']     = gmdate( 'Y-m-d H:i:s', $tv_show->get_date_created( 'edit' )->getOffsetTimestamp() );
                $post_data['post_date_gmt'] = gmdate( 'Y-m-d H:i:s', $tv_show->get_date_created( 'edit' )->getTimestamp() );
            }
            if ( isset( $changes['date_modified'] ) && $tv_show->get_date_modified( 'edit' ) ) {
                $post_data['post_modified']     = gmdate( 'Y-m-d H:i:s', $tv_show->get_date_modified( 'edit' )->getOffsetTimestamp() );
                $post_data['post_modified_gmt'] = gmdate( 'Y-m-d H:i:s', $tv_show->get_date_modified( 'edit' )->getTimestamp() );
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
                $GLOBALS['wpdb']->update( $GLOBALS['wpdb']->posts, $post_data, array( 'ID' => $tv_show->get_id() ) );
                clean_post_cache( $tv_show->get_id() );
            } else {
                wp_update_post( array_merge( array( 'ID' => $tv_show->get_id() ), $post_data ) );
            }
            $tv_show->read_meta_data( true ); // Refresh internal meta data, in case things were hooked into `save_post` or another WP hook.

        } else { // Only update post modified time to record this save event.
            $GLOBALS['wpdb']->update(
                $GLOBALS['wpdb']->posts,
                array(
                    'post_modified'     => current_time( 'mysql' ),
                    'post_modified_gmt' => current_time( 'mysql', 1 ),
                ),
                array(
                    'ID' => $tv_show->get_id(),
                )
            );
            clean_post_cache( $tv_show->get_id() );
        }

        $this->update_post_meta( $tv_show );
        $this->update_terms( $tv_show );
        $this->update_visibility( $tv_show );
        $this->update_attributes( $tv_show );
        $this->handle_updated_props( $tv_show );

        $tv_show->apply_changes();

        $this->clear_caches( $tv_show );

        do_action( 'masvideos_update_tv_show', $tv_show->get_id() );
    }

    /**
     * Method to delete a tv show from the database.
     *
     * @param MasVideos_TV_Show $tv_show TV Show object.
     * @param array      $args Array of args to pass to the delete method.
     */
    public function delete( &$tv_show, $args = array() ) {
        $id        = $tv_show->get_id();
        $post_type = 'tv_show';

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
            $tv_show->set_id( 0 );
            do_action( 'masvideos_delete_' . $post_type, $id );
        } else {
            wp_trash_post( $id );
            $tv_show->set_status( 'trash' );
            do_action( 'masvideos_trash_' . $post_type, $id );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Additional Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Read tv show data. Can be overridden by child classes to load other props.
     *
     * @param MasVideos_TV_Show $tv_show TV Show object.
     * @since 1.0.0
     */
    protected function read_tv_show_data( &$tv_show ) {
        $id             = $tv_show->get_id();
        $review_count   = get_post_meta( $id, '_masvideos_review_count', true );
        $rating_counts  = get_post_meta( $id, '_masvideos_rating_count', true );
        $average_rating = get_post_meta( $id, '_masvideos_average_rating', true );

        if ( '' === $review_count ) {
            MasVideos_Comments::get_review_count_for_tv_show( $tv_show );
        } else {
            $tv_show->set_review_count( $review_count );
        }

        if ( '' === $rating_counts ) {
            MasVideos_Comments::get_rating_counts_for_tv_show( $tv_show );
        } else {
            $tv_show->set_rating_counts( $rating_counts );
        }

        if ( '' === $average_rating ) {
            MasVideos_Comments::get_average_rating_for_tv_show( $tv_show );
        } else {
            $tv_show->set_average_rating( $average_rating );
        }

        $tv_show->set_props(
            array(
                'cast'                  => get_post_meta( $id, '_cast', true ),
                'crew'                  => get_post_meta( $id, '_crew', true ),
                'default_attributes'    => get_post_meta( $id, '_default_attributes', true ),
                'seasons'               => get_post_meta( $id, '_seasons', true ),
                'genre_ids'             => $this->get_term_ids( $tv_show, 'tv_show_genre' ),
                'tag_ids'               => $this->get_term_ids( $tv_show, 'tv_show_tag' ),
                'gallery_image_ids'     => array_filter( explode( ',', get_post_meta( $id, '_tv_show_image_gallery', true ) ) ),
                'image_id'              => get_post_thumbnail_id( $id ),
                'imdb_id'               => get_post_meta( $id, '_imdb_id', true ),
                'tmdb_id'               => get_post_meta( $id, '_tmdb_id', true ),
            )
        );
    }

    /**
     * Read extra data associated with the tv show, like button text or tv show URL for external tv shows.
     *
     * @param MasVideos_TV_Show $tv_show TV Show object.
     * @since 1.0.0
     */
    protected function read_extra_data( &$tv_show ) {
        foreach ( $tv_show->get_extra_data_keys() as $key ) {
            $function = 'set_' . $key;
            if ( is_callable( array( $tv_show, $function ) ) ) {
                $tv_show->{$function}( get_post_meta( $tv_show->get_id(), '_' . $key, true ) );
            }
        }
    }

    /**
     * Convert visibility terms to props.
     * Catalog visibility valid values are 'visible', 'catalog', 'search', and 'hidden'.
     *
     * @param MasVideos_TV_Show $tv_show TV Show object.
     * @since 1.0.0
     */
    protected function read_visibility( &$tv_show ) {
        $terms           = get_the_terms( $tv_show->get_id(), 'tv_show_visibility' );
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

        $tv_show->set_props(
            array(
                'featured'           => $featured,
                'catalog_visibility' => $catalog_visibility,
            )
        );
    }

    /**
     * Read attributes from post meta.
     *
     * @param MasVideos_TV_Show $tv_show TV Show object.
     */
    protected function read_attributes( &$tv_show ) {
        $meta_attributes = get_post_meta( $tv_show->get_id(), '_tv_show_attributes', true );

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
                    $id      = masvideos_attribute_taxonomy_id_by_name( 'tv_show', $meta_value['name'] );
                    $options = masvideos_get_object_terms( $tv_show->get_id(), $meta_value['name'], 'term_id' );
                } else {
                    $id      = 0;
                    $options = masvideos_get_text_attributes( $meta_value['value'] );
                }

                $attribute = new MasVideos_TV_Show_Attribute();
                $attribute->set_id( $id );
                $attribute->set_name( $meta_value['name'] );
                $attribute->set_options( $options );
                $attribute->set_position( $meta_value['position'] );
                $attribute->set_visible( $meta_value['is_visible'] );
                $attributes[] = $attribute;
            }
            $tv_show->set_attributes( $attributes );
        }
    }

    /**
     * Helper method that updates all the post meta for a tv show based on it's settings in the MasVideos_TV_Show class.
     *
     * @param MasVideos_TV_Show $tv_show TV Show object.
     * @param bool       $force Force update. Used during create.
     * @since 1.0.0
     */
    protected function update_post_meta( &$tv_show, $force = false ) {
        $meta_key_to_props = array(
            '_cast'                         => 'cast',
            '_crew'                         => 'crew',
            '_default_attributes'           => 'default_attributes',
            '_seasons'                      => 'seasons',
            '_tv_show_image_gallery'        => 'gallery_image_ids',
            '_thumbnail_id'                 => 'image_id',
            '_masvideos_average_rating'     => 'average_rating',
            '_masvideos_rating_count'       => 'rating_counts',
            '_masvideos_review_count'       => 'review_count',
            '_imdb_id'                      => 'imdb_id',
            '_tmdb_id'                      => 'tmdb_id',
        );

        // Make sure to take extra data (like tv show url or text for external tv shows) into account.
        $extra_data_keys = $tv_show->get_extra_data_keys();

        foreach ( $extra_data_keys as $key ) {
            $meta_key_to_props[ '_' . $key ] = $key;
        }

        $props_to_update = $force ? $meta_key_to_props : $this->get_props_to_update( $tv_show, $meta_key_to_props );

        foreach ( $props_to_update as $meta_key => $prop ) {
            $value = $tv_show->{"get_$prop"}( 'edit' );
            $value = is_string( $value ) ? wp_slash( $value ) : $value;
            switch ( $prop ) {
                case 'gallery_image_ids':
                    $updated = update_post_meta( $tv_show->get_id(), $meta_key, implode( ',', $value ) );
                    break;
                case 'image_id':
                    if ( ! empty( $value ) ) {
                        set_post_thumbnail( $tv_show->get_id(), $value );
                    } else {
                        delete_post_meta( $tv_show->get_id(), '_thumbnail_id' );
                    }
                    $updated = true;
                    break;
                default:
                    $updated = update_post_meta( $tv_show->get_id(), $meta_key, $value );
                    break;
            }
            if ( $updated ) {
                $this->updated_props[] = $prop;
            }
        }

        // Update extra data associated with the tv show like button text or tv show URL for external tv shows.
        if ( ! $this->extra_data_saved ) {
            foreach ( $extra_data_keys as $key ) {
                if ( ! array_key_exists( '_' . $key, $props_to_update ) ) {
                    continue;
                }
                $function = 'get_' . $key;
                if ( is_callable( array( $tv_show, $function ) ) ) {
                    $value = $tv_show->{$function}( 'edit' );
                    $value = is_string( $value ) ? wp_slash( $value ) : $value;

                    if ( update_post_meta( $tv_show->get_id(), '_' . $key, $value ) ) {
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
     * @param MasVideos_TV_Show $tv_show TV Show Object.
     */
    protected function handle_updated_props( &$tv_show ) {

        // Trigger action so 3rd parties can deal with updated props.
        do_action( 'masvideos_tv_show_object_updated_props', $tv_show, $this->updated_props );

        // After handling, we can reset the props array.
        $this->updated_props = array();
    }

    /**
     * For all stored terms in all taxonomies, save them to the DB.
     *
     * @param MasVideos_TV_Show $tv_show TV Show object.
     * @param bool       $force Force update. Used during create.
     * @since 1.0.0
     */
    protected function update_terms( &$tv_show, $force = false ) {
        $changes = $tv_show->get_changes();

        if ( $force || array_key_exists( 'genre_ids', $changes ) ) {
            $categories = $tv_show->get_genre_ids( 'edit' );

            if ( empty( $categories ) && get_option( 'default_tv_show_genre', 0 ) ) {
                $categories = array( get_option( 'default_tv_show_genre', 0 ) );
            }

            wp_set_post_terms( $tv_show->get_id(), $categories, 'tv_show_genre', false );
        }
        if ( $force || array_key_exists( 'tag_ids', $changes ) ) {
            wp_set_post_terms( $tv_show->get_id(), $tv_show->get_tag_ids( 'edit' ), 'tv_show_tag', false );
        }
    }

    /**
     * Update visibility terms based on props.
     *
     * @since 1.0.0
     *
     * @param MasVideos_TV_Show $tv_show TV Show object.
     * @param bool       $force Force update. Used during create.
     */
    protected function update_visibility( &$tv_show, $force = false ) {
        $changes = $tv_show->get_changes();

        if ( $force || array_intersect( array( 'featured', 'average_rating', 'catalog_visibility' ), array_keys( $changes ) ) ) {
            $terms = array();

            if ( $tv_show->get_featured() ) {
                $terms[] = 'featured';
            }

            $rating = min( 10, round( $tv_show->get_average_rating(), 0 ) );

            if ( $rating > 0 ) {
                $terms[] = 'rated-' . $rating;
            }

            switch ( $tv_show->get_catalog_visibility() ) {
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

            if ( ! is_wp_error( wp_set_post_terms( $tv_show->get_id(), $terms, 'tv_show_visibility', false ) ) ) {
                delete_transient( 'masvideos_featured_tv_shows' );
                do_action( 'masvideos_tv_show_set_visibility', $tv_show->get_id(), $tv_show->get_catalog_visibility() );
            }
        }
    }

    /**
     * Update attributes which are a mix of terms and meta data.
     *
     * @param MasVideos_TV_Show $tv_show TV Show object.
     * @param bool       $force Force update. Used during create.
     * @since 1.0.0
     */
    protected function update_attributes( &$tv_show, $force = false ) {
        $changes = $tv_show->get_changes();

        if ( $force || array_key_exists( 'attributes', $changes ) ) {
            $attributes  = $tv_show->get_attributes();
            $meta_values = array();

            if ( $attributes ) {
                foreach ( $attributes as $attribute_key => $attribute ) {
                    $value = '';

                    delete_transient( 'masvideos_layered_nav_counts_' . $attribute_key );

                    if ( is_null( $attribute ) ) {
                        if ( taxonomy_exists( $attribute_key ) ) {
                            // Handle attributes that have been unset.
                            wp_set_object_terms( $tv_show->get_id(), array(), $attribute_key );
                        }
                        continue;

                    } elseif ( $attribute->is_taxonomy() ) {
                        wp_set_object_terms( $tv_show->get_id(), wp_list_pluck( $attribute->get_terms(), 'term_id' ), $attribute->get_name() );
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
            update_post_meta( $tv_show->get_id(), '_tv_show_attributes', $meta_values );
        }
    }

    /**
     * Clear any caches.
     *
     * @param MasVideos_TV_Show $tv_show TV Show object.
     * @since 1.0.0
     */
    protected function clear_caches( &$tv_show ) {
        masvideos_delete_tv_show_transients( $tv_show->get_id() );
        MasVideos_Cache_Helper::incr_cache_prefix( 'tv_show_' . $tv_show->get_id() );
    }

    /*
    |--------------------------------------------------------------------------
    | masvideos-tv_show-functions.php methods
    |--------------------------------------------------------------------------
    */

    /**
     * Returns a list of tv show IDs ( id as key => parent as value) that are
     * featured. Uses get_posts instead of masvideos_get_tv_shows since we want
     * some extra meta queries and ALL tv shows (posts_per_page = -1).
     *
     * @return array
     * @since 1.0.0
     */
    public function get_featured_tv_show_ids() {
        $tv_show_visibility_term_ids = masvideos_get_tv_show_visibility_term_ids();

        return get_posts(
            array(
                'post_type'      => array( 'tv_show' ),
                'posts_per_page' => -1,
                'post_status'    => 'publish',
                // phpcs:ignore WordPress.VIP.SlowDBQuery.slow_db_query_tax_query
                'tax_query'      => array(
                    'relation' => 'AND',
                    array(
                        'taxonomy' => 'tv_show_visibility',
                        'field'    => 'term_taxonomy_id',
                        'terms'    => array( $tv_show_visibility_term_ids['featured'] ),
                    ),
                    array(
                        'taxonomy' => 'tv_show_visibility',
                        'field'    => 'term_taxonomy_id',
                        'terms'    => array( $tv_show_visibility_term_ids['exclude-from-catalog'] ),
                        'operator' => 'NOT IN',
                    ),
                ),
                'fields'         => 'id=>parent',
            )
        );
    }

    /**
     * Check if tv show imdb_id is found for any other tv show IDs.
     *
     * @since 3.0.0
     * @param int    $tv_show_id TV Show ID.
     * @param string $imdb_id Will be slashed to work around https://core.trac.wordpress.org/ticket/27421.
     * @return bool
     */
    public function is_existing_imdb_id( $tv_show_id, $imdb_id ) {
        global $wpdb;

        // phpcs:ignore WordPress.VIP.DirectDatabaseQuery.DirectQuery
        return $wpdb->get_var(
            $wpdb->prepare(
                "
                SELECT posts.ID
                FROM {$wpdb->posts} as posts
                INNER JOIN {$wpdb->postmeta} AS pmeta ON posts.ID = pmeta.post_id
                WHERE
                posts.post_type IN ( 'tv_show' )
                AND posts.post_status != 'trash'
                AND pmeta.meta_key = '_imdb_id'
                AND pmeta.meta_value = %s
                AND pmeta.post_id <> %d
                LIMIT 1
                ",
                wp_slash( $imdb_id ),
                $tv_show_id
            )
        );
    }

    /**
     * Return tv show ID based on IMDB Id.
     *
     * @since 3.0.0
     * @param string $imdb_id TV Show IMDB Id.
     * @return int
     */
    public function get_tv_show_id_by_imdb_id( $imdb_id ) {
        global $wpdb;

        // phpcs:ignore WordPress.VIP.DirectDatabaseQuery.DirectQuery
        $id = $wpdb->get_var(
            $wpdb->prepare(
                "
                SELECT posts.ID
                FROM {$wpdb->posts} as posts
                INNER JOIN {$wpdb->postmeta} AS pmeta ON posts.ID = pmeta.post_id
                WHERE
                posts.post_type IN ( 'tv_show' )
                AND posts.post_status != 'trash'
                AND pmeta.meta_key = '_imdb_id'
                AND pmeta.meta_value = %s
                LIMIT 1
                ",
                $imdb_id
            )
        );

        return (int) apply_filters( 'masvideos_get_tv_show_id_by_imdb_id', $id, $imdb_id );
    }

    /**
     * Check if tv show tmdb_id is found for any other tv show IDs.
     *
     * @since 3.0.0
     * @param int    $tv_show_id TV Show ID.
     * @param string $tmdb_id Will be slashed to work around https://core.trac.wordpress.org/ticket/27421.
     * @return bool
     */
    public function is_existing_tmdb_id( $tv_show_id, $tmdb_id ) {
        global $wpdb;

        // phpcs:ignore WordPress.VIP.DirectDatabaseQuery.DirectQuery
        return $wpdb->get_var(
            $wpdb->prepare(
                "
                SELECT posts.ID
                FROM {$wpdb->posts} as posts
                INNER JOIN {$wpdb->postmeta} AS pmeta ON posts.ID = pmeta.post_id
                WHERE
                posts.post_type IN ( 'tv_show' )
                AND posts.post_status != 'trash'
                AND pmeta.meta_key = '_tmdb_id'
                AND pmeta.meta_value = %s
                AND pmeta.post_id <> %d
                LIMIT 1
                ",
                wp_slash( $tmdb_id ),
                $tv_show_id
            )
        );
    }

    /**
     * Return tv show ID based on TMDB Id.
     *
     * @since 3.0.0
     * @param string $tmdb_id TV Show TMDB Id.
     * @return int
     */
    public function get_tv_show_id_by_tmdb_id( $tmdb_id ) {
        global $wpdb;

        // phpcs:ignore WordPress.VIP.DirectDatabaseQuery.DirectQuery
        $id = $wpdb->get_var(
            $wpdb->prepare(
                "
                SELECT posts.ID
                FROM {$wpdb->posts} as posts
                INNER JOIN {$wpdb->postmeta} AS pmeta ON posts.ID = pmeta.post_id
                WHERE
                posts.post_type IN ( 'tv_show' )
                AND posts.post_status != 'trash'
                AND pmeta.meta_key = '_tmdb_id'
                AND pmeta.meta_value = %s
                LIMIT 1
                ",
                $tmdb_id
            )
        );

        return (int) apply_filters( 'masvideos_get_tv_show_id_by_tmdb_id', $id, $tmdb_id );
    }

    /**
     * Return a list of related tv shows (using data like categories and IDs).
     *
     * @since 1.0.0
     * @param array $cats_array  List of categories IDs.
     * @param array $tags_array  List of tags IDs.
     * @param array $exclude_ids Excluded IDs.
     * @param int   $limit       Limit of results.
     * @param int   $tv_show_id  TV Show ID.
     * @return array
     */
    public function get_related_tv_shows( $cats_array, $tags_array, $exclude_ids, $limit, $tv_show_id ) {
        global $wpdb;

        $args = array(
            'categories'  => $cats_array,
            'tags'        => $tags_array,
            'exclude_ids' => $exclude_ids,
            'limit'       => $limit + 10,
        );

        $related_tv_show_query = (array) apply_filters( 'masvideos_tv_show_related_posts_query', $this->get_related_tv_shows_query( $cats_array, $tags_array, $exclude_ids, $limit + 10 ), $tv_show_id, $args );

        // phpcs:ignore WordPress.VIP.DirectDatabaseQuery.DirectQuery, WordPress.WP.PreparedSQL.NotPrepared
        return $wpdb->get_col( implode( ' ', $related_tv_show_query ) );
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
    public function get_related_tv_shows_query( $cats_array, $tags_array, $exclude_ids, $limit ) {
        global $wpdb;

        $include_term_ids            = array_merge( $cats_array, $tags_array );
        $exclude_term_ids            = array();
        $tv_show_visibility_term_ids   = masvideos_get_tv_show_visibility_term_ids();

        if ( $tv_show_visibility_term_ids['exclude-from-catalog'] ) {
            $exclude_term_ids[] = $tv_show_visibility_term_ids['exclude-from-catalog'];
        }

        $query = array(
            'fields' => "
                SELECT DISTINCT ID FROM {$wpdb->posts} p
            ",
            'join'   => '',
            'where'  => "
                WHERE 1=1
                AND p.post_status = 'publish'
                AND p.post_type = 'tv_show'

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
     * Update a tv shows average rating meta.
     *
     * @since 1.0.0
     * @param MasVideos_TV_Show $tv_show TV Show object.
     */
    public function update_average_rating( $tv_show ) {
        update_post_meta( $tv_show->get_id(), '_masvideos_average_rating', $tv_show->get_average_rating( 'edit' ) );
        self::update_visibility( $tv_show, true );
    }

    /**
     * Update a tv shows review count meta.
     *
     * @since 1.0.0
     * @param MasVideos_TV_Show $tv_show TV Show object.
     */
    public function update_review_count( $tv_show ) {
        update_post_meta( $tv_show->get_id(), '_masvideos_review_count', $tv_show->get_review_count( 'edit' ) );
    }

    /**
     * Update a tv shows rating counts.
     *
     * @since 1.0.0
     * @param MasVideos_TV_Show $tv_show TV Show object.
     */
    public function update_rating_counts( $tv_show ) {
        update_post_meta( $tv_show->get_id(), '_masvideos_rating_count', $tv_show->get_rating_counts( 'edit' ) );
    }

    /**
     * Returns an array of tv shows.
     *
     * @param  array $args Args to pass to MasVideos_TV_Show_Query().
     * @return array|object
     * @see masvideos_get_tv_shows
     */
    public function get_tv_shows( $args = array() ) {
        $query = new MasVideos_TV_Show_Query( $args );
        return $query->get_tv_shows();
    }

    /**
     * Search tv show data for a term and return ids.
     *
     * @param  string   $term Search term.
     * @param  bool     $all_statuses Should we search all statuses or limit to published.
     * @param  null|int $limit Limit returned results.
     * @since  1.0.0.
     * @return array of ids
     */
    public function search_tv_shows( $term, $all_statuses = false, $limit = null ) {
        global $wpdb;

        $post_types    = array( 'tv_show' );
        $post_statuses = current_user_can( 'edit_private_tv_shows' ) ? array( 'private', 'publish' ) : array( 'publish' );
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
            "SELECT DISTINCT posts.ID as tv show_id, posts.post_parent as parent_id FROM {$wpdb->posts} posts
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

        $tv_show_ids = wp_parse_id_list( array_merge( wp_list_pluck( $search_results, 'tv_show_id' ), wp_list_pluck( $search_results, 'parent_id' ) ) );

        if ( is_numeric( $term ) ) {
            $post_id   = absint( $term );
            $post_type = get_post_type( $post_id );

            if ( 'tv_show' === $post_type ) {
                $tv_show_ids[] = $post_id;
            }

            $tv_show_ids[] = wp_get_post_parent_id( $post_id );
        }

        return wp_parse_id_list( $tv_show_ids );
    }

    /**
     * Add ability to get tv shows by 'reviews_allowed' in MasVideos_TV_Show_Query.
     *
     * @since 3.2.0
     * @param string   $where Where clause.
     * @param WP_Query $wp_query WP_Query instance.
     * @return string
     */
    public function reviews_allowed_query_where( $where, $wp_query ) {
        global $wpdb;

        if ( isset( $wp_query->query_vars['reviews_allowed'] ) && is_bool( $wp_query->query_vars['reviews_allowed'] ) ) {
            if ( $wp_query->query_vars['reviews_allowed'] ) {
                $where .= " AND $wpdb->posts.comment_status = 'open'";
            } else {
                $where .= " AND $wpdb->posts.comment_status = 'closed'";
            }
        }

        return $where;
    }

    /**
     * Get valid WP_Query args from a MasVideos_TV_Show_Query's query variables.
     *
     * @since 3.2.0
     * @param array $query_vars Query vars from a MasVideos_TV_Show_Query.
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

        // Handle tv show categories.
        if ( ! empty( $query_vars['genre'] ) ) {
            $wp_query_args['tax_query'][] = array(
                'taxonomy' => 'tv_show_genre',
                'field'    => 'slug',
                'terms'    => $query_vars['genre'],
            );
        }

        // Handle tv show tags.
        if ( ! empty( $query_vars['tag'] ) ) {
            unset( $wp_query_args['tag'] );
            $wp_query_args['tax_query'][] = array(
                'taxonomy' => 'tv_show_tag',
                'field'    => 'slug',
                'terms'    => $query_vars['tag'],
            );
        }

        // Handle featured.
        if ( '' !== $manual_queries['featured'] ) {
            $tv_show_visibility_term_ids = masvideos_get_tv_show_visibility_term_ids();
            if ( $manual_queries['featured'] ) {
                $wp_query_args['tax_query'][] = array(
                    'taxonomy' => 'tv_show_visibility',
                    'field'    => 'term_taxonomy_id',
                    'terms'    => array( $tv_show_visibility_term_ids['featured'] ),
                );
                $wp_query_args['tax_query'][] = array(
                    'taxonomy' => 'tv_show_visibility',
                    'field'    => 'term_taxonomy_id',
                    'terms'    => array( $tv_show_visibility_term_ids['exclude-from-catalog'] ),
                    'operator' => 'NOT IN',
                );
            } else {
                $wp_query_args['tax_query'][] = array(
                    'taxonomy' => 'tv_show_visibility',
                    'field'    => 'term_taxonomy_id',
                    'terms'    => array( $tv_show_visibility_term_ids['featured'] ),
                    'operator' => 'NOT IN',
                );
            }
        }

        // Handle visibility.
        if ( $manual_queries['visibility'] ) {
            switch ( $manual_queries['visibility'] ) {
                case 'search':
                    $wp_query_args['tax_query'][] = array(
                        'taxonomy' => 'tv_show_visibility',
                        'field'    => 'slug',
                        'terms'    => array( 'exclude-from-search' ),
                        'operator' => 'NOT IN',
                    );
                    break;
                case 'catalog':
                    $wp_query_args['tax_query'][] = array(
                        'taxonomy' => 'tv_show_visibility',
                        'field'    => 'slug',
                        'terms'    => array( 'exclude-from-catalog' ),
                        'operator' => 'NOT IN',
                    );
                    break;
                case 'visible':
                    $wp_query_args['tax_query'][] = array(
                        'taxonomy' => 'tv_show_visibility',
                        'field'    => 'slug',
                        'terms'    => array( 'exclude-from-catalog', 'exclude-from-search' ),
                        'operator' => 'NOT IN',
                    );
                    break;
                case 'hidden':
                    $wp_query_args['tax_query'][] = array(
                        'taxonomy' => 'tv_show_visibility',
                        'field'    => 'slug',
                        'terms'    => array( 'exclude-from-catalog', 'exclude-from-search' ),
                        'operator' => 'AND',
                    );
                    break;
            }
        }

        // Handle date queries.
        $date_queries = array(
            'date_created'      => 'post_date',
            'date_modified'     => 'post_modified',
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

        return apply_filters( 'masvideos_tv_show_data_store_cpt_get_tv_shows_query', $wp_query_args, $query_vars, $this );
    }

    /**
     * Query for TV Shows matching specific criteria.
     *
     * @since 1.0.0
     *
     * @param array $query_vars Query vars from a MasVideos_TV_Show_Query.
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
            update_post_caches( $query->posts, array( 'tv_show' ) );
        }

        $tv_shows = ( isset( $query_vars['return'] ) && 'ids' === $query_vars['return'] ) ? $query->posts : array_filter( array_map( 'masvideos_get_tv_show', $query->posts ) );

        if ( isset( $query_vars['paginate'] ) && $query_vars['paginate'] ) {
            return (object) array(
                'tv_shows'      => $tv_shows,
                'total'         => $query->found_posts,
                'max_num_pages' => $query->max_num_pages,
            );
        }

        return $tv_shows;
    }
}
