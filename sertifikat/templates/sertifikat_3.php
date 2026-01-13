<?php
// Template Pelatihan (Training) Certificate - Professional Design
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * {
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        
        .certificate {
            width: 21cm;
            height: 29.7cm;
            background: #f6e9ff;
            position: relative;
            overflow: hidden;
            margin: auto;
            page-break-after: avoid;
        }
        
        /* Left Border - Purple */
        .left-border {
            position: absolute;
            left: 0;
            top: 0;
            width: 20px;
            height: 100%;
            background: linear-gradient(to bottom, #6b21a8 0%, #a855f7 50%, #6b21a8 100%);
        }
        
        /* Top Border */
        .top-border {
            position: absolute;
            top: 0;
            left: 20px;
            right: 0;
            height: 8px;
            background: linear-gradient(to right, #a855f7 0%, #c084fc 50%, #a855f7 100%);
        }
        
        .content {
            padding: 60px 60px 50px 80px;
            text-align: center;
            min-height: 700px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .badge {
            font-size: 50px;
            margin-bottom: 20px;
            opacity: 0.8;
        }
        
        .title {
            font-size: 64px;
            color: #a855f7;
            font-weight: bold;
            margin-bottom: 5px;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        
        .subtitle {
            font-size: 20px;
            color: #6b21a8;
            margin-bottom: 40px;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-weight: 600;
            border-bottom: 2px solid #a855f7;
            padding-bottom: 15px;
            display: inline-block;
            min-width: 400px;
        }
        
        .intro-text {
            font-size: 16px;
            color: #555;
            margin-bottom: 40px;
            line-height: 1.6;
        }
        
        .recipient-name {
            font-size: 56px;
            color: #a855f7;
            font-weight: bold;
            margin: 40px 0 20px 0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .detail-section {
            background: #f6e9ff;
            padding: 25px;
            border-radius: 8px;
            margin: 30px auto;
            max-width: 500px;
            line-height: 1.8;
        }
        
        .detail-row {
            font-size: 15px;
            color: #333;
            margin: 10px 0;
        }
        
        .detail-label {
            font-weight: 600;
            color: #6b21a8;
        }
        
        .certificate-text {
            font-size: 14px;
            color: #666;
            line-height: 1.7;
            margin: 30px 0;
            max-width: 700px;
        }
        
        .signature-section {
            display: flex;
            justify-content: space-around;
            align-items: flex-end;
            margin-top: 60px;
            padding-top: 40px;
            border-top: 1px solid #ddd;
            width: 100%;
        }
        
        .signature-box {
            text-align: center;
            width: 140px;
        }
        
        .signature-space {
            height: 50px;
            margin-bottom: 5px;
        }
        
        .signature-line {
            border-top: 2px solid #333;
            margin: 10px 0;
        }
        
        .signature-title {
            font-size: 12px;
            color: #333;
            font-weight: 600;
            margin-top: 5px;
        }
        
        .date-box {
            text-align: center;
            font-size: 13px;
            color: #666;
        }
        
        .certificate-number {
            font-size: 12px;
            color: #999;
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="left-border"></div>
        <div class="top-border"></div>
        
        <div class="content">
            
            <div class="title">SERTIFIKAT</div>
            
            <div class="subtitle">PENYELESAIAN PELATIHAN</div>
            
            <div class="intro-text">
                Dengan hormat kami umumkan bahwa
            </div>
            
            <div class="recipient-name"><?= htmlspecialchars(strtoupper($nama)); ?></div>
            
            <div class="detail-section">
                <div class="detail-row">
                    <span class="detail-label">Peran:</span> <?= htmlspecialchars($peran); ?>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Dari Instansi:</span> <?= htmlspecialchars($instansi); ?>
                </div>
            </div>
            
            <div class="certificate-text">
                Telah berhasil menyelesaikan program pelatihan dengan prestasi memuaskan<br>
                dan menunjukkan komitmen serta dedikasi tinggi dalam mengikuti<br>
                seluruh rangkaian kegiatan pelatihan.
            </div>
            
            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-space"></div>
                    <div class="signature-line"></div>
                    <div class="signature-title">Penyelenggara</div>
                </div>
                <div class="signature-box date-box">
                    <div style="margin-top: 30px;">
                        <?= date('d F Y', strtotime(date('Y-m-d'))); ?>
                    </div>
                </div>
                <div class="signature-box">
                    <div class="signature-space"></div>
                    <div class="signature-line"></div>
                    <div class="signature-title">Direktur Pelatihan</div>
                </div>
            </div>
            
            <div class="certificate-number">
                Nomor Sertifikat: TR-<?= date('Ymd'); ?>-<?= str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT); ?>
            </div>
        </div>
    </div>
</body>
</html>