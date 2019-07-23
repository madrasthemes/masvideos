<?php
/**
 * Init MasVideos data importers.
 *
 * @package MasVideos/Admin
 */

defined( 'ABSPATH' ) || exit;

/**
 * MasVideos_Admin_Importers Class.
 */
class MasVideos_Admin_Importers {

	/**
	 * Array of importer IDs.
	 *
	 * @var string[]
	 */
	protected $importers = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( ! $this->import_allowed() ) {
			return;
		}

		add_action( 'admin_menu', array( $this, 'add_to_menus' ) );
		add_action( 'admin_init', array( $this, 'register_importers' ) );
		add_action( 'admin_head', array( $this, 'hide_from_menus' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_action( 'wp_ajax_masvideos_do_ajax_person_import', array( $this, 'do_ajax_person_import' ) );
		add_action( 'wp_ajax_masvideos_do_ajax_tv_show_import', array( $this, 'do_ajax_tv_show_import' ) );
		add_action( 'wp_ajax_masvideos_do_ajax_video_import', array( $this, 'do_ajax_video_import' ) );
		add_action( 'wp_ajax_masvideos_do_ajax_movie_import', array( $this, 'do_ajax_movie_import' ) );

		// Register MasVideos importers.
		$this->importers['person_importer'] = array(
			'menu'       => 'edit.php?post_type=person',
			'name'       => __( 'Person Import', 'masvideos' ),
			'capability' => 'import',
			'callback'   => array( $this, 'person_importer' ),
		);

		$this->importers['tv_show_importer'] = array(
			'menu'       => 'edit.php?post_type=tv_show',
			'name'       => __( 'TV Show Import', 'masvideos' ),
			'capability' => 'import',
			'callback'   => array( $this, 'tv_show_importer' ),
		);

		$this->importers['video_importer'] = array(
			'menu'       => 'edit.php?post_type=video',
			'name'       => __( 'Video Import', 'masvideos' ),
			'capability' => 'import',
			'callback'   => array( $this, 'video_importer' ),
		);

		$this->importers['movie_importer'] = array(
			'menu'       => 'edit.php?post_type=movie',
			'name'       => __( 'Movie Import', 'masvideos' ),
			'capability' => 'import',
			'callback'   => array( $this, 'movie_importer' ),
		);

		$this->importers['tmdb_importer'] = array(
			'menu'       => 'import.php',
			'name'       => __( 'TMDB Import', 'masvideos' ),
			'capability' => 'import',
			'callback'   => array( $this, 'tmdb_importer' ),
		);
	}

	/**
	 * Return true if MasVideos imports are allowed for current user, false otherwise.
	 *
	 * @return bool Whether current user can perform imports.
	 */
	protected function import_allowed() {
		return ( current_user_can( 'edit_tv_shows' ) || current_user_can( 'edit_videos' ) || current_user_can( 'edit_movies' ) || current_user_can( 'edit_persons' ) ) && current_user_can( 'import' );
	}

	/**
	 * Add menu items for our custom importers.
	 */
	public function add_to_menus() {
		foreach ( $this->importers as $id => $importer ) {
			add_submenu_page( $importer['menu'], $importer['name'], $importer['name'], $importer['capability'], $id, $importer['callback'] );
		}
	}

	/**
	 * Hide menu items from view so the pages exist, but the menu items do not.
	 */
	public function hide_from_menus() {
		global $submenu;

		foreach ( $this->importers as $id => $importer ) {
			if ( isset( $submenu[ $importer['menu'] ] ) ) {
				foreach ( $submenu[ $importer['menu'] ] as $key => $menu ) {
					if ( $id === $menu[2] ) {
						unset( $submenu[ $importer['menu'] ][ $key ] );
					}
				}
			}
		}
	}

	/**
	 * Register importer scripts.
	 */
	public function admin_scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_register_script( 'masvideos-person-import', MasVideos()->plugin_url() . '/assets/js/admin/masvideos-person-import' . $suffix . '.js', array( 'jquery' ), MASVIDEOS_VERSION );
		wp_register_script( 'masvideos-tv-show-import', MasVideos()->plugin_url() . '/assets/js/admin/masvideos-tv-show-import' . $suffix . '.js', array( 'jquery' ), MASVIDEOS_VERSION );
		wp_register_script( 'masvideos-video-import', MasVideos()->plugin_url() . '/assets/js/admin/masvideos-video-import' . $suffix . '.js', array( 'jquery' ), MASVIDEOS_VERSION );
		wp_register_script( 'masvideos-movie-import', MasVideos()->plugin_url() . '/assets/js/admin/masvideos-movie-import' . $suffix . '.js', array( 'jquery' ), MASVIDEOS_VERSION );
	}

