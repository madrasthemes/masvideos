<?php
/**
 * Display notices in admin
 *
 * @package MasVideos\Admin
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * MasVideos_Admin_Notices Class.
 */
class MasVideos_Admin_Notices {

    /**
     * Stores notices.
     *
     * @var array
     */
    private static $notices = array();

    /**
     * Array of notices - name => callback.
     *
     * @var array
     */
    private static $core_notices = array(
        'install'                 => 'install_notice',
    );

    /**
     * Constructor.
     */
    public static function init() {
        self::$notices = get_option( 'masvideos_admin_notices', array() );

        add_action( 'wp_loaded', array( __CLASS__, 'hide_notices' ) );
        add_action( 'shutdown', array( __CLASS__, 'store_notices' ) );

        if ( current_user_can( 'manage_masvideos' ) ) {
            add_action( 'admin_print_styles', array( __CLASS__, 'add_notices' ) );
        }
    }

    /**
     * Store notices to DB
     */
    public static function store_notices() {
        update_option( 'masvideos_admin_notices', self::get_notices() );
    }

    /**
     * Get notices
     *
     * @return array
     */
    public static function get_notices() {
        return self::$notices;
    }

    /**
     * Remove all notices.
     */
    public static function remove_all_notices() {
        self::$notices = array();
    }

    /**
     * Show a notice.
     *
     * @param string $name Notice name.
     */
    public static function add_notice( $name ) {
        self::$notices = array_unique( array_merge( self::get_notices(), array( $name ) ) );
    }

    /**
     * Remove a notice from being displayed.
     *
     * @param string $name Notice name.
     */
    public static function remove_notice( $name ) {
        self::$notices = array_diff( self::get_notices(), array( $name ) );
        delete_option( 'masvideos_admin_notice_' . $name );
    }

    /**
     * See if a notice is being shown.
     *
     * @param string $name Notice name.
     *
     * @return boolean
     */
    public static function has_notice( $name ) {
        return in_array( $name, self::get_notices(), true );
    }

    /**
     * Hide a notice if the GET variable is set.
     */
    public static function hide_notices() {
        if ( isset( $_GET['masvideos-hide-notice'] ) && isset( $_GET['_masvideos_notice_nonce'] ) ) { // WPCS: input var ok, CSRF ok.
            if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_masvideos_notice_nonce'] ) ), 'masvideos_hide_notices_nonce' ) ) { // WPCS: input var ok, CSRF ok.
                wp_die( esc_html__( 'Action failed. Please refresh the page and retry.', 'masvideos' ) );
            }

            if ( ! current_user_can( 'manage_masvideos' ) ) {
                wp_die( esc_html__( 'You don&#8217;t have permission to do this.', 'masvideos' ) );
            }

            $hide_notice = sanitize_text_field( wp_unslash( $_GET['masvideos-hide-notice'] ) ); // WPCS: input var ok, CSRF ok.

            self::remove_notice( $hide_notice );

            update_user_meta( get_current_user_id(), 'dismissed_' . $hide_notice . '_notice', true );

            do_action( 'masvideos_hide_' . $hide_notice . '_notice' );
        }
    }

    /**
     * Add notices + styles if needed.
     */
    public static function add_notices() {
        $notices = self::get_notices();

        if ( empty( $notices ) ) {
            return;
        }

        $screen          = get_current_screen();
        $screen_id       = $screen ? $screen->id : '';
        $show_on_screens = array(
            'dashboard',
            'plugins',
        );

        // Notices should only show on MasVideos screens, the main dashboard, and on the plugins screen.
        if ( ! in_array( $screen_id, masvideos_get_screen_ids(), true ) && ! in_array( $screen_id, $show_on_screens, true ) ) {
            return;
        }

        wp_enqueue_style( 'masvideos-activation', plugins_url( '/assets/css/activation.css', MASVIDEOS_PLUGIN_FILE ), array(), MASVIDEOS_VERSION );

        // Add RTL support.
        wp_style_add_data( 'masvideos-activation', 'rtl', 'replace' );

        foreach ( $notices as $notice ) {
            if ( ! empty( self::$core_notices[ $notice ] ) && apply_filters( 'masvideos_show_admin_notice', true, $notice ) ) {
                add_action( 'admin_notices', array( __CLASS__, self::$core_notices[ $notice ] ) );
            } else {
                add_action( 'admin_notices', array( __CLASS__, 'output_custom_notices' ) );
            }
        }
    }

    /**
     * Add a custom notice.
     *
     * @param string $name        Notice name.
     * @param string $notice_html Notice HTML.
     */
    public static function add_custom_notice( $name, $notice_html ) {
        self::add_notice( $name );
        update_option( 'masvideos_admin_notice_' . $name, wp_kses_post( $notice_html ) );
    }

    /**
     * Output any stored custom notices.
     */
    public static function output_custom_notices() {
        $notices = self::get_notices();

        if ( ! empty( $notices ) ) {
            foreach ( $notices as $notice ) {
                if ( empty( self::$core_notices[ $notice ] ) ) {
                    $notice_html = get_option( 'masvideos_admin_notice_' . $notice );

                    if ( $notice_html ) {
                        include dirname( __FILE__ ) . '/views/html-notice-custom.php';
                    }
                }
            }
        }
    }

    /**
     * If we have just installed, show a message with the install pages button.
     */
    public static function install_notice() {
        include dirname( __FILE__ ) . '/views/html-notice-install.php';
    }
}

MasVideos_Admin_Notices::init();
