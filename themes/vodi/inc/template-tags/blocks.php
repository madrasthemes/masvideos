<?php
/**
 * Template tags used in Header, Footer and Sidebar
 */
if ( ! function_exists( 'vodi_header_right_start' ) ) {
    /**
     * Displays start of header right
     */
    function vodi_header_right_start() {
        ?><div class="site-header__right"><?php 
    }
}

if ( ! function_exists( 'vodi_offcanvas_menu' ) ) {
    /**
     * Displays the offcanvas menu in header
     */
    function vodi_offcanvas_menu() {
        ?><div class="site-header__offcanvas">
            <a href="#" class="site-header__offcanvas--toggler navbar-toggler" data-toggle="offcanvas"><?php vodi_get_template( 'templates/svg/menu-toggle-icon.svg' ); ?></a>
            <div class="offcanvas-drawer">
                <div class="offcanvas-collapse"><?php 
                    wp_nav_menu( array(
                        'theme_location'    => 'offcanvas',
                        'container_class'   => 'site_header__offcanvas-nav',
                        'menu_class'        => 'offcanvas-nav',
                        'fallback_cb'       => false
                    ) );
                ?></div>
            </div>
        </div><?php
    }
}

if ( ! function_exists( 'vodi_header_logo' ) ) {
    /**
     * Displays site logo in header
     */
    function vodi_header_logo() {
        ?><div class="site-header__logo"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php vodi_get_template( 'global/logo-svg.php' ); ?></a></div><?php
    }
}

if ( ! function_exists( 'vodi_header_v4_logo' ) ) {
    /**
     * Displays site logo in header
     */
    function vodi_header_v4_logo() {
        ?><div class="site-header__logo"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php vodi_get_template( 'global/logo-2-svg.php' ); ?></a></div><?php
    }
}

if ( ! function_exists( 'vodi_primary_nav' ) ) {
    /**
     * Displays the primary menu in header
     */
    function vodi_primary_nav() {
        wp_nav_menu( array(
            'theme_location'    => 'primary',
            'container_class'   => 'site_header__primary-nav',
            'menu_class'        => 'nav',
            'fallback_cb'       => false
        ) );
    }
}

if ( ! function_exists( 'vodi_secondary_nav' ) ) {
    /**
     * Displays the secondary menu in header
     */
    function vodi_secondary_nav() {
        wp_nav_menu( array(
            'theme_location'    => 'secondary',
            'container_class'   => 'site_header__secondary-nav',
            'menu_class'        => 'nav',
            'fallback_cb'       => false
        ) );
    }
}

if ( ! function_exists( 'vodi_secondary_nav_v3' ) ) {
    /**
     * Displays the secondary menu in header
     */
    function vodi_secondary_nav_v3() {
        wp_nav_menu( array(
            'theme_location'    => 'secondary-nav-v3',
            'menu_class'        => 'nav',
            'fallback_cb'       => false,
        ) );
    }
}

if ( ! function_exists( 'vodi_navbar_primary' ) ) {
    /**
     * Displays the secondary menu in header
     */
    function vodi_navbar_primary() {
        wp_nav_menu( array(
            'theme_location'    => 'navbar-primary',
            'menu_class'        => 'nav navbar-primary',
            'fallback_cb'       => false,
        ) );
    }
}


if ( ! function_exists( 'vodi_header_right_end' ) ) {
    /**
     * Displays end of header right
     */
    function vodi_header_right_end() {
        ?></div><!-- /.site-header__right --><?php 
    }
}

if ( ! function_exists( 'vodi_header_left_start' ) ) {
    /**
     * Displays start of header left
     */
    function vodi_header_left_start() {
        ?><div class="site-header__left"><?php 
    }
}

if ( ! function_exists( 'vodi_header_icon_start' ) ) {
    /**
     * Displays start of header icon
     */
    function vodi_header_icon_start() {
        ?><div class="site-header__header-icons"><?php
    }
}

if ( ! function_exists( 'vodi_header_icon_end' ) ) {
    /**
     * Displays end of header icon
     */
    function vodi_header_icon_end() {
        ?></div><!-- /.site-header__header-icon --><?php 
    }
}

if ( ! function_exists( 'vodi_header_search' ) ) {
    /**
     * Displays search form in header
     */
    function vodi_header_search() {
        ?><div class="site-header__search"><?php get_search_form(); ?></div><?php
    }
}


if ( ! function_exists( 'vodi_masthead_v3' ) ) {
    function vodi_masthead_v3() {
        ?><div class="masthead">
            <div class="container-fluid">
                <div class="site-header__inner">
                <?php do_action( 'vodi_masthead_v3' ); ?>
                </div>
            </div>
        </div><?php
    }
}

if ( ! function_exists( 'vodi_masthead_v4' ) ) {
    function vodi_masthead_v4() {
        ?><div class="masthead">
            <div class="site-header__inner">
            <?php do_action( 'vodi_masthead_v4' ); ?>
            </div>
        </div><?php
    }
}

if ( ! function_exists( 'vodi_menu_with_search_bar' ) ) {
    function vodi_menu_with_search_bar() {
        ?><div class="masthead-row-2">
            <div class="container">
                <div class="masthead-row-2__inner">
                    <?php do_action( 'vodi_menu_with_search_bar' ); ?>
                </div>
            </div>
        </div><?php
    }
}



