<?php
require_once __DIR__ . '/../controllers/AuthController.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $auth = new AuthController();

    if ($auth->register($_POST)) {
        header("Location: index.php?page=login");
        exit();
    } else {
        $error = "Register gagal. Email mungkin sudah digunakan.";
    }
}
?>

<div class="login-page">
    <div class="login-card">

        <h2>Create Account </h2>
        <p class="login-subtitle">Register to get started</p>

        <?php if ($error): ?>
            <div class="login-error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">

            <div class="input-group">
                <label>Nama</label>
                <input type="text" name="name" required>
            </div>

            <div class="input-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>

            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <button type="submit" class="login-btn">Register</button>

        </form>

        <div class="login-footer">
            Sudah punya akun?
            <a href="index.php?page=login">Login</a>
        </div>

    </div>
</div>