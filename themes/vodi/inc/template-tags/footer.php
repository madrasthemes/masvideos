<?php
/**
 * Template tags used in Footer
 */

if ( ! function_exists( 'vodi_footer_top_bar' ) ) {
    function vodi_footer_top_bar() {
        if ( apply_filters( 'vodi_footer_v1_top_bar', true ) ): ?>
            <div class="footer-top-bar">
                <?php
                    /**
                     * Functions hooked in to vodi_footer_v3_bar action
                     *
                     * @hooked vodi_footer_logo            - 10
                     * @hooked vodi_footer_social_icons    - 20
                     *
                     */
                    do_action( 'vodi_footer_top_bar' );
                ?>
            </div>
        <?php endif;
    }
}

if ( ! function_exists( 'vodi_footer_site_info' ) ) {
    function vodi_footer_site_info() {
        if ( apply_filters( 'vodi_footer_site_info_bar', true ) ): ?>
            <div class="footer-bottom-bar">
                <div class="container">
                    <div class="footer-bottom-bar-inner">
                        <?php
                            /**
                             * Functions hooked in to vodi_footer_v3_bar action
                             *
                             * @hooked vodi_credit         - 10
                             * @hooked vodi_policy         - 20
                             *
                             */
                            do_action( 'vodi_footer_site_info' );
                        ?>
                    </div>
                </div>
            </div>
        <?php endif;
    }
}

if ( ! function_exists( 'vodi_footer_bar' ) ) {
    function vodi_footer_bar() {
        ?>
        <div class="footer-bar">
            <?php
                /**
                 * Functions hooked in to vodi_footer_bar action
                 *
                 * @hooked vodi_footer_logo         - 10
                 * @hooked vodi_footer_menu         - 20
                 * @hooked vodi_footer_social_icons - 30
                 *
                 */
                do_action( 'vodi_footer_bar' ); ?>
        </div><!-- /.footer-bar -->
        <?php
    }
}

if ( ! function_exists( 'vodi_credit' ) ) {
    /**
     * Display the theme credit
     *
     * @since  1.0.0
     * @return void
     */
    function vodi_credit() {
        ?>
        <div class="site-info">
            <?php
                $copyright_text = sprintf( esc_html__( 'Copyright &copy; %s, %s Platform, INC. All Rights Reserved', 'vodi' ), date( 'Y' ), get_bloginfo( 'name' ) ); 
                echo esc_html( apply_filters( 'vodi_copyright_text', $copyright_text ) ); ?>
        </div><!-- .site-info -->
        <?php
    }
}

if ( ! function_exists( 'vodi_policy' ) ) {
    /**
     * Display the theme Policy
     *
     * @since  1.0.0
     * @return void
     */
    function vodi_policy() {
        ?>
        <div class="policy-info">
            <?php
                $policy_text = wp_kses_post( __( '<a href="#">Privacy Policy</a>', 'vodi' ) ); 
                echo wp_kses_post( apply_filters( 'vodi_policy_text', $policy_text ) ); ?>
        </div><!-- .policy-info -->
        <?php
    }
}

if ( ! function_exists( 'vodi_footer_logo' ) ) {
    function vodi_footer_logo() {
        ?><div class="footer-logo">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php vodi_get_template( 'global/logo-svg.php' ); ?></a>
        </div><?php
    }
}

if ( ! function_exists( 'vodi_footer_menu' ) ) {
    /**
     * Display the footer menu
     *
     * @since 1.0.0
     * @return void
     */
    function vodi_footer_menu() {
        if ( has_nav_menu( 'footer' ) ) {
            wp_nav_menu( array( 
                'container'       => false,
                'menu_class'      => 'footer-menu', 
                'depth'           => 1,
                'theme_location'  => 'footer' 
            ) );
        }
    }
}

if ( ! function_exists( 'vodi_footer_menu_primary_menu' ) ) {
    /**
     * Display the footer primary menu
     *
     * @since 1.0.0
     * @return void
     */
    function vodi_footer_menu_primary_menu() {
        if ( has_nav_menu( 'footer-primary-menu' ) ) {

            ?> <div class= "footer__primary-nav-wrap">
                <div class="container"><?php 
                    wp_nav_menu( array( 
                        'container'         => false,
                        'menu_class'        => 'footer-primary-menu', 
                        'depth'             => 1,
                        'theme_location'    => 'footer-primary-menu' 
                    ) );
                ?></div>
            </div> <?php
        }
    }
}

