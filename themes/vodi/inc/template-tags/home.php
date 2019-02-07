<?php
require_once get_template_directory() . '/inc/template-tags/home/home-v1.php';
require_once get_template_directory() . '/inc/template-tags/home/home-v2.php';
require_once get_template_directory() . '/inc/template-tags/home/home-v3.php';
require_once get_template_directory() . '/inc/template-tags/home/home-v4.php';
require_once get_template_directory() . '/inc/template-tags/home/home-v5.php';
require_once get_template_directory() . '/inc/template-tags/home/home-v6.php';
require_once get_template_directory() . '/inc/template-tags/home/home-v7.php';
require_once get_template_directory() . '/inc/template-tags/home/home-v9.php';

if ( ! function_exists( 'vodi_video_section' ) ) {
    function vodi_video_section( $args = array() ) {
        if( vodi_is_masvideos_activated() ) {
            $defaults = apply_filters( 'vodi_video_section_default_args', array(
                'section_title'         => '',
                'section_nav_links'     => array(),
                'footer_action_text'    => '',
                'footer_action_link'    => '#',
                'section_background'    => '',
                'section_style'         => '',
                'el_class'              => '',
                'design_options'        => array(),
                'shortcode_atts'        => array(
                    'columns'               => '2',
                    'limit'                 => '2',
                ),
            ) );

            $args = wp_parse_args( $args, $defaults );
            extract( $args );

            $section_class = 'home-section home-videos-section section-movies-carousel-2';

            if ( !empty ( $section_background ) ) {
                $section_class .= ' has-bg-color ' . $section_background;
            }

            if ( !empty ( $section_style ) ) {
                $section_class .= ' ' . $section_style;
            }

            if ( !empty ( $el_class ) ) {
                $section_class .= ' ' . $el_class;
            }

            $style_attr = '';

            if ( ! empty( $design_options ) && is_array( $design_options ) ) {
                foreach ( $design_options as $key => $design_option ) {
                    if ( !empty ( $design_option ) ) {
                        $style_attr .= str_replace ( '_', '-', $key ) . ': ' . $design_option . 'px; ';
                    }
                }
            }

            ?><section class="<?php echo esc_attr( $section_class ); ?>" <?php echo !empty ( $style_attr ) ? ' style="' . esc_attr( $style_attr ) . '"' : ''; ?>>
                <div class="container">
                    <div class="home-section__inner home-section__videos">
                        <?php if ( ! empty ( $section_title ) ) {
                            echo '<header class="home-section__flex-header">';
                                if ( ! empty ( $section_title ) ) {
                                    echo '<h2 class="section-title">' . esc_html( $section_title ) . '</h2>';
                                }
                                if ( ! empty ( $section_nav_links ) ) {
                                    echo '<ul class="nav nav-tabs">';
                                        $i = 0;
                                        $nav_count = count( $section_nav_links );
                                        foreach ( $section_nav_links as $section_nav_link ) {
                                            if( $i < 1 && $nav_count > 1 ) {
                                                $active = ' active';
                                                $i++;
                                            } else {
                                                $active = '';
                                            }
                                            if( ! empty ( $section_nav_link['title'] ) && ! empty ( $section_nav_link['link'] ) ) {
                                                echo '<li class="nav-item"><a class="nav-link' . esc_attr( $active ) . '" href="' . esc_url( $section_nav_link['link'] ) . '">' . esc_html( $section_nav_link['title'] ) . '</a></li>';
                                            }
                                        }
                                    echo '</ul>';
                                }
                            echo '</header>';
                        }

                        echo vodi_do_shortcode( 'mas_videos', $shortcode_atts );
                        if ( ! empty ( $footer_action_text ) ) {
                            echo '<div class="home-section__action"><a href="' . esc_url( $footer_action_link ) . '" class="home-section__action-link">' . esc_html( $footer_action_text ) . '</a></div>';
                        } ?>
                    </div>
                </div>
            </section><?php
        }
    }
}

if ( ! function_exists( 'vodi_movie_section_aside_header' ) ) {
    function vodi_movie_section_aside_header( $args = array() ) {
        if( vodi_is_masvideos_activated() ) {
            $defaults = apply_filters( 'vodi_movie_section_aside_header_default_args', array(
                'section_title'         => '',
                'section_subtitle'      => '',
                'action_text'           => '',
                'action_link'           => '#',
                'section_background'    => '',
                'section_style'         => '',
                'el_class'              => '',
                'design_options'        => array(),
                'shortcode_atts_1'      => array(
                    'columns'               => '5',
                    'limit'                 => '5',
                ),
                'shortcode_atts_2'      => array(
                    'columns'               => '7',
                    'limit'                 => '7',
                ),
            ) );

            $args = wp_parse_args( $args, $defaults );
            extract( $args );

            $section_class = 'home-section home-section__movies';

            if ( !empty ( $section_background ) ) {
                $section_class .= ' has-bg-color ' . $section_background;
            }

            if ( !empty ( $section_style ) ) {
                $section_class .= ' ' . $section_style;
            }

            if ( !empty ( $el_class ) ) {
                $section_class .= ' ' . $el_class;
            }

            $style_attr = '';

            if ( ! empty( $design_options ) && is_array( $design_options ) ) {
                foreach ( $design_options as $key => $design_option ) {
                    if ( !empty ( $design_option ) ) {
                        $style_attr .= str_replace ( '_', '-', $key ) . ': ' . $design_option . 'px; ';
                    }
                }
            }

            ?><section class="<?php echo esc_attr( $section_class ); ?>" <?php echo !empty ( $style_attr ) ? ' style="' . esc_attr( $style_attr ) . '"' : ''; ?>>
                <div class="container">
                    <div class="home-section__inner home-section__movies__inner">
                        <?php if ( ! empty ( $section_title ) || ! empty ( $section_subtitle ) ) {
                            echo '<header class="home-section__header">';
                                if ( ! empty ( $section_title ) ) {
                                    echo '<h2 class="home-section__title">' . esc_html( $section_title ) . '</h2>';
                                }
                                if ( ! empty ( $section_subtitle ) ) {
                                    echo '<p class="home-section__subtitle">' . esc_html( $section_subtitle ) . '</p>';
                                }
                                if ( ! empty ( $action_text ) ) {
                                    echo '<div class="home-section__action"><a href="' . esc_url( $action_link ) . '" class="home-section__action-link">' . esc_html( $action_text ) . '</a></div>';
                                }
                            echo '</header>';
                        } ?>
                        <?php echo vodi_do_shortcode( 'mas_movies', $shortcode_atts_1 ); ?>
                        <?php echo vodi_do_shortcode( 'mas_movies', $shortcode_atts_2 ); ?>
                    </div>
                </div>
            </section><?php
        }
    }
}

if ( ! function_exists( 'vodi_video_section_aside_header' ) ) {
    function vodi_video_section_aside_header( $args = array() ) {
        if( vodi_is_masvideos_activated() ) {
            $defaults = apply_filters( 'vodi_video_section_aside_header_default_args', array(
                'section_title'         => '',
                'section_subtitle'      => '',
                'action_link'           => '#',
                'action_text'           => '',
                'section_background'    => '',
                'section_style'         => '',
                'el_class'              => '',
                'design_options'        => array(),
                'shortcode_atts_1'      => array(
                    'columns'               => '4',
                    'limit'                 => '4',
                    'class'                 => '',
                ),
                'shortcode_atts_2'      => array(
                    'columns'               => '6',
                    'limit'                 => '6',
                    'class'                 => '',
                ),
            ) );

            $args = wp_parse_args( $args, $defaults );
            extract( $args );

            $section_class = 'home-section home-section__videos';

            if ( !empty ( $section_background ) ) {
                $section_class .= ' has-bg-color ' . $section_background;
            }

            if ( !empty ( $section_style ) ) {
                $section_class .= ' ' . $section_style;
            }

            if ( !empty ( $el_class ) ) {
                $section_class .= ' ' . $el_class;
            }

            $style_attr = '';

            if ( ! empty( $design_options ) && is_array( $design_options ) ) {
                foreach ( $design_options as $key => $design_option ) {
                    if ( !empty ( $design_option ) ) {
                        $style_attr .= str_replace ( '_', '-', $key ) . ': ' . $design_option . 'px; ';
                    }
                }
            }

            ?><section class="<?php echo esc_attr( $section_class ); ?>" <?php echo !empty ( $style_attr ) ? ' style="' . esc_attr( $style_attr ) . '"' : ''; ?>>
                <div class="container">
                    <div class="home-section__inner home-section__videos__inner">
                        <?php if ( ! empty ( $section_title ) || ! empty ( $section_subtitle ) ) {
                            echo '<header class="home-section__header">';
                                if ( ! empty ( $section_title ) ) {
                                    echo '<h2 class="home-section__title">' . esc_html( $section_title ) . '</h2>';
                                }
                                if ( ! empty ( $section_subtitle ) ) {
                                    echo '<p class="home-section__subtitle">' . esc_html( $section_subtitle ) . '</p>';
                                }
                                if ( ! empty ( $action_text ) ) {
                                    echo '<div class="home-section__action"><a href="' . esc_url( $action_link ) . '" class="home-section__action-link">' . esc_html( $action_text ) . '</a></div>';
                                }
                            echo '</header>';
                        } ?>
                        <?php echo vodi_do_shortcode( 'mas_videos', $shortcode_atts_1 ); ?>
                        <?php echo vodi_do_shortcode( 'mas_videos', $shortcode_atts_2 ); ?>
                    </div>
                </div>
            </section><?php
        }
    }
}

if ( ! function_exists( 'vodi_section_movies_carousel_aside_header' ) ) {
    function vodi_section_movies_carousel_aside_header( $args = array() ) {
        if( vodi_is_masvideos_activated() ) {
            $defaults = apply_filters( 'vodi_section_movies_carousel_aside_header_default_args', array(
                'section_title'         => '',
                'section_subtitle'      => '',
                'action_link'           => '#',
                'action_text'           => '',
                'section_background'    => '',
                'section_style'         => '',
                'header_posisition'     => '',
                'el_class'              => '',
                'design_options'        => array(),
                'shortcode_atts'        => array(
                    'columns'               => '6',
                    'limit'                 => '15',
                ),
                'carousel_args'         => array(
                    'slidesToShow'          => 6,
                    'slidesToScroll'        => 6,
                    'dots'                  => false,
                    'arrows'                => true,
                    'autoplay'              => false,
                ),
            ) );

            $args = wp_parse_args( $args, $defaults );
            extract( $args );

            $section_class = 'home-section section-movies-carousel';

            $shortcode_atts['columns'] = $carousel_args['slidesToShow'];

            if ( !empty ( $section_background ) ) {
                $section_class .= ' has-bg-color ' . $section_background;
            }

            if ( !empty ( $section_style ) ) {
                $section_class .= ' ' . $section_style;
            }


            if ( !empty ( $header_posisition ) ) {
                $section_class .= ' ' . $header_posisition;
            }

            if ( !empty ( $el_class ) ) {
                $section_class .= ' ' . $el_class;
            }

            $style_attr = '';

            if ( ! empty( $design_options ) && is_array( $design_options ) ) {
                foreach ( $design_options as $key => $design_option ) {
                    if ( !empty ( $design_option ) ) {
                        $style_attr .= str_replace ( '_', '-', $key ) . ': ' . $design_option . 'px; ';
                    }
                }
            }

            $section_id = 'section-movies-carousel-' . uniqid();
            $carousel_args['appendArrows'] = '#' . $section_id . ' .section-movies-carousel__custom-arrows';

            ?><section id="<?php echo esc_attr( $section_id ); ?>" class="<?php echo esc_attr( $section_class ); ?>" <?php echo !empty ( $style_attr ) ? ' style="' . esc_attr( $style_attr ) . '"' : ''; ?>>
                <div class="container">
                    <div class="section-movies-carousel__inner">
                        <?php if ( ! empty ( $section_title ) || ! empty ( $section_subtitle ) ) {
                            echo '<header class="home-section__header section-movies-carousel__header">';
                                if ( ! empty ( $section_title ) ) {
                                    echo '<h2 class="home-section__title">' . esc_html( $section_title ) . '</h2>';
                                }
                                if ( ! empty ( $section_subtitle ) ) {
                                    echo '<p class="home-section__subtitle">' . esc_html( $section_subtitle ) . '</p>';
                                }
                                echo '<div class="section-movies-carousel__custom-arrows"></div>';
                                if ( ! empty ( $action_text ) ) {
                                    echo '<div class="home-section__action"><a href="' . esc_url( $action_link ) . '" class="home-section__action-link">' . esc_html( $action_text ) . '</a></div>';
                                }
                            echo '</header>';
                        } ?>

                        <div class="section-movies-carousel__carousel">
                            <div class="movies-carousel__inner" data-ride="vodi-slick-carousel" data-wrap=".movies__inner" data-slick="<?php echo htmlspecialchars( json_encode( $carousel_args ), ENT_QUOTES, 'UTF-8' ); ?>">
                                    <?php echo vodi_do_shortcode( 'mas_movies', $shortcode_atts ); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section><?php
        }
    }
}

