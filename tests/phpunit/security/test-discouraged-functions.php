<?php
/**
 * Phase 2D2 Pass 4 regression tests: discouraged-function and filesystem-safety conversions.
 *
 * Covers the date()/gmdate()/wp_date(), rand()/wp_rand(), strip_tags()/wp_strip_all_tags() and
 * raw-filesystem -> WP_Filesystem changes made to clear the remaining Plugin Check code errors,
 * using disposable fixtures only (never a production theme or user template).
 *
 * @package MasVideos/Tests
 * @group   security
 * @group   discouraged-functions
 */

defined( 'ABSPATH' ) || exit;

class MasVideos_Test_Discouraged_Functions extends MasVideos_Unit_Test_Case {

	private $tmp_dirs = array();

	public function tear_down() {
		remove_filter( 'user_has_cap', array( $this, 'grant_edit_themes' ) );
		$GLOBALS['wp_stylesheet_path'] = null;
		$GLOBALS['wp_template_path']   = null;
		unset( $GLOBALS['movie'], $GLOBALS['episode'] );
		update_option( 'timezone_string', '' );
		update_option( 'gmt_offset', 0 );
		foreach ( $this->tmp_dirs as $dir ) {
			$this->rrmdir( $dir );
		}
		$this->tmp_dirs = array();
		parent::tear_down();
	}

	private function make_tmp_dir() {
		$dir = get_temp_dir() . 'masvideos-fs-' . wp_generate_password( 10, false );
		mkdir( $dir, 0777, true );
		$this->tmp_dirs[] = $dir;
		return $dir;
	}

	private function rrmdir( $dir ) {
		if ( ! is_dir( $dir ) ) {
			return;
		}
		$items = array_diff( scandir( $dir ), array( '.', '..' ) );
		foreach ( $items as $item ) {
			$path = $dir . '/' . $item;
			is_dir( $path ) ? $this->rrmdir( $path ) : @unlink( $path );
		}
		@rmdir( $dir );
	}

	/* ===================== Date/time: timezone correctness ===================== */

	/**
	 * A date-only release value must format to the same calendar year regardless of the site
	 * timezone (gmdate), including across a New-Year boundary. wp_date() would shift it a day
	 * and change the year for negative-offset zones.
	 */
	public function test_release_year_is_timezone_stable_across_year_boundary() {
		if ( ! function_exists( 'masvideos_template_single_movie_release_year' ) ) {
			$this->markTestSkipped( 'release-year template function unavailable' );
		}
		$author = $this->make_user( 'administrator' );
		$id     = self::factory()->post->create( array( 'post_type' => 'movie', 'post_status' => 'publish', 'post_author' => $author ) );
		$movie  = masvideos_get_movie( $id );
		$movie->set_movie_release_date( '2020-01-01' ); // Midnight UTC; the day before in all negative offsets.
		$movie->save();

		$render = function () use ( $id ) {
			$GLOBALS['movie'] = masvideos_get_movie( $id );
			ob_start();
			masvideos_template_single_movie_release_year();
			$out = ob_get_clean();
			unset( $GLOBALS['movie'] );
			return $out;
		};

		update_option( 'timezone_string', 'UTC' );
		$this->assertStringContainsString( '2020', $render(), 'UTC: release year is 2020' );

		update_option( 'timezone_string', 'Pacific/Honolulu' ); // UTC-10.
		$this->assertStringContainsString( '2020', $render(), 'UTC-10: release year is still 2020 (gmdate is timezone-stable)' );
		$this->assertStringNotContainsString( '2019', $render(), 'the date-only value must not slip to the previous year' );
	}

	public function test_episode_release_date_format_is_timezone_stable() {
		if ( ! function_exists( 'masvideos_template_single_episode_release_date' ) || ! function_exists( 'masvideos_get_episode' ) ) {
			$this->markTestSkipped( 'episode release-date function unavailable' );
		}
		$author = $this->make_user( 'administrator' );
		$id     = self::factory()->post->create( array( 'post_type' => 'episode', 'post_status' => 'publish', 'post_author' => $author ) );
		$episode = masvideos_get_episode( $id );
		if ( ! is_object( $episode ) || ! method_exists( $episode, 'set_episode_release_date' ) ) {
			$this->markTestSkipped( 'episode object/setter unavailable' );
		}
		$episode->set_episode_release_date( '2020-01-01' );
		$episode->save();

		$render = function () use ( $id ) {
			$GLOBALS['episode'] = masvideos_get_episode( $id );
			ob_start();
			masvideos_template_single_episode_release_date();
			$out = ob_get_clean();
			unset( $GLOBALS['episode'] );
			return $out;
		};

		update_option( 'timezone_string', 'Pacific/Kiritimati' ); // UTC+14, the far edge.
		$this->assertStringContainsString( '01.01.2020', $render(), 'UTC+14: d.m.Y of a date-only value is stable' );
		update_option( 'timezone_string', 'Pacific/Honolulu' );   // UTC-10.
		$this->assertStringContainsString( '01.01.2020', $render(), 'UTC-10: d.m.Y of a date-only value is stable' );
	}

