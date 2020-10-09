<?php
/**
 * Class MasVideos_TMDB_Importer_Controller file.
 *
 * @package MasVideos\Admin\Importers
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WP_Importer' ) ) {
    return;
}

/**
 * Movie importer controller - handles tmdb api in admin.
 *
 * @package     MasVideos/Admin/Importers
 * @version     1.0.0
 */
class MasVideos_TMDB_Importer_Controller {

    /**
     * API results.
     *
     * @var array
     */
    protected $results = array();

    /**
     * API results data for CSV.
     *
     * @var array
     */
    protected $results_csv_data_key = array();

    /**
     * API results data for CSV.
     *
     * @var array
     */
    protected $results_csv_data = array();

    /**
     * API results data count.
     *
     * @var int
     */
    protected $results_csv_data_count = '';

    /**
     * API results.
     *
     * @var array
     */
    protected $file = '';


    /**
     * Importer type.
     *
     * @var array
     */
    protected $type = '';

    /**
     * The current import step.
     *
     * @var string
     */
    protected $step = '';

    /**
     * Progress steps.
     *
     * @var array
     */
    protected $steps = array();

    /**
     * Errors.
     *
     * @var array
     */
    protected $errors = array();

    /**
     * Constructor.
     */
    public function __construct() {
        $default_steps = array(
            'fetch'  => array(
                'name'    => __( 'Fetch TMDB API', 'masvideos' ),
                'view'    => array( $this, 'fetch_form' ),
                'handler' => array( $this, 'fetch_form_handler' ),
            ),
            'results'  => array(
                'name'    => __( 'Import', 'masvideos' ),
                'view'    => array( $this, 'import_form' ),
                'handler' => ''
            ),
        );

        $this->steps = apply_filters( 'masvideos_tmdb_importer_steps', $default_steps );

        // phpcs:disable WordPress.CSRF.NonceVerification.NoNonceVerification
        $this->step             = isset( $_REQUEST['step'] ) ? sanitize_key( $_REQUEST['step'] ) : current( array_keys( $this->steps ) );
        $this->file             = isset( $_REQUEST['file'] ) ? masvideos_clean( wp_unslash( $_REQUEST['file'] ) ) : '';
        $this->type             = ! empty( $_REQUEST['type'] ) ? masvideos_clean( wp_unslash( $_REQUEST['type'] ) ) : '';
        $this->results_csv_data_count = ! empty( $_REQUEST['result_count'] ) ? masvideos_clean( wp_unslash( $_REQUEST['result_count'] ) ) : '';
        // $this->results_csv_data = ! empty( $_REQUEST['results_csv_data'] ) ? masvideos_clean( wp_unslash( $_REQUEST['results_csv_data'] ) ) : array();
    }

    /**
     * Get the URL for the next step's screen.
     *
     * @param string $step  slug (default: current step).
     * @return string       URL for next step if a next step exists.
     *                      Admin URL if it's the last step.
     *                      Empty string on failure.
     */
    public function get_next_step_link( $step = '' ) {
        if ( ! $step ) {
            $step = $this->step;
        }

        $keys = array_keys( $this->steps );

        if ( end( $keys ) === $step ) {
            return admin_url();
        }

        $step_index = array_search( $step, $keys, true );

        if ( false === $step_index ) {
            return '';
        }

        $params = array(
            'step'            => $keys[ $step_index + 1 ],
            'file'            => str_replace( DIRECTORY_SEPARATOR, '/', $this->file ),
            'type'            => $this->type,
            'result_count'    => $this->results_csv_data_count,
            '_wpnonce'        => wp_create_nonce( 'masvideos-tmdb-fetch-data' ), // wp_nonce_url() escapes & to &amp; breaking redirects.
        );

        return add_query_arg( $params );
    }

    /**
     * Dispatch the output of api page.
     */
    public function dispatch() {        
        // phpcs:ignore WordPress.CSRF.NonceVerification.NoNonceVerification
        if ( ! empty( $_POST['save_step'] ) && ! empty( $this->steps[ $this->step ]['handler'] ) ) {
            call_user_func( $this->steps[ $this->step ]['handler'], $this );
        }
        // $this->output_header();
        // $this->output_steps();
        // $this->output_errors();
        call_user_func( $this->steps[ $this->step ]['view'], $this );
        // $this->output_footer();
    }

