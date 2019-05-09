<?php
/**
 * My Account page
 *
 * This template can be overridden by copying it to yourtheme/masvideos/myaccount/my-account.php.
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

defined( 'ABSPATH' ) || exit;

do_action( 'masvideos_account_navigation' ); ?>

<div class="masvideos-MyAccount-content">
    <?php do_action( 'masvideos_account_content' ); ?>
</div>
