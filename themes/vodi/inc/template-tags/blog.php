<?php
/**
 * Template tags used in Blog Pages
 */

if ( ! function_exists( 'vodi_loop_wrap_open' ) ) {
    function vodi_loop_wrap_open() {
        ?><div class="articles"><?php
    }
}

if ( ! function_exists( 'vodi_loop_wrap_close' ) ) {
    function vodi_loop_wrap_close() {
        ?></div><?php
    }
}

if ( ! function_exists( 'vodi_post_header' ) ) {
    function vodi_post_header() {
        ?><header class="article__header"><?php 
            do_action( 'vodi_post_header' ); 
        ?></header><?php
    }
}

if ( ! function_exists( 'vodi_single_post_header' ) ) {
    function vodi_single_post_header() {
        ?><header class="article__header"><?php 
            do_action( 'vodi_single_post_header' ); 
        ?></header><?php
    }
}

if ( ! function_exists( 'vodi_post_title' ) ) {
    function vodi_post_title() {
        the_title( sprintf( '<h2 class="article__title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' );
    }
}

if ( ! function_exists( 'vodi_wrap_single_post_start' ) ) {
    function vodi_wrap_single_post_start() {
        ?><div class="single-article__inner"><?php
    }
}

if ( ! function_exists( 'vodi_wrap_single_post_end' ) ) {
    function vodi_wrap_single_post_end() {
        ?></div><!-- /.single-article__inner --><?php
    }
}

if ( ! function_exists( 'vodi_single_post_title' ) ) {
    function vodi_single_post_title() {
        the_title( '<h2 class="article__title entry-title">', '</h2>' );
    }
}

if ( ! function_exists( 'vodi_post_meta' ) ) {
    function vodi_post_meta() {
        ?><div class="article__meta"><?php 
            do_action( 'vodi_post_meta' );
        ?></div><?php
    }
}

if ( ! function_exists( 'vodi_post_categories' ) ) {
    function vodi_post_categories( $post_id = false, $return = false ) {
        /* translators: used between list items, there is a space after the comma */
        $categories_list = get_the_category_list( esc_html__( ', ', 'vodi' ), '', $post_id );

        if ( $categories_list ) {
            $categories_list = '<div class="article__categories">' . $categories_list . '</div>';

            if ( ! $return ) {
            
                echo wp_kses_post( $categories_list );   
            
            } else {
            
                return $categories_list;
            
            }
        }
    }
}

if ( ! function_exists( 'vodi_post_date' ) ) {
    function vodi_post_date() {
        $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
        if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
            $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time> <time class="updated" datetime="%3$s">%4$s</time>';
        }

        $time_string = sprintf( $time_string,
            esc_attr( get_the_date( 'c' ) ),
            esc_html( get_the_date() ),
            esc_attr( get_the_modified_date( 'c' ) ),
            esc_html( get_the_modified_date() )
        );

        $posted_on = sprintf(
            _x( '%s', 'post date', 'vodi' ),
            '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
        );

        echo wp_kses( apply_filters( 'vodi_single_post_posted_on_html', '<span class="article__date">' . $posted_on . '</span>', $posted_on ), array(
            'span' => array(
                'class'  => array(),
            ),
            'a'    => array(
                'href'  => array(),
                'title' => array(),
                'rel'   => array(),
            ),
            'time' => array(
                'datetime' => array(),
                'class'    => array(),
            ),
        ) );
    }
}

if ( ! function_exists( 'vodi_post_comments' ) ) {
    function vodi_post_comments() {
        if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) : ?>
            <div class="article__comments comments-link">
                <span class="comments-link"><?php comments_popup_link( esc_html__( 'Leave a comment', 'vodi' ), esc_html__( '1 Comment', 'vodi' ), esc_html__( '% Comments', 'vodi' ) ); ?></span>
            </div>
        <?php endif;
    }
}

if ( ! function_exists( 'vodi_post_excerpt' ) ) {
    function vodi_post_excerpt() {
        ?><div class="article__excerpt"><?php the_excerpt(); ?></div><?php
    }
}


