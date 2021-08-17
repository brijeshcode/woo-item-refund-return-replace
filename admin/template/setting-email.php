<?php
    if(! defined('ABSPATH')) exit; // Exit if accessed directly
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'partials/admin-setting-header.php';
    $pre = 'phoe_wc_item_action';
    $type = 'email';
    $prefix = $pre .'['.$type.']';
    if (isset($_POST['phoe_order_item_action_settings_nonce_field'])) {
        $this->phoe_save_email_templates();
    }
    $templateSetting = phoe_settings($type);

    $tmeplates = [
        ['title' => 'Request Template',  'file' =>'request'],
        ['title' => 'Processing Template',  'file' =>'processing'],
        ['title' => 'Completed Template',  'file' =>'completed'],
        ['title' => 'Denied Template' , 'file' =>'denied'],
    ];

?>
<h2 class=""><?php _e('Email templates' , 'wc-item-actions'); ?> </h2>
<hr>
    Tags :
    <ul>
        <li> <code>{{ site_title }}</code>: Site title</li>
        <li> <code>{{ customer_full_name }}</code>: Customer Name </li>
        <li> <code>{{ customer_first_name }}</code>: Customer Name </li>
        <li> <code>{{ customer_email }}</code>: Customer Email </li>
        <li> <code>{{ order_no }}</code>: Order Number </li>
        <li> <code>{{ product_name }}</code>: Product name </li>
    </ul>

<h3><?php _e('Requested Mail Template', 'wc-item-actions'); ?></h3>
<table class="form-table">
    <tbody>
        <tr valign="top">
            <td class="wc_emails_wrapper" colspan="2">
                <table class="wc_emails widefat" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="pl-2">Template</th>
                            <th class="pl-2">Enable</th>
                            <th style="width: 20%;">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tmeplates as $key => $template): ?>
                            <tr>
                                <td class="wc-email-settings-table-name">
                                    <a href="<?= add_query_arg([
                                    'tab' => 'email-setting',
                                    'template' => $template['file']
                                    ]); ?>" target="_blank"><?= $template['title'] ?></a>
                                </td>
                                <td>
                                    <?php if (isset($templateSetting[$template['file']. '_template']['enable'])): ?>
                                        Yes
                                    <?php else: ?>
                                        No
                                    <?php endif ?>
                                </td>
                                <td class="wc-email-settings-table-actions">
                                    <a class="button alignright" href="<?= add_query_arg([
                                    'tab' => 'email-setting',
                                    'template' => $template['file']
                                    ]); ?>">Manage</a>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>



<?php require_once plugin_dir_path( dirname( __FILE__ ) ) . 'partials/admin-setting-footer.php';?>