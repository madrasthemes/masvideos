<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div id="movie_persons" class="panel masvideos-metaboxes-wrapper hidden">
    <div class="toolbar toolbar-top">
        <span class="expand-close">
            <a href="#" class="expand_all"><?php esc_html_e( 'Expand', 'masvideos' ); ?></a> / <a href="#" class="close_all"><?php esc_html_e( 'Close', 'masvideos' ); ?></a>
        </span>
        <select class="person_id masvideos-enhanced-search" style="width: 50%;" id="person_id" name="person_id" data-placeholder="<?php esc_attr_e( 'Search for a person&hellip;', 'masvideos' ); ?>" data-allow_clear="1" data-action="masvideos_json_search_persons" data-nonce_key="search_persons_nonce">
        </select>
        <button type="button" class="button add_person_movie"><?php esc_html_e( 'Add', 'masvideos' ); ?></button>
    </div>
    <div class="movie_persons masvideos-metaboxes">
        <?php
        // Movie persons - taxonomies and custom, ordered, with visibility.
        $persons = $movie_object->get_persons( 'edit' );
        $i          = -1;

        foreach ( $persons as $person ) {
            $i++;
            $metabox_class = array();

            include 'html-movie-person.php';
        }
        ?>
    </div>
    <div class="toolbar">
        <span class="expand-close">
            <a href="#" class="expand_all"><?php esc_html_e( 'Expand', 'masvideos' ); ?></a> / <a href="#" class="close_all"><?php esc_html_e( 'Close', 'masvideos' ); ?></a>
        </span>
        <button type="button" class="button save_persons_movie button-primary"><?php esc_html_e( 'Save persons', 'masvideos' ); ?></button>
    </div>
    <?php do_action( 'masvideos_movie_options_persons' ); ?>
</div>
