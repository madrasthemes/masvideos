<?php
/**
 * MasVideos Data Store.
 *
 * @package MasVideos\Classes
 * @since   1.0.0
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Data store class.
 */
class MasVideos_Data_Store {

    /**
     * Contains an instance of the data store class that we are working with.
     *
     * @var MasVideos_Data_Store
     */
    private $instance = null;

    /**
     * Contains an array of default MasVideos_Data supported data stores.
     * Format of object name => class name.
     * Example: 'movie' => 'MasVideos_Movie_Data_Store_CPT'
     * You can also pass something like movie_<type> for movie stores and
     * that type will be used first when available, if a store is requested like
     * this and doesn't exist, then the store would fall back to 'movie'.
     * Ran through `masvideos_data_stores`.
     *
     * @var array
     */
    private $stores = array(
        'episode'             => 'MasVideos_Episode_Data_Store_CPT',
        'tv_show'             => 'MasVideos_TV_Show_Data_Store_CPT',
        'tv_show_playlist'    => 'MasVideos_TV_Show_Playlist_Data_Store_CPT',
        'video'               => 'MasVideos_Video_Data_Store_CPT',
        'video_playlist'      => 'MasVideos_Video_Playlist_Data_Store_CPT',
        'movie'               => 'MasVideos_Movie_Data_Store_CPT',
        'movie_playlist'      => 'MasVideos_Movie_Playlist_Data_Store_CPT',
        'person'              => 'MasVideos_Person_Data_Store_CPT',
    );

    /**
     * Contains the name of the current data store's class name.
     *
     * @var string
     */
    private $current_class_name = '';

    /**
     * The object type this store works with.
     *
     * @var string
     */
    private $object_type = '';


    /**
     * Tells MasVideos_Data_Store which object (coupon, movie, order, etc)
     * store we want to work with.
     *
     * @throws Exception When validation fails.
     * @param string $object_type Name of object.
     */
    public function __construct( $object_type ) {
        $this->object_type = $object_type;
        $this->stores      = apply_filters( 'masvideos_data_stores', $this->stores );

        // If this object type can't be found, check to see if we can load one
        // level up (so if movie-type isn't found, we try movie).
        if ( ! array_key_exists( $object_type, $this->stores ) ) {
            $pieces      = explode( '-', $object_type );
            $object_type = $pieces[0];
        }

        if ( array_key_exists( $object_type, $this->stores ) ) {
            $store = apply_filters( 'masvideos_' . $object_type . '_data_store', $this->stores[ $object_type ] );
            if ( is_object( $store ) ) {
                if ( ! $store instanceof WC_Object_Data_Store_Interface ) {
                    throw new Exception( __( 'Invalid data store.', 'masvideos' ) );
                }
                $this->current_class_name = get_class( $store );
                $this->instance           = $store;
            } else {
                if ( ! class_exists( $store ) ) {
                    throw new Exception( __( 'Invalid data store.', 'masvideos' ) );
                }
                $this->current_class_name = $store;
                $this->instance           = new $store();
            }
        } else {
            throw new Exception( __( 'Invalid data store.', 'masvideos' ) );
        }
    }

    /**
     * Only store the object type to avoid serializing the data store instance.
     *
     * @return array
     */
    public function __sleep() {
        return array( 'object_type' );
    }

    /**
     * Re-run the constructor with the object type.
     *
     * @throws Exception When validation fails.
     */
    public function __wakeup() {
        $this->__construct( $this->object_type );
    }

    /**
     * Loads a data store.
     *
     * @param string $object_type Name of object.
     *
     * @since 1.0.0
     * @throws Exception When validation fails.
     * @return MasVideos_Data_Store
     */
    public static function load( $object_type ) {
        return new MasVideos_Data_Store( $object_type );
    }

    /**
     * Returns the class name of the current data store.
     *
     * @since 1.0.0
     * @return string
     */
    public function get_current_class_name() {
        return $this->current_class_name;
    }

    /**
     * Reads an object from the data store.
     *
     * @since 1.0.0
     * @param MasVideos_Data $data MasVideos data instance.
     */
    public function read( &$data ) {
        $this->instance->read( $data );
    }

    /**
     * Create an object in the data store.
     *
     * @since 1.0.0
     * @param MasVideos_Data $data MasVideos data instance.
     */
    public function create( &$data ) {
        $this->instance->create( $data );
    }

    /**
     * Update an object in the data store.
     *
     * @since 1.0.0
     * @param MasVideos_Data $data MasVideos data instance.
     */
    public function update( &$data ) {
        $this->instance->update( $data );
    }

    /**
     * Delete an object from the data store.
     *
     * @since 1.0.0
     * @param MasVideos_Data $data MasVideos data instance.
     * @param array   $args Array of args to pass to the delete method.
     */
    public function delete( &$data, $args = array() ) {
        $this->instance->delete( $data, $args );
    }

    /**
     * Data stores can define additional functions (for example, coupons have
     * some helper methods for increasing or decreasing usage). This passes
     * through to the instance if that function exists.
     *
     * @since 1.0.0
     * @param string $method     Method.
     * @param mixed  $parameters Parameters.
     * @return mixed
     */
    public function __call( $method, $parameters ) {
        if ( is_callable( array( $this->instance, $method ) ) ) {
            $object = array_shift( $parameters );
            return call_user_func_array( array( $this->instance, $method ), array_merge( array( &$object ), $parameters ) );
        }
    }
}