if ( ! function_exists( 'vodi_footer_menu_secondary_menu' ) ) {
    /**
     * Display the footer secondary menu
     *
     * @since 1.0.0
     * @return void
     */
    function vodi_footer_menu_secondary_menu() {
        if ( has_nav_menu( 'footer-secondary-menu' ) ) {
            wp_nav_menu( array( 
                'container'       => false,
                'menu_class'      => 'footer-secondary-menu', 
                'depth'           => 1,
                'theme_location'  => 'footer-secondary-menu' 
            ) );
        }
    }
}

if ( ! function_exists( 'vodi_footer_menu_tertiary_menu' ) ) {
    /**
     * Display the footer tertiary menu
     *
     * @since 1.0.0
     * @return void
     */
    function vodi_footer_menu_tertiary_menu() {
        if ( has_nav_menu( 'footer-tertiary-menu' ) ) {
            wp_nav_menu( array( 
                'container'       => false,
                'menu_class'      => 'footer-tertiary-menu', 
                'depth'           => 1,
                'theme_location'  => 'footer-tertiary-menu' 
            ) );
        }
    }
}

if ( ! function_exists( 'vodi_footer_social_icons' ) ) {
    /**
     * Displays social icons at the footer
     */
    function vodi_footer_social_icons() {
        $allowed_protocols   = wp_parse_args( array( 'whatsapp' ), wp_allowed_protocols() );
        $social_networks     = apply_filters( 'vodi_set_social_networks', vodi_get_social_networks() );
        $social_links_output = '';
        $social_link_html    = apply_filters( 'vodi_footer_social_link_html', '<a class="footer-social-icon" target="_blank" href="%2$s"><span class="fa-stack"><i class="fas fa-circle fa-stack-2x"></i><i class="%1$s fa-stack-1x fa-inverse"></i></span></a>' );

        foreach ( $social_networks as $social_network ) {
            if ( isset( $social_network[ 'link' ] ) && !empty( $social_network[ 'link' ] ) ) {
                $social_links_output .= sprintf( '<li>' . $social_link_html . '</li>', $social_network[ 'icon' ], $social_network[ 'link' ] );
            }
        }

        if ( apply_filters( 'vodi_footer_social_icons', true ) && ! empty( $social_links_output ) ) {

            ob_start();
            ?>
            <div class="footer-social-icons">
                <ul class="social-icons">
                    <?php echo wp_kses( $social_links_output, 'post', $allowed_protocols ); ?>
                </ul>
            </div>
            <?php
            echo apply_filters( 'vodi_footer_social_links_html', ob_get_clean() );
        }
    }
}

if ( ! function_exists( 'vodi_footer_social_icons_1' ) ) {
    /**
     * Displays footer social icons with name
     */
    function vodi_footer_social_icons_1() {
        $allowed_protocols   = wp_parse_args( array( 'whatsapp' ), wp_allowed_protocols() );
        $social_networks        = apply_filters( 'vodi_set_social_networks', vodi_get_social_networks() );
        $social_links_output    = '';
        $social_link_html       = apply_filters( 'vodi_footer_social_link_html', '<a class="footer-social-icon" target="_blank" href="%2$s"><i class="%1$s"></i> %3$s</a>' );

        foreach ( $social_networks as $social_network ) {
            if ( isset( $social_network[ 'link' ] ) && !empty( $social_network[ 'link' ] ) ) {
                $social_links_output .= sprintf( '<li>' . $social_link_html . '</li>', $social_network[ 'icon' ], $social_network[ 'link' ], $social_network[ 'label' ] );
            }
        }

        if ( apply_filters( 'vodi_footer_social_icons', true ) && ! empty( $social_links_output ) ) {

            ob_start();
            ?>
            <div class="footer-social-icons social-label">
                <ul class="social-icons">
                    <?php echo wp_kses( $social_links_output, 'post', $allowed_protocols ); ?>
                </ul>
            </div>
            <?php
            echo apply_filters( 'vodi_footer_social_links_html', ob_get_clean() );
        }
    }
}

