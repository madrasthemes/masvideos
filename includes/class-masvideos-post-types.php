<?php
/**
 * Post Types
 *
 * Registers post types and taxonomies.
 *
 * @package WooCommerce/Classes/Videos
 * @version 2.5.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Post types Class.
 */
class Mas_Videos_Post_Types {

    /**
     * Hook in methods.
     */
    public static function init() {
        add_action( 'init', array( __CLASS__, 'register_post_types' ), 5 );
        add_action( 'init', array( __CLASS__, 'register_taxonomies' ), 5 );
        add_action( 'init', array( __CLASS__, 'support_jetpack_omnisearch' ) );
        add_filter( 'rest_api_allowed_post_types', array( __CLASS__, 'rest_api_allowed_post_types' ) );
        add_action( 'masvideos_after_register_post_type', array( __CLASS__, 'maybe_flush_rewrite_rules' ) );
        add_action( 'masvideos_flush_rewrite_rules', array( __CLASS__, 'flush_rewrite_rules' ) );
        add_filter( 'gutenberg_can_edit_post_type', array( __CLASS__, 'gutenberg_can_edit_post_type' ), 10, 2 );
    }

    /**
     * Register core post types.
     */
    public static function register_post_types() {

        if ( ! is_blog_installed() || post_type_exists( 'video' ) ) {
            return;
        }

        do_action( 'masvideos_register_post_type' );

        $permalinks = masvideos_get_video_permalink_structure();
        $supports   = array( 'title', 'editor', 'excerpt', 'thumbnail', 'comments', 'custom-fields', 'publicize', 'wpcom-markdown' );

        $videos_page_id = 0;

        // if ( current_theme_supports( 'masvideos' ) ) {
            $has_archive = $videos_page_id && get_post( $videos_page_id ) ? urldecode( get_page_uri( $videos_page_id ) ) : 'videos';
        // } else {
        //     $has_archive = false;
        // }

        // If theme support changes, we may need to flush permalinks since some are changed based on this flag.
        // if ( update_option( 'current_theme_supports_masvideos', current_theme_supports( 'masvideos' ) ? 'yes' : 'no' ) ) {
        //     update_option( 'masvideos_queue_flush_rewrite_rules', 'yes' );
        // }

        register_post_type(
            'video',
            apply_filters(
                'masvideos_register_post_type_video',
                array(
                    'labels'              => array(
                        'name'                  => __( 'Videos', 'masvideos' ),
                        'singular_name'         => __( 'Video', 'masvideos' ),
                        'all_items'             => __( 'All Videos', 'masvideos' ),
                        'menu_name'             => _x( 'Videos', 'Admin menu name', 'masvideos' ),
                        'add_new'               => __( 'Add New', 'masvideos' ),
                        'add_new_item'          => __( 'Add new video', 'masvideos' ),
                        'edit'                  => __( 'Edit', 'masvideos' ),
                        'edit_item'             => __( 'Edit video', 'masvideos' ),
                        'new_item'              => __( 'New video', 'masvideos' ),
                        'view_item'             => __( 'View video', 'masvideos' ),
                        'view_items'            => __( 'View videos', 'masvideos' ),
                        'search_items'          => __( 'Search videos', 'masvideos' ),
                        'not_found'             => __( 'No videos found', 'masvideos' ),
                        'not_found_in_trash'    => __( 'No videos found in trash', 'masvideos' ),
                        'parent'                => __( 'Parent video', 'masvideos' ),
                        'featured_image'        => __( 'Video image', 'masvideos' ),
                        'set_featured_image'    => __( 'Set video image', 'masvideos' ),
                        'remove_featured_image' => __( 'Remove video image', 'masvideos' ),
                        'use_featured_image'    => __( 'Use as video image', 'masvideos' ),
                        'insert_into_item'      => __( 'Insert into video', 'masvideos' ),
                        'uploaded_to_this_item' => __( 'Uploaded to this video', 'masvideos' ),
                        'filter_items_list'     => __( 'Filter videos', 'masvideos' ),
                        'items_list_navigation' => __( 'Videos navigation', 'masvideos' ),
                        'items_list'            => __( 'Videos list', 'masvideos' ),
                    ),
                    'description'         => __( 'This is where you can add new videos to your store.', 'masvideos' ),
                    'public'              => true,
                    'show_ui'             => true,
                    'capability_type'     => 'video',
                    'map_meta_cap'        => true,
                    'publicly_queryable'  => true,
                    'exclude_from_search' => false,
                    'hierarchical'        => false, // Hierarchical causes memory issues - WP loads all records!
                    'rewrite'             => $permalinks['video_rewrite_slug'] ? array(
                        'slug'       => $permalinks['video_rewrite_slug'],
                        'with_front' => false,
                        'feeds'      => true,
                    ) : false,
                    'query_var'           => true,
                    'supports'            => $supports,
                    'has_archive'         => $has_archive,
                    'show_in_nav_menus'   => true,
                    'show_in_rest'        => true,
                )
            )
        );

        register_post_type(
            'movie',
            apply_filters(
                'masvideos_register_post_type_movie',
                array(
                    'labels'              => array(
                        'name'                  => __( 'Movies', 'masvideos' ),
                        'singular_name'         => __( 'Movie', 'masvideos' ),
                        'all_items'             => __( 'All Movies', 'masvideos' ),
                        'menu_name'             => _x( 'Movies', 'Admin menu name', 'masvideos' ),
                        'add_new'               => __( 'Add New', 'masvideos' ),
                        'add_new_item'          => __( 'Add new movie', 'masvideos' ),
                        'edit'                  => __( 'Edit', 'masvideos' ),
                        'edit_item'             => __( 'Edit movie', 'masvideos' ),
                        'new_item'              => __( 'New movie', 'masvideos' ),
                        'view_item'             => __( 'View movie', 'masvideos' ),
                        'view_items'            => __( 'View movies', 'masvideos' ),
                        'search_items'          => __( 'Search movies', 'masvideos' ),
                        'not_found'             => __( 'No movies found', 'masvideos' ),
                        'not_found_in_trash'    => __( 'No movies found in trash', 'masvideos' ),
                        'parent'                => __( 'Parent movie', 'masvideos' ),
                        'featured_image'        => __( 'Movie image', 'masvideos' ),
                        'set_featured_image'    => __( 'Set movie image', 'masvideos' ),
                        'remove_featured_image' => __( 'Remove movie image', 'masvideos' ),
                        'use_featured_image'    => __( 'Use as movie image', 'masvideos' ),
                        'insert_into_item'      => __( 'Insert into movie', 'masvideos' ),
                        'uploaded_to_this_item' => __( 'Uploaded to this movie', 'masvideos' ),
                        'filter_items_list'     => __( 'Filter movies', 'masvideos' ),
                        'items_list_navigation' => __( 'Movies navigation', 'masvideos' ),
                        'items_list'            => __( 'Movies list', 'masvideos' ),
                    ),
                    'description'         => __( 'This is where you can add new movies to your store.', 'masvideos' ),
                    'public'              => true,
                    'show_ui'             => true,
                    'capability_type'     => 'movie',
                    'map_meta_cap'        => true,
                    'publicly_queryable'  => true,
                    'exclude_from_search' => false,
                    'hierarchical'        => false, // Hierarchical causes memory issues - WP loads all records!
                    'rewrite'             => $permalinks['movie_rewrite_slug'] ? array(
                        'slug'       => $permalinks['movie_rewrite_slug'],
                        'with_front' => false,
                        'feeds'      => true,
                    ) : false,
                    'query_var'           => true,
                    'supports'            => $supports,
                    'has_archive'         => $has_archive,
                    'show_in_nav_menus'   => true,
                    'show_in_rest'        => true,
                )
            )
        );

        do_action( 'masvideos_after_register_post_type' );
    }

