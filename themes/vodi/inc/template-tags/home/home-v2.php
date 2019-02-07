<?php

/**
 * Template functions in Home v2
 *
 */

if ( ! function_exists( 'vodi_home_v2_slider_movies' ) ) {
    function vodi_home_v2_slider_movies() {
        ?><div class="movie-slider">
            <div class="slider-movies-list">
                <div class="slider-movie" style="background-image: url( https://placehold.it/640x700 );">
                    <div class="slider-movie__hover">
                        <div class="slider-movie__hover_watch-now">
                            <a class="watch-now-btn" href="#">
                                <div class="watch-now-btn-bg">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="49px" height="54px">
                                        <path fill-rule="evenodd"  fill="rgb(255, 255, 255)"d="M2.000,51.000 C-0.150,46.056 0.424,8.178 2.000,5.000 C3.282,2.414 5.732,0.351 9.000,1.000 C19.348,3.054 45.393,19.419 48.000,25.000 C49.019,27.182 48.794,28.758 48.000,31.000 C46.967,33.919 13.512,54.257 9.000,54.000 C6.740,53.873 3.005,53.311 2.000,51.000 Z"/>
                                    </svg>
                                </div>
                                <div class="watch-now-txt">Watch Now</div>
                            </a>
                        </div>
                        <div class="slider-movie__title">
                            <a href="#"><h1>Project Cars 3</h1></a>
                        </div>
                        <div class="slider-movie__meta">
                            <ul class="movie-details">
                                <li class="movie-release-info">23 January, 2017 (USA)</li>
                                <li class="movie-duration">142 min.</li>
                                <li class="movie-genre">Comedy</li>
                            </ul>
                        </div>
                        <div class="movie-description">
                            <p>John Doe and his IMF team, along with some familiar allies, race against time after a mission gone wrong.</p>
                        </div>
                        <div class="slider-movie__hover_action">
                            <a href="#" class="watch-now">WATCH NOW</a>
                            <a href="#" class="add-to-playlist">+ PLAYLIST</a>
                        </div>
                    </div>
                </div>

                <div class="slider-movie" style="background-image: url( https://placehold.it/640x700 );">
                    <div class="slider-movie__hover">
                        <div class="slider-movie__hover_watch-now">
                            <a class="watch-now-btn" href="#">
                                <div class="watch-now-btn-bg">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="49px" height="54px">
                                        <path fill-rule="evenodd"  fill="rgb(255, 255, 255)"d="M2.000,51.000 C-0.150,46.056 0.424,8.178 2.000,5.000 C3.282,2.414 5.732,0.351 9.000,1.000 C19.348,3.054 45.393,19.419 48.000,25.000 C49.019,27.182 48.794,28.758 48.000,31.000 C46.967,33.919 13.512,54.257 9.000,54.000 C6.740,53.873 3.005,53.311 2.000,51.000 Z"/>
                                    </svg>
                                </div>
                                <div class="watch-now-txt">Watch Now</div>
                            </a>
                        </div>
                        <div class="slider-movie__title">
                            <a href="#"><h1>Delta Bravo</h1></a>
                        </div>
                        <div class="slider-movie__meta">
                            <ul class="movie-details">
                                <li class="movie-release-info">15 October, 2018 (USA)</li>
                                <li class="movie-duration">115 min.</li>
                                <li class="movie-genre">Action, Drama</li>
                            </ul>
                        </div>
                        <div class="movie-description">
                            <p>John Doe and his IMF team, along with some familiar allies, race against time after a mission gone wrong.</p>
                        </div>
                        <div class="slider-movie__hover_action">
                            <a href="#" class="watch-now">WATCH NOW</a>
                            <a href="#" class="add-to-playlist">+ PLAYLIST</a>
                        </div>
                    </div>
                </div>
                <div class="slider-movie" style="background-image: url( https://placehold.it/640x700 );">
                    <div class="slider-movie__hover">
                        <div class="slider-movie__hover_watch-now">
                            <a class="watch-now-btn" href="#">
                                <div class="watch-now-btn-bg">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="49px" height="54px">
                                        <path fill-rule="evenodd"  fill="rgb(255, 255, 255)"d="M2.000,51.000 C-0.150,46.056 0.424,8.178 2.000,5.000 C3.282,2.414 5.732,0.351 9.000,1.000 C19.348,3.054 45.393,19.419 48.000,25.000 C49.019,27.182 48.794,28.758 48.000,31.000 C46.967,33.919 13.512,54.257 9.000,54.000 C6.740,53.873 3.005,53.311 2.000,51.000 Z"/>
                                    </svg>
                                </div>
                                <div class="watch-now-txt">Watch Now</div>
                            </a>
                        </div>
                        <div class="slider-movie__title">
                            <a href="#"><h1>Rogue One Definition of a War Story</h1></a>
                        </div>
                        <div class="slider-movie__meta">
                            <ul class="movie-details">
                                <li class="movie-release-info">29 May, 2018 (USA)</li>
                                <li class="movie-duration">190 min.</li>
                                <li class="movie-genre">Thiller</li>
                            </ul>
                        </div>
                        <div class="movie-description">
                            <p>John Doe and his IMF team, along with some familiar allies, race against time after a mission gone wrong.</p>
                        </div>
                        <div class="slider-movie__hover_action">
                            <a href="#" class="watch-now">WATCH NOW</a>
                            <a href="#" class="add-to-playlist">+ PLAYLIST</a>
                        </div>
                    </div>
                </div>
            </div>
        </div> <?php
    }
}

