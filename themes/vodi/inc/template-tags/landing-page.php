<?php

if ( ! function_exists( 'vodi_lp_featured_section' ) ) {
    function vodi_lp_featured_section() {
    	?>  <section class="lp-featured-section">
                <div class="container">
                    <div class="lp-featured-section__inner">
                        <div class="lp-featured-section__details">
                            <div class="section-header">
                                <h3 class="section-title">Westworld</h3>
                                <span class="section-sub-title">
                                    5 Awarded Thriller
                                </span>
                                <p class="discription">
                                    Curabitur nec congue lorem. Class aptent  taciti sociosqe ad litora torquent per conubia nostra, per inceptos himenaeos  
                                </p>  
                            </div>
                            <div class="lp-featured-section__link">
                                <button type="button" class="btn-secondary">Watch Trailer</button>
                            </div>
                        </div>
                        <div class="lp-featured-section__banner">
                            <div class="banner-inner">
                                <a href="#">
                                    <img src="https://placehold.it/440x440">
                                </a> 
                                <div class="label-new">
                                    <span>NEW</span>
                                </div>   
                            </div>    
                        </div>
                    </div>
                </div>
            </section>
        <?php
    }
}

if ( ! function_exists( 'vodi_section_landing_page_movies' ) ) {
    function vodi_section_landing_page_movies() {
    	?> <section class="section-landing-page-movies">
            <div class="container">
                <div class="section-landing-page-movies__inner">
                    <header class="section-landing-page-movies__section-header">
                        <h2 class="section-title">Your favorite movies & shows in one place</h2>
                        <p class="section-sub-title">Choose from the list of best selection movies, series, children's programs and vodi exclusive</p>
                    </header>
                    <div class="section-landing-page-movies__carousel">
                        <div class="movies-carousel">
                            <div class="movies-carousel__inner" data-slick='{"slidesToShow": 10, "slidesToScroll": 10, "dots": true, "arrows": false,"infinite": false, "appenddots": "#section-movies-carousel-ID-1 .section-movies-carousel__custom-dots"}'>
                                <?php for( $i=0; $i<40; $i++ ): ?>
                                <?php get_template_part( 'templates/contents/content', 'movie' ); ?>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                    <div class="discover-all-btn">
                        <button type="button" class="btn-secondary">Discover all movies & shows</button>
                    </div>
                </div>
            </div>        
        </section>
        <?php
    }
}

