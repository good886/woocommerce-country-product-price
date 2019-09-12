/* global wc_country_product_price_admin_params, woocommerce_admin */
jQuery( function( $ ) {
	'use strict';

	// /**
	//  * Metaboxes actions
	//  */
	// var wccpp_meta_boxes = {

	// 	/**
	// 	 * Initialize metabox actions
	// 	 */
	// 	init: function() {
	// 		$(document).ready( this.show_and_hide_panels );
	// 		$( 'body' ).on( 'click', '.wccpp_price_method', this.price_method_change );
	// 		$( 'body' ).on( 'click', '.wccpp_sale_price_dates[type="radio"]', this.sale_dates_change );
	// 		$( 'body' ).on( 'keyup', '.wccpp_sale_price[type=text]', this.sale_price_keyup );
	// 		$( 'body' ).on( 'change', '.wccpp_sale_price[type=text]', this.sale_price_change );
	// 		$( document.body ).on( 'woocommerce_variations_added', this.show_and_hide_panels );
	// 		$( '#woocommerce-product-data' ).on( 'woocommerce_variations_loaded', this.show_and_hide_panels );
	// 		$( document.body ).on( 'wc_price_based_country_show_and_hide_panels', this.show_and_hide_panels );

	// 		$(document).ready( this.product_type_not_supported_alert )
	// 		$(document.body).on( 'woocommerce-product-type-change', this.product_type_not_supported_alert );
	// 	},

	// 	/**
	// 	 * Show/Hide elements if manual
	// 	 */
	// 	show_and_hide_panels: function() {
	// 		$( '.wccpp_price_method[type="radio"][value="manual"]' ).each( function(){
	// 			var $wrapper = $( this ).closest( '.wccpp_pricing' );
	// 			var show     = $(this).prop( 'checked' );

	// 			wccpp_meta_boxes.show_hide_price_controls( $wrapper, show );
	// 		});
	// 	},

	// 	/**
	// 	 * Show or hide the pricing zone controls.
	// 	 *
	// 	 * @param {*} $wrapper
	// 	 * @param {*} show
	// 	 */
	// 	show_hide_price_controls: function( $wrapper, show ) {
	// 		var	 hide_sale_dates = show && ! $wrapper.find( '.wccpp_sale_price_dates[type="radio"][value="default"]').first().prop( 'checked' );

	// 		$wrapper.find( '.wccpp_show_if_manual' ).toggle( show );
	// 		$wrapper.find( '.wccpp_hide_if_sale_dates_default').toggle( hide_sale_dates );

	// 		$( document.body ).trigger( 'wc_price_based_country_manual_price_' + ( show ? 'show' : 'hide' ), [$wrapper] );
	// 	},

	// 	/**
	// 	 * Check if price method is manual and show/hide elements
	// 	 */
	// 	price_method_change: function() {
	// 		var $wrapper = $( this ).closest( '.wccpp_pricing' );
	// 		var show     = $( this ).val() == 'manual';

	// 		wccpp_meta_boxes.show_hide_price_controls( $wrapper, show );
	// 	},

	// 	*
	// 	 * Check if sale dates is default and show/hide elements

	// 	sale_dates_change: function() {
	// 		$( this ).closest( '.wccpp_pricing' ).find( '.wccpp_hide_if_sale_dates_default' ).toggle( 'default' !== $(this).val() );
	// 	},

	// 	/**
	// 	 * Is sale price bigger than regular price
	// 	 */
	// 	is_sale_bigger_than_regular: function( sale_price_field ) {
	// 		var regular_price_field = $('#' + sale_price_field.attr('id').replace('_sale','_regular') ) ;

	// 		var sale_price    = parseFloat( accounting.unformat( sale_price_field.val(), woocommerce_admin.mon_decimal_point ) );
	// 		var regular_price = parseFloat( accounting.unformat( regular_price_field.val(), woocommerce_admin.mon_decimal_point ) );

	// 		return sale_price >= regular_price;
	// 	},

	// 	/**
	// 	 * Trigger on sale price change
	// 	 */
	// 	sale_price_keyup: function() {
	// 		if ( wccpp_meta_boxes.is_sale_bigger_than_regular( $(this) ) ) {
	// 			$( document.body ).triggerHandler( 'wc_add_error_tip', [ $(this), 'i18n_sale_less_than_regular_error' ] );
	// 		} else {
	// 			$( document.body ).triggerHandler( 'wc_remove_error_tip', [ $(this), 'i18n_sale_less_than_regular_error' ] );
	// 		}
	// 	},

	// 	/**
	// 	 * Trigger on sale price change
	// 	 */
	// 	sale_price_change: function() {
	// 		if ( wccpp_meta_boxes.is_sale_bigger_than_regular( $(this) ) ) {
	// 			$(this).val( '' );
	// 		}
	// 	},

	// 	/**
	// 	 * Display or hide the product type not supported alert.
	// 	 */
	// 	product_type_not_supported_alert: function() {
	// 		var select_val = $( 'select#product-type' ).val();
	// 		$( '#general_product_data .wc-price-based-country-upgrade-notice.wc-pbc-show-if-not-supported').hide();
	// 		$( '#general_product_data .wc-price-based-country-upgrade-notice.wc-pbc-show-if-not-supported.product-type-' + select_val ).toggle(
	// 			0 > $.inArray( select_val, wc_country_product_price_admin_params.product_type_supported )
	// 		);
	// 		$( '#general_product_data .wc-price-based-country-upgrade-notice.wc-pbc-show-if-third-party').hide();
	// 		$( '#general_product_data .wc-price-based-country-upgrade-notice.wc-pbc-show-if-third-party.product-type-' + select_val ).toggle(
	// 			0 <= $.inArray( select_val, wc_country_product_price_admin_params.product_type_third_party )
	// 		);
	// 	}
	// };

	// /**
	//  * Coupon Metabox actions
	//  */
	// var wccpp_coupon_metaboxes = {

	// 	init: function() {
	// 		this.discount_type_change();
	// 		$('#general_coupon_data #discount_type').on('change', this.discount_type_change );
	// 	},

	// 	discount_type_change: function() {
	// 		var show = $( '#discount_type' ).val()=='fixed_cart' || $('#discount_type').val()=='fixed_product';
	// 		$('#general_coupon_data #zone_pricing_type').closest('p').toggle( show );
	// 	}
	// };

	/**
	 * Settings page actions
	 */
	var wccpp_settings = {

		init: function(){
			$( '.wccpp-zone-settings' ).on( 'click', '.select_eur', this.select_eur_click );
			$( '.wccpp-zone-settings' ).on( 'click', '.select_eur_none', this.unselect_eur_click );
			// $( '.wcpbc-zone-settings' ).on( 'keyup', '#name', this.zone_name_keyup );
			// $( '.wcpbc-zone-settings, table.pricingzones' ).on( 'click', 'a.wcpbc-delete-zone', this.delete_click );
			// $( '#wc_price_based_country_test_mode').on( 'change', this.test_mode_change );
			// $( '#wc_price_based_country_test_country').closest('tr').toggle( $( '#wc_price_based_country_test_mode' ).is(':checked') );
			// // Move submit button in setting page
			// $('.wc-price-based-country-setting-wrapper-ads .wc-price-based-country-setting-content').append(
			// 	$('.wc-price-based-country-setting-wrapper-ads').siblings('p.submit')
			// );
		},

		select_eur_click: function() {
			var countries = $( this ).data( 'countries' );
			if ( countries instanceof Array ) {
				$( this ).closest( 'td' ).find( 'select option' ).each( function( index, that ) {
					if ( countries.indexOf( $(that).attr( 'value' ) ) > -1 ) {
						$( that ).attr( 'selected', 'selected' );
					}
				});
				$( this ).closest( 'td' ).find( 'select' ).trigger( 'change' );
			}
			return false;
		},

		unselect_eur_click: function() {
			var countries = $( this ).data( 'countries' );
			if ( countries instanceof Array ) {
				$( this ).closest( 'td' ).find( 'select option' ).each( function( index, that ) {
					if ( countries.indexOf( $(that).attr( 'value' ) ) > -1 ) {
						$( that ).removeAttr( 'selected');
					}
				});
				$( this ).closest( 'td' ).find( 'select' ).trigger( 'change' );
			}
			return false;
		},

		// zone_name_keyup: function() {
		// 	var zone_name = $( this ).val();
		// 	$( '.wcpbc-zone-name' ).text( zone_name ? zone_name : wc_country_product_price_admin_params.i18n_default_zone_name );
		// },

		// delete_click: function(e) {
		// 	if ( ! confirm( wc_country_product_price_admin_params.i18n_delete_zone_alert ) ) {
		// 		e.preventDefault();
		// 	}
		// },

		// test_mode_change: function() {
		// 	$( '#wc_price_based_country_test_country' ).closest('tr').toggle( $(this).is(':checked') )
		// }
	};

	// wccpp_meta_boxes.init();
	// wccpp_coupon_metaboxes.init();
	wccpp_settings.init();
});
