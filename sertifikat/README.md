# Sistem Manajemen Sertifikat

Sistem lengkap untuk membuat dan mengelola sertifikat digital dengan:
- **3 template desain profesional** (default)
- **Custom Certificate Builder** dengan Fabric.js (Canva-like editor)
- Variable placeholder system untuk personalisasi otomatis
- Generate PDF dengan Dompdf

## 📋 Fitur Utama

### Default Templates
- ✅ 3 template desain sertifikat profesional (Seminar, Workshop, Pelatihan)
- ✅ Form input data peserta
- ✅ Generate PDF otomatis
- ✅ Kelola daftar peserta
- ✅ Delete/hapus peserta
- ✅ Validasi input data

### Custom Certificate Builder (NEW)
- ✅ **Canva-like editor** dengan Fabric.js
- ✅ Add text, placeholder, shapes, images
- ✅ Visual designer (drag & drop)
- ✅ Save template ke database
- ✅ **4 placeholder types**: {nama}, {instansi}, {peran}, {keterangan}
- ✅ Generate PDF dari custom template
- ✅ Fully synced dengan form participant data

## 🚀 Instalasi & Setup

### 1. Database Setup
```bash
# Jalankan migrations untuk setup tables
php config/migrate_builder.php       # Create custom certificate tables
php config/migrate_templates.php     # Create template storage table
php config/add_keterangan_field.php  # Add keterangan field to peserta
php config/add_keterangan_to_custom_peserta.php  # Add keterangan to custom_cert_peserta
```

Atau buka browser: `http://localhost/sertifikat/config/setup_db.php`

### 2. Install Dependencies
```bash
cd c:\laragon\www\sertifikat
composer install
# atau if composer.json exists
composer require dompdf/dompdf
```

### 3. Konfigurasi Database
Edit file `config/database.php`:
```php
$host = 'localhost';
$db = 'sertifikat';
$user = 'root';
$password = '';
```

## 📁 Struktur Project

```
sertifikat/
├── admin/
│   ├── builder.php               // Custom certificate builder (Fabric.js)
│   ├── builder_api.php           // API for builder operations
│   ├── custom_certificates.php   // Custom certificate management
│   ├── form_peserta.php          // Form input peserta
│   ├── simpan_peserta.php        // Simpan data peserta
│   ├── generate.php              // Daftar peserta & generate PDF
│   ├── generate_pdf.php          // PDF generation engine (supports both templates)
│   └── download_cert.php         // Secure PDF download
├── config/
│   ├── database.php              // Database config
│   ├── setup_db.php              // DB setup script
│   ├── migrate_builder.php       // Create builder tables
│   ├── migrate_templates.php     // Create template table
│   ├── add_keterangan_field.php  // Add keterangan to peserta
│   └── add_keterangan_to_custom_peserta.php  // Add keterangan to custom_cert_peserta
├── helpers/
│   ├── replace_variables.php     // Variable placeholder replacement
│   └── fabric_to_html.php        // Convert Fabric.js JSON to HTML
├── templates/
│   ├── sertifikat_1.php          // Template: Seminar (Blue)
│   ├── sertifikat_2.php          // Template: Workshop (Green)
│   ├── sertifikat_3.php          // Template: Pelatihan (Purple)
│   └── save_custom_template.php  // Save custom template endpoint
├── editor/
│   └── index.html                // Standalone Fabric.js editor (A4 landscape)
├── assets/
│   └── img/                      // Images & backgrounds
└── index.php                     // Homepage
```

## 📝 Penggunaan

### A. Default Templates (Original Flow)

**1. Tambah Peserta**
```
http://sertifikat.test/admin/form_peserta.php
- Fill form: Nama, Instansi, Peran, Template
- Klik "Simpan"
- Peserta otomatis muncul di Daftar Peserta
```

**2. Generate PDF**
```
http://sertifikat.test/admin/generate.php
- Click "PDF" untuk generate certificate
- PDF download: sertifikat_NamaPeserta_ddmmyy.pdf
```

### B. Custom Certificate Builder (NEW)

