<?php
/**
 * Admin View: Notice - Install
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="message" class="updated masvideos-message masvideos-connect">
	<p><?php _e( '<strong>Welcome to MAS Videos</strong> &#8211; You&lsquo;re almost ready :)', 'masvideos' ); ?></p>
	<p class="submit"><a href="<?php echo esc_url( add_query_arg( 'masvideos-setup', 'true', admin_url( 'admin.php' ) ) ); ?>" class="button-primary"><?php esc_html_e( 'Create Pages', 'masvideos' ); ?></a> <a class="button-secondary skip" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'masvideos-hide-notice', 'install' ), 'masvideos_hide_notices_nonce', '_masvideos_notice_nonce' ) ); ?>"><?php esc_html_e( 'Skip', 'masvideos' ); ?></a></p>
</div>
