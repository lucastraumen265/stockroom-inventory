<?php
if (session_status() === PHP_SESSION_NONE) session_start();
date_default_timezone_set('Asia/Manila');

const DB_HOST = 'localhost';
const DB_NAME = 'stockroom_db';
const DB_USER = 'root';
const DB_PASS = '';
const ORG_NAME = 'BULSU COE 2';
const ORG_SUB  = 'Office supplies workspace';

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
} catch (PDOException $ex) {
    die('Database connection failed: ' . $ex->getMessage());
}

function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function redirect($to) { header('Location: ' . $to); exit; }
function n($v) { return number_format((int)$v); }

function current_user() {
    global $pdo;
    static $cache = false;
    if ($cache !== false) return $cache;
    if (empty($_SESSION['uid'])) return $cache = null;
    $st = $pdo->prepare('SELECT id, name, email, role FROM users WHERE id = ?');
    $st->execute([$_SESSION['uid']]);
    return $cache = ($st->fetch() ?: null);
}
function require_login() { $u = current_user(); if (!$u) redirect('index.php'); return $u; }
function require_admin() {
    $u = require_login();
    if ($u['role'] !== 'admin') { flash_set('error', 'Access denied', 'Only admins can perform this action.'); redirect('dashboard.php'); }
    return $u;
}

function flash_set($type, $title, $msg) { $_SESSION['flash'] = compact('type', 'title', 'msg'); }
function flash_ok() { flash_set('success', 'Operation completed', 'Your inventory update was processed successfully.'); }
function flash_get() { $f = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $f; }

function item_status($qty, $th) { if ($qty <= 0) return 'out'; return $qty <= $th ? 'low' : 'in'; }
function status_badge($s) {
    $m = ['in' => ['In stock', 'ok'], 'low' => ['Low stock', 'warn'], 'out' => ['Out of stock', 'bad']];
    return '<span class="badge ' . $m[$s][1] . '">' . $m[$s][0] . '</span>';
}
function mv_meta($t) {
    return ['restock' => ['Restocked', 'ok'], 'issue' => ['Issued', 'bad'], 'adjust' => ['Adjusted', 'info']][$t];
}
function initials($name) {
    $p = explode(' ', trim($name));
    return strtoupper(substr($p[0], 0, 1) . (isset($p[1]) ? substr($p[1], 0, 1) : ''));
}
function time_ago($ts) {
    $d = time() - strtotime($ts);
    if ($d < 60) return 'just now';
    if ($d < 3600) return floor($d / 60) . ' min ago';
    if ($d < 86400) return floor($d / 3600) . ' hr ago';
    return date('M j, Y', strtotime($ts));
}
function fmt_dt($ts) { return date('M j, Y · g:i A', strtotime($ts)); }

function stats($pdo) {
    $r = $pdo->query('SELECT COUNT(*) AS total, COALESCE(SUM(quantity = 0),0) AS outc, COALESCE(SUM(quantity > 0 AND quantity <= threshold),0) AS low FROM items')->fetch();
    $cats = (int)$pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();
    return ['total' => (int)$r['total'], 'low' => (int)$r['low'], 'out' => (int)$r['outc'], 'alerts' => (int)$r['low'] + (int)$r['outc'], 'cats' => $cats];
}

function record_movement($pdo, $type, $itemId, $userId, $value, $x = []) {
    if (in_array($type, ['restock', 'issue']) && $value < 1) throw new Exception('Enter a positive whole number (at least 1).');
    if ($type === 'adjust' && $value < 0) throw new Exception('Enter a whole number of 0 or more.');
    $pdo->beginTransaction();
    try {
        $st = $pdo->prepare('SELECT * FROM items WHERE id = ? FOR UPDATE');
        $st->execute([$itemId]);
        $it = $st->fetch();
        if (!$it) throw new Exception('Item not found.');
        $before = (int)$it['quantity'];
        if ($type === 'restock') $after = $before + $value;
        elseif ($type === 'issue') {
            if ($before < 1) throw new Exception('This item is out of stock and cannot be issued.');
            if ($value > $before) throw new Exception("Only $before {$it['unit']} are available. Enter a whole number from 1 to $before.");
            $after = $before - $value;
        } else $after = $value;
        $delta = $after - $before;
        if ($delta === 0) throw new Exception('No change in quantity.');
        $pdo->prepare('UPDATE items SET quantity = ? WHERE id = ?')->execute([$after, $itemId]);
        $pdo->prepare('INSERT INTO movements (item_id, user_id, type, quantity_change, qty_before, qty_after, recipient, purpose, supplier, location, reference, reason, notes, received_date) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)')
            ->execute([$itemId, $userId, $type, $delta, $before, $after, $x['recipient'] ?? null, $x['purpose'] ?? null, $x['supplier'] ?? null, $x['location'] ?? null, $x['reference'] ?? null, $x['reason'] ?? null, $x['notes'] ?? null, $x['received_date'] ?? null]);
        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}