if ( ! function_exists( 'vodi_footer_widgets' ) ) {
    /**
     * Display the footer widget regions.
     *
     * @since  1.0.0
     * @return void
     */
    function vodi_footer_widgets() {
        
        if( apply_filters( 'vodi_footer_widgets', true  ) ) {

            $rows    = intval( apply_filters( 'vodi_footer_widget_rows', 1 ) );
            $regions = intval( apply_filters( 'vodi_footer_widget_columns', 3 ) );
            for ( $row = 1; $row <= $rows; $row++ ) :
                // Defines the number of active columns in this footer row.
                for ( $region = $regions; 0 < $region; $region-- ) {
                    if ( is_active_sidebar( 'footer-' . strval( $region + $regions * ( $row - 1 ) ) ) ) {
                        $columns = $region;
                        break;
                    }
                }

                if ( isset( $columns ) ) : ?>
                    <div class="footer-widgets">
                        <div class=<?php echo '"footer-widgets-inner row-' . strval( $row ) . ' col-' . strval( $columns ) . ' fix"'; ?>><?php
                            for ( $column = 1; $column <= $columns; $column++ ) :
                                $footer_n = $column + $regions * ( $row - 1 );
                                if ( is_active_sidebar( 'footer- ' . strval( $footer_n ) ) ) : ?>

                                    <div class="block footer-widget-<?php echo strval( $column ); ?>">
                                        <?php dynamic_sidebar( 'footer-' . strval( $footer_n ) ); ?>
                                    </div><?php
                                endif;
                            endfor; ?>
                        </div>
                    </div><!-- .footer-widgets.row-<?php echo strval( $row ); ?> --><?php
                    unset( $columns );
                endif;
            endfor;
        }
    }
}

if ( ! function_exists( 'vodi_footer_bottom' ) ) {
    function vodi_footer_bottom() {
        if ( apply_filters( 'vodi_footer_v2_bottom', true ) ): ?>
            <div class="footer-bottom">
                <?php
                    /**
                     * Functions hooked in to vodi_footer_bottom action
                     *
                     * @hooked vodi_footer_bottom_content_div_open  - 10
                     * @hooked vodi_footer_top_bar                  - 20
                     * @hooked vodi_footer_menu_secondary_menu      - 30
                     * @hooked vodi_credit                          - 40
                     * @hooked vodi_footer_menu_tertiary_menu       - 50
                     * @hooked vodi_footer_div_close                - 60
                     * @hooked vodi_footer_news_letter              - 70
                     *
                     */
                    do_action( 'vodi_footer_bottom' );
                ?>
            </div>
        <?php endif;
    }
}

if ( ! function_exists( 'vodi_footer_bottom_content_div_open' ) ) {
    function vodi_footer_bottom_content_div_open() {
        ?>
            <div class="footer-bottom-content">
        <?php
    }
}

if ( ! function_exists( 'vodi_footer_div_close' ) ) {
    function vodi_footer_div_close() {
        ?>
            </div>
        <?php
    }
}

if ( ! function_exists( 'vodi_footer_news_letter' ) ) {
    function vodi_footer_news_letter() {

        if ( apply_filters( 'vodi_footer_news_letter', true ) ) {

            $footer_news_letter = apply_filters( 'footer_news_letter_content', array(
                'title'         => wp_kses_post( __( 'Watch Vodi. Anytime<br> Anywhere.', 'vodi' ) ),
                'sub_title'     => wp_kses_post( __( 'Subscribe to our newsletter and get unique alerts.', 'vodi' ) ),
                'link'          => '#',
                'action_text'   => esc_html__( 'SIGN UP', 'vodi' )
            ) );

            ?><div class="footer-news-letter">
                <h2 class="title"><?php echo wp_kses_post( $footer_news_letter['title'] ); ?></h2>
                <h4 class="sub-title"><?php echo wp_kses_post( $footer_news_letter['sub_title'] ); ?></h4>
                <a href="<?php echo esc_url( $footer_news_letter['link'] );?>" class="footer-news-letter-action-link">
                    <?php echo esc_html( $footer_news_letter['action_text'] ); ?>
                </a>
            </div><?php
        }
    }
}

