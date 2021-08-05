<?php

class Phoe_Woo_Rrrec_Public
{
    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/phoe-woo-rrrec-public.css', [], $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/phoe-woo-rrrec-public.js', ['jquery'], $this->version, false);
    }

    public function phoe_woo_template_path($template, $template_name, $template_path)
    {
        global $woocommerce;
        $_template = $template;
        if (!$template_path) {
            $template_path = $woocommerce->template_url;
        }

        $plugin_path = untrailingslashit(plugin_dir_path(__FILE__)) . '/template/WooCommerce/';

        // Look within passed path within the theme - this is priority
        $template = locate_template([$template_path . $template_name, $template_name]);
        if (!$template && file_exists($plugin_path . $template_name)) {
            $template = $plugin_path . $template_name;
        }

        if (!$template) {
            $template = $_template;
        }
        return $template;
    }

    public function phoe_order_item_action_td($show, $order)
    {
        $getData = get_option("phoe_order_item_actions");

        if (!isset($getData['exchange']['_enable_item_exchange']) && !isset($getData['refund']['_enable_item_refund'])) {
            return false;
        }

        if (isset($getData['exchange']['_enable_item_exchange']) && !$getData['exchange']['_enable_item_exchange'] && (isset($getData['refund']['_enable_item_refund']) && !$getData['refund']['_enable_item_refund'])) {
            return false;
        }

        $order_items = $order->get_items();
        $order_id = $order->get_id();

        $item_acted = phoe_count_order_item_acted($order_id);
        $orderItemsCount = count($order->get_items());

        if ($orderItemsCount - $item_acted < 2) {
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
        if (!in_array($orderStatus, ['completed', 'processing'])) {
            return false;
        }

        if ($orderStatus == 'processing') {
            if (!isset($getData['cancel']['_enable_item_cancel'])) {
                return false;
            }

            if (isset($getData['cancel']['_enable_item_cancel']) && !$getData['cancel']['_enable_item_cancel']) {
                return false;
            }
        }

        if ($orderStatus == 'processing' && sizeof($order->get_items()) > 1 && !is_page('checkout')) {
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

        if ($orderItemsCount - $item_acted < 2) {
            return false;
        }

        if (is_page('checkout')) {
            return false;
        }

        $getData = get_option("phoe_order_item_actions");
        if (isset($getData['cancel']['_enable_item_cancel']) && $getData['cancel']['_enable_item_cancel']) {
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
        if (!isset($getData['refund']['_enable_item_refund']) || !$getData['refund']['_enable_item_refund']) {
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

        if ($orderItemsCount - $item_acted < 2) {
            return false;
        }

        if ($orderStatus != 'completed') {
            return false;
        }
        return $show;
    }

    public function phoe_show_exchange_btn($show, $order, $item_id)
    {
        $getData = get_option("phoe_order_item_actions");
        if (!isset($getData['exchange']['_enable_item_exchange']) || !$getData['exchange']['_enable_item_exchange']) {
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

        if ($orderItemsCount - $item_acted < 2) {
            return false;
        }

        if ($orderStatus == 'completed') {
            return true;
        }
        return false;
    }
}
