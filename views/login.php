<?php
require_once '../controllers/AuthController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth = new AuthController();
    if ($auth->login($_POST)) {
        header("Location: dashboard.php");
    } else {
        echo "Login gagal.";
    }
}
?>

<h2>Login</h2>
<form method="POST">
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Login</button>
</form>