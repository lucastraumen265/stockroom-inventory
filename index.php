<?php
require_once __DIR__ . '/layout.php';
if (current_user()) redirect('dashboard.php');
$error = ''; $email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $pass = $_POST['password'] ?? '';
    $st = $pdo->prepare('SELECT id, password_hash FROM users WHERE email = ?');
    $st->execute([$email]);
    $row = $st->fetch();
    if ($row && password_verify($pass, $row['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['uid'] = $row['id'];
        redirect('dashboard.php');
    }
    $error = 'Invalid email or password. Please try again.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Login · Stockroom</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="phone-wrapper"><div class="auth-wrap">
    <header class="auth-header">
        <h1>Welcome.</h1>
        <div class="auth-logo"><?= icon('layers', 34) ?> stockroom</div>
        <p>Inventory Monitoring System</p>
    </header>
    <section class="auth-card">
        <h2>Login to your account</h2>
        <form method="post" data-auth>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="Enter email address" value="<?= e($email) ?>" autocomplete="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <div class="pw-wrap">
                    <input type="password" name="password" id="password" class="form-control <?= $error ? 'error' : '' ?>" placeholder="Enter your password" autocomplete="current-password" required>
                    <button type="button" class="eye" data-eye aria-label="Show or hide password"><?= icon('eye', 20) ?></button>
                </div>
                <?php if ($error): ?><div class="alert" role="alert"><?= e($error) ?></div><?php endif; ?>
            </div>
            <button type="submit" class="btn primary block" id="authBtn" disabled>Log in</button>
            <p class="hint">Enter your email and password to log in.</p>
        </form>
        <p class="auth-foot">Don't have an account? <a href="signup.php">Sign up</a></p>
    </section>
</div></div>
<script src="app.js"></script>
</body>
</html>
