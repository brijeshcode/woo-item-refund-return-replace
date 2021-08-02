<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.phoeniixx.com/
 * @since      1.0.0
 *
 * @package    Phoe_Woo_Rrrec
 * @subpackage Phoe_Woo_Rrrec/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Phoe_Woo_Rrrec
 * @subpackage Phoe_Woo_Rrrec/public
 * @author     Brijesh <brijeshchaturvedi.it@gmail.com>
 */
class Phoe_Woo_Rrrec_Public {

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
		 * defined in Phoe_Woo_Rrrec_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Phoe_Woo_Rrrec_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/phoe-woo-rrrec-public.css', array(), $this->version, 'all' );

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
		 * defined in Phoe_Woo_Rrrec_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Phoe_Woo_Rrrec_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/phoe-woo-rrrec-public.js', array( 'jquery' ), $this->version, false );

	}

	public function phoe_woo_template_path( $template, $template_name, $template_path ) {
		global $woocommerce;
		$_template = $template;
		if ( ! $template_path )
		$template_path = $woocommerce->template_url;

		$plugin_path  = untrailingslashit( plugin_dir_path( __FILE__ ) )  . '/template/WooCommerce/';



		// Look within passed path within the theme - this is priority
		$template = locate_template(
		array(
		$template_path . $template_name,
		$template_name
		)
		);
		if( ! $template && file_exists( $plugin_path . $template_name ) )
		$template = $plugin_path . $template_name;

		if ( ! $template )
		$template = $_template;
		return $template;
	}

	public function phoe_order_item_action_td($show, $order)
	{

		$getData = get_option("phoe_order_item_actions");

		if (!isset($getData['exchange']['_enable_item_exchange']) && !isset($getData['refund']['_enable_item_refund'])) {
			return false;
		}

		if (
			(isset($getData['exchange']['_enable_item_exchange']) && !$getData['exchange']['_enable_item_exchange'] ) &&
			(isset($getData['refund']['_enable_item_refund']) && !$getData['refund']['_enable_item_refund'])
			) {
			return false;
		}


		$order_items   = $order->get_items();
        $order_id = $order->get_id();

		$item_acted = phoe_count_order_item_acted($order_id);
		$orderItemsCount = count($order->get_items());

		if (($orderItemsCount - $item_acted) < 2) {
			return false;
		}

		foreach ($order_items as $item_id => $item) {

            if ($item->get_type() != 'line_item') {
                continue;
            }

			$req_status = phoe_getItemCancelStatus($order_id, $item_id);
			if ($req_status) {
				return true;
			}
        }

		$orderStatus = $order->get_status();
		if ( !in_array($orderStatus, ['completed','processing']) ) {
			return false;
		}

		if ($orderStatus == 'processing' ) {

			if (!isset($getData['cancel']['_enable_item_cancel']) ){
				return false;
			}

			if (isset($getData['cancel']['_enable_item_cancel']) && !$getData['cancel']['_enable_item_cancel']) {
				return false;
			}
		}

		if ($orderStatus == 'processing' && sizeof( $order->get_items() ) > 1 && !is_page('checkout')) {
			return true;
		}



		return $show;
	}

	public function phoe_show_cancel_btn($show, $order, $item_id)
	{
		$orderStatus = $order->get_status();
		if ($orderStatus != 'processing') {
			return false;
		}

		$order_id = $order->get_id();
		$item_acted = phoe_count_order_item_acted($order_id);
		$orderItemsCount = count($order->get_items());

		if (($orderItemsCount - $item_acted) < 2) {
			return false;
		}


		if (  is_page('checkout')) {
			return false;
		}

		$getData = get_option("phoe_order_item_actions");
		if (isset($getData['cancel']['_enable_item_cancel']) &&  $getData['cancel']['_enable_item_cancel']) {
			return true;
		}

		$req_status = phoe_getItemCancelStatus($order_id, $item_id);

		if ($req_status) {
			return true;
		}

		return false;
	}

	public function phoe_show_refund_btn($show, $order, $item_id)
	{
		$getData = get_option("phoe_order_item_actions");
		if (!isset($getData['refund']['_enable_item_refund']) ||  !$getData['refund']['_enable_item_refund']) {
			return false;
		}

		$orderStatus = $order->get_status();

		$order_id = $order->get_id();
		$req_status = phoe_getItemCancelStatus($order_id, $item_id);
		// if item is cancelled
		if ($req_status) {
			return false;
		}

		$item_acted = phoe_count_order_item_acted($order_id);
		$orderItemsCount = count($order->get_items());

		if (($orderItemsCount - $item_acted) < 2) {
			return false;
		}

		if ($orderStatus != 'completed' ) {
			return false;
		}


		return $show;
	}

	public function phoe_show_exchange_btn($show, $order, $item_id)
	{
		$getData = get_option("phoe_order_item_actions");
		if (!isset($getData['exchange']['_enable_item_exchange']) ||  !$getData['exchange']['_enable_item_exchange']) {
			return false;
		}

		$orderStatus = $order->get_status();

		$order_id = $order->get_id();
		$req_status = phoe_getItemCancelStatus($order_id, $item_id);
		// if item is cancelled
		if ($req_status) {
			return false;
		}

		$item_acted = phoe_count_order_item_acted($order_id);
		$orderItemsCount = count($order->get_items());

		if (($orderItemsCount - $item_acted) < 2) {
			return false;
		}

		if ($orderStatus == 'completed' ) {
			return true;
		}


		return false;
	}



}
