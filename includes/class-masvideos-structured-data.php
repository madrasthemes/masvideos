<?php
/**
 * Structured data's handler and generator using JSON-LD format.
 *
 * @package MasVideos/Classes
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Structured data class.
 */
class MasVideos_Structured_Data {

	/**
	 * Stores the structured data.
	 *
	 * @var array $_data Array of structured data.
	 */
	private $_data = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Generate structured data.
		add_action( 'masvideos_before_main_content', array( $this, 'generate_website_data' ), 30 );
		add_action( 'masvideos_breadcrumb', array( $this, 'generate_breadcrumblist_data' ), 10 );

		add_action( 'masvideos_episodes_loop', array( $this, 'generate_episode_data' ), 10 );
		add_action( 'masvideos_single_episode_summary', array( $this, 'generate_episode_data' ), 60 );
		add_action( 'masvideos_episode_review_meta', array( $this, 'generate_review_data' ), 20 );

		add_action( 'masvideos_tv_shows_loop', array( $this, 'generate_tv_show_data' ), 10 );
		add_action( 'masvideos_single_tv_show_summary', array( $this, 'generate_tv_show_data' ), 60 );
		add_action( 'masvideos_tv_show_review_meta', array( $this, 'generate_review_data' ), 20 );

		add_action( 'masvideos_movies_loop', array( $this, 'generate_movie_data' ), 10 );
		add_action( 'masvideos_single_movie_summary', array( $this, 'generate_movie_data' ), 60 );
		add_action( 'masvideos_movie_review_meta', array( $this, 'generate_review_data' ), 20 );

		add_action( 'masvideos_videos_loop', array( $this, 'generate_video_data' ), 10 );
		add_action( 'masvideos_single_video_summary', array( $this, 'generate_video_data' ), 60 );
		add_action( 'masvideos_video_review_meta', array( $this, 'generate_review_data' ), 20 );

