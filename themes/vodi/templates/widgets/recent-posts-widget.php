<?php
/**
 * Standard ultimate posts widget template
 *
 * @version     2.0.0
 */
global $post;
?>
<div class="<?php echo esc_attr( $instance['template'] ); ?>">
    <ul>
        <?php if ( $vpw_query->have_posts() ) : ?>

            <?php while ( $vpw_query->have_posts() ) : $vpw_query->the_post(); ?>

                <?php $current_post = ( $post->ID == $current_post_id && is_single() ) ? 'active' : ''; ?>

                <li <?php post_class( $current_post ); ?>>
                    <?php if ( has_post_thumbnail() ) : ?>
                    <a class="post-thumbnail" href="<?php the_permalink(); ?>" rel="bookmark">
                        <?php the_post_thumbnail( $instance['thumb_size'] ); ?>
                    </a>
                    <?php endif; ?>

                    <div class="post-content">

                        <h2 class="entry-title">
                            <a href="<?php the_permalink(); ?>" rel="bookmark">
                              <?php the_title(); ?>
                            </a>
                        </h2>

                        <div class="entry-meta">

                            <?php $categories = get_the_term_list($post->ID, 'category', '', ', ');?>
                            
                            <span class="entry-categories">
                                <span class="entry-cats-list"><?php echo $categories; ?></span>
                            </span>
                            
                            <time class="published" datetime="<?php echo get_the_time('c'); ?>"><?php echo get_the_time('g a, j M'); ?></time>
                        </div>
                    </div>
                </li>

            <?php endwhile; ?>

        <?php else : ?>

            <p class="vpw-not-found">
                <?php _e('No posts found.', 'vodi'); ?>
            </p>

        <?php endif; ?>
    </ul>
</div>