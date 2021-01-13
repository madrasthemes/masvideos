<?php
/**
 * Video Filter widget
 *
 * @package MasVideos/Widgets
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists('MasVideos_Widget') ) :

/**
 * Widget layered nav class.
 */
class MasVideos_Videos_Tags_Filter_Widget extends MasVideos_Widget {

    /**
     * Constructor.
     */
    public function __construct() {
        $widget_ops = array(
            $this->widget_cssclass    = 'masvideos widget_layered_nav masvideos-videos-tags-filter-widget',
            $this->widget_description = esc_html__( 'Display a list of TV Show tags to filter TV shows in your site.', 'masvideos' ),
            $this->widget_id          = 'masvideos_videos_tags_filter_widget',
            $this->widget_name        = esc_html__( 'MAS Videos Filter Videos by Tags', 'masvideos' ),
        );
        
        parent::__construct( 'masvideos_videos_tag_filter_widget', esc_html__('MasVideos Videos Tags Filter Widget', 'masvideos'), $widget_ops );
    }

    /**
     * Updates a particular instance of a widget.
     *
     * @see WP_Widget->update
     *
     * @param array $new_instance New Instance.
     * @param array $old_instance Old Instance.
     *
     * @return array
     */
    public function update( $new_instance, $old_instance ) {
        $this->init_settings();
        return parent::update( $new_instance, $old_instance );
    }

    /**
     * Outputs the settings update form.
     *
     * @see WP_Widget->form
     *
     * @param array $instance Instance.
     */
    public function form( $instance ) {
        $this->init_settings();
        parent::form( $instance );
    }

    /**
     * Init settings after post types are registered.
     */
    public function init_settings() {
        $attribute_array      = array();
        $attribute_taxonomies = masvideos_get_attribute_taxonomies( $this->get_post_type() );

        if ( ! empty( $attribute_taxonomies ) ) {
            foreach ( $attribute_taxonomies as $tax ) {
                if ( taxonomy_exists( masvideos_attribute_taxonomy_name( $this->get_post_type(), $tax->attribute_name ) ) ) {
                    $attribute_array[ $tax->attribute_name ] = $tax->attribute_name;
                }
            }
        }

        $this->settings = array(
            'title'        => array(
                'type'  => 'text',
                'std'   => __( 'Filter by', 'masvideos' ),
                'label' => __( 'Title', 'masvideos' ),
            ),
            'limit'         => array(
                'type'  => 'number',
                'std'   => 4,
                'min'   => 0,
                'max'   => 20,
                'step'  => 1,
                'label' => __( 'Limit', 'masvideos' ),
            ),
            'slugs'        => array(
                'type'  => 'text',
                'std'   => '',
                'label' => __( 'Tag slug&#8217;s ( separated by comma&#8217;s )', 'masvideos' ),
            ),
            'query_type'   => array(
                'type'    => 'select',
                'std'     => 'and',
                'label'   => __( 'Query type', 'masvideos' ),
                'options' => array(
                    'and' => __( 'AND', 'masvideos' ),
                    'or'  => __( 'OR', 'masvideos' ),
                ),
            ),
        );
    }

    /**
     * Output widget.
     *
     * @see WP_Widget
     *
     * @param array $args Arguments.
     * @param array $instance Instance.
     */
    public function widget( $args, $instance ) {
        if ( ! is_videos() && ! is_video_taxonomy() ) {
            return;
        }

        $_chosen_attributes = MasVideos_Videos_Query::get_layered_nav_chosen_attributes();
        $taxonomy           = 'video_tag';
        $query_type         = isset( $instance['query_type'] ) ? $instance['query_type'] : $this->settings['query_type']['std'];
        $limit              = isset( $instance['limit'] ) ? $instance['limit'] : $this->settings['limit']['std'];
        $slugs              = isset( $instance['slugs'] ) ? $instance['slugs'] : $this->settings['slugs']['std'];

        if ( ! taxonomy_exists( $taxonomy ) ) {
            return;
        }

        $get_terms_args = array( 'hide_empty' => '1', 'number' => $limit );

        $orderby = masvideos_attribute_orderby( $this->get_post_type(), $taxonomy );

        switch ( $orderby ) {
            case 'name':
                $get_terms_args['orderby']    = 'name';
                $get_terms_args['menu_order'] = false;
                break;
            case 'id':
                $get_terms_args['orderby']    = 'id';
                $get_terms_args['order']      = 'ASC';
                $get_terms_args['menu_order'] = false;
                break;
            case 'menu_order':
                $get_terms_args['menu_order'] = 'ASC';
                break;
        }

        if ( ! empty( $slugs ) ){
            $get_terms_args['slug'] = array_map( 'trim', explode( ",", $slugs ) );
            $get_terms_args['orderby'] = 'slug__in';
        }

        $terms = get_terms( $taxonomy, apply_filters( 'masvideos_videos_tags_filter_widget_terms_args', $get_terms_args ) );

        if ( 0 === count( $terms ) ) {
            return;
        }

        switch ( $orderby ) {
            case 'name_num':
                usort( $terms, '_masvideos_get_video_terms_name_num_usort_callback' );
                break;
            case 'parent':
                usort( $terms, '_masvideos_get_video_terms_parent_usort_callback' );
                break;
        }

        ob_start();

        $this->widget_start( $args, $instance );
        $found = $this->layered_nav_list( $terms, $taxonomy, $query_type );

        $this->widget_end( $args );

        // Force found when option is selected - do not force found on taxonomy attributes.
        if ( ! is_tax() && is_array( $_chosen_attributes ) && array_key_exists( $taxonomy, $_chosen_attributes ) ) {
            $found = true;
        }

        if ( ! $found ) {
            ob_end_clean();
        } else {
            echo ob_get_clean(); // @codingStandardsIgnoreLine
        }
    }

