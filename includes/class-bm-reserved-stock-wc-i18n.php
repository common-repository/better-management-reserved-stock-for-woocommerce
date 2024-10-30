<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://github.com/h8ps1tm
 * @since      1.0.0
 *
 * @package    Bm_Reserved_Stock_Wc
 * @subpackage Bm_Reserved_Stock_Wc/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Bm_Reserved_Stock_Wc
 * @subpackage Bm_Reserved_Stock_Wc/includes
 * @author     Tiago Mano <tiago@hellodev.us>
 */
class Bm_Reserved_Stock_Wc_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'bm-reserved-stock-wc',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
