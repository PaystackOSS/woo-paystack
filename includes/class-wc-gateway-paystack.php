<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Gateway_Paystack extends WC_Payment_Gateway_CC {

	/**
	 * Is test mode active?
	 *
	 * @var bool
	 */
	public $testmode;

	/**
	 * Should orders be marked as complete after payment?
	 *
	 * @var bool
	 */
	public $autocomplete_order;

	/**
	 * Paystack payment page type.
	 *
	 * @var string
	 */
	public $payment_page;

	/**
	 * Paystack test public key.
	 *
	 * @var string
	 */
	public $test_public_key;

	/**
	 * Paystack test secret key.
	 *
	 * @var string
	 */
	public $test_secret_key;

	/**
	 * Paystack live public key.
	 *
	 * @var string
	 */
	public $live_public_key;

	/**
	 * Paystack live secret key.
	 *
	 * @var string
	 */
	public $live_secret_key;

	/**
	 * Should we save customer cards?
	 *
	 * @var bool
	 */
	public $saved_cards;

	/**
	 * Should Paystack split payment be enabled.
	 *
	 * @var bool
	 */
	public $split_payment;

	/**
	 * Should the cancel & remove order button be removed on the pay for order page.
	 *
	 * @var bool
	 */
	public $remove_cancel_order_button;

	/**
	 * Paystack sub account code.
	 *
	 * @var string
	 */
	public $subaccount_code;

	/**
	 * Who bears Paystack charges?
	 *
	 * @var string
	 */
	public $charges_account;

	/**
	 * A flat fee to charge the sub account for each transaction.
	 *
	 * @var string
	 */
	public $transaction_charges;

	/**
	 * Should custom metadata be enabled?
	 *
	 * @var bool
	 */
	public $custom_metadata;

	/**
	 * Should the order id be sent as a custom metadata to Paystack?
	 *
	 * @var bool
	 */
	public $meta_order_id;

	/**
	 * Should the customer name be sent as a custom metadata to Paystack?
	 *
	 * @var bool
	 */
	public $meta_name;

	/**
	 * Should the billing email be sent as a custom metadata to Paystack?
	 *
	 * @var bool
	 */
	public $meta_email;

	/**
	 * Should the billing phone be sent as a custom metadata to Paystack?
	 *
	 * @var bool
	 */
	public $meta_phone;

	/**
	 * Should the billing address be sent as a custom metadata to Paystack?
	 *
	 * @var bool
	 */
	public $meta_billing_address;

	/**
	 * Should the shipping address be sent as a custom metadata to Paystack?
	 *
	 * @var bool
	 */
	public $meta_shipping_address;

	/**
	 * Should the order items be sent as a custom metadata to Paystack?
	 *
	 * @var bool
	 */
	public $meta_products;

	/**
	 * API public key
	 *
	 * @var string
	 */
	public $public_key;

	/**
	 * API secret key
	 *
	 * @var string
	 */
	public $secret_key;

	/**
	 * Gateway disabled message
	 *
	 * @var string
	 */
	public $msg;

	/**
	 * Payment channels.
	 *
	 * @var array
	 */
	public $payment_channels = array();

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->id           = 'paystack';
		$this->method_title = __( 'Paystack', 'woo-paystack' );
		/* translators: 1: Paystack signup URL, 2: Paystack API keys URL. */
		$this->method_description = sprintf( __( 'Paystack provide merchants with the tools and services needed to accept online payments from local and international customers using Mastercard, Visa, Verve Cards and Bank Accounts. <a href="%1$s" target="_blank">Sign up</a> for a Paystack account, and <a href="%2$s" target="_blank">get your API keys</a>.', 'woo-paystack' ), esc_url( 'https://paystack.com' ), esc_url( 'https://dashboard.paystack.com/#/settings/developers' ) );
		$this->has_fields         = true;

		$this->payment_page = $this->get_option( 'payment_page' );

		$this->supports = array(
			'products',
			'refunds',
			'tokenization',
			'subscriptions',
			'multiple_subscriptions',
			'subscription_cancellation',
			'subscription_suspension',
			'subscription_reactivation',
			'subscription_amount_changes',
			'subscription_date_changes',
			'subscription_payment_method_change',
			'subscription_payment_method_change_customer',
		);

		// Load the form fields
		$this->init_form_fields();

		// Load the settings
		$this->init_settings();

		// Get setting values

		$this->title              = $this->get_option( 'title' );
		$this->description        = $this->get_option( 'description' );
		$this->enabled            = $this->get_option( 'enabled' );
		$this->testmode           = $this->get_option( 'testmode' ) === 'yes' ? true : false;
		$this->autocomplete_order = $this->get_option( 'autocomplete_order' ) === 'yes' ? true : false;

		$this->test_public_key = $this->get_option( 'test_public_key' );
		$this->test_secret_key = $this->get_option( 'test_secret_key' );

		$this->live_public_key = $this->get_option( 'live_public_key' );
		$this->live_secret_key = $this->get_option( 'live_secret_key' );

		$this->saved_cards = $this->get_option( 'saved_cards' ) === 'yes' ? true : false;

		$this->split_payment              = $this->get_option( 'split_payment' ) === 'yes' ? true : false;
		$this->remove_cancel_order_button = $this->get_option( 'remove_cancel_order_button' ) === 'yes' ? true : false;
		$this->subaccount_code            = $this->get_option( 'subaccount_code' );
		$this->charges_account            = $this->get_option( 'split_payment_charge_account' );
		$this->transaction_charges        = $this->get_option( 'split_payment_transaction_charge' );

		$this->custom_metadata = $this->get_option( 'custom_metadata' ) === 'yes' ? true : false;

		$this->meta_order_id         = $this->get_option( 'meta_order_id' ) === 'yes' ? true : false;
		$this->meta_name             = $this->get_option( 'meta_name' ) === 'yes' ? true : false;
		$this->meta_email            = $this->get_option( 'meta_email' ) === 'yes' ? true : false;
		$this->meta_phone            = $this->get_option( 'meta_phone' ) === 'yes' ? true : false;
		$this->meta_billing_address  = $this->get_option( 'meta_billing_address' ) === 'yes' ? true : false;
		$this->meta_shipping_address = $this->get_option( 'meta_shipping_address' ) === 'yes' ? true : false;
		$this->meta_products         = $this->get_option( 'meta_products' ) === 'yes' ? true : false;

		$this->public_key = $this->testmode ? $this->test_public_key : $this->live_public_key;
		$this->secret_key = $this->testmode ? $this->test_secret_key : $this->live_secret_key;

		// Hooks
		add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		add_action(
			'woocommerce_update_options_payment_gateways_' . $this->id,
			array(
				$this,
				'process_admin_options',
			)
		);

		add_action( 'woocommerce_receipt_' . $this->id, array( $this, 'receipt_page' ) );

		// Payment listener/API hook.
		add_action( 'woocommerce_api_wc_gateway_paystack', array( $this, 'verify_paystack_transaction' ) );

		// Webhook listener/API hook.
		add_action( 'woocommerce_api_tbz_wc_paystack_webhook', array( $this, 'process_webhooks' ) );

		// Check if the gateway can be used.
		if ( ! $this->is_valid_for_use() ) {
			$this->enabled = false;
		}

	}

	/**
	 * Check if this gateway is enabled and available in the user's country.
	 */
	public function is_valid_for_use() {

		if ( ! in_array( get_woocommerce_currency(), apply_filters( 'woocommerce_paystack_supported_currencies', array( 'NGN', 'USD', 'ZAR', 'GHS', 'KES', 'XOF', 'EGP', 'RWF' ) ) ) ) {

			/* translators: %s: WooCommerce general settings URL. */
			$this->msg = sprintf( __( 'Paystack does not support your store currency. Kindly set it to either NGN (&#8358), GHS (&#x20b5;), USD (&#36;), KES (KSh), RWF (R₣), ZAR (R), XOF (CFA), or EGP (E£) <a href="%s">here</a>', 'woo-paystack' ), esc_url( admin_url( 'admin.php?page=wc-settings&tab=general' ) ) );

			return false;

		}

		return true;

	}

	/**
	 * Display paystack payment icon.
	 */
	public function get_icon() {

		$base_location = wc_get_base_location();

		if ( 'GH' === $base_location['country'] ) {
			$icon = '<img src="' . WC_HTTPS::force_https_url( plugins_url( 'assets/images/paystack-gh.png', WC_PAYSTACK_MAIN_FILE ) ) . '" alt="Paystack Payment Options" />';
		} elseif ( 'ZA' === $base_location['country'] ) {
			$icon = '<img src="' . WC_HTTPS::force_https_url( plugins_url( 'assets/images/paystack-za.png', WC_PAYSTACK_MAIN_FILE ) ) . '" alt="Paystack Payment Options" />';
		} elseif ( 'KE' === $base_location['country'] ) {
			$icon = '<img src="' . WC_HTTPS::force_https_url( plugins_url( 'assets/images/paystack-ke.png', WC_PAYSTACK_MAIN_FILE ) ) . '" alt="Paystack Payment Options" />';
		} elseif ( 'CI' === $base_location['country'] ) {
			$icon = '<img src="' . WC_HTTPS::force_https_url( plugins_url( 'assets/images/paystack-civ.png', WC_PAYSTACK_MAIN_FILE ) ) . '" alt="Paystack Payment Options" />';
		} else {
			$icon = '<img src="' . WC_HTTPS::force_https_url( plugins_url( 'assets/images/paystack-wc.png', WC_PAYSTACK_MAIN_FILE ) ) . '" alt="Paystack Payment Options" />';
		}

		return apply_filters( 'woocommerce_gateway_icon', $icon, $this->id );

	}

	/**
	 * Check if Paystack merchant details is filled.
	 */
	public function admin_notices() {

		if ( 'no' === $this->enabled ) {
			return;
		}

		// Check required fields.
		if ( ! ( $this->public_key && $this->secret_key ) ) {
			echo '<div class="error"><p>' . wp_kses(
				sprintf(
					/* translators: %s: Paystack gateway settings URL. */
					__( 'Please enter your Paystack merchant details <a href="%s">here</a> to be able to use the Paystack WooCommerce plugin.', 'woo-paystack' ),
					esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=paystack' ) )
				),
				array(
					'a' => array(
						'href' => array(),
					),
				)
			) . '</p></div>';
			return;
		}

	}

	/**
	 * Check if Paystack gateway is enabled.
	 *
	 * @return bool
	 */
	public function is_available() {

		if ( 'yes' === $this->enabled ) {

			if ( ! ( $this->public_key && $this->secret_key ) ) {

				return false;

			}

			return true;

		}

		return false;

	}

	/**
	 * Admin Panel Options.
	 */
	public function admin_options() {

		?>

		<h2><?php esc_html_e( 'Paystack', 'woo-paystack' ); ?>
		<?php
		if ( function_exists( 'wc_back_link' ) ) {
			wc_back_link( __( 'Return to payments', 'woo-paystack' ), esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout' ) ) );
		}
		?>
		</h2>

		<h4>
			<strong>
				<?php
				printf(
					wp_kses(
						/* translators: %s: Paystack dashboard developer settings URL. */
						__( 'Optional: To avoid situations where bad network makes it impossible to verify transactions, set your webhook URL <a href="%s" target="_blank" rel="noopener noreferrer">here</a> to the URL below', 'woo-paystack' ),
						array(
							'a' => array(
								'href'   => array(),
								'target' => array(),
								'rel'    => array(),
							),
						)
					),
					esc_url( 'https://dashboard.paystack.com/#/settings/developers' )
				);
				?>
			</strong>
		</h4>

		<p style="color: red">
			<code><?php echo esc_url( WC()->api_request_url( 'Tbz_WC_Paystack_Webhook' ) ); ?></code>
		</p>

		<?php

		if ( $this->is_valid_for_use() ) {

			echo '<table class="form-table">';
			$this->generate_settings_html();
			echo '</table>';

		} else {
			?>
			<div class="inline error"><p><strong><?php esc_html_e( 'Paystack Payment Gateway Disabled', 'woo-paystack' ); ?></strong>: <?php echo wp_kses(
				$this->msg,
				array(
					'a' => array(
						'href' => array(),
					),
				)
			); ?></p></div>

			<?php
		}

	}

	/**
	 * Initialise Gateway Settings Form Fields.
	 */
	public function init_form_fields() {

		$form_fields = array(
			'enabled'                          => array(
				'title'       => __( 'Enable/Disable', 'woo-paystack' ),
				'label'       => __( 'Enable Paystack', 'woo-paystack' ),
				'type'        => 'checkbox',
				'description' => __( 'Enable Paystack as a payment option on the checkout page.', 'woo-paystack' ),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'title'                            => array(
				'title'       => __( 'Title', 'woo-paystack' ),
				'type'        => 'text',
				'description' => __( 'This controls the payment method title which the user sees during checkout.', 'woo-paystack' ),
				'default'     => __( 'Debit/Credit Cards', 'woo-paystack' ),
				'desc_tip'    => true,
			),
			'description'                      => array(
				'title'       => __( 'Description', 'woo-paystack' ),
				'type'        => 'textarea',
				'description' => __( 'This controls the payment method description which the user sees during checkout.', 'woo-paystack' ),
				'default'     => __( 'Make payment using your debit and credit cards', 'woo-paystack' ),
				'desc_tip'    => true,
			),
			'testmode'                         => array(
				'title'       => __( 'Test mode', 'woo-paystack' ),
				'label'       => __( 'Enable Test Mode', 'woo-paystack' ),
				'type'        => 'checkbox',
				'description' => __( 'Test mode enables you to test payments before going live. <br />Once the LIVE MODE is enabled on your Paystack account uncheck this.', 'woo-paystack' ),
				'default'     => 'yes',
				'desc_tip'    => true,
			),
			'payment_page'                     => array(
				'title'       => __( 'Payment Option', 'woo-paystack' ),
				'type'        => 'select',
				'description' => __( 'Popup shows the payment popup on the page while Redirect will redirect the customer to Paystack to make payment.', 'woo-paystack' ),
				'default'     => '',
				'desc_tip'    => false,
				'options'     => array(
					''         => __( 'Select One', 'woo-paystack' ),
					'inline'   => __( 'Popup', 'woo-paystack' ),
					'redirect' => __( 'Redirect', 'woo-paystack' ),
				),
			),
			'test_secret_key'                  => array(
				'title'       => __( 'Test Secret Key', 'woo-paystack' ),
				'type'        => 'password',
				'description' => __( 'Enter your Test Secret Key here', 'woo-paystack' ),
				'default'     => '',
			),
			'test_public_key'                  => array(
				'title'       => __( 'Test Public Key', 'woo-paystack' ),
				'type'        => 'text',
				'description' => __( 'Enter your Test Public Key here.', 'woo-paystack' ),
				'default'     => '',
			),
			'live_secret_key'                  => array(
				'title'       => __( 'Live Secret Key', 'woo-paystack' ),
				'type'        => 'password',
				'description' => __( 'Enter your Live Secret Key here.', 'woo-paystack' ),
				'default'     => '',
			),
			'live_public_key'                  => array(
				'title'       => __( 'Live Public Key', 'woo-paystack' ),
				'type'        => 'text',
				'description' => __( 'Enter your Live Public Key here.', 'woo-paystack' ),
				'default'     => '',
			),
			'autocomplete_order'               => array(
				'title'       => __( 'Autocomplete Order After Payment', 'woo-paystack' ),
				'label'       => __( 'Autocomplete Order', 'woo-paystack' ),
				'type'        => 'checkbox',
				'class'       => 'wc-paystack-autocomplete-order',
				'description' => __( 'If enabled, the order will be marked as complete after successful payment', 'woo-paystack' ),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'remove_cancel_order_button'       => array(
				'title'       => __( 'Remove Cancel Order & Restore Cart Button', 'woo-paystack' ),
				'label'       => __( 'Remove the cancel order & restore cart button on the pay for order page', 'woo-paystack' ),
				'type'        => 'checkbox',
				'description' => '',
				'default'     => 'no',
			),
			'split_payment'                    => array(
				'title'       => __( 'Split Payment', 'woo-paystack' ),
				'label'       => __( 'Enable Split Payment', 'woo-paystack' ),
				'type'        => 'checkbox',
				'description' => '',
				'class'       => 'woocommerce_paystack_split_payment',
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'subaccount_code'                  => array(
				'title'       => __( 'Subaccount Code', 'woo-paystack' ),
				'type'        => 'text',
				'description' => __( 'Enter the subaccount code here.', 'woo-paystack' ),
				'class'       => 'woocommerce_paystack_subaccount_code',
				'default'     => '',
			),
			'split_payment_transaction_charge' => array(
				'title'             => __( 'Split Payment Transaction Charge', 'woo-paystack' ),
				'type'              => 'number',
				'description'       => __( 'A flat fee to charge the subaccount for this transaction, in Naira (&#8358;). This overrides the split percentage set when the subaccount was created. Ideally, you will need to use this if you are splitting in flat rates (since subaccount creation only allows for percentage split). e.g. 100 for a &#8358;100 flat fee.', 'woo-paystack' ),
				'class'             => __( 'woocommerce_paystack_split_payment_transaction_charge', 'woo-paystack' ),
				'default'           => '',
				'custom_attributes' => array(
					'min'  => 1,
					'step' => 0.1,
				),
				'desc_tip'          => false,
			),
			'split_payment_charge_account'     => array(
				'title'       => __( 'Paystack Charges Bearer', 'woo-paystack' ),
				'type'        => 'select',
				'description' => __( 'Who bears Paystack charges?', 'woo-paystack' ),
				'class'       => 'woocommerce_paystack_split_payment_charge_account',
				'default'     => '',
				'desc_tip'    => false,
				'options'     => array(
					''           => __( 'Select One', 'woo-paystack' ),
					'account'    => __( 'Account', 'woo-paystack' ),
					'subaccount' => __( 'Subaccount', 'woo-paystack' ),
				),
			),
			'custom_gateways'                  => array(
				'title'       => __( 'Additional Paystack Gateways', 'woo-paystack' ),
				'type'        => 'select',
				'description' => __( 'Create additional custom Paystack based gateways. This allows you to create additional Paystack gateways using custom filters. You can create a gateway that accepts only verve cards, a gateway that accepts only bank payment, a gateway that accepts a specific bank issued cards.', 'woo-paystack' ),
				'default'     => '',
				'desc_tip'    => true,
				'options'     => array(
					''  => __( 'Select One', 'woo-paystack' ),
					'1' => __( '1 gateway', 'woo-paystack' ),
					'2' => __( '2 gateways', 'woo-paystack' ),
					'3' => __( '3 gateways', 'woo-paystack' ),
					'4' => __( '4 gateways', 'woo-paystack' ),
					'5' => __( '5 gateways', 'woo-paystack' ),
				),
			),
			'saved_cards'                      => array(
				'title'       => __( 'Saved Cards', 'woo-paystack' ),
				'label'       => __( 'Enable Payment via Saved Cards', 'woo-paystack' ),
				'type'        => 'checkbox',
				'description' => __( 'If enabled, users will be able to pay with a saved card during checkout. Card details are saved on Paystack servers, not on your store.<br>Note that you need to have a valid SSL certificate installed.', 'woo-paystack' ),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'custom_metadata'                  => array(
				'title'       => __( 'Custom Metadata', 'woo-paystack' ),
				'label'       => __( 'Enable Custom Metadata', 'woo-paystack' ),
				'type'        => 'checkbox',
				'class'       => 'wc-paystack-metadata',
				'description' => __( 'If enabled, you will be able to send more information about the order to Paystack.', 'woo-paystack' ),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'meta_order_id'                    => array(
				'title'       => __( 'Order ID', 'woo-paystack' ),
				'label'       => __( 'Send Order ID', 'woo-paystack' ),
				'type'        => 'checkbox',
				'class'       => 'wc-paystack-meta-order-id',
				'description' => __( 'If checked, the Order ID will be sent to Paystack', 'woo-paystack' ),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'meta_name'                        => array(
				'title'       => __( 'Customer Name', 'woo-paystack' ),
				'label'       => __( 'Send Customer Name', 'woo-paystack' ),
				'type'        => 'checkbox',
				'class'       => 'wc-paystack-meta-name',
				'description' => __( 'If checked, the customer full name will be sent to Paystack', 'woo-paystack' ),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'meta_email'                       => array(
				'title'       => __( 'Customer Email', 'woo-paystack' ),
				'label'       => __( 'Send Customer Email', 'woo-paystack' ),
				'type'        => 'checkbox',
				'class'       => 'wc-paystack-meta-email',
				'description' => __( 'If checked, the customer email address will be sent to Paystack', 'woo-paystack' ),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'meta_phone'                       => array(
				'title'       => __( 'Customer Phone', 'woo-paystack' ),
				'label'       => __( 'Send Customer Phone', 'woo-paystack' ),
				'type'        => 'checkbox',
				'class'       => 'wc-paystack-meta-phone',
				'description' => __( 'If checked, the customer phone will be sent to Paystack', 'woo-paystack' ),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'meta_billing_address'             => array(
				'title'       => __( 'Order Billing Address', 'woo-paystack' ),
				'label'       => __( 'Send Order Billing Address', 'woo-paystack' ),
				'type'        => 'checkbox',
				'class'       => 'wc-paystack-meta-billing-address',
				'description' => __( 'If checked, the order billing address will be sent to Paystack', 'woo-paystack' ),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'meta_shipping_address'            => array(
				'title'       => __( 'Order Shipping Address', 'woo-paystack' ),
				'label'       => __( 'Send Order Shipping Address', 'woo-paystack' ),
				'type'        => 'checkbox',
				'class'       => 'wc-paystack-meta-shipping-address',
				'description' => __( 'If checked, the order shipping address will be sent to Paystack', 'woo-paystack' ),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'meta_products'                    => array(
				'title'       => __( 'Product(s) Purchased', 'woo-paystack' ),
				'label'       => __( 'Send Product(s) Purchased', 'woo-paystack' ),
				'type'        => 'checkbox',
				'class'       => 'wc-paystack-meta-products',
				'description' => __( 'If checked, the product(s) purchased will be sent to Paystack', 'woo-paystack' ),
				'default'     => 'no',
				'desc_tip'    => true,
			),
		);

		if ( 'NGN' !== get_woocommerce_currency() ) {
			unset( $form_fields['custom_gateways'] );
		}

		$this->form_fields = $form_fields;

	}

	/**
	 * Payment form on checkout page
	 */
	public function payment_fields() {

		if ( $this->description ) {
			echo wp_kses_post( wpautop( wptexturize( $this->description ) ) );
		}

		if ( ! is_ssl() ) {
			return;
		}

		if ( $this->supports( 'tokenization' ) && is_checkout() && $this->saved_cards && is_user_logged_in() ) {
			$this->tokenization_script();
			$this->saved_payment_methods();
			$this->save_payment_method_checkbox();
		}

	}

	/**
	 * Outputs scripts used for paystack payment.
	 */
	public function payment_scripts() {

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only WooCommerce checkout query arg.
		$pay_for_order = isset( $_GET['pay_for_order'] );

		if ( $pay_for_order || ! is_checkout_pay_page() ) {
			return;
		}

		if ( 'no' === $this->enabled ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- WooCommerce validates this order key against the order below.
		$order_key = isset( $_GET['key'] ) ? sanitize_text_field( wp_unslash( $_GET['key'] ) ) : '';
		$order_id  = absint( get_query_var( 'order-pay' ) );

		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return;
		}

		if ( $this->id !== $order->get_payment_method() ) {
			return;
		}

		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		wp_enqueue_script( 'jquery' );

		wp_enqueue_script( 'paystack', 'https://js.paystack.co/v2/inline.js', array( 'jquery' ), WC_PAYSTACK_VERSION, false );

		wp_enqueue_script( 'wc_paystack', plugins_url( 'assets/js/paystack' . $suffix . '.js', WC_PAYSTACK_MAIN_FILE ), array( 'jquery', 'paystack' ), WC_PAYSTACK_VERSION, false );

		$paystack_params = array(
			'key' => $this->public_key,
		);

		if ( is_checkout_pay_page() && get_query_var( 'order-pay' ) ) {

			$email         = $order->get_billing_email();
			$amount        = $order->get_total() * 100;
			$txnref        = $order_id . '_' . time();
			$the_order_id  = $order->get_id();
			$the_order_key = $order->get_order_key();
			$currency      = $order->get_currency();

			if ( $the_order_id === $order_id && $the_order_key === $order_key ) {

				$paystack_params['email']    = $email;
				$paystack_params['amount']   = absint( $amount );
				$paystack_params['txnref']   = $txnref;
				$paystack_params['currency'] = $currency;

			}

			if ( $this->split_payment ) {

				$paystack_params['subaccount_code'] = $this->subaccount_code;
				$paystack_params['charges_account'] = $this->charges_account;

				if ( empty( $this->transaction_charges ) ) {
					$paystack_params['transaction_charges'] = '';
				} else {
					$paystack_params['transaction_charges'] = $this->transaction_charges * 100;
				}
			}

			if ( $this->custom_metadata ) {

				if ( $this->meta_order_id ) {

					$paystack_params['meta_order_id'] = $order_id;

				}

				if ( $this->meta_name ) {

					$paystack_params['meta_name'] = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();

				}

				if ( $this->meta_email ) {

					$paystack_params['meta_email'] = $email;

				}

				if ( $this->meta_phone ) {

					$paystack_params['meta_phone'] = $order->get_billing_phone();

				}

				if ( $this->meta_products ) {

					$line_items = $order->get_items();

					$products = '';

					foreach ( $line_items as $item_id => $item ) {
						$name      = $item['name'];
						$quantity  = $item['qty'];
						$products .= $name . ' (Qty: ' . $quantity . ')';
						$products .= ' | ';
					}

					$products = rtrim( $products, ' | ' );

					$paystack_params['meta_products'] = $products;

				}

				if ( $this->meta_billing_address ) {

					$billing_address = $order->get_formatted_billing_address();
					$billing_address = esc_html( preg_replace( '#<br\s*/?>#i', ', ', $billing_address ) );

					$paystack_params['meta_billing_address'] = $billing_address;

				}

				if ( $this->meta_shipping_address ) {

					$shipping_address = $order->get_formatted_shipping_address();
					$shipping_address = esc_html( preg_replace( '#<br\s*/?>#i', ', ', $shipping_address ) );

					if ( empty( $shipping_address ) ) {

						$billing_address = $order->get_formatted_billing_address();
						$billing_address = esc_html( preg_replace( '#<br\s*/?>#i', ', ', $billing_address ) );

						$shipping_address = $billing_address;

					}

					$paystack_params['meta_shipping_address'] = $shipping_address;

				}
			}

			$order->update_meta_data( '_paystack_txn_ref', $txnref );
			$order->save();
		}

		$payment_channels = $this->get_gateway_payment_channels( $order );

		if ( ! empty( $payment_channels ) ) {
			if ( in_array( 'card', $payment_channels, true ) ) {
				$paystack_params['card_channel'] = 'true';
			}

			if ( in_array( 'bank', $payment_channels, true ) ) {
				$paystack_params['bank_channel'] = 'true';
			}

			if ( in_array( 'ussd', $payment_channels, true ) ) {
				$paystack_params['ussd_channel'] = 'true';
			}

			if ( in_array( 'qr', $payment_channels, true ) ) {
				$paystack_params['qr_channel'] = 'true';
			}

			if ( in_array( 'bank_transfer', $payment_channels, true ) ) {
				$paystack_params['bank_transfer_channel'] = 'true';
			}
		}

		wp_localize_script( 'wc_paystack', 'wc_paystack_params', $paystack_params );

	}

	/**
	 * Load admin scripts.
	 */
	public function admin_scripts() {

		if ( 'woocommerce_page_wc-settings' !== get_current_screen()->id ) {
			return;
		}

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		$paystack_admin_params = array(
			'plugin_url' => WC_PAYSTACK_URL,
		);

		wp_enqueue_script( 'wc_paystack_admin', plugins_url( 'assets/js/paystack-admin' . $suffix . '.js', WC_PAYSTACK_MAIN_FILE ), array(), WC_PAYSTACK_VERSION, true );

		wp_localize_script( 'wc_paystack_admin', 'wc_paystack_admin_params', $paystack_admin_params );

	}

	/**
	 * Process the payment.
	 *
	 * @param int $order_id
	 *
	 * @return array|void
	 */
	public function process_payment( $order_id ) {
		$payment_token        = 'wc-' . trim( $this->id ) . '-payment-token';
		$posted_payment_token = '';

		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- WooCommerce checkout processing verifies the checkout nonce.
		if ( isset( $_POST[ $payment_token ] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing -- WooCommerce checkout processing verifies the checkout nonce.
			$posted_payment_token = sanitize_text_field( wp_unslash( $_POST[ $payment_token ] ) );
		}

		if ( $posted_payment_token && 'new' !== $posted_payment_token ) {

			$token = \WC_Payment_Tokens::get( $posted_payment_token );

			if ( $token->get_user_id() !== get_current_user_id() ) {

				wc_add_notice( 'Invalid token ID', 'error' );

				return;
			}

			$token_payment_status = $this->process_token_payment( $token->get_token(), $order_id );

			if ( ! $token_payment_status ) {
				return;
			}

			$order = wc_get_order( $order_id );

			return array(
				'result'   => 'success',
				'redirect' => $this->get_return_url( $order ),
			);
		}

		$order = wc_get_order( $order_id );

		$new_payment_method = 'wc-' . trim( $this->id ) . '-new-payment-method';

		// phpcs:ignore WordPress.Security.NonceVerification
		if ( isset( $_POST[ $new_payment_method ] ) && ( true === (bool) $_POST[ $new_payment_method ] && $this->saved_cards ) && is_user_logged_in() ) {

			$order->update_meta_data( '_wc_paystack_save_card', true );

			$order->save();
		}

		if ( 'redirect' === $this->payment_page ) {
			return $this->process_redirect_payment_option( $order_id );
		}

		return array(
			'result'   => 'success',
			'redirect' => $order->get_checkout_payment_url( true ),
		);
	}

	/**
	 * Process a redirect payment option payment.
	 *
	 * @since 5.7
	 * @param int $order_id
	 * @return array|void
	 */
	public function process_redirect_payment_option( $order_id ) {

		$order        = wc_get_order( $order_id );
		$amount       = $order->get_total() * 100;
		$txnref       = $order_id . '_' . time();
		$callback_url = WC()->api_request_url( 'WC_Gateway_Paystack' );

		$payment_channels = $this->get_gateway_payment_channels( $order );

		$paystack_params = array(
			'amount'       => absint( $amount ),
			'email'        => $order->get_billing_email(),
			'currency'     => $order->get_currency(),
			'reference'    => $txnref,
			'callback_url' => $callback_url,
		);

		if ( ! empty( $payment_channels ) ) {
			$paystack_params['channels'] = $payment_channels;
		}

		if ( $this->split_payment ) {

			$paystack_params['subaccount'] = $this->subaccount_code;
			$paystack_params['bearer']     = $this->charges_account;

			if ( empty( $this->transaction_charges ) ) {
				$paystack_params['transaction_charge'] = '';
			} else {
				$paystack_params['transaction_charge'] = $this->transaction_charges * 100;
			}
		}

		$paystack_params['metadata']['custom_fields'] = $this->get_custom_fields( $order_id );
		$paystack_params['metadata']['cancel_action'] = wc_get_cart_url();
		$paystack_params['metadata']['referrer']      = esc_url( site_url() );

		$order->update_meta_data( '_paystack_txn_ref', $txnref );
		$order->save();

		$paystack_url = 'https://api.paystack.co/transaction/initialize/';

		$headers = array(
			'Authorization' => 'Bearer ' . $this->secret_key,
			'Content-Type'  => 'application/json',
		);

		$args = array(
			'headers' => $headers,
			'timeout' => 60,
			'body'    => wp_json_encode( $paystack_params ),
		);

		$request = wp_remote_post( $paystack_url, $args );

		if ( ! is_wp_error( $request ) && 200 === wp_remote_retrieve_response_code( $request ) ) {

			$paystack_response = json_decode( wp_remote_retrieve_body( $request ) );

			return array(
				'result'   => 'success',
				'redirect' => $paystack_response->data->authorization_url,
			);

		}

		wc_add_notice( __( 'Unable to process payment try again', 'woo-paystack' ), 'error' );
	}

	/**
	 * Process a token payment.
	 *
	 * @param $token
	 * @param $order_id
	 *
	 * @return bool
	 */
	public function process_token_payment( $token, $order_id ) {

		if ( $token && $order_id ) {

			$order = wc_get_order( $order_id );

			$order_amount = $order->get_total() * 100;
			$txnref       = $order_id . '_' . time();

			$order->update_meta_data( '_paystack_txn_ref', $txnref );
			$order->save();

			$paystack_url = 'https://api.paystack.co/transaction/charge_authorization';

			$headers = array(
				'Content-Type'  => 'application/json',
				'Authorization' => 'Bearer ' . $this->secret_key,
			);

			$metadata['custom_fields'] = $this->get_custom_fields( $order_id );
			$metadata['referrer']      = esc_url( site_url() );

			if ( strpos( $token, '###' ) !== false ) {
				$payment_token  = explode( '###', $token );
				$auth_code      = $payment_token[0];
				$customer_email = $payment_token[1];
			} else {
				$auth_code      = $token;
				$customer_email = $order->get_billing_email();
			}

			$body = array(
				'email'              => $customer_email,
				'amount'             => absint( $order_amount ),
				'metadata'           => $metadata,
				'authorization_code' => $auth_code,
				'reference'          => $txnref,
				'currency'           => $order->get_currency(),
			);

			$args = array(
				'body'    => wp_json_encode( $body ),
				'headers' => $headers,
				'timeout' => 60,
			);

			$request = wp_remote_post( $paystack_url, $args );

			$response_code = wp_remote_retrieve_response_code( $request );

			if ( ! is_wp_error( $request ) && in_array( $response_code, array( 200, 400 ), true ) ) {

				$paystack_response = json_decode( wp_remote_retrieve_body( $request ) );

				if ( ( 200 === $response_code ) && ( 'success' === strtolower( $paystack_response->data->status ) ) ) {

					$order = wc_get_order( $order_id );

					if ( in_array( $order->get_status(), array( 'processing', 'completed', 'on-hold' ), true ) ) {

						wp_safe_redirect( $this->get_return_url( $order ) );

						exit;

					}

					$order_total      = $order->get_total();
					$order_currency   = $order->get_currency();
					$currency_symbol  = get_woocommerce_currency_symbol( $order_currency );
					$amount_paid      = $paystack_response->data->amount / 100;
					$paystack_ref     = $paystack_response->data->reference;
					$payment_currency = $paystack_response->data->currency;
					$gateway_symbol   = get_woocommerce_currency_symbol( $payment_currency );

					// check if the amount paid is equal to the order amount.
					if ( round( $amount_paid, 2 ) < round( $order_total, 2 ) ) {

						$order->update_status( 'on-hold', '' );

						$order->add_meta_data( '_transaction_id', $paystack_ref, true );

						/* translators: 1: line break, 2: line break, 3: line break. */
						$notice      = sprintf( __( 'Thank you for shopping with us.%1$sYour payment transaction was successful, but the amount paid is not the same as the total order amount.%2$sYour order is currently on hold.%3$sKindly contact us for more information regarding your order and payment status.', 'woo-paystack' ), '<br />', '<br />', '<br />' );
						$notice_type = 'notice';

						// Add Customer Order Note
						$order->add_order_note( $notice, 1 );

						// Add Admin Order Note
						/* translators: 1: line break, 2: line break, 3: line break, 4: paid currency symbol, 5: amount paid, 6: order currency symbol, 7: order total, 8: line break, 9: Paystack transaction reference. */
						$admin_order_note = sprintf( __( '<strong>Look into this order</strong>%1$sThis order is currently on hold.%2$sReason: Amount paid is less than the total order amount.%3$sAmount Paid was <strong>%4$s (%5$s)</strong> while the total order amount is <strong>%6$s (%7$s)</strong>%8$s<strong>Paystack Transaction Reference:</strong> %9$s', 'woo-paystack' ), '<br />', '<br />', '<br />', $currency_symbol, $amount_paid, $currency_symbol, $order_total, '<br />', $paystack_ref );
						$order->add_order_note( $admin_order_note );

						wc_add_notice( $notice, $notice_type );

					} else {

						if ( $payment_currency !== $order_currency ) {

							$order->update_status( 'on-hold', '' );

							$order->update_meta_data( '_transaction_id', $paystack_ref );

							/* translators: 1: line break, 2: line break, 3: line break. */
							$notice      = sprintf( __( 'Thank you for shopping with us.%1$sYour payment was successful, but the payment currency is different from the order currency.%2$sYour order is currently on-hold.%3$sKindly contact us for more information regarding your order and payment status.', 'woo-paystack' ), '<br />', '<br />', '<br />' );
							$notice_type = 'notice';

							// Add Customer Order Note
							$order->add_order_note( $notice, 1 );

							// Add Admin Order Note
							/* translators: 1: line break, 2: line break, 3: line break, 4: order currency, 5: order currency symbol, 6: payment currency, 7: payment currency symbol, 8: line break, 9: Paystack transaction reference. */
							$admin_order_note = sprintf( __( '<strong>Look into this order</strong>%1$sThis order is currently on hold.%2$sReason: Order currency is different from the payment currency.%3$sOrder Currency is <strong>%4$s (%5$s)</strong> while the payment currency is <strong>%6$s (%7$s)</strong>%8$s<strong>Paystack Transaction Reference:</strong> %9$s', 'woo-paystack' ), '<br />', '<br />', '<br />', $order_currency, $currency_symbol, $payment_currency, $gateway_symbol, '<br />', $paystack_ref );
							$order->add_order_note( $admin_order_note );

							function_exists( 'wc_reduce_stock_levels' ) ? wc_reduce_stock_levels( $order_id ) : $order->reduce_order_stock();

							wc_add_notice( $notice, $notice_type );

						} else {

							$order->payment_complete( $paystack_ref );

							/* translators: %s: Paystack transaction reference. */
							$order->add_order_note( sprintf( __( 'Payment via Paystack successful (Transaction Reference: %s)', 'woo-paystack' ), $paystack_ref ) );

							if ( $this->is_autocomplete_order_enabled( $order ) ) {
								$order->update_status( 'completed' );
							}
						}
					}

					$order->save();

					$this->save_subscription_payment_token( $order_id, $paystack_response );

					WC()->cart->empty_cart();

					return true;

				} else {

					$order_notice  = __( 'Payment was declined by Paystack.', 'woo-paystack' );
					$failed_notice = __( 'Payment failed using the saved card. Kindly use another payment option.', 'woo-paystack' );

					if ( ! empty( $paystack_response->message ) ) {

						/* translators: %s: Paystack decline reason. */
						$order_notice = sprintf( __( 'Payment was declined by Paystack. Reason: %s.', 'woo-paystack' ), $paystack_response->message );
						/* translators: %s: Paystack decline reason. */
						$failed_notice = sprintf( __( 'Payment failed using the saved card. Reason: %s. Kindly use another payment option.', 'woo-paystack' ), $paystack_response->message );

					}

					$order->update_status( 'failed', $order_notice );

					wc_add_notice( $failed_notice, 'error' );

					do_action( 'wc_gateway_paystack_process_payment_error', $failed_notice, $order );

					return false;
				}
			}
		} else {

			wc_add_notice( __( 'Payment Failed.', 'woo-paystack' ), 'error' );

			return false;
		}

	}

	/**
	 * Show new card can only be added when placing an order notice.
	 */
	public function add_payment_method() {

		wc_add_notice( __( 'You can only add a new card when placing an order.', 'woo-paystack' ), 'error' );

	}

	/**
	 * Displays the payment page.
	 *
	 * @param $order_id
	 */
	public function receipt_page( $order_id ) {

		$order = wc_get_order( $order_id );

		echo '<div id="wc-paystack-form">';

		echo '<p>' . esc_html__( 'Thank you for your order, please click the button below to pay with Paystack.', 'woo-paystack' ) . '</p>';

		echo '<div id="paystack_form"><form id="order_review" method="post" action="' . esc_url( WC()->api_request_url( 'WC_Gateway_Paystack' ) ) . '"></form><button class="button" id="paystack-payment-button">' . esc_html__( 'Pay Now', 'woo-paystack' ) . '</button>';

		if ( ! $this->remove_cancel_order_button ) {
			echo '  <a class="button cancel" id="paystack-cancel-payment-button" href="' . esc_url( $order->get_cancel_order_url() ) . '">' . esc_html__( 'Cancel order & restore cart', 'woo-paystack' ) . '</a>';
		}

		echo '</div>';
		echo '</div>';

	}

	/**
	 * Verify Paystack payment.
	 */
	public function verify_paystack_transaction() {

		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Paystack redirects to this callback without a WordPress nonce; the reference is verified against Paystack.
		if ( isset( $_REQUEST['paystack_txnref'] ) ) {
			$paystack_txn_ref = sanitize_text_field( wp_unslash( $_REQUEST['paystack_txnref'] ) );
		} elseif ( isset( $_REQUEST['reference'] ) ) {
			$paystack_txn_ref = sanitize_text_field( wp_unslash( $_REQUEST['reference'] ) );
		} else {
			$paystack_txn_ref = false;
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		@ob_clean();

		if ( $paystack_txn_ref ) {

			$paystack_response = $this->get_paystack_transaction( $paystack_txn_ref );

			if ( false !== $paystack_response ) {

				if ( 'success' === $paystack_response->data->status ) {

					$order_details = explode( '_', $paystack_response->data->reference );
					$order_id      = (int) $order_details[0];
					$order         = wc_get_order( $order_id );

					if ( in_array( $order->get_status(), array( 'processing', 'completed', 'on-hold' ), true ) ) {

						wp_safe_redirect( $this->get_return_url( $order ) );

						exit;

					}

					$order_total      = $order->get_total();
					$order_currency   = $order->get_currency();
					$currency_symbol  = get_woocommerce_currency_symbol( $order_currency );
					$amount_paid      = $paystack_response->data->amount / 100;
					$paystack_ref     = $paystack_response->data->reference;
					$payment_currency = strtoupper( $paystack_response->data->currency );
					$gateway_symbol   = get_woocommerce_currency_symbol( $payment_currency );

					// check if the amount paid is equal to the order amount.
					if ( round( $amount_paid, 2 ) < round( $order_total, 2 ) ) {

						$order->update_status( 'on-hold', '' );

						$order->add_meta_data( '_transaction_id', $paystack_ref, true );

						/* translators: 1: line break, 2: line break, 3: line break. */
						$notice      = sprintf( __( 'Thank you for shopping with us.%1$sYour payment transaction was successful, but the amount paid is not the same as the total order amount.%2$sYour order is currently on hold.%3$sKindly contact us for more information regarding your order and payment status.', 'woo-paystack' ), '<br />', '<br />', '<br />' );
						$notice_type = 'notice';

						// Add Customer Order Note
						$order->add_order_note( $notice, 1 );

						// Add Admin Order Note
						/* translators: 1: line break, 2: line break, 3: line break, 4: paid currency symbol, 5: amount paid, 6: order currency symbol, 7: order total, 8: line break, 9: Paystack transaction reference. */
						$admin_order_note = sprintf( __( '<strong>Look into this order</strong>%1$sThis order is currently on hold.%2$sReason: Amount paid is less than the total order amount.%3$sAmount Paid was <strong>%4$s (%5$s)</strong> while the total order amount is <strong>%6$s (%7$s)</strong>%8$s<strong>Paystack Transaction Reference:</strong> %9$s', 'woo-paystack' ), '<br />', '<br />', '<br />', $currency_symbol, $amount_paid, $currency_symbol, $order_total, '<br />', $paystack_ref );
						$order->add_order_note( $admin_order_note );

						function_exists( 'wc_reduce_stock_levels' ) ? wc_reduce_stock_levels( $order_id ) : $order->reduce_order_stock();

						wc_add_notice( $notice, $notice_type );

					} else {

						if ( $payment_currency !== $order_currency ) {

							$order->update_status( 'on-hold', '' );

							$order->update_meta_data( '_transaction_id', $paystack_ref );

							/* translators: 1: line break, 2: line break, 3: line break. */
							$notice      = sprintf( __( 'Thank you for shopping with us.%1$sYour payment was successful, but the payment currency is different from the order currency.%2$sYour order is currently on-hold.%3$sKindly contact us for more information regarding your order and payment status.', 'woo-paystack' ), '<br />', '<br />', '<br />' );
							$notice_type = 'notice';

							// Add Customer Order Note
							$order->add_order_note( $notice, 1 );

							// Add Admin Order Note
							/* translators: 1: line break, 2: line break, 3: line break, 4: order currency, 5: order currency symbol, 6: payment currency, 7: payment currency symbol, 8: line break, 9: Paystack transaction reference. */
							$admin_order_note = sprintf( __( '<strong>Look into this order</strong>%1$sThis order is currently on hold.%2$sReason: Order currency is different from the payment currency.%3$sOrder Currency is <strong>%4$s (%5$s)</strong> while the payment currency is <strong>%6$s (%7$s)</strong>%8$s<strong>Paystack Transaction Reference:</strong> %9$s', 'woo-paystack' ), '<br />', '<br />', '<br />', $order_currency, $currency_symbol, $payment_currency, $gateway_symbol, '<br />', $paystack_ref );
							$order->add_order_note( $admin_order_note );

							function_exists( 'wc_reduce_stock_levels' ) ? wc_reduce_stock_levels( $order_id ) : $order->reduce_order_stock();

							wc_add_notice( $notice, $notice_type );

						} else {

							$order->payment_complete( $paystack_ref );
							/* translators: %s: Paystack transaction reference. */
							$order->add_order_note( sprintf( __( 'Payment via Paystack successful (Transaction Reference: %s)', 'woo-paystack' ), $paystack_ref ) );

							if ( $this->is_autocomplete_order_enabled( $order ) ) {
								$order->update_status( 'completed' );
							}
						}
					}

					$order->save();

					$this->save_card_details( $paystack_response, $order->get_user_id(), $order_id );

					WC()->cart->empty_cart();

				} else {

					$order_details = explode( '_', $paystack_txn_ref );

					$order_id = (int) $order_details[0];

					$order = wc_get_order( $order_id );

					$order->update_status( 'failed', __( 'Payment was declined by Paystack.', 'woo-paystack' ) );

				}
			}

			wp_safe_redirect( $this->get_return_url( $order ) );

			exit;
		}

		wp_safe_redirect( wc_get_page_permalink( 'cart' ) );

		exit;

	}

	/**
	 * Process Webhook.
	 */
	public function process_webhooks() {

		$request_method     = isset( $_SERVER['REQUEST_METHOD'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) : '';
		$paystack_signature = isset( $_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] ) ) : '';

		if ( empty( $paystack_signature ) || ( 'POST' !== strtoupper( $request_method ) ) ) {
			exit;
		}

		$json = file_get_contents( 'php://input' );

		// validate event do all at once to avoid timing attack.
		if ( $paystack_signature !== hash_hmac( 'sha512', $json, $this->secret_key ) ) {
			exit;
		}

		$event = json_decode( $json );

		if ( 'charge.success' !== strtolower( $event->event ) ) {
			return;
		}

		sleep( 10 );

		$paystack_response = $this->get_paystack_transaction( $event->data->reference );

		if ( false === $paystack_response ) {
			return;
		}

		$order_details = explode( '_', $paystack_response->data->reference );

		$order_id = (int) $order_details[0];

		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return;
		}

		$paystack_txn_ref = $order->get_meta( '_paystack_txn_ref' );

		if ( $paystack_response->data->reference !== $paystack_txn_ref ) {
			exit;
		}

		http_response_code( 200 );

		if ( in_array( $order->get_status(), array( 'processing', 'completed', 'on-hold' ), true ) ) {
			exit;
		}

		$order_currency = $order->get_currency();

		$currency_symbol = get_woocommerce_currency_symbol( $order_currency );

		$order_total = $order->get_total();

		$amount_paid = $paystack_response->data->amount / 100;

		$paystack_ref = $paystack_response->data->reference;

		$payment_currency = strtoupper( $paystack_response->data->currency );

		$gateway_symbol = get_woocommerce_currency_symbol( $payment_currency );

		// check if the amount paid is equal to the order amount.
		if ( round( $amount_paid, 2 ) < round( $order_total, 2 ) ) {

			$order->update_status( 'on-hold', '' );

			$order->add_meta_data( '_transaction_id', $paystack_ref, true );

			/* translators: 1: line break, 2: line break, 3: line break. */
			$notice      = sprintf( __( 'Thank you for shopping with us.%1$sYour payment transaction was successful, but the amount paid is not the same as the total order amount.%2$sYour order is currently on hold.%3$sKindly contact us for more information regarding your order and payment status.', 'woo-paystack' ), '<br />', '<br />', '<br />' );
			$notice_type = 'notice';

			// Add Customer Order Note.
			$order->add_order_note( $notice, 1 );

			// Add Admin Order Note.
			/* translators: 1: line break, 2: line break, 3: line break, 4: paid currency symbol, 5: amount paid, 6: order currency symbol, 7: order total, 8: line break, 9: Paystack transaction reference. */
			$admin_order_note = sprintf( __( '<strong>Look into this order</strong>%1$sThis order is currently on hold.%2$sReason: Amount paid is less than the total order amount.%3$sAmount Paid was <strong>%4$s (%5$s)</strong> while the total order amount is <strong>%6$s (%7$s)</strong>%8$s<strong>Paystack Transaction Reference:</strong> %9$s', 'woo-paystack' ), '<br />', '<br />', '<br />', $currency_symbol, $amount_paid, $currency_symbol, $order_total, '<br />', $paystack_ref );
			$order->add_order_note( $admin_order_note );

			function_exists( 'wc_reduce_stock_levels' ) ? wc_reduce_stock_levels( $order_id ) : $order->reduce_order_stock();

			wc_add_notice( $notice, $notice_type );

			WC()->cart->empty_cart();

		} else {

			if ( $payment_currency !== $order_currency ) {

				$order->update_status( 'on-hold', '' );

				$order->update_meta_data( '_transaction_id', $paystack_ref );

				/* translators: 1: line break, 2: line break, 3: line break. */
				$notice      = sprintf( __( 'Thank you for shopping with us.%1$sYour payment was successful, but the payment currency is different from the order currency.%2$sYour order is currently on-hold.%3$sKindly contact us for more information regarding your order and payment status.', 'woo-paystack' ), '<br />', '<br />', '<br />' );
				$notice_type = 'notice';

				// Add Customer Order Note.
				$order->add_order_note( $notice, 1 );

				// Add Admin Order Note.
				/* translators: 1: line break, 2: line break, 3: line break, 4: order currency, 5: order currency symbol, 6: payment currency, 7: payment currency symbol, 8: line break, 9: Paystack transaction reference. */
				$admin_order_note = sprintf( __( '<strong>Look into this order</strong>%1$sThis order is currently on hold.%2$sReason: Order currency is different from the payment currency.%3$sOrder Currency is <strong>%4$s (%5$s)</strong> while the payment currency is <strong>%6$s (%7$s)</strong>%8$s<strong>Paystack Transaction Reference:</strong> %9$s', 'woo-paystack' ), '<br />', '<br />', '<br />', $order_currency, $currency_symbol, $payment_currency, $gateway_symbol, '<br />', $paystack_ref );
				$order->add_order_note( $admin_order_note );

				function_exists( 'wc_reduce_stock_levels' ) ? wc_reduce_stock_levels( $order_id ) : $order->reduce_order_stock();

				wc_add_notice( $notice, $notice_type );

			} else {

				$order->payment_complete( $paystack_ref );

				/* translators: %s: Paystack transaction reference. */
				$order->add_order_note( sprintf( __( 'Payment via Paystack successful (Transaction Reference: %s)', 'woo-paystack' ), $paystack_ref ) );

				WC()->cart->empty_cart();

				if ( $this->is_autocomplete_order_enabled( $order ) ) {
					$order->update_status( 'completed' );
				}
			}
		}

		$order->save();

		$this->save_card_details( $paystack_response, $order->get_user_id(), $order_id );

		exit;
	}

	/**
	 * Save Customer Card Details.
	 *
	 * @param $paystack_response
	 * @param $user_id
	 * @param $order_id
	 */
	public function save_card_details( $paystack_response, $user_id, $order_id ) {

		$this->save_subscription_payment_token( $order_id, $paystack_response );

		$order = wc_get_order( $order_id );

		$save_card = $order->get_meta( '_wc_paystack_save_card' );

		if ( $user_id && $this->saved_cards && $save_card && $paystack_response->data->authorization->reusable && 'card' === $paystack_response->data->authorization->channel ) {

			$gateway_id = $order->get_payment_method();

			$last4          = $paystack_response->data->authorization->last4;
			$exp_year       = $paystack_response->data->authorization->exp_year;
			$brand          = $paystack_response->data->authorization->card_type;
			$exp_month      = $paystack_response->data->authorization->exp_month;
			$auth_code      = $paystack_response->data->authorization->authorization_code;
			$customer_email = $paystack_response->data->customer->email;

			$payment_token = "$auth_code###$customer_email";

			$token = new WC_Payment_Token_CC();
			$token->set_token( $payment_token );
			$token->set_gateway_id( $gateway_id );
			$token->set_card_type( strtolower( $brand ) );
			$token->set_last4( $last4 );
			$token->set_expiry_month( $exp_month );
			$token->set_expiry_year( $exp_year );
			$token->set_user_id( $user_id );
			$token->save();

			$order->delete_meta_data( '_wc_paystack_save_card' );
			$order->save();
		}
	}

	/**
	 * Save payment token to the order for automatic renewal for further subscription payment.
	 *
	 * @param $order_id
	 * @param $paystack_response
	 */
	public function save_subscription_payment_token( $order_id, $paystack_response ) {

		if ( ! function_exists( 'wcs_order_contains_subscription' ) ) {
			return;
		}

		if ( $this->order_contains_subscription( $order_id ) && $paystack_response->data->authorization->reusable && 'card' === $paystack_response->data->authorization->channel ) {

			$auth_code      = $paystack_response->data->authorization->authorization_code;
			$customer_email = $paystack_response->data->customer->email;

			$payment_token = "$auth_code###$customer_email";

			// Also store it on the subscriptions being purchased or paid for in the order
			if ( function_exists( 'wcs_order_contains_subscription' ) && wcs_order_contains_subscription( $order_id ) ) {

				$subscriptions = wcs_get_subscriptions_for_order( $order_id );

			} elseif ( function_exists( 'wcs_order_contains_renewal' ) && wcs_order_contains_renewal( $order_id ) ) {

				$subscriptions = wcs_get_subscriptions_for_renewal_order( $order_id );

			} else {

				$subscriptions = array();

			}

			if ( empty( $subscriptions ) ) {
				return;
			}

			foreach ( $subscriptions as $subscription ) {
				$subscription->update_meta_data( '_paystack_token', $payment_token );
				$subscription->save();
			}
		}

	}

	/**
	 * Get custom fields to pass to Paystack.
	 *
	 * @param int $order_id WC Order ID
	 *
	 * @return array
	 */
	public function get_custom_fields( $order_id ) {

		$order = wc_get_order( $order_id );

		$custom_fields = array();

		$custom_fields[] = array(
			'display_name'  => 'Plugin',
			'variable_name' => 'plugin',
			'value'         => 'woo-paystack',
		);

		if ( $this->custom_metadata ) {

			if ( $this->meta_order_id ) {

				$custom_fields[] = array(
					'display_name'  => 'Order ID',
					'variable_name' => 'order_id',
					'value'         => $order_id,
				);

			}

			if ( $this->meta_name ) {

				$custom_fields[] = array(
					'display_name'  => 'Customer Name',
					'variable_name' => 'customer_name',
					'value'         => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
				);

			}

			if ( $this->meta_email ) {

				$custom_fields[] = array(
					'display_name'  => 'Customer Email',
					'variable_name' => 'customer_email',
					'value'         => $order->get_billing_email(),
				);

			}

			if ( $this->meta_phone ) {

				$custom_fields[] = array(
					'display_name'  => 'Customer Phone',
					'variable_name' => 'customer_phone',
					'value'         => $order->get_billing_phone(),
				);

			}

			if ( $this->meta_products ) {

				$line_items = $order->get_items();

				$products = '';

				foreach ( $line_items as $item_id => $item ) {
					$name      = $item['name'];
					$quantity  = $item['qty'];
					$products .= $name . ' (Qty: ' . $quantity . ')';
					$products .= ' | ';
				}

				$products = rtrim( $products, ' | ' );

				$custom_fields[] = array(
					'display_name'  => 'Products',
					'variable_name' => 'products',
					'value'         => $products,
				);

			}

			if ( $this->meta_billing_address ) {

				$billing_address = $order->get_formatted_billing_address();
				$billing_address = esc_html( preg_replace( '#<br\s*/?>#i', ', ', $billing_address ) );

				$custom_fields[] = array(
					'display_name'  => 'Billing Address',
					'variable_name' => 'billing_address',
					'value'         => $billing_address,
				);

			}

			if ( $this->meta_shipping_address ) {

				$shipping_address = $order->get_formatted_shipping_address();
				$shipping_address = esc_html( preg_replace( '#<br\s*/?>#i', ', ', $shipping_address ) );

				if ( empty( $shipping_address ) ) {

					$billing_address = $order->get_formatted_billing_address();
					$billing_address = esc_html( preg_replace( '#<br\s*/?>#i', ', ', $billing_address ) );

					$shipping_address = $billing_address;

				}
				$custom_fields[] = array(
					'display_name'  => 'Shipping Address',
					'variable_name' => 'shipping_address',
					'value'         => $shipping_address,
				);

			}
		}

		return $custom_fields;
	}

	/**
	 * Process a refund request from the Order details screen.
	 *
	 * @param int $order_id WC Order ID.
	 * @param float|null $amount Refund Amount.
	 * @param string $reason Refund Reason
	 *
	 * @return bool|WP_Error
	 */
	public function process_refund( $order_id, $amount = null, $reason = '' ) {

		if ( ! ( $this->public_key && $this->secret_key ) ) {
			return false;
		}

		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return false;
		}

		$order_currency = $order->get_currency();
		$transaction_id = $order->get_transaction_id();

		$paystack_response = $this->get_paystack_transaction( $transaction_id );

		if ( false !== $paystack_response ) {

			if ( 'success' === $paystack_response->data->status ) {

				/* translators: 1: WooCommerce order ID, 2: Site URL. */
				$merchant_note = sprintf( __( 'Refund for Order ID: #%1$s on %2$s', 'woo-paystack' ), $order_id, get_site_url() );

				$body = array(
					'transaction'   => $transaction_id,
					'amount'        => $amount * 100,
					'currency'      => $order_currency,
					'customer_note' => $reason,
					'merchant_note' => $merchant_note,
				);

				$headers = array(
					'Authorization' => 'Bearer ' . $this->secret_key,
				);

				$args = array(
					'headers' => $headers,
					'timeout' => 60,
					'body'    => $body,
				);

				$refund_url = 'https://api.paystack.co/refund';

				$refund_request = wp_remote_post( $refund_url, $args );

				if ( ! is_wp_error( $refund_request ) && 200 === wp_remote_retrieve_response_code( $refund_request ) ) {

					$refund_response = json_decode( wp_remote_retrieve_body( $refund_request ) );

					if ( $refund_response->status ) {
						$amount    = wc_price( $amount, array( 'currency' => $order_currency ) );
						$refund_id = $refund_response->data->id;
						/* translators: 1: Refunded amount, 2: Paystack refund ID, 3: Refund reason. */
						$refund_message = sprintf( __( 'Refunded %1$s. Refund ID: %2$s. Reason: %3$s', 'woo-paystack' ), $amount, $refund_id, $reason );
						$order->add_order_note( $refund_message );

						return true;
					}
				} else {

					$refund_response = json_decode( wp_remote_retrieve_body( $refund_request ) );

					if ( isset( $refund_response->message ) ) {
						return new WP_Error( 'error', $refund_response->message );
					}

					return new WP_Error( 'error', __( 'Can&#39;t process refund at the moment. Try again later.', 'woo-paystack' ) );
				}
			}
		}

	}

	/**
	 * Checks if WC version is less than passed in version.
	 *
	 * @param string $version Version to check against.
	 *
	 * @return bool
	 */
	public function is_wc_lt( $version ) {
		return version_compare( WC_VERSION, $version, '<' );
	}

	/**
	 * Checks if autocomplete order is enabled for the payment method.
	 *
	 * @since 5.7
	 * @param WC_Order $order Order object.
	 * @return bool
	 */
	protected function is_autocomplete_order_enabled( $order ) {
		$autocomplete_order = false;

		$payment_method = $order->get_payment_method();

		$paystack_settings = get_option( 'woocommerce_' . $payment_method . '_settings' );

		if ( isset( $paystack_settings['autocomplete_order'] ) && 'yes' === $paystack_settings['autocomplete_order'] ) {
			$autocomplete_order = true;
		}

		return $autocomplete_order;
	}

	/**
	 * Retrieve the payment channels configured for the gateway
	 *
	 * @since 5.7
	 * @param WC_Order $order Order object.
	 * @return array
	 */
	protected function get_gateway_payment_channels( $order ) {
		$payment_channels = $this->payment_channels;
		if ( empty( $payment_channels ) && ( 'paystack' !== $order->get_payment_method() ) ) {
			$payment_channels = array( 'card' );
		}

		/**
		 * Filter the list of payment channels.
		 *
		 * @param array $payment_channels A list of payment channels.
		 * @param string $id Payment method ID.
		 * @param WC_Order $order Order object.
		 * @since 5.8.2
		 */
		return apply_filters( 'wc_paystack_payment_channels', $payment_channels, $this->id, $order );
	}

	/**
	 * Retrieve a transaction from Paystack.
	 *
	 * @since 5.7.5
	 * @param $paystack_txn_ref
	 * @return false|mixed
	 */
	private function get_paystack_transaction( $paystack_txn_ref ) {

		$paystack_url = 'https://api.paystack.co/transaction/verify/' . $paystack_txn_ref;

		$headers = array(
			'Authorization' => 'Bearer ' . $this->secret_key,
		);

		$args = array(
			'headers' => $headers,
			'timeout' => 60,
		);

		$request = wp_remote_get( $paystack_url, $args );

		if ( ! is_wp_error( $request ) && 200 === wp_remote_retrieve_response_code( $request ) ) {
			return json_decode( wp_remote_retrieve_body( $request ) );
		}

		return false;
	}

	/**
	 * Get Paystack payment icon URL.
	 */
	public function get_logo_url() {

		$base_location = wc_get_base_location();

		if ( 'GH' === $base_location['country'] ) {
			$url = WC_HTTPS::force_https_url( plugins_url( 'assets/images/paystack-gh.png', WC_PAYSTACK_MAIN_FILE ) );
		} elseif ( 'ZA' === $base_location['country'] ) {
			$url = WC_HTTPS::force_https_url( plugins_url( 'assets/images/paystack-za.png', WC_PAYSTACK_MAIN_FILE ) );
		} elseif ( 'KE' === $base_location['country'] ) {
			$url = WC_HTTPS::force_https_url( plugins_url( 'assets/images/paystack-ke.png', WC_PAYSTACK_MAIN_FILE ) );
		} elseif ( 'CI' === $base_location['country'] ) {
			$url = WC_HTTPS::force_https_url( plugins_url( 'assets/images/paystack-civ.png', WC_PAYSTACK_MAIN_FILE ) );
		} else {
			$url = WC_HTTPS::force_https_url( plugins_url( 'assets/images/paystack-wc.png', WC_PAYSTACK_MAIN_FILE ) );
		}

		return apply_filters( 'wc_paystack_gateway_icon_url', $url, $this->id );
	}

	/**
	 * Check if an order contains a subscription.
	 *
	 * @param int $order_id WC Order ID.
	 *
	 * @return bool
	 */
	public function order_contains_subscription( $order_id ) {

		return function_exists( 'wcs_order_contains_subscription' ) && ( wcs_order_contains_subscription( $order_id ) || wcs_order_contains_renewal( $order_id ) );

	}
}
