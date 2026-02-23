<?php
require_once __DIR__ . '/../models/User.php';

class AuthController {

    public function register($data) {
        $user = new User();

        $user->name = htmlspecialchars(strip_tags($data['name']));
        $user->email = htmlspecialchars(strip_tags($data['email']));
        $user->password = password_hash($data['password'], PASSWORD_DEFAULT);

        return $user->register();
    }

    public function login($data) {
        $user = new User();

        $user->email = $data['email'];
        $result = $user->login();

        if ($result && password_verify($data['password'], $result['password'])) {
            session_start();
            $_SESSION['user_id'] = $result['id'];
            $_SESSION['user_name'] = $result['name'];
            return true;
        }

        return false;
    }

    public function logout() {
        session_start();
        session_destroy();
        header("Location: index.php");
    }
}