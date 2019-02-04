<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div id="tv_show_attributes" class="panel masvideos-metaboxes-wrapper hidden">
    <div class="toolbar toolbar-top">
        <span class="expand-close">
            <a href="#" class="expand_all"><?php esc_html_e( 'Expand', 'masvideos' ); ?></a> / <a href="#" class="close_all"><?php esc_html_e( 'Close', 'masvideos' ); ?></a>
        </span>
        <select name="attribute_taxonomy" class="attribute_taxonomy">
            <option value=""><?php esc_html_e( 'Custom tv show attribute', 'masvideos' ); ?></option>
            <?php
            global $masvideos_attributes;

            // Array of defined attribute taxonomies.
            $attribute_taxonomies = masvideos_get_attribute_taxonomies( 'tv_show' );

            if ( ! empty( $attribute_taxonomies ) ) {
                foreach ( $attribute_taxonomies as $tax ) {
                    $attribute_taxonomy_name = masvideos_attribute_taxonomy_name( $tax->post_type, $tax->attribute_name );
                    $label                   = $tax->attribute_label ? $tax->attribute_label : $tax->attribute_name;
                    echo '<option value="' . esc_attr( $attribute_taxonomy_name ) . '">' . esc_html( $label ) . '</option>';
                }
            }
            ?>
        </select>
        <button type="button" class="button add_attribute_tv_show"><?php esc_html_e( 'Add', 'masvideos' ); ?></button>
    </div>
    <div class="tv_show_attributes masvideos-metaboxes">
        <?php
        // TV Show attributes - taxonomies and custom, ordered, with visibility.
        $attributes = $tv_show_object->get_attributes( 'edit' );
        $i          = -1;

        foreach ( $attributes as $attribute ) {
            $i++;
            $metabox_class = array();

            if ( $attribute->is_taxonomy() ) {
                $metabox_class[] = 'taxonomy';
                $metabox_class[] = $attribute->get_name();
            }

            include 'html-tv-show-attribute.php';
        }
        ?>
    </div>
    <div class="toolbar">
        <span class="expand-close">
            <a href="#" class="expand_all"><?php esc_html_e( 'Expand', 'masvideos' ); ?></a> / <a href="#" class="close_all"><?php esc_html_e( 'Close', 'masvideos' ); ?></a>
        </span>
        <button type="button" class="button save_attributes_tv_show button-primary"><?php esc_html_e( 'Save attributes', 'masvideos' ); ?></button>
    </div>
    <?php do_action( 'masvideos_tv_show_options_attributes' ); ?>
</div>
