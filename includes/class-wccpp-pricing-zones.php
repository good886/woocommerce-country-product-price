<?php
/**
 * Handles storage and retrieval of pricing zones
 *
 * @version 1.0.0
 * @package WCCPP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Pricing zones class.
 */
class WCCPP_Pricing_Zones {

	/**
	 * Return the pricing zone class.
	 *
	 * @return string
	 */
	private static function get_pricing_zone_class_name() {
		$classname = 'WCCPP_Pricing_Zone';
		return $classname;
	}

	/**
	 * Return a empty pricing zone object.
	 *
	 * @return WCCPP_Pricing_Zone
	 */
	public static function create() {
		$classname = self::get_pricing_zone_class_name();
		return new $classname();
	}

	/**
	 * Save a zone.
	 *
	 * @param WCCPP_Pricing_Zone $zone Zone instance.
	 * @return string
	 */
	public static function save( $zone ) {
		$zones = (array) get_option( 'wc_country_product_price_zones', array() );

		if ( ! $zone->get_zone_id() ) {
			$zone_id = self::get_unique_slug( sanitize_key( sanitize_title( $zone->get_name() ) ), array_keys( $zones ) );
			$zone->set_zone_id( $zone_id );
		} else {
			$zone_id = $zone->get_zone_id();
		}
		$zone_data = $zone->get_data();
		unset( $zone_data['zone_id'] );

		$zones[ $zone_id ] = $zone_data;
		update_option( 'wc_country_product_price_zones', $zones );

		return $zone_id;
	}

	/**
	 * Save a group zones.
	 *
	 * @param array $zones Array of pricing zones.
	 */
	public static function bulk_save( $zones ) {
		$azones = (array) get_option( 'wc_country_product_price_zones', array() );
		foreach ( $zones as $zone ) {
			$zone_data = $zone->get_data();
			unset( $zone_data['zone_id'] );
			$azones[ $zone->get_zone_id() ] = $zone_data;
		}
		update_option( 'wc_country_product_price_zones', $azones );
	}

	/**
	 * Get a unique slug that indentify a zone
	 *
	 * @param string $new_slug New slug.
	 * @param array  $slugs All IDs of the zones.
	 * @return array
	 */
	private static function get_unique_slug( $new_slug, $slugs ) {
		$seqs = array();

		foreach ( $slugs as $slug ) {
			$slug_parts = explode( '-', $slug, 2 );
			if ( $slug_parts[0] === $new_slug && ( count( $slug_parts ) === 1 || is_numeric( $slug_parts[1] ) ) ) {
				$seqs[] = isset( $slug_parts[1] ) ? $slug_parts[1] : 0;
			}
		}

		if ( $seqs ) {
			rsort( $seqs );
			$new_slug = $new_slug . '-' . ( $seqs[0] + 1 );
		}

		return $new_slug;
	}

	/**
	 * Delete a zone.
	 *
	 * @param WCCPP_Pricing_Zone $zone Zone instance.
	 */
	public static function delete( $zone ) {
		global $wpdb;

		$zones = (array) get_option( 'wc_country_product_price_zones', array() );

		if ( isset( $zones[ $zone->get_zone_id() ] ) ) {
			foreach ( wccpp_get_meta_keys_to_delete() as $metakey ) {
				$wpdb->delete( $wpdb->postmeta, array( 'meta_key' => $zone->get_postmetakey( $metakey ) ) ); // WPCS: slow query ok.
			}
			unset( $zones[ $zone->get_zone_id() ] );
			update_option( 'wc_country_product_price_zones', $zones );
		}
	}

	/**
	 * Get pricing zones.
	 *
	 * @return array Array of WCCPP_Pricing_Zone instances.
	 */
	public static function get_zones() {
		$classname = self::get_pricing_zone_class_name();
		$zones     = array();

		foreach ( (array) get_option( 'wc_country_product_price_zones', array() ) as $id => $data ) {
			$zones[ $id ] = new $classname( array_merge( $data, array( 'zone_id' => $id ) ) );
		}

		return $zones;
	}

