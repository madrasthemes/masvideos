<?php
/**
 * MV-09 regression tests: safe rendering of user-controlled video metadata,
 * plus MV-02 x MV-09 interaction (both boundaries hold independently).
 *
 * @package MasVideos/Tests
 * @group   security
 * @group   mv-09
 */

defined( 'ABSPATH' ) || exit;

class MasVideos_Test_Video_Rendering extends MasVideos_Unit_Test_Case {

	private $canary_file = '';
	private $http_called = false;

	public function set_up() {
		parent::set_up();

		$GLOBALS['masvideos_canary']       = false;
		$GLOBALS['masvideos_shortcode_ran'] = false;

		// A shortcode that would reveal execution if ever run on untrusted metadata.
		add_shortcode( 'mvran', array( $this, 'sc_ran' ) );
		// A shortcode that attempts a path-traversal template load when executed.
		add_shortcode( 'mvtpl', array( $this, 'sc_tpl' ) );

		// Canary PHP file outside any allowed template directory.
		$uploads = wp_upload_dir();
		wp_mkdir_p( trailingslashit( $uploads['basedir'] ) . 'mv-canary' );
		$this->canary_file = trailingslashit( $uploads['basedir'] ) . 'mv-canary/evil.php';
		file_put_contents( $this->canary_file, "<?php \$GLOBALS['masvideos_canary'] = true;" ); // phpcs:ignore

		// Fail loudly if any HTTP request is attempted during rendering.
		$this->http_called = false;
		add_filter( 'pre_http_request', array( $this, 'block_http' ), 10, 3 );
	}

	public function tear_down() {
		remove_shortcode( 'mvran' );
		remove_shortcode( 'mvtpl' );
		remove_filter( 'pre_http_request', array( $this, 'block_http' ), 10 );
		if ( $this->canary_file && file_exists( $this->canary_file ) ) {
			@unlink( $this->canary_file ); // phpcs:ignore
		}
		unset( $GLOBALS['masvideos_canary'], $GLOBALS['masvideos_shortcode_ran'] );
		parent::tear_down();
	}

	public function sc_ran() {
		$GLOBALS['masvideos_shortcode_ran'] = true;
		return 'SHORTCODE-RAN';
	}

	public function sc_tpl() {
		$GLOBALS['masvideos_shortcode_ran'] = true;
		masvideos_get_template_part( '../mv-canary/evil' ); // must be blocked by MV-02.
		return '';
	}

	public function block_http( $pre, $args, $url ) {
		$this->http_called = true;
		return new WP_Error( 'masvideos_test_no_http', 'HTTP blocked in test: ' . $url );
	}

	/* ---------------- embed markup ---------------- */

	public function test_embed_strips_disallowed_iframe_host() {
		$out = masvideos_render_video_source( 'video_embed', '<iframe src="https://evil.example/malware"></iframe>' );
		$this->assertStringNotContainsString( 'evil.example', $out );
		$this->assertStringNotContainsString( '<iframe', $out );
	}

	public function test_embed_keeps_allowed_provider_iframe() {
		$out = masvideos_render_video_source( 'video_embed', '<iframe src="https://www.youtube.com/embed/abc123" allowfullscreen></iframe>' );
		$this->assertStringContainsString( '<iframe', $out );
		$this->assertStringContainsString( 'youtube.com/embed/abc123', $out );
	}

	public function test_embed_does_not_execute_shortcodes_or_scripts() {
		$in  = '[mvran]<script>window.x=1</script><iframe src="https://player.vimeo.com/video/1"></iframe>';
		$out = masvideos_render_video_source( 'video_embed', $in );
		$this->assertFalse( $GLOBALS['masvideos_shortcode_ran'], 'embed metadata must not execute shortcodes' );
		$this->assertStringNotContainsString( 'SHORTCODE-RAN', $out );
		$this->assertStringNotContainsString( '<script', $out );
		$this->assertStringContainsString( 'player.vimeo.com/video/1', $out );
	}

	public function test_embed_strips_event_handlers() {
		$out = masvideos_render_video_source( 'video_embed', '<img src="https://www.youtube.com/x.jpg" onerror="alert(1)">' );
		$this->assertStringNotContainsString( 'onerror', $out );
	}

	/* ---------------- url ---------------- */

	public function test_url_direct_media_renders_player_without_http() {
		$out = masvideos_render_video_source( 'video_url', 'https://example.com/clip.mp4' );
		$this->assertStringContainsString( 'clip.mp4', $out );
		$this->assertFalse( $this->http_called, 'direct media URL must not trigger remote discovery' );
	}

