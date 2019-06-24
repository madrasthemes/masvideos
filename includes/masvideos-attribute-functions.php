<?php
/**
 * MasVideos Attribute Functions
 *
 * @package MasVideos/Functions
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gets text attributes from a string.
 *
 * @since  1.0.0
 * @param string $raw_attributes Raw attributes.
 * @return array
 */
function masvideos_get_text_attributes( $raw_attributes ) {
    return array_filter( array_map( 'trim', explode( MASVIDEOS_DELIMITER, html_entity_decode( $raw_attributes, ENT_QUOTES, get_bloginfo( 'charset' ) ) ) ), 'masvideos_get_text_attributes_filter_callback' );
}

/**
 * See if an attribute is actually valid.
 *
 * @since  1.0.0
 * @param  string $value Value.
 * @return bool
 */
function masvideos_get_text_attributes_filter_callback( $value ) {
    return '' !== $value;
}

/**
 * Implode an array of attributes using MASVIDEOS_DELIMITER.
 *
 * @since  1.0.0
 * @param  array $attributes Attributes list.
 * @return string
 */
function masvideos_implode_text_attributes( $attributes ) {
    return implode( ' ' . MASVIDEOS_DELIMITER . ' ', $attributes );
}

/**
 * Get attribute taxonomies.
 *
 * @return array of objects
 */
function masvideos_get_attribute_taxonomies( $post_type = '' ) {
    $transient_name = 'masvideos_attribute_taxonomies';
    if( ! empty( $post_type ) ) {
        $transient_name = "masvideos_{$post_type}_attribute_taxonomies";
    }
    $attribute_taxonomies = get_transient( $transient_name );

    if ( false === $attribute_taxonomies ) {
        global $wpdb;

        $where = "attribute_name != ''";
        if( ! empty( $post_type ) ) {
            $where .= " AND post_type = '{$post_type}'";
        }
        $attribute_taxonomies = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}masvideos_attribute_taxonomies WHERE {$where} ORDER BY attribute_name ASC;" );

        set_transient( $transient_name, $attribute_taxonomies );
    }

    return (array) array_filter( apply_filters( $transient_name, $attribute_taxonomies ) );
}

/**
 * Get a attribute name.
 *
 * @param string $attribute_name Attribute name.
 * @return string
 */
function masvideos_attribute_taxonomy_name( $post_type, $attribute_name ) {
    return $attribute_name ? $post_type . '_' . masvideos_sanitize_taxonomy_name( $attribute_name ) : '';
}

/**
 * Get a attribute post_type by ID.
 *
 * @since  1.0.0
 * @param int $attribute_id Attribute ID.
 * @return string Return an empty string if attribute doesn't exist.
 */
function masvideos_attribute_post_type_by_id( $attribute_id ) {
    global $wpdb;

    $data = $wpdb->get_row(
        $wpdb->prepare(
            "
        SELECT post_type
        FROM {$wpdb->prefix}masvideos_attribute_taxonomies
        WHERE attribute_id = %d
    ", $attribute_id
        )
    );

    if ( $data && ! is_wp_error( $data ) ) {
        return $data->post_type;
    }

    return '';
}

/**
 * Get a attribute name by ID.
 *
 * @since  1.0.0
 * @param int $attribute_id Attribute ID.
 * @return string Return an empty string if attribute doesn't exist.
 */
function masvideos_attribute_taxonomy_name_by_id( $attribute_id ) {
    global $wpdb;

    $data = $wpdb->get_row(
        $wpdb->prepare(
            "
        SELECT attribute_name, post_type
        FROM {$wpdb->prefix}masvideos_attribute_taxonomies
        WHERE attribute_id = %d
    ", $attribute_id
        )
    );

    if ( $data && ! is_wp_error( $data ) ) {
        $attribute_name = $data->attribute_name;
        $post_type = $data->post_type;
        return masvideos_attribute_taxonomy_name( $post_type, $attribute_name );
    }

    return '';
}

/**
 * Get a attribute ID by name.
 *
 * @since  1.0.0
 * @param string $post_type post type name.
 * @param string $name Attribute name.
 * @return int
 */
