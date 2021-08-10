<div id="refund" class="modal fade" role="dialog">
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
                    $type = 'refund';
                    $reasons = get_item_reasons($type);
                    ?>
                    <div>
                        <?php foreach ($reasons as $key => $value): ?>
                        	<tags onclick="show_my_reasons('<?= sanitize_title($value['tag']) ?>' , '<?= $value['tag'] ?>')"><?= $value['tag'] ?></tags>
                        <?php endforeach; ?>

                    </div>
                    <hr />
                    <h2 class="selected-reason"></h2>
                    <ul class="list-group">
                        <?php foreach ($reasons as $key => $value): ?>
                        <?php foreach (explode(',', $value['reasons']) as $reasonVal): ?>
                        <li class="list-group-item reason-items <?= sanitize_title($value['tag']) ?>">
                            <label>
                                <input type="radio" name="reason" value="<?= $value['tag'] . ' | ' . $reasonVal ?>" />
                                <?= $reasonVal ?>
                            </label>
                        </li>
                        <?php endforeach; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="request_item_action" value="refund" />
                    <button type="submit" class="btn btn-primary" value="refund_request">Submit</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>


<style type="text/css">
tags {
    background: #d9edf7;
    padding: 5px;
    margin: 5px 4px 9px;
    font-size: 20px;
    font-weight: bolder;
    display: inline-block;
    cursor: pointer;
    border-radius: 2px 10px 9px 2px;
    box-shadow: 1px 1px 5px 1px #988a8a;
}
.reason-items{
	display: none;
}
</style>
