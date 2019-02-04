<?php
/**
 * Comments
 *
 * Handle comments (reviews and order notes).
 *
 * @package MasVideos/Classes
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Comments class.
 */
class MasVideos_Comments {

    /**
     * Hook in methods.
     */
    public static function init() {
        // Rating posts.
        add_filter( 'comments_open', array( __CLASS__, 'comments_open' ), 10, 2 );
        add_filter( 'preprocess_comment', array( __CLASS__, 'check_comment_rating' ), 0 );
        add_action( 'comment_post', array( __CLASS__, 'add_comment_rating' ), 1 );
        add_action( 'comment_moderation_recipients', array( __CLASS__, 'comment_moderation_recipients' ), 10, 2 );

        // Clear transients.
        add_action( 'wp_update_comment_count', array( __CLASS__, 'clear_transients' ) );

        // Count comments.
        add_filter( 'wp_count_comments', array( __CLASS__, 'wp_count_comments' ), 10, 2 );

        // Delete comments count cache whenever there is a new comment or a comment status changes.
        add_action( 'wp_insert_comment', array( __CLASS__, 'delete_comments_count_cache' ) );
        add_action( 'wp_set_comment_status', array( __CLASS__, 'delete_comments_count_cache' ) );

        // Support avatars for `review` comment type.
        add_filter( 'get_avatar_comment_types', array( __CLASS__, 'add_avatar_for_review_comment_type' ) );
    }

    /**
     * See if comments are open.
     *
     * @since  1.0.0
     * @param  bool $open    Whether the current post is open for comments.
     * @param  int  $post_id Post ID.
     * @return bool
     */
    public static function comments_open( $open, $post_id ) {
        if ( 'episode' === get_post_type( $post_id ) && ! post_type_supports( 'episode', 'comments' ) ) {
            $open = false;
        }
        if ( 'tv_show' === get_post_type( $post_id ) && ! post_type_supports( 'tv_show', 'comments' ) ) {
            $open = false;
        }
        if ( 'video' === get_post_type( $post_id ) && ! post_type_supports( 'video', 'comments' ) ) {
            $open = false;
        }
        if ( 'movie' === get_post_type( $post_id ) && ! post_type_supports( 'movie', 'comments' ) ) {
            $open = false;
        }
        return $open;
    }

    /**
     * Validate the comment ratings.
     *
     * @param  array $comment_data Comment data.
     * @return array
     */
    public static function check_comment_rating( $comment_data ) {
        // If posting a comment (not trackback etc) and not logged in.
        if ( ! is_admin() && isset( $_POST['comment_post_ID'], $_POST['rating'], $comment_data['comment_type'] ) && 'episode' === get_post_type( absint( $_POST['comment_post_ID'] ) ) && empty( $_POST['rating'] ) && '' === $comment_data['comment_type'] && 'yes' === get_option( 'masvideos_enable_review_rating' ) && 'yes' === get_option( 'masvideos_episode_review_rating_required' ) ) { // WPCS: input var ok, CSRF ok.
            wp_die( esc_html__( 'Please rate the episode.', 'masvideos' ) );
            exit;
        }

        // If posting a comment (not trackback etc) and not logged in.
        if ( ! is_admin() && isset( $_POST['comment_post_ID'], $_POST['rating'], $comment_data['comment_type'] ) && 'tv_show' === get_post_type( absint( $_POST['comment_post_ID'] ) ) && empty( $_POST['rating'] ) && '' === $comment_data['comment_type'] && 'yes' === get_option( 'masvideos_enable_review_rating' ) && 'yes' === get_option( 'masvideos_tv_show_review_rating_required' ) ) { // WPCS: input var ok, CSRF ok.
            wp_die( esc_html__( 'Please rate the tv show.', 'masvideos' ) );
            exit;
        }

        // If posting a comment (not trackback etc) and not logged in.
        if ( ! is_admin() && isset( $_POST['comment_post_ID'], $_POST['rating'], $comment_data['comment_type'] ) && 'video' === get_post_type( absint( $_POST['comment_post_ID'] ) ) && empty( $_POST['rating'] ) && '' === $comment_data['comment_type'] && 'yes' === get_option( 'masvideos_enable_review_rating' ) && 'yes' === get_option( 'masvideos_video_review_rating_required' ) ) { // WPCS: input var ok, CSRF ok.
            wp_die( esc_html__( 'Please rate the video.', 'masvideos' ) );
            exit;
        }

        // If posting a comment (not trackback etc) and not logged in.
        if ( ! is_admin() && isset( $_POST['comment_post_ID'], $_POST['rating'], $comment_data['comment_type'] ) && 'movie' === get_post_type( absint( $_POST['comment_post_ID'] ) ) && empty( $_POST['rating'] ) && '' === $comment_data['comment_type'] && 'yes' === get_option( 'masvideos_enable_review_rating' ) && 'yes' === get_option( 'masvideos_movie_review_rating_required' ) ) { // WPCS: input var ok, CSRF ok.
            wp_die( esc_html__( 'Please rate the movie.', 'masvideos' ) );
            exit;
        }
        return $comment_data;
    }

