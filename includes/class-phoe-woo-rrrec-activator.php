<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.phoeniixx.com/
 * @since      1.0.0
 *
 * @package    Phoe_Woo_Rrrec
 * @subpackage Phoe_Woo_Rrrec/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Phoe_Woo_Rrrec
 * @subpackage Phoe_Woo_Rrrec/includes
 * @author     Brijesh <brijeshchaturvedi.it@gmail.com>
 */
class Phoe_Woo_Rrrec_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		self::createRequiredDbTables();
	}

	public static function createRequiredDbTables()
	{
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

	   	$table_name = $wpdb->prefix . "phoe_order_item_request";

	   	$query = " CREATE TABLE IF NOT EXISTS $table_name (
   			`id` INT NOT NULL AUTO_INCREMENT ,
   			`customer_email` VARCHAR(100) NULL,
   			`customer_name` VARCHAR(100) NULL,
   			`order_id` INT NOT NULL,
   			`request_for` varchar(20) default 'Item',
   			`item_id` INT NULL,
   			`request_from` varchar(20) NOT NULL DEFAULT 'API',
   			`request_type` varchar(50) NOT NULL ,
   			`request_status` varchar(50) NOT NULL ,
   			`request_reason` text NOT NULL,
   			`request_admin_reason` text NULL,
   			`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
   			PRIMARY KEY (`id`)) $charset_collate;";
	   	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $query );

		// request_for: [order, item];
		// request_type possible values are ['cancel', 'refund', 'exchange']
		// request_status possible values are ['requested', 'proccessing', 'completed', 'denied']
	}

}
