<?php
/**
 * Base test case for MAS Videos integration tests.
 *
 * Provides fixtures (users, videos, playlists, attachments), request-global isolation,
 * redirect/exit interception, and side-effect assertions.
 *
 * @package MasVideos/Tests
 */

defined( 'ABSPATH' ) || exit;

class MasVideos_Unit_Test_Case extends WP_UnitTestCase {

	/**
	 * Captured redirect location from the most recent intercepted redirect, or null.
	 *
	 * @var string|null
	 */
	protected $last_redirect = null;

	/**
	 * Set up each test.
	 */
	public function set_up() {
		parent::set_up();

		// Deterministic capabilities for every test. On a brand-new install (e.g. a fresh CI database)
		// the capabilities create_roles() writes to the {prefix}user_roles option are not yet reflected
		// in the cached alloptions / the WP_Roles singleton for this request, which would make the first
		// run's ownership/capability authorizations fail. Rebuild the roles cache so every run — cold or
		// warm — sees the plugin capabilities.
		MasVideos_Install::create_roles();
		wp_cache_delete( 'alloptions', 'options' );
		wp_cache_delete( $GLOBALS['wpdb']->get_blog_prefix() . 'user_roles', 'options' );
		$GLOBALS['wp_roles'] = null;
		wp_roles();

		// Front-end-ish request context so session/notice helpers behave.
		if ( empty( $_SERVER['REQUEST_URI'] ) ) {
			$_SERVER['REQUEST_URI'] = '/';
		}

		// Clean request state.
		$this->reset_request_state();

		// Intercept wp_redirect()/exit without changing production behaviour.
		$this->last_redirect = null;
		add_filter( 'wp_redirect', array( $this, 'intercept_redirect' ), 1 );

		// Clear any notices left over from a previous test.
		if ( function_exists( 'masvideos_clear_notices' ) && is_object( MasVideos()->session ) ) {
			masvideos_clear_notices();
		}
	}

	/**
	 * Tear down each test.
	 */
	public function tear_down() {
		remove_filter( 'wp_redirect', array( $this, 'intercept_redirect' ), 1 );
		$this->reset_request_state();
		wp_set_current_user( 0 );
		parent::tear_down();
	}

	/**
	 * Restore global request state between tests.
	 */
	protected function reset_request_state() {
		$_GET     = array();
		$_POST    = array();
		$_REQUEST = array();
	}

	/**
	 * Throw instead of redirecting+exiting.
	 *
	 * @param string $location Redirect location.
	 * @throws MasVideos_Redirect_Exception Always.
	 * @return never
	 */
	public function intercept_redirect( $location ) {
		$this->last_redirect = $location;
		throw new MasVideos_Redirect_Exception( $location );
	}

	/**
	 * Invoke a handler, capturing a success redirect if one occurs.
	 *
	 * @param callable $callback Handler to run.
	 * @return string|null Redirect location if the handler redirected (success), else null.
	 */
	protected function run_handler( $callback ) {
		try {
			call_user_func( $callback );
		} catch ( MasVideos_Redirect_Exception $e ) {
			return $e->location;
		}
		return null;
	}

	/**
	 * Invoke an AJAX handler, capturing its echoed output and whether it called wp_die().
	 *
	 * AJAX handlers terminate with wp_send_json()/wp_die(); in the test harness those throw a
	 * WPDieException. This runs the handler inside an output buffer, swallows that exception, and
	 * returns the raw output plus a decoded JSON payload when the output is JSON.
	 *
	 * @param callable $callback Handler to run.
	 * @return array { bool 'died', string 'output', mixed 'json' }
	 */
	protected function capture_ajax( $callback ) {
		$died   = false;
		$output = '';

		// Simulate an AJAX request so wp_send_json()/wp_die() take the throwable path (otherwise
		// wp_send_json() would call die; and terminate the PHPUnit process). The die handler throws
		// an Error subclass, mirroring the finality of die(): a handler's own catch( Exception ) must
		// not swallow it and go on to emit a second JSON envelope.
		$throw_handler = function () {
			return function ( $message = '', $title = '', $args = array() ) {
				throw new MasVideos_Ajax_Die_Exception( is_scalar( $message ) ? (string) $message : '' );
			};
		};
		add_filter( 'wp_doing_ajax', '__return_true' );
		add_filter( 'wp_die_ajax_handler', $throw_handler );
		add_filter( 'wp_die_handler', $throw_handler );

		// Some handlers call ob_start() and terminate via wp_send_json()/wp_die() without closing it.
		// Record the buffer level so we can drain every buffer opened at/after our own, capturing
		// their contents and leaving the buffer stack exactly as we found it.
		$base_ob_level = ob_get_level();
		ob_start();
		try {
			call_user_func( $callback );
		} catch ( MasVideos_Ajax_Die_Exception $e ) {
			$died = true;
		}
		while ( ob_get_level() > $base_ob_level ) {
			$output = ob_get_clean() . $output;
		}

		remove_filter( 'wp_doing_ajax', '__return_true' );
		remove_filter( 'wp_die_ajax_handler', $throw_handler );
		remove_filter( 'wp_die_handler', $throw_handler );

		$json = null;
		if ( '' !== $output ) {
			$decoded = json_decode( $output, true );
			if ( null !== $decoded || 'null' === $output ) {
				$json = $decoded;
			}
		}

		return array(
			'died'   => $died,
			'output' => $output,
			'json'   => $json,
		);
	}

