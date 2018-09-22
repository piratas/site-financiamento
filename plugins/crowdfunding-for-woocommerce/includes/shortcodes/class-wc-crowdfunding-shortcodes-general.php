<?php
/**
 * Crowdfunding for WooCommerce - Shortcodes General
 *
 * @version 2.3.6
 * @since   2.3.6
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Crowdfunding_Shortcodes_General' ) ) :

class Alg_WC_Crowdfunding_Shortcodes_General extends Alg_WC_Crowdfunding_Shortcodes {

	/**
	 * Constructor.
	 *
	 * @version 2.3.6
	 * @since   2.3.6
	 */
	function __construct() {
		add_shortcode( 'product_crowdfunding_total_backers',                       array( $this, 'alg_product_crowdfunding_total_backers' ) );
		add_shortcode( 'product_crowdfunding_total_sum',                           array( $this, 'alg_product_crowdfunding_total_sum' ) );
		add_shortcode( 'product_crowdfunding_total_items',                         array( $this, 'alg_product_crowdfunding_total_items' ) );
		add_shortcode( 'product_crowdfunding_goal',                                array( $this, 'alg_product_crowdfunding_goal' ) );
		add_shortcode( 'product_crowdfunding_goal_backers',                        array( $this, 'alg_product_crowdfunding_goal_backers' ) );
		add_shortcode( 'product_crowdfunding_goal_items',                          array( $this, 'alg_product_crowdfunding_goal_items' ) );
		add_shortcode( 'product_crowdfunding_goal_remaining',                      array( $this, 'alg_product_crowdfunding_goal_remaining' ) );
		add_shortcode( 'product_crowdfunding_goal_backers_remaining',              array( $this, 'alg_product_crowdfunding_goal_backers_remaining' ) );
		add_shortcode( 'product_crowdfunding_goal_items_remaining',                array( $this, 'alg_product_crowdfunding_goal_items_remaining' ) );
		add_shortcode( 'product_crowdfunding_add_to_cart_form',                    array( $this, 'alg_product_crowdfunding_add_to_cart_form' ) );
		// Deprecated
		add_shortcode( 'product_total_orders',                                     array( $this, 'alg_product_total_orders' ) );
		add_shortcode( 'product_total_orders_sum',                                 array( $this, 'alg_product_total_orders_sum' ) );
	}

	/**
	 * alg_product_crowdfunding_add_to_cart_form.
	 *
	 * @version 2.1.0
	 * @since   1.2.0
	 */
	function alg_product_crowdfunding_add_to_cart_form( $atts ) {
//		remove_filter( 'wc_get_template', array( $this, 'change_variable_add_to_cart_template' ), PHP_INT_MAX );
		$the_product = isset( $atts['product_id'] ) ? wc_get_product( $atts['product_id'] ) : wc_get_product();
		$return = ( $the_product->is_type( 'variable' ) ) ? woocommerce_variable_add_to_cart() : woocommerce_simple_add_to_cart();
//		add_filter(    'wc_get_template', array( $this, 'change_variable_add_to_cart_template' ), PHP_INT_MAX, 5 );
		return $return;
	}

	/**
	 * alg_product_crowdfunding_total_items.
	 *
	 * @version 2.3.6
	 * @since   2.2.0
	 */
	function alg_product_crowdfunding_total_items( $atts ) {
		if ( isset( $atts['type'] ) && 'percent' === $atts['type'] ) {
			$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
			if ( ! $product_id ) {
				return '';
			}
			$atts['total_value'] = get_post_meta( $product_id, '_' . 'alg_crowdfunding_goal_items', true );
		}
		return $this->output_shortcode( alg_get_product_orders_data( 'total_items', $atts ), $atts );
	}

	/**
	 * alg_product_crowdfunding_total_backers.
	 *
	 * @version 2.3.6
	 * @since   2.2.0
	 */
	function alg_product_crowdfunding_total_backers( $atts ) {
		if ( isset( $atts['type'] ) && 'percent' === $atts['type'] ) {
			$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
			if ( ! $product_id ) {
				return '';
			}
			$atts['total_value'] = get_post_meta( $product_id, '_' . 'alg_crowdfunding_goal_backers', true );
		}
		return $this->output_shortcode( alg_get_product_orders_data( 'total_orders', $atts ), $atts );
	}

	/**
	 * alg_product_total_orders.
	 *
	 * @version     2.2.0
	 * @since       1.0.0
	 * @deprecated
	 */
	function alg_product_total_orders( $atts ) {
		return $this->alg_product_crowdfunding_total_backers( $atts );
	}

	/**
	 * alg_product_crowdfunding_total_sum.
	 *
	 * @version 2.3.6
	 * @since   2.2.0
	 */
	function alg_product_crowdfunding_total_sum( $atts ) {
		if ( isset( $atts['type'] ) && 'percent' === $atts['type'] ) {
			$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
			if ( ! $product_id ) {
				return '';
			}
			$atts['total_value'] = get_post_meta( $product_id, '_' . 'alg_crowdfunding_goal_sum', true );
		} elseif ( ! isset( $atts['type'] ) ) {
			$atts['type'] = 'price';
		}
		return $this->output_shortcode( alg_get_product_orders_data( 'orders_sum', $atts ), $atts );
	}

	/**
	 * alg_product_total_orders_sum.
	 *
	 * @version     2.2.0
	 * @since       1.0.0
	 * @deprecated
	 */
	function alg_product_total_orders_sum( $atts ) {
		return $this->alg_product_crowdfunding_total_sum( $atts );
	}

	/**
	 * alg_product_crowdfunding_goal_items.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	function alg_product_crowdfunding_goal_items( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) return '';
		return $this->output_shortcode( get_post_meta( $product_id, '_' . 'alg_crowdfunding_goal_items', true ), $atts );
	}

	/**
	 * alg_product_crowdfunding_goal_backers.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	function alg_product_crowdfunding_goal_backers( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) return '';
		return $this->output_shortcode( get_post_meta( $product_id, '_' . 'alg_crowdfunding_goal_backers', true ), $atts );
	}

	/**
	 * alg_product_crowdfunding_goal.
	 *
	 * @version 2.1.0
	 * @since   1.0.0
	 */
	function alg_product_crowdfunding_goal( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) return '';
		$atts['type'] = 'price';
		return $this->output_shortcode( get_post_meta( $product_id, '_' . 'alg_crowdfunding_goal_sum', true ), $atts );
	}

	/**
	 * alg_product_crowdfunding_goal_items_remaining.
	 *
	 * @version 2.3.6
	 * @since   2.2.0
	 */
	function alg_product_crowdfunding_goal_items_remaining( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) return '';
		if ( isset( $atts['type'] ) && 'percent' === $atts['type'] ) {
			$atts['total_value'] = get_post_meta( $product_id, '_' . 'alg_crowdfunding_goal_items', true );
		}
		return $this->output_shortcode( get_post_meta( $product_id, '_' . 'alg_crowdfunding_goal_items', true ) - alg_get_product_orders_data( 'total_items', $atts ), $atts );
	}

	/**
	 * alg_product_crowdfunding_goal_backers_remaining.
	 *
	 * @version 2.3.6
	 * @since   2.2.0
	 */
	function alg_product_crowdfunding_goal_backers_remaining( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) return '';
		if ( isset( $atts['type'] ) && 'percent' === $atts['type'] ) {
			$atts['total_value'] = get_post_meta( $product_id, '_' . 'alg_crowdfunding_goal_backers', true );
		}
		return $this->output_shortcode( get_post_meta( $product_id, '_' . 'alg_crowdfunding_goal_backers', true ) - alg_get_product_orders_data( 'total_orders', $atts ), $atts );
	}

	/**
	 * alg_product_crowdfunding_goal_remaining.
	 *
	 * @version 2.3.6
	 * @since   1.0.0
	 */
	function alg_product_crowdfunding_goal_remaining( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) return '';
		if ( isset( $atts['type'] ) && 'percent' === $atts['type'] ) {
			$atts['total_value'] = get_post_meta( $product_id, '_' . 'alg_crowdfunding_goal_sum', true );
		} elseif ( ! isset( $atts['type'] ) ) {
			$atts['type'] = 'price';
		}
		return $this->output_shortcode( get_post_meta( $product_id, '_' . 'alg_crowdfunding_goal_sum', true ) - alg_get_product_orders_data( 'orders_sum', $atts ), $atts );
	}

}

endif;

return new Alg_WC_Crowdfunding_Shortcodes_General();
