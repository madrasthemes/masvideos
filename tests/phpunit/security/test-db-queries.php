<?php
/**
 * Phase 2D2 Pass 3 regression tests: database query security and prepared SQL.
 *
 * Exercises the queries touched while clearing the PreparedSQL / DirectDB Plugin Check errors:
 *  - related-content data-store queries (assembled, absint()-escaped fragment query);
 *  - the attribute slug-rename postmeta UPDATE (meta_key now a bound %s placeholder);
 *  - wp_session_cleanup()'s expired-session DELETE (variable-length IN() now fully prepared);
 *  - the get_terms() legacy-signature precedence correction (explicit taxonomy always wins);
 *  - the search data-store query's esc_like + prepare handling of special characters.
 *
 * The assertions intentionally compare real query *results* against fixtures rather than SQL
 * strings, and verify that hostile input neither errors nor mutates protected data.
 *
 * @package MasVideos/Tests
 * @group   security
 * @group   db-queries
 */

defined( 'ABSPATH' ) || exit;

class MasVideos_Test_DB_Queries extends MasVideos_Unit_Test_Case {

	private $created_attribute_ids = array();

	public function tear_down() {
		foreach ( $this->created_attribute_ids as $id ) {
			masvideos_delete_attribute( $id );
		}
		$this->created_attribute_ids = array();
		parent::tear_down();
	}

	/** Create a published video assigned to a video_cat term. */
	private function make_video_in_cat( $author, $term_id ) {
		$v = $this->make_video( $author, 'publish' );
		wp_set_object_terms( $v, array( (int) $term_id ), 'video_cat' );
		return $v;
	}

	private function video_store() {
		return MasVideos_Data_Store::load( 'video' );
	}

	/* ====================== Related-content queries ====================== */

	public function test_related_videos_returns_same_category_videos() {
		$author  = $this->make_user( 'administrator' );
		$cat     = wp_insert_term( 'RelCat', 'video_cat' );
		$this->assertIsArray( $cat );
		$term_id = (int) $cat['term_id'];

		$source = $this->make_video_in_cat( $author, $term_id );
		$rel1   = $this->make_video_in_cat( $author, $term_id );
		$rel2   = $this->make_video_in_cat( $author, $term_id );

		$ids = array_map( 'intval', $this->video_store()->get_related_videos( array( $term_id ), array(), array( 0, $source ), 10, $source ) );

		$this->assertContains( $rel1, $ids );
		$this->assertContains( $rel2, $ids );
		$this->assertNotContains( $source, $ids, 'the source video must be excluded' );
	}

	public function test_related_videos_empty_relationship_returns_empty() {
		// No categories/tags and no force-display => no query, empty result.
		$author = $this->make_user( 'administrator' );
		$video  = $this->make_video( $author, 'publish' );
		$ids    = masvideos_get_related_videos( $video, 5 );
		$this->assertIsArray( $ids );
		$this->assertSame( array(), $ids );
	}

	public function test_related_videos_excludes_unpublished_videos() {
		// Visibility: the related query pins post_status = 'publish'; drafts/private never leak.
		$author  = $this->make_user( 'administrator' );
		$cat     = wp_insert_term( 'VisCat', 'video_cat' );
		$term_id = (int) $cat['term_id'];

		$source = $this->make_video_in_cat( $author, $term_id );
		$draft  = $this->make_video( $author, 'draft' );
		wp_set_object_terms( $draft, array( $term_id ), 'video_cat' );
		$private = $this->make_video( $author, 'private' );
		wp_set_object_terms( $private, array( $term_id ), 'video_cat' );
		$published = $this->make_video_in_cat( $author, $term_id );

		$ids = array_map( 'intval', $this->video_store()->get_related_videos( array( $term_id ), array(), array( 0, $source ), 10, $source ) );

		$this->assertContains( $published, $ids );
		$this->assertNotContains( $draft, $ids, 'draft videos must not appear in related results' );
		$this->assertNotContains( $private, $ids, 'private videos must not appear in related results' );
	}

	public function test_related_videos_cross_type_isolation() {
		// A movie must never surface in video related results (post_type is pinned to 'video').
		$author  = $this->make_user( 'administrator' );
		$cat     = wp_insert_term( 'IsoCat', 'video_cat' );
		$term_id = (int) $cat['term_id'];

		$source = $this->make_video_in_cat( $author, $term_id );
		$rel    = $this->make_video_in_cat( $author, $term_id );
		$movie  = self::factory()->post->create( array( 'post_type' => 'movie', 'post_status' => 'publish', 'post_author' => $author ) );
		wp_set_object_terms( $movie, array( $term_id ), 'video_cat' );

		$ids = array_map( 'intval', $this->video_store()->get_related_videos( array( $term_id ), array(), array( 0, $source ), 10, $source ) );

		$this->assertContains( $rel, $ids );
		$this->assertNotContains( $movie, $ids, 'movies must never appear in video related results' );
	}

