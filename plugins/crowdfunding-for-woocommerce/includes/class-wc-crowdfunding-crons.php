<?php
/**
 * WooCommerce Crowdfunding Crons
 *
 * The WooCommerce Crowdfunding Crons class.
 *
 * @version 2.3.1
 * @since   2.3.0
 * @author  Tom Anbinder
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Alg_WC_Crowdfunding_Crons' ) ) :

class Alg_WC_Crowdfunding_Crons {

	/**
	 * Constructor.
	 *
	 * @version 2.3.0
	 * @since   2.3.0
	 */
	public function __construct() {
		add_action( 'init',                          array( $this, 'schedule_the_events' ) );
		add_action( 'admin_init',                    array( $this, 'schedule_the_events' ) );
		add_action( 'alg_update_products_data_hook', array( $this, 'update_products_data' ) );
		add_filter( 'cron_schedules',                array( $this, 'cron_add_custom_intervals' ) );
	}

	/**
	 * On an early action hook, check if the hook is scheduled - if not, schedule it.
	 *
	 * @version 2.3.0
	 * @since   2.3.0
	 */
	function schedule_the_events() {
		$selected_interval = get_option( 'alg_crowdfunding_products_data_update', 'fifthteen' );
		$update_intervals = array(
			'minutely',
			'fifthteen',
			'hourly',
			'twicedaily',
			'daily',
			'weekly',
		);
		foreach ( $update_intervals as $interval ) {
			$event_hook = 'alg_update_products_data_hook';
			$event_timestamp = wp_next_scheduled( $event_hook, array( $interval ) );
			if ( $selected_interval === $interval ) {
				update_option( 'alg_crowdfunding_products_data_update_cron_time', $event_timestamp );
			}
			if ( ! $event_timestamp && $selected_interval === $interval ) {
				wp_schedule_event( time(), $selected_interval, $event_hook, array( $selected_interval ) );
			} elseif ( $event_timestamp && $selected_interval !== $interval ) {
				wp_unschedule_event( $event_timestamp, $event_hook, array( $interval ) );
			}
		}
	}

	/**
	 * On the scheduled action hook, run a function.
	 *
	 * @version 2.3.1
	 * @since   2.3.0
	 */
	function update_products_data( $interval ) {
		if ( 'yes' === get_option( 'alg_woocommerce_crowdfunding_enabled', 'yes' ) ) {
			if ( 'manual' != get_option( 'alg_crowdfunding_products_data_update', 'fifthteen' ) ) {
				$offset = 0;
				$block_size = 256;
				while( true ) {
					$args = array(
						'post_type'      => 'product',
						'post_status'    => 'any',
						'posts_per_page' => $block_size,
						'offset'         => $offset,
						'orderby'        => 'title',
						'order'          => 'ASC',
						'fields'         => 'ids',
					);
					$loop = new WP_Query( $args );
					if ( ! $loop->have_posts() ) {
						break;
					}
					foreach ( $loop->posts as $product_id ) {
						if ( 'yes' === get_post_meta( $product_id, '_' . 'alg_crowdfunding_enabled', true ) ) {
							$orders_sum   = alg_calculate_product_orders_data( 'orders_sum',   $product_id );
							$total_orders = alg_calculate_product_orders_data( 'total_orders', $product_id );
							$total_items  = alg_calculate_product_orders_data( 'total_items',  $product_id );
							update_post_meta( $product_id, '_' . 'alg_crowdfunding_' . 'orders_sum',   $orders_sum );
							update_post_meta( $product_id, '_' . 'alg_crowdfunding_' . 'total_orders', $total_orders );
							update_post_meta( $product_id, '_' . 'alg_crowdfunding_' . 'total_items',  $total_items );
							update_post_meta( $product_id, '_' . 'alg_crowdfunding_' . 'products_data_updated_time', time() );
						}
					}
					$offset += $block_size;
				}
				wp_reset_postdata(); // do we really need this line?
			}
		}
	}

	/**
	 * cron_add_custom_intervals.
	 *
	 * @version 2.3.0
	 * @since   2.3.0
	 */
	function cron_add_custom_intervals( $schedules ) {
		$schedules['weekly'] = array(
			'interval' => 604800,
			'display' => __( 'Once Weekly', 'crowdfunding-for-woocommerce' )
		);
		$schedules['fifthteen'] = array(
			'interval' => 900,
			'display' => __( 'Every Fifthteen Minutes', 'crowdfunding-for-woocommerce' )
		);
		$schedules['minutely'] = array(
			'interval' => 60,
			'display' => __( 'Once a Minute', 'crowdfunding-for-woocommerce' )
		);
		return $schedules;
	}
}

endif;

return new Alg_WC_Crowdfunding_Crons();
