<?php

/**
 * Return the current pricing zone. Alias of WCCPP()->current_zone.
 *
 * @since 1.0.0
 * @return WCCPP_Pricing_Zone
 */
function wccpp_the_zone() {
	if ( wccpp()->current_zone ) {
		return wccpp()->current_zone;
	};
	return false;
}

/**
 * Return is a value is empty and no-zero
 *
 * @since 1.0.0
 * @param string $value The value to check.
 * @return bool
 */
function wccpp_empty_nozero( $value ) {
	return ( empty( $value ) && ! ( is_numeric( $value ) && 0 === absint( $value ) ) );
}

/**
 * Alias of WCCPP_Pricing_Zones::get_zone_by_country
 *
 * @param string $country The country.
 * @return WCCPP_Pricing_Zone
 */
function wccpp_get_zone_by_country( $country = '' ) {
	if ( ! class_exists( 'WCCPP_Pricing_Zones' ) ) {
		return false;
	}
	$country = empty( $country ) ? wccpp_get_woocommerce_country() : $country;
	return WCCPP_Pricing_Zones::get_zone_by_country( $country );
}

/**
 * Get WooCommerce customer country
 *
 * @return string
 */
function wccpp_get_woocommerce_country() {

	$country = false;

	if ( wccpp_is_woocommerce_frontend() ) {
		$location = WC_Geolocation::geolocate_ip();

		if ( get_option( 'wc_country_product_price_test_mode', 'no' ) === 'yes' && get_option( 'wc_country_product_price_test_country' ) ) {
			$location = wc_format_country_state_string( get_option( 'wc_country_product_price_test_country' ) );
		}

		if ( $location && !empty($location['country'])) {
			$country = $location['country']; // example: IT, DE
		}

		// $country          = wccpp_get_prop_value( wc()->customer, 'billing_country' );
		// $shipping_country = wc()->customer->get_shipping_country();
		// if ( ! empty( $shipping_country ) && $country !== $shipping_country && 'shipping' === get_option( 'wc_country_product_price_based_on', 'billing' ) ) {
		// 	$country = $shipping_country;
		// }

	}

	return $country;
}

/**
 * Return the object property value. Add compatibility with WC 2.6
 *
 * @param mixed  $object The object instance.
 * @param string $prop_name Property name.
 * @return mixed
 */
function wccpp_get_prop_value( $object, $prop_name ) {
	$props   = is_array( $prop_name ) ? $prop_name : array( $prop_name );
	$value   = array();
	$mapping = array(
		'billing_country' => 'get_country',
		'parent_id'       => 'get_parent',
		'type'            => 'get_type',
		'amount'          => 'coupon_amount',
	);

	if ( version_compare( WC_VERSION, '3.0', '>=' ) ) {
		foreach ( $props as $prop ) {
			$get            = 'get_' . $prop;
			$value[ $prop ] = $object->{$get}();
		}
	} else {
		foreach ( $props as $prop ) {
			$get            = ! empty( $mapping[ $prop ] ) ? $mapping[ $prop ] : $prop;
			$value[ $prop ] = 'get_' === substr( $get, 0, 4 ) ? $object->{$get}() : $object->$get;
		}
	}

	if ( 1 === count( $value ) ) {
		$value = $value[ $prop_name ];
	}
	return $value;
}

/**
 * Check is WooCommerce frontend
 *
 * @return bool
 */
function wccpp_is_woocommerce_frontend() {
	return function_exists( 'WC' ) && ! empty( WC()->customer );
}

/**
 * Return base currency
 *
 * @return string
 */
function wccpp_get_base_currency() {
	return get_option( 'woocommerce_currency' );
}

/**
 * Return a a array with all currencies avaiables in WooCommerce with associate countries
 *
 * @param string $currency_code Currency code.
 * @return array
 */