**1. Akses Templates & Manager**
```
http://sertifikat.test/admin/custom_certificates.php
```

**2. Buat Template Baru**
- Click "+ Create New"
- Input: Template Name & Description
- Click "Create & Edit"

**3. Design Certificate** (in /admin/builder.php)
- **Sidebar tools**:
  - Add Text
  - Add Placeholder (4 types: nama, instansi, peran, keterangan)
  - Add Shape (rectangle, circle, line)
  - Upload Image
  - Background color
  
- **Canvas**:
  - Drag & drop objects
  - Edit styling (font, color, size, position)
  - Undo/Redo (50-step history)
  
- **Properties Panel**:
  - Adjust position (X, Y)
  - Adjust size (width, height)
  - Change color & styling

**4. Save Template**
- Click "Save Design"
- Template saved to database

**5. Generate PDF & Add Participant** (NEW - Synced!)
- Back to custom_certificates.php
- Click "Generate" pada template
- **Modal Form**:
  - Participant Name * (required)
  - Institution/Organization
  - Role/Title
  - Keterangan (Optional notes)
- Click "Generate PDF"
- PDF auto-download dengan data participant
- **✨ Data otomatis muncul di Daftar Peserta!**

### 🔄 Data Sync: Custom Participants → Daftar Peserta (NEW!)

**Unified Participant List**

Setelah mengisi form "Add Participant & Generate PDF" di custom_certificates.php, data otomatis tersinkron ke:
```
http://sertifikat.test/admin/generate.php
```

**Daftar Peserta sekarang menampilkan:**
- ✅ Peserta dari default templates (📋 Default badge)
- ✅ Peserta dari custom certificates (🎨 Custom badge)  
- ✅ Sorted by date (newest first)
- ✅ Bisa regenerate PDF dari kedua tipe
- ✅ Bisa delete dari unified list

**Workflow Lengkap:**

```
1. Custom Certificates (custom_certificates.php)
   └─ "Generate" button
   └─ Fill form: Nama, Instansi, Peran, Keterangan
   └─ PDF download
   └─ Data saved to database

2. Generate Peserta (generate.php) - UNIFIED LIST
   └─ Peserta custom muncul automatically
   └─ Badge: 🎨 Custom (vs 📋 Default)
   └─ Click "PDF" untuk regenerate
   └─ Click "Hapus" untuk delete

3. Backend UNION Query:
   └─ SELECT from peserta (default)
   └─ UNION ALL SELECT from custom_cert_peserta (custom)
   └─ Single unified result set
```

**Examples:**

| Scenario | Flow |
|----------|------|
| Create default cert | form_peserta.php → generate.php (📋 Default) |
| Create custom cert | custom_certificates.php → "Generate" → generate.php (🎨 Custom) |
| Regenerate any cert | generate.php → click "PDF" |
| Delete any cert | generate.php → click "Hapus" |

## 🔄 Data Synchronization Feature (NEW!)

### Unified Participant Management

**The Key Feature: Automatic Sync**

Ketika user fill form "Add Participant & Generate PDF" di custom_certificates.php, data automatically:
1. ✅ Save ke custom_cert_peserta table
2. ✅ Generate PDF dengan placeholder replacement
3. ✅ **Muncul di generate.php unified list**

### Database Tables Involved

```
custom_cert_peserta
├─ Stores: participant data dari custom builder
├─ Fields: id, username, nama, instansi, peran, keterangan, custom_cert_id
└─ Relationship: FK custom_cert_id → custom_certificates

peserta
├─ Stores: participant data dari default templates
├─ Fields: id, username, nama, instansi, peran, keterangan, template_id
└─ Relationship: FK template_id → template_sertifikat

generate.php
├─ UNION query both tables
├─ Single result set dengan 'tipe' field (default/custom)
└─ Display with badges: 📋 Default or 🎨 Custom
```

### How Sync Works

**Flow Diagram:**

```
custom_certificates.php
  └─ "Generate" button
  └─ Fill form (nama, instansi, peran, keterangan)
  └─ POST to builder_api.php
  └─ Insert to custom_cert_peserta
  └─ Generate PDF
  └─ Return download_url
  
generate.php (Unified List)
  └─ UNION Query
  ├─ SELECT from peserta WHERE username = ?
  ├─ UNION ALL SELECT from custom_cert_peserta WHERE username = ?
  └─ Display both types with badges
```