    /**
     * Register core taxonomies.
     */
    public static function register_taxonomies() {

        if ( ! is_blog_installed() ) {
            return;
        }

        do_action( 'masvideos_register_taxonomy' );

        $permalinks = masvideos_get_video_permalink_structure();

        register_taxonomy(
            'video_cat',
            apply_filters( 'masvideos_taxonomy_objects_video_cat', array( 'video' ) ),
            apply_filters(
                'masvideos_taxonomy_args_video_cat', array(
                    'hierarchical'          => true,
                    'update_count_callback' => '_wc_term_recount',
                    'label'                 => __( 'Categories', 'masvideos' ),
                    'labels'                => array(
                        'name'              => __( 'Video categories', 'masvideos' ),
                        'singular_name'     => __( 'Category', 'masvideos' ),
                        'menu_name'         => _x( 'Categories', 'Admin menu name', 'masvideos' ),
                        'search_items'      => __( 'Search categories', 'masvideos' ),
                        'all_items'         => __( 'All categories', 'masvideos' ),
                        'parent_item'       => __( 'Parent category', 'masvideos' ),
                        'parent_item_colon' => __( 'Parent category:', 'masvideos' ),
                        'edit_item'         => __( 'Edit category', 'masvideos' ),
                        'update_item'       => __( 'Update category', 'masvideos' ),
                        'add_new_item'      => __( 'Add new category', 'masvideos' ),
                        'new_item_name'     => __( 'New category name', 'masvideos' ),
                        'not_found'         => __( 'No categories found', 'masvideos' ),
                    ),
                    'show_ui'               => true,
                    'query_var'             => true,
                    'capabilities'          => array(
                        'manage_terms' => 'manage_video_terms',
                        'edit_terms'   => 'edit_video_terms',
                        'delete_terms' => 'delete_video_terms',
                        'assign_terms' => 'assign_video_terms',
                    ),
                    'rewrite'               => array(
                        'slug'         => $permalinks['video_category_rewrite_slug'],
                        'with_front'   => false,
                        'hierarchical' => true,
                    ),
                )
            )
        );

        register_taxonomy(
            'video_tag',
            apply_filters( 'masvideos_taxonomy_objects_video_tag', array( 'video' ) ),
            apply_filters(
                'masvideos_taxonomy_args_video_tag', array(
                    'hierarchical'          => false,
                    'update_count_callback' => '_wc_term_recount',
                    'label'                 => __( 'Video tags', 'masvideos' ),
                    'labels'                => array(
                        'name'                       => __( 'Video tags', 'masvideos' ),
                        'singular_name'              => __( 'Tag', 'masvideos' ),
                        'menu_name'                  => _x( 'Tags', 'Admin menu name', 'masvideos' ),
                        'search_items'               => __( 'Search tags', 'masvideos' ),
                        'all_items'                  => __( 'All tags', 'masvideos' ),
                        'edit_item'                  => __( 'Edit tag', 'masvideos' ),
                        'update_item'                => __( 'Update tag', 'masvideos' ),
                        'add_new_item'               => __( 'Add new tag', 'masvideos' ),
                        'new_item_name'              => __( 'New tag name', 'masvideos' ),
                        'popular_items'              => __( 'Popular tags', 'masvideos' ),
                        'separate_items_with_commas' => __( 'Separate tags with commas', 'masvideos' ),
                        'add_or_remove_items'        => __( 'Add or remove tags', 'masvideos' ),
                        'choose_from_most_used'      => __( 'Choose from the most used tags', 'masvideos' ),
                        'not_found'                  => __( 'No tags found', 'masvideos' ),
                    ),
                    'show_ui'               => true,
                    'query_var'             => true,
                    'capabilities'          => array(
                        'manage_terms' => 'manage_video_terms',
                        'edit_terms'   => 'edit_video_terms',
                        'delete_terms' => 'delete_video_terms',
                        'assign_terms' => 'assign_video_terms',
                    ),
                    'rewrite'               => array(
                        'slug'       => $permalinks['video_tag_rewrite_slug'],
                        'with_front' => false,
                    ),
                )
            )
        );

        register_taxonomy(
            'movie_cat',
            apply_filters( 'masvideos_taxonomy_objects_movie_cat', array( 'movie' ) ),
            apply_filters(
                'masvideos_taxonomy_args_movie_cat', array(
                    'hierarchical'          => true,
                    'update_count_callback' => '_wc_term_recount',
                    'label'                 => __( 'Categories', 'masvideos' ),
                    'labels'                => array(
                        'name'              => __( 'Movie categories', 'masvideos' ),
                        'singular_name'     => __( 'Category', 'masvideos' ),
                        'menu_name'         => _x( 'Categories', 'Admin menu name', 'masvideos' ),
                        'search_items'      => __( 'Search categories', 'masvideos' ),
                        'all_items'         => __( 'All categories', 'masvideos' ),
                        'parent_item'       => __( 'Parent category', 'masvideos' ),
                        'parent_item_colon' => __( 'Parent category:', 'masvideos' ),
                        'edit_item'         => __( 'Edit category', 'masvideos' ),
                        'update_item'       => __( 'Update category', 'masvideos' ),
                        'add_new_item'      => __( 'Add new category', 'masvideos' ),
                        'new_item_name'     => __( 'New category name', 'masvideos' ),
                        'not_found'         => __( 'No categories found', 'masvideos' ),
                    ),
                    'show_ui'               => true,
                    'query_var'             => true,
                    'capabilities'          => array(
                        'manage_terms' => 'manage_movie_terms',
                        'edit_terms'   => 'edit_movie_terms',
                        'delete_terms' => 'delete_movie_terms',
                        'assign_terms' => 'assign_movie_terms',
                    ),
                    'rewrite'               => array(
                        'slug'         => $permalinks['movie_category_rewrite_slug'],
                        'with_front'   => false,
                        'hierarchical' => true,
                    ),
                )
            )
        );

        register_taxonomy(
            'movie_tag',
            apply_filters( 'masvideos_taxonomy_objects_movie_tag', array( 'movie' ) ),
            apply_filters(
                'masvideos_taxonomy_args_movie_tag', array(
                    'hierarchical'          => false,
                    'update_count_callback' => '_wc_term_recount',
                    'label'                 => __( 'Movie tags', 'masvideos' ),
                    'labels'                => array(
                        'name'                       => __( 'Movie tags', 'masvideos' ),
                        'singular_name'              => __( 'Tag', 'masvideos' ),
                        'menu_name'                  => _x( 'Tags', 'Admin menu name', 'masvideos' ),
                        'search_items'               => __( 'Search tags', 'masvideos' ),
                        'all_items'                  => __( 'All tags', 'masvideos' ),
                        'edit_item'                  => __( 'Edit tag', 'masvideos' ),
                        'update_item'                => __( 'Update tag', 'masvideos' ),
                        'add_new_item'               => __( 'Add new tag', 'masvideos' ),
                        'new_item_name'              => __( 'New tag name', 'masvideos' ),
                        'popular_items'              => __( 'Popular tags', 'masvideos' ),
                        'separate_items_with_commas' => __( 'Separate tags with commas', 'masvideos' ),
                        'add_or_remove_items'        => __( 'Add or remove tags', 'masvideos' ),
                        'choose_from_most_used'      => __( 'Choose from the most used tags', 'masvideos' ),
                        'not_found'                  => __( 'No tags found', 'masvideos' ),
                    ),
                    'show_ui'               => true,
                    'query_var'             => true,
                    'capabilities'          => array(
                        'manage_terms' => 'manage_movie_terms',
                        'edit_terms'   => 'edit_movie_terms',
                        'delete_terms' => 'delete_movie_terms',
                        'assign_terms' => 'assign_movie_terms',
                    ),
                    'rewrite'               => array(
                        'slug'       => $permalinks['movie_tag_rewrite_slug'],
                        'with_front' => false,
                    ),
                )
            )
        );

        global $masvideos_attributes;

        $masvideos_attributes = array();
        $attribute_taxonomies = masvideos_get_attribute_taxonomies();

        if ( $attribute_taxonomies ) {
            foreach ( $attribute_taxonomies as $tax ) {
                $name = masvideos_attribute_taxonomy_name( $tax->post_type, $tax->attribute_name );

                if ( $name ) {
                    $masvideos_attributes[ $tax->post_type ][ $name ] = $tax;

                    $post_type_object = get_post_type_object( $tax->post_type );

                    $tax->attribute_public          = absint( isset( $tax->attribute_public ) ? $tax->attribute_public : 1 );
                    $label                          = ! empty( $tax->attribute_label ) ? $tax->attribute_label : $tax->attribute_name;
                    $taxonomy_data                  = array(
                        'hierarchical'          => false,
                        'update_count_callback' => '_update_post_term_count',
                        'labels'                => array(
                            /* translators: %s: attribute name */
                            'name'              => sprintf( __( '%s %s', 'masvideos' ), $post_type_object->labels->singular_name, $label ),
                            'singular_name'     => $label,
                            /* translators: %s: attribute name */
                            'search_items'      => sprintf( __( 'Search %s', 'masvideos' ), $label ),
                            /* translators: %s: attribute name */
                            'all_items'         => sprintf( __( 'All %s', 'masvideos' ), $label ),
                            /* translators: %s: attribute name */
                            'parent_item'       => sprintf( __( 'Parent %s', 'masvideos' ), $label ),
                            /* translators: %s: attribute name */
                            'parent_item_colon' => sprintf( __( 'Parent %s:', 'masvideos' ), $label ),
                            /* translators: %s: attribute name */
                            'edit_item'         => sprintf( __( 'Edit %s', 'masvideos' ), $label ),
                            /* translators: %s: attribute name */
                            'update_item'       => sprintf( __( 'Update %s', 'masvideos' ), $label ),
                            /* translators: %s: attribute name */
                            'add_new_item'      => sprintf( __( 'Add new %s', 'masvideos' ), $label ),
                            /* translators: %s: attribute name */
                            'new_item_name'     => sprintf( __( 'New %s', 'masvideos' ), $label ),
                            /* translators: %s: attribute name */
                            'not_found'         => sprintf( __( 'No &quot;%s&quot; found', 'masvideos' ), $label ),
                        ),
                        'show_ui'               => true,
                        'show_in_quick_edit'    => false,
                        'show_in_menu'          => false,
                        'meta_box_cb'           => false,
                        'query_var'             => 1 === $tax->attribute_public,
                        'rewrite'               => false,
                        'sort'                  => false,
                        'public'                => 1 === $tax->attribute_public,
                        'show_in_nav_menus'     => 1 === $tax->attribute_public && apply_filters( 'masvideos_{$tax->post_type}_attribute_show_in_nav_menus', false, $name ),
                        'capabilities'          => array(
                            'manage_terms' => "manage_{$tax->post_type}_terms",
                            'edit_terms'   => "edit_{$tax->post_type}_terms",
                            'delete_terms' => "delete_{$tax->post_type}_terms",
                            'assign_terms' => "assign_{$tax->post_type}_terms",
                        ),
                    );

                    if ( 1 === $tax->attribute_public && sanitize_title( $tax->attribute_name ) ) {
                        $taxonomy_data['rewrite'] = array(
                            'slug'         => trailingslashit( $permalinks[ $tax->post_type . '_attribute_rewrite_slug' ] ) . sanitize_title( $tax->attribute_name ),
                            'with_front'   => false,
                            'hierarchical' => true,
                        );
                    }

                    register_taxonomy( $name, apply_filters( "masvideos_{$tax->post_type}_taxonomy_objects_{$name}", array( $tax->post_type ) ), apply_filters( "masvideos_{$tax->post_type}_taxonomy_args_{$name}", $taxonomy_data ) );
                }
            }
        }

        do_action( 'masvideos_after_register_taxonomy' );
    }