if ( ! function_exists( 'vodi_paging_nav' ) ) {
    /**
     * Display navigation to next/previous set of posts when applicable.
     */
    function vodi_paging_nav() {
        global $wp_query;

        $args = array(
            'next_text' => esc_html_x( 'Next', 'Next post', 'vodi' ),
            'prev_text' => esc_html_x( 'Previous', 'Previous post', 'vodi' ),
        );

        the_posts_pagination( $args );
    }
}

if ( ! function_exists( 'vodi_post_the_content' ) ) {
    function vodi_post_the_content() {
        the_content(
            sprintf(
                wp_kses_post( __( 'Continue reading %s', 'vodi' ) ),
                '<span class="screen-reader-text">' . get_the_title() . '</span>'
            )
        );
    }
}

if ( ! function_exists( 'vodi_post_featured_image' ) ) {
    /**
     * Displays post thumbnail when applicable
     */
    function vodi_post_featured_image() {

        if ( '' !== get_the_post_thumbnail() ) : ?>
        
            <div class="article__attachment--thumbnail"><?php 
                
                if ( is_sticky() || is_single() ) :
                
                    the_post_thumbnail( 'vodi-single-post-featured-image' );
                
                else : ?>
                
                    <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'vodi-featured-image' ); ?></a><?php
                
                endif; 
            
            ?></div><!-- .article__thumbnail --><?php 

        endif;
    }
}

if ( ! function_exists( 'vodi_post_gallery' ) ) {
    /**
     * Displays post gallery when applicable
     */
    function vodi_post_gallery() {
        ?><div class="article__attachment--gallery"><?php echo get_post_gallery(); ?></div><?php
    }
}

if ( ! function_exists( 'vodi_post_audio' ) ) {
    /**
     * Displays post audio when applicable
     */
    function vodi_post_audio() {
        $content = apply_filters( 'the_content', get_the_content() );
        $audio   = false;

        // Only get audio from the content if a playlist isn't present.
        if ( false === strpos( $content, 'wp-playlist-script' ) ) {
            $audio = get_media_embedded_in_content( $content, array( 'audio' ) );
        }

        if ( ! empty( $audio ) ) {
            foreach ( $audio as $audio_html ) {
                ?><div class="article__attachment--audio"><?php 
                    echo ( $audio_html ); 
                ?></div><!-- .article__attachment--audio --><?php
            }
        }
    }
}

if ( ! function_exists( 'vodi_post_video' ) ) {
    /**
     * Displays post video when applicable
     */
    function vodi_post_video() {
        $content = apply_filters( 'the_content', get_the_content() );
        $video   = false;

        // Only get video from the content if a playlist isn't present.
        if ( false === strpos( $content, 'wp-playlist-script' ) ) {
            $video = get_media_embedded_in_content( $content, array( 'video', 'object', 'embed', 'iframe' ) );
        }

        if ( ! empty( $video ) ) {
            foreach ( $video as $video_html ) {
                ?><div class="article__attachment--video"><?php 
                    echo ( $video_html ); 
                ?></div><!-- .article__attachment--video --><?php
            }
        }
    }
}

if ( ! function_exists( 'vodi_post_attachment' ) ) {
    /**
     * Displays post attachments such as audio, video, gallery and featured image
     */
    function vodi_post_attachment() {
        ob_start();
        $post_format = get_post_format();

        if ( has_post_thumbnail() ) {
            vodi_post_featured_image();
        } else {
            if ( 'audio' === $post_format ) {
                vodi_post_audio();
            } elseif ( 'video' === $post_format ) {
                vodi_post_video();
            } elseif( 'gallery' === $post_format ) {
                vodi_post_gallery();
            }
        }
        $post_attachment = ob_get_clean();

        if ( ! empty( $post_attachment ) ) {
            ?><div class="article__attachment"><?php echo $post_attachment; ?></div><?php
        }
    }
}

if ( ! function_exists( 'vodi_post_summary' ) ) {
    /**
     * Displays post summary
     */
    function vodi_post_summary() {
        ?><div class="article__summary"><?php 
            do_action( 'vodi_post_summary' );
        ?></div><?php
    }
}

