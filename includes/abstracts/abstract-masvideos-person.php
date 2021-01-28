<?php
/**
 * MasVideos person base class.
 *
 * @package MasVideos/Abstracts
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Abstract Person Class
 *
 * The MasVideos person class handles individual person data.
 *
 * @version  1.0.0
 * @package  MasVideos/Abstracts
 */
class MasVideos_Person extends MasVideos_Data {

    /**
     * This is the name of this object type.
     *
     * @var string
     */
    protected $object_type = 'person';

    /**
     * Post type.
     *
     * @var string
     */
    protected $post_type = 'person';

    /**
     * Cache group.
     *
     * @var string
     */
    protected $cache_group = 'persons';

    /**
     * Stores person data.
     *
     * @var array
     */
    protected $data = array(
        'name'                  => '',
        'slug'                  => '',
        'date_created'          => null,
        'date_modified'         => null,
        'status'                => false,
        'featured'              => false,
        'catalog_visibility'    => 'visible',
        'description'           => '',
        'short_description'     => '',
        'parent_id'             => 0,
        'attributes'            => array(),
        'default_attributes'    => array(),
        'movie_cast'            => array(),
        'movie_crew'            => array(),
        'tv_show_cast'          => array(),
        'tv_show_crew'          => array(),
        'menu_order'            => 0,
        'category_ids'          => array(),
        'tag_ids'               => array(),
        'image_id'              => '',
        'gallery_image_ids'     => array(),
        'also_known_as'         => '',
        'place_of_birth'        => '',
        'birthday'              => '',
        'deathday'              => '',
        'imdb_id'               => '',
        'tmdb_id'               => '',
    );

    /**
     * Supported features such as 'ajax_add_to_cart'.
     *
     * @var array
     */
    protected $supports = array();

