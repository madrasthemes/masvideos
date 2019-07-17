<?php
/**
 * Movie Data
 *
 * Displays the movie data box, tabbed, with several panels covering price, stock etc.
 *
 * @category Admin
 * @package  MasVideos/Admin/Meta Boxes
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * MasVideos_Meta_Box_Movie_Data Class.
 */
class MasVideos_Meta_Box_Movie_Data {

    /**
     * Output the metabox.
     *
     * @param WP_Post $post
     */
    public static function output( $post ) {
        global $thepostid, $movie_object;

        $thepostid      = $post->ID;
        $movie_object   = $thepostid ? masvideos_get_movie( $thepostid ) : new MasVideos_Movie();

        wp_nonce_field( 'masvideos_save_data', 'masvideos_meta_nonce' );

        include 'views/html-movie-data-panel.php';
    }

    /**
     * Show tab content/settings.
     */
    private static function output_tabs() {
        global $post, $thepostid, $movie_object;

        include 'views/html-movie-data-general.php';
        include 'views/html-movie-data-linked-movies.php';
        include 'views/html-movie-data-linked-videos.php';
        include 'views/html-movie-data-cast-persons.php';
        include 'views/html-movie-data-crew-persons.php';
        include 'views/html-movie-data-attributes.php';
        include 'views/html-movie-data-sources.php';
    }

    /**
     * Return array of tabs to show.
     *
     * @return array
     */
    private static function get_movie_data_tabs() {
        $tabs = apply_filters(
            'masvideos_movie_data_tabs', array(
                'general'       => array(
                    'label'    => __( 'General', 'masvideos' ),
                    'target'   => 'general_movie_data',
                    'class'    => array(),
                    'priority' => 10,
                ),
                'linked_movie'  => array(
                    'label'    => __( 'Linked Movies', 'masvideos' ),
                    'target'   => 'linked_movie_data',
                    'class'    => array(),
                    'priority' => 20,
                ),
                'linked_video'  => array(
                    'label'    => __( 'Linked Videos', 'masvideos' ),
                    'target'   => 'linked_video_data',
                    'class'    => array(),
                    'priority' => 30,
                ),
                'cast'        => array(
                    'label'    => __( 'Cast', 'masvideos' ),
                    'target'   => 'movie_cast_persons',
                    'class'    => array(),
                    'priority' => 40,
                ),
                'crew'        => array(
                    'label'    => __( 'Crew', 'masvideos' ),
                    'target'   => 'movie_crew_persons',
                    'class'    => array(),
                    'priority' => 50,
                ),
                'attribute'     => array(
                    'label'    => __( 'Attributes', 'masvideos' ),
                    'target'   => 'movie_attributes',
                    'class'    => array(),
                    'priority' => 60,
                ),
                'source'        => array(
                    'label'    => __( 'Sources', 'masvideos' ),
                    'target'   => 'movie_sources',
                    'class'    => array(),
                    'priority' => 70,
                ),
            )
        );

        // Sort tabs based on priority.
        uasort( $tabs, array( __CLASS__, 'movie_data_tabs_sort' ) );

        return $tabs;
    }

    /**
     * Callback to sort movie data tabs on priority.
     *
     * @since 1.0.0
     * @param int $a First item.
     * @param int $b Second item.
     *
     * @return bool
     */
    private static function movie_data_tabs_sort( $a, $b ) {
        if ( ! isset( $a['priority'], $b['priority'] ) ) {
            return -1;
        }

        if ( $a['priority'] == $b['priority'] ) {
            return 0;
        }

        return $a['priority'] < $b['priority'] ? -1 : 1;
    }

    /**
     * Prepare cast for save.
     *
     * @param array $data
     *
     * @return array
     */
    public static function prepare_cast( $data = false ) {
        $cast = array();

        if ( ! $data ) {
            $data = $_POST;
        }

        if ( isset( $data['cast_person_ids'], $data['cast_person_characters'] ) ) {
            $person_ids         = $data['cast_person_ids'];
            $person_characters  = isset( $data['cast_person_characters'] ) ? $data['cast_person_characters'] : array();
            $person_position    = $data['cast_person_position'];
            $person_ids_max_key = max( array_keys( $person_ids ) );

            for ( $i = 0; $i <= $person_ids_max_key; $i++ ) {
                if ( empty( $person_ids[ $i ] ) ) {
                    continue;
                }

                $person = array(
                    'id'            => $person_ids[ $i ],
                    'character'     => isset( $person_characters[ $i ] ) ? $person_characters[ $i ] : '',
                    'position'      => isset( $person_position[ $i ] ) ? absint( $person_position[ $i ] ) : 0
                );

                $cast[] = $person;
            }
        }
        return $cast;
    }

