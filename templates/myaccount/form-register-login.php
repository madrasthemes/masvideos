<?php
/**
 * Register/Login Form
 *
 * This template can be overridden by copying it to yourtheme/masvideos/myaccount/form-register-login.php.
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

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

do_action( 'masvideos_before_user_register_login_form' );

echo '<div class="masvideos-register-login">';

masvideos_get_template( 'myaccount/form-register.php' );

masvideos_get_template( 'myaccount/form-login.php' );

echo '</div>';

do_action( 'masvideos_after_user_register_login_form' ); ?>
