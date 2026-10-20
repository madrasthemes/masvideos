<?php
/**
 * Plugin Name: MAS Videos
 * Plugin URI: https://github.com/madrasthemes/masvideos
 * Description: A free plugin to create and list movies, videos and TV shows.
 * Version: 1.3.5
 * Author: MadrasThemes
 * Author URI: https://madrasthemes.com/
 * Requires at least: 6.4
 * Requires PHP: 7.4
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: masvideos
 * Domain Path: /languages/
 *
 * @package MasVideos
 * @category Core
 * @author MadrasThemes
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
