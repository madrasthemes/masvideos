<?php
/**
 * MV-03 / MV-04 / MV-17 regression tests: MasVideos_Form_Handler::edit_video().
 *
 * @package MasVideos/Tests
 * @group   security
 * @group   mv-03
 * @group   mv-04
 */

defined( 'ABSPATH' ) || exit;

class MasVideos_Test_Edit_Video extends MasVideos_Unit_Test_Case {

	/**
	 * Build a POST payload and run the edit_video handler.
	 *
	 * @param array $overrides Fields to merge into the default payload.
	 * @return string|null Redirect location on success, null on rejection.
	 */
	protected function attempt_edit( $overrides = array() ) {
		$_POST = array_merge(
			array(
				'edit-video'                 => 'Submit video',
				'masvideos-edit-video-nonce' => wp_create_nonce( 'masvideos-edit-video' ),
				'title'                      => 'Payload Title',
				'status'                     => 'pending',
			),
			$overrides
		);
		return $this->run_handler( array( 'MasVideos_Form_Handler', 'edit_video' ) );
	}

	/**
	 * Most recent video authored by a user.
	 *
	 * @param int $uid User id.
	 * @return WP_Post|null
	 */
	protected function latest_video_of( $uid ) {
		$posts = get_posts(
			array(
				'post_type'   => 'video',
				'author'      => $uid,
				'post_status' => 'any',
				'numberposts' => 1,
			)
		);
		return $posts ? $posts[0] : null;
	}

	/** T-13: a subscriber cannot edit an administrator's video. */
	public function test_subscriber_cannot_edit_admin_video() {
		$admin = $this->make_user( 'administrator' );
		$video = $this->make_video( $admin, 'publish', 'Original Admin Title' );
		$snap  = $this->snapshot_post( $video );

		wp_set_current_user( $this->make_user( 'subscriber' ) );
		$this->assertNull( $this->attempt_edit( array( 'id' => $video, 'title' => 'HACKED', 'status' => 'draft' ) ) );
		$this->assertPostUnchanged( $video, $snap );
	}

	/** T-13: a contributor cannot edit another contributor's video. */
	public function test_contributor_cannot_edit_other_users_video() {
		$con_a = $this->make_user( 'contributor' );
		$con_b = $this->make_user( 'contributor' );
		$video = $this->make_video( $con_b, 'draft', 'Belongs to B' );
		$snap  = $this->snapshot_post( $video );

		wp_set_current_user( $con_a );
		$this->assertNull( $this->attempt_edit( array( 'id' => $video, 'title' => 'HACKED' ) ) );
		$this->assertPostUnchanged( $video, $snap );
	}

	/** T-14: a contributor can edit their own draft video. */
	public function test_contributor_can_edit_own_draft() {
		$con_a = $this->make_user( 'contributor' );
		$video = $this->make_video( $con_a, 'draft', 'Before' );

		wp_set_current_user( $con_a );
		$redirect = $this->attempt_edit( array( 'id' => $video, 'title' => 'After', 'status' => 'pending' ) );

		$this->assertNotNull( $redirect );
		$this->assertSame( 'After', get_post( $video )->post_title );
	}

	/** T-15: a contributor cannot edit their own PUBLISHED video (mirrors the edit-screen edit_post gate). */
	public function test_contributor_cannot_edit_own_published_video() {
		$con_a = $this->make_user( 'contributor' );
		$video = $this->make_video( $con_a, 'publish', 'Published Original' );
		$snap  = $this->snapshot_post( $video );

		wp_set_current_user( $con_a );
		$this->assertNull( $this->attempt_edit( array( 'id' => $video, 'title' => 'Changed' ) ) );
		$this->assertPostUnchanged( $video, $snap );
	}

	/** T-16: status is restricted; a contributor cannot publish by submitting status=publish (filter off by default). */
	public function test_status_publish_rejected_when_not_allowed() {
		$con_a = $this->make_user( 'contributor' );
		$video = $this->make_video( $con_a, 'draft', 'Draft' );

		wp_set_current_user( $con_a );
		$redirect = $this->attempt_edit( array( 'id' => $video, 'title' => 'Draft', 'status' => 'publish' ) );

		$this->assertNotNull( $redirect, 'Edit should succeed but with a coerced status.' );
		$this->assertNotSame( 'publish', get_post_status( $video ), 'Manipulated publish must not take effect.' );
	}

