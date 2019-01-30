<?php
/**
 * Structured data's handler and generator using JSON-LD format.
 *
 * @package MasVideos/Classes
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Structured data class.
 */
class MasVideos_Structured_Data {

	/**
	 * Stores the structured data.
	 *
	 * @var array $_data Array of structured data.
	 */
	private $_data = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Generate structured data.
		add_action( 'masvideos_breadcrumb', array( $this, 'generate_breadcrumblist_data' ), 10 );
	}

	/**
	 * Sets data.
	 *
	 * @param  array $data  Structured data.
	 * @param  bool  $reset Unset data (default: false).
	 * @return bool
	 */
	public function set_data( $data, $reset = false ) {
		if ( ! isset( $data['@type'] ) || ! preg_match( '|^[a-zA-Z]{1,20}$|', $data['@type'] ) ) {
			return false;
		}

		if ( $reset && isset( $this->_data ) ) {
			unset( $this->_data );
		}

		$this->_data[] = $data;

		return true;
	}

	/**
	 * Gets data.
	 *
	 * @return array
	 */
	public function get_data() {
		return $this->_data;
	}

	/**
	 * Structures and returns data.
	 *
	 * List of types available by default for specific request:
	 *
	 * 'breadcrumblist',
	 *
	 * @param  array $types Structured data types.
	 * @return array
	 */
	public function get_structured_data( $types ) {
		$data = array();

		// Put together the values of same type of structured data.
		foreach ( $this->get_data() as $value ) {
			$data[ strtolower( $value['@type'] ) ][] = $value;
		}

		// Wrap the multiple values of each type inside a graph... Then add context to each type.
		foreach ( $data as $type => $value ) {
			$data[ $type ] = count( $value ) > 1 ? array( '@graph' => $value ) : $value[0];
			$data[ $type ] = apply_filters( 'masvideos_structured_data_context', array( '@context' => 'https://schema.org/' ), $data, $type, $value ) + $data[ $type ];
		}

		// If requested types, pick them up... Finally change the associative array to an indexed one.
		$data = $types ? array_values( array_intersect_key( $data, array_flip( $types ) ) ) : array_values( $data );

		if ( ! empty( $data ) ) {
			if ( 1 < count( $data ) ) {
				$data = apply_filters( 'masvideos_structured_data_context', array( '@context' => 'https://schema.org/' ), $data, '', '' ) + array( '@graph' => $data );
			} else {
				$data = $data[0];
			}
		}

		return $data;
	}

	/**
	 * Get data types for pages.
	 *
	 * @return array
	 */
	protected function get_data_type_for_page() {
		$types   = array();
		$types[] = is_movies() || movie_genre() || is_movie() ? 'movie' : '';
		$types[] = is_movies() && is_front_page() ? 'website' : '';
		$types[] = is_movie() ? 'review' : '';
		$types[] = ! is_movies() ? 'breadcrumblist' : '';
		$types[] = 'order';

		return array_filter( apply_filters( 'masvideos_structured_data_type_for_page', $types ) );
	}

	/**
	 * Makes sure email structured data only outputs on non-plain text versions.
	 *
	 * @param WP_Order $order         Order data.
	 * @param bool     $sent_to_admin Send to admin (default: false).
	 * @param bool     $plain_text    Plain text email (default: false).
	 */
	public function output_email_structured_data( $order, $sent_to_admin = false, $plain_text = false ) {
		if ( $plain_text ) {
			return;
		}
		echo '<div style="display: none; font-size: 0; max-height: 0; line-height: 0; padding: 0; mso-hide: all;">';
		$this->output_structured_data();
		echo '</div>';
	}

	/**
	 * Sanitizes, encodes and outputs structured data.
	 *
	 * Hooked into `wp_footer` action hook.
	 * Hooked into `masvideos_email_order_details` action hook.
	 */
	public function output_structured_data() {
		$types = $this->get_data_type_for_page();
		$data  = $this->get_structured_data( $types );

		if ( $data ) {
			echo '<script type="application/ld+json">' . wp_json_encode( $data ) . '</script>';
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Generators
	|--------------------------------------------------------------------------
	|
	| Methods for generating specific structured data types:
	|
	| - BreadcrumbList
	|
	| The generated data is stored into `$this->_data`.
	| See the methods above for handling `$this->_data`.
	|
	*/

	/**
	 * Generates BreadcrumbList structured data.
	 *
	 * Hooked into `masvideos_breadcrumb` action hook.
	 *
	 * @param MasVideos_Breadcrumb $breadcrumbs Breadcrumb data.
	 */
	public function generate_breadcrumblist_data( $breadcrumbs ) {
		$crumbs = $breadcrumbs->get_breadcrumb();

		if ( empty( $crumbs ) || ! is_array( $crumbs ) ) {
			return;
		}

		$markup                    = array();
		$markup['@type']           = 'BreadcrumbList';
		$markup['itemListElement'] = array();

		foreach ( $crumbs as $key => $crumb ) {
			$markup['itemListElement'][ $key ] = array(
				'@type'    => 'ListItem',
				'position' => $key + 1,
				'item'     => array(
					'name' => $crumb[0],
				),
			);

			if ( ! empty( $crumb[1] ) ) {
				$markup['itemListElement'][ $key ]['item'] += array( '@id' => $crumb[1] );
			}
		}

		$this->set_data( apply_filters( 'masvideos_structured_data_breadcrumblist', $markup, $breadcrumbs ) );
	}
}
