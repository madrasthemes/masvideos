<?php
/**
 * MasVideos_Video_Data_Store_CPT class file.
 *
 * @package MasVideos/Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * MasVideos Video Data Store: Stored in CPT.
 *
 * @version  1.0.0
 */
class MasVideos_Video_Data_Store_CPT extends MasVideos_Data_Store_WP implements MasVideos_Object_Data_Store_Interface, MasVideos_Video_Data_Store_Interface {

    /**
     * Data stored in meta keys, but not considered "meta".
     *
     * @since 1.0.0
     * @var array
     */
    protected $internal_meta_keys = array(
        '_visibility',
        '_default_attributes',
        '_video_attributes',
        '_featured',
        '_masvideos_rating_count',
        '_masvideos_average_rating',
        '_masvideos_review_count',
        '_thumbnail_id',
        '_video_choice',
        '_video_attachment_id',
        '_video_embed_content',
        '_video_url_link',
        '_file_paths',
        '_video_image_gallery',
        '_video_version',
        '_wp_old_slug',
        '_edit_last',
        '_edit_lock',
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
     * Method to create a new video in the database.
     *
     * @param MasVideos_Video $video Video object.
     */
    public function create( &$video ) {
        if ( ! $video->get_date_created( 'edit' ) ) {
            $video->set_date_created( current_time( 'timestamp', true ) );
        }

        $id = wp_insert_post(
            apply_filters(
                'masvideos_new_video_data', array(
                    'post_type'      => 'video',
                    'post_status'    => $video->get_status() ? $video->get_status() : 'publish',
                    'post_author'    => get_current_user_id(),
                    'post_title'     => $video->get_name() ? $video->get_name() : __( 'Video', 'masvideos' ),
                    'post_content'   => $video->get_description(),
                    'post_excerpt'   => $video->get_short_description(),
                    'post_parent'    => $video->get_parent_id(),
                    'comment_status' => $video->get_reviews_allowed() ? 'open' : 'closed',
                    'ping_status'    => 'closed',
                    'menu_order'     => $video->get_menu_order(),
                    'post_date'      => gmdate( 'Y-m-d H:i:s', $video->get_date_created( 'edit' )->getOffsetTimestamp() ),
                    'post_date_gmt'  => gmdate( 'Y-m-d H:i:s', $video->get_date_created( 'edit' )->getTimestamp() ),
                    'post_name'      => $video->get_slug( 'edit' ),
                )
            ), true
        );

        if ( $id && ! is_wp_error( $id ) ) {
            $video->set_id( $id );

            $this->update_post_meta( $video, true );
            $this->update_terms( $video, true );
            $this->update_visibility( $video, true );
            $this->update_attributes( $video, true );
            $this->handle_updated_props( $video );

            $video->save_meta_data();
            $video->apply_changes();

            $this->clear_caches( $video );

            do_action( 'masvideos_new_video', $id );
        }
    }

    /**
     * Method to read a video from the database.
     *
     * @param MasVideos_Video $video Video object.
     * @throws Exception If invalid video.
     */
    public function read( &$video ) {
        $video->set_defaults();
        $post_object = get_post( $video->get_id() );

        if ( ! $video->get_id() || ! $post_object || 'video' !== $post_object->post_type ) {
            throw new Exception( __( 'Invalid video.', 'masvideos' ) );
        }

        $video->set_props(
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

        $this->read_attributes( $video );
        $this->read_visibility( $video );
        $this->read_video_data( $video );
        $this->read_extra_data( $video );
        $video->set_object_read( true );
    }

    /**
     * Method to update a video in the database.
     *
     * @param MasVideos_Video $video Video object.
     */
    public function update( &$video ) {
        $video->save_meta_data();
        $changes = $video->get_changes();

        // Only update the post when the post data changes.
        if ( array_intersect( array( 'description', 'short_description', 'name', 'parent_id', 'reviews_allowed', 'status', 'menu_order', 'date_created', 'date_modified', 'slug' ), array_keys( $changes ) ) ) {
            $post_data = array(
                'post_content'   => $video->get_description( 'edit' ),
                'post_excerpt'   => $video->get_short_description( 'edit' ),
                'post_title'     => $video->get_name( 'edit' ),
                'post_parent'    => $video->get_parent_id( 'edit' ),
                'comment_status' => $video->get_reviews_allowed( 'edit' ) ? 'open' : 'closed',
                'post_status'    => $video->get_status( 'edit' ) ? $video->get_status( 'edit' ) : 'publish',
                'menu_order'     => $video->get_menu_order( 'edit' ),
                'post_name'      => $video->get_slug( 'edit' ),
                'post_type'      => 'video',
            );
            if ( $video->get_date_created( 'edit' ) ) {
                $post_data['post_date']     = gmdate( 'Y-m-d H:i:s', $video->get_date_created( 'edit' )->getOffsetTimestamp() );
                $post_data['post_date_gmt'] = gmdate( 'Y-m-d H:i:s', $video->get_date_created( 'edit' )->getTimestamp() );
            }
            if ( isset( $changes['date_modified'] ) && $video->get_date_modified( 'edit' ) ) {
                $post_data['post_modified']     = gmdate( 'Y-m-d H:i:s', $video->get_date_modified( 'edit' )->getOffsetTimestamp() );
                $post_data['post_modified_gmt'] = gmdate( 'Y-m-d H:i:s', $video->get_date_modified( 'edit' )->getTimestamp() );
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
                $GLOBALS['wpdb']->update( $GLOBALS['wpdb']->posts, $post_data, array( 'ID' => $video->get_id() ) );
                clean_post_cache( $video->get_id() );
            } else {
                wp_update_post( array_merge( array( 'ID' => $video->get_id() ), $post_data ) );
            }
            $video->read_meta_data( true ); // Refresh internal meta data, in case things were hooked into `save_post` or another WP hook.

        } else { // Only update post modified time to record this save event.
            $GLOBALS['wpdb']->update(
                $GLOBALS['wpdb']->posts,
                array(
                    'post_modified'     => current_time( 'mysql' ),
                    'post_modified_gmt' => current_time( 'mysql', 1 ),
                ),
                array(
                    'ID' => $video->get_id(),
                )
            );
            clean_post_cache( $video->get_id() );
        }

        $this->update_post_meta( $video );
        $this->update_terms( $video );
        $this->update_visibility( $video );
        $this->update_attributes( $video );
        $this->handle_updated_props( $video );

        $video->apply_changes();

        $this->clear_caches( $video );

        do_action( 'masvideos_update_video', $video->get_id() );
    }

    /**
     * Method to delete a video from the database.
     *
     * @param MasVideos_Video $video Video object.
     * @param array      $args Array of args to pass to the delete method.
     */
    public function delete( &$video, $args = array() ) {
        $id        = $video->get_id();
        $post_type = 'video';

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
            $video->set_id( 0 );
            do_action( 'masvideos_delete_' . $post_type, $id );
        } else {
            wp_trash_post( $id );
            $video->set_status( 'trash' );
            do_action( 'masvideos_trash_' . $post_type, $id );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Additional Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Read video data. Can be overridden by child classes to load other props.
     *
     * @param MasVideos_Video $video Video object.
     * @since 1.0.0
     */
    protected function read_video_data( &$video ) {
        $id             = $video->get_id();
        $review_count   = get_post_meta( $id, '_masvideos_review_count', true );
        $rating_counts  = get_post_meta( $id, '_masvideos_rating_count', true );
        $average_rating = get_post_meta( $id, '_masvideos_average_rating', true );

        if ( '' === $review_count ) {
            MasVideos_Comments::get_review_count_for_video( $video );
        } else {
            $video->set_review_count( $review_count );
        }

        if ( '' === $rating_counts ) {
            MasVideos_Comments::get_rating_counts_for_video( $video );
        } else {
            $video->set_rating_counts( $rating_counts );
        }

        if ( '' === $average_rating ) {
            MasVideos_Comments::get_average_rating_for_video( $video );
        } else {
            $video->set_average_rating( $average_rating );
        }

        $video->set_props(
            array(
                'default_attributes'    => get_post_meta( $id, '_default_attributes', true ),
                'category_ids'          => $this->get_term_ids( $video, 'video_cat' ),
                'tag_ids'               => $this->get_term_ids( $video, 'video_tag' ),
                'gallery_image_ids'     => array_filter( explode( ',', get_post_meta( $id, '_video_image_gallery', true ) ) ),
                'image_id'              => get_post_thumbnail_id( $id ),
                'video_choice'          => get_post_meta( $id, '_video_choice', true ),
                'video_attachment_id'   => get_post_meta( $id, '_video_attachment_id', true ),
                'video_embed_content'   => get_post_meta( $id, '_video_embed_content', true ),
                'video_url_link'        => get_post_meta( $id, '_video_url_link', true ),
            )
        );
    }

    /**
     * Read extra data associated with the video, like button text or video URL for external videos.
     *
     * @param MasVideos_Video $video Video object.
     * @since 1.0.0
     */
    protected function read_extra_data( &$video ) {
        foreach ( $video->get_extra_data_keys() as $key ) {
            $function = 'set_' . $key;
            if ( is_callable( array( $video, $function ) ) ) {
                $video->{$function}( get_post_meta( $video->get_id(), '_' . $key, true ) );
            }
        }
    }

    /**
	 * Convert visibility terms to props.
	 * Catalog visibility valid values are 'visible', 'catalog', 'search', and 'hidden'.
	 *
	 * @param MasVideos_Video $video Video object.
	 * @since 1.0.0
	 */
	protected function read_visibility( &$video ) {
		$terms           = get_the_terms( $video->get_id(), 'video_visibility' );
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

		$video->set_props(
			array(
				'featured'           => $featured,
				'catalog_visibility' => $catalog_visibility,
			)
		);
	}

    /**
     * Read attributes from post meta.
     *
     * @param MasVideos_Video $video Video object.
     */
    protected function read_attributes( &$video ) {
        $meta_attributes = get_post_meta( $video->get_id(), '_video_attributes', true );

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
                    $id      = masvideos_attribute_taxonomy_id_by_name( 'video', $meta_value['name'] );
                    $options = masvideos_get_object_terms( $video->get_id(), $meta_value['name'], 'term_id' );
                } else {
                    $id      = 0;
                    $options = masvideos_get_text_attributes( $meta_value['value'] );
                }

                $attribute = new MasVideos_Video_Attribute();
                $attribute->set_id( $id );
                $attribute->set_name( $meta_value['name'] );
                $attribute->set_options( $options );
                $attribute->set_position( $meta_value['position'] );
                $attribute->set_visible( $meta_value['is_visible'] );
                $attributes[] = $attribute;
            }
            $video->set_attributes( $attributes );
        }
    }

