<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/masvideos/myaccount/form-login.php.
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

do_action( 'masvideos_before_user_login_form' ); ?>

<div class="masvideos-login">
    <div class="masvideos-login__inner">

        <h2><?php esc_html_e( 'Login', 'masvideos' ); ?></h2>

        <form class="masvideos-form masvideos-form-login login" method="post">

            <?php do_action( 'masvideos_login_form_start' ); ?>

            <p class="masvideos-form-row masvideos-form-row--wide form-row form-row-wide">
                <label for="username"><?php esc_html_e( 'Username or email address', 'masvideos' ); ?>&nbsp;<span class="required">*</span></label>
                <input type="text" class="masvideos-Input masvideos-Input--text input-text" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
            </p>
            <p class="masvideos-form-row masvideos-form-row--wide form-row form-row-wide">
                <label for="password"><?php esc_html_e( 'Password', 'masvideos' ); ?>&nbsp;<span class="required">*</span></label>
                <input class="masvideos-Input masvideos-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" />
            </p>

            <?php do_action( 'masvideos_login_form' ); ?>

            <p class="form-row">
                <?php wp_nonce_field( 'masvideos-login', 'masvideos-login-nonce' ); ?>
                <button type="submit" class="masvideos-Button button" name="login" value="<?php esc_attr_e( 'Log in', 'masvideos' ); ?>"><?php esc_html_e( 'Log in', 'masvideos' ); ?></button>
                <label class="masvideos-form__label masvideos-form__label-for-checkbox inline">
                    <input class="masvideos-form__input masvideos-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span><?php esc_html_e( 'Remember me', 'masvideos' ); ?></span>
                </label>
            </p>
            <p class="masvideos-LostPassword lost_password">
                <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'masvideos' ); ?></a>
            </p>

            <?php do_action( 'masvideos_login_form_end' ); ?>

        </form>
    </div>
</div>

<?php do_action( 'masvideos_after_user_login_form' ); ?>
