<?php
require_once __DIR__ . '/layout.php';
$u = require_login();
$err = '';
$v = ['item_id' => 0, 'qty' => '', 'recipient' => '', 'reference' => '', 'purpose' => '', 'notes' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($v as $k => $_) $v[$k] = trim($_POST[$k] ?? '');
    try {
        if ($v['recipient'] === '' || $v['purpose'] === '') throw new Exception('Recipient and purpose are required.');
        record_movement($pdo, 'issue', (int)$v['item_id'], $u['id'], (int)$v['qty'], ['recipient' => $v['recipient'], 'purpose' => $v['purpose'], 'reference' => $v['reference'] ?: null, 'notes' => $v['notes'] ?: null]);
        flash_ok(); redirect('dashboard.php');
    } catch (Exception $ex) { $err = $ex->getMessage(); }
}
$items = $pdo->query('SELECT * FROM items ORDER BY name')->fetchAll();
page_start('Issue Items', [['Inventory', 'dashboard.php'], ['Issue Items', null]], 'dashboard');
echo form_head('Issue Items', 'Issue available office supplies to a recipient and record their purpose.', $u);
?>
<form method="post" data-review data-mode="issue" class="item-list">
    <div class="card"><div class="card-head"><h3>Issue details</h3><p>Fields marked * are required.</p></div>
        <h4 class="sec">Item and quantity</h4>
        <?= str_replace('Existing item', 'Item', item_select($items, (int)$v['item_id'], true)) . cur_field('Current stock') ?>
        <?= fld('Quantity to issue', 'qty', $v['qty'], ['type' => 'number', 'req' => 1, 'unit' => 1, 'qerr' => 1, 'err' => $err, 'help' => 'Whole number, from 1 up to the current stock.', 'attr' => 'data-qty min="1" step="1" inputmode="numeric"']) ?>
        <h4 class="sec" style="margin-top:8px">Recipient and purpose</h4>
        <?= fld('Recipient / department', 'recipient', $v['recipient'], ['req' => 1, 'ph' => 'e.g. COE 2 Office']) ?>
        <?= fld('Reference (optional)', 'reference', $v['reference'], ['ph' => 'Request slip or reference number']) ?>
        <?= fld('Purpose', 'purpose', $v['purpose'], ['req' => 1, 'ph' => 'e.g. Office document review', 'help' => 'Describe how the supplies will be used.']) ?>
        <?= fld('Notes (optional)', 'notes', $v['notes'], ['type' => 'textarea', 'ph' => 'Add handover details or additional notes']) ?>
    </div>
    <?= preview_card('Issue preview') . form_actions('Review issue') ?>
</form>
<?php page_end(); ?>
