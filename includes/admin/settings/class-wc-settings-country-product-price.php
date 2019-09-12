<?php
/**
 * WooCommerce Country Product Price settings page
 *
 * @version 1.8.0
 * @package WCCPP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_Settings_Country_Product_Price' ) ) :

	/**
	 * WC_Settings_Country_Product_Price Class
	 */
	class WC_Settings_Country_Product_Price extends WC_Settings_Page {

		/**
		 * Zone ID.
		 *
		 * @var string
		 */
		protected $zone_id;

		/**
		 * Constructor.
		 */
		public function __construct() {

			$this->id      = 'country-product-price';
			$this->label   = __( 'Country Pricing', 'wc-country-product-price' );
			$this->zone_id = empty( $_GET['zone_id'] ) ? false : wc_clean( wp_unslash( $_GET['zone_id'] ) ); // WPCS: CSRF ok.

			$this->init_hooks();
			$this->delete_zone();
		}

		/**
		 * Init action and filters
		 */
		protected function init_hooks() {
			add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
			add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
			add_action( 'woocommerce_sections_' . $this->id, array( $this, 'update_zone_notice' ), 5 );
			add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );
			add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
		}

		/**
		 * Delete a zone
		 */
		protected function delete_zone() {
			if ( ! empty( $_GET['delete_zone'] ) && isset( $_GET['tab'] ) && 'country-product-price' === $_GET['tab'] && isset( $_GET['section'] ) && 'zones' === $_GET['section'] ) { // WPCS: CSRF ok.

				if ( empty( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'wc-country-product-price-delete-zone' ) ) { // WPCS: input var ok, sanitization ok.
					wp_die( esc_html__( 'Action failed. Please refresh the page and retry.', 'woocommerce' ) );
				}

				$zone = WCCPP_Pricing_Zones::get_zone_by_id( wc_clean( wp_unslash( $_GET['delete_zone'] ) ) );
				if ( ! $zone ) {
					wp_die( esc_html__( 'Zone does not exist!', 'wc-country-product-price' ) );
				}

				WCCPP_Pricing_Zones::delete( $zone );

				WC_Admin_Settings::add_message( __( 'Zone have been deleted.', 'wc-country-product-price' ) );

			}
		}

		/**
		 * Checks the current section
		 *
		 * @param string $section String to check.
		 * @return bool
		 */
		protected function is_section( $section ) {
			global $current_section;
			return $section === $current_section;
		}

		/**
		 * Get sections
		 *
		 * @return array
		 */
		public function get_sections() {
			$sections = array(
				''      => __( 'General options', 'wc-country-product-price' ),
				'zones' => __( 'Pricing zones', 'wc-country-product-price' ),
			);

			return $sections;
		}

		/**
		 * Get settings array
		 *
		 * @return array
		 */
		public function get_settings() {
			$settings = apply_filters( 'wc_country_product_price_settings_general', array(
				array(
					'title' => __( 'General Options', 'woocommerce' ),
					'type'  => 'title',
					'desc'  => '',
					'id'    => 'general_options',
				),

				// array(
				// 	'title'    => __( 'Price Based On', 'wc-country-product-price' ),
				// 	'desc'     => __( 'This controls which address is used to refresh products prices on checkout.' ),
				// 	'id'       => 'wc_country_product_price_based_on',
				// 	'default'  => 'billing',
				// 	'type'     => 'select',
				// 	'class'    => 'wc-enhanced-select',
				// 	'desc_tip' => true,
				// 	'options'  => array(
				// 		'billing'  => __( 'Customer billing country', 'wc-country-product-price' ),
				// 		'shipping' => __( 'Customer shipping country', 'wc-country-product-price' ),
				// 	),
				// ),

				// array(
				// 	'title'   => __( 'Shipping', 'wc-country-product-price' ),
				// 	'desc'    => __( 'Apply exchange rates to shipping cost.', 'wc-country-product-price' ),
				// 	'id'      => 'wc_country_product_price_shipping_exchange_rate',
				// 	'default' => 'no',
				// 	'type'    => 'checkbox',
				// ),

				// array(
				// 	'title'    => __( 'Caching support', 'wc-country-product-price' ),
				// 	'desc'     => __( 'Load products price in background.', 'wc-country-product-price' ),
				// 	'id'       => 'wc_country_product_price_caching_support',
				// 	'default'  => 'no',
				// 	'type'     => 'checkbox',
				// 	// translators: HTML tags.
				// 	'desc_tip' => sprintf( __( 'This fired an AJAX request per page (%1$sread more%2$s).' ), '<a target="_blank" rel="noopener noreferrer" href="https://www.pricebasedcountry.com/docs/getting-started/geolocation-cache-support/?utm_source=settings&utm_medium=banner&utm_campaign=Docs">', '</a>' ),
				// ),

				// array(
				// 	'title'    => __( 'Test mode', 'wc-country-product-price' ),
				// 	'desc'     => __( 'Enable test mode', 'wc-country-product-price' ),
				// 	'id'       => 'wc_country_product_price_test_mode',
				// 	'default'  => 'no',
				// 	'type'     => 'checkbox',
				// 	// translators: HTML tags.
				// 	'desc_tip' => sprintf( __( 'Enable test mode to show pricing for a specific country (%1$sHow to test%2$s).', 'wc-country-product-price' ), '<a target="_blank" rel="noopener noreferrer" href="https://www.pricebasedcountry.com/docs/getting-started/testing/?utm_source=settings&utm_medium=banner&utm_campaign=Docs">', '</a>' ),
				// ),

				// array(
				// 	'title'   => __( 'Test country', 'wc-country-product-price' ),
				// 	'id'      => 'wc_country_product_price_test_country',
				// 	'default' => wc_get_base_location(),
				// 	'type'    => 'select',
				// 	'class'   => 'chosen_select',
				// 	'options' => WC()->countries->countries,
				// ),

				array(
					'type' => 'sectionend',
					'id'   => 'general_options',
				),
			));

			return $settings;
		}

		/**
		 * Output the settings
		 */
		public function output() {
			ob_start();

			/**
			 * Zones
			 */
			if ( $this->is_section( 'zones' ) ) {
				$this->output_zones_screen();
			}

			/**
			 * Settings
			 */
			else {
				$settings = $this->get_settings();
				WC_Admin_Settings::output_fields( $settings );
			}
			$output = ob_get_clean();

			echo $output;
		}

		/**
		 * Save settings
		 */
		public function save() {

			if ( $this->is_section( 'zones' ) && $this->zone_id ) {
				$this->save_zone();
			}

			// Save settings.
			elseif ( !$this->is_section( 'zones' ) ) {
				$settings = $this->get_settings();
				WC_Admin_Settings::save_fields( $settings );

				// // Update WooCommerce Default Customer Address.
				// if ( 'geolocation_ajax' === get_option( 'woocommerce_default_customer_address' ) && 'yes' === get_option( 'wc_country_product_price_caching_support', 'no' ) ) {
				// 	update_option( 'woocommerce_default_customer_address', 'geolocation' );
				// }
			}
		}

		/**
		 * Handles output of the pricing zones page in admin.
		 */
		protected function output_zones_screen() {
			global $hide_save_button;

			$hide_save_button = true;

			// Single zone screen.
			if ( $this->zone_id ) {
				if ( 'new' === $this->zone_id ) {
					$zone = WCCPP_Pricing_Zones::create();
				} else {
					$zone = WCCPP_Pricing_Zones::get_zone_by_id( $this->zone_id );
				}

				if ( ! $zone ) {
					wp_die( esc_html__( 'Zone does not exist!', 'wc-country-product-price' ) );
				}

				$allowed_countries = WCCPP_Pricing_Zones::get_allowed_countries( $zone );

				include dirname( __FILE__ ) . '/views/html-admin-page-pricing-zone.php';

			}

			// Zone list table.
			else {
				include_once WCCPP()->plugin_path() . 'includes/admin/class-wccpp-admin-zone-list-table.php';

				echo '<h3>' . esc_html__( 'Pricing zones', 'wc-country-product-price' ) . ' <a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=country-product-price&section=zones&zone_id=new' ) ) . '" class="add-new-h2">' . esc_html__( 'Add pricing zone', 'wc-country-product-price' ) . '</a></h3>';
				echo '<p>' . esc_html__( 'A Pricing Zone is a group of countries to which you sell your products at a different price and (or) currency.', 'wc-country-product-price' ) . '</p>';

				$table_list = new WCCPP_Admin_Zone_List_Table();
				$table_list->prepare_items();
				$table_list->views();
				$table_list->display();
			}
		}

		/**
		 * Save a zone from the $_POST array.
		 */
		protected function save_zone() {

			do_action( 'wc_country_product_price_settings_before_save_zone' );

			$postdata                           = wc_clean( wp_unslash( $_POST ) ); // WPCS: CSRF ok.
			$postdata['disable_tax_adjustment'] = isset( $postdata['disable_tax_adjustment'] ) ? 'yes' : 'no';

			if ( 'new' === $this->zone_id ) {
				$zone = WCCPP_Pricing_Zones::create();
			} else {
				$zone = WCCPP_Pricing_Zones::get_zone_by_id( $this->zone_id );
			}

			if ( ! $zone ) {
				wp_die( esc_html__( 'Zone does not exist!', 'wc-country-product-price' ) );
			}

			// Fields validation.
			$pass = false;

			if ( empty( $postdata['name'] ) ) {
				WC_Admin_Settings::add_error( __( 'Zone name is required.', 'wc-country-product-price' ) );

			} elseif ( empty( $postdata['countries'] ) ) {
				WC_Admin_Settings::add_error( __( 'Add at least one country to the list.', 'wc-country-product-price' ) );

			} elseif ( apply_filters( 'wc_country_product_price_settings_zone_validation', true ) ) {
				$pass = true;
			}

			if ( $pass ) {
				foreach ( $postdata as $field => $value ) {
					if ( is_callable( array( $zone, 'set_' . $field ) ) ) {
						$zone->{'set_' . $field}( $value );
					}
				}

				$id = WCCPP_Pricing_Zones::save( $zone );

				wp_safe_redirect( admin_url( 'admin.php?page=wc-settings&tab=country-product-price&section=zones&zone_id=' . $id . '&updated=1' ) );
			}
		}

		/**
		 * Output the zone update notice
		 */
		public function update_zone_notice() {
			if ( $this->is_section( 'zones' ) && !empty( $_GET['updated'] ) ) { // WPCS: CSRF ok.
			?>
			<div id="message" class="updated inline">
				<p><strong><?php esc_html_e( 'Zone updated successfully.', 'wc-country-product-price' ); ?></strong></p>
				<p>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=country-product-price&section=zones' ) ); ?>">&larr; <?php esc_html_e( 'Back to Zones', 'wc-country-product-price' ); ?></a>
					<a style="margin-left:15px;" href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=country-product-price&section=zones&zone_id=new' ) ); ?>"><?php esc_html_e( 'Add a new zone', 'wc-country-product-price' ); ?></a>
				</p>
			</div>
			<?php
			}
		}
	}

endif;

return new WC_Settings_Country_Product_Price();
