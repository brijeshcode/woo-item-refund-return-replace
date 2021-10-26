<?php
	function phoe_getRequestStatus($order_id, $item_id, $text = true)
	{

        $orderAction = check_order_request_exist($order_id);
        if ($orderAction) return '';

		global $wpdb;
        $tb = $wpdb->prefix . "phoe_order_item_request";
        $con = 1;
        if ($item_id == '') {
            $con = "item_id = $item_id";
        }

        $query = " SELECT request_type, request_status FROM $tb where order_id = $order_id AND $con limit 1 ";

        $result = $wpdb->get_row($query);

        if (empty($result)) {
        	return false;
        }

        if ($text) {
            if ($result->request_type == 'Cancel') {
                if ($result->request_status == 'Completed') {
                    return 'Cancelled';
                }else{
                    return 'Cancel '. $result->request_status;
                }
            }

            if ($result->request_type == 'Refund') {
                if ($result->request_status == 'Completed') {
                    return 'Refunded';
                }else{
                    return 'Refund '. $result->request_status;
                }
            }

            if ($result->request_type == 'Exchange') {
                if ($result->request_status == 'Completed') {
                    return 'Exchanged';
                }else{
                    return 'Exchange '. $result->request_status;
                }
            }
        }
        return '';
	}

	function phoe_count_order_item_acted($order_id)
    {
        global $wpdb;
        $tb = $wpdb->prefix . "phoe_order_item_request";
        $query = " SELECT count(id) as item_requested FROM $tb where order_id = $order_id";

        $result = $wpdb->get_row($query);
        return $result->item_requested;
    }

    function  is_req_exist($type, $order_id, $request_for = 'Item', $item_id){

        global $wpdb;
        $tb = $wpdb->prefix . "phoe_order_item_request";
        $con = '';
        if ($item_id != '') {
            $con = " AND item_id = $item_id ";
        }

        if ($item_id != 0) {
            $con = " AND item_id = $item_id ";
        }

       $query = " SELECT * FROM $tb where order_id = $order_id  $con and request_for = '$request_for' ";

        $result = $wpdb->get_row($query);
        return empty($result) ? false: true;
    }

    function check_order_request_exist($order_id){

        global $wpdb;
        $tb = $wpdb->prefix . "phoe_order_item_request";
       $query = " SELECT * FROM $tb where order_id = $order_id and request_for = 'Order' ";
        $result = $wpdb->get_row($query);
        return empty($result) ? false: true;
    }

    function get_cancel_requests( $request_id = ''){
        return get_customer_order_item_requests('Cancel', $request_id);
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

    function get_customer_order_requests($type = 'Cancel',  $request_id = ''){
        global $wpdb;
        $tb = $wpdb->prefix . "phoe_order_item_request";
        $con = '';
        if ($request_id != '') {
            $con = " AND $tb.id = $request_id ";
        }
        $query = "
            SELECT * FROM $tb
            where request_type = '{$type}' $con AND request_for = 'Order'
            Order by  $tb.created_at asc
        ";

        $result = $wpdb->get_results($query);
        if (empty($result)) {
            return false;
        }
        return $result ;
    }

    function phoe_get_request($request_id = ''){
        if($request_id == '') return false;

        global $wpdb;
        $tb = $wpdb->prefix . "phoe_order_item_request";

        $query = " SELECT * FROM $tb where id = '{$request_id}' ";

        $request = $wpdb->get_row($query);
        if (empty($request)) {
            return false;
        }
        return $request ;
    }

	function insertNewRequest($type = "Cancel", $order_id, $item_id, $reason, $autoApproved  = false ){

        $request_for = 'Item';
        if ($item_id == '') {
            $request_for = 'Order';
        }

        if (isset($_GET['request_for']) && !empty($_GET['request_for'])) {
            $request_for = $_GET['request_for'];
        }

        if (isset($_POST['request_for']) && !empty($_POST['request_for'])) {
            $request_for = $_POST['request_for'];
        }

        if (is_req_exist($type , $order_id, $request_for, $item_id)) {
            return 'Already Cancelled';
        }

        if (empty($order_id) || empty($reason) || empty($type)) {
            return 'Invalid request';
        }

        $status = 'Requested';
        if ( $autoApproved ) {
            $status = 'Completed';
        }

        $arrPara = [
            'order_id' => $order_id,
            'item_id' => $item_id,
            'request_for' => $request_for,
            'request_from' => 'web',
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


    function phoe_change_request_status($request_id, $status){
        global $wpdb;
        $tb = $wpdb->prefix . "phoe_order_item_request";
        $where = ['id' => $request_id];
        $data = ['request_status' => $status];
        $updated = $wpdb->update( $tb, $data, $where );
        return $updated ? true : false;
    }

    function phoe_count_incomplete_requests($type, $for){
        global $wpdb;
        $tb = $wpdb->prefix . "phoe_order_item_request";

       $query = " SELECT count(id) as requests FROM $tb where request_for = '$for' And request_type = '$type' AND request_status <> 'Completed' AND request_status <> 'Denied' ";

        $result = $wpdb->get_row($query);
        return $result->requests;
    }
?>