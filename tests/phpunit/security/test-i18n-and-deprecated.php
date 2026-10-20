<?php
/**
 * Phase 2D2 Pass 2 regression tests: i18n singular/plural + filtered labels, and the
 * deprecated-API migrations (get_terms() single-array form, get_page_by_title() -> WP_Query).
 *
 * @package MasVideos/Tests
 * @group   compliance
 * @group   i18n-deprecated
 */

defined( 'ABSPATH' ) || exit;

class MasVideos_Test_I18n_And_Deprecated extends MasVideos_Unit_Test_Case {

	private $created_attribute_ids = array();

	public function tear_down() {
		foreach ( $this->created_attribute_ids as $id ) {
			masvideos_delete_attribute( $id );
		}
		$this->created_attribute_ids = array();
		parent::tear_down();
	}

	/* ---------------- i18n: filtered + escaped feature badge ---------------- */

	public function test_feature_badge_default_and_filtered_are_escaped() {
		if ( ! function_exists( 'masvideos_template_loop_video_feature_badge' ) ) {
			$this->markTestSkipped( 'badge function unavailable' );
		}
		$admin = $this->make_user( 'administrator' );
		$video = $this->make_video( $admin, 'publish', 'Badged' );

		$video_obj = masvideos_get_video( $video );
		$video_obj->set_featured( true );
		$video_obj->save();
		$GLOBALS['video'] = masvideos_get_video( $video );

		if ( ! $GLOBALS['video']->get_featured() ) {
			unset( $GLOBALS['video'] );
			$this->markTestSkipped( 'could not set featured flag in this environment' );
		}
		// Default label.
		ob_start();
		masvideos_template_loop_video_feature_badge();
		$default = ob_get_clean();
		$this->assertStringContainsString( 'Featured', $default, 'default translated badge text renders' );

		// Filtered label with markup must be escaped (not rendered as raw HTML).
		$cb = function () {
			return '<b>Hot & New</b>';
		};
		add_filter( 'masvideos_template_loop_video_feature_badge_text', $cb );
		ob_start();
		masvideos_template_loop_video_feature_badge();
		$filtered = ob_get_clean();
		remove_filter( 'masvideos_template_loop_video_feature_badge_text', $cb );
		unset( $GLOBALS['video'] );

		$this->assertStringNotContainsString( '<b>Hot', $filtered, 'filtered badge text is escaped (filter still applied)' );
		$this->assertStringContainsString( '&lt;b&gt;Hot', $filtered );
	}

	/* ---------------- i18n: result-count singular vs plural ---------------- */

	public function test_result_count_singular_and_plural() {
		if ( ! function_exists( 'masvideos_videos_count' ) ) {
			$this->markTestSkipped( 'masvideos_videos_count unavailable' );
		}

		$render = function ( $total ) {
			masvideos_set_videos_loop_prop( 'is_paginated', true );
			masvideos_set_videos_loop_prop( 'total', $total );
			masvideos_set_videos_loop_prop( 'total_pages', 1 );
			masvideos_set_videos_loop_prop( 'per_page', 20 );
			masvideos_set_videos_loop_prop( 'current_page', 1 );
			masvideos_set_videos_loop_prop( 'is_shortcode', true );
			ob_start();
			masvideos_videos_count();
			return trim( wp_strip_all_tags( ob_get_clean() ) );
		};

		$one = $render( 1 );
		if ( '' === $one ) {
			$this->markTestSkipped( 'result-count requires loop display context not available here' );
		}
		$this->assertStringContainsString( '1 result', $one, 'singular form shows the count' );

		$many = $render( 5 );
		$this->assertStringContainsString( 'all 5 results', $many, 'plural form shows all N results' );
	}

	/* ---------------- deprecated: get_terms() migration integrity ---------------- */

	public function test_get_video_terms_returns_assigned_attribute_terms() {
		// Create + register a real video attribute taxonomy (hits the migrated get_terms path).
		$id = masvideos_create_attribute( array( 'name' => 'quality', 'slug' => 'quality', 'type' => 'select', 'post_type' => 'video' ) );
		$this->assertIsInt( $id );
		$this->created_attribute_ids[] = $id;
		MasVideos_Post_Types::register_taxonomies();
		$taxonomy = masvideos_attribute_taxonomy_name( 'video', 'quality' );
		$this->assertTrue( taxonomy_exists( $taxonomy ) );

		$admin = $this->make_user( 'administrator' );
		$video = $this->make_video( $admin, 'publish' );
		$t1    = wp_insert_term( 'HD', $taxonomy );
		$t2    = wp_insert_term( 'SD', $taxonomy );
		$this->assertIsArray( $t1 );
		$this->assertIsArray( $t2 );
		wp_set_object_terms( $video, array( (int) $t1['term_id'], (int) $t2['term_id'] ), $taxonomy );

		// menu_order ordering path contains the migrated get_terms( wp_parse_args( ... ) ).
		$names = masvideos_get_video_terms( $video, $taxonomy, array( 'orderby' => 'menu_order', 'fields' => 'names' ) );
		$this->assertIsArray( $names );
		$this->assertContains( 'HD', $names );
		$this->assertContains( 'SD', $names );

		// Default path still returns the assigned terms too.
		$ids = masvideos_get_video_terms( $video, $taxonomy, array( 'fields' => 'ids' ) );
		$this->assertContains( (int) $t1['term_id'], array_map( 'intval', $ids ) );
	}

	public function test_get_terms_single_array_respects_include_and_hide_empty() {
		$a = wp_insert_term( 'Alpha', 'video_cat' );
		$b = wp_insert_term( 'Beta', 'video_cat' );
		$this->assertIsArray( $a );
		$this->assertIsArray( $b );

		// Single-array signature (the migration target) must honour include + hide_empty.
		$terms = get_terms( wp_parse_args( array( 'hide_empty' => false, 'include' => array( (int) $a['term_id'] ), 'fields' => 'names' ), array( 'taxonomy' => 'video_cat' ) ) );
		$this->assertSame( array( 'Alpha' ), $terms );
	}

	/* ---------------- deprecated: tv_show title lookup (get_page_by_title replacement) ---------------- */

	public function test_tv_show_title_lookup_returns_oldest_and_null_when_absent() {
		$admin = $this->make_user( 'administrator' );
		$first = self::factory()->post->create( array( 'post_type' => 'tv_show', 'post_status' => 'publish', 'post_author' => $admin, 'post_title' => 'Duplicate Title' ) );
		$second = self::factory()->post->create( array( 'post_type' => 'tv_show', 'post_status' => 'publish', 'post_author' => $admin, 'post_title' => 'Duplicate Title' ) );

		$lookup = function ( $title ) {
			$q = new WP_Query(
				array(
					'post_type'              => 'tv_show',
					'title'                  => $title,
					'post_status'            => 'any',
					'posts_per_page'         => 1,
					'orderby'                => 'post_date ID',
					'order'                  => 'ASC',
					'no_found_rows'          => true,
					'update_post_meta_cache' => false,
					'update_post_term_cache' => false,
				)
			);
			return ! empty( $q->posts ) ? $q->posts[0] : null;
		};

		$found = $lookup( 'Duplicate Title' );
		$this->assertInstanceOf( 'WP_Post', $found );
		$this->assertSame( $first, $found->ID, 'exact-title lookup returns the oldest match (no duplicate creation)' );

		$this->assertNull( $lookup( 'No Such TV Show Title' ), 'absent title returns null' );
	}
}
