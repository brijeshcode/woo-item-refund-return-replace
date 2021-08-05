<?php
    $tabs = [
        'refund' => 'Refunds',
        'cancel' => 'Cancel',
        'replace' => 'Exchange',
    ];
    $pluginSlug = '?page=phoe-wc-item-action&tab=';
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

<nav class="nav-tab-wrapper woo-nav-tab-wrapper" style="margin-bottom:25px">
    <?php foreach ($tabs as $para => $tab): ?>
        <?php if (!isset($_GET['tab']) && $para == 'refund'): ?>
            <a href="<?= $pluginSlug.$para; ?>" class="nav-tab nav-tab-active ?> "><?= $tab; ?></a>
        <?php else: ?>
            <a href="<?= $pluginSlug.$para; ?>" class="nav-tab <?=  $para == $_GET['tab'] ||  $para. '-setting' == $_GET['tab'] ?'nav-tab-active': ''  ?> "><?= $tab; ?></a>
        <?php endif ?>
    <?php endforeach ?>
</nav>
