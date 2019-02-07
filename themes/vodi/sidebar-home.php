<?php
/**
 * The sidebar containing the home widget area.
 *
 * @package vodi
 */

if ( ! is_active_sidebar( 'home-sidebar' ) ) {
	return;
}
?>

<div id="secondary" class="home-sidebar-area light" role="complementary">
	<div class="home-sidebar-area-inner">
		<?php dynamic_sidebar( 'home-sidebar' ); ?>
	</div>
</div><!-- #secondary -->
