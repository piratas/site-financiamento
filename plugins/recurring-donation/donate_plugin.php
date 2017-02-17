<<<<<<< HEAD
<?php
/*
Plugin Name: Recurring Donations
Plugin URI: https://wp-ecommerce.net/ 
Description: Plugin for accepting recurring donations via shortcode
Author: wpecommerce
Version: 1.0.3
Author URI: https://wp-ecommerce.net/
License: GPLv2 or later
*/

// Initialization of the plugin function
if ( ! function_exists ( 'dntplgn_plugin_init' ) ) {
	function dntplgn_plugin_init() {
		global $dntplgn_options;
		// Internationalization, first(!)
		load_plugin_textdomain( 'donateplugin', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' ); 
		if ( ! is_admin() || ( is_admin() && isset( $_GET['page'] ) && $_GET['page'] == 'dntplgn_plugin' ) ) {
			dntplgn_register_settings();
		}
	}
}

// Adding admin plugin settings page function
if ( ! function_exists( 'add_dntplgn_admin_menu' ) ) {
	function add_dntplgn_admin_menu() {
		add_menu_page( __( 'Donate Plugin', 'donateplugin' ), __( 'Donate Plugin', 'donateplugin' ), 'manage_options', 'dntplgn_plugin', 'dntplgn_settings_page' );
		//call register settings function
	}
}

// Initialization plugin settings function
if ( ! function_exists( 'dntplgn_register_settings' ) ) {
	function dntplgn_register_settings() {
		global $wpdb, $dntplgn_options;
		$dntplgn_option_defaults = array(
			'dntplgn_paypal_email'       => ''
		);
		// install the option defaults
		if ( is_multisite() ) {
			if ( ! get_site_option( 'dntplgn_options' ) ) {
				add_site_option( 'dntplgn_options', $dntplgn_option_defaults, '', 'yes' );
			}
		} else {
			if ( ! get_option( 'dntplgn_options' ) )
				add_option( 'dntplgn_options', $dntplgn_option_defaults, '', 'yes' );
		}
		// get options from the database
		if ( is_multisite() )
			$dntplgn_options = get_site_option( 'dntplgn_options' ); // get options from the database
		else
			$dntplgn_options = get_option( 'dntplgn_options' );// get options from the database
		// array merge incase this version has added new options
		$dntplgn_options = array_merge( $dntplgn_option_defaults, $dntplgn_options );
		update_option( 'dntplgn_options', $dntplgn_options );
	}
}
// Admin plugin settings page content function
if ( ! function_exists( 'dntplgn_settings_page' ) ) {
	function dntplgn_settings_page() { 
		global $dntplgn_options;
		$message = '';
		if( isset( $_POST['dntplgn_submit'] ) && check_admin_referer( plugin_basename(__FILE__), 'dntplgn_nonce_name' ) ) {
			if ( isset( $_POST['dntplgn_paypal_account'] ) ) {
				if ( is_email( $_POST['dntplgn_paypal_account'] ) ) {
					$dntplgn_options['dntplgn_paypal_email'] = $_POST['dntplgn_paypal_account'];
				} else {
					$error_message = __( 'Email is incorrect', 'donateplugin' );
				}
			} 
			$message = __( 'Settings saved' , 'donateplugin' );
			update_option( 'dntplgn_options', $dntplgn_options );
		} ?>
		<div class="wrap">
			<h2><?php _e( 'Donate Plugin Settings', 'donateplugin' ); ?></h2>
			<?php if ( $message != '' && isset( $_POST['dntplgn_submit'] ) && is_email( $_POST['dntplgn_paypal_account'] ) ) { ?>
				<div class="updated fade">
					<p><strong><?php echo $message; ?></strong></p>
				</div>
			<?php } elseif ( '' != $error_message && ! is_email( $_POST['dntplgn_paypal_account'] ) ) { ?> 
				<div class="error">
					<p><strong><?php echo $error_message; ?></strong></p>
				</div>
			<?php } ?>
			<div class="dntplgn_description_shortcode_block">
				<p><?php _e( 'This shortcode you can insert on page or post or widget in your site', 'donateplugin' ); ?> - <code>[dntplgn]</code> <?php _e( 'or you can use shortcode with custom parametrs', 'donateplugin' ); ?> - <code>[dntplgn recurring_amt1="your_first_amount" recurring_amt2="your_second_amount" recurring_amt3="your_third_amount" item_name="Your item name"]</code></p>
				<p><?php _e( 'Default parametrs', 'donateplugin' ); ?>:</br >
				recurring_amt1="25"</br >
				recurring_amt2="50"</br >
				recurring_amt3="100"</br >
				item_name=""</p>
			</div>
			<form id="dntplgn_settings_form" method='post' action=''>
				<table id='dnt_noscript' class="form-table">
					<tr>
						<th class='dnt_row dnt_account_row' scope="row">
							<?php _e( 'Your paypal account email address:', 'donateplugin' ); ?>
						</th>
						<td class='dnt_account_row'>
							<input type='text' name='dntplgn_paypal_account' id='dntplgn_paypal_account' value="<?php if ( '' != $dntplgn_options['dntplgn_paypal_email'] ) echo $dntplgn_options['dntplgn_paypal_email']; ?>" />
							<input type='hidden' id='dnt_tab_paypal' name='dnt_tab_paypal' value='1' />
						</td>
					</tr>
				</table>
				<p class="submit">
					<input type='submit' name='dntplgn_submit' value='<?php _e( "Save changes", "donateplugin" ); ?>' class='button-primary' />
					<?php wp_nonce_field( plugin_basename( __FILE__ ), 'dntplgn_nonce_name' ); ?>
				</p>
			</form>					
		</div>
                <div style="background: none repeat scroll 0 0 #FFF6D5;border: 1px solid #D1B655;color: #3F2502;margin: 10px 0;padding: 5px 5px 5px 10px;text-shadow: 1px 1px #FFFFFF;">
                <p>If you need a robust method of accepting donations in WordPress, feel free to check out the <a href="https://www.tipsandtricks-hq.com/wordpress-estore-plugin-complete-solution-to-sell-digital-products-from-your-wordpress-blog-securely-1059?ap_id=wpecommerce" target="_blank">WP eStore Plugin</a></p>
                </div>
	<?php }
}

// Enqueue plugins scripts and styles function
if ( ! function_exists( 'dntplgn_enqueue_scripts' ) ) {
	function dntplgn_enqueue_scripts() {
		wp_enqueue_script( 'dntplgn_script', plugins_url( 'js/script.js' , __FILE__ ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-tabs' ) );
		wp_enqueue_style( 'dntplgn_style', plugins_url( 'css/style.css' , __FILE__ ) );
		wp_enqueue_style( 'jquery_ui_style', plugins_url( 'css/jquery-ui-styles.css' , __FILE__ ) );
	}
}

// Plugin form content function
if ( ! function_exists ( 'dntplgn_show_form' ) ) {
	function dntplgn_show_form( $atts ) { 
		global $dntplgn_options;
		$dntplgn_atts = shortcode_atts( array(
			'recurring_amt1' =>	'25',
			'recurring_amt2' =>	'50',
			'recurring_amt3' =>	'100',
			'item_name'		 =>	''
		), $atts );
		if ( isset( $_POST['dntplgn_monthly_submit_button'] ) ) {
			if ( "other" != esc_html( $_POST["monthly_donate_buttons"] ) ) {
				$monthly_amount = esc_html( $_POST["monthly_donate_buttons"] );
			} elseif ( "other" == esc_html( $_POST["monthly_donate_buttons"] )  && isset( $_POST['dntplgn_monthly_other_sum'] ) ) {
				$monthly_amount = esc_html( $_POST['dntplgn_monthly_other_sum'] );
			}
		} elseif ( isset( $_POST['dntplgn_once_submit_button'] ) ) {
			if ( isset( $_POST['dntplgn_once_amount'] ) ) {
				$once_amount = esc_html( $_POST['dntplgn_once_amount'] );
			}
		} 
		ob_start(); ?>
		<div id="tabs" class="dntplgn_form_wrapper">
			<ul>
				<li><a href="#tabs-1"><?php _e( 'mensal', 'donateplugin' ); ?></a></li>
				<li><a href="#tabs-2"><?php _e( 'única', 'donateplugin' ); ?></a></li>
			</ul>
			<div id="tabs-1">
				<!--Monthly donate form-->
				<form class="dntplgn_donate_monthly"  action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top" class="dntplgn_form">
					<input type="hidden" name="cmd" value="_xclick-subscriptions">
					<input type="hidden" name="business" value="<?php echo $dntplgn_options['dntplgn_paypal_email']; ?>" />
					<input type="hidden" name="lc" value="BR">
					<input type="hidden" name="item_name" value="<?php echo $dntplgn_atts['item_name']; ?>">
					<input type="hidden" name="no_note" value="1">
					<input type="hidden" name="src" value="1">
					<!-- Donate Amount -->
					<input id="first_button" type="radio" name="a3" checked="checked" value="<?php echo preg_replace("/[^0-9]/", '', $dntplgn_atts['recurring_amt1']); ?>" />
					<label for="first_button"> R$<?php echo preg_replace("/[^0-9]/", '', $dntplgn_atts['recurring_amt1']); ?> <span>(mensais)</span></label>
					<input id="second_button" type="radio" name="a3" value="<?php echo preg_replace("/[^0-9]/", '', $dntplgn_atts['recurring_amt2']); ?>" />
					<label for="second_button"> R$<?php echo preg_replace("/[^0-9]/", '', $dntplgn_atts['recurring_amt2']); ?> <span>(mensais)</span></label>
					<input id="third_button" type="radio" name="a3" value="<?php echo preg_replace("/[^0-9]/", '', $dntplgn_atts['recurring_amt3']); ?>" />
					<label for="third_button"> R$<?php echo preg_replace("/[^0-9]/", '', $dntplgn_atts['recurring_amt3']); ?> <span>(mensais)</span></label>
					<input id="fourth_button" type="radio" name="a3" value="other" />
					<label for="fourth_button"> <?php _e( 'Outro', 'donateplugin' ); ?> <span>(mensais)</span></label></br>
					<input class="dntplgn_monthly_other_sum" type="text" name="dntplgn_monthly_other_sum" />
					<!-- End Donate Amount -->
					<input type="hidden" name="p3" value="1">
					<input type="hidden" name="t3" value="M">
					<input type="hidden" name="currency_code" value="BRL">
					<input type="hidden" name="bn" value="TipsandTricks_SP">
					<input type="hidden" name="on0" value="contribuindo" />
					<input type="hidden" name="os0" value="mensalmente" />
					<input class="dntplgn_submit_button" type="submit" name="submit"  value="<?php _e( 'Contribuir', 'donateplugin' ); ?>" alt="PayPal - The safer, easier way to pay online!" />
					<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
				</form>
				<!--End monthly donate form-->
				<div class="clear"></div>
			</div>
			<div id="tabs-2">
				<!--Donate once only form-->
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
					<input type="hidden" name="cmd" value="_donations">
					<input type="hidden" name="business" value="<?php echo $dntplgn_options['dntplgn_paypal_email']; ?>">
					<input type="hidden" name="lc" value="BR">
					<input id="dntplgn_once_amount" type="text" name="amount" value="" />
					<input type="hidden" name="currency_code" value="BRL">
					<input type="hidden" name="no_note" value="0">
					<input type="hidden" name="bn" value="TipsandTricks_SP">
					<input class="dntplgn_submit_button" type="submit" name="submit"  value="<?php _e( 'Contribuir', 'donateplugin' ); ?>" alt="PayPal - The safer, easier way to pay online!" />
					<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
				</form>
				<!--End donate once only form-->
				<div class="clear"></div>
			</div>
		</div>
		<?php $content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
}

// Plugin delete function
if ( ! function_exists( 'dntplgn_delete_options' ) ) {
	function dntplgn_delete_options() {
		delete_option( 'dntplgn_options' );
	}
}

register_activation_hook( __FILE__, 'dntplgn_register_settings' );

add_action( 'init', 'dntplgn_plugin_init' );
add_action( 'admin_init', 'dntplgn_plugin_init' );
add_action( 'admin_menu', 'add_dntplgn_admin_menu' );
add_action( 'admin_enqueue_scripts', 'dntplgn_enqueue_scripts' );
add_action( 'wp_enqueue_scripts', 'dntplgn_enqueue_scripts' );
add_shortcode( 'dntplgn', 'dntplgn_show_form' );
add_filter( 'widget_text', 'do_shortcode' );

=======
<?php
/*
Plugin Name: Recurring Donations
Plugin URI: https://wp-ecommerce.net/ 
Description: Plugin for accepting recurring donations via shortcode
Author: wpecommerce
Version: 1.0.3
Author URI: https://wp-ecommerce.net/
License: GPLv2 or later
*/

// Initialization of the plugin function
if ( ! function_exists ( 'dntplgn_plugin_init' ) ) {
	function dntplgn_plugin_init() {
		global $dntplgn_options;
		// Internationalization, first(!)
		load_plugin_textdomain( 'donateplugin', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' ); 
		if ( ! is_admin() || ( is_admin() && isset( $_GET['page'] ) && $_GET['page'] == 'dntplgn_plugin' ) ) {
			dntplgn_register_settings();
		}
	}
}

// Adding admin plugin settings page function
if ( ! function_exists( 'add_dntplgn_admin_menu' ) ) {
	function add_dntplgn_admin_menu() {
		add_menu_page( __( 'Donate Plugin', 'donateplugin' ), __( 'Donate Plugin', 'donateplugin' ), 'manage_options', 'dntplgn_plugin', 'dntplgn_settings_page' );
		//call register settings function
	}
}

// Initialization plugin settings function
if ( ! function_exists( 'dntplgn_register_settings' ) ) {
	function dntplgn_register_settings() {
		global $wpdb, $dntplgn_options;
		$dntplgn_option_defaults = array(
			'dntplgn_paypal_email'       => ''
		);
		// install the option defaults
		if ( is_multisite() ) {
			if ( ! get_site_option( 'dntplgn_options' ) ) {
				add_site_option( 'dntplgn_options', $dntplgn_option_defaults, '', 'yes' );
			}
		} else {
			if ( ! get_option( 'dntplgn_options' ) )
				add_option( 'dntplgn_options', $dntplgn_option_defaults, '', 'yes' );
		}
		// get options from the database
		if ( is_multisite() )
			$dntplgn_options = get_site_option( 'dntplgn_options' ); // get options from the database
		else
			$dntplgn_options = get_option( 'dntplgn_options' );// get options from the database
		// array merge incase this version has added new options
		$dntplgn_options = array_merge( $dntplgn_option_defaults, $dntplgn_options );
		update_option( 'dntplgn_options', $dntplgn_options );
	}
}
// Admin plugin settings page content function
if ( ! function_exists( 'dntplgn_settings_page' ) ) {
	function dntplgn_settings_page() { 
		global $dntplgn_options;
		$message = '';
		if( isset( $_POST['dntplgn_submit'] ) && check_admin_referer( plugin_basename(__FILE__), 'dntplgn_nonce_name' ) ) {
			if ( isset( $_POST['dntplgn_paypal_account'] ) ) {
				if ( is_email( $_POST['dntplgn_paypal_account'] ) ) {
					$dntplgn_options['dntplgn_paypal_email'] = $_POST['dntplgn_paypal_account'];
				} else {
					$error_message = __( 'Email is incorrect', 'donateplugin' );
				}
			} 
			$message = __( 'Settings saved' , 'donateplugin' );
			update_option( 'dntplgn_options', $dntplgn_options );
		} ?>
		<div class="wrap">
			<h2><?php _e( 'Donate Plugin Settings', 'donateplugin' ); ?></h2>
			<?php if ( $message != '' && isset( $_POST['dntplgn_submit'] ) && is_email( $_POST['dntplgn_paypal_account'] ) ) { ?>
				<div class="updated fade">
					<p><strong><?php echo $message; ?></strong></p>
				</div>
			<?php } elseif ( '' != $error_message && ! is_email( $_POST['dntplgn_paypal_account'] ) ) { ?> 
				<div class="error">
					<p><strong><?php echo $error_message; ?></strong></p>
				</div>
			<?php } ?>
			<div class="dntplgn_description_shortcode_block">
				<p><?php _e( 'This shortcode you can insert on page or post or widget in your site', 'donateplugin' ); ?> - <code>[dntplgn]</code> <?php _e( 'or you can use shortcode with custom parametrs', 'donateplugin' ); ?> - <code>[dntplgn recurring_amt1="your_first_amount" recurring_amt2="your_second_amount" recurring_amt3="your_third_amount" item_name="Your item name"]</code></p>
				<p><?php _e( 'Default parametrs', 'donateplugin' ); ?>:</br >
				recurring_amt1="25"</br >
				recurring_amt2="50"</br >
				recurring_amt3="100"</br >
				item_name=""</p>
			</div>
			<form id="dntplgn_settings_form" method='post' action=''>
				<table id='dnt_noscript' class="form-table">
					<tr>
						<th class='dnt_row dnt_account_row' scope="row">
							<?php _e( 'Your paypal account email address:', 'donateplugin' ); ?>
						</th>
						<td class='dnt_account_row'>
							<input type='text' name='dntplgn_paypal_account' id='dntplgn_paypal_account' value="<?php if ( '' != $dntplgn_options['dntplgn_paypal_email'] ) echo $dntplgn_options['dntplgn_paypal_email']; ?>" />
							<input type='hidden' id='dnt_tab_paypal' name='dnt_tab_paypal' value='1' />
						</td>
					</tr>
				</table>
				<p class="submit">
					<input type='submit' name='dntplgn_submit' value='<?php _e( "Save changes", "donateplugin" ); ?>' class='button-primary' />
					<?php wp_nonce_field( plugin_basename( __FILE__ ), 'dntplgn_nonce_name' ); ?>
				</p>
			</form>					
		</div>
                <div style="background: none repeat scroll 0 0 #FFF6D5;border: 1px solid #D1B655;color: #3F2502;margin: 10px 0;padding: 5px 5px 5px 10px;text-shadow: 1px 1px #FFFFFF;">
                <p>If you need a robust method of accepting donations in WordPress, feel free to check out the <a href="https://www.tipsandtricks-hq.com/wordpress-estore-plugin-complete-solution-to-sell-digital-products-from-your-wordpress-blog-securely-1059?ap_id=wpecommerce" target="_blank">WP eStore Plugin</a></p>
                </div>
	<?php }
}

// Enqueue plugins scripts and styles function
if ( ! function_exists( 'dntplgn_enqueue_scripts' ) ) {
	function dntplgn_enqueue_scripts() {
		wp_enqueue_script( 'dntplgn_script', plugins_url( 'js/script.js' , __FILE__ ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-tabs' ) );
		wp_enqueue_style( 'dntplgn_style', plugins_url( 'css/style.css' , __FILE__ ) );
		wp_enqueue_style( 'jquery_ui_style', plugins_url( 'css/jquery-ui-styles.css' , __FILE__ ) );
	}
}

// Plugin form content function
if ( ! function_exists ( 'dntplgn_show_form' ) ) {
	function dntplgn_show_form( $atts ) { 
		global $dntplgn_options;
		$dntplgn_atts = shortcode_atts( array(
			'recurring_amt1' =>	'25',
			'recurring_amt2' =>	'50',
			'recurring_amt3' =>	'100',
			'item_name'		 =>	''
		), $atts );
		if ( isset( $_POST['dntplgn_monthly_submit_button'] ) ) {
			if ( "other" != esc_html( $_POST["monthly_donate_buttons"] ) ) {
				$monthly_amount = esc_html( $_POST["monthly_donate_buttons"] );
			} elseif ( "other" == esc_html( $_POST["monthly_donate_buttons"] )  && isset( $_POST['dntplgn_monthly_other_sum'] ) ) {
				$monthly_amount = esc_html( $_POST['dntplgn_monthly_other_sum'] );
			}
		} elseif ( isset( $_POST['dntplgn_once_submit_button'] ) ) {
			if ( isset( $_POST['dntplgn_once_amount'] ) ) {
				$once_amount = esc_html( $_POST['dntplgn_once_amount'] );
			}
		} 
		ob_start(); ?>
		<div id="tabs" class="dntplgn_form_wrapper">
			<ul>
				<li><a href="#tabs-1"><?php _e( 'mensal', 'donateplugin' ); ?></a></li>
				<li><a href="#tabs-2"><?php _e( 'única', 'donateplugin' ); ?></a></li>
			</ul>
			<div id="tabs-1">
				<!--Monthly donate form-->
				<form class="dntplgn_donate_monthly"  action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top" class="dntplgn_form">
					<input type="hidden" name="cmd" value="_xclick-subscriptions">
					<input type="hidden" name="business" value="<?php echo $dntplgn_options['dntplgn_paypal_email']; ?>" />
					<input type="hidden" name="lc" value="BR">
					<input type="hidden" name="item_name" value="<?php echo $dntplgn_atts['item_name']; ?>">
					<input type="hidden" name="no_note" value="1">
					<input type="hidden" name="src" value="1">
					<!-- Donate Amount -->
					<input id="first_button" type="radio" name="a3" checked="checked" value="<?php echo preg_replace("/[^0-9]/", '', $dntplgn_atts['recurring_amt1']); ?>" />
					<label for="first_button"> R$<?php echo preg_replace("/[^0-9]/", '', $dntplgn_atts['recurring_amt1']); ?> <span>(mensais)</span></label>
					<input id="second_button" type="radio" name="a3" value="<?php echo preg_replace("/[^0-9]/", '', $dntplgn_atts['recurring_amt2']); ?>" />
					<label for="second_button"> R$<?php echo preg_replace("/[^0-9]/", '', $dntplgn_atts['recurring_amt2']); ?> <span>(mensais)</span></label>
					<input id="third_button" type="radio" name="a3" value="<?php echo preg_replace("/[^0-9]/", '', $dntplgn_atts['recurring_amt3']); ?>" />
					<label for="third_button"> R$<?php echo preg_replace("/[^0-9]/", '', $dntplgn_atts['recurring_amt3']); ?> <span>(mensais)</span></label>
					<input id="fourth_button" type="radio" name="a3" value="other" />
					<label for="fourth_button"> <?php _e( 'Outro', 'donateplugin' ); ?> <span>(mensais)</span></label></br>
					<input class="dntplgn_monthly_other_sum" type="text" name="dntplgn_monthly_other_sum" />
					<!-- End Donate Amount -->
					<input type="hidden" name="p3" value="1">
					<input type="hidden" name="t3" value="M">
					<input type="hidden" name="currency_code" value="BRL">
					<input type="hidden" name="bn" value="TipsandTricks_SP">
					<input type="hidden" name="on0" value="contribuindo" />
					<input type="hidden" name="os0" value="mensalmente" />
					<input class="dntplgn_submit_button" type="submit" name="submit"  value="<?php _e( 'Contribuir', 'donateplugin' ); ?>" alt="PayPal - The safer, easier way to pay online!" />
					<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
				</form>
				<!--End monthly donate form-->
				<div class="clear"></div>
			</div>
			<div id="tabs-2">
				<!--Donate once only form-->
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
					<input type="hidden" name="cmd" value="_donations">
					<input type="hidden" name="business" value="<?php echo $dntplgn_options['dntplgn_paypal_email']; ?>">
					<input type="hidden" name="lc" value="BR">
					<input id="dntplgn_once_amount" type="text" name="amount" value="" />
					<input type="hidden" name="currency_code" value="BRL">
					<input type="hidden" name="no_note" value="0">
					<input type="hidden" name="bn" value="TipsandTricks_SP">
					<input class="dntplgn_submit_button" type="submit" name="submit"  value="<?php _e( 'Contribuir', 'donateplugin' ); ?>" alt="PayPal - The safer, easier way to pay online!" />
					<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
				</form>
				<!--End donate once only form-->
				<div class="clear"></div>
			</div>
		</div>
		<?php $content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
}

// Plugin delete function
if ( ! function_exists( 'dntplgn_delete_options' ) ) {
	function dntplgn_delete_options() {
		delete_option( 'dntplgn_options' );
	}
}

register_activation_hook( __FILE__, 'dntplgn_register_settings' );

add_action( 'init', 'dntplgn_plugin_init' );
add_action( 'admin_init', 'dntplgn_plugin_init' );
add_action( 'admin_menu', 'add_dntplgn_admin_menu' );
add_action( 'admin_enqueue_scripts', 'dntplgn_enqueue_scripts' );
add_action( 'wp_enqueue_scripts', 'dntplgn_enqueue_scripts' );
add_shortcode( 'dntplgn', 'dntplgn_show_form' );
add_filter( 'widget_text', 'do_shortcode' );

>>>>>>> atualiza plugins e temas
register_uninstall_hook( __FILE__, 'dntplgn_delete_options' ); ?>