	public function test_related_videos_absint_neutralizes_malicious_ids() {
		global $wpdb;
		$author  = $this->make_user( 'administrator' );
		$cat     = wp_insert_term( 'SafeCat', 'video_cat' );
		$term_id = (int) $cat['term_id'];

		$source = $this->make_video_in_cat( $author, $term_id );
		$rel    = $this->make_video_in_cat( $author, $term_id );

		// Inject SQL payloads into the term and exclude arrays; absint() must neutralise them.
		$cats = array( $term_id, "1) UNION SELECT user_pass FROM {$wpdb->users} -- ", '0 OR 1=1' );
		$excl = array( 0, $source, '99 OR 1=1', ';DROP TABLE ' . $wpdb->posts . ';' );

		$wpdb->last_error = '';
		$ids = array();
		try {
			$ids = array_map( 'intval', $this->video_store()->get_related_videos( $cats, array(), $excl, 10, $source ) );
		} catch ( Exception $e ) {
			$this->fail( 'related query must not error on hostile input: ' . $e->getMessage() );
		}

		$this->assertSame( '', $wpdb->last_error, 'hostile IDs must not produce a SQL error' );
		$this->assertContains( $rel, $ids, 'legitimate match still returned alongside neutralised payloads' );
		// Core tables intact; no injection executed.
		$this->assertSame( '1', $wpdb->get_var( 'SELECT 1' ) );
		$this->assertInstanceOf( 'WP_Post', get_post( $source ), 'wp_posts must still exist' );
	}

	public function test_related_videos_query_builder_absints_limit_and_ids() {
		// Directly assert the assembled fragment query only ever interpolates absint()'d integers and
		// trusted identifiers, so the LIMIT and IN() clauses cannot carry injected SQL.
		$q = $this->video_store()->get_related_videos_query(
			array( 5, '7 OR 1=1', '9); DROP TABLE x; --' ), // categories (include_join)
			array(),                                         // tags
			array( 0, '12 UNION SELECT user_pass FROM wp_users' ), // exclude IDs
			'3; DROP TABLE wp_posts'                          // limit
		);

		$this->assertIsArray( $q );
		$this->assertArrayHasKey( 'limits', $q );

		// LIMIT is reduced to the bare integer 3 by absint().
		$this->assertMatchesRegularExpression( '/LIMIT\s+3\b/', $q['limits'] );
		$this->assertStringNotContainsStringIgnoringCase( 'DROP', $q['limits'] );

		// include/exclude IN() lists contain only the absint()'d integers, no payloads.
		$this->assertStringContainsString( '5,7,9', $q['join'], 'category IDs reduced to absint integers' );
		$this->assertStringContainsString( '0,12', $q['where'], 'exclude IDs reduced to absint integers' );
		// Note: 'WHERE 1=1' is a hard-coded idiom in the base query, so it is not an injection marker.
		foreach ( array( 'DROP', 'UNION', 'user_pass' ) as $payload ) {
			$this->assertStringNotContainsStringIgnoringCase( $payload, $q['join'] . $q['where'] . $q['limits'], "no '{$payload}' fragment survives absint()" );
		}
	}

	public function test_related_videos_query_filter_fragment_is_honoured() {
		// The masvideos_video_related_posts_query developer filter can adjust fragments.
		$author  = $this->make_user( 'administrator' );
		$cat     = wp_insert_term( 'FilterCat', 'video_cat' );
		$term_id = (int) $cat['term_id'];

		$source = $this->make_video_in_cat( $author, $term_id );
		$this->make_video_in_cat( $author, $term_id );

		$cb = function ( $query ) {
			$query['limits'] = ' LIMIT 0 ';
			return $query;
		};
		add_filter( 'masvideos_video_related_posts_query', $cb );
		$ids = array_map( 'intval', $this->video_store()->get_related_videos( array( $term_id ), array(), array( 0, $source ), 10, $source ) );
		remove_filter( 'masvideos_video_related_posts_query', $cb );

		$this->assertSame( array(), $ids, 'a LIMIT 0 fragment from the filter yields no rows' );
	}

	/* ====================== Search query (esc_like + prepare) ====================== */