if ( ! function_exists( 'vodi_post_content' ) ) {
    /**
     * Display the post content with a link to the single post
     *
     * @since 1.0.0
     */
    function vodi_post_content() {
        ?>
        <div class="article__content entry-content">
        <?php

        /**
         * Functions hooked in to vodi_post_content_before action.
         *
         * @hooked vodi_post_thumbnail - 10
         */
        do_action( 'vodi_post_content_before' );

        the_content(
            sprintf(
                __( 'Continue reading %s', 'vodi' ),
                '<span class="screen-reader-text">' . get_the_title() . '</span>'
            )
        );

        do_action( 'vodi_post_content_after' );

        wp_link_pages( array(
            'before'      => '<div class="page-links">' . __( 'Pages:', 'vodi' ) . '<div class="page-links-inner">',
            'after'       => '</div></div>',
            'link_before' => '<span class="page-link">',
            'link_after'  => '</span>'
        ) );
        ?>
        </div><!-- .entry-content -->
        <?php
    }
}

if ( ! function_exists( 'vodi_post_nav' ) ) {
    /**
     * Display navigation to next/previous post when applicable.
     */
    function vodi_post_nav() {
        $args = array(
            'next_text' => '<span class="post-nav__title">' . esc_html__( 'Next Post', 'vodi' ) . ' </span><span class="post-nav__article--title">%title</span><span class="post-nav__article--meta"><span class="post-nav__article--categories">%categories</span><span class="post-nav__article--date">%date</span>',
            'prev_text' => '<span class="post-nav__title">' . esc_html__( 'Previous Post', 'vodi' ) . ' </span><span class="post-nav__article--title">%title</span><span class="post-nav__article--meta"><span class="post-nav__article--categories">%categories</span><span class="post-nav__article--date">%date</span>',
        );
        the_post_navigation( $args );
    }
}

if ( ! function_exists( 'vodi_display_comments' ) ) {
    /**
     * vodi display comments
     *
     * @since  1.0.0
     */
    function vodi_display_comments() {
        // If comments are open or we have at least one comment, load up the comment template.
        if ( comments_open() || '0' != get_comments_number() ) :
            comments_template();
        endif;
    }
}

