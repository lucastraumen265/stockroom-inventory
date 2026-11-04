<?php
require_once __DIR__ . '/layout.php';
$u = require_admin();
$err = '';
$reasons = ['Physical count discrepancy', 'Damaged items', 'Lost or missing', 'Expired', 'Data entry correction', 'Other'];
$v = ['item_id' => (int)($_GET['id'] ?? 0), 'qty' => '', 'reason' => $reasons[0], 'reference' => '', 'notes' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($v as $k => $_) $v[$k] = trim($_POST[$k] ?? '');
    try {
        if ($v['qty'] === '') throw new Exception('Enter the corrected count.');
        record_movement($pdo, 'adjust', (int)$v['item_id'], $u['id'], (int)$v['qty'], ['reason' => $v['reason'], 'reference' => $v['reference'] ?: null, 'notes' => $v['notes'] ?: null]);
        flash_ok(); redirect('dashboard.php');
    } catch (Exception $ex) { $err = $ex->getMessage(); }
}
$items = $pdo->query('SELECT * FROM items ORDER BY name')->fetchAll();
page_start('Adjust Stock', [['Inventory', 'dashboard.php'], ['Adjust Stock', null]], 'dashboard');
echo form_head('Adjust Stock', 'Reconcile recorded stock with a physical count and document the reason for the difference.', $u);
?>
<form method="post" data-review data-mode="adjust" class="item-list">
    <div class="card"><div class="card-head"><h3>Adjustment details</h3><p>Fields marked * are required.</p></div>
        <h4 class="sec">Item and stock count</h4>
        <?= item_select($items, $v['item_id']) . cur_field('Recorded quantity') ?>
        <?= fld('Corrected count', 'qty', $v['qty'], ['type' => 'number', 'req' => 1, 'unit' => 1, 'qerr' => 1, 'err' => $err, 'help' => 'Enter the actual whole-number count, 0 or more.', 'attr' => 'data-qty min="0" step="1" inputmode="numeric"']) ?>
        <h4 class="sec" style="margin-top:8px">Adjustment record</h4>
        <?= select_fld('Reason', 'reason', array_combine($reasons, $reasons), $v['reason'], 'A reason is required for every stock adjustment.') ?>
        <?= fld('Reference (optional)', 'reference', $v['reference'], ['ph' => 'Count sheet or audit reference']) ?>
        <?= fld('Notes (optional)', 'notes', $v['notes'], ['type' => 'textarea', 'ph' => 'Add details about the difference']) ?>
    </div>
    <?= preview_card('Difference preview') ?>
    <div class="card"><h3 style="font-size:16px">Use adjustments for corrections</h3><p class="muted-p">For incoming supplies, use Restock. For supplies given to a recipient, use Issue Items. Saved corrections appear in Stock movements.</p></div>
    <?= form_actions('Review adjustment') ?>
</form>
<?php page_end(); ?>