    /**
     * Rating field for comments.
     *
     * @param int $comment_id Comment ID.
     */
    public static function add_comment_rating( $comment_id ) {
        if ( isset( $_POST['rating'], $_POST['comment_post_ID'] ) && in_array( get_post_type( absint( $_POST['comment_post_ID'] ) ), array( 'episode', 'tv_show', 'video', 'movie' ) ) ) { // WPCS: input var ok, CSRF ok.
            if ( ! $_POST['rating'] || $_POST['rating'] > 10 || $_POST['rating'] < 0 ) { // WPCS: input var ok, CSRF ok, sanitization ok.
                return;
            }
            add_comment_meta( $comment_id, 'rating', intval( $_POST['rating'] ), true ); // WPCS: input var ok, CSRF ok.

            $post_id = isset( $_POST['comment_post_ID'] ) ? absint( $_POST['comment_post_ID'] ) : 0; // WPCS: input var ok, CSRF ok.
            if ( $post_id ) {
                self::clear_transients( $post_id );
            }
        }
    }

    /**
     * Modify recipient of review email.
     *
     * @param array $emails     Emails.
     * @param int   $comment_id Comment ID.
     * @return array
     */
    public static function comment_moderation_recipients( $emails, $comment_id ) {
        $comment = get_comment( $comment_id );

        if( $comment && in_array( get_post_type( $comment->comment_post_ID ), array( 'episode', 'tv_show', 'video', 'movie' ) ) ) {
            $emails = array( get_option( 'admin_email' ) );
        }

        return $emails;
    }

    /**
     * Ensure movie average rating and review count is kept up to date.
     *
     * @param int $post_id Post ID.
     */
    public static function clear_transients( $post_id ) {

        if ( 'tv_show' === get_post_type( $post_id ) ) {
            $tv_show = masvideos_get_tv_show( $post_id );
            self::get_rating_counts_for_tv_show( $tv_show );
            self::get_average_rating_for_tv_show( $tv_show );
            self::get_review_count_for_tv_show( $tv_show );
        }

        if ( 'episode' === get_post_type( $post_id ) ) {
            $episode = masvideos_get_episode( $post_id );
            self::get_rating_counts_for_episode( $episode );
            self::get_average_rating_for_episode( $episode );
            self::get_review_count_for_episode( $episode );
        }

        if ( 'video' === get_post_type( $post_id ) ) {
            $video = masvideos_get_video( $post_id );
            self::get_rating_counts_for_video( $video );
            self::get_average_rating_for_video( $video );
            self::get_review_count_for_video( $video );
        }

        if ( 'movie' === get_post_type( $post_id ) ) {
            $movie = masvideos_get_movie( $post_id );
            self::get_rating_counts_for_movie( $movie );
            self::get_average_rating_for_movie( $movie );
            self::get_review_count_for_movie( $movie );
        }
    }

    /**
     * Delete comments count cache whenever there is
     * new comment or the status of a comment changes. Cache
     * will be regenerated next time MasVideos_Comments::wp_count_comments()
     * is called.
     */
    public static function delete_comments_count_cache() {
        delete_transient( 'masvideos_count_comments' );
    }

