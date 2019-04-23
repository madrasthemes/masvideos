<?php
/**
 * MasVideos Tabs
 *
 * This template can be overridden by copying it to yourtheme/masvideos/global/tabs.php.
 *
 * HOWEVER, on occasion MasVideos will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * Each tab is an array containing title, callback and priority.
 *
 * @package MasVideos/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( empty( $tabs ) || ! is_array( $tabs ) ) {
    return;
}

$default_active_tab = empty( $default_active_tab ) ? 0 : $default_active_tab;
$tab_uniqid = 'tab-' . uniqid();
$class = empty( $class ) ? 'masvideos-tabs' : 'masvideos-tabs ' . $class;

uasort( $tabs, 'masvideos_sort_priority_callback' );

?>
<div class="<?php echo esc_attr( $class ); ?>">
    <ul class="nav">
        <?php foreach ( $tabs as $key => $tab ) :
            if ( ! is_numeric( $key ) && ! $default_active_tab ) {
                $default_active_tab = $key;
            }
            $tab_id = $tab_uniqid . $key; ?>
            <li class="nav-item">
                <a href="#<?php echo esc_attr( $tab_id ); ?>" data-toggle="tab" class="nav-link<?php if ( $key == $default_active_tab ) echo esc_attr( ' active show' ); ?>">
                    <?php echo wp_kses_post( $tab['title'] ); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
    <div class="tab-content">
        <?php foreach ( $tabs as $key => $tab ) :
            $tab_id = $tab_uniqid . $key; ?>
            <div id="<?php echo esc_attr( $tab_id ); ?>" class="tab-pane<?php if ( $key == $default_active_tab ) echo esc_attr( ' active show' ); ?>">
                <?php
                    if ( isset( $tab['callback'] ) ) {
                        call_user_func( $tab['callback'], $key, $tab );
                    } elseif ( ! empty( $tab['content'] ) ) {
                        echo wp_kses_post( $tab['content'] );
                    }
                ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>