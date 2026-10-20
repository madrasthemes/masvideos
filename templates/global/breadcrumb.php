<?php
/**
 * MasVideos breadcrumb
 *
 *
 * @package MasVideos/Templates
 * @version 1.0.0
 * @see 	masvideos_breadcrumb()
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! empty( $breadcrumb ) ) {

	echo $wrap_before; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Wrapper markup supplied by masvideos_breadcrumb() args/filters (trusted template markup).

	foreach ( $breadcrumb as $key => $crumb ) {

		echo $before; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Item-before markup supplied by masvideos_breadcrumb() args/filters (trusted template markup).

		if ( ! empty( $crumb[1] ) && sizeof( $breadcrumb ) !== $key + 1 ) {
			echo '<a href="' . esc_url( $crumb[1] ) . '">' . esc_html( $crumb[0] ) . '</a>';
		} else {
			echo esc_html( $crumb[0] );
		}

		echo $after; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Item-after markup supplied by masvideos_breadcrumb() args/filters (trusted template markup).

		if ( sizeof( $breadcrumb ) !== $key + 1 ) {
			echo $delimiter; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Delimiter markup supplied by masvideos_breadcrumb() args/filters (trusted template markup).
		}
	}

	echo $wrap_after; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Wrapper markup supplied by masvideos_breadcrumb() args/filters (trusted template markup).

}
