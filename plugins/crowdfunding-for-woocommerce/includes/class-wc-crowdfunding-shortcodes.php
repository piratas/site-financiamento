<?php
/**
 * Crowdfunding for WooCommerce - Shortcodes
 *
 * @version 2.3.2
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Crowdfunding_Shortcodes' ) ) :

class Alg_WC_Crowdfunding_Shortcodes {

	/**
	 * Constructor.
	 *
	 * @version 2.3.2
	 */
	function __construct() {
		add_shortcode( 'product_crowdfunding_total_backers',                       array( $this, 'alg_product_crowdfunding_total_backers' ) );
		add_shortcode( 'product_crowdfunding_total_sum',                           array( $this, 'alg_product_crowdfunding_total_sum' ) );
		add_shortcode( 'product_crowdfunding_total_items',                         array( $this, 'alg_product_crowdfunding_total_items' ) );
		add_shortcode( 'product_crowdfunding_goal',                                array( $this, 'alg_product_crowdfunding_goal' ) );
		add_shortcode( 'product_crowdfunding_goal_backers',                        array( $this, 'alg_product_crowdfunding_goal_backers' ) );
		add_shortcode( 'product_crowdfunding_goal_items',                          array( $this, 'alg_product_crowdfunding_goal_items' ) );
		add_shortcode( 'product_crowdfunding_goal_remaining',                      array( $this, 'alg_product_crowdfunding_goal_remaining' ) );
		add_shortcode( 'product_crowdfunding_goal_backers_remaining',              array( $this, 'alg_product_crowdfunding_goal_backers_remaining' ) );
		add_shortcode( 'product_crowdfunding_goal_items_remaining',                array( $this, 'alg_product_crowdfunding_goal_items_remaining' ) );
		add_shortcode( 'product_crowdfunding_startdate',                           array( $this, 'alg_product_crowdfunding_startdate' ) );
		add_shortcode( 'product_crowdfunding_starttime',                           array( $this, 'alg_product_crowdfunding_starttime' ) );
		add_shortcode( 'product_crowdfunding_startdatetime',                       array( $this, 'alg_product_crowdfunding_startdatetime' ) );
		add_shortcode( 'product_crowdfunding_deadline',                            array( $this, 'alg_product_crowdfunding_deadline' ) );
		add_shortcode( 'product_crowdfunding_deadline_time',                       array( $this, 'alg_product_crowdfunding_deadline_time' ) );
		add_shortcode( 'product_crowdfunding_deadline_datetime',                   array( $this, 'alg_product_crowdfunding_deadline_datetime' ) );
		add_shortcode( 'product_crowdfunding_time_remaining',                      array( $this, 'alg_product_crowdfunding_time_remaining' ) );
		add_shortcode( 'product_crowdfunding_time_to_start',                       array( $this, 'alg_product_crowdfunding_time_to_start' ) );
		add_shortcode( 'product_crowdfunding_goal_remaining_progress_bar',         array( $this, 'alg_product_crowdfunding_goal_remaining_progress_bar' ) );
		add_shortcode( 'product_crowdfunding_goal_backers_remaining_progress_bar', array( $this, 'alg_product_crowdfunding_goal_backers_remaining_progress_bar' ) );
		add_shortcode( 'product_crowdfunding_goal_items_remaining_progress_bar',   array( $this, 'alg_product_crowdfunding_goal_items_remaining_progress_bar' ) );
		add_shortcode( 'product_crowdfunding_time_remaining_progress_bar',         array( $this, 'alg_product_crowdfunding_time_remaining_progress_bar' ) );
		add_shortcode( 'product_crowdfunding_add_to_cart_form',                    array( $this, 'alg_product_crowdfunding_add_to_cart_form' ) );
		// Deprecated
		add_shortcode( 'product_total_orders',                                     array( $this, 'alg_product_total_orders' ) );
		add_shortcode( 'product_total_orders_sum',                                 array( $this, 'alg_product_total_orders_sum' ) );
		add_shortcode( 'product_crowdfunding_goal_progress_bar',                   array( $this, 'alg_product_crowdfunding_goal_progress_bar' ) );
		add_shortcode( 'product_crowdfunding_time_progress_bar',                   array( $this, 'alg_product_crowdfunding_time_progress_bar' ) );
	}

	/**
	 * alg_product_crowdfunding_add_to_cart_form.
	 *
	 * @version 2.1.0
	 * @since   1.2.0
	 */
	function alg_product_crowdfunding_add_to_cart_form( $atts ) {
//		remove_filter( 'wc_get_template', array( $this, 'change_variable_add_to_cart_template' ), PHP_INT_MAX );
		$the_product = isset( $atts['product_id'] ) ? wc_get_product( $atts['product_id'] ) : wc_get_product();
		$return = ( $the_product->is_type( 'variable' ) ) ? woocommerce_variable_add_to_cart() : woocommerce_simple_add_to_cart();
//		add_filter(    'wc_get_template', array( $this, 'change_variable_add_to_cart_template' ), PHP_INT_MAX, 5 );
		return $return;
	}

	/**
	 * get_progress_bar.
	 *
	 * @version 2.3.0
	 * @since   2.3.0
	 */
	function get_progress_bar( $atts, $value, $max_value ) {
		if ( ! isset( $atts['type'] ) ) {
			$atts['type'] = 'standard';
		}
		if ( ! isset( $atts['color'] ) ) {
			$atts['color'] = '#2bde73';
		}
		if ( ! isset( $atts['text_color'] ) ) {
			$atts['text_color'] = '#999';
		}
		if ( ! isset( $atts['width'] ) ) {
			$atts['width'] = '200px'; // todo
//			$atts['width'] = '100%';
		}
		if ( ! isset( $atts['height'] ) ) {
			$atts['height'] = ( 'line' === $atts['type'] ) ? '8px' : '200px';
		}
		if ( ! isset( $atts['style'] ) ) {
			$atts['style'] = '';
		}
		if ( $value < 0 ) {
			$value = 0;
		}
		if ( $max_value < 0 ) {
			$max_value = 0;
		}
		switch ( $atts['type'] ) {
			case 'line':
			case 'circle':
				return '<div class="alg-progress-bar"' .
					' type="' . $atts['type'] . '"' .
					' color="' . $atts['color'] . '"' .
					' text_color="' . $atts['text_color'] . '"' .
					' style="width:' . $atts['width'] . ';height:' . $atts['height'] . ';position:relative;' . $atts['style'] . '"' .
					' value="' . ( 0 != $max_value ? $value / $max_value : 0 ) . '">' .
				'</div>';
			default: // 'standard'
				return '<progress value="' . $value . '" max="' . $max_value . '"></progress>';
		}
	}

	/**
	 * alg_product_crowdfunding_time_remaining_progress_bar.
	 *
	 * @version 2.3.2
	 * @since   2.2.1
	 */
	function alg_product_crowdfunding_time_remaining_progress_bar( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) return '';

		$deadline_datetime  = trim( get_post_meta( $product_id, '_' . 'alg_crowdfunding_deadline', true )  . ' ' . get_post_meta( $product_id, '_' . 'alg_crowdfunding_deadline_time', true ), ' ' );
		$startdate_datetime = trim( get_post_meta( $product_id, '_' . 'alg_crowdfunding_startdate', true ) . ' ' . get_post_meta( $product_id, '_' . 'alg_crowdfunding_starttime', true ), ' ' );

		$seconds_remaining = strtotime( $deadline_datetime ) - ( (int) current_time( 'timestamp' ) );
		$seconds_total     = strtotime( $deadline_datetime ) - strtotime( $startdate_datetime );

		return $this->output_shortcode( $this->get_progress_bar( $atts, $seconds_remaining, $seconds_total ), $atts );
	}

	/**
	 * alg_product_crowdfunding_time_progress_bar.
	 *
	 * @version     2.2.1
	 * @since       1.2.0
	 * @deprecated
	 */
	function alg_product_crowdfunding_time_progress_bar( $atts ) {
		return $this->alg_product_crowdfunding_time_remaining_progress_bar( $atts );
	}

	/**
	 * alg_product_crowdfunding_goal_items_remaining_progress_bar.
	 *
	 * @version 2.3.0
	 * @since   2.2.0
	 */
	function alg_product_crowdfunding_goal_items_remaining_progress_bar( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) return '';
		$current_value = alg_get_product_orders_data( 'total_items', $atts );
		$max_value     = get_post_meta( $product_id, '_' . 'alg_crowdfunding_goal_items', true );
		return $this->output_shortcode( $this->get_progress_bar( $atts, $current_value, $max_value ), $atts );
	}

	/**
	 * alg_product_crowdfunding_goal_backers_remaining_progress_bar.
	 *
	 * @version 2.3.0
	 * @since   2.2.0
	 */
	function alg_product_crowdfunding_goal_backers_remaining_progress_bar( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) return '';
		$current_value = alg_get_product_orders_data( 'total_orders', $atts );
		$max_value     = get_post_meta( $product_id, '_' . 'alg_crowdfunding_goal_backers', true );
		return $this->output_shortcode( $this->get_progress_bar( $atts, $current_value, $max_value ), $atts );
	}

	/**
	 * alg_product_crowdfunding_goal_remaining_progress_bar.
	 *
	 * @version 2.3.0
	 * @since   2.2.0
	 */
	function alg_product_crowdfunding_goal_remaining_progress_bar( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) return '';
		$current_value = alg_get_product_orders_data( 'orders_sum', $atts );
		$max_value     = get_post_meta( $product_id, '_' . 'alg_crowdfunding_goal_sum', true );
		return $this->output_shortcode( $this->get_progress_bar( $atts, $current_value, $max_value ), $atts );
	}

	/**
	 * alg_product_crowdfunding_goal_progress_bar.
	 *
	 * @version     2.2.0
	 * @since       1.2.0
	 * @deprecated
	 */
	function alg_product_crowdfunding_goal_progress_bar( $atts ) {
		return $this->alg_product_crowdfunding_goal_remaining_progress_bar( $atts );
	}

	/**
	 * output_shortcode.
	 *
	 * @version 2.2.0
	 * @since   1.0.0
	 */
	function output_shortcode( $value, $atts ) {
		if ( '' != $value || ( isset( $atts['show_if_zero'] ) && 'yes' === $atts['show_if_zero'] ) ) {
			if ( ! isset( $atts['before'] ) ) $atts['before'] = '';
			if ( ! isset( $atts['after'] ) )  $atts['after'] = '';
			$value = ( isset( $atts['type'] ) && 'price' === $atts['type'] ) ? wc_price( $value ) : $value;
			return $atts['before'] . $value . $atts['after'];
		}
		return '';
	}

	/**
	 * alg_product_crowdfunding_total_items.
	 *
	 * @version 2.3.0
	 * @since   2.2.0
	 */
	function alg_product_crowdfunding_total_items( $atts ) {
		return $this->output_shortcode( alg_get_product_orders_data( 'total_items', $atts ), $atts );
	}

	/**
	 * alg_product_crowdfunding_total_backers.
	 *
	 * @version 2.3.0
	 * @since   2.2.0
	 */
	function alg_product_crowdfunding_total_backers( $atts ) {
		return $this->output_shortcode( alg_get_product_orders_data( 'total_orders', $atts ), $atts );
	}

	/**
	 * alg_product_total_orders.
	 *
	 * @version     2.2.0
	 * @since       1.0.0
	 * @deprecated
	 */
	function alg_product_total_orders( $atts ) {
		return $this->alg_product_crowdfunding_total_backers( $atts );
	}

	/**
	 * alg_product_crowdfunding_total_sum.
	 *
	 * @version 2.3.0
	 * @since   2.2.0
	 */
	function alg_product_crowdfunding_total_sum( $atts ) {
		$atts['type'] = 'price';
		return $this->output_shortcode( alg_get_product_orders_data( 'orders_sum', $atts ), $atts );
	}

	/**
	 * alg_product_total_orders_sum.
	 *
	 * @version     2.2.0
	 * @since       1.0.0
	 * @deprecated
	 */
	function alg_product_total_orders_sum( $atts ) {
		return $this->alg_product_crowdfunding_total_sum( $atts );
	}

	/**
	 * alg_product_crowdfunding_deadline_datetime.
	 *
	 * @version 2.2.0
	 * @since   1.1.0
	 */
	function alg_product_crowdfunding_deadline_datetime( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) return '';
		return $this->output_shortcode(
			date_i18n( get_option( 'date_format' ), strtotime( get_post_meta( $product_id, '_' . 'alg_crowdfunding_deadline', true ) ) ) .
			' ' .
			date_i18n( get_option( 'time_format' ), strtotime( get_post_meta( $product_id, '_' . 'alg_crowdfunding_deadline_time', true ) ) ), $atts );
	}

	/**
	 * alg_product_crowdfunding_deadline_time.
	 *
	 * @version 2.1.0
	 * @since   1.1.0
	 */
	function alg_product_crowdfunding_deadline_time( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) return '';
		return $this->output_shortcode( get_post_meta( $product_id, '_' . 'alg_crowdfunding_deadline_time', true ), $atts );
	}

	/**
	 * alg_product_crowdfunding_startdatetime.
	 *
	 * @version 2.2.0
	 * @since   1.1.0
	 */
	function alg_product_crowdfunding_startdatetime( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) return '';
		return $this->output_shortcode(
			date_i18n( get_option( 'date_format' ), strtotime( get_post_meta( $product_id, '_' . 'alg_crowdfunding_startdate', true ) ) ) .
			' ' .
			date_i18n( get_option( 'time_format' ), strtotime( get_post_meta( $product_id, '_' . 'alg_crowdfunding_starttime', true ) ) ), $atts );
	}

	/**
	 * alg_product_crowdfunding_starttime.
	 *
	 * @version 2.1.0
	 * @since   1.1.0
	 */
	function alg_product_crowdfunding_starttime( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) return '';
		return $this->output_shortcode( get_post_meta( $product_id, '_' . 'alg_crowdfunding_starttime', true ), $atts );
	}

	/**
	 * alg_product_crowdfunding_startdate.
	 *
	 * @version 2.2.0
	 * @since   1.0.0
	 */
	function alg_product_crowdfunding_startdate( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) return '';
		return $this->output_shortcode( date_i18n( get_option( 'date_format' ), strtotime( get_post_meta( $product_id, '_' . 'alg_crowdfunding_startdate', true ) ) ), $atts );
	}

	/**
	 * alg_product_crowdfunding_deadline.
	 *
	 * @version 2.2.0
	 * @since   1.0.0
	 */
	function alg_product_crowdfunding_deadline( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) return '';
		return $this->output_shortcode( date_i18n( get_option( 'date_format' ), strtotime( get_post_meta( $product_id, '_' . 'alg_crowdfunding_deadline', true ) ) ), $atts );
	}

	/**
	 * alg_product_crowdfunding_time_to_start.
	 *
	 * @version 2.3.2
	 * @since   2.3.2
	 */
	function alg_product_crowdfunding_time_to_start( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) return '';

		$from = (int) current_time( 'timestamp' );
		$to = strtotime( trim( get_post_meta( $product_id, '_' . 'alg_crowdfunding_startdate', true ) . ' ' . get_post_meta( $product_id, '_' . 'alg_crowdfunding_starttime', true ), ' ' ) );

		if ( ! isset( $atts['campaign_will_start'] ) ) {
			$atts['campaign_will_start'] = __( 'The campaign will start in %s', 'crowdfunding-for-woocommerce' );
		}
		if ( ! isset( $atts['campaign_started'] ) ) {
			$atts['campaign_started']    = __( 'The campaign started %s ago', 'crowdfunding-for-woocommerce' );
		}

		if ( ! isset( $atts['precision'] ) ) {
			$atts['precision'] = 6;
		}

		if ( $from < $to ) {
			$return = sprintf( $atts['campaign_will_start'], $this->get_date_diff( $from, $to, $atts['precision'] ) );
		} else {
			$return = sprintf( $atts['campaign_started'],    $this->get_date_diff( $from, $to, $atts['precision'] ) );
		}

		return $this->output_shortcode( $return, $atts );
	}

	/**
	 * alg_product_crowdfunding_time_remaining.
	 *
	 * @version 2.3.2
	 * @since   1.0.0
	 */
	function alg_product_crowdfunding_time_remaining( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) return '';

		$from = (int) current_time( 'timestamp' );
		$to = strtotime( trim( get_post_meta( $product_id, '_' . 'alg_crowdfunding_deadline', true ) . ' ' . get_post_meta( $product_id, '_' . 'alg_crowdfunding_deadline_time', true ), ' ' ) );

		if ( ! isset( $atts['campaign_will_end'] ) ) {
			$atts['campaign_will_end'] = __( 'The campaign will end in %s', 'crowdfunding-for-woocommerce' );
		}
		if ( ! isset( $atts['campaign_ended'] ) ) {
			$atts['campaign_ended']    = __( 'The campaign ended %s ago', 'crowdfunding-for-woocommerce' );
		}

		if ( ! isset( $atts['precision'] ) ) {
			$atts['precision'] = 6;
		}

		if ( $from < $to ) {
			$return = sprintf( $atts['campaign_will_end'], $this->get_date_diff( $from, $to, $atts['precision'] ) );
		} else {
			$return = sprintf( $atts['campaign_ended'],    $this->get_date_diff( $from, $to, $atts['precision'] ) );
		}
		return $this->output_shortcode( $return, $atts );

		/*
		$return = human_time_diff( $from, $to );
		return $this->output_shortcode( $return, $atts );
		*/

		/*
		$seconds_remaining = $to - $from;
		$days_remaining    = floor( $seconds_remaining / ( 24 * 60 * 60 ) );
		$hours_remaining   = floor( $seconds_remaining / (      60 * 60 ) );
		$minutes_remaining = floor( $seconds_remaining /             60   );
		if ( $seconds_remaining <= 0 ) return '';

		if ( ! isset( $atts['day'] ) )     $atts['day']     = __( ' day left', 'crowdfunding-for-woocommerce' );
		if ( ! isset( $atts['days'] ) )    $atts['days']    = __( ' days left', 'crowdfunding-for-woocommerce' );
		if ( ! isset( $atts['hour'] ) )    $atts['hour']    = __( ' hour left', 'crowdfunding-for-woocommerce' );
		if ( ! isset( $atts['hours'] ) )   $atts['hours']   = __( ' hours left', 'crowdfunding-for-woocommerce' );
		if ( ! isset( $atts['minute'] ) )  $atts['minute']  = __( ' minute left', 'crowdfunding-for-woocommerce' );
		if ( ! isset( $atts['minutes'] ) ) $atts['minutes'] = __( ' minutes left', 'crowdfunding-for-woocommerce' );
		if ( ! isset( $atts['second'] ) )  $atts['second']  = __( ' second left', 'crowdfunding-for-woocommerce' );
		if ( ! isset( $atts['seconds'] ) ) $atts['seconds'] = __( ' seconds left', 'crowdfunding-for-woocommerce' );

		     if ( $days_remaining    >  0 ) $return = ( 1 == $days_remaining    ) ? $days_remaining    . $atts['day']    : $days_remaining    . $atts['days'];
		else if ( $hours_remaining   >  0 ) $return = ( 1 == $hours_remaining   ) ? $hours_remaining   . $atts['hour']   : $hours_remaining   . $atts['hours'];
		else if ( $minutes_remaining >  0 ) $return = ( 1 == $minutes_remaining ) ? $minutes_remaining . $atts['minute'] : $minutes_remaining . $atts['minutes'];
		else                                $return = ( 1 == $seconds_remaining ) ? $seconds_remaining . $atts['second'] : $seconds_remaining . $atts['seconds'];

		return $this->output_shortcode( $return, $atts );
		*/
	}

	/*
	 * get_date_diff.
	 *
	 * @version 2.3.2
	 * @since   2.3.2
	 * @see     https://gist.github.com/ozh/8169202
	 */
	function get_date_diff( $time1, $time2, $precision = 2 ) {
		// If not numeric then convert timestamps
		if( !is_int( $time1 ) ) {
			$time1 = strtotime( $time1 );
		}
		if( !is_int( $time2 ) ) {
			$time2 = strtotime( $time2 );
		}
		// If time1 > time2 then swap the 2 values
		if( $time1 > $time2 ) {
			list( $time1, $time2 ) = array( $time2, $time1 );
		}
		// Set up intervals and diffs arrays
		$intervals = array( 'year', 'month', 'day', 'hour', 'minute', 'second' );
		$diffs = array();
		foreach( $intervals as $interval ) {
			// Create temp time from time1 and interval
			$ttime = strtotime( '+1 ' . $interval, $time1 );
			// Set initial values
			$add = 1;
			$looped = 0;
			// Loop until temp time is smaller than time2
			while ( $time2 >= $ttime ) {
				// Create new temp time from time1 and interval
				$add++;
				$ttime = strtotime( "+" . $add . " " . $interval, $time1 );
				$looped++;
			}
			$time1 = strtotime( "+" . $looped . " " . $interval, $time1 );
			$diffs[ $interval ] = $looped;
		}
		$count = 0;
		$times = array();
		foreach( $diffs as $interval => $value ) {
			// Break if we have needed precission
			if( $count >= $precision ) {
				break;
			}
			// Add value and interval if value is bigger than 0
			if( $value > 0 ) {
				/* if( $value != 1 ) {
					$interval .= "s";
				}
				// Add value and interval to times array
				$times[] = $value . " " . $interval; */
				switch ( $interval ) {
					case 'year':
						$times[] = sprintf( _n( '%s year', '%s years', $value, 'crowdfunding-for-woocommerce' ), $value );
						break;
					case 'month':
						$times[] = sprintf( _n( '%s month', '%s months', $value, 'crowdfunding-for-woocommerce' ), $value );
						break;
					case 'day':
						$times[] = sprintf( _n( '%s day', '%s days', $value, 'crowdfunding-for-woocommerce' ), $value );
						break;
					case 'hour':
						$times[] = sprintf( _n( '%s hour', '%s hours', $value, 'crowdfunding-for-woocommerce' ), $value );
						break;
					case 'minute':
						$times[] = sprintf( _n( '%s minute', '%s minutes', $value, 'crowdfunding-for-woocommerce' ), $value );
						break;
					case 'second':
						$times[] = sprintf( _n( '%s second', '%s seconds', $value, 'crowdfunding-for-woocommerce' ), $value );
						break;
				}
				$count++;
			}
		}
		// Return string with times
		return implode( ", ", $times );
	}

	/**
	 * alg_product_crowdfunding_goal_items.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	function alg_product_crowdfunding_goal_items( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) return '';
		return $this->output_shortcode( get_post_meta( $product_id, '_' . 'alg_crowdfunding_goal_items', true ), $atts );
	}

	/**
	 * alg_product_crowdfunding_goal_backers.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	function alg_product_crowdfunding_goal_backers( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) return '';
		return $this->output_shortcode( get_post_meta( $product_id, '_' . 'alg_crowdfunding_goal_backers', true ), $atts );
	}

	/**
	 * alg_product_crowdfunding_goal.
	 *
	 * @version 2.1.0
	 * @since   1.0.0
	 */
	function alg_product_crowdfunding_goal( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) return '';
		$atts['type'] = 'price';
		return $this->output_shortcode( get_post_meta( $product_id, '_' . 'alg_crowdfunding_goal_sum', true ), $atts );
	}

	/**
	 * alg_product_crowdfunding_goal_items_remaining.
	 *
	 * @version 2.3.0
	 * @since   2.2.0
	 */
	function alg_product_crowdfunding_goal_items_remaining( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) return '';
		return $this->output_shortcode( get_post_meta( $product_id, '_' . 'alg_crowdfunding_goal_items', true ) - alg_get_product_orders_data( 'total_items', $atts ), $atts );
	}

	/**
	 * alg_product_crowdfunding_goal_backers_remaining.
	 *
	 * @version 2.3.0
	 * @since   2.2.0
	 */
	function alg_product_crowdfunding_goal_backers_remaining( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) return '';
		return $this->output_shortcode( get_post_meta( $product_id, '_' . 'alg_crowdfunding_goal_backers', true ) - alg_get_product_orders_data( 'total_orders', $atts ), $atts );
	}

	/**
	 * alg_product_crowdfunding_goal_remaining.
	 *
	 * @version 2.3.0
	 * @since   1.0.0
	 */
	function alg_product_crowdfunding_goal_remaining( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) return '';
		$atts['type'] = 'price';
		return $this->output_shortcode( get_post_meta( $product_id, '_' . 'alg_crowdfunding_goal_sum', true ) - alg_get_product_orders_data( 'orders_sum', $atts ), $atts );
	}

}

endif;

return new Alg_WC_Crowdfunding_Shortcodes();