    /**
     * Output information about the uploading process.
     */
    protected function fetch_form() {
        $type_options = apply_filters( 'masvideos_tmdb_importer_type_options', array(
            'search-movie'          => __( 'Search Movie', 'masvideos' ),
            'movie-by-id'           => __( 'Movie By ID', 'masvideos' ),
            'latest-movie'          => __( 'Latest Movie', 'masvideos' ),
            'now-playing-movies'    => __( 'Now Playing Movies', 'masvideos' ),
            'upcoming-movies'       => __( 'Upcoming Movies', 'masvideos' ),
            'popular-movies'        => __( 'Popular Movies', 'masvideos' ),
            'top-rated-movies'      => __( 'Top Rated Movies', 'masvideos' ),
            'discover-movies'       => __( 'Discover Movies', 'masvideos' ),
            'search-tv-show'        => __( 'Search TV Show', 'masvideos' ),
            'tv-show-by-id'         => __( 'TV Show By ID', 'masvideos' ),
            'latest-tv-show'        => __( 'Latest TV Show', 'masvideos' ),
            'on-air-tv-shows'       => __( 'ON Air TV Shows', 'masvideos' ),
            'on-air-today-tv-shows' => __( 'ON Air Today TV Shows', 'masvideos' ),
            'popular-tv-shows'      => __( 'Popular TV Shows', 'masvideos' ),
            'top-rated-tv-shows'    => __( 'Top Rated TV Shows', 'masvideos' ),
            'discover-tv-shows'     => __( 'Discover TV Shows', 'masvideos' ),
        ) );
        include dirname( __FILE__ ) . '/views/html-tmdb-import-fetch-form.php';
    }

