<div class="modal reason-select exchange">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title">Please Select A Exchange Reason</p>
        </header>
        <form method="post">
            <section class="modal-card-body">
                <?php wp_nonce_field('phoe_order_item_action_cancel', 'phoe_order_item_action_cancel_nonce_field'); ?>
                <input type="hidden" name="order_id" class="order_id" />
                <input type="hidden" name="item_id" class="order_item_id" />
                <input type="hidden" name="request_for" class="request_for" />
                <div class="modal-body">
                    <?php
	      	 	$type = 'exchange' ;
	      	 	$reasons = get_item_reasons($type);
	      	?>
                    <div class="tags">
                        <?php foreach ($reasons as $key =>
                        $value): ?>
                        <tags class="tag is-primary" onclick="show_my_reasons('<?= sanitize_title($value['tag']); ?>' , '<?= $value['tag'] ?>')"><?= $value['tag'] ?></tags>
                        <?php endforeach ?>
                    </div>
                    <hr />
                    <h2 class="selected-reason"></h2>
                    <ul class="list-group">
                        <?php foreach ($reasons as $key =>
                        $value): ?>
                        <?php foreach (explode(',', $value['reasons']) as $reasonVal): ?>
                        <li class="list-group-item reason-items <?= sanitize_title($value['tag']) ?>">
                            <label>
                                <input type="radio" name="reason" value="<?= $value['tag'].' | '.$reasonVal ?>" />
                                <?= $reasonVal ?>
                            </label>
                        </li>
                        <?php endforeach ?>

                        <?php endforeach ?>
                    </ul>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="request_item_action" id="request_item_action" value="cancel" />
                </div>
            </section>
            <footer class="modal-card-foot">
                <button type="submit" class="button is-success" value="cancel_request">Submit</button>
                <button type="button" class="button is-small is-success" onclick="closeReasonPopup()" data-dismiss="modal">Close</button>
            </footer>
        </form>
    </div>
</div>
