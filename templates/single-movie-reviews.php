<?php
/**
 * Display single movie reviews (comments)
 *
 * This template can be overridden by copying it to yourtheme/masvideos/single-movie-reviews.php.
 *
 * HOWEVER, on occasion MasVideos will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @package     MasVideos/Templates
 * @version     1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

global $movie;

if ( ! comments_open() ) {
    return;
}

?>
<div id="reviews" class="masvideos-Reviews">
    
    <div id="review_form_wrapper">
        <div id="review_form">
            <?php
                $commenter = wp_get_current_commenter();

                $comment_form = array(
                    'title_reply'          => have_comments() ? __( 'Add a review', 'masvideos' ) : sprintf( __( 'Be the first to review &ldquo;%s&rdquo;', 'masvideos' ), get_the_title() ),
                    'title_reply_to'       => __( 'Leave a Reply to %s', 'masvideos' ),
                    'title_reply_before'   => '<span id="reply-title" class="comment-reply-title">',
                    'title_reply_after'    => '</span>',
                    'comment_notes_after'  => '',
                    'fields'               => array(
                        'author' => '<p class="comment-form-author">' . '<label for="author">' . esc_html__( 'Name', 'masvideos' ) . '&nbsp;<span class="required">*</span></label> ' .
                                    '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" required /></p>',
                        'email'  => '<p class="comment-form-email"><label for="email">' . esc_html__( 'Email', 'masvideos' ) . '&nbsp;<span class="required">*</span></label> ' .
                                    '<input id="email" name="email" type="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30" required /></p>',
                    ),
                    'label_submit'  => __( 'Submit', 'masvideos' ),
                    'logged_in_as'  => '',
                    'comment_field' => '',
                );

                if ( $account_page_url = wp_login_url() ) {
                    $comment_form['must_log_in'] = '<p class="must-log-in">' . sprintf( __( 'You must be <a href="%s">logged in</a> to post a review.', 'masvideos' ), esc_url( $account_page_url ) ) . '</p>';
                }

                if ( get_option( 'masvideos_movie_review_rating_required' ) === 'yes' ) {
                    $comment_form['comment_field'] = '<div class="comment-form-rating"><label for="rating">' . esc_html__( 'Your rating', 'masvideos' ) . '</label><select name="rating" id="rating" required>
                        <option value="">' . esc_html__( 'Rate&hellip;', 'masvideos' ) . '</option>
                        <option value="10">10</option>
                        <option value="9">9</option>
                        <option value="8">8</option>
                        <option value="7">7</option>
                        <option value="6">6</option>
                        <option value="5">5</option>
                        <option value="4">4</option>
                        <option value="3">3</option>
                        <option value="2">2</option>
                        <option value="1">1</option>
                    </select></div>';
                }

                $comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . esc_html__( 'Your review', 'masvideos' ) . '&nbsp;<span class="required">*</span></label><textarea id="comment" name="comment" cols="45" rows="8" required></textarea></p>';

                comment_form( apply_filters( 'masvideos_movie_review_comment_form_args', $comment_form ) );
            ?>
        </div>
    </div>

    <div id="comments">
        <?php if ( have_comments() ) : 
            $title = apply_filters( 'masvideos_reviews_title', esc_html__( 'Users Reviews', 'masvideos' ), $movie ); ?>
            <?php echo sprintf( '<h2 class="masvideos-reviews__title">%s</h2>', $title ); ?>
            <ol class="commentlist">
                <?php wp_list_comments( apply_filters( 'masvideos_movie_review_list_args', array( 'callback' => 'masvideos_movie_comments' ) ) ); ?>
            </ol>

            <?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
                echo '<nav class="masvideos-pagination">';
                paginate_comments_links( apply_filters( 'masvideos_comment_pagination_args', array(
                    'prev_text' => '&larr;',
                    'next_text' => '&rarr;',
                    'type'      => 'list',
                ) ) );
                echo '</nav>';
            endif; ?>

        <?php else : ?>

            <p class="masvideos-noreviews"><?php _e( 'There are no reviews yet.', 'masvideos' ); ?></p>

        <?php endif; ?>
    </div>

    <div class="clear"></div>
</div>