    /**
     * Prepare crew for save.
     *
     * @param array $data
     *
     * @return array
     */
    public static function prepare_crew( $data = false ) {
        $crew = array();

        if ( ! $data ) {
            $data = $_POST;
        }

        if ( isset( $data['crew_person_ids'], $data['crew_person_categories'] ) ) {
            $person_ids         = $data['crew_person_ids'];
            $person_categories  = isset( $data['crew_person_categories'] ) ? $data['crew_person_categories'] : array();
            $person_jobs        = isset( $data['crew_person_jobs'] ) ? $data['crew_person_jobs'] : array();
            $person_position    = $data['crew_person_position'];
            $person_ids_max_key = max( array_keys( $person_ids ) );

            for ( $i = 0; $i <= $person_ids_max_key; $i++ ) {
                if ( empty( $person_ids[ $i ] ) ) {
                    continue;
                }

                $person = array(
                    'id'            => $person_ids[ $i ],
                    'category'      => isset( $person_categories[ $i ] ) ? $person_categories[ $i ] : '',
                    'job'           => isset( $person_jobs[ $i ] ) ? $person_jobs[ $i ] : '',
                    'position'      => isset( $person_position[ $i ] ) ? absint( $person_position[ $i ] ) : 0
                );

                $crew[] = $person;
            }
        }
        return $crew;
    }

    /**
     * Prepare sources for save.
     *
     * @param array $data
     *
     * @return array
     */
    public static function prepare_sources( $data = false ) {
        $sources = array();

        if ( ! $data ) {
            $data = $_POST;
        }

        if ( isset( $data['source_names'], $data['source_embed_content'] ) ) {
            $source_names         = $data['source_names'];
            $source_choice        = isset( $data['source_choice'] ) ? $data['source_choice'] : array();
            $source_embed_content = $data['source_embed_content'];
            $source_link          = isset( $data['source_link'] ) ? $data['source_link'] : array();
            $source_is_affiliate  = isset( $data['source_is_affiliate'] ) ? $data['source_is_affiliate'] : array();
            $source_quality       = isset( $data['source_quality'] ) ? $data['source_quality'] : array();
            $source_language      = isset( $data['source_language'] ) ? $data['source_language'] : array();
            $source_player        = isset( $data['source_player'] ) ? $data['source_player'] : array();
            $source_date_added    = isset( $data['source_date_added'] ) ? $data['source_date_added'] : array();
            $source_position      = isset( $data['source_position'] ) ? $data['source_position'] : array();
            $source_names_max_key = max( array_keys( $source_names ) );

            for ( $i = 0; $i <= $source_names_max_key; $i++ ) {
                if ( empty( $source_names[ $i ] ) ) {
                    continue;
                }

                $source = array(
                    'name'          => isset( $source_names[ $i ] ) ? masvideos_clean( $source_names[ $i ] ) : '',
                    'choice'        => isset( $source_choice[ $i ] ) ? masvideos_clean( $source_choice[ $i ] ) : '',
                    'embed_content' => isset( $source_embed_content[ $i ] ) ? masvideos_sanitize_textarea_iframe( stripslashes( $source_embed_content[ $i ] ) ) : '',
                    'link'          => isset( $source_link[ $i ] ) ? masvideos_sanitize_textarea_iframe( stripslashes( $source_link[ $i ] ) ) : '',
                    'is_affiliate'  => isset( $source_is_affiliate[ $i ] ),
                    'quality'       => isset( $source_quality[ $i ] ) ? masvideos_clean( $source_quality[ $i ] ) : '',
                    'language'      => isset( $source_language[ $i ] ) ? masvideos_clean( $source_language[ $i ] ) : '',
                    'player'        => isset( $source_player[ $i ] ) ? masvideos_clean( $source_player[ $i ] ) : '',
                    'date_added'    => isset( $source_date_added[ $i ] ) ? masvideos_clean( $source_date_added[ $i ] ) : '',
                    'position'      => isset( $source_position[ $i ] ) ? absint( $source_position[ $i ] ) : 0
                );
                $sources[] = $source;
            }
        }
        return $sources;
    }

