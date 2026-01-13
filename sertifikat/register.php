<?php
require_once 'config/database.php';

session_start();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validasi
    if (empty($username)) {
        $error = 'Username harus diisi!';
    } else if (strlen($username) < 3) {
        $error = 'Username minimal 3 karakter!';
    } else if (empty($password)) {
        $error = 'Password harus diisi!';
    } else if (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter!';
    } else if ($password !== $confirm_password) {
        $error = 'Password tidak cocok!';
    } else {
        // Cek apakah username sudah terdaftar
        $check = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $check->execute([$username]);
        
        if ($check->rowCount() > 0) {
            $error = 'Username sudah terdaftar!';
        } else {
            // Register ke database
            try {
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $insert = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
                $insert->execute([$username, $hashed_password]);
                
                $success = 'Registrasi berhasil! Silakan login dengan akun baru Anda.';
                // Clear form
                $username = '';
                $password = '';
                $confirm_password = '';
            } catch (PDOException $e) {
                $error = 'Gagal mendaftar: ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Sistem Sertifikat</title>
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
            padding: 50px 40px;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 450px;
        }
        
        .logo {
            text-align: center;
            font-size: 50px;
            margin-bottom: 20px;
        }
        
        h1 {
            color: #333;
            font-size: 28px;
            text-align: center;
            margin-bottom: 10px;
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
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 10px;
        }
        
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .alert {
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 13px;
        }
        
        .alert-error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        
        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        
        .password-requirements {
            background: #f0f4ff;
            border-left: 4px solid #667eea;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 12px;
            color: #555;
        }
        
        .password-requirements li {
            margin-left: 20px;
            margin-bottom: 5px;
        }
        
        .login-link {
            text-align: center;
            margin-top: 20px;
            font-size: 13px;
            color: #666;
        }
        
        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        .back-link {
            text-align: center;
            margin-top: 15px;
        }
        
        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-size: 13px;
        }
        
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">📝</div>
        
        <h1>Registrasi</h1>
        <p class="subtitle">Buat akun baru untuk Sistem Sertifikat</p>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                ✗ <?= htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                ✓ <?= htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <div class="password-requirements">
            <strong>📋 Persyaratan Password:</strong>
            <ul>
                <li>Minimal 6 karakter</li>
                <li>Harus sama di semua field</li>
            </ul>
        </div>
        
        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Masukkan username (min 3 karakter)" value="<?= htmlspecialchars($username ?? ''); ?>" required autofocus>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Masukkan password (min 6 karakter)" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Ketik password lagi" required>
            </div>
            
            <button type="submit">✓ Daftar Akun</button>
        </form>
        
        <div class="login-link">
            Sudah punya akun? <a href="login.php">Masuk di sini</a>
        </div>
        
        <div class="back-link">
            <a href="index.php">← Kembali ke Homepage</a>
        </div>
    </div>
</body>
</html>
