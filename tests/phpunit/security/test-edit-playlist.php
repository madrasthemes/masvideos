<?php
/**
 * MV-05 regression tests: MasVideos_Form_Handler::edit_playlist().
 *
 * @package MasVideos/Tests
 * @group   security
 * @group   mv-05
 */

defined( 'ABSPATH' ) || exit;

class MasVideos_Test_Edit_Playlist extends MasVideos_Unit_Test_Case {

	public function playlist_types() {
		return array(
			'movie'   => array( 'movie_playlist' ),
			'video'   => array( 'video_playlist' ),
			'tv_show' => array( 'tv_show_playlist' ),
		);
	}

	protected function attempt_edit( $overrides = array() ) {
		$_POST = array_merge(
			array(
				'edit-playlist'                 => 'Update',
				'masvideos-edit-playlist-nonce' => wp_create_nonce( 'masvideos-edit-playlist' ),
				'id'                            => 0,
				'title'                         => 'Playlist Title',
				'visibility'                    => 'private',
				'post_type'                     => 'movie_playlist',
			),
			$overrides
		);
		return $this->run_handler( array( 'MasVideos_Form_Handler', 'edit_playlist' ) );
	}

	protected function latest_playlist_of( $type, $uid ) {
		$posts = get_posts(
			array(
				'post_type'   => $type,
				'author'      => $uid,
				'post_status' => 'any',
				'numberposts' => 1,
			)
		);
		return $posts ? $posts[0] : null;
	}

	/**
	 * T-22: a subscriber cannot edit another subscriber's playlist.
	 *
	 * @dataProvider playlist_types
	 */
	public function test_subscriber_cannot_edit_others_playlist( $type ) {
		$sub_a    = $this->make_user( 'subscriber' );
		$sub_b    = $this->make_user( 'subscriber' );
		$playlist = $this->make_playlist( $type, $sub_b, 'private', 'B Private List' );
		$snap     = $this->snapshot_post( $playlist );

		wp_set_current_user( $sub_a );
		$result = $this->attempt_edit(
			array(
				'id'         => $playlist,
				'post_type'  => $type,
				'title'      => 'Hijacked',
				'visibility' => 'publish',
			)
		);

		$this->assertNull( $result );
		$this->assertPostUnchanged( $playlist, $snap );
		$this->assertSame( 'private', get_post_status( $playlist ), 'Another user cannot flip a private playlist to public.' );
	}

	/**
	 * T-23: a subscriber can edit their own playlist.
	 *
	 * @dataProvider playlist_types
	 */
	public function test_subscriber_can_edit_own_playlist( $type ) {
		$sub_a    = $this->make_user( 'subscriber' );
		$playlist = $this->make_playlist( $type, $sub_a, 'private', 'Before' );

		wp_set_current_user( $sub_a );
		$result = $this->attempt_edit(
			array(
				'id'         => $playlist,
				'post_type'  => $type,
				'title'      => 'After',
				'visibility' => 'publish',
			)
		);

		$this->assertNotNull( $result );
		$this->assertSame( 'After', get_post( $playlist )->post_title );
		$this->assertSame( 'publish', get_post_status( $playlist ) );
	}

	/**
	 * T-24: a subscriber can create a new playlist, owned by them.
	 *
	 * @dataProvider playlist_types
	 */
	public function test_subscriber_can_create_playlist( $type ) {
		$sub_a = $this->make_user( 'subscriber' );

		wp_set_current_user( $sub_a );
		$result = $this->attempt_edit(
			array(
				'id'         => 0,
				'post_type'  => $type,
				'title'      => 'My New List',
				'visibility' => 'private',
			)
		);

		$this->assertNotNull( $result );
		$created = $this->latest_playlist_of( $type, $sub_a );
		$this->assertInstanceOf( 'WP_Post', $created );
		$this->assertSame( 'My New List', $created->post_title );
		$this->assertEquals( $sub_a, (int) $created->post_author );
	}

	/**
	 * T-25: an invalid visibility is coerced to an allowed status, never applied verbatim.
	 *
	 * @dataProvider playlist_types
	 */
	public function test_invalid_visibility_is_coerced( $type ) {
		$sub_a    = $this->make_user( 'subscriber' );
		$playlist = $this->make_playlist( $type, $sub_a, 'publish', 'Vis Test' );

		wp_set_current_user( $sub_a );
		$result = $this->attempt_edit(
			array(
				'id'         => $playlist,
				'post_type'  => $type,
				'title'      => 'Vis Test',
				'visibility' => 'trash', // not an allowed visibility option
			)
		);

		$this->assertNotNull( $result );
		$this->assertContains( get_post_status( $playlist ), array( 'publish', 'private' ), 'Visibility must stay within the allowed set.' );
	}

	/** T-26: submitting an unsupported post_type is rejected. */
	public function test_unsupported_post_type_rejected() {
		$sub_a = $this->make_user( 'subscriber' );
		wp_set_current_user( $sub_a );

		$result = $this->attempt_edit( array( 'id' => 0, 'post_type' => 'page', 'title' => 'X' ) );
		$this->assertNull( $result );
	}

	/**
	 * T-26: the stored-vs-submitted type check is isolated — the user OWNS the playlist
	 * (so ownership cannot be the reason for rejection), but submits a mismatched type.
	 * It must still be rejected, with no change.
	 */
	public function test_type_mismatch_on_owned_playlist_rejected() {
		$sub_a = $this->make_user( 'subscriber' );

		// The subscriber OWNS this movie_playlist.
		$playlist = $this->make_playlist( 'movie_playlist', $sub_a, 'private', 'Mine' );
		$snap     = $this->snapshot_post( $playlist );

		wp_set_current_user( $sub_a );
		$result = $this->attempt_edit(
			array(
				'id'         => $playlist,
				'post_type'  => 'video_playlist', // claim the wrong type for an owned movie_playlist
				'title'      => 'Renamed',
				'visibility' => 'publish',
			)
		);

		$this->assertNull( $result );
		$this->assertPostUnchanged( $playlist, $snap );
		$this->assertSame( 'private', get_post_status( $playlist ), 'A mismatched type must not change visibility.' );
	}
}
