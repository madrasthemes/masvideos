<?php
/**
 * Rating Filter Widget and related functions.
 *
 * @package MasVideos/Widgets
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Widget rating filter class.
 */
class MasVideos_Widget_Movies_Rating_Filter extends MasVideos_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'masvideos movies_widget_rating_filter';
		$this->widget_description = __( 'Display a list of star ratings to filter movies.', 'masvideos' );
		$this->widget_id          = 'masvideos_movies_rating_filter';
		$this->widget_name        = __( 'MAS Videos Filter Movies by Rating', 'masvideos' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Average rating', 'masvideos' ),
				'label' => __( 'Title', 'masvideos' ),
			),
		);
		parent::__construct();
	}

	/**
	 * Count movies after other filters have occurred by adjusting the main query.
	 *
	 * @param  int $rating Rating.
	 * @return int
	 */
	protected function get_filtered_movie_count( $rating ) {
		global $wpdb;

		$tax_query  = MasVideos_Movies_Query::get_main_tax_query();
		$meta_query = MasVideos_Movies_Query::get_main_meta_query();

		// Unset current rating filter.
		foreach ( $tax_query as $key => $query ) {
			if ( ! empty( $query['rating_filter'] ) ) {
				unset( $tax_query[ $key ] );
				break;
			}
		}

		// Set new rating filter.
		$movie_visibility_terms = masvideos_get_movie_visibility_term_ids();
		$tax_query[]              = array(
			'taxonomy'      => 'movie_visibility',
			'field'         => 'term_taxonomy_id',
			'terms'         => $movie_visibility_terms[ 'rated-' . $rating ],
			'operator'      => 'IN',
			'rating_filter' => true,
		);

		$meta_query     = new WP_Meta_Query( $meta_query );
		$tax_query      = new WP_Tax_Query( $tax_query );
		$meta_query_sql = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
		$tax_query_sql  = $tax_query->get_sql( $wpdb->posts, 'ID' );

		$sql  = "SELECT COUNT( DISTINCT {$wpdb->posts}.ID ) FROM {$wpdb->posts} ";
		$sql .= $tax_query_sql['join'] . $meta_query_sql['join'];
		$sql .= " WHERE {$wpdb->posts}.post_type = 'movie' AND {$wpdb->posts}.post_status = 'publish' ";
		$sql .= $tax_query_sql['where'] . $meta_query_sql['where'];

		$search = MasVideos_Movies_Query::get_main_search_query_sql();
		if ( $search ) {
			$sql .= ' AND ' . $search;
		}

		return absint( $wpdb->get_var( $sql ) ); // WPCS: unprepared SQL ok.
	}

	/**
	 * Widget function.
	 *
	 * @see WP_Widget
	 * @param array $args     Arguments.
	 * @param array $instance Widget instance.
	 */
	public function widget( $args, $instance ) {
		if ( ! is_movies() && ! is_movie_taxonomy() ) {
			return;
		}

		if ( ! MasVideos()->movie_query->get_main_query()->post_count ) {
			return;
		}

		ob_start();

		$found         = false;
		$rating_filter = isset( $_GET['rating_filter'] ) ? array_filter( array_map( 'absint', explode( ',', wp_unslash( $_GET['rating_filter'] ) ) ) ) : array(); // WPCS: input var ok, CSRF ok, sanitization ok.

		$this->widget_start( $args, $instance );

		echo '<ul>';

		for ( $rating = 10; $rating >= 1; $rating-- ) {
			$count = $this->get_filtered_movie_count( $rating );
			if ( empty( $count ) ) {
				continue;
			}
			$found = true;
			$link  = $this->get_current_page_url();

			if ( in_array( $rating, $rating_filter, true ) ) {
				$link_ratings = implode( ',', array_diff( $rating_filter, array( $rating ) ) );
			} else {
				$link_ratings = implode( ',', array_merge( $rating_filter, array( $rating ) ) );
			}

			$class       = in_array( $rating, $rating_filter, true ) ? 'masvideos-layered-nav-rating chosen' : 'masvideos-layered-nav-rating';
			$link        = apply_filters( 'masvideos_movies_rating_filter_link', $link_ratings ? add_query_arg( 'rating_filter', $link_ratings ) : remove_query_arg( 'rating_filter' ) );
			$rating_html = masvideos_get_star_rating_html( $rating );
			$count_html  = esc_html( apply_filters( 'masvideos_movies_rating_filter_count', "({$count})", $count, $rating ) );

			printf( '<li class="%s"><a href="%s"><div class="star-rating">%s</div> %s</a></li>', esc_attr( $class ), esc_url( $link ), $rating_html, $count_html ); // WPCS: XSS ok.
		}

		echo '</ul>';

		$this->widget_end( $args );

		if ( ! $found ) {
			ob_end_clean();
		} else {
			echo ob_get_clean(); // WPCS: XSS ok.
		}
	}
}
