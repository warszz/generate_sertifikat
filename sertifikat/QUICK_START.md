# Custom Certificate Builder - Quick Start

## 🚀 Installation (5 minutes)

### 1. Run Database Migration
```bash
# Option A: Via Terminal
php config/migrate_builder.php

# Option B: Via Browser
# Open: http://your-domain/sertifikat/config/migrate_builder.php
```

### 2. Verify Installation
- Log into the admin panel
- Go to certificate management page
- Look for the "🎨 Custom Builder" button
- It should take you to `/admin/custom_certificates.php`

## 📝 How to Use

### Create Your First Certificate

1. **Click "Custom Builder"** from the main certificate page
2. **Click "Create New"**
   - Enter a name: "My First Certificate"
   - Add optional description
   - Click "Create & Edit"

3. **Design Your Certificate**
   - Use left sidebar to add elements
   - Add text (click "Add Text")
   - Add shapes (rectangles, circles, lines)
   - Upload images (background, logo, etc.)

4. **Use Dynamic Placeholders**
   - Type `[NAMA_PESERTA]` where you want the participant's name
   - Type `[INSTANSI]` for organization
   - Type `[PERAN]` for role/position

5. **Save Your Design**
   - Click "Save Design" button
   - Enter a name if different from initial
   - Choose if it's private or a template
   - Click "Save Design"

### Generate PDFs for Participants

1. **Go back to Certificates list**
   - Click "Back to Original Certificates" or use the back button
   
2. **Find your certificate**
   - Look for your newly created certificate
   
3. **Click "Generate"**
   - Enter participant name
   - Enter institution (optional)
   - Enter role (optional)
   - Click "Generate PDF"
   - PDF downloads automatically

## 🎨 Design Tips

### Good Practices
✅ Keep text readable at normal sizes  
✅ Use contrasting colors for text/background  
✅ Leave margins around edges  
✅ Test with actual text before publishing  
✅ Use high-quality images  

### Canvas Sizes
- **Default (A4 Landscape)**: 800 x 566 pixels
- **A4 Portrait**: 566 x 800 pixels
- **Custom**: Any size you need

### Placeholder Examples
```
Certificate of Completion

This certifies that

[NAMA_PESERTA]

from [INSTANSI]

has successfully completed the role of

[PERAN]
```

## 🔄 Comparison: Templates vs Builder

| Feature | Original Templates | Custom Builder |
|---------|------------------|-----------------|
| Learning curve | Low | Medium |
| Design control | Limited | Full |
| Speed | Very fast | Normal |
| Complexity | Simple | Advanced |
| Code editing | No | No (visual only) |

**Use Templates For:** Quick, pre-designed certificates  
**Use Builder For:** Custom, branded certificates

## ⌨️ Keyboard Shortcuts

| Action | Shortcut |
|--------|----------|
| Undo | Ctrl+Z |
| Redo | Ctrl+Y |
| Delete object | Delete key |
| Copy/Paste | Ctrl+C / Ctrl+V |

## 🛠️ Troubleshooting

### "Custom Builder" button not showing
→ Run migration: `php config/migrate_builder.php`

### Can't save designs
→ Check file permissions on `uploads/certificates/` directory

### PDFs not generating
→ Verify Dompdf installed: `composer list | grep dompdf`

### Objects not appearing in PDF
→ Check that placeholder text is correct (`[NAMA_PESERTA]` not `[nama]`)

## 📂 File Structure

```
sertifikat/
├── admin/
│   ├── builder.php              ← Main design editor
│   ├── builder_api.php          ← API backend
│   ├── custom_certificates.php  ← Certificate manager
│   └── download_cert.php        ← PDF downloader
│
├── config/
│   └── migrate_builder.php      ← Run this first!
│
├── uploads/
│   └── certificates/            ← Generated PDFs stored here
│
└── BUILDER_SETUP.md             ← Full documentation
```

## 🎯 Next Steps

1. ✅ Run migration script
2. ✅ Create your first certificate
3. ✅ Generate a test PDF
4. ✅ Customize colors and fonts
5. ✅ Add your logo/images
6. ✅ Save as template for reuse

## 📞 Need Help?

1. Check [BUILDER_SETUP.md](./BUILDER_SETUP.md) for detailed docs
2. Review troubleshooting section above
3. Check browser console (F12) for JavaScript errors
4. Verify database permissions

---

**Version:** 1.0  
**Last Updated:** January 2026  
**Compatibility:** PHP 7.4+, MySQL 5.7+, All modern browsers