    /**
     * Handle the upload form and store options.
     */
    public function fetch_form_handler() {
        check_admin_referer( 'masvideos-tmdb-fetch-data' );

        // phpcs:disable WordPress.CSRF.NonceVerification.NoNonceVerification -- Nonce already verified in MasVideos_Movie_CSV_Importer_Controller::upload_form_handler()
        $api_key = get_option( 'masvideos_tmdb_api', '' );
        $language = isset( $_POST['masvideos-tmdb-language'] ) ? str_replace( '_', '-', masvideos_clean( wp_unslash( $_POST['masvideos-tmdb-language'] ) ) ) : 'en';
        $type = isset( $_POST['masvideos-tmdb-type'] ) ? masvideos_clean( wp_unslash( $_POST['masvideos-tmdb-type'] ) ) : '';
        $page = isset( $_POST['masvideos-tmdb-page-number'] ) ? masvideos_clean( wp_unslash( $_POST['masvideos-tmdb-page-number'] ) ) : 1;
        $tmdb_id = isset( $_POST['masvideos-tmdb-id'] ) ? masvideos_clean( wp_unslash( $_POST['masvideos-tmdb-id'] ) ) : 1;
        $keyword = isset( $_POST['masvideos-tmdb-search-keyword'] ) ? masvideos_clean( wp_unslash( $_POST['masvideos-tmdb-search-keyword'] ) ) : '';

        if ( empty( $api_key ) || empty( $type ) ) {
            return;
        }

        include_once MASVIDEOS_ABSPATH . 'includes/integrations/tmdb/class-masvideos-tmdb.php';

        // Configuration
        $cnf = array(
            'apikey'    => $api_key,
            'lang'      => $language,
            'timezone'  => 'Europe/London',
            'adult'     => false,
            'debug'     => false
        );

        // Data Return Configuration - Manipulate if you want to tune your results
        $cnf['appender'] = array(
            'movie'         => array( 'account_states', 'alternative_titles', 'credits', 'images','keywords', 'release_dates', 'videos', 'translations', 'similar', 'reviews', 'lists', 'changes', 'rating' ),
            'tvshow'        => array( 'account_states', 'alternative_titles', 'changes', 'content_rating', 'credits', 'external_ids', 'images', 'keywords', 'rating', 'similar', 'translations', 'videos' ),
            'season'        => array( 'changes', 'account_states', 'credits', 'external_ids', 'images', 'videos' ),
            'episode'       => array( 'changes', 'account_states', 'credits', 'external_ids', 'images', 'rating', 'videos' ),
            'person'        => array( 'movie_credits', 'tv_credits', 'combined_credits', 'external_ids', 'images', 'tagged_images', 'changes' ),
            'collection'    => array( 'images' ),
            'company'       => array( 'movies' ),
        );

        $tmdb = new MasVideos_TMDB( $cnf );

        switch ( $type ) {
            case 'now-playing-movies':
                $this->type = 'movie';
                $this->results = $tmdb->getNowPlayingMovies( $page );
                $this->results_csv_data_count = count( $this->results );
                $movies = array();
                foreach ( $this->results as $key => $movie ) {
                    $movie = $this->handle_movie_data( $tmdb, $tmdb->getMovie( $movie['id'] ) );
                    $movies[] = $movie;
                    if ( count( $this->results_csv_data_key ) < count( $movie ) ) {
                        $this->results_csv_data_key = array_keys( $movie );
                    }
                }
                $this->results_csv_data = $movies;
                break;

            case 'upcoming-movies':
                $this->type = 'movie';
                $this->results = $tmdb->getUpcomingMovies( $page );
                $this->results_csv_data_count = count( $this->results );
                $movies = array();
                foreach ( $this->results as $key => $movie ) {
                    $movie = $this->handle_movie_data( $tmdb, $tmdb->getMovie( $movie['id'] ) );
                    $movies[] = $movie;
                    if ( count( $this->results_csv_data_key ) < count( $movie ) ) {
                        $this->results_csv_data_key = array_keys( $movie );
                    }
                }
                $this->results_csv_data = $movies;
                break;

            case 'popular-movies':
                $this->type = 'movie';
                $this->results = $tmdb->getPopularMovies( $page );
                $this->results_csv_data_count = count( $this->results );
                $movies = array();
                foreach ( $this->results as $key => $movie ) {
                    $movie = $this->handle_movie_data( $tmdb, $tmdb->getMovie( $movie['id'] ) );
                    $movies[] = $movie;
                    if ( count( $this->results_csv_data_key ) < count( $movie ) ) {
                        $this->results_csv_data_key = array_keys( $movie );
                    }
                }
                $this->results_csv_data = $movies;
                break;

            case 'top-rated-movies':
                $this->type = 'movie';
                $this->results = $tmdb->getTopRatedMovies( $page );
                $this->results_csv_data_count = count( $this->results );
                $movies = array();
                foreach ( $this->results as $key => $movie ) {
                    $movie = $this->handle_movie_data( $tmdb, $tmdb->getMovie( $movie['id'] ) );
                    $movies[] = $movie;
                    if ( count( $this->results_csv_data_key ) < count( $movie ) ) {
                        $this->results_csv_data_key = array_keys( $movie );
                    }
                }
                $this->results_csv_data = $movies;
                break;

            case 'discover-movies':
                $this->type = 'movie';
                $this->results = $tmdb->getDiscoverMovies( $page );
                $this->results_csv_data_count = count( $this->results );
                $movies = array();
                foreach ( $this->results as $key => $movie ) {
                    $movie = $this->handle_movie_data( $tmdb, $tmdb->getMovie( $movie['id'] ) );
                    $movies[] = $movie;
                    if ( count( $this->results_csv_data_key ) < count( $movie ) ) {
                        $this->results_csv_data_key = array_keys( $movie );
                    }
                }
                $this->results_csv_data = $movies;
                break;

            case 'latest-movie':
                $this->type = 'movie';
                $this->results = $tmdb->getLatestMovie();
                $this->results_csv_data_count = 1;
                $movies = array();
                $movie = $this->handle_movie_data( $tmdb, $tmdb->getMovie( $this->results['id'] ) );
                $movies[] = $movie;
                if ( count( $this->results_csv_data_key ) < count( $movie ) ) {
                    $this->results_csv_data_key = array_keys( $movie );
                }
                $this->results_csv_data = $movies;
                break;

            case 'movie-by-id':
                $this->type = 'movie';
                $this->results = $tmdb->getMovie( $tmdb_id );
                $this->results_csv_data_count = 1;
                $movies = array();
                $movie = $this->handle_movie_data( $tmdb, $this->results );
                $movies[] = $movie;
                if ( count( $this->results_csv_data_key ) < count( $movie ) ) {
                    $this->results_csv_data_key = array_keys( $movie );
                }
                $this->results_csv_data = $movies;
                break;

            case 'search-movie':
                $this->type = 'movie';
                $this->results = $tmdb->searchMovie( $keyword );
                $this->results_csv_data_count = count( $this->results );
                $movies = array();
                foreach ( $this->results as $key => $movie ) {
                    if ( ! empty( $movie ) ) {
                        $movie = $this->handle_movie_data( $tmdb, $tmdb->getMovie( $movie['id'] ) );
                        $movies[] = $movie;
                        if ( count( $this->results_csv_data_key ) < count( $movie ) ) {
                            $this->results_csv_data_key = array_keys( $movie );
                        }
                    }
                }
                $this->results_csv_data = $movies;
                break;

            case 'on-air-tv-shows':
                $this->type = 'tv_show';
                $this->results = $tmdb->getOnTheAirTVShows( $page );
                $this->results_csv_data_count = count( $this->results );
                $tv_shows = array();
                $episode_keys = array();
                foreach ( $this->results as $key => $tv_show ) {
                    if ( ! empty( $tv_show ) ) {
                        $tv_show = $this->handle_tv_show_data( $tmdb, $tmdb->getTVShow( $tv_show['id'] ) );
                        if( isset( $tv_show['episodes'] ) && ! empty( $tv_show['episodes'] ) ) {
                            $episodes = $tv_show['episodes'];
                            unset( $tv_show['episodes'] );
                        } else {
                            $episodes = array();
                        }

                        $tv_shows[] = $tv_show;

                        foreach ( $episodes as $key => $episode ) {
                            $tv_shows[] = $episode;
                            if ( count( $episode_keys ) < count( $episode ) ) {
                                $episode_keys = array_keys( $episode );
                            }
                        }

                        if ( count( $this->results_csv_data_key ) < count( $tv_show ) ) {
                            $this->results_csv_data_key = array_keys( $tv_show );
                        }
                    }
                }
                $this->results_csv_data_key = array_unique( array_merge( $this->results_csv_data_key, $episode_keys ) );
                $this->results_csv_data = $tv_shows;
                break;

            case 'on-air-today-tv-shows':
                $this->type = 'tv_show';
                $this->results = $tmdb->getAiringTodayTVShows( $page );
                $this->results_csv_data_count = count( $this->results );
                $tv_shows = array();
                $episode_keys = array();
                foreach ( $this->results as $key => $tv_show ) {
                    if ( ! empty( $tv_show ) ) {
                        $tv_show = $this->handle_tv_show_data( $tmdb, $tmdb->getTVShow( $tv_show['id'] ) );
                        if( isset( $tv_show['episodes'] ) && ! empty( $tv_show['episodes'] ) ) {
                            $episodes = $tv_show['episodes'];
                            unset( $tv_show['episodes'] );
                        } else {
                            $episodes = array();
                        }

                        $tv_shows[] = $tv_show;

                        foreach ( $episodes as $key => $episode ) {
                            $tv_shows[] = $episode;
                            if ( count( $episode_keys ) < count( $episode ) ) {
                                $episode_keys = array_keys( $episode );
                            }
                        }

                        if ( count( $this->results_csv_data_key ) < count( $tv_show ) ) {
                            $this->results_csv_data_key = array_keys( $tv_show );
                        }
                    }
                }
                $this->results_csv_data_key = array_unique( array_merge( $this->results_csv_data_key, $episode_keys ) );
                $this->results_csv_data = $tv_shows;
                break;

            case 'popular-tv-shows':
                $this->type = 'tv_show';
                $this->results = $tmdb->getAiringTodayTVShows( $page );
                $this->results_csv_data_count = count( $this->results );
                $tv_shows = array();
                $episode_keys = array();
                foreach ( $this->results as $key => $tv_show ) {
                    if ( ! empty( $tv_show ) ) {
                        $tv_show = $this->handle_tv_show_data( $tmdb, $tmdb->getTVShow( $tv_show['id'] ) );
                        if( isset( $tv_show['episodes'] ) && ! empty( $tv_show['episodes'] ) ) {
                            $episodes = $tv_show['episodes'];
                            unset( $tv_show['episodes'] );
                        } else {
                            $episodes = array();
                        }

                        $tv_shows[] = $tv_show;

                        foreach ( $episodes as $key => $episode ) {
                            $tv_shows[] = $episode;
                            if ( count( $episode_keys ) < count( $episode ) ) {
                                $episode_keys = array_keys( $episode );
                            }
                        }

                        if ( count( $this->results_csv_data_key ) < count( $tv_show ) ) {
                            $this->results_csv_data_key = array_keys( $tv_show );
                        }
                    }
                }
                $this->results_csv_data_key = array_unique( array_merge( $this->results_csv_data_key, $episode_keys ) );
                $this->results_csv_data = $tv_shows;
                break;

            case 'top-rated-tv-showss':
                $this->type = 'tv_show';
                $this->results = $tmdb->getTopRatedTVShows( $page );
                $this->results_csv_data_count = count( $this->results );
                $tv_shows = array();
                $episode_keys = array();
                foreach ( $this->results as $key => $tv_show ) {
                    if ( ! empty( $tv_show ) ) {
                        $tv_show = $this->handle_tv_show_data( $tmdb, $tmdb->getTVShow( $tv_show['id'] ) );
                        if( isset( $tv_show['episodes'] ) && ! empty( $tv_show['episodes'] ) ) {
                            $episodes = $tv_show['episodes'];
                            unset( $tv_show['episodes'] );
                        } else {
                            $episodes = array();
                        }

                        $tv_shows[] = $tv_show;

                        foreach ( $episodes as $key => $episode ) {
                            $tv_shows[] = $episode;
                            if ( count( $episode_keys ) < count( $episode ) ) {
                                $episode_keys = array_keys( $episode );
                            }
                        }

                        if ( count( $this->results_csv_data_key ) < count( $tv_show ) ) {
                            $this->results_csv_data_key = array_keys( $tv_show );
                        }
                    }
                }
                $this->results_csv_data_key = array_unique( array_merge( $this->results_csv_data_key, $episode_keys ) );
                $this->results_csv_data = $tv_shows;
                break;

            case 'discover-tv-shows':
                $this->type = 'tv_show';
                $this->results = $tmdb->getDiscoverTVShows( $page );
                $this->results_csv_data_count = count( $this->results );
                $tv_shows = array();
                $episode_keys = array();
                foreach ( $this->results as $key => $tv_show ) {
                    if ( ! empty( $tv_show ) ) {
                        $tv_show = $this->handle_tv_show_data( $tmdb, $tmdb->getTVShow( $tv_show['id'] ) );
                        if( isset( $tv_show['episodes'] ) && ! empty( $tv_show['episodes'] ) ) {
                            $episodes = $tv_show['episodes'];
                            unset( $tv_show['episodes'] );
                        } else {
                            $episodes = array();
                        }

                        $tv_shows[] = $tv_show;

                        foreach ( $episodes as $key => $episode ) {
                            $tv_shows[] = $episode;
                            if ( count( $episode_keys ) < count( $episode ) ) {
                                $episode_keys = array_keys( $episode );
                            }
                        }

                        if ( count( $this->results_csv_data_key ) < count( $tv_show ) ) {
                            $this->results_csv_data_key = array_keys( $tv_show );
                        }
                    }
                }
                $this->results_csv_data_key = array_unique( array_merge( $this->results_csv_data_key, $episode_keys ) );
                $this->results_csv_data = $tv_shows;
                break;

            case 'latest-tv-show':
                $this->type = 'tv_show';
                $this->results = $tmdb->getLatestTVShow();
                $this->results_csv_data_count = 1;
                $tv_shows = array();
                $episode_keys = array();
                foreach ( $this->results as $key => $tv_show ) {
                    if ( ! empty( $tv_show ) ) {
                        $tv_show = $this->handle_tv_show_data( $tmdb, $tmdb->getTVShow( $tv_show['id'] ) );
                        if( isset( $tv_show['episodes'] ) && ! empty( $tv_show['episodes'] ) ) {
                            $episodes = $tv_show['episodes'];
                            unset( $tv_show['episodes'] );
                        } else {
                            $episodes = array();
                        }

                        $tv_shows[] = $tv_show;

                        foreach ( $episodes as $key => $episode ) {
                            $tv_shows[] = $episode;
                            if ( count( $episode_keys ) < count( $episode ) ) {
                                $episode_keys = array_keys( $episode );
                            }
                        }

                        if ( count( $this->results_csv_data_key ) < count( $tv_show ) ) {
                            $this->results_csv_data_key = array_keys( $tv_show );
                        }
                    }
                }
                $this->results_csv_data_key = array_unique( array_merge( $this->results_csv_data_key, $episode_keys ) );
                $this->results_csv_data = $tv_shows;
                break;

            case 'tv-show-by-id':
                $this->type = 'tv_show';
                $this->results = $tmdb->getTVShow( $tmdb_id );
                $this->results_csv_data_count = 1;
                $tv_shows = array();
                $episode_keys = array();
                $tv_show = $this->handle_tv_show_data( $tmdb, $this->results );
                if( isset( $tv_show['episodes'] ) && ! empty( $tv_show['episodes'] ) ) {
                    $episodes = $tv_show['episodes'];
                    unset( $tv_show['episodes'] );
                } else {
                    $episodes = array();
                }

                $tv_shows[] = $tv_show;

                foreach ( $episodes as $key => $episode ) {
                    $tv_shows[] = $episode;
                    if ( count( $episode_keys ) < count( $episode ) ) {
                        $episode_keys = array_keys( $episode );
                    }
                }

                if ( count( $this->results_csv_data_key ) < count( $tv_show ) ) {
                    $this->results_csv_data_key = array_keys( $tv_show );
                }
                $this->results_csv_data_key = array_unique( array_merge( $this->results_csv_data_key, $episode_keys ) );
                $this->results_csv_data = $tv_shows;
                break;

            case 'search-tv-show':
                $this->type = 'tv_show';
                $this->results = $tmdb->searchTVShow( $keyword );
                $this->results_csv_data_count = count( $this->results );
                $tv_shows = array();
                $episode_keys = array();
                foreach ( $this->results as $key => $tv_show ) {
                    if ( ! empty( $tv_show ) ) {
                        $tv_show = $this->handle_tv_show_data( $tmdb, $tmdb->getTVShow( $tv_show['id'] ) );
                        if( isset( $tv_show['episodes'] ) && ! empty( $tv_show['episodes'] ) ) {
                            $episodes = $tv_show['episodes'];
                            unset( $tv_show['episodes'] );
                        } else {
                            $episodes = array();
                        }

                        $tv_shows[] = $tv_show;

                        foreach ( $episodes as $key => $episode ) {
                            $tv_shows[] = $episode;
                            if ( count( $episode_keys ) < count( $episode ) ) {
                                $episode_keys = array_keys( $episode );
                            }
                        }

                        if ( count( $this->results_csv_data_key ) < count( $tv_show ) ) {
                            $this->results_csv_data_key = array_keys( $tv_show );
                        }
                    }
                }
                $this->results_csv_data_key = array_unique( array_merge( $this->results_csv_data_key, $episode_keys ) );
                $this->results_csv_data = $tv_shows;
                break;

            default:
                break;
        }

        // echo '<pre>' . print_r( $this->results, 1 ) . '</pre>';
        // echo '<pre>' . print_r( $this->results_csv_data, 1 ) . '</pre>';
        // exit;

        if ( empty( $this->results_csv_data ) ) {
            return;
        }

        $file = $this->handle_upload();

        if ( is_wp_error( $file ) ) {
            // $this->add_error( $file->get_error_message() );
            return;
        } else {
            $this->file = $file;
        }

        wp_redirect( esc_url_raw( $this->get_next_step_link() ) );
        exit;
    }

