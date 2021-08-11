<?php
	if(! defined('ABSPATH')) exit; // Exit if accessed directly
	if (isset($_POST['phoe_save_order_item_settings'])) {
		$this->phoe_save_order_item_action_settings();
	}
?>

<div class="wrap">

<?php require_once plugin_dir_path( dirname( __FILE__ ) ) . 'partials/admin-setting-header.php'; ?>

<?php
	$pre = 'phoe_wc_item_action';
	$getData = get_option("phoe_order_item_actions");
?>
<form method="post">
	<?php wp_nonce_field('phoe_order_item_action_settings', 'phoe_order_item_action_settings_nonce_field'); ?>
	<div class="row">
		<div class="phoe-card w-100 f-left mr-p-10"style="display: inline-block;" >
			<div class="phoe-card-header"><h2><?php _e('Refund Settings', 'wc-item-actions' ); ?> </h2></div>
			<div class="phoe-card-body">
				<table class="form-table">

					<tr>
						<?php
							$type = 'refund';
							$prefix = $pre .'['.$type.']';
							$id  = '_enable_item_refund';
							$name = $prefix.'['.$id .']';
						?>
						<th>
							<label for="<?= $id ?>"><?php _e( 'Enable Refund Items', 'wc-item-actions' ) ?> </label>
						</th>

						<td>
							<input name="<?= $name; ?>" id="<?= $id; ?>" type="checkbox" class="" value="1" <?= isset($getData[$type][$id]) && $getData[$type][$id] == '1' ? 'checked' : '' ?> />
						</td>
					</tr>
				</table>
				<table class="wp-list-table widefat fixed striped table-view-list posts">
					<thead>
					<tr>
						<th scope="row" style="width: 20%" > Tags </th>
						<th scope="row" class="reason"> Reasons [Add comma seprated multiple reasons.]</th>
						<th  style="width: 10%"><span class="my-btn btn-b-green" onclick="addReasonRowOld('<?= $type ?>')" >Add</span></th>
					</tr>
					</thead>
					<tbody class="<?= $type ?>-reasons">
						<?php
						$index = 0;
						if (isset($getData[$type]['reason'])) {
						foreach ($getData[$type]['reason'] as $key => $value) {
							$id  = '_enable_item_refund';
							$name = $prefix.'['.$id .']';
						?>
							<tr class="<?= $type.'-'. $index; ?>">
								<th scope="row" class="titledesc">
									<input type="text" required class="w-100" placeholder="Tag Name" name="<?= $pre; ?>[<?= $type ?>][reason][<?= $index ?>][tag]" name="tags" value="<?= $value['tag'] ?>">
								</th>
								<td class="forminp forminp-text">
									<textarea  class="w-100" required placeholder=" Reason 1, Reason 2, Reason 3, .... " name="<?= $pre; ?>[<?= $type ?>][reason][<?= $index ?>][reasons]"><?= implode(', ', $value['reasons']) ?></textarea>
								</td>
								<th><span class="my-btn btn-b-red" onclick="removeMe('<?= $type; ?>', '<?= $index; ?>')">Delete</span></th>
							</tr>
						<?php
						$index ++;
							}
						}
						?>
					</tbody>
				</table>
				<input type="hidden" class="<?= $type ?>-reason-index" value="<?= $index; ?>">
			</div>
		</div>
	</div>

	<div class="row">
		<div class="phoe-card w-100 f-left mr-p-10"style="display: inline-block;" >
			<div class="phoe-card-header"><h2><?php _e('Cancel Setting', 'wc-item-actions' ); ?> </h2></div>
			<div class="phoe-card-body">
				<?php
					$type = 'cancel';
					$prefix = $pre .'['.$type.']';
				 ?>
				<table class="form-table">
					<tr>
						<?php $id = '_enable_item_cancel'; $name = $prefix.'['.$id .']'; ?>
						<th>
							<label for="<?= $id ?>"><?php _e( 'Enable Cancel Item', 'wc-item-actions' ) ?> </label>
						</th>

						<td>
							<input name="<?= $name; ?>" id="<?= $id; ?>" type="checkbox" class="" value="1" <?= isset($getData[$type][$id]) && $getData[$type][$id] == '1' ? 'checked' : '' ?> />
						</td>
					</tr>
				</table>

				<table class="wp-list-table widefat fixed striped table-view-list posts">
					<thead>
					<tr>
						<th scope="row" style="width: 20%" > Tags </th>
						<th scope="row" class="reason"> Reasons [Add comma seprated multiple reasons.]</th>
						<th  style="width: 10%"><span class="my-btn btn-b-green" onclick="addReasonRowOld('<?= $type ?>')" >Add</span></th>
					</tr>
					</thead>
					<tbody class="<?= $type ?>-reasons">
						<?php
						$index = 0;
						if (isset($getData[$type]['reason'])) {
						foreach ($getData[$type]['reason'] as $key => $value) {
							$id  = '_enable_item_refund';
							$name = $prefix.'['.$id .']';
						?>
							<tr class="<?= $type.'-'. $index; ?>">
								<th scope="row" class="titledesc">
									<input type="text" required class="w-100" placeholder="Tag Name" name="<?= $pre; ?>[<?= $type ?>][reason][<?= $index ?>][tag]" name="tags" value="<?= $value['tag'] ?>">
								</th>
								<td class="forminp forminp-text">
									<textarea  class="w-100" required placeholder=" Reason 1, Reason 2, Reason 3, .... " name="<?= $pre; ?>[<?= $type ?>][reason][<?= $index ?>][reasons]"><?= implode(', ', $value['reasons']) ?></textarea>
								</td>
								<th><span class="my-btn btn-b-red" onclick="removeMe('<?= $type; ?>', '<?= $index; ?>')">Delete</span></th>
							</tr>
						<?php
						$index ++;
							}
						}
						?>
					</tbody>
				</table>
				<input type="hidden" class="<?= $type ?>-reason-index" value="<?= $index; ?>">
			</div>
		</div>
	</div>

	<div class="row">
		<div class="phoe-card w-100 f-left mr-p-10"style="display: inline-block;" >
			<div class="phoe-card-header"><h2><?php _e('Exchange Settings', 'wc-item-actions' ); ?> </h2></div>
			<div class="phoe-card-body">
				<table class="form-table">
					<?php
					$type = 'exchange';
					$prefix = $pre .'['.$type.']';
				 ?>
					<tr>
						<?php $id = '_enable_item_exchange'; $name = $prefix.'['.$id .']'; ?>
						<th>
							<label for="<?= $id ?>"><?php _e( 'Enable Exchange Item', 'wc-item-actions' ) ?> </label>
						</th>

						<td>
							<input name="<?= $name; ?>" id="<?= $id; ?>" type="checkbox" class="" value="1" <?= isset($getData[$type][$id]) && $getData[$type][$id] == '1' ? 'checked' : '' ?> />
						</td>
					</tr>


				</table>

				<table class="wp-list-table widefat fixed striped table-view-list posts">
					<thead>
					<tr>
						<th scope="row" style="width: 20%" > Tags </th>
						<th scope="row" class="reason"> Reasons [Add comma seprated multiple reasons.]</th>
						<th  style="width: 10%"><span class="my-btn btn-b-green" onclick="addReasonRowOld('<?= $type ?>')" >Add</span></th>
					</tr>
					</thead>
					<tbody class="<?= $type ?>-reasons">
						<?php
						$index = 0;
						if (isset($getData[$type]['reason'])) {
						foreach ($getData[$type]['reason'] as $key => $value) {
							$id  = '_enable_item_refund';
							$name = $prefix.'['.$id .']';
						?>
							<tr class="<?= $type.'-'. $index; ?>">
								<th scope="row" class="titledesc">
									<input type="text" required class="w-100" placeholder="Tag Name" name="<?= $pre; ?>[<?= $type ?>][reason][<?= $index ?>][tag]" name="tags" value="<?= $value['tag'] ?>">
								</th>
								<td class="forminp forminp-text">
									<textarea  class="w-100" required placeholder=" Reason 1, Reason 2, Reason 3, .... " name="<?= $pre; ?>[<?= $type ?>][reason][<?= $index ?>][reasons]"><?= implode(', ', $value['reasons']) ?></textarea>
								</td>
								<th><span class="my-btn btn-b-red" onclick="removeMe('<?= $type; ?>', '<?= $index; ?>')">Delete</span></th>
							</tr>
						<?php
						$index ++;
							}
						}
						?>
					</tbody>
				</table>
				<input type="hidden" class="<?= $type ?>-reason-index" value="<?= $index; ?>">
			</div>
		</div>
	</div>

	<!-- <div class="phoe-card">
		<div class="phoe-card-header"></div>
		<div class="phoe-card-body"></div>
		<div class="phoe-card-footer"></div>
	</div> -->

	<input type="submit" name="phoe_save_order_item_settings" value="Save" class="w-10 my-btn">
