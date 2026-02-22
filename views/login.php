<?php
require_once __DIR__ . '/../controllers/AuthController.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth = new AuthController();

    if ($auth->login($_POST)) {
        header("Location: index.php?page=dashboard");
        exit();
    } else {
        $error = "Email atau password salah.";
    }
}
?>

<div class="login-page">
    <div class="login-card">

        <h2>Welcome Back </h2>
        <p class="login-subtitle">Login to continue</p>

        <?php if ($error): ?>
            <div class="login-error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">

            <div class="input-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>

            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <button type="submit" class="login-btn">Login</button>

        </form>

        <div class="login-footer">
            Belum punya akun?
            <a href="index.php?page=register">Buat akun</a>
        </div>

    </div>
</div>