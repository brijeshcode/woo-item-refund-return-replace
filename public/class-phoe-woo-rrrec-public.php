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

         wp_enqueue_style($this->plugin_name. 'bulma', 'https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css', [], $this->version, 'all');
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

    // called by filter
    public function phoe_order_item_action_td($show, $order)
    {
        /*$actions = $this->phoe_add_action_item_buttons($order);

        if (is_array($actions) && !empty($actions)) {
            return $actions;
        }*/

        //td to show order action
        $getData = get_option("phoe_order_item_actions");


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

            $req_status = phoe_getRequestStatus($order_id, $item_id);
            if ($req_status) {
                return true;
            }
        }
        return $show;
    }

    // called by filter
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

        $req_status = phoe_getRequestStatus($order_id, $item_id);

        if ($req_status) {
            return true;
        }

        return false;
    }


    // called by filter
    public function phoe_show_refund_btn($show, $order, $item_id)
    {
        $getData = get_option("phoe_order_item_actions");
        if (!isset($getData['refund']['_enable_item_refund']) || !$getData['refund']['_enable_item_refund']) {
            return false;
        }

        $orderStatus = $order->get_status();

        $order_id = $order->get_id();
        $req_status = phoe_getRequestStatus($order_id, $item_id);
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

    // called by filter
    public function phoe_show_exchange_btn($show, $order, $item_id)
    {
        $getData = get_option("phoe_order_item_actions");
        if (!isset($getData['exchange']['_enable_item_exchange']) || !$getData['exchange']['_enable_item_exchange']) {
            return false;
        }

        $orderStatus = $order->get_status();

        $order_id = $order->get_id();
        $req_status = phoe_getRequestStatus($order_id, $item_id);
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

    public function phoe_add_action_buttons($action , $order)
    {
        $status = $order->get_status();
        if (in_array($status, ['cancelled', 'refunded'])) return $action;

        $order_id = $order->get_id();
        $types = ['refund', 'cancel', 'exchange'];
        foreach ($types as $key => $type) {
            if (canShowPhoeOrderBtns($order, $type)) {
                $action[$type]  = [
                    'url' => '',
                    'name' => ucfirst($type),
                    'id' => $type,
                    'order_id' => $order_id
                ];
            }
        }

        return $action;
    }

    public function phoe_add_action_item_buttons( $btns, $order, $item_id)
    {
        $btns = array();
        $status = $order->get_status();

        if (in_array($status, ['cancelled', 'refunded'])) return $btns;

        $order_id = $order->get_id();

        $types = ['refund', 'cancel', 'exchange'];
        foreach ($types as $key => $type) {
            if (canShowOrderItemBtns($order, $item_id, $type)) {
                $btns[$type]  = [
                    'url' => '',
                    'name' => ucfirst($type),
                    'id' => $type,
                    'order_id' => $order_id
                ];
            }
        }

        return $btns;
    }
}
