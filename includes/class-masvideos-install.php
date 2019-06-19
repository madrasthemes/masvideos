<?php
/**
 * Installation related functions and actions.
 *
 * @package MasVideos/Classes
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * MasVideos_Install Class.
 */
class MasVideos_Install {

    /**
     * DB updates and callbacks that need to be run per version.
     *
     * @var array
     */
    private static $db_updates = array(
        '1.0.0' => array(
            'masvideos_update_100_attributes',
            'masvideos_update_100_db_version',
        ),
    );

    /**
     * Hook in tabs.
     */
    public static function init() {
        add_action( 'init', array( __CLASS__, 'check_version' ), 5 );
        add_filter( 'wpmu_drop_tables', array( __CLASS__, 'wpmu_drop_tables' ) );
        add_filter( 'cron_schedules', array( __CLASS__, 'cron_schedules' ) );
    }

    /**
     * Check MasVideos version and run the updater is required.
     *
     * This check is done on all requests and runs if the versions do not match.
     */
    public static function check_version() {
        if ( ! defined( 'IFRAME_REQUEST' ) && version_compare( get_option( 'masvideos_version' ), MasVideos()->version, '<' ) ) {
            self::install();
            do_action( 'masvideos_updated' );
        }
    }

    /**
     * Install WC.
     */
    public static function install() {
        if ( ! is_blog_installed() ) {
            return;
        }

        // Check if we are not already running this routine.
        if ( 'yes' === get_transient( 'masvideos_installing' ) ) {
            return;
        }

        // If we made it till here nothing is running yet, lets set the transient now.
        set_transient( 'masvideos_installing', 'yes', MINUTE_IN_SECONDS * 10 );
        masvideos_maybe_define_constant( 'MASVIDEOS_INSTALLING', true );

        self::create_tables();
        self::create_roles();
        self::setup_environment();
        self::create_terms();
        self::maybe_enable_setup_wizard();
        self::update_version();
        self::maybe_update_db_version();

        delete_transient( 'masvideos_installing' );

        do_action( 'masvideos_flush_rewrite_rules' );
        do_action( 'masvideos_installed' );
    }

    /**
     * Setup WC environment - post types, taxonomies, endpoints.
     *
     * @since 3.2.0
     */
    private static function setup_environment() {
        MasVideos_Post_Types::register_post_types();
        MasVideos_Post_Types::register_taxonomies();
        MasVideos()->query->init_query_vars();
        MasVideos()->query->add_endpoints();
    }

    /**
     * Add the default terms for taxonomies and order statuses. Modify this at your own risk.
     */
    public static function create_terms() {
        $taxonomies = array(
            'episode_visibility' => array(
                'exclude-from-search',
                'exclude-from-catalog',
                'featured',
                'rated-1',
                'rated-2',
                'rated-3',
                'rated-4',
                'rated-5',
                'rated-6',
                'rated-7',
                'rated-8',
                'rated-9',
                'rated-10',
            ),
            'tv_show_visibility' => array(
                'exclude-from-search',
                'exclude-from-catalog',
                'featured',
                'rated-1',
                'rated-2',
                'rated-3',
                'rated-4',
                'rated-5',
                'rated-6',
                'rated-7',
                'rated-8',
                'rated-9',
                'rated-10',
            ),
            'video_visibility' => array(
                'exclude-from-search',
                'exclude-from-catalog',
                'featured',
                'rated-1',
                'rated-2',
                'rated-3',
                'rated-4',
                'rated-5',
                'rated-6',
                'rated-7',
                'rated-8',
                'rated-9',
                'rated-10',
            ),
            'movie_visibility' => array(
                'exclude-from-search',
                'exclude-from-catalog',
                'featured',
                'rated-1',
                'rated-2',
                'rated-3',
                'rated-4',
                'rated-5',
                'rated-6',
                'rated-7',
                'rated-8',
                'rated-9',
                'rated-10',
            ),
            'person_visibility' => array(
                'exclude-from-search',
                'exclude-from-catalog',
                'featured',
            ),
        );

        foreach ( $taxonomies as $taxonomy => $terms ) {
            foreach ( $terms as $term ) {
                if ( ! get_term_by( 'name', $term, $taxonomy ) ) { // @codingStandardsIgnoreLine.
                    wp_insert_term( $term, $taxonomy );
                }
            }
        }
    }

    /**
     * Update WC version to current.
     */
    private static function update_version() {
        delete_option( 'masvideos_version' );
        add_option( 'masvideos_version', MasVideos()->version );
    }

    /**
     * Is this a brand new WC install?
     *
     * @since 3.2.0
     * @return boolean
     */
    private static function is_new_install() {
        return is_null( get_option( 'masvideos_version', null ) ) && is_null( get_option( 'masvideos_db_version', null ) );
    }

    /**
     * See if we need the wizard or not.
     *
     * @since 3.2.0
     */
    private static function maybe_enable_setup_wizard() {
        if ( apply_filters( 'masvideos_enable_setup_wizard', self::is_new_install() ) ) {
            MasVideos_Admin_Notices::add_notice( 'install' );
            set_transient( '_masvideos_activation_redirect', 1, 30 );
        }
    }

