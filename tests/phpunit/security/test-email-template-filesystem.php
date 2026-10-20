<?php
/**
 * Phase 2D2 Pass 4 (final filesystem correction) regression tests.
 *
 * Canonical-path containment (realpath/symlink-aware), atomic-via-temp write that preserves the
 * live template on a failed write, and capability/containment on move and delete — exercised on
 * disposable theme fixtures only, never a real customer template.
 *
 * @package MasVideos/Tests
 * @group   security
 * @group   email-filesystem
 */

defined( 'ABSPATH' ) || exit;

class MasVideos_Test_Email_Template_Filesystem extends MasVideos_Unit_Test_Case {

	private $tmp_dirs = array();
	private $theme;      // disposable stylesheet dir
	private $base;       // $theme/masvideos (allowed template dir)
	private $theme_cb;

	public function set_up() {
		parent::set_up();
		if ( ! class_exists( 'MasVideos_Email' ) ) {
			$path = MASVIDEOS_ABSPATH . 'includes/emails/class-masvideos-email.php';
			if ( file_exists( $path ) ) {
				require_once $path;
			}
		}
		wp_set_current_user( $this->make_user( 'administrator' ) );
		add_filter( 'user_has_cap', array( $this, 'grant_edit_themes' ) );

		$this->theme = $this->make_tmp_dir();
		$this->base  = $this->theme . '/masvideos';
		wp_mkdir_p( $this->base . '/emails' );
		$this->theme_cb = function () {
			return $this->theme;
		};
		add_filter( 'stylesheet_directory', $this->theme_cb );
		// WP 6.4+ caches get_stylesheet_directory() in $wp_stylesheet_path; clear it so the filter above
		// takes effect (otherwise a pre-cached real-theme path defeats the test's temp-theme override).
		$GLOBALS['wp_stylesheet_path'] = null;
		$GLOBALS['wp_template_path']   = null;
	}

	public function tear_down() {
		remove_filter( 'user_has_cap', array( $this, 'grant_edit_themes' ) );
		if ( $this->theme_cb ) {
			remove_filter( 'stylesheet_directory', $this->theme_cb );
		}
		$GLOBALS['wp_stylesheet_path'] = null;
		$GLOBALS['wp_template_path']   = null;
		foreach ( $this->tmp_dirs as $dir ) {
			$this->rrmdir( $dir );
		}
		$this->tmp_dirs = array();
		parent::tear_down();
	}

	public function grant_edit_themes( $allcaps ) {
		$allcaps['edit_themes'] = true;
		return $allcaps;
	}

	private function make_tmp_dir() {
		$dir = get_temp_dir() . 'masvideos-efs-' . wp_generate_password( 10, false );
		mkdir( $dir, 0777, true );
		$this->tmp_dirs[] = $dir;
		return $dir;
	}

	private function rrmdir( $dir ) {
		if ( ! is_dir( $dir ) && ! is_link( $dir ) ) {
			@unlink( $dir );
			return;
		}
		if ( is_link( $dir ) ) {
			@unlink( $dir );
			return;
		}
		foreach ( array_diff( scandir( $dir ), array( '.', '..' ) ) as $item ) {
			$path = $dir . '/' . $item;
			( is_dir( $path ) && ! is_link( $path ) ) ? $this->rrmdir( $path ) : @unlink( $path );
		}
		@rmdir( $dir );
	}

	private function email() {
		if ( ! class_exists( 'MasVideos_Email' ) ) {
			$this->markTestSkipped( 'MasVideos_Email unavailable' );
		}
		return new MasVideos_Email();
	}

	private function set_template_html( $email, $rel ) {
		$ref = new ReflectionProperty( $email, 'template_html' );
		$ref->setAccessible( true );
		$ref->setValue( $email, $rel );
	}

	private function invoke( $obj, $method, array $args = array() ) {
		$ref = new ReflectionMethod( $obj, $method );
		$ref->setAccessible( true );
		return $ref->invokeArgs( $obj, $args );
	}

	private function require_symlinks() {
		$probe = $this->base . '/.symlink-probe';
		$ok    = @symlink( __FILE__, $probe );
		if ( $ok ) {
			@unlink( $probe );
			return true;
		}
		$this->markTestSkipped( 'symlinks not supported on this filesystem' );
	}

	/* ===================== legitimate write ===================== */

