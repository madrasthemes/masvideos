<?php

/**
 * Template functions in Home v9
 *
 */

if ( ! function_exists( 'vodi_section_blog_1_4_columns' ) ) {
    function vodi_section_blog_1_4_columns() {
        ?><section class="home-section home-section__game-blog-1-4-columns section-game-blog-1-4-columns dark negative-bg" style=" background-image: url( https://placehold.it/2100x1200/1a2439 );">
            <div class="container">
                <div class="section-game-blog-1-4-columns__inner">
                    <div class="articles columns-1">
                        <div class="articles__inner">
                            <article class="article">
                                <div class="article__attachment">
                                    <div class="article__attachment--thumbnail">
                                        <a href="#"><img src="https://placehold.it/697x677" alt="Featured Image"></a>
                                    </div>
                                </div>
                                <div class="article__summary">
                                    <header class="article__header">
                                        <h2 class="article__title entry-title">
                                            <a href="#" rel="bookmark">Thorin Used Anti-glay Slur</a>
                                        </h2>
                                        <div class="article__meta">
                                            <div class="article__categories">
                                                <a href="#">News</a>
                                            </div>
                                            <span class="article__date">
                                                <a href="#">
                                                    <time class="entry-date published updated">
                                                        3 days ago
                                                    </time>
                                                </a>
                                            </span>
                                            <div class="article__comments comments-link">
                                                <span class="comments-link">
                                                    <a href="#">5 Comments</a>
                                                </span>
                                            </div>
                                        </div>
                                    </header>
                                    <div class="article__excerpt">
                                        <p>Gamer has come under fire for defending recent...</p>
                                    </div>
                                </div>
                            </article>
                        </div>
                    </div>
                    <div class="articles columns-4">
                        <div class="articles__inner">
                            <?php for( $i=0; $i<4; $i++ ):  ?>
                                <article class="article">
                                    <div class="article__attachment">
                                        <div class="article__attachment--thumbnail">
                                            <a href="#"><img src="https://placehold.it/697x677" alt="Featured Image"></a>
                                        </div><!-- .article__thumbnail -->
                                    </div>
                                    <div class="article__summary">
                                        <header class="article__header">
                                            <h2 class="article__title entry-title">
                                                <a href="#" rel="bookmark">Thorin Used Anti-glay Slur</a>
                                            </h2>
                                            <div class="article__meta">
                                                <div class="article__categories">
                                                    <a href="#">News</a>
                                                </div>
                                                <span class="article__date">
                                                    <a href="#">
                                                        <time class="entry-date published updated">
                                                            3 days ago
                                                        </time>
                                                    </a>
                                                </span>
                                                <div class="article__comments comments-link">
                                                    <span class="comments-link">
                                                        <a href="#">5 Comments</a>
                                                    </span>
                                                </div>
                                            </div>
                                        </header>
                                        <div class="article__excerpt">
                                            <p>Gamer has come under fire for defending recent...</p>
                                        </div>
                                    </div>
                                </article>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php
    }
}

if ( ! function_exists( 'vodi_home_v9_section_full_width_banner' ) ) {
    function vodi_home_v9_section_full_width_banner() {
        $args = apply_filters( 'vodi_home_v9_section_full_width_banner_default_args', array(
            'banner_image'          => array( '//placehold.it/1920x96', '1920', '96' ),
            'banner_link'           => '#',
            'el_class'              => '',
        ) );

        vodi_section_full_width_banner( $args );
    }
}

if ( ! function_exists( 'vodi_section_live_game_players_1' ) ) {
    function vodi_section_live_game_players_1() {
        ?><section class="home-section section-live-game-players-list dark style-2">
            <div class="container">
                <div class="section-live-game-players-list__inner">
                    <header class="home-section__flex-header">
                        <h2 class="home-section__title section-title">Stream Team</h2>
                        <div class="header-aside">
                            <a href="#">View all</a>
                        </div>
                    </header>
                    <div class="live-game-players-list columns-5">
                        <?php for( $i=0; $i<5; $i++ ):  ?>
                            <div class="live-game-player">
                                <div class="live-game-player__inner">
                                    <div class="live-game-player__social-network-page-links">
                                        <a href="#" class="live-game-player__social-network-page-link facebook">
                                            <i class="fab fa-facebook-f"></i>
                                        </a>
                                        <a href="#" class="live-game-player__social-network-page-link youtube">
                                            <i class="fab fa-youtube"></i>
                                        </a>
                                    </div>
                                    <div class="live-game-player__profile">
                                        <div class="live-game-player__thumbnail">
                                            <img src="https://placehold.it/100x100" alt="" class="live-game-player__thumbnail--image">
                                            <div class="live-game-player__status">on:<span>4115</span></div>
                                        </div>
                                        <div class="live-game-player__profile--info">
                                            <h3 class="live-game-player__name"><a href="#">Potato</a></h3>
                                            <p class="live-game-player__about">Anytime a stream is late, its just a secret stream</p>
                                            <span class="game-now-playing">League of Legends</span>
                                            <div class="watch-live-playing-game">
                                                <a href="#" class="watch-live-playing-game__link">Watch Live</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </section><?php
    }
}

