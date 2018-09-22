<?php
/**
 * Crowdfunding for WooCommerce - Functions
 *
 * @version 2.6.0
 * @since   2.3.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! function_exists( 'alg_get_product_id_or_variation_parent_id' ) ) {
	/**
	 * alg_get_product_id_or_variation_parent_id.
	 *
	 * @version 2.6.0
	 * @since   2.4.0
	 */
	function alg_get_product_id_or_variation_parent_id( $_product ) {
		if ( ! $_product || ! is_object( $_product ) ) {
			return 0;
		}
		return ( version_compare( get_option( 'woocommerce_version', null ), '3.0.0', '<' ) ?
			$_product->id : ( $_product->is_type( 'variation' ) ? $_product->get_parent_id() : $_product->get_id() ) );
	}
}

if ( ! function_exists( 'alg_get_product_post_status' ) ) {
	/**
	 * alg_get_product_post_status.
	 *
	 * @version 2.6.0
	 * @since   2.4.0
	 */
	function alg_get_product_post_status( $_product ) {
		if ( ! $_product || ! is_object( $_product ) ) {
			return '';
		}
		return ( version_compare( get_option( 'woocommerce_version', null ), '3.0.0', '<' ) ? $_product->post->post_status : $_product->get_status() );
	}
}

if ( ! function_exists( 'alg_get_user_campaign_standard_fields' ) ) {
	/**
	 * alg_get_user_campaign_standard_fields.
	 *
	 * @version 2.3.2
	 * @since   2.3.0
	 */
	function alg_get_user_campaign_standard_fields() {
		return array(
			'desc' => array(
				'desc'      => __( 'Description', 'crowdfunding-for-woocommerce' ),
			),
			'short_desc' => array(
				'desc'      => __( 'Short Description', 'crowdfunding-for-woocommerce' ),
			),
			'image' => array(
				'desc'      => __( 'Image', 'crowdfunding-for-woocommerce' ),
			),
			'regular_price' => array(
				'desc'      => __( 'Regular Price', 'crowdfunding-for-woocommerce' ),
			),
			'sale_price' => array(
				'desc'      => __( 'Sale Price', 'crowdfunding-for-woocommerce' ),
			),
			'cats' => array(
				'desc'      => __( 'Categories', 'crowdfunding-for-woocommerce' ),
			),
			'tags' => array(
				'desc'      => __( 'Tags', 'crowdfunding-for-woocommerce' ),
			),
		);
	}
}

