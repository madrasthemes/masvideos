<?php
/**
 * MasVideos_Breadcrumb class.
 *
 * @package MasVideos/Classes
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Breadcrumb class.
 */
class MasVideos_Breadcrumb {

	/**
	 * Breadcrumb trail.
	 *
	 * @var array
	 */
	private $crumbs = array();

	/**
	 * Add a crumb so we don't get lost.
	 *
	 * @param string $name Name.
	 * @param string $link Link.
	 */
	public function add_crumb( $name, $link = '' ) {
		$this->crumbs[] = array(
			strip_tags( $name ),
			$link,
		);
	}

	/**
	 * Reset crumbs.
	 */
	public function reset() {
		$this->crumbs = array();
	}

	/**
	 * Get the breadcrumb.
	 *
	 * @return array
	 */
	public function get_breadcrumb() {
		return apply_filters( 'masvideos_get_breadcrumb', $this->crumbs, $this );
	}

	/**
	 * Generate breadcrumb trail.
	 *
	 * @return array of breadcrumbs
	 */
	public function generate() {
		$conditionals = array(
			'is_home',
			'is_404',
			'is_attachment',
			'is_single',
			'is_tv_show_genre',
			'is_tv_show_tag',
			'is_tv_shows',
			'is_video_category',
			'is_video_tag',
			'is_videos',
			'is_movie_genre',
			'is_movie_tag',
			'is_movies',
			'is_page',
			'is_post_type_archive',
			'is_category',
			'is_tag',
			'is_author',
			'is_date',
			'is_tax',
		);

		if ( ( ! is_front_page() && ! ( is_post_type_archive() && in_array( intval( get_option( 'page_on_front' ) ), array( masvideos_get_page_id( 'tv_shows' ), masvideos_get_page_id( 'videos' ), masvideos_get_page_id( 'movies' ) ) ) ) ) || is_paged() ) {
			foreach ( $conditionals as $conditional ) {
				if ( call_user_func( $conditional ) ) {
					call_user_func( array( $this, 'add_crumbs_' . substr( $conditional, 3 ) ) );
					break;
				}
			}

			$this->search_trail();
			$this->paged_trail();

			return $this->get_breadcrumb();
		}

		return array();
	}

	/**
	 * Prepend the archive page to archive breadcrumbs.
	 */
	private function prepend_tv_shows_page() {
		$permalinks 		= masvideos_get_permalink_structure();
		$tv_shows_page_id 	= masvideos_get_page_id( 'tv_shows' );
		$tv_shows_page 		= get_post( $tv_shows_page_id );

		// If permalinks contain the tv_show page in the URI prepend the breadcrumb with tv_shows.
		if ( $tv_shows_page_id && $tv_shows_page && isset( $permalinks['tv_show_base'] ) && strstr( $permalinks['tv_show_base'], '/' . $tv_shows_page->post_name ) && intval( get_option( 'page_on_front' ) ) !== $tv_shows_page_id ) {
			$this->add_crumb( get_the_title( $tv_shows_page ), get_permalink( $tv_shows_page ) );
		}
	}

	/**
	 * Prepend the archive page to archive breadcrumbs.
	 */
	private function prepend_videos_page() {
		$permalinks 	= masvideos_get_permalink_structure();
		$videos_page_id = masvideos_get_page_id( 'videos' );
		$videos_page 	= get_post( $videos_page_id );

		// If permalinks contain the video page in the URI prepend the breadcrumb with videos.
		if ( $videos_page_id && $videos_page && isset( $permalinks['video_base'] ) && strstr( $permalinks['video_base'], '/' . $videos_page->post_name ) && intval( get_option( 'page_on_front' ) ) !== $videos_page_id ) {
			$this->add_crumb( get_the_title( $videos_page ), get_permalink( $videos_page ) );
		}
	}

	/**
	 * Prepend the archive page to archive breadcrumbs.
	 */
	private function prepend_movies_page() {
		$permalinks 	= masvideos_get_permalink_structure();
		$movies_page_id = masvideos_get_page_id( 'movies' );
		$movies_page 	= get_post( $movies_page_id );

		// If permalinks contain the movie page in the URI prepend the breadcrumb with movies.
		if ( $movies_page_id && $movies_page && isset( $permalinks['movie_base'] ) && strstr( $permalinks['movie_base'], '/' . $movies_page->post_name ) && intval( get_option( 'page_on_front' ) ) !== $movies_page_id ) {
			$this->add_crumb( get_the_title( $movies_page ), get_permalink( $movies_page ) );
		}
	}

	/**
	 * Is home trail..
	 */
	private function add_crumbs_home() {
		$this->add_crumb( single_post_title( '', false ) );
	}

