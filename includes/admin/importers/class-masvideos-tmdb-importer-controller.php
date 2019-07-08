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
                'name'    => __( 'Results', 'masvideos' ),
                'view'    => array( $this, 'results_form' ),
                'handler' => array( $this, 'results_form_handler' ),
            ),
        );

        $this->steps = apply_filters( 'masvideos_tmdb_importer_steps', $default_steps );

        // phpcs:disable WordPress.CSRF.NonceVerification.NoNonceVerification
        $this->step            = isset( $_REQUEST['step'] ) ? sanitize_key( $_REQUEST['step'] ) : current( array_keys( $this->steps ) );
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
        include dirname( __FILE__ ) . '/views/html-tmdb-import-form.php';
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

        include_once MASVIDEOS_ABSPATH . 'includes/integrations/tmdb-api/tmdb-api.php';

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

        $tmdb = new TMDB( $cnf );

        switch ( $type ) {
            case 'now-playing-movies':
                $this->results = $tmdb->getNowPlayingMovies( $page );
                break;

            case 'popular-movies':
                $this->results = $tmdb->getPopularMovies( $page );
                break;

            case 'top-rated-movies':
                $this->results = $tmdb->getTopRatedMovies( $page );
                break;

            case 'upcoming-movies':
                $this->results = $tmdb->getUpcomingMovies( $page );
                break;

            default:
                $this->results = $tmdb->getNowPlayingMovies( $page );
                break;
        }

        // echo '<pre>' . print_r( $this->results, 1 ) . '</pre>';

        // wp_redirect( esc_url_raw( $this->get_next_step_link() ) );
        // exit;
    }
}