    /**
     * Remove order notes and webhook delivery logs from wp_count_comments().
     *
     * @since  1.0.0
     * @param  object $stats   Comment stats.
     * @param  int    $post_id Post ID.
     * @return object
     */
    public static function wp_count_comments( $stats, $post_id ) {
        global $wpdb;

        if ( 0 === $post_id ) {
            $stats = get_transient( 'masvideos_count_comments' );

            if ( ! $stats ) {
                $stats = array(
                    'total_comments' => 0,
                    'all'            => 0,
                );

                $count = $wpdb->get_results(
                    "
                    SELECT comment_approved, COUNT(*) AS num_comments
                    FROM {$wpdb->comments}
                    WHERE comment_type NOT IN ('order_note', 'webhook_delivery')
                    GROUP BY comment_approved
                ", ARRAY_A
                );

                $approved = array(
                    '0'            => 'moderated',
                    '1'            => 'approved',
                    'spam'         => 'spam',
                    'trash'        => 'trash',
                    'post-trashed' => 'post-trashed',
                );

                foreach ( (array) $count as $row ) {
                    // Don't count post-trashed toward totals.
                    if ( ! in_array( $row['comment_approved'], array( 'post-trashed', 'trash', 'spam' ), true ) ) {
                        $stats['all']            += $row['num_comments'];
                        $stats['total_comments'] += $row['num_comments'];
                    } elseif ( ! in_array( $row['comment_approved'], array( 'post-trashed', 'trash' ), true ) ) {
                        $stats['total_comments'] += $row['num_comments'];
                    }
                    if ( isset( $approved[ $row['comment_approved'] ] ) ) {
                        $stats[ $approved[ $row['comment_approved'] ] ] = $row['num_comments'];
                    }
                }

                foreach ( $approved as $key ) {
                    if ( empty( $stats[ $key ] ) ) {
                        $stats[ $key ] = 0;
                    }
                }

                $stats = (object) $stats;
                set_transient( 'masvideos_count_comments', $stats );
            }
        }

        return $stats;
    }

    /**
     * Make sure WP displays avatars for comments with the `review` type.
     *
     * @since  1.0.0
     * @param  array $comment_types Comment types.
     * @return array
     */
    public static function add_avatar_for_review_comment_type( $comment_types ) {
        return array_merge( $comment_types, array( 'review' ) );
    }

    /**
     * Get tv show rating for a tv show. Please note this is not cached.
     *
     * @since 1.0.0
     * @param MasVideos_Movie $tv_show Movie instance.
     * @return float
     */
    public static function get_average_rating_for_tv_show( &$tv_show ) {
        global $wpdb;

        $count = $tv_show->get_rating_count();

        if ( $count ) {
            $ratings = $wpdb->get_var(
                $wpdb->prepare(
                    "
                SELECT SUM(meta_value) FROM $wpdb->commentmeta
                LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
                WHERE meta_key = 'rating'
                AND comment_post_ID = %d
                AND comment_approved = '1'
                AND meta_value > 0
            ", $tv_show->get_id()
                )
            );
            $average = number_format( $ratings / $count, 2, '.', '' );
        } else {
            $average = 0;
        }

        $tv_show->set_average_rating( $average );

        $data_store = $tv_show->get_data_store();
        $data_store->update_average_rating( $tv_show );

        return $average;
    }

    /**
     * Get tv show review count for a tv show (not replies). Please note this is not cached.
     *
     * @since 1.0.0
     * @param MasVideos_Movie $tv_show Movie instance.
     * @return int
     */
    public static function get_review_count_for_tv_show( &$tv_show ) {
        global $wpdb;

        $count = $wpdb->get_var(
            $wpdb->prepare(
                "
            SELECT COUNT(*) FROM $wpdb->comments
            WHERE comment_parent = 0
            AND comment_post_ID = %d
            AND comment_approved = '1'
        ", $tv_show->get_id()
            )
        );

        $tv_show->set_review_count( $count );

        $data_store = $tv_show->get_data_store();
        $data_store->update_review_count( $tv_show );

        return $count;
    }