	/**
	 * Register WordPress based importers.
	 */
	public function register_importers() {
		if ( defined( 'WP_LOAD_IMPORTERS' ) ) {
			add_action( 'import_start', array( $this, 'post_importer_compatibility' ) );
			register_importer( 'masvideos_person_csv', __( 'MAS Videos Persons (CSV)', 'masvideos' ), __( 'Import <strong>persons</strong> to your website via a csv file.', 'masvideos' ), array( $this, 'person_importer' ) );
			register_importer( 'masvideos_tv_show_csv', __( 'MAS Videos TV Shows (CSV)', 'masvideos' ), __( 'Import <strong>tv shows</strong> to your website via a csv file.', 'masvideos' ), array( $this, 'tv_show_importer' ) );
			register_importer( 'masvideos_video_csv', __( 'MAS Videos Videos (CSV)', 'masvideos' ), __( 'Import <strong>videos</strong> to your website via a csv file.', 'masvideos' ), array( $this, 'video_importer' ) );
			register_importer( 'masvideos_movie_csv', __( 'MAS Videos Movies (CSV)', 'masvideos' ), __( 'Import <strong>movies</strong> to your website via a csv file.', 'masvideos' ), array( $this, 'movie_importer' ) );
			register_importer( 'masvideos_tmdb_import', __( 'MAS Videos TMDB Import', 'masvideos' ), __( 'Import <strong>movies, tv shows, persons</strong> to your website via TMDB API.', 'masvideos' ), array( $this, 'tmdb_importer' ) );
		}
	}

	/**
	 * When running the WP XML importer, ensure attributes exist.
	 *
	 * WordPress import should work - however, it fails to import custom movie attribute taxonomies.
	 * This code grabs the file before it is imported and ensures the taxonomies are created.
	 */
	public function post_importer_compatibility() {
		global $wpdb;

		if ( empty( $_POST['import_id'] ) || ! class_exists( 'WXR_Parser' ) ) { // PHPCS: input var ok, CSRF ok.
			return;
		}

		$id          = absint( $_POST['import_id'] ); // PHPCS: input var ok.
		$file        = get_attached_file( $id );
		$parser      = new WXR_Parser();
		$import_data = $parser->parse( $file );

		if ( isset( $import_data['posts'] ) && ! empty( $import_data['posts'] ) ) {
			foreach ( $import_data['posts'] as $post ) {
				if ( in_array( $post['post_type'], array( 'tv_show', 'video', 'movie' ) ) && ! empty( $post['terms'] ) ) {
					foreach ( $post['terms'] as $term ) {
						if ( strstr( $term['domain'], $post['post_type'] . '_' ) ) {
							if ( ! taxonomy_exists( $term['domain'] ) ) {
								$attribute_name = masvideos_sanitize_taxonomy_name( str_replace( $post['post_type'] . '_', '', $term['domain'] ) );

								// Create the taxonomy.
								if ( ! in_array( $attribute_name, masvideos_get_attribute_taxonomies(), true ) ) {
									masvideos_create_attribute(
										array(
											'name'         => $attribute_name,
											'slug'         => $attribute_name,
											'type'         => 'select',
											'order_by'     => 'menu_order',
											'has_archives' => false,
										)
									);
								}

								// Register the taxonomy now so that the import works!
								register_taxonomy(
									$term['domain'],
									apply_filters( 'masvideos_taxonomy_objects_' . $term['domain'], array( $post['post_type'] ) ),
									apply_filters(
										'masvideos_taxonomy_args_' . $term['domain'], array(
											'hierarchical' => true,
											'show_ui'      => false,
											'query_var'    => true,
											'rewrite'      => false,
										)
									)
								);
							}
						}
					}
				}
			}
		}
	}

