<?php
/**
 * WooCommerce Crowdfunding Product Open Pricing
 *
 * The WooCommerce Crowdfunding Product Open Pricing class.
 *
 * @version 2.3.4
 * @since   2.2.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Alg_Crowdfunding_Product_Open_Pricing' ) ) :

class Alg_Crowdfunding_Product_Open_Pricing {

	/**
	 * Constructor.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	function __construct() {
		add_filter( 'woocommerce_get_price',                  array( $this, 'get_open_price' ), PHP_INT_MAX, 2 );
		add_filter( 'woocommerce_get_price_html',             array( $this, 'hide_original_price' ), PHP_INT_MAX, 2 );
		add_filter( 'woocommerce_get_variation_price_html',   array( $this, 'hide_original_price' ), PHP_INT_MAX, 2 );
		add_filter( 'woocommerce_is_sold_individually',       array( $this, 'hide_quantity_input_field' ), PHP_INT_MAX, 2 );
		add_filter( 'woocommerce_is_purchasable',             array( $this, 'is_purchasable' ), PHP_INT_MAX - 100, 2 );
		add_filter( 'woocommerce_product_supports',           array( $this, 'disable_add_to_cart_ajax' ), PHP_INT_MAX, 3 );
		add_filter( 'woocommerce_product_add_to_cart_url',    array( $this, 'add_to_cart_url' ), PHP_INT_MAX, 2 );
		add_filter( 'woocommerce_product_add_to_cart_text',   array( $this, 'add_to_cart_text' ), PHP_INT_MAX, 2 );
		add_action( 'woocommerce_before_add_to_cart_button',  array( $this, 'add_open_price_input_field_to_frontend' ), PHP_INT_MAX );
		add_filter( 'woocommerce_add_to_cart_validation',     array( $this, 'validate_open_price_on_add_to_cart' ), PHP_INT_MAX, 2 );
		add_filter( 'woocommerce_add_cart_item_data',         array( $this, 'add_open_price_to_cart_item_data' ), PHP_INT_MAX, 3 );
		add_filter( 'woocommerce_add_cart_item',              array( $this, 'add_open_price_to_cart_item' ), PHP_INT_MAX, 2 );
		add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_item_open_price_from_session' ), PHP_INT_MAX, 3 );
	}

	/**
	 * is_open_price_product.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	function is_open_price_product( $_product ) {
		$is_crowdfudning            = ( 'yes' === get_post_meta( $_product->id, '_' . 'alg_crowdfunding_enabled', true ) ) ? true : false;
		$is_crowdfudning_open_price = ( 'yes' === get_post_meta( $_product->id, '_' . 'alg_crowdfunding_product_open_price_enabled', true ) ) ? true : false;
		return ( $is_crowdfudning && $is_crowdfudning_open_price );
	}

	/**
	 * is_purchasable.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	function is_purchasable( $purchasable, $_product ) {
		if ( $this->is_open_price_product( $_product ) ) {
			$purchasable = true;

			// Products must exist of course
			if ( ! $_product->exists() ) {
				$purchasable = false;

			// Other products types need a price to be set
			/* } elseif ( $_product->get_price() === '' ) {
				$purchasable = false; */

			// Check the product is published
			} elseif ( $_product->post->post_status !== 'publish' && ! current_user_can( 'edit_post', $_product->id ) ) {
				$purchasable = false;
			}
		}
		return $purchasable;
	}

	/**
	 * add_to_cart_text.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	function add_to_cart_text( $text, $_product ) {
		return ( $this->is_open_price_product( $_product ) ) ? __( 'Read more', 'woocommerce' ) : $text;
	}

	/**
	 * disable_add_to_cart_ajax.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	function disable_add_to_cart_ajax( $supports, $feature, $_product ) {
		if ( $this->is_open_price_product( $_product ) && 'ajax_add_to_cart' === $feature ) {
			$supports = false;
		}
		return $supports;
	}

	/**
	 * add_to_cart_url.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	function add_to_cart_url( $url, $_product ) {
		return ( $this->is_open_price_product( $_product ) ) ? get_permalink( $_product->id ) : $url;
	}

	/**
	 * hide_quantity_input_field.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	function hide_quantity_input_field( $return, $_product ) {
		return ( $this->is_open_price_product( $_product ) ) ? true : $return;
	}

	/**
	 * hide_original_price.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	function hide_original_price( $price, $_product ) {
		return ( $this->is_open_price_product( $_product ) ) ? '' : $price;
	}

	/**
	 * get_open_price.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	function get_open_price( $price, $_product ) {
		return ( $this->is_open_price_product( $_product ) && isset( $_product->alg_crowdfunding_open_price ) ) ? $_product->alg_crowdfunding_open_price : $price;
	}

	/**
	 * validate_open_price_on_add_to_cart.
	 *
	 * @version 2.3.3
	 * @since   2.2.0
	 */
	function validate_open_price_on_add_to_cart( $passed, $product_id ) {
		$the_product = wc_get_product( $product_id );
		if ( $this->is_open_price_product( $the_product ) ) {
			$min_price = get_post_meta( $product_id, '_' . 'alg_crowdfunding_product_open_price_min_price', true );
			$max_price = get_post_meta( $product_id, '_' . 'alg_crowdfunding_product_open_price_max_price', true );
			if ( $min_price > 0 ) {
				if ( ! isset( $_POST['alg_crowdfunding_open_price'] ) || '' === $_POST['alg_crowdfunding_open_price'] ) {
					wc_add_notice( get_option( 'alg_crowdfunding_product_open_price_messages_required', __( 'Price is required!', 'crowdfunding-for-woocommerce' ) ), 'error' );
					return false;
				}
				if ( $_POST['alg_crowdfunding_open_price'] < $min_price ) {
					wc_add_notice( get_option( 'alg_crowdfunding_product_open_price_messages_to_small', __( 'Entered price is too small!', 'crowdfunding-for-woocommerce' ) ), 'error' );
					return false;
				}
			}
			if ( $max_price > 0 ) {
				if ( isset( $_POST['alg_crowdfunding_open_price'] ) && $_POST['alg_crowdfunding_open_price'] > $max_price ) {
					wc_add_notice( get_option( 'alg_crowdfunding_product_open_price_messages_to_big', __( 'Entered price is too big!', 'crowdfunding-for-woocommerce' ) ), 'error' );
					return false;
				}
			}
		}
		return $passed;
	}

	/**
	 * get_cart_item_open_price_from_session.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	function get_cart_item_open_price_from_session( $item, $values, $key ) {
		if ( array_key_exists( 'alg_crowdfunding_open_price', $values ) ) {
			$item['data']->alg_crowdfunding_open_price = $values['alg_crowdfunding_open_price'];
		}
		return $item;
	}

	/**
	 * add_open_price_to_cart_item_data.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	function add_open_price_to_cart_item_data( $cart_item_data, $product_id, $variation_id ) {
		if ( isset( $_POST['alg_crowdfunding_open_price'] ) ) {
			$cart_item_data['alg_crowdfunding_open_price'] = $_POST['alg_crowdfunding_open_price'];
		}
		return $cart_item_data;
	}

	/**
	 * add_open_price_to_cart_item.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	function add_open_price_to_cart_item( $cart_item_data, $cart_item_key ) {
		if ( isset( $cart_item_data['alg_crowdfunding_open_price'] ) ) {
			$cart_item_data['data']->alg_crowdfunding_open_price = $cart_item_data['alg_crowdfunding_open_price'];
		}
		return $cart_item_data;
	}

	/**
	 * add_open_price_input_field_to_frontend.
	 *
	 * @version 2.3.4
	 * @since   2.2.0
	 */
	function add_open_price_input_field_to_frontend() {
		$the_product = wc_get_product();
		if ( $this->is_open_price_product( $the_product ) ) {
			$title = get_option( 'alg_crowdfunding_product_open_price_label_frontend', __( 'Name Your Price', 'crowdfunding-for-woocommerce' ) );
//			$placeholder = $the_product->get_price();
			$value = ( isset( $_POST['alg_crowdfunding_open_price'] ) ) ?
				$_POST['alg_crowdfunding_open_price'] :
				get_post_meta( $the_product->id, '_' . 'alg_crowdfunding_product_open_price_default_price', true );
			$custom_attributes = '';
			$wc_price_decimals = get_post_meta( $the_product->id, '_' . 'alg_crowdfunding_product_open_price_step', true );
			if ( '' === $wc_price_decimals ) {
				$wc_price_decimals = wc_get_price_decimals();
			}
			if ( $wc_price_decimals > 0 ) {
				$custom_attributes .= sprintf( 'step="0.%0' . ( $wc_price_decimals ) . 'd" ', 1 );
			}
			$input_field = '<input '
				. 'type="number" '
				. 'class="text" '
				. 'style="width:75px;text-align:center;" '
				. 'name="alg_crowdfunding_open_price" '
				. 'id="alg_crowdfunding_open_price" '
//				. 'placeholder="' . $placeholder . '" '
				. 'value="' . $value . '" '
				. $custom_attributes
			. '>';
			$template = get_option( 'alg_crowdfunding_open_price_template',
				'<label for="alg_crowdfunding_open_price">%title%</label> %input_field% %currency_symbol%' );
			echo str_replace(
				array( '%title%', '%input_field%', '%currency_symbol%' ),
				array( $title,    $input_field,    get_woocommerce_currency_symbol() ),
				$template
			);
		}
	}

}

endif;

return new Alg_Crowdfunding_Product_Open_Pricing();
