<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div id="movie_cast_persons" class="panel masvideos-metaboxes-wrapper hidden">
    <div class="toolbar toolbar-top">
        <span class="expand-close">
            <a href="#" class="expand_all"><?php esc_html_e( 'Expand', 'masvideos' ); ?></a> / <a href="#" class="close_all"><?php esc_html_e( 'Close', 'masvideos' ); ?></a>
        </span>
        <select class="person_id masvideos-enhanced-search" style="width: 50%;" id="person_id" name="person_id" data-placeholder="<?php esc_attr_e( 'Search for a person&hellip;', 'masvideos' ); ?>" data-allow_clear="1" data-action="masvideos_json_search_persons" data-nonce_key="search_persons_nonce">
        </select>
        <button type="button" class="button add_person_movie_cast"><?php esc_html_e( 'Add', 'masvideos' ); ?></button>
    </div>
    <div class="movie_cast_persons masvideos-metaboxes">
        <?php
        // Movie cast - taxonomies and custom, ordered, with visibility.
        $cast = $movie_object->get_cast( 'edit' );
        $i          = -1;

        if( is_array( $cast ) ) {
            foreach ( $cast as $person ) {
                $i++;
                $metabox_class = array();

                include 'html-movie-cast-person.php';
            }
        }
        ?>
    </div>
    <div class="toolbar">
        <span class="expand-close">
            <a href="#" class="expand_all"><?php esc_html_e( 'Expand', 'masvideos' ); ?></a> / <a href="#" class="close_all"><?php esc_html_e( 'Close', 'masvideos' ); ?></a>
        </span>
        <button type="button" class="button save_persons_movie_cast button-primary"><?php esc_html_e( 'Save cast', 'masvideos' ); ?></button>
    </div>
    <?php do_action( 'masvideos_movie_options_movie_cast' ); ?>
</div>
