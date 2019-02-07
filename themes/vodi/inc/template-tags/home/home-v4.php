<?php

/**
 * Template functions in Home v4
 *
 */

if ( ! function_exists( 'vodi_section_videos_live_coming_soon' ) ) {
    function vodi_section_videos_live_coming_soon() {
        ?><section class="home-section section-videos-live-coming-soon">
            <div class="container">
                <div class="section-videos-live-coming-soon__inner">
                    <div class="live-videos">
                        <h5 class="live-videos__title">Live Now</h5>
                        <div class="live-videos__inner">
                            <?php for( $i=0; $i<3; $i++ ):  ?>
                                <div class="live-video">
                                    <div class="live-video__poster">
                                        <a href="#"><img src="https://placehold.it/244x138" class="live-video__poster--image"></a>
                                    </div>
                                    <div class="live-video__content">
                                        <div class="live-video__info">
                                            <div class="live-video__meta">
                                                <span class="live-video__meta--live">Live</span>
                                                <span class="live-video__meta--sport-name">Racing Motor Sports</span>
                                                <span class="live-video__meta--location">Le Mans</span>
                                            </div>
                                            <h3 class="live-video__title"><a href="#">2018 Le Mans 24 Hour - Car GT Onboards, Race Timing <br>and Commentary</a></h3>
                                            <div class="streaming-meta">
                                                <div class="streaming-meta__chennal">on <span>XTremeSports3</span></div>
                                                <div class="streaming-meta__current-views">1.02K viewers</div>
                                            </div>
                                        </div>
                                        <div class="live-video__watch">
                                            <a href="#" class="live-video__watch--link">Watch now!</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>
                        <div class="home-section__action"><a href="#" class="home-section__action-link">View All</a></div>
                    </div>
                    <div class="coming-soon-videos">
                        <h5 class="coming-soon-videos__title">Coming Soon</h5>
                        <div class="coming-soon-videos__inner">
                            <?php for( $i=0; $i<3; $i++ ): ?>
                                <div class="coming-soon-video">
                                    <div class="coming-soon-video__release-date-time">
                                        <div class="coming-soon-video__release-time">12.00</div>
                                        <div class="coming-soon-video__release-date">17 June</div>
                                    </div>
                                    <div class="coming-soon-video__info">
                                        <div class="coming-soon-video__meta">
                                            <span class="coming-soon-video__meta--sport-name">Racing Motor Sports</span>
                                            <span class="coming-soon-video__meta--location">Le Mans</span>
                                        </div>
                                        <h3 class="coming-soon-video__title"><a href="#">MLB Baseball: Chicago Cubs at St. Louis Cardinals</a></h3>
                                        <div class="coming-soon-video__count-down">time left <span>1 day 10 hours 58 min 09 sec</span></div>
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>
                        <div class="home-section__action"><a href="#" class="home-section__action-link">View All</a></div>
                    </div>
                </div>
            </div>
        </section><?php
    }
}

if ( ! function_exists( 'vodi_home_v4_section_full_width_banner' ) ) {
    function vodi_home_v4_section_full_width_banner() {
        $args = apply_filters( 'vodi_home_v4_section_full_width_banner_default_args', array(
            'banner_image'          => array( '//placehold.it/1920x96', '1920', '96' ),
            'banner_link'           => '#',
            'el_class'              => '',
        ) );

        vodi_section_full_width_banner( $args );
    }
}

