<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/h8ps1tm
 * @since      1.0.0
 *
 * @package    Bm_Reserved_Stock_Wc
 * @subpackage Bm_Reserved_Stock_Wc/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Bm_Reserved_Stock_Wc
 * @subpackage Bm_Reserved_Stock_Wc/public
 * @author     Tiago Mano <tiago@hellodev.us>
 */
class Bm_Reserved_Stock_Wc_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/bm-reserved-stock-wc-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		if ( is_product() ) {
		 	wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/bm-reserved-stock-wc-public.js', array( 'jquery' ), $this->version, false );
		
			global $post;
			wp_localize_script( $this->plugin_name, 'wp_ajax', array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'my-nonce' ),
				'product_id' => $post->ID, 
			) );
		}
	}
	
	/**
	 * Function in ajax to update the max quantity of the quantity field
	 * 
	 * @return quantity value
	 */
	public function ajax_uqp() {

		if ( ! isset( $_POST['nonce'] ) && ! wp_verify_nonce( $_POST['nonce'], 'my-nonce' ) ) {
			wp_send_json_error();
			die();
		}

		$product_id 	= sanitize_text_field( $_POST["product_id"] );
		$product 		= wc_get_product( $product_id );
		
		$admin_class 	= new Bm_Reserved_Stock_Wc_Admin( null, null );	

		if ( $product->managing_stock() && ! $product->backorders_allowed() ) {
			$qty_remaining 		= $admin_class->getStatusPurchasable( $product, '', 'qty_remaining' );
			$qty_stock			= $product->get_stock_quantity();
		}
		
		$qty_total = $qty_stock - $qty_remaining;
		die( json_encode( $qty_total ) );
	
	}

}
