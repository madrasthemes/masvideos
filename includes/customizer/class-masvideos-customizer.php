<?php
/**
 * Adds options to the customizer for MasVideos.
 *
 * @version 1.0.0
 * @package MasVideos
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
            'title'          => esc_html__( 'MAS Videos', 'masvideos' ),
        ) );

        $this->add_general_section( $wp_customize );
        $this->add_myaccount_section( $wp_customize );
        $this->add_email_section( $wp_customize );
        $this->add_movies_section( $wp_customize );
        $this->add_videos_section( $wp_customize );
        $this->add_tv_shows_section( $wp_customize );
        $this->add_persons_section( $wp_customize );
    }

    /**
     * General section.
     *
     * @param WP_Customize_Manager $wp_customize Theme Customizer object.
     */
    private function add_general_section( $wp_customize ) {
        $wp_customize->add_section(
            'masvideos_general',
            array(
                'title'    => esc_html__( 'General', 'masvideos' ),
                'priority' => 10,
                'panel'    => 'masvideos',
            )
        );

        $wp_customize->add_setting(
            'masvideos_tmdb_api',
            array(
                'default'              => '',
                'type'                 => 'option',
                'capability'           => 'manage_masvideos',
                'sanitize_callback'    => 'wp_kses_post',
            )
        );

        $wp_customize->add_control(
            'masvideos_tmdb_api',
            array(
                'label'       => esc_html__( 'TMDB API Key', 'masvideos' ),
                'description' => esc_html__( 'Add TMDB API Key to integrate with TMDB.', 'masvideos' ),
                'section'     => 'masvideos_general',
                'settings'    => 'masvideos_tmdb_api',
                'type'        => 'text',
            )
        );
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
                'priority' => 20,
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
            'masvideos_upload_video_page_id',
            array(
                'default'       => '',
                'type'          => 'option',
                'capability'    => 'manage_masvideos',
            )
        );

        $wp_customize->add_control(
            'masvideos_upload_video_page_id',
            array(
                'label'       => esc_html__( 'Upload Video Page', 'masvideos' ),
                'section'     => 'masvideos_myaccount',
                'settings'    => 'masvideos_upload_video_page_id',
                'type'        => 'select',
                'choices'     => $this->get_all_pages_array(),
            )
        );

        $wp_customize->add_setting(
            'masvideos_enable_myaccount_registration',
            array(
                'default'              => 'no',
                'type'                 => 'option',
                'capability'           => 'manage_masvideos',
                'sanitize_callback'    => 'masvideos_bool_to_string',
                'sanitize_js_callback' => 'masvideos_string_to_bool',
            )
        );

        $wp_customize->add_control(
            'masvideos_enable_myaccount_registration',
            array(
                'label'    => esc_html__( 'Account Creation', 'masvideos' ),
                'section'  => 'masvideos_myaccount',
                'settings' => 'masvideos_enable_myaccount_registration',
                'type'     => 'checkbox',
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
     * Email Section.
     *
     * @param WP_Customize_Manager $wp_customize Theme Customizer object.
     * @since 1.0.0
     */
    public function add_email_section( $wp_customize ) {
        $wp_customize->add_section(
            'masvideos_email',
            array(
                'title'    => esc_html__( 'Email', 'masvideos' ),
                'priority' => 30,
                'panel'    => 'masvideos',
            )
        );

        $wp_customize->add_setting(
            'masvideos_email_from_name',
            array(
                'default'       => esc_attr( get_bloginfo( 'name', 'display' ) ),
                'type'          => 'option',
                'capability'    => 'manage_masvideos',
                'transport'     => 'postMessage',
            )
        );

        $wp_customize->add_control(
            'masvideos_email_from_name',
            array(
                'label'       => esc_html__( '"From" Name', 'masvideos' ),
                'section'     => 'masvideos_email',
                'settings'    => 'masvideos_email_from_name',
                'type'        => 'text',
            )
        );

        $wp_customize->add_setting(
            'masvideos_email_from_address',
            array(
                'default'       => get_option( 'admin_email' ),
                'type'          => 'option',
                'capability'    => 'manage_masvideos',
                'sanitize_callback'    => 'sanitize_email',
                'sanitize_js_callback' => 'sanitize_email',
                'transport'     => 'postMessage',
            )
        );

        $wp_customize->add_control(
            'masvideos_email_from_address',
            array(
                'label'       => esc_html__( '"From" Address', 'masvideos' ),
                'section'     => 'masvideos_email',
                'settings'    => 'masvideos_email_from_address',
                'type'        => 'email',
                'input_attrs' => array(
                    'type'        => 'email',
                ),
            )
        );

        $wp_customize->add_setting(
            'masvideos_email_header_image',
            array(
                'default'       => '',
                'type'          => 'option',
                'capability'    => 'manage_masvideos',
                'sanitize_callback' => 'sanitize_url',
                'transport'     => 'postMessage',
            )
        );

        $wp_customize->add_control(
            'masvideos_email_header_image',
            array(
                'label'       => esc_html__( 'Header image', 'masvideos' ),
                'description' => esc_html__( 'URL to an image you want to show in the email header. Upload images using the media uploader (Admin > Media).', 'masvideos' ),
                'section'     => 'masvideos_email',
                'settings'    => 'masvideos_email_header_image',
                'type'        => 'url',
                'input_attrs' => array(
                    'type'        => 'url',
                    'placeholder' => esc_html__( 'N/A', 'masvideos' ),
                ),
            )
        );

        $wp_customize->add_setting(
            'masvideos_email_footer_text',
            array(
                'default'       => esc_html__( '{site_title} &mdash; Built with {MasVideos}', 'masvideos' ),
                'type'          => 'option',
                'capability'    => 'manage_masvideos',
                'transport'     => 'postMessage',
            )
        );

        $wp_customize->add_control(
            'masvideos_email_footer_text',
            array(
                'label'       => esc_html__( 'Footer text', 'masvideos' ),
                'section'     => 'masvideos_email',
                'settings'    => 'masvideos_email_footer_text',
                'type'        => 'textarea',
            )
        );

        $wp_customize->add_setting(
            'masvideos_email_base_color', array(
                'default'           => '#24baef',
                'sanitize_callback' => 'sanitize_hex_color',
                'type'          => 'option',
                'transport'     => 'postMessage',
            )
        );

        $wp_customize->add_control(
            new WP_Customize_Color_Control(
                $wp_customize, 'masvideos_email_base_color', 
                array(
                    'label'    => __( 'Base color', 'masvideos' ),
                    'section'  => 'masvideos_email',
                    'settings' => 'masvideos_email_base_color',
                )
            )
        );

        $wp_customize->add_setting(
            'masvideos_email_background_color', array(
                'default'           => '#f7f7f7',
                'sanitize_callback' => 'sanitize_hex_color',
                'type'          => 'option',
                'transport'     => 'postMessage',
            )
        );

        $wp_customize->add_control(
            new WP_Customize_Color_Control(
                $wp_customize, 'masvideos_email_background_color', 
                array(
                    'label'    => __( 'Background color', 'masvideos' ),
                    'section'  => 'masvideos_email',
                    'settings' => 'masvideos_email_background_color',
                )
            )
        );

        $wp_customize->add_setting(
            'masvideos_email_body_background_color', array(
                'default'           => '#ffffff',
                'sanitize_callback' => 'sanitize_hex_color',
                'type'          => 'option',
                'transport'     => 'postMessage',
            )
        );

        $wp_customize->add_control(
            new WP_Customize_Color_Control(
                $wp_customize, 'masvideos_email_body_background_color', 
                array(
                    'label'    => __( 'Body background color', 'masvideos' ),
                    'section'  => 'masvideos_email',
                    'settings' => 'masvideos_email_body_background_color',
                )
            )
        );

        $wp_customize->add_setting(
            'masvideos_email_text_color', array(
                'default'           => '#3c3c3c',
                'sanitize_callback' => 'sanitize_hex_color',
                'type'          => 'option',
                'transport'     => 'postMessage',
            )
        );

        $wp_customize->add_control(
            new WP_Customize_Color_Control(
                $wp_customize, 'masvideos_email_text_color', 
                array(
                    'label'    => __( 'Body text color', 'masvideos' ),
                    'section'  => 'masvideos_email',
                    'settings' => 'masvideos_email_text_color',
                )
            )
        );

        // New Account
        $wp_customize->add_setting(
            'masvideos_user_new_account_enabled',
            array(
                'default'              => 'yes',
                'type'                 => 'option',
                'capability'           => 'manage_masvideos',
                'sanitize_callback'    => 'masvideos_bool_to_string',
                'sanitize_js_callback' => 'masvideos_string_to_bool',
                'transport'     => 'postMessage',
            )
        );

        $wp_customize->add_control(
            'masvideos_user_new_account_enabled',
            array(
                'label'       => esc_html__( 'Enable New Account Email?', 'masvideos' ),
                'section'  => 'masvideos_email',
                'settings' => 'masvideos_user_new_account_enabled',
                'type'     => 'checkbox',
            )
        );

        $wp_customize->add_setting(
            'masvideos_user_new_account_subject',
            array(
                'default'       => '',
                'type'          => 'option',
                'capability'    => 'manage_masvideos',
                'transport'     => 'postMessage',
            )
        );

        $wp_customize->add_control(
            'masvideos_user_new_account_subject',
            array(
                'label'       => esc_html__( 'New Account Subject', 'masvideos' ),
                'section'     => 'masvideos_email',
                'settings'    => 'masvideos_user_new_account_subject',
                'type'        => 'text',
                'input_attrs' => array(
                    'placeholder' => esc_html__( 'Your {site_title} account has been created!', 'masvideos' ),
                ),
            )
        );

        $wp_customize->add_setting(
            'masvideos_user_new_account_heading',
            array(
                'default'       => '',
                'type'          => 'option',
                'capability'    => 'manage_masvideos',
                'transport'     => 'postMessage',
            )
        );

        $wp_customize->add_control(
            'masvideos_user_new_account_heading',
            array(
                'label'       => esc_html__( 'New Account Email Heading', 'masvideos' ),
                'section'     => 'masvideos_email',
                'settings'    => 'masvideos_user_new_account_heading',
                'type'        => 'text',
                'input_attrs' => array(
                    'placeholder' => esc_html__( 'Welcome to {site_title}', 'masvideos' ),
                ),
            )
        );

        $wp_customize->add_setting(
            'masvideos_user_new_account_additional_content',
            array(
                'default'       => esc_html__( 'We look forward to seeing you soon.', 'masvideos' ),
                'type'          => 'option',
                'capability'    => 'manage_masvideos',
                'transport'     => 'postMessage',
            )
        );

        $wp_customize->add_control(
            'masvideos_user_new_account_additional_content',
            array(
                'label'       => esc_html__( 'New Account Additional Content', 'masvideos' ),
                'section'     => 'masvideos_email',
                'settings'    => 'masvideos_user_new_account_additional_content',
                'type'        => 'textarea',
            )
        );

        $wp_customize->add_setting(
            'masvideos_user_new_account_email_type',
            array(
                'default'       => 'html',
                'type'          => 'option',
                'capability'    => 'manage_masvideos',
                'transport'     => 'postMessage',
            )
        );

        $wp_customize->add_control(
            'masvideos_user_new_account_email_type',
            array(
                'label'       => esc_html__( 'New Account Email Type', 'masvideos' ),
                'section'     => 'masvideos_email',
                'settings'    => 'masvideos_user_new_account_email_type',
                'type'        => 'select',
                'choices'     => $this->get_email_type_options(),
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
                'priority' => 30,
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
            'masvideos_default_movies_catalog_orderby',
            array(
                'default'           => 'release_date',
                'type'              => 'option',
                'capability'        => 'manage_masvideos',
            )
        );

        $wp_customize->add_control(
            'masvideos_default_movies_catalog_orderby',
            array(
                'label'       => esc_html__( 'Default movie sorting', 'masvideos' ),
                'description' => esc_html__( 'How should movies be sorted in the catalog by default?', 'masvideos' ),
                'section'     => 'masvideos_movies',
                'settings'    => 'masvideos_default_movies_catalog_orderby',
                'type'        => 'select',
                'choices'     => apply_filters( 'masvideos_default_movies_catalog_orderby_options', array(
                    'title-asc'     => esc_html__( 'Name: Ascending', 'masvideos' ),
                    'title-desc'    => esc_html__( 'Name: Descending', 'masvideos' ),
                    'release_date'  => esc_html__( 'Latest', 'masvideos' ),
                    'menu_order'    => esc_html__( 'Menu Order', 'masvideos' ),
                    'rating'        => esc_html__( 'Rating', 'masvideos' ),
                ) ),
            )
        );

        // The following settings should be hidden if the theme is declaring the values.
        if ( ! has_filter( 'masvideos_movie_columns' ) ) {
            $wp_customize->add_setting(
                'masvideos_movie_columns',
                array(
                    'default'              => 4,
                    'type'                 => 'option',
                    'capability'           => 'manage_masvideos',
                    'sanitize_callback'    => 'absint',
                    'sanitize_js_callback' => 'absint',
                )
            );

            $wp_customize->add_control(
                'masvideos_movie_columns',
                array(
                    'label'       => esc_html__( 'Movies per row', 'masvideos' ),
                    'description' => esc_html__( 'How many movies should be shown per row?', 'masvideos' ),
                    'section'     => 'masvideos_movies',
                    'settings'    => 'masvideos_movie_columns',
                    'type'        => 'number',
                    'input_attrs' => array(
                        'min'  => masvideos_get_theme_support( 'movie_grid::min_columns', 1 ),
                        'max'  => masvideos_get_theme_support( 'movie_grid::max_columns', '' ),
                        'step' => 1,
                    ),
                )
            );
        }

        // The following settings should be hidden if the theme is declaring the values.
        if ( ! has_filter( 'masvideos_movie_rows' ) ) {
            $wp_customize->add_setting(
                'masvideos_movie_rows',
                array(
                    'default'              => 4,
                    'type'                 => 'option',
                    'capability'           => 'manage_masvideos',
                    'sanitize_callback'    => 'absint',
                    'sanitize_js_callback' => 'absint',
                )
            );

            $wp_customize->add_control(
                'masvideos_movie_rows',
                array(
                    'label'       => esc_html__( 'Rows per page', 'masvideos' ),
                    'description' => esc_html__( 'How many rows of movies should be shown per page?', 'masvideos' ),
                    'section'     => 'masvideos_movies',
                    'settings'    => 'masvideos_movie_rows',
                    'type'        => 'number',
                    'input_attrs' => array(
                        'min'  => masvideos_get_theme_support( 'movie_grid::min_rows', 1 ),
                        'max'  => masvideos_get_theme_support( 'movie_grid::max_rows', '' ),
                        'step' => 1,
                    ),
                )
            );
        }

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
                'priority' => 40,
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

        $wp_customize->add_setting(
            'masvideos_default_videos_catalog_orderby',
            array(
                'default'           => 'date',
                'type'              => 'option',
                'capability'        => 'manage_masvideos',
            )
        );

        $wp_customize->add_control(
            'masvideos_default_videos_catalog_orderby',
            array(
                'label'       => esc_html__( 'Default video sorting', 'masvideos' ),
                'description' => esc_html__( 'How should videos be sorted in the catalog by default?', 'masvideos' ),
                'section'     => 'masvideos_videos',
                'settings'    => 'masvideos_default_videos_catalog_orderby',
                'type'        => 'select',
                'choices'     => apply_filters( 'masvideos_default_videos_catalog_orderby_options', array(
                    'title-asc'     => esc_html__( 'Name: Ascending', 'masvideos' ),
                    'title-desc'    => esc_html__( 'Name: Descending', 'masvideos' ),
                    'date'          => esc_html__( 'Latest', 'masvideos' ),
                    'menu_order'    => esc_html__( 'Menu Order', 'masvideos' ),
                ) ),
            )
        );

        // The following settings should be hidden if the theme is declaring the values.
        if ( ! has_filter( 'masvideos_video_columns' ) ) {
            $wp_customize->add_setting(
                'masvideos_video_columns',
                array(
                    'default'              => 4,
                    'type'                 => 'option',
                    'capability'           => 'manage_masvideos',
                    'sanitize_callback'    => 'absint',
                    'sanitize_js_callback' => 'absint',
                )
            );

            $wp_customize->add_control(
                'masvideos_video_columns',
                array(
                    'label'       => esc_html__( 'Videos per row', 'masvideos' ),
                    'description' => esc_html__( 'How many videos should be shown per row?', 'masvideos' ),
                    'section'     => 'masvideos_videos',
                    'settings'    => 'masvideos_video_columns',
                    'type'        => 'number',
                    'input_attrs' => array(
                        'min'  => masvideos_get_theme_support( 'video_grid::min_columns', 1 ),
                        'max'  => masvideos_get_theme_support( 'video_grid::max_columns', '' ),
                        'step' => 1,
                    ),
                )
            );
        }

        // The following settings should be hidden if the theme is declaring the values.
        if ( ! has_filter( 'masvideos_video_rows' ) ) {
            $wp_customize->add_setting(
                'masvideos_video_rows',
                array(
                    'default'              => 4,
                    'type'                 => 'option',
                    'capability'           => 'manage_masvideos',
                    'sanitize_callback'    => 'absint',
                    'sanitize_js_callback' => 'absint',
                )
            );

            $wp_customize->add_control(
                'masvideos_video_rows',
                array(
                    'label'       => esc_html__( 'Rows per page', 'masvideos' ),
                    'description' => esc_html__( 'How many rows of videos should be shown per page?', 'masvideos' ),
                    'section'     => 'masvideos_videos',
                    'settings'    => 'masvideos_video_rows',
                    'type'        => 'number',
                    'input_attrs' => array(
                        'min'  => masvideos_get_theme_support( 'video_grid::min_rows', 1 ),
                        'max'  => masvideos_get_theme_support( 'video_grid::max_rows', '' ),
                        'step' => 1,
                    ),
                )
            );
        }
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
                'priority' => 50,
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
            'masvideos_default_tv_shows_catalog_orderby',
            array(
                'default'           => 'date',
                'type'              => 'option',
                'capability'        => 'manage_masvideos',
            )
        );

        $wp_customize->add_control(
            'masvideos_default_tv_shows_catalog_orderby',
            array(
                'label'       => esc_html__( 'Default tv show sorting', 'masvideos' ),
                'description' => esc_html__( 'How should tv shows be sorted in the catalog by default?', 'masvideos' ),
                'section'     => 'masvideos_tv_shows',
                'settings'    => 'masvideos_default_tv_shows_catalog_orderby',
                'type'        => 'select',
                'choices'     => apply_filters( 'masvideos_default_tv_shows_catalog_orderby_options', array(
                    'title-asc'  => esc_html__( 'Name: Ascending', 'masvideos' ),
                    'title-desc' => esc_html__( 'Name: Descending', 'masvideos' ),
                    'date'       => esc_html__( 'Latest', 'masvideos' ),
                    'menu_order' => esc_html__( 'Menu Order', 'masvideos' ),
                    'rating'     => esc_html__( 'Rating', 'masvideos' ),
                ) ),
            )
        );

        // The following settings should be hidden if the theme is declaring the values.
        if ( ! has_filter( 'masvideos_tv_show_columns' ) ) {
            $wp_customize->add_setting(
                'masvideos_tv_show_columns',
                array(
                    'default'              => 4,
                    'type'                 => 'option',
                    'capability'           => 'manage_masvideos',
                    'sanitize_callback'    => 'absint',
                    'sanitize_js_callback' => 'absint',
                )
            );

            $wp_customize->add_control(
                'masvideos_tv_show_columns',
                array(
                    'label'       => esc_html__( 'TV Shows per row', 'masvideos' ),
                    'description' => esc_html__( 'How many tv shows should be shown per row?', 'masvideos' ),
                    'section'     => 'masvideos_tv_shows',
                    'settings'    => 'masvideos_tv_show_columns',
                    'type'        => 'number',
                    'input_attrs' => array(
                        'min'  => masvideos_get_theme_support( 'tv_show_grid::min_columns', 1 ),
                        'max'  => masvideos_get_theme_support( 'tv_show_grid::max_columns', '' ),
                        'step' => 1,
                    ),
                )
            );
        }

        // The following settings should be hidden if the theme is declaring the values.
        if ( ! has_filter( 'masvideos_tv_show_rows' ) ) {
            $wp_customize->add_setting(
                'masvideos_tv_show_rows',
                array(
                    'default'              => 4,
                    'type'                 => 'option',
                    'capability'           => 'manage_masvideos',
                    'sanitize_callback'    => 'absint',
                    'sanitize_js_callback' => 'absint',
                )
            );

            $wp_customize->add_control(
                'masvideos_tv_show_rows',
                array(
                    'label'       => esc_html__( 'Rows per page', 'masvideos' ),
                    'description' => esc_html__( 'How many rows of tv shows should be shown per page?', 'masvideos' ),
                    'section'     => 'masvideos_tv_shows',
                    'settings'    => 'masvideos_tv_show_rows',
                    'type'        => 'number',
                    'input_attrs' => array(
                        'min'  => masvideos_get_theme_support( 'tv_show_grid::min_rows', 1 ),
                        'max'  => masvideos_get_theme_support( 'tv_show_grid::max_rows', '' ),
                        'step' => 1,
                    ),
                )
            );
        }

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

        $wp_customize->add_setting(
            'masvideos_episode_review_rating_required',
            array(
                'default'              => 'yes',
                'type'                 => 'option',
                'capability'           => 'manage_masvideos',
                'sanitize_callback'    => 'masvideos_bool_to_string',
                'sanitize_js_callback' => 'masvideos_string_to_bool',
            )
        );

        $wp_customize->add_control(
            'masvideos_episode_review_rating_required',
            array(
                'label'    => esc_html__( 'Enable Episodes Review', 'masvideos' ),
                'section'  => 'masvideos_tv_shows',
                'settings' => 'masvideos_episode_review_rating_required',
                'type'     => 'checkbox',
            )
        );
    }

    /**
     * Persons section.
     *
     * @param WP_Customize_Manager $wp_customize Theme Customizer object.
     */
    private function add_persons_section( $wp_customize ) {
        $wp_customize->add_section(
            'masvideos_persons',
            array(
                'title'    => esc_html__( 'Persons', 'masvideos' ),
                'priority' => 30,
                'panel'    => 'masvideos',
            )
        );

        $wp_customize->add_setting(
            'masvideos_persons_page_id',
            array(
                'default'       => '',
                'type'          => 'option',
                'capability'    => 'manage_masvideos',
            )
        );

        $wp_customize->add_control(
            'masvideos_persons_page_id',
            array(
                'label'       => esc_html__( 'Persons Page', 'masvideos' ),
                'section'     => 'masvideos_persons',
                'settings'    => 'masvideos_persons_page_id',
                'type'        => 'select',
                'choices'     => $this->get_all_pages_array(),
            )
        );

        $wp_customize->add_setting(
            'masvideos_default_persons_catalog_orderby',
            array(
                'default'           => 'date',
                'type'              => 'option',
                'capability'        => 'manage_masvideos',
            )
        );

        $wp_customize->add_control(
            'masvideos_default_persons_catalog_orderby',
            array(
                'label'       => esc_html__( 'Default person sorting', 'masvideos' ),
                'description' => esc_html__( 'How should persons be sorted in the catalog by default?', 'masvideos' ),
                'section'     => 'masvideos_persons',
                'settings'    => 'masvideos_default_persons_catalog_orderby',
                'type'        => 'select',
                'choices'     => apply_filters( 'masvideos_default_persons_catalog_orderby_options', array(
                    'title-asc'     => esc_html__( 'Name: Ascending', 'masvideos' ),
                    'title-desc'    => esc_html__( 'Name: Descending', 'masvideos' ),
                    'date'          => esc_html__( 'Latest', 'masvideos' ),
                    'menu_order'    => esc_html__( 'Menu Order', 'masvideos' ),
                ) ),
            )
        );

        // The following settings should be hidden if the theme is declaring the values.
        if ( ! has_filter( 'masvideos_person_columns' ) ) {
            $wp_customize->add_setting(
                'masvideos_person_columns',
                array(
                    'default'              => 4,
                    'type'                 => 'option',
                    'capability'           => 'manage_masvideos',
                    'sanitize_callback'    => 'absint',
                    'sanitize_js_callback' => 'absint',
                )
            );

            $wp_customize->add_control(
                'masvideos_person_columns',
                array(
                    'label'       => esc_html__( 'Persons per row', 'masvideos' ),
                    'description' => esc_html__( 'How many persons should be shown per row?', 'masvideos' ),
                    'section'     => 'masvideos_persons',
                    'settings'    => 'masvideos_person_columns',
                    'type'        => 'number',
                    'input_attrs' => array(
                        'min'  => masvideos_get_theme_support( 'person_grid::min_columns', 1 ),
                        'max'  => masvideos_get_theme_support( 'person_grid::max_columns', '' ),
                        'step' => 1,
                    ),
                )
            );
        }

        // The following settings should be hidden if the theme is declaring the values.
        if ( ! has_filter( 'masvideos_person_rows' ) ) {
            $wp_customize->add_setting(
                'masvideos_person_rows',
                array(
                    'default'              => 4,
                    'type'                 => 'option',
                    'capability'           => 'manage_masvideos',
                    'sanitize_callback'    => 'absint',
                    'sanitize_js_callback' => 'absint',
                )
            );

            $wp_customize->add_control(
                'masvideos_person_rows',
                array(
                    'label'       => esc_html__( 'Rows per page', 'masvideos' ),
                    'description' => esc_html__( 'How many rows of persons should be shown per page?', 'masvideos' ),
                    'section'     => 'masvideos_persons',
                    'settings'    => 'masvideos_person_rows',
                    'type'        => 'number',
                    'input_attrs' => array(
                        'min'  => masvideos_get_theme_support( 'person_grid::min_rows', 1 ),
                        'max'  => masvideos_get_theme_support( 'person_grid::max_rows', '' ),
                        'step' => 1,
                    ),
                )
            );
        }
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

    /**
     * Email type options.
     *
     * @return array
     */
    public function get_email_type_options() {
        $types = array( 'plain' => __( 'Plain text', 'masvideos' ) );

        if ( class_exists( 'DOMDocument' ) ) {
            $types['html']      = __( 'HTML', 'masvideos' );
            $types['multipart'] = __( 'Multipart', 'masvideos' );
        }

        return $types;
    }

}

new MasVideos_Customizer();