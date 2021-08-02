<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.phoeniixx.com/
 * @since      1.0.0
 *
 * @package    Phoe_Woo_Rrrec
 * @subpackage Phoe_Woo_Rrrec/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Phoe_Woo_Rrrec
 * @subpackage Phoe_Woo_Rrrec/includes
 * @author     Brijesh <brijeshchaturvedi.it@gmail.com>
 */
class Phoe_Woo_Rrrec_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'phoe-woo-rrrec',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
