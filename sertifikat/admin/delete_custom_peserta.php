<?php
/**
 * Delete Custom Certificate Participant
 */

require_once 'check_login.php';
require_once '../config/database.php';

$username = $_SESSION['user'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: generate.php');
    exit;
}

$peserta_id = $_POST['peserta_id'] ?? null;

if (!$peserta_id) {
    $_SESSION['errors'] = ['Peserta ID tidak ditemukan'];
    header('Location: generate.php');
    exit;
}

try {
    // Verify ownership
    $checkStmt = $pdo->prepare("
        SELECT id FROM custom_cert_peserta 
        WHERE id = ? AND username = ?
    ");
    $checkStmt->execute([$peserta_id, $username]);
    
    if ($checkStmt->rowCount() === 0) {
        $_SESSION['errors'] = ['Data peserta tidak ditemukan atau Anda tidak memiliki akses'];
        header('Location: generate.php');
        exit;
    }
    
    // Delete peserta
    $deleteStmt = $pdo->prepare("
        DELETE FROM custom_cert_peserta 
        WHERE id = ? AND username = ?
    ");
    $deleteStmt->execute([$peserta_id, $username]);
    
    $_SESSION['success'] = 'Data peserta berhasil dihapus';
    header('Location: generate.php');
    
} catch (PDOException $e) {
    $_SESSION['errors'] = ['Database error: ' . $e->getMessage()];
    header('Location: generate.php');
}
?>