if ( ! function_exists( 'vodi_section_lp_features' ) ) {
    function vodi_section_lp_features() {
    	?><section class="section-lp__features tabs-section">
            <div class="container">
                <div class="tabs-section-inner">
                    <ul class="nav nav-tabs">
                        <li class="nav-link">
                            <a data-toggle="tab" href="#Watch-everywhere" class="active show">
                                <svg xmlns="http://www.w3.org/2000/svg" width="68" height="39"><path d="M16.2 0C30.5 0 44.9 0 59.2 0 59.2 4.5 59.2 9.1 59.2 13.6 61.1 13.6 63 13.6 64.9 13.6 66.1 13.6 67.1 13.6 67.7 14.3 68 14.8 68 15.5 68 16.3 68 17.6 68 18.8 68 20.1 68 24.4 68 28.8 68 33.2 68 34.3 68 35.3 68 36.4 68 36.9 68.1 37.5 67.9 37.9 67.7 38.4 67.3 38.6 67 38.9 63.7 38.9 60.4 38.9 57 38.9 55.9 38.9 54 39.2 53.5 38.5 52.9 37.8 53.1 36.4 53.1 35.1 53.1 32.6 53.1 30.1 53.1 27.6 43.5 27.6 33.9 27.6 24.3 27.6 24.3 30.1 24.3 32.7 24.3 35.2 24.3 36.7 24.4 38 23.6 38.6 22.7 39.2 20.1 38.9 18.6 38.9 14.3 38.9 10.1 38.9 5.9 38.9 4.2 38.9 1.3 39.3 0.6 38.3 -0.3 37.2 0.1 33.5 0.1 31.5 0.1 25.7 0.1 19.9 0.1 14.1 0.1 12.6 0.1 11.2 0.1 9.7 0.1 8.5 0.1 7.5 0.7 6.9 1.5 6 3.4 6.3 5 6.3 8.8 6.3 12.5 6.3 16.2 6.3 16.2 4.2 16.2 2.1 16.2 0ZM17.6 1.4C17.6 3 17.6 4.7 17.6 6.3 19.5 6.3 23 5.9 23.8 7 24.6 8.1 24.3 10.7 24.3 12.5 24.3 17.1 24.3 21.6 24.3 26.2 33.9 26.2 43.5 26.2 53.1 26.2 53.1 23.4 53.1 20.6 53.1 17.7 53.1 16.4 52.9 14.9 53.5 14.1 54.2 13.3 56.3 13.6 57.8 13.6 57.8 9.5 57.8 5.5 57.8 1.4 44.4 1.4 31 1.4 17.6 1.4ZM2 7.7C1.9 7.7 1.9 7.7 1.8 7.7 1.7 7.9 1.6 8 1.5 8.1 1.5 14.7 1.5 21.3 1.5 27.9 1.5 30 1.5 32 1.5 34.1 1.5 35.1 1.4 36.5 1.6 37.4 2.4 37.8 5.1 37.6 6.3 37.6 10.2 37.6 14.1 37.6 18 37.6 19.1 37.6 20.3 37.6 21.5 37.6 22.1 37.6 22.6 37.7 22.8 37.2 23.1 36.8 22.9 35.7 22.9 35.2 22.9 33.3 22.9 31.5 22.9 29.7 22.9 25 22.9 20.2 22.9 15.5 22.9 13.9 22.9 12.4 22.9 10.9 22.9 9.9 23 8.9 22.9 8.1 22.7 7.9 22.7 7.8 22.3 7.7 22 7.6 21.2 7.7 20.8 7.7 19.5 7.7 18.2 7.7 16.9 7.7 11.9 7.7 6.9 7.7 2 7.7ZM54.7 15C54.4 15.4 54.5 16.3 54.5 17 54.5 18.4 54.5 19.9 54.5 21.3 54.5 26.7 54.5 32.1 54.5 37.5 54.6 37.6 54.7 37.6 54.7 37.7 57.6 37.7 60.5 37.7 63.4 37.7 64 37.7 66.1 37.9 66.5 37.6 66.7 37.3 66.6 36.5 66.6 36.1 66.6 34.7 66.6 33.3 66.6 31.9 66.6 26.3 66.6 20.8 66.6 15.2 66.5 15.1 66.5 15.1 66.4 15 62.5 15 58.6 15 54.7 15ZM58.9 16.9C59.7 16.9 60.5 16.9 61.3 16.9 61.7 16.9 62.2 16.9 62.4 17 62.6 17.2 62.6 17.3 62.6 17.6 62.6 17.7 62.6 17.6 62.6 17.7 61.9 18.1 60.7 18 59.7 18 59.2 18 58.7 18 58.5 17.6 58.3 17.3 58.7 17.1 58.9 16.9ZM48.6 34.9C41.2 34.9 33.7 34.9 26.2 34.9 26.2 34.4 26.2 34 26.2 33.5 26.8 33.3 27.7 33.5 28.4 33.5 29.9 33.5 31.5 33.5 33.1 33.5 38.2 33.5 43.4 33.5 48.6 33.5 48.7 33.8 48.6 34.4 48.6 34.9ZM12 33.8C13.2 33.7 13.5 34.6 13.2 35.6 13 35.8 12.7 36 12.6 36.1 11.5 36.2 11.6 35.9 11.1 35.4 11 34.3 11.2 34.3 11.8 33.8 11.9 33.8 12 33.8 12 33.8Z"/></svg>
                                <span>Watch everywhere</span>
                            </a>
                        </li>
                        <li class="nav-link">
                            <a data-toggle="tab" href="#Unsubscribe-anytime">
                                <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38"><path d="M1.9 0C8.5 0 15.1 0 21.7 0 23.4 0 25 0 26.7 0 27.8 0 28.7 0 29.3 0.4 30.7 1.4 30.3 5.8 30.3 8.3 30.2 8.3 30.2 8.3 30.1 8.3 29.8 7.9 28.9 7.2 28.7 6.9 28.7 5.3 28.7 3.7 28.7 2.1 28.6 2 28.6 1.9 28.6 1.8 28 1.5 27 1.6 26.2 1.6 24.5 1.6 22.7 1.6 21 1.6 14.7 1.6 8.3 1.6 2 1.6 1.9 1.7 1.6 1.8 1.6 1.9 1.6 2.8 1.6 3.7 1.6 4.6 1.6 6.8 1.6 8.9 1.6 11.1 1.6 19.4 1.6 27.7 1.6 36 1.7 36 1.6 36 1.7 36.1 1.8 36.2 1.9 36.3 2 36.4 8 36.4 13.9 36.4 19.8 36.4 21.7 36.4 23.5 36.4 25.3 36.4 26.4 36.4 27.7 36.4 28.5 36.2 29 35.2 28.7 32.5 28.7 31.1 29.2 30.6 29.7 30.1 30.3 29.6 30.3 31.7 30.3 33.8 30.3 35.9 30.2 36 30.2 36.3 30.1 36.4 29.9 36.9 29.4 37.5 29 37.7 28.5 38 27.6 37.9 26.9 37.9 25.6 37.9 24.2 37.9 22.9 37.9 18 37.9 13.1 37.9 8.2 37.9 6.4 37.9 2 38.2 1 37.5 0.7 37.3 0.3 36.8 0.1 36.4 -0.1 35.7 0.1 34.7 0.1 33.9 0.1 32.1 0.1 30.4 0.1 28.6 0.1 21.9 0.1 15.3 0.1 8.6 0.1 7 0.1 5.3 0.1 3.6 0.1 2.8 0 1.8 0.3 1.2 0.4 0.9 0.9 0.4 1.2 0.2 1.5 0.1 1.7 0.1 1.9 0ZM28.5 9.9C29.5 9.9 30.4 11.3 31 11.9 32.6 13.6 34.3 15.2 36 16.9 36.4 17.4 37.8 18.5 38 19 35.8 21.3 33.5 23.5 31.3 25.7 30.7 26.3 29.5 28 28.4 28 28.4 28 28.4 28 28.3 28 28.1 27.6 27.9 27.6 27.9 27 28.2 26.7 28.5 26.3 28.8 26 28.8 26 28.9 26 28.9 26 29 26 29 25.8 29.1 25.8 29.1 25.8 29.2 25.8 29.2 25.8 29.2 25.7 29.2 25.7 29.2 25.6 29.2 25.6 29.3 25.6 29.3 25.6 29.3 25.6 29.3 25.5 29.3 25.5 30.4 24.4 31.5 23.4 32.6 22.4 32.6 22.3 32.6 22.2 32.7 22.1 32.8 22.1 32.8 22.1 32.9 22.1 32.9 22 32.9 22 32.9 21.9 32.9 21.9 33 21.9 33 21.9 33.5 21.4 34 20.8 34.5 20.3 34.7 20.1 34.9 20 35 19.7 27.6 19.7 20.2 19.7 12.7 19.7 12.6 19.7 12.5 19.6 12.4 19.6 12.1 19.2 11.9 18.6 12.4 18.3 12.8 18.1 13.5 18.2 13.9 18.2 15.1 18.2 16.2 18.2 17.4 18.2 23.3 18.2 29.2 18.2 35.1 18.2 35.1 18.2 35.1 18.1 35.1 18.1 34.3 17.6 33.7 16.7 33 16 31.3 14.3 29.7 12.6 28 11 28 10.8 28 10.5 28 10.3 28.2 10.2 28.4 10 28.5 9.9Z"/>
                                </svg>
                                <span>Unsubscribe anytime</span>
                            </a>
                        </li>
                        <li class="nav-link">
                            <a data-toggle="tab" href="#Pick-our-Plan">
                                <svg xmlns="http://www.w3.org/2000/svg" width="42" height="37"><path d="M3.2 27.3C0 26.9 0.1 23.9 0.1 20.4 0.1 16.5 0.1 12.7 0.1 8.8 0.1 7.2-0.1 5 0.2 3.5 0.2 3.3 0.2 3.1 0.2 2.9 1.2-0.4 4.4 0.1 8.5 0.1 15.2 0.1 21.9 0.1 28.6 0.1 32 0.1 36.2-0.4 37.7 1.5 38.4 2.6 38.4 4.2 38.4 6 38.4 6.6 38.4 7.1 38.4 7.6 38.8 9.1 38.9 10.8 39.3 12.4 40.1 16.9 40.9 21.4 41.8 25.9 42.2 27.6 42 29.8 40.9 30.7 39 32.1 35.4 32 32.8 32.6 31.4 33 29.9 33 28.4 33.4 24 34.6 19.1 34.8 14.7 35.9 14 36 13.4 36.1 12.7 36.2 10.9 36.7 7.6 37.5 6 36.5 4.1 35.4 4.5 33.5 3.9 31.1 3.6 30 3.2 28.6 3.2 27.3ZM2 6.5C13.5 6.5 25.1 6.5 36.6 6.5 36.6 4.9 36.8 2.9 35.9 2.3 34.8 1.6 30.6 2 28.8 2 22.8 2 16.7 2 10.6 2 8.8 2 7 2 5.2 2 4.6 2 3.5 1.8 3.1 2.1 1.7 2.5 2 4.6 2 6.5ZM2 11C2 14.6 2 18.1 2 21.6 2 23.3 1.8 24.9 3 25.4 3.4 25.7 4.3 25.6 5.1 25.6 6.7 25.6 8.3 25.6 9.9 25.6 16.2 25.6 22.6 25.6 28.9 25.6 31.2 25.6 35.5 26.1 36.3 24.8 36.8 24 36.6 22 36.6 20.9 36.6 17.6 36.6 14.3 36.6 11 25.1 11 13.5 11 2 11ZM6.6 13.8C11.4 13.8 16.3 13.8 21.1 13.8 21.1 14.4 21.1 15 21.1 15.5 20.1 15.9 17.8 15.6 16.6 15.6 13.3 15.6 9.9 15.6 6.6 15.6 6.6 15 6.6 14.4 6.6 13.8ZM38.4 17.9C38.4 18 38.4 18 38.4 18.1 38.4 18.1 38.4 18.1 38.4 18.1 38.4 17.9 38.4 18 38.4 17.9ZM5.1 27.4C5 28.2 5.3 29.1 5.5 29.8 5.7 30.7 5.7 31.7 6 32.6 6.3 33.7 6.2 34.5 7 35 7.7 35.4 9.6 35 10.3 34.8 12.9 34.1 15.9 34 18.5 33.3 19.8 33 21 33.1 22.3 32.7 26.1 31.7 30.4 31.5 34.2 30.5 35.8 30.1 39.1 30.2 39.9 29.1 40.2 28.6 40.3 27.9 40.1 27.2 39.8 25.7 39.6 24 39.2 22.5 39 21.5 38.9 18.8 38.4 18.1 38.5 21.3 38.9 25.3 37.1 26.6 35.4 27.9 30.8 27.4 27.9 27.4 20.3 27.4 12.7 27.4 5.1 27.4Z"/></svg>
                                <span>Pick our Plan</span>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div id="Watch-everywhere" class="tab-pane fade in active">
                            <div class="tab-content-header">
                                <h3 class="section-title">
                                    Available for most ios and Android devices.
                                </h3>
                                <p class="section-sub-title">
                                    Watch TV shows and movies anytime, anywhere--personalized for you
                                </p>
                            </div>
                            <div class="tab-content-details">    
                                <div class="screen">
                                    <div class="screen-thumbnail">
                                        <img src="https://placehold.it/340x191">
                                    </div>
                                    <div class="screen-content">
                                        <h4 class="title">On large screen</h4>
                                        <p class="discription">
                                            Smart TVs, Game Consoles, Android TV, DVD, Blue-ray players and more. 
                                        </p>
                                    </div>
                                </div>
                                <div class="screen">
                                    <div class="screen-thumbnail">
                                        <img src="https://placehold.it/340x191">
                                    </div>
                                    <div class="screen-content">
                                        <h4 class="title">On Mobile</h4>
                                        <p class="discription">
                                            Available on phone and tablet with Android or ios, wherever you go. 
                                        </p>
                                    </div>
                                </div>
                                <div class="screen">
                                    <div class="screen-thumbnail">
                                        <img src="https://placehold.it/340x191">
                                    </div>
                                    <div class="screen-content">
                                        <h4 class="title">Desktops</h4>
                                        <p class="discription">
                                            Use your PC desktop or laptop no matter
                                            what size it is. 
                                        </p>
                                    </div>
                                </div>
                            </div>    
                        </div>

                        <div id="Unsubscribe-anytime" class="tab-pane fade">
                            <div class="tab-content-header">
                                <h3 class="section-title">
                                    Unsubscribe at any time you want.
                                </h3>
                            </div>
                            <div class="tab-content-details unsubscribe">    
                                <div class="unsubscribe-content">
                                    <p class="discription">
                                        If you decide vodi isn't for you – no problem. No commitment. Cancel online at any time. 
                                    </p>
                                </div>
                                <div class="unsubscribe-thumbnail">
                                    <img src="https://placehold.it/430x300">
                                </div>
                            </div>
                        </div>

                        <div id="Pick-our-Plan" class="tab-pane fade">
                            <div class="tab-content-header">
                                <h3 class="section-title">
                                    Choose one plan and watch everything on Vodi.
                                </h3>
                            </div>
                            <div class="tab-content-details">
                                <table class="pricing-table">    
                                    <tr class="table-header">
                                        <th></th>
                                        <th>BASIC</th>
                                        <th>STANDARD</th>
                                        <th>PREMIUM</th>
                                    </tr>
                                    <tr class="alt-header">
                                        <td class="alt-data">Monthly price after free month ends on 13/10/18</td>
                                    </tr>
                                    <tr>
                                        <td>Monthly price after free month ends on 15/9/18</td>
                                        <td>Rs. 500 </td>
                                        <td>Rs. 650 </td>
                                        <td>Rs. 800</td>
                                    </tr>
                                    <tr class="alt-header"><td class="alt-data">HD available</td></tr>
                                    <tr>
                                        <td>HD available</td>
                                        <td><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 26 26" version="1.1" width="26px" height="26px"><g id="surface1"><path style=" " d="M 22.566406 4.730469 L 20.773438 3.511719 C 20.277344 3.175781 19.597656 3.304688 19.265625 3.796875 L 10.476563 16.757813 L 6.4375 12.71875 C 6.015625 12.296875 5.328125 12.296875 4.90625 12.71875 L 3.371094 14.253906 C 2.949219 14.675781 2.949219 15.363281 3.371094 15.789063 L 9.582031 22 C 9.929688 22.347656 10.476563 22.613281 10.96875 22.613281 C 11.460938 22.613281 11.957031 22.304688 12.277344 21.839844 L 22.855469 6.234375 C 23.191406 5.742188 23.0625 5.066406 22.566406 4.730469 Z "/>
                                        </g>
                                        </svg></td>
                                        <td><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 26 26" version="1.1" width="26px" height="26px"><g id="surface1"><path style=" " d="M 22.566406 4.730469 L 20.773438 3.511719 C 20.277344 3.175781 19.597656 3.304688 19.265625 3.796875 L 10.476563 16.757813 L 6.4375 12.71875 C 6.015625 12.296875 5.328125 12.296875 4.90625 12.71875 L 3.371094 14.253906 C 2.949219 14.675781 2.949219 15.363281 3.371094 15.789063 L 9.582031 22 C 9.929688 22.347656 10.476563 22.613281 10.96875 22.613281 C 11.460938 22.613281 11.957031 22.304688 12.277344 21.839844 L 22.855469 6.234375 C 23.191406 5.742188 23.0625 5.066406 22.566406 4.730469 Z "/>
                                        </g>
                                        </svg></td>
                                        <td><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 26 26" version="1.1" width="26px" height="26px"><g id="surface1"><path style=" " d="M 22.566406 4.730469 L 20.773438 3.511719 C 20.277344 3.175781 19.597656 3.304688 19.265625 3.796875 L 10.476563 16.757813 L 6.4375 12.71875 C 6.015625 12.296875 5.328125 12.296875 4.90625 12.71875 L 3.371094 14.253906 C 2.949219 14.675781 2.949219 15.363281 3.371094 15.789063 L 9.582031 22 C 9.929688 22.347656 10.476563 22.613281 10.96875 22.613281 C 11.460938 22.613281 11.957031 22.304688 12.277344 21.839844 L 22.855469 6.234375 C 23.191406 5.742188 23.0625 5.066406 22.566406 4.730469 Z "/>
                                        </g>
                                        </svg></td>
                                    </tr>
                                    <tr class="alt-header"><td class="alt-data">Screens you can watch on at the same time</td></tr>
                                    <tr>
                                        <td>Screens you can watch on at the same time</td>
                                        <td>1</td>
                                        <td>2</td>
                                        <td>4</td>
                                    </tr>
                                    <tr class="alt-header">
                                        <td class="alt-data">Watch on your laptop, TV, phone and tablet</td>
                                    </tr>
                                    <tr>
                                        <td>Watch on your laptop, TV, phone and tablet</td>
                                        <td><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 26 26" version="1.1" width="26px" height="26px"><g id="surface1"><path style=" " d="M 22.566406 4.730469 L 20.773438 3.511719 C 20.277344 3.175781 19.597656 3.304688 19.265625 3.796875 L 10.476563 16.757813 L 6.4375 12.71875 C 6.015625 12.296875 5.328125 12.296875 4.90625 12.71875 L 3.371094 14.253906 C 2.949219 14.675781 2.949219 15.363281 3.371094 15.789063 L 9.582031 22 C 9.929688 22.347656 10.476563 22.613281 10.96875 22.613281 C 11.460938 22.613281 11.957031 22.304688 12.277344 21.839844 L 22.855469 6.234375 C 23.191406 5.742188 23.0625 5.066406 22.566406 4.730469 Z "/>
                                        </g>
                                        </svg></td>
                                        <td><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 26 26" version="1.1" width="26px" height="26px"><g id="surface1"><path style=" " d="M 22.566406 4.730469 L 20.773438 3.511719 C 20.277344 3.175781 19.597656 3.304688 19.265625 3.796875 L 10.476563 16.757813 L 6.4375 12.71875 C 6.015625 12.296875 5.328125 12.296875 4.90625 12.71875 L 3.371094 14.253906 C 2.949219 14.675781 2.949219 15.363281 3.371094 15.789063 L 9.582031 22 C 9.929688 22.347656 10.476563 22.613281 10.96875 22.613281 C 11.460938 22.613281 11.957031 22.304688 12.277344 21.839844 L 22.855469 6.234375 C 23.191406 5.742188 23.0625 5.066406 22.566406 4.730469 Z "/>
                                        </g>
                                        </svg></td>
                                        <td><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 26 26" version="1.1" width="26px" height="26px"><g id="surface1"><path style=" " d="M 22.566406 4.730469 L 20.773438 3.511719 C 20.277344 3.175781 19.597656 3.304688 19.265625 3.796875 L 10.476563 16.757813 L 6.4375 12.71875 C 6.015625 12.296875 5.328125 12.296875 4.90625 12.71875 L 3.371094 14.253906 C 2.949219 14.675781 2.949219 15.363281 3.371094 15.789063 L 9.582031 22 C 9.929688 22.347656 10.476563 22.613281 10.96875 22.613281 C 11.460938 22.613281 11.957031 22.304688 12.277344 21.839844 L 22.855469 6.234375 C 23.191406 5.742188 23.0625 5.066406 22.566406 4.730469 Z "/>
                                        </g>
                                        </svg></td>
                                    </tr>
                                    <tr class="alt-header">
                                        <td class="alt-data">Unlimited films and TV programmes</td>
                                    </tr>
                                    <tr>
                                        <td>Unlimited films and TV programmes</td>
                                        <td><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 26 26" version="1.1" width="26px" height="26px"><g id="surface1"><path style=" " d="M 22.566406 4.730469 L 20.773438 3.511719 C 20.277344 3.175781 19.597656 3.304688 19.265625 3.796875 L 10.476563 16.757813 L 6.4375 12.71875 C 6.015625 12.296875 5.328125 12.296875 4.90625 12.71875 L 3.371094 14.253906 C 2.949219 14.675781 2.949219 15.363281 3.371094 15.789063 L 9.582031 22 C 9.929688 22.347656 10.476563 22.613281 10.96875 22.613281 C 11.460938 22.613281 11.957031 22.304688 12.277344 21.839844 L 22.855469 6.234375 C 23.191406 5.742188 23.0625 5.066406 22.566406 4.730469 Z "/>
                                        </g>
                                        </svg></td>
                                        <td><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 26 26" version="1.1" width="26px" height="26px"><g id="surface1"><path style=" " d="M 22.566406 4.730469 L 20.773438 3.511719 C 20.277344 3.175781 19.597656 3.304688 19.265625 3.796875 L 10.476563 16.757813 L 6.4375 12.71875 C 6.015625 12.296875 5.328125 12.296875 4.90625 12.71875 L 3.371094 14.253906 C 2.949219 14.675781 2.949219 15.363281 3.371094 15.789063 L 9.582031 22 C 9.929688 22.347656 10.476563 22.613281 10.96875 22.613281 C 11.460938 22.613281 11.957031 22.304688 12.277344 21.839844 L 22.855469 6.234375 C 23.191406 5.742188 23.0625 5.066406 22.566406 4.730469 Z "/>
                                        </g>
                                        </svg></td>
                                        <td><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 26 26" version="1.1" width="26px" height="26px"><g id="surface1"><path style=" " d="M 22.566406 4.730469 L 20.773438 3.511719 C 20.277344 3.175781 19.597656 3.304688 19.265625 3.796875 L 10.476563 16.757813 L 6.4375 12.71875 C 6.015625 12.296875 5.328125 12.296875 4.90625 12.71875 L 3.371094 14.253906 C 2.949219 14.675781 2.949219 15.363281 3.371094 15.789063 L 9.582031 22 C 9.929688 22.347656 10.476563 22.613281 10.96875 22.613281 C 11.460938 22.613281 11.957031 22.304688 12.277344 21.839844 L 22.855469 6.234375 C 23.191406 5.742188 23.0625 5.066406 22.566406 4.730469 Z "/>
                                        </g>
                                        </svg></td>
                                    </tr>
                                    <tr class="alt-header">
                                        <td class="alt-data">Cancel at any time</td>
                                    </tr>
                                    <tr>
                                        <td>Cancel at any time</td>
                                        <td><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 26 26" version="1.1" width="26px" height="26px"><g id="surface1"><path style=" " d="M 22.566406 4.730469 L 20.773438 3.511719 C 20.277344 3.175781 19.597656 3.304688 19.265625 3.796875 L 10.476563 16.757813 L 6.4375 12.71875 C 6.015625 12.296875 5.328125 12.296875 4.90625 12.71875 L 3.371094 14.253906 C 2.949219 14.675781 2.949219 15.363281 3.371094 15.789063 L 9.582031 22 C 9.929688 22.347656 10.476563 22.613281 10.96875 22.613281 C 11.460938 22.613281 11.957031 22.304688 12.277344 21.839844 L 22.855469 6.234375 C 23.191406 5.742188 23.0625 5.066406 22.566406 4.730469 Z "/>
                                        </g>
                                        </svg></td>
                                        <td><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 26 26" version="1.1" width="26px" height="26px"><g id="surface1"><path style=" " d="M 22.566406 4.730469 L 20.773438 3.511719 C 20.277344 3.175781 19.597656 3.304688 19.265625 3.796875 L 10.476563 16.757813 L 6.4375 12.71875 C 6.015625 12.296875 5.328125 12.296875 4.90625 12.71875 L 3.371094 14.253906 C 2.949219 14.675781 2.949219 15.363281 3.371094 15.789063 L 9.582031 22 C 9.929688 22.347656 10.476563 22.613281 10.96875 22.613281 C 11.460938 22.613281 11.957031 22.304688 12.277344 21.839844 L 22.855469 6.234375 C 23.191406 5.742188 23.0625 5.066406 22.566406 4.730469 Z "/>
                                        </g>
                                        </svg></td>
                                        <td><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 26 26" version="1.1" width="26px" height="26px"><g id="surface1"><path style=" " d="M 22.566406 4.730469 L 20.773438 3.511719 C 20.277344 3.175781 19.597656 3.304688 19.265625 3.796875 L 10.476563 16.757813 L 6.4375 12.71875 C 6.015625 12.296875 5.328125 12.296875 4.90625 12.71875 L 3.371094 14.253906 C 2.949219 14.675781 2.949219 15.363281 3.371094 15.789063 L 9.582031 22 C 9.929688 22.347656 10.476563 22.613281 10.96875 22.613281 C 11.460938 22.613281 11.957031 22.304688 12.277344 21.839844 L 22.855469 6.234375 C 23.191406 5.742188 23.0625 5.066406 22.566406 4.730469 Z "/>
                                        </g>
                                        </svg></td>
                                    </tr>
                                    <tr class="alt-header">
                                        <td class="alt-data">First month free</td>
                                    </tr>
                                    <tr>
                                        <td>First month free</td>
                                        <td><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 26 26" version="1.1" width="26px" height="26px"><g id="surface1"><path style=" " d="M 22.566406 4.730469 L 20.773438 3.511719 C 20.277344 3.175781 19.597656 3.304688 19.265625 3.796875 L 10.476563 16.757813 L 6.4375 12.71875 C 6.015625 12.296875 5.328125 12.296875 4.90625 12.71875 L 3.371094 14.253906 C 2.949219 14.675781 2.949219 15.363281 3.371094 15.789063 L 9.582031 22 C 9.929688 22.347656 10.476563 22.613281 10.96875 22.613281 C 11.460938 22.613281 11.957031 22.304688 12.277344 21.839844 L 22.855469 6.234375 C 23.191406 5.742188 23.0625 5.066406 22.566406 4.730469 Z "/>
                                        </g>
                                        </svg></td>
                                        <td><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 26 26" version="1.1" width="26px" height="26px"><g id="surface1"><path style=" " d="M 22.566406 4.730469 L 20.773438 3.511719 C 20.277344 3.175781 19.597656 3.304688 19.265625 3.796875 L 10.476563 16.757813 L 6.4375 12.71875 C 6.015625 12.296875 5.328125 12.296875 4.90625 12.71875 L 3.371094 14.253906 C 2.949219 14.675781 2.949219 15.363281 3.371094 15.789063 L 9.582031 22 C 9.929688 22.347656 10.476563 22.613281 10.96875 22.613281 C 11.460938 22.613281 11.957031 22.304688 12.277344 21.839844 L 22.855469 6.234375 C 23.191406 5.742188 23.0625 5.066406 22.566406 4.730469 Z "/>
                                        </g>
                                        </svg></td>
                                        <td><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 26 26" version="1.1" width="26px" height="26px"><g id="surface1"><path style=" " d="M 22.566406 4.730469 L 20.773438 3.511719 C 20.277344 3.175781 19.597656 3.304688 19.265625 3.796875 L 10.476563 16.757813 L 6.4375 12.71875 C 6.015625 12.296875 5.328125 12.296875 4.90625 12.71875 L 3.371094 14.253906 C 2.949219 14.675781 2.949219 15.363281 3.371094 15.789063 L 9.582031 22 C 9.929688 22.347656 10.476563 22.613281 10.96875 22.613281 C 11.460938 22.613281 11.957031 22.304688 12.277344 21.839844 L 22.855469 6.234375 C 23.191406 5.742188 23.0625 5.066406 22.566406 4.730469 Z "/>
                                        </g>
                                        </svg></td>
                                    </tr>
                                </table>    
                            </div>
                        </div>
                    </div>

                    <div class="trail-btn">
                        <button type="button" class="tab__btn-action">Start Your Free 14-Days Trial</button>
                    </div>    
                </div>
            </div>    
        
        </section>
        <?php  
    }
}

