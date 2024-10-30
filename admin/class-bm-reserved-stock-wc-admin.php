<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/h8ps1tm
 * @since      1.0.0
 *
 * @package    Bm_Reserved_Stock_Wc
 * @subpackage Bm_Reserved_Stock_Wc/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Bm_Reserved_Stock_Wc
 * @subpackage Bm_Reserved_Stock_Wc/admin
 * @author     Tiago Mano <tiago@hellodev.us>
 */
class Bm_Reserved_Stock_Wc_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Bm_Reserved_Stock_Wc_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Bm_Reserved_Stock_Wc_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/bm-reserved-stock-wc-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Bm_Reserved_Stock_Wc_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Bm_Reserved_Stock_Wc_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/bm-reserved-stock-wc-admin.js', array( 'jquery' ), $this->version, false );

	}
	
	/**
	 * Prevents the add to cart if the stock chosen is more than the available
	 *
	 * @since    1.0.0
	*/
	public function filter_woocommerce_add_to_cart_validation( $true, $product_id, $quantity ) { 
		
		$product = wc_get_product( $product_id );

		if ( $product->managing_stock() && ! $product->backorders_allowed() ) {
			
			$items = WC()->cart->get_cart();
	
			foreach( $items as $item => $values ) { 
				if ( $values['data']->get_id() === $product_id ) 
					$quantity_cart = $values['quantity'];
			} 
			$total_quantity = $quantity + $quantity_cart;
			$args = Bm_Reserved_Stock_Wc_Admin::getStatusPurchasable( $product, $total_quantity, 'validate' );
	
			if ( $args['bool'] == 'false' ) {
				
				$product_stock = $args['qty_remaining'];
				$qtt_available = $args['product_stock'] - $args['qty_remaining'];
				if ( $quantity_cart ) {
					wc_add_notice( 	sprintf(
						__( 'You cannot add that amount of &quot;%1$s&quot; to the cart because there is not enough stock (%2$s remaining). You already have %3$s units of this product in the cart.', 'woocommerce' ),
						$product->get_name(),
						wc_format_stock_quantity_for_display( $qtt_available, $product ),
						$quantity_cart
					), 
					'error' 
				);
				} else {
					wc_add_notice( 	sprintf(
						__( 'You cannot add that amount of &quot;%1$s&quot; to the cart because there is not enough stock (%2$s remaining).', 'woocommerce' ),
						$product->get_name(),
						wc_format_stock_quantity_for_display( $qtt_available, $product )
					), 
					'error' 
				);
				}
				
				
				$true = false;
				
			}
		}
		
		return $true;
	} 
	
	/**
	 * Define if the product is purchasable or not
	 *
	 * @since    1.0.0
	*/
	public function hide_add_to_cart_single_button_on_reduced_stock( $is_purchasable, $product ) {
		
		$product_id = $product->get_id();
	
		if ( $product->managing_stock() ) {
	
			global $wpdb;
			$query = $wpdb->prepare(
				"
				SELECT count(`product_id`) FROM $wpdb->wc_reserved_stock stock_table
				LEFT JOIN $wpdb->posts posts ON stock_table.`order_id` = posts.ID
				WHERE posts.post_status IN ( 'wc-checkout-draft', 'wc-pending' )
				AND stock_table.`expires` > NOW()
				AND stock_table.`product_id` = %d
				",
				$product_id
			);
			$has_results = $wpdb->get_var( $query );
			
			if ( $has_results > 0 ) { 
				$arg = Bm_Reserved_Stock_Wc_Admin::getStatusPurchasable( $product, 1, 'button' );
				if ( $arg === 'false' )
					$is_purchasable = false;
				else
					$is_purchasable = true;
			} else {
				$is_purchasable = true;
			}
			
		}
			
		return $is_purchasable;
	}
	
	/**
	 * Return if the product is available or not
	 *
	 * @since    1.0.0
	*/
	public function getStatusPurchasable ( $product, $quantity, $type ) {
		
		$product_id = $product->get_id();
		
		global $wpdb;
		$query = $wpdb->prepare(
			"
			SELECT COALESCE( SUM( stock_table.`stock_quantity` ), 0 ) FROM $wpdb->wc_reserved_stock stock_table
			LEFT JOIN $wpdb->posts posts ON stock_table.`order_id` = posts.ID
			WHERE posts.post_status IN ( 'wc-checkout-draft', 'wc-pending' )
			AND stock_table.`expires` > NOW()
			AND stock_table.`product_id` = %d
			",
			$product_id
		);
	
		$product_stock = $product->get_stock_quantity();
		$qty_remaining = $wpdb->get_var( $query );

		if ( $type == 'qty_remaining' ) {
			return $qty_remaining;
		} else {
			if ( $product_stock < ( $qty_remaining + $quantity ) ) {
				
				if ( $type == 'validate' ) {
					return array( 'bool' => 'false', 'qty_remaining' => $qty_remaining, 'product_stock' => $product_stock );
				} else {
					return 'false';
				}
			}			
		}
		
	}
	
	/**
	 * Change the available text with the correct stock quantity
	 *
	 * @since    1.0.0
	*/
	function change_stock_message( $text, $product ) {  
		
		// Managing stock checked
		if ( $product->managing_stock() ) {
	
			// If product is in stock
			if ( $product->is_in_stock() ) {
				
				// Check the real stock counting with reserved stock
				$qty_remaining 		= Bm_Reserved_Stock_Wc_Admin::getStatusPurchasable( $product, '', 'qty_remaining' );
				$actual_stock 		= $product->get_stock_quantity();
				$qtt_available 		= $actual_stock - $qty_remaining;
				$wsf 				= get_option( 'woocommerce_stock_format' );
				
				if ( $wsf === 'no_amount' ) {
					return $text;
				} else {
					if ( $wsf === 'low_amount' && $qtt_available <= 2 ) {
						return $qtt_available . ' ' . $text;
					}
					else if ( $wsf === 'low_amount' && $qtt_available > 2 ) {
						return $text;
					} else {
						return str_replace( $actual_stock, $qtt_available, $text );
					}
					
				}
				
			}
		}
	
		return $text;
	}
	
	/**
	 * Check if WooCommerce is activated and if not show a notice message
	 *
	 * @since    1.0.0
	*/
	public function hc_wc_admin_notice() {
		
		if ( ! class_exists( 'woocommerce' ) && current_user_can( 'activate_plugins' ) ) :
			?>
			<div class="notice notice-error is-dismissible">
				<p>
					<?php
					printf(
						__('To use our plugin %1$s you need to activate the %2$sWooCommerce%3$s.', $this->plugin_name ),
						'<strong>Better Management Reserved Stock - WooCommerce</strong>',
						'<a href="https://pt.wordpress.org/plugins/woocommerce/" target="_blank" >',
						'</a>'
					);
					?>
				</p>
			</div>		
			<?php
		endif;
		
	}

}