function masvideos_attribute_taxonomy_id_by_name( $post_type, $name ) {
    $name       = str_replace( $post_type . '_', '', masvideos_sanitize_taxonomy_name( $name ) );
    $taxonomies = wp_list_pluck( masvideos_get_attribute_taxonomies( $post_type ), 'attribute_id', 'attribute_name' );

    return isset( $taxonomies[ $name ] ) ? (int) $taxonomies[ $name ] : 0;
}

/**
 * Get a person attributes label.
 *
 * @param string $name Attribute name.
 * @param MasVideos_Person $person Person data.
 * @return string
 */
function masvideos_person_attribute_label( $name, $person = '' ) {
    if ( taxonomy_is_person_attribute( $name ) ) {
        $name       = masvideos_sanitize_taxonomy_name( str_replace( 'person_', '', $name ) );
        $all_labels = wp_list_pluck( masvideos_get_attribute_taxonomies( 'person' ), 'attribute_label', 'attribute_name' );
        $label      = isset( $all_labels[ $name ] ) ? $all_labels[ $name ] : $name;
    } elseif ( $person ) {
        $attributes = array();

        if ( false !== $person ) {
            $attributes = $person->get_attributes();
        }

        // Attempt to get label from person, as entered by the user.
        if ( $attributes && isset( $attributes[ sanitize_title( $name ) ] ) ) {
            $label = $attributes[ sanitize_title( $name ) ]->get_name();
        } else {
            $label = $name;
        }
    } else {
        $label = $name;
    }

    return apply_filters( 'masvideos_person_attribute_label', $label, $name, $person );
}

/**
 * Get a episode attributes label.
 *
 * @param string $name Attribute name.
 * @param MasVideos_Episode $episode Episode data.
 * @return string
 */
function masvideos_episode_attribute_label( $name, $episode = '' ) {
    if ( taxonomy_is_episode_attribute( $name ) ) {
        $name       = masvideos_sanitize_taxonomy_name( str_replace( 'episode_', '', $name ) );
        $all_labels = wp_list_pluck( masvideos_get_attribute_taxonomies( 'episode' ), 'attribute_label', 'attribute_name' );
        $label      = isset( $all_labels[ $name ] ) ? $all_labels[ $name ] : $name;
    } elseif ( $episode ) {
        $attributes = array();

        if ( false !== $episode ) {
            $attributes = $episode->get_attributes();
        }

        // Attempt to get label from episode, as entered by the user.
        if ( $attributes && isset( $attributes[ sanitize_title( $name ) ] ) ) {
            $label = $attributes[ sanitize_title( $name ) ]->get_name();
        } else {
            $label = $name;
        }
    } else {
        $label = $name;
    }

    return apply_filters( 'masvideos_episode_attribute_label', $label, $name, $episode );
}

/**
 * Get a tv show attributes label.
 *
 * @param string $name Attribute name.
 * @param MasVideos_TV_Show $tv_show TV Show data.
 * @return string
 */
function masvideos_tv_show_attribute_label( $name, $tv_show = '' ) {
    if ( taxonomy_is_tv_show_attribute( $name ) ) {
        $name       = masvideos_sanitize_taxonomy_name( str_replace( 'tv_show_', '', $name ) );
        $all_labels = wp_list_pluck( masvideos_get_attribute_taxonomies( 'tv_show' ), 'attribute_label', 'attribute_name' );
        $label      = isset( $all_labels[ $name ] ) ? $all_labels[ $name ] : $name;
    } elseif ( $tv_show ) {
        $attributes = array();

        if ( false !== $tv_show ) {
            $attributes = $tv_show->get_attributes();
        }

        // Attempt to get label from tv_show, as entered by the user.
        if ( $attributes && isset( $attributes[ sanitize_title( $name ) ] ) ) {
            $label = $attributes[ sanitize_title( $name ) ]->get_name();
        } else {
            $label = $name;
        }
    } else {
        $label = $name;
    }

    return apply_filters( 'masvideos_tv_show_attribute_label', $label, $name, $tv_show );
}

/**
 * Get a video attributes label.
 *
 * @param string $name Attribute name.
 * @param MasVideos_Video $video Video data.
 * @return string
 */
