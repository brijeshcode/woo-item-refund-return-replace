<?php
	function getRequestTemplate()
	{
		// code...
	}

	function getTemplates($type)
	{
		$status = [
			'Completed' => 'completed_template',
			'Requested' => 'request_template',
			'Processing' => 'processing_template',
			'Denied' => 'denied_template',
		];
		return $template = phoe_settings('email', $status[$type]);
	}

	function replaceEmailBodyTags($body, $request_id){

		$request = phoe_get_request($request_id);

		$order = wc_get_order($request->order_id);

		$product_name = '';
		if ($request->request_for == 'Item') {
			$request = get_customer_order_item_requests();
			$product_name = $request[0]->order_item_name;
		}

		$tags =[
			// ['tag' => '{{ customer_last_name }}', 'value' => $order->get_billing_last_name()],
			['tag' => '{{ site_title }}', 'value' => get_bloginfo( 'name' )],
			['tag' => '{{ customer_full_name }}', 'value' => $order->get_billing_first_name() . ' '. $order->get_billing_last_name()],
			['tag' => '{{ customer_name }}', 'value' => $order->get_billing_first_name() . ' '. $order->get_billing_last_name()],
			['tag' => '{{ customer_first_name }}', 'value' => $order->get_billing_first_name()],
			['tag' => '{{ customer_email }}', 'value' => $order->get_billing_email()],
			['tag' => '{{ order_no }}', 'value' => $order->get_id()],
			['tag' => '{{ product_name }}', 'value' => $product_name],
		];

		foreach ($tags as $key => $value) {
			$body = str_replace($value['tag'], $value['value'], $body);
		}
		return $body;
	}

	function emailContent(){
	}

	function sendMail($request_id){

		$request = phoe_get_request($request_id);
		if (!$request ) return '';

		$email_settings = getTemplates($request->request_status);

		if (!isset($email_settings['enable'])) return true;

		$order = wc_get_order($request->order_id);

		$subject = $email_settings['subject'];
		$mail_body = replaceEmailBodyTags($email_settings['body'], $request_id);
		$message = $mail_body;

		$from = get_bloginfo('admin_email');
		$site_name =  get_bloginfo( 'name' );

		$to = $order->get_billing_email();

		$headers[] = 'From: '.$site_name.' <'.$from.'>';
		// $headers[] = 'Cc: John Q Codex <jqc@wordpress.org>';
		$headers[] = 'Content-Type: text/html; charset=UTF-8';

    	return wp_mail( $to, $subject, $message, $headers ) ? true : false;
	}


?>