</form>

</div>



<?php require_once plugin_dir_path( dirname( __FILE__ ) ) . 'partials/admin-setting-footer.php';?>


<script type="text/javascript">
	function addReasonRowOld(type ='refund') {
		index = jQuery('.'+type+ '-reason-index').val();
		add_to  = '.'+type+'-reasons';
		var pre = '<?= $pre; ?>';
		var js_html = '';
		js_html += '<tr class="'+type+'-'+index+'">';
		js_html += '<td>';
		js_html += '<input type="text" required class="w-100" placeholder="Tag Name" name="<?= $pre; ?>['+type+'][reason]['+index+'][tag]">';
		js_html += '</td>';
		js_html += '<td>';
		js_html += '<textarea  class="w-100" required placeholder=" Reason 1, Reason 2, Reason 3, .... " name="<?= $pre; ?>['+type+'][reason]['+index+'][reasons]"></textarea>';
		js_html += '</td>';
		js_html += '<td>';
		js_html += '<span class="my-btn btn-b-red" onclick="removeMe(\''+type+'\', \''+index+'\')">Delete</span>';
		js_html += '</td>';
		js_html += '</tr>';
		js_html += '';

		jQuery(add_to).append(js_html);
		jQuery('.'+type+ '-reason-index').val(++index);
	}

	function removeMe(type, index) {
		if (confirm('Delete confirm')) {
			jQuery('.'+type+'-'+index).remove();
		}
	}
</script>
