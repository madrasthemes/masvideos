<?php
/**
 * Generic mappings
 *
 * @package MasVideos\Admin\Importers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add generic mappings.
 *
 * @since 3.1.0
 * @param array $mappings Importer columns mappings.
 * @return array
 */
function masvideos_importer_generic_mappings( $mappings ) {
	$generic_mappings = array(
		__( 'Title', 'masvideos' )         => 'name',
        __( 'TV Show Title', 'masvideos' ) => 'name',
		__( 'Video Title', 'masvideos' )   => 'name',
		__( 'Movie Title', 'masvideos' )   => 'name',
		__( 'Menu order', 'masvideos' )    => 'menu_order',
	);

	return array_merge( $mappings, $generic_mappings );
}
add_filter( 'masvideos_csv_tv_show_import_mapping_default_columns', 'masvideos_importer_generic_mappings' );
add_filter( 'masvideos_csv_video_import_mapping_default_columns', 'masvideos_importer_generic_mappings' );
add_filter( 'masvideos_csv_movie_import_mapping_default_columns', 'masvideos_importer_generic_mappings' );
