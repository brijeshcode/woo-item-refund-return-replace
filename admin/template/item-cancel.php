<?php require_once plugin_dir_path(dirname(__FILE__)) . 'partials/admin-setting-header.php'; ?>

<?php
// echo "<pre>"; print_r(get_cancel_requests()); echo "</pre>"; die();
if (isset($_GET['action']) && $_GET['action'] == 'approve' && isset($_GET['item_id'])) {
    phoe_admin_approve_order_item_request($_GET['item_id'], 'Cancel');
}

if (isset($_GET['action']) && $_GET['action'] == 'denied' && isset($_GET['item_id'])) {
    phoe_admin_denied_order_item_request($_GET['item_id'], 'Cancel');
}
$cancelRequests = get_customer_order_item_requests('Cancel');
?>
<link rel="stylesheet" type="text/css" href="http://cdn.datatables.net/1.10.23/css/jquery.dataTables.min.css">
<script type="text/javascript" src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
	jQuery(document).ready( function () {
    jQuery('.data-table-init').DataTable({
        "order": [[ 0, "desc" ]]
    } );
} );
</script>
<div class="phoe-card w-95">
	<div class="phoe-card-header">
		<div class="row">
			<div class="col-6">
				<h2><?php _e('Requests', 'wc-item-actions'); ?> </h2>
			</div>
			<div class="col-6 text-right"><h2><a href="<?= add_query_arg(['tab' => 'cancel-setting']) ?>">Settings</a></h2></div>
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
</div>

<?php require_once plugin_dir_path(dirname(__FILE__)) . 'partials/admin-setting-footer.php';

?>
