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
        add_action( 'init', array( __CLASS__, 'register_taxonomies' ), 5 );
        add_action( 'init', array( __CLASS__, 'register_post_types' ), 5 );
        add_action( 'init', array( __CLASS__, 'support_jetpack_omnisearch' ) );
        add_filter( 'rest_api_allowed_post_types', array( __CLASS__, 'rest_api_allowed_post_types' ) );
        add_action( 'masvideos_after_register_post_type', array( __CLASS__, 'maybe_flush_rewrite_rules' ) );
        add_action( 'masvideos_flush_rewrite_rules', array( __CLASS__, 'flush_rewrite_rules' ) );
        add_filter( 'gutenberg_can_edit_post_type', array( __CLASS__, 'gutenberg_can_edit_post_type' ), 10, 2 );
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
                        'slug'         => $permalinks['category_rewrite_slug'],
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
                        'slug'       => $permalinks['tag_rewrite_slug'],
                        'with_front' => false,
                    ),
                )
            )
        );

        do_action( 'masvideos_after_register_taxonomy' );
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

        do_action( 'masvideos_after_register_post_type' );
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
