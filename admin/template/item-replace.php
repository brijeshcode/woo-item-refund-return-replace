<?php
	require_once plugin_dir_path(dirname(__FILE__)) . 'partials/admin-setting-header.php';
	$type = 'Exchange';

if (isset($_GET['action']) &&  isset($_GET['item_id'])) {
    phoe_admin_item_request_change_status($_GET['item_id'], $_GET['action'], $type);
}

	$cancelRequests = get_customer_order_item_requests($type);
?>
<div class="phoe-card w-95">
<div class="phoe-card-header">
	<div class="row">
		<div class="col-6">
			<h2>
				<?php _e('Requests', 'wc-item-actions'); ?>

				<?php
                        _e('Items', 'wc-item-actions' );
                        $itemCount = phoe_count_incomplete_requests($type, 'Item');
                    ?>
                    <?php if ($itemCount > 0): ?>
                         <span class="requests-count"><?= $itemCount ?></span>
                    <?php endif ?>
                        |
                    <a href="<?= add_query_arg(['tab' => 'replace-order']) ?>"><?php _e('Orders', 'wc-item-actions' ); ?></a>

                    <?php
                        $orderCount = phoe_count_incomplete_requests($type, 'Order');
                    ?>

                    <?php if ($orderCount > 0): ?>
                         <span class="requests-count"><?= $orderCount ?></span>
                    <?php endif ?>
			</h2>
		</div>
		<div class="col-6 text-right"><h2><a href="<?= add_query_arg(['tab' => 'replace-setting']) ?>">Settings</a></h2></div>
	</div>

</div>
<div class="phoe-card-body">
<table class="wp-list-table widefat fixed striped table-view-list pages data-table-init">
	<thead>
		<tr>
			<th>Date</th>
			<th>order#</th>
			<th>Item</th>
			<th>Status</th>
			<th>Reason</th>
			<!-- <th>Admin Reason</th> -->
			<th>Action</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th>Date</th>
			<th>order#</th>
			<th>Item</th>
			<th>Status</th>
			<th>Reason</th>
			<!-- <th>Admin Reason</th> -->
			<th>Action</th>
		</tr>
	</tfoot>

	<tbody>
		<?php if (!empty($cancelRequests)): ?>
			<?php foreach ($cancelRequests as $key => $request): ?>
				<tr>
					<td><?= $request->created_at ?></td>
					<td><a href="<?= admin_url() ?>post.php?post=<?= $request->order_id ?>&action=edit" target="_blank"><?= $request->order_id ?></a></td>
					<td><?= $request->order_item_name ?></td>
					<td><?= $request->request_status ?></td>
					<td><?= $request->request_reason ?></td>
					<!-- <td> <?= $request->request_admin_reason ?></td> -->
					<td>
						<?php if (!in_array($request->request_status, ['Completed', 'Denied'])): ?>
							<a href="<?= add_query_arg(['item_id' => $request->id, 'action' => 'approve']) ?>">Approve</a> |
							<a href="<?= add_query_arg(['item_id' => $request->id, 'action' => 'denied']) ?>">Denied</a>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
	</tbody>
</table>
</div>
<?php require_once plugin_dir_path(dirname(__FILE__)) . 'partials/admin-setting-footer.php';

?>
