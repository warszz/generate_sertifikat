<?php
// File untuk check apakah user sudah login
// Include ini di awal file admin

session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../login.php');
    exit();
}

// Get username dari session
$current_user = $_SESSION['user'] ?? 'Admin';
?>