	/**
	 * The person importer.
	 *
	 * This has a custom screen - the Tools > Import item is a placeholder.
	 * If we're on that screen, redirect to the custom one.
	 */
	public function person_importer() {
		if ( defined( 'WP_LOAD_IMPORTERS' ) ) {
			wp_safe_redirect( admin_url( 'edit.php?post_type=person&page=person_importer' ) );
			exit;
		}

		include_once MASVIDEOS_ABSPATH . 'includes/import/class-masvideos-person-csv-importer.php';
		include_once MASVIDEOS_ABSPATH . 'includes/admin/importers/class-masvideos-person-csv-importer-controller.php';

		$importer = new MasVideos_Person_CSV_Importer_Controller();
		$importer->dispatch();
	}

	/**
	 * The tv show importer.
	 *
	 * This has a custom screen - the Tools > Import item is a placeholder.
	 * If we're on that screen, redirect to the custom one.
	 */
	public function tv_show_importer() {
		if ( defined( 'WP_LOAD_IMPORTERS' ) ) {
			wp_safe_redirect( admin_url( 'edit.php?post_type=tv_show&page=tv_show_importer' ) );
			exit;
		}

		include_once MASVIDEOS_ABSPATH . 'includes/import/class-masvideos-tv-show-csv-importer.php';
		include_once MASVIDEOS_ABSPATH . 'includes/admin/importers/class-masvideos-tv-show-csv-importer-controller.php';

		$importer = new MasVideos_TV_Show_CSV_Importer_Controller();
		$importer->dispatch();
	}

	/**
	 * The video importer.
	 *
	 * This has a custom screen - the Tools > Import item is a placeholder.
	 * If we're on that screen, redirect to the custom one.
	 */
	public function video_importer() {
		if ( defined( 'WP_LOAD_IMPORTERS' ) ) {
			wp_safe_redirect( admin_url( 'edit.php?post_type=video&page=video_importer' ) );
			exit;
		}

		include_once MASVIDEOS_ABSPATH . 'includes/import/class-masvideos-video-csv-importer.php';
		include_once MASVIDEOS_ABSPATH . 'includes/admin/importers/class-masvideos-video-csv-importer-controller.php';

		$importer = new MasVideos_Video_CSV_Importer_Controller();
		$importer->dispatch();
	}

	/**
	 * The movie importer.
	 *
	 * This has a custom screen - the Tools > Import item is a placeholder.
	 * If we're on that screen, redirect to the custom one.
	 */
	public function movie_importer() {
		if ( defined( 'WP_LOAD_IMPORTERS' ) ) {
			wp_safe_redirect( admin_url( 'edit.php?post_type=movie&page=movie_importer' ) );
			exit;
		}

		include_once MASVIDEOS_ABSPATH . 'includes/import/class-masvideos-movie-csv-importer.php';
		include_once MASVIDEOS_ABSPATH . 'includes/admin/importers/class-masvideos-movie-csv-importer-controller.php';

		$importer = new MasVideos_Movie_CSV_Importer_Controller();
		$importer->dispatch();
	}

	/**
	 * The tmdb importer.
	 *
	 * This has a custom screen - the Tools > Import item is a placeholder.
	 * If we're on that screen, redirect to the custom one.
	 */
	public function tmdb_importer() {
		if ( defined( 'WP_LOAD_IMPORTERS' ) ) {
			wp_safe_redirect( admin_url( 'admin.php?page=tmdb_importer' ) );
			exit;
		}

		include_once MASVIDEOS_ABSPATH . 'includes/admin/importers/class-masvideos-tmdb-importer-controller.php';

		$importer = new MasVideos_TMDB_Importer_Controller();
		$importer->dispatch();
	}

