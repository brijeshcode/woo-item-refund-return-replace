<div class="woocommerce-layout__header">
    <h1 class="woocommerce-layout__header-heading css-12vx9xw-Text ">Item Actions</h1>
</div>

<?php
    $tabs = [
        'refund' => 'Refunds',
        'cancel' => 'Cancel',
        'replace' => 'Exchange',
    ];
    $pluginSlug = '?page=phoe-wc-item-action&tab=';
?>


<nav class="nav-tab-wrapper woo-nav-tab-wrapper" style="margin-bottom:25px">
    <?php foreach ($tabs as $para => $tab): ?>
        <?php if (!isset($_GET['tab']) && $para == 'refund'): ?>
            <a href="<?= $pluginSlug.$para; ?>" class="nav-tab nav-tab-active ?> "><?= $tab; ?></a>
        <?php else: ?>
            <a href="<?= $pluginSlug.$para; ?>" class="nav-tab <?=  $para == $_GET['tab'] ||  $para. '-setting' == $_GET['tab'] ?'nav-tab-active': ''  ?> "><?= $tab; ?></a>
        <?php endif ?>
    <?php endforeach ?>
</nav>
