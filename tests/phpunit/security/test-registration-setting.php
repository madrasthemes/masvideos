<?php
/**
 * MV-11 regression tests: frontend registration must respect the server-side registration setting
 * at request time and must not run for already-authenticated users.
 *
 * @package MasVideos/Tests
 * @group   security
 * @group   mv-11
 */

defined( 'ABSPATH' ) || exit;

class MasVideos_Test_Registration_Setting extends MasVideos_Unit_Test_Case {

	public function set_up() {
		parent::set_up();
		update_option( 'masvideos_registration_generate_username', 'yes' );
		update_option( 'masvideos_registration_generate_password', 'yes' );
	}

	private function submit_registration( $email ) {
		$_POST = array(
			'register'                 => '1',
			'masvideos-register-nonce' => wp_create_nonce( 'masvideos-register' ),
			'email'                    => $email,
		);
		$_REQUEST = $_POST;
		return $this->run_handler( array( 'MasVideos_Form_Handler', 'process_registration' ) );
	}

	public function test_registration_blocked_when_disabled() {
		update_option( 'masvideos_enable_myaccount_registration', 'no' );
		$email = 'blocked@example.test';

		$redirect = $this->submit_registration( $email );

		$this->assertNull( $redirect, 'a disabled registration must not redirect (no success)' );
		$this->assertFalse( email_exists( $email ), 'no account may be created when registration is disabled (MV-11)' );
		$this->assertGreaterThan( 0, masvideos_notice_count( 'error' ) );
	}

	public function test_registration_blocked_when_option_absent() {
		delete_option( 'masvideos_enable_myaccount_registration' );
		$email = 'absent@example.test';

		$redirect = $this->submit_registration( $email );

		$this->assertNull( $redirect );
		$this->assertFalse( email_exists( $email ), 'absent option is treated as disabled' );
	}

	public function test_valid_nonce_cannot_bypass_disabled_setting() {
		// Mint the nonce while enabled, then disable before the request is processed.
		update_option( 'masvideos_enable_myaccount_registration', 'yes' );
		$nonce = wp_create_nonce( 'masvideos-register' );
		update_option( 'masvideos_enable_myaccount_registration', 'no' );

		$email    = 'retained-nonce@example.test';
		$_POST    = array( 'register' => '1', 'masvideos-register-nonce' => $nonce, 'email' => $email );
		$_REQUEST = $_POST;

		$redirect = $this->run_handler( array( 'MasVideos_Form_Handler', 'process_registration' ) );

		$this->assertNull( $redirect );
		$this->assertFalse( email_exists( $email ), 'a retained valid nonce must not bypass a disabled setting' );
	}

	public function test_registration_allowed_when_enabled() {
		update_option( 'masvideos_enable_myaccount_registration', 'yes' );
		$email = 'allowed@example.test';

		$redirect = $this->submit_registration( $email );

		$this->assertNotNull( $redirect, 'an enabled registration should complete and redirect' );
		$this->assertTrue( email_exists( $email ) !== false, 'the account should be created' );

		$user = get_user_by( 'email', $email );
		$this->assertInstanceOf( 'WP_User', $user );
		$this->assertContains( 'contributor', (array) $user->roles, 'the intentional Contributor role is preserved' );
	}

	public function test_logged_in_user_cannot_register() {
		update_option( 'masvideos_enable_myaccount_registration', 'yes' );
		wp_set_current_user( $this->make_user( 'subscriber' ) );
		$email = 'loggedin@example.test';

		$redirect = $this->submit_registration( $email );

		$this->assertNull( $redirect );
		$this->assertFalse( email_exists( $email ), 'an authenticated user must not trigger a new registration' );
		$this->assertGreaterThan( 0, masvideos_notice_count( 'error' ) );
	}
}
