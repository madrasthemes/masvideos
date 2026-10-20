<?php
/**
 * MV-12 regression tests: playlist-toggle AJAX endpoints require a CSRF nonce and validate the
 * playlist type, the item type and the item's readability, in addition to ownership.
 *
 * @package MasVideos/Tests
 * @group   security
 * @group   mv-12
 */

defined( 'ABSPATH' ) || exit;

class MasVideos_Test_Ajax_Playlist_Csrf extends MasVideos_Unit_Test_Case {

	private function toggle_request( $playlist_id, $video_id, $nonce = null, $delete = false ) {
		$_POST = array(
			'playlist_id' => $playlist_id,
			'video_id'    => $video_id,
			'delete'      => $delete ? 'true' : 'false',
		);
		if ( null !== $nonce ) {
			$_POST['security'] = $nonce;
		}
		$_REQUEST = $_POST;
	}

	private function valid_nonce() {
		return wp_create_nonce( 'masvideos-playlist' );
	}

	public function test_anonymous_is_rejected() {
		$owner    = $this->make_user( 'subscriber' );
		$playlist = $this->make_playlist( 'video_playlist', $owner, 'private' );
		$video    = $this->make_video( $this->make_user( 'administrator' ), 'publish' );

		wp_set_current_user( 0 );
		$this->toggle_request( $playlist, $video, $this->valid_nonce() );
		$result = $this->capture_ajax( array( 'MasVideos_AJAX', 'toggle_video_playlist' ) );

		$this->assertTrue( (bool) $result['json']['error'] );
		$this->assertFalse( masvideos_is_video_added_to_playlist( $playlist, $video ) );
	}

	public function test_missing_nonce_is_rejected() {
		$owner    = $this->make_user( 'subscriber' );
		$playlist = $this->make_playlist( 'video_playlist', $owner, 'private' );
		$video    = $this->make_video( $this->make_user( 'administrator' ), 'publish' );

		wp_set_current_user( $owner );
		$this->toggle_request( $playlist, $video, null ); // no security field
		$result = $this->capture_ajax( array( 'MasVideos_AJAX', 'toggle_video_playlist' ) );

		$this->assertTrue( (bool) $result['json']['error'], 'a missing nonce must be rejected (CSRF)' );
		$this->assertFalse( masvideos_is_video_added_to_playlist( $playlist, $video ), 'nothing should be added without a nonce' );
	}

	public function test_invalid_nonce_is_rejected() {
		$owner    = $this->make_user( 'subscriber' );
		$playlist = $this->make_playlist( 'video_playlist', $owner, 'private' );
		$video    = $this->make_video( $this->make_user( 'administrator' ), 'publish' );

		wp_set_current_user( $owner );
		$this->toggle_request( $playlist, $video, 'not-a-real-nonce' );
		$result = $this->capture_ajax( array( 'MasVideos_AJAX', 'toggle_video_playlist' ) );

		$this->assertTrue( (bool) $result['json']['error'] );
		$this->assertFalse( masvideos_is_video_added_to_playlist( $playlist, $video ) );
	}

	public function test_non_owner_is_rejected() {
		$owner    = $this->make_user( 'subscriber' );
		$other    = $this->make_user( 'subscriber' );
		$playlist = $this->make_playlist( 'video_playlist', $owner, 'private' );
		$video    = $this->make_video( $this->make_user( 'administrator' ), 'publish' );

		wp_set_current_user( $other );
		$this->toggle_request( $playlist, $video, $this->valid_nonce() );
		$result = $this->capture_ajax( array( 'MasVideos_AJAX', 'toggle_video_playlist' ) );

		$this->assertTrue( (bool) $result['json']['error'] );
		$this->assertFalse( masvideos_is_video_added_to_playlist( $playlist, $video ) );
	}

	public function test_wrong_playlist_type_is_rejected() {
		$owner        = $this->make_user( 'subscriber' );
		$movie_pllist = $this->make_playlist( 'movie_playlist', $owner, 'private' );
		$video        = $this->make_video( $this->make_user( 'administrator' ), 'publish' );

		wp_set_current_user( $owner );
		$this->toggle_request( $movie_pllist, $video, $this->valid_nonce() );
		$result = $this->capture_ajax( array( 'MasVideos_AJAX', 'toggle_video_playlist' ) );

		$this->assertTrue( (bool) $result['json']['error'], 'a movie_playlist must not be toggled by the video endpoint' );
	}

	public function test_wrong_item_type_is_rejected() {
		$owner    = $this->make_user( 'subscriber' );
		$playlist = $this->make_playlist( 'video_playlist', $owner, 'private' );
		$movie    = self::factory()->post->create( array( 'post_type' => 'movie', 'post_status' => 'publish' ) );

		wp_set_current_user( $owner );
		$this->toggle_request( $playlist, $movie, $this->valid_nonce() );
		$result = $this->capture_ajax( array( 'MasVideos_AJAX', 'toggle_video_playlist' ) );

		$this->assertTrue( (bool) $result['json']['error'], 'a movie id must not be accepted as a video item' );
		$this->assertFalse( masvideos_is_video_added_to_playlist( $playlist, $movie ) );
	}

