<?php
/**
 * MV-08 regression tests: add_new_attribute_* restricted to the content type's own attribute
 * taxonomies and authorized with that taxonomy's real edit_terms capability.
 *
 * @package MasVideos/Tests
 * @group   security
 * @group   mv-08
 */

defined( 'ABSPATH' ) || exit;

class MasVideos_Test_Ajax_Attribute_Terms extends MasVideos_Unit_Test_Case {

	private $created_attribute_ids = array();

	public function tear_down() {
		foreach ( $this->created_attribute_ids as $id ) {
			masvideos_delete_attribute( $id );
		}
		$this->created_attribute_ids = array();
		parent::tear_down();
	}

	/**
	 * Create a real video attribute taxonomy and register it so get_taxonomy() resolves it.
	 *
	 * @param string $slug Attribute slug.
	 * @return string Full taxonomy name (e.g. 'video_testgenre').
	 */
	private function make_registered_video_attribute( $slug = 'testgenre' ) {
		$id = masvideos_create_attribute(
			array(
				'name'      => $slug,
				'slug'      => $slug,
				'type'      => 'select',
				'post_type' => 'video',
			)
		);
		$this->assertIsInt( $id, 'attribute should be created' );
		$this->created_attribute_ids[] = $id;

		// Re-run taxonomy registration so the new attribute taxonomy becomes a live taxonomy.
		MasVideos_Post_Types::register_taxonomies();

		return masvideos_attribute_taxonomy_name( 'video', $slug );
	}

	private function set_term_request( $taxonomy, $term ) {
		$_POST['security'] = wp_create_nonce( 'add-attribute-video' );
		$_POST['taxonomy'] = $taxonomy;
		$_POST['term']     = $term;
		$_REQUEST          = $_POST;
	}

	public function test_rejects_core_category_taxonomy() {
		wp_set_current_user( $this->make_user( 'administrator' ) );
		$this->set_term_request( 'category', 'EvilCat' );

		$result = $this->capture_ajax( array( 'MasVideos_AJAX', 'add_new_attribute_video' ) );

		$this->assertTrue( $result['died'] );
		$this->assertNull( $result['json'], 'an unrelated core taxonomy must be rejected (no success payload)' );
		$this->assertFalse( get_term_by( 'name', 'EvilCat', 'category' ), 'no term may be created in category (MV-08)' );
	}

	public function test_rejects_post_tag_taxonomy() {
		wp_set_current_user( $this->make_user( 'administrator' ) );
		$this->set_term_request( 'post_tag', 'EvilTag' );

		$result = $this->capture_ajax( array( 'MasVideos_AJAX', 'add_new_attribute_video' ) );

		$this->assertTrue( $result['died'] );
		$this->assertFalse( get_term_by( 'name', 'EvilTag', 'post_tag' ) );
	}

	public function test_rejects_other_content_types_attribute_taxonomy() {
		// A video endpoint must not create terms in a movie attribute taxonomy.
		$movie_tax = masvideos_create_attribute( array( 'name' => 'moviegenre', 'slug' => 'moviegenre', 'type' => 'select', 'post_type' => 'movie' ) );
		$this->assertIsInt( $movie_tax );
		$this->created_attribute_ids[] = $movie_tax;
		MasVideos_Post_Types::register_taxonomies();

		wp_set_current_user( $this->make_user( 'administrator' ) );
		$this->set_term_request( masvideos_attribute_taxonomy_name( 'movie', 'moviegenre' ), 'CrossType' );

		$result = $this->capture_ajax( array( 'MasVideos_AJAX', 'add_new_attribute_video' ) );

		$this->assertTrue( $result['died'] );
		$this->assertNull( $result['json'], 'a movie taxonomy is not in the video allowlist' );
		$this->assertFalse( get_term_by( 'name', 'CrossType', masvideos_attribute_taxonomy_name( 'movie', 'moviegenre' ) ) );
	}

	public function test_admin_can_create_term_in_video_attribute_taxonomy() {
		$taxonomy = $this->make_registered_video_attribute( 'genreok' );
		$this->assertTrue( taxonomy_exists( $taxonomy ) );

		wp_set_current_user( $this->make_user( 'administrator' ) );
		$this->set_term_request( $taxonomy, 'Drama' );

		$result = $this->capture_ajax( array( 'MasVideos_AJAX', 'add_new_attribute_video' ) );

		$this->assertIsArray( $result['json'], 'a successful creation returns JSON' );
		$this->assertArrayHasKey( 'term_id', $result['json'] );
		$this->assertInstanceOf( 'WP_Term', get_term_by( 'name', 'Drama', $taxonomy ), 'the term should now exist' );
	}

	public function test_contributor_without_edit_terms_cannot_create_term() {
		$taxonomy = $this->make_registered_video_attribute( 'genrelocked' );

		// Contributors have assign_video_terms but NOT edit_video_terms (the taxonomy's edit_terms cap).
		wp_set_current_user( $this->make_user( 'contributor' ) );
		$this->set_term_request( $taxonomy, 'ShouldNotExist' );

		$result = $this->capture_ajax( array( 'MasVideos_AJAX', 'add_new_attribute_video' ) );

		$this->assertTrue( $result['died'] );
		$this->assertNull( $result['json'] );
		$this->assertFalse( get_term_by( 'name', 'ShouldNotExist', $taxonomy ), 'lacking edit_terms must prevent creation' );
	}
}
