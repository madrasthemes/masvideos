<?php
/**
 * Template tags used in Page
 */

if ( ! function_exists( 'vodi_container_start' ) ) {
    function vodi_container_start() {
        ?><div class="container"><?php
    }
}

if ( ! function_exists( 'vodi_container_end' ) ) {
    function vodi_container_end() {
        ?></div><!-- /.container --><?php
    }
}

if ( ! function_exists( 'vodi_page_header' ) ) {
    /**
     * Display the page header
     *
     * @since 1.0.0
     */
    function vodi_page_header() {
        
        if ( is_page() ) : ?>
        
        <header class="page__header">
            <div class="container">
            <?php
                //vodi_post_thumbnail( 'full' );
                the_title( '<h1 class="page__title">', '</h1>' );
            ?>
            </div>
        </header><!-- .entry-header -->
        
        <?php endif;
    }
}

if ( ! function_exists( 'vodi_page_content' ) ) {
    /**
     * Display the post content
     *
     * @since 1.0.0
     */
    function vodi_page_content() {
        ?>
        <div class="page__content">
            <?php the_content(); ?>
            <?php
                wp_link_pages( array(
                    'before'      => '<div class="page-links">' . __( 'Pages:', 'vodi' ) . '<div class="page-links-inner">',
                    'after'       => '</div></div>',
                    'link_before' => '<span class="page-link">',
                    'link_after'  => '</span>'
                ) );
            ?>
        </div><!-- .entry-content -->
        <?php
    }
}

if ( ! function_exists( 'vodi_bg_style' ) ) {
    function vodi_bg_style() {
        $bg_class = apply_filters( 'vodi_bg_style_class', '' );

        return $bg_class;
    }
}