function wccpp_get_currencies_countries( $currency_code = false ) {

	$currencies = array(
		'AED' => array( 'AE' ),
		'ARS' => array( 'AR' ),
		'AUD' => array( 'AU', 'CC', 'CX', 'HM', 'KI', 'NF', 'NR', 'TV' ),
		'BDT' => array( 'BD' ),
		'BRL' => array( 'BR' ),
		'BGN' => array( 'BG' ),
		'CAD' => array( 'CA' ),
		'CLP' => array( 'CL' ),
		'CNY' => array( 'CN' ),
		'COP' => array( 'CO' ),
		'CZK' => array( 'CZ' ),
		'DKK' => array( 'DK', 'FO', 'GL' ),
		'DOP' => array( 'DO' ),
		'EUR' => array( 'AD', 'AT', 'AX', 'BE', 'BL', 'CY', 'DE', 'EE', 'ES', 'FI', 'FR', 'GF', 'GP', 'GR', 'IE', 'IT', 'LT', 'LU', 'LV', 'MC', 'ME', 'MF', 'MQ', 'MT', 'NL', 'PM', 'PT', 'RE', 'SI', 'SK', 'SM', 'TF', 'VA', 'YT' ),
		'HKD' => array( 'HK' ),
		'HRK' => array( 'HR' ),
		'HUF' => array( 'HU' ),
		'ISK' => array( 'IS' ),
		'IDR' => array( 'ID' ),
		'INR' => array( 'IN' ),
		'NPR' => array( 'NP' ),
		'ILS' => array( 'IL' ),
		'JPY' => array( 'JP' ),
		'KIP' => array( 'LA' ),
		'KRW' => array( 'KR' ),
		'MYR' => array( 'MY' ),
		'MXN' => array( 'MX' ),
		'NGN' => array( 'NG' ),
		'NOK' => array( 'BV', 'NO', 'SJ' ),
		'NZD' => array( 'CK', 'NU', 'NZ', 'PN', 'TK' ),
		'PYG' => array( 'PY' ),
		'PHP' => array( 'PH' ),
		'PLN' => array( 'PL' ),
		'GBP' => array( 'GB', 'GG', 'GS', 'IM', 'JE' ),
		'RON' => array( 'RO' ),
		'RUB' => array( 'RU' ),
		'SGD' => array( 'SG' ),
		'ZAR' => array( 'ZA' ),
		'SEK' => array( 'SE' ),
		'CHF' => array( 'LI' ),
		'TWD' => array( 'TW' ),
		'THB' => array( 'TH' ),
		'TRY' => array( 'TR' ),
		'UAH' => array( 'UA' ),
		'USD' => array( 'BQ', 'EC', 'FM', 'IO', 'MH', 'PW', 'TC', 'TL', 'US', 'VG' ),
		'VND' => array( 'VN' ),
		'EGP' => array( 'EG' ),
	);

	if ( $currency_code && array_key_exists( $currency_code, $currencies ) ) {
		$currencies = $currencies[ $currency_code ];
	}

	return $currencies;
}

/**
 * Return an array of product type supported
 *
 * @param string $source basic|pro|third-party.
 * @param string $context Context to use the function.
 * @return boolean
 */
function wccpp_product_types_supported( $source = '', $context = '' ) {

	$types = array(
		'basic' => array(
			'simple'   => 'Simple product',
			'grouped'  => 'Grouped product',
			'external' => 'External/Affiliate product',
			'variable' => 'Variable product',
		),
		'third-party' => array(
			'course' => 'WooCommerce LearnDash Product',
		),
	);

	$types['third-party'] = apply_filters( 'wc_country_product_price_third_party_product_types', $types['third-party'] );

	if ( empty( $source ) ) {
		$types = array_merge( $types['basic'], $types['third-party'] );
	} elseif ( 'basic' === $source ) {
		$types = $types['basic'];
	} else {
		$types = $types['third-party'];
	}

	return $types;
}

/**
 * Return the price method options.
 *
 * @return array
 */
function wccpp_price_method_options() {
	return array(
		// 'exchange_rate' => __( 'Calculate prices by the exchange rate', 'wc-country-product-price' ),
		'manual'        => __( 'Set prices manually', 'wc-country-product-price' ),
	);
}

/**
 * Return price method label.
 *
 * @param string             $text Text to construct the price method label.
 * @param WCCPP_Pricing_Zone $zone Pricing zone instance.
 * @return string
 */
