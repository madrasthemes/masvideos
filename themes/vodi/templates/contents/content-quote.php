<?php
/**
 * Template used to display post type quote.
 *
 * @package vodi
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'article article__quote' ); ?>>
    <?php
    /**
     * Functions hooked in to vodi_loop_post_quote action.
     *
     * @hooked vodi_post_the_content - 10
     */
    do_action( 'vodi_loop_post_quote' );
    ?>

</article><!-- #post-## -->