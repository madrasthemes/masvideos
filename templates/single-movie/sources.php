<?php
/**
 * Sources Template
 *
 * This template can be overridden by copying it to yourtheme/masvideos/single-movie/sources.php.
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
    exit; // Exit if accessed directly
}

global $movie;

if ( ! $movie || ! ( $movie->has_sources() ) ) {
    return;
}

$sources = $movie->get_sources();

?>
<table class="movie-sources">
    <thead>
        <tr>
            <th><?php echo esc_html__( 'Links', 'masvideos' ) ?></th>
            <th><?php echo esc_html__( 'Quality', 'masvideos' ) ?></th>
            <th><?php echo esc_html__( 'Language', 'masvideos' ) ?></th>
            <th><?php echo esc_html__( 'Player', 'masvideos' ) ?></th>
            <th><?php echo esc_html__( 'Date Added', 'masvideos' ) ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ( $sources as $key => $source ) : ?>
            <?php
                if( empty( $source['embed_content'] ) && empty( $source['link'] ) ) {
                    continue;
                }
            ?>
            <tr>
                <td>
                    <?php masvideos_template_single_movie_play_source_link( $source ); ?>
                </td>
                <td>
                    <?php if( ! empty( $source['quality'] ) ) {
                        echo wp_kses_post( $source['quality'] );
                    } ?>
                </td>
                <td>
                    <?php if( ! empty( $source['language'] ) ) {
                        echo wp_kses_post( $source['language'] );
                    } ?>
                </td>
                <td>
                    <?php if( ! empty( $source['player'] ) ) {
                        echo wp_kses_post( $source['player'] );
                    } ?>
                </td>
                <td>
                    <?php if( ! empty( $source['date_added'] ) ) {
                        echo wp_kses_post( $source['date_added'] );
                    } ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>