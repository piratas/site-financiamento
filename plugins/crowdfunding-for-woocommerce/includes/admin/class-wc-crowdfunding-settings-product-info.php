<?php
/**
 * Crowdfunding for WooCommerce - Product Info Section Settings
 *
 * @version 2.0.0
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Crowdfunding_Settings_Product_Info' ) ) :

class Alg_WC_Crowdfunding_Settings_Product_Info {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->id   = 'product_info';
		$this->desc = __( 'Product Info', 'crowdfunding-for-woocommerce' );

		add_filter( 'woocommerce_get_sections_alg_crowdfunding',              array( $this, 'settings_section' ) );
		add_filter( 'woocommerce_get_settings_alg_crowdfunding_' . $this->id, array( $this, 'get_settings' ), PHP_INT_MAX );
	}

	/**
	 * settings_section.
	 */
	function settings_section( $sections ) {
		$sections[ $this->id ] = $this->desc;
		return $sections;
	}

	/**
	 * get_settings.
	 *
	 * @version 2.0.0
	 */
	function get_settings() {

		$settings = array(

			array(
				'title' => __( 'Custom Product Tab', 'crowdfunding-for-woocommerce' ),
				'type'  => 'title',
				'id'    => 'alg_woocommerce_crowdfunding_product_tab_options'
			),

			array(
				'title'     => __( 'Add Product Info to Custom Tab on Single Product Page', 'crowdfunding-for-woocommerce' ),
				'desc'      => __( 'Add', 'crowdfunding-for-woocommerce' ),
				'id'        => 'alg_woocommerce_crowdfunding_product_tab_enabled',
				'default'   => 'yes',
				'type'      => 'checkbox',
			),

			array(
				'title'     => __( 'Custom Tab Title', 'crowdfunding-for-woocommerce' ),
				'id'        => 'alg_woocommerce_crowdfunding_product_tab_title',
				'default'   => __( 'Crowdfunding', 'crowdfunding-for-woocommerce' ),
				'type'      => 'text',
			),

			array(
				'title'   => __( 'Info', 'crowdfunding-for-woocommerce' ),
				'id'      => 'alg_woocommerce_crowdfunding_product_tab',
				'default' => '<table>' . PHP_EOL
				           . '[product_total_orders before="<tr><td>Total Backers</td><td>" after="</td></tr>"]' . PHP_EOL
				           . '[product_total_orders_sum before="<tr><td>Total Sum</td><td>" after="</td></tr>"]' . PHP_EOL
				           . '[product_crowdfunding_goal before="<tr><td>Goal</td><td>" after="</td></tr>"]' . PHP_EOL
				           . '[product_crowdfunding_goal_remaining before="<tr><td>Remaining</td><td>" after="</td></tr>"]' . PHP_EOL
				           . '[product_crowdfunding_goal_progress_bar before="<tr><td>Remaining</td><td>" after="</td></tr>"]' . PHP_EOL
				           . '[product_crowdfunding_startdate before="<tr><td>Start Date</td><td>" after="</td></tr>"]' . PHP_EOL
				           . '[product_crowdfunding_deadline before="<tr><td>End Date</td><td>" after="</td></tr>"]' . PHP_EOL
				           . '[product_crowdfunding_time_remaining before="<tr><td>Time Remaining</td><td>" after="</td></tr>"]' . PHP_EOL
				           . '[product_crowdfunding_time_progress_bar before="<tr><td>Time Remaining</td><td>" after="</td></tr>"]' . PHP_EOL
				           . '</table>',
				'type'    => 'textarea',
				'css'     => 'width:66%;min-width:300px;height:200px;',
			),

			array(
				'title'   => __( 'Order', 'crowdfunding-for-woocommerce' ),
				'id'      => 'alg_woocommerce_crowdfunding_product_tab_priority',
				'default' => 40,
				'type'    => 'number',
				'custom_attributes' => array('step' => '1', 'min' => '1', ),
			),

			array(
				'type' => 'sectionend',
				'id'   => 'alg_woocommerce_crowdfunding_product_tab_options'
			),

			array(
				'title' => __( 'Custom Product Info', 'crowdfunding-for-woocommerce' ),
				'type'  => 'title',
				'id'    => 'alg_woocommerce_crowdfunding_product_info_options'
			),

			array(
				'title'     => __( 'Add Product Info to Single Product Page', 'crowdfunding-for-woocommerce' ),
				'desc'      => __( 'Add', 'crowdfunding-for-woocommerce' ),
				'id'        => 'alg_woocommerce_crowdfunding_product_info_enabled',
				'default'   => 'no',
				'type'      => 'checkbox',
			),

			array(
				'title'   => __( 'Info', 'crowdfunding-for-woocommerce' ),
				'id'      => 'alg_woocommerce_crowdfunding_product_info',
				'default' => '[product_total_orders before="<p>Total Backers: " after="</p>"]' . PHP_EOL
				           . '[product_total_orders_sum before="<p>Total Sum: " after="</p>"]' . PHP_EOL
				           . '[product_crowdfunding_goal before="<p>Goal: " after="</p>"]' . PHP_EOL
				           . '[product_crowdfunding_goal_remaining before="<p>Remaining: " after="</p>"]' . PHP_EOL
				           . '[product_crowdfunding_goal_progress_bar before="<p>Remaining: " after="</p>"]' . PHP_EOL
				           . '[product_crowdfunding_startdate before="<p>Start Date: " after="</p>"]' . PHP_EOL
				           . '[product_crowdfunding_deadline before="<p>End Date: " after="</p>"]' . PHP_EOL
				           . '[product_crowdfunding_time_remaining before="<p>Time Remaining: " after="</p>"]' . PHP_EOL
				           . '[product_crowdfunding_time_progress_bar before="<p>Time Remaining: " after="</p>"]',
				'type'    => 'textarea',
				'css'     => 'width:66%;min-width:300px;height:200px;',
			),

			array(
				'title'   => __( 'Position', 'crowdfunding-for-woocommerce' ),
				'id'      => 'alg_woocommerce_crowdfunding_product_info_filter',
				'default' => 'woocommerce_before_single_product_summary',
				'type'    => 'select',
				'options' => array(
					'woocommerce_before_single_product_summary' => __( 'Before single product summary', 'crowdfunding-for-woocommerce' ),
					'woocommerce_after_single_product_summary'  => __( 'After single product summary',  'crowdfunding-for-woocommerce' ),
					'woocommerce_single_product_summary'        => __( 'Inside single product summary', 'crowdfunding-for-woocommerce' ),
				),
			),

			array(
				'title'   => __( 'Order', 'crowdfunding-for-woocommerce' ),
				'id'      => 'alg_woocommerce_crowdfunding_product_info_filter_priority',
				'default' => 10,
				'type'    => 'number',
				'custom_attributes' => array('step' => '1', 'min' => '1', ),
			),

			array(
				'type' => 'sectionend',
				'id'   => 'alg_woocommerce_crowdfunding_product_info_options'
			),

			array(
				'title' => __( 'Custom Product Info - Category View', 'crowdfunding-for-woocommerce' ),
				'type'  => 'title',
				'id'    => 'alg_woocommerce_crowdfunding_product_info_archives_options'
			),

			array(
				'title'     => __( 'Add Product Info to Archives Pages', 'crowdfunding-for-woocommerce' ),
				'desc'      => __( 'Add', 'crowdfunding-for-woocommerce' ),
				'id'        => 'alg_woocommerce_crowdfunding_product_info_archives_enabled',
				'default'   => 'no',
				'type'      => 'checkbox',
			),

			array(
				'title'   => __( 'Info', 'crowdfunding-for-woocommerce' ),
				'id'      => 'alg_woocommerce_crowdfunding_product_info_archives',
				'default' => '[product_total_orders_sum before="<p>Funds to Date: " after="</p>"]' . PHP_EOL
				           . '[product_crowdfunding_goal before="<p>End Goal: " after="</p>"]' . PHP_EOL
				           . '[product_crowdfunding_time_progress_bar before="<p>" after="</p>"]',
				'type'    => 'textarea',
				'css'     => 'width:66%;min-width:300px;height:200px;',
			),

			array(
				'title'   => __( 'Position', 'crowdfunding-for-woocommerce' ),
				'id'      => 'alg_woocommerce_crowdfunding_product_info_archives_filter',
				'default' => 'woocommerce_after_shop_loop_item',
				'type'    => 'select',
				'options' => array(
					'woocommerce_before_shop_loop_item'       => __( 'Before product', 'crowdfunding-for-woocommerce' ),
					'woocommerce_before_shop_loop_item_title' => __( 'Before product title', 'crowdfunding-for-woocommerce' ),
					'woocommerce_after_shop_loop_item'        => __( 'After product', 'crowdfunding-for-woocommerce' ),
					'woocommerce_after_shop_loop_item_title'  => __( 'After product title', 'crowdfunding-for-woocommerce' ),
				),
			),

			array(
				'title'   => __( 'Order', 'crowdfunding-for-woocommerce' ),
				'id'      => 'alg_woocommerce_crowdfunding_product_info_arch_filter_priority',
				'default' => 10,
				'type'    => 'number',
				'custom_attributes' => array('step' => '1', 'min' => '1', ),
			),

			array(
				'type' => 'sectionend',
				'id'   => 'alg_woocommerce_crowdfunding_product_info_archives_options'
			),

			array(
				'title' => __( 'Product Price', 'crowdfunding-for-woocommerce' ),
				'type'  => 'title',
				'id'    => 'alg_woocommerce_crowdfunding_product_info_price_options'
			),

			array(
				'title'     => __( 'Hide Main Price for Variable Crowdfunding Products', 'crowdfunding-for-woocommerce' ),
				'desc'      => __( 'Hide', 'crowdfunding-for-woocommerce' ),
				'id'        => 'alg_woocommerce_crowdfunding_hide_variable_price',
				'default'   => 'no',
				'type'      => 'checkbox',
			),

			array(
				'type' => 'sectionend',
				'id'   => 'alg_woocommerce_crowdfunding_product_info_price_options'
			),

		);

		return $settings;
	}

}

endif;

return new Alg_WC_Crowdfunding_Settings_Product_Info();
