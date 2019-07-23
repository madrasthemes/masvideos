<?php
/**
 * TV Show Data
 *
 * Displays the tv show data box, tabbed, with several panels covering price, stock etc.
 *
 * @category Admin
 * @package  MasVideos/Admin/Meta Boxes
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * MasVideos_Meta_Box_TV_Show_Data Class.
 */
class MasVideos_Meta_Box_TV_Show_Data {

    /**
     * Output the metabox.
     *
     * @param WP_Post $post
     */
    public static function output( $post ) {
        global $thepostid, $tv_show_object;

        $thepostid      = $post->ID;
        $tv_show_object   = $thepostid ? masvideos_get_tv_show( $thepostid ) : new MasVideos_TV_Show();

        wp_nonce_field( 'masvideos_save_data', 'masvideos_meta_nonce' );

        include 'views/html-tv-show-data-panel.php';
    }

    /**
     * Show tab content/settings.
     */
    private static function output_tabs() {
        global $post, $thepostid, $tv_show_object;

        include 'views/html-tv-show-data-general.php';
        include 'views/html-tv-show-data-cast-persons.php';
        include 'views/html-tv-show-data-crew-persons.php';
        include 'views/html-tv-show-data-seasons.php';
        include 'views/html-tv-show-data-attributes.php';
    }

    /**
     * Return array of tabs to show.
     *
     * @return array
     */
    private static function get_tv_show_data_tabs() {
        $tabs = apply_filters(
            'masvideos_tv_show_data_tabs', array(
                'general'        => array(
                    'label'    => __( 'General', 'masvideos' ),
                    'target'   => 'general_tv_show_data',
                    'class'    => array(),
                    'priority' => 10,
                ),
                'cast'        => array(
                    'label'    => __( 'Cast', 'masvideos' ),
                    'target'   => 'tv_show_cast_persons',
                    'class'    => array(),
                    'priority' => 20,
                ),
                'crew'        => array(
                    'label'    => __( 'Crew', 'masvideos' ),
                    'target'   => 'tv_show_crew_persons',
                    'class'    => array(),
                    'priority' => 30,
                ),
                'seasons'      => array(
                    'label'    => __( 'Seasons & Episodes', 'masvideos' ),
                    'target'   => 'tv_show_seasons',
                    'class'    => array(),
                    'priority' => 40,
                ),
                'attribute'      => array(
                    'label'    => __( 'Attributes', 'masvideos' ),
                    'target'   => 'tv_show_attributes',
                    'class'    => array(),
                    'priority' => 50,
                ),
            )
        );

        // Sort tabs based on priority.
        uasort( $tabs, array( __CLASS__, 'tv_show_data_tabs_sort' ) );

        return $tabs;
    }

    /**
     * Callback to sort tv show data tabs on priority.
     *
     * @since 1.0.0
     * @param int $a First item.
     * @param int $b Second item.
     *
     * @return bool
     */
    private static function tv_show_data_tabs_sort( $a, $b ) {
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
     * Prepare seasons for save.
     *
     * @param array $data
     *
     * @return array
     */
    public static function prepare_seasons( $data = false ) {
        $seasons = array();

        if ( ! $data ) {
            $data = $_POST;
        }

        if ( isset( $data['season_names'], $data['season_episodes'] ) ) {
            $season_names         = $data['season_names'];
            $season_image_id      = $data['season_image_id'];
            $season_episodes      = $data['season_episodes'];
            $season_year          = $data['season_year'];
            $season_description   = $data['season_description'];
            $season_position      = $data['season_position'];
            $season_names_max_key = max( array_keys( $season_names ) );

            for ( $i = 0; $i <= $season_names_max_key; $i++ ) {
                if ( empty( $season_names[ $i ] ) ) {
                    continue;
                }

                $season = array(
                    'name'          => isset( $season_names[ $i ] ) ? masvideos_clean( $season_names[ $i ] ) : '',
                    'image_id'      => isset( $season_image_id[ $i ] ) ? absint( $season_image_id[ $i ] ) : 0,
                    'episodes'      => isset( $season_episodes[ $i ] ) ? $season_episodes[ $i ] : array(),
                    'year'          => isset( $season_year[ $i ] ) ? masvideos_clean( $season_year[ $i ] ) : '',
                    'description'   => isset( $season_description[ $i ] ) ? masvideos_sanitize_textarea( $season_description[ $i ] ) : '',
                    'position'      => isset( $season_position[ $i ] ) ? absint( $season_position[ $i ] ) : 0
                );
                $seasons[] = $season;
            }
        }
        return $seasons;
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

        $post_type = 'tv_show';

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

                if ( $post_type . '_' === substr( $attribute_name, 0, 8 ) ) {
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

                $attribute = new MasVideos_TV_Show_Attribute();
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
        // Process tv show type first so we have the correct class to run setters.
        $classname    = MasVideos_TV_Show_Factory::get_tv_show_classname( $post_id );
        $tv_show      = new $classname( $post_id );
        $attributes   = self::prepare_attributes();

        $errors = $tv_show->set_props(
            array(
                'featured'                  => isset( $_POST['_featured'] ),
                'catalog_visibility'        => masvideos_clean( wp_unslash( $_POST['_catalog_visibility'] ) ),
                'attributes'                => $attributes,
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
        do_action( 'masvideos_admin_process_tv_show_object', $tv_show );

        $tv_show->save();
    }
}
