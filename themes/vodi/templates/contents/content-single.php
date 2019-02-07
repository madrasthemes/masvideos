<?php
/**
 * Template used to display post content on single pages.
 *
 * @package vodi
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'single-article' ); ?>>

    <?php
    do_action( 'vodi_single_post_top' );

    /**
     * Functions hooked into vodi_single_post add_action
     *
     * @hooked vodi_post_header          - 10
     * @hooked vodi_post_content         - 30
     */
    do_action( 'vodi_single_post' );

    /**
     * Functions hooked in to vodi_single_post_bottom action
     *
     * @hooked vodi_post_nav         - 10
     * @hooked vodi_display_comments - 20
     */
    do_action( 'vodi_single_post_bottom' );
    ?>

</article><!-- #post-## -->