	public function test_another_users_private_video_cannot_be_added() {
		$owner        = $this->make_user( 'subscriber' );
		$other        = $this->make_user( 'subscriber' );
		$playlist     = $this->make_playlist( 'video_playlist', $owner, 'private' );
		$private_vid  = $this->make_video( $other, 'private', 'Someone Elses Private Video' );

		wp_set_current_user( $owner );
		$this->toggle_request( $playlist, $private_vid, $this->valid_nonce() );
		$result = $this->capture_ajax( array( 'MasVideos_AJAX', 'toggle_video_playlist' ) );

		$this->assertTrue( (bool) $result['json']['error'], 'a non-readable private item must be refused (disclosure)' );
		$this->assertFalse( masvideos_is_video_added_to_playlist( $playlist, $private_vid ) );
	}

	public function test_valid_add_and_remove_with_nonce() {
		$owner    = $this->make_user( 'subscriber' );
		$playlist = $this->make_playlist( 'video_playlist', $owner, 'private' );
		$video    = $this->make_video( $this->make_user( 'administrator' ), 'publish' );

		wp_set_current_user( $owner );

		// Add.
		$this->toggle_request( $playlist, $video, $this->valid_nonce(), false );
		$add = $this->capture_ajax( array( 'MasVideos_AJAX', 'toggle_video_playlist' ) );
		$this->assertTrue( (bool) $add['json']['success'], 'a valid nonced add should succeed' );
		$this->assertTrue( masvideos_is_video_added_to_playlist( $playlist, $video ) );

		// Remove.
		$this->toggle_request( $playlist, $video, $this->valid_nonce(), true );
		$remove = $this->capture_ajax( array( 'MasVideos_AJAX', 'toggle_video_playlist' ) );
		$this->assertTrue( (bool) $remove['json']['success'], 'a valid nonced remove should succeed' );
		$this->assertFalse( masvideos_is_video_added_to_playlist( $playlist, $video ) );
	}

	public function test_password_protected_published_item_cannot_be_added() {
		$owner    = $this->make_user( 'subscriber' );
		$admin    = $this->make_user( 'administrator' );
		$playlist = $this->make_playlist( 'video_playlist', $owner, 'private' );
		$protected = self::factory()->post->create(
			array(
				'post_type'     => 'video',
				'post_status'   => 'publish',
				'post_author'   => $admin,
				'post_password' => 'secret',
			)
		);

		wp_set_current_user( $owner );
		$this->toggle_request( $playlist, $protected, $this->valid_nonce() );
		$result = $this->capture_ajax( array( 'MasVideos_AJAX', 'toggle_video_playlist' ) );

		$this->assertTrue( (bool) $result['json']['error'], 'a published but password-protected item must not be addable by a non-owner' );
		$this->assertFalse( masvideos_is_video_added_to_playlist( $playlist, $protected ) );
	}

	public function test_trashed_playlist_cannot_be_modified() {
		$owner    = $this->make_user( 'subscriber' );
		$playlist = $this->make_playlist( 'video_playlist', $owner, 'publish' );
		$video    = $this->make_video( $this->make_user( 'administrator' ), 'publish' );
		wp_trash_post( $playlist );
		$this->assertSame( 'trash', get_post_status( $playlist ) );

		wp_set_current_user( $owner );
		$this->toggle_request( $playlist, $video, $this->valid_nonce() );
		$result = $this->capture_ajax( array( 'MasVideos_AJAX', 'toggle_video_playlist' ) );

		$this->assertTrue( (bool) $result['json']['error'], 'a trashed playlist must not be modifiable via a stale AJAX request' );
		$this->assertFalse( masvideos_is_video_added_to_playlist( $playlist, $video ) );
	}

	public function test_owner_can_add_their_own_private_video() {
		$owner    = $this->make_user( 'contributor' );
		$playlist = $this->make_playlist( 'video_playlist', $owner, 'private' );
		$own_vid  = $this->make_video( $owner, 'private', 'My Own Private Video' );

		wp_set_current_user( $owner );
		$this->toggle_request( $playlist, $own_vid, $this->valid_nonce(), false );
		$result = $this->capture_ajax( array( 'MasVideos_AJAX', 'toggle_video_playlist' ) );

		$this->assertTrue( (bool) $result['json']['success'], 'an owner may add their own readable private video' );
		$this->assertTrue( masvideos_is_video_added_to_playlist( $playlist, $own_vid ) );
	}
}
