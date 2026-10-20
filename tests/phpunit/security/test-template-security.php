<?php
/**
 * MV-02 regression tests: secure template resolution.
 *
 * @package MasVideos/Tests
 * @group   security
 * @group   mv-02
 */

defined( 'ABSPATH' ) || exit;

class MasVideos_Test_Template_Security extends MasVideos_Unit_Test_Case {

	private $canary_file  = '';
	private $theme_files  = array();
	private $tmp_paths    = array();

	public function set_up() {
		parent::set_up();
		$GLOBALS['masvideos_canary'] = false;

		$uploads = wp_upload_dir();
		$dir     = trailingslashit( $uploads['basedir'] ) . 'mv-canary';
		wp_mkdir_p( $dir );
		$this->canary_file = $dir . '/evil.php';
		file_put_contents( $this->canary_file, "<?php \$GLOBALS['masvideos_canary'] = true; echo 'CANARY-EXECUTED';" ); // phpcs:ignore
		$this->tmp_paths[] = $this->canary_file;
	}

	public function tear_down() {
		foreach ( array_merge( $this->tmp_paths, $this->theme_files ) as $p ) {
			if ( $p && file_exists( $p ) ) {
				@unlink( $p ); // phpcs:ignore
			}
		}
		unset( $GLOBALS['masvideos_canary'] );
		parent::tear_down();
	}

	private function render_part( $slug, $name = '' ) {
		ob_start();
		masvideos_get_template_part( $slug, $name );
		return ob_get_clean();
	}

	/* ---------------- format validator ---------------- */

	public function test_valid_template_names() {
		foreach ( array( 'content-video', 'content', 'myaccount/edit-video', 'content-video.php', 'a_b-c/d', 'single-movie' ) as $n ) {
			$this->assertTrue( masvideos_is_valid_template_name( $n ), "should be valid: {$n}" );
		}
	}

	public function test_invalid_template_names() {
		$bad = array(
			'', '../evil', '../../etc/passwd', 'a/../b', 'a//b', '/etc/passwd', 'C:\\windows',
			'foo\\bar', 'php://filter/resource=x', 'data://text/plain', 'http://evil/x',
			"a\0b", "a\nb", './x', 'x/', '/x', '.', '..', '-leading-dash',
		);
		foreach ( $bad as $n ) {
			$this->assertFalse( masvideos_is_valid_template_name( $n ), 'should be invalid: ' . var_export( $n, true ) );
		}
		$this->assertFalse( masvideos_is_valid_template_name( array( 'x' ) ) );
		$this->assertFalse( masvideos_is_valid_template_name( 123 ) );
	}

	/* ---------------- containment ---------------- */

	public function test_containment_allows_plugin_template() {
		$tpl = MasVideos()->plugin_path() . '/templates/content-video.php';
		$this->assertFileExists( $tpl );
		$this->assertTrue( masvideos_template_path_is_allowed( $tpl ) );
	}

	public function test_containment_allows_theme_file() {
		$f = get_stylesheet_directory() . '/masvideos-allowed-xtest.php';
		file_put_contents( $f, '<?php' ); // phpcs:ignore
		$this->theme_files[] = $f;
		$this->assertTrue( masvideos_template_path_is_allowed( $f ) );
	}

	public function test_containment_rejects_outside_and_missing() {
		$this->assertFalse( masvideos_template_path_is_allowed( $this->canary_file ) );
		$this->assertFalse( masvideos_template_path_is_allowed( MasVideos()->plugin_path() . '/templates/does-not-exist.php' ) );
		$this->assertFalse( masvideos_template_path_is_allowed( '' ) );
	}

