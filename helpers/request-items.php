<?php
	// all functions related to order item requests

	// request by customre
    function phoe_create_customer_order_item_request($type){
        // $order_id, $cancel_item_id;
        $valid = validateCustomerItemRequest();

        if (true !==  $valid) return $valid;

        $item_id = $_POST['item_id'];
        $order_id = $_POST['order_id'];
        $reason = $_POST['reason'];
        $response = [ 'status' => 'info', 'message' => 'Request Submmited Successfull.' ];

        $request_id = insertNewRequest(ucfirst($type), $order_id, $item_id, $reason, $autoApproved = false );
        if (!$request_id)  return $response = ['status' => 'error', 'message' => 'Request Fail.'];

        sendMail($request_id);
        return $response;
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


	// Denied cancel requert by admin
    function phoe_admin_item_request_change_status($requestId, $action, $type = 'Cancel'){
        // only admin allow to perform this action
        $valid = phoe_validate_admin_action($type, $requestId);
        if (false !==  $valid) return $valid;

        if ($action == 'Completed') {
            $response =  phoe_admin_approve_order_item_request($requestId, $type);
        }else{
            if (phoe_change_request_status($requestId, $action)) {
                phoe_request_order_item_note($type, $requestId);
                $response =  ['status' => 'success', 'message' => 'Request status changes Successfull.'];
            }
        }

        if ($response['status'] == 'success') sendMail($requestId);
        return $response;
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

    function phoe_admin_approve_order_item_request($requestId, $type = 'Cancel'){

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
                }
            }
            $message = ['status' => 'success' , 'message' => 'Order has been updated.'];
        }
        $message = ['status' => 'error', 'message' => 'Request not fund.'];
        return $message;
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


?>