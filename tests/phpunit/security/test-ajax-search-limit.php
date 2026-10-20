<?php
/**
 * S-06 regression test: the json_search_* AJAX endpoints bound the requested result limit so a
 * crafted request cannot force an unbounded query.
 *
 * @package MasVideos/Tests
 * @group   security
 * @group   s-06
 */

defined( 'ABSPATH' ) || exit;

class MasVideos_Test_Ajax_Search_Limit extends MasVideos_Unit_Test_Case {

	private function make_matching_videos( $count, $token ) {
		$admin = $this->make_user( 'administrator' );
		for ( $i = 0; $i < $count; $i++ ) {
			$this->make_video( $admin, 'publish', $token . ' ' . $i );
		}
		return $admin;
	}

	private function run_search( $term, $limit ) {
		$_GET['security'] = wp_create_nonce( 'search-videos' );
		$_GET['term']     = $term;
		$_GET['limit']    = $limit;
		$_REQUEST         = $_GET;
		$result           = $this->capture_ajax( array( 'MasVideos_AJAX', 'json_search_videos' ) );
		return is_array( $result['json'] ) ? $result['json'] : array();
	}

	public function test_requested_limit_is_capped() {
		$token = 'ZZSEARCHTOKEN';
		$admin = $this->make_matching_videos( 8, $token );
		wp_set_current_user( $admin );

		$cap = function () {
			return 3;
		};
		add_filter( 'masvideos_json_search_max_limit', $cap );

		$results = $this->run_search( $token, 100000 );
		$this->assertLessThanOrEqual( 3, count( $results ), 'an over-large requested limit must be bounded (S-06)' );

		remove_filter( 'masvideos_json_search_max_limit', $cap );
	}

	public function test_results_available_without_cap_to_prove_bound_is_effective() {
		$token = 'ZZOTHERTOKEN';
		$admin = $this->make_matching_videos( 6, $token );
		wp_set_current_user( $admin );

		// Default max is 100, so all six matches are returned — confirming the previous test's
		// bound of 3 was the cap doing the work, not a lack of data.
		$results = $this->run_search( $token, 100000 );
		$this->assertGreaterThan( 3, count( $results ) );
	}
}