	public function test_legitimate_template_write_replaces_content() {
		$target = $this->base . '/emails/legit.php';
		file_put_contents( $target, "OLD\n" );
		$email = $this->email();
		$this->invoke( $email, 'save_template', array( "NEW CONTENT\n", 'emails/legit.php' ) );
		$this->assertStringEqualsFile( $target, "NEW CONTENT\n" );
	}

	/* ===================== symlink containment (write) ===================== */

	public function test_symlinked_destination_file_outside_is_rejected() {
		$this->require_symlinks();
		$outside = $this->make_tmp_dir() . '/secret.php';
		file_put_contents( $outside, "SECRET\n" );
		$link = $this->base . '/emails/evil.php';
		symlink( $outside, $link );

		$email    = $this->email();
		$location = $this->run_handler( function () use ( $email ) {
			$this->invoke( $email, 'save_template', array( "PWNED\n", 'emails/evil.php' ) );
		} );

		$this->assertNotNull( $location, 'symlinked-out destination must redirect (rejected)' );
		$this->assertStringContainsString( 'masvideos_error=template_file_not_writable', $location );
		$this->assertStringEqualsFile( $outside, "SECRET\n", 'file outside the allowed dir is byte-for-byte unchanged' );
	}

	public function test_symlinked_parent_directory_outside_is_rejected() {
		$this->require_symlinks();
		$outside_dir = $this->make_tmp_dir();
		$outside     = $outside_dir . '/thing.php';
		file_put_contents( $outside, "SECRET\n" );
		// A directory inside the allowed base that is actually a symlink to an outside directory.
		symlink( $outside_dir, $this->base . '/linkdir' );

		$email    = $this->email();
		$location = $this->run_handler( function () use ( $email ) {
			$this->invoke( $email, 'save_template', array( "PWNED\n", 'linkdir/thing.php' ) );
		} );

		$this->assertNotNull( $location );
		$this->assertStringContainsString( 'masvideos_error=template_file_not_writable', $location );
		$this->assertStringEqualsFile( $outside, "SECRET\n", 'file under a symlinked parent is unchanged' );
	}

	public function test_sibling_prefix_directory_is_rejected() {
		// Directly exercise the containment resolver: ".../masvideos-evil/x" must not count as inside ".../masvideos".
		$sibling = $this->theme . '/masvideos-evil';
		wp_mkdir_p( $sibling );
		$file = $sibling . '/x.php';
		file_put_contents( $file, "X\n" );

		$email  = $this->email();
		$result = $this->invoke( $email, 'resolve_contained_template_path', array( $file, $this->base ) );
		$this->assertFalse( $result, 'a sibling-prefix directory must not be treated as contained' );

		// And a genuine child resolves to its canonical path.
		$inside = $this->base . '/emails/ok.php';
		file_put_contents( $inside, "OK\n" );
		$ok = $this->invoke( $email, 'resolve_contained_template_path', array( $inside, $this->base ) );
		$this->assertIsString( $ok );
		$this->assertStringEndsWith( '/masvideos/emails/ok.php', $ok );
	}

	public function test_stream_wrapper_and_nul_paths_rejected() {
		$email = $this->email();
		$this->assertFalse( $this->invoke( $email, 'resolve_contained_template_path', array( 'php://temp', $this->base ) ) );
		$this->assertFalse( $this->invoke( $email, 'resolve_contained_template_path', array( "a\0b.php", $this->base ) ) );
	}

	/* ===================== missing / permission / write-failure ===================== */

	public function test_missing_destination_fails_closed() {
		$email    = $this->email();
		$location = $this->run_handler( function () use ( $email ) {
			$this->invoke( $email, 'save_template', array( "NEW\n", 'emails/does-not-exist.php' ) );
		} );
		$this->assertNotNull( $location, 'a missing target fails closed (editor only rewrites existing files)' );
		$this->assertFileDoesNotExist( $this->base . '/emails/does-not-exist.php' );
	}

	public function test_permission_failure_preserves_file() {
		$target = $this->base . '/emails/ro.php';
		file_put_contents( $target, "KEEP\n" );
		chmod( $target, 0444 );
		if ( is_writable( $target ) ) {
			$this->markTestSkipped( 'cannot make file read-only (running as root?)' );
		}
		$email    = $this->email();
		$location = $this->run_handler( function () use ( $email ) {
			$this->invoke( $email, 'save_template', array( "HACKED\n", 'emails/ro.php' ) );
		} );
		chmod( $target, 0644 );
		$this->assertNotNull( $location );
		$this->assertStringEqualsFile( $target, "KEEP\n", 'read-only template is untouched' );
	}

