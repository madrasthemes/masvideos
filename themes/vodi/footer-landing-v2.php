<?php
/**
 * The template for displaying the Landing Page v2 footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package vodi
 */

?>

        </div><!-- /.site-content-inner -->
        
    </div><!-- /.site-content-->

    <?php do_action( 'vodi_before_footer' ); ?>

	<footer class="site-footer site-landing-v2__footer"> 

        <?php
            /**
             * Functions hooked in to vodi_footer_landing_v2 action
             */
            do_action( 'vodi_footer_landing_v2' );
            ?>  
    </footer> 

    <?php do_action( 'vodi_after_footer' ); ?>

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>