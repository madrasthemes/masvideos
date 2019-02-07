<?php
/**
 * The template for displaying Category TV Shows.
 * 
 * Template name: Category TV Shows
 *
 * @package vodi
 */

get_header( 'v1' );

/**
 * Hook: vodi_before_main_content.
 *
 */
do_action( 'vodi_before_main_content' );
?>
<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
    	<div class="site-main-inner">
			<?php
                do_action( 'vodi_category_tvshows' ); ?>
		</div><!-- .site-main-inner -->

    </main><!-- #main -->
</div><!-- #primary -->
<?php 

do_action( 'vodi_sidebar', 'shop' );

get_footer( 'v1' );


