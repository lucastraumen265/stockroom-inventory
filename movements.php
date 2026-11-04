<?php
require_once __DIR__ . '/layout.php';
$u = require_admin();
page_start('Stock Movements', [['Stock Movements', null]], 'movements');

$type = in_array($_GET['type'] ?? 'all', ['all', 'restock', 'issue', 'adjust']) ? ($_GET['type'] ?? 'all') : 'all';
$q = trim($_GET['q'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1)); $per = 8;
$cnt = ['all' => 0, 'restock' => 0, 'issue' => 0, 'adjust' => 0];
foreach ($pdo->query('SELECT type, COUNT(*) c FROM movements GROUP BY type') as $r) { $cnt[$r['type']] = (int)$r['c']; $cnt['all'] += (int)$r['c']; }
$w = []; $a = [];
if ($type !== 'all') { $w[] = 'm.type = ?'; $a[] = $type; }
if ($q !== '') { $w[] = '(i.name LIKE ? OR i.item_code LIKE ? OR u.name LIKE ? OR m.reference LIKE ? OR m.recipient LIKE ?)'; array_push($a, "%$q%", "%$q%", "%$q%", "%$q%", "%$q%"); }
$wh = $w ? ' WHERE ' . implode(' AND ', $w) : '';
$from = ' FROM movements m JOIN items i ON i.id=m.item_id JOIN users u ON u.id=m.user_id' . $wh;
$st = $pdo->prepare('SELECT COUNT(*)' . $from); $st->execute($a); $total = (int)$st->fetchColumn();
$page = min($page, max(1, (int)ceil($total / $per)));
$st = $pdo->prepare('SELECT m.*, i.name item_name, i.description, i.item_code, i.unit, u.name user_name' . $from . " ORDER BY m.created_at DESC, m.id DESC LIMIT $per OFFSET " . (($page - 1) * $per)); $st->execute($a);
$rows = $st->fetchAll();
$tab = fn($k, $lbl) => '<a href="?' . e(http_build_query(['type' => $k, 'q' => $q])) . '" class="' . ($type === $k ? 'active' : '') . '">' . $lbl . ' <span class="c">' . $cnt[$k] . '</span></a>';
?>
<div class="page-header"><h1>Stock Movements</h1><p>Monitor and record all inventory stock movements accurately.</p></div>

<div class="card panel">
    <div class="tabs"><?= $tab('all', 'All') . $tab('restock', 'Restocked') . $tab('issue', 'Issued') . $tab('adjust', 'Adjusted') ?></div>
    <div class="panel-actions">
        <a href="adjust.php" class="btn secondary"><?= icon('adjust', 18) ?> Adjust stock</a>
        <a href="issue.php" class="btn secondary"><?= icon('upload', 18) ?> Issue item(s)</a>
    </div>
    <form method="get" class="search"><?= icon('search', 18) ?><input type="hidden" name="type" value="<?= e($type) ?>">
        <input name="q" class="form-control" placeholder="Search movements…" value="<?= e($q) ?>" aria-label="Search movements"></form>
</div>

<div class="item-list">
    <?php if (!$rows): ?><p class="empty">No movements found.</p><?php endif; ?>
    <?php foreach ($rows as $m): $in = $m['quantity_change'] > 0; $mt = mv_meta($m['type']); ?>
    <div class="item-card">
        <div class="mv-head"><span class="mv-ico <?= $m['type'] == 'adjust' ? 'adj' : ($in ? 'in' : 'out') ?>"><?= icon($in ? 'in' : 'out', 20) ?></span>
            <div><div class="name"><?= e($m['item_name']) ?></div><div class="sub"><?= e($m['description']) ?></div></div></div>
        <?= row('Item ID', e($m['item_code'])) ?>
        <?= row('Quantity moved', ($in ? '+' : '−') . n(abs($m['quantity_change'])) . ' ' . e($m['unit']), 'font-weight:600;color:' . ($in ? '#15803d' : '#b91c1c')) ?>
        <?= row('Movement type', '<span class="badge ' . $mt[1] . '">' . $mt[0] . '</span>') ?>
        <?= row('User', e($m['user_name'])) ?>
        <?= row('Date/time', '<span title="' . e(fmt_dt($m['created_at'])) . '">' . time_ago($m['created_at']) . '</span>') ?>
        <?php foreach (['recipient' => 'Recipient', 'purpose' => 'Purpose', 'supplier' => 'Supplier', 'location' => 'Location', 'reason' => 'Reason', 'reference' => 'Reference'] as $k => $l) if (!empty($m[$k])) echo row($l, e($m[$k])); ?>
    </div>
    <?php endforeach; ?>
</div>
<?= pager($total, $page, $per, ['type' => $type, 'q' => $q], 'movements') ?>
<?php page_end(); ?>
