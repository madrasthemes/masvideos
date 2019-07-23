<?php
/**
 * Init MasVideos TMDB.
 *
 * @package MasVideos/Integrations
 */

defined( 'ABSPATH' ) || exit;

include_once MASVIDEOS_ABSPATH . 'includes/integrations/tmdb/class-masvideos-tmdb-configuration.php';
include_once MASVIDEOS_ABSPATH . 'includes/integrations/tmdb/class-masvideos-tmdb-api-configuration.php';

class MasVideos_TMDB {

    #@var string url of API TMDB
    private $api_url = 'http://api.themoviedb.org/3/';

    #@var array of config parameters
    private $config;

    #@var array of TMDB config
    private $apiconfiguration;

    /**
     *  Construct Class
     *
     *  @param array $cnf The necessary configuration
     */
    public function __construct( $config = null ) {

        // Set configuration
        $this->setConfig( $config );

        // Load the API configuration
        if ( ! $this->_loadConfig() ) {
            echo __( 'Unable to read configuration, verify that the API key is valid', 'masvideos' );
            exit;
        }
    }

    //------------------------------------------------------------------------------
    // Configuration Parameters
    //------------------------------------------------------------------------------

    /**
     *  Set configuration parameters
     *
     *  @param array $config
     */
    private function setConfig($config) {
        $this->config = new MasVideos_TMDB_Configuration( $config );
    }

    /**
     *  Get the config parameters
     *
     *  @return array $config
     */
    private function getConfig() {
        return $this->config;
    }

    //------------------------------------------------------------------------------
    // API Key
    //------------------------------------------------------------------------------

    /**
     *  Set the API Key
     *
     *  @param string $apikey
     */
    public function setAPIKey($apikey) {
        $this->getConfig()->setAPIKey($apikey);
    }

    //------------------------------------------------------------------------------
    // Language
    //------------------------------------------------------------------------------

    /**
     *  Set the language
     *  By default english
     *
     *  @param string $lang
     */
    public function setLang($lang = 'en') {
        $this->getConfig()->setLang($lang);
    }

    /**
     *  Get the language
     *
     *  @return string
     */
    public function getLang() {
        return $this->getConfig()->getLang();
    }

    //------------------------------------------------------------------------------
    // TimeZone
    //------------------------------------------------------------------------------

    /**
     *  Set the timezone
     *  By default 'Europe/London'
     *
     *  @param string $timezone
     */
    public function setTimeZone($timezone = 'Europe/London') {
        $this->getConfig()->setTimeZone($timezone);
    }

    /**
     *  Get the timezone
     *
     *  @return string
     */
    public function getTimeZone() {
        return $this->getConfig()->getTimeZone();
    }

    //------------------------------------------------------------------------------
    // Adult Content
    //------------------------------------------------------------------------------

    /**
     *  Set adult content flag
     *  By default false
     *
     *  @param boolean $adult
     */
    public function setAdult($adult = false) {
        $this->getConfig()->setAdult($adult);
    }

    /**
     *  Get the adult content flag
     *
     *  @return string
     */
    public function getAdult() {
        return $this->getConfig()->getAdult();
    }

    //------------------------------------------------------------------------------
    // Debug Mode
    //------------------------------------------------------------------------------

    /**
     *  Set debug mode
     *  By default false
     *
     *  @param boolean $debug
     */
    public function setDebug($debug = false) {
        $this->getConfig()->setDebug($debug);
    }

    /**
     *  Get debug status
     *
     *  @return boolean
     */
    public function getDebug() {
        return $this->getConfig()->getDebug();
    }

    //------------------------------------------------------------------------------
    // Config
    //------------------------------------------------------------------------------

    /**
     *  Loads the configuration of the API
     *
     *  @return boolean
     */
    private function _loadConfig() {
        $this->_apiconfiguration = new MasVideos_TMDB_API_Configuration( $this->_call( 'configuration' ) );

        return ! empty( $this->_apiconfiguration );
    }

    /**
     *  Get Configuration of the API (Revisar)
     *
     *  @return Configuration
     */
    public function getAPIConfig() {
        return $this->_apiconfiguration;
    }

