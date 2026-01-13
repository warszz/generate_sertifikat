<?php
session_start();
require_once '../config/database.php';

// Debug: Log POST data
error_log("POST Data: " . print_r($_POST, true));

// Validasi input
$errors = [];

if (empty($_POST['nama'])) {
    $errors[] = "Nama peserta tidak boleh kosong";
} else if (strlen($_POST['nama']) < 3) {
    $errors[] = "Nama peserta minimal 3 karakter";
}

if (empty($_POST['instansi'])) {
    $errors[] = "Instansi tidak boleh kosong";
} else if (strlen($_POST['instansi']) < 3) {
    $errors[] = "Instansi minimal 3 karakter";
}

if (empty($_POST['peran'])) {
    $errors[] = "Peran tidak boleh kosong";
}

if (empty($_POST['template_id'])) {
    $errors[] = "Template sertifikat harus dipilih";
} else {
    // Validasi template_id ada di database
    $check_template = $pdo->prepare("SELECT id FROM template_sertifikat WHERE id = ?");
    $check_template->execute([$_POST['template_id']]);
    if ($check_template->rowCount() === 0) {
        $errors[] = "Template sertifikat tidak valid";
    }
}

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header("Location: form_peserta.php");
    exit();
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO peserta (username, nama, instansi, peran, template_id)
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $_SESSION['user'],
        trim($_POST['nama']),
        trim($_POST['instansi']),
        trim($_POST['peran']),
        intval($_POST['template_id'])
    ]);

    $_SESSION['success'] = "Data peserta berhasil disimpan!";
    header("Location: generate.php");
} catch (PDOException $e) {
    $_SESSION['errors'] = ["Gagal menyimpan data: " . $e->getMessage()];
    header("Location: form_peserta.php");
}
?>