if ( ! function_exists( 'vodi_lp_v2_video_section' ) ) {
    function vodi_lp_v2_video_section() {
        ?>  <section class="lp-v2-video-section">
                <div class="container">
                    <div class="lp-v2-video-section__inner">
                        <div class="lp-v2-video-section__caption">
                            <h2 class="lp-v2-video-section__title">Watch whatever you wish</h2>
                            <p class="lp-v2-video-section__subtitle">Only exclusive materials for video fans</p>
                        </div>
                        <div class="lp-v2-video-section__video">
                            <img src="https://placehold.it/1214x758">
                        </div>
                    </div>
                </div>    
            </section>
        <?php
    }
} 

if ( ! function_exists( 'vodi_lp_v2_features_section' ) ) {
    function vodi_lp_v2_features_section() {
        ?>  <section class="lp-v2-features-section">
                <div class="container">
                    <div class="lp-v2-features-section__inner">
                        <div class="lp-v2-features-section__caption">
                            <h2 class="lp-v2-features-section__title">You can also get these things.</h2>
                        </div>
                        <div class="lp-v2-features-section__content">
                            <ul class="features">
                                <li class="feature">
                                    <div class="feature-title">
                                        <i class="fab fa-adversal"></i><span>No Ads</span>
                                    </div>
                                    <div class="feature-discribtion">
                                        Watch what you want, when you want with no ads, ever. 
                                    </div>
                                </li>
                                <li class="feature">
                                    <div class="feature-title">
                                        <i class="fas fa-desktop"></i><span>Multiple device</span>
                                    </div>
                                    <div class="feature-discribtion">
                                        Use upto 3 different devices on one account.
                                    </div>   
                                </li>
                                <li class="feature">  
                                    <div class="feature-title">
                                        <i class="fas fa-magic"></i><span>More Features</span>
                                    </div>
                                    <div class="feature-discribtion">
                                        Use subtitles, other lector voices and settings.
                                    </div>
                                </li>
                                <li class="feature">    
                                    <div class="feature-title">
                                        <i class="fas fa-bong"></i><span>Early Access</span>
                                    </div>
                                    <div class="feature-discribtion">
                                        Automatically get new features before we release them.
                                    </div>
                                </li>
                                <li class="feature">
                                    <div class="feature-title">
                                        <i class="far fa-life-ring"></i> <span>Premium support</span>
                                    </div>
                                    <div class="feature-discribtion">
                                        Get faster support and answers to all of your questions.
                                    </div>    
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>
        <?php
    }
} 

