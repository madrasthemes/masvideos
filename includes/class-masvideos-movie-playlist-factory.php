<?php
/**
 * Movie Playlist Factory
 *
 * The MasVideos movie playlist factory creating the right movie playlist object.
 *
 * @package MasVideos/Classes
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Movie Playlist factory class.
 */
class MasVideos_Movie_Playlist_Factory {

    /**
     * Get a movie playlist.
     *
     * @param mixed $movie_playlist_id MasVideos_Movie_Playlist|WP_Post|int|bool $movie_playlist Movie Playlist instance, post instance, numeric or false to use global $post.
     * @return MasVideos_Movie_Playlist|bool Movie Playlist object or null if the movie playlist cannot be loaded.
     */
    public function get_movie_playlist( $movie_playlist_id = false ) {
        $movie_playlist_id = $this->get_movie_playlist_id( $movie_playlist_id );

        if ( ! $movie_playlist_id ) {
            return false;
        }

        $classname = $this->get_movie_playlist_classname( $movie_playlist_id );

        try {
            return new $classname( $movie_playlist_id );
        } catch ( Exception $e ) {
            return false;
        }
    }

    /**
     * Gets a movie playlist classname and allows filtering. Returns MasVideos_Movie_Playlist if the class does not exist.
     *
     * @since  1.0.0
     * @param  int    $movie_playlist_id   Movie Playlist ID.
     * @return string
     */
    public static function get_movie_playlist_classname( $movie_playlist_id ) {
        $classname = apply_filters( 'masvideos_movie_playlist_class', 'MasVideos_Movie_Playlist', $movie_playlist_id );

        if ( ! $classname || ! class_exists( $classname ) ) {
            $classname = 'MasVideos_Movie_Playlist';
        }

        return $classname;
    }

    /**
     * Get the movie playlist ID depending on what was passed.
     *
     * @since  1.0.0
     * @param  MasVideos_Movie_Playlist|WP_Post|int|bool $movie_playlist Movie Playlist instance, post instance, numeric or false to use global $post.
     * @return int|bool false on failure
     */
    private function get_movie_playlist_id( $movie_playlist ) {
        global $post;

        if ( false === $movie_playlist && isset( $post, $post->ID ) && 'movie_playlist' === get_post_type( $post->ID ) ) {
            return absint( $post->ID );
        } elseif ( is_numeric( $movie_playlist ) ) {
            return $movie_playlist;
        } elseif ( $movie_playlist instanceof MasVideos_Movie_Playlist ) {
            return $movie_playlist->get_id();
        } elseif ( ! empty( $movie_playlist->ID ) ) {
            return $movie_playlist->ID;
        } else {
            return false;
        }
    }
}
