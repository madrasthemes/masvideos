<?php
/**
 * Setup Wizard Class
 *
 * Takes new users through some basic steps to setup their store.
 *
 * @package     MasVideos/Admin
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * MasVideos_Admin_Setup_Wizard class.
 */
class MasVideos_Admin_Setup_Wizard {

    /**
     * Hook in tabs.
     */
    public function __construct() {
        if ( apply_filters( 'masvideos_enable_setup_wizard', true ) && current_user_can( 'manage_masvideos' ) ) {
            add_action( 'admin_init', array( $this, 'setup_wizard' ) );
        }
    }

    /**
     * Show the setup wizard.
     */
    public function setup_wizard() {
        if ( isset( $_GET['masvideos-setup'] ) && $_GET['masvideos-setup'] ) {
            // This entry point creates pages and writes options, so it must be protected against
            // CSRF and re-checked for capability (MV-15). Both the nonce and the capability are
            // required before any state change; a GET without a valid nonce does nothing.
            if ( ! current_user_can( 'manage_masvideos' ) ) {
                return;
            }

            check_admin_referer( 'masvideos-setup' );

            // We've made it! Don't prompt the user to run the wizard again.
            MasVideos_Admin_Notices::remove_notice( 'install' );

            // Install Pages
            MasVideos_Install::create_pages();

            // Redirect to dashboard
            wp_safe_redirect( esc_url_raw( admin_url() ) );
            exit;
        }
    }
}

new MasVideos_Admin_Setup_Wizard();
