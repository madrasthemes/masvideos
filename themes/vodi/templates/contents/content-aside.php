<?php
/**
 * Template used to display post type aside.
 *
 * @package vodi
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'article article__aside' ); ?>>
    <?php
    /**
     * Functions hooked in to vodi_loop_post_aside action.
     *
     * @hooked vodi_post_meta        - 10
     * @hooked vodi_post_the_content - 20
     */
    do_action( 'vodi_loop_post_aside' );
    ?>
</article><!-- #post-## -->