<?php
/**
 * MV-13 / S-01 / S-03 regression tests: attribute post-type allowlisting, prepared queries and
 * bounded/expiring transient caching.
 *
 * @package MasVideos/Tests
 * @group   security
 * @group   mv-13
 */

defined( 'ABSPATH' ) || exit;

class MasVideos_Test_Attribute_Functions extends MasVideos_Unit_Test_Case {

	private $created_attribute_ids = array();

	public function tear_down() {
		foreach ( $this->created_attribute_ids as $id ) {
			masvideos_delete_attribute( $id );
		}
		$this->created_attribute_ids = array();
		parent::tear_down();
	}

	public function test_disallowed_post_type_returns_empty() {
		$this->assertSame( array(), masvideos_get_attribute_taxonomies( 'category' ) );
		$this->assertSame( array(), masvideos_get_attribute_taxonomies( 'post' ) );
	}

	public function test_sql_injection_payload_post_type_returns_empty() {
		// A payload is not an allowlisted type, so it is rejected before reaching the query.
		$this->assertSame( array(), masvideos_get_attribute_taxonomies( "video' OR '1'='1" ) );
		$this->assertSame( array(), masvideos_get_attribute_taxonomies( "video; DROP TABLE wp_posts; --" ) );
	}

	public function test_allowed_post_type_returns_its_attributes() {
		$id = masvideos_create_attribute( array( 'name' => 'quality', 'slug' => 'quality', 'type' => 'select', 'post_type' => 'video' ) );
		$this->assertIsInt( $id );
		$this->created_attribute_ids[] = $id;

		$taxes = masvideos_get_attribute_taxonomies( 'video' );
		$names = wp_list_pluck( $taxes, 'attribute_name' );
		$this->assertContains( 'quality', $names );
	}

	public function test_create_attribute_rejects_invalid_post_type() {
		$result = masvideos_create_attribute( array( 'name' => 'evil', 'slug' => 'evil', 'type' => 'select', 'post_type' => 'category' ) );
		$this->assertWPError( $result );
		$this->assertSame( 'invalid_masvideos_attribute_post_type', $result->get_error_code() );
	}

	public function test_create_attribute_rejects_empty_post_type() {
		$result = masvideos_create_attribute( array( 'name' => 'nopt', 'slug' => 'nopt', 'type' => 'select', 'post_type' => '' ) );
		$this->assertWPError( $result );
	}

	public function test_create_attribute_accepts_valid_post_type() {
		$id = masvideos_create_attribute( array( 'name' => 'studio', 'slug' => 'studio', 'type' => 'select', 'post_type' => 'movie' ) );
		$this->assertIsInt( $id );
		$this->assertGreaterThan( 0, $id );
		$this->created_attribute_ids[] = $id;
	}

	public function test_transient_is_stored_with_expiry() {
		if ( wp_using_ext_object_cache() ) {
			$this->markTestSkipped( 'Transient timeouts are not stored in options when an external object cache is active.' );
		}

		delete_transient( 'masvideos_video_attribute_taxonomies' );
		masvideos_get_attribute_taxonomies( 'video' );

		$timeout = get_option( '_transient_timeout_masvideos_video_attribute_taxonomies' );
		$this->assertNotFalse( $timeout, 'the cached attribute list must have an expiry (not autoloaded forever)' );
		$this->assertGreaterThan( time(), (int) $timeout );
	}

	public function test_post_type_allowlist_is_filterable() {
		$types = masvideos_get_attribute_taxonomy_post_types();
		foreach ( array( 'video', 'movie', 'tv_show', 'episode', 'person' ) as $expected ) {
			$this->assertContains( $expected, $types );
		}
	}
}
