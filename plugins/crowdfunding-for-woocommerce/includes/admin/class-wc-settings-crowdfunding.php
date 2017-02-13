<?php
/**
 * Crowdfunding for WooCommerce - Settings
 *
 * @version 2.3.0
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Settings_Crowdfunding' ) ) :

class Alg_WC_Settings_Crowdfunding extends WC_Settings_Page {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function __construct() {

		$this->id    = 'alg_crowdfunding';
		$this->label = __( 'Crowdfunding', 'crowdfunding-for-woocommerce' );

		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 2.3.0
	 * @since   1.0.0
	 */
	public function get_settings() {
		global $current_section;
		return apply_filters( 'woocommerce_get_settings_' . $this->id . '_' . $current_section, array() );
	}

}

endif;

return new Alg_WC_Settings_Crowdfunding();
