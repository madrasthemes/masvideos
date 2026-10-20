<?php
/**
 * MV-14 regression tests: TMDB communication goes through the WordPress HTTP API over HTTPS with
 * TLS verification enabled, handles failures/non-200 responses, and never leaks the API key.
 *
 * @package MasVideos/Tests
 * @group   security
 * @group   mv-14
 */

defined( 'ABSPATH' ) || exit;

class MasVideos_Test_Tmdb_Transport extends MasVideos_Unit_Test_Case {

	private $requests  = array();
	private $http_code = 200;
	private $http_body = '';

	public function set_up() {
		parent::set_up();

		if ( ! class_exists( 'MasVideos_TMDB' ) ) {
			include_once MASVIDEOS_ABSPATH . 'includes/integrations/tmdb/class-masvideos-tmdb.php';
		}

		$this->requests  = array();
		$this->http_code = 200;
		// A valid-looking configuration payload so the constructor's config load succeeds.
		$this->http_body = wp_json_encode(
			array(
				'images' => array(
					'base_url'        => 'http://image.tmdb.org/t/p/',
					'secure_base_url' => 'https://image.tmdb.org/t/p/',
				),
			)
		);

		add_filter( 'pre_http_request', array( $this, 'mock_http' ), 10, 3 );
	}

	public function tear_down() {
		remove_filter( 'pre_http_request', array( $this, 'mock_http' ), 10 );
		parent::tear_down();
	}

	public function mock_http( $preempt, $args, $url ) {
		$this->requests[] = array(
			'url'                => $url,
			'sslverify'          => isset( $args['sslverify'] ) ? $args['sslverify'] : null,
			'reject_unsafe_urls' => isset( $args['reject_unsafe_urls'] ) ? $args['reject_unsafe_urls'] : null,
		);

		return array(
			'headers'  => array(),
			'body'     => $this->http_body,
			'response' => array( 'code' => $this->http_code, 'message' => '' ),
			'cookies'  => array(),
		);
	}

	private function new_tmdb( $debug = false ) {
		return new MasVideos_TMDB(
			array(
				'apikey' => 'SECRETKEY123',
				'lang'   => 'en-US',
				'adult'  => false,
				'debug'  => $debug,
			)
		);
	}

	private function call_private( $tmdb, $action, $append = '' ) {
		$ref = new ReflectionMethod( 'MasVideos_TMDB', '_call' );
		$ref->setAccessible( true );
		return $ref->invoke( $tmdb, $action, $append );
	}

	public function test_requests_go_over_https_with_tls_verification() {
		$this->new_tmdb(); // constructor performs the 'configuration' request

		$this->assertNotEmpty( $this->requests, 'the WordPress HTTP API must be used (pre_http_request intercepts it, proving no raw cURL)' );

		foreach ( $this->requests as $request ) {
			$this->assertStringStartsWith( 'https://api.themoviedb.org/', $request['url'], 'all TMDB requests must be HTTPS (MV-14)' );
			$this->assertNotSame( false, $request['sslverify'], 'TLS verification must never be disabled' );
		}
	}

	public function test_requests_use_safe_http_api_blocking_internal_redirects() {
		$this->new_tmdb();

		$this->assertNotEmpty( $this->requests );
		foreach ( $this->requests as $request ) {
			$this->assertTrue( (bool) $request['reject_unsafe_urls'], 'wp_safe_remote_get() must be used so redirects to internal/unsafe hosts are blocked (MV-14 SSRF hardening)' );
		}
	}

	public function test_request_pinned_to_tmdb_host() {
		$tmdb = $this->new_tmdb();
		$this->requests = array();

		// Even with an id that tries to look like another target, the host stays api.themoviedb.org.
		$this->call_private( $tmdb, 'movie/123@evil.example.com' );

		$this->assertNotEmpty( $this->requests );
		foreach ( $this->requests as $request ) {
			$host = wp_parse_url( $request['url'], PHP_URL_HOST );
			$this->assertSame( 'api.themoviedb.org', strtolower( (string) $host ), 'the request host must remain TMDB regardless of the action string' );
		}
	}

	public function test_successful_response_is_decoded_to_array() {
		$tmdb = $this->new_tmdb();
		$this->http_body = wp_json_encode( array( 'id' => 42, 'title' => 'Mocked Movie' ) );

		$result = $this->call_private( $tmdb, 'movie/42' );
		$this->assertIsArray( $result );
		$this->assertSame( 42, $result['id'] );
	}

	public function test_non_200_response_returns_empty_array() {
		$tmdb            = $this->new_tmdb();
		$this->http_code = 401; // e.g. invalid API key
		$this->http_body = wp_json_encode( array( 'status_message' => 'Invalid API key' ) );

		$result = $this->call_private( $tmdb, 'movie/42' );
		$this->assertSame( array(), $result, 'a non-200 response must not be treated as data' );
	}

	public function test_wp_error_returns_empty_array() {
		$tmdb = $this->new_tmdb();

		$err = function () {
			return new WP_Error( 'http_request_failed', 'boom' );
		};
		add_filter( 'pre_http_request', $err, 20 );

		$result = $this->call_private( $tmdb, 'movie/42' );
		$this->assertSame( array(), $result );

		remove_filter( 'pre_http_request', $err, 20 );
	}

	public function test_image_url_uses_secure_base() {
		$tmdb = $this->new_tmdb();
		$this->assertStringStartsWith( 'https://', $tmdb->getImageURL(), 'imported media URLs must use the secure (https) base' );
	}

	public function test_debug_output_does_not_leak_api_key() {
		ob_start();
		$this->call_private( $this->new_tmdb( true ), 'movie/42' );
		$output = ob_get_clean();

		$this->assertStringNotContainsString( 'SECRETKEY123', $output, 'the API key must never be printed, even in debug mode (MV-14)' );
	}
}