    /**
     * Return the currently viewed post_type name.
     *
     * @return string
     */
    protected function get_post_type() {
        return 'video';
    }

    /**
     * Return the currently viewed taxonomy name.
     *
     * @return string
     */
    protected function get_current_taxonomy() {
        return is_tax() ? get_queried_object()->taxonomy : '';
    }

    /**
     * Return the currently viewed term ID.
     *
     * @return int
     */
    protected function get_current_term_id() {
        return absint( is_tax() ? get_queried_object()->term_id : 0 );
    }

    /**
     * Return the currently viewed term slug.
     *
     * @return int
     */
    protected function get_current_term_slug() {
        return absint( is_tax() ? get_queried_object()->slug : 0 );
    }

    /**
     * Count TV shows within certain terms, taking the main WP query into consideration.
     *
     * This query allows counts to be generated based on the viewed TV shows, not all TV shows.
     *
     * @param  array  $term_ids Term IDs.
     * @param  string $taxonomy Taxonomy.
     * @param  string $query_type Query Type.
     * @return array
     */
    protected function get_filtered_term_video_counts( $term_ids, $taxonomy, $query_type ) {
        global $wpdb;

        $tax_query  = MasVideos_Videos_Query::get_main_tax_query();
        $meta_query = MasVideos_Videos_Query::get_main_meta_query();

        if ( 'or' === $query_type ) {
            foreach ( $tax_query as $key => $query ) {
                if ( is_array( $query ) && $taxonomy === $query['taxonomy'] ) {
                    unset( $tax_query[ $key ] );
                }
            }
        }

        $meta_query     = new WP_Meta_Query( $meta_query );
        $tax_query      = new WP_Tax_Query( $tax_query );
        $meta_query_sql = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
        $tax_query_sql  = $tax_query->get_sql( $wpdb->posts, 'ID' );

        // Generate query.
        $query           = array();
        $query['select'] = "SELECT COUNT( DISTINCT {$wpdb->posts}.ID ) as term_count, terms.term_id as term_count_id";
        $query['from']   = "FROM {$wpdb->posts}";
        $query['join']   = "
            INNER JOIN {$wpdb->term_relationships} AS term_relationships ON {$wpdb->posts}.ID = term_relationships.object_id
            INNER JOIN {$wpdb->term_taxonomy} AS term_taxonomy USING( term_taxonomy_id )
            INNER JOIN {$wpdb->terms} AS terms USING( term_id )
            " . $tax_query_sql['join'] . $meta_query_sql['join'];

        $query['where'] = "
            WHERE {$wpdb->posts}.post_type IN ( 'video' )
            AND {$wpdb->posts}.post_status = 'publish'"
            . $tax_query_sql['where'] . $meta_query_sql['where'] .
            'AND terms.term_id IN (' . implode( ',', array_map( 'absint', $term_ids ) ) . ')';

        $search = MasVideos_Videos_Query::get_main_search_query_sql();
        if ( $search ) {
            $query['where'] .= ' AND ' . $search;
        }

        $query['group_by'] = 'GROUP BY terms.term_id';
        $query             = apply_filters( 'masvideos_get_filtered_term_video_counts_query', $query );
        $query             = implode( ' ', $query );

        // We have a query - let's see if cached results of this query already exist.
        $query_hash    = md5( $query );

        // Maybe store a transient of the count values.
        $cache = apply_filters( 'masvideos_layered_nav_count_maybe_cache', true );
        if ( true === $cache ) {
            $cached_counts = (array) get_transient( 'masvideos_layered_nav_counts_' . sanitize_title( $taxonomy ) );
        } else {
            $cached_counts = array();
        }

        if ( ! isset( $cached_counts[ $query_hash ] ) ) {
            $results                      = $wpdb->get_results( $query, ARRAY_A ); // @codingStandardsIgnoreLine
            $counts                       = array_map( 'absint', wp_list_pluck( $results, 'term_count', 'term_count_id' ) );
            $cached_counts[ $query_hash ] = $counts;
            if ( true === $cache ) {
                set_transient( 'masvideos_layered_nav_counts_' . sanitize_title( $taxonomy ), $cached_counts, DAY_IN_SECONDS );
            }
        }

        return array_map( 'absint', (array) $cached_counts[ $query_hash ] );
    }

