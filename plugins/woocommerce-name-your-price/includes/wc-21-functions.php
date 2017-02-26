<?php
/**
 * NYP WC 2.1 Compatibility Functions
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function wc_nyp_get_product( $the_product = false, $args = array() ) {
	return get_product( $the_product, $args );
}