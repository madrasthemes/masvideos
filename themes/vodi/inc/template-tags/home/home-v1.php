<?php

/**
 * Template functions in Home v1
 *
 */

if ( ! function_exists( 'vodi_home_v1_movie_section_aside_header' ) ) {
    function vodi_home_v1_movie_section_aside_header() {
        if( vodi_is_masvideos_activated() ) {
            $args = apply_filters( 'vodi_home_v1_movie_section_aside_header_default_args', array(
                'section_title'         => esc_html__( 'Popular Movies to Watch Now', 'vodi' ),
                'section_subtitle'      => esc_html__( 'Most watched movies by days', 'vodi' ),
                'action_link'   => '#',
                'action_text'   => esc_html__( 'View All', 'vodi' ),
                'section_background'    => 'dark',
                'shortcode_atts_1'      => array(
                    'columns'               => '5',
                    'limit'                 => '5',
                ),
                'shortcode_atts_2'      => array(
                    'columns'               => '7',
                    'limit'                 => '7',
                ),
            ) );

            vodi_movie_section_aside_header( $args );
        }
    }
}

if ( ! function_exists( 'vodi_home_v1_section_movies_carousel_aside_header_1' ) ) {
    function vodi_home_v1_section_movies_carousel_aside_header_1() {
        if( vodi_is_masvideos_activated() ) {
            $args = apply_filters( 'vodi_home_v1_section_movies_carousel_aside_header_1_default_args', array(
                'section_title'         => esc_html__( 'Romantic for Valentines Day', 'vodi' ),
                'section_subtitle'      => '',
                'action_link'   => '#',
                'action_text'   => esc_html__( 'View All', 'vodi' ),
                'section_background'    => '',
                'header_posisition'     => 'header-right',
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

            vodi_section_movies_carousel_aside_header( $args );
        }
    }
}

if ( ! function_exists( 'vodi_home_v1_section_featured_video' ) ) {
    function vodi_home_v1_section_featured_video() {
        if( vodi_is_masvideos_activated() ) {
            $args = apply_filters( 'vodi_home_v1_section_featured_video_default_args', array(
                'feature_video_title'           => esc_html__( 'Big Comeback', 'vodi' ),
                'feature_video_subtitle'        => esc_html__( 'Nullam porta, eros id aliquam pulvinar, urna ex mattis eros, quis vestibulum urna turpis et risus. Mauris porttitor risus faucibus, auctor arcu a, tincidunt nibh...', 'vodi' ),
                'feature_video_action_icon'     => '<i class="fas fa-play"></i>',
                'section_background'            => 'dark',
                'video_id'                      => '144',
                'image'                         => array( '//placehold.it/187x59', '187', '59' ),
                'bg_image'                      => array( '//placehold.it/2100x600', '2100', '600' ),
            ) );

            vodi_section_featured_video( $args );
        }
    }
}

if ( ! function_exists( 'vodi_home_v1_section_movies_carousel_aside_header_2' ) ) {
    function vodi_home_v1_section_movies_carousel_aside_header_2() {
        if( vodi_is_masvideos_activated() ) {
            $args = apply_filters( 'vodi_home_v1_section_movies_carousel_aside_header_1_default_args', array(
                'section_title'         => esc_html__( 'Action &amp; Drama Movies', 'vodi' ),
                'section_subtitle'      => '',
                'action_link'   => '#',
                'action_text'   => esc_html__( 'View All', 'vodi' ),
                'section_background'    => 'dark',
                'header_posisition'     => 'header-right',
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

            vodi_section_movies_carousel_aside_header( $args );
        }
    }
}

if ( ! function_exists( 'vodi_home_v1_section_movies_carousel_aside_header_3' ) ) {
    function vodi_home_v1_section_movies_carousel_aside_header_3() {
        if( vodi_is_masvideos_activated() ) {
            $args = apply_filters( 'vodi_home_v1_section_movies_carousel_aside_header_1_default_args', array(
                'section_title'         => esc_html__( 'Funniest Comedy Movies of 2018', 'vodi' ),
                'section_subtitle'      => '',
                'action_link'   => '#',
                'action_text'   => esc_html__( 'View All', 'vodi' ),
                'section_background'    => 'dark more-dark',
                'header_posisition'     => '',
                'movies_shortcode'      => 'mas_movies',
                'shortcode_atts'      => array(
                    'columns'               => '6',
                    'limit'                 => '15',
                ),
                'carousel_args'     => array(
                    'slidesToShow'      => 6,
                    'slidesToScroll'    => 6,
                    'dots'              => false,
                    'arrows'            => true,
                    'autoplay'          => false,
                ),
            ) );

            vodi_section_movies_carousel_aside_header( $args );
        }
    }
}

if ( ! function_exists( 'vodi_home_v1_section_featured_tv_show' ) ) {
    function vodi_home_v1_section_featured_tv_show() {
        if( vodi_is_masvideos_activated() ) {
            $args = apply_filters( 'vodi_home_v1_section_featured_tv_show_default_args', array(
                'feature_tv_show_pre_title'     => esc_html__( 'Featured', 'vodi' ),
                'feature_tv_show_title'         => esc_html__( 'Vikings', 'vodi' ),
                'feature_tv_show_subtitle'      => esc_html__( 'New Season 5 just flown in. Watch and debate.', 'vodi' ),
                'section_nav_links'      => array(
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

if ( ! function_exists( 'vodi_home_v1_video_section_aside_header' ) ) {
    function vodi_home_v1_video_section_aside_header() {
        if( vodi_is_masvideos_activated() ) {
            $args = apply_filters( 'vodi_home_v1_video_section_aside_header_default_args', array(
                'section_title'         => esc_html__( 'Popular Tv Series Right Now', 'vodi' ),
                'section_subtitle'      => '',
                'action_link'   => '#',
                'action_text'   => esc_html__( 'View All', 'vodi' ),
                'section_background'    => '',
                'shortcode_atts_1'      => array(
                    'columns'               => '4',
                    'limit'                 => '4',
                ),
                'shortcode_atts_2'      => array(
                    'columns'               => '6',
                    'limit'                 => '6',
                ),
            ) );

            vodi_video_section_aside_header( $args );
        }
    }
}

if ( ! function_exists( 'vodi_home_v1_section_videos_carousel_aside_header' ) ) {
    function vodi_home_v1_section_videos_carousel_aside_header() {
        if( vodi_is_masvideos_activated() ) {
            $args = apply_filters( 'vodi_home_v1_section_videos_carousel_aside_header_default_args', array(
                'section_title'         => esc_html__( 'Featured TV Episode Premieres', 'vodi' ),
                'section_subtitle'      => '',
                'action_link'   => '#',
                'action_text'   => esc_html__( 'View All', 'vodi' ),
                'section_background'    => 'dark',
                'header_posisition'     => 'header-right',
                'videos_shortcode'      => 'mas_videos',
                'shortcode_atts'      => array(
                    'columns'               => '4',
                    'limit'                 => '15',
                ),
                'carousel_args'     => array(
                    'slidesToShow'      => 4,
                    'slidesToScroll'    => 4,
                    'dots'              => false,
                    'arrows'            => true,
                    'autoplay'          => false,
                ),
            ) );

            vodi_section_videos_carousel_aside_header( $args );
        }
    }
}

if ( ! function_exists( 'vodi_top_new_arrivals' ) ) {
    function vodi_top_new_arrivals() {
        if( vodi_is_masvideos_activated() ) {
            $args = apply_filters( 'vodi_movies_list_default_args', array(
                'section_title_1'         => esc_html__( 'TOP 9 this Week', 'vodi' ),
                'section_title_2'         => esc_html__( 'Newest Movies', 'vodi' ),
                'section_nav_links_1'      => array(
                    array(
                        'nav_title'         => esc_html__( 'Movies', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                    array(
                        'nav_title'         => esc_html__( 'TV Series', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                ),
                'section_nav_links_2'      => array(
                    array(
                        'nav_title'         => esc_html__( 'Movies', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                    array(
                        'nav_title'         => esc_html__( 'TV Series', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                ),
                'featured_movie_id'     => '',
                'shortcode_atts_1'      => array(
                    'columns'               => '1',
                    'limit'                 => '8',
                ),
                'shortcode_atts_2'      => array(
                    'columns'               => '1',
                    'limit'                 => '8',
                ),
            ) );

            vodi_movies_list( $args );
        }
    }
}

if ( ! function_exists( 'vodi_home_v1_section_brand_video_carousel' ) ) {
    function vodi_home_v1_section_brand_video_carousel() {
        ?><section class="home-section brand-video-channel-carousel dark">
            <div class="container">
                <div class="brand-video-channel-carousel__inner" data-slick='{"slidesToShow": 7, "slidesToScroll": 7, "dots": false, "arrows": false }'>
                    <?php for( $i=0; $i<7; $i++ ): ?>
                        <div class="thumbnail"> 
                            <a href="#"> <img src="https://placehold.it/140x36" class="channel-thumbnail" width="600" height="600" scale="0"> </a>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        </section>  
        <?php
    }
} 
