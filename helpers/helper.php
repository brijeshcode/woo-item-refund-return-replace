<?php
	// todo : check if givien order id is of the given user ?
	// return status for order item
    function refundForCancel($order, $cancel_item_id, $reason)
    {
        return phoe_refundItemAmount($type = 'Cancled', $order, $cancel_item_id, $reason);
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
                <p>'. __($message['message'], 'wc-item-actions') .'</p>
            </div>';
        }
    }
?>