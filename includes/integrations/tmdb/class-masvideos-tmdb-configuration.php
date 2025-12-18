<?php
/**
 * MasVideos TMDB Configuration.
 *
 * @package MasVideos/Integrations
 */

defined( 'ABSPATH' ) || exit;

class MasVideos_TMDB_Configuration {

    //------------------------------------------------------------------------------
    // Class Variables
    //------------------------------------------------------------------------------

    private $apikey   = '';
    private $lang     = 'en';
    private $timezone = 'Europe/London';
    private $adult    = false;
    private $debug    = false;
    private $appender = array();

    //------------------------------------------------------------------------------
    // Constructor
    //------------------------------------------------------------------------------

    /**
     * Construct Class
     *
     * @param array $cnf Configuration array
     */
    public function __construct( $cnf = array() ) {

        $cnf = is_array( $cnf ) ? $cnf : array();

        $this->setAPIKey( $cnf['apikey']   ?? '' );
        $this->setLang(   $cnf['lang']     ?? 'en' );
        $this->setTimeZone( $cnf['timezone'] ?? 'Europe/London' );
        $this->setAdult(  $cnf['adult']    ?? false );
        $this->setDebug(  $cnf['debug']    ?? false );

        if ( ! empty( $cnf['appender'] ) && is_array( $cnf['appender'] ) ) {
            foreach ( $cnf['appender'] as $type => $appender ) {
                if ( is_array( $appender ) ) {
                    $this->setAppender( $appender, $type );
                }
            }
        }
    }

    //------------------------------------------------------------------------------
    // Setters
    //------------------------------------------------------------------------------

    public function setAPIKey( $apikey ) {
        $this->apikey = (string) $apikey;
    }

    public function setLang( $lang ) {
        $this->lang = (string) $lang;
    }

    public function setTimeZone( $timezone ) {
        $this->timezone = (string) $timezone;
    }

    public function setAdult( $adult ) {
        $this->adult = (bool) $adult;
    }

    public function setDebug( $debug ) {
        $this->debug = (bool) $debug;
    }

    public function setAppender( $appender, $type ) {
        if ( ! is_array( $this->appender ) ) {
            $this->appender = array();
        }

        $this->appender[ $type ] = $appender;
    }

    //------------------------------------------------------------------------------
    // Getters
    //------------------------------------------------------------------------------

    public function getAPIKey() {
        return $this->apikey;
    }

    public function getLang() {
        return $this->lang;
    }

    public function getTimeZone() {
        return $this->timezone;
    }

    public function getAdult() {
        return $this->adult ? 'true' : 'false';
    }

    public function getDebug() {
        return $this->debug;
    }

    public function getAppender( $type ) {
        return $this->appender[ $type ] ?? array();
    }
}
