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
    protected $results_csv_data = array();

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
        $this->step            = isset( $_REQUEST['step'] ) ? sanitize_key( $_REQUEST['step'] ) : current( array_keys( $this->steps ) );
        $this->file            = isset( $_REQUEST['file'] ) ? masvideos_clean( wp_unslash( $_REQUEST['file'] ) ) : '';
        $this->type            = ! empty( $_REQUEST['type'] ) ? masvideos_clean( wp_unslash( $_REQUEST['type'] ) ) : '';
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
        include dirname( __FILE__ ) . '/views/html-tmdb-import-fetch-form.php';
    }

    /**
     * Handle the upload form and store options.
     */
    public function fetch_form_handler() {
        check_admin_referer( 'masvideos-tmdb-fetch-data' );

        // phpcs:disable WordPress.CSRF.NonceVerification.NoNonceVerification -- Nonce already verified in MasVideos_Movie_CSV_Importer_Controller::upload_form_handler()
        $api_key = get_option( 'masvideos_tmdb_api', '' );
        $type = isset( $_POST['type'] ) ? masvideos_clean( wp_unslash( $_POST['type'] ) ) : '';
        $page = isset( $_POST['page'] ) ? masvideos_clean( wp_unslash( $_POST['page'] ) ) : 1;

        if ( empty( $api_key ) || empty( $type ) ) {
            return;
        }

        include_once MASVIDEOS_ABSPATH . 'includes/integrations/tmdb/class-masvideos-tmdb.php';

        // Configuration
        $cnf = array(
            'apikey'    => $api_key,
            'lang'      => 'ta',
            'timezone'  => 'Asia/Kolkata',
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
                $this->results = $tmdb->getNowPlayingMovies( $page );
                $movies = array();
                foreach ( $this->results as $key => $movie ) {
                    $movies[] = $this->handle_movie_data( $tmdb, $tmdb->getMovie( $movie['id'] ) );
                }
                $this->results_csv_data = $movies;
                $this->type = 'movie';
                break;

            case 'popular-movies':
                $this->results = $tmdb->getPopularMovies( $page );
                $movies = array();
                foreach ( $this->results as $key => $movie ) {
                    $movies[] = $this->handle_movie_data( $tmdb, $tmdb->getMovie( $movie['id'] ) );
                }
                $this->results_csv_data = $movies;
                $this->type = 'movie';
                break;

            case 'top-rated-movies':
                $this->results = $tmdb->getTopRatedMovies( $page );
                $movies = array();
                foreach ( $this->results as $key => $movie ) {
                    $movies[] = $this->handle_movie_data( $tmdb, $tmdb->getMovie( $movie['id'] ) );
                }
                $this->results_csv_data = $movies;
                $this->type = 'movie';
                break;

            case 'upcoming-movies':
                $this->results = $tmdb->getUpcomingMovies( $page );
                $movies = array();
                foreach ( $this->results as $key => $movie ) {
                    $movies[] = $this->handle_movie_data( $tmdb, $tmdb->getMovie( $movie['id'] ) );
                }
                $this->results_csv_data = $movies;
                $this->type = 'movie';
                break;

            default:
                $this->results = $tmdb->getNowPlayingMovies( $page );
                $movies = array();
                foreach ( $this->results as $key => $movie ) {
                    $movies[] = $this->handle_movie_data( $tmdb, $tmdb->getMovie( $movie['id'] ) );
                }
                $this->results_csv_data = $movies;
                $this->type = 'movie';
                break;
        }

        // echo '<pre>' . print_r( $this->results, 1 ) . '</pre>';
        // echo '<pre>' . print_r( $this->results_csv_data, 1 ) . '</pre>';
        // exit;

        // $file = $this->handle_upload();

        // if ( is_wp_error( $file ) ) {
        //     // $this->add_error( $file->get_error_message() );
        //     return;
        // } else {
        //     $this->file = $file;
        // }

        // wp_redirect( esc_url_raw( $this->get_next_step_link() ) );
        // exit;
    }

    /**
     * Generate movie data array for CSV.
     */
    protected function handle_movie_data( $tmdb, $data ) {
        foreach( $data as $key => $values ) {
            if( ! is_array( $values ) && ! empty( $values ) ) {
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
                        break;
                    case 'poster_path' :
                    case 'backdrop_path' :
                        $movie['Images'] = $tmdb->getImageURL() . $values;
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
            } elseif( !empty( $values ) ) {
                if( $key == 'credits' ) {
                    if( isset( $values['cast'] ) && !empty( $values['cast'] ) ) {
                        $i = 1;
                        foreach( $values['cast'] as $cast ) {
                            $movie["Cast ${i} Person IMDB ID"] = '';
                            $movie["Cast ${i} Person TMDB ID"] = $cast['id'];
                            $movie["Cast ${i} Person Name"] = $cast['name'];
                            $movie["Cast ${i} Person Images"] = ! empty( $cast['profile_path'] ) ? $tmdb->getImageURL() . $cast['profile_path'] : '';
                            $movie["Cast ${i} Person Category"] = 'Acting';
                            $movie["Cast ${i} Person Character"] = $cast['character'];
                            $movie["Cast ${i} Position"] = $cast['order'];
                            $i++;
                        }
                    }

                    if( isset( $values['crew'] ) && !empty( $values['crew'] ) ) {
                        $i = 1;
                        foreach( $values['crew'] as $crew ) {
                            $movie["Crew ${i} Person IMDB ID"] = '';
                            $movie["Crew ${i} Person TMDB ID"] = $crew['id'];
                            $movie["Crew ${i} Person Name"] = $crew['name'];
                            $movie["Crew ${i} Person Images"] = ! empty( $crew['profile_path'] ) ? $tmdb->getImageURL() . $crew['profile_path'] : '';
                            $movie["Crew ${i} Person Category"] = $crew['department'];
                            $movie["Crew ${i} Person Job"] = $crew['job'];
                            $movie["Crew ${i} Position"] = $crew['order'];
                            $i++;
                        }
                    }
                } elseif( $key == 'genres' ) {
                    $movie['Genres'] = implode( ",", array_column( $values, 'name') );
                }
            }
        }

        return $movie;
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

        $firstLineKeys = array();
        foreach ( $this->results_csv_data as $line ) {
            if ( empty( $firstLineKeys ) ) {
                $firstLineKeys = array_keys( $line );
                fputcsv( $f, $firstLineKeys );
                $firstLineKeys = array_flip( $firstLineKeys );
            }

            // Using array_merge is important to maintain the order of keys acording to the first element
            fputcsv( $f, array_merge( $firstLineKeys, $line ) );
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
        include dirname( __FILE__ ) . '/views/html-tmdb-import-form.php';
    }
}