if ( ! function_exists( 'vodi_masthead_v3_search' ) ) {
    /**
     * Displays search form in header v3
     */
    function vodi_masthead_v3_search() {
        ?><div class="site-header__search">
            <span class="fas fa-search search-btn">
            </span>
            <?php get_search_form(); ?></div><?php
    }
}

if ( ! function_exists( 'vodi_quick_links' ) ) {
    /**
     * Displays quick links menu
     */
    function vodi_quick_links() {
        $menu_title    = apply_filters( 'vodi_quick_links_title', esc_html__( 'Quick links:', 'vodi' ) );
        if ( has_nav_menu( 'secondary-nav-v3' ) ) {
        ?><div class="container-fluid">
            <div class="site_header__secondary-nav-v3">
            <span class="nav-title"><?php echo wp_kses_post( $menu_title ); ?></span>
            <?php vodi_secondary_nav_v3(); ?>
            </div>
        </div>
            <?php
        }
    }
}

if ( ! function_exists( 'vodi_header_upload_link' ) ) {
    /**
     * Displays a header upload link
     */
    function vodi_header_upload_link() {
        ?><div class="site-header__upload"><a href="#" class="site-header__upload--link"><span class="site-header__upload--icon"><?php vodi_get_template( 'templates/svg/upload-icon.svg' ); ?></span><?php echo esc_html__( 'Upload', 'vodi' ); ?></a></div><?php
    }
}

if ( ! function_exists( 'vodi_header_notification' ) ) {
    /**
     * Displays a notification in header
     */
    function vodi_header_notification() {
        ?><div class="site-header__notification"><a href="#" class="site-header__notification--link"><span class="site-header__notification--icon"><?php vodi_get_template( 'templates/svg/notification-icon.svg' ); ?></span></a></div><?php
    }
}

if ( ! function_exists( 'vodi_header_user_account' ) ) {
    /**
     * Displays user account in header
     */
    function vodi_header_user_account() {
        $register_page_url = ! empty( $custom_userpage ) ?  get_permalink( $custom_userpage ) : wp_registration_url();
        $login_page_url = ! empty( $custom_userpage ) ?  get_permalink( $custom_userpage ) . '#vo-login-tab-content' : wp_login_url( get_permalink() );
        $register_page_url = apply_filters( 'vodi_header_register_page_url', $register_page_url );
        $login_page_url =apply_filters( 'vodi_header_login_page_url', $login_page_url );
        
        if ( is_user_logged_in() ) { ?>
            <div class="site-header__user-account">
                <a href="#" class="site-header__user-account--link"><?php echo esc_html__( 'Sign in', 'vodi' ); ?></a>
            </div>
            <?php
        } else { ?>
            <div class="site-header__user-account">
            <a href="<?php echo esc_url( $register_page_url ); ?>" class="site-header__user-account--link" <?php echo vodi_is_header_register_login_modal_form() ? 'data-toggle="modal" data-target="#modal-register-login"' : ''?>><?php echo esc_html__( 'Sign in', 'vodi' ); ?></a>
            </div>
            <?php
       }
    }
}

if ( ! function_exists( 'vodi_is_header_register_login_modal_form' ) ) {
    function vodi_is_header_register_login_modal_form() {
        return apply_filters( 'vodi_is_header_register_login_modal_form', true );
    }
}

