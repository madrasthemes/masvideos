<?php
/**
 * Posts Block
 *
 * @author  MadrasThemes
 * @package Vodi/Templates
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$section_class = empty( $section_class ) ? 'vodi-related-articles' : 'vodi-related-articles ' . $section_class;
if ( ! empty( $animation ) ) {
    $section_class .= ' animate-in-view';
}

$related_posts = new WP_Query( $query_args );
if ( $related_posts->have_posts() ) : ?>
<section class="<?php echo esc_attr( $section_class ); ?>" <?php if ( !empty( $animation ) ) : ?>data-animation="<?php echo esc_attr( $animation );?>"<?php endif; ?>>
    
    <header class="section-header">
        <?php if( ! empty( $section_title ) ) : ?>
            <h2 class="section-title"><?php echo wp_kses_post ( $section_title ); ?></h2>
        <?php endif; ?>

        <?php if ( ! empty( $header_aside_action_text ) ) : ?>
            <div class="header-aside">
                <a href="<?php echo esc_url( $header_aside_action_link ); ?>"><?php echo wp_kses_post( $header_aside_action_text ); ?></a>
            </div>
        <?php endif; ?>
    </header>

    <div class="related-posts columns-<?php echo esc_attr( $columns ); ?>">
        <?php while ( $related_posts->have_posts() ) : $related_posts->the_post(); ?>
        <article class="post article">
            <?php
                vodi_post_featured_image();
                vodi_post_header();
            ?>
        </article>
        <?php endwhile; ?>
    </div>
</section>
<?php endif;
wp_reset_postdata();