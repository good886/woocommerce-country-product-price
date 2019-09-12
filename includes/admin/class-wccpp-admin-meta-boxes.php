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
		// add_action( 'woocommerce_product_after_variable_attributes', array( __CLASS__, 'after_variable_attributes' ), 10, 3 );
		add_action( 'woocommerce_process_product_meta_simple', array( __CLASS__, 'process_product_meta' ) );
		add_action( 'woocommerce_process_product_meta_external', array( __CLASS__, 'process_product_meta' ) );
		add_action( 'woocommerce_process_product_meta_course', array( __CLASS__, 'process_product_meta' ) );
		add_action( 'woocommerce_save_product_variation', array( __CLASS__, 'process_product_meta' ), 10, 2 );
		// add_action( 'woocommerce_process_product_meta_grouped', array( __CLASS__, 'process_product_meta_grouped' ) );
		// add_action( 'woocommerce_product_quick_edit_save', array( __CLASS__, 'product_quick_edit_save' ) );
		// add_action( 'woocommerce_product_bulk_edit_save', array( __CLASS__, 'product_quick_edit_save' ), 20 );
		// add_action( 'woocommerce_bulk_edit_variations', array( __CLASS__, 'bulk_edit_variations' ), 20, 4 );
		// add_action( 'woocommerce_coupon_options', array( __CLASS__, 'coupon_options' ) );
		// add_action( 'woocommerce_coupon_options_save', array( __CLASS__, 'coupon_options_save' ) );
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

	// /**
	//  * Output the zone pricing for variations
	//  *
	//  * @param int                  $loop Variations loop index.
	//  * @param array                $variation_data Array of variation data @deprecated.
	//  * @param WC_Product_Variation $variation The variation product instance.
	//  */
	// public static function after_variable_attributes( $loop, $variation_data, $variation ) {
	// 	$post_id = $variation->ID;
	// 	$field   = array(
	// 		'name'          => "_variable_price_method[$loop]",
	// 		'wrapper_class' => wccpp_is_pro() ? '' : 'hide_if_variable-subscription hide_if_nyp-wcpbc',
	// 		'fields'        => array_merge( array(
	// 			'_regular_price'         => array(
	// 				'name'          => "_variable_regular_price[$loop]",
	// 				// Translators: currency symbol.
	// 				'label'         => __( 'Regular price (%s)', 'wc-country-product-price' ),
	// 				'wrapper_class' => 'form-row form-row-first _variable_regular_price_wccpp_field',
	// 			),
	// 			'_sale_price'            => array(
	// 				'name'          => "_variable_sale_price[$loop]",
	// 				// Translators: currency symbol.
	// 				'label'         => __( 'Sale price (%s)', 'wc-country-product-price' ),
	// 				'class'         => 'wccpp_sale_price',
	// 				'wrapper_class' => 'form-row form-row-last _variable_sale_price_wccpp_field',
	// 			),
	// 			'_sale_price_dates'      => array(
	// 				'name'          => "_variable_sale_price_dates[$loop]",
	// 				'type'          => 'radio',
	// 				'class'         => 'wccpp_sale_price_dates',
	// 				'wrapper_class' => 'wccpp_sale_price_dates_wrapper',
	// 				'default_value' => 'default',
	// 				'label'         => __( 'Sale price dates', 'wc-country-product-price' ),
	// 				'options'       => array(
	// 					'default' => __( 'Same as default price', 'wc-country-product-price' ),
	// 					'manual'  => __( 'Set specific dates', 'wc-country-product-price' ),
	// 				),
	// 			),
	// 			'_sale_price_dates_from' => array(
	// 				'name'          => "_variable_sale_price_dates_from[$loop]",
	// 				'label'         => __( 'Sale start date', 'wc-country-product-price' ),
	// 				'data_type'     => 'date',
	// 				'class'         => 'sale_price_dates_from',
	// 				'wrapper_class' => 'form-row form-row-first sale_price_dates_fields wccpp_hide_if_sale_dates_default',
	// 				'placeholder'   => _x( 'From&hellip;', 'placeholder', 'wc-country-product-price' ) . ' YYYY-MM-DD',
	// 			),
	// 			'_sale_price_dates_to'   => array(
	// 				'name'          => "_variable_sale_price_dates_to[$loop]",
	// 				'label'         => __( 'Sale end date', 'wc-country-product-price' ),
	// 				'data_type'     => 'date',
	// 				'class'         => 'sale_price_dates_to',
	// 				'wrapper_class' => 'form-row form-row-last sale_price_dates_fields wccpp_hide_if_sale_dates_default',
	// 				'placeholder'   => _x( 'To&hellip;', 'placeholder', 'wc-country-product-price' ) . ' YYYY-MM-DD',
	// 			),
	// 		), apply_filters( 'wc_country_product_price_product_variation_fields', array(), $loop ) ),
	// 	);

	// 	// Output the input control.
	// 	foreach ( WCCPP_Pricing_Zones::get_zones() as $zone ) {

	// 		$field['value'] = $zone->get_postmeta( $post_id, '_price_method' );

	// 		foreach ( $field['fields'] as $key => $field_data ) {
	// 			$field['fields'][ $key ]['value'] = $zone->get_postmeta( $post_id, $key );
	// 		}

	// 		wccpp_princing_input( $field, $zone );
	// 	}
	// }

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

	// /**
	//  * Save meta data product grouped
	//  *
	//  * @param int $post_id Post ID.
	//  */
	// public static function process_product_meta_grouped( $post_id ) {
	// 	foreach ( WCCPP_Pricing_Zones::get_zones() as $zone ) {
	// 		wccpp_zone_grouped_product_sync( $zone, $post_id );
	// 	}
	// }

	// /**
	//  * Quick and Bulk product edit.
	//  *
	//  * @param WC_Product $product Product instance.
	//  */
	// public static function product_quick_edit_save( $product ) {
	// 	$_product = (object) wccpp_get_prop_value( $product, array( 'id', 'parent_id', 'type' ) );

	// 	foreach ( WCCPP_Pricing_Zones::get_zones() as $zone ) {
	// 		if ( $zone->is_exchange_rate_price( $_product->id ) ) {

	// 			wccpp_update_product_pricing( $_product->id, $zone );

	// 			// Sync grouped parent product.
	// 			if ( ! empty( $_product->parent_id ) && 'simple' === $_product->type ) {
	// 				wccpp_zone_grouped_product_sync( $zone, $_product->id );
	// 			}
	// 		}
	// 	}
	// }

	// /**
	//  * Bulk edit variations via AJAX.
	//  *
	//  * @param string $bulk_action Variation bulk action.
	//  * @param array  $data Sanitized post data.
	//  * @param int    $product_id Variable product ID.
	//  * @param array  $variations Array of varations ID.
	//  */
	// public static function bulk_edit_variations( $bulk_action, $data, $product_id, $variations ) {
	// 	$actions = array( 'variable_regular_price', 'variable_sale_price', 'variable_sale_schedule', 'variable_regular_price_increase', 'variable_regular_price_decrease', 'variable_sale_price_increase', 'variable_sale_price_decrease' );

	// 	if ( ! in_array( $bulk_action, $actions, true ) ) {
	// 		return;
	// 	}

	// 	foreach ( WCCPP_Pricing_Zones::get_zones() as $zone ) {
	// 		foreach ( $variations as $variation_id ) {
	// 			if ( $zone->is_exchange_rate_price( $variation_id ) ) {
	// 				wccpp_update_product_pricing( $variation_id, $zone );
	// 			}
	// 		}
	// 	}
	// }

	// /**
	//  * Display coupon amount options.
	//  *
	//  * @since 1.6
	//  */
	// public static function coupon_options() {
	// 	woocommerce_wp_checkbox( array(
	// 		'id'          => 'zone_pricing_type',
	// 		'cbvalue'     => 'exchange_rate',
	// 		'label'       => __( 'Calculate amount by exchange rate', 'wc-country-product-price' ),
	// 		// Translators: HTML tags.
	// 		'description' => sprintf( __( 'Check this box if for the countries defined in zone pricing the coupon amount should be calculated using exchange rate. %1$s(%2$sUpgrade to Price Based on Country Pro to set copupon amount by zone%3$s)', 'wc-country-product-price' ), '<br />', '<a target="_blank" el="noopener noreferrer" href="https://www.pricebasedcountry.com/pricing/?utm_source=coupon&utm_medium=banner&utm_campaign=Get_Pro">', '</a>' ),
	// 	) );
	// }

	// /**
	//  * Save coupon amount options.
	//  *
	//  * @since 1.6
	//  * @param int $post_id Post ID.
	//  */
	// public static function coupon_options_save( $post_id ) {
	// 	$type              = get_post_meta( $post_id, 'discount_type', true );
	// 	$zone_pricing_type = in_array( $type, array( 'fixed_cart', 'fixed_product' ), true ) && isset( $_POST['zone_pricing_type'] ) ? 'exchange_rate' : 'nothig'; // WPCS: CSRF ok.
	// 	update_post_meta( $post_id, 'zone_pricing_type', $zone_pricing_type );
	// }
}
