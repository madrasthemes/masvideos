<?php
/**
 * MV-01 regression tests: MasVideos_Form_Handler::delete_video().
 *
 * @package MasVideos/Tests
 * @group   security
 * @group   mv-01
 */

defined( 'ABSPATH' ) || exit;

class MasVideos_Test_Delete_Video extends MasVideos_Unit_Test_Case {

	/**
	 * Drive the delete_video handler with the given request params.
	 *
	 * @param int    $post_id Target post id to place in $_GET['post'].
	 * @param string $nonce   Nonce value.
	 * @param string $action  delete|trash|other.
	 * @return string|null Redirect location on success, null on rejection.
	 */
	protected function attempt_delete( $post_id, $nonce, $action = 'delete' ) {
		$_GET = array(
			'post'                         => $post_id,
			'action'                       => $action,
			'masvideos-delete-video-nonce' => $nonce,
		);
		return $this->run_handler( array( 'MasVideos_Form_Handler', 'delete_video' ) );
	}

	/** T-01: anonymous user cannot delete, even an unprotected video — proven independent of the nonce. */
	public function test_anonymous_cannot_delete_video() {
		$admin    = $this->make_user( 'administrator' );
		$video    = $this->make_video( $admin, 'publish' );
		$snapshot = $this->snapshot_post( $video );

		wp_set_current_user( 0 );
		// Mint a nonce as the logged-out visitor so wp_verify_nonce() PASSES and the
		// is_user_logged_in() guard (not the nonce) is what rejects the request.
		$nonce = wp_create_nonce( 'masvideos-delete-video_' . $video );
		$this->assertNotFalse( wp_verify_nonce( $nonce, 'masvideos-delete-video_' . $video ), 'logged-out nonce should verify within the same session' );

		$this->assertNull( $this->attempt_delete( $video, $nonce, 'trash' ) );
		$this->assertNull( $this->attempt_delete( $video, $nonce, 'delete' ) );
		$this->assertPostUnchanged( $video, $snapshot );
		// Also a plainly forged nonce is rejected.
		$this->assertNull( $this->attempt_delete( $video, 'forged-nonce' ) );
		$this->assertPostUnchanged( $video, $snapshot );
	}

	/** T-02: subscriber cannot delete an administrator's video (valid object nonce). */
	public function test_subscriber_cannot_delete_admin_video() {
		$admin      = $this->make_user( 'administrator' );
		$subscriber = $this->make_user( 'subscriber' );
		$video      = $this->make_video( $admin, 'publish' );
		$snapshot   = $this->snapshot_post( $video );

		wp_set_current_user( $subscriber );
		// Give the attacker a *validly minted* object nonce to prove authorization is independent of the nonce.
		$nonce = $this->video_delete_nonce( $video );

		$this->assertNull( $this->attempt_delete( $video, $nonce ) );
		$this->assertPostUnchanged( $video, $snapshot );
	}

	/** T-02 (cont.): subscriber cannot delete arbitrary pages/posts/attachments via this handler. */
	public function test_subscriber_cannot_delete_other_post_types() {
		$admin      = $this->make_user( 'administrator' );
		$subscriber = $this->make_user( 'subscriber' );

		$page = self::factory()->post->create( array( 'post_type' => 'page', 'post_author' => $admin, 'post_title' => 'Important Page' ) );
		$post = self::factory()->post->create( array( 'post_type' => 'post', 'post_author' => $admin, 'post_title' => 'Important Post' ) );
		$att  = $this->make_attachment_with_file( $admin );

		$page_snap = $this->snapshot_post( $page );
		$post_snap = $this->snapshot_post( $post );

		wp_set_current_user( $subscriber );

		// Even with a validly minted object nonce for each target, the post-type guard rejects them.
		$this->assertNull( $this->attempt_delete( $page, $this->video_delete_nonce( $page ) ) );
		$this->assertNull( $this->attempt_delete( $post, $this->video_delete_nonce( $post ) ) );
		$this->assertNull( $this->attempt_delete( $att['id'], $this->video_delete_nonce( $att['id'] ) ) );

		$this->assertPostUnchanged( $page, $page_snap );
		$this->assertPostUnchanged( $post, $post_snap );
		$this->assertInstanceOf( 'WP_Post', get_post( $att['id'] ), 'Attachment must survive.' );
		$this->assertFileExists( $att['file'], 'Attachment file must not be deleted from disk.' );
	}

	/** T-03: a contributor cannot delete another contributor's video. */
	public function test_contributor_cannot_delete_other_users_video() {
		$con_a    = $this->make_user( 'contributor' );
		$con_b    = $this->make_user( 'contributor' );
		$video    = $this->make_video( $con_b, 'draft' );
		$snapshot = $this->snapshot_post( $video );

		wp_set_current_user( $con_a );
		$this->assertNull( $this->attempt_delete( $video, $this->video_delete_nonce( $video ) ) );
		$this->assertPostUnchanged( $video, $snapshot );
	}

	/** T-04: a contributor can delete their own draft video. */
	public function test_contributor_can_delete_own_draft_video() {
		$con_a = $this->make_user( 'contributor' );
		$video = $this->make_video( $con_a, 'draft' );

		wp_set_current_user( $con_a );
		$redirect = $this->attempt_delete( $video, $this->video_delete_nonce( $video ) );

		$this->assertNotNull( $redirect, 'Owner deletion should succeed and redirect.' );
		$this->assertNull( get_post( $video ), 'Own draft video should be permanently deleted.' );
	}