function wccpp_price_method_label( $text, $zone ) {
	return $text . ' ' . str_replace( ' ', '&nbsp;', sprintf( '%s (%s)', $zone->get_name(), get_woocommerce_currency_symbol( $zone->get_currency() ) ) );
}

/**
 * Output a product pricing input control.
 *
 * @since 1.8.0
 * @param array              $field Field arguments.
 * @param WCCPP_Pricing_Zone $zone Pricig zone instance.
 */
function wccpp_princing_input( $field, $zone ) {
	global $thepostid, $post;
	$thepostid = empty( $thepostid ) ? $post->ID : $thepostid;

	$field['name']           = empty( $field['name'] ) ? '_price_method' : $field['name'];
	$field['id']             = empty( $field['id'] ) ? str_replace( array( '[', ']' ), array( '_', '' ), $field['name'] ) : $field['id'];
	$field['value']          = empty( $field['value'] ) ? $zone->get_postmeta( $thepostid, $field['name'] ) : $field['value'];
	$field['label']          = empty( $field['label'] ) ? __( 'Price for', 'wc-country-product-price' ) : $field['label'];
	$field['fields']         = isset( $field['fields'] ) && is_array( $field['fields'] ) ? $field['fields'] : array();
	$field['wrapper']        = isset( $field['wrapper'] ) ? $field['wrapper'] : true;
	$field['wrapper_class']  = empty( $field['wrapper_class'] ) ? '' : $field['wrapper_class'];
	$field['wrapper_class'] .= ' wccpp_pricing wccpp_pricing_' . $zone->get_zone_id();

	if ( $field['wrapper'] ) {
		echo '<div class="' . esc_attr( $field['wrapper_class'] ) . '">';
	}

	woocommerce_wp_radio(
		array(
			'id'            => $zone->get_postmetakey( $field['id'] ),
			'name'          => $zone->get_postmetakey( $field['name'] ),
			'value'         => 'manual',
			'class'         => 'wccpp_price_method',
			'label'         => wccpp_price_method_label( $field['label'], $zone ),
			'wrapper_class' => '_price_method_wccpp_field',
			'options'       => wccpp_price_method_options(),
		)
	);

	foreach ( $field['fields'] as $child_field ) {
		$child_field['name']              = empty( $child_field['name'] ) ? '' : $child_field['name'];
		$child_field['id']                = empty( $child_field['id'] ) ? str_replace( array( '[', ']' ), array( '_', '' ), $child_field['name'] ) : $child_field['id'];
		$child_field['label']             = empty( $child_field['label'] ) ? '' : sprintf( $child_field['label'], get_woocommerce_currency_symbol( $zone->get_currency() ) );
		$child_field['value']             = isset( $child_field['value'] ) ? $child_field['value'] : $zone->get_postmeta( $thepostid, $child_field['name'] );
		$child_field['type']              = empty( $child_field['type'] ) ? 'text' : $child_field['type'];
		$child_field['data_type']         = isset( $child_field['data_type'] ) ? $child_field['data_type'] : '';
		$child_field['custom_attributes'] = isset( $child_field['custom_attributes'] ) && is_array( $child_field['custom_attributes'] ) ? $child_field['custom_attributes'] : array();
		$child_field['wrapper_class']     = empty( $child_field['wrapper_class'] ) ? ' ' : $child_field['wrapper_class'] . ' ';
		$child_field['wrapper_class']    .= $child_field['id'] . '_wccpp_field wccpp_show_if_manual';

		if ( empty( $child_field['data_type'] ) && 'text' === $child_field['type'] ) {
			$child_field['data_type'] = 'price';
		}
		if ( empty( $child_field['value'] ) && isset( $child_field['default_value'] ) ) {
			$child_field['value'] = $child_field['default_value'];
		}
		if ( 'date' === $child_field['data_type'] ) {
			$child_field['custom_attributes']['maxlength'] = '10';
			$child_field['custom_attributes']['pattern']   = apply_filters( 'woocommerce_date_input_html_pattern', '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])' );
			$child_field['value']                          = empty( $child_field['value'] ) ? '' : date_i18n( 'Y-m-d', $child_field['value'] );
		}

		$child_field['id']   = $zone->get_postmetakey( $child_field['id'] );
		$child_field['name'] = $zone->get_postmetakey( $child_field['name'] );

		// Output the field.
		if ( 'radio' === $child_field['type'] ) {
			woocommerce_wp_radio( $child_field );
		} else {
			woocommerce_wp_text_input( $child_field );
		}
	}

	if ( $field['wrapper'] ) {
		echo '</div>';
	}
}

