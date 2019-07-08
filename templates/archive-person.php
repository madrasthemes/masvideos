<?php
/**
 * The Template for displaying person archives, including the main persons page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/masvideos/archive-person.php.
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

get_header( 'person' );

/**
 * Hook: masvideos_before_main_content.
 *
 * @hooked masvideos_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked masvideos_breadcrumb - 20
 * @hooked WC_Structured_Data::generate_website_data() - 30
 */
do_action( 'masvideos_before_main_content' );

/**
 * Hook: masvideos_archive_header.
 *
 * @hooked masvideos_person_archive_description - 10
 */
do_action( 'masvideos_archive_header' );

if ( masvideos_persons_loop() ) {

    /**
     * Hook: masvideos_before_persons_loop.
     *
     * @hooked masvideos_print_notices - 10
     * @hooked masvideos_result_count - 20
     * @hooked masvideos_catalog_ordering - 30
     */
    do_action( 'masvideos_before_persons_loop' );

    masvideos_person_loop_start();

    if ( masvideos_get_persons_loop_prop( 'total' ) ) {
        while ( have_posts() ) {
            the_post();

            /**
             * Hook: masvideos_persons_loop.
             *
             * @hooked WC_Structured_Data::generate_person_data() - 10
             * @hooked masvideos_persons_loop_content() - 20
             */
            do_action( 'masvideos_persons_loop' );
        }
    }

    masvideos_person_loop_end();

    /**
     * Hook: masvideos_after_persons_loop.
     *
     * @hooked masvideos_pagination - 10
     */
    do_action( 'masvideos_after_persons_loop' );
} else {
    /**
     * Hook: masvideos_no_persons_found.
     *
     * @hooked masvideos_no_persons_found - 10
     */
    do_action( 'masvideos_no_persons_found' );
}

/**
 * Hook: masvideos_after_main_content.
 *
 * @hooked masvideos_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'masvideos_after_main_content' );

/**
 * Hook: masvideos_sidebar.
 *
 * @hooked masvideos_get_sidebar - 10
 */
do_action( 'masvideos_sidebar' );

get_footer( 'person' );
