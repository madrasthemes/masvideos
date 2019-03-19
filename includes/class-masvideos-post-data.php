<?php
/**
 * Post Data
 *
 * Standardises certain post data on save.
 *
 * @package MasVideos/Classes/Data
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Post data class.
 */
class MasVideos_Post_Data {

    /**
     * Editing term.
     *
     * @var object
     */
    private static $editing_term = null;

    /**
     * Hook in methods.
     */
    public static function init() {
        add_action( 'set_object_terms', array( __CLASS__, 'set_object_terms' ), 10, 6 );

        add_action( 'transition_post_status', array( __CLASS__, 'transition_post_status' ), 10, 3 );
        add_action( 'masvideos_episode_set_visibility', array( __CLASS__, 'delete_episode_query_transients' ) );
        add_action( 'masvideos_tv_show_set_visibility', array( __CLASS__, 'delete_tv_show_query_transients' ) );
        add_action( 'masvideos_tv_show_playlist_set_visibility', array( __CLASS__, 'delete_tv_show_playlist_query_transients' ) );
        add_action( 'masvideos_video_set_visibility', array( __CLASS__, 'delete_video_query_transients' ) );
        add_action( 'masvideos_video_playlist_set_visibility', array( __CLASS__, 'delete_video_playlist_query_transients' ) );
        add_action( 'masvideos_movie_set_visibility', array( __CLASS__, 'delete_movie_query_transients' ) );
        add_action( 'masvideos_movie_playlist_set_visibility', array( __CLASS__, 'delete_movie_playlist_query_transients' ) );

        // Meta cache flushing.
        add_action( 'updated_post_meta', array( __CLASS__, 'flush_object_meta_cache' ), 10, 4 );
        add_action( 'updated_order_item_meta', array( __CLASS__, 'flush_object_meta_cache' ), 10, 4 );
    }

    /**
     * Delete transients when terms are set.
     *
     * @param int    $object_id  Object ID.
     * @param mixed  $terms      An array of object terms.
     * @param array  $tt_ids     An array of term taxonomy IDs.
     * @param string $taxonomy   Taxonomy slug.
     * @param mixed  $append     Whether to append new terms to the old terms.
     * @param array  $old_tt_ids Old array of term taxonomy IDs.
     */
    public static function set_object_terms( $object_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids ) {
        foreach ( array_merge( $tt_ids, $old_tt_ids ) as $id ) {
            delete_transient( 'masvideos_ln_count_' . md5( sanitize_key( $taxonomy ) . sanitize_key( $id ) ) );
        }
        if ( in_array( get_post_type( $object_id ), array( 'episode', 'tv_show', 'tv_show_playlist', 'video', 'video_playlist', 'movie', 'movie_playlist' ), true ) ) {
            switch ( get_post_type( $object_id ) ) {
                case 'episode':
                    self::delete_episode_query_transients();
                    break;
                
                case 'tv_show':
                    self::delete_tv_show_query_transients();
                    break;

                case 'tv_show_playlist':
                    self::delete_tv_show_playlist_query_transients();
                    break;

                case 'video':
                    self::delete_video_query_transients();
                    break;

                case 'video_playlist':
                    self::delete_video_playlist_query_transients();
                    break;

                case 'movie':
                    self::delete_movie_query_transients();
                    break;

                case 'movie_playlist':
                    self::delete_movie_playlist_query_transients();
                    break;

                default:
                    self::delete_movie_query_transients();
                    break;
            }
        }
    }