	/** T-16: when the site opts in via the filter, publish is accepted. */
	public function test_status_publish_allowed_when_filter_enabled() {
		$con_a = $this->make_user( 'contributor' );
		$video = $this->make_video( $con_a, 'draft', 'Draft' );

		add_filter( 'masvideos_allow_users_to_publish_video', '__return_true' );
		wp_set_current_user( $con_a );
		$redirect = $this->attempt_edit( array( 'id' => $video, 'title' => 'Draft', 'status' => 'publish' ) );
		remove_filter( 'masvideos_allow_users_to_publish_video', '__return_true' );

		$this->assertNotNull( $redirect );
		$this->assertSame( 'publish', get_post_status( $video ), 'Opt-in publishing should work.' );
	}

	/**
	 * T-17: on an existing video, an invalid/forbidden/missing status must leave the current
	 * status UNCHANGED (never silently unpublish, downgrade, or flip it).
	 *
	 * This asserts the EXACT prior status, so a regression that coerces to a default status
	 * (e.g. 'pending') would fail here.
	 */
	public function test_invalid_status_leaves_existing_status_unchanged() {
		$con_a = $this->make_user( 'contributor' );
		wp_set_current_user( $con_a );

		// 'publish' is not offered while the publish filter is off, so it is treated as invalid too.
		foreach ( array( 'auto-draft', 'trash', 'future', 'inherit', 'publish', 'totally-made-up', '' ) as $bad ) {
			$video = $this->make_video( $con_a, 'draft', 'D' );
			$this->attempt_edit( array( 'id' => $video, 'title' => 'D', 'status' => $bad ) );
			$this->assertSame( 'draft', get_post_status( $video ), "Invalid status '{$bad}' must leave the existing status unchanged." );
		}
	}

	/** T-17: on an existing video, omitting the status field must not change the status. */
	public function test_missing_status_leaves_existing_status_unchanged() {
		$con_a = $this->make_user( 'contributor' );
		$video = $this->make_video( $con_a, 'private', 'Keep' );

		wp_set_current_user( $con_a );
		$_POST = array(
			'edit-video'                 => 'Save',
			'masvideos-edit-video-nonce' => wp_create_nonce( 'masvideos-edit-video' ),
			'id'                         => $video,
			'title'                      => 'Keep',
			// no 'status' field at all
		);
		$this->run_handler( array( 'MasVideos_Form_Handler', 'edit_video' ) );
		$this->assertSame( 'private', get_post_status( $video ) );
	}

	/** T-17: a new video with an invalid status defaults to pending. */
	public function test_new_video_invalid_status_defaults_to_pending() {
		$con_a = $this->make_user( 'contributor' );
		wp_set_current_user( $con_a );

		$this->attempt_edit( array( 'id' => 0, 'title' => 'NewVid', 'status' => 'auto-draft' ) );
		$video = $this->latest_video_of( $con_a );
		$this->assertInstanceOf( 'WP_Post', $video );
		$this->assertSame( 'pending', $video->post_status );
	}

	/** T-17: a new video with no status field defaults to pending. */
	public function test_new_video_missing_status_defaults_to_pending() {
		$con_a = $this->make_user( 'contributor' );
		wp_set_current_user( $con_a );

		$_POST = array(
			'edit-video'                 => 'Submit video',
			'masvideos-edit-video-nonce' => wp_create_nonce( 'masvideos-edit-video' ),
			'title'                      => 'NoStatusNew',
			// no 'status'
		);
		$this->run_handler( array( 'MasVideos_Form_Handler', 'edit_video' ) );
		$video = $this->latest_video_of( $con_a );
		$this->assertInstanceOf( 'WP_Post', $video );
		$this->assertSame( 'pending', $video->post_status );
	}

	/**
	 * T-18: the post-type guard is isolated — a contributor editing a REGULAR post they OWN
	 * (and can edit via edit_post) is still rejected because it is not a `video`. This proves
	 * the rejection is the post-type check, not a capability failure.
	 */
	public function test_post_type_guard_isolated_on_owned_post() {
		$con_a = $this->make_user( 'contributor' );
		$post  = self::factory()->post->create( array( 'post_type' => 'post', 'post_author' => $con_a, 'post_status' => 'draft', 'post_title' => 'My Blog Post' ) );
		$snap  = $this->snapshot_post( $post );

		wp_set_current_user( $con_a );
		// Sanity: the contributor CAN edit their own draft post, so only the post-type guard can reject this.
		$this->assertTrue( current_user_can( 'edit_post', $post ), 'Contributor should be able to edit their own draft post.' );

		$this->assertNull( $this->attempt_edit( array( 'id' => $post, 'title' => 'HACKED' ) ) );
		$this->assertPostUnchanged( $post, $snap );
	}

