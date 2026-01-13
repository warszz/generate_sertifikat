<?php
session_start();
require_once '../config/database.php';

if (empty($_GET['id'])) {
    header("Location: generate.php");
    exit();
}

$id = intval($_GET['id']);

try {
    // Check if peserta exists and belongs to user
    $check = $pdo->prepare("SELECT id FROM peserta WHERE id = ? AND username = ?");
    $check->execute([$id, $_SESSION['user']]);
    
    if ($check->rowCount() === 0) {
        $_SESSION['errors'] = ["Data peserta tidak ditemukan atau Anda tidak memiliki akses"];
        header("Location: generate.php");
        exit();
    }
    
    // Delete peserta
    $stmt = $pdo->prepare("DELETE FROM peserta WHERE id = ?");
    $stmt->execute([$id]);
    
    $_SESSION['success'] = "Data peserta berhasil dihapus!";
    header("Location: generate.php");
} catch (PDOException $e) {
    $_SESSION['errors'] = ["Error: " . $e->getMessage()];
    header("Location: generate.php");
}
?>