	/**
	 * Ajax callback for importing one batch of movies from a CSV.
	 */
	public function do_ajax_person_import() {
		global $wpdb;

		check_ajax_referer( 'masvideos-person-import', 'security' );

		if ( ! $this->import_allowed() || ! isset( $_POST['file'] ) ) { // PHPCS: input var ok.
			wp_send_json_error( array( 'message' => __( 'Insufficient privileges to import persons.', 'masvideos' ) ) );
		}

		include_once MASVIDEOS_ABSPATH . 'includes/admin/importers/class-masvideos-person-csv-importer-controller.php';
		include_once MASVIDEOS_ABSPATH . 'includes/import/class-masvideos-person-csv-importer.php';

		$file   = masvideos_clean( wp_unslash( $_POST['file'] ) ); // PHPCS: input var ok.
		$params = array(
			'delimiter'       => ! empty( $_POST['delimiter'] ) ? masvideos_clean( wp_unslash( $_POST['delimiter'] ) ) : ',', // PHPCS: input var ok.
			'start_pos'       => isset( $_POST['position'] ) ? absint( $_POST['position'] ) : 0, // PHPCS: input var ok.
			'mapping'         => isset( $_POST['mapping'] ) ? (array) masvideos_clean( wp_unslash( $_POST['mapping'] ) ) : array(), // PHPCS: input var ok.
			'update_existing' => isset( $_POST['update_existing'] ) ? (bool) $_POST['update_existing'] : false, // PHPCS: input var ok.
			'lines'           => apply_filters( 'masvideos_person_import_batch_size', 1 ),
			'parse'           => true,
		);

		// Log failures.
		if ( 0 !== $params['start_pos'] ) {
			$error_log = array_filter( (array) get_user_option( 'person_import_error_log' ) );
		} else {
			$error_log = array();
		}

		$importer         = MasVideos_Person_CSV_Importer_Controller::get_importer( $file, $params );
		$results          = $importer->import();
		$percent_complete = $importer->get_percent_complete();
		$error_log        = array_merge( $error_log, $results['failed'], $results['skipped'] );

		update_user_option( get_current_user_id(), 'person_import_error_log', $error_log );

		if ( 100 === $percent_complete ) {
			// @codingStandardsIgnoreStart.
			$wpdb->delete( $wpdb->postmeta, array( 'meta_key' => '_original_id' ) );
			$wpdb->delete( $wpdb->posts, array(
				'post_type'   => 'person',
				'post_status' => 'importing',
			) );
			// @codingStandardsIgnoreEnd.

			// Clean up orphaned data.
			$wpdb->query( "
				DELETE {$wpdb->postmeta}.* FROM {$wpdb->postmeta}
				LEFT JOIN {$wpdb->posts} wp ON wp.ID = {$wpdb->postmeta}.post_id
				WHERE wp.ID IS NULL
			" );
			// @codingStandardsIgnoreStart.
			$wpdb->query( "
				DELETE tr.* FROM {$wpdb->term_relationships} tr
				LEFT JOIN {$wpdb->posts} wp ON wp.ID = tr.object_id
				LEFT JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
				WHERE wp.ID IS NULL
				AND tt.taxonomy IN ( '" . implode( "','", array_map( 'esc_sql', get_object_taxonomies( 'person' ) ) ) . "' )
			" );
			// @codingStandardsIgnoreEnd.

			// Send success.
			wp_send_json_success(
				array(
					'position'   => 'done',
					'percentage' => 100,
					'url'        => add_query_arg( array( 'nonce' => wp_create_nonce( 'person-csv' ) ), admin_url( 'edit.php?post_type=person&page=person_importer&step=done' ) ),
					'imported'   => count( $results['imported'] ),
					'failed'     => count( $results['failed'] ),
					'updated'    => count( $results['updated'] ),
					'skipped'    => count( $results['skipped'] ),
				)
			);
		} else {
			wp_send_json_success(
				array(
					'position'   => $importer->get_file_position(),
					'percentage' => $percent_complete,
					'imported'   => count( $results['imported'] ),
					'failed'     => count( $results['failed'] ),
					'updated'    => count( $results['updated'] ),
					'skipped'    => count( $results['skipped'] ),
				)
			);
		}
	}

	/**
	 * Ajax callback for importing one batch of tv shows from a CSV.
	 */
	public function do_ajax_tv_show_import() {
		global $wpdb;

		check_ajax_referer( 'masvideos-tv-show-import', 'security' );

		if ( ! $this->import_allowed() || ! isset( $_POST['file'] ) ) { // PHPCS: input var ok.
			wp_send_json_error( array( 'message' => __( 'Insufficient privileges to import tv shows.', 'masvideos' ) ) );
		}

		include_once MASVIDEOS_ABSPATH . 'includes/admin/importers/class-masvideos-tv-show-csv-importer-controller.php';
		include_once MASVIDEOS_ABSPATH . 'includes/import/class-masvideos-tv-show-csv-importer.php';

		$file   = masvideos_clean( wp_unslash( $_POST['file'] ) ); // PHPCS: input var ok.
		$params = array(
			'delimiter'       => ! empty( $_POST['delimiter'] ) ? masvideos_clean( wp_unslash( $_POST['delimiter'] ) ) : ',', // PHPCS: input var ok.
			'start_pos'       => isset( $_POST['position'] ) ? absint( $_POST['position'] ) : 0, // PHPCS: input var ok.
			'mapping'         => isset( $_POST['mapping'] ) ? (array) masvideos_clean( wp_unslash( $_POST['mapping'] ) ) : array(), // PHPCS: input var ok.
			'update_existing' => isset( $_POST['update_existing'] ) ? (bool) $_POST['update_existing'] : false, // PHPCS: input var ok.
			'lines'           => apply_filters( 'masvideos_tv_show_import_batch_size', 1 ),
			'parse'           => true,
		);

		// Log failures.
		if ( 0 !== $params['start_pos'] ) {
			$error_log = array_filter( (array) get_user_option( 'tv_show_import_error_log' ) );
		} else {
			$error_log = array();
		}

		$importer         = MasVideos_TV_Show_CSV_Importer_Controller::get_importer( $file, $params );
		$results          = $importer->import();
		$percent_complete = $importer->get_percent_complete();
		$error_log        = array_merge( $error_log, $results['failed'], $results['skipped'] );

		update_user_option( get_current_user_id(), 'tv_show_import_error_log', $error_log );

		if ( 100 === $percent_complete ) {
			// @codingStandardsIgnoreStart.
			$wpdb->delete( $wpdb->postmeta, array( 'meta_key' => '_original_id' ) );
			$wpdb->delete( $wpdb->posts, array(
				'post_type'   => 'tv_show',
				'post_status' => 'importing',
			) );
			// @codingStandardsIgnoreEnd.

			// Clean up orphaned data.
			$wpdb->query( "
				DELETE {$wpdb->postmeta}.* FROM {$wpdb->postmeta}
				LEFT JOIN {$wpdb->posts} wp ON wp.ID = {$wpdb->postmeta}.post_id
				WHERE wp.ID IS NULL
			" );
			// @codingStandardsIgnoreStart.
			$wpdb->query( "
				DELETE tr.* FROM {$wpdb->term_relationships} tr
				LEFT JOIN {$wpdb->posts} wp ON wp.ID = tr.object_id
				LEFT JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
				WHERE wp.ID IS NULL
				AND tt.taxonomy IN ( '" . implode( "','", array_map( 'esc_sql', get_object_taxonomies( 'tv_show' ) ) ) . "' )
			" );
			// @codingStandardsIgnoreEnd.

			// Send success.
			wp_send_json_success(
				array(
					'position'   => 'done',
					'percentage' => 100,
					'url'        => add_query_arg( array( 'nonce' => wp_create_nonce( 'tv-show-csv' ) ), admin_url( 'edit.php?post_type=tv_show&page=tv_show_importer&step=done' ) ),
					'imported'   => count( $results['imported'] ),
					'failed'     => count( $results['failed'] ),
					'updated'    => count( $results['updated'] ),
					'skipped'    => count( $results['skipped'] ),
				)
			);
		} else {
			wp_send_json_success(
				array(
					'position'   => $importer->get_file_position(),
					'percentage' => $percent_complete,
					'imported'   => count( $results['imported'] ),
					'failed'     => count( $results['failed'] ),
					'updated'    => count( $results['updated'] ),
					'skipped'    => count( $results['skipped'] ),
				)
			);
		}
	}

	/**
	 * Ajax callback for importing one batch of movies from a CSV.
	 */
	public function do_ajax_video_import() {
		global $wpdb;

		check_ajax_referer( 'masvideos-video-import', 'security' );

		if ( ! $this->import_allowed() || ! isset( $_POST['file'] ) ) { // PHPCS: input var ok.
			wp_send_json_error( array( 'message' => __( 'Insufficient privileges to import videos.', 'masvideos' ) ) );
		}

		include_once MASVIDEOS_ABSPATH . 'includes/admin/importers/class-masvideos-video-csv-importer-controller.php';
		include_once MASVIDEOS_ABSPATH . 'includes/import/class-masvideos-video-csv-importer.php';

		$file   = masvideos_clean( wp_unslash( $_POST['file'] ) ); // PHPCS: input var ok.
		$params = array(
			'delimiter'       => ! empty( $_POST['delimiter'] ) ? masvideos_clean( wp_unslash( $_POST['delimiter'] ) ) : ',', // PHPCS: input var ok.
			'start_pos'       => isset( $_POST['position'] ) ? absint( $_POST['position'] ) : 0, // PHPCS: input var ok.
			'mapping'         => isset( $_POST['mapping'] ) ? (array) masvideos_clean( wp_unslash( $_POST['mapping'] ) ) : array(), // PHPCS: input var ok.
			'update_existing' => isset( $_POST['update_existing'] ) ? (bool) $_POST['update_existing'] : false, // PHPCS: input var ok.
			'lines'           => apply_filters( 'masvideos_video_import_batch_size', 1 ),
			'parse'           => true,
		);

		// Log failures.
		if ( 0 !== $params['start_pos'] ) {
			$error_log = array_filter( (array) get_user_option( 'video_import_error_log' ) );
		} else {
			$error_log = array();
		}

		$importer         = MasVideos_Video_CSV_Importer_Controller::get_importer( $file, $params );
		$results          = $importer->import();
		$percent_complete = $importer->get_percent_complete();
		$error_log        = array_merge( $error_log, $results['failed'], $results['skipped'] );

		update_user_option( get_current_user_id(), 'video_import_error_log', $error_log );

		if ( 100 === $percent_complete ) {
			// @codingStandardsIgnoreStart.
			$wpdb->delete( $wpdb->postmeta, array( 'meta_key' => '_original_id' ) );
			$wpdb->delete( $wpdb->posts, array(
				'post_type'   => 'video',
				'post_status' => 'importing',
			) );
			// @codingStandardsIgnoreEnd.

			// Clean up orphaned data.
			$wpdb->query( "
				DELETE {$wpdb->postmeta}.* FROM {$wpdb->postmeta}
				LEFT JOIN {$wpdb->posts} wp ON wp.ID = {$wpdb->postmeta}.post_id
				WHERE wp.ID IS NULL
			" );
			// @codingStandardsIgnoreStart.
			$wpdb->query( "
				DELETE tr.* FROM {$wpdb->term_relationships} tr
				LEFT JOIN {$wpdb->posts} wp ON wp.ID = tr.object_id
				LEFT JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
				WHERE wp.ID IS NULL
				AND tt.taxonomy IN ( '" . implode( "','", array_map( 'esc_sql', get_object_taxonomies( 'video' ) ) ) . "' )
			" );
			// @codingStandardsIgnoreEnd.

			// Send success.
			wp_send_json_success(
				array(
					'position'   => 'done',
					'percentage' => 100,
					'url'        => add_query_arg( array( 'nonce' => wp_create_nonce( 'video-csv' ) ), admin_url( 'edit.php?post_type=video&page=video_importer&step=done' ) ),
					'imported'   => count( $results['imported'] ),
					'failed'     => count( $results['failed'] ),
					'updated'    => count( $results['updated'] ),
					'skipped'    => count( $results['skipped'] ),
				)
			);
		} else {
			wp_send_json_success(
				array(
					'position'   => $importer->get_file_position(),
					'percentage' => $percent_complete,
					'imported'   => count( $results['imported'] ),
					'failed'     => count( $results['failed'] ),
					'updated'    => count( $results['updated'] ),
					'skipped'    => count( $results['skipped'] ),
				)
			);
		}
	}

	/**
	 * Ajax callback for importing one batch of movies from a CSV.
	 */
	public function do_ajax_movie_import() {
		global $wpdb;

		check_ajax_referer( 'masvideos-movie-import', 'security' );

		if ( ! $this->import_allowed() || ! isset( $_POST['file'] ) ) { // PHPCS: input var ok.
			wp_send_json_error( array( 'message' => __( 'Insufficient privileges to import movies.', 'masvideos' ) ) );
		}

		include_once MASVIDEOS_ABSPATH . 'includes/admin/importers/class-masvideos-movie-csv-importer-controller.php';
		include_once MASVIDEOS_ABSPATH . 'includes/import/class-masvideos-movie-csv-importer.php';

		$file   = masvideos_clean( wp_unslash( $_POST['file'] ) ); // PHPCS: input var ok.
		$params = array(
			'delimiter'       => ! empty( $_POST['delimiter'] ) ? masvideos_clean( wp_unslash( $_POST['delimiter'] ) ) : ',', // PHPCS: input var ok.
			'start_pos'       => isset( $_POST['position'] ) ? absint( $_POST['position'] ) : 0, // PHPCS: input var ok.
			'mapping'         => isset( $_POST['mapping'] ) ? (array) masvideos_clean( wp_unslash( $_POST['mapping'] ) ) : array(), // PHPCS: input var ok.
			'update_existing' => isset( $_POST['update_existing'] ) ? (bool) $_POST['update_existing'] : false, // PHPCS: input var ok.
			'lines'           => apply_filters( 'masvideos_movie_import_batch_size', 1 ),
			'parse'           => true,
		);

		// Log failures.
		if ( 0 !== $params['start_pos'] ) {
			$error_log = array_filter( (array) get_user_option( 'movie_import_error_log' ) );
		} else {
			$error_log = array();
		}

		$importer         = MasVideos_Movie_CSV_Importer_Controller::get_importer( $file, $params );
		$results          = $importer->import();
		$percent_complete = $importer->get_percent_complete();
		$error_log        = array_merge( $error_log, $results['failed'], $results['skipped'] );

		update_user_option( get_current_user_id(), 'movie_import_error_log', $error_log );

		if ( 100 === $percent_complete ) {
			// @codingStandardsIgnoreStart.
			$wpdb->delete( $wpdb->postmeta, array( 'meta_key' => '_original_id' ) );
			$wpdb->delete( $wpdb->posts, array(
				'post_type'   => 'movie',
				'post_status' => 'importing',
			) );
			// @codingStandardsIgnoreEnd.

			// Clean up orphaned data.
			$wpdb->query( "
				DELETE {$wpdb->postmeta}.* FROM {$wpdb->postmeta}
				LEFT JOIN {$wpdb->posts} wp ON wp.ID = {$wpdb->postmeta}.post_id
				WHERE wp.ID IS NULL
			" );
			// @codingStandardsIgnoreStart.
			$wpdb->query( "
				DELETE tr.* FROM {$wpdb->term_relationships} tr
				LEFT JOIN {$wpdb->posts} wp ON wp.ID = tr.object_id
				LEFT JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
				WHERE wp.ID IS NULL
				AND tt.taxonomy IN ( '" . implode( "','", array_map( 'esc_sql', get_object_taxonomies( 'movie' ) ) ) . "' )
			" );
			// @codingStandardsIgnoreEnd.

			// Send success.
			wp_send_json_success(
				array(
					'position'   => 'done',
					'percentage' => 100,
					'url'        => add_query_arg( array( 'nonce' => wp_create_nonce( 'movie-csv' ) ), admin_url( 'edit.php?post_type=movie&page=movie_importer&step=done' ) ),
					'imported'   => count( $results['imported'] ),
					'failed'     => count( $results['failed'] ),
					'updated'    => count( $results['updated'] ),
					'skipped'    => count( $results['skipped'] ),
				)
			);
		} else {
			wp_send_json_success(
				array(
					'position'   => $importer->get_file_position(),
					'percentage' => $percent_complete,
					'imported'   => count( $results['imported'] ),
					'failed'     => count( $results['failed'] ),
					'updated'    => count( $results['updated'] ),
					'skipped'    => count( $results['skipped'] ),
				)
			);
		}
	}
}

new MasVideos_Admin_Importers();