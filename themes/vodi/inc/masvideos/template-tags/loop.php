<?php
/**
 * Template functions for loop
 */

if ( ! function_exists( 'movies_slider_template_loop_open' ) ) {
    function movies_slider_template_loop_open() {
        global $movie;

        $image_data = wp_get_attachment_image_src( $movie->get_image_id() );
        ?>
        <div class="slider-movie" style="background-image: url( '<?php echo $image_data['0'];?>' );">
            <div class="slider-movie__hover">
        <?php
    }
}

if ( ! function_exists( 'movies_slider_template_loop_close' ) ) {
    function movies_slider_template_loop_close() {
        ?>
            </div>
        </div>
        <?php
    }
}

if ( ! function_exists( 'masvideos_template_loop_movie_short_desc_wrap_open' ) ) {
    function masvideos_template_loop_movie_short_desc_wrap_open() {
        ?><div class="slider-movie-description-wrap"><?php
    }
}

if ( ! function_exists( 'masvideos_template_loop_movie_short_desc_wrap_close' ) ) {
    function masvideos_template_loop_movie_short_desc_wrap_close() {
        ?></div><?php
    }
}

if ( ! function_exists( 'movies_slider_action_button' ) ) {
    function movies_slider_action_button() {
        global $movie;

        $link = apply_filters( 'movies_slider_action_button_args', get_the_permalink(), $movie );

        $action_text = apply_filters( 'movies_slider_action_text', esc_html__( 'Watch Now', 'vodi' ) );

        ?>

        <div class="slider-movie__hover_watch-now">
            <a class="watch-now-btn" href="<?php echo esc_url( $link ) ?>">
                <div class="watch-now-btn-bg">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="49px" height="54px">
                        <path fill-rule="evenodd"  fill="rgb(255, 255, 255)"d="M2.000,51.000 C-0.150,46.056 0.424,8.178 2.000,5.000 C3.282,2.414 5.732,0.351 9.000,1.000 C19.348,3.054 45.393,19.419 48.000,25.000 C49.019,27.182 48.794,28.758 48.000,31.000 C46.967,33.919 13.512,54.257 9.000,54.000 C6.740,53.873 3.005,53.311 2.000,51.000 Z"/>
                    </svg>
                </div>
                <?php if( ! empty( $action_text) ) : ?>
                    <div class="watch-now-txt"><?php echo wp_kses_post( $action_text ); ?></div>
                <?php endif; ?>
            </a>
        </div>
        <?php
    }
}


if ( ! function_exists( 'movies_slider_loop_movie_meta' ) ) {
    function movies_slider_loop_movie_meta() {
        global $post, $movie;

        $categories = get_the_term_list( $post->ID, 'movie_genre', '', ', ' );
        if( taxonomy_exists( 'movie_release-year' ) ) {
            $relaese_year = get_the_term_list( $post->ID, 'movie_release-year', '', ', ' );
        } else {
            $relaese_year = '';
        }

        $duration = $movie->get_movie_run_time();

        if ( ! empty( $categories ) || ! empty( $relaese_year ) ) {
            echo '<div class="slider-movie__meta"><ul class="movie-details">';
                if( ! empty ( $relaese_year ) ) {
                   echo '<li class="movie-release-info">' . $relaese_year . '</li>';
                }
                if( ! empty ( $duration ) ) {
                   echo '<li class="movie-duration">' . $duration . '</li>';
                }
                if( ! empty ( $categories ) ) {
                    echo '<li class="movie-genre">' . $categories . '</li>';
                }
            echo '</ul></div>';
        }
    }
}

if ( ! function_exists( 'movies_slider_action_button' ) ) {
    function movies_slider_action_button() {
        global $movie;

        $link = apply_filters( 'movies_slider_action_button_args', get_the_permalink(), $movie );

        $action_text = apply_filters( 'movies_slider_action_text', esc_html__( 'Watch Now', 'vodi' ) );

        ?>

        <div class="slider-movie__hover_watch-now">
            <a class="watch-now-btn" href="<?php esc_url( $link ) ?>">
                <div class="watch-now-btn-bg">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="49px" height="54px">
                        <path fill-rule="evenodd"  fill="rgb(255, 255, 255)"d="M2.000,51.000 C-0.150,46.056 0.424,8.178 2.000,5.000 C3.282,2.414 5.732,0.351 9.000,1.000 C19.348,3.054 45.393,19.419 48.000,25.000 C49.019,27.182 48.794,28.758 48.000,31.000 C46.967,33.919 13.512,54.257 9.000,54.000 C6.740,53.873 3.005,53.311 2.000,51.000 Z"/>
                    </svg>
                </div>
                <?php if( ! empty( $action_text) ) : ?>
                    <div class="watch-now-txt"><?php echo wp_kses_post( $action_text ); ?></div>
                <?php endif; ?>
            </a>
        </div>
        <?php
    }
}