    /**
     * Prepare attributes for save.
     *
     * @param array $data
     *
     * @return array
     */
    public static function prepare_attributes( $data = false ) {
        $attributes = array();

        if ( ! $data ) {
            $data = $_POST;
        }

        $post_type = 'movie';

        if ( isset( $data['attribute_names'], $data['attribute_values'] ) ) {
            $attribute_names         = $data['attribute_names'];
            $attribute_values        = $data['attribute_values'];
            $attribute_visibility    = isset( $data['attribute_visibility'] ) ? $data['attribute_visibility'] : array();
            $attribute_position      = $data['attribute_position'];
            $attribute_names_max_key = max( array_keys( $attribute_names ) );

            for ( $i = 0; $i <= $attribute_names_max_key; $i++ ) {
                if ( empty( $attribute_names[ $i ] ) || ! isset( $attribute_values[ $i ] ) ) {
                    continue;
                }
                $attribute_id   = 0;
                $attribute_name = masvideos_clean( $attribute_names[ $i ] );

                if ( $post_type . '_' === substr( $attribute_name, 0, 6 ) ) {
                    $attribute_id = masvideos_attribute_taxonomy_id_by_name( $post_type, $attribute_name );
                }

                $options = isset( $attribute_values[ $i ] ) ? $attribute_values[ $i ] : '';

                if ( is_array( $options ) ) {
                    // Term ids sent as array.
                    $options = wp_parse_id_list( $options );
                } else {
                    // Terms or text sent in textarea.
                    $options = 0 < $attribute_id ? masvideos_sanitize_textarea( masvideos_sanitize_term_text_based( $options ) ) : masvideos_sanitize_textarea( $options );
                    $options = masvideos_get_text_attributes( $options );
                }

                if ( empty( $options ) ) {
                    continue;
                }

                $attribute = new MasVideos_Movie_Attribute();
                $attribute->set_id( $attribute_id );
                $attribute->set_name( $attribute_name );
                $attribute->set_options( $options );
                $attribute->set_position( $attribute_position[ $i ] );
                $attribute->set_visible( isset( $attribute_visibility[ $i ] ) );
                $attributes[] = $attribute;
            }
        }
        return $attributes;
    }

    /**
     * Save meta box data.
     *
     * @param int  $post_id
     * @param $post
     */
    public static function save( $post_id, $post ) {
        // Process movie type first so we have the correct class to run setters.
        $classname      = MasVideos_Movie_Factory::get_movie_classname( $post_id );
        $movie          = new $classname( $post_id );
        $attributes     = self::prepare_attributes();

        $errors = $movie->set_props(
            array(
                'featured'                  => isset( $_POST['_featured'] ),
                'catalog_visibility'        => masvideos_clean( wp_unslash( $_POST['_catalog_visibility'] ) ),
                'movie_choice'              => isset( $_POST['_movie_choice'] ) ? masvideos_clean( $_POST['_movie_choice'] ) : null,
                'movie_attachment_id'       => isset( $_POST['_movie_attachment_id'] ) ? masvideos_clean( $_POST['_movie_attachment_id'] ) : null,
                'movie_embed_content'       => isset( $_POST['_movie_embed_content'] ) ? masvideos_sanitize_textarea_iframe( stripslashes( $_POST['_movie_embed_content'] ) ) : null,
                'movie_url_link'            => isset( $_POST['_movie_url_link'] ) ? masvideos_clean( $_POST['_movie_url_link'] ) : null,
                'movie_is_affiliate_link'   => isset( $_POST['_movie_is_affiliate_link'] ),
                'attributes'                => $attributes,
                'movie_release_date'        => isset( $_POST['_movie_release_date'] ) ? masvideos_clean( $_POST['_movie_release_date'] ) : null,
                'movie_run_time'            => isset( $_POST['_movie_run_time'] ) ? masvideos_clean( $_POST['_movie_run_time'] ) : null,
                'movie_censor_rating'       => isset( $_POST['_movie_censor_rating'] ) ? masvideos_clean( $_POST['_movie_censor_rating'] ) : null,
                'recommended_movie_ids'     => isset( $_POST['recommended_movie_ids'] ) ? array_map( 'intval', (array) wp_unslash( $_POST['recommended_movie_ids'] ) ) : array(),
                'related_video_ids'         => isset( $_POST['related_video_ids'] ) ? array_map( 'intval', (array) wp_unslash( $_POST['related_video_ids'] ) ) : array(),
                'imdb_id'                   => isset( $_POST['_imdb_id'] ) ? masvideos_clean( wp_unslash( $_POST['_imdb_id'] ) ) : null,
                'tmdb_id'                   => isset( $_POST['_tmdb_id'] ) ? masvideos_clean( wp_unslash( $_POST['_tmdb_id'] ) ) : null,
            )
        );

        if ( is_wp_error( $errors ) ) {
            MasVideos_Admin_Meta_Boxes::add_error( $errors->get_error_message() );
        }

        /**
         * @since 1.0.0 to set props before save.
         */
        do_action( 'masvideos_admin_process_movie_object', $movie );

        $movie->save();
    }
}