    //------------------------------------------------------------------------------
    // Get Variables
    //------------------------------------------------------------------------------

    /**
     *  Get the URL images
     *  You can specify the width, by default original
     *
     *  @param String $size A String like 'w185' where you specify the image width
     *  @return string
     */
    public function getImageURL( $size = 'original' ) {
        return $this->_apiconfiguration->getImageBaseURL().$size;
    }

    //------------------------------------------------------------------------------
    // API Call
    //------------------------------------------------------------------------------

    /**
     *  Makes the call to the API and retrieves the data as a JSON
     *
     *  @param string $action   API specific function name for in the URL
     *  @param string $appendToResponse The extra append of the request
     *  @return string
     */
    private function _call($action, $appendToResponse = '') {
        $append_to_response = is_array( $appendToResponse ) ? implode( ',', $appendToResponse ) : $appendToResponse;

        $url = $this->api_url . $action . '?api_key=' . $this->getConfig()->getAPIKey() . '&language=' . $this->getConfig()->getLang() . '&append_to_response=' . $append_to_response . '&include_adult='. $this->getConfig()->getAdult();

        if ( $this->getConfig()->getDebug() ) {
            echo '<pre><a href="' . $url . '">check request</a></pre>';
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);

        $results = curl_exec($ch);

        curl_close( $ch );

        return (array) json_decode( ( $results ), true );
    }

    //------------------------------------------------------------------------------
    // Get Lists of Discover
    //------------------------------------------------------------------------------

    /**
     *  Discover Movies
     *
     *  @return Movie[]
     */
    public function getDiscoverMovies( $page = 1 ) {

        $movies = array();

        $result = $this->_call( 'discover/movie', '&page=' . $page );

        if( ! empty( $result['results'] ) ) {
            $movies = $result['results'];
        }

        return $movies;
    }

    /**
     *  Discover TVShows
     *
     *  @return TVShow[]
     */
    public function getDiscoverTVShows( $page = 1 ) {

        $tvShows = array();

        $result = $this->_call( 'discover/tv', '&page=' . $page );

        if( ! empty( $result['results'] ) ) {
            $tvShows = $result['results'];
        }

        return $tvShows;
    }

    //------------------------------------------------------------------------------
    // Get Featured Movies
    //------------------------------------------------------------------------------

    /**
     *  Get latest Movie
     *
     *  @return Movie
     */
    public function getLatestMovie() {

        $movie = array();

        $result = $this->_call( 'movie/latest' );

        if( ! empty( $result ) ) {
            $movie = $result;
        }

        return $movie;
    }

    /**
     *  Get Now Playing Movies
     *
     *  @param integer $page
     *  @return Movie[]
     */
    public function getNowPlayingMovies( $page = 1 ) {

        $movies = array();

        $result = $this->_call( 'movie/now_playing', '&page=' . $page );

        if( ! empty( $result['results'] ) ) {
            $movies = $result['results'];
        }

        return $movies;
    }

    /**
     *  Get Popular Movies
     *
     *  @param integer $page
     *  @return Movie[]
     */
    public function getPopularMovies( $page = 1 ) {

        $movies = array();

        $result = $this->_call( 'movie/popular', '&page=' . $page );

        if( ! empty( $result['results'] ) ) {
            $movies = $result['results'];
        }

        return $movies;
    }

    /**
     *  Get Top Rated Movies
     *
     *  @param integer $page
     *  @return Movie[]
     */
    public function getTopRatedMovies( $page = 1 ) {

        $movies = array();

        $result = $this->_call( 'movie/top_rated', '&page=' . $page );

        if( ! empty( $result['results'] ) ) {
            $movies = $result['results'];
        }

        return $movies;
    }

    /**
     *  Get Upcoming Movies
     *
     *  @param integer $page
     *  @return Movie[]
     */
    public function getUpcomingMovies( $page = 1 ) {

        $movies = array();

        $result = $this->_call( 'movie/upcoming', '&page=' . $page );

        if( ! empty( $result['results'] ) ) {
            $movies = $result['results'];
        }

        return $movies;
    }

    //------------------------------------------------------------------------------
    // Get Featured TVShows
    //------------------------------------------------------------------------------