if ( ! function_exists( 'vodi_lp_v2_section_plans' ) ) {
    function vodi_lp_v2_section_plans() {
        ?>  <section class="lp-v2-section-plans">
                <div class="container">
                    <div class="lp-v2-section-plans__inner">
                        <div class="lp-v2-section-plans__plan-box">
                            <div class="lp-v2-section-plans__plan">
                                <h3 class="title">Free</h3>
                                <p class="price-per-month">$0</p> 
                            </div>
                            <div class="lp-v2-section-plans__plan-features">
                                <ul class="features">
                                    <li class="feature">Browse as much you want</li>
                                    <li class="feature">Up to 1000 images</li>
                                    <li class="feature">Up to 20 Collections</li>
                                </ul>
                            </div>
                            <div class="lp-v2-section-plans__plan-join-link">
                                <a href="#">Join</a>
                            </div>
                        </div>
                        <div class="lp-v2-section-plans__plan-box">
                            <div class="lp-v2-section-plans__plan highlight">
                                <h3 class="title">Premium</h3>
                                <p class="price-per-month">$8<span class="per-month"> /mo</span></p>
                            </div>
                            <div class="lp-v2-section-plans__plan-features">
                                <ul class="features">
                                    <li class="feature">Unlimited images</li>
                                    <li class="feature">Unlimited Collections</li>
                                    <li class="feature">Print your Collections</li>
                                    <li class="feature">Save Private images</li>
                                    <li class="feature">Create Private Collections</li>
                                </ul>
                            </div>
                            <div class="lp-v2-section-plans__plan-join-link">
                                <a href="#">Get Premium</a>
                            </div>
                        </div>
                        <div class="lp-v2-section-plans__plan-box">
                            <div class="lp-v2-section-plans__plan">
                                <h3 class="title">Elite</h3>
                                <p class="price-per-month">$17<span class="per-month"> /mo</span></p>
                            </div>
                            <div class="lp-v2-section-plans__plan-features">
                                <ul class="features">
                                    <li class="feature">Unlimited images</li>
                                    <li class="feature">Unlimited Collections</li>
                                    <li class="feature">Print your Collections</li>
                                    <li class="feature">Save Private images</li>
                                    <li class="feature">Create Private Collections</li>
                                    <li class="feature">Dropbox sync</li>
                                </ul>
                            </div>
                            <div class="lp-v2-section-plans__plan-join-link">
                                <a href="#">Get Elite</a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>    
        <?php
    }
}  

