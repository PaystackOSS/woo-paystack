jQuery( function( $ ) {
	'use strict';

	/**
	 * Object to handle Paystack admin functions.
	 */
	var wc_paystack_admin = {
		/**
		 * Initialize.
		 */
		init: function() {

			// Toggle api key settings.
			$( document.body ).on( 'change', '#woocommerce_paystack_testmode', function() {
                var test_secret_key             = $( '#woocommerce_paystack_test_secret_key' ).parents( 'tr' ).eq( 0 ),
                    test_public_key             = $( '#woocommerce_paystack_test_public_key' ).parents( 'tr' ).eq( 0 ),
                    test_webhook_signing_secret = $( '#woocommerce_paystack_test_webhook_signing_secret' ).parents( 'tr' ).eq( 0 ),
                    live_secret_key             = $( '#woocommerce_paystack_live_secret_key' ).parents( 'tr' ).eq( 0 ),
                    live_public_key             = $( '#woocommerce_paystack_live_public_key' ).parents( 'tr' ).eq( 0 ),
                    live_webhook_signing_secret = $( '#woocommerce_paystack_live_webhook_signing_secret' ).parents( 'tr' ).eq( 0 );

                if ($( this ).is( ':checked' )) {
                    test_secret_key.show();
                    test_public_key.show();
                    test_webhook_signing_secret.show();
                    live_secret_key.hide();
                    live_public_key.hide();
                    live_webhook_signing_secret.hide();
                } else {
                    test_secret_key.hide();
                    test_public_key.hide();
                    test_webhook_signing_secret.hide();
                    live_secret_key.show();
                    live_public_key.show();
                    live_webhook_signing_secret.show();
                }
			} );

			$( '#woocommerce_paystack_testmode' ).change();

			$( document.body ).on( 'change', '.woocommerce_paystack_split_payment', function() {
				var subaccount_code = $( '.woocommerce_paystack_subaccount_code' ).parents( 'tr' ).eq( 0 ),
					subaccount_charge = $( '.woocommerce_paystack_split_payment_charge_account' ).parents( 'tr' ).eq( 0 ),
					transaction_charge = $( '.woocommerce_paystack_split_payment_transaction_charge' ).parents( 'tr' ).eq( 0 );

				if ( $( this ).is( ':checked' ) ) {
					subaccount_code.show();
					subaccount_charge.show();
					transaction_charge.show();
				} else {
					subaccount_code.hide();
					subaccount_charge.hide();
					transaction_charge.hide();
				}
			} );

			$( '#woocommerce_paystack_split_payment' ).change();

			// Toggle Custom Metadata settings.
			$( '.wc-paystack-metadata' ).change( function() {
				if ( $( this ).is( ':checked' ) ) {
					$( '.wc-paystack-meta-order-id, .wc-paystack-meta-name, .wc-paystack-meta-email, .wc-paystack-meta-phone, .wc-paystack-meta-billing-address, .wc-paystack-meta-shipping-address, .wc-paystack-meta-products' ).closest( 'tr' ).show();
				} else {
					$( '.wc-paystack-meta-order-id, .wc-paystack-meta-name, .wc-paystack-meta-email, .wc-paystack-meta-phone, .wc-paystack-meta-billing-address, .wc-paystack-meta-shipping-address, .wc-paystack-meta-products' ).closest( 'tr' ).hide();
				}
			} ).change();

			// Toggle Bank filters settings.
			$( '.wc-paystack-payment-channels' ).on( 'change', function() {

				var channels = $( ".wc-paystack-payment-channels" ).val();

				if ( $.inArray( 'card', channels ) != '-1' ) {
					$( '.wc-paystack-cards-allowed' ).closest( 'tr' ).show();
					$( '.wc-paystack-banks-allowed' ).closest( 'tr' ).show();
				}
				else {
					$( '.wc-paystack-cards-allowed' ).closest( 'tr' ).hide();
					$( '.wc-paystack-banks-allowed' ).closest( 'tr' ).hide();
				}

			} ).change();

			$( ".wc-paystack-payment-icons" ).select2( {
				templateResult: formatPaystackPaymentIcons,
				templateSelection: formatPaystackPaymentIconDisplay
			} );

			$( '#woocommerce_paystack_test_secret_key, #woocommerce_paystack_live_secret_key' ).after(
				'<button class="wc-paystack-toggle-secret" style="height: 30px; margin-left: 2px; cursor: pointer"><span class="dashicons dashicons-visibility"></span></button>'
			);

            $( '#woocommerce_paystack_test_webhook_signing_secret, #woocommerce_paystack_live_webhook_signing_secret' ).after(
                '<button class="wc-paystack-toggle-secret" style="height: 30px; margin-left: 2px; cursor: pointer"><span class="dashicons dashicons-visibility"></span></button>'
            );

			$( '.wc-paystack-toggle-secret' ).on( 'click', function( event ) {
				event.preventDefault();

				let $dashicon = $( this ).closest( 'button' ).find( '.dashicons' );
				let $input = $( this ).closest( 'tr' ).find( '.input-text' );
				let inputType = $input.attr( 'type' );

				if ( 'text' == inputType ) {
					$input.attr( 'type', 'password' );
					$dashicon.removeClass( 'dashicons-hidden' );
					$dashicon.addClass( 'dashicons-visibility' );
				} else {
					$input.attr( 'type', 'text' );
					$dashicon.removeClass( 'dashicons-visibility' );
					$dashicon.addClass( 'dashicons-hidden' );
				}
			} );
		}
	};

	function formatPaystackPaymentIcons( payment_method ) {
		if ( !payment_method.id ) {
			return payment_method.text;
		}

		var $payment_method = $(
			'<span><img src=" ' + wc_paystack_admin_params.plugin_url + '/assets/images/' + payment_method.element.value.toLowerCase() + '.png" class="img-flag" style="height: 15px; weight:18px;" /> ' + payment_method.text + '</span>'
		);

		return $payment_method;
	};

	function formatPaystackPaymentIconDisplay( payment_method ) {
		return payment_method.text;
	};

	wc_paystack_admin.init();

} );
