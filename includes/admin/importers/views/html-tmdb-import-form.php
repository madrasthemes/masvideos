<?php
/**
 * Admin View: Fetch TMDB data
 *
 * @package MasVideos/Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<form class="masvideos-tmdb-form-content masvideos-tmdb-fetch-data" enctype="multipart/form-data" method="post">
    <header>
        <h2><?php esc_html_e( 'Fetch TMDB API', 'masvideos' ); ?></h2>
        <p><?php esc_html_e( 'This tool allows you to fetch data from TMDB.', 'masvideos' ); ?></p>
    </header>
    <section>
        <label for="masvideos-tmdb-api-key"><?php esc_html_e( 'Enter your API Key:', 'masvideos' ); ?></label>
        <input type="text" id="masvideos-tmdb-api-key" name="api_key" />
        <select name="type">
            <option value="now-playing-movies"><?php esc_html_e( 'Now Playing Movies', 'masvideos' ); ?></option>
            <option value="popular-movies"><?php esc_html_e( 'Popular Movies', 'masvideos' ); ?></option>
            <option value="top-rated-movies"><?php esc_html_e( 'Top Rated Movies', 'masvideos' ); ?></option>
            <option value="upcoming-movies"><?php esc_html_e( 'Upcoming Movies', 'masvideos' ); ?></option>
        </select>
    </section>
    <div class="masvideos-actions">
        <button type="submit" class="button button-primary button-next" value="<?php esc_attr_e( 'Continue', 'masvideos' ); ?>" name="save_step"><?php esc_html_e( 'Continue', 'masvideos' ); ?></button>
        <?php wp_nonce_field( 'masvideos-tmdb-fetch-data' ); ?>
    </div>
</form>
