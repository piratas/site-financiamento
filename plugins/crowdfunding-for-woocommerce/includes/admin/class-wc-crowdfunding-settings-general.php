<?php
/**
 * Crowdfunding for WooCommerce - General Section Settings
 *
 * @version 2.3.2
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Crowdfunding_Settings_General' ) ) :

class Alg_WC_Crowdfunding_Settings_General {

	/**
	 * Constructor.
	 *
	 * @version 2.3.0
	 * @since   1.0.0
	 */
	public function __construct() {

		$this->id   = '';
		$this->desc = __( 'General', 'crowdfunding-for-woocommerce' );

		add_filter( 'woocommerce_get_sections_alg_crowdfunding',              array( $this, 'settings_section' ) );
		add_filter( 'woocommerce_get_settings_alg_crowdfunding_' . $this->id, array( $this, 'get_settings' ), PHP_INT_MAX );
	}

	/**
	 * settings_section.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function settings_section( $sections ) {
		$sections[ $this->id ] = $this->desc;
		return $sections;
	}

	/**
	 * get_settings.
	 *
	 * @version 2.3.2
	 * @since   1.0.0
	 */
	function get_settings() {

		$desc = '';
		if ( 'manual' != get_option( 'alg_crowdfunding_products_data_update', 'fifthteen' ) ) {
			if ( '' != get_option( 'alg_crowdfunding_products_data_update_cron_time', '' ) ) {
				$scheduled_time_diff = get_option( 'alg_crowdfunding_products_data_update_cron_time', '' ) - time();
				if ( $scheduled_time_diff > 60 ) {
					$desc = sprintf( __( '%s till next update.', 'crowdfunding-for-woocommerce' ), human_time_diff( get_option( 'alg_crowdfunding_products_data_update_cron_time', '' ) ) );
				} elseif ( $scheduled_time_diff > 0 ) {
					$desc = sprintf( __( '%s seconds till next update.', 'crowdfunding-for-woocommerce' ), $scheduled_time_diff );
				} else {
					$desc = sprintf( __( '%s seconds since last update.', 'crowdfunding-for-woocommerce' ), -1 * $scheduled_time_diff );
				}
			}
		}

		$order_statuses = array(
			'wc-pending'    => _x( 'Pending Payment', 'Order status', 'woocommerce' ),
			'wc-processing' => _x( 'Processing', 'Order status', 'woocommerce' ),
			'wc-on-hold'    => _x( 'On Hold', 'Order status', 'woocommerce' ),
			'wc-completed'  => _x( 'Completed', 'Order status', 'woocommerce' ),
			'wc-cancelled'  => _x( 'Cancelled', 'Order status', 'woocommerce' ),
			'wc-refunded'   => _x( 'Refunded', 'Order status', 'woocommerce' ),
			'wc-failed'     => _x( 'Failed', 'Order status', 'woocommerce' ),
		);
		$order_statuses = apply_filters( 'wc_order_statuses', $order_statuses );

		$settings = array(
			array(
				'title'     => __( 'Crowdfunding Options', 'crowdfunding-for-woocommerce' ),
				'type'      => 'title',
				'desc'      => ( '' != get_option( 'alg_woocommerce_crowdfunding_version', '' ) ? 'v' . get_option( 'alg_woocommerce_crowdfunding_version', '' ) : '' ),
				'id'        => 'alg_woocommerce_crowdfunding_options',
			),
			array(
				'title'     => __( 'WooCommerce Crowdfunding', 'crowdfunding-for-woocommerce' ),
				'desc'      => '<strong>' . __( 'Enable', 'crowdfunding-for-woocommerce' ) . '</strong>',
				'desc_tip'  => __( 'Crowdfunding Products for WooCommerce.', 'crowdfunding-for-woocommerce' ),
				'id'        => 'alg_woocommerce_crowdfunding_enabled',
				'default'   => 'yes',
				'type'      => 'checkbox',
			),
			array(
				'type'      => 'sectionend',
				'id'        => 'alg_woocommerce_crowdfunding_options',
			),

			array(
				'title'     => __( 'Status Options', 'crowdfunding-for-woocommerce' ),
				'type'      => 'title',
				'id'        => 'alg_woocommerce_crowdfunding_status_options',
			),
			array(
				'title'     => __( 'Order Statuses to Include in Calculations', 'crowdfunding-for-woocommerce' ),
				'id'        => 'alg_woocommerce_crowdfunding_order_statuses',
				'default'   => array( 'wc-completed' ),
				'type'      => 'multiselect',
				'options'   => $order_statuses,
				'class'     => 'chosen_select',
			),
			array(
				'type'      => 'sectionend',
				'id'        => 'alg_woocommerce_crowdfunding_status_options',
			),

			array(
				'title'     => __( 'Crowdfunding Buttons Options', 'crowdfunding-for-woocommerce' ),
				'type'      => 'title',
				'id'        => 'alg_woocommerce_crowdfunding_buttons_options',
			),
			array(
				'title'     => __( 'Default Button Label on Single Product Page', 'crowdfunding-for-woocommerce' ),
				'desc_tip'  => __( 'You can change this in product edit on per product basis', 'crowdfunding-for-woocommerce' ),
				'id'        => 'alg_woocommerce_crowdfunding_button_single',
				'default'   => __( 'Back This Project', 'crowdfunding-for-woocommerce' ),
				'type'      => 'textarea',
				'css'       => 'width:300px;',
			),
			array(
				'title'     => __( 'Default Button Label on Archive Pages', 'crowdfunding-for-woocommerce' ),
				'desc_tip'  => __( 'You can change this in product edit on per product basis', 'crowdfunding-for-woocommerce' ),
				'id'        => 'alg_woocommerce_crowdfunding_button_archives',
				'default'   => __( 'Read More', 'crowdfunding-for-woocommerce' ),
				'type'      => 'textarea',
				'css'       => 'width:300px;',
			),
			array(
				'type'      => 'sectionend',
				'id'        => 'alg_woocommerce_crowdfunding_buttons_options',
			),

			array(
				'title'     => __( 'Crowdfunding Messages Options', 'crowdfunding-for-woocommerce' ),
				'type'      => 'title',
				'id'        => 'alg_woocommerce_crowdfunding_messages_options',
			),
			array(
				'title'     => __( 'Message on Product Not Yet Started', 'crowdfunding-for-woocommerce' ),
				'desc'      => sprintf( __( 'You can use shortcodes here. For example: %s.', 'crowdfunding-for-woocommerce' ), '[product_crowdfunding_time_to_start]' ),
				'id'        => 'alg_woocommerce_crowdfunding_message_not_started',
				'default'   => __( '<strong>Not yet started!</strong>', 'crowdfunding-for-woocommerce' ),
				'type'      => 'textarea',
				'css'       => 'width:300px;',
			),
			array(
				'title'     => __( 'Message on Product Ended', 'crowdfunding-for-woocommerce' ),
				'desc'      => sprintf( __( 'You can use shortcodes here. For example: %s.', 'crowdfunding-for-woocommerce' ), '[product_crowdfunding_time_remaining]' ),
				'id'        => 'alg_woocommerce_crowdfunding_message_ended',
				'default'   => __( '<strong>Ended!</strong>', 'crowdfunding-for-woocommerce' ),
				'type'      => 'textarea',
				'css'       => 'width:300px;',
			),
			array(
				'type'      => 'sectionend',
				'id'        => 'alg_woocommerce_crowdfunding_messages_options',
			),

			array(
				'title'     => __( 'Variable Add to Cart Form Options', 'crowdfunding-for-woocommerce' ),
				'type'      => 'title',
				'id'        => 'alg_woocommerce_crowdfunding_variable_add_to_cart_options',
			),
			array(
				'title'     => __( 'Radio Buttons for Variable Products', 'crowdfunding-for-woocommerce' ),
				'desc'      => __( 'Enable', 'crowdfunding-for-woocommerce' ),
				'id'        => 'alg_woocommerce_crowdfunding_variable_add_to_cart_radio_enabled',
				'default'   => 'yes',
				'type'      => 'checkbox',
			),
			/* array(
				'title'     => __( 'Template', 'crowdfunding-for-woocommerce' ),
				'id'        => 'alg_woocommerce_crowdfunding_variable_add_to_cart_radio_template',
				'default'   => file_get_contents( untrailingslashit( realpath( plugin_dir_path( __FILE__ ) . '/../..' ) ) . '/includes/alg-add-to-cart-variable.php' ),
				'type'      => 'custom_textarea',
				'css'       => 'width:90%;height:300px;',
			), */
			array(
				'type'      => 'sectionend',
				'id'        => 'alg_woocommerce_crowdfunding_variable_add_to_cart_options',
			),

			array(
				'title'     => __( 'Ending Options', 'crowdfunding-for-woocommerce' ),
				'type'      => 'title',
				'id'        => 'alg_woocommerce_crowdfunding_ending_options',
			),
			array(
				'title'     => __( 'End On Time Ended', 'crowdfunding-for-woocommerce' ),
				'desc'      => __( 'Enable', 'crowdfunding-for-woocommerce' ),
				'id'        => 'alg_crowdfunding_end_on_time',
				'default'   => 'yes',
				'type'      => 'checkbox',
			),
			array(
				'title'     => __( 'End On Goal Reached', 'crowdfunding-for-woocommerce' ),
				'desc'      => __( 'Enable', 'crowdfunding-for-woocommerce' ),
				'id'        => 'alg_crowdfunding_end_on_goal_reached',
				'default'   => 'no',
				'type'      => 'checkbox',
			),
			array(
				'type'      => 'sectionend',
				'id'        => 'alg_woocommerce_crowdfunding_ending_options',
			),

			array(
				'title'     => __( 'Products Data Update Options', 'crowdfunding-for-woocommerce' ),
				'type'      => 'title',
				'desc'      => $desc,
				'id'        => 'alg_woocommerce_crowdfunding_data_update_options',
			),
			array(
				'title'     => __( 'Update Rate', 'crowdfunding-for-woocommerce' ),
				'id'        => 'alg_crowdfunding_products_data_update',
				'default'   => 'fifthteen',
				'type'      => 'select',
				'options'   => array(
					'minutely'   => __( 'Update Every Minute', 'crowdfunding-for-woocommerce' ),
					'fifthteen'  => __( 'Update Every Fifthteen Minutes', 'crowdfunding-for-woocommerce' ),
					'hourly'     => __( 'Update Hourly', 'crowdfunding-for-woocommerce' ),
					'twicedaily' => __( 'Update Twice Daily', 'crowdfunding-for-woocommerce' ),
					'daily'      => __( 'Update Daily', 'crowdfunding-for-woocommerce' ),
					'weekly'     => __( 'Update Weekly', 'crowdfunding-for-woocommerce' ),
					'manual'     => __( 'Realtime (Not Recommended)', 'crowdfunding-for-woocommerce' ),
				),
			),
			array(
				'type'      => 'sectionend',
				'id'        => 'alg_woocommerce_crowdfunding_data_update_options',
			),

		);

		return $settings;
	}

}

endif;

return new Alg_WC_Crowdfunding_Settings_General();
