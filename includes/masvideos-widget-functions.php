<?php
/**
 * MasVideos Widget Functions
 *
 * Widget related functions and widget registration.
 *
 * @package MasVideos/Functions
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Include widget classes.
require MASVIDEOS_ABSPATH . 'includes/abstracts/abstract-masvideos-widget.php';
require MASVIDEOS_ABSPATH . 'includes/widgets/class-masvideos-widget-search.php';
require MASVIDEOS_ABSPATH . 'includes/widgets/class-masvideos-widget-movies-layered-nav.php';
require MASVIDEOS_ABSPATH . 'includes/widgets/class-masvideos-widget-movies-genres.php';
require MASVIDEOS_ABSPATH . 'includes/widgets/class-masvideos-widget-movies-rating-filter.php';
require MASVIDEOS_ABSPATH . 'includes/widgets/class-masvideos-widget-movies-year-filter.php';
require MASVIDEOS_ABSPATH . 'includes/widgets/class-masvideos-movies-genres-filter-widgets.php';
require MASVIDEOS_ABSPATH . 'includes/widgets/class-masvideos-movies-widget.php';
require MASVIDEOS_ABSPATH . 'includes/widgets/class-masvideos-widget-tv-shows-rating-filter.php';
require MASVIDEOS_ABSPATH . 'includes/widgets/class-masvideos-widget-tv-shows-genres.php';
require MASVIDEOS_ABSPATH . 'includes/widgets/class-masvideos-widget-tv-shows-layered-nav.php';
require MASVIDEOS_ABSPATH . 'includes/widgets/class-masvideos-tv-shows-genres-filter-widgets.php';
require MASVIDEOS_ABSPATH . 'includes/widgets/class-masvideos-tv-shows-widget.php';
require MASVIDEOS_ABSPATH . 'includes/widgets/class-masvideos-widget-videos-rating-filter.php';
require MASVIDEOS_ABSPATH . 'includes/widgets/class-masvideos-videos-categories-filter-widgets.php';
require MASVIDEOS_ABSPATH . 'includes/widgets/class-masvideos-widget-videos-categories.php';
require MASVIDEOS_ABSPATH . 'includes/widgets/class-masvideos-widget-videos-layered-nav.php';
require MASVIDEOS_ABSPATH . 'includes/widgets/class-masvideos-videos-widget.php';
require MASVIDEOS_ABSPATH . 'includes/widgets/class-masvideos-movies-tags-filter-widgets.php';
require MASVIDEOS_ABSPATH . 'includes/widgets/class-masvideos-tv-shows-tags-filter-widgets.php';
require MASVIDEOS_ABSPATH . 'includes/widgets/class-masvideos-videos-tags-filter-widgets.php';

/**
 * Register Widgets.
 *
 * @since 1.0.0
 */
function masvideos_register_widgets() {
    register_widget( 'MasVideos_Widget_Search' );
    register_widget( 'MasVideos_Widget_Movies_Layered_Nav' );
    register_widget( 'MasVideos_Widget_Movies_Genres' );
    register_widget( 'MasVideos_Widget_Movies_Rating_Filter' );
    register_widget( 'MasVideos_Widget_Movies_Year_Filter' );
    register_widget( 'MasVideos_Movies_Widget' );
    register_widget( 'MasVideos_Movies_Genres_Filter_Widget' );
    register_widget( 'MasVideos_Widget_TV_Shows_Rating_Filter' );
    register_widget( 'MasVideos_Widget_TV_Shows_Genres' );
    register_widget( 'MasVideos_Widget_TV_Shows_Layered_Nav' );
    register_widget( 'MasVideos_TV_Shows_Widget' );
    register_widget( 'MasVideos_Widget_Videos_Rating_Filter' );
    register_widget( 'MasVideos_Widget_Videos_Categories' );
    register_widget( 'MasVideos_Widget_Videos_Layered_Nav' );
    register_widget( 'MasVideos_Videos_Widget' );
    register_widget( 'MasVideos_Videos_Categories_Filter_Widget' );
    register_widget( 'MasVideos_TV_Shows_Genres_Filter_Widget' );
    register_widget( 'MasVideos_Movies_Tags_Filter_Widget' );
    register_widget( 'MasVideos_TV_Shows_Tags_Filter_Widget' );
    register_widget( 'MasVideos_Videos_Tags_Filter_Widget' );
}
add_action( 'widgets_init', 'masvideos_register_widgets' );
