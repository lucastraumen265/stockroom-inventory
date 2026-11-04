<?php
require_once __DIR__ . '/db.php';

function icon($name, $size = 20) {
    $p = [
        'chevron-right' => '<path d="m9 18 6-6-6-6"/>',
        'arrow-left' => '<path d="m12 19-7-7 7-7"/><path d="M19 12H5"/>',
        'box' => '<path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/>',
        'boxx' => '<path d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/><path d="m16 16 5 5"/><path d="m21 16-5 5"/>',
        'swap' => '<path d="M8 3 4 7l4 4"/><path d="M4 7h16"/><path d="m16 21 4-4-4-4"/><path d="M20 17H4"/>',
        'logout' => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/>',
        'eye' => '<path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>',
        'x' => '<path d="M18 6 6 18"/><path d="m6 6 12 12"/>',
        'check' => '<path d="M20 6 9 17l-5-5"/>',
        'alert' => '<path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"/><path d="M12 9v4"/><path d="M12 17h.01"/>',
        'download' => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/>',
        'upload' => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/>',
        'plus' => '<path d="M5 12h14"/><path d="M12 5v14"/>',
        'search' => '<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>',
        'bell' => '<path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>',
        'adjust' => '<line x1="21" x2="14" y1="4" y2="4"/><line x1="10" x2="3" y1="4" y2="4"/><line x1="21" x2="12" y1="12" y2="12"/><line x1="8" x2="3" y1="12" y2="12"/><line x1="21" x2="16" y1="20" y2="20"/><line x1="12" x2="3" y1="20" y2="20"/><line x1="14" x2="14" y1="2" y2="6"/><line x1="8" x2="8" y1="10" y2="14"/><line x1="16" x2="16" y1="18" y2="22"/>',
        'in' => '<path d="M17 7 7 17"/><path d="M17 17H7V7"/>',
        'out' => '<path d="M7 7h10v10"/><path d="M7 17 17 7"/>',
        'history' => '<path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l4 2"/>',
        'building' => '<rect width="16" height="20" x="4" y="2" rx="2"/><path d="M9 22v-4h6v4"/><path d="M8 6h.01M16 6h.01M12 6h.01M12 10h.01M12 14h.01M16 10h.01M16 14h.01M8 10h.01M8 14h.01"/>',
        'layers' => '<path d="m12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83Z"/><path d="m22 17.65-9.17 4.16a2 2 0 0 1-1.66 0L2 17.65"/><path d="m22 12.65-9.17 4.16a2 2 0 0 1-1.66 0L2 12.65"/>',
    ];
    return '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ($p[$name] ?? '') . '</svg>';
}

/* ---------- reusable UI helpers ---------- */
function role_pill($u) { return '<span class="role-pill">' . e(ucfirst($u['role'])) . '</span>'; }

function form_head($title, $desc, $u) {
    return '<a href="dashboard.php" class="back">' . icon('arrow-left', 18) . ' Back to inventory</a>'
        . '<div class="page-header"><div class="ph-top"><h1>' . e($title) . '</h1>' . role_pill($u) . '</div><p>' . e($desc) . '</p></div>';
}

function fld($label, $name, $val = '', $o = []) {
    $t = $o['type'] ?? 'text'; $req = !empty($o['req']); $err = $o['err'] ?? ''; $ph = $o['ph'] ?? ''; $h = $o['help'] ?? ''; $ex = $o['attr'] ?? '';
    $cls = 'form-control' . ($err ? ' error' : '');
    $lab = 'data-label="' . e($label) . '"';
    if ($t === 'textarea') $in = '<textarea name="' . $name . '" class="' . $cls . '" rows="3" placeholder="' . e($ph) . '" ' . $lab . '>' . e($val) . '</textarea>';
    else $in = '<input type="' . $t . '" name="' . $name . '" class="' . $cls . '" value="' . e($val) . '" placeholder="' . e($ph) . '" ' . $lab . ' ' . ($req ? 'required' : '') . ' ' . $ex . '>';
    if (!empty($o['unit'])) $in = '<div class="input-suffix">' . $in . '<span data-unit-lbl></span></div>';
    $qe = !empty($o['qerr']) ? '<div class="field-error" data-qerr>' . e($err) . '</div>' : ($err ? '<div class="field-error">' . e($err) . '</div>' : '');
    return '<div class="form-group"><label>' . e($label) . ($req ? ' *' : '') . '</label>' . $in . $qe . ($h ? '<div class="helper-text">' . e($h) . '</div>' : '') . '</div>';
}

