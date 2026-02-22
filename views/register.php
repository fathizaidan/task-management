<?php
require_once '../controllers/AuthController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth = new AuthController();
    if ($auth->register($_POST)) {
        echo "Register berhasil. <a href='login.php'>Login</a>";
    } else {
        echo "Register gagal.";
    }
}
?>

<h2>Register</h2>
<form method="POST">
    <input type="text" name="name" placeholder="Nama" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Register</button>
</form>