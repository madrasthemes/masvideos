<?php
/**
 * Video Playlists shortcode
 *
 * @package  MasVideos/Shortcodes
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Video Playlists shortcode class.
 */
class MasVideos_Shortcode_Video_Playlists {

    /**
     * Shortcode type.
     *
     * @since 1.0.0
     * @var   string
     */
    protected $type = 'video_playlists';

    /**
     * Attributes.
     *
     * @since 1.0.0
     * @var   array
     */
    protected $attributes = array();

    /**
     * Query args.
     *
     * @since 1.0.0
     * @var   array
     */
    protected $query_args = array();

    /**
     * Set custom visibility.
     *
     * @since 1.0.0
     * @var   bool
     */
    protected $custom_visibility = false;

    /**
     * Initialize shortcode.
     *
     * @since 1.0.0
     * @param array  $attributes Shortcode attributes.
     * @param string $type       Shortcode type.
     */
    public function __construct( $attributes = array(), $type = 'video_playlists' ) {
        $this->type       = $type;
        $this->attributes = $this->parse_attributes( $attributes );
        $this->query_args = $this->parse_query_args();
    }

    /**
     * Get shortcode attributes.
     *
     * @since  1.0.0
     * @return array
     */
    public function get_attributes() {
        return $this->attributes;
    }

    /**
     * Get query args.
     *
     * @since  1.0.0
     * @return array
     */
    public function get_query_args() {
        return $this->query_args;
    }

    /**
     * Get shortcode type.
     *
     * @since  1.0.0
     * @return array
     */
    public function get_type() {
        return $this->type;
    }

    /**
     * Get shortcode content.
     *
     * @since  1.0.0
     * @return string
     */
    public function get_content() {
        return $this->video_playlist_loop();
    }

    /**
     * Parse attributes.
     *
     * @since  1.0.0
     * @param  array $attributes Shortcode attributes.
     * @return array
     */
    protected function parse_attributes( $attributes ) {

        $attributes = shortcode_atts(
            array(
                'limit'          => '-1',      // Results limit.
                'columns'        => '',        // Number of columns.
                'rows'           => '',        // Number of rows. If defined, limit will be ignored.
                'orderby'        => 'title',   // menu_order, title, date, rand, price, popularity, rating, or id.
                'order'          => 'ASC',     // ASC or DESC.
                'ids'            => '',        // Comma separated IDs.
                'class'          => '',        // HTML class.
                'template'       => '',        // Template file to run.
                'page'           => 1,         // Page for pagination.
                'paginate'       => false,     // Should results be paginated.
                'cache'          => true,      // Should shortcode output be cached.
            ), $attributes, $this->type
        );

        if ( ! absint( $attributes['columns'] ) ) {
            $attributes['columns'] = 5;
        }

        return $attributes;
    }

    /**
     * Parse query args.
     *
     * @since  1.0.0
     * @return array
     */
    protected function parse_query_args() {
        $query_args = array(
            'post_type'           => 'video_playlist',
            'post_status'         => 'publish',
            'ignore_sticky_posts' => true,
            'no_found_rows'       => false === masvideos_string_to_bool( $this->attributes['paginate'] ),
            'orderby'             => empty( $_GET['orderby'] ) ? $this->attributes['orderby'] : masvideos_clean( wp_unslash( $_GET['orderby'] ) ),
        );

        $orderby_value         = explode( '-', $query_args['orderby'] );
        $orderby               = esc_attr( $orderby_value[0] );
        $order                 = ! empty( $orderby_value[1] ) ? $orderby_value[1] : strtoupper( $this->attributes['order'] );
        $query_args['orderby'] = $orderby;
        $query_args['order']   = $order;

        if ( masvideos_string_to_bool( $this->attributes['paginate'] ) ) {
            $this->attributes['page'] = absint( empty( $_GET['video_playlist-page'] ) ? 1 : $_GET['video_playlist-page'] ); // WPCS: input var ok, CSRF ok.
        }

        if ( ! empty( $this->attributes['rows'] ) ) {
            $this->attributes['limit'] = $this->attributes['columns'] * $this->attributes['rows'];
        }

        // @codingStandardsIgnoreStart
        $query_args['posts_per_page'] = intval( $this->attributes['limit'] );
        if ( 1 < $this->attributes['page'] ) {
            $query_args['paged']          = absint( $this->attributes['page'] );
        }
        // @codingStandardsIgnoreEnd

        // IDs.
        $this->set_ids_query_args( $query_args );

        // Set specific types query args.
        if ( method_exists( $this, "set_{$this->type}_query_args" ) ) {
            $this->{"set_{$this->type}_query_args"}( $query_args );
        }

        $query_args = apply_filters( 'masvideos_shortcode_video_playlists_query', $query_args, $this->attributes, $this->type );

        // Always query only IDs.
        $query_args['fields'] = 'ids';

        return $query_args;
    }

    /**
     * Set ids query args.
     *
     * @since 1.0.0
     * @param array $query_args Query args.
     */
    protected function set_ids_query_args( &$query_args ) {
        if ( ! empty( $this->attributes['ids'] ) ) {
            $ids = array_map( 'trim', explode( ',', $this->attributes['ids'] ) );

            if ( 1 === count( $ids ) ) {
                $query_args['p'] = $ids[0];
            } else {
                $query_args['post__in'] = $ids;
            }
        }
    }

    /**
     * Set video_playlist as visible when quering for hidden video_playlists.
     *
     * @since  1.0.0
     * @param  bool $visibility Video Playlist visibility.
     * @return bool
     */
    public function set_video_playlist_as_visible( $visibility ) {
        return $this->custom_visibility ? true : $visibility;
    }