	/** T-18: editing a page id (cross-user) is rejected without a fatal error. */
	public function test_editing_page_id_rejected() {
		$admin = $this->make_user( 'administrator' );
		$page  = self::factory()->post->create( array( 'post_type' => 'page', 'post_author' => $admin, 'post_title' => 'A Page' ) );
		$snap  = $this->snapshot_post( $page );

		wp_set_current_user( $this->make_user( 'contributor' ) );
		$this->assertNull( $this->attempt_edit( array( 'id' => $page, 'title' => 'HACKED' ) ) );
		$this->assertPostUnchanged( $page, $snap );
	}

	/** T-19: anonymous cannot create or edit. */
	public function test_anonymous_rejected() {
		wp_set_current_user( 0 );
		$before = (int) wp_count_posts( 'video' )->pending + (int) wp_count_posts( 'video' )->draft;
		$this->assertNull( $this->attempt_edit( array( 'id' => 0, 'title' => 'New' ) ) );
		$after = (int) wp_count_posts( 'video' )->pending + (int) wp_count_posts( 'video' )->draft;
		$this->assertSame( $before, $after, 'Anonymous must not create a video.' );
	}

	/** T-20: a scalar submitted where an array is expected must not cause a TypeError. */
	public function test_malformed_array_field_does_not_fatal() {
		$con_a = $this->make_user( 'contributor' );
		$video = $this->make_video( $con_a, 'draft', 'Keep' );

		wp_set_current_user( $con_a );
		// category_ids / tag_ids are multiselect (array) fields; send scalars.
		$redirect = $this->attempt_edit(
			array(
				'id'           => $video,
				'title'        => 'Keep',
				'status'       => 'pending',
				'category_ids' => 'not-an-array',
				'tag_ids'      => 'also-not-an-array',
			)
		);
		// No fatal; operation completes.
		$this->assertNotNull( $redirect );
		$this->assertInstanceOf( 'WP_Post', get_post( $video ) );
	}

	/** T-21: a subscriber (no edit_videos) cannot create a video. */
	public function test_subscriber_cannot_create_video() {
		$sub = $this->make_user( 'subscriber' );
		wp_set_current_user( $sub );

		$this->assertNull( $this->attempt_edit( array( 'id' => 0, 'title' => 'Subscriber Upload' ) ) );
		$this->assertNull( $this->latest_video_of( $sub ), 'Subscriber must not be able to create a video.' );
	}

	/** Code quality: a malformed (array) `redirect` param must not cause a warning/TypeError. */
	public function test_malformed_redirect_array_does_not_error() {
		$con_a = $this->make_user( 'contributor' );
		$video = $this->make_video( $con_a, 'draft', 'R' );

		wp_set_current_user( $con_a );
		$_POST = array(
			'edit-video'                 => 'Save',
			'masvideos-edit-video-nonce' => wp_create_nonce( 'masvideos-edit-video' ),
			'id'                         => $video,
			'title'                      => 'R',
			'status'                     => 'pending',
			'redirect'                   => array( 'https://evil.example/' ),
		);
		$redirect = $this->run_handler( array( 'MasVideos_Form_Handler', 'edit_video' ) );
		$this->assertNotNull( $redirect, 'Edit should still succeed, ignoring the malformed redirect.' );
	}

	/** T-45 (subset): a contributor CAN create a video, defaulting to a non-public status. */
	public function test_contributor_can_create_pending_video() {
		$con_a = $this->make_user( 'contributor' );
		wp_set_current_user( $con_a );

		$redirect = $this->attempt_edit( array( 'id' => 0, 'title' => 'Legit Upload', 'status' => 'pending' ) );
		$this->assertNotNull( $redirect );

		$video = $this->latest_video_of( $con_a );
		$this->assertInstanceOf( 'WP_Post', $video );
		$this->assertSame( 'Legit Upload', $video->post_title );
		$this->assertNotSame( 'publish', $video->post_status );
	}
}
