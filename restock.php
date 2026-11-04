<?php
require_once __DIR__ . '/layout.php';
$u = require_admin();
$err = '';
$v = ['item_id' => (int)($_GET['id'] ?? 0), 'qty' => '', 'supplier' => '', 'location' => 'Supply closet', 'reference' => '', 'received_date' => date('Y-m-d'), 'notes' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($v as $k => $_) $v[$k] = trim($_POST[$k === 'qty' ? 'qty' : $k] ?? '');
    try {
        if ($v['location'] === '') throw new Exception('Location is required.');
        if (!strtotime($v['received_date'])) throw new Exception('Enter a valid received date.');
        record_movement($pdo, 'restock', (int)$v['item_id'], $u['id'], (int)$v['qty'], ['supplier' => $v['supplier'] ?: null, 'location' => $v['location'], 'reference' => $v['reference'] ?: null, 'received_date' => $v['received_date'], 'notes' => $v['notes'] ?: null]);
        flash_ok(); redirect('dashboard.php');
    } catch (Exception $ex) { $err = $ex->getMessage(); }
}
$items = $pdo->query('SELECT * FROM items ORDER BY name')->fetchAll();
page_start('Restock', [['Inventory', 'dashboard.php'], ['Restock', null]], 'dashboard');
echo form_head('Restock', 'Record incoming office supplies for an existing item. Review quantities before saving.', $u);
?>
<form method="post" data-review data-mode="restock" class="item-list">
    <div class="card"><div class="card-head"><h3>Restock details</h3><p>Fields marked * are required.</p></div>
        <h4 class="sec">Item and quantity</h4>
        <?= item_select($items, $v['item_id']) . cur_field('Current quantity') ?>
        <?= fld('Quantity added', 'qty', $v['qty'], ['type' => 'number', 'req' => 1, 'unit' => 1, 'qerr' => 1, 'err' => $err, 'help' => 'Enter a positive whole number (at least 1).', 'attr' => 'data-qty min="1" step="1" inputmode="numeric"']) ?>
        <h4 class="sec" style="margin-top:8px">Receiving information</h4>
        <?= fld('Supplier (optional)', 'supplier', $v['supplier'], ['ph' => 'Enter supplier name']) ?>
        <?= fld('Location', 'location', $v['location'], ['req' => 1]) ?>
        <?= fld('Reference (optional)', 'reference', $v['reference'], ['ph' => 'Purchase order or delivery receipt']) ?>
        <?= fld('Received date', 'received_date', $v['received_date'], ['type' => 'date', 'req' => 1]) ?>
        <?= fld('Notes (optional)', 'notes', $v['notes'], ['type' => 'textarea', 'ph' => 'Add receiving details or delivery notes']) ?>
    </div>
    <?= preview_card('Restock preview') . form_actions('Review restock') ?>
</form>
<?php page_end(); ?>
