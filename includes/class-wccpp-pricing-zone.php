<?php
/**
 * Represents a single pricing zone
 *
 * @version 1.0.0
 * @package WCCPP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WCCPP_Pricing_Zone
 */
class WCCPP_Pricing_Zone {

	/**
	 * Zone data.
	 *
	 * @var array
	 */
	protected $data = array();

	/**
	 * Constructor for zones.
	 *
	 * @param array $data Pricing zone attributes as array.
	 */
	public function __construct( $data = null ) {

		$this->data = wp_parse_args( $data, array(
			'zone_id'                => '',
			'name'                   => '',
			'countries'              => array(),
			'currency'               => get_option( 'woocommerce_currency' ),
		) );
	}

	/**
	 * Get zone data.
	 *
	 * @return array
	 */
	public function get_data() {
		return $this->data;
	}

	/**
	 * Gets a prop for a getter method.
	 *
	 * @since 1.7.9
	 * @param  string $prop Name of prop to get.
	 * @return mixed
	 */
	protected function get_prop( $prop ) {
		return isset( $this->data[ $prop ] ) ? $this->data[ $prop ] : false;
	}

	/**
	 * Sets a prop for a setter method.
	 *
	 * @since 1.8.0
	 * @param string $prop Name of prop to set.
	 * @param mixed  $value Value to set.
	 */
	protected function set_prop( $prop, $value ) {
		if ( isset( $this->data[ $prop ] ) ) {
			$this->data[ $prop ] = $value;
		}
	}

	/**
	 * Set zone id.
	 *
	 * @param string $zone_id Zone ID.
	 */
	public function set_zone_id( $zone_id ) {
		return $this->set_prop( 'zone_id', $zone_id );
	}

	/**
	 * Get zone id.
	 *
	 * @return string
	 */
	public function get_zone_id() {
		return $this->get_prop( 'zone_id' );
	}

	/**
	 * Get zone name.
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->get_prop( 'name' );
	}

	/**
	 * Set the zone name.
	 *
	 * @param string $name Zone name.
	 */
	public function set_name( $name ) {
		$this->set_prop( 'name', $name );
	}

	/**
	 * Get countries.
	 *
	 * @return array
	 */
	public function get_countries() {
		return $this->get_prop( 'countries' );
	}

	/**
	 * Set countries of the zone.
	 *
	 * @param array $countries Countries.
	 */
	public function set_countries( $countries ) {
		if ( is_array( $countries ) ) {
			$this->set_prop( 'countries', $countries );
		}
	}

	/**
	 * Get zone currency.
	 *
	 * @return string
	 */
	public function get_currency() {
		return $this->get_prop( 'currency' );
	}

	/**
	 * Set the zone currency.
	 *
	 * @param string $currency Zone currency.
	 */
	public function set_currency( $currency ) {
		$this->set_prop( 'currency', $currency );
	}

	/**
	 * Get a meta key based on zone ID
	 *
	 * @param string $meta_key Metadata key.
	 * @return string
	 */
	public function get_postmetakey( $meta_key = '' ) {
		return '_' . $this->get_zone_id() . $meta_key;
	}

	/**
	 * Get a meta value based on zone ID
	 *
	 * @param int    $post_id Post ID.
	 * @param string $meta_key Metadata key.
	 * @return mixed
	 */
	public function get_postmeta( $post_id, $meta_key ) {
		return get_post_meta( $post_id, $this->get_postmetakey( $meta_key ), true );
	}

	/**
	 * Add meta data field to a post
	 *
	 * @param int    $post_id Post ID.
	 * @param string $meta_key Metadata key.
	 * @param string $meta_value Metadata value.
	 * @return int|bool
	 */
	public function add_postmeta( $post_id, $meta_key, $meta_value ) {
		return add_post_meta( $post_id, $this->get_postmetakey( $meta_key ), $meta_value, false );
	}

	/**
	 * Update meta value based on zone ID
	 *
	 * @param int    $post_id Post ID.
	 * @param string $meta_key Metadata key.
	 * @param string $meta_value Metadata value.
	 * @return int|bool
	 */
	public function set_postmeta( $post_id, $meta_key, $meta_value ) {
		return update_post_meta( $post_id, $this->get_postmetakey( $meta_key ), $meta_value );
	}

	/**
	 * Remove metadata from a post
	 *
	 * @param int    $post_id Post ID.
	 * @param string $meta_key Metadata key.
	 * @return bool True on success, false on failure.
	 */
	public function delete_postmeta( $post_id, $meta_key ) {
		return delete_post_meta( $post_id, $this->get_postmetakey( $meta_key ) );
	}

	/**
	 * Get product price
	 *
	 * @param int    $post_id Post ID.
	 * @param string $meta_key Metadata key.
	 * @return mixed
	 */
	public function get_post_price( $post_id, $meta_key ) {
		$zone_price = $this->get_postmeta( $post_id, $meta_key );

		return $zone_price;
	}

	/**
	 * Round a price
	 *
	 * @param float $price Amount to round.
	 * @param float $num_decimals Number of decimals.
	 * @return float
	 */
	protected function round( $price, $num_decimals = '' ) {
		if ( wccpp_empty_nozero( $num_decimals ) ) {
			$num_decimals = wc_get_price_decimals();
		}

		$value = $price;

		if ( ! empty( $value ) ) {
			$value = round( $value, $num_decimals );
		}
		return $value;
	}

	/**
	 * Return an amount in the shop base currency
	 *
	 * @since 1.7.4
	 *
	 * @param float $amount Amount to convert to base currency.
	 * @return float
	 */
	public function get_base_currency_amount( $amount ) {
		$amount = floatval( $amount );
		return $amount;
	}

	/**
	 * Helper function that return the value of a $_POST variable.
	 *
	 * @since 1.8.0
	 * @param string $key POST parameter name.
	 * @param int    $index If the POST value is a array, the index array to return.
	 * @return mixed
	 */
	public function get_input_var( $key, $index = false ) {
		$metakey = $this->get_postmetakey( $key );
		$value   = null;

		if ( false !== $index && isset( $_POST[ $metakey ][ $index ] ) ) {
			$value = wc_clean( wp_unslash( $_POST[ $metakey ][ $index ] ) ); // WPCS: CSRF ok.
		} elseif ( isset( $_POST[ $metakey ] ) ) {
			$value = wc_clean( wp_unslash( $_POST[ $metakey ] ) ); // WPCS: CSRF ok.
		}

		return $value;
	}
}
