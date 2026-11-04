<?php
require_once __DIR__ . '/layout.php';
if (current_user()) redirect('dashboard.php');
$errors = []; $name = ''; $email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $pass = $_POST['password'] ?? '';
    $conf = $_POST['confirm'] ?? '';
    if ($name === '') $errors['name'] = 'Full name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Enter a valid email address.';
    if (strlen($pass) < 8 || !preg_match('/[A-Za-z]/', $pass) || !preg_match('/\d/', $pass)) $errors['password'] = 'Password must be at least 8 characters with a letter and a number.';
    if ($pass !== $conf) $errors['confirm'] = 'Passwords do not match.';
    if (!$errors) {
        $st = $pdo->prepare('SELECT 1 FROM users WHERE email = ?'); $st->execute([$email]);
        if ($st->fetch()) $errors['email'] = 'This email is already registered.';
        else {
            $pdo->prepare('INSERT INTO users (name, email, password_hash, role) VALUES (?,?,?,"staff")')->execute([$name, $email, password_hash($pass, PASSWORD_BCRYPT)]);
            session_regenerate_id(true);
            $_SESSION['uid'] = $pdo->lastInsertId();
            flash_set('success', 'Account created', 'Welcome to Stockroom!');
            redirect('dashboard.php');
        }
    }
}
$fe = fn($k) => isset($errors[$k]) ? '<div class="field-error" role="alert">' . e($errors[$k]) . '</div>' : '';
$ec = fn($k) => isset($errors[$k]) ? ' error' : '';
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Sign Up · Stockroom</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="phone-wrapper"><div class="auth-wrap">
    <header class="auth-header">
        <h1>Welcome.</h1>
        <div class="auth-logo"><?= icon('layers', 34) ?> stockroom</div>
        <p>Inventory Monitoring System</p>
    </header>
    <section class="auth-card">
        <h2>Create an account</h2>
        <form method="post" data-signup>
            <div class="form-group"><label for="name">Full name</label><input type="text" name="name" id="name" class="form-control<?= $ec('name') ?>" placeholder="Enter your full name" value="<?= e($name) ?>" required><?= $fe('name') ?></div>
            <div class="form-group"><label for="email">Email</label><input type="email" name="email" id="email" class="form-control<?= $ec('email') ?>" placeholder="Enter email address" value="<?= e($email) ?>" required><?= $fe('email') ?></div>
            <div class="form-group"><label for="password">Password</label>
                <div class="pw-wrap"><input type="password" name="password" id="password" class="form-control<?= $ec('password') ?>" placeholder="Enter your password" required>
                    <button type="button" class="eye" data-eye aria-label="Show or hide password"><?= icon('eye', 20) ?></button></div>
                <?= $fe('password') ?>
                <ul class="pw-rules" id="pwRules"><li>At least 8 characters</li><li>Contains a letter</li><li>Contains a number</li></ul></div>
            <div class="form-group"><label for="confirm">Confirm password</label><div class="pw-wrap"><input type="password" name="confirm" id="confirm" class="form-control<?= $ec('confirm') ?>" placeholder="Re-enter your password" required>
                <button type="button" class="eye" data-eye aria-label="Show or hide password"><?= icon('eye', 20) ?></button></div><?= $fe('confirm') ?></div>
            <button type="submit" class="btn primary block" id="authBtn" disabled>Create account</button>
            <p class="hint">Complete every field and meet all password rules to continue.</p>
        </form>
        <p class="auth-foot">Already have an account? <a href="index.php">Log in</a></p>
    </section>
</div></div>
<script src="app.js"></script>
</body>
</html>
