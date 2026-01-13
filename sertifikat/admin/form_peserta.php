<?php require_once 'check_login.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Input Peserta - Sistem Sertifikat</title>
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
        
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
        }
        
        .back-link a:hover {
            text-decoration: underline;
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
        <h1>📋 Input Data Peserta</h1>
        <p class="subtitle">Buat dan kelola sertifikat dengan mudah</p>
        
        <div class="info-box">
            Isi semua field dibawah untuk membuat sertifikat baru
        </div>
        
        <form action="simpan_peserta.php" method="POST">
            <div class="form-group">
                <label for="nama">Nama Peserta *</label>
                <input type="text" id="nama" name="nama" placeholder="Masukkan nama lengkap" required>
            </div>

            <div class="form-group">
                <label for="instansi">Instansi/Organisasi *</label>
                <input type="text" id="instansi" name="instansi" placeholder="Masukkan nama instansi" required>
            </div>

            <div class="form-group">
                <label for="peran">Sebagai (Peran) *</label>
                <select id="peran" name="peran" required>
                    <option value="">-- Pilih Peran --</option>
                    <option value="Peserta">Peserta</option>
                    <option value="Panitia">Panitia</option>
                    <option value="Pemateri">Pemateri</option>
                    <option value="Narasumber">Narasumber</option>
                    <option value="Fasilitator">Fasilitator</option>
                </select>
            </div>

            <div class="form-group">
                <label for="template_id">Template Sertifikat *</label>
                <select id="template_id" name="template_id" required>
                    <option value="">-- Pilih Template --</option>
                    <option value="1">🎓 Template Seminar</option>
                    <option value="2">🛠️ Template Workshop</option>
                    <option value="3">📚 Template Pelatihan</option>
                </select>
            </div>

            <button type="submit">💾 Simpan dan Buat Sertifikat</button>
            
            <div class="back-link">
                <a href="generate.php">← Lihat Data Peserta</a>
            </div>
        </form>
    </div>
</body>
</html>
