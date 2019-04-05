<?php
/**
 * Linked movies options.
 *
 * @package MasVideos/admin
 */

defined( 'ABSPATH' ) || exit;
?>
<div id="linked_movie_data" class="panel masvideos_options_panel hidden">

    <div class="options_group">
        <p class="form-field">
            <label for="linked_recommended_movie_ids"><?php esc_html_e( 'Recommended Movies', 'masvideos' ); ?></label>
            <select class="multiselect movies masvideos-enhanced-search" multiple="multiple" style="width: 50%;" id="linked_recommended_movie_ids" name="recommended_movie_ids[]" data-placeholder="<?php esc_attr_e( 'Search for a recommended movie&hellip;', 'masvideos' ); ?>" data-action="masvideos_json_search_movies" data-sortable="true" data-exclude="<?php echo intval( $post->ID ); ?>" data-nonce_key="search_movies_nonce">
                <?php $movie_ids = $movie_object->get_recommended_movie_ids( 'edit' );

                foreach ( $movie_ids as $movie_id ) {
                    $movie = masvideos_get_movie( $movie_id );
                    if ( is_object( $movie ) ) {
                        echo '<option value="' . esc_attr( $movie_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $movie->get_name() ) . '</option>';
                    }
                }
                ?>
            </select> <?php echo masvideos_help_tip( __( 'Linked Movies are movies which you recommend instead of the currently viewed movie, for example, movies that are better quality.', 'masvideos' ) ); // WPCS: XSS ok. ?>
        </p>
    </div>

    <?php do_action( 'masvideos_movie_options_related' ); ?>
</div>
