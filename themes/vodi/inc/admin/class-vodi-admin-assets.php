<?php
/**
 * Load assets
 *
 * @author      CheThemes
 * @category    Admin
 * @package     Vodi/Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Vodi_Admin_Assets' ) ) :

/**
 * Vodi_Admin_Assets Class.
 */
class Vodi_Admin_Assets {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
	}

	/**
	 * Enqueue styles.
	 */
	public function admin_styles() {
		global $wp_scripts, $vodi_version;

		$screen         = get_current_screen();
		$screen_id      = $screen ? $screen->id : '';
		$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';

		// Register admin styles
		wp_register_style( 'vodi_post_widget_admin_style', get_template_directory_uri() . '/assets/css/admin/vpw-admin.min.css', array(), $vodi_version );
		wp_enqueue_style( 'vodi_post_widget_admin_style' );
	}

	/**
	 * Enqueue scripts.
	 */
	public function admin_scripts() {
		global $wp_query, $post, $vodi_version;

		$screen       = get_current_screen();
		$screen_id    = $screen ? $screen->id : '';
		$ec_screen_id = sanitize_title( esc_html__( 'vodi', 'vodi' ) );
		$suffix       = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_script( 'vodi_post_widget_admin_script', get_template_directory_uri() . '/assets/js/admin/vpw-admin.js', array(), $vodi_version );

		wp_enqueue_script( 'vodi_post_widget_admin_script' );

	}
}
endif;

return new Vodi_Admin_Assets();