	/**
	 * 404 trail.
	 */
	private function add_crumbs_404() {
		$this->add_crumb( __( 'Error 404', 'masvideos' ) );
	}

	/**
	 * Attachment trail.
	 */
	private function add_crumbs_attachment() {
		global $post;

		$this->add_crumbs_single( $post->post_parent, get_permalink( $post->post_parent ) );
		$this->add_crumb( get_the_title(), get_permalink() );
	}

	/**
	 * Single post trail.
	 *
	 * @param int    $post_id   Post ID.
	 * @param string $permalink Post permalink.
	 */
	private function add_crumbs_single( $post_id = 0, $permalink = '' ) {
		if ( ! $post_id ) {
			global $post;
		} else {
			$post = get_post( $post_id ); // WPCS: override ok.
		}

		if ( ! $permalink ) {
			$permalink = get_permalink( $post );
		}

		if ( 'episode' === get_post_type( $post ) ) {
			$episode 	= masvideos_get_episode( $post->ID );
			$tv_show_id = $episode->get_tv_show_id();
			$tv_show 	= masvideos_get_tv_show( $tv_show_id );

			if( $tv_show ) {
				$this->prepend_tv_shows_page();

				$terms = masvideos_get_tv_show_terms(
					$tv_show_id, 'tv_show_genre', apply_filters(
						'masvideos_breadcrumb_tv_show_terms_args', array(
							'orderby' => 'parent',
							'order'   => 'DESC',
						)
					)
				);

				if ( $terms ) {
					$main_term = apply_filters( 'masvideos_breadcrumb_main_term', $terms[0], $terms );
					$this->term_ancestors( $main_term->term_id, 'tv_show_genre' );
					$this->add_crumb( $main_term->name, get_term_link( $main_term ) );
				}

				$this->add_crumb( get_the_title( $tv_show_id ), get_permalink( $tv_show_id ) );
				$season_id = $episode->get_tv_show_season_id();
				$seasons = $tv_show->get_seasons();
				if( ! empty( $seasons ) && isset( $seasons[$season_id]['name'] ) ) {
					$this->add_crumb( $seasons[$season_id]['name'], get_permalink( $tv_show_id ) . '?season-position=' . $seasons[$season_id]['position'] );
				}
			}
		} elseif ( 'tv_show' === get_post_type( $post ) ) {
			$this->prepend_tv_shows_page();

			$terms = masvideos_get_tv_show_terms(
				$post->ID, 'tv_show_genre', apply_filters(
					'masvideos_breadcrumb_tv_show_terms_args', array(
						'orderby' => 'parent',
						'order'   => 'DESC',
					)
				)
			);

			if ( $terms ) {
				$main_term = apply_filters( 'masvideos_breadcrumb_main_term', $terms[0], $terms );
				$this->term_ancestors( $main_term->term_id, 'tv_show_genre' );
				$this->add_crumb( $main_term->name, get_term_link( $main_term ) );
			}
		} elseif ( 'video' === get_post_type( $post ) ) {
			$this->prepend_videos_page();

			$terms = masvideos_get_video_terms(
				$post->ID, 'video_cat', apply_filters(
					'masvideos_breadcrumb_video_terms_args', array(
						'orderby' => 'parent',
						'order'   => 'DESC',
					)
				)
			);

			if ( $terms ) {
				$main_term = apply_filters( 'masvideos_breadcrumb_main_term', $terms[0], $terms );
				$this->term_ancestors( $main_term->term_id, 'video_cat' );
				$this->add_crumb( $main_term->name, get_term_link( $main_term ) );
			}
		} elseif ( 'movie' === get_post_type( $post ) ) {
			$this->prepend_movies_page();

			$terms = masvideos_get_movie_terms(
				$post->ID, 'movie_genre', apply_filters(
					'masvideos_breadcrumb_movie_terms_args', array(
						'orderby' => 'parent',
						'order'   => 'DESC',
					)
				)
			);

			if ( $terms ) {
				$main_term = apply_filters( 'masvideos_breadcrumb_main_term', $terms[0], $terms );
				$this->term_ancestors( $main_term->term_id, 'movie_genre' );
				$this->add_crumb( $main_term->name, get_term_link( $main_term ) );
			}
		} elseif ( 'post' !== get_post_type( $post ) ) {
			$post_type = get_post_type_object( get_post_type( $post ) );

			if ( ! empty( $post_type->has_archive ) ) {
				$this->add_crumb( $post_type->labels->singular_name, get_post_type_archive_link( get_post_type( $post ) ) );
			}
		} else {
			$cat = current( get_the_category( $post ) );
			if ( $cat ) {
				$this->term_ancestors( $cat->term_id, 'category' );
				$this->add_crumb( $cat->name, get_term_link( $cat ) );
			}
		}

		$this->add_crumb( get_the_title( $post ), $permalink );
	}