    /**
     * Generate movie data array for CSV.
     */
    protected function handle_movie_data( $tmdb, $data ) {
        // echo '<pre>' . print_r( $data, 1 ) . '</pre>';
        // exit;
        foreach( $data as $key => $values ) {
            if( ! is_array( $values ) ) {
                switch( $key ) {
                    case 'title' :
                        $movie['Title'] = $values;
                        break;
                    case 'tagline' :
                        $movie['Short description'] = $values;
                        break;
                    case 'overview' :
                        $movie['Description'] = $values;
                        break;
                    case 'release_date' :
                        $movie['Movie Release Date'] = $values;
                        break;
                    case 'runtime' :
                        $movie['Movie Run Time'] = $values;
                        break;
                    case 'status' :
                        $movie['movie_status'] = $values;
                        break;
                    case 'homepage' :
                        $movie['Movie Choice'] = 'movie_url';
                        $movie['Movie Link'] = $values;
                        $movie['Is Affiliate URL ?'] = 1;
                        break;
                    case 'poster_path' :
                    case 'backdrop_path' :
                        $movie['Images'] = ! empty( $values ) ? $tmdb->getImageURL() . $values : $values;
                        break;
                    case 'id' :
                        $movie['TMDB ID'] = $values;
                        break;
                    case 'imdb_id' :
                        $movie['IMDB ID'] = $values;
                        break;
                    default :
                        $movie[$key] = $values;
                        break;
                }
            } else {
                if( $key == 'credits' ) {
                    $offset = apply_filters( 'masvideos_tmdb_import_movie_cast_crew_offset', 1 );
                    $limit = 15;
                    if( isset( $values['cast'] ) && !empty( $values['cast'] ) ) {
                        for( $i = $offset - 1; $i < min( ( $limit + $offset - 1 ), count( $values['cast'] ) ); $i++ ) {
                            $cast_no = $i + 1;
                            $movie["Cast {$cast_no} Person IMDB ID"] = '';
                            $movie["Cast {$cast_no} Person TMDB ID"] = $values['cast'][$i]['id'];
                            $movie["Cast {$cast_no} Person Name"] = $values['cast'][$i]['name'];
                            $movie["Cast {$cast_no} Person Images"] = ! empty( $values['cast'][$i]['profile_path'] ) ? $tmdb->getImageURL() . $values['cast'][$i]['profile_path'] : '';
                            $movie["Cast {$cast_no} Person Category"] = 'Acting';
                            $movie["Cast {$cast_no} Person Character"] = ! empty( $values['cast'][$i]['character'] ) ? $values['cast'][$i]['character'] : '';
                            $movie["Cast {$cast_no} Position"] = ! empty( $values['cast'][$i]['order'] ) ? $values['cast'][$i]['order'] : $cast_no;
                        }
                    }

                    if( isset( $values['crew'] ) && !empty( $values['crew'] ) ) {
                        $i = 1;
                        for( $i = $offset - 1; $i < min( ( $limit + $offset - 1 ), count( $values['crew'] ) ); $i++ ) {
                            $crew_no = $i + 1;
                            $movie["Crew {$crew_no} Person IMDB ID"] = '';
                            $movie["Crew {$crew_no} Person TMDB ID"] = $values['crew'][$i]['id'];
                            $movie["Crew {$crew_no} Person Name"] = $values['crew'][$i]['name'];
                            $movie["Crew {$crew_no} Person Images"] = ! empty( $values['crew'][$i]['profile_path'] ) ? $tmdb->getImageURL() . $values['crew'][$i]['profile_path'] : '';
                            $movie["Crew {$crew_no} Person Category"] = ! empty( $values['crew'][$i]['department'] ) ? $values['crew'][$i]['department'] : '';
                            $movie["Crew {$crew_no} Person Job"] = ! empty( $values['crew'][$i]['job'] ) ? $values['crew'][$i]['job'] : '';
                            $movie["Crew {$crew_no} Position"] = ! empty( $values['crew'][$i]['order'] ) ? $values['crew'][$i]['order'] : $crew_no;
                        }
                    }
                } elseif( $key == 'genres' ) {
                    $movie['Genres'] = implode( ",", array_column( $values, 'name') );
                }
            }
        }

        return apply_filters( 'masvideos_tmdb_importer_handle_movie_data', $movie, $tmdb, $data );
    }

