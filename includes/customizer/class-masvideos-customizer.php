<?php
/**
 * Adds options to the customizer for WooCommerce.
 *
 * @version 3.3.0
 * @package WooCommerce
 */

defined( 'ABSPATH' ) || exit;

/**
 * MasVideos_Customizer class.
 */
class MasVideos_Customizer {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'customize_register', array( $this, 'add_sections' ) );
    }

    /**
     * Add settings to the customizer.
     *
     * @param WP_Customize_Manager $wp_customize Theme Customizer object.
     */
    public function add_sections( $wp_customize ) {
        $wp_customize->add_panel( 'masvideos', array(
            'priority'       => 200,
            'capability'     => 'manage_masvideos',
            'theme_supports' => '',
            'title'          => esc_html__( 'Masvideos', 'masvideos' ),
        ) );

        $this->add_myaccount_section( $wp_customize );
        $this->add_movies_section( $wp_customize );
        $this->add_videos_section( $wp_customize );
        $this->add_tv_shows_section( $wp_customize );
        $this->add_playlist_section( $wp_customize );
    }

    /**
     * My Account section.
     *
     * @param WP_Customize_Manager $wp_customize Theme Customizer object.
     */
    private function add_myaccount_section( $wp_customize ) {
        $wp_customize->add_section(
            'masvideos_myaccount',
            array(
                'title'    => esc_html__( 'My Account', 'masvideos' ),
                'priority' => 10,
                'panel'    => 'masvideos',
            )
        );

        $wp_customize->add_setting(
            'masvideos_myaccount_page_id',
            array(
                'default'       => '',
                'type'          => 'option',
                'capability'    => 'manage_masvideos',
            )
        );

        $wp_customize->add_control(
            'masvideos_myaccount_page_id',
            array(
                'label'       => esc_html__( 'My Account Page', 'masvideos' ),
                'section'     => 'masvideos_myaccount',
                'settings'    => 'masvideos_myaccount_page_id',
                'type'        => 'select',
                'choices'     => $this->get_all_pages_array(),
            )
        );

        $wp_customize->add_setting(
            'masvideos_registration_generate_username',
            array(
                'default'              => 'no',
                'type'                 => 'option',
                'capability'           => 'manage_masvideos',
                'sanitize_callback'    => 'masvideos_bool_to_string',
                'sanitize_js_callback' => 'masvideos_string_to_bool',
            )
        );

        $wp_customize->add_control(
            'masvideos_registration_generate_username',
            array(
                'label'    => esc_html__( 'Generate Username', 'masvideos' ),
                'section'  => 'masvideos_myaccount',
                'settings' => 'masvideos_registration_generate_username',
                'type'     => 'checkbox',
            )
        );

        $wp_customize->add_setting(
            'masvideos_registration_generate_password',
            array(
                'default'              => 'no',
                'type'                 => 'option',
                'capability'           => 'manage_masvideos',
                'sanitize_callback'    => 'masvideos_bool_to_string',
                'sanitize_js_callback' => 'masvideos_string_to_bool',
            )
        );

        $wp_customize->add_control(
            'masvideos_registration_generate_password',
            array(
                'label'    => esc_html__( 'Generate Password', 'masvideos' ),
                'section'  => 'masvideos_myaccount',
                'settings' => 'masvideos_registration_generate_password',
                'type'     => 'checkbox',
            )
        );
    }

    /**
     * Movies section.
     *
     * @param WP_Customize_Manager $wp_customize Theme Customizer object.
     */
    private function add_movies_section( $wp_customize ) {
        $wp_customize->add_section(
            'masvideos_movies',
            array(
                'title'    => esc_html__( 'Movies', 'masvideos' ),
                'priority' => 20,
                'panel'    => 'masvideos',
            )
        );

        $wp_customize->add_setting(
            'masvideos_movies_page_id',
            array(
                'default'       => '',
                'type'          => 'option',
                'capability'    => 'manage_masvideos',
            )
        );

        $wp_customize->add_control(
            'masvideos_movies_page_id',
            array(
                'label'       => esc_html__( 'Movies Page', 'masvideos' ),
                'section'     => 'masvideos_movies',
                'settings'    => 'masvideos_movies_page_id',
                'type'        => 'select',
                'choices'     => $this->get_all_pages_array(),
            )
        );

        $wp_customize->add_setting(
            'masvideos_movie_review_rating_required',
            array(
                'default'              => 'yes',
                'type'                 => 'option',
                'capability'           => 'manage_masvideos',
                'sanitize_callback'    => 'masvideos_bool_to_string',
                'sanitize_js_callback' => 'masvideos_string_to_bool',
            )
        );

        $wp_customize->add_control(
            'masvideos_movie_review_rating_required',
            array(
                'label'    => esc_html__( 'Enable Movies Review', 'masvideos' ),
                'section'  => 'masvideos_movies',
                'settings' => 'masvideos_movie_review_rating_required',
                'type'     => 'checkbox',
            )
        );
    }
    
    /**
     * Videos section.
     *
     * @param WP_Customize_Manager $wp_customize Theme Customizer object.
     */
    private function add_videos_section( $wp_customize ) {
        $wp_customize->add_section(
            'masvideos_videos',
            array(
                'title'    => esc_html__( 'Videos', 'masvideos' ),
                'priority' => 30,
                'panel'    => 'masvideos',
            )
        );

        $wp_customize->add_setting(
            'masvideos_videos_page_id',
            array(
                'default'       => '',
                'type'          => 'option',
                'capability'    => 'manage_masvideos',
            )
        );

        $wp_customize->add_control(
            'masvideos_videos_page_id',
            array(
                'label'       => esc_html__( 'Videos Page', 'masvideos' ),
                'section'     => 'masvideos_videos',
                'settings'    => 'masvideos_videos_page_id',
                'type'        => 'select',
                'choices'     => $this->get_all_pages_array(),
            )
        );
    }

    /**
     * TV Shows section.
     *
     * @param WP_Customize_Manager $wp_customize Theme Customizer object.
     */
    private function add_tv_shows_section( $wp_customize ) {
        $wp_customize->add_section(
            'masvideos_tv_shows',
            array(
                'title'    => esc_html__( 'TV Shows', 'masvideos' ),
                'priority' => 40,
                'panel'    => 'masvideos',
            )
        );

        $wp_customize->add_setting(
            'masvideos_tv_shows_page_id',
            array(
                'default'       => '',
                'type'          => 'option',
                'capability'    => 'manage_masvideos',
            )
        );

        $wp_customize->add_control(
            'masvideos_tv_shows_page_id',
            array(
                'label'       => esc_html__( 'TV Shows Page', 'masvideos' ),
                'section'     => 'masvideos_tv_shows',
                'settings'    => 'masvideos_tv_shows_page_id',
                'type'        => 'select',
                'choices'     => $this->get_all_pages_array(),
            )
        );

        $wp_customize->add_setting(
            'masvideos_tv_show_review_rating_required',
            array(
                'default'              => 'yes',
                'type'                 => 'option',
                'capability'           => 'manage_masvideos',
                'sanitize_callback'    => 'masvideos_bool_to_string',
                'sanitize_js_callback' => 'masvideos_string_to_bool',
            )
        );

        $wp_customize->add_control(
            'masvideos_tv_show_review_rating_required',
            array(
                'label'    => esc_html__( 'Enable TV Shows Review', 'masvideos' ),
                'section'  => 'masvideos_tv_shows',
                'settings' => 'masvideos_tv_show_review_rating_required',
                'type'     => 'checkbox',
            )
        );
    }

    /**
     * Playlist section.
     *
     * @param WP_Customize_Manager $wp_customize Theme Customizer object.
     */
    private function add_playlist_section( $wp_customize ) {

        $wp_customize->add_section(
            'masvideos_playlists',
            array(
                'title'    => esc_html__( 'Playlist', 'masvideos' ),
                'priority' => 50,
                'panel'    => 'masvideos',
            )
        );

        $wp_customize->add_setting(
            'masvideos_movie_playlists_page_id',
            array(
                'default'       => '',
                'type'          => 'option',
                'capability'    => 'manage_masvideos',
            )
        );

        $wp_customize->add_control(
            'masvideos_movie_playlists_page_id',
            array(
                'label'       => esc_html__( 'Movie Playlist Page', 'masvideos' ),
                'section'     => 'masvideos_playlists',
                'settings'    => 'masvideos_movie_playlists_page_id',
                'type'        => 'select',
                'choices'     => $this->get_all_pages_array(),
            )
        );

        $wp_customize->add_setting(
            'masvideos_video_playlists_page_id',
            array(
                'default'       => '',
                'type'          => 'option',
                'capability'    => 'manage_masvideos',
            )
        );

        $wp_customize->add_control(
            'masvideos_video_playlists_page_id',
            array(
                'label'       => esc_html__( 'Video Playlist Page', 'masvideos' ),
                'section'     => 'masvideos_playlists',
                'settings'    => 'masvideos_video_playlists_page_id',
                'type'        => 'select',
                'choices'     => $this->get_all_pages_array(),
            )
        );

        $wp_customize->add_setting(
            'masvideos_tv_show_playlists_page_id',
            array(
                'default'       => '',
                'type'          => 'option',
                'capability'    => 'manage_masvideos',
            )
        );

        $wp_customize->add_control(
            'masvideos_tv_show_playlists_page_id',
            array(
                'label'       => esc_html__( 'TV Show Playlist Page', 'masvideos' ),
                'section'     => 'masvideos_playlists',
                'settings'    => 'masvideos_tv_show_playlists_page_id',
                'type'        => 'select',
                'choices'     => $this->get_all_pages_array(),
            )
        );
    }

    /**
     * Get Pages.
     *
     * @param string $value '', 'subcategories', or 'both'.
     * @return string
     */
    public function get_all_pages_array( $exclude = array() ) {
        $pages = get_pages( array(
            'post_type'   => 'page',
            'post_status' => 'publish,private,draft',
            'child_of'    => 0,
            'parent'      => -1,
            'exclude'     => $exclude,
            'sort_order'  => 'asc',
            'sort_column' => 'post_title',
        ) );
        
        $page_choices = array( '' => esc_html__( 'No page set', 'masvideos' ) ) + array_combine( array_map( 'strval', wp_list_pluck( $pages, 'ID' ) ), wp_list_pluck( $pages, 'post_title' ) );

        return $page_choices;
    }

}

new MasVideos_Customizer();