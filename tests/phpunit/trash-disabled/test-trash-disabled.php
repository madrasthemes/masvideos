<?php
/**
 * MV-01 trash-safety regression tests, run with WordPress Trash DISABLED.
 *
 * With EMPTY_TRASH_DAYS = 0, wp_trash_post() permanently deletes instead of trashing.
 * These tests exercise that REAL core behaviour (no mocking) to prove that an
 * ownership-only `action=trash` can never cause a permanent deletion: it fails closed
 * and requires the delete capability, exactly like `action=delete`.
 *
 * This file only runs via phpunit-trash-disabled.xml.dist, which is launched with
 * MASVIDEOS_TEST_EMPTY_TRASH_DAYS=0 so the bootstrap defines EMPTY_TRASH_DAYS = 0 for real.
 *
 * @package MasVideos/Tests
 * @group   security
 * @group   mv-01
 * @group   trash-disabled
 */

defined( 'ABSPATH' ) || exit;

class MasVideos_Test_Trash_Disabled extends MasVideos_Unit_Test_Case {

	public function playlist_types() {
		return array(
			'movie'   => array( 'movie_playlist' ),
			'video'   => array( 'video_playlist' ),
			'tv_show' => array( 'tv_show_playlist' ),
		);
	}

	protected function attempt_delete_video( $post_id, $nonce, $action = 'trash' ) {
		$_GET = array(
			'post'                         => $post_id,
			'action'                       => $action,
			'masvideos-delete-video-nonce' => $nonce,
		);
		return $this->run_handler( array( 'MasVideos_Form_Handler', 'delete_video' ) );
	}

	protected function attempt_delete_playlist( $post_id, $nonce, $action = 'trash' ) {
		$_GET = array(
			'post'                            => $post_id,
			'action'                          => $action,
			'masvideos-delete-playlist-nonce' => $nonce,
		);
		return $this->run_handler( array( 'MasVideos_Form_Handler', 'delete_playlist' ) );
	}

	/** Sanity: this suite really runs with Trash disabled. */
	public function test_trash_is_actually_disabled() {
		$this->assertSame( 0, (int) EMPTY_TRASH_DAYS, 'This suite must run with EMPTY_TRASH_DAYS = 0.' );
		$this->assertFalse( masvideos_trash_is_recoverable(), 'Trash must be reported as non-recoverable.' );
	}

	/**
	 * #3: With Trash disabled, an owner WITHOUT the permanent-delete capability cannot cause a
	 * permanent deletion through action=trash. The request must be rejected and the video intact.
	 */
	public function test_owner_cannot_permanently_delete_published_video_via_trash() {
		$con_a = $this->make_user( 'contributor' );
		$video = $this->make_video( $con_a, 'publish', 'Published' );
		$snap  = $this->snapshot_post( $video );

		wp_set_current_user( $con_a );
		$this->assertNull( $this->attempt_delete_video( $video, $this->video_delete_nonce( $video ), 'trash' ) );
		$this->assertPostUnchanged( $video, $snap );
		$this->assertSame( 'publish', get_post_status( $video ), 'Video must remain published (not trashed, not deleted).' );
	}

	/**
	 * #3 (playlists): same protection for all three playlist types — a subscriber owner cannot
	 * permanently delete their own private playlist via action=trash when Trash is disabled.
	 *
	 * @dataProvider playlist_types
	 */
	public function test_owner_cannot_permanently_delete_playlist_via_trash( $type ) {
		$sub  = $this->make_user( 'subscriber' );
		$pl   = $this->make_playlist( $type, $sub, 'private', 'Mine' );
		$snap = $this->snapshot_post( $pl );

		wp_set_current_user( $sub );
		$this->assertNull( $this->attempt_delete_playlist( $pl, $this->playlist_delete_nonce( $pl ), 'trash' ) );
		$this->assertPostUnchanged( $pl, $snap );
		$this->assertInstanceOf( 'WP_Post', get_post( $pl ), 'Playlist must still exist.' );
	}

	/**
	 * #4: Under the same configuration, an administrator (capability holder) CAN perform the
	 * operation, which — consistent with the site's configured behaviour — permanently deletes.
	 */
	public function test_admin_delete_is_consistent_with_config() {
		$admin = $this->make_user( 'administrator' );
		$owner = $this->make_user( 'contributor' );
		$video = $this->make_video( $owner, 'publish', 'Published' );

		wp_set_current_user( $admin );
		$redirect = $this->attempt_delete_video( $video, $this->video_delete_nonce( $video ), 'trash' );

		$this->assertNotNull( $redirect, 'Admin should be authorized.' );
		$this->assertNull( get_post( $video ), 'With Trash disabled, wp_trash_post() permanently deletes for a capability holder.' );
	}

	/** #4 (playlist): admin permanent deletion via trash under disabled Trash. */
	public function test_admin_delete_playlist_is_consistent_with_config() {
		$admin = $this->make_user( 'administrator' );
		$sub   = $this->make_user( 'subscriber' );
		$pl    = $this->make_playlist( 'video_playlist', $sub, 'private', 'Mine' );

		wp_set_current_user( $admin );
		$redirect = $this->attempt_delete_playlist( $pl, $this->playlist_delete_nonce( $pl ), 'trash' );

		$this->assertNotNull( $redirect );
		$this->assertNull( get_post( $pl ) );
	}

	/**
	 * #6/#7: even with Trash disabled, post-type restrictions and object-bound nonces hold, and a
	 * rejected request leaves unrelated content (incl. an attachment file) untouched.
	 */
	public function test_post_type_and_nonce_guards_hold_with_trash_disabled() {
		$admin = $this->make_user( 'administrator' );
		$sub   = $this->make_user( 'subscriber' );

		$att   = $this->make_attachment_with_file( $admin );
		$video = $this->make_video( $admin, 'publish' );
		$vsnap = $this->snapshot_post( $video );

		wp_set_current_user( $sub );

		// Attachment via the video delete handler -> post-type guard rejects; file preserved.
		$this->assertNull( $this->attempt_delete_video( $att['id'], $this->video_delete_nonce( $att['id'] ), 'trash' ) );
		$this->assertInstanceOf( 'WP_Post', get_post( $att['id'] ) );
		$this->assertFileExists( $att['file'] );

		// A video via the playlist handler -> post-type guard rejects; video unchanged.
		$this->assertNull( $this->attempt_delete_playlist( $video, $this->playlist_delete_nonce( $video ), 'trash' ) );
		$this->assertPostUnchanged( $video, $vsnap );

		// Object-bound nonce: a nonce for a different id is rejected.
		$mine  = $this->make_playlist( 'video_playlist', $sub, 'private' );
		$other = $this->make_playlist( 'video_playlist', $sub, 'private' );
		$msnap = $this->snapshot_post( $mine );
		$this->assertNull( $this->attempt_delete_playlist( $mine, $this->playlist_delete_nonce( $other ), 'trash' ) );
		$this->assertPostUnchanged( $mine, $msnap );
	}
}
