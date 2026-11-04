<?php
require_once __DIR__ . '/layout.php';
$u = page_start('Notifications', [['Inventory', 'dashboard.php'], ['Notifications', null]], 'notifications');
$s = stats($pdo);
$f = in_array($_GET['status'] ?? 'all', ['all', 'low', 'out']) ? ($_GET['status'] ?? 'all') : 'all';
$page = max(1, (int)($_GET['page'] ?? 1)); $per = 8;
$wh = $f === 'low' ? 'quantity > 0 AND quantity <= threshold' : ($f === 'out' ? 'quantity = 0' : 'quantity <= threshold');
$total = (int)$pdo->query("SELECT COUNT(*) FROM items WHERE $wh")->fetchColumn();
$page = min($page, max(1, (int)ceil($total / $per)));
$items = $pdo->query("SELECT * FROM items WHERE $wh ORDER BY quantity ASC, name ASC LIMIT $per OFFSET " . (($page - 1) * $per))->fetchAll();
$tab = fn($k, $lbl, $c) => '<a href="?status=' . $k . '" class="' . ($f === $k ? 'active' : '') . '">' . $lbl . ' <span class="c">' . $c . '</span></a>';
?>
<a href="dashboard.php" class="back"><?= icon('arrow-left', 18) ?> Back to inventory</a>
<div class="page-header"><div class="ph-top"><h1>Notifications</h1><?= role_pill($u) ?></div><p>Review low-stock and out-of-stock office supplies that need attention.</p></div>
<?= stat_cards($s) ?>
<div class="card panel"><div class="tabs"><?= $tab('all', 'All stock alerts', $s['alerts']) . $tab('low', 'Low stock', $s['low']) . $tab('out', 'Out of stock', $s['out']) ?></div></div>
<div class="item-list">
    <?php if (!$items): ?><p class="empty">All items are sufficiently stocked.</p><?php endif; ?>
    <?php foreach ($items as $it):
        $btn = $u['role'] === 'admin' ? '<a href="restock.php?id=' . $it['id'] . '" class="btn secondary block">Restock now</a>' : '';
        echo str_replace('class="item-card"', 'class="item-card alert-card ' . ($it['quantity'] == 0 ? 'out' : '') . '"', item_card($it, $btn));
    endforeach; ?>
</div>
<?= pager($total, $page, $per, ['status' => $f], 'alerts') ?>
<?php page_end(); ?>