    /**
     * Show list based layered nav.
     *
     * @param  array  $terms Terms.
     * @param  string $taxonomy Taxonomy.
     * @param  string $query_type Query Type.
     * @return bool   Will nav display?
     */
    protected function layered_nav_list( $terms, $taxonomy, $query_type ) {
        // List display.
        echo '<ul class="masvideos-widget-videos-layered-nav-list">';

        $term_counts        = $this->get_filtered_term_video_counts( wp_list_pluck( $terms, 'term_id' ), $taxonomy, $query_type );
        $_chosen_attributes = MasVideos_Videos_Query::get_layered_nav_chosen_attributes();
        $found              = false;

        foreach ( $terms as $term ) {
            $current_values = isset( $_chosen_attributes[ $taxonomy ]['terms'] ) ? $_chosen_attributes[ $taxonomy ]['terms'] : array();
            $option_is_set  = in_array( $term->slug, $current_values, true );
            $count          = isset( $term_counts[ $term->term_id ] ) ? $term_counts[ $term->term_id ] : 0;

            // Skip the term for the current archive.
            if ( $this->get_current_term_id() === $term->term_id ) {
                continue;
            }

            // Only show options with count > 0.
            if ( 0 < $count ) {
                $found = true;
            } elseif ( 0 === $count && ! $option_is_set ) {
                continue;
            }

            $filter_name    = 'filter_' . str_replace( $this->get_post_type() . '_', '', $taxonomy );
            $current_filter = isset( $_GET[ $filter_name ] ) ? explode( ',', masvideos_clean( wp_unslash( $_GET[ $filter_name ] ) ) ) : array(); // WPCS: input var ok, CSRF ok.
            $current_filter = array_map( 'sanitize_title', $current_filter );

            if ( ! in_array( $term->slug, $current_filter, true ) ) {
                $current_filter[] = $term->slug;
            }

            $link = remove_query_arg( $filter_name, $this->get_current_page_url() );

            // Add current filters to URL.
            foreach ( $current_filter as $key => $value ) {
                // Exclude query arg for current term archive term.
                if ( $value === $this->get_current_term_slug() ) {
                    unset( $current_filter[ $key ] );
                }

                // Exclude self so filter can be unset on click.
                if ( $option_is_set && $value === $term->slug ) {
                    unset( $current_filter[ $key ] );
                }
            }

            if ( ! empty( $current_filter ) ) {
                asort( $current_filter );
                $link = add_query_arg( $filter_name, implode( ',', $current_filter ), $link );

                // Add Query type Arg to URL.
                if ( 'or' === $query_type && ! ( 1 === count( $current_filter ) && $option_is_set ) ) {
                    $link = add_query_arg( 'query_type_' . sanitize_title( str_replace( $this->get_post_type() . '_', '', $taxonomy ) ), 'or', $link );
                }
                $link = str_replace( '%2C', ',', $link );
            }

            $term_icon = ! empty( $icon_class = get_term_meta( $term->term_id, 'icon', true ) ) ? ( '<i class="' . esc_attr( $icon_class ) . '"></i>' ) : "";

            if ( $count > 0 || $option_is_set ) {
                $link      = esc_url( apply_filters( 'masvideos_layered_nav_link', $link, $term, $taxonomy ) );
                $term_html = $term_icon . '<a rel="nofollow" href="' . $link . '">' . esc_html( $term->name ) . '</a>';
            } else {
                $link      = false;
                $term_html = $term_icon . '<span>' . esc_html( $term->name ) . '</span>';
            }

            echo '<li class="masvideos-widget-videos-layered-nav-list__item masvideos-layered-nav-term ' . ( $option_is_set ? 'masvideos-widget-videos-layered-nav-list__item--chosen chosen' : '' ) . '">';
            echo wp_kses_post( apply_filters( 'masvideos_layered_nav_term_html', $term_html, $term, $link, $count ) );
            echo '</li>';
        }

        echo '</ul>';

        return $found;
    }
}

endif;