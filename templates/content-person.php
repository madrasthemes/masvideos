<?php
/**
 * The template for displaying person content within loops
 *
 * This template can be overridden by copying it to yourtheme/masvideos/content-person.php.
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

global $person;

// Ensure visibility.
if ( empty( $person ) || ! $person->is_visible() ) {
    return;
}
?>
<div <?php masvideos_person_class(); ?>>
    <?php
    /**
     * Hook: masvideos_before_persons_loop_item.
     *
     * @hooked masvideos_template_loop_person_link_open - 10
     */
    do_action( 'masvideos_before_persons_loop_item' );

    /**
     * Hook: masvideos_before_persons_loop_item_title.
     *
     * @hooked masvideos_show_person_loop_sale_flash - 10
     * @hooked masvideos_template_loop_person_thumbnail - 10
     */
    do_action( 'masvideos_before_persons_loop_item_title' );

    /**
     * Hook: masvideos_persons_loop_item_title.
     *
     * @hooked masvideos_template_loop_person_title - 10
     */
    do_action( 'masvideos_persons_loop_item_title' );

    /**
     * Hook: masvideos_after_persons_loop_item_title.
     *
     * @hooked masvideos_template_loop_rating - 5
     * @hooked masvideos_template_loop_price - 10
     */
    do_action( 'masvideos_after_persons_loop_item_title' );

    /**
     * Hook: masvideos_after_persons_loop_item.
     *
     * @hooked masvideos_template_loop_person_link_close - 5
     * @hooked masvideos_template_loop_add_to_cart - 10
     */
    do_action( 'masvideos_after_persons_loop_item' );
    ?>
</div>
