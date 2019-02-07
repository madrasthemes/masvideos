<?php
/**
 * Gutenberg Blocks
 *
 * @package Vodi/Classes
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Vodi Gutenberg Blocks class.
 */
class Vodi_Gutenberg_Blocks {

    /**
     * Init blocks.
     */
    public static function init() {
        if( function_exists( 'register_block_type' ) ) {
            $blocks = array(
                'section-full-width-banner'    => array(
                    'attributes'        => array(
                        'banner_image'  => array(
                            'type'      => 'number',
                        ),
                        'banner_link'   => array(
                            'type'      => 'string',
                        ),
                        'className'     => array(
                            'type'      => 'string',
                        ),
                    ),
                    'editor_script'     => 'vodi-section-full-width-banner',
                    'editor_style'      => 'vodi-section-full-width-banner-editor',
                    'style'             => 'vodi-section-full-width-banner',
                    'render_callback'   => 'vodi_section_full_width_banner_element',
                ),
                'video-section'    => array(
                    'attributes'        => array(
                        'section_title' => array(
                            'type'      => 'string',
                        ),
                        'section_nav_links'=> array(
                            'type'      => 'array',
                            'items'   => [
                                'type' => 'object',
                            ],
                        ),
                        'section_background'=> array(
                            'type'      => 'string',
                        ),
                        'section_style'=> array(
                            'type'      => 'string',
                        ),
                        'footer_action_text'=> array(
                            'type'          => 'string',
                        ),
                        'footer_action_link'=> array(
                            'type'          => 'string',
                            'default'       => '#',
                        ),
                        'shortcode_atts'=> array(
                            'type'      => 'object',
                            'default'   => array(
                                'limit'         => 10,
                                'columns'       => 4,
                                'orderby'       => 'date',
                                'order'         => 'DESC',
                                'featured'      => false,
                                'top_rated'     => false,
                            ),
                        ),
                        'design_options'=> array(
                            'type'      => 'object',
                        ),
                        'className'     => array(
                            'type'      => 'string',
                        ),
                    ),
                    'editor_script'     => 'vodi-video-section',
                    'editor_style'      => 'vodi-video-section-editor',
                    'style'             => 'vodi-video-section',
                    'render_callback'   => 'vodi_video_section_element',
                ),
                'hot-premieres-block'    => array(
                    'attributes'        => array(
                        'section_title' => array(
                            'type'      => 'string',
                        ),
                        'hide_movie_title'  => array(
                            'type'      => 'boolean',
                            'default'   => true,
                        ),
                        'section_nav_links'=> array(
                            'type'      => 'array',
                            'items'   => [
                                'type' => 'object',
                            ],
                        ),
                        'shortcode_atts'=> array(
                            'type'      => 'object',
                            'default'   => array(
                                'limit'         => 10,
                                'columns'       => 4,
                                'orderby'       => 'date',
                                'order'         => 'DESC',
                                'featured'      => false,
                                'top_rated'     => false,
                            ),
                        ),
                        'design_options'=> array(
                            'type'      => 'object',
                        ),
                        'className'     => array(
                            'type'      => 'string',
                        ),
                    ),
                    'editor_script'     => 'vodi-hot-premieres-block',
                    'editor_style'      => 'vodi-hot-premieres-block-editor',
                    'style'             => 'vodi-hot-premieres-block',
                    'render_callback'   => 'vodi_hot_premieres_block_element',
                ),
                'movie-section-aside-header'    => array(
                    'attributes'        => array(
                        'section_title' => array(
                            'type'      => 'string',
                        ),
                        'section_subtitle' => array(
                            'type'      => 'string',
                        ),
                        'action_text'=> array(
                            'type'          => 'string',
                        ),
                        'action_link'=> array(
                            'type'          => 'string',
                            'default'       => '#',
                        ),
                        'section_background'=> array(
                            'type'      => 'string',
                        ),
                        'section_style'=> array(
                            'type'      => 'string',
                        ),
                        'shortcode_atts_1'=> array(
                            'type'      => 'object',
                            'default'   => array(
                                'limit'         => 5,
                                'columns'       => 5,
                                'orderby'       => 'date',
                                'order'         => 'DESC',
                                'featured'      => false,
                                'top_rated'     => false,
                            ),
                        ),
                        'shortcode_atts_2'=> array(
                            'type'      => 'object',
                            'default'   => array(
                                'limit'         => 7,
                                'columns'       => 7,
                                'orderby'       => 'date',
                                'order'         => 'DESC',
                                'featured'      => false,
                                'top_rated'     => false,
                            ),
                        ),
                        'design_options'=> array(
                            'type'      => 'object',
                        ),
                        'className'     => array(
                            'type'      => 'string',
                        ),
                    ),
                    'editor_script'     => 'vodi-movie-section-aside-header',
                    'editor_style'      => 'vodi-movie-section-aside-header-editor',
                    'style'             => 'vodi-movie-section-aside-header',
                    'render_callback'   => 'vodi_movie_section_aside_header_element',
                ),
                'video-section-aside-header'    => array(
                    'attributes'        => array(
                        'section_title' => array(
                            'type'      => 'string',
                        ),
                        'section_subtitle' => array(
                            'type'      => 'string',
                        ),
                        'action_text'=> array(
                            'type'          => 'string',
                        ),
                        'action_link'=> array(
                            'type'          => 'string',
                            'default'       => '#',
                        ),
                        'section_background'=> array(
                            'type'      => 'string',
                        ),
                        'section_style'=> array(
                            'type'      => 'string',
                        ),
                        'shortcode_atts_1'=> array(
                            'type'      => 'object',
                            'default'   => array(
                                'limit'         => 4,
                                'columns'       => 4,
                                'orderby'       => 'date',
                                'order'         => 'DESC',
                                'featured'      => false,
                                'top_rated'     => false,
                            ),
                        ),
                        'shortcode_atts_2'=> array(
                            'type'      => 'object',
                            'default'   => array(
                                'limit'         => 6,
                                'columns'       => 6,
                                'orderby'       => 'date',
                                'order'         => 'DESC',
                                'featured'      => false,
                                'top_rated'     => false,
                            ),
                        ),
                        'design_options'=> array(
                            'type'      => 'object',
                        ),
                        'className'     => array(
                            'type'      => 'string',
                        ),
                    ),
                    'editor_script'     => 'vodi-video-section-aside-header',
                    'render_callback'   => 'vodi_video_section_aside_header_element',
                ),
                'section-movies-carousel-aside-header'    => array(
                    'attributes'        => array(
                        'section_title' => array(
                            'type'      => 'string',
                        ),
                        'section_subtitle' => array(
                            'type'      => 'string',
                        ),
                        'action_text'=> array(
                            'type'          => 'string',
                        ),
                        'action_link'=> array(
                            'type'          => 'string',
                            'default'       => '#',
                        ),
                        'header_posisition'=> array(
                            'type'      => 'string',
                        ),
                        'section_background'=> array(
                            'type'      => 'string',
                        ),
                        'section_style'=> array(
                            'type'      => 'string',
                        ),
                        'shortcode_atts'=> array(
                            'type'      => 'object',
                            'default'   => array(
                                'limit'         => 15,
                                'columns'       => 6,
                                'orderby'       => 'date',
                                'order'         => 'DESC',
                                'featured'      => false,
                                'top_rated'     => false,
                            ),
                        ),
                        'carousel_args' => array(
                            'type'      => 'object',
                            'default'   => array(
                                'slidesToShow'  => 6,
                                'slidesToScroll'=> 6,
                                'dots'          => false,
                                'arrows'        => true,
                                'autoplay'      => false,
                                'infinite'      => false,
                            ),
                        ),
                        'design_options'=> array(
                            'type'      => 'object',
                        ),
                        'className'     => array(
                            'type'      => 'string',
                        ),
                    ),
                    'editor_script'     => 'vodi-section-movies-carousel-aside-header',
                    'editor_style'      => 'vodi-section-movies-carousel-aside-header-editor',
                    'style'             => 'vodi-section-movies-carousel-aside-header',
                    'render_callback'   => 'vodi_section_movies_carousel_aside_header_element',
                ),
                'section-videos-carousel-aside-header'    => array(
                    'attributes'        => array(
                        'section_title' => array(
                            'type'      => 'string',
                        ),
                        'section_subtitle' => array(
                            'type'      => 'string',
                        ),
                        'action_text'=> array(
                            'type'          => 'string',
                        ),
                        'action_link'=> array(
                            'type'          => 'string',
                            'default'       => '#',
                        ),
                        'header_posisition'=> array(
                            'type'      => 'string',
                        ),
                        'section_background'=> array(
                            'type'      => 'string',
                        ),
                        'section_style'=> array(
                            'type'      => 'string',
                        ),
                        'shortcode_atts'=> array(
                            'type'      => 'object',
                            'default'   => array(
                                'limit'         => 15,
                                'columns'       => 4,
                                'orderby'       => 'date',
                                'order'         => 'DESC',
                                'featured'      => false,
                                'top_rated'     => false,
                            ),
                        ),
                        'carousel_args' => array(
                            'type'      => 'object',
                            'default'   => array(
                                'slidesToShow'  => 4,
                                'slidesToScroll'=> 4,
                                'dots'          => false,
                                'arrows'        => true,
                                'autoplay'      => false,
                                'infinite'      => false,
                            ),
                        ),
                        'design_options'=> array(
                            'type'      => 'object',
                        ),
                        'className'     => array(
                            'type'      => 'string',
                        ),
                    ),
                    'editor_script'     => 'vodi-section-videos-carousel-aside-header',
                    'editor_style'      => 'vodi-section-videos-carousel-aside-header-editor',
                    'style'             => 'vodi-section-videos-carousel-aside-header',
                    'render_callback'   => 'vodi_section_videos_carousel_aside_header_element',
                ),
                'section-movies-carousel-nav-header'    => array(
                    'attributes'        => array(
                        'section_title' => array(
                            'type'      => 'string',
                        ),
                        'section_nav_links'=> array(
                            'type'      => 'array',
                            'items'   => [
                                'type' => 'object',
                            ],
                        ),
                        'section_background'=> array(
                            'type'      => 'string',
                        ),
                        'section_style'=> array(
                            'type'      => 'string',
                        ),
                        'shortcode_atts'=> array(
                            'type'      => 'object',
                            'default'   => array(
                                'limit'         => 15,
                                'columns'       => 7,
                                'orderby'       => 'date',
                                'order'         => 'DESC',
                                'featured'      => false,
                                'top_rated'     => false,
                            ),
                        ),
                        'carousel_args' => array(
                            'type'      => 'object',
                            'default'   => array(
                                'slidesToShow'  => 7,
                                'slidesToScroll'=> 7,
                                'dots'          => false,
                                'arrows'        => true,
                                'autoplay'      => false,
                                'infinite'      => false,
                            ),
                        ),
                        'design_options'=> array(
                            'type'      => 'object',
                        ),
                        'className'     => array(
                            'type'      => 'string',
                        ),
                    ),
                    'editor_script'     => 'vodi-section-movies-carousel-nav-header',
                    'editor_style'      => 'vodi-section-movies-carousel-nav-header-editor',
                    'style'             => 'vodi-section-movies-carousel-nav-header',
                    'render_callback'   => 'vodi_section_movies_carousel_nav_header_element',
                ),
                'section-videos-carousel-nav-header'    => array(
                    'attributes'        => array(
                        'section_title' => array(
                            'type'      => 'string',
                        ),
                        'section_nav_links'=> array(
                            'type'      => 'array',
                            'items'   => [
                                'type' => 'object',
                            ],
                        ),
                        'section_background'=> array(
                            'type'      => 'string',
                        ),
                        'shortcode_atts'=> array(
                            'type'      => 'object',
                            'default'   => array(
                                'limit'         => 15,
                                'columns'       => 5,
                                'orderby'       => 'date',
                                'order'         => 'DESC',
                                'featured'      => false,
                                'top_rated'     => false,
                            ),
                        ),
                        'carousel_args' => array(
                            'type'      => 'object',
                            'default'   => array(
                                'slidesToShow'  => 5,
                                'slidesToScroll'=> 5,
                                'dots'          => false,
                                'arrows'        => true,
                                'autoplay'      => false,
                                'infinite'      => false,
                            ),
                        ),
                        'design_options'=> array(
                            'type'      => 'object',
                        ),
                        'className'     => array(
                            'type'      => 'string',
                        ),
                    ),
                    'editor_script'     => 'vodi-section-videos-carousel-nav-header',
                    'editor_style'      => 'vodi-section-videos-carousel-nav-header-editor',
                    'style'             => 'vodi-section-videos-carousel-nav-header',
                    'render_callback'   => 'vodi_section_videos_carousel_nav_header_element',
                ),
                'section-movies-carousel-flex-header'    => array(
                    'attributes'        => array(
                        'section_title' => array(
                            'type'      => 'string',
                        ),
                        'section_background'=> array(
                            'type'      => 'string',
                        ),
                        'section_nav_links'=> array(
                            'type'      => 'array',
                            'items'   => [
                                'type' => 'object',
                            ],
                        ),
                        'section_style'=> array(
                            'type'      => 'string',
                        ),
                        'footer_action_text'=> array(
                            'type'          => 'string',
                        ),
                        'footer_action_link'=> array(
                            'type'          => 'string',
                            'default'       => '#',
                        ),
                        'shortcode_atts'=> array(
                            'type'      => 'object',
                            'default'   => array(
                                'limit'         => 15,
                                'columns'       => 6,
                                'orderby'       => 'date',
                                'order'         => 'DESC',
                                'featured'      => false,
                                'top_rated'     => false,
                            ),
                        ),
                        'carousel_args' => array(
                            'type'      => 'object',
                            'default'   => array(
                                'slidesToShow'  => 6,
                                'slidesToScroll'=> 6,
                                'dots'          => false,
                                'arrows'        => true,
                                'autoplay'      => false,
                                'infinite'      => false,
                            ),
                        ),
                        'design_options'=> array(
                            'type'      => 'object',
                        ),
                        'className'     => array(
                            'type'      => 'string',
                        ),
                    ),
                    'editor_script'     => 'vodi-section-movies-carousel-flex-header',
                    'editor_style'      => 'vodi-section-movies-carousel-flex-header-editor',
                    'style'             => 'vodi-section-movies-carousel-flex-header',
                    'render_callback'   => 'vodi_section_movies_carousel_flex_header_element',
                ),
                'section-videos-carousel-flex-header'    => array(
                    'attributes'        => array(
                        'section_title' => array(
                            'type'      => 'string',
                        ),
                        'section_nav_links'=> array(
                            'type'      => 'array',
                            'items'   => [
                                'type' => 'object',
                            ],
                        ),
                        'section_background'=> array(
                            'type'      => 'string',
                        ),
                        'section_style'=> array(
                            'type'      => 'string',
                        ),
                        'footer_action_text'=> array(
                            'type'          => 'string',
                        ),
                        'footer_action_link'=> array(
                            'type'          => 'string',
                            'default'       => '#',
                        ),
                        'shortcode_atts'=> array(
                            'type'      => 'object',
                            'default'   => array(
                                'limit'         => 15,
                                'columns'       => 5,
                                'orderby'       => 'date',
                                'order'         => 'DESC',
                                'featured'      => false,
                                'top_rated'     => false,
                            ),
                        ),
                        'carousel_args' => array(
                            'type'      => 'object',
                            'default'   => array(
                                'slidesToShow'  => 5,
                                'slidesToScroll'=> 5,
                                'dots'          => false,
                                'arrows'        => true,
                                'autoplay'      => false,
                                'infinite'      => false,
                            ),
                        ),
                        'design_options'=> array(
                            'type'      => 'object',
                        ),
                        'className'     => array(
                            'type'      => 'string',
                        ),
                    ),
                    'editor_script'     => 'vodi-section-videos-carousel-flex-header',
                    'editor_style'      => 'vodi-section-videos-carousel-flex-header-editor',
                    'style'             => 'vodi-section-videos-carousel-flex-header',
                    'render_callback'   => 'vodi_section_videos_carousel_flex_header_element',
                ),
                'single-featured-movie'    => array(
                    'attributes'        => array(

                        'movie_action_icon' => array(
                            'type'      => 'string',
                        ),

                        'action_text' => array(
                            'type'      => 'string',
                        ),

                        'movie_id'=> array(
                            'type'      => 'string',
                        ),

                        'design_options'=> array(
                            'type'      => 'object',
                        ),

                        'bg_image'  => array(
                            'type'      => 'number',
                        ),
                    ),
                    'editor_script'     => 'vodi-single-featured-movie',
                    'editor_style'      => 'vodi-single-featured-movie-editor',
                    'style'             => 'vodi-single-featured-movie',
                    'render_callback'   => 'vodi_single_featured_movie_section_element',
                ),
                'videos-with-featured-video'    => array(
                    'attributes'        => array(
                        'section_title' => array(
                            'type'      => 'string',
                        ),
                        'section_nav_links'=> array(
                            'type'      => 'array',
                            'items'   => [
                                'type' => 'object',
                            ],
                        ),
                        'bg_image'  => array(
                            'type'      => 'number',
                        ),
                        'section_background'=> array(
                            'type'      => 'string',
                        ),
                        'section_style'=> array(
                            'type'      => 'string',
                        ),
                        'feature_video_id'=> array(
                            'type'      => 'string',
                        ),
                        'shortcode_atts'=> array(
                            'type'      => 'object',
                            'default'   => array(
                                'limit'         => 6,
                                'columns'       => 3,
                                'orderby'       => 'date',
                                'order'         => 'DESC',
                                'featured'      => false,
                                'top_rated'     => false,
                            ),
                        ),
                        'design_options'=> array(
                            'type'      => 'object',
                        ),
                        'className'     => array(
                            'type'      => 'string',
                        ),
                    ),
                    'editor_script'     => 'vodi-videos-with-featured-video',
                    'editor_style'      => 'videos-with-featured-video-editor',
                    'style'             => 'videos-with-featured-video',
                    'render_callback'   => 'vodi_videos_with_featured_video_element',
                ),
                'featured-movies-carousel'    => array(
                    'attributes'        => array(
                        'feature_movie_pre_title' => array(
                            'type'      => 'string',
                        ),
                        'feature_movie_title' => array(
                            'type'      => 'string',
                        ),
                        'feature_movie_subtitle' => array(
                            'type'      => 'string',
                        ),
                        'section_nav_links'=> array(
                            'type'      => 'array',
                            'items'   => [
                                'type' => 'object',
                            ],
                        ),
                        'section_background'=> array(
                            'type'      => 'string',
                        ),
                        'section_style'=> array(
                            'type'      => 'string',
                        ),
                        'bg_image'  => array(
                            'type'      => 'number',
                        ),
                        'shortcode_atts'=> array(
                            'type'      => 'object',
                            'default'   => array(
                                'limit'         => 15,
                                'columns'       => 8,
                                'orderby'       => 'date',
                                'order'         => 'DESC',
                                'featured'      => false,
                                'top_rated'     => false,
                            ),
                        ),
                        'carousel_args' => array(
                            'type'      => 'object',
                            'default'   => array(
                                'slidesToShow'  => 8,
                                'slidesToScroll'=> 8,
                                'dots'          => false,
                                'arrows'        => true,
                                'autoplay'      => false,
                                'infinite'      => false,
                            ),
                        ),
                        'design_options'=> array(
                            'type'      => 'object',
                        ),
                        'className'     => array(
                            'type'      => 'string',
                        ),
                    ),
                    'editor_script'     => 'vodi-featured-movies-carousel',
                    'editor_style'      => 'vodi-featured-movies-carousel-editor',
                    'style'             => 'vodi-featured-movies-carousel',
                    'render_callback'   => 'vodi_featured_movies_carousel_element',
                ),
                'section-featured-video'    => array(
                    'attributes'        => array(
                        'feature_video_action_icon' => array(
                            'type'      => 'string',
                        ),
                        'video_id'=> array(
                            'type'      => 'string',
                        ),
                        'image'  => array(
                            'type'      => 'number',
                        ),
                        'bg_image'  => array(
                            'type'      => 'number',
                        ),
                        'design_options'=> array(
                            'type'      => 'object',
                        ),
                        'className'     => array(
                            'type'      => 'string',
                        ),
                    ),
                    'editor_script'     => 'vodi-section-featured-video',
                    'editor_style'      => 'vodi-section-featured-video-editor',
                    'style'             => 'vodi-section-featured-video',
                    'render_callback'   => 'vodi_section_featured_video_element',
                ),
                'section-featured-tv-show'    => array(
                    'attributes'        => array(
                        'feature_tv_show_pre_title' => array(
                            'type'      => 'string',
                        ),
                        'feature_tv_show_title' => array(
                            'type'      => 'string',
                        ),
                        'feature_tv_show_subtitle' => array(
                            'type'      => 'string',
                        ),
                        'section_style'=> array(
                            'type'      => 'string',
                        ),
                        'image'  => array(
                            'type'      => 'number',
                        ),
                        'bg_image'  => array(
                            'type'      => 'number',
                        ),
                        'shortcode_atts'=> array(
                            'type'      => 'object',
                            'default'   => array(
                                'limit'         => 5,
                                'columns'       => 5,
                                'orderby'       => 'date',
                                'order'         => 'DESC',
                                'featured'      => false,
                                'top_rated'     => false,
                            ),
                        ),
                        'design_options'=> array(
                            'type'      => 'object',
                        ),
                        'className'     => array(
                            'type'      => 'string',
                        ),
                    ),
                    'editor_script'     => 'vodi-section-featured-tv-show',
                    'render_callback'   => 'vodi_section_featured_tv_show_element',
                ),
                'banner-with-section-videos'    => array(
                    'attributes'        => array(
                        'section_title' => array(
                            'type'      => 'string',
                        ),
                        'section_nav_links'=> array(
                            'type'      => 'array',
                            'items'   => [
                                'type' => 'object',
                            ],
                        ),
                        'section_background'=> array(
                            'type'      => 'string',
                        ),
                        'section_style'=> array(
                            'type'      => 'string',
                        ),
                        'footer_action_text'=> array(
                            'type'          => 'string',
                        ),
                        'footer_action_link'=> array(
                            'type'          => 'string',
                            'default'       => '#',
                        ),
                        'image'  => array(
                            'type'      => 'number',
                        ),
                        'shortcode_atts'=> array(
                            'type'      => 'object',
                            'default'   => array(
                                'limit'         => 8,
                                'columns'       => 4,
                                'orderby'       => 'date',
                                'order'         => 'DESC',
                                'featured'      => false,
                                'top_rated'     => false,
                            ),
                        ),
                        'design_options'=> array(
                            'type'      => 'object',
                        ),
                        'className'     => array(
                            'type'      => 'string',
                        ),
                    ),
                    'editor_script'     => 'vodi-banner-with-section-videos',
                    'editor_style'      => 'vodi-banner-with-section-videos-editor',
                    'style'             => 'vodi-banner-with-section-videos',
                    'render_callback'   => 'vodi_banner_with_section_videos_element',
                ),
                'blog-list-section'    => array(
                    'attributes'        => array(
                        'section_title' => array(
                            'type'      => 'string',
                        ),
                        'section_nav_links'=> array(
                            'type'      => 'array',
                            'items'   => [
                                'type' => 'object',
                            ],
                        ),
                        'style'         => array(
                            'type'      => 'string',
                        ),
                        'hide_excerpt'  => array(
                            'type'      => 'boolean',
                            'default'   => false,
                        ),
                        'enable_divider'=> array(
                            'type'      => 'boolean',
                            'default'   => false,
                        ),
                        'post_atts'     => array(
                            'type'      => 'object',
                            'default'   => array(
                                'posts_per_page'=> 5,
                                'orderby'       => 'date',
                                'order'         => 'DESC',
                            ),
                        ),
                        'design_options'=> array(
                            'type'      => 'object',
                        ),
                        'className'     => array(
                            'type'      => 'string',
                        ),
                    ),
                    'editor_script'     => 'vodi-blog-list-section',
                    'editor_style'      => 'vodi-blog-list-section-editor',
                    'style'             => 'vodi-blog-list-section',
                    'render_callback'   => 'vodi_blog_list_section_element',
                ),
                'blog-grid-section'    => array(
                    'attributes'        => array(
                        'section_title' => array(
                            'type'      => 'string',
                        ),
                        'section_nav_links'=> array(
                            'type'      => 'array',
                            'items'   => [
                                'type' => 'object',
                            ],
                        ),
                        'style'         => array(
                            'type'      => 'string',
                        ),
                        'hide_excerpt'  => array(
                            'type'      => 'boolean',
                            'default'   => false,
                        ),
                        'columns'       => array(
                            'type'      => 'number',
                            'default'   => 5,
                        ),
                        'post_atts'     => array(
                            'type'      => 'object',
                            'default'   => array(
                                'posts_per_page'=> 5,
                                'orderby'       => 'date',
                                'order'         => 'DESC',
                            ),
                        ),
                        'design_options'=> array(
                            'type'      => 'object',
                        ),
                        'className'     => array(
                            'type'      => 'string',
                        ),
                    ),
                    'editor_script'     => 'vodi-blog-grid-section',
                    'editor_style'      => 'vodi-blog-grid-section-editor',
                    'style'             => 'vodi-blog-grid-section',
                    'render_callback'   => 'vodi_blog_grid_section_element',
                ),
                'blog-tab-section'    => array(
                    'attributes'        => array(
                        'tab_args'=> array(
                            'type'      => 'array',
                            'items'   => [
                                'type' => 'object',
                            ],
                        ),
                        'section_nav_links'=> array(
                            'type'      => 'array',
                            'items'   => [
                                'type' => 'object',
                            ],
                        ),
                        'style'         => array(
                            'type'      => 'string',
                        ),
                        'design_options'=> array(
                            'type'      => 'object',
                        ),
                        'className'     => array(
                            'type'      => 'string',
                        ),
                    ),
                    'editor_script'     => 'vodi-blog-tab-section',
                    'editor_style'      => 'vodi-blog-tab-section-editor',
                    'style'             => 'vodi-blog-tab-section',
                    'render_callback'   => 'vodi_blog_tab_section_element',
                ),
                'slider-movies'    => array(
                    'attributes'        => array(
                        'shortcode_atts'=> array(
                            'type'      => 'object',
                            'default'   => array(
                                'limit'         => 3,
                                'columns'       => 3,
                                'orderby'       => 'date',
                                'order'         => 'DESC',
                                'featured'      => false,
                                'top_rated'     => false,
                            ),
                        ),
                        'design_options'=> array(
                            'type'      => 'object',
                        ),
                        'className'     => array(
                            'type'      => 'string',
                        ),
                    ),
                    'editor_script'     => 'vodi-slider-movies',
                    'editor_style'      => 'vodi-slider-movies-editor',
                    'style'             => 'vodi-slider-movies',
                    'render_callback'   => 'vodi_slider_movies_element',
                ),
                'section-live-videos'    => array(
                    'attributes'        => array(
                        'live_videos_title' => array(
                            'type'          => 'string',
                        ),
                        'footer_action_text' => array(
                            'type'           => 'string',
                        ),
                        'footer_action_link' => array(
                            'type'           => 'string',
                        ),
                        'shortcode_atts'=> array(
                            'type'      => 'object',
                            'default'   => array(
                                'limit'         => 3,
                                'columns'       => 1,
                                'orderby'       => 'date',
                                'order'         => 'DESC',
                                'featured'      => false,
                                'top_rated'     => false,
                            ),
                        ),
                        'design_options'=> array(
                            'type'      => 'object',
                        ),
                        'className'     => array(
                            'type'      => 'string',
                        ),
                    ),
                    'editor_script'     => 'vodi-section-live-videos',
                    'editor_style'      => 'vodi-section-live-videos-editor',
                    'style'             => 'vodi-section-live-videos',
                    'render_callback'   => 'vodi_section_live_videos_element',
                ),
                'section-coming-soon-videos'    => array(
                    'attributes'        => array(
                        'coming_soon_videos_title' => array(
                            'type'                 => 'string',
                        ),
                        'footer_action_text' => array(
                            'type'           => 'string',
                        ),
                        'footer_action_link' => array(
                            'type'           => 'string',
                        ),
                        'shortcode_atts'=> array(
                            'type'      => 'object',
                            'default'   => array(
                                'limit'         => 3,
                                'columns'       => 1,
                                'orderby'       => 'date',
                                'order'         => 'DESC',
                                'featured'      => false,
                                'top_rated'     => false,
                            ),
                        ),
                        'design_options'=> array(
                            'type'      => 'object',
                        ),
                        'className'     => array(
                            'type'      => 'string',
                        ),
                    ),
                    'editor_script'     => 'vodi-section-coming-soon-videos',
                    'editor_style'      => 'vodi-section-coming-soon-videos-editor',
                    'style'             => 'vodi-section-coming-soon-videos',
                    'render_callback'   => 'vodi_section_coming_soon_videos_element',
                ),
                'movies-list'    => array(
                    'attributes'        => array(
                        'section_title_1' => array(
                            'type'      => 'string',
                        ),
                        'section_title_2' => array(
                            'type'      => 'string',
                        ),
                        'section_nav_links_1'=> array(
                            'type'      => 'array',
                            'items'   => [
                                'type' => 'object',
                            ],
                        ),
                        'section_nav_links_2'=> array(
                            'type'      => 'array',
                            'items'   => [
                                'type' => 'object',
                            ],
                        ),
                        'featured_movie_id'=> array(
                            'type'      => 'string',
                        ),
                        'shortcode_atts_1'=> array(
                            'type'      => 'object',
                            'default'   => array(
                                'limit'         => 9,
                                'columns'       => 1,
                                'orderby'       => 'date',
                                'order'         => 'DESC',
                                'featured'      => false,
                                'top_rated'     => false,
                            ),
                        ),

                        'shortcode_atts_2'=> array(
                            'type'      => 'object',
                            'default'   => array(
                                'limit'         => 8,
                                'columns'       => 1,
                                'orderby'       => 'date',
                                'order'         => 'DESC',
                                'featured'      => false,
                                'top_rated'     => false,
                            ),
                        ),
                        'design_options'=> array(
                            'type'      => 'object',
                        ),
                        'className'     => array(
                            'type'      => 'string',
                        ),
                    ),
                    'editor_script'     => 'vodi-movies-list',
                    'editor_style'      => 'vodi-movies-list-editor',
                    'style'             => 'vodi-movies-list',
                    'render_callback'   => 'vodi_movies_list_element',
                ),
                'blog-grid-with-list-section'    => array(
                    'attributes'        => array(
                        'section_title' => array(
                            'type'      => 'string',
                        ),
                        'section_nav_links'=> array(
                            'type'      => 'array',
                            'items'   => [
                                'type' => 'object',
                            ],
                        ),
                        'hide_excerpt_1'  => array(
                            'type'      => 'boolean',
                            'default'   => true,
                        ),
                        'hide_excerpt_2'  => array(
                            'type'      => 'boolean',
                            'default'   => false,
                        ),
                        'ids'=> array(
                            'type'      => 'string',
                        ),
                        'post_atts_2'     => array(
                            'type'      => 'object',
                            'default'   => array(
                                'posts_per_page'=> 3,
                                'orderby'       => 'date',
                                'order'         => 'DESC',
                            ),
                        ),
                        'design_options'=> array(
                            'type'      => 'object',
                        ),
                        'className'     => array(
                            'type'      => 'string',
                        ),
                    ),
                    'editor_script'     => 'vodi-blog-grid-with-list-section',
                    'editor_style'      => 'vodi-blog-grid-with-list-section-editor',
                    'style'             => 'vodi-blog-grid-with-list-section',
                    'render_callback'   => 'vodi_blog_grid_with_list_section_element',
                ),
                'recent-comments'    => array(
                    'attributes'        => array(
                        'section_title' => array(
                            'type'      => 'string',
                        ),
                        'section_nav_links'=> array(
                            'type'      => 'array',
                            'items'   => [
                                'type' => 'object',
                            ],
                        ),
                        'limit'       => array(
                            'type'      => 'number',
                            'default'   => 5,
                        ),
                        'design_options'=> array(
                            'type'      => 'object',
                        ),
                        'className'     => array(
                            'type'      => 'string',
                        ),
                    ),
                    'editor_script'     => 'vodi-recent-comments',
                    'editor_style'      => 'vodi-recent-comments-editor',
                    'style'             => 'vodi-recent-comments',
                    'render_callback'   => 'vodi_recent_comments_element',
                ),
                'section-event-category-list'=> array(
                    'attributes'        => array(
                        'section_title' => array(
                            'type'      => 'string',
                        ),
                        'section_nav_links'=> array(
                            'type'      => 'array',
                            'items'   => [
                                'type' => 'object',
                            ],
                        ),
                        'columns'       => array(
                            'type'      => 'number',
                            'default'   => 4,
                        ),
                        'category_args' => array(
                            'type'      => 'object',
                            'default'   => array(
                                'number'    => 4,
                                'orderby'   => 'id',
                                'order'     => 'DESC',
                                'hide_empty'=> true,
                            ),
                        ),
                        'design_options'=> array(
                            'type'      => 'object',
                        ),
                        'className'     => array(
                            'type'      => 'string',
                        ),
                    ),
                    'editor_script'     => 'vodi-section-event-category-list',
                    'editor_style'      => 'vodi-section-event-category-list-editor',
                    'style'             => 'vodi-section-event-category-list',
                    'render_callback'   => 'vodi_section_event_category_list_element',
                ),
            );

            $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
            foreach ( $blocks as $block => $args ) {
                wp_register_script( $args['editor_script'], get_template_directory_uri() . '/assets/js/blocks/' . $block . $suffix . '.js', array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' ) );
                if( isset( $args['editor_style'] ) ) {
                    wp_register_style( $args['editor_style'], get_template_directory_uri() . '/assets/css/gutenberg-blocks/' . $block . '/editor' . $suffix . '.css', array( 'wp-edit-blocks' ), filemtime( get_template_directory() . '/assets/css/gutenberg-blocks/' . $block . '/editor' . $suffix . '.css' ) );
                }
                if( isset( $args['style'] ) ) {
                    wp_register_style( $args['style'], get_template_directory_uri() . '/assets/css/gutenberg-blocks/' . $block . '/style' . $suffix . '.css', array(), filemtime( get_template_directory() . '/assets/css/gutenberg-blocks/' . $block . '/style' . $suffix . '.css' ) );
                }
                register_block_type( 'vodi/' . $block, $args );
            }
            add_filter( 'block_categories', array( __CLASS__, 'block_categories' ), 10, 2 );
        }
    }
    public static function block_categories( $categories, $post ) {
        return array_merge(
            $categories,
            array(
                array(
                    'slug' => 'vodi-blocks',
                    'title' => esc_html__( 'Vodi Blocks', 'vodi' ),
                ),
            )
        );
    }
}
