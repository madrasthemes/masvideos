<?php
/**
 * MasVideos_Cache_Helper class.
 *
 * @package MasVideos/Classes
 */

defined( 'ABSPATH' ) || exit;

/**
 * MasVideos_Cache_Helper.
 */
class MasVideos_Cache_Helper {

    /**
     * Hook in methods.
     */
    public static function init() {
        add_action( 'delete_version_transients', array( __CLASS__, 'delete_version_transients' ) );
        add_action( 'wp', array( __CLASS__, 'prevent_caching' ) );
        add_action( 'clean_term_cache', array( __CLASS__, 'clean_term_cache' ), 10, 2 );
        add_action( 'edit_terms', array( __CLASS__, 'clean_term_cache' ), 10, 2 );
    }

    /**
     * Get prefix for use with wp_cache_set. Allows all cache in a group to be invalidated at once.
     *
     * @param  string $group Group of cache to get.
     * @return string
     */
    public static function get_cache_prefix( $group ) {
        // Get cache key - uses cache key masvideos_orders_cache_prefix to invalidate when needed.
        $prefix = wp_cache_get( 'masvideos_' . $group . '_cache_prefix', $group );

        if ( false === $prefix ) {
            $prefix = 1;
            wp_cache_set( 'masvideos_' . $group . '_cache_prefix', $prefix, $group );
        }

        return 'masvideos_cache_' . $prefix . '_';
    }

    /**
     * Increment group cache prefix (invalidates cache).
     *
     * @param string $group Group of cache to clear.
     */
    public static function incr_cache_prefix( $group ) {
        wp_cache_incr( 'masvideos_' . $group . '_cache_prefix', 1, $group );
    }

    /**
     * Prevent caching on certain pages
     */
    public static function prevent_caching() {
        if ( ! is_blog_installed() ) {
            return;
        }
        $page_ids = array_filter( array() );

        if ( ! empty( $page_ids ) && is_page( $page_ids ) ) {
            self::set_nocache_constants();
            nocache_headers();
        }
    }

    /**
     * Get transient version.
     *
     * When using transients with unpredictable names, e.g. those containing an md5
     * hash in the name, we need a way to invalidate them all at once.
     *
     * When using default WP transients we're able to do this with a DB query to
     * delete transients manually.
     *
     * With external cache however, this isn't possible. Instead, this function is used
     * to append a unique string (based on time()) to each transient. When transients
     * are invalidated, the transient version will increment and data will be regenerated.
     *
     * Raised in issue https://github.com/masvideos/masvideos/issues/5777.
     * Adapted from ideas in http://tollmanz.com/invalidation-schemes/.
     *
     * @param  string  $group   Name for the group of transients we need to invalidate.
     * @param  boolean $refresh true to force a new version.
     * @return string transient version based on time(), 10 digits.
     */
    public static function get_transient_version( $group, $refresh = false ) {
        $transient_name  = $group . '-transient-version';
        $transient_value = get_transient( $transient_name );
        $transient_value = strval( $transient_value ? $transient_value : '' );

        if ( '' === $transient_value || true === $refresh ) {
            $old_transient_value = $transient_value;
            $transient_value     = (string) time();

            if ( $old_transient_value === $transient_value ) {
                // Time did not change but transient needs flushing now.
                self::delete_version_transients( $transient_value );
            } else {
                self::queue_delete_version_transients( $transient_value );
            }

            set_transient( $transient_name, $transient_value );
        }
        return $transient_value;
    }

    /**
     * Queues a cleanup event for version transients.
     *
     * @param string $version Version of the transient to remove.
     */
    protected static function queue_delete_version_transients( $version = '' ) {
        if ( ! wp_using_ext_object_cache() && ! empty( $version ) ) {
            wp_schedule_single_event( time() + 30, 'delete_version_transients', array( $version ) );
        }
    }

    /**
     * When the transient version increases, this is used to remove all past transients to avoid filling the DB.
     *
     * Note; this only works on transients appended with the transient version, and when object caching is not being used.
     *
     * @since 1.0.0
     * @param string $version Version of the transient to remove.
     */
    public static function delete_version_transients( $version = '' ) {
        if ( ! wp_using_ext_object_cache() && ! empty( $version ) ) {
            global $wpdb;

            $limit = apply_filters( 'masvideos_delete_version_transients_limit', 1000 );

            if ( ! $limit ) {
                return;
            }

            $affected = $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s LIMIT %d;", '\_transient\_%' . $version, $limit ) ); // WPCS: cache ok, db call ok.

            // If affected rows is equal to limit, there are more rows to delete. Delete in 30 secs.
            if ( $affected === $limit ) {
                self::queue_delete_version_transients( $version );
            }
        }
    }

    /**
     * Set constants to prevent caching by some plugins.
     *
     * @param  mixed $return Value to return. Previously hooked into a filter.
     * @return mixed
     */
    public static function set_nocache_constants( $return = true ) {
        masvideos_maybe_define_constant( 'DONOTCACHEPAGE', true );
        masvideos_maybe_define_constant( 'DONOTCACHEOBJECT', true );
        masvideos_maybe_define_constant( 'DONOTCACHEDB', true );
        return $return;
    }

    /**
     * Clean term caches added by MasVideos.
     *
     * @since 1.0.0
     * @param array|int $ids Array of ids or single ID to clear cache for.
     * @param string    $taxonomy Taxonomy name.
     */
    public static function clean_term_cache( $ids, $taxonomy ) {
        if ( 'video_cat' === $taxonomy || 'movie_genre' === $taxonomy ) {
            $ids = is_array( $ids ) ? $ids : array( $ids );

            $clear_ids = array( 0 );

            foreach ( $ids as $id ) {
                $clear_ids[] = $id;
                $clear_ids   = array_merge( $clear_ids, get_ancestors( $id, $taxonomy, 'taxonomy' ) );
            }

            $clear_ids = array_unique( $clear_ids );

            foreach ( $clear_ids as $id ) {
                wp_cache_delete( 'masvideos-' . $taxonomy . '-hierarchy-' . $id, $taxonomy );
            }
        }
    }
}

MasVideos_Cache_Helper::init();
