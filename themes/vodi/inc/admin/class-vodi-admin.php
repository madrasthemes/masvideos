<?php
/**
 * Vodi Admin Class
 *
 * @author   WooThemes
 * @package  Vodi
 * @since    2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Vodi_Admin' ) ) :
	/**
	 * The Vodi admin class
	 */
	class Vodi_Admin {

		/**
		 * Setup class.
		 *
		 * @since 1.0
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'includes' ) );
		}

		/**
		 * Include any classes we need within admin
		 */
		public function includes() {
			include_once get_template_directory() . '/inc/admin/class-vodi-admin-assets.php';
		}


		/**
		 * Get product data from json
		 *
		 * @param  string $url       URL to the json file.
		 * @param  string $transient Name the transient.
		 * @return [type]            [description]
		 */
		public function get_vodi_product_data( $url, $transient ) {
			$raw_products = wp_safe_remote_get( $url );
			$products     = json_decode( wp_remote_retrieve_body( $raw_products ) );

			if ( ! empty( $products ) ) {
				set_transient( $transient, $products, DAY_IN_SECONDS );
			}

			return $products;
		}
	}

endif;

return new Vodi_Admin();
