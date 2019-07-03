<?php
/**
 * Register Form
 *
 * This template can be overridden by copying it to yourtheme/masvideos/myaccount/form-register.php.
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

if ( 'yes' === get_option( 'masvideos_enable_myaccount_registration' ) ) :

do_action( 'masvideos_before_user_register_form' ); ?>

<div class="masvideos-register">
    <div class="masvideos-register__inner">

        <h2><?php esc_html_e( 'Register', 'masvideos' ); ?></h2>

        <form method="post" class="masvideos-form masvideos-form-register register" <?php do_action( 'masvideos_register_form_tag' ); ?> >

            <?php do_action( 'masvideos_register_form_start' ); ?>

            <?php if ( 'no' === get_option( 'masvideos_registration_generate_username' ) ) : ?>

                <p class="masvideos-form-row masvideos-form-row--wide form-row form-row-wide">
                    <label for="reg_username"><?php esc_html_e( 'Username', 'masvideos' ); ?>&nbsp;<span class="required">*</span></label>
                    <input type="text" class="masvideos-Input masvideos-Input--text input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
                </p>

            <?php endif; ?>

            <p class="masvideos-form-row masvideos-form-row--wide form-row form-row-wide">
                <label for="reg_email"><?php esc_html_e( 'Email address', 'masvideos' ); ?>&nbsp;<span class="required">*</span></label>
                <input type="email" class="masvideos-Input masvideos-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
            </p>

            <?php if ( 'no' === get_option( 'masvideos_registration_generate_password' ) ) : ?>

                <p class="masvideos-form-row masvideos-form-row--wide form-row form-row-wide">
                    <label for="reg_password"><?php esc_html_e( 'Password', 'masvideos' ); ?>&nbsp;<span class="required">*</span></label>
                    <input type="password" class="masvideos-Input masvideos-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" />
                </p>

            <?php endif; ?>

            <?php do_action( 'masvideos_register_form' ); ?>

            <p class="masvideos-FormRow form-row">
                <?php wp_nonce_field( 'masvideos-register', 'masvideos-register-nonce' ); ?>
                <button type="submit" class="masvideos-Button button" name="register" value="<?php esc_attr_e( 'Register', 'masvideos' ); ?>"><?php esc_html_e( 'Register', 'masvideos' ); ?></button>
            </p>

            <?php do_action( 'masvideos_register_form_end' ); ?>

        </form>
    </div>
</div>

<?php do_action( 'masvideos_after_user_register_form' );

endif; ?>