if ( ! function_exists( 'vodi_comingsoon_footer' ) ) {
    function vodi_comingsoon_footer() {
        ?><div class="cs-footer">
                <div class="cs-footer__footer-bar">
                    <div class="cs-footer__footer-social-icons">
                        <ul class="social-icons">
                            <li>
                                <a class="footer-social-icon" target="_blank" href="#">
                                    <span class="fa-stack">
                                        <i class="fas fa-circle fa-stack-2x"></i>
                                        <i class="fab fa-facebook-f fa-stack-1x fa-inverse"></i>
                                    </span>
                                </a>
                            </li>
                            <li>
                                <a class="footer-social-icon" target="_blank" href="#">
                                    <span class="fa-stack">
                                        <i class="fas fa-circle fa-stack-2x"></i>
                                        <i class="fab fa-twitter fa-stack-1x fa-inverse"></i>
                                    </span>
                                </a>
                            </li>                
                        </ul>
                    </div>
                    <div class="cs-footer__site-info">
                        © 2018 Vodi Platform.. All Rights Reserved.        
                    </div>
                </div>
            </div> 
        <?php
    }
}

if ( ! function_exists( 'vodi_landing_v1_footer' ) ) {
    function vodi_landing_v1_footer() {
        ?><div class="footer-bar">
            <a href="#">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" width="103px" height="37px" viewBox="0 0 103 37" enable-background="new 0 0 103 37" xml:space="preserve">  <image id="image0" width="103" height="37" x="0" y="0" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGcAAAAlCAMAAABoDwesAAAABGdBTUEAALGPC/xhBQAAACBjSFJN AAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAACkVBMVEX///+l7f8Lz/8w1v8v 1v+q7v8P0P/4/v9G2/9P3f9L3P9A2v8Y0f8Y0v9b3/862P9q4v/C8/+98v9f3/+/8/8Tz/915P+8 8v+y8P/x/P/7/v/i+f////////////////////8v1v8v1v8v1v////////////8v1v8v1v8v1v// //////8e0v8v1v8v1v////////8b0v8v1v////////////8c0v8v1v////////8c0v////////8c 0v8v1v8c0v8v1v8v1v////8c0v8v1v8v1v////////////////////////////////////////// //////////////////////8d0v8y1/8g0/////////////////////////////////////////// //////////////////////8Y0f8V0f8Mz/////////////////////////////////////////// //////////////////////////////////////////////////////////////////////////// //////////////////////////////////////////////////////////////////////////// //////////////////////////////////////////////////////////////////////////// //////////////////////////////////////////////////////////////////////////// //////////////////////////////////////////////////////////////////////////// //////////////////8v1v+J7W/yAAAA2XRSTlMAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA Kk5KTRkaOgICnUskzyiGJpSCCQq5jkIj6AF3eyLkVyDgXHRd6zMhbXcGBg8JCxQtMg0EAxU4Rz0I EyDKIVn72US6jC6Oz+ulSSyVi4ALFgF5r9yJzmspOtVVDLBGbVYOVB7zmFGgGvFdc9G+GJo3ogX1 daj3rSvvtBEoJMg5B5K1H5RC5rJoTxb96e1hEtcwLzTDWMWjEKY8q9p3HPmbgkGqZ8FwfsyPW3Fk wFBAtzMn4iVfiF6Df8dcdIV8qeINmwAAAAFiS0dEAIgFHUgAAAAHdElNRQfiCAkVGRvRqBUNAAAD 9klEQVRIx7WW6X8NVxjHH0stEdROSxIVW4KoGHUrtFJucm96b242RBC5QpRYWlsQQramTSxJqbpV Yg9ZEEFsoYutltZSnPvXmOWc55yZO+PTN/O8Ouf3POf5njnznHkGwA4bPiIiMgqgU+cuIz8Z1VUx OyjRo4liY+CDbmODwXHj7eLEEM1iu/eYEJRt4iRbOHEUQyZDz0+Dqk2xgxPPOFOlsGkaJ/iZDZzp jOOQwj6nnKANnBmMkyCFzaSYWTZzvlApX8624/0kCpyvFMycuVq9OZOizeKTknVTyeVO+drt8Vpk T/WlpadHZCRmQhbn9JoXDM5fEN47PFyOyF5IchaFrItdTMiSVMySuzRPW+xflh8XEiwtX5HAshes 5Jw+nb9Ztbrvh/36DwAoVLUYw8o0VV2jgaIj1xLR1rkNweuJmSVIAwcNHjJ0mBrjpuK3upXfUXWD MonfGJJikxC7eQshFpyPPgZn0dZt2+WoYqbuEA97JxVL5Mkusxy78UhLy4glR96wmikfoJypayXO qWBipRCgt+8pqIqQ93A8dJgo7DYfMT7UfoAfrbJUq6E1e97L2UuH+4T97K9hnFrU6lKt0+xSQneL yvqfjJwDdDgVpErUCylmOCoLk1fyZbVp7oM/rxPyyPfrEJ/9cjgA3h2/6jlH6NAvXxTuyNKq2IFC hIc76bH+th8VuUT34eQovevHdBxWivWy5yg6jquRJ3CeAydxjDf51GkmnQGvYali5VYc51n+OkBo U4T4AC/gEV6MDegPROHwHPeft+BAo7gDKMHZVqjBcRPP40JxejMbtQiX74IVB5biymzepchFCQ6K 7xytBeEZfEvCl9GS461nrrKsS5i6FaAVJ04h0WUm5raxUYPgxr7tMHJ4yyD8BihLebkJ5y85mJhy mI2uCJwiJl4FIweuEaO1KJ8hL5ZIMc9TyuuknY2uZ3I/uzOkNpQj5Rg5Sap+g03reX9DjUi8rm+i uwm18lAOtBsw5Zp8C4Vq9pndhNJtgMU42UbdVXi9lLMO4UCHDnOHZvXyz+Qa9RUF7vKgRoBmPitU uow3nQsHwIwDup7oY2qxIFZHFv0uNJs8eS/SH4K/9s/RfmFaZc7xCSF/8eKqJFam7uWcpfsemHPg PoY8EFqex6pdtml+q/50RVd7foEDF1mMS1Szdprmecj8j0zdl7Stmj0PuOjp1oHONv9tkucx9z/x h7pPGM6oRJfRpZTp02dgMOmCMU2B7omdFQb3bfynCVDF8CMmJf6TYfbDmdzgENL82270e57zat3z YoZ46spHt/4J/F+Ljip+uexV3vGC+3UBM7/U2vTf6zdvO4pKawye+IwUudW+AyQwJXa88WdhAAAA JXRFWHRkYXRlOmNyZWF0ZQAyMDE4LTA4LTA5VDIxOjI1OjI3LTA3OjAwWVHnbgAAACV0RVh0ZGF0 ZTptb2RpZnkAMjAxOC0wOC0wOVQyMToyNToyNy0wNzowMCgMX9IAAAAZdEVYdFNvZnR3YXJlAEFk b2JlIEltYWdlUmVhZHlxyWU8AAAAAElFTkSuQmCC"/>
                </svg>
            </a>
            <ul class="footer-menu">
                <li class="menu-item">
                    <a href="#">About Us</a>
                </li>
                <li class="menu-item">
                    <a href="#">FAQ</a>
                </li>
                <li class="menu-item">
                    <a href="#">Use voucher</a>
                </li>
                <li class="menu-item">
                    <a href="#">Privacy Ploicy</a>
                </li>
                <li class="menu-item">
                    <a href="#">Need help?</a>
                </li>
            </ul>            
            <div class="footer-social-icons">
                <ul class="social-icons">
                    <li>
                        <a class="footer-social-icon" target="_blank" href="#">
                            <span class="fa-stack">
                                <i class="fas fa-circle fa-stack-2x"></i>
                                <i class="fab fa-facebook-f fa-stack-1x fa-inverse"></i>
                            </span>
                        </a>
                    </li>
                    <li>
                        <a class="footer-social-icon" target="_blank" href="#">
                            <span class="fa-stack">
                                <i class="fas fa-circle fa-stack-2x"></i>
                                <i class="fab fa-twitter fa-stack-1x fa-inverse"></i>
                            </span>
                        </a>
                    </li>                
                </ul>
            </div>
        </div>
        <div class="site-info">
            © 2018 Vodi Platform.. All Rights Reserved.        
        </div>
        <?php
    }
}