		// Output structured data.
		add_action( 'wp_footer', array( $this, 'output_structured_data' ), 10 );
	}

	/**
	 * Sets data.
	 *
	 * @param  array $data  Structured data.
	 * @param  bool  $reset Unset data (default: false).
	 * @return bool
	 */
	public function set_data( $data, $reset = false ) {
		if ( ! isset( $data['@type'] ) || ! preg_match( '|^[a-zA-Z]{1,20}$|', $data['@type'] ) ) {
			return false;
		}

		if ( $reset && isset( $this->_data ) ) {
			unset( $this->_data );
		}

		$this->_data[] = $data;

		return true;
	}

	/**
	 * Gets data.
	 *
	 * @return array
	 */
	public function get_data() {
		return $this->_data;
	}

	/**
	 * Structures and returns data.
	 *
	 * List of types available by default for specific request:
	 *
	 * 'video',
	 * 'review',
	 * 'breadcrumblist',
	 * 'website',
	 *
	 * @param  array $types Structured data types.
	 * @return array
	 */
	public function get_structured_data( $types ) {
		$data = array();

		// Put together the values of same type of structured data.
		foreach ( $this->get_data() as $value ) {
			$data[ strtolower( $value['@type'] ) ][] = $value;
		}

		// Wrap the multiple values of each type inside a graph... Then add context to each type.
		foreach ( $data as $type => $value ) {
			$data[ $type ] = count( $value ) > 1 ? array( '@graph' => $value ) : $value[0];
			$data[ $type ] = apply_filters( 'masvideos_structured_data_context', array( '@context' => 'https://schema.org/' ), $data, $type, $value ) + $data[ $type ];
		}

		// If requested types, pick them up... Finally change the associative array to an indexed one.
		$data = $types ? array_values( array_intersect_key( $data, array_flip( $types ) ) ) : array_values( $data );

		if ( ! empty( $data ) ) {
			if ( 1 < count( $data ) ) {
				$data = apply_filters( 'masvideos_structured_data_context', array( '@context' => 'https://schema.org/' ), $data, '', '' ) + array( '@graph' => $data );
			} else {
				$data = $data[0];
			}
		}

		return $data;
	}

	/**
	 * Get data types for pages.
	 *
	 * @return array
	 */
	protected function get_data_type_for_page() {
		$types   = array();
		$types[] = is_masvideos() ? strtolower('VideoObject') : '';
		$types[] = ( is_tv_shows() || is_episodes() || is_movies() || is_videos() ) && is_front_page() ? 'website' : '';
		$types[] = ( is_tv_show() || is_episode() || is_movie() || is_video() ) ? 'review' : '';
		$types[] = ! ( is_tv_shows() || is_episodes() || is_movies() || is_videos() ) ? 'breadcrumblist' : '';

		return array_filter( apply_filters( 'masvideos_structured_data_type_for_page', $types ) );
	}

	/**
	 * Sanitizes, encodes and outputs structured data.
	 *
	 * Hooked into `wp_footer` action hook.
	 */
	public function output_structured_data() {
		$types = $this->get_data_type_for_page();
		$data  = $this->get_structured_data( $types );

		if ( $data ) {
			echo '<script type="application/ld+json">' . wp_json_encode( $data ) . '</script>';
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Generators
	|--------------------------------------------------------------------------
	|
	| Methods for generating specific structured data types:
	|
	| - Episode
	| - TV Show
	| - Movie
	| - Video
	| - Review
	| - BreadcrumbList
	| - WebSite
	|
	| The generated data is stored into `$this->_data`.
	| See the methods above for handling `$this->_data`.
	|
	*/

	/**
	 * Generates Episode structured data.
	 *
	 * Hooked into `masvideos_single_episode_summary` action hook.
	 * Hooked into `masvideos_episodes_loop` action hook.
	 *
	 * @param MasVideos_Episode $episode Episode data (default: null).
	 */
	public function generate_episode_data( $episode = null ) {
		if ( ! is_object( $episode ) ) {
			global $episode;
		}

		if ( ! is_a( $episode, 'MasVideos_Episode' ) ) {
			return;
		}

		$markup = array(
			'@type'			=> 'VideoObject',
			'name' 			=> $episode->get_name(),
			'description'	=> wpautop( do_shortcode( $episode->get_short_description() ? $episode->get_short_description() : $episode->get_description() ) ),
			'thumbnailUrl'	=> array( wp_get_attachment_url( $episode->get_image_id() ) ),
			'uploadDate'	=> $episode->get_date_created()->date( 'c' ),
		);

		if( ! empty( $episode->get_episode_run_time() ) ) {
			$markup['duration']	= $episode->get_episode_run_time();
		}

		$episode_choice = $episode->get_episode_choice();

		if ( $episode_choice == 'episode_file' ) {
			$markup['contentUrl']	= wp_get_attachment_url( $episode->get_episode_attachment_id() );
		} elseif ( $episode_choice == 'episode_embed' ) {
			preg_match( '/src="([^"]+)"/', $episode->get_episode_embed_content(), $match );
			if( isset( $match[1] ) ) {
				$markup['embedUrl']		= $match[1];
			}
		} elseif ( $episode_choice == 'episode_url' ) {
			$markup['contentUrl']	= $episode->get_episode_url_link();
		}

		$this->set_data( apply_filters( 'masvideos_structured_data_episode', $markup, $episode ) );
	}

	/**
     * Generates TV_Show structured data.
     *
     * Hooked into `masvideos_single_tv_show_summary` action hook.
     * Hooked into `masvideos_tv_shows_loop` action hook.
     *
     * @param MasVideos_TV_Show $tv_show TV_Show data (default: null).
     */
    public function generate_tv_show_data( $tv_show = null ) {
        if ( ! is_object( $tv_show ) ) {
            global $tv_show;
        }

        if ( ! is_a( $tv_show, 'MasVideos_TV_Show' ) ) {
            return;
        }

        $markup = array(
            '@type'         => 'VideoObject',
            'name'          => $tv_show->get_name(),
            'description'   => wpautop( do_shortcode( $tv_show->get_short_description() ? $tv_show->get_short_description() : $tv_show->get_description() ) ),
            'thumbnailUrl'  => array( wp_get_attachment_url( $tv_show->get_image_id() ) ),
            'uploadDate'    => $tv_show->get_date_created()->date( 'c' ),
        );

        $this->set_data( apply_filters( 'masvideos_structured_data_tv_show', $markup, $tv_show ) );
    }

	/**
	 * Generates Movie structured data.
	 *
	 * Hooked into `masvideos_single_movie_summary` action hook.
	 * Hooked into `masvideos_movies_loop` action hook.
	 *
	 * @param MasVideos_Movie $movie Movie data (default: null).
	 */
	public function generate_movie_data( $movie = null ) {
		if ( ! is_object( $movie ) ) {
			global $movie;
		}

		if ( ! is_a( $movie, 'MasVideos_Movie' ) ) {
			return;
		}

		$markup = array(
			'@type'			=> 'VideoObject',
			'name' 			=> $movie->get_name(),
			'description'	=> wpautop( do_shortcode( $movie->get_short_description() ? $movie->get_short_description() : $movie->get_description() ) ),
			'thumbnailUrl'	=> array( wp_get_attachment_url( $movie->get_image_id() ) ),
			'uploadDate'	=> $movie->get_date_created()->date( 'c' ),
		);

		if( ! empty( $movie->get_movie_run_time() ) ) {
			$markup['duration']	= $movie->get_movie_run_time();
		}

		$movie_choice = $movie->get_movie_choice();

		if ( $movie_choice == 'movie_file' ) {
			$markup['contentUrl']	= wp_get_attachment_url( $movie->get_movie_attachment_id() );
		} elseif ( $movie_choice == 'movie_embed' ) {
			preg_match( '/src="([^"]+)"/', $movie->get_movie_embed_content(), $match );
			if( isset( $match[1] ) ) {
				$markup['embedUrl']		= $match[1];
			}
		} elseif ( $movie_choice == 'movie_url' ) {
			$markup['contentUrl']	= $movie->get_movie_url_link();
		}

		$this->set_data( apply_filters( 'masvideos_structured_data_movie', $markup, $movie ) );
	}

	/**
     * Generates Video structured data.
     *
     * Hooked into `masvideos_single_video_summary` action hook.
     * Hooked into `masvideos_videos_loop` action hook.
     *
     * @param MasVideos_Video $video Video data (default: null).
     */
    public function generate_video_data( $video = null ) {
        if ( ! is_object( $video ) ) {
            global $video;
        }

        if ( ! is_a( $video, 'MasVideos_Video' ) ) {
            return;
        }

        $markup = array(
            '@type'         => 'VideoObject',
            'name'          => $video->get_name(),
            'description'   => wpautop( do_shortcode( $video->get_short_description() ? $video->get_short_description() : $video->get_description() ) ),
            'thumbnailUrl'  => array( wp_get_attachment_url( $video->get_image_id() ) ),
            'uploadDate'    => $video->get_date_created()->date( 'c' ),
        );

        $video_choice = $video->get_video_choice();

        if ( $video_choice == 'video_file' ) {
            $markup['contentUrl']   = wp_get_attachment_url( $video->get_video_attachment_id() );
        } elseif ( $video_choice == 'video_embed' ) {
            preg_match( '/src="([^"]+)"/', $video->get_video_embed_content(), $match );
            if( isset( $match[1] ) ) {
				$markup['embedUrl']		= $match[1];
			}
        } elseif ( $video_choice == 'video_url' ) {
            $markup['contentUrl']   = $video->get_video_url_link();
        }

        $this->set_data( apply_filters( 'masvideos_structured_data_video', $markup, $video ) );
    }

	/**
	 * Generates Review structured data.
	 *
	 * Hooked into `masvideos_review_meta` action hook.
	 *
	 * @param WP_Comment $comment Comment data.
	 */
	public function generate_review_data( $comment ) {
		$markup                  = array();
		$markup['@type']         = 'Review';
		$markup['@id']           = get_comment_link( $comment->comment_ID );
		$markup['datePublished'] = get_comment_date( 'c', $comment->comment_ID );
		$markup['description']   = get_comment_text( $comment->comment_ID );
		$markup['itemReviewed']  = array(
			'@type' => 'MediaObject',
			'name'  => get_the_title( $comment->comment_post_ID ),
		);

		// Skip replies unless they have a rating.
		$rating = get_comment_meta( $comment->comment_ID, 'rating', true );

		if ( $rating ) {
			$markup['reviewRating'] = array(
				'@type'         => 'rating',
				'ratingValue'   => $rating,
				'bestRating'    => 10,
				'worstRating'   => 1
			);
		} elseif ( $comment->comment_parent ) {
			return;
		}

		$markup['author'] = array(
			'@type' => 'Person',
			'name'  => get_comment_author( $comment->comment_ID ),
		);

		$this->set_data( apply_filters( 'masvideos_structured_data_review', $markup, $comment ) );
	}

	/**
	 * Generates BreadcrumbList structured data.
	 *
	 * Hooked into `masvideos_breadcrumb` action hook.
	 *
	 * @param MasVideos_Breadcrumb $breadcrumbs Breadcrumb data.
	 */
	public function generate_breadcrumblist_data( $breadcrumbs ) {
		$crumbs = $breadcrumbs->get_breadcrumb();

		if ( empty( $crumbs ) || ! is_array( $crumbs ) ) {
			return;
		}

		$markup                    = array();
		$markup['@type']           = 'BreadcrumbList';
		$markup['itemListElement'] = array();

		foreach ( $crumbs as $key => $crumb ) {
			// Don't add the current page to the breadcrumb list on single pages,
			// otherwise Google will not recognize both the BreadcrumbList and Single structured data.
			if ( ( is_tv_show() || is_episode() || is_movie() || is_video() ) && count( $crumbs ) - 1 === $key ) {
				continue;
			}

			$markup['itemListElement'][ $key ] = array(
				'@type'    => 'ListItem',
				'position' => $key + 1,
				'item'     => array(
					'name' => $crumb[0],
				),
			);

			if ( ! empty( $crumb[1] ) ) {
				$markup['itemListElement'][ $key ]['item'] += array( '@id' => $crumb[1] );
			}
		}

		$this->set_data( apply_filters( 'masvideos_structured_data_breadcrumblist', $markup, $breadcrumbs ) );
	}

	/**
	 * Generates WebSite structured data.
	 *
	 * Hooked into `masvideos_before_main_content` action hook.
	 */
	public function generate_website_data() {
		$markup                    = array();
		$markup['@type']           = 'WebSite';
		$markup['name']            = get_bloginfo( 'name' );
		$markup['url']             = home_url();
		$markup['potentialAction'] = array(
			'@type'       => 'SearchAction',
			'target'      => home_url( '?s={search_term_string}' ),
			'query-input' => 'required name=search_term_string',
		);

		$this->set_data( apply_filters( 'masvideos_structured_data_website', $markup ) );
	}
}
