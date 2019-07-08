<?php
/**
 * Handles taxonomies in admin
 *
 * @class    MasVideos_Admin_Taxonomies
 * @version  1.0.0
 * @package  MasVideos/Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * MasVideos_Admin_Taxonomies class.
 */
class MasVideos_Admin_Taxonomies {

    /**
     * Constructor.
     */
    public function __construct() {

        // Enqueue scripts
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

        // Add form for movie genre
        add_action( 'movie_genre_add_form_fields', array( $this, 'add_movie_genre_fields' ) );
        add_action( 'movie_genre_edit_form_fields', array( $this, 'edit_movie_genre_fields' ), 10 );
        add_action( 'created_term', array( $this, 'save_movie_genre_fields' ), 10, 3 );
        add_action( 'edit_term', array( $this, 'save_movie_genre_fields' ), 10, 3 );

        // Add columns for movie genre
        add_filter( 'manage_edit-movie_genre_columns', array( $this, 'movie_genre_columns' ) );
        add_filter( 'manage_movie_genre_custom_column', array( $this, 'movie_genre_column' ), 10, 3 );

        // Add form for video cat
        add_action( 'video_cat_add_form_fields', array( $this, 'add_video_cat_fields' ) );
        add_action( 'video_cat_edit_form_fields', array( $this, 'edit_video_cat_fields' ), 10 );
        add_action( 'created_term', array( $this, 'save_video_cat_fields' ), 10, 3 );
        add_action( 'edit_term', array( $this, 'save_video_cat_fields' ), 10, 3 );

        // Add columns for video cat
        add_filter( 'manage_edit-video_cat_columns', array( $this, 'video_cat_columns' ) );
        add_filter( 'manage_video_cat_custom_column', array( $this, 'video_cat_column' ), 10, 3 );


        $movies_attribute_taxonomies = masvideos_get_attribute_taxonomies( 'movie' );

        if ( ! empty( $movies_attribute_taxonomies ) ) {
            foreach ( $movies_attribute_taxonomies as $movies_attribute_taxonomie ) {
                add_action( $movies_attribute_taxonomie->post_type . '_' . $movies_attribute_taxonomie->attribute_name . '_pre_add_form', array( $this, 'movie_attribute_description' ) );
            }
        }

        $videos_attribute_taxonomies = masvideos_get_attribute_taxonomies( 'video' );

        if ( ! empty( $videos_attribute_taxonomies ) ) {
            foreach ( $videos_attribute_taxonomies as $videos_attribute_taxonomie ) {
                add_action( $videos_attribute_taxonomie->post_type . '_' . $videos_attribute_taxonomie->attribute_name . '_pre_add_form', array( $this, 'video_attribute_description' ) );
            }
        }
    }

    public function admin_scripts() {
        wp_enqueue_media();
        wp_enqueue_script( 'masvideos-admin-meta-boxes' );
    }

    /**
     * Description for movies attribute to aid users.
     */
    public function movie_attribute_description() {
        echo wpautop( __( 'Attribute terms can be assigned to movies and variations.<br/><br/><b>Note</b>: Deleting a term will remove it from all movies and variations to which it has been assigned. Recreating a term will not automatically assign it back to movies.', 'masvideos' ) );
    }

    /**
     * Description for videos attribute to aid users.
     */
    public function video_attribute_description() {
        echo wpautop( __( 'Attribute terms can be assigned to mvideoes and variations.<br/><br/><b>Note</b>: Deleting a term will remove it from all videos and variations to which it has been assigned. Recreating a term will not automatically assign it back to videos.', 'masvideos' ) );
    }

