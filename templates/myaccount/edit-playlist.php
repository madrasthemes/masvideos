<?php
/**
 * Playlist Form
 *
 * This template can be overridden by copying it to yourtheme/masvideos/myaccount/edit-playlist.php.
 *
 * HOWEVER, on occasion MasVideos will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @package MasVideos/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if( ! isset( $post_type ) ) {
    return;
}

$create_section_title   = ! empty( $create_section_title ) ? $create_section_title : esc_html__( 'Create Playlist', 'masvideos' );
$update_section_title   = ! empty( $update_section_title ) ? $update_section_title : esc_html__( 'Update Playlist', 'masvideos' );
$section_title          = $create_section_title;

$create_button_text     = ! empty( $create_button_text ) ? $create_button_text : esc_html__( 'Create', 'masvideos' );
$update_button_text     = ! empty( $update_button_text ) ? $update_button_text : esc_html__( 'Update', 'masvideos' );
$button_text            = $create_button_text;

$id             = 0;
$title          = '';
$visibility     = '';

if( ! empty( $playlist ) ) {
    $id             = $playlist->get_id();
    $title          = $playlist->get_name();
    $visibility     = $playlist->get_status();
    $section_title  = $update_section_title;
    $button_text    = $update_button_text;
}

do_action( 'masvideos_before_edit_playlist_form', $playlist ); ?>

<div class="masvideos-edit-playlist">
    <div class="masvideos-edit-playlist__inner">

        <h2><?php echo esc_html( $section_title ); ?></h2>

        <form method="post" class="masvideos-form masvideos-edit-playlist__form masvideos-edit-playlist__form--<?php echo esc_attr( $post_type ); ?>" <?php do_action( 'masvideos_edit_playlist_form_tag', $playlist ); ?> >

            <?php do_action( 'masvideos_edit_playlist_form_start', $playlist ); ?>

            <p class="masvideos-form-row masvideos-form-row--wide form-row form-row-wide">
                <label for="<?php echo esc_attr( $post_type ); ?>-title"><?php esc_html_e( 'Title', 'masvideos' ); ?>&nbsp;<span class="required">*</span></label>
                <input type="text" class="masvideos-Input masvideos-Input--text input-text" name="title" id="<?php echo esc_attr( $post_type ); ?>-title" value="<?php echo esc_attr( $title ); ?>" /><?php // @codingStandardsIgnoreLine ?>
            </p>

            <p class="masvideos-form-row masvideos-form-row--wide form-row form-row-wide">
                <label for="<?php echo esc_attr( $post_type ); ?>-visibility"><?php esc_html_e( 'Visibility', 'masvideos' ); ?>&nbsp;<span class="required">*</span></label>
                <select class="masvideos-Input masvideos-Input--select input-select" name="visibility" id="<?php echo esc_attr( $post_type ); ?>-visibility">
                    <?php foreach ( masvideos_get_movie_playlist_visibility_options() as $key => $value ) : ?>
                        <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $visibility, $key ); ?>><?php echo esc_attr( $value ); ?></option><?php // @codingStandardsIgnoreLine ?>
                    <?php endforeach; ?>
                </select>
            </p>

            <?php do_action( 'masvideos_edit_playlist_form', $playlist ); ?>

            <p class="masvideos-FormRow form-row">
                <input name="id" type="hidden" value="<?php echo esc_attr( $id ); ?>" />
                <input name="post_type" type="hidden" value="<?php echo esc_attr( $post_type ); ?>" />
                <?php wp_nonce_field( 'masvideos-edit-playlist', 'masvideos-edit-playlist-nonce' ); ?>
                <button type="submit" class="masvideos-Button button" name="edit-playlist" value="<?php echo esc_attr( $button_text ); ?>"><?php echo esc_html( $button_text ); ?></button>
                <?php if( ! empty( $playlist ) ) : ?>
                    <a href="<?php echo esc_url( get_permalink() ); ?>" ><?php echo esc_html( $create_button_text ); ?></a>
                <?php endif; ?>
            </p>

            <?php do_action( 'masvideos_edit_playlist_form_end', $playlist ); ?>

        </form>
    </div>
</div>

<?php do_action( 'masvideos_after_edit_playlist_form', $playlist ); ?>
