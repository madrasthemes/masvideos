<?php
/**
 * Plugin Name: MAS Videos
 * Plugin URI: https://madrasthemes.com/masvideos
 * Description: This plugins helps to run videos, movies and series in your site.
 * Version: 1.0.0
 * Author: MadrasThemes
 * Author URI: https://madrasthemes.com/
 * Network: true
 * Requires at least: 4.8
 * Tested up to: 4.8
 *
 * Text Domain: masvideos
 * Domain Path: /languages/
 *
 * @package Mas_Videos
 * @category Core
 * @author Madras Themes
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Define MAS_VIDEOS_PLUGIN_FILE.
if ( ! defined( 'MAS_VIDEOS_PLUGIN_FILE' ) ) {
    define( 'MAS_VIDEOS_PLUGIN_FILE', __FILE__ );
}

// Include the main Mas_Videos class.
if ( ! class_exists( 'Mas_Videos' ) ) {
    include_once dirname( MAS_VIDEOS_PLUGIN_FILE ) . '/includes/class-masvideos.php';
}

/**
 * Unique access instance for Mas_Videos class
 */
function Mas_Videos() {
    return Mas_Videos::instance();
}

// Global for backwards compatibility.
$GLOBALS['masvideos'] = Mas_Videos();