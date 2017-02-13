<?php
/*
Plugin Name: Crowdfunding for WooCommerce
Plugin URI: http://coder.fm/item/crowdfunding-for-woocommerce-plugin/
Description: Crowdfunding products for WooCommerce.
Version: 2.3.4
Author: Algoritmika Ltd
Author URI: http://www.algoritmika.com
Text Domain: crowdfunding-for-woocommerce
Domain Path: /langs
Copyright: © 2016 Algoritmika Ltd.
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Check if WooCommerce is active
$plugin = 'woocommerce/woocommerce.php';
if (
	! in_array( $plugin, apply_filters( 'active_plugins', get_option( 'active_plugins', array() ) ) ) &&
	! ( is_multisite() && array_key_exists( $plugin, get_site_option( 'active_sitewide_plugins', array() ) ) )
) return;

if ( 'crowdfunding-for-woocommerce.php' === basename( __FILE__ ) ) {
	// Check if Pro is active, if so then return
	$plugin = 'crowdfunding-for-woocommerce-pro/crowdfunding-for-woocommerce-pro.php';
	if (
		in_array( $plugin, apply_filters( 'active_plugins', get_option( 'active_plugins', array() ) ) ) ||
		( is_multisite() && array_key_exists( $plugin, get_site_option( 'active_sitewide_plugins', array() ) ) )
	) return;
}

if ( ! class_exists( 'Alg_Woocommerce_Crowdfunding' ) ) :

/**
 * Main Alg_Woocommerce_Crowdfunding Class
 *
 * @class   Alg_Woocommerce_Crowdfunding
 * @version 2.3.3
 */

final class Alg_Woocommerce_Crowdfunding {

	/**
	 * Plugin version.
	 *
	 * @var   string
	 * @since 2.3.0
	 */
	public $version = '2.3.4';

	/**
	 * @var Alg_Woocommerce_Crowdfunding The single instance of the class
	 */
	protected static $_instance = null;

	/**
	 * Main Alg_Woocommerce_Crowdfunding Instance
	 *
	 * Ensures only one instance of Alg_Woocommerce_Crowdfunding is loaded or can be loaded.
	 *
	 * @static
	 * @return Alg_Woocommerce_Crowdfunding - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	}

	/**
	 * Alg_Woocommerce_Crowdfunding Constructor.
	 *
	 * @version 2.3.3
	 * @access  public
	 */
	public function __construct() {

		// Set up localisation
		load_plugin_textdomain( 'crowdfunding-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/langs/' );

		// Include required files
		$this->includes();

		// Settings & Scripts
		if ( is_admin() ) {
			// Backend
			add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_woocommerce_settings_tab' ) );
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'action_links' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
			add_action( 'admin_init',            array( $this, 'register_admin_scripts' ) );
		} else {
			// Frontend
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			if (
				'yes' === get_option( 'alg_wc_crowdfunding_product_by_user_' . 'start_date' . '_enabled', 'no' ) ||
				'yes' === get_option( 'alg_wc_crowdfunding_product_by_user_' . 'start_time' . '_enabled', 'no' ) ||
				'yes' === get_option( 'alg_wc_crowdfunding_product_by_user_' . 'end_date'   . '_enabled', 'no' ) ||
				'yes' === get_option( 'alg_wc_crowdfunding_product_by_user_' . 'end_time'   . '_enabled', 'no' )
			) {
				add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
				add_action( 'init',               array( $this, 'register_admin_scripts' ) );
			}
		}
	}

	/**
	 * enqueue_scripts.
	 *
	 * @version 2.3.2
	 * @since   1.2.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'alg-variations',      $this->plugin_url() . '/includes/js/alg-variations-frontend.js', array( 'jquery' ), $this->version );

		wp_enqueue_script( 'alg-progress-bar-src', $this->plugin_url() . '/includes/js/progressbar.min.js',        array( 'jquery' ), $this->version );
		wp_enqueue_script( 'alg-progress-bar',     $this->plugin_url() . '/includes/js/alg-progressbar.js',        array( 'jquery' ), $this->version );
	}

	/**
	 * register_admin_scripts.
	 *
	 * @version 2.3.2
	 * @since   1.1.0
	 */
	public function register_admin_scripts() {
		wp_register_script(
			'jquery-ui-timepicker',
			$this->plugin_url() . '/includes/js/jquery.timepicker.min.js',
			array( 'jquery' ),
			$this->version,
			true
		);
	}

