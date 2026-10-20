<?php
/**
 * MV-06 regression tests: private playlist disclosure.
 *
 * @package MasVideos/Tests
 * @group   security
 * @group   mv-06
 */

defined( 'ABSPATH' ) || exit;

class MasVideos_Test_Playlist_Privacy extends MasVideos_Unit_Test_Case {

	public function playlist_types() {
		return array(
			'movie'   => array( 'movie_playlist' ),
			'video'   => array( 'video_playlist' ),
			'tv_show' => array( 'tv_show_playlist' ),
		);
	}

	/* ---------------- authorization helper (all three types) ---------------- */

	/** @dataProvider playlist_types */
	public function test_anonymous_can_read_public_playlist( $type ) {
		wp_set_current_user( 0 );
		$pl = $this->make_playlist( $type, $this->make_user( 'subscriber' ), 'publish', 'Public List' );
		$this->assertTrue( masvideos_current_user_can_read_playlist( $pl, $type ) );
	}

	/** @dataProvider playlist_types */
	public function test_anonymous_cannot_read_private_playlist( $type ) {
		wp_set_current_user( 0 );
		$pl = $this->make_playlist( $type, $this->make_user( 'subscriber' ), 'private', 'Secret List' );
		$this->assertFalse( masvideos_current_user_can_read_playlist( $pl, $type ) );
	}

	/** @dataProvider playlist_types */
	public function test_non_owner_cannot_read_private_playlist( $type ) {
		$owner = $this->make_user( 'subscriber' );
		$other = $this->make_user( 'subscriber' );
		$pl    = $this->make_playlist( $type, $owner, 'private', 'Secret List' );

		wp_set_current_user( $other );
		$this->assertFalse( masvideos_current_user_can_read_playlist( $pl, $type ) );
	}

	/** @dataProvider playlist_types */
	public function test_owner_can_read_own_private_playlist( $type ) {
		$owner = $this->make_user( 'subscriber' );
		$pl    = $this->make_playlist( $type, $owner, 'private', 'Secret List' );

		wp_set_current_user( $owner );
		$this->assertTrue( masvideos_current_user_can_read_playlist( $pl, $type ) );
	}

	/** @dataProvider playlist_types */
	public function test_admin_can_read_private_playlist( $type ) {
		$pl = $this->make_playlist( $type, $this->make_user( 'subscriber' ), 'private', 'Secret List' );
		wp_set_current_user( $this->make_user( 'administrator' ) );
		$this->assertTrue( masvideos_current_user_can_read_playlist( $pl, $type ) );
	}

	public function test_invalid_ids_and_wrong_types_are_denied() {
		wp_set_current_user( $this->make_user( 'administrator' ) );
		$this->assertFalse( masvideos_current_user_can_read_playlist( 0, 'video_playlist' ) );
		$this->assertFalse( masvideos_current_user_can_read_playlist( 999999, 'video_playlist' ) );

		// A real post of the WRONG type must not be treated as a playlist.
		$page = self::factory()->post->create( array( 'post_type' => 'page', 'post_status' => 'private' ) );
		$this->assertFalse( masvideos_current_user_can_read_playlist( $page, 'video_playlist' ) );

		$movie_pl = $this->make_playlist( 'movie_playlist', $this->make_user( 'subscriber' ), 'private' );
		$this->assertFalse( masvideos_current_user_can_read_playlist( $movie_pl, 'video_playlist' ), 'type must match exactly' );
	}

	public function test_history_playlist_is_private_to_others() {
		// The automatic History playlist is a private playlist.
		$owner   = $this->make_user( 'subscriber' );
		$other   = $this->make_user( 'subscriber' );
		$history = $this->make_playlist( 'video_playlist', $owner, 'private', 'History' );

		wp_set_current_user( $other );
		$this->assertFalse( masvideos_current_user_can_read_playlist( $history, 'video_playlist' ) );
		wp_set_current_user( $owner );
		$this->assertTrue( masvideos_current_user_can_read_playlist( $history, 'video_playlist' ) );
	}

	/* ---------------- trashed / non-public states ---------------- */

	/** @dataProvider playlist_types */
	public function test_trashed_playlist_is_never_exposed( $type ) {
		$owner = $this->make_user( 'subscriber' );
		$admin = $this->make_user( 'administrator' );
		$pl    = $this->make_playlist( $type, $owner, 'publish', 'Was Public' );
		wp_trash_post( $pl ); // status -> trash (EMPTY_TRASH_DAYS default in the normal suite).
		$this->assertSame( 'trash', get_post_status( $pl ) );

		foreach ( array( 0, $owner, $admin ) as $uid ) {
			wp_set_current_user( $uid );
			$this->assertFalse( masvideos_current_user_can_read_playlist( $pl, $type ), "trashed must be hidden for user {$uid}" );
		}
	}

