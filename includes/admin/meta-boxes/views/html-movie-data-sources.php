<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div id="movie_sources" class="panel masvideos-metaboxes-wrapper masvideos_options_panel hidden">
    <div class="toolbar toolbar-top">
        <span class="expand-close">
            <a href="#" class="expand_all"><?php esc_html_e( 'Expand', 'masvideos' ); ?></a> / <a href="#" class="close_all"><?php esc_html_e( 'Close', 'masvideos' ); ?></a>
        </span>
        <button type="button" class="button add_movie_source"><?php esc_html_e( 'Add Source', 'masvideos' ); ?></button>
    </div>
    <div class="movie_sources masvideos-metaboxes">
        <?php
        // Movie Sources
        $sources = $movie_object->get_sources( 'edit' );
        $i          = -1;

        if( is_array( $sources ) ) {
            foreach ( $sources as $source ) {
                $i++;
                $metabox_class = array();

                include 'html-movie-source.php';
            }
        }
        ?>
    </div>
    <div class="toolbar">
        <span class="expand-close">
            <a href="#" class="expand_all"><?php esc_html_e( 'Expand', 'masvideos' ); ?></a> / <a href="#" class="close_all"><?php esc_html_e( 'Close', 'masvideos' ); ?></a>
        </span>
        <button type="button" class="button save_sources_movie button-primary"><?php esc_html_e( 'Save sources', 'masvideos' ); ?></button>
    </div>
    <?php do_action( 'masvideos_movie_options_sources' ); ?>
</div>
