<?php
/**
 * Episode data meta box.
 *
 * @package MasVideos/Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<style type="text/css">
    #post-preview { display:none }
</style>
<div class="panel-wrap episode_data">

    <ul class="episode_data_tabs masvideos-tabs">
        <?php foreach ( self::get_episode_data_tabs() as $key => $tab ) : ?>
            <li class="<?php echo esc_attr( $key ); ?>_options <?php echo esc_attr( $key ); ?>_tab <?php echo esc_attr( isset( $tab['class'] ) ? implode( ' ', (array) $tab['class'] ) : '' ); ?>">
                <a href="#<?php echo esc_attr( $tab['target'] ); ?>"><span><?php echo esc_html( $tab['label'] ); ?></span></a>
            </li>
        <?php endforeach; ?>
        <?php do_action( 'masvideos_episode_write_panel_tabs' ); ?>
    </ul>

    <?php
        self::output_tabs();
        do_action( 'masvideos_episode_data_panels' );
    ?>
    <div class="clear"></div>
</div>
