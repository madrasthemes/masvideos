<?php
/**
 * Linked videos options.
 *
 * @package MasVideos/admin
 */

defined( 'ABSPATH' ) || exit;
?>
<div id="linked_video_data" class="panel masvideos_options_panel hidden">

    <div class="options_group">
        <p class="form-field">
            <label for="linked_related_video_ids"><?php esc_html_e( 'Related Videos', 'masvideos' ); ?></label>
            <select class="multiselect videos masvideos-enhanced-search" multiple="multiple" style="width: 50%;" id="linked_related_video_ids" name="related_video_ids[]" data-placeholder="<?php esc_attr_e( 'Search for a related video&hellip;', 'masvideos' ); ?>" data-action="masvideos_json_search_videos" data-sortable="true" data-exclude="<?php echo intval( $post->ID ); ?>" data-nonce_key="search_videos_nonce">
                <?php $video_ids = $movie_object->get_related_video_ids( 'edit' );

                foreach ( $video_ids as $video_id ) {
                    $video = masvideos_get_video( $video_id );
                    if ( is_object( $video ) ) {
                        echo '<option value="' . esc_attr( $video_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $video->get_name() ) . '</option>';
                    }
                }
                ?>
            </select> <?php echo masvideos_help_tip( __( 'Linked Videos are videos that related to this movie.', 'masvideos' ) ); // WPCS: XSS ok. ?>
        </p>
    </div>

    <?php do_action( 'masvideos_movie_options_related_video' ); ?>
</div>
