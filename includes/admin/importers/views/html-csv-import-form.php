<?php
/**
 * Admin View: Import form
 *
 * @package MasVideos/Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<form class="masvideos-progress-form-content masvideos-importer" enctype="multipart/form-data" method="post">
	<header>
		<h2><?php esc_html_e( 'Import posts from a CSV file', 'masvideos' ); ?></h2>
		<p><?php esc_html_e( 'This tool allows you to import (or merge) data to your site from a CSV file.', 'masvideos' ); ?></p>
	</header>
	<section>
		<table class="form-table masvideos-importer-options">
			<tbody>
				<tr>
					<th scope="row">
						<label for="upload">
							<?php esc_html_e( 'Choose a CSV file from your computer:', 'masvideos' ); ?>
						</label>
					</th>
					<td>
						<?php
						if ( ! empty( $upload_dir['error'] ) ) {
							?>
							<div class="inline error">
								<p><?php esc_html_e( 'Before you can upload your import file, you will need to fix the following error:', 'masvideos' ); ?></p>
								<p><strong><?php echo esc_html( $upload_dir['error'] ); ?></strong></p>
							</div>
							<?php
						} else {
							?>
							<input type="file" id="upload" name="import" size="25" />
							<input type="hidden" name="action" value="save" />
							<input type="hidden" name="max_file_size" value="<?php echo esc_attr( $bytes ); ?>" />
							<br>
							<small>
								<?php
								printf(
									/* translators: %s: maximum upload size */
									esc_html__( 'Maximum size: %s', 'masvideos' ),
									esc_html( $size )
								);
								?>
							</small>
							<?php
						}
					?>
					</td>
				</tr>
				<tr>
					<th><label for="masvideos-importer-update-existing"><?php esc_html_e( 'Update existing', 'masvideos' ); ?></label><br/></th>
					<td>
						<input type="hidden" name="update_existing" value="0" />
						<input type="checkbox" id="masvideos-importer-update-existing" name="update_existing" value="1" />
						<label for="masvideos-importer-update-existing"><?php esc_html_e( 'Existing posts that match by ID will be updated. Posts that do not exist will be skipped.', 'masvideos' ); ?></label>
					</td>
				</tr>
				<tr class="masvideos-importer-advanced hidden">
					<th>
						<label for="masvideos-importer-file-url"><?php esc_html_e( 'Alternatively, enter the path to a CSV file on your server:', 'masvideos' ); ?></label>
					</th>
					<td>
						<label for="masvideos-importer-file-url" class="masvideos-importer-file-url-field-wrapper">
							<code><?php echo esc_html( ABSPATH ) . ' (or) ' . esc_html( $_SERVER['DOCUMENT_ROOT'] . '/ ' ) ; ?></code><input type="text" id="masvideos-importer-file-url" name="file_url" />
						</label>
					</td>
				</tr>
				<tr class="masvideos-importer-advanced hidden">
					<th><label><?php esc_html_e( 'CSV Delimiter', 'masvideos' ); ?></label><br/></th>
					<td><input type="text" name="delimiter" placeholder="," size="2" /></td>
				</tr>
				<tr class="masvideos-importer-advanced hidden">
					<th><label><?php esc_html_e( 'Use previous column mapping preferences?', 'masvideos' ); ?></label><br/></th>
					<td><input type="checkbox" id="masvideos-importer-map-preferences" name="map_preferences" value="1" /></td>
				</tr>
			</tbody>
		</table>
	</section>
	<script type="text/javascript">
		jQuery(function() {
			jQuery( '.masvideos-importer-toggle-advanced-options' ).on( 'click', function() {
				var elements = jQuery( '.masvideos-importer-advanced' );
				if ( elements.is( '.hidden' ) ) {
					elements.removeClass( 'hidden' );
					jQuery( this ).text( jQuery( this ).data( 'hidetext' ) );
				} else {
					elements.addClass( 'hidden' );
					jQuery( this ).text( jQuery( this ).data( 'showtext' ) );
				}
				return false;
			} );
		});
	</script>
	<div class="masvideos-actions">
		<a href="#" class="masvideos-importer-toggle-advanced-options" data-hidetext="<?php esc_html_e( 'Hide advanced options', 'masvideos' ); ?>" data-showtext="<?php esc_html_e( 'Hide advanced options', 'masvideos' ); ?>"><?php esc_html_e( 'Show advanced options', 'masvideos' ); ?></a>
		<button type="submit" class="button button-primary button-next" value="<?php esc_attr_e( 'Continue', 'masvideos' ); ?>" name="save_step"><?php esc_html_e( 'Continue', 'masvideos' ); ?></button>
		<?php wp_nonce_field( 'masvideos-csv-importer' ); ?>
	</div>
</form>
