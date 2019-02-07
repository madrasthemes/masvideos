
<?php
/**
 * The sidebar containing the blog sidebar widget area.
 *
 * @package vodi
 */
if ( ! is_active_sidebar( 'sidebar-movie' ) ) {
    return;
}
?>

<div id="secondary" class="widget-area sidebar-area movie-sidebar" role="complementary">
    <div class="widget-area-inner">
        <?php dynamic_sidebar( 'sidebar-movie' ); ?>
    </div><!-- /.widget-area-inner -->
</div><!-- #secondary -->