if ( ! function_exists( 'vodi_header_register_login_modal_form' ) ) {
    /**
    * Modal Register/Login Form
    *
    * @return void
    * @since  1.0.0
    */
    function vodi_header_register_login_modal_form() {
        if ( vodi_is_header_register_login_modal_form() && ! is_user_logged_in() ) {
            ?>
            <div class="modal-register-login-wrapper">
                <div class="modal fade modal-register-login" id="modal-register-login" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-body">
                                <?php echo do_shortcode( '[vodi_register_login_form]' ); ?>
                                    <a class="close-button" data-dismiss="modal" aria-label="<?php echo esc_attr__( 'Close', 'vodi' ) ?>"><i class="la la-close"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
    }
}

if ( ! function_exists( 'vodi_register_login_form' ) ) {
    function vodi_register_login_form() {

        $output = '';

        if( ! is_user_logged_in() ) {
            ob_start();
            ?>
            <div class="vodi-register-login-form">
                <div class="vodi-register-login-form-inner">
                    <ul class="nav" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="vo-register-tab" data-toggle="pill" href="#vo-register-tab-content" role="tab" aria-controls="vo-register-tab-content" aria-selected="true"><?php echo esc_html__( 'Register', 'vodi-extensions'); ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="vo-login-tab" data-toggle="pill" href="#vo-login-tab-content" role="tab" aria-controls="vo-login-tab-content" aria-selected="false"><?php echo esc_html__( 'Login', 'vodi-extensions'); ?></a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="vo-register-tab-content" role="tabpanel" aria-labelledby="vo-register-tab"><?php echo vodi_registration_form(); ?></div>
                        <div class="tab-pane fade" id="vo-login-tab-content" role="tabpanel" aria-labelledby="vo-login-tab"><?php echo vodi_login_form(); ?></div>
                    </div>
                </div>
            </div>
            <?php
            $output = ob_get_clean();
        } elseif( function_exists( 'woocommerce_account_content' ) ) {
            ob_start();
            woocommerce_account_content();
            $output = ob_get_clean();
        }

        return $output;
    }
}

add_shortcode( 'vodi_register_login_form', 'vodi_register_login_form' );

if ( ! function_exists( 'vodi_header_left_end' ) ) {
    /**
     * Displays end of header left
     */
    function vodi_header_left_end() {
        ?></div><!-- /.site-header__left --><?php 
    }
}


if ( ! function_exists( 'vodi_sidebar' ) ) {
    function vodi_sidebar() {
        
        $layout = vodi_get_layout();
        
        if ( 'sidebar-right' === $layout || 'sidebar-left' === $layout ) {
            do_action( 'vodi_sidebar' );
        }
    }
}

if ( ! function_exists( 'vodi_get_sidebar' ) ) {
    /**
     * Display vodi sidebar
     * @uses get_sidebar()
     * 
     */
    function vodi_get_sidebar( $name = 'blog' ) {

        if ( empty( $name ) ) {
            $name = 'blog';
        }

        get_sidebar( $name );
    }
}

// if ( ! function_exists( 'vodi_get_sidebar' ) ) {
//     *
//      * Display vodi sidebar
//      * @uses get_sidebar()
//      * 
     
//     function vodi_get_sidebar( $name = null ) {
//         get_sidebar( $name );
//     }
// }

if ( ! function_exists( 'vodi_header_search_menu_start' ) ) {
    /**
     * Displays start of masthead
     */
    function vodi_header_search_menu_start() {
        ?><div class="site-header__search-outer"><?php
    }
}

if ( ! function_exists( 'vodi_header_search_menu_end' ) ) {
    /**
     * Displays end of masthead
     */
    function vodi_header_search_menu_end() {
        ?></div><?php
    }
}

if ( ! function_exists( 'vodi_site_header_comingsoon' ) ) {
    function vodi_site_header_comingsoon() {
        ?><div class="site-header__content">
            <div class="site-header__cs-site-branding-with-nav">
                <a href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" width="103px" height="37px" viewBox="0 0 103 37" enable-background="new 0 0 103 37" xml:space="preserve">  <image id="image0" width="103" height="37" x="0" y="0" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGcAAAAlCAMAAABoDwesAAAABGdBTUEAALGPC/xhBQAAACBjSFJN AAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAACkVBMVEX///+l7f8Lz/8w1v8v 1v+q7v8P0P/4/v9G2/9P3f9L3P9A2v8Y0f8Y0v9b3/862P9q4v/C8/+98v9f3/+/8/8Tz/915P+8 8v+y8P/x/P/7/v/i+f////////////////////8v1v8v1v8v1v////////////8v1v8v1v8v1v// //////8e0v8v1v8v1v////////8b0v8v1v////////////8c0v8v1v////////8c0v////////8c 0v8v1v8c0v8v1v8v1v////8c0v8v1v8v1v////////////////////////////////////////// //////////////////////8d0v8y1/8g0/////////////////////////////////////////// //////////////////////8Y0f8V0f8Mz/////////////////////////////////////////// //////////////////////////////////////////////////////////////////////////// //////////////////////////////////////////////////////////////////////////// //////////////////////////////////////////////////////////////////////////// //////////////////////////////////////////////////////////////////////////// //////////////////////////////////////////////////////////////////////////// //////////////////8v1v+J7W/yAAAA2XRSTlMAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA Kk5KTRkaOgICnUskzyiGJpSCCQq5jkIj6AF3eyLkVyDgXHRd6zMhbXcGBg8JCxQtMg0EAxU4Rz0I EyDKIVn72US6jC6Oz+ulSSyVi4ALFgF5r9yJzmspOtVVDLBGbVYOVB7zmFGgGvFdc9G+GJo3ogX1 daj3rSvvtBEoJMg5B5K1H5RC5rJoTxb96e1hEtcwLzTDWMWjEKY8q9p3HPmbgkGqZ8FwfsyPW3Fk wFBAtzMn4iVfiF6Df8dcdIV8qeINmwAAAAFiS0dEAIgFHUgAAAAHdElNRQfiCAkVGRvRqBUNAAAD 9klEQVRIx7WW6X8NVxjHH0stEdROSxIVW4KoGHUrtFJucm96b242RBC5QpRYWlsQQramTSxJqbpV Yg9ZEEFsoYutltZSnPvXmOWc55yZO+PTN/O8Ouf3POf5njnznHkGwA4bPiIiMgqgU+cuIz8Z1VUx OyjRo4liY+CDbmODwXHj7eLEEM1iu/eYEJRt4iRbOHEUQyZDz0+Dqk2xgxPPOFOlsGkaJ/iZDZzp jOOQwj6nnKANnBmMkyCFzaSYWTZzvlApX8624/0kCpyvFMycuVq9OZOizeKTknVTyeVO+drt8Vpk T/WlpadHZCRmQhbn9JoXDM5fEN47PFyOyF5IchaFrItdTMiSVMySuzRPW+xflh8XEiwtX5HAshes 5Jw+nb9Ztbrvh/36DwAoVLUYw8o0VV2jgaIj1xLR1rkNweuJmSVIAwcNHjJ0mBrjpuK3upXfUXWD MonfGJJikxC7eQshFpyPPgZn0dZt2+WoYqbuEA97JxVL5Mkusxy78UhLy4glR96wmikfoJypayXO qWBipRCgt+8pqIqQ93A8dJgo7DYfMT7UfoAfrbJUq6E1e97L2UuH+4T97K9hnFrU6lKt0+xSQneL yvqfjJwDdDgVpErUCylmOCoLk1fyZbVp7oM/rxPyyPfrEJ/9cjgA3h2/6jlH6NAvXxTuyNKq2IFC hIc76bH+th8VuUT34eQovevHdBxWivWy5yg6jquRJ3CeAydxjDf51GkmnQGvYali5VYc51n+OkBo U4T4AC/gEV6MDegPROHwHPeft+BAo7gDKMHZVqjBcRPP40JxejMbtQiX74IVB5biymzepchFCQ6K 7xytBeEZfEvCl9GS461nrrKsS5i6FaAVJ04h0WUm5raxUYPgxr7tMHJ4yyD8BihLebkJ5y85mJhy mI2uCJwiJl4FIweuEaO1KJ8hL5ZIMc9TyuuknY2uZ3I/uzOkNpQj5Rg5Sap+g03reX9DjUi8rm+i uwm18lAOtBsw5Zp8C4Vq9pndhNJtgMU42UbdVXi9lLMO4UCHDnOHZvXyz+Qa9RUF7vKgRoBmPitU uow3nQsHwIwDup7oY2qxIFZHFv0uNJs8eS/SH4K/9s/RfmFaZc7xCSF/8eKqJFam7uWcpfsemHPg PoY8EFqex6pdtml+q/50RVd7foEDF1mMS1Szdprmecj8j0zdl7Stmj0PuOjp1oHONv9tkucx9z/x h7pPGM6oRJfRpZTp02dgMOmCMU2B7omdFQb3bfynCVDF8CMmJf6TYfbDmdzgENL82270e57zat3z YoZ46spHt/4J/F+Ljip+uexV3vGC+3UBM7/U2vTf6zdvO4pKawye+IwUudW+AyQwJXa88WdhAAAA JXRFWHRkYXRlOmNyZWF0ZQAyMDE4LTA4LTA5VDIxOjI1OjI3LTA3OjAwWVHnbgAAACV0RVh0ZGF0 ZTptb2RpZnkAMjAxOC0wOC0wOVQyMToyNToyNy0wNzowMCgMX9IAAAAZdEVYdFNvZnR3YXJlAEFk b2JlIEltYWdlUmVhZHlxyWU8AAAAAElFTkSuQmCC"/>
                    </svg>
                </a>
                <a class="btn btn-link" data-toggle="collapse" data-target="#vodi-cs-nav" aria-controls="vodi-cs-nav" aria-expanded="true">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30" width="30" height="30" focusable="false">
                    <title>Menu</title> <path stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-miterlimit="10" d="M4 7h22M4 15h22M4 23h22"></path></svg>
                </a>
            </div>  
            <nav class="links collapse" id="vodi-cs-nav">
                <div class="item">
                   <a class="link" href="#">
                        Overview
                    </a>
                </div>
                <div class="item">
                    <a class="link" href="#">
                        Layout
                    </a>
                </div>
                <div class="item active">
                    <a class="link" href="#">
                        Our Database
                    </a>
                </div>
                <div class="item">
                    <a class="link" href="#">
                        Features
                    </a> 
                </div>
                <div class="item">
                    <a class="link" href="#">
                        Page 1
                    </a>
                </div>
                <div class="item">
                    <a class="link" href="#">
                        Page 2
                    </a>
                </div>
                <div class="item">
                    <a class="link" href="#">
                        Page 3
                    </a>
                </div>
                <div class="item">
                    <a class="link" href="#">
                        Page 3
                    </a>
                </div>
                <div class="item">
                    <a class="link" href="#">
                        Page 5
                    </a>
                </div>
                <div class="item">
                    <a class="link" href="#">
                        Page 6
                    </a>
                </div>
            </nav>
        </div>
        <?php
    }
}

if ( ! function_exists( 'vodi_site_header_landing_v1' ) ) {
    function vodi_site_header_landing_v1() {
        ?><div class="site-header__left">
            <div class="site-header__logo">
                <a href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" width="103px" height="37px" viewBox="0 0 103 37" enable-background="new 0 0 103 37" xml:space="preserve">  <image id="image0" width="103" height="37" x="0" y="0" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGcAAAAlCAMAAABoDwesAAAABGdBTUEAALGPC/xhBQAAACBjSFJN AAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAACkVBMVEX///+l7f8Lz/8w1v8v 1v+q7v8P0P/4/v9G2/9P3f9L3P9A2v8Y0f8Y0v9b3/862P9q4v/C8/+98v9f3/+/8/8Tz/915P+8 8v+y8P/x/P/7/v/i+f////////////////////8v1v8v1v8v1v////////////8v1v8v1v8v1v// //////8e0v8v1v8v1v////////8b0v8v1v////////////8c0v8v1v////////8c0v////////8c 0v8v1v8c0v8v1v8v1v////8c0v8v1v8v1v////////////////////////////////////////// //////////////////////8d0v8y1/8g0/////////////////////////////////////////// //////////////////////8Y0f8V0f8Mz/////////////////////////////////////////// //////////////////////////////////////////////////////////////////////////// //////////////////////////////////////////////////////////////////////////// //////////////////////////////////////////////////////////////////////////// //////////////////////////////////////////////////////////////////////////// //////////////////////////////////////////////////////////////////////////// //////////////////8v1v+J7W/yAAAA2XRSTlMAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA Kk5KTRkaOgICnUskzyiGJpSCCQq5jkIj6AF3eyLkVyDgXHRd6zMhbXcGBg8JCxQtMg0EAxU4Rz0I EyDKIVn72US6jC6Oz+ulSSyVi4ALFgF5r9yJzmspOtVVDLBGbVYOVB7zmFGgGvFdc9G+GJo3ogX1 daj3rSvvtBEoJMg5B5K1H5RC5rJoTxb96e1hEtcwLzTDWMWjEKY8q9p3HPmbgkGqZ8FwfsyPW3Fk wFBAtzMn4iVfiF6Df8dcdIV8qeINmwAAAAFiS0dEAIgFHUgAAAAHdElNRQfiCAkVGRvRqBUNAAAD 9klEQVRIx7WW6X8NVxjHH0stEdROSxIVW4KoGHUrtFJucm96b242RBC5QpRYWlsQQramTSxJqbpV Yg9ZEEFsoYutltZSnPvXmOWc55yZO+PTN/O8Ouf3POf5njnznHkGwA4bPiIiMgqgU+cuIz8Z1VUx OyjRo4liY+CDbmODwXHj7eLEEM1iu/eYEJRt4iRbOHEUQyZDz0+Dqk2xgxPPOFOlsGkaJ/iZDZzp jOOQwj6nnKANnBmMkyCFzaSYWTZzvlApX8624/0kCpyvFMycuVq9OZOizeKTknVTyeVO+drt8Vpk T/WlpadHZCRmQhbn9JoXDM5fEN47PFyOyF5IchaFrItdTMiSVMySuzRPW+xflh8XEiwtX5HAshes 5Jw+nb9Ztbrvh/36DwAoVLUYw8o0VV2jgaIj1xLR1rkNweuJmSVIAwcNHjJ0mBrjpuK3upXfUXWD MonfGJJikxC7eQshFpyPPgZn0dZt2+WoYqbuEA97JxVL5Mkusxy78UhLy4glR96wmikfoJypayXO qWBipRCgt+8pqIqQ93A8dJgo7DYfMT7UfoAfrbJUq6E1e97L2UuH+4T97K9hnFrU6lKt0+xSQneL yvqfjJwDdDgVpErUCylmOCoLk1fyZbVp7oM/rxPyyPfrEJ/9cjgA3h2/6jlH6NAvXxTuyNKq2IFC hIc76bH+th8VuUT34eQovevHdBxWivWy5yg6jquRJ3CeAydxjDf51GkmnQGvYali5VYc51n+OkBo U4T4AC/gEV6MDegPROHwHPeft+BAo7gDKMHZVqjBcRPP40JxejMbtQiX74IVB5biymzepchFCQ6K 7xytBeEZfEvCl9GS461nrrKsS5i6FaAVJ04h0WUm5raxUYPgxr7tMHJ4yyD8BihLebkJ5y85mJhy mI2uCJwiJl4FIweuEaO1KJ8hL5ZIMc9TyuuknY2uZ3I/uzOkNpQj5Rg5Sap+g03reX9DjUi8rm+i uwm18lAOtBsw5Zp8C4Vq9pndhNJtgMU42UbdVXi9lLMO4UCHDnOHZvXyz+Qa9RUF7vKgRoBmPitU uow3nQsHwIwDup7oY2qxIFZHFv0uNJs8eS/SH4K/9s/RfmFaZc7xCSF/8eKqJFam7uWcpfsemHPg PoY8EFqex6pdtml+q/50RVd7foEDF1mMS1Szdprmecj8j0zdl7Stmj0PuOjp1oHONv9tkucx9z/x h7pPGM6oRJfRpZTp02dgMOmCMU2B7omdFQb3bfynCVDF8CMmJf6TYfbDmdzgENL82270e57zat3z YoZ46spHt/4J/F+Ljip+uexV3vGC+3UBM7/U2vTf6zdvO4pKawye+IwUudW+AyQwJXa88WdhAAAA JXRFWHRkYXRlOmNyZWF0ZQAyMDE4LTA4LTA5VDIxOjI1OjI3LTA3OjAwWVHnbgAAACV0RVh0ZGF0 ZTptb2RpZnkAMjAxOC0wOC0wOVQyMToyNToyNy0wNzowMCgMX9IAAAAZdEVYdFNvZnR3YXJlAEFk b2JlIEltYWdlUmVhZHlxyWU8AAAAAElFTkSuQmCC"/>
                    </svg>
                </a>
            </div>

            <div class="site-header__nav">
                <ul class="menu"> 
                    <li class="menu-item menu-item-has-children">
                        <a href="#">Overview</a>
                        <ul class="sub-menu"> 
                            <li class="menu-item"><a href="#"> Overview1</a></li>
                            <li class="menu-item"><a href="#"> Overview2</a></li>
                            <li class="menu-item"><a href="#"> Overview3</a></li>
                            <li class="menu-item"><a href="#"> Overview4</a></li>
                        </ul>
                    </li>
                    <li class="menu-item menu-item-has-children">
                        <a href="#">Our Database</a>
                        <ul class="sub-menu"> 
                            <li class="menu-item"><a href="#">Our Database1</a></li>
                            <li class="menu-item"><a href="#">Our Database2</a></li>
                            <li class="menu-item"><a href="#">Our Database3</a></li>
                            <li class="menu-item"><a href="#">Our Database4</a></li>
                        </ul>
                    </li>
                    <li class="menu-item menu-item-has-children">
                        <a href="#">Features</a>
                        <ul class="sub-menu"> 
                            <li class="menu-item"><a href="#"> Features1</a></li>
                            <li class="menu-item"><a href="#"> Features2</a></li>
                            <li class="menu-item"><a href="#"> Features3</a></li>
                            <li class="menu-item"><a href="#"> Features4</a></li>
                        </ul>
                    </li>
                    <li class="menu-item menu-item-has-children">
                        <a href="#">Pricing</a>
                        <ul class="sub-menu"> 
                            <li class="menu-item"><a href="#"> pricing1</a></li>
                            <li class="menu-item"><a href="#"> pricing2</a></li>
                            <li class="menu-item"><a href="#"> pricing3</a></li>
                            <li class="menu-item"><a href="#"> pricing4</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>

        <div class="site-header__right">
            <div class="site-header__user-account">
                <a href="#" class="site-header__user-account--link">Sign in</a>
            </div>
        </div>
        <?php
    }
}

if ( ! function_exists( 'vodi_site_header_landing_v2' ) ) {
    function vodi_site_header_landing_v2() {
        ?><div class="site-header-lp-v2">  
            <div class="site-header-lp-v2__back-option">
                Go back to <a href="#">vodi.tv</a>
            </div>
            <div class="site-header-lp-v2__site-branding">
                <a href="#">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" width="180px" height="29px" viewBox="0 0 180 29" enable-background="new 0 0 180 29" xml:space="preserve">  <image id="image0" width="180" height="29" x="0" y="0" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAALQAAAAdCAYAAAAD3kYUAAAABGdBTUEAALGPC/xhBQAAACBjSFJN AAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAABmJLR0QA/wD/AP+gvaeTAAAL mklEQVR42u2bebBVxRHGf/c94YGACIi4gXFBjFHBqJGIRnGJGnEFcctWGoLGYBSCC8EtpWjUaKIV xS1aJRqN4BpBo9Go4C5GFlGiIIvKIvJQwMf65Y/u4513ONuF0ofkfVWn5sxMd0/PnJ6ZvtNzS5LY APFr4If+/jIwbPBsuPkTOu1YwyXAC8BdacwfroBDW8G92zV0NxpRKTZqaAW+AgwAbgjyRwHN9mrB xTULGCQ4rQSnAccAg4BpSUI2yGm+bqgGrgJaYOP2RUMrlISqhlbgK8DghLJBnZrCRiXqAkM9Fngb 6BcSljBjXryqobux3uHnwG+BM4H2Da1MGjZEg07q0+qlq0EwK1ZeA9xaj7kEC1bCfq0auhvrHd4A RmMLxsyGViYNG6LLsTyjrEmsfBHmolhlCd6ug702hrPW2zWowfAf4MiGViIPDWHQTUk2urVBG8xL qAOWelldEuEywUqxSv6+YCV3L17Nec1LzGlaBRLMXQm7NoNHd4CWG+Le9X+AyKB/DJyFbSUXkvJD KQG9gIHAMk+nJNDshvmr3YFOQFtgLvAhMA54EJhagc69sR90ewNbYi5GHfAu8CywcQJPadNqahau 5O5V0LZtNY8d0oo39m1pK/LMZU5Ugjs6weZNEtutBvYBvg20w1ztacDzwPyCuvcA9gRaAe8DTwIL U2ibB+21xn6ETfIxq8ttyXAI0BVbRCYDY4AVMZpNgZ5AF2AlMB54JkbTBjgOuAf71n18vCdW8N0i tHZZo4DPM/R9G3Nx8vR9Hfj3l7WSjlN9fC5pB0nkPKfF+JZI6hDUd5b0gIrhPknb5LR3pKSJBeXF 8enK1Wp3xcfisVoxtU5VknYv0EckbS7pGkkfp8heKukmSS0yZPSS9FoC7yJJA2K0HSXdKGlBSntz JA3N0bmfpGkJvDNcl4juKkmLE+jGSdopoNvby8d4vs7zWxccw+jZStJc560JyvtLmp6gx8yYvldm 6LujJJD0rwSCqTmK7ZAy2Od4/dFBp4tikaSDUtq7pEJZcXwqqZnLGh6UPy+pfUY/fyabqBFWSpog 6TlJk2JtTFf9CR09lyYM/uuxsuOd9kxvI8IySa+4nnNiPGMlVSe0d3dAs8Tp3o3x9pE0OsjPkfSC 6k/azyS1dZmnetkM7+NCz4fGVuS53flGBmUjgjYXp+jbWzaZ4vp+FJTVSmqLD3ASBmYo9lIKz08k ddK6Ye9YWxesozzJVjtkHzKOB1P6ODSgedF548a/m6R7ArrJsfpfBXV/Uv0VrbukayUNkdRKtgtE GCf7iG0D+hpJR8TGfnSsvZuCusGSWgd1J6n+ZJFsEekrqUnQRjgBh3v5MM/fKWmXoL6LKjPo8c4X 7TDh4nJ+TN8TJa2K6VuboO9lob74QCfhCyVvo0cpHT9StlvwhZK3whDzJG3sbe2ZQztR0pPe0SxE Bn1bSnvxPoZu2OXK/1DhGJ6qsusQYUgOfzhpLynQ3lMB/WFetn9QdnwK38CAZqnSXctRTjPd89Gk PVPSAf4+W1KpgK7R00LmzkpSV0lbBLocm8IzKKBZLGm7FLqRTjMTSdtnGMIdMcYqSR+m0I6SdHGG rGEyH6rkA3lLBu1gb++fKfVLZStYpFdb2WqXhsigb0qomx3rY0tJy73uzxV8sHec5ymV/T1JeiuH b59Al7MLtlWj8iQe4WX/8Pz9GXzbBm0NyKDrHYxNU5X9/wNk/q4kPVHB2ES7WYSWst8JkvT3DJ5w Ucgam0jfT9L8vBA7B4wXZdB1k/mWSTg/RZHhKfTPSdpM5kMmoUeKvLtS6CODvjGhbkZMxhAv/6jC DxaNzTTPP+v5C3P43nK6Gyps7wrnGytbJOZ5/pgMnm5Bvztk0J0eyN5C5QneWdLN/n5thfr2db5Z MtdttuePVTF9t8ig+4XTTI2O7S4F+voRURy3A/thMfyLU45ifglMAH6QUDcd+EMK3wDgFOwYK0Qn 7CixaQLPk9jRVRLOc3lNWHtEofDrK+R711NhR1OdPf9OBs/ewO7+PhM73myZ046wM/edPN8M2JZy OPr9DN5unn6EHZ2mYS9Px2Lfpgl2xLYEO34FO1arBLt6Os7lbe35/xbQd44/adjD00lhYOUE7Jwz jh7eicNJDsRMBG4LhMbxfIYiK4BXgYNj5TVAxxSesRny5gGzgfg9uShMknfnaHvgW/7+NJVhE0+n Yefj0QfL+vCHBe/XVNhehHuwc2KAz8g2kF08nUQ2dvP0Tcrf4RXsu3Tz/IQK9YwWy/HA5v6+MEff aBLktdXV08mhgU4GhgDDEhieJn3VO9HTTVPqN8tRJq1+WUp5mqGDBT/aZdSXcnSJVss6slfWJOzv 6auUd5xFZAepung6H6h1/Ytc9GuCrdKPYzvJ2V7+Tsa4QTGDbkLZoN8FDvD38dhugOs4i8rQw9OX gR39fQrZUeNI3yyDbkrZ8CfHV9wrMdejW6y8RYqwiyhHBycDq1nzctDBQAeSt7iulGdXiM+AR7CL MHEdTwDOpRzqDtGP8koZIrpM05xsRKvIJCq7Htkci54BjKBsEBNYM9IVIjKQZ4CTKmgvjsjoJufQ RXpNyaDZifIYfoS5RWA7cWh81RXo1wvbtcB22Midy3Nbdi9A1wVz8QAmJd1YOL6gkpOBy4P8PJLd gWbYShL3DbcEHkiRPQHb4pI60gbbMVrHyg8GbkyR95CneQYdrkyV4E4s5D4DM5ZeXj4lh2+GpwdW 2F4cRVbebbDfJpBtINGkno1Nxn09/x7wsb+XsLB8Udzs6Q3YoneI57MmYEfKblsWXdT3RcDUJIOe DpxTQMk+CWXDUmj3xIxkKHYX4/fY9tg5hT66oH9VSv33Xd512GXz+zEjT/LxVwfy8lyO73j6iqdF rigNpex29fV0P0/zVqDHPe0AXFagLYCjvc2enm9D2XXJ+vA7e7oqR69oUr+I+czR75H52K64wPMX UwxPY5OpDvtWTSkbYZYeob5ZC8OX7gawPOuY5VWl46IMvpFaN9wSk/fIOsr7aSDr/oT66By6pHK4 vrPsKHOC7PgwqZ+bSfprIOcCL2+ncoTrYOUfZ70VyMg6G+6q+uHfM7y8R1DWKYP/XKeZmKNPFFQZ KLvvIlkYPAp2nRe094Dq38kIn+6qH+LvnqDvthl6nFNQ34edbrhkdznSnq1SDOS1Ah/pWa0dxiTI Kq2DvPjEezCBZr7X7RyUNZOFuyW7t3C9LPq2v6RTfPDCOx7hOXvPoDzrrDd6tpe0IuB5SWbYh8ru tvTXmotE34A/CnTU5rRzn9Pdm0FTJekDpztcdpdFslB82mI3Szb5j/C+91fZyCSLDvb8CvWd7nTn KMegkd1wCzFF9e8XZD1JQYwsXJ0j748VyJor6eQEGacn0N7nddEFnDmeP6NAO69rzQtVURj7xYLj hGwlnFSgvYdVP9AVfvjrctqIVvesm3rVQVvtVQ6V3xWjq5H0WAF9R8qifSHv3yrUN8sbqFJ5NzxI BQwaSbvKLs78Tsm3u7KeHt6B2pQOL5CFbr9XUF43SbeqvIqEWOUGNlgWWk2TcZ3Kl3SekLSpl1/t ZU8FtEfK7jFMlYXbl0l633XunSJ/hOyGXLsKxyqacKNlUcrlsq3+TdkO0V3JH3ScpIcKyN5Dtpr1 LaDDTEmbyHaY92RuRhJtHzfama7vEtkFrb8oOZpbJZvoRfT9rn/nE3Po+nn7O0iiJH0t/29ugx0F bocdCdUCH2D/U/t8LeSVsB9wHbFTlFosQlb0v27bYGfF4Y+NMVjw6AbgNwk8bT39NEd2e4pf+E9D Ux+nOmBxBl1VMJ6VyM77x1C100VHlzVkn29X+/isyNGlChv3RQV1rcLOxZfl0G2EnY2v+roM+puA 2dgxUX9if5xtxDcHjf+cMxQ982zEeo5GgzbsErw3GvQ3GI0GbTjQ01FU5o82Yj1Do0Eb2gGPAic3 tCKNWDf8D2K0oVUy0TZ4AAAAJXRFWHRkYXRlOmNyZWF0ZQAyMDE4LTA4LTE3VDA0OjM1OjM5LTA3 OjAwXy8JOwAAACV0RVh0ZGF0ZTptb2RpZnkAMjAxOC0wOC0xN1QwNDozNTozOS0wNzowMC5ysYcA AAAZdEVYdFNvZnR3YXJlAEFkb2JlIEltYWdlUmVhZHlxyWU8AAAAAElFTkSuQmCC"/></svg>  
                </a>
            </div>
        </div>    
        <?php
    }
}

if ( ! function_exists( 'vodi_live_videos' ) ) {
    function vodi_live_videos() {
        ?>
        <div class="vodi-live-videos">
            <div class="container">
                <div class="vodi-live-videos__inner">
                    <header class="live-video-section__header">
                        <h2 class="live-video-section__title">
                            <a href="#">
                                <span>
                                    <svg 
                                     xmlns="http://www.w3.org/2000/svg"
                                     xmlns:xlink="http://www.w3.org/1999/xlink"
                                     width="16px" height="16px">
                                     <path fill-rule="evenodd"  fill="rgb(36, 186, 239)"
                                     d="M8.000,16.000 C3.582,16.000 -0.000,12.418 -0.000,8.000 C-0.000,3.581 3.582,-0.000 8.000,-0.000 C12.418,-0.000 16.000,3.581 16.000,8.000 C16.000,12.418 12.418,16.000 8.000,16.000 ZM8.000,1.281 C4.289,1.281 1.281,4.289 1.281,8.000 C1.281,11.710 4.289,14.718 8.000,14.718 C11.711,14.718 14.719,11.710 14.719,8.000 C14.719,4.289 11.711,1.281 8.000,1.281 ZM9.947,9.048 C9.386,9.425 8.782,9.814 8.221,10.192 C8.115,10.263 8.001,10.264 7.895,10.335 C7.432,10.647 7.070,11.065 6.331,10.978 C6.232,10.815 6.093,10.771 6.071,10.513 C5.930,10.288 6.005,9.679 6.005,9.334 C6.005,8.405 6.005,7.475 6.005,6.546 C6.005,6.176 5.951,5.627 6.103,5.402 C6.172,5.152 6.335,5.151 6.494,5.009 C6.993,5.001 7.265,5.300 7.569,5.510 C7.731,5.620 7.896,5.651 8.058,5.760 C8.715,6.203 9.422,6.634 10.078,7.082 C10.460,7.344 10.954,7.445 10.990,8.083 C10.921,8.182 10.925,8.336 10.860,8.441 C10.676,8.736 10.232,8.857 9.947,9.048 Z"/>
                                    </svg>
                                    Lives on Twitch
                                </span>
                            </a>
                        </h2>
                    </header>

                    <div class="live-video-list">
                        <?php for( $i=0; $i<3; $i++ ): ?>
                            <div class="live-video">
                                <a href="#" class="live-video__link">
                                    <div class="live-video__poster">
                                        <img src="https://placehold.it/150x90">
                                    </div>

                                    <div class="live-video__info">
                                        <h3 class="live-video__title">DesignInGame:<span>Rocket Bunny</span></h3>
                                        <p class="live-video__duration">Live on:<span>4458</span></p>
                                    </div>
                                </a>
                            </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php
    }
}