	public function test_search_videos_handles_special_characters() {
		global $wpdb;
		$author = $this->make_user( 'administrator' );
		$match  = $this->make_video( $author, 'publish', "O'Brien 50% _off_ \"quoted\"" );
		$this->make_video( $author, 'publish', 'Unrelated Title' );

		$wpdb->last_error = '';
		$ids = array();
		try {
			$ids = array_map( 'intval', $this->video_store()->search_videos( "O'Brien 50% _off_ \"quoted\"" ) );
		} catch ( Exception $e ) {
			$this->fail( 'search must not error on special characters: ' . $e->getMessage() );
		}

		$this->assertSame( '', $wpdb->last_error, 'special characters must be escaped, not error' );
		$this->assertContains( $match, $ids, 'the matching video is found despite quotes/wildcards' );

		// LIKE wildcards in the term must be treated literally (esc_like), not as wildcards.
		$wild = array_map( 'intval', $this->video_store()->search_videos( '%%%' ) );
		$this->assertNotContains( (int) $match, $wild, 'bare wildcards must not match every row' );
	}

	/* ====================== Attribute slug-rename UPDATE (bound meta_key) ====================== */

	public function test_update_attribute_rename_scopes_postmeta_by_meta_key() {
		global $wpdb;
		$id = masvideos_create_attribute( array( 'name' => 'Color', 'slug' => 'color', 'type' => 'select', 'post_type' => 'video' ) );
		$this->assertIsInt( $id );
		$this->created_attribute_ids[] = $id;

		$author = $this->make_user( 'administrator' );
		$video  = $this->make_video( $author, 'publish' );
		$movie  = self::factory()->post->create( array( 'post_type' => 'movie', 'post_status' => 'publish', 'post_author' => $author ) );

		// Seed stored-attribute postmeta using the exact token the production query searches for.
		$old_token = 's:' . ( strlen( 'color' ) + 3 ) . ':"video_color"';
		update_post_meta( $video, '_video_attributes', 'wrap[' . $old_token . ']' );
		update_post_meta( $movie, '_movie_attributes', 'MOVIE_UNTOUCHED' );

		$wpdb->last_error = '';
		$result = masvideos_update_attribute( $id, array( 'name' => 'Colour', 'slug' => 'colour', 'type' => 'select', 'post_type' => 'video' ) );

		$this->assertNotWPError( $result );
		$this->assertSame( '', $wpdb->last_error, 'the slug-rename UPDATE must not raise a DB error' );

		// The meta_key = %s placeholder scopes the REPLACE to video attributes only.
		$this->assertSame( 'MOVIE_UNTOUCHED', get_post_meta( $movie, '_movie_attributes', true ), 'movie-scoped meta (other meta_key) must be untouched' );
		$video_meta = get_post_meta( $video, '_video_attributes', true );
		$this->assertStringContainsString( 'colour', $video_meta, 'old slug token rewritten to the new slug' );
		$this->assertStringNotContainsString( 'video_color"', $video_meta, 'old slug token no longer present' );
	}

	public function test_update_attribute_rejects_injection_post_type_before_query() {
		global $wpdb;
		$wpdb->last_error = '';
		$result = masvideos_update_attribute(
			0,
			array( 'name' => 'Evil', 'slug' => 'evil', 'type' => 'select', 'post_type' => "video'; DROP TABLE {$wpdb->postmeta}; --" )
		);
		$this->assertWPError( $result );
		$this->assertSame( 'invalid_masvideos_attribute_post_type', $result->get_error_code() );
		$this->assertSame( '', $wpdb->last_error );
	}

	/* ====================== wp_session_cleanup() prepared IN() delete ====================== */

	/**
	 * The wp_session_* functions live in a library loaded only when the session handler initialises,
	 * which does not happen in the test harness. Load it on demand and drop the shutdown writer so the
	 * test can exercise wp_session_cleanup() in isolation.
	 */
	private function ensure_wp_session_loaded() {
		if ( ! function_exists( 'wp_session_cleanup' ) ) {
			require_once MASVIDEOS_ABSPATH . 'includes/libraries/wp-session.php';
			remove_action( 'shutdown', 'wp_session_write_close' );
		}
		return function_exists( 'wp_session_cleanup' ) && ! defined( 'WP_INSTALLING' );
	}