    /**
     * Is a DB update needed?
     *
     * @since 3.2.0
     * @return boolean
     */
    private static function needs_db_update() {
        $current_db_version = get_option( 'masvideos_db_version', null );
        $updates            = self::get_db_update_callbacks();

        return ! is_null( $current_db_version ) && version_compare( $current_db_version, max( array_keys( $updates ) ), '<' );
    }

    /**
     * See if we need to show or run database updates during install.
     *
     * @since 3.2.0
     */
    private static function maybe_update_db_version() {
        if ( self::needs_db_update() ) {
            self::update_db_version();
        } else {
            self::update_db_version();
        }
    }

    /**
     * Get list of DB update callbacks.
     *
     * @since  3.0.0
     * @return array
     */
    public static function get_db_update_callbacks() {
        return self::$db_updates;
    }

    /**
     * Update DB version to current.
     *
     * @param string|null $version New MasVideos DB version or null.
     */
    public static function update_db_version( $version = null ) {
        delete_option( 'masvideos_db_version' );
        add_option( 'masvideos_db_version', is_null( $version ) ? MasVideos()->version : $version );
    }

    /**
     * Add more cron schedules.
     *
     * @param  array $schedules List of WP scheduled cron jobs.
     * @return array
     */
    public static function cron_schedules( $schedules ) {
        $schedules['monthly'] = array(
            'interval' => 2635200,
            'display'  => __( 'Monthly', 'masvideos' ),
        );
        return $schedules;
    }

    /**
     * Create pages that the plugin relies on, storing page IDs in variables.
     */
    public static function create_pages() {
        include_once dirname( __FILE__ ) . '/admin/masvideos-admin-functions.php';

        $pages = apply_filters(
            'masvideos_create_pages', array(
                'myaccount'      => array(
                    'name'    => _x( 'my-account', 'Page slug', 'masvideos' ),
                    'title'   => _x( 'My Account', 'Page title', 'masvideos' ),
                    'content' => '<!-- wp:shortcode -->[' . apply_filters( 'masvideos_my_account_shortcode_tag', 'mas_my_account' ) . ']<!-- /wp:shortcode -->',
                ),
                'upload_video'   => array(
                    'name'    => _x( 'upload-video', 'Page slug', 'masvideos' ),
                    'title'   => _x( 'Upload Video', 'Page title', 'masvideos' ),
                    'content' => '<!-- wp:shortcode -->[' . apply_filters( 'masvideos_upload_video_shortcode_tag', 'mas_upload_video' ) . ']<!-- /wp:shortcode -->',
                ),
                'movies'      => array(
                    'name'    => _x( 'movies', 'Page slug', 'masvideos' ),
                    'title'   => _x( 'Movies', 'Page title', 'masvideos' ),
                    'content' => '',
                ),
                'tv_shows'      => array(
                    'name'    => _x( 'tv-shows', 'Page slug', 'masvideos' ),
                    'title'   => _x( 'TV Shows', 'Page title', 'masvideos' ),
                    'content' => '',
                ),
                'videos'        => array(
                    'name'    => _x( 'videos', 'Page slug', 'masvideos' ),
                    'title'   => _x( 'Videos', 'Page title', 'masvideos' ),
                    'content' => '',
                ),
                'persons'       => array(
                    'name'    => _x( 'persons', 'Page slug', 'masvideos' ),
                    'title'   => _x( 'Persons', 'Page title', 'masvideos' ),
                    'content' => '',
                ),
            )
        );

        foreach ( $pages as $key => $page ) {
            masvideos_create_page( esc_sql( $page['name'] ), 'masvideos_' . $key . '_page_id', $page['title'], $page['content'], ! empty( $page['parent'] ) ? masvideos_get_page_id( $page['parent'] ) : '' );
        }
    }

    /**
     * Set up the database tables which the plugin needs to function.
     *
     * Tables:
     *      masvideos_attribute_taxonomies - Table for storing attribute taxonomies - these are user defined
     */
    private static function create_tables() {
        global $wpdb;

        $wpdb->hide_errors();

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        dbDelta( self::get_schema() );
    }

