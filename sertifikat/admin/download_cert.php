<?php
/**
 * Download Certificate PDF
 */
session_start();
require_once '../config/database.php';

if (empty($_SESSION['user']) || empty($_GET['id'])) {
    header("Location: custom_certificates.php");
    exit;
}

$username = $_SESSION['user'];
$pesertaId = intval($_GET['id']);

try {
    // Verify user owns this participant
    $stmt = $pdo->prepare("
        SELECT gcp.file_path, ccp.nama
        FROM generated_custom_pdf gcp
        JOIN custom_cert_peserta ccp ON gcp.peserta_id = ccp.id
        WHERE ccp.id = ? AND ccp.username = ?
    ");
    $stmt->execute([$pesertaId, $username]);

    if ($stmt->rowCount() === 0) {
        die("Unauthorized");
    }

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $filepath = $row['file_path'];
    $nama = $row['nama'];

    if (!file_exists($filepath)) {
        die("File not found");
    }

    // Download file
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . str_replace(' ', '_', $nama) . '.pdf"');
    readfile($filepath);
    exit;

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>