	/**
	 * T-05 (refined policy): PERMANENT deletion of an owner's PUBLISHED video requires the
	 * delete capability (ownership alone is not enough); but the owner can still TRASH it.
	 */
	public function test_permanent_delete_of_own_published_video_requires_capability() {
		$con_a = $this->make_user( 'contributor' );
		$video = $this->make_video( $con_a, 'publish' );
		$snap  = $this->snapshot_post( $video );

		wp_set_current_user( $con_a );
		// Contributor lacks delete_published_videos -> permanent delete is rejected, post intact.
		$this->assertNull( $this->attempt_delete( $video, $this->video_delete_nonce( $video ), 'delete' ) );
		$this->assertPostUnchanged( $video, $snap );
		$this->assertSame( 'publish', get_post_status( $video ) );

		// Owner CAN recoverably trash their own published video (account self-service).
		$redirect = $this->attempt_delete( $video, $this->video_delete_nonce( $video ), 'trash' );
		$this->assertNotNull( $redirect );
		$this->assertSame( 'trash', get_post_status( $video ) );
	}

	/** T-05 (trash): the trash action trashes rather than permanently deletes. */
	public function test_owner_trash_action_trashes_video() {
		$con_a = $this->make_user( 'contributor' );
		$video = $this->make_video( $con_a, 'publish' );

		wp_set_current_user( $con_a );
		$redirect = $this->attempt_delete( $video, $this->video_delete_nonce( $video ), 'trash' );

		$this->assertNotNull( $redirect );
		$this->assertSame( 'trash', get_post_status( $video ), 'Trash action must trash, not hard-delete.' );
	}

	/** T-06: administrator can delete any video; a plain editor (no video caps, not owner) cannot. */
	public function test_admin_can_delete_but_editor_cannot() {
		$admin  = $this->make_user( 'administrator' );
		$editor = $this->make_user( 'editor' );
		$owner  = $this->make_user( 'contributor' );

		$video_for_editor = $this->make_video( $owner, 'publish' );
		$editor_snap      = $this->snapshot_post( $video_for_editor );

		wp_set_current_user( $editor );
		$this->assertNull( $this->attempt_delete( $video_for_editor, $this->video_delete_nonce( $video_for_editor ) ) );
		$this->assertPostUnchanged( $video_for_editor, $editor_snap );

		$video_for_admin = $this->make_video( $owner, 'publish' );
		wp_set_current_user( $admin );
		$this->assertNotNull( $this->attempt_delete( $video_for_admin, $this->video_delete_nonce( $video_for_admin ) ) );
		$this->assertNull( get_post( $video_for_admin ) );
	}

	/** T-07: a valid nonce minted for one video cannot delete a different video (object binding). */
	public function test_nonce_is_bound_to_object() {
		$con_a  = $this->make_user( 'contributor' );
		$video_a = $this->make_video( $con_a, 'draft' );
		$video_b = $this->make_video( $con_a, 'draft' );
		$snap_a  = $this->snapshot_post( $video_a );

		wp_set_current_user( $con_a );
		// Nonce minted for B, used against A.
		$nonce_b = $this->video_delete_nonce( $video_b );
		$this->assertNull( $this->attempt_delete( $video_a, $nonce_b ) );
		$this->assertPostUnchanged( $video_a, $snap_a );
		// B itself is untouched too (handler never ran against it).
		$this->assertInstanceOf( 'WP_Post', get_post( $video_b ) );
	}

	/** T-08: malformed / invalid ids are rejected cleanly without fatals or warnings. */
	public function test_invalid_ids_rejected_cleanly() {
		$con_a = $this->make_user( 'contributor' );
		wp_set_current_user( $con_a );

		$this->assertNull( $this->attempt_delete( 0, 'x' ) );
		$this->assertNull( $this->attempt_delete( -5, 'x' ) );
		$this->assertNull( $this->attempt_delete( 999999, $this->video_delete_nonce( 999999 ) ) );

		// Array where a scalar id is expected must not fatal.
		$_GET = array(
			'post'                         => array( 'x' => 'y' ),
			'action'                       => 'delete',
			'masvideos-delete-video-nonce' => 'x',
		);
		$this->assertNull( $this->run_handler( array( 'MasVideos_Form_Handler', 'delete_video' ) ) );
	}

	/** Code quality: a malformed (array) `redirect` param must not cause a warning/TypeError. */
	public function test_malformed_redirect_array_does_not_error() {
		$con_a = $this->make_user( 'contributor' );
		$video = $this->make_video( $con_a, 'draft' );

		wp_set_current_user( $con_a );
		$_GET = array(
			'post'                         => $video,
			'action'                       => 'trash',
			'masvideos-delete-video-nonce' => $this->video_delete_nonce( $video ),
			'redirect'                     => array( 'https://evil.example/' ),
		);
		// A TypeError from the array reaching trim()/wp_sanitize_redirect() would propagate here
		// (it is an Error, not MasVideos_Redirect_Exception) and fail the test.
		$redirect = $this->run_handler( array( 'MasVideos_Form_Handler', 'delete_video' ) );
		$this->assertNotNull( $redirect, 'Operation should still succeed, ignoring the malformed redirect.' );
		$this->assertSame( 'trash', get_post_status( $video ) );
	}

	/** T-12: unknown action is a no-op. */
	public function test_unknown_action_is_noop() {
		$con_a = $this->make_user( 'contributor' );
		$video = $this->make_video( $con_a, 'publish' );
		$snap  = $this->snapshot_post( $video );

		wp_set_current_user( $con_a );
		$this->assertNull( $this->attempt_delete( $video, $this->video_delete_nonce( $video ), 'frobnicate' ) );
		$this->assertPostUnchanged( $video, $snap );
	}
}
