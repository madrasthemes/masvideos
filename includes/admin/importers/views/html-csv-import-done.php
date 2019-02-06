<?php
/**
 * Admin View: Importer - Done!
 *
 * @package MasVideos\Admin\Importers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="masvideos-progress-form-content masvideos-importer">
	<section class="masvideos-importer-done">
		<?php
		$results = array();

		if ( 0 < $imported ) {
			$results[] = sprintf(
				/* translators: %d: movies count */
				_n( '%s movie imported', '%s movies imported', $imported, 'masvideos' ),
				'<strong>' . number_format_i18n( $imported ) . '</strong>'
			);
		}

		if ( 0 < $updated ) {
			$results[] = sprintf(
				/* translators: %d: movies count */
				_n( '%s movie updated', '%s movies updated', $updated, 'masvideos' ),
				'<strong>' . number_format_i18n( $updated ) . '</strong>'
			);
		}

		if ( 0 < $skipped ) {
			$results[] = sprintf(
				/* translators: %d: movies count */
				_n( '%s movie was skipped', '%s movies were skipped', $skipped, 'masvideos' ),
				'<strong>' . number_format_i18n( $skipped ) . '</strong>'
			);
		}

		if ( 0 < $failed ) {
			$results [] = sprintf(
				/* translators: %d: movies count */
				_n( 'Failed to import %s movie', 'Failed to import %s movies', $failed, 'masvideos' ),
				'<strong>' . number_format_i18n( $failed ) . '</strong>'
			);
		}

		if ( 0 < $failed || 0 < $skipped ) {
			$results[] = '<a href="#" class="masvideos-importer-done-view-errors">' . __( 'View import log', 'masvideos' ) . '</a>';
		}

		/* translators: %d: import results */
		echo wp_kses_post( __( 'Import complete!', 'masvideos' ) . ' ' . implode( '. ', $results ) );
		?>
	</section>
	<section class="masvideos-importer-error-log" style="display:none">
		<table class="widefat masvideos-importer-error-log-table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Product', 'masvideos' ); ?></th>
					<th><?php esc_html_e( 'Reason for failure', 'masvideos' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				if ( count( $errors ) ) {
					foreach ( $errors as $error ) {
						if ( ! is_wp_error( $error ) ) {
							continue;
						}
						$error_data = $error->get_error_data();
						?>
						<tr>
							<th><code><?php echo esc_html( $error_data['row'] ); ?></code></th>
							<td><?php echo esc_html( $error->get_error_message() ); ?></td>
						</tr>
						<?php
					}
				}
				?>
			</tbody>
		</table>
	</section>
	<script type="text/javascript">
		jQuery(function() {
			jQuery( '.masvideos-importer-done-view-errors' ).on( 'click', function() {
				jQuery( '.masvideos-importer-error-log' ).slideToggle();
				return false;
			} );
		} );
	</script>
	<div class="masvideos-actions">
		<a class="button button-primary" href="<?php echo esc_url( admin_url( 'edit.php?post_type=movie' ) ); ?>"><?php esc_html_e( 'View movies', 'masvideos' ); ?></a>
	</div>
</div>