if ( ! function_exists( 'vodi_home_v2_movie_section_aside_header' ) ) {
    function vodi_home_v2_movie_section_aside_header() {
        if( vodi_is_masvideos_activated() ) {
            $args = apply_filters( 'vodi_home_v2_movie_section_aside_header_default_args', array(
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

if ( ! function_exists( 'vodi_home_v2_videos_with_featured_video_1' ) ) {
    function vodi_home_v2_videos_with_featured_video_1() {
        if( vodi_is_masvideos_activated() ) {
            $args = apply_filters( 'vodi_home_v2_videos_with_featured_video_1_default_args', array(
                'section_title'         => esc_html__( 'Newest Episodes', 'vodi' ),
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
                'section_background'    => '',
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

if ( ! function_exists( 'vodi_home_v2_video_section_1' ) ) {
    function vodi_home_v2_video_section_1() {
        if( vodi_is_masvideos_activated() ) {
            $args = apply_filters( 'vodi_home_v2_video_section_1_default_args', array(
                'section_title'         => esc_html__( 'Newest Episodes', 'vodi' ),
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
                'section_background'    => '',
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

if ( ! function_exists( 'vodi_home_v2_featured_movies_carousel' ) ) {
    function vodi_home_v2_featured_movies_carousel() {
        if( vodi_is_masvideos_activated() ) {
            $args = apply_filters( 'vodi_home_v2_featured_movies_carousel_default_args', array(
                'feature_movie_pre_title'       => esc_html__( 'Featured', 'vodi' ),
                'feature_movie_title'           => esc_html__( 'Discover\'18', 'vodi' ),
                'feature_movie_subtitle'        => esc_html__( 'New Movies that are already playing in theaters and watch them online now.', 'vodi' ),
                'section_nav_links'             => array(
                    array(
                        'nav_title'         => esc_html__( 'New Arrivals', 'vodi' ),
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
                        'nav_title'         => esc_html__( 'Sci-Fi', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                    array(
                        'nav_title'         => esc_html__( 'Action', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                    array(
                        'nav_title'         => esc_html__( 'Thriller', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                    array(
                        'nav_title'         => esc_html__( 'Horror', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                ),
                'section_background'            => '',
                'bg_image'                      => array( '//placehold.it/1610x757/f5f5f5/f5f5f5', '1610', '757' ),
                'movies_shortcode'              => 'mas_movies',
                'shortcode_atts'                => array(
                    'columns'               => '8',
                    'limit'                 => '15',
                ),
                'carousel_args'                 => array(
                    'slidesToShow'          => 8,
                    'slidesToScroll'        => 8,
                    'dots'                  => false,
                    'arrows'                => true,
                    'autoplay'              => false,
                ),
            ) );

            vodi_featured_movies_carousel( $args );
        }
    }
}

if ( ! function_exists( 'vodi_home_v2_section_featured_video' ) ) {
    function vodi_home_v2_section_featured_video() {
        if( vodi_is_masvideos_activated() ) {
            $args = apply_filters( 'vodi_home_v2_section_featured_video_default_args', array(
                'feature_video_title'           => esc_html__( 'Chaos Takes Control', 'vodi' ),
                'feature_video_subtitle'        => esc_html__( 'Curabitur nec congue lorem. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Pellentesque dapibus, neque non accumsan porttitor.', 'vodi' ),
                'feature_video_action_icon'     => '<i class="fas fa-play"></i>',
                'video_id'                      => '144',
                'image'                         => array( '//placehold.it/187x59', '187', '59' ),
                'bg_image'                      => array( '//placehold.it/2100x600', '2100', '600' ),
            ) );

            vodi_section_featured_video( $args );
        }
    }
}

if ( ! function_exists( 'vodi_home_v2_section_movies_carousel_aside_header_1' ) ) {
    function vodi_home_v2_section_movies_carousel_aside_header_1() {
        if( vodi_is_masvideos_activated() ) {
            $args = apply_filters( 'vodi_home_v2_section_movies_carousel_aside_header_1_default_args', array(
                'section_title'         => esc_html__( 'Romantic for Valentines Day', 'vodi' ),
                'section_subtitle'      => '',
                'action_link'   => '#',
                'action_text'   => esc_html__( 'View All', 'vodi' ),
                'section_background'    => '',
                'header_posisition'     => '',
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

if ( ! function_exists( 'vodi_home_v2_section_movies_carousel_aside_header_2' ) ) {
    function vodi_home_v2_section_movies_carousel_aside_header_2() {
        if( vodi_is_masvideos_activated() ) {
            $args = apply_filters( 'vodi_home_v2_section_movies_carousel_aside_header_2_default_args', array(
                'section_title'         => esc_html__( 'Action & Drama Movies', 'vodi' ),
                'section_subtitle'      => '',
                'action_link'   => '#',
                'action_text'   => esc_html__( 'View All', 'vodi' ),
                'section_background'    => 'section-movies-carousel__with-bg',
                'header_posisition'     => 'header-right',
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

if ( ! function_exists( 'vodi_home_v2_videos_with_featured_video_2' ) ) {
    function vodi_home_v2_videos_with_featured_video_2() {
        if( vodi_is_masvideos_activated() ) {
            $args = apply_filters( 'vodi_home_v2_videos_with_featured_video_2_default_args', array(
                'section_title'         => esc_html__( 'Featured TV Episode Premieres', 'vodi' ),
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
                'section_background'    => 'home-section-videos-with-featured-video-with-bg',
                'shortcode_atts_1'          => array(
                    'columns'               => '1',
                    'limit'                 => '1',
                ),
                'shortcode_atts_2'          => array(
                    'columns'               => '3',
                    'limit'                 => '6',
                ),
            ) );

            vodi_videos_with_featured_video( $args );
        }
        
    }
}

if ( ! function_exists( 'vodi_home_v2_video_section_2' ) ) {
    function vodi_home_v2_video_section_2() {
        if( vodi_is_masvideos_activated() ) {
            $args = apply_filters( 'vodi_home_v2_video_section_2_default_args', array(
                'section_title'         => esc_html__( 'Continue Watching - TV Series', 'vodi' ),
                'section_nav_links'     => array(
                    array(
                        'nav_title'         => esc_html__( 'Movies', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                    array(
                        'nav_title'         => esc_html__( 'TV Series', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                    array(
                        'nav_title'         => esc_html__( 'TV Shows', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                    array(
                        'nav_title'         => esc_html__( 'Kids', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                ),
                'section_background'    => '',
                'videos_shortcode'      => 'mas_videos',
                'shortcode_atts'        => array(
                    'columns'               => '5',
                    'limit'                 => '5',
                ),
            ) );

            vodi_video_section( $args );
        }
    }
}

if ( ! function_exists( 'vodi_home_v2_section_brand_video_carousel' ) ) {
    function vodi_home_v2_section_brand_video_carousel() {
        ?><section class="home-section brand-video-channel-carousel light">
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