if ( ! function_exists( 'vodi_section_videos_carousel_aside_header' ) ) {
    function vodi_section_videos_carousel_aside_header( $args = array() ) {
        if( vodi_is_masvideos_activated() ) {
            $defaults = apply_filters( 'vodi_section_videos_carousel_aside_header_default_args', array(
                'section_title'         => esc_html__( 'Action &amp; Drama Movies', 'vodi' ),
                'section_subtitle'      => '',
                'action_link'           => '#',
                'action_text'           => '',
                'section_background'    => '',
                'section_style'         => '',
                'header_posisition'     => '',
                'el_class'              => '',
                'design_options'        => array(),
                'shortcode_atts'        => array(
                    'columns'           => '4',
                    'limit'             => '15',
                ),
                'carousel_args'         => array(
                    'slidesToShow'      => 4,
                    'slidesToScroll'    => 4,
                    'dots'              => false,
                    'arrows'            => true,
                    'autoplay'          => false,
                ),
            ) );

            $args = wp_parse_args( $args, $defaults );
            extract( $args );

            $section_class = 'home-section section-videos-carousel-aside-header';

            $shortcode_atts['columns'] = $carousel_args['slidesToShow'];

            if ( !empty ( $section_background ) ) {
                $section_class .= ' has-bg-color ' . $section_background;
            }

            if ( !empty ( $section_style ) ) {
                $section_class .= ' ' . $section_style;
            }

            if ( !empty ( $header_posisition ) ) {
                $section_class .= ' ' . $header_posisition;
            }

            if ( !empty ( $el_class ) ) {
                $section_class .= ' ' . $el_class;
            }

            $style_attr = '';

            if ( ! empty( $design_options ) && is_array( $design_options ) ) {
                foreach ( $design_options as $key => $design_option ) {
                    if ( !empty ( $design_option ) ) {
                        $style_attr .= str_replace ( '_', '-', $key ) . ': ' . $design_option . 'px; ';
                    }
                }
            }

            $section_id = 'section-videos-carousel-' . uniqid();
            $carousel_args['appendArrows'] = '#' . $section_id . ' .section-videos-carousel-aside-header__custom-arrows';

            ?><section id="<?php echo esc_attr( $section_id ); ?>" class="<?php echo esc_attr( $section_class ); ?>" <?php echo !empty ( $style_attr ) ? ' style="' . esc_attr( $style_attr ) . '"' : ''; ?>>
                <div class="container">
                    <div class="section-videos-carousel__inner">
                        <?php if ( ! empty ( $section_title ) || ! empty ( $section_subtitle ) ) {
                            echo '<header class="home-section__header section-videos-carousel-aside-header__header">';
                                if ( ! empty ( $section_title ) ) {
                                    echo '<h2 class="home-section__title">' . esc_html( $section_title ) . '</h2>';
                                }
                                if ( ! empty ( $section_subtitle ) ) {
                                    echo '<p class="home-section__subtitle">' . esc_html( $section_subtitle ) . '</p>';
                                }
                                echo '<div class="section-videos-carousel-aside-header__custom-arrows"></div>';
                                if ( ! empty ( $action_text ) ) {
                                    echo '<div class="home-section__action"><a href="' . esc_url( $action_link ) . '" class="home-section__action-link">' . esc_html( $action_text ) . '</a></div>';
                                }
                            echo '</header>';
                        } ?>

                        <div class="section-videos-carousel__carousel">
                            <div class="videos-carousel__inner" data-ride="vodi-slick-carousel" data-wrap=".videos__inner" data-slick="<?php echo htmlspecialchars( json_encode( $carousel_args ), ENT_QUOTES, 'UTF-8' ); ?>">
                                    <?php echo vodi_do_shortcode( 'mas_videos', $shortcode_atts ); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section><?php
        }
    }
}

if ( ! function_exists( 'vodi_section_movies_carousel_nav_header' ) ) {
    function vodi_section_movies_carousel_nav_header( $args = array() ) {
        if( vodi_is_masvideos_activated() ) {
            $defaults = apply_filters( 'vodi_section_movies_carousel_nav_header_default_args', array(
                'section_title'         => '',
                'section_nav_links'     => array(),
                'section_background'    => '',
                'section_style'         => '',
                'el_class'              => '',
                'design_options'        => array(),
                'shortcode_atts'        => array(
                    'columns'               => '7',
                    'limit'                 => '15',
                ),
                'carousel_args'         => array(
                    'slidesToShow'      => 7,
                    'slidesToScroll'    => 7,
                    'dots'              => false,
                    'arrows'            => true,
                    'autoplay'          => false,
                ),
            ) );

            $args = wp_parse_args( $args, $defaults );
            extract( $args );

            $section_id = 'section-movies-carousel-2-' . uniqid();
            $section_class = 'home-section section-movies-carousel-2';

            $shortcode_atts['columns'] = $carousel_args['slidesToShow'];

            if ( !empty ( $section_background ) ) {
                $section_class .= ' has-bg-color ' . $section_background;
            }

            if ( !empty ( $section_style ) ) {
                $section_class .= ' ' . $section_style;
            }

            if ( !empty ( $el_class ) ) {
                $section_class .= ' ' . $el_class;
            }

            $style_attr = '';

            if ( ! empty( $design_options ) && is_array( $design_options ) ) {
                foreach ( $design_options as $key => $design_option ) {
                    if ( !empty ( $design_option ) ) {
                        $style_attr .= str_replace ( '_', '-', $key ) . ': ' . $design_option . 'px; ';
                    }
                }
            }

            ?><section id="<?php echo esc_attr( $section_id ); ?>" class="<?php echo esc_attr( $section_class ); ?>" <?php echo !empty ( $style_attr ) ? ' style="' . esc_attr( $style_attr ) . '"' : ''; ?>>
                <div class="container">
                    <div class="section-movies-carousel-2__inner">
                        <?php if ( ! empty ( $section_title ) || ! empty ( $section_nav_links ) ) {
                            echo '<header class="home-section__header home-section__nav-header">';
                                if ( ! empty ( $section_title ) ) {
                                    echo '<h2 class="home-section__title">' . esc_html( $section_title ) . '</h2>';
                                }
                                if ( ! empty ( $section_nav_links ) ) {
                                    echo '<div class="movie-list-tabs"><div class="tabs-section-inner"><ul class="nav">';
                                        $i = 0;
                                        $nav_count = count( $section_nav_links );
                                        foreach ( $section_nav_links as $section_nav_link ) {
                                            if( $i < 1 && $nav_count > 1 ) {
                                                $active = ' active';
                                                $i++;
                                            } else {
                                                $active = '';
                                            }
                                            if( ! empty ( $section_nav_link['title'] ) && ! empty ( $section_nav_link['link'] ) ) {
                                                echo '<li class="nav-item"><a class="nav-link' . esc_attr( $active ) . '" href="' . esc_url( $section_nav_link['link'] ) . '">' . esc_html( $section_nav_link['title'] ) . '</a></li>';
                                            }
                                        }
                                    echo '</ul></div></div>';
                                }
                            echo '</header>';
                        } ?>

                        <div class="section-movies-carousel-2__carousel">
                            <div class="movies-carousel__inner" data-ride="vodi-slick-carousel" data-wrap=".movies__inner" data-slick="<?php echo htmlspecialchars( json_encode( $carousel_args ), ENT_QUOTES, 'UTF-8' ); ?>">
                                    <?php echo vodi_do_shortcode( 'mas_movies', $shortcode_atts ); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section><?php
        }
    }
}