	/* ---------------------------------------------------------------------
	 * Fixtures
	 * ------------------------------------------------------------------- */

	/**
	 * Create a user with a role.
	 *
	 * @param string $role Role slug.
	 * @return int User ID.
	 */
	protected function make_user( $role ) {
		return self::factory()->user->create( array( 'role' => $role ) );
	}

	/**
	 * Create a video post owned by a user.
	 *
	 * @param int    $author  Author user ID.
	 * @param string $status  Post status.
	 * @param string $title   Title.
	 * @return int Post ID.
	 */
	protected function make_video( $author, $status = 'publish', $title = 'Test Video' ) {
		return self::factory()->post->create(
			array(
				'post_type'   => 'video',
				'post_author' => $author,
				'post_status' => $status,
				'post_title'  => $title,
			)
		);
	}

	/**
	 * Create a playlist post owned by a user.
	 *
	 * @param string $type   One of movie_playlist|video_playlist|tv_show_playlist.
	 * @param int    $author Author user ID.
	 * @param string $status Post status (publish|private).
	 * @param string $title  Title.
	 * @return int Post ID.
	 */
	protected function make_playlist( $type, $author, $status = 'private', $title = 'Test Playlist' ) {
		return self::factory()->post->create(
			array(
				'post_type'   => $type,
				'post_author' => $author,
				'post_status' => $status,
				'post_title'  => $title,
			)
		);
	}

	/**
	 * Create a real attachment backed by a file on disk.
	 *
	 * @param int $author Author user ID.
	 * @return array { 'id' => int, 'file' => string path }
	 */
	protected function make_attachment_with_file( $author ) {
		$upload_dir = wp_upload_dir();
		$file       = trailingslashit( $upload_dir['basedir'] ) . 'masvideos-test-' . wp_generate_password( 8, false ) . '.txt';
		file_put_contents( $file, 'masvideos attachment fixture' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents

		$id = self::factory()->attachment->create_object(
			$file,
			0,
			array(
				'post_mime_type' => 'text/plain',
				'post_author'    => $author,
				'post_type'      => 'attachment',
			)
		);

		return array(
			'id'   => $id,
			'file' => get_attached_file( $id ),
		);
	}

	/* ---------------------------------------------------------------------
	 * Nonces
	 * ------------------------------------------------------------------- */

	/**
	 * Create an object-bound delete nonce for a video (as the current user).
	 *
	 * @param int $id Video ID.
	 * @return string
	 */
	protected function video_delete_nonce( $id ) {
		return wp_create_nonce( 'masvideos-delete-video_' . $id );
	}

	/**
	 * Create an object-bound delete nonce for a playlist (as the current user).
	 *
	 * @param int $id Playlist ID.
	 * @return string
	 */
	protected function playlist_delete_nonce( $id ) {
		return wp_create_nonce( 'masvideos-delete-playlist_' . $id );
	}

	/* ---------------------------------------------------------------------
	 * Side-effect assertions
	 * ------------------------------------------------------------------- */

	/**
	 * Snapshot the fields of a post that must not change on a rejected operation.
	 *
	 * @param int $id Post ID.
	 * @return array
	 */
	protected function snapshot_post( $id ) {
		$post = get_post( $id );
		$this->assertInstanceOf( 'WP_Post', $post, "Post {$id} should exist before the operation." );

		return array(
			'post_title'    => $post->post_title,
			'post_status'   => $post->post_status,
			'post_type'     => $post->post_type,
			'post_author'   => $post->post_author,
			'post_content'  => $post->post_content,
			'post_name'     => $post->post_name,
			'categories'    => wp_get_post_terms( $id, 'video_cat', array( 'fields' => 'ids' ) ),
			'tags'          => wp_get_post_terms( $id, 'video_tag', array( 'fields' => 'ids' ) ),
		);
	}

	/**
	 * Assert a post still exists and its snapshot fields are unchanged.
	 *
	 * @param int   $id       Post ID.
	 * @param array $snapshot Snapshot from snapshot_post().
	 */
	protected function assertPostUnchanged( $id, $snapshot ) {
		$post = get_post( $id );
		$this->assertInstanceOf( 'WP_Post', $post, "Post {$id} must still exist after a rejected operation." );
		$this->assertSame( $snapshot['post_title'], $post->post_title, 'post_title changed' );
		$this->assertSame( $snapshot['post_status'], $post->post_status, 'post_status changed' );
		$this->assertSame( $snapshot['post_type'], $post->post_type, 'post_type changed' );
		$this->assertSame( $snapshot['post_author'], $post->post_author, 'post_author changed' );
		$this->assertSame( $snapshot['post_content'], $post->post_content, 'post_content changed' );
	}
}
