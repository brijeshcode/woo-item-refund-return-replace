<?php
	// todo : check if givien order id is of the given user ?
	// return status for order item
	function phoe_getItemCancelStatus($order_id, $item_id, $text = true)
	{
		global $wpdb;
        $tb = $wpdb->prefix . "phoe_order_item_request";
        $query = " SELECT request_type, request_status FROM $tb where order_id = $order_id AND item_id = $item_id limit 1";

        $result = $wpdb->get_row($query);

        if (empty($result)) {
        	return false;
        }
        if ($text) {
            if ($result->request_type == 'Cancel') {
                if ($result->request_status == 'Submitted') {
                    $status = 'Cancel Requested';
                    return  $status;
                }elseif ($result->request_status == 'Denied') {
                    return 'Cancel Denied';
                }else{
                    return 'Cancelled';
                }
            }

            if ($result->request_type == 'Refund') {
                if ($result->request_status == 'Submitted') {
                    return 'Refund Requested';
                }elseif ($result->request_status == 'Denied') {
                    return 'Refund Denied';
                }else{
                    return 'Refunded';
                }
            }

            if ($result->request_type == 'Exchange') {
                if ($result->request_status == 'Submitted') {
                    return 'Exchange Requested';
                }elseif ($result->request_status == 'Denied') {
                    return 'Exchange Denied';
                }else{
                    return 'Exchanged';
                }
            }
        }
        return $result['request_status'];
	}

    function phoe_count_order_item_acted($order_id)
    {
        global $wpdb;
        $tb = $wpdb->prefix . "phoe_order_item_request";
        $query = " SELECT * FROM $tb where order_id = $order_id";

        $result = $wpdb->get_results($query);


        if (empty($result)) {
            return 0;
        }else{
            return count($result);
        }
    }

    function phoe_cancel_order_item($order_id = '', $cancel_item_id = '', $calledby ='api') {
        return '';  // no refund
        die('logic fail');
        if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
            $userId = $_GET['user_id'];
        }else{
            if ( !is_user_logged_in() )
                return '';

            $userId = get_current_user_id();
        }


        if ($order_id == '') {
            $order_id = $_GET['order_id'];
        }

        if ($cancel_item_id == '') {
            $cancel_item_id = $_GET['item_id']  ;
        }

        $order = wc_get_order($order_id);

        if( ! is_a( $order, 'WC_Order') ) {
            return 'Provided ID is not a WC Order';
            // return new WP_Error( 'wc-order', __( 'Provided ID is not a WC Order', 'yourtextdomain' ) );
        }

        if( 'refunded' == $order->get_status() ) {
            return 'Order has been already refunded';
            // return new WP_Error( 'wc-order', __( 'Order has been already refunded', 'yourtextdomain' ) );
        }
        if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
            $userId = $_GET['user_id'];
        }else{
            $userId = get_current_user_id();
        }

        if ($order->get_customer_id() != $userId || $userId != $order->get_user_id()) {
            return 'Invalid user';
        }

        if (is_req_exist($type = 'Cancel', $order_id, $cancel_item_id)) {
            return 'Already Cancelled';
        }

        $reason = ' no ';
        $order_items   = $order->get_items();
        $autoApproved  = true;

        // save the request in phoe_order_item_request table
        $id = insertNewRequest("Cancel", $order_id, $cancel_item_id, $reason, $autoApproved );

        // return if items already cancled ;
        if (!$id) {
            return 'Already Cancelled and stored';
        }

        $refund = array();
        if ($autoApproved && $id) {
            $refund = refundForCancel($order, $cancel_item_id, $reason);
        }

        // add note to order for cancel request

        foreach ($order_items as $item_id => $item) {

            if ($cancel_item_id != $item_id || $item->get_type() != 'line_item') {
                continue;
            }

            $name = $item->get_name();
            $note = " Customer cancel Item, Name: {$name} from the order.";
            $order->add_order_note( $note );
             $order->save();
        }
        return 'Completed';
    }

    function insertNewRequest($type = "Cancel", $order_id, $item_id, $reason, $autoApproved  = false ){
        if (is_req_exist($type , $order_id, $cancel_item_id)) {
            return 'Already Cancelled';
        }
        $status = 'Submitted';
        if ( $autoApproved ) {
            $status = 'Completed';
        }

        $arrPara = [
            'order_id' => $order_id,
            'item_id' => $item_id,
            'request_type' => $type,
            'request_status' => $status,
            'request_type' => $type,
            'request_reason' => $reason,
        ];

        global $wpdb;
        $tb = $wpdb->prefix . "phoe_order_item_request";
        $wpdb->insert($tb, $arrPara);
        $id = $wpdb->insert_id;
        return $id;
    }

    function  is_req_exist($type, $order_id, $item_id){
        global $wpdb;
        $tb = $wpdb->prefix . "phoe_order_item_request";
        $query = " SELECT * FROM $tb where order_id = $order_id AND item_id = $item_id and request_type = '$type' ";

        $result = $wpdb->get_row($query);

        if (empty($result)) {
            return false;
        }
        return true;
    }

    function refundForCancel($order, $cancel_item_id, $reason)
    {
        return phoe_refundItemAmount($type = 'Cancled', $order, $cancel_item_id, $reason);
    }

    function phoe_refundItemAmount($type = 'Cancled', $order, $cancel_item_id, $reason)
    {
        $order_id = $order->get_id();
        //
        $order_items   = $order->get_items();
        // Refund Amount
        $refund_amount = 0;

        // Prepare line items which we are refunding
        $line_items = array();

        $name = '';
        foreach ($order_items as $item_id => $item) {
            if ($cancel_item_id != $item_id || $item->get_type() != 'line_item') {
                continue;
            }

            $itemData = $item->get_data();

            $refund_amount = wc_format_decimal( $refund_amount ) + wc_format_decimal( $itemData['total'] ) + wc_format_decimal( $itemData['total_tax'] );

            $line_items[ $item_id ] = array(
                'qty' => $itemData['quantity'],
                'refund_total' => wc_format_decimal( $itemData['total']),
                'refund_tax' =>  $itemData['total_tax']
            );

            $name = $item->get_name();
            $refund_reason = $name . ' ' . $type;
        }

        if (!empty($line_items)) {
            $refund = wc_create_refund( array(
            'amount'         => $refund_amount,
            'reason'         => $refund_reason,
            'order_id'       => $order_id,
            'line_items'     => $line_items,
            'refund_payment' => false,
            'restock_items'  => true,
            ));
        return $refund;
        }
    }

    function get_item_reasons($type = 'refund'){
        $getData = get_option("phoe_order_item_actions");
        if (isset($getData[$type]['reason'])) {
            // echo "<pre>"; print_r($getData[$type]['reason']); echo "</pre>"; die();
            return $getData[$type]['reason'];
        }
    }

    function get_cancel_requests( $request_id = ''){
        global $wpdb;
        $tb = $wpdb->prefix . "phoe_order_item_request";
        $con = '';
        if ($request_id != '') {
            $con = " AND $tb.id = $request_id ";
        }
        $query = "
            SELECT *, i.order_item_name FROM $tb
            join wp_woocommerce_order_items i on i.order_item_id = $tb.item_id
            where request_type = 'Cancel' $con
            Order by  $tb.created_at asc

        ";

        $result = $wpdb->get_results($query);
        if (empty($result)) {
            return false;
        }
        return $result ;
    }

    function get_customer_order_item_requests($type = 'Cancel',  $request_id = ''){
        global $wpdb;
        $tb = $wpdb->prefix . "phoe_order_item_request";
        $con = '';
        if ($request_id != '') {
            $con = " AND $tb.id = $request_id ";
        }
        $query = "
            SELECT *, i.order_item_name FROM $tb
            join wp_woocommerce_order_items i on i.order_item_id = $tb.item_id
            where request_type = '{$type}' $con
            Order by  $tb.created_at asc
        ";

        $result = $wpdb->get_results($query);
        if (empty($result)) {
            return false;
        }
        return $result ;
    }

    // request by customre
    function phoe_customer_order_item_request($type){
        // $order_id, $cancel_item_id;
        if (empty($type))
        return '';

        if (!isset($_POST['order_id']) || empty($_POST['order_id']))
        return '';

        if (!isset($_POST['reason']) || empty($_POST['reason']))
        return '';

        if (!isset($_POST['item_id']) || empty($_POST['item_id']))
        return '';

        if (!isset($_POST['request_item_action']) || empty($_POST['request_item_action']))
        return '';

        if ($_POST['request_item_action'] != $type)
        return '';

        $item_id = $_POST['item_id'];
        $order_id = $_POST['order_id'];

        if (is_req_exist(ucfirst($type) , $order_id, $item_id)) {
            return ucfirst($type).' request already there.';
        }

        $reason = $_POST['reason'];
        insertNewRequest(ucfirst($type), $order_id, $item_id, $reason, $autoApproved = false );
    }

    // approve refund requert by admin
    function phoe_admin_approve_order_item_request($requestId, $type = 'Cancel'){
        // only admin allow to perform this action
        if (!is_admin()) {
            return 'not allowed';
        }

        $data = get_customer_order_item_requests($type, $requestId);

        if (empty($data)) {
            return 'No request found on given request id || invalid request id';
        }
        if ($data[0]->request_status != 'Submitted') {
            return 'invalid request.';
        }

        if ($data[0]->request_type != $type) {
            return 'invalid request.';
        }

        $order = wc_get_order($data[0]->order_id);
        $item_id = $data[0]->item_id;
        $reason = $data[0]->request_reason;
        $order_items = $order->get_items();
        $refund = array();

        global $wpdb;
        $tb = $wpdb->prefix . "phoe_order_item_request";
        $where = ['id' => $requestId];
        $data = ['request_status' => 'Completed'];

        $updated = $wpdb->update( $tb, $data, $where );
        if ($type != 'Exchange') {
            phoe_refundItemAmount($type, $order, $item_id, $reason);
        }


        // add note to order for cancel request
        foreach ($order_items as $item_id => $item) {

            if ($item_id != $item_id || $item->get_type() != 'line_item') {
                continue;
            }

            $name = $item->get_name();
            $note = " Admin Approved customer {$type} request for Item, Name: {$name} from the order.";
            $order->add_order_note( $note );
            $order->save();
            break;
        }

    }

    // Denied cancel requert by admin
    function phoe_admin_denied_order_item_request($requestId, $type = 'Cancel'){
        // only admin allow to perform this action
        if (!is_admin()) {
            return 'Unauthorized access.';
        }

        $data = get_cancel_requests($requestId);
        if (empty($data)) {
            return 'No request found on given request id || Invalid request id';
        }

        if ($data[0]->request_status != 'Submitted') {
            return 'invalid request.';
        }

        if ($data[0]->request_type != $type) {
            return 'invalid request.';
        }

        global $wpdb;
        $tb = $wpdb->prefix . "phoe_order_item_request";
        $where = ['id' => $requestId];
        $data = ['request_status' => 'Denied'];
        $updated = $wpdb->update( $tb, $data, $where );
        if ($updated) {
            $data = get_cancel_requests($requestId);
            $order = wc_get_order($data[0]->order_id);
            $item_id = $data[0]->item_id;
            $order_items   = $order->get_items();
            foreach ($order_items as $item_id => $item) {

                if ($item_id != $item_id || $item->get_type() != 'line_item') {
                    continue;
                }

                $name = $item->get_name();
                $note = " Admin Denied customer {$type} request for Item, Name: {$name} from the order.";
                $order->add_order_note( $note );
                $order->save();
                break;
            }
        }

    }

?>