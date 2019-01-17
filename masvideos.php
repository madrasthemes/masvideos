<?php
/**
 * Plugin Name: MAS Videos
 * Plugin URI: https://madrasthemes.com/masvideos
 * Description: This plugins helps to run videos, movies and series in your site.
 * Version: 0.0.24
 * Author: MadrasThemes
 * Author URI: https://madrasthemes.com/
 * Requires at least: 4.8
 * Tested up to: 4.8
 *
 * Text Domain: masvideos
 * Domain Path: /languages/
 *
 * @package MasVideos
 * @category Core
 * @author Madras Themes
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Define MASVIDEOS_PLUGIN_FILE.
if ( ! defined( 'MASVIDEOS_PLUGIN_FILE' ) ) {
    define( 'MASVIDEOS_PLUGIN_FILE', __FILE__ );
}

// Include the main MasVideos class.
if ( ! class_exists( 'MasVideos' ) ) {
    include_once dirname( MASVIDEOS_PLUGIN_FILE ) . '/includes/class-masvideos.php';
}

/**
 * Unique access instance for MasVideos class
 */
function MasVideos() {
    return MasVideos::instance();
}

// Global for backwards compatibility.
$GLOBALS['masvideos'] = MasVideos();