<?php
/**
 * The template for displaying search form
 *
 * This template can be overridden by copying it to yourtheme/masvideos/searchform.php.
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
    exit;
}

if( ! isset( $post_type ) ) {
    return;
}

?>
<form role="search" method="get" class="masvideos-search masvideos-search-<?php echo esc_attr( $post_type ); ?>" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <label class="screen-reader-text" for="masvideos-search-field-<?php echo isset( $index ) ? absint( $index ) : 0; ?>"><?php esc_html_e( 'Search for:', 'masvideos' ); ?></label>
    <input type="search" id="masvideos-search-field-<?php echo isset( $index ) ? absint( $index ) : 0; ?>" class="search-field" placeholder="<?php echo esc_attr__( 'Search &hellip;', 'masvideos' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
    <button type="submit" value="<?php echo esc_attr_x( 'Search', 'submit button', 'masvideos' ); ?>"><?php echo esc_html_x( 'Search', 'submit button', 'masvideos' ); ?></button>
    <input type="hidden" name="post_type" value="<?php echo esc_attr( $post_type ); ?>" />
</form>