	/**
	 * Page trail.
	 */
	private function add_crumbs_page() {
		global $post;

		if ( $post->post_parent ) {
			$parent_crumbs = array();
			$parent_id     = $post->post_parent;

			while ( $parent_id ) {
				$page            = get_post( $parent_id );
				$parent_id       = $page->post_parent;
				$parent_crumbs[] = array( get_the_title( $page->ID ), get_permalink( $page->ID ) );
			}

			$parent_crumbs = array_reverse( $parent_crumbs );

			foreach ( $parent_crumbs as $crumb ) {
				$this->add_crumb( $crumb[0], $crumb[1] );
			}
		}

		$this->add_crumb( get_the_title(), get_permalink() );
	}

	/**
	 * TV Show category trail.
	 */
	private function add_crumbs_tv_show_genre() {
		$current_term = $GLOBALS['wp_query']->get_queried_object();

		$this->prepend_tv_shows_page();
		$this->term_ancestors( $current_term->term_id, 'tv_show_genre' );
		$this->add_crumb( $current_term->name, get_term_link( $current_term, 'tv_show_genre' ) );
	}

	/**
	 * TV Show tag trail.
	 */
	private function add_crumbs_tv_show_tag() {
		$current_term = $GLOBALS['wp_query']->get_queried_object();

		$this->prepend_tv_shows_page();

		/* translators: %s: tv_show tag */
		$this->add_crumb( sprintf( __( 'TV Shows tagged &ldquo;%s&rdquo;', 'masvideos' ), $current_term->name ), get_term_link( $current_term, 'tv_show_genre' ) );
	}

	/**
	 * TV Shows breadcrumb.
	 */
	private function add_crumbs_tv_shows() {
		if ( intval( get_option( 'page_on_front' ) ) === masvideos_get_page_id( 'tv_shows' ) ) {
			return;
		}

		$_name = masvideos_get_page_id( 'tv_shows' ) ? get_the_title( masvideos_get_page_id( 'tv_shows' ) ) : '';

		if ( ! $_name ) {
			$tv_show_post_type = get_post_type_object( 'tv_show' );
			$_name             = $tv_show_post_type->labels->name;
		}

		$this->add_crumb( $_name, get_post_type_archive_link( 'tv_show' ) );
	}

	/**
	 * Video category trail.
	 */
	private function add_crumbs_video_category() {
		$current_term = $GLOBALS['wp_query']->get_queried_object();

		$this->prepend_videos_page();
		$this->term_ancestors( $current_term->term_id, 'video_genre' );
		$this->add_crumb( $current_term->name, get_term_link( $current_term, 'video_genre' ) );
	}

	/**
	 * Video tag trail.
	 */
	private function add_crumbs_video_tag() {
		$current_term = $GLOBALS['wp_query']->get_queried_object();

		$this->prepend_videos_page();

		/* translators: %s: video tag */
		$this->add_crumb( sprintf( __( 'Videos tagged &ldquo;%s&rdquo;', 'masvideos' ), $current_term->name ), get_term_link( $current_term, 'video_genre' ) );
	}

	/**
	 * Movies breadcrumb.
	 */
	private function add_crumbs_videos() {
		if ( intval( get_option( 'page_on_front' ) ) === masvideos_get_page_id( 'videos' ) ) {
			return;
		}

		$_name = masvideos_get_page_id( 'videos' ) ? get_the_title( masvideos_get_page_id( 'videos' ) ) : '';

		if ( ! $_name ) {
			$video_post_type = get_post_type_object( 'video' );
			$_name             = $video_post_type->labels->name;
		}

		$this->add_crumb( $_name, get_post_type_archive_link( 'video' ) );
	}

	/**
	 * Movie category trail.
	 */
	private function add_crumbs_movie_genre() {
		$current_term = $GLOBALS['wp_query']->get_queried_object();

		$this->prepend_movies_page();
		$this->term_ancestors( $current_term->term_id, 'movie_genre' );
		$this->add_crumb( $current_term->name, get_term_link( $current_term, 'movie_genre' ) );
	}

	/**
	 * Movie tag trail.
	 */
	private function add_crumbs_movie_tag() {
		$current_term = $GLOBALS['wp_query']->get_queried_object();

		$this->prepend_movies_page();

		/* translators: %s: movie tag */
		$this->add_crumb( sprintf( __( 'Movies tagged &ldquo;%s&rdquo;', 'masvideos' ), $current_term->name ), get_term_link( $current_term, 'movie_genre' ) );
	}

