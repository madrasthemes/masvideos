<?php
/**
 * The template for displaying archive pages.
 *
 * Learn more: https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package vodi
 */
add_action( 'vodi_content_top', 'vodi_archive_header' );

get_header();

    do_action( 'vodi_before_main_content' ); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php if ( have_posts() ) : ?>

			<?php get_template_part( 'loop' );

		else :

			get_template_part( 'content', 'none' );

		endif; ?>

		</main><!-- #main -->
	</div><!-- #primary --><?php
    
    do_action( 'vodi_after_main_content' );

get_footer();