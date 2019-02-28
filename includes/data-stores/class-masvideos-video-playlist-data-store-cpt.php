<?php
/**
 * MasVideos_Video_Playlist_Data_Store_CPT class file.
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
class MasVideos_Video_Playlist_Data_Store_CPT extends MasVideos_Data_Store_WP implements MasVideos_Object_Data_Store_Interface, MasVideos_Video_Playlist_Data_Store_Interface {

    /**
     * Data stored in meta keys, but not considered "meta".
     *
     * @since 1.0.0
     * @var array
     */
    protected $internal_meta_keys = array(
        '_thumbnail_id',
        '_file_paths',
        '_video_playlist_version',
        '_wp_old_slug',
        '_edit_last',
        '_edit_lock',
        '_video_ids',
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
     * Method to create a new video playlist in the database.
     *
     * @param MasVideos_Video_Playlist $video_playlist Video object.
     */
    public function create( &$video_playlist ) {
        if ( ! $video_playlist->get_date_created( 'edit' ) ) {
            $video_playlist->set_date_created( current_time( 'timestamp', true ) );
        }

        $id = wp_insert_post(
            apply_filters(
                'masvideos_new_video_playlist_data', array(
                    'post_type'      => 'video_playlist',
                    'post_status'    => $video_playlist->get_status() ? $video_playlist->get_status() : 'publish',
                    'post_author'    => get_current_user_id(),
                    'post_title'     => $video_playlist->get_name() ? $video_playlist->get_name() : __( 'Video', 'masvideos' ),
                    'post_content'   => $video_playlist->get_description(),
                    'post_excerpt'   => $video_playlist->get_short_description(),
                    'post_parent'    => $video_playlist->get_parent_id(),
                    'ping_status'    => 'closed',
                    'post_date'      => gmdate( 'Y-m-d H:i:s', $video_playlist->get_date_created( 'edit' )->getOffsetTimestamp() ),
                    'post_date_gmt'  => gmdate( 'Y-m-d H:i:s', $video_playlist->get_date_created( 'edit' )->getTimestamp() ),
                    'post_name'      => $video_playlist->get_slug( 'edit' ),
                )
            ), true
        );

        if ( $id && ! is_wp_error( $id ) ) {
            $video_playlist->set_id( $id );

            $this->update_post_meta( $video_playlist, true );
            $this->handle_updated_props( $video_playlist );

            $video_playlist->save_meta_data();
            $video_playlist->apply_changes();

            $this->clear_caches( $video_playlist );

            do_action( 'masvideos_new_video_playlist', $id );
        }
    }

    /**
     * Method to read a video playlist from the database.
     *
     * @param MasVideos_Video_Playlist $video_playlist Video object.
     * @throws Exception If invalid video playlist.
     */
    public function read( &$video_playlist ) {
        $video_playlist->set_defaults();
        $post_object = get_post( $video_playlist->get_id() );

        if ( ! $video_playlist->get_id() || ! $post_object || 'video_playlist' !== $post_object->post_type ) {
            throw new Exception( __( 'Invalid video playlist.', 'masvideos' ) );
        }

        $video_playlist->set_props(
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

        $this->read_video_playlist_data( $video_playlist );
        $this->read_extra_data( $video_playlist );
        $video_playlist->set_object_read( true );
    }

    /**
     * Method to update a video playlist in the database.
     *
     * @param MasVideos_Video_Playlist $video_playlist Video object.
     */
    public function update( &$video_playlist ) {
        $video_playlist->save_meta_data();
        $changes = $video_playlist->get_changes();

        // Only update the post when the post data changes.
        if ( array_intersect( array( 'description', 'short_description', 'name', 'parent_id', 'reviews_allowed', 'status', 'menu_order', 'date_created', 'date_modified', 'slug' ), array_keys( $changes ) ) ) {
            $post_data = array(
                'post_content'   => $video_playlist->get_description( 'edit' ),
                'post_excerpt'   => $video_playlist->get_short_description( 'edit' ),
                'post_title'     => $video_playlist->get_name( 'edit' ),
                'post_parent'    => $video_playlist->get_parent_id( 'edit' ),
                'post_status'    => $video_playlist->get_status( 'edit' ) ? $video_playlist->get_status( 'edit' ) : 'publish',
                'post_name'      => $video_playlist->get_slug( 'edit' ),
                'post_type'      => 'video_playlist',
            );
            if ( $video_playlist->get_date_created( 'edit' ) ) {
                $post_data['post_date']     = gmdate( 'Y-m-d H:i:s', $video_playlist->get_date_created( 'edit' )->getOffsetTimestamp() );
                $post_data['post_date_gmt'] = gmdate( 'Y-m-d H:i:s', $video_playlist->get_date_created( 'edit' )->getTimestamp() );
            }
            if ( isset( $changes['date_modified'] ) && $video_playlist->get_date_modified( 'edit' ) ) {
                $post_data['post_modified']     = gmdate( 'Y-m-d H:i:s', $video_playlist->get_date_modified( 'edit' )->getOffsetTimestamp() );
                $post_data['post_modified_gmt'] = gmdate( 'Y-m-d H:i:s', $video_playlist->get_date_modified( 'edit' )->getTimestamp() );
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
                $GLOBALS['wpdb']->update( $GLOBALS['wpdb']->posts, $post_data, array( 'ID' => $video_playlist->get_id() ) );
                clean_post_cache( $video_playlist->get_id() );
            } else {
                wp_update_post( array_merge( array( 'ID' => $video_playlist->get_id() ), $post_data ) );
            }
            $video_playlist->read_meta_data( true ); // Refresh internal meta data, in case things were hooked into `save_post` or another WP hook.

        } else { // Only update post modified time to record this save event.
            $GLOBALS['wpdb']->update(
                $GLOBALS['wpdb']->posts,
                array(
                    'post_modified'     => current_time( 'mysql' ),
                    'post_modified_gmt' => current_time( 'mysql', 1 ),
                ),
                array(
                    'ID' => $video_playlist->get_id(),
                )
            );
            clean_post_cache( $video_playlist->get_id() );
        }

        $this->update_post_meta( $video_playlist );
        $this->handle_updated_props( $video_playlist );

        $video_playlist->apply_changes();

        $this->clear_caches( $video_playlist );

        do_action( 'masvideos_update_video_playlist', $video_playlist->get_id() );
    }

    /**
     * Method to delete a video playlist from the database.
     *
     * @param MasVideos_Video_Playlist $video_playlist Video object.
     * @param array      $args Array of args to pass to the delete method.
     */
    public function delete( &$video_playlist, $args = array() ) {
        $id        = $video_playlist->get_id();
        $post_type = 'video_playlist';

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
            $video_playlist->set_id( 0 );
            do_action( 'masvideos_delete_' . $post_type, $id );
        } else {
            wp_trash_post( $id );
            $video_playlist->set_status( 'trash' );
            do_action( 'masvideos_trash_' . $post_type, $id );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Additional Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Read video playlist data. Can be overridden by child classes to load other props.
     *
     * @param MasVideos_Video_Playlist $video_playlist Video object.
     * @since 1.0.0
     */
    protected function read_video_playlist_data( &$video_playlist ) {
        $id             = $video_playlist->get_id();

        $video_playlist->set_props(
            array(
                'image_id'              => get_post_thumbnail_id( $id ),
                'video_ids'             => get_post_meta( $id, '_video_ids', true ),
            )
        );
    }

    /**
     * Read extra data associated with the video, like button text or video URL for external video playlists.
     *
     * @param MasVideos_Video_Playlist $video_playlist Video object.
     * @since 1.0.0
     */
    protected function read_extra_data( &$video_playlist ) {
        foreach ( $video_playlist->get_extra_data_keys() as $key ) {
            $function = 'set_' . $key;
            if ( is_callable( array( $video_playlist, $function ) ) ) {
                $video_playlist->{$function}( get_post_meta( $video_playlist->get_id(), '_' . $key, true ) );
            }
        }
    }

    /**
     * Helper method that updates all the post meta for a video playlist based on it's settings in the MasVideos_Video_Playlist class.
     *
     * @param MasVideos_Video_Playlist $video_playlist Video object.
     * @param bool       $force Force update. Used during create.
     * @since 1.0.0
     */
    protected function update_post_meta( &$video_playlist, $force = false ) {
        $meta_key_to_props = array(
            '_thumbnail_id'                 => 'image_id',
            '_video_ids'                    => 'video_ids',
        );

        // Make sure to take extra data (like video playlist url or text for external video playlists) into account.
        $extra_data_keys = $video_playlist->get_extra_data_keys();

        foreach ( $extra_data_keys as $key ) {
            $meta_key_to_props[ '_' . $key ] = $key;
        }

        $props_to_update = $force ? $meta_key_to_props : $this->get_props_to_update( $video_playlist, $meta_key_to_props );

        foreach ( $props_to_update as $meta_key => $prop ) {
            $value = $video_playlist->{"get_$prop"}( 'edit' );
            $value = is_string( $value ) ? wp_slash( $value ) : $value;
            switch ( $prop ) {
                case 'image_id':
                    if ( ! empty( $value ) ) {
                        set_post_thumbnail( $video_playlist->get_id(), $value );
                    } else {
                        delete_post_meta( $video_playlist->get_id(), '_thumbnail_id' );
                    }
                    $updated = true;
                    break;
                default:
                    $updated = update_post_meta( $video_playlist->get_id(), $meta_key, $value );
                    break;
            }
            if ( $updated ) {
                $this->updated_props[] = $prop;
            }
        }

        // Update extra data associated with the video playlist like button text or video playlist URL for external video playlists.
        if ( ! $this->extra_data_saved ) {
            foreach ( $extra_data_keys as $key ) {
                if ( ! array_key_exists( '_' . $key, $props_to_update ) ) {
                    continue;
                }
                $function = 'get_' . $key;
                if ( is_callable( array( $video_playlist, $function ) ) ) {
                    $value = $video_playlist->{$function}( 'edit' );
                    $value = is_string( $value ) ? wp_slash( $value ) : $value;

                    if ( update_post_meta( $video_playlist->get_id(), '_' . $key, $value ) ) {
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
     * @param MasVideos_Video_Playlist $video_playlist Video Object.
     */
    protected function handle_updated_props( &$video_playlist ) {

        // Trigger action so 3rd parties can deal with updated props.
        do_action( 'masvideos_video_playlist_object_updated_props', $video_playlist, $this->updated_props );

        // After handling, we can reset the props array.
        $this->updated_props = array();
    }

    /**
     * Clear any caches.
     *
     * @param MasVideos_Video_Playlist $video_playlist Video object.
     * @since 1.0.0
     */
    protected function clear_caches( &$video_playlist ) {
        masvideos_delete_video_playlist_transients( $video_playlist->get_id() );
        MasVideos_Cache_Helper::incr_cache_prefix( 'video_playlist_' . $video_playlist->get_id() );
    }

    /*
    |--------------------------------------------------------------------------
    | masvideos-video-playlist-functions.php methods
    |--------------------------------------------------------------------------
    */

    /**
     * Returns an array of video playlists.
     *
     * @param  array $args Args to pass to MasVideos_Video_Playlist_Query().
     * @return array|object
     * @see masvideos_get_video_playlists
     */
    public function get_video_playlists( $args = array() ) {
        $query = new MasVideos_Video_Playlist_Query( $args );
        return $query->get_video_playlists();
    }

    /**
     * Search video playlist data for a term and return ids.
     *
     * @param  string   $term Search term.
     * @param  bool     $all_statuses Should we search all statuses or limit to published.
     * @param  null|int $limit Limit returned results.
     * @since  1.0.0.
     * @return array of ids
     */
    public function search_video_playlists( $term, $all_statuses = false, $limit = null ) {
        global $wpdb;

        $post_types    = array( 'video_playlist' );
        $post_statuses = current_user_can( 'edit_private_video_playlists' ) ? array( 'private', 'publish' ) : array( 'publish' );
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
            "SELECT DISTINCT posts.ID as video_playlist_id, posts.post_parent as parent_id FROM {$wpdb->posts} posts
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

        $video_playlist_ids = wp_parse_id_list( array_merge( wp_list_pluck( $search_results, 'video_playlist_id' ), wp_list_pluck( $search_results, 'parent_id' ) ) );

        if ( is_numeric( $term ) ) {
            $post_id   = absint( $term );
            $post_type = get_post_type( $post_id );

            if ( 'video_playlist' === $post_type ) {
                $video_playlist_ids[] = $post_id;
            }

            $video_playlist_ids[] = wp_get_post_parent_id( $post_id );
        }

        return wp_parse_id_list( $video_playlist_ids );
    }

    /**
     * Get valid WP_Query args from a MasVideos_Video_Playlist_Query's query variables.
     *
     * @since 1.0.0
     * @param array $query_vars Query vars from a MasVideos_Video_Playlist_Query.
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

        return apply_filters( 'masvideos_video_playlist_data_store_cpt_get_video_playlists_query', $wp_query_args, $query_vars, $this );
    }

    /**
     * Query for Video Playlists matching specific criteria.
     *
     * @since 1.0.0
     *
     * @param array $query_vars Query vars from a MasVideos_Video_Playlist_Query.
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
            update_post_caches( $query->posts, array( 'video_playlist' ) );
        }

        $video_playlists = ( isset( $query_vars['return'] ) && 'ids' === $query_vars['return'] ) ? $query->posts : array_filter( array_map( 'masvideos_get_video_playlist', $query->posts ) );

        if ( isset( $query_vars['paginate'] ) && $query_vars['paginate'] ) {
            return (object) array(
                'video_playlists'   => $video_playlists,
                'total'             => $query->found_posts,
                'max_num_pages'     => $query->max_num_pages,
            );
        }

        return $video_playlists;
    }
}
