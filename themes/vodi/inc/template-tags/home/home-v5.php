<?php

/**
 * Template functions in Home v5
 *
 */

if ( ! function_exists( 'vodi_1_6_videos_list' ) ) {
    function vodi_1_6_videos_list() {
        ?><section class="home-section home-section-videos-with-featured-video dark style-2">
            <div class="container">
                <div class="home-section__inner home-section__videos">
                    <header class="home-section__flex-header">
                        <div class="home-section__title-tabs section-title">
                            <div class="section-title__inner">
                                <div class="video-header-tabs">
                                    <ul class="nav nav-tabs">
                                        <li class="nav-item">
                                            <a class="nav-link active" href="#newest-movies" data-toggle="tab">Treanding TV Shows</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#Newsest-episodes" data-toggle="tab">Treanding Movies</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="video-list-tabs">
                            <div class="tabs-section-inner">
                                <ul class="nav nav-tabs">
                                    <li class="nav-item">
                                        <a class="nav-link active" href="#">Today</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#">This week</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#">This month</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#">Last 3 months</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </header>

                    
                    <div class="videos-with-featured-video__1-6-column">
                        <div class="featured-video columns-1">
                            <?php get_template_part( 'templates/contents/content', 'video' ); ?>
                        </div>

                        <div class="videos columns-3">
                            <div class="videos__inner">
                                <?php for( $i=0; $i<6; $i++): ?>
                                <?php get_template_part( 'templates/contents/content', 'video' ); ?>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section><?php
    }
}

