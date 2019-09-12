<?php
/**
 * WooCommerce Country Product Price admin metaboxes
 *
 * @package WCCPP
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WCCPP_Admin_Product_Data Class
 */
class WCCPP_Admin_Meta_Boxes {

	/**
	 * Init hooks
	 */
	public static function init() {

		add_action( 'woocommerce_product_options_general_product_data', array( __CLASS__, 'options_general_product_data' ) );
		add_action( 'woocommerce_process_product_meta_simple', array( __CLASS__, 'process_product_meta' ) );
		add_action( 'woocommerce_process_product_meta_external', array( __CLASS__, 'process_product_meta' ) );
		add_action( 'woocommerce_process_product_meta_course', array( __CLASS__, 'process_product_meta' ) );
	}

	/**
	 * Output the zone pricing for simple products
	 */
	public static function options_general_product_data() {
		$wrapper_class = array( 'options_group', 'show_if_simple', 'show_if_external', 'show_if_course' );

		$field = array(
			'wrapper_class' => implode( ' ', $wrapper_class ),
			'fields'        => array_merge( array(
				array(
					'name'  => '_regular_price',
					'label' => __( 'Regular price (%s)', 'wc-country-product-price' ),
				),
				array(
					'name'  => '_sale_price',
					'label' => __( 'Sale price (%s)', 'wc-country-product-price' ),
					'class' => 'wccpp_sale_price',
				),

				/**
				 * This field is hidden because we support only default sale price dates
				 */
				array(
					'name'          => '_sale_price_dates',
					'type'          => 'radio',
					'default_value' => 'default',
					'class'         => 'wccpp_sale_price_dates',
					'wrapper_class' => 'hidden',
					'label'         => __( 'Sale price dates', 'wc-country-product-price' ),
					'options'       => array(
						'default' => __( 'Same as default price', 'wc-country-product-price' ),
						// 'manual'  => __( 'Set specific dates', 'wc-country-product-price' ),
					),
				),
				array(
					'name'          => '_sale_price_dates_from',
					'label'         => '',
					'data_type'     => 'date',
					'class'         => 'sale_price_dates_from',
					'wrapper_class' => 'sale_price_dates_fields wccpp_hide_if_sale_dates_default',
					'placeholder'   => _x( 'From&hellip;', 'placeholder', 'wc-country-product-price' ) . ' YYYY-MM-DD',
				),
				array(
					'name'          => '_sale_price_dates_to',
					'label'         => '',
					'data_type'     => 'date',
					'class'         => 'sale_price_dates_to',
					'wrapper_class' => 'sale_price_dates_fields wccpp_hide_if_sale_dates_default',
					'placeholder'   => _x( 'To&hellip;', 'placeholder', 'wc-country-product-price' ) . ' YYYY-MM-DD',
				),
			), apply_filters( 'wc_country_product_price_product_simple_fields', array() ) ),
		);

		// Output the input control.
		foreach ( WCCPP_Pricing_Zones::get_zones() as $zone ) {
			wccpp_princing_input( $field, $zone );
		}
	}

	/**
	 * Save product metadata
	 *
	 * @param int $post_id Post ID.
	 * @param int $index Index of variations to save.
	 */
	public static function process_product_meta( $post_id, $index = false ) {
		$fields = array( '_price_method', '_regular_price', '_sale_price', '_sale_price_dates', '_sale_price_dates_from', '_sale_price_dates_to' );
		foreach ( WCCPP_Pricing_Zones::get_zones() as $zone ) {
			$data = array();
			foreach ( $fields as $field ) {
				$var_name       = false !== $index ? '_variable' . $field : $field;
				$data[ $field ] = $zone->get_input_var( $var_name, $index );
			}

			// Save metadata.
			wccpp_update_product_pricing( $post_id, $zone, $data );
		}
	}
}
