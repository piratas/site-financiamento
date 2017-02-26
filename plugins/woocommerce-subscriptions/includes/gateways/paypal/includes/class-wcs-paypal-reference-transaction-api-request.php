<?php
/**
 * PayPal Reference Transaction API Request Class
 *
 * Generates request data to send to the PayPal Express Checkout API for Reference Transaction related API calls
 *
 * Heavily inspired by the WC_Paypal_Express_API_Request class developed by the masterful SkyVerge team
 *
 * @package		WooCommerce Subscriptions
 * @subpackage	Gateways/PayPal
 * @category	Class
 * @since		2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WCS_PayPal_Reference_Transaction_API_Request {

	/** auth/capture transaction type */
	const AUTH_CAPTURE = 'Sale';

	/** @var array the request parameters */
	private $parameters = array();

	/**
	 * Construct an PayPal Express request object
	 *
	 * @param string $api_username the API username
	 * @param string $api_password the API password
	 * @param string $api_signature the API signature
	 * @param string $api_version the API version
	 * @since 2.0
	 */
	public function __construct( $api_username, $api_password, $api_signature, $api_version ) {

		$this->add_parameters( array(
			'USER'      => $api_username,
			'PWD'       => $api_password,
			'SIGNATURE' => $api_signature,
			'VERSION'   => $api_version,
		) );
	}

	/**
	 * Sets up the express checkout transaction
	 *
	 * @link https://developer.paypal.com/docs/classic/express-checkout/integration-guide/ECGettingStarted/#id084RN060BPF
	 * @link https://developer.paypal.com/webapps/developer/docs/classic/api/merchant/SetExpressCheckout_API_Operation_NVP/
	 *
	 * @param array $args {
	 *     @type string 'currency'              (Optional) A 3-character currency code (default is store's currency).
	 *     @type string 'billing_type'          (Optional) Type of billing agreement for reference transactions. You must have permission from PayPal to use this field. This field must be set to one of the following values: MerchantInitiatedBilling - PayPal creates a billing agreement for each transaction associated with buyer. You must specify version 54.0 or higher to use this option; MerchantInitiatedBillingSingleAgreement - PayPal creates a single billing agreement for all transactions associated with buyer. Use this value unless you need per-transaction billing agreements. You must specify version 58.0 or higher to use this option.
	 *     @type string 'billing_description'   (Optional) Description of goods or services associated with the billing agreement. This field is required for each recurring payment billing agreement if using MerchantInitiatedBilling as the billing type, that means you can use a different agreement for each subscription/order. PayPal recommends that the description contain a brief summary of the billing agreement terms and conditions (but this only makes sense when the billing type is MerchantInitiatedBilling, otherwise the terms will be incorrectly displayed for all agreements). For example, buyer is billed at "9.99 per month for 2 years".
	 *     @type string 'maximum_amount'        (Optional) The expected maximum total amount of the complete order and future payments, including shipping cost and tax charges. If you pass the expected average transaction amount (default 25.00). PayPal uses this value to validate the buyer's funding source.
	 *     @type string 'no_shipping'           (Optional) Determines where or not PayPal displays shipping address fields on the PayPal pages. For digital goods, this field is required, and you must set it to 1. It is one of the following values: 0 – PayPal displays the shipping address on the PayPal pages; 1 – PayPal does not display shipping address fields whatsoever (default); 2 – If you do not pass the shipping address, PayPal obtains it from the buyer's account profile.
	 *     @type string 'page_style'            (Optional) Name of the Custom Payment Page Style for payment pages associated with this button or link. It corresponds to the HTML variable page_style for customizing payment pages. It is the same name as the Page Style Name you chose to add or edit the page style in your PayPal Account profile.
	 *     @type string 'brand_name'            (Optional) A label that overrides the business name in the PayPal account on the PayPal hosted checkout pages. Default: store name.
	 *     @type string 'landing_page'          (Optional) Type of PayPal page to display. It is one of the following values: 'login' – PayPal account login (default); 'Billing' – Non-PayPal account.
	 *     @type string 'payment_action'        (Optional) How you want to obtain payment. If the transaction does not include a one-time purchase, this field is ignored. Default 'Sale' – This is a final sale for which you are requesting payment (default). Alternative: 'Authorization' – This payment is a basic authorization subject to settlement with PayPal Authorization and Capture. You cannot set this field to Sale in SetExpressCheckout request and then change the value to Authorization or Order in the DoExpressCheckoutPayment request. If you set the field to Authorization or Order in SetExpressCheckout, you may set the field to Sale.
	 *     @type string 'return_url'            (Required) URL to which the buyer's browser is returned after choosing to pay with PayPal.
	 *     @type string 'cancel_url'            (Required) URL to which the buyer is returned if the buyer does not approve the use of PayPal to pay you.
	 *     @type string 'custom'                (Optional) A free-form field for up to 256 single-byte alphanumeric characters
	 * }
	 * @since 2.0
	 */
	public function set_express_checkout( $args ) {

		$default_description = sprintf( _x( 'Orders with %s', 'data sent to paypal', 'woocommerce-subscriptions' ), get_bloginfo( 'name' ) );

		$defaults = array(
			'currency'            => get_woocommerce_currency(),
			'billing_type'        => apply_filters( 'woocommerce_subscriptions_paypal_billing_agreement_type', 'MerchantInitiatedBillingSingleAgreement', $args ),
			// translators: placeholder is for blog name
			'billing_description' => html_entity_decode( apply_filters( 'woocommerce_subscriptions_paypal_billing_agreement_description', $default_description, $args ), ENT_NOQUOTES, 'UTF-8' ),
			'maximum_amount'      => null,
			'no_shipping'         => 1,
			'page_style'          => null,
			'brand_name'          => html_entity_decode( get_bloginfo( 'name' ), ENT_NOQUOTES, 'UTF-8' ),
			'landing_page'        => 'login',
			'payment_action'      => 'Sale',
			'custom'              => '',
		);

		$args = wp_parse_args( $args, $defaults );

		$this->set_method( 'SetExpressCheckout' );

		$this->add_parameters( array(

			'PAYMENTREQUEST_0_AMT'           => 0, // a zero amount is use so that no DoExpressCheckout action is required and instead CreateBillingAgreement is used to first create a billing agreement not attached to any order and then DoReferenceTransaction is used to charge both the initial order and renewal order amounts
			'PAYMENTREQUEST_0_ITEMAMT'       => 0,
			'PAYMENTREQUEST_0_SHIPPINGAMT'   => 0,
			'PAYMENTREQUEST_0_TAXAMT'        => 0,
			'PAYMENTREQUEST_0_CURRENCYCODE'  => $args['currency'],
			'PAYMENTREQUEST_0_CUSTOM'        => $args['custom'],

			'L_BILLINGTYPE0'                 => $args['billing_type'],
			'L_BILLINGAGREEMENTDESCRIPTION0' => wcs_get_paypal_item_name( $args['billing_description'] ),
			'L_BILLINGAGREEMENTCUSTOM0'      => $args['custom'],

			'RETURNURL'                      => $args['return_url'],
			'CANCELURL'                      => $args['cancel_url'],
			'PAGESTYLE'                      => $args['page_style'],
			'BRANDNAME'                      => $args['brand_name'],
			'LANDINGPAGE'                    => ( 'login' == $args['landing_page'] ) ? 'Login' : 'Billing',
			'NOSHIPPING'                     => $args['no_shipping'],

			'MAXAMT'                         => $args['maximum_amount'],
			'PAYMENTREQUEST_0_PAYMENTACTION' => $args['payment_action'],
		) );
	}

	/**
	 * Get info about the buyer & transaction from PayPal
	 *
	 * @link https://developer.paypal.com/docs/classic/express-checkout/integration-guide/ECGettingStarted/#id084RN060BPF
	 * @link https://developer.paypal.com/webapps/developer/docs/classic/api/merchant/GetExpressCheckoutDetails_API_Operation_NVP/
	 *
	 * @param string $token token from SetExpressCheckout response
	 * @since 2.0
	 */
	public function get_express_checkout_details( $token ) {

		$this->set_method( 'GetExpressCheckoutDetails' );
		$this->add_parameter( 'TOKEN', $token );
	}

	/**
	 * Create a billing agreement, required when a subscription sign-up has no initial payment
	 *
	 * @link https://developer.paypal.com/docs/classic/express-checkout/integration-guide/ECReferenceTxns/#id094TB0Y0J5Z__id094TB4003HS
	 * @link https://developer.paypal.com/docs/classic/api/merchant/CreateBillingAgreement_API_Operation_NVP/
	 *
	 * @param string $token token from SetExpressCheckout response
	 * @since 2.0
	 */
	public function create_billing_agreement( $token ) {

		$this->set_method( 'CreateBillingAgreement' );
		$this->add_parameter( 'TOKEN', $token );
	}

	/**
	 * Charge a payment against a reference token
	 *
	 * @link https://developer.paypal.com/docs/classic/express-checkout/integration-guide/ECReferenceTxns/#id094UM0DA0HS
	 * @link https://developer.paypal.com/docs/classic/api/merchant/DoReferenceTransaction_API_Operation_NVP/
	 *
	 * @param string $reference_id the ID of a refrence object, e.g. billing agreement ID.
	 * @param WC_Order $order order object
	 * @param array $args {
	 *     @type string 'payment_type'         (Optional) Specifies type of PayPal payment you require for the billing agreement. It is one of the following values. 'Any' or 'InstantOnly'. Echeck is not supported for DoReferenceTransaction requests.
	 *     @type string 'payment_action'       How you want to obtain payment. It is one of the following values: 'Authorization' - this payment is a basic authorization subject to settlement with PayPal Authorization and Capture; or 'Sale' - This is a final sale for which you are requesting payment.
	 *     @type string 'return_fraud_filters' (Optional) Flag to indicate whether you want the results returned by Fraud Management Filters. By default, you do not receive this information.
	 * }
	 * @since 2.0
	 */
	public function do_reference_transaction( $reference_id, $order, $args = array() ) {

		$defaults = array(
			'amount'               => $order->get_total(),
			'payment_type'         => 'Any',
			'payment_action'       => 'Sale',
			'return_fraud_filters' => 1,
			'notify_url'           => WC()->api_request_url( 'WC_Gateway_Paypal' ),
			'invoice_number'       => wcs_str_to_ascii( ltrim( $order->get_order_number(), _x( '#', 'hash before the order number', 'woocommerce-subscriptions' ) ) ),
			'custom'               => json_encode( array( 'order_id' => $order->id, 'order_key' => $order->order_key ) ),
		);

		$args = wp_parse_args( $args, $defaults );

		$this->set_method( 'DoReferenceTransaction' );

		// set base params
		$this->add_parameters( array(
			'REFERENCEID'      => $reference_id,
			'BUTTONSOURCE'     => 'WooThemes_Cart',
			'RETURNFMFDETAILS' => $args['return_fraud_filters'],
			'AMT'              => $order->get_total(),
			'CURRENCYCODE'     => $order->get_order_currency(),
			'INVNUM'           => $args['invoice_number'],
			'PAYMENTACTION'    => $args['payment_action'],
			'NOTIFYURL'        => $args['notify_url'],
			'CUSTOM'           => $args['custom'],
		) );

		$order_subtotal = $i = 0;
		$order_items    = array();

		// add line items
		foreach ( $order->get_items() as $item ) {

			$order_items[] = array(
				'NAME'    => wcs_get_paypal_item_name( $item['name'] ),
				'AMT'     => $order->get_item_subtotal( $item ),
				'QTY'     => ( ! empty( $item['qty'] ) ) ? absint( $item['qty'] ) : 1,
			);

			$order_subtotal += $item['line_total'];
		}

		// add fees
		foreach ( $order->get_fees() as $fee ) {

			$order_items[] = array(
				'NAME' => wcs_get_paypal_item_name( $fee['name'] ),
				'AMT'  => $fee['line_total'],
				'QTY'  => 1,
			);

			$order_subtotal += $fee['line_total'];
		}

		// WC 2.3+, no after-tax discounts
		if ( $order->get_total_discount() > 0 ) {

			$order_items[] = array(
				'NAME' => __( 'Total Discount', 'woocommerce-subscriptions' ),
				'QTY'  => 1,
				'AMT'  => - $order->get_total_discount(),
			);
		}

		if ( $order->prices_include_tax ) {

			$item_names = array();

			foreach ( $order_items as $item ) {
				$item_names[] = sprintf( '%s x %s', $item['NAME'], $item['QTY'] );
			}

			// add a single item for the entire order
			$this->add_line_item_parameters( array(
				'NAME' => sprintf( __( '%s - Order', 'woocommerce-subscriptions' ), get_option( 'blogname' ) ),
				'DESC' => wcs_get_paypal_item_name( implode( ', ', $item_names ) ),
				'AMT'  => $order_subtotal + $order->get_cart_tax(),
				'QTY'  => 1,
			), 0 );

			// add order-level parameters - do not send the TAXAMT due to rounding errors
			$this->add_payment_parameters( array(
				'ITEMAMT'     => $order_subtotal + $order->get_cart_tax(),
				'SHIPPINGAMT' => $order->get_total_shipping() + $order->get_shipping_tax(),
			) );

		} else {

			// add individual order items
			foreach ( $order_items as $item ) {
				$this->add_line_item_parameters( $item, $i++ );
			}

			// add order-level parameters
			$this->add_payment_parameters( array(
				'ITEMAMT'     => $order_subtotal,
				'SHIPPINGAMT' => $order->get_total_shipping(),
				'TAXAMT'      => $order->get_total_tax(),
			) );
		}
	}

	/**
	 * Performs an Express Checkout NVP API operation as passed in $api_method.
	 *
	 * Although the PayPal Standard API provides no facility for cancelling a subscription, the PayPal
	 * Express Checkout NVP API can be used.
	 *
	 * @since 2.0
	 */
	public function manage_recurring_payments_profile_status( $profile_id, $new_status, $order = null ) {

		$this->set_method( 'ManageRecurringPaymentsProfileStatus' );

		// We need to get merge the existing params to ensure the method and API credentials are passed to the filter for backward compatibility
		$this->add_parameters( apply_filters( 'woocommerce_subscriptions_paypal_change_status_data', array_merge( $this->get_parameters(), array(
			'PROFILEID' => $profile_id,
			'ACTION'    => $new_status,
			// translators: 1$: new status (e.g. "Cancel"), 2$: blog name
			'NOTE'      => html_entity_decode( sprintf( _x( '%1$s subscription event triggered at %2$s', 'data sent to paypal', 'woocommerce-subscriptions' ), $new_status, get_bloginfo( 'name' ) ), ENT_NOQUOTES, 'UTF-8' ),
		) ), $new_status, $order, $profile_id ) );
	}

	/** Helper Methods ******************************************************/

	/**
	 * Add a parameter
	 *
	 * @param string $key
	 * @param string|int $value
	 * @since 2.0
	 */
	private function add_parameter( $key, $value ) {
		$this->parameters[ $key ] = $value;
	}

	/**
	 * Add multiple parameters
	 *
	 * @param array $params
	 * @since 2.0
	 */
	private function add_parameters( array $params ) {
		foreach ( $params as $key => $value ) {
			$this->add_parameter( $key, $value );
		}
	}

	/**
	 * Set the method for the request, currently using:
	 *
	 * + `SetExpressCheckout` - setup transaction
	 * + `GetExpressCheckout` - gets buyers info from PayPal
	 * + `DoExpressCheckoutPayment` - completes the transaction
	 * + `DoCapture` - captures a previously authorized transaction
	 *
	 * @param string $method
	 * @since 2.0
	 */
	private function set_method( $method ) {
		$this->add_parameter( 'METHOD', $method );
	}

	/**
	 * Add payment parameters, auto-prefixes the parameter key with `PAYMENTREQUEST_0_`
	 * for convenience and readability
	 *
	 * @param array $params
	 * @since 2.0
	 */
	private function add_payment_parameters( array $params ) {
		foreach ( $params as $key => $value ) {
			$this->add_parameter( "PAYMENTREQUEST_0_{$key}", $value );
		}
	}

	/**
	 * Adds a line item parameters to the request, auto-prefixes the parameter key
	 * with `L_PAYMENTREQUEST_0_` for convenience and readability
	 *
	 * @param array $params
	 * @param int $item_count current item count
	 * @since 2.0
	 */
	private function add_line_item_parameters( array $params, $item_count ) {
		foreach ( $params as $key => $value ) {
			$this->add_parameter( "L_PAYMENTREQUEST_0_{$key}{$item_count}", $value );
		}
	}

	/**
	 * Helper method to return the item description, which is composed of item
	 * meta flattened into a comma-separated string, if available. Otherwise the
	 * product SKU is included.
	 *
	 * The description is automatically truncated to the 127 char limit.
	 *
	 * @param array $item cart or order item
	 * @param \WC_Product $product product data
	 * @return string
	 * @since 2.0
	 */
	private function get_item_description( $item, $product ) {

		if ( empty( $item['item_meta'] ) ) {

			// cart item
			$item_desc = WC()->cart->get_item_data( $item, true );

			$item_desc = str_replace( "\n", ', ', rtrim( $item_desc ) );

		} else {

			// order item
			$item_meta = new WC_Order_Item_Meta( $item );

			$item_meta = $item_meta->get_formatted();

			if ( ! empty( $item_meta ) ) {

				$item_desc = array();

				foreach ( $item_meta as $meta ) {
					$item_desc[] = sprintf( '%s: %s', $meta['label'], $meta['value'] );
				}

				$item_desc = implode( ', ', $item_desc );

			} else {

				$item_desc = is_callable( array( $product, 'get_sku' ) ) && $product->get_sku() ? sprintf( __( 'SKU: %s', 'woocommerce-subscriptions' ), $product->get_sku() ) : null;
			}
		}

		return wcs_get_paypal_item_name( $item_desc );
	}

	/**
	 * Returns the string representation of this request
	 *
	 * @see SV_WC_Payment_Gateway_API_Request::to_string()
	 * @return string the request query string
	 * @since 2.0
	 */
	public function to_string() {
		return http_build_query( $this->get_parameters() );
	}

	/**
	 * Returns the string representation of this request with any and all
	 * sensitive elements masked or removed
	 *
	 * @see SV_WC_Payment_Gateway_API_Request::to_string_safe()
	 * @return string the pretty-printed request array string representation, safe for logging
	 * @since 2.0
	 */
	public function to_string_safe() {

		$request = $this->get_parameters();

		$sensitive_fields = array( 'USER', 'PWD', 'SIGNATURE' );

		foreach ( $sensitive_fields as $field ) {

			if ( isset( $request[ $field ] ) ) {

				$request[ $field ] = str_repeat( '*', strlen( $request[ $field ] ) );
			}
		}

		return print_r( $request, true );
	}

	/**
	 * Returns the request parameters after validation & filtering
	 *
	 * @throws \SV_WC_Payment_Gateway_Exception invalid amount
	 * @return array request parameters
	 * @since 2.0
	 */
	public function get_parameters() {

		/**
		 * Filter PPE request parameters.
		 *
		 * Use this to modify the PayPal request parameters prior to validation
		 *
			 * @param array $parameters
		 * @param \WC_PayPal_Express_API_Request $this instance
		 */
		$this->parameters = apply_filters( 'wcs_paypal_request_params', $this->parameters, $this );

		// validate parameters
		foreach ( $this->parameters as $key => $value ) {

			// remove unused params
			if ( '' === $value || is_null( $value ) ) {
				unset( $this->parameters[ $key ] );
			}

			// format and check amounts
			if ( false !== strpos( $key, 'AMT' ) ) {

				// amounts must be 10,000.00 or less for USD
				if ( isset( $this->parameters['PAYMENTREQUEST_0_CURRENCYCODE'] ) && 'USD' == $this->parameters['PAYMENTREQUEST_0_CURRENCYCODE'] && $value > 10000 ) {

					throw new SV_WC_Payment_Gateway_Exception( sprintf( '%s amount of %s must be less than $10,000.00', $key, $value ) );
				}

				// PayPal requires locale-specific number formats (e.g. USD is 123.45)
				// PayPal requires the decimal separator to be a period (.)
				$this->parameters[ $key ] = number_format( $value, 2, '.', '' );
			}
		}

		return $this->parameters;
	}

	/**
	 * Returns the method for this request. PPE uses the API default request
	 * method (POST)
	 *
	 * @return null
	 * @since 2.0
	 */
	public function get_method() { }

	/**
	 * Returns the request path for this request. PPE request paths do not
	 * vary per request
	 *
	 * @return string
	 * @since 2.0
	 */
	public function get_path() {
		return '';
	}
}
