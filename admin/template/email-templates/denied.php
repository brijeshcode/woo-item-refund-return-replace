<?php require_once plugin_dir_path(dirname(__FILE__, 2)) . 'partials/admin-setting-header.php'; ?>

<?php
    if (isset($_POST['phoe_order_item_action_settings_nonce_field']))  $this->phoe_save_email_templates();
    $pre = 'phoe_wc_item_action';
    $type = 'email';
    $prefix = $pre .'['.$type.'][denied_template]' ;

    $templateSetting = phoe_settings($type, 'denied_template');
    // echo "<pre>"; print_r($templateSetting); echo "</pre>"; die();
?>

<h2><a href="admin.php?page=phoe-wc-item-action&tab=email-setting"><?php _e('Back', 'wc-item-actions'); ?></a></h2>

<h2 class=""><?php _e('Denied templates' , 'wc-item-actions'); ?> </h2>
<div class="row">
    <div class="col requested-mail-template">
        <div class="phoe-card w-95 f-left mr-p-5 pl-2" style="display: inline-block;">

        <form method="post" class="mt-2">
            <?php wp_nonce_field('phoe_order_item_action_settings', 'phoe_order_item_action_settings_nonce_field'); ?>
            <div class="row mb-2">
                <div class="col-2"><label><b>Enable</b></label></div>
                <div class="col-4"><input type="checkbox" name="<?= $prefix."[enable]" ?>" value="1" <?= isset($templateSetting['enable']) && $templateSetting['enable'] == 1 ? 'checked' : ''; ?>></div>
            </div>
            <div class="row mb-2">
                <div class="col-2"><label><b>Subject</b></label></div>
                <div class="col-4"><input type="text" name="<?= $prefix."[subject]" ?>" placeholder="Your order item request to {{ sitetitle }} has been raised. " class="w-100" value="<?= isset($templateSetting['subject']) ? $templateSetting['subject'] : ''; ?>"></div>
            </div>
            <div class="eidtor w-95 mb-2">
                <label><b>Body</b></label>
                <?php
                    $defaultCompleted = "Hi {{ customer_name }}, \n
                          This is to inform you that your data is been removed.";
                    $content = isset($templateSetting['body'])? $templateSetting['body'] : $defaultCompleted;
                    $editorHtmlId = 'processing_template_id';
                    $options = [
                        'textarea_name' => $prefix."[body]",
                        'textarea_rows' => 15
                    ];
                    wp_editor($content, $editorHtmlId, $options);
                ?>
            </div>

            <div class="mb-2">
                <button class="my-btn btn-green">Save</button>
                <!-- <button class="my-btn btn-red">Rest to Default</button> -->
            </div>

            </form>
        </div>
    </div>
</div>
<?php require_once plugin_dir_path(dirname(__FILE__, 2)) . 'partials/admin-setting-footer.php';  ?>