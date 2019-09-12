<?php
/**
 * WooCommerce Country Product Price Admin
 *
 * @package WCCPP
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WCPBC_Admin Class
 */
class WCCPP_Admin {

	/**
	 * Hook actions and filters
	 */
	public static function init() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_assets' ) );
		add_filter( 'woocommerce_get_settings_pages', array( __CLASS__, 'settings_country_product_price' ) );
	}

	/**
	 * Add settings tab to woocommerce settings
	 *
	 * @param array $settings Array of setting pages.
	 * @return array
	 */
	public static function settings_country_product_price( $settings ) {
		$settings[] = include 'settings/class-wc-settings-country-product-price.php';
		return $settings;
	}

	/**
	 * Enqueue admin assets.
	 */
	public static function admin_assets() {
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';
		if ( ! ( in_array( $screen_id, wc_get_screen_ids(), true ) || 'plugins' === $screen_id ) ) {
			return;
		}

		// JS.
		$suffix = '';
		wp_register_script( 'wc_country_product_price_admin', WCCPP()->plugin_url() . 'assets/js/admin' . $suffix . '.js', array( 'jquery', 'woocommerce_admin', 'accounting' ), WCCPP()->version, true );

		wp_localize_script( 'wc_country_product_price_admin', 'wc_country_product_price_admin_params', array(
			'ajax_url' => admin_url( 'admin-ajax.php' )
		) );

		if ( in_array( $screen_id, wc_get_screen_ids(), true ) ) {
			wp_enqueue_script( 'wc_country_product_price_admin' );
		}

		// Styles.
		wp_enqueue_style( 'wc_country_product_price_admin_styles', WCCPP()->plugin_url() . 'assets/css/admin.css', array(), WCCPP()->version );
	}
}