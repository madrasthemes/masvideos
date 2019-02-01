<?php
/**
 * Shortcodes
 *
 * @package MasVideos/Classes
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * MasVideos Shortcodes class.
 */
class MasVideos_Shortcodes {

    /**
     * Init shortcodes.
     */
    public static function init() {
        $shortcodes = array(
            'mas_episodes'                 => __CLASS__ . '::episodes',
            'mas_tv_shows'                 => __CLASS__ . '::tv_shows',
            'mas_videos'                   => __CLASS__ . '::videos',
            'mas_movies'                   => __CLASS__ . '::movies',
        );

        foreach ( $shortcodes as $shortcode => $function ) {
            add_shortcode( apply_filters( "{$shortcode}_shortcode_tag", $shortcode ), $function );
        }
    }

    /**
     * Shortcode Wrapper.
     *
     * @param string[] $function Callback function.
     * @param array    $atts     Attributes. Default to empty array.
     * @param array    $wrapper  Customer wrapper data.
     *
     * @return string
     */
    public static function shortcode_wrapper(
        $function,
        $atts = array(),
        $wrapper = array(
            'class'  => 'masvideos',
            'before' => null,
            'after'  => null,
        )
    ) {
        ob_start();

        // @codingStandardsIgnoreStart
        echo empty( $wrapper['before'] ) ? '<div class="' . esc_attr( $wrapper['class'] ) . '">' : $wrapper['before'];
        call_user_func( $function, $atts );
        echo empty( $wrapper['after'] ) ? '</div>' : $wrapper['after'];
        // @codingStandardsIgnoreEnd

        return ob_get_clean();
    }

    /**
     * List multiple episodes shortcode.
     *
     * @param array $atts Attributes.
     * @return string
     */
    public static function episodes( $atts ) {
        $atts = (array) $atts;
        $type = 'episodes';

        // Allow list movie based on specific cases.
        if ( isset( $atts['top_rated'] ) && masvideos_string_to_bool( $atts['top_rated'] ) ) {
            $type = 'top_rated_episodes';
        }

        if ( isset( $atts['featured'] ) && masvideos_string_to_bool( $atts['featured'] ) ) {
            $type = 'featured_episodes';
            $atts['visibility'] = 'featured';
        }

        if( isset( $atts['className'] ) ) {
            $atts['class'] = $atts['className'];
            unset( $atts['className'] );
        }

        $shortcode = new MasVideos_Shortcode_Episodes( $atts, $type );

        return $shortcode->get_content();
    }

    /**
     * List multiple tv shows shortcode.
     *
     * @param array $atts Attributes.
     * @return string
     */
    public static function tv_shows( $atts ) {
        $atts = (array) $atts;
        $type = 'tv_shows';

        // Allow list movie based on specific cases.
        if ( isset( $atts['top_rated'] ) && masvideos_string_to_bool( $atts['top_rated'] ) ) {
            $type = 'top_rated_tv_shows';
        }

        if ( isset( $atts['featured'] ) && masvideos_string_to_bool( $atts['featured'] ) ) {
            $type = 'featured_tv_shows';
            $atts['visibility'] = 'featured';
        }

        if( isset( $atts['className'] ) ) {
            $atts['class'] = $atts['className'];
            unset( $atts['className'] );
        }

        $shortcode = new MasVideos_Shortcode_TV_Shows( $atts, $type );

        return $shortcode->get_content();
    }

    /**
     * List multiple videos shortcode.
     *
     * @param array $atts Attributes.
     * @return string
     */
    public static function videos( $atts ) {
        $atts = (array) $atts;
        $type = 'videos';

        // Allow list video based on specific cases.
        if ( isset( $atts['top_rated'] ) && masvideos_string_to_bool( $atts['top_rated'] ) ) {
            $type = 'top_rated_videos';
        }

        if ( isset( $atts['featured'] ) && masvideos_string_to_bool( $atts['featured'] ) ) {
            $type = 'featured_videos';
            $atts['visibility'] = 'featured';
        }

        if( isset( $atts['className'] ) ) {
            $atts['class'] = $atts['className'];
            unset( $atts['className'] );
        }

        $shortcode = new MasVideos_Shortcode_Videos( $atts, $type );

        return $shortcode->get_content();
    }

    /**
     * List multiple movies shortcode.
     *
     * @param array $atts Attributes.
     * @return string
     */
    public static function movies( $atts ) {
        $atts = (array) $atts;
        $type = 'movies';

        // Allow list movie based on specific cases.
        if ( isset( $atts['top_rated'] ) && masvideos_string_to_bool( $atts['top_rated'] ) ) {
            $type = 'top_rated_movies';
        }

        if ( isset( $atts['featured'] ) && masvideos_string_to_bool( $atts['featured'] ) ) {
            $type = 'featured_movies';
            $atts['visibility'] = 'featured';
        }

        if( isset( $atts['className'] ) ) {
            $atts['class'] = $atts['className'];
            unset( $atts['className'] );
        }

        $shortcode = new MasVideos_Shortcode_Movies( $atts, $type );

        return $shortcode->get_content();
    }
}
