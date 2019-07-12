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

    private $apikey = '';
    private $lang = 'en';
    private $timezone = 'Europe/London';
    private $adult = false;
    private $debug = false;
    private $appender;

    //------------------------------------------------------------------------------
    // Constructor
    //------------------------------------------------------------------------------

    /**
     *  Construct Class
     *
     *  @param array $cnf An array with the configuration data
     */
    public function __construct( $cnf ) {

        $this->setAPIKey( $cnf['apikey'] );
        $this->setLang( $cnf['lang'] );
        $this->setTimeZone( 'timezone' );
        $this->setAdult( $cnf['adult'] );
        $this->setDebug( $cnf['debug'] );

        foreach( $cnf['appender'] as $type => $appender ) {
            $this->setAppender( $appender, $type );
        }
    }

    //------------------------------------------------------------------------------
    // Set Variables
    //------------------------------------------------------------------------------

    /**
     *  Set the API Key
     *
     *  @param string $apikey
     */
    public function setAPIKey( $apikey ) {
        $this->apikey = $apikey;
    }

    /**
     *  Set the language code
     *
     *  @param string $lang
     */
    public function setLang( $lang ) {
        $this->lang = $lang;
    }

    /**
     *  Set the timezone
     *
     *  @param string $timezone
     */
    public function setTimeZone( $timezone ) {
        $this->timezone = $timezone;
    }

    /**
     *  Set the adult flag
     *
     *  @param boolean $adult
     */
    public function setAdult( $adult ) {
        $this->adult = $adult;
    }

    /**
     *  Set the debug flag
     *
     *  @param boolean $debug
     */
    public function setDebug( $debug ) {
        $this->debug = $debug;
    }

    /**
     *  Set an appender for a special type
     *
     *  @param array $appender
     *  @param string $type
     */
    public function setAppender( $appender, $type ) {
        $this->appender[$type] = $appender;
    }

    //------------------------------------------------------------------------------
    // Get Variables
    //------------------------------------------------------------------------------

    /**
     *  Get the API Key
     *
     *  @return string
     */
    public function getAPIKey() {
        return $this->apikey;
    }

    /**
     *  Get the language code
     *
     *  @return string
     */
    public function getLang() {
        return $this->lang;
    }

    /**
     *  Get the timezone
     *
     *  @return string
     */
    public function getTimeZone() {
        return $this->timezone;
    }

    /**
     *  Get the adult string
     *
     *  @return string
     */
    public function getAdult() {
        return ($this->adult) ? 'true' : 'false';
    }

    /**
     *  Get the debug flag
     *
     *  @return boolean
     */
    public function getDebug() {
        return $this->debug;
    }

    /**
     *  Get the appender array for a type
     *
     *  @return array
     */
    public function getAppender( $type ) {
        return $this->appender[$type];
    }
}