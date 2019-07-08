<?php
/**
 * Setup menus in WP admin.
 *
 * @package MasVideos\Admin
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'MasVideos_Admin_Menus', false ) ) {
    return new MasVideos_Admin_Menus();
}

/**
 * MasVideos_Admin_Menus Class.
 */
class MasVideos_Admin_Menus {

    /**
     * Hook in tabs.
     */
    public function __construct() {
        // Add menus.
        add_action( 'admin_menu', array( $this, 'admin_menu' ), 9 );

        add_action( 'admin_head', array( $this, 'menu_highlight' ) );
        add_filter( 'menu_order', array( $this, 'menu_order' ) );
        add_filter( 'custom_menu_order', array( $this, 'custom_menu_order' ) );
        add_filter( 'set-screen-option', array( $this, 'set_screen_option' ), 10, 3 );
    }

    /**
     * Add menu items.
     */
    public function admin_menu() {
        global $menu;

        if ( current_user_can( 'manage_masvideos' ) ) {
            $menu[] = array( '', 'read', 'separator-masvideos', '', 'wp-menu-separator masvideos' ); // WPCS: override ok.
        }

        // add_menu_page( __( 'MasVideos', 'masvideos' ), __( 'MasVideos', 'masvideos' ), 'manage_masvideos', 'masvideos', null, null, '55.5' );

        if ( masvideos_is_episode_archive() ) {
            add_submenu_page( 'edit.php?post_type=episode', __( 'Attributes', 'masvideos' ), __( 'Attributes', 'masvideos' ), 'manage_episode_terms', 'episode_attributes', array( $this, 'attributes_page' ) );
        }

        add_submenu_page( 'edit.php?post_type=tv_show', __( 'Attributes', 'masvideos' ), __( 'Attributes', 'masvideos' ), 'manage_tv_show_terms', 'tv_show_attributes', array( $this, 'attributes_page' ) );
        add_submenu_page( 'edit.php?post_type=person', __( 'Attributes', 'masvideos' ), __( 'Attributes', 'masvideos' ), 'manage_person_terms', 'person_attributes', array( $this, 'attributes_page' ) );
        add_submenu_page( 'edit.php?post_type=video', __( 'Attributes', 'masvideos' ), __( 'Attributes', 'masvideos' ), 'manage_video_terms', 'video_attributes', array( $this, 'attributes_page' ) );
        add_submenu_page( 'edit.php?post_type=movie', __( 'Attributes', 'masvideos' ), __( 'Attributes', 'masvideos' ), 'manage_movie_terms', 'movie_attributes', array( $this, 'attributes_page' ) );
    }

    /**
     * Highlights the correct top level admin menu item for post type add screens.
     */
    public function menu_highlight() {
        global $parent_file, $submenu_file, $post_type;

        switch ( $post_type ) {
            // case 'masvideos':
            //     $parent_file = 'masvideos'; // WPCS: override ok.
            //     break;
            case 'episode':
                $screen = get_current_screen();
                if ( $screen && taxonomy_is_episode_attribute( $screen->taxonomy ) ) {
                    $submenu_file = 'episode_attributes'; // WPCS: override ok.
                    $parent_file  = 'edit.php?post_type=episode'; // WPCS: override ok.
                }
                break;
            case 'tv_show':
                $screen = get_current_screen();
                if ( $screen && taxonomy_is_tv_show_attribute( $screen->taxonomy ) ) {
                    $submenu_file = 'tv_show_attributes'; // WPCS: override ok.
                    $parent_file  = 'edit.php?post_type=tv_show'; // WPCS: override ok.
                }
                break;
            case 'person':
                $screen = get_current_screen();
                if ( $screen && taxonomy_is_person_attribute( $screen->taxonomy ) ) {
                    $submenu_file = 'person_attributes'; // WPCS: override ok.
                    $parent_file  = 'edit.php?post_type=person'; // WPCS: override ok.
                }
                break;
            case 'video':
                $screen = get_current_screen();
                if ( $screen && taxonomy_is_video_attribute( $screen->taxonomy ) ) {
                    $submenu_file = 'video_attributes'; // WPCS: override ok.
                    $parent_file  = 'edit.php?post_type=video'; // WPCS: override ok.
                }
                break;
            case 'movie':
                $screen = get_current_screen();
                if ( $screen && taxonomy_is_movie_attribute( $screen->taxonomy ) ) {
                    $submenu_file = 'movie_attributes'; // WPCS: override ok.
                    $parent_file  = 'edit.php?post_type=movie'; // WPCS: override ok.
                }
                break;
        }
    }

    /**
     * Reorder the WC menu items in admin.
     *
     * @param int $menu_order Menu order.
     * @return array
     */
    public function menu_order( $menu_order ) {
        // Initialize our custom order array.
        $masvideos_menu_order = array();

        // Get the index of our custom separator.
        $masvideos_separator = array_search( 'separator-masvideos', $menu_order, true );

        // Get index of episode menu.
        // $masvideos_episode = array_search( 'edit.php?post_type=episode', $menu_order, true );
        // Get index of tv_show menu.
        // $masvideos_tv_show = array_search( 'edit.php?post_type=tv_show', $menu_order, true );
        // Get index of video menu.
        $masvideos_video = array_search( 'edit.php?post_type=video', $menu_order, true );
        // Get index of movie menu.
        // $masvideos_movie = array_search( 'edit.php?post_type=movie', $menu_order, true );

        // Loop through menu order and do some rearranging.
        foreach ( $menu_order as $index => $item ) {

            if ( 'masvideos' === $item ) {
                $masvideos_menu_order[] = 'separator-masvideos';
                $masvideos_menu_order[] = $item;
                $masvideos_menu_order[] = 'edit.php?post_type=video';
                unset( $menu_order[ $masvideos_separator ] );
                unset( $menu_order[ $masvideos_video ] );
            } elseif ( ! in_array( $item, array( 'separator-masvideos' ), true ) ) {
                $masvideos_menu_order[] = $item;
            }
        }

        // Return order.
        return $masvideos_menu_order;
    }

    /**
     * Custom menu order.
     *
     * @return bool
     */
    public function custom_menu_order() {
        return current_user_can( 'manage_masvideos' );
    }

    /**
     * Validate screen options on update.
     *
     * @param bool|int $status Screen option value. Default false to skip.
     * @param string   $option The option name.
     * @param int      $value  The number of rows to use.
     */
    public function set_screen_option( $status, $option, $value ) {
        if ( in_array( $option, array( 'masvideos_keys_per_page', 'masvideos_webhooks_per_page' ), true ) ) {
            return $value;
        }

        return $status;
    }

    /**
     * Init the attributes page.
     */
    public function attributes_page() {
        MasVideos_Admin_Attributes::output();
    }
}

return new MasVideos_Admin_Menus();
