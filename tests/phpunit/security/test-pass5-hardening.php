<?php
/**
 * Phase 2D2 Pass 5 regression tests: input-sanitization and authorization hardening.
 *
 * Covers the confirmed Pass 5 changes:
 *  - cast/crew preparers now absint() person IDs and masvideos_clean() the free-text
 *    character/category/job fields before they are stored in _cast / _crew post meta
 *    (reached via the AJAX cast/crew save handlers);
 *  - the admin permalink settings save now requires manage_options explicitly.
 *
 * @package MasVideos/Tests
 * @group   security
 * @group   pass5
 */

defined( 'ABSPATH' ) || exit;

class MasVideos_Test_Pass5_Hardening extends MasVideos_Unit_Test_Case {

	/* ============ cast / crew input sanitization ============ */

	public function test_movie_prepare_cast_sanitizes_fields() {
		if ( ! class_exists( 'MasVideos_Meta_Box_Movie_Data' ) ) {
			$this->markTestSkipped( 'movie-data meta box unavailable' );
		}
		$cast = MasVideos_Meta_Box_Movie_Data::prepare_cast(
			array(
				'cast_person_ids'        => array( 0 => '7abc', 1 => '0' ), // 2nd is empty-ish -> skipped
				'cast_person_characters' => array( 0 => '<script>alert(1)</script>Hero & Co' ),
				'cast_person_position'   => array( 0 => '3' ),
			)
		);
		$this->assertCount( 1, $cast, 'empty person id rows are skipped' );
		$this->assertSame( 7, $cast[0]['id'], 'person id is absint()-cast' );
		$this->assertSame( 'Hero & Co', $cast[0]['character'], 'character markup stripped, entity-safe text kept' );
		$this->assertStringNotContainsString( '<script', $cast[0]['character'] );
		$this->assertSame( 3, $cast[0]['position'] );
	}

	public function test_movie_prepare_crew_sanitizes_fields() {
		if ( ! class_exists( 'MasVideos_Meta_Box_Movie_Data' ) ) {
			$this->markTestSkipped( 'movie-data meta box unavailable' );
		}
		$crew = MasVideos_Meta_Box_Movie_Data::prepare_crew(
			array(
				'crew_person_ids'        => array( 0 => '9' ),
				'crew_person_categories' => array( 0 => '<b>Direction</b>' ),
				'crew_person_jobs'       => array( 0 => 'Director<img src=x onerror=1>' ),
				'crew_person_position'   => array( 0 => '1' ),
			)
		);
		$this->assertSame( 9, $crew[0]['id'] );
		$this->assertSame( 'Direction', $crew[0]['category'], 'category markup stripped' );
		$this->assertSame( 'Director', $crew[0]['job'], 'job markup stripped' );
	}

	public function test_tv_show_prepare_cast_crew_sanitizes_fields() {
		if ( ! class_exists( 'MasVideos_Meta_Box_TV_Show_Data' ) ) {
			$this->markTestSkipped( 'tv-show-data meta box unavailable' );
		}
		$cast = MasVideos_Meta_Box_TV_Show_Data::prepare_cast(
			array(
				'cast_person_ids'        => array( 0 => '4' ),
				'cast_person_characters' => array( 0 => "O'Brien <b>x</b>" ),
				'cast_person_position'   => array( 0 => '0' ),
			)
		);
		$this->assertSame( 4, $cast[0]['id'] );
		$this->assertSame( "O'Brien x", $cast[0]['character'], 'apostrophe preserved, tags stripped' );

		$crew = MasVideos_Meta_Box_TV_Show_Data::prepare_crew(
			array(
				'crew_person_ids'        => array( 0 => '5' ),
				'crew_person_categories' => array( 0 => 'Writing' ),
				'crew_person_jobs'       => array( 0 => '<script>x()</script>Writer' ),
				'crew_person_position'   => array( 0 => '2' ),
			)
		);
		$this->assertSame( 5, $crew[0]['id'] );
		$this->assertSame( 'Writer', $crew[0]['job'], 'script content removed' );
	}

	/* ============ permalink settings capability gate ============ */

	private function permalink_post( $nonce ) {
		return array(
			'permalink_structure'            => '/%postname%/',
			'masvideos-permalinks-nonce'     => $nonce,
			'masvideos_movie_genre_slug'     => 'evil-genre',
			'masvideos_movie_tag_slug'       => 'evil-tag',
			'masvideos_movie_attribute_slug' => 'evil-attr',
		);
	}

	public function test_permalink_settings_save_blocked_for_non_admin() {
		if ( ! class_exists( 'MasVideos_Admin_Permalink_Settings' ) ) {
			$this->markTestSkipped( 'permalink settings class unavailable' );
		}
		set_current_screen( 'options-permalink' ); // make is_admin() true
		$before = get_option( 'masvideos_permalinks', array() );

		$subscriber = $this->make_user( 'subscriber' );
		wp_set_current_user( $subscriber );
		$_POST = $this->permalink_post( wp_create_nonce( 'masvideos-permalinks' ) );

		$settings = new MasVideos_Admin_Permalink_Settings();
		$settings->settings_save();

		$after = get_option( 'masvideos_permalinks', array() );
		$this->assertSame( $before, $after, 'a subscriber (no manage_options) cannot write permalink options' );
		$this->assertNotSame( 'evil-genre', isset( $after['movie_genre_base'] ) ? $after['movie_genre_base'] : null );

		$_POST = array();
		set_current_screen( 'front' );
	}

	public function test_permalink_settings_save_allowed_for_admin() {
		if ( ! class_exists( 'MasVideos_Admin_Permalink_Settings' ) ) {
			$this->markTestSkipped( 'permalink settings class unavailable' );
		}
		set_current_screen( 'options-permalink' );
		$admin = $this->make_user( 'administrator' );
		wp_set_current_user( $admin );
		if ( ! current_user_can( 'manage_options' ) ) {
			set_current_screen( 'front' );
			$this->markTestSkipped( 'administrator lacks manage_options in this environment' );
		}
		$_POST = $this->permalink_post( wp_create_nonce( 'masvideos-permalinks' ) );

		$settings = new MasVideos_Admin_Permalink_Settings();
		$settings->settings_save();

		$after = get_option( 'masvideos_permalinks', array() );
		$this->assertSame( 'evil-genre', $after['movie_genre_base'], 'an administrator with a valid nonce can save (sanitized) permalink options' );

		$_POST = array();
		delete_option( 'masvideos_permalinks' );
		set_current_screen( 'front' );
	}
}
