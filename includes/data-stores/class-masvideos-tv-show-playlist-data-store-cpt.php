<?php
/**
 * MasVideos_TV_Show_Playlist_Data_Store_CPT class file.
 *
 * @package MasVideos/Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * MasVideos Movie Data Store: Stored in CPT.
 *
 * @version  1.0.0
 */
class MasVideos_TV_Show_Playlist_Data_Store_CPT extends MasVideos_Data_Store_WP implements MasVideos_Object_Data_Store_Interface, MasVideos_TV_Show_Playlist_Data_Store_Interface {

    /**
     * Data stored in meta keys, but not considered "meta".
     *
     * @since 1.0.0
     * @var array
     */
    protected $internal_meta_keys = array(
        '_thumbnail_id',
        '_file_paths',
        '_tv_show_playlist_version',
        '_wp_old_slug',
        '_edit_last',
        '_edit_lock',
        '_tv_show_ids',
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
     * Method to create a new tv show playlist in the database.
     *
     * @param MasVideos_TV_Show_Playlist $tv_show_playlist Movie object.
     */
    public function create( &$tv_show_playlist ) {
        if ( ! $tv_show_playlist->get_date_created( 'edit' ) ) {
            $tv_show_playlist->set_date_created( current_time( 'timestamp', true ) );
        }

        $id = wp_insert_post(
            apply_filters(
                'masvideos_new_tv_show_playlist_data', array(
                    'post_type'      => 'tv_show_playlist',
                    'post_status'    => $tv_show_playlist->get_status() ? $tv_show_playlist->get_status() : 'publish',
                    'post_author'    => get_current_user_id(),
                    'post_title'     => $tv_show_playlist->get_name() ? $tv_show_playlist->get_name() : __( 'Movie', 'masvideos' ),
                    'post_content'   => $tv_show_playlist->get_description(),
                    'post_excerpt'   => $tv_show_playlist->get_short_description(),
                    'post_parent'    => $tv_show_playlist->get_parent_id(),
                    'ping_status'    => 'closed',
                    'post_date'      => gmdate( 'Y-m-d H:i:s', $tv_show_playlist->get_date_created( 'edit' )->getOffsetTimestamp() ),
                    'post_date_gmt'  => gmdate( 'Y-m-d H:i:s', $tv_show_playlist->get_date_created( 'edit' )->getTimestamp() ),
                    'post_name'      => $tv_show_playlist->get_slug( 'edit' ),
                )
            ), true
        );

        if ( $id && ! is_wp_error( $id ) ) {
            $tv_show_playlist->set_id( $id );

            $this->update_post_meta( $tv_show_playlist, true );
            $this->handle_updated_props( $tv_show_playlist );

            $tv_show_playlist->save_meta_data();
            $tv_show_playlist->apply_changes();

            $this->clear_caches( $tv_show_playlist );

            do_action( 'masvideos_new_tv_show_playlist', $id );
        }
    }

    /**
     * Method to read a tv show playlist from the database.
     *
     * @param MasVideos_TV_Show_Playlist $tv_show_playlist Movie object.
     * @throws Exception If invalid tv show playlist.
     */
    public function read( &$tv_show_playlist ) {
        $tv_show_playlist->set_defaults();
        $post_object = get_post( $tv_show_playlist->get_id() );

        if ( ! $tv_show_playlist->get_id() || ! $post_object || 'tv_show_playlist' !== $post_object->post_type ) {
            throw new Exception( __( 'Invalid tv show playlist.', 'masvideos' ) );
        }

        $tv_show_playlist->set_props(
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

        $this->read_tv_show_playlist_data( $tv_show_playlist );
        $this->read_extra_data( $tv_show_playlist );
        $tv_show_playlist->set_object_read( true );
    }

    /**
     * Method to update a tv show playlist in the database.
     *
     * @param MasVideos_TV_Show_Playlist $tv_show_playlist Movie object.
     */
    public function update( &$tv_show_playlist ) {
        $tv_show_playlist->save_meta_data();
        $changes = $tv_show_playlist->get_changes();

        // Only update the post when the post data changes.
        if ( array_intersect( array( 'description', 'short_description', 'name', 'parent_id', 'reviews_allowed', 'status', 'menu_order', 'date_created', 'date_modified', 'slug' ), array_keys( $changes ) ) ) {
            $post_data = array(
                'post_content'   => $tv_show_playlist->get_description( 'edit' ),
                'post_excerpt'   => $tv_show_playlist->get_short_description( 'edit' ),
                'post_title'     => $tv_show_playlist->get_name( 'edit' ),
                'post_parent'    => $tv_show_playlist->get_parent_id( 'edit' ),
                'post_status'    => $tv_show_playlist->get_status( 'edit' ) ? $tv_show_playlist->get_status( 'edit' ) : 'publish',
                'post_name'      => $tv_show_playlist->get_slug( 'edit' ),
                'post_type'      => 'tv_show_playlist',
            );
            if ( $tv_show_playlist->get_date_created( 'edit' ) ) {
                $post_data['post_date']     = gmdate( 'Y-m-d H:i:s', $tv_show_playlist->get_date_created( 'edit' )->getOffsetTimestamp() );
                $post_data['post_date_gmt'] = gmdate( 'Y-m-d H:i:s', $tv_show_playlist->get_date_created( 'edit' )->getTimestamp() );
            }
            if ( isset( $changes['date_modified'] ) && $tv_show_playlist->get_date_modified( 'edit' ) ) {
                $post_data['post_modified']     = gmdate( 'Y-m-d H:i:s', $tv_show_playlist->get_date_modified( 'edit' )->getOffsetTimestamp() );
                $post_data['post_modified_gmt'] = gmdate( 'Y-m-d H:i:s', $tv_show_playlist->get_date_modified( 'edit' )->getTimestamp() );
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
                $GLOBALS['wpdb']->update( $GLOBALS['wpdb']->posts, $post_data, array( 'ID' => $tv_show_playlist->get_id() ) );
                clean_post_cache( $tv_show_playlist->get_id() );
            } else {
                wp_update_post( array_merge( array( 'ID' => $tv_show_playlist->get_id() ), $post_data ) );
            }
            $tv_show_playlist->read_meta_data( true ); // Refresh internal meta data, in case things were hooked into `save_post` or another WP hook.

        } else { // Only update post modified time to record this save event.
            $GLOBALS['wpdb']->update(
                $GLOBALS['wpdb']->posts,
                array(
                    'post_modified'     => current_time( 'mysql' ),
                    'post_modified_gmt' => current_time( 'mysql', 1 ),
                ),
                array(
                    'ID' => $tv_show_playlist->get_id(),
                )
            );
            clean_post_cache( $tv_show_playlist->get_id() );
        }

        $this->update_post_meta( $tv_show_playlist );
        $this->handle_updated_props( $tv_show_playlist );

        $tv_show_playlist->apply_changes();

        $this->clear_caches( $tv_show_playlist );

        do_action( 'masvideos_update_tv_show_playlist', $tv_show_playlist->get_id() );
    }

    /**
     * Method to delete a tv show playlist from the database.
     *
     * @param MasVideos_TV_Show_Playlist $tv_show_playlist Movie object.
     * @param array      $args Array of args to pass to the delete method.
     */
    public function delete( &$tv_show_playlist, $args = array() ) {
        $id        = $tv_show_playlist->get_id();
        $post_type = 'tv_show_playlist';

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
            $tv_show_playlist->set_id( 0 );
            do_action( 'masvideos_delete_' . $post_type, $id );
        } else {
            wp_trash_post( $id );
            $tv_show_playlist->set_status( 'trash' );
            do_action( 'masvideos_trash_' . $post_type, $id );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Additional Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Read tv show playlist data. Can be overridden by child classes to load other props.
     *
     * @param MasVideos_TV_Show_Playlist $tv_show_playlist Movie object.
     * @since 1.0.0
     */
    protected function read_tv_show_playlist_data( &$tv_show_playlist ) {
        $id             = $tv_show_playlist->get_id();

        $tv_show_playlist->set_props(
            array(
                'image_id'              => get_post_thumbnail_id( $id ),
                'tv_show_ids'           => get_post_meta( $id, '_tv_show_ids', true ),
            )
        );
    }

    /**
     * Read extra data associated with the tv show, like button text or tv show URL for external tv show playlists.
     *
     * @param MasVideos_TV_Show_Playlist $tv_show_playlist Movie object.
     * @since 1.0.0
     */
    protected function read_extra_data( &$tv_show_playlist ) {
        foreach ( $tv_show_playlist->get_extra_data_keys() as $key ) {
            $function = 'set_' . $key;
            if ( is_callable( array( $tv_show_playlist, $function ) ) ) {
                $tv_show_playlist->{$function}( get_post_meta( $tv_show_playlist->get_id(), '_' . $key, true ) );
            }
        }
    }

    /**
     * Helper method that updates all the post meta for a tv show playlist based on it's settings in the MasVideos_TV_Show_Playlist class.
     *
     * @param MasVideos_TV_Show_Playlist $tv_show_playlist Movie object.
     * @param bool       $force Force update. Used during create.
     * @since 1.0.0
     */
    protected function update_post_meta( &$tv_show_playlist, $force = false ) {
        $meta_key_to_props = array(
            '_thumbnail_id'                 => 'image_id',
            '_tv_show_ids'                  => 'tv_show_ids',
        );

        // Make sure to take extra data (like tv show playlist url or text for external tv show playlists) into account.
        $extra_data_keys = $tv_show_playlist->get_extra_data_keys();

        foreach ( $extra_data_keys as $key ) {
            $meta_key_to_props[ '_' . $key ] = $key;
        }

        $props_to_update = $force ? $meta_key_to_props : $this->get_props_to_update( $tv_show_playlist, $meta_key_to_props );

        foreach ( $props_to_update as $meta_key => $prop ) {
            $value = $tv_show_playlist->{"get_$prop"}( 'edit' );
            $value = is_string( $value ) ? wp_slash( $value ) : $value;
            switch ( $prop ) {
                case 'image_id':
                    if ( ! empty( $value ) ) {
                        set_post_thumbnail( $tv_show_playlist->get_id(), $value );
                    } else {
                        delete_post_meta( $tv_show_playlist->get_id(), '_thumbnail_id' );
                    }
                    $updated = true;
                    break;
                default:
                    $updated = update_post_meta( $tv_show_playlist->get_id(), $meta_key, $value );
                    break;
            }
            if ( $updated ) {
                $this->updated_props[] = $prop;
            }
        }

        // Update extra data associated with the tv show playlist like button text or tv show playlist URL for external tv show playlists.
        if ( ! $this->extra_data_saved ) {
            foreach ( $extra_data_keys as $key ) {
                if ( ! array_key_exists( '_' . $key, $props_to_update ) ) {
                    continue;
                }
                $function = 'get_' . $key;
                if ( is_callable( array( $tv_show_playlist, $function ) ) ) {
                    $value = $tv_show_playlist->{$function}( 'edit' );
                    $value = is_string( $value ) ? wp_slash( $value ) : $value;

                    if ( update_post_meta( $tv_show_playlist->get_id(), '_' . $key, $value ) ) {
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
     * @param MasVideos_TV_Show_Playlist $tv_show_playlist Movie Object.
     */
    protected function handle_updated_props( &$tv_show_playlist ) {

        // Trigger action so 3rd parties can deal with updated props.
        do_action( 'masvideos_tv_show_playlist_object_updated_props', $tv_show_playlist, $this->updated_props );

        // After handling, we can reset the props array.
        $this->updated_props = array();
    }

    /**
     * Clear any caches.
     *
     * @param MasVideos_TV_Show_Playlist $tv_show_playlist Movie object.
     * @since 1.0.0
     */
    protected function clear_caches( &$tv_show_playlist ) {
        masvideos_delete_tv_show_playlist_transients( $tv_show_playlist->get_id() );
        MasVideos_Cache_Helper::incr_cache_prefix( 'tv_show_playlist_' . $tv_show_playlist->get_id() );
    }

    /*
    |--------------------------------------------------------------------------
    | masvideos-tv-show-playlist-functions.php methods
    |--------------------------------------------------------------------------
    */

    /**
     * Returns an array of tv show playlists.
     *
     * @param  array $args Args to pass to MasVideos_TV_Show_Playlist_Query().
     * @return array|object
     * @see masvideos_get_tv_show_playlists
     */
    public function get_tv_show_playlists( $args = array() ) {
        $query = new MasVideos_TV_Show_Playlist_Query( $args );
        return $query->get_tv_show_playlists();
    }

    /**
     * Search tv show playlist data for a term and return ids.
     *
     * @param  string   $term Search term.
     * @param  bool     $all_statuses Should we search all statuses or limit to published.
     * @param  null|int $limit Limit returned results.
     * @since  1.0.0.
     * @return array of ids
     */
    public function search_tv_show_playlists( $term, $all_statuses = false, $limit = null ) {
        global $wpdb;

        $post_types    = array( 'tv_show_playlist' );
        $post_statuses = current_user_can( 'edit_private_tv_show_playlists' ) ? array( 'private', 'publish' ) : array( 'publish' );
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
            "SELECT DISTINCT posts.ID as tv_show_playlist_id, posts.post_parent as parent_id FROM {$wpdb->posts} posts
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

        $tv_show_playlist_ids = wp_parse_id_list( array_merge( wp_list_pluck( $search_results, 'tv_show_playlist_id' ), wp_list_pluck( $search_results, 'parent_id' ) ) );

        if ( is_numeric( $term ) ) {
            $post_id   = absint( $term );
            $post_type = get_post_type( $post_id );

            if ( 'tv_show_playlist' === $post_type ) {
                $tv_show_playlist_ids[] = $post_id;
            }

            $tv_show_playlist_ids[] = wp_get_post_parent_id( $post_id );
        }

        return wp_parse_id_list( $tv_show_playlist_ids );
    }

    /**
     * Get valid WP_Query args from a MasVideos_TV_Show_Playlist_Query's query variables.
     *
     * @since 1.0.0
     * @param array $query_vars Query vars from a MasVideos_TV_Show_Playlist_Query.
     * @return array
     */
    protected function get_wp_query_args( $query_vars ) {

        // Map query vars to ones that get_wp_query_args or WP_Query recognize.
        $key_mapping = array(
            'status'         => 'post_status',
            'page'           => 'paged',
            'include'        => 'post__in',
        );
        foreach ( $key_mapping as $query_key => $db_key ) {
            if ( isset( $query_vars[ $query_key ] ) ) {
                $query_vars[ $db_key ] = $query_vars[ $query_key ];
                unset( $query_vars[ $query_key ] );
            }
        }

        // These queries cannot be auto-generated so we have to remove them and build them manually.
        $manual_queries = array(
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

        // Handle date queries.
        $date_queries = array(
            'date_created'          => 'post_date',
            'date_modified'         => 'post_modified',
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

        return apply_filters( 'masvideos_tv_show_playlist_data_store_cpt_get_tv_show_playlists_query', $wp_query_args, $query_vars, $this );
    }

    /**
     * Query for TV Show Playlists matching specific criteria.
     *
     * @since 1.0.0
     *
     * @param array $query_vars Query vars from a MasVideos_TV_Show_Playlist_Query.
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
            update_post_caches( $query->posts, array( 'tv_show_playlist' ) );
        }

        $tv_show_playlists = ( isset( $query_vars['return'] ) && 'ids' === $query_vars['return'] ) ? $query->posts : array_filter( array_map( 'masvideos_get_tv_show_playlist', $query->posts ) );

        if ( isset( $query_vars['paginate'] ) && $query_vars['paginate'] ) {
            return (object) array(
                'tv_show_playlists' => $tv_show_playlists,
                'total'             => $query->found_posts,
                'max_num_pages'     => $query->max_num_pages,
            );
        }

        return $tv_show_playlists;
    }
}
