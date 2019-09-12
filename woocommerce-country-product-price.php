<?php
/*
Plugin Name: WooCommerce Country Product Price
Plugin URI: https://www.stefanomarra.com
Description: Define custom prices per country/zone
Version: 1.0
Author: Stefano Marra
Author URI: https://www.stefanomarra.com
License: GPL2
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define WCCPP
if ( ! defined( 'WCCPP_PLUGIN_FILE' ) ) {
	define( 'WCCPP_PLUGIN_FILE', __FILE__ );
}

class WooCommerce_Country_Product_Price {

	/**
	 * Country Product Price Version
	 *
	 * @var string
	 */
	public $version = '1.0.0';

	/**
	 * The front-end pricing zone
	 *
	 * @var WCCPP_Pricing_Zone
	 */
	public $current_zone = null;

	/**
	 * The single instance of the class.
	 *
	 * @var WooCommerce_Country_Product_Price
	 */
	protected static $_instance = null;

	/**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	public function plugin_url() {
		return plugin_dir_url( WCCPP_PLUGIN_FILE );
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return plugin_dir_path( WCCPP_PLUGIN_FILE );
	}

	/**
	 * Return the plugin base name
	 *
	 * @return string
	 */
	public function plugin_basename() {
		return plugin_basename( WCCPP_PLUGIN_FILE );
	}

	/**
	 * Initialize
	 */
	function __construct() {

		$this->includes();
		$this->init();
	}

	/**
	 * Main WC_Product_Price_Based_Country Instance
	 *
	 * @static
	 * @see WCPBC()
	 * @return WC_Product_Price_Based_Country
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Include required plugin files
	 */
	private function includes() {
		include_once $this->plugin_path() . 'includes/class-wccpp-pricing-zone.php';
		include_once $this->plugin_path() . 'includes/class-wccpp-pricing-zones.php';
		include_once $this->plugin_path() . 'includes/wccpp-core-functions.php';
		include_once $this->plugin_path() . 'includes/wccpp-metadata-functions.php';

		if ( is_admin() ) {
			include_once $this->plugin_path() . 'includes/admin/class-wccpp-admin.php';
			include_once $this->plugin_path() . 'includes/admin/class-wccpp-admin-meta-boxes.php';
		}
	}

	/**
	 * Init
	 */
	public function init() {

		// Set the current zone.
		$this->current_zone = wccpp_get_zone_by_country();

		if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
			// Admin request.
			WCCPP_Admin::init();
			// WCCPP_Admin_Meta_Boxes::init();
		}
	}
}

function wccpp() {
	return WooCommerce_Country_Product_Price::instance();
}

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	return wccpp();
}

