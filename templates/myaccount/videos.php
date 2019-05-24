<?php
/**
 * Orders
 *
 * Shows videos on the account page.
 *
 * This template can be overridden by copying it to yourtheme/masvideos/myaccount/videos.php.
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

do_action( 'masvideos_before_account_videos', $has_videos ); ?>

<?php if ( $has_videos ) : ?>

    <table class="masvideos-videos-table masvideos-MyAccount-videos shop_table shop_table_responsive my_account_videos account-videos-table">
        <thead>
            <tr>
                <?php foreach ( masvideos_get_account_videos_columns() as $column_id => $column_name ) : ?>
                    <th class="masvideos-videos-table__header masvideos-videos-table__header-<?php echo esc_attr( $column_id ); ?>"><span class="nobr"><?php echo esc_html( $column_name ); ?></span></th>
                <?php endforeach; ?>
            </tr>
        </thead>

        <tbody>
            <?php foreach ( $user_videos->videos as $customer_video ) :
                $video      = masvideos_get_video( $customer_video );
                ?>
                <tr class="masvideos-videos-table__row masvideos-videos-table__row--status-<?php echo esc_attr( $video->get_status() ); ?> video">
                    <?php foreach ( masvideos_get_account_videos_columns() as $column_id => $column_name ) : ?>
                        <td class="masvideos-videos-table__cell masvideos-videos-table__cell-<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_name ); ?>">
                            <?php if ( has_action( 'masvideos_my_account_my_videos_column_' . $column_id ) ) : ?>
                                <?php do_action( 'masvideos_my_account_my_videos_column_' . $column_id, $video ); ?>

                            <?php elseif ( 'video-title' === $column_id ) : ?>
                                <a href="<?php echo esc_url( get_permalink( $video->get_id() ) ); ?>">
                                    <?php echo $video->get_name(); ?>
                                </a>

                            <?php elseif ( 'video-date' === $column_id ) : ?>
                                <time datetime="<?php echo esc_attr( $video->get_date_created()->date( 'c' ) ); ?>"><?php echo esc_html( masvideos_format_datetime( $video->get_date_created() ) ); ?></time>

                            <?php elseif ( 'video-status' === $column_id ) : ?>
                                <?php echo esc_html( $video->get_status() ); ?>

                            <?php elseif ( 'video-actions' === $column_id ) : ?>
                                <?php
                                $actions = masvideos_get_account_videos_actions( $video );

                                if ( ! empty( $actions ) ) {
                                    foreach ( $actions as $key => $action ) {
                                        echo '<a href="' . esc_url( $action['url'] ) . '" class="masvideos-button button ' . sanitize_html_class( $key ) . '">' . esc_html( $action['name'] ) . '</a>';
                                    }
                                }
                                ?>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php do_action( 'masvideos_before_account_videos_pagination' ); ?>

    <?php if ( 1 < $user_videos->max_num_pages ) : ?>
        <div class="masvideos-pagination masvideos-pagination--without-numbers masvideos-Pagination">
            <?php if ( 1 !== $current_page ) : ?>
                <a class="masvideos-button masvideos-button--previous masvideos-Button masvideos-Button--previous button" href="<?php echo esc_url( masvideos_get_endpoint_url( 'videos', $current_page - 1 ) ); ?>"><?php _e( 'Previous', 'masvideos' ); ?></a>
            <?php endif; ?>

            <?php if ( intval( $user_videos->max_num_pages ) !== $current_page ) : ?>
                <a class="masvideos-button masvideos-button--next masvideos-Button masvideos-Button--next button" href="<?php echo esc_url( masvideos_get_endpoint_url( 'videos', $current_page + 1 ) ); ?>"><?php _e( 'Next', 'masvideos' ); ?></a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

<?php else : ?>
    <div class="masvideos-message masvideos-message--info masvideos-Message masvideos-Message--info masvideos-info">
        <a class="masvideos-Button button" href="<?php echo esc_url( apply_filters( 'masvideos_account_videos_upload_redirect', masvideos_get_page_permalink( 'upload_video' ) ) ); ?>">
            <?php _e( 'Upload videos', 'masvideos' ); ?>
        </a>
        <?php _e( 'No video has been made yet.', 'masvideos' ); ?>
    </div>
<?php endif; ?>

<?php do_action( 'masvideos_after_account_videos', $has_videos ); ?>
