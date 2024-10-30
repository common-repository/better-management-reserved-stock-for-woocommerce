<?php

/**
 *
 * @link              https://github.com/h8ps1tm
 * @since             1.0.0
 * @package           Bm_Reserved_Stock_Wc
 *
 * @wordpress-plugin
 * Plugin Name:       Better Management Reserved Stock for WooCommerce
 * Plugin URI:        https://hellodev.us
 * Description:       Prevent a product from being added to the cart if the stock is reserved.
 * Version:           1.0.0
 * Author:            Tiago Mano
 * Author URI:        https://github.com/h8ps1tm
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bm-reserved-stock-wc
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'BM_RESERVED_STOCK_WC_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-bm-reserved-stock-wc-activator.php
 */
function activate_bm_reserved_stock_wc() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bm-reserved-stock-wc-activator.php';
	Bm_Reserved_Stock_Wc_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-bm-reserved-stock-wc-deactivator.php
 */
function deactivate_bm_reserved_stock_wc() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bm-reserved-stock-wc-deactivator.php';
	Bm_Reserved_Stock_Wc_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_bm_reserved_stock_wc' );
register_deactivation_hook( __FILE__, 'deactivate_bm_reserved_stock_wc' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-bm-reserved-stock-wc.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_bm_reserved_stock_wc() {

	$plugin = new Bm_Reserved_Stock_Wc();
	$plugin->run();

}
run_bm_reserved_stock_wc();