    /**
     * Movie Genre thumbnail fields.
     */
    public function add_movie_genre_fields() {
        ?>
        <div id="movie_genre_thumbnail_field" class="form-field term-thumbnail-wrap">
            <label><?php _e( 'Thumbnail', 'masvideos' ); ?></label>
            <img src="<?php echo esc_url( masvideos_placeholder_img_src() ); ?>" width="60px" height="60px" style="margin-right: 10px;" class="upload_image_preview" />
            <input type="hidden" id="movie_genre_thumbnail_id" class="upload_image_id" name="movie_genre_thumbnail_id"/>
            <a href="#" class="button masvideos_upload_image_button tips"><?php _e( 'Upload/Add image', 'masvideos' ); ?></a>
            <a href="#" class="button masvideos_remove_image_button tips"><?php _e( 'Remove image', 'masvideos' ); ?></a>
            <div class="clear"></div>
        </div>
        <?php
    }

    /**
     * Edit movie genre thumbnail field.
     *
     * @param mixed $term Term (movie genre) being edited
     */
    public function edit_movie_genre_fields( $term ) {

        $thumbnail_id = absint( get_term_meta( $term->term_id, 'thumbnail_id', true ) );

        if ( $thumbnail_id ) {
            $image = wp_get_attachment_thumb_url( $thumbnail_id );
        } else {
            $image = masvideos_placeholder_img_src();
        }
        ?>
        <tr class="term-thumbnail-wrap">
            <th scope="row" valign="top"><label><?php _e( 'Thumbnail', 'masvideos' ); ?></label></th>
            <td id="movie_genre_thumbnail_field" class="form-field">
                <img src="<?php echo esc_url( $image ); ?>" width="60px" height="60px" class="upload_image_preview" /></div>
                <input type="hidden" class="upload_image_id" name="movie_genre_thumbnail_id" value="<?php echo esc_attr( $thumbnail_id ); ?>" />
                <a href="#" class="button masvideos_upload_image_button tips"><?php _e( 'Upload/Add image', 'masvideos' ); ?></a>
                <a href="#" class="button masvideos_remove_image_button tips"><?php _e( 'Remove image', 'masvideos' ); ?></a>
                <div class="clear"></div>
            </td>
        </tr>
        <?php
    }

    /**
     * save_movie_genre_fields function.
     *
     * @param mixed  $term_id Term ID being saved
     * @param mixed  $tt_id
     * @param string $taxonomy
     */
    public function save_movie_genre_fields( $term_id, $tt_id = '', $taxonomy = '' ) {
        if ( isset( $_POST['movie_genre_thumbnail_id'] ) && 'movie_genre' === $taxonomy ) {
            update_term_meta( $term_id, 'thumbnail_id', absint( $_POST['movie_genre_thumbnail_id'] ) );
        }
    }

    /**
     * Thumbnail column added to movie genre admin.
     *
     * @param mixed $columns
     * @return array
     */
    public function movie_genre_columns( $columns ) {
        $new_columns = array();

        if ( isset( $columns['cb'] ) ) {
            $new_columns['cb'] = $columns['cb'];
            unset( $columns['cb'] );
        }

        $new_columns['thumb'] = __( 'Image', 'masvideos' );

        $columns = array_merge( $new_columns, $columns );

        return $columns;
    }

    /**
     * Thumbnail column value added to movie genre admin.
     *
     * @param string $columns
     * @param string $column
     * @param int    $id
     *
     * @return string
     */
    public function movie_genre_column( $columns, $column, $id ) {
        if ( 'thumb' === $column ) {

            $thumbnail_id = get_term_meta( $id, 'thumbnail_id', true );

            if ( $thumbnail_id ) {
                $image = wp_get_attachment_thumb_url( $thumbnail_id );
            } else {
                $image = masvideos_placeholder_img_src();
            }

            // Prevent esc_url from breaking spaces in urls for image embeds. Ref: https://core.trac.wordpress.org/ticket/23605
            $image    = str_replace( ' ', '%20', $image );
            $columns .= '<img src="' . esc_url( $image ) . '" alt="' . esc_attr__( 'Thumbnail', 'masvideos' ) . '" class="wp-post-image" height="48" width="48" />';
        }
        return $columns;
    }