	/**
	 * The movies year-filter widget's current-year ceiling is the site-local year (wp_date), the
	 * documented display semantic.
	 */
	public function test_year_filter_current_year_is_site_local() {
		if ( ! class_exists( 'MasVideos_Widget_Movies_Year_Filter' ) ) {
			$this->markTestSkipped( 'year-filter widget unavailable' );
		}
		$w       = new MasVideos_Widget_Movies_Year_Filter();
		$ref     = new ReflectionObject( $w );
		$settings = $ref->getProperty( 'settings' );
		$settings->setAccessible( true );
		$max = (int) $settings->getValue( $w )['start_year']['max'];
		$this->assertSame( (int) wp_date( 'Y' ) - 1, $max, 'the max start-year is (site-local current year - 1)' );
	}

	/* ===================== Random: cache-bucket bounds ===================== */

	public function test_shortcode_rand_cache_bucket_within_bounds() {
		if ( ! class_exists( 'MasVideos_Shortcode_Videos' ) ) {
			$this->markTestSkipped( 'videos shortcode unavailable' );
		}
		$sc  = new MasVideos_Shortcode_Videos( array(), 'videos' );
		$ref = new ReflectionObject( $sc );
		$qa  = $ref->getProperty( 'query_args' );
		$qa->setAccessible( true );
		$args            = $qa->getValue( $sc );
		$args['orderby'] = 'rand';
		$qa->setValue( $sc, $args );
		$tp = $ref->getProperty( 'type' );
		$tp->setAccessible( true );
		$type = $tp->getValue( $sc );

		$method = $ref->getMethod( 'get_transient_name' );
		$method->setAccessible( true );

		// The transient name is: 'masvideos_video_loop' . <md5 suffix> . <rand bucket> . <cache version>.
		// Reconstruct the fixed prefix and version so the bucket can be isolated exactly (the md5 suffix
		// and the version both contain digits, so a regex alone is ambiguous).
		$extract_bucket = function ( $name ) use ( $args, $type ) {
			$prefix  = 'masvideos_video_loop' . substr( md5( wp_json_encode( $args ) . $type ), 28 );
			$version = MasVideos_Cache_Helper::get_transient_version( 'video_query' );
			$middle  = substr( $name, strlen( $prefix ), strlen( $name ) - strlen( $prefix ) - strlen( $version ) );
			return $middle;
		};

		// Default ceiling is 5: the bucket must always be an integer in 0..5.
		for ( $i = 0; $i < 60; $i++ ) {
			$bucket = $extract_bucket( $method->invoke( $sc ) );
			$this->assertMatchesRegularExpression( '/^[0-9]+$/', $bucket, 'bucket is a bare integer' );
			$this->assertGreaterThanOrEqual( 0, (int) $bucket );
			$this->assertLessThanOrEqual( 5, (int) $bucket, 'default rand ceiling is 5' );
		}

		// The filter bounds the ceiling (max( 1, … ) keeps it >= 1). Forcing it to 3 keeps every bucket in 0..3.
		$to_three = function () {
			return 3;
		};
		add_filter( 'masvideos_video_query_max_rand_cache_count', $to_three );
		for ( $i = 0; $i < 40; $i++ ) {
			$bucket = (int) $extract_bucket( $method->invoke( $sc ) );
			$this->assertGreaterThanOrEqual( 0, $bucket );
			$this->assertLessThanOrEqual( 3, $bucket, 'filter ceiling of 3 bounds the bucket' );
		}
		remove_filter( 'masvideos_video_query_max_rand_cache_count', $to_three );
	}

	/* ===================== Filesystem helper ===================== */

	public function test_wp_filesystem_helper_returns_direct_instance() {
		$fs = masvideos_wp_filesystem();
		$this->assertInstanceOf( 'WP_Filesystem_Base', $fs, 'direct filesystem is available in the test environment' );
		$this->assertSame( 'direct', $fs->method );
	}

	public function test_wp_filesystem_helper_null_when_not_direct() {
		// Force a credential-required method; the helper must return null (no prompt, no fallback).
		$GLOBALS['wp_filesystem'] = null;
		add_filter( 'filesystem_method', array( $this, 'force_ftp_method' ) );
		$fs = masvideos_wp_filesystem();
		remove_filter( 'filesystem_method', array( $this, 'force_ftp_method' ) );
		$GLOBALS['wp_filesystem'] = null;
		$this->assertNull( $fs, 'a non-direct method with no credentials yields null (treated as write failure)' );
	}

