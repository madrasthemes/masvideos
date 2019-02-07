<?php

if ( ! function_exists( 'vodi_section_full_width_banner_element' ) ) {
    function vodi_section_full_width_banner_element( $atts ) {
        if( isset( $atts['className'] ) ) {
            $atts['el_class'] = $atts['className'];
            unset( $atts['className'] );
        }
        ob_start();
        vodi_section_full_width_banner( $atts );
        return ob_get_clean();
    }
}


if ( ! function_exists( 'vodi_video_section_element' ) ) {
    function vodi_video_section_element( $atts ) {
        if( isset( $atts['className'] ) ) {
            $atts['el_class'] = $atts['className'];
            unset( $atts['className'] );
        }

        ob_start();
        vodi_video_section( $atts );
        return ob_get_clean();
    }
}

if ( ! function_exists( 'vodi_movie_section_aside_header_element' ) ) {
    function vodi_movie_section_aside_header_element( $atts ) {
        if( isset( $atts['className'] ) ) {
            $atts['el_class'] = $atts['className'];
            unset( $atts['className'] );
        }

        ob_start();
        vodi_movie_section_aside_header( $atts );
        return ob_get_clean();
    }
}

if ( ! function_exists( 'vodi_video_section_aside_header_element' ) ) {
    function vodi_video_section_aside_header_element( $atts ) {
        if( isset( $atts['className'] ) ) {
            $atts['el_class'] = $atts['className'];
            unset( $atts['className'] );
        }

        ob_start();
        vodi_video_section_aside_header( $atts );
        return ob_get_clean();
    }
}


if ( ! function_exists( 'vodi_section_movies_carousel_aside_header_element' ) ) {
    function vodi_section_movies_carousel_aside_header_element( $atts ) {
        if( isset( $atts['className'] ) ) {
            $atts['el_class'] = $atts['className'];
            unset( $atts['className'] );
        }

        ob_start();
        vodi_section_movies_carousel_aside_header( $atts );
        return ob_get_clean();
    }
}

if ( ! function_exists( 'vodi_section_videos_carousel_aside_header_element' ) ) {
    function vodi_section_videos_carousel_aside_header_element( $atts ) {
        if( isset( $atts['className'] ) ) {
            $atts['el_class'] = $atts['className'];
            unset( $atts['className'] );
        }

        ob_start();
        vodi_section_videos_carousel_aside_header( $atts );
        return ob_get_clean();
    }
}

if ( ! function_exists( 'vodi_section_movies_carousel_nav_header_element' ) ) {
    function vodi_section_movies_carousel_nav_header_element( $atts ) {
        if( isset( $atts['className'] ) ) {
            $atts['el_class'] = $atts['className'];
            unset( $atts['className'] );
        }

        ob_start();
        vodi_section_movies_carousel_nav_header( $atts );
        return ob_get_clean();
    }
}

if ( ! function_exists( 'vodi_section_videos_carousel_nav_header_element' ) ) {
    function vodi_section_videos_carousel_nav_header_element( $atts ) {
        if( isset( $atts['className'] ) ) {
            $atts['el_class'] = $atts['className'];
            unset( $atts['className'] );
        }

        ob_start();
        vodi_section_videos_carousel_nav_header( $atts );
        return ob_get_clean();
    }
}

if ( ! function_exists( 'vodi_section_movies_carousel_flex_header_element' ) ) {
    function vodi_section_movies_carousel_flex_header_element( $atts ) {
        if( isset( $atts['className'] ) ) {
            $atts['el_class'] = $atts['className'];
            unset( $atts['className'] );
        }

        ob_start();
        vodi_section_movies_carousel_flex_header( $atts );
        return ob_get_clean();
    }
}

if ( ! function_exists( 'vodi_section_videos_carousel_flex_header_element' ) ) {
    function vodi_section_videos_carousel_flex_header_element( $atts ) {
        if( isset( $atts['className'] ) ) {
            $atts['el_class'] = $atts['className'];
            unset( $atts['className'] );
        }

        ob_start();
        vodi_section_videos_carousel_flex_header( $atts );
        return ob_get_clean();
    }
}

if ( ! function_exists( 'vodi_videos_with_featured_video_element' ) ) {
    function vodi_videos_with_featured_video_element( $atts ) {
        if( isset( $atts['className'] ) ) {
            $atts['el_class'] = $atts['className'];
            unset( $atts['className'] );
        }

        ob_start();
        vodi_videos_with_featured_video( $atts );
        return ob_get_clean();
    }
}

if ( ! function_exists( 'vodi_featured_movies_carousel_element' ) ) {
    function vodi_featured_movies_carousel_element( $atts ) {
        if( isset( $atts['className'] ) ) {
            $atts['el_class'] = $atts['className'];
            unset( $atts['className'] );
        }

        ob_start();
        vodi_featured_movies_carousel( $atts );
        return ob_get_clean();
    }
}

if ( ! function_exists( 'vodi_section_featured_video_element' ) ) {
    function vodi_section_featured_video_element( $atts ) {
        if( isset( $atts['className'] ) ) {
            $atts['el_class'] = $atts['className'];
            unset( $atts['className'] );
        }

        ob_start();
        vodi_section_featured_video( $atts );
        return ob_get_clean();
    }
}

