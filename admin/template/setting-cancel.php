<?php
	if(! defined('ABSPATH')) exit; // Exit if accessed directly
	if (isset($_POST['phoe_save_order_item_settings'])) {
		$this->phoe_save_order_item_action_settings(); } ?>
<?php require_once plugin_dir_path( dirname( __FILE__ ) ) . 'partials/admin-setting-header.php'; ?>

<?php

	$pre = 'phoe_wc_item_action';
    $type = 'cancel';

	$getData = get_option("phoe_order_item_actions");

    $getData = $getData[$type] ;
    $woo_statuses = wc_get_order_statuses();

    $prefix = $pre .'['.$type.']';

    $checkboxes = [
        [
            'id' => '_enable_item_cancel',
            'name' => $prefix.'[_enable_item_cancel]',
            'label' => 'Enable cancel on Items'
        ],
        [
            'id' => '_enable_order_cancel',
            'name' => $prefix.'[_enable_order_cancel]',
            'label' => 'Enable cancel on Orders'
        ],
        /*[
            'id' => '_include_shipping_charges_on_cancel',
            'name' => $prefix.'[_include_shipping_charges_on_cancel]',
            'label' => 'Include Shipping charges in cancel amount'
        ],
        [
            'id' => '_include_cod_charges_on_cancel',
            'name' => $prefix.'[_include_cod_charges_on_cancel]',
            'label' => 'Include COD charges in cancel amount'
        ],*/
        [
            'id' => '_cancel_in_same_source',
            'name' => $prefix.'[_cancel_in_same_source]',
            'label' => 'Cancel to same source'
        ]
    ];
?>

<form method="post">
    <?php wp_nonce_field('phoe_order_item_action_settings', 'phoe_order_item_action_settings_nonce_field'); ?>
    <div class="row">
        <div class="phoe-card w-95 f-left mr-p-10" style="display: inline-block;">
            <div class="phoe-card-header">
                <div class="row">
                    <div class="col-6">
                        <h2><?php _e('Settings', 'wc-item-actions' ); ?></h2>
                    </div>
                    <div class="col-6 text-right">
                        <h2><a href="<?= add_query_arg(['tab' => 'cancel']) ?>">Requests</a></h2>
                    </div>
                </div>
            </div>
            <div class="phoe-card-body">
                <table class="wp-list-table widefat fixed striped table-view-list posts">
                    <thead>
                        <tr>
                            <th><?= _e('Options', 'wc-item-actions'); ?></th>
                            <th><?= _e('Enable', 'wc-item-actions'); ?></th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($checkboxes as $key => $check): ?>
                            <tr>
                                <th>
                                    <label for="<?= $check['id']; ?>"><?php _e($check['label'], 'wc-item-actions');?></label>
                                </th>
                                <td>
                                    <input name="<?= $check['name']; ?>" id="<?= $check['id']; ?>" type="checkbox" class="" value="1"
                                    <?= isset($getData[$check['id']]) && $getData[$check['id']] == '1' ? 'checked' : '' ; ?>
                                    />
                                </td>
                            </tr>
                        <?php endforeach ?>

                        <tr>
                            <th>
                                <?= _e('Cancel on Order Status.', 'wc-item-actions'); ?>
                            </th>
                            <td>
                                <?php
                                    $name =  '_enable_cancel_on_status';

                                ?>
                                <select name="<?= $prefix.  '['.$name.']'; ?>">
                                    <?php foreach ($woo_statuses as $key => $status): ?>
                                        <option  <?= isset($getData[$name]) && $getData[$name] == $key ? 'selected' : '' ; ?> value="<?= $key ?>"><?= $status ?></option>
                                    <?php endforeach ?>
                                </select>

                            </td>
                        </tr>


                    </tbody>
                </table>
                <hr />
                <h2>List Reasons for Customers To Cancel.</h2>
                <table class="wp-list-table widefat fixed striped table-view-list posts">
                    <thead>
                        <tr>
                            <th scope="row" style="width: 20%;">Tags</th>
                            <th scope="row" class="reason">Reasons [Add comma seprated multiple reasons.]</th>
                            <th style="width: 10%;"><span class="my-btn btn-b-green" onclick="addReasonRow('<?= $type ?>', '<?= $pre ?>')">Add</span></th>
                        </tr>
                    </thead>
                    <tbody class="<?= $type ?>-reasons">
                        <?php
						$index = 0;
						if (isset($getData['reason'])) {
						foreach ($getData['reason'] as $key =>
                        $value) { $id = '_enable_item_refund'; $name = $prefix.'['.$id .']'; ?>
                        <tr class="<?= $type.'-'. $index; ?>">
                            <th scope="row" class="titledesc">
                                <input type="text" required class="w-100" placeholder="Tag Name" name="<?= $pre; ?>[<?= $type ?>][reason][<?= $index ?>][tag]" name="tags" value="<?= $value['tag'] ?>" />
                            </th>
                            <td class="forminp forminp-text">
                                <textarea class="w-100" required placeholder=" Reason 1, Reason 2, Reason 3, .... " name="<?= $pre; ?>[<?= $type ?>][reason][<?= $index ?>][reasons]"><?= $value['reasons']; ?></textarea>
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
                <input type="hidden" class="<?= $type ?>-reason-index" value="<?= $index; ?>" />
            </div>
        </div>
    </div>
    <input type="submit" name="phoe_save_order_item_settings" value="Save" class="w-10 my-btn btn-green" />
</form>

<?php require_once plugin_dir_path( dirname( __FILE__ ) ) . 'partials/admin-setting-footer.php';?>
