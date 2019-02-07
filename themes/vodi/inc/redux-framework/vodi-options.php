<?php
if ( ! class_exists( 'ReduxFramework' ) ) {
    return;
}

if ( ! class_exists( 'Vodi_Options' ) ) {

    class Vodi_Options {

        public function __construct( ) {
            add_action( 'after_setup_theme', array( $this, 'load_config' ) );
        }

        public function load_config() {

            $options        = array( 'general', 'header', 'footer', 'blog');
            $options_dir    = get_template_directory() . '/inc/redux-framework/options';

            foreach ( $options as $option ) {
                $options_file = $option . '-options.php';
                require_once $options_dir . '/' . $options_file ;
            }

            $sections   = apply_filters( 'vodi_options_sections_args', array( $general_options, $header_options, $footer_options, $blog_options ) );
            $theme      = wp_get_theme();
            $args       = array(
                'opt_name'          => 'vodi_options',
                'display_name'      => $theme->get( 'Name' ),
                'display_version'   => $theme->get( 'Version' ),
                'allow_sub_menu'    => true,
                'menu_title'        => esc_html__( 'Vodi', 'vodi' ),
                'page_priority'     => 3,
                'page_slug'         => 'theme_options',
                'intro_text'        => '',
                'dev_mode'          => false,
                'customizer'        => true,
                'footer_credit'     => '&nbsp;',
            );

            $ReduxFramework = new ReduxFramework( $sections, $args );
        }
    }

    new Vodi_Options();
}

if( ! array_key_exists( 'vodi_options' , $GLOBALS ) ) {
    $GLOBALS['vodi_options'] = get_option( 'vodi_options', array() );
}