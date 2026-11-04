<?php
require_once __DIR__ . '/layout.php';
$u = page_start('Inventory', [['Inventory', null]], 'dashboard');
$s = stats($pdo);
$f = in_array($_GET['status'] ?? 'all', ['all', 'low', 'out']) ? ($_GET['status'] ?? 'all') : 'all';
$q = trim($_GET['q'] ?? '');
$dir = ($_GET['sort'] ?? 'asc') === 'desc' ? 'desc' : 'asc';
$page = max(1, (int)($_GET['page'] ?? 1)); $per = 8;
$w = []; $a = [];
if ($f === 'low') $w[] = 'quantity > 0 AND quantity <= threshold';
if ($f === 'out') $w[] = 'quantity = 0';
if ($q !== '') { $w[] = '(name LIKE ? OR item_code LIKE ?)'; $a[] = "%$q%"; $a[] = "%$q%"; }
$wh = $w ? ' WHERE ' . implode(' AND ', $w) : '';
$st = $pdo->prepare("SELECT COUNT(*) FROM items$wh"); $st->execute($a); $total = (int)$st->fetchColumn();
$page = min($page, max(1, (int)ceil($total / $per)));
$st = $pdo->prepare("SELECT * FROM items$wh ORDER BY name " . strtoupper($dir) . " LIMIT $per OFFSET " . (($page - 1) * $per)); $st->execute($a);
$items = $st->fetchAll();
$last = $u['role'] === 'admin' ? $pdo->query('SELECT m.*, i.name item_name, i.unit, u.name user_name FROM movements m JOIN items i ON i.id=m.item_id JOIN users u ON u.id=m.user_id ORDER BY m.created_at DESC, m.id DESC LIMIT 1')->fetch() : null;
$qs = ['status' => $f, 'q' => $q, 'sort' => $dir];
$tab = fn($k, $lbl, $c) => '<a href="?' . e(http_build_query(['status' => $k, 'q' => $q])) . '" class="' . ($f === $k ? 'active' : '') . '">' . $lbl . ' <span class="c">' . $c . '</span></a>';
?>
<div class="page-header"><h1>Inventory</h1><p>Track stock levels, manage supplies, and keep the office running smoothly.</p></div>

<?php if ($u['role'] === 'admin'): ?>
<div class="top-actions">
    <a href="restock.php" class="btn secondary"><?= icon('download', 18) ?> Restock item(s)</a>
    <a href="add_item.php" class="btn primary"><?= icon('plus', 18) ?> Add item</a>
</div>
<?php endif; ?>

<?= stat_cards($s) ?>

<?php if ($last): $mt = mv_meta($last['type']); $in = $last['quantity_change'] > 0;
    $verb = ['restock' => 'received', 'issue' => 'issued', 'adjust' => 'adjusted'][$last['type']];
    $meta = implode(' · ', array_filter([$last['user_name'], $last['location'], $last['reference']])); ?>
<div class="card latest">
    <div class="t"><?= icon('history', 20) ?> Latest supply movement</div>
    <div class="m"><span class="mv-ico <?= $last['type'] == 'adjust' ? 'adj' : ($in ? 'in' : 'out') ?>"><?= icon($in ? 'in' : 'out', 20) ?></span>
        <div><b><?= ($in ? '+' : '−') . n(abs($last['quantity_change'])) . ' ' . e($last['unit']) . ' ' . $verb . ' · ' . e($last['item_name']) ?></b><span><?= e($meta) ?></span></div></div>
    <div class="f"><a href="movements.php">View all movements →</a><span><?= time_ago($last['created_at']) ?></span></div>
</div>
<?php endif; ?>

<div class="card panel">
    <div class="tabs"><?= $tab('all', 'All items', $s['total']) . $tab('low', 'Low stock', $s['low']) . $tab('out', 'Out of stock', $s['out']) ?></div>
    <div class="panel-actions">
        <?php if ($u['role'] === 'admin'): ?><a href="adjust.php" class="btn secondary"><?= icon('adjust', 18) ?> Adjust stock</a><?php endif; ?>
        <a href="issue.php" class="btn secondary"><?= icon('upload', 18) ?> Issue item(s)</a>
    </div>
    <form method="get" class="search"><?= icon('search', 18) ?>
        <input type="hidden" name="status" value="<?= e($f) ?>"><input type="hidden" name="sort" value="<?= e($dir) ?>">
        <input name="q" class="form-control" placeholder="Search by item name or ID" value="<?= e($q) ?>" aria-label="Search items"></form>
    <div class="sortbar"><span>Item name</span><a href="?<?= e(http_build_query(['status' => $f, 'q' => $q, 'sort' => $dir === 'asc' ? 'desc' : 'asc'])) ?>">Sort <?= $dir === 'asc' ? 'A–Z ↓' : 'Z–A ↑' ?></a></div>
</div>

<div class="item-list">
    <?php if (!$items): ?><p class="empty">No items found.</p><?php endif; ?>
    <?php foreach ($items as $it) echo item_card($it); ?>
</div>
<?= pager($total, $page, $per, $qs, 'items') ?>
<?php page_end(); ?>
