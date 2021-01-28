<?php
/**
 * MasVideos video base class.
 *
 * @package MasVideos/Abstracts
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Abstract Video Class
 *
 * The MasVideos video class handles individual video data.
 *
 * @version  1.0.0
 * @package  MasVideos/Abstracts
 */
class MasVideos_Video extends MasVideos_Data {

    /**
     * This is the name of this object type.
     *
     * @var string
     */
    protected $object_type = 'video';

    /**
     * Post type.
     *
     * @var string
     */
    protected $post_type = 'video';

    /**
     * Cache group.
     *
     * @var string
     */
    protected $cache_group = 'videos';

    /**
     * Stores video data.
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
        'reviews_allowed'       => true,
        'attributes'            => array(),
        'default_attributes'    => array(),
        'menu_order'            => 0,
        'category_ids'          => array(),
        'tag_ids'               => array(),
        'image_id'              => '',
        'video_choice'          => '',
        'video_attachment_id'   => '',
        'video_embed_content'   => '',
        'video_url_link'        => '',
        'gallery_image_ids'     => array(),
        'rating_counts'         => array(),
        'average_rating'        => 0,
        'review_count'          => 0,
    );

    /**
     * Supported features such as 'ajax_add_to_cart'.
     *
     * @var array
     */
    protected $supports = array();

