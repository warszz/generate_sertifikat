<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Manajemen Sertifikat</title>
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
            padding: 60px 40px;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            text-align: center;
            max-width: 600px;
            width: 100%;
        }
        
        .logo {
            font-size: 60px;
            margin-bottom: 20px;
        }
        
        h1 {
            color: #333;
            font-size: 32px;
            margin-bottom: 10px;
        }
        
        .subtitle {
            color: #666;
            font-size: 16px;
            margin-bottom: 40px;
            line-height: 1.6;
        }
        
        .features {
            text-align: left;
            background: #f9f9f9;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 40px;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            color: #555;
            font-size: 14px;
        }
        
        .feature-item:last-child {
            margin-bottom: 0;
        }
        
        .feature-icon {
            font-size: 24px;
            margin-right: 15px;
            min-width: 30px;
        }
        
        .buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .btn {
            padding: 14px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
            font-size: 15px;
            flex: 1;
            min-width: 150px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .btn-secondary {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }
        
        .btn-secondary:hover {
            background: #667eea;
            color: white;
        }
        
        .info-box {
            background: #e8f4f8;
            border-left: 4px solid #667eea;
            padding: 15px;
            border-radius: 5px;
            margin-top: 30px;
            font-size: 13px;
            color: #555;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">🎓</div>
        
        <h1>Sistem Manajemen Sertifikat</h1>
        <p class="subtitle">
            Platform lengkap untuk membuat dan mengelola sertifikat digital<br>
            dengan berbagai template yang dapat disesuaikan
        </p>
        
        <div class="features">
            <div class="feature-item">
                <div class="feature-icon">📋</div>
                <div>Kelola data peserta dengan mudah dan cepat</div>
            </div>
            <div class="feature-item">
                <div class="feature-icon">🎨</div>
                <div>3 template desain sertifikat profesional</div>
            </div>
            <div class="feature-item">
                <div class="feature-icon">📄</div>
                <div>Generate PDF berkualitas tinggi secara otomatis</div>
            </div>
            <div class="feature-item">
                <div class="feature-icon">⚡</div>
                <div>Proses cepat dan efisien</div>
            </div>
        </div>
        
        <div class="buttons">
            <a href="login.php" class="btn btn-primary">🔐 Login Sistem</a>
            <a href="register.php" class="btn btn-secondary">📝 Daftar Akun Baru</a>
        </div>
        
        
    </div>
</body>
</html>