<?php
/*
 * Plugin Name: Page Specific Menu Items
 * Plugin URI: http://www.wordpress.org/plugins
 * Description: This plugin allows you to select menu items page wise.
 * Version: 1.6.5
 * Author: Dharma Poudel (@rogercomred)
 * Author URI: https://www.twitter.com/rogercomred
 * Text Domain: page-specific-menu-items
 * Domain Path: /l10n
 */
 
//define some constants
if (!defined('PSMI_TEXTDOMAIN'))	define('PSMI_TEXTDOMAIN', 'page-specific-menu-items');
 
if(!class_exists('Page_Specific_Menu_Items')) {

	class Page_Specific_Menu_Items {
	
		/**
		 * some private variables
		**/
		private $metabox_htmlID = 'psmi_menu_items';		// HTML ID attribute of the metabox
		private $nonce = 'psmi-menu-items';					// Name of Nonce 
		private $psmi_defaults = array();					// Default setting values
		
		
		
		/**
		 * Constructor
		 */
		function __construct() {
			if(is_admin()) {	// Admin
				// Installation and uninstallation hooks
				register_activation_hook(__FILE__, array($this, 'psmi_install'));
				// Installation and uninstallation hooks
				register_deactivation_hook(__FILE__, array($this, 'psmi_uninstall'));

				add_action( 'admin_init', array( $this, 'psmi_init' ));
				add_action( 'admin_init', array( $this, 'psmi_page_init' ));
				add_action( 'add_meta_boxes', array( $this, 'psmi_add_meta_box' ));
				add_action( 'admin_menu', array( $this, 'psmi_add_page' ) );
				add_action( 'save_post', array( $this, 'psmi_save_menuitems') );
				
			}else {	// Frontend
			
				//add_action( 'wp_footer', array($this, 'psmi_hide_menuitems'));
				add_action( 'wp_head', array($this, 'psmi_hide_menuitems'));
				add_filter( 'wp_nav_menu_objects', array($this, 'psmi_add_menu_class'), 10, 2);
				
			}
			
		}
		
		
		
		/**
		* install 
		**/
		public static function psmi_install() {
			// do nothing for now
		}


				
		/**
		* uninstall 
		**/
		public static function psmi_uninstall() {
			// do nothing for now
			//delete_option('psmi_defaults');
		}


		/**
		* get the default values
		**/
		public  function get_psmi_defaults($menu_id ='') {
			
			$this->psmi_defaults = get_option( 'psmi_defaults' ) 
				? get_option( 'psmi_defaults' ) 
				: array('post_type'=>array('page'),'menu_id'=>$menu_id, 'items_defaultview' =>'show');

			//now persist the default options on database
			update_option('psmi_defaults', $this->psmi_defaults);
			return $this->psmi_defaults;
		}
		
		
		/**
		 *  initialization and localization
		**/
		function psmi_init() {

			$menu_id ='';
			$menus = wp_get_nav_menus();
			foreach ( $menus as $menu) {
				if (wp_get_nav_menu_items( $menu->term_id, array( 'update_post_term_cache' => false ) )) {
					$menu_id = $menu->term_id;
					break;
				}
			}
			
			$this->get_psmi_defaults($menu_id);

			if(function_exists('load_plugin_textdomain')) {
				load_plugin_textdomain(PSMI_TEXTDOMAIN, false, dirname(plugin_basename( __FILE__ )) . '/l10n/');
			}
			
		}

		
		/**
		 * adds plugin options page
		**/
		public function psmi_add_page() {
			$page_hook_suffix= add_options_page(
								'Settings Admin', 
								__('PS MenuItems', PSMI_TEXTDOMAIN), 
								'manage_options', 
								'psmi-setting-admin', 
								array( $this, 'psmi_create_admin_page' )
							);
			add_action('admin_enqueue_scripts', array($this, 'add_plugin_scripts'), 199);
		}
		
		/**
		 * adds styles and scripts
		**/
		public function add_plugin_scripts(){

        	wp_enqueue_script('psmi-script', plugins_url('/assets/script.js', __FILE__), array( 'jquery' ), '1.0', true);
        	wp_enqueue_style( 'psmi-style', plugins_url('/assets/style.css', __FILE__), array(), '1.0', 'all' );

        }
		
		
		/**
		 * prints html for plugin options page
		**/
		public function psmi_create_admin_page() {
		
			echo '<div class="wrap">'. screen_icon();
			echo __('<h2>Post Specific Menu Items Settings</h2>',  PSMI_TEXTDOMAIN);           
			echo '<form method="post" action="options.php">';
			// This prints out all hidden setting fields
			settings_fields( 'psmi_menuitems_group' );   
			do_settings_sections( 'psmi-setting-admin' );
			submit_button();
			echo  '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=75YPUFWTBMMNE" target="_blank" style="background: #E50914;border-radius: 3px;color: #fff;padding: 5px;text-decoration: none;margin-left: 150px;margin-top: -52px;display: block;width: 195px;">Enjoying plugin? Help developer</a>'; 
			echo '</form>';
			echo '</div>';
			
		}
		
		
		
		/**
		 * registers an adds the settings
		**/
		function psmi_page_init() {
			
			register_setting( 'psmi_menuitems_group',  'psmi_defaults' );

			add_settings_section(
				'psmi-settings', // ID
				' ', // Title
				array( $this, 'psmi_print_section_text' ), // Callback
				'psmi-setting-admin' // Page
			); 
 
			add_settings_field(
				'psmi-select-menu', // ID
				__('Select Menu', PSMI_TEXTDOMAIN), // Title 
				array( $this, 'psmi_select_menu_cb' ), // Callback
				'psmi-setting-admin', // Page
				'psmi-settings' // Section           
			); 

			add_settings_field(
				'psmi-posttype-checkbox', // ID
				__('Select Post Type', PSMI_TEXTDOMAIN), // Title 
				array( $this, 'psmi_posttype_checkbox_cb' ), // Callback
				'psmi-setting-admin', // Page
				'psmi-settings' // Section           
			);

			add_settings_field(
				'psmi-items-viewoptions', // ID
				__('Items default visibility', PSMI_TEXTDOMAIN), // Title 
				array( $this, 'psmi_items_viewoption_cb' ), // Callback
				'psmi-setting-admin', // Page
				'psmi-settings' // Section           
			);	
		}
		
		
		/**
		 * Prints the menu select box 
		**/	
		public function psmi_posttype_checkbox_cb() {
			$args = array( 'public'   => true,  '_builtin' => false );
			$custom_post_types = array_merge(array('post', 'page'), array_values(get_post_types($args, 'names')));
			echo '<ul>';
			foreach($custom_post_types as $cpt => $name){
				$checked = (!empty($this->psmi_defaults['post_type']) && $this->psmi_defaults['post_type'][0]!='' && in_array($name, $this->psmi_defaults['post_type'])) ? 'checked="checked"' :  '';
				
				echo '<li><input type="checkbox" style="margin:1px 5px 0;" '.$checked.' name="psmi_defaults[post_type][]" value="'.$name.'" />'. $name .'</li>';
			}
			echo '</ul>';
		}



		/** 
		 * Prints the Section text
		**/
		public function psmi_print_section_text() {
			//echo __('Select which menu you want to use :', PSMI_TEXTDOMAIN);
		}
		
		
		/**
		 * Prints the menu select box 
		**/	
		public function psmi_select_menu_cb() {

			$all_menus = wp_get_nav_menus();
			if($all_menus){
				echo "<select style='min-width:120px;' id='psmi_select_menu' name='psmi_defaults[menu_id]' >";
				$selected = ('' == $this->psmi_defaults['menu_id'])? 'selected="selected"' : '' ;
				echo "<option value='' {$selected} >".__('Select Menu', PSMI_TEXTDOMAIN)."</options>";
				foreach($all_menus as $menu){
					$selected = ($menu->term_id == $this->psmi_defaults['menu_id'])? 'selected="selected"' : '' ;
					printf('<option value="%s"  %s >%s</option>', $menu->term_id, $selected, $menu->name);
				}
				echo "</select>";
				
			}else{
				echo __('Unfortunately no menus are available. Create one?', PSMI_TEXTDOMAIN);
			}
		}

		/**
		 * Prints the menu select box 
		**/	
		public function psmi_items_viewoption_cb() {

			echo "<select style='min-width:120px;' id='psmi_items_viewoptions' name='psmi_defaults[items_defaultview]' >";
			$selected = ('' == $this->psmi_defaults['items_defaultview'])? 'selected="selected"' : '' ;
			$hide = ('hide' == $this->psmi_defaults['items_defaultview'])? 'selected="selected"' : '' ;
			$show = ('show' == $this->psmi_defaults['items_defaultview'])? 'selected="selected"' : '' ;
			printf('<option value="show"  %s >show all</option>', $show);
			printf('<option value="hide"  %s >hide all</option>', $hide);
			echo "</select>";
		}
		
		
		/**
		 * Adds meta box on page screen
		**/
		function psmi_add_meta_box(){
		
			foreach( $this->psmi_defaults['post_type'] as $post_type ) {
				add_meta_box(
					$this->metabox_htmlID,								// HTML id  attribute of the edit screen section
					__('Page Specific Menu Items', PSMI_TEXTDOMAIN),	// title of the edit screen section
					array( $this, 'psmi_display_menu_items' ), 			//callback function that prints html
					$post_type, 										// post type on which to show edit screen
					'side', 											// context - part of page where to show the edit screen
					'high'												// priority where the boxes should show
				);
			}
			
		}
		
		
		/**
		 * Prints html for meta box
		**/
		function psmi_display_menu_items(){
		
			global $post;
			$menu_object = wp_get_nav_menu_object( $this->psmi_defaults['menu_id'] );
			$menu_items = wp_get_nav_menu_items( $this->psmi_defaults['menu_id'] );
			// verify using nonce
			wp_nonce_field(plugin_basename( __FILE__ ), $this->nonce);
			echo "<div class='psmi-menucontainer'>";
			echo "<p><strong>".__('Current Menu: ',  PSMI_TEXTDOMAIN).$menu_object->name."</strong></p>";
			if ($menu_items) {
				$items_viewoption = ('show' == $this->psmi_defaults['items_defaultview']) ? 'hide' : 'show';
				_e("<p>Select menu items to $items_viewoption in this page. Top level menu items are marked bold.</p>", PSMI_TEXTDOMAIN);

				echo "<div class='bpwpc_select_row'>";
	            echo "<a href='#' class='select_all'>".__('select all', PSMI_TEXTDOMAIN)."</a>";
	            echo "<a href='#' class='deselect_all'>".__('unselect all', PSMI_TEXTDOMAIN)."</a>";
	            echo "<a href='#' class='invert_selection'>".__('invert selection', PSMI_TEXTDOMAIN)."</a>";
            	echo "</div>"; 
				
				$currentpage_items =get_post_meta($post->ID, PSMI_TEXTDOMAIN.'_currentpage_items', true);
				$menu_list = '<ul id="menu-' . $this->psmi_defaults['menu_id'] . '">';
				foreach ( $menu_items as $key => $menu_item ) {
					$checked = (!empty($currentpage_items) && $currentpage_items[0]!='' && in_array($menu_item->ID, $currentpage_items)) ? 'checked="checked"' :  '';
					$menu_list .= '<li><input type="checkbox" style="margin:1px 5px 0;" '.$checked.' name="currentpage_items[]" value="'.$menu_item->ID.'" />';
					if($menu_item->menu_item_parent ==0) $menu_list .= '<strong>';
					$menu_list .= '<a href="' . $menu_item->url . '">' . $menu_item->title . '</a></li>';
					if($menu_item->menu_item_parent ==0) $menu_list .= '</strong>';
				}
				$menu_list .= '</ul>';
				
			} else {
			
				$menu_list = __('<ul><li>Menu items not defined. Please add one.</li></ul>', PSMI_TEXTDOMAIN);
				
			}
			
			echo $menu_list;
			echo '<input type="hidden" value="" name="currentpage_items[]" />';
			echo "</div>";
			
		}
		
		
		/**
		 * saves post specific menu items when updating
		**/
		function psmi_save_menuitems(){
		
			global $post;
			
			if($post){
				if( !current_user_can('edit_page', $post->ID)) { return; }
				if(!wp_verify_nonce($_REQUEST[$this->nonce], plugin_basename(__FILE__))) { return; }
				
				if ( isset($_POST['currentpage_items'])) {
					update_post_meta($post->ID, PSMI_TEXTDOMAIN.'_currentpage_items', $_POST['currentpage_items']);
				}
			}
			
		}
		
		
		/**
		 * adds styles to the head of the page in frontend
		**/
		function psmi_hide_menuitems(){
		
			echo '<style type="text/css" media="screen">';
			echo '.menu-item.hide_this_item{ display:none !important; }';
			echo '</style>';
			
		}
		
		
		/**
		 * adds 'hide_this_item' class to each checked menu item
		**/
		function psmi_add_menu_class( $items , $args) {
			
			$currentpage_items = get_post_meta(get_queried_object_id(), PSMI_TEXTDOMAIN.'_currentpage_items', true);
			
			if (!empty($currentpage_items) && $currentpage_items[0] !=''){
				$psmi = Page_Specific_Menu_Items::get_psmi_defaults();

				foreach ( $items as $item ) {
					if ('show'== $psmi['items_defaultview'] && in_array( $item->ID, $currentpage_items ) ) {
						$item->classes[] = 'hide_this_item '; 
					}
					if ('hide'== $psmi['items_defaultview'] && !in_array( $item->ID, $currentpage_items ) ) {
						$item->classes[] = 'hide_this_item '; 
					}
				}
			}

			return $items; 
		}
		
	}

	new Page_Specific_Menu_Items();
	// Add the settings link to the plugins page
	function psmi_add_plugin_settings_link($links){
		$settings_link = '<a href="options-general.php?page=psmi-setting-admin">'.__('Settings', PSMI_TEXTDOMAIN).'</a>';
		array_unshift($links, $settings_link);
		return $links;
	}
	add_filter("plugin_action_links_".plugin_basename(__FILE__),'psmi_add_plugin_settings_link');
}