	/**
	 * enqueue_admin_scripts.
	 *
	 * @version 2.3.3
	 */
	public function enqueue_admin_scripts() {
		wp_enqueue_script( 'jquery-ui-datepicker', false,                                                                            array(),           $this->version );
		wp_enqueue_script( 'jquery-ui-timepicker' );
		wp_enqueue_script( 'alg-datepicker',       $this->plugin_url() . '/includes/js/alg-datepicker.js',                           array( 'jquery' ), $this->version, true );
//		wp_enqueue_style( 'jquery-ui-css',         '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css', array(),           $this->version );
		wp_enqueue_style( 'jquery-ui-css',         '//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css',                    array(),           $this->version );
		wp_enqueue_style( 'alg-timepicker',        $this->plugin_url() . '/includes/css/jquery.timepicker.min.css',                  array(),           $this->version );
		wp_enqueue_script( 'jquery-ui-dialog',     false,                                                                            array(),           $this->version );
	}

	/**
	 * Show action links on the plugin screen
	 *
	 * @version 2.2.0
	 * @param   mixed $links
	 * @return  array
	 */
	public function action_links( $links ) {
		$settings_link   = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_crowdfunding' )         . '">' . __( 'Settings', 'woocommerce' )   . '</a>';
		$unlock_all_link = '<a href="' . esc_url( 'http://coder.fm/item/crowdfunding-for-woocommerce-plugin/' ) . '">' . __( 'Unlock all', 'woocommerce' ) . '</a>';
		$custom_links    = ( PHP_INT_MAX === apply_filters( 'alg_crowdfunding_option', 1 ) ) ? array( $settings_link ) : array( $settings_link, $unlock_all_link );
		return array_merge( $custom_links, $links );
	}

	/**
	 * alg_is_user_role.
	 *
	 * @version     2.2.2
	 * @since       2.2.2
	 * @deprecated  2.3.0
	 * @return      bool
	 */
	/* function alg_is_user_role( $user_role, $user_id = 0 ) {
		$the_user = ( 0 == $user_id ) ? wp_get_current_user() : get_user_by( 'id', $user_id );
		return ( isset( $the_user->roles ) && is_array( $the_user->roles ) && in_array( $user_role, $the_user->roles ) ) ? true : false;
	} */

	/**
	 * Include required core files used in admin and on the frontend.
	 *
	 * @version 2.3.0
	 */
	private function includes() {

		require_once( 'includes/admin/class-wc-crowdfunding-admin.php' );

		$settings = array();
		$settings[] = require_once( 'includes/admin/class-wc-crowdfunding-settings-general.php' );
		$settings[] = require_once( 'includes/admin/class-wc-crowdfunding-settings-product-info.php' );
		$settings[] = require_once( 'includes/admin/class-wc-crowdfunding-settings-open-pricing.php' );
		$settings[] = require_once( 'includes/admin/class-wc-crowdfunding-settings-product-by-user.php' );
		if ( is_admin() && get_option( 'alg_woocommerce_crowdfunding_version', '' ) !== $this->version ) {
			foreach ( $settings as $section ) {
				foreach ( $section->get_settings() as $value ) {
					if ( isset( $value['default'] ) && isset( $value['id'] ) ) {
						/* if ( isset ( $_GET['alg_woocommerce_crowdfunding_admin_options_reset'] ) ) {
							require_once( ABSPATH . 'wp-includes/pluggable.php' );
							if ( $this->alg_is_user_role( 'administrator' ) ) {
								delete_option( $value['id'] );
							}
						} */
						$autoload = isset( $value['autoload'] ) ? ( bool ) $value['autoload'] : true;
						add_option( $value['id'], $value['default'], '', ( $autoload ? 'yes' : 'no' ) );
					}
				}
			}
			update_option( 'alg_woocommerce_crowdfunding_version', $this->version );
		}

		require_once( 'includes/class-wc-crowdfunding.php' );
	}

	/**
	 * Add Woocommerce settings tab to WooCommerce settings.
	 */
	public function add_woocommerce_settings_tab( $settings ) {
		$settings[] = include( 'includes/admin/class-wc-settings-crowdfunding.php' );
		return $settings;
	}

	/**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugin_dir_url( __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}
}

endif;

/**
 * Returns the main instance of Alg_Woocommerce_Crowdfunding to prevent the need to use globals.
 *
 * @return Alg_Woocommerce_Crowdfunding
 */
if ( ! function_exists( 'alg_wc_crowdfunding' ) ) {
	function alg_wc_crowdfunding() {
		return Alg_Woocommerce_Crowdfunding::instance();
	}
}

/**
 * alg_wc_crowdfunding_get_file.
 *
 * @version 2.3.1
 * @since   2.3.1
 */
if ( ! function_exists( 'alg_wc_crowdfunding_get_file' ) ) {
	function alg_wc_crowdfunding_get_file() {
		return __FILE__;
	}
}

alg_wc_crowdfunding();
