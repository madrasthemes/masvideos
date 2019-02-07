<?php
/**
 * Template used to display post content.
 *
 * @package vodi
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'article' ); ?>>

    <?php
    /**
     * Functions hooked in to vodi_loop_post action.
     *
     * @hooked vodi_post_header          - 10
     * @hooked vodi_post_meta            - 20
     * @hooked vodi_post_content         - 30
     */
    do_action( 'vodi_loop_post' );
    ?>

</article><!-- #post-## -->