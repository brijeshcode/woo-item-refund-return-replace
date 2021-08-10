<?php
	if(! defined('ABSPATH')) exit; // Exit if accessed directly
	if (isset($_POST['phoe_save_order_item_settings'])) {
		$this->phoe_save_order_item_action_settings(); } ?>
<?php require_once plugin_dir_path( dirname( __FILE__ ) ) . 'partials/admin-setting-header.php'; ?>

<?php
    $type = 'exchange';
	$pre = 'phoe_wc_item_action';
	$getData = get_option("phoe_order_item_actions");
    $getData = $getData[$type];
    $prefix = $pre .'['.$type.']';
    $woo_statuses = wc_get_order_statuses();

    $checkboxes = [
        [
            'id' => '_enable_item_exchange',
            'name' => $prefix.'[_enable_item_exchange]',
            'label' => 'Enable Exchange on Items'
        ],
        [
            'id' => '_enable_order_exchange',
            'name' => $prefix.'[_enable_order_exchange]',
            'label' => 'Enable Exchange on Orders'
        ],
        [
            'id' => '_add_product_image_on_exchange',
            'name' => $prefix.'[_add_product_image_on_exchange]',
            'label' => 'Add Product image for Exchange verification'
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
                        <h2><a href="<?= add_query_arg(['tab' => 'replace']) ?>">Requests</a></h2>
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
                            <?php
                                $name =  '_exchange_valid_days';
                            ?>
                            <th>
                                <?= _e('Refund valid till days, after order completed.', 'wc-item-actions'); ?>
                            </th>
                            <td>
                                <input type="number" placeholder="10" value="<?= isset($getData[$name]) ? $getData[$name]: ''; ?>" name="<?= $prefix.'['.$name.']'; ?>"> Days
                            </td>
                        </tr>

                        <tr>
                            <th>
                                <?= _e('Exchange on Order Status.', 'wc-item-actions'); ?>
                            </th>
                            <td>
                                <?php
                                    $name =  '_enable_exchange_on_status';
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

                <h2><?= _e('List Reasons for Customers To Cancel.', 'wc-item-actions' ) ?></h2>
                <table class="wp-list-table widefat fixed striped table-view-list posts">
                    <thead>
                        <tr>
                            <th scope="row" style="width: 20%;">Tags</th>
                            <th scope="row" class="reason"><?= _e('Reasons [Add comma seprated multiple reasons.]', 'wc-item-actions') ?></th>
                            <th style="width: 10%;"><span class="my-btn btn-b-green" onclick="addReasonRow('<?= $type ?>', '<?= $pre ?>')"><?= _e('Add', 'wc-item-actions') ?></span></th>
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