if ( ! function_exists( 'movie_list_template_loop_open' ) ) {
    function movie_list_template_loop_open() {
        global $movie;
        ?>
        <div class="movie-list">
        <?php
    }
}

if ( ! function_exists( 'movie_list_template_loop_close' ) ) {
    function movie_list_template_loop_close() {
        ?>
        </div>
        <?php
    }
}

if ( ! function_exists( 'movie_list_template_loop_movie_link_open' ) ) {
    /**
     * Insert the opening anchor tag for movies in the loop.
     */
    function movie_list_template_loop_movie_link_open() {
        global $movie;

        $link = apply_filters( 'masvideos_loop_movie_link', get_the_permalink(), $movie );

        echo '<a href="' . esc_url( $link ) . '" class="masvideos-LoopMovie-link masvideos-loop-movie__link movie__link">';
    }
}

if ( ! function_exists( 'movie_list_template_loop_movie_link_close' ) ) {
    /**
     * Insert the opening anchor tag for movies in the loop.
     */
    function movie_list_template_loop_movie_link_close() {
        echo '</a>';
    }
}

if ( ! function_exists( 'movie_list_template_loop_movie_link_close' ) ) {
    /**
     * Insert the opening anchor tag for movies in the loop.
     */
    function movie_list_template_loop_movie_link_close() {
        echo '</a>';
    }
}

if ( ! function_exists( 'movie_list_template_loop_movie_poster' ) ) {
    /**
     * movies poster in the loop.
     */
    function movie_list_template_loop_movie_poster() {
        echo masvideos_get_movie_thumbnail( 'masvideos_movie_medium' );
    }
}

if ( ! function_exists( 'movie_list_template_loop_movie_poster_open' ) ) {
    function movie_list_template_loop_movie_poster_open() {
        ?>
        <div class="movie-list__poster">
        <?php
    }
}

if ( ! function_exists( 'movie_list_template_loop_movie_poster_close' ) ) {
    function movie_list_template_loop_movie_poster_close() {
        ?>
        </div>
        <?php
    }
}


if ( ! function_exists( 'movie_list_template_loop_movie_body_open' ) ) {
    function movie_list_template_loop_movie_body_open() {
        ?>
        <div class="movie-list__body">
        <?php
    }
}

if ( ! function_exists( 'movie_list_template_loop_movie_body_close' ) ) {
    function movie_list_template_loop_movie_body_close() {
        ?>
        </div>
        <?php
    }
}

if ( ! function_exists( 'movie_list_template_loop_movie_release' ) ) {
    function movie_list_template_loop_movie_release() {
        global $post, $movie;

        if( taxonomy_exists( 'movie_release-year' ) ) {
            $relaese_year = get_the_term_list( $post->ID, 'movie_release-year', '', ', ' );
        } else {
            $relaese_year = '';
        }

        if( ! empty( $relaese_year) ) : ?>
            <span class="movie-list__year"><?php echo wp_kses_post( $relaese_year ); ?></span>
        <?php endif; 
    }
}

if ( ! function_exists( 'movie_list_template_loop_movie_title' ) ) {
    function movie_list_template_loop_movie_title() {
        the_title( '<h3 class="movie-list__name">', '</h3>' );
    }
}

if ( ! function_exists( 'movie_list_template_loop_movie_category' ) ) {
    function movie_list_template_loop_movie_category() {
        global $post, $movie;

        $categories = get_the_term_list( $post->ID, 'movie_genre', '', ', ' );
        if( ! empty( $categories) ) : ?>
           <span class="movie-list__genre"><?php echo wp_kses_post( $categories ); ?></span>
        <?php endif; 
    }
}


