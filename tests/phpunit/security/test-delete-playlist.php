<?php
/**
 * MV-01 regression tests: MasVideos_Form_Handler::delete_playlist().
 *
 * @package MasVideos/Tests
 * @group   security
 * @group   mv-01
 */

defined( 'ABSPATH' ) || exit;

class MasVideos_Test_Delete_Playlist extends MasVideos_Unit_Test_Case {

	public function playlist_types() {
		return array(
			'movie'   => array( 'movie_playlist' ),
			'video'   => array( 'video_playlist' ),
			'tv_show' => array( 'tv_show_playlist' ),
		);
	}

	protected function attempt_delete( $post_id, $nonce, $action = 'delete' ) {
		$_GET = array(
			'post'                            => $post_id,
			'action'                          => $action,
			'masvideos-delete-playlist-nonce' => $nonce,
		);
		return $this->run_handler( array( 'MasVideos_Form_Handler', 'delete_playlist' ) );
	}

	/**
	 * T-09: a subscriber cannot delete another subscriber's playlist.
	 *
	 * @dataProvider playlist_types
	 */
	public function test_subscriber_cannot_delete_others_playlist( $type ) {
		$sub_a    = $this->make_user( 'subscriber' );
		$sub_b    = $this->make_user( 'subscriber' );
		$playlist = $this->make_playlist( $type, $sub_b, 'private' );
		$snap     = $this->snapshot_post( $playlist );

		wp_set_current_user( $sub_a );
		$this->assertNull( $this->attempt_delete( $playlist, $this->playlist_delete_nonce( $playlist ) ) );
		$this->assertPostUnchanged( $playlist, $snap );
	}

	/**
	 * T-10: a subscriber can recoverably TRASH their own playlist (the account UI "Delete" action).
	 *
	 * @dataProvider playlist_types
	 */
	public function test_subscriber_can_trash_own_playlist( $type ) {
		$sub_a    = $this->make_user( 'subscriber' );
		$playlist = $this->make_playlist( $type, $sub_a, 'private' );

		wp_set_current_user( $sub_a );
		$redirect = $this->attempt_delete( $playlist, $this->playlist_delete_nonce( $playlist ), 'trash' );

		$this->assertNotNull( $redirect, 'Owner should be able to trash their own playlist.' );
		$this->assertSame( 'trash', get_post_status( $playlist ), 'Trash must be recoverable, not a hard delete.' );
	}

	/**
	 * T-10 (refined policy): a subscriber CANNOT permanently delete their own playlist
	 * (no delete_private_* capability); that needs a capability holder.
	 *
	 * @dataProvider playlist_types
	 */
	public function test_subscriber_cannot_permanently_delete_own_playlist( $type ) {
		$sub_a    = $this->make_user( 'subscriber' );
		$playlist = $this->make_playlist( $type, $sub_a, 'private' );
		$snap     = $this->snapshot_post( $playlist );

		wp_set_current_user( $sub_a );
		$this->assertNull( $this->attempt_delete( $playlist, $this->playlist_delete_nonce( $playlist ), 'delete' ) );
		$this->assertPostUnchanged( $playlist, $snap );
	}

	/** An administrator may permanently delete a playlist (capability path). */
	public function test_admin_can_permanently_delete_playlist() {
		$admin    = $this->make_user( 'administrator' );
		$sub      = $this->make_user( 'subscriber' );
		$playlist = $this->make_playlist( 'video_playlist', $sub, 'private' );

		wp_set_current_user( $admin );
		$this->assertNotNull( $this->attempt_delete( $playlist, $this->playlist_delete_nonce( $playlist ), 'delete' ) );
		$this->assertNull( get_post( $playlist ), 'Admin permanent delete should remove the playlist.' );
	}

	/** T-09 (cont.): the playlist handler refuses non-playlist post types (page/post/attachment/video). */
	public function test_playlist_handler_refuses_other_post_types() {
		$admin = $this->make_user( 'administrator' );
		$sub   = $this->make_user( 'subscriber' );

		$page  = self::factory()->post->create( array( 'post_type' => 'page', 'post_author' => $admin ) );
		$video = $this->make_video( $admin, 'publish' );
		$att   = $this->make_attachment_with_file( $admin );

		$page_snap  = $this->snapshot_post( $page );
		$video_snap = $this->snapshot_post( $video );

		wp_set_current_user( $sub );
		$this->assertNull( $this->attempt_delete( $page, $this->playlist_delete_nonce( $page ) ) );
		$this->assertNull( $this->attempt_delete( $video, $this->playlist_delete_nonce( $video ) ) );
		$this->assertNull( $this->attempt_delete( $att['id'], $this->playlist_delete_nonce( $att['id'] ) ) );

		$this->assertPostUnchanged( $page, $page_snap );
		$this->assertPostUnchanged( $video, $video_snap );
		$this->assertFileExists( $att['file'] );
	}

	/** T-11: a nonce minted for one playlist cannot delete another (object binding). */
	public function test_playlist_nonce_object_binding() {
		$sub = $this->make_user( 'subscriber' );
		$a   = $this->make_playlist( 'video_playlist', $sub, 'private' );
		$b   = $this->make_playlist( 'video_playlist', $sub, 'private' );
		$snap_a = $this->snapshot_post( $a );

		wp_set_current_user( $sub );
		$this->assertNull( $this->attempt_delete( $a, $this->playlist_delete_nonce( $b ) ) );
		$this->assertPostUnchanged( $a, $snap_a );
	}

	/** T-12: unknown action is a no-op. */
	public function test_unknown_action_is_noop() {
		$sub      = $this->make_user( 'subscriber' );
		$playlist = $this->make_playlist( 'movie_playlist', $sub, 'private' );
		$snap     = $this->snapshot_post( $playlist );

		wp_set_current_user( $sub );
		$this->assertNull( $this->attempt_delete( $playlist, $this->playlist_delete_nonce( $playlist ), 'wat' ) );
		$this->assertPostUnchanged( $playlist, $snap );
	}

	/** T-08-equivalent: malformed ids rejected cleanly. */
	public function test_invalid_ids_rejected_cleanly() {
		$sub = $this->make_user( 'subscriber' );
		wp_set_current_user( $sub );
		$this->assertNull( $this->attempt_delete( 0, 'x' ) );
		$this->assertNull( $this->attempt_delete( 999999, $this->playlist_delete_nonce( 999999 ) ) );
	}
}
