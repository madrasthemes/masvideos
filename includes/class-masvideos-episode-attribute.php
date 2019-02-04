<?php
/**
 * Represents a attribute
 *
 * Attributes can be global (taxonomy based) or local to the itself.
 * Uses ArrayAccess to be BW compatible with previous ways of reading attributes.
 *
 * @package MasVideos/Classes
 * @version 1.0.0
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Attribute class.
 */
class MasVideos_Episode_Attribute extends MasVideos_Attribute {

    /**
     * Get taxonomy object.
     *
     * @return array|null
     */
    public function get_taxonomy_object() {
        global $masvideos_attributes;
        return $this->is_taxonomy() ? $masvideos_attributes['episode'][ $this->get_name() ] : null;
    }
}
