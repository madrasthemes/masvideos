<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div id="tv_show_crew_persons" class="panel masvideos-metaboxes-wrapper hidden">
    <div class="toolbar toolbar-top">
        <span class="expand-close">
            <a href="#" class="expand_all"><?php esc_html_e( 'Expand', 'masvideos' ); ?></a> / <a href="#" class="close_all"><?php esc_html_e( 'Close', 'masvideos' ); ?></a>
        </span>
        <select class="person_id masvideos-enhanced-search" style="width: 50%;" id="person_id" name="person_id" data-placeholder="<?php esc_attr_e( 'Search for a person&hellip;', 'masvideos' ); ?>" data-allow_clear="1" data-action="masvideos_json_search_persons" data-nonce_key="search_persons_nonce">
        </select>
        <button type="button" class="button add_person_tv_show_crew"><?php esc_html_e( 'Add', 'masvideos' ); ?></button>
    </div>
    <div class="tv_show_crew_persons masvideos-metaboxes">
        <?php
        // Movie crew - taxonomies and custom, ordered, with visibility.
        $crew = $tv_show_object->get_crew( 'edit' );
        $i          = -1;

        if( is_array( $crew ) ) {
            foreach ( $crew as $person ) {
                $i++;
                $metabox_class = array();

                include 'html-tv-show-crew-person.php';
            }
        }
        ?>
    </div>
    <div class="toolbar">
        <span class="expand-close">
            <a href="#" class="expand_all"><?php esc_html_e( 'Expand', 'masvideos' ); ?></a> / <a href="#" class="close_all"><?php esc_html_e( 'Close', 'masvideos' ); ?></a>
        </span>
        <button type="button" class="button save_persons_tv_show_crew button-primary"><?php esc_html_e( 'Save crew', 'masvideos' ); ?></button>
    </div>
    <?php do_action( 'masvideos_tv_show_options_tv_show_crew' ); ?>
</div>
