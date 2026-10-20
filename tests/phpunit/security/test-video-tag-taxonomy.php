<?php
/**
 * MV-18 regression tests: frontend video editing uses the field's own taxonomy (never a submitted
 * one), requires assign/edit term capabilities, and does not create or assign terms in arbitrary
 * taxonomies.
 *
 * @package MasVideos/Tests
 * @group   security
 * @group   mv-18
 */

defined( 'ABSPATH' ) || exit;

class MasVideos_Test_Video_Tag_Taxonomy extends MasVideos_Unit_Test_Case {

	private function submit_edit_video( $args ) {
		$_POST = array_merge(
			array(
				'edit-video'                 => '1',
				'masvideos-edit-video-nonce' => wp_create_nonce( 'masvideos-edit-video' ),
				'title'                      => 'Edited Video',
			),
			$args
		);
		$_REQUEST = $_POST;
		return $this->run_handler( array( 'MasVideos_Form_Handler', 'edit_video' ) );
	}

	public function test_submitted_taxonomy_is_ignored_for_tag_creation() {
		add_filter( 'masvideos_allow_users_to_create_tag', '__return_true' );

		$admin = $this->make_user( 'administrator' );
		$video = $this->make_video( $admin, 'draft', 'Tagging Video' );
		wp_set_current_user( $admin );

		$this->submit_edit_video(
			array(
				'id'       => $video,
				'taxonomy' => 'category', // malicious injected taxonomy — must be ignored
				'tag_ids'  => array( 'BrandNewTag' ),
			)
		);

		$this->assertFalse( get_term_by( 'name', 'BrandNewTag', 'category' ), 'no term may be created in the injected taxonomy (MV-18)' );
		$created = get_term_by( 'name', 'BrandNewTag', 'video_tag' );
		$this->assertInstanceOf( 'WP_Term', $created, 'the tag is created in the field taxonomy (video_tag)' );
		$this->assertContains( (int) $created->term_id, wp_get_post_terms( $video, 'video_tag', array( 'fields' => 'ids' ) ) );

		remove_filter( 'masvideos_allow_users_to_create_tag', '__return_true' );
	}

	public function test_contributor_without_edit_terms_cannot_create_tag() {
		add_filter( 'masvideos_allow_users_to_create_tag', '__return_true' );

		$contributor = $this->make_user( 'contributor' );
		$video       = $this->make_video( $contributor, 'draft', 'Contributor Tagging' );
		wp_set_current_user( $contributor );

		$this->submit_edit_video(
			array(
				'id'      => $video,
				'tag_ids' => array( 'ContributorMadeTag' ),
			)
		);

		$this->assertFalse( get_term_by( 'name', 'ContributorMadeTag', 'video_tag' ), 'creation requires edit_video_terms which contributors lack' );

		remove_filter( 'masvideos_allow_users_to_create_tag', '__return_true' );
	}

	public function test_existing_tag_can_still_be_selected() {
		$admin = $this->make_user( 'administrator' );
		$video = $this->make_video( $admin, 'draft' );
		wp_set_current_user( $admin );

		$term = wp_insert_term( 'ExistingTag', 'video_tag' );
		$this->assertIsArray( $term );

		// Default tag field is a term-multiselect of IDs.
		$this->submit_edit_video(
			array(
				'id'      => $video,
				'tag_ids' => array( (string) $term['term_id'] ),
			)
		);

		$this->assertContains( (int) $term['term_id'], wp_get_post_terms( $video, 'video_tag', array( 'fields' => 'ids' ) ), 'authorized selection of an existing tag is preserved' );
	}

	public function test_foreign_taxonomy_term_id_is_not_assigned() {
		$admin = $this->make_user( 'administrator' );
		$video = $this->make_video( $admin, 'draft' );
		wp_set_current_user( $admin );

		$real_cat    = wp_insert_term( 'RealVideoCat', 'video_cat' );
		$foreign_cat = wp_insert_term( 'ForeignCoreCat', 'category' );
		$this->assertIsArray( $real_cat );
		$this->assertIsArray( $foreign_cat );

		$this->submit_edit_video(
			array(
				'id'           => $video,
				'taxonomy'     => 'post_tag', // ignored
				'category_ids' => array( (string) $real_cat['term_id'], (string) $foreign_cat['term_id'] ),
			)
		);

		$assigned = wp_get_post_terms( $video, 'video_cat', array( 'fields' => 'ids' ) );
		$this->assertContains( (int) $real_cat['term_id'], $assigned, 'a valid video_cat term is assigned' );
		$this->assertNotContains( (int) $foreign_cat['term_id'], $assigned, 'a term from another taxonomy must not be assigned (MV-18)' );
	}
}
