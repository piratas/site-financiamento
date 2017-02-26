<?php
/**
 * Plugin Name: Storefront WooCommerce Customiser
 * Plugin URI: http://woothemes.com/products/storefront-woocommerce-customiser/
 * Description: Adds options to the customise the WooCommerce appearance and behaviour
 * Version: 1.9.0
 * Author: WooThemes
 * Author URI: http://woothemes.com/
 * Requires at least: 4.0.0
 * Tested up to: 4.0.0
 *
 * Text Domain: storefront-woocommerce-customiser
 * Domain Path: /languages/
 *
 * @package Storefront_WooCommerce_Customiser
 * @category Core
 * @author James Koster
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), '645b6c28ced85553f07e81e72c3e9186', '518369' );

/**
 * Returns the main instance of Storefront_WooCommerce_Customiser to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Storefront_WooCommerce_Customiser
 */
function Storefront_WooCommerce_Customiser() {
	return Storefront_WooCommerce_Customiser::instance();
} // End Storefront_WooCommerce_Customiser()

Storefront_WooCommerce_Customiser();

/**
 * Main Storefront_WooCommerce_Customiser Class
 *
 * @class Storefront_WooCommerce_Customiser
 * @version	1.0.0
 * @since 1.0.0
 * @package	Storefront_WooCommerce_Customiser
 */
final class Storefront_WooCommerce_Customiser {
	/**
	 * Storefront_WooCommerce_Customiser The single instance of Storefront_WooCommerce_Customiser.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $token;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $version;

	// Admin - Start
	/**
	 * The admin object.
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $admin;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct () {
		$this->token 			= 'storefront-woocommerce-customiser';
		$this->plugin_url 		= plugin_dir_url( __FILE__ );
		$this->plugin_path 		= plugin_dir_path( __FILE__ );
		$this->version 			= '1.9.0';

		register_activation_hook( __FILE__, array( $this, 'install' ) );

		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		add_action( 'init', array( $this, 'swc_setup' ) );

		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'swc_plugin_links' ) );
	} // End __construct()

	/**
	 * Main Storefront_WooCommerce_Customiser Instance
	 *
	 * Ensures only one instance of Storefront_WooCommerce_Customiser is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Storefront_WooCommerce_Customiser()
	 * @return Main Storefront_WooCommerce_Customiser instance
	 */
	public static function instance () {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	} // End instance()

	/**
	 * Load the localisation file.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'storefront-woocommerce-customiser', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	} // End load_plugin_textdomain()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	} // End __wakeup()

	/**
	 * Plugin page links
	 *
	 * @since  1.4.0
	 */
	public function swc_plugin_links( $links ) {
		$plugin_links = array(
			'<a href="http://support.woothemes.com/">' . __( 'Support', 'storefront-woocommerce-customiser' ) . '</a>',
			'<a href="http://docs.woothemes.com/document/storefront-woocommerce-customiser/">' . __( 'Docs', 'storefront-woocommerce-customiser' ) . '</a>',
		);

		return array_merge( $plugin_links, $links );
	}

	/**
	 * Installation. Runs on activation.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install () {
		$this->_log_version_number();

		// get theme customizer url
        $url = admin_url() . 'customize.php?';
        $url .= 'url=' . urlencode( site_url() . '?storefront-customizer=true' ) ;
        $url .= '&return=' . urlencode( admin_url() . 'plugins.php' );
        $url .= '&storefront-customizer=true';

		$notices 		= get_option( 'swc_activation_notice', array() );
		$notices[]		= sprintf( __( '%sThanks for installing the Storefront WooCommerce Customiser. To get started, visit the %sCustomizer%s.%s %sOpen the Customizer%s', 'storefront-woocommerce-customiser' ), '<p>', '<a href="' . $url . '">', '</a>', '</p>', '<p><a href="' . $url . '" class="button button-primary">', '</a></p>' );

		update_option( 'swc_activation_notice', $notices );

	} // End install()

	/**
	 * Log the plugin version number.
	 * @access  private
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number () {
		// Log the version number.
		update_option( $this->token . '-version', $this->version );
	} // End _log_version_number()

	/**
	 * Setup all the things, if Storefront or a child theme using Storefront that has not disabled the Customizer settings is active
	 * @return void
	 */
	public function swc_setup() {

		if ( 'storefront' == get_option( 'template' ) && class_exists( 'WooCommerce' ) && apply_filters( 'storefront_woocommerce_customizer_enabled', true ) ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'swc_script' ) );
			add_action( 'customize_register', array( $this, 'swc_customize_register' ) );
			add_filter( 'body_class', array( $this, 'swc_body_class' ) );

			add_filter( 'storefront_loop_columns', array( $this, 'swc_shop_columns' ), 999 );
			add_filter( 'storefront_products_per_page', array( $this, 'swc_shop_products_per_page' ), 999 );
			add_action( 'woocommerce_before_shop_loop', array( $this, 'swc_product_loop_wrap' ), 40 );
			add_action( 'woocommerce_after_shop_loop', array( $this, 'swc_product_loop_wrap_close' ), 5 );
			add_action( 'wp', array( $this, 'swc_shop_layout' ), 999 );

			add_filter( 'storefront_product_categories_args', array( $this, 'swc_product_category_args' ) );
			add_filter( 'storefront_recent_products_args', array( $this, 'swc_recent_product_args' ) );
			add_filter( 'storefront_featured_products_args', array( $this, 'swc_featured_product_args' ) );
			add_filter( 'storefront_popular_products_args', array( $this, 'swc_popular_product_args' ) );
			add_filter( 'storefront_on_sale_products_args', array( $this, 'swc_on_sale_product_args' ) );
			add_filter( 'storefront_best_selling_products_args', array( $this, 'swc_best_sellers_product_args' ) );
			add_filter( 'storefront_product_thumbnail_columns', array( $this, 'swc_product_thumbnails' ) );

			add_action( 'admin_notices', array( $this, 'customizer_notice' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'swc_add_customizer_css' ), 999 );

			// View more buttons
			add_action( 'storefront_homepage_after_product_categories', array( $this, 'swc_homepage_product_categories_view_more' ) );
			add_action( 'storefront_homepage_after_recent_products', array( $this, 'swc_homepage_recent_products_view_more' ) );
			add_action( 'storefront_homepage_after_featured_products', array( $this, 'swc_homepage_featured_products_view_more' ) );
			add_action( 'storefront_homepage_after_popular_products', array( $this, 'swc_homepage_top_rated_products_view_more' ) );
			add_action( 'storefront_homepage_after_on_sale_products', array( $this, 'swc_homepage_on_sale_products_view_more' ) );
			add_action( 'storefront_homepage_after_best_selling_products', array( $this, 'swc_homepage_best_sellers_products_view_more' ) );

			// Infinite scroll wrapper
			add_action( 'woocommerce_before_shop_loop', array( $this, 'swc_scroll_wrapper' ), 4 );
			add_action( 'woocommerce_after_shop_loop', array( $this, 'swc_scroll_wrapper_close' ), 40 );

			// Hide the 'More' section in the customizer
			add_filter( 'storefront_customizer_more', '__return_false' );

			// Homepage description
			add_action( 'storefront_homepage_after_product_categories_title', array( $this, 'swc_homepage_product_categories_description' ) );
			add_action( 'storefront_homepage_after_recent_products_title', array( $this, 'swc_homepage_recent_products_description' ) );
			add_action( 'storefront_homepage_after_featured_products_title', array( $this, 'swc_homepage_featured_products_description' ) );
			add_action( 'storefront_homepage_after_popular_products_title', array( $this, 'swc_homepage_popular_products_description' ) );
			add_action( 'storefront_homepage_after_on_sale_products_title', array( $this, 'swc_homepage_on_sale_products_description' ) );
			add_action( 'storefront_homepage_after_best_selling_products_title', array( $this, 'swc_homepage_best_sellers_products_description' ) );

			// Storefront custom panel
			if ( current_user_can( 'manage_woocommerce' ) ) {
				add_filter( 'woocommerce_product_data_tabs', array( $this, 'swc_custom_product_data_tab' ) );
				add_action( 'woocommerce_product_data_panels', array( $this, 'swc_custom_product_data_panel' ) );
				add_action( 'woocommerce_process_product_meta', array( $this, 'swc_single_product_layout_override_admin_process' ) );
			}

			add_filter( 'body_class', array( $this, 'swc_single_body_class' ), 11 );
			add_action( 'wp', array( $this, 'swc_single_product_layout_sidebar' ), 999 );