	public function test_write_failure_preserves_original_byte_for_byte() {
		// Original is writable, but its directory is read-only so the temp file cannot be created.
		// The temp-then-move strategy must leave the live template completely intact.
		$dir = $this->base . '/locked';
		wp_mkdir_p( $dir );
		$target = $dir . '/tmpl.php';
		file_put_contents( $target, "LIVE TEMPLATE\n" );
		chmod( $dir, 0555 );
		if ( is_writable( $dir ) ) {
			chmod( $dir, 0755 );
			$this->markTestSkipped( 'cannot make directory read-only (running as root?)' );
		}

		$email    = $this->email();
		$location = $this->run_handler( function () use ( $email ) {
			$this->invoke( $email, 'save_template', array( "REPLACEMENT\n", 'locked/tmpl.php' ) );
		} );
		chmod( $dir, 0755 );

		$this->assertNotNull( $location, 'a failed write reports the not-writable error' );
		$this->assertStringEqualsFile( $target, "LIVE TEMPLATE\n", 'the live template is preserved byte-for-byte on write failure' );
		// No stray temp file is left behind in the directory.
		$leftovers = glob( $dir . '/masvideos-email-tmpl-*.tmp' );
		$this->assertSame( array(), $leftovers, 'no temporary file is left behind' );
	}

	public function test_permissions_preserved_across_replace() {
		$target = $this->base . '/emails/perm.php';
		file_put_contents( $target, "OLD\n" );
		chmod( $target, 0640 );
		clearstatcache();
		$before = substr( sprintf( '%o', fileperms( $target ) ), -3 );

		$email = $this->email();
		$this->invoke( $email, 'save_template', array( "NEW\n", 'emails/perm.php' ) );

		$this->assertStringEqualsFile( $target, "NEW\n" );
		clearstatcache();
		$after = substr( sprintf( '%o', fileperms( $target ) ), -3 );
		$this->assertSame( $before, $after, 'existing file permissions are carried across the atomic replace' );
	}

	/* ===================== delete ===================== */

	public function test_valid_deletion() {
		$target = $this->base . '/emails/del.php';
		file_put_contents( $target, "DELETE ME\n" );
		$email = $this->email();
		$this->set_template_html( $email, 'emails/del.php' );
		$this->invoke( $email, 'delete_template_action', array( 'template_html' ) );
		$this->assertFileDoesNotExist( $target );
	}

	public function test_symlinked_deletion_target_outside_is_not_deleted() {
		$this->require_symlinks();
		$outside = $this->make_tmp_dir() . '/keep.php';
		file_put_contents( $outside, "KEEP OUTSIDE\n" );
		symlink( $outside, $this->base . '/emails/del-evil.php' );

		$email = $this->email();
		$this->set_template_html( $email, 'emails/del-evil.php' );
		$this->invoke( $email, 'delete_template_action', array( 'template_html' ) );

		$this->assertFileExists( $outside, 'a symlink escaping the allowed dir must not be followed for deletion' );
		$this->assertStringEqualsFile( $outside, "KEEP OUTSIDE\n" );
	}

	/* ===================== move / copy confinement ===================== */

	public function test_move_copies_within_theme() {
		$email = $this->email();
		$this->set_template_html( $email, 'emails/moved.php' );
		// Point the core-template source at a real fixture file.
		$src = $this->make_tmp_dir() . '/core.php';
		file_put_contents( $src, "CORE TEMPLATE\n" );
		$cb = function () use ( $src ) {
			return $src;
		};
		add_filter( 'masvideos_locate_core_template', $cb );
		$this->invoke( $email, 'move_template_action', array( 'template_html' ) );
		remove_filter( 'masvideos_locate_core_template', $cb );

		$this->assertFileExists( $this->base . '/emails/moved.php' );
		$this->assertStringEqualsFile( $this->base . '/emails/moved.php', "CORE TEMPLATE\n" );
	}