if ( ! function_exists( 'vodi_landing_v2_footer' ) ) {
    function vodi_landing_v2_footer() {
        ?><div class="lp-v2-footer">
           <div class="view-count">
                <div class="container">
                    <div class="view-count__content">
                        <h3 class="count">1,500,886</h3>
                        <p>materials to watch so far.</p>
                    </div>
                </div>
            </div>
            <div class="footer-bar-site-info">
                <div class="container">
                    <div class="footer-bar">
                        <a href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" width="103px" height="37px" viewBox="0 0 103 37" enable-background="new 0 0 103 37" xml:space="preserve">  <image id="image0" width="103" height="37" x="0" y="0" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGcAAAAlCAMAAABoDwesAAAABGdBTUEAALGPC/xhBQAAACBjSFJN AAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAACkVBMVEX///+l7f8Lz/8w1v8v 1v+q7v8P0P/4/v9G2/9P3f9L3P9A2v8Y0f8Y0v9b3/862P9q4v/C8/+98v9f3/+/8/8Tz/915P+8 8v+y8P/x/P/7/v/i+f////////////////////8v1v8v1v8v1v////////////8v1v8v1v8v1v// //////8e0v8v1v8v1v////////8b0v8v1v////////////8c0v8v1v////////8c0v////////8c 0v8v1v8c0v8v1v8v1v////8c0v8v1v8v1v////////////////////////////////////////// //////////////////////8d0v8y1/8g0/////////////////////////////////////////// //////////////////////8Y0f8V0f8Mz/////////////////////////////////////////// //////////////////////////////////////////////////////////////////////////// //////////////////////////////////////////////////////////////////////////// //////////////////////////////////////////////////////////////////////////// //////////////////////////////////////////////////////////////////////////// //////////////////////////////////////////////////////////////////////////// //////////////////8v1v+J7W/yAAAA2XRSTlMAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA Kk5KTRkaOgICnUskzyiGJpSCCQq5jkIj6AF3eyLkVyDgXHRd6zMhbXcGBg8JCxQtMg0EAxU4Rz0I EyDKIVn72US6jC6Oz+ulSSyVi4ALFgF5r9yJzmspOtVVDLBGbVYOVB7zmFGgGvFdc9G+GJo3ogX1 daj3rSvvtBEoJMg5B5K1H5RC5rJoTxb96e1hEtcwLzTDWMWjEKY8q9p3HPmbgkGqZ8FwfsyPW3Fk wFBAtzMn4iVfiF6Df8dcdIV8qeINmwAAAAFiS0dEAIgFHUgAAAAHdElNRQfiCAkVGRvRqBUNAAAD 9klEQVRIx7WW6X8NVxjHH0stEdROSxIVW4KoGHUrtFJucm96b242RBC5QpRYWlsQQramTSxJqbpV Yg9ZEEFsoYutltZSnPvXmOWc55yZO+PTN/O8Ouf3POf5njnznHkGwA4bPiIiMgqgU+cuIz8Z1VUx OyjRo4liY+CDbmODwXHj7eLEEM1iu/eYEJRt4iRbOHEUQyZDz0+Dqk2xgxPPOFOlsGkaJ/iZDZzp jOOQwj6nnKANnBmMkyCFzaSYWTZzvlApX8624/0kCpyvFMycuVq9OZOizeKTknVTyeVO+drt8Vpk T/WlpadHZCRmQhbn9JoXDM5fEN47PFyOyF5IchaFrItdTMiSVMySuzRPW+xflh8XEiwtX5HAshes 5Jw+nb9Ztbrvh/36DwAoVLUYw8o0VV2jgaIj1xLR1rkNweuJmSVIAwcNHjJ0mBrjpuK3upXfUXWD MonfGJJikxC7eQshFpyPPgZn0dZt2+WoYqbuEA97JxVL5Mkusxy78UhLy4glR96wmikfoJypayXO qWBipRCgt+8pqIqQ93A8dJgo7DYfMT7UfoAfrbJUq6E1e97L2UuH+4T97K9hnFrU6lKt0+xSQneL yvqfjJwDdDgVpErUCylmOCoLk1fyZbVp7oM/rxPyyPfrEJ/9cjgA3h2/6jlH6NAvXxTuyNKq2IFC hIc76bH+th8VuUT34eQovevHdBxWivWy5yg6jquRJ3CeAydxjDf51GkmnQGvYali5VYc51n+OkBo U4T4AC/gEV6MDegPROHwHPeft+BAo7gDKMHZVqjBcRPP40JxejMbtQiX74IVB5biymzepchFCQ6K 7xytBeEZfEvCl9GS461nrrKsS5i6FaAVJ04h0WUm5raxUYPgxr7tMHJ4yyD8BihLebkJ5y85mJhy mI2uCJwiJl4FIweuEaO1KJ8hL5ZIMc9TyuuknY2uZ3I/uzOkNpQj5Rg5Sap+g03reX9DjUi8rm+i uwm18lAOtBsw5Zp8C4Vq9pndhNJtgMU42UbdVXi9lLMO4UCHDnOHZvXyz+Qa9RUF7vKgRoBmPitU uow3nQsHwIwDup7oY2qxIFZHFv0uNJs8eS/SH4K/9s/RfmFaZc7xCSF/8eKqJFam7uWcpfsemHPg PoY8EFqex6pdtml+q/50RVd7foEDF1mMS1Szdprmecj8j0zdl7Stmj0PuOjp1oHONv9tkucx9z/x h7pPGM6oRJfRpZTp02dgMOmCMU2B7omdFQb3bfynCVDF8CMmJf6TYfbDmdzgENL82270e57zat3z YoZ46spHt/4J/F+Ljip+uexV3vGC+3UBM7/U2vTf6zdvO4pKawye+IwUudW+AyQwJXa88WdhAAAA JXRFWHRkYXRlOmNyZWF0ZQAyMDE4LTA4LTA5VDIxOjI1OjI3LTA3OjAwWVHnbgAAACV0RVh0ZGF0 ZTptb2RpZnkAMjAxOC0wOC0wOVQyMToyNToyNy0wNzowMCgMX9IAAAAZdEVYdFNvZnR3YXJlAEFk b2JlIEltYWdlUmVhZHlxyWU8AAAAAElFTkSuQmCC"/>
                            </svg>
                        </a>
                        <ul class="footer-menu">
                            <li class="menu-item">
                                <a href="#">About Us</a>
                            </li>
                            <li class="menu-item">
                                <a href="#">FAQ</a>
                            </li>
                            <li class="menu-item">
                                <a href="#">Use voucher</a>
                            </li>
                            <li class="menu-item">
                                <a href="#">Privacy Ploicy</a>
                            </li>
                            <li class="menu-item">
                                <a href="#">Need help?</a>
                            </li>
                        </ul>            
                        <div class="footer-social-icons">
                            <ul class="social-icons">
                                <li>
                                    <a class="footer-social-icon" target="_blank" href="#">
                                        <span class="fa-stack">
                                            <i class="fas fa-circle fa-stack-2x"></i>
                                            <i class="fab fa-facebook-f fa-stack-1x fa-inverse"></i>
                                        </span>
                                    </a>
                                </li>
                                <li>
                                    <a class="footer-social-icon" target="_blank" href="#">
                                        <span class="fa-stack">
                                            <i class="fas fa-circle fa-stack-2x"></i>
                                            <i class="fab fa-twitter fa-stack-1x fa-inverse"></i>
                                        </span>
                                    </a>
                                </li>                
                            </ul>
                        </div>
                    </div>
                    <div class="site-info">
                        © 2018 Vodi Platform.. All Rights Reserved.        
                    </div> 
                </div>     
            </div>    
        </div> 
        <?php
    }
}