if ( ! function_exists( 'alg_get_user_campaign_crowdfunding_fields' ) ) {
	/**
	 * alg_get_user_campaign_crowdfunding_fields.
	 *
	 * @version 2.3.2
	 * @since   2.3.0
	 */
	function alg_get_user_campaign_crowdfunding_fields() {
		return apply_filters( 'alg_crowdfunding_user_campaign_fields', array(
			'goal' => array(
				'desc'      => __( 'Goal', 'crowdfunding-for-woocommerce' ) . ' (' . get_woocommerce_currency_symbol() . ')',
				'type'      => 'price',
				'meta_name' => 'alg_crowdfunding_goal_sum',
			),
			'goal_backers' => array(
				'desc'      => __( 'Goal (Backers)', 'crowdfunding-for-woocommerce' ),
				'type'      => 'number',
				'meta_name' => 'alg_crowdfunding_goal_backers',
			),
			'goal_items' => array(
				'desc'      => __( 'Goal (Items)', 'crowdfunding-for-woocommerce' ),
				'type'      => 'number',
				'meta_name' => 'alg_crowdfunding_goal_items',
			),
			'start_date' => array(
				'desc'      => __( 'Time: Start Date', 'crowdfunding-for-woocommerce' ),
				'type'      => 'date',
				'meta_name' => 'alg_crowdfunding_startdate',
			),
			'start_time' => array(
				'desc'      => __( 'Time: Start Time', 'crowdfunding-for-woocommerce' ),
				'type'      => 'time',
				'meta_name' => 'alg_crowdfunding_starttime',
			),
			'end_date' => array(
				'desc'      => __( 'Time: End Date', 'crowdfunding-for-woocommerce' ),
				'type'      => 'date',
				'meta_name' => 'alg_crowdfunding_deadline',
			),
			'end_time' => array(
				'desc'      => __( 'Time: End Time', 'crowdfunding-for-woocommerce' ),
				'type'      => 'time',
				'meta_name' => 'alg_crowdfunding_deadline_time',
			),
			'add_to_cart_label_single' => array(
				'desc'      => __( 'Labels: Add to Cart Button Text (Single)', 'crowdfunding-for-woocommerce' ),
				'type'      => 'text',
				'meta_name' => 'alg_crowdfunding_button_label_single',
			),
			'add_to_cart_label_archives' => array(
				'desc'      => __( 'Labels: Add to Cart Button Text (Archive/Category)', 'crowdfunding-for-woocommerce' ),
				'type'      => 'text',
				'meta_name' => 'alg_crowdfunding_button_label_loop',
			),
			'open_pricing_default_price' => array(
				'desc'      => __( 'Open Pricing: Default Price', 'crowdfunding-for-woocommerce' ) . ' (' . get_woocommerce_currency_symbol() . ')',
				'type'      => 'price',
				'meta_name' => 'alg_crowdfunding_product_open_price_default_price',
			),
			'open_pricing_min_price' => array(
				'desc'      => __( 'Open Pricing: Min Price', 'crowdfunding-for-woocommerce' ) . ' (' . get_woocommerce_currency_symbol() . ')',
				'type'      => 'price',
				'meta_name' => 'alg_crowdfunding_product_open_price_min_price',
			),
			'open_pricing_max_price' => array(
				'desc'      => __( 'Open Pricing: Max Price', 'crowdfunding-for-woocommerce' ) . ' (' . get_woocommerce_currency_symbol() . ')',
				'type'      => 'price',
				'meta_name' => 'alg_crowdfunding_product_open_price_max_price',
			),
		) );
	}
}

if ( ! function_exists( 'alg_get_user_campaign_all_fields' ) ) {
	/**
	 * alg_get_user_campaign_all_fields.
	 *
	 * @version 2.3.2
	 * @since   2.3.0
	 */
	function alg_get_user_campaign_all_fields() {
		return array_merge( alg_get_user_campaign_standard_fields(), alg_get_user_campaign_crowdfunding_fields() );
	}
}

