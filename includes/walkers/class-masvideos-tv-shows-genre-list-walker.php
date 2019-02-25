<?php
/**
 * MasVideos_TV_Show_Genre_List_Walker class
 *
 * @package MasVideos/Classes/Walkers
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'MasVideos_TV_Show_Genre_List_Walker', false ) ) {
	return;
}

/**
 * MasVideos TV Shows genre list walker class.
 */
class MasVideos_TV_Show_Genre_List_Walker extends Walker {

	/**
	 * What the class handles.
	 *
	 * @var string
	 */
	public $tree_type = 'tv_show_genre';

	/**
	 * DB fields to use.
	 *
	 * @var array
	 */
	public $db_fields = array(
		'parent' => 'parent',
		'id'     => 'term_id',
		'slug'   => 'slug',
	);

	/**
	 * Starts the list before the elements are added.
	 *
	 * @see Walker::start_lvl()
	 * @since 1.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth Depth of genre. Used for tab indentation.
	 * @param array  $args Will only append content if style argument value is 'list'.
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		if ( 'list' !== $args['style'] ) {
			return;
		}

		$indent  = str_repeat( "\t", $depth );
		$output .= "$indent<ul class='children'>\n";
	}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * @see Walker::end_lvl()
	 * @since 1.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth Depth of genre. Used for tab indentation.
	 * @param array  $args Will only append content if style argument value is 'list'.
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		if ( 'list' !== $args['style'] ) {
			return;
		}

		$indent  = str_repeat( "\t", $depth );
		$output .= "$indent</ul>\n";
	}

	/**
	 * Start the element output.
	 *
	 * @see Walker::start_el()
	 * @since 1.0.0
	 *
	 * @param string  $output            Passed by reference. Used to append additional content.
	 * @param object  $genre               Genreegory.
	 * @param int     $depth             Depth of genre in reference to parents.
	 * @param array   $args              Arguments.
	 * @param integer $current_object_id Current object ID.
	 */
	public function start_el( &$output, $genre, $depth = 0, $args = array(), $current_object_id = 0 ) {
		$genre_id = intval( $genre->term_id );

		$output .= '<li class="genre-item genre-item-' . $genre_id;

		if ( $args['current_genre'] === $genre_id ) {
			$output .= ' current-genre';
		}

		if ( $args['has_children'] && $args['hierarchical'] && ( empty( $args['max_depth'] ) || $args['max_depth'] > $depth + 1 ) ) {
			$output .= ' genre-parent';
		}

		if ( $args['current_genre_ancestors'] && $args['current_genre'] && in_array( $genre_id, $args['current_genre_ancestors'], true ) ) {
			$output .= ' current-genre-parent';
		}

		$output .= '"><a href="' . get_term_link( $genre_id, $this->tree_type ) . '">' . apply_filters( 'list_tv_show_genres', $genre->name, $genre ) . '</a>';

		if ( $args['show_count'] ) {
			$output .= ' <span class="count">(' . $genre->count . ')</span>';
		}
	}

	/**
	 * Ends the element output, if needed.
	 *
	 * @see Walker::end_el()
	 * @since 1.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $genre    Genre
	 * @param int    $depth  Depth of genre. Not used.
	 * @param array  $args   Only uses 'list' for whether should append to output.
	 */
	public function end_el( &$output, $genre, $depth = 0, $args = array() ) {
		$output .= "</li>\n";
	}

	/**
	 * Traverse elements to create list from elements.
	 *
	 * Display one element if the element doesn't have any children otherwise,
	 * display the element and its children. Will only traverse up to the max.
	 * depth and no ignore elements under that depth. It is possible to set the.
	 * max depth to include all depths, see walk() method.
	 *
	 * This method shouldn't be called directly, use the walk() method instead.
	 *
	 * @since 1.0.0
	 *
	 * @param object $element           Data object.
	 * @param array  $children_elements List of elements to continue traversing.
	 * @param int    $max_depth         Max depth to traverse.
	 * @param int    $depth             Depth of current element.
	 * @param array  $args              Arguments.
	 * @param string $output            Passed by reference. Used to append additional content.
	 * @return null Null on failure with no changes to parameters.
	 */
	public function display_element( $element, &$children_elements, $max_depth, $depth = 0, $args, &$output ) {
		if ( ! $element || ( 0 === $element->count && ! empty( $args[0]['hide_empty'] ) ) ) {
			return;
		}
		parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}
}