    /**
     * Get Table schema.
     *
     * A note on indexes; Indexes have a maximum size of 767 bytes. Historically, we haven't need to be concerned about that.
     * As of WordPress 4.2, however, we moved to utf8mb4, which uses 4 bytes per character. This means that an index which
     * used to have room for floor(767/3) = 255 characters, now only has room for floor(767/4) = 191 characters.
     *
     * Changing indexes may cause duplicate index notices in logs due to https://core.trac.wordpress.org/ticket/34870 but dropping
     * indexes first causes too much load on some servers/larger DB.
     *
     * When adding or removing a table, make sure to update the list of tables in MasVideos_Install::get_tables().
     *
     * @return string
     */
    private static function get_schema() {
        global $wpdb;

        $collate = '';

        if ( $wpdb->has_cap( 'collation' ) ) {
            $collate = $wpdb->get_charset_collate();
        }

        $tables = "
CREATE TABLE {$wpdb->prefix}masvideos_attribute_taxonomies (
  attribute_id BIGINT UNSIGNED NOT NULL auto_increment,
  attribute_name varchar(200) NOT NULL,
  attribute_label varchar(200) NULL,
  attribute_type varchar(20) NOT NULL,
  attribute_orderby varchar(20) NOT NULL,
  attribute_public int(1) NOT NULL DEFAULT 1,
  post_type varchar(20) NOT NULL,
  PRIMARY KEY  (attribute_id),
  KEY attribute_name (attribute_name(20))
) $collate;
        ";

        return $tables;
    }

    /**
     * Return a list of MasVideos tables. Used to make sure all WC tables are dropped when uninstalling the plugin
     * in a single site or multi site environment.
     *
     * @return array WC tables.
     */
    public static function get_tables() {
        global $wpdb;

        $tables = array(
            "{$wpdb->prefix}masvideos_attribute_taxonomies",
        );

        /**
         * Filter the list of known MasVideos tables.
         *
         * If MasVideos plugins need to add new tables, they can inject them here.
         *
         * @param array $tables An array of MasVideos-specific database table names.
         */
        $tables = apply_filters( 'masvideos_install_get_tables', $tables );

        return $tables;
    }

    /**
     * Drop MasVideos tables.
     *
     * @return void
     */
    public static function drop_tables() {
        global $wpdb;

        $tables = self::get_tables();

        foreach ( $tables as $table ) {
            $wpdb->query( "DROP TABLE IF EXISTS {$table}" ); // phpcs:ignore WordPress.WP.PreparedSQL.NotPrepared
        }
    }

    /**
     * Uninstall tables when MU blog is deleted.
     *
     * @param  array $tables List of tables that will be deleted by WP.
     * @return string[]
     */
    public static function wpmu_drop_tables( $tables ) {
        return array_merge( $tables, self::get_tables() );
    }

    /**
     * Create roles and capabilities.
     */
    public static function create_roles() {
        global $wp_roles;

        if ( ! class_exists( 'WP_Roles' ) ) {
            return;
        }

        if ( ! isset( $wp_roles ) ) {
            $wp_roles = new WP_Roles(); // @codingStandardsIgnoreLine
        }

        $capabilities = self::get_core_capabilities();

        foreach ( $capabilities as $cap_group ) {
            foreach ( $cap_group as $cap ) {
                $wp_roles->add_cap( 'administrator', $cap );
            }
        }

        $video_contributor_capabilities = apply_filters( 'masvideos_video_contributor_capabilities', array(
            'edit_video',
            'read_video',
            'delete_video',
            'edit_videos',
            'delete_videos',
            'manage_video_terms',
            'assign_video_terms',
            'upload_files',
        ) );

        foreach ( $video_contributor_capabilities as $cap ) {
            $wp_roles->add_cap( 'contributor', $cap );
        }
    }

    /**
     * Get capabilities for MasVideos - these are assigned to admin/shop manager during installation or reset.
     *
     * @return array
     */
    private static function get_core_capabilities() {
        $capabilities = array();

        $capabilities['core'] = array(
            'manage_masvideos',
        );

        $capability_types = array( 'episode', 'tv_show', 'tv_show_playlist', 'video', 'video_playlist', 'movie', 'movie_playlist', 'person' );

        foreach ( $capability_types as $capability_type ) {

            $capabilities[ $capability_type ] = array(
                // Post type.
                "edit_{$capability_type}",
                "read_{$capability_type}",
                "delete_{$capability_type}",
                "edit_{$capability_type}s",
                "edit_others_{$capability_type}s",
                "publish_{$capability_type}s",
                "read_private_{$capability_type}s",
                "delete_{$capability_type}s",
                "delete_private_{$capability_type}s",
                "delete_published_{$capability_type}s",
                "delete_others_{$capability_type}s",
                "edit_private_{$capability_type}s",
                "edit_published_{$capability_type}s",

                // Terms.
                "manage_{$capability_type}_terms",
                "edit_{$capability_type}_terms",
                "delete_{$capability_type}_terms",
                "assign_{$capability_type}_terms",
            );
        }

        return $capabilities;
    }

    /**
     * Remove MasVideos roles.
     */
    public static function remove_roles() {
        global $wp_roles;

        if ( ! class_exists( 'WP_Roles' ) ) {
            return;
        }

        if ( ! isset( $wp_roles ) ) {
            $wp_roles = new WP_Roles(); // @codingStandardsIgnoreLine
        }

        $capabilities = self::get_core_capabilities();

        foreach ( $capabilities as $cap_group ) {
            foreach ( $cap_group as $cap ) {
                $wp_roles->remove_cap( 'administrator', $cap );
            }
        }
    }
}

MasVideos_Install::init();
