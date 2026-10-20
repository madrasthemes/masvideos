<?php
/**
 * MV-10 multisite regression tests: automatic site enrolment on login happens only AFTER
 * authentication succeeds, is disabled by default, and never lets request input choose the role.
 *
 * Run under multisite, e.g.:
 *   WP_MULTISITE=1 vendor/bin/phpunit -c phpunit-multisite.xml.dist
 *
 * @package MasVideos/Tests
 * @group   security
 * @group   mv-10
 * @group   ms-required
 */

defined( 'ABSPATH' ) || exit;

class MasVideos_Test_Multisite_Login extends MasVideos_Unit_Test_Case {

	const PASSWORD = 'correct-horse-battery';

	public function set_up() {
		parent::set_up();
		if ( ! is_multisite() ) {
			$this->markTestSkipped( 'Multisite required (run with WP_MULTISITE=1 -c phpunit-multisite.xml.dist).' );
		}
	}

	/**
	 * Create a network user that is NOT a member of the current site.
	 */
	private function make_network_user( $login ) {
		$uid = self::factory()->user->create(
			array(
				'user_login' => $login,
				'user_pass'  => self::PASSWORD,
				'user_email' => $login . '@example.test',
				'role'       => '',
			)
		);
		remove_user_from_blog( $uid, get_current_blog_id() );
		return $uid;
	}

	private function submit_login( $login, $password ) {
		$_POST = array(
			'login'                 => '1',
			'masvideos-login-nonce' => wp_create_nonce( 'masvideos-login' ),
			'username'              => $login,
			'password'              => $password,
		);
		$_REQUEST = $_POST;
		return $this->run_handler( array( 'MasVideos_Form_Handler', 'process_login' ) );
	}

	private function roles_on_current_blog( $user_id ) {
		$user = new WP_User( $user_id, '', get_current_blog_id() );
		return (array) $user->roles;
	}

	/* ---------------------------------------------------------------- */

	public function test_existing_member_logs_in_successfully() {
		$uid = $this->make_network_user( 'ms_member' );
		add_user_to_blog( get_current_blog_id(), $uid, 'subscriber' );
		$this->assertTrue( is_user_member_of_blog( $uid, get_current_blog_id() ) );

		$redirect = $this->submit_login( 'ms_member', self::PASSWORD );

		$this->assertNotNull( $redirect, 'an existing member should log in and be redirected' );
		$this->assertTrue( is_user_member_of_blog( $uid, get_current_blog_id() ), 'membership is unchanged' );
		$this->assertContains( 'subscriber', $this->roles_on_current_blog( $uid ) );
	}

	public function test_non_member_login_does_not_enrol_when_disabled() {
		$uid = $this->make_network_user( 'ms_nonmember' );
		$this->assertFalse( is_user_member_of_blog( $uid, get_current_blog_id() ) );

		$redirect = $this->submit_login( 'ms_nonmember', self::PASSWORD );

		$this->assertNotNull( $redirect, 'authentication still succeeds network-wide' );
		$this->assertFalse( is_user_member_of_blog( $uid, get_current_blog_id() ), 'no enrolment when the feature is disabled (default) (MV-10)' );
	}

	public function test_invalid_password_with_enrolment_enabled_changes_nothing() {
		$uid = $this->make_network_user( 'ms_badpass' );
		add_filter( 'masvideos_add_user_to_blog_on_login', '__return_true' );

		$redirect = $this->submit_login( 'ms_badpass', 'wrong-password' );

		$this->assertNull( $redirect, 'a failed login must not redirect' );
		$this->assertFalse( is_user_member_of_blog( $uid, get_current_blog_id() ), 'failed authentication must not enrol the user (MV-10)' );
		$this->assertSame( array(), $this->roles_on_current_blog( $uid ), 'no role is assigned on a failed login' );

		remove_filter( 'masvideos_add_user_to_blog_on_login', '__return_true' );
	}

