<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.phoeniixx.com/
 * @since      1.0.0
 *
 * @package    Phoe_Woo_Rrrec
 * @subpackage Phoe_Woo_Rrrec/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Phoe_Woo_Rrrec
 * @subpackage Phoe_Woo_Rrrec/includes
 * @author     Brijesh <brijeshchaturvedi.it@gmail.com>
 */
class Phoe_Woo_Rrrec {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Phoe_Woo_Rrrec_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'PHOE_WOO_RRREC_VERSION' ) ) {
			$this->version = PHOE_WOO_RRREC_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'phoe-woo-rrrec';

		$this->load_dependencies();
		$this->set_locale();
			$this->define_admin_hooks();


		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Phoe_Woo_Rrrec_Loader. Orchestrates the hooks of the plugin.
	 * - Phoe_Woo_Rrrec_i18n. Defines internationalization functionality.
	 * - Phoe_Woo_Rrrec_Admin. Defines all hooks for the admin area.
	 * - Phoe_Woo_Rrrec_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-phoe-woo-rrrec-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-phoe-woo-rrrec-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-phoe-woo-rrrec-admin.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'helpers/admin-settings.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'helpers/check-buttons-settings.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'helpers/manage-customer-requests.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'helpers/helper.php';


		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-phoe-woo-rrrec-public.php';


		$this->loader = new Phoe_Woo_Rrrec_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Phoe_Woo_Rrrec_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Phoe_Woo_Rrrec_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Phoe_Woo_Rrrec_Admin( $this->get_plugin_name(), $this->get_version() );
		if (isset($_GET['page']) && $_GET['page'] == 'phoe-wc-item-action') {
			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		}
      	$this->loader->add_action( 'admin_menu',  $plugin_admin, 'woo_item_action_menu' );

		if (isset($_GET['bkc_testing'])) {
			$this->loader->add_action( 'wp_loaded', $plugin_admin, 'test' );
		}


		/*if ((isset($_GET['item_id']) && !empty($_GET['item_id']))&& (isset($_GET['order_id']) && !empty($_GET['order_id'])) ) {
			if (!isset($_GET['user_id'])) {
				$this->loader->add_action( 'wp_loaded', $plugin_admin, 'phoe_cancel_order_item_met' );
			}
		}*/


		/*if (isset($_POST['cancel_request']) && !empty($_POST['cancel_request'])) {
			if (!isset($_GET['user_id'])) {
				$this->loader->add_action( 'wp_loaded', $plugin_admin, 'phoe_cancel_order_item_request' );
			}
		}*/


	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Phoe_Woo_Rrrec_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_filter( 'woocommerce_locate_template', $plugin_public, 'phoe_woo_template_path' , 1 , 3);
		$this->loader->add_filter( 'phoe_woo_order_item_action', $plugin_public, 'phoe_order_item_action_td', 10 , 2);
		$this->loader->add_filter( 'phoe_woo_order_item_show_cancel_btn', $plugin_public, 'phoe_show_cancel_btn', 10 , 3);
		$this->loader->add_filter( 'phoe_woo_order_item_show_refund_btn', $plugin_public, 'phoe_show_refund_btn', 10 , 3);
		$this->loader->add_filter( 'phoe_woo_order_item_show_exchange_btn', $plugin_public, 'phoe_show_exchange_btn', 10 , 3);
		$this->loader->add_filter( 'phoe_woo_order_item_show_btn', $plugin_public, 'phoe_add_action_item_buttons', 10 , 3);

		$this->loader->add_filter( 'woocommerce_my_account_my_orders_actions', $plugin_public, 'phoe_add_action_buttons', 10 , 2);




	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Phoe_Woo_Rrrec_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
