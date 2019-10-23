<?php
/**
 * Abstract widget class
 *
 * @class MasVideos_Widget
 * @package  MasVideos/Abstracts
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * MasVideos_Widget
 *
 * @package  MasVideos/Abstracts
 * @version  1.0.0
 * @extends  WP_Widget
 */
abstract class MasVideos_Widget extends WP_Widget {

    /**
     * CSS class.
     *
     * @var string
     */
    public $widget_cssclass;

    /**
     * Widget description.
     *
     * @var string
     */
    public $widget_description;

    /**
     * Widget ID.
     *
     * @var string
     */
    public $widget_id;

    /**
     * Widget name.
     *
     * @var string
     */
    public $widget_name;

    /**
     * Settings.
     *
     * @var array
     */
    public $settings;

    /**
     * Constructor.
     */
    public function __construct() {
        $widget_ops = array(
            'classname'   => $this->widget_cssclass,
            'description' => $this->widget_description,
            'customize_selective_refresh' => true,
        );

        parent::__construct( $this->widget_id, $this->widget_name, $widget_ops );

        add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
        add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
        add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
    }

    /**
     * Get cached widget.
     *
     * @param  array $args Arguments.
     * @return bool true if the widget is cached otherwise false
     */
    public function get_cached_widget( $args ) {
        $cache = wp_cache_get( $this->get_widget_id_for_cache( $this->widget_id ), 'widget' );

        if ( ! is_array( $cache ) ) {
            $cache = array();
        }

        if ( isset( $cache[ $this->get_widget_id_for_cache( $args['widget_id'] ) ] ) ) {
            echo $cache[ $this->get_widget_id_for_cache( $args['widget_id'] ) ]; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
            return true;
        }

        return false;
    }

    /**
     * Cache the widget.
     *
     * @param  array  $args Arguments.
     * @param  string $content Content.
     * @return string the content that was cached
     */
    public function cache_widget( $args, $content ) {
        $cache = wp_cache_get( $this->get_widget_id_for_cache( $this->widget_id ), 'widget' );

        if ( ! is_array( $cache ) ) {
            $cache = array();
        }

        $cache[ $this->get_widget_id_for_cache( $args['widget_id'] ) ] = $content;

        wp_cache_set( $this->get_widget_id_for_cache( $this->widget_id ), $cache, 'widget' );

        return $content;
    }

    /**
     * Flush the cache.
     */
    public function flush_widget_cache() {
        foreach ( array( 'https', 'http' ) as $scheme ) {
            wp_cache_delete( $this->get_widget_id_for_cache( $this->widget_id, $scheme ), 'widget' );
        }
    }