	/**
	 * Movies breadcrumb.
	 */
	private function add_crumbs_movies() {
		if ( intval( get_option( 'page_on_front' ) ) === masvideos_get_page_id( 'movies' ) ) {
			return;
		}

		$_name = masvideos_get_page_id( 'movies' ) ? get_the_title( masvideos_get_page_id( 'movies' ) ) : '';

		if ( ! $_name ) {
			$movie_post_type = get_post_type_object( 'movie' );
			$_name             = $movie_post_type->labels->name;
		}

		$this->add_crumb( $_name, get_post_type_archive_link( 'movie' ) );
	}

	/**
	 * Post type archive trail.
	 */
	private function add_crumbs_post_type_archive() {
		$post_type = get_post_type_object( get_post_type() );

		if ( $post_type ) {
			$this->add_crumb( $post_type->labels->name, get_post_type_archive_link( get_post_type() ) );
		}
	}

	/**
	 * Category trail.
	 */
	private function add_crumbs_category() {
		$this_category = get_category( $GLOBALS['wp_query']->get_queried_object() );

		if ( 0 !== intval( $this_category->parent ) ) {
			$this->term_ancestors( $this_category->term_id, 'category' );
		}

		$this->add_crumb( single_cat_title( '', false ), get_category_link( $this_category->term_id ) );
	}

	/**
	 * Tag trail.
	 */
	private function add_crumbs_tag() {
		$queried_object = $GLOBALS['wp_query']->get_queried_object();

		/* translators: %s: tag name */
		$this->add_crumb( sprintf( __( 'Posts tagged &ldquo;%s&rdquo;', 'masvideos' ), single_tag_title( '', false ) ), get_tag_link( $queried_object->term_id ) );
	}

	/**
	 * Add crumbs for date based archives.
	 */
	private function add_crumbs_date() {
		if ( is_year() || is_month() || is_day() ) {
			$this->add_crumb( get_the_time( 'Y' ), get_year_link( get_the_time( 'Y' ) ) );
		}
		if ( is_month() || is_day() ) {
			$this->add_crumb( get_the_time( 'F' ), get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ) );
		}
		if ( is_day() ) {
			$this->add_crumb( get_the_time( 'd' ) );
		}
	}

	/**
	 * Add crumbs for taxonomies
	 */
	private function add_crumbs_tax() {
		$this_term = $GLOBALS['wp_query']->get_queried_object();
		$taxonomy  = get_taxonomy( $this_term->taxonomy );

		$this->add_crumb( $taxonomy->labels->name );

		if ( 0 !== intval( $this_term->parent ) ) {
			$this->term_ancestors( $this_term->term_id, $this_term->taxonomy );
		}

		$this->add_crumb( single_term_title( '', false ), get_term_link( $this_term->term_id, $this_term->taxonomy ) );
	}

	/**
	 * Add a breadcrumb for author archives.
	 */
	private function add_crumbs_author() {
		global $author;

		$userdata = get_userdata( $author );

		/* translators: %s: author name */
		$this->add_crumb( sprintf( __( 'Author: %s', 'masvideos' ), $userdata->display_name ) );
	}

	/**
	 * Add crumbs for a term.
	 *
	 * @param int    $term_id  Term ID.
	 * @param string $taxonomy Taxonomy.
	 */
	private function term_ancestors( $term_id, $taxonomy ) {
		$ancestors = get_ancestors( $term_id, $taxonomy );
		$ancestors = array_reverse( $ancestors );

		foreach ( $ancestors as $ancestor ) {
			$ancestor = get_term( $ancestor, $taxonomy );

			if ( ! is_wp_error( $ancestor ) && $ancestor ) {
				$this->add_crumb( $ancestor->name, get_term_link( $ancestor ) );
			}
		}
	}

	/**
	 * Add a breadcrumb for search results.
	 */
	private function search_trail() {
		if ( is_search() ) {
			/* translators: %s: search term */
			$this->add_crumb( sprintf( __( 'Search results for &ldquo;%s&rdquo;', 'masvideos' ), get_search_query() ), remove_query_arg( 'paged' ) );
		}
	}

	/**
	 * Add a breadcrumb for pagination.
	 */
	private function paged_trail() {
		if ( get_query_var( 'paged' ) ) {
			/* translators: %d: page number */
			$this->add_crumb( sprintf( __( 'Page %d', 'masvideos' ), get_query_var( 'paged' ) ) );
		}
	}
}