    /**
     * Generate movie data array for CSV.
     */
    protected function handle_episode_data( $tmdb, $data, $parent_tv_show, $parent_season ) {
        foreach ( $data as $key => $values ) {
            if( ! is_array( $values ) ) {
                switch( $key ) {
                    case 'name' :
                        $episode['Title'] = $values;
                        break;
                    case 'overview' :
                        $episode['Description'] = $values;
                        break;
                    case 'air_date' :
                        $episode['Episode Release Date'] = $values;
                        break;
                    case 'episode_number' :
                        $episode['Episode Number'] = $values;
                        break;
                    case 'still_path' :
                        $episode['Images'] = ! empty( $values ) ? $tmdb->getImageURL() . $values : $values;
                        break;
                    case 'id' :
                        $episode['TMDB ID'] = $values;
                        break;
                    case 'imdb_id' :
                        $episode['IMDB ID'] = $values;
                        break;
                    default :
                        $episode[$key] = $values;
                        break;
                }
            } elseif( $key == 'genres' ) {
                $episode['Genres'] = implode( ",", array_column( $values, 'name') );
            }
        }

        $episode['type'] = 'episode';
        $episode['Parent TV Show'] = $parent_tv_show;
        $episode['Parent Season'] = $parent_season;
        
        return apply_filters( 'masvideos_tmdb_importer_handle_episode_data', $episode, $tmdb, $data, $parent_tv_show, $parent_season );
    }