    /**
     * Output the html at the start of a widget.
     *
     * @param array $args Arguments.
     * @param array $instance Instance.
     */
    public function widget_start( $args, $instance ) {
        echo $args['before_widget']; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped

        if ( $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base ) ) { // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found, WordPress.CodeAnalysis.AssignmentInCondition.Found
            echo $args['before_title'] . $title . $args['after_title']; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
        }
    }

    /**
     * Output the html at the end of a widget.
     *
     * @param  array $args Arguments.
     */
    public function widget_end( $args ) {
        echo $args['after_widget']; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
    }

    /**
     * Updates a particular instance of a widget.
     *
     * @see    WP_Widget->update
     * @param  array $new_instance New instance.
     * @param  array $old_instance Old instance.
     * @return array
     */
    public function update( $new_instance, $old_instance ) {

        $instance = $old_instance;

        if ( empty( $this->settings ) ) {
            return $instance;
        }

        // Loop settings and get values to save.
        foreach ( $this->settings as $key => $setting ) {
            if ( ! isset( $setting['type'] ) ) {
                continue;
            }

            // Format the value based on settings type.
            switch ( $setting['type'] ) {
                case 'number':
                    $instance[ $key ] = absint( $new_instance[ $key ] );

                    if ( isset( $setting['min'] ) && '' !== $setting['min'] ) {
                        $instance[ $key ] = max( $instance[ $key ], $setting['min'] );
                    }

                    if ( isset( $setting['max'] ) && '' !== $setting['max'] ) {
                        $instance[ $key ] = min( $instance[ $key ], $setting['max'] );
                    }
                    break;
                case 'textarea':
                    $instance[ $key ] = wp_kses( trim( wp_unslash( $new_instance[ $key ] ) ), wp_kses_allowed_html( 'post' ) );
                    break;
                case 'checkbox':
                    $instance[ $key ] = empty( $new_instance[ $key ] ) ? 0 : 1;
                    break;
                default:
                    $instance[ $key ] = isset( $new_instance[ $key ] ) ? sanitize_text_field( $new_instance[ $key ] ) : $setting['std'];
                    break;
            }

            /**
             * Sanitize the value of a setting.
             */
            $instance[ $key ] = apply_filters( 'masvideos_widget_settings_sanitize_option', $instance[ $key ], $new_instance, $key, $setting );
        }

        $this->flush_widget_cache();

        return $instance;
    }

    /**
     * Outputs the settings update form.
     *
     * @see   WP_Widget->form
     *
     * @param array $instance Instance.
     */
    public function form( $instance ) {

        if ( empty( $this->settings ) ) {
            return;
        }

        foreach ( $this->settings as $key => $setting ) {

            $class = isset( $setting['class'] ) ? $setting['class'] : '';
            $value = isset( $instance[ $key ] ) ? $instance[ $key ] : $setting['std'];

            switch ( $setting['type'] ) {

                case 'text':
                    ?>
                    <p>
                        <label for="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>"><?php echo $setting['label']; ?></label><?php // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped ?>
                        <input class="widefat <?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>" type="text" value="<?php echo esc_attr( $value ); ?>" />
                    </p>
                    <?php
                    break;

                case 'number':
                    ?>
                    <p>
                        <label for="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>"><?php echo $setting['label']; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?></label>
                        <input class="widefat <?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>" type="number" step="<?php echo esc_attr( $setting['step'] ); ?>" min="<?php echo esc_attr( $setting['min'] ); ?>" max="<?php echo esc_attr( $setting['max'] ); ?>" value="<?php echo esc_attr( $value ); ?>" />
                    </p>
                    <?php
                    break;

                case 'select':
                    ?>
                    <p>
                        <label for="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>"><?php echo $setting['label']; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?></label>
                        <select class="widefat <?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>">
                            <?php foreach ( $setting['options'] as $option_key => $option_value ) : ?>
                                <option value="<?php echo esc_attr( $option_key ); ?>" <?php selected( $option_key, $value ); ?>><?php echo esc_html( $option_value ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </p>
                    <?php
                    break;

                case 'textarea':
                    ?>
                    <p>
                        <label for="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>"><?php echo $setting['label']; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?></label>
                        <textarea class="widefat <?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>" cols="20" rows="3"><?php echo esc_textarea( $value ); ?></textarea>
                        <?php if ( isset( $setting['desc'] ) ) : ?>
                            <small><?php echo esc_html( $setting['desc'] ); ?></small>
                        <?php endif; ?>
                    </p>
                    <?php
                    break;

                case 'checkbox':
                    ?>
                    <p>
                        <input class="checkbox <?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>" type="checkbox" value="1" <?php checked( $value, 1 ); ?> />
                        <label for="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>"><?php echo $setting['label']; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?></label>
                    </p>
                    <?php
                    break;

                // Default: run an action.
                default:
                    do_action( 'masvideos_widget_field_' . $setting['type'], $key, $value, $setting, $instance );
                    break;
            }
        }
    }

    /**
     * Get current page URL with various filtering props supported by MasVideos.
     *
     * @return string
     * @since  1.0.0
     */
    protected function get_current_page_url() {
        if ( defined( 'EPISODES_ON_FRONT' ) ||  defined( 'TV_SHOWS_ON_FRONT' ) || defined( 'VIDEOS_ON_FRONT' ) || defined( 'MOVIES_ON_FRONT' ) ) {
            $link = home_url();
        } elseif ( is_episodes() ) {
            $episodes_page_id = masvideos_get_page_id( 'episodes' );
            $link = 0 < $episodes_page_id ? get_permalink( $episodes_page_id ) : get_post_type_archive_link( 'episode' );
        } elseif ( is_episode_genre() ) {
            $link = get_term_link( get_query_var( 'episode_genre' ), 'episode_genre' );
        } elseif ( is_episode_tag() ) {
            $link = get_term_link( get_query_var( 'episode_tag' ), 'episode_tag' );
        } elseif ( is_tv_shows() ) {
            $tv_shows_page_id = masvideos_get_page_id( 'tv_shows' );
            $link = 0 < $tv_shows_page_id ? get_permalink( $tv_shows_page_id ) : get_post_type_archive_link( 'tv_show' );
        } elseif ( is_tv_show_genre() ) {
            $link = get_term_link( get_query_var( 'tv_show_genre' ), 'tv_show_genre' );
        } elseif ( is_tv_show_tag() ) {
            $link = get_term_link( get_query_var( 'tv_show_tag' ), 'tv_show_tag' );
        } elseif ( is_videos() ) {
            $videos_page_id = masvideos_get_page_id( 'videos' );
            $link = 0 < $videos_page_id ? get_permalink( $videos_page_id ) : get_post_type_archive_link( 'video' );
        } elseif ( is_video_category() ) {
            $link = get_term_link( get_query_var( 'video_cat' ), 'video_cat' );
        } elseif ( is_video_tag() ) {
            $link = get_term_link( get_query_var( 'video_tag' ), 'video_tag' );
        } elseif ( is_movies() ) {
            $movies_page_id = masvideos_get_page_id( 'movies' );
            $link = 0 < $movies_page_id ? get_permalink( $movies_page_id ) : get_post_type_archive_link( 'movie' );
        } elseif ( is_movie_genre() ) {
            $link = get_term_link( get_query_var( 'movie_genre' ), 'movie_genre' );
        } elseif ( is_movie_tag() ) {
            $link = get_term_link( get_query_var( 'movie_tag' ), 'movie_tag' );
        } else {
            $queried_object = get_queried_object();
            $link = get_term_link( $queried_object->slug, $queried_object->taxonomy );
        }

        // Order by.
        if ( isset( $_GET['orderby'] ) ) {
            $link = add_query_arg( 'orderby', masvideos_clean( wp_unslash( $_GET['orderby'] ) ), $link );
        }

        /**
         * Search Arg.
         * To support quote characters, first they are decoded from &quot; entities, then URL encoded.
         */
        if ( get_search_query() ) {
            $link = add_query_arg( 's', rawurlencode( htmlspecialchars_decode( get_search_query() ) ), $link );
        }

        // Post Type Arg.
        if ( isset( $_GET['post_type'] ) ) {
            $link = add_query_arg( 'post_type', masvideos_clean( wp_unslash( $_GET['post_type'] ) ), $link );

            // Prevent post type and page id when pretty permalinks are disabled.
            if ( is_episodes() || is_tv_shows() || is_videos() || is_movies() ) {
                $link = remove_query_arg( 'page_id', $link );
            }
        }

        // Year Arg.
        if ( isset( $_GET['year_filter'] ) ) {
            $link = add_query_arg( 'year_filter', masvideos_clean( wp_unslash( $_GET['year_filter'] ) ), $link );
        }

        // Rating Arg.
        if ( isset( $_GET['rating_filter'] ) ) {
            $link = add_query_arg( 'rating_filter', masvideos_clean( wp_unslash( $_GET['rating_filter'] ) ), $link );
        }

        // All current filters.
        if ( $_chosen_attributes = MasVideos_Episodes_Query::get_layered_nav_chosen_attributes() ) { // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found, WordPress.CodeAnalysis.AssignmentInCondition.Found
            foreach ( $_chosen_attributes as $name => $data ) {
                $filter_name = sanitize_title( str_replace( 'episode_', '', $name ) );
                if ( ! empty( $data['terms'] ) ) {
                    $link = add_query_arg( 'filter_' . $filter_name, implode( ',', $data['terms'] ), $link );
                }
                if ( 'or' === $data['query_type'] ) {
                    $link = add_query_arg( 'query_type_' . $filter_name, 'or', $link );
                }
            }
        } elseif ( $_chosen_attributes = MasVideos_TV_Shows_Query::get_layered_nav_chosen_attributes() ) { // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found, WordPress.CodeAnalysis.AssignmentInCondition.Found
            foreach ( $_chosen_attributes as $name => $data ) {
                $filter_name = sanitize_title( str_replace( 'tv_show_', '', $name ) );
                if ( ! empty( $data['terms'] ) ) {
                    $link = add_query_arg( 'filter_' . $filter_name, implode( ',', $data['terms'] ), $link );
                }
                if ( 'or' === $data['query_type'] ) {
                    $link = add_query_arg( 'query_type_' . $filter_name, 'or', $link );
                }
            }
        } elseif ( $_chosen_attributes = MasVideos_Videos_Query::get_layered_nav_chosen_attributes() ) { // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found, WordPress.CodeAnalysis.AssignmentInCondition.Found
            foreach ( $_chosen_attributes as $name => $data ) {
                $filter_name = sanitize_title( str_replace( 'video_', '', $name ) );
                if ( ! empty( $data['terms'] ) ) {
                    $link = add_query_arg( 'filter_' . $filter_name, implode( ',', $data['terms'] ), $link );
                }
                if ( 'or' === $data['query_type'] ) {
                    $link = add_query_arg( 'query_type_' . $filter_name, 'or', $link );
                }
            }
        } elseif ( $_chosen_attributes = MasVideos_Movies_Query::get_layered_nav_chosen_attributes() ) { // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found, WordPress.CodeAnalysis.AssignmentInCondition.Found
            foreach ( $_chosen_attributes as $name => $data ) {
                $filter_name = sanitize_title( str_replace( 'movie_', '', $name ) );
                if ( ! empty( $data['terms'] ) ) {
                    $link = add_query_arg( 'filter_' . $filter_name, implode( ',', $data['terms'] ), $link );
                }
                if ( 'or' === $data['query_type'] ) {
                    $link = add_query_arg( 'query_type_' . $filter_name, 'or', $link );
                }
            }
        }

        return $link;
    }

    /**
     * Get widget id plus scheme/protocol to prevent serving mixed content from (persistently) cached widgets.
     *
     * @since  3.4.0
     * @param  string $widget_id Id of the cached widget.
     * @param  string $scheme    Scheme for the widget id.
     * @return string            Widget id including scheme/protocol.
     */
    protected function get_widget_id_for_cache( $widget_id, $scheme = '' ) {
        if ( $scheme ) {
            $widget_id_for_cache = $widget_id . '-' . $scheme;
        } else {
            $widget_id_for_cache = $widget_id . '-' . ( is_ssl() ? 'https' : 'http' );
        }

        return apply_filters( 'masvideos_cached_widget_id', $widget_id_for_cache );
    }
}