    /**
     *  Get latest TVShow
     *
     *  @return TVShow
     */
    public function getLatestTVShow() {

        $tvShow = array();

        $result = $this->_call( 'tv/latest' );

        if( ! empty( $result ) ) {
            $tvShow = $result;
        }

        return $tvShow;
    }

    /**
     *  Get On The Air TVShows
     *
     *  @param integer $page
     *  @return TVShow[]
     */
    public function getOnTheAirTVShows( $page = 1 ) {

        $tvShows = array();

        $result = $this->_call( 'tv/on_the_air', '&page=' . $page );

        if( ! empty( $result['results'] ) ) {
            $tvShows = $result['results'];
        }

        return $tvShows;
    }

    /**
     *  Get Airing Today TVShows
     *
     *  @param integer $page
     *  @param string $timezone
     *  @return TVShow[]
     */
    public function getAiringTodayTVShows( $page = 1, $timeZone = null ) {
        $timeZone = (isset($timeZone)) ? $timeZone : $this->getConfig()->getTimeZone();
        $tvShows = array();

        $result = $this->_call( 'tv/airing_today', '&page=' . $page );

        if( ! empty( $result['results'] ) ) {
            $tvShows = $result['results'];
        }

        return $tvShows;
    }

    /**
     *  Get Top Rated TVShows
     *
     *  @param integer $page
     *  @return TVShow[]
     */
    public function getTopRatedTVShows( $page = 1 ) {

        $tvShows = array();

        $result = $this->_call( 'tv/top_rated', '&page=' . $page );

        if( ! empty( $result['results'] ) ) {
            $tvShows = $result['results'];
        }

        return $tvShows;
    }

    /**
     *  Get Popular TVShows
     *
     *  @param integer $page
     *  @return TVShow[]
     */
    public function getPopularTVShows( $page = 1 ) {

        $tvShows = array();

        $result = $this->_call( 'tv/popular', '&page=' . $page );

        if( ! empty( $result['results'] ) ) {
            $tvShows = $result['results'];
        }

        return $tvShows;
    }

    //------------------------------------------------------------------------------
    // Get Featured Persons
    //------------------------------------------------------------------------------

    /**
     *  Get latest Person
     *
     *  @return Person
     */
    public function getLatestPerson() {

        $person = array();

        $result = $this->_call( 'person/latest' );

        if( ! empty( $result ) ) {
            $person = $result;
        }

        return $person;
    }

    /**
     *  Get Popular Persons
     *
     *  @return Person[]
     */
    public function getPopularPersons( $page = 1 ) {

        $persons = array();

        $result = $this->_call( 'person/popular', '&page=' . $page );

        if( ! empty( $result['results'] ) ) {
            $persons = $result['results'];
        }

        return $persons;
    }

    //------------------------------------------------------------------------------
    // Get Data Objects
    //------------------------------------------------------------------------------

    /**
     *  Get a Movie
     *
     *  @param int $idMovie The Movie id
     *  @param array $appendToResponse The extra append of the request
     *  @return Movie
     */
    public function getMovie( $idMovie, $appendToResponse = null ) {
        $appendToResponse = ( isset( $appendToResponse ) ) ? $appendToResponse : $this->getConfig()->getAppender( 'movie' );

        $movie = array();

        $result = $this->_call( 'movie/' . $idMovie, $appendToResponse );

        if( ! empty( $result ) ) {
            $movie = $result;
        }

        return $movie;
    }

    /**
     *  Get a TVShow
     *
     *  @param int $idTVShow The TVShow id
     *  @param array $appendToResponse The extra append of the request
     *  @return TVShow
     */
    public function getTVShow( $idTVShow, $appendToResponse = null ) {
        $appendToResponse = ( isset( $appendToResponse ) ) ? $appendToResponse : $this->getConfig()->getAppender( 'tvshow' );

        $tvShow = array();

        $result = $this->_call( 'tv/' . $idTVShow, $appendToResponse );

        if( ! empty( $result ) ) {
            $tvShow = $result;
        }

        return $tvShow;
    }