if ( ! function_exists( 'vodi_section_videos_carousel_nav_header' ) ) {
    function vodi_section_videos_carousel_nav_header( $args = array() ) {
        if( vodi_is_masvideos_activated() ) {
            $defaults = apply_filters( 'vodi_section_videos_carousel_nav_header_default_args', array(
                'section_title'         => '',
                'section_nav_links'     => array(),
                'section_background'    => '',
                'el_class'              => '',
                'design_options'        => array(),
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

            $args = wp_parse_args( $args, $defaults );
            extract( $args );

            $section_class = 'home-section section-videos-carousel-nav-header';

            $shortcode_atts['columns'] = $carousel_args['slidesToShow'];

            if ( !empty ( $section_background ) ) {
                $section_class .= ' has-bg-color ' . $section_background;
            }

            if ( !empty ( $section_style ) ) {
                $section_class .= ' ' . $section_style;
            }

            if ( !empty ( $el_class ) ) {
                $section_class .= ' ' . $el_class;
            }

            $style_attr = '';

            if ( ! empty( $design_options ) && is_array( $design_options ) ) {
                foreach ( $design_options as $key => $design_option ) {
                    if ( !empty ( $design_option ) ) {
                        $style_attr .= str_replace ( '_', '-', $key ) . ': ' . $design_option . 'px; ';
                    }
                }
            }

            $section_id = 'section-videos-carousel-nav-header-' . uniqid();

            ?><section id="<?php echo esc_attr( $section_id ); ?>" class="<?php echo esc_attr( $section_class ); ?>" <?php echo !empty ( $style_attr ) ? ' style="' . esc_attr( $style_attr ) . '"' : ''; ?>>
                <div class="container">
                    <div class="section-videos-carousel-2__inner">
                        <?php if ( ! empty ( $section_title ) || ! empty ( $section_subtitle ) ) {
                            echo '<header class="home-section__header home-section__nav-header">';
                                if ( ! empty ( $section_title ) ) {
                                    echo '<h2 class="home-section__title">' . esc_html( $section_title ) . '</h2>';
                                }
                                if ( ! empty ( $section_nav_links ) ) {
                                    echo '<div class="video-list-tabs"><div class="tabs-section-inner"><ul class="nav">';
                                        $i = 0;
                                        $nav_count = count( $section_nav_links );
                                        foreach ( $section_nav_links as $section_nav_link ) {
                                            if( $i < 1 && $nav_count > 1 ) {
                                                $active = ' active';
                                                $i++;
                                            } else {
                                                $active = '';
                                            }
                                            if( ! empty ( $section_nav_link['title'] ) && ! empty ( $section_nav_link['link'] ) ) {
                                                echo '<li class="nav-item"><a class="nav-link' . esc_attr( $active ) . '" href="' . esc_url( $section_nav_link['link'] ) . '">' . esc_html( $section_nav_link['title'] ) . '</a></li>';
                                            }
                                        }
                                    echo '</ul></div></div>';
                                }
                            echo '</header>';
                        } ?>

                        <div class="section-videos-carousel-nav-header__carousel">
                            <div class="videos-carousel__inner" data-ride="vodi-slick-carousel" data-wrap=".videos__inner" data-slick="<?php echo htmlspecialchars( json_encode( $carousel_args ), ENT_QUOTES, 'UTF-8' ); ?>">
                                    <?php echo vodi_do_shortcode( 'mas_videos', $shortcode_atts ); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section><?php
        }
    }
}

if ( ! function_exists( 'vodi_section_movies_carousel_flex_header' ) ) {
    function vodi_section_movies_carousel_flex_header( $args = array() ) {
        if( vodi_is_masvideos_activated() ) {
            $defaults = apply_filters( 'vodi_section_movies_carousel_flex_header_default_args', array(
                'section_title'         => '',
                'section_nav_links'     => array(),
                'section_background'    => '',
                'section_style'         => '',
                'footer_action_text'    => '',
                'footer_action_link'    => '#',
                'el_class'              => '',
                'design_options'        => array(),
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

            $args = wp_parse_args( $args, $defaults );
            extract( $args );

            $section_class = 'home-section section-movies-carousel-flex-header';

            $shortcode_atts['columns'] = $carousel_args['slidesToShow'];

            $shortcode_atts['columns'] = $carousel_args['slidesToShow'];

            if ( !empty ( $section_background ) ) {
                $section_class .= ' has-bg-color ' . $section_background;
            }

            if ( !empty ( $section_style ) ) {
                $section_class .= ' ' . $section_style;
            }


            if ( !empty ( $header_posisition ) ) {
                $section_class .= ' ' . $header_posisition;
            }

            if ( !empty ( $el_class ) ) {
                $section_class .= ' ' . $el_class;
            }

            $style_attr = '';

            if ( ! empty( $design_options ) && is_array( $design_options ) ) {
                foreach ( $design_options as $key => $design_option ) {
                    if ( !empty ( $design_option ) ) {
                        $style_attr .= str_replace ( '_', '-', $key ) . ': ' . $design_option . 'px; ';
                    }
                }
            }

            $section_id = 'section-movies-carousel-' . uniqid();

            ?><section id="<?php echo esc_attr( $section_id ); ?>" class="<?php echo esc_attr( $section_class ); ?>" <?php echo !empty ( $style_attr ) ? ' style="' . esc_attr( $style_attr ) . '"' : ''; ?>>
                <div class="container">
                    <div class="home-section__inner">
                        <?php if ( ! empty ( $section_title ) || ! empty ( $section_nav_links ) ) {
                            echo '<header class="home-section__flex-header">';
                                if ( ! empty ( $section_title ) ) {
                                    echo '<h2 class="section-title home-section__title">' . esc_html( $section_title ) . '</h2>';
                                }
                                if ( ! empty ( $section_nav_links ) ) {
                                    echo '<div class="movie-list-tabs"><div class="tabs-section-inner"><ul class="nav">';
                                        $i = 0;
                                        $nav_count = count( $section_nav_links );
                                        foreach ( $section_nav_links as $section_nav_link ) {
                                            if( $i < 1 && $nav_count > 1 ) {
                                                $active = ' active';
                                                $i++;
                                            } else {
                                                $active = '';
                                            }
                                            if( ! empty ( $section_nav_link['title'] ) && ! empty ( $section_nav_link['link'] ) ) {
                                                echo '<li class="nav-item"><a class="nav-link' . esc_attr( $active ) . '" href="' . esc_url( $section_nav_link['link'] ) . '">' . esc_html( $section_nav_link['title'] ) . '</a></li>';
                                            }
                                        }
                                    echo '</ul></div></div>';
                                }
                            echo '</header>';
                        } ?>
                        <div class="section-movies-carousel__carousel">
                            <div class="movies-carousel__inner" data-ride="vodi-slick-carousel" data-wrap=".movies__inner" data-slick="<?php echo htmlspecialchars( json_encode( $carousel_args ), ENT_QUOTES, 'UTF-8' ); ?>">
                                    <?php echo vodi_do_shortcode( 'mas_movies', $shortcode_atts ); ?>
                            </div>
                        </div>
                        <?php if ( ! empty ( $footer_action_text ) ) {
                            echo '<div class="home-section__action"><a href="' . esc_url( $footer_action_link ) . '" class="home-section__action-link">' . esc_html( $footer_action_text ) . '</a></div>';
                        } ?>
                    </div>
                </div>
            </section><?php
        }
    }
}

if ( ! function_exists( 'vodi_section_videos_carousel_flex_header' ) ) {
    function vodi_section_videos_carousel_flex_header( $args = array() ) {
        if( vodi_is_masvideos_activated() ) {
            $defaults = apply_filters( 'vodi_section_videos_carousel_flex_header_default_args', array(
                'section_title'         => '',
                'section_nav_links'     => array(),
                'section_background'    => '',
                'section_style'         => '',
                'footer_action_text'    => '',
                'footer_action_link'    => '#',
                'el_class'              => '',
                'design_options'        => array(),
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

            $args = wp_parse_args( $args, $defaults );
            extract( $args );

            $section_class = 'home-section section-videos-carousel-flex-header';

            $shortcode_atts['columns'] = $carousel_args['slidesToShow'];

            if ( !empty ( $section_background ) ) {
                $section_class .= ' has-bg-color ' . $section_background;
            }

            if ( !empty ( $section_style ) ) {
                $section_class .= ' ' . $section_style;
            }

            if ( !empty ( $header_posisition ) ) {
                $section_class .= ' ' . $header_posisition;
            }

            if ( !empty ( $el_class ) ) {
                $section_class .= ' ' . $el_class;
            }

            $style_attr = '';

            if ( ! empty( $design_options ) && is_array( $design_options ) ) {
                foreach ( $design_options as $key => $design_option ) {
                    if ( !empty ( $design_option ) ) {
                        $style_attr .= str_replace ( '_', '-', $key ) . ': ' . $design_option . 'px; ';
                    }
                }
            }

            $section_id = 'section-videos-carousel-' . uniqid();

            ?><section id="<?php echo esc_attr( $section_id ); ?>" class="<?php echo esc_attr( $section_class ); ?>" <?php echo !empty ( $style_attr ) ? ' style="' . esc_attr( $style_attr ) . '"' : ''; ?>>
                <div class="container">
                    <div class="home-section__inner">
                        <?php if ( ! empty ( $section_title ) || ! empty ( $section_subtitle ) ) {
                            echo '<header class="home-section__flex-header">';
                                if ( ! empty ( $section_title ) ) {
                                    echo '<h2 class="section-title home-section__title">' . esc_html( $section_title ) . '</h2>';
                                }
                                if ( ! empty ( $section_nav_links ) ) {
                                    echo '<div class="video-list-tabs"><div class="tabs-section-inner"><ul class="nav">';
                                        $i = 0;
                                        $nav_count = count( $section_nav_links );
                                        foreach ( $section_nav_links as $section_nav_link ) {
                                            if( $i < 1 && $nav_count > 1 ) {
                                                $active = ' active';
                                                $i++;
                                            } else {
                                                $active = '';
                                            }
                                            if( ! empty ( $section_nav_link['title'] ) && ! empty ( $section_nav_link['link'] ) ) {
                                                echo '<li class="nav-item"><a class="nav-link' . esc_attr( $active ) . '" href="' . esc_url( $section_nav_link['link'] ) . '">' . esc_html( $section_nav_link['title'] ) . '</a></li>';
                                            }
                                        }
                                    echo '</ul></div></div>';
                                }
                            echo '</header>';
                        } ?>
                        <div class="section-videos-carousel__carousel">
                            <div class="videos-carousel__inner" data-ride="vodi-slick-carousel" data-wrap=".videos__inner" data-slick="<?php echo htmlspecialchars( json_encode( $carousel_args ), ENT_QUOTES, 'UTF-8' ); ?>">
                                    <?php echo vodi_do_shortcode( 'mas_videos', $shortcode_atts ); ?>
                            </div>
                        </div>
                        <?php if ( ! empty ( $footer_action_text ) ) {
                            echo '<div class="home-section__action"><a href="' . esc_url( $footer_action_link ) . '" class="home-section__action-link">' . esc_html( $footer_action_text ) . '</a></div>';
                        } ?>
                    </div>
                </div>
            </section><?php
        }
    }
}

if ( ! function_exists( 'vodi_featured_movies_carousel' ) ) {
    function vodi_featured_movies_carousel( $args = array() ) {
        if( vodi_is_masvideos_activated() ) {
            $defaults = apply_filters( 'vodi_featured_movies_carousel_default_args', array(
                'feature_movie_pre_title'       => '',
                'feature_movie_title'           => '',
                'feature_movie_subtitle'        => '',
                'section_nav_links'             => array(),
                'section_background'            => '',
                'section_style'                 => '',
                'bg_image'                      => '',
                'el_class'                      => '',
                'design_options'                => array(),
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

            $args = wp_parse_args( $args, $defaults );
            extract( $args );

            $section_id = 'section-movies-carousel-' . uniqid();
            $section_class = 'home-section section-featured-movies-carousel';
            
            $shortcode_atts['columns'] = $carousel_args['slidesToShow'];

            if ( !empty ( $section_background ) ) {
                $section_class .= ' has-bg-color ' . $section_background;
            }

            if ( !empty ( $section_style ) ) {
                $section_class .= ' ' . $section_style;
            }

            if ( !empty ( $el_class ) ) {
                $section_class .= ' ' . $el_class;
            }

            $style_attr = '';

            if ( ! empty( $design_options ) && is_array( $design_options ) ) {
                foreach ( $design_options as $key => $design_option ) {
                    if ( !empty ( $design_option ) ) {
                        $style_attr .= str_replace ( '_', '-', $key ) . ': ' . $design_option . 'px; ';
                    }
                }
            }

            if( ! empty( $bg_image ) && ! is_array( $bg_image ) ) {
                $bg_image = wp_get_attachment_image_src( $bg_image, 'full' );
            }

            if ( ! empty( $bg_image ) && is_array( $bg_image ) ) {
                $style_attr .= 'background-image: linear-gradient(rgba(0,0,1,0) 51%, rgb(255, 255, 255) 73%),url( ' . esc_url( $bg_image[0] ) . ' ); ';
            }

            ?><section id="<?php echo esc_attr( $section_id ); ?>" class="<?php echo esc_attr( $section_class ); ?>" <?php echo !empty ( $style_attr ) ? ' style="' . esc_attr( $style_attr ) . '"' : ''; ?>>
                <div class="container">
                    <div class="section-featured-movies-carousel__inner">
                        <?php if ( ! empty ( $feature_movie_pre_title ) || ! empty ( $feature_movie_title ) || ! empty ( $feature_movie_subtitle ) ) {
                            echo '<header class="featured-movies-carousel__header">';
                                if ( ! empty ( $feature_movie_pre_title ) ) {
                                    echo '<h5 class="featured-movies-carousel__header-pretitle">' . esc_html( $feature_movie_pre_title ) . '</h5>';
                                }
                                if ( ! empty ( $feature_movie_title ) ) {
                                    echo '<h2 class="featured-movies-carousel__header-title">' . esc_html( $feature_movie_title ) . '</h2>';
                                }
                                if ( ! empty ( $feature_movie_subtitle ) ) {
                                    echo '<span class="featured-movies-carousel__header-subtitle">' . esc_html( $feature_movie_subtitle ) . '</span>';
                                }
                            echo '</header>';
                        }

                        if ( ! empty ( $section_nav_links ) ) {
                            echo '<ul class="nav">';
                                $i = 0;
                                $nav_count = count( $section_nav_links );
                                        foreach ( $section_nav_links as $section_nav_link ) {
                                            if( $i < 1 && $nav_count > 1 ) {
                                        $active = ' active';
                                        $i++;
                                    } else {
                                        $active = '';
                                    }
                                    if( ! empty ( $section_nav_link['title'] ) && ! empty ( $section_nav_link['link'] ) ) {
                                        echo '<li class="nav-item"><a class="nav-link' . esc_attr( $active ) . '" href="' . esc_url( $section_nav_link['link'] ) . '">' . esc_html( $section_nav_link['title'] ) . '</a></li>';
                                    }
                                }
                            echo '</ul>';
                        }
                        ?>
                        <div class="featured-movies-carousel">
                            <div class="movies-carousel">
                                <div class="movies-carousel__inner" data-ride="vodi-slick-carousel" data-wrap=".movies__inner" data-slick="<?php echo htmlspecialchars( json_encode( $carousel_args ), ENT_QUOTES, 'UTF-8' ); ?>">
                                        <?php echo vodi_do_shortcode( $movies_shortcode, $shortcode_atts ); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section><?php
        }
    }
}

if ( ! function_exists( 'vodi_section_featured_video' ) ) {
    function vodi_section_featured_video( $args = array() ) {
        if( vodi_is_masvideos_activated() ) {
            $defaults = apply_filters( 'vodi_section_featured_video_default_args', array(
                'feature_video_action_icon'     => '',
                'video_id'                      => '',
                'videos_shortcode'              => 'mas_videos',
                'image'                         => '',
                'bg_image'                      => '',
                'el_class'                      => '',
                'design_options'                => array(),
                'shortcode_atts'                => array(
                    'columns'                   => '1',
                    'limit'                     => '1',
                ),
            ) );

            $args = wp_parse_args( $args, $defaults );
            extract( $args );

            if( !empty( $video_id ) ) {
                $video = masvideos_get_video( $video_id );
                $section_class = 'home-section section-featured-video';

                if ( !empty ( $section_style ) ) {
                    $section_class .= ' ' . $section_style;
                }

                if ( !empty ( $el_class ) ) {
                    $section_class .= ' ' . $el_class;
                }

                $style_attr = '';

                if ( ! empty( $design_options ) && is_array( $design_options ) ) {
                    foreach ( $design_options as $key => $design_option ) {
                        if ( !empty ( $design_option ) ) {
                            $style_attr .= str_replace ( '_', '-', $key ) . ': ' . $design_option . 'px; ';
                        }
                    }
                }

                if( ! empty( $bg_image ) && ! is_array( $bg_image ) ) {
                    $bg_image = wp_get_attachment_image_src( $bg_image, 'full' );
                }

                if ( ! empty( $bg_image ) && is_array( $bg_image ) ) {
                    $style_attr .= 'background-image: url( ' . esc_url( $bg_image[0] ) . ' );';
                }

                ?><section class="<?php echo esc_attr( $section_class ); ?>" <?php echo !empty ( $style_attr ) ? ' style="' . esc_attr( $style_attr ) . '"' : ''; ?>>
                    <div class="container">
                        <div class="section-featured-video__inner">
                           <div class="featured-video__content">
                                <?php if ( ! empty( $image ) ) {
                                    echo wp_get_attachment_image( $image, 'full', '', array( "class" => "featured-video__content-image" ) );
                                } ?>

                                <?php echo vodi_do_shortcode( $videos_shortcode, $shortcode_atts ); ?>
                                
                                <div class="featured-video__content-action">
                                    <a href="<?php echo esc_url( get_permalink( $video_id ) ); ?>" class="featured-video__content-action--link featured-video__content-action--link_watch">Watch Now</a>
                                    <a href="#" class="featured-video__content-action--link featured-video__content-action--link_add-to-playlist">+ Playlist</a>
                                </div>
                            </div>
                            <?php if( !empty( $feature_video_action_icon ) ) { ?>
                                <div class="featured-video__action">
                                    <a href="<?php echo esc_url( get_permalink( $video_id ) ); ?>" class="featured-video__action-icon"><i class="<?php echo wp_kses_post( $feature_video_action_icon ); ?>"></i></a>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </section><?php
            }
        }
    }
}

if ( ! function_exists( 'vodi_videos_with_featured_video' ) ) {
    function vodi_videos_with_featured_video( $args = array() ) {
        if( vodi_is_masvideos_activated() ) {
            $defaults = apply_filters( 'vodi_videos_with_featured_video_default_args', array(
                'section_title'         => '',
                'section_nav_links'     => array(),
                'section_background'    => '',
                'bg_image'              => '',
                'section_style'         => '',
                'el_class'              => '',
                'design_options'        => array(),
                'feature_video_id'      => '',
                'shortcode_atts'        => array(
                    'columns'               => '3',
                    'limit'                 => '6',
                ),
            ) );

            $args = wp_parse_args( $args, $defaults );
            extract( $args );

            $section_class = 'home-section home-section-videos-with-featured-video';

            if ( !empty ( $section_background ) ) {
                $section_class .= ' has-bg-color ' . $section_background;
            }

            if ( !empty ( $section_style ) ) {
                $section_class .= ' ' . $section_style;
            }

            if ( !empty ( $el_class ) ) {
                $section_class .= ' ' . $el_class;
            }

            $featured_shortcode_atts = array(
                'columns'   => 1,
                'limit'     => 1,
                'ids'       => $feature_video_id,
            );

            $style_attr = '';

            if ( ! empty( $design_options ) && is_array( $design_options ) ) {
                foreach ( $design_options as $key => $design_option ) {
                    if ( !empty ( $design_option ) ) {
                        $style_attr .= str_replace ( '_', '-', $key ) . ': ' . $design_option . 'px; ';
                    }
                }
            }

            if( ! empty( $bg_image ) && ! is_array( $bg_image ) ) {
                $bg_image = wp_get_attachment_image_src( $bg_image, 'full' );
            }

            if ( ! empty( $bg_image ) && is_array( $bg_image ) ) {
                $style_attr .= 'background-image: url( ' . esc_url( $bg_image[0] ) . ' );';
            }

            ?><section class="<?php echo esc_attr( $section_class ); ?>" <?php echo !empty ( $style_attr ) ? ' style="' . esc_attr( $style_attr ) . '"' : ''; ?>>
                <div class="container">
                    <div class="home-section__inner  home-section__videos">
                        <?php if ( ! empty ( $section_title ) ) {
                            echo '<header class="home-section__flex-header">';
                                if ( ! empty ( $section_title ) ) {
                                    echo '<h2 class="section-title">' . esc_html( $section_title ) . '</h2>';
                                }
                                if ( ! empty ( $section_nav_links ) ) {
                                    echo '<div class="video-list-tabs"><div class="tabs-section-inner"><ul class="nav nav-tabs">';
                                        $i = 0;
                                        $nav_count = count($section_nav_links);
                                        foreach ( $section_nav_links as $section_nav_link ) {
                                            if( $i < 1 && $nav_count > 1 ) {
                                                $active = ' active';
                                                $i++;
                                            } else {
                                                $active = '';
                                            }
                                            if( ! empty ( $section_nav_link['title'] ) && ! empty ( $section_nav_link['link'] ) ) {
                                                echo '<li class="nav-item"><a class="nav-link' . esc_attr( $active ) . '" href="' . esc_url( $section_nav_link['link'] ) . '">' . esc_html( $section_nav_link['title'] ) . '</a></li>';
                                            }
                                        }
                                    echo '</ul></div></div>';
                                }
                            echo '</header>';
                        } ?>
                        <div class="videos-with-featured-video__1-<?php echo esc_attr( intval( $shortcode_atts['columns'] * 2 ) ); ?>-column">
                            <?php echo vodi_do_shortcode( 'mas_videos', $featured_shortcode_atts ); ?>
                            <?php echo vodi_do_shortcode( 'mas_videos', $shortcode_atts ); ?>
                        </div>
                    </div>
                </div>
            </section><?php
        }
    }
}

if ( ! function_exists( 'vodi_section_featured_tv_show' ) ) {
    function vodi_section_featured_tv_show( $args = array() ) {
        if( vodi_is_masvideos_activated() ) {
            $defaults = apply_filters( 'vodi_section_featured_tv_show_default_args', array(
                'feature_tv_show_pre_title'     => '',
                'feature_tv_show_title'         => '',
                'feature_tv_show_subtitle'      => '',
                'section_nav_links'             => array(),
                'section_background'            => 'dark',
                'bg_image'                      => '',
                'el_class'                      => '',
                'design_options'                => array(),
                'shortcode_atts'                => array(
                    'columns'               => '5',
                    'limit'                 => '5',
                ),
            ) );

            $args = wp_parse_args( $args, $defaults );
            extract( $args );

            $section_class = 'home-section section-featured-tv-show';

            if ( !empty ( $section_background ) ) {
                $section_class .= ' has-bg-color ' . $section_background;
            }

            if ( !empty ( $section_style ) ) {
                $section_class .= ' ' . $section_style;
            }

            if ( !empty ( $el_class ) ) {
                $section_class .= ' ' . $el_class;
            }

            $style_attr = '';

            if ( ! empty( $design_options ) && is_array( $design_options ) ) {
                foreach ( $design_options as $key => $design_option ) {
                    if ( !empty ( $design_option ) ) {
                        $style_attr .= str_replace ( '_', '-', $key ) . ': ' . $design_option . 'px; ';
                    }
                }
            }

            if( ! empty( $bg_image ) && ! is_array( $bg_image ) ) {
                $bg_image = wp_get_attachment_image_src( $bg_image, 'full' );
            }

            if ( ! empty( $bg_image ) && is_array( $bg_image ) ) {
                $style_attr .= 'background-image: url( ' . esc_url( $bg_image[0] ) . ' );';
            }

            ?><section class="<?php echo esc_attr( $section_class ); ?>" <?php echo !empty ( $style_attr ) ? ' style="' . esc_attr( $style_attr ) . '"' : ''; ?>>
                <div class="container">
                    <div class="section-featured-tv-show__inner">
                        <?php if ( ! empty ( $feature_tv_show_pre_title ) || ! empty ( $feature_tv_show_title ) || ! empty ( $feature_tv_show_subtitle ) ) {
                            echo '<header class="featured-tv-show__header">';
                                if ( ! empty ( $feature_tv_show_pre_title ) ) {
                                    echo '<h5 class="featured-tv-show__header-pretitle">' . esc_html( $feature_tv_show_pre_title ) . '</h5>';
                                }
                                if ( ! empty ( $feature_tv_show_title ) ) {
                                    echo '<h2 class="featured-tv-show__header-title">' . esc_html( $feature_tv_show_title ) . '</h2>';
                                }
                                if ( ! empty ( $feature_tv_show_subtitle ) ) {
                                    echo '<span class="featured-tv-show__header-subtitle">' . esc_html( $feature_tv_show_subtitle ) . '</span>';
                                }
                            echo '</header>';
                        }

                        if ( ! empty ( $section_nav_links ) ) {
                            echo '<div class="featured-tv-show__videos"><ul class="nav">';
                                $i = 0;
                                $nav_count = count( $section_nav_links );
                                foreach ( $section_nav_links as $section_nav_link ) {
                                    if( $i < 1 && $nav_count > 1 ) {
                                        $active = ' active';
                                        $i++;
                                    } else {
                                        $active = '';
                                    }
                                    if( ! empty ( $section_nav_link['title'] ) && ! empty ( $section_nav_link['link'] ) ) {
                                        echo '<li class="nav-item"><a class="nav-link' . esc_attr( $active ) . '" href="' . esc_url( $section_nav_link['link'] ) . '">' . esc_html( $section_nav_link['title'] ) . '</a></li>';
                                    }
                                }
                            echo '</ul></div>';
                        }

                        echo vodi_do_shortcode( 'mas_videos', $shortcode_atts ); ?>
                    </div>
                </div>
            </section><?php
        }
    }
}

if ( ! function_exists( 'vodi_banner_with_section_videos' ) ) {
    function vodi_banner_with_section_videos( $args = array() ) {
        if( vodi_is_masvideos_activated() ) {
            $defaults = apply_filters( 'vodi_banner_with_section_videos_default_args', array(
                'section_title'         => '',
                'section_nav_links'     => array(),
                'section_background'    => '',
                'section_style'         => '',
                'image'                 => '',
                'footer_action_text'    => '',
                'footer_action_link'    => '',
                'el_class'              => '',
                'design_options'        => array(),
                'shortcode_atts'        => array(
                    'columns'               => '4',
                    'limit'                 => '8',
                ),
            ) );

            $args = wp_parse_args( $args, $defaults );
            extract( $args );

            $section_class = 'home-section banner-with-section-videos';

            if ( !empty ( $section_background ) ) {
                $section_class .= ' has-bg-color ' . $section_background;
            }

            if ( !empty ( $section_style ) ) {
                $section_class .= ' ' . $section_style;
            }

            if ( !empty ( $el_class ) ) {
                $section_class .= ' ' . $el_class;
            }

            $style_attr = '';

            if ( ! empty( $design_options ) && is_array( $design_options ) ) {
                foreach ( $design_options as $key => $design_option ) {
                    if ( !empty ( $design_option ) ) {
                        $style_attr .= str_replace ( '_', '-', $key ) . ': ' . $design_option . 'px; ';
                    }
                }
            }

            ?><section class="<?php echo esc_attr( $section_class ); ?>" <?php echo !empty ( $style_attr ) ? ' style="' . esc_attr( $style_attr ) . '"' : ''; ?>>
                <div class="container">
                    <div class="home-section__inner banner-with-section-videos__inner">
                        <?php if ( ! empty ( $section_title ) ) {
                            echo '<header class="home-section__flex-header">';
                                if ( ! empty ( $section_title ) ) {
                                    echo '<h2 class="section-title">' . esc_html( $section_title ) . '</h2>';
                                }
                                if ( ! empty ( $section_nav_links ) ) {
                                    echo '<div class="video-list-tabs"><div class="tabs-section-inner"><ul class="nav nav-tabs">';
                                        $i = 0;
                                        $nav_count = count($section_nav_links);
                                        foreach ( $section_nav_links as $section_nav_link ) {
                                            if( $i < 1 && $nav_count > 1 ) {
                                                $active = ' active';
                                                $i++;
                                            } else {
                                                $active = '';
                                            }
                                            if( ! empty ( $section_nav_link['title'] ) && ! empty ( $section_nav_link['link'] ) ) {
                                                echo '<li class="nav-item"><a class="nav-link' . esc_attr( $active ) . '" href="' . esc_url( $section_nav_link['link'] ) . '">' . esc_html( $section_nav_link['title'] ) . '</a></li>';
                                            }
                                        }
                                    echo '</ul></div></div>';
                                }
                            echo '</header>';
                        } ?>
                        <div class="banner-with-section-videos__content">
                            <?php if ( ! empty( $image ) ) {
                                echo '<div class="banner">';
                                    echo wp_get_attachment_image( $image, 'full', '', array( "class" => "banner__image" ) );
                                echo '</div>';
                            } ?>
                            <?php echo vodi_do_shortcode( 'mas_videos', $shortcode_atts ); ?>
                        </div>
                        <?php if ( ! empty( $footer_action_text ) && ! empty( $footer_action_link ) ) {
                            echo '<div class="home-section__footer-action"><a href="' . esc_url( $footer_action_link ) . '" class="home-section__footer-action--link">' . esc_html( $footer_action_text ) . '</a></div>';
                        } ?>
                    </div>
                </div>
            </section><?php
        }
    }
}

if ( ! function_exists( 'vodi_featured_tv_shows' ) ) {
    function vodi_featured_tv_shows() {
        ?>
        <section class="home-section-featured-tv-shows dark">
            <header class="home-section__flex-header style-2">
                <h2 class="section-title">Featured TV Shows</h2>
            </header>
                <div class="featured-tv-shows">
                    <div class="video">
                        <div class="video-container">
                            <a href="#" class="video__link">
                                <div class="video__poster">
                                    <img src="https://placehold.it/500x310">
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="video__body">
                        <div class="video-info">
                            <a href="#" class="video__link">
                            <div class="video__episode">
                                <span class="video__episode--code">August,2018</span>
                            </div>
                            <h3 class="video__title">The Leisure Seeker</h3>
                            <a href="#">
                                <div class="star"></div>
                                <div class="rating-counts"><span class="rating">9.5</span><span class="vote-count">231 Votes</span></div></a>
                                <p>A chronicled look at the criminal exploits of Colombian drug lord Pablo Escobar, as well as the many other drug kingpins who plagued the country through the years. </p>
                                <div class="movie-tab-action">
                                    <a href="#" class="movie-tab__content-action--link movie-tab__content-action--link_watch">Watch Now</a>
                                    <a href="#" class="movie-tab__content-action--link movie-tab__content-action--link_add-to-playlist">+ Playlist</a>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <?php
    }
}

if ( ! function_exists( 'vodi_tabbed_movie_list' ) ) {
    function vodi_tabbed_movie_list() {
        ?><section class="tabbed-list-movies dark">
            <div class="tabbed-list-movies__inner">
                <header class="home-section__flex-header">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a href="#movie-news" data-toggle="tab" class="nav-link active show">Now Watching</a>
                        </li>
                        <li class="nav-item">
                            <a href="#movie-news" data-toggle="tab" class="nav-link">New Episodes</a>
                        </li>
                        <li class="nav-item">
                            <a href="#movie-news" data-toggle="tab" class="nav-link">Laslty Uploaded Shows</a>
                        </li>
                    </ul>
                </header>
                <div class="grid-movie-list">
                    <div class="movie__list">
                        <a href="#" class="movie__link">
                            <img src="https://via.placeholder.com/68x100">
                            <div class="wrapper">
                                <span class="year">2018</span>
                                <h3 class="movie__name">Bilal: A New Breed of Hero</h3>
                                <span class="movie__type">Comedy,Drama</span>
                            </div>
                        </a>
                    </div>
                    <div class="movie__list">
                        <a href="#" class="movie__link">
                            <img src="https://via.placeholder.com/68x100">
                            <div class="wrapper">
                                <span class="year">2018</span>
                                <h3 class="movie__name">Bilal: A New Breed of Hero</h3>
                                <span class="movie__type">Comedy,Drama</span>
                            </div>
                        </a>
                    </div>
                    <div class="movie__list">
                        <a href="#" class="movie__link">
                            <img src="https://via.placeholder.com/68x100">
                            <div class="wrapper">
                                <span class="year">2018</span>
                                <h3 class="movie__name">Bilal: A New Breed of Hero</h3>
                                <span class="movie__type">Comedy,Drama</span>
                            </div>
                        </a>
                    </div>
                    <div class="movie__list">
                        <a href="#" class="movie__link">
                            <img src="https://via.placeholder.com/68x100">
                            <div class="wrapper">
                                <span class="year">2018</span>
                                <h3 class="movie__name">Bilal: A New Breed of Hero</h3>
                                <span class="movie__type">Comedy,Drama</span>
                            </div>
                        </a>
                    </div>
                    <div class="movie__list">
                        <a href="#" class="movie__link">
                            <img src="https://via.placeholder.com/68x100">
                            <div class="wrapper">
                                <span class="year">2018</span>
                                <h3 class="movie__name">Bilal: A New Breed of Hero</h3>
                                <span class="movie__type">Comedy,Drama</span>
                            </div>
                        </a>
                    </div>
                    <div class="movie__list">
                        <a href="#" class="movie__link">
                            <img src="https://via.placeholder.com/68x100">
                            <div class="wrapper">
                                <span class="year">2018</span>
                                <h3 class="movie__name">Bilal: A New Breed of Hero</h3>
                                <span class="movie__type">Comedy,Drama</span>
                            </div>
                        </a>
                    </div>
                    <div class="movie__list">
                        <a href="#" class="movie__link">
                            <img src="https://via.placeholder.com/68x100">
                            <div class="wrapper">
                                <span class="year">2018</span>
                                <h3 class="movie__name">Bilal: A New Breed of Hero</h3>
                                <span class="movie__type">Comedy,Drama</span>
                            </div>
                        </a>
                    </div>
                    <div class="movie__list">
                        <a href="#" class="movie__link">
                            <img src="https://via.placeholder.com/68x100">
                            <div class="wrapper">
                                <span class="year">2018</span>
                                <h3 class="movie__name">Bilal: A New Breed of Hero</h3>
                                <span class="movie__type">Comedy,Drama</span>
                            </div>
                        </a>
                    </div>
                    <div class="movie__list">
                        <a href="#" class="movie__link">
                            <img src="https://via.placeholder.com/68x100">
                            <div class="wrapper">
                                <span class="year">2018</span>
                                <h3 class="movie__name">Bilal: A New Breed of Hero</h3>
                                <span class="movie__type">Comedy,Drama</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </section>
        <?php
    }
}

if ( ! function_exists( 'vodi_section_full_width_banner' ) ) {
    function vodi_section_full_width_banner( $args = array() ) {
        $defaults = apply_filters( 'vodi_section_full_width_banner_default_args', array(
            'banner_image'          => '',
            'banner_link'           => '#',
            'el_class'              => '',
            'design_options'        => array(),
        ) );

        $args = wp_parse_args( $args, $defaults );
        extract( $args );

        if( ! empty( $banner_image ) ) {
            $section_class = 'home-section home-section__full-width-banner';

            if ( !empty ( $el_class ) ) {
                $section_class .= ' ' . $el_class;
            }

            $style_attr = '';

            if ( ! empty( $design_options ) && is_array( $design_options ) ) {
                foreach ( $design_options as $key => $design_option ) {
                    if ( !empty ( $design_option ) ) {
                        $style_attr .= str_replace ( '_', '-', $key ) . ': ' . $design_option . 'px; ';
                    }
                }
            }

            ?><section class="<?php echo esc_attr( $section_class ); ?>" <?php echo !empty ( $style_attr ) ? ' style="' . esc_attr( $style_attr ) . '"' : ''; ?>>
                <div class="banner section">
                    <a href="<?php echo esc_url( $banner_link ); ?>" class="vodi-banner-link">
                        <?php echo wp_get_attachment_image( $banner_image, 'full', '', array( 'class' => 'img-responsive' ) );  ?>
                    </a>
                </div>
            </section>
            <?php
        }
    }
}

if ( ! function_exists( 'vodi_blog_list_section' ) ) {
    /**
     * Display Posts
     */
    function vodi_blog_list_section( $args = array() ) {
        $defaults = apply_filters( 'vodi_blog_list_section_args', array(
            'section_title'     => '',
            'section_nav_links' => array(),
            'post_atts'         => array(),
            'style'             => 'style-1',
            'enable_divider'    => false,
            'hide_excerpt'      => false,
            'design_options'    => array(),
            'el_class'          => '',
        ) );

        $args = wp_parse_args( $args, $defaults );
        extract( $args );

        $query_args = array(
            'post_type'             => 'post',
            'post_status'           => 'publish',
            'ignore_sticky_posts'   => 1,
            'posts_per_page'        => '5',
            'orderby'               => 'date',
            'order'                 => 'desc',
            'category__in'          => '',
            'post__in'              => '',
            'post__not_in'          => '',
        );

        if( isset( $post_atts['category'] ) && ! empty( $post_atts['category'] ) ) {
            $query_args['category__in'] = explode( ",", $post_atts['category'] );
        }

        if( isset( $post_atts['ids'] ) && ! empty( $post_atts['ids'] ) ) {
            $query_args['post__in'] = explode( ",", $post_atts['ids'] );
        }

        // Sticky posts
        if ( isset( $post_atts['sticky'] ) && $post_atts['sticky'] == 'only' ) {
            $query_args['post__in'] = get_option( 'sticky_posts' );
        } elseif ( isset( $post_atts['sticky'] ) && $post_atts['sticky'] == 'hide' ) {
            $query_args['post__not_in'] = get_option( 'sticky_posts' );
        }

        $post_atts = wp_parse_args( $post_atts, $query_args );

        $section_class = 'home-section home-blog-list-section';

        if ( !empty ( $style ) ) {
            $section_class .= ' ' . $style;
        }

        if( !empty ( $enable_divider ) && !empty ( $style ) && $style != 'style-2' ) {
            $section_class .= ' enable-divider';
        }
        
        if ( !empty ( $el_class ) ) {
            $section_class .= ' ' . $el_class;
        }

        $style_attr = '';

        if ( ! empty( $design_options ) && is_array( $design_options ) ) {
            foreach ( $design_options as $key => $design_option ) {
                if ( !empty ( $design_option ) ) {
                    $style_attr .= str_replace ( '_', '-', $key ) . ': ' . $design_option . 'px; ';
                }
            }
        }

        $posts_query = new WP_Query( $post_atts );
        if ( $posts_query->have_posts() ) : ?>
            <section class="<?php echo esc_attr( $section_class ); ?>" <?php echo !empty ( $style_attr ) ? ' style="' . esc_attr( $style_attr ) . '"' : ''; ?>>
                <div class="container">
                    <div class="home-blog-list-section__inner">
                        <?php if ( ! empty ( $section_title ) ) {
                            echo '<header class="home-section__flex-header">';
                                if ( ! empty ( $section_title ) ) {
                                    echo '<h2 class="section-title">' . esc_html( $section_title ) . '</h2>';
                                }
                                if ( ! empty ( $section_nav_links ) ) {
                                    echo '<div class="blog-list-navs"><div class="navs-section-inner"><ul class="nav nav-tabs">';
                                        $i = 0;
                                        $nav_count = count($section_nav_links);
                                        foreach ( $section_nav_links as $section_nav_link ) {
                                            if( $i < 1 && $nav_count > 1 ) {
                                                $active = ' active';
                                                $i++;
                                            } else {
                                                $active = '';
                                            }
                                            if( ! empty ( $section_nav_link['title'] ) && ! empty ( $section_nav_link['link'] ) ) {
                                                echo '<li class="nav-item"><a class="nav-link' . esc_attr( $active ) . '" href="' . esc_url( $section_nav_link['link'] ) . '">' . esc_html( $section_nav_link['title'] ) . '</a></li>';
                                            }
                                        }
                                    echo '</ul></div></div>';
                                }
                            echo '</header>';
                        } ?>
                        <div class="articles">
                            <?php while ( $posts_query->have_posts() ) : $posts_query->the_post(); ?>
                            <article id="post-<?php the_ID(); ?>" <?php post_class( 'article' ); ?>>
                                <?php if ( $style != 'style-2' ) {
                                    vodi_post_attachment();
                                    echo '<div class="article__summary">';
                                        vodi_post_header();
                                        if( empty( $hide_excerpt ) && !empty( $text = get_the_content() ) ) {
                                            $excerpt_length = apply_filters( 'vodi_excerpt_length', 55 );
                                            $excerpt_more = apply_filters( 'vodi_excerpt_more', ' ' . '[&hellip;]' );
                                            $text = wp_trim_words( $text, $excerpt_length, $excerpt_more );
                                            echo '<div class="article__excerpt"><p>' . wp_kses_post( $text ) . '</p></div>';
                                        }
                                    echo '</div>';
                                } else {
                                    vodi_post_title();
                                } ?>
                            </article>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif;
        wp_reset_postdata();
    }
}

if ( ! function_exists( 'vodi_blog_grid_section' ) ) {
    /**
     * Display Posts
     */
    function vodi_blog_grid_section( $args = array() ) {
        $defaults = apply_filters( 'vodi_blog_grid_section_args', array(
            'section_title'     => '',
            'section_nav_links' => array(),
            'columns'           => 4,
            'post_atts'         => array(),
            'style'             => 'style-1',
            'hide_excerpt'      => false,
            'design_options'    => array(),
            'el_class'          => '',
        ) );

        $args = wp_parse_args( $args, $defaults );
        extract( $args );

        $query_args = array(
            'post_type'             => 'post',
            'post_status'           => 'publish',
            'ignore_sticky_posts'   => 1,
            'posts_per_page'        => 5,
            'orderby'               => 'date',
            'order'                 => 'desc',
            'category__in'          => '',
            'post__in'              => '',
            'post__not_in'          => '',
        );

        if( isset( $post_atts['category'] ) && ! empty( $post_atts['category'] ) ) {
            $query_args['category__in'] = explode( ",", $post_atts['category'] );
        }

        if( isset( $post_atts['ids'] ) && ! empty( $post_atts['ids'] ) ) {
            $query_args['post__in'] = explode( ",", $post_atts['ids'] );
        }

        // Sticky posts
        if ( isset( $post_atts['sticky'] ) && $post_atts['sticky'] == 'only' ) {
            $query_args['post__in'] = get_option( 'sticky_posts' );
        } elseif ( isset( $post_atts['sticky'] ) && $post_atts['sticky'] == 'hide' ) {
            $query_args['post__not_in'] = get_option( 'sticky_posts' );
        }

        $post_atts = wp_parse_args( $post_atts, $query_args );

        $section_class = 'home-section home-blog-grid-section';

        if( !empty ( $style ) && $style == 'style-3' ) {
            $hide_excerpt = true;
        }

        if ( !empty ( $style ) ) {
            $section_class .= ' ' . $style;
        }

        if ( !empty ( $el_class ) ) {
            $section_class .= ' ' . $el_class;
        }

        $style_attr = '';

        if ( ! empty( $design_options ) && is_array( $design_options ) ) {
            foreach ( $design_options as $key => $design_option ) {
                if ( !empty ( $design_option ) ) {
                    $style_attr .= str_replace ( '_', '-', $key ) . ': ' . $design_option . 'px; ';
                }
            }
        }

        $posts_query = new WP_Query( $post_atts );
        if ( $posts_query->have_posts() ) : ?>
            <section class="<?php echo esc_attr( $section_class ); ?>" <?php echo !empty ( $style_attr ) ? ' style="' . esc_attr( $style_attr ) . '"' : ''; ?>>
                <div class="container">
                    <div class="home-blog-grid-section__inner">
                        <?php if ( ! empty ( $section_title ) ) {
                            echo '<header class="home-section__flex-header">';
                                if ( ! empty ( $section_title ) ) {
                                    echo '<h2 class="section-title">' . esc_html( $section_title ) . '</h2>';
                                }
                                if ( ! empty ( $section_nav_links ) ) {
                                    echo '<div class="blog-list-navs"><div class="navs-section-inner"><ul class="nav nav-tabs">';
                                        $i = 0;
                                        $nav_count = count($section_nav_links);
                                        foreach ( $section_nav_links as $section_nav_link ) {
                                            if( $i < 1 && $nav_count > 1 ) {
                                                $active = ' active';
                                                $i++;
                                            } else {
                                                $active = '';
                                            }
                                            if( ! empty ( $section_nav_link['title'] ) && ! empty ( $section_nav_link['link'] ) ) {
                                                echo '<li class="nav-item"><a class="nav-link' . esc_attr( $active ) . '" href="' . esc_url( $section_nav_link['link'] ) . '">' . esc_html( $section_nav_link['title'] ) . '</a></li>';
                                            }
                                        }
                                    echo '</ul></div></div>';
                                }
                            echo '</header>';
                        } ?>
                        <div class="articles columns-<?php echo esc_attr( $columns ); ?>">
                            <?php while ( $posts_query->have_posts() ) : $posts_query->the_post(); ?>
                            <article id="post-<?php the_ID(); ?>" <?php post_class( 'article' ); ?>>
                                <?php
                                    if ( ! is_sticky() || in_array( get_post_format(), array( 'audio', 'video','gallery' ) ) ) {
                                        vodi_post_attachment();
                                    } else {
                                        if ( has_post_thumbnail() ) {
                                            echo '<div class="article__attachment"><div class="article__attachment--thumbnail"><a href="' . esc_url( get_the_permalink() ) . '">';
                                                the_post_thumbnail( 'vodi-featured-image' );
                                            echo '</a></div></div>';
                                        }
                                    }
                                    echo '<div class="article__summary">';
                                        if ( !empty ( $style ) && $style == 'style-2' ) {
                                            add_action( 'vodi_post_header','vodi_post_categories', 10 );
                                            remove_action( 'vodi_post_meta','vodi_post_categories', 10 );
                                        }
                                        vodi_post_header();
                                        if( empty( $hide_excerpt ) && !empty( $text = get_the_content() ) ) {
                                            $excerpt_length = apply_filters( 'vodi_excerpt_length', 55 );
                                            $excerpt_more = apply_filters( 'vodi_excerpt_more', ' ' . '[&hellip;]' );
                                            $text = wp_trim_words( $text, $excerpt_length, $excerpt_more );
                                            echo '<div class="article__excerpt"><p>' . wp_kses_post( $text ) . '</p></div>';
                                        }
                                    echo '</div>';
                                ?>
                            </article>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif;
        wp_reset_postdata();
    }
}

if ( ! function_exists( 'vodi_blog_tab_section' ) ) {
    /**
     * Display Blog Tab Section.
     */
    function vodi_blog_tab_section( $args = array() ) {
        $defaults = apply_filters( 'vodi_blog_tab_section_args', array(
            'tab_args'          => array(),
            'section_nav_links'     => array(),
            'style'             => 'style-1',
            'design_options'    => array(),
            'el_class'          => '',
        ) );

        $args = wp_parse_args( $args, $defaults );
        extract( $args );

        $section_class = 'home-section home-blog-tab-section';

        if ( !empty ( $style ) ) {
            $section_class .= ' ' . $style;
        }

        if ( !empty ( $el_class ) ) {
            $section_class .= ' ' . $el_class;
        }

        $style_attr = '';

        if ( ! empty( $design_options ) && is_array( $design_options ) ) {
            foreach ( $design_options as $key => $design_option ) {
                if ( !empty ( $design_option ) ) {
                    $style_attr .= str_replace ( '_', '-', $key ) . ': ' . $design_option . 'px; ';
                }
            }
        } ?>
        <section class="<?php echo esc_attr( $section_class ); ?>" <?php echo !empty ( $style_attr ) ? ' style="' . esc_attr( $style_attr ) . '"' : ''; ?>>
            <div class="container">
                <div class="home-blog-tab-section__inner">
                    <?php if ( ! empty ( $section_nav_links ) || ! empty ( $tab_args ) ) { ?>
                        <header class="home-section__flex-header">
                            <?php if ( ! empty ( $tab_args ) ) {
                                echo '<div class="home-section__title-tabs section-title"><div class="section-title__inner"><ul class="nav nav-tabs">';
                                    $i = 0;
                                    foreach ( $tab_args as $key => $tab_arg ) {
                                        if( isset( $tab_arg['tab_title'] ) && !empty( $tab_arg['tab_title'] ) ) {
                                            $tab_args[$key]['tab_id'] = $tab_arg['tab_id'] = uniqid();
                                            if( $i == 0 ) {
                                                $active_class = ' active show';
                                                $i++;
                                            } else {
                                                $active_class = '';
                                            }
                                            echo '<li class="nav-item"><a href="#' . esc_attr( $tab_arg['tab_id'] ) . '" data-toggle="tab" class="nav-link' . esc_attr( $active_class ) . '">' . esc_html__( $tab_arg['tab_title'], 'vodi' ) . '</a></li>';
                                        }
                                    }
                                echo '</ul></div></div>';
                            }
                            if ( ! empty ( $section_nav_links ) ) {
                                echo '<div class="blog-list-navs"><div class="navs-section-inner"><ul class="nav">';
                                    $i = 0;
                                    $nav_count = count( $section_nav_links );
                                    foreach ( $section_nav_links as $section_nav_link ) {
                                        if( $i < 1 && $nav_count > 1 ) {
                                            $active = ' active';
                                            $i++;
                                        } else {
                                            $active = '';
                                        }
                                        if( ! empty ( $section_nav_link['title'] ) && ! empty ( $section_nav_link['link'] ) ) {
                                            echo '<li class="nav-item"><a class="nav-link' . esc_attr( $active ) . '" href="' . esc_url( $section_nav_link['link'] ) . '">' . esc_html( $section_nav_link['title'] ) . '</a></li>';
                                        }
                                    }
                                echo '</ul></div></div>';
                            } ?>
                        </header>
                    <?php } ?>
                    <div class="tab-content">
                        <?php $i = 0;
                        foreach ( $tab_args as $tab_arg ) {
                            $query_args = array(
                                'post_type'             => 'post',
                                'post_status'           => 'publish',
                                'posts_per_page'        => '5',
                                'orderby'               => 'date',
                                'order'                 => 'desc',
                                'ignore_sticky_posts'   => 1,
                                'category__in'          => '',
                                'post__in'              => '',
                                'post__not_in'          => '',
                            );

                            if( isset( $tab_arg['post_atts']['category'] ) && ! empty( $tab_arg['post_atts']['category'] ) ) {
                                $query_args['category__in'] = explode( ",", $tab_arg['post_atts']['category'] );
                            }

                            if( isset( $tab_arg['post_atts']['ids'] ) && ! empty( $tab_arg['post_atts']['ids'] ) ) {
                                $query_args['post__in'] = explode( ",", $tab_arg['post_atts']['ids'] );
                            }

                            // Sticky posts
                            if ( isset( $tab_arg['post_atts']['sticky'] ) && $tab_arg['post_atts']['sticky'] == 'only' ) {
                                $query_args['post__in'] = get_option( 'sticky_posts' );
                            } elseif ( isset( $tab_arg['post_atts']['sticky'] ) && $tab_arg['post_atts']['sticky'] == 'hide' ) {
                                $query_args['post__not_in'] = get_option( 'sticky_posts' );
                            }

                            $tab_arg['post_atts'] = wp_parse_args( $tab_arg['post_atts'], $query_args );

                            $posts_query = new WP_Query( $tab_arg['post_atts'] );
                            if ( $posts_query->have_posts() && isset( $tab_arg['tab_title'] ) && !empty( $tab_arg['tab_title'] ) ) {
                                if( $i == 0 ) {
                                    $active_class = ' active show';
                                    $i++;
                                } else {
                                    $active_class = '';
                                }?>
                                <div id="<?php echo esc_attr( $tab_arg['tab_id'] ) ?>" class="articles tab-pane<?php echo esc_attr( $active_class ); ?>">
                                    <?php while ( $posts_query->have_posts() ) : $posts_query->the_post(); ?>
                                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'article' ); ?>>
                                        <?php
                                            if ( ! is_sticky() || in_array( get_post_format(), array( 'audio', 'video','gallery' ) ) ) {
                                                vodi_post_attachment();
                                            } else {
                                                if ( has_post_thumbnail() ) {
                                                    echo '<div class="article__attachment"><div class="article__attachment--thumbnail"><a href="' . esc_url( get_the_permalink() ) . '">';
                                                        the_post_thumbnail( 'vodi-featured-image' );
                                                    echo '</a></div></div>';
                                                }
                                            }
                                            echo '<div class="article__summary">';
                                                vodi_post_header();
                                                if( empty( $hide_excerpt ) && !empty( $text = get_the_content() ) ) {
                                                    $excerpt_length = apply_filters( 'vodi_excerpt_length', 55 );
                                                    $excerpt_more = apply_filters( 'vodi_excerpt_more', ' ' . '[&hellip;]' );
                                                    $text = wp_trim_words( $text, $excerpt_length, $excerpt_more );
                                                    echo '<div class="article__excerpt"><p>' . wp_kses_post( $text ) . '</p></div>';
                                                }
                                            echo '</div>';
                                        ?>
                                    </article>
                                    <?php endwhile; ?>
                                </div>
                            <?php }
                            wp_reset_postdata();
                        } ?>
                    </div>
                </div>
            </div>
        </section><?php
    }
}

if ( ! function_exists( 'vodi_single_featured_movie' ) ) {
    function vodi_single_featured_movie( $args = array() ) {
        if( vodi_is_masvideos_activated() ) {
            $defaults = apply_filters( 'vodi_single_featured_movie', array(
                'movie_action_icon'             => '',
                'movie_id'                      => '',
                'action_text'                   => 'Play Trailer',
                'el_class'                      => '',
                'bg_image'                      => '',
                'design_options'                => array(),
            ) );

            
            $args = wp_parse_args( $args, $defaults );
            extract( $args );

            if( !empty( $movie_id ) ) {
                $movie = masvideos_get_movie($movie_id);

                $categories = get_the_term_list( $movie_id, 'movie_genre', '', ', ' );

                $comment_count = $movie->get_review_count();

                $section_class = 'home-section single-featured-movie-section';

                if ( !empty ( $el_class ) ) {
                    $section_class .= ' ' . $el_class;
                }

                $style_attr = '';

                if ( ! empty( $design_options ) && is_array( $design_options ) ) {
                    foreach ( $design_options as $key => $design_option ) {
                        if ( !empty ( $design_option ) ) {
                            $style_attr .= str_replace ( '_', '-', $key ) . ': ' . $design_option . 'px; ';
                        }
                    }
                }

                if( ! empty( $bg_image ) && ! is_array( $bg_image ) ) {
                    $bg_image = wp_get_attachment_image_src( $bg_image, 'full' );
                }

                if ( ! empty( $bg_image ) && is_array( $bg_image ) ) {
                    $style_attr .=  ' background-size: cover; background-image: url( ' . esc_url( $bg_image[0] ) . ' ); height: ' . esc_attr( $bg_image[2] ) . 'px;';
                }

                ?><section class="<?php echo esc_attr( $section_class ); ?>" <?php echo !empty ( $style_attr ) ? ' style="' . esc_attr( $style_attr ) . '"' : ''; ?>>
                    <div class="container">
                        <div class="single-featured-movie">
                        <div class="single-featured-movie__inner">
                            <?php 
                                if ( ! empty( $categories ) ||  !empty( $comment_count ) ) {
                                    echo '<div class="movie__meta">';
                                        if( ! empty ( $categories ) ) {
                                           echo '<span class="movie__meta--genre">' . $categories . '</span>';
                                        }

                                        if ( $comment_count > 0 ) {
                                            ?>
                                            <span class="avg-rating-text">
                                                <a href="<?php echo esc_url( get_permalink( $movie->get_id() ) ); ?>/#reviews" class="avg-rating">
                                                
                                                    <?php echo wp_kses_post( sprintf( _n( '<span>%s</span> comments', '<span>%s</span> Comments', $movie->get_review_count(), 'vodi' ), $movie->get_review_count() ) ) ; ?>
                                                </a>
                                            </span>
                                            <?php
                                        }
                                    echo '</div>';
                                }
                            ?>
                            <h2 class="single-movie__title entry-title"><a href="<?php echo esc_url( get_permalink( $movie_id ) ); ?>" class="featured-movie__action-icon"><?php echo get_the_title($movie_id) ?></a></h2>
                            <div class="featured-movie__action">
                                <a href="<?php echo esc_url( get_permalink( $movie_id ) ); ?>" class="featured-movie__action-icon">
                                   <?php if( ! empty( $action_text) ) : ?>
                                    <i class="icon <?php echo esc_html__( $movie_action_icon ); ?>"></i>
                                    <div class="play-trailer-txt"><?php echo wp_kses_post( $action_text ); ?></div>
                                    <?php endif; ?></a>
                            </div>
                        </div>
                    </div>
                </div> 
                </section><?php
            }
        }
    }
}

if ( ! function_exists( 'vodi_hot_premieres_block' ) ) {
    function vodi_hot_premieres_block( $args = array() ) {
        if( vodi_is_masvideos_activated() ) {
            $defaults = apply_filters( 'vodi_hot_premieres_block_default_args', array(
                'section_title'         => '',
                'section_nav_links'     => array(),
                'el_class'              => '',
                'design_options'        => array(),
                'shortcode_atts'        => array(
                    'columns'               => '4',
                    'limit'                 => '4',
                ),
            ) );

            $args = wp_parse_args( $args, $defaults );
            extract( $args );

            $section_class = 'home-section section-hot-premier-show';

            if ( !empty ( $el_class ) ) {
                $section_class .= ' ' . $el_class;
            }

            if ( !empty ( $hide_movie_title ) ) {
                $section_class .= ' hide-movie-title' ;
            }

            $style_attr = '';

            if ( ! empty( $design_options ) && is_array( $design_options ) ) {
                foreach ( $design_options as $key => $design_option ) {
                    if ( !empty ( $design_option ) ) {
                        $style_attr .= str_replace ( '_', '-', $key ) . ': ' . $design_option . 'px; ';
                    }
                }
            }

            ?><section class="<?php echo esc_attr( $section_class ); ?>" <?php echo !empty ( $style_attr ) ? ' style="' . esc_attr( $style_attr ) . '"' : ''; ?>>
                <div class="container">
                    <div class="home-section__inner">
                        <?php if ( ! empty ( $section_title ) ) {
                            echo '<header class="home-section__flex-header">';
                                if ( ! empty ( $section_title ) ) {
                                    echo '<h2 class="section-title">' . esc_html( $section_title ) . '</h2>';
                                }
                                if ( ! empty ( $section_nav_links ) ) {
                                    echo '<ul class="nav nav-tabs">';
                                        $i = 0;
                                        $nav_count = count( $section_nav_links );
                                        foreach ( $section_nav_links as $section_nav_link ) {
                                            if( $i < 1 && $nav_count > 1 ) {
                                                $active = ' active';
                                                $i++;
                                            } else {
                                                $active = '';
                                            }
                                            if( ! empty ( $section_nav_link['title'] ) && ! empty ( $section_nav_link['link'] ) ) {
                                                echo '<li class="nav-item"><a class="nav-link' . esc_attr( $active ) . '" href="' . esc_url( $section_nav_link['link'] ) . '">' . esc_html( $section_nav_link['title'] ) . '</a></li>';
                                            }
                                        }
                                    echo '</ul>';
                                }
                            echo '</header>';
                        }?>

                        <div class="hot-premier-show"><?php
                            echo vodi_do_shortcode( 'mas_movies', $shortcode_atts );?>
                        </div>
                    </div>
                </div>
            </section><?php
        }
    }
}

if ( ! function_exists( 'vodi_slider_movies' ) ) {
    function vodi_slider_movies( $args = array() ) {
        if( vodi_is_masvideos_activated() ) {

            $defaults = apply_filters( 'vodi_slider_movies_default_args', array(
                'el_class'              => '',
                'action_text'           => 'Watch Now',
                'design_options'        => array(),
                'shortcode_atts'        => array(
                    'columns'               => '3',
                    'limit'                 => '3',
                ),
            ) );
            $args = wp_parse_args( $args, $defaults );
            extract( $args );
            $section_class = 'movie-slider';
            if ( !empty ( $el_class ) ) {
                $section_class .= ' ' . $el_class;
            }
            $style_attr = '';
            if ( ! empty( $design_options ) && is_array( $design_options ) ) {
                foreach ( $design_options as $key => $design_option ) {
                    if ( !empty ( $design_option ) ) {
                        $style_attr .= str_replace ( '_', '-', $key ) . ': ' . $design_option . 'px; ';
                    }
                }
            }

            $shortcode_movies   = new Vodi_Shortcode_Movies( $shortcode_atts, 'mas_movies' );
            $movies             = $shortcode_movies->get_movies();
            $movie_ids          = $movies->ids;

            ?><div class="<?php echo esc_attr( $section_class ); ?>" <?php echo !empty ( $style_attr ) ? ' style="' . esc_attr( $style_attr ) . '"' : ''; ?>>
                <?php
                    $shortcode_movies->movie_loop_start();
                    
                    if ( masvideos_get_movies_loop_prop( 'total' ) ) {
                        foreach ( $movies->ids as $movie_id ) {
                            $GLOBALS['post'] = get_post( $movie_id );
                            setup_postdata( $GLOBALS['post'] );

                            // Set custom movie visibility when quering hidden movies.
                            // add_action( 'masvideos_movie_is_visible', array( $this, 'set_movie_as_visible' ) );

                            masvideos_get_template_part( 'content', 'movie-slider' );

                            // Restore movie visibility.
                            // remove_action( 'masvideos_movie_is_visible', array( $this, 'set_movie_as_visible' ) );
                        }
                    }
                    $shortcode_movies->movie_loop_end();
                ?>
            </div> <?php
        }
    }
}

if ( ! function_exists( 'vodi_section_live_videos' ) ) {
    function vodi_section_live_videos( $args = array() ) {
        if( vodi_is_masvideos_activated() ) {
            $defaults = apply_filters( 'vodi_section_live_videos_default_args', array(
                'live_videos_title'     => '',
                'footer_action_text'    => 'View All',
                'footer_action_link'    => '#',
                'el_class'              => '',
                'design_options'        => array(),
                'shortcode_atts'        => array(
                    'columns'               => '1',
                    'limit'                 => '3',
                ),
            ) );

            $args = wp_parse_args( $args, $defaults );
            extract( $args );

            $section_class = 'live-videos';

            if ( !empty ( $el_class ) ) {
                $section_class .= ' ' . $el_class;
            }

            $style_attr = '';

            if ( ! empty( $design_options ) && is_array( $design_options ) ) {
                foreach ( $design_options as $key => $design_option ) {
                    if ( !empty ( $design_option ) ) {
                        $style_attr .= str_replace ( '_', '-', $key ) . ': ' . $design_option . 'px; ';
                    }
                }
            }

            ?><section class="<?php echo esc_attr( $section_class ); ?>" <?php echo !empty ( $style_attr ) ? ' style="' . esc_attr( $style_attr ) . '"' : ''; ?>>
                <div class="live-videos__inner">
                    <?php if ( ! empty ( $live_videos_title ) ) {
                        echo '<h5 class="live-videos__title">' . esc_html( $live_videos_title ) . '</h5>';
                    }?>

                    <?php echo vodi_do_shortcode( 'mas_videos', $shortcode_atts );?>

                    <?php if ( ! empty ( $footer_action_text ) ) {
                        echo '<div class="home-section__action"><a href="' . esc_url( $footer_action_link ) . '" class="home-section__action-link">' . esc_html( $footer_action_text ) . '</a></div>';
                    } ?>
                </div>
            </section><?php
        }
    }
}

if ( ! function_exists( 'vodi_section_coming_soon_videos' ) ) {
    function vodi_section_coming_soon_videos( $args = array() ) {
        if( vodi_is_masvideos_activated() ) {
            $defaults = apply_filters( 'vodi_section_coming_soon_videos_default_args', array(
                'coming_soon_videos_title'     => '',
                'footer_action_text'           => 'View All',
                'footer_action_link'           => '#',
                'el_class'                     => '',
                'design_options'               => array(),
                'shortcode_atts'               => array(
                    'columns'                      => '1',
                    'limit'                        => '3',
                ),
            ) );

            $args = wp_parse_args( $args, $defaults );
            extract( $args );

            $section_class = 'coming-soon-videos';

            if ( !empty ( $el_class ) ) {
                $section_class .= ' ' . $el_class;
            }

            $style_attr = '';

            if ( ! empty( $design_options ) && is_array( $design_options ) ) {
                foreach ( $design_options as $key => $design_option ) {
                    if ( !empty ( $design_option ) ) {
                        $style_attr .= str_replace ( '_', '-', $key ) . ': ' . $design_option . 'px; ';
                    }
                }
            }

            ?><section class="<?php echo esc_attr( $section_class ); ?>" <?php echo !empty ( $style_attr ) ? ' style="' . esc_attr( $style_attr ) . '"' : ''; ?>>
                <div class="coming-soon-videos__inner">
                    <?php if ( ! empty ( $coming_soon_videos_title ) ) {
                        echo '<h5 class="coming-soon-videos__title">' . esc_html( $coming_soon_videos_title ) . '</h5>';
                    }?>

                    <?php echo vodi_do_shortcode( 'mas_videos', $shortcode_atts );?>

                    <?php if ( ! empty ( $footer_action_text ) ) {
                        echo '<div class="home-section__action"><a href="' . esc_url( $footer_action_link ) . '" class="home-section__action-link">' . esc_html( $footer_action_text ) . '</a></div>';
                    } ?>
                </div>
            </section><?php
        }
    }
}

if ( ! function_exists( 'vodi_movies_list' ) ) {
    function vodi_movies_list( $args = array() ) {
        if( vodi_is_masvideos_activated() ) {
            $defaults = apply_filters( 'vodi_movies_list_default_args', array(
                'section_title_1'         => '',
                'section_title_2'         => '',
                'section_nav_links_1'     => array(),
                'section_nav_links_2'     => array(),
                'featured_movie_id'     => '',
                'el_class'              => '',
                'design_options'        => array(),
                'shortcode_atts_1'      => array(
                    'columns'               => '1',
                    'limit'                 => '8',
                ),
                'shortcode_atts_2'      => array(
                    'columns'               => '1',
                    'limit'                 => '8',
                ),
            ) );
            $args = wp_parse_args( $args, $defaults );
            extract( $args );
            $section_id = 'section-movies-list-' . uniqid();
            $section_class = 'home-section section-movies-list';
            if ( !empty ( $el_class ) ) {
                $section_class .= ' ' . $el_class;
            }
            $style_attr = '';
            if ( ! empty( $design_options ) && is_array( $design_options ) ) {
                foreach ( $design_options as $key => $design_option ) {
                    if ( !empty ( $design_option ) ) {
                        $style_attr .= str_replace ( '_', '-', $key ) . ': ' . $design_option . 'px; ';
                    }
                }
            }
            $shortcode_movies_1 = new Vodi_Shortcode_Movies( $shortcode_atts_1, 'mas_movies' );
            $movies_1           = $shortcode_movies_1->get_movies();
            $movie_ids_1        = $movies_1->ids;

            $shortcode_movies_2 = new Vodi_Shortcode_Movies( $shortcode_atts_2, 'mas_movies' );
            $movies_2           = $shortcode_movies_2->get_movies();
            $movie_ids_2        = $movies_2->ids;
            
            $excerpt_length = apply_filters( 'vodi_featured_movie_excerpt_length', '20' );
            $excerpt_more = apply_filters( 'vodi_featured_movie_excerpt_more', '' );
            ?><section id="<?php echo esc_attr( $section_id ); ?>" class="<?php echo esc_attr( $section_class ); ?>" <?php echo !empty ( $style_attr ) ? ' style="' . esc_attr( $style_attr ) . '"' : ''; ?>>
                <div class="container">
                    <div class="section-movies-list__inner">
                        <div class="top-movies-list">
                            <?php if ( ! empty ( $section_title_1 ) ) {
                                echo '<header class="home-section__header top-movies-list__header">';
                                    if ( ! empty ( $section_title_1 ) ) {
                                        echo '<h2 class="section-movies-list__title">' . esc_html( $section_title_1 ) . '</h2>';
                                    }
                                    if ( ! empty ( $section_nav_links_1 ) ) {
                                        echo '<ul class="nav nav-tabs">';
                                            $i = 0;
                                            $nav_count = count( $section_nav_links_1 );
                                            foreach ( $section_nav_links_1 as $section_nav_link_1 ) {
                                                if( $i < 1 && $nav_count > 1 ) {
                                                    $active = ' active';
                                                    $i++;
                                                } else {
                                                    $active = '';
                                                }
                                                if( ! empty ( $section_nav_link_1['title'] ) && ! empty ( $section_nav_link_1['link'] ) ) {
                                                    echo '<li class="nav-item"><a class="nav-link' . esc_attr( $active ) . '" href="' . esc_url( $section_nav_link_1['link'] ) . '">' . esc_html( $section_nav_link_1['title'] ) . '</a></li>';
                                                }
                                            }
                                        echo '</ul>';
                                    }
                                echo '</header>';
                            } ?>

                            <div class="top-movies-list__info">

                                <?php
                                    $shortcode_movies_1->movie_loop_start();
                                    
                                    if ( masvideos_get_movies_loop_prop( 'total' ) ) {
                                        foreach( $movie_ids_1 as $movie_id_1 ) {
                                            $GLOBALS['post'] = get_post( $movie_id_1 );
                                            setup_postdata( $GLOBALS['post'] );

                                            // Set custom movie visibility when quering hidden movies.
                                            // add_action( 'masvideos_movie_is_visible', array( $this, 'set_movie_as_visible' ) );

                                            masvideos_get_template_part( 'content', 'movie-list' );

                                            // Restore movie visibility.
                                            // remove_action( 'masvideos_movie_is_visible', array( $this, 'set_movie_as_visible' ) );
                                        }
                                    }
                                    $shortcode_movies_1->movie_loop_end();
                                ?>
                            </div>
                        </div>

                        <div class="featured-with-list-view-movies-list">
                            <?php if ( ! empty ( $section_title_2 ) ) {
                                echo '<header class="home-section__header featured-with-list-view-movies-list__header">';
                                    if ( ! empty ( $section_title_2 ) ) {
                                        echo '<h2 class="section-movies-list__title">' . esc_html( $section_title_2 ) . '</h2>';
                                    }
                                    if ( ! empty ( $section_nav_links_2 ) ) {
                                        echo '<ul class="nav nav-tabs">';
                                            $i = 0;
                                            $nav_count = count( $section_nav_links_2 );
                                            foreach ( $section_nav_links_2 as $section_nav_link_2 ) {
                                                if( $i < 1 && $nav_count > 1 ) {
                                                    $active = ' active';
                                                    $i++;
                                                } else {
                                                    $active = '';
                                                }
                                                if( ! empty ( $section_nav_link_2['title'] ) && ! empty ( $section_nav_link_2['link'] ) ) {
                                                    echo '<li class="nav-item"><a class="nav-link' . esc_attr( $active ) . '" href="' . esc_url( $section_nav_link_2['link'] ) . '">' . esc_html( $section_nav_link_2['title'] ) . '</a></li>';
                                                }
                                            }
                                        echo '</ul>';
                                    }
                                echo '</header>';
                            } ?>

                            <div class="featured-with-list-view-movies-list__info">
                                <?php if( !empty( $featured_movie_id ) ) { ?>
                                    <div class="featured-movie">
                                        <?php echo vodi_do_shortcode( 'mas_movies',array('columns' => '1', 'limit' => '1', 'ids' => $featured_movie_id ) ); ?>
                                    </div>
                                <?php } ?>

                                <div class="list-view-movies-list">
                                    <?php
                                    $shortcode_movies_2->movie_loop_start();
                                    
                                    if ( masvideos_get_movies_loop_prop( 'total' ) ) {
                                        foreach( $movie_ids_2 as $movie_id_2 ) {
                                            $GLOBALS['post'] = get_post( $movie_id_2 );
                                            setup_postdata( $GLOBALS['post'] );

                                            // Set custom movie visibility when quering hidden movies.
                                            // add_action( 'masvideos_movie_is_visible', array( $this, 'set_movie_as_visible' ) );

                                            masvideos_get_template_part( 'content', 'movie-list' );

                                            // Restore movie visibility.
                                            // remove_action( 'masvideos_movie_is_visible', array( $this, 'set_movie_as_visible' ) );
                                        }
                                    }
                                    $shortcode_movies_2->movie_loop_end();
                                ?>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </section><?php
        }
    }
}

if ( ! function_exists( 'vodi_section_event_category_list' ) ) {
    function vodi_section_event_category_list( $args = array() ) {
        if( vodi_is_masvideos_activated() ) {
            $defaults = apply_filters( 'vodi_section_event_category_list_default_args', array(
                'section_title'         => '',
                'section_nav_links'     => array(),
                'design_options'        => array(),
                'columns'               => 5,
                'category_args'         => array(
                    'orderby'           => 'name',
                    'order'             => 'ASC',
                    'number'            => 4,
                    'hide_empty'        => true,
                ),
                'el_class'              => '',
            ) );

            $args = wp_parse_args( $args, $defaults );
            extract( $args );

            $section_class = 'home-section vodi-event-category';

            if ( !empty ( $el_class ) ) {
                $section_class .= ' ' . $el_class;
            }

            $style_attr = '';

            if ( ! empty( $design_options ) && is_array( $design_options ) ) {
                foreach ( $design_options as $key => $design_option ) {
                    if ( !empty ( $design_option ) ) {
                        $style_attr .= str_replace ( '_', '-', $key ) . ': ' . $design_option . 'px; ';
                    }
                }
            }

            $categories = get_terms( 'video_cat', $category_args );

            ?><section class="<?php echo esc_attr( $section_class ); ?>" <?php echo !empty ( $style_attr ) ? ' style="' . esc_attr( $style_attr ) . '"' : ''; ?>>
                <div class="container">
                    <?php if ( ! empty ( $section_title ) ) {
                        echo '<header class="home-section__flex-header">';
                            if ( ! empty ( $section_title ) ) {
                                echo '<h2 class="section-title">' . esc_html( $section_title ) . '</h2>';
                            }
                            if ( ! empty ( $section_nav_links ) ) {
                                echo '<ul class="nav nav-tabs">';
                                    $i = 0;
                                    $nav_count = count( $section_nav_links );
                                    foreach ( $section_nav_links as $section_nav_link ) {
                                        if( $i < 1 && $nav_count > 1 ) {
                                            $active = ' active';
                                            $i++;
                                        } else {
                                            $active = '';
                                        }
                                        if( ! empty ( $section_nav_link['title'] ) && ! empty ( $section_nav_link['link'] ) ) {
                                            echo '<li class="nav-item"><a class="nav-link' . esc_attr( $active ) . '" href="' . esc_url( $section_nav_link['link'] ) . '">' . esc_html( $section_nav_link['title'] ) . '</a></li>';
                                        }
                                    }
                                echo '</ul>';
                            }
                        echo '</header>';
                    } ?>

                    <ul class="event-category-lists row columns-<?php echo esc_attr( $columns ); ?>">
                        <?php foreach( $categories as $category ) :
                        $thumbnail_id = get_term_meta( $category->term_id, 'thumbnail_id', true ); ?>
                        <li class="event-category-list column">
                            <a href="<?php echo esc_url( get_term_link( $category ) ); ?>" class="event-category-list__inner">
                                <div class="event-category-list__inner-poster">
                                <?php if ( !empty( $thumbnail_id ) ) {
                                    echo wp_get_attachment_image( $thumbnail_id, 'full', '', array( 'class' => 'event-category-list__inner-poster-image' ) );
                                } else {
                                    echo '<div class="event-category-list__inner-poster-image empty"></div>';
                                }?>
                                </div>
                                <div class="event-category-list__inner-content">
                                    <h2 class="event-category-title"><?php echo esc_html( $category->name );?></h2>
                                </div>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </section><?php
        }
    }
}

if ( ! function_exists( 'vodi_blog_grid_with_list_section' ) ) {
    function vodi_blog_grid_with_list_section( $args = array() ) {
        $defaults = apply_filters( 'vodi_blog_grid_with_list_section_default_args', array(
            'section_title'     => '',
            'section_nav_links' => array(),
            'post_atts_1'       => array(),
            'post_atts_2'       => array(),
            'hide_excerpt_1'    => true,
            'hide_excerpt_2'    => false,
            'design_options'    => array(),
            'el_class'          => '',
        ) );

        $args = wp_parse_args( $args, $defaults );
        extract( $args );

        $query_args_1 = array(
            'post_type'         => 'post',
            'post_status'       => 'publish',
            'posts_per_page'    => 1,
            'orderby'           => 'date',
            'order'             => 'desc',
            'category__in'      => '',
            'post__in'          => '',
            'post__not_in'      => '',
            'ignore_sticky_posts' => 1

        );


        $query_args_2 = array(
            'post_type'         => 'post',
            'post_status'       => 'publish',
            'posts_per_page'    => 3,
            'orderby'           => 'date',
            'order'             => 'desc',
            'category__in'      => '',
            'post__in'          => '',
            'post__not_in'      => '',
        );

        if( isset( $ids ) && ! empty( $ids ) ) {
            $query_args_1['post__in'] = explode( ",", $ids );
        }

        // Sticky posts
        if ( isset( $post_atts_1['sticky'] ) && $post_atts_1['sticky'] == 'only' ) {
            $query_args_1['post__in'] = get_option( 'sticky_posts' );
        } elseif ( isset( $post_atts_1['sticky'] ) && $post_atts_1['sticky'] == 'hide' ) {
            $query_args_1['post__not_in'] = get_option( 'sticky_posts' );
        }

        if( isset( $post_atts_2['category'] ) && ! empty( $post_atts_2['category'] ) ) {
            $query_args_2['category__in'] = explode( ",", $post_atts_2['category'] );
        }

        if( isset( $post_atts_2['ids'] ) && ! empty( $post_atts_2['ids'] ) ) {
            $query_args_2['post__in'] = explode( ",", $post_atts_2['ids'] );
        }


        // Sticky posts
        if ( isset( $post_atts_2['sticky'] ) && $post_atts_2['sticky'] == 'only' ) {
            $query_args_2['post__in'] = get_option( 'sticky_posts' );
        } elseif ( isset( $post_atts_2['sticky'] ) && $post_atts_2['sticky'] == 'hide' ) {
            $query_args_2['post__not_in'] = get_option( 'sticky_posts' );
        }

        $post_atts_1 = wp_parse_args( $post_atts_1, $query_args_1 );

        $post_atts_2 = wp_parse_args( $post_atts_2, $query_args_2 );

        $section_class = 'home-section home-blog-grid-with-list-section';

        if ( !empty ( $style ) ) {
            $section_class .= ' ' . $style;
        }

        if ( !empty ( $el_class ) ) {
            $section_class .= ' ' . $el_class;
        }

        $style_attr = '';

        if ( ! empty( $design_options ) && is_array( $design_options ) ) {
            foreach ( $design_options as $key => $design_option ) {
                if ( !empty ( $design_option ) ) {
                    $style_attr .= str_replace ( '_', '-', $key ) . ': ' . $design_option . 'px; ';
                }
            }
        } 
        $posts_query_1 = new WP_Query( $post_atts_1 );
        $posts_query_2 = new WP_Query( $post_atts_2 );

        if ( $posts_query_1->have_posts() || $posts_query_2->have_posts()  ) : ?>


        <section class="<?php echo esc_attr( $section_class ); ?>" <?php echo !empty ( $style_attr ) ? ' style="' . esc_attr( $style_attr ) . '"' : ''; ?>>
            <div class="container">
                <div class="home-blog-grid-with-list-section__inner">
                    <?php if ( ! empty ( $section_title ) || ! empty ( $section_nav_links ) ) {
                        echo '<header class="home-section__flex-header">';
                            if ( ! empty ( $section_title ) ) {
                                echo '<h2 class="section-title">' . esc_html( $section_title ) . '</h2>';
                            }
                            if ( ! empty ( $section_nav_links ) ) {
                                echo '<div class="blog-list-tabs"><div class="tabs-section-inner"><ul class="nav">';
                                    $i = 0;
                                    $nav_count = count( $section_nav_links );
                                    foreach ( $section_nav_links as $section_nav_link ) {
                                        if( $i < 1 && $nav_count > 1 ) {
                                            $active = ' active';
                                            $i++;
                                        } else {
                                            $active = '';
                                        }
                                        if( ! empty ( $section_nav_link['title'] ) && ! empty ( $section_nav_link['link'] ) ) {
                                            echo '<li class="nav-item"><a class="nav-link' . esc_attr( $active ) . '" href="' . esc_url( $section_nav_link['link'] ) . '">' . esc_html( $section_nav_link['title'] ) . '</a></li>';
                                        }
                                    }
                                echo '</ul></div></div>';
                            }
                        echo '</header>';
                    } ?>
                    <div class="blog-grid-with-list-section">
                        <div class="blog-grid-with-list-section__article--grid column">
                            
                            <?php while ( $posts_query_1->have_posts() ) : $posts_query_1->the_post(); ?>
                                <article id="post-<?php the_ID(); ?>" <?php post_class( 'article' ); ?>>
                                    <?php
                                        vodi_post_attachment();
                                        echo '<div class="article__summary">';
                                            vodi_post_header();
                                            if( empty( $hide_excerpt_1 ) ) {
                                                vodi_post_excerpt();
                                            }
                                        echo '</div>';
                                    ?>
                                </article>
                            <?php endwhile; ?>
                            

                        </div>

                        <div class="blog-grid-with-list-section__article--list column">
                            <?php while ( $posts_query_2->have_posts() ) : $posts_query_2->the_post(); ?>
                                <article id="post-<?php the_ID(); ?>" <?php post_class( 'article' ); ?>>
                                    <?php
                                        vodi_post_attachment();
                                        echo '<div class="article__summary">';
                                            vodi_post_header();
                                            if( empty( $hide_excerpt_2 ) ) {
                                                vodi_post_excerpt();
                                            }
                                        echo '</div>';
                                    ?>
                                </article>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php endif;
        wp_reset_postdata();
    }   
}

if ( ! function_exists( 'vodi_recent_comments' ) ) {
    function vodi_recent_comments( $args = array() ) {
        $defaults = apply_filters( 'vodi_recent_comments_default_args', array(
            'section_title'     => '',
            'section_nav_links' => array(),
            'design_options'    => array(),
            'limit'            =>'',
            'el_class'          => '',
        ) );

        $args = wp_parse_args( $args, $defaults );
        extract( $args );

        $section_class = 'home-section home-recent-comments';

        if ( !empty ( $el_class ) ) {
            $section_class .= ' ' . $el_class;
        }

        $style_attr = '';

        if ( ! empty( $design_options ) && is_array( $design_options ) ) {
            foreach ( $design_options as $key => $design_option ) {
                if ( !empty ( $design_option ) ) {
                    $style_attr .= str_replace ( '_', '-', $key ) . ': ' . $design_option . 'px; ';
                }
            }
        }

        ?><section class="<?php echo esc_attr( $section_class ); ?>" <?php echo !empty ( $style_attr ) ? ' style="' . esc_attr( $style_attr ) . '"' : ''; ?>>
                <div class="container">
                    <?php if ( ! empty ( $section_title ) ) {
                        echo '<header class="home-section__flex-header">';
                            if ( ! empty ( $section_title ) ) {
                                echo '<h2 class="section-title">' . esc_html( $section_title ) . '</h2>';
                            }
                            if ( ! empty ( $section_nav_links ) ) {
                                echo '<ul class="nav nav-tabs">';
                                    $i = 0;
                                    $nav_count = count( $section_nav_links );
                                    foreach ( $section_nav_links as $section_nav_link ) {
                                        if( $i < 1 && $nav_count > 1 ) {
                                            $active = ' active';
                                            $i++;
                                        } else {
                                            $active = '';
                                        }
                                        if( ! empty ( $section_nav_link['title'] ) && ! empty ( $section_nav_link['link'] ) ) {
                                            echo '<li class="nav-item"><a class="nav-link' . esc_attr( $active ) . '" href="' . esc_url( $section_nav_link['link'] ) . '">' . esc_html( $section_nav_link['title'] ) . '</a></li>';
                                        }
                                    }
                                echo '</ul>';
                            }
                        echo '</header>';
                    } ?>
                    <div class="recent-comments">
                        <?php the_widget( 'WP_Widget_Recent_Comments', array( 'title' =>' ', 'number' => $limit ) ); ?>
                    </div>
                </div>
            </section>
        <?php
        
    }
}
    
