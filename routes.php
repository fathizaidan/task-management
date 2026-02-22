<?php

$page = $_GET['page'] ?? 'login';

switch ($page) {

    case 'login':
        require_once __DIR__ . '/views/login.php';
        break;

    case 'register':
        require_once __DIR__ . '/views/register.php';
        break;

    case 'dashboard':
        require_once __DIR__ . '/views/dashboard.php';
        break;

    case 'project-detail':
        require_once __DIR__ . '/views/project_detail.php';
        break;

    case 'task-action':
        require_once __DIR__ . '/controllers/TaskController.php';
        break;

    case 'logout':
        session_unset();
        session_destroy();
        header("Location: index.php?page=login");
        exit();
        break;

    default:
        echo "<h2>404 - Page Not Found</h2>";
}