    /**
     * When a post status changes.
     *
     * @param string  $new_status New status.
     * @param string  $old_status Old status.
     * @param WP_Post $post       Post data.
     */
    public static function transition_post_status( $new_status, $old_status, $post ) {
        if ( ( 'publish' === $new_status || 'publish' === $old_status ) && in_array( $post->post_type, array( 'episode', 'tv_show', 'tv_show_playlist', 'video', 'video_playlist', 'movie', 'movie_playlist' ), true ) ) {
            switch ( $post->post_type ) {
                case 'episode':
                    self::delete_episode_query_transients();
                    break;
                
                case 'tv_show':
                    self::delete_tv_show_query_transients();
                    break;

                case 'tv_show_playlist':
                    self::delete_tv_show_playlist_query_transients();
                    break;

                case 'video':
                    self::delete_video_query_transients();
                    break;

                case 'video_playlist':
                    self::delete_video_playlist_query_transients();
                    break;

                case 'movie':
                    self::delete_movie_query_transients();
                    break;

                case 'movie_playlist':
                    self::delete_movie_playlist_query_transients();
                    break;

                default:
                    self::delete_movie_query_transients();
                    break;
            }
        }
    }

    /**
     * Delete episode view transients when needed e.g. when post status changes, or visibility/stock status is modified.
     */
    public static function delete_episode_query_transients() {
        // Increments the transient version to invalidate cache.
        MasVideos_Cache_Helper::get_transient_version( 'episode_query', true );

        // If not using an external caching system, we can clear the transients out manually and avoid filling our DB.
        if ( ! wp_using_ext_object_cache() ) {
            global $wpdb;

            $wpdb->query(
                "
                DELETE FROM `$wpdb->options`
                WHERE `option_name` LIKE ('\_transient\_masvideos\_uf\_pid\_%')
                OR `option_name` LIKE ('\_transient\_timeout\_masvideos\_uf\_pid\_%')
                OR `option_name` LIKE ('\_transient\_masvideos\_episodes\_will\_display\_%')
                OR `option_name` LIKE ('\_transient\_timeout\_masvideos\_episodes\_will\_display\_%')
            "
            );
        }
    }

    /**
     * Delete tv show view transients when needed e.g. when post status changes, or visibility/stock status is modified.
     */
    public static function delete_tv_show_query_transients() {
        // Increments the transient version to invalidate cache.
        MasVideos_Cache_Helper::get_transient_version( 'tv_show_query', true );

        // If not using an external caching system, we can clear the transients out manually and avoid filling our DB.
        if ( ! wp_using_ext_object_cache() ) {
            global $wpdb;

            $wpdb->query(
                "
                DELETE FROM `$wpdb->options`
                WHERE `option_name` LIKE ('\_transient\_masvideos\_uf\_pid\_%')
                OR `option_name` LIKE ('\_transient\_timeout\_masvideos\_uf\_pid\_%')
                OR `option_name` LIKE ('\_transient\_masvideos\_tv_shows\_will\_display\_%')
                OR `option_name` LIKE ('\_transient\_timeout\_masvideos\_tv_shows\_will\_display\_%')
            "
            );
        }
    }

    /**
     * Delete tv show playlist view transients when needed e.g. when post status changes, or visibility/stock status is modified.
     */
    public static function delete_tv_show_playlist_query_transients() {
        // Increments the transient version to invalidate cache.
        MasVideos_Cache_Helper::get_transient_version( 'tv_show_playlist_query', true );

        // If not using an external caching system, we can clear the transients out manually and avoid filling our DB.
        if ( ! wp_using_ext_object_cache() ) {
            global $wpdb;

            $wpdb->query(
                "
                DELETE FROM `$wpdb->options`
                WHERE `option_name` LIKE ('\_transient\_masvideos\_uf\_pid\_%')
                OR `option_name` LIKE ('\_transient\_timeout\_masvideos\_uf\_pid\_%')
                OR `option_name` LIKE ('\_transient\_masvideos\_tv_show_playlists\_will\_display\_%')
                OR `option_name` LIKE ('\_transient\_timeout\_masvideos\_tv_show_playlists\_will\_display\_%')
            "
            );
        }
    }