if ( ! function_exists( 'alg_variation_radio_button' ) ) {
	/**
	 * alg_variation_radio_button.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function alg_variation_radio_button( $_product, $variation ) {
		$attributes_html = '';
		$variation_attributes_display_values = array();
		$is_checked = true;
		foreach ( $variation['attributes'] as $attribute_full_name => $attribute_value ) {

			$attributes_html .= ' ' . $attribute_full_name . '="' . $attribute_value . '"';

			$attribute_name = $attribute_full_name;
			$prefix = 'attribute_';
			if ( substr( $attribute_full_name, 0, strlen( $prefix ) ) === $prefix ) {
				$attribute_name = substr( $attribute_full_name, strlen( $prefix ) );
			}

			$checked = isset( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ? wc_clean( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) : $_product->get_variation_default_attribute( $attribute_name );
			if ( $checked != $attribute_value ) $is_checked = false;

			$terms = get_terms( $attribute_name );
			foreach ( $terms as $term ) {
				if ( is_object( $term ) && isset( $term->slug ) && $term->slug === $attribute_value && isset( $term->name ) ) {
					$attribute_value = $term->name;
				}
			}

			$variation_attributes_display_values[] = $attribute_value;

		}
		$variation_title = implode( ', ', $variation_attributes_display_values ) . ' (' . wc_price( $variation['display_price'] ) . ')';
		$variation_id    = $variation['variation_id'];
		$is_checked = checked( $is_checked, true, false );

		echo '<p>';
		echo '<input name="alg_variations" type="radio"' . $attributes_html . ' variation_id="' . $variation_id . '"' . $is_checked . '>' . ' ' . $variation_title;
		echo '<br>';
//		echo '<small>' . $variation['variation_description'] . '</small>';
		echo '<small>' . get_post_meta( $variation_id, '_variation_description', true )  . '</small>';
		echo '</p>';
	}
}

/**
 * alg_get_table_html.
 *
 * @version 2.3.0
 * @since   2.3.0
 */
if ( ! function_exists( 'alg_get_table_html' ) ) {
	function alg_get_table_html( $data, $args = array() ) {
		$defaults = array(
			'table_class'        => '',
			'table_style'        => '',
			'table_heading_type' => 'horizontal',
			'columns_classes'    => array(),
			'columns_styles'     => array(),
		);
		$args = array_merge( $defaults, $args );
		extract( $args );
		$table_class = ( '' == $table_class ) ? '' : ' class="' . $table_class . '"';
		$table_style = ( '' == $table_style ) ? '' : ' style="' . $table_style . '"';
		$html = '';
		$html .= '<table' . $table_class . $table_style . '>';
		$html .= '<tbody>';
		foreach( $data as $row_number => $row ) {
			$html .= '<tr>';
			foreach( $row as $column_number => $value ) {
				$th_or_td = ( ( 0 === $row_number && 'horizontal' === $table_heading_type ) || ( 0 === $column_number && 'vertical' === $table_heading_type ) ) ? 'th' : 'td';
				$column_class = ( ! empty( $columns_classes ) && isset( $columns_classes[ $column_number ] ) ) ? ' class="' . $columns_classes[ $column_number ] . '"' : '';
				$column_style = ( ! empty( $columns_styles ) && isset( $columns_styles[ $column_number ] ) ) ? ' style="' . $columns_styles[ $column_number ] . '"' : '';

				$html .= '<' . $th_or_td . $column_class . $column_style . '>';
				$html .= $value;
				$html .= '</' . $th_or_td . '>';
			}
			$html .= '</tr>';
		}
		$html .= '</tbody>';
		$html .= '</table>';
		return $html;
	}
}

if ( ! function_exists( 'alg_get_product_orders_data' ) ) {
	/**
	 * alg_get_product_orders_data.
	 *
	 * @version 2.3.0
	 * @since   2.3.0
	 */
	function alg_get_product_orders_data( $return_value = 'total_orders', $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) return '';
		$saved_value = get_post_meta( $product_id, '_' . 'alg_crowdfunding_' . $return_value, true );
		if ( '' === $saved_value || 'manual' === get_option( 'alg_crowdfunding_products_data_update', 'fifthteen' ) ) {
			$calculated_value = alg_calculate_product_orders_data( $return_value, $product_id );
			update_post_meta( $product_id, '_' . 'alg_crowdfunding_' . $return_value, $calculated_value );
			$return = $calculated_value;
		} else {
			$return = $saved_value;
		}
		if ( isset( $atts['starting_offset'] ) && 0 != $atts['starting_offset'] ) {
			$return += $atts['starting_offset'];
		}
		return $return;
	}
}

