<?php
	/*
	* Here we check all the admin options and validate buttons to show or now.
	*/



    // check cancel order option enable and all other conditions fullfild
    function canShowPhoeOrderBtns($order, $type)
    {
        $settings = phoe_settings($type);
        // check is enabled
        $enabled = checkEnalbeOrderAction($settings, $type);
        if (!$enabled)  return $enabled;


        $validStatus = checkOrderStatuValid($order, $settings, $type);
        if (!$validStatus)  return $validStatus;

        if ($type != 'cancel') {
            $validDays = checkOrderUnderDaysLimit($order, $settings, $type);
            if (!$validDays)  return $validDays;
        }

        // check if already requested or not
        $requested = checkIsRequestedOrder($order);
        if (!$requested)  return $requested;

        return true;
    }


    // order button validation functionality
    function checkEnalbeOrderAction($settings , $type )
    {
        $optionKey = '';

        if ($type == 'cancel') $optionKey = '_enable_order_cancel';
        if ($type == 'exchange') $optionKey = '_enable_order_exchange';
        if ($type == 'refund') $optionKey = '_enable_order_refund';

        if ($optionKey == '')   return false;

        if (isset($settings[$optionKey]) && $settings[$optionKey] == 1 ) return true;

        return false;
    }

    // check order status matches the admin selection
    function checkOrderStatuValid($order, $settings, $type){

        $optionKey = '';

        if ($type == 'cancel') $optionKey = '_enable_cancel_on_status';
        if ($type == 'exchange') $optionKey = '_enable_exchange_on_status';
        if ($type == 'refund') $optionKey = '_enable_refund_on_status';

        if ($optionKey == '')   return false;
        $status = $order->get_status();

        if (isset($settings[$optionKey])
                && strtolower(wc_get_order_status_name($settings[$optionKey])) == strtolower($status)
            ) return true;

        return false;
    }

    function checkOrderUnderDaysLimit($order, $settings, $type){
        $optionKey = '';

        if ($type == 'cancel') return true;
        if ($type == 'exchange') $optionKey = '_exchange_valid_days';
        if ($type == 'refund') $optionKey = '_refund_valid_days';

        if ($optionKey == '')   return false;

        if (isset($settings[$optionKey]) && !empty($settings[$optionKey])) {

            $daysLimit = $settings[$optionKey];
            $completedDate = date('d-m-Y',strtotime($order->get_date_completed()));
            $validDate = date('d-m-Y', strtotime($completedDate.'+'.$daysLimit.' days'));

            if (strtotime($validDate) > time()) {
                return true;
            }else{ ;
                return false;
            }
        }
        return true;
    }

    // order Item button button validation functionality



    // this will check for cancel item buttons
    function canShowOrderItemBtns($order, $item_id, $type){
    	// check if enable or not
    	$settings = phoe_settings($type);

        // check is enabled
        $enabled = checkEnalbeOrderItemAction($settings, $type);
        if (!$enabled)  return $enabled;

        $validStatus = checkOrderStatuValid($order, $settings, $type);
        if (!$validStatus)  return $validStatus;

        // check if already requested or not
        $requested = checkIsRequestedItem($order, $item_id, $type);
        if (!$requested)  return $requested;

        // no action on item if item count less then one
        if ($order->get_item_count() < 2 ) return false;

        if ($type != 'cancel') {
            $validDays = checkOrderUnderDaysLimit($order, $settings, $type);
            if (!$validDays)  return $validDays;
        }

        // main changes
        $order_id = $order->get_id();
        $orderTypeRequest = check_order_request_exist($order_id);
        if ($orderTypeRequest)  return !$orderTypeRequest;

    	return true;
    }

    function checkEnalbeOrderItemAction($settings , $type )
    {
        $optionKey = '';

        if ($type == 'cancel') $optionKey = '_enable_item_cancel';
        if ($type == 'exchange') $optionKey = '_enable_item_exchange';
        if ($type == 'refund') $optionKey = '_enable_item_refund';

        if ($optionKey == '') return false;

        if (isset($settings[$optionKey]) && $settings[$optionKey] == 1 ) return true;

        return false;
    }

    function checkIsRequestedItem($order, $item_id, $type, $for = "Item")
    {
        return is_req_exist($type, $order->get_id(),  $for ,$item_id ) ? false : true;
    }

    function checkIsRequestedOrder($order)
    {
        return check_order_request_exist( $order->get_id()) ? false : true;
    }
?>