	public function test_url_non_provider_becomes_safe_link_without_http() {
		$out = masvideos_render_video_source( 'video_url', 'https://example.com/watch?v=1' );
		$this->assertStringContainsString( '<a href="https://example.com/watch?v=1"', $out );
		$this->assertStringNotContainsString( '<iframe', $out );
		$this->assertFalse( $this->http_called, 'non-provider URL must not trigger remote discovery (discovery disabled)' );
	}

	public function test_url_provider_uses_oembed() {
		// Short-circuit oEmbed so no real network call is needed; proves the provider path is used.
		$cb = function () {
			return '<iframe src="https://www.youtube.com/embed/xyz"></iframe>';
		};
		add_filter( 'pre_oembed_result', $cb );
		$out = masvideos_render_video_source( 'video_url', 'https://www.youtube.com/watch?v=xyz' );
		remove_filter( 'pre_oembed_result', $cb );

		$this->assertStringContainsString( 'youtube.com/embed/xyz', $out );
	}

	public function test_url_javascript_scheme_rejected() {
		$out = masvideos_render_video_source( 'video_url', 'javascript:alert(1)' );
		$this->assertStringNotContainsString( 'javascript:', $out );
	}

	/* ---------------- oEmbed response sanitisation ---------------- */

	public function test_malicious_oembed_response_is_sanitised() {
		$cb = function () {
			return '<script>window.evil=1</script>'
				. '<iframe src="https://evil.example/x"></iframe>'
				. '<iframe src="https://www.youtube.com/embed/ok" onload="x()"></iframe>';
		};
		add_filter( 'pre_oembed_result', $cb );
		$out = masvideos_render_video_source( 'video_url', 'https://www.youtube.com/watch?v=ok' );
		remove_filter( 'pre_oembed_result', $cb );

		$this->assertStringNotContainsString( '<script', $out, 'provider scripts must be stripped' );
		$this->assertStringNotContainsString( 'evil.example', $out, 'disallowed provider iframe must be removed' );
		$this->assertStringNotContainsString( 'onload', $out, 'event handlers must be stripped' );
		$this->assertStringContainsString( 'youtube.com/embed/ok', $out, 'the legitimate provider iframe survives' );
	}

	public function test_malformed_oembed_response_is_safe() {
		$cb = function () {
			return '<iframe src="https://evil.example/x"><<<broken';
		};
		add_filter( 'pre_oembed_result', $cb );
		$out = masvideos_render_video_source( 'video_url', 'https://www.youtube.com/watch?v=x' );
		remove_filter( 'pre_oembed_result', $cb );
		$this->assertStringNotContainsString( 'evil.example', $out );
		$this->assertStringNotContainsString( '<script', $out );
	}

	/* ---------------- iframe src URL validation ---------------- */

	public function test_iframe_src_validation_matrix() {
		$allow = array(
			'https://www.youtube.com/embed/abc',
			'https://youtube.com/embed/abc?rel=0',
			'https://www.youtube-nocookie.com/embed/abc',
			'https://player.vimeo.com/video/123',
		);
		foreach ( $allow as $src ) {
			$this->assertTrue( masvideos_is_allowed_embed_iframe_src( $src ), "should allow: {$src}" );
		}

		$deny = array(
			'http://www.youtube.com/embed/abc',                // not https
			'https://user:pass@www.youtube.com/embed/abc',     // userinfo
			'https://www.youtube.com:8443/embed/abc',          // non-default port
			'https://www.youtube.com/watch?v=abc',             // unsupported path
			'https://www.youtube.com.evil.com/embed/abc',      // suffix trick / not exact host
			'https://notyoutube.com/embed/abc',                // different host
			'https://vimeo.com/123',                           // page host, not the player host
			'javascript:alert(1)',
			'https:///embed/abc',                              // empty host
			'not a url',
			'',
		);
		foreach ( $deny as $src ) {
			$this->assertFalse( masvideos_is_allowed_embed_iframe_src( $src ), "should deny: {$src}" );
		}
	}

	public function test_embed_removes_unapproved_subdomain_and_userinfo() {
		$in  = '<iframe src="https://evil.youtube.com.attacker.com/embed/x"></iframe>'
			 . '<iframe src="https://user:pw@player.vimeo.com/video/1"></iframe>';
		$out = masvideos_render_video_source( 'video_embed', $in );
		$this->assertStringNotContainsString( 'attacker.com', $out );
		$this->assertStringNotContainsString( 'user:pw', $out );
		$this->assertStringNotContainsString( '<iframe', $out, 'both unsafe iframes removed' );
	}

