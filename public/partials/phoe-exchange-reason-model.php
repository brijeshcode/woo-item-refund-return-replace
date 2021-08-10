<div id="exchange" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Please Select Reason</h4>
      </div>
      <form method="post">
				<?php wp_nonce_field('phoe_order_item_action_cancel', 'phoe_order_item_action_cancel_nonce_field'); ?>
      	<input type="hidden" name="order_id" class="order_id" >
        <input type="hidden" name="item_id" class="order_item_id">
        <input type="hidden" name="request_for" class="request_for">
	      <div class="modal-body">
	      	<?php
	      	 	/*if ($order->get_status() == 'processing') {
	      	 		$type = 'cancel' ;
	      	 	}elseif($order->get_status() == 'completed'){
	      	 	}*/
	      	 		$type = 'exchange';
	      	 	$reasons = get_item_reasons($type);
	      	?>
	      	 <div>
	      	 	<?php foreach ($reasons as $key => $value): ?>
	      	 		<tags onclick="show_my_reasons('<?= sanitize_title($value['tag']); ?>' , '<?= $value['tag'] ?>')"><?= $value['tag'] ?></tags>
	      	 	<?php endforeach ?>
	      	 </div>
	      	<hr>
	      	<h2 class="selected-reason"></h2>
	        <ul class="list-group">
	        	<?php foreach ($reasons as $key => $value): ?>
	 						<?php foreach (explode(',', $value['reasons']) as $reasonVal): ?>
	        		<li class="list-group-item reason-items <?= sanitize_title($value['tag']) ?> ">
	        			<label> <input type="radio" name="reason" value="<?= $value['tag'].' | '.$reasonVal ?>"><?= $reasonVal ?></label>
	        		</li>
	 				<?php endforeach ?>

	        	<?php endforeach ?>
	        </ul>
	      </div>
	      <div class="modal-footer">
	      	<input type="hidden" name="request_item_action" id="request_item_action" value="exchange">
	        <button type="submit" class="btn btn-primary" value="exchange_request">Submit</button>
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      </div>
      </form>
    </div>

  </div>
</div>