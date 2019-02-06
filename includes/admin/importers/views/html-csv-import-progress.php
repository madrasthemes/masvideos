<?php
/**
 * Admin View: Importer - CSV import progress
 *
 * @package MasVideos\Admin\Importers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="masvideos-progress-form-content masvideos-importer masvideos-importer__importing">
	<header>
		<span class="spinner is-active"></span>
		<h2><?php esc_html_e( 'Importing', 'masvideos' ); ?></h2>
		<p><?php esc_html_e( 'Your posts are now being imported...', 'masvideos' ); ?></p>
	</header>
	<section>
		<progress class="masvideos-importer-progress" max="100" value="0"></progress>
	</section>
</div>