	public function test_draft_playlist_is_never_exposed() {
		$owner = $this->make_user( 'subscriber' );
		$pl    = $this->make_playlist( 'video_playlist', $owner, 'draft', 'Draft List' );

		wp_set_current_user( $owner );
		$this->assertFalse( masvideos_current_user_can_read_playlist( $pl, 'video_playlist' ), 'owner' );
		wp_set_current_user( $this->make_user( 'administrator' ) );
		$this->assertFalse( masvideos_current_user_can_read_playlist( $pl, 'video_playlist' ), 'admin' );
	}

	/* ---------------- end-to-end: single-video related playlist renderer ---------------- */

	private function render_related_playlist( $playlist_id, $current_video_id ) {
		$GLOBALS['video'] = masvideos_get_video( $current_video_id );
		$_GET['video_playlist_id'] = $playlist_id;
		ob_start();
		masvideos_template_single_video_related_playlist_videos();
		$out = ob_get_clean();
		unset( $GLOBALS['video'] );
		return $out;
	}

	private function make_video_playlist_with_items( $author, $status, $title, $video_ids ) {
		$pl = $this->make_playlist( 'video_playlist', $author, $status, $title );
		update_post_meta( $pl, '_video_ids', array_map( 'absint', $video_ids ) );
		return $pl;
	}

	public function test_renderer_hides_private_playlist_from_anonymous() {
		$owner    = $this->make_user( 'subscriber' );
		$admin    = $this->make_user( 'administrator' );
		$in_list  = $this->make_video( $admin, 'publish', 'In List' );
		$current  = $this->make_video( $admin, 'publish', 'Current' );
		$playlist = $this->make_video_playlist_with_items( $owner, 'private', 'SecretPlaylistName', array( $in_list ) );

		wp_set_current_user( 0 );
		$out = $this->render_related_playlist( $playlist, $current );

		$this->assertSame( '', trim( $out ), 'No markup at all should be emitted for an unauthorized private playlist.' );
		$this->assertStringNotContainsString( 'SecretPlaylistName', $out );
	}

	public function test_renderer_hides_private_playlist_from_non_owner() {
		$owner    = $this->make_user( 'subscriber' );
		$other    = $this->make_user( 'subscriber' );
		$admin    = $this->make_user( 'administrator' );
		$in_list  = $this->make_video( $admin, 'publish', 'In List' );
		$current  = $this->make_video( $admin, 'publish', 'Current' );
		$playlist = $this->make_video_playlist_with_items( $owner, 'private', 'SecretPlaylistName', array( $in_list ) );

		wp_set_current_user( $other );
		$out = $this->render_related_playlist( $playlist, $current );
		$this->assertStringNotContainsString( 'SecretPlaylistName', $out );
	}

	public function test_renderer_shows_private_playlist_to_owner() {
		$owner    = $this->make_user( 'subscriber' );
		$admin    = $this->make_user( 'administrator' );
		$in_list  = $this->make_video( $admin, 'publish', 'In List' );
		$current  = $this->make_video( $admin, 'publish', 'Current' );
		$playlist = $this->make_video_playlist_with_items( $owner, 'private', 'OwnerPlaylistName', array( $in_list ) );

		wp_set_current_user( $owner );
		$out = $this->render_related_playlist( $playlist, $current );
		$this->assertStringContainsString( 'OwnerPlaylistName', $out, 'Owner should see their own playlist.' );
	}

	public function test_renderer_hides_trashed_playlist_from_owner() {
		$owner    = $this->make_user( 'subscriber' );
		$admin    = $this->make_user( 'administrator' );
		$in_list  = $this->make_video( $admin, 'publish', 'In List' );
		$current  = $this->make_video( $admin, 'publish', 'Current' );
		$playlist = $this->make_video_playlist_with_items( $owner, 'publish', 'WasPublicNowTrashed', array( $in_list ) );
		wp_trash_post( $playlist );

		wp_set_current_user( $owner );
		$out = $this->render_related_playlist( $playlist, $current );
		$this->assertStringNotContainsString( 'WasPublicNowTrashed', $out, 'A trashed playlist must not render, even for its owner.' );
	}

	public function test_renderer_shows_public_playlist_to_anonymous() {
		$owner    = $this->make_user( 'subscriber' );
		$admin    = $this->make_user( 'administrator' );
		$in_list  = $this->make_video( $admin, 'publish', 'In List' );
		$current  = $this->make_video( $admin, 'publish', 'Current' );
		$playlist = $this->make_video_playlist_with_items( $owner, 'publish', 'PublicPlaylistName', array( $in_list ) );

		wp_set_current_user( 0 );
		$out = $this->render_related_playlist( $playlist, $current );
		$this->assertStringContainsString( 'PublicPlaylistName', $out, 'Public playlists remain visible to anyone.' );
	}
}