if ( ! function_exists( 'vodi_home_v4_videos_with_featured_video' ) ) {
    function vodi_home_v4_videos_with_featured_video() {
        if( vodi_is_masvideos_activated() ) {
            $args = apply_filters( 'vodi_home_v4_videos_with_featured_video_default_args', array(
                'section_title'         => esc_html__( 'Popular Replays', 'vodi' ),
                'section_nav_links'     => array(
                    array(
                        'title'         => esc_html__( 'Today', 'vodi' ),
                        'link'          => '#',
                    ),
                    array(
                        'title'         => esc_html__( 'This Week', 'vodi' ),
                        'link'          => '#',
                    ),
                    array(
                        'title'         => esc_html__( 'This Months', 'vodi' ),
                        'link'          => '#',
                    ),
                    array(
                        'title'         => esc_html__( 'Last 3 Months', 'vodi' ),
                        'link'          => '#',
                    ),
                ),
                'section_background'    => '',
                'el_class'              => 'sports-videos',
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

if ( ! function_exists( 'vodi_home_v4_video_section_1' ) ) {
    function vodi_home_v4_video_section_1() {
        if( vodi_is_masvideos_activated() ) {
            $args = apply_filters( 'vodi_home_v4_video_section_1_default_args', array(
                'section_title'         => esc_html__( 'Football World Cup', 'vodi' ),
                'section_nav_links'     => array(
                    array(
                        'title'         => esc_html__( 'View all', 'vodi' ),
                        'link'          => '#',
                    ),
                ),
                'section_background'    => '',
                'el_class'              => 'sports-videos',
                'videos_shortcode'      => 'mas_videos',
                'shortcode_atts'        => array(
                    'columns'               => '4',
                    'limit'                 => '4',
                ),
            ) );

            vodi_video_section( $args );
        }
    }
}

if ( ! function_exists( 'vodi_home_v4_video_section_2' ) ) {
    function vodi_home_v4_video_section_2() {
        if( vodi_is_masvideos_activated() ) {
            $args = apply_filters( 'vodi_home_v4_video_section_2_default_args', array(
                'section_title'         => esc_html__( 'Moto GP', 'vodi' ),
                'section_nav_links'     => array(
                    array(
                        'title'         => esc_html__( 'View all', 'vodi' ),
                        'link'          => '#',
                    ),
                ),
                'section_background'    => '',
                'el_class'              => 'sports-videos',
                'videos_shortcode'      => 'mas_videos',
                'shortcode_atts'        => array(
                    'columns'               => '4',
                    'limit'                 => '4',
                ),
            ) );

            vodi_video_section( $args );
        }
    }
}

if ( ! function_exists( 'vodi_blog_section_with_sidebar_v4' ) ) {
    function vodi_blog_section_with_sidebar_v4() {
        ?><section class="home-section home-blog-sidebar-section blog-section-2-with-sidebar blog-sidebar">
            <div class="container">
                <div class="home-blog-sidebar-section__inner">
                    <div class="home-blog-sidebar-section__sidebar-section">
                        <div class="home-blog-sidebar-section__sidebar-section-4-inner">
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
                                                        <span class="article__date">
                                                            <a href="#" rel="bookmark">
                                                                <time class="entry-date published" datetime="2013-01-10T20:15:40+00:00">Added: 17.05.2018</time>
                                                            </a>
                                                        </span>
                                                    </div>
                                                </div>
                                            </li>
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
                                                        <span class="entry__date">
                                                            <a href="#" rel="bookmark">
                                                                <time class="entry-date published" datetime="2013-01-10T20:15:40+00:00">Added: 17.05.2018</time>
                                                            </a>
                                                        </span>
                                                    </div>
                                                </div>
                                            </li>
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
                                                        <span class="article__date">
                                                            <a href="#" rel="bookmark">
                                                                <time class="entry-date published" datetime="2013-01-10T20:15:40+00:00">Added: 17.05.2018</time>
                                                            </a>
                                                        </span>
                                                    </div>
                                                </div>
                                            </li>
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
                                                        <span class="article__date">
                                                            <a href="#" rel="bookmark">
                                                                <time class="entry-date published" datetime="2013-01-10T20:15:40+00:00">Added: 17.05.2018</time>
                                                            </a>
                                                        </span>
                                                    </div>
                                                </div>
                                            </li>
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
                                                        <span class="article__date">
                                                            <a href="#" rel="bookmark">
                                                                <time class="entry-date published" datetime="2013-01-10T20:15:40+00:00">Added: 17.05.2018</time>
                                                            </a>
                                                        </span>
                                                    </div>
                                                </div>
                                            </li>
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
                                                        <span class="article__date">
                                                            <a href="#" rel="bookmark">
                                                                <time class="entry-date published" datetime="2013-01-10T20:15:40+00:00">Added: 17.05.2018</time>
                                                            </a>
                                                        </span>
                                                    </div>
                                                </div>
                                            </li>
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
                    <div class="home-blog-sidebar-section__blog-section blog-section-4">
                        <header class="home-section__flex-header">
                            <h2 class="section-title">Latest News</h2>
                            <div class="header-aside">
                                <a href="#">View all</a>
                            </div>
                        </header>
                        <div class="blog-section-2-with-sidebar__blog-section-2-content">
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
                                        </div>
                                    </header>
                                    <div class="article__excerpt">
                                        <p>Welcome to image alignment! The best way to demonstrate the ebb and flow of the various image positioning options is to nestle
                                        </p>
                                    </div>
                                </div>
                            </article>
                            <article class="article">
                                <div class="article__attachment">
                                    <div class="article__attachment--thumbnail">
                                        <a href="#"><img src="https://placehold.it/270x150" alt="Horizontal Featured Image"></a>
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
                            <article class="article">
                                <div class="article__attachment">
                                    <div class="article__attachment--thumbnail">
                                        <a href="#"><img src="https://placehold.it/270x150" alt="Horizontal Featured Image"></a>
                                    </div><!-- .article__thumbnail -->
                                </div>
                                <div class="article__summary">
                                    <header class="article__header">
                                        <h2 class="article__title entry-title">
                                            <a href="#" rel="bookmark">After The Fall Of Alantis, Some Of The Kingdom Evolved And Some Devolved</a>
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
                            <article class="article">
                                <div class="article__attachment">
                                    <div class="article__attachment--thumbnail">
                                        <a href="#"><img src="https://placehold.it/270x150" alt="Horizontal Featured Image"></a>
                                    </div><!-- .article__thumbnail -->
                                </div>
                                <div class="article__summary">
                                    <header class="article__header">
                                        <h2 class="article__title entry-title">
                                            <a href="#" rel="bookmark">NHRA at Bristol:Courtny Force 7th No 1 Spot Of 2018;Greg McPerson Takes 100th</a>
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
                            <article class="article">
                                <div class="article__attachment">
                                    <div class="article__attachment--thumbnail">
                                        <a href="#"><img src="https://placehold.it/270x150" alt="Horizontal Featured Image"></a>
                                    </div><!-- .article__thumbnail -->
                                </div>
                                <div class="article__summary">
                                    <header class="article__header">
                                        <h2 class="article__title entry-title">
                                            <a href="#" rel="bookmark">David Fales May Have Inside Track No 2 Job In Miami But Go Againts Goverment</a>
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
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php
    }
}

if ( ! function_exists( 'vodi_home_v4_section_event_category_list' ) ) {
    function vodi_home_v4_section_event_category_list() {
        if( vodi_is_masvideos_activated() ) {
            $args = apply_filters( 'vodi_home_v4_section_event_category_list_default_args', array(
                'section_title'         => esc_html__( 'Trending Leagues', 'vodi' ),
                'section_nav_links'     => array(
                    array(
                        'title'         => esc_html__( 'Today', 'vodi' ),
                        'link'          => '#',
                    ),
                    array(
                        'title'         => esc_html__( 'This Week', 'vodi' ),
                        'link'          => '#',
                    ),
                    array(
                        'title'         => esc_html__( 'This Months', 'vodi' ),
                        'link'          => '#',
                    ),
                    array(
                        'title'         => esc_html__( 'Last 3 Months', 'vodi' ),
                        'link'          => '#',
                    ),
                ),
                'el_class'              => '',
            ) );
            vodi_section_event_category_list( $args );
        }
    }
}

if ( ! function_exists( 'vodi_home_v4_video_section_3' ) ) {
    function vodi_home_v4_video_section_3() {
        if( vodi_is_masvideos_activated() ) {
            $args = apply_filters( 'vodi_home_v4_video_section_3_default_args', array(
                'section_title'         => esc_html__( 'Recommended for You', 'vodi' ),
                'section_background'    => '',
                'el_class'              => 'sports-videos',
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