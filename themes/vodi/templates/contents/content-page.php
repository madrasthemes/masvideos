<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package vodi
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <?php
    /**
     * Functions hooked in to vodi_page add_action
     *
     * @hooked vodi_page_header          - 10
     * @hooked vodi_page_content         - 20
     */
    do_action( 'vodi_page' );
    ?>
</article><!-- #post-## -->