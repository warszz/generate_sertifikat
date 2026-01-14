# Certificate Builder Extension - Setup Guide

## Overview
This extension adds an **optional Canva-like certificate builder** using Fabric.js to your PHP Native Certificate Management System. The existing template-based certificate generation remains fully functional.

## Features Added

### 1. **Custom Certificate Builder** (Fabric.js-based)
- Drag-and-drop canvas interface
- Add shapes (rectangles, circles, lines)
- Add text with customizable fonts and colors
- Add placeholder fields ([NAMA_PESERTA], [INSTANSI], [PERAN])
- Upload images
- Full property editing panel
- Undo/Redo functionality
- Auto-save designs

### 2. **Certificate Management**
- Save designs as drafts or publish as templates
- Edit existing designs
- Generate PDFs for participants
- Delete certificates

### 3. **Database Extension**
Three new tables (backward-compatible):
- `custom_certificates` - stores certificate designs
- `custom_cert_peserta` - stores participant data
- `generated_custom_pdf` - tracks generated PDFs

## Installation Steps

### Step 1: Database Setup

Run the migration script to create new tables:

```bash
# Via PHP CLI
php config/migrate_builder.php

# Or directly from the web browser:
# http://your-domain/sertifikat/config/migrate_builder.php
```

### Step 2: Files Added

The following files have been added (all backward-compatible):

```
admin/
  ├── builder.php               # Main Fabric.js builder interface
  ├── builder_api.php           # API endpoints for save/load/generate
  ├── custom_certificates.php   # Certificate management dashboard
  └── download_cert.php         # PDF download handler

config/
  └── migrate_builder.php       # Database migration script

uploads/
  └── certificates/             # Directory for storing generated PDFs
```

### Step 3: Create Upload Directory

Ensure the uploads directory exists:

```bash
mkdir -p uploads/certificates
chmod 755 uploads/certificates
```

## Usage

### For Users

1. **Access the Builder**
   - Log in to the admin panel
   - Click "🎨 Custom Builder" button on the certificate page
   - Or visit: `/admin/custom_certificates.php`

2. **Create a New Certificate**
   - Click "Create New"
   - Enter a name and optional description
   - Click "Create & Edit"

3. **Design Your Certificate**
   - Use the left sidebar to add shapes, text, and images
   - Click on objects to edit their properties
   - Use placeholders like `[NAMA_PESERTA]` for dynamic content
   - Save your design using the "Save Design" button

4. **Generate PDFs**
   - Click "Generate" on a certificate
   - Enter participant information
   - Click "Generate PDF" to create and download

### Placeholder Tags

You can use these placeholders in text fields:

| Placeholder | Replacement |
|-----------|------------|
| `[NAMA_PESERTA]` | Participant's name |
| `[INSTANSI]` | Organization/Institution |
| `[PERAN]` | Role/Position |

## API Endpoints

All endpoints are in `admin/builder_api.php`:

### Save Design
```
POST /admin/builder_api.php
Parameters:
- action=save
- name (required)
- description
- design_data (JSON)
- canvas_width
- canvas_height
- cert_id (optional, for updates)
```

### Load Design
```
GET /admin/builder_api.php?action=load&id=CERT_ID
```

### List Certificates
```
GET /admin/builder_api.php?action=list
```

### Delete Certificate
```
POST /admin/builder_api.php
Parameters:
- action=delete
- id (required)
```

### Generate PDF
```
POST /admin/builder_api.php
Parameters:
- action=generate_pdf
- cert_id (required)
- participant_name (required)
- institution
- role
```

## Backward Compatibility

✅ **All existing functionality preserved:**
- Original template-based certificates still work
- Template system unchanged
- PDO database layer unchanged
- Dompdf integration unchanged
- Admin interface enhanced (not replaced)

✅ **No breaking changes:**
- Existing tables remain untouched
- New functionality is opt-in
- Users can use either system or both

## Customization

### Change Canvas Size
Edit the default size in `admin/builder.php`:

```javascript
// Line ~150
updateCanvasSize(800, 566); // width, height
```

### Modify Fabric.js Version
In `admin/builder.php`, update the CDN link:

```html
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.0/fabric.min.js"></script>
```

### Customize Colors
Edit the CSS variables in the `<style>` sections:

```css
Primary color: #667eea
Secondary color: #764ba2
```

## Troubleshooting

### PDFs not generating
1. Verify Dompdf is installed: `composer require dompdf/dompdf`
2. Check `uploads/certificates/` directory exists and is writable
3. Verify PHP has `exec()` enabled (for PDF generation)

### Canvas not loading
1. Check browser console for JavaScript errors
2. Verify Fabric.js CDN is accessible
3. Clear browser cache and reload

### Database errors
1. Run migration script again: `php config/migrate_builder.php`
2. Check database user has CREATE TABLE permissions
3. Verify PDO connection in `config/database.php`

## File Size Considerations

- **Design JSON**: Typically 5-50KB per certificate
- **Generated PDFs**: Varies by image quality (100KB-5MB)
- Recommend cleaning up old PDFs periodically

## Security Notes

- All user inputs are properly escaped in HTML output
- Database queries use prepared statements (PDO)
- User ownership verified for all operations
- Session-based authentication required

## Performance Tips

1. Keep designs simple (limit to <50 objects per canvas)
2. Optimize images before uploading
3. Clean up old PDFs regularly
4. Use PDF compression if file size becomes an issue

## Future Enhancements

Potential features for future versions:
- Preset templates gallery
- Batch PDF generation
- Certificate templates from community
- Advanced font library
- Digital signatures
- Certificate validation system

## Support

For issues or questions:
1. Check troubleshooting section above
2. Review browser console for JavaScript errors
3. Check PHP error logs
4. Verify database permissions

---

**Last Updated:** January 2026
**Compatibility:** PHP 7.4+, MySQL 5.7+, Dompdf 2.0+