	public function force_ftp_method() {
		return 'ftp';
	}

	public function grant_edit_themes( $allcaps ) {
		$allcaps['edit_themes'] = true;
		return $allcaps;
	}

	/* ===================== Email template save/delete ===================== */

	private function with_theme_dir( $dir ) {
		$cb = function () use ( $dir ) {
			return $dir;
		};
		add_filter( 'stylesheet_directory', $cb );
		// WP 6.4+ caches get_stylesheet_directory() in $wp_stylesheet_path; clear it so the filter applies.
		$GLOBALS['wp_stylesheet_path'] = null;
		$GLOBALS['wp_template_path']   = null;
		return $cb;
	}

	public function test_save_template_writes_within_theme_dir() {
		$admin = $this->make_user( 'administrator' );
		wp_set_current_user( $admin );
		add_filter( 'user_has_cap', array( $this, 'grant_edit_themes' ) );
		if ( ! current_user_can( 'edit_themes' ) ) {
			$this->markTestSkipped( 'edit_themes not available (e.g. DISALLOW_FILE_EDIT)' );
		}

		$theme = $this->make_tmp_dir();
		$cb    = $this->with_theme_dir( $theme );
		$rel   = 'emails/plain/test-email.php';
		$target = $theme . '/' . MasVideos()->template_path() . $rel;
		wp_mkdir_p( dirname( $target ) );
		file_put_contents( $target, "OLD\n" );

		$email  = $this->make_email_stub();
		$this->invoke_protected( $email, 'save_template', array( "NEW CONTENT\n", $rel ) );

		remove_filter( 'stylesheet_directory', $cb );
		$this->assertStringEqualsFile( $target, "NEW CONTENT\n", 'template file overwritten in place' );
	}

	public function test_save_template_permission_failure_redirects_and_preserves_file() {
		$admin = $this->make_user( 'administrator' );
		wp_set_current_user( $admin );
		add_filter( 'user_has_cap', array( $this, 'grant_edit_themes' ) );
		if ( ! current_user_can( 'edit_themes' ) ) {
			$this->markTestSkipped( 'edit_themes not available' );
		}

		$theme  = $this->make_tmp_dir();
		$cb     = $this->with_theme_dir( $theme );
		$rel    = 'emails/ro-email.php';
		$target = $theme . '/' . MasVideos()->template_path() . $rel;
		wp_mkdir_p( dirname( $target ) );
		file_put_contents( $target, "KEEP\n" );
		chmod( $target, 0444 );

		if ( is_writable( $target ) ) {
			remove_filter( 'stylesheet_directory', $cb );
			$this->markTestSkipped( 'cannot make file read-only (likely running as root)' );
		}

		$email    = $this->make_email_stub();
		$location = $this->run_handler( function () use ( $email, $rel ) {
			$this->invoke_protected( $email, 'save_template', array( "HACKED\n", $rel ) );
		} );

		remove_filter( 'stylesheet_directory', $cb );
		chmod( $target, 0644 );

		$this->assertNotNull( $location, 'a non-writable target must redirect' );
		$this->assertStringContainsString( 'masvideos_error=template_file_not_writable', $location );
		$this->assertStringEqualsFile( $target, "KEEP\n", 'the read-only file is left untouched' );
	}

	public function test_save_template_rejects_path_traversal() {
		$admin = $this->make_user( 'administrator' );
		wp_set_current_user( $admin );
		add_filter( 'user_has_cap', array( $this, 'grant_edit_themes' ) );
		if ( ! current_user_can( 'edit_themes' ) ) {
			$this->markTestSkipped( 'edit_themes not available' );
		}

		$theme  = $this->make_tmp_dir();
		$cb     = $this->with_theme_dir( $theme );
		$outside = dirname( $theme ) . '/escape-' . wp_generate_password( 6, false ) . '.php';
		$this->assertFileDoesNotExist( $outside );

		$email    = $this->make_email_stub();
		$location = $this->run_handler( function () use ( $email, $outside ) {
			$this->invoke_protected( $email, 'save_template', array( "PWNED\n", '../../' . basename( $outside ) ) );
		} );

		remove_filter( 'stylesheet_directory', $cb );
		$this->assertNotNull( $location, 'a traversal path must be rejected via the not-writable redirect' );
		$this->assertStringContainsString( 'masvideos_error=template_file_not_writable', $location );
		$this->assertFileDoesNotExist( $outside, 'no file is written outside the theme directory' );
	}

	/* ===================== TMDB CSV write + cleanup ===================== */

