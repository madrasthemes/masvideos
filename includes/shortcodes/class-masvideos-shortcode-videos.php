<?php
/**
 * Videos shortcode
 *
 * @package  MasVideos/Shortcodes
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Videos shortcode class.
 */
class MasVideos_Shortcode_Videos {

    /**
     * Shortcode type.
     *
     * @since 1.0.0
     * @var   string
     */
    protected $type = 'videos';

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
    public function __construct( $attributes = array(), $type = 'videos' ) {
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
        return $this->video_loop();
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
                'category'       => '',        // Comma separated category slugs or ids.
                'cat_operator'   => 'IN',      // Operator to compare categories. Possible values are 'IN', 'NOT IN', 'AND'.
                'attribute'      => '',        // Single attribute slug.
                'terms'          => '',        // Comma separated term slugs or ids.
                'terms_operator' => 'IN',      // Operator to compare terms. Possible values are 'IN', 'NOT IN', 'AND'.
                'tag'            => '',        // Comma separated tag slugs or ids.
                'tag_operator'   => 'IN',      // Operator to compare tags. Possible values are 'IN', 'NOT IN', 'AND'.
                'visibility'     => 'visible', // Possible values are 'visible', 'catalog', 'search', 'hidden', 'featured'.
                'status'         => 'publish', // Possible values are 'publish', 'future'.
                'class'          => '',        // HTML class.
                'template'       => '',        // Template file to run.
                'page'           => 1,         // Page for pagination.
                'paginate'       => false,     // Should results be paginated.
                'cache'          => true,      // Should shortcode output be cached.
            ), $attributes, $this->type
        );

        if ( ! absint( $attributes['columns'] ) ) {
            $attributes['columns'] = masvideos_get_default_videos_per_row();
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
            'post_type'           => 'video',
            'ignore_sticky_posts' => true,
            'no_found_rows'       => false === masvideos_string_to_bool( $this->attributes['paginate'] ),
            'orderby'             => empty( $_GET['orderby'] ) ? $this->attributes['orderby'] : masvideos_clean( wp_unslash( $_GET['orderby'] ) ),
        );

        $orderby_value              = explode( '-', $query_args['orderby'] );
        $orderby                    = esc_attr( $orderby_value[0] );
        $order                      = ! empty( $orderby_value[1] ) ? $orderby_value[1] : strtoupper( $this->attributes['order'] );
        $query_args['orderby']      = $orderby;
        $query_args['order']        = $order;
        $query_args['post_status']  = isset( $this->attributes['status'] ) && in_array( $this->attributes['status'], array( 'publish', 'future' ) ) ? $this->attributes['status'] : 'publish';

        if ( masvideos_string_to_bool( $this->attributes['paginate'] ) ) {
            $this->attributes['page'] = absint( empty( $_GET['video-page'] ) ? 1 : $_GET['video-page'] ); // WPCS: input var ok, CSRF ok.
        }

        if ( ! empty( $this->attributes['rows'] ) ) {
            $this->attributes['limit'] = $this->attributes['columns'] * $this->attributes['rows'];
        }

        // @codingStandardsIgnoreStart
        $ordering_args                = MasVideos()->video_query->get_catalog_ordering_args( $query_args['orderby'], $query_args['order'] );
        $query_args['orderby']        = $ordering_args['orderby'];
        $query_args['order']          = $ordering_args['order'];
        if ( $ordering_args['meta_key'] ) {
            $query_args['meta_key']       = $ordering_args['meta_key'];
        }
        $query_args['posts_per_page'] = intval( $this->attributes['limit'] );
        if ( 1 < $this->attributes['page'] ) {
            $query_args['paged']          = absint( $this->attributes['page'] );
        }
        $query_args['meta_query']     = MasVideos()->video_query->get_meta_query();
        $query_args['tax_query']      = array();
        // @codingStandardsIgnoreEnd

        // Visibility.
        $this->set_visibility_query_args( $query_args );

        // IDs.
        $this->set_ids_query_args( $query_args );

        // Set specific types query args.
        if ( method_exists( $this, "set_{$this->type}_query_args" ) ) {
            $this->{"set_{$this->type}_query_args"}( $query_args );
        }

        // Attributes.
        $this->set_attributes_query_args( $query_args );

        // Categories.
        $this->set_categories_query_args( $query_args );

        // Tags.
        $this->set_tags_query_args( $query_args );

        $query_args = apply_filters( 'masvideos_shortcode_videos_query', $query_args, $this->attributes, $this->type );

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
     * Set attributes query args.
     *
     * @since 1.0.0
     * @param array $query_args Query args.
     */
    protected function set_attributes_query_args( &$query_args ) {
        if ( ! empty( $this->attributes['attribute'] ) || ! empty( $this->attributes['terms'] ) ) {
            $taxonomy = strstr( $this->attributes['attribute'], 'video_' ) ? sanitize_title( $this->attributes['attribute'] ) : 'video_' . sanitize_title( $this->attributes['attribute'] );
            $terms    = $this->attributes['terms'] ? array_map( 'sanitize_title', explode( ',', $this->attributes['terms'] ) ) : array();
            $field    = 'slug';

            if ( $terms && is_numeric( $terms[0] ) ) {
                $field = 'term_id';
                $terms = array_map( 'absint', $terms );
                // Check numeric slugs.
                foreach ( $terms as $term ) {
                    $the_term = get_term_by( 'slug', $term, $taxonomy );
                    if ( false !== $the_term ) {
                        $terms[] = $the_term->term_id;
                    }
                }
            }

            // If no terms were specified get all videos that are in the attribute taxonomy.
            if ( ! $terms ) {
                $terms = get_terms(
                    array(
                        'taxonomy' => $taxonomy,
                        'fields'   => 'ids',
                    )
                );
                $field = 'term_id';
            }

            // We always need to search based on the slug as well, this is to accommodate numeric slugs.
            $query_args['tax_query'][] = array(
                'taxonomy' => $taxonomy,
                'terms'    => $terms,
                'field'    => $field,
                'operator' => $this->attributes['terms_operator'],
            );
        }
    }

    /**
     * Set categories query args.
     *
     * @since 1.0.0
     * @param array $query_args Query args.
     */
    protected function set_categories_query_args( &$query_args ) {
        if ( ! empty( $this->attributes['category'] ) ) {
            $categories = array_map( 'sanitize_title', explode( ',', $this->attributes['category'] ) );
            $field      = 'slug';

            if ( is_numeric( $categories[0] ) ) {
                $field = 'term_id';
                $categories = array_map( 'absint', $categories );
                // Check numeric slugs.
                foreach ( $categories as $cat ) {
                    $the_cat = get_term_by( 'slug', $cat, 'video_cat' );
                    if ( false !== $the_cat ) {
                        $categories[] = $the_cat->term_id;
                    }
                }
            }

            $query_args['tax_query'][] = array(
                'taxonomy'         => 'video_cat',
                'terms'            => $categories,
                'field'            => $field,
                'operator'         => $this->attributes['cat_operator'],

                /*
                 * When cat_operator is AND, the children categories should be excluded,
                 * as only videos belonging to all the children categories would be selected.
                 */
                'include_children' => 'AND' === $this->attributes['cat_operator'] ? false : true,
            );
        }
    }

    /**
     * Set tags query args.
     *
     * @since 3.3.0
     * @param array $query_args Query args.
     */
    protected function set_tags_query_args( &$query_args ) {
        if ( ! empty( $this->attributes['tag'] ) ) {
            $tags       = array_map( 'sanitize_title', explode( ',', $this->attributes['tag'] ) );
            $field      = 'slug';

            if ( is_numeric( $tags[0] ) ) {
                $field = 'term_id';
                $tags = array_map( 'absint', $tags );
                // Check numeric slugs.
                foreach ( $tags as $tag ) {
                    $the_tag = get_term_by( 'slug', $tag, 'video_tag' );
                    if ( false !== $the_tag ) {
                        $tags[] = $the_tag->term_id;
                    }
                }
            }

            $query_args['tax_query'][] = array(
                'taxonomy'         => 'video_tag',
                'terms'            => $tags,
                'field'            => $field,
                'operator'         => $this->attributes['tag_operator'],
            );
        }
    }

    /**
     * Set visibility as hidden.
     *
     * @since 1.0.0
     * @param array $query_args Query args.
     */
    protected function set_visibility_hidden_query_args( &$query_args ) {
        $this->custom_visibility   = true;
        $query_args['tax_query'][] = array(
            'taxonomy'         => 'video_visibility',
            'terms'            => array( 'exclude-from-catalog', 'exclude-from-search' ),
            'field'            => 'name',
            'operator'         => 'AND',
            'include_children' => false,
        );
    }

    /**
     * Set visibility as catalog.
     *
     * @since 1.0.0
     * @param array $query_args Query args.
     */
    protected function set_visibility_catalog_query_args( &$query_args ) {
        $this->custom_visibility   = true;
        $query_args['tax_query'][] = array(
            'taxonomy'         => 'video_visibility',
            'terms'            => 'exclude-from-search',
            'field'            => 'name',
            'operator'         => 'IN',
            'include_children' => false,
        );
        $query_args['tax_query'][] = array(
            'taxonomy'         => 'video_visibility',
            'terms'            => 'exclude-from-catalog',
            'field'            => 'name',
            'operator'         => 'NOT IN',
            'include_children' => false,
        );
    }

    /**
     * Set visibility as search.
     *
     * @since 1.0.0
     * @param array $query_args Query args.
     */
    protected function set_visibility_search_query_args( &$query_args ) {
        $this->custom_visibility   = true;
        $query_args['tax_query'][] = array(
            'taxonomy'         => 'video_visibility',
            'terms'            => 'exclude-from-catalog',
            'field'            => 'name',
            'operator'         => 'IN',
            'include_children' => false,
        );
        $query_args['tax_query'][] = array(
            'taxonomy'         => 'video_visibility',
            'terms'            => 'exclude-from-search',
            'field'            => 'name',
            'operator'         => 'NOT IN',
            'include_children' => false,
        );
    }

    /**
     * Set visibility as featured.
     *
     * @since 1.0.0
     * @param array $query_args Query args.
     */
    protected function set_visibility_featured_query_args( &$query_args ) {
        // @codingStandardsIgnoreStart
        $query_args['tax_query'] = array_merge( $query_args['tax_query'], MasVideos()->video_query->get_tax_query() );
        // @codingStandardsIgnoreEnd

        $query_args['tax_query'][] = array(
            'taxonomy'         => 'video_visibility',
            'terms'            => 'featured',
            'field'            => 'name',
            'operator'         => 'IN',
            'include_children' => false,
        );
    }

    /**
     * Set visibility query args.
     *
     * @since 1.0.0
     * @param array $query_args Query args.
     */
    protected function set_visibility_query_args( &$query_args ) {
        if ( method_exists( $this, 'set_visibility_' . $this->attributes['visibility'] . '_query_args' ) ) {
            $this->{'set_visibility_' . $this->attributes['visibility'] . '_query_args'}( $query_args );
        } else {
            // @codingStandardsIgnoreStart
            $query_args['tax_query'] = array_merge( $query_args['tax_query'], MasVideos()->video_query->get_tax_query() );
            // @codingStandardsIgnoreEnd
        }
    }

    /**
     * Set video as visible when quering for hidden videos.
     *
     * @since  1.0.0
     * @param  bool $visibility Video visibility.
     * @return bool
     */
    public function set_video_as_visible( $visibility ) {
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
        $classes = array( 'masvideos', 'masvideos-videos' );

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
        $transient_name = 'masvideos_video_loop' . substr( md5( wp_json_encode( $this->query_args ) . $this->type ), 28 );

        if ( 'rand' === $this->query_args['orderby'] ) {
            // When using rand, we'll cache a number of random queries and pull those to avoid querying rand on each page load.
            $rand_index      = rand( 0, max( 1, absint( apply_filters( 'masvideos_video_query_max_rand_cache_count', 5 ) ) ) );
            $transient_name .= $rand_index;
        }

        $transient_name .= MasVideos_Cache_Helper::get_transient_version( 'video_query' );

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
            if ( 'top_rated_videos' === $this->type ) {
                add_filter( 'posts_clauses', array( __CLASS__, 'order_by_rating_post_clauses' ) );
                $query = new WP_Query( $this->query_args );
                remove_filter( 'posts_clauses', array( __CLASS__, 'order_by_rating_post_clauses' ) );
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
        // Remove ordering query arguments which may have been added by get_catalog_ordering_args.
        MasVideos()->video_query->remove_ordering_args();
        return $results;
    }

    /**
     * Loop over found videos.
     *
     * @since  1.0.0
     * @return string
     */
    protected function video_loop() {
        $columns  = absint( $this->attributes['columns'] );
        $classes  = $this->get_wrapper_classes( $columns );
        $videos = $this->get_query_results();

        ob_start();

        if ( $videos && $videos->ids ) {
            // Prime caches to reduce future queries.
            if ( is_callable( '_prime_post_caches' ) ) {
                _prime_post_caches( $videos->ids );
            }

            // Setup the loop.
            masvideos_setup_videos_loop(
                array(
                    'columns'      => $columns,
                    'name'         => $this->type,
                    'is_shortcode' => true,
                    'is_search'    => false,
                    'is_paginated' => masvideos_string_to_bool( $this->attributes['paginate'] ),
                    'total'        => $videos->total,
                    'total_pages'  => $videos->total_pages,
                    'per_page'     => $videos->per_page,
                    'current_page' => $videos->current_page,
                )
            );

            $original_post = $GLOBALS['post'];

            do_action( "masvideos_shortcode_before_{$this->type}_loop", $this->attributes );

            // Fire standard video loop hooks when paginating results so we can show result counts and so on.
            if ( masvideos_string_to_bool( $this->attributes['paginate'] ) ) {
                do_action( 'masvideos_before_videos_loop' );
            }

            masvideos_video_loop_start();

            if ( masvideos_get_videos_loop_prop( 'total' ) ) {
                foreach ( $videos->ids as $video_id ) {
                    $GLOBALS['post'] = get_post( $video_id ); // WPCS: override ok.
                    setup_postdata( $GLOBALS['post'] );

                    // Set custom video visibility when quering hidden videos.
                    add_action( 'masvideos_video_is_visible', array( $this, 'set_video_as_visible' ) );

                    // Render video template.
                    if( ! empty( $this->attributes['template'] ) ) {
                        masvideos_get_template_part( $this->attributes['template'] );
                    } else {
                        masvideos_get_template_part( 'content', 'video' );
                    }

                    // Restore video visibility.
                    remove_action( 'masvideos_video_is_visible', array( $this, 'set_video_as_visible' ) );
                }
            }

            $GLOBALS['post'] = $original_post; // WPCS: override ok.
            masvideos_video_loop_end();

            // Fire standard video loop hooks when paginating results so we can show result counts and so on.
            if ( masvideos_string_to_bool( $this->attributes['paginate'] ) ) {
                do_action( 'masvideos_after_videos_loop' );
            }

            do_action( "masvideos_shortcode_after_{$this->type}_loop", $this->attributes );

            wp_reset_postdata();
            masvideos_reset_videos_loop();
        } else {
            do_action( "masvideos_shortcode_{$this->type}_loop_no_results", $this->attributes );
        }

        return '<div class="' . esc_attr( implode( ' ', $classes ) ) . '">' . ob_get_clean() . '</div>';
    }

    /**
     * Order by rating.
     *
     * @since  1.0.0
     * @param  array $args Query args.
     * @return array
     */
    public static function order_by_rating_post_clauses( $args ) {
        global $wpdb;

        $args['where']  .= " AND $wpdb->commentmeta.meta_key = 'rating' ";
        $args['join']   .= "LEFT JOIN $wpdb->comments ON($wpdb->posts.ID = $wpdb->comments.comment_post_ID) LEFT JOIN $wpdb->commentmeta ON($wpdb->comments.comment_ID = $wpdb->commentmeta.comment_id)";
        $args['orderby'] = "$wpdb->commentmeta.meta_value DESC";
        $args['groupby'] = "$wpdb->posts.ID";

        return $args;
    }
}