if ( ! function_exists( 'vodi_home_v9_section_featured_tv_show' ) ) {
    function vodi_home_v9_section_featured_tv_show() {
        if( vodi_is_masvideos_activated() ) {
            $args = apply_filters( 'vodi_home_v9_section_featured_tv_show_default_args', array(
                'feature_tv_show_title'         => esc_html__( '#GameplaySeries', 'vodi' ),
                'feature_tv_show_subtitle'      => esc_html__( 'Check our newest videos from below fresh games series.', 'vodi' ),
                'section_nav_links'             => array(
                    array(
                        'nav_title'         => esc_html__( 'Battlefield 1', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                    array(
                        'nav_title'         => esc_html__( 'God of War', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                    array(
                        'nav_title'         => esc_html__( 'Assasin’s Creed', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                    array(
                        'nav_title'         => esc_html__( 'Rainbow Six', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                    array(
                        'nav_title'         => esc_html__( 'Hidden', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                    array(
                        'nav_title'         => esc_html__( 'Agenda', 'vodi' ),
                        'nav_link'          => '#',
                    ),
                ),
                'section_background'            => '',
                'bg_image'                      => array( '//placehold.it/2100x675', '2100', '675' ),
                'el_class'                      => 'sports-videos',
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

if ( ! function_exists( 'vodi_home_v9_video_section_aside_header' ) ) {
    function vodi_home_v9_video_section_aside_header() {
        if( vodi_is_masvideos_activated() ) {
            $args = apply_filters( 'vodi_home_v9_video_section_aside_header_default_args', array(
                'section_title'         => esc_html__( 'Just Fresh from Our YouTube Channel', 'vodi' ),
                'section_subtitle'      => '',
                'action_link'   => '#',
                'action_text'   => esc_html__( 'View All', 'vodi' ),
                'section_background'    => 'dark less-dark',
                'el_class'              => 'column-5_6',
                'shortcode_atts_1'      => array(
                    'columns'               => '5',
                    'limit'                 => '5',
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

if ( ! function_exists( 'vodi_blog_section_5_with_sidebar' ) ) {
    function vodi_blog_section_5_with_sidebar() {
        ?><section class="home-section home-blog-sidebar-section blog-section-5-with-sidebar blog-sidebar left-sidebar dark style-2">
            <div class="container">
                <div class="home-blog-sidebar-section__inner">
                    <div class="home-blog-sidebar-section__blog-section">
                        <header class="home-section__flex-header">
                            <h2 class="section-title">Actors Life</h2>
                            <div class="header-aside">
                                <a href="#">View all</a>
                            </div>
                        </header>
                        <div class="blog-section-5-with-sidebar__blog-section-5-content">
                            <article class="article featured">
                                <div class="article__attachment">        
                                    <div class="article__attachment--thumbnail">
                                        <a href="#">
                                            <img src="https://placehold.it/990x440">
                                        </a>
                                    </div><!-- .article__thumbnail -->
                                </div>
                                <div class="article__summary">
                                    <header class="article__header">
                                        <h2 class="article__title entry-title">
                                            <a href="#">How Wizard Actor Has Another Digital Comics Role</a>
                                        </h2>
                                        <div class="article__meta">
                                            <div class="article__categories">
                                                <a href="#">Hot Rumors</a>
                                            </div>
                                            <span class="article__date">
                                                <a href="#">
                                                    <time class="entry-date published">Added: 15.05.2018</time>
                                                </a>
                                            </span>
                                            <div class="article__comments comments-link">
                                                <span class="comments-link">
                                                    <a href="#">5 Comments</a>
                                                </span>
                                            </div>
                                        </div>
                                    </header>
                                    <div class="article__excerpt">
                                        <p>Welcome to image alignment! The best way to demonstrate the ebb and flow of the various image positioning options is to nestle them snuggly among
                                        </p>
                                    </div>
                                </div>
                            </article>
                            <?php for( $i=0; $i<4; $i++ ):  ?>
                                <article class="article">
                                    <div class="article__attachment">
                                        <div class="article__attachment--thumbnail">
                                            <a href="#"><img src="https://placehold.it/274x155" alt="Horizontal Featured Image"></a>
                                        </div><!-- .article__thumbnail -->
                                    </div>
                                    <div class="article__summary">
                                        <header class="article__header">
                                            <h2 class="article__title entry-title">
                                                <a href="#" rel="bookmark">How Martin Absense Affects The Other Sprinting Fear Characters On Plan</a>
                                            </h2>
                                            <div class="article__meta">
                                                <div class="article__categories">
                                                    <a href="#">Hor Rumors</a>
                                                </div>
                                                <span class="article__date">
                                                    <a href="#">
                                                        <time class="entry-date published updated">
                                                            Added: 17/05/2018
                                                        </time>
                                                    </a>
                                                </span>
                                                <div class="article__comments comments-link">
                                                    <span class="comments-link">
                                                        <a href="#">5 Comments</a>
                                                    </span>
                                                </div>
                                            </div>
                                        </header>
                                        <div class="article__excerpt">
                                            <p>This post should display a featured image, if the theme supports it.</p>
                                        </div>
                                    </div>
                                </article>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div class="home-blog-sidebar-section__sidebar-section">
                        <div class="home-blog-sidebar-section__sidebar-section-inner">
                            <div class="widget vodi_posts_widget">
                                <div class="widget-header">
                                    <span class="widget-title">
                                        What's hot
                                    </span>
                                    <div class="header-aside">
                                        <a href="#">View all</a>
                                    </div>
                                </div>
                                <div id="vodi_recent_posts_widget" class="widget vodi_posts_widget">
                                    <div class="style-1">
                                        <ul>
                                            <?php for( $i=0; $i<6; $i++ ):  ?>
                                                <li class="has-post-thumbnail">
                                                    <a href="#" class="post-thumbnail">
                                                        <img src="https://via.placeholder.com/115x75"> 
                                                    </a>
                                                    <div class="post-content">
                                                        <h2 class="entry-title">
                                                            <a href="#">
                                                                Sunday's man ud transfer rumores
                                                            </a>
                                                        </h2>
                                                        <div class="entry-meta">
                                                            <span class="entry-categories">
                                                                <span class="entry-cats-list"><a href="# rel="tag">futbal</a></span>
                                                            </span>
                                                            <span class="article__time">
                                                                <a href="#" rel="bookmark">
                                                                    <time class="entry-time published" datetime="2013-01-10T20:15:40+00:00">2 hours ago</time>
                                                                </a>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </li>
                                            <?php endfor; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="widget ad-banner">
                                <a href="#"><img src="https://placehold.it/350x277"></a>
                            </div>
                            <div class="widget widget_recent_comments">
                                <div class="widget-header">
                                    <span class="widget-title">Recent Comments</span>
                                    <div class="header-aside">
                                        <a href="#">View all</a>
                                    </div>
                                </div>
                                <ul id="recentcomments">
                                    <li class="recentcomments">
                                        <span class="comment-author-link">
                                            <a href="#" class="url">John Green</a>
                                        </span> on <a href="#">How turned Hot wheels into sheer genius</a>
                                    </li>
                                    <li class="recentcomments">
                                        <span class="comment-author-link">
                                            <a href="#" class="url">Anna McQueen</a>
                                        </span> on <a href="#">5 Best & 5 Worst MCU Characters</a>
                                    </li>
                                    <li class="recentcomments">
                                        <span class="comment-author-link">
                                            <a href="#" class="url">Carolina Owen</a>
                                        </span> on <a href="#">Nagel Nadel wins 11th French Open title</a>
                                    </li>
                                    <li class="recentcomments">
                                        <span class="comment-author-link">
                                            <a href="#" class="url">Dwayne Nicolson</a>
                                        </span> on <a href="#">Bolt's London Olympic spikes stolen</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section><?php
    }
}

if ( ! function_exists( 'vodi_section_live_game_players_2' ) ) {
    function vodi_section_live_game_players_2() {
        ?><section class="home-section section-live-game-players-list dark style-2">
            <div class="container">
                <div class="section-live-game-players-list__inner">
                    <header class="home-section__flex-header">
                        <h2 class="home-section__title section-title">Our Friends</h2>
                        <div class="header-aside">
                            <a href="#">View all</a>
                        </div>
                    </header>
                    <div class="live-game-players-list columns-6">
                        <?php for( $i=0; $i<6; $i++ ):  ?>
                            <div class="live-game-player">
                                <div class="live-game-player__inner">
                                    <div class="live-game-player__social-network-page-links">
                                        <a href="#" class="live-game-player__social-network-page-link facebook">
                                            <i class="fab fa-facebook-f"></i>
                                        </a>
                                        <a href="#" class="live-game-player__social-network-page-link youtube">
                                            <i class="fab fa-youtube"></i>
                                        </a>
                                    </div>
                                    <div class="live-game-player__profile">
                                        <div class="live-game-player__thumbnail">
                                            <img src="https://placehold.it/100x100" alt="" class="live-game-player__thumbnail--image">
                                            <div class="live-game-player__status">on:<span>4115</span></div>
                                        </div>
                                        <div class="live-game-player__profile--info">
                                            <h3 class="live-game-player__name"><a href="#">Potato</a></h3>
                                            <p class="live-game-player__about">Anytime a stream is late, its just a secret stream</p>
                                            <span class="game-now-playing">League of Legends</span>
                                            <div class="watch-live-playing-game">
                                                <a href="#" class="watch-live-playing-game__link">Watch Live</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </section><?php
    }
}

if ( ! function_exists( 'vodi_home_v9_section_brand_video_carousel' ) ) {
    function vodi_home_v9_section_brand_video_carousel() {
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