	public function test_wp_session_cleanup_deletes_expired_keeps_valid() {
		global $wpdb;
		if ( ! $this->ensure_wp_session_loaded() ) {
			$this->markTestSkipped( 'wp_session_cleanup() not runnable in this context' );
		}

		$expired_id = str_repeat( 'a', 32 );
		$valid_id   = str_repeat( 'b', 32 );
		$bad_id     = 'not-hex-id'; // fails the [a-f0-9]{32} guard => skipped, never deleted.

		update_option( '_wp_session_expires_' . $expired_id, time() - 100 );
		update_option( '_wp_session_' . $expired_id, 'payload-a' );
		update_option( '_wp_session_expires_' . $valid_id, time() + HOUR_IN_SECONDS );
		update_option( '_wp_session_' . $valid_id, 'payload-b' );
		update_option( '_wp_session_expires_' . $bad_id, time() - 100 );

		$wpdb->last_error = '';
		wp_session_cleanup();
		$this->assertSame( '', $wpdb->last_error, 'prepared IN() delete must not raise a DB error' );

		// The cleanup deletes directly; drop the stale options cache before reading back.
		wp_cache_delete( 'alloptions', 'options' );

		$this->assertFalse( get_option( '_wp_session_expires_' . $expired_id ), 'expired expiry row removed' );
		$this->assertFalse( get_option( '_wp_session_' . $expired_id ), 'expired session data removed' );
		$this->assertNotFalse( get_option( '_wp_session_expires_' . $valid_id ), 'valid session preserved' );
		$this->assertNotFalse( get_option( '_wp_session_' . $valid_id ), 'valid session data preserved' );
		$this->assertNotFalse( get_option( '_wp_session_expires_' . $bad_id ), 'malformed-id row skipped, not deleted' );

		delete_option( '_wp_session_expires_' . $valid_id );
		delete_option( '_wp_session_' . $valid_id );
		delete_option( '_wp_session_expires_' . $bad_id );
	}

	public function test_wp_session_cleanup_empty_set_no_error() {
		global $wpdb;
		if ( ! $this->ensure_wp_session_loaded() ) {
			$this->markTestSkipped( 'wp_session_cleanup() not runnable in this context' );
		}
		// No expired sessions present: the IN() delete branch must be skipped without error.
		$wpdb->last_error = '';
		wp_session_cleanup();
		$this->assertSame( '', $wpdb->last_error );
	}

	/* ====================== get_terms() legacy-signature precedence (§6) ====================== */

	public function test_get_terms_explicit_taxonomy_always_wins_over_args() {
		$a = wp_insert_term( 'CatAlpha', 'video_cat' );
		$b = wp_insert_term( 'TagBeta', 'video_tag' );
		$this->assertIsArray( $a );
		$this->assertIsArray( $b );

		// Corrected single-array form: the explicit taxonomy (video_cat) must win even when $args
		// carries a conflicting 'taxonomy' key, matching legacy get_terms( $taxonomy, $args ).
		$corrected = get_terms(
			array_merge(
				wp_parse_args( array( 'hide_empty' => false, 'fields' => 'names', 'taxonomy' => 'video_tag' ) ),
				array( 'taxonomy' => 'video_cat' )
			)
		);
		$this->assertContains( 'CatAlpha', $corrected );
		$this->assertNotContains( 'TagBeta', $corrected, 'a taxonomy key in $args must not override the explicit taxonomy' );

		// Equivalent to the canonical modern call that pins the intended taxonomy with no conflict.
		$canonical = get_terms( array( 'taxonomy' => 'video_cat', 'hide_empty' => false, 'fields' => 'names' ) );
		sort( $corrected );
		sort( $canonical );
		$this->assertSame( $canonical, $corrected );
	}

	public function test_get_video_terms_taxonomy_not_hijacked_by_args() {
		$author = $this->make_user( 'administrator' );
		$video  = $this->make_video( $author, 'publish' );

		$cat = wp_insert_term( 'HJCat', 'video_cat' );
		$tag = wp_insert_term( 'HJTag', 'video_tag' );
		$this->assertIsArray( $cat );
		$this->assertIsArray( $tag );
		wp_set_object_terms( $video, array( (int) $cat['term_id'] ), 'video_cat' );
		wp_set_object_terms( $video, array( (int) $tag['term_id'] ), 'video_tag' );

		// menu_order path reaches the corrected get_terms(); the explicit 'video_cat' must win over
		// the 'taxonomy' => 'video_tag' hijack attempt in $args.
		$names = masvideos_get_video_terms(
			$video,
			'video_cat',
			array( 'orderby' => 'menu_order', 'fields' => 'names', 'taxonomy' => 'video_tag' )
		);
		$this->assertIsArray( $names );
		$this->assertContains( 'HJCat', $names, 'terms come from the explicit video_cat taxonomy' );
		$this->assertNotContains( 'HJTag', $names, '$args[taxonomy] must not redirect the query to video_tag' );
	}
}