	public function test_move_traversal_filter_creates_no_directory_outside_theme() {
		// A malicious masvideos_template_directory filter returns a traversal that would place the
		// destination (and its parent directory) outside the theme. Nothing must be created there.
		$escape_dir  = dirname( $this->theme ) . '/masvideos-escape-' . wp_generate_password( 6, false );
		$escape_file = $escape_dir . '/moved.php';
		$this->assertFileDoesNotExist( $escape_dir );

		$dir_cb = function () use ( $escape_dir ) {
			return '../' . basename( $escape_dir );
		};
		add_filter( 'masvideos_template_directory', $dir_cb );

		$email = $this->email();
		$this->set_template_html( $email, 'moved.php' );
		$src = $this->make_tmp_dir() . '/core.php';
		file_put_contents( $src, "CORE\n" );
		$src_cb = function () use ( $src ) {
			return $src;
		};
		add_filter( 'masvideos_locate_core_template', $src_cb );
		$this->invoke( $email, 'move_template_action', array( 'template_html' ) );
		remove_filter( 'masvideos_locate_core_template', $src_cb );
		remove_filter( 'masvideos_template_directory', $dir_cb );

		$this->assertDirectoryDoesNotExist( $escape_dir, 'wp_mkdir_p must not create a directory outside the theme' );
		$this->assertFileDoesNotExist( $escape_file );
		// Clean up only if the defect regressed and created it.
		if ( is_dir( $escape_dir ) ) {
			$this->rrmdir( $escape_dir );
		}
	}

	public function test_move_creatable_check_rejects_symlinked_ancestor_before_mkdir() {
		$this->require_symlinks();
		// An existing ancestor inside the theme is a symlink to an outside directory.
		$outside_dir = $this->make_tmp_dir();
		symlink( $outside_dir, $this->base . '/linksub' );
		$email = $this->email();
		// The would-be destination is theme/masvideos/linksub/deep/moved.php (deep/ does not exist yet).
		$target = $this->base . '/linksub/deep/moved.php';
		$this->assertFalse(
			$this->invoke( $email, 'template_path_is_creatable_within_theme', array( $target ) ),
			'a symlinked existing ancestor that escapes the theme must be rejected before any mkdir'
		);
		$this->assertDirectoryDoesNotExist( $outside_dir . '/deep' );
	}

	public function test_move_destination_outside_allowed_is_rejected() {
		$this->require_symlinks();
		// Redirect the template subdirectory to a symlink that escapes the theme.
		$outside_dir = $this->make_tmp_dir();
		$sentinel    = $outside_dir . '/moved.php';
		symlink( $outside_dir, $this->theme . '/masvideos-link' );
		$dir_cb = function () {
			return 'masvideos-link';
		};
		add_filter( 'masvideos_template_directory', $dir_cb );

		$email = $this->email();
		$this->set_template_html( $email, 'moved.php' );
		$src = $this->make_tmp_dir() . '/core.php';
		file_put_contents( $src, "CORE\n" );
		$src_cb = function () use ( $src ) {
			return $src;
		};
		add_filter( 'masvideos_locate_core_template', $src_cb );
		$this->invoke( $email, 'move_template_action', array( 'template_html' ) );
		remove_filter( 'masvideos_locate_core_template', $src_cb );
		remove_filter( 'masvideos_template_directory', $dir_cb );

		$this->assertFileDoesNotExist( $sentinel, 'move must not copy into a symlinked directory outside the theme' );
	}

	/* ===================== filesystem init ===================== */

	public function test_failed_filesystem_init_blocks_write_and_keeps_state_consistent() {
		$target = $this->base . '/emails/fsinit.php';
		file_put_contents( $target, "KEEP\n" );

		$GLOBALS['wp_filesystem'] = null;
		add_filter( 'filesystem_method', array( $this, 'force_ftp_method' ) );

		$email    = $this->email();
		$location = $this->run_handler( function () use ( $email ) {
			$this->invoke( $email, 'save_template', array( "NEW\n", 'emails/fsinit.php' ) );
		} );

		remove_filter( 'filesystem_method', array( $this, 'force_ftp_method' ) );
		$GLOBALS['wp_filesystem'] = null;

		$this->assertNotNull( $location, 'a credential-required method fails closed (no prompt)' );
		$this->assertStringEqualsFile( $target, "KEEP\n", 'nothing is written when the filesystem cannot initialize directly' );

		// After resetting, a direct initialization still works (no inconsistent global left behind).
		$this->assertInstanceOf( 'WP_Filesystem_Base', masvideos_wp_filesystem() );
	}

	public function test_wp_filesystem_helper_uses_preinitialized_direct_instance() {
		$fs = masvideos_wp_filesystem();
		$this->assertInstanceOf( 'WP_Filesystem_Base', $fs );
		// A second call returns the same preinitialized direct instance.
		$this->assertSame( $fs, masvideos_wp_filesystem() );
	}

	public function force_ftp_method() {
		return 'ftp';
	}
}
