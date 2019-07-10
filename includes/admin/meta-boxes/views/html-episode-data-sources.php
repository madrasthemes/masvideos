<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div id="episode_sources" class="panel masvideos-metaboxes-wrapper masvideos_options_panel hidden">
    <div class="toolbar toolbar-top">
        <span class="expand-close">
            <a href="#" class="expand_all"><?php esc_html_e( 'Expand', 'masvideos' ); ?></a> / <a href="#" class="close_all"><?php esc_html_e( 'Close', 'masvideos' ); ?></a>
        </span>
        <button type="button" class="button add_episode_source"><?php esc_html_e( 'Add Source', 'masvideos' ); ?></button>
    </div>
    <div class="episode_sources masvideos-metaboxes">
        <?php
        // Episode Sources
        $sources = $episode_object->get_sources( 'edit' );
        $i          = -1;

        if( is_array( $sources ) ) {
            foreach ( $sources as $source ) {
                $i++;
                $metabox_class = array();

                include 'html-episode-source.php';
            }
        }
        ?>
    </div>
    <div class="toolbar">
        <span class="expand-close">
            <a href="#" class="expand_all"><?php esc_html_e( 'Expand', 'masvideos' ); ?></a> / <a href="#" class="close_all"><?php esc_html_e( 'Close', 'masvideos' ); ?></a>
        </span>
        <button type="button" class="button save_sources_episode button-primary"><?php esc_html_e( 'Save sources', 'masvideos' ); ?></button>
    </div>
    <?php do_action( 'masvideos_episode_options_sources' ); ?>
</div>