    /**
     * Get wrapper classes.
     *
     * @since  1.0.0
     * @param  array $columns Number of columns.
     * @return array
     */
    protected function get_wrapper_classes( $columns ) {
        $classes = array( 'masvideos', 'masvideos-video-playlists' );

        $classes[] = $this->attributes['class'];

        return $classes;
    }

    /**
     * Generate and return the transient name for this shortcode based on the query args.
     *
     * @since 3.3.0
     * @return string
     */
    protected function get_transient_name() {
        $transient_name = 'masvideos_video_playlist_loop' . substr( md5( wp_json_encode( $this->query_args ) . $this->type ), 28 );

        if ( 'rand' === $this->query_args['orderby'] ) {
            // When using rand, we'll cache a number of random queries and pull those to avoid querying rand on each page load.
            $rand_index      = rand( 0, max( 1, absint( apply_filters( 'masvideos_video_playlist_query_max_rand_cache_count', 5 ) ) ) );
            $transient_name .= $rand_index;
        }

        $transient_name .= MasVideos_Cache_Helper::get_transient_version( 'video_playlist_query' );

        return $transient_name;
    }

    /**
     * Run the query and return an array of data, including queried ids and pagination information.
     *
     * @since  3.3.0
     * @return object Object with the following props; ids, per_page, found_posts, max_num_pages, current_page
     */
    protected function get_query_results() {
        $transient_name = $this->get_transient_name();
        $cache          = masvideos_string_to_bool( $this->attributes['cache'] ) === true;
        $results        = $cache ? get_transient( $transient_name ) : false;

        if ( false === $results ) {
            if ( 'top_rated_video_playlists' === $this->type ) {
                $query = new WP_Query( $this->query_args );
            } else {
                $query = new WP_Query( $this->query_args );
            }

            $paginated = ! $query->get( 'no_found_rows' );

            $results = (object) array(
                'ids'          => wp_parse_id_list( $query->posts ),
                'total'        => $paginated ? (int) $query->found_posts : count( $query->posts ),
                'total_pages'  => $paginated ? (int) $query->max_num_pages : 1,
                'per_page'     => (int) $query->get( 'posts_per_page' ),
                'current_page' => $paginated ? (int) max( 1, $query->get( 'paged', 1 ) ) : 1,
            );

            if ( $cache ) {
                set_transient( $transient_name, $results, DAY_IN_SECONDS * 30 );
            }
        }
        return $results;
    }

    /**
     * Loop over found video_playlists.
     *
     * @since  1.0.0
     * @return string
     */
    protected function video_playlist_loop() {
        $columns  = absint( $this->attributes['columns'] );
        $classes  = $this->get_wrapper_classes( $columns );
        $video_playlists = $this->get_query_results();

        ob_start();

        if ( $video_playlists && $video_playlists->ids ) {
            // Prime caches to reduce future queries.
            if ( is_callable( '_prime_post_caches' ) ) {
                _prime_post_caches( $video_playlists->ids );
            }

            // Setup the loop.
            masvideos_setup_video_playlists_loop(
                array(
                    'columns'      => $columns,
                    'name'         => $this->type,
                    'is_shortcode' => true,
                    'is_search'    => false,
                    'is_paginated' => masvideos_string_to_bool( $this->attributes['paginate'] ),
                    'total'        => $video_playlists->total,
                    'total_pages'  => $video_playlists->total_pages,
                    'per_page'     => $video_playlists->per_page,
                    'current_page' => $video_playlists->current_page,
                )
            );

            $original_post = $GLOBALS['post'];

            do_action( "masvideos_shortcode_before_{$this->type}_loop", $this->attributes );

            // Fire standard video playlist loop hooks when paginating results so we can show result counts and so on.
            if ( masvideos_string_to_bool( $this->attributes['paginate'] ) ) {
                do_action( 'masvideos_before_video_playlists_loop' );
            }

            masvideos_video_playlist_loop_start();

            if ( masvideos_get_video_playlists_loop_prop( 'total' ) ) {
                foreach ( $video_playlists->ids as $video_playlist_id ) {
                    $GLOBALS['post'] = get_post( $video_playlist_id ); // WPCS: override ok.
                    setup_postdata( $GLOBALS['post'] );

                    // Set custom video_playlist visibility when quering hidden video_playlists.
                    add_action( 'masvideos_video_playlist_is_visible', array( $this, 'set_video_playlist_as_visible' ) );

                    // Render video_playlist template.
                    if( ! empty( $this->attributes['template'] ) ) {
                        masvideos_get_template_part( $this->attributes['template'] );
                    } else {
                        masvideos_get_template_part( 'content', 'video-playlist' );
                    }

                    // Restore video_playlist visibility.
                    remove_action( 'masvideos_video_playlist_is_visible', array( $this, 'set_video_playlist_as_visible' ) );
                }
            }

            $GLOBALS['post'] = $original_post; // WPCS: override ok.
            masvideos_video_playlist_loop_end();

            // Fire standard video playlist loop hooks when paginating results so we can show result counts and so on.
            if ( masvideos_string_to_bool( $this->attributes['paginate'] ) ) {
                do_action( 'masvideos_after_video_playlists_loop' );
            }

            do_action( "masvideos_shortcode_after_{$this->type}_loop", $this->attributes );

            wp_reset_postdata();
            masvideos_reset_video_playlists_loop();
        } else {
            do_action( "masvideos_shortcode_{$this->type}_loop_no_results", $this->attributes );
        }

        return '<div class="' . esc_attr( implode( ' ', $classes ) ) . '">' . ob_get_clean() . '</div>';
    }
}