	public function test_containment_is_separator_aware_for_sibling_prefix() {
		$uploads = wp_upload_dir();
		$base    = trailingslashit( $uploads['basedir'] ) . 'mvbase';
		$evil    = trailingslashit( $uploads['basedir'] ) . 'mvbase-evil';
		wp_mkdir_p( $base );
		wp_mkdir_p( $evil );
		$ok  = $base . '/ok.php';
		$bad = $evil . '/bad.php';
		file_put_contents( $ok, '<?php' );  // phpcs:ignore
		file_put_contents( $bad, '<?php' ); // phpcs:ignore
		$this->tmp_paths[] = $ok;
		$this->tmp_paths[] = $bad;

		$cb = function () use ( $base ) {
			return array( $base );
		};
		add_filter( 'masvideos_allowed_template_directories', $cb );
		$this->assertTrue( masvideos_template_path_is_allowed( $ok ), 'file in base allowed' );
		$this->assertFalse( masvideos_template_path_is_allowed( $bad ), 'sibling dir with shared prefix must NOT pass' );
		remove_filter( 'masvideos_allowed_template_directories', $cb );
	}

	public function test_containment_rejects_symlink_escape() {
		if ( ! function_exists( 'symlink' ) ) {
			$this->markTestSkipped( 'symlink() unavailable' );
		}
		$uploads = wp_upload_dir();
		$base    = trailingslashit( $uploads['basedir'] ) . 'mvbase-link';
		wp_mkdir_p( $base );
		$link = $base . '/link.php';
		@unlink( $link ); // phpcs:ignore
		if ( ! @symlink( $this->canary_file, $link ) ) { // phpcs:ignore
			$this->markTestSkipped( 'cannot create symlink in this environment' );
		}
		$this->tmp_paths[] = $link;

		$cb = function () use ( $base ) {
			return array( $base );
		};
		add_filter( 'masvideos_allowed_template_directories', $cb );
		// realpath() resolves the symlink to the canary (outside the base) -> rejected.
		$this->assertFalse( masvideos_template_path_is_allowed( $link ) );
		remove_filter( 'masvideos_allowed_template_directories', $cb );
	}

	/* ---------------- end-to-end get_template_part ---------------- */

	public function test_legit_plugin_template_loads_without_canary() {
		$this->render_part( 'content', 'video' ); // -> plugin templates/content-video.php
		$this->assertFalse( $GLOBALS['masvideos_canary'] );
	}

	public function test_parent_theme_override_loads() {
		$dir = get_template_directory() . '/masvideos';
		wp_mkdir_p( $dir );
		$f = $dir . '/content-xtest.php';
		file_put_contents( $f, 'MV-THEME-OVERRIDE' ); // phpcs:ignore
		$this->theme_files[] = $f;

		$out = $this->render_part( 'content', 'xtest' );
		$this->assertStringContainsString( 'MV-THEME-OVERRIDE', $out );
	}

	public function test_traversal_and_scheme_attempts_never_execute() {
		$attempts = array(
			'../../../../evil',
			'../mv-canary/evil',
			'/etc/passwd',
			'php://filter/resource=evil',
			"content\0../mv-canary/evil",
			'..\\..\\evil',
		);
		foreach ( $attempts as $bad ) {
			$out = $this->render_part( $bad );
			$this->assertFalse( $GLOBALS['masvideos_canary'], "canary executed for: {$bad}" );
			$this->assertSame( '', $out, "unexpected output for: {$bad}" );
		}
	}

	public function test_unknown_template_is_graceful_noop() {
		$out = $this->render_part( 'content', 'totally-unknown-xyz' );
		$this->assertSame( '', $out );
		$this->assertFalse( $GLOBALS['masvideos_canary'] );
	}

	/* ---------------- template-path trust boundary ---------------- */

	public function test_get_template_part_cannot_reach_outside_base_with_valid_slug() {
		// A format-valid slug that is not under any allowed template dir must not load.
		$GLOBALS['masvideos_canary'] = false;
		$this->render_part( 'mv-canary/evil' );
		$this->assertFalse( $GLOBALS['masvideos_canary'] );
	}