    /**
     * Delete video view transients when needed e.g. when post status changes, or visibility/stock status is modified.
     */
    public static function delete_video_query_transients() {
        // Increments the transient version to invalidate cache.
        MasVideos_Cache_Helper::get_transient_version( 'video_query', true );

        // If not using an external caching system, we can clear the transients out manually and avoid filling our DB.
        if ( ! wp_using_ext_object_cache() ) {
            global $wpdb;

            $wpdb->query(
                "
                DELETE FROM `$wpdb->options`
                WHERE `option_name` LIKE ('\_transient\_masvideos\_uf\_pid\_%')
                OR `option_name` LIKE ('\_transient\_timeout\_masvideos\_uf\_pid\_%')
                OR `option_name` LIKE ('\_transient\_masvideos\_videos\_will\_display\_%')
                OR `option_name` LIKE ('\_transient\_timeout\_masvideos\_videos\_will\_display\_%')
            "
            );
        }
    }

    /**
     * Delete video playlist view transients when needed e.g. when post status changes, or visibility/stock status is modified.
     */
    public static function delete_video_playlist_query_transients() {
        // Increments the transient version to invalidate cache.
        MasVideos_Cache_Helper::get_transient_version( 'video_playlist_query', true );

        // If not using an external caching system, we can clear the transients out manually and avoid filling our DB.
        if ( ! wp_using_ext_object_cache() ) {
            global $wpdb;

            $wpdb->query(
                "
                DELETE FROM `$wpdb->options`
                WHERE `option_name` LIKE ('\_transient\_masvideos\_uf\_pid\_%')
                OR `option_name` LIKE ('\_transient\_timeout\_masvideos\_uf\_pid\_%')
                OR `option_name` LIKE ('\_transient\_masvideos\_video_playlists\_will\_display\_%')
                OR `option_name` LIKE ('\_transient\_timeout\_masvideos\_video_playlists\_will\_display\_%')
            "
            );
        }
    }

    /**
     * Delete movie view transients when needed e.g. when post status changes, or visibility/stock status is modified.
     */
    public static function delete_movie_query_transients() {
        // Increments the transient version to invalidate cache.
        MasVideos_Cache_Helper::get_transient_version( 'movie_query', true );

        // If not using an external caching system, we can clear the transients out manually and avoid filling our DB.
        if ( ! wp_using_ext_object_cache() ) {
            global $wpdb;

            $wpdb->query(
                "
                DELETE FROM `$wpdb->options`
                WHERE `option_name` LIKE ('\_transient\_masvideos\_uf\_pid\_%')
                OR `option_name` LIKE ('\_transient\_timeout\_masvideos\_uf\_pid\_%')
                OR `option_name` LIKE ('\_transient\_masvideos\_movies\_will\_display\_%')
                OR `option_name` LIKE ('\_transient\_timeout\_masvideos\_movies\_will\_display\_%')
            "
            );
        }
    }

    /**
     * Delete movie playlist view transients when needed e.g. when post status changes, or visibility/stock status is modified.
     */
    public static function delete_movie_playlist_query_transients() {
        // Increments the transient version to invalidate cache.
        MasVideos_Cache_Helper::get_transient_version( 'movie_playlist_query', true );

        // If not using an external caching system, we can clear the transients out manually and avoid filling our DB.
        if ( ! wp_using_ext_object_cache() ) {
            global $wpdb;

            $wpdb->query(
                "
                DELETE FROM `$wpdb->options`
                WHERE `option_name` LIKE ('\_transient\_masvideos\_uf\_pid\_%')
                OR `option_name` LIKE ('\_transient\_timeout\_masvideos\_uf\_pid\_%')
                OR `option_name` LIKE ('\_transient\_masvideos\_movie_playlists\_will\_display\_%')
                OR `option_name` LIKE ('\_transient\_timeout\_masvideos\_movie_playlists\_will\_display\_%')
            "
            );
        }
    }

    /**
     * Flush meta cache for CRUD objects on direct update.
     *
     * @param  int    $meta_id    Meta ID.
     * @param  int    $object_id  Object ID.
     * @param  string $meta_key   Meta key.
     * @param  string $meta_value Meta value.
     */
    public static function flush_object_meta_cache( $meta_id, $object_id, $meta_key, $meta_value ) {
        MasVideos_Cache_Helper::incr_cache_prefix( 'object_' . $object_id );
    }
}

MasVideos_Post_Data::init();