**Key Implementation Details:**

1. **Query (generate.php)**:
   - UNION query menggabung peserta + custom_cert_peserta
   - Add field 'tipe' untuk membedakan (default/custom)
   - Sorted by dibuat_pada DESC

2. **Display**:
   - Badge 📋 Default untuk peserta dari default templates
   - Badge 🎨 Custom untuk peserta dari custom certificates
   - Different action buttons based on type

3. **Actions**:
   - Default: Edit, PDF, Hapus (via delete_peserta.php)
   - Custom: PDF, Hapus (via delete_custom_peserta.php)

### Files Involved

```
Backend:
├─ admin/generate.php             # Unified list query
├─ admin/delete_custom_peserta.php # Delete custom participant [NEW]
├─ admin/builder_api.php          # Generate PDF & insert to custom_cert_peserta
├─ admin/custom_certificates.php  # Modal form & API call
└─ config/database.php            # Database config

Database:
├─ peserta table                  # Default participants
└─ custom_cert_peserta table      # Custom participants
```

### Testing Sync Feature

**Step by Step:**

1. Open http://sertifikat.test/admin/custom_certificates.php
2. Create template or select existing one
3. Click "Generate"
4. Fill form:
   - Nama: "John Doe"
   - Instansi: "PT ABC"
   - Peran: "Developer"
   - Keterangan: "Outstanding"
5. Click "Generate PDF" → PDF downloads
6. Open http://sertifikat.test/admin/generate.php
7. **Verify**: "John Doe" muncul di list dengan badge "🎨 Custom"
8. Click "PDF" to regenerate
9. Click "Hapus" to delete

✅ If participant muncul, sync works perfectly!

## 🎨 Placeholder System

### Available Placeholders

| Placeholder | Source | Type | Example |
|-------------|--------|------|---------|
| `{nama}` | peserta.nama | Required | John Doe |
| `{instansi}` | peserta.instansi | Required | PT ABC |
| `{peran}` | peserta.peran | Required | Manager |
| `{keterangan}` | peserta.keterangan | Optional | Peserta berprestasi |

### How It Works

1. **In Builder**: Add text with `{nama}`, `{instansi}`, etc
2. **Save**: Canvas JSON saved with placeholder text
3. **Generate PDF**: 
   - Load participant data from form
   - Replace `{nama}` → "John Doe"
   - Replace `{instansi}` → "PT ABC"
   - etc
4. **Output**: PDF dengan data terintegrasi

### Backward Compatibility

Old format masih support:
- `[NAMA_PESERTA]` → `{nama}`
- `[INSTANSI]` → `{instansi}`
- `[PERAN]` → `{peran}`

## 📊 Database Schema

### peserta (existing)
```sql
- id (INT, PK)
- username (VARCHAR)
- nama (VARCHAR 255) - Participant name
- instansi (VARCHAR 255) - Institution
- peran (VARCHAR 255) - Role
- keterangan (VARCHAR 500) - Optional notes [NEW]
- template_id (INT, FK)
- created_at (TIMESTAMP)
```

### custom_certificates (new)
```sql
- id (INT, PK)
- username (VARCHAR) - Owner
- nama (VARCHAR 255) - Template name
- deskripsi (TEXT) - Description
- design_data (LONGTEXT) - Fabric.js JSON
- canvas_width (INT) - Default: 800
- canvas_height (INT) - Default: 566
- status (VARCHAR) - draft/published
- dibuat_pada (TIMESTAMP)
```

### custom_cert_peserta (new)
```sql
- id (INT, PK)
- username (VARCHAR)
- nama (VARCHAR 200) - Participant name
- instansi (VARCHAR 200) - Institution
- peran (VARCHAR 100) - Role
- keterangan (VARCHAR 500) - Notes [NEW]
- custom_cert_id (INT, FK)
- dibuat_pada (TIMESTAMP)
```

