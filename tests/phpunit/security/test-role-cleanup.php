<?php
/**
 * MV-19 regression tests: capabilities the plugin grants to the built-in contributor role are
 * removed on uninstall, while unrelated/ambiguous core capabilities are preserved.
 *
 * @package MasVideos/Tests
 * @group   security
 * @group   mv-19
 */

defined( 'ABSPATH' ) || exit;

class MasVideos_Test_Role_Cleanup extends MasVideos_Unit_Test_Case {

	/**
	 * Rebuild the global roles from the stored option so cap changes made by create_roles()/
	 * remove_roles() are observed (WP_Roles keeps cached role objects in-process; production
	 * rebuilds them on the next request).
	 */
	private function reload_roles() {
		$GLOBALS['wp_roles'] = null;
		wp_roles();
	}

	/**
	 * This class deliberately mutates the global roles. The parent rolls back the database, but the
	 * in-memory WP_Roles cache is not transactional, so drop and rebuild it from the restored
	 * database to avoid leaking stripped capabilities into later tests.
	 */
	public function tear_down() {
		parent::tear_down();
		// The parent rolls back the database, but the autoloaded-options cache (which holds
		// wp_user_roles) and the WP_Roles singleton are not transactional. Flush both so later
		// tests see the restored capabilities.
		wp_cache_delete( 'alloptions', 'options' );
		wp_cache_delete( 'wp_user_roles', 'options' );
		$GLOBALS['wp_roles'] = null;
		wp_roles();
	}

	private $plugin_contributor_caps = array(
		'edit_video',
		'read_video',
		'delete_video',
		'edit_videos',
		'delete_videos',
		'manage_video_terms',
		'assign_video_terms',
	);

	public function test_install_grants_contributor_caps() {
		// create_roles() runs in the base set_up().
		$contributor = get_role( 'contributor' );
		$this->assertNotNull( $contributor );

		foreach ( $this->plugin_contributor_caps as $cap ) {
			$this->assertTrue( $contributor->has_cap( $cap ), "contributor should have {$cap} after install" );
		}
		$this->assertTrue( $contributor->has_cap( 'upload_files' ) );
	}

	public function test_uninstall_removes_plugin_contributor_caps() {
		MasVideos_Install::remove_roles();

		$this->reload_roles();

		$contributor = get_role( 'contributor' );

		foreach ( $this->plugin_contributor_caps as $cap ) {
			$this->assertFalse( $contributor->has_cap( $cap ), "contributor must not retain {$cap} after uninstall (MV-19)" );
		}
	}

	public function test_uninstall_preserves_upload_files_by_default() {
		MasVideos_Install::remove_roles();

		$this->reload_roles();

		$contributor = get_role( 'contributor' );
		$this->assertTrue( $contributor->has_cap( 'upload_files' ), 'the ambiguous core cap upload_files is preserved by default' );
	}

	public function test_preserve_filter_allows_removing_upload_files() {
		$cb = function () {
			return array(); // preserve nothing
		};
		add_filter( 'masvideos_preserve_contributor_capabilities', $cb );

		MasVideos_Install::remove_roles();

		$this->reload_roles();

		$contributor = get_role( 'contributor' );
		$this->assertFalse( $contributor->has_cap( 'upload_files' ), 'opting out of preservation removes upload_files too' );

		remove_filter( 'masvideos_preserve_contributor_capabilities', $cb );
	}

	public function test_uninstall_removes_admin_core_caps() {
		MasVideos_Install::remove_roles();

		$this->reload_roles();

		$admin = get_role( 'administrator' );
		$this->assertFalse( $admin->has_cap( 'manage_masvideos' ), 'administrator matrix is also removed on uninstall' );
		$this->assertFalse( $admin->has_cap( 'edit_others_videos' ) );
	}

	public function test_unrelated_contributor_caps_are_not_touched() {
		// A core contributor capability the plugin never manages must survive cleanup.
		MasVideos_Install::remove_roles();

		$this->reload_roles();

		$contributor = get_role( 'contributor' );
		$this->assertTrue( $contributor->has_cap( 'edit_posts' ), 'core contributor capabilities must be left intact' );
		$this->assertTrue( $contributor->has_cap( 'read' ) );
	}
}
