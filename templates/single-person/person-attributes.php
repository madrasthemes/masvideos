<?php
/**
 * Person attributes
 *
 * Used by list_attributes() in the persons class.
 *
 * This template can be overridden by copying it to yourtheme/masvideos/single-person/person-attributes.php.
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

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if( empty( $attributes ) ) {
    return;
}
?>
<table class="person__attributes">

    <?php foreach ( $attributes as $attribute ) : ?>
        <tr>
            <th><?php echo masvideos_person_attribute_label( $attribute->get_name() ); ?></th>
            <td><?php
                $values = array();

                if ( $attribute->is_taxonomy() ) {
                    $attribute_taxonomy = $attribute->get_taxonomy_object();
                    $attribute_values = masvideos_get_person_terms( $person->get_id(), $attribute->get_name(), array( 'fields' => 'all' ) );

                    foreach ( $attribute_values as $attribute_value ) {
                        $value_name = esc_html( $attribute_value->name );

                        if ( $attribute_taxonomy->attribute_public ) {
                            $values[] = '<a href="' . esc_url( get_term_link( $attribute_value->term_id, $attribute->get_name() ) ) . '" rel="tag">' . $value_name . '</a>';
                        } else {
                            $values[] = $value_name;
                        }
                    }
                } else {
                    $values = $attribute->get_options();

                    foreach ( $values as &$value ) {
                        $value = make_clickable( esc_html( $value ) );
                    }
                }

                echo apply_filters( 'masvideos_person_attribute', wpautop( wptexturize( implode( ', ', $values ) ) ), $attribute, $values );
            ?></td>
        </tr>
    <?php endforeach; ?>
</table>
