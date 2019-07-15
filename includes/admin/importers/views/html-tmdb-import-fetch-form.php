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
        <div class="options_group">
            <div class=" form-field masvideos-tmdb-type_field">
                <label for="masvideos-tmdb-type"><?php esc_html_e( 'Type:', 'masvideos' ); ?></label>
                <?php if( ! empty( $type_options ) )
                    ?><select id="masvideos-tmdb-type" name="masvideos-tmdb-type" class="show_hide_select"><?php
                        foreach ( $type_options as $key => $value ) {
                            ?><option value="<?php echo esc_attr( $key ); ?>">
                                <?php echo esc_html( $value ); ?>
                            </option><?php
                        }
                    ?></select><?php
                ?>
            </div>
            <p class="form-field masvideos-tmdb-page-number_field show_if_now-playing-movies show_if_popular-movies show_if_top-rated-movies show_if_upcoming-movies show_if_discover-movies hide">
                <label for="masvideos-tmdb-page-number"><?php esc_html_e( 'Page Number', 'masvideos' ) ?> : </label>
                <input type="text" class="short" name="masvideos-tmdb-page-number" id="masvideos-tmdb-page-number" value="1" placeholder="" style="width: auto;">
            </p>
            <p class="form-field masvideos-tmdb-movie-id_field show_if_movie-by-id hide" style="display: none;">
                <label for="masvideos-tmdb-movie-id"><?php esc_html_e( 'Movie ID', 'masvideos' ) ?> : </label>
                <input type="text" class="short" name="masvideos-tmdb-movie-id" id="masvideos-tmdb-movie-id" value="" placeholder="" style="width: auto;"> 
            </p>
            <p class="form-field masvideos-tmdb-search-movie_field show_if_search-movie hide" style="display: none;">
                <label for="masvideos-tmdb-search-movie"><?php esc_html_e( 'Movie title', 'masvideos' ) ?> : </label>
                <input type="text" class="short" name="masvideos-tmdb-search-movie" id="masvideos-tmdb-search-movie" value="" placeholder="" style="width: auto;"> 
            </p>
        </div>
    </section>
    <div class="masvideos-actions">
        <button type="submit" class="button button-primary button-next" value="<?php esc_attr_e( 'Continue', 'masvideos' ); ?>" name="save_step"><?php esc_html_e( 'Continue', 'masvideos' ); ?></button>
        <?php wp_nonce_field( 'masvideos-tmdb-fetch-data' ); ?>
    </div>
</form>