if ( ! function_exists( 'vodi_lp_v2_frequently_asked_questions' ) ) {
    function vodi_lp_v2_frequently_asked_questions() {
        ?>  <section class="lp-v2-frequently-asked-questions">
                <div class="container">
                    <div class="lp-v2-frequently-asked-questions__inner">
                        <div class="accordion" id="accordionExample">
                          <div class="card">
                            <div class="card-header" id="headingOne">
                              <h5 class="mb-0">
                                <a class="link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                  What forms of payment do you accept for vodiPremium?
                                </a>
                              </h5>
                            </div>

                            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                              <div class="card-body">
                                Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
                              </div>
                            </div>
                          </div>
                          <div class="card">
                            <div class="card-header" id="headingTwo">
                              <h5 class="mb-0">
                                <a class="link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                  Is Premuim a subcribtion?
                                </a>
                              </h5>
                            </div>
                            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
                              <div class="card-body">
                                Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
                              </div>
                            </div>
                          </div>
                          <div class="card">
                            <div class="card-header" id="headingThree">
                              <h5 class="mb-0">
                                <a class="link collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                  Can i cancel my Pro subscribtion?
                                </a>
                              </h5>
                            </div>
                            <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
                              <div class="card-body">
                                Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
                              </div>
                            </div>
                          </div>
                          <div class="card">
                            <div class="card-header" id="headingThree">
                              <h5 class="mb-0">
                                <a class="link collapsed" data-toggle="collapse" data-target="#collapsefour" aria-expanded="false" aria-controls="collapsefour">
                                  Move and Arrange your images the way you want?
                                </a>
                              </h5>
                            </div>
                            <div id="collapsefour" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
                              <div class="card-body">
                                Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
                              </div>
                            </div>
                          </div>
                        </div>
                    </div>
                </div>
            </section>
        <?php
    }
}  
  