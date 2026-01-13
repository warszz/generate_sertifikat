<?php
session_start();
require_once '../config/database.php';

// Validasi input
$errors = [];

if (empty($_POST['id'])) {
    $errors[] = "ID peserta tidak valid";
} else {
    $id = intval($_POST['id']);
    
    // Cek apakah peserta ada dan milik user ini
    $check = $pdo->prepare("SELECT id FROM peserta WHERE id = ? AND username = ?");
    $check->execute([$id, $_SESSION['user']]);
    if ($check->rowCount() === 0) {
        $errors[] = "Data peserta tidak ditemukan atau Anda tidak memiliki akses";
    }
}

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
    header("Location: edit_peserta.php?id=" . intval($_POST['id']));
    exit();
}

try {
    $id = intval($_POST['id']);
    
    $stmt = $pdo->prepare("
        UPDATE peserta 
        SET nama = ?, instansi = ?, peran = ?, template_id = ?
        WHERE id = ?
    ");

    $stmt->execute([
        trim($_POST['nama']),
        trim($_POST['instansi']),
        trim($_POST['peran']),
        intval($_POST['template_id']),
        $id
    ]);

    $_SESSION['success'] = "Data peserta berhasil diperbarui!";
    header("Location: generate.php");
} catch (PDOException $e) {
    $_SESSION['errors'] = ["Gagal memperbarui data: " . $e->getMessage()];
    header("Location: edit_peserta.php?id=" . intval($_POST['id']));
}
?>
