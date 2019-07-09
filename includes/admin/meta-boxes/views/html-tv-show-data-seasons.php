<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div id="tv_show_seasons" class="panel masvideos-metaboxes-wrapper masvideos_options_panel hidden">
    <div class="toolbar toolbar-top">
        <span class="expand-close">
            <a href="#" class="expand_all"><?php esc_html_e( 'Expand', 'masvideos' ); ?></a> / <a href="#" class="close_all"><?php esc_html_e( 'Close', 'masvideos' ); ?></a>
        </span>
        <button type="button" class="button add_tv_show_season"><?php esc_html_e( 'Add Season', 'masvideos' ); ?></button>
    </div>
    <div class="tv_show_seasons masvideos-metaboxes">
        <?php
        // TV Show Seasons
        $seasons = $tv_show_object->get_seasons( 'edit' );
        $i          = -1;

        if( is_array( $seasons ) ) {
            foreach ( $seasons as $season ) {
                $i++;
                $metabox_class = array();

                include 'html-tv-show-season.php';
            }
        }
        ?>
    </div>
    <div class="toolbar">
        <span class="expand-close">
            <a href="#" class="expand_all"><?php esc_html_e( 'Expand', 'masvideos' ); ?></a> / <a href="#" class="close_all"><?php esc_html_e( 'Close', 'masvideos' ); ?></a>
        </span>
        <button type="button" class="button save_seasons_tv_show button-primary"><?php esc_html_e( 'Save seasons', 'masvideos' ); ?></button>
    </div>
    <?php do_action( 'masvideos_tv_show_options_seasons' ); ?>
</div>
