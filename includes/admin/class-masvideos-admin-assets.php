<?php
/**
 * Load assets
 *
 * @package     MasVideos/Admin
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'MasVideos_Admin_Assets', false ) ) :

    /**
     * MasVideos_Admin_Assets Class.
     */
    class MasVideos_Admin_Assets {

        /**
         * Hook in tabs.
         */
        public function __construct() {
            add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
        }

        /**
         * Enqueue styles.
         */
        public function admin_styles() {
            global $wp_scripts;

            $screen    = get_current_screen();
            $screen_id = $screen ? $screen->id : '';
            $suffix    = '';

            // Register admin styles.
            // wp_register_style( 'masvideos_admin_menu_styles', MasVideos()->plugin_url() . '/assets/css/menu.css', array(), MASVIDEOS_VERSION );
            wp_register_style( 'masvideos_admin_styles', MasVideos()->plugin_url() . '/assets/css/admin' . $suffix . '.css', array(), MASVIDEOS_VERSION );
            wp_register_style( 'jquery-ui-style', MasVideos()->plugin_url() . '/assets/css/jquery-ui/jquery-ui.min.css', array(), MASVIDEOS_VERSION );
            // wp_register_style( 'masvideos_admin_dashboard_styles', MasVideos()->plugin_url() . '/assets/css/dashboard.css', array(), MASVIDEOS_VERSION );
            // wp_register_style( 'masvideos_admin_print_reports_styles', MasVideos()->plugin_url() . '/assets/css/reports-print.css', array(), MASVIDEOS_VERSION, 'print' );

            // Add RTL support for admin styles.
            // wp_style_add_data( 'masvideos_admin_menu_styles', 'rtl', 'replace' );
            wp_style_add_data( 'masvideos_admin_styles', 'rtl', 'replace' );
            // wp_style_add_data( 'masvideos_admin_dashboard_styles', 'rtl', 'replace' );
            // wp_style_add_data( 'masvideos_admin_print_reports_styles', 'rtl', 'replace' );

            // Sitewide menu CSS.
            // wp_enqueue_style( 'masvideos_admin_menu_styles' );

            // Admin styles for MasVideos pages only.
            if ( in_array( $screen_id, masvideos_get_screen_ids() ) ) {
                wp_enqueue_style( 'masvideos_admin_styles' );
                wp_enqueue_style( 'jquery-ui-style' );
                wp_enqueue_style( 'wp-color-picker' );
            }

            // if ( in_array( $screen_id, array( 'dashboard' ) ) ) {
            //     wp_enqueue_style( 'masvideos_admin_dashboard_styles' );
            // }

            // if ( in_array( $screen_id, array( 'masvideos_page_wc-reports', 'toplevel_page_wc-reports' ) ) ) {
            //     wp_enqueue_style( 'masvideos_admin_print_reports_styles' );
            // }
        }


        /**
         * Enqueue scripts.
         */
        public function admin_scripts() {
            global $wp_query, $post;

            $screen       = get_current_screen();
            $screen_id    = $screen ? $screen->id : '';
            $wc_screen_id = sanitize_title( __( 'MasVideos', 'masvideos' ) );
            $suffix       = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

            // Register scripts.
            // wp_register_script( 'masvideos_admin', MasVideos()->plugin_url() . '/assets/js/admin/masvideos_admin' . $suffix . '.js', array( 'jquery', 'jquery-blockui', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip' ), MASVIDEOS_VERSION );
            // wp_register_script( 'jquery-blockui', MasVideos()->plugin_url() . '/assets/js/jquery-blockui/jquery.blockUI' . $suffix . '.js', array( 'jquery' ), '2.70', true );
            wp_register_script( 'jquery-tiptip', MasVideos()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip' . $suffix . '.js', array( 'jquery' ), MASVIDEOS_VERSION, true );
            wp_register_script( 'round', MasVideos()->plugin_url() . '/assets/js/round/round' . $suffix . '.js', array( 'jquery' ), MASVIDEOS_VERSION );
            wp_register_script( 'masvideos-admin-meta-boxes', MasVideos()->plugin_url() . '/assets/js/admin/meta-boxes' . $suffix . '.js', array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-sortable', 'round', 'masvideos-enhanced-select', 'plupload-all', 'stupidtable', 'jquery-tiptip' ), MASVIDEOS_VERSION );
            // wp_register_script( 'zeroclipboard', MasVideos()->plugin_url() . '/assets/js/zeroclipboard/jquery.zeroclipboard' . $suffix . '.js', array( 'jquery' ), MASVIDEOS_VERSION );
            // wp_register_script( 'qrcode', MasVideos()->plugin_url() . '/assets/js/jquery-qrcode/jquery.qrcode' . $suffix . '.js', array( 'jquery' ), MASVIDEOS_VERSION );
            wp_register_script( 'stupidtable', MasVideos()->plugin_url() . '/assets/js/stupidtable/stupidtable' . $suffix . '.js', array( 'jquery' ), MASVIDEOS_VERSION );
            // wp_register_script( 'serializejson', MasVideos()->plugin_url() . '/assets/js/jquery-serializejson/jquery.serializejson' . $suffix . '.js', array( 'jquery' ), '2.8.1' );
            // wp_register_script( 'flot', MasVideos()->plugin_url() . '/assets/js/jquery-flot/jquery.flot' . $suffix . '.js', array( 'jquery' ), MASVIDEOS_VERSION );
            // wp_register_script( 'flot-resize', MasVideos()->plugin_url() . '/assets/js/jquery-flot/jquery.flot.resize' . $suffix . '.js', array( 'jquery', 'flot' ), MASVIDEOS_VERSION );
            // wp_register_script( 'flot-time', MasVideos()->plugin_url() . '/assets/js/jquery-flot/jquery.flot.time' . $suffix . '.js', array( 'jquery', 'flot' ), MASVIDEOS_VERSION );
            // wp_register_script( 'flot-pie', MasVideos()->plugin_url() . '/assets/js/jquery-flot/jquery.flot.pie' . $suffix . '.js', array( 'jquery', 'flot' ), MASVIDEOS_VERSION );
            // wp_register_script( 'flot-stack', MasVideos()->plugin_url() . '/assets/js/jquery-flot/jquery.flot.stack' . $suffix . '.js', array( 'jquery', 'flot' ), MASVIDEOS_VERSION );
            // wp_register_script( 'wc-settings-tax', MasVideos()->plugin_url() . '/assets/js/admin/settings-views-html-settings-tax' . $suffix . '.js', array( 'jquery', 'wp-util', 'underscore', 'backbone', 'jquery-blockui' ), MASVIDEOS_VERSION );
            // wp_register_script( 'wc-backbone-modal', MasVideos()->plugin_url() . '/assets/js/admin/backbone-modal' . $suffix . '.js', array( 'underscore', 'backbone', 'wp-util' ), MASVIDEOS_VERSION );
            // wp_register_script( 'wc-clipboard', MasVideos()->plugin_url() . '/assets/js/admin/wc-clipboard' . $suffix . '.js', array( 'jquery' ), MASVIDEOS_VERSION );
            wp_register_script( 'select2', MasVideos()->plugin_url() . '/assets/js/select2/select2.full' . $suffix . '.js', array( 'jquery' ), '4.0.3' );
            wp_register_script( 'selectWoo', MasVideos()->plugin_url() . '/assets/js/selectWoo/selectWoo.full' . $suffix . '.js', array( 'jquery' ), '1.0.4' );
            wp_register_script( 'masvideos-enhanced-select', MasVideos()->plugin_url() . '/assets/js/admin/masvideos-enhanced-select' . $suffix . '.js', array( 'jquery', 'selectWoo' ), MASVIDEOS_VERSION );
            wp_localize_script(
                'masvideos-enhanced-select',
                'masvideos_enhanced_select_params',
                array(
                    'i18n_no_matches'           => _x( 'No matches found', 'enhanced select', 'masvideos' ),
                    'i18n_ajax_error'           => _x( 'Loading failed', 'enhanced select', 'masvideos' ),
                    'i18n_input_too_short_1'    => _x( 'Please enter 1 or more characters', 'enhanced select', 'masvideos' ),
                    'i18n_input_too_short_n'    => _x( 'Please enter %qty% or more characters', 'enhanced select', 'masvideos' ),
                    'i18n_input_too_long_1'     => _x( 'Please delete 1 character', 'enhanced select', 'masvideos' ),
                    'i18n_input_too_long_n'     => _x( 'Please delete %qty% characters', 'enhanced select', 'masvideos' ),
                    'i18n_selection_too_long_1' => _x( 'You can only select 1 item', 'enhanced select', 'masvideos' ),
                    'i18n_selection_too_long_n' => _x( 'You can only select %qty% items', 'enhanced select', 'masvideos' ),
                    'i18n_load_more'            => _x( 'Loading more results&hellip;', 'enhanced select', 'masvideos' ),
                    'i18n_searching'            => _x( 'Searching&hellip;', 'enhanced select', 'masvideos' ),
                    'ajax_url'                  => admin_url( 'admin-ajax.php' ),
                    'search_videos_nonce'       => wp_create_nonce( 'search-videos' ),
                    'search_movies_nonce'       => wp_create_nonce( 'search-movies' ),
                    'search_categories_nonce'   => wp_create_nonce( 'search-categories' ),
                )
            );

            // wp_register_script( 'accounting', MasVideos()->plugin_url() . '/assets/js/accounting/accounting' . $suffix . '.js', array( 'jquery' ), '0.4.2' );
            // wp_localize_script(
            //     'accounting',
            //     'accounting_params',
            //     array(
            //         'mon_decimal_point' => wc_get_price_decimal_separator(),
            //     )
            // );

            // MasVideos admin pages.
            // if ( in_array( $screen_id, wc_get_screen_ids() ) ) {
            //     wp_enqueue_script( 'iris' );
            //     wp_enqueue_script( 'masvideos_admin' );
            //     wp_enqueue_script( 'wc-enhanced-select' );
            //     wp_enqueue_script( 'jquery-ui-sortable' );
            //     wp_enqueue_script( 'jquery-ui-autocomplete' );

            //     $locale  = localeconv();
            //     $decimal = isset( $locale['decimal_point'] ) ? $locale['decimal_point'] : '.';

            //     $params = array(
            //         /* translators: %s: decimal */
            //         'i18n_decimal_error'                => sprintf( __( 'Please enter in decimal (%s) format without thousand separators.', 'masvideos' ), $decimal ),
            //         /* translators: %s: price decimal separator */
            //         'i18n_mon_decimal_error'            => sprintf( __( 'Please enter in monetary decimal (%s) format without thousand separators and currency symbols.', 'masvideos' ), wc_get_price_decimal_separator() ),
            //         'i18n_country_iso_error'            => __( 'Please enter in country code with two capital letters.', 'masvideos' ),
            //         'i18n_sale_less_than_regular_error' => __( 'Please enter in a value less than the regular price.', 'masvideos' ),
            //         'i18n_delete_product_notice'        => __( 'This product has produced sales and may be linked to existing orders. Are you sure you want to delete it?', 'masvideos' ),
            //         'i18n_remove_personal_data_notice'  => __( 'This action cannot be reversed. Are you sure you wish to erase personal data from the selected orders?', 'masvideos' ),
            //         'decimal_point'                     => $decimal,
            //         'mon_decimal_point'                 => wc_get_price_decimal_separator(),
            //         'ajax_url'                          => admin_url( 'admin-ajax.php' ),
            //         'strings'                           => array(
            //             'import_products' => __( 'Import', 'masvideos' ),
            //             'export_products' => __( 'Export', 'masvideos' ),
            //         ),
            //         'nonces'                            => array(
            //             'gateway_toggle' => wp_create_nonce( 'masvideos-toggle-payment-gateway-enabled' ),
            //         ),
            //         'urls'                              => array(
            //             'import_products' => current_user_can( 'import' ) ? esc_url_raw( admin_url( 'edit.php?post_type=product&page=product_importer' ) ) : null,
            //             'export_products' => current_user_can( 'export' ) ? esc_url_raw( admin_url( 'edit.php?post_type=product&page=product_exporter' ) ) : null,
            //         ),
            //     );

            //     wp_localize_script( 'masvideos_admin', 'masvideos_admin', $params );
            // }

            // Edit product category pages.
            // if ( in_array( $screen_id, array( 'edit-product_cat' ) ) ) {
            //     wp_enqueue_media();
            // }

            // Products.
            // if ( in_array( $screen_id, array( 'edit-product' ) ) ) {
            //     wp_enqueue_script( 'masvideos_quick-edit', MasVideos()->plugin_url() . '/assets/js/admin/quick-edit' . $suffix . '.js', array( 'jquery', 'masvideos_admin' ), MASVIDEOS_VERSION );

            //     $params = array(
            //         'strings' => array(
            //             'allow_reviews' => esc_js( __( 'Enable reviews', 'masvideos' ) ),
            //         ),
            //     );

            //     wp_localize_script( 'masvideos_quick-edit', 'masvideos_quick_edit', $params );
            // }

            // Meta boxes.
            if ( in_array( $screen_id, array( 'movie', 'edit-movie' ) ) ) {
                wp_enqueue_media();
                wp_register_script( 'masvideos-admin-movie-meta-boxes', MasVideos()->plugin_url() . '/assets/js/admin/meta-boxes-movie' . $suffix . '.js', array( 'masvideos-admin-meta-boxes', 'media-models' ), MASVIDEOS_VERSION );
                // wp_register_script( 'wc-admin-variation-meta-boxes', MasVideos()->plugin_url() . '/assets/js/admin/meta-boxes-product-variation' . $suffix . '.js', array( 'wc-admin-meta-boxes', 'serializejson', 'media-models' ), MASVIDEOS_VERSION );

                wp_enqueue_script( 'masvideos-admin-movie-meta-boxes' );
                // wp_enqueue_script( 'wc-admin-variation-meta-boxes' );

                $params = array(
                    'post_id'                             => isset( $post->ID ) ? $post->ID : '',
                    'plugin_url'                          => MasVideos()->plugin_url(),
                    'ajax_url'                            => admin_url( 'admin-ajax.php' ),
                    'masvideos_placeholder_img_src'       => masvideos_placeholder_img_src(),
                    'i18n_enter_a_value'                  => esc_js( __( 'Enter a value', 'masvideos' ) ),
                    'i18n_enter_menu_order'               => esc_js( __( 'Variation menu order (determines position in the list of variations)', 'masvideos' ) ),
                    'i18n_enter_a_value_fixed_or_percent' => esc_js( __( 'Enter a value (fixed or %)', 'masvideos' ) ),
                    'i18n_delete_all_variations'          => esc_js( __( 'Are you sure you want to delete all variations? This cannot be undone.', 'masvideos' ) ),
                    'i18n_last_warning'                   => esc_js( __( 'Last warning, are you sure?', 'masvideos' ) ),
                    'i18n_choose_image'                   => esc_js( __( 'Choose an image', 'masvideos' ) ),
                    'i18n_set_image'                      => esc_js( __( 'Set variation image', 'masvideos' ) ),
                    'i18n_variation_added'                => esc_js( __( 'variation added', 'masvideos' ) ),
                    'i18n_variations_added'               => esc_js( __( 'variations added', 'masvideos' ) ),
                    'i18n_no_variations_added'            => esc_js( __( 'No variations added', 'masvideos' ) ),
                    'i18n_remove_variation'               => esc_js( __( 'Are you sure you want to remove this variation?', 'masvideos' ) ),
                    'i18n_scheduled_sale_start'           => esc_js( __( 'Sale start date (YYYY-MM-DD format or leave blank)', 'masvideos' ) ),
                    'i18n_scheduled_sale_end'             => esc_js( __( 'Sale end date (YYYY-MM-DD format or leave blank)', 'masvideos' ) ),
                    'i18n_edited_variations'              => esc_js( __( 'Save changes before changing page?', 'masvideos' ) ),
                    'i18n_variation_count_single'         => esc_js( __( '%qty% variation', 'masvideos' ) ),
                    'i18n_variation_count_plural'         => esc_js( __( '%qty% variations', 'masvideos' ) ),
                    'variations_per_page'                 => absint( apply_filters( 'masvideos_admin_meta_boxes_variations_per_page', 15 ) ),
                );

                wp_localize_script( 'wc-admin-variation-meta-boxes', 'masvideos_admin_meta_boxes_variations', $params );
            }
            if ( in_array( str_replace( 'edit-', '', $screen_id ), array( 'product' ) ) ) {
                $post_id            = isset( $post->ID ) ? $post->ID : '';
                $currency           = '';
                $remove_item_notice = __( 'Are you sure you want to remove the selected items?', 'masvideos' );

                $params = array(
                    'remove_item_notice'            => $remove_item_notice,
                    'i18n_select_items'             => __( 'Please select some items.', 'masvideos' ),
                    'i18n_do_refund'                => __( 'Are you sure you wish to process this refund? This action cannot be undone.', 'masvideos' ),
                    'i18n_delete_refund'            => __( 'Are you sure you wish to delete this refund? This action cannot be undone.', 'masvideos' ),
                    'i18n_delete_tax'               => __( 'Are you sure you wish to delete this tax column? This action cannot be undone.', 'masvideos' ),
                    'remove_item_meta'              => __( 'Remove this item meta?', 'masvideos' ),
                    'remove_attribute'              => __( 'Remove this attribute?', 'masvideos' ),
                    'name_label'                    => __( 'Name', 'masvideos' ),
                    'remove_label'                  => __( 'Remove', 'masvideos' ),
                    'click_to_toggle'               => __( 'Click to toggle', 'masvideos' ),
                    'values_label'                  => __( 'Value(s)', 'masvideos' ),
                    'text_attribute_tip'            => __( 'Enter some text, or some attributes by pipe (|) separating values.', 'masvideos' ),
                    'visible_label'                 => __( 'Visible on the product page', 'masvideos' ),
                    'used_for_variations_label'     => __( 'Used for variations', 'masvideos' ),
                    'new_attribute_prompt'          => __( 'Enter a name for the new attribute term:', 'masvideos' ),
                    'calc_totals'                   => __( 'Recalculate totals? This will calculate taxes based on the customers country (or the store base country) and update totals.', 'masvideos' ),
                    'copy_billing'                  => __( 'Copy billing information to shipping information? This will remove any currently entered shipping information.', 'masvideos' ),
                    'load_billing'                  => __( "Load the customer's billing information? This will remove any currently entered billing information.", 'masvideos' ),
                    'load_shipping'                 => __( "Load the customer's shipping information? This will remove any currently entered shipping information.", 'masvideos' ),
                    'featured_label'                => __( 'Featured', 'masvideos' ),
                    'prices_include_tax'            => esc_attr( get_option( 'masvideos_prices_include_tax' ) ),
                    'tax_based_on'                  => esc_attr( get_option( 'masvideos_tax_based_on' ) ),
                    'round_at_subtotal'             => esc_attr( get_option( 'masvideos_tax_round_at_subtotal' ) ),
                    'no_customer_selected'          => __( 'No customer selected', 'masvideos' ),
                    'plugin_url'                    => MasVideos()->plugin_url(),
                    'ajax_url'                      => admin_url( 'admin-ajax.php' ),
                    'order_item_nonce'              => wp_create_nonce( 'order-item' ),
                    'add_attribute_nonce'           => wp_create_nonce( 'add-attribute' ),
                    'save_attributes_nonce'         => wp_create_nonce( 'save-attributes' ),
                    'calc_totals_nonce'             => wp_create_nonce( 'calc-totals' ),
                    'get_customer_details_nonce'    => wp_create_nonce( 'get-customer-details' ),
                    'search_products_nonce'         => wp_create_nonce( 'search-products' ),
                    'grant_access_nonce'            => wp_create_nonce( 'grant-access' ),
                    'revoke_access_nonce'           => wp_create_nonce( 'revoke-access' ),
                    'add_order_note_nonce'          => wp_create_nonce( 'add-order-note' ),
                    'delete_order_note_nonce'       => wp_create_nonce( 'delete-order-note' ),
                    'calendar_image'                => MasVideos()->plugin_url() . '/assets/images/calendar.png',
                    'post_id'                       => isset( $post->ID ) ? $post->ID : '',
                    'base_country'                  => MasVideos()->countries->get_base_country(),
                    // 'currency_format_num_decimals'  => wc_get_price_decimals(),
                    // 'currency_format_symbol'        => get_masvideos_currency_symbol( $currency ),
                    // 'currency_format_decimal_sep'   => esc_attr( wc_get_price_decimal_separator() ),
                    // 'currency_format_thousand_sep'  => esc_attr( wc_get_price_thousand_separator() ),
                    // 'currency_format'               => esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_masvideos_price_format() ) ), // For accounting JS.
                    // 'rounding_precision'            => wc_get_rounding_precision(),
                    // 'tax_rounding_mode'             => wc_get_tax_rounding_mode(),
                    // 'product_types'                 => array_unique( array_merge( array( 'simple', 'grouped', 'variable', 'external' ), array_keys( wc_get_product_types() ) ) ),
                    'i18n_download_permission_fail' => __( 'Could not grant access - the user may already have permission for this file or billing email is not set. Ensure the billing email is set, and the order has been saved.', 'masvideos' ),
                    'i18n_permission_revoke'        => __( 'Are you sure you want to revoke access to this download?', 'masvideos' ),
                    'i18n_tax_rate_already_exists'  => __( 'You cannot add the same tax rate twice!', 'masvideos' ),
                    'i18n_delete_note'              => __( 'Are you sure you wish to delete this note? This action cannot be undone.', 'masvideos' ),
                    'i18n_apply_coupon'             => __( 'Enter a coupon code to apply to this order.', 'masvideos' ),
                    'i18n_add_fee'                  => __( 'Enter a fixed amount or percentage to apply as a fee.', 'masvideos' ),
                );

                wp_localize_script( 'wc-admin-meta-boxes', 'masvideos_admin_meta_boxes', $params );
            }

            // Term ordering - only when sorting by term_order.
            // if ( ( strstr( $screen_id, 'edit-pa_' ) || ( ! empty( $_GET['taxonomy'] ) && in_array( wp_unslash( $_GET['taxonomy'] ), apply_filters( 'masvideos_sortable_taxonomies', array( 'product_cat' ) ) ) ) ) && ! isset( $_GET['orderby'] ) ) {

            //     wp_register_script( 'masvideos_term_ordering', MasVideos()->plugin_url() . '/assets/js/admin/term-ordering' . $suffix . '.js', array( 'jquery-ui-sortable' ), MASVIDEOS_VERSION );
            //     wp_enqueue_script( 'masvideos_term_ordering' );

            //     $taxonomy = isset( $_GET['taxonomy'] ) ? wc_clean( wp_unslash( $_GET['taxonomy'] ) ) : '';

            //     $masvideos_term_order_params = array(
            //         'taxonomy' => $taxonomy,
            //     );

            //     wp_localize_script( 'masvideos_term_ordering', 'masvideos_term_ordering_params', $masvideos_term_order_params );
            // }

            // Product sorting - only when sorting by menu order on the products page.
            // if ( current_user_can( 'edit_others_pages' ) && 'edit-product' === $screen_id && isset( $wp_query->query['orderby'] ) && 'menu_order title' === $wp_query->query['orderby'] ) {
            //     wp_register_script( 'masvideos_product_ordering', MasVideos()->plugin_url() . '/assets/js/admin/product-ordering' . $suffix . '.js', array( 'jquery-ui-sortable' ), MASVIDEOS_VERSION, true );
            //     wp_enqueue_script( 'masvideos_product_ordering' );
            // }

            // Reports Pages.
            // if ( in_array( $screen_id, apply_filters( 'masvideos_reports_screen_ids', array( $wc_screen_id . '_page_wc-reports', 'toplevel_page_wc-reports', 'dashboard' ) ) ) ) {
            //     wp_register_script( 'wc-reports', MasVideos()->plugin_url() . '/assets/js/admin/reports' . $suffix . '.js', array( 'jquery', 'jquery-ui-datepicker' ), MASVIDEOS_VERSION );

            //     wp_enqueue_script( 'wc-reports' );
            //     wp_enqueue_script( 'flot' );
            //     wp_enqueue_script( 'flot-resize' );
            //     wp_enqueue_script( 'flot-time' );
            //     wp_enqueue_script( 'flot-pie' );
            //     wp_enqueue_script( 'flot-stack' );
            // }

            // API settings.
            // if ( $wc_screen_id . '_page_wc-settings' === $screen_id && isset( $_GET['section'] ) && 'keys' == $_GET['section'] ) {
            //     wp_register_script( 'wc-api-keys', MasVideos()->plugin_url() . '/assets/js/admin/api-keys' . $suffix . '.js', array( 'jquery', 'masvideos_admin', 'underscore', 'backbone', 'wp-util', 'qrcode', 'wc-clipboard' ), MASVIDEOS_VERSION, true );
            //     wp_enqueue_script( 'wc-api-keys' );
            //     wp_localize_script(
            //         'wc-api-keys',
            //         'masvideos_admin_api_keys',
            //         array(
            //             'ajax_url'         => admin_url( 'admin-ajax.php' ),
            //             'update_api_nonce' => wp_create_nonce( 'update-api-key' ),
            //             'clipboard_failed' => esc_html__( 'Copying to clipboard failed. Please press Ctrl/Cmd+C to copy.', 'masvideos' ),
            //         )
            //     );
            // }

            // System status.
            // if ( $wc_screen_id . '_page_wc-status' === $screen_id ) {
            //     wp_register_script( 'wc-admin-system-status', MasVideos()->plugin_url() . '/assets/js/admin/system-status' . $suffix . '.js', array( 'wc-clipboard' ), MASVIDEOS_VERSION );
            //     wp_enqueue_script( 'wc-admin-system-status' );
            //     wp_localize_script(
            //         'wc-admin-system-status',
            //         'masvideos_admin_system_status',
            //         array(
            //             'delete_log_confirmation' => esc_js( __( 'Are you sure you want to delete this log?', 'masvideos' ) ),
            //         )
            //     );
            // }

            // if ( in_array( $screen_id, array( 'user-edit', 'profile' ) ) ) {
            //     wp_register_script( 'wc-users', MasVideos()->plugin_url() . '/assets/js/admin/users' . $suffix . '.js', array( 'jquery', 'wc-enhanced-select', 'selectWoo' ), MASVIDEOS_VERSION, true );
            //     wp_enqueue_script( 'wc-users' );
            //     wp_localize_script(
            //         'wc-users',
            //         'wc_users_params',
            //         array(
            //             'countries'              => json_encode( array_merge( MasVideos()->countries->get_allowed_country_states(), MasVideos()->countries->get_shipping_country_states() ) ),
            //             'i18n_select_state_text' => esc_attr__( 'Select an option&hellip;', 'masvideos' ),
            //         )
            //     );
            // }
        }
    }

endif;

return new MasVideos_Admin_Assets();
