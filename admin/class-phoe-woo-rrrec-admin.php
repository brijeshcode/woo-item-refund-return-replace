<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.phoeniixx.com/
 * @since      1.0.0
 *
 * @package    Phoe_Woo_Rrrec
 * @subpackage Phoe_Woo_Rrrec/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Phoe_Woo_Rrrec
 * @subpackage Phoe_Woo_Rrrec/admin
 * @author     Brijesh <brijeshchaturvedi.it@gmail.com>
 */
class Phoe_Woo_Rrrec_Admin {

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
		// add_action( 'save_post', array(&$this,'phoe_save_order_item_action_settings' ),10,3 );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/phoe-woo-rrrec-admin.css', array(), $this->version, 'all' );

		wp_enqueue_style( $this->plugin_name. '-boot-utility', plugin_dir_url( __FILE__ ) . 'css/phoe-woo-rrrec-admin-bootstarp.min.css', array(), $this->version, 'all' );


	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/phoe-woo-rrrec-admin.js', array( 'jquery' ), $this->version, false );

	}


	public function test(){
		if (isset($_GET['order_id']) && isset($_GET['item_id'])) {
			$orderId = $_GET['order_id'];
			$cancelItemId = $_GET['item_id'];

		}
	}

	/*public function phoe_cancel_order_item_met() {
		phoe_cancel_order_item();
	}*/


	public function woo_item_action_menu()
	{
		 add_submenu_page( 'woocommerce', 'Actions', 'Actions', 'manage_options', 'phoe-wc-item-action',array(&$this,'phoe_wc_item_action_menu' )  );

		// add_submenu_page( 'admin-menu-url', 'Admin Submenu name', 'Admin Submenu name','administrator', 'admin-submenu-url' , array(&$this,'menu-viewpage-function'));
	}

	public function phoe_wc_item_action_menu() {
		$default = 'refund';
		$selecteTab = isset($_GET['tab']) ? $_GET['tab'] : $default; // default

		$tabs = [
			'cancel' => 'item-cancel' ,
		 	'cancel-setting'=> 'setting-cancel',
		 	'cancel-order'=> 'order-cancel',

		 	'refund'=> 'item-refund',
		 	'refund-setting'=> 'setting-refund',
		 	'refund-order'=> 'order-refund',

		 	'replace'=> 'item-replace',
		 	'replace-setting'=> 'setting-replace',
		 	'replace-order'=> 'order-replace',
		 	'settings'=> 'item-settings',
		];

		$template = isset($tabs[$selecteTab]) ? $tabs[$selecteTab] : 'invalid-tab';

		include 'template/'.$template.'.php' ;
	}

	public function phoe_save_order_item_action_settings()
	{
		if (!check_admin_referer('phoe_order_item_action_settings', 'phoe_order_item_action_settings_nonce_field')) {
			return '';
		}

		if (!wp_verify_nonce( $_POST['phoe_order_item_action_settings_nonce_field'], 'phoe_order_item_action_settings' ) ) {
			return '';
		}

		$submitted = $_POST['phoe_wc_item_action'] ;
		$key = array_keys($submitted)[0];
		$old = get_option("phoe_order_item_actions");
		$old[$key] = $submitted[$key];
    	update_option("phoe_order_item_actions",$old);
	}
}