function masvideos_video_attribute_label( $name, $video = '' ) {
    if ( taxonomy_is_video_attribute( $name ) ) {
        $name       = masvideos_sanitize_taxonomy_name( str_replace( 'video_', '', $name ) );
        $all_labels = wp_list_pluck( masvideos_get_attribute_taxonomies( 'video' ), 'attribute_label', 'attribute_name' );
        $label      = isset( $all_labels[ $name ] ) ? $all_labels[ $name ] : $name;
    } elseif ( $video ) {
        $attributes = array();

        if ( false !== $video ) {
            $attributes = $video->get_attributes();
        }

        // Attempt to get label from video, as entered by the user.
        if ( $attributes && isset( $attributes[ sanitize_title( $name ) ] ) ) {
            $label = $attributes[ sanitize_title( $name ) ]->get_name();
        } else {
            $label = $name;
        }
    } else {
        $label = $name;
    }

    return apply_filters( 'masvideos_video_attribute_label', $label, $name, $video );
}

/**
 * Get a movie attributes label.
 *
 * @param string $name Attribute name.
 * @param MasVideos_Movie $movie Movie data.
 * @return string
 */
function masvideos_movie_attribute_label( $name, $movie = '' ) {
    if ( taxonomy_is_movie_attribute( $name ) ) {
        $name       = masvideos_sanitize_taxonomy_name( str_replace( 'movie_', '', $name ) );
        $all_labels = wp_list_pluck( masvideos_get_attribute_taxonomies( 'movie' ), 'attribute_label', 'attribute_name' );
        $label      = isset( $all_labels[ $name ] ) ? $all_labels[ $name ] : $name;
    } elseif ( $movie ) {
        $attributes = array();

        if ( false !== $movie ) {
            $attributes = $movie->get_attributes();
        }

        // Attempt to get label from movie, as entered by the user.
        if ( $attributes && isset( $attributes[ sanitize_title( $name ) ] ) ) {
            $label = $attributes[ sanitize_title( $name ) ]->get_name();
        } else {
            $label = $name;
        }
    } else {
        $label = $name;
    }

    return apply_filters( 'masvideos_movie_attribute_label', $label, $name, $movie );
}

/**
 * Get a attribute orderby setting.
 *
 * @param string $post_type post type name.
 * @param string $name Attribute name.
 * @return string
 */
function masvideos_attribute_orderby( $post_type, $name ) {
    global $masvideos_attributes, $wpdb;

    $name = str_replace( $post_type . '_', '', sanitize_title( $name ) );

    if ( isset( $masvideos_attributes[ $post_type . '_' . $name ] ) ) {
        $orderby = $masvideos_attributes[ $post_type . '_' . $name ]->attribute_orderby;
    } else {
        $orderby = $wpdb->get_var( $wpdb->prepare( "SELECT attribute_orderby FROM {$wpdb->prefix}masvideos_attribute_taxonomies WHERE post_type = %s AND attribute_name = %s;", $post_type, $name ) );
    }

    return apply_filters( 'masvideos_attribute_orderby', $orderby, $name );
}

/**
 * Get an array of attribute taxonomies.
 *
 * @return array
 */
function masvideos_get_attribute_taxonomy_names( $post_type = '' ) {
    $taxonomy_names       = array();
    $attribute_taxonomies = masvideos_get_attribute_taxonomies( $post_type );
    if ( ! empty( $attribute_taxonomies ) ) {
        foreach ( $attribute_taxonomies as $tax ) {
            $taxonomy_names[] = masvideos_attribute_taxonomy_name( $tax->post_type, $tax->attribute_name );
        }
    }
    return $taxonomy_names;
}

/**
 * Get attribute types.
 *
 * @since  1.0.0
 * @return array
 */
function masvideos_get_attribute_types() {
    return (array) apply_filters(
        'masvideos_attributes_type_selector', array(
            'select' => esc_html__( 'Select', 'masvideos' ),
        )
    );
}

/**
 * Check if there are custom attribute types.
 *
 * @since  1.0.0
 * @return bool True if there are custom types, otherwise false.
 */
function masvideos_has_custom_attribute_types() {
    $types = masvideos_get_attribute_types();

    return 1 < count( $types ) || ! array_key_exists( 'select', $types );
}

