<?php
/**
 * The template to display the reviewers meta data (name, verified owner, review date)
 *
 * This template can be overridden by copying it to yourtheme/masvideos/single-tv-show/review-meta.php.
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

defined( 'ABSPATH' ) || exit;

global $comment;

if ( '0' === $comment->comment_approved ) { ?>

	<p class="meta">
		<em class="masvideos-review__awaiting-approval">
			<?php esc_html_e( 'Your review is awaiting approval', 'masvideos' ); ?>
		</em>
	</p>

<?php } else { ?>

	<p class="meta">
		<strong class="masvideos-review__author"><?php comment_author(); ?> </strong>
		<time class="masvideos-review__published-date" datetime="<?php echo esc_attr( get_comment_date( 'c' ) ); ?>"><?php echo esc_html( get_comment_date( masvideos_date_format() ) ); ?></time>
	</p>

<?php
}
