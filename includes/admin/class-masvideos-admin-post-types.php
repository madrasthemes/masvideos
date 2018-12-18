<?php
/**
 * Post Types Admin
 *
 * @category Admin
 * @package  MasVideos/admin
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( class_exists( 'MasVideos_Admin_Post_Types', false ) ) {
    new MasVideos_Admin_Post_Types();
    return;
}

/**
 * MasVideos_Admin_Post_Types Class.
 *
 * Handles the edit posts views and some functionality on the edit post screen for WC post types.
 */
class MasVideos_Admin_Post_Types {

    /**
     * Constructor.
     */
    public function __construct() {
        include_once dirname( __FILE__ ) . '/class-masvideos-admin-meta-boxes.php';
    }
}

new MasVideos_Admin_Post_Types();