	public function test_get_template_rejects_traversal_name_even_with_custom_base() {
		// Rejecting the name triggers _doing_it_wrong(); that is the correct, safe behaviour.
		$this->setExpectedIncorrectUsage( 'masvideos_get_template' );

		// Even if a caller supplied an attacker base, a traversal NAME is rejected up front.
		$uploads = wp_upload_dir();
		$base    = trailingslashit( $uploads['basedir'] ) . 'mv-canary'; // contains evil.php
		$GLOBALS['masvideos_canary'] = false;
		ob_start();
		masvideos_get_template( '../evil', array(), $base, $base );
		$out = ob_get_clean();
		$this->assertSame( '', $out );
		$this->assertFalse( $GLOBALS['masvideos_canary'] );
	}

	public function test_get_template_allows_valid_name_in_custom_trusted_base() {
		// A trusted (caller-hardcoded) base with a valid name still works — preserving
		// third-party template overrides.
		$uploads = wp_upload_dir();
		$base    = trailingslashit( $uploads['basedir'] ) . 'mv-tpl-base/';
		wp_mkdir_p( $base );
		$f = $base . 'x-tpl.php';
		file_put_contents( $f, 'CUSTOM-BASE-OK' ); // phpcs:ignore
		$this->tmp_paths[] = $f;

		ob_start();
		masvideos_get_template( 'x-tpl.php', array(), $base, $base );
		$out = ob_get_clean();
		$this->assertStringContainsString( 'CUSTOM-BASE-OK', $out );
	}

	/* ---------------- shortcode families + blocks ---------------- */

	public function test_shortcode_families_cannot_escape_template_dir() {
		// Create one matching published item per type so each loop reaches the template call.
		$this->make_video( $this->make_user( 'administrator' ), 'publish' );
		$map = array(
			'mas_videos'             => 'video',
			'mas_movies'             => 'movie',
			'mas_tv_shows'           => 'tv_show',
			'mas_episodes'           => 'episode',
			'mas_movie_playlists'    => 'movie_playlist',
			'mas_video_playlists'    => 'video_playlist',
			'mas_tv_show_playlists'  => 'tv_show_playlist',
		);
		$admin = $this->make_user( 'administrator' );
		foreach ( $map as $tag => $ptype ) {
			self::factory()->post->create( array( 'post_type' => $ptype, 'post_status' => 'publish', 'post_author' => $admin ) );
			$GLOBALS['masvideos_canary'] = false;
			// Malicious template attribute via a real shortcode invocation.
			do_shortcode( '[' . $tag . ' template="../mv-canary/evil"]' );
			$this->assertFalse( $GLOBALS['masvideos_canary'], "canary executed via [{$tag}]" );
		}
	}

	public function test_server_rendered_blocks_cannot_escape_template_dir() {
		$admin = $this->make_user( 'administrator' );
		$video = $this->make_video( $admin, 'publish' );
		$movie = self::factory()->post->create( array( 'post_type' => 'movie', 'post_status' => 'publish', 'post_author' => $admin ) );
		$tv    = self::factory()->post->create( array( 'post_type' => 'tv_show', 'post_status' => 'publish', 'post_author' => $admin ) );

		$cases = array(
			array( array( 'MasVideos_Shortcodes', 'videos' ),   array( 'template' => '../mv-canary/evil', 'ids' => (string) $video ) ),
			array( array( 'MasVideos_Shortcodes', 'movies' ),   array( 'template' => '../mv-canary/evil', 'ids' => (string) $movie ) ),
			array( array( 'MasVideos_Shortcodes', 'tv_shows' ), array( 'template' => '../mv-canary/evil', 'ids' => (string) $tv ) ),
		);
		foreach ( $cases as $case ) {
			if ( ! is_callable( $case[0] ) ) {
				continue;
			}
			$GLOBALS['masvideos_canary'] = false;
			call_user_func( $case[0], $case[1] );
			$this->assertFalse( $GLOBALS['masvideos_canary'], 'canary executed via block render_callback ' . $case[0][1] );
		}
	}
}