if ( ! function_exists( 'vodi_section_featured_tv_show_element' ) ) {
    function vodi_section_featured_tv_show_element( $atts ) {
        if( isset( $atts['className'] ) ) {
            $atts['el_class'] = $atts['className'];
            unset( $atts['className'] );
        }

        ob_start();
        vodi_section_featured_tv_show( $atts );
        return ob_get_clean();
    }
}

if ( ! function_exists( 'vodi_banner_with_section_videos_element' ) ) {
    function vodi_banner_with_section_videos_element( $atts ) {
        if( isset( $atts['className'] ) ) {
            $atts['el_class'] = $atts['className'];
            unset( $atts['className'] );
        }

        ob_start();
        vodi_banner_with_section_videos( $atts );
        return ob_get_clean();
    }
}

if ( ! function_exists( 'vodi_blog_list_section_element' ) ) {
    function vodi_blog_list_section_element( $atts ) {
        if( isset( $atts['className'] ) ) {
            $atts['el_class'] = $atts['className'];
            unset( $atts['className'] );
        }

        ob_start();
        vodi_blog_list_section( $atts );
        return ob_get_clean();
    }
}

if ( ! function_exists( 'vodi_blog_grid_section_element' ) ) {
    function vodi_blog_grid_section_element( $atts ) {
        if( isset( $atts['className'] ) ) {
            $atts['el_class'] = $atts['className'];
            unset( $atts['className'] );
        }

        ob_start();
        vodi_blog_grid_section( $atts );
        return ob_get_clean();
    }
}

if ( ! function_exists( 'vodi_blog_tab_section_element' ) ) {
    function vodi_blog_tab_section_element( $atts ) {
        if( isset( $atts['className'] ) ) {
            $atts['el_class'] = $atts['className'];
            unset( $atts['className'] );
        }

        ob_start();
        vodi_blog_tab_section( $atts );
        return ob_get_clean();
    }
}

if ( ! function_exists( 'vodi_single_featured_movie_section_element' ) ) {
    function vodi_single_featured_movie_section_element( $atts ) {
        if( isset( $atts['className'] ) ) {
            $atts['el_class'] = $atts['className'];
            unset( $atts['className'] );
        }

        ob_start();
        vodi_single_featured_movie( $atts );
        return ob_get_clean();
    }
}

if ( ! function_exists( 'vodi_slider_movies_element' ) ) {
    function vodi_slider_movies_element( $atts ) {
        if( isset( $atts['className'] ) ) {
            $atts['el_class'] = $atts['className'];
            unset( $atts['className'] );
        }
        
        ob_start();
        vodi_slider_movies( $atts );
        return ob_get_clean();
    }
}

if ( ! function_exists( 'vodi_hot_premieres_block_element' ) ) {
    function vodi_hot_premieres_block_element( $atts ) {
        if( isset( $atts['className'] ) ) {
            $atts['el_class'] = $atts['className'];
            unset( $atts['className'] );
        }
        ob_start();
        vodi_hot_premieres_block( $atts );
        return ob_get_clean();
    }
}

if ( ! function_exists( 'vodi_section_live_videos_element' ) ) {
    function vodi_section_live_videos_element( $atts ) {
        if( isset( $atts['className'] ) ) {
            $atts['el_class'] = $atts['className'];
            unset( $atts['className'] );
        }

        ob_start();
        vodi_section_live_videos( $atts );
        return ob_get_clean();
    }
}

if ( ! function_exists( 'vodi_section_coming_soon_videos_element' ) ) {
    function vodi_section_coming_soon_videos_element( $atts ) {
        if( isset( $atts['className'] ) ) {
            $atts['el_class'] = $atts['className'];
            unset( $atts['className'] );
        }

        ob_start();
        vodi_section_coming_soon_videos( $atts );
        return ob_get_clean();
    }
}

if ( ! function_exists( 'vodi_movies_list_element' ) ) {
    function vodi_movies_list_element( $atts ) {
        if( isset( $atts['className'] ) ) {
            $atts['el_class'] = $atts['className'];
            unset( $atts['className'] );
        }

        ob_start();
        vodi_movies_list( $atts );
        return ob_get_clean();
    }
}

if ( ! function_exists( 'vodi_blog_grid_with_list_section_element' ) ) {
    function vodi_blog_grid_with_list_section_element( $atts ) {
        if( isset( $atts['className'] ) ) {
            $atts['el_class'] = $atts['className'];
            unset( $atts['className'] );
        }

        ob_start();
        vodi_blog_grid_with_list_section( $atts );
        return ob_get_clean();
    }
}

if ( ! function_exists( 'vodi_recent_comments_element' ) ) {
    function vodi_recent_comments_element( $atts ) {
        if( isset( $atts['className'] ) ) {
            $atts['el_class'] = $atts['className'];
            unset( $atts['className'] );
        }

        ob_start();
        vodi_recent_comments( $atts );
        return ob_get_clean();
    }
}

if ( ! function_exists( 'vodi_section_event_category_list_element' ) ) {
    function vodi_section_event_category_list_element( $atts ) {
        if( isset( $atts['className'] ) ) {
            $atts['el_class'] = $atts['className'];
            unset( $atts['className'] );
        }

        ob_start();
        vodi_section_event_category_list( $atts );
        return ob_get_clean();
    }
}