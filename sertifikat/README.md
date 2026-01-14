# Sistem Manajemen Sertifikat

Sistem lengkap untuk membuat dan mengelola sertifikat digital dengan template yang dapat disesuaikan.

## 📋 Fitur Utama

- ✅ Form input data peserta
- ✅ 3 template desain sertifikat profesional
- ✅ Generate PDF otomatis
- ✅ Kelola daftar peserta
- ✅ Delete/hapus peserta
- ✅ Validasi input data

## 🚀 Instalasi & Setup

### 1. Database Setup
```sql
-- Database sudah otomatis dibuat dengan menjalankan config/setup_db.php
```

Atau buka browser dan akses: `http://localhost/sertifikat/config/setup_db.php`

### 2. Install Dompdf (untuk PDF generation)
```bash
cd c:\laragon\www\sertifikat
composer require dompdf/dompdf
```

### 3. Konfigurasi Database
Edit file `config/database.php` sesuai konfigurasi lokal:
```php
$host = 'localhost';      // Host database
$db = 'sertifikat_db';    // Nama database
$user = 'root';           // Username database
$password = '';           // Password database
```

## 📁 Struktur Project

```
sertifikat/
├── admin/
│   ├── form_peserta.php      // Form input peserta
│   ├── simpan_peserta.php    // Simpan data peserta
│   ├── generate.php          // Daftar peserta
│   ├── generate_pdf.php      // Generate PDF
│   └── delete_peserta.php    // Delete peserta
├── config/
│   ├── database.php          // Konfigurasi database
│   └── setup_db.php          // Setup database
├── templates/
│   ├── sertifikat_1.php      // Template Seminar
│   ├── sertifikat_2.php      // Template Workshop
│   └── sertifikat_3.php      // Template Pelatihan
└── index.php                 // Homepage
└── login.php                 
└── logout.php
└── register.php
```

## 📝 Penggunaan

### Alur Kerja Sistem

1. **Buka Homepage**
   - Akses: `http://localhost/sertifikat/`
2. **Autentikasi**
   -resgister untuk mebuat akun 
   -login ke akun yang sudah ada 
   -logout keluar dari akun 

2. **Tambah Peserta Baru**
   - Klik "Buat Sertifikat Baru"
   - Isi form dengan data peserta
   - Pilih template sertifikat
   - Klik "Simpan"

3. **Lihat Daftar Peserta**
   - Klik "Lihat Daftar Peserta"
   - Tampil tabel semua peserta

4. **Generate PDF**
   - Klik tombol "PDF" di samping nama peserta
   - PDF akan di-download otomatis

5. **Hapus Peserta**
   - Klik tombol "Hapus" 
   - Konfirmasi penghapusan

## 🎨 Template Sertifikat

### Template 1: Seminar
- Design klasik dengan background gold gradient
- Cocok untuk acara seminar dan konferensi

### Template 2: Workshop
- Design modern dengan warna biru
- Cocok untuk workshop dan training teknis

### Template 3: Pelatihan
- Design elegan dengan border dekoratif
- Cocok untuk program pelatihan dan certification

## 🔧 Teknologi yang Digunakan

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Library PDF**: Dompdf
- **Frontend**: HTML5, CSS3

## 📋 Data Peserta

Field yang disimpan:
- Nama Peserta (required)
- Instansi (required)
- Peran (required) - Peserta, Panitia, Pemateri, Narasumber, Fasilitator
- Template ID (required) - ID template yang dipilih
- Tanggal dibuat (auto)

## 🛠️ Troubleshooting

### Error: "Dompdf library tidak ditemukan"
**Solusi**: Install dompdf dengan command:
```bash
composer require dompdf/dompdf
```

### Error: "Connection Error"
**Solusi**: 
- Pastikan MySQL sudah running
- Cek konfigurasi di `config/database.php`
- Pastikan database `sertifikat_db` sudah dibuat

### Error: "Template file tidak ditemukan"
**Solusi**: 
- Pastikan file template ada di folder `templates/`
- Nama file template harus sesuai di database

### PDF tidak ter-generate
**Solusi**:
- Pastikan data peserta sudah tersimpan
- Check PHP error log
- Pastikan folder `assets/` memiliki write permission

## 📞 Support

Untuk pertanyaan atau masalah, silakan cek:
1. Error message di browser
2. PHP error log
3. Database connection status

## 📄 Lisensi

Free to use for educational and commercial purposes

---

**Versi**: 1.0
**Last Updated**: 2026-01-13
