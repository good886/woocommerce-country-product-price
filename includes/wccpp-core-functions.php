<?php

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

	// if ( wcpbc_is_woocommerce_frontend() ) {

	// 	$country          = wcpbc_get_prop_value( wc()->customer, 'billing_country' );
	// 	$shipping_country = wc()->customer->get_shipping_country();
	// 	if ( ! empty( $shipping_country ) && $country !== $shipping_country && 'shipping' === get_option( 'wc_price_based_country_based_on', 'billing' ) ) {
	// 		$country = $shipping_country;
	// 	}
	// }

	return $country;
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