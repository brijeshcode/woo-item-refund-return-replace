<?php
	// todo : check if givien order id is of the given user ?
	// return status for order item





    /*function phoe_cancel_order_item($order_id = '', $cancel_item_id = '', $calledby ='api') {
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
    }*/





    /*------------------till here -----------------*/

    function refundForCancel($order, $cancel_item_id, $reason)
    {
        return phoe_refundItemAmount($type = 'Cancled', $order, $cancel_item_id, $reason);
    }

    function phoe_orderRefund(){
        //p
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

    // request by customre
    function phoe_create_customer_order_item_request($type = ''){
        // $order_id, $cancel_item_id;
        $valid = validateCustomerItemRequest();
        if (true !==  $valid) {
            return $valid;
        }

        $item_id = $_POST['item_id'];
        $order_id = $_POST['order_id'];
        $reason = $_POST['reason'];

        insertNewRequest(ucfirst($type), $order_id, $item_id, $reason, $autoApproved = false );
        $data = [
            'status' => 'info',
            'message' => 'Request Submmited Successfull.'
        ];
        return $data;
    }

    function phoe_create_customer_order_request($type = ''){
        // $order_id, $cancel_item_id;
        $valid = validateCustomerItemRequest();
        if (true !==  $valid) {
            return $valid;
        }

        $order_id = $_POST['order_id'];
        $reason = $_POST['reason'];

        insertNewRequest(ucfirst($type), $order_id, '', $reason, $autoApproved = false );
        $data = [
            'status' => 'info',
            'message' => 'Request Submmited Successfull.'
        ];
        return $data;
    }



    function validateCustomerItemRequest()
    {
        $data = [
            'status' => 'error',
            'message' => 'Someting went wrong. Sorry request cannot be process'
        ];

        $type  = $_POST['request_item_action'];
        if (empty($type)) return $data;

        $data['message'] = 'Something went wrong. Order not found.';
        if (!isset($_POST['order_id']) || empty($_POST['order_id'])) return $data;


        $data['message'] = 'Something went wrong. Invalid request made.';
        if (!isset($_POST['request_item_action']) || empty($_POST['request_item_action'])) return $data;


        if (isset($_POST['request_for']) && empty($_POST['request_for'])) {
            $data['message'] = 'Please select valid Item.';
            if (!isset($_POST['item_id']) || empty($_POST['item_id'])) return $data;
        }

        $data['message'] = 'Please select a valid reason.';
        if (!isset($_POST['reason']) || empty($_POST['reason'])) return $data;

        if (isset($_POST['request_for']) && !empty($_POST['request_for'])) {

            if (is_req_exist(ucfirst($type) ,$_POST['order_id'], $_POST['request_for'],  $_POST['item_id'])) {
                $data['message'] = ucfirst($type).' request already there.';
                return $data;
            }
        }else{
            if (is_req_exist(ucfirst($type) ,$_POST['order_id'], 'Item', $_POST['item_id'])) {
                $data['message'] = ucfirst($type).' request already there.';
                return $data;
            }
        }

        return true;
    }

    // approve refund requert by admin
    function phoe_admin_approve_order_item_request($requestId, $type = 'Cancel'){

        $message = ['status' => 'success' , 'message' => 'Order has been updated.'];


        /* continue from here  ------------------*/
        $data = get_customer_order_item_requests($type, $requestId);
        $data = $data[0];
        $order = wc_get_order($data->order_id);
        $item_id = $data->item_id;
        $reason = $data->request_reason;
        $order_items = $order->get_items();
        $refund = array();

        $updated = phoe_change_request_status($requestId, 'Completed');
        if ($updated) {
            if ($type != 'Exchange') {
                if (phoe_refundItemAmount($type, $order, $item_id, $reason)) {
                    // add admin note on order
                    phoe_request_order_item_note($type, $requestId);
                    return $message;
                }
            }
        }else{
            $message = ['status' => 'error', 'message' => 'Request not fund.'];
            return $message;
        }
    }

    function phoe_admin_approve_order_request($requestId, $type = 'Cancel'){

        $message = ['status' => 'success' , 'message' => 'Order has been updated.'];

        $data = get_customer_order_requests($type, $requestId);
        $data = $data[0];
        $order = wc_get_order($data->order_id);

        $reason = $data->request_reason;
        $requestType = $data->request_type;


        $updated = phoe_change_request_status($requestId, 'Completed');
        if ($updated) {
            if ($type != 'Exchange') {
                // we will update order status to cancelled or refund
                if ($requestType == 'Refund') {
                    $order->update_status('wc-refunded', 'Order status changed by admin against order Refund request by customer. Request# ' . $requestId) ;
                    return $message;

                }elseif($requestType == 'Cancel'){
                    $order->update_status('wc-cancelled', 'Order status changed by admin against order Cancel request by customer. Request# ' . $requestId );
                    return $message;
                }

                return ['status' => 'error' , 'message' => 'Invalid request type.'];
            }
        }else{
            return ['status' => 'error' , 'message' => 'Sorry Process cannot be completed.'];
        }


    }

    // Denied cancel requert by admin
    function phoe_admin_item_request_change_status($requestId, $action, $type = 'Cancel'){

        // only admin allow to perform this action
        $valid = phoe_validate_admin_action($type, $requestId);
        if (false !==  $valid) return $valid;

        if ($action == 'Completed') {
            return phoe_admin_approve_order_item_request($requestId, $type);
        }else{
            if (phoe_change_request_status($requestId, $action)) {
                phoe_request_order_item_note($type, $requestId);
                return ['status' => 'success', 'message' => 'Request status changes Successfull.'];
            }
        }
    }

    function phoe_validate_admin_action($type, $requestId){
        $error = [
            'status' => 'error',
            'message' => 'Someting went wrong. Sorry request cannot be process'
        ];

        if (!is_admin()) {
            $error['message'] = 'Unauthorized access.';
            return $error;
        }

        $data = get_customer_order_item_requests($type, $requestId);

        if (empty($data)) {
            $error['message'] = 'No request found on given request id || Invalid request id';
            return $error;
        }

        if (!in_array($data[0]->request_status, requestsOptions())) {
            $error['message'] = 'Invalid request status. ' .$data[0]->request_status ;
            return $error;
        }

        if ($data[0]->request_type != $type) {
            $error['message'] = 'Invalid request.';
            return $error;
        }

        return false;
    }

    function phoe_change_order_request_status( $requestId, $action, $type = 'Cancel' ){
        // only admin allow to perform this action
        $valid = phoe_validate_admin_action_order($type, $requestId);
        if (false !==  $valid) return $valid;

        if ($action == 'Completed') {
            return phoe_admin_approve_order_request($requestId, $type);
        }else{
            if (phoe_change_request_status($requestId, $action)) {
                phoe_request_order_note($type, $requestId);
            }
        }

        return ['status' => 'success' , 'message' => 'Request Status updated'];
    }

    function phoe_validate_admin_action_order($type, $requestId){
        $error = [
            'status' => 'error',
            'message' => 'Someting went wrong. Sorry request cannot be process'
        ];

        if (!is_admin()) {
            $error['message'] = 'Unauthorized access.';
            return $error;
        }

        $data = get_customer_order_requests($type, $requestId);

        if (empty($data)) {
            $error['message'] = 'No request found on given request id || Invalid request id';
            return $error;
        }

        if (!in_array($data[0]->request_status, requestsOptions())) {
            $error['message'] = 'Invalid request status. ' .$data[0]->request_status ;
            return $error;
        }

        if ($data[0]->request_type != $type) {
            $error['message'] = 'Invalid request.';
            return $error;
        }

        return false;
    }

    function phoe_request_order_item_note($type, $requestId){

        $data = get_customer_order_item_requests($type, $requestId);
        $data = $data[0];
        if ($data->request_status != 'Denied')  return '';


        $order = wc_get_order($data->order_id);
        $item_id = $data->item_id;
        $order_items   = $order->get_items();

        foreach ($order_items as $item_key => $item) {

            if ($item_key != $item_id || $item->get_type() != 'line_item') {
                continue;
            }

            $name = $item->get_name();

            $note = " Admin {$data->request_status} customer {$type} request for Item, Name: {$name} from the order.";
            $order->add_order_note( $note );
            $order->save();
            break;
        }
    }

    function phoe_request_order_note($type, $requestId){

        $data = get_customer_order_requests($type, $requestId);
        $data = $data[0];
        if ($data->request_status != 'Denied')  return '';


        $order = wc_get_order($data->order_id);
        $item_id = $data->item_id;

        $note = "Admin {$data->request_status} customer {$type} request for Order.";
        $order->add_order_note( $note );
        $order->save();
    }


    function requestForm($item_id, $currentStatus){
        $c_html = '';
        $c_html .= '<form method="post">';
        $c_html .= '<input type="hidden" value="' . $item_id . '" name="item_id">';
        $c_html .= '<input type="hidden" value="phoe-wc-item-action" name="page">';
        $c_html .= '<input type="hidden" value="'.$_GET['tab'].'" name="tab">';

        $request_actions = requestsOptions();
        $c_html .= '<select name="action"  onchange="if(confirm(\'Continue?\')){this.form.submit()}">';
        foreach ($request_actions as $key => $request) {
            $selected = $currentStatus == $request ? 'selected' : '';
            $c_html .= '<option value="' .$request . '" '.$selected.' >' . $request . '</option>' ;
        }
        $c_html .= '</select>';
        $c_html .= '';
        $c_html .= '</form>';
        $c_html .= '';

        return $c_html;
    }

    function requestsOptions(){
        return ['Requested', 'Processing','Completed','Denied'];
    }

    function phoe_admin_notice($message){
        if (is_array($message)) {
            echo '<div class="notice notice-'.$message['status'].'  is-dismissible w-50 mb-4 ">
                <p>'.$message['message'].'</p>
            </div>';
        }
    }
?>