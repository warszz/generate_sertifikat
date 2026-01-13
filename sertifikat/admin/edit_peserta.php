<?php
session_start();
require_once '../config/database.php';

if (empty($_GET['id'])) {
    header("Location: generate.php");
    exit();
}

$id = intval($_GET['id']);

// Ambil data peserta dan verifikasi ownership
$stmt = $pdo->prepare("
    SELECT p.*, t.nama_template
    FROM peserta p
    JOIN template_sertifikat t ON p.template_id = t.id
    WHERE p.id = ? AND p.username = ?
");
$stmt->execute([$id, $_SESSION['user']]);

if ($stmt->rowCount() === 0) {
    $_SESSION['errors'] = ["Data peserta tidak ditemukan atau Anda tidak memiliki akses"];
    header("Location: generate.php");
    exit();
}

$peserta = $stmt->fetch(PDO::FETCH_ASSOC);

// Ambil semua template untuk dropdown
$templates = $pdo->query("SELECT id, nama_template FROM template_sertifikat ORDER BY id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Peserta - Sistem Sertifikat</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 500px;
        }
        
        h1 {
            color: #333;
            margin-bottom: 10px;
            text-align: center;
            font-size: 28px;
        }
        
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            color: #333;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        input[type="text"],
        select {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        input[type="text"]:focus,
        select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        button,
        .btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            text-decoration: none;
            text-align: center;
            display: inline-block;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: #ccc;
            color: #333;
        }
        
        .btn-secondary:hover {
            background: #bbb;
        }
        
        .alert {
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 13px;
        }
        
        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        
        .alert-error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        
        .info-box {
            background: #f0f4ff;
            border-left: 4px solid #667eea;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>✏️ Edit Data Peserta</h1>
        <p class="subtitle">Ubah informasi peserta yang sudah terdaftar</p>
        
        <div class="info-box">
            Peserta ID: #<?= $peserta['id']; ?>
        </div>
        
        <?php if (isset($_SESSION['errors'])): ?>
            <div class="alert alert-error">
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    ✗ <?= htmlspecialchars($error); ?><br>
                <?php endforeach; ?>
            </div>
            <?php unset($_SESSION['errors']); ?>
        <?php endif; ?>
        
        <form action="update_peserta.php" method="POST">
            <input type="hidden" name="id" value="<?= $peserta['id']; ?>">
            
            <div class="form-group">
                <label for="nama">Nama Peserta *</label>
                <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($peserta['nama']); ?>" placeholder="Masukkan nama lengkap" required>
            </div>

            <div class="form-group">
                <label for="instansi">Instansi/Organisasi *</label>
                <input type="text" id="instansi" name="instansi" value="<?= htmlspecialchars($peserta['instansi']); ?>" placeholder="Masukkan nama instansi" required>
            </div>

            <div class="form-group">
                <label for="peran">Sebagai (Peran) *</label>
                <select id="peran" name="peran" required>
                    <option value="">-- Pilih Peran --</option>
                    <option value="Peserta" <?= $peserta['peran'] === 'Peserta' ? 'selected' : ''; ?>>Peserta</option>
                    <option value="Panitia" <?= $peserta['peran'] === 'Panitia' ? 'selected' : ''; ?>>Panitia</option>
                    <option value="Pemateri" <?= $peserta['peran'] === 'Pemateri' ? 'selected' : ''; ?>>Pemateri</option>
                    <option value="Narasumber" <?= $peserta['peran'] === 'Narasumber' ? 'selected' : ''; ?>>Narasumber</option>
                    <option value="Fasilitator" <?= $peserta['peran'] === 'Fasilitator' ? 'selected' : ''; ?>>Fasilitator</option>
                </select>
            </div>

            <div class="form-group">
                <label for="template_id">Template Sertifikat *</label>
                <select id="template_id" name="template_id" required>
                    <option value="">-- Pilih Template --</option>
                    <?php foreach ($templates as $tpl): ?>
                        <option value="<?= $tpl['id']; ?>" <?= $peserta['template_id'] == $tpl['id'] ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($tpl['nama_template']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="button-group">
                <button type="submit" class="btn-primary">💾 Perbarui Data</button>
                <a href="generate.php" class="btn btn-secondary">← Kembali</a>
            </div>
        </form>
    </div>
</body>
</html>