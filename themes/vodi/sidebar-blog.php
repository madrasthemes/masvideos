<?php
/**
 * The sidebar containing the blog sidebar widget area.
 *
 * @package vodi
 */
if ( ! is_active_sidebar( 'sidebar-blog' ) ) {
    return;
}
?>

<div id="secondary" class="widget-area sidebar-area blog-sidebar" role="complementary">
    <div class="widget-area-inner">
        <?php dynamic_sidebar( 'sidebar-blog' ); ?>
    </div><!-- /.widget-area-inner -->
</div><!-- #secondary -->