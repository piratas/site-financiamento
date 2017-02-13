<?php
/**
 * Functions related to extension cross-compatibility.
 *
 * @class    WC_Name_Your_Price_Compatibility
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ){
	exit; 	
}

class WC_Name_Your_Price_Compatibility {

	function __construct() {

		// fix for variable billing period subs string to array notice
		add_filter( 'woocommerce_subscriptions_product_period', array( $this, 'product_period' ), 10, 2 );

	}

	/*-----------------------------------------------------------------------------------*/
	/* Subscriptions */
	/*-----------------------------------------------------------------------------------*/

	/*
	 * Resolves the string to array notice for variable period subs
	 * by providing the suggested billing period
	 *
	 * @param string $period
	 * @param obj $product
	 * @return string
	 * @since 2.2.0
	 */
	public function product_period( $period, $product ){

		if( WC_Name_Your_Price_Helpers::is_billing_period_variable( $product->id ) ){
			$period = WC_Name_Your_Price_Helpers::get_posted_period( $product->id );		
		}

		return $period;
	}

} // end class