/**
 * Get attribute type label.
 *
 * @since  1.0.0
 * @param  string $type Attribute type slug.
 * @return string
 */
function masvideos_get_attribute_type_label( $type ) {
    $types = masvideos_get_attribute_types();

    return isset( $types[ $type ] ) ? $types[ $type ] : esc_html__( 'Select', 'masvideos' );
}

/**
 * Check if attribute name is reserved.
 * https://codex.wordpress.org/Function_Reference/register_taxonomy#Reserved_Terms.
 *
 * @since  1.0.0
 * @param  string $attribute_name Attribute name.
 * @return bool
 */
function masvideos_check_if_attribute_name_is_reserved( $attribute_name ) {
    // Forbidden attribute names.
    $reserved_terms = array(
        'attachment',
        'attachment_id',
        'author',
        'author_name',
        'calendar',
        'cat',
        'category',
        'category__and',
        'category__in',
        'category__not_in',
        'category_name',
        'comments_per_page',
        'comments_popup',
        'cpage',
        'day',
        'debug',
        'error',
        'exact',
        'feed',
        'hour',
        'link_category',
        'm',
        'minute',
        'monthnum',
        'more',
        'name',
        'nav_menu',
        'nopaging',
        'offset',
        'order',
        'orderby',
        'p',
        'page',
        'page_id',
        'paged',
        'pagename',
        'pb',
        'perm',
        'post',
        'post__in',
        'post__not_in',
        'post_format',
        'post_mime_type',
        'post_status',
        'post_tag',
        'post_type',
        'posts',
        'posts_per_archive_page',
        'posts_per_page',
        'preview',
        'robots',
        's',
        'search',
        'second',
        'sentence',
        'showposts',
        'static',
        'subpost',
        'subpost_id',
        'tag',
        'tag__and',
        'tag__in',
        'tag__not_in',
        'tag_id',
        'tag_slug__and',
        'tag_slug__in',
        'taxonomy',
        'tb',
        'term',
        'type',
        'w',
        'withcomments',
        'withoutcomments',
        'year',
    );

    return in_array( $attribute_name, $reserved_terms, true );
}

/**
 * Callback for person array filter to get visible only.
 *
 * @since  1.0.0
 * @param  MasVideos_Person_Attribute $attribute Attribute data.
 * @return bool
 */
function masvideos_attributes_person_array_filter_visible( $attribute ) {
    $class_name = 'MasVideos_Person_Attribute';

    return $class_name && $attribute && is_a( $attribute, $class_name ) && $attribute->get_visible() && ( ! $attribute->is_taxonomy() || taxonomy_exists( $attribute->get_name() ) );
}

/**
 * Callback for episode array filter to get visible only.
 *
 * @since  1.0.0
 * @param  MasVideos_Episode_Attribute $attribute Attribute data.
 * @return bool
 */
function masvideos_attributes_episode_array_filter_visible( $attribute ) {
    $class_name = 'MasVideos_Episode_Attribute';

    return $class_name && $attribute && is_a( $attribute, $class_name ) && $attribute->get_visible() && ( ! $attribute->is_taxonomy() || taxonomy_exists( $attribute->get_name() ) );
}

/**
 * Callback for tv show array filter to get visible only.
 *
 * @since  1.0.0
 * @param  MasVideos_TV_Show_Attribute $attribute Attribute data.
 * @return bool
 */
function masvideos_attributes_tv_show_array_filter_visible( $attribute ) {
    $class_name = 'MasVideos_TV_Show_Attribute';

    return $class_name && $attribute && is_a( $attribute, $class_name ) && $attribute->get_visible() && ( ! $attribute->is_taxonomy() || taxonomy_exists( $attribute->get_name() ) );
}

/**
 * Callback for video array filter to get visible only.
 *
 * @since  1.0.0
 * @param  MasVideos_Video_Attribute $attribute Attribute data.
 * @return bool
 */
function masvideos_attributes_video_array_filter_visible( $attribute ) {
    $class_name = 'MasVideos_Video_Attribute';

    return $class_name && $attribute && is_a( $attribute, $class_name ) && $attribute->get_visible() && ( ! $attribute->is_taxonomy() || taxonomy_exists( $attribute->get_name() ) );
}

