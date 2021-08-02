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

	public function phoe_cancel_order_item_met() {
		phoe_cancel_order_item();
	}


	public function woo_item_action_menu()
	{
		 add_submenu_page( 'woocommerce', 'Item Actions', 'Item Action', 'manage_options', 'phoe-wc-item-action',array(&$this,'phoe_wc_item_action_menu' )  );

		// add_submenu_page( 'admin-menu-url', 'Admin Submenu name', 'Admin Submenu name','administrator', 'admin-submenu-url' , array(&$this,'menu-viewpage-function'));
	}


	public function phoe_wc_item_action_menu() {
		$default = 'refund';
		$tab = isset($_GET['tab']) ? $_GET['tab'] : $default; // default
		$template = '';
		switch ($tab) {
			case 'cancel':
				$template = 'item-cancel';
				break;

			case 'cancel-setting':
				$template = 'setting-cancel';
				break;

			case 'refund':
				$template = 'item-refund';
				break;

			case 'refund-setting':
				$template = 'setting-refund';
				break;

			case 'replace':
				$template = 'item-replace';
				break;

			case 'replace-setting':
				$template = 'setting-replace';
				break;

			case 'settings':
				$template = 'item-settings';
				break;
			default:
				$template = 'item-'.$default;
				break;
		}
		include 'template/'.$template.'.php';
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

		$arrTemp = array();
		$type = ['refund', 'cancel', 'exchange'];
		foreach ($type as $Tkey => $Tvalue) {
			if (!isset($submitted[$Tvalue]))
				continue;

			foreach ($submitted[$Tvalue] as $key => $reasons) {
				if ($key = 'reason' && !empty($reasons) && is_array($reasons)) {
					foreach ($reasons as $reasonKey => $value) {
						$temp = explode(',', $value['reasons']);
						foreach ($temp as $arrKey => $arrValue) {
							$temp[$arrKey] = trim($arrValue);
						}
						$temp = array_values(array_filter($temp));
						$submitted[$Tvalue]['reason'][$reasonKey]['reasons'] = $temp;
					}
					$submitted[$Tvalue]['reason'] = array_values($submitted[$Tvalue]['reason']);
				}
			}
		}
    	update_option("phoe_order_item_actions",$submitted);
	}
}
