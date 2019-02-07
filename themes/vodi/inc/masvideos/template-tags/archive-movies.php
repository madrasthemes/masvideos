<?php
if ( ! function_exists( 'vodi_movies_control_bar_top' ) ) {
    /**
     * movies top control bar.
     */
    function vodi_movies_control_bar_top() {
        do_action( 'vodi_movies_control_bar_top' );
    }
}

if ( ! function_exists( 'vodi_movies_control_bar_top_open' ) ) {
    /**
     * Display top control bar open
     */
    function vodi_movies_control_bar_top_open() {
        echo '<div class="vodi-control-bar">';
    }
}

if ( ! function_exists( 'vodi_archive_wrapper_open' ) ) {
    /**
     * Vodi Archive Wrapper Open
     */
    function vodi_archive_wrapper_open() {
        echo '<div class="vodi-archive-wrapper" data-view="grid">';
    }
}

if ( ! function_exists( 'vodi_archive_wrapper_close' ) ) {
    /**
     * Vodi Archive Wrapper Close
     */
    function vodi_archive_wrapper_close() {
        echo '</div>';
    }
}

if ( ! function_exists( 'vodi_movies_control_bar_bottom' ) ) {
    /**
     * movies bottom control bar.
     */
    function vodi_movies_control_bar_bottom() {
        do_action( 'vodi_movies_control_bar_bottom' );
    }
}

if ( ! function_exists( 'vodi_movies_control_bar_bottom_open' ) ) {
    /**
     * Display Bottom Control Bar open
     */
    function vodi_movies_control_bar_bottom_open() {
        echo '<div class="page-control-bar-bottom">';
    }
}

if ( ! function_exists( 'vodi_movies_control_bar_bottom_close' ) ) {
    /**
     * Display Bottom Control Bar Close
     */
    function vodi_movies_control_bar_bottom_close() {
        echo '</div>';
    }
}

if ( ! function_exists( 'vodi_movies_control_bar_top_left' ) ) {
    /**
     * Display top control bar left
     */
    function vodi_movies_control_bar_top_left() {
        $archive_views = vodi_get_archive_views();
        ?>
        <div class="vodi-control-bar__left">
            <ul class="archive-view-switcher nav nav-tabs">
                <?php foreach( $archive_views as $archive_view_id => $archive_view ) : ?>
                    <li class="nav-item"><a id="vodi-archive-view-switcher-<?php echo esc_attr( $archive_view_id );?>" class="nav-link <?php $active_class = $archive_view[ 'active' ] ? 'active': ''; echo esc_attr( $active_class ); ?>" data-toggle="tab" data-archive-class="<?php echo esc_attr( $archive_view_id );?>" title="<?php echo esc_attr( $archive_view[ 'label' ] ); ?>" href="#vodi-archive-view-content"><?php vodi_get_template( $archive_view[ 'svg' ] ); ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php
    }
}

if ( ! function_exists( 'vodi_movies_control_bar_top_right' ) ) {
    /**
     * Display top control bar right
     */
    function vodi_movies_control_bar_top_right() {
        echo '<div class="vodi-control-bar__right">';
            masviseos_movies_per_page();
            masvideos_movies_catalog_ordering();
        echo '</div>';
    }
}

if ( ! function_exists( 'vodi_movies_control_bar_top_close' ) ) {
    /**
     * Display top control bar close
     */
    function vodi_movies_control_bar_top_close() {
        echo '</div>';
    }
}

if ( ! function_exists( 'vodi_get_archive_views' ) ) {
    /**
     * Archive views
     */
    function vodi_get_archive_views() {
        return $archive_views = apply_filters( 'vodi_get_archive_views_args', array(
            'grid'              => array(
                'label'         => esc_html__( 'Grid View', 'vodi' ),
                'svg'          => 'templates/svg/grid-small-icon.svg',
                'enabled'       => true,
                'active'        => true,
            ),
            'grid-extended'          => array(
                'label'         => esc_html__( 'Grid View', 'vodi' ),
                'svg'          => 'templates/svg/grid-icon.svg',
                'enabled'       => true,
                'active'        => false,
            ),
            'list-large'          => array(
                'label'         => esc_html__( 'List Large View', 'vodi' ),
                'svg'          => 'templates/svg/listing-large-icon.svg',
                'enabled'       => true,
                'active'        => false,

            ),
            'list-small'          => array(
                'label'         => esc_html__( 'List View', 'vodi' ),
                'svg'          => 'templates/svg/listing-icon.svg',
                'enabled'       => true,
                'active'        => false,

            ),
            'list'          => array(
                'label'         => esc_html__( 'List Small View', 'vodi' ),
                'svg'          => 'templates/svg/listing-small.svg',
                'enabled'       => true,
                'active'        => false,

            ),
        ) );
    }
}

if ( ! function_exists( 'vodi_movies_get_sidebar' ) ) {
    function vodi_movies_get_sidebar() {
        get_sidebar( 'movie' );
    }
}

if ( ! function_exists( 'vodi_movies_loop_title' ) ) {
    /**
     * Outputs Mas Movies Title
     */
    function vodi_movies_loop_title() {

        if ( apply_filters( 'movies_show_loop_title', true ) ) {
            ?>
            <header class="page-header">
                <h1 class="page-title"><?php masvideos_movie_page_title(); ?></h1>
            </header>
            <?php
        }
    }
}

if ( ! function_exists( 'masvideos_vodi_movies_pagination_args' ) ) {
    function masvideos_vodi_movies_pagination_args( $args ) {
        $args['next_text'] = esc_html__( 'Next Page &nbsp;&nbsp;&nbsp;&rarr;', 'Next page', 'vodi' );
        $args['prev_text'] = esc_html__( '&larr;&nbsp;&nbsp;&nbsp; Previous Page', 'Previous page', 'vodi' );

        return $args;
    }
}