/**
 * Callback for movie array filter to get visible only.
 *
 * @since  1.0.0
 * @param  MasVideos_Movie_Attribute $attribute Attribute data.
 * @return bool
 */
function masvideos_attributes_movie_array_filter_visible( $attribute ) {
    $class_name = 'MasVideos_Movie_Attribute';

    return $class_name && $attribute && is_a( $attribute, $class_name ) && $attribute->get_visible() && ( ! $attribute->is_taxonomy() || taxonomy_exists( $attribute->get_name() ) );
}

/**
 * Callback for array filter to get default attributes.  Will allow for '0' string values, but regard all other
 * class PHP FALSE equivalents normally.
 *
 * @since 1.0.0
 * @param mixed $attribute Attribute being considered for exclusion from parent array.
 * @return bool
 */
function masvideos_array_filter_default_attributes( $attribute ) {
    return ( ! empty( $attribute ) || '0' === $attribute );
}

/**
 * Get attribute data by ID.
 *
 * @since  1.0.0
 * @param  int $id Attribute ID.
 * @return stdClass|null
 */
function masvideos_get_attribute( $id ) {
    global $wpdb;

    $data = $wpdb->get_row(
        $wpdb->prepare(
            "
        SELECT *
        FROM {$wpdb->prefix}masvideos_attribute_taxonomies
        WHERE attribute_id = %d
     ", $id
        )
    );

    if ( is_wp_error( $data ) || is_null( $data ) ) {
        return null;
    }

    $attribute               = new stdClass();
    $attribute->id           = (int) $data->attribute_id;
    $attribute->name         = $data->attribute_label;
    $attribute->slug         = masvideos_attribute_taxonomy_name( $data->post_type, $data->attribute_name );
    $attribute->type         = $data->attribute_type;
    $attribute->order_by     = $data->attribute_orderby;
    $attribute->has_archives = (bool) $data->attribute_public;
    $attribute->post_type    = $data->post_type;

    return $attribute;
}

/**
 * Create attribute.
 *
 * @since  1.0.0
 * @param  array $args Attribute arguments {
 *     Array of attribute parameters.
 *
 *     @type int    $id           Unique identifier, used to update an attribute.
 *     @type string $name         Attribute name. Always required.
 *     @type string $slug         Attribute alphanumeric identifier.
 *     @type string $type         Type of attribute.
 *                                Core by default accepts: 'select' and 'text'.
 *                                Default to 'select'.
 *     @type string $order_by     Sort order.
 *                                Accepts: 'menu_order', 'name', 'name_num' and 'id'.
 *                                Default to 'menu_order'.
 *     @type bool   $has_archives Enable or disable attribute archives. False by default.
 *     @type string $post_type    post_type.
 * }
 * @return int|WP_Error
 */
