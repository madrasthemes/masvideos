<?php
/**
 * The sidebar containing the shop sidebar widget area.
 *
 * @package vodi
 */

if ( ! is_active_sidebar( 'sidebar-shop' ) ) {
    return;
}
?>

<div id="secondary" class="widget-area" role="complementary">
	<div class="widget-area-inner">
	    <?php dynamic_sidebar( 'sidebar-shop' ); ?>
	</div>
</div><!-- #secondary -->
