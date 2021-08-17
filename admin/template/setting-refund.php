<?php
	if(! defined('ABSPATH')) exit; // Exit if accessed directly

    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'partials/admin-setting-header.php';

	if (isset($_POST['phoe_save_order_item_settings'])) {
		$this->phoe_save_order_item_action_settings();
    }

	$pre = 'phoe_wc_item_action';
    $type = 'refund';

    $prefix = $pre .'['.$type.']';
    $getData = get_option("phoe_order_item_actions");
    $woo_statuses = wc_get_order_statuses();

    $getData = $getData[$type] ;


    $checkboxes = [
        [
            'id' => '_enable_item_refund',
            'name' => $prefix.'[_enable_item_refund]',
            'label' => 'Enable refund on Items'
        ],
        [
            'id' => '_enable_order_refund',
            'name' => $prefix.'[_enable_order_refund]',
            'label' => 'Enable refund on Orders'
        ],
        /*[
            'id' => '_include_shipping_charges_on_refund',
            'name' => $prefix.'[_include_shipping_charges_on_refund]',
            'label' => 'Include Shipping charges in refund amount'
        ],
        [
            'id' => '_include_cod_charges_on_refund',
            'name' => $prefix.'[_include_cod_charges_on_refund]',
            'label' => 'Include COD charges in refund amount'
        ],*/
        /*[
            'id' => '_add_product_image_on_refund',
            'name' => $prefix.'[_add_product_image_on_refund]',
            'label' => 'Add Product image for refund verification'
        ],

        [
            'id' => '_refund_in_wallet',
            'name' => $prefix.'[_refund_in_wallet]',
            'label' => 'Refund in wallet'
        ],
        [
            'id' => '_refund_in_account',
            'name' => $prefix.'[_refund_in_account]',
            'label' => 'Refund in account'
        ],*/
        /*[
            'id' => '_refund_in_same_source',
            'name' => $prefix.'[_refund_in_same_source]',
            'label' => 'Refund to same source'
        ]*/
    ]

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
                        <h2><a href="<?= add_query_arg(['tab' => 'refund']) ?>"><?php _e('Requests', 'wc-item-actions' ); ?></a></h2>
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
                                $name =  '_refund_valid_days';
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
                                <?= _e('Refund on Order Status.', 'wc-item-actions'); ?>
                            </th>
                            <td>

                                <?php
                                    $name =  '_enable_refund_on_status';
                                ?>
                                <select name="<?= $prefix.'['.$name.']'; ?>">
                                    <?php foreach ($woo_statuses as $key => $status): ?>
                                        <option  <?= isset($getData[$name]) && $getData[$name] == $key ? 'selected' : '' ; ?> value="<?= $key ?>"><?= $status ?></option>
                                    <?php endforeach ?>
                                </select>
                            </td>
                        </tr>



                    </tbody>
                </table>
                <hr />

                <h2><?= _e('List Reasons for Customers.', 'wc-item-actions'); ?></h2>
                <table class="wp-list-table widefat fixed striped table-view-list posts">
                    <thead>
                        <tr>
                            <th scope="row" style="width: 20%;"><?= _e('Tags' , 'wc-item-actions') ?></th>
                            <th scope="row" class="reason"><?= _e('Reasons [Add comma seprated multiple reasons.]', 'wc-item-actions'); ?></th>
                            <th style="width: 10%;"><span class="my-btn btn-b-green" onclick="addReasonRow('<?= $type ?>', '<?= $pre ?>')"><?= _e('Add', 'wc-item-actions'); ?></span></th>
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
