<?php
/*-----------------------------------------------------------------------------------*/
/*  Recent Posts Widget Class
/*-----------------------------------------------------------------------------------*/
class Vodi_Posts_Widget extends WP_Widget {

    public $defaults;

    public function __construct() {

        $widget_ops = array(
            'classname'   => 'vodi_posts_widget',
            'description'   => esc_html__( 'Your site&#8217;s most recent Posts.', 'vodi' )
        );

        parent::__construct( 'vodi_recent_posts_widget', esc_html__('Vodi Posts Widget', 'vodi'), $widget_ops );
    }

    public function widget( $args, $instance ) {

        global $post;

        if ( is_object( $post ) ) {
            $current_post_id = $post->ID;
        } else {
            $current_post_id = 0;
        }

        $cache = wp_cache_get( 'widget_recent_posts', 'widget' );

        if ( !is_array( $cache ) )
            $cache = array();

        if ( isset( $cache[$args['widget_id']] ) ) {
            echo $cache[$args['widget_id']];
            return;
        }

        ob_start();
        extract( $args );

        $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        $header_aside_text = apply_filters('header_aside_text', empty($instance['header_aside_text']) ? '' : $instance['header_aside_text'], $instance, $this->id_base);
        $header_aside_link = $instance['header_aside_link'];
        $class = $instance['class'];
        $number = empty($instance['number']) ? -1 : $instance['number'];
        $types = empty($instance['types']) ? 'any' : explode(',', $instance['types']);
        $cats = empty($instance['cats']) ? '' : explode(',', $instance['cats']);
        $tags = empty($instance['tags']) ? '' : explode(',', $instance['tags']);
        $atcat = $instance['atcat'] ? true : false;
        $thumb_size = $instance['thumb_size'];
        $attag = $instance['attag'] ? true : false;
        $excerpt_length = $instance['excerpt_length'];
        $excerpt_readmore = $instance['excerpt_readmore'];
        $sticky = $instance['sticky'];
        $order = $instance['order'];
        $orderby = $instance['orderby'];
        $meta_key = $instance['meta_key'];
        $custom_fields = $instance['custom_fields'];

        // Sticky posts
        if ($sticky == 'only') {
            $sticky_query = array( 'post__in' => get_option( 'sticky_posts' ) );
        } elseif ($sticky == 'hide') {
            $sticky_query = array( 'post__not_in' => get_option( 'sticky_posts' ) );
        } else {
            $sticky_query = null;
        }

        // If $atcat true and in category
        if ($atcat && is_category()) {
            $cats = get_query_var('cat');
        }

        // If $atcat true and is single post
        if ($atcat && is_single()) {
            $cats = '';
            foreach (get_the_category() as $catt) {
                $cats .= $catt->term_id.' ';
            }
            $cats = str_replace(' ', ',', trim($cats));
        }

        // If $attag true and in tag
        if ($attag && is_tag()) {
            $tags = get_query_var('tag_id');
        }

        // If $attag true and is single post
        if ($attag && is_single()) {
            $tags = '';
            $thetags = get_the_tags();
            if ($thetags) {
                foreach ($thetags as $tagg) {
                    $tags .= $tagg->term_id . ' ';
                }
            }
            $tags = str_replace(' ', ',', trim($tags));
        }

        // Excerpt more filter
        $new_excerpt_more = create_function('$more', 'return "...";');
        add_filter('excerpt_more', $new_excerpt_more);

        // Excerpt length filter
        $new_excerpt_length = create_function('$length', "return " . $excerpt_length . ";");
        if ( $instance['excerpt_length'] > 0 ) add_filter('excerpt_length', $new_excerpt_length);

        if( $class ) {
            $before_widget = str_replace('class="', 'class="'. $class . ' ', $before_widget);
        }

        echo $before_widget;

        if ( $title ) {
            echo $before_title;
            echo $title;
            echo '</span>';
            if ( ! empty ( $header_aside_text ) && ! empty ( $header_aside_link ) ) {
                echo '<span class="header-aside"><a href="' . esc_url( $header_aside_link ) . '">' . esc_html__( $header_aside_text, "vodi" ) . '</a></span>';
            }
            echo '</div>';
        }

        $args = array(
            'posts_per_page' => $number,
            'order' => $order,
            'orderby' => $orderby,
            'category__in' => $cats,
            'tag__in' => $tags,
            'post_type' => $types
        );

        if ($orderby === 'meta_value') {
            $args['meta_key'] = $meta_key;
        }

        if (!empty($sticky_query)) {
            $args[key($sticky_query)] = reset($sticky_query);
        }

        $args = apply_filters('vpw_wp_query_args', $args, $instance, $this->id_base);

        $vpw_query = new WP_Query($args);

        vodi_get_template( 'widgets/recent-posts-widget.php', array( 'vpw_query' => $vpw_query, 'args' => $args,'instance' => $instance, 'current_post_id' => $current_post_id ) );

        // Reset the global $the_post as this query will have stomped on it
        wp_reset_postdata();

        echo $after_widget;

        if ($cache) {
            $cache[$args['widget_id']] = ob_get_flush();
        }
        wp_cache_set( 'widget_recent_posts', $cache, 'widget' );
    }

    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['header_aside_text'] = strip_tags( $new_instance['header_aside_text'] );
        $instance['class'] = strip_tags( $new_instance['class']);
        $instance['header_aside_link'] = strip_tags( $new_instance['header_aside_link'] );
        $instance['number'] = strip_tags( $new_instance['number'] );
        $instance['types'] = (isset( $new_instance['types'] )) ? implode(',', (array) $new_instance['types']) : '';
        $instance['cats'] = (isset( $new_instance['cats'] )) ? implode(',', (array) $new_instance['cats']) : '';
        $instance['tags'] = (isset( $new_instance['tags'] )) ? implode(',', (array) $new_instance['tags']) : '';
        $instance['atcat'] = isset( $new_instance['atcat'] );
        $instance['attag'] = isset( $new_instance['attag'] );
        $instance['show_excerpt'] = isset( $new_instance['show_excerpt'] );
        $instance['show_content'] = isset( $new_instance['show_content'] );
        $instance['show_thumbnail'] = isset( $new_instance['show_thumbnail'] );
        $instance['show_date'] = isset( $new_instance['show_date'] );
        $instance['date_format'] = strip_tags( $new_instance['date_format'] );
        $instance['show_title'] = isset( $new_instance['show_title'] );
        $instance['show_author'] = isset( $new_instance['show_author'] );
        $instance['show_comments'] = isset( $new_instance['show_comments'] );
        $instance['thumb_size'] = strip_tags( $new_instance['thumb_size'] );
        $instance['show_readmore'] = isset( $new_instance['show_readmore']);
        $instance['excerpt_length'] = strip_tags( $new_instance['excerpt_length'] );
        $instance['excerpt_readmore'] = strip_tags( $new_instance['excerpt_readmore'] );
        $instance['sticky'] = $new_instance['sticky'];
        $instance['order'] = $new_instance['order'];
        $instance['orderby'] = $new_instance['orderby'];
        $instance['meta_key'] = $new_instance['meta_key'];
        $instance['show_cats'] = isset( $new_instance['show_cats'] );
        $instance['show_tags'] = isset( $new_instance['show_tags'] );
        $instance['custom_fields'] = strip_tags( $new_instance['custom_fields'] );
        $instance['template'] = strip_tags( $new_instance['template'] );
        $instance['template_custom'] = strip_tags( $new_instance['template_custom'] );