    /**
     * Generate movie data array for CSV.
     */
    protected function handle_tv_show_data( $tmdb, $data ) {
        $episodes = array();

        foreach( $data as $key => $values ) {
            if( ! is_array( $values ) ) {
                switch( $key ) {
                    case 'name' :
                        $tv_show['Title'] = $values;
                        break;
                    case 'overview' :
                        $tv_show['Description'] = $values;
                        break;
                    case 'status' :
                        $tv_show['tv_show_status'] = $values;
                        break;
                    case 'homepage' :
                        $tv_show['TV Show Choice'] = 'tv_show_url';
                        $tv_show['TV Show Link'] = $values;
                        break;
                    case 'poster_path' :
                    case 'backdrop_path' :
                        $tv_show['Images'] = ! empty( $values ) ? $tmdb->getImageURL() . $values : $values;
                        break;
                    case 'id' :
                        $tv_show['TMDB ID'] = $values;
                        break;
                    case 'imdb_id' :
                        $tv_show['IMDB ID'] = $values;
                        break;
                    case 'type' :
                        $tv_show['tv_show_type'] = $values;
                        break;
                    default :
                        $tv_show[$key] = $values;
                        break;
                }
            } else {
                if( $key == 'credits' ) {
                    $offset = apply_filters( 'masvideos_tmdb_import_tv_show_cast_crew_offset', 1 );
                    $limit = 15;
                    if( isset( $values['cast'] ) && !empty( $values['cast'] ) ) {
                        for( $i = $offset - 1; $i < min( ( $limit + $offset - 1 ), count( $values['cast'] ) ); $i++ ) {
                            $cast_no = $i + 1;
                            $tv_show["Cast {$cast_no} Person IMDB ID"] = '';
                            $tv_show["Cast {$cast_no} Person TMDB ID"] = $values['cast'][$i]['id'];
                            $tv_show["Cast {$cast_no} Person Name"] = $values['cast'][$i]['name'];
                            $tv_show["Cast {$cast_no} Person Images"] = ! empty( $values['cast'][$i]['profile_path'] ) ? $tmdb->getImageURL() . $values['cast'][$i]['profile_path'] : '';
                            $tv_show["Cast {$cast_no} Person Category"] = 'Acting';
                            $tv_show["Cast {$cast_no} Person Character"] = ! empty( $values['cast'][$i]['character'] ) ? $values['cast'][$i]['character'] : '';
                            $tv_show["Cast {$cast_no} Position"] = ! empty( $values['cast'][$i]['order'] ) ? $values['cast'][$i]['order'] : $cast_no;
                        }
                    }

                    if( isset( $values['crew'] ) && !empty( $values['crew'] ) ) {
                        $i = 1;
                        for( $i = $offset - 1; $i < min( ( $limit + $offset - 1 ), count( $values['crew'] ) ); $i++ ) {
                            $crew_no = $i + 1;
                            $tv_show["Crew {$crew_no} Person IMDB ID"] = '';
                            $tv_show["Crew {$crew_no} Person TMDB ID"] = $values['crew'][$i]['id'];
                            $tv_show["Crew {$crew_no} Person Name"] = $values['crew'][$i]['name'];
                            $tv_show["Crew {$crew_no} Person Images"] = ! empty( $values['crew'][$i]['profile_path'] ) ? $tmdb->getImageURL() . $values['crew'][$i]['profile_path'] : '';
                            $tv_show["Crew {$crew_no} Person Category"] = ! empty( $values['crew'][$i]['department'] ) ? $values['crew'][$i]['department'] : '';
                            $tv_show["Crew {$crew_no} Person Job"] = ! empty( $values['crew'][$i]['job'] ) ? $values['crew'][$i]['job'] : '';
                            $tv_show["Crew {$crew_no} Position"] = ! empty( $values['crew'][$i]['order'] ) ? $values['crew'][$i]['order'] : $crew_no;
                        }
                    }
                } elseif( $key == 'genres' ) {
                    $tv_show['Genres'] = implode( ",", array_column( $values, 'name') );
                } elseif( $key == 'seasons' ) {
                    $i = 1;
                    foreach( $values as $season ) {
                        $season_data = $tmdb->getSeason( $data['id'], $season['season_number'] );
                        $tv_show["Season ${i} name"] = isset( $season_data['name'] ) ? $season_data['name'] : '';
                        $tv_show["Season ${i} image"] = isset( $season_data['poster_path'] ) && ! empty( $season_data['poster_path'] ) ? $tmdb->getImageURL() . $season_data['poster_path'] : '';
                        $tv_show["Season ${i} description"] = isset( $season_data['overview'] ) ? $season_data['overview'] : '';
                        $tv_show["Season ${i} position"] = isset( $season_data['season_number'] ) ? $season_data['season_number'] : '';
                        $tv_show["Season ${i} number"] = isset( $season_data['season_number'] ) ? $season_data['season_number'] : '';
                        $tv_show["Season ${i} IMDB ID"] = '';
                        $tv_show["Season ${i} TMDB ID"] = isset( $season_data['id'] ) ? $season_data['id'] : '';
                        $tv_show["Season ${i} year"] = isset( $season_data['air_date'] ) ? $season_data['air_date'] : '';
                        if( ! empty( $season_data['episodes'] ) ) {
                            foreach ( $season_data['episodes'] as $key => $episode ) {
                                $episodes[] = $this->handle_episode_data( $tmdb, $episode, $tv_show['Title'], $season_data['name'] );
                            }
                        }
                        $i++;
                    }
                }
            }
        }

        $tv_show['episodes'] = $episodes;
        $tv_show['type'] = 'tv_show';

        return apply_filters( 'masvideos_tmdb_importer_handle_tv_show_data', $tv_show, $tmdb, $data );
    }