if ( ! function_exists( 'vodi_home_v5_section_movies_carousel_flex_header_1' ) ) {
    function vodi_home_v5_section_movies_carousel_flex_header_1() {
        if( vodi_is_masvideos_activated() ) {
            $args = apply_filters( 'vodi_home_v5_section_movies_carousel_flex_header_1_default_args', array(
                'section_title'         => esc_html__( '2018 Top Movies', 'vodi' ),
                'section_nav_links'     => array(
                    array(
                        'nav_title'         => esc_html__( 'Action', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                    array(
                        'nav_title'         => esc_html__( 'Biography', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                    array(
                        'nav_title'         => esc_html__( 'Sci-Fi', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                    array(
                        'nav_title'         => esc_html__( 'Crime', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                    array(
                        'nav_title'         => esc_html__( 'Drama', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                    array(
                        'nav_title'         => esc_html__( 'Kids', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                ),
                'section_background'    => 'dark',
                'footer_action_text'    => esc_html__( 'View all', 'vodi' ),
                'footer_action_link'    => '#',
                'el_class'              => 'style-2',
                'movies_shortcode'      => 'mas_movies',
                'shortcode_atts'        => array(
                    'columns'               => '6',
                    'limit'                 => '15',
                ),
                'carousel_args'         => array(
                    'slidesToShow'      => 6,
                    'slidesToScroll'    => 6,
                    'dots'              => false,
                    'arrows'            => true,
                    'autoplay'          => false,
                ),
            ) );

            vodi_section_movies_carousel_flex_header( $args );
        }
    }
}

if ( ! function_exists( 'vodi_home_v5_section_videos_carousel_flex_header' ) ) {
    function vodi_home_v5_section_videos_carousel_flex_header() {
        if( vodi_is_masvideos_activated() ) {
            $args = apply_filters( 'vodi_home_v5_section_videos_carousel_flex_header_default_args', array(
                'section_title'         => esc_html__( 'New Episodes', 'vodi' ),
                'section_nav_links'     => array(
                    array(
                        'nav_title'         => esc_html__( 'All', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                    array(
                        'nav_title'         => esc_html__( 'Comedy', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                    array(
                        'nav_title'         => esc_html__( 'Drama', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                    array(
                        'nav_title'         => esc_html__( 'Musical', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                    array(
                        'nav_title'         => esc_html__( 'Romance', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                ),
                'section_background'    => 'dark',
                'el_class'              => 'style-2',
                'videos_shortcode'      => 'mas_videos',
                'shortcode_atts'        => array(
                    'columns'               => '5',
                    'limit'                 => '15',
                ),
                'carousel_args'         => array(
                    'slidesToShow'      => 5,
                    'slidesToScroll'    => 5,
                    'dots'              => false,
                    'arrows'            => true,
                    'autoplay'          => false,
                ),
            ) );

            vodi_section_videos_carousel_flex_header( $args );
        }
    }
}

if ( ! function_exists( 'vodi_home_v5_section_featured_tv_show' ) ) {
    function vodi_home_v5_section_featured_tv_show() {
        if( vodi_is_masvideos_activated() ) {
            $args = apply_filters( 'vodi_home_v5_section_featured_tv_show_default_args', array(
                'feature_tv_show_pre_title'     => esc_html__( 'Featured', 'vodi' ),
                'feature_tv_show_title'         => esc_html__( 'Vikings', 'vodi' ),
                'feature_tv_show_subtitle'      => esc_html__( 'New Season 5 just flown in. Watch and debate.', 'vodi' ),
                'section_nav_links'             => array(
                    array(
                        'nav_title'         => esc_html__( 'Season 5', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                    array(
                        'nav_title'         => esc_html__( 'Season 4', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                    array(
                        'nav_title'         => esc_html__( 'Season 3', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                    array(
                        'nav_title'         => esc_html__( 'Season 2', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                    array(
                        'nav_title'         => esc_html__( 'Season 1', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                ),
                'section_background'            => '',
                'bg_image'                      => array( '//placehold.it/2100x675', '2100', '675' ),
                'videos_shortcode'              => 'mas_videos',
                'shortcode_atts'                => array(
                    'columns'               => '5',
                    'limit'                 => '5',
                ),
            ) );

            vodi_section_featured_tv_show( $args );
        }
    }
}

if ( ! function_exists( 'vodi_home_v5_section_movies_carousel_flex_header_2' ) ) {
    function vodi_home_v5_section_movies_carousel_flex_header_2() {
        if( vodi_is_masvideos_activated() ) {
            $args = apply_filters( 'vodi_home_v5_section_movies_carousel_flex_header_2_default_args', array(
                'section_title'         => esc_html__( 'Valentines Day Movies', 'vodi' ),
                'section_nav_links'     => array(
                    array(
                        'nav_title'         => esc_html__( 'All', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                    array(
                        'nav_title'         => esc_html__( 'Comedy', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                    array(
                        'nav_title'         => esc_html__( 'Drama', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                    array(
                        'nav_title'         => esc_html__( 'Musical', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                    array(
                        'nav_title'         => esc_html__( 'Romance', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                ),
                'section_background'    => 'dark',
                'footer_action_text'    => esc_html__( 'View all', 'vodi' ),
                'footer_action_link'    => '#',
                'el_class'              => 'style-2',
                'movies_shortcode'      => 'mas_movies',
                'shortcode_atts'        => array(
                    'columns'               => '6',
                    'limit'                 => '15',
                ),
                'carousel_args'         => array(
                    'slidesToShow'      => 6,
                    'slidesToScroll'    => 6,
                    'dots'              => false,
                    'arrows'            => true,
                    'autoplay'          => false,
                ),
            ) );

            vodi_section_movies_carousel_flex_header( $args );
        }
    }
}

if ( ! function_exists( 'vodi_home_v5_section_featured_video' ) ) {
    function vodi_home_v5_section_featured_video() {
        if( vodi_is_masvideos_activated() ) {
            $args = apply_filters( 'vodi_home_v2_section_featured_video_default_args', array(
                'feature_video_title'           => esc_html__( 'The Dark Towel', 'vodi' ),
                'feature_video_subtitle'        => esc_html__( 'oul-mouthed mutant mercenary Wade Wilson (AKA. Deadpool), brings together a team of fellow mutant rogues to protect a young boy with supernatural abilities from the brutal.', 'vodi' ),
                'feature_video_action_icon'     => '<i class="fas fa-play"></i>',
                'video_id'                      => '144',
                'image'                         => array( '//placehold.it/187x59', '187', '59' ),
                'bg_image'                      => array( '//placehold.it/2100x600', '2100', '600' ),
            ) );

            vodi_section_featured_video( $args );
        }
    }
}

if ( ! function_exists( 'vodi_home_v5_banner_with_section_videos' ) ) {
    function vodi_home_v5_banner_with_section_videos() {
        ?><section class="home-section banner-with-section-videos dark style-2">
            <div class="container">
                <div class="home-section__inner home-section__videos">
                    <header class="home-section__flex-header">
                        <h2 class="section-title home-section__title">Vodi in 4K Ultra HD</h2>
                        <div class="video-list-tabs">
                            <div class="tabs-section-inner">
                                <ul class="nav nav-tabs">
                                    <li class="nav-item">
                                        <a class="nav-link active" href="#">Today</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#">This week</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#">This month</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#">Last 3 months</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </header>

                    
                    <div class="banner-with-section-videos__1-8-column">
                        <div class="banner column-1">
                            <img src="https://via.placeholder.com/324x388">
                        </div>

                        <div class="videos column-4">
                            <div class="videos__inner">
                                <?php for( $i=0; $i<8; $i++): ?>
                                <?php get_template_part( 'templates/contents/content', 'video' ); ?>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section><?php
    }
}

if ( ! function_exists( 'vodi_home_v5_video_section' ) ) {
    function vodi_home_v5_video_section() {
        if( vodi_is_masvideos_activated() ) {
            $args = apply_filters( 'vodi_home_v5_video_section_default_args', array(
                'section_title'         => esc_html__( 'Feature Tv Serie', 'vodi' ),
                'footer_action_text'    => esc_html__( 'View all', 'vodi' ),
                'footer_action_link'    => '#',
                'section_background'    => 'dark',
                'el_class'              => 'style-2',
                'videos_shortcode'      => 'mas_videos',
                'shortcode_atts'        => array(
                    'columns'               => '2',
                    'limit'                 => '2',
                ),
            ) );

            vodi_video_section( $args );
        }
    }
}

if ( ! function_exists( 'vodi_home_v5_videos_with_featured_video' ) ) {
    function vodi_home_v5_videos_with_featured_video() {
        if( vodi_is_masvideos_activated() ) {
            $args = apply_filters( 'vodi_home_v5_videos_with_featured_video_default_args', array(
                'section_title'         => esc_html__( 'Vodi Kids', 'vodi' ),
                'section_nav_links'     => array(
                    array(
                        'nav_title'         => esc_html__( 'Today', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                    array(
                        'nav_title'         => esc_html__( 'This week', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                    array(
                        'nav_title'         => esc_html__( 'This month', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                    array(
                        'nav_title'         => esc_html__( 'Last 3 months', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                ),
                'section_background'    => 'dark',
                'el_class'              => 'style-2',
                'shortcode_atts_1'              => array(
                    'columns'               => '1',
                    'limit'                 => '1',
                ),
                'shortcode_atts_2'              => array(
                    'columns'               => '3',
                    'limit'                 => '6',
                ),
            ) );

            vodi_videos_with_featured_video( $args );
        }
    }
}

if ( ! function_exists( 'vodi_home_v5_section_movies_carousel_flex_header_3' ) ) {
    function vodi_home_v5_section_movies_carousel_flex_header_3() {
        if( vodi_is_masvideos_activated() ) {
            $args = apply_filters( 'vodi_home_v5_section_movies_carousel_flex_header_3_default_args', array(
                'section_title'         => esc_html__( 'Recently Viewed Movies', 'vodi' ),
                'section_background'    => 'dark',
                'footer_action_text'    => esc_html__( 'View all', 'vodi' ),
                'footer_action_link'    => '#',
                'el_class'              => 'style-2',
                'movies_shortcode'      => 'mas_movies',
                'shortcode_atts'        => array(
                    'columns'               => '10',
                    'limit'                 => '15',
                ),
                'carousel_args'         => array(
                    'slidesToShow'      => 10,
                    'slidesToScroll'    => 10,
                    'dots'              => false,
                    'arrows'            => true,
                    'autoplay'          => false,
                ),
            ) );

            vodi_section_movies_carousel_flex_header( $args );
        }
    }
}