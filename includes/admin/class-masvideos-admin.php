<?php
/**
 * MadrasThemes Admin
 *
 * @class    Mas_Videos_Admin
 * @author   MadrasThemes
 * @category Admin
 * @package  MadrasThemes/Admin
 * @version  2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Mas_Videos_Admin class.
 */
class Mas_Videos_Admin {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'init', array( $this, 'includes' ) );
        add_action( 'admin_init', array( $this, 'buffer' ), 1 );
        add_action( 'admin_init', array( $this, 'prevent_admin_access' ) );
        add_action( 'wp_ajax_setup_wizard_check_jetpack', array( $this, 'setup_wizard_check_jetpack' ) );
    }

    /**
     * Output buffering allows admin screens to make redirects later on.
     */
    public function buffer() {
        ob_start();
    }

    /**
     * Include any classes we need within admin.
     */
    public function includes() {
        include_once dirname( __FILE__ ) . '/class-masvideos-admin-attributes.php';
        include_once dirname( __FILE__ ) . '/class-masvideos-admin-menus.php';
    }

    /**
     * Prevent any user who cannot 'edit_posts' (subscribers, customers etc) from accessing admin.
     */
    public function prevent_admin_access() {
        $prevent_access = false;

        if ( 'yes' === get_option( 'masvideos_lock_down_admin', 'yes' ) && ! is_ajax() && basename( $_SERVER['SCRIPT_FILENAME'] ) !== 'admin-post.php' ) {
            $has_cap     = false;
            $access_caps = array( 'edit_posts', 'manage_masvideos', 'view_admin_dashboard' );

            foreach ( $access_caps as $access_cap ) {
                if ( current_user_can( $access_cap ) ) {
                    $has_cap = true;
                    break;
                }
            }

            if ( ! $has_cap ) {
                $prevent_access = true;
            }
        }

        if ( apply_filters( 'masvideos_prevent_admin_access', $prevent_access ) ) {
            wp_safe_redirect( site_url() );
            exit;
        }
    }

    /**
     * Check on a Jetpack install queued by the Setup Wizard.
     *
     * See: Mas_Videos_Admin_Setup_Wizard::install_jetpack()
     */
    public function setup_wizard_check_jetpack() {
        $jetpack_active = class_exists( 'Jetpack' );

        wp_send_json_success(
            array(
                'is_active' => $jetpack_active ? 'yes' : 'no',
            )
        );
    }
}

return new Mas_Videos_Admin();