    /**
     * Get tv show rating count for a tv show. Please note this is not cached.
     *
     * @since 1.0.0
     * @param MasVideos_Movie $tv_show Movie instance.
     * @return int[]
     */
    public static function get_rating_counts_for_tv_show( &$tv_show ) {
        global $wpdb;

        $counts     = array();
        $raw_counts = $wpdb->get_results(
            $wpdb->prepare(
                "
            SELECT meta_value, COUNT( * ) as meta_value_count FROM $wpdb->commentmeta
            LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
            WHERE meta_key = 'rating'
            AND comment_post_ID = %d
            AND comment_approved = '1'
            AND meta_value > 0
            GROUP BY meta_value
        ", $tv_show->get_id()
            )
        );

        foreach ( $raw_counts as $count ) {
            $counts[ $count->meta_value ] = absint( $count->meta_value_count ); // WPCS: slow query ok.
        }

        $tv_show->set_rating_counts( $counts );

        $data_store = $tv_show->get_data_store();
        $data_store->update_rating_counts( $tv_show );

        return $counts;
    }

    /**
     * Get episode rating for a episode. Please note this is not cached.
     *
     * @since 1.0.0
     * @param MasVideos_Movie $episode Movie instance.
     * @return float
     */
    public static function get_average_rating_for_episode( &$episode ) {
        global $wpdb;

        $count = $episode->get_rating_count();

        if ( $count ) {
            $ratings = $wpdb->get_var(
                $wpdb->prepare(
                    "
                SELECT SUM(meta_value) FROM $wpdb->commentmeta
                LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
                WHERE meta_key = 'rating'
                AND comment_post_ID = %d
                AND comment_approved = '1'
                AND meta_value > 0
            ", $episode->get_id()
                )
            );
            $average = number_format( $ratings / $count, 2, '.', '' );
        } else {
            $average = 0;
        }

        $episode->set_average_rating( $average );

        $data_store = $episode->get_data_store();
        $data_store->update_average_rating( $episode );

        return $average;
    }

    /**
     * Get episode review count for a episode (not replies). Please note this is not cached.
     *
     * @since 1.0.0
     * @param MasVideos_Movie $episode Movie instance.
     * @return int
     */
    public static function get_review_count_for_episode( &$episode ) {
        global $wpdb;

        $count = $wpdb->get_var(
            $wpdb->prepare(
                "
            SELECT COUNT(*) FROM $wpdb->comments
            WHERE comment_parent = 0
            AND comment_post_ID = %d
            AND comment_approved = '1'
        ", $episode->get_id()
            )
        );

        $episode->set_review_count( $count );

        $data_store = $episode->get_data_store();
        $data_store->update_review_count( $episode );

        return $count;
    }

    /**
     * Get episode rating count for a episode. Please note this is not cached.
     *
     * @since 1.0.0
     * @param MasVideos_Movie $episode Movie instance.
     * @return int[]
     */
    public static function get_rating_counts_for_episode( &$episode ) {
        global $wpdb;

        $counts     = array();
        $raw_counts = $wpdb->get_results(
            $wpdb->prepare(
                "
            SELECT meta_value, COUNT( * ) as meta_value_count FROM $wpdb->commentmeta
            LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
            WHERE meta_key = 'rating'
            AND comment_post_ID = %d
            AND comment_approved = '1'
            AND meta_value > 0
            GROUP BY meta_value
        ", $episode->get_id()
            )
        );

        foreach ( $raw_counts as $count ) {
            $counts[ $count->meta_value ] = absint( $count->meta_value_count ); // WPCS: slow query ok.
        }

        $episode->set_rating_counts( $counts );

        $data_store = $episode->get_data_store();
        $data_store->update_rating_counts( $episode );

        return $counts;
    }

    /**
     * Get video rating for a video. Please note this is not cached.
     *
     * @since 1.0.0
     * @param MasVideos_Video $video Video instance.
     * @return float
     */
    public static function get_average_rating_for_video( &$video ) {
        global $wpdb;

        $count = $video->get_rating_count();

        if ( $count ) {
            $ratings = $wpdb->get_var(
                $wpdb->prepare(
                    "
                SELECT SUM(meta_value) FROM $wpdb->commentmeta
                LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
                WHERE meta_key = 'rating'
                AND comment_post_ID = %d
                AND comment_approved = '1'
                AND meta_value > 0
            ", $video->get_id()
                )
            );
            $average = number_format( $ratings / $count, 2, '.', '' );
        } else {
            $average = 0;
        }

        $video->set_average_rating( $average );

        $data_store = $video->get_data_store();
        $data_store->update_average_rating( $video );

        return $average;
    }

