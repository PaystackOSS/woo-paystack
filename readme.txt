=== Paystack WooCommerce Payment Gateway ===
Contributors: paystack, tubiz
Tags: paystack, woocommerce, payment gateway, mastercard, visa
Requires at least: 6.8
Tested up to: 7.0
Stable tag: 5.8.4
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Paystack for WooCommerce allows your WooCommerce store to accept secure payments from multiple local and global payment channels.

== Description ==

Paystack makes it easy for businesses in Nigeria, Ghana, Kenya and South Africa to accept secure payments from multiple local and global payment channels. Integrate Paystack with your store today, and let your customers pay you with their choice of methods.

With Paystack for WooCommerce, you can accept payments via:

* Credit/Debit Cards — Visa, Mastercard, Verve (NG, GH, KE), American Express (SA only)
* Bank transfer (Nigeria)
* Mobile money (Ghana)
* Masterpass (South Africa)
* EFT (South Africa)
* USSD (Nigeria)
* Visa QR (Nigeria)
* Many more coming soon

= Why Paystack? =

* Start receiving payments instantly—go from sign-up to your first real transaction in as little as 15 minutes
* Simple, transparent pricing—no hidden charges or fees
* Modern, seamless payment experience via the Paystack Checkout — [Try the demo!](https://paystack.com/demo/checkout)
* Advanced fraud detection
* Understand your customers better through a simple and elegant dashboard
* Access to attentive, empathetic customer support 24/7
* Free updates as we launch new features and payment options
* Clearly documented APIs to build your custom payment experiences

Over 60,000 businesses of all sizes in Nigeria, Ghana, Kenya, and South Africa rely on Paystack's suite of products to receive payments and make payouts seamlessly. Sign up on [Paystack.com/signup](https://paystack.com/signup) to get started.


= Note =

This plugin is meant to be used by merchants in Ghana, Kenya, Nigeria and South Africa.

= Plugin Features =

*   __Accept payment__ via Mastercard, Visa, Verve, USSD, Mobile Money, Bank Transfer, EFT, Bank Accounts, GTB 737 & Visa QR.
*   __Seamless integration__ into the WooCommerce checkout page. Accept payment directly on your site
*   __Refunds__ from the WooCommerce order details page. Refund an order directly from the order details page
*   __Recurring payment__ using [WooCommerce Subscriptions](https://woocommerce.com/products/woocommerce-subscriptions/) plugin

= WooCommerce Subscriptions Integration =

*	The [WooCommerce Subscriptions](https://woocommerce.com/products/woocommerce-subscriptions/) integration only works with __WooCommerce v2.6 and above__ and __WooCommerce Subscriptions v2.0 and above__.

*	No subscription plans is created on Paystack. The [WooCommerce Subscriptions](https://woocommerce.com/products/woocommerce-subscriptions/) plugin handles all the subscription functionality.

*	If a customer pays for a subscription using a Mastercard or Visa card, their subscription will renew automatically throughout the duration of the subscription. If an automatic renewal fail their subscription will be put on-hold and they will have to login to their account to renew the subscription.

*	For customers paying with a Verve card, their subscription can't be renewed automatically, once a payment is due their subscription will be on-hold. The customer will have to login to his account to manually renew his subscription.

*	If a subscription has a free trial and no signup-fee, automatic renewal is not possible for the first payment because the initial order total will be 0, after the free trial the subscription will be put on-hold. The customer will have to login to his account to renew his subscription. If a Mastercard or Visa card is used to renew the subscription subsequent renewals will be automatic throughout the duration of the subscription, if a Verve card is used automatic renewal isn't possible.

== Installation ==

*   Go to __WordPress Admin__ > __Plugins__ > __Add New__ from the left-hand menu
*   In the search box type __Paystack WooCommerce Payment Gateway__
*   Click on Install now when you see __Paystack WooCommerce Payment Gateway__ to install the plugin
*   After installation, __activate__ the plugin.


= Paystack Setup and Configuration =
*   Go to __WooCommerce > Settings__ and click on the __Payments__ tab
*   You'll see Paystack listed along with your other payment methods. Click __Set Up__
*   On the next screen, configure the plugin. There is a selection of options on the screen. Read what each one does below.

1. __Enable/Disable__ - Check this checkbox to Enable Paystack on your store's checkout
2. __Title__ - This will represent Paystack on your list of Payment options during checkout. It guides users to know which option to select to pay with Paystack. __Title__ is set to "Debit/Credit Cards" by default, but you can change it to suit your needs.
3. __Description__ - This controls the message that appears under the payment fields on the checkout page. Use this space to give more details to customers about what Paystack is and what payment methods they can use with it.
4. __Test Mode__ - Check this to enable test mode. When selected, the fields in step six will say "Test" instead of "Live." Test mode enables you to test payments before going live. The orders process with test payment methods, no money is involved so there is no risk. You can uncheck this when your store is ready to accept real payments.
5. __Payment Option__ - Select how Paystack Checkout displays to your customers. A popup displays Paystack Checkout on the same page, while Redirect will redirect your customer to make payment.
6. __API Keys__ - The next two text boxes are for your Paystack API keys, which you can get from your Paystack Dashboard. If you enabled Test Mode in step four, then you'll need to use your test API keys here. Otherwise, you can enter your live keys.
7. __Additional Settings__ - While not necessary for the plugin to function, there are some extra configuration options you have here. You can do things like add custom metadata to your transactions (the data will show up on your Paystack dashboard) or use Paystack's [Split Payment feature](https://paystack.com/docs/payments/split-payments). The tooltips next to the options provide more information on what they do.
8. Click on __Save Changes__ to update the settings.

To account for poor network connections, which can sometimes affect order status updates after a transaction, we __strongly__ recommend that you set a Webhook URL on your Paystack dashboard. This way, whenever a transaction is complete on your store, we'll send a notification to the Webhook URL, which will update the order and mark it as paid. You can set this up by using the URL in red at the top of the Settings page. Just copy the URL and save it as your webhook URL on your Paystack dashboard under __Settings > API Keys & Webhooks__ tab.

If you do not find Paystack on the Payment method options, please go through the settings again and ensure that:

*   You've checked the __"Enable/Disable"__ checkbox
*   You've entered your __API Keys__ in the appropriate field
*   You've clicked on __Save Changes__ during setup

== Frequently Asked Questions ==

= What Do I Need To Use The Plugin =

*   A Paystack merchant account—use an existing account or [create an account here](https://paystack.com/signup)
*   An active [WooCommerce installation](https://docs.woocommerce.com/document/installing-uninstalling-woocommerce/)
*   A valid [SSL Certificate](https://docs.woocommerce.com/document/ssl-and-https/)

= WooCommerce Subscriptions Integration =

*	The [WooCommerce Subscriptions](https://woocommerce.com/products/woocommerce-subscriptions/) integration only works with WooCommerce v2.6 and above and WooCommerce Subscriptions v2.0 and above.

*	No subscription plans is created on Paystack. The [WooCommerce Subscriptions](https://woocommerce.com/products/woocommerce-subscriptions/) handles all the subscription functionality.

*	If a customer pays for a subscription using a MasterCard or Visa card, their subscription will renew automatically throughout the duration of the subscription. If an automatic renewal fail their subscription will be put on-hold and they will have to login to their account to renew the subscription.

*	For customers paying with a Verve card, their subscription can't be renewed automatically, once a payment is due their subscription will be on-hold. The customer will have to login to his account to manually renew his subscription.

*	If a subscription has a free trial and no signup-fee, automatic renewal is not possible because the order total will be 0, after the free trial the subscription will be put on-hold. The customer will have to login to his account to renew his subscription. If a MasterCard or Visa card is used to renew subsequent renewals will be automatic throughout the duration of the subscription, if a Verve card is used automatic renewal isn't possible.


== Changelog ==

= 5.8.4 - June 16, 2026 =
*   New: Refund support for custom gateways
*   Fix: Payment amount verification now correctly handles decimal values
*   Tweak: WooCommerce 10.8 compatibility
*   Dev: Resolve all issues reported by Plugin Check (PCP)

= 5.8.3 - April 30, 2026 =
*   Misc: Pass site url in the referrer metadata
*   Tweak: WooCommerce 10.7 compatibility
*   Tweak: Minimum WooCommerce supported version: 9.6

= 5.8.2 - July 18, 2024 =
*   New: Introduce filter hook `wc_paystack_payment_channels`
*   New: Add WooCommerce plugin dependency
*   Misc: Add payment icon for Côte d'Ivoire (Ivory Coast)
*   Fix: Payment with saved card not working if payment method option is set to redirect
*   Tweak: Minimum WooCommerce supported version: 8.0
*   Tweak: WooCommerce 9.1 compatibility

= 5.8.1 - November 28, 2023 =
*   New: Add support for WooCommerce checkout block to custom gateways
*   New: Add support for RWF as an accepted currency
*   Tweak: WooCommerce 8.3 compatibility
*   Improve: Ensure order amount is in integer when initializing payment on Paystack

= 5.8.0 - October 3, 2023 =
*   New: Add support for WooCommerce checkout block
*   Tweak: WooCommerce 8.1 compatibility
*   Tweak: Pass order currency when making payment using saved cards
*   Update: Load Paystack InlineJS (Popup) V2 in the custom payment gateways

= 5.7.6 - June 20, 2023 =
*   New: Minimum WooCommerce supported version: 7.0
*   Fix: Unable to process refund on the view order screen
*   Improve: Paystack test mode notice will now be displayed in the WooCommerce Admin Notes Inbox

= 5.7.5 - May 22, 2023 =
*   Update: Add support for EGP as an accepted currency
*   Update: Update icon for Kenyan payment methods
*   Update: Migrate to Paystack InlineJS (Popup) V2
*   Tweak: Minimum PHP version: 7.4
*   Tweak: Declare compatibility for High Performance Order Storage (HPOS)
*   Tweak: WooCommerce 7.7 compatibility
*   Improve: Improvement to payment token. The customer's email address is now saved with the authorization code.
*   Improve: Improvement to webhook notifications and order processing

= 5.7.4 - October 4, 2022 =
*   New: Add support for XOF as an accepted currency.
*   New: Minimum PHP version: 7.2
*   New: Minimum WooCommerce supported version: 6.1
*   Misc: WooCommerce 6.9 compatibility
*   Fix: Paystack payment modal displaying on the "Pay for Order" page
*   Improve: Change secret key input field type to a password field.

= 5.7.3 - October 26, 2021 =
*   New: Add support for KES as an accepted currency.
*   Tweak: WooCommerce 5.8 compatibility

= 5.7.2 - March 12, 2021 =
* New: Auto display Paystack payment popup on the payment page
* Removed: Remove Paystack metrics tracker
* Tweak: WooCommerce 5.1 compatibility

= 5.7.1 - March 1, 2021 =
* Removed: Remove inline embed payment option
* Updated: Update payment method icon for South Africa

= 5.7 - January 13, 2021 =
* New: Add additional payment channels to the custom gateways
* New: Add redirect payment option
* New: Add option to autocomplete order after successful payment
* Misc: Add deprecate notice for Inline Embed payment option
* Fix: The default gateway should display only the payment channel(s) set on the Paystack settings page
* Tweak: WooCommerce 4.9 compatibility.

= 5.6.4 - September 29, 2020 =
*   Fix: Use order currency when paying for an order and not the store currency
*   Misc: Test mode enabled admin notice not displayed properly
*   Misc: Add payment icon for South Africa

= 5.6.3 - July 27, 2020 =
*   New: Add support for ZAR as an accepted currency.
*   New: Add setting to remove "Cancel order & restore cart" button.
*   New: Minimum PHP version: 5.6
*   New: Minimum WooCommerce supported version: 3.0.0
*   Misc: Add icon for Ghanaian payment methods to checkout.
*   Misc: Remove GBP as an accepted currency.
*   Fix: Cart not fully cleared after successful payment.
*   Fix: Selected payment icons not displayed on custom gateways settings page.
*   Tweak: WooCommerce 4.3 compatibility.

= 5.6.2 - March 12, 2020 =
*   Update: WooCommerce 4.0 compatibility.

= 5.6.1 - November 13, 2019 =
*   Update: WooCommerce 3.8 compatibility.

= 5.6.0 - August 7, 2019 =
*   New: Support for refunds via Paystack from the order details screen.
*   New: Log successful transaction to Paystack metrics tracker.
*   New: Add support for sending additional order details to Paystack when making payment using a saved card.
*   New: Add support for sending additional order details to Paystack when a subscription payment is renewed.
*   Update: WC 3.7 compatibility.

= 5.5.0 - May 27, 2019 =
*   Misc: Renamed Diamond Bank to Access Bank (Diamond)
*   Tweak: Significant cleanup of code formatting and adherence of WordPress coding standards
*   New: Support for translation

= 5.4.2 - February 13, 2019 =
*   Misc: Remove Paystack fee and Paystack payout amount on the order details page

= 5.4.1 - February 1, 2019 =
*   Fix: Split payment not working properly when the split payment transaction charge setting field is empty

= 5.4.0 - December 9, 2018 =
*   New: Add support for Paystack split payments
*   New: Display Paystack fee and Paystack payout amount on the order details page
*   Misc: Add support for WooCommerce 3.5
*   Misc: Renamed Skye Bank Plc to Polaris Bank Limited
*   Misc: Add new banks (ALAT by WEMA, ASO Savings and Loans, MainStreet Bank & Ekondo Microfinance Bank) to Allowed Banks Card list
*   Misc: Add new banks logos (ALAT by WEMA, ASO Savings and Loans, MainStreet Bank & Ekondo Microfinance Bank) to Payment Icons list

= 5.3.1 - July 26, 2018 =
*	Fix: The bank payment channel not showing in the default gateway

= 5.3.0 - June 2, 2018 =
*	Fix: Saved cards feature not working in the custom gateways
*	Fix: Custom gateways not processing automatic renewal payments via WooCommerce Subscriptions plugin

= 5.2.1 - June 1, 2018 =
*	Misc: Add support for WooCommerce 3.4

= 5.2.0 - May 18, 2018 =
*	New: Add support for multiple subscriptions purchase using WooCommerce Subscriptions plugin
*	Fix: Deprecated functions in the Tbz_WC_Gateway_Paystack_Subscription class

= 5.1.0 - March 27, 2018 =
*	New: Add support for GHS (Ghanaian cedi) currency
* 	Fix: Deprecated WooCommerce 2.X functions

= 5.0.2 - September 15, 2017 =
*	Fix: Illegal string offset warnings when plugin is newly installed

= 5.0.1 - September 14, 2017 =
*	Fix: Fatal error on the checkout page if WooCommerce 2.6.14 and below is installed

= 5.0.0 - August 29, 2017 =
*	New: Add support for Paystack custom filters
*	New: Create additional Paystack gateways (max of 5) using different custom filters. You can create a gateway that accepts only Verve cards, a gateway that accepts only bank account payments, a gateway that accepts only GTB issued Mastercard.

= 4.1.0 - July 7, 2017 =
*	Fix: Deprecated WooCommerce 2.X functions

= 4.0.1 - April 10, 2017 =
* 	Fix: Fatal error if WooCommerce 2.6.14 and below is installed

= 4.0.0 - April 10, 2017 =
* 	New: Add support for Paystack Inline Embed.
*  	New: Add support for sending additional order details to Paystack

= 3.1.1 - February 13, 2017 =
* 	New: Changed Paystack payment methods icon.

= 3.1.0 - January 10, 2017 =
* 	New: Add support for USD and GBP currency. Note this has to be enabled by Paystack for your account before it can be used on your site.

= 3.0.0 - November 11, 2016 =
* 	New: Add support for recurring payment using [WooCommerce Subscriptions](https://woocommerce.com/products/woocommerce-subscriptions/) plugin.

= 2.1.0 - October 15, 2016 =
*	New: Add support for confirming payment using the webhook url

= 2.0.1 - July 5, 2016 =
*	Fix: Paystack payment option and settings not available if Paystack WooCommerce Payment Gateway version 2.0.0 is installed and WooCommerce version 2.5.5 and below is installed

= 2.0.0 - June 28, 2016 =
* 	New: Saved cards - allow store customers to save their card details and pay again using the same card. Card details are saved on Paystack servers and not on your store.
*	Fix: Change payment icon

= 1.1.0 - April 22, 2016 =
*   Fix: Fatal error if the WooCommerce plugin is deactivated while the Paystack plugin is active

= 1.0.0 - February 3, 2016 =
*   First release



== Screenshots ==

1. Paystack displayed as a payment method on the WooCommerce payment methods page

2. Paystack WooCommerce payment gateway settings page

3. Paystack on WooCommerce Checkout