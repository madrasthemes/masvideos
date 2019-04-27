<?php
/**
 * Categories Widget
 *
 * @package MasVideos/Widgets
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Categories widget class.
 */
class MasVideos_Widget_Videos_Categories extends MasVideos_Widget {

	/**
	 * category ancestors.
	 *
	 * @var array
	 */
	public $category_ancestors;

	/**
	 * Current category.
	 *
	 * @var bool
	 */
	public $current_category;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'masvideos widget_videos_categories masvideos-widget-videos-categories';
		$this->widget_description = __( 'A list or dropdown of videos categories.', 'masvideos' );
		$this->widget_id          = 'masvideos_widget_videos_categories';
		$this->widget_name        = __( 'MAS Videos Videos by Categories', 'masvideos' );
		$this->settings           = array(
			'title'              => array(
				'type'  => 'text',
				'std'   => __( 'Videos categories', 'masvideos' ),
				'label' => __( 'Title', 'masvideos' ),
			),
			'orderby'            => array(
				'type'    => 'select',
				'std'     => 'name',
				'label'   => __( 'Order by', 'masvideos' ),
				'options' => array(
					'order' => __( 'category order', 'masvideos' ),
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
				'label' => __( 'Show videos counts', 'masvideos' ),
			),
			'hierarchical'       => array(
				'type'  => 'checkbox',
				'std'   => 1,
				'label' => __( 'Show hierarchy', 'masvideos' ),
			),
			'show_children_only' => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Only show children of the current category', 'masvideos' ),
			),
			'hide_empty'         => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Hide empty categories', 'masvideos' ),
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
			'taxonomy'     => 'video_cat',
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

		$this->current_category   = false;
		$this->category_ancestors = array();

		if ( is_tax( 'video_cat' ) ) {
			$this->current_category   = $wp_query->queried_object;
			$this->category_ancestors = get_ancestors( $this->current_category->term_id, 'video_cat' );

		} elseif ( is_singular( 'video' ) ) {
			$terms = masvideos_get_video_terms(
				$post->ID, 'video_cat', apply_filters(
					'masvideos_video_categories_widget_terms_args', array(
						'orderby' => 'parent',
						'order'   => 'DESC',
					)
				)
			);

			if ( $terms ) {
				$main_term           = apply_filters( 'masvideos_video_categories_widget_main_term', $terms[0], $terms );
				$this->current_category   = $main_term;
				$this->category_ancestors = get_ancestors( $main_term->term_id, 'video_cat' );
			}
		}

		// Show Siblings and Children Only.
		if ( $show_children_only && $this->current_category ) {
			if ( $hierarchical ) {
				$include = array_merge(
					$this->category_ancestors,
					array( $this->current_category->term_id ),
					get_terms(
						'video_cat',
						array(
							'fields'       => 'ids',
							'parent'       => 0,
							'hierarchical' => true,
							'hide_empty'   => false,
						)
					),
					get_terms(
						'video_cat',
						array(
							'fields'       => 'ids',
							'parent'       => $this->current_category->term_id,
							'hierarchical' => true,
							'hide_empty'   => false,
						)
					)
				);
				// Gather siblings of ancestors.
				if ( $this->category_ancestors ) {
					foreach ( $this->category_ancestors as $ancestor ) {
						$include = array_merge(
							$include, get_terms(
								'video_cat',
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
					'video_cat',
					array(
						'fields'       => 'ids',
						'parent'       => $this->current_category->term_id,
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
			masvideos_video_dropdown_categories(
				apply_filters(
					'masvideos_video_categories_widget_dropdown_args', wp_parse_args(
						$dropdown_args, array(
							'show_count'         => $count,
							'hierarchical'       => $hierarchical,
							'show_uncategorized' => 0,
							'orderby'            => $orderby,
							'selected'           => $this->current_category ? $this->current_category->slug : '',
						)
					)
				)
			);

			wp_enqueue_script( 'selectWoo' );
			wp_enqueue_style( 'select2' );

			masvideos_enqueue_js(
				"
				jQuery( '.dropdown_video_cat' ).change( function() {
					if ( jQuery(this).val() != '' ) {
						var this_page = '';
						var home_url  = '" . esc_js( home_url( '/' ) ) . "';
						if ( home_url.indexOf( '?' ) > 0 ) {
							this_page = home_url + '&video_cat=' + jQuery(this).val();
						} else {
							this_page = home_url + '?video_cat=' + jQuery(this).val();
						}
						location.href = this_page;
					} else {
						location.href = '" . esc_js( masvideos_get_page_permalink( 'videos' ) ) . "';
					}
				});

				if ( jQuery().selectWoo ) {
					var masvideos_video_cat_select = function() {
						jQuery( '.dropdown_video_cat' ).selectWoo( {
							placeholder: '" . esc_js( __( 'Select a category', 'masvideos' ) ) . "',
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
					masvideos_video_cat_select();
				}
			"
			);
		} else {
			include_once MasVideos()->plugin_path() . '/includes/walkers/class-masvideos-videos-category-list-walker.php';

			$list_args['walker']                     = new MasVideos_video_category_List_Walker();
			$list_args['title_li']                   = '';
			$list_args['pad_counts']                 = 1;
			$list_args['show_option_none']           = __( 'No videos categories exist.', 'masvideos' );
			$list_args['current_category']           = ( $this->current_category ) ? $this->current_category->term_id : '';
			$list_args['current_category_ancestors'] = $this->category_ancestors;
			$list_args['max_depth']                  = $max_depth;

			echo '<ul class="videos-categories">';

			wp_list_categories( apply_filters( 'masvideos_videos_categories_widget_args', $list_args ) );

			echo '</ul>';
		}

		$this->widget_end( $args );
	}
}