    /**
     *  Get a Season
     *
     *  @param int $idTVShow The TVShow id
     *  @param int $numSeason The Season number
     *  @param array $appendToResponse The extra append of the request
     *  @return Season
     */
    public function getSeason( $idTVShow, $numSeason, $appendToResponse = null ) {
        $appendToResponse = ( isset( $appendToResponse ) ) ? $appendToResponse : $this->getConfig()->getAppender( 'season' );

        $season = array();

        $result = $this->_call( 'tv/' . $idTVShow . '/season/' . $numSeason, $appendToResponse );

        if( ! empty( $result ) ) {
            $season = $result;
        }

        return $season;
    }

    /**
     *  Get a Episode
     *
     *  @param int $idTVShow The TVShow id
     *  @param int $numSeason The Season number
     *  @param int $numEpisode the Episode number
     *  @param array $appendToResponse The extra append of the request
     *  @return Episode
     */
    public function getEpisode( $idTVShow, $numSeason, $numEpisode, $appendToResponse = null ) {
        $appendToResponse = ( isset( $appendToResponse ) ) ? $appendToResponse : $this->getConfig()->getAppender( 'episode' );

        $episode = array();

        $result = $this->_call( 'tv/' . $idTVShow . '/season/' . $numSeason . '/episode/' . $numEpisode, $appendToResponse );

        if( ! empty( $result ) ) {
            $episode = $result;
        }

        return $episode;
    }

    /**
     *  Get a Person
     *
     *  @param int $idPerson The Person id
     *  @param array $appendToResponse The extra append of the request
     *  @return Person
     */
    public function getPerson( $idPerson, $appendToResponse = null ) {
        $appendToResponse = ( isset( $appendToResponse ) ) ? $appendToResponse : $this->getConfig()->getAppender( 'person' );

        $person = array();

        $result = $this->_call( 'person/' . $idPerson, $appendToResponse );

        if( ! empty( $result ) ) {
            $person = $result;
        }

        return $person;
    }

    /**
     *  Get a Collection
     *
     *  @param int $idCollection The Person id
     *  @param array $appendToResponse The extra append of the request
     *  @return Collection
     */
    public function getCollection( $idCollection, $appendToResponse = null ) {
        $appendToResponse = ( isset( $appendToResponse ) ) ? $appendToResponse : $this->getConfig()->getAppender( 'collection' );

        return $this->_call( 'collection/' . $idCollection, $appendToResponse );
    }

    /**
     *  Get a Company
     *
     *  @param int $idCompany The Person id
     *  @param array $appendToResponse The extra append of the request
     *  @return Company
     */
    public function getCompany($idCompany, $appendToResponse = null) {
        $appendToResponse = (isset($appendToResponse)) ? $appendToResponse : $this->getConfig()->getAppender('company');

        return $this->_call( 'company/' . $idCompany, $appendToResponse );
    }

    //------------------------------------------------------------------------------
    // Searches
    //------------------------------------------------------------------------------

    /**
     *  Multi Search
     *
     *  @param string $searchQuery The query for the search
     *  @return array[]
     */
    public function multiSearch( $searchQuery ) {
        $searchResults = array(
            Movie::MEDIA_TYPE_MOVIE => array(),
            TVShow::MEDIA_TYPE_TV => array(),
            Person::MEDIA_TYPE_PERSON => array(),
        );

        $result = $this->_call('search/multi', '&query=' . urlencode($searchQuery));

        if(!array_key_exists('results', $result)){
            return $searchResults;
        }

        foreach ($result['results'] as $data) {
            if ($data['media_type'] === Movie::MEDIA_TYPE_MOVIE) {
                $searchResults[Movie::MEDIA_TYPE_MOVIE][] = new Movie($data);
            } elseif ($data['media_type']  === TVShow::MEDIA_TYPE_TV) {
                $searchResults[TVShow::MEDIA_TYPE_TV][] = new TvShow($data);
            } elseif ($data['media_type']  === Person::MEDIA_TYPE_PERSON) {
                $searchResults[Person::MEDIA_TYPE_PERSON][] = new Person($data);
            }
        }

        return $searchResults;
    }

    /**
     *  Search Movie
     *
     *  @param string $movieTitle The title of a Movie
     *  @return Movie[]
     */
    public function searchMovie( $movieTitle ) {

        $movies = array();

        $result = $this->_call( 'search/movie', '&query=' . urlencode( $movieTitle ) );

        if( ! empty( $result['results'] ) ) {
            $movies = $result['results'];
        }

        return $movies;
    }

