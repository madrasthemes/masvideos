<?php
/**
 * Post Types Admin
 *
 * @category Admin
 * @package  MasVideos/admin
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( class_exists( 'MasVideos_Admin_Post_Types', false ) ) {
    new MasVideos_Admin_Post_Types();
    return;
}

/**
 * MasVideos_Admin_Post_Types Class.
 *
 * Handles the edit posts views and some functionality on the edit post screen for WC post types.
 */
class MasVideos_Admin_Post_Types {

    /**
     * Constructor.
     */
    public function __construct() {
        include_once dirname( __FILE__ ) . '/class-masvideos-admin-meta-boxes.php';

        add_action( 'post_submitbox_misc_actions', array( $this, 'data_visibility' ) );
    }

    /**
	 * Output video/movie visibility options.
	 */
	public function data_visibility() {
		global $post, $thepostid, $video_object, $movie_object;

        if ( 'video' == $post->post_type ) {
            $thepostid          = $post->ID;
            $video_object       = $thepostid ? masvideos_get_video( $thepostid ) : new MasVideos_Video();
            $current_visibility = $video_object->get_catalog_visibility();
            $current_featured   = masvideos_bool_to_string( $video_object->get_featured() );
            $visibility_options = masvideos_get_video_visibility_options();
        } elseif ( 'movie' == $post->post_type ) {
            $thepostid          = $post->ID;
            $movie_object       = $thepostid ? masvideos_get_movie( $thepostid ) : new MasVideos_Movie();
            $current_visibility = $movie_object->get_catalog_visibility();
            $current_featured   = masvideos_bool_to_string( $movie_object->get_featured() );
            $visibility_options = masvideos_get_movie_visibility_options();
        } else {
            return;
        }
		?>
		<div class="misc-pub-section" id="catalog-visibility">
			<?php esc_html_e( 'Catalog visibility:', 'masvideos' ); ?>
			<strong id="catalog-visibility-display">
				<?php

				echo isset( $visibility_options[ $current_visibility ] ) ? esc_html( $visibility_options[ $current_visibility ] ) : esc_html( $current_visibility );

				if ( 'yes' === $current_featured ) {
					echo ', ' . esc_html__( 'Featured', 'masvideos' );
				}
				?>
			</strong>

			<a href="#catalog-visibility" class="edit-catalog-visibility hide-if-no-js"><?php esc_html_e( 'Edit', 'masvideos' ); ?></a>

			<div id="catalog-visibility-select" class="hide-if-js">

				<input type="hidden" name="current_visibility" id="current_visibility" value="<?php echo esc_attr( $current_visibility ); ?>" />
				<input type="hidden" name="current_featured" id="current_featured" value="<?php echo esc_attr( $current_featured ); ?>" />

				<?php
				echo '<p>' . esc_html__( 'This setting determines which catalog pages posts will be listed on.', 'masvideos' ) . '</p>';

				foreach ( $visibility_options as $name => $label ) {
					echo '<input type="radio" name="_visibility" id="_visibility_' . esc_attr( $name ) . '" value="' . esc_attr( $name ) . '" ' . checked( $current_visibility, $name, false ) . ' data-label="' . esc_attr( $label ) . '" /> <label for="_visibility_' . esc_attr( $name ) . '" class="selectit">' . esc_html( $label ) . '</label><br />';
				}

				echo '<br /><input type="checkbox" name="_featured" id="_featured" ' . checked( $current_featured, 'yes', false ) . ' /> <label for="_featured">' . esc_html__( 'This is a featured post', 'masvideos' ) . '</label><br />';
				?>
				<p>
					<a href="#catalog-visibility" class="save-post-visibility hide-if-no-js button"><?php esc_html_e( 'OK', 'masvideos' ); ?></a>
					<a href="#catalog-visibility" class="cancel-post-visibility hide-if-no-js"><?php esc_html_e( 'Cancel', 'masvideos' ); ?></a>
				</p>
			</div>
		</div>
		<?php
	}
}

new MasVideos_Admin_Post_Types();
