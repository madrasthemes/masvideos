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
        if ( isset( $_GET['masvideos-setup'] ) && $_GET['masvideos-setup'] ) { // WPCS: CSRF ok, input var ok.
            // We've made it! Don't prompt the user to run the wizard again.
            MasVideos_Admin_Notices::remove_notice( 'install' );

            // Install Pages
            MasVideos_Install::create_pages();
            
            // Redirect to dashboard
            wp_redirect( esc_url_raw( admin_url() ) );
            exit;
        }
    }
}

new MasVideos_Admin_Setup_Wizard();