if ( ! function_exists( 'alg_calculate_product_orders_data' ) ) {
	/**
	 * alg_calculate_product_orders_data.
	 *
	 * @version 2.4.0
	 * @since   1.0.0
	 * @todo    recheck if "$woocommerce_loop" stuff is really needed; "global $product" - do I need this?
	 */
	function alg_calculate_product_orders_data( $return_value = 'total_orders', $product_id ) {

		$the_product = wc_get_product( $product_id );

		if ( ! $the_product ) {
			return;
		}

		global $woocommerce_loop, $post;
		$saved_wc_loop = $woocommerce_loop;
		$saved_post    = $post;
		/*
		global $loop, $product, $wp_query, $woocommerce;
		$saved_loop = $loop;
		$saved_product = $product;
		$saved_wp_query = $wp_query;
		$saved_woocommerce = $woocommerce;
		*/

		$total_orders = 0;
		$total_qty    = 0;
		$total_sum    = 0;
//		$order_statuses = ( isset( $atts['order_status'] ) ) ? explode( ',', str_replace( ' ', '', $atts['order_status'] ) ) : array( 'wc-completed' );
		$order_statuses = get_option( 'alg_woocommerce_crowdfunding_order_statuses', array( 'wc-completed' ) );
		$product_ids = array();
		if ( $the_product->is_type( 'grouped' ) ) {
			$product_ids = $the_product->get_children();
		} else {
			$product_ids = array( $product_id );
		}
		$date_query_after = trim(
			get_post_meta( $product_id, '_' . 'alg_crowdfunding_startdate', true ) . ' ' . get_post_meta( $product_id, '_' . 'alg_crowdfunding_starttime', true ),
			' '
		);
		$offset = 0;
		$block_size = 256;
		while( true ) {
			$args = array(
				'post_type'      => 'shop_order',
				'post_status'    => $order_statuses,
				'posts_per_page' => $block_size,
				'offset'         => $offset,
				'orderby'        => 'date',
				'order'          => 'ASC',
				'date_query'     => array(
					array(
						'after'     => $date_query_after,
						'inclusive' => true,
					),
				),
				'fields'         => 'ids',
			);
			$loop = new WP_Query( $args );
			if ( ! $loop->have_posts() ) {
				break;
			}
			foreach ( $loop->posts as $order_id ) {
				$the_order = wc_get_order( $order_id );
				$the_items = $the_order->get_items();
				$item_found = false;
				foreach( $the_items as $item ) {
					if ( in_array( $item['product_id'], $product_ids ) ) {
						$total_sum += $item['line_total'] + $item['line_tax'];
						$total_qty += $item['qty'];
						$item_found = true;
					}
				}
				if ( $item_found ) {
					$total_orders++;
				}
			}
			$offset += $block_size;
		}
		woocommerce_reset_loop();
		wp_reset_postdata();
//		wp_reset_query();

		$woocommerce_loop = $saved_wc_loop;
		$post             = $saved_post;
		setup_postdata( $post );

		global $product;
		$product = wc_get_product();

		/*
		$loop = $saved_loop;
		$product = $saved_product;
		$wp_query = $saved_wp_query;
		$woocommerce = $saved_woocommerce;
		*/

		switch ( $return_value ) {
			case 'orders_sum':
				return $total_sum;
			case 'total_items':
				return $total_qty;
			default: // 'total_orders'
				return $total_orders;
		}

	}
}

if ( ! function_exists( 'alg_count_crowdfunding_products' ) ) {
	/**
	 * alg_count_crowdfunding_products.
	 *
	 * @version 2.6.0
	 * @since   2.0.0
	 */
	function alg_count_crowdfunding_products( $post_id ) {
		$args = array(
			'post_type'      => 'product',
			'post_status'    => array( 'any', 'trash' ),
			'posts_per_page' => 3,
			'meta_key'       => '_alg_crowdfunding_enabled',
			'meta_value'     => 'yes',
			'post__not_in'   => array( $post_id ),
			'fields'         => 'ids',
		);
		$loop = new WP_Query( $args );
		return $loop->found_posts;
	}
}

if ( ! function_exists( 'alg_wc_crowdfunding_calculate_and_update_product_orders_data' ) ) {
	/**
	 * alg_wc_crowdfunding_calculate_and_update_product_orders_data.
	 *
	 * @version 2.6.0
	 * @since   2.6.0
	 */
	function alg_wc_crowdfunding_calculate_and_update_product_orders_data( $product_id ) {
		update_post_meta( $product_id, '_' . 'alg_crowdfunding_' . 'orders_sum',   alg_calculate_product_orders_data( 'orders_sum',   $product_id ) );
		update_post_meta( $product_id, '_' . 'alg_crowdfunding_' . 'total_orders', alg_calculate_product_orders_data( 'total_orders', $product_id ) );
		update_post_meta( $product_id, '_' . 'alg_crowdfunding_' . 'total_items',  alg_calculate_product_orders_data( 'total_items',  $product_id ) );
		update_post_meta( $product_id, '_' . 'alg_crowdfunding_' . 'products_data_updated_time', time() );
	}
}