    /**
     * Store results in CSV file.
     */
    protected function handle_upload() {
        $upload_dir = wp_upload_dir( null, false );

        $file_name = 'masvideos-tmdb-csv-output' . date('U') . '.csv';
        $file = $upload_dir['path'] . '/' . $file_name;

        $f = fopen( $file, 'w+' );
        if ( $f === false ) {
            die( __( 'Couldn\'t create the file to store the CSV, or the path is invalid.', 'masvideos' ) );
        }

        fputcsv( $f, $this->results_csv_data_key );
        $lineKeys = array_fill_keys( $this->results_csv_data_key, '' );
        foreach ( $this->results_csv_data as $line ) {
            // Using array_merge is important to maintain the order of keys acording to the first element
            fputcsv( $f, array_merge( $lineKeys, $line ) );
        }
        fclose( $f );

        // Construct the object array.
        $object = array(
            'post_title'     => basename( $file ),
            'post_content'   => $upload_dir['url'] . '/' . $file_name,
            'post_mime_type' => 'text/csv',
            'guid'           => $upload_dir['url'] . '/' . $file_name,
            'context'        => 'import',
            'post_status'    => 'private',
        );

        // Save the data.
        $id = wp_insert_attachment( $object, $file );

        /*
         * Schedule a cleanup for one day from now in case of failed
         * import or missing wp_import_cleanup() call.
         */
        wp_schedule_single_event( time() + DAY_IN_SECONDS, 'importer_scheduled_cleanup', array( $id ) );

        return $file;
    }

    /**
     * Import the results.
     */
    protected function import_form() {
        $action = admin_url( 'edit.php?post_type=' . $this->type . '&page=' . $this->type . '_importer' );
        $file_url = str_replace( ABSPATH, '', $this->file );
        if( isset( $_SERVER['DOCUMENT_ROOT'] ) ) {
            $file_url = str_replace( $_SERVER['DOCUMENT_ROOT'] . '/', '', $this->file );
        }
        include dirname( __FILE__ ) . '/views/html-tmdb-import-form.php';
    }
}
