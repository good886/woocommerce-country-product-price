<?php
/**
 * Pricing zone admin
 *
 * @package WCCPP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="settings-panel wccpp-zone-settings">

	<h2>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=country-product-price&section=zones' ) ); ?>"><?php esc_html_e( 'Pricing zones', 'wc-country-product-price' ); ?></a> &gt;
		<span class="wccpp-zone-name"><?php echo esc_html( $zone->get_name() ? $zone->get_name() : __( 'Zone', 'wc-country-product-price' ) ); ?></span>
	</h2>

	<table class="form-table">

		<!-- Name -->
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="name"><?php esc_html_e( 'Zone Name', 'wc-country-product-price' ); ?></label>
				<?php echo wp_kses_post( wc_help_tip( __( 'This is the name of the zone for your reference.', 'wc-country-product-price' ) ) ); ?>
			</th>
				<td class="forminp forminp-text">
					<input name="name" id="name" type="text" value="<?php echo esc_attr( $zone->get_name() ); ?>"/>
				</td>
		</tr>

		<!-- Country multiselect -->
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="countries"><?php esc_html_e( 'Countries', 'wc-country-product-price' ); ?></label>
				<?php echo wp_kses_post( wc_help_tip( __( 'These are countries inside this zone. Customers will be matched against these countries.', 'wc-country-product-price' ) ) ); ?>
			</th>
			<td class="forminp">
				<select multiple="multiple" name="countries[]" style="width:350px" data-placeholder="<?php esc_html_e( 'Choose countries&hellip;', 'woocommerce' ); ?>" title="Country" class="chosen_select">
					<?php
					foreach ( $allowed_countries as $country_code => $country_name ) {
						echo '<option value="' . esc_attr( $country_code ) . '" ' . selected( in_array( $country_code, $zone->get_countries(), true ), true, false ) . '>' . esc_html( WC()->countries->countries[ $country_code ] ) . '</option>';
					}
					?>
				</select><br />
				<a class="select_all button" href="#"><?php esc_html_e( 'Select all', 'woocommerce' ); ?></a>
				<a class="select_none button" href="#"><?php esc_html_e( 'Select none', 'woocommerce' ); ?></a>
				<a class="select_eur button" data-countries='<?php echo esc_attr( '["' . implode( '","', array_intersect( wccpp_get_currencies_countries( 'EUR' ), array_keys( $allowed_countries ) ) ) . '"]' ); ?>' href="#"><?php esc_html_e( 'Select Eurozone', 'wc-country-product-price' ); ?></a>
				<a class="select_eur_none button" data-countries='<?php echo esc_attr( '["' . implode( '","', wccpp_get_currencies_countries( 'EUR' ) ) . '"]' ); ?>' href="#"><?php esc_html_e( 'Unselect Eurozone', 'wc-country-product-price' ); ?></a>
			</td>
		</tr>

		<!-- Currency select -->
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="currency"><?php esc_html_e( 'Currency', 'woocommerce' ); ?></label>
			</th>
			<td class="forminp forminp-select">
				<select name="currency" id="currency" class="chosen_select">
					<?php
					foreach ( get_woocommerce_currencies() as $code => $name ) {
						echo '<option value="' . esc_attr( $code ) . '" ' . selected( $zone->get_currency(), $code ) . '>' . esc_html( $name . ' (' . get_woocommerce_currency_symbol( $code ) . ')' ) . '</option>';
					}
					?>
				</select>
			</td>
		</tr>
	</table>

	<input type="hidden" name="page" value="wc-settings" />
	<input type="hidden" name="tab" value="wc_country_product_price" />
	<input type="hidden" name="section" value="zones" />

	<p class="submit">
		<?php submit_button( __( 'Save Changes', 'woocommerce' ), 'primary', 'save', false ); ?>
		<?php if ( $zone->get_zone_id() ) : ?>
		<a class="wccpp-delete-zone" style="color: #a00; text-decoration: none; margin-left: 10px;" href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'delete_zone' => $zone->get_zone_id() ), admin_url( 'admin.php?page=wc-settings&tab=country-product-price&section=zones' ) ), 'wc-country-product-price-delete-zone' ) ); ?>"><?php esc_html_e( 'Delete zone', 'wc-country-product-price' ); ?></a>
		<?php endif; ?>
	</p>

</div>
