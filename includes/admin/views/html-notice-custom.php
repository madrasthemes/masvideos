<?php
/**
 * Admin View: Custom Notices
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="message" class="updated masvideos-message">
	<a class="masvideos-message-close notice-dismiss" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'masvideos-hide-notice', $notice ), 'masvideos_hide_notices_nonce', '_masvideos_notice_nonce' ) ); ?>"><?php _e( 'Dismiss', 'masvideos' ); ?></a>
	<?php echo wp_kses_post( wpautop( $notice_html ) ); ?>
</div>