### certificate_templates_custom (new)
```sql
- id (INT, PK)
- name (VARCHAR 255) - Template name
- canvas_json (LONGTEXT) - Fabric.js canvas JSON
- created_at (TIMESTAMP)
```

## 🔗 API Endpoints

### Builder API
```
POST /admin/builder_api.php
```

**Actions**:
- `save` - Save certificate design
- `load` - Load certificate for editing
- `list` - Get all certificates
- `delete` - Delete certificate
- `generate_pdf` - Generate PDF from design

### Template Save API
```
POST /templates/save_custom_template.php
```

**Payload**:
```json
{
  "name": "Template Name",
  "canvas_json": {...fabric.js canvas data...}
}
```

**Response**:
```json
{
  "success": true,
  "template_id": 1,
  "message": "Template saved successfully"
}
```

## 📋 Teknologi yang Digunakan

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **PDF Generation**: Dompdf 2.0+
- **Canvas Editor**: Fabric.js 5.3.0
- **Icons**: Font Awesome 6.4.0
- **Frontend**: HTML5, CSS3, Vanilla JavaScript

## 🛠️ Troubleshooting

### Error: "Dompdf library tidak ditemukan"
```bash
composer require dompdf/dompdf
```

### Error: "Table not found"
```bash
php config/migrate_builder.php
php config/migrate_templates.php
```

### Placeholder tidak diganti di PDF
- Check: Field ada di database peserta?
- Check: Text di canvas adalah `{nama}` bukan `[NAMA_PESERTA]`?
- Check: generate_pdf.php logs

### Canvas tidak load di builder
- Clear browser cache
- Check: Fabric.js CDN accessible?
- Check: builder_api.php returns correct JSON?

## 📖 Documentation Files

- [PLACEHOLDERS.md](PLACEHOLDERS.md) - Placeholder system documentation
- [BUILDER_PLACEHOLDER.md](BUILDER_PLACEHOLDER.md) - Builder placeholder guide
- [IMPLEMENTATION_REVIEW.md](IMPLEMENTATION_REVIEW.md) - Implementation details

## 🔄 Version History

### v2.1 (2026-01-14) - Data Synchronization
- ✨ **Unified Participant List**: Custom participants automatically sync to generate.php
- ✨ UNION query untuk menampilkan peserta dari both sources (default + custom)
- ✨ Badge system: 📋 Default vs 🎨 Custom
- 🔧 Update: generate.php UNION query
- 🔧 Update: Add delete_custom_peserta.php handler
- 📝 Full sync documentation in README

### v2.0 (2026-01-14)
- ✨ Add Custom Certificate Builder with Fabric.js
- ✨ Add 4 placeholder types: {nama}, {instansi}, {peran}, {keterangan}
- ✨ Add keterangan field untuk custom notes
- ✨ Fully backward compatible dengan existing templates
- 🔧 Update: generate_pdf.php support both template types
- 🔧 Update: builder_api.php support new placeholder format

### v1.0 (Initial)
- 3 default templates (Seminar, Workshop, Pelatihan)
- Form input peserta
- PDF generation
- Peserta management

## ✅ Checklist Setup

- [ ] Database created & tables migrated
- [ ] Dompdf installed via composer
- [ ] Database config valid
- [ ] Can access http://sertifikat.test/admin/
- [ ] Can create peserta & generate default PDF
- [ ] Can access /admin/builder.php
- [ ] Can create custom template & generate custom PDF

## 📞 Support

Quick Links:
- Default Certificates: http://sertifikat.test/admin/generate.php
- Custom Builder: http://sertifikat.test/admin/builder.php
- Template Management: http://sertifikat.test/admin/custom_certificates.php
- Add Peserta: http://sertifikat.test/admin/form_peserta.php

For issues:
1. Check browser console (F12)
2. Check PHP error log
3. Check database connection
4. Review PLACEHOLDERS.md documentation

## 📄 Lisensi

Free to use untuk keperluan educational dan commercial

---

**Version**: 2.1 (Unified Sync Added)
**Last Updated**: 2026-01-14
**Status**: Production Ready ✅
