<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.phoeniixx.com/
 * @since             1.0.0
 * @package           Phoe_Woo_Rrrec
 *
 * @wordpress-plugin
 * Plugin Name:       return/refund-cancel-exchange/replace
 * Plugin URI:        https://www.phoeniixx.com/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Brijesh
 * Author URI:        https://www.phoeniixx.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       phoe-woo-rrrec
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
define( 'PHOE_WOO_RRREC_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-phoe-woo-rrrec-activator.php
 */
function activate_phoe_woo_rrrec() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-phoe-woo-rrrec-activator.php';
	Phoe_Woo_Rrrec_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-phoe-woo-rrrec-deactivator.php
 */
function deactivate_phoe_woo_rrrec() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-phoe-woo-rrrec-deactivator.php';
	Phoe_Woo_Rrrec_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_phoe_woo_rrrec' );
register_deactivation_hook( __FILE__, 'deactivate_phoe_woo_rrrec' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-phoe-woo-rrrec.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_phoe_woo_rrrec() {

	$plugin = new Phoe_Woo_Rrrec();
	$plugin->run();

}
run_phoe_woo_rrrec();
