<?php
/**
 * MV-15 regression tests: the setup wizard requires an administrator capability AND a valid nonce
 * before it creates pages / changes options.
 *
 * @package MasVideos/Tests
 * @group   security
 * @group   mv-15
 */

defined( 'ABSPATH' ) || exit;

class MasVideos_Test_Setup_Wizard_Csrf extends MasVideos_Unit_Test_Case {

	public function set_up() {
		parent::set_up();

		if ( ! function_exists( 'masvideos_create_page' ) && file_exists( MASVIDEOS_ABSPATH . 'includes/admin/masvideos-admin-functions.php' ) ) {
			include_once MASVIDEOS_ABSPATH . 'includes/admin/masvideos-admin-functions.php';
		}
		if ( ! class_exists( 'MasVideos_Admin_Notices' ) && file_exists( MASVIDEOS_ABSPATH . 'includes/admin/class-masvideos-admin-notices.php' ) ) {
			include_once MASVIDEOS_ABSPATH . 'includes/admin/class-masvideos-admin-notices.php';
		}
		if ( ! class_exists( 'MasVideos_Admin_Setup_Wizard' ) ) {
			include_once MASVIDEOS_ABSPATH . 'includes/admin/class-masvideos-admin-setup-wizard.php';
		}

		delete_option( 'masvideos_myaccount_page_id' );
	}

	private function wizard() {
		return new MasVideos_Admin_Setup_Wizard();
	}

	public function test_without_nonce_no_pages_are_created() {
		wp_set_current_user( $this->make_user( 'administrator' ) );
		$_GET['masvideos-setup'] = 'true';
		$_REQUEST                = $_GET; // no _wpnonce

		$died = false;
		try {
			$this->wizard()->setup_wizard();
		} catch ( WPDieException $e ) {
			$died = true; // check_admin_referer() fails closed.
		} catch ( MasVideos_Redirect_Exception $e ) {
			$this->fail( 'setup must not reach the redirect without a valid nonce' );
		}

		$this->assertTrue( $died, 'a missing nonce must stop the wizard (MV-15)' );
		$this->assertFalse( (bool) get_option( 'masvideos_myaccount_page_id' ), 'no pages/options may be created without a nonce' );
	}

	public function test_non_admin_cannot_run_wizard_even_with_nonce() {
		wp_set_current_user( $this->make_user( 'subscriber' ) );
		$_GET['masvideos-setup'] = 'true';
		$_GET['_wpnonce']        = wp_create_nonce( 'masvideos-setup' );
		$_REQUEST                = $_GET;

		$ran_redirect = $this->run_handler( array( $this->wizard(), 'setup_wizard' ) );

		$this->assertNull( $ran_redirect, 'a non-admin must not trigger the wizard' );
		$this->assertFalse( (bool) get_option( 'masvideos_myaccount_page_id' ) );
	}

	public function test_admin_with_valid_nonce_runs_wizard() {
		if ( ! function_exists( 'masvideos_create_page' ) ) {
			$this->markTestSkipped( 'masvideos_create_page() is unavailable in this context.' );
		}

		wp_set_current_user( $this->make_user( 'administrator' ) );
		$_GET['masvideos-setup'] = 'true';
		$_GET['_wpnonce']        = wp_create_nonce( 'masvideos-setup' );
		$_REQUEST                = $_GET;

		$redirect = $this->run_handler( array( $this->wizard(), 'setup_wizard' ) );

		$this->assertNotNull( $redirect, 'a valid admin + nonce request completes and redirects' );
		$this->assertGreaterThan( 0, (int) get_option( 'masvideos_myaccount_page_id' ), 'pages are created on the legitimate path' );
	}
}