	public function test_iframe_style_and_name_attributes_stripped() {
		$in  = '<iframe src="https://www.youtube.com/embed/ok" style="position:fixed;opacity:0" name="x"></iframe>';
		$out = masvideos_render_video_source( 'video_embed', $in );
		$this->assertStringContainsString( 'youtube.com/embed/ok', $out );
		$this->assertStringNotContainsString( 'style=', $out );
		$this->assertStringNotContainsString( 'name=', $out );
	}

	/* ---------------- direct media URL schemes ---------------- */

	public function test_non_http_media_urls_rejected() {
		foreach ( array( 'ftp://example.com/v.mp4', 'data:video/mp4;base64,AAAA', 'file:///etc/passwd.mp4' ) as $u ) {
			$out = masvideos_render_video_source( 'video_url', $u );
			$this->assertSame( '', $out, "non-http(s) URL must render nothing: {$u}" );
		}
		$this->assertFalse( $this->http_called );
	}

	/* ---------------- structured data ---------------- */

	public function test_structured_data_text_strips_shortcodes_and_tags() {
		$out = masvideos_safe_structured_data_text( '[mvran]<b>Hello</b> & world' );
		$this->assertFalse( $GLOBALS['masvideos_shortcode_ran'] );
		$this->assertStringNotContainsString( 'SHORTCODE-RAN', $out );
		$this->assertStringNotContainsString( '<b>', $out );
		$this->assertStringContainsString( 'Hello', $out );
		$this->assertStringContainsString( 'world', $out );
	}

	/* ---------------- integration: masvideos_get_the_video ---------------- */

	public function test_get_the_video_embed_is_safe() {
		$admin = $this->make_user( 'administrator' );
		$vid   = $this->make_video( $admin, 'publish', 'V' );
		$video = masvideos_get_video( $vid );
		$video->set_video_choice( 'video_embed' );
		$video->set_video_embed_content( '[mvran]<iframe src="https://evil.example/x"></iframe>' );
		$video->save();

		$out = masvideos_get_the_video( masvideos_get_video( $vid ) );
		$this->assertFalse( $GLOBALS['masvideos_shortcode_ran'], 'the_content/do_shortcode must not run on embed metadata' );
		$this->assertStringNotContainsString( 'SHORTCODE-RAN', $out );
		$this->assertStringNotContainsString( 'evil.example', $out );
	}

	/* ---------------- MV-02 x MV-09 interaction ---------------- */

	/**
	 * Boundary 1 (MV-09): an untrusted shortcode embedded in video metadata is never evaluated,
	 * so it cannot even attempt the traversal.
	 */
	public function test_interaction_metadata_does_not_evaluate_shortcode() {
		$admin = $this->make_user( 'administrator' );
		$vid   = $this->make_video( $admin, 'publish', 'V' );
		$video = masvideos_get_video( $vid );
		$video->set_video_choice( 'video_embed' );
		$video->set_video_embed_content( '[mvtpl]' );
		$video->save();

		$GLOBALS['masvideos_shortcode_ran'] = false;
		$GLOBALS['masvideos_canary']        = false;
		masvideos_get_the_video( masvideos_get_video( $vid ) );

		$this->assertFalse( $GLOBALS['masvideos_shortcode_ran'], 'MV-09: shortcode in metadata must not run' );
		$this->assertFalse( $GLOBALS['masvideos_canary'], 'canary must never execute' );
	}

	/**
	 * Boundary 2 (MV-02): even if the untrusted shortcode IS evaluated elsewhere, its template
	 * attribute / traversal cannot escape the template directories.
	 */
	public function test_interaction_shortcode_execution_cannot_escape_templates() {
		$GLOBALS['masvideos_shortcode_ran'] = false;
		$GLOBALS['masvideos_canary']        = false;

		// Directly evaluate the shortcode (simulating evaluation via some other path).
		do_shortcode( '[mvtpl]' );

		$this->assertTrue( $GLOBALS['masvideos_shortcode_ran'], 'sanity: the shortcode did run here' );
		$this->assertFalse( $GLOBALS['masvideos_canary'], 'MV-02: traversal via get_template_part must be blocked even when a shortcode runs' );
	}
}
