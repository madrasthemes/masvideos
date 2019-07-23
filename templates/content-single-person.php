<?php
/**
 * The template for displaying person content in the single-person.php template
 *
 * This template can be overridden by copying it to yourtheme/masvideos/content-single-person.php.
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
 * Hook: masvideos_before_single_person.
 *
 * @hooked wc_print_notices - 10
 */
do_action( 'masvideos_before_single_person' );

if ( post_password_required() ) {
    echo get_the_password_form(); // WPCS: XSS ok.
    return;
}
?>
<div id="person-<?php the_ID(); ?>" <?php masvideos_person_class(); ?>>

    <?php
        /**
         * Hook: masvideos_before_single_person_summary.
         *
         * @hooked masvideos_show_person_sale_flash - 10
         * @hooked masvideos_show_person_images - 20
         */
        do_action( 'masvideos_before_single_person_summary' );

        /**
         * Hook: masvideos_single_person_summary.
         *
         * @hooked masvideos_template_single_title - 5
         * @hooked masvideos_template_single_rating - 10
         * @hooked masvideos_template_single_price - 10
         * @hooked masvideos_template_single_excerpt - 20
         * @hooked masvideos_template_single_add_to_cart - 30
         * @hooked masvideos_template_single_meta - 40
         * @hooked masvideos_template_single_sharing - 50
         * @hooked WC_Structured_Data::generate_person_data() - 60
         */
        do_action( 'masvideos_single_person_summary' );

        /**
         * Hook: masvideos_after_single_person_summary.
         *
         * @hooked masvideos_output_person_data_tabs - 10
         * @hooked masvideos_upsell_display - 15
         * @hooked masvideos_output_related_persons - 20
         */
        do_action( 'masvideos_after_single_person_summary' );
    ?>
</div>

<?php do_action( 'masvideos_after_single_person' ); ?>
