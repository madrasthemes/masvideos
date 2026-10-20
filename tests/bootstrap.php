<?php
/**
 * PHPUnit bootstrap for MAS Videos integration tests.
 *
 * Runs against a disposable WordPress test database. Works both with the bundled
 * wp-env container (`wp-env run tests-cli ...`) and a local wp-phpunit + MySQL setup.
 *
 * Local run (no Docker) expects these environment variables:
 *   WP_TESTS_CONFIG_FILE_PATH  Absolute path to a wp-tests-config.php (DB creds + ABSPATH).
 *   WP_PHPUNIT__DIR (optional) Path to the wp-phpunit library; defaults to the composer copy.
 *
 * @package MasVideos/Tests
 */

// Composer autoload (phpunit, polyfills, wp-phpunit).
require_once dirname( __DIR__ ) . '/vendor/autoload.php';

// Allow a config file to be supplied via environment for local (non-container) runs.
if ( ! defined( 'WP_TESTS_CONFIG_FILE_PATH' ) && getenv( 'WP_TESTS_CONFIG_FILE_PATH' ) ) {
	define( 'WP_TESTS_CONFIG_FILE_PATH', getenv( 'WP_TESTS_CONFIG_FILE_PATH' ) );
}

// Locate the WordPress PHPUnit test library.
$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = getenv( 'WP_PHPUNIT__DIR' );
}

if ( ! $_tests_dir ) {
	$_tests_dir = dirname( __DIR__ ) . '/vendor/wp-phpunit/wp-phpunit';
}

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	echo "Could not find {$_tests_dir}/includes/functions.php. Run `composer install`." . PHP_EOL;
	exit( 1 );
}

// PHPUnit Polyfills (required by the WP test suite).
$_polyfills = dirname( __DIR__ ) . '/vendor/yoast/phpunit-polyfills/phpunitpolyfills-autoload.php';
if ( file_exists( $_polyfills ) ) {
	require_once $_polyfills;
}

// Give access to tests_add_filter().
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load MAS Videos before WordPress finishes loading.
 */
function _masvideos_manually_load_plugin() {
	require dirname( __DIR__ ) . '/masvideos.php';
}
tests_add_filter( 'muplugins_loaded', '_masvideos_manually_load_plugin' );

/*
 * Optional: let a test run exercise a specific EMPTY_TRASH_DAYS value for real (it is a
 * constant, so it cannot be toggled per-test). Setting MASVIDEOS_TEST_EMPTY_TRASH_DAYS=0
 * reproduces a site that has disabled Trash, where wp_trash_post() permanently deletes.
 * Defined before the WP bootstrap so WordPress keeps this value instead of its default.
 */
if ( false !== getenv( 'MASVIDEOS_TEST_EMPTY_TRASH_DAYS' ) && ! defined( 'EMPTY_TRASH_DAYS' ) ) {
	define( 'EMPTY_TRASH_DAYS', (int) getenv( 'MASVIDEOS_TEST_EMPTY_TRASH_DAYS' ) );
}

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';

/*
 * Set up MAS Videos' own schema and roles once, against the base (committed) test
 * database, so Contributor capabilities and the attribute-taxonomies table exist
 * for every test. The plugin normally does this during its install routine.
 */
if ( class_exists( 'MasVideos_Install' ) ) {
	MasVideos_Install::install();
}

// Shared test helpers (loaded after WP bootstrap so ABSPATH and WP_UnitTestCase exist).
require_once __DIR__ . '/phpunit/includes/class-masvideos-redirect-exception.php';
require_once __DIR__ . '/phpunit/includes/class-masvideos-ajax-die-exception.php';
require_once __DIR__ . '/phpunit/includes/class-masvideos-unit-test-case.php';
