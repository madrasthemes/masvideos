<?php
/**
 * Manage Playlists
 *
 * This template can be overridden by copying it to yourtheme/masvideos/myaccount/manage-playlists.php.
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

if( empty( $playlists ) ) {
    return;
}

$section_title = ! empty( $manage_section_title ) ? $manage_section_title : esc_html__( 'Manage Playlists', 'masvideos' );

do_action( 'masvideos_before_manage_playlists' ); ?>

<div class="masvideos-manage-playlists">
    <div class="masvideos-manage-playlists__inner">

        <h2><?php echo esc_html( $section_title ); ?></h2>

        <table>
            <thead>
                <tr>
                    <td><?php echo esc_html__( 'Title', 'masvideos' ) ?></td>
                    <td><?php echo esc_html__( 'Visibility', 'masvideos' ) ?></td>
                    <td><?php echo esc_html__( 'Actions', 'masvideos' ) ?></td>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $playlists as $key => $obj ) : ?>
                    <tr>
                        <td>
                            <a class="view" href="<?php echo get_permalink( $obj->ID ) ?>"><?php echo get_the_title( $obj->ID ); ?></a>
                        </td>
                        <td>
                            <?php
                                $post_status = get_post_status( $obj->ID );
                                $get_visibility_options_func_name = 'masvideos_get_' . $post_type . '_visibility_options';
                                $visibility_options = function_exists( $get_visibility_options_func_name ) ? $get_visibility_options_func_name() : false;
                                if( is_array( $visibility_options ) && isset( $visibility_options[$post_status] ) ) {
                                    echo esc_html( $visibility_options[$post_status] );
                                } else {
                                    echo esc_html( $post_status );
                                }
                            ?>
                        </td>
                        <td>
                            <?php
                                $actions = masvideos_get_account_playlists_actions( $obj );

                                if ( ! empty( $actions ) ) {
                                    foreach ( $actions as $key => $action ) {
                                        echo '<a href="' . esc_url( $action['url'] ) . '" class="masvideos-button button ' . sanitize_html_class( $key ) . '">' . esc_html( $action['name'] ) . '</a>';
                                    }
                                }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</div>

<?php do_action( 'masvideos_after_manage_playlists' ); ?>
