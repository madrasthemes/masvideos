<?php
/**
 * MasVideos TMDB API Configuration.
 *
 * @package MasVideos/Integrations
 */

defined( 'ABSPATH' ) || exit;

class MasVideos_TMDB_API_Configuration {

    //------------------------------------------------------------------------------
    // Class Variables
    //------------------------------------------------------------------------------

    private $_data;

    /**
     *  Construct Class
     *
     *  @param array $data An array with the data of a Configuration
     */
    public function __construct( $data ) {
        $this->_data = $data;
    }

    //------------------------------------------------------------------------------
    // Get Variables
    //------------------------------------------------------------------------------

    /** 
     *  Get the Configuration's base URL for images
     *
     *  @return string
     */
    public function getImageBaseURL() {
        return $this->_data['images']['base_url'];
    }

    /** 
     *  Get the Configuration's secure base URL for images
     *
     *  @return string
     */
    public function getSecureImageBaseURL() {
        return $this->_data['images']['secure_base_url'];
    }

    /** 
     *  Get the Configuration's list of sizes for backdrops
     *
     *  @return string[]
     */
    public function getBackdropSizes() {
        return $this->_data['images']['backdrop_sizes'];
    }

    /** 
     *  Get the Configuration's list of sizes for logos
     *
     *  @return string[]
     */
    public function getLogoSizes() {
        return $this->_data['images']['logo_sizes'];
    }

    /** 
     *  Get the Configuration's list of sizes for posters
     *
     *  @return string[]
     */
    public function getPosterSizes() {
        return $this->_data['images']['poster_sizes'];
    }

    /** 
     *  Get the Configuration's list of sizes for profiles
     *
     *  @return string[]
     */
    public function getProfileSizes() {
        return $this->_data['images']['profile_sizes'];
    }

    /** 
     *  Get the Configuration's list of sizes for stills
     *
     *  @return string[]
     */
    public function getStillSizes() {
        return $this->_data['images']['still_sizes'];
    }

    /**
     *  Get Generic.<br>
     *  Get a item of the array, you should not get used to use this, better use specific get's.
     *
     *  @param string $item The item of the $data array you want
     *  @return array
     */
    public function get( $item = '' ) {
        return ( empty( $item ) ) ? $this->_data : $this->_data[$item];
    }

    //------------------------------------------------------------------------------
    // Export
    //------------------------------------------------------------------------------

    /** 
     *  Get the JSON representation of the Configuration
     *
     *  @return string
     */
    public function getJSON() {
        return json_encode( $this->_data, JSON_PRETTY_PRINT );
    }
}