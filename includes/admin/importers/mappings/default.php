<?php
/**
 * Default mappings
 *
 * @package MasVideos\Admin\Importers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Importer current locale.
 *
 * @since 3.1.0
 * @return string
 */
function masvideos_importer_current_locale() {
	$locale = get_locale();
	if ( function_exists( 'get_user_locale' ) ) {
		$locale = get_user_locale();
	}

	return $locale;
}

/**
 * Add English mapping placeholders when not using English as current language.
 *
 * @since 3.1.0
 * @param array $mappings Importer columns mappings.
 * @return array
 */
function masvideos_importer_default_english_mappings( $mappings ) {
	if ( 'en_US' === masvideos_importer_current_locale() ) {
		return $mappings;
	}

	$new_mappings   = array(
		'ID'                                      => 'id',
		'Name'                                    => 'name',
		'Published'                               => 'published',
		'Is featured?'                            => 'featured',
		'Visibility in catalog'                   => 'catalog_visibility',
		'Short description'                       => 'short_description',
		'Description'                             => 'description',
		'Allow customer reviews?'                 => 'reviews_allowed',
		'Categories'                              => 'category_ids',
		'Genres'                                  => 'genre_ids',
		'Tags'                                    => 'tag_ids',
		'Images'                                  => 'images',
		'Parent'                                  => 'parent_id',
		'External URL'                            => 'movie_url',
		'Button text'                             => 'button_text',
		'Position'                                => 'menu_order',
		'Recommended Movie'                       => 'recommended_movie_ids',
		'Related Video'                           => 'related_video_ids',
		'IMDB ID'								  => 'imdb_id',
		'TMDB ID' 								  => 'tmdb_id',
	);

	return array_merge( $mappings, $new_mappings );
}
add_filter( 'masvideos_csv_movie_import_mapping_default_columns', 'masvideos_importer_default_english_mappings', 100 );

/**
 * Add English special mapping placeholders when not using English as current language.
 *
 * @since 3.1.0
 * @param array $mappings Importer columns mappings.
 * @return array
 */
function masvideos_importer_default_special_english_mappings( $mappings ) {
	if ( 'en_US' === masvideos_importer_current_locale() ) {
		return $mappings;
	}

	$new_mappings = array(
		'Attribute %d name'     => 'attributes:name',
		'Attribute %d value(s)' => 'attributes:value',
		'Attribute %d visible'  => 'attributes:visible',
		'Attribute %d global'   => 'attributes:taxonomy',
		'Attribute %d default'  => 'attributes:default',
		'Meta: %s'              => 'meta:',
	);

	return array_merge( $mappings, $new_mappings );
}
add_filter( 'masvideos_csv_movie_import_mapping_special_columns', 'masvideos_importer_default_special_english_mappings', 100 );