    /**
     * Helper method that updates all the post meta for a video based on it's settings in the MasVideos_Video class.
     *
     * @param MasVideos_Video $video Video object.
     * @param bool       $force Force update. Used during create.
     * @since 1.0.0
     */
    protected function update_post_meta( &$video, $force = false ) {
        $meta_key_to_props = array(
            '_default_attributes'           => 'default_attributes',
            '_video_image_gallery'          => 'gallery_image_ids',
            '_thumbnail_id'                 => 'image_id',
            '_video_choice'                 => 'video_choice',
            '_video_attachment_id'          => 'video_attachment_id',
            '_video_embed_content'          => 'video_embed_content',
            '_video_url_link'               => 'video_url_link',
            '_masvideos_average_rating'     => 'average_rating',
            '_masvideos_rating_count'       => 'rating_counts',
            '_masvideos_review_count'       => 'review_count',
        );

        // Make sure to take extra data (like video url or text for external videos) into account.
        $extra_data_keys = $video->get_extra_data_keys();

        foreach ( $extra_data_keys as $key ) {
            $meta_key_to_props[ '_' . $key ] = $key;
        }

        $props_to_update = $force ? $meta_key_to_props : $this->get_props_to_update( $video, $meta_key_to_props );

        foreach ( $props_to_update as $meta_key => $prop ) {
            $value = $video->{"get_$prop"}( 'edit' );
            $value = is_string( $value ) ? wp_slash( $value ) : $value;
            switch ( $prop ) {
                case 'gallery_image_ids':
                    $updated = update_post_meta( $video->get_id(), $meta_key, implode( ',', $value ) );
                    break;
                case 'image_id':
                    if ( ! empty( $value ) ) {
                        set_post_thumbnail( $video->get_id(), $value );
                    } else {
                        delete_post_meta( $video->get_id(), '_thumbnail_id' );
                    }
                    $updated = true;
                    break;
                default:
                    $updated = update_post_meta( $video->get_id(), $meta_key, $value );
                    break;
            }
            if ( $updated ) {
                $this->updated_props[] = $prop;
            }
        }

        // Update extra data associated with the video like button text or video URL for external videos.
        if ( ! $this->extra_data_saved ) {
            foreach ( $extra_data_keys as $key ) {
                if ( ! array_key_exists( '_' . $key, $props_to_update ) ) {
                    continue;
                }
                $function = 'get_' . $key;
                if ( is_callable( array( $video, $function ) ) ) {
                    $value = $video->{$function}( 'edit' );
                    $value = is_string( $value ) ? wp_slash( $value ) : $value;

                    if ( update_post_meta( $video->get_id(), '_' . $key, $value ) ) {
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
     * @param MasVideos_Video $video Video Object.
     */
    protected function handle_updated_props( &$video ) {

        // Trigger action so 3rd parties can deal with updated props.
        do_action( 'masvideos_video_object_updated_props', $video, $this->updated_props );

        // After handling, we can reset the props array.
        $this->updated_props = array();
    }

    /**
     * For all stored terms in all taxonomies, save them to the DB.
     *
     * @param MasVideos_Video $video Video object.
     * @param bool       $force Force update. Used during create.
     * @since 1.0.0
     */
    protected function update_terms( &$video, $force = false ) {
        $changes = $video->get_changes();

        if ( $force || array_key_exists( 'category_ids', $changes ) ) {
            $categories = $video->get_category_ids( 'edit' );

            if ( empty( $categories ) && get_option( 'default_video_cat', 0 ) ) {
                $categories = array( get_option( 'default_video_cat', 0 ) );
            }

            wp_set_post_terms( $video->get_id(), $categories, 'video_cat', false );
        }
        if ( $force || array_key_exists( 'tag_ids', $changes ) ) {
            wp_set_post_terms( $video->get_id(), $video->get_tag_ids( 'edit' ), 'video_tag', false );
        }
    }

    /**
	 * Update visibility terms based on props.
	 *
	 * @since 1.0.0
	 *
	 * @param MasVideos_Video $video Video object.
	 * @param bool       $force Force update. Used during create.
	 */
	protected function update_visibility( &$video, $force = false ) {
		$changes = $video->get_changes();

		if ( $force || array_intersect( array( 'featured', 'average_rating', 'catalog_visibility' ), array_keys( $changes ) ) ) {
			$terms = array();

			if ( $video->get_featured() ) {
				$terms[] = 'featured';
			}

			$rating = min( 10, round( $video->get_average_rating(), 0 ) );

			if ( $rating > 0 ) {
				$terms[] = 'rated-' . $rating;
			}

			switch ( $video->get_catalog_visibility() ) {
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

			if ( ! is_wp_error( wp_set_post_terms( $video->get_id(), $terms, 'video_visibility', false ) ) ) {
				delete_transient( 'masvideos_featured_videos' );
				do_action( 'masvideos_video_set_visibility', $video->get_id(), $video->get_catalog_visibility() );
			}
		}
	}

    /**
     * Update attributes which are a mix of terms and meta data.
     *
     * @param MasVideos_Video $video Video object.
     * @param bool       $force Force update. Used during create.
     * @since 1.0.0
     */
    protected function update_attributes( &$video, $force = false ) {
        $changes = $video->get_changes();

        if ( $force || array_key_exists( 'attributes', $changes ) ) {
            $attributes  = $video->get_attributes();
            $meta_values = array();

            if ( $attributes ) {
                foreach ( $attributes as $attribute_key => $attribute ) {
                    $value = '';

                    delete_transient( 'masvideos_layered_nav_counts_' . $attribute_key );

                    if ( is_null( $attribute ) ) {
                        if ( taxonomy_exists( $attribute_key ) ) {
                            // Handle attributes that have been unset.
                            wp_set_object_terms( $video->get_id(), array(), $attribute_key );
                        }
                        continue;

                    } elseif ( $attribute->is_taxonomy() ) {
                        wp_set_object_terms( $video->get_id(), wp_list_pluck( $attribute->get_terms(), 'term_id' ), $attribute->get_name() );
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
            update_post_meta( $video->get_id(), '_video_attributes', $meta_values );
        }
    }

    /**
     * Clear any caches.
     *
     * @param MasVideos_Video $video Video object.
     * @since 1.0.0
     */
    protected function clear_caches( &$video ) {
        masvideos_delete_video_transients( $video->get_id() );
        MasVideos_Cache_Helper::incr_cache_prefix( 'video_' . $video->get_id() );
    }

    /*
    |--------------------------------------------------------------------------
    | masvideos-video-functions.php methods
    |--------------------------------------------------------------------------
    */

    /**
     * Returns a list of video IDs ( id as key => parent as value) that are
     * featured. Uses get_posts instead of masvideos_get_videos since we want
     * some extra meta queries and ALL videos (posts_per_page = -1).
     *
     * @return array
     * @since 1.0.0
     */
    public function get_featured_video_ids() {
        $video_visibility_term_ids = masvideos_get_video_visibility_term_ids();

		return get_posts(
			array(
				'post_type'      => array( 'video' ),
				'posts_per_page' => -1,
				'post_status'    => 'publish',
				// phpcs:ignore WordPress.VIP.SlowDBQuery.slow_db_query_tax_query
				'tax_query'      => array(
					'relation' => 'AND',
					array(
						'taxonomy' => 'video_visibility',
						'field'    => 'term_taxonomy_id',
						'terms'    => array( $video_visibility_term_ids['featured'] ),
					),
					array(
						'taxonomy' => 'video_visibility',
						'field'    => 'term_taxonomy_id',
						'terms'    => array( $video_visibility_term_ids['exclude-from-catalog'] ),
						'operator' => 'NOT IN',
					),
				),
				'fields'         => 'id=>parent',
			)
		);
    }

    /**
     * Return a list of related videos (using data like categories and IDs).
     *
     * @since 1.0.0
     * @param array $cats_array  List of categories IDs.
     * @param array $tags_array  List of tags IDs.
     * @param array $exclude_ids Excluded IDs.
     * @param int   $limit       Limit of results.
     * @param int   $video_id  Video ID.
     * @return array
     */
    public function get_related_videos( $cats_array, $tags_array, $exclude_ids, $limit, $video_id ) {
        global $wpdb;

        $args = array(
            'categories'  => $cats_array,
            'tags'        => $tags_array,
            'exclude_ids' => $exclude_ids,
            'limit'       => $limit + 10,
        );

        $related_video_query = (array) apply_filters( 'masvideos_video_related_posts_query', $this->get_related_videos_query( $cats_array, $tags_array, $exclude_ids, $limit + 10 ), $video_id, $args );

        // phpcs:ignore WordPress.VIP.DirectDatabaseQuery.DirectQuery, WordPress.WP.PreparedSQL.NotPrepared
        return $wpdb->get_col( implode( ' ', $related_video_query ) );
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
    public function get_related_videos_query( $cats_array, $tags_array, $exclude_ids, $limit ) {
        global $wpdb;

		$include_term_ids            = array_merge( $cats_array, $tags_array );
		$exclude_term_ids            = array();
		$video_visibility_term_ids   = masvideos_get_video_visibility_term_ids();

		if ( $video_visibility_term_ids['exclude-from-catalog'] ) {
			$exclude_term_ids[] = $video_visibility_term_ids['exclude-from-catalog'];
		}

		$query = array(
			'fields' => "
				SELECT DISTINCT ID FROM {$wpdb->posts} p
			",
			'join'   => '',
			'where'  => "
				WHERE 1=1
				AND p.post_status = 'publish'
				AND p.post_type = 'video'

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
     * Update a videos average rating meta.
     *
     * @since 1.0.0
     * @param MasVideos_Video $video Video object.
     */
    public function update_average_rating( $video ) {
        update_post_meta( $video->get_id(), '_masvideos_average_rating', $video->get_average_rating( 'edit' ) );
		self::update_visibility( $video, true );
    }

    /**
     * Update a videos review count meta.
     *
     * @since 1.0.0
     * @param MasVideos_Video $video Video object.
     */
    public function update_review_count( $video ) {
        update_post_meta( $video->get_id(), '_masvideos_review_count', $video->get_review_count( 'edit' ) );
    }

    /**
     * Update a videos rating counts.
     *
     * @since 1.0.0
     * @param MasVideos_Video $video Video object.
     */
    public function update_rating_counts( $video ) {
        update_post_meta( $video->get_id(), '_masvideos_rating_count', $video->get_rating_counts( 'edit' ) );
    }

    /**
     * Returns an array of videos.
     *
     * @param  array $args Args to pass to MasVideos_Video_Query().
     * @return array|object
     * @see masvideos_get_videos
     */
    public function get_videos( $args = array() ) {
        $query = new MasVideos_Video_Query( $args );
        return $query->get_videos();
    }

    /**
     * Search video data for a term and return ids.
     *
     * @param  string   $term Search term.
     * @param  bool     $all_statuses Should we search all statuses or limit to published.
     * @param  null|int $limit Limit returned results.
     * @since  1.0.0.
     * @return array of ids
     */
    public function search_videos( $term, $all_statuses = false, $limit = null ) {
        global $wpdb;

        $post_types    = array( 'video' );
        $post_statuses = current_user_can( 'edit_private_videos' ) ? array( 'private', 'publish' ) : array( 'publish' );
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
            "SELECT DISTINCT posts.ID as video_id, posts.post_parent as parent_id FROM {$wpdb->posts} posts
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

        $video_ids = wp_parse_id_list( array_merge( wp_list_pluck( $search_results, 'video_id' ), wp_list_pluck( $search_results, 'parent_id' ) ) );

        if ( is_numeric( $term ) ) {
            $post_id   = absint( $term );
            $post_type = get_post_type( $post_id );

            if ( 'video' === $post_type ) {
                $video_ids[] = $post_id;
            }

            $video_ids[] = wp_get_post_parent_id( $post_id );
        }

        return wp_parse_id_list( $video_ids );
    }

    /**
     * Add ability to get videos by 'reviews_allowed' in MasVideos_Video_Query.
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
     * Get valid WP_Query args from a MasVideos_Video_Query's query variables.
     *
     * @since 3.2.0
     * @param array $query_vars Query vars from a MasVideos_Video_Query.
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

        // Handle video categories.
        if ( ! empty( $query_vars['category'] ) ) {
            $wp_query_args['tax_query'][] = array(
                'taxonomy' => 'video_cat',
                'field'    => 'slug',
                'terms'    => $query_vars['category'],
            );
        }

        // Handle video tags.
        if ( ! empty( $query_vars['tag'] ) ) {
            unset( $wp_query_args['tag'] );
            $wp_query_args['tax_query'][] = array(
                'taxonomy' => 'video_tag',
                'field'    => 'slug',
                'terms'    => $query_vars['tag'],
            );
        }

        // Handle featured.
		if ( '' !== $manual_queries['featured'] ) {
			$video_visibility_term_ids = masvideos_get_video_visibility_term_ids();
			if ( $manual_queries['featured'] ) {
				$wp_query_args['tax_query'][] = array(
					'taxonomy' => 'video_visibility',
					'field'    => 'term_taxonomy_id',
					'terms'    => array( $video_visibility_term_ids['featured'] ),
				);
				$wp_query_args['tax_query'][] = array(
					'taxonomy' => 'video_visibility',
					'field'    => 'term_taxonomy_id',
					'terms'    => array( $video_visibility_term_ids['exclude-from-catalog'] ),
					'operator' => 'NOT IN',
				);
			} else {
				$wp_query_args['tax_query'][] = array(
					'taxonomy' => 'video_visibility',
					'field'    => 'term_taxonomy_id',
					'terms'    => array( $video_visibility_term_ids['featured'] ),
					'operator' => 'NOT IN',
				);
			}
		}

		// Handle visibility.
		if ( $manual_queries['visibility'] ) {
			switch ( $manual_queries['visibility'] ) {
				case 'search':
					$wp_query_args['tax_query'][] = array(
						'taxonomy' => 'video_visibility',
						'field'    => 'slug',
						'terms'    => array( 'exclude-from-search' ),
						'operator' => 'NOT IN',
					);
					break;
				case 'catalog':
					$wp_query_args['tax_query'][] = array(
						'taxonomy' => 'video_visibility',
						'field'    => 'slug',
						'terms'    => array( 'exclude-from-catalog' ),
						'operator' => 'NOT IN',
					);
					break;
				case 'visible':
					$wp_query_args['tax_query'][] = array(
						'taxonomy' => 'video_visibility',
						'field'    => 'slug',
						'terms'    => array( 'exclude-from-catalog', 'exclude-from-search' ),
						'operator' => 'NOT IN',
					);
					break;
				case 'hidden':
					$wp_query_args['tax_query'][] = array(
						'taxonomy' => 'video_visibility',
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

        return apply_filters( 'masvideos_video_data_store_cpt_get_videos_query', $wp_query_args, $query_vars, $this );
    }

    /**
     * Query for Videos matching specific criteria.
     *
     * @since 1.0.0
     *
     * @param array $query_vars Query vars from a MasVideos_Video_Query.
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
            update_post_caches( $query->posts, array( 'video' ) );
        }

        $videos = ( isset( $query_vars['return'] ) && 'ids' === $query_vars['return'] ) ? $query->posts : array_filter( array_map( 'masvideos_get_video', $query->posts ) );

        if ( isset( $query_vars['paginate'] ) && $query_vars['paginate'] ) {
            return (object) array(
                'videos'        => $videos,
                'total'         => $query->found_posts,
                'max_num_pages' => $query->max_num_pages,
            );
        }

        return $videos;
    }
}