    /**
     * Get the person if ID is passed, otherwise the person is new and empty.
     * This class should NOT be instantiated, but the masvideos_get_person() function
     * should be used. It is possible, but the masvideos_get_person() is preferred.
     *
     * @param int|MasVideos_Person|object $person Person to init.
     */
    public function __construct( $person = 0 ) {
        parent::__construct( $person );
        if ( is_numeric( $person ) && $person > 0 ) {
            $this->set_id( $person );
        } elseif ( $person instanceof self ) {
            $this->set_id( absint( $person->get_id() ) );
        } elseif ( ! empty( $person->ID ) ) {
            $this->set_id( absint( $person->ID ) );
        } else {
            $this->set_object_read( true );
        }

        $this->data_store = MasVideos_Data_Store::load( 'person' );
        if ( $this->get_id() > 0 ) {
            $this->data_store->read( $this );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Getters
    |--------------------------------------------------------------------------
    |
    | Methods for getting data from the person object.
    */

    /**
     * Get person name.
     *
     * @since 1.0.0
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return string
     */
    public function get_name( $context = 'view' ) {
        return $this->get_prop( 'name', $context );
    }

    /**
     * Get person slug.
     *
     * @since 1.0.0
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return string
     */
    public function get_slug( $context = 'view' ) {
        return $this->get_prop( 'slug', $context );
    }

    /**
     * Get person created date.
     *
     * @since 1.0.0
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return MasVideos_DateTime|NULL object if the date is set or null if there is no date.
     */
    public function get_date_created( $context = 'view' ) {
        return $this->get_prop( 'date_created', $context );
    }

    /**
     * Get person modified date.
     *
     * @since 1.0.0
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return MasVideos_DateTime|NULL object if the date is set or null if there is no date.
     */
    public function get_date_modified( $context = 'view' ) {
        return $this->get_prop( 'date_modified', $context );
    }

    /**
     * Get person status.
     *
     * @since 1.0.0
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return string
     */
    public function get_status( $context = 'view' ) {
        return $this->get_prop( 'status', $context );
    }

    /**
     * If the person is featured.
     *
     * @since 1.0.0
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return boolean
     */
    public function get_featured( $context = 'view' ) {
        return $this->get_prop( 'featured', $context );
    }

    /**
     * Get catalog visibility.
     *
     * @since 1.0.0
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return string
     */
    public function get_catalog_visibility( $context = 'view' ) {
        return $this->get_prop( 'catalog_visibility', $context );
    }

    /**
     * Get person description.
     *
     * @since 1.0.0
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return string
     */
    public function get_description( $context = 'view' ) {
        return $this->get_prop( 'description', $context );
    }

    /**
     * Get person short description.
     *
     * @since 1.0.0
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return string
     */
    public function get_short_description( $context = 'view' ) {
        return $this->get_prop( 'short_description', $context );
    }

    /**
     * Get parent ID.
     *
     * @since 1.0.0
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return int
     */
    public function get_parent_id( $context = 'view' ) {
        return $this->get_prop( 'parent_id', $context );
    }

    /**
     * Returns person attributes.
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return array
     */
    public function get_attributes( $context = 'view' ) {
        return $this->get_prop( 'attributes', $context );
    }

    /**
     * Get default attributes.
     *
     * @since 1.0.0
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return array
     */
    public function get_default_attributes( $context = 'view' ) {
        return $this->get_prop( 'default_attributes', $context );
    }

    /**
     * Returns movie cast.
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return array
     */
    public function get_movie_cast( $context = 'view' ) {
        return $this->get_prop( 'movie_cast', $context );
    }

    /**
     * Returns movie crew.
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return array
     */
    public function get_movie_crew( $context = 'view' ) {
        return $this->get_prop( 'movie_crew', $context );
    }

    /**
     * Returns tv show cast.
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return array
     */
    public function get_tv_show_cast( $context = 'view' ) {
        return $this->get_prop( 'tv_show_cast', $context );
    }

    /**
     * Returns tv show crew.
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return array
     */
    public function get_tv_show_crew( $context = 'view' ) {
        return $this->get_prop( 'tv_show_crew', $context );
    }

    /**
     * Get menu order.
     *
     * @since 1.0.0
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return int
     */
    public function get_menu_order( $context = 'view' ) {
        return $this->get_prop( 'menu_order', $context );
    }

    /**
     * Get category ids.
     *
     * @since 1.0.0
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return array
     */
    public function get_category_ids( $context = 'view' ) {
        return $this->get_prop( 'category_ids', $context );
    }

    /**
     * Get tag ids.
     *
     * @since 1.0.0
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return array
     */
    public function get_tag_ids( $context = 'view' ) {
        return $this->get_prop( 'tag_ids', $context );
    }

    /**
     * Returns the gallery attachment ids.
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return array
     */
    public function get_gallery_image_ids( $context = 'view' ) {
        return $this->get_prop( 'gallery_image_ids', $context );
    }

    /**
     * Get main image ID.
     *
     * @since 1.0.0
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return string
     */
    public function get_image_id( $context = 'view' ) {
        return $this->get_prop( 'image_id', $context );
    }

    /**
     * Get main person also known as.
     *
     * @since 1.0.0
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return string
     */
    public function get_also_known_as( $context = 'view' ) {
        return $this->get_prop( 'also_known_as', $context );
    }

    /**
     * Get main person place of birth.
     *
     * @since 1.0.0
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return string
     */
    public function get_place_of_birth( $context = 'view' ) {
        return $this->get_prop( 'place_of_birth', $context );
    }

    /**
     * Get main person birthday.
     *
     * @since 1.0.0
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return string
     */
    public function get_birthday( $context = 'view' ) {
        return $this->get_prop( 'birthday', $context );
    }

    /**
     * Get main person deathday.
     *
     * @since 1.0.0
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return string
     */
    public function get_deathday( $context = 'view' ) {
        return $this->get_prop( 'deathday', $context );
    }

    /**
     * Get main person imdb id.
     *
     * @since 1.0.0
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return string
     */
    public function get_imdb_id( $context = 'view' ) {
        return $this->get_prop( 'imdb_id', $context );
    }

    /**
     * Get main person tmdb id.
     *
     * @since 1.0.0
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return string
     */
    public function get_tmdb_id( $context = 'view' ) {
        return $this->get_prop( 'tmdb_id', $context );
    }

    /*
    |--------------------------------------------------------------------------
    | Setters
    |--------------------------------------------------------------------------
    |
    | Functions for setting person data. These should not update anything in the
    | database itself and should only change what is stored in the class
    | object.
    */

    /**
     * Set person name.
     *
     * @since 1.0.0
     * @param string $name Person name.
     */
    public function set_name( $name ) {
        $this->set_prop( 'name', $name );
    }

    /**
     * Set person slug.
     *
     * @since 1.0.0
     * @param string $slug Person slug.
     */
    public function set_slug( $slug ) {
        $this->set_prop( 'slug', $slug );
    }

    /**
     * Set person created date.
     *
     * @since 1.0.0
     * @param string|integer|null $date UTC timestamp, or ISO 8601 DateTime. If the DateTime string has no timezone or offset, WordPress site timezone will be assumed. Null if their is no date.
     */
    public function set_date_created( $date = null ) {
        $this->set_date_prop( 'date_created', $date );
    }

    /**
     * Set person modified date.
     *
     * @since 1.0.0
     * @param string|integer|null $date UTC timestamp, or ISO 8601 DateTime. If the DateTime string has no timezone or offset, WordPress site timezone will be assumed. Null if their is no date.
     */
    public function set_date_modified( $date = null ) {
        $this->set_date_prop( 'date_modified', $date );
    }

    /**
     * Set person status.
     *
     * @since 1.0.0
     * @param string $status Person status.
     */
    public function set_status( $status ) {
        $this->set_prop( 'status', $status );
    }

    /**
     * Set if the person is featured.
     *
     * @since 1.0.0
     * @param bool|string $featured Whether the person is featured or not.
     */
    public function set_featured( $featured ) {
        $this->set_prop( 'featured', masvideos_string_to_bool( $featured ) );
    }

    /**
     * Set catalog visibility.
     *
     * @since 1.0.0
     * @throws MasVideos_Data_Exception Throws exception when invalid data is found.
     * @param string $visibility Options: 'hidden', 'visible', 'search' and 'catalog'.
     */
    public function set_catalog_visibility( $visibility ) {
        $options = array_keys( masvideos_get_person_visibility_options() );
        if ( ! in_array( $visibility, $options, true ) ) {
            $this->error( 'person_invalid_catalog_visibility', __( 'Invalid catalog visibility option.', 'masvideos' ) );
        }
        $this->set_prop( 'catalog_visibility', $visibility );
    }

    /**
     * Set person description.
     *
     * @since 1.0.0
     * @param string $description Person description.
     */
    public function set_description( $description ) {
        $this->set_prop( 'description', $description );
    }

    /**
     * Set person short description.
     *
     * @since 1.0.0
     * @param string $short_description Person short description.
     */
    public function set_short_description( $short_description ) {
        $this->set_prop( 'short_description', $short_description );
    }

    /**
     * Set parent ID.
     *
     * @since 1.0.0
     * @param int $parent_id Person parent ID.
     */
    public function set_parent_id( $parent_id ) {
        $this->set_prop( 'parent_id', absint( $parent_id ) );
    }

    /**
     * Set person attributes.
     *
     * Attributes are made up of:
     *     id - 0 for person level attributes. ID for global attributes.
     *     name - Attribute name.
     *     options - attribute value or array of term ids/names.
     *     position - integer sort order.
     *     visible - If visible on frontend.
     *     variation - If used for variations.
     * Indexed by unqiue key to allow clearing old ones after a set.
     *
     * @since 1.0.0
     * @param array $raw_attributes Array of MasVideos_Person_Attribute objects.
     */
    public function set_attributes( $raw_attributes ) {
        $attributes = array_fill_keys( array_keys( $this->get_attributes( 'edit' ) ), null );
        foreach ( $raw_attributes as $attribute ) {
            if ( is_a( $attribute, 'MasVideos_Person_Attribute' ) ) {
                $attributes[ sanitize_title( $attribute->get_name() ) ] = $attribute;
            }
        }

        uasort( $attributes, 'masvideos_attribute_uasort_comparison' );
        $this->set_prop( 'attributes', $attributes );
    }

    /**
     * Set default attributes. These will be saved as strings and should map to attribute values.
     *
     * @since 1.0.0
     * @param array $default_attributes List of default attributes.
     */
    public function set_default_attributes( $default_attributes ) {
        $this->set_prop( 'default_attributes', array_map( 'strval', array_filter( (array) $default_attributes, 'masvideos_array_filter_default_attributes' ) ) );
    }

    /**
     * Set cast. These will be saved as strings and should map to source values.
     *
     * @since 1.0.0
     * @param array $cast List of cast.
     */
    public function set_movie_cast( $cast ) {
        $this->set_prop( 'movie_cast', $cast );
    }

    /**
     * Set crew. These will be saved as strings and should map to source values.
     *
     * @since 1.0.0
     * @param array $crew List of crew.
     */
    public function set_movie_crew( $crew ) {
        $this->set_prop( 'movie_crew', $crew );
    }

    /**
     * Set cast. These will be saved as strings and should map to source values.
     *
     * @since 1.0.0
     * @param array $cast List of cast.
     */
    public function set_tv_show_cast( $cast ) {
        $this->set_prop( 'tv_show_cast', $cast );
    }

    /**
     * Set crew. These will be saved as strings and should map to source values.
     *
     * @since 1.0.0
     * @param array $crew List of crew.
     */
    public function set_tv_show_crew( $crew ) {
        $this->set_prop( 'tv_show_crew', $crew );
    }

    /**
     * Set menu order.
     *
     * @since 1.0.0
     * @param int $menu_order Menu order.
     */
    public function set_menu_order( $menu_order ) {
        $this->set_prop( 'menu_order', intval( $menu_order ) );
    }

    /**
     * Set the person categorys.
     *
     * @since 1.0.0
     * @param array $term_ids List of terms IDs.
     */
    public function set_category_ids( $term_ids ) {
        $this->set_prop( 'category_ids', array_unique( array_map( 'intval', $term_ids ) ) );
    }

    /**
     * Set the person tags.
     *
     * @since 1.0.0
     * @param array $term_ids List of terms IDs.
     */
    public function set_tag_ids( $term_ids ) {
        $this->set_prop( 'tag_ids', array_unique( array_map( 'intval', $term_ids ) ) );
    }

    /**
     * Set gallery attachment ids.
     *
     * @since 1.0.0
     * @param array $image_ids List of image ids.
     */
    public function set_gallery_image_ids( $image_ids ) {
        $image_ids = wp_parse_id_list( $image_ids );

        if ( $this->get_object_read() ) {
            $image_ids = array_filter( $image_ids, 'wp_attachment_is_image' );
        }

        $this->set_prop( 'gallery_image_ids', $image_ids );
    }

    /**
     * Set main image ID.
     *
     * @since 1.0.0
     * @param int|string $image_id Person image id.
     */
    public function set_image_id( $image_id = '' ) {
        $this->set_prop( 'image_id', $image_id );
    }

    /**
     * Set main person also known as content.
     *
     * @since 1.0.0
     * @param int|string $also_known_as Person also known as.
     */
    public function set_also_known_as( $also_known_as = '' ) {
        $this->set_prop( 'also_known_as', $also_known_as );
    }

    /**
     * Set main person place of birth content.
     *
     * @since 1.0.0
     * @param int|string $place_of_birth Person place of birth.
     */
    public function set_place_of_birth( $place_of_birth = '' ) {
        $this->set_prop( 'place_of_birth', $place_of_birth );
    }

    /**
     * Set main person birthday content.
     *
     * @since 1.0.0
     * @param int|string $birthday Person birthday.
     */
    public function set_birthday( $birthday = '' ) {
        $this->set_date_prop( 'birthday', $birthday );
    }

    /**
     * Set main person deathday content.
     *
     * @since 1.0.0
     * @param int|string $deathday Person deathday.
     */
    public function set_deathday( $deathday = '' ) {
        $this->set_date_prop( 'deathday', $deathday );
    }

    /**
     * Set main person imdb id content.
     *
     * @since 1.0.0
     * @param int|string $imdb_id Person imdb id.
     */
    public function set_imdb_id( $imdb_id = '' ) {
        $imdb_id = (string) $imdb_id;
        if ( $this->get_object_read() && ! empty( $imdb_id ) && ! masvideos_person_has_unique_imdb_id( $this->get_id(), $imdb_id ) ) {
            $imdb_id_found = masvideos_get_person_id_by_imdb_id( $imdb_id );

            $this->error( 'person_invalid_imdb_id', __( 'Invalid or duplicated IMDB Id.', 'masvideos' ), 400, array( 'resource_id' => $imdb_id_found ) );
        }
        $this->set_prop( 'imdb_id', $imdb_id );
    }

    /**
     * Set main person tmdb id content.
     *
     * @since 1.0.0
     * @param int|string $tmdb_id Person tmdb id.
     */
    public function set_tmdb_id( $tmdb_id = '' ) {
        $tmdb_id = (string) $tmdb_id;
        if ( $this->get_object_read() && ! empty( $tmdb_id ) && ! masvideos_person_has_unique_tmdb_id( $this->get_id(), $tmdb_id ) ) {
            $tmdb_id_found = masvideos_get_person_id_by_tmdb_id( $tmdb_id );

            $this->error( 'person_invalid_tmdb_id', __( 'Invalid or duplicated TMDB Id.', 'masvideos' ), 400, array( 'resource_id' => $tmdb_id_found ) );
        }
        $this->set_prop( 'tmdb_id', $tmdb_id );
    }

    /*
    |--------------------------------------------------------------------------
    | Other Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Ensure properties are set correctly before save.
     *
     * @since 1.0.0
     */
    public function validate_props() {
    }

    /**
     * Save data (either create or update depending on if we are working on an existing person).
     *
     * @since 1.0.0
     * @return int
     */
    public function save() {
        $this->validate_props();

        if ( $this->data_store ) {
            // Trigger action before saving to the DB. Use a pointer to adjust object props before save.
            do_action( 'masvideos_before_' . $this->object_type . '_object_save', $this, $this->data_store );

            if ( $this->get_id() ) {
                $this->data_store->update( $this );
            } else {
                $this->data_store->create( $this );
            }
            if ( $this->get_parent_id() ) {
                masvideos_deferred_person_sync( $this->get_parent_id() );
            }
        }
        return $this->get_id();
    }

    /*
    |--------------------------------------------------------------------------
    | Conditionals
    |--------------------------------------------------------------------------
    */

    /**
     * Check if a person supports a given feature.
     *
     * Person classes should override this to declare support (or lack of support) for a feature.
     *
     * @param string $feature string The name of a feature to test support for.
     * @return bool True if the person supports the feature, false otherwise.
     * @since 2.5.0
     */
    public function supports( $feature ) {
        return apply_filters( 'masvideos_person_supports', in_array( $feature, $this->supports ), $feature, $this );
    }

    /**
     * Returns whether or not the person post exists.
     *
     * @return bool
     */
    public function exists() {
        return false !== $this->get_status();
    }

    /**
     * Returns whether or not the person is featured.
     *
     * @return bool
     */
    public function is_featured() {
        return true === $this->get_featured();
    }

    /**
     * Returns whether or not the person is visible in the catalog.
     *
     * @return bool
     */
    public function is_visible() {
        $visible = 'visible' === $this->get_catalog_visibility() || ( is_search() && 'search' === $this->get_catalog_visibility() ) || ( ! is_search() && 'catalog' === $this->get_catalog_visibility() );

        if ( 'trash' === $this->get_status() ) {
            $visible = false;
        } elseif ( 'publish' !== $this->get_status() && ! current_user_can( 'edit_post', $this->get_id() ) ) {
            $visible = false;
        }

        if ( $this->get_parent_id() ) {
            $parent_person = masvideos_get_person( $this->get_parent_id() );

            if ( $parent_person && 'publish' !== $parent_person->get_status() ) {
                $visible = false;
            }
        }

        return apply_filters( 'masvideos_person_is_visible', $visible, $this->get_id() );
    }

    /**
     * Returns whether or not the person has any visible attributes.
     *
     * @return boolean
     */
    public function has_attributes() {
        foreach ( $this->get_attributes() as $attribute ) {
            if ( $attribute->get_visible() ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns whether or not the person has any child person.
     *
     * @return bool
     */
    public function has_child() {
        return 0 < count( $this->get_children() );
    }

    /*
    |--------------------------------------------------------------------------
    | Non-CRUD Getters
    |--------------------------------------------------------------------------
    */

    /**
     * Get the person's title. For persons this is the person name.
     *
     * @return string
     */
    public function get_title() {
        return apply_filters( 'masvideos_person_title', $this->get_name(), $this );
    }

    /**
     * Person permalink.
     *
     * @return string
     */
    public function get_permalink() {
        return get_permalink( $this->get_id() );
    }

    /**
     * Returns the children IDs if applicable. Overridden by child classes.
     *
     * @return array of IDs
     */
    public function get_children() {
        return array();
    }

    /**
     * Returns the main person image.
     *
     * @param string $size (default: 'masvideos_thumbnail').
     * @param array  $attr Image attributes.
     * @param bool   $placeholder True to return $placeholder if no image is found, or false to return an empty string.
     * @return string
     */
    public function get_image( $size = 'masvideos_thumbnail', $attr = array(), $placeholder = true ) {
        if ( $this->get_image_id() ) {
            $image = wp_get_attachment_image( $this->get_image_id(), $size, false, $attr );
        } elseif ( $this->get_parent_id() ) {
            $parent_person = masvideos_get_person( $this->get_parent_id() );
            $image          = $parent_person->get_image();
        } elseif ( $placeholder ) {
            $image = masvideos_placeholder_img( $size );
        } else {
            $image = '';
        }

        return apply_filters( 'masvideos_person_get_image', $image, $this, $size, $attr, $placeholder, $image );
    }

    /**
     * Returns a single person attribute as a string.
     *
     * @param  string $attribute to get.
     * @return string
     */
    public function get_attribute( $attribute ) {
        $attributes = $this->get_attributes();
        $attribute  = sanitize_title( $attribute );

        if ( isset( $attributes[ $attribute ] ) ) {
            $attribute_object = $attributes[ $attribute ];
        } elseif ( isset( $attributes[ 'person_' . $attribute ] ) ) {
            $attribute_object = $attributes[ 'person_' . $attribute ];
        } else {
            return '';
        }
        return $attribute_object->is_taxonomy() ? implode( ', ', masvideos_get_person_terms( $this->get_id(), $attribute_object->get_name(), array( 'fields' => 'names' ) ) ) : masvideos_implode_text_attributes( $attribute_object->get_options() );
    }
}
