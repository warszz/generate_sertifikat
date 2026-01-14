<?php
require_once 'check_login.php';
require_once '../config/database.php';

$username = $_SESSION['user'];

// Query UNION: Get both default peserta and custom certificate participants
$stmt = $pdo->prepare("
    SELECT 
        p.id,
        p.nama,
        p.instansi,
        p.peran,
        'default' as tipe,
        t.nama_template as template_name,
        p.dibuat_pada,
        NULL as custom_cert_id
    FROM peserta p
    LEFT JOIN template_sertifikat t ON p.template_id = t.id
    WHERE p.username = ?
    
    UNION ALL
    
    SELECT 
        ccp.id,
        ccp.nama,
        ccp.instansi,
        ccp.peran,
        'custom' as tipe,
        cc.nama as template_name,
        ccp.dibuat_pada,
        ccp.custom_cert_id
    FROM custom_cert_peserta ccp
    LEFT JOIN custom_certificates cc ON ccp.custom_cert_id = cc.id
    WHERE ccp.username = ?
    
    ORDER BY dibuat_pada DESC
");
$stmt->execute([$username, $username]);
$data = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Sertifikat - Sistem Sertifikat</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            padding: 30px 20px;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        h1 {
            color: #333;
            font-size: 28px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-danger {
            background: #e74c3c;
            color: white;
            font-size: 12px;
        }
        
        .btn-danger:hover {
            background: #c0392b;
        }
        
        .btn-success {
            background: #27ae60;
            color: white;
            font-size: 12px;
        }
        
        .btn-success:hover {
            background: #229954;
        }
        
        .btn-warning {
            background: #f39c12;
            color: white;
            font-size: 12px;
        }
        
        .btn-warning:hover {
            background: #d68910;
        }
        
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
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
        
        .table-container {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        tbody tr:hover {
            background: #f9f9f9;
        }
        
        .action-buttons {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }
        
        .empty-state {
            background: white;
            padding: 40px;
            text-align: center;
            border-radius: 10px;
            color: #666;
        }
        
        .empty-state p {
            margin-bottom: 20px;
            font-size: 16px;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .badge-primary {
            background: #cce5ff;
            color: #004085;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h1>📊 Daftar Peserta</h1>
                <p style="color: #666; margin-top: 5px;">Kelola dan generate sertifikat peserta</p>
            </div>
            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <span style="color: #666; font-size: 13px;">👤 <?= htmlspecialchars($current_user); ?></span>
                <a href="form_peserta.php" class="btn btn-primary">➕ Tambah Peserta Baru</a>
                <a href="custom_certificates.php" class="btn btn-primary" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">🎨 Custom Builder</a>
                <a href="../logout.php" class="btn btn-danger" style="background: #e74c3c; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; font-size: 13px; cursor: pointer;">🚪 Logout</a>
            </div>
        </div>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                ✓ <?= htmlspecialchars($_SESSION['success']); ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['errors'])): ?>
            <div class="alert alert-error">
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    ✗ <?= htmlspecialchars($error); ?><br>
                <?php endforeach; ?>
            </div>
            <?php unset($_SESSION['errors']); ?>
        <?php endif; ?>
        
        <?php if (count($data) === 0): ?>
            <div class="empty-state">
                <p>📭 Belum ada data peserta</p>
                <a href="form_peserta.php" class="btn btn-primary">Buat Peserta Baru</a>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Peserta</th>
                            <th>Instansi</th>
                            <th>Peran</th>
                            <th>Template</th>
                            <th>Tipe</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($data as $index => $d): ?>
                        <tr>
                            <td><?= $index + 1; ?></td>
                            <td><strong><?= htmlspecialchars($d['nama']); ?></strong></td>
                            <td><?= htmlspecialchars($d['instansi']); ?></td>
                            <td><span class="badge badge-info"><?= htmlspecialchars($d['peran']); ?></span></td>
                            <td><span class="badge badge-primary"><?= htmlspecialchars($d['template_name']); ?></span></td>
                            <td>
                                <?php if ($d['tipe'] === 'default'): ?>
                                    <span class="badge" style="background: #e8f5e9; color: #2e7d32;">📋 Default</span>
                                <?php else: ?>
                                    <span class="badge" style="background: #f3e5f5; color: #6a1b9a;">🎨 Custom</span>
                                <?php endif; ?>
                            </td>
                            <td style="font-size: 12px; color: #999;"><?= date('d/m/Y H:i', strtotime($d['dibuat_pada'])); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <?php if ($d['tipe'] === 'default'): ?>
                                        <a href="edit_peserta.php?id=<?= $d['id']; ?>" class="btn btn-warning">✏️ Edit</a>
                                        <a href="generate_pdf.php?id=<?= $d['id']; ?>" class="btn btn-success">📄 PDF</a>
                                        <a href="delete_peserta.php?id=<?= $d['id']; ?>" class="btn btn-danger" onclick="return confirm('Yakin hapus data ini?')">🗑️ Hapus</a>
                                    <?php else: ?>
                                        <a href="generate_pdf.php?type=custom&template_id=<?= $d['custom_cert_id']; ?>&peserta_id=<?= $d['id']; ?>" class="btn btn-success">📄 PDF</a>
                                        <form method="POST" action="delete_custom_peserta.php" style="display: inline;">
                                            <input type="hidden" name="peserta_id" value="<?= $d['id']; ?>">
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin hapus data ini?')">🗑️ Hapus</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
