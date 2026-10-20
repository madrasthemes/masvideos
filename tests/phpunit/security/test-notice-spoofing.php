<?php
/**
 * MV-16 regression tests: the `masvideos_error` query string is treated as an allowlisted error
 * code mapped to a fixed message, never as arbitrary notice text.
 *
 * @package MasVideos/Tests
 * @group   security
 * @group   mv-16
 */

defined( 'ABSPATH' ) || exit;

class MasVideos_Test_Notice_Spoofing extends MasVideos_Unit_Test_Case {

	private function query() {
		if ( isset( MasVideos()->query ) && is_object( MasVideos()->query ) ) {
			return MasVideos()->query;
		}
		return new MasVideos_Query();
	}

	public function test_arbitrary_text_is_not_rendered_as_a_notice() {
		$_GET['masvideos_error'] = 'Your account was suspended, call 1-800-SCAM now';
		$this->query()->get_errors();

		$this->assertSame( 0, masvideos_notice_count( 'error' ), 'arbitrary attacker text must never become a notice (MV-16)' );
	}

	public function test_unknown_code_is_ignored() {
		$_GET['masvideos_error'] = 'totally_unknown_code';
		$this->query()->get_errors();

		$this->assertSame( 0, masvideos_notice_count( 'error' ) );
	}

	public function test_known_code_shows_fixed_message() {
		$messages = masvideos_get_notice_error_messages();
		$this->assertArrayHasKey( 'template_file_not_writable', $messages );

		$_GET['masvideos_error'] = 'template_file_not_writable';
		$this->query()->get_errors();

		$this->assertTrue( masvideos_has_notice( $messages['template_file_not_writable'], 'error' ), 'the fixed, server-defined message should be shown' );
	}

	public function test_code_is_sanitised_as_key() {
		// Anything that is not a valid key is stripped; the resulting value is not a known code.
		$_GET['masvideos_error'] = '<script>alert(1)</script>';
		$this->query()->get_errors();

		$this->assertSame( 0, masvideos_notice_count( 'error' ) );
	}

	public function test_integrations_can_register_codes_via_filter() {
		$cb = function ( $messages ) {
			$messages['my_custom_code'] = 'Custom integration message.';
			return $messages;
		};
		add_filter( 'masvideos_error_notice_messages', $cb );

		$_GET['masvideos_error'] = 'my_custom_code';
		$this->query()->get_errors();

		$this->assertTrue( masvideos_has_notice( 'Custom integration message.', 'error' ) );
		remove_filter( 'masvideos_error_notice_messages', $cb );
	}
}