	public function test_tmdb_handle_upload_writes_csv_in_uploads_and_cleans_up() {
		// The importer controller is an admin-only include; load it on demand for the test.
		if ( ! class_exists( 'MasVideos_TMDB_Importer_Controller' ) ) {
			$path = MASVIDEOS_ABSPATH . 'includes/admin/importers/class-masvideos-tmdb-importer-controller.php';
			if ( file_exists( $path ) ) {
				require_once $path;
			}
		}
		if ( ! class_exists( 'MasVideos_TMDB_Importer_Controller' ) ) {
			$this->markTestSkipped( 'TMDB importer controller unavailable' );
		}
		$ref  = new ReflectionClass( 'MasVideos_TMDB_Importer_Controller' );
		$ctrl = $ref->newInstanceWithoutConstructor();

		$this->set_protected( $ctrl, 'results_csv_data_key', array( 'name', 'year' ) );
		$this->set_protected(
			$ctrl,
			'results_csv_data',
			array(
				array( 'name' => 'Movie, With Comma', 'year' => '2020' ),
				array( 'name' => 'Quote "Inside"', 'year' => '2021' ),
			)
		);

		$file = $this->invoke_protected( $ctrl, 'handle_upload', array() );

		$upload_dir = wp_upload_dir( null, false );
		$this->assertStringStartsWith( trailingslashit( $upload_dir['path'] ), $file, 'CSV lands inside the uploads directory' );
		$this->assertFileExists( $file );
		$this->assertStringContainsString( 'masvideos-tmdb-csv-output', basename( $file ) );
		$this->assertDoesNotMatchRegularExpression( '/masvideos-tmdb-csv-output[0-9]+\.csv$/', basename( $file ), 'filename is not a bare predictable timestamp' );

		$contents = file_get_contents( $file );
		$this->assertStringContainsString( 'name,year', $contents, 'header row preserved' );
		$this->assertStringContainsString( '"Movie, With Comma",2020', $contents, 'comma field quoted exactly as fputcsv would' );
		$this->assertStringContainsString( '"Quote ""Inside""",2021', $contents, 'embedded quotes doubled exactly as fputcsv would' );

		// Lifecycle: the matching private attachment is removable and its file is cleaned up.
		$attachments = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'private', 'numberposts' => -1, 'fields' => 'ids' ) );
		$deleted     = false;
		foreach ( $attachments as $att_id ) {
			if ( get_attached_file( $att_id ) === $file ) {
				wp_delete_attachment( $att_id, true );
				$deleted = true;
				break;
			}
		}
		$this->assertTrue( $deleted, 'a private CSV attachment was created' );
		$this->assertFileDoesNotExist( $file, 'cleanup removes the CSV file' );
	}

	/* ===================== Text stripping ===================== */

	public function test_sanitize_term_text_based_strips_markup_and_script_content() {
		$this->assertSame( 'Hello', masvideos_sanitize_term_text_based( '<b>Hello</b>' ) );
		// wp_strip_all_tags removes the CONTENT of script/style (strip_tags would leave "alert(1)Keep").
		$this->assertSame( 'Keep', masvideos_sanitize_term_text_based( '<script>alert(1)</script>Keep' ) );
		// Entities are preserved (not decoded) and surrounding whitespace is trimmed.
		$this->assertSame( 'A &amp; B', masvideos_sanitize_term_text_based( '  A &amp; B  ' ) );
	}

	public function test_breadcrumb_add_crumb_strips_markup() {
		$bc  = new MasVideos_Breadcrumb();
		$bc->add_crumb( '<em>Home</em>', 'https://example.org' );
		$bc->add_crumb( '<script>x()</script>Shop' );
		$crumbs = $bc->get_breadcrumb();
		$this->assertSame( 'Home', $crumbs[0][0] );
		$this->assertSame( 'Shop', $crumbs[1][0], 'script content removed from crumb label' );
	}

	/* ===================== helpers ===================== */

	private function make_email_stub() {
		// MasVideos_Email loads lazily (via MasVideos_Emails::init()); load it on demand for the test.
		if ( ! class_exists( 'MasVideos_Email' ) ) {
			$path = MASVIDEOS_ABSPATH . 'includes/emails/class-masvideos-email.php';
			if ( file_exists( $path ) ) {
				require_once $path;
			}
		}
		if ( ! class_exists( 'MasVideos_Email' ) ) {
			$this->markTestSkipped( 'MasVideos_Email unavailable' );
		}
		return new MasVideos_Email();
	}

	private function invoke_protected( $obj, $method, array $args ) {
		$ref = new ReflectionMethod( $obj, $method );
		$ref->setAccessible( true );
		return $ref->invokeArgs( $obj, $args );
	}

	private function set_protected( $obj, $prop, $value ) {
		$ref = new ReflectionProperty( $obj, $prop );
		$ref->setAccessible( true );
		$ref->setValue( $obj, $value );
	}
}
