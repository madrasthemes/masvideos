<?php
/**
 * Genres Widget
 *
 * @package MasVideos/Widgets
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Genres widget class.
 */
class MasVideos_Widget_Movies_Genres extends MasVideos_Widget {

	/**
	 * Genre ancestors.
	 *
	 * @var array
	 */
	public $genre_ancestors;

	/**
	 * Current Genre.
	 *
	 * @var bool
	 */
	public $current_genre;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'masvideos widget_movies_genres masvideos-widget-movies-genres';
		$this->widget_description = __( 'A list or dropdown of movies genres.', 'masvideos' );
		$this->widget_id          = 'masvideos_widget_movies_genres';
		$this->widget_name        = __( 'MAS Videos Movies by Genres', 'masvideos' );
		$this->settings           = array(
			'title'              => array(
				'type'  => 'text',
				'std'   => __( 'Movies genres', 'masvideos' ),
				'label' => __( 'Title', 'masvideos' ),
			),
			'orderby'            => array(
				'type'    => 'select',
				'std'     => 'name',
				'label'   => __( 'Order by', 'masvideos' ),
				'options' => array(
					'order' => __( 'Genre order', 'masvideos' ),
					'name'  => __( 'Name', 'masvideos' ),
				),
			),
			'dropdown'           => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Show as dropdown', 'masvideos' ),
			),
			'count'              => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Show movies counts', 'masvideos' ),
			),
			'hierarchical'       => array(
				'type'  => 'checkbox',
				'std'   => 1,
				'label' => __( 'Show hierarchy', 'masvideos' ),
			),
			'show_children_only' => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Only show children of the current genre', 'masvideos' ),
			),
			'hide_empty'         => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Hide empty genres', 'masvideos' ),
			),
			'max_depth'          => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Maximum depth', 'masvideos' ),
			),
		);

		parent::__construct();
	}

	/**
	 * Output widget.
	 *
	 * @see WP_Widget
	 * @param array $args     Widget arguments.
	 * @param array $instance Widget instance.
	 */
	public function widget( $args, $instance ) {
		global $wp_query, $post;

		$count              = isset( $instance['count'] ) ? $instance['count'] : $this->settings['count']['std'];
		$hierarchical       = isset( $instance['hierarchical'] ) ? $instance['hierarchical'] : $this->settings['hierarchical']['std'];
		$show_children_only = isset( $instance['show_children_only'] ) ? $instance['show_children_only'] : $this->settings['show_children_only']['std'];
		$dropdown           = isset( $instance['dropdown'] ) ? $instance['dropdown'] : $this->settings['dropdown']['std'];
		$orderby            = isset( $instance['orderby'] ) ? $instance['orderby'] : $this->settings['orderby']['std'];
		$hide_empty         = isset( $instance['hide_empty'] ) ? $instance['hide_empty'] : $this->settings['hide_empty']['std'];
		$dropdown_args      = array(
			'hide_empty' => $hide_empty,
		);
		$list_args          = array(
			'show_count'   => $count,
			'hierarchical' => $hierarchical,
			'taxonomy'     => 'movie_genre',
			'hide_empty'   => $hide_empty,
		);
		$max_depth          = absint( isset( $instance['max_depth'] ) ? $instance['max_depth'] : $this->settings['max_depth']['std'] );

		$list_args['menu_order'] = false;
		$dropdown_args['depth']  = $max_depth;
		$list_args['depth']      = $max_depth;

		if ( 'order' === $orderby ) {
			$list_args['menu_order'] = 'asc';
		} else {
			$list_args['orderby'] = 'title';
		}

		$this->current_genre   = false;
		$this->genre_ancestors = array();

		if ( is_tax( 'movie_genre' ) ) {
			$this->current_genre   = $wp_query->queried_object;
			$this->genre_ancestors = get_ancestors( $this->current_genre->term_id, 'movie_genre' );

		} elseif ( is_singular( 'movie' ) ) {
			$terms = masvideos_get_movie_terms(
				$post->ID, 'movie_genre', apply_filters(
					'masvideos_movie_genres_widget_terms_args', array(
						'orderby' => 'parent',
						'order'   => 'DESC',
					)
				)
			);

			if ( $terms ) {
				$main_term           = apply_filters( 'masvideos_movie_genres_widget_main_term', $terms[0], $terms );
				$this->current_genre   = $main_term;
				$this->genre_ancestors = get_ancestors( $main_term->term_id, 'movie_genre' );
			}
		}

		// Show Siblings and Children Only.
		if ( $show_children_only && $this->current_genre ) {
			if ( $hierarchical ) {
				$include = array_merge(
					$this->genre_ancestors,
					array( $this->current_genre->term_id ),
					get_terms(
						'movie_genre',
						array(
							'fields'       => 'ids',
							'parent'       => 0,
							'hierarchical' => true,
							'hide_empty'   => false,
						)
					),
					get_terms(
						'movie_genre',
						array(
							'fields'       => 'ids',
							'parent'       => $this->current_genre->term_id,
							'hierarchical' => true,
							'hide_empty'   => false,
						)
					)
				);
				// Gather siblings of ancestors.
				if ( $this->genre_ancestors ) {
					foreach ( $this->genre_ancestors as $ancestor ) {
						$include = array_merge(
							$include, get_terms(
								'movie_genre',
								array(
									'fields'       => 'ids',
									'parent'       => $ancestor,
									'hierarchical' => false,
									'hide_empty'   => false,
								)
							)
						);
					}
				}
			} else {
				// Direct children.
				$include = get_terms(
					'movie_genre',
					array(
						'fields'       => 'ids',
						'parent'       => $this->current_genre->term_id,
						'hierarchical' => true,
						'hide_empty'   => false,
					)
				);
			}

			$list_args['include']     = implode( ',', $include );
			$dropdown_args['include'] = $list_args['include'];

			if ( empty( $include ) ) {
				return;
			}
		} elseif ( $show_children_only ) {
			$dropdown_args['depth']        = 1;
			$dropdown_args['child_of']     = 0;
			$dropdown_args['hierarchical'] = 1;
			$list_args['depth']            = 1;
			$list_args['child_of']         = 0;
			$list_args['hierarchical']     = 1;
		}

		$this->widget_start( $args, $instance );

		if ( $dropdown ) {
			masvideos_movie_dropdown_genres(
				apply_filters(
					'masvideos_movie_genres_widget_dropdown_args', wp_parse_args(
						$dropdown_args, array(
							'show_count'         => $count,
							'hierarchical'       => $hierarchical,
							'show_uncategorized' => 0,
							'orderby'            => $orderby,
							'selected'           => $this->current_genre ? $this->current_genre->slug : '',
						)
					)
				)
			);

			wp_enqueue_script( 'selectWoo' );
			wp_enqueue_style( 'select2' );

			masvideos_enqueue_js(
				"
				jQuery( '.dropdown_movie_genre' ).change( function() {
					if ( jQuery(this).val() != '' ) {
						var this_page = '';
						var home_url  = '" . esc_js( home_url( '/' ) ) . "';
						if ( home_url.indexOf( '?' ) > 0 ) {
							this_page = home_url + '&movie_genre=' + jQuery(this).val();
						} else {
							this_page = home_url + '?movie_genre=' + jQuery(this).val();
						}
						location.href = this_page;
					} else {
						location.href = '" . esc_js( masvideos_get_page_permalink( 'movies' ) ) . "';
					}
				});

				if ( jQuery().selectWoo ) {
					var masvideos_movie_genre_select = function() {
						jQuery( '.dropdown_movie_genre' ).selectWoo( {
							placeholder: '" . esc_js( __( 'Select a genre', 'masvideos' ) ) . "',
							minimumResultsForSearch: 5,
							width: '100%',
							allowClear: true,
							language: {
								noResults: function() {
									return '" . esc_js( _x( 'No matches found', 'enhanced select', 'masvideos' ) ) . "';
								}
							}
						} );
					};
					masvideos_movie_genre_select();
				}
			"
			);
		} else {
			include_once MasVideos()->plugin_path() . '/includes/walkers/class-masvideos-movies-genre-list-walker.php';

			$list_args['walker']                     = new MasVideos_Movie_Genre_List_Walker();
			$list_args['title_li']                   = '';
			$list_args['pad_counts']                 = 1;
			$list_args['show_option_none']           = __( 'No movies genres exist.', 'masvideos' );
			$list_args['current_genre']           = ( $this->current_genre ) ? $this->current_genre->term_id : '';
			$list_args['current_genre_ancestors'] = $this->genre_ancestors;
			$list_args['max_depth']                  = $max_depth;

			echo '<ul class="movies-genres">';

			wp_list_categories( apply_filters( 'masvideos_movies_genres_widget_args', $list_args ) );

			echo '</ul>';
		}

		$this->widget_end( $args );
	}
}
