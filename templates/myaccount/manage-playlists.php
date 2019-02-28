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
                            <?php echo get_post_status( $obj->ID ); ?>
                        </td>
                        <td>
                            <?php
                                $current_page_link = get_permalink();
                                $view_link = get_permalink( $obj->ID );
                                $edit_link = add_query_arg( array( 'post' => $obj->ID, 'action' => 'edit' ), $current_page_link );
                                $delete_link = wp_nonce_url( add_query_arg( array( 'post' => $obj->ID, 'action' => 'delete' ), $current_page_link ), 'masvideos-delete-playlist', 'masvideos-delete-playlist-nonce' );
                            ?>
                            <a class="view" href="<?php echo esc_url( $view_link ); ?>"><?php echo esc_html__( 'View', 'masvideos' ) ?></a>
                            <a class="edit" href="<?php echo esc_url( $edit_link ); ?>"><?php echo esc_html__( 'Edit', 'masvideos' ) ?></a>
                            <a class="delete" href="<?php echo esc_url( $delete_link ); ?>"><?php echo esc_html__( 'Delete', 'masvideos' ) ?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</div>

<?php do_action( 'masvideos_after_manage_playlists' ); ?>