    /**
     * Get video review count for a video (not replies). Please note this is not cached.
     *
     * @since 1.0.0
     * @param MasVideos_Video $video Video instance.
     * @return int
     */
    public static function get_review_count_for_video( &$video ) {
        global $wpdb;

        $count = $wpdb->get_var(
            $wpdb->prepare(
                "
            SELECT COUNT(*) FROM $wpdb->comments
            WHERE comment_parent = 0
            AND comment_post_ID = %d
            AND comment_approved = '1'
        ", $video->get_id()
            )
        );

        $video->set_review_count( $count );

        $data_store = $video->get_data_store();
        $data_store->update_review_count( $video );

        return $count;
    }

    /**
     * Get video rating count for a video. Please note this is not cached.
     *
     * @since 1.0.0
     * @param MasVideos_Video $video Video instance.
     * @return int[]
     */
    public static function get_rating_counts_for_video( &$video ) {
        global $wpdb;

        $counts     = array();
        $raw_counts = $wpdb->get_results(
            $wpdb->prepare(
                "
            SELECT meta_value, COUNT( * ) as meta_value_count FROM $wpdb->commentmeta
            LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
            WHERE meta_key = 'rating'
            AND comment_post_ID = %d
            AND comment_approved = '1'
            AND meta_value > 0
            GROUP BY meta_value
        ", $video->get_id()
            )
        );

        foreach ( $raw_counts as $count ) {
            $counts[ $count->meta_value ] = absint( $count->meta_value_count ); // WPCS: slow query ok.
        }

        $video->set_rating_counts( $counts );

        $data_store = $video->get_data_store();
        $data_store->update_rating_counts( $video );

        return $counts;
    }

    /**
     * Get movie rating for a movie. Please note this is not cached.
     *
     * @since 1.0.0
     * @param MasVideos_Movie $movie Movie instance.
     * @return float
     */
    public static function get_average_rating_for_movie( &$movie ) {
        global $wpdb;

        $count = $movie->get_rating_count();

        if ( $count ) {
            $ratings = $wpdb->get_var(
                $wpdb->prepare(
                    "
                SELECT SUM(meta_value) FROM $wpdb->commentmeta
                LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
                WHERE meta_key = 'rating'
                AND comment_post_ID = %d
                AND comment_approved = '1'
                AND meta_value > 0
            ", $movie->get_id()
                )
            );
            $average = number_format( $ratings / $count, 2, '.', '' );
        } else {
            $average = 0;
        }

        $movie->set_average_rating( $average );

        $data_store = $movie->get_data_store();
        $data_store->update_average_rating( $movie );

        return $average;
    }

    /**
     * Get movie review count for a movie (not replies). Please note this is not cached.
     *
     * @since 1.0.0
     * @param MasVideos_Movie $movie Movie instance.
     * @return int
     */
    public static function get_review_count_for_movie( &$movie ) {
        global $wpdb;

        $count = $wpdb->get_var(
            $wpdb->prepare(
                "
            SELECT COUNT(*) FROM $wpdb->comments
            WHERE comment_parent = 0
            AND comment_post_ID = %d
            AND comment_approved = '1'
        ", $movie->get_id()
            )
        );

        $movie->set_review_count( $count );

        $data_store = $movie->get_data_store();
        $data_store->update_review_count( $movie );

        return $count;
    }

    /**
     * Get movie rating count for a movie. Please note this is not cached.
     *
     * @since 1.0.0
     * @param MasVideos_Movie $movie Movie instance.
     * @return int[]
     */
    public static function get_rating_counts_for_movie( &$movie ) {
        global $wpdb;

        $counts     = array();
        $raw_counts = $wpdb->get_results(
            $wpdb->prepare(
                "
            SELECT meta_value, COUNT( * ) as meta_value_count FROM $wpdb->commentmeta
            LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
            WHERE meta_key = 'rating'
            AND comment_post_ID = %d
            AND comment_approved = '1'
            AND meta_value > 0
            GROUP BY meta_value
        ", $movie->get_id()
            )
        );

        foreach ( $raw_counts as $count ) {
            $counts[ $count->meta_value ] = absint( $count->meta_value_count ); // WPCS: slow query ok.
        }

        $movie->set_rating_counts( $counts );

        $data_store = $movie->get_data_store();
        $data_store->update_rating_counts( $movie );

        return $counts;
    }
}

MasVideos_Comments::init();
