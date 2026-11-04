<?php
require_once __DIR__ . '/layout.php';
$u = require_admin();
$errs = [];
$units = ['packs', 'pens', 'sets', 'reams', 'bottles', 'boxes', 'pieces', 'dispensers'];
$v = ['name' => '', 'item_code' => '', 'category_id' => '', 'unit' => 'packs', 'quantity' => '0', 'threshold' => '10', 'description' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($v as $k => $_) $v[$k] = trim($_POST[$k] ?? '');
    $v['item_code'] = strtoupper($v['item_code']);
    if ($v['name'] === '') $errs['name'] = 'Item name is required.';
    if (!preg_match('/^[A-Z0-9-]{3,50}$/', $v['item_code'])) $errs['item_code'] = 'Use letters, numbers and dashes only, e.g. PEN-1001.';
    else {
        $st = $pdo->prepare('SELECT name FROM items WHERE item_code = ?'); $st->execute([$v['item_code']]);
        if ($row = $st->fetch()) $errs['item_code'] = $v['item_code'] . ' already belongs to ' . $row['name'] . '. Use a unique ID.';
    }
    if (!ctype_digit($v['quantity'])) $errs['quantity'] = 'Enter a whole number of 0 or more.';
    if (!ctype_digit($v['threshold'])) $errs['threshold'] = 'Enter a whole number of 0 or more.';
    if ($v['unit'] === '') $errs['unit'] = 'Unit is required.';
    if (!$errs) {
        $pdo->prepare('INSERT INTO items (item_code, name, description, category_id, quantity, threshold, unit) VALUES (?,?,?,?,?,?,?)')
            ->execute([$v['item_code'], $v['name'], $v['description'], (int)$v['category_id'] ?: null, (int)$v['quantity'], (int)$v['threshold'], $v['unit']]);
        flash_ok(); redirect('dashboard.php');
    }
}
$cats = $pdo->query('SELECT id, name FROM categories ORDER BY name')->fetchAll(PDO::FETCH_KEY_PAIR);
$total = (int)$pdo->query('SELECT COUNT(*) FROM items')->fetchColumn();
page_start('Add Item', [['Inventory', 'dashboard.php'], ['Add Item', null]], 'dashboard');
echo form_head('Add Item', 'Create an office supply item and define its opening stock and reorder threshold.', $u);
?>
<form method="post" data-review class="item-list">
    <div class="card"><div class="card-head"><h3>Item details</h3><p>Fields marked * are required.</p></div>
        <h4 class="sec">Item identity</h4>
        <?= fld('Item name', 'name', $v['name'], ['req' => 1, 'err' => $errs['name'] ?? '', 'help' => 'Use a clear, descriptive supply name.']) ?>
        <?= fld('Unique item ID', 'item_code', $v['item_code'], ['req' => 1, 'ph' => 'e.g. PEN-1001', 'err' => $errs['item_code'] ?? '', 'help' => 'Choose an unused ID.']) ?>
        <?= select_fld('Category', 'category_id', $cats, $v['category_id']) ?>
        <?= select_fld('Unit', 'unit', array_combine($units, $units), $v['unit'], 'Quantities will be counted in this unit.') ?>
        <h4 class="sec" style="margin-top:8px">Stock settings</h4>
        <?= fld('Initial quantity', 'quantity', $v['quantity'], ['type' => 'number', 'req' => 1, 'err' => $errs['quantity'] ?? '', 'help' => 'Enter a whole number of 0 or more.', 'attr' => 'min="0" step="1" inputmode="numeric"']) ?>
        <?= fld('Reorder threshold', 'threshold', $v['threshold'], ['type' => 'number', 'req' => 1, 'err' => $errs['threshold'] ?? '', 'help' => 'Enter a whole number of 0 or more.', 'attr' => 'min="0" step="1" inputmode="numeric"']) ?>
        <?= fld('Description (optional)', 'description', $v['description'], ['ph' => 'e.g. Black ink · Box of 24', 'attr' => 'maxlength="255"']) ?>
    </div>
    <div class="card rules"><h3>Stock status rules</h3>
        <?= row('Out of stock', 'Quantity = 0') . row('Low stock', '0 &lt; qty ≤ threshold') . row('In stock', 'Qty &gt; threshold') ?>
        <p class="muted-p" style="margin-top:8px">A zero opening quantity is allowed. Existing inventory: <?= $total ?> items. Use Restock for supplies already in inventory.</p></div>
    <?= form_actions('Review item') ?>
</form>
<?php page_end(); ?>