/**
 * Save the product pricing for a zone
 *
 * @param int                $post_id Post ID.
 * @param WCCPP_Pricing_Zone $zone Pricig zone instance.
 * @param array              $data The product pricing.
 */
function wccpp_update_product_pricing( $post_id, $zone, $data = array() ) {

	$data['_price_method']          = 'manual';
	$data['_regular_price']         = isset( $data['_regular_price'] ) ? $data['_regular_price'] : $zone->get_postmeta( $post_id, '_regular_price' );
	$data['_sale_price']            = isset( $data['_sale_price'] ) ? $data['_sale_price'] : $zone->get_postmeta( $post_id, '_sale_price' );
	$data['_sale_price_dates']      = isset( $data['_sale_price_dates'] ) ? $data['_sale_price_dates'] : $zone->get_postmeta( $post_id, '_sale_price_dates' );
	$data['_sale_price_dates_from'] = isset( $data['_sale_price_dates_from'] ) ? strtotime( $data['_sale_price_dates_from'] ) : $zone->get_postmeta( $post_id, '_sale_price_dates_from' );
	$data['_sale_price_dates_to']   = isset( $data['_sale_price_dates_to'] ) ? strtotime( $data['_sale_price_dates_to'] ) : $zone->get_postmeta( $post_id, '_sale_price_dates_to' );

	if ( ! empty( $data['_sale_price_dates_to'] ) && empty( $data['_sale_price_dates_from'] ) ) {
		$data['_sale_price_dates_from'] = strtotime( 'NOW', current_time( 'timestamp' ) );
	}

	if ( 'manual' !== $data['_sale_price_dates'] ) {
		$data['_sale_price_dates']      = 'default';
		$data['_sale_price_dates_from'] = get_post_meta( $post_id, '_sale_price_dates_from', true );
		$data['_sale_price_dates_to']   = get_post_meta( $post_id, '_sale_price_dates_to', true );
	}

	$data['_regular_price'] = wc_format_decimal( $data['_regular_price'] );
	$data['_sale_price']    = wc_format_decimal( $data['_sale_price'] );

	// Update price if on sale.
	if ( ! wccpp_empty_nozero( $data['_sale_price'] ) && empty( $data['_sale_price_dates_to'] ) && empty( $data['_sale_price_dates_from'] ) ) {
		$data['_price'] = $data['_sale_price'];
	} elseif ( ! wccpp_empty_nozero( $data['_sale_price'] ) && $data['_sale_price_dates_from'] && $data['_sale_price_dates_from'] <= strtotime( 'NOW', current_time( 'timestamp' ) ) ) {
		$data['_price'] = $data['_sale_price'];
	} else {
		$data['_price'] = $data['_regular_price'];
	}

	if ( $data['_sale_price_dates_to'] && $data['_sale_price_dates_to'] < strtotime( 'NOW', current_time( 'timestamp' ) ) ) {
		$data['_price']                 = $data['_regular_price'];
		$data['_sale_price']            = '';
		$data['_sale_price_dates_from'] = '';
		$data['_sale_price_dates_to']   = '';
	}

	// Save metadata.
	$zone->set_postmeta( $post_id, '_price_method', $data['_price_method'] );
	$zone->set_postmeta( $post_id, '_regular_price', $data['_regular_price'] );
	$zone->set_postmeta( $post_id, '_sale_price', $data['_sale_price'] );
	$zone->set_postmeta( $post_id, '_price', $data['_price'] );
	$zone->set_postmeta( $post_id, '_sale_price_dates', $data['_sale_price_dates'] );
	$zone->set_postmeta( $post_id, '_sale_price_dates_from', $data['_sale_price_dates_from'] );
	$zone->set_postmeta( $post_id, '_sale_price_dates_to', $data['_sale_price_dates_to'] );
}