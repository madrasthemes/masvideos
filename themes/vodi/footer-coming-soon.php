<?php
/**
 * The template for displaying the coming soon Page footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package vodi
 */

?>

            </div><!-- /.site-content-inner -->

        <?php do_action( 'vodi_content_bottom' ); ?>

        
    </div><!-- /.site-content-->

    <?php do_action( 'vodi_before_footer' ); ?>

    <footer class="site-footer">
    	<div class="container">
            <?php
            /**
             * Functions hooked in to vodi_footer_comingsoon action
             */
            do_action( 'vodi_footer_comingsoon' );
            ?>   
        </div>
    </footer>
    
    <?php do_action( 'vodi_after_footer' ); ?>

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>