    /**
     * Get the video if ID is passed, otherwise the video is new and empty.
     * This class should NOT be instantiated, but the masvideos_get_video() function
     * should be used. It is possible, but the masvideos_get_video() is preferred.
     *
     * @param int|MasVideos_Video|object $video Video to init.
     */
    public function __construct( $video = 0 ) {
        parent::__construct( $video );
        if ( is_numeric( $video ) && $video > 0 ) {
            $this->set_id( $video );
        } elseif ( $video instanceof self ) {
            $this->set_id( absint( $video->get_id() ) );
        } elseif ( ! empty( $video->ID ) ) {
            $this->set_id( absint( $video->ID ) );
        } else {
            $this->set_object_read( true );
        }

        $this->data_store = MasVideos_Data_Store::load( 'video' );
        if ( $this->get_id() > 0 ) {
            $this->data_store->read( $this );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Getters
    |--------------------------------------------------------------------------
    |
    | Methods for getting data from the video object.
    */

    /**
     * Get video name.
     *
     * @since 1.0.0
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return string
     */
    public function get_name( $context = 'view' ) {
        return $this->get_prop( 'name', $context );
    }

    /**
     * Get video slug.
     *
     * @since 1.0.0
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return string
     */
    public function get_slug( $context = 'view' ) {
        return $this->get_prop( 'slug', $context );
    }

    /**
     * Get video created date.
     *
     * @since 1.0.0
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return MasVideos_DateTime|NULL object if the date is set or null if there is no date.
     */
    public function get_date_created( $context = 'view' ) {
        return $this->get_prop( 'date_created', $context );
    }

    /**
     * Get video modified date.
     *
     * @since 1.0.0
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return MasVideos_DateTime|NULL object if the date is set or null if there is no date.
     */
    public function get_date_modified( $context = 'view' ) {
        return $this->get_prop( 'date_modified', $context );
    }

    /**
     * Get video status.
     *
     * @since 1.0.0
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return string
     */
    public function get_status( $context = 'view' ) {
        return $this->get_prop( 'status', $context );
    }

    /**
     * If the video is featured.
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
     * Get video description.
     *
     * @since 1.0.0
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return string
     */
    public function get_description( $context = 'view' ) {
        return $this->get_prop( 'description', $context );
    }

    /**
     * Get video short description.
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
     * Return if reviews is allowed.
     *
     * @since 1.0.0
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return bool
     */
    public function get_reviews_allowed( $context = 'view' ) {
        return $this->get_prop( 'reviews_allowed', $context );
    }

    /**
     * Returns video attributes.
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
     * Get main video choice
     *
     * @since 1.0.0
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return string
     */
    public function get_video_choice( $context = 'view' ) {
        return $this->get_prop( 'video_choice', $context );
    }

    /**
     * Get main video attachment ID.
     *
     * @since 1.0.0
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return string
     */
    public function get_video_attachment_id( $context = 'view' ) {
        return $this->get_prop( 'video_attachment_id', $context );
    }

    /**
     * Get main video embed content.
     *
     * @since 1.0.0
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return string
     */
    public function get_video_embed_content( $context = 'view' ) {
        return $this->get_prop( 'video_embed_content', $context );
    }

    /**
     * Get main video url.
     *
     * @since 1.0.0
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return string
     */
    public function get_video_url_link( $context = 'view' ) {
        return $this->get_prop( 'video_url_link', $context );
    }


    /**
     * Get rating count.
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return array of counts
     */
    public function get_rating_counts( $context = 'view' ) {
        return $this->get_prop( 'rating_counts', $context );
    }

    /**
     * Get average rating.
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return float
     */
    public function get_average_rating( $context = 'view' ) {
        return $this->get_prop( 'average_rating', $context );
    }

    /**
     * Get review count.
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return int
     */
    public function get_review_count( $context = 'view' ) {
        return $this->get_prop( 'review_count', $context );
    }

    /*
    |--------------------------------------------------------------------------
    | Setters
    |--------------------------------------------------------------------------
    |
    | Functions for setting video data. These should not update anything in the
    | database itself and should only change what is stored in the class
    | object.
    */

    /**
     * Set video name.
     *
     * @since 1.0.0
     * @param string $name Video name.
     */
    public function set_name( $name ) {
        $this->set_prop( 'name', $name );
    }

    /**
     * Set video slug.
     *
     * @since 1.0.0
     * @param string $slug Video slug.
     */
    public function set_slug( $slug ) {
        $this->set_prop( 'slug', $slug );
    }

    /**
     * Set video created date.
     *
     * @since 1.0.0
     * @param string|integer|null $date UTC timestamp, or ISO 8601 DateTime. If the DateTime string has no timezone or offset, WordPress site timezone will be assumed. Null if their is no date.
     */
    public function set_date_created( $date = null ) {
        $this->set_date_prop( 'date_created', $date );
    }

    /**
     * Set video modified date.
     *
     * @since 1.0.0
     * @param string|integer|null $date UTC timestamp, or ISO 8601 DateTime. If the DateTime string has no timezone or offset, WordPress site timezone will be assumed. Null if their is no date.
     */
    public function set_date_modified( $date = null ) {
        $this->set_date_prop( 'date_modified', $date );
    }

    /**
     * Set video status.
     *
     * @since 1.0.0
     * @param string $status Video status.
     */
    public function set_status( $status ) {
        $this->set_prop( 'status', $status );
    }

    /**
     * Set if the video is featured.
     *
     * @since 1.0.0
     * @param bool|string $featured Whether the video is featured or not.
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
        $options = array_keys( masvideos_get_video_visibility_options() );
        if ( ! in_array( $visibility, $options, true ) ) {
            $this->error( 'video_invalid_catalog_visibility', __( 'Invalid catalog visibility option.', 'masvideos' ) );
        }
        $this->set_prop( 'catalog_visibility', $visibility );
    }

    /**
     * Set video description.
     *
     * @since 1.0.0
     * @param string $description Video description.
     */
    public function set_description( $description ) {
        $this->set_prop( 'description', $description );
    }

    /**
     * Set video short description.
     *
     * @since 1.0.0
     * @param string $short_description Video short description.
     */
    public function set_short_description( $short_description ) {
        $this->set_prop( 'short_description', $short_description );
    }

    /**
     * Set parent ID.
     *
     * @since 1.0.0
     * @param int $parent_id Video parent ID.
     */
    public function set_parent_id( $parent_id ) {
        $this->set_prop( 'parent_id', absint( $parent_id ) );
    }

    /**
     * Set if reviews is allowed.
     *
     * @since 1.0.0
     * @param bool $reviews_allowed Reviews allowed or not.
     */
    public function set_reviews_allowed( $reviews_allowed ) {
        $this->set_prop( 'reviews_allowed', masvideos_string_to_bool( $reviews_allowed ) );
    }

    /**
     * Set video attributes.
     *
     * Attributes are made up of:
     *     id - 0 for video level attributes. ID for global attributes.
     *     name - Attribute name.
     *     options - attribute value or array of term ids/names.
     *     position - integer sort order.
     *     visible - If visible on frontend.
     *     variation - If used for variations.
     * Indexed by unqiue key to allow clearing old ones after a set.
     *
     * @since 1.0.0
     * @param array $raw_attributes Array of MasVideos_Video_Attribute objects.
     */
    public function set_attributes( $raw_attributes ) {
        $attributes = array_fill_keys( array_keys( $this->get_attributes( 'edit' ) ), null );
        foreach ( $raw_attributes as $attribute ) {
            if ( is_a( $attribute, 'MasVideos_Video_Attribute' ) ) {
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
     * Set menu order.
     *
     * @since 1.0.0
     * @param int $menu_order Menu order.
     */
    public function set_menu_order( $menu_order ) {
        $this->set_prop( 'menu_order', intval( $menu_order ) );
    }

    /**
     * Set the video categories.
     *
     * @since 1.0.0
     * @param array $term_ids List of terms IDs.
     */
    public function set_category_ids( $term_ids ) {
        $this->set_prop( 'category_ids', array_unique( array_map( 'intval', $term_ids ) ) );
    }

    /**
     * Set the video tags.
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

        $this->set_prop( 'gallery_image_ids', $image_ids );
    }

    /**
     * Set main image ID.
     *
     * @since 1.0.0
     * @param int|string $image_id Video image id.
     */
    public function set_image_id( $image_id = '' ) {
        $this->set_prop( 'image_id', $image_id );
    }

    /**
     * Set main video choice
     *
     * @since 1.0.0
     * @param int|string $video_choice Video attachment id.
     */
    public function set_video_choice( $video_choice = '' ) {
        $this->set_prop( 'video_choice', $video_choice );
    }

    /**
     * Set main video attachment ID.
     *
     * @since 1.0.0
     * @param int|string $video_attachment_id Video attachment id.
     */
    public function set_video_attachment_id( $video_attachment_id = '' ) {
        $this->set_prop( 'video_attachment_id', $video_attachment_id );
    }

    /**
     * Set main video embed content.
     *
     * @since 1.0.0
     * @param int|string $video_embed_content Video embed content.
     */
    public function set_video_embed_content( $video_embed_content = '' ) {
        $this->set_prop( 'video_embed_content', $video_embed_content );
    }

    /**
     * Set main video url.
     *
     * @since 1.0.0
     * @param int|string $video_url_link Video embed content.
     */
    public function set_video_url_link( $video_url_link = '' ) {
        $this->set_prop( 'video_url_link', $video_url_link );
    }

    /**
     * Set rating counts. Read only.
     *
     * @param array $counts Video rating counts.
     */
    public function set_rating_counts( $counts ) {
        $this->set_prop( 'rating_counts', array_filter( array_map( 'absint', (array) $counts ) ) );
    }

    /**
     * Set average rating. Read only.
     *
     * @param float $average Video average rating.
     */
    public function set_average_rating( $average ) {
        $this->set_prop( 'average_rating', masvideos_format_decimal( $average ) );
    }

    /**
     * Set review count. Read only.
     *
     * @param int $count Video review count.
     */
    public function set_review_count( $count ) {
        $this->set_prop( 'review_count', absint( $count ) );
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
     * Save data (either create or update depending on if we are working on an existing video).
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
                masvideos_deferred_video_sync( $this->get_parent_id() );
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
     * Check if a video supports a given feature.
     *
     * Video classes should override this to declare support (or lack of support) for a feature.
     *
     * @param string $feature string The name of a feature to test support for.
     * @return bool True if the video supports the feature, false otherwise.
     * @since 1.0.0
     */
    public function supports( $feature ) {
        return apply_filters( 'masvideos_video_supports', in_array( $feature, $this->supports ), $feature, $this );
    }

    /**
     * Returns whether or not the video post exists.
     *
     * @return bool
     */
    public function exists() {
        return false !== $this->get_status();
    }

    /**
     * Returns whether or not the video is featured.
     *
     * @return bool
     */
    public function is_featured() {
        return true === $this->get_featured();
    }

    /**
     * Returns whether or not the video is visible in the catalog.
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
            $parent_video = masvideos_get_video( $this->get_parent_id() );

            if ( $parent_video && 'publish' !== $parent_video->get_status() ) {
                $visible = false;
            }
        }

        return apply_filters( 'masvideos_video_is_visible', $visible, $this->get_id() );
    }

    /**
     * Returns whether or not the video has any visible attributes.
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
     * Returns whether or not the video has any child video.
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
     * Get the video's title. For videos this is the video name.
     *
     * @return string
     */
    public function get_title() {
        return apply_filters( 'masvideos_video_title', $this->get_name(), $this );
    }

    /**
     * Video permalink.
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
     * Returns the main video image.
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
            $parent_video = masvideos_get_video( $this->get_parent_id() );
            $image          = $parent_video->get_image();
        } elseif ( $placeholder ) {
            $image = masvideos_placeholder_img( $size );
        } else {
            $image = '';
        }

        return apply_filters( 'masvideos_video_get_image', $image, $this, $size, $attr, $placeholder, $image );
    }

    /**
     * Returns a single video attribute as a string.
     *
     * @param  string $attribute to get.
     * @return string
     */
    public function get_attribute( $attribute ) {
        $attributes = $this->get_attributes();
        $attribute  = sanitize_title( $attribute );

        if ( isset( $attributes[ $attribute ] ) ) {
            $attribute_object = $attributes[ $attribute ];
        } elseif ( isset( $attributes[ 'video_' . $attribute ] ) ) {
            $attribute_object = $attributes[ 'video_' . $attribute ];
        } else {
            return '';
        }
        return $attribute_object->is_taxonomy() ? implode( ', ', masvideos_get_video_terms( $this->get_id(), $attribute_object->get_name(), array( 'fields' => 'names' ) ) ) : masvideos_implode_text_attributes( $attribute_object->get_options() );
    }

    /**
     * Get the total amount (COUNT) of ratings, or just the count for one rating e.g. number of 5 star ratings.
     *
     * @param  int $value Optional. Rating value to get the count for. By default returns the count of all rating values.
     * @return int
     */
    public function get_rating_count( $value = null ) {
        $counts = $this->get_rating_counts();

        if ( is_null( $value ) ) {
            return array_sum( $counts );
        } elseif ( isset( $counts[ $value ] ) ) {
            return absint( $counts[ $value ] );
        } else {
            return 0;
        }
    }
}