if ( ! function_exists( 'vodi_comment' ) ) {
    /**
     * vodi comment template
     *
     * @param array $comment the comment array.
     * @param array $args the comment args.
     * @param int   $depth the comment depth.
     * @since 1.0.0
     */
    function vodi_comment( $comment, $args, $depth ) {
        if ( 'div' == $args['style'] ) {
            $tag = 'div';
            $add_below = 'comment';
        } else {
            $tag = 'li';
            $add_below = 'div-comment';
        }
        ?>
        <<?php echo esc_attr( $tag ); ?> <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ) ?> id="comment-<?php comment_ID() ?>">
        <div class="comment-body">
        <?php 
            $comment_avatar = get_avatar( $comment, 50 ); 
            if ( ! empty( $comment_avatar ) ): ?>
        <div class="comment-author-gravatar"><?php echo wp_kses_post( $comment_avatar ); ?></div><?php endif; ?>
        <div class="comment-meta-and-content">
        <div class="comment-meta commentmetadata">
            <div class="comment-author vcard">
            <?php printf( wp_kses_post( '<cite class="comment-author-name fn">%s</cite>', 'vodi' ), get_comment_author_link() ); ?>
            </div>
            <?php if ( '0' == $comment->comment_approved ) : ?>
                <em class="comment-awaiting-moderation"><?php esc_attr_e( 'Your comment is awaiting moderation.', 'vodi' ); ?></em>
            <?php endif; ?>
            <a href="<?php echo esc_url( htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ); ?>" class="comment-date">
                <?php echo '<time datetime="' . get_comment_date( 'c' ) . '">' . get_comment_date() . '</time>'; ?>
            </a>
        </div>
        <?php if ( 'div' != $args['style'] ) : ?>
        <div id="div-comment-<?php comment_ID() ?>" class="comment-content">
        <?php endif; ?>
        <div class="comment-text">
        <?php comment_text(); ?>
        </div>
        <div class="reply">
        <?php comment_reply_link( array_merge( $args, array( 'add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
        <?php edit_comment_link( esc_html__( 'Edit', 'vodi' ), '  ', '' ); ?>
        </div>
        </div>
        <?php if ( 'div' != $args['style'] ) : ?>
        </div>
        <?php endif; ?>
        </div>
    <?php
    }
}

if ( ! function_exists( 'vodi_adjacent_post_link' ) ) {
    /**
     * Modify previous, next post nav
     */
    function vodi_adjacent_post_link( $output, $format, $link, $post, $adjacent ) {

        if ( $post ) {

            $categories = strip_tags( vodi_post_categories( $post->ID, true ) );
            $output     = str_replace( '%categories', $categories, $output );

        }
        
        return $output;
    }
}

if ( ! function_exists( 'vodi_archive_header' ) ) {
    /**
     * Displays archive header
     */
    function vodi_archive_header() {
        
        if ( is_archive() && have_posts() ) : ?>
        
        <header class="archive__header page__header">
            <div class="container">
            <?php
                the_archive_title( '<h1 class="page__title">', '</h1>' );
                the_archive_description( '<div class="page__caption taxonomy-description">', '</div>' );
            ?>
            </div>
        </header><!-- .page-header --><?php
        
        endif; 
    }
}

if ( ! function_exists( 'vodi_related_posts' ) ) {
    /**
     * Display Posts
     */
    function vodi_related_posts( $args = array() ) {

        $defaults = apply_filters( 'vodi_related_posts_args', array(
            'section_class'             => '',
            'section_title'             => esc_html__( 'You May Also Like', 'vodi' ),
            'header_aside_action_text'  => esc_html__( 'View all', 'vodi' ),
            'header_aside_action_link'  => '#',
            'limit'             => 3,
            'columns'           => 3,
            'post_choice'       => 'recent',
            'post_ids'          => ''
        ) );

        $args = wp_parse_args( $args, $defaults );

        $query_args = array(
            'post_type'             => 'post',
            'post_status'           => 'publish',
            'ignore_sticky_posts'   => 1,
            'orderby'               => 'date',
            'order'                 => 'desc',
            'posts_per_page'        => 3,
        );

        $tags = wp_get_post_terms( get_queried_object_id(), 'post_tag', ['fields' => 'ids'] );
        if( ! empty( $tags ) ) {
            $query_args['tax_query'] = [
                [
                    'taxonomy' => 'post_tag',
                    'terms'    => $tags
                ]
            ];
        }

        extract( $args );

        if( ! empty( $limit ) ) {
            $query_args['posts_per_page'] = $limit;
        }

        if( ! empty( $post_choice ) ) {
            if( $post_choice == 'specific' && ! empty( $post_ids ) ) {
                $query_args['post__in'] = explode( ",", $post_ids );
            } elseif( $post_choice == 'random' ) {
                $query_args['orderby'] = 'rand';
            }
        }

        $args['query_args'] = apply_filters( 'vodi_related_posts_query_args', $query_args );
        
        vodi_get_template( 'sections/related-posts.php', $args );
    }
}



if ( ! function_exists( 'vodi_jetpack_share' ) ) {
    function vodi_jetpack_share() {
        ob_start();
        if( function_exists( 'vodi_show_jetpack_share' ) ) {
            vodi_show_jetpack_share();
        }

        $jetpack_share_html = ob_get_clean();
            if( apply_filters( 'vodi_show_jetpack_share', true ) ) {
            ?>
            <?php if ( ! empty( $jetpack_share_html ) ) : ?>
                <div class="single-post-sharing"><?php echo wp_kses_post( $jetpack_share_html ); ?></div>
            <?php endif; ?>
            
            <?php
        }
    }
}

if ( ! function_exists( 'vodi_custom_excerpt_length' ) ) {
    function vodi_custom_excerpt_length( $length ) {
        return apply_filters( 'vodi_custom_excerpt_length', 20 );
    }
}
