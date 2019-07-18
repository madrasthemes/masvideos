<?php
/**
 * Admin View: Import TMDB data
 *
 * @package MasVideos/Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<form class="masvideos-tmdb-form-content masvideos-tmdb-import-data" enctype="multipart/form-data" action="<?php echo esc_url( $action ) ?>" method="post">
    <header>
        <h2><?php esc_html_e( 'Import', 'masvideos' ); ?></h2>
        <p><?php esc_html_e( 'This tool allows you to import data.', 'masvideos' ); ?></p>
        <?php if( $this->type === 'movie' ) : ?>
            <p><?php echo sprintf( '%d %s', $this->results_csv_data_count, esc_html__( 'Movies found.', 'masvideos' ) ); ?></p>
        <?php elseif( $this->type === 'tv_show' ) : ?>
            <p><?php echo sprintf( '%d %s', $this->results_csv_data_count, esc_html__( 'TV Shows found.', 'masvideos' ) ); ?></p>
        <?php endif; ?>
    </header>
    <section>
        <input type="hidden" name="file_url" value="<?php echo esc_attr( $file_url ) ?>" />
        <input type="hidden" name="update_existing" value="1" />
    </section>
    <div class="masvideos-actions">
        <button type="submit" class="button button-primary button-next" value="<?php esc_attr_e( 'Continue', 'masvideos' ); ?>" name="save_step"><?php esc_html_e( 'Continue', 'masvideos' ); ?></button>
        <?php wp_nonce_field( 'masvideos-csv-importer' ); ?>
    </div>
</form>
