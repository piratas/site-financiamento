<?php
/**
 * Crowdfunding for WooCommerce - Open Pricing Section Settings
 *
 * @version 2.3.4
 * @since   2.2.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Crowdfunding_Settings_Open_Pricing' ) ) :

class Alg_WC_Crowdfunding_Settings_Open_Pricing {

	/**
	 * Constructor.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	public function __construct() {

		$this->id   = 'open_pricing';
		$this->desc = __( 'Open Pricing (Name Your Price)', 'crowdfunding-for-woocommerce' );

		add_filter( 'woocommerce_get_sections_alg_crowdfunding',              array( $this, 'settings_section' ) );
		add_filter( 'woocommerce_get_settings_alg_crowdfunding_' . $this->id, array( $this, 'get_settings' ), PHP_INT_MAX );
	}

	/**
	 * settings_section.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	function settings_section( $sections ) {
		$sections[ $this->id ] = $this->desc;
		return $sections;
	}

	/**
	 * get_settings.
	 *
	 * @version 2.3.4
	 * @since   2.2.0
	 */
	function get_settings() {
		$settings = array(
			array(
				'title'    => __( 'Labels and Messages', 'crowdfunding-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_crowdfunding_product_open_price_messages_options',
			),
			array(
				'title'    => __( 'Frontend Label', 'crowdfunding-for-woocommerce' ),
				'id'       => 'alg_crowdfunding_product_open_price_label_frontend',
				'default'  => __( 'Name Your Price', 'crowdfunding-for-woocommerce' ),
				'type'     => 'text',
				'css'      => 'width:250px;',
			),
			array(
				'title'    => __( 'Message on Empty Price', 'crowdfunding-for-woocommerce' ),
				'id'       => 'alg_crowdfunding_product_open_price_messages_required',
				'default'  => __( 'Price is required!', 'crowdfunding-for-woocommerce' ),
				'type'     => 'text',
				'css'      => 'width:250px;',
			),
			array(
				'title'    => __( 'Message on Price too Small', 'crowdfunding-for-woocommerce' ),
				'id'       => 'alg_crowdfunding_product_open_price_messages_to_small',
				'default'  => __( 'Entered price is too small!', 'crowdfunding-for-woocommerce' ),
				'type'     => 'text',
				'css'      => 'width:250px;',
			),
			array(
				'title'    => __( 'Message on Price too Big', 'crowdfunding-for-woocommerce' ),
				'id'       => 'alg_crowdfunding_product_open_price_messages_to_big',
				'default'  => __( 'Entered price is too big!', 'crowdfunding-for-woocommerce' ),
				'type'     => 'text',
				'css'      => 'width:250px;',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_crowdfunding_product_open_price_messages_options',
			),
			array(
				'title'    => __( 'Template', 'crowdfunding-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_crowdfunding_open_price_template_options',
			),
			array(
				'title'    => __( 'Frontend Template', 'crowdfunding-for-woocommerce' ),
				'id'       => 'alg_crowdfunding_open_price_template',
				'default'  => '<label for="alg_crowdfunding_open_price">%title%</label> %input_field% %currency_symbol%',
				'type'     => 'textarea',
				'css'      => 'min-width:300px;width:100%;height:50px;',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_crowdfunding_open_price_template_options',
			),
		);
		return $settings;
	}

}

endif;

return new Alg_WC_Crowdfunding_Settings_Open_Pricing();