	public function test_unknown_username_with_enrolment_enabled_changes_nothing() {
		add_filter( 'masvideos_add_user_to_blog_on_login', '__return_true' );
		$before = get_user_by( 'login', 'does_not_exist_user' );
		$this->assertFalse( $before );

		$redirect = $this->submit_login( 'does_not_exist_user', self::PASSWORD );

		$this->assertNull( $redirect );
		$this->assertFalse( get_user_by( 'login', 'does_not_exist_user' ), 'no account is created or enrolled for an unknown username' );

		remove_filter( 'masvideos_add_user_to_blog_on_login', '__return_true' );
	}

	public function test_successful_login_with_enrolment_enabled_enrols_as_subscriber() {
		$uid = $this->make_network_user( 'ms_enrol' );
		add_filter( 'masvideos_add_user_to_blog_on_login', '__return_true' );

		$redirect = $this->submit_login( 'ms_enrol', self::PASSWORD );

		$this->assertNotNull( $redirect );
		$this->assertTrue( is_user_member_of_blog( $uid, get_current_blog_id() ), 'an authenticated user is enrolled when the feature is explicitly enabled' );
		$this->assertContains( 'subscriber', $this->roles_on_current_blog( $uid ), 'the default enrolment role is subscriber, not a content role' );
		$this->assertNotContains( 'contributor', $this->roles_on_current_blog( $uid ) );

		remove_filter( 'masvideos_add_user_to_blog_on_login', '__return_true' );
	}

	public function test_invalid_filtered_role_is_rejected() {
		$uid = $this->make_network_user( 'ms_badrole' );
		add_filter( 'masvideos_add_user_to_blog_on_login', '__return_true' );
		$role_cb = function () {
			return 'this_is_not_a_registered_role';
		};
		add_filter( 'masvideos_add_user_to_blog_on_login_role', $role_cb );

		$redirect = $this->submit_login( 'ms_badrole', self::PASSWORD );

		$this->assertNotNull( $redirect, 'login still succeeds' );
		$this->assertFalse( is_user_member_of_blog( $uid, get_current_blog_id() ), 'an unregistered role must not be used to enrol (no escalation, fail closed)' );

		remove_filter( 'masvideos_add_user_to_blog_on_login_role', $role_cb );
		remove_filter( 'masvideos_add_user_to_blog_on_login', '__return_true' );
	}

	public function test_enrolment_failure_does_not_break_login() {
		$uid = $this->make_network_user( 'ms_enrolfail' );
		add_filter( 'masvideos_add_user_to_blog_on_login', '__return_true' );
		// Force add_user_to_blog() to fail.
		$deny = function () {
			return new WP_Error( 'denied', 'denied' );
		};
		add_filter( 'can_add_user_to_blog', $deny );

		$redirect = $this->submit_login( 'ms_enrolfail', self::PASSWORD );

		$this->assertNotNull( $redirect, 'a failed enrolment must not break the successful login' );
		$this->assertFalse( is_user_member_of_blog( $uid, get_current_blog_id() ), 'the user is not a member after a failed enrolment' );

		remove_filter( 'can_add_user_to_blog', $deny );
		remove_filter( 'masvideos_add_user_to_blog_on_login', '__return_true' );
	}

	public function test_enrolled_user_gets_no_unexpected_capabilities() {
		$uid = $this->make_network_user( 'ms_caps' );
		add_filter( 'masvideos_add_user_to_blog_on_login', '__return_true' );

		$this->submit_login( 'ms_caps', self::PASSWORD );

		$user = new WP_User( $uid, '', get_current_blog_id() );
		$this->assertFalse( $user->has_cap( 'edit_videos' ), 'enrolment must not grant content-creation capabilities' );
		$this->assertFalse( $user->has_cap( 'publish_videos' ) );
		$this->assertFalse( $user->has_cap( 'manage_masvideos' ) );

		remove_filter( 'masvideos_add_user_to_blog_on_login', '__return_true' );
	}
}