        if (current_user_can('unfiltered_html')) {
            $instance['before_posts'] =  $new_instance['before_posts'];
            $instance['after_posts'] =  $new_instance['after_posts'];
        } else {
            $instance['before_posts'] = wp_filter_post_kses($new_instance['before_posts']);
            $instance['after_posts'] = wp_filter_post_kses($new_instance['after_posts']);
        }

        $this->flush_widget_cache();

        $alloptions = wp_cache_get( 'alloptions', 'options' );
        if ( isset( $alloptions['vodi_posts_widget'] ) )
            delete_option( 'vodi_posts_widget' );

        return $instance;
    }

    public function flush_widget_cache() {

        wp_cache_delete( 'vodi_posts_widget', 'vodi' );

    }

    public function form( $instance ) {

        // Set default arguments
        $instance = wp_parse_args( (array) $instance, array(
            'title' => __('Vodi Post Widget', 'vodi'),
            'header_aside_text' => __('View All', 'vodi'),
            'class' => '',
            'header_aside_link' => '' ,
            'number' => '5',
            'types' => 'post',
            'cats' => '',
            'tags' => '',
            'atcat' => false,
            'thumb_size' => 'thumbnail',
            'attag' => false,
            'excerpt_length' => 10,
            'excerpt_readmore' => __('Read more &rarr;', 'vodi'),
            'order' => 'DESC',
            'orderby' => 'date',
            'meta_key' => '',
            'sticky' => 'show',
            'show_cats' => false,
            'show_tags' => false,
            'show_title' => true,
            'show_date' => true,
            'date_format' => get_option('date_format') . ' ' . get_option('time_format'),
            'show_author' => true,
            'show_comments' => false,
            'show_excerpt' => true,
            'show_content' => false,
            'show_readmore' => true,
            'show_thumbnail' => true,
            'custom_fields' => '',
    // Set template to 'legacy' if field from vodi < 2.0 is set.
        'template' => empty($instance['morebutton_text']) ? 'standard' : 'legacy',
        'template_custom' => '',
        'before_posts' => '',
        'after_posts' => ''
    ) );

    // Or use the instance
            $title  = strip_tags($instance['title']);
            $header_aside_text  = strip_tags($instance['header_aside_text']);
            $class  = strip_tags($instance['class']);
            $header_aside_link  = strip_tags($instance['header_aside_link']);
            $number = strip_tags($instance['number']);
            $types  = $instance['types'];
            $cats = $instance['cats'];
            $tags = $instance['tags'];
            $atcat = $instance['atcat'];
            $thumb_size = $instance['thumb_size'];
            $attag = $instance['attag'];
            $excerpt_length = strip_tags($instance['excerpt_length']);
            $excerpt_readmore = strip_tags($instance['excerpt_readmore']);
            $order = $instance['order'];
            $orderby = $instance['orderby'];
            $meta_key = $instance['meta_key'];
            $sticky = $instance['sticky'];
            $show_cats = $instance['show_cats'];
            $show_tags = $instance['show_tags'];
            $show_title = $instance['show_title'];
            $show_date = $instance['show_date'];
            $date_format = $instance['date_format'];
            $show_author = $instance['show_author'];
            $show_comments = $instance['show_comments'];
            $show_excerpt = $instance['show_excerpt'];
            $show_content = $instance['show_content'];
            $show_readmore = $instance['show_readmore'];
            $show_thumbnail = $instance['show_thumbnail'];
            $custom_fields = strip_tags($instance['custom_fields']);
            $template = $instance['template'];
            $template_custom = strip_tags($instance['template_custom']);
            $before_posts = format_to_edit($instance['before_posts']);
            $after_posts = format_to_edit($instance['after_posts']);

    // Let's turn $types, $cats, and $tags into an array if they are set
            if (!empty($types)) $types = explode(',', $types);
            if (!empty($cats)) $cats = explode(',', $cats);
            if (!empty($tags)) $tags = explode(',', $tags);

    // Count number of post types for select box sizing
            $cpt_types = get_post_types( array( 'public' => true ), 'names' );
            if ($cpt_types) {
                foreach ($cpt_types as $cpt ) {
                    $cpt_ar[] = $cpt;
                }
                $n = count($cpt_ar);
                if($n > 6) { $n = 6; }
            } else {
                $n = 3;
            }

    // Count number of categories for select box sizing
        $cat_list = get_categories( 'hide_empty=0' );
        if ($cat_list) {
            foreach ($cat_list as $cat) {
                $cat_ar[] = $cat;
            }
            $c = count($cat_ar);
            if($c > 6) { $c = 6; }
        } else {
            $c = 3;
        }   

    // Count number of tags for select box sizing
        $tag_list = get_tags( 'hide_empty=0' );
        if ($tag_list) {
            foreach ($tag_list as $tag) {
                $tag_ar[] = $tag;
            }
            $t = count($tag_ar);
            if($t > 6) { $t = 6; }
        } else {
            $t = 3;
        }

        ?>

        <div class="vpw-tabs">
            <a class="vpw-tab-item active" data-toggle="vpw-tab-general"><?php _e('General', 'vodi'); ?></a>
            <a class="vpw-tab-item" data-toggle="vpw-tab-display"><?php _e('Display', 'vodi'); ?></a>
            <a class="vpw-tab-item" data-toggle="vpw-tab-filter"><?php _e('Filter', 'vodi'); ?></a>
            <a class="vpw-tab-item" data-toggle="vpw-tab-order"><?php _e('Order', 'vodi'); ?></a>
        </div>

        <div class="vpw-tab vpw-tab-general">

            <p>
                <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'vodi' ); ?>:</label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
            </p>

            <p>
                <label for="<?php echo $this->get_field_id( 'header_aside_text' ); ?>"><?php _e( 'Header Aside Text', 'vodi' ); ?>:</label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'header_aside_text' ); ?>" name="<?php echo $this->get_field_name( 'header_aside_text' ); ?>" type="text" value="<?php echo $header_aside_text; ?>" />
            </p>

            <p>
                <label for="<?php echo $this->get_field_id( 'header_aside_link' ); ?>"><?php _e( 'Header Aside Link', 'vodi' ); ?>:</label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'header_aside_link' ); ?>" name="<?php echo $this->get_field_name( 'header_aside_link' ); ?>" type="text" value="<?php echo $header_aside_link; ?>" />
            </p>

        </div>

        <div class="vpw-tab vpw-hide vpw-tab-display">

            <p>
                <label for="<?php echo $this->get_field_id('template'); ?>"><?php _e('Template', 'vodi'); ?>:</label>
                <select name="<?php echo $this->get_field_name('template'); ?>" id="<?php echo $this->get_field_id('template'); ?>" class="widefat">
                    <option value="style-1"<?php if( $template == 'style-1') echo ' selected'; ?>><?php _e('Style 1', 'vodi'); ?></option>
                    <option value="style-2"<?php if( $template == 'style-2') echo ' selected'; ?>><?php _e('Style 2', 'vodi'); ?></option>
                    <option value="style-3"<?php if( $template == 'style-3') echo ' selected'; ?>><?php _e('Style 3', 'vodi'); ?></option>
                </select>
            </p>


            <p>
                <label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts', 'vodi' ); ?>:</label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" value="<?php echo $number; ?>" min="-1" />
            </p>

        </div>

        <div class="vpw-tab vpw-hide vpw-tab-filter">

            <p>
                <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('atcat'); ?>" name="<?php echo $this->get_field_name('atcat'); ?>" <?php checked( (bool) $atcat, true ); ?> />
                <label for="<?php echo $this->get_field_id('atcat'); ?>"> <?php _e('Show posts only from current category', 'vodi');?></label>
            </p>

            <p>
                <label for="<?php echo $this->get_field_id('cats'); ?>"><?php _e( 'Categories', 'vodi' ); ?>:</label>
                <select name="<?php echo $this->get_field_name('cats'); ?>[]" id="<?php echo $this->get_field_id('cats'); ?>" class="widefat" style="height: auto;" size="<?php echo $c ?>" multiple>
                    <option value="" <?php if (empty($cats)) echo 'selected="selected"'; ?>><?php _e('&ndash; Show All &ndash;') ?></option>
                    <?php
                    $categories = get_categories( 'hide_empty=0' );
                    foreach ($categories as $category ) { ?>
                        <option value="<?php echo $category->term_id; ?>" <?php if(is_array($cats) && in_array($category->term_id, $cats)) echo 'selected="selected"'; ?>><?php echo $category->cat_name;?></option>
                    <?php } ?>
                </select>
            </p>

            <?php if ($tag_list) : ?>
                <p>
                    <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('attag'); ?>" name="<?php echo $this->get_field_name('attag'); ?>" <?php checked( (bool) $attag, true ); ?> />
                    <label for="<?php echo $this->get_field_id('attag'); ?>"> <?php _e('Show posts only from current tag', 'vodi');?></label>
                </p>

                <p>
                    <label for="<?php echo $this->get_field_id('tags'); ?>"><?php _e( 'Tags', 'vodi' ); ?>:</label>
                    <select name="<?php echo $this->get_field_name('tags'); ?>[]" id="<?php echo $this->get_field_id('tags'); ?>" class="widefat" style="height: auto;" size="<?php echo $t ?>" multiple>
                        <option value="" <?php if (empty($tags)) echo 'selected="selected"'; ?>><?php _e('&ndash; Show All &ndash;') ?></option>
                        <?php
                        foreach ($tag_list as $tag) { ?>
                            <option value="<?php echo $tag->term_id; ?>" <?php if (is_array($tags) && in_array($tag->term_id, $tags)) echo 'selected="selected"'; ?>><?php echo $tag->name;?></option>
                        <?php } ?>
                    </select>
                </p>
            <?php endif; ?>

            <p>
                <label for="<?php echo $this->get_field_id('types'); ?>"><?php _e( 'Post types', 'vodi' ); ?>:</label>
                <select name="<?php echo $this->get_field_name('types'); ?>[]" id="<?php echo $this->get_field_id('types'); ?>" class="widefat" style="height: auto;" size="<?php echo $n ?>" multiple>
                    <option value="" <?php if (empty($types)) echo 'selected="selected"'; ?>><?php _e('&ndash; Show All &ndash;') ?></option>
                    <?php
                    $args = array( 'public' => true );
                    $post_types = get_post_types( $args, 'names' );
                    foreach ($post_types as $post_type ) { ?>
                        <option value="<?php echo $post_type; ?>" <?php if(is_array($types) && in_array($post_type, $types)) { echo 'selected="selected"'; } ?>><?php echo $post_type;?></option>
                    <?php } ?>
                </select>
            </p>

            <p>
                <label for="<?php echo $this->get_field_id('sticky'); ?>"><?php _e( 'Sticky posts', 'vodi' ); ?>:</label>
                <select name="<?php echo $this->get_field_name('sticky'); ?>" id="<?php echo $this->get_field_id('sticky'); ?>" class="widefat">
                    <option value="show"<?php if( $sticky === 'show') echo ' selected'; ?>><?php _e('Show All Posts', 'vodi'); ?></option>
                    <option value="hide"<?php if( $sticky == 'hide') echo ' selected'; ?>><?php _e('Hide Sticky Posts', 'vodi'); ?></option>
                    <option value="only"<?php if( $sticky == 'only') echo ' selected'; ?>><?php _e('Show Only Sticky Posts', 'vodi'); ?></option>
                </select>
            </p>

        </div>

        <div class="vpw-tab vpw-hide vpw-tab-order">

            <p>
                <label for="<?php echo $this->get_field_id('orderby'); ?>"><?php _e('Order by', 'vodi'); ?>:</label>
                <select name="<?php echo $this->get_field_name('orderby'); ?>" id="<?php echo $this->get_field_id('orderby'); ?>" class="widefat">
                    <option value="date"<?php if( $orderby == 'date') echo ' selected'; ?>><?php _e('Published Date', 'vodi'); ?></option>
                    <option value="title"<?php if( $orderby == 'title') echo ' selected'; ?>><?php _e('Title', 'vodi'); ?></option>
                    <option value="comment_count"<?php if( $orderby == 'comment_count') echo ' selected'; ?>><?php _e('Comment Count', 'vodi'); ?></option>
                    <option value="rand"<?php if( $orderby == 'rand') echo ' selected'; ?>><?php _e('Random'); ?></option>
                    <option value="meta_value"<?php if( $orderby == 'meta_value') echo ' selected'; ?>><?php _e('Custom Field', 'vodi'); ?></option>
                    <option value="menu_order"<?php if( $orderby == 'menu_order') echo ' selected'; ?>><?php _e('Menu Order', 'vodi'); ?></option>
                </select>
            </p>

            <p
                <?php if ($orderby !== 'meta_value') echo ' style="display:none;"'; ?>>
                <label for="<?php echo $this->get_field_id( 'meta_key' ); ?>"><?php _e('Custom field', 'vodi'); ?>:</label>
                <input class="widefat" id="<?php echo $this->get_field_id('meta_key'); ?>" name="<?php echo $this->get_field_name('meta_key'); ?>" type="text" value="<?php echo $meta_key; ?>" />
            </p>

            <p>
                <label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('Order', 'vodi'); ?>:</label>
                <select name="<?php echo $this->get_field_name('order'); ?>" id="<?php echo $this->get_field_id('order'); ?>" class="widefat">
                    <option value="DESC"<?php if( $order == 'DESC') echo ' selected'; ?>><?php _e('Descending', 'vodi'); ?></option>
                    <option value="ASC"<?php if( $order == 'ASC') echo ' selected'; ?>><?php _e('Ascending', 'vodi'); ?></option>
                </select>
            </p>

        </div>

        <?php if ( $instance ) { ?>

            <script>

                jQuery(document).ready(function($){

                    var show_excerpt = $("#<?php echo $this->get_field_id( 'show_excerpt' ); ?>");
                    var show_content = $("#<?php echo $this->get_field_id( 'show_content' ); ?>");
                    var show_readmore = $("#<?php echo $this->get_field_id( 'show_readmore' ); ?>");
                    var show_readmore_wrap = $("#<?php echo $this->get_field_id( 'show_readmore' ); ?>").parents('p');
                    var show_thumbnail = $("#<?php echo $this->get_field_id( 'show_thumbnail' ); ?>");
                    var show_date = $("#<?php echo $this->get_field_id( 'show_date' ); ?>");
                    var date_format = $("#<?php echo $this->get_field_id( 'date_format' ); ?>").parents('p');
                    var excerpt_length = $("#<?php echo $this->get_field_id( 'excerpt_length' ); ?>").parents('p');
                    var excerpt_readmore_wrap = $("#<?php echo $this->get_field_id( 'excerpt_readmore' ); ?>").parents('p');
                    var thumb_size_wrap = $("#<?php echo $this->get_field_id( 'thumb_size' ); ?>").parents('p');
                    var order = $("#<?php echo $this->get_field_id('orderby'); ?>");
                    var meta_key_wrap = $("#<?php echo $this->get_field_id( 'meta_key' ); ?>").parents('p');
                    var template = $("#<?php echo $this->get_field_id('template'); ?>");
                    var template_custom = $("#<?php echo $this->get_field_id('template_custom'); ?>").parents('p');

                    var toggleReadmore = function() {
                        if (show_excerpt.is(':checked') || show_content.is(':checked')) {
                            show_readmore_wrap.show('fast');
                        } else {
                            show_readmore_wrap.hide('fast');
                        }
                        toggleExcerptReadmore();
                    }

                    var toggleExcerptReadmore = function() {
                        if ((show_excerpt.is(':checked') || show_content.is(':checked')) && show_readmore.is(':checked')) {
                            excerpt_readmore_wrap.show('fast');
                        } else {
                            excerpt_readmore_wrap.hide('fast');
                        }
                    }

                // Toggle read more option
                show_excerpt.click(function() {
                    toggleReadmore();
                });

                // Toggle read more option
                show_content.click(function() {
                    toggleReadmore();
                });

                // Toggle excerpt length on click
                show_excerpt.click(function(){
                    excerpt_length.toggle('fast');
                });

                // Toggle excerpt length on click
                show_readmore.click(function(){
                    toggleExcerptReadmore();
                });

                // Toggle date format on click
                show_date.click(function(){
                    date_format.toggle('fast');
                });

                // Toggle excerpt length on click
                show_thumbnail.click(function(){
                    thumb_size_wrap.toggle('fast');
                });

                // Show or hide custom field meta_key value on order change
                order.change(function(){
                    if ($(this).val() === 'meta_value') {
                        meta_key_wrap.show('fast');
                    } else {
                        meta_key_wrap.hide('fast');
                    }
                });

                // Show or hide custom template field
                template.change(function(){
                    if ($(this).val() === 'custom') {
                        template_custom.show('fast');
                    } else {
                        template_custom.hide('fast');
                    }
                });

                });

            </script>

        <?php

    }

    }
}