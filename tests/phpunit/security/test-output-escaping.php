<?php
/**
 * Phase 2D2 Pass 1 regression tests: output escaping / safe rendering.
 *
 * These exercise real rendering helpers and templates and assert that (a) attacker-controlled
 * values injected at the render boundary are escaped, (b) script/event-handler injection is
 * rejected, and (c) intended/trusted markup is preserved (not turned into visible text).
 *
 * @package MasVideos/Tests
 * @group   security
 * @group   escaping
 */

defined( 'ABSPATH' ) || exit;

class MasVideos_Test_Output_Escaping extends MasVideos_Unit_Test_Case {

	private function capture( $callback ) {
		ob_start();
		call_user_func( $callback );
		return ob_get_clean();
	}

	/* ---------------- page titles: filtered value is escaped ---------------- */

	public function page_title_functions() {
		return array(
			array( 'masvideos_movie_page_title', 'masvideos_movie_page_title' ),
			array( 'masvideos_video_page_title', 'masvideos_video_page_title' ),
			array( 'masvideos_tv_show_page_title', 'masvideos_tv_show_page_title' ),
			array( 'masvideos_person_page_title', 'masvideos_person_page_title' ),
			array( 'masvideos_episode_page_title', 'masvideos_episode_page_title' ),
		);
	}

	/**
	 * @dataProvider page_title_functions
	 */
	public function test_page_title_escapes_injected_markup( $fn, $filter ) {
		if ( ! function_exists( $fn ) ) {
			$this->markTestSkipped( "{$fn} unavailable" );
		}

		$payload = '<script>alert(1)</script>Movies & "More"';
		$cb      = function () use ( $payload ) {
			return $payload;
		};
		add_filter( $filter, $cb );

		$out = $this->capture( function () use ( $fn ) {
			$fn( true );
		} );

		remove_filter( $filter, $cb );

		$this->assertStringNotContainsString( '<script>', $out, "{$fn} must not emit a raw <script> tag" );
		$this->assertStringContainsString( '&lt;script&gt;', $out, "{$fn} must HTML-escape injected markup" );
		$this->assertStringContainsString( '&amp;', $out, 'ampersand must be encoded' );
	}

	/* ---------------- notices: script stripped, safe markup kept ---------------- */

	public function test_notice_rendering_strips_scripts_but_keeps_safe_markup() {
		masvideos_clear_notices();
		masvideos_add_notice( '<strong>Heads up</strong><script>alert(1)</script><img src=x onerror=alert(2)>', 'notice' );

		$out = $this->capture( 'masvideos_print_notices' );

		$this->assertStringNotContainsString( '<script', $out, 'script tag must be stripped from notices' );
		$this->assertStringNotContainsString( 'onerror', $out, 'event-handler attributes must be stripped' );
		$this->assertStringContainsString( '<strong>Heads up</strong>', $out, 'intended inline markup is preserved' );
	}

	/* ---------------- star rating: markup preserved, SR text escaped ---------------- */

	public function test_star_rating_markup_preserved() {
		if ( ! function_exists( 'masvideos_star_rating' ) ) {
			$this->markTestSkipped( 'masvideos_star_rating unavailable' );
		}
		$html = masvideos_star_rating( array( 'rating' => 3.5, 'number' => 12, 'echo' => false ) );

		$this->assertStringContainsString( '<div class="star-rating">', $html, 'star markup must be preserved (not escaped to text)' );
		$this->assertStringContainsString( 'star-full', $html );
		$this->assertStringContainsString( 'screen-reader-text', $html );
		$this->assertStringNotContainsString( '&lt;div', $html, 'the trusted markup must not be double-escaped into text' );
	}

	/* ---------------- breadcrumb template: crumb name escaped, url escaped ---------------- */

	public function test_breadcrumb_template_escapes_crumb_name_and_url() {
		$args = array(
			'breadcrumb'  => array(
				array( 'Home & <script>bad</script>', 'https://example.test/?a=1&b=2' ),
				array( 'Current "Item"', '' ),
			),
			'wrap_before' => '<nav class="mv-breadcrumb">',
			'wrap_after'  => '</nav>',
			'before'      => '',
			'after'       => '',
			'delimiter'   => ' / ',
		);

		$out = $this->capture( function () use ( $args ) {
			masvideos_get_template( 'global/breadcrumb.php', $args );
		} );

		$this->assertNotSame( '', trim( $out ), 'breadcrumb should render' );
		$this->assertStringNotContainsString( '<script>bad</script>', $out, 'crumb name must be escaped' );
		$this->assertStringContainsString( '&lt;script&gt;', $out );
		// The & in the URL must be entity-encoded by esc_url, not left raw as a bare &.
		$this->assertStringContainsString( 'a=1&#038;b=2', $out );
		$this->assertStringContainsString( '<nav class="mv-breadcrumb">', $out, 'trusted wrapper markup preserved' );
	}

	/* ---------------- shortcode / block render path: no raw script ---------------- */

	public function test_movies_shortcode_renders_without_raw_script() {
		$admin = $this->make_user( 'administrator' );
		$movie = self::factory()->post->create(
			array(
				'post_type'   => 'movie',
				'post_status' => 'publish',
				'post_author' => $admin,
				'post_title'  => 'Safe Movie Title',
			)
		);

		$out = do_shortcode( '[mas_movies ids="' . $movie . '"]' );

		$this->assertIsString( $out );
		$this->assertStringNotContainsString( '<script>alert', $out );
		// The loop should render the movie's (escaped) title text.
		$this->assertStringContainsString( 'Safe Movie Title', $out );
	}

	public function test_movies_block_render_callback_matches_shortcode() {
		if ( ! is_callable( array( 'MasVideos_Shortcodes', 'movies' ) ) ) {
			$this->markTestSkipped( 'MasVideos_Shortcodes::movies unavailable' );
		}
		$admin = $this->make_user( 'administrator' );
		$movie = self::factory()->post->create( array( 'post_type' => 'movie', 'post_status' => 'publish', 'post_author' => $admin, 'post_title' => 'Blockable' ) );

		$out = MasVideos_Shortcodes::movies( array( 'ids' => (string) $movie ) );

		$this->assertIsString( $out );
		$this->assertStringContainsString( 'Blockable', $out );
		$this->assertStringNotContainsString( '<script>alert', $out );
	}
}
