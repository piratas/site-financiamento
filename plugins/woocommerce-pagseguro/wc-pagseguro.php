<?php
/**
 * Fix main file for old installations.
 *
 * @package WooCommerce_PagSeguro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Update the main file.
$active_plugins = get_option( 'active_plugins', array() );

foreach ( $active_plugins as $key => $active_plugin ) {
	if ( strstr( $active_plugin, '/wc-pagseguro.php' ) ) {
		$active_plugins[ $key ] = str_replace( '/wc-pagseguro.php', '/woocommerce-pagseguro.php', $active_plugin );
	}
}

update_option( 'active_plugins', $active_plugins );
