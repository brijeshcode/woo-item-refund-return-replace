<?php
if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
	return '';

	add_action( 'rest_api_init', '_cancel_order_item_pro');

	function _cancel_order_item_pro()
	{
		if ( version_compare( WC_VERSION, '3.6.0', '>=' ) && WC()->is_rest_api_request() == 'wc/' ) {
			require_once( WC_ABSPATH . 'includes/wc-cart-functions.php' );
			require_once( WC_ABSPATH . 'includes/wc-notice-functions.php' );
 			if ( null === WC()->session ) {
				$session_class = apply_filters( 'woocommerce_session_handler', 'WC_Session_Handler' );
 				// Prefix session class with global namespace if not already namespaced
				if ( false === strpos( $session_class, '\\' ) ) {
					$session_class = '\\' . $session_class;
				}
 				WC()->session = new $session_class();
				WC()->session->init();
			}
		}



		register_rest_route( 'wc/v2','/order-item/action-settings', array(
			'methods'  =>WP_REST_Server::ALLMETHODS,
			'callback' => 'phoe_order_item_action_settings' ,
			'args'     => array(
				'thumb' => array(
					'default' => null
				),
			),
			'permission_callback' => '__return_true'

		));

		register_rest_route( 'wc/v2','/order-item/cancel', array(
			'methods'  =>WP_REST_Server::ALLMETHODS,
			'callback' => 'phoe_order_item_cancel_request',
			'args'     => array(
				'thumb' => array(
					'default' => null
				),
			),
			'permission_callback' => '__return_true'

		));

		register_rest_route( 'wc/v2','/order-item/refund', array(
			'methods'  =>WP_REST_Server::ALLMETHODS,
			'callback' => 'phoe_order_item_refund_request' ,
			'args'     => array(
				'thumb' => array(
					'default' => null
				),
			),
			'permission_callback' => '__return_true'

		));

		register_rest_route( 'wc/v2','/order-item/exchange', array(
			'methods'  =>WP_REST_Server::ALLMETHODS,
			'callback' => 'phoe_order_item_exchange_request' ,
			'args'     => array(
				'thumb' => array(
					'default' => null
				),
			),
			'permission_callback' => '__return_true'

		));

		register_rest_route( 'wc/v2','/order-item/status', array(
			'methods'  =>WP_REST_Server::ALLMETHODS,
			'callback' => 'phoe_order_item_status' ,
			'args'     => array(
				'thumb' => array(
					'default' => null
				),
			),
			'permission_callback' => '__return_true'
		));

		register_rest_route( 'wc/v2','/order-items/status', array(
			'methods'  =>WP_REST_Server::ALLMETHODS,
			'callback' => 'phoe_order_items_status' ,
			'args'     => array(
				'thumb' => array(
					'default' => null
				),
			),
			'permission_callback' => '__return_true'
		));
	}

	function phoe_cancel_order_item_api(){

		if ((isset($_GET['item_id_']) && !empty($_GET['item_id_']))&& (isset($_GET['order_id_']) && !empty($_GET['order_id_'] ) ) )
		{
			$cancel_item_id = $_GET['item_id_'];
            $order_id = $_GET['order_id_'];

	        $canclled =  phoe_cancel_order_item($order_id, $cancel_item_id);

			if ($canclled != 'Completed') {
				$response = ['status' => 0];
				$response['data'] = 'Error: '. $canclled;
				return new WP_REST_Response( $response, 200 );
			}

			$response = ['status' => 1];
			$response['data'] = 'Item Canclled successfull.';
			return new WP_REST_Response( $response, 200 );
		}

		$response = ['status' => 0];
		$response['data'] = 'Error: Incomplete resource.' ;
		return new WP_REST_Response( $response, 200 );
	}

	function phoe_order_item_action_settings(){
		$response = ['status' => 1];
		$getData = get_option("phoe_order_item_actions");
		$response['data'] = $getData;
		return new WP_REST_Response( $response, 200 );
	}

	function phoe_order_item_refund_request(){
        return phoe_make_request('refund');
	}

	function phoe_order_item_cancel_request(){
        return phoe_make_request('cancel');
	}

	function phoe_order_item_exchange_request(){
        return phoe_make_request('exchange');
	}

	function phoe_make_request($type){
        $response = performOrderItemActionRequest($type);
        $res_type = $response['res_type'];
        unset($response['res_type']);
        return new WP_REST_Response( $response, $res_type );
	}

	function phoe_order_item_status()
	{
		if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
			$response = ['status' => 0];
			$response['data'] = 'Error: Insufficient Ressource.' ;
			return new WP_REST_Response( $response, 400 );
		}

		if (!isset($_GET['item_id']) || empty($_GET['item_id'])) {
			$response = ['status' => 0];
			$response['data'] = 'Error: Insufficient Ressource.' ;
			return new WP_REST_Response( $response, 400 );
		}

		$order_id = $_GET['order_id'];
		$item_id = $_GET['item_id'];

		$data = phoe_getRequestStatus($order_id, $item_id);

		$response = ['status' => 1];
		$response['data'] = $data ;
		return new WP_REST_Response( $response, 200 );
	}

	function phoe_order_items_status()
	{
		if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
			$response = ['status' => 0];
			$response['data'] = 'Error: Insufficient Ressource.' ;
			return new WP_REST_Response( $response, 400 );
		}

		$order_id = $_GET['order_id'];

		$data = phoe_query_order_items_request($order_id);
		if (!$data) {
			$response = ['status' => 0];
			$response['data'] = 'No action requested, or invalid resource provided.' ;
			return new WP_REST_Response( $response, 200 );
		}

		$response = ['status' => 1];
		$response['data'] = $data ;
		return new WP_REST_Response( $response, 200 );
	}

	// permform order item requests
	function performOrderItemActionRequest($type){

		$valid = validateRequestActions($type);
		if (!is_bool($valid)  ) {
			return $valid;
		}

		if (!$valid) {
			$response = ['status' => 0];
			$response['data'] = 'Error: Insufficient Ressource, Validation fails.' ;
			if (isset($_POST['dev'])) {
				$response['resource'] = 'Order ID missing.' ;
			}
			$response['res_type'] = '400';
			return $response;
		}
        $order_id = $_POST['order_id'];
        $item_id = $_POST['item_id'];
        $reason = $_POST['reason'];

		if (function_exists('insertNewRequest')) {
			if (is_req_exist($type , $order_id, 'Item', $cancel_item_id)) {
				$response = ['status' => 0];
				$response['data'] = 'Request allready in system.';
				$response['res_type'] = '208';
				return $response;
	        }

			$id = insertNewRequest(ucfirst($type), $order_id, $item_id, $reason, $autoApproved = false );

			if ($id) {
				$response = ['status' => 1];
				$response['data'] = 'Request Submmited Successfull.';
				$response['res_type'] = '201';
				return $response;
			}else{
				$response = ['status' => 0];
				$response['data'] = 'Request Not Submmited.';
				$response['res_type'] = '203';
				return $response;
			}
		}else{
			$response = ['status' => 0];
			$response['data'] = 'Error: Insert request Not implemented.' ;
			if (isset($_POST['dev'])) {
				$response['resource'] = 'insertNewRequest method not found.' ;
			}
			$response['res_type'] = '501';
			return $response;
		}
	}

	// validate order item action requests
	function validateRequestActions($type){

		if (!isset($_POST['order_id']) || empty($_POST['order_id'])){
			$response = ['status' => 0];
			$response['data'] = 'Error: Insufficient Ressource.' ;
			if (isset($_POST['dev'])) {
				$response['resource'] = 'Order ID missing.' ;
			}
			$response['res_type'] = '400';
			return $response;
		}

        if (!isset($_POST['reason']) || empty($_POST['reason'])){
        	$response = ['status' => 0];
			$response['data'] = 'Error: Insufficient Ressource.' ;
			if (isset($_POST['dev'])) {
				$response['resource'] = 'Reason missing.' ;
			}
			$response['res_type'] = '400';
			return $response;
        }

        if (!isset($_POST['item_id']) || empty($_POST['item_id'])){
        	$response = ['status' => 0];
			$response['data'] = 'Error: Insufficient Ressource.' ;
			if (isset($_POST['dev'])) {
				$response['resource'] = 'Item ID missing.' ;
			}
			$response['res_type'] = '400';
			return $response;
        }

        if (!isset($_POST['request_item_action']) || empty($_POST['request_item_action'])){
        	$response = ['status' => 0];
			$response['data'] = 'Error: Insufficient Ressource.' ;
			if (isset($_POST['dev'])) {
				$response['resource'] = 'request_item_action' ;
			}
			$response['res_type'] = '400';
			return $response;
        }

        if ($_POST['request_item_action'] != $type){
        	$response = ['status' => 0];
			$response['data'] = 'Error: Invalid Request.' ;
			if (isset($_POST['dev'])) {
				$response['resource'] = 'request_item_action not matches request type' ;
			}
			$response['res_type'] = '501';
			return $response;
        }
        return true;
	}
?>