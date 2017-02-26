<?php
/**
 * NYP WC 2.3 Compatibility Functions
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 
function wc_nyp_get_product( $the_product = false, $args = array() ) {
	return wc_get_product( $the_product, $args );
}