function masvideos_create_attribute( $args ) {
    global $wpdb;

    $args   = wp_unslash( $args );
    $id     = ! empty( $args['id'] ) ? intval( $args['id'] ) : 0;
    $format = array( '%s', '%s', '%s', '%s', '%d', '%s' );

    // Name is required.
    if ( empty( $args['name'] ) ) {
        return new WP_Error( 'missing_attribute_name', __( 'Please, provide an attribute name.', 'masvideos' ), array( 'status' => 400 ) );
    }

    // Set the attribute slug.
    if ( empty( $args['slug'] ) ) {
        $slug = masvideos_sanitize_taxonomy_name( $args['name'] );
    } else {
        $slug = preg_replace( '/^'. $args['post_type'] .'\_/', '', masvideos_sanitize_taxonomy_name( $args['slug'] ) );
    }

    // Validate slug.
    if ( strlen( $slug ) >= 28 ) {
        /* translators: %s: attribute slug */
        return new WP_Error( 'invalid_masvideos_attribute_slug_too_long', sprintf( __( 'Slug "%s" is too long (28 characters max). Shorten it, please.', 'masvideos' ), $slug ), array( 'status' => 400 ) );
    } elseif ( masvideos_check_if_attribute_name_is_reserved( $slug ) ) {
        /* translators: %s: attribute slug */
        return new WP_Error( 'invalid_masvideos_attribute_slug_reserved_name', sprintf( __( 'Slug "%s" is not allowed because it is a reserved term. Change it, please.', 'masvideos' ), $slug ), array( 'status' => 400 ) );
    } elseif ( ( 0 === $id && taxonomy_exists( masvideos_attribute_taxonomy_name( $args['post_type'], $slug ) ) ) || ( isset( $args['old_slug'] ) && $args['old_slug'] !== $slug && taxonomy_exists( masvideos_attribute_taxonomy_name( $args['post_type'], $slug ) ) ) ) {
        /* translators: %s: attribute slug */
        return new WP_Error( 'invalid_masvideos_attribute_slug_already_exists', sprintf( __( 'Slug "%s" is already in use. Change it, please.', 'masvideos' ), $slug ), array( 'status' => 400 ) );
    }

    // Validate type.
    if ( empty( $args['type'] ) || ! array_key_exists( $args['type'], masvideos_get_attribute_types() ) ) {
        $args['type'] = 'select';
    }

    // Validate order by.
    if ( empty( $args['order_by'] ) || ! in_array( $args['order_by'], array( 'menu_order', 'name', 'name_num', 'id' ), true ) ) {
        $args['order_by'] = 'menu_order';
    }

    $data = array(
        'attribute_label'   => $args['name'],
        'attribute_name'    => $slug,
        'attribute_type'    => $args['type'],
        'attribute_orderby' => $args['order_by'],
        'attribute_public'  => isset( $args['has_archives'] ) ? (int) $args['has_archives'] : 0,
        'post_type'         => $args['post_type'],
    );

    $post_type = $data['post_type'];

    // Create or update.
    if ( 0 === $id ) {
        $results = $wpdb->insert(
            $wpdb->prefix . 'masvideos_attribute_taxonomies',
            $data,
            $format
        );

        if ( is_wp_error( $results ) ) {
            return new WP_Error( 'cannot_create_attribute', $results->get_error_message(), array( 'status' => 400 ) );
        }

        $id = $wpdb->insert_id;

        /**
         * Attribute added.
         *
         * @param int   $id   Added attribute ID.
         * @param array $data Attribute data.
         */
        do_action( 'masvideos_attribute_added', $id, $data );
    } else {
        $results = $wpdb->update(
            $wpdb->prefix . 'masvideos_attribute_taxonomies',
            $data,
            array( 'attribute_id' => $id ),
            $format,
            array( '%d' )
        );

        if ( false === $results ) {
            return new WP_Error( 'cannot_update_attribute', __( 'Could not update the attribute.', 'masvideos' ), array( 'status' => 400 ) );
        }

        // Set old slug to check for database changes.
        $old_slug = ! empty( $args['old_slug'] ) ? masvideos_sanitize_taxonomy_name( $args['old_slug'] ) : $slug;

        /**
         * Attribute updated.
         *
         * @param int    $id       Added attribute ID.
         * @param array  $data     Attribute data.
         * @param string $old_slug Attribute old name.
         */
        do_action( 'masvideos_attribute_updated', $id, $data, $old_slug );

        if ( $old_slug !== $slug ) {
            // Update taxonomies in the wp term taxonomy table.
            $wpdb->update(
                $wpdb->term_taxonomy,
                array( 'taxonomy' => masvideos_attribute_taxonomy_name( $post_type, $data['attribute_name'] ) ),
                array( 'taxonomy' => $post_type . $old_slug )
            );

            // Update taxonomy ordering term meta.
            $table_name = get_option( 'db_version' ) < 34370 ? $wpdb->prefix . 'masvideos_termmeta' : $wpdb->termmeta;
            $wpdb->update(
                $table_name,
                array( 'meta_key' => 'order_' . $post_type . '_' . sanitize_title( $data['attribute_name'] ) ), // WPCS: slow query ok.
                array( 'meta_key' => 'order_' . $post_type . '_' . sanitize_title( $old_slug ) ) // WPCS: slow query ok.
            );

            // Update attributes which use this taxonomy.
            $old_attribute_name_length = strlen( $old_slug ) + 3;
            $attribute_name_length     = strlen( $data['attribute_name'] ) + 3;

            $wpdb->query(
                $wpdb->prepare(
                    "UPDATE {$wpdb->postmeta} SET meta_value = REPLACE( meta_value, %s, %s ) WHERE meta_key = '_" . $post_type . "_attributes'",
                    's:' . $old_attribute_name_length . ':"' . $post_type . '_' . $old_slug . '"',
                    's:' . $attribute_name_length . ':"' . $post_type . '_' . $data['attribute_name'] . '"'
                )
            );

            // Update variations which use this taxonomy.
            $wpdb->update(
                $wpdb->postmeta,
                array( 'meta_key' => 'attribute_' . $post_type . '_' . sanitize_title( $data['attribute_name'] ) ), // WPCS: slow query ok.
                array( 'meta_key' => 'attribute_' . $post_type . '_' . sanitize_title( $old_slug ) ) // WPCS: slow query ok.
            );
        }
    }

    // Clear cache and flush rewrite rules.
    wp_schedule_single_event( time(), 'masvideos_flush_rewrite_rules' );
    delete_transient( 'masvideos_attribute_taxonomies' );
    delete_transient( "masvideos_{$post_type}_attribute_taxonomies" );

    return $id;
}

