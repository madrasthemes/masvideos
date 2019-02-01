<?php
/**
 * The template for displaying tv show content in the single-tv-show.php template
 *
 * This template can be overridden by copying it to yourtheme/masvideos/content-single-tv-show.php.
 *
 * HOWEVER, on occasion MasVideos will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @package MasVideos/Templates
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Hook: masvideos_before_single_tv_show.
 *
 * @hooked wc_print_notices - 10
 */
do_action( 'masvideos_before_single_tv_show' );

if ( post_password_required() ) {
    echo get_the_password_form(); // WPCS: XSS ok.
    return;
}
?>
<div id="tv-show-<?php the_ID(); ?>" <?php masvideos_tv_show_class(); ?>>

    <?php
        /**
         * Hook: masvideos_before_single_tv_show_summary.
         *
         * @hooked masvideos_show_tv_show_sale_flash - 10
         * @hooked masvideos_show_tv_show_images - 20
         */
        do_action( 'masvideos_before_single_tv_show_summary' );
    ?>

    <div class="summary entry-summary">
        <?php
            /**
             * Hook: masvideos_single_tv_show_summary.
             *
             * @hooked masvideos_template_single_title - 5
             * @hooked masvideos_template_single_rating - 10
             * @hooked masvideos_template_single_price - 10
             * @hooked masvideos_template_single_excerpt - 20
             * @hooked masvideos_template_single_add_to_cart - 30
             * @hooked masvideos_template_single_meta - 40
             * @hooked masvideos_template_single_sharing - 50
             * @hooked WC_Structured_Data::generate_tv_show_data() - 60
             */
            do_action( 'masvideos_single_tv_show_summary' );
        ?>
    </div>

    <?php
        /**
         * Hook: masvideos_after_single_tv_show_summary.
         *
         * @hooked masvideos_output_tv_show_data_tabs - 10
         * @hooked masvideos_upsell_display - 15
         * @hooked masvideos_output_related_tv_shows - 20
         */
        do_action( 'masvideos_after_single_tv_show_summary' );
    ?>
</div>

<?php do_action( 'masvideos_after_single_tv_show' ); ?>