function select_fld($label, $name, $opts, $sel = '', $help = '') {
    $h = '<div class="form-group"><label>' . e($label) . ' *</label><select name="' . $name . '" class="form-control" data-label="' . e($label) . '" required>';
    foreach ($opts as $k => $v) $h .= '<option value="' . e($k) . '"' . ((string)$k === (string)$sel ? ' selected' : '') . '>' . e($v) . '</option>';
    return $h . '</select>' . ($help ? '<div class="helper-text">' . e($help) . '</div>' : '') . '</div>';
}

function item_select($items, $sel = 0, $dis = false) {
    $h = '<div class="form-group"><label>Existing item *</label><select name="item_id" class="form-control" data-item data-label="Item" required>';
    foreach ($items as $i) {
        $off = $dis && $i['quantity'] < 1;
        $h .= '<option value="' . $i['id'] . '" data-qty="' . $i['quantity'] . '" data-unit="' . e($i['unit']) . '" data-th="' . $i['threshold'] . '"' . ($i['id'] == $sel ? ' selected' : '') . ($off ? ' disabled' : '') . '>' . e($i['name']) . ' · ' . e($i['item_code']) . ($off ? ' (out of stock)' : '') . '</option>';
    }
    return $h . '</select><div class="helper-text">Select an existing item by name or ID.</div></div>';
}

function cur_field($label) {
    return '<div class="form-group"><label>' . e($label) . ' · Read-only</label><div class="input-suffix"><input class="form-control ro" readonly tabindex="-1" data-cur><span data-unit-lbl></span></div></div>';
}

function preview_card($title) {
    return '<div class="card"><h3>' . e($title) . '</h3><div class="preview"><div class="calc" data-calc>Enter a quantity to preview</div><div data-after></div><p class="muted-p">Current inventory is not changed until you confirm.</p></div></div>';
}

function form_actions($label) {
    return '<div class="card"><p class="muted-p">Review the details before recording a stock change.</p><div class="actions"><a href="dashboard.php" class="btn secondary">Cancel</a><button type="button" class="btn primary" data-review-btn>' . e($label) . '</button></div></div>';
}

function row($label, $val, $style = '') {
    return '<div class="row"><span class="lbl">' . e($label) . '</span><span class="val" style="' . $style . '">' . $val . '</span></div>';
}

function item_card($it, $extra = '') {
    $st = item_status($it['quantity'], $it['threshold']);
    $c = $st == 'out' ? 'color:#b91c1c;font-weight:600' : ($st == 'low' ? 'color:#a16207;font-weight:600' : 'font-weight:600');
    return '<div class="item-card"><div class="item-head"><div class="item-title"><div class="name">' . e($it['name']) . '</div><div class="sub">' . e($it['description']) . '</div></div></div>'
        . row('Item ID', e($it['item_code'])) . row('Available', n($it['quantity']) . ' ' . e($it['unit']), $c)
        . row('Reorder threshold', n($it['threshold']) . ' ' . e($it['unit'])) . row('Status', status_badge($st)) . $extra . '</div>';
}

function stat_cards($s) {
    return '<div class="stats"><div class="card"><div class="stat-top">Total items ' . icon('box', 20) . '</div><div class="stat-num">' . $s['total'] . ' <small>items</small></div><div class="stat-sub">' . ORG_NAME . ' · ' . $s['cats'] . ' categories</div></div>'
        . '<div class="card"><div class="stat-top">Low stock <span style="color:#a16207">' . icon('alert', 20) . '</span></div><div class="stat-num">' . $s['low'] . ' <small>items</small></div><div class="stat-sub" style="color:#a16207">Above 0 and at or below threshold</div></div>'
        . '<div class="card"><div class="stat-top">Out of stock <span style="color:#b91c1c">' . icon('boxx', 20) . '</span></div><div class="stat-num">' . $s['out'] . ' <small>items</small></div><div class="stat-sub" style="color:#b91c1c">Item(s) with zero available stock</div></div></div>';
}

function pager($total, $page, $per, $qs, $label = 'items') {
    $pages = max(1, (int)ceil($total / $per));
    $from = $total ? ($page - 1) * $per + 1 : 0; $to = min($total, $page * $per);
    $u = fn($p) => e('?' . http_build_query($qs + ['page' => $p]));
    $h = '<div class="card pager"><p class="muted-p">Showing ' . $from . '–' . $to . ' of ' . $total . ' ' . $label . '</p>';
    if ($pages > 1) {
        $nums = array_unique(array_filter([1, $page - 1, $page, $page + 1, $pages], fn($p) => $p >= 1 && $p <= $pages)); sort($nums);
        $h .= '<nav>' . ($page > 1 ? '<a class="btn secondary" href="' . $u($page - 1) . '">Previous</a>' : '<span class="btn secondary off">Previous</span>');
        $prev = 0;
        foreach ($nums as $p) { if ($p - $prev > 1) $h .= '<span class="dots">…</span>'; $h .= '<a class="pg' . ($p == $page ? ' on' : '') . '" href="' . $u($p) . '">' . $p . '</a>'; $prev = $p; }
        $h .= ($page < $pages ? '<a class="btn secondary" href="' . $u($page + 1) . '">Next</a>' : '<span class="btn secondary off">Next</span>') . '</nav>';
    }
    return $h . '</div>';
}