/**
 * Update an attribute.
 *
 * For available args see masvideos_create_attribute().
 *
 * @since  1.0.0
 * @param  int   $id   Attribute ID.
 * @param  array $args Attribute arguments.
 * @return int|WP_Error
 */
function masvideos_update_attribute( $id, $args ) {
    global $wpdb;

    $attribute = masvideos_get_attribute( $id );

    $args['id'] = $attribute ? $attribute->id : 0;

    if ( $args['id'] && empty( $args['name'] ) ) {
        $args['name'] = $attribute->name;
    }

    $args['old_slug'] = $wpdb->get_var(
        $wpdb->prepare(
            "
                SELECT attribute_name
                FROM {$wpdb->prefix}masvideos_attribute_taxonomies
                WHERE attribute_id = %d
            ", $args['id']
        )
    );

    return masvideos_create_attribute( $args );
}

/**
 * Delete attribute by ID.
 *
 * @since  1.0.0
 * @param  int $id Attribute ID.
 * @return bool
 */
function masvideos_delete_attribute( $id ) {
    global $wpdb;

    $data = $wpdb->get_row(
        $wpdb->prepare(
            "
        SELECT attribute_name, post_type
        FROM {$wpdb->prefix}masvideos_attribute_taxonomies
        WHERE attribute_id = %d
    ", $id
        )
    );

    $name = $data->attribute_name;
    $post_type = $data->post_type;

    $taxonomy = masvideos_attribute_taxonomy_name( $post_type, $name );

    /**
     * Before deleting an attribute.
     *
     * @param int    $id       Attribute ID.
     * @param string $name     Attribute name.
     * @param string $taxonomy Attribute taxonomy name.
     */
    do_action( 'masvideos_before_attribute_delete', $id, $name, $taxonomy );

    if ( $name && $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}masvideos_attribute_taxonomies WHERE attribute_id = %d", $id ) ) ) {
        if ( taxonomy_exists( $taxonomy ) ) {
            $terms = get_terms( $taxonomy, 'orderby=name&hide_empty=0' );
            foreach ( $terms as $term ) {
                wp_delete_term( $term->term_id, $taxonomy );
            }
        }

        /**
         * After deleting an attribute.
         *
         * @param int    $id       Attribute ID.
         * @param string $name     Attribute name.
         * @param string $taxonomy Attribute taxonomy name.
         */
        do_action( 'masvideos_attribute_deleted', $id, $name, $taxonomy );
        wp_schedule_single_event( time(), 'masvideos_flush_rewrite_rules' );
        delete_transient( 'masvideos_attribute_taxonomies' );
        delete_transient( "masvideos_{$post_type}_attribute_taxonomies" );

        return true;
    }

    return false;
}

/**
 * Used to sort movies attributes with uasort.
 *
 * @since 1.0.0
 * @param array $a First attribute to compare.
 * @param array $b Second attribute to compare.
 * @return int
 */
function masvideos_attribute_uasort_comparison( $a, $b ) {
    if ( $a['position'] === $b['position'] ) {
        return 0;
    }
    return ( $a['position'] < $b['position'] ) ? -1 : 1;
}