    /**
     * Flush rules if the event is queued.
     *
     * @since 3.3.0
     */
    public static function maybe_flush_rewrite_rules() {
        // if ( 'yes' === get_option( 'masvideos_queue_flush_rewrite_rules' ) ) {
            // update_option( 'masvideos_queue_flush_rewrite_rules', 'no' );
            self::flush_rewrite_rules();
        // }
    }

    /**
     * Flush rewrite rules.
     */
    public static function flush_rewrite_rules() {
        flush_rewrite_rules();
    }

    /**
     * Disable Gutenberg for videos.
     *
     * @param bool   $can_edit Whether the post type can be edited or not.
     * @param string $post_type The post type being checked.
     * @return bool
     */
    public static function gutenberg_can_edit_post_type( $can_edit, $post_type ) {
        return 'video' === $post_type ? false : $can_edit;
    }

    /**
     * Add Video Support to Jetpack Omnisearch.
     */
    public static function support_jetpack_omnisearch() {
        if ( class_exists( 'Jetpack_Omnisearch_Posts' ) ) {
            new Jetpack_Omnisearch_Posts( 'video' );
        }
    }

    /**
     * Added video for Jetpack related posts.
     *
     * @param  array $post_types Post types.
     * @return array
     */
    public static function rest_api_allowed_post_types( $post_types ) {
        $post_types[] = 'video';

        return $post_types;
    }
}

Mas_Videos_Post_Types::init();
