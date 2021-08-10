<?php


    // get all plugin settings
    function phoe_settings($type = '', $optionKey = ''){

        $settings = get_option("phoe_order_item_actions");

        if ($type != '' && $optionKey) return $settings[$type][$optionKey];

        if ($type != '') return $settings[$type];

        return $settings;
    }

    function get_item_reasons($type = 'refund'){
        return phoe_settings($type,'reason');
    }
?>