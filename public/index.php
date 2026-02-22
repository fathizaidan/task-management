<!DOCTYPE html>
<html>
<head>
    <title>Task Management</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>

<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once dirname(__DIR__) . '/routes.php';