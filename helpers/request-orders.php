<?php
	// all functions related to order requests

	function phoe_change_order_request_status( $requestId, $action, $type = 'Cancel' ){
        // only admin allow to perform this action
        $valid = phoe_validate_admin_action_order($type, $requestId);
        if (false !==  $valid) return $valid;

        if ($action == 'Completed') {
            $response =  phoe_approve_order_request($requestId, $type);
        }else{
            if (phoe_change_request_status($requestId, $action)) {
                phoe_create_order_note($type, $requestId);
                $response = ['status' => 'success' , 'message' => 'Order has been updated.'];
            }
        }

        if ($response['status'] == 'success') sendMail($requestId);
        return $response;
    }


    function phoe_approve_order_request($requestId, $type = 'Cancel'){

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

    // creates order note for order request status change
	function phoe_create_order_note($type, $requestId){

        $data = get_customer_order_requests($type, $requestId);
        $data = $data[0];
        if ($data->request_status != 'Denied')  return '';


        $order = wc_get_order($data->order_id);
        $item_id = $data->item_id;

        $note = "Admin {$data->request_status} customer {$type} request for Order.";
        $order->add_order_note( $note );
        $order->save();
    }


    // valid request type is valid order request or not.
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

    function phoe_create_customer_order_request($type = ''){
        // $order_id, $cancel_item_id;
        $valid = validateCustomerOrderRequest();
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

        sendMail($request_id);
        return $data;
    }

    function validateCustomerOrderRequest()
    {
        $data = [
            'status' => 'error',
            'message' => 'Someting went wrong. Sorry request cannot be process'
        ];


        $data['message'] = 'Something went wrong. Order not found.';
        if (!isset($_POST['order_id']) || empty($_POST['order_id'])) return $data;


        $data['message'] = 'Please select a valid reason.';
        if (!isset($_POST['reason']) || empty($_POST['reason'])) return $data;

        if (check_order_request_exist($_POST['order_id'])) {
            $data['message'] = ucfirst($type).' request already there.';
            return $data;
        }

        return true;
    }

?>