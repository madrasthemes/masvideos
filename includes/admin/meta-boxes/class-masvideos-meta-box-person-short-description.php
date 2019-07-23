<?php
/**
 * Person Short Description
 *
 * Replaces the standard excerpt box.
 *
 * @package     MasVideos/Admin/Meta Boxes
 * @version     1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * MasVideos_Meta_Box_Person_Short_Description Class.
 */
class MasVideos_Meta_Box_Person_Short_Description {

    /**
     * Output the metabox.
     *
     * @param WP_Post $post Post object.
     */
    public static function output( $post ) {

        $settings = array(
            'textarea_name' => 'excerpt',
            'quicktags'     => array( 'buttons' => 'em,strong,link' ),
            'tinymce'       => array(
                'theme_advanced_buttons1' => 'bold,italic,strikethrough,separator,bullist,numlist,separator,blockquote,separator,justifyleft,justifycenter,justifyright,separator,link,unlink,separator,undo,redo,separator',
                'theme_advanced_buttons2' => '',
            ),
            'editor_css'    => '<style>#wp-excerpt-editor-container .wp-editor-area{height:175px; width:100%;}</style>',
        );

        wp_editor( htmlspecialchars_decode( $post->post_excerpt, ENT_QUOTES ), 'excerpt', apply_filters( 'masvideos_person_short_description_editor_settings', $settings ) );
    }
}