			// Composite Products integration
			if ( class_exists( 'WC_Composite_Products' ) ) {
				global $woocommerce_composite_products;

				if ( isset( $woocommerce_composite_products->version ) && version_compare( $woocommerce_composite_products->version, '3.0', '>=' ) ) {

					// Filter component options loop columns
					add_filter( 'woocommerce_composite_component_loop_columns', array( $this, 'swc_cp_component_options_loop_columns' ), 5 );
					// Filter max component options per page
					add_filter( 'woocommerce_component_options_per_page', array( $this, 'swc_cp_component_options_per_page' ), 5 );
					// Filter max component columns in review/summary
					add_filter( 'woocommerce_composite_component_summary_max_columns', array( $this, 'swc_cp_summary_max_columns' ), 5 );
					// Filter toggle-box view
					add_filter( 'woocommerce_composite_component_toggled', array( $this, 'swc_cp_component_toggled' ), 5, 3 );

					// Register additional customizer section to configure these parameters
					add_action( 'customize_register', array( $this, 'swc_cp_customize_register' ) );
				}
			}
		}
	}

	/**
	 * Display a notice linking to the Customizer
	 * @since   1.0.0
	 * @return  void
	 */
	public function customizer_notice() {
		$notices = get_option( 'swc_activation_notice' );

		if ( $notices = get_option( 'swc_activation_notice' ) ) {

			foreach ( $notices as $notice ) {
				echo '<div class="updated">' . $notice . '</div>';
			}

			delete_option( 'swc_activation_notice' );
		}
	}

	/**
	 * Enqueue CSS.
	 * @since   1.0.0
	 * @return  void
	 */
	public function swc_script() {
		$infinite_scroll = get_theme_mod( 'swc_infinite_scroll', false );

		wp_enqueue_style( 'swc-styles', plugins_url( '/assets/css/style.css', __FILE__ ), '', '1.2.1' );

		if ( ( true == $infinite_scroll ) && ( is_shop() || is_product_taxonomy() || is_product_category() || is_product_tag() ) ) {
			wp_enqueue_script( 'jscroll', plugins_url( '/assets/js/jquery.jscroll.min.js', __FILE__ ), array( 'jquery' ) );
			wp_enqueue_script( 'jscroll-init', plugins_url( '/assets/js/jscroll-init.min.js', __FILE__ ), array( 'jscroll' ) );
		}
	}

	/**
	 * Customizer Controls and settings
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 */
	public function swc_customize_register( $wp_customize ) {

        /**
		 * Header search bar toggle
		 */
		$wp_customize->add_setting( 'swc_header_search', array(
	        'default'           => true,
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_header_search', array(
            'label'         => __( 'Search - W-P-L-O-C-K-E-R-.-C-O-M', 'storefront-woocommerce-customiser' ),
            'description' 	=> __( 'Toggle the display of the search form.', 'storefront-woocommerce-customiser' ),
            'section'       => 'header_image',
            'settings'      => 'swc_header_search',
            'type'          => 'checkbox',
            'priority'		=> 50,
        ) ) );

		/**
		 * Header cart toggle
		 */
		$wp_customize->add_setting( 'swc_header_cart', array(
	        'default'           => true,
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_header_cart', array(
            'label'         => __( 'Cart link', 'storefront-woocommerce-customiser' ),
            'description' 	=> __( 'Toggle the display of the cart link / dropdown.', 'storefront-woocommerce-customiser' ),
            'section'       => 'header_image',
            'settings'      => 'swc_header_cart',
            'type'          => 'checkbox',
            'priority'		=> 60,
        ) ) );

	    /**
	     * Shop Section
	     */
        $wp_customize->add_section( 'swc_shop_section' , array(
		    'title'      		=> __( 'Shop', 'storefront-woocommerce-customiser' ),
		    'description' 		=> __( 'Customise the look & feel of your product catalog', 'storefront-woocommerce-customiser' ),
		    'priority'   		=> 55,
		    'active_callback'	=> array( $this, 'swc_storefront_shop_callback' ),
		) );

		/**
    	 * Shop Layout
    	 */
        $wp_customize->add_setting( 'swc_shop_layout', array(
	        'default'           => 'default',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_shop_layout', array(
            'label'         => __( 'Shop layout', 'storefront-woocommerce-customiser' ),
            'description'	=> __( 'Applied to the shop page & product archives.', 'storefront-woocommerce-customiser' ),
            'section'       => 'swc_shop_section',
            'settings'      => 'swc_shop_layout',
            'type'     		=> 'radio',
            'priority'		=> 5,
			'choices'  		=> array(
				'default'			=> 'Default',
				'full-width'		=> 'Full Width',
			),
        ) ) );

		/**
    	 * Archive Description
    	 */
        $wp_customize->add_setting( 'swc_archive_description', array(
	        'default'           => 'default',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_archive_description', array(
            'label'         => __( 'Archive description', 'storefront-woocommerce-customiser' ),
            'description'	=> __( 'Applied to the product and tag archives.', 'storefront-woocommerce-customiser' ),
            'section'       => 'swc_shop_section',
            'settings'      => 'swc_archive_description',
            'type'     		=> 'radio',
            'priority'		=> 6,
			'choices'  		=> array(
				'default'		=> 'Above products',
				'beneath'		=> 'Beneath products',
			),
        ) ) );

        /**
         * Product Columns
         */
	    $wp_customize->add_setting( 'swc_product_columns', array(
	        'default'           => apply_filters( 'swc_product_columns_default', 3 ),
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_product_columns', array(
            'label'         => __( 'Product columns', 'storefront-woocommerce-customiser' ),
            'section'       => 'swc_shop_section',
            'settings'      => 'swc_product_columns',
            'type'     		=> 'select',
            'priority'		=> 7,
			'choices'  		=> array(
				'1'			=> '1',
				'2'			=> '2',
				'3' 		=> '3',
				'4'  		=> '4',
				'5'  		=> '5',
			),
        ) ) );

	    /**
	     * Products per Page
	     */
        $wp_customize->add_setting( 'swc_products_per_page', array(
	        'default'           => '12',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_products_per_page', array(
            'label'         => __( 'Products per page', 'storefront-woocommerce-customiser' ),
            'section'       => 'swc_shop_section',
            'settings'      => 'swc_products_per_page',
            'type'     		=> 'select',
            'priority'		=> 10,
            'choices'  		=> array(
				'1'			=> '1',
            	'2'			=> '2',
            	'3'			=> '3',
				'4'			=> '4',
				'5'			=> '5',
				'6' 		=> '6',
				'7'  		=> '7',
				'8'			=> '8',
				'9'			=> '9',
				'10'		=> '10',
				'11'		=> '11',
				'12'		=> '12',
				'13'		=> '13',
				'14'		=> '14',
				'15'		=> '15',
				'16'		=> '16',
				'17'		=> '17',
				'18'		=> '18',
				'19'		=> '19',
				'20'		=> '20',
				'21'		=> '21',
				'22'		=> '22',
				'23'		=> '23',
				'24'		=> '24',
			),
        ) ) );

        /**
         * Product Alignment
         */
	    $wp_customize->add_setting( 'swc_shop_alignment', array(
	        'default'           => 'center',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_shop_alignment', array(
            'label'         => __( 'Product alignment', 'storefront-woocommerce-customiser' ),
            'description'	=> __( 'Align product titles, prices, add to cart buttons, etc.', 'storefront-woocommerce-customiser' ),
            'section'       => 'swc_shop_section',
            'settings'      => 'swc_shop_alignment',
            'type'     		=> 'select',
            'priority'		=> 11,
			'choices'  		=> array(
				'center'			=> 'Center',
				'left'				=> 'Left',
				'right' 			=> 'Right',
			),
        ) ) );

	    $wp_customize->add_setting( 'swc_product_archive_results_count', array(
	        'default'           => true,
	    ) );

        $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_product_archive_results_count', array(
            'label'         => __( 'Display product results count', 'storefront-woocommerce-customiser' ),
            'description'	=> __( 'Toggle the display of the product results count.', 'storefront-woocommerce-customiser' ),
            'section'       => 'swc_shop_section',
            'settings'      => 'swc_product_archive_results_count',
            'type'          => 'checkbox',
            'priority'		=> 15,
        ) ) );

        $wp_customize->add_setting( 'swc_product_archive_sorting', array(
	        'default'           => true,
	    ) );

        $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_product_archive_sorting', array(
            'label'         => __( 'Display product sorting', 'storefront-woocommerce-customiser' ),
            'description'	=> __( 'Toggle the display of the product sorting dropdown.', 'storefront-woocommerce-customiser' ),
            'section'       => 'swc_shop_section',
            'settings'      => 'swc_product_archive_sorting',
            'type'          => 'checkbox',
            'priority'		=> 15,
        ) ) );

         $wp_customize->add_setting( 'swc_product_archive_image', array(
	        'default'           => true,
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_product_archive_image', array(
            'label'         => __( 'Display product image', 'storefront-woocommerce-customiser' ),
            'description'	=> __( 'Toggle the display of the product images.', 'storefront-woocommerce-customiser' ),
            'section'       => 'swc_shop_section',
            'settings'      => 'swc_product_archive_image',
            'type'          => 'checkbox',
            'priority'		=> 20,
        ) ) );

        $wp_customize->add_setting( 'swc_product_archive_title', array(
	        'default'           => true,
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_product_archive_title', array(
            'label'         => __( 'Display product title', 'storefront-woocommerce-customiser' ),
            'description'	=> __( 'Toggle the display of the product titles.', 'storefront-woocommerce-customiser' ),
            'section'       => 'swc_shop_section',
            'settings'      => 'swc_product_archive_title',
            'type'          => 'checkbox',
            'priority'		=> 30,
        ) ) );

        $wp_customize->add_setting( 'swc_product_archive_sale_flash', array(
	        'default'           => true,
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_product_archive_sale_flash', array(
            'label'         => __( 'Display sale flash', 'storefront-woocommerce-customiser' ),
            'description'	=> __( 'Toggle the display of the sale flashes.', 'storefront-woocommerce-customiser' ),
            'section'       => 'swc_shop_section',
            'settings'      => 'swc_product_archive_sale_flash',
            'type'          => 'checkbox',
            'priority'		=> 40,
        ) ) );

        $wp_customize->add_setting( 'swc_product_archive_rating', array(
	        'default'           => true,
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_product_archive_rating', array(
            'label'         => __( 'Display rating', 'storefront-woocommerce-customiser' ),
            'description'	=> __( 'Toggle the display of the product ratings.', 'storefront-woocommerce-customiser' ),
            'section'       => 'swc_shop_section',
            'settings'      => 'swc_product_archive_rating',
            'type'          => 'checkbox',
            'priority'		=> 50,
        ) ) );

        $wp_customize->add_setting( 'swc_product_archive_price', array(
	        'default'           => true,
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_product_archive_price', array(
            'label'         => __( 'Display price', 'storefront-woocommerce-customiser' ),
            'description'	=> __( 'Toggle the display of the product prices.', 'storefront-woocommerce-customiser' ),
            'section'       => 'swc_shop_section',
            'settings'      => 'swc_product_archive_price',
            'type'          => 'checkbox',
            'priority'		=> 60,
        ) ) );

        $wp_customize->add_setting( 'swc_product_archive_add_to_cart', array(
	        'default'       => true,
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_product_archive_add_to_cart', array(
            'label'         => __( 'Display add to cart button', 'storefront-woocommerce-customiser' ),
            'description'	=> __( 'Toggle the display of the add to cart buttons.', 'storefront-woocommerce-customiser' ),
            'section'       => 'swc_shop_section',
            'settings'      => 'swc_product_archive_add_to_cart',
            'type'          => 'checkbox',
            'priority'		=> 65,
        ) ) );

        $wp_customize->add_setting( 'swc_product_archive_description', array(
	        'default'       => false,
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_product_archive_description', array(
            'label'         => __( 'Display description', 'storefront-woocommerce-customiser' ),
            'description'	=> __( 'Toggle the display of the product description.', 'storefront-woocommerce-customiser' ),
            'section'       => 'swc_shop_section',
            'settings'      => 'swc_product_archive_description',
            'type'          => 'checkbox',
            'priority'		=> 70,
        ) ) );

        if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
	        $wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'swc_infinite_scroll_divider', array(
				'section'  	=> 'swc_shop_section',
				'type'		=> 'divider',
				'priority' 	=> 75,
			) ) );
	    }

        $wp_customize->add_setting( 'swc_infinite_scroll', array(
	        'default'           => false,
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_infinite_scroll', array(
            'label'         => __( 'Infinite scroll', 'storefront-woocommerce-customiser' ),
            'description'	=> __( 'Toggle the infinite scroll functionality.', 'storefront-woocommerce-customiser' ),
            'section'       => 'swc_shop_section',
            'settings'      => 'swc_infinite_scroll',
            'type'          => 'checkbox',
            'priority'		=> 80,
        ) ) );

        if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
	        $wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'swc_messages_divider', array(
				'section'  	=> 'swc_shop_section',
				'type'		=> 'divider',
				'priority' 	=> 85,
			) ) );
	    }

	    /**
		 * Message Colors
		 */
		$wp_customize->add_setting( 'swc_message_background_color', array(
			'default'           => apply_filters( 'swc_default_message_background_color', '#0f834d' ),
			'sanitize_callback' => 'sanitize_hex_color',
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'swc_message_background_color', array(
			'label'	   => __( 'Success message background color', 'storefront-woocommerce-customiser' ),
			'section'  => 'swc_shop_section',
			'settings' => 'swc_message_background_color',
			'priority' => 90,
		) ) );

		$wp_customize->add_setting( 'swc_message_text_color', array(
			'default'           => apply_filters( 'swc_default_message_text_color', '#ffffff' ),
			'sanitize_callback' => 'sanitize_hex_color',
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'swc_message_text_color', array(
			'label'	   => __( 'Success message text color', 'storefront-woocommerce-customiser' ),
			'section'  => 'swc_shop_section',
			'settings' => 'swc_message_text_color',
			'priority' => 100,
		) ) );

		if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
	        $wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'swc_message_divider', array(
				'section'  	=> 'swc_shop_section',
				'type'		=> 'divider',
				'priority' 	=> 105,
			) ) );
	    }

	    $wp_customize->add_setting( 'swc_info_background_color', array(
			'default'           => apply_filters( 'swc_default_info_background_color', '#3D9CD2' ),
			'sanitize_callback' => 'sanitize_hex_color',
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'swc_info_background_color', array(
			'label'	   => __( 'Info message background color', 'storefront-woocommerce-customiser' ),
			'section'  => 'swc_shop_section',
			'settings' => 'swc_info_background_color',
			'priority' => 110,
		) ) );

		$wp_customize->add_setting( 'swc_info_text_color', array(
			'default'           => apply_filters( 'swc_default_info_text_color', '#ffffff' ),
			'sanitize_callback' => 'sanitize_hex_color',
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'swc_info_text_color', array(
			'label'	   => __( 'Info message text color', 'storefront-woocommerce-customiser' ),
			'section'  => 'swc_shop_section',
			'settings' => 'swc_info_text_color',
			'priority' => 120,
		) ) );

		if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
	        $wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'swc_info_divider', array(
				'section'  	=> 'swc_shop_section',
				'type'		=> 'divider',
				'priority' 	=> 125,
			) ) );
	    }

	    $wp_customize->add_setting( 'swc_error_background_color', array(
			'default'           => apply_filters( 'swc_default_error_background_color', '#e2401c' ),
			'sanitize_callback' => 'sanitize_hex_color',
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'swc_error_background_color', array(
			'label'	   => __( 'Error message background color', 'storefront-woocommerce-customiser' ),
			'section'  => 'swc_shop_section',
			'settings' => 'swc_error_background_color',
			'priority' => 130,
		) ) );

		$wp_customize->add_setting( 'swc_error_text_color', array(
			'default'           => apply_filters( 'swc_default_error_text_color', '#ffffff' ),
			'sanitize_callback' => 'sanitize_hex_color',
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'swc_error_text_color', array(
			'label'	   => __( 'Error message text color', 'storefront-woocommerce-customiser' ),
			'section'  => 'swc_shop_section',
			'settings' => 'swc_error_text_color',
			'priority' => 140,
		) ) );

	    /**
	     * Product Details Section
	     */
        $wp_customize->add_section( 'swc_product_details_section' , array(
		    'title'      		=> __( 'Product Details', 'storefront-woocommerce-customiser' ),
		    'description' 		=> __( 'Customise the look & feel of your product details pages', 'storefront-woocommerce-customiser' ),
		    'priority'   		=> 56,
		    'active_callback'	=> 'is_product',
		) );

		/**
    	 * Product Layout
    	 */
        $wp_customize->add_setting( 'swc_product_layout', array(
	        'default'           => 'default',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_product_layout', array(
            'label'         => __( 'Layout', 'storefront-woocommerce-customiser' ),
            'description'	=> __( 'Applied to the product details page', 'storefront-woocommerce-customiser' ),
            'section'       => 'swc_product_details_section',
            'settings'      => 'swc_product_layout',
            'type'     		=> 'radio',
            'priority'		=> 5,
			'choices'  		=> array(
				'default'			=> 'Default',
				'full-width'		=> 'Full Width',
			),
        ) ) );

	    /**
	     * Product gallery layout
	     */
        $wp_customize->add_setting( 'swc_product_gallery_layout', array(
	        'default'           => 'default',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_product_gallery_layout', array(
            'label'         => __( 'Gallery layout', 'storefront-woocommerce-customiser' ),
            'section'       => 'swc_product_details_section',
            'settings'      => 'swc_product_gallery_layout',
            'type'     		=> 'select',
            'priority'		=> 10,
            'choices'  		=> array(
				'default'			=> 'Default',
            	'stacked'			=> 'Stacked',
            	'hidden'			=> 'Hide product galleries',
			),
        ) ) );

        /**
         * Toggle product tabs
         */
        $wp_customize->add_setting( 'swc_product_details_tab', array(
	        'default'           => true,
	    ) );

        $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_product_details_tab', array(
            'label'         => __( 'Display product tabs', 'storefront-woocommerce-customiser' ),
            'description'	=> __( 'Toggle the display of the product tabs.', 'storefront-woocommerce-customiser' ),
            'section'       => 'swc_product_details_section',
            'settings'      => 'swc_product_details_tab',
            'type'          => 'checkbox',
            'priority'		=> 20,
        ) ) );

        /**
         * Toggle related products
         */
        $wp_customize->add_setting( 'swc_related_products', array(
	        'default'           => true,
	    ) );

        $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_related_products', array(
            'label'         => __( 'Display related products', 'storefront-woocommerce-customiser' ),
            'description'	=> __( 'Toggle the display of related products.', 'storefront-woocommerce-customiser' ),
            'section'       => 'swc_product_details_section',
            'settings'      => 'swc_related_products',
            'type'          => 'checkbox',
            'priority'		=> 30,
        ) ) );

        /**
         * Toggle product description
         */
        $wp_customize->add_setting( 'swc_product_description', array(
	        'default'           => true,
	    ) );

        $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_product_description', array(
            'label'         => __( 'Display product description', 'storefront-woocommerce-customiser' ),
            'description'	=> __( 'Toggle the display of product description.', 'storefront-woocommerce-customiser' ),
            'section'       => 'swc_product_details_section',
            'settings'      => 'swc_product_description',
            'type'          => 'checkbox',
            'priority'		=> 40,
        ) ) );

        /**
         * Toggle product meta
         */
        $wp_customize->add_setting( 'swc_product_meta', array(
	        'default'           => true,
	    ) );

        $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_product_meta', array(
            'label'         => __( 'Display product meta', 'storefront-woocommerce-customiser' ),
            'description'	=> __( 'Toggle the display of product meta (category/sku).', 'storefront-woocommerce-customiser' ),
            'section'       => 'swc_product_details_section',
            'settings'      => 'swc_product_meta',
            'type'          => 'checkbox',
            'priority'		=> 50,
        ) ) );

        $wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'swc_product_info_divider', array(
			'section'  		=> 'swc_product_details_section',
			'type' 			=> 'divider',
			'priority' 		=> 55,
		) ) );

        $wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'swc_product_info', array(
			'section'  		=> 'swc_product_details_section',
			'type' 			=> 'text',
			'description'	=> __( 'Note: These settings are applied globally to all products in your store. If you\'d like to apply a different layout to this product only you can do so by visiting the Edit Product page in your dashboard.', 'storefront-woocommerce-customiser' ),
			'priority' 		=> 60,
		) ) );

        /**
	     * Homepage Section
	     */
	    $wp_customize->add_section( 'storefront_homepage' , array(
		    'title'      		=> __( 'Homepage', 'storefront' ),
		    'priority'   		=> 60,
		    'description' 		=> __( 'Customise the look & feel of the Storefront homepage template.', 'storefront' ),
		    'active_callback'	=> array( $this, 'swc_storefront_homepage_template_callback' ),
		) );

		/**
		 * Page Content Toggle
		 */
		$wp_customize->add_setting( 'swc_homepage_content', array(
	        'default'           => true,
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_content', array(
            'label'         => __( 'Display page content', 'storefront-woocommerce-customiser' ),
            'description'	=> __( 'Toggle the display of the page content.', 'storefront-woocommerce-customiser' ),
            'section'       => 'storefront_homepage',
            'settings'      => 'swc_homepage_content',
            'type'          => 'checkbox',
            'priority'		=> 10,
        ) ) );

        if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
	        $wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'swc_home_content_divider', array(
				'section'  	=> 'storefront_homepage',
				'type'		=> 'divider',
				'priority' 	=> 15,
			) ) );
	    }

		/**
		 * Product Category Toggle
		 */
		$wp_customize->add_setting( 'swc_homepage_categories', array(
	        'default'           => true,
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_categories', array(
            'label'         => __( 'Display product categories', 'storefront-woocommerce-customiser' ),
            'description'	=> __( 'Toggle the display of the product categories.', 'storefront-woocommerce-customiser' ),
            'section'       => 'storefront_homepage',
            'settings'      => 'swc_homepage_categories',
            'type'          => 'checkbox',
            'priority'		=> 20,
        ) ) );

        /**
         * Category Title
         */
	    $wp_customize->add_setting( 'swc_homepage_category_title', array(
	        'default'           => __( 'Product Categories', 'storefront-woocommerce-customiser' ),
	        'sanitize_callback' => 'sanitize_text_field',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_category_title', array(
            'label'         	=> __( 'Product category title', 'storefront-woocommerce-customiser' ),
            'section'       	=> 'storefront_homepage',
            'settings'      	=> 'swc_homepage_category_title',
            'type'     			=> 'text',
            'priority'			=> 22,
            'active_callback' 	=> array( $this, 'swc_product_category_callback' ),
        ) ) );

        /**
         * Category Description
         */
	    $wp_customize->add_setting( 'swc_homepage_category_description', array(
	        'default'           => __( '', 'storefront-woocommerce-customiser' ),
	        'sanitize_callback' => 'sanitize_text_field',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_category_description', array(
            'label'         	=> __( 'Product category description', 'storefront-woocommerce-customiser' ),
            'section'       	=> 'storefront_homepage',
            'settings'      	=> 'swc_homepage_category_description',
            'type'     			=> 'textarea',
            'priority'			=> 23,
            'active_callback' 	=> array( $this, 'swc_product_category_callback' ),
        ) ) );

        /**
         * Category Columns
         */
	    $wp_customize->add_setting( 'swc_homepage_category_columns', array(
	        'default'           => apply_filters( 'swc_homepage_category_columns_default', '3' ),
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_category_columns', array(
            'label'         	=> __( 'Product category columns', 'storefront-woocommerce-customiser' ),
            'section'       	=> 'storefront_homepage',
            'settings'      	=> 'swc_homepage_category_columns',
            'type'     			=> 'select',
            'priority'			=> 24,
            'active_callback' 	=> array( $this, 'swc_product_category_callback' ),
			'choices'  		=> array(
				'1'			=> '1',
				'2'			=> '2',
				'3' 		=> '3',
				'4'  		=> '4',
				'5'			=> '5',
			),
        ) ) );

	    /**
	     * Category limit
	     */
        $wp_customize->add_setting( 'swc_homepage_category_limit', array(
	        'default'           => apply_filters( 'swc_homepage_category_limit_default', '3' ),
	    ) );


	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_category_limit', array(
            'label'         	=> __( 'Product categories to display', 'storefront-woocommerce-customiser' ),
            'section'       	=> 'storefront_homepage',
            'settings'      	=> 'swc_homepage_category_limit',
            'type'     			=> 'select',
            'priority'			=> 26,
            'active_callback' 	=> array( $this, 'swc_product_category_callback' ),
            'choices'  		=> array(
            	'1'			=> '1',
            	'2'			=> '2',
            	'3'			=> '3',
				'4'			=> '4',
				'5'			=> '5',
				'6' 		=> '6',
				'7'  		=> '7',
				'8'			=> '8',
				'9'			=> '9',
				'10'		=> '10',
				'11'		=> '11',
				'12'		=> '12',
			),
        ) ) );

        /**
         * Category url
         */
	    $wp_customize->add_setting( 'swc_homepage_category_more_url', array(
	        'default'           => '',
	        'sanitize_callback' => 'esc_url_raw',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_category_more_url', array(
            'label'         	=> __( '"View more" url', 'storefront-woocommerce-customiser' ),
            'description'       => __( 'Add a url to append a "view more" button beneath product categories.', 'storefront-woocommerce-customiser' ),
            'section'       	=> 'storefront_homepage',
            'settings'      	=> 'swc_homepage_category_more_url',
            'type'     			=> 'url',
            'priority'			=> 27,
            'active_callback' 	=> array( $this, 'swc_product_category_callback' ),
        ) ) );

        if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
	        $wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'swc_home_product_cats_divider', array(
				'section'  	=> 'storefront_homepage',
				'type'		=> 'divider',
				'priority' 	=> 28,
			) ) );
	    }

        /**
		 * Recent Products Toggle
		 */
		$wp_customize->add_setting( 'swc_homepage_recent', array(
	        'default'           => true,
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_recent', array(
            'label'         => __( 'Display recent products', 'storefront-woocommerce-customiser' ),
            'description'	=> __( 'Toggle the display of the recent products.', 'storefront-woocommerce-customiser' ),
            'section'       => 'storefront_homepage',
            'settings'      => 'swc_homepage_recent',
            'type'          => 'checkbox',
            'priority'		=> 30,
        ) ) );

        /**
         * Recent Products Title
         */
	    $wp_customize->add_setting( 'swc_homepage_recent_products_title', array(
	        'default'           => __( 'Recent Products', 'storefront-woocommerce-customiser' ),
	        'sanitize_callback' => 'sanitize_text_field',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_recent_products_title', array(
            'label'         	=> __( 'Recent products title', 'storefront-woocommerce-customiser' ),
            'section'       	=> 'storefront_homepage',
            'settings'      	=> 'swc_homepage_recent_products_title',
            'type'     			=> 'text',
            'priority'			=> 32,
            'active_callback' 	=> array( $this, 'swc_recent_products_callback' ),
        ) ) );

        /**
         * Recent Products Description
         */
	    $wp_customize->add_setting( 'swc_homepage_recent_products_description', array(
	        'default'           => __( '', 'storefront-woocommerce-customiser' ),
	        'sanitize_callback' => 'sanitize_text_field',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_recent_products_description', array(
            'label'         	=> __( 'Recent products description', 'storefront-woocommerce-customiser' ),
            'section'       	=> 'storefront_homepage',
            'settings'      	=> 'swc_homepage_recent_products_description',
            'type'     			=> 'textarea',
            'priority'			=> 33,
            'active_callback' 	=> array( $this, 'swc_recent_products_callback' ),
        ) ) );

        /**
         * Recent Products Columns
         */
	    $wp_customize->add_setting( 'swc_homepage_recent_products_columns', array(
	        'default'           => apply_filters( 'swc_homepage_recent_products_columns_default', '4' ),
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_recent_products_columns', array(
            'label'         	=> __( 'Recent product columns', 'storefront-woocommerce-customiser' ),
            'section'       	=> 'storefront_homepage',
            'settings'      	=> 'swc_homepage_recent_products_columns',
            'type'     			=> 'select',
            'priority'			=> 34,
            'active_callback' 	=> array( $this, 'swc_recent_products_callback' ),
			'choices'  		=> array(
				'1'			=> '1',
				'2'			=> '2',
				'3' 		=> '3',
				'4'  		=> '4',
				'5'			=> '5',
			),
        ) ) );

	    /**
	     * Recent Products limit
	     */
        $wp_customize->add_setting( 'swc_homepage_recent_products_limit', array(
	        'default'           => apply_filters( 'swc_homepage_recent_products_limit_default', '4' ),
	    ) );


	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_recent_products_limit', array(
            'label'         	=> __( 'Recent products to display', 'storefront-woocommerce-customiser' ),
            'section'       	=> 'storefront_homepage',
            'settings'      	=> 'swc_homepage_recent_products_limit',
            'type'     			=> 'select',
            'active_callback' 	=> array( $this, 'swc_recent_products_callback' ),
            'priority'			=> 36,
            'choices'  		=> array(
            	'1'			=> '1',
            	'2'			=> '2',
            	'3'			=> '3',
				'4'			=> '4',
				'5'			=> '5',
				'6' 		=> '6',
				'7'  		=> '7',
				'8'			=> '8',
				'9'			=> '9',
				'10'		=> '10',
				'11'		=> '11',
				'12'		=> '12',
			),
        ) ) );

        /**
         * Recent products url
         */
	    $wp_customize->add_setting( 'swc_homepage_recent_products_more_url', array(
	        'default'           => '',
	        'sanitize_callback' => 'esc_url_raw',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_recent_products_more_url', array(
            'label'         	=> __( '"View more" url', 'storefront-woocommerce-customiser' ),
            'description'       => __( 'Add a url to append a "view more" button beneath recent products.', 'storefront-woocommerce-customiser' ),
            'section'       	=> 'storefront_homepage',
            'settings'      	=> 'swc_homepage_recent_products_more_url',
            'type'     			=> 'url',
            'priority'			=> 37,
            'active_callback' 	=> array( $this, 'swc_recent_products_callback' ),
        ) ) );

        if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
	        $wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'swc_home_recent_products_divider', array(
				'section'  	=> 'storefront_homepage',
				'type'		=> 'divider',
				'priority' 	=> 38,
			) ) );
	    }

        /**
		 * Featured Products Toggle
		 */
		$wp_customize->add_setting( 'swc_homepage_featured', array(
	        'default'           => true,
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_featured', array(
            'label'         => __( 'Display featured products', 'storefront-woocommerce-customiser' ),
            'description'	=> __( 'Toggle the display of the featured products.', 'storefront-woocommerce-customiser' ),
            'section'       => 'storefront_homepage',
            'settings'      => 'swc_homepage_featured',
            'type'          => 'checkbox',
            'priority'		=> 40,
        ) ) );

        /**
         * Featured Products Title
         */
	    $wp_customize->add_setting( 'swc_homepage_featured_products_title', array(
	        'default'           => __( 'Featured Products', 'storefront-woocommerce-customiser' ),
	        'sanitize_callback' => 'sanitize_text_field',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_featured_products_title', array(
            'label'         	=> __( 'Featured products title', 'storefront-woocommerce-customiser' ),
            'section'       	=> 'storefront_homepage',
            'settings'      	=> 'swc_homepage_featured_products_title',
            'type'     			=> 'text',
            'priority'			=> 42,
            'active_callback' 	=> array( $this, 'swc_featured_products_callback' ),
        ) ) );

        /**
         * Featured Products description
         */
	    $wp_customize->add_setting( 'swc_homepage_featured_products_description', array(
	        'default'           => __( '', 'storefront-woocommerce-customiser' ),
	        'sanitize_callback' => 'sanitize_text_field',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_featured_products_description', array(
            'label'         	=> __( 'Featured products description', 'storefront-woocommerce-customiser' ),
            'section'       	=> 'storefront_homepage',
            'settings'      	=> 'swc_homepage_featured_products_description',
            'type'     			=> 'textarea',
            'priority'			=> 43,
            'active_callback' 	=> array( $this, 'swc_featured_products_callback' ),
        ) ) );

        /**
         * Featured Products Columns
         */
	    $wp_customize->add_setting( 'swc_homepage_featured_products_columns', array(
	        'default'           => apply_filters( 'swc_homepage_featured_products_columns_default', '4' ),
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_featured_products_columns', array(
            'label'         	=> __( 'Featured products columns', 'storefront-woocommerce-customiser' ),
            'section'       	=> 'storefront_homepage',
            'settings'      	=> 'swc_homepage_featured_products_columns',
            'type'     			=> 'select',
            'priority'			=> 44,
            'active_callback' 	=> array( $this, 'swc_featured_products_callback' ),
			'choices'  		=> array(
				'1'			=> '1',
				'2'			=> '2',
				'3' 		=> '3',
				'4'  		=> '4',
				'5'  		=> '5',
			),
        ) ) );

	    /**
	     * Featured Products limit
	     */
        $wp_customize->add_setting( 'swc_homepage_featured_products_limit', array(
	        'default'           => apply_filters( 'swc_homepage_featured_products_limit_default', '4' ),
	    ) );


	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_featured_products_limit', array(
            'label'         	=> __( 'Featured products to display', 'storefront-woocommerce-customiser' ),
            'section'       	=> 'storefront_homepage',
            'settings'      	=> 'swc_homepage_featured_products_limit',
            'type'     			=> 'select',
            'priority'			=> 46,
            'active_callback' 	=> array( $this, 'swc_featured_products_callback' ),
            'choices'  		=> array(
            	'1'			=> '1',
            	'2'			=> '2',
            	'3'			=> '3',
				'4'			=> '4',
				'5'			=> '5',
				'6' 		=> '6',
				'7'  		=> '7',
				'8'			=> '8',
				'9'			=> '9',
				'10'		=> '10',
				'11'		=> '11',
				'12'		=> '12',
			),
        ) ) );

        /**
         * Featured products url
         */
	    $wp_customize->add_setting( 'swc_homepage_featured_products_more_url', array(
	        'default'           => '',
	        'sanitize_callback' => 'esc_url_raw',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_featured_products_more_url', array(
            'label'         	=> __( '"View more" url', 'storefront-woocommerce-customiser' ),
            'description'       => __( 'Add a url to append a "view more" button beneath featured products.', 'storefront-woocommerce-customiser' ),
            'section'       	=> 'storefront_homepage',
            'settings'      	=> 'swc_homepage_featured_products_more_url',
            'type'     			=> 'url',
            'priority'			=> 47,
            'active_callback' 	=> array( $this, 'swc_featured_products_callback' ),
        ) ) );

        if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
	        $wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'swc_home_featured_products_divider', array(
				'section'  	=> 'storefront_homepage',
				'type'		=> 'divider',
				'priority' 	=> 48,
			) ) );
	    }

        /**
		 * Top Rated Toggle
		 */
		$wp_customize->add_setting( 'swc_homepage_top_rated', array(
	        'default'           => true,
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_top_rated', array(
            'label'         => __( 'Display top rated products', 'storefront-woocommerce-customiser' ),
            'description'	=> __( 'Toggle the display of the top rated products.', 'storefront-woocommerce-customiser' ),
            'section'       => 'storefront_homepage',
            'settings'      => 'swc_homepage_top_rated',
            'type'          => 'checkbox',
            'priority'		=> 50,
        ) ) );

        /**
         * Top rated Products Title
         */
	    $wp_customize->add_setting( 'swc_homepage_top_rated_products_title', array(
	        'default'           => __( 'Top rated Products', 'storefront-woocommerce-customiser' ),
	        'sanitize_callback' => 'sanitize_text_field',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_top_rated_products_title', array(
            'label'         	=> __( 'Top rated products title', 'storefront-woocommerce-customiser' ),
            'section'       	=> 'storefront_homepage',
            'settings'      	=> 'swc_homepage_top_rated_products_title',
            'type'     			=> 'text',
            'priority'			=> 52,
            'active_callback' 	=> array( $this, 'swc_top_rated_products_callback' ),
        ) ) );

        /**
         * Top rated Products description
         */
        $wp_customize->add_setting( 'swc_homepage_top_rated_products_description', array(
            'default'           => __( '', 'storefront-woocommerce-customiser' ),
            'sanitize_callback' => 'sanitize_text_field',
        ) );

        $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_top_rated_products_description', array(
            'label'             => __( 'Top rated products description', 'storefront-woocommerce-customiser' ),
            'section'           => 'storefront_homepage',
            'settings'          => 'swc_homepage_top_rated_products_description',
            'type'              => 'textarea',
            'priority'          => 53,
            'active_callback'   => array( $this, 'swc_top_rated_products_callback' ),
        ) ) );

        /**
         * Top rated Products Columns
         */
	    $wp_customize->add_setting( 'swc_homepage_top_rated_products_columns', array(
	        'default'           => apply_filters( 'swc_homepage_top_rated_products_columns_default', '4' ),
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_top_rated_products_columns', array(
            'label'         	=> __( 'Top rated product columns', 'storefront-woocommerce-customiser' ),
            'section'       	=> 'storefront_homepage',
            'settings'      	=> 'swc_homepage_top_rated_products_columns',
            'type'     			=> 'select',
            'priority'			=> 54,
            'active_callback' 	=> array( $this, 'swc_top_rated_products_callback' ),
			'choices'  		=> array(
				'1'			=> '1',
				'2'			=> '2',
				'3' 		=> '3',
				'4'  		=> '4',
				'5'  		=> '5',
			),
        ) ) );

	    /**
	     * Top rated Products limit
	     */
        $wp_customize->add_setting( 'swc_homepage_top_rated_products_limit', array(
	        'default'           => apply_filters( 'swc_homepage_top_rated_products_limit_default', '4' ),
	    ) );


	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_top_rated_products_limit', array(
            'label'         	=> __( 'Top rated products to display', 'storefront-woocommerce-customiser' ),
            'section'       	=> 'storefront_homepage',
            'settings'      	=> 'swc_homepage_top_rated_products_limit',
            'type'     			=> 'select',
            'priority'			=> 56,
            'active_callback' 	=> array( $this, 'swc_top_rated_products_callback' ),
            'choices'  		=> array(
            	'1'			=> '1',
            	'2'			=> '2',
            	'3'			=> '3',
				'4'			=> '4',
				'5'			=> '5',
				'6' 		=> '6',
				'7'  		=> '7',
				'8'			=> '8',
				'9'			=> '9',
				'10'		=> '10',
				'11'		=> '11',
				'12'		=> '12',
			),
        ) ) );

        /**
         * Top rated products url
         */
	    $wp_customize->add_setting( 'swc_homepage_top_rated_products_more_url', array(
	        'default'           => '',
	        'sanitize_callback' => 'esc_url_raw',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_top_rated_products_more_url', array(
            'label'         	=> __( '"View more" url', 'storefront-woocommerce-customiser' ),
            'description'       => __( 'Add a url to append a "view more" button beneath top rated products.', 'storefront-woocommerce-customiser' ),
            'section'       	=> 'storefront_homepage',
            'settings'      	=> 'swc_homepage_top_rated_products_more_url',
            'type'     			=> 'url',
            'priority'			=> 57,
            'active_callback' 	=> array( $this, 'swc_top_rated_products_callback' ),
        ) ) );

        if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
	        $wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'swc_home_top_rated_products_divider', array(
				'section'  	=> 'storefront_homepage',
				'type'		=> 'divider',
				'priority' 	=> 58,
			) ) );
	    }

        /**
		 * On Sale Toggle
		 */
		$wp_customize->add_setting( 'swc_homepage_on_sale', array(
	        'default'           => true,
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_on_sale', array(
            'label'         => __( 'Display on sale products', 'storefront-woocommerce-customiser' ),
            'description'	=> __( 'Toggle the display of the on sale products.', 'storefront-woocommerce-customiser' ),
            'section'       => 'storefront_homepage',
            'settings'      => 'swc_homepage_on_sale',
            'type'          => 'checkbox',
            'priority'		=> 60,
        ) ) );

        /**
         * On sale Products Title
         */
	    $wp_customize->add_setting( 'swc_homepage_on_sale_products_title', array(
	        'default'           => __( 'On sale Products', 'storefront-woocommerce-customiser' ),
	        'sanitize_callback' => 'sanitize_text_field',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_on_sale_products_title', array(
            'label'         	=> __( 'On sale products title', 'storefront-woocommerce-customiser' ),
            'section'       	=> 'storefront_homepage',
            'settings'      	=> 'swc_homepage_on_sale_products_title',
            'type'     			=> 'text',
            'priority'			=> 62,
            'active_callback' 	=> array( $this, 'swc_on_sale_products_callback' ),
        ) ) );


        /**
         * On sale Products description
         */
        $wp_customize->add_setting( 'swc_homepage_on_sale_products_description', array(
            'default'           => __( '', 'storefront-woocommerce-customiser' ),
            'sanitize_callback' => 'sanitize_text_field',
        ) );

        $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_on_sale_products_description', array(
            'label'             => __( 'On sale products description', 'storefront-woocommerce-customiser' ),
            'section'           => 'storefront_homepage',
            'settings'          => 'swc_homepage_on_sale_products_description',
            'type'              => 'textarea',
            'priority'          => 63,
            'active_callback'   => array( $this, 'swc_on_sale_products_callback' ),
        ) ) );

        /**
         * On sale Products Columns
         */
	    $wp_customize->add_setting( 'swc_homepage_on_sale_products_columns', array(
	        'default'           => apply_filters( 'swc_homepage_on_sale_products_columns_default', '4' ),
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_on_sale_products_columns', array(
            'label'         	=> __( 'On sale product columns', 'storefront-woocommerce-customiser' ),
            'section'       	=> 'storefront_homepage',
            'settings'      	=> 'swc_homepage_on_sale_products_columns',
            'type'     			=> 'select',
            'priority'			=> 64,
            'active_callback' 	=> array( $this, 'swc_on_sale_products_callback' ),
			'choices'  		=> array(
				'1'			=> '1',
				'2'			=> '2',
				'3' 		=> '3',
				'4'  		=> '4',
				'5'  		=> '5',
			),
        ) ) );

	    /**
	     * On sale Products limit
	     */
        $wp_customize->add_setting( 'swc_homepage_on_sale_products_limit', array(
	        'default'           => apply_filters( 'swc_homepage_on_sale_products_limit_default', '4' ),
	    ) );


	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_on_sale_products_limit', array(
            'label'         	=> __( 'On sale products to display', 'storefront-woocommerce-customiser' ),
            'section'       	=> 'storefront_homepage',
            'settings'      	=> 'swc_homepage_on_sale_products_limit',
            'type'     			=> 'select',
            'priority'			=> 66,
            'active_callback' 	=> array( $this, 'swc_on_sale_products_callback' ),
            'choices'  		=> array(
            	'1'			=> '1',
            	'2'			=> '2',
            	'3'			=> '3',
				'4'			=> '4',
				'5'			=> '5',
				'6' 		=> '6',
				'7'  		=> '7',
				'8'			=> '8',
				'9'			=> '9',
				'10'		=> '10',
				'11'		=> '11',
				'12'		=> '12',
			),
        ) ) );

        /**
         * On sale products url
         */
	    $wp_customize->add_setting( 'swc_homepage_on_sale_products_more_url', array(
	        'default'           => '',
	        'sanitize_callback' => 'esc_url_raw',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_on_sale_products_more_url', array(
            'label'         	=> __( '"View more" url', 'storefront-woocommerce-customiser' ),
            'description'       => __( 'Add a url to append a "view more" button beneath on sale products.', 'storefront-woocommerce-customiser' ),
            'section'       	=> 'storefront_homepage',
            'settings'      	=> 'swc_homepage_on_sale_products_more_url',
            'type'     			=> 'url',
            'priority'			=> 67,
            'active_callback' 	=> array( $this, 'swc_on_sale_products_callback' ),
        ) ) );

		if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
	        $wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'swc_home_on_sale_divider', array(
				'section'  	=> 'storefront_homepage',
				'type'		=> 'divider',
				'priority' 	=> 68,
			) ) );
	    }

		/**
		 * Best Sellers Toggle
		 */
		$wp_customize->add_setting( 'swc_homepage_best_sellers', array(
	        'default'           => true,
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_best_sellers', array(
            'label'         => __( 'Display best selling products', 'storefront-woocommerce-customiser' ),
            'description'	=> __( 'Toggle the display of the best selling products.', 'storefront-woocommerce-customiser' ),
            'section'       => 'storefront_homepage',
            'settings'      => 'swc_homepage_best_sellers',
            'type'          => 'checkbox',
            'priority'		=> 70,
        ) ) );

        /**
         * Best Sellers Products Title
         */
	    $wp_customize->add_setting( 'swc_homepage_best_sellers_products_title', array(
	        'default'           => __( 'Best Sellers', 'storefront-woocommerce-customiser' ),
	        'sanitize_callback' => 'sanitize_text_field',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_best_sellers_products_title', array(
            'label'         	=> __( 'Best selling products title', 'storefront-woocommerce-customiser' ),
            'section'       	=> 'storefront_homepage',
            'settings'      	=> 'swc_homepage_best_sellers_products_title',
            'type'     			=> 'text',
            'priority'			=> 72,
            'active_callback' 	=> array( $this, 'swc_best_sellers_products_callback' ),
        ) ) );


        /**
         * Best Sellers Products description
         */
        $wp_customize->add_setting( 'swc_homepage_best_sellers_products_description', array(
            'default'           => __( '', 'storefront-woocommerce-customiser' ),
            'sanitize_callback' => 'sanitize_text_field',
        ) );

        $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_best_sellers_products_description', array(
            'label'             => __( 'Best selling products description', 'storefront-woocommerce-customiser' ),
            'section'           => 'storefront_homepage',
            'settings'          => 'swc_homepage_best_sellers_products_description',
            'type'              => 'textarea',
            'priority'          => 73,
            'active_callback'   => array( $this, 'swc_best_sellers_products_callback' ),
        ) ) );

        /**
         * Best Sellers Products Columns
         */
	    $wp_customize->add_setting( 'swc_homepage_best_sellers_products_columns', array(
	        'default'           => apply_filters( 'swc_homepage_best_sellers_products_columns_default', '4' ),
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_best_sellers_products_columns', array(
            'label'         	=> __( 'Best selling products columns', 'storefront-woocommerce-customiser' ),
            'section'       	=> 'storefront_homepage',
            'settings'      	=> 'swc_homepage_best_sellers_products_columns',
            'type'     			=> 'select',
            'priority'			=> 74,
            'active_callback' 	=> array( $this, 'swc_best_sellers_products_callback' ),
			'choices'  		=> array(
				'1'			=> '1',
				'2'			=> '2',
				'3' 		=> '3',
				'4'  		=> '4',
				'5'  		=> '5',
			),
        ) ) );

	    /**
	     * Best sellers Products limit
	     */
        $wp_customize->add_setting( 'swc_homepage_best_sellers_products_limit', array(
	        'default'           => apply_filters( 'swc_homepage_on_sale_products_limit_default', '4' ),
	    ) );


	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_best_sellers_products_limit', array(
            'label'         	=> __( 'Best selling products to display', 'storefront-woocommerce-customiser' ),
            'section'       	=> 'storefront_homepage',
            'settings'      	=> 'swc_homepage_best_sellers_products_limit',
            'type'     			=> 'select',
            'priority'			=> 76,
            'active_callback' 	=> array( $this, 'swc_best_sellers_products_callback' ),
            'choices'  		=> array(
            	'1'			=> '1',
            	'2'			=> '2',
            	'3'			=> '3',
				'4'			=> '4',
				'5'			=> '5',
				'6' 		=> '6',
				'7'  		=> '7',
				'8'			=> '8',
				'9'			=> '9',
				'10'		=> '10',
				'11'		=> '11',
				'12'		=> '12',
			),
        ) ) );

        /**
         * Best sellers products url
         */
	    $wp_customize->add_setting( 'swc_homepage_best_sellers_products_more_url', array(
	        'default'           => '',
	        'sanitize_callback' => 'esc_url_raw',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_best_sellers_products_more_url', array(
            'label'         	=> __( '"View more" url', 'storefront-woocommerce-customiser' ),
            'description'       => __( 'Add a url to append a "view more" button beneath best selling products.', 'storefront-woocommerce-customiser' ),
            'section'       	=> 'storefront_homepage',
            'settings'      	=> 'swc_homepage_best_sellers_products_more_url',
            'type'     			=> 'url',
            'priority'			=> 77,
            'active_callback' 	=> array( $this, 'swc_best_sellers_products_callback' ),
        ) ) );
	}

	/**
	 * Filter the homepage product categories
	 * @param  array $args the default args
	 * @return array $args the filtered args based on settings
	 */
	public function swc_product_category_args( $args ) {
		$title 					= get_theme_mod( 'swc_homepage_category_title', __( 'Product Categories', 'storefront-woocommerce-customiser' ) );
		$columns 				= get_theme_mod( 'swc_homepage_category_columns', '3' );
		$limit 					= get_theme_mod( 'swc_homepage_category_limit', '3' );

		$args['title']			= $title;
		$args['columns'] 		= $columns;
		$args['limit'] 			= $limit;

		return $args;
	}

	/**
	 * Filter the homepage recent product args
	 * @param  array $args the default args
	 * @return array $args the filtered args based on settings
	 */
	public function swc_recent_product_args( $args ) {
		$title 					= get_theme_mod( 'swc_homepage_recent_products_title', __( 'Recent Products', 'storefront-woocommerce-customiser' ) );
		$columns 				= get_theme_mod( 'swc_homepage_recent_products_columns', '4' );
		$limit 					= get_theme_mod( 'swc_homepage_recent_products_limit', '4' );

		$args['title']			= $title;
		$args['columns'] 		= $columns;
		$args['limit'] 			= $limit;

		return $args;
	}

	/**
	 * Filter the homepage featured product args
	 * @param  array $args the default args
	 * @return array $args the filtered args based on settings
	 */
	public function swc_featured_product_args( $args ) {
		$title 					= get_theme_mod( 'swc_homepage_featured_products_title', __( 'Featured Products', 'storefront-woocommerce-customiser' ) );
		$columns 				= get_theme_mod( 'swc_homepage_featured_products_columns', '4' );
		$limit 					= get_theme_mod( 'swc_homepage_featured_products_limit', '4' );

		$args['title']			= $title;
		$args['columns'] 		= $columns;
		$args['limit'] 			= $limit;

		return $args;
	}

	/**
	 * Filter the homepage popular product args
	 * @param  array $args the default args
	 * @return array $args the filtered args based on settings
	 */
	public function swc_popular_product_args( $args ) {
		$title 					= get_theme_mod( 'swc_homepage_top_rated_products_title', __( 'Top rated Products', 'storefront-woocommerce-customiser' ) );
		$columns 				= get_theme_mod( 'swc_homepage_top_rated_products_columns', '4' );
		$limit 					= get_theme_mod( 'swc_homepage_top_rated_products_limit', '4' );

		$args['title']			= $title;
		$args['columns'] 		= $columns;
		$args['limit'] 			= $limit;

		return $args;
	}

	/**
	 * Filter the homepage on sale product args
	 * @param  array $args the default args
	 * @return array $args the filtered args based on settings
	 */
	public function swc_on_sale_product_args( $args ) {
		$title 					= get_theme_mod( 'swc_homepage_on_sale_products_title', __( 'On sale Products', 'storefront-woocommerce-customiser' ) );
		$columns 				= get_theme_mod( 'swc_homepage_on_sale_products_columns', '4' );
		$limit 					= get_theme_mod( 'swc_homepage_on_sale_products_limit', '4' );

		$args['title']			= $title;
		$args['columns'] 		= $columns;
		$args['limit'] 			= $limit;

		return $args;
	}

	/**
	 * Filter the homepage best selling product args
	 * @param  array $args the default args
	 * @return array $args the filtered args based on settings
	 */
	public function swc_best_sellers_product_args( $args ) {
		$title 					= get_theme_mod( 'swc_homepage_best_sellers_products_title', __( 'On sale Products', 'storefront-woocommerce-customiser' ) );
		$columns 				= get_theme_mod( 'swc_homepage_best_sellers_products_columns', '4' );
		$limit 					= get_theme_mod( 'swc_homepage_best_sellers_products_limit', '4' );

		$args['title']			= $title;
		$args['columns'] 		= $columns;
		$args['limit'] 			= $limit;

		return $args;
	}

	/**
	 * Storefront WooCommerce Customiser Body Class
	 * @see get_theme_mod()
	 */
	public function swc_body_class( $classes ) {
		$shop_layout 	 		= get_theme_mod( 'swc_shop_layout', 'default' );
		$shop_alignment 	 	= get_theme_mod( 'swc_shop_alignment', 'center' );
		$product_layout 		= get_theme_mod( 'swc_product_layout', 'default' );
		$header_search 			= get_theme_mod( 'swc_header_search', true );
		$header_cart 			= get_theme_mod( 'swc_header_cart', true );
		$archive_titles 		= get_theme_mod( 'swc_product_archive_title', true );
		$product_gallery_layout = get_theme_mod( 'swc_product_gallery_layout', 'default' );

		if ( class_exists( 'WooCommerce' ) ) {

			if ( is_shop() || is_product_taxonomy() || is_product_category() || is_product_tag() ) {
				if ( 'full-width' == $shop_layout ) {
					$classes[] = 'storefront-full-width-content';
				}
			}

			if ( is_shop() || is_product_taxonomy() || is_product_category() || is_product_tag() || is_page_template( 'template-homepage.php' ) ) {
				if ( false == $archive_titles ) {
					$classes[] = 'swc-archive-hide-product-titles';
				}
			}

			if ( is_product() ) {
				if ( 'full-width' == $product_layout ) {
					$classes[] = 'storefront-full-width-content';
				}
			}

			if ( is_product() && 'hidden' == $product_gallery_layout ) {
				$classes[] = 'swc-product-gallery-hidden';
			}

			if ( is_product() && 'stacked' == $product_gallery_layout ) {
				$classes[] = 'swc-product-gallery-stacked';
			}

			$classes[] = 'swc-shop-alignment-' . $shop_alignment;

		}

		if ( false == $header_search ) {
			$classes[] = 'swc-header-no-search';
		}

		if ( false == $header_cart ) {
			$classes[] = 'swc-header-no-cart';
		}

		return $classes;
	}

	/**
	 * Shop columns
	 * @return integer shop columns
	 */
	public function swc_shop_columns( $columns ) {
		$columns = get_theme_mod( 'swc_product_columns', apply_filters( 'swc_product_columns_default', 3 ) );

		if ( $columns ) {
			return $columns;
		} else {
			return apply_filters( 'storefront_loop_columns', apply_filters( 'swc_product_columns_default', 3 ) );
		}
	}

	/**
	 * Product thumbnail layout
	 * Tweak the number of columns thumbnails are arranged into based on settings
	 */
	public function swc_product_thumbnails( $cols ) {
		$product_layout 	 	= get_theme_mod( 'swc_product_layout', 'default' );
		$product_gallery_layout = get_theme_mod( 'swc_product_gallery_layout', 'default' );

		$cols = 4;

		if ( 'full-width' == $product_layout && 'stacked' == $product_gallery_layout ) {
			$cols = 6;
		}

		if ( 'default' == $product_layout && 'stacked' == $product_gallery_layout ) {
			$cols = 3;
		}

		return $cols;
	}

	/**
	 * Shop Layout
	 * Tweaks the WooCommerce layout based on settings
	 */
	public function swc_shop_layout() {
		$shop_layout					= get_theme_mod( 'swc_shop_layout', 'default' );
		$archive_description			= get_theme_mod( 'swc_archive_description', 'default' );
		$product_layout					= get_theme_mod( 'swc_product_layout', 'default' );
		$header_search					= get_theme_mod( 'swc_header_search', true );
		$header_cart					= get_theme_mod( 'swc_header_cart', true );
		$homepage_content				= get_theme_mod( 'swc_homepage_content', true );
		$homepage_cats					= get_theme_mod( 'swc_homepage_categories', true );
		$homepage_recent				= get_theme_mod( 'swc_homepage_recent', true );
		$homepage_featured				= get_theme_mod( 'swc_homepage_featured', true );
		$homepage_top_rated				= get_theme_mod( 'swc_homepage_top_rated', true );
		$homepage_on_sale				= get_theme_mod( 'swc_homepage_on_sale', true );
		$homepage_best_sellers			= get_theme_mod( 'swc_homepage_best_sellers', true );
		$archive_results_count			= get_theme_mod( 'swc_product_archive_results_count', true );
		$archive_sorting				= get_theme_mod( 'swc_product_archive_sorting', true );
		$archive_image					= get_theme_mod( 'swc_product_archive_image', true );
		$archive_sale_flash				= get_theme_mod( 'swc_product_archive_sale_flash', true );
		$archive_rating					= get_theme_mod( 'swc_product_archive_rating', true );
		$archive_price					= get_theme_mod( 'swc_product_archive_price', true );
		$archive_add_to_cart			= get_theme_mod( 'swc_product_archive_add_to_cart', true );
		$archive_product_description	= get_theme_mod( 'swc_product_archive_description', false );
		$product_gallery_layout			= get_theme_mod( 'swc_product_gallery_layout', 'default' );
		$product_details_tabs			= get_theme_mod( 'swc_product_details_tab', true );
		$product_related				= get_theme_mod( 'swc_related_products', true );
		$product_meta					= get_theme_mod( 'swc_product_meta', true );
		$product_description			= get_theme_mod( 'swc_product_description', true );

		if ( class_exists( 'WooCommerce' ) ) {

			if ( is_shop() || is_product_taxonomy() || is_product_category() || is_product_tag() ) {
				if ( 'full-width' == $shop_layout ) {
					remove_action( 'storefront_sidebar', 'storefront_get_sidebar' );
				}
			}

			if ( is_product() ) {
				if ( 'hidden' == $product_gallery_layout ) {
					remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
				}

				if ( 'full-width' == $product_layout ) {
					remove_action( 'storefront_sidebar', 'storefront_get_sidebar' );
				}

				if ( false == $product_details_tabs ) {
					remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
				}

				if ( false == $product_related ) {
					remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
				}

				if ( false == $product_description ) {
					remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
				}

				if ( false == $product_meta ) {
					remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
				}
			}

			if ( 'beneath' == $archive_description ) {
				remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );
				remove_action( 'woocommerce_archive_description', 'woocommerce_product_archive_description', 10 );
				add_action( 'woocommerce_after_main_content', 'woocommerce_taxonomy_archive_description', 5 );
				add_action( 'woocommerce_after_main_content', 'woocommerce_product_archive_description', 5 );
			}
		}

		if ( false == $header_search ) {
			remove_action( 'storefront_header', 'storefront_product_search', 	40 );
		}

		if ( false == $header_cart ) {
			remove_action( 'storefront_header', 'storefront_header_cart', 		60 );
		}

		if ( false == $homepage_content ) {
			remove_action( 'homepage', 'storefront_homepage_content', 10 );
		}

		if ( false == $homepage_cats ) {
			remove_action( 'homepage', 'storefront_product_categories', 20 );
		}

		if ( false == $homepage_recent ) {
			remove_action( 'homepage', 'storefront_recent_products', 30 );
		}

		if ( false == $homepage_featured ) {
			remove_action( 'homepage', 'storefront_featured_products', 40 );
		}

		if ( false == $homepage_top_rated ) {
			remove_action( 'homepage', 'storefront_popular_products', 50 );
		}

		if ( false == $homepage_on_sale ) {
			remove_action( 'homepage', 'storefront_on_sale_products', 60 );
		}

		if ( false == $homepage_best_sellers ) {
			remove_action( 'homepage', 'storefront_best_selling_products', 70 );
		}

		if ( false == $archive_results_count ) {
			remove_action( 'woocommerce_after_shop_loop', 'woocommerce_result_count', 20 );
			remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
		}

		if ( false == $archive_sorting ) {
			remove_action( 'woocommerce_after_shop_loop', 'woocommerce_catalog_ordering', 10 );
			remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 10 );
		}

		if ( false == $archive_image ) {
			remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
		}

		if ( false == $archive_sale_flash ) {
			remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 6 );
		}

		if ( false == $archive_rating ) {
			remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
		}

		if ( false == $archive_price ) {
			remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
		}

		if ( false == $archive_add_to_cart ) {
			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
		}

		if ( true == $archive_product_description ) {
			add_action( 'woocommerce_after_shop_loop_item', array( $this, 'swc_loop_product_description' ), 6 );
		}
	}

	/**
	 * Shop products per page
	 * @return integer shop products per page
	 */
	public function swc_shop_products_per_page( $per_page ) {
		$per_page = get_theme_mod( 'swc_products_per_page', '12' );
		return $per_page;
	}

	/**
	 * Product loop wrap
	 * @return void
	 */
	public function swc_product_loop_wrap() {
		$columns = get_theme_mod( 'swc_product_columns', apply_filters( 'swc_product_columns_default', 3 ) );

		if ( is_shop() || is_product_taxonomy() || is_product_category() || is_product_tag() ) {
			echo '<div class="columns-' . $columns . '">';
		}
	}

	/**
	 * Product categories view more button
	 * @return void
	 */
	public function swc_homepage_product_categories_view_more() {
		$url = get_theme_mod( 'swc_homepage_category_more_url', '' );

		if ( '' != $url ) {
			echo '<p class="clearfix view-more"><a href="' . esc_url( $url ) . '" class="button alt alignright">' . __( 'View more product categories', 'storefront-woocommerce-customiser' ) . '</a></p>';
		}
	}

	/**
	 * Recent products view more button
	 * @return void
	 */
	public function swc_homepage_recent_products_view_more() {
		$url = get_theme_mod( 'swc_homepage_recent_products_more_url', '' );

		if ( '' != $url ) {
			echo '<p class="clearfix view-more"><a href="' . esc_url( $url ) . '" class="button alt alignright">' . __( 'View more new products', 'storefront-woocommerce-customiser' ) . '</a></p>';
		}
	}

	/**
	 * Featured products view more button
	 * @return void
	 */
	public function swc_homepage_featured_products_view_more() {
		$url = get_theme_mod( 'swc_homepage_featured_products_more_url', '' );

		if ( '' != $url ) {
			echo '<p class="clearfix view-more"><a href="' . esc_url( $url ) . '" class="button alt alignright">' . __( 'View more featured products', 'storefront-woocommerce-customiser' ) . '</a></p>';
		}
	}

	/**
	 * Top rated products view more button
	 * @return void
	 */
	public function swc_homepage_top_rated_products_view_more() {
		$url = get_theme_mod( 'swc_homepage_top_rated_products_more_url', '' );

		if ( '' != $url ) {
			echo '<p class="clearfix view-more"><a href="' . esc_url( $url ) . '" class="button alt alignright">' . __( 'View more popular products', 'storefront-woocommerce-customiser' ) . '</a></p>';
		}
	}

	/**
	 * On sale products view more button
	 * @return void
	 */
	public function swc_homepage_on_sale_products_view_more() {
		$url = get_theme_mod( 'swc_homepage_on_sale_products_more_url', '' );

		if ( '' != $url ) {
			echo '<p class="clearfix view-more"><a href="' . esc_url( $url ) . '" class="button alt alignright">' . __( 'View more products on sale', 'storefront-woocommerce-customiser' ) . '</a></p>';
		}
	}

	/**
	 * Best selling products view more button
	 * @return void
	 */
	public function swc_homepage_best_sellers_products_view_more() {
		$url = get_theme_mod( 'swc_homepage_best_sellers_products_more_url', '' );

		if ( '' != $url ) {
			echo '<p class="clearfix view-more"><a href="' . esc_url( $url ) . '" class="button alt alignright">' . __( 'View more best selling products', 'storefront-woocommerce-customiser' ) . '</a></p>';
		}
	}

	/**
	 * Product loop wrap
	 * @return void
	 */
	public function swc_product_loop_wrap_close() {
		if ( is_shop() || is_product_taxonomy() || is_product_category() || is_product_tag() ) {
			echo '</div>';
		}
	}

	public function is_homepage_control_activated() {
		if ( class_exists( 'Homepage_Control' ) ) { return true; } else { return false; }
	}

	/**
	 * Product category callback
	 * @return bool
	 */
	public function swc_product_category_callback( $control ) {
	    return $control->manager->get_setting( 'swc_homepage_categories' )->value() == 'true' ? true : false;
	}

	/**
	 * Recent products callback
	 * @return bool
	 */
	public function swc_recent_products_callback( $control ) {
		return $control->manager->get_setting( 'swc_homepage_recent' )->value() == 'true' ? true : false;
	}

	/**
	 * Featured products callback
	 * @return bool
	 */
	public function swc_featured_products_callback( $control ) {
		return $control->manager->get_setting( 'swc_homepage_featured' )->value() == 'true' ? true : false;
	}

	/**
	 * Top rated products callback
	 * @return bool
	 */
	public function swc_top_rated_products_callback( $control ) {
		return $control->manager->get_setting( 'swc_homepage_top_rated' )->value() == 'true' ? true : false;
	}

	/**
	 * On sale products callback
	 * @return bool
	 */
	public function swc_on_sale_products_callback( $control ) {
		return $control->manager->get_setting( 'swc_homepage_on_sale' )->value() == 'true' ? true : false;
	}

	/**
	 * Best sellers products callback
	 * @return bool
	 */
	public function swc_best_sellers_products_callback( $control ) {
		return $control->manager->get_setting( 'swc_homepage_best_sellers' )->value() == 'true' ? true : false;
	}

	/**
	 * Homepage callback
	 * @return bool
	 */
	public function swc_storefront_homepage_template_callback() {
		return is_page_template( 'template-homepage.php' ) ? true : false;
	}

	/**
	 * Product archive callback
	 * @return bool
	 */
	public function swc_storefront_shop_callback() {
		if ( is_shop() || is_product_taxonomy() || is_product_category() || is_product_tag() ) {
			return true;
		} else {
			return false;
		}
	}

	/* ---------------------------------- */
	/* Composite Products Integration
	/* -----------------------------------*/

	/**
	 * Number of component option columns when the Product Thumbnails setting is active
	 * @param  integer $cols
	 * @return integer
	 */
	public function swc_cp_component_options_loop_columns( $cols ) {
		$cols = get_theme_mod( 'swc_cp_component_options_loop_columns', '3' );
		return $cols;
	}

	/**
	 * Number of component options per page when the Product Thumbnails setting is active
	 * @param  integer $num
	 * @return integer
	 */
	public function swc_cp_component_options_per_page( $num_per_page ) {
		$num_per_page = get_theme_mod( 'swc_cp_component_options_per_page', '6' );
		return $num_per_page;
	}

	/**
	 * Max number of Review/Summary columns when a Multi-page layout is active
	 * @param  integer $num
	 * @return integer
	 */
	public function swc_cp_summary_max_columns( $max_cols ) {
		$max_cols = get_theme_mod( 'swc_cp_summary_max_columns', '6' );
		return $max_cols;
	}

	/**
	 * Enable/disable the toggle-box component view when a Single-page layout is active
	 * @param  boolean              $show_toggle
	 * @param  string               $component_id
	 * @param  WC_Product_Composite $product
	 * @return boolean
	 */
	public function swc_cp_component_toggled( $show_toggle, $component_id, $product ) {
		$show_toggle = get_theme_mod( 'swc_cp_component_toggled', 'progressive' );

		$style = $product->get_composite_layout_style();

		if ( $style === $show_toggle || $show_toggle === 'both' ) {
			return true;
		}

		return false;
	}

	/**
	 * Customizer Composite Products settings
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 */
	public function swc_cp_customize_register( $wp_customize ) {

		/**
	     * Composite Products section
	     */
        $wp_customize->add_section( 'swc_cp_section' , array(
			'title'       => __( 'Composite Products', 'storefront-woocommerce-customiser' ),
			'description' => __( 'Customise the look & feel of Composite product pages', 'storefront-woocommerce-customiser' ),
			'priority'    => 59,
		) );

        /**
         * Component Options (Product) Columns
         */
	    $wp_customize->add_setting( 'swc_cp_component_options_loop_columns', array(
	        'default'           => '3',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_cp_component_options_loop_columns', array(
			'label'       => __( 'Component options columns', 'storefront-woocommerce-customiser' ),
			'description' => sprintf( __( 'In effect when the %sProduct Thumbnails%s options style is active', 'storefront-woocommerce-customiser' ), '<strong>', '</strong>' ),
			'section'     => 'swc_cp_section',
			'settings'    => 'swc_cp_component_options_loop_columns',
			'type'        => 'select',
			'priority'    => 1,
			'choices'     => array(
				'1'           => '1',
				'2'           => '2',
				'3'           => '3',
				'4'           => '4',
				'5'           => '5',
			),
        ) ) );

        /**
         * Component Options per Page
         */
	    $wp_customize->add_setting( 'swc_cp_component_options_per_page', array(
	        'default'           => '6',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_cp_component_options_per_page', array(
			'label'       => __( 'Component options per page', 'storefront-woocommerce-customiser' ),
			'description' => sprintf( __( 'In effect when the %sProduct Thumbnails%s options style is active', 'storefront-woocommerce-customiser' ), '<strong>', '</strong>' ),
			'section'     => 'swc_cp_section',
			'settings'    => 'swc_cp_component_options_per_page',
			'type'        => 'select',
			'priority'    => 2,
			'choices'     => array(
				'1'           => '1',
				'2'           => '2',
				'3'           => '3',
				'4'           => '4',
				'5'           => '5',
				'6'           => '6',
				'7'           => '7',
				'8'           => '8',
				'9'           => '9',
				'10'          => '10',
				'11'          => '11',
				'12'          => '12',
				'13'          => '13',
				'14'          => '14',
				'15'          => '15',
				'16'          => '16',
				'17'          => '17',
				'18'          => '18',
				'19'          => '19',
				'20'          => '20',
				'21'          => '21',
				'22'          => '22',
				'23'          => '23',
				'24'          => '24',
			),
        ) ) );

        /**
         * Max columns in Summary/Review section
         */
	    $wp_customize->add_setting( 'swc_cp_summary_max_columns', array(
	        'default'           => '6',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_cp_summary_max_columns', array(
			'label'       => __( 'Max columns in Summary', 'storefront-woocommerce-customiser' ),
			'description' => sprintf( __( 'In effect when using the %1$sStepped%2$s or %1$sComponentized%2$s layout', 'storefront-woocommerce-customiser' ), '<strong>', '</strong>' ),
			'section'     => 'swc_cp_section',
			'settings'    => 'swc_cp_summary_max_columns',
			'type'        => 'select',
			'priority'    => 3,
			'choices'     => array(
				'1'           => '1',
				'2'           => '2',
				'3'           => '3',
				'4'           => '4',
				'5'           => '5',
				'6'           => '6',
				'7'           => '7',
				'8'           => '8',
			),
        ) ) );

        /**
         * Toggle Box
         */
	    $wp_customize->add_setting( 'swc_cp_component_toggled', array(
	        'default'           => 'progressive',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_cp_component_toggled', array(
			'label'       => __( 'Toggle-box view', 'storefront-woocommerce-customiser' ),
			'description' => __( 'Apply the toggle-box Component view to the following layout(s)', 'storefront-woocommerce-customiser' ),
			'section'     => 'swc_cp_section',
			'settings'    => 'swc_cp_component_toggled',
			'type'        => 'select',
			'priority'    => 5,
			'choices'     => array(
				'single'      => 'Stacked',
				'progressive' => 'Progressive',
				'both'        => 'Both',
				'none'        => 'None',
			),
        ) ) );

	}

	/**
	 * Add CSS in <head> for styles handled by the Customizer
	 *
	 * @since 1.0.0
	 */
	public function swc_add_customizer_css() {
		$success_bg_color 		= get_theme_mod( 'swc_message_background_color', apply_filters( 'swc_default_message_background_color', '#0f834d' ) );
		$success_text_color 	= get_theme_mod( 'swc_message_text_color', apply_filters( 'swc_default_message_text_color', '#ffffff' ) );
		$message_bg_color 		= get_theme_mod( 'swc_info_background_color', apply_filters( 'swc_default_info_background_color', '#3D9CD2' ) );
		$message_text_color 	= get_theme_mod( 'swc_info_text_color', apply_filters( 'swc_default_info_text_color', '#ffffff' ) );
		$error_bg_color 		= get_theme_mod( 'swc_error_background_color', apply_filters( 'swc_default_error_background_color', '#e2401c' ) );
		$error_text_color 		= get_theme_mod( 'swc_error_text_color', apply_filters( 'swc_default_error_text_color', '#ffffff' ) );


		$wc_style = '';

		$wc_style .= '
			.woocommerce-message {
				background-color: ' . $success_bg_color . ' !important;
				color: ' . $success_text_color . ' !important;
			}

			.woocommerce-message a,
			.woocommerce-message a:hover,
			.woocommerce-message .button,
			.woocommerce-message .button:hover {
				color: ' . $success_text_color . ' !important;
			}

			.woocommerce-info {
				background-color: ' . $message_bg_color . ' !important;
				color: ' . $message_text_color . ' !important;
			}

			.woocommerce-info a,
			.woocommerce-info a:hover,
			.woocommerce-info .button,
			.woocommerce-info .button:hover {
				color: ' . $message_text_color . ' !important;
			}

			.woocommerce-error {
				background-color: ' . $error_bg_color . ' !important;
				color: ' . $error_text_color . ' !important;
			}

			.woocommerce-error a,
			.woocommerce-error a:hover,
			.woocommerce-error .button,
			.woocommerce-error .button:hover {
				color: ' . $error_text_color . ' !important;
			}

		';

		wp_add_inline_style( 'storefront-woocommerce-style', $wc_style );
	}

	/**
	 * Infinite scroll wrapper
	 * @return void
	 */
	public function swc_scroll_wrapper() {
		$infinite_scroll = get_theme_mod( 'swc_infinite_scroll', false );

		if ( true == $infinite_scroll ) {
			echo '<div class="scroll-wrap">';
		}
	}

	/**
	 * Infinite scroll wrapper close
	 * @return void
	 */
	public function swc_scroll_wrapper_close() {
		$infinite_scroll = get_theme_mod( 'swc_infinite_scroll', false );

		if ( true == $infinite_scroll ) {
			echo '</div>';
		}
	}

	/**
	 * Loop product short description
	 * @return void
	 */
	public function swc_loop_product_description() {
		global $product;

		$short_description = apply_filters( 'woocommerce_short_description', $product->post->post_excerpt );

		if ( '' !== $short_description ) {
			echo '<div itemprop="description">' . $short_description . '</div>';
		}
	}

	/**
	 * Homepage Product Categories description.
	 * @return void
	 */
	public function swc_homepage_product_categories_description() {
		$description = get_theme_mod( 'swc_homepage_category_description', '' );

		if ( '' !== $description ) {
			echo '<div class="swc-section-description">' . wpautop( wptexturize( $description ) ) . '</div>';
		}
	}

	/**
	 * Homepage Recent Products description.
	 * @return void
	 */
	public function swc_homepage_recent_products_description() {
		$description = get_theme_mod( 'swc_homepage_recent_products_description', '' );

		if ( '' !== $description ) {
			echo '<div class="swc-section-description">' . wpautop( wptexturize( $description ) ) . '</div>';
		}
	}

	/**
	 * Homepage Featured Products description.
	 * @return void
	 */
	public function swc_homepage_featured_products_description() {
		$description = get_theme_mod( 'swc_homepage_featured_products_description', '' );

		if ( '' !== $description ) {
			echo '<div class="swc-section-description">' . wpautop( wptexturize( $description ) ) . '</div>';
		}
	}

	/**
	 * Homepage Popular Products description.
	 * @return void
	 */
	public function swc_homepage_popular_products_description() {
		$description = get_theme_mod( 'swc_homepage_top_rated_products_description', '' );

		if ( '' !== $description ) {
			echo '<div class="swc-section-description">' . wpautop( wptexturize( $description ) ) . '</div>';
		}
	}

	/**
	 * Homepage On Sale Products description.
	 * @return void
	 */
	public function swc_homepage_on_sale_products_description() {
		$description = get_theme_mod( 'swc_homepage_on_sale_products_description', '' );

		if ( '' !== $description ) {
			echo '<div class="swc-section-description">' . wpautop( wptexturize( $description ) ) . '</div>';
		}
	}

	/**
	 * Homepage Best selling Products description.
	 * @return void
	 */
	public function swc_homepage_best_sellers_products_description() {
		$description = get_theme_mod( 'swc_homepage_best_sellers_products_description', '' );

		if ( '' !== $description ) {
			echo '<div class="swc-section-description">' . wpautop( wptexturize( $description ) ) . '</div>';
		}
	}

	/**
	 * Adds a custom 'Storefront' tab to the product data box.
	 * @return void
	 */
	public function swc_custom_product_data_tab( $tabs ) {
		$tabs['storefront'] = array(
			'label'  => __( 'Storefront', 'storefront-woocommerce-customiser' ),
			'target' => 'storefront_data',
			'class'  => array(),
		);

		return $tabs;
	}

	/**
	 * Storefront Layout field: overrides the Customizer > Product Details > Layout option
	 * @return void
	 */
	public function swc_custom_product_data_panel() {
		global $post;
		?>
		<div id="storefront_data" class="panel woocommerce_options_panel">
			<div class="options_group sf_layout_group">
				<h3 style="margin:15px 0 20px 11px;;font-size:14px;"><?php _e( 'Storefront Layout Options', 'storefront-woocommerce-customiser' ); ?><img class="help_tip" data-tip="<?php echo sprintf( __( 'Use these options to fine tune the appearance of this product, overriding the current global Customizer configuration located at %sAppearance > Customize > Product Details%s.', 'storefront-woocommerce-customiser' ), '<strong>', '<strong>' ); ?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" width="16" height="16" /></h3>
				<?php

				$product_layout = get_post_meta( $post->ID, '_swc_sf_product_layout', true );

				// Storefront sidebar layout
				woocommerce_wp_select( array( 'id' => '_swc_sf_product_layout', 'value' => $product_layout, 'label' => __( 'Product Details', 'storefront-woocommerce-customiser' ), 'desc_tip' => true, 'description' => sprintf( __( 'Overrides the Layout option under %sAppearance > Customize > Product Details%s.', 'storefront-woocommerce-customiser' ), '<strong>', '<strong>' ), 'options' => array(
					''           => __( 'Override layout&hellip;', 'storefront-woocommerce-customiser' ),
					'default'    => __( 'Default', 'storefront-woocommerce-customiser' ),
					'full-width' => __( 'Full Width', 'storefront-woocommerce-customiser' ),
				) ) );

				$product_gallery_layout = get_post_meta( $post->ID, '_swc_sf_gallery_layout', true );

				// Storefront gallery layout
				woocommerce_wp_select( array( 'id' => '_swc_sf_gallery_layout', 'value' => $product_gallery_layout, 'label' => __( 'Gallery', 'storefront-woocommerce-customiser' ), 'desc_tip' => true, 'description' => sprintf( __( 'Overrides the Gallery Layout option under %sAppearance > Customize > Product Details%s.', 'storefront-woocommerce-customiser' ), '<strong>', '<strong>' ), 'options' => array(
					''        => __( 'Override layout&hellip;', 'storefront-woocommerce-customiser' ),
					'default' => __( 'Default', 'storefront-woocommerce-customiser' ),
					'stacked' => __( 'Stacked', 'storefront-woocommerce-customiser' ),
					'hide'    => __( 'Hide', 'storefront-woocommerce-customiser' ),
				) ) );

				// Storefront product tabs
				$product_tabs = get_post_meta( $post->ID, '_swc_sf_product_tabs', true );

				woocommerce_wp_select( array( 'id' => '_swc_sf_product_tabs', 'value' => $product_tabs, 'label' => __( 'Product tabs', 'storefront-woocommerce-customiser' ), 'desc_tip' => true, 'description' => sprintf( __( 'Overrides the Product Tabs option under %sAppearance > Customize > Product Details%s.', 'storefront-woocommerce-customiser' ), '<strong>', '<strong>' ), 'options' => array(
					''     => __( 'Override visibility&hellip;', 'storefront-woocommerce-customiser' ),
					'show' => __( 'Show', 'storefront-woocommerce-customiser' ),
					'hide' => __( 'Hide', 'storefront-woocommerce-customiser' ),
				) ) );

				// Storefront related products
				$product_related = get_post_meta( $post->ID, '_swc_sf_product_related', true );

				woocommerce_wp_select( array( 'id' => '_swc_sf_product_related', 'value' => $product_related, 'label' => __( 'Related products', 'storefront-woocommerce-customiser' ), 'desc_tip' => true, 'description' => sprintf( __( 'Overrides the Related Products option under %sAppearance > Customize > Product Details%s.', 'storefront-woocommerce-customiser' ), '<strong>', '<strong>' ), 'options' => array(
					''     => __( 'Override visibility&hellip;', 'storefront-woocommerce-customiser' ),
					'show' => __( 'Show', 'storefront-woocommerce-customiser' ),
					'hide' => __( 'Hide', 'storefront-woocommerce-customiser' ),
				) ) );

				// Storefront product description
				$product_description = get_post_meta( $post->ID, '_swc_sf_product_description', true );

				woocommerce_wp_select( array( 'id' => '_swc_sf_product_description', 'value' => $product_description, 'label' => __( 'Product description', 'storefront-woocommerce-customiser' ), 'desc_tip' => true, 'description' => sprintf( __( 'Overrides the Product Description option under %sAppearance > Customize > Product Details%s.', 'storefront-woocommerce-customiser' ), '<strong>', '<strong>' ), 'options' => array(
					''     => __( 'Override visibility&hellip;', 'storefront-woocommerce-customiser' ),
					'show' => __( 'Show', 'storefront-woocommerce-customiser' ),
					'hide' => __( 'Hide', 'storefront-woocommerce-customiser' ),
				) ) );

				// Storefront product meta
				$product_meta = get_post_meta( $post->ID, '_swc_sf_product_meta', true );

				woocommerce_wp_select( array( 'id' => '_swc_sf_product_meta', 'value' => $product_meta, 'label' => __( 'Product meta', 'storefront-woocommerce-customiser' ), 'desc_tip' => true, 'description' => sprintf( __( 'Overrides the Product Meta option under %sAppearance > Customize > Product Details%s.', 'storefront-woocommerce-customiser' ), '<strong>', '<strong>' ), 'options' => array(
					''     => __( 'Override visibility&hellip;', 'storefront-woocommerce-customiser' ),
					'show' => __( 'Show', 'storefront-woocommerce-customiser' ),
					'hide' => __( 'Hide', 'storefront-woocommerce-customiser' ),
				) ) );

				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Save Storefront Layout field
	 * @param  int   $post_id
	 * @return void
	 */
	public function swc_single_product_layout_override_admin_process( $post_id ) {
		$fields = array( '_swc_sf_product_layout', '_swc_sf_gallery_layout', '_swc_sf_product_tabs', '_swc_sf_product_related', '_swc_sf_product_description', '_swc_sf_product_meta' );
		foreach ( $fields as $field ) {
			if ( empty( $_POST[ $field ] ) ) {
				delete_post_meta( $post_id, $field );
			} else {
				update_post_meta( $post_id, $field, stripslashes( $_POST[ $field ] ) );
			}
		}
	}

	/**
	 * Override the body classes when viewing a product, based on our saved meta
	 * @param  array $classes
	 * @return array
	 */
	public function swc_single_body_class( $classes ) {

		global $post;

		if ( is_product() ) {

			$sf_layout         = get_post_meta( $post->ID, '_swc_sf_product_layout', true );
			$sf_gallery_layout = get_post_meta( $post->ID, '_swc_sf_gallery_layout', true );

			if ( $sf_layout ) {

				if ( $sf_layout === 'default' ) {

					$key = array_search( 'storefront-full-width-content', $classes );

					if ( false !== $key ) {
						unset( $classes[ $key ] );
					}
				} elseif ( $sf_layout === 'full-width' ) {

					$key = array_search( 'storefront-full-width-content', $classes );

					if ( false === $key ) {
						$classes[] = 'storefront-full-width-content';
					}
				}
			}

			if ( $sf_gallery_layout ) {
				if ( $sf_gallery_layout === 'default' ) {

					$key1 = array_search( 'swc-product-gallery-stacked', $classes );
					$key2 = array_search( 'swc-product-gallery-hidden', $classes );

					if ( false !== $key1 ) {
						unset( $classes[ $key1 ] );
					}

					if ( false !== $key2 ) {
						unset( $classes[ $key2 ] );
					}

				} elseif ( $sf_gallery_layout === 'stacked' ) {

					$key1 = array_search( 'swc-product-gallery-hidden', $classes );
					$key2 = array_search( 'swc-product-gallery-stacked', $classes );

					if ( false !== $key1 ) {
						unset( $classes[ $key1 ] );
					}

					if ( false === $key2 ) {
						$classes[] = 'swc-product-gallery-stacked';
					}

				} elseif ( $sf_gallery_layout === 'hide' ) {

					$key1 = array_search( 'swc-product-gallery-stacked', $classes );
					$key2 = array_search( 'swc-product-gallery-hidden', $classes );

					if ( false !== $key1 ) {
						unset( $classes[ $key1 ] );
					}

					if ( false === $key2 ) {
						$classes[] = 'swc-product-gallery-hidden';
					}
				}
			}
		}

		return $classes;
	}

	/**
	 * Remove sidebar on single products if layout is set to full width
	 * @param  array $classes
	 * @return array
	 */
	public function swc_single_product_layout_sidebar() {
		global $post;

		if ( is_product() ) {
			$sf_layout = get_post_meta( $post->ID, '_swc_sf_product_layout', true );

			if ( 'full-width' === $sf_layout ) {
				remove_action( 'storefront_sidebar', 'storefront_get_sidebar' );
			}

			// Gallery
			$sf_gallery_layout = get_post_meta( $post->ID, '_swc_sf_gallery_layout', true );

			if ( 'default' === $sf_gallery_layout || 'stacked' === $sf_gallery_layout ) {
				$product_gallery_layout	= get_theme_mod( 'swc_product_gallery_layout', 'default' );

				if ( 'hidden' === $product_gallery_layout ) {
					add_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );

				}
			} elseif ( 'hide' === $sf_gallery_layout ) {
				remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
			}

			// Product tabs
			$sf_product_tabs = get_post_meta( $post->ID, '_swc_sf_product_tabs', true );

			if ( 'show' === $sf_product_tabs ) {
				$product_details_tabs = get_theme_mod( 'swc_product_details_tab', true );

				if ( false === $product_details_tabs ) {
					add_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
				}
			} elseif ( 'hide' === $sf_product_tabs ) {
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
			}

			// Product Related
			$sf_product_related = get_post_meta( $post->ID, '_swc_sf_product_related', true );

			if ( 'show' === $sf_product_related ) {
				$product_related = get_theme_mod( 'swc_related_products', true );

				if ( false === $product_related ) {
					add_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
				}
			} elseif ( 'hide' === $sf_product_related ) {
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
			}

			// Product Descrition
			$sf_product_description = get_post_meta( $post->ID, '_swc_sf_product_description', true );

			if ( 'show' === $sf_product_description ) {
				$product_description = get_theme_mod( 'swc_product_description', true );

				if ( false === $product_description ) {
					add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
				}
			} elseif ( 'hide' === $sf_product_description ) {
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
			}

			// Product Meta
			$sf_product_meta = get_post_meta( $post->ID, '_swc_sf_product_meta', true );

			if ( 'show' === $sf_product_meta ) {
				$product_meta = get_theme_mod( 'swc_product_meta', true );

				if ( false === $product_meta ) {
					add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
				}
			} elseif ( 'hide' === $sf_product_meta ) {
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
			}
		}
	}
} // End Class
