<?php
/**
 * Crowdfunding for WooCommerce - Settings
 *
 * @version 2.5.0
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
	 * @version 2.5.0
	 * @since   1.0.0
	 */
	function get_settings() {
		global $current_section;
		return array_merge( apply_filters( 'woocommerce_get_settings_' . $this->id . '_' . $current_section, array() ), array(
			array(
				'title'     => __( 'Reset Options', 'crowdfunding-for-woocommerce' ),
				'type'      => 'title',
				'id'        => 'alg_wc_crowdfunding_reset_' . $current_section . '_options',
			),
			array(
				'title'     => __( 'Reset Section Settings', 'crowdfunding-for-woocommerce' ),
				'desc'      => '<strong>' . __( 'Reset', 'crowdfunding-for-woocommerce' ) . '</strong>',
				'id'        => 'alg_wc_crowdfunding_reset_' . $current_section,
				'default'   => 'no',
				'type'      => 'checkbox',
			),
			array(
				'type'      => 'sectionend',
				'id'        => 'alg_wc_crowdfunding_reset_' . $current_section . '_options',
			),
		) );
	}

	/**
	 * Save settings.
	 *
	 * @version 2.5.0
	 * @since   2.5.0
	 */
	function save() {
		parent::save();
		global $current_section;
		if ( 'yes' === get_option( 'alg_wc_crowdfunding_reset_' . $current_section, 'no' ) ) {
			foreach ( $this->get_settings() as $value ) {
				if ( isset( $value['default'] ) ) {
					delete_option( $value['id'] );
					$autoload = isset( $value['autoload'] ) ? ( bool ) $value['autoload'] : true;
					add_option( $value['id'], $value['default'], '', ( $autoload ? 'yes' : 'no' ) );
				}
			}
		}
	}

}

endif;

return new Alg_WC_Settings_Crowdfunding();
