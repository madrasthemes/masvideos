<?php
/**
 * MV-07 / S-02 regression tests: object-level authorization for admin AJAX "save" handlers.
 *
 * The attribute/source/cast/crew/season save endpoints must (a) never implicitly create a post
 * from a missing/zero id, (b) verify the post type, and (c) require edit_post on the specific
 * object — not merely the plural capability.
 *
 * @package MasVideos/Tests
 * @group   security
 * @group   mv-07
 */

defined( 'ABSPATH' ) || exit;

class MasVideos_Test_Ajax_Attribute_Save extends MasVideos_Unit_Test_Case {

	private function set_save_request( $post_id, $data = 'attribute_names%5B0%5D=' ) {
		$_POST['security'] = wp_create_nonce( 'save-attributes-video' );
		$_POST['post_id']  = $post_id;
		$_POST['data']     = $data;
		$_REQUEST          = $_POST;
	}

	private function count_videos() {
		return count(
			get_posts(
				array(
					'post_type'      => 'video',
					'post_status'    => 'any',
					'posts_per_page' => -1,
					'fields'         => 'ids',
				)
			)
		);
	}

	public function test_zero_post_id_is_rejected_and_creates_no_post() {
		wp_set_current_user( $this->make_user( 'administrator' ) );
		$before = $this->count_videos();

		$this->set_save_request( 0 );
		$result = $this->capture_ajax( array( 'MasVideos_AJAX', 'save_attributes_video' ) );

		$this->assertTrue( $result['died'], 'handler should terminate' );
		$this->assertNull( $result['json'], 'an unauthorized save must not return a success envelope' );
		$this->assertSame( $before, $this->count_videos(), 'no video may be implicitly created (MV-07)' );
	}

	public function test_missing_post_id_is_rejected_and_creates_no_post() {
		wp_set_current_user( $this->make_user( 'administrator' ) );
		$before = $this->count_videos();

		// No post_id key at all.
		$_POST['security'] = wp_create_nonce( 'save-attributes-video' );
		$_POST['data']     = 'attribute_names%5B0%5D=';
		$_REQUEST          = $_POST;

		$result = $this->capture_ajax( array( 'MasVideos_AJAX', 'save_attributes_video' ) );

		$this->assertTrue( $result['died'] );
		$this->assertNull( $result['json'] );
		$this->assertSame( $before, $this->count_videos() );
	}

	public function test_contributor_cannot_modify_another_users_published_video() {
		$owner       = $this->make_user( 'administrator' );
		$contributor = $this->make_user( 'contributor' );
		$video       = $this->make_video( $owner, 'publish', 'Owned By Admin' );
		$snapshot    = $this->snapshot_post( $video );

		wp_set_current_user( $contributor );
		$this->set_save_request( $video );
		$result = $this->capture_ajax( array( 'MasVideos_AJAX', 'save_attributes_video' ) );

		$this->assertTrue( $result['died'] );
		$this->assertNull( $result['json'], 'plural edit_videos must not authorize another user\'s video' );
		$this->assertPostUnchanged( $video, $snapshot );
	}

	public function test_wrong_post_type_is_rejected() {
		$admin = $this->make_user( 'administrator' );
		$movie = self::factory()->post->create(
			array(
				'post_type'   => 'movie',
				'post_status' => 'publish',
				'post_author' => $admin,
			)
		);
		$snapshot = $this->snapshot_post( $movie );

		wp_set_current_user( $admin );
		$this->set_save_request( $movie );
		$result = $this->capture_ajax( array( 'MasVideos_AJAX', 'save_attributes_video' ) );

		$this->assertTrue( $result['died'] );
		$this->assertNull( $result['json'], 'a movie id must not be accepted by the video handler' );
		$this->assertPostUnchanged( $movie, $snapshot );
	}

	public function test_admin_editing_own_video_is_authorized() {
		$admin = $this->make_user( 'administrator' );
		$video = $this->make_video( $admin, 'draft', 'Admin Draft' );

		wp_set_current_user( $admin );
		$this->set_save_request( $video );
		$result = $this->capture_ajax( array( 'MasVideos_AJAX', 'save_attributes_video' ) );

		// Authorized: the handler passes the gate and returns a JSON success envelope.
		$this->assertIsArray( $result['json'], 'an authorized owner must pass the authorization gate' );
		$this->assertArrayHasKey( 'success', $result['json'] );
		$this->assertTrue( $result['json']['success'] );
	}

	public function test_contributor_editing_own_draft_is_authorized() {
		$contributor = $this->make_user( 'contributor' );
		$video       = $this->make_video( $contributor, 'draft', 'Contributor Draft' );

		wp_set_current_user( $contributor );
		$this->set_save_request( $video );
		$result = $this->capture_ajax( array( 'MasVideos_AJAX', 'save_attributes_video' ) );

		$this->assertIsArray( $result['json'], 'a contributor editing their own draft must be allowed' );
		$this->assertTrue( $result['json']['success'] );
	}
}