function page_start($title, $crumbs, $active) {
    global $pdo;
    $u = require_login();
    $s = stats($pdo);
    $bell = '<a href="notifications.php" class="icon-btn bell" aria-label="Notifications">' . icon('bell', 22) . ($s['alerts'] ? '<i class="dot"></i>' : '') . '</a>';
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?> · Stockroom</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="phone-wrapper">
    <div class="topbar">
        <button class="icon-btn" data-sidebar-toggle aria-label="Open menu"><?= icon('chevron-right', 20) ?></button>
        <nav class="crumbs">
            <?php foreach ($crumbs as $i => $c): ?>
                <?php if ($i > 0) echo icon('chevron-right', 14); ?>
                <?php if (!empty($c[1])): ?><a href="<?= e($c[1]) ?>"><?= e($c[0]) ?></a><?php else: ?><span><?= e($c[0]) ?></span><?php endif; ?>
            <?php endforeach; ?>
        </nav>
        <?= $bell ?>
    </div>

    <div class="scrim" data-sidebar-toggle></div>
    <aside class="sidebar" id="sidebar">
        <div class="sb-head">
            <div class="logo"><span class="logo-ico"><?= icon('layers', 20) ?></span> stockroom</div>
            <button class="icon-btn" data-sidebar-toggle aria-label="Close menu"><?= icon('x', 22) ?></button>
        </div>
        <div class="ws-card"><span class="ws-ico"><?= icon('building', 22) ?></span><div><b><?= ORG_NAME ?></b><small><?= ORG_SUB ?></small></div></div>
        <form action="dashboard.php" method="get" class="search"><?= icon('search', 18) ?><input name="q" class="form-control" placeholder="Search items" aria-label="Search items"></form>
        <div class="sb-date"><span><?= date('F j, Y') ?></span><?= $bell ?></div>
        <div class="sb-label">WORKSPACE</div>
        <nav class="sb-nav">
            <a href="dashboard.php" class="<?= $active == 'dashboard' ? 'active' : '' ?>"><?= icon('box') ?> Inventory <span class="cnt"><?= $s['total'] ?></span></a>
            <?php if ($u['role'] === 'admin'): ?><a href="movements.php" class="<?= $active == 'movements' ? 'active' : '' ?>"><?= icon('swap') ?> Stock movements</a><?php endif; ?>
            <a href="notifications.php" class="<?= $active == 'notifications' ? 'active' : '' ?>"><?= icon('alert') ?> Notifications <?php if ($s['alerts']): ?><span class="cnt warn"><?= $s['alerts'] ?></span><?php endif; ?></a>
        </nav>
        <div class="sb-foot">
            <div class="user"><span class="avatar"><?= e(initials($u['name'])) ?></span><b><?= e($u['name']) ?> - <?= e(ucfirst($u['role'])) ?></b></div>
            <button class="btn secondary block" data-open="logoutModal"><?= icon('logout', 18) ?> Log out</button>
        </div>
    </aside>

    <main class="main">
<?php return $u; }

function page_end() {
    $f = flash_get();
    ?>
    </main>

    <div class="modal" id="logoutModal"><div class="modal-card"><h2>Log out of Stockroom?</h2>
        <div class="btn-row"><button class="btn secondary" data-close>Cancel</button><a href="logout.php" class="btn primary">Log out</a></div></div></div>

    <div class="modal" id="reviewModal"><div class="modal-card left"><h2>Review details</h2><dl id="reviewBody"></dl>
        <div class="btn-row"><button type="button" class="btn secondary" data-close>Back</button><button type="button" class="btn primary" id="reviewConfirm">Confirm</button></div></div></div>

    <?php if ($f): $ok = $f['type'] == 'success'; ?>
    <div class="modal open" id="flashModal" <?= $ok ? 'data-auto="1"' : '' ?>>
        <div class="modal-card">
            <div class="status-circle <?= $ok ? 'ok' : 'bad' ?>"><?= icon($ok ? 'check' : 'x', 36) ?></div>
            <h2><?= e($f['title']) ?></h2>
            <p><?= e($f['msg']) ?></p>
            <?php if (!$ok): ?><button class="btn primary block" data-close style="margin-top:20px;">Close</button><?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
<script src="app.js"></script>
</body>
</html>
<?php } ?>