	/**
	 * Get a pricing zone.
	 *
	 * @param mixed $the_zone WC_Pricing_Zone|array|string|bool Pricing zone instance, array of pricing zone properties, pricing zone ID, or false to return the current pricing zone.
	 * @return WCCPP_Pricing_Zone
	 */
	public static function get_zone( $the_zone = false ) {
		$zone      = false;
		$classname = self::get_pricing_zone_class_name();

		if ( is_object( $the_zone ) && in_array( get_class( $the_zone ), array( 'WCCPP_Pricing_Zone' ), true ) ) {
			$zone = $the_zone;
		} elseif ( is_array( $the_zone ) ) {
			$zone = new $classname( $the_zone );
		} elseif ( ! $the_zone ) {
			$zone = WCCPP()->current_zone;
		} else {
			$zone = self::get_zone_by_id( $the_zone );
		}

		return $zone;
	}

	/**
	 * Get pricing zone by an ID.
	 *
	 * @param string $id Pricing zone ID.
	 * @return WCCPP_Pricing_Zone
	 */
	public static function get_zone_by_id( $id ) {
		$zone      = null;
		$zones     = (array) get_option( 'wc_country_product_price_zones', array() );
		$classname = self::get_pricing_zone_class_name();

		if ( ! empty( $zones[ $id ] ) ) {
			$zone = new $classname( array_merge( $zones[ $id ], array( 'zone_id' => $id ) ) );
		}

		return $zone;
	}

	/**
	 * Get pricing zone by country.
	 *
	 * @param string $country Country code.
	 * @return WCCPP_Pricing_Zone
	 */
	public static function get_zone_by_country( $country ) {

		$zone      = null;
		$zones     = (array) get_option( 'wc_country_product_price_zones', array() );
		$classname = self::get_pricing_zone_class_name();

		foreach ( $zones as $key => $zone_data ) {
			if ( in_array( $country, $zone_data['countries'], true ) ) {
				$zone = new $classname( array_merge( $zone_data, array( 'zone_id' => $key ) ) );
				break;
			}
		}
		return $zone;
	}

	/**
	 * Return a pricing zone from an order.
	 *
	 * @param mixed $order WC_Order|int Order instance or order ID.
	 * @return WCCPP_Pricing_Zone
	 */
	public static function get_zone_from_order( $order ) {
		$zone     = false;
		$order_id = false;

		if ( is_numeric( $order ) ) {
			$order_id = $order;
		} elseif ( is_callable( array( $order, 'get_id' ) ) ) {
			$order_id = $order->get_id();
		} elseif ( isset( $order->id ) ) {
			$order_id = $order->id;
		}

		if ( $order_id ) {
			$data = get_post_meta( $order_id, '_wcpbc_pricing_zone', true );

			if ( $data ) {
				$zone = self::get_zone( $data );
			} else {
				// Find zone by order country.
				$based_on = get_option( 'wc_price_based_country_based_on', 'billing' );
				$country  = get_post_meta( $order_id, '_' . $based_on . '_country', true );
				$zone     = self::get_zone_by_country( $country );
			}
		}
		return $zone;
	}

	/**
	 * Return the allowed countries for a zone.
	 *
	 * @param WCCPP_Pricing_Zone $zone Zone instance.
	 * @return array
	 */
	public static function get_allowed_countries( $zone ) {
		$allowed_countries = array();
		$raw_countries     = array_keys( wc()->countries->get_allowed_countries() );
		$zone_countries    = array();

		foreach ( self::get_zones() as $_zone ) {
			if ( $_zone->get_zone_id() !== $zone->get_zone_id() ) {
				$zone_countries = array_merge( $zone_countries, $_zone->get_countries() );
			}
		}

		$raw_countries  = array_diff( $raw_countries, $zone_countries );
		$not_in_allowed = array_diff( $zone->get_countries(), $raw_countries );
		foreach ( array_merge( $raw_countries, $not_in_allowed ) as $country ) {
			$allowed_countries[ $country ] = wc()->countries->countries[ $country ];
		}
		return $allowed_countries;
	}
}