    /**
     * Video Category thumbnail fields.
     */
    public function add_video_cat_fields() {
        ?>
        <div id="video_cat_thumbnail_field" class="form-field term-thumbnail-wrap">
            <label><?php _e( 'Thumbnail', 'masvideos' ); ?></label>
            <img src="<?php echo esc_url( masvideos_placeholder_img_src() ); ?>" width="60px" height="60px" style="margin-right: 10px;" class="upload_image_preview" />
            <input type="hidden" id="video_cat_thumbnail_id" class="upload_image_id" name="video_cat_thumbnail_id"/>
            <a href="#" class="button masvideos_upload_image_button tips"><?php _e( 'Upload/Add image', 'masvideos' ); ?></a>
            <a href="#" class="button masvideos_remove_image_button tips"><?php _e( 'Remove image', 'masvideos' ); ?></a>
            <div class="clear"></div>
        </div>
        <?php
    }

    /**
     * Edit video category thumbnail field.
     *
     * @param mixed $term Term (video category) being edited
     */
    public function edit_video_cat_fields( $term ) {

        $thumbnail_id = absint( get_term_meta( $term->term_id, 'thumbnail_id', true ) );

        if ( $thumbnail_id ) {
            $image = wp_get_attachment_thumb_url( $thumbnail_id );
        } else {
            $image = masvideos_placeholder_img_src();
        }
        ?>
        <tr class="term-thumbnail-wrap">
            <th scope="row" valign="top"><label><?php _e( 'Thumbnail', 'masvideos' ); ?></label></th>
            <td id="video_cat_thumbnail_field" class="form-field">
                <img src="<?php echo esc_url( $image ); ?>" width="60px" height="60px" class="upload_image_preview" /></div>
                <input type="hidden" class="upload_image_id" name="video_cat_thumbnail_id" value="<?php echo esc_attr( $thumbnail_id ); ?>" />
                <a href="#" class="button masvideos_upload_image_button tips"><?php _e( 'Upload/Add image', 'masvideos' ); ?></a>
                <a href="#" class="button masvideos_remove_image_button tips"><?php _e( 'Remove image', 'masvideos' ); ?></a>
                <div class="clear"></div>
            </td>
        </tr>
        <?php
    }

    /**
     * save_video_cat_fields function.
     *
     * @param mixed  $term_id Term ID being saved
     * @param mixed  $tt_id
     * @param string $taxonomy
     */
    public function save_video_cat_fields( $term_id, $tt_id = '', $taxonomy = '' ) {
        if ( isset( $_POST['video_cat_thumbnail_id'] ) && 'video_cat' === $taxonomy ) {
            update_term_meta( $term_id, 'thumbnail_id', absint( $_POST['video_cat_thumbnail_id'] ) );
        }
    }

    /**
     * Thumbnail column added to video category admin.
     *
     * @param mixed $columns
     * @return array
     */
    public function video_cat_columns( $columns ) {
        $new_columns = array();

        if ( isset( $columns['cb'] ) ) {
            $new_columns['cb'] = $columns['cb'];
            unset( $columns['cb'] );
        }

        $new_columns['thumb'] = __( 'Image', 'masvideos' );

        $columns = array_merge( $new_columns, $columns );

        return $columns;
    }

    /**
     * Thumbnail column value added to movie genre admin.
     *
     * @param string $columns
     * @param string $column
     * @param int    $id
     *
     * @return string
     */
    public function video_cat_column( $columns, $column, $id ) {
        if ( 'thumb' === $column ) {

            $thumbnail_id = get_term_meta( $id, 'thumbnail_id', true );

            if ( $thumbnail_id ) {
                $image = wp_get_attachment_thumb_url( $thumbnail_id );
            } else {
                $image = masvideos_placeholder_img_src();
            }

            // Prevent esc_url from breaking spaces in urls for image embeds. Ref: https://core.trac.wordpress.org/ticket/23605
            $image    = str_replace( ' ', '%20', $image );
            $columns .= '<img src="' . esc_url( $image ) . '" alt="' . esc_attr__( 'Thumbnail', 'masvideos' ) . '" class="wp-post-image" height="48" width="48" />';
        }
        return $columns;
    }
}

new MasVideos_Admin_Taxonomies();