    /**
     *  Search TVShow
     *
     *  @param string $tvShowTitle The title of a TVShow
     *  @return TVShow[]
     */
    public function searchTVShow( $tvShowTitle ) {

        $tvShows = array();

        $result = $this->_call( 'search/tv', '&query=' . urlencode( $tvShowTitle ) );

        if( ! empty( $result['results'] ) ) {
            $tvShows = $result['results'];
        }

        return $tvShows;
    }

    /**
     *  Search Person
     *
     *  @param string $personName The name of the Person
     *  @return Person[]
     */
    public function searchPerson( $personName ) {

        $persons = array();

        $result = $this->_call( 'search/person', '&query=' . urlencode( $personName ) );

        if( ! empty( $result['results'] ) ) {
            $persons = $result['results'];
        }

        return $persons;
    }

    /**
     *  Search Collection
     *
     *  @param string $collectionName The name of the Collection
     *  @return Collection[]
     */
    public function searchCollection($collectionName) {

        $collections = array();

        $result = $this->_call( 'search/collection', '&query=' . urlencode( $collectionName ) );

        if( ! empty( $result['results'] ) ) {
            $collections = $result['results'];
        }

        return $collections;
    }

    /**
     *  Search Company
     *
     *  @param string $companyName The name of the Company
     *  @return Company[]
     */
    public function searchCompany( $companyName ) {

        $companies = array();

        $result = $this->_call( 'search/company', '&query=' . urlencode( $companyName ) );

        if( ! empty( $result['results'] ) ) {
            $companies = $result['results'];
        }

        return $companies;
    }

    //------------------------------------------------------------------------------
    // Find
    //------------------------------------------------------------------------------

    /**
     *  Find
     *
     *  @param string $companyName The name of the Company
     *  @return array
     */
    public function find( $id, $external_source = 'imdb_id' ) {

        $found = array();

        $result = $this->_call( 'find/' . $id, '&external_source=' . urlencode( $external_source ) );

        foreach( $result['movie_results'] as $data ) {
            $found['movies'][] = $data;
        }
        foreach( $result['person_results'] as $data ) {
            $found['persons'][] = $data;
        }
        foreach( $result['tv_results'] as $data ) {
            $found['tvshows'][] = $data;
        }
        foreach( $result['tv_season_results'] as $data ) {
            $found['seasons'][] = $data;
        }
        foreach($result['tv_episode_results'] as $data){
            $found['episodes'][] = $data;
        }

        return $found;
    }

    //------------------------------------------------------------------------------
    // API Extra Info
    //------------------------------------------------------------------------------

    /**
     *  Get Timezones
     *
     *  @return array
     */
    public function getTimezones() {
        return $this->_call( 'timezones/list' );
    }

    /**
     *  Get Jobs
     *
     *  @return array
     */
    public function getJobs() {
        return $this->_call( 'job/list' );
    }

    /**
     *  Get Movie Genres
     *
     *  @return Genre[]
     */
    public function getMovieGenres() {

        $genres = array();

        $result = $this->_call( 'genre/movie/list' );

        if( ! empty( $result['genres'] ) ) {
            $genres = $result['genres'];
        }

        return $genres;
    }

    /**
     *  Get TV Genres
     *
     *  @return Genre[]
     */
    public function getTVGenres() {

        $genres = array();

        $result = $this->_call( 'genre/tv/list' );

        if( ! empty( $result['genres'] ) ) {
            $genres = $result['genres'];
        }

        return $genres;
    }

    //------------------------------------------------------------------------------
    // Genre
    //------------------------------------------------------------------------------

    /**
     *  Get Movies by Genre
     *
     *  @param integer $idGenre
     *  @param integer $page
     *  @return Movie[]
     */
    public function getMoviesByGenre( $idGenre, $page = 1 ) {

        $movies = array();

        $result = $this->_call('genre/'.$idGenre.'/movies', '&page='. $page);

        if( ! empty( $result['results'] ) ) {
            $movies = $result['results'];
